<?php
/**
 * Cloudinary Helper Functions
 * 
 * This file provides helper functions for uploading images to Cloudinary
 */

require_once 'cloudinary_config.php';

// Load Cloudinary PHP SDK via Composer
// The SDK is installed via Composer, so we use the vendor autoloader
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
} elseif (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
} else {
    $errorMsg = 'Cloudinary SDK not found. Please run: composer install';
    error_log($errorMsg);
    throw new Exception($errorMsg);
}

// Verify that Cloudinary classes are available
if (!class_exists('Cloudinary\Cloudinary')) {
    $errorMsg = 'Cloudinary class not found after loading autoloader. Please run: composer install';
    error_log($errorMsg);
    throw new Exception($errorMsg);
}

use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;

// Configure cURL to handle SSL certificates (for Windows/WAMP)
// This fixes the "SSL certificate problem: unable to get local issuer certificate" error
$caBundlePath = __DIR__ . '/cacert.pem';

// Always try to set the certificate path if the file exists
if (file_exists($caBundlePath)) {
    // Set PHP ini values (these may not work if php.ini is read-only, but worth trying)
    ini_set('curl.cainfo', $caBundlePath);
    ini_set('openssl.cafile', $caBundlePath);
    
    // Also set environment variables that Guzzle might check
    putenv('SSL_CERT_FILE=' . $caBundlePath);
    $_ENV['SSL_CERT_FILE'] = $caBundlePath;
    $_SERVER['SSL_CERT_FILE'] = $caBundlePath;
    
    // Set cURL default options that Guzzle will inherit
    if (function_exists('curl_setopt_array')) {
        // This will be used by Guzzle when creating cURL handles
        $defaultCurlOpts = [
            CURLOPT_CAINFO => $caBundlePath,
            CURLOPT_CAPATH => dirname($caBundlePath),
        ];
        // Store for later use
        if (!defined('CLOUDINARY_CA_BUNDLE_PATH')) {
            define('CLOUDINARY_CA_BUNDLE_PATH', $caBundlePath);
        }
    }
} else {
    error_log('Warning: CA certificate bundle not found at: ' . $caBundlePath);
    error_log('Run update_php_ini.php to download it, or download from: https://curl.se/ca/cacert.pem');
}

/**
 * Initialize Cloudinary
 * Uses Cloudinary URL format which is the most reliable method
 */
function initCloudinary() {
    static $cloudinaryInstance = null;
    
    // Return cached instance if already initialized
    if ($cloudinaryInstance !== null) {
        return $cloudinaryInstance;
    }
    
    $config = include 'cloudinary_config.php';
    
    // Validate configuration
    if (empty($config['cloud_name']) || empty($config['api_key']) || empty($config['api_secret'])) {
        throw new Exception("Cloudinary configuration is incomplete. Please check cloudinary_config.php");
    }
    
    // Initialize Cloudinary v3
    // Ensure SSL certificate is configured before initializing
    $caBundlePath = defined('CLOUDINARY_CA_BUNDLE_PATH') ? CLOUDINARY_CA_BUNDLE_PATH : (__DIR__ . '/cacert.pem');
    
    // First, set the global configuration
    try {
        Configuration::instance([
            'cloud' => [
                'cloud_name' => $config['cloud_name'],
                'api_key' => $config['api_key'],
                'api_secret' => $config['api_secret']
            ],
            'url' => [
                'secure' => $config['secure'] ?? true
            ]
        ]);
        
        // Then create the Cloudinary instance
        $cloudinaryInstance = new Cloudinary();
        
        // Workaround: Configure the HTTP client to use the certificate bundle
        // This is needed because Guzzle doesn't always respect php.ini settings
        if (file_exists($caBundlePath)) {
            try {
                // Try to get the API client and configure its HTTP client
                $uploadApi = $cloudinaryInstance->uploadApi();
                // The HTTP client is created internally, so we can't easily modify it
                // But we've set the certificate path above, which should work
            } catch (\Exception $e) {
                // If we can't configure it, that's OK - we'll rely on php.ini
            }
        }
        
        return $cloudinaryInstance;
        
    } catch (\Exception $e) {
        // If Configuration::instance() fails, try passing config directly
        try {
            $cloudinaryInstance = new Cloudinary([
                'cloud' => [
                    'cloud_name' => $config['cloud_name'],
                    'api_key' => $config['api_key'],
                    'api_secret' => $config['api_secret']
                ],
                'url' => [
                    'secure' => $config['secure'] ?? true
                ]
            ]);
            return $cloudinaryInstance;
        } catch (\Exception $e2) {
            throw new Exception("Failed to initialize Cloudinary. Error 1: " . $e->getMessage() . " | Error 2: " . $e2->getMessage() . " (Check your credentials in cloudinary_config.php)");
        }
    }
}

/**
 * Upload image to Cloudinary
 * 
 * @param string $file_path Temporary file path
 * @param string $folder Cloudinary folder name (optional)
 * @param string $public_id Public ID for the image (optional)
 * @param string $original_filename Original filename for format detection (optional)
 * @return array|false Returns array with 'url' and 'public_id' on success, false on failure
 */
function uploadToCloudinary($file_path, $folder = 'student_enrollment', $public_id = null, $original_filename = null) {
    try {
        $cloudinary = initCloudinary();
        
        $options = [
            'folder' => $folder,
            'resource_type' => 'image',
            'overwrite' => false,
            'invalidate' => true
        ];
        
        if ($public_id) {
            $options['public_id'] = $public_id;
        }
        
        $result = $cloudinary->uploadApi()->upload($file_path, $options);
        
        // Handle both array and object responses
        $url = null;
        $public_id_result = null;
        
        if (is_array($result)) {
            $url = $result['secure_url'] ?? $result['url'] ?? null;
            $public_id_result = $result['public_id'] ?? null;
        } elseif (is_object($result)) {
            // If result is an object, try multiple ways to get the data
            if (method_exists($result, 'getArrayCopy')) {
                $resultArray = $result->getArrayCopy();
                $url = $resultArray['secure_url'] ?? $resultArray['url'] ?? null;
                $public_id_result = $resultArray['public_id'] ?? null;
            } elseif (property_exists($result, 'secure_url')) {
                $url = $result->secure_url;
                $public_id_result = $result->public_id ?? null;
            } elseif (property_exists($result, 'url')) {
                $url = $result->url;
                $public_id_result = $result->public_id ?? null;
            } elseif (method_exists($result, 'offsetGet')) {
                $url = $result->offsetGet('secure_url') ?? $result->offsetGet('url');
                $public_id_result = $result->offsetGet('public_id');
            }
        } else {
            error_log("Cloudinary upload returned unexpected result type: " . gettype($result));
            error_log("Result value: " . print_r($result, true));
            throw new Exception("Unexpected result type from Cloudinary: " . gettype($result));
        }
        
        if (!$url) {
            $resultDebug = is_object($result) ? json_encode($result) : print_r($result, true);
            error_log("Cloudinary upload succeeded but no URL returned. Result: " . $resultDebug);
            throw new Exception("Cloudinary upload succeeded but no URL was returned in the response");
        }
        
        // Get format from result if available
        $format = null;
        if (is_array($result)) {
            $format = $result['format'] ?? null;
        } elseif (is_object($result)) {
            if (method_exists($result, 'getArrayCopy')) {
                $resultArray = $result->getArrayCopy();
                $format = $resultArray['format'] ?? null;
            } elseif (property_exists($result, 'format')) {
                $format = $result->format;
            } elseif (method_exists($result, 'offsetGet')) {
                $format = $result->offsetGet('format');
            }
        }
        
        // If format is not in result, try to extract from original filename
        if (!$format && $original_filename) {
            $pathInfo = pathinfo($original_filename);
            $format = $pathInfo['extension'] ?? null;
        }
        
        // If still no format, try to extract from file path
        if (!$format && isset($file_path)) {
            $pathInfo = pathinfo($file_path);
            $format = $pathInfo['extension'] ?? null;
        }
        
        // Ensure URL has file extension - Cloudinary URLs should include format
        // If URL doesn't end with a file extension, add it
        if ($format && !preg_match('/\.(jpg|jpeg|png|gif|webp|pdf)$/i', $url)) {
            // Check if URL ends with public_id (no extension)
            // If so, append the format
            $url = rtrim($url, '/') . '.' . strtolower($format);
        }
        
        if (!$public_id_result) {
            // Extract public_id from URL if not in response
            $public_id_result = extractPublicIdFromUrl($url);
        }
        
        return [
            'url' => $url,
            'public_id' => $public_id_result
        ];
        
    } catch (\Exception $e) {
        $errorMsg = $e->getMessage();
        if ($e->getPrevious()) {
            $errorMsg .= " (Previous: " . $e->getPrevious()->getMessage() . ")";
        }
        error_log("Cloudinary upload error: " . $errorMsg);
        error_log("Stack trace: " . $e->getTraceAsString());
        throw new Exception($errorMsg);
    }
}

/**
 * Upload image from $_FILES array to Cloudinary
 * 
 * @param array $file $_FILES array element
 * @param string $folder Cloudinary folder name
 * @param string $prefix Prefix for the public_id
 * @return array|false Returns array with 'url' and 'public_id' on success, false on failure
 */
function uploadFileToCloudinary($file, $folder = 'student_enrollment', $prefix = '') {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        $errorCode = $file['error'] ?? 'unknown';
        $errorMsg = "File upload error code: " . $errorCode;
        error_log($errorMsg);
        throw new Exception($errorMsg);
    }
    
    $tmp_path = $file['tmp_name'];
    
    if (!file_exists($tmp_path)) {
        $errorMsg = "Temporary file not found: " . $tmp_path;
        error_log($errorMsg);
        throw new Exception($errorMsg);
    }
    
    // Get file extension from original filename
    $original_name = $file['name'] ?? '';
    $file_extension = '';
    if ($original_name) {
        $pathInfo = pathinfo($original_name);
        $file_extension = strtolower($pathInfo['extension'] ?? '');
    }
    
    // Generate public_id with prefix and timestamp, include extension if available
    $public_id = $prefix . '_' . time() . '_' . uniqid();
    if ($file_extension) {
        $public_id .= '.' . $file_extension;
    }
    // Remove any invalid characters for Cloudinary public_id (but keep dots and extensions)
    $public_id = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $public_id);
    
    try {
        // Pass original filename to uploadToCloudinary for format detection
        $result = uploadToCloudinary($tmp_path, $folder, $public_id, $original_name);
        
        // Ensure the URL has the file extension (double check)
        if ($result && isset($result['url']) && $file_extension) {
            // If URL doesn't have extension, add it
            if (!preg_match('/\.(jpg|jpeg|png|gif|webp|pdf)$/i', $result['url'])) {
                $result['url'] = rtrim($result['url'], '/') . '.' . $file_extension;
            }
        }
        
        return $result;
    } catch (Exception $e) {
        // Re-throw with context
        throw new Exception("Failed to upload file to Cloudinary: " . $e->getMessage());
    }
}

/**
 * Delete image from Cloudinary
 * 
 * @param string $public_id Public ID of the image to delete
 * @return bool True on success, false on failure
 */
function deleteFromCloudinary($public_id) {
    try {
        $cloudinary = initCloudinary();
        
        $result = $cloudinary->uploadApi()->destroy($public_id);
        
        return isset($result['result']) && $result['result'] === 'ok';
        
    } catch (Exception $e) {
        error_log("Cloudinary delete error: " . $e->getMessage());
        return false;
    }
}

/**
 * Generate Cloudinary URL from public_id
 * 
 * @param string $public_id The Cloudinary public_id
 * @param array $transformations Optional transformations (e.g., ['width' => 300, 'quality' => 'auto'])
 * @param string $resource_type Resource type (default: 'image')
 * @return string The Cloudinary URL
 */
function getCloudinaryUrlFromPublicId($public_id, $transformations = [], $resource_type = 'image') {
    if (empty($public_id)) {
        return '';
    }
    
    $config = include 'cloudinary_config.php';
    $cloud_name = $config['cloud_name'];
    $secure = $config['secure'] ?? true;
    
    $protocol = $secure ? 'https' : 'http';
    $base_url = "$protocol://res.cloudinary.com/$cloud_name/$resource_type/upload";
    
    // Build transformation string
    $transform_string = '';
    if (!empty($transformations)) {
        if (isset($transformations['width'])) {
            $transform_string .= 'w_' . $transformations['width'] . ',';
        }
        if (isset($transformations['height'])) {
            $transform_string .= 'h_' . $transformations['height'] . ',';
        }
        if (isset($transformations['quality'])) {
            $transform_string .= 'q_' . $transformations['quality'] . ',';
        }
        if (isset($transformations['crop'])) {
            $transform_string .= 'c_' . $transformations['crop'] . ',';
        }
        if (isset($transformations['format'])) {
            $transform_string .= 'f_' . $transformations['format'] . ',';
        }
        
        // Remove trailing comma
        $transform_string = rtrim($transform_string, ',');
        
        if ($transform_string) {
            $transform_string .= '/';
        }
    }
    
    // Build full URL
    $url = "$base_url/$transform_string$public_id";
    
    return $url;
}

/**
 * Extract public_id from Cloudinary URL
 * 
 * @param string $cloudinary_url The Cloudinary URL
 * @return string|false The public_id on success, false on failure
 */
function extractPublicIdFromUrl($cloudinary_url) {
    if (empty($cloudinary_url)) {
        return false;
    }
    
    // Pattern: https://res.cloudinary.com/{cloud_name}/image/upload/{transformations}/{version}/{public_id}.{format}
    // Or: https://res.cloudinary.com/{cloud_name}/image/upload/{public_id}.{format}
    if (preg_match('#/image/upload/(?:v\d+/)?(?:[^/]+/)*(.+?)(?:\.[^.]+)?$#', $cloudinary_url, $matches)) {
        $public_id = $matches[1];
        // Remove file extension if present
        $public_id = preg_replace('/\.[^.]+$/', '', $public_id);
        return $public_id;
    }
    
    return false;
}

/**
 * Get Cloudinary image URL with optional transformations
 * 
 * This function can be used to add Cloudinary transformations to existing URLs
 * for better performance (resizing, quality optimization, etc.)
 * 
 * @param string $cloudinary_url_or_public_id The Cloudinary URL or public_id from the database
 * @param array $transformations Optional transformations (e.g., ['width' => 300, 'quality' => 'auto'])
 * @return string The transformed Cloudinary URL
 */
function getCloudinaryImageUrl($cloudinary_url_or_public_id, $transformations = []) {
    // If it's a public_id (doesn't contain http:// or https://), generate URL from it
    if (strpos($cloudinary_url_or_public_id, 'http://') === false && strpos($cloudinary_url_or_public_id, 'https://') === false) {
        return getCloudinaryUrlFromPublicId($cloudinary_url_or_public_id, $transformations);
    }
    
    // Otherwise, treat it as a URL
    $cloudinary_url = $cloudinary_url_or_public_id;
    // If it's not a Cloudinary URL (backward compatibility with old local paths), return as-is
    if (strpos($cloudinary_url, 'res.cloudinary.com') === false && strpos($cloudinary_url, 'cloudinary.com') === false) {
        return $cloudinary_url;
    }
    
    // If no transformations requested, return original URL
    if (empty($transformations)) {
        return $cloudinary_url;
    }
    
    // Parse the Cloudinary URL to extract components
    // Format: https://res.cloudinary.com/{cloud_name}/image/upload/{transformations}/{version}/{public_id}.{format}
    $url_parts = parse_url($cloudinary_url);
    $path = $url_parts['path'] ?? '';
    
    // Extract the path after /image/upload/
    if (preg_match('#/image/upload/(.*?)/(v\d+/)?([^/]+\.\w+)$#', $path, $matches)) {
        $existing_transforms = $matches[1] ?? '';
        $version = $matches[2] ?? '';
        $filename = $matches[3] ?? '';
        
        // Build transformation string
        $transform_string = '';
        if (isset($transformations['width'])) {
            $transform_string .= 'w_' . $transformations['width'] . ',';
        }
        if (isset($transformations['height'])) {
            $transform_string .= 'h_' . $transformations['height'] . ',';
        }
        if (isset($transformations['quality'])) {
            $transform_string .= 'q_' . $transformations['quality'] . ',';
        }
        if (isset($transformations['crop'])) {
            $transform_string .= 'c_' . $transformations['crop'] . ',';
        }
        
        // Remove trailing comma
        $transform_string = rtrim($transform_string, ',');
        
        // If there are existing transformations, combine them
        if ($existing_transforms && $transform_string) {
            $transform_string = $existing_transforms . ',' . $transform_string;
        } elseif ($existing_transforms) {
            $transform_string = $existing_transforms;
        }
        
        // Rebuild URL
        $new_path = '/image/upload/' . ($transform_string ? $transform_string . '/' : '') . ($version ? $version : '') . $filename;
        return $url_parts['scheme'] . '://' . $url_parts['host'] . $new_path;
    }
    
    // If URL parsing fails, return original URL
    return $cloudinary_url;
}

/**
 * Check if a URL is a Cloudinary URL
 * 
 * @param string $url The URL to check
 * @return bool True if it's a Cloudinary URL, false otherwise
 */
function isCloudinaryUrl($url) {
    return strpos($url, 'res.cloudinary.com') !== false || strpos($url, 'cloudinary.com') !== false;
}


