<?php
session_start();
require_once 'db_connection.php';

if (!isset($_GET['studentacc_id'])) {
    if (isset($_SESSION['studentacc_id'])) {
        $studentacc_id = $_SESSION['studentacc_id'];
    } else {
        die('Invalid access');
    }
} else {
    $studentacc_id = $_GET['studentacc_id'];
}

// Fetch student data from all tables
$sql_studentaccount = "SELECT * FROM studentaccount WHERE studentacc_id = ?";
$stmt = $pdo->prepare($sql_studentaccount);
$stmt->execute([$studentacc_id]);
$studentaccount = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$studentaccount) {
    die('Student not found');
}

// Fetch related data only if enrolled
if ($studentaccount['enrollmentstatus'] === 'enrolled') {
    $lpi = $pdo->query("SELECT * FROM lpi WHERE studentacc_id = " . $studentacc_id)->fetch(PDO::FETCH_ASSOC);
    $current_address = $pdo->query("SELECT * FROM current_address WHERE studentacc_id = " . $studentacc_id)->fetch(PDO::FETCH_ASSOC);
    $permanent_address = $pdo->query("SELECT * FROM permanent_address WHERE studentacc_id = " . $studentacc_id)->fetch(PDO::FETCH_ASSOC);
    $parent_info = $pdo->query("SELECT * FROM parent_info WHERE studentacc_id = " . $studentacc_id)->fetch(PDO::FETCH_ASSOC);
    $lshs = $pdo->query("SELECT * FROM lshs WHERE studentacc_id = " . $studentacc_id)->fetch(PDO::FETCH_ASSOC);
    $sne = $pdo->query("SELECT * FROM sne WHERE studentacc_id = " . $studentacc_id)->fetch(PDO::FETCH_ASSOC);
    $transferee = $pdo->query("SELECT * FROM transferee WHERE studentacc_id = " . $studentacc_id)->fetch(PDO::FETCH_ASSOC);
    $learning_modalities = $pdo->query("SELECT * FROM learning_modalities WHERE studentacc_id = " . $studentacc_id)->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profile</title>
    <link rel="icon" href="adminicons/logomnhs.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <style>
        /* Page scrolling */
        html, body {
            height: 100%;
            background-color: #f8f9fa;
            font-family: 'Poppins', sans-serif;
        }

        * { font-family: 'Poppins', sans-serif !important; }
        .sidebar-link { text-decoration: none; }
        .form-section-title { font-weight: 600; margin-top: 1.5rem; }
        .muted-note { font-size: .95rem; white-space: pre-line; }

        /* Make content area scrollable */
        #content {
            overflow-y: auto;
            height: 100vh;
        }
    </style>
</head>
<body>
    <div class="d-flex">
       <?php include 'studentsidebar.php'; ?>
        <div id="content" class="flex-grow-1 p-4">
            <h2>Student Profile</h2>
            <div class="container">
                <?php if ($studentaccount['enrollmentstatus'] === 'pending'): ?>
                    <div class="d-flex align-items-center justify-content-center" style="min-height: 400px;">
                        <div class="text-center">
                            <h4 class="mb-3">Not yet enrolled</h4>
                            <p class="text-muted">Please wait for the administrator for the approval.</p>
                        </div>
                    </div>
                <?php else: ?>
                <!-- Profile Picture with Account Information -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <h5>Profile Picture</h5>
                        <?php if ($lpi && !empty($lpi['profile_picture'])): ?>
                            <div class="text-center">
                                <img src="<?php echo htmlspecialchars($lpi['profile_picture']); ?>" alt="Profile Picture" class="img-thumbnail" style="max-width: 250px; max-height: 250px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                            </div>
                        <?php else: ?>
                            <div class="text-center">
                                <div class="img-thumbnail d-flex align-items-center justify-content-center" style="width: 250px; height: 250px; background-color: #f8f9fa; border: 2px solid #dee2e6; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                                    <span class="text-muted fs-5">No Image</span>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Account Information beside profile picture -->
                    <div class="col-md-8">
                        <h5>Account Information</h5>
                        <div class="card" style="border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                <i class="bi bi-person-circle text-primary" style="font-size: 1.5rem;"></i>
                                            </div>
                                            <div>
                                                <strong class="text-muted small">Username</strong><br>
                                                <span class="fs-6"><?php echo htmlspecialchars($studentaccount['studentacc_username'] ?? 'N/A'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                <i class="bi bi-envelope text-primary" style="font-size: 1.5rem;"></i>
                                            </div>
                                            <div>
                                                <strong class="text-muted small">Email</strong><br>
                                                <span class="fs-6"><?php echo htmlspecialchars($studentaccount['studentacc_email'] ?? 'N/A'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                <i class="bi bi-key text-primary" style="font-size: 1.5rem;"></i>
                                            </div>
                                            <div>
                                                <strong class="text-muted small">Password</strong><br>
                                                <span class="fs-6"><?php echo htmlspecialchars($studentaccount['studentacc_password'] ?? 'N/A'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                <i class="bi bi-check-circle text-primary" style="font-size: 1.5rem;"></i>
                                            </div>
                                            <div>
                                                <strong class="text-muted small">Enrollment Status</strong><br>
                                                <span class="fs-6">
                                                    <?php 
                                                    $status = $studentaccount['enrollmentstatus'] ?? 'N/A';
                                                    $badgeClass = $status === 'enrolled' ? 'badge bg-success' : ($status === 'pending' ? 'badge bg-warning' : 'badge bg-secondary');
                                                    echo '<span class="' . $badgeClass . '">' . htmlspecialchars($status) . '</span>';
                                                    ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>

                <!-- LPI -->
                <?php if ($lpi): ?>
                <h5>Learner Personal Information</h5>
                <div class="row">
                    <div class="col-md-6"><strong>School Year:</strong> <?php echo htmlspecialchars($lpi['school_year'] ?? 'N/A'); ?></div>
                    <div class="col-md-6"><strong>LRN:</strong> <?php echo htmlspecialchars($lpi['lrn'] ?? 'N/A'); ?></div>
                    <div class="col-md-6"><strong>Grade Level to Enroll:</strong> <?php echo htmlspecialchars($lpi['Gradelvl_enroll'] ?? 'N/A'); ?></div>
                    <div class="col-md-6"><strong>PWD:</strong> <?php echo htmlspecialchars($lpi['pwd'] ?? 'N/A'); ?></div>
                    <div class="col-md-6"><strong>PSA No.:</strong> <?php echo htmlspecialchars($lpi['psa_no'] ?? 'N/A'); ?></div>
                    <?php if (!empty($lpi['psa_picture'])): ?>
                    <div class="col-md-6">
                        <strong>PSA Birth Certificate:</strong><br>
                        <button type="button" class="btn btn-outline-primary btn-sm mt-1" onclick="togglePSAPicture()">View PSA Picture</button>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($lpi['card_pic'])): ?>
                    <div class="col-md-6">
                        <strong>Card:</strong><br>
                        <button type="button" class="btn btn-outline-primary btn-sm mt-1" onclick="toggleCardPicture()">View Card Picture</button>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($lpi['good_moral'])): ?>
                    <div class="col-md-6">
                        <strong>Good Moral Certificate:</strong><br>
                        <button type="button" class="btn btn-outline-primary btn-sm mt-1" onclick="toggleGoodMoralPicture()">View Good Moral Picture</button>
                    </div>
                    <?php endif; ?>
                    <div class="col-md-6"><strong>Birthdate:</strong> <?php echo htmlspecialchars($lpi['birthdate'] ?? 'N/A'); ?></div>
                    <div class="col-md-6"><strong>Age:</strong> <?php echo htmlspecialchars($lpi['age'] ?? 'N/A'); ?></div>
                    <div class="col-md-6"><strong>Sex:</strong> <?php echo htmlspecialchars($lpi['sex'] ?? 'N/A'); ?></div>
                    <div class="col-md-6"><strong>Place of Birth:</strong> <?php echo htmlspecialchars($lpi['pob'] ?? 'N/A'); ?></div>
                    <div class="col-md-6"><strong>Religion:</strong> <?php echo htmlspecialchars($lpi['religion'] ?? 'N/A'); ?></div>
                    <div class="col-md-6"><strong>First Name:</strong> <?php echo htmlspecialchars($lpi['fname'] ?? 'N/A'); ?></div>
                    <div class="col-md-6"><strong>Middle Name:</strong> <?php echo htmlspecialchars($lpi['mname'] ?? 'N/A'); ?></div>
                    <div class="col-md-6"><strong>Last Name:</strong> <?php echo htmlspecialchars($lpi['lname'] ?? 'N/A'); ?></div>
                    <div class="col-md-6"><strong>Extension Name:</strong> <?php echo htmlspecialchars($lpi['extname'] ?? 'N/A'); ?></div>
                    <div class="col-md-6"><strong>Mother Tongue:</strong> <?php echo htmlspecialchars($lpi['mother_tongue'] ?? 'N/A'); ?></div>
                    <div class="col-md-6"><strong>IP:</strong> <?php echo htmlspecialchars($lpi['ip'] ?? 'N/A'); ?></div>
                    <div class="col-md-6"><strong>4Ps:</strong> <?php echo htmlspecialchars($lpi['4ps'] ?? 'N/A'); ?></div>
                </div>
                <hr>
                <?php endif; ?>

                <!-- PSA Picture Container -->
                <?php if ($lpi && !empty($lpi['psa_picture'])): ?>
                <div id="psaPictureContainer" class="mt-3 d-none">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">PSA Birth Certificate</h6>
                            <button type="button" class="btn-close" onclick="togglePSAPicture()" aria-label="Close"></button>
                        </div>
                        <div class="card-body text-center">
                            <img src="<?php echo htmlspecialchars($lpi['psa_picture']); ?>" alt="PSA Birth Certificate" class="img-fluid" style="max-height: 60vh; max-width: 100%;">
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Card Picture Container -->
                <?php if ($lpi && !empty($lpi['card_pic'])): ?>
                <div id="cardPictureContainer" class="mt-3 d-none">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Card</h6>
                            <button type="button" class="btn-close" onclick="toggleCardPicture()" aria-label="Close"></button>
                        </div>
                        <div class="card-body text-center">
                            <img src="<?php echo htmlspecialchars($lpi['card_pic']); ?>" alt="Card" class="img-fluid" style="max-height: 60vh; max-width: 100%;">
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Good Moral Picture Container -->
                <?php if ($lpi && !empty($lpi['good_moral'])): ?>
                <div id="goodMoralPictureContainer" class="mt-3 d-none">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Good Moral Certificate</h6>
                            <button type="button" class="btn-close" onclick="toggleGoodMoralPicture()" aria-label="Close"></button>
                        </div>
                        <div class="card-body text-center">
                            <img src="<?php echo htmlspecialchars($lpi['good_moral']); ?>" alt="Good Moral Certificate" class="img-fluid" style="max-height: 60vh; max-width: 100%;">
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Current Address -->
                <?php if ($current_address): ?>
                <h5>Current Address</h5>
                <div class="row">
                    <div class="col-md-6"><strong>House No.:</strong> <?php echo htmlspecialchars($current_address['currenthouse_no'] ?? 'N/A'); ?></div>
                    <div class="col-md-6"><strong>Street Name:</strong> <?php echo htmlspecialchars($current_address['current_streetname'] ?? 'N/A'); ?></div>
                    <div class="col-md-6"><strong>Barangay:</strong> <?php echo htmlspecialchars($current_address['current_barangay'] ?? 'N/A'); ?></div>
                    <div class="col-md-6"><strong>Municipality:</strong> <?php echo htmlspecialchars($current_address['current_municipality'] ?? 'N/A'); ?></div>
                    <div class="col-md-6"><strong>Province:</strong> <?php echo htmlspecialchars($current_address['current_province'] ?? 'N/A'); ?></div>
                    <div class="col-md-6"><strong>Country:</strong> <?php echo htmlspecialchars($current_address['current_country'] ?? 'N/A'); ?></div>
                    <div class="col-md-6"><strong>Zip Code:</strong> <?php echo htmlspecialchars($current_address['current_zipcode'] ?? 'N/A'); ?></div>
                </div>
                <hr>
                <?php endif; ?>

                <!-- Permanent Address -->
                <?php if ($permanent_address): ?>
                <h5>Permanent Address</h5>
                <div class="row">
                    <div class="col-md-6"><strong>House No.:</strong> <?php echo htmlspecialchars($permanent_address['permhouse_no'] ?? 'N/A'); ?></div>
                    <div class="col-md-6"><strong>Street Name:</strong> <?php echo htmlspecialchars($permanent_address['perm_streetname'] ?? 'N/A'); ?></div>
                    <div class="col-md-6"><strong>Barangay:</strong> <?php echo htmlspecialchars($permanent_address['perm_barangay'] ?? 'N/A'); ?></div>
                    <div class="col-md-6"><strong>Municipality:</strong> <?php echo htmlspecialchars($permanent_address['perm_municipality'] ?? 'N/A'); ?></div>
                    <div class="col-md-6"><strong>Province:</strong> <?php echo htmlspecialchars($permanent_address['perm_province'] ?? 'N/A'); ?></div>
                    <div class="col-md-6"><strong>Country:</strong> <?php echo htmlspecialchars($permanent_address['perm_country'] ?? 'N/A'); ?></div>
                    <div class="col-md-6"><strong>Zip Code:</strong> <?php echo htmlspecialchars($permanent_address['perm_zipcode'] ?? 'N/A'); ?></div>
                </div>
                <hr>
                <?php endif; ?>

                <!-- Parent Info -->
                <?php if ($parent_info): ?>
                <h5>Parent Information</h5>
                <div class="row">
                    <div class="col-md-6"><strong>Father First Name:</strong> <?php echo htmlspecialchars($parent_info['father_fname'] ?? 'N/A'); ?></div>
                    <div class="col-md-6"><strong>Father Middle Name:</strong> <?php echo htmlspecialchars($parent_info['father_mname'] ?? 'N/A'); ?></div>
                    <div class="col-md-6"><strong>Father Last Name:</strong> <?php echo htmlspecialchars($parent_info['father_lname'] ?? 'N/A'); ?></div>
                    <div class="col-md-6"><strong>Mother First Name:</strong> <?php echo htmlspecialchars($parent_info['mother_fname'] ?? 'N/A'); ?></div>
                    <div class="col-md-6"><strong>Mother Middle Name:</strong> <?php echo htmlspecialchars($parent_info['mother_mname'] ?? 'N/A'); ?></div>
                    <div class="col-md-6"><strong>Mother Last Name:</strong> <?php echo htmlspecialchars($parent_info['mother_lname'] ?? 'N/A'); ?></div>
                    <div class="col-md-6"><strong>Guardian First Name:</strong> <?php echo htmlspecialchars($parent_info['guardian_fname'] ?? 'N/A'); ?></div>
                    <div class="col-md-6"><strong>Guardian Middle Name:</strong> <?php echo htmlspecialchars($parent_info['guardian_mname'] ?? 'N/A'); ?></div>
                    <div class="col-md-6"><strong>Guardian Last Name:</strong> <?php echo htmlspecialchars($parent_info['guardian_lname'] ?? 'N/A'); ?></div>
                    <div class="col-md-6"><strong>Father Contact No.:</strong> <?php echo htmlspecialchars($parent_info['fathercontact_no'] ?? 'N/A'); ?></div>
                    <div class="col-md-6"><strong>Mother Contact No.:</strong> <?php echo htmlspecialchars($parent_info['mothercontact_no'] ?? 'N/A'); ?></div>
                    <div class="col-md-6"><strong>Guardian Contact No.:</strong> <?php echo htmlspecialchars($parent_info['guardiancontact_no'] ?? 'N/A'); ?></div>
                </div>
                <hr>
                <?php endif; ?>

                <!-- LSHS -->
                <?php if ($lshs): ?>
                <h5>Senior High School</h5>
                <div class="row">
                    <div class="col-md-6"><strong>Semester:</strong> <?php echo htmlspecialchars($lshs['shs_semester'] ?? 'N/A'); ?></div>
                    <div class="col-md-6"><strong>Track:</strong> <?php echo htmlspecialchars($lshs['shs_track'] ?? 'N/A'); ?></div>
                    <div class="col-md-6"><strong>Strand:</strong> <?php echo htmlspecialchars($lshs['shs_strand'] ?? 'N/A'); ?></div>
                </div>
                <hr>
                <?php endif; ?>

                <!-- SNE -->
                <?php if ($sne): ?>
                <h5>Special Needs Education</h5>
                <div class="row">
                    <div class="col-md-6"><strong>Type of Disability:</strong> <?php echo htmlspecialchars($sne['type_disability'] ?? 'N/A'); ?></div>
                </div>
                <hr>
                <?php endif; ?>

                <!-- Transferee -->
                <?php if ($transferee): ?>
                <h5>Transferee Information</h5>
                <div class="row">
                    <div class="col-md-6"><strong>Last Grade Level:</strong> <?php echo htmlspecialchars($transferee['last_gradelevel'] ?? 'N/A'); ?></div>
                    <div class="col-md-6"><strong>Last School Year Completed:</strong> <?php echo htmlspecialchars($transferee['last_sycompleted'] ?? 'N/A'); ?></div>
                    <div class="col-md-6"><strong>Last School Attended:</strong> <?php echo htmlspecialchars($transferee['last_schoolattended'] ?? 'N/A'); ?></div>
                    <div class="col-md-6"><strong>Last School ID:</strong> <?php echo htmlspecialchars($transferee['last_schoolid'] ?? 'N/A'); ?></div>
                </div>
                <hr>
                <?php endif; ?>

                <!-- Learning Modalities -->
                <?php if ($learning_modalities): ?>
                <h5>Learning Modalities</h5>
                <div class="row">
                    <div class="col-md-6"><strong>Learning Modality:</strong> <?php echo htmlspecialchars($learning_modalities['learning_mod'] ?? 'N/A'); ?></div>
                </div>
                <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

<!-- load bootstrap JS (bundle includes Collapse) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var sidebar = document.getElementById('sidebar');
    var sidebarMenu = document.getElementById('sidebarMenu');

    // Ensure collapse events add/remove the collapsed class for styling
    if (sidebarMenu && sidebar) {
        sidebarMenu.addEventListener('shown.bs.collapse', function () {
            sidebar.classList.remove('sidebar-collapsed');
        });
        sidebarMenu.addEventListener('hidden.bs.collapse', function () {
            sidebar.classList.add('sidebar-collapsed');
            // Keep sidebar scrolled to top when collapsed (optional)
            sidebar.scrollTop = 0;
        });

        // Initialize collapsed state based on whether the menu is shown
        var bsInstance = bootstrap.Collapse.getOrCreateInstance(sidebarMenu);
        // If collapse is hidden by default, make sure class is present
        if (!sidebarMenu.classList.contains('show')) {
            sidebar.classList.add('sidebar-collapsed');
        }
    }

    // Expose a toggle function for any manual toggler buttons
    window.toggleSidebar = function () {
        if (!sidebar) return;
        sidebar.classList.toggle('sidebar-collapsed');

        // Keep the collapse element in sync (for small screens)
        if (sidebarMenu) {
            var inst = bootstrap.Collapse.getOrCreateInstance(sidebarMenu);
            if (sidebar.classList.contains('sidebar-collapsed')) {
                inst.hide();
            } else {
                inst.show();
            }
        }
    };
});

// Function to toggle PSA picture visibility
function togglePSAPicture() {
    const container = document.getElementById('psaPictureContainer');
    if (!container) return;

    if (container.classList.contains('d-none')) {
        // Show the container
        container.classList.remove('d-none');
        // Update button text
        const button = document.querySelector('button[onclick="togglePSAPicture()"]');
        if (button) {
            button.textContent = 'Hide PSA Picture';
        }
    } else {
        // Hide the container
        container.classList.add('d-none');
        // Update button text
        const button = document.querySelector('button[onclick="togglePSAPicture()"]');
        if (button) {
            button.textContent = 'View PSA Picture';
        }
    }
}

// Function to toggle Card picture visibility
function toggleCardPicture() {
    const container = document.getElementById('cardPictureContainer');
    if (!container) return;

    if (container.classList.contains('d-none')) {
        // Show the container
        container.classList.remove('d-none');
        // Update button text
        const button = document.querySelector('button[onclick="toggleCardPicture()"]');
        if (button) {
            button.textContent = 'Hide Card Picture';
        }
    } else {
        // Hide the container
        container.classList.add('d-none');
        // Update button text
        const button = document.querySelector('button[onclick="toggleCardPicture()"]');
        if (button) {
            button.textContent = 'View Card Picture';
        }
    }
}

// Function to toggle Good Moral picture visibility
function toggleGoodMoralPicture() {
    const container = document.getElementById('goodMoralPictureContainer');
    if (!container) return;

    if (container.classList.contains('d-none')) {
        // Show the container
        container.classList.remove('d-none');
        // Update button text
        const button = document.querySelector('button[onclick="toggleGoodMoralPicture()"]');
        if (button) {
            button.textContent = 'Hide Good Moral Picture';
        }
    } else {
        // Hide the container
        container.classList.add('d-none');
        // Update button text
        const button = document.querySelector('button[onclick="toggleGoodMoralPicture()"]');
        if (button) {
            button.textContent = 'View Good Moral Picture';
        }
    }
}
</script>
</html>