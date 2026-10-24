<head>
    <link href="https://cdn.jsdelivr.net" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

<!-- Bootstrap JS bundle (Popper included) -->
<script src="https://cdn.jsdelivr.net" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5b9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="sidebar_navbar.css">
</head>

<div class="sidebar-main"> <!-- div that dictates sidebar width and length-->
    <aside id="sidebar">
        <div class="d-flex"> <!-- div for sidebar toggle -->
            <button id="toggle-btn" type="button">
                <img src="adminicons/logomnhs.png" width="29px">
            </button>
            <div class="sidebar-logo">
                <a class="fs-5 fw-bold">Admin</a>
            </div>
        </div>
        <ul class="sidebar-nav">
            <li class="sidebar-item">
                <a role="button" onclick="window.location.href = 'admindashboard.php';" class="sidebar-link">
                    <i class="bi bi-display-fill"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a role="button" class="sidebar-link has-dropdown collapsed" data-bs-toggle="collapse"
                    data-bs-target="#branch-opt" aria-expanded="false" aria-controls="branch-opt">
                    <i class="bi bi-card-checklist"></i>
                    <span>Enrollees</span>
                </a>
                <ul id="branch-opt" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                    <li class="sidebar-item">
                        <a role="button" onclick="window.location.href = 'pendingforms.php';" class="sidebar-link">Pending Enrollees</a>
                    </li>
                    <li class="sidebar-item">
                        <a role="button" onclick="window.location.href = 'enrolledstudents.php';" class="sidebar-link">Enrolled Students</a>
                    </li>
                </ul>
            </li>
           
           
            <li class="sidebar-item">
                <a role="button" onclick="window.location.href = 'setupdates.php';" class="sidebar-link">
                    <i class="bi bi-envelope-paper-fill"></i>
                    <span>Generate Updates</span>
                </a>
            </li>
          
            <li class="sidebar-item">
                <a role="button" class="sidebar-link has-dropdown collapsed" data-bs-toggle="collapse"
                    data-bs-target="#reports-opt" aria-expanded="false" aria-controls="reports-opt">
                    <i class="bi bi-clock-history"></i>
                    <span>Reports</span>
                </a>
                <ul id="reports-opt" class="sidebar-dropdown list-unstyled collapse"
                    data-bs-parent="#sidebar">
                    <li class="sidebar-item">
                        <a role="button" onclick="window.location.href = 'login_history.php';" class="sidebar-link">Login History</a>
                    </li>
                </ul>
            </li>
        
        </ul>
        <div class="sidebar-footer">
            <a role="button" class="sidebar-link" data-bs-toggle="modal" data-bs-target="#confirmLogout">
                <i class="bi bi-box-arrow-left"></i>

                <span>Logout</span>
            </a>
        </div>
    </aside>
</div>


<!-- JQuery Script for Changing Tabs
<script src="JQuery/changeTab.js"></script> -->

<!-- Sidebar Toggle Script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var toggleBtn = document.getElementById('toggle-btn');
        var sidebar = document.getElementById('sidebar');
        var sidebarMain = document.querySelector('.sidebar-main');

        if (toggleBtn && sidebar && sidebarMain) {
            toggleBtn.addEventListener('click', function() {
                sidebar.classList.toggle('expand');
                sidebarMain.classList.toggle('expand');
            });
        }
    });
</script>

<!-- // Modal -->
<div class="modal fade" id="confirmLogout" tabindex="-1" aria-labelledby="logoutLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="logoutLabel">Confirm</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to Log out?
            </div>
            <form action="logout.php" method="POST">
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Log Out</button>
                </div>
            </form>
        </div>
    </div>
</div>