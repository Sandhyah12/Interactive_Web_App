<?php
/**
 * Contact Page with Database Storage
 * ICT 2204 / COM 2303 - Phase 3
 */

session_start();
require_once 'includes/functions.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitizeInput($_POST['name'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $message = sanitizeInput($_POST['message'] ?? '');
    
    if (empty($name) || empty($email) || empty($message)) {
        $error = 'All fields are required.';
    } elseif (strlen($name) < 2) {
        $error = 'Name must be at least 2 characters.';
    } elseif (!isValidEmail($email)) {
        $error = 'Please enter a valid email.';
    } elseif (strlen($message) < 10) {
        $error = 'Message must be at least 10 characters.';
    } else {
        try {
            $pdo = getDBConnection();
            
            // Rate limiting
            $stmt = $pdo->prepare("
                SELECT COUNT(*) as count FROM contact_messages 
                WHERE email = ? AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)
            ");
            $stmt->execute([$email]);
            $result = $stmt->fetch();
            
            if ($result['count'] >= 5) {
                $error = 'Too many messages. Please try again later.';
            } else {
                $stmt = $pdo->prepare("
                    INSERT INTO contact_messages (name, email, message) 
                    VALUES (?, ?, ?)
                ");
                $stmt->execute([$name, $email, $message]);
                
                $success = 'Thank you for your message! We will get back to you soon.';
            }
        } catch (PDOException $e) {
            error_log("Contact error: " . $e->getMessage());
            $error = 'Failed to send message. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - CycleCare</title>
    <link rel="icon" href="https://image.winudf.com/v2/image1/Y29tLm1uLm92dWxhdGlvbnRyYWNrZXIucGVyaW9kY2FsZW5kYXIucGVyaW9kdHJhY2tlcl9pY29uXzE2OTQ0ODE1NTNfMDM4/icon.png?w=102&fakeurl=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .bg-pink { background-color: #d63384 !important; color: white !important; }
        .text-pink { color: #d63384 !important; }
        .bg-dark-pink { background-color: #6b2147 !important; }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="https://image.winudf.com/v2/image1/Y29tLm1uLm92dWxhdGlvbnRyYWNrZXIucGVyaW9kY2FsZW5kYXIucGVyaW9kdHJhY2tlcl9pY29uXzE2OTQ0ODE1NTNfMDM4/icon.png?w=102&fakeurl=1" alt="Logo">
                CycleCare
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="offcanvas offcanvas-start offcanvas-lg" id="mobileMenu">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title">Menu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
                </div>
                <div class="offcanvas-body">
                    <div class="navbar-nav ms-auto">
                        <a class="nav-link" href="index.php">Home</a>
                        <?php if (isLoggedIn()): ?>
                            <a class="nav-link" href="dashboard.php">Dashboard</a>
                            <a class="nav-link" href="tracker.php">Tracker</a>
                            <a class="nav-link" href="history.php">History</a>
                        <?php else: ?>
                            <a class="nav-link" href="auth/login.php">Login</a>
                        <?php endif; ?>
                        <a class="nav-link active" href="contact.php">Contact</a>
                        <a class="nav-link" href="auth/logout.php">Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Contact Form -->
    <div class="container py-5 mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="card-header bg-pink text-white text-center py-4">
                        <h2 class="fw-bold mb-0">Contact Us 💌</h2>
                    </div>
                    <div class="card-body p-4 p-md-5">
                        <p class="text-center text-muted mb-4">Have questions or feedback? We'd love to hear from you!</p>
                        
                        <?php 
                        if ($error) echo showError($error);
                        if ($success) echo showSuccess($success);
                        ?>
                        
                        <form method="POST" action="" id="contactForm">
                            <div class="mb-4">
                                <label class="form-label fw-bold">Name</label>
                                <input type="text" name="name" class="form-control form-control-lg rounded-pill" 
                                       required minlength="2" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">Email</label>
                                <input type="email" name="email" class="form-control form-control-lg rounded-pill" 
                                       required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">Message</label>
                                <textarea name="message" class="form-control rounded-4" rows="5" 
                                          required minlength="10"><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
                                <small class="text-muted">Minimum 10 characters</small>
                            </div>

                            <button type="submit" class="btn btn-pink w-100 rounded-pill py-3 fw-bold shadow-lg">
                                <i class="fas fa-paper-plane me-2"></i>Send Message
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark-pink text-white py-4 mt-5">
        <div class="container text-center">
            <p class="mb-0">&copy; 2026 CycleCare | Designed for Women's Health 💗</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>