<?php
include 'db.php';

$project_id = null;
$project = ['title' => 'Booking'];
$blocks = [];

// ✅ Get project details
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $project_id = intval($_GET['id']);

    $stmt = $conn->prepare("SELECT title FROM projects WHERE id = ?");
    $stmt->bind_param("i", $project_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $project = $row;

        $stmt = $conn->prepare("SELECT block_title, block_text, block_image FROM project_blocks WHERE project_id = ?");
        $stmt->bind_param("i", $project_id);
        $stmt->execute();
        $block_result = $stmt->get_result();
        while ($block = $block_result->fetch_assoc()) {
            $blocks[] = $block;
        }
    }
    $stmt->close();
}

// ✅ تعديل الاستعلام للحصول على كل الأعمدة المطلوبة
$info_blocks = [];
$info_query = $conn->query("SELECT username, phone, amount, payment_method, description, image, background_image, created_at FROM info_blocks");
while ($row = $info_query->fetch_assoc()) {
    $info_blocks[] = $row;
}

// ✅ معالجة نموذج الحجز
$booking_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_project'])) {
    $type = $_POST['booking_type'] ?? 'inquiry';
    $client_name = trim($_POST['client_name'] ?? '');
    $client_phone = trim($_POST['client_phone'] ?? '');

    if ($type === 'inquiry') {
        if ($client_name && $client_phone) {
            $stmt = $conn->prepare("INSERT INTO visitors (project_id, name, phone, status, created_at) VALUES (?, ?, ?, 'inquiry', NOW())");
            $stmt->bind_param("iss", $project_id, $client_name, $client_phone);
            $booking_message = $stmt->execute() ? "✅ Inquiry sent successfully!" : "❌ Error sending inquiry.";
            $stmt->close();
        } else {
            $booking_message = "❌ Please enter name and phone number.";
        }
    } elseif ($type === 'visit') {
        $visit_date = trim($_POST['visit_date'] ?? '');
        $visit_time = trim($_POST['visit_time'] ?? '');
        $amount = floatval($_POST['amount'] ?? 0);
        $receipt = null;

        if (isset($_FILES['receipt']) && $_FILES['receipt']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['receipt']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'pdf'];
            if (in_array($ext, $allowed)) {
                $newName = time() . '_' . basename($_FILES['receipt']['name']);
                move_uploaded_file($_FILES['receipt']['tmp_name'], 'uploads/' . $newName);
                $receipt = $newName;
            } else {
                $booking_message = "❌ Invalid file type. Allowed: JPG, PNG, PDF.";
            }
        }

        if ($client_name && $client_phone && $visit_date && $visit_time && $amount && !$booking_message) {
            $stmt = $conn->prepare("INSERT INTO visitors (project_id, name, phone, visit_date, visit_time, amount, payment_receipt, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', NOW())");
            $stmt->bind_param("issssds", $project_id, $client_name, $client_phone, $visit_date, $visit_time, $amount, $receipt);
            $booking_message = $stmt->execute() ? "✅ Visit request sent successfully!" : "❌ Error sending visit request.";
            $stmt->close();
        } else {
            if (!$booking_message) {
                $booking_message = "❌ Please fill in all required fields for the visit.";
            }
        }
    }
}
?>

<!-- ✅ عرض معلومات البلوكات -->
<div class="container my-4">
    <h3 class="mb-3">Info Blocks</h3>
    <div class="row">
        <?php foreach ($info_blocks as $block): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <?php if (!empty($block['image']) && file_exists("uploads/{$block['image']}")): ?>
                        <img src="uploads/<?= htmlspecialchars($block['image']) ?>" class="card-img-top" alt="Block Image">
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($block['username']) ?> -
                            <?= htmlspecialchars($block['phone']) ?></h5>
                        <p class="mb-1"><strong>Amount:</strong> <?= number_format($block['amount'], 2) ?></p>
                        <p class="mb-1"><strong>Payment Method:</strong> <?= htmlspecialchars($block['payment_method']) ?>
                        </p>
                        <p class="mb-2">
                            <strong>Description:</strong><br><?= nl2br(htmlspecialchars($block['description'])) ?></p>
                        <?php if (!empty($block['background_image']) && file_exists("uploads/{$block['background_image']}")): ?>
                            <img src="uploads/<?= htmlspecialchars($block['background_image']) ?>"
                                class="img-fluid mt-2 rounded" alt="Background Image">
                        <?php endif; ?>
                        <small class="text-muted d-block mt-2">Added at:
                            <?= htmlspecialchars($block['created_at']) ?></small>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($project['title']) ?> - Booking</title>
    <link rel="stylesheet" href="../css/page.css">
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/booking.css">

    <script
        src="https://www.rj-investments.co.uk/wp-content/themes/rj-investments/assets/js/min/jquery.min.js?ver=2.2.4"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo&display=swap" rel="stylesheet">


</head>

<body>

    <?php include './header.php'; ?>
    <?php include './loging.php'; ?>

    <section class="site-banner site-banner--bg site-banner--page" style="background-image:url(../img/cover.jpg);">
        <div class="site-banner__txt section section--medium txt-center post-styles">
            <h1 class="site-banner__title">
                <a href="#" data-translate>Inquiry</a> /
                <a href="#" data-translate>Home</a>
            </h1>
            <h2 class="site-banner__subtitle" data-translate>Homes that move you</h2>
        </div>
    </section>

    <article class="card mt-5">
        <div class="container">
            <div class="card-body no-rtl">
                <div class="card-title">
                    <h2 data-translate>Payment</h2>
                </div>

                <div class="payment-type mb-4">
                    <h4 data-translate>Choose a payment method below</h4>
                    <div class="types flex justify-space-between flex-wrap">
                        <?php if ($info_blocks): ?>
                            <?php foreach ($info_blocks as $info): ?>
                                <div class="type selected mb-3">
                                    <div class="text">
                                        <?php if ($info['image']): ?>
                                            <img src="uploads/<?= htmlspecialchars($info['image']) ?>" alt="Info Image">
                                        <?php endif; ?>
                                        <p class="fw-bold"><?= htmlspecialchars($info['title']) ?></p>
                                        <p><?= nl2br(htmlspecialchars($info['text'])) ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p data-translate>No general info blocks found.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="payment-info flex justify-space-between flex-wrap">
                    <div class="column billing">
                        <div class="title mb-3">
                            <div class="num">1</div>
                            <h4 data-translate>Booking Info</h4>
                        </div>

                        <div class="flex-end mb-4">
                            <button type="button" class="button booking-btn" data-type="inquiry"
                                data-translate>Inquiry</button>
                            <button type="button" class="button booking-btn" data-type="visit"
                                data-translate>Visit</button>
                        </div>

                        <form method="post" enctype="multipart/form-data">
                            <input type="hidden" name="booking_type" id="booking_type">

                            <div class="field full mb-3">
                                <label data-translate>Full Name</label>
                                <input type="text" name="client_name" placeholder="Full Name" required>
                            </div>

                            <div class="field full mb-3">
                                <label data-translate>Phone Number</label>
                                <input type="tel" name="client_phone" placeholder="Phone Number" required>
                            </div>

                            <div id="visit_fields" style="display:none;">
                                <div class="field full mb-3">
                                    <label data-translate>Booking Date</label>
                                    <input type="date" name="visit_date">
                                </div>

                                <div class="field full mb-3">
                                    <label data-translate>Booking Time</label>
                                    <input type="time" name="visit_time">
                                </div>

                                <div class="field full mb-3">
                                    <label data-translate>Amount</label>
                                    <input type="number" name="amount" step="0.01">
                                </div>

                                <div class="field full mb-3">
                                    <label data-translate>Upload Payment Receipt</label>
                                    <input type="file" name="receipt" accept=".jpg,.jpeg,.png,.pdf">
                                </div>
                            </div>

                            <button class="button button-secondary mt-3" type="submit" name="book_project"
                                data-translate>
                                Submit Booking
                            </button>
                        </form>
                    </div>

                    <div class="column billing bg" style="background-image: url(../img/pexels-heyho.jpg);">
                        <style>
                            .column.billing.bg {
                                background-color: red;
                                height: 100%;
                                border-radius: 10px;
                                background-size: cover;
                                background-repeat: no-repeat;
                            }
                        </style>
                    </div>
                </div>
            </div>
        </div>
    </article>

    <?php if ($booking_message): ?>
        <p><?= htmlspecialchars($booking_message) ?></p>
    <?php endif; ?>

    <?php include './footer.php'; ?>
    <script src="../script/app.js"></script>

    <script>
        const buttons = document.querySelectorAll('.booking-btn');
        const bookingType = document.getElementById('booking_type');
        const visitFields = document.getElementById('visit_fields');

        bookingType.value = 'inquiry';

        buttons.forEach(b => {
            if (b.dataset.type === 'inquiry') {
                b.classList.add('selected');
            }
        });

        buttons.forEach(btn => {
            btn.addEventListener('click', () => {
                const type = btn.dataset.type;
                bookingType.value = type;

                if (type === 'visit') {
                    visitFields.style.display = 'block';
                } else {
                    visitFields.style.display = 'none';
                }

                buttons.forEach(b => b.classList.remove('selected'));
                btn.classList.add('selected');
            });
        });
    </script>




</body>

</html>