<?php
include 'db.php';

// Validate project ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid project ID.');
}

$project_id = intval($_GET['id']);

// ✅ Fetch full project card data
$stmt = $conn->prepare("SELECT * FROM projects WHERE id = ?");
$stmt->bind_param("i", $project_id);
$stmt->execute();
$project = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$project) {
    die('Project not found.');
}

// ✅ Fetch general info blocks
$info_blocks = [];
$result = $conn->query("SELECT title, text, image FROM info_blocks");
while ($row = $result->fetch_assoc()) {
    $info_blocks[] = $row;
}

// ✅ Handle booking or inquiry
$booking_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_project'])) {
    $type = $_POST['booking_type'] ?? '';
    $client_name = trim($_POST['client_name'] ?? '');
    $client_phone = trim($_POST['client_phone'] ?? '');

    if ($client_name && $client_phone) {
        if ($type === 'inquiry') {
            $stmt = $conn->prepare("INSERT INTO visitors (project_id, name, phone, status, created_at) VALUES (?, ?, ?, 'inquiry', NOW())");
            $stmt->bind_param("iss", $project_id, $client_name, $client_phone);
            $booking_message = $stmt->execute() ? "✅ Inquiry sent successfully!" : "❌ Error sending inquiry.";
            $stmt->close();
        } elseif ($type === 'visit') {
            $visit_date = trim($_POST['visit_date'] ?? '');
            $visit_time = trim($_POST['visit_time'] ?? '');
            $amount = floatval($_POST['amount'] ?? 0);
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

            if ($visit_date && $visit_time && $amount && !$booking_message) {
                $stmt = $conn->prepare("INSERT INTO visitors (project_id, name, phone, visit_date, visit_time, amount, payment_receipt, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', NOW())");
                $stmt->bind_param("issssds", $project_id, $client_name, $client_phone, $visit_date, $visit_time, $amount, $receipt);
                $booking_message = $stmt->execute() ? "✅ Visit request sent successfully!" : "❌ Error sending visit request.";
                $stmt->close();
            } elseif (!$booking_message) {
                $booking_message = "❌ Please fill in all required fields for the visit.";
            }
        } else {
            $booking_message = "❌ Invalid booking type.";
        }
    } else {
        $booking_message = "❌ Please enter name and phone number.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($project['title']) ?> - Booking</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/booking.css">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
</head>

<body>

    <?php include './header.php'; ?>

    <section class="site-banner site-banner--bg site-banner--page"
        style="background-image:url(uploads/1750093639_20250524083242.webp);">
        <div class="site-banner__txt section section--medium txt-center post-styles">
            <h1 class="site-banner__title"><a href="#">About</a> / <a href="#">Home</a></h1>
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
                                <div class="type">
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

                    <div class="cccc">
                        <div class="row">
                            <div class="title mb-3 ">
                                <div class="num">1</div>
                                <h4>Booking Info</h4>
                            </div>
                            <div class="col-12 mb-4">
                                <a href="project_details.php?id=<?= $project['id'] ?>">
                                    <div class="property-card">
                                        <div class="cover_card"
                                            style="background-image: url('uploads/<?= htmlspecialchars($project['image']) ?>');">
                                        </div>
                                        <div class="property-card-content">
                                            <p class="property-card-location">
                                                <?= htmlspecialchars($project['location'] ?? 'No Location') ?></p>
                                            <div class="d-flex align-items-center justify-content-between">
                                                <h3 class="property-card-title">
                                                    <?= htmlspecialchars($project['title']) ?></h3>
                                                <h3 class="property-card-title">
                                                    <?= htmlspecialchars($project['price']) ?></h3>
                                            </div>
                                            <div class="property-card-features">
                                                <div class="property-card-feature">
                                                    <?= htmlspecialchars($project['beds']) ?> Beds</div>
                                                <div class="property-card-feature">
                                                    <?= htmlspecialchars($project['baths']) ?> Baths</div>
                                                <div class="property-card-feature">
                                                    <?= htmlspecialchars($project['area']) ?> sqm</div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
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

        // ✅ عيّن القيمة الافتراضية عند التحميل (Inquiry)
        bookingType.value = 'inquiry';
        visitFields.style.display = 'none';

        // ✅ ميّز زر Inquiry افتراضيًا
        buttons.forEach(btn => {
            if (btn.dataset.type === 'inquiry') {
                btn.classList.add('selected');
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