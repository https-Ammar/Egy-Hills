<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/vendor/autoload.php';

$message_sent = "";
$error_name = "";
$error_email = "";
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = strip_tags(trim($_POST["name"] ?? ""));
    $email = filter_var(trim($_POST["email"] ?? ""), FILTER_SANITIZE_EMAIL);
    $message = trim($_POST["message"] ?? "");

    if (!$name) {
        $error_name = "Please enter your name.";
    }

    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_email = "Please enter a valid email.";
    }

    if (!$message) {
        $error_message = "Please enter your message.";
    }

    if (!$error_name && !$error_email && !$error_message) {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'ammar132004@gmail.com';
            $mail->Password = 'okcejzwtuepbqgyq'; // استخدم كلمة مرور التطبيق
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;

            $mail->setFrom($email, $name);
            $mail->addAddress('ammar132004@gmail.com', 'Ammar');

            $mail->isHTML(false);
            $mail->Subject = "New Contact Message from $name";
            $mail->Body = "Name: $name\nEmail: $email\n\nMessage:\n$message";

            $mail->send();
            $message_sent = "Thank you! Your message has been sent successfully.";
        } catch (Exception $e) {
            $message_sent = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Contact Us</title>
    <link rel="stylesheet" href="../css/main.css" />
    <link rel="stylesheet" href="../css/style.css" />
    <link rel="stylesheet" href="../css/bootstrap.min.css" />
    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet" />
    <link rel="stylesheet" href="../css/page.css" />
</head>

<body class="Contact">

    <?php include './header.php'; ?>

    <div class="landing_page">
        <div class="responsive-container-block big-container">
            <div class="responsive-container-block container">

                <!-- Left Content -->
                <div class="responsive-cell-block wk-desk-6 wk-ipadp-6 wk-tab-12 wk-mobile-12 left-one">
                    <div class="content-box">
       <p class="text-blk section-head">Get in Touch with Egy-Hills</p>
<p class="text-blk section-subhead">
Whether you're looking to find your dream home, invest in property, or have any real estate inquiries, the professional team at <strong>Egy-Hills Real Estate</strong> is always ready to assist you. Your journey starts here — let’s make it successful together.
</p>

                        <div class="icons-container">
                            <a class="share-icon"><img class="img"
                                    src="https://workik-widget-assets.s3.amazonaws.com/Footer1-83/v1/images/Icon-twitter.png" /></a>
                            <a class="share-icon"><img class="img"
                                    src="https://workik-widget-assets.s3.amazonaws.com/Footer1-83/v1/images/Icon-facebook.png" /></a>
                            <a class="share-icon"><img class="img"
                                    src="https://workik-widget-assets.s3.amazonaws.com/Footer1-83/v1/images/Icon-google.png" /></a>
                            <a class="share-icon"><img class="img"
                                    src="https://workik-widget-assets.s3.amazonaws.com/Footer1-83/v1/images/Icon-instagram.png" /></a>
                        </div>
                    </div>
                </div>

                <!-- Contact Form -->
                <div class="responsive-cell-block wk-desk-6 wk-ipadp-6 wk-tab-12 wk-mobile-12 right-one" id="i1zj">
                    <form action="" method="POST" id="contact-form">
                        <div class="container-block form-wrapper">
                            <p class="text-blk contactus-head">Get a quote</p>
                            <p class="text-blk contactus-subhead">We will get back to you in 24 hours</p>

                            <div class="responsive-container-block">

                                <!-- Name -->
                                <div class="responsive-cell-block wk-tab-12 wk-mobile-12 wk-desk-12 wk-ipadp-12">
                                    <input class="input" type="text" name="name" required placeholder="Full Name"
                                        value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" />
                                    <?php if ($error_name): ?>
                                        <p class="message-status text-danger"><?= htmlspecialchars($error_name) ?></p>
                                    <?php endif; ?>
                                </div>

                                <!-- Email -->
                                <div class="responsive-cell-block wk-tab-12 wk-mobile-12 wk-desk-12 wk-ipadp-12">
                                    <input class="input" type="email" name="email" required placeholder="Email"
                                        value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" />
                                    <?php if ($error_email): ?>
                                        <p class="message-status text-danger"><?= htmlspecialchars($error_email) ?></p>
                                    <?php endif; ?>
                                </div>

                                <!-- Message -->
                                <div class="responsive-cell-block wk-tab-12 wk-mobile-12 wk-desk-12 wk-ipadp-12">
                                    <textarea name="message" required class="textinput"
                                        placeholder="Type message here"><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
                                    <?php if ($error_message): ?>
                                        <p class="message-status text-danger"><?= htmlspecialchars($error_message) ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <button class="submit-btn">Get quote</button>

                            <!-- Overall message (success or failure) -->
                            <?php if ($message_sent): ?>
                                <p class="message-status mt-3"><?= htmlspecialchars($message_sent) ?></p>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

</body>

</html>