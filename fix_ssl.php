<?php
/**
 * SSL Certificate Fix for Cloudinary
 * 
 * This script helps fix the SSL certificate issue on Windows/WAMP
 * Run this once to configure PHP for Cloudinary SSL connections
 */

echo "<h2>SSL Certificate Configuration for Cloudinary</h2>";

// Check if cacert.pem exists
$caBundlePath = __DIR__ . '/cacert.pem';
if (!file_exists($caBundlePath)) {
    echo "<p>❌ CA certificate bundle not found. Downloading...</p>";
    $caBundle = file_get_contents('https://curl.se/ca/cacert.pem');
    if ($caBundle) {
        file_put_contents($caBundlePath, $caBundle);
        echo "<p>✓ CA certificate bundle downloaded.</p>";
    } else {
        echo "<p>❌ Failed to download CA certificate bundle.</p>";
        exit;
    }
} else {
    echo "<p>✓ CA certificate bundle found.</p>";
}

// Get PHP configuration file path
$phpIniPath = php_ini_loaded_file();
echo "<h3>PHP Configuration</h3>";
echo "<p><strong>php.ini location:</strong> " . htmlspecialchars($phpIniPath) . "</p>";

// Check current settings
$currentCurlCainfo = ini_get('curl.cainfo');
$currentOpensslCafile = ini_get('openssl.cafile');

echo "<p><strong>Current curl.cainfo:</strong> " . ($currentCurlCainfo ?: 'Not set') . "</p>";
echo "<p><strong>Current openssl.cafile:</strong> " . ($currentOpensslCafile ?: 'Not set') . "</p>";

// Try to set it programmatically (this only works for current script execution)
ini_set('curl.cainfo', $caBundlePath);
ini_set('openssl.cafile', $caBundlePath);

echo "<h3>Manual Configuration Required</h3>";
echo "<p>To permanently fix this, you need to edit your php.ini file:</p>";
echo "<ol>";
echo "<li>Open: <code>" . htmlspecialchars($phpIniPath) . "</code></li>";
echo "<li>Find the line with <code>curl.cainfo</code> (or add it if it doesn't exist)</li>";
echo "<li>Set it to: <code>curl.cainfo = \"" . htmlspecialchars($caBundlePath) . "\"</code></li>";
echo "<li>Find the line with <code>openssl.cafile</code> (or add it if it doesn't exist)</li>";
echo "<li>Set it to: <code>openssl.cafile = \"" . htmlspecialchars($caBundlePath) . "\"</code></li>";
echo "<li>Save the file and restart Apache/WAMP</li>";
echo "</ol>";

echo "<h3>Alternative: Development Workaround</h3>";
echo "<p>For development only, you can disable SSL verification. This is NOT secure for production!</p>";
echo "<p>Add this to your <code>cloudinary_config.php</code>:</p>";
echo "<pre>";
echo "'disable_ssl_verify' => true  // Development only!\n";
echo "</pre>";

echo "<h3>Test Configuration</h3>";
echo "<p><a href='test_cloudinary.php'>Test Cloudinary Connection</a></p>";

?>

