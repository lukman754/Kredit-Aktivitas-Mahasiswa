<?php
require_once 'config/database.php';

error_reporting(0);

session_start();
if (isset($_SESSION['user_id'])) {
  // Jika sudah login, langsung arahkan ke index.php
  header("Location: index.php");
  exit();
}

// Fungsi sanitasi untuk mencegah injeksi
function sanitizeInput($data)
{
  $data = trim($data); // Menghapus spasi ekstra
  $data = stripslashes($data); // Menghapus backslash
  $data = htmlspecialchars($data); // Mengubah karakter khusus menjadi entitas HTML
  return $data;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Ambil dan sanitasi input
  $nim = sanitizeInput($_POST['nim']);
  $full_name = sanitizeInput($_POST['full_name']);
  $password = sanitizeInput($_POST['password']);
  $prodi_id = sanitizeInput($_POST['prodi_id']);

  // Validasi input
  if (empty($nim) || empty($full_name) || empty($password) || empty($prodi_id)) {
    $error = "All fields are required!";
  } elseif (!is_numeric($nim)) {
    $error = "NIM must be a number!";
  } elseif (!preg_match("/^[a-zA-Z\s]+$/", $full_name)) {
    $error = "Full name should only contain letters and spaces!";
  } else {
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Cek apakah NIM sudah ada di database
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE nim = ?");
    $stmt->execute([$nim]);
    $existingNIM = $stmt->fetchColumn();

    if ($existingNIM > 0) {
      $error = "NIM already exists!";
    } else {
      // Insert data ke database
      $stmt = $pdo->prepare("INSERT INTO users (nim, full_name, password, prodi_id) VALUES (?, ?, ?, ?)");
      try {
        $stmt->execute([$nim, $full_name, $hashed_password, $prodi_id]);
        header("Location: login.php?success=1");
        exit();
      } catch (PDOException $e) {
        $error = "Error: " . $e->getMessage();
      }
    }
  }
}


// Ambil data fakultas dan prodi
$query = "
    SELECT f.fakultas_id, f.nama_fakultas, p.prodi_id, p.nama_prodi 
    FROM fakultas f
    JOIN prodi p ON f.fakultas_id = p.fakultas_id
    ORDER BY f.nama_fakultas, p.nama_prodi
";
$stmt = $pdo->query($query);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Kelompokkan prodi berdasarkan fakultas
$fakultasProdi = [];
foreach ($data as $row) {
  $fakultasProdi[$row['nama_fakultas']][] = [
    'prodi_id' => $row['prodi_id'],
    'nama_prodi' => $row['nama_prodi']
  ];
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
  data-assets-path="assets/"
  data-template="vertical-menu-template-free"
>
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"
    />

    <title>Register Basic - Pages | Sneat - Bootstrap 5 HTML Admin Template - Pro</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/img/favicon/favicon.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet"
    />

    <!-- Icons. Uncomment required icon fonts -->
    <link rel="stylesheet" href="assets/vendor/fonts/boxicons.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="assets/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

    <!-- Page CSS -->
    <!-- Page -->
    <link rel="stylesheet" href="assets/vendor/css/pages/page-auth.css" />
    <!-- Helpers -->
    <script src="assets/vendor/js/helpers.js"></script>

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="assets/js/config.js"></script>
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
                <img src="assets/img/icons/logo-unpam.png" width="35" alt="">
              </span>
              <span class="app-brand-text demo text-body fw-bolder">KAK - Unpam</span>
            </a>
          </div>
          <!-- /Logo -->

          <h4 class="mb-2">Halo, Mahasiswa Aktif! 👋</h4>
          <p class="mb-4 text-muted small">
            Teruslah berkarya, karena setiap langkah kecil Anda membawa perubahan besar!
          </p>

          <!-- Error Alert -->
          <?php if (isset($error)): ?>
                  <div class="alert alert-danger">
                    <?php echo $error; ?>
                  </div>
          <?php endif; ?>

          <!-- Registration Form -->
          <form method="POST" onsubmit="return sanitizeForm();">
            <!-- Program Studi -->
            <div class="mb-3">
              <label for="prodi_id" class="form-label">Program Studi</label>
              <select class="form-control" id="prodi_id" name="prodi_id" required>
                <option value="" disabled selected>Pilih Program Studi</option>
                <?php foreach ($fakultasProdi as $namaFakultas => $prodiList): ?>
                        <optgroup label="<?= htmlspecialchars($namaFakultas) ?>">
                          <?php foreach ($prodiList as $prodi): ?>
                                  <option value="<?= htmlspecialchars($prodi['prodi_id']) ?>">
                                    <?= htmlspecialchars($prodi['nama_prodi']) ?>
                                  </option>
                          <?php endforeach; ?>
                        </optgroup>
                <?php endforeach; ?>
              </select>
            </div>

            <!-- NIM -->
            <div class="mb-3">
              <label for="nim" class="form-label">NIM</label>
              <input type="number" class="form-control" id="nim" name="nim" placeholder="Masukkan NIM" autofocus
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
    <!-- build:js assets/vendor/js/core.js -->
    <script src="assets/vendor/libs/jquery/jquery.js"></script>
    <script src="assets/vendor/libs/popper/popper.js"></script>
    <script src="assets/vendor/js/bootstrap.js"></script>
    <script src="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>

    <script src="assets/vendor/js/menu.js"></script>
    <!-- endbuild -->

    <!-- Vendors JS -->

    <!-- Main JS -->
    <script src="assets/js/main.js"></script>

    <!-- Page JS -->

    <!-- Place this tag in your head or just before your close body tag. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
  </body>
</html>