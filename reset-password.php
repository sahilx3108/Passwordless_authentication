<?php
session_start();
include_once('config.php');

if (!isset($_SESSION['reset_email']) || !isset($_SESSION['reset_otp'])) {
    header("Location: forgot-password.php");
    exit;
}

if (isset($_POST['reset'])) {
    $enteredOtp = $_POST['otp'];
    $newPassword = md5($_POST['new_password']);

    if ($enteredOtp == $_SESSION['reset_otp']) {
        $email = $_SESSION['reset_email'];
        $sql = "UPDATE tblusers SET userPassword = :newpass WHERE emailId = :email";
        $query = $dbh->prepare($sql);
        $query->bindParam(':newpass', $newPassword, PDO::PARAM_STR);
        $query->bindParam(':email', $email, PDO::PARAM_STR);
        $query->execute();

        unset($_SESSION['reset_email'], $_SESSION['reset_otp']);
        echo "<script>alert('Password updated successfully.'); window.location.href='login.php';</script>";
    } else {
        echo "<script>alert('Invalid OTP.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <h2 class="text-2xl font-bold text-center text-indigo-600 mb-4">Reset Password</h2>
        <form method="post" class="space-y-4">
            <div>
                <label for="otp" class="block text-sm font-medium text-gray-700">Enter OTP</label>
                <input type="text" id="otp" name="otp" required 
                       class="mt-1 block w-full px-3 py-2 border rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label for="new_password" class="block text-sm font-medium text-gray-700">New Password</label>
                <input type="password" id="new_password" name="new_password" required 
                       class="mt-1 block w-full px-3 py-2 border rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <button type="submit" name="reset"
                class="w-full py-2 px-4 bg-indigo-600 text-white font-semibold rounded-md hover:bg-indigo-700">
                Reset Password
            </button>
        </form>
        <div class="mt-4 text-center">
            <a href="login.php" class="text-sm text-indigo-600 hover:underline">Back to login</a>
        </div>
    </div>
</body>
</html>
