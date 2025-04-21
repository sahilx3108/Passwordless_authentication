Passwordless Authentication System with Email OTP Verification

This project implements a secure passwordless authentication system using email OTP verification.

Features:
- User registration with email verification
- OTP-based authentication
- Secure password hashing
- Session management
- Responsive UI with Tailwind CSS

Setup Instructions:

1. Prerequisites:
   - PHP 7.4 or higher
   - MySQL 5.7 or higher
   - Web server (Apache/Nginx)
   - Composer (for PHPMailer)

2. Installation:
   a. Clone or download the project
   b. Place the project in your web server's root directory:
      - XAMPP: xampp/htdocs/
      - WAMP: wamp/www/
      - LAMP: var/www/html/

3. Database Setup:
   a. Open PHPMyAdmin (http://localhost/phpmyadmin)
   b. Create a new database named 'emailoptverification'
   c. Import the database schema from emailoptverification.sql

4. Configuration:
   - Database settings are in config.php
   - Default credentials:
     - Host: localhost
     - Username: root
     - Password: (empty)
     - Database: emailoptverification

5. Email Configuration:
   - Update SMTP settings in relevant files for OTP emails
   - Currently configured for Gmail SMTP

6. Running the Application:
   - Access the application at: http://localhost/passwordless_auth
   - Default entry point: index.php

Project Structure:
- index.php - Main entry point
- login.php - User login
- signup.php - User registration
- verify-otp.php - OTP verification
- resend-otp.php - OTP resend functionality
- welcome.php - User dashboard
- config.php - Database configuration
- db.php - Alternative database connection
- vendor/ - Composer dependencies

Security Features:
- PDO for secure database operations
- Prepared statements to prevent SQL injection
- Session-based authentication
- Email verification for new accounts
- Password hashing using MD5

Note: For production use, please:
1. Change default database credentials
2. Configure proper email settings
3. Implement additional security measures
4. Use HTTPS
5. Update error reporting settings


