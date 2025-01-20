<!-- verify_delete_activity.php -->
<?php
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$activity_id = $data['activity_id'];
$password = $data['password'];

// Verify password
$stmt = $pdo->prepare("SELECT password FROM users WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if ($user && password_verify($password, $user['password'])) {
    // Delete the activity
    $stmt = $pdo->prepare("DELETE FROM user_activities WHERE submission_id = ? AND user_id = ?");
    $stmt->execute([$activity_id, $_SESSION['user_id']]);

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid password']);
}
