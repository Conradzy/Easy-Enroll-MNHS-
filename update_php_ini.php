<?php
/**
 * Update php.ini to fix SSL certificate issue
 * 
 * This script attempts to automatically update php.ini with the CA certificate path
 * Run this script once to configure PHP for Cloudinary SSL connections
 */

echo "<h2>PHP.ini SSL Certificate Configuration</h2>";

$caBundlePath = __DIR__ . '/cacert.pem';

// Check if cacert.pem exists
if (!file_exists($caBundlePath)) {
    echo "<p>❌ CA certificate bundle not found. Downloading...</p>";
    $caBundle = @file_get_contents('https://curl.se/ca/cacert.pem');
    if ($caBundle) {
        file_put_contents($caBundlePath, $caBundle);
        echo "<p>✓ CA certificate bundle downloaded.</p>";
    } else {
        echo "<p>❌ Failed to download CA certificate bundle. Please download manually from: https://curl.se/ca/cacert.pem</p>";
        exit;
    }
} else {
    echo "<p>✓ CA certificate bundle found at: " . htmlspecialchars($caBundlePath) . "</p>";
}

// Get php.ini path
$phpIniPath = php_ini_loaded_file();
if (!$phpIniPath) {
    echo "<p>❌ Could not find php.ini file.</p>";
    exit;
}

echo "<p><strong>PHP.ini location:</strong> " . htmlspecialchars($phpIniPath) . "</p>";

// Check if we can write to php.ini
if (!is_writable($phpIniPath)) {
    echo "<p>⚠️ Cannot write to php.ini (permission denied).</p>";
    echo "<p><strong>Manual steps required:</strong></p>";
    echo "<ol>";
    echo "<li>Open php.ini as Administrator: <code>" . htmlspecialchars($phpIniPath) . "</code></li>";
    echo "<li>Find or add these lines:</li>";
    echo "<pre>";
    echo "curl.cainfo = \"" . str_replace('\\', '/', $caBundlePath) . "\"\n";
    echo "openssl.cafile = \"" . str_replace('\\', '/', $caBundlePath) . "\"\n";
    echo "</pre>";
    echo "<li>Save and restart Apache/WAMP</li>";
    echo "</ol>";
    exit;
}

// Read php.ini
$phpIniContent = file_get_contents($phpIniPath);
$updated = false;

// Update or add curl.cainfo
if (preg_match('/^\s*curl\.cainfo\s*=/m', $phpIniContent)) {
    $phpIniContent = preg_replace(
        '/^\s*curl\.cainfo\s*=.*$/m',
        'curl.cainfo = "' . str_replace('\\', '/', $caBundlePath) . '"',
        $phpIniContent
    );
    $updated = true;
} else {
    $phpIniContent .= "\n; Cloudinary SSL Certificate\n";
    $phpIniContent .= "curl.cainfo = \"" . str_replace('\\', '/', $caBundlePath) . "\"\n";
    $updated = true;
}

// Update or add openssl.cafile
if (preg_match('/^\s*openssl\.cafile\s*=/m', $phpIniContent)) {
    $phpIniContent = preg_replace(
        '/^\s*openssl\.cafile\s*=.*$/m',
        'openssl.cafile = "' . str_replace('\\', '/', $caBundlePath) . '"',
        $phpIniContent
    );
    $updated = true;
} else {
    $phpIniContent .= "openssl.cafile = \"" . str_replace('\\', '/', $caBundlePath) . "\"\n";
    $updated = true;
}

if ($updated) {
    // Backup original php.ini
    $backupPath = $phpIniPath . '.backup.' . date('Y-m-d_H-i-s');
    copy($phpIniPath, $backupPath);
    echo "<p>✓ Backup created: " . htmlspecialchars(basename($backupPath)) . "</p>";
    
    // Write updated php.ini
    if (file_put_contents($phpIniPath, $phpIniContent)) {
        echo "<p>✓ php.ini updated successfully!</p>";
        echo "<p><strong>⚠️ IMPORTANT:</strong> You must restart Apache/WAMP for changes to take effect.</p>";
        echo "<p>After restarting, test at: <a href='test_cloudinary.php'>test_cloudinary.php</a></p>";
    } else {
        echo "<p>❌ Failed to write to php.ini.</p>";
    }
} else {
    echo "<p>ℹ️ php.ini already configured correctly.</p>";
}

?>

