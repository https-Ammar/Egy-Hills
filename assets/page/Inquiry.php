<?php
include 'db.php';

// ✅ Allow project ID to be optional
$project_id = null;
$project = ['title' => 'Booking']; // ✅ Default title
$blocks = [];

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $project_id = intval($_GET['id']);

    // Fetch project title
    $stmt = $conn->prepare("SELECT title FROM projects WHERE id = ?");
    $stmt->bind_param("i", $project_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $fetched = $result->fetch_assoc();
    $stmt->close();

    if ($fetched) {
        $project = $fetched;

        // Fetch project-specific blocks
        $stmt = $conn->prepare("SELECT block_title, block_text, block_image FROM project_blocks WHERE project_id = ?");
        $stmt->bind_param("i", $project_id);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $blocks[] = $row;
        }
        $stmt->close();
    }
}

// ✅ Fetch general info blocks
$info_blocks = [];
$info_query = $conn->query("SELECT title, text, image FROM info_blocks");
while ($row = $info_query->fetch_assoc()) {
    $info_blocks[] = $row;
}

// ✅ Handle booking or inquiry
$booking_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_project'])) {
    $type = $_POST['booking_type'] ?? 'inquiry';
    $client_name = trim($_POST['client_name']);
    $client_phone = trim($_POST['client_phone']);

    if ($type === 'inquiry') {
        if ($client_name && $client_phone) {
            $stmt = $conn->prepare("INSERT INTO visitors (project_id, name, phone, status, created_at) VALUES (?, ?, ?, 'inquiry', NOW())");
            $stmt->bind_param("iss", $project_id, $client_name, $client_phone);
            if ($stmt->execute()) {
                $booking_message = "✅ Inquiry sent successfully!";
            } else {
                $booking_message = "❌ Error sending inquiry.";
            }
            $stmt->close();
        } else {
            $booking_message = "❌ Please enter name and phone number.";
        }
    } elseif ($type === 'visit') {
        $visit_date = trim($_POST['visit_date']);
        $visit_time = trim($_POST['visit_time']);
        $amount = floatval($_POST['amount']);
        $receipt = null;

        if (isset($_FILES['receipt']) && $_FILES['receipt']['error'] === UPLOAD_ERR_OK) {
            $allowed = ['jpg', 'jpeg', 'png', 'pdf'];
            $ext = strtolower(pathinfo($_FILES['receipt']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, $allowed)) {
                $newName = time() . '_' . basename($_FILES['receipt']['name']);
                move_uploaded_file($_FILES['receipt']['tmp_name'], 'uploads/' . $newName);
                $receipt = $newName;
            } else {
                $booking_message = "❌ Invalid file type. Allowed: JPG, PNG, PDF.";
            }
        }

        if ($client_name && $client_phone && $visit_date && $visit_time && $amount) {
            $stmt = $conn->prepare("INSERT INTO visitors (project_id, name, phone, visit_date, visit_time, amount, payment_receipt, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', NOW())");
            $stmt->bind_param("issssds", $project_id, $client_name, $client_phone, $visit_date, $visit_time, $amount, $receipt);
            if ($stmt->execute()) {
                $booking_message = "✅ Visit request sent successfully!";
            } else {
                $booking_message = "❌ Error sending visit request.";
            }
            $stmt->close();
        } else {
            $booking_message = "❌ Please fill in all required fields for the visit.";
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

</head>

<body>

    <?php include './header.php'; ?>
    <?php include './loging.php'; ?>

    <section class="site-banner site-banner--bg site-banner--page" style="background-image:url(../img/cover.jpg);">
        <div class="site-banner__txt section section--medium txt-center post-styles">
            <h1 class="site-banner__title"><a href="#">Inquiry</a> / <a href="#">Home</a></h1>
            <h2 class="site-banner__subtitle">Homes that move you</h2>
        </div>
    </section>

    <article class="card mt-5 ">
        <div class="container">
            <div class="card-body">
                <div class="card-title">
                    <h2>Payment</h2>
                </div>
                <div class="payment-type">
                    <h4>Choose payment method below</h4>
                    <div class="types flex justify-space-between">
                        <?php if ($info_blocks): ?>
                            <?php foreach ($info_blocks as $info): ?>
                                <div class="type selected">
                                    <div class="text">
                                        <?php if ($info['image']): ?>
                                            <img src="uploads/<?= htmlspecialchars($info['image']) ?>" alt="Info Image">
                                        <?php endif; ?>
                                        <p><?= htmlspecialchars($info['title']) ?></p>
                                        <p><?= nl2br(htmlspecialchars($info['text'])) ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>No general info blocks found.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="payment-info flex justify-space-between">
                    <div class="column billing">
                        <div class="title">
                            <div class="num">1</div>
                            <h4>Booking Info</h4>
                        </div>
                        <div class="flex-end">
                            <button type="button" class="button booking-btn" data-type="inquiry">Inquiry</button>
                            <button type="button" class="button booking-btn" data-type="visit">Visit</button>
                        </div>
                        <form method="post" enctype="multipart/form-data">
                            <input type="hidden" name="booking_type" id="booking_type">
                            <div class="field full">
                                <label>Full Name</label>
                                <input type="text" name="client_name" placeholder="Full Name" required>
                            </div>
                            <div class="field full">
                                <label>Phone Number</label>
                                <input type="tel" name="client_phone" placeholder="Phone Number" required>
                            </div>

                            <div id="visit_fields" style="display:none;">
                                <div class="field full">
                                    <label>Booking Date</label>
                                    <input type="date" name="visit_date">
                                </div>
                                <div class="field full">
                                    <label>Booking Time</label>
                                    <input type="time" name="visit_time">
                                </div>
                                <div class="field full">
                                    <label>Amount</label>
                                    <input type="number" name="amount" step="0.01">
                                </div>
                                <div class="field full">
                                    <label>Upload Payment Receipt</label>
                                    <input type="file" name="receipt" accept=".jpg,.jpeg,.png,.pdf">
                                </div>
                            </div>

                            <button class="button button-secondary" type="submit" name="book_project">Submit
                                Booking</button>
                        </form>
                    </div>


                    <div class="column billing bg" style="background-image: url(../img/pexels-heyho.jpg);">
                        <style>
                            .column.billing.bg {
                                background: red;
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

    <section id="footer"></section>

    <script>
        const buttons = document.querySelectorAll('.booking-btn');
        const bookingType = document.getElementById('booking_type');
        const visitFields = document.getElementById('visit_fields');

        // ✅ اجعل النوع الافتراضي inquiry عند تحميل الصفحة
        bookingType.value = 'inquiry';

        // ✅ حدد زر inquiry افتراضيًا
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

    <script src="../script/footer.js"></script>



</body>

</html>