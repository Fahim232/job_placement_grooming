<?php 
    session_start();
    include 'admin/dbcon.php';
    include('header.php');
    
    // Get job ID
    if (!isset($_GET['id'])) {
        header('location: browse_jobs.php');
        exit();
    }
    
    $job_id = mysqli_real_escape_string($con, $_GET['id']);
    
    // Fetch job details (alias company_website -> website for compatibility)
    $job_query = "SELECT cj.*, c.company_name, c.industry, c.company_size, c.company_website AS website, c.description as company_desc, c.logo,
                   (SELECT COUNT(*) FROM company_job_questions WHERE job_id = cj.id) as quiz_count,
                   (SELECT COUNT(*) FROM job_applications WHERE job_id = cj.id) as applicant_count
                   FROM company_jobs cj
                   JOIN companies c ON cj.company_id = c.id
                   WHERE cj.id = '$job_id' AND cj.status = 'active'";
    $job_result = mysqli_query($con, $job_query);
    
    if (mysqli_num_rows($job_result) == 0) {
        echo "<script>alert('Job not found or no longer available'); window.location.href='browse_jobs.php';</script>";
        exit();
    }
    
    $job = mysqli_fetch_assoc($job_result);
    
    // Check if user is logged in
    $is_logged_in = isset($_SESSION['id']);
    $has_applied = false;
    $application_status = null;
    $quiz_status = null;
    
    if ($is_logged_in) {
        $user_id = $_SESSION['id'];

        // Application status
        $check_query = "SELECT * FROM job_applications WHERE user_id = '$user_id' AND job_id = '$job_id'";
        $check_result = mysqli_query($con, $check_query);
        if (mysqli_num_rows($check_result) > 0) {
            $has_applied = true;
            $app_data = mysqli_fetch_assoc($check_result);
            $application_status = $app_data['application_status'];
        }

        // Latest quiz status: prefer job_applications if exists, otherwise derive from last attempt score
        $quiz_status = null;
        if ($has_applied && isset($app_data['quiz_status'])) {
            $quiz_status = $app_data['quiz_status'];
        } else {
            $quiz_query = "SELECT score_percentage FROM job_quiz_attempts WHERE user_id = '$user_id' AND job_id = '$job_id' ORDER BY attempt_date DESC LIMIT 1";
            $quiz_result = mysqli_query($con, $quiz_query);
            if (mysqli_num_rows($quiz_result) > 0) {
                $quiz_row = mysqli_fetch_assoc($quiz_result);
                $quiz_status = ($quiz_row['score_percentage'] >= 50) ? 'passed' : 'failed';
            }
        }
    }
    
    // Check if deadline has passed
    $deadline_passed = strtotime($job['deadline']) < time();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php echo $job['job_title']; ?> | Job Portal</title>
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .job-details-container {
            max-width: 1200px;
            margin: 100px auto 50px;
            padding: 0 20px;
        }
        
        .job-header {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .job-title {
            font-size: 32px;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 10px;
        }
        
        .company-info {
            display: flex;
            align-items: center;
            gap: 15px;
            margin: 20px 0;
        }
        
        .company-badge {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 8px 20px;
            border-radius: 50px;
            font-weight: 600;
        }
        
        .job-meta-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }
        
        .meta-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 15px;
            background: #f7fafc;
            border-radius: 12px;
        }
        
        .meta-item i {
            color: #667eea;
            font-size: 20px;
        }
        
        .meta-label {
            font-size: 12px;
            color: #718096;
            display: block;
        }
        
        .meta-value {
            font-size: 16px;
            font-weight: 600;
            color: #2d3748;
            display: block;
        }
        
        .content-section {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .section-title {
            font-size: 24px;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid #667eea;
        }
        
        .job-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin: 20px 0;
        }
        
        .job-tag {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 8px 18px;
            border-radius: 25px;
            font-size: 14px;
            font-weight: 500;
        }
        
        .requirements-list {
            list-style: none;
            padding: 0;
        }
        
        .requirements-list li {
            padding: 12px 0;
            padding-left: 35px;
            position: relative;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .requirements-list li:before {
            content: "✓";
            position: absolute;
            left: 0;
            top: 12px;
            width: 25px;
            height: 25px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
        
        .apply-section {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .apply-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 18px 50px;
            border-radius: 50px;
            border: none;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
            text-decoration: none;
            display: inline-block;
        }
        
        .apply-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(102, 126, 234, 0.4);
            color: white;
        }
        
        .apply-btn:disabled {
            background: #cbd5e0;
            cursor: not-allowed;
            box-shadow: none;
        }
        
        .apply-btn:disabled:hover {
            transform: none;
        }
        
        .quiz-info {
            background: #fff5f5;
            border: 2px dashed #fc8181;
            border-radius: 15px;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
        }
        
        .quiz-info i {
            font-size: 40px;
            color: #fc8181;
            margin-bottom: 10px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 10px 25px;
            border-radius: 25px;
            font-weight: 600;
            margin: 20px 0;
        }
        
        .status-pending {
            background: #fef5e7;
            color: #f39c12;
        }
        
        .status-reviewed {
            background: #ebf5fb;
            color: #3498db;
        }
        
        .status-shortlisted {
            background: #e8f8f5;
            color: #1abc9c;
        }
        
        .status-rejected {
            background: #fadbd8;
            color: #e74c3c;
        }
        
        .company-section {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 30px;
            align-items: start;
        }
        
        .company-card {
            background: #f7fafc;
            border-radius: 15px;
            padding: 25px;
            text-align: center;
        }
        
        .company-card h3 {
            color: #2d3748;
            margin: 15px 0 10px;
        }
        
        .company-details p {
            margin: 10px 0;
            color: #4a5568;
        }
        
        @media (max-width: 768px) {
            .job-meta-grid {
                grid-template-columns: 1fr;
            }
            
            .company-section {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="job-details-container">
        <!-- Job Header -->
        <div class="job-header">
            <div class="d-flex justify-content-between align-items-start">
                <div class="flex-grow-1">
                    <?php if (!empty($job['logo']) && file_exists($job['logo'])): ?>
                        <div class="mb-3">
                            <img src="<?php echo $job['logo']; ?>" alt="<?php echo $job['company_name']; ?>" 
                                 style="max-width: 150px; max-height: 60px; object-fit: contain; border-radius: 8px; border: 1px solid #e0e0e0; padding: 8px; background: white;">
                        </div>
                    <?php endif; ?>
                    <h1 class="job-title"><?php echo $job['job_title']; ?></h1>
                    <div class="company-info">
                        <span class="company-badge"><?php echo $job['company_name']; ?></span>
                        <span class="badge badge-secondary"><?php echo $job['industry']; ?></span>
                        <span class="badge badge-info"><?php echo $job['job_category']; ?></span>
                    </div>
                </div>
                <div>
                    <?php if ($has_applied): ?>
                        <span class="status-badge status-<?php echo $application_status; ?>">
                            <i class="fas fa-check-circle mr-2"></i>Applied - <?php echo ucfirst($application_status); ?>
                        </span>
                    <?php elseif ($deadline_passed): ?>
                        <span class="status-badge status-rejected">
                            <i class="fas fa-times-circle mr-2"></i>Deadline Passed
                        </span>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="job-meta-grid">
                <div class="meta-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <div>
                        <span class="meta-label">Location</span>
                        <span class="meta-value"><?php echo $job['location']; ?></span>
                    </div>
                </div>
                
                <div class="meta-item">
                    <i class="fas fa-briefcase"></i>
                    <div>
                        <span class="meta-label">Job Type</span>
                        <span class="meta-value"><?php echo $job['employment_type']; ?></span>
                    </div>
                </div>
                
                <div class="meta-item">
                    <i class="fas fa-clock"></i>
                    <div>
                        <span class="meta-label">Experience</span>
                        <span class="meta-value"><?php echo $job['experience_required']; ?></span>
                    </div>
                </div>
                
                <?php if ($job['salary_range']): ?>
                <div class="meta-item">
                    <i class="fas fa-dollar-sign"></i>
                    <div>
                        <span class="meta-label">Salary</span>
                        <span class="meta-value"><?php echo $job['salary_range']; ?></span>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="meta-item">
                    <i class="fas fa-calendar"></i>
                    <div>
                        <span class="meta-label">Posted On</span>
                        <span class="meta-value"><?php echo date('M d, Y', strtotime($job['posted_date'])); ?></span>
                    </div>
                </div>
                
                <div class="meta-item">
                    <i class="fas fa-calendar-times"></i>
                    <div>
                        <span class="meta-label">Deadline</span>
                        <span class="meta-value"><?php echo date('M d, Y', strtotime($job['deadline'])); ?></span>
                    </div>
                </div>
                
                <div class="meta-item">
                    <i class="fas fa-users"></i>
                    <div>
                        <span class="meta-label">Applicants</span>
                        <span class="meta-value"><?php echo $job['applicant_count']; ?></span>
                    </div>
                </div>
                
                <div class="meta-item">
                    <i class="fas fa-users-cog"></i>
                    <div>
                        <span class="meta-label">Vacancies</span>
                        <span class="meta-value"><?php echo $job['vacancy_count']; ?></span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Job Description -->
        <div class="content-section">
            <h2 class="section-title"><i class="fas fa-file-alt mr-2"></i>Job Description</h2>
            <p style="line-height: 1.8; color: #4a5568;"><?php echo nl2br($job['job_description']); ?></p>
        </div>
        
        <!-- Responsibilities -->
        <?php if ($job['responsibilities']): ?>
        <div class="content-section">
            <h2 class="section-title"><i class="fas fa-tasks mr-2"></i>Key Responsibilities</h2>
            <ul class="requirements-list">
                <?php 
                    $responsibilities = explode("\n", $job['responsibilities']);
                    foreach ($responsibilities as $resp) {
                        if (trim($resp)) {
                            echo '<li>' . trim($resp) . '</li>';
                        }
                    }
                ?>
            </ul>
        </div>
        <?php endif; ?>
        
        <!-- Requirements -->
        <?php if ($job['requirements']): ?>
        <div class="content-section">
            <h2 class="section-title"><i class="fas fa-check-circle mr-2"></i>Requirements</h2>
            <ul class="requirements-list">
                <?php 
                    $requirements = explode("\n", $job['requirements']);
                    foreach ($requirements as $req) {
                        if (trim($req)) {
                            echo '<li>' . trim($req) . '</li>';
                        }
                    }
                ?>
            </ul>
        </div>
        <?php endif; ?>
        
        <!-- Skills Required -->
        <div class="content-section">
            <h2 class="section-title"><i class="fas fa-code mr-2"></i>Required Skills</h2>
            <div class="job-tags">
                <?php 
                    $skills = explode(',', $job['skills_required']);
                    foreach ($skills as $skill) {
                        echo '<span class="job-tag">' . trim($skill) . '</span>';
                    }
                ?>
            </div>
        </div>
        
        <!-- Company Information -->
        <div class="content-section">
            <h2 class="section-title"><i class="fas fa-building mr-2"></i>About <?php echo $job['company_name']; ?></h2>
            <div class="company-section">
                <div class="company-card">
                    <?php if (!empty($job['logo']) && file_exists($job['logo'])): ?>
                        <img src="<?php echo $job['logo']; ?>" alt="<?php echo $job['company_name']; ?>" 
                             style="max-width: 120px; max-height: 80px; object-fit: contain; margin-bottom: 15px; border-radius: 8px; border: 1px solid #e0e0e0; padding: 10px; background: white;">
                    <?php else: ?>
                        <i class="fas fa-building" style="font-size: 50px; color: #667eea;"></i>
                    <?php endif; ?>
                    <h3><?php echo $job['company_name']; ?></h3>
                    <p><i class="fas fa-industry mr-2"></i><?php echo $job['industry']; ?></p>
                    <p><i class="fas fa-users mr-2"></i><?php echo $job['company_size']; ?> Employees</p>
                    <?php if ($job['website']): ?>
                        <a href="<?php echo $job['website']; ?>" target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                            <i class="fas fa-globe mr-2"></i>Visit Website
                        </a>
                    <?php endif; ?>
                </div>
                <div class="company-details">
                    <p style="line-height: 1.8; color: #4a5568;"><?php echo nl2br($job['company_desc']); ?></p>
                </div>
            </div>
        </div>
        
        <!-- Apply Section -->
        <div class="apply-section">
            <?php if (!$is_logged_in): ?>
                <i class="fas fa-user-lock" style="font-size: 50px; color: #cbd5e0; margin-bottom: 20px;"></i>
                <h3 style="color: #2d3748; margin-bottom: 15px;">Login Required</h3>
                <p style="color: #718096; margin-bottom: 30px;">You need to login to continue</p>
                <a href="login.php?redirect=job_details.php?id=<?php echo $job_id; ?>" class="apply-btn">
                    <i class="fas fa-sign-in-alt mr-2"></i>Login
                </a>
                <p class="mt-3">
                    Don't have an account? <a href="registration.php" style="color: #667eea; font-weight: 600;">Register here</a>
                </p>
            <?php elseif ($has_applied): ?>
                <i class="fas fa-check-circle" style="font-size: 50px; color: #48bb78; margin-bottom: 20px;"></i>
                <h3 style="color: #2d3748; margin-bottom: 15px;">Application Submitted</h3>
                <p style="color: #718096; margin-bottom: 20px;">You have already applied for this position</p>
                <p style="color: #4a5568;"><strong>Status:</strong> <span class="status-badge status-<?php echo $application_status; ?>"><?php echo ucfirst($application_status); ?></span></p>
                <a href="my_application.php" class="btn btn-outline-primary mt-3">
                    <i class="fas fa-list mr-2"></i>View My Applications
                </a>
            <?php elseif ($deadline_passed): ?>
                <i class="fas fa-exclamation-circle" style="font-size: 50px; color: #f56565; margin-bottom: 20px;"></i>
                <h3 style="color: #2d3748; margin-bottom: 15px;">Application Deadline Passed</h3>
                <p style="color: #718096; margin-bottom: 30px;">Applications for this position are no longer being accepted</p>
                <a href="browse_jobs.php" class="btn btn-outline-primary">
                    <i class="fas fa-search mr-2"></i>Browse Other Jobs
                </a>
            <?php else: ?>
                <?php if ($job['quiz_count'] > 0): ?>
                    <div class="quiz-info">
                        <i class="fas fa-question-circle"></i>
                        <h4 style="color: #c53030; margin: 10px 0;">Assessment Required</h4>
                        <p style="color: #742a2a; margin: 0;">You must pass the assessment before applying and uploading your CV.</p>
                    </div>
                    
                    <?php if ($quiz_status === 'passed'): ?>
                        <i class="fas fa-paper-plane" style="font-size: 50px; color: #667eea; margin-bottom: 20px;"></i>
                        <h3 style="color: #2d3748; margin-bottom: 15px;">Great! You passed.</h3>
                        <p style="color: #718096; margin-bottom: 30px;">You can now submit your application and upload your CV.</p>
                        <a href="company_job_application.php?job_id=<?php echo $job_id; ?>" class="apply-btn">
                            <i class="fas fa-paper-plane mr-2"></i>Apply Now
                        </a>
                    <?php elseif ($quiz_status === 'failed'): ?>
                        <i class="fas fa-exclamation-triangle" style="font-size: 50px; color: #f6ad55; margin-bottom: 20px;"></i>
                        <h3 style="color: #c05621; margin-bottom: 15px;">Quiz not passed</h3>
                        <p style="color: #744210; margin-bottom: 30px;">Please complete the grooming session before reapplying.</p>
                        <a href="grooming.php?category=<?php echo urlencode($job['job_category']); ?>" class="btn btn-outline-light" style="border-color: #f6ad55; color: #f6ad55;">
                            <i class="fas fa-chalkboard-teacher mr-2"></i>Go to Grooming
                        </a>
                    <?php else: ?>
                        <i class="fas fa-clipboard-check" style="font-size: 50px; color: #4299e1; margin-bottom: 20px;"></i>
                        <h3 style="color: #2b6cb0; margin-bottom: 15px;">Take the Assessment</h3>
                        <p style="color: #2c5282; margin-bottom: 30px;">Complete the quiz to unlock the application form.</p>
                        <a href="company_job_quiz.php?job_id=<?php echo $job_id; ?>" class="apply-btn">
                            <i class="fas fa-play mr-2"></i>Start Quiz
                        </a>
                    <?php endif; ?>
                <?php else: ?>
                    <i class="fas fa-paper-plane" style="font-size: 50px; color: #667eea; margin-bottom: 20px;"></i>
                    <h3 style="color: #2d3748; margin-bottom: 15px;">Ready to Apply?</h3>
                    <p style="color: #718096; margin-bottom: 30px;">Submit your application now and take the first step towards your new career</p>
                    <a href="company_job_application.php?job_id=<?php echo $job_id; ?>" class="apply-btn">
                        <i class="fas fa-paper-plane mr-2"></i>Apply Now
                    </a>
                    <p class="mt-3" style="color: #718096; font-size: 14px;">
                        <i class="fas fa-info-circle mr-2"></i>You will need to upload your CV.
                    </p>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <footer class="text-center py-4 mt-5" style="background: rgba(255,255,255,0.1); color: white;">
        <p class="mb-0">&copy; 2026 Job Application Portal. All rights reserved.</p>
    </footer>
</body>
</html>
