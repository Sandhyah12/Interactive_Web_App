<?php
/**
 * User Dashboard
 * ICT 2204 / COM 2303 - Phase 3
 */

require_once 'includes/auth_check.php';
require_once 'includes/functions.php';

$userName = getCurrentUserName();

// Get user's recent cycles
try {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("
        SELECT * FROM cycles 
        WHERE user_id = ? 
        ORDER BY start_date DESC 
        LIMIT 5
    ");
    $stmt->execute([getCurrentUserId()]);
    $recentCycles = $stmt->fetchAll();
} catch (PDOException $e) {
    $recentCycles = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - CycleCare</title>
    <link rel="icon" href="https://image.winudf.com/v2/image1/Y29tLm1uLm92dWxhdGlvbnRyYWNrZXIucGVyaW9kY2FsZW5kYXIucGVyaW9kdHJhY2tlcl9pY29uXzE2OTQ0ODE1NTNfMDM4/icon.png?w=102&fakeurl=1"
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .bg-pink { background-color: #d63384 !important; color: white !important; }
        .text-pink { color: #d63384 !important; }
        .bg-dark-pink { background-color: #6b2147 !important; }
        .dashboard-card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .dashboard-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top bg-pink">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <img src="https://image.winudf.com/v2/image1/Y29tLm1uLm92dWxhdGlvbnRyYWNrZXIucGVyaW9kY2FsZW5kYXIucGVyaW9kdHJhY2tlcl9pY29uXzE2OTQ0ODE1NTNfMDM4/icon.png?w=102&fakeurl=1" 
                     alt="Logo" width="35" class="me-2">
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
                        <a class="nav-link active" href="dashboard.php">Dashboard</a>
                        <a class="nav-link" href="tracker.php">Tracker</a>
                        <a class="nav-link" href="history.php">History</a>
                        <a class="nav-link" href="contact.php">Contact</a>
                        <a class="nav-link" href="auth/logout.php">Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="container py-5 mt-5">
        <h2 class="text-pink mb-4">Your Dashboard 💗</h2>
        
        <div class="row g-4">
            <!-- Quick Actions -->
            <div class="col-md-4">
                <div class="card dashboard-card h-100 text-center p-4">
                    <i class="fas fa-calendar-alt fa-3x text-pink mb-3"></i>
                    <h5 class="card-title">Track Your Cycle</h5>
                    <p class="card-text text-muted">Log your period and symptoms</p>
                    <a href="tracker.php" class="btn btn-pink">Go to Tracker</a>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card dashboard-card h-100 text-center p-4">
                    <i class="fas fa-history fa-3x text-success mb-3"></i>
                    <h5 class="card-title">View History</h5>
                    <p class="card-text text-muted">See your past cycles and patterns</p>
                    <a href="history.php" class="btn btn-pink">View History</a>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card dashboard-card h-100 text-center p-4">
                    <i class="fas fa-envelope fa-3x text-info mb-3"></i>
                    <h5 class="card-title">Get Help</h5>
                    <p class="card-text text-muted">Contact us for support</p>
                    <a href="contact.php" class="btn btn-pink">Contact Us</a>
                </div>
            </div>
        </div>
        
        <!-- Recent Cycles -->
        <?php if (!empty($recentCycles)): ?>
        <div class="mt-5">
            <h4 class="text-pink mb-3">Recent Cycles</h4>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="bg-pink text-white">
                        <tr>
                            <th>Start Date</th>
                            <th>Period Length</th>
                            <th>Cycle Length</th>
                            <th>Next Period</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentCycles as $cycle): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($cycle['start_date']); ?></td>
                            <td><?php echo htmlspecialchars($cycle['period_length']); ?> days</td>
                            <td><?php echo htmlspecialchars($cycle['cycle_length']); ?> days</td>
                            <td><?php echo htmlspecialchars($cycle['next_period_date']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php else: ?>
        <div class="mt-5 text-center">
            <p class="text-muted">No cycles recorded yet. Start tracking today!</p>
            <a href="tracker.php" class="btn btn-pink">Start Tracking</a>
        </div>
        <?php endif; ?>
    </div>
    
    <footer class="bg-dark-pink text-white py-4 mt-5">
        <div class="container text-center">
            <p class="mb-0">&copy; 2026 CycleCare | Designed for Women's Health 💗</p>
        </div>
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>