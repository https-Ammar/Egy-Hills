<?php
include 'db.php';

// جلب كل المشاريع
$projects = $conn->query("SELECT * FROM projects");

if (!$projects) {
    die("Query Error: " . $conn->error);
}
?>


<!--  -->
<!--  -->


<!--  -->

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Our Projects</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1000px;
            margin: 20px auto;
            padding: 0 15px;
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
        }

        .projects-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }

        .project-card {
            border: 1px solid #ccc;
            border-radius: 8px;
            width: 300px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            text-decoration: none;
            color: inherit;
            transition: box-shadow 0.3s ease;
            background: #fff;
        }

        .project-card:hover {
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
        }

        .project-card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            display: block;
        }

        .project-content {
            padding: 15px;
        }

        .project-title {
            font-size: 1.2em;
            margin: 0 0 10px 0;
        }

        .project-location {
            font-weight: bold;
            color: #555;
            margin-bottom: 8px;
        }

        .project-price {
            color: #008000;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .project-info {
            font-size: 0.9em;
            color: #666;
        }
    </style>
</head>

<body>

    <h1>Our Projects</h1>

    <p>Total Projects Found: <?= $projects->num_rows ?></p>

    <?php if ($projects->num_rows > 0): ?>
        <div class="projects-container">
            <?php while ($row = $projects->fetch_assoc()): ?>
                <a href="project_details.php?id=<?= (int) $row['id'] ?>" class="project-card">
                    <?php if (!empty($row['image'])): ?>
                        <img src="uploads/<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['title']) ?>">
                    <?php else: ?>
                        <img src="placeholder.jpg" alt="No Image Available">
                    <?php endif; ?>
                    <div class="project-content">
                        <div class="project-location"><?= htmlspecialchars($row['location']) ?></div>
                        <h2 class="project-title"><?= htmlspecialchars($row['title']) ?></h2>
                        <div class="project-price"><?= htmlspecialchars($row['price']) ?></div>
                        <div class="project-info">
                            <?= (int) $row['beds'] ?> Beds | <?= (int) $row['baths'] ?> Baths |
                            <?= htmlspecialchars($row['size']) ?>
                        </div>
                    </div>
                </a>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p>No projects found.</p>
    <?php endif; ?>

</body>

</html>