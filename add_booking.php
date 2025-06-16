<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $project_id = intval($_POST['project_id'] ?? 0);
    $payment_method_id = isset($_POST['payment_method_id']) ? intval($_POST['payment_method_id']) : null;
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $visit_date = $_POST['visit_date'] ?? null;
    $visit_time = $_POST['visit_time'] ?? null;
    $amount = floatval($_POST['amount'] ?? 0);

    // رفع ملف الإيصال (اختياري)
    $payment_receipt_name = null;
    if (isset($_FILES['payment_receipt']) && $_FILES['payment_receipt']['error'] === UPLOAD_ERR_OK) {
        $tmp_name = $_FILES['payment_receipt']['tmp_name'];
        $original_name = basename($_FILES['payment_receipt']['name']);
        $ext = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];

        if (in_array($ext, $allowed_ext)) {
            $new_name = time() . '_' . uniqid() . '.' . $ext;
            $upload_dir = __DIR__ . '/uploads/';
            if (!is_dir($upload_dir))
                mkdir($upload_dir, 0755, true);
            $destination = $upload_dir . $new_name;
            if (move_uploaded_file($tmp_name, $destination)) {
                $payment_receipt_name = $new_name;
            } else {
                die("خطأ أثناء رفع ملف الإيصال.");
            }
        } else {
            die("نوع ملف الإيصال غير مدعوم.");
        }
    }

    // تحقق من القيم الأساسية
    if ($project_id === 0 || empty($name) || empty($phone) || empty($visit_date) || empty($visit_time)) {
        die("يرجى ملء جميع الحقول الأساسية.");
    }

    $stmt = $conn->prepare("INSERT INTO visitors (project_id, payment_method_id, name, phone, visit_date, visit_time, amount, payment_receipt, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())");
    $stmt->bind_param("iissssds", $project_id, $payment_method_id, $name, $phone, $visit_date, $visit_time, $amount, $payment_receipt_name);

    if ($stmt->execute()) {
        echo "تم إرسال طلب الزيارة بنجاح.";
    } else {
        echo "خطأ في حفظ البيانات: " . $stmt->error;
    }

    $stmt->close();

} else {
    echo "يرجى إرسال الطلب عبر النموذج.";
}
?>