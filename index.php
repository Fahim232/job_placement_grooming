<?php
    session_start();
    include '../admin/dbcon.php';
    
    // Check if company is logged in
    if (!isset($_SESSION['company_id'])) {
        header('Location: ../company_login.php');
        exit;
    }
    
    $company_id = $_SESSION['company_id'];
    $company_name = $_SESSION['company_name'];
    
    // Fetch company statistics
    $stats_query = "SELECT 
        (SELECT COUNT(*) FROM company_jobs WHERE company_id = $company_id) as total_jobs,
        (SELECT COUNT(*) FROM company_jobs WHERE company_id = $company_id AND status = 'active') as active_jobs,
        (SELECT COUNT(*) FROM job_applications WHERE company_id = $company_id) as total_applications,
        (SELECT COUNT(*) FROM job_applications WHERE company_id = $company_id AND quiz_status = 'passed') as qualified_candidates";
    $stats_result = mysqli_query($con, $stats_query);
    $stats = mysqli_fetch_assoc($stats_result);
    
    // Fetch recent jobs
    $jobs_query = "SELECT * FROM company_jobs WHERE company_id = $company_id ORDER BY posted_date DESC LIMIT 5";
    $jobs_result = mysqli_query($con, $jobs_query);
    
    // Fetch recent applications
    $applications_query = "SELECT ja.*, cj.job_title, ui.username, ui.email, ui.phone 
                          FROM job_applications ja
                          JOIN company_jobs cj ON ja.job_id = cj.id
                          JOIN user_info ui ON ja.user_id = ui.id
                          WHERE ja.company_id = $company_id
                          ORDER BY ja.applied_date DESC LIMIT 10";
    $applications_result = mysqli_query($con, $applications_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Company Dashboard | Job Portal</title>
    <?php include '../links.php'; ?>
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }

        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
        }

        body {
            padding-top: 70px;
        }

        .navbar-brand {
            color: white !important;
            font-weight: 700;
            font-size: 1.5rem;
        }

        .nav-link {
            color: rgba(255,255,255,0.9) !important;
            font-weight: 500;
            margin: 0 10px;
            transition: all 0.3s;
        }

        .nav-link:hover {
            color: white !important;
            transform: translateY(-2px);
        }

        .btn-logout {
            background: var(--glass-bg);
            color: var(--text-primary) !important;
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 8px 25px;
            font-weight: 600;
            box-shadow: 0 8px 20px var(--glass-shadow);
            transition: all 0.25s ease;
        }

        .btn-logout:hover {
            transform: translateY(-1px);
            box-shadow: 0 12px 25px var(--glass-shadow);
            background: rgba(255,255,255,0.85);
            color: #111827 !important;
        }

        .dashboard-header {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            color: var(--text-primary);
            border-radius: 15px;
            padding: 30px;
            margin: 30px 0;
            box-shadow: 0 10px 30px var(--glass-shadow);
            backdrop-filter: blur(16px);
        }

        .stat-card {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 30px var(--glass-shadow);
            transition: all 0.3s;
            border-left: 5px solid;
            color: var(--text-primary);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }

        .stat-card.blue {
            border-color: #667eea;
        }

        .stat-card.green {
            border-color: #28a745;
        }

        .stat-card.orange {
            border-color: #fd7e14;
        }

        .stat-card.purple {
            border-color: #764ba2;
        }

        .stat-card h3 {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 10px 0;
            color: var(--text-primary);
        }

        .stat-card p {
            color: var(--text-secondary);
            margin: 0;
            font-size: 1.1rem;
        }

        .stat-card i {
            font-size: 3rem;
            opacity: 0.3;
        }

        .content-section {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px var(--glass-shadow);
            backdrop-filter: blur(16px);
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            color: var(--text-primary);
        }

        .table {
            margin: 0;
        }

        .table thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .badge {
            padding: 8px 15px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .btn-action {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            margin: 2px;
        }

        .job-card {
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            transition: all 0.3s;
        }

        .job-card:hover {
            border-color: #667eea;
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.2);
        }

        .quick-actions {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .quick-action-btn {
            flex: 1;
            min-width: 200px;
            padding: 20px;
            border-radius: 10px;
            text-decoration: none;
            color: white;
            font-weight: 600;
            text-align: center;
            transition: all 0.3s;
        }

        .quick-action-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
            color: white;
            text-decoration: none;
        }

        .quick-action-btn.blue {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .quick-action-btn.green {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }

        .quick-action-btn.orange {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        /* Theme tokens */
        :root {
            --bg-main: linear-gradient(135deg, #eef2ff 0%, #f5f7ff 100%);
            --bg-accent-1: radial-gradient(circle at 10% 20%, rgba(102, 126, 234, 0.18), transparent 25%);
            --bg-accent-2: radial-gradient(circle at 90% 10%, rgba(244, 114, 182, 0.14), transparent 28%);
            --glass-bg: rgba(255, 255, 255, 0.65);
            --glass-border: rgba(255, 255, 255, 0.6);
            --glass-shadow: rgba(102, 126, 234, 0.2);
            --text-primary: #0f172a;
            --text-secondary: rgba(15, 23, 42, 0.75);
            --nav-bg: rgba(255, 255, 255, 0.75);
            --nav-border: rgba(255, 255, 255, 0.6);
            --badge-bg: rgba(255, 255, 255, 0.6);
        }

        [data-theme="dark"] {
            --bg-main: linear-gradient(135deg, #0f172a 0%, #111827 50%, #0b1021 100%);
            --bg-accent-1: radial-gradient(circle at 10% 20%, rgba(102, 126, 234, 0.32), transparent 25%);
            --bg-accent-2: radial-gradient(circle at 90% 10%, rgba(244, 114, 182, 0.26), transparent 28%);
            --glass-bg: rgba(255, 255, 255, 0.08);
            --glass-border: rgba(255, 255, 255, 0.14);
            --glass-shadow: rgba(0, 0, 0, 0.35);
            --text-primary: #e8edff;
            --text-secondary: rgba(232, 237, 255, 0.8);
            --nav-bg: rgba(255, 255, 255, 0.08);
            --nav-border: rgba(255, 255, 255, 0.15);
            --badge-bg: rgba(255, 255, 255, 0.12);
        }

        body {
            padding-top: 70px;
            min-height: 100vh;
            background: var(--bg-accent-1), var(--bg-accent-2), var(--bg-main);
            color: var(--text-primary);
            transition: background 0.4s ease, color 0.3s ease;
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            pointer-events: none;
            background: radial-gradient(circle at 50% 50%, rgba(255, 255, 255, 0.06), transparent 45%);
            z-index: 0;
        }

        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            background: var(--nav-bg);
            border: 1px solid var(--nav-border);
            box-shadow: 0 15px 35px var(--glass-shadow);
            backdrop-filter: blur(16px);
            transition: background 0.3s ease, border 0.3s ease, box-shadow 0.3s ease;
        }

        .navbar-brand, .nav-link {
            color: var(--text-primary) !important;
        }

        .nav-link:hover {
            color: #ffffff !important;
            text-shadow: 0 0 10px rgba(255, 255, 255, 0.35);
        }

        .content-section,
        .stat-card,
        .job-card,
        .quick-action-btn,
        .profile-card,
        .quiz-card,
        .applicant-card,
        .detail-card,
        .card {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            box-shadow: 0 10px 30px var(--glass-shadow);
            backdrop-filter: blur(18px);
            color: var(--text-primary);
            transition: background 0.3s ease, border 0.3s ease, box-shadow 0.3s ease, color 0.2s ease;
        }

        .quick-action-btn {
            color: #f8f9ff !important;
            text-shadow: 0 5px 20px rgba(0, 0, 0, 0.35);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 10px 25px var(--glass-shadow);
        }

        h1, h2, h3, h4, h5, h6 {
            color: var(--text-primary);
        }

        p, label, .text-muted {
            color: var(--text-secondary) !important;
        }

        .form-control {
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: var(--text-primary);
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(255, 255, 255, 0.35);
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            color: #ffffff;
        }

        .badge {
            background: var(--badge-bg);
            border: 1px solid rgba(255, 255, 255, 0.25);
            color: var(--text-primary);
        }

        .theme-toggle {
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 30px;
            padding: 6px 14px;
            background: var(--glass-bg);
            color: var(--text-primary);
            box-shadow: 0 8px 20px var(--glass-shadow);
            backdrop-filter: blur(14px);
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .theme-toggle:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 25px var(--glass-shadow);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php" style="font-size: 1.3rem; font-weight: 600;">
                <?php if (!empty($_SESSION['company_logo']) && file_exists('../' . $_SESSION['company_logo'])): ?>
                    <img src="../ <?php echo $_SESSION['company_logo']; ?>" alt="<?php echo $company_name; ?>" 
                         style="height: 40px; width: auto; object-fit: contain; margin-right: 12px; background: white; padding: 5px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <?php else: ?>
                    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 12px;">
                        <i class="fas fa-building text-white"></i>
                    </div>
                <?php endif; ?>
                <span style="background: linear-gradient(135deg, #fff 0%, #f0f0f0 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"><?php echo $company_name; ?></span>
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto align-items-center">
                    <li class="nav-item active">
                        <a class="nav-link px-3" href="index.php">
                            <i class="fas fa-home mr-2"></i>Dashboard
                        </a>
                    </li>
                    
                    <!-- Jobs Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle px-3" href="#" id="jobsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-briefcase mr-2"></i>Jobs
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow-lg" aria-labelledby="jobsDropdown" style="border-radius: 10px; border: none; margin-top: 8px;">
                            <a class="dropdown-item" href="my_jobs.php">
                                <i class="fas fa-list mr-2 text-primary"></i>My Posted Jobs
                            </a>
                            <a class="dropdown-item" href="post_job.php">
                                <i class="fas fa-plus-circle mr-2 text-success"></i>Post New Job
                            </a>
                        </div>
                    </li>
                    
                    <!-- Applicants Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle px-3" href="#" id="applicantsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-users mr-2"></i>Applicants
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow-lg" aria-labelledby="applicantsDropdown" style="border-radius: 10px; border: none; margin-top: 8px;">
                            <a class="dropdown-item" href="view_applicants.php">
                                <i class="fas fa-file-alt mr-2 text-info"></i>Job Applicants
                            </a>
                            <a class="dropdown-item" href="category_applicants.php">
                                <i class="fas fa-user-graduate mr-2 text-warning"></i>Category Applicants
                            </a>
                        </div>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link px-3" href="profile.php">
                            <i class="fas fa-user-circle mr-2"></i>Profile
                        </a>
                    </li>
                    
                    <li class="nav-item d-flex align-items-center mx-2">
                        <button class="theme-toggle" id="themeToggle" type="button" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); padding: 8px 16px; border-radius: 20px; color: white; cursor: pointer; transition: all 0.3s;">
                            <i class="fas fa-moon mr-1"></i><span id="themeLabel">Dark</span>
                        </button>
                    </li>
                    
                    <li class="nav-item">
                        <a class="btn btn-logout ml-3" href="logout.php" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border: none; padding: 8px 20px; border-radius: 20px; font-weight: 600; box-shadow: 0 4px 15px rgba(245, 87, 108, 0.3); transition: all 0.3s;">
                            <i class="fas fa-sign-out-alt mr-1"></i>Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <style>
        .navbar {
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
            padding: 1rem 0;
        }
        
        .navbar-nav .nav-link {
            font-weight: 500;
            transition: all 0.3s;
            border-radius: 8px;
            position: relative;
        }
        
        .navbar-nav .nav-link:hover {
            background: rgba(255,255,255,0.1);
            transform: translateY(-2px);
        }
        
        .navbar-nav .nav-item.active .nav-link {
            background: rgba(255,255,255,0.15);
            font-weight: 600;
        }
        
        .dropdown-menu {
            animation: slideDown 0.3s ease;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .dropdown-item {
            padding: 12px 20px;
            transition: all 0.3s;
            font-weight: 500;
        }
        
        .dropdown-item:hover {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            transform: translateX(5px);
            padding-left: 25px;
        }
        
        .theme-toggle:hover {
            background: rgba(255,255,255,0.2) !important;
            transform: scale(1.05);
        }
        
        .btn-logout:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(245, 87, 108, 0.4) !important;
        }
    </style>

    <div class="container mt-4">
        <!-- Welcome Header -->
        <div class="dashboard-header">
            <h1><i class="fas fa-chart-line mr-3"></i>Welcome back, <?php echo $company_name; ?>!</h1>
            <p class="text-muted mb-0">Here's an overview of your recruitment activities</p>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="stat-card blue">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p>Total Jobs</p>

                    <script>
                        (function() {
                            const root = document.documentElement;
                            const toggle = document.getElementById('themeToggle');
                            const label = document.getElementById('themeLabel');
                            const stored = localStorage.getItem('company-theme') || 'dark';

                            function apply(theme) {
                                root.setAttribute('data-theme', theme);
                                label.textContent = theme === 'dark' ? 'Dark' : 'Light';
                                toggle.innerHTML = (theme === 'dark' ? '<i class="fas fa-moon mr-1"></i>' : '<i class="fas fa-sun mr-1"></i>') + label.textContent;
                            }

                            apply(stored);

                            toggle.addEventListener('click', () => {
                                const next = root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
                                localStorage.setItem('company-theme', next);
                                apply(next);
                            });
                        })();
                    </script>
                            <h3><?php echo $stats['total_jobs']; ?></h3>
                        </div>
                        <i class="fas fa-briefcase text-primary"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stat-card green">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p>Active Jobs</p>
                            <h3><?php echo $stats['active_jobs']; ?></h3>
                        </div>
                        <i class="fas fa-check-circle text-success"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stat-card orange">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p>Applications</p>
                            <h3><?php echo $stats['total_applications']; ?></h3>
                        </div>
                        <i class="fas fa-file-alt text-warning"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stat-card purple">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p>Qualified</p>
                            <h3><?php echo $stats['qualified_candidates']; ?></h3>
                        </div>
                        <i class="fas fa-user-check text-info"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="content-section">
            <h3 class="section-title"><i class="fas fa-bolt mr-2"></i>Quick Actions</h3>
            <div class="quick-actions">
                <a href="post_job.php" class="quick-action-btn blue">
                    <i class="fas fa-plus-circle fa-2x mb-2"></i><br>
                    Post New Job
                </a>
                <a href="view_applicants.php" class="quick-action-btn green">
                    <i class="fas fa-users fa-2x mb-2"></i><br>
                    View Applicants
                </a>
                <a href="my_jobs.php" class="quick-action-btn orange">
                    <i class="fas fa-list fa-2x mb-2"></i><br>
                    Manage Jobs
                </a>
            </div>
        </div>

        <!-- Recent Jobs -->
        <div class="content-section">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="section-title mb-0"><i class="fas fa-briefcase mr-2"></i>Recent Jobs</h3>
                <a href="my_jobs.php" class="btn btn-primary btn-sm">View All</a>
            </div>
            
            <?php if (mysqli_num_rows($jobs_result) > 0): ?>
                <?php while ($job = mysqli_fetch_assoc($jobs_result)): ?>
                    <div class="job-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h5 class="mb-2"><?php echo $job['job_title']; ?></h5>
                                <p class="text-muted mb-2">
                                    <i class="fas fa-map-marker-alt mr-2"></i><?php echo $job['location']; ?>
                                    <span class="ml-3"><i class="fas fa-briefcase mr-2"></i><?php echo $job['employment_type']; ?></span>
                                    <span class="ml-3"><i class="fas fa-calendar mr-2"></i><?php echo date('M d, Y', strtotime($job['posted_date'])); ?></span>
                                </p>
                            </div>
                            <div>
                                <span class="badge badge-<?php echo $job['status'] == 'active' ? 'success' : 'secondary'; ?>">
                                    <?php echo strtoupper($job['status']); ?>
                                </span>
                            </div>
                        </div>
                        <div class="mt-2">
                            <a href="edit_job.php?id=<?php echo $job['id']; ?>" class="btn btn-sm btn-primary btn-action">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="manage_quiz.php?job_id=<?php echo $job['id']; ?>" class="btn btn-sm btn-info btn-action">
                                <i class="fas fa-question-circle"></i> Quiz
                            </a>
                            <a href="view_applicants.php?job_id=<?php echo $job['id']; ?>" class="btn btn-sm btn-success btn-action">
                                <i class="fas fa-users"></i> Applicants
                            </a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle mr-2"></i>No jobs posted yet. <a href="post_job.php">Post your first job!</a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Recent Applications -->
        <div class="content-section">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="section-title mb-0"><i class="fas fa-file-alt mr-2"></i>Recent Applications</h3>
                <a href="view_applicants.php" class="btn btn-primary btn-sm">View All</a>
            </div>
            
            <?php if (mysqli_num_rows($applications_result) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Applicant</th>
                                <th>Job Title</th>
                                <th>Applied Date</th>
                                <th>Quiz Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($app = mysqli_fetch_assoc($applications_result)): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo $app['username']; ?></strong><br>
                                        <small class="text-muted"><?php echo $app['email']; ?></small>
                                    </td>
                                    <td><?php echo $app['job_title']; ?></td>
                                    <td><?php echo date('M d, Y', strtotime($app['applied_date'])); ?></td>
                                    <td>
                                        <?php if ($app['quiz_status'] == 'passed'): ?>
                                            <span class="badge badge-success">Passed</span>
                                        <?php elseif ($app['quiz_status'] == 'failed'): ?>
                                            <span class="badge badge-danger">Failed</span>
                                        <?php else: ?>
                                            <span class="badge badge-warning">Not Taken</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="view_applicant_detail.php?id=<?php echo $app['id']; ?>" class="btn btn-sm btn-info btn-action">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle mr-2"></i>No applications received yet.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <footer class="text-center py-4 mt-5">
        <p class="text-muted">&copy; 2026 Job Application Portal. All rights reserved.</p>
    </footer>
</body>
</html>
