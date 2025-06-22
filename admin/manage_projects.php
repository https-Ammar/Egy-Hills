<?php
include 'db.php';

// جلب كل المشاريع
$result = $conn->query("SELECT id, image, title, location, price FROM projects");
?>

<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="UTF-8">
    <title>إدارة المشاريع</title>
    <style>
        body {
            font-family: Arial;
            max-width: 1200px;
            margin: auto;
            padding: 20px;
        }

        .projects {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }

        .card {
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 6px;
            text-align: center;
            position: relative;
        }

        .card img {
            width: 100%;
            max-height: 200px;
            object-fit: cover;
            border-radius: 4px;
        }

        .card h3 {
            margin: 10px 0 5px;
        }

        .card p {
            margin: 0 0 10px;
        }

        .btn {
            background: #3498db;
            color: #fff;
            border: none;
            padding: 8px 12px;
            border-radius: 4px;
            cursor: pointer;
            margin: 5px;
            display: inline-block;
        }

        .btn:hover {
            background: #2980b9;
        }

        .delete-btn {
            background: #e74c3c;
        }

        .delete-btn:hover {
            background: #c0392b;
        }

        .hidden {
            display: none;
        }

        .edit-form {
            margin-top: 10px;
            text-align: left;
        }

        .edit-form input {
            display: block;
            margin: 5px 0;
            width: 100%;
            padding: 5px;
        }
    </style>
</head>

<body>

    <h1>إدارة المشاريع</h1>

    <div class="projects">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="card" id="card-<?= $row['id'] ?>">
                    <?php if (!empty($row['image'])): ?>
                        <img src="uploads/<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['title']) ?>">
                    <?php endif; ?>
                    <h3><?= htmlspecialchars($row['title']) ?></h3>
                    <p>📍 <?= htmlspecialchars($row['location']) ?></p>
                    <p>💰 <?= htmlspecialchars($row['price']) ?></p>
                    <button class="btn delete-btn" onclick="deleteProject(<?= $row['id'] ?>)">🗑️ حذف</button>
                    <button class="btn" onclick="toggleEditForm(<?= $row['id'] ?>)">✏️ تعديل</button>

                    <form class="edit-form hidden" id="edit-form-<?= $row['id'] ?>"
                        onsubmit="event.preventDefault(); updateProject(<?= $row['id'] ?>);">
                        <input type="text" id="title-<?= $row['id'] ?>" value="<?= htmlspecialchars($row['title']) ?>"
                            placeholder="عنوان المشروع">
                        <input type="text" id="location-<?= $row['id'] ?>" value="<?= htmlspecialchars($row['location']) ?>"
                            placeholder="الموقع">
                        <input type="text" id="price-<?= $row['id'] ?>" value="<?= htmlspecialchars($row['price']) ?>"
                            placeholder="السعر">
                        <button class="btn" type="submit">💾 حفظ التعديلات</button>
                    </form>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>لا يوجد مشاريع مضافة حتى الآن.</p>
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
            form.classList.toggle('hidden');
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
                        location.reload(); // يمكن استبداله بتحديث جزء من الصفحة لو تحب
                    } else {
                        alert('❌ حدث خطأ أثناء التعديل');
                    }
                });
        }
    </script>

</body>

</html>