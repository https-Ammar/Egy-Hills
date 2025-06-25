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
                                                <iconify-icon icon="solar:cart-5-bold-duotone"
                                                    class="avatar-title fs-32 text-primary"></iconify-icon>
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
                                                <i class="bx bx-check-circle avatar-title fs-24 text-primary"></i>
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
                                                <i class="bx bx-x-circle avatar-title text-primary fs-24"></i>
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
                                                <i class="bx bx-time-five avatar-title text-primary fs-24"></i>
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
                                                    <a href="uploads/<?= safe($row['payment_receipt']) ?>" target="_blank"
                                                        class="btn btn-sm btn-primary">View</a>
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
                                                        <p><strong>Email:</strong> <?= safe($row['email']) ?></p>
                                                        <p><strong>Project:</strong>
                                                            <?= safe($row['project_title']) ?: '(Deleted)' ?></p>
                                                        <p><strong>Visit Date:</strong> <?= safe($row['visit_date']) ?></p>
                                                        <p><strong>Visit Time:</strong> <?= safe($row['visit_time']) ?></p>
                                                        <p><strong>Amount:</strong> <?= safe($row['amount']) ?></p>
                                                        <p><strong>Payment Receipt:</strong><br>
                                                            <?php if (!empty($row['payment_receipt'])): ?>
                                                                <a href="uploads/<?= safe($row['payment_receipt']) ?>"
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