<?php
require_once '../config/database.php';

// Query to fetch all violations with student details
$query = "SELECT 
    v.violation_id,
    u.nim,
    u.full_name,
    p.nama_prodi,
    vt.type_name,
    v.description,
    v.statement,
    v.violation_photo,
    vt.points,
    v.violation_date
FROM user_violations v
JOIN users u ON v.user_id = u.user_id
JOIN prodi p ON u.prodi_id = p.prodi_id
JOIN violation_types vt ON v.violation_type_id = vt.violation_type_id
ORDER BY v.violation_date DESC";

$stmt = $pdo->prepare($query);
$stmt->execute();
$violations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pelanggaran</title><!-- Favicon -->
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

    <style>
        /* Style untuk kolom sticky */
        .sticky-column {
            position: sticky;
            right: 0;
            background-color: #fff;
            /* Pastikan background putih agar konten di belakang tidak terlihat */
            z-index: 1;
        }

        /* Tambahkan shadow effect untuk membedakan kolom sticky */
        .sticky-column::after {
            content: '';
            position: absolute;
            left: -5px;
            top: 0;
            height: 100%;
            width: 5px;
            background: linear-gradient(to right, transparent, rgba(0, 0, 0, 0.05));
        }

        /* Pastikan header juga sticky */
        .table th.sticky-column {
            background-color: #fff;
            z-index: 2;
        }



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

                        <h2>Data Pelanggaran</h2>

                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="tableViolation" class="table w-100 table-borderless text-nowrap">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>NIM</th>
                                                <th>Nama</th>
                                                <th>Program Studi</th>
                                                <th>Jenis Pelanggaran</th>
                                                <th>Deskripsi</th>
                                                <th>Surat Pernyataan</th>
                                                <th>Foto Pelanggaran</th>
                                                <th class="sticky-column bg-white">Points</th>
                                                <th>Tanggal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($violations)): ?>
                                                <tr>
                                                    <td colspan="10" class="text-center">
                                                        <div class="alert alert-success mt-3" role="alert">
                                                            Tidak ada catatan pelanggaran! Pertahankan perilaku baik ini dan
                                                            terus
                                                            menjadi teladan yang positif!
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php else: ?>
                                                <?php $counter = 1;
                                                foreach ($violations as $violation): ?>
                                                    <tr class="border-bottom">
                                                        <td class="fw-bold"><?php echo $counter++; ?></td>
                                                        <td><?php echo htmlspecialchars($violation['nim']); ?></td>
                                                        <td><?php echo htmlspecialchars($violation['full_name']); ?></td>
                                                        <td><?php echo htmlspecialchars($violation['nama_prodi']); ?></td>
                                                        <td><?php echo htmlspecialchars($violation['type_name']); ?></td>
                                                        <td><?php echo htmlspecialchars($violation['description']); ?></td>
                                                        <td>
                                                            <a href="../uploads/<?php echo htmlspecialchars($violation['statement']); ?>"
                                                                data-fancybox
                                                                data-caption="Statement - <?php echo htmlspecialchars($violation['statement']); ?>"
                                                                class="btn btn-outline-primary w-100 btn-sm">
                                                                <i class='bx bxs-file-image'></i> Lihat Surat
                                                            </a>
                                                        </td>
                                                        <td>
                                                            <a href="../uploads/<?php echo htmlspecialchars($violation['violation_photo']); ?>"
                                                                data-fancybox
                                                                data-caption="Foto Pelanggaran - <?php echo htmlspecialchars($violation['violation_photo']); ?>"
                                                                class="btn btn-outline-primary w-100 btn-sm">
                                                                <i class='bx bxs-image'></i> Lihat Foto
                                                            </a>
                                                        </td>
                                                        <td class="sticky-column bg-white">
                                                            <span
                                                                class="badge bg-label-danger px-3 py-1 text-dark rounded-pill">
                                                                <i class="bx bx-down-arrow-alt text-danger"></i>
                                                                <?php echo $violation['points']; ?>
                                                            </span>
                                                        </td>
                                                        <td><?php echo $violation['violation_date']; ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <!-- Bootstrap Bundle JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.bootstrap5.js"></script>
    <!-- DataTables Search Panes -->
    <script src="https://cdn.datatables.net/searchpanes/2.3.3/js/dataTables.searchPanes.js"></script>
    <script src="https://cdn.datatables.net/searchpanes/2.3.3/js/searchPanes.bootstrap5.js"></script>
    <!-- DataTables Select -->
    <script src="https://cdn.datatables.net/select/2.1.0/js/dataTables.select.js"></script>
    <script src="https://cdn.datatables.net/select/2.1.0/js/select.bootstrap5.js"></script>

    <script>
        $(document).ready(function () {
            $('#tableViolation').DataTable({
                dom: 'Pfrtip',
                searchPanes: {
                    layout: 'columns-4'
                },
                select: true
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
    <script>
        Fancybox.bind("[data-fancybox]", {
            Images: {
                zoom: true,
            },
            Toolbar: {
                display: {
                    left: ["infobar"],
                    middle: [
                        "zoomIn",
                        "zoomOut",
                        "toggle1to1",
                        "rotateCCW",
                        "rotateCW",
                        "flipX",
                        "flipY",
                    ],
                    right: ["slideshow", "thumbs", "close"],
                },
            },
        });
    </script>
</body>

</html>