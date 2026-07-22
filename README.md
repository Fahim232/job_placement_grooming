# 💼 Job Placement Grooming Portal

> A modern, feature-rich Job Application Portal designed to streamline candidate recruitment, skill assessment, and professional development.

![Status](https://img.shields.io/badge/status-active-success.svg)
![PHP](https://img.shields.io/badge/PHP-7.4+-blue.svg)
![MySQL](https://img.shields.io/badge/MySQL-compatible-orange.svg)
![License](https://img.shields.io/badge/license-MIT-green.svg)

---

## ✨ Key Features

- 🎨 **Premium UI/UX Design** – Clean glassmorphism aesthetic with smooth animations and modern minimalist layout
- 📝 **Skill Assessment System** – Multi-category quiz system (PHP, Java, Python) for comprehensive candidate evaluation
- 🎓 **Grooming Sessions** – Interactive learning module to help candidates enhance skills and retake assessments
- 📊 **Candidate Dashboard** – Personalized tracking and management of job applications in real-time
- 📄 **Automated CV Generation** – Instant PDF CV creation for registered candidates
- 👨‍💼 **Admin Control Panel** – Complete management of candidates, applications, assessments, and portal settings

---

## 🛠️ Tech Stack

| Component | Technology |
|-----------|-----------|
| **Frontend** | HTML5, CSS3, Bootstrap 4.6.1, Font Awesome |
| **Backend** | PHP 7.4+ |
| **Database** | MySQL |
| **Assets** | Font Awesome Icons, Popsy Illustrations |

---

## 🚀 Quick Start

### Prerequisites
- **XAMPP** (or similar local server environment with PHP & MySQL support)
- Basic knowledge of PHP and MySQL

### Installation Steps

#### 1️⃣ Database Configuration
```bash
# Open phpMyAdmin and create a new database
Database Name: projects

# Import the provided database schema
1. Select the 'projects' database
2. Navigate to Import tab
3. Upload database.sql
4. Click Go
```

#### 2️⃣ Project Setup
```bash
# Clone/extract project to htdocs
C:/xampp/htdocs/job_placement_grooming/

# Verify database connection
File: admin/dbcon.php
Check: $database = 'projects';
```

#### 3️⃣ Run the Application
```bash
# Start Apache & MySQL in XAMPP Control Panel

# Open in browser
http://localhost/job_placement_grooming/index.php
```

---

## 🔐 Default Credentials

### Admin Access
| Field | Value |
|-------|-------|
| **URL** | `http://localhost/job_placement_grooming/admin/admin_login.php` |
| **Username** | `admin` |
| **Password** | `admin123` |

⚠️ **Note:** Change default credentials immediately after first login for security.

---

## 📁 Project Structure

```
job_placement_grooming/
├── /admin/               # Admin panel & backend logic
├── /css/                 # Stylesheets (style.css, Bootstrap)
├── /js/                  # Client-side JavaScript
├── /files/               # Uploaded candidate CVs
├── index.php             # Main landing page
├── database.sql          # Database schema & sample data
└── README.md             # This file
```

---

## 📖 Usage Guide

### For Candidates
1. Register or login to your account
2. Take a skill assessment in your preferred category
3. Use grooming sessions to improve skills
4. Retake assessments for better scores
5. Download your auto-generated CV
6. Track application status on dashboard

### For Administrators
1. Login to admin panel
2. View and manage candidate profiles
3. Monitor assessment scores
4. Track applications and grooming progress
5. Configure portal settings
6. Generate reports

---

## 🔄 Features Workflow

```
Candidate Registration
    ↓
Skill Assessment (PHP/Java/Python)
    ↓
View Results & Feedback
    ↓
Grooming Session (Learn & Improve)
    ↓
Retake Assessment
    ↓
Generate CV & Apply for Jobs
    ↓
Track Applications
```

---

## 🤝 Contributing

Contributions are welcome! Feel free to:
- Report bugs via GitHub Issues
- Suggest improvements
- Submit pull requests with enhancements

---

## 📝 License

This project is open source and available for personal and educational use.

---

## 🙏 Acknowledgments

- **Design Inspiration** – Modern glassmorphism UI trends
- **Icons** – Font Awesome
- **Illustrations** – Popsy
- **Framework** – Bootstrap Community

---

## 📧 Support

For questions or issues, please open a GitHub Issue or contact the development team.

---

*Last Updated: July 2026*


