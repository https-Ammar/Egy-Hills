<?php
include 'db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid project ID.');
}

$project_id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT * FROM projects WHERE id = ?");
$stmt->bind_param("i", $project_id);
$stmt->execute();
$project = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$project) {
    die('Project not found.');
}

// جلب بيانات info_blocks كاملة
$info_blocks = [];
$result = $conn->query("SELECT * FROM info_blocks ORDER BY id DESC");
while ($row = $result->fetch_assoc()) {
    $info_blocks[] = $row;
}

$booking_message = '';
$booking_class = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_project'])) {
    $type = $_POST['booking_type'] ?? '';
    $client_name = trim($_POST['client_name'] ?? '');
    $client_phone = trim($_POST['client_phone'] ?? '');

    if ($client_name && $client_phone) {
        if ($type === 'inquiry') {
            $stmt = $conn->prepare("INSERT INTO visitors (project_id, name, phone, status, created_at) VALUES (?, ?, ?, 'inquiry', NOW())");
            $stmt->bind_param("iss", $project_id, $client_name, $client_phone);
            if ($stmt->execute()) {
                header("Location: " . $_SERVER['REQUEST_URI'] . "&status=inquiry_success");
                exit;
            } else {
                $booking_message = "Error sending inquiry.";
                $booking_class = "alert alert-danger";
            }
            $stmt->close();
        } elseif ($type === 'visit') {
            $visit_date = trim($_POST['visit_date'] ?? '');
            $visit_time = trim($_POST['visit_time'] ?? '');
            $amount = intval($_POST['amount'] ?? 0);
            $receipt = null;

            if (isset($_FILES['receipt']) && $_FILES['receipt']['error'] === UPLOAD_ERR_OK) {
                $allowed = ['jpg', 'jpeg', 'png', 'pdf'];
                $ext = strtolower(pathinfo($_FILES['receipt']['name'], PATHINFO_EXTENSION));
                if (in_array($ext, $allowed)) {
                    $newName = time() . '_' . basename($_FILES['receipt']['name']);
                    move_uploaded_file($_FILES['receipt']['tmp_name'], 'uploads/' . $newName);
                    $receipt = $newName;
                } else {
                    $booking_message = "Invalid file type. Allowed: JPG, PNG, PDF.";
                    $booking_class = "alert alert-warning";
                }
            }

            if ($visit_date && $visit_time && $amount && !$booking_message) {
                $stmt = $conn->prepare("INSERT INTO visitors (project_id, name, phone, visit_date, visit_time, amount, payment_receipt, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', NOW())");
                $stmt->bind_param("issssds", $project_id, $client_name, $client_phone, $visit_date, $visit_time, $amount, $receipt);
                if ($stmt->execute()) {
                    header("Location: " . $_SERVER['REQUEST_URI'] . "&status=visit_success");
                    exit;
                } else {
                    $booking_message = "Error sending visit request.";
                    $booking_class = "alert alert-danger";
                }
                $stmt->close();
            } elseif (!$booking_message) {
                $booking_message = "Please fill in all required fields for the visit.";
                $booking_class = "alert alert-warning";
            }
        } else {
            $booking_message = "Invalid booking type.";
            $booking_class = "alert alert-danger";
        }
    } else {
        $booking_message = "Please enter name and phone number.";
        $booking_class = "alert alert-warning";
    }
}

if (isset($_GET['status'])) {
    if ($_GET['status'] === 'inquiry_success') {
        $booking_message = "Inquiry sent successfully!";
        $booking_class = "alert alert-success";
    } elseif ($_GET['status'] === 'visit_success') {
        $booking_message = "Visit request sent successfully!";
        $booking_class = "alert alert-success";
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
                        <p class="mb-1"><strong>Amount:</strong> <?= htmlspecialchars(number_format($block['amount'], 2)) ?>
                        </p>
                        <p class="mb-1"><strong>Payment Method:</strong> <?= htmlspecialchars($block['payment_method']) ?>
                        </p>
                        <p class="mb-2">
                            <strong>Description:</strong><br><?= nl2br(htmlspecialchars($block['description'])) ?></p>
                        <?php if (!empty($block['background_image']) && file_exists("uploads/{$block['background_image']}")): ?>
                            <img src="uploads/<?= htmlspecialchars($block['background_image']) ?>"
                                class="img-fluid mt-2 rounded" alt="Background Image">
                        <?php endif; ?>
                        <small class="text-muted d-block mt-2">Added at: <?= $block['created_at'] ?></small>
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/booking.css">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo&display=swap" rel="stylesheet">
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

</head>

<body>
    <?php if (isset($_GET['status'])): ?>
        <div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content <?= $booking_class ?>">
                    <div class="modal-header">
                        <h5 class="modal-title" id="bookingModalLabel">Booking Status</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <?= htmlspecialchars($booking_message) ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php include './header.php'; ?>
    <?php include './loging.php'; ?>

    <section class="site-banner site-banner--bg site-banner--page" style="background-image:url(../img/cover.jpg);">
        <div class="site-banner__txt section section--medium txt-center post-styles">
            <h1 class="site-banner__title"><a href="#" data-translate>About</a> / <a href="#" data-translate>Home</a>
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
                <div class="payment-type">
                    <h4 data-translate> Choose payment method below</h4>
                    <div class="types flex justify-space-between">
                        <?php if ($info_blocks): ?>
                            <?php foreach ($info_blocks as $info): ?>
                                <div class="type selected">
                                    <div class="text">
                                        <p data-translate><?= htmlspecialchars($info['title']) ?></p>
                                        <p data-translate><?= nl2br(htmlspecialchars($info['text'])) ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p data-translate>No general info blocks found.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="payment-info flex justify-space-between">
                    <div class="column billing">
                        <div class="title">
                            <div class="num">1</div>
                            <h4 data-translate> data-translateBooking Info</h4>
                        </div>
                        <div class="flex-end">
                            <button type="button" class="button booking-btn" data-type="inquiry"
                                data-translate>Inquiry</button>
                            <button type="button" class="button booking-btn" data-type="visit"
                                data-translate>Visit</button>
                        </div>
                        <form method="post" enctype="multipart/form-data">
                            <input type="hidden" name="booking_type" id="booking_type">
                            <div class="field full">
                                <label data-translate>Full Name</label>
                                <input type="text" name="client_name" placeholder="Full Name" required>
                            </div>
                            <div class="field full">
                                <label data-translate>Phone Number</label>
                                <input type="tel" name="client_phone" placeholder="Phone Number" required>
                            </div>
                            <div id="visit_fields" style="display:none;">
                                <div class="field full">
                                    <label data-translate>Booking Date</label>
                                    <input type="date" name="visit_date">
                                </div>
                                <div class="field full">
                                    <label data-translate>Booking Time</label>
                                    <input type="time" name="visit_time">
                                </div>
                                <div class="field full">
                                    <label data-translate>Amount</label>
                                    <input type="number" name="amount" step="1" min="1">
                                </div>
                                <div class="field full">
                                    <label data-translate>Upload Payment Receipt</label>
                                    <input type="file" name="receipt" accept=".jpg,.jpeg,.png,.pdf">
                                </div>
                            </div>
                            <button class="button button-secondary" type="submit" name="book_project"
                                data-translate>Submit
                                Booking</button>
                        </form>
                    </div>

                    <div class="cccc">
                        <div class="row">
                            <div class="title mb-3">
                                <div class="num">2</div>
                                <h4 data-translate>card info</h4>
                            </div>
                            <div class="col-12 mb-4">
                                <a href="project_details.php?id=<?= $project['id'] ?>">
                                    <div class="property-card">
                                        <div class="cover_card"
                                            style="background-image: url('/Egy-Hills/uploads/<?= htmlspecialchars($project['image']) ?>');">
                                        </div>
                                        <div class="property-card-content">
                                            <p class="property-card-location">
                                                <?= htmlspecialchars($project['location'] ?? 'No Location') ?>
                                            </p>
                                            <div class="d-flex align-items-center justify-content-between">
                                                <h3 class="property-card-title">
                                                    <?= htmlspecialchars($project['title']) ?>
                                                </h3>
                                                <h3 class="property-card-title">
                                                    <?= htmlspecialchars($project['price']) ?>
                                                </h3>
                                            </div>
                                            <div class="property-card-features">
                                                <div class="property-card-feature" data-translate>
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                        class="lucide lucide-bed">
                                                        <path d="M2 9V4a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5"></path>
                                                        <path d="M2 11v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-9"></path>
                                                        <path d="M2 14h20"></path>
                                                        <path
                                                            d="M4 9h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2Z">
                                                        </path>
                                                    </svg> <?= htmlspecialchars($project['beds']) ?> Beds
                                                </div>
                                                <div class="property-card-feature" data-translate>
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                        class="lucide lucide-bath">
                                                        <path
                                                            d="M9 6 6.5 3.5a1.5 1.5 0 0 0-1-.5C4.683 3 4 3.683 4 4.5V17a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-5">
                                                        </path>
                                                        <line x1="10" x2="8" y1="5" y2="7"></line>
                                                        <line x1="2" x2="22" y1="12" y2="12"></line>
                                                        <line x1="7" x2="7" y1="19" y2="21"></line>
                                                        <line x1="17" x2="17" y1="19" y2="21"></line>
                                                    </svg> <?= htmlspecialchars($project['baths']) ?> Baths
                                                </div>
                                                <div class="property-card-feature" data-translate>
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                        class="lucide lucide-square">
                                                        <rect width="18" height="18" x="3" y="3" rx="2"></rect>
                                                    </svg>
                                                    <?= htmlspecialchars($project['area']) ?> sqm
                                                </div>
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

    <section id="footer"></section>

    <script>
        const buttons = document.querySelectorAll('.booking-btn');
        const bookingType = document.getElementById('booking_type');
        const visitFields = document.getElementById('visit_fields');

        bookingType.value = 'inquiry';
        visitFields.style.display = 'none';

        buttons.forEach(btn => {
            if (btn.dataset.type === 'inquiry') {
                btn.classList.add('selected');
            }
        });

        buttons.forEach(btn => {
            btn.addEventListener('click', () => {
                const type = btn.dataset.type;
                bookingType.value = type;
                visitFields.style.display = (type === 'visit') ? 'block' : 'none';
                buttons.forEach(b => b.classList.remove('selected'));
                btn.classList.add('selected');
            });
        });
    </script>

    <script src="../script/app.js"></script>

    <style>
        @media screen and (max-width:992px) {
            .payment-info.flex.justify-space-between {
                grid-template-columns: 1fr;
                gap: 30px;
            }
        }
    </style>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <?php if (isset($_GET['status'])): ?>
        <script>
            const bookingModal = new bootstrap.Modal(document.getElementById('bookingModal'));
            bookingModal.show();

            if (window.history.replaceState) {
                const url = new URL(window.location.href);
                url.searchParams.delete('status');
                window.history.replaceState({}, document.title, url.toString());
            }
        </script>
    <?php endif; ?>
</body>

</html>