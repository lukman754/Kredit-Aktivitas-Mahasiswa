<?php
require_once '../config/database.php';

// Handle activity status updates
if (isset($_POST['update_activity_status'])) {
    $submission_id = $_POST['submission_id'];
    $status = $_POST['status'];
    $feedback = $_POST['feedback'];
    $admin_id = 1; // Replace with actual logged-in admin ID
    $stmt = $pdo->prepare("UPDATE user_activities SET status = ?, reviewed_by = ?, feedback = ?, reviewed_at = CURRENT_TIMESTAMP WHERE submission_id = ?");
    $stmt->execute([$status, $admin_id, $feedback, $submission_id]);

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Handle violation status updates
// Handle violation status updates
if (isset($_POST['update_violation_status'])) {
    $violation_id = $_POST['violation_id'];
    $status = $_POST['status'];
    $admin_id = 1; // Replace with actual logged-in admin ID

    $stmt = $pdo->prepare("UPDATE user_violations SET status = ?, reviewed_by = ?, reviewed_at = CURRENT_TIMESTAMP WHERE violation_id = ?");
    $stmt->execute([$status, $admin_id, $violation_id]);

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
// Fetch pending activities
$stmt = $pdo->query("
    SELECT ua.*, u.full_name, u.nim, a.activity_name, al.level_name, al.points 
    FROM user_activities ua
    JOIN users u ON ua.user_id = u.user_id
    JOIN activities a ON ua.activity_id = a.activity_id
    JOIN activity_levels al ON ua.activity_level_id = al.level_id
    WHERE ua.status = 'pending'
");
$pending_activities = $stmt->fetchAll();

// Fetch pending violations
$stmt = $pdo->query("
    SELECT uv.*, u.full_name, u.nim, vt.type_name, vt.points 
    FROM user_violations uv
    JOIN users u ON uv.user_id = u.user_id
    JOIN violation_types vt ON uv.violation_type_id = vt.violation_type_id
    WHERE uv.status = 'pending'
");
$pending_violations = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="../assets/img/icons/logo-unpam.png" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet" />

    <!-- Icons. Uncomment required icon fonts -->
    <link rel="stylesheet" href="../assets/vendor/fonts/boxicons.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="../assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="../assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="../assets/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" />
    <link rel="stylesheet" href="../assets/vendor/libs/apex-charts/apex-charts.css" />

    <!-- Page CSS -->

    <!-- Helpers -->
    <script src="../assets/vendor/js/helpers.js"></script>

    <script src="https://kit.fontawesome.com/f59e2d85df.js" crossorigin="anonymous"></script>

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="../assets/js/config.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>Pending Reviews</title>


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- aside -->
            <?php include 'includes/aside.php'; ?>
            <!-- / aside -->

            <div class="layout-page">
                <!-- Navbar -->
                <?php include 'includes/nav.php'; ?>
                <!-- / Navbar -->

                <div class="content-wrapper">
                    <div class="container-xxl py-4">

                        <h2>Review Aktivitas</h2>
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="card-title">Pending Activities</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover text-nowrap">
                                        <thead>
                                            <tr>
                                                <th>Student Name</th>
                                                <th>NIM</th>
                                                <th>Activity</th>
                                                <th>Level</th>
                                                <th>Points</th>
                                                <th>Description</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($pending_activities as $activity): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($activity['full_name']) ?></td>
                                                    <td><?= htmlspecialchars($activity['nim']) ?></td>
                                                    <td><?= htmlspecialchars($activity['activity_name']) ?></td>
                                                    <td><?= htmlspecialchars($activity['level_name']) ?></td>
                                                    <td><?= htmlspecialchars($activity['points']) ?></td>
                                                    <td><?= htmlspecialchars($activity['activity_desc']) ?></td>
                                                    <td>
                                                        <a href="detail-aktivitas.php?id=<?= $activity['submission_id'] ?>"
                                                            class="btn btn-sm btn-warning">
                                                            <i class="fa-solid fa-magnifying-glass"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-sm btn-primary"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#activityModal<?= $activity['submission_id'] ?>">
                                                            Review
                                                        </button>

                                                        <!-- Modal for activity review -->
                                                        <div class="modal fade"
                                                            id="activityModal<?= $activity['submission_id'] ?>"
                                                            tabindex="-1">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title">Review Activity</h5>
                                                                        <button type="button" class="btn-close"
                                                                            data-bs-dismiss="modal"></button>
                                                                    </div>
                                                                    <form method="POST">
                                                                        <div class="modal-body">
                                                                            <input type="hidden" name="submission_id"
                                                                                value="<?= $activity['submission_id'] ?>">
                                                                            <div class="mb-3">
                                                                                <label for="feedback"
                                                                                    class="form-label">Feedback</label>
                                                                                <textarea name="feedback"
                                                                                    class="form-control"
                                                                                    required></textarea>
                                                                            </div>
                                                                            <div class="mb-3">
                                                                                <label class="form-label">Status</label>
                                                                                <select name="status" class="form-select"
                                                                                    required>
                                                                                    <option value="">Select status</option>
                                                                                    <option value="approved">Approve
                                                                                    </option>
                                                                                    <option value="rejected">Reject</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="button" class="btn btn-secondary"
                                                                                data-bs-dismiss="modal">Close</button>
                                                                            <button type="submit"
                                                                                name="update_activity_status"
                                                                                class="btn btn-primary">Submit</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title">Pending Violations</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover text-nowrap">
                                        <thead>
                                            <tr>
                                                <th>Student Name</th>
                                                <th>NIM</th>
                                                <th>Violation Type</th>
                                                <th>Points</th>
                                                <th>Date</th>
                                                <th>Description</th>
                                                <th>Statement</th>
                                                <th>Violation Photo</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($pending_violations as $violation): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($violation['full_name']) ?></td>
                                                    <td><?= htmlspecialchars($violation['nim']) ?></td>
                                                    <td><?= htmlspecialchars($violation['type_name']) ?></td>
                                                    <td><?= htmlspecialchars($violation['points']) ?></td>
                                                    <td><?= htmlspecialchars($violation['violation_date']) ?></td>
                                                    <td><?= htmlspecialchars($violation['description']) ?></td>
                                                    <td>
                                                        <a href="../uploads/<?= htmlspecialchars($violation['statement']) ?>"
                                                            data-fancybox
                                                            data-caption="Statement - <?= htmlspecialchars($violation['statement']) ?>"
                                                            class="btn btn-outline-primary btn-sm">
                                                            <i class='bx bxs-file-image'></i> Lihat Surat
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <a href="../uploads/<?= htmlspecialchars($violation['violation_photo']) ?>"
                                                            data-fancybox
                                                            data-caption="Foto Pelanggaran - <?= htmlspecialchars($violation['violation_photo']) ?>"
                                                            class="btn btn-outline-primary btn-sm">
                                                            <i class='bx bxs-image'></i> Lihat Foto
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <form method="POST" class="d-flex gap-2">
                                                            <input type="hidden" name="violation_id"
                                                                value="<?= $violation['violation_id'] ?>">
                                                            <select name="status" class="form-select form-select-sm"
                                                                style="width: auto;" onchange="toggleButton(this);">
                                                                <option value="" disabled selected>Select</option>
                                                                <option value="approved">Approve</option>
                                                                <option value="rejected">Reject</option>
                                                            </select>
                                                            <button type="submit" name="update_violation_status"
                                                                class="btn btn-primary btn-sm" disabled>Update</button>
                                                        </form>
                                                    </td>

                                                    <script>
                                                        function toggleButton(select) {
                                                            const button = select.closest('form').querySelector('button[name="update_violation_status"]');
                                                            button.disabled = !select.value; // Disable if no value is selected
                                                        }
                                                    </script>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
    <script src="../assets/vendor/libs/jquery/jquery.js"></script>
    <script src="../assets/vendor/libs/popper/popper.js"></script>
    <script src="../assets/vendor/js/bootstrap.js"></script>
    <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>

    <script src="../assets/vendor/js/menu.js"></script>
    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="../assets/vendor/libs/apex-charts/apexcharts.js"></script>

    <!-- Main JS -->
    <script src="../assets/js/main.js"></script>

    <!-- Page JS -->
    <script src="../assets/js/dashboards-analytics.js"></script>


    <!-- Place this tag in your head or just before your close body tag. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>

    <!-- Bootstrap JS (Letakkan sebelum </body>) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>



</body>

</html>