<?php
require_once '../config/database.php';

error_reporting(0);
session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Set GROUP_CONCAT_MAX_LEN di awal
$pdo->query('SET SESSION group_concat_max_len = 1000000');

$query = "
SELECT DISTINCT 
    a.activity_id,
    a.activity_name,
    c.category_name,
    GROUP_CONCAT(DISTINCT CONCAT(al.level_name, ' (', al.points, ')') ORDER BY al.level_id SEPARATOR '\n') AS level_points,
    GROUP_CONCAT(DISTINCT dt.doc_type_name ORDER BY dt.doc_type_name SEPARATOR '\n') AS doc_types,
    GROUP_CONCAT(DISTINCT ap.period_name ORDER BY ap.period_name SEPARATOR '\n') AS periods,
    GROUP_CONCAT(DISTINCT ass.system_code ORDER BY ass.system_code SEPARATOR '\n') AS systems
FROM activities a
LEFT JOIN categories c ON a.category_id = c.category_id
LEFT JOIN activity_levels al ON a.activity_id = al.activity_id
LEFT JOIN activity_documents ad ON a.activity_id = ad.activity_id
LEFT JOIN document_types dt ON ad.doc_type_id = dt.doc_type_id
LEFT JOIN assessment_periods ap ON al.period_id = ap.period_id
LEFT JOIN assessment_systems ass ON al.system_id = ass.system_id
GROUP BY a.activity_id, a.activity_name, c.category_name
ORDER BY c.category_id ASC, a.activity_name ASC
";

$stmt = $pdo->prepare($query);
$stmt->execute();
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
    <title>Activity Management</title>
    <style>
        .levels-co .points-column,
        {
        white-space: pre-line;
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
                        <div class="d-flex justify-content-between align-items-center mb-4">

                            <h2>Kelola Kegiatan</h2>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#addActivityModal">
                                Add New Activity
                            </button>
                        </div>

                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover table-striped">
                                        <thead class="table-dark text-nowrap">
                                            <tr>
                                                <th>No</th>
                                                <th>Activity Name</th>
                                                <th>Levels</th>
                                                <th>Points</th>
                                                <th>Document Types</th>
                                                <th>Assessment Periods</th>
                                                <th>Assessment Systems</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $no = 1;
                                            $previousCategory = null;

                                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                if ($row['category_name'] !== $previousCategory) {
                                                    $previousCategory = $row['category_name'];
                                                    ?>
                                                    <tr class="table-secondary">
                                                        <td colspan="8">
                                                            <strong><?= htmlspecialchars($row['category_name']) ?></strong>
                                                        </td>
                                                    </tr>
                                                    <?php
                                                }

                                                // Pisahkan level_name dan points
                                                $levelPoints = explode("\n", $row['level_points'] ?? '');
                                                $levels = [];
                                                $points = [];
                                                foreach ($levelPoints as $lp) {
                                                    if (preg_match('/^(.*) \((\d+)\)$/', $lp, $matches)) {
                                                        $levels[] = $matches[1]; // Nama Level
                                                        $points[] = $matches[2]; // Poin
                                                    }
                                                }
                                                ?>
                                                <tr>
                                                    <td><?= $no++ ?></td>
                                                    <td><?= htmlspecialchars($row['activity_name']) ?></td>
                                                    <td class="levels-column text-nowrap">
                                                        <?= nl2br(htmlspecialchars(implode("\n", $levels) ?? '-')) ?>
                                                    </td>
                                                    <td class="points-column text-nowrap">
                                                        <?= nl2br(htmlspecialchars(implode("\n", $points) ?? '-')) ?>
                                                    </td>
                                                    <td class="doc-types-column small">
                                                        <?= nl2br(htmlspecialchars($row['doc_types'] ?? '-')) ?>
                                                    </td>
                                                    <td><?= htmlspecialchars($row['periods'] ?? '-') ?></td>
                                                    <td><?= htmlspecialchars($row['systems'] ?? '-') ?></td>
                                                    <td class="text-nowrap">
                                                        <button class="btn btn-sm btn-primary me-1"
                                                            onclick="editActivity(<?= $row['activity_id'] ?>)">
                                                            <i class='bx bxs-message-square-edit'></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-danger"
                                                            onclick="deleteActivity(<?= $row['activity_id'] ?>)">
                                                            <i class='bx bxs-message-square-x'></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                            ?>
                                        </tbody>
                                    </table>


                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Add Activity Modal -->
                    <div class="modal fade" id="addActivityModal" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Add New Activity</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="addActivityForm" action="process_activity.php" method="POST">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Activity Name</label>
                                                    <textarea type="text" class="form-control" name="activity_name"
                                                        required></textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Category</label>
                                                    <select class="form-select" name="category_id" required>
                                                        <option value="">Select Category</option>
                                                        <?php
                                                        $categories = $pdo->query("SELECT * FROM categories ORDER BY category_name");
                                                        while ($category = $categories->fetch(PDO::FETCH_ASSOC)) {
                                                            echo "<option value='{$category['category_id']}'>{$category['category_name']}</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Activity Levels</label>
                                            <div id="levelContainer">
                                                <div class="row mb-2">
                                                    <div class="col-md-4">
                                                        <input type="text" class="form-control" name="level_names[]"
                                                            placeholder="Level Name" required>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <input type="number" class="form-control" name="points[]"
                                                            placeholder="Points" required>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <button type="button" class="btn btn-success"
                                                            onclick="addLevel()">Add
                                                            Level</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Document Types</label>
                                            <div class="row">
                                                <?php
                                                $doc_types = $pdo->query("SELECT * FROM document_types");
                                                while ($dt = $doc_types->fetch(PDO::FETCH_ASSOC)) {
                                                    echo "<div class='col-md-6 col-xl-4'>";
                                                    echo "<div class='form-check'>";
                                                    echo "<input class='form-check-input' type='checkbox' name='doc_types[]' value='{$dt['doc_type_id']}'>";
                                                    echo "<label class='form-check-label'>{$dt['doc_type_name']}</label>";
                                                    echo "</div>";
                                                    echo "</div>";
                                                }
                                                ?>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Assessment Period</label>
                                                    <select class="form-select" name="period_id" required>
                                                        <option value="">Select Period</option>
                                                        <?php
                                                        $periods = $pdo->query("SELECT * FROM assessment_periods");
                                                        while ($period = $periods->fetch(PDO::FETCH_ASSOC)) {
                                                            echo "<option value='{$period['period_id']}'>{$period['period_name']}</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Assessment System</label>
                                                    <select class="form-select" name="system_id" required>
                                                        <option value="">Select System</option>
                                                        <?php
                                                        $systems = $pdo->query("SELECT * FROM assessment_systems");
                                                        while ($system = $systems->fetch(PDO::FETCH_ASSOC)) {
                                                            echo "<option value='{$system['system_id']}'>{$system['system_code']}</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary">Save Activity</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Edit Activity Modal -->
                    <div class="modal fade" id="editActivityModal" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Activity</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="editActivityForm" action="edit_activity.php" method="POST">
                                        <input type="hidden" name="action" value="edit">
                                        <input type="hidden" name="activity_id" id="edit_activity_id">

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Activity Name</label>
                                                    <textarea type="text" class="form-control" name="activity_name"
                                                        id="edit_activity_name" required></textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Category</label>
                                                    <select class="form-select" name="category_id" id="edit_category_id"
                                                        required>
                                                        <option value="">Select Category</option>
                                                        <?php
                                                        $categories = $pdo->query("SELECT * FROM categories ORDER BY category_name");
                                                        while ($category = $categories->fetch(PDO::FETCH_ASSOC)) {
                                                            echo "<option value='{$category['category_id']}'>{$category['category_name']}</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Activity Levels</label>
                                            <div id="editLevelContainer">
                                                <!-- Level rows will be dynamically added here -->
                                            </div>
                                            <button type="button" class="btn btn-success mt-2"
                                                onclick="addEditLevel()">Add
                                                Level</button>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Document Types</label>
                                            <div class="row">
                                                <?php
                                                $doc_types = $pdo->query("SELECT * FROM document_types");
                                                while ($dt = $doc_types->fetch(PDO::FETCH_ASSOC)) {
                                                    echo "<div class='col-md-6 col-xl-4'>";
                                                    echo "<div class='form-check'>";
                                                    echo "<input class='form-check-input edit-doc-type' type='checkbox' name='doc_types[]' value='{$dt['doc_type_id']}'>";
                                                    echo "<label class='form-check-label'>{$dt['doc_type_name']}</label>";
                                                    echo "</div>";
                                                    echo "</div>";
                                                }
                                                ?>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Assessment Period</label>
                                                    <select class="form-select" name="period_id" id="edit_period_id"
                                                        required>
                                                        <option value="">Select Period</option>
                                                        <?php
                                                        $periods = $pdo->query("SELECT * FROM assessment_periods");
                                                        while ($period = $periods->fetch(PDO::FETCH_ASSOC)) {
                                                            echo "<option value='{$period['period_id']}'>{$period['period_name']}</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Assessment System</label>
                                                    <select class="form-select" name="system_id" id="edit_system_id"
                                                        required>
                                                        <option value="">Select System</option>
                                                        <?php
                                                        $systems = $pdo->query("SELECT * FROM assessment_systems");
                                                        while ($system = $systems->fetch(PDO::FETCH_ASSOC)) {
                                                            echo "<option value='{$system['system_id']}'>{$system['system_code']}</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary">Update Activity</button>
                                        </div>
                                    </form>
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



    <script>
        function addLevel() {
            const container = document.getElementById('levelContainer');
            const newRow = document.createElement('div');
            newRow.className = 'row mb-2';
            newRow.innerHTML = `
            <div class="col-md-4">
                <input type="text" class="form-control" name="level_names[]" placeholder="Level Name" required>
            </div>
            <div class="col-md-4">
                <input type="number" class="form-control" name="points[]" placeholder="Points" required>
            </div>
            <div class="col-md-4">
                <button type="button" class="btn btn-danger" onclick="this.parentElement.parentElement.remove()"><i class="fa-solid fa-delete-left"></i></button>
            </div>
        `;
            container.appendChild(newRow);
        }

        function deleteActivity(activityId) {
            if (confirm('Are you sure you want to delete this activity?')) {
                fetch('process_activity.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=delete&activity_id=${activityId}`
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Error deleting activity');
                        }
                    });
            }
        }

        function editActivity(activityId) {
            fetch(`get_activity.php?id=${activityId}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('editActivityName').value = data.activity_name;
                    new bootstrap.Modal(document.getElementById('editActivityModal')).show();
                });
        }
    </script>

    <script>
        function addEditLevel() {
            const container = document.getElementById('editLevelContainer');
            const newRow = document.createElement('div');
            newRow.className = 'row mb-2';
            newRow.innerHTML = `
        <div class="col-md-4">
            <input type="text" class="form-control" name="level_names[]" placeholder="Level Name" required>
        </div>
        <div class="col-md-4">
            <input type="number" class="form-control" name="points[]" placeholder="Points" required>
        </div>
        <div class="col-md-4">
            <button type="button" class="btn btn-danger" onclick="this.parentElement.parentElement.remove()"><i class="fa-solid fa-delete-left"></i></button>
        </div>
    `;
            container.appendChild(newRow);
        }

        function editActivity(activityId) {
            // Reset form
            document.getElementById('editLevelContainer').innerHTML = '';
            document.querySelectorAll('.edit-doc-type').forEach(checkbox => checkbox.checked = false);

            // Fetch activity data
            fetch(`get_activity.php?id=${activityId}`)
                .then(response => response.json())
                .then(data => {
                    // Set basic activity data
                    document.getElementById('edit_activity_id').value = data.activity_id;
                    document.getElementById('edit_activity_name').value = data.activity_name;
                    document.getElementById('edit_category_id').value = data.category_id;

                    // Add existing levels
                    const container = document.getElementById('editLevelContainer');
                    data.levels.forEach(level => {
                        const newRow = document.createElement('div');
                        newRow.className = 'row mb-2';
                        newRow.innerHTML = `
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="level_names[]" value="${level.level_name}" required>
                    </div>
                    <div class="col-md-4">
                        <input type="number" class="form-control" name="points[]" value="${level.points}" required>
                    </div>
                    <div class="col-md-4">
                        <button type="button" class="btn btn-danger" onclick="this.parentElement.parentElement.remove()"><i class="fa-solid fa-delete-left"></i></button>
                    </div>
                `;
                        container.appendChild(newRow);
                    });

                    // Set document types
                    data.doc_types.forEach(docTypeId => {
                        document.querySelector(`.edit-doc-type[value="${docTypeId}"]`).checked = true;
                    });

                    // Set period and system
                    document.getElementById('edit_period_id').value = data.period_id;
                    document.getElementById('edit_system_id').value = data.system_id;

                    // Show modal
                    new bootstrap.Modal(document.getElementById('editActivityModal')).show();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading activity data');
                });
        }

        function deleteActivity(activityId) {
            if (confirm('Are you sure you want to delete this activity?')) {
                fetch('process_activity.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=delete&activity_id=${activityId}`
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Error deleting activity');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error deleting activity');
                    });
            }
        }
    </script>

</body>

</html>