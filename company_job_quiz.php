<?php
session_start();
if (!isset($_SESSION['id'])) {
    header('location: login.php');
    exit();
}

include 'admin/dbcon.php';
include 'header.php';
if (!isset($_GET['job_id'])) {
    header('location: browse_jobs.php');
    exit();
}

$job_id = mysqli_real_escape_string($con, $_GET['job_id']);
$user_id = $_SESSION['id'];
$application_id = null;
$app_lookup = "SELECT id FROM job_applications WHERE user_id = '$user_id' AND job_id = '$job_id' ORDER BY applied_date DESC LIMIT 1";
$app_lookup_res = mysqli_query($con, $app_lookup);
if ($app_lookup_res && mysqli_num_rows($app_lookup_res) > 0) {
    $app_row = mysqli_fetch_assoc($app_lookup_res);
    $application_id = $app_row['id'];
}

// Fetch job details
$job_query = "SELECT cj.*, c.company_name 
              FROM company_jobs cj
              JOIN companies c ON cj.company_id = c.id
              WHERE cj.id = '$job_id'";
$job_result = mysqli_query($con, $job_query);

if (mysqli_num_rows($job_result) == 0) {
    echo "<script>alert('Job not found'); window.location.href='browse_jobs.php';</script>";
    exit();
}

$job = mysqli_fetch_assoc($job_result);

// NOTE: Quiz is now taken BEFORE application. No need to have an existing application.

// Check if quiz already taken
$quiz_check = "SELECT * FROM job_quiz_attempts WHERE user_id = '$user_id' AND job_id = '$job_id'";
$quiz_check_result = mysqli_query($con, $quiz_check);

if (mysqli_num_rows($quiz_check_result) > 0) {
    $attempt = mysqli_fetch_assoc($quiz_check_result);
    echo "<script>
        alert('You have already completed this quiz.\\nScore: " . $attempt['score_percentage'] . "%\\nStatus: " . ucfirst($attempt['quiz_status']) . "');
        window.location.href='my_application.php';
    </script>";
    exit();
}

// Fetch quiz questions
$questions_query = "SELECT * FROM company_job_questions WHERE job_id = '$job_id' ORDER BY id";
$questions_result = mysqli_query($con, $questions_query);

if (mysqli_num_rows($questions_result) == 0) {
    echo "<script>alert('No quiz questions available for this job'); window.location.href='my_application.php';</script>";
    exit();
}

$total_questions = mysqli_num_rows($questions_result);

// Handle quiz submission
if (isset($_POST['submit_quiz'])) {
    $start_time = $_POST['start_time'];
    $end_time = time();
    $time_taken = $end_time - $start_time;
    
    $score = 0;
    $total = 0;
    
    // Calculate score
    mysqli_data_seek($questions_result, 0);
    while ($question = mysqli_fetch_assoc($questions_result)) {
        $total++;
        $q_id = $question['id'];
        $user_answer = isset($_POST['q_' . $q_id]) ? $_POST['q_' . $q_id] : '';

        // Support both text-based answers and legacy option-key storage
        $correct_value = $question['correct_answer'];
        if (in_array($correct_value, ['option1','option2','option3','option4'])) {
            $correct_value = $question[$correct_value];
        }
        
        if ($user_answer === $correct_value) {
            $score++;
        }
    }
    
    $score_percentage = ($score / $total) * 100;
    $quiz_status = ($score_percentage >= 50) ? 'passed' : 'failed';
    
    // Ensure application record exists (required for application_id NOT NULL constraint)
    if (!$application_id) {
        // Get company_id for the job
        $company_query = "SELECT company_id FROM company_jobs WHERE id = '$job_id'";
        $company_res = mysqli_query($con, $company_query);
        $company_row = mysqli_fetch_assoc($company_res);
        $company_id = $company_row['company_id'];
        
        // Create application with quiz status
        $create_app = "INSERT INTO job_applications (user_id, job_id, company_id, application_status, quiz_status, quiz_score, applied_date) 
                       VALUES ('$user_id', '$job_id', '$company_id', 'pending', '$quiz_status', '$score_percentage', NOW())";
        mysqli_query($con, $create_app);
        $application_id = mysqli_insert_id($con);
    } else {
        // Update existing application with quiz result
        $update_app = "UPDATE job_applications 
                       SET quiz_score = '$score_percentage', quiz_status = '$quiz_status'
                       WHERE id = '$application_id'";
        mysqli_query($con, $update_app);
    }
    
    // Insert quiz attempt with valid application_id
    $insert_attempt = "INSERT INTO job_quiz_attempts 
                       (application_id, job_id, user_id, total_questions, correct_answers, score_percentage, time_taken, attempt_date)
                       VALUES 
                       ('$application_id', '$job_id', '$user_id', '$total', '$score', '$score_percentage', '$time_taken', NOW())";
    mysqli_query($con, $insert_attempt);
    
    // Redirect with results and gate next step
    $pass_fail = ($quiz_status == 'passed') ? 'PASSED' : 'FAILED';
    if ($quiz_status == 'passed') {
        echo "<script>
            alert('Quiz Passed!\\n\\nScore: $score/$total (" . round($score_percentage, 2) . "%)\\nStatus: $pass_fail\\nTime: " . gmdate('i:s', $time_taken) . "');
            window.location.href='company_job_application.php?job_id=$job_id';
        </script>";
    } else {
        $category = urlencode($job['job_category']);
        echo "<script>
            alert('Quiz Failed. Please complete the grooming session before reapplying.');
            window.location.href='grooming.php?category=$category';
        </script>";
    }
    exit();
}

mysqli_data_seek($questions_result, 0);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Assessment Quiz - <?php echo $job['job_title']; ?></title>
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .quiz-container {
            max-width: 900px;
            margin: 100px auto 50px;
            padding: 0 20px;
        }
        
        .quiz-header {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            text-align: center;
        }
        
        .quiz-header h1 {
            color: #2d3748;
            font-size: 28px;
            margin-bottom: 15px;
        }
        
        .quiz-info {
            display: flex;
            justify-content: center;
            gap: 30px;
            flex-wrap: wrap;
            margin-top: 20px;
        }
        
        .quiz-info-item {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #4a5568;
        }
        
        .quiz-info-item i {
            color: #667eea;
            font-size: 20px;
        }
        
        .timer-display {
            position: fixed;
            top: 80px;
            right: 20px;
            background: white;
            padding: 20px 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            z-index: 1000;
        }
        
        .timer-display i {
            color: #667eea;
            font-size: 24px;
            margin-right: 10px;
        }
        
        .timer-time {
            font-size: 24px;
            font-weight: 700;
            color: #2d3748;
        }
        
        .question-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            margin-bottom: 25px;
            transition: all 0.3s ease;
        }
        
        .question-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 50px rgba(0,0,0,0.15);
        }
        
        .question-number {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 8px 20px;
            border-radius: 25px;
            font-weight: 600;
            margin-bottom: 20px;
        }
        
        .question-text {
            font-size: 20px;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 25px;
            line-height: 1.6;
        }
        
        .options-container {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .option-wrapper {
            position: relative;
        }
        
        .option-input {
            display: none;
        }
        
        .option-label {
            display: flex;
            align-items: center;
            padding: 18px 25px;
            background: #f7fafc;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 16px;
            color: #2d3748;
        }
        
        .option-label:hover {
            background: #edf2f7;
            border-color: #667eea;
        }
        
        .option-input:checked + .option-label {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-color: #667eea;
        }
        
        .option-letter {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 35px;
            height: 35px;
            background: white;
            border-radius: 50%;
            font-weight: 700;
            margin-right: 15px;
            flex-shrink: 0;
        }
        
        .option-input:checked + .option-label .option-letter {
            background: white;
            color: #667eea;
        }
        
        .submit-section {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            text-align: center;
            margin-top: 40px;
        }
        
        .submit-btn {
            background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
            color: white;
            padding: 18px 60px;
            border-radius: 50px;
            border: none;
            font-size: 20px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(72, 187, 120, 0.3);
        }
        
        .submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(72, 187, 120, 0.4);
        }
        
        .warning-box {
            background: #fffaf0;
            border: 2px solid #ed8936;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
        }
        
        .warning-box i {
            color: #ed8936;
            font-size: 24px;
            margin-right: 10px;
        }
        
        .progress-indicator {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        }
        
        .progress-bar {
            height: 8px;
            background: #e2e8f0;
            border-radius: 10px;
            overflow: hidden;
        }
        
        .progress-fill {
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: width 0.3s ease;
        }
        
        .progress-text {
            text-align: center;
            margin-top: 10px;
            color: #4a5568;
            font-weight: 600;
        }
        
        @media (max-width: 768px) {
            .timer-display {
                position: static;
                margin-bottom: 20px;
                text-align: center;
            }
            
            .quiz-info {
                flex-direction: column;
                gap: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="timer-display">
        <i class="fas fa-clock"></i>
        <span class="timer-time" id="timer">00:00</span>
    </div>
    
    <div class="quiz-container">
        <div class="quiz-header">
            <h1><i class="fas fa-clipboard-list mr-3"></i><?php echo $job['job_title']; ?> Assessment</h1>
            <p style="color: #718096; margin-top: 10px;"><?php echo $job['company_name']; ?></p>
            
            <div class="quiz-info">
                <div class="quiz-info-item">
                    <i class="fas fa-question-circle"></i>
                    <span><strong><?php echo $total_questions; ?></strong> Questions</span>
                </div>
                <div class="quiz-info-item">
                    <i class="fas fa-check-circle"></i>
                    <span>Passing Score: <strong>50%</strong></span>
                </div>
                <div class="quiz-info-item">
                    <i class="fas fa-ban"></i>
                    <span>One Attempt Only</span>
                </div>
            </div>
        </div>
        
        <div class="warning-box">
            <i class="fas fa-exclamation-triangle"></i>
            <strong>Important:</strong> Answer all questions carefully. You can only take this quiz once. Make sure you have a stable internet connection before starting.
        </div>
        
        <form method="POST" id="quizForm" onsubmit="return confirmSubmit()">
            <input type="hidden" name="start_time" value="<?php echo time(); ?>">
            
            <div class="progress-indicator">
                <div class="progress-bar">
                    <div class="progress-fill" id="progressFill" style="width: 0%"></div>
                </div>
                <div class="progress-text">
                    <span id="answeredCount">0</span> of <?php echo $total_questions; ?> questions answered
                </div>
            </div>
            
            <?php 
            $q_number = 1;
            while ($question = mysqli_fetch_assoc($questions_result)): 
            ?>
                <div class="question-card">
                    <span class="question-number">Question <?php echo $q_number; ?></span>
                    <div class="question-text"><?php echo htmlspecialchars($question['question']); ?></div>
                    
                    <div class="options-container">
                        <div class="option-wrapper">
                            <input type="radio" 
                                   name="q_<?php echo $question['id']; ?>" 
                                   value="<?php echo htmlspecialchars($question['option1']); ?>" 
                                   id="q<?php echo $question['id']; ?>_a"
                                   class="option-input"
                                   onchange="updateProgress()">
                            <label for="q<?php echo $question['id']; ?>_a" class="option-label">
                                <span class="option-letter">A</span>
                                <?php echo htmlspecialchars($question['option1']); ?>
                            </label>
                        </div>
                        
                        <div class="option-wrapper">
                            <input type="radio" 
                                   name="q_<?php echo $question['id']; ?>" 
                                   value="<?php echo htmlspecialchars($question['option2']); ?>" 
                                   id="q<?php echo $question['id']; ?>_b"
                                   class="option-input"
                                   onchange="updateProgress()">
                            <label for="q<?php echo $question['id']; ?>_b" class="option-label">
                                <span class="option-letter">B</span>
                                <?php echo htmlspecialchars($question['option2']); ?>
                            </label>
                        </div>
                        
                        <div class="option-wrapper">
                            <input type="radio" 
                                   name="q_<?php echo $question['id']; ?>" 
                                   value="<?php echo htmlspecialchars($question['option3']); ?>" 
                                   id="q<?php echo $question['id']; ?>_c"
                                   class="option-input"
                                   onchange="updateProgress()">
                            <label for="q<?php echo $question['id']; ?>_c" class="option-label">
                                <span class="option-letter">C</span>
                                <?php echo htmlspecialchars($question['option3']); ?>
                            </label>
                        </div>
                        
                        <div class="option-wrapper">
                            <input type="radio" 
                                   name="q_<?php echo $question['id']; ?>" 
                                   value="<?php echo htmlspecialchars($question['option4']); ?>" 
                                   id="q<?php echo $question['id']; ?>_d"
                                   class="option-input"
                                   onchange="updateProgress()">
                            <label for="q<?php echo $question['id']; ?>_d" class="option-label">
                                <span class="option-letter">D</span>
                                <?php echo htmlspecialchars($question['option4']); ?>
                            </label>
                        </div>
                    </div>
                </div>
            <?php 
                $q_number++;
            endwhile; 
            ?>
            
            <div class="submit-section">
                <h3 style="color: #2d3748; margin-bottom: 20px;">Ready to Submit?</h3>
                <p style="color: #718096; margin-bottom: 30px;">Please review your answers before submitting. You cannot retake this quiz.</p>
                <button type="submit" name="submit_quiz" class="submit-btn">
                    <i class="fas fa-check-circle mr-2"></i>Submit Quiz
                </button>
            </div>
        </form>
    </div>

    <footer class="text-center py-4 mt-5" style="background: rgba(255,255,255,0.1); color: white;">
        <p class="mb-0">&copy; 2026 Job Application Portal. All rights reserved.</p>
    </footer>
    
    <script>
        // Timer
        let startTime = Date.now();
        function updateTimer() {
            let elapsed = Math.floor((Date.now() - startTime) / 1000);
            let minutes = Math.floor(elapsed / 60);
            let seconds = elapsed % 60;
            document.getElementById('timer').textContent = 
                String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');
        }
        setInterval(updateTimer, 1000);
        
        // Progress tracking
        const totalQuestions = <?php echo $total_questions; ?>;
        
        function updateProgress() {
            let answered = 0;
            const form = document.getElementById('quizForm');
            const radioGroups = {};
            
            // Count unique answered questions
            const radios = form.querySelectorAll('input[type="radio"]:checked');
            radios.forEach(radio => {
                radioGroups[radio.name] = true;
            });
            
            answered = Object.keys(radioGroups).length;
            
            // Update display
            document.getElementById('answeredCount').textContent = answered;
            document.getElementById('progressFill').style.width = (answered / totalQuestions * 100) + '%';
        }
        
        // Confirm submission
        function confirmSubmit() {
            const form = document.getElementById('quizForm');
            const radioGroups = {};
            
            const radios = form.querySelectorAll('input[type="radio"]:checked');
            radios.forEach(radio => {
                radioGroups[radio.name] = true;
            });
            
            const answered = Object.keys(radioGroups).length;
            
            if (answered < totalQuestions) {
                const unanswered = totalQuestions - answered;
                return confirm(`You have ${unanswered} unanswered question(s). Submit anyway?`);
            }
            
            return confirm('Are you sure you want to submit your quiz? You cannot change your answers after submission.');
        }
        
        // Prevent accidental page leave
        window.addEventListener('beforeunload', function (e) {
            e.preventDefault();
            e.returnValue = '';
        });
        
        // Remove warning on form submit
        document.getElementById('quizForm').addEventListener('submit', function() {
            window.removeEventListener('beforeunload', function() {});
        });
    </script>
</body>
</html>
