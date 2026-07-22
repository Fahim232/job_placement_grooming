<?php 
    session_start();
    if (!isset($_SESSION['id'])) {
        header('location: login.php');
        exit();
    }
    
    include 'admin/dbcon.php';
    include 'header.php';
    
    // Get job ID
    if (!isset($_GET['job_id'])) {
        header('location: browse_jobs.php');
        exit();
    }
    
    $job_id = mysqli_real_escape_string($con, $_GET['job_id']);
    $user_id = $_SESSION['id'];
    
    // Fetch job details
    $job_query = "SELECT cj.*, c.company_name, c.industry,
                   (SELECT COUNT(*) FROM company_job_questions WHERE job_id = cj.id) as quiz_count
                   FROM company_jobs cj
                   JOIN companies c ON cj.company_id = c.id
                   WHERE cj.id = '$job_id' AND cj.status = 'active'";
    $job_result = mysqli_query($con, $job_query);
    
    if (mysqli_num_rows($job_result) == 0) {
        echo "<script>alert('Job not found'); window.location.href='browse_jobs.php';</script>";
        exit();
    }
    
    $job = mysqli_fetch_assoc($job_result);
    
    // Check if already applied and completed application (has cover letter)
    $check_query = "SELECT * FROM job_applications WHERE user_id = '$user_id' AND job_id = '$job_id' AND (cover_letter IS NOT NULL AND cover_letter != '')";
    $check_result = mysqli_query($con, $check_query);
    if (mysqli_num_rows($check_result) > 0) {
        echo "<script>alert('You have already submitted your application'); window.location.href='my_application.php';</script>";
        exit();
    }

    // Enforce quiz before application when quiz exists (derive status from score)
    if ($job['quiz_count'] > 0) {
        $quiz_check = "SELECT score_percentage FROM job_quiz_attempts WHERE user_id = '$user_id' AND job_id = '$job_id' ORDER BY attempt_date DESC LIMIT 1";
        $quiz_res = mysqli_query($con, $quiz_check);
        if (mysqli_num_rows($quiz_res) == 0) {
            echo "<script>alert('Please complete the quiz before applying'); window.location.href='company_job_quiz.php?job_id=$job_id';</script>";
            exit();
        }
        $quiz_row = mysqli_fetch_assoc($quiz_res);
        $quiz_status = ($quiz_row['score_percentage'] >= 50) ? 'passed' : 'failed';
        if ($quiz_status !== 'passed') {
            $category = urlencode($job['job_category']);
            echo "<script>alert('You must pass the quiz before submitting your CV. Redirecting to grooming.'); window.location.href='grooming.php?category=$category';</script>";
            exit();
        }
    }
    
    // Check if deadline has passed
    if (strtotime($job['deadline']) < time()) {
        echo "<script>alert('Application deadline has passed'); window.location.href='job_details.php?id=$job_id';</script>";
        exit();
    }
    
    // Fetch user info
    $user_query = "SELECT * FROM user_info WHERE id = '$user_id'";
    $user_result = mysqli_query($con, $user_query);
    $user_data = mysqli_fetch_assoc($user_result);
    
    $submission_status = null;
    $error_message = '';
    
    // Handle form submission
    if (isset($_POST['submit_application'])) {
        $cover_letter = mysqli_real_escape_string($con, $_POST['cover_letter']);
        
        // Handle CV upload
        $cv_path = '';
        if (isset($_FILES['cv']) && $_FILES['cv']['error'] == 0) {
            $target_dir = "files/";
            $file_extension = pathinfo($_FILES["cv"]["name"], PATHINFO_EXTENSION);
            $new_filename = "cv_" . $user_id . "_job_" . $job_id . "_" . time() . "." . $file_extension;
            $target_file = $target_dir . $new_filename;
            
            $allowed_extensions = array('pdf', 'doc', 'docx');
            if (!in_array(strtolower($file_extension), $allowed_extensions)) {
                $error_message = "Only PDF, DOC, and DOCX files are allowed";
            } elseif ($_FILES["cv"]["size"] > 5000000) { // 5MB limit
                $error_message = "File size must be less than 5MB";
            } else {
                if (move_uploaded_file($_FILES["cv"]["tmp_name"], $target_file)) {
                    $cv_path = $target_file;
                } else {
                    $error_message = "Failed to upload CV";
                }
            }
        } else {
            // Check if user has existing CV
            if (!empty($user_data['cv_path'])) {
                $cv_path = $user_data['cv_path'];
            } else {
                $error_message = "Please upload your CV";
            }
        }
        
        if (empty($error_message)) {
            // Check if application record exists and update, or insert new
            $app_exists_query = "SELECT id FROM job_applications WHERE user_id = '$user_id' AND job_id = '$job_id'";
            $app_exists_result = mysqli_query($con, $app_exists_query);
            
            if (mysqli_num_rows($app_exists_result) > 0) {
                // Update existing application with cover letter
                $existing_app = mysqli_fetch_assoc($app_exists_result);
                $update_query = "UPDATE job_applications 
                                SET cover_letter = '$cover_letter', application_status = 'pending'
                                WHERE id = '{$existing_app['id']}'";
                if (mysqli_query($con, $update_query)) {
                    echo "<script>alert('Application submitted successfully!'); window.location.href='my_application.php';</script>";
                    exit();
                } else {
                    $error_message = "Failed to submit application. Please try again.";
                }
            } else {
                // Insert new application (should not happen if quiz was taken first)
                $get_company = "SELECT company_id FROM company_jobs WHERE id = '$job_id'";
                $get_company_res = mysqli_query($con, $get_company);
                $comp_row = mysqli_fetch_assoc($get_company_res);
                $company_id = $comp_row['company_id'];
                
                $insert_query = "INSERT INTO job_applications 
                                (user_id, job_id, company_id, cover_letter, applied_date, application_status) 
                                VALUES 
                                ('$user_id', '$job_id', '$company_id', '$cover_letter', NOW(), 'pending')";
                
                if (mysqli_query($con, $insert_query)) {
                    echo "<script>alert('Application submitted successfully!'); window.location.href='my_application.php';</script>";
                    exit();
                } else {
                    $error_message = "Failed to submit application. Please try again.";
                }
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Apply for <?php echo $job['job_title']; ?> | Job Portal</title>
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .application-container {
            max-width: 900px;
            margin: 100px auto 50px;
            padding: 0 20px;
        }
        
        .application-card {
            background: white;
            border-radius: 20px;
            padding: 50px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }
        
        .page-title {
            font-size: 32px;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 10px;
            text-align: center;
        }
        
        .job-info-banner {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 40px;
        }
        
        .job-info-banner h3 {
            margin: 0 0 10px 0;
            font-size: 24px;
        }
        
        .job-info-banner p {
            margin: 5px 0;
            opacity: 0.9;
        }
        
        .form-section {
            margin-bottom: 35px;
        }
        
        .section-title {
            font-size: 20px;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e2e8f0;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-group label {
            display: block;
            font-weight: 600;
            color: #4a5568;
            margin-bottom: 10px;
            font-size: 15px;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 15px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #667eea;
            outline: none;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        textarea.form-control {
            min-height: 150px;
            resize: vertical;
        }
        
        .file-upload-wrapper {
            position: relative;
            display: block;
            width: 100%;
        }
        
        .file-upload-input {
            display: none;
        }
        
        .file-upload-label {
            display: block;
            padding: 20px;
            background: #f7fafc;
            border: 2px dashed #cbd5e0;
            border-radius: 10px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .file-upload-label:hover {
            background: #edf2f7;
            border-color: #667eea;
        }
        
        .file-upload-label i {
            font-size: 40px;
            color: #667eea;
            margin-bottom: 10px;
        }
        
        .file-name {
            margin-top: 10px;
            color: #4a5568;
            font-weight: 500;
        }
        
        .info-box {
            background: #ebf8ff;
            border-left: 4px solid #3182ce;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 25px;
        }
        
        .info-box i {
            color: #3182ce;
            margin-right: 10px;
        }
        
        .warning-box {
            background: #fffaf0;
            border-left: 4px solid #ed8936;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 25px;
        }
        
        .warning-box i {
            color: #ed8936;
            margin-right: 10px;
        }
        
        .error-message {
            background: #fff5f5;
            border: 1px solid #fc8181;
            color: #c53030;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .btn-submit {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 50px;
            border-radius: 50px;
            border: none;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
            width: 100%;
        }
        
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 40px rgba(102, 126, 234, 0.4);
        }
        
        .btn-back {
            display: inline-block;
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            margin-bottom: 20px;
        }
        
        .btn-back:hover {
            text-decoration: underline;
        }
        
        .user-info-display {
            background: #f7fafc;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 25px;
        }
        
        .user-info-item {
            display: flex;
            padding: 10px 0;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .user-info-item:last-child {
            border-bottom: none;
        }
        
        .user-info-label {
            font-weight: 600;
            color: #4a5568;
            width: 150px;
        }
        
        .user-info-value {
            color: #2d3748;
        }
    </style>
</head>
<body>
    <div class="application-container">
        <div class="application-card">
            <a href="job_details.php?id=<?php echo $job_id; ?>" class="btn-back">
                <i class="fas fa-arrow-left mr-2"></i>Back to Job Details
            </a>
            
            <h1 class="page-title"><i class="fas fa-paper-plane mr-3"></i>Submit Your Application</h1>
            
            <div class="job-info-banner">
                <h3><i class="fas fa-briefcase mr-2"></i><?php echo $job['job_title']; ?></h3>
                <p><i class="fas fa-building mr-2"></i><?php echo $job['company_name']; ?> • <?php echo $job['industry']; ?></p>
                <p><i class="fas fa-map-marker-alt mr-2"></i><?php echo $job['location']; ?> • <?php echo $job['employment_type']; ?></p>
            </div>
            
            <?php if (!empty($error_message)): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle mr-2"></i><?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($job['quiz_count'] > 0): ?>
                <div class="warning-box">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Assessment Required:</strong> After submitting your application, you will be required to complete a <?php echo $job['quiz_count']; ?>-question assessment quiz.
                </div>
            <?php endif; ?>
            
            <form method="POST" enctype="multipart/form-data">
                <!-- Your Information -->
                <div class="form-section">
                    <h3 class="section-title"><i class="fas fa-user mr-2"></i>Your Information</h3>
                    
                    <div class="user-info-display">
                        <div class="user-info-item">
                            <span class="user-info-label">Full Name:</span>
                            <span class="user-info-value"><?php echo $user_data['username']; ?></span>
                        </div>
                        <div class="user-info-item">
                            <span class="user-info-label">Email:</span>
                            <span class="user-info-value"><?php echo $user_data['email']; ?></span>
                        </div>
                        <div class="user-info-item">
                            <span class="user-info-label">Phone:</span>
                            <span class="user-info-value"><?php echo $user_data['phone']; ?></span>
                        </div>
                        <?php if ($user_data['user_degree']): ?>
                        <div class="user-info-item">
                            <span class="user-info-label">Education:</span>
                            <span class="user-info-value"><?php echo $user_data['user_degree']; ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if ($user_data['user_skills']): ?>
                        <div class="user-info-item">
                            <span class="user-info-label">Skills:</span>
                            <span class="user-info-value"><?php echo $user_data['user_skills']; ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="info-box">
                        <i class="fas fa-info-circle"></i>
                        To update your profile information, please visit your <a href="profile.php" style="color: #3182ce; font-weight: 600;">profile page</a>.
                    </div>
                </div>
                
                <!-- Upload CV -->
                <div class="form-section">
                    <h3 class="section-title"><i class="fas fa-file-alt mr-2"></i>Upload Your CV/Resume</h3>
                    
                    <?php if (!empty($user_data['cv_path'])): ?>
                        <div class="info-box">
                            <i class="fas fa-check-circle"></i>
                            You have an existing CV on file. You can submit with your existing CV or upload a new one.
                            <a href="<?php echo $user_data['cv_path']; ?>" target="_blank" class="ml-3" style="color: #3182ce; font-weight: 600;">
                                <i class="fas fa-eye mr-1"></i>View Current CV
                            </a>
                        </div>
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <div class="file-upload-wrapper">
                            <input type="file" name="cv" id="cv" class="file-upload-input" accept=".pdf,.doc,.docx">
                            <label for="cv" class="file-upload-label">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <div style="color: #2d3748; font-weight: 600; margin-top: 10px;">
                                    <?php if (!empty($user_data['cv_path'])): ?>
                                        Upload New CV (Optional)
                                    <?php else: ?>
                                        Click to Upload CV (Required)
                                    <?php endif; ?>
                                </div>
                                <div style="color: #718096; font-size: 14px; margin-top: 5px;">
                                    Supported formats: PDF, DOC, DOCX (Max 5MB)
                                </div>
                                <div class="file-name" id="fileName"></div>
                            </label>
                        </div>
                    </div>
                </div>
                
                <!-- Cover Letter -->
                <div class="form-section">
                    <h3 class="section-title"><i class="fas fa-edit mr-2"></i>Cover Letter</h3>
                    
                    <div class="form-group">
                        <label for="cover_letter">
                            Tell us why you're a great fit for this position *
                        </label>
                        <textarea 
                            name="cover_letter" 
                            id="cover_letter" 
                            class="form-control" 
                            required
                            placeholder="Explain why you're interested in this position and what makes you a good candidate..."
                        ></textarea>
                    </div>
                </div>
                
                <!-- Submit -->
                <div class="form-section">
                    <button type="submit" name="submit_application" class="btn-submit">
                        <i class="fas fa-paper-plane mr-2"></i>Submit Application
                        <?php if ($job['quiz_count'] > 0): ?>
                            & Take Quiz
                        <?php endif; ?>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <footer class="text-center py-4 mt-5" style="background: rgba(255,255,255,0.1); color: white;">
        <p class="mb-0">&copy; 2026 Job Application Portal. All rights reserved.</p>
    </footer>
    
    <script>
        // File upload display
        document.getElementById('cv').addEventListener('change', function(e) {
            const fileName = e.target.files[0] ? e.target.files[0].name : '';
            document.getElementById('fileName').textContent = fileName ? 'Selected: ' + fileName : '';
        });
    </script>
</body>
</html>
