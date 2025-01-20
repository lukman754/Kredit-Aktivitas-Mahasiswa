<?php
// get_activity.php
require_once '../config/database.php';
if (isset($_GET['id'])) {
    $activity_id = $_GET['id'];

    try {
        // Get activity basic info
        $stmt = $pdo->prepare("
            SELECT * FROM activities 
            WHERE activity_id = ?
        ");
        $stmt->execute([$activity_id]);
        $activity = $stmt->fetch(PDO::FETCH_ASSOC);

        // Get activity levels
        $stmt = $pdo->prepare("
            SELECT level_id, level_name, points 
            FROM activity_levels 
            WHERE activity_id = ?
            ORDER BY level_id
        ");
        $stmt->execute([$activity_id]);
        $activity['levels'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get document types
        $stmt = $pdo->prepare("
            SELECT doc_type_id 
            FROM activity_documents 
            WHERE activity_id = ?
        ");
        $stmt->execute([$activity_id]);
        $activity['doc_types'] = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'doc_type_id');

        header('Content-Type: application/json');
        echo json_encode($activity);

    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
}
?>