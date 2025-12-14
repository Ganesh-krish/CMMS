<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>DrillU - Online Coding and Aptitude Assessments</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --accent-color: #4895ef;
            --text-color: #333333;
            --light-text: #6c757d;
            --background-color: #ffffff;
            --light-background: #f8f9fa;
            --border-color: #e0e0e0;
            --shadow-color: rgba(0, 0, 0, 0.1);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--light-background);
            color: var(--text-color);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .login-container {
            display: flex;
            min-height: 100vh;
        }
        
        .login-wallpaper {
            flex: 1;
            position: relative;
            display: none;
            background-color: #f0f0f0;
        }
                
        .login-wallpaper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            position: absolute;
        }
        
        .login-wallpaper-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .wallpaper-logo {
            max-width: 120px;
            filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.2));
        }
        
        .login-form-container {
            /* flex: 1; */
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 2rem;
        }
        
        .login-form-wrapper {
            width: 100%;
            max-width: 400px;
            background-color: var(--background-color);
            border-radius: 12px;
            box-shadow: 0 8px 30px var(--shadow-color);
            padding: 2.5rem;
            transition: all 0.3s ease;
        }
        
        .form-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }
        
        .form-logo {
            max-width: 100px;
            margin-bottom: 1.5rem;
        }
        
        .form-title {
            font-size: 1.75rem;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }
        
        .form-subtitle {
            color: var(--light-text);
            font-size: 0.95rem;
        }
        
        .form-group {
            margin-bottom: 1.75rem;
        }
        
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            font-size: 0.9rem;
        }
        
        .form-control {
            width: 100%;
            height: 48px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px rgba(72, 149, 239, 0.2);
        }
        
        .password-field {
            position: relative;
        }
        
        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--light-text);
            cursor: pointer;
        }
        
        .btn-login {
            width: 100%;
            height: 48px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 0.5rem;
        }
        
        .btn-login:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(67, 97, 238, 0.3);
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        .footer {
            margin-top: 2rem;
            text-align: center;
            color: var(--light-text);
            font-size: 0.85rem;
        }
        
        /* Alert messages */
        .alert {
            padding: 0.75rem 1.25rem;
            margin-bottom: 1.75rem;
            border: 1px solid transparent;
            border-radius: 8px;
            font-size: 0.95rem;
        }
        
        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }
        
        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }
        
        /* Responsive Design */
        @media (min-width: 992px) {
            .login-wallpaper {
                display: block;
            }
        }
        
        @media (max-width: 768px) {
            .login-form-wrapper {
                padding: 2rem;
            }
        }
        
        @media (max-width: 576px) {
            .login-form-wrapper {
                padding: 1.5rem;
                box-shadow: none;
                background-color: transparent;
            }
            
            .login-form-container {
                padding: 1rem;
            }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <!-- Left Side - Wallpaper Only -->
        <div class="login-wallpaper">
            <img id="wallpaperImage" src="<?php echo $banner; ?>" alt="Login Wallpaper">
        </div>
        <!-- Right Side - Login Form -->
        <div class="login-form-container">
            <div class="login-form-wrapper">
                <div class="form-header">
                    <img id="formLogo" class="form-logo" src="<?php echo $logo; ?>" alt="DrillU Logo">
                    <h2 class=""><?php echo $college_name ?></h2>
                    <p class="form-subtitle">Sign in to continue to your account</p>
                </div>
                
                <!-- Redirecting to OAuth login -->
                <script>
                    window.location.href = '<?= base_url("OAuth") ?>';
                </script>
                <div class="alert alert-info text-center">
                    <h4>Redirecting to Login...</h4>
                    <p>If you're not redirected automatically, <a href="<?= base_url('OAuth') ?>">click here</a>.</p>
                    </div>
                
                <div class="footer">
                    <p>&copy; <span id="currentYear">2024</span> DrillU. All rights reserved.</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Set current year
        document.getElementById('currentYear').textContent = new Date().getFullYear();
        
        // Password visibility toggle
        const passwordToggle = document.getElementById('passwordToggle');
        const passwordField = document.getElementById('password');
        
        passwordToggle.addEventListener('click', function() {
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
            
            // Toggle icon
            const icon = this.querySelector('i');
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html>