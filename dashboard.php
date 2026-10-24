<?php

session_start();
if (empty($_SESSION['logged_in'])) {
    header('Location: login.php');
    exit;
}

require_once 'db_connection.php';

$sql_studentaccount = "SELECT * FROM studentaccount WHERE studentacc_id = ?";
$stmt = $pdo->prepare($sql_studentaccount);
$stmt->execute([$_SESSION['studentacc_id']]);
$studentaccount = $stmt->fetch(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="icon" href="adminicons/logomnhs.png" type="image/png">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="adminicons/logomnhs.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <style>
        html, body { 
            background-color: #f8f9fa; 
            overflow-x: hidden; 
            font-family: 'Poppins', sans-serif; 
            height: auto;
        }
        body {
            padding-bottom: 0;
            margin-bottom: 0;
        }
        * { font-family: 'Poppins', sans-serif !important; }
        .sidebar-link { text-decoration: none; }
        .form-section-title { font-weight: 600; margin-top: 1.5rem; }
        .muted-note { font-size: .95rem; white-space: pre-line; }
      
       
    </style>
    </head>
<body>
        <div class="d-flex">
        <?php include 'studentsidebar.php'; ?>
        <div id="content" class="flex-grow-1 p-4">
            
            <div id="dashboard" class="mb-4">
                    <h3 class="mb-1">Welcome</h3>
                    <p class="text-muted mb-4">Logged in as <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></p>
                    
                    <?php if (isset($_GET['success']) && $_GET['success'] == '1'): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>Success!</strong> Your enrollment form has been submitted successfully.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($_GET['error']) && $_GET['error'] == '1'): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Error!</strong> There was an error submitting your enrollment form. Please try again.
                            <?php if (isset($_GET['debug'])): ?>
                                <br><br><strong>Debug Info:</strong> <?php echo htmlspecialchars($_GET['debug']); ?>
                            <?php endif; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
            </div>

                <div class="card shadow-sm mb-5" style="margin-bottom: 3rem !important;">
                    <div class="card-body">
                        
                        <div class="alert alert-info muted-note" role="alert">
Instructitons

Please fill out all fields completely and accurately.

Use CAPITAL letters when typing names and important details.

Check (✔) all appropriate boxes.

Input (N/A) for not applicable fields.

Incomplete forms may delay your enrollment process.
                        </div>

                        <?php if ($studentaccount['enrollmentstatus'] === 'enrolled'): ?>
                            <div class="d-flex align-items-center justify-content-center" style="min-height:240px;">
                                <div class="text-center">
                                    <h4 class="mb-3">Already Enrolled in Maragusan National High School</h4>
                                    <p class="text-muted">You are already enrolled. You can view your profile or check notifications.</p>
                                </div>
                            </div>
                        <?php elseif (isset($_GET['success']) && $_GET['success'] === '1'): ?>
                            <div class="d-flex align-items-center justify-content-center" style="min-height:240px;">
                                <div class="text-center">
                                    <h4 class="mb-3">Pending enrollment for the approval of admin</h4>
                                    <p class="text-muted">Your submission has been received and is awaiting admin approval. You cannot submit another enrollment form while this request is pending.</p>
                                </div>
                            </div>
                        <?php else: ?>
                        <form id="enrollmentForm" autocomplete="off" method="post" action="process_enrollment.php" enctype="multipart/form-data">
                            <div id="step1Container">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-bold">Profile Picture</label>
                                    <input type="file" class="form-control" name="profile_picture" accept="image/*">
                                    <div class="form-text">Upload a clear photo of yourself (JPG, PNG, GIF)</div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold">School Year</label>
                                    <select class="form-select" name="school_year" required>
                                        <option value="" disabled selected>Select School Year</option>
                                        
                                        <option>2024-2025</option>
                                        <option>2025-2026</option>
                                        <option>2026-2027</option>
                                        <option>2027-2028</option>
                                        <option>2028-2029</option>
                                        <option>2029-2030</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold">LRN</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="lrn"
                                        placeholder="Learner Reference Number"
                                        inputmode="numeric"
                                        maxlength="12"
                                        required>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-bold">Grade level to enroll</label>
                                    <div class="d-flex align-items-center flex-wrap gap-3">
                                        
                                        <div class="form-check me-3">
                                            <input class="form-check-input" type="checkbox" id="gradedCheck" name="gradedCheck">
                                            <label class="form-check-label" for="gradedCheck">Graded</label>
                                        </div>
                                        <div id="gradeLevelGroup" class="d-none">
                                            <label class="form-label me-2">Specify Grade Level (1–12):</label>
                                            <input
                                                type="number"
                                                class="form-control d-inline-block"
                                                style="width: 200px;"
                                                id="gradeLevelInput"
                                                name="gradeLevelInput"
                                                min="1"
                                                max="12"
                                                step="1"
                                                required>
                                        </div>
                                        <div class="form-check ms-0 ms-md-3">
                                            <input class="form-check-input" type="checkbox" id="nonGradedCheck" name="nonGradedCheck">
                                            <label class="form-check-label" for="nonGradedCheck">Non-Graded (For Special Needs Education (SNEd) Only)</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">
                            <h5 class="form-section-title">Learner's Personal Information</h5>

                            <div class="row g-3 mt-1">
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold">PSA Birth Certificate No.</label>
                                    <input type="number" class="form-control" name="psa_birth_cert_no">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold">PSA Birth Certificate Picture</label>
                                    <input type="file" class="form-control" name="psa_picture" accept="image/*">
                                    <div class="form-text">Upload a clear photo of your PSA Birth Certificate (JPG, PNG, GIF)</div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold">Card Picture</label>
                                    <input type="file" class="form-control" name="card_pic" accept="image/*">
                                    <div class="form-text">Upload a clear photo of your Card (JPG, PNG, GIF)</div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold">Good Moral Picture</label>
                                    <input type="file" class="form-control" name="good_moral" accept="image/*">
                                    <div class="form-text">Upload a clear photo of your Good Moral Certificate (JPG, PNG, GIF)</div>
                                </div>
                                <div class="col-12 col-md-3">
                                    <label class="form-label fw-bold">Birthday</label>
                                    <input type="date" class="form-control" name="birthday">
                                </div>
                                <div class="col-12 col-md-3">
                                    <label class="form-label fw-bold">Age</label>
                                    <input type="number" class="form-control" name="age" min="1">
                                </div>

                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Sex</label><br>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" name="sex" id="sexMale" value="Male" onclick="toggleSexCheckbox(this)">
                                            <label class="form-check-label" for="sexMale">Male</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" name="sex" id="sexFemale" value="Female" onclick="toggleSexCheckbox(this)">
                                            <label class="form-check-label" for="sexFemale">Female</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold">Place of Birth (Municipality/City)</label>
                                    <input type="text" class="form-control" name="place_of_birth">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold">Religion</label>
                                    <input type="text" class="form-control" name="religion">
                                </div>

                                <div class="col-12 col-md-3">
                                    <label class="form-label fw-bold">Last Name</label>
                                    <input type="text" class="form-control" name="last_name">
                                </div>
                                <div class="col-12 col-md-3">
                                    <label class="form-label fw-bold">First Name</label>
                                    <input type="text" class="form-control" name="first_name">
                                </div>
                                <div class="col-12 col-md-3">
                                    <label class="form-label fw-bold">Middle Name</label>
                                    <input type="text" class="form-control" name="middle_name">
                                </div>
                                <div class="col-12 col-md-3">
                                    <label class="form-label fw-bold">Extension Name (e.g., Jr., III)</label>
                                    <input type="text" class="form-control" name="extension_name" placeholder="e.g., Jr., III">
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold">Mother Tongue</label>
                                    <input type="text" class="form-control" name="mother_tongue">
                                </div>

                                <div class="col-12">
                                    <label class="form-label d-block fw-bold">Belonging to any Indigenous Peoples (IP) Community/Indigenous Cultural Community?</label>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" id="ipYes" name="ipYes">
                                        <label class="form-check-label" for="ipYes">Yes</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" id="ipNo" name="ipNo">
                                        <label class="form-check-label" for="ipNo">No</label>
                                    </div>
                                    <div id="ipSpecifyGroup" class="mt-2 d-none">
                                        <label class="form-label me-2">If Yes, please specify:</label>
                                        <input type="text" class="form-control" id="ipSpecifyInput" name="ipSpecifyInput" placeholder="Specify community">
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label d-block fw-bold">Is your family a beneficiary of 4Ps?</label>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" id="fourPsYes" name="fourPsYes">
                                        <label class="form-check-label" for="fourPsYes">Yes</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" id="fourPsNo" name="fourPsNo">
                                        <label class="form-check-label" for="fourPsNo">No</label>
                                    </div>
                                    <div id="fourPsIdGroup" class="mt-2 d-none">
                                        <label class="form-label me-2 fw-bold">If Yes, 4Ps Household ID Number:</label>
                                        <input type="text" class="form-control" id="fourPsIdInput" name="fourPsIdInput" placeholder="Enter Household ID Number">
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">
                            <h5 class="form-section-title">Current Address</h5>
                            <div class="row g-3 mt-1">
                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-bold">House No.</label>
                                    <input type="number" class="form-control" name="curr_house_no">
                                </div>
                                <div class="col-12 col-md-8">
                                    <label class="form-label fw-bold">Sitio/Street Name</label>
                                    <input type="text" class="form-control" name="curr_street">
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-bold">Barangay</label>
                                    <input type="text" class="form-control" name="curr_barangay">
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-bold">Municipality/City</label>
                                    <input type="text" class="form-control" name="curr_city">
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-bold">Province</label>
                                    <input type="text" class="form-control" name="curr_province">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold">Country</label>
                                    <input type="text" class="form-control" name="curr_country">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold">Zip Code</label>
                                    <input type="number" class="form-control" name="curr_zip">
                                </div>
                            </div>

                            <hr class="my-4">
                            <h5 class="form-section-title">Permanent Address</h5>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="sameAsCurrent">
                                <label class="form-check-label" for="sameAsCurrent">Same with Current Address</label>
                            </div>
                            <div id="permAddressFields" class="row g-3 mt-1">
                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-bold">House No.</label>
                                    <input type="text" class="form-control" name="perm_house_no">
                                </div>
                                <div class="col-12 col-md-8">
                                    <label class="form-label fw-bold">Sitio/Street Name</label>
                                    <input type="text" class="form-control" name="perm_street">
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-bold">Barangay</label>
                                    <input type="text" class="form-control" name="perm_barangay">
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-bold">Municipality/City</label>
                                    <input type="text" class="form-control" name="perm_city">
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-bold">Province</label>
                                    <input type="text" class="form-control" name="perm_province">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold">Country</label>
                                    <input type="text" class="form-control" name="perm_country">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold">Zip Code</label>
                                    <input type="text" class="form-control" name="perm_zip">
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <button type="button" id="toStep2" class="btn btn-primary">Next</button>
                            </div>
                            </div>

                            <div id="step2Container" class="d-none">
                                 <hr class="my-4">
                                 <h5 class="form-section-title">4. Parent’s/Guardian’s Information</h5>
                                 <div class="row g-3 mt-1">
                                     <div class="col-12"><strong>Father’s Name</strong></div>
                                     <div class="col-12 col-md-3">
                                        <label class="form-label">Last Name</label>
                                        <input type="text" class="form-control" name="father_last_name" maxlength="100" autocomplete="new-password" required>
                                     </div>
                                     <div class="col-12 col-md-3">
                                        <label class="form-label">First Name</label>
                                        <input type="text" class="form-control" name="father_first_name" maxlength="100" autocomplete="new-password" required>
                                     </div>
                                     <div class="col-12 col-md-3">
                                        <label class="form-label">Middle Name</label>
                                        <input type="text" class="form-control" name="father_middle_name" maxlength="100" autocomplete="new-password" required>
                                     </div>
                                     <div class="col-12 col-md-3">
                                        <label class="form-label">Contact No.</label>
                                        <input
                                            type="tel"
                                            class="form-control"
                                            name="father_contact"
                                            maxlength="11"
                                            inputmode="numeric"
                                            autocomplete="new-password"
                                            required>
                                     </div>
 
                                      <div class="col-12"><strong>Mother's Maiden Name</strong></div>
                                     <div class="col-12 col-md-3">
                                        <label class="form-label">Last Name</label>
                                        <input type="text" class="form-control" name="mother_last_name" maxlength="100" autocomplete="new-password" required>
                                     </div>
                                     <div class="col-12 col-md-3">
                                        <label class="form-label">First Name</label>
                                        <input type="text" class="form-control" name="mother_first_name" maxlength="100" autocomplete="new-password" required>
                                     </div>
                                     <div class="col-12 col-md-3">
                                        <label class="form-label">Middle Name</label>
                                        <input type="text" class="form-control" name="mother_middle_name" maxlength="100" autocomplete="new-password" required>
                                     </div>
                                     <div class="col-12 col-md-3">
                                        <label class="form-label">Contact No.</label>
                                        <input
                                            type="tel"
                                            class="form-control"
                                            name="mother_contact"
                                            maxlength="11"
                                            inputmode="numeric"
                                            autocomplete="new-password"
                                            required>
                                     </div>
 
                                      <div class="col-12"><strong>Guardian's Name</strong></div>
                                     <div class="col-12 col-md-3">
                                        <label class="form-label">Last Name</label>
                                        <input type="text" class="form-control" name="guardian_last_name" maxlength="100" autocomplete="new-password" required>
                                     </div>
                                     <div class="col-12 col-md-3">
                                        <label class="form-label">First Name</label>
                                        <input type="text" class="form-control" name="guardian_first_name" maxlength="100" autocomplete="new-password" required>
                                     </div>
                                     <div class="col-12 col-md-3">
                                        <label class="form-label">Middle Name</label>
                                        <input type="text" class="form-control" name="guardian_middle_name" maxlength="100" autocomplete="new-password" required>
                                     </div>
                                     <div class="col-12 col-md-3">
                                        <label class="form-label">Contact No.</label>
                                        <input
                                            type="tel"
                                            class="form-control"
                                            name="guardian_contact"
                                            maxlength="11"
                                            inputmode="numeric"
                                            autocomplete="new-password"
                                            required>
                                     </div>
                                     
                                 </div>
 
                                 
                                 <hr class="my-4">
                                 <h5 class="form-section-title">5. Special Needs Education (SNEd)</h5>
                                 <div class="row g-3 mt-1">
                                     <div class="col-12">
                                         <label class="form-label d-block">Is the Learner under the Special Needs Education Program?</label>
                                         <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="snedYes" name="snedYes" >
                                             <label class="form-check-label" for="snedYes">Yes</label>
                                         </div>
                                         <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="snedNo" name="snedNo">
                                             <label class="form-check-label" for="snedNo">No</label>
                                         </div>
                                     </div>
                                    <div class="col-12 d-none" id="snedDisabilityGroup">
                                         <label class="form-label d-block">6. With Disability (Check if applicable)</label>
                                         <div class="row g-2">
                                             <div class="col-12 col-md-6">
                                                 <div class="form-check"><input class="form-check-input" type="checkbox" id="disAutism" name="disAutism" value="Autism Spectrum Disorder"><label class="form-check-label" for="disAutism">Autism Spectrum Disorder</label></div>
                                                 <div class="form-check"><input class="form-check-input" type="checkbox" id="disADHD" name="disADHD" value="Attention Deficit Hyperactivity Disorder"><label class="form-check-label" for="disADHD">Attention Deficit Hyperactivity Disorder</label></div>
                                                 <div class="form-check"><input class="form-check-input" type="checkbox" id="disBlind" name="disBlind" value="Blind"><label class="form-check-label" for="disBlind">Blind</label></div>
                                                 <div class="form-check"><input class="form-check-input" type="checkbox" id="disCP" name="disCP" value="Cerebral Palsy"><label class="form-check-label" for="disCP">Cerebral Palsy</label></div>
                                                 <div class="form-check"><input class="form-check-input" type="checkbox" id="disDeaf" name="disDeaf" value="Deaf"><label class="form-check-label" for="disDeaf">Deaf</label></div>
                                                 <div class="form-check"><input class="form-check-input" type="checkbox" id="disEBD" name="disEBD" value="Emotional-Behavioral Disorder"><label class="form-check-label" for="disEBD">Emotional-Behavioral Disorder</label></div>
                                             </div>
                                             <div class="col-12 col-md-6">
                                                 <div class="form-check"><input class="form-check-input" type="checkbox" id="disHI" name="disHI" value="Hearing Impairment"><label class="form-check-label" for="disHI">Hearing Impairment</label></div>
                                                 <div class="form-check"><input class="form-check-input" type="checkbox" id="disID" name="disID" value="Intellectual Disability"><label class="form-check-label" for="disID">Intellectual Disability</label></div>
                                                 <div class="form-check"><input class="form-check-input" type="checkbox" id="disLD" name="disLD" value="Learning Disability"><label class="form-check-label" for="disLD">Learning Disability</label></div>
                                                 <div class="form-check"><input class="form-check-input" type="checkbox" id="disOPH" name="disOPH" value="Orthopedic/Physical Handicap"><label class="form-check-label" for="disOPH">Orthopedic/Physical Handicap</label></div>
                                                 <div class="form-check"><input class="form-check-input" type="checkbox" id="disSLD" name="disSLD" value="Speech/Language Disorder"><label class="form-check-label" for="disSLD">Speech/Language Disorder</label></div>
                                                 <div class="form-check"><input class="form-check-input" type="checkbox" id="disVI" name="disVI" value="Visual Impairment"><label class="form-check-label" for="disVI">Visual Impairment</label></div>
                                                <div class="form-check d-flex align-items-center gap-2"><input class="form-check-input" type="checkbox" id="disOther" name="disOther"><label class="form-check-label" for="disOther">Others</label><input type="text" class="form-control d-none" id="disOtherSpecify" name="disOtherSpecify" placeholder="Please specify"></div>
                                             </div>
                                         </div>
                                     </div>
                                 </div>
 
                                 <hr class="my-4">
                                 <h5 class="form-section-title">7. For Returning Learner (Balik-Aral) / Transferee</h5>
                                 <div class="row g-3 mt-1">
                                    <div class="col-12 col-md-3"><label class="form-label">Last Grade Level Completed</label><input type="text" class="form-control" name="last_grade_completed" maxlength="50" autocomplete="new-password" required></div>
                                    <div class="col-12 col-md-3"><label class="form-label">Last School Year Completed</label><input type="text" class="form-control" name="last_sy_completed" placeholder="e.g., 2023-2024" maxlength="10" autocomplete="new-password" required></div>
                                    <div class="col-12 col-md-4"><label class="form-label">Last School Attended</label><input type="text" class="form-control" name="last_school_attended" maxlength="200" autocomplete="new-password" required></div>
                                    <div class="col-12 col-md-2"><label class="form-label">School ID</label><input type="text" class="form-control" name="last_school_id" maxlength="20" autocomplete="new-password" required></div>
                                 </div>
 
                                 <hr class="my-4">
                                 <h5 class="form-section-title">8. For Learner in Senior High School</h5>
                                 <div class="row g-3 mt-1">
                                     <div class="col-12">
                                         <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="shsNa" name="shsNa">
                                             <label class="form-check-label" for="shsNa">Not Applicable</label>
                                         </div>
                                     </div>
                                     <div class="col-12">
                                         <label class="form-label d-block">Semester</label>
                                         <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="sem1" name="sem1">
                                             <label class="form-check-label" for="sem1">1st</label>
                                         </div>
                                         <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="sem2" name="sem2">
                                             <label class="form-check-label" for="sem2">2nd</label>
                                         </div>
                                         <input type="hidden" id="shsSemesterHidden" name="shs_semester" value="">
                                     </div>
                                     <div class="col-12 col-md-6">
                                         <label class="form-label">Track</label>
                                         <select class="form-select" name="shs_track" id="shsTrack" autocomplete="new-password" required>
                                             <option value="" selected disabled>Choose Track</option>
                                             <option value="Technical-Vocational-Livelihood">Technical-Vocational-Livelihood</option>
                                             <option value="Academic">Academic</option>
                                             <option value="N/A">N/A</option>
                                         </select>
                                     </div>
                                     <div class="col-12 col-md-6">
                                         <label class="form-label">Strand</label>
                                        <select class="form-select" name="shs_strand" id="shsStrand" autocomplete="new-password" required>
                                             <option value="" selected disabled>Choose Strand</option>
                                             <option value="STEM">STEM</option>
                                             <option value="ABM">ABM</option>
                                             <option value="GAS">GAS</option>
                                             <option value="ICT">ICT</option>
                                             <option value="AFA">AFA</option>
                                             <option value="N/A">N/A</option>
                                         </select>
                                     </div>
                                 </div>
 
                                 <hr class="my-4">
                                 <h5 class="form-section-title">8. Preferred Distance Learning Modalities</h5>
                                 <div class="row g-3 mt-1">
                                     <div class="col-12 col-md-6 col-lg-4">
                                         <label class="form-label" for="preferredModality">Select Modality</label>
                                        <select id="preferredModality" name="preferredModality" class="form-select" autocomplete="new-password" required>
                                             <option value="" selected disabled>Choose...</option>
                                             <option value="Blended">Blended (Combination)</option>
                                             <option value="ModularPrint">Modular (Print)</option>
                                             <option value="ModularDigital">Modular (Digital)</option>
                                             <option value="Online">Online</option>
                                             <option value="EducationalTV">Educational TV (Television)</option>
                                             <option value="Radio">Radio-Based Instruction</option>
                                             <option value="Homeschooling">Homeschooling</option>
                                         </select>
                                     </div>
                                 </div>
 
                                 <div class="d-flex justify-content-between mt-4">
                                     <button type="button" id="backToStep1" class="btn btn-outline-secondary">Back</button>
                                     <button type="submit" class="btn btn-primary">Submit</button>
                                 </div>
                             </div>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (function(){
            var sidebar = document.getElementById('sidebar');
            var sidebarMenu = document.getElementById('sidebarMenu');
            if (sidebarMenu) {
                sidebarMenu.addEventListener('shown.bs.collapse', function(){
                    sidebar.classList.remove('sidebar-collapsed');
                });
                sidebarMenu.addEventListener('hidden.bs.collapse', function(){
                    sidebar.classList.add('sidebar-collapsed');
                    // Ensure the toggler stays aligned and clickable when collapsed
                    sidebar.scrollTop = 0;
                });
            }

            var step1 = document.getElementById('step1Container');
            var step2 = document.getElementById('step2Container');
            var nextBtn = document.getElementById('toStep2');
            if (nextBtn) {
                nextBtn.addEventListener('click', function(e) {
                    var step1 = document.getElementById('step1Container');
                    var valid = true;
                    var firstInvalid = null;

                    // Check all required input fields
                    var inputs = step1.querySelectorAll('input[type="text"], input[type="date"], input[type="number"]');
                    inputs.forEach(function(input) {
                        if (input.offsetParent !== null && input.hasAttribute('required') && !input.value.trim()) {
                            input.classList.add('is-invalid');
                            valid = false;
                            if (!firstInvalid) firstInvalid = input;
                        } else {
                            input.classList.remove('is-invalid');
                        }
                    });

                    // Additional numeric validation for LRN (max 12 digits, numbers only)
                    var lrnInput = step1.querySelector('input[name="lrn"]');
                    if (lrnInput && lrnInput.offsetParent !== null) {
                        var lrnVal = (lrnInput.value || '').toString().trim();
                        if (!/^\d{1,12}$/.test(lrnVal)) {
                            lrnInput.classList.add('is-invalid');
                            valid = false;
                            if (!firstInvalid) firstInvalid = lrnInput;
                        } else {
                            lrnInput.classList.remove('is-invalid');
                        }
                    }

                    // Check required checkboxes (example: sex, graded/non-graded, IP, 4Ps)
                    // Sex
                    var sexMale = document.getElementById('sexMale');
                    var sexFemale = document.getElementById('sexFemale');
                    if (sexMale && sexFemale && !sexMale.checked && !sexFemale.checked) {
                        sexMale.classList.add('is-invalid');
                        sexFemale.classList.add('is-invalid');
                        valid = false;
                        if (!firstInvalid) firstInvalid = sexMale;
                    } else {
                        sexMale.classList.remove('is-invalid');
                        sexFemale.classList.remove('is-invalid');
                    }

                    // Graded/Non-Graded
                    var graded = document.getElementById('gradedCheck');
                    var nonGraded = document.getElementById('nonGradedCheck');
                    if (graded && nonGraded && !graded.checked && !nonGraded.checked) {
                        graded.classList.add('is-invalid');
                        nonGraded.classList.add('is-invalid');
                        valid = false;
                        if (!firstInvalid) firstInvalid = graded;
                    } else {
                        graded.classList.remove('is-invalid');
                        nonGraded.classList.remove('is-invalid');
                    }
                    // If graded is checked, gradeLevelInput must be filled and between 1 and 12
                    var gradeLevelInput = document.getElementById('gradeLevelInput');
                    var gradeGroup = document.getElementById('gradeLevelGroup');
                    if (graded && graded.checked && gradeLevelInput && gradeGroup && !gradeGroup.classList.contains('d-none')) {
                        var gradeVal = gradeLevelInput.value.trim();
                        var gradeNum = parseInt(gradeVal, 10);
                        if (!gradeVal || isNaN(gradeNum) || gradeNum < 1 || gradeNum > 12) {
                            gradeLevelInput.classList.add('is-invalid');
                            valid = false;
                            if (!firstInvalid) firstInvalid = gradeLevelInput;
                        } else {
                            gradeLevelInput.classList.remove('is-invalid');
                        }
                    } else if (gradeLevelInput) {
                        gradeLevelInput.classList.remove('is-invalid');
                    }

                    // IP
                    var ipYes = document.getElementById('ipYes');
                    var ipNo = document.getElementById('ipNo');
                    if (ipYes && ipNo && !ipYes.checked && !ipNo.checked) {
                        ipYes.classList.add('is-invalid');
                        ipNo.classList.add('is-invalid');
                        valid = false;
                        if (!firstInvalid) firstInvalid = ipYes;
                    } else {
                        ipYes.classList.remove('is-invalid');
                        ipNo.classList.remove('is-invalid');
                    }
                    // If IP Yes is checked, ipSpecifyInput must be filled
                    var ipSpecifyInput = document.getElementById('ipSpecifyInput');
                    var ipGroup = document.getElementById('ipSpecifyGroup');
                    if (ipYes && ipYes.checked && ipSpecifyInput && ipGroup && !ipGroup.classList.contains('d-none') && !ipSpecifyInput.value.trim()) {
                        ipSpecifyInput.classList.add('is-invalid');
                        valid = false;
                        if (!firstInvalid) firstInvalid = ipSpecifyInput;
                    } else if (ipSpecifyInput) {
                        ipSpecifyInput.classList.remove('is-invalid');
                    }

                    // 4Ps
                    var fourPsYes = document.getElementById('fourPsYes');
                    var fourPsNo = document.getElementById('fourPsNo');
                    if (fourPsYes && fourPsNo && !fourPsYes.checked && !fourPsNo.checked) {
                        fourPsYes.classList.add('is-invalid');
                        fourPsNo.classList.add('is-invalid');
                        valid = false;
                        if (!firstInvalid) firstInvalid = fourPsYes;
                    } else {
                        fourPsYes.classList.remove('is-invalid');
                        fourPsNo.classList.remove('is-invalid');
                    }
                    // If 4Ps Yes is checked, fourPsIdInput must be filled
                    var fourPsIdInput = document.getElementById('fourPsIdInput');
                    var fourPsIdGroup = document.getElementById('fourPsIdGroup');
                    if (fourPsYes && fourPsYes.checked && fourPsIdInput && fourPsIdGroup && !fourPsIdGroup.classList.contains('d-none') && !fourPsIdInput.value.trim()) {
                        fourPsIdInput.classList.add('is-invalid');
                        valid = false;
                        if (!firstInvalid) firstInvalid = fourPsIdInput;
                    } else if (fourPsIdInput) {
                        fourPsIdInput.classList.remove('is-invalid');
                    }

                    // Permanent address fields if "Same with Current Address" is NOT checked
                    var sameAddr = document.getElementById('sameAsCurrent');
                    var permFields = document.getElementById('permAddressFields');
                    if (sameAddr && !sameAddr.checked && permFields) {
                        var permInputs = permFields.querySelectorAll('input[type="text"]');
                        permInputs.forEach(function(input) {
                            if (input.hasAttribute('required') && !input.value.trim()) {
                                input.classList.add('is-invalid');
                                valid = false;
                                if (!firstInvalid) firstInvalid = input;
                            } else {
                                input.classList.remove('is-invalid');
                            }
                        });
                    }

                    if (!valid) {
                        e.preventDefault();
                        alert('Please fill out all required fields and questions before proceeding.');
                        if (firstInvalid) firstInvalid.focus();
                        return false;
                    }
                    // If valid, proceed to step 2
                    var step2 = document.getElementById('step2Container');
                    step1.classList.add('d-none');
                    step2.classList.remove('d-none');
                    window.scrollTo({top:0, behavior:'smooth'});
                });
            }
            var backBtn = document.getElementById('backToStep1');
            if (backBtn) backBtn.addEventListener('click', function(){
                var step1 = document.getElementById('step1Container');
                var step2 = document.getElementById('step2Container');
                if (step1 && step2){
                    step2.classList.add('d-none');
                    step1.classList.remove('d-none');
                    window.scrollTo({top:0, behavior:'smooth'});
                }
            });

            var graded = document.getElementById('gradedCheck');
            var nonGraded = document.getElementById('nonGradedCheck');
            var gradeGroup = document.getElementById('gradeLevelGroup');

            function updateGradedState() {
                if (graded.checked) {
                    nonGraded.checked = false;
                    gradeGroup.classList.remove('d-none');
                } else {
                    gradeGroup.classList.add('d-none');
                }
                if (nonGraded.checked) {
                    graded.checked = false;
                    gradeGroup.classList.add('d-none');
                }
            }
            if (graded && nonGraded) {
                graded.addEventListener('change', updateGradedState);
                nonGraded.addEventListener('change', updateGradedState);
                updateGradedState();
            }

            var ipYes = document.getElementById('ipYes');
            var ipNo = document.getElementById('ipNo');
            var ipGroup = document.getElementById('ipSpecifyGroup');
            function updateIpState(){
                if (ipYes.checked) {
                    ipNo.checked = false;
                    ipGroup.classList.remove('d-none');
                } else if (ipNo.checked) {
                    ipYes.checked = false;
                    ipGroup.classList.add('d-none');
                } else {
                    ipGroup.classList.add('d-none');
                }
            }
            if (ipYes && ipNo) {
                ipYes.addEventListener('change', updateIpState);
                ipNo.addEventListener('change', updateIpState);
                updateIpState();
            }

            var fourYes = document.getElementById('fourPsYes');
            var fourNo = document.getElementById('fourPsNo');
            var fourGroup = document.getElementById('fourPsIdGroup');
            function updateFourPs(){
                if (fourYes.checked) {
                    fourNo.checked = false;
                    fourGroup.classList.remove('d-none');
                } else if (fourNo.checked) {
                    fourYes.checked = false;
                    fourGroup.classList.add('d-none');
                } else {
                    fourGroup.classList.add('d-none');
                }
            }
            if (fourYes && fourNo) {
                fourYes.addEventListener('change', updateFourPs);
                fourNo.addEventListener('change', updateFourPs);
                updateFourPs();
            }

            var sameAddr = document.getElementById('sameAsCurrent');
            var permFields = document.getElementById('permAddressFields');
            // Copy current address values into permanent address when checkbox is checked,
            // and make permanent inputs readonly while synced.
            function copyCurrentToPerm() {
                var map = [
                    ['curr_house_no','perm_house_no'],
                    ['curr_street','perm_street'],
                    ['curr_barangay','perm_barangay'],
                    ['curr_city','perm_city'],
                    ['curr_province','perm_province'],
                    ['curr_country','perm_country'],
                    ['curr_zip','perm_zip']
                ];
                map.forEach(function(pair){
                    var curr = document.getElementsByName(pair[0])[0];
                    var perm = document.getElementsByName(pair[1])[0];
                    if (curr && perm) perm.value = curr.value;
                });
            }

            function setPermReadonly(readonly) {
                if (!permFields) return;
                var permInputs = permFields.querySelectorAll('input');
                permInputs.forEach(function(inp){
                    if (readonly) inp.setAttribute('readonly','readonly');
                    else inp.removeAttribute('readonly');
                });
            }

            function updatePerm(){
                if (!sameAddr) return;
                if (sameAddr.checked) {
                    // Copy values and lock permanent inputs
                    copyCurrentToPerm();
                    setPermReadonly(true);
                } else {
                    // Unlock permanent inputs so user can edit independently
                    setPermReadonly(false);
                }
            }

            if (sameAddr && permFields) {
                sameAddr.addEventListener('change', updatePerm);
                // Keep permanent address synced while checkbox is checked
                var currentNames = ['curr_house_no','curr_street','curr_barangay','curr_city','curr_province','curr_country','curr_zip'];
                currentNames.forEach(function(name){
                    var inp = document.getElementsByName(name)[0];
                    if (inp) {
                        inp.addEventListener('input', function(){
                            if (sameAddr.checked) copyCurrentToPerm();
                        });
                    }
                });
                updatePerm();
            }

            // Step 2 dynamic logic
            function makeBinaryToggle(yesEl, noEl, onYes, onNo){
                if (!yesEl || !noEl) return;
                function sync(){
                    if (yesEl.checked) { noEl.checked = false; if (onYes) onYes(); }
                    else if (noEl.checked) { yesEl.checked = false; if (onNo) onNo(); }
                    else { if (onNo) onNo(); }
                }
                yesEl.addEventListener('change', sync);
                noEl.addEventListener('change', sync);
                sync();
            }

            // 5-6. SNEd and Disabilities
            var snedYes = document.getElementById('snedYes');
            var snedNo = document.getElementById('snedNo');
            var snedGroup = document.getElementById('snedDisabilityGroup');
            var disOther = document.getElementById('disOther');
            var disOtherSpecify = document.getElementById('disOtherSpecify');
            makeBinaryToggle(snedYes, snedNo, function(){ if(snedGroup) snedGroup.classList.remove('d-none'); }, function(){ if(snedGroup) snedGroup.classList.add('d-none'); });
            if (disOther && disOtherSpecify) {
                function syncDisOther(){ disOtherSpecify.classList.toggle('d-none', !disOther.checked); }
                disOther.addEventListener('change', syncDisOther);
                syncDisOther();
            }

            // SHS Semester
            var sem1 = document.getElementById('sem1');
            var sem2 = document.getElementById('sem2');
            var shsNa = document.getElementById('shsNa');
            var shsSemesterHidden = document.getElementById('shsSemesterHidden');
            var shsTrack = document.getElementById('shsTrack');
            var shsStrand = document.getElementById('shsStrand');
            if (sem1 && sem2) {
                function syncSem(){ if (sem1.checked) sem2.checked = false; if (sem2.checked) sem1.checked = false; }
                sem1.addEventListener('change', syncSem);
                sem2.addEventListener('change', syncSem);
                syncSem();
            }

            function updateShsNaState(){
                if (!shsNa) return;
                var na = shsNa.checked;
                // For select elements we disable; for text inputs we use readonly (backwards-compatible)
                if (na) {
                    if (shsTrack) {
                        if (shsTrack.tagName === 'SELECT') {
                            shsTrack.value = 'N/A';
                            shsTrack.disabled = true;
                        } else {
                            shsTrack.value = 'N/A';
                            shsTrack.setAttribute('readonly', 'readonly');
                        }
                    }
                    if (shsStrand) {
                        if (shsStrand.tagName === 'SELECT') {
                            shsStrand.value = 'N/A';
                            shsStrand.disabled = true;
                        } else {
                            shsStrand.value = 'N/A';
                            shsStrand.setAttribute('readonly', 'readonly');
                        }
                    }
                    if (sem1) { sem1.checked = false; sem1.disabled = true; }
                    if (sem2) { sem2.checked = false; sem2.disabled = true; }
                    if (shsSemesterHidden) { shsSemesterHidden.value = 'N/A'; }
                } else {
                    if (shsTrack) {
                        if (shsTrack.tagName === 'SELECT') {
                            if (shsTrack.value === 'N/A') shsTrack.value = '';
                            shsTrack.disabled = false;
                        } else {
                            if (shsTrack.value === 'N/A') shsTrack.value = '';
                            shsTrack.removeAttribute('readonly');
                        }
                    }
                    if (shsStrand) {
                        if (shsStrand.tagName === 'SELECT') {
                            if (shsStrand.value === 'N/A') shsStrand.value = '';
                            shsStrand.disabled = false;
                        } else {
                            if (shsStrand.value === 'N/A') shsStrand.value = '';
                            shsStrand.removeAttribute('readonly');
                        }
                    }
                    if (sem1) { sem1.disabled = false; }
                    if (sem2) { sem2.disabled = false; }
                    if (shsSemesterHidden) { shsSemesterHidden.value = ''; }
                }
            }
            if (shsNa) {
                shsNa.addEventListener('change', updateShsNaState);
                updateShsNaState();
            }

            // Keep hidden semester value in sync when NA is not selected
            function syncSemesterHidden(){
                if (!shsSemesterHidden) return;
                if (shsNa && shsNa.checked) return; // NA overrides
                if (sem1 && sem1.checked) { shsSemesterHidden.value = '1st'; return; }
                if (sem2 && sem2.checked) { shsSemesterHidden.value = '2nd'; return; }
                shsSemesterHidden.value = '';
            }
            if (sem1) sem1.addEventListener('change', syncSemesterHidden);
            if (sem2) sem2.addEventListener('change', syncSemesterHidden);
            syncSemesterHidden();

            // Step 2 validation: only validate when submit button is clicked
            function validateStep2() {
                var step2 = document.getElementById('step2Container');
                if (!step2 || step2.classList.contains('d-none')) return true;

                var firstInvalid = null;
                var valid = true;

                // Clear all previous validation states
                var allFields = step2.querySelectorAll('input, select, textarea');
                allFields.forEach(function(f) {
                    f.classList.remove('is-invalid');
                });

                // Consider text/number/date/hidden/select/textarea inputs (ignore checkboxes/radios)
                var fields = step2.querySelectorAll('input[type="text"], input[type="number"], input[type="tel"], input[type="date"], select, textarea');
                fields.forEach(function(f) {
                    // only validate visible fields
                    if (f.offsetParent === null) {
                        return;
                    }
                    var val = (f.value || '').toString().trim();
                    var isValid = val !== '';
                    if (!isValid) {
                        f.classList.add('is-invalid');
                        valid = false;
                        if (!firstInvalid) firstInvalid = f;
                    }
                });

                // Extra validation: phone numbers must be 11 digits and start with 09
                var phoneNames = ['father_contact', 'mother_contact', 'guardian_contact'];
                phoneNames.forEach(function(name) {
                    var phoneInput = step2.querySelector('input[name="' + name + '"]');
                    if (!phoneInput || phoneInput.offsetParent === null) return;
                    var val = (phoneInput.value || '').toString().trim();
                    if (!/^09\d{9}$/.test(val)) {
                        phoneInput.classList.add('is-invalid');
                        valid = false;
                        if (!firstInvalid) firstInvalid = phoneInput;
                    } else {
                        phoneInput.classList.remove('is-invalid');
                    }
                });

                if (!valid && firstInvalid) firstInvalid.focus();
                return valid;
            }

            // Attach form submission validation
            (function attachFormValidation(){
                var form = document.getElementById('enrollmentForm');
                if (!form) return;

                // Prevent form submission if validation fails
                form.addEventListener('submit', function(e){
                    // If step2 is not visible, prevent submission (user must go to step2 first)
                    var step2El = document.getElementById('step2Container');
                    if (!step2El || step2El.classList.contains('d-none')) {
                        e.preventDefault();
                        alert('Please complete Step 1 and click Next to proceed to Step 2 before submitting.');
                        return;
                    }
                    if (!validateStep2()) {
                        e.preventDefault();
                        alert('Please fill out all required fields in Step 2 before submitting.');
                        return;
                    }
                });
            })();

             // The Social Background related JS handlers were removed
             // (sbFourPs, sbIp, diffOther, tat/preg/gang/broken/live, work/alco/rent/transPrivate,
             //  transferee/harass/abuse handlers removed)
         })();
         
         function toggleSexCheckbox(clicked) {
             var male = document.getElementById('sexMale');
             var female = document.getElementById('sexFemale');
             if (clicked === male) {
                 female.checked = false;
             } else {
                 male.checked = false;
             }
         }
    </script>
    <style>
        /* existing styles unchanged */
    </style>
</body>
</html>

