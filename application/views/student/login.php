<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($college['name']) ? $college['name'] . ' - Student Login' : 'Student Login'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f8e0b2;
            min-height: 100vh;
            margin: 0;
            padding: 0;
            overflow: hidden;
        }

        .login-container {
            background:rgb(79, 35, 23);
            border-radius: 10px;
            box-shadow: 0 0 20px rgb(79, 35, 23);
            padding: 2rem;
            /* height: 110%; */
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .banner-container {
            height: 100vh;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            /* border-radius: 10px; */
            /* box-shadow: 0 0 20px rgba(0,0,0,0.1); */
        }

        .logo-container {
            text-align: center;
            margin-bottom: 2rem;
        }

        .logo-container img {
            max-width: 120px;
            max-height: 120px;
            object-fit: contain;
        }

        .form-floating {
            position: relative;
        }

        .form-floating .form-control {
            height: calc(3.5rem + 2px);
            padding-top: 1.625rem;
            padding-bottom: 0.625rem;
        }

        .form-floating > label {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            padding: 1rem 0.75rem;
            pointer-events: none;
            border: 1px solid transparent;
            transform-origin: 0 0;
            transition: opacity 0.1s ease-in-out, transform 0.1s ease-in-out;
        }

        .form-floating > .form-control:focus ~ label,
        .form-floating > .form-control:not(:placeholder-shown) ~ label {
            opacity: 0.65;
            transform: scale(0.85) translateY(-0.5rem) translateX(0.15rem);
        }

        .input-group-text {
            background-color: #f5910a;
            border-right: none;
        }

        .form-control {
            border-left: none;
        }

        .form-control:focus {
            border-left: 1px solid #ced4da;
        }

        .btn-login {
            background: #f5910a;
            border: none;
            padding: 0.75rem 2rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* .btn-login:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        } */

        .college-name {
            color: #ffffff;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .login-title {
            color: #ffffff;
            font-size: 0.9rem;
            margin-bottom: 2rem;
        }

        @media (max-width: 768px) {
            .banner-container {
                height: 200px;
                margin-bottom: 2rem;
            }

            .login-container {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row min-vh-100">
            <!-- Left Side - Login Form (md-4) -->
            <div class="col-md-4 d-flex align-items-center justify-content-center p-4">
                <div class="login-container w-100">
                    <!-- College Logo -->
                    <div class="logo-container">
                        <?php if(isset($college['logo']) && !empty($college['logo'])): ?>
                            <img src="<?php echo base_url('uploads/college/' . $college['logo']); ?>" alt="College Logo" class="img-fluid">
                        <?php endif; ?>
                    </div>

                    <!-- College Name -->
                    <div class="text-center mb-4">
                        <h4 class="college-name">
                            <?php echo isset($college['name']) ? $college['name'] : 'College Management System'; ?>
                        </h4>
                        <p class="login-title">Student Portal - Please sign in to your account</p>
                    </div>

                    <!-- Error Message -->
                    <?php if(isset($error)): ?>
                        <div class="alert alert-danger">
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Login Form -->
<form action="<?php echo base_url('student-portal/authenticate'); ?>" method="POST">
                        <div class="mb-3">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-user" style="color:#ffffff;"></i>
                                </span>
                                <div class="form-floating flex-grow-1">
                                    <input type="text" class="form-control" id="email" name="email"
                                           placeholder="Enter your username" required>
                                    <label for="email">Username</label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-lock" style="color:#ffffff;"></i>
                                </span>
                                <div class="form-floating flex-grow-1">
                                    <input type="password" class="form-control" id="password" name="password"
                                           placeholder="Enter your password" required>
                                    <label for="password">Password</label>
                                </div>
                                <button class="btn btn-outline-light border-0" type="button" id="toggleLoginPassword" style="padding: 0.75rem;">
                                    <i class="feather icon-eye" style="color:#ffffff;"></i>
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-login w-100">
                            <i class="fas fa-sign-in-alt me-2"></i>Login
                        </button>
                    </form>
                </div>
            </div>

            <!-- Right Side - Banner (md-8) -->
            <div class="col-md-8 d-flex align-items-center p-4">
                <div class="banner-container w-100"
                     style="background-image: url('<?php echo isset($college['banner']) && !empty($college['banner']) ? base_url('uploads/college/' . $college['banner']) : base_url('assets/images/default-banner.jpg'); ?>');">
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Password visibility toggle functionality
        function togglePasswordVisibility(passwordField, toggleButton) {
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);

            const icon = toggleButton.querySelector('i');
            if (type === 'password') {
                icon.className = 'feather icon-eye';
            } else {
                icon.className = 'feather icon-eye-off';
            }
        }

        // Add event listener for password toggle button
        const togglePasswordBtn = document.getElementById('toggleLoginPassword');
        const passwordField = document.getElementById('password');

        if (togglePasswordBtn && passwordField) {
            togglePasswordBtn.addEventListener('click', function() {
                togglePasswordVisibility(passwordField, togglePasswordBtn);
            });
        }
    });
    </script>
</body>
</html>


