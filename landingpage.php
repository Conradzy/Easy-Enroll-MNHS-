<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>EASY - ENROLL | Maragusan National High School</title>
  <link rel="icon" href="adminicons/logomnhs.png" type="image/png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f8faff;
      overflow-x: hidden;
    }

    /* Hero Section */
    .hero {
      position: relative;
      overflow: hidden;
      color: white;
      padding: 120px 0;
      text-align: center;
    }

    .hero-video {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      object-fit: cover;
      z-index: -1;
    }

    .hero h1 {
      font-weight: 700;
      font-size: 3rem;
    }

    .hero p {
      font-size: 1.1rem;
      max-width: 700px;
      margin: 20px auto;
    }

    .hero .btn-primary {
      background-color: #007bff;
      border: none;
      border-radius: 50px;
      padding: 10px 25px;
      transition: 0.3s ease;
    }

    .hero .btn-primary:hover {
      background-color: #0056b3;
      transform: translateY(-2px);
    }

    /* Features */
    .features {
      padding: 100px 0;
      background-color: white;
    }

    .feature-card {
      background: #edf4ffff;
      border: none;
      border-radius: 15px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.05);
      transition: all 0.3s ease;
      text-align: center;
      padding: 30px;
      height: 100%;
    }

    .feature-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 25px rgba(0,0,0,0.08);
    }

    .feature-card img {
      width: 70px;
      margin-bottom: 20px;
    }

    /* Footer */
    footer {
      background-color: #001f3f;
      color: white;
      padding: 40px 0;
    }

    footer a {
      color: #aad4ff;
      text-decoration: none;
    }

    footer a:hover {
      text-decoration: underline;
    }

    .social-icons a {
      color: white;
      font-size: 20px;
      margin-right: 15px;
      transition: color 0.3s ease;
    }

    .social-icons a:hover {
      color: #aad4ff;
    }
  </style>
</head>
<body>

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-light bg-transparent fixed-top"> 
    <div class="container-fluid">
      <a class="navbar-brand fw-bold text-light ms-2 d-flex align-items-center" href="login.php">
        <img src="emsbg/emslogo.png" alt="EMS Logo" width="40" height="40" class="me-2">
        <span style="font-size: 24px;">EASY - ENROLL</span>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav">
          <li class="nav-item ms-2"><a class="nav-link text-light" href="adminlogin.php">Admin</a></li>
          <li class="nav-item"><a class="nav-link text-light" href="login.php">Student</a></li>
          <li class="nav-item"><a class="nav-link text-light" href="register.php">Register</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Hero Section -->
  <section class="hero d-flex flex-column justify-content-center align-items-center text-center">
    <video class="hero-video" autoplay muted loop>
      <source src="emsbg/bidyowbg.mp4" type="video/mp4">
      Your browser does not support the video tag.
    </video>
    <div class="container">
      <h1>EASY - ENROLL</h1>
      <h4 class="fw-light mt-2 mb-4">Maragusan National High School</h4>
      <p>
        Experience hassle-free online enrollment. Say goodbye to long lines and manual processes.
        Manage student data with security, accuracy, and ease.
      </p>
      <a href="register.php" class="btn btn-primary mt-3">Get Started</a>
    </div>
  </section>

  <!-- Features Section -->
  <section class="features">
    <div class="container">
      <div class="text-center mb-5">
        <h2 class="fw-bold" style="color:#001f3f;">Why Choose EMS?</h2>
        <p class="text-muted">Simplify and secure your enrollment process with our powerful online system.</p>
      </div>

      <div class="row g-4">
        <div class="col-md-4">
          <div class="feature-card">
            <img src="https://cdn-icons-png.flaticon.com/512/1042/1042339.png" alt="Online Enrollment">
            <h5 class="fw-bold">Online Enrollment</h5>
            <p>Easily enroll from anywhere, anytime — no more waiting in long lines or filling out paper forms.</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="feature-card">
            <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="Admin Access">
            <h5 class="fw-bold">Easy Admin Access</h5>
            <p>Administrators can efficiently manage, update, and view student records with just a few clicks.</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="feature-card">
            <img src="https://cdn-icons-png.flaticon.com/512/4845/4845825.png" alt="Data Security">
            <h5 class="fw-bold">Data Security & Privacy</h5>
            <p>Protecting student information is our top priority with secure database encryption and privacy measures.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="text-center text-lg-start">
    <div class="container text-center text-md-start">
      <div class="row">
        <div class="col-md-6 mb-4">
          <h5 class="fw-bold">Maragusan National High School</h5>
          <p>Empowering education through technology — simplifying enrollment for students and administrators alike.</p>
        </div>
        <div class="col-md-3 mb-4">
          <h6 class="fw-bold">Quick Links</h6>
          <ul class="list-unstyled">
            <li><a href="register.php">Register</a></li>
            <li><a href="login.php">Student Login</a></li>
            <li><a href="adminlogin.php">Admin Login</a></li>
          </ul>
        </div>
        <div class="col-md-3 mb-4">
          <h6 class="fw-bold">Connect With Us</h6>
          <div class="social-icons">
            <a href="https://www.facebook.com/MaragusanNHSOfficial"><i class="bi bi-facebook"></i></a>
            <a href="#"><i class="bi bi-twitter"></i></a>
            <a href="#"><i class="bi bi-envelope-fill"></i></a>
          </div>
        </div>
      </div>
    </div>
    <hr class="border-light">
    <div class="text-center py-3">
      <small>&copy; 2025 Maragusan National High School | Enrollment Management System. All rights reserved.</small>
    </div>
  </footer>

  <!-- Bootstrap & Icons -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</body>
</html>
