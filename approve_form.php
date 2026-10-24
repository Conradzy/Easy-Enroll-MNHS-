<?php
require_once 'db_connection.php';

if (isset($_POST['pending_id'])) {
    $pending_id = $_POST['pending_id'];

    try {
        // Start transaction
        $pdo->beginTransaction();

        // Get all data from pending_forms
        $sql_get_pending = "SELECT * FROM pending_forms WHERE pending_id = ?";
        $stmt = $pdo->prepare($sql_get_pending);
        $stmt->execute([$pending_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $studentacc_id = $result['studentacc_id'];

            // Update enrollmentstatus to 'enrolled'
            $sql_update_status = "UPDATE studentaccount SET enrollmentstatus = 'enrolled' WHERE studentacc_id = ?";
            $stmt = $pdo->prepare($sql_update_status);
            $stmt->execute([$studentacc_id]);

            // Get the studentacc_id for redirect
            $redirect_id = $studentacc_id;

            // Delete from pending_forms
            $sql_delete_pending = "DELETE FROM pending_forms WHERE pending_id = ?";
            $stmt = $pdo->prepare($sql_delete_pending);
            $stmt->execute([$pending_id]);

            // Commit transaction
            $pdo->commit();

            // Fetch student email for notification
            $student_email = "";
            $sql_get_email = "SELECT studentacc_email FROM studentaccount WHERE studentacc_id = ?";
            $stmtEmail = $pdo->prepare($sql_get_email);
            if ($stmtEmail->execute([$studentacc_id])) {
                $emailRow = $stmtEmail->fetch(PDO::FETCH_ASSOC);
                if ($emailRow && !empty($emailRow['studentacc_email'])) {
                    $student_email = $emailRow['studentacc_email'];
                }
            }

            // Load SMTP config used for verification emails (known working)
            $smtp = @include __DIR__ . '/smtp_config.php';
            if (!is_array($smtp)) {
                $smtp = [];
            }

            // Trigger Python script to send enrollment confirmation email (best-effort),
            // using the same calling pattern as send_code.py in register.php
            if (!empty($student_email)) {
                $emailArg  = escapeshellarg($student_email);
                $host      = escapeshellarg($smtp['SMTP_HOST'] ?? 'smtp.gmail.com');
                $port      = escapeshellarg((string)($smtp['SMTP_PORT'] ?? '587'));
                $user      = escapeshellarg($smtp['SMTP_USER'] ?? '');
                $pass      = escapeshellarg($smtp['SMTP_PASS'] ?? '');
                $fromEmail = escapeshellarg($smtp['FROM_EMAIL'] ?? '');
                // Use a clearer sender name for enrollment confirmation emails
                $fromName  = escapeshellarg('Maragusan National High School - Enrollment');

                $pythonPaths = [
                    'C:\\Users\\raymo\\AppData\\Local\\Programs\\Python\\Python313\\python.exe',
                    'python',
                    'py',
                    'C:\\Python\\python.exe'
                ];
                $scriptDir = __DIR__;
                $sendOutput = '';
                $debugInfo  = [];

                foreach ($pythonPaths as $pythonPath) {
                    $cmd = "cd /d \"$scriptDir\" && $pythonPath send_enrolled_email.py $emailArg --smtp-host $host --smtp-port $port --smtp-user $user --smtp-pass $pass --from-email $fromEmail --from-name $fromName 2>&1";
                    $sendOutput = shell_exec($cmd);
                    $debugInfo[] = "Command: $cmd | Output: " . ($sendOutput ?: 'No output');

                    if ($sendOutput && strpos($sendOutput, 'sent') !== false) {
                        break;
                    }
                }

                if (!$sendOutput || strpos($sendOutput, 'sent') === false) {
                    error_log("Enrollment email sending failed. Debug info: " . implode(' | ', $debugInfo));
                }
            }

            echo json_encode(['success' => true, 'studentacc_id' => $studentacc_id]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Pending form not found']);
        }
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>
