<?php
require_once '../config/database.php';

error_reporting(0);
session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Get filter parameters
$fakultas_id = isset($_GET['fakultas_id']) ? $_GET['fakultas_id'] : '';
$prodi_id = isset($_GET['prodi_id']) ? $_GET['prodi_id'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';
$point_range = isset($_GET['point_range']) ? $_GET['point_range'] : '';
$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'users.nim';
$sort_order = isset($_GET['sort_order']) ? $_GET['sort_order'] : 'ASC';

// Build the base query
$query = "
   SELECT 
    users.user_id,
    users.nim,
    users.full_name,
    fakultas.nama_fakultas,
    prodi.nama_prodi,
    COUNT(DISTINCT ua.submission_id) as total_activities,
    COUNT(DISTINCT uv.violation_id) as total_violations,
    COALESCE((
        SELECT SUM(al.points)
        FROM user_activities ua2
        JOIN activity_levels al ON ua2.activity_level_id = al.level_id
        WHERE ua2.user_id = users.user_id 
        AND ua2.status = 'approved'
    ), 0) as activity_points,
    COALESCE((
        SELECT SUM(vt.points)
        FROM user_violations uv2
        JOIN violation_types vt ON uv2.violation_type_id = vt.violation_type_id
        WHERE uv2.user_id = users.user_id 
        AND uv2.status = 'approved'
    ), 0) as violation_points,
    (
        COALESCE((
            SELECT SUM(al.points)
            FROM user_activities ua2
            JOIN activity_levels al ON ua2.activity_level_id = al.level_id
            WHERE ua2.user_id = users.user_id 
            AND ua2.status = 'approved'
        ), 0)
        -
        COALESCE((
            SELECT SUM(vt.points)
            FROM user_violations uv2
            JOIN violation_types vt ON uv2.violation_type_id = vt.violation_type_id
            WHERE uv2.user_id = users.user_id 
            AND uv2.status = 'approved'
        ), 0)
    ) as total_points
FROM users
LEFT JOIN prodi ON users.prodi_id = prodi.prodi_id
LEFT JOIN fakultas ON prodi.fakultas_id = fakultas.fakultas_id
LEFT JOIN user_activities ua ON users.user_id = ua.user_id AND ua.status = 'approved'
LEFT JOIN user_violations uv ON users.user_id = uv.user_id AND uv.status = 'approved'
WHERE 1=1
";

$params = array();

// Add filters
if ($fakultas_id) {
    $query .= " AND fakultas.fakultas_id = :fakultas_id";
    $params[':fakultas_id'] = $fakultas_id;
}
if ($prodi_id) {
    $query .= " AND prodi.prodi_id = :prodi_id";
    $params[':prodi_id'] = $prodi_id;
}
if ($search) {
    $query .= " AND (users.full_name LIKE :search OR users.nim LIKE :search)";
    $params[':search'] = "%$search%";
}

$query .= " GROUP BY users.user_id";

if ($point_range) {
    list($min, $max) = explode('-', $point_range);
    $query .= " HAVING total_points BETWEEN :min_points AND :max_points";
    $params[':min_points'] = $min;
    $params[':max_points'] = $max;
}

$query .= " ORDER BY $sort_by $sort_order";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Mahasiswa</title><!-- Favicon -->
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

    <style>
        .dt-search {
            display: none;
        }

        .dt-length {
            display: none;
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

                        <h2>Data Mahasiswa</h2>

                        <!-- Filters -->
                        <div class="card border-0 mb-4">
                            <div class="card-body">
                                <form method="GET" class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label">Fakultas</label>
                                        <select class="form-select" name="fakultas_id" id="fakultas_id">
                                            <option value="">Semua Fakultas</option>
                                            <?php
                                            $fakultas_stmt = $pdo->query("SELECT * FROM fakultas ORDER BY nama_fakultas");
                                            while ($row = $fakultas_stmt->fetch(PDO::FETCH_ASSOC)) {
                                                $selected = $fakultas_id == $row['fakultas_id'] ? 'selected' : '';
                                                echo "<option value='{$row['fakultas_id']}' $selected>{$row['nama_fakultas']}</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">Program Studi</label>
                                        <select class="form-select" name="prodi_id" id="prodi_id">
                                            <option value="">Semua Prodi</option>
                                            <?php
                                            if ($fakultas_id) {
                                                $prodi_stmt = $pdo->prepare("SELECT * FROM prodi WHERE fakultas_id = ? ORDER BY nama_prodi");
                                                $prodi_stmt->execute([$fakultas_id]);
                                                while ($row = $prodi_stmt->fetch(PDO::FETCH_ASSOC)) {
                                                    $selected = $prodi_id == $row['prodi_id'] ? 'selected' : '';
                                                    echo "<option value='{$row['prodi_id']}' $selected>{$row['nama_prodi']}</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <div class="col-md-2">
                                        <label class="form-label">Rentang Poin</label>
                                        <select class="form-select" name="point_range">
                                            <option value="">Semua</option>
                                            <option value="200-999" <?= $point_range == '200-999' ? 'selected' : '' ?>>
                                                Unggul
                                                (≥ 200)
                                            </option>
                                            <option value="100-199" <?= $point_range == '100-199' ? 'selected' : '' ?>>
                                                Sangat
                                                Baik
                                                (100-199)
                                            </option>
                                            <option value="50-99" <?= $point_range == '50-99' ? 'selected' : '' ?>>Baik
                                                (50-99)
                                            </option>
                                            <option value="0-49" <?= $point_range == '0-49' ? 'selected' : '' ?>>Kurang (<
                                                    50)</option>
                                        </select>
                                    </div>

                                    <div class="col-md-2">
                                        <label class="form-label">Pencarian</label>
                                        <input type="text" class="form-control" name="search"
                                            value="<?= htmlspecialchars($search) ?>"
                                            placeholder="Cari nama atau NIM...">
                                    </div>

                                    <div class="col-md-1">
                                        <label class="form-label">&nbsp;</label>
                                        <a href="?" class="btn btn-secondary w-100"><i
                                                class="fa-solid fa-rotate-right"></i></a>
                                    </div>
                                    <div class="col-md-1">
                                        <label class="form-label">&nbsp;</label>
                                        <button type="submit" class="btn btn-primary w-100"><i
                                                class="fa-solid fa-filter"></i></button>
                                    </div>

                                </form>
                            </div>
                        </div>


                        <!-- Table -->
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="studentTable" class="table table-hover nowrap">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>NIM</th>
                                                <th>Nama Lengkap</th>
                                                <th>Program Studi</th>
                                                <th>Total Aktivitas</th>
                                                <th>Total Pelanggaran</th>
                                                <th>Total Poin</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $no = 1;
                                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)):
                                                $total_points = $row['total_points'];
                                                $activity_points = $row['activity_points'];
                                                $violation_points = $row['violation_points'];

                                                if ($total_points >= 200) {
                                                    $predikat = "Unggul";
                                                    $badge_class = "bg-label-primary";
                                                } elseif ($total_points >= 100 && $total_points <= 199) {
                                                    $predikat = "Sangat Baik";
                                                    $badge_class = "bg-label-success";
                                                } elseif ($total_points >= 50 && $total_points <= 99) {
                                                    $predikat = "Baik";
                                                    $badge_class = "bg-label-warning";
                                                } else {
                                                    $predikat = "Kurang";
                                                    $badge_class = "bg-label-danger";
                                                }
                                                ?>
                                                <tr>
                                                    <td><?= $no++ ?></td>
                                                    <td><?= htmlspecialchars($row['nim']) ?></td>
                                                    <td><?= htmlspecialchars($row['full_name']) ?></td>
                                                    <td><?= htmlspecialchars($row['nama_prodi']) ?></td>
                                                    <td>
                                                        <?= $row['total_activities'] ?>
                                                        <span
                                                            class="text-success small rounded-pill">+<?= $activity_points ?></span>
                                                    </td>
                                                    <td>
                                                        <?= $row['total_violations'] ?>
                                                        <span
                                                            class="text-danger small rounded-pill">-<?= $violation_points ?></span>
                                                    </td>
                                                    <td>
                                                        <span class="badge <?= $badge_class ?>">
                                                            <?= $total_points ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <a href="detail-mahasiswa.php?id=<?= $row['user_id'] ?>"
                                                            class="btn btn-sm btn-primary">
                                                            <i class='bx bxs-user-detail'></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
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


    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.bootstrap5.js"></script>
    <!-- DataTables Select -->
    <script src="https://cdn.datatables.net/select/2.1.0/js/dataTables.select.js"></script>
    <script src="https://cdn.datatables.net/select/2.1.0/js/select.bootstrap5.js"></script>



    <!-- DataTables Initialization -->
    <script>
        $(document).ready(function () {
            $('#studentTable').DataTable({
                dom: 'Plfrtip',
                searchPanes: {
                    cascadePanes: true
                },
                select: true
            });
        });

        $(document).ready(function () {
            $('#fakultas_id').change(function () {
                var fakultas_id = $(this).val();
                $.ajax({
                    url: 'get_prodi.php',
                    type: 'POST',
                    data: { fakultas_id: fakultas_id },
                    success: function (response) {
                        $('#prodi_id').html(response);
                    }
                });
            });
        });
    </script>
</body>

</html>