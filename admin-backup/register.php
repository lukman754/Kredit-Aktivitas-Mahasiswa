<?php
session_start();
error_reporting(0);

require_once '../config/database.php';

// Redirect if already logged in
if (isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nidn = trim($_POST['nidn']);
    $full_name = trim($_POST['full_name']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validation
    if (empty($nidn) || empty($full_name) || empty($password)) {
        $error = "Semua field harus diisi";
    } elseif ($password !== $confirm_password) {
        $error = "Password tidak cocok";
    } else {
        // Check if NIDN already exists
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM admins WHERE nidn = ?");
        $stmt->execute([$nidn]);
        if ($stmt->fetchColumn() > 0) {
            $error = "NIDN sudah terdaftar";
        } else {
            // Insert new admin
            $stmt = $pdo->prepare("INSERT INTO admins (nidn, full_name, password) VALUES (?, ?, ?)");
            try {
                $stmt->execute([
                    $nidn,
                    $full_name,
                    password_hash($password, PASSWORD_DEFAULT)
                ]);
                $success = "Registrasi berhasil! Silakan login";

            } catch (PDOException $e) {
                $error = "Terjadi kesalahan saat mendaftar";
            }
        }
    }
}
?>



<!DOCTYPE html>

<!-- =========================================================
* Sneat - Bootstrap 5 HTML Admin Template - Pro | v1.0.0
==============================================================

* Product Page: https://themeselection.com/products/sneat-bootstrap-html-admin-template/
* Created by: ThemeSelection
* License: You must have a valid license purchased in order to legally use the theme for your project.
* Copyright ThemeSelection (https://themeselection.com)

=========================================================
 -->
<!-- beautify ignore:start -->
<html
  lang="en"
  class="light-style customizer-hide"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="../assets/"
  data-template="vertical-menu-template-free"
>
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"
    />

    <title>Register Admin</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="../assets/img/favicon/favicon.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet"
    />

    <!-- Icons. Uncomment required icon fonts -->
    <link rel="stylesheet" href="../assets/vendor/fonts/boxicons.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="../assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="../assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="../assets/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

    <!-- Page CSS -->
    <!-- Page -->
    <link rel="stylesheet" href="../assets/vendor/css/pages/page-auth.css" />
    <!-- Helpers -->
    <script src="../assets/vendor/js/helpers.js"></script>

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="../assets/js/config.js"></script>
  </head>

  <body>
    <!-- Content -->
<div class="container-xxl">
  <div class="authentication-wrapper authentication-basic container-p-y">
    <div class="authentication-inner">
      <!-- Register Card -->
      <div class="card">
        <div class="card-body">
          <!-- Logo -->
          <div class="app-brand justify-content-center">
            <a href="index.html" class="app-brand-link gap-2">
              <span class="app-brand-logo demo">
                <img src="../assets/img/icons/logo-unpam.png" width="35" alt="">
              </span>
              <span class="app-brand-text demo text-body fw-bolder">Admin</span>
            </a>
          </div>
          <!-- /Logo -->
<h4 class="mb-2">Halo, Admin! 👋</h4>
<p class="mb-4 text-muted small">Siap membantu dan membuat perbedaan? Mari kita buat sistem ini lebih baik!</p>


          <!-- Error Alert -->
            <!-- Tampilkan pesan sukses jika ada -->
            <?php if ($success): ?>
                        <div style="color: green; background-color: #d4edda; padding: 10px; border-radius: 5px;">
                            <?php echo $success; ?>
                        </div>
            <?php endif; ?>
            
            <!-- Tampilkan pesan error jika ada -->
            <?php if ($error): ?>
                        <div style="color: red; background-color: #f8d7da; padding: 10px; border-radius: 5px;">
                            <?php echo $error; ?>
                        </div>
            <?php endif; ?>
          <!-- Registration Form -->
          <form method="POST" onsubmit="return sanitizeForm();">
            <!-- nidn -->
            <div class="mb-3">
              <label for="nidn" class="form-label">NIDN</label>
              <input type="number" class="form-control" id="nidn" name="nidn" placeholder="Masukkan NIDN" autofocus
                required />
            </div>

            <!-- Nama Lengkap -->
            <div class="mb-3">
              <label for="full_name" class="form-label">Nama Lengkap</label>
              <input type="text" class="form-control" id="full_name" name="full_name"
                placeholder="Masukkan Nama Lengkap" required oninput="validateFullName(this)" />
              <small id="nameError" class="text-danger" style="display: none;">
                Nama tidak boleh mengandung angka atau karakter spesial!
              </small>
            </div>

            <!-- Password -->
            <div class="mb-3 form-password-toggle">
              <label class="form-label" for="password">Password</label>
              <div class="input-group input-group-merge">
                <input type="password" id="password" class="form-control" name="password"
                  placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                  aria-describedby="password" required />
                <span class="input-group-text cursor-pointer">
                  <i class="bx bx-hide"></i>
                </span>
              </div>
            </div>

             <div class="mb-3 form-password-toggle">
              <label class="form-label" for="confirm_password">Konfirmasi Password</label>
              <div class="input-group input-group-merge">
                <input type="password" id="confirm_password" class="form-control" name="confirm_password"
                  placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                  aria-describedby="confirm_password" required />
                <span class="input-group-text cursor-pointer">
                  <i class="bx bx-hide"></i>
                </span>
              </div>
            </div>


            <!-- Terms and Conditions -->
            <div class="mb-3">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="terms-conditions" name="terms" />
                <label class="form-check-label" for="terms-conditions">
                  I agree to
                  <a href="javascript:void(0);">privacy policy & terms</a>
                </label>
              </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary d-grid w-100">Sign up</button>
          </form>

          <!-- Login Redirect -->
          <p class="text-center mt-2">
            <span>Sudah punya akun?</span>
            <a href="login.php">
              <span>Login</span>
            </a>
          </p>
        </div>
      </div>
      <!-- /Register Card -->
    </div>
  </div>
</div>

    <!-- / Content -->

<script>
    function validateFullName(input) {
        const regex = /^[a-zA-Z\s]*$/; // Hanya huruf dan spasi
        const errorElement = document.getElementById('nameError');

        if (!regex.test(input.value)) {
            errorElement.style.display = 'block';
            input.setCustomValidity('Nama tidak valid');
        } else {
            errorElement.style.display = 'none';
            input.setCustomValidity('');
        }
    }
</script>


<script>
    // Sanitasi form input sebelum dikirim
    function sanitizeForm() {
        // Ambil semua input dari form
        const inputs = document.querySelectorAll('input');

        // Validasi input
        for (let input of inputs) {
            let value = input.value;

            // Menghapus karakter berbahaya seperti <, >, dan script
            value = value.replace(/[<>{}()]/g, "");

            // Update input dengan value yang telah disanitasi
            input.value = value;
        }
        return true; // Lanjutkan submit form
    }

    // Validasi Nama Lengkap
    function validateFullName(input) {
        const regex = /^[a-zA-Z\s]*$/; // Hanya huruf dan spasi
        const errorElement = document.getElementById('nameError');

        if (!regex.test(input.value)) {
            errorElement.style.display = 'block';
            input.setCustomValidity('Nama tidak valid');
        } else {
            errorElement.style.display = 'none';
            input.setCustomValidity('');
        }
    }
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

    <!-- Main JS -->
    <script src="../assets/js/main.js"></script>

    <!-- Page JS -->

    <!-- Place this tag in your head or just before your close body tag. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
  </body>
</html>