<?php
session_start();
require 'vendor/autoload.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    
    // Generate a unique token
    $token = bin2hex(random_bytes(32));
    
    // Store token in session with email
    $_SESSION['magic_link_token'] = $token;
    $_SESSION['magic_link_email'] = $email;
    
    // Get the current directory path
    $current_dir = dirname($_SERVER['PHP_SELF']);
    $current_dir = $current_dir === '/' ? '' : $current_dir;
    
    // Create magic link with the correct path
    $magic_link = "http://" . $_SERVER['HTTP_HOST'] . $current_dir . "/verify_magic_link.php?token=" . $token;
    
    // Send email using PHPMailer
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'sahilx3108@gmail.com';
        $mail->Password   = 'ratg jily enbe nult';
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('sahilx3108@gmail.com', 'Passwordless Auth');
        $mail->addAddress($email);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Your Magic Link Login';
        $mail->Body    = "<html><body>
            <h2>Click the link below to login</h2>
            <p>Here's your magic link to login to your account:</p>
            <a href='$magic_link' style='background-color: #4f46e5; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 20px 0;'>Login Now</a>
            <p>This link will expire in 15 minutes.</p>
            <p>If you didn't request this login, please ignore this email.</p>
            </body></html>";
        
        $mail->send();
        echo json_encode(['success' => true, 'message' => 'Magic link sent successfully!']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to send magic link. Please try again.']);
    }
}
?> 
