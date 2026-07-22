<?php
    session_start();
    include 'admin/dbcon.php';

    if (isset($_POST['submit'])) {
        $email = mysqli_real_escape_string($con, $_POST['email']);
        $pass = $_POST['password'];

        $email_check = "SELECT * FROM companies WHERE company_email='$email' AND status='active'";
        $query = mysqli_query($con, $email_check);
        $email_count = mysqli_num_rows($query);

        if ($email_count > 0) {
            $company_data = mysqli_fetch_assoc($query);
            $dbpass = $company_data['password'];
            
            // Password verification
            $passDecrypt = password_verify($pass, $dbpass);
            
            if ($passDecrypt) {
                $_SESSION['company_id'] = $company_data['id'];
                $_SESSION['company_name'] = $company_data['company_name'];
                $_SESSION['company_email'] = $company_data['company_email'];
                $_SESSION['company_logo'] = $company_data['logo'];
                $_SESSION['user_type'] = 'company';
                
                echo "<script>alert('Login Successful!'); window.location.href='company/index.php';</script>";
                exit;
            } else {
                $error_msg = '<div class="alert alert-danger text-center"><i class="fas fa-exclamation-circle mr-2"></i>Incorrect Password!</div>';
            }
        } else {
            $error_msg = '<div class="alert alert-danger text-center"><i class="fas fa-user-slash mr-2"></i>Company not found or inactive!</div>';
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Company Login | Job Portal</title>
    <?php include './links.php'; ?>
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: auto;
            padding: 40px 0;
        }

        .login-container {
            width: 100%;
            max-width: 500px;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border-radius: 30px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.25);
            overflow: hidden;
            margin: 20px;
        }

        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 50px 40px;
            text-align: center;
        }

        .login-header i {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.9;
        }

        .login-header h2 {
            margin: 0;
            font-size: 2rem;
            font-weight: 700;
        }

        .login-header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
        }

        .login-body {
            padding: 50px 40px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        label {
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
            display: block;
        }

        .form-control {
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            padding: 15px 20px;
            transition: all 0.3s;
            font-size: 1rem;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 25px;
            padding: 15px 40px;
            color: white;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s;
            width: 100%;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
            color: white;
        }

        .register-link {
            text-align: center;
            margin-top: 25px;
            color: #666;
        }

        .register-link a {
            color: #667eea;
            font-weight: 600;
            text-decoration: none;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        .divider {
            text-align: center;
            margin: 30px 0;
            position: relative;
        }

        .divider::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            width: 100%;
            height: 1px;
            background: #ddd;
        }

        .divider span {
            background: white;
            padding: 0 15px;
            position: relative;
            color: #999;
            font-size: 0.9rem;
        }

        .user-login-link {
            text-align: center;
            margin-top: 20px;
        }

        .user-login-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }

        .user-login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <i class="fas fa-building"></i>
            <h2>Company Portal</h2>
            <p>Login to manage your job postings</p>
        </div>
        
        <div class="login-body">
            <?php if (isset($error_msg)) echo $error_msg; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="email">
                        <i class="fas fa-envelope mr-2"></i>Company Email
                    </label>
                    <input type="email" class="form-control" id="email" name="email" 
                           placeholder="Enter your company email" required>
                </div>
                
                <div class="form-group">
                    <label for="password">
                        <i class="fas fa-lock mr-2"></i>Password
                    </label>
                    <input type="password" class="form-control" id="password" name="password" 
                           placeholder="Enter your password" required>
                </div>
                
                <div style="text-align: right; margin: -10px 0 20px 0;">
                    <a href="forgot_password.php?type=company" style="color: #667eea; text-decoration: none; font-size: 0.9rem; font-weight: 600;">
                        <i class="fas fa-key mr-1"></i>Forgot Password?
                    </a>
                </div>
                
                <button type="submit" name="submit" class="btn btn-login">
                    <i class="fas fa-sign-in-alt mr-2"></i>Login
                </button>
                
                <div class="register-link">
                    Don't have an account? <a href="company_registration.php">Register your company</a>
                </div>
                
                <div class="divider">
                    <span>OR</span>
                </div>
                
                <div class="user-login-link">
                    <a href="login.php">
                        <i class="fas fa-user mr-2"></i>Login as Job Seeker
                    </a>
                </div>
                
                <div class="user-login-link mt-3">
                    <a href="index.php">
                        <i class="fas fa-home mr-2"></i>Back to Home
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
