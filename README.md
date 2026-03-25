# CycleCare - Period Tracking Application
## ICT 2204 / COM 2303 - Web Design and Technologies
### Phase 3: PHP and Database Integration

---

## Project Description

CycleCare is a web-based period tracking application that helps users monitor their menstrual cycle, predict upcoming periods, identify ovulation days, and track daily symptoms. This project demonstrates full-stack web development using HTML, CSS, JavaScript, PHP, and MySQL.

---

## Features

### User Authentication
- ✅ User Registration with username, email, and password
- ✅ Secure Login with session management
- ✅ Password hashing using bcrypt
- ✅ Logout functionality

### Cycle Tracking
- ✅ Interactive calendar showing period, ovulation, and fertile days
- ✅ Customizable period and cycle length
- ✅ Next period prediction

### Daily Logging
- ✅ Symptom tracking (cramps, bloating, headache, etc.)
- ✅ Water intake counter
- ✅ Height and weight tracking
- ✅ View recent logs

### History & Statistics
- ✅ View all past cycles
- ✅ Average cycle and period length calculations
- ✅ Detailed history with next period predictions

### Contact Form
- ✅ Submit inquiries to database
- ✅ Rate limiting (5 messages per hour per email)
- ✅ Input validation and sanitization

---

## Technologies Used

| Frontend | Backend | Database |
|----------|---------|----------|
| HTML5 | PHP 7.4+ | MySQL 5.7+ |
| CSS3 | PDO | phpMyAdmin |
| Bootstrap 5 | Session Management | |
| JavaScript | REST API | |
| Font Awesome | Input Validation | |

---
## Project Structure
- Interactive_Web_App/
- │
- ├── api/                 # Backend APIs for AJAX calls
- │   └── ...              # API scripts
- │
- ├── auth/                # User authentication scripts
- │   ├── login.php
- │   ├── logout.php
- │   └── signup.php
- │
- ├── css/                 # Stylesheets
- │   └── style.css
- │
- ├── includes/            # Reusable PHP components
- │   ├── db_connect.php   # Database connection
- │   ├── header.php       # Site header
- │   └── footer.php       # Site footer
- │
- ├── js/                  # JavaScript files
- │   └── scripts.js
- │
- ├── index.php            # Landing page / home page
- ├── tracker.php          # Period tracker page
- ├── dashboard.php        # User dashboard
- ├── history.php          # Cycle history page
- ├── contact.php          # Contact form page
- ├── database.sql         # Database schema and sample data
- └── assets/              # Images, icons, and other static files
---

## Security Features

- **Password Hashing**: All passwords hashed using `password_hash()` with bcrypt
- **SQL Injection Prevention**: PDO prepared statements with parameter binding
- **XSS Protection**: `htmlspecialchars()` encoding on all outputs
- **Session Security**: `session_regenerate_id()` on login
- **Rate Limiting**: Contact form limited to 5 submissions per hour per email
- **Input Validation**: Server-side validation for all inputs

---

## Installation & Setup

### Prerequisites
- XAMPP (or WAMP) installed
- Web browser (Chrome, Firefox, Edge)
- Text editor (VS Code, Notepad++, etc.)

### Step 1: Install XAMPP
1. Download XAMPP from [https://www.apachefriends.org](https://www.apachefriends.org)
2. Install to `C:\xampp` (Windows) or `/Applications/XAMPP` (Mac)
3. Start Apache and MySQL from XAMPP Control Panel

### Step 2: Import Database
1. Open browser and go to `http://localhost/phpmyadmin`
2. Click "Import" tab
3. Click "Choose File" and select `database.sql` from this project
4. Click "Go" button to import
 ---
## How to Run the Project
1. Place the project in XAMPP
2. Copy the Interactive_Web_App folder into htdocs inside your XAMPP installation directory.
Windows: C:\xampp\htdocs\Interactive_Web_App
Mac: /Applications/XAMPP/htdocs/Interactive_Web_App
3. Start the local server
4. Open XAMPP Control Panel
5. Start Apache (and MySQL if using the database)
6. Open the project in a browser

7. Go to:

- http://localhost/Interactive_Web_App/
- You should see the CycleCare homepage.
- Navigate to pages like Login, Dashboard, Tracker, etc.
- Login / Signup
- Use the signup form to create a new account
- Login to start tracking cycles and viewing predictions

---
**Alternative: Create database manually**
```sql
CREATE DATABASE cyclecare;
-- Then import the SQL file

