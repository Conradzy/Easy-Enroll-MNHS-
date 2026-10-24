<?php
// Debug script to test email sending from PHP
echo "<h2>Email Debug Test</h2>";

// Load SMTP config
$smtp = @include __DIR__ . '/smtp_config.php';
if (!is_array($smtp)) { 
    $smtp = []; 
}

echo "<h3>SMTP Configuration:</h3>";
echo "<pre>";
print_r($smtp);
echo "</pre>";

// Test email and code
$testEmail = 'test@example.com';
$testCode = '123456';

$host = escapeshellarg($smtp['SMTP_HOST'] ?? 'smtp.gmail.com');
$port = escapeshellarg((string)($smtp['SMTP_PORT'] ?? '587'));
$user = escapeshellarg($smtp['SMTP_USER'] ?? '');
$pass = escapeshellarg($smtp['SMTP_PASS'] ?? '');
$fromEmail = escapeshellarg($smtp['FROM_EMAIL'] ?? '');
$fromName = escapeshellarg($smtp['FROM_NAME'] ?? 'Account Verification');

$emailArg = escapeshellarg($testEmail);
$codeArg = escapeshellarg($testCode);

echo "<h3>Test Command:</h3>";
echo "<pre>";
echo "python send_code.py $emailArg $codeArg --smtp-host $host --smtp-port $port --smtp-user $user --smtp-pass $pass --from-email $fromEmail --from-name $fromName";
echo "</pre>";

// Test different Python paths
$pythonPaths = [
    'C:\\Users\\raymo\\AppData\\Local\\Programs\\Python\\Python313\\python.exe',
    'python', 
    'py'
];
$scriptDir = __DIR__;

echo "<h3>Testing Python Executables:</h3>";

foreach ($pythonPaths as $pythonPath) {
    echo "<h4>Testing: $pythonPath</h4>";
    
    // Test if Python is available
    $testCmd = "$pythonPath --version 2>&1";
    $versionOutput = shell_exec($testCmd);
    echo "<p><strong>Version check:</strong> " . htmlspecialchars($versionOutput ?: 'No output') . "</p>";
    
    // Test the actual email command
    $cmd = "cd /d \"$scriptDir\" && $pythonPath send_code.py $emailArg $codeArg --smtp-host $host --smtp-port $port --smtp-user $user --smtp-pass $pass --from-email $fromEmail --from-name $fromName 2>&1";
    echo "<p><strong>Command:</strong> " . htmlspecialchars($cmd) . "</p>";
    
    $output = shell_exec($cmd);
    echo "<p><strong>Output:</strong> " . htmlspecialchars($output ?: 'No output') . "</p>";
    
    if ($output && strpos($output, 'sent') !== false) {
        echo "<p style='color: green;'><strong>SUCCESS!</strong></p>";
        break;
    } else {
        echo "<p style='color: red;'><strong>FAILED</strong></p>";
    }
    echo "<hr>";
}

echo "<h3>Environment Info:</h3>";
echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";
echo "<p><strong>Current Directory:</strong> " . getcwd() . "</p>";
echo "<p><strong>Script Directory:</strong> " . __DIR__ . "</p>";
echo "<p><strong>PATH:</strong> " . getenv('PATH') . "</p>";
?>
