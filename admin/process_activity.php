<?php
// process_activity.php
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'delete') {
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("DELETE FROM activity_levels WHERE activity_id = ?");
            $stmt->execute([$_POST['activity_id']]);

            $stmt = $pdo->prepare("DELETE FROM activity_documents WHERE activity_id = ?");
            $stmt->execute([$_POST['activity_id']]);

            $stmt = $pdo->prepare("DELETE FROM activities WHERE activity_id = ?");
            $stmt->execute([$_POST['activity_id']]);

            $pdo->commit();
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }

    // Add new activity
    try {
        $pdo->beginTransaction();

        // Insert activity with category
        $stmt = $pdo->prepare("INSERT INTO activities (activity_name, category_id) VALUES (?, ?)");
        $stmt->execute([$_POST['activity_name'], $_POST['category_id']]);
        $activity_id = $pdo->lastInsertId();

        // Insert levels
        $stmt = $pdo->prepare("INSERT INTO activity_levels (activity_id, level_name, points, period_id, system_id) VALUES (?, ?, ?, ?, ?)");

        foreach ($_POST['level_names'] as $key => $level_name) {
            $stmt->execute([
                $activity_id,
                $level_name,
                $_POST['points'][$key],
                $_POST['period_id'],
                $_POST['system_id']
            ]);
        }

        // Insert document types
        if (isset($_POST['doc_types'])) {
            $stmt = $pdo->prepare("INSERT INTO activity_documents (activity_id, doc_type_id) VALUES (?, ?)");
            foreach ($_POST['doc_types'] as $doc_type_id) {
                $stmt->execute([$activity_id, $doc_type_id]);
            }
        }

        $pdo->commit();
        header('Location: activities.php');
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "Error: " . $e->getMessage();
    }
}

?>