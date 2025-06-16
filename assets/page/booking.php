<?php
include 'db.php';

// Validate project ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid project ID.');
}

$project_id = intval($_GET['id']);

// Fetch project title
$stmt = $conn->prepare("SELECT title FROM projects WHERE id = ?");
$stmt->bind_param("i", $project_id);
$stmt->execute();
$project = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$project) {
    die('Project not found.');
}

// Fetch project-specific blocks
$blocks = [];
$stmt = $conn->prepare("SELECT block_title, block_text, block_image FROM project_blocks WHERE project_id = ?");
$stmt->bind_param("i", $project_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $blocks[] = $row;
}
$stmt->close();

// Fetch general info blocks
$info_blocks = [];
$result = $conn->query("SELECT title, text, image FROM info_blocks");
while ($row = $result->fetch_assoc()) {
    $info_blocks[] = $row;
}

// Handle booking or inquiry
$booking_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_project'])) {
    $type = $_POST['booking_type'];
    $client_name = trim($_POST['client_name']);
    $client_phone = trim($_POST['client_phone']);

    if ($client_name && $client_phone) {
        if ($type === 'inquiry') {
            $stmt = $conn->prepare("INSERT INTO visitors (project_id, name, phone, status, created_at) VALUES (?, ?, ?, 'inquiry', NOW())");
            $stmt->bind_param("iss", $project_id, $client_name, $client_phone);
            $booking_message = $stmt->execute() ? "✅ Inquiry sent successfully!" : "❌ Error sending inquiry.";
            $stmt->close();
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

            if ($visit_date && $visit_time && $amount && !$booking_message) {
                $stmt = $conn->prepare("INSERT INTO visitors (project_id, name, phone, visit_date, visit_time, amount, payment_receipt, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', NOW())");
                $stmt->bind_param("issssds", $project_id, $client_name, $client_phone, $visit_date, $visit_time, $amount, $receipt);
                $booking_message = $stmt->execute() ? "✅ Visit request sent successfully!" : "❌ Error sending visit request.";
                $stmt->close();
            } elseif (!$booking_message) {
                $booking_message = "❌ Please fill in all required fields for the visit.";
            }
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
    <style>
        .block {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 20px;
        }

        .block img {
            max-width: 100%;
            height: auto;
        }

        .message {
            color: green;
        }

        .error {
            color: red;
        }
    </style>
</head>

<body>

    <h1><?= htmlspecialchars($project['title']) ?></h1>

    <h2>Project Blocks</h2>
    <?php if ($blocks): ?>
        <?php foreach ($blocks as $b): ?>
            <div class="block">
                <h3><?= htmlspecialchars($b['block_title']) ?></h3>
                <p><?= nl2br(htmlspecialchars($b['block_text'])) ?></p>
                <?php if ($b['block_image']): ?>
                    <img src="uploads/<?= htmlspecialchars($b['block_image']) ?>" alt="Block Image">
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No project-specific blocks found.</p>
    <?php endif; ?>

    <h2>General Info Blocks</h2>
    <?php if ($info_blocks): ?>
        <?php foreach ($info_blocks as $info): ?>
            <div class="block">
                <h3><?= htmlspecialchars($info['title']) ?></h3>
                <p><?= nl2br(htmlspecialchars($info['text'])) ?></p>
                <?php if ($info['image']): ?>
                    <img src="uploads/<?= htmlspecialchars($info['image']) ?>" alt="Info Image">
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No general info blocks found.</p>
    <?php endif; ?>

    <h2>Booking / Inquiry</h2>
    <?php if ($booking_message): ?>
        <p class="<?= strpos($booking_message, '✅') !== false ? 'message' : 'error' ?>">
            <?= htmlspecialchars($booking_message) ?>
        </p>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <label>Booking Type:</label><br>
        <select name="booking_type" required>
            <option value="inquiry">Inquiry</option>
            <option value="visit">Visit</option>
        </select><br><br>

        <label>Name:</label><br>
        <input type="text" name="client_name" required><br><br>

        <label>Phone:</label><br>
        <input type="text" name="client_phone" required><br><br>

        <div id="visit_fields" style="display: none;">
            <label>Visit Date:</label><br>
            <input type="date" name="visit_date"><br><br>

            <label>Visit Time:</label><br>
            <input type="time" name="visit_time"><br><br>

            <label>Amount:</label><br>
            <input type="number" name="amount" step="0.01"><br><br>

            <label>Payment Receipt (JPG, PNG, PDF):</label><br>
            <input type="file" name="receipt" accept=".jpg,.jpeg,.png,.pdf"><br><br>
        </div>

        <button type="submit" name="book_project">Submit</button>
    </form>

    <script>
        const typeSelect = document.querySelector('select[name="booking_type"]');
        const visitFields = document.getElementById('visit_fields');

        typeSelect.addEventListener('change', function () {
            visitFields.style.display = this.value === 'visit' ? 'block' : 'none';
        });
    </script>

</body>

</html>