<?php
require_once 'config/database.php';

if (isset($_GET['activity_id'])) {
    $activity_id = $_GET['activity_id'];

    // Make sure the activity_id is numeric and valid
    if (is_numeric($activity_id)) {
        $stmt = $pdo->prepare("SELECT * FROM activity_levels WHERE activity_id = ?");
        $stmt->execute([$activity_id]);
        $levels = $stmt->fetchAll();

        // Check if levels are found for the given activity_id
        if ($levels) {
            echo json_encode($levels);
        } else {
            echo json_encode([]);  // Return an empty array if no levels found
        }
    } else {
        echo json_encode([]);  // Return an empty array if the activity_id is invalid
    }
    exit();
}
?>