<?php
/**
 * Test Cloudinary Connection
 * 
 * This script tests if Cloudinary is properly configured and can upload files
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'cloudinary_helper.php';

echo "<h2>Cloudinary Connection Test</h2>";

// Test 1: Check if config file exists and is readable
echo "<h3>1. Configuration Check</h3>";
try {
    $config = include 'cloudinary_config.php';
    echo "✓ Config file loaded<br>";
    echo "Cloud Name: " . htmlspecialchars($config['cloud_name']) . "<br>";
    echo "API Key: " . htmlspecialchars(substr($config['api_key'], 0, 5)) . "***<br>";
    echo "API Secret: " . (empty($config['api_secret']) ? '❌ Empty' : '✓ Set') . "<br>";
} catch (Exception $e) {
    echo "❌ Error loading config: " . htmlspecialchars($e->getMessage()) . "<br>";
    exit;
}

// Test 2: Check if Cloudinary SDK is loaded
echo "<h3>2. SDK Check</h3>";
if (class_exists('Cloudinary\Cloudinary')) {
    echo "✓ Cloudinary SDK loaded<br>";
} else {
    echo "❌ Cloudinary SDK not found<br>";
    exit;
}

// Test 3: Test Cloudinary initialization
echo "<h3>3. Cloudinary Initialization</h3>";
try {
    $cloudinary = initCloudinary();
    echo "✓ Cloudinary initialized successfully<br>";
} catch (Exception $e) {
    echo "❌ Initialization failed: " . htmlspecialchars($e->getMessage()) . "<br>";
    exit;
}

// Test 4: Test upload with a small test image (if available)
echo "<h3>4. Upload Test</h3>";
if (isset($_FILES['test_image']) && $_FILES['test_image']['error'] === UPLOAD_ERR_OK) {
    try {
        $url = uploadFileToCloudinary($_FILES['test_image'], 'test_uploads', 'test');
        if ($url) {
            echo "✓ Upload successful!<br>";
            echo "URL: <a href='" . htmlspecialchars($url) . "' target='_blank'>" . htmlspecialchars($url) . "</a><br>";
            echo "<img src='" . htmlspecialchars($url) . "' style='max-width: 300px; margin-top: 10px;'><br>";
        } else {
            echo "❌ Upload returned false (no URL)<br>";
        }
    } catch (Exception $e) {
        echo "❌ Upload failed: " . htmlspecialchars($e->getMessage()) . "<br>";
        echo "Error details: <pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    }
} else {
    echo "<form method='post' enctype='multipart/form-data'>";
    echo "Upload a test image: <input type='file' name='test_image' accept='image/*' required><br>";
    echo "<button type='submit'>Test Upload</button>";
    echo "</form>";
}

// Test 5: Check PHP configuration
echo "<h3>5. PHP Configuration</h3>";
echo "upload_max_filesize: " . ini_get('upload_max_filesize') . "<br>";
echo "post_max_size: " . ini_get('post_max_size') . "<br>";
echo "max_execution_time: " . ini_get('max_execution_time') . "<br>";
echo "memory_limit: " . ini_get('memory_limit') . "<br>";

// Test 6: Check network connectivity
echo "<h3>6. Network Connectivity</h3>";
$testUrl = "https://api.cloudinary.com/v1_1/" . $config['cloud_name'] . "/ping";
$ch = curl_init($testUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode == 200 || $httpCode == 401) {
    echo "✓ Can reach Cloudinary API (HTTP $httpCode)<br>";
    if ($httpCode == 401) {
        echo "⚠ Authentication may be required, but connection works<br>";
    }
} else {
    echo "❌ Cannot reach Cloudinary API (HTTP $httpCode)<br>";
    echo "Response: " . htmlspecialchars(substr($response, 0, 200)) . "<br>";
}

?>

