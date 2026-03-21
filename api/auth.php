<?php
/**
 * API Authentication Endpoint
 * ICT 2204 / COM 2303 - Phase 3
 */

session_start();
require_once '../includes/db.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

try {
    $pdo = getDBConnection();
    
    switch ($action) {
        case 'register':
            if ($method !== 'POST') {
                sendError('Method not allowed', 405);
            }
            
            $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
            
            $username = htmlspecialchars(trim($input['username'] ?? ''));
            $email = filter_var(trim($input['email'] ?? ''), FILTER_SANITIZE_EMAIL);
            $password = $input['password'] ?? '';
            $fullName = htmlspecialchars(trim($input['full_name'] ?? ''));
            
            if (empty($username) || empty($email) || empty($password)) {
                sendError('All fields are required');
            }
            
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            if ($stmt->fetch()) {
                sendError('Username or email already exists');
            }
            
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, full_name) VALUES (?, ?, ?, ?)");
            $stmt->execute([$username, $email, $hash, $fullName]);
            
            $_SESSION['user_id'] = $pdo->lastInsertId();
            $_SESSION['user_name'] = $fullName;
            
            sendSuccess(['user_id' => $_SESSION['user_id']], 'Registration successful');
            break;
            
        case 'login':
            if ($method !== 'POST') {
                sendError('Method not allowed', 405);
            }
            
            $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
            
            $username = $input['username'] ?? '';
            $password = $input['password'] ?? '';
            
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ? LIMIT 1");
            $stmt->execute([$username, $username]);
            $user = $stmt->fetch();
            
            if (!$user || !password_verify($password, $user['password'])) {
                sendError('Invalid credentials', 401);
            }
            
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['full_name'] ?? $user['username'];
            
            sendSuccess([
                'user_id' => $user['id'],
                'name' => $_SESSION['user_name']
            ], 'Login successful');
            break;
            
        case 'logout':
            $_SESSION = [];
            session_destroy();
            sendSuccess(null, 'Logged out');
            break;
            
        case 'check':
            if (isset($_SESSION['user_id'])) {
                sendSuccess([
                    'user_id' => $_SESSION['user_id'],
                    'name' => $_SESSION['user_name']
                ]);
            } else {
                sendError('Not authenticated', 401);
            }
            break;
            
        default:
            sendError('Invalid action', 404);
    }
    
} catch (Exception $e) {
    error_log("API Error: " . $e->getMessage());
    sendError('Server error', 500);
}
?>
