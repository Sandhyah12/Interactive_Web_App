/**
 * CycleCare Frontend Logic
 * ICT 2204 / COM 2303 - Phase 3
 */

// ==================== GLOBAL VARIABLES ====================
let currentDate = new Date();
let currentMonth = currentDate.getMonth();
let currentYear = currentDate.getFullYear();
let selectedDate = new Date();
let startDate = new Date();
let periodLength = 5;
let cycleLength = 28;
let activeSymptoms = [];
let waterIntake = 3;

// ==================== INITIALIZATION ====================
document.addEventListener('DOMContentLoaded', function() {
    // Set default date values
    const today = new Date();
    const lastMonth = new Date(today.getFullYear(), today.getMonth() - 1, 1);
    
    const startDateInput = document.getElementById('startDate');
    if (startDateInput) {
        startDateInput.valueAsDate = lastMonth;
        startDate = lastMonth;
    }
    
    // Initialize displays
    updateDateDisplay();
    
    // Render calendar if on tracker page
    if (document.getElementById('calendarGrid')) {
        renderCalendar();
        loadLogs();
    }
    
    // Load history if on history page
    if (document.getElementById('historyList')) {
        displayHistory();
    }
});

// ==================== CALENDAR FUNCTIONS ====================
function generateCalendar() {
    const startValue = document.getElementById('startDate').value;
    periodLength = parseInt(document.getElementById('periodLength').value);
    cycleLength = parseInt(document.getElementById('cycleLength').value);

    if (!startValue) {
        alert('Please select a start date');
        return;
    }

    startDate = new Date(startValue);
    currentMonth = startDate.getMonth();
    currentYear = startDate.getFullYear();
    
    saveToHistory();
    renderCalendar();
}

function renderCalendar() {
    const firstDay = new Date(currentYear, currentMonth, 1).getDay();
    const totalDays = new Date(currentYear, currentMonth + 1, 0).getDate();
    const today = new Date();

    const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'];

    const monthDisplay = document.getElementById('monthDisplay');
    if (monthDisplay) {
        monthDisplay.textContent = `${monthNames[currentMonth]} ${currentYear}`;
    }

    let html = '';

    // Empty cells for alignment
    for (let i = 0; i < firstDay; i++) {
        html += '<div class="calendar-day empty"></div>';
    }

    // Days in the month
    for (let day = 1; day <= totalDays; day++) {
        let date = new Date(currentYear, currentMonth, day);
        let className = 'calendar-day';

        // Check cycle phases for any future cycles
        let tempStart = new Date(startDate);
        while (tempStart.getFullYear() < currentYear + 2) {
            const tempPeriodEnd = new Date(tempStart);
            tempPeriodEnd.setDate(tempStart.getDate() + periodLength);

            const tempNextPeriod = new Date(tempStart);
            tempNextPeriod.setDate(tempStart.getDate() + cycleLength);

            const tempOvulation = new Date(tempNextPeriod);
            tempOvulation.setDate(tempNextPeriod.getDate() - 14);

            const fertileStart = new Date(tempOvulation);
            fertileStart.setDate(tempOvulation.getDate() - 4);

            const fertileEnd = new Date(tempOvulation);
            fertileEnd.setDate(tempOvulation.getDate() + 1);

            if (date >= tempStart && date < tempPeriodEnd) {
                className += ' period-day';
                break;
            } else if (date.toDateString() === tempOvulation.toDateString()) {
                className += ' ovulation-day';
                break;
            } else if (date >= fertileStart && date <= fertileEnd) {
                className += ' fertile-day';
                break;
            }

            tempStart.setDate(tempStart.getDate() + cycleLength);
        }

        // Today
        if (date.toDateString() === today.toDateString()) {
            className += ' today';
        }

        // Selected date
        if (selectedDate && date.toDateString() === selectedDate.toDateString()) {
            className += ' selected';
        }

        html += `<div class="${className}" onclick="selectDate(${currentYear}, ${currentMonth}, ${day})">${day}</div>`;
    }

    const grid = document.getElementById('calendarGrid');
    if (grid) grid.innerHTML = html;
}

function changeMonth(step) {
    currentMonth += step;
    if (currentMonth < 0) {
        currentMonth = 11;
        currentYear--;
    } else if (currentMonth > 11) {
        currentMonth = 0;
        currentYear++;
    }
    renderCalendar();
}

function selectDate(year, month, day) {
    selectedDate = new Date(year, month, day);
    renderCalendar();
    updateSelectedDateDisplay();
    updateFertilityStatus();
}

function updateDateDisplay() {
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    
    const currentDateDisplay = document.getElementById('currentDateDisplay');
    if (currentDateDisplay) {
        currentDateDisplay.textContent = new Date().toLocaleDateString('en-US', options);
    }
    
    updateSelectedDateDisplay();
}

function updateSelectedDateDisplay() {
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    const display = document.getElementById('selectedDateDisplay');
    if (display) {
        display.textContent = selectedDate.toLocaleDateString('en-US', options);
    }
}

function updateFertilityStatus() {
    const statusDiv = document.getElementById('fertilityStatus');
    if (!statusDiv) return;
    
    const nextPeriod = new Date(startDate);
    nextPeriod.setDate(startDate.getDate() + cycleLength);
    
    const ovulation = new Date(nextPeriod);
    ovulation.setDate(nextPeriod.getDate() - 14);
    
    const fertileStart = new Date(ovulation);
    fertileStart.setDate(ovulation.getDate() - 4);
    
    const fertileEnd = new Date(ovulation);
    fertileEnd.setDate(ovulation.getDate() + 1);
    
    const periodEnd = new Date(startDate);
    periodEnd.setDate(startDate.getDate() + periodLength);
    
    let badgeClass = 'bg-secondary';
    let badgeText = 'Low fertility window';
    let description = 'Lower chance of pregnancy';
    
    if (selectedDate >= startDate && selectedDate < periodEnd) {
        badgeClass = 'bg-pink';
        badgeText = 'Menstruation';
        description = 'Period day - Take care of yourself';
    } else if (selectedDate.toDateString() === ovulation.toDateString()) {
        badgeClass = 'bg-warning text-dark';
        badgeText = 'Ovulation Day';
        description = 'Highest chance of pregnancy';
    } else if (selectedDate >= fertileStart && selectedDate <= fertileEnd) {
        badgeClass = 'bg-success';
        badgeText = 'High fertility window';
        description = 'Increased chance of pregnancy';
    }
    
    statusDiv.innerHTML = `
        <span class="badge ${badgeClass} mb-2 d-inline-block">${badgeText}</span>
        <p class="mb-0 text-muted small">- ${description}</p>
    `;
}

// ==================== SYMPTOM & LOG FUNCTIONS ====================
function toggleSymptom(element, symptom) {
    element.classList.toggle('active');
    
    if (element.classList.contains('active')) {
        if (!activeSymptoms.includes(symptom)) {
            activeSymptoms.push(symptom);
        }
        // Remove "None" if other symptoms selected
        if (symptom !== 'None') {
            const noneBtn = Array.from(document.querySelectorAll('.symptom-btn')).find(
                btn => btn.querySelector('span').textContent === 'None'
            );
            if (noneBtn) {
                noneBtn.classList.remove('active');
                activeSymptoms = activeSymptoms.filter(s => s !== 'None');
            }
        } else {
            // Clear other symptoms if "None" selected
            document.querySelectorAll('.symptom-btn').forEach(btn => {
                if (btn !== element) btn.classList.remove('active');
            });
            activeSymptoms = ['None'];
        }
    } else {
        activeSymptoms = activeSymptoms.filter(s => s !== symptom);
    }
}

function changeWater(delta) {
    waterIntake = Math.max(0, Math.min(20, waterIntake + delta));
    const count = document.getElementById('waterCount');
    if (count) count.textContent = waterIntake;
}

function saveLog() {
    const height = document.getElementById('height')?.value || '';
    const weight = document.getElementById('weight')?.value || '';
    
    const log = {
        id: Date.now(),
        date: selectedDate.toLocaleDateString(),
        symptoms: [...activeSymptoms],
        waterIntake: waterIntake,
        height: height,
        weight: weight,
        timestamp: new Date().toISOString()
    };
    
    // Save to localStorage
    let logs = JSON.parse(localStorage.getItem('cycleLogs') || '[]');
    logs.unshift(log);
    localStorage.setItem('cycleLogs', JSON.stringify(logs));
    
    // Reset form
    document.querySelectorAll('.symptom-btn').forEach(btn => btn.classList.remove('active'));
    activeSymptoms = [];
    document.getElementById('height').value = '';
    document.getElementById('weight').value = '';
    waterIntake = 3;
    document.getElementById('waterCount').textContent = '3';
    
    loadLogs();
    
    alert('Log saved successfully! 💗');
}

function loadLogs() {
    const logs = JSON.parse(localStorage.getItem('cycleLogs') || '[]');
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
    
    container.innerHTML = logs.slice(0, 5).map(log => `
        <div class="log-entry">
            <div>
                <div class="fw-bold text-pink">${log.date}</div>
                <div class="small text-muted">
                    ${log.symptoms.length ? log.symptoms.join(', ') : 'No symptoms'} • 
                    <i class="fas fa-droplet"></i> ${log.waterIntake} cups
                    ${log.weight ? `• <i class="fas fa-weight-scale"></i> ${log.weight}kg` : ''}
                </div>
            </div>
            <i class="fas fa-check-circle text-success"></i>
        </div>
    `).join('');
}

// ==================== HISTORY FUNCTIONS ====================
function saveToHistory() {
    let history = JSON.parse(localStorage.getItem('cycleHistory') || '[]');
    
    const entry = {
        startDate: startDate.toDateString(),
        periodLength: periodLength,
        cycleLength: cycleLength,
        nextPeriod: new Date(startDate.getTime() + cycleLength * 24 * 60 * 60 * 1000).toDateString(),
        timestamp: new Date().toISOString()
    };
    
    // Avoid duplicates
    const exists = history.some(h => h.startDate === entry.startDate);
    if (!exists) {
        history.push(entry);
        localStorage.setItem('cycleHistory', JSON.stringify(history));
    }
}

function displayHistory() {
    const history = JSON.parse(localStorage.getItem('cycleHistory') || '[]');
    const logs = JSON.parse(localStorage.getItem('cycleLogs') || '[]');
    
    // Update stats
    const totalCyclesEl = document.getElementById('totalCycles');
    const avgCycleEl = document.getElementById('avgCycle');
    const avgPeriodEl = document.getElementById('avgPeriod');
    
    if (totalCyclesEl) totalCyclesEl.textContent = history.length;
    
    if (history.length > 0) {
        const avgCycle = Math.round(history.reduce((sum, h) => sum + h.cycleLength, 0) / history.length);
        const avgPeriod = Math.round(history.reduce((sum, h) => sum + h.periodLength, 0) / history.length);
        if (avgCycleEl) avgCycleEl.textContent = avgCycle;
        if (avgPeriodEl) avgPeriodEl.textContent = avgPeriod;
    }
    
    // Display list
    const container = document.getElementById('historyList');
    if (!container) return;
    
    if (history.length === 0 && logs.length === 0) {
        container.innerHTML = `
            <div class="text-center py-5 text-muted">
                <i class="fas fa-calendar-times fa-3x mb-3 opacity-25"></i>
                <p>No history available yet. Start tracking your cycle!</p>
                <a href="tracker.php" class="btn btn-pink rounded-pill mt-3">Go to Tracker</a>
            </div>
        `;
        return;
    }
    
    // Combine and sort
    const allEntries = [
        ...history.map(h => ({...h, type: 'cycle'})),
        ...logs.map(l => ({...l, type: 'log'}))
    ].sort((a, b) => new Date(b.timestamp) - new Date(a.timestamp));
    
    container.innerHTML = allEntries.map(entry => {
        if (entry.type === 'cycle') {
            return `
                <div class="history-item">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                        <div>
                            <div class="history-date"><i class="fas fa-calendar-day me-2"></i>${entry.startDate}</div>
                            <div class="history-stats">
                                <span class="history-stat"><i class="fas fa-clock text-pink"></i> ${entry.cycleLength} days cycle</span>
                                <span class="history-stat"><i class="fas fa-droplet text-danger"></i> ${entry.periodLength} days period</span>
                            </div>
                        </div>
                        <span class="badge bg-pink rounded-pill">Cycle</span>
                    </div>
                    <div class="mt-2 small text-muted">
                        Next period: ${entry.nextPeriod}
                    </div>
                </div>
            `;
        } else {
            return `
                <div class="history-item">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                        <div>
                            <div class="history-date"><i class="fas fa-clipboard-list me-2"></i>${entry.date}</div>
                            <div class="history-stats">
                                ${entry.symptoms.length ? `<span class="history-stat"><i class="fas fa-heartbeat text-pink"></i> ${entry.symptoms.join(', ')}</span>` : ''}
                                <span class="history-stat"><i class="fas fa-glass-water text-info"></i> ${entry.waterIntake} cups</span>
                            </div>
                        </div>
                        <span class="badge bg-info rounded-pill">Daily Log</span>
                    </div>
                </div>
            `;
        }
    }).join('');
}

function clearHistory() {
    if (confirm('Are you sure you want to clear all history? This cannot be undone.')) {
        localStorage.removeItem('cycleHistory');
        localStorage.removeItem('cycleLogs');
        displayHistory();
    }
}
