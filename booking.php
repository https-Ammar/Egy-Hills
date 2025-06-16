<?php
include 'db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('معرف المشروع غير صحيح.');
}

$project_id = intval($_GET['id']);

// جلب بيانات المشروع
$stmt = $conn->prepare("SELECT title FROM projects WHERE id = ?");
$stmt->bind_param("i", $project_id);
$stmt->execute();
$result = $stmt->get_result();
$project = $result->fetch_assoc();
$stmt->close();

if (!$project) {
    die('المشروع غير موجود.');
}

// جلب بلوكات المشروع
$blocks = [];
$stmt = $conn->prepare("SELECT block_title, block_text, block_image FROM project_blocks WHERE project_id = ?");
$stmt->bind_param("i", $project_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $blocks[] = $row;
}
$stmt->close();

$booking_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_project'])) {
    $type = $_POST['booking_type'];
    $client_name = trim($_POST['client_name']);
    $client_phone = trim($_POST['client_phone']);

    if ($type === 'inquiry') {
        // استفسار فقط
        if ($client_name && $client_phone) {
            $stmt = $conn->prepare("INSERT INTO visitors 
                (project_id, name, phone, status, created_at)
                VALUES (?, ?, ?, 'inquiry', NOW())");
            $stmt->bind_param("iss", $project_id, $client_name, $client_phone);
            if ($stmt->execute()) {
                $booking_message = "✅ تم إرسال الاستفسار بنجاح!";
            } else {
                $booking_message = "❌ حدث خطأ أثناء إرسال الاستفسار.";
            }
            $stmt->close();
        } else {
            $booking_message = "❌ الرجاء إدخال الاسم ورقم الهاتف.";
        }
    } elseif ($type === 'visit') {
        // معاينة مع دفع
        $visit_date = trim($_POST['visit_date']);
        $visit_time = trim($_POST['visit_time']);
        $amount = floatval($_POST['amount']);

        // رفع إيصال الدفع
        $receipt = null;
        if (isset($_FILES['receipt']) && $_FILES['receipt']['error'] === UPLOAD_ERR_OK) {
            $allowed = ['jpg', 'jpeg', 'png', 'pdf'];
            $ext = strtolower(pathinfo($_FILES['receipt']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, $allowed)) {
                $newName = time() . '_' . basename($_FILES['receipt']['name']);
                move_uploaded_file($_FILES['receipt']['tmp_name'], 'uploads/' . $newName);
                $receipt = $newName;
            } else {
                $booking_message = "❌ نوع الملف غير مسموح به. الملفات المسموحة: JPG, PNG, PDF.";
            }
        }

        if ($client_name && $client_phone && $visit_date && $visit_time && $amount) {
            $stmt = $conn->prepare("INSERT INTO visitors 
                (project_id, name, phone, visit_date, visit_time, amount, payment_receipt, status, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', NOW())");
            $stmt->bind_param("issssds", $project_id, $client_name, $client_phone, $visit_date, $visit_time, $amount, $receipt);
            if ($stmt->execute()) {
                $booking_message = "✅ تم إرسال طلب المعاينة بنجاح!";
            } else {
                $booking_message = "❌ حدث خطأ أثناء إرسال طلب المعاينة.";
            }
            $stmt->close();
        } else {
            $booking_message = "❌ الرجاء ملء جميع الحقول المطلوبة للمعاينة.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="UTF-8">
    <title>حجز معاينة أو استفسار - <?= htmlspecialchars($project['title']) ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: auto;
            padding: 20px;
            direction: rtl;
        }

        .booking-form {
            border: 1px solid #ddd;
            padding: 20px;
            margin-top: 20px;
        }

        .booking-form input,
        .booking-form select,
        .booking-form button {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
        }

        .message {
            color: green;
            margin: 10px 0;
        }

        .error {
            color: red;
        }

        .project-block {
            border: 1px solid #ccc;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .project-block h3 {
            margin-top: 0;
        }

        .project-block img {
            max-width: 100%;
            height: auto;
            margin-top: 10px;
            border-radius: 4px;
        }
    </style>
    <script>
        function toggleFields() {
            var type = document.getElementById('booking_type').value;
            var extraFields = document.getElementById('extra-fields');
            if (type === 'visit') {
                extraFields.style.display = 'block';
                // إضافة required عند التحويل إلى معاينة
                document.querySelector('input[name="visit_date"]').required = true;
                document.querySelector('input[name="visit_time"]').required = true;
                document.querySelector('input[name="amount"]').required = true;
            } else {
                extraFields.style.display = 'none';
                // إزالة required عند التحويل إلى استفسار
                document.querySelector('input[name="visit_date"]').required = false;
                document.querySelector('input[name="visit_time"]').required = false;
                document.querySelector('input[name="amount"]').required = false;
            }
        }
    </script>
</head>

<body>

    <h1>حجز معاينة أو إرسال استفسار لـ: <?= htmlspecialchars($project['title']) ?></h1>

    <?php if (!empty($blocks)): ?>
        <h2>معلومات إضافية عن المشروع</h2>
        <?php foreach ($blocks as $block): ?>
            <div class="project-block">
                <h3><?= htmlspecialchars($block['block_title']) ?></h3>
                <p><?= nl2br(htmlspecialchars($block['block_text'])) ?></p>
                <?php if ($block['block_image']): ?>
                    <img src="uploads/<?= htmlspecialchars($block['block_image']) ?>"
                        alt="<?= htmlspecialchars($block['block_title']) ?>">
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <?php if ($booking_message): ?>
        <p class="<?= strpos($booking_message, '✅') !== false ? 'message' : 'error' ?>">
            <?= htmlspecialchars($booking_message) ?>
        </p>
    <?php endif; ?>

    <div class="booking-form">
        <form method="post" enctype="multipart/form-data">
            <label>نوع الطلب:</label>
            <select name="booking_type" id="booking_type" onchange="toggleFields()" required>
                <option value="visit">معاينة</option>
                <option value="inquiry">استفسار</option>
            </select>

            <label>الاسم الكامل:</label>
            <input type="text" name="client_name" placeholder="اسمك الكامل" required>

            <label>رقم الهاتف:</label>
            <input type="tel" name="client_phone" placeholder="رقم هاتفك" required>

            <div id="extra-fields">
                <label>تاريخ المعاينة:</label>
                <input type="date" name="visit_date" required>

                <label>وقت المعاينة:</label>
                <input type="time" name="visit_time" required>

                <label>قيمة المبلغ:</label>
                <input type="number" name="amount" step="0.01" required>

                <label>إيصال الدفع (اختياري):</label>
                <input type="file" name="receipt" accept=".jpg,.jpeg,.png,.pdf">
            </div>

            <button type="submit" name="book_project">إرسال</button>
        </form>
    </div>

    <a class="back-link" href="project_details.php?id=<?= $project_id ?>">عودة لتفاصيل المشروع</a>

    <script>
        toggleFields(); // لتشغيلها عند التحميل
    </script>

</body>

</html>