<?php
/**
 * Verify SSL Certificate Configuration
 * 
 * This script checks if SSL certificate is properly configured
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>SSL Certificate Configuration Verification</h2>";

$caBundlePath = __DIR__ . '/cacert.pem';

// Check 1: CA Bundle exists
echo "<h3>1. CA Certificate Bundle</h3>";
if (file_exists($caBundlePath)) {
    echo "✓ CA bundle found at: " . htmlspecialchars($caBundlePath) . "<br>";
    echo "File size: " . filesize($caBundlePath) . " bytes<br>";
} else {
    echo "❌ CA bundle NOT found. Please run update_php_ini.php first.<br>";
    exit;
}

// Check 2: PHP ini settings
echo "<h3>2. PHP Configuration</h3>";
$curlCainfo = ini_get('curl.cainfo');
$opensslCafile = ini_get('openssl.cafile');

echo "curl.cainfo: " . ($curlCainfo ? htmlspecialchars($curlCainfo) : '<strong style="color:red">NOT SET</strong>') . "<br>";
echo "openssl.cafile: " . ($opensslCafile ? htmlspecialchars($opensslCafile) : '<strong style="color:red">NOT SET</strong>') . "<br>";

if (!$curlCainfo || !$opensslCafile) {
    echo "<p style='color:red;'><strong>⚠️ php.ini is not configured correctly!</strong></p>";
    echo "<p>You need to:</p>";
    echo "<ol>";
    echo "<li>Open php.ini: <code>" . htmlspecialchars(php_ini_loaded_file()) . "</code></li>";
    echo "<li>Add or update these lines:</li>";
    echo "<pre>";
    echo "curl.cainfo = \"" . str_replace('\\', '/', $caBundlePath) . "\"\n";
    echo "openssl.cafile = \"" . str_replace('\\', '/', $caBundlePath) . "\"\n";
    echo "</pre>";
    echo "<li>Save and <strong>restart Apache/WAMP</strong></li>";
    echo "</ol>";
    echo "<p>Or run: <a href='update_php_ini.php'>update_php_ini.php</a> (may require Administrator privileges)</p>";
}

// Check 3: Test cURL with certificate
echo "<h3>3. cURL SSL Test</h3>";
if (function_exists('curl_init')) {
    $ch = curl_init('https://api.cloudinary.com/v1_1/dziqaj90l/ping');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    
    if ($curlCainfo) {
        curl_setopt($ch, CURLOPT_CAINFO, $curlCainfo);
    } elseif (file_exists($caBundlePath)) {
        curl_setopt($ch, CURLOPT_CAINFO, $caBundlePath);
    }
    
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);
    
    if ($curlError) {
        echo "❌ cURL Error: " . htmlspecialchars($curlError) . "<br>";
        if (strpos($curlError, 'SSL certificate') !== false) {
            echo "<p style='color:red;'><strong>The SSL certificate is still not working!</strong></p>";
            echo "<p>Even though we set the certificate path, cURL is still failing.</p>";
            echo "<p><strong>Solution:</strong> You MUST update php.ini and restart Apache/WAMP.</p>";
        }
    } elseif ($httpCode == 200 || $httpCode == 401) {
        echo "✓ cURL SSL test successful! (HTTP $httpCode)<br>";
        echo "The certificate bundle is working correctly.<br>";
    } else {
        echo "⚠️ Unexpected HTTP code: $httpCode<br>";
    }
} else {
    echo "❌ cURL extension not available<br>";
}

// Check 4: Guzzle test
echo "<h3>4. Guzzle HTTP Client Test</h3>";
if (class_exists('GuzzleHttp\Client')) {
    try {
        $client = new \GuzzleHttp\Client([
            'verify' => $caBundlePath, // Direct path to certificate bundle
            'timeout' => 10
        ]);
        
        $response = $client->get('https://api.cloudinary.com/v1_1/dziqaj90l/ping');
        $statusCode = $response->getStatusCode();
        
        if ($statusCode == 200 || $statusCode == 401) {
            echo "✓ Guzzle SSL test successful! (HTTP $statusCode)<br>";
            echo "Guzzle can connect to Cloudinary with the certificate bundle.<br>";
        } else {
            echo "⚠️ Unexpected HTTP code: $statusCode<br>";
        }
    } catch (\Exception $e) {
        echo "❌ Guzzle Error: " . htmlspecialchars($e->getMessage()) . "<br>";
        if (strpos($e->getMessage(), 'SSL') !== false || strpos($e->getMessage(), 'certificate') !== false) {
            echo "<p style='color:red;'><strong>Guzzle is still having SSL issues!</strong></p>";
            echo "<p>This means the certificate bundle path needs to be set in Guzzle's configuration.</p>";
        }
    }
} else {
    echo "⚠️ Guzzle not loaded (this is OK if you're not using it directly)<br>";
}

echo "<hr>";
echo "<h3>Next Steps</h3>";
echo "<ol>";
echo "<li>If php.ini is not configured, update it and restart Apache/WAMP</li>";
echo "<li>Test again: <a href='test_cloudinary.php'>test_cloudinary.php</a></li>";
echo "<li>If still failing, check the error messages above</li>";
echo "</ol>";

?>

