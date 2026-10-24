<?php
require_once 'db_connection.php';

// Get search parameter
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

// Build query to fetch enrolled students
$sql = "SELECT lpi.fname, lpi.lname, lpi.gradelvl_enroll AS Gradelvl_enroll, studentaccount.studentacc_id AS id
        FROM studentaccount
        INNER JOIN lpi ON studentaccount.studentacc_id = lpi.studentacc_id
        WHERE studentaccount.enrollmentstatus = 'enrolled'";

// Add search condition if provided
if (!empty($search)) {
    $sql .= " AND lpi.lname LIKE :search";
}

// Get total count for pagination
$count_sql = "SELECT COUNT(*) as total
              FROM studentaccount
              INNER JOIN lpi ON studentaccount.studentacc_id = lpi.studentacc_id
              WHERE studentaccount.enrollmentstatus = 'enrolled'";
if (!empty($search)) {
    $count_sql .= " AND lpi.lname LIKE :search";
}

$count_stmt = $pdo->prepare($count_sql);
if (!empty($search)) {
    $count_stmt->execute([':search' => '%' . $search . '%']);
} else {
    $count_stmt->execute();
}
$total_records = $count_stmt->fetch(PDO::FETCH_ASSOC)['total'];
$total_pages = ceil($total_records / $per_page);

// Add pagination and ordering
$sql .= " ORDER BY lpi.lname ASC LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($sql);
if (!empty($search)) {
    $stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
}
$stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$enrolled_students = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enrolled Students</title>
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
            background-color: #e7e7e7ff;
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
       <?php include 'sidebar_admin.php'; ?>
        <div id="content" class="flex-grow-1 p-4">
            <h1 class="mt-5 m-2" style="font-weight: bold; color: #0a397eff;">Enrolled Students</h1>
            <hr class="mb-5 mt-5 m-2">
            
            <!-- Search Bar -->
            <div class="mb-3">
                <div class="d-flex gap-2">
                    <input type="text" id="searchInput" class="form-control" placeholder="Search by last name..." value="<?php echo htmlspecialchars($search); ?>" style="max-width: 300px;" autocomplete="off">
                    <?php if (!empty($search)): ?>
                        <a href="enrolledstudents.php" class="btn btn-secondary"><i class="bi bi-x-circle"></i> Clear</a>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="table-responsive mb-5">
                <table class="table table-striped table-bordered">
                    <thead class="table-primary">
                        <tr>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Grade Level</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($enrolled_students)): ?>
                        <tr>
                            <td colspan="4" class="text-center">No enrolled students found.</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($enrolled_students as $student): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($student['fname']); ?></td>
                            <td><?php echo htmlspecialchars($student['lname']); ?></td>
                            <td><?php echo htmlspecialchars($student['Gradelvl_enroll'] ?? 'N/A'); ?></td>
                            <td>
                                <button class="btn btn-primary btn-sm" onclick="viewForm(<?php echo $student['id']; ?>)"><i class="bi bi-arrows-angle-expand"></i></button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <small class="text-muted">Showing <?php echo count($enrolled_students); ?> of <?php echo $total_records; ?> enrolled students</small>
                                    </div>
                                    <nav aria-label="Page navigation">
                                        <ul class="pagination justify-content-end mb-0">
                                            <?php if ($page > 1): ?>
                                                <li class="page-item">
                                                    <a class="page-link" href="?page=<?php echo $page - 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>"><i class="bi bi-arrow-left-circle-fill"></i></a>
                                                </li>
                                            <?php else: ?>
                                                <li class="page-item disabled">
                                                    <span class="page-link"><i class="bi bi-arrow-left-circle-fill"></i></span>
                                                </li>
                                            <?php endif; ?>
                                            
                                            <?php
                                            $start_page = max(1, $page - 2);
                                            $end_page = min($total_pages, $page + 2);
                                            
                                            if ($start_page > 1): ?>
                                                <li class="page-item">
                                                    <a class="page-link" href="?page=1<?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>">1</a>
                                                </li>
                                                <?php if ($start_page > 2): ?>
                                                    <li class="page-item disabled">
                                                        <span class="page-link">...</span>
                                                    </li>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                            
                                            <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                                                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                                    <a class="page-link" href="?page=<?php echo $i; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>"><?php echo $i; ?></a>
                                                </li>
                                            <?php endfor; ?>
                                            
                                            <?php if ($end_page < $total_pages): ?>
                                                <?php if ($end_page < $total_pages - 1): ?>
                                                    <li class="page-item disabled">
                                                        <span class="page-link">...</span>
                                                    </li>
                                                <?php endif; ?>
                                                <li class="page-item">
                                                    <a class="page-link" href="?page=<?php echo $total_pages; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>"><?php echo $total_pages; ?></a>
                                                </li>
                                            <?php endif; ?>
                                            
                                            <?php if ($page < $total_pages): ?>
                                                <li class="page-item">
                                                    <a class="page-link" href="?page=<?php echo $page + 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>"><i class="bi bi-arrow-right-circle-fill"></i></a>
                                                </li>
                                            <?php else: ?>
                                                <li class="page-item disabled">
                                                    <span class="page-link"><i class="bi bi-arrow-right-circle-fill"></i></span>
                                                </li>
                                            <?php endif; ?>
                                        </ul>
                                    </nav>
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
     </div>

<!-- Modal -->
<div class="modal fade" id="studentModal" tabindex="-1" aria-labelledby="studentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="studentModalLabel">Student Information</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalBody">
                <!-- Content will be populated here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- load bootstrap JS (bundle includes Collapse) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    console.log('DOM loaded, Bootstrap available:', typeof bootstrap !== 'undefined');
    console.log('Modal class available:', typeof bootstrap.Modal !== 'undefined');
    
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

    // Live search functionality
    var searchInput = document.getElementById('searchInput');
    var searchTimeout;
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            var searchValue = this.value.trim();
            
            // Clear previous timeout
            clearTimeout(searchTimeout);
            
            // Set a new timeout to search after user stops typing (500ms delay)
            searchTimeout = setTimeout(function() {
                var url = new URL(window.location.href);
                url.searchParams.delete('page'); // Reset to page 1 when searching
                
                if (searchValue) {
                    url.searchParams.set('search', searchValue);
                } else {
                    url.searchParams.delete('search');
                }
                
                // Navigate to the new URL
                window.location.href = url.toString();
            }, 500); // Wait 500ms after user stops typing
        });
    }
});

// Function to view form (placeholder - redirect to view page or open modal)
function viewForm(id) {
    console.log('viewForm called with id:', id);
    // Store the current studentacc_id
    window.currentStudentAccId = id;
    // Fetch student data via AJAX
    fetch('fetch_student_data.php?studentacc_id=' + id)
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log('Data received:', data);
            if (data.error) {
                alert('Error: ' + data.error);
                return;
            }
            // Populate modal with data
            populateModal(data);
            // Show modal
            const modalElement = document.getElementById('studentModal');
            console.log('Modal element found:', modalElement);
            if (!modalElement) {
                alert('Modal element not found!');
                return;
            }
            let modal = bootstrap.Modal.getInstance(modalElement);
            if (!modal) {
                console.log('Creating new modal instance');
                modal = new bootstrap.Modal(modalElement);
            }
            console.log('Showing modal');
            modal.show();
        })
        .catch(error => {
            console.error('Error in viewForm:', error);
            alert('Error loading student data: ' + error.message);
        });
}

// Function to populate modal with student data
function populateModal(data) {
    console.log('populateModal called with data:', data);
    console.log('LPI data:', data.lpi);
    console.log('Card pic:', data.lpi ? data.lpi.card_pic : 'N/A');
    console.log('Good moral:', data.lpi ? data.lpi.good_moral : 'N/A');
    const modalBody = document.getElementById('modalBody');
    console.log('Modal body element:', modalBody);
    if (!modalBody) {
        console.error('Modal body not found!');
        return;
    }
    let content = '';

    // LPI
    if (data.lpi) {
        content += '<h5>Learner Personal Information</h5>';
        
        // Profile Picture with Account Info beside it
        content += '<div class="row mb-3">';
        content += '<div class="col-md-4">';
        content += '<strong>Profile Picture:</strong><br>';
        if (data.lpi.profile_picture) {
            content += '<img src="' + data.lpi.profile_picture + '" alt="Profile Picture" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">';
        } else {
            content += '<div class="img-thumbnail d-flex align-items-center justify-content-center" style="width: 200px; height: 200px; background-color: #f8f9fa; border: 1px solid #dee2e6;">';
            content += '<span class="text-muted">No Image</span>';
            content += '</div>';
        }
        content += '</div>';
        
        // Account Information beside profile picture
        content += '<div class="col-md-8">';
        if (data.studentaccount) {
            content += '<h6 class="mb-3">Account Information</h6>';
            content += '<div class="row">';
            content += '<div class="col-md-6 mb-2"><strong>Username:</strong><br>' + (data.studentaccount.studentacc_username || 'N/A') + '</div>';
            content += '<div class="col-md-6 mb-2"><strong>Email:</strong><br>' + (data.studentaccount.studentacc_email || 'N/A') + '</div>';
            content += '<div class="col-md-6 mb-2"><strong>Enrollment Status:</strong><br>' + (data.studentaccount.enrollmentstatus || 'N/A') + '</div>';
            content += '</div>';
        }
        content += '</div>';
        content += '</div>';
        
        content += '<div class="row">';
        content += '<div class="col-md-6"><strong>School Year:</strong> ' + (data.lpi.school_year || 'N/A') + '</div>';
        content += '<div class="col-md-6"><strong>LRN:</strong> ' + (data.lpi.lrn || 'N/A') + '</div>';
        content += '<div class="col-md-6"><strong>Grade Level to Enroll:</strong> ' + (data.lpi.Gradelvl_enroll || 'N/A') + '</div>';
        content += '<div class="col-md-6"><strong>PWD:</strong> ' + (data.lpi.pwd || 'N/A') + '</div>';
        content += '<div class="col-md-6"><strong>PSA No.:</strong> ' + (data.lpi.psa_no || 'N/A') + '</div>';

        // PSA Picture Button
        if (data.lpi.psa_picture) {
            content += '<div class="col-md-6">';
            content += '<strong>PSA Birth Certificate:</strong><br>';
            content += '<button type="button" class="btn btn-outline-primary btn-sm mt-1" onclick="togglePSAPicture()">View PSA Picture</button>';
            content += '</div>';
        }

        // Card Picture Button
        if (data.lpi && data.lpi.card_pic && data.lpi.card_pic.trim() !== '') {
            content += '<div class="col-md-6">';
            content += '<strong>Card:</strong><br>';
            content += '<button type="button" class="btn btn-outline-primary btn-sm mt-1" onclick="toggleCardPicture()">View Card Picture</button>';
            content += '</div>';
        }

        // Good Moral Picture Button
        if (data.lpi && data.lpi.good_moral && data.lpi.good_moral.trim() !== '') {
            content += '<div class="col-md-6">';
            content += '<strong>Good Moral Certificate:</strong><br>';
            content += '<button type="button" class="btn btn-outline-primary btn-sm mt-1" onclick="toggleGoodMoralPicture()">View Good Moral Picture</button>';
            content += '</div>';
        }
        
        content += '<div class="col-md-6"><strong>Birthdate:</strong> ' + (data.lpi.birthdate || 'N/A') + '</div>';
        content += '<div class="col-md-6"><strong>Age:</strong> ' + (data.lpi.age || 'N/A') + '</div>';
        content += '<div class="col-md-6"><strong>Sex:</strong> ' + (data.lpi.sex || 'N/A') + '</div>';
        content += '<div class="col-md-6"><strong>Place of Birth:</strong> ' + (data.lpi.pob || 'N/A') + '</div>';
        content += '<div class="col-md-6"><strong>Religion:</strong> ' + (data.lpi.religion || 'N/A') + '</div>';
        content += '<div class="col-md-6"><strong>First Name:</strong> ' + (data.lpi.fname || 'N/A') + '</div>';
        content += '<div class="col-md-6"><strong>Middle Name:</strong> ' + (data.lpi.mname || 'N/A') + '</div>';
        content += '<div class="col-md-6"><strong>Last Name:</strong> ' + (data.lpi.lname || 'N/A') + '</div>';
        content += '<div class="col-md-6"><strong>Extension Name:</strong> ' + (data.lpi.extname || 'N/A') + '</div>';
        content += '<div class="col-md-6"><strong>Mother Tongue:</strong> ' + (data.lpi.mother_tongue || 'N/A') + '</div>';
        content += '<div class="col-md-6"><strong>IP:</strong> ' + (data.lpi.ip || 'N/A') + '</div>';
        content += '<div class="col-md-6"><strong>4Ps:</strong> ' + (data.lpi['4ps'] || 'N/A') + '</div>';
        content += '</div><hr>';
    }

    // Current Address
    if (data.current_address) {
        content += '<h5>Current Address</h5>';
        content += '<div class="row">';
        content += '<div class="col-md-6"><strong>House No.:</strong> ' + (data.current_address.currenthouse_no || 'N/A') + '</div>';
        content += '<div class="col-md-6"><strong>Street Name:</strong> ' + (data.current_address.current_streetname || 'N/A') + '</div>';
        content += '<div class="col-md-6"><strong>Barangay:</strong> ' + (data.current_address.current_barangay || 'N/A') + '</div>';
        content += '<div class="col-md-6"><strong>Municipality:</strong> ' + (data.current_address.current_municipality || 'N/A') + '</div>';
        content += '<div class="col-md-6"><strong>Province:</strong> ' + (data.current_address.current_province || 'N/A') + '</div>';
        content += '<div class="col-md-6"><strong>Country:</strong> ' + (data.current_address.current_country || 'N/A') + '</div>';
        content += '<div class="col-md-6"><strong>Zip Code:</strong> ' + (data.current_address.current_zipcode || 'N/A') + '</div>';
        content += '</div><hr>';
    }

    // Permanent Address
    if (data.permanent_address) {
        content += '<h5>Permanent Address</h5>';
        content += '<div class="row">';
        content += '<div class="col-md-6"><strong>House No.:</strong> ' + (data.permanent_address.permhouse_no || 'N/A') + '</div>';
        content += '<div class="col-md-6"><strong>Street Name:</strong> ' + (data.permanent_address.perm_streetname || 'N/A') + '</div>';
        content += '<div class="col-md-6"><strong>Barangay:</strong> ' + (data.permanent_address.perm_barangay || 'N/A') + '</div>';
        content += '<div class="col-md-6"><strong>Municipality:</strong> ' + (data.permanent_address.perm_municipality || 'N/A') + '</div>';
        content += '<div class="col-md-6"><strong>Province:</strong> ' + (data.permanent_address.perm_province || 'N/A') + '</div>';
        content += '<div class="col-md-6"><strong>Country:</strong> ' + (data.permanent_address.perm_country || 'N/A') + '</div>';
        content += '<div class="col-md-6"><strong>Zip Code:</strong> ' + (data.permanent_address.perm_zipcode || 'N/A') + '</div>';
        content += '</div><hr>';
    }

    // Parent Info
    if (data.parent_info) {
        content += '<h5>Parent Information</h5>';
        content += '<div class="row">';
        content += '<div class="col-md-6"><strong>Father First Name:</strong> ' + (data.parent_info.father_fname || 'N/A') + '</div>';
        content += '<div class="col-md-6"><strong>Father Middle Name:</strong> ' + (data.parent_info.father_mname || 'N/A') + '</div>';
        content += '<div class="col-md-6"><strong>Father Last Name:</strong> ' + (data.parent_info.father_lname || 'N/A') + '</div>';
        content += '<div class="col-md-6"><strong>Mother First Name:</strong> ' + (data.parent_info.mother_fname || 'N/A') + '</div>';
        content += '<div class="col-md-6"><strong>Mother Middle Name:</strong> ' + (data.parent_info.mother_mname || 'N/A') + '</div>';
        content += '<div class="col-md-6"><strong>Mother Last Name:</strong> ' + (data.parent_info.mother_lname || 'N/A') + '</div>';
        content += '<div class="col-md-6"><strong>Guardian First Name:</strong> ' + (data.parent_info.guardian_fname || 'N/A') + '</div>';
        content += '<div class="col-md-6"><strong>Guardian Middle Name:</strong> ' + (data.parent_info.guardian_mname || 'N/A') + '</div>';
        content += '<div class="col-md-6"><strong>Guardian Last Name:</strong> ' + (data.parent_info.guardian_lname || 'N/A') + '</div>';
        content += '<div class="col-md-6"><strong>Father Contact No.:</strong> ' + (data.parent_info.fathercontact_no || 'N/A') + '</div>';
        content += '<div class="col-md-6"><strong>Mother Contact No.:</strong> ' + (data.parent_info.mothercontact_no || 'N/A') + '</div>';
        content += '<div class="col-md-6"><strong>Guardian Contact No.:</strong> ' + (data.parent_info.guardiancontact_no || 'N/A') + '</div>';
        content += '</div><hr>';
    }

    // LSHS
    if (data.lshs) {
        content += '<h5>Senior High School</h5>';
        content += '<div class="row">';
        content += '<div class="col-md-6"><strong>Semester:</strong> ' + (data.lshs.shs_semester || 'N/A') + '</div>';
        content += '<div class="col-md-6"><strong>Track:</strong> ' + (data.lshs.shs_track || 'N/A') + '</div>';
        content += '<div class="col-md-6"><strong>Strand:</strong> ' + (data.lshs.shs_strand || 'N/A') + '</div>';
        content += '</div><hr>';
    }

    // SNE
    if (data.sne) {
        content += '<h5>Special Needs Education</h5>';
        content += '<div class="row">';
        content += '<div class="col-md-6"><strong>Type of Disability:</strong> ' + (data.sne.type_disability || 'N/A') + '</div>';
        content += '</div><hr>';
    }

    // Transferee
    if (data.transferee) {
        content += '<h5>Transferee Information</h5>';
        content += '<div class="row">';
        content += '<div class="col-md-6"><strong>Last Grade Level:</strong> ' + (data.transferee.last_gradelevel || 'N/A') + '</div>';
        content += '<div class="col-md-6"><strong>Last School Year Completed:</strong> ' + (data.transferee.last_sycompleted || 'N/A') + '</div>';
        content += '<div class="col-md-6"><strong>Last School Attended:</strong> ' + (data.transferee.last_schoolattended || 'N/A') + '</div>';
        content += '<div class="col-md-6"><strong>Last School ID:</strong> ' + (data.transferee.last_schoolid || 'N/A') + '</div>';
        content += '</div><hr>';
    }

    // Learning Modalities
    if (data.learning_modalities) {
        content += '<h5>Learning Modalities</h5>';
        content += '<div class="row">';
        content += '<div class="col-md-6"><strong>Learning Modality:</strong> ' + (data.learning_modalities.learning_mod || 'N/A') + '</div>';
        content += '</div>';
    }

    modalBody.innerHTML = content;
    
    // Add PSA picture container at the end
    if (data.lpi && data.lpi.psa_picture) {
        console.log('Creating PSA picture container with URL:', data.lpi.psa_picture);
        const psaContainer = document.createElement('div');
        psaContainer.id = 'psaPictureContainer';
        psaContainer.className = 'mt-3 d-none';
        psaContainer.innerHTML = `
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">PSA Birth Certificate</h6>
                    <button type="button" class="btn-close" onclick="togglePSAPicture()" aria-label="Close"></button>
                </div>
                <div class="card-body text-center">
                    <img src="${data.lpi.psa_picture}" alt="PSA Birth Certificate" class="img-fluid" style="max-height: 60vh; max-width: 100%;">
                </div>
            </div>
        `;
        modalBody.appendChild(psaContainer);
        console.log('PSA container appended');
    }

    // Helper function to ensure URL has file extension
    function ensureUrlHasExtension(url) {
        if (!url) return url;
        // If URL already has an extension, return as-is
        if (/\.(jpg|jpeg|png|gif|webp|pdf)$/i.test(url)) {
            return url;
        }
        // If it's a Cloudinary URL without extension, try to add .jpg (most common)
        if (url.indexOf('res.cloudinary.com') !== -1) {
            return url + '.jpg';
        }
        return url;
    }
    
    // Add Card picture container at the end
    if (data.lpi && data.lpi.card_pic && data.lpi.card_pic.trim() !== '') {
        console.log('Creating Card picture container with URL:', data.lpi.card_pic);
        const cardContainer = document.createElement('div');
        cardContainer.id = 'cardPictureContainer';
        cardContainer.className = 'mt-3 d-none';
        // Ensure URL has extension
        let cardPicUrl = ensureUrlHasExtension(data.lpi.card_pic);
        // Escape the URL to prevent XSS
        cardPicUrl = cardPicUrl.replace(/"/g, '&quot;').replace(/'/g, '&#39;');
        console.log('Card picture URL (with extension):', cardPicUrl);
        cardContainer.innerHTML = `
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Card</h6>
                    <button type="button" class="btn-close" onclick="toggleCardPicture()" aria-label="Close"></button>
                </div>
                <div class="card-body text-center">
                    <img src="${cardPicUrl}" alt="Card" class="img-fluid" style="max-height: 60vh; max-width: 100%;" onerror="console.error('Failed to load card image:', this.src);">
                </div>
            </div>
        `;
        modalBody.appendChild(cardContainer);
        console.log('Card container appended, ID:', cardContainer.id);
    } else {
        console.log('Card picture not found or empty. data.lpi:', data.lpi, 'card_pic:', data.lpi ? data.lpi.card_pic : 'N/A');
    }

    // Add Good Moral picture container at the end
    if (data.lpi && data.lpi.good_moral && data.lpi.good_moral.trim() !== '') {
        console.log('Creating Good Moral picture container with URL:', data.lpi.good_moral);
        const goodMoralContainer = document.createElement('div');
        goodMoralContainer.id = 'goodMoralPictureContainer';
        goodMoralContainer.className = 'mt-3 d-none';
        // Ensure URL has extension
        let goodMoralUrl = ensureUrlHasExtension(data.lpi.good_moral);
        // Escape the URL to prevent XSS
        goodMoralUrl = goodMoralUrl.replace(/"/g, '&quot;').replace(/'/g, '&#39;');
        console.log('Good Moral URL (with extension):', goodMoralUrl);
        goodMoralContainer.innerHTML = `
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Good Moral Certificate</h6>
                    <button type="button" class="btn-close" onclick="toggleGoodMoralPicture()" aria-label="Close"></button>
                </div>
                <div class="card-body text-center">
                    <img src="${goodMoralUrl}" alt="Good Moral Certificate" class="img-fluid" style="max-height: 60vh; max-width: 100%;" onerror="console.error('Failed to load good moral image:', this.src);">
                </div>
            </div>
        `;
        modalBody.appendChild(goodMoralContainer);
        console.log('Good Moral container appended, ID:', goodMoralContainer.id);
    } else {
        console.log('Good Moral picture not found or empty. data.lpi:', data.lpi, 'good_moral:', data.lpi ? data.lpi.good_moral : 'N/A');
    }
}


// Function to toggle PSA picture visibility
function togglePSAPicture() {
    const container = document.getElementById('psaPictureContainer');
    if (!container) return;

    if (container.classList.contains('d-none')) {
        // Show the container
        container.classList.remove('d-none');
        // Update button text
        const button = document.querySelector('button[onclick*="togglePSAPicture"]');
        if (button) {
            button.textContent = 'Hide PSA Picture';
        }
    } else {
        // Hide the container
        container.classList.add('d-none');
        // Update button text
        const button = document.querySelector('button[onclick*="togglePSAPicture"]');
        if (button) {
            button.textContent = 'View PSA Picture';
        }
    }
}

// Function to toggle Card picture visibility
function toggleCardPicture() {
    console.log('toggleCardPicture called');
    const container = document.getElementById('cardPictureContainer');
    console.log('Card container found:', container);
    if (!container) {
        console.error('Card picture container not found!');
        alert('Card picture container not found. Please check the console for details.');
        return;
    }

    if (container.classList.contains('d-none')) {
        // Show the container
        container.classList.remove('d-none');
        // Update button text
        const button = document.querySelector('button[onclick*="toggleCardPicture"]');
        if (button) {
            button.textContent = 'Hide Card Picture';
        }
        console.log('Card picture shown');
    } else {
        // Hide the container
        container.classList.add('d-none');
        // Update button text
        const button = document.querySelector('button[onclick*="toggleCardPicture"]');
        if (button) {
            button.textContent = 'View Card Picture';
        }
        console.log('Card picture hidden');
    }
}

// Function to toggle Good Moral picture visibility
function toggleGoodMoralPicture() {
    console.log('toggleGoodMoralPicture called');
    const container = document.getElementById('goodMoralPictureContainer');
    console.log('Good Moral container found:', container);
    if (!container) {
        console.error('Good Moral picture container not found!');
        alert('Good Moral picture container not found. Please check the console for details.');
        return;
    }

    if (container.classList.contains('d-none')) {
        // Show the container
        container.classList.remove('d-none');
        // Update button text
        const button = document.querySelector('button[onclick*="toggleGoodMoralPicture"]');
        if (button) {
            button.textContent = 'Hide Good Moral Picture';
        }
        console.log('Good Moral picture shown');
    } else {
        // Hide the container
        container.classList.add('d-none');
        // Update button text
        const button = document.querySelector('button[onclick*="toggleGoodMoralPicture"]');
        if (button) {
            button.textContent = 'View Good Moral Picture';
        }
        console.log('Good Moral picture hidden');
    }
}
</script>
</html>