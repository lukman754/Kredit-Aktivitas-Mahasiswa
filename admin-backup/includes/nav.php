<style>
    .avatar-initials {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        font-size: 14px;
        font-weight: bold;
        color: white;
        text-transform: uppercase;
    }

    .dropdown-menu {
        max-height: 400px;
        overflow-y: auto;
    }

    .badge.cutome-badge {
        font-size: 0.65rem;
        min-width: 1.2rem;
        padding: 0.2rem 0.4rem;
    }

    .dropdown-item:hover {
        background-color: #f5f5f5;
    }

    .dropdown-item:active {
        background-color: #e9ecef;
        color: inherit;
    }
</style>

<?php
// At the top of nav.php, make sure $pdo is available
// If you need to include the connection file:
// require_once __DIR__ . '/db_connect.php';

// Function to get notifications
function getNotifications($limit = 10)
{
    global $pdo;

    $sql = "
        (SELECT 
            'activity' as type,
            ua.submission_id as id,
            CONCAT(u.full_name, ' mengajukan aktivitas baru') as description,
            a.activity_name as additional_info,
            ua.created_at
        FROM user_activities ua
        JOIN users u ON ua.user_id = u.user_id
        JOIN activities a ON ua.activity_id = a.activity_id
        WHERE ua.status = 'pending')
        
        UNION
        
        (SELECT 
            'violation' as type,
            uv.violation_id as id,
            CONCAT(u.full_name, ' melaporkan pelanggaran') as description,
            vt.type_name as additional_info,
            uv.created_at
        FROM user_violations uv
        JOIN users u ON uv.user_id = u.user_id
        JOIN violation_types vt ON uv.violation_type_id = vt.violation_type_id
        WHERE uv.status = 'pending')
        
        ORDER BY created_at DESC
        LIMIT :limit";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get total notification count
function getNotificationCount()
{
    global $pdo;

    $sql = "
        SELECT 
            (SELECT COUNT(*) FROM user_activities WHERE status = 'pending') +
            (SELECT COUNT(*) FROM user_violations WHERE status = 'pending') as total";

    return $pdo->query($sql)->fetch(PDO::FETCH_ASSOC)['total'];
}

// Get notifications and count
$notifications = getNotifications();
$notificationCount = getNotificationCount();
?>
<nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
    id="layout-navbar">

    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
            <i class="bx bx-menu bx-sm"></i>
        </a>
    </div>

    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">


        <ul class="navbar-nav flex-row align-items-center ms-auto">


            <!-- Notifications -->
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle hide-arrow me-3" href="javascript:void(0);"
                    data-bs-toggle="dropdown">
                    <i class="bx bx-bell bx-sm position-relative">
                        <?php if ($notificationCount > 0): ?>
                            <span
                                class="badge bg-danger rounded-pill position-absolute top-0 start-100 p-1 cutome-badge translate-middle">
                                <?php echo $notificationCount; ?>
                            </span>
                        <?php endif; ?>
                    </i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <h6 class="dropdown-header">Notifikasi (<?php echo $notificationCount; ?>)</h6>
                    </li>
                    <li>
                        <div class="dropdown-divider"></div>
                    </li>

                    <?php if ($notificationCount > 0): ?>
                        <?php foreach ($notifications as $notif): ?>
                            <li>
                                <a class="dropdown-item" href="review-activity.php">
                                    <div class="d-flex align-items-center">
                                        <?php if ($notif['type'] === 'activity'): ?>
                                            <i class="bx bx-file-blank me-2 text-warning"></i>
                                        <?php else: ?>
                                            <i class="bx bx-error-circle me-2 text-danger"></i>
                                        <?php endif; ?>
                                        <div>
                                            <span class="d-block fw-semibold">
                                                <?php echo htmlspecialchars($notif['description']); ?>
                                            </span>
                                            <small class="text-muted">
                                                <?php echo htmlspecialchars($notif['additional_info']); ?>
                                                <span class="badge bg-warning text-dark ms-1">Butuh Persetujuan</span>
                                            </small>
                                            <br>
                                            <small class="text-muted">
                                                <?php echo date('d M Y H:i', strtotime($notif['created_at'])); ?>
                                            </small>
                                        </div>
                                    </div>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li>
                            <a class="dropdown-item text-center" href="#">
                                <span class="text-muted">Tidak ada notifikasi</span>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </li>

            <!-- User -->
            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <?php
                    $fullName = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : 'Anonymous';
                    $initials = '';
                    $words = explode(' ', $fullName);
                    foreach ($words as $word) {
                        $initials .= strtoupper($word[0]); // Ambil huruf pertama dari setiap kata
                    }
                    $bgColor = sprintf('#%06X', mt_rand(0, 0xFFFFFF)); // Warna acak
                    ?>

                    <div class="avatar avatar-initials" style="background-color: <?php echo $bgColor; ?>;">
                        <span>
                            <?php echo htmlspecialchars($initials); ?>
                        </span>
                    </div>


                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">

                                    <div class="mt-1 avatar avatar-initials" style="background-color:
                            <?php echo $bgColor; ?>;">
                                        <span>
                                            <?php echo htmlspecialchars($initials); ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <span class="fw-semibold d-block">
                                        <?php echo htmlspecialchars($_SESSION['full_name']); ?>
                                    </span>
                                    <small class="text-muted">
                                        <?php echo htmlspecialchars($_SESSION['nidn']); ?>
                                    </small>
                                </div>
                            </div>


                        </a>
                    </li>
                    <li>
                        <div class="dropdown-divider"></div>
                    </li>
                    <li>
                        <a class="dropdown-item" href="includes/logout.php">
                            <i class="bx bx-power-off me-2"></i>
                            <span class="align-middle">Log Out</span>
                        </a>
                    </li>
                </ul>
            </li>
            <!--/ User -->
        </ul>
    </div>
</nav>