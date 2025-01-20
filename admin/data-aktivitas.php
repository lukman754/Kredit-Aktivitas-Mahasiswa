<?php
require_once '../config/database.php';

error_reporting(0);
session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch categories for the filter dropdown
$categoriesQuery = $pdo->query("SELECT category_id, category_name FROM categories ORDER BY category_name");
$categories = $categoriesQuery->fetchAll(PDO::FETCH_ASSOC);

// Fetch assessment systems for the filter dropdown
$systemsQuery = $pdo->query("SELECT system_id, system_code FROM assessment_systems ORDER BY system_code");
$systems = $systemsQuery->fetchAll(PDO::FETCH_ASSOC);

// Get selected filters from query string
$selectedCategory = isset($_GET['category']) ? (int) $_GET['category'] : null;
$selectedSystem = isset($_GET['system']) ? (int) $_GET['system'] : null;

// Build the query based on filters
$query = "
    SELECT 
        a.activity_id,
        a.activity_name,
        c.category_name,
        s.system_code,
        COUNT(DISTINCT CASE WHEN ua.status = 'approved' THEN ua.submission_id END) AS total_activities,
        COALESCE(SUM(CASE WHEN ua.status = 'approved' THEN al.points ELSE 0 END), 0) AS total_points
    FROM activities a
    LEFT JOIN categories c ON a.category_id = c.category_id
    LEFT JOIN activity_levels al ON a.activity_id = al.activity_id
    LEFT JOIN assessment_systems s ON al.system_id = s.system_id
    LEFT JOIN user_activities ua ON a.activity_id = ua.activity_id AND ua.activity_level_id = al.level_id
    WHERE 1=1
";

$params = [];

if ($selectedCategory) {
    $query .= " AND a.category_id = :category_id";
    $params['category_id'] = $selectedCategory;
}

if ($selectedSystem) {
    $query .= " AND s.system_id = :system_id";
    $params['system_id'] = $selectedSystem;
}

$query .= " GROUP BY a.activity_id, a.activity_name, c.category_name, s.system_code ORDER BY a.activity_name";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$activities = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Aktivitas</title><!-- Favicon -->
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
    <title>Data Aktivitas</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Include Chart.js -->


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

<!DOCTYPE html>
<html lang="en">

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

                        <h2>Data Aktivitas</h2>



                        <!-- Table of activities -->
                        <div class="card border-0">
                            <div class="card-body">
                                <!-- Filters -->
                                <form method="GET" action="data-aktivitas.php" class="mb-4">
                                    <div class="d-flex justify-content-between">
                                        <div class="d-flex">
                                            <select name="category" id="category" class="form-select me-2">
                                                <option value="">-- Semua Kategori --</option>
                                                <?php foreach ($categories as $category): ?>
                                                    <option value="<?= $category['category_id']; ?>"
                                                        <?= $selectedCategory == $category['category_id'] ? 'selected' : ''; ?>>
                                                        <?= htmlspecialchars($category['category_name']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <select name="system" id="system" class="form-select">
                                                <option value="">-- Semua Sistem --</option>
                                                <?php foreach ($systems as $system): ?>
                                                    <option value="<?= $system['system_id']; ?>"
                                                        <?= $selectedSystem == $system['system_id'] ? 'selected' : ''; ?>>
                                                        <?= htmlspecialchars($system['system_code']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <div class="col-auto">
                                            <div id="control" class="btn-group">
                                                <button type="submit" class="btn btn-primary">Filter</button>
                                                <a href="data-aktivitas.php" class="btn btn-secondary">Reset</a>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                <div class="table-responsive">
                                    <table id="activityTable" class="table table-borderless table-hover">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>#</th>
                                                <th>Nama Aktivitas</th>
                                                <th>Sistem</th>
                                                <th>Total Aktivitas Yang Disetujui</th>
                                                <th>Total Point</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($activities)): ?>
                                                <?php foreach ($activities as $index => $activity): ?>
                                                    <tr>
                                                        <td><?= $index + 1; ?></td>
                                                        <td><?= htmlspecialchars($activity['activity_name']); ?></td>
                                                        <td><?= htmlspecialchars($activity['system_code'] ?? '-'); ?></td>
                                                        <td><?= $activity['total_activities']; ?></td>
                                                        <td><i
                                                                class="bx bx-up-arrow-alt text-success"></i><?= $activity['total_points']; ?>
                                                        </td>
                                                        <td>
                                                            <a
                                                                href="data-aktivitas-user.php?id=<?= $activity['activity_id']; ?>">
                                                                <span class="btn btn-sm btn-primary"><i
                                                                        class='bx bx-list-ul'></i></span>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="6" class="text-center">Tidak ada data aktivitas untuk
                                                        filter yang dipilih.
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <!-- Bootstrap Bundle JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

    <!-- Include Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.bootstrap5.js"></script>
    <!-- DataTables Select -->
    <script src="https://cdn.datatables.net/select/2.1.0/js/dataTables.select.js"></script>
    <script src="https://cdn.datatables.net/select/2.1.0/js/select.bootstrap5.js"></script>


    <!-- DataTables Initialization -->
    <script>
        $(document).ready(function () {
            $('#activityTable').DataTable({
                dom: 'Plfrtip',
                searchPanes: {
                    cascadePanes: true
                },
                select: true
            });
        });
    </script>
</body>

</html>