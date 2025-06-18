<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // استقبل البيانات
    $name = strip_tags(trim($_POST["name"]));
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $message = trim($_POST["message"]);

    // التحقق من البيانات
    if (empty($name) || !filter_var($email, FILTER_VALIDATE_EMAIL) || empty($message)) {
        die("Please complete the form correctly.");
    }

    // الإيميل الذي سيستقبل الرسالة
    $to = "ntamerrwael@mfano.ga"; // غيّره إلى إيميلك

    // الموضوع
    $subject = "New Contact Message from $name";

    // محتوى الرسالة
    $email_content = "Name: $name\n";
    $email_content .= "Email: $email\n\n";
    $email_content .= "Message:\n$message\n";

    // رؤوس الإيميل
    $email_headers = "From: $name <$email>";

    // أرسل الإيميل
    if (mail($to, $subject, $email_content, $email_headers)) {
        echo "Thank you! Your message has been sent.";
    } else {
        echo "Sorry, something went wrong. Please try again later.";
    }
} else {
    // إذا دخل على الصفحة مباشرة بدون POST
    die("Invalid request.");
}
?>