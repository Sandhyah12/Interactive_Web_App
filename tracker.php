<?php
/**
 * Cycle Tracker Page
 * ICT 2204 / COM 2303 - Phase 3
 */

require_once 'includes/auth_check.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tracker - CycleCare</title>
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
                        <a class="nav-link active" href="tracker.php">Tracker</a>
                        <a class="nav-link" href="history.php">History</a>
                        <a class="nav-link" href="contact.php">Contact</a>
                        <a class="nav-link" href="auth/logout.php">Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container-fluid container-lg py-5 mt-5">
        <div class="row g-4">
            <!-- Calendar Column -->
            <div class="col-lg-7">
                <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                    <div class="card-header bg-white border-0 p-4">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <div>
                                <h3 class="fw-bold text-pink mb-1">Your Cycle Calendar</h3>
                                <p class="text-muted mb-0" id="currentDateDisplay">Loading...</p>
                            </div>
                            <div class="legend d-flex gap-3 flex-wrap">
                                <span class="badge rounded-pill bg-pink">Period</span>
                                <span class="badge rounded-pill bg-warning text-dark">Ovulation</span>
                                <span class="badge rounded-pill bg-success">Fertile</span>
                                <span class="badge rounded-pill bg-secondary">Low</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <!-- Cycle Inputs -->
                        <div class="row g-3 mb-4 bg-light-pink p-3 rounded-4">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Last Period Start</label>
                                <input type="date" id="startDate" class="form-control rounded-pill">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Period Length</label>
                                <select id="periodLength" class="form-select rounded-pill">
                                    <option value="3">3 days</option>
                                    <option value="4">4 days</option>
                                    <option value="5" selected>5 days</option>
                                    <option value="6">6 days</option>
                                    <option value="7">7 days</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Cycle Length</label>
                                <select id="cycleLength" class="form-select rounded-pill">
                                    <option value="24">24 days</option>
                                    <option value="26">26 days</option>
                                    <option value="28" selected>28 days</option>
                                    <option value="30">30 days</option>
                                    <option value="32">32 days</option>
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button class="btn btn-pink w-100 rounded-pill" onclick="generateCalendar()" title="Update Calendar">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Month Navigation -->
                        <div class="d-flex justify-content-center align-items-center gap-3 mb-4">
                            <button class="btn btn-outline-pink rounded-circle" onclick="changeMonth(-1)">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <h4 class="mb-0 fw-bold text-pink" id="monthDisplay">Loading...</h4>
                            <button class="btn btn-outline-pink rounded-circle" onclick="changeMonth(1)">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>

                        <!-- Calendar Grid -->
                        <div class="calendar-wrapper">
                            <div class="calendar-weekdays">
                                <div>Sun</div><div>Mon</div><div>Tue</div>
                                                                <div>Wed</div>
                                <div>Thu</div><div>Fri</div><div>Sat</div>
                            </div>
                            <div id="calendarGrid" class="calendar-grid">
                                <!-- Generated by JS -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Log Column -->
            <div class="col-lg-5">
                <div class="card shadow-lg border-0 rounded-4 h-100">
                    <div class="card-body p-4">
                        <h4 class="fw-bold text-pink mb-3" id="selectedDateDisplay">Select a date</h4>
                        
                        <!-- Fertility Status -->
                        <div class="alert alert-light border-0 rounded-4 mb-4" id="fertilityStatus">
                            <span class="badge bg-secondary mb-2">Select a date</span>
                            <p class="mb-0 text-muted small">Click on a calendar date to see fertility status</p>
                        </div>

                        <!-- Symptoms -->
                        <h6 class="fw-bold mb-3">How are you feeling?</h6>
                        <div class="symptoms-grid mb-4">
                            <button class="symptom-btn" onclick="toggleSymptom(this, 'Cramps')">
                                <i class="fas fa-frown"></i><span>Cramps</span>
                            </button>
                            <button class="symptom-btn" onclick="toggleSymptom(this, 'Bloating')">
                                <i class="fas fa-wind"></i><span>Bloating</span>
                            </button>
                            <button class="symptom-btn" onclick="toggleSymptom(this, 'Headache')">
                                <i class="fas fa-brain"></i><span>Headache</span>
                            </button>
                            <button class="symptom-btn" onclick="toggleSymptom(this, 'Acne')">
                                <i class="fas fa-sad-tear"></i><span>Acne</span>
                            </button>
                            <button class="symptom-btn" onclick="toggleSymptom(this, 'Nausea')">
                                <i class="fas fa-stomach"></i><span>Nausea</span>
                            </button>
                            <button class="symptom-btn" onclick="toggleSymptom(this, 'Fatigue')">
                                <i class="fas fa-bolt"></i><span>Fatigue</span>
                            </button>
                            <button class="symptom-btn" onclick="toggleSymptom(this, 'Breast Pain')">
                                <i class="fas fa-heart"></i><span>Breast Pain</span>
                            </button>
                            <button class="symptom-btn" onclick="toggleSymptom(this, 'None')">
                                <i class="fas fa-smile"></i><span>None</span>
                            </button>
                        </div>

                        <!-- Metrics -->
                        <div class="row g-3 mb-4">
                            <div class="col-6">
                                <label class="form-label small fw-bold">Height (cm)</label>
                                <input type="number" id="height" class="form-control rounded-pill" placeholder="165">
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold">Weight (kg)</label>
                                <input type="number" id="weight" class="form-control rounded-pill" placeholder="65" step="0.1">
                            </div>
                        </div>

                        <!-- Water Intake -->
                        <div class="bg-info bg-opacity-10 rounded-4 p-3 mb-4">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="fw-bold text-info"><i class="fas fa-droplet me-2"></i>Water Intake</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-center gap-4">
                                <button class="btn btn-light rounded-circle" onclick="changeWater(-1)">−</button>
                                <div class="text-center">
                                    <span class="display-6 fw-bold text-info" id="waterCount">3</span>
                                    <div class="small text-muted">cups</div>
                                </div>
                                <button class="btn btn-light rounded-circle" onclick="changeWater(1)">+</button>
                            </div>
                        </div>

                        <!-- Save Button -->
                        <button class="btn btn-pink w-100 rounded-pill py-3 fw-bold mb-4" onclick="saveLogToBackend()" id="saveLogBtn">
                            <i class="fas fa-save me-2"></i>Save Log
                        </button>

                        <!-- Logs History -->
                        <div class="logs-section">
                            <h6 class="fw-bold text-pink mb-3"><i class="fas fa-history me-2"></i>Your Logs</h6>
                            <div id="logsContainer" class="logs-container">
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-clipboard-list fa-2x mb-2 opacity-25"></i>
                                    <p class="small mb-0">No logs yet. Start tracking today!</p>
                                </div>
                            </div>
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
    <script src="js/script.js"></script>
    
    <script>
    // Override saveLog to use backend
    async function saveLogToBackend() {
        const height = document.getElementById('height')?.value || null;
        const weight = document.getElementById('weight')?.value || null;
        
        const logData = {
            log_date: selectedDate.toISOString().split('T')[0],
            symptoms: activeSymptoms,
            water_intake: waterIntake,
            height: height,
            weight: weight
        };
        
        const saveBtn = document.getElementById('saveLogBtn');
        const originalText = saveBtn.innerHTML;
        
        try {
            saveBtn.disabled = true;
            saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';
            
            const result = await api.saveLog(logData);
            
            // Also save to localStorage as backup
            saveLog();
            
            // Show success alert
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-5';
            alertDiv.style.zIndex = '9999';
            alertDiv.innerHTML = `
                <i class="fas fa-check-circle me-2"></i>Log saved to database!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.body.appendChild(alertDiv);
            
            setTimeout(() => alertDiv.remove(), 3000);
            
            loadLogsFromBackend();
            
        } catch (error) {
            console.warn('Backend save failed, using localStorage:', error);
            saveLog();
            
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-warning alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-5';
            alertDiv.style.zIndex = '9999';
            alertDiv.innerHTML = `
                <i class="fas fa-exclamation-triangle me-2"></i>Saved locally (offline mode)
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.body.appendChild(alertDiv);
            
            setTimeout(() => alertDiv.remove(), 3000);
        } finally {
            saveBtn.disabled = false;
            saveBtn.innerHTML = originalText;
        }
    }

    // Load logs from backend
    async function loadLogsFromBackend() {
        try {
            const result = await api.getLogs(5);
            const logs = result.data;
            
            const container = document.getElementById('logsContainer');
            if (!container) return;
            
            if (logs.length === 0) {
                container.innerHTML = `
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-clipboard-list fa-2x mb-2 opacity-25"></i>
                        <p class="small mb-0">No logs yet. Start tracking today!</p>
                    </div>
                `;
                return;
            }
            
            container.innerHTML = logs.map(log => `
                <div class="log-entry">
                    <div>
                        <div class="fw-bold text-pink">${new Date(log.log_date).toLocaleDateString()}</div>
                        <div class="small text-muted">
                            ${log.symptoms ? log.symptoms.join(', ') : 'No symptoms'} • 
                            <i class="fas fa-droplet"></i> ${log.water_intake} cups
                            ${log.weight_kg ? `• <i class="fas fa-weight-scale"></i> ${log.weight_kg}kg` : ''}
                        </div>
                    </div>
                    <i class="fas fa-check-circle text-success"></i>
                </div>
            `).join('');
            
        } catch (error) {
            console.warn('Failed to load from backend:', error);
            loadLogs();
        }
    }

    // Override generateCalendar to also save to backend
    const originalGenerateCalendar = generateCalendar;
    generateCalendar = async function() {
        const startValue = document.getElementById('startDate').value;
        const periodLength = parseInt(document.getElementById('periodLength').value);
        const cycleLength = parseInt(document.getElementById('cycleLength').value);

        if (!startValue) {
            alert('Please select a start date');
            return;
        }

        originalGenerateCalendar();
        
        try {
            await api.saveCycle({
                start_date: startValue,
                period_length: periodLength,
                cycle_length: cycleLength
            });
            console.log('Cycle saved to database');
        } catch (error) {
            console.warn('Failed to save cycle to backend:', error);
        }
    };

    // Load logs from backend on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadLogsFromBackend();
    });
    </script>
</body>
</html>
