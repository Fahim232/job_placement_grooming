<?php
    session_start();
    include 'admin/dbcon.php';

    if (isset($_POST['submit'])) {
        $email = $_POST['email'];
        $pass = $_POST['password'];

        $email_check = " select * from user_info where email='$email' ";
        $query = mysqli_query($con, $email_check);
        $email_count = mysqli_num_rows($query);

        if (($email_count>0)){
            $email_pass = mysqli_fetch_assoc($query);
            $dbpass = $email_pass['password'];
            
            // Password verification using password_verify since we used password_hash in registration
            $passDecrypt = password_verify($pass, $dbpass);
            
            if ($passDecrypt) {
                $_SESSION['username'] = $email_pass['username'];
                $_SESSION['uphone'] = $email_pass['phone'];
                $_SESSION['email'] = $email_pass['email'];
                $_SESSION['id'] = $email_pass['id'];
                
                // Handle redirect if specified
                $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'index.php';
                echo "<script>alert('Login Successful!'); window.location.href='$redirect';</script>";
                exit;
            } else {
                echo "<script>alert('Incorrect Password!');</script>"; // Using simple alert for now to ensure visibility before redirect or page load
                $error_msg = '<div class="alert alert-danger text-center fixed-top m-3 shadow rounded-pill px-4" style="z-index: 1000;"><i class="fas fa-exclamation-circle mr-2"></i> Incorrect Password! <button type="button" class="close" data-dismiss="alert">&times;</button></div>';
            }
        } else {
            echo "<script>alert('User does not exist!');</script>";
            $error_msg = '<div class="alert alert-danger text-center fixed-top m-3 shadow rounded-pill px-4" style="z-index: 1000;"><i class="fas fa-user-slash mr-2"></i> User does not exist! <button type="button" class="close" data-dismiss="alert">&times;</button></div>';
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Log In | Job Portal</title>
    <?php include './links.php'; ?>
    <style>
        body {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .login-container {
            width: 100%;
            max-width: 900px;
            height: 550px;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            border-radius: 30px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.15);
            overflow: hidden;
            display: flex;
            position: relative;
        }

        .login-visual {
            width: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 40px;
            text-align: center;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .login-visual::before {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            top: -50px;
            left: -50px;
        }

        .login-visual::after {
            content: '';
            position: absolute;
            width: 200px;
            height: 200px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            bottom: -30px;
            right: -30px;
        }

        .login-visual h2 {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 20px;
            z-index: 2;
        }

        .login-visual p {
            font-size: 1.1rem;
            opacity: 0.9;
            z-index: 2;
        }

        .login-form-side {
            width: 50%;
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: white;
        }

        .form-title {
            color: #2d3436;
            font-weight: 800;
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .form-subtitle {
            color: #b2bec3;
            margin-bottom: 40px;
            font-size: 0.95rem;
        }

        .input-group-modern {
            position: relative;
            margin-bottom: 25px;
        }

        .input-group-modern i {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: #b2bec3;
            transition: color 0.3s;
        }

        .form-control-modern {
            width: 100%;
            padding: 15px 15px 15px 50px;
            border: 2px solid #f1f2f6;
            border-radius: 15px;
            font-size: 1rem;
            color: #2d3436;
            font-weight: 500;
            transition: all 0.3s;
            background: #fdfdfd;
        }

        .form-control-modern:focus {
            border-color: #667eea;
            background: white;
            outline: none;
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.1);
        }

        .form-control-modern:focus + i {
            color: #667eea;
        }

        .btn-modern {
            background: linear-gradient(to right, #667eea, #764ba2);
            color: white;
            border: none;
            padding: 15px;
            border-radius: 15px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            font-size: 0.9rem;
            transition: transform 0.3s, box-shadow 0.3s;
            width: 100%;
            cursor: pointer;
        }

        .btn-modern:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(118, 75, 162, 0.3);
            color: white;
        }

        .login-link {
            text-align: center;
            margin-top: 25px;
            font-size: 0.9rem;
            color: #636e72;
        }

        .login-link a {
            color: #667eea;
            font-weight: 700;
            text-decoration: none;
        }

        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
                height: auto;
                max-width: 90%;
            }
            .login-visual {
                width: 100%;
                padding: 30px;
                height: 150px;
            }
            .login-visual h2 { font-size: 1.8rem; }
            .login-form-side { width: 100%; padding: 30px; }
            .login-visual::before, .login-visual::after { display: none; }
        }
    </style>
</head>

<body>
    <?php if(isset($error_msg)) { echo $error_msg; } ?>

    <div class="login-container">
        <!-- Visual Side -->
        <div class="login-visual">
            <h2>Welcome Back</h2>
            <p>Access your dashboard and continue your journey.</p>
        </div>

        <!-- Form Side -->
        <div class="login-form-side">
            <h1 class="form-title">Log In</h1>
            <p class="form-subtitle">Enter your credentials to access your account.</p>

            <form action="<?php echo htmlentities($_SERVER['PHP_SELF']);?>" method="POST">
                <div class="input-group-modern">
                    <input name="email" type="email" placeholder="Email Address" class="form-control-modern" required>
                    <i class="fa fa-envelope"></i>
                </div>

                <div class="input-group-modern">
                    <input name="password" type="password" placeholder="Password" class="form-control-modern" required>
                    <i class="fa fa-lock"></i>
                </div>
                <div style="text-align: right; margin: 10px 0;">
                    <a href="forgot_password.php?type=user" style="color: #667eea; text-decoration: none; font-size: 0.9rem; font-weight: 600;">
                        <i class="fas fa-key mr-1"></i>Forgot Password?
                    </a>
                </div>
                <button type="submit" name="submit" class="btn-modern">
                    Sign In <i class="fas fa-arrow-right ml-2"></i>
                </button>

                <div class="login-link">
                    Don't have an account? <a href="registration.php">Create Account</a>
                </div>
                <!-- Guest link -->
                <div class="text-center mt-3">
                    <a href="index.php" class="small text-muted"><i class="fas fa-home mr-1"></i> Back to Home</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>