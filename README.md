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

**Alternative: Create database manually**
```sql
CREATE DATABASE cyclecare;
-- Then import the SQL file
