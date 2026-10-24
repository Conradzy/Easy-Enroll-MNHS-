<nav class="navbar navbar-expand-lg navbar-light bg-transparent fixed-top">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold text-light ms-2 d-flex align-items-center" href="landingpage.php">
      <img src="emsbg/emslogo.png" alt="EMS Logo" width="40" height="40" class="me-2">
      <span style="font-size: 24px;">EASY - ENROLL</span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation" id="navbarToggler">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
      <ul class="navbar-nav">
        
        <li class="nav-item">
          <a class="nav-link text-light" href="adminlogin.php">admin</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-light" href="login.php">student</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-light" href="register.php">Register</a>
        </li>
      </ul>
    </div>
  </div>
</nav>
<style>
  .navbar-toggler {
    border: 1px solid rgba(255, 255, 255, 0.3);
    background-color: transparent !important;
    transition: background-color 0.3s ease, border-color 0.3s ease;
  }
  .navbar-toggler-icon {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 1%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
  }
  .navbar-toggler.active,
  .navbar-toggler[aria-expanded="true"] {
    background-color: rgba(255, 255, 255, 0.25) !important;
    border-color: rgba(255, 255, 255, 0.6);
    box-shadow: 0 0 0 0.2rem rgba(255, 255, 255, 0.25);
  }
  /* Menu content background - transparent by default */
  #navbarNav {
    background-color: transparent;
    transition: background-color 0.3s ease;
    border-radius: 8px;
    padding: 0.5rem 0;
  }
  /* Menu content background - visible when expanded */
  #navbarNav.show,
  #navbarNav.collapsing,
  #navbarNav:not(.collapse) {
    background-color: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
  }
</style>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const toggler = document.getElementById('navbarToggler');
    const navbarNav = document.getElementById('navbarNav');
    
    if (toggler && navbarNav) {
      // Function to update toggler state
      function updateTogglerState(isExpanded) {
        if (isExpanded) {
          toggler.classList.add('active');
          toggler.style.backgroundColor = 'rgba(255, 255, 255, 0.25)';
        } else {
          toggler.classList.remove('active');
          toggler.style.backgroundColor = 'transparent';
        }
      }
      
      // Function to update menu content background
      function updateMenuBackground(isExpanded) {
        if (isExpanded) {
          navbarNav.style.backgroundColor = 'rgba(255, 255, 255, 0.15)';
          navbarNav.classList.add('show');
        } else {
          navbarNav.style.backgroundColor = 'transparent';
          navbarNav.classList.remove('show');
        }
      }
      
      // Listen to Bootstrap collapse events
      navbarNav.addEventListener('show.bs.collapse', function() {
        updateTogglerState(true);
        updateMenuBackground(true);
      });
      
      navbarNav.addEventListener('shown.bs.collapse', function() {
        updateTogglerState(true);
        updateMenuBackground(true);
      });
      
      navbarNav.addEventListener('hide.bs.collapse', function() {
        updateTogglerState(false);
        updateMenuBackground(false);
      });
      
      navbarNav.addEventListener('hidden.bs.collapse', function() {
        updateTogglerState(false);
        updateMenuBackground(false);
      });
      
      // Also handle click events as backup
      toggler.addEventListener('click', function() {
        // Small delay to let Bootstrap update aria-expanded
        setTimeout(function() {
          const isExpanded = toggler.getAttribute('aria-expanded') === 'true';
          updateTogglerState(isExpanded);
          updateMenuBackground(isExpanded);
        }, 10);
      });
      
      // Check initial state
      const initialExpanded = toggler.getAttribute('aria-expanded') === 'true';
      updateTogglerState(initialExpanded);
      updateMenuBackground(initialExpanded);
    }
  });
</script>