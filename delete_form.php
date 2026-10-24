<?php
session_start();
require_once 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];

    try {
        // Delete from pending_forms table
        $sql = "DELETE FROM pending_forms WHERE pending_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);

        echo "Form deleted successfully.";
    } catch (PDOException $e) {
        echo "Error deleting form: " . $e->getMessage();
    }
} else {
    echo "Invalid request.";
}
?>
