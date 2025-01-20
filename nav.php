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
</style>
<nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
    id="layout-navbar">


    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
        <!-- Search -->
        <div class="navbar-nav align-items-center">
            <div class="nav-item d-flex align-items-center">
                <img src="assets/img/icons/logo-unpam.png" width="40" alt="">
                <div class="title ms-2">
                    <div class="fw-bold">Universitas Pamulang</div>
                    <div class="text-muted small">Kredit Aktivitas Kemahasiswaan</div>
                </div>
            </div>
        </div>
        <!-- /Search -->

        <ul class="navbar-nav flex-row align-items-center ms-auto">
            <!-- Add this button wherever you want it to appear in index.php -->
            <a href="generate_report.php?user_id=<?php echo $_SESSION['user_id']; ?>"
                class="btn btn-primary btn-sm me-2 d-none d-md-block" target="_blank">
                <i class="bx bx-file"></i> Cetak
            </a>


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
                        <span><?php echo htmlspecialchars($initials); ?></span>
                    </div>


                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="#">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">

                                    <div class="mt-1 avatar avatar-initials"
                                        style="background-color: <?php echo $bgColor; ?>;">
                                        <span><?php echo htmlspecialchars($initials); ?></span>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <span
                                        class="fw-semibold d-block"><?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
                                    <small class="text-muted"><?php echo htmlspecialchars($_SESSION['nim']); ?></small>
                                </div>
                            </div>


                        </a>
                    </li>
                    <li>
                        <div class="dropdown-divider"></div>
                    </li>
                    <li>
                        <div class="m-2">
                            <a href="generate_report.php?user_id=<?php echo $_SESSION['user_id']; ?>"
                                class="btn btn-primary btn-sm me-2 w-100 d-block d-md-none" target="_blank">
                                <i class="bx bx-file"></i> Cetak
                            </a>
                        </div>
                    </li>
                    <li>
                        <a class="dropdown-item" href="logout.php">
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