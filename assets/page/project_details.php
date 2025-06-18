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
    <title>Get in Touch</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

</head>

<body>
    <?php include './header.php'; ?>


    <main>


        <section class="py-5 bg-white">
            <div class="container pt-5">
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

                <div class="d-flex align-items-center justify-content-between mt-5">
                    <div class="d-flex align-items-center gap-3">
                        <span class="icon_span">
                            <i class="fa-solid fa-chart-area"></i>
                        </span>
                        <span>
                            <p class="titel_project">Project Area</p>
                            <p class="des_project"> <?= e($project['area']) ?></p>
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
                            <p class="titel_project">baths</p>
                            <p class="des_project"><?= (int) $project['baths'] ?></p>
                        </span>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <span class="icon_span"><i class="fa-solid fa-dollar-sign"></i></span>
                        <span>
                            <p class="titel_project">price</p>
                            <p class="des_project"><?= e($project['price']) ?></p>
                        </span>
                    </div>
                </div>


                <?php if (!empty($project['image'])): ?>

                    <div class="cover_img_product mt-5 rounded-5"
                        style="background: url('uploads/<?= e($project['image']) ?>') center/cover no-repeat; height: 70vh;">
                    </div>
                <?php else: ?>
                    <p>لا توجد صورة كافر.</p>
                <?php endif; ?>


            </div>







            <div class="container py-5">
                <div class="row">
                    <div class="col-lg-8">
                        <h2 class="fw-bold"> <?= e($project['subtitle']) ?></h2>
                        <p>
                            <?= nl2br(e($project['description'])) ?>
                        </p>

                    </div>

                    <div class="col-lg-4">
                        <h4 class="fw-bold">Property Details</h4>
                        <ul class="list-unstyled">

                            <p><?= nl2br(e($project['details'])) ?></p>

                        </ul>
                    </div>
                </div>


                <hr>


                <?php if (!empty($project['video_url'])): ?>
                    <video src="<?= e($project['video_url']) ?>" controls></video>
                <?php elseif (!empty($project['main_media'])): ?>


                    <div class="cover_img_product mt-5 rounded-5"
                        style="background: url('uploads/<?= e($project['main_media']) ?>') center/cover no-repeat; height: 70vh;">
                    </div>
                <?php else: ?>
                    <p>لا توجد صورة أو فيديو رئيسي.</p>
                <?php endif; ?>


                <div class="row">
                    <?php if (!empty($images)): ?>
                        <?php foreach ($images as $img): ?>

                            <div class="col-6">
                                <div class="cover_img_product mt-5 rounded-5"
                                    style="background: url('uploads/<?= e($img) ?>') center/cover no-repeat; height: 40vh;">
                                </div>
                            </div>



                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>لا توجد صور متعددة.</p>
                    <?php endif; ?>



                    <?php if (!empty($project['last_image'])): ?>

                        <div class="col-6">
                            <div class="cover_img_product mt-5 rounded-5"
                                style="background: url('uploads/<?= e($project['last_image']) ?>') center/cover no-repeat; height: 40vh;">
                            </div>
                        </div>
                    <?php else: ?>
                        <p>لا توجد صورة أخيرة.</p>
                    <?php endif; ?>




                    <div class="col-lg-12 mt-5">


                        <h2 class="fw-bold"><?= e($project['extra_title']) ?></h2>
                        <p>
                            <?= nl2br(e($project['extra_text'])) ?>
                        </p>

                    </div>


                </div>


                <?php if (!empty($project['extra_image'])): ?>
                    <div class="row d-flex justify-content-center justify-content-center  mt-5">
                        <img src="uploads/<?= e($project['extra_image']) ?>" class="imp-main-image">
                    </div>



                <?php else: ?>
                    <p>لا توجد صورة إضافية.</p>
                <?php endif; ?>




                <div class="col-lg-12 mt-5">

                    <h2 class="fw-bold"><?= e($project['last_title']) ?: 'لا يوجد' ?></h2>
                    <p>
                        <?= nl2br(e($project['last_text'])) ?: 'لا يوجد' ?>
                    </p>

                </div>



                <?php if (!empty($table_rows)): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle text-center">
                            <thead>
                                <tr>
                                    <th>العمود 1</th>
                                    <th>العمود 2</th>
                                </tr>
                            </thead>
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





            <div class="btn_payment flex">

                <button class="btn_payment left">next</button>
                <button class="btn_payment right"> <a href="booking.php?id=<?= (int) $project['id'] ?>">احجز معاينة</a>
                </button>

                <style>
                    .btn_payment.flex {
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        gap: 10px;
                    }

                    button.btn_payment {
                        width: 30%;
                        text-align: center;
                        padding: 30px;
                        background: black;

                    }


                    button.btn_payment.left {
                        border-radius: 30px 0 0 30px;
                    }

                    button.btn_payment.right {
                        border-radius: 0 30px 30px 0px;
                    }
                </style>
            </div>


        </section>




        <section id="footer"></section>

    </main>




    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../script/app.js"></script>
    <script src="../script/footer.js"></script>

</body>

</html>