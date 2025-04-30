<?php
require 'vendor/autoload.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $feedback = $_POST['feedback'];

    // 1. Save feedback to database
    $conn = new mysqli("localhost", "root", "", "feedback"); // Change to your DB name

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("INSERT INTO feedbacks (name, email, feedback) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $feedback);
    $stmt->execute();
    $stmt->close();
    $conn->close();

    // 2. Send feedback via email
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
        $mail->setFrom($email, $name);
        $mail->addAddress('sahilx3108@gmail.com', 'Sahil');

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'New Feedback from ' . $name;
        $mail->Body    = "<html><body>
            <h2>New Feedback Received</h2>
            <p><strong>From:</strong> $name ($email)</p>
            <p><strong>Feedback:</strong></p>
            <p>$feedback</p>
            </body></html>";

        $mail->send();
        // Redirect to a confirmation page or update the current page
        $success = true;
    } catch (Exception $e) {
        $error = "Failed to send feedback. Please try again later.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Feedback | Passwordless Auth</title>
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
        .card-hover {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            background-color: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(8px);
        }
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center bg-pattern">
    <?php if (isset($success) && $success): ?>
        <div class="max-w-md w-full space-y-8 p-8">
            <div class="bg-white shadow-lg rounded-lg overflow-hidden card-hover">
                <div class="bg-gradient-to-r from-indigo-600 to-indigo-800 px-6 py-8">
                    <div class="text-center">
                        <h2 class="text-4xl font-bold text-white font-pacifico">
                            Feedback Submitted
                        </h2>
                    </div>
                </div>
                <div class="px-6 py-8 text-center">
                    <p class="text-2xl text-indigo-800 mb-6">Thank you for your feedback!</p>
                    <a href="index.php" class="inline-block bg-indigo-600 text-white px-6 py-3 rounded-md hover:bg-indigo-700 transition duration-200">Go to Home</a>
                </div>
            </div>
        </div>
    <?php elseif (isset($error)): ?>
        <div class="max-w-md w-full space-y-8 p-8">
            <div class="bg-white shadow-lg rounded-lg overflow-hidden card-hover">
                <div class="bg-gradient-to-r from-indigo-600 to-indigo-800 px-6 py-8">
                    <div class="text-center">
                        <h2 class="text-4xl font-bold text-white font-pacifico">
                            Feedback Error
                        </h2>
                    </div>
                </div>
                <div class="px-6 py-8 text-center">
                    <p class="text-xl text-red-700 mb-6"><?php echo $error; ?></p>
                    <a href="feedback.php" class="inline-block bg-indigo-600 text-white px-6 py-3 rounded-md hover:bg-indigo-700 transition duration-200">Try Again</a>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="max-w-md w-full space-y-8 p-8">
            <div class="bg-white shadow-lg rounded-lg overflow-hidden card-hover">
                <div class="bg-gradient-to-r from-indigo-600 to-indigo-800 px-6 py-8">
                    <div class="text-center">
                        <h2 class="text-4xl font-bold text-white font-pacifico">
                            Feedback
                        </h2>
                        <p class="text-indigo-100 mt-2 font-courgette">
                            Share your thoughts with us
                        </p>
                    </div>
                </div>
                <div class="px-6 py-8">
                    <form method="post" class="space-y-6">
                        <div class="space-y-2">
                            <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                            <input id="name" name="name" type="text" required 
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                placeholder="Enter your name">
                        </div>

                        <div class="space-y-2">
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input id="email" name="email" type="email" required 
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                placeholder="Enter your email">
                        </div>

                        <div class="space-y-2">
                            <label for="feedback" class="block text-sm font-medium text-gray-700">Feedback</label>
                            <textarea id="feedback" name="feedback" rows="4" required 
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                placeholder="Enter your feedback"></textarea>
                        </div>

                        <div>
                            <button type="submit" 
                                class="group relative w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 transform hover:scale-105">
                                Submit Feedback
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>
</body>
</html>
