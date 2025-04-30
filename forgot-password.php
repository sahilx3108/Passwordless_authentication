<?php
session_start();
include_once('config.php');
require 'vendor/autoload.php';

if (isset($_POST['send_otp'])) {
    $email = $_POST['email'];

    // Check if email exists
    $sql = "SELECT id FROM tblusers WHERE emailId = :email";
    $query = $dbh->prepare($sql);
    $query->bindParam(':email', $email, PDO::PARAM_STR);
    $query->execute();
    if ($query->rowCount() > 0) {
        $otp = mt_rand(100000, 999999);
        $_SESSION['reset_email'] = $email;
        $_SESSION['reset_otp'] = $otp;
        $_SESSION['otp_timestamp'] = time(); // Store the timestamp when OTP is generated

        // Send OTP via email
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
            $mail->Subject = 'OTP for Password Reset';
            $mail->Body    = "<html><body><div><div>Dear User,</div><br><br>
                              <div style='padding-top:8px;'>Your OTP for password reset is <b>$otp</b></div><div></div></body></html>";
            $mail->AltBody = "Dear User,\n\nYour OTP for password reset is $otp";

            $mail->send();
            header("Location: reset-password.php");
            exit;
        } catch (Exception $e) {
            $error = "Failed to send OTP. Please try again.";
        }
    } else {
        $error = "Email not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <h2 class="text-2xl font-bold text-center text-indigo-600 mb-6">Forgot Password</h2>
        <?php if (isset($error)) echo "<p class='text-red-500 text-center'>$error</p>"; ?>
        <form method="post" class="space-y-6">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" id="email" name="email" required 
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <button type="submit" name="send_otp"
                    class="w-full py-2 px-4 bg-indigo-600 text-white font-semibold rounded-md hover:bg-indigo-700">
                Send OTP
            </button>
        </form>
        <div class="mt-4 text-center">
            <a href="login.php" class="text-sm text-indigo-600 hover:underline">Back to login</a>
        </div>
    </div>
</body>
</html>
