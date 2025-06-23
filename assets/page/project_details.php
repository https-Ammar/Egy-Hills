<?php
include 'db.php';

if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    http_response_code(400);
    exit('Invalid project ID.');
}

$project_id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT * FROM projects WHERE id = ?");
$stmt->bind_param("i", $project_id);
$stmt->execute();
$result = $stmt->get_result();
$project = $result->fetch_assoc();
$stmt->close();

if (!$project) {
    http_response_code(404);
    exit('Project not found.');
}

$images = [];
$stmt = $conn->prepare("SELECT image FROM project_images WHERE project_id = ?");
$stmt->bind_param("i", $project_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $images[] = $row['image'];
}
$stmt->close();

$table_rows = [];
$stmt = $conn->prepare("SELECT col1, col2 FROM project_table WHERE project_id = ?");
$stmt->bind_param("i", $project_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $table_rows[] = $row;
}
$stmt->close();

function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Project Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
</head>

<body>
    <?php include './header.php'; ?>
    <?php include './loging.php'; ?>

    <main>
        <section class="py-5 bg-white">
            <div class="container pt-5 animate__animated animate__fadeInUp">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                        class="bi bi-geo-alt" viewBox="0 0 16 16">
                        <path
                            d="M12.166 8.94c-.524 1.062-1.234 2.12-1.96 3.07A32 32 0 0 1 8 14.58a32 32 0 0 1-2.206-2.57c-.726-.95-1.436-2.008-1.96-3.07C3.304 7.867 3 6.862 3 6a5 5 0 0 1 10 0c0 .862-.305 1.867-.834 2.94M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10">
                        </path>
                        <path d="M8 8a2 2 0 1 1 0-4 2 2 0 0 1 0 4m0 1a3 3 0 1 0 0-6 3 3 0 0 0 0 6"></path>
                    </svg>
                    <p class="m-0"><?= e($project['location']) ?></p>
                </div>

                <h2 class="elementor-heading-title elementor-size-default pb-3"><?= e($project['title']) ?></h2>
                <hr>

                <div class="d-flex align-items-center justify-content-between mt-5 animate__animated animate__fadeInUp">
                    <div class="d-flex align-items-center gap-3">
                        <span class="icon_span"><i class="fa-solid fa-chart-area"></i></span>
                        <span>
                            <p class="titel_project">Project Area</p>
                            <p class="des_project"><?= e($project['area']) ?></p>
                        </span>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <span class="icon_span"><i class="fa-solid fa-bed"></i></span>
                        <span>
                            <p class="titel_project">Number of rooms</p>
                            <p class="des_project"><?= (int) $project['beds'] ?></p>
                        </span>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <span class="icon_span"><i class="fa-regular fa-calendar"></i></span>
                        <span>
                            <p class="titel_project">Baths</p>
                            <p class="des_project"><?= (int) $project['baths'] ?></p>
                        </span>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <span class="icon_span"><i class="fa-solid fa-dollar-sign"></i></span>
                        <span>
                            <p class="titel_project">Price</p>
                            <p class="des_project"><?= e($project['price']) ?></p>
                        </span>
                    </div>
                </div>

                <?php if (!empty($project['image'])): ?>
                    <div class="cover_img_product mt-5 rounded-5 animate__animated animate__zoomIn"
                        style="background: url('/Egy-Hills/uploads/<?= e($project['image']) ?>') center/cover no-repeat; height: 70vh;">
                    </div>
                <?php endif; ?>
            </div>

            <div class="container py-5 animate__animated animate__fadeIn">
                <div class="row">
                    <div class="col-lg-8">
                        <h2 class="fw-bold"><?= e($project['subtitle']) ?></h2>
                        <p><?= nl2br(e($project['description'])) ?></p>
                    </div>
                    <div class="col-lg-4">
                        <h4 class="fw-bold">Property Details</h4>
                        <p><?= nl2br(e($project['details'])) ?></p>
                    </div>
                </div>

                <hr>

                <?php if (!empty($project['video_url'])): ?>
                    <video src="<?= e($project['video_url']) ?>" controls
                        class="animate__animated animate__fadeInUp"></video>
                <?php elseif (!empty($project['main_media'])): ?>
                    <div class="cover_img_product mt-5 rounded-5 animate__animated animate__zoomIn"
                        style="background: url('/Egy-Hills/uploads/<?= e($project['main_media']) ?>') center/cover no-repeat; height: 70vh;">
                    </div>
                <?php endif; ?>

                <div class="row">
                    <?php foreach ($images as $img): ?>
                        <div class="col-6">
                            <div class="cover_img_product mt-5 rounded-5 animate__animated animate__zoomIn"
                                style="background: url('/Egy-Hills/uploads/<?= e($img) ?>') center/cover no-repeat; height: 40vh;">
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <?php if (!empty($project['last_image'])): ?>
                        <div class="col-6">
                            <div class="cover_img_product mt-5 rounded-5 animate__animated animate__zoomIn"
                                style="background: url('/Egy-Hills/uploads/<?= e($project['last_image']) ?>') center/cover no-repeat; height: 40vh;">
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="col-lg-12 mt-5">
                        <h2 class="fw-bold"><?= e($project['extra_title']) ?></h2>
                        <p><?= nl2br(e($project['extra_text'])) ?></p>
                    </div>
                </div>

                <?php if (!empty($project['extra_image'])): ?>
                    <div class="row d-flex justify-content-center mt-5 animate__animated animate__fadeIn">
                        <img src="/Egy-Hills/uploads/<?= e($project['extra_image']) ?>" class="imp-main-image">
                    </div>
                <?php endif; ?>

                <div class="col-lg-12 mt-5">
                    <h2 class="fw-bold"><?= e($project['last_title']) ?: 'N/A' ?></h2>
                    <p><?= nl2br(e($project['last_text'])) ?: 'N/A' ?></p>
                </div>

                <?php if (!empty($table_rows)): ?>
                    <div class="table-responsive mt-4 animate__animated animate__fadeInUp">
                        <table class="table table-bordered align-middle text-center">

                            <tbody>
                                <?php foreach ($table_rows as $row): ?>
                                    <tr>
                                        <td><?= e($row['col1']) ?></td>
                                        <td><?= e($row['col2']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <div class="btn_payment flex animate__animated animate__fadeInUp">
                <a href="booking.php?id=<?= (int) $project['id'] ?>">
                    <button class="btn_payment right">
                        Book a Visit
                    </button>
                </a>

            </div>
        </section>

        <section id="footer"></section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../script/app.js"></script>
    <script src="../script/footer.js"></script>
</body>

</html>