<?php
require_once '../config/database.php';


$mahasiswa_id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("SELECT u.*, p.* FROM users u JOIN prodi p ON u.prodi_id = p.prodi_id WHERE u.user_id = ?");
$stmt->execute([$mahasiswa_id]);
$mhs = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch activities approved
$stmt = $pdo->prepare("
    SELECT ua.*, a.activity_name, al.level_name, al.points 
    FROM user_activities ua 
    JOIN activities a ON ua.activity_id = a.activity_id 
    JOIN activity_levels al ON ua.activity_level_id = al.level_id 
    WHERE ua.user_id = ? AND ua.status = 'approved'
");
$stmt->execute([$mahasiswa_id]);
$activities = $stmt->fetchAll();

// Fetch activities
$stmt = $pdo->prepare("
    SELECT ua.*, a.activity_name, al.level_name, al.points 
    FROM user_activities ua 
    JOIN activities a ON ua.activity_id = a.activity_id 
    JOIN activity_levels al ON ua.activity_level_id = al.level_id
    WHERE ua.user_id = ? ORDER BY ua.created_at DESC
");
$stmt->execute([$mahasiswa_id]);
$activities_table = $stmt->fetchAll();

// Fetch violations
$stmt = $pdo->prepare("
    SELECT uv.*, vt.type_name, vt.points 
    FROM user_violations uv 
    JOIN violation_types vt ON uv.violation_type_id = vt.violation_type_id 
    WHERE uv.user_id = ? AND uv.status = 'approved' 
    ORDER BY uv.created_at DESC
");
$stmt->execute([$mahasiswa_id]);
$violations = $stmt->fetchAll();


// Fetch the last 5 entries (activities or violations)
$stmt = $pdo->prepare("
    SELECT 'activity' AS type, ua.created_at AS date_added, al.points AS points, a.activity_name AS name
    FROM user_activities ua
    JOIN activities a ON ua.activity_id = a.activity_id
    JOIN activity_levels al ON ua.activity_level_id = al.level_id
    WHERE ua.user_id = ? AND ua.status = 'approved'


    UNION ALL

    SELECT 'violation' AS type, uv.created_at AS date_added, -vt.points AS points, vt.type_name AS name
    FROM user_violations uv
    JOIN violation_types vt ON uv.violation_type_id = vt.violation_type_id
    WHERE uv.user_id = ? AND uv.status = 'approved'

    ORDER BY date_added DESC
    LIMIT 5
");
$stmt->execute([$mahasiswa_id, $mahasiswa_id]);
$last_entries = $stmt->fetchAll();


// Fetch all activities and violations, ordered by the most recent entry
$stmt = $pdo->prepare("
SELECT 'activity' AS type, ua.created_at AS date_added, al.points, a.activity_name AS name
FROM user_activities ua
JOIN activities a ON ua.activity_id = a.activity_id
JOIN activity_levels al ON ua.activity_level_id = al.level_id
WHERE ua.user_id = ? AND ua.status = 'approved'

UNION ALL

SELECT 'violation' AS type, uv.created_at AS date_added, vt.points, vt.type_name AS name
FROM user_violations uv
JOIN violation_types vt ON uv.violation_type_id = vt.violation_type_id
WHERE uv.user_id = ? AND uv.status = 'approved'

ORDER BY date_added DESC
LIMIT 1;

");
$stmt->execute([$mahasiswa_id, $mahasiswa_id]);
$latest_entry = $stmt->fetch();

if ($latest_entry) {
    $latest_type = $latest_entry['type']; // Either 'activity' or 'violation'
    $latest_name = $latest_entry['name'];
    $latest_points = $latest_entry['points'];
    $latest_date = $latest_entry['date_added'];

    // Determine the class and icon based on the type
    $class = $latest_type === 'activity' ? 'text-success' : 'text-danger';
    $icon = $latest_type === 'activity' ? 'bx-chevron-up' : 'bx-chevron-down';
}

// Calculate total points
$activity_points = array_sum(array_column($activities, 'points'));
$violation_points = array_sum(array_column($violations, 'points'));
$total_points = $activity_points - $violation_points;

// Fetch activities for dropdown
$stmt = $pdo->prepare("
    SELECT c.category_id, c.category_name, a.activity_id, a.activity_name
    FROM categories c
    LEFT JOIN activities a ON c.category_id = a.category_id
    ORDER BY c.category_id, a.activity_name
");
$stmt->execute();
$all_data = $stmt->fetchAll();


// Fetch violation types for dropdown
$stmt = $pdo->prepare("SELECT * FROM violation_types");
$stmt->execute();
$violation_types = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

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
</head>

<!-- Add this CSS to your dashboard.php -->
<style>
    * {
        font-family: Poppins, sans-serif;
    }

    .doc-preview img {
        border-radius: 5px;
        max-width: 50%;
        margin-top: 10px;
    }

    @media (max-width: 900px) {
        .doc-preview img {
            max-width: 100%;
        }
    }


    .required-doc {
        border-left: 3px solid #595cd9;
        padding-left: 15px;
        margin-bottom: 20px;
    }

    .level-option {
        border-left: 5px solid #595cd9;
        padding: 15px;
        margin-bottom: 10px;
        border-radius: 5px;
        cursor: pointer;
    }

    .level-option:hover {
        background-color: #595cd9;
        color: white;
        font-weight: 500;

    }

    .level-option.selected {
        border-color: #595cd9;
        background-color: #595cd9;
        font-weight: 500;
        color: white;
    }



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

    .avatar i {
        font-size: 18px;
        /* Menyesuaikan ukuran ikon */
    }
</style>


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
                                    <a href="data-mahasiswa.php">Data Mahasiswa</a>
                                </li>
                                <li class="breadcrumb-item active">
                                    Detail Mahasiswa
                                </li>
                            </ol>
                        </nav>
                        <div class="card mb-4 shadow-sm">
                            <div class="card-header bg-light">
                                <h5 class="card-title mb-0">Identitas Mahasiswa</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <h6 class="mb-0"><?php echo $mhs['full_name']; ?></h6>
                                            <span class="text-muted d-block">NIM: <?php echo $mhs['nim']; ?></span>
                                        </div>
                                        <div class="mb-3">
                                            <span class="text-muted d-block">Program Studi:
                                                <?php echo $mhs['nama_prodi']; ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row g-2 mb-2">

                            <div class="col-md-12 col-xl-8">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between flex-sm-row flex-column gap-3">
                                            <?php
                                            // Logika untuk menentukan predikat, warna, emoji, dan pesan
                                            if ($total_points > 200) {
                                                $predikat = "Unggul";
                                                $badge_class = "bg-label-primary";
                                                $emoji = "🌟";
                                                $pesan = "Mahasiswa ini sangat aktif dan berprestasi. Terus dorong potensinya.";
                                            } elseif ($total_points >= 100 && $total_points <= 199) {
                                                $predikat = "Sangat Baik";
                                                $badge_class = "bg-label-success";
                                                $emoji = "👍";
                                                $pesan = "Performa baik, tetap beri motivasi untuk lebih optimal.";
                                            } elseif ($total_points >= 50 && $total_points <= 99) {
                                                $predikat = "Baik";
                                                $badge_class = "bg-label-warning";
                                                $emoji = "🙂";
                                                $pesan = "Cukup aktif, arahkan agar bisa lebih berkembang.";
                                            } else {
                                                $predikat = "Kurang";
                                                $badge_class = "bg-label-danger";
                                                $emoji = "😔";
                                                $pesan = "Kurang aktif, beri dukungan dan arahan lebih.";
                                            }
                                            ?>

                                            <div class="align-items-start justify-content-between">
                                                <div class="card-title">
                                                    <h5 class="text-nowrap mb-2">Total Point</h5>
                                                    <span
                                                        class="badge <?php echo $badge_class; ?> rounded-pill"><?php echo $predikat; ?>
                                                        <?php echo $emoji; ?></span>
                                                </div>
                                                <div class="mt-sm-auto">
                                                    <?php if (isset($latest_entry) && isset($latest_points) && isset($latest_name) && isset($class) && isset($icon)): ?>
                                                        <small class="fw-semibold <?php echo htmlspecialchars($class); ?>">
                                                            <i class="bx <?php echo htmlspecialchars($icon); ?>"></i>
                                                            <?php echo htmlspecialchars($latest_points); ?>
                                                            (<?php echo htmlspecialchars($latest_name); ?>)
                                                        </small>
                                                    <?php else: ?>
                                                        <small class="text-muted fw-semibold">Belum ada data
                                                            aktivitas</small>
                                                    <?php endif; ?>

                                                    <h3 class="mb-0"><?php echo $total_points; ?> Point</h3>
                                                    <p><?php echo $pesan; ?></p>
                                                </div>
                                            </div>
                                            <div>
                                                <canvas id="activityChart" width="150" height="150"></canvas>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 col-xl-4">
                                <div class="row g-2">
                                    <div class="col-xl-12 col-md-6">
                                        <div class="card p-2">
                                            <div class="card-body d-flex justify-content-between">
                                                <div class="card-title mb-0 d-flex align-items-center">
                                                    <div class="avatar flex-shrink-0">
                                                        <img src="../assets/img/icons/unicons/chart-success.png"
                                                            alt="chart success" class="rounded" />
                                                    </div>

                                                    <span class="fw-semibold ms-2">
                                                        <h5 class="mb-0">Point Kegiatan</h5>
                                                    </span>
                                                </div>
                                                <span class="badge bg-label-success text-black mb-0"
                                                    style="font-size: large"><i
                                                        class="bx bx-up-arrow-alt text-success"></i><?php echo $activity_points; ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-12 col-md-6">
                                        <div class="card p-2">
                                            <div class="card-body d-flex justify-content-between">
                                                <div class="card-title mb-0 d-flex align-items-center">
                                                    <div
                                                        class="avatar flex-shrink-0 bg-label-danger rounded d-flex align-items-center justify-content-center">
                                                        <i class="fa-solid fa-circle-exclamation"></i>
                                                    </div>


                                                    <span class="fw-semibold ms-2">
                                                        <h5 class="mb-0">Point Pelanggaran</h5>
                                                    </span>
                                                </div>
                                                <span class="badge bg-label-danger text-black mb-0"
                                                    style="font-size: large"><i
                                                        class="bx bx-down-arrow-alt text-danger"></i><?php echo $violation_points; ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="divider text-end">
                            <div class="divider-text">
                                <i class="bx bx-cut bx-rotate-180"></i>
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-md-12">
                                <div class="card p-2">
                                    <h5 class="card-header">Tabel Kegiatan</h5>
                                    <div class="table-responsive text-nowrap position-relative">
                                        <table class="table table-borderless">
                                            <thead>
                                                <tr class="text-nowrap">
                                                    <th>No</th>
                                                    <th>Kegiatan</th>
                                                    <th>Deskripsi</th>
                                                    <th>Kategori</th>
                                                    <th class="sticky-column">Points</th>
                                                    <th>Status</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (empty($activities_table)): ?>
                                                    <tr>
                                                        <td colspan="7" class="text-center">
                                                            <div class="alert alert-warning mt-3" role="alert">
                                                                Belum ada data kegiatan! Tetap semangat untuk aktif
                                                                berkegiatan dan raih
                                                                lebih banyak pencapaian!
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php else: ?>
                                                    <?php
                                                    $counter = 1;
                                                    foreach ($activities_table as $index => $activity): ?>
                                                        <tr class="border-bottom">
                                                            <td class="fw-bold"><?php echo $counter++; ?></td>
                                                            <td><?php echo htmlspecialchars($activity['activity_name']); ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($activity['activity_desc']); ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($activity['level_name']); ?>
                                                            </td>
                                                            <td class="sticky-column bg-white">
                                                                <span
                                                                    class="badge bg-label-success px-3 py-1 text-dark rounded-pill">
                                                                    <i class="bx bx-up-arrow-alt text-success"></i>
                                                                    <?php echo $activity['points']; ?>
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <?php
                                                                $status = strtolower($activity['status']);
                                                                $badgeClass = '';
                                                                switch ($status) {
                                                                    case 'pending':
                                                                        $badgeClass = 'badge bg-warning text-dark';
                                                                        break;
                                                                    case 'approved':
                                                                        $badgeClass = 'badge bg-success';
                                                                        break;
                                                                    case 'rejected':
                                                                        $badgeClass = 'badge bg-danger';
                                                                        break;
                                                                    default:
                                                                        $badgeClass = 'badge bg-secondary';
                                                                }
                                                                ?>
                                                                <span class="<?php echo $badgeClass; ?>">
                                                                    <?php echo ucfirst($status); ?>
                                                                </span>
                                                            </td>
                                                            <td class="d-flex gap-2">
                                                                <a href="detail-aktivitas.php?id=<?php echo $activity['submission_id']; ?>"
                                                                    class="btn btn-sm btn-primary">
                                                                    <i class='bx bxs-message-square-detail'></i>
                                                                </a>
                                                                <form action="../delete_activity.php" method="POST"
                                                                    onsubmit="return confirm('Are you sure you want to delete this activity and all associated media?');">
                                                                    <input type="hidden" name="submission_id"
                                                                        value="<?php echo $activity['submission_id']; ?>">

                                                                    <button type="submit" class="btn btn-sm btn-danger"
                                                                        data-bs-toggle="tooltip" data-bs-offset="0,4"
                                                                        data-bs-placement="left" data-bs-html="true" title=""
                                                                        data-bs-original-title="<span>Hapus Data Kegiatan</span>">
                                                                        <i class='bx bxs-message-square-x'></i>
                                                                    </button>

                                                                </form>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </tbody>

                                        </table>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="row mt-2 mb-4">
                            <div class="col-md-12">
                                <div class="card p-2">
                                    <h5 class="card-header">Tabel Pelanggaran</h5>
                                    <div class="table-responsive text-nowrap position-relative">
                                        <table class="table table-borderless">
                                            <thead>
                                                <tr class="text-nowrap">
                                                    <th>No</th>
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
                                                        <td colspan="7" class="text-center">
                                                            <div class="alert alert-success mt-3" role="alert">
                                                                Tidak ada catatan pelanggaran! Pertahankan perilaku
                                                                baik ini
                                                                dan terus
                                                                menjadi teladan yang positif!
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php else: ?>
                                                    <?php $counter = 1;
                                                    foreach ($violations as $violation): ?>
                                                        <tr class="border-bottom">
                                                            <td class="fw-bold"><?php echo $counter++; ?></td>
                                                            <td><?php echo htmlspecialchars($violation['type_name']); ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($violation['description']); ?>
                                                            </td>
                                                            <td>
                                                                <a href="../uploads/<?php echo htmlspecialchars($violation['statement']); ?>"
                                                                    data-fancybox
                                                                    data-caption="Statement - <?php echo htmlspecialchars($violation['statement']); ?>"
                                                                    class="btn btn-outline-primary w-100 btn-sm"><i
                                                                        class='bx bxs-file-image'></i> Lihat
                                                                    Surat</a>
                                                            </td>
                                                            <td>
                                                                <a href="../uploads/<?php echo htmlspecialchars($violation['violation_photo']); ?>"
                                                                    data-fancybox
                                                                    data-caption="Foto Pelanggaran - <?php echo htmlspecialchars($violation['violation_photo']); ?>"
                                                                    class="btn btn-outline-primary w-100 btn-sm"><i
                                                                        class='bx bxs-image'></i>
                                                                    Lihat
                                                                    Foto</a>
                                                            </td>
                                                            <td class="sticky-column bg-white">
                                                                <span
                                                                    class="badge bg-label-danger px-3 py-1 text-dark rounded-pill">
                                                                    <i
                                                                        class="bx bx-down-arrow-alt text-danger"></i><?php echo $violation['points']; ?>
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
    </div>


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

    <script>
        // Function to preview image
        function previewImage(input, previewElementId) {
            const preview = document.getElementById(previewElementId);
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block'; // Show the preview image
                };
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.src = '';
                preview.style.display = 'none'; // Hide the preview image
            }
        }

        // Event listeners for file inputs
        document.getElementById('violationPhoto').addEventListener('change', function () {
            previewImage(this, 'violationPhotoPreview');
        });

        document.getElementById('statement').addEventListener('change', function () {
            previewImage(this, 'statementPreview');
        });
    </script>


    <script>
        // Ambil NIM dari session PHP
        const nimSession = "<?php echo htmlspecialchars($nimSession); ?>";

        // Ambil elemen input NIM, tombol submit, dan pesan error
        const nimInput = document.getElementById('nameWithTitle');
        const submitBtn = document.getElementById('submitBtn');
        const errorMessage = document.getElementById('errorMessage');

        // Fungsi untuk memeriksa NIM
        nimInput.addEventListener('input', function () {
            if (nimInput.value === nimSession) {
                submitBtn.disabled = false; // Aktifkan tombol submit
                errorMessage.style.display = 'none'; // Sembunyikan pesan error
            } else {
                submitBtn.disabled = true; // Nonaktifkan tombol submit
                errorMessage.style.display = 'inline'; // Tampilkan pesan error
            }
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const activitySelect = document.getElementById('activitySelect');
            const activityForm = document.getElementById('activityForm');
            const submitButton = document.getElementById('submitActivity');
            let requiredDocs = [];

            // Handle activity selection
            activitySelect.addEventListener('change', function () {
                if (this.value) {
                    fetch(`get_activity_docs.php?activity_id=${this.value}`)
                        .then(response => response.json())
                        .then(docs => {
                            requiredDocs = docs;
                            document.querySelector('[data-step="1"]').disabled = false;
                            renderDocumentForms(docs);
                            loadActivityLevels(this.value);
                        });
                }
            });



            function renderDocumentForms(docs) {
                const container = document.getElementById('documentForms');
                container.innerHTML = '<p class="mb-3 text-warning">Required Documents *</p>';

                docs.forEach(doc => {
                    const docDiv = document.createElement('div');
                    docDiv.className = 'required-doc';

                    if (doc.doc_type_name.toLowerCase().includes('url')) {
                        docDiv.innerHTML = `
            <div class="mb-3">
                <label>${doc.doc_type_name} <span class="text-danger">*</span></label>
                <input type="url" name="urls[${doc.doc_type_id}]" 
                       class="form-control doc-input" data-doc-type="${doc.doc_type_id}"
                       placeholder="https://" required>
            </div>
            `;
                    } else {
                        docDiv.innerHTML = `
            <div class="mb-3">
                <label>${doc.doc_type_name} <span class="text-danger">*</span></label>
                <input type="file" name="documents[${doc.doc_type_id}]" 
                       class="form-control doc-input" data-doc-type="${doc.doc_type_id}"
                       accept=".jpg,.jpeg,.png,.pdf" required>
                <div class="doc-preview doc-preview-${doc.doc_type_id}"></div>
            </div>
            `;
                    }
                    container.appendChild(docDiv);

                    // Add input event listeners
                    const input = docDiv.querySelector('.doc-input');
                    if (input.type === 'file') {
                        input.addEventListener('change', function () {
                            validateFile(input);
                            handleFilePreview(this);
                            checkAllInputs();
                        });
                    } else {
                        input.addEventListener('input', function () {
                            checkAllInputs();
                        });
                    }
                });
            }

            function validateFile(input) {
                const file = input.files[0];
                if (file) {
                    const allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf'];
                    const fileExtension = file.name.split('.').pop().toLowerCase();

                    if (!allowedExtensions.includes(fileExtension)) {
                        alert('Only JPG, JPEG, PNG, and PDF files are allowed.');
                        input.value = ''; // Clear the input field
                    }
                }
            }

            function handleFilePreview(input) {
                const previewDiv = input.nextElementSibling;
                previewDiv.innerHTML = '';

                if (input.files && input.files[0]) {
                    const file = input.files[0];
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function (e) {
                            const img = document.createElement('img');
                            img.src = e.target.result;
                            img.className = 'img-thumbnail';
                            previewDiv.appendChild(img);
                        }
                        reader.readAsDataURL(file);
                    } else {
                        previewDiv.innerHTML = `<p class="mt-2">File selected: ${file.name}</p>`;
                    }
                }
            }

            function loadActivityLevels(activityId) {
                // Validate activityId before calling the API
                if (!activityId || isNaN(activityId)) {
                    console.error("Invalid activityId:", activityId);
                    return;
                }

                fetch(`get_activity_levels.php?activity_id=${activityId}`)
                    .then(response => response.json())
                    .then(levels => {
                        const container = document.getElementById('levelOptions');
                        container.innerHTML = ''; // Clear previous options

                        // Check if the data is empty or not
                        if (levels.length === 0) {
                            const noDataMessage = document.createElement('div');
                            noDataMessage.textContent = "No levels available for this activity.";
                            container.appendChild(noDataMessage);
                            return;
                        }

                        levels.forEach(level => {
                            const div = document.createElement('div');
                            div.className = 'level-option shadow-sm is-active';
                            div.innerHTML = `
                    <div class="form-check">
                        <input type="radio" name="activity_level_id" 
                               value="${level.level_id}" class="form-check-input shadow-sm" required>
                        <label class="form-check-label d-flex justify-content-between align-items-center">
                            <p>${level.level_name}</p>
                           <span class="bg-label-success px-2 rounded-pill fw-bold text-primary">+${level.points}</span>
                        </label>
                    </div>
                `;
                            container.appendChild(div);
                        });

                        // Handle level selection styling
                        const levelOptions = document.querySelectorAll('.level-option');
                        levelOptions.forEach(option => {
                            option.addEventListener('click', function () {
                                levelOptions.forEach(opt => opt.classList.remove('selected'));
                                this.classList.add('selected');
                                const radio = this.querySelector('input[type="radio"]');
                                radio.checked = true;
                                checkAllInputs();
                            });
                        });
                    })
                    .catch(error => {
                        console.error("Error loading levels:", error);
                    });
            }

            function checkAllInputs() {
                const inputs = document.querySelectorAll('.doc-input');
                const levelSelected = document.querySelector('input[name="activity_level_id"]:checked');

                const allDocsComplete = Array.from(inputs).every(input => {
                    if (input.type === 'file') {
                        return input.files.length > 0;
                    } else {
                        return input.value.trim() !== '';
                    }
                });

                submitButton.disabled = !(allDocsComplete && levelSelected);
            }

            // Handle step navigation
            document.querySelectorAll('.next-step').forEach(button => {
                button.addEventListener('click', function () {
                    const currentStep = parseInt(this.dataset.step);
                    document.getElementById(`step${currentStep}`).style.display = 'none';
                    document.getElementById(`step${currentStep + 1}`).style.display = 'block';
                });
            });

            document.querySelectorAll('.prev-step').forEach(button => {
                button.addEventListener('click', function () {
                    const currentStep = parseInt(this.dataset.step);
                    document.getElementById(`step${currentStep}`).style.display = 'none';
                    document.getElementById(`step${currentStep - 1}`).style.display = 'block';
                });
            });
        });
    </script>
    <script>
        // Image preview
        document.querySelector('input[type="file"]').addEventListener('change', function (e) {
            const preview = document.getElementById('preview');
            preview.innerHTML = '';

            for (const file of this.files) {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.style.maxHeight = '100px';
                        img.style.marginRight = '10px';
                        preview.appendChild(img);
                    }
                    reader.readAsDataURL(file);
                }
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Parse data from PHP
        const chartData = <?php echo json_encode(array_reverse($last_entries)); ?>;

        const labels = chartData.map(entry => entry.name);
        const dataPoints = chartData.map(entry => entry.points);
        const colors = chartData.map(entry => entry.points > 0 ? '#71dd37' : 'red');

        // Configure Chart.js
        const ctx = document.getElementById('activityChart').getContext('2d');

        // Gradient background untuk line
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(65, 102, 255, 0.3)');
        gradient.addColorStop(1, 'rgba(65, 102, 255, 0)');

        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Recent Points',
                    data: dataPoints,
                    borderColor: '#4166ff',
                    backgroundColor: gradient,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#4166ff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointHoverBackgroundColor: '#4166ff',
                    pointHoverBorderColor: '#ffffff',
                    pointHoverBorderWidth: 2,
                    fill: true,
                    tension: 0.35,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    tooltip: {
                        backgroundColor: 'rgba(255, 255, 255, 0.95)',
                        titleColor: '#1a1a1a',
                        bodyColor: '#1a1a1a',
                        bodyFont: {
                            family: "'Inter', sans-serif",
                            size: 12
                        },
                        padding: 12,
                        cornerRadius: 8,
                        displayColors: false,
                        borderColor: 'rgba(0, 0, 0, 0.1)',
                        borderWidth: 1,
                        callbacks: {
                            label: function (tooltipItem) {
                                const pointValue = tooltipItem.raw;
                                if (pointValue > 0) {
                                    return `✨ Kegiatan: +${pointValue} points`;
                                }
                                return `⚠️ Pelanggaran: ${pointValue} points`;
                            }
                        }
                    },
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            display: false,
                            color: '#6b7280',
                            font: {
                                size: 11,
                                family: "'Inter', sans-serif"
                            },
                            maxRotation: 45,
                            minRotation: 45,
                            padding: 10
                        },
                        border: {
                            display: false
                        }
                    },
                    y: {
                        grid: {
                            display: true,
                            color: 'rgba(0, 0, 0, 0.05)',
                            drawBorder: false
                        },
                        ticks: {
                            display: true,
                            color: '#9ca3af',
                            font: {
                                size: 11,
                                family: "'Inter', sans-serif"
                            },
                            padding: 10,
                            callback: function (value) {
                                return value > 0 ? `+${value}` : value;
                            }
                        },
                        border: {
                            display: false
                        }
                    }
                },
                elements: {
                    line: {
                        borderWidth: 2.5
                    }
                },
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                hover: {
                    mode: 'nearest',
                    intersect: true
                },
            }
        });
    </script>




    <!-- Core JS -->
    <!-- build:js ../assets/vendor/js/core.js -->
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


    <!-- Place this tag in your head or just before your close body tag. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>

    <!-- Bootstrap JS (Letakkan sebelum </body>) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>