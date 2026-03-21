<?php
/**
 * History Page
 * ICT 2204 / COM 2303 - Phase 3
 */

require_once 'includes/auth_check.php';
require_once 'includes/functions.php';

$userId = getCurrentUserId();

// Fetch cycles from database
try {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("
        SELECT * FROM cycles 
        WHERE user_id = ? 
        ORDER BY start_date DESC
    ");
    $stmt->execute([$userId]);
    $dbCycles = $stmt->fetchAll();
    
    // Calculate statistics
    $totalCycles = count($dbCycles);
    $avgCycleLength = 0;
    $avgPeriodLength = 0;
    
    if ($totalCycles > 0) {
        $totalCycleDays = array_sum(array_column($dbCycles, 'cycle_length'));
        $totalPeriodDays = array_sum(array_column($dbCycles, 'period_length'));
        $avgCycleLength = round($totalCycleDays / $totalCycles);
        $avgPeriodLength = round($totalPeriodDays / $totalCycles);
    }
} catch (PDOException $e) {
    $dbCycles = [];
    $totalCycles = 0;
    $avgCycleLength = 28;
    $avgPeriodLength = 5;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History - CycleCare</title>
    <link rel="icon" href="https://image.winudf.com/v2/image1/Y29tLm1uLm92dWxhdGlvbnRyYWNrZXIucGVyaW9kY2FsZW5kYXIucGVyaW9kdHJhY2tlcl9pY29uXzE2OTQ0ODE1NTNfMDM4/icon.png?w=102&fakeurl=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
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
                        <a class="nav-link" href="dashboard.php">Dashboard</a>
                        <a class="nav-link" href="tracker.php">Tracker</a>
                        <a class="nav-link active" href="history.php">History</a>
                        <a class="nav-link" href="contact.php">Contact</a>
                        <a class="nav-link" href="auth/logout.php">Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container py-5 mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="text-center mb-5">
                    <h1 class="display-5 fw-bold text-pink mb-3">Cycle History 📅</h1>
                    <p class="text-muted">View your past periods, ovulation days, and fertile windows</p>
                </div>

                <!-- Stats Cards -->
                <div class="row g-4 mb-5">
                    <div class="col-md-4">
                        <div class="card border-0 shadow-lg rounded-4 bg-pink text-white">
                            <div class="card-body text-center p-4">
                                <i class="fas fa-calendar-day fa-3x mb-3 opacity-75"></i>
                                <h3 class="fw-bold mb-1" id="totalCyclesDisplay"><?php echo $totalCycles; ?></h3>
                                <p class="mb-0 opacity-75">Total Cycles</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-lg rounded-4 bg-success text-white">
                            <div class="card-body text-center p-4">
                                <i class="fas fa-clock fa-3x mb-3 opacity-75"></i>
                                <h3 class="fw-bold mb-1" id="avgCycleDisplay"><?php echo $avgCycleLength; ?></h3>
                                <p class="mb-0 opacity-75">Avg Cycle Length</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-lg rounded-4 bg-info text-white">
                            <div class="card-body text-center p-4">
                                <i class="fas fa-droplet fa-3x mb-3 opacity-75"></i>
                                <h3 class="fw-bold mb-1" id="avgPeriodDisplay"><?php echo $avgPeriodLength; ?></h3>
                                <p class="mb-0 opacity-75">Avg Period Length</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- History List -->
                <div class="card border-0 shadow-lg rounded-4">
                    <div class="card-header bg-white border-0 p-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h4 class="fw-bold text-pink mb-0">Detailed History</h4>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-primary btn-sm rounded-pill" onclick="syncWithDatabase()">
                                <i class="fas fa-sync-alt me-2"></i>Sync
                            </button>
                            <button class="btn btn-outline-danger btn-sm rounded-pill" onclick="clearAllHistory()">
                                <i class="fas fa-trash me-2"></i>Clear All
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div id="historyList" class="history-list">
                            <!-- Content loaded by JavaScript -->
                        </div>
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
    <script src="js/api.js"></script>
    
    <script>
    // PHP data passed to JavaScript
    const phpCycles = <?php echo json_encode($dbCycles); ?>;
    const phpStats = {
        totalCycles: <?php echo $totalCycles; ?>,
        avgCycleLength: <?php echo $avgCycleLength; ?>,
        avgPeriodLength: <?php echo $avgPeriodLength; ?>
    };

    // Load and display all history
    async function loadAllHistory() {
        const container = document.getElementById('historyList');
        
        // Show loading
        container.innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-pink" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="text-muted mt-2">Loading your history...</p>
            </div>
        `;

        // Get localStorage data
        const localHistory = JSON.parse(localStorage.getItem('cycleHistory') || '[]');
        const localLogs = JSON.parse(localStorage.getItem('cycleLogs') || '[]');

        // Combine database and localStorage data
        let allCycles = [...phpCycles];
        
        // Add localStorage cycles that aren't in database
        localHistory.forEach(localCycle => {
            const exists = allCycles.some(dbCycle => 
                dbCycle.start_date === new Date(localCycle.startDate).toISOString().split('T')[0]
            );
            if (!exists) {
                allCycles.push({
                    id: 'local_' + localCycle.timestamp,
                    start_date: new Date(localCycle.startDate).toISOString().split('T')[0],
                    period_length: localCycle.periodLength,
                    cycle_length: localCycle.cycleLength,
                    next_period_date: new Date(localCycle.nextPeriod).toISOString().split('T')[0],
                    source: 'local'
                });
            }
        });

        // Sort by date (newest first)
        allCycles.sort((a, b) => new Date(b.start_date) - new Date(a.start_date));

        // Update stats
        updateStats(allCycles);

        // Render cycles
        if (allCycles.length === 0 && localLogs.length === 0) {
            container.innerHTML = `
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-calendar-times fa-3x mb-3 opacity-25"></i>
                    <p>No history available yet. Start tracking your cycle!</p>
                    <a href="tracker.php" class="btn btn-pink rounded-pill mt-3">Go to Tracker</a>
                </div>
            `;
            return;
        }

        let html = '';

        // Render cycles
        allCycles.forEach(cycle => {
            const isLocal = cycle.source === 'local' || String(cycle.id).startsWith('local_');
            const badgeClass = isLocal ? 'bg-warning text-dark' : 'bg-pink';
            const badgeText = isLocal ? 'Local' : 'Database';
            
            html += `
                <div class="history-item" data-id="${cycle.id}">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                        <div>
                            <div class="history-date">
                                <i class="fas fa-calendar-day me-2"></i>
                                ${new Date(cycle.start_date).toLocaleDateString()}
                            </div>
                            <div class="history-stats">
                                <span class="history-stat">
                                    <i class="fas fa-clock text-pink"></i> 
                                    ${cycle.cycle_length} days cycle
                                </span>
                                <span class="history-stat">
                                    <i class="fas fa-droplet text-danger"></i> 
                                    ${cycle.period_length} days period
                                </span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge ${badgeClass} rounded-pill">${badgeText}</span>
                            ${!isLocal ? `
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteCycle(${cycle.id})" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            ` : ''}
                        </div>
                    </div>
                    <div class="mt-2 small text-muted">
                        Next period: ${new Date(cycle.next_period_date).toLocaleDateString()}
                    </div>
                </div>
            `;
        });

        // Render daily logs
        if (localLogs.length > 0) {
            html += `<div class="p-3 bg-light"><h6 class="text-pink mb-0"><i class="fas fa-clipboard-list me-2"></i>Daily Logs</h6></div>`;
            
            localLogs.sort((a, b) => new Date(b.timestamp) - new Date(a.timestamp));
            
            localLogs.slice(0, 10).forEach(log => {
                html += `
                    <div class="history-item">
                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                            <div>
                                <div class="history-date">
                                    <i class="fas fa-clipboard-list me-2"></i>
                                    ${log.date}
                                </div>
                                <div class="history-stats">
                                    ${log.symptoms && log.symptoms.length ? `
                                        <span class="history-stat">
                                            <i class="fas fa-heartbeat text-pink"></i> 
                                            ${log.symptoms.join(', ')}
                                        </span>
                                    ` : ''}
                                    <span class="history-stat">
                                        <i class="fas fa-glass-water text-info"></i> 
                                        ${log.waterIntake} cups
                                    </span>
                                    ${log.weight ? `
                                        <span class="history-stat">
                                            <i class="fas fa-weight-scale text-success"></i> 
                                            ${log.weight}kg
                                        </span>
                                    ` : ''}
                                </div>
                            </div>
                            <span class="badge bg-info rounded-pill">Daily Log</span>
                        </div>
                    </div>
                `;
            });
        }

        container.innerHTML = html;
    }

    // Update statistics display
    function updateStats(cycles) {
        if (cycles.length === 0) return;

        const totalCycleDays = cycles.reduce((sum, c) => sum + parseInt(c.cycle_length), 0);
        const totalPeriodDays = cycles.reduce((sum, c) => sum + parseInt(c.period_length), 0);
        
        const avgCycle = Math.round(totalCycleDays / cycles.length);
        const avgPeriod = Math.round(totalPeriodDays / cycles.length);

        document.getElementById('totalCyclesDisplay').textContent = cycles.length;
        document.getElementById('avgCycleDisplay').textContent = avgCycle;
        document.getElementById('avgPeriodDisplay').textContent = avgPeriod;
    }

    // Sync localStorage with database
    async function syncWithDatabase() {
        const localHistory = JSON.parse(localStorage.getItem('cycleHistory') || '[]');
        
        if (localHistory.length === 0) {
            alert('No local data to sync!');
            return;
        }

        let synced = 0;
        let failed = 0;

        for (const cycle of localHistory) {
            try {
                await api.saveCycle({
                    start_date: new Date(cycle.startDate).toISOString().split('T')[0],
                    period_length: cycle.periodLength,
                    cycle_length: cycle.cycleLength
                });
                synced++;
            } catch (error) {
                console.error('Sync failed for cycle:', cycle, error);
                failed++;
            }
        }

        if (synced > 0) {
            alert(`Successfully synced ${synced} cycles to database!${failed > 0 ? ` (${failed} failed)` : ''}`);
            // Clear localStorage after successful sync
            localStorage.removeItem('cycleHistory');
            location.reload();
        } else {
            alert('Failed to sync. Please try again.');
        }
    }

    // Delete cycle from database
    async function deleteCycle(id) {
        if (!confirm('Are you sure you want to delete this cycle?')) {
            return;
        }

        try {
            await api.deleteCycle(id);
            alert('Cycle deleted successfully!');
            location.reload();
        } catch (error) {
            alert('Failed to delete: ' + error.message);
        }
    }

    // Clear all history
    async function clearAllHistory() {
        if (!confirm('Are you sure you want to clear ALL history? This cannot be undone!')) {
            return;
        }

        // Clear localStorage
        localStorage.removeItem('cycleHistory');
        localStorage.removeItem('cycleLogs');

        // Note: Database records are not deleted for safety
        // Only localStorage is cleared

        alert('Local history cleared! Database records preserved.');
        location.reload();
    }

    // Load on page load
    document.addEventListener('DOMContentLoaded', loadAllHistory);
    </script>
</body>
</html>
