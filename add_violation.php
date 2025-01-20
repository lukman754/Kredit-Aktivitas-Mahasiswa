<?php
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $violation_type_id = $_POST['violation_type_id'];
    $description = htmlspecialchars($_POST['description'], ENT_QUOTES, 'UTF-8');
    $user_id = $_SESSION['user_id'];
    $violation_date = date('Y-m-d');

    $uploadDir = 'uploads/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Handle violation photo
    $violationPhoto = $_FILES['violation_photo'];
    $violationPhotoName = uniqid() . '_' . $violationPhoto['name'];
    $violationPhotoPath = $uploadDir . $violationPhotoName;

    if (!move_uploaded_file($violationPhoto['tmp_name'], $violationPhotoPath)) {
        die('Failed to upload violation photo.');
    }

    // Handle statement photo
    $statement = $_FILES['statement'];
    $statementName = uniqid() . '_' . $statement['name'];
    $statementPath = $uploadDir . $statementName;

    if (!move_uploaded_file($statement['tmp_name'], $statementPath)) {
        die('Failed to upload statement photo.');
    }

    // Insert into database
    $stmt = $pdo->prepare("INSERT INTO user_violations (user_id, violation_type_id, violation_date, description, statement, violation_photo, status) 
                           VALUES (?, ?, ?, ?, ?, ?, 'pending')");
    $stmt->execute([
        $user_id,
        $violation_type_id,
        $violation_date,
        $description,
        $statementName,
        $violationPhotoName
    ]);

    header("Location: index.php?success=1");
    exit();
}
?>