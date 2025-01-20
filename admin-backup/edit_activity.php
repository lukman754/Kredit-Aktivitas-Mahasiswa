<?php
// edit_activity.php
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['action'] == 'edit') {
    try {
        $pdo->beginTransaction();

        // Update activity basic info
        $stmt = $pdo->prepare("
            UPDATE activities 
            SET activity_name = ?, category_id = ?
            WHERE activity_id = ?
        ");
        $stmt->execute([
            $_POST['activity_name'],
            $_POST['category_id'],
            $_POST['activity_id']
        ]);

        // Delete existing levels
        $stmt = $pdo->prepare("DELETE FROM activity_levels WHERE activity_id = ?");
        $stmt->execute([$_POST['activity_id']]);

        // Insert new levels
        if (isset($_POST['level_names']) && isset($_POST['points'])) {
            $stmt = $pdo->prepare("
                INSERT INTO activity_levels (activity_id, level_name, points, period_id, system_id)
                VALUES (?, ?, ?, ?, ?)
            ");

            foreach ($_POST['level_names'] as $index => $level_name) {
                if (!empty($level_name) && isset($_POST['points'][$index])) {
                    $stmt->execute([
                        $_POST['activity_id'],
                        $level_name,
                        $_POST['points'][$index], // Changed from level_points to points to match form
                        $_POST['period_id'],
                        $_POST['system_id']
                    ]);
                }
            }
        }

        // Update document types
        $stmt = $pdo->prepare("DELETE FROM activity_documents WHERE activity_id = ?");
        $stmt->execute([$_POST['activity_id']]);

        if (isset($_POST['doc_types']) && is_array($_POST['doc_types'])) {
            $stmt = $pdo->prepare("
                INSERT INTO activity_documents (activity_id, doc_type_id)
                VALUES (?, ?)
            ");
            foreach ($_POST['doc_types'] as $doc_type_id) {
                if (!empty($doc_type_id)) {
                    $stmt->execute([$_POST['activity_id'], $doc_type_id]);
                }
            }
        }

        $pdo->commit();
        header('Location: kelola-kegiatan.php?success=' . urlencode('Activity updated successfully'));
        exit();

    } catch (PDOException $e) {
        $pdo->rollBack();
        header('Location: kelola-kegiatan.php?error=' . urlencode($e->getMessage()));
        exit();
    }
} else {
    header('Location: kelola-kegiatan.php?error=' . urlencode('Invalid request method'));
    exit();
}
?>