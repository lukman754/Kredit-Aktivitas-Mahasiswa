<?php
require_once '../config/database.php';

error_reporting(0);
session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Ambil activity_id dari parameter URL
if (!isset($_GET['id'])) {
    header("Location: data-aktivitas.php");
    exit();
}
$activityId = $_GET['id'];

// Activities
$activitiesQuery = $pdo->prepare("SELECT * FROM activities WHERE activity_id = ?");
$activitiesQuery->execute([$activityId]);

// Ambil hasil query
$activites = $activitiesQuery->fetch(PDO::FETCH_ASSOC);

// Cek apakah aktivitas ditemukan
if (!$activites) {
    header("Location: data-aktivitas.php");
    exit();
}

// Ambil data aktivitas berdasarkan activity_id
$activityQuery = $pdo->prepare("
    SELECT 
        ua.submission_id,
        u.full_name,
        u.nim,
        p.nama_prodi,
        al.level_name AS activity_level,
        al.points AS level_points,
        ua.activity_desc,
        ua.submission_date,
        ua.status
    FROM user_activities ua
    JOIN users u ON ua.user_id = u.user_id
    JOIN prodi p ON u.prodi_id = p.prodi_id
    JOIN activity_levels al ON ua.activity_level_id = al.level_id
    WHERE ua.activity_id = ?
    ORDER BY ua.submission_date DESC
");
$activityQuery->execute([$activityId]);
$activities = $activityQuery->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="id">

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

    <!-- DataTables Search Panes Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/searchpanes/2.3.3/css/searchPanes.bootstrap5.css">
    <!-- DataTables Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.css">

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
    <title>Data Aktivitas Mahasiswa</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <style>
        .dt-length,
        .dt-search {
            display: inline-block;
            vertical-align: middle;
            margin-bottom: 10px;
        }

        .dt-length select,
        .dt-search input {
            display: inline-block;
            width: auto;
        }

        .dt-search input,
        .dt-length label,
        .dt-search {
            float: right;
        }

        .dt-search label,
        .dt-length label {
            margin-right: 5px;
        }
    </style>
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
                        <nav aria-label="breadcrumb mb-0">
                            <ol class="breadcrumb breadcrumb-style1">
                                <li class="breadcrumb-item">
                                    <a href="index.php">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="data-aktivitas.php">Data Aktivitas</a>
                                </li>
                                <li class="breadcrumb-item active">
                                    <?= htmlspecialchars($activites['activity_name']) ?>
                                </li>
                            </ol>
                        </nav>
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="activityTable" class="table table-hover table-striped align-middle">
                                        <thead class="table-dark">
                                            <tr>
                                                <th scope="col">No</th>
                                                <th scope="col">Nama Mahasiswa</th>
                                                <th scope="col">NIM</th>
                                                <th scope="col">Program Studi</th>
                                                <th scope="col">Deskripsi Aktivitas</th>
                                                <th scope="col">Activity Level</th>
                                                <th scope="col">Points</th>
                                                <th scope="col">Tanggal Pengajuan</th>
                                                <th scope="col">Status</th>
                                                <th scope="col">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (count($activities) > 0): ?>
                                                <?php foreach ($activities as $index => $activity): ?>
                                                    <tr>
                                                        <td><?= $index + 1 ?></td>
                                                        <td class="fw-semibold"><?= htmlspecialchars($activity['full_name']) ?>
                                                        </td>
                                                        <td><?= htmlspecialchars($activity['nim']) ?></td>
                                                        <td><?= htmlspecialchars($activity['nama_prodi']) ?></td>
                                                        <td><?= htmlspecialchars($activity['activity_desc']) ?></td>

                                                        <td>
                                                            <span
                                                                class="badge bg-primary"><?= htmlspecialchars($activity['activity_level']) ?></span>
                                                        </td>
                                                        <td>
                                                            <span
                                                                class="badge bg-success"><?= htmlspecialchars($activity['level_points']) ?></span>
                                                        </td>
                                                        <td><?= htmlspecialchars($activity['submission_date']) ?></td>
                                                        <td>
                                                            <?php
                                                            $statusClass = match (strtolower($activity['status'])) {
                                                                'pending' => 'bg-warning',
                                                                'approved' => 'bg-success',
                                                                'rejected' => 'bg-danger',
                                                                default => 'bg-secondary'
                                                            };
                                                            ?>
                                                            <span
                                                                class="badge <?= $statusClass ?>"><?= htmlspecialchars($activity['status']) ?></span>
                                                        </td>
                                                        <td><a
                                                                href="detail-aktivitas.php?id=<?= $activity['submission_id']; ?>"><span
                                                                    class="btn btn-sm btn-primary"><i
                                                                        class='bx bx-list-ul'></i></span></a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="9" class="text-center text-muted py-4">
                                                        <i class="fas fa-info-circle me-2"></i>
                                                        Tidak ada data aktivitas untuk ID ini.
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
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

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <!-- Bootstrap Bundle JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.bootstrap5.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.bootstrap5.js"></script>
    <!-- DataTables Search Panes -->
    <script src="https://cdn.datatables.net/searchpanes/2.3.3/js/dataTables.searchPanes.js"></script>
    <script src="https://cdn.datatables.net/searchpanes/2.3.3/js/searchPanes.bootstrap5.js"></script>
    <!-- DataTables Select -->
    <script src="https://cdn.datatables.net/select/2.1.0/js/dataTables.select.js"></script>
    <script src="https://cdn.datatables.net/select/2.1.0/js/select.bootstrap5.js"></script>


    <!-- Place this tag in your head or just before your close body tag. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>

    <!-- Bootstrap JS (Letakkan sebelum </body>) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function () {
            // Meredam semua warning dari DataTables
            $.fn.dataTable.ext.errMode = 'none';

            $('#activityTable').DataTable({
                dom: 'lfrtip',
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
                },
                // Menambahkan handling error yang silent
                initComplete: function () {
                    $(this).on('error.dt', function (e, settings, techNote, message) {
                        console.log('An error occurred: ', message);
                    });
                }
            }).on('error.dt', function (e, settings, techNote, message) {
                // Optional: log error ke console tanpa menampilkan ke user
                console.log('DataTables error: ', message);
            });
        });
    </script>

</body>

</html>