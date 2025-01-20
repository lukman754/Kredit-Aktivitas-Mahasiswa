<?php
// get_activity_docs.php
require_once 'config/database.php';

if (isset($_GET['activity_id'])) {
    $stmt = $pdo->prepare("SELECT dt.* 
                          FROM activity_documents ad
                          JOIN document_types dt ON ad.doc_type_id = dt.doc_type_id
                          WHERE ad.activity_id = ?");
    $stmt->execute([$_GET['activity_id']]);
    $docs = $stmt->fetchAll();
    echo json_encode($docs);
    exit();
}