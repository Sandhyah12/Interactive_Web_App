<?php
/**
 * User Login
 * ICT 2204 / COM 2303 - Phase 3
 */

session_start();
require_once '../includes/functions.php';

if (isLoggedIn()) {
    header('Location: ../dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Please enter username and password.';
    } else {
        try {
            $pdo = getDBConnection();
            
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ? LIMIT 1");
            $stmt->execute([$username, $username]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['full_name'] ?? $user['username'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['username'] = $user['username'];
                
                session_regenerate_id(true);
                
                header('Location: ../dashboard.php');
                exit;
            } else {
                $error = 'Invalid username or password.';
            }
        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            $error = 'Login failed. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - CycleCare</title>
    <link rel="icon" href="https://image.winudf.com/v2/image1/Y29tLm1uLm92dWxhdGlvbnRyYWNrZXIucGVyaW9kY2FsZW5kYXIucGVyaW9kdHJhY2tlcl9pY29uXzE2OTQ0ODE1NTNfMDM4/icon.png?w=102&fakeurl=1">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body { background: #fff5f8; }
        .login-card {
            max-width: 400px;
            margin: 100px auto;
            background: #fff8fb;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-card">
            <h3 class="text-center text-pink mb-4">CycleCare Login 💗</h3>
            
            <?php if ($error) echo showError($error); ?>
            
            <form method="POST" action="">
                <div class="mb-3">
                    <label class="form-label">Username or Email</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                
                <button type="submit" class="btn btn-pink w-100 py-2">Login</button>
                
                <p class="text-center mt-3">
                    Don't have an account? <a href="register.php" class="text-pink">Sign Up</a>
                </p>
            </form>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>