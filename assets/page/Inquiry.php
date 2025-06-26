<?php
include 'db.php';

$project_id = null;
$project = ['title' => 'Booking'];
$blocks = [];

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $project_id = intval($_GET['id']);

    $stmt = $conn->prepare("SELECT title FROM projects WHERE id = ?");
    $stmt->bind_param("i", $project_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $project = $row;

        $stmt_blocks = $conn->prepare("SELECT block_title, block_text, block_image FROM project_blocks WHERE project_id = ?");
        $stmt_blocks->bind_param("i", $project_id);
        $stmt_blocks->execute();
        $block_result = $stmt_blocks->get_result();
        while ($block = $block_result->fetch_assoc()) {
            $blocks[] = $block;
        }
        $stmt_blocks->close();
    }
    $stmt->close();
}

$info_blocks = [];
$info_query = $conn->query("SELECT username, phone, amount, payment_method, description, image, created_at FROM info_blocks");
while ($row = $info_query->fetch_assoc()) {
    $info_blocks[] = $row;
}

$booking_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_project'])) {
    $type = $_POST['booking_type'] ?? 'inquiry';
    $client_name = trim($_POST['client_name'] ?? '');
    $client_phone = trim($_POST['client_phone'] ?? '');

    if ($type === 'inquiry') {
        if ($client_name && $client_phone) {
            $stmt = $conn->prepare("INSERT INTO visitors (project_id, name, phone, status, created_at) VALUES (?, ?, ?, 'inquiry', NOW())");
            $stmt->bind_param("iss", $project_id, $client_name, $client_phone);
            if ($stmt->execute()) {
                $stmt->close();
                header("Location: /Egy-Hills/index.php");
                exit();
            } else {
                $booking_message = "❌ Error sending inquiry.";
                $stmt->close();
            }
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
                $fullPath = '/Applications/MAMP/htdocs/Egy-Hills/uploads/' . $newName;
                if (move_uploaded_file($_FILES['receipt']['tmp_name'], $fullPath)) {
                    $receipt = $newName;
                } else {
                    $booking_message = "❌ Failed to move uploaded file.";
                }
            } else {
                $booking_message = "❌ Invalid file type. Allowed: JPG, PNG, PDF.";
            }
        }

        if ($client_name && $client_phone && $visit_date && $visit_time && $amount > 0 && !$booking_message) {
            $stmt = $conn->prepare("INSERT INTO visitors (project_id, name, phone, visit_date, visit_time, amount, payment_receipt, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', NOW())");
            $stmt->bind_param("issssds", $project_id, $client_name, $client_phone, $visit_date, $visit_time, $amount, $receipt);
            if ($stmt->execute()) {
                $stmt->close();
                header("Location: /Egy-Hills/index.php");
                exit();
            } else {
                $booking_message = "❌ Error sending visit request.";
                $stmt->close();
            }
        } else {
            if (!$booking_message) {
                $booking_message = "❌ Please fill in all required fields for the visit.";
            }
        }
    }
}
?>




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
    <style>
        .card-box {
            background-color: #f9f9f9;
            border: 2px solid #eee;
            border-radius: 20px;
            padding: 20px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;

            max-width: 600px;
        }

        .card-number {
            display: flex;
            align-items: center;
            gap: 20px;
            font-size: 28px;
            font-weight: 500;
            color: #555;
            flex-grow: 1;
        }

        .mastercard-logo {
            width: 40px;
            margin-right: 10px;
        }

        .copy-icon {
            cursor: pointer;
            font-size: 22px;
            color: #333;
        }

        .copy-icon:hover {
            color: #007bff;
        }

        .bg-light.p-3.rounded {
            padding: 25px !important;
            border-radius: 10px !important;
        }

        input.form-control {
            padding: 15px;
            border: 2px solid #eeeeee;
        }

        button.btn.btn-secondary.mt-3 {
            padding: 15px;
            border-radius: 46px;
            background: black;
            border: navajowhite;
            letter-spacing: 0;
        }

        .d-flex.justify-content-end.mb-4.gap-2 {
            justify-content: start !important;
        }

        button.btn.btn-primary.booking-btn {
            background: #f8f9fa;
            border: navajowhite;
            padding: 10px 40px;
            border-radius: 30px;
            color: black;
            font-weight: bold;
            letter-spacing: 0;
        }

        label {
            margin-bottom: 1.5vh;
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.min.css" rel="stylesheet">
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

    <article class="card mt-5 mb-5">
        <div class="container">
            <div class="card-body no-rtl">
                <div class="card-title">
                    <h2 data-translate>Payment</h2>
                </div>

                <div class="payment-type mb-4">
                    <h4 data-translate>Choose a payment method below</h4>
                    <div class="types flex justify-space-between flex-wrap"></div>
                </div>

                <div class="container">
                    <div class="row g-4">
                        <div class="col-12 col-md-6">
                            <div class="d-flex justify-content-end mb-4 gap-2">
                                <button type="button" class="btn btn-primary booking-btn" data-type="inquiry"
                                    data-translate>Inquiry</button>
                                <button type="button" class="btn btn-primary booking-btn" data-type="visit"
                                    data-translate>Visit</button>
                            </div>

                            <form method="post" enctype="multipart/form-data">
                                <input type="hidden" name="booking_type" id="booking_type">

                                <div class="mb-3">
                                    <label data-translate>Full Name</label>
                                    <input type="text" name="client_name" placeholder="Full Name" class="form-control"
                                        required>
                                </div>

                                <div class="mb-3">
                                    <label data-translate>Phone Number</label>
                                    <input type="tel" name="client_phone" placeholder="Phone Number"
                                        class="form-control" required>
                                </div>

                                <div id="visit_fields" style="display:none;">
                                    <div class="mb-3">
                                        <label data-translate>Booking Date</label>
                                        <input type="date" name="visit_date" class="form-control">
                                    </div>

                                    <div class="mb-3">
                                        <label data-translate>Booking Time</label>
                                        <input type="time" name="visit_time" class="form-control">
                                    </div>

                                    <div class="mb-3">
                                        <label data-translate>Amount</label>
                                        <input type="number" name="amount" step="1" class="form-control">
                                    </div>

                                    <div class="mb-3">
                                        <label data-translate>Upload Payment Receipt</label>
                                        <input type="file" name="receipt" accept=".jpg,.jpeg,.png,.pdf"
                                            class="form-control">
                                    </div>
                                </div>

                                <button class="btn btn-secondary mt-3" type="submit" name="book_project" data-translate>
                                    Submit Booking
                                </button>
                            </form>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="bg-light p-3 rounded">
                                <?php if ($info_blocks): ?>
                                    <div class="row">
                                        <?php foreach ($info_blocks as $block): ?>
                                            <?php if (!empty($block['amount']) && $block['amount'] != 0): ?>
                                                <h2 class="mb-3"> . <?= number_format($block['amount'], 2) ?> <sub
                                                        style="font-size: small;">EGP</sub></h2>
                                                <hr class="mb-3">
                                            <?php endif; ?>



                                            <div class="card-box d-flex justify-content-between align-items-center mb-3">
                                                <div class="d-flex align-items-center">
                                                    <?php if (!empty($block['image'])): ?>
                                                        <?php $imagePath = $_SERVER['DOCUMENT_ROOT'] . "/Egy-Hills/uploads/{$block['image']}"; ?>
                                                        <?php if (file_exists($imagePath)): ?>
                                                            <img src="/Egy-Hills/uploads/<?= htmlspecialchars($block['image']) ?>"
                                                                alt="MasterCard" class="me-2" width="50">
                                                        <?php endif; ?>
                                                    <?php endif; ?>

                                                    <?php if (!empty($block['phone'])): ?>
                                                        <span class="number-text"> <?= htmlspecialchars($block['phone']) ?></span>

                                                    <?php endif; ?>

                                                    <span class="number-text">
                                                        <?= htmlspecialchars($block['username'] ?? $block['title'] ?? '') ?></span>
                                                </div>
                                                <i class="bi bi-clipboard copy-icon" onclick="copyCardNumber(this)"
                                                    title="Copy"></i>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <p class="text-danger">No general info blocks found.</p>
                                <?php endif; ?>
                            </div>

                            <script>
                                function copyCardNumber(iconElement) {
                                    const cardBox = iconElement.closest('.card-box');
                                    const numberText = cardBox.querySelector('.number-text').innerText.trim();
                                    navigator.clipboard.writeText(numberText).then(() => {
                                        alert("تم نسخ رقم البطاقة!");
                                    });
                                }
                            </script>
                        </div>
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