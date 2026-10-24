<?php
session_start();
require_once 'db_connection.php';
require_once 'cloudinary_helper.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $pdo->beginTransaction();
        
        $studentacc_id = $_SESSION['studentacc_id'] ?? null;
        if (!$studentacc_id) {
            throw new Exception("User not logged in or studentacc_id not found");
        }

        // Handle file uploads to Cloudinary
        $profile_picture_url = '';
        $psa_picture_url = '';
        $card_pic_url = '';
        $good_moral_url = '';

        // Upload profile picture to Cloudinary
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
            $result = uploadFileToCloudinary(
                $_FILES['profile_picture'], 
                'student_enrollment', 
                'profile_' . $studentacc_id
            );
            if (!$result || empty($result['url'])) {
                throw new Exception("Failed to upload profile picture to Cloudinary");
            }
            $profile_picture_url = $result['url'];
        }

        // Upload PSA picture to Cloudinary
        if (isset($_FILES['psa_picture']) && $_FILES['psa_picture']['error'] === UPLOAD_ERR_OK) {
            $result = uploadFileToCloudinary(
                $_FILES['psa_picture'], 
                'student_enrollment', 
                'psa_' . $studentacc_id
            );
            if (!$result || empty($result['url'])) {
                throw new Exception("Failed to upload PSA picture to Cloudinary");
            }
            $psa_picture_url = $result['url'];
        }

        // Upload Card picture to Cloudinary
        if (isset($_FILES['card_pic']) && $_FILES['card_pic']['error'] === UPLOAD_ERR_OK) {
            $result = uploadFileToCloudinary(
                $_FILES['card_pic'], 
                'student_enrollment', 
                'card_' . $studentacc_id
            );
            if (!$result || empty($result['url'])) {
                throw new Exception("Failed to upload Card picture to Cloudinary");
            }
            $card_pic_url = $result['url'];
        }

        // Upload Good Moral picture to Cloudinary
        if (isset($_FILES['good_moral']) && $_FILES['good_moral']['error'] === UPLOAD_ERR_OK) {
            $result = uploadFileToCloudinary(
                $_FILES['good_moral'], 
                'student_enrollment', 
                'good_moral_' . $studentacc_id
            );
            if (!$result || empty($result['url'])) {
                throw new Exception("Failed to upload Good Moral picture to Cloudinary");
            }
            $good_moral_url = $result['url'];
        }

        // 1. Insert into lpi table
        $lpi_sql = "INSERT INTO lpi (profile_picture, school_year, lrn, gradelvl_enroll, psa_no, psa_picture, card_pic, good_moral, birthdate, age, sex, pob, religion, fname, mname, lname, extname, mother_tongue, ip, fourps, studentacc_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $school_year = $_POST['school_year'] ?? '';
        $lrn = $_POST['lrn'] ?? '';

        // Handle graded/non-graded logic
        $graded_check = isset($_POST['gradedCheck']) ? true : false;
        $non_graded_check = isset($_POST['nonGradedCheck']) ? true : false;
        $grade_level_input = $_POST['gradeLevelInput'] ?? '';

        if ($graded_check && !empty($grade_level_input)) {
            $gradelvl_enroll = $grade_level_input;
        } elseif ($non_graded_check) {
            $gradelvl_enroll = 'Non-Graded';
        } else {
            $gradelvl_enroll = '';
        }

        $psa_no = $_POST['psa_birth_cert_no'] ?? '';
        $birthdate = $_POST['birthday'] ?? '';
        $age = $_POST['age'] ?? '';

        // Handle sex
        $sex = '';
        if (isset($_POST['sex'])) {
            if (is_array($_POST['sex'])) {
                $sex = implode(',', $_POST['sex']);
            } else {
                $sex = $_POST['sex'];
            }
        }

        $pob = $_POST['place_of_birth'] ?? '';
        $religion = $_POST['religion'] ?? '';
        $fname = $_POST['first_name'] ?? '';
        $mname = $_POST['middle_name'] ?? '';
        $lname = $_POST['last_name'] ?? '';
        $extname = $_POST['extension_name'] ?? '';
        $mother_tongue = $_POST['mother_tongue'] ?? '';

        // Handle IP
        $ip_yes = isset($_POST['ipYes']) ? true : false;
        $ip_no = isset($_POST['ipNo']) ? true : false;
        $ip_specify = $_POST['ipSpecifyInput'] ?? '';

        if ($ip_yes && !empty($ip_specify)) {
            $ip = $ip_specify;
        } elseif ($ip_no) {
            $ip = 'N/A';
        } else {
            $ip = '';
        }

        // Handle 4Ps
        $fourps_yes = isset($_POST['fourPsYes']) ? true : false;
        $fourps_no = isset($_POST['fourPsNo']) ? true : false;
        $fourps_id = $_POST['fourPsIdInput'] ?? '';

        if ($fourps_yes && !empty($fourps_id)) {
            $fourps = $fourps_id;
        } elseif ($fourps_no) {
            $fourps = 'N/A';
        } else {
            $fourps = '';
        }

        $stmt = $pdo->prepare($lpi_sql);
        $stmt->execute([$profile_picture_url, $school_year, $lrn, $gradelvl_enroll, $psa_no, $psa_picture_url, $card_pic_url, $good_moral_url, $birthdate, $age, $sex, $pob, $religion, $fname, $mname, $lname, $extname, $mother_tongue, $ip, $fourps, $studentacc_id]);
        $lpi_id = $pdo->lastInsertId();

        // 2. Insert into current_address table
        $current_address_sql = "INSERT INTO current_address (currenthouse_no, current_streetname, current_barangay, current_municipality, current_province, current_country, current_zipcode, studentacc_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $curr_house_no = $_POST['curr_house_no'] ?? '';
        $curr_street = $_POST['curr_street'] ?? '';
        $curr_barangay = $_POST['curr_barangay'] ?? '';
        $curr_city = $_POST['curr_city'] ?? '';
        $curr_province = $_POST['curr_province'] ?? '';
        $curr_country = $_POST['curr_country'] ?? '';
        $curr_zip = $_POST['curr_zip'] ?? '';

        $stmt = $pdo->prepare($current_address_sql);
        $stmt->execute([$curr_house_no, $curr_street, $curr_barangay, $curr_city, $curr_province, $curr_country, $curr_zip, $studentacc_id]);
        $currentadd_id = $pdo->lastInsertId();

        // 3. Insert into permanent_address table
        $same_as_current = isset($_POST['sameAsCurrent']) && $_POST['sameAsCurrent'] == 'on';

        if ($same_as_current) {
            // Copy current address data
            $perm_house_no = $curr_house_no;
            $perm_street = $curr_street;
            $perm_barangay = $curr_barangay;
            $perm_city = $curr_city;
            $perm_province = $curr_province;
            $perm_country = $curr_country;
            $perm_zip = $curr_zip;
        } else {
            // Use permanent address data
            $perm_house_no = $_POST['perm_house_no'] ?? '';
            $perm_street = $_POST['perm_street'] ?? '';
            $perm_barangay = $_POST['perm_barangay'] ?? '';
            $perm_city = $_POST['perm_city'] ?? '';
            $perm_province = $_POST['perm_province'] ?? '';
            $perm_country = $_POST['perm_country'] ?? '';
            $perm_zip = $_POST['perm_zip'] ?? '';
        }

        $permanent_address_sql = "INSERT INTO permanent_address (permhouse_no, perm_streetname, perm_barangay, perm_municipality, perm_province, perm_country, perm_zipcode, studentacc_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $pdo->prepare($permanent_address_sql);
        $stmt->execute([$perm_house_no, $perm_street, $perm_barangay, $perm_city, $perm_province, $perm_country, $perm_zip, $studentacc_id]);
        $perm_id = $pdo->lastInsertId();

        // 4. Insert into parent_info table
        $parent_sql = "INSERT INTO parent_info (father_fname, father_mname, father_lname, mother_fname, mother_mname, mother_lname, guardian_fname, guardian_mname, guardian_lname, fathercontact_no, mothercontact_no, guardiancontact_no, studentacc_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $father_fname = $_POST['father_first_name'] ?? '';
        $father_mname = $_POST['father_middle_name'] ?? '';
        $father_lname = $_POST['father_last_name'] ?? '';
        $father_contact = $_POST['father_contact'] ?? '';

        $mother_fname = $_POST['mother_first_name'] ?? '';
        $mother_mname = $_POST['mother_middle_name'] ?? '';
        $mother_lname = $_POST['mother_last_name'] ?? '';
        $mother_contact = $_POST['mother_contact'] ?? '';

        $guardian_fname = $_POST['guardian_first_name'] ?? '';
        $guardian_mname = $_POST['guardian_middle_name'] ?? '';
        $guardian_lname = $_POST['guardian_last_name'] ?? '';
        $guardian_contact = $_POST['guardian_contact'] ?? '';

        $stmt = $pdo->prepare($parent_sql);
        $stmt->execute([$father_fname, $father_mname, $father_lname, $mother_fname, $mother_mname, $mother_lname, $guardian_fname, $guardian_mname, $guardian_lname, $father_contact, $mother_contact, $guardian_contact, $studentacc_id]);
        $parent_id = $pdo->lastInsertId();

        // 5. Insert into sne table
        $sned_yes = isset($_POST['snedYes']) ? true : false;
        $sned_no = isset($_POST['snedNo']) ? true : false;

        $disabilities = [];
        $disability_fields = ['disAutism', 'disADHD', 'disBlind', 'disCP', 'disDeaf', 'disEBD', 'disHI', 'disID', 'disLD', 'disOPH', 'disSLD', 'disVI'];
        foreach ($disability_fields as $field) {
            if (isset($_POST[$field])) {
                $disabilities[] = $_POST[$field];
            }
        }

        $dis_other = isset($_POST['disOther']) ? true : false;
        $dis_other_specify = $_POST['disOtherSpecify'] ?? '';

        if ($dis_other && !empty($dis_other_specify)) {
            $disabilities[] = $dis_other_specify;
        }

        if ($sned_yes && !empty($disabilities)) {
            $type_disability = implode(', ', $disabilities);
        } elseif ($sned_no) {
            $type_disability = 'N/A';
        } else {
            $type_disability = '';
        }

        $sne_sql = "INSERT INTO sne (type_disability, studentacc_id) VALUES (?, ?)";
        $stmt = $pdo->prepare($sne_sql);
        $stmt->execute([$type_disability, $studentacc_id]);
        $sne_id = $pdo->lastInsertId();

        // 6. Insert into transferee table
        $transferee_sql = "INSERT INTO transferee (last_gradelevel, last_sycompleted, last_schoolattended, last_schoolid, studentacc_id) VALUES (?, ?, ?, ?, ?)";

        $last_grade_completed = $_POST['last_grade_completed'] ?? '';
        $last_sy_completed = $_POST['last_sy_completed'] ?? '';
        $last_school_attended = $_POST['last_school_attended'] ?? '';
        $last_school_id = $_POST['last_school_id'] ?? '';

        $stmt = $pdo->prepare($transferee_sql);
        $stmt->execute([$last_grade_completed, $last_sy_completed, $last_school_attended, $last_school_id, $studentacc_id]);
        $trans_id = $pdo->lastInsertId();

        // 7. Insert into lshs table
        $shs_na = isset($_POST['shsNa']) ? true : false;

        if ($shs_na) {
            $shs_semester = 'N/A';
            $shs_track = 'N/A';
            $shs_strand = 'N/A';
        } else {
            $sem1 = isset($_POST['sem1']) ? true : false;
            $sem2 = isset($_POST['sem2']) ? true : false;

            if ($sem1) {
                $shs_semester = '1st';
            } elseif ($sem2) {
                $shs_semester = '2nd';
            } else {
                $shs_semester = '';
            }

            $shs_track = $_POST['shs_track'] ?? '';
            $shs_strand = $_POST['shs_strand'] ?? '';
        }

        $lshs_sql = "INSERT INTO lshs (shs_semester, shs_track, shs_strand, studentacc_id) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($lshs_sql);
        $stmt->execute([$shs_semester, $shs_track, $shs_strand, $studentacc_id]);
        $shs_id = $pdo->lastInsertId();

        // 8. Insert into learning_modalities table
        $learning_mod = $_POST['preferredModality'] ?? '';

        $lm_sql = "INSERT INTO learning_modalities (learning_mod, studentacc_id) VALUES (?, ?)";
        $stmt = $pdo->prepare($lm_sql);
        $stmt->execute([$learning_mod, $studentacc_id]);
        $lm_id = $pdo->lastInsertId();

        // 9. Insert into pending_forms table
        $pending_forms_sql = "INSERT INTO pending_forms (pending_date, currentadd_id, lm_id, lpi_id, shs_id, parent_id, perm_id, sne_id, studentacc_id, trans_id) VALUES (NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($pending_forms_sql);
        $stmt->execute([$currentadd_id, $lm_id, $lpi_id, $shs_id, $parent_id, $perm_id, $sne_id, $studentacc_id, $trans_id]);
        
        $pdo->commit();
        
        // Redirect with success message
        header('Location: dashboard.php?success=1');
        exit;
        
    } catch (Exception $e) {
        $pdo->rollback();
        error_log("Enrollment error: " . $e->getMessage());
        // For debugging, let's show the actual error
        header('Location: dashboard.php?error=1&debug=' . urlencode($e->getMessage()));
        exit;
    }
} else {
    header('Location: dashboard.php');
    exit;
}
?>
