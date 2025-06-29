<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}



function uploadFile($file)
{
    if (!empty($file['name'])) {
        $uploadDir = '/Applications/MAMP/htdocs/Egy-Hills/uploads';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $name = time() . '_' . basename($file['name']);
        move_uploaded_file($file['tmp_name'], $uploadDir . '/' . $name);
        return $name;
    }
    return '';
}

function deleteImage($path)
{
    if (file_exists(__DIR__ . $path)) {
        unlink(__DIR__ . $path);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_team_card'])) {
        $image = uploadFile($_FILES['image']);
        $name = $conn->real_escape_string($_POST['name']);
        $phone = $conn->real_escape_string($_POST['phone']);
        $name = mb_substr($name, 0, 191);
        $conn->query("INSERT INTO about_team_cards (image, name, phone) VALUES ('$image', '$name', '$phone')");
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    if (isset($_POST['save_director_card'])) {
        $image = uploadFile($_FILES['image']);
        $title = $conn->real_escape_string($_POST['title']);
        $text = $conn->real_escape_string($_POST['text']);
        $title = mb_substr($title, 0, 191);
        $conn->query("INSERT INTO about_director_card (image, title, text) VALUES ('$image', '$title', '$text')");
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    if (isset($_POST['add_about_slider'])) {
        $image = uploadFile($_FILES['image']);
        $conn->query("INSERT INTO about_slider (image) VALUES ('$image')");
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    if (isset($_POST['add_initiative'])) {
        $title = $conn->real_escape_string($_POST['title']);
        $name = $conn->real_escape_string($_POST['name']);
        $link = $conn->real_escape_string($_POST['link']);
        $image = uploadFile($_FILES['image']);
        $title = mb_substr($title, 0, 191);
        $name = mb_substr($name, 0, 191);
        $link = mb_substr($link, 0, 191);
        $conn->query("INSERT INTO about_initiatives (title, name, link, image) VALUES ('$title', '$name', '$link', '$image')");
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['delete_team_card'])) {
        $id = intval($_GET['delete_team_card']);
        $result = $conn->query("SELECT image FROM about_team_cards WHERE id=$id");
        if ($result && $row = $result->fetch_assoc()) {
            deleteImage('/uploads/' . $row['image']);
        }
        $conn->query("DELETE FROM about_team_cards WHERE id=$id");
        header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
        exit();
    }

    if (isset($_GET['delete_director_card'])) {
        $id = intval($_GET['delete_director_card']);
        $result = $conn->query("SELECT image FROM about_director_card WHERE id=$id");
        if ($result && $row = $result->fetch_assoc()) {
            deleteImage('/uploads/' . $row['image']);
        }
        $conn->query("DELETE FROM about_director_card WHERE id=$id");
        header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
        exit();
    }

    if (isset($_GET['delete_about_slider'])) {
        $id = intval($_GET['delete_about_slider']);
        $result = $conn->query("SELECT image FROM about_slider WHERE id=$id");
        if ($result && $row = $result->fetch_assoc()) {
            deleteImage('/uploads/' . $row['image']);
        }
        $conn->query("DELETE FROM about_slider WHERE id=$id");
        header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
        exit();
    }

    if (isset($_GET['delete_initiative'])) {
        $id = intval($_GET['delete_initiative']);
        $result = $conn->query("SELECT image FROM about_initiatives WHERE id=$id");
        if ($result && $row = $result->fetch_assoc()) {
            deleteImage('/uploads/' . $row['image']);
        }
        $conn->query("DELETE FROM about_initiatives WHERE id=$id");
        header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
        exit();
    }
}

$about_team_cards = $conn->query("SELECT * FROM about_team_cards");
$about_director_card = $conn->query("SELECT * FROM about_director_card LIMIT 1");
$about_sliders = $conn->query("SELECT * FROM about_slider");
$initiatives = $conn->query("SELECT * FROM about_initiatives");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <!-- App favicon -->

    <!-- Vendor css (Require in all Page) -->
    <link href="assets/css/vendor.min.css" rel="stylesheet" type="text/css" />

    <!-- Icons css (Require in all Page) -->
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />

    <!-- App css (Require in all Page) -->
    <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" />

    <!-- Theme Config js (Require in all Page) -->
    <script src="assets/js/config.js"></script>
</head>

<body>


    <div class="container mt-5">

        <!-- العنصر 11 -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center gap-1">
                <h4 class="card-title flex-grow-1">Add Slider</h4>
                <a href="#" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal-1">
                    Add Product
                </a>
            </div>
            <div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0 table-hover table-centered">
                        <thead class="bg-light-subtle">
                            <tr>
                                <th style="width: 20px;">
                                    <div class="form-check ms-1">
                                        <input type="checkbox" class="form-check-input" id="customCheck11">
                                        <label class="form-check-label" for="customCheck11"></label>
                                    </div>
                                </th>
                                <th>img cover</th>

                                <th>Category</th>

                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php while ($slider = $about_sliders->fetch_assoc()): ?>

                                <tr id="row-11">

                                    <td>
                                        <div class="form-check ms-1">
                                            <input type="checkbox" class="form-check-input" id="check-11">
                                            <label class="form-check-label" for="check-11">&nbsp;</label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="rounded bg-light avatar-md d-flex align-items-center justify-content-center"
                                            style="background-image: url(/Egy-Hills/uploads/<?= $slider['image'] ?>)">
                                        </div>
                                    </td>


                                    <td>Real Estate</td>

                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="?delete_about_slider=<?= $slider['id'] ?>">
                                                <button onclick="return confirm('Are you sure?')"
                                                    class="btn btn-soft-danger btn-sm">
                                                    <iconify-icon icon="solar:trash-bin-minimalistic-2-broken"
                                                        class="align-middle fs-18"></iconify-icon>
                                                </button>
                                            </a>
                                        </div>
                                    </td>
                                </tr>


                            <?php endwhile; ?>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal 11 -->
        <div class="modal fade" id="addProductModal-1" tabindex="-1" aria-labelledby="addProductModalLabel-1"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Slider
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" enctype="multipart/form-data" class="p-3  rounded ">
                            <div class="mb-3">
                                <label for="image" class="form-label">Upload Image</label>
                                <input type="file" name="image" id="image" class="form-control" required>
                            </div>
                            <button type="submit" name="add_about_slider" class="btn btn-primary">Add Slider</button>
                        </form>

                    </div>
                </div>
            </div>
        </div>


        <!-- العنصر 11 -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center gap-1">
                <h4 class="card-title flex-grow-1">Our Journey</h4>
                <a href="#" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal-2">
                    Add Product
                </a>
            </div>
            <div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0 table-hover table-centered">
                        <thead class="bg-light-subtle">
                            <tr>
                                <th style="width: 20px;">
                                    <div class="form-check ms-1">
                                        <input type="checkbox" class="form-check-input" id="customCheck11">
                                        <label class="form-check-label" for="customCheck11"></label>
                                    </div>
                                </th>
                                <th>img</th>
                                <th>titel</th>


                                <th>years</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php while ($row = $about_team_cards->fetch_assoc()): ?>
                                <tr id="row-11">
                                    <td>
                                        <div class="form-check ms-1">
                                            <input type="checkbox" class="form-check-input" id="check-11">
                                            <label class="form-check-label" for="check-11">&nbsp;</label>
                                        </div>
                                    </td>
                                    <td>

                                        <div class="rounded bg-light avatar-md d-flex align-items-center justify-content-center"
                                            style="background-image: url(/Egy-Hills/uploads/<?= $row['image'] ?>)">
                                        </div>
                                    </td>
                                    <td> <?= htmlspecialchars($row['name']) ?></td>

                                    <td> <?= htmlspecialchars($row['phone']) ?></td>

                                    <td>
                                        <div class="d-flex gap-2">

                                            <a href="?delete_team_card=<?= $row['id'] ?>">
                                                <button onclick="return confirm('Are you sure?')"
                                                    class="btn btn-soft-danger btn-sm">
                                                    <iconify-icon icon="solar:trash-bin-minimalistic-2-broken"
                                                        class="align-middle fs-18"></iconify-icon>
                                                </button>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <div>



                                </div>
                            <?php endwhile; ?>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal 11 -->
        <div class="modal fade" id="addProductModal-2" tabindex="-1" aria-labelledby="addProductModalLabel-2"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Our Journey
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" enctype="multipart/form-data" class="p-4 rounded ">
                            <div class="mb-3">
                                <label for="image" class="form-label">Upload Image</label>
                                <input type="file" name="image" id="image" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" name="name" id="name" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="text" name="phone" id="phone" class="form-control">
                            </div>

                            <button type="submit" name="add_team_card" class="btn btn-primary">Add Card</button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
        <!-- العنصر 11 -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center gap-1">
                <h4 class="card-title flex-grow-1">The founders</h4>
                <a href="#" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal-4">
                    Add Product
                </a>
            </div>
            <div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0 table-hover table-centered">
                        <thead class="bg-light-subtle">
                            <tr>
                                <th style="width: 20px;">
                                    <div class="form-check ms-1">
                                        <input type="checkbox" class="form-check-input" id="customCheck11">
                                        <label class="form-check-label" for="customCheck11"></label>
                                    </div>
                                </th>
                                <th>img</th>
                                <th>name</th>
                                <th>text</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $director = $about_director_card->fetch_assoc(); ?>
                            <?php if ($director): ?>
                                <tr id="row-11">
                                    <td>
                                        <div class="form-check ms-1">
                                            <input type="checkbox" class="form-check-input" id="check-11">
                                            <label class="form-check-label" for="check-11">&nbsp;</label>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if ($director['image']): ?>
                                            <img src="/Egy-Hills/uploads/<?= htmlspecialchars($director['image']) ?>"
                                                alt="Director Image" class="rounded"
                                                style="width: 60px; height: 60px; object-fit: cover;">
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($director['title']) ?></td>
                                    <td>
                                        <?= htmlspecialchars(explode("\n", $director['text'])[0]) ?>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="?delete_director_card=<?= $director['id'] ?>">
                                                <button onclick="return confirm('Are you sure?')"
                                                    class="btn btn-soft-danger btn-sm">
                                                    <iconify-icon icon="solar:trash-bin-minimalistic-2-broken"
                                                        class="align-middle fs-18"></iconify-icon>
                                                </button>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal 11 -->
        <div class="modal fade" id="addProductModal-4" tabindex="-1" aria-labelledby="addProductModalLabel-4"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">The founders
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" enctype="multipart/form-data" class="p-4 rounded ">
                            <div class="mb-3">
                                <label for="image" class="form-label">Upload Image</label>
                                <input type="file" name="image" id="image" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label for="title" class="form-label">Title</label>
                                <input type="text" name="title" id="title" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label for="text" class="form-label">Description</label>
                                <textarea name="text" id="text" class="form-control" rows="4" required></textarea>
                            </div>

                            <button type="submit" name="save_director_card" class="btn btn-primary">Save</button>
                        </form>

                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center gap-1">
                <h4 class="card-title flex-grow-1">Initiatives</h4>
                <a href="#" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal-3">
                    Add Product
                </a>
            </div>
            <div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0 table-hover table-centered">
                        <thead class="bg-light-subtle">
                            <tr>
                                <th style="width: 20px;">
                                    <div class="form-check ms-1">
                                        <input type="checkbox" class="form-check-input" id="customCheck11">
                                        <label class="form-check-label" for="customCheck11"></label>
                                    </div>
                                </th>
                                <th>Product </th>
                                <th>titel</th>
                                <th>text</th>
                                <th>link</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $initiatives->fetch_assoc()): ?>
                                <tr id="row-11">
                                    <td>
                                        <div class="form-check ms-1">
                                            <input type="checkbox" class="form-check-input" id="check-11">
                                            <label class="form-check-label" for="check-11">&nbsp;</label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="rounded bg-light avatar-md d-flex align-items-center justify-content-center"
                                            style="background-image: url(/Egy-Hills/uploads/<?= htmlspecialchars($row['image']) ?>)">
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($row['title']) ?></td>
                                    <td>
                                        <?= htmlspecialchars($row['name']) ?>
                                    </td>

                                    <td>
                                        <a href="<?= htmlspecialchars($row['link']) ?>" target="_blank">link</a>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">

                                            <a href="?delete_initiative=<?= $row['id'] ?>">


                                                <button onclick="return confirm('Are you sure?')"
                                                    class="btn btn-soft-danger btn-sm">
                                                    <iconify-icon icon="solar:trash-bin-minimalistic-2-broken"
                                                        class="align-middle fs-18"></iconify-icon>
                                                </button>
                                            </a>


                                        </div>
                                    </td>
                                </tr>
                                <div>



                                </div>
                            <?php endwhile; ?>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal 11 -->
        <div class="modal fade" id="addProductModal-3" tabindex="-1" aria-labelledby="addProductModalLabel-3"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Initiatives</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" enctype="multipart/form-data" class="p-4 rounded ">
                            <div class="mb-3">
                                <label for="title" class="form-label">Initiative Title</label>
                                <input type="text" name="title" id="title" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label for="name" class="form-label">Initiator Name</label>
                                <input type="text" name="name" id="name" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label for="link" class="form-label">Website or Link (Optional)</label>
                                <input type="url" name="link" id="link" class="form-control">
                            </div>

                            <div class="mb-3">
                                <label for="image" class="form-label">Upload Image</label>
                                <input type="file" name="image" id="image" class="form-control" accept="image/*"
                                    required>
                            </div>

                            <button type="submit" name="add_initiative" class="btn btn-primary">Add Initiative</button>
                        </form>

                    </div>
                </div>
            </div>
        </div>

    </div>






    <script src="assets/js/vendor.js"></script>

    <script src="assets/js/app.js"></script>

    <script src="assets/vendor/jsvectormap/js/jsvectormap.min.js"></script>
    <script src="assets/vendor/jsvectormap/maps/world-merc.js"></script>
    <script src="assets/vendor/jsvectormap/maps/world.js"></script>


</body>

</html>