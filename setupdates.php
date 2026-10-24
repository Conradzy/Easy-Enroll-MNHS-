<?php
include 'db_connection.php';

$message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_id'])) {
        // Handle delete
        $delete_id = $_POST['delete_id'];
        try {
            $stmt = $pdo->prepare("DELETE FROM updates WHERE update_id = ?");
            $stmt->execute([$delete_id]);
            $message = 'Update deleted successfully!';
        } catch (PDOException $e) {
            $message = 'Error deleting update: ' . $e->getMessage();
        }
    } else {
        // Handle add
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $date_issued = $_POST['date_issued'] ?? '';
        $person = trim($_POST['person'] ?? '');

        if (empty($title) || empty($content) || empty($date_issued) || empty($person)) {
            $message = 'All fields are required.';
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO updates (title, content, date_issued, person_approved) VALUES (?, ?, ?, ?)");
                $stmt->execute([$title, $content, $date_issued, $person]);
                $message = 'Update added successfully!';
            } catch (PDOException $e) {
                $message = 'Error adding update: ' . $e->getMessage();
            }
        }
    }
}

// Fetch all updates
$updates = $pdo->query("SELECT * FROM updates ORDER BY date_issued DESC, update_id DESC")->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
   
    <style>
        /* Prevent page scrolling and keep layout within the viewport */
        html,
        body {
            height: 100%;
            
            /* disable page scrollbars */
            background-color: #e7e7e7ff;
            font-family: 'Poppins', sans-serif;
        }

        * {
            font-family: 'Poppins', sans-serif !important;
        }

        .sidebar-link {
            text-decoration: none;
        }

        .form-section-title {
            font-weight: 600;
            margin-top: 1.5rem;
        }

        .muted-note {
            font-size: .95rem;
            white-space: pre-line;
        }

    

        #content {
            transition: margin-left 0.3s;
            height: 100vh;
            overflow: auto;
        }

      

        .text-justify {
            text-align: justify;
        }
    </style>
</head>

<body>
    <div class="d-flex">
        <!-- Sidebar: column layout so logout is pinned to the bottom of the sidebar -->
     <?php include 'sidebar_admin.php'; ?>

        <div class="container d-flex flex-column">
            <h1 class="mt-5 m-2" style="font-weight: bold; color: #0a397eff;">Updates</h1>
            <hr class="mt-5 m-2">
            
            <div class="container d-flex mt-4">
                <div class="card p-4 w-100">
                    <h4 class="mb-4">Add New Update</h4>
                    <?php if (!empty($message)): ?>
                        <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
                    <?php endif; ?>
                    <form method="post">
                        <div class="mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="content" class="form-label">Content</label>
                            <textarea class="form-control" id="content" name="content" rows="5" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="date_issued" class="form-label">Date Issued</label>
                            <input type="date" class="form-control" id="date_issued" name="date_issued" required>
                        </div>
                        <div class="mb-3">
                            <label for="person" class="form-label">Person Who Approved</label>
                            <input type="text" class="form-control" id="person" name="person" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Update</button>
                    </form>
                </div>
            </div>

            <div class="container mt-4 mb-5">
                <h4 class="mb-4">Recent Updates</h4>
                <?php if (empty($updates)): ?>
                    <p>No updates found.</p>
                <?php else: ?>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th style="background-color: #1d4584;" class="text-white">Title</th>
                                <th style="background-color: #1d4584;" class="text-white">Date</th>
                                <th style="background-color: #1d4584;" class="text-white">Approved by</th>
                                <th style="background-color: #1d4584;" class="text-white">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($updates as $update): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($update['title']); ?></td>
                                    <td><?php echo htmlspecialchars($update['date_issued']); ?></td>
                                    <td><?php echo htmlspecialchars($update['person_approved']); ?></td>
                                    <td>
                                        <button class="btn btn-info btn-sm me-2" data-bs-toggle="modal" data-bs-target="#viewModal"
                                                data-title="<?php echo htmlspecialchars($update['title']); ?>"
                                                data-content="<?php echo htmlspecialchars($update['content']); ?>"
                                                data-date="<?php echo htmlspecialchars($update['date_issued']); ?>"
                                                data-person="<?php echo htmlspecialchars($update['person_approved']); ?>">
                                            View
                                        </button>
                                        <form method="post" style="display:inline;">
                                            <input type="hidden" name="delete_id" value="<?php echo $update['update_id']; ?>">
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this update?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

            <!-- Modal for viewing update -->
            <div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="viewModalLabel">Update Details</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body d-flex flex-column">
                            <h4 id="modalTitle" class="text-center mb-3"></h4>
                            <p id="modalContent" class="text-justify mb-5"></p>
                            <div class="d-flex justify-content-between mt-auto">
                                <p><strong>Date Issued:</strong> <span id="modalDate"></span></p>
                                <p><strong>Approved by:</strong> <span id="modalPerson"></span></p>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
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

            // Handle view modal
            var viewModal = document.getElementById('viewModal');
            if (viewModal) {
                viewModal.addEventListener('show.bs.modal', function (event) {
                    var button = event.relatedTarget;
                    var title = button.getAttribute('data-title');
                    var content = button.getAttribute('data-content');
                    var date = button.getAttribute('data-date');
                    var person = button.getAttribute('data-person');

                    var modalTitle = viewModal.querySelector('#modalTitle');
                    var modalContent = viewModal.querySelector('#modalContent');
                    var modalDate = viewModal.querySelector('#modalDate');
                    var modalPerson = viewModal.querySelector('#modalPerson');

                    modalTitle.textContent = title;
                    modalContent.textContent = content;
                    modalDate.textContent = date;
                    modalPerson.textContent = person;
                });
            }
        });
    </script>

</html>