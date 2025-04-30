<?php 
session_start();
include_once('config.php');
require 'vendor/autoload.php';

// Handle signup logic (only if action=signup and POST request is for signup)
if (isset($_GET['action']) && $_GET['action'] === 'signup' && isset($_POST['submit'])) {
    $name = $_POST['username'];	
    $email = $_POST['email'];	
    $cnumber = $_POST['contactnumber'];	
    $loginpass = md5($_POST['password']);
    $otp = mt_rand(100000, 999999);	

    $ret = "SELECT id FROM tblusers WHERE emailId = :uemail";
    $queryt = $dbh->prepare($ret);
    $queryt->bindParam(':uemail', $email, PDO::PARAM_STR);
    $queryt->execute();

    if ($queryt->rowCount() == 0) {
        $emailverifiy = 0;
        $sql = "INSERT INTO tblusers(userName, emailId, ContactNumber, userPassword, emailOtp, isEmailVerify)
                VALUES (:fname, :emaill, :cnumber, :hashedpass, :otp, :isactive)";
        $query = $dbh->prepare($sql);
        $query->bindParam(':fname', $name, PDO::PARAM_STR);
        $query->bindParam(':emaill', $email, PDO::PARAM_STR);
        $query->bindParam(':cnumber', $cnumber, PDO::PARAM_STR);
        $query->bindParam(':hashedpass', $loginpass, PDO::PARAM_STR);
        $query->bindParam(':otp', $otp, PDO::PARAM_STR);
        $query->bindParam(':isactive', $emailverifiy, PDO::PARAM_STR);
        $query->execute();

        $lastInsertId = $dbh->lastInsertId();
        if ($lastInsertId) {
            $_SESSION['emailid'] = $email;

            // PHPMailer
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'sahilx3108@gmail.com';
                $mail->Password   = 'ratg jily enbe nult';
                $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                $mail->setFrom('sahilx3108@gmail.com', 'Sahil');
                $mail->addAddress($email);
                $mail->isHTML(true);
                $mail->Subject = 'OTP Verification';
                $mail->Body    = "<html><body><div><div>Dear $name,</div><br><br>
                                  <div style='padding-top:8px;'>Thank you for registering with us. 
                                  OTP for Account Verification is <b>$otp</b></div><div></div></body></html>";
                $mail->AltBody = "Dear $name,\n\nThank you for registering with us. OTP for Account Verification is $otp";

                $mail->send();
                echo "<script>window.location.href='verify-otp.php'</script>";
            } catch (Exception $e) {
                echo "<script>alert('Failed to send OTP. Please try again.');</script>";
            }
        } else {
            echo "<script>alert('Something went wrong. Please try again');</script>";	
        }
    } else {
        echo "<script>alert('Email ID already associated with another account.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo (isset($_GET['action']) && $_GET['action'] === 'signup') ? 'Sign Up | Passwordless Auth' : 'Home | Passwordless Auth'; ?></title>
    <?php if (isset($_GET['action']) && $_GET['action'] === 'signup'): ?>
        <script src="https://cdn.tailwindcss.com"></script>
        <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Courgette&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
        <style>
            .bg-pattern {
                background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);
                background-image: 
                    radial-gradient(circle at 25px 25px, rgba(99, 102, 241, 0.15) 3%, transparent 0),
                    radial-gradient(circle at 75px 75px, rgba(99, 102, 241, 0.15) 3%, transparent 0),
                    radial-gradient(circle at 50px 50px, rgba(99, 102, 241, 0.1) 2%, transparent 0);
                background-size: 100px 100px;
                position: relative;
                overflow: hidden;
            }
            .bg-pattern::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: 
                    linear-gradient(45deg, rgba(99, 102, 241, 0.1) 0%, transparent 50%),
                    linear-gradient(-45deg, rgba(99, 102, 241, 0.1) 0%, transparent 50%);
                z-index: -1;
            }
            .bg-pattern::after {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, transparent 100%);
                z-index: -1;
            }
            .card-hover {
                transition: transform 0.3s ease, box-shadow 0.3s ease;
                background-color: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(8px);
            }
            .card-hover:hover {
                transform: translateY(-5px);
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            }
            .input-focus {
                transition: all 0.3s ease;
            }
            .input-focus:focus {
                transform: translateY(-2px);
            }
            .error-message {
                color: #ef4444;
                font-size: 0.875rem;
                margin-top: 0.25rem;
            }
            .input-error {
                border-color: #ef4444 !important;
            }
        </style>
    <?php else: ?>
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet"/>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet"/>
        <style>
            .hero-gradient {
                background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #a855f7 100%);
            }
            .feature-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            }
            .auth-method-card:hover {
                transform: scale(1.03);
            }
        </style>
    <?php endif; ?>
</head>

<body class="<?php echo (isset($_GET['action']) && $_GET['action'] === 'signup') ? 'bg-gray-100 min-h-screen flex items-center justify-center bg-pattern' : 'bg-gradient-to-br from-blue-50 to-indigo-100 text-gray-900 min-h-screen'; ?>">
    <?php if (isset($_GET['action']) && $_GET['action'] === 'signup'): ?>
        <div class="max-w-md w-full space-y-8 p-8">
            <div class="bg-white shadow-lg rounded-lg overflow-hidden card-hover">
                <div class="bg-gradient-to-r from-indigo-600 to-indigo-800 px-6 py-8">
                    <div class="text-center">
                        <h2 class="text-4xl font-bold text-white font-pacifico">
                            Create Account
                        </h2>
                        <p class="text-indigo-100 mt-2 font-courgette">
                            Join us and start your journey
                        </p>
                    </div>
                </div>
                <div class="px-6 py-8">
                    <form method="post" class="space-y-6" id="signupForm" onsubmit="return validateForm()">
                        <div class="space-y-2">
                            <label for="username" class="block text-sm font-medium text-gray-700">Full Name</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <input type="text" name="username" id="username" required
                                    class="input-focus pl-10 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                    placeholder="Enter your full name">
                            </div>
                            <div id="usernameError" class="error-message"></div>
                        </div>
                        <div class="space-y-2">
                            <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                    </svg>
                                </div>
                                <input type="email" name="email" id="email" required
                                    class="input-focus pl-10 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                    placeholder="Enter your email">
                            </div>
                            <div id="emailError" class="error-message"></div>
                        </div>
                        <div class="space-y-2">
                            <label for="contactnumber" class="block text-sm font-medium text-gray-700">Contact Number</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                                    </svg>
                                </div>
                                <input type="text" name="contactnumber" id="contactnumber" required
                                    class="input-focus pl-10 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                    placeholder="Enter your contact number">
                            </div>
                            <div id="contactError" class="error-message"></div>
                        </div>
                        <div class="space-y-2">
                            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <input type="password" name="password" id="password" required
                                    class="input-focus pl-10 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                    placeholder="Enter your password">
                            </div>
                            <div id="passwordError" class="error-message"></div>
                        </div>
                        <div>
                            <button type="submit" name="submit"
                                class="group relative w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 transform hover:scale-105">
                                <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                    <svg class="h-5 w-5 text-indigo-500 group-hover:text-indigo-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                </span>
                                Sign Up
                            </button>
                        </div>
                    </form>
                    <div class="mt-6 text-center text-sm">
                        <span class="text-gray-600">Already have an account?</span>
                        <a href="login.php" class="font-medium text-indigo-600 hover:text-indigo-500 transition-colors duration-200">
                            Login here
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function validateForm() {
                let isValid = true;
                
                document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
                document.querySelectorAll('input').forEach(el => el.classList.remove('input-error'));
                
                const username = document.getElementById('username').value.trim();
                if (username.length < 3) {
                    document.getElementById('usernameError').textContent = 'Username must be at least 3 characters long';
                    document.getElementById('username').classList.add('input-error');
                    isValid = false;
                }
                
                const email = document.getElementById('email').value.trim();
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email)) {
                    document.getElementById('emailError').textContent = 'Please enter a valid email address';
                    document.getElementById('email').classList.add('input-error');
                    isValid = false;
                }
                
                const contact = document.getElementById('contactnumber').value.trim();
                const contactRegex = /^[0-9]{10}$/;
                if (!contactRegex.test(contact)) {
                    document.getElementById('contactError').textContent = 'Please enter a valid 10-digit phone number';
                    document.getElementById('contactnumber').classList.add('input-error');
                    isValid = false;
                }
                
                const password = document.getElementById('password').value;
                if (password.length < 8) {
                    document.getElementById('passwordError').textContent = 'Password must be at least 8 characters long';
                    document.getElementById('password').classList.add('input-error');
                    isValid = false;
                }
                
                return isValid;
            }
        </script>

        <?php
        if(isset($_POST['send_feedback'])) {
            $contact_email = $_POST['contact_email'];
            $message = $_POST['message'];
            
            if (!filter_var($contact_email, FILTER_VALIDATE_EMAIL)) {
                echo "<script>alert('Please enter a valid email address');</script>";
                exit;
            }
            
            $message = htmlspecialchars($message);
            
            $to = "sahilx3108@gmail.com";
            $subject = "Feedback from Passwordless Auth";
            $email_message = "Email: $contact_email\n\nMessage:\n$message";
            $headers = "From: $contact_email\r\n";
            $headers .= "Reply-To: $contact_email\r\n";
            $headers .= "X-Mailer: PHP/" . phpversion();
            
            if(mail($to, $subject, $email_message, $headers)) {
                echo "<script>alert('Thank you for your feedback! We will get back to you soon.');</script>";
            } else {
                echo "<script>alert('Sorry, there was an error sending your message. Please try again later.');</script>";
            }
        }
        ?>
    <?php else: ?>
        <header class="bg-white shadow-lg sticky top-0 z-50">
    <div class="container mx-auto px-4 py-4 flex justify-between items-center">
        <div class="flex items-center">
            <i class="fas fa-fingerprint text-2xl text-indigo-600 mr-2"></i>
            <h1 class="text-2xl font-bold text-indigo-700">Passwordless Auth</h1>
        </div>
        <nav class="hidden md:flex space-x-6">
            <a class="px-3 py-2 text-indigo-600 hover:text-indigo-800 transition relative group" href="index.php">
                Home
                <span class="absolute bottom-0 left-0 w-full h-0.5 bg-indigo-600 transform scale-x-0 group-hover:scale-x-100 transition-transform"></span>
            </a>
            <a class="px-3 py-2 text-gray-600 hover:text-indigo-600 transition" href="otp.html">OTP</a>
            <a class="px-3 py-2 text-gray-600 hover:text-indigo-600 transition" href="magic_link.html">Magic Link</a>
            <a class="px-3 py-2 text-gray-600 hover:text-indigo-600 transition" href="biometrics.html">Biometrics</a>
            <a class="px-3 py-2 text-gray-600 hover:text-indigo-600 transition" href="device_auth.html">Device Auth</a>
            <a class="px-3 py-2 text-gray-600 hover:text-indigo-600 transition" href="feedback.php">Feedback</a>
            
            <?php if (isset($_SESSION['ulogin']) && isset($_SESSION['fname'])): ?>
                <?php
                // Fetch contact number from database
                $userId = $_SESSION['ulogin'];
                $sql = "SELECT emailId, ContactNumber FROM tblusers WHERE id = :uid";
                $query = $dbh->prepare($sql);
                $query->bindParam(':uid', $userId, PDO::PARAM_INT);
                $query->execute();
                $userDetails = $query->fetch(PDO::FETCH_ASSOC);
                $email = htmlspecialchars($userDetails['emailId']);
                $contact = htmlspecialchars($userDetails['ContactNumber']);
                ?>
                <div class="relative">
                    <button id="userDropdown" class="px-3 py-2 text-indigo-800 font-semibold hover:text-indigo-600 transition flex items-center">
                        <i class="fas fa-user text-purple-700 mr-2"></i>
                        <span class="text-purple-700 font-bold"><?php echo htmlspecialchars($_SESSION['fname']); ?></span>
                        <i class="fas fa-caret-down ml-1 text-purple-700"></i>
                    </button>
                    <div id="dropdownMenu" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl py-4 z-50 border border-gray-200">
                        <div class="px-4 py-2 text-gray-700 flex items-center">
                            <span class="text-blue-600 font-medium mr-2">Email:</span>
                            <span class="flex-1 truncate"><?php echo $email; ?></span>
                        </div>
                        <div class="px-4 py-2 text-gray-700">
                            <span class="text-green-500 font-medium">Contact:</span>
                            <span><?php echo $contact; ?></span>
                        </div>
                        <div class="px-4 py-2">
                            <a href="logout.php" class="inline-block bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition duration-200">Logout</a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <a class="px-3 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition" href="login.php">
                    <i class="fas fa-sign-in-alt mr-2"></i>Login
                </a>
            <?php endif; ?>
        </nav>
        <button class="md:hidden text-indigo-700">
            <i class="fas fa-bars text-2xl"></i>
        </button>
    </div>
</header>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dropdownBtn = document.getElementById('userDropdown');
        const dropdownMenu = document.getElementById('dropdownMenu');

        dropdownBtn.addEventListener('click', function(e) {
            e.preventDefault();
            dropdownMenu.classList.toggle('hidden');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!dropdownBtn.contains(e.target) && !dropdownMenu.contains(e.target)) {
                dropdownMenu.classList.add('hidden');
            }
        });
    });
</script>

<style>
    #dropdownMenu {
        border-radius: 0.5rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    #dropdownMenu a {
        display: block;
        width: 100%;
        text-align: center;
    }
</style>

<style>
    #dropdownMenu {
        border-radius: 0.5rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    #dropdownMenu a {
        display: block;
        width: 100%;
        text-align: center;
    }
</style>

        <section class="hero-gradient text-white py-20">
            <div class="container mx-auto px-4 text-center animate__animated animate__fadeIn">
                <h1 class="text-4xl md:text-5xl font-bold mb-6">The Future of Authentication is <span class="text-yellow-300">Passwordless</span></h1>
                <p class="text-xl md:text-2xl opacity-90 max-w-3xl mx-auto mb-8">
                    Secure, seamless login experiences without the hassle of passwords. Reduce friction and eliminate security risks.
                </p>
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <a href="#signup" class="bg-white text-indigo-600 px-8 py-4 rounded-lg font-bold text-lg hover:bg-gray-100 transition transform hover:scale-105 shadow-lg">
                        <i class="fas fa-rocket mr-2"></i> Get Started
                    </a>
                    <a href="#features" class="bg-transparent border-2 border-white text-white px-8 py-4 rounded-lg font-bold text-lg hover:bg-white hover:bg-opacity-10 transition transform hover:scale-105">
                        <i class="fas fa-info-circle mr-2"></i> Learn More
                    </a>
                </div>
            </div>
        </section>

        <section id="features" class="py-16 bg-white">
            <div class="container mx-auto px-4">
                <h2 class="text-3xl md:text-4xl font-bold text-center mb-16 text-gray-800">
                    Why Go <span class="text-indigo-600">Passwordless</span>?
                </h2>
                <div class="grid md:grid-cols-4 gap-8">
                    <div class="feature-card bg-white rounded-xl p-8 shadow-md border border-gray-100 transition duration-300">
                        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mb-6 mx-auto">
                            <i class="fas fa-ban text-red-500 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-center mb-3 text-gray-800">No Password Fatigue</h3>
                        <p class="text-gray-600 text-center">
                            Eliminate the need to remember complex passwords or reset forgotten ones. Users never get locked out.
                        </p>
                    </div>
                    <div class="feature-card bg-white rounded-xl p-8 shadow-md border border-gray-100 transition duration-300">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-6 mx-auto">
                            <i class="fas fa-shield-alt text-green-500 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-center mb-3 text-gray-800">Enhanced Security</h3>
                        <p class="text-gray-600 text-center">
                            Remove the risk of password breaches, phishing, and credential stuffing attacks.
                        </p>
                    </div>
                    <div class="feature-card bg-white rounded-xl p-8 shadow-md border border-gray-100 transition duration-300">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-6 mx-auto">
                            <i class="fas fa-bolt text-blue-500 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-center mb-3 text-gray-800">Faster Logins</h3>
                        <p class="text-gray-600 text-center">
                            Users authenticate in seconds with just a click or biometric scan—no typing required.
                        </p>
                    </div>
                    <div class="feature-card bg-white rounded-xl p-8 shadow-md border border-gray-100 transition duration-300">
                        <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mb-6 mx-auto">
                            <i class="fas fa-mobile-alt text-purple-500 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-center mb-3 text-gray-800">Universal Access</h3>
                        <p class="text-gray-600 text-center">
                            Works across all devices and platforms with consistent user experience everywhere.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-20 bg-gray-50">
            <div class="container mx-auto px-4">
                <div class="text-center mb-16">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">
                        Passwordless <span class="text-indigo-600">Authentication Methods</span>
                    </h2>
                    <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                        Choose the right passwordless solution for your security needs and user experience goals.
                    </p>
                </div>
                <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="auth-method-card bg-white rounded-xl p-6 shadow-lg border border-gray-100 transition duration-300">
                        <div class="flex items-center mb-4">
                            <div class="bg-blue-100 p-3 rounded-lg mr-4">
                                <i class="fas fa-sms text-blue-500 text-xl"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800">One-Time Passwords</h3>
                        </div>
                        <p class="text-gray-600 mb-4">
                            Temporary codes sent via SMS or email that expire after use. Simple to implement with wide compatibility.
                        </p>
                        <a href="otp.html" class="text-indigo-600 font-semibold inline-flex items-center group">
                            Learn more
                            <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                        </a>
                    </div>
                    <div class="auth-method-card bg-white rounded-xl p-6 shadow-lg border border-gray-100 transition duration-300">
                        <div class="flex items-center mb-4">
                            <div class="bg-purple-100 p-3 rounded-lg mr-4">
                                <i class="fas fa-link text-purple-500 text-xl"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800">Magic Links</h3>
                        </div>
                        <p class="text-gray-600 mb-4">
                            Secure, one-click login links delivered to user's email. Eliminates codes for smoother experience.
                        </p>
                        <a href="magic_link.html" class="text-indigo-600 font-semibold inline-flex items-center group">
                            Learn more
                            <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                        </a>
                    </div>
                    <div class="auth-method-card bg-white rounded-xl p-6 shadow-lg border border-gray-100 transition duration-300">
                        <div class="flex items-center mb-4">
                            <div class="bg-green-100 p-3 rounded-lg mr-4">
                                <i class="fas fa-fingerprint text-green-500 text-xl"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800">Biometrics</h3>
                        </div>
                        <p class="text-gray-600 mb-4">
                            Fingerprint, facial recognition, or voice authentication using built-in device capabilities.
                        </p>
                        <a href="biometrics.html" class="text-indigo-600 font-semibold inline-flex items-center group">
                            Learn more
                            <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                        </a>
                    </div>
                    <div class="auth-method-card bg-white rounded-xl p-6 shadow-lg border border-gray-100 transition duration-300">
                        <div class="flex items-center mb-4">
                            <div class="bg-yellow-100 p-3 rounded-lg mr-4">
                                <i class="fas fa-key text-yellow-500 text-xl"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800">Device Auth</h3>
                        </div>
                        <p class="text-gray-600 mb-4">
                            Hardware security keys or authenticator apps using WebAuthn and FIDO2 standards.
                        </p>
                        <a href="device_auth.html" class="text-indigo-600 font-semibold inline-flex items-center group">
                            Learn more
                            <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <section id="signup" class="py-20 bg-indigo-700 text-white">
            <div class="container mx-auto px-4 text-center">
                <div class="max-w-3xl mx-auto">
                    <h2 class="text-3xl md:text-4xl font-bold mb-6">Ready to Ditch Passwords?</h2>
                    <p class="text-xl opacity-90 mb-8">
                        Join thousands of businesses that have eliminated password-related support costs and security risks with our passwordless authentication solutions.
                    </p>
                    <div class="flex flex-col sm:flex-row justify-center gap-4">
                        <a href="index.php?action=signup" class="bg-white text-indigo-600 px-8 py-4 rounded-lg font-bold text-lg hover:bg-gray-100 transition transform hover:scale-105 shadow-lg">
                            <i class="fas fa-user-plus mr-2"></i> Sign Up Free
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <footer class="bg-gray-900 text-white py-12">
            <div class="container mx-auto px-4">
                <div class="grid md:grid-cols-4 gap-8 mb-8">
                    <div>
                        <h3 class="text-xl font-bold mb-4 flex items-center">
                            <i class="fas fa-fingerprint mr-2 text-indigo-400"></i> Passwordless Auth
                        </h3>
                        <p class="text-gray-400">
                            Making authentication secure, simple, and seamless for everyone.
                        </p>
                    </div>
                    <div>
                        <h4 class="font-bold mb-4 text-lg">Solutions</h4>
                        <ul class="space-y-2">
                            <li><a href="otp.html" class="text-gray-400 hover:text-white transition">OTP Authentication</a></li>
                            <li><a href="magic_link.html" class="text-gray-400 hover:text-white transition">Magic Links</a></li>
                            <li><a href="biometrics.html" class="text-gray-400 hover:text-white transition">Biometric Auth</a></li>
                            <li><a href="device_auth.html" class="text-gray-400 hover:text-white transition">Device Authentication</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-bold mb-4 text-lg">Resources</h4>
                        <ul class="space-y-2">
                            <li><a href="blog.html" class="text-gray-400 hover:text-white transition">Blog</a></li>
                            <li><a href="docs.html" class="text-gray-400 hover:text-white transition">Documentation</a></li>
                            <li><a href="case-studies.html" class="text-gray-400 hover:text-white transition">Case Studies</a></li>
                            <li><a href="security.html" class="text-gray-400 hover:text-white transition">Security</a></li>
                            <li><a href="feedback.php" class="text-gray-400 hover:text-white transition">Feedback</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-bold mb-4 text-lg">Connect</h4>
                        <div class="flex space-x-4 mb-4">
                            <a href="#" class="text-gray-400 hover:text-white text-xl transition"><i class="fab fa-twitter"></i></a>
                            <a href="" class="text-gray-400 hover:text-white text-xl transition"><i class="fab fa-github"></i></a>
                            <a href="#" class="text-gray-400 hover:text-white text-xl transition"><i class="fab fa-linkedin"></i></a>
                            <a href="#" class="text-gray-400 hover:text-white text-xl transition"><i class="fab fa-discord"></i></a>
                        </div>
                        <a href="/cdn-cgi/l/email-protection#5b337e7d7d7e51717e7c7c7e7e716d656c7a6c7e646f333e727e7c" class="text-indigo-400 hover:text-indigo-300 transition"><span class="__cf_email__" data-cfemail="7e161b1212133e0e1f0d0d09110c1a121b0d0d1f0b0a16005d101c1e">[email protected]</span></a>
                    </div>
                </div>
                <div class="border-t border-gray-800 pt-8 text-center text-gray-500">
                    <p>© 2025 Passwordless Auth. All rights reserved.</p>
                </div>
            </div>
        </footer>

        <script data-cfasync="false" src="/cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const animatedElements = document.querySelectorAll('.feature-card, .auth-method-card');
                
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('animate__animated', 'animate__fadeInUp');
                            observer.unobserve(entry.target);
                        }
                    });
                }, {
                    threshold: 0.1
                });

                animatedElements.forEach(element => {
                    observer.observe(element);
                });
            });
        </script>
    <?php endif; ?>
</body>
</html>
