<?php
// Company Navigation Component
// Include this file in all company pages after session check

$current_page = basename($_SERVER['PHP_SELF']);
$company_name = $_SESSION['company_name'] ?? 'Company Dashboard';
$company_logo = $_SESSION['company_logo'] ?? '';
?>

<!-- Modern Company Navigation -->
<nav class="navbar navbar-expand-lg navbar-dark company-navbar">
    <div class="container-fluid px-4">
        <a class="navbar-brand d-flex align-items-center company-brand" href="index.php">
            <?php if (!empty($company_logo) && file_exists('../' . $company_logo)): ?>
                <img src="../<?php echo $company_logo; ?>" alt="<?php echo htmlspecialchars($company_name); ?>" 
                     class="company-logo-img">
            <?php else: ?>
                <div class="company-logo-placeholder">
                    <i class="fas fa-building"></i>
                </div>
            <?php endif; ?>
            <span class="company-name-text"><?php echo htmlspecialchars($company_name); ?></span>
        </a>
        
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#companyNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="companyNavbar">
            <ul class="navbar-nav ml-auto align-items-center">
                <!-- Dashboard -->
                <li class="nav-item <?php echo $current_page == 'index.php' ? 'active' : ''; ?>">
                    <a class="nav-link nav-link-modern" href="index.php">
                        <i class="fas fa-home"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                
                <!-- Jobs Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link nav-link-modern dropdown-toggle" href="#" id="jobsDropdown" 
                       role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-briefcase"></i>
                        <span>Jobs</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-modern shadow-lg" aria-labelledby="jobsDropdown">
                        <h6 class="dropdown-header">
                            <i class="fas fa-briefcase mr-2"></i>Job Management
                        </h6>
                        <a class="dropdown-item <?php echo $current_page == 'my_jobs.php' ? 'active' : ''; ?>" href="my_jobs.php">
                            <i class="fas fa-list text-primary"></i>
                            <div>
                                <strong>My Posted Jobs</strong>
                                <small>View all your job postings</small>
                            </div>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item <?php echo $current_page == 'post_job.php' ? 'active' : ''; ?>" href="post_job.php">
                            <i class="fas fa-plus-circle text-success"></i>
                            <div>
                                <strong>Post New Job</strong>
                                <small>Create a new job listing</small>
                            </div>
                        </a>
                    </div>
                </li>
                
                <!-- Applicants Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link nav-link-modern dropdown-toggle" href="#" id="applicantsDropdown" 
                       role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-users"></i>
                        <span>Applicants</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-modern shadow-lg" aria-labelledby="applicantsDropdown">
                        <h6 class="dropdown-header">
                            <i class="fas fa-users mr-2"></i>Candidate Management
                        </h6>
                        <a class="dropdown-item <?php echo $current_page == 'view_applicants.php' ? 'active' : ''; ?>" href="view_applicants.php">
                            <i class="fas fa-file-alt text-info"></i>
                            <div>
                                <strong>Job Applicants</strong>
                                <small>View job-specific applications</small>
                            </div>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item <?php echo $current_page == 'category_applicants.php' ? 'active' : ''; ?>" href="category_applicants.php">
                            <i class="fas fa-user-graduate text-warning"></i>
                            <div>
                                <strong>Category Applicants</strong>
                                <small>View category-based applications</small>
                            </div>
                        </a>
                    </div>
                </li>
                
                <!-- Profile -->
                <li class="nav-item <?php echo $current_page == 'profile.php' ? 'active' : ''; ?>">
                    <a class="nav-link nav-link-modern" href="profile.php">
                        <i class="fas fa-user-circle"></i>
                        <span>Profile</span>
                    </a>
                </li>
                
                <!-- Theme Toggle -->
                <li class="nav-item mx-2">
                    <button class="btn-theme-toggle" id="themeToggle" type="button" 
                            onclick="toggleTheme()" title="Toggle Theme">
                        <i class="fas fa-moon" id="themeIcon"></i>
                    </button>
                </li>
                
                <!-- Logout -->
                <li class="nav-item">
                    <a class="btn btn-logout-modern" href="logout.php">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<style>
    .company-navbar {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        padding: 0.8rem 0;
        position: sticky;
        top: 0;
        z-index: 1000;
    }
    
    .company-brand {
        font-size: 1.4rem;
        font-weight: 700;
        color: white !important;
        transition: all 0.3s;
    }
    
    .company-brand:hover {
        transform: scale(1.05);
    }
    
    .company-logo-img {
        height: 45px;
        width: auto;
        object-fit: contain;
        margin-right: 15px;
        background: white;
        padding: 6px;
        border-radius: 10px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.15);
        transition: all 0.3s;
    }
    
    .company-logo-img:hover {
        transform: rotate(-5deg) scale(1.1);
    }
    
    .company-logo-placeholder {
        background: linear-gradient(135deg, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0.1) 100%);
        width: 45px;
        height: 45px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        border: 2px solid rgba(255,255,255,0.3);
    }
    
    .company-logo-placeholder i {
        color: white;
        font-size: 1.5rem;
    }
    
    .company-name-text {
        background: linear-gradient(135deg, #fff 0%, #e0e0e0 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
    }
    
    .nav-link-modern {
        color: rgba(255,255,255,0.9) !important;
        font-weight: 500;
        padding: 10px 16px !important;
        margin: 0 4px;
        border-radius: 10px;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .nav-link-modern:hover {
        background: rgba(255,255,255,0.15);
        color: white !important;
        transform: translateY(-2px);
    }
    
    .nav-item.active .nav-link-modern {
        background: rgba(255,255,255,0.2);
        color: white !important;
        font-weight: 600;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .nav-link-modern i {
        font-size: 1.1rem;
        width: 20px;
        text-align: center;
    }
    
    .dropdown-menu-modern {
        border: none;
        border-radius: 12px;
        padding: 10px 0;
        margin-top: 12px;
        min-width: 280px;
        animation: slideDown 0.3s ease;
        background: white;
    }
    
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-15px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .dropdown-header {
        color: #667eea;
        font-weight: 700;
        font-size: 0.85rem;
        padding: 12px 20px 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .dropdown-menu-modern .dropdown-item {
        padding: 12px 20px;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        gap: 15px;
        border-left: 3px solid transparent;
    }
    
    .dropdown-menu-modern .dropdown-item:hover {
        background: linear-gradient(90deg, #f8f9fa 0%, #e9ecef 100%);
        border-left-color: #667eea;
        padding-left: 25px;
    }
    
    .dropdown-menu-modern .dropdown-item.active {
        background: linear-gradient(90deg, #e7e9fc 0%, #f0f1ff 100%);
        border-left-color: #667eea;
        font-weight: 600;
    }
    
    .dropdown-menu-modern .dropdown-item i {
        font-size: 1.3rem;
        width: 30px;
        text-align: center;
    }
    
    .dropdown-menu-modern .dropdown-item div {
        flex: 1;
    }
    
    .dropdown-menu-modern .dropdown-item strong {
        display: block;
        color: #333;
        font-size: 0.95rem;
        margin-bottom: 2px;
    }
    
    .dropdown-menu-modern .dropdown-item small {
        color: #666;
        font-size: 0.8rem;
    }
    
    .btn-theme-toggle {
        background: rgba(255,255,255,0.15);
        border: 2px solid rgba(255,255,255,0.3);
        width: 40px;
        height: 40px;
        border-radius: 50%;
        color: white;
        cursor: pointer;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .btn-theme-toggle:hover {
        background: rgba(255,255,255,0.25);
        transform: rotate(15deg) scale(1.1);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    
    .btn-logout-modern {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white !important;
        border: none;
        padding: 10px 24px;
        border-radius: 25px;
        font-weight: 600;
        box-shadow: 0 4px 15px rgba(245, 87, 108, 0.3);
        transition: all 0.3s;
        margin-left: 8px;
    }
    
    .btn-logout-modern:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(245, 87, 108, 0.5);
        color: white !important;
    }
    
    .dropdown-divider {
        margin: 8px 0;
        border-top-color: #e0e0e0;
    }
    
    @media (max-width: 991px) {
        .company-navbar {
            padding: 1rem 0;
        }
        
        .nav-link-modern {
            margin: 4px 0;
        }
        
        .btn-logout-modern {
            margin: 10px 0;
            display: inline-block;
        }
        
        .btn-theme-toggle {
            margin: 10px 0;
        }
    }
</style>

<script>
    function toggleTheme() {
        const body = document.body;
        const themeIcon = document.getElementById('themeIcon');
        
        body.classList.toggle('dark-theme');
        
        if (body.classList.contains('dark-theme')) {
            themeIcon.classList.remove('fa-moon');
            themeIcon.classList.add('fa-sun');
            localStorage.setItem('companyTheme', 'dark');
        } else {
            themeIcon.classList.remove('fa-sun');
            themeIcon.classList.add('fa-moon');
            localStorage.setItem('companyTheme', 'light');
        }
    }
    
    // Load saved theme
    document.addEventListener('DOMContentLoaded', function() {
        const savedTheme = localStorage.getItem('companyTheme');
        if (savedTheme === 'dark') {
            document.body.classList.add('dark-theme');
            document.getElementById('themeIcon').classList.replace('fa-moon', 'fa-sun');
        }
    });
</script>
