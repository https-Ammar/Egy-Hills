<?php
include 'db.php';

// تحديث الحالة عند القبول أو الرفض
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    if ($_GET['action'] === 'accept') {
        $action = 'accepted';
    } elseif ($_GET['action'] === 'reject') {
        $action = 'rejected';
    } else {
        header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
        exit();
    }

    $stmt = $conn->prepare("UPDATE visitors SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $action, $id);
    $stmt->execute();
    $stmt->close();

    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
    exit();
}

// جلب كل طلبات visitors مع بيانات المشروع
$result = $conn->query("
    SELECT v.*, p.title AS project_title, p.location AS project_location, p.image AS project_image 
    FROM visitors v 
    LEFT JOIN projects p ON v.project_id = p.id 
    ORDER BY v.id DESC
");
if (!$result) {
    die("Error fetching visitors: " . $conn->error);
}

// الطلبات المقبولة
$accepted = $conn->query("
    SELECT v.*, p.title AS project_title, p.location AS project_location, p.image AS project_image 
    FROM visitors v 
    LEFT JOIN projects p ON v.project_id = p.id 
    WHERE v.status = 'accepted'
    ORDER BY v.id DESC
");
if (!$accepted) {
    die("Error fetching accepted visitors: " . $conn->error);
}

// الطلبات المرفوضة
$rejected = $conn->query("
    SELECT v.*, p.title AS project_title, p.location AS project_location, p.image AS project_image 
    FROM visitors v 
    LEFT JOIN projects p ON v.project_id = p.id 
    WHERE v.status = 'rejected'
    ORDER BY v.id DESC
");
if (!$rejected) {
    die("Error fetching rejected visitors: " . $conn->error);
}
?>


<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="UTF-8">
    <title>لوحة طلبات الحجز</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            direction: rtl;
        }

        h1,
        h2 {
            text-align: center;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin: 20px auto;
        }

        th,
        td {
            border: 1px solid #999;
            padding: 8px;
            text-align: center;
        }

        img {
            max-width: 100px;
            height: auto;
            border-radius: 4px;
        }

        .accepted {
            background: #d4edda;
        }

        .rejected {
            background: #f8d7da;
        }
    </style>
</head>

<body>

    <h1>لوحة طلبات الحجز</h1>

    <h2>كل الطلبات</h2>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>المشروع</th>
                <th>الموقع</th>
                <th>صورة المشروع</th>
                <th>الاسم</th>
                <th>رقم الهاتف</th>
                <th>تاريخ المعاينة</th>
                <th>وقت المعاينة</th>
                <th>المبلغ</th>
                <th>الإيصال</th>
                <th>تاريخ الطلب</th>
                <th>الحالة</th>
                <th>إجراءات</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr class="<?= htmlspecialchars($row['status']) ?>">
                    <td><?= $row['id'] ?></td>
                    <td><?= !empty($row['project_title']) ? htmlspecialchars($row['project_title']) : '(تم حذف المشروع)' ?>
                    </td>
                    <td><?= !empty($row['project_location']) ? htmlspecialchars($row['project_location']) : '(تم حذف المشروع)' ?>
                    </td>
                    <td>
                        <?php if (!empty($row['project_image'])): ?>
                            <img src="uploads/<?= htmlspecialchars($row['project_image']) ?>" alt="صورة المشروع">
                        <?php else: ?>
                            (تم حذف المشروع)
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['phone']) ?></td>
                    <td><?= htmlspecialchars($row['visit_date']) ?></td>
                    <td><?= htmlspecialchars($row['visit_time']) ?></td>
                    <td><?= htmlspecialchars($row['amount']) ?></td>
                    <td>
                        <?php if (!empty($row['payment_receipt'])): ?>
                            <a href="uploads/<?= htmlspecialchars($row['payment_receipt']) ?>" target="_blank">مشاهدة</a>
                        <?php else: ?> لا يوجد <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($row['created_at']) ?></td>
                    <td><?= ucfirst($row['status']) ?></td>
                    <td>
                        <?php if ($row['status'] === 'pending' || empty($row['status'])): ?>
                            <a href="?action=accept&id=<?= $row['id'] ?>">قبول</a> |
                            <a href="?action=reject&id=<?= $row['id'] ?>">رفض</a>
                        <?php else: ?> - <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <h2>الطلبات المقبولة</h2>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>المشروع</th>
                <th>الموقع</th>
                <th>صورة المشروع</th>
                <th>الاسم</th>
                <th>رقم الهاتف</th>
                <th>تاريخ المعاينة</th>
                <th>وقت المعاينة</th>
                <th>المبلغ</th>
                <th>الإيصال</th>
                <th>تاريخ الطلب</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $accepted->fetch_assoc()): ?>
                <tr class="accepted">
                    <td><?= $row['id'] ?></td>
                    <td><?= !empty($row['project_title']) ? htmlspecialchars($row['project_title']) : '(تم حذف المشروع)' ?>
                    </td>
                    <td><?= !empty($row['project_location']) ? htmlspecialchars($row['project_location']) : '(تم حذف المشروع)' ?>
                    </td>
                    <td>
                        <?php if (!empty($row['project_image'])): ?>
                            <img src="uploads/<?= htmlspecialchars($row['project_image']) ?>" alt="صورة المشروع">
                        <?php else: ?>
                            (تم حذف المشروع)
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['phone']) ?></td>
                    <td><?= htmlspecialchars($row['visit_date']) ?></td>
                    <td><?= htmlspecialchars($row['visit_time']) ?></td>
                    <td><?= htmlspecialchars($row['amount']) ?></td>
                    <td>
                        <?php if (!empty($row['payment_receipt'])): ?>
                            <a href="uploads/<?= htmlspecialchars($row['payment_receipt']) ?>" target="_blank">مشاهدة</a>
                        <?php else: ?> لا يوجد <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($row['created_at']) ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <h2>الطلبات المرفوضة</h2>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>المشروع</th>
                <th>الموقع</th>
                <th>صورة المشروع</th>
                <th>الاسم</th>
                <th>رقم الهاتف</th>
                <th>تاريخ المعاينة</th>
                <th>وقت المعاينة</th>
                <th>المبلغ</th>
                <th>الإيصال</th>
                <th>تاريخ الطلب</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $rejected->fetch_assoc()): ?>
                <tr class="rejected">
                    <td><?= $row['id'] ?></td>
                    <td><?= !empty($row['project_title']) ? htmlspecialchars($row['project_title']) : '(تم حذف المشروع)' ?>
                    </td>
                    <td><?= !empty($row['project_location']) ? htmlspecialchars($row['project_location']) : '(تم حذف المشروع)' ?>
                    </td>
                    <td>
                        <?php if (!empty($row['project_image'])): ?>
                            <img src="uploads/<?= htmlspecialchars($row['project_image']) ?>" alt="صورة المشروع">
                        <?php else: ?>
                            (تم حذف المشروع)
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['phone']) ?></td>
                    <td><?= htmlspecialchars($row['visit_date']) ?></td>
                    <td><?= htmlspecialchars($row['visit_time']) ?></td>
                    <td><?= htmlspecialchars($row['amount']) ?></td>
                    <td>
                        <?php if (!empty($row['payment_receipt'])): ?>
                            <a href="uploads/<?= htmlspecialchars($row['payment_receipt']) ?>" target="_blank">مشاهدة</a>
                        <?php else: ?> لا يوجد <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($row['created_at']) ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

</body>

</html>