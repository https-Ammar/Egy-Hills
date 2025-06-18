<?php
include 'db.php';

// === Helper function ===
function safe($value)
{
    return htmlspecialchars($value ?? '');
}

// === Update status on accept or reject ===
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    if ($_GET['action'] === 'accept') {
        $status = 'accepted';
    } elseif ($_GET['action'] === 'reject') {
        $status = 'rejected';
    } else {
        header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
        exit();
    }

    $stmt = $conn->prepare("UPDATE visitors SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);
    $stmt->execute();
    $stmt->close();

    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
    exit();
}

// === Fetch all visitors with project data ===
$result = $conn->query("
    SELECT v.*, p.title AS project_title, p.location AS project_location, p.image AS project_image 
    FROM visitors v 
    LEFT JOIN projects p ON v.project_id = p.id 
    ORDER BY v.id DESC
") or die("Error fetching visitors: " . $conn->error);

$accepted = $conn->query("
    SELECT v.*, p.title AS project_title, p.location AS project_location, p.image AS project_image 
    FROM visitors v 
    LEFT JOIN projects p ON v.project_id = p.id 
    WHERE v.status = 'accepted'
    ORDER BY v.id DESC
") or die("Error fetching accepted visitors: " . $conn->error);

$rejected = $conn->query("
    SELECT v.*, p.title AS project_title, p.location AS project_location, p.image AS project_image 
    FROM visitors v 
    LEFT JOIN projects p ON v.project_id = p.id 
    WHERE v.status = 'rejected'
    ORDER BY v.id DESC
") or die("Error fetching rejected visitors: " . $conn->error);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Reservation Requests Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container py-5">
        <h1 class="text-center mb-5">Reservation Requests Dashboard</h1>

        <h3>All Requests</h3>
        <div class="table-responsive mb-5">
            <table class="table table-bordered table-striped align-middle text-center">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Project</th>
                        <th>Location</th>
                        <th>Project Image</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Visit Date</th>
                        <th>Visit Time</th>
                        <th>Amount</th>
                        <th>Receipt</th>
                        <th>Created At</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr class="<?= safe($row['status']) ?>">
                            <td><?= safe($row['id']) ?></td>
                            <td><?= safe($row['project_title']) ?: '(Deleted Project)' ?></td>
                            <td><?= safe($row['project_location']) ?: '(Deleted Project)' ?></td>
                            <td>
                                <?php if (!empty($row['project_image'])): ?>
                                    <img src="uploads/<?= safe($row['project_image']) ?>" alt="Project Image" width="100"
                                        class="rounded">
                                <?php else: ?>
                                    (Deleted Project)
                                <?php endif; ?>
                            </td>
                            <td><?= safe($row['name']) ?></td>
                            <td><?= safe($row['phone']) ?></td>
                            <td><?= safe($row['visit_date']) ?></td>
                            <td><?= safe($row['visit_time']) ?></td>
                            <td><?= safe($row['amount']) ?></td>
                            <td>
                                <?php if (!empty($row['payment_receipt'])): ?>
                                    <a href="uploads/<?= safe($row['payment_receipt']) ?>" target="_blank"
                                        class="btn btn-sm btn-primary">View</a>
                                <?php else: ?>
                                    None
                                <?php endif; ?>
                            </td>
                            <td><?= safe($row['created_at']) ?></td>
                            <td>
                                <?= empty($row['status']) ? 'pending' : safe($row['status']) ?>
                            </td>
                            <td>
                                <?php if ($row['status'] === 'pending' || empty($row['status'])): ?>
                                    <a href="?action=accept&id=<?= safe($row['id']) ?>"
                                        class="btn btn-success btn-sm">Accept</a>
                                    <a href="?action=reject&id=<?= safe($row['id']) ?>" class="btn btn-danger btn-sm">Reject</a>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <h3>Accepted Requests</h3>
        <div class="table-responsive mb-5">
            <table class="table table-bordered table-success table-striped align-middle text-center">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Project</th>
                        <th>Location</th>
                        <th>Project Image</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Visit Date</th>
                        <th>Visit Time</th>
                        <th>Amount</th>
                        <th>Receipt</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $accepted->fetch_assoc()): ?>
                        <tr>
                            <td><?= safe($row['id']) ?></td>
                            <td><?= safe($row['project_title']) ?: '(Deleted Project)' ?></td>
                            <td><?= safe($row['project_location']) ?: '(Deleted Project)' ?></td>
                            <td>
                                <?php if (!empty($row['project_image'])): ?>
                                    <img src="uploads/<?= safe($row['project_image']) ?>" alt="Project Image" width="100"
                                        class="rounded">
                                <?php else: ?>
                                    (Deleted Project)
                                <?php endif; ?>
                            </td>
                            <td><?= safe($row['name']) ?></td>
                            <td><?= safe($row['phone']) ?></td>
                            <td><?= safe($row['visit_date']) ?></td>
                            <td><?= safe($row['visit_time']) ?></td>
                            <td><?= safe($row['amount']) ?></td>
                            <td>
                                <?php if (!empty($row['payment_receipt'])): ?>
                                    <a href="uploads/<?= safe($row['payment_receipt']) ?>" target="_blank"
                                        class="btn btn-sm btn-primary">View</a>
                                <?php else: ?>
                                    None
                                <?php endif; ?>
                            </td>
                            <td><?= safe($row['created_at']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <h3>Rejected Requests</h3>
        <div class="table-responsive">
            <table class="table table-bordered table-danger table-striped align-middle text-center">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Project</th>
                        <th>Location</th>
                        <th>Project Image</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Visit Date</th>
                        <th>Visit Time</th>
                        <th>Amount</th>
                        <th>Receipt</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $rejected->fetch_assoc()): ?>
                        <tr>
                            <td><?= safe($row['id']) ?></td>
                            <td><?= safe($row['project_title']) ?: '(Deleted Project)' ?></td>
                            <td><?= safe($row['project_location']) ?: '(Deleted Project)' ?></td>
                            <td>
                                <?php if (!empty($row['project_image'])): ?>
                                    <img src="uploads/<?= safe($row['project_image']) ?>" alt="Project Image" width="100"
                                        class="rounded">
                                <?php else: ?>
                                    (Deleted Project)
                                <?php endif; ?>
                            </td>
                            <td><?= safe($row['name']) ?></td>
                            <td><?= safe($row['phone']) ?></td>
                            <td><?= safe($row['visit_date']) ?></td>
                            <td><?= safe($row['visit_time']) ?></td>
                            <td><?= safe($row['amount']) ?></td>
                            <td>
                                <?php if (!empty($row['payment_receipt'])): ?>
                                    <a href="uploads/<?= safe($row['payment_receipt']) ?>" target="_blank"
                                        class="btn btn-sm btn-primary">View</a>
                                <?php else: ?>
                                    None
                                <?php endif; ?>
                            </td>
                            <td><?= safe($row['created_at']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>