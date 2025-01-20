<?php
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submission_id = $_POST['submission_id'];
    $activity_desc = $_POST['activity_desc'];
    $documents = $_POST['documents'];

    // Update activity description
    $stmt = $pdo->prepare("UPDATE user_activities SET activity_desc = ? WHERE submission_id = ?");
    $stmt->execute([$activity_desc, $submission_id]);

    // Update each document
    foreach ($documents as $upload_id => $file_path) {
        $stmt = $pdo->prepare("UPDATE document_uploads SET file_path = ? WHERE upload_id = ?");
        $stmt->execute([$file_path, $upload_id]);
    }

    // Redirect to the same page with success message
    header("Location: view_submission.php?id=$submission_id&status=success");
    exit();
}
?>