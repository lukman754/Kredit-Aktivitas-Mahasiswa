<?php
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$submission_id = $_GET['id'] ?? 0;



// Enhanced query to fetch all related information including admin details
$stmt = $pdo->prepare("
    SELECT 
        ua.*,
        a.activity_name,
        al.level_name,
        al.points,
        c.category_name,
        ap.period_name,
        asy.system_name,
        asy.system_code,
        adm.full_name as reviewer_name,
        adm.nidn as reviewer_nidn,
        ua.reviewed_at,
        ua.feedback
    FROM user_activities ua 
    JOIN activities a ON ua.activity_id = a.activity_id 
    JOIN activity_levels al ON ua.activity_level_id = al.level_id 
    JOIN categories c ON a.category_id = c.category_id
    JOIN assessment_periods ap ON al.period_id = ap.period_id
    JOIN assessment_systems asy ON al.system_id = asy.system_id
    LEFT JOIN admins adm ON ua.reviewed_by = adm.admin_id
    WHERE ua.submission_id = ? AND ua.user_id = ?");
$stmt->execute([$submission_id, $_SESSION['user_id']]);
$activity = $stmt->fetch();

if (!$activity) {
    header("Location: dashboard.php");
    exit();
}

// Fetch uploaded documents with document type names
$stmt = $pdo->prepare("
    SELECT du.*, dt.doc_type_name 
    FROM document_uploads du
    JOIN document_types dt ON du.doc_type_id = dt.doc_type_id
    WHERE du.submission_id = ?");
$stmt->execute([$submission_id]);
$documents = $stmt->fetchAll();


// Helper function to get status badge class
function getStatusBadgeClass($status)
{
    switch ($status) {
        case 'approved':
            return 'bg-label-success';
        case 'rejected':
            return 'bg-label-danger';
        default:
            return 'bg-label-warning';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="assets/img/icons/logo-unpam.png" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet" />

    <!-- Icons. Uncomment required icon fonts -->
    <link rel="stylesheet" href="assets/vendor/fonts/boxicons.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="assets/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

    <link rel="stylesheet" href="assets/vendor/libs/apex-charts/apex-charts.css" />

    <!-- Page CSS -->

    <!-- Helpers -->
    <script src="assets/vendor/js/helpers.js"></script>

    <script src="https://kit.fontawesome.com/f59e2d85df.js" crossorigin="anonymous"></script>

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="assets/js/config.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <title>Detail <?php echo htmlspecialchars(ucfirst(strtolower($activity['system_code']))); ?></title>
    <!-- Add Fancybox CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" />
    <style>
        .detail-card {
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }

        .detail-card:hover {
            transform: translateY(-5px);
        }

        .doc-card {
            border-radius: 10px;
            transition: all 0.3s;
        }

        .doc-card:hover {
            background-color: #f8f9fa;
            transform: scale(1.02);
        }

        .status-badge {
            font-size: 0.9rem;
            padding: 0.5em 1em;
            border-radius: 20px;
        }

        .info-icon {
            width: 40px;
            height: 40px;
            background-color: #e9ecef;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
        }

        .image-thumbnail {
            cursor: zoom-in;
            border-radius: 8px;
            max-height: 200px;
            width: auto;
            object-fit: cover;
        }

        .image-container {
            position: relative;
            overflow: hidden;
        }

        .zoom-indicator {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: rgba(0, 0, 0, 0.5);
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.8rem;
        }
    </style>
</head>

<body>
    <?php include 'nav.php'; ?>



    <div class="container-xxl mt-2 justify-content-center">
        <div class="mb-4">
            <nav aria-label="breadcrumb mb-0">
                <ol class="breadcrumb breadcrumb-style1">
                    <li class="breadcrumb-item">
                        <a href="index.php">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">Detail
                        <?php echo htmlspecialchars(ucfirst(strtolower($activity['system_code']))); ?>

                    </li>
                </ol>
            </nav>
            <div class="row g-2">
                <!-- Basic Information -->
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title mb-4">Basic Information</h5>
                            <div class="p-2">
                                <div class="row mb-3">
                                    <div class="col-2 info-icon">
                                        <i class='bx bxs-detail'></i>
                                    </div>
                                    <div class="col-10">
                                        <small class="text-muted d-block">Deskripsi Kegiatan</small>
                                        <small
                                            class="fw-semibold"><?php echo htmlspecialchars($activity['activity_desc']); ?></small>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-2 info-icon">
                                        <i class='bx bxs-pie-chart-alt-2'></i>
                                    </div>
                                    <div class="col-10">
                                        <small class="text-muted d-block">Jenis Kegiatan</small>
                                        <strong><?php echo htmlspecialchars($activity['activity_name']); ?></strong>
                                    </div>
                                </div>
                                <div class="row  mb-3">
                                    <div class="col-2 info-icon "><i class='bx bxs-bar-chart-alt-2'></i>
                                    </div>
                                    <div class="col-10">
                                        <small class="text-muted d-block">Kategori</small>
                                        <strong><?php echo htmlspecialchars($activity['level_name']); ?></strong>
                                    </div>
                                </div>

                                <div class="row  mb-3">
                                    <div class="col-2 info-icon bg-warning">
                                        <div class="btn btn-warning rounded-pill p-0">
                                            <i class='bx bxs-star text-white'></i>
                                        </div>
                                    </div>
                                    <div class="col-10">
                                        <small class="text-muted d-block">Points</small>
                                        <strong style="font-size: 1rem;"
                                            class="badge bg-label-success px-2 py-1 rounded-pill">+<?php echo $activity['points']; ?></strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Assessment Information -->
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-body align-items-center">
                            <h5 class="card-title mb-4">Assessment Information</h5>
                            <div class="p-2">
                                <div class="row mb-3">
                                    <div class="col-2 info-icon">
                                        <i class='bx bxs-calendar-event'></i>
                                    </div>
                                    <div class="col-10">
                                        <small class="text-muted d-block">Periode Penilaian</small>
                                        <strong><?php echo htmlspecialchars($activity['period_name']); ?></strong>
                                    </div>
                                </div>
                                <div class="row  mb-3">
                                    <div class="col-2 info-icon ">
                                        <i class='bx bxs-cog'></i>
                                    </div>
                                    <div class="col-10">
                                        <small class="text-muted d-block">Penilai & Sistem</small>
                                        <strong><?php echo htmlspecialchars($activity['system_name']); ?>
                                            <?php echo htmlspecialchars($activity['system_code']); ?></strong>
                                    </div>
                                </div>

                                <div class="row  mb-3">
                                    <div class="col-2 info-icon ">
                                        <i class='bx bxs-folder-open'></i>

                                    </div>
                                    <div class="col-10">
                                        <small class="text-muted d-block">Kategori</small>

                                        <strong><?php echo htmlspecialchars($activity['category_name']); ?></strong>
                                    </div>
                                </div>

                                <div class="row  mb-3">
                                    <div class="col-2 info-icon ">
                                        <i class='bx bxs-check-circle'></i>

                                    </div>
                                    <div class="col-10">
                                        <small class="text-muted d-block">Status</small>

                                        <span
                                            class="status-badge <?php echo getStatusBadgeClass($activity['status']); ?> py-1 px-3">
                                            <?php echo ucfirst($activity['status']); ?>
                                        </span>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <!-- Add this after your existing card sections, before the Documents Section -->
                <?php if ($activity['status'] === 'pending'): ?>
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="w-100">
                                        <button type="button" class="btn btn-danger w-100" data-bs-toggle="modal"
                                            data-bs-target="#deleteModal">
                                            <i class='bx bx-trash me-1'></i> Delete Submission
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php elseif ($activity['status'] === 'approved'): ?>
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-start">
                                    <div class="flex-shrink-0">
                                        <div class="avatar avatar-online">
                                            <!-- Ganti gambar dengan inisial reviewer -->
                                            <span class="d-flex justify-content-center align-items-center"
                                                style="background-color: #007bff; color: white; font-weight: bold;">
                                                <?php
                                                // Ambil inisial dari reviewer_name
                                                $nameParts = explode(' ', $activity['reviewer_name']);
                                                $initials = strtoupper($nameParts[0][0] . (isset($nameParts[1]) ? $nameParts[1][0] : ''));
                                                echo $initials;
                                                ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1"><?php echo htmlspecialchars($activity['reviewer_name']); ?></h6>
                                        <small class="text-muted">NIDN:
                                            <?php echo htmlspecialchars($activity['reviewer_nidn']); ?></small>
                                        <p class="mb-1 mt-2"><?php echo nl2br(htmlspecialchars($activity['feedback'])); ?>
                                        </p>
                                        <small class="text-muted">
                                            Reviewed on:
                                            <?php echo date('d M Y H:i', strtotime($activity['reviewed_at'])); ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php elseif ($activity['status'] === 'rejected'): ?>
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-start">
                                    <div class="flex-shrink-0">
                                        <div class="avatar avatar-online">
                                            <!-- Ganti gambar dengan inisial reviewer -->
                                            <span class="d-flex justify-content-center align-items-center"
                                                style="background-color: #007bff; color: white; font-weight: bold;">
                                                <?php
                                                // Ambil inisial dari reviewer_name
                                                $nameParts = explode(' ', $activity['reviewer_name']);
                                                $initials = strtoupper($nameParts[0][0] . (isset($nameParts[1]) ? $nameParts[1][0] : ''));
                                                echo $initials;
                                                ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1"><?php echo htmlspecialchars($activity['reviewer_name']); ?></h6>
                                        <small class="text-muted">NIDN:
                                            <?php echo htmlspecialchars($activity['reviewer_nidn']); ?></small>
                                        <p class="mb-1 mt-2"><?php echo nl2br(htmlspecialchars($activity['feedback'])); ?>
                                        </p>
                                        <small class="text-muted">
                                            Reviewed on:
                                            <?php echo date('d M Y H:i', strtotime($activity['reviewed_at'])); ?>
                                        </small>
                                    </div>
                                </div>
                                <div class="w-100 mt-2">
                                    <button type="button" class="btn btn-danger w-100" data-bs-toggle="modal"
                                        data-bs-target="#deleteModal">
                                        <i class='bx bx-trash me-1'></i> Delete Submission
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Delete Confirmation Modal -->
                <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Confirm Deletion</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                Are you sure you want to delete this submission? This action cannot be undone.
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <form action="delete_submission.php" method="POST" class="d-inline">
                                    <input type="hidden" name="submission_id" value="<?php echo $submission_id; ?>">
                                    <button type="submit" class="btn btn-danger">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal for Editing Description and Documents -->

                <!-- Add this to your existing JavaScript section -->
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const deleteModal = document.getElementById('deleteModal');
                        if (deleteModal) {
                            deleteModal.addEventListener('show.bs.modal', function (event) {
                                // Additional modal initialization if needed
                            });
                        }
                    });
                </script>

                <!-- Documents Section -->
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-4">Uploaded Documents</h5>
                            <div class="row g-3">
                                <?php foreach ($documents as $doc): ?>
                                    <div class="col-md-4">
                                        <div class="doc-card h-100 p-3 border">
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="bi bi-file-earmark-text me-2"></i>
                                                <small class="text-muted">
                                                    <?php echo htmlspecialchars($doc['doc_type_name']); ?>
                                                </small>
                                            </div>
                                            <?php if ($doc['file_type'] === 'url'): ?>
                                                <a href="<?php echo htmlspecialchars($doc['file_path']); ?>"
                                                    class="btn btn-sm btn-outline-primary w-100" target="_blank">
                                                    <i class='bx bx-link-alt'></i> Open URL
                                                </a>
                                            <?php elseif (strpos($doc['file_type'], 'image/') === 0): ?>
                                                <div class="image-container">
                                                    <a href="<?php echo htmlspecialchars($doc['file_path']); ?>"
                                                        data-fancybox="gallery"
                                                        data-caption="<?php echo htmlspecialchars($doc['file_name']); ?>">
                                                        <img src="<?php echo htmlspecialchars($doc['file_path']); ?>"
                                                            class="img-fluid rounded image-thumbnail"
                                                            alt="<?php echo htmlspecialchars($doc['file_name']); ?>">
                                                        <span class="zoom-indicator">
                                                            <i class='bx bx-zoom-in'></i> Click to zoom
                                                        </span>
                                                    </a>
                                                </div>
                                            <?php else: ?>
                                                <a href="<?php echo htmlspecialchars($doc['file_path']); ?>"
                                                    class="btn btn-sm btn-outline-secondary w-100" target="_blank">
                                                    <i class='bx bx-file'></i> View Document
                                                </a>

                                            <?php endif; ?>
                                            <div class="mt-2">
                                                <small class="text-muted">
                                                    <?php echo htmlspecialchars($doc['file_name']); ?>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="content-footer footer bg-footer-theme text-center">
        <div class="container-xxl justify-content-center py-2 ">
            <div class="d-flex justify-content-center mb-2">
                <a href="https://github.com/Lukman754" class="footer-link m-2" target="_blank">
                    <i class='bx bxl-github me-1'></i>
                </a>
                <a href="https://facebook.com/lukman-mauludin-754" class="footer-link m-2" target="_blank">
                    <i class='bx bxl-facebook-circle me-1'></i>
                </a>
                <a href="https://instagram.com/_.chopin" class="footer-link m-2" target="_blank">
                    <i class='bx bxl-instagram-alt me-1'></i>
                </a>
            </div>
            <div class="mb-2">
                ©
                <script>
                    document.write(new Date().getFullYear());
                </script>
                , made with ❤️ by
                <a href="https://themeselection.com" target="_blank" class="footer-link fw-bold">Lukman Muludin</a>
            </div>

        </div>
    </footer>
    <!-- / Footer -->



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Add Fancybox JS -->
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
    <script>
        // Initialize Fancybox
        Fancybox.bind("[data-fancybox]", {
            // Custom options
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