<?php
session_start();
if (!isset($_SESSION['id'])) {
    ?><script>alert("You are logged out!"); window.location.href="login.php";</script>
    <?php
    exit();
}
include('admin/dbcon.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Job Portal</title>
  <?php include 'links.php' ?>
</head>
<body class="dashboard-body">

<nav class="navbar navbar-expand-lg glass-nav fixed-top">
  <div class="container-fluid px-5 custom-nav-container">
      <a class="navbar-brand d-flex align-items-center" href="index.php">
        <div class="brand-icon mr-2"><i class="fas fa-layer-group"></i></div>
        <span class="brand-text">Job<span class="brand-highlight">Portal</span></span>
      </a>
      
      <button class="navbar-toggler custom-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
        <span class="fas fa-bars fa-lg text-dark"></span>
      </button>

      <div class="collapse navbar-collapse justify-content-between" id="collapsibleNavbar">
        <!-- Center Menu -->
        <ul class="navbar-nav mx-auto center-menu">
           <li class="nav-item">
            <a class="nav-link" href="index.php">Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="profile.php">Profile</a>
          </li>
          
           <!-- Categories Dropdown (New) -->
           <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="catDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              Jobs
            </a>
            <div class="dropdown-menu shadow-lg border-0" aria-labelledby="catDropdown" style="border-radius: 15px; margin-top: 10px;">
                <a class="dropdown-item" href="browse_jobs.php"><i class="fas fa-briefcase mr-2 text-success"></i> Browse Company Jobs</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="quiz.php?category=PHP"><i class="fab fa-php mr-2 text-primary"></i> PHP Developer</a>
                <a class="dropdown-item" href="quiz.php?category=Java"><i class="fab fa-java mr-2 text-danger"></i> Java Developer</a>
                <a class="dropdown-item" href="quiz.php?category=Python"><i class="fab fa-python mr-2 text-warning"></i> Python Developer</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="quiz.php?category=Frontend"><i class="fab fa-html5 mr-2 text-danger"></i> Frontend Dev</a>
                <a class="dropdown-item" href="quiz.php?category=JavaScript"><i class="fab fa-js mr-2 text-warning"></i> JS Developer</a>
                <a class="dropdown-item" href="quiz.php?category=UI/UX"><i class="fas fa-pencil-ruler mr-2 text-info"></i> UI/UX Designer</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="quiz.php?category=DataScience"><i class="fas fa-chart-line mr-2 text-success"></i> Data Scientist</a>
                 <a class="dropdown-item" href="quiz.php?category=Marketing"><i class="fas fa-bullhorn mr-2 text-danger"></i> Marketing</a>
            </div>
          </li>
          
          <li class="nav-item">
            <a class="nav-link" href="available_companies.php">
              <i class="fas fa-building mr-1"></i> Companies
            </a>
          </li>

          <li class="nav-item">
            <a class="nav-link" href="my_application.php">Applications</a>
          </li>
        </ul>
        
        <!-- Right Action Menu -->
        <ul class="navbar-nav align-items-center right-menu">
            <!-- Theme Switcher -->
            <li class="nav-item dropdown mr-3">
                <a class="nav-link nav-icon-btn d-flex align-items-center justify-content-center" href="#" id="themeDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Switch Theme">
                    <i class="fas fa-swatchbook"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow-lg border-0 theme-dropdown" aria-labelledby="themeDropdown">
                    <h6 class="dropdown-header text-uppercase text-xs font-weight-bold pl-3 mb-2 opacity-50">Theme</h6>
                    <a class="dropdown-item" href="#" onclick="setTheme('default'); return false;"><span class="dot bg-primary mr-2"></span>Default</a>
                    <a class="dropdown-item" href="#" onclick="setTheme('ocean'); return false;"><span class="dot bg-info mr-2"></span>Ocean</a>
                    <a class="dropdown-item" href="#" onclick="setTheme('sunset'); return false;"><span class="dot bg-warning mr-2"></span>Sunset</a>
                    <a class="dropdown-item" href="#" onclick="setTheme('dark'); return false;"><span class="dot bg-dark mr-2"></span>Dark</a>
                </div>
            </li>
            
            <!-- User Profile Dropdown -->
             <li class="nav-item dropdown">
                <a class="nav-link user-pill dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                   <div class="user-avatar-sm">
                        <i class="fas fa-user"></i>
                   </div>
                   <span class="user-name-text"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow-lg border-0 user-menu-dropdown" aria-labelledby="userDropdown">
                    <div class="px-4 py-3 bg-light-gradient border-bottom mb-2">
                        <small class="text-muted d-block text-uppercase font-weight-bold" style="font-size: 0.65rem;">Signed in as</small>
                        <h6 class="mb-0 font-weight-bold text-dark text-truncate" style="max-width: 150px;"><?php echo htmlspecialchars($_SESSION['username']); ?></h6>
                    </div>
                    <a class="dropdown-item" href="profile.php"><i class="fas fa-user-circle mr-2 text-muted"></i> My Profile</a>
                    <a class="dropdown-item" href="my_application.php"><i class="fas fa-file-alt mr-2 text-muted"></i> Applications</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt mr-2"></i> Logout</a>
                </div>
            </li>
        </ul>
      </div> 
  </div>
</nav>

<!-- Spacer for fixed navbar -->
<div style="margin-top: 100px;"></div>

<script>
    // Theme Switcher Logic
    function setTheme(themeName) {
        document.body.setAttribute('data-theme', themeName);
        localStorage.setItem('theme', themeName);
    }

    // Load Saved Theme
    (function() {
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme) {
            document.body.setAttribute('data-theme', savedTheme);
        }
    })();
</script>