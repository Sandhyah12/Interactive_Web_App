<?php
/**
 * CycleCare Helper Functions
 * ICT 2204 / COM 2303 - Phase 3
 */

require_once 'db.php';

/**
 * Sanitize user input
 */
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Validate email format
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Redirect if not logged in
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

/**
 * Get current user ID
 */
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current user name
 */
function getCurrentUserName() {
    return $_SESSION['user_name'] ?? null;
}

/**
 * Display error message
 */
function showError($message) {
    return '<div class="alert alert-danger alert-dismissible fade show">' . $message . 
           '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
}

/**
 * Display success message
 */
function showSuccess($message) {
    return '<div class="alert alert-success alert-dismissible fade show">' . $message . 
           '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
}

/**
 * Calculate next period date
 */
function calculateNextPeriod($startDate, $cycleLength) {
    $date = new DateTime($startDate);
    $date->modify("+{$cycleLength} days");
    return $date->format('Y-m-d');
}
?>
