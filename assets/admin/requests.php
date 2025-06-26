<?php
include 'db.php';
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

function safe($value)
{
    return htmlspecialchars($value ?? '');
}

if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    if ($_GET['action'] === 'accept') {
        $status = 'accepted';
        $stmt = $conn->prepare("UPDATE visitors SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $id);
        $stmt->execute();
        $stmt->close();
    } elseif ($_GET['action'] === 'reject') {
        $status = 'rejected';
        $stmt = $conn->prepare("UPDATE visitors SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $id);
        $stmt->execute();
        $stmt->close();
    } elseif ($_GET['action'] === 'delete') {
        $stmt = $conn->prepare("DELETE FROM visitors WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
    exit();
}

$totalRequests = $conn->query("SELECT COUNT(*) as total FROM visitors")->fetch_assoc()['total'];
$acceptedRequests = $conn->query("SELECT COUNT(*) as accepted FROM visitors WHERE status = 'accepted'")->fetch_assoc()['accepted'];
$rejectedRequests = $conn->query("SELECT COUNT(*) as rejected FROM visitors WHERE status = 'rejected'")->fetch_assoc()['rejected'];
$pendingRequests = $conn->query("SELECT COUNT(*) as pending FROM visitors WHERE status IS NULL OR status = 'pending'")->fetch_assoc()['pending'];

$result = $conn->query("
    SELECT v.*, p.title AS project_title, p.location AS project_location, p.image AS project_image 
    FROM visitors v 
    LEFT JOIN projects p ON v.project_id = p.id 
    ORDER BY v.id DESC
") or die("Error fetching visitors: " . $conn->error);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['receipt'])) {
    $upload_message = '';
    $receipt = null;

    if ($_FILES['receipt']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['receipt']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'pdf'];
        if (in_array($ext, $allowed)) {
            $newName = time() . '_' . basename($_FILES['receipt']['name']);
            $destination = '/Applications/MAMP/htdocs/Egy-Hills/uploads/' . $newName;

            if (move_uploaded_file($_FILES['receipt']['tmp_name'], $destination)) {
                $receipt = $newName;
                $upload_message = "✅ File uploaded successfully.";
            } else {
                $upload_message = "❌ Failed to move uploaded file.";
            }
        } else {
            $upload_message = "❌ Invalid file type. Allowed: JPG, PNG, PDF.";
        }
    } else {
        $upload_message = "❌ File upload error.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation Requests Dashboard</title>
    <link href="assets/css/vendor.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.0/font/bootstrap-icons.css">
    <script src="assets/js/config.js"></script>
    <style>
        .status-accepted {
            color: #28a745;
            font-weight: bold;
        }

        .status-rejected {
            color: #dc3545;
            font-weight: bold;
        }

        .status-pending {
            color: #ffc107;
            font-weight: bold;
        }

        .avatar-md.bg-soft-primary.rounded {
            display: flex;
            align-items: center;
            justify-content: center;
            /* font-size: smaller; */
        }

        .avatar-md.bg-soft-primary.rounded svg {
            width: 35px;
            height: 35px;
            color: #ff6c30;
        }
    </style>
    <script src="assets/js/config.js"></script>
</head>

<body class=" mt-5">
    <div class="page-content_">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="row">
                        <div class="col-3">
                            <div class="card overflow-hidden">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="avatar-md bg-soft-primary rounded">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48"
                                                    viewBox="0 0 48 48">
                                                    <g fill="none" stroke="currentColor" stroke-linejoin="round"
                                                        stroke-width="4">
                                                        <path
                                                            d="M37 44a4 4 0 1 0 0-8a4 4 0 0 0 0 8ZM11 12a4 4 0 1 0 0-8a4 4 0 0 0 0 8Zm0 32a4 4 0 1 0 0-8a4 4 0 0 0 0 8Z" />
                                                        <path stroke-linecap="round"
                                                            d="M11 12v24m13-26h9a4 4 0 0 1 4 4v22" />
                                                        <path stroke-linecap="round" d="m30 16l-6-6l6-6" />
                                                    </g>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="col-6 text-end">
                                            <p class="text-muted mb-0 text-truncate">Total Requests</p>
                                            <h3 class="text-dark mt-1 mb-0"><?= $totalRequests ?></h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-3">
                            <div class="card overflow-hidden">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="avatar-md bg-soft-primary rounded">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48"
                                                    viewBox="0 0 48 48">
                                                    <g fill="currentColor">
                                                        <path
                                                            d="M32.707 22.707a1 1 0 0 0-1.414-1.414L24 28.586l-3.293-3.293a1 1 0 0 0-1.414 1.414L24 31.414z" />
                                                        <path fill-rule="evenodd"
                                                            d="M38 15v21a3 3 0 0 1-3 3H17a3 3 0 0 1-3-3V8a3 3 0 0 1 3-3h11zm-10 1a1 1 0 0 1-1-1V7H17a1 1 0 0 0-1 1v28a1 1 0 0 0 1 1h18a1 1 0 0 0 1-1V16zm1-7.172L34.172 14H29z"
                                                            clip-rule="evenodd" />
                                                        <path d="M12 11v27a3 3 0 0 0 3 3h19v2H15a5 5 0 0 1-5-5V11z" />
                                                    </g>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="col-6 text-end">
                                            <p class="text-muted mb-0 text-truncate">Accepted</p>
                                            <h3 class="text-dark mt-1 mb-0"><?= $acceptedRequests ?></h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-3">
                            <div class="card overflow-hidden">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="avatar-md bg-soft-primary rounded">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24">
                                                    <path fill="currentColor"
                                                        d="M6 18H4q-.425 0-.712-.288T3 17t.288-.712T4 16h3q.425 0 .713.288T8 17v3q0 .425-.288.713T7 21t-.712-.288T6 20zm12 0v2q0 .425-.288.713T17 21t-.712-.288T16 20v-3q0-.425.288-.712T17 16h3q.425 0 .713.288T21 17t-.288.713T20 18zM6 6V4q0-.425.288-.712T7 3t.713.288T8 4v3q0 .425-.288.713T7 8H4q-.425 0-.712-.288T3 7t.288-.712T4 6zm12 0h2q.425 0 .713.288T21 7t-.288.713T20 8h-3q-.425 0-.712-.288T16 7V4q0-.425.288-.712T17 3t.713.288T18 4z" />
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="col-6 text-end">
                                            <p class="text-muted mb-0 text-truncate">Rejected</p>
                                            <h3 class="text-dark mt-1 mb-0"><?= $rejectedRequests ?></h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-3">
                            <div class="card overflow-hidden">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="avatar-md bg-soft-primary rounded">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24">
                                                    <g fill="none" stroke="currentColor" stroke-width="1.5">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M10.568 2.975a2.06 2.06 0 0 0-.73 1.27a1 1 0 0 1-.2.42a1 1 0 0 1-.36.3l-.79.38a5.1 5.1 0 0 0-1.65 1.29c-1.4 1.67-1.4 2.42-1.4 5.27c0 1.29-1.37 2.46-1.73 3.62c-.22.69-.34 2.25 1.48 2.25h13.58a1.6 1.6 0 0 0 .77-.16a1.64 1.64 0 0 0 .6-.51c.148-.218.24-.469.27-.73a1.6 1.6 0 0 0-.13-.78c-.36-1.09-1.79-2.39-1.79-3.68v-2.13" />
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M15.228 17.775c.003.427-.075.851-.23 1.25a3.4 3.4 0 0 1-.71 1.06a3.2 3.2 0 0 1-2.33.94a3.2 3.2 0 0 1-1.26-.25a3.3 3.3 0 0 1-1.77-1.77a3.2 3.2 0 0 1-.23-1.23" />
                                                        <circle cx="15.228" cy="5.475" r="2.5" />
                                                    </g>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="col-6 text-end">
                                            <p class="text-muted mb-0 text-truncate">Pending</p>
                                            <h3 class="text-dark mt-1 mb-0"><?= $pendingRequests ?></h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row ptn_box_open" id="box1" style="display: block;">
                <div class="col">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <h4 class="card-title">Reservation Requests</h4>
                            </div>
                        </div>
                        <div class="table-responsive table-centered">
                            <table class="table mb-0">
                                <thead class="bg-light bg-opacity-50">
                                    <tr>
                                        <th>Project</th>
                                        <th>Name</th>
                                        <th>Phone</th>
                                        <th>Amount</th>
                                        <th>Receipt</th>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $result->fetch_assoc()): ?>
                                        <?php
                                        $modalId = 'modal_' . safe($row['id']);
                                        $createdAt = new DateTime($row['created_at']);
                                        $date = $createdAt->format('Y-m-d');
                                        $time = $createdAt->format('H:i:s');
                                        ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <?php if (!empty($row['project_image'])): ?>
                                                        <div class="rounded bg-light avatar-md d-flex align-items-center justify-content-center"
                                                            style="background-image: url(/Egy-Hills/uploads/<?= safe($row['project_image']) ?>)">
                                                        </div>
                                                    <?php else: ?>
                                                        (Deleted Project)
                                                    <?php endif; ?>
                                                    <div>
                                                        <a href="#!"
                                                            class="text-dark fw-medium fs-15"><?= safe($row['project_title']) ?: '(Deleted)' ?></a>
                                                        <p class="text-muted mb-0 mt-1 fs-13">
                                                            <span></span><?= safe($row['project_location']) ?: '(Deleted)' ?>
                                                        </p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?= safe($row['name']) ?></td>
                                            <td><?= safe($row['phone']) ?></td>
                                            <td><?= safe($row['amount']) ?></td>
                                            <td>
                                                <?php if (!empty($row['payment_receipt'])): ?>
                                                    <a href="/Egy-Hills/uploads/<?= safe($row['payment_receipt']) ?>"
                                                        target="_blank" class="btn btn-sm btn-primary">View</a>
                                                <?php else: ?>
                                                    None
                                                <?php endif; ?>
                                            </td>
                                            <td><?= $date ?></td>
                                            <td><?= $time ?></td>
                                            <td class="status-<?= safe($row['status'] ?: 'pending') ?>">
                                                <?php
                                                if ($row['status'] === 'accepted') {
                                                    echo '✓ Accepted';
                                                } elseif ($row['status'] === 'rejected') {
                                                    echo '✗ Rejected';
                                                } else {
                                                    echo 'Pending';
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal"
                                                    data-bs-target="#<?= $modalId ?>">
                                                    <i class="bi bi-eye-fill"></i>
                                                </button>
                                                <?php if ($row['status'] === 'pending' || empty($row['status'])): ?>
                                                    <a href="?action=accept&id=<?= safe($row['id']) ?>"
                                                        class="btn btn-success btn-sm">
                                                        <i class="bi bi-check-circle-fill"></i>
                                                    </a>
                                                    <a href="?action=reject&id=<?= safe($row['id']) ?>"
                                                        class="btn btn-danger btn-sm">
                                                        <i class="bi bi-x-circle-fill"></i>
                                                    </a>
                                                <?php endif; ?>

                                                <a href="?action=delete&id=<?= safe($row['id']) ?>">
                                                    <button
                                                        onclick="return confirm('Are you sure you want to delete this item?');"
                                                        class="btn btn-soft-danger btn-sm">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </a>
                                            </td>


                                        </tr>

                                        <div class="modal fade" id="<?= $modalId ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Visitor Details #<?= safe($row['id']) ?>
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p><strong>Name:</strong> <?= safe($row['name']) ?></p>
                                                        <p><strong>Phone:</strong> <?= safe($row['phone']) ?></p>
                                                        <p><strong>Project:</strong>
                                                            <?= safe($row['project_title']) ?: '(Deleted)' ?></p>
                                                        <p><strong>Visit Date:</strong> <?= safe($row['visit_date']) ?></p>
                                                        <p><strong>Visit Time:</strong> <?= safe($row['visit_time']) ?></p>
                                                        <p><strong>Amount:</strong> <?= safe($row['amount']) ?></p>
                                                        <p><strong>Payment Receipt:</strong><br>
                                                            <?php if (!empty($row['payment_receipt'])): ?>
                                                                <a href="/Egy-Hills/uploads/<?= safe($row['payment_receipt']) ?>"
                                                                    target="_blank" class="btn btn-primary btn-sm">View
                                                                    Receipt</a>
                                                            <?php else: ?>
                                                                None
                                                            <?php endif; ?>
                                                        </p>
                                                        <p><strong>Status:</strong>
                                                            <?php
                                                            if ($row['status'] === 'accepted') {
                                                                echo '<span class="text-success">✓ Accepted</span>';
                                                            } elseif ($row['status'] === 'rejected') {
                                                                echo '<span class="text-danger">✗ Rejected</span>';
                                                            } else {
                                                                echo '<span class="text-warning">Pending</span>';
                                                            }
                                                            ?>
                                                        </p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>