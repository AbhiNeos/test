<?php
/**
 * Admin Login Page
 * @package FashionShop
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Fashion Shop</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #3d2b1f 0%, #4a3728 50%, #5c4033 100%);
            position: relative;
            overflow: hidden;
        }
        body::before {
            content: '';
            position: absolute;
            top: -20%;
            right: -10%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(166,124,82,0.15) 0%, transparent 70%);
            border-radius: 50%;
        }
        body::after {
            content: '';
            position: absolute;
            bottom: -20%;
            left: -10%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(166,124,82,0.1) 0%, transparent 70%);
            border-radius: 50%;
        }
        .login-box {
            background: rgba(255,255,255,0.98);
            padding: 48px 40px;
            border-radius: 20px;
            box-shadow: 0 30px 80px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 420px;
            position: relative;
            z-index: 1;
        }
        .login-logo {
            text-align: center;
            margin-bottom: 32px;
        }
        .login-logo i {
            font-size: 40px;
            color: #a67c52;
            margin-bottom: 10px;
        }
        .login-logo h1 {
            font-size: 1.6rem;
            color: #3d2b1f;
            font-weight: 700;
        }
        .login-logo p {
            color: #999;
            font-size: 14px;
            margin-top: 4px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            font-size: 13px;
            color: #555;
        }
        .form-group input {
            width: 100%;
            padding: 14px 18px;
            border: 2px solid #efe7dd;
            border-radius: 12px;
            font-size: 14px;
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s;
            background: #faf6f1;
        }
        .form-group input:focus {
            border-color: #a67c52;
            outline: none;
            box-shadow: 0 0 0 3px rgba(166,124,82,0.1);
            background: #fff;
        }
        .login-btn {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #a67c52, #8b5e3c);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(166,124,82,0.3);
        }
        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(166,124,82,0.4);
        }
        .error-msg {
            background: #f8d7da;
            color: #721c24;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            text-align: center;
        }
        .login-footer {
            text-align: center;
            margin-top: 24px;
        }
        .login-footer a {
            color: #a67c52;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <div class="login-logo">
            <i class="fas fa-store"></i>
            <h1>Fashion Shop</h1>
            <p>Admin Panel Login</p>
        </div>

        <?php if (isset($login_error)) : ?>
            <div class="error-msg"><i class="fas fa-exclamation-circle"></i> <?php echo esc_html($login_error); ?></div>
        <?php endif; ?>

        <form method="post" action="">
            <div class="form-group">
                <label><i class="fas fa-user"></i> Username</label>
                <input type="text" name="username" placeholder="Enter username" required>
            </div>
            <div class="form-group">
                <label><i class="fas fa-lock"></i> Password</label>
                <input type="password" name="password" placeholder="Enter password" required>
            </div>
            <button type="submit" name="fs_login" class="login-btn">
                <i class="fas fa-sign-in-alt"></i> Sign In
            </button>
        </form>

        <div class="login-footer">
            <a href="<?php echo esc_url(home_url('/')); ?>"><i class="fas fa-arrow-left"></i> Back to Store</a>
        </div>
    </div>
</body>
</html>
