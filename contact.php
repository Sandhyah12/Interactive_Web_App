<?php
/**
 * Contact Form API
 * ICT 2204 / COM 2303 - Phase 3
 */

require_once '../includes/db.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

if ($method !== 'POST') {
    sendError('Method not allowed', 405);
}

try {
    $pdo = getDBConnection();
    
    $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    
    $name = htmlspecialchars(trim($input['name'] ?? ''));
    $email = filter_var(trim($input['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $message = htmlspecialchars(trim($input['message'] ?? ''));
    
    if (empty($name) || strlen($name) < 2) {
        sendError('Name is required (min 2 characters)');
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        sendError('Valid email is required');
    }
    
    if (empty($message) || strlen($message) < 10) {
        sendError('Message is required (min 10 characters)');
    }
    
    // Rate limiting
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as count FROM contact_messages 
        WHERE email = ? AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)
    ");
    $stmt->execute([$email]);
    $result = $stmt->fetch();
    
    if ($result['count'] >= 5) {
        sendError('Too many messages. Please try again later.', 429);
    }
    
    $stmt = $pdo->prepare("
        INSERT INTO contact_messages (name, email, message) 
        VALUES (?, ?, ?)
    ");
    $stmt->execute([$name, $email, $message]);
    
    sendSuccess(null, 'Thank you for your message! We will get back to you soon.');
    
} catch (PDOException $e) {
    error_log("Contact error: " . $e->getMessage());
    sendError('Failed to save message', 500);
}
?>