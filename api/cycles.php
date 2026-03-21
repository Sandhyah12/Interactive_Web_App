<?php
/**
 * Cycle Tracking API
 * ICT 2204 / COM 2303 - Phase 3
 */

session_start();
require_once '../includes/db.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display to users

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
            
            // Log received data for debugging
            error_log("Cycle POST data: " . json_encode($input));
            
            if (empty($input['start_date'])) {
                sendError('Start date is required');
            }
            
            $startDate = $input['start_date'];
            $periodLength = filter_var($input['period_length'] ?? 5, FILTER_VALIDATE_INT);
            $cycleLength = filter_var($input['cycle_length'] ?? 28, FILTER_VALIDATE_INT);
            
            if ($periodLength === false || $periodLength < 1) $periodLength = 5;
            if ($cycleLength === false || $cycleLength < 1) $cycleLength = 28;
            
            // Validate date format
            $dateObj = DateTime::createFromFormat('Y-m-d', $startDate);
            if (!$dateObj || $dateObj->format('Y-m-d') !== $startDate) {
                sendError('Invalid date format. Use YYYY-MM-DD');
            }
            
            // Calculate next period date
            $nextPeriod = clone $dateObj;
            $nextPeriod->modify("+{$cycleLength} days");
            
            // Check for duplicate entry (within same day)
            $stmt = $pdo->prepare("SELECT id FROM cycles WHERE user_id = ? AND start_date = ?");
            $stmt->execute([$userId, $startDate]);
            if ($stmt->fetch()) {
                // Update existing instead of error
                $stmt = $pdo->prepare("
                    UPDATE cycles 
                    SET period_length = ?, cycle_length = ?, next_period_date = ?
                    WHERE user_id = ? AND start_date = ?
                ");
                $stmt->execute([$periodLength, $cycleLength, $nextPeriod->format('Y-m-d'), $userId, $startDate]);
                
                sendSuccess([
                    'start_date' => $startDate,
                    'next_period_date' => $nextPeriod->format('Y-m-d'),
                    'updated' => true
                ], 'Cycle updated successfully');
                exit;
            }
            
            // Insert new cycle
            $stmt = $pdo->prepare("
                INSERT INTO cycles (user_id, start_date, period_length, cycle_length, next_period_date) 
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $userId, 
                $startDate, 
                $periodLength, 
                $cycleLength, 
                $nextPeriod->format('Y-m-d')
            ]);
            
            sendSuccess([
                'id' => $pdo->lastInsertId(),
                'start_date' => $startDate,
                'next_period_date' => $nextPeriod->format('Y-m-d'),
                'created' => true
            ], 'Cycle saved successfully');
            break;
            
        case 'GET':
            $cycleId = $_GET['id'] ?? null;
            
            if ($cycleId) {
                // Get specific cycle
                $stmt = $pdo->prepare("SELECT * FROM cycles WHERE id = ? AND user_id = ?");
                $stmt->execute([$cycleId, $userId]);
                $cycle = $stmt->fetch();
                
                if (!$cycle) {
                    sendError('Cycle not found', 404);
                }
                
                sendSuccess($cycle);
            } else {
                // Get all cycles for user with stats
                $stmt = $pdo->prepare("
                    SELECT * FROM cycles 
                    WHERE user_id = ? 
                    ORDER BY start_date DESC
                ");
                $stmt->execute([$userId]);
                $cycles = $stmt->fetchAll();
                
                // Calculate statistics
                $stats = [
                    'total_cycles' => count($cycles),
                    'avg_cycle_length' => 0,
                    'avg_period_length' => 0
                ];
                
                if (count($cycles) > 0) {
                    $totalCycle = array_sum(array_column($cycles, 'cycle_length'));
                    $totalPeriod = array_sum(array_column($cycles, 'period_length'));
                    $stats['avg_cycle_length'] = round($totalCycle / count($cycles));
                    $stats['avg_period_length'] = round($totalPeriod / count($cycles));
                }
                
                sendSuccess([
                    'cycles' => $cycles,
                    'stats' => $stats
                ]);
            }
            break;
            
        case 'PUT':
            // Update cycle
            $cycleId = $_GET['id'] ?? null;
            if (!$cycleId) {
                sendError('Cycle ID required');
            }
            
            parse_str(file_get_contents('php://input'), $input);
            
            // Verify ownership
            $stmt = $pdo->prepare("SELECT id FROM cycles WHERE id = ? AND user_id = ?");
            $stmt->execute([$cycleId, $userId]);
            if (!$stmt->fetch()) {
                sendError('Cycle not found or access denied', 404);
            }
            
            $updates = [];
            $params = [];
            
            if (isset($input['start_date'])) {
                $updates[] = "start_date = ?";
                $params[] = $input['start_date'];
            }
            if (isset($input['period_length'])) {
                $updates[] = "period_length = ?";
                $params[] = filter_var($input['period_length'], FILTER_VALIDATE_INT);
            }
            if (isset($input['cycle_length'])) {
                $updates[] = "cycle_length = ?";
                $params[] = filter_var($input['cycle_length'], FILTER_VALIDATE_INT);
            }
            
            if (empty($updates)) {
                sendError('No fields to update');
            }
            
            $params[] = $cycleId;
            $params[] = $userId;
            
            $sql = "UPDATE cycles SET " . implode(',
