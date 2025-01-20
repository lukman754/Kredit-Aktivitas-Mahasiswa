<?php
require_once '../config/database.php';



if (isset($_POST['fakultas_id'])) {
    $fakultas_id = $_POST['fakultas_id'];

    $stmt = $pdo->prepare("SELECT * FROM prodi WHERE fakultas_id = ? ORDER BY nama_prodi");
    $stmt->execute([$fakultas_id]);

    echo "<option value=''>Semua Prodi</option>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<option value='{$row['prodi_id']}'>{$row['nama_prodi']}</option>";
    }
}
?>