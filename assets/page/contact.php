<?php
// استدعاء PHPMailer عبر Composer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// حمل كل المكتبات تلقائياً
require __DIR__ . '/vendor/autoload.php';

// نتيجة الإرسال
$message_sent = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = strip_tags(trim($_POST["name"] ?? ""));
    $email = filter_var(trim($_POST["email"] ?? ""), FILTER_SANITIZE_EMAIL);
    $message = trim($_POST["message"] ?? "");

    if ($name && filter_var($email, FILTER_VALIDATE_EMAIL) && $message) {
        $mail = new PHPMailer(true);

        try {
            // إعدادات SMTP
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'ammar132004@gmail.com'; // ✏️ بريدك
            $mail->Password = 'okcejzwtuepbqgyq';     // ✏️ كلمة مرور التطبيق الخاصة
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;

            // من وإلى
            $mail->setFrom($email, $name);
            $mail->addAddress('ammar132004@gmail.com', 'Ammar');

            // المحتوى
            $mail->isHTML(false);
            $mail->Subject = "New Contact Message from $name";
            $mail->Body = "Name: $name\nEmail: $email\n\nMessage:\n$message";

            $mail->send();
            $message_sent = "Thank you! Your message has been sent successfully.";
        } catch (Exception $e) {
            $message_sent = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        $message_sent = "Please fill in all fields correctly.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Contact Us SMTP</title>
    <script src="https://kit.fontawesome.com/c32adfdcda.js" crossorigin="anonymous"></script>
    <style>
        body {
            font-family: Arial;
            margin: 0;
            padding: 0;
        }

        .section-header {
            text-align: center;
            padding: 50px 20px;
        }

        .container {
            width: 90%;
            max-width: 1000px;
            margin: auto;
        }

        .row {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .contact-info,
        .contact-form {
            flex: 1 1 300px;
            padding: 20px;
            box-shadow: 0 0 10px #ccc;
            border-radius: 5px;
        }

        .contact-info-item {
            display: flex;
            margin-bottom: 20px;
        }

        .contact-info-icon {
            font-size: 30px;
            margin-right: 15px;
        }

        .input-box {
            position: relative;
            margin-bottom: 20px;
        }

        .input-box input,
        .input-box textarea {
            width: 100%;
            padding: 10px;
            outline: none;
            border: 1px solid #ccc;
            border-radius: 3px;
        }

        .input-box span {
            position: absolute;
            left: 10px;
            top: -20px;
            font-size: 12px;
            color: #555;
        }

        .input-box input[type="submit"] {
            background: #333;
            color: #fff;
            cursor: pointer;
            border: none;
        }

        .message-status {
            margin: 20px 0;
            color: green;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <section>
        <div class="section-header">
            <div class="container">
                <h2>Contact Us</h2>
                <p>Contact us securely using Gmail SMTP.</p>
            </div>
        </div>

        <div class="container">
            <?php if ($message_sent): ?>
                <p class="message-status"><?= htmlspecialchars($message_sent) ?></p>
            <?php endif; ?>

            <div class="row">

                <div class="contact-info">
                    <div class="contact-info-item">
                        <div class="contact-info-icon"><i class="fas fa-home"></i></div>
                        <div class="contact-info-content">
                            <h4>Address</h4>
                            <p>4671 Sugar Camp Road, Owatonna, MN, 55060</p>
                        </div>
                    </div>
                    <div class="contact-info-item">
                        <div class="contact-info-icon"><i class="fas fa-phone"></i></div>
                        <div class="contact-info-content">
                            <h4>Phone</h4>
                            <p>571-457-2321</p>
                        </div>
                    </div>
                    <div class="contact-info-item">
                        <div class="contact-info-icon"><i class="fas fa-envelope"></i></div>
                        <div class="contact-info-content">
                            <h4>Email</h4>
                            <p>ammar132004@gmail.com</p>
                        </div>
                    </div>
                </div>

                <div class="contact-form">
                    <form action="" method="POST" id="contact-form">
                        <h2>Send Message</h2>
                        <div class="input-box">
                            <input type="text" required name="name">
                            <span>Full Name</span>
                        </div>
                        <div class="input-box">
                            <input type="email" required name="email">
                            <span>Email</span>
                        </div>
                        <div class="input-box">
                            <textarea required name="message"></textarea>
                            <span>Type your Message...</span>
                        </div>
                        <div class="input-box">
                            <input type="submit" value="Send">
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </section>
</body>

</html>