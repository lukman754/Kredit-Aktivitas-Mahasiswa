<?php
require_once 'config/database.php';

// Start session and check if the user is logged in
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submission_id = $_POST['submission_id'];

    // Check if the submission ID is valid
    if (!empty($submission_id)) {
        try {
            // Begin transaction to ensure both activity and media are deleted together
            $pdo->beginTransaction();

            // First, delete associated media (documents)
            $stmt = $pdo->prepare("SELECT * FROM document_uploads WHERE submission_id = ?");
            $stmt->execute([$submission_id]);
            $documents = $stmt->fetchAll();

            // Delete files from server
            foreach ($documents as $doc) {
                $file_path = $doc['file_path'];

                // Check if file exists on the server and delete it
                if (file_exists($file_path)) {
                    unlink($file_path); // Delete the file
                }
            }

            // Delete the records from the document_uploads table
            $stmt = $pdo->prepare("DELETE FROM document_uploads WHERE submission_id = ?");
            $stmt->execute([$submission_id]);

            // Then, delete the activity record from the user_activities table
            $stmt = $pdo->prepare("DELETE FROM user_activities WHERE submission_id = ? AND user_id = ?");
            $stmt->execute([$submission_id, $_SESSION['user_id']]);

            // Commit the transaction
            $pdo->commit();

            // Redirect to dashboard after deletion
            header("Location: index.php");
            exit();
        } catch (Exception $e) {
            // Rollback the transaction in case of an error
            $pdo->rollBack();
            echo "Error deleting activity and associated media: " . $e->getMessage();
        }
    } else {
        // If no submission ID is provided, redirect back
        header("Location: index.php");
        exit();
    }
}
?>