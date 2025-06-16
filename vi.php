<?php
include 'db.php';

// جلب كل المشاريع
$projects = $conn->query("SELECT * FROM projects");

// تابع لجلب الصور المتعددة
function getMultiImages($conn, $project_id)
{
    $stmt = $conn->prepare("SELECT image FROM project_images WHERE project_id = ?");
    $stmt->bind_param("i", $project_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $images = [];
    while ($row = $result->fetch_assoc()) {
        $images[] = $row['image'];
    }
    $stmt->close();
    return $images;
}

// تابع لجلب صفوف الجدول
function getTableRows($conn, $project_id)
{
    $stmt = $conn->prepare("SELECT col1, col2 FROM project_table WHERE project_id = ?");
    $stmt->bind_param("i", $project_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    $stmt->close();
    return $rows;
}
?>

<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="UTF-8">
    <title>عرض المشاريع</title>
    <style>
        body {
            font-family: Arial;
            direction: rtl;
            padding: 20px;
        }

        .project {
            border: 1px solid #ccc;
            margin-bottom: 20px;
            padding: 15px;
        }

        img,
        video {
            max-width: 200px;
            display: block;
            margin-bottom: 10px;
        }

        table {
            border-collapse: collapse;
            margin-top: 10px;
        }

        table,
        th,
        td {
            border: 1px solid #333;
            padding: 5px;
        }
    </style>
</head>

<body>

    <h1>📌 المشاريع المضافة</h1>

    <?php if ($projects->num_rows > 0): ?>
        <?php while ($row = $projects->fetch_assoc()): ?>
            <div class="project">
                <h2><?= htmlspecialchars($row['title']) ?></h2>
                <p>📍 المكان: <?= htmlspecialchars($row['location']) ?></p>
                <p>🏠 المساحة: <?= htmlspecialchars($row['area']) ?></p>
                <p>🛏 الغرف: <?= (int) $row['beds'] ?> | 🛁 الحمامات: <?= (int) $row['baths'] ?> | 📐 الحجم:
                    <?= htmlspecialchars($row['size']) ?></p>
                <p>💰 السعر: <?= htmlspecialchars($row['price']) ?></p>

                <?php if (!empty($row['image'])): ?>
                    <img src="uploads/<?= htmlspecialchars($row['image']) ?>" alt="صورة كافر">
                <?php endif; ?>

                <?php if (!empty($row['video_url'])): ?>
                    <video controls src="<?= htmlspecialchars($row['video_url']) ?>"></video>
                <?php elseif (!empty($row['main_media'])): ?>
                    <video controls src="uploads/<?= htmlspecialchars($row['main_media']) ?>"></video>
                <?php endif; ?>

                <h3>📑 التفاصيل</h3>
                <p><strong><?= htmlspecialchars($row['subtitle']) ?></strong></p>
                <p><?= nl2br(htmlspecialchars($row['description'])) ?></p>
                <p>📌 تفاصيل القائمة:<br><?= nl2br(htmlspecialchars($row['details'])) ?></p>

                <?php if (!empty($row['extra_title'])): ?>
                    <h4><?= htmlspecialchars($row['extra_title']) ?></h4>
                    <p><?= nl2br(htmlspecialchars($row['extra_text'])) ?></p>
                <?php endif; ?>

                <?php if (!empty($row['extra_image'])): ?>
                    <img src="uploads/<?= htmlspecialchars($row['extra_image']) ?>" alt="صورة إضافية">
                <?php endif; ?>

                <h4>📷 الصور المتعددة</h4>
                <?php
                $multi = getMultiImages($conn, $row['id']);
                foreach ($multi as $img):
                    ?>
                    <img src="uploads/<?= htmlspecialchars($img) ?>" alt="صورة متعددة">
                <?php endforeach; ?>

                <h4>📊 جدول المشروع</h4>
                <?php
                $tableRows = getTableRows($conn, $row['id']);
                if ($tableRows):
                    ?>
                    <table>
                        <tr>
                            <th>العمود 1</th>
                            <th>العمود 2</th>
                        </tr>
                        <?php foreach ($tableRows as $tr): ?>
                            <tr>
                                <td><?= htmlspecialchars($tr['col1']) ?></td>
                                <td><?= htmlspecialchars($tr['col2']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                <?php else: ?>
                    <p>لا توجد صفوف جدول.</p>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>🚫 لا توجد مشاريع بعد.</p>
    <?php endif; ?>

</body>

</html>