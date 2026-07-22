<?php
    include 'admin/dbcon.php';
    
    if (isset($_POST['register'])) {
        $company_name = mysqli_real_escape_string($con, $_POST['company_name']);
        $email = mysqli_real_escape_string($con, $_POST['email']);
        $phone = mysqli_real_escape_string($con, $_POST['phone']);
        $address = mysqli_real_escape_string($con, $_POST['address']);
        $website = mysqli_real_escape_string($con, $_POST['website']);
        $industry = mysqli_real_escape_string($con, $_POST['industry']);
        $company_size = mysqli_real_escape_string($con, $_POST['company_size']);
        $description = mysqli_real_escape_string($con, $_POST['description']);
        $password = $_POST['password'];
        $cpassword = $_POST['cpassword'];
        
        // Check if email already exists
        $email_check = "SELECT * FROM companies WHERE company_email='$email'";
        $query = mysqli_query($con, $email_check);
        $email_count = mysqli_num_rows($query);
        
        if ($email_count > 0) {
            $error_msg = '<div class="alert alert-danger">Company email already registered!</div>';
        } elseif ($password !== $cpassword) {
            $error_msg = '<div class="alert alert-danger">Passwords do not match!</div>';
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert company
            $insert_query = "INSERT INTO companies (company_name, company_email, company_phone, company_address, company_website, industry, company_size, description, password, status) 
                            VALUES ('$company_name', '$email', '$phone', '$address', '$website', '$industry', '$company_size', '$description', '$hashed_password', 'active')";
            
            if (mysqli_query($con, $insert_query)) {
                $success_msg = '<div class="alert alert-success">Company registered successfully! Please login.</div>';
                echo "<script>
                    setTimeout(function() {
                        window.location.href='company_login.php';
                    }, 2000);
                </script>";
            } else {
                $error_msg = '<div class="alert alert-danger">Registration failed! Please try again.</div>';
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Company Registration | Job Portal</title>
    <?php include 'links.php'; ?>
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 0;
        }

        .reg-container {
            width: 100%;
            max-width: 900px;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border-radius: 30px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.25);
            overflow: hidden;
            margin: 20px;
        }

        .reg-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px;
            text-align: center;
        }

        .reg-header h2 {
            margin: 0;
            font-size: 2rem;
            font-weight: 700;
        }

        .reg-header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
        }

        .reg-body {
            padding: 40px;
        }

        .form-control {
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 12px 20px;
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .btn-register {
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

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }

        .login-link {
            text-align: center;
            margin-top: 20px;
            color: #666;
        }

        .login-link a {
            color: #667eea;
            font-weight: 600;
            text-decoration: none;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .icon-input {
            position: relative;
        }

        .icon-input i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #667eea;
        }

        .icon-input .form-control {
            padding-left: 45px;
        }
    </style>
</head>
<body>
    <div class="reg-container">
        <div class="reg-header">
            <i class="fas fa-building fa-3x mb-3"></i>
            <h2>Company Registration</h2>
            <p>Join us and find the best talents for your company</p>
        </div>
        
        <div class="reg-body">
            <?php if (isset($error_msg)) echo $error_msg; ?>
            <?php if (isset($success_msg)) echo $success_msg; ?>
            
            <form method="POST" action="">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="company_name"><i class="fas fa-building mr-2"></i>Company Name *</label>
                            <input type="text" class="form-control" id="company_name" name="company_name" required>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="email"><i class="fas fa-envelope mr-2"></i>Company Email *</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="phone"><i class="fas fa-phone mr-2"></i>Phone Number *</label>
                            <input type="tel" class="form-control" id="phone" name="phone" required>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="website"><i class="fas fa-globe mr-2"></i>Website (Optional)</label>
                            <input type="url" class="form-control" id="website" name="website" placeholder="https://example.com">
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="industry"><i class="fas fa-industry mr-2"></i>Industry *</label>
                            <select class="form-control" id="industry" name="industry" required>
                                <option value="">Select Industry</option>
                                <option value="Information Technology">Information Technology</option>
                                <option value="Software Development">Software Development</option>
                                <option value="Web Development">Web Development</option>
                                <option value="Mobile App Development">Mobile App Development</option>
                                <option value="E-commerce">E-commerce</option>
                                <option value="Finance">Finance</option>
                                <option value="Healthcare">Healthcare</option>
                                <option value="Education">Education</option>
                                <option value="Consulting">Consulting</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="company_size"><i class="fas fa-users mr-2"></i>Company Size *</label>
                            <select class="form-control" id="company_size" name="company_size" required>
                                <option value="">Select Company Size</option>
                                <option value="1-10">1-10 employees</option>
                                <option value="11-50">11-50 employees</option>
                                <option value="51-200">51-200 employees</option>
                                <option value="201-500">201-500 employees</option>
                                <option value="501-1000">501-1000 employees</option>
                                <option value="1000+">1000+ employees</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="address"><i class="fas fa-map-marker-alt mr-2"></i>Company Address *</label>
                            <textarea class="form-control" id="address" name="address" rows="2" required></textarea>
                        </div>
                    </div>
                    
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="description"><i class="fas fa-info-circle mr-2"></i>Company Description *</label>
                            <textarea class="form-control" id="description" name="description" rows="3" placeholder="Tell us about your company..." required></textarea>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="password"><i class="fas fa-lock mr-2"></i>Password *</label>
                            <input type="password" class="form-control" id="password" name="password" required minlength="6">
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="cpassword"><i class="fas fa-lock mr-2"></i>Confirm Password *</label>
                            <input type="password" class="form-control" id="cpassword" name="cpassword" required minlength="6">
                        </div>
                    </div>
                </div>
                
                <button type="submit" name="register" class="btn btn-register mt-3">
                    <i class="fas fa-user-plus mr-2"></i>Register Company
                </button>
                
                <div class="login-link">
                    Already have an account? <a href="company_login.php">Login here</a>
                </div>
                <div class="login-link mt-2">
                    <a href="index.php"><i class="fas fa-arrow-left mr-2"></i>Back to Home</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
