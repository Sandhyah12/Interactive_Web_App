<?php
/**
 * Daily Logs API
 * ICT 2204 / COM 2303 - Phase 3
 */

session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    sendError('Authentication required', 401);
}

$userId = $_SESSION['user_id'];
$method = $_SERVER['REQUEST_METHOD'];

try {
    $pdo = getDBConnection();
    
    switch ($method) {
        case 'POST':
            $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
            
            if (empty($input['log_date'])) {
                sendError('Log date is required');
            }
            
            $logDate = $input['log_date'];
            
            $dateObj = DateTime::createFromFormat('Y-m-d', $logDate);
            if (!$dateObj || $dateObj->format('Y-m-d') !== $logDate) {
                sendError('Invalid date format');
            }
            
            $symptoms = [];
            if (!empty($input['symptoms']) && is_array($input['symptoms'])) {
                $allowed = ['Cramps', 'Bloating', 'Headache', 'Acne', 'Nausea', 
                          'Fatigue', 'Breast Pain', 'None', 'Mood Swings', 
                          'Back Pain', 'Insomnia', 'Cravings'];
                $symptoms = array_intersect($input['symptoms'], $allowed);
            }
            $symptomsJson = !empty($symptoms) ? json_encode($symptoms) : null;
            
            $waterIntake = filter_var($input['water_intake'] ?? 0, FILTER_VALIDATE_INT);
            $height = !empty($input['height']) ? filter_var($input['height'], FILTER_VALIDATE_FLOAT) : null;
            $weight = !empty($input['weight']) ? filter_var($input['weight'], FILTER_VALIDATE_FLOAT) : null;
            
            $stmt = $pdo->prepare("SELECT id FROM daily_logs WHERE user_id = ? AND log_date = ?");
            $stmt->execute([$userId, $logDate]);
            $existing = $stmt->fetch();
            
            if ($existing) {
                $stmt = $pdo->prepare("
                    UPDATE daily_logs 
                    SET symptoms = ?, water_intake = ?, height_cm = ?, weight_kg = ?
                    WHERE id = ? AND user_id = ?
                ");
                $stmt->execute([$symptomsJson, $waterIntake, $height, $weight, 
                               $existing['id'], $userId]);
                $message = 'Log updated successfully';
            } else {
                $stmt = $pdo->prepare("
                    INSERT INTO daily_logs 
                    (user_id, log_date, symptoms, water_intake, height_cm, weight_kg)
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([$userId, $logDate, $symptomsJson, $waterIntake, $height, $weight]);
                $message = 'Log saved successfully';
            }
            
            sendSuccess([
                'log_date' => $logDate,
                'symptoms' => $symptoms,
                'water_intake' => $waterIntake
            ], $message);
            break;
            
        case 'GET':
            $limit = filter_var($_GET['limit'] ?? 30, FILTER_VALIDATE_INT) ?: 30;
            
            $stmt = $pdo->prepare("
                SELECT * FROM daily_logs 
                WHERE user_id = ? 
                ORDER BY log_date DESC 
                LIMIT ?
            ");
            $stmt->execute([$userId, $limit]);
            $logs = $stmt->fetchAll();
            
            foreach ($logs as &$log) {
                if ($log['symptoms']) {
                    $log['symptoms'] = json_decode($log['symptoms'], true);
                }
            }
            
            sendSuccess($logs);
            break;
            
        default:
            sendError('Method not allowed', 405);
    }
    
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    sendError('Database error', 500);
}
?>
