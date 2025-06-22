<?php
include 'db.php';

// جلب كل المشاريع
$result = $conn->query("SELECT id, image, title, location, price FROM projects");
?>

<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="UTF-8" />
    <title>إدارة المشاريع</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>

<body class="bg-light">

    <div class="container py-4">
        <h1 class="mb-4 text-center">إدارة المشاريع</h1>

        <?php if ($result && $result->num_rows > 0): ?>
            <ul class="list-group">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <li class="list-group-item d-flex align-items-center justify-content-between flex-wrap"
                        id="card-<?= $row['id'] ?>">
                        <div class="d-flex align-items-center flex-grow-1 gap-3">
                            <?php if (!empty($row['image'])): ?>
                                <img src="uploads/<?= htmlspecialchars($row['image']) ?>"
                                    alt="<?= htmlspecialchars($row['title']) ?>" class="img-thumbnail"
                                    style="width: 120px; height: 80px; object-fit: cover;" />
                            <?php else: ?>
                                <div class="bg-secondary rounded" style="width: 120px; height: 80px;"></div>
                            <?php endif; ?>
                            <div class="project-info text-end flex-grow-1">
                                <h5 class="mb-1"><?= htmlspecialchars($row['title']) ?></h5>
                                <p class="mb-1">📍 <?= htmlspecialchars($row['location']) ?></p>
                                <p class="mb-0">💰 <?= htmlspecialchars($row['price']) ?></p>

                                <form class="edit-form mt-2 d-none" id="edit-form-<?= $row['id'] ?>"
                                    onsubmit="event.preventDefault(); updateProject(<?= $row['id'] ?>);">
                                    <div class="mb-2">
                                        <input type="text" class="form-control form-control-sm" id="title-<?= $row['id'] ?>"
                                            value="<?= htmlspecialchars($row['title']) ?>" placeholder="عنوان المشروع" />
                                    </div>
                                    <div class="mb-2">
                                        <input type="text" class="form-control form-control-sm" id="location-<?= $row['id'] ?>"
                                            value="<?= htmlspecialchars($row['location']) ?>" placeholder="الموقع" />
                                    </div>
                                    <div class="mb-2">
                                        <input type="text" class="form-control form-control-sm" id="price-<?= $row['id'] ?>"
                                            value="<?= htmlspecialchars($row['price']) ?>" placeholder="السعر" />
                                    </div>
                                    <button type="submit" class="btn btn-sm btn-success">💾 حفظ التعديلات</button>
                                </form>
                            </div>
                        </div>

                        <div class="btn-group btn-group-sm flex-column flex-sm-row gap-2 mt-3 mt-sm-0">
                            <button class="btn btn-danger" onclick="deleteProject(<?= $row['id'] ?>)">🗑️ حذف</button>
                            <button class="btn btn-primary" onclick="toggleEditForm(<?= $row['id'] ?>)">✏️ تعديل</button>
                        </div>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p class="text-center">لا يوجد مشاريع مضافة حتى الآن.</p>
        <?php endif; ?>
    </div>

    <script>
        function deleteProject(id) {
            if (!confirm("هل أنت متأكد من حذف هذا المشروع؟")) return;
            fetch('delete_project.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'id=' + id
            })
                .then(response => response.text())
                .then(data => {
                    if (data.trim() === 'success') {
                        document.getElementById('card-' + id).remove();
                    } else {
                        alert('❌ حدث خطأ أثناء الحذف');
                    }
                });
        }

        function toggleEditForm(id) {
            const form = document.getElementById('edit-form-' + id);
            form.classList.toggle('d-none');
        }

        function updateProject(id) {
            const title = document.getElementById('title-' + id).value;
            const location = document.getElementById('location-' + id).value;
            const price = document.getElementById('price-' + id).value;

            fetch('update_project.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `id=${id}&title=${encodeURIComponent(title)}&location=${encodeURIComponent(location)}&price=${encodeURIComponent(price)}`
            })
                .then(response => response.text())
                .then(data => {
                    if (data.trim() === 'success') {
                        alert('✅ تم حفظ التعديلات');
                        location.reload();
                    } else {
                        alert('❌ حدث خطأ أثناء التعديل');
                    }
                });
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>