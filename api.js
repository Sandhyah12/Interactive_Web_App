/**
 * CycleCare API Client
 * ICT 2204 / COM 2303 - Phase 3
 * Handles all communication with PHP backend
 */

// ============================================
// CONFIGURATION - UPDATE THIS FOR YOUR SETUP
// ============================================

// XAMPP Local Development
const API_BASE_URL = 'http://localhost/cyclecare/api';

// ============================================
// API CLIENT CLASS
// ============================================

class CycleCareAPI {
    constructor() {
        this.baseURL = API_BASE_URL;
    }

    /**
     * Make HTTP request to backend
     */
    async request(endpoint, method = 'GET', data = null) {
        const url = `${this.baseURL}/${endpoint}`;
        
        const options = {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'include' // Important: sends cookies/session
        };

        // Add body for POST/PUT requests
        if (data && (method === 'POST' || method === 'PUT')) {
            options.body = JSON.stringify(data);
        }

        try {
            console.log(`API Request: ${method} ${url}`);
            
            const response = await fetch(url, options);
            const result = await response.json();
            
            console.log(`API Response:`, result);

            // Check if response was successful
            if (!response.ok || !result.success) {
                throw new Error(result.error || `Server error: ${response.status}`);
            }

            return result;
            
        } catch (error) {
            console.error('API Error:', error);
            throw error;
        }
    }

    // ============================================
    // AUTHENTICATION METHODS
    // ============================================

    async register(userData) {
        return this.request('auth.php?action=register', 'POST', userData);
    }

    async login(credentials) {
        return this.request('auth.php?action=login', 'POST', credentials);
    }

    async logout() {
        return this.request('auth.php?action=logout', 'POST');
    }

    async checkSession() {
        return this.request('auth.php?action=check', 'GET');
    }

    // ============================================
    // CYCLE TRACKING METHODS
    // ============================================

    async saveCycle(cycleData) {
        return this.request('cycles.php', 'POST', cycleData);
    }

    async getCycles() {
        return this.request('cycles.php', 'GET');
    }

    async getCycle(id) {
        return this.request(`cycles.php?id=${id}`, 'GET');
    }

    async updateCycle(id, updates) {
        return this.request(`cycles.php?id=${id}`, 'PUT', updates);
    }

    async deleteCycle(id) {
        return this.request(`cycles.php?id=${id}`, 'DELETE');
    }

    // ============================================
    // DAILY LOGS METHODS
    // ============================================

    async saveLog(logData) {
        return this.request('logs.php', 'POST', logData);
    }

    async getLogs(limit = 30) {
        return this.request(`logs.php?limit=${limit}`, 'GET');
    }

    async getLogByDate(date) {
        return this.request(`logs.php?date=${date}`, 'GET');
    }

    async deleteLog(date) {
        return this.request(`logs.php?date=${date}`, 'DELETE');
    }

    // ============================================
    // CONTACT METHOD
    // ============================================

    async sendContact(messageData) {
        return this.request('contact.php', 'POST', messageData);
    }
}

// ============================================
// CREATE GLOBAL INSTANCE
// ============================================

const api = new CycleCareAPI();
window.api = api;

console.log('API Client loaded! Base URL:', API_BASE_URL);