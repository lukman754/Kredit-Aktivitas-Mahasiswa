<?php
require_once '../config/database.php';

error_reporting(0);
session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch fakultas
$stmt = $pdo->query("SELECT * FROM fakultas");
$fakultasList = $stmt->fetchAll();

// Fetch system codes
$stmt = $pdo->query("SELECT * FROM assessment_systems");
$systemsList = $stmt->fetchAll();

// Helper function to get total points for a student
function calculateTotalPoints($pdo, $userId)
{
    // Points from activities
    $activityPoints = $pdo->prepare("
        SELECT COALESCE(SUM(al.points), 0) as total
        FROM user_activities ua
        JOIN activity_levels al ON ua.activity_level_id = al.level_id
        WHERE ua.user_id = ? AND ua.status = 'approved'
    ");
    $activityPoints->execute([$userId]);
    $positivePoints = $activityPoints->fetch(PDO::FETCH_ASSOC)['total'];

    // Points from violations
    $violationPoints = $pdo->prepare("
        SELECT COALESCE(SUM(vt.points), 0) as total
        FROM user_violations uv
        JOIN violation_types vt ON uv.violation_type_id = vt.violation_type_id
        WHERE uv.user_id = ? AND uv.status = 'approved'
    ");
    $violationPoints->execute([$userId]);
    $negativePoints = $violationPoints->fetch(PDO::FETCH_ASSOC)['total'];

    return $positivePoints - $negativePoints;
}


// Get dashboard stats
$totalActivities = $pdo->query("SELECT COUNT(*) as count FROM activities")->fetch()['count'];
$totalViolations = $pdo->query("SELECT COUNT(*) as count FROM user_violations WHERE status = 'approved'")->fetch()['count'];

// Calculate total points for all students
$totalPoints = 0;
$students = $pdo->query("SELECT user_id FROM users");
while ($student = $students->fetch()) {
    $totalPoints += calculateTotalPoints($pdo, $student['user_id']);
}

// Get students data for top 10
$studentsQuery = $pdo->query("
    SELECT u.user_id, u.full_name, u.nim, p.nama_prodi
    FROM users u
    JOIN prodi p ON u.prodi_id = p.prodi_id
");

// Calculate points and sort for top 10
$topStudents = [];
while ($student = $studentsQuery->fetch()) {
    $points = calculateTotalPoints($pdo, $student['user_id']);
    $student['total_points'] = $points;
    $topStudents[] = $student;
}

// Sort by points and get top 10
usort($topStudents, function ($a, $b) {
    return $b['total_points'] - $a['total_points'];
});
$topStudents = array_slice($topStudents, 0, 10);

// Get prodi stats grouped by fakultas
$prodiStats = $pdo->query("
    SELECT 
        f.fakultas_id,
        f.nama_fakultas, 
        p.nama_prodi,
        p.prodi_id,
        (
            SELECT COUNT(DISTINCT ua.submission_id) 
            FROM user_activities ua 
            JOIN users u ON ua.user_id = u.user_id 
            WHERE u.prodi_id = p.prodi_id AND ua.status = 'approved'
        ) as total_activities,
        (
            SELECT COUNT(DISTINCT uv.violation_id)
            FROM user_violations uv 
            JOIN users u ON uv.user_id = u.user_id 
            WHERE u.prodi_id = p.prodi_id AND uv.status = 'approved'
        ) as total_violations
    FROM fakultas f
    JOIN prodi p ON f.fakultas_id = p.fakultas_id
    ORDER BY f.nama_fakultas, p.nama_prodi
")->fetchAll();

// Calculate total points per prodi
foreach ($prodiStats as &$stat) {
    $prodiPoints = 0;
    $studentsInProdi = $pdo->prepare("SELECT user_id FROM users WHERE prodi_id = ?");
    $studentsInProdi->execute([$stat['prodi_id']]);
    while ($student = $studentsInProdi->fetch()) {
        $prodiPoints += calculateTotalPoints($pdo, $student['user_id']);
    }
    $stat['total_points'] = $prodiPoints;
}

// Calculate student rankings
$studentRanks = [
    'unggul' => 0,
    'sangat_baik' => 0,
    'baik' => 0,
    'kurang' => 0
];

$allStudents = $pdo->query("SELECT user_id FROM users");
while ($student = $allStudents->fetch()) {
    $points = calculateTotalPoints($pdo, $student['user_id']);
    if ($points > 200)
        $studentRanks['unggul']++;
    elseif ($points >= 100)
        $studentRanks['sangat_baik']++;
    elseif ($points >= 50)
        $studentRanks['baik']++;
    else
        $studentRanks['kurang']++;
}

// Query for assessment system stats
$assessmentStats = $pdo->query("
    SELECT 
        s.system_id,
        s.system_name,
        s.system_code,
        COUNT(DISTINCT ua.submission_id) as total_activities,
        COALESCE(SUM(
            CASE 
                WHEN ua.status = 'approved' THEN al.points 
                ELSE 0 
            END
        ), 0) as total_points
    FROM assessment_systems s
    LEFT JOIN activity_levels al ON s.system_id = al.system_id
    LEFT JOIN user_activities ua ON al.level_id = ua.activity_level_id
    GROUP BY s.system_id, s.system_name, s.system_code
    ORDER BY s.system_name
")->fetchAll();

// Calculate totals for assessment stats
$totalActivities = array_sum(array_column($assessmentStats, 'total_activities'));
$totalPoints = array_sum(array_column($assessmentStats, 'total_points'));

// Build query based on filters
function buildQuery($fakultasId = '', $systemId = '')
{
    $baseQuery = "
        SELECT 
            p.prodi_id,
            p.nama_prodi,
            f.nama_fakultas,
            COUNT(DISTINCT ua.submission_id) as total_aktivitas,
            SUM(al.points) as total_points
        FROM prodi p
        LEFT JOIN fakultas f ON p.fakultas_id = f.fakultas_id
        LEFT JOIN users u ON u.prodi_id = p.prodi_id
        LEFT JOIN user_activities ua ON ua.user_id = u.user_id
        LEFT JOIN activity_levels al ON ua.activity_level_id = al.level_id
    ";

    $where = [];
    $params = [];

    if ($fakultasId) {
        $where[] = "p.fakultas_id = ?";
        $params[] = $fakultasId;
    }

    if ($systemId) {
        $where[] = "al.system_id = ?";
        $params[] = $systemId;
    }

    if (!empty($where)) {
        $baseQuery .= " WHERE " . implode(" AND ", $where);
    }

    $baseQuery .= " GROUP BY p.prodi_id";
    return [$baseQuery, $params];
}
?>


<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title><!-- Favicon -->
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
                        <!-- Stats Cards -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <div class="card h-100 shadow-sm border-0 bg-label-success">
                                    <div class="card-body">
                                        <h6 class="card-title mb-0 text-dark">Total Points</h6>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h2 class="mt-2 mb-0 fw-bold text-success">
                                                    <?= number_format($totalPoints) ?>
                                                </h2>
                                            </div>
                                            <div class="text-success">
                                                <i class="fa-solid fa-star fs-1 text-warning"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card h-100 shadow-sm border-0 bg-label-primary">
                                    <div class="card-body">
                                        <h6 class="card-title mb-0 text-dark">Total Aktivitas</h6>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h2 class="mt-2 mb-0 fw-bold text-primary">
                                                    <?= number_format($totalActivities) ?>
                                                </h2>
                                            </div>
                                            <div class="text-primary">
                                                <i class='bx bxs-bar-chart-alt-2 fs-1'></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card h-100 shadow-sm border-0 bg-label-danger">
                                    <div class="card-body">
                                        <h6 class="card-title mb-0 text-dark">Total Pelanggaran</h6>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h2 class="mt-2 mb-0 fw-bold text-danger">
                                                    <?= number_format($totalViolations) ?>
                                                </h2>
                                            </div>
                                            <div class="text-danger">
                                                <i class='bx bxs-error fs-1 text-danger'></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card h-100 shadow-sm border-0 bg-label-warning">
                                    <div class="card-body">

                                        <h6 class="card-title mb-0 text-dark">Menunggu Persetujuan</h6>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h2 class="mt-2 mb-0 fw-bold text-warning">
                                                    <?= number_format($notificationCount) ?>
                                                </h2>
                                            </div>
                                            <div class="text-warning">
                                                <i class='bx bxs-hourglass-top fs-1'></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="row mb-4">
                            <!-- Top 10 Students -->
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Top 10 Mahasiswa</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-hover w-100 text-nowrap">
                                                <thead>
                                                    <tr>
                                                        <th>No</th>
                                                        <th>Nama</th>
                                                        <th>NIM</th>
                                                        <th>Program Studi</th>
                                                        <th class="sticky-column bg-white">Points</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($topStudents as $student): ?>
                                                        <tr>
                                                            <td><?= array_search($student, $topStudents) + 1 ?></td>
                                                            <td><?= htmlspecialchars($student['full_name']) ?></td>
                                                            <td><?= htmlspecialchars($student['nim']) ?></td>
                                                            <td><?= htmlspecialchars($student['nama_prodi']) ?></td>
                                                            <td class="sticky-column bg-white">

                                                                <?php

                                                                if ($student['total_points'] >= 200) {
                                                                    $color = 'bg-label-primary';
                                                                } elseif ($student['total_points'] >= 100 && $student['total_points'] < 200) {
                                                                    $color = 'bg-label-success';
                                                                } elseif ($student['total_points'] >= 50 && $student['total_points'] < 100) {
                                                                    $color = 'bg-label-warning';
                                                                } else {
                                                                    $color = 'bg-label-danger';
                                                                }

                                                                ?>
                                                                <span class="badge <?= $color ?>">
                                                                    <?= number_format($student['total_points']) ?>
                                                                </span>

                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Student Rankings Pie Chart -->
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Distribusi Predikat Mahasiswa</h5>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="rankingChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Assessment System Stats Card -->
                        <!-- Assessment System Stats Card -->
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Statistik Assessment System</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Nama Luaran</th>
                                                <th class="text-end">Total Aktivitas</th>
                                                <th class="text-end">Total Points</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($assessmentStats as $stat): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($stat['system_code']) ?>
                                                    </td>
                                                    <td class="text-end"><?= number_format($stat['total_activities']) ?>
                                                    </td>
                                                    <td class="text-end"><?= number_format($stat['total_points']) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                        <tfoot class="table-dark">
                                            <tr>
                                                <td><strong>Total Keseluruhan</strong></td>
                                                <td class="text-end">
                                                    <strong><?= number_format($totalActivities) ?></strong>
                                                </td>
                                                <td class="text-end"><strong><?= number_format($totalPoints) ?></strong>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Filter Prodi Stats -->

                        <!-- Filter Prodi Stats -->
                        <div class="card">
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-6">
                                        <select class="form-select" id="fakultasFilter">
                                            <option value="">All Fakultas</option>
                                            <?php foreach ($fakultasList as $fakultas): ?>
                                                <option value="<?= $fakultas['fakultas_id'] ?>">
                                                    <?= htmlspecialchars($fakultas['nama_fakultas']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <select class="form-select" id="systemFilter">
                                            <option value="">All Systems</option>
                                            <?php foreach ($systemsList as $system): ?>
                                                <option value="<?= $system['system_id'] ?>">
                                                    <?= htmlspecialchars($system['system_code']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div id="levelCardsContainer" class="table-responsive mb-2">
                                    <!-- Kartu level counts akan dimuat di sini -->
                                </div>

                                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">

                                    <table class="table table-striped w-100" id="prodiTable">
                                        <thead class="bg-white sticky-top shadow-sm">
                                            <tr>
                                                <th>Prodi Name</th>
                                                <th class="d-none">Faculty</th>
                                                <th>Total Activities</th>
                                                <th>Total Points</th>
                                                <?php if (isset($_GET['system']) && $_GET['system'] == '3'): ?>
                                                    <?php
                                                    $levelStmt = $pdo->query("SELECT DISTINCT level_name FROM activity_levels WHERE system_id = 3");
                                                    while ($level = $levelStmt->fetch()): ?>
                                                        <th><?= htmlspecialchars($level['level_name']) ?></th>
                                                    <?php endwhile; ?>
                                                <?php endif; ?>
                                            </tr>
                                        </thead>
                                        <tbody id="tableBody">

                                        </tbody>
                                    </table>

                                </div>

                                <div class="mt-4">
                                </div>
                            </div>

                        </div>




                        <!-- Prodi Chart -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Grafik Aktivitas dan Pelanggaran per Prodi</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="prodiChart"></canvas>
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
    <script>
        let chart = null;

        function loadData() {
            const fakultasId = document.getElementById('fakultasFilter').value;
            const systemId = document.getElementById('systemFilter').value;

            fetch(`../api/get_prodi_data.php?fakultas=${fakultasId}&system=${systemId}`)
                .then(response => response.json())
                .then(data => {
                    updateTable(data);
                    updateChart(data);
                });
        }

        function updateTable(data) {
            const tbody = document.getElementById('tableBody');
            const levelCardsContainer = document.getElementById('levelCardsContainer');

            // Kosongkan konten sebelumnya
            tbody.innerHTML = '';
            levelCardsContainer.innerHTML = '';

            // Mengisi tabel utama
            data.forEach(row => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
            <td>${row.nama_prodi}</td>
            <td class="d-none">${row.nama_fakultas}</td>
            <td>${row.total_aktivitas}</td>
            <td>${row.total_points}</td>
        `;
                tbody.appendChild(tr);
            });

            // Menghitung total level counts dari semua prodi
            const totalLevelCounts = {};
            data.forEach(row => {
                if (row.level_counts) {
                    Object.entries(row.level_counts).forEach(([levelName, count]) => {
                        // Periksa jika levelName mengandung kata "Juara"
                        if (levelName.toLowerCase().includes('(juara')) {
                            totalLevelCounts[levelName] = (totalLevelCounts[levelName] || 0) + count;
                        }
                    });
                }
            });

            // Create table header once, outside the loop
            const table = document.createElement('table');
            table.className = 'table bg-label-warning table-borderless';
            table.innerHTML = `
            <thead class="d-none">
                <tr>
                <th>Level Prestasi</th>
                <th>Total Prestasi</th>
                </tr>
            </thead>
            <tbody></tbody>
            `;

            // Add rows for each level
            Object.entries(totalLevelCounts).forEach(([levelName, totalCount]) => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${levelName}</td>
                    <td><span class="badge bg-label-success">${totalCount}</span> Prestasi</td>
                `;
                table.querySelector('tbody').appendChild(row);
            });

            levelCardsContainer.appendChild(table);

        }

        function updateChart(data) {
            const ctx = document.getElementById('prodiChart').getContext('2d');

            if (chart) {
                chart.destroy();
            }

            chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.map(row => row.nama_prodi),
                    datasets: [{
                        label: 'Total Activities',
                        data: data.map(row => row.total_aktivitas),
                        backgroundColor: 'rgba(54, 162, 235, 0.5)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        x: {
                            ticks: {
                                minRotation: 90,
                                maxRotation: 90
                            }
                        },
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
        document.getElementById('fakultasFilter').addEventListener('change', loadData);
        document.getElementById('systemFilter').addEventListener('change', loadData);

        // Initial load
        loadData();
    </script>
    <script>

        // Pie Chart for Student Rankings
        new Chart(document.getElementById('rankingChart'), {
            type: 'pie',
            data: {
                labels: [
                    'Unggul 🌟',
                    'Sangat Baik 👍',
                    'Baik 🙂',
                    'Kurang 😔'
                ],
                datasets: [{
                    data: [
                        <?= $studentRanks['unggul'] ?>,
                        <?= $studentRanks['sangat_baik'] ?>,
                        <?= $studentRanks['baik'] ?>,
                        <?= $studentRanks['kurang'] ?>
                    ],
                    backgroundColor: [
                        '#007bff', // Biru
                        '#28a745', // Hijau
                        '#ffc107', // Kuning
                        '#dc3545'  // Merah
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>
</body>

</html>