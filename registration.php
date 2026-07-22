<?php
    include 'admin/dbcon.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Create Account | Job Portal</title>
    <?php include 'links.php'; ?>
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            /* Allow scrolling for vertical overflow on mobile */
            overflow-y: auto; 
            padding: 40px 0;
        }

        .reg-container {
            width: 100%;
            max-width: 1000px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 30px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.15);
            overflow: hidden;
            display: flex;
            position: relative;
            margin: 20px;
        }

        .reg-visual {
            width: 40%;
            background: linear-gradient(135deg, #0984e3 0%, #00cec9 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 40px;
            text-align: center;
            color: white;
            position: relative;
        }

        .reg-visual::before {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            top: -100px;
            left: -100px;
        }

        .reg-visual h2 {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 20px;
            position: relative;
            z-index: 2;
        }

        .reg-visual p {
            font-size: 1.1rem;
            opacity: 0.9;
            position: relative;
            z-index: 2;
        }
        
        .reg-btn-outline {
            border: 2px solid white;
            color: white;
            background: transparent;
            padding: 10px 30px;
            border-radius: 50px;
            font-weight: 700;
            margin-top: 30px;
            transition: all 0.3s;
            position: relative;
            z-index: 2;
        }
        
        .reg-btn-outline:hover {
            background: white;
            color: #0984e3;
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }

        .reg-form-side {
            width: 60%;
            padding: 50px;
            background: white;
        }

        .form-title {
            color: #2d3436;
            font-weight: 800;
            font-size: 2rem;
            margin-bottom: 30px;
        }

        .input-group-modern {
            position: relative;
            margin-bottom: 20px;
        }

        .input-group-modern i {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: #b2bec3;
            transition: color 0.3s;
            z-index: 5;
        }

        .form-control-modern {
            width: 100%;
            padding: 15px 15px 15px 50px;
            border: 2px solid #f1f2f6;
            border-radius: 15px;
            font-size: 0.95rem;
            color: #2d3436;
            font-weight: 500;
            transition: all 0.3s;
            background: #fdfdfd;
            height: auto;
        }

        .form-control-modern:focus {
            border-color: #00cec9;
            background: white;
            outline: none;
            box-shadow: 0 5px 20px rgba(0, 206, 201, 0.1);
        }
        
        .form-control-modern:focus + i {
            color: #00cec9;
        }

        .btn-modern {
            background: linear-gradient(to right, #0984e3, #00cec9);
            color: white;
            border: none;
            padding: 15px;
            border-radius: 15px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            font-size: 0.9rem;
            transition: transform 0.3s;
            width: 100%;
            cursor: pointer;
            margin-top: 10px;
        }

        .btn-modern:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(9, 132, 227, 0.3);
            color: white;
        }

        /* Responsive */
        @media (max-width: 991px) {
            .reg-container { flex-direction: column; max-width: 500px; }
            .reg-visual { width: 100%; height: 200px; padding: 20px; }
            .reg-visual::before { display: none; }
            .reg-form-side { width: 100%; padding: 30px; }
            .reg-visual h2 { font-size: 1.8rem; margin-bottom: 10px; }
            .reg-btn-outline { margin-top: 15px; }
        }
    </style>
</head>
<body>
    <?php 
        if (isset($_POST['submit'])){
            $username = mysqli_real_escape_string($con, $_POST['username']);
            $email = mysqli_real_escape_string($con, $_POST['email']);
            $phone = mysqli_real_escape_string($con, $_POST['phone']);
            $password = mysqli_real_escape_string($con, $_POST['password']);
            $cpassword = mysqli_real_escape_string($con, $_POST['cpassword']);
            $degree = mysqli_real_escape_string($con, $_POST['degree']);
            $skills = mysqli_real_escape_string($con, $_POST['skills']);

            $passEncrypt = password_hash($password, PASSWORD_BCRYPT);
            $cpassEncrypt = password_hash($password, PASSWORD_BCRYPT);

            $emailquery = " select * from user_info where email='$email' ";
            $query = mysqli_query($con, $emailquery);
            $emailcount = mysqli_num_rows($query);

            if ($emailcount>0) {
                echo '<div class="alert alert-danger fixed-top text-center m-3 shadow rounded-pill">Email already exists! <button class="close" data-dismiss="alert">&times;</button></div>';
            }else {
                if ($password === $cpassword) {
                    $insertquery = " insert into user_info(username, email, phone, password, cpassword, user_degree, user_skills) 
                    values('$username','$email','$phone','$passEncrypt','$cpassEncrypt', '$degree', '$skills') ";
                    $iquery = mysqli_query($con, $insertquery);

                    if ($iquery) {
                        echo "<script>alert('Account Created Successfully!'); window.location.href='login.php';</script>";
                    }else{
                        echo "<script>alert('Registration Failed');</script>";
                    }
                }else {
                    echo "<script>alert('Password not matching!');</script>";
                }
            }
        }
    ?>

    <div class="reg-container">
        <!-- Visual Sidebar -->
        <div class="reg-visual">
            <h2>Join Us</h2>
            <p>Start your professional journey with us today.</p>
            <a href="login.php" class="reg-btn-outline">Sign In</a>
            <a href="index.php" class="text-white small mt-4 opacity-75"><i class="fas fa-home mr-1"></i> Back to Home</a>
        </div>
        
        <!-- Form Side -->
        <div class="reg-form-side">
            <h1 class="form-title">Create Account</h1>
            
            <form action="<?php echo htmlentities($_SERVER['PHP_SELF']);?>" method="POST">
                
                <div class="input-group-modern">
                    <input name="username" type="text" placeholder="Full Name" class="form-control-modern" required>
                    <i class="fas fa-user"></i>
                </div>

                <div class="input-group-modern">
                    <input name="email" type="email" placeholder="Email Address" class="form-control-modern" required>
                    <i class="fas fa-envelope"></i>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group-modern">
                            <input name="phone" type="text" placeholder="Phone" class="form-control-modern" maxlength="10" required>
                            <i class="fas fa-phone"></i>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group-modern">
                             <!-- Using a div wrapper for the select icon positioning trick or just simple select -->
                             <select name="degree" class="form-control-modern" required style="appearance: none; -webkit-appearance: none;">
                                <option value="" disabled selected>Select Degree</option>
                                <option value="BE/BTech">BE/BTech</option>
                                <option value="ME/MTech">ME/MTech</option>
                                <option value="BCA">BCA</option>
                                <option value="MCA">MCA</option>
                                <option value="BSc">BSc</option>
                                <option value="MSc">MSc</option>
                                <option value="Other">Other</option>
                            </select>
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                    </div>
                </div>

                <div class="input-group-modern">
                    <input name="skills" type="text" placeholder="Key Skills (e.g. PHP, Java)" class="form-control-modern" required>
                    <i class="fas fa-code"></i>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group-modern">
                            <input name="password" type="password" placeholder="Password" class="form-control-modern" required>
                            <i class="fas fa-lock"></i>
                        </div>
                    </div>
                    <div class="col-md-6">
                         <div class="input-group-modern">
                            <input name="cpassword" type="password" placeholder="Confirm" class="form-control-modern" required>
                            <i class="fas fa-lock"></i>
                        </div>
                    </div>
                </div>

                <button type="submit" name="submit" class="btn-modern">Register Now</button>
            </form>
        </div>
    </div>
</body>
</html>