<?php
require_once '../config/database.php';

header('Content-Type: application/json');

$fakultasId = $_GET['fakultas'] ?? '';
$systemId = $_GET['system'] ?? '';

$query = "
    SELECT 
        p.prodi_id,
        p.nama_prodi,
        f.nama_fakultas,
        al.*,
        COUNT(DISTINCT ua.submission_id) as total_aktivitas,
        COALESCE(SUM(al.points), 0) as total_points
    FROM prodi p
    LEFT JOIN fakultas f ON p.fakultas_id = f.fakultas_id
    LEFT JOIN users u ON u.prodi_id = p.prodi_id
    LEFT JOIN user_activities ua ON ua.user_id = u.user_id
    LEFT JOIN activity_levels al ON ua.activity_level_id = al.level_id
";

$params = [];
$where = [];

if ($fakultasId) {
    $where[] = "p.fakultas_id = ?";
    $params[] = $fakultasId;
}

if ($systemId) {
    $where[] = "al.system_id = ?";
    $params[] = $systemId;
}

if (!empty($where)) {
    $query .= " WHERE " . implode(" AND ", $where);
}

$query .= " GROUP BY p.prodi_id, p.nama_prodi, f.nama_fakultas";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

// If system_id is 3, fetch level counts
if ($systemId == '3') {
    foreach ($result as &$row) {
        $levelQuery = "
            SELECT 
                al.level_name,
                COUNT(DISTINCT ua.submission_id) as count
            FROM activity_levels al
            LEFT JOIN user_activities ua ON ua.activity_level_id = al.level_id
            LEFT JOIN users u ON ua.user_id = u.user_id
            WHERE al.system_id = 3 
            AND u.prodi_id = ?
            GROUP BY al.level_name
        ";

        $levelStmt = $pdo->prepare($levelQuery);
        $levelStmt->execute([$row['prodi_id']]);
        $levelCounts = $levelStmt->fetchAll(PDO::FETCH_KEY_PAIR);
        $row['level_counts'] = $levelCounts;
    }
}

echo json_encode($result);
?>