<?php
// Disable error display to prevent HTML output in JSON response
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Start output buffering to catch any unexpected output
ob_start();

try {
    require_once 'db_connection.php';
    
    // Set JSON header
    header('Content-Type: application/json');
    
    $studentacc_id = null;
    
    // Check if studentacc_id is provided directly
    if (isset($_GET['studentacc_id'])) {
        $studentacc_id = $_GET['studentacc_id'];
        // Validate studentacc_id
        if (!is_numeric($studentacc_id)) {
            throw new Exception('Invalid studentacc_id format');
        }
    }
    // Otherwise, check for pending_id
    else if (isset($_GET['pending_id'])) {
        $pending_id = $_GET['pending_id'];
        
        // Validate pending_id
        if (!is_numeric($pending_id)) {
            throw new Exception('Invalid pending_id format');
        }

        // Get studentacc_id from pending_forms
        $sql_pending = "SELECT studentacc_id FROM pending_forms WHERE pending_id = ?";
        $stmt_pending = $pdo->prepare($sql_pending);
        $stmt_pending->execute([$pending_id]);
        $pending_data = $stmt_pending->fetch(PDO::FETCH_ASSOC);

        if (!$pending_data) {
            throw new Exception('Pending form not found');
        }
        
        $studentacc_id = $pending_data['studentacc_id'];
    } else {
        throw new Exception('Invalid request - missing pending_id or studentacc_id');
    }

    // Fetch data from all related tables
    $data = [];

    // Student Account
    $sql_studentacc = "SELECT studentacc_username, studentacc_password, studentacc_email, enrollmentstatus FROM studentaccount WHERE studentacc_id = ?";
    $stmt = $pdo->prepare($sql_studentacc);
    $stmt->execute([$studentacc_id]);
    $data['studentaccount'] = $stmt->fetch(PDO::FETCH_ASSOC);

    // LPI - Fixed column name from 'fourps' to '4ps'
    $sql_lpi = "SELECT profile_picture, school_year, lrn, Gradelvl_enroll, psa_no, psa_picture, card_pic, good_moral, birthdate, age, sex, pob, religion, fname, mname, lname, extname, mother_tongue, ip, fourps FROM lpi WHERE studentacc_id = ?";
    $stmt = $pdo->prepare($sql_lpi);
    $stmt->execute([$studentacc_id]);
    $data['lpi'] = $stmt->fetch(PDO::FETCH_ASSOC);

    // Current Address
    $sql_current = "SELECT currenthouse_no, current_streetname, current_barangay, current_municipality, current_province, current_country, current_zipcode FROM current_address WHERE studentacc_id = ?";
    $stmt = $pdo->prepare($sql_current);
    $stmt->execute([$studentacc_id]);
    $data['current_address'] = $stmt->fetch(PDO::FETCH_ASSOC);

    // Permanent Address
    $sql_perm = "SELECT permhouse_no, perm_streetname, perm_barangay, perm_municipality, perm_province, perm_country, perm_zipcode FROM permanent_address WHERE studentacc_id = ?";
    $stmt = $pdo->prepare($sql_perm);
    $stmt->execute([$studentacc_id]);
    $data['permanent_address'] = $stmt->fetch(PDO::FETCH_ASSOC);

    // Parent Info
    $sql_parent = "SELECT father_fname, father_mname, father_lname, mother_fname, mother_mname, mother_lname, guardian_fname, guardian_mname, guardian_lname, fathercontact_no, mothercontact_no, guardiancontact_no FROM parent_info WHERE studentacc_id = ?";
    $stmt = $pdo->prepare($sql_parent);
    $stmt->execute([$studentacc_id]);
    $data['parent_info'] = $stmt->fetch(PDO::FETCH_ASSOC);

    // LSHS
    $sql_lshs = "SELECT shs_semester, shs_track, shs_strand FROM lshs WHERE studentacc_id = ?";
    $stmt = $pdo->prepare($sql_lshs);
    $stmt->execute([$studentacc_id]);
    $data['lshs'] = $stmt->fetch(PDO::FETCH_ASSOC);

    // SNE
    $sql_sne = "SELECT type_disability FROM sne WHERE studentacc_id = ?";
    $stmt = $pdo->prepare($sql_sne);
    $stmt->execute([$studentacc_id]);
    $data['sne'] = $stmt->fetch(PDO::FETCH_ASSOC);

    // Transferee
    $sql_trans = "SELECT last_gradelevel, last_sycompleted, last_schoolattended, last_schoolid FROM transferee WHERE studentacc_id = ?";
    $stmt = $pdo->prepare($sql_trans);
    $stmt->execute([$studentacc_id]);
    $data['transferee'] = $stmt->fetch(PDO::FETCH_ASSOC);

    // Learning Modalities
    $sql_lm = "SELECT learning_mod FROM learning_modalities WHERE studentacc_id = ?";
    $stmt = $pdo->prepare($sql_lm);
    $stmt->execute([$studentacc_id]);
    $data['learning_modalities'] = $stmt->fetch(PDO::FETCH_ASSOC);

    // Clear any output buffer content
    ob_clean();
    
    // Return JSON
    echo json_encode($data);
    
} catch (Exception $e) {
    // Clear any output buffer content
    ob_clean();
    
    // Return error as JSON
    echo json_encode(['error' => $e->getMessage()]);
} catch (PDOException $e) {
    // Clear any output buffer content
    ob_clean();
    
    // Return database error as JSON
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}

// End output buffering
ob_end_flush();
?>