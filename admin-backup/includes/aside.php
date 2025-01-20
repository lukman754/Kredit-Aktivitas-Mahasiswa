<?php


// Get total notification count
function getNotificationCountAside()
{
    global $pdo;

    $sql = "
        SELECT 
            (SELECT COUNT(*) FROM user_activities WHERE status = 'pending') +
            (SELECT COUNT(*) FROM user_violations WHERE status = 'pending') as total";

    return $pdo->query($sql)->fetch(PDO::FETCH_ASSOC)['total'];
}

$notificationCountAside = getNotificationCountAside();
$current_page = basename($_SERVER['SCRIPT_NAME']);
?>

<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="index.php" class="app-brand-link">
            <span class="app-brand-logo demo">
                <img src="../assets/img/icons/logo.png" alt="">
            </span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <!-- Dashboard -->
        <li class="menu-item <?= ($current_page == 'index.php') ? 'active' : '' ?>">
            <a href="index.php" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Analytics">Dashboard</div>
            </a>
        </li>



        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Data Master</span>
        </li>
        <!-- Data -->
        <li class="menu-item <?= ($current_page == 'data-mahasiswa.php') ? 'active' : '' ?>">
            <a href="data-mahasiswa.php" class="menu-link">
                <i class="fa-solid fa-graduation-cap menu-icon tf-icons"></i>
                <div data-i18n="Basic">Data Mahasiswa</div>
            </a>
        </li>
        <li class="menu-item <?= ($current_page == 'data-aktivitas.php') ? 'active' : '' ?>">
            <a href="data-aktivitas.php" class="menu-link">
                <i class="fa-solid fa-chart-simple menu-icon tf-icons"></i>
                <div data-i18n="Basic">Data Aktivitas</div>
            </a>
        </li>
        <li class="menu-item <?= ($current_page == 'data-pelanggaran.php') ? 'active' : '' ?>">
            <a href="data-pelanggaran.php" class="menu-link">
                <i class="fa-solid fa-triangle-exclamation  menu-icon tf-icons"></i>
                <div data-i18n="Basic">Data Pelanggaran</div>
            </a>
        </li>
        <li class="menu-item <?= ($current_page == 'review-activity.php') ? 'active' : '' ?>">
            <a href="review-activity.php" class="menu-link">
                <i class="fa-solid fa-square-check menu-icon tf-icons"></i>
                <div data-i18n="Basic">Review Aktivitas</div>

                <div class="badge bg-danger ms-2 rounded-circle"><?= $notificationCountAside ?></div>
            </a>
        </li>
        <!-- Components -->
        <li class="menu-header small text-uppercase"><span class="menu-header-text">Pengaturan</span></li>
        <!-- Cards -->
        <li class="menu-item <?= ($current_page == 'kelola-kegiatan.php') ? 'active' : '' ?>">
            <a href="kelola-kegiatan.php" class="menu-link">
                <i class="fa-solid fa-gear menu-icon tf-icons"></i>
                <div data-i18n="Basic">Kelola Aktivitas</div>
            </a>
        </li>
    </ul>
</aside>