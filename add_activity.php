<?php
// Modified add_activity.php
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Di dalam add_activity.php, dalam blok POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $activity_id = $_POST['activity_id'];
    $activity_level_id = $_POST['activity_level_id'];

    // Sanitize input untuk deskripsi
    $activity_desc = htmlspecialchars($_POST['activity_desc'], ENT_QUOTES, 'UTF-8');  // Menambahkan sanitasi untuk activity_desc



    $stmt = $pdo->prepare("INSERT INTO user_activities (user_id, activity_id, activity_level_id, activity_desc, status) 
                      VALUES (?, ?, ?, ?, 'pending')");
    $stmt->execute([$_SESSION['user_id'], $activity_id, $activity_level_id, $activity_desc]);

    $submission_id = $pdo->lastInsertId();

    // Handle URL submissions
    if (isset($_POST['urls'])) {
        foreach ($_POST['urls'] as $doc_type_id => $url) {
            if (!empty($url)) {
                $stmt = $pdo->prepare("INSERT INTO document_uploads (submission_id, doc_type_id, file_name, file_path, file_type) 
                                     VALUES (?, ?, ?, ?, 'url')");
                $stmt->execute([
                    $submission_id,
                    $doc_type_id,
                    'URL Document',
                    $url,
                ]);
            }
        }
    }

    // Mendapatkan informasi file yang di-upload
    if (isset($_FILES['documents'])) {
        $uploadDir = 'uploads/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        foreach ($_FILES['documents']['tmp_name'] as $doc_type_id => $tmp_name) {
            if (is_uploaded_file($tmp_name)) {
                $fileName = uniqid() . '_' . $_FILES['documents']['name'][$doc_type_id];
                $filePath = $uploadDir . $fileName;

                // Validasi ekstensi file (hanya JPG, JPEG, PNG, PDF yang diperbolehkan)
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf'];
                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                if (!in_array($fileExtension, $allowedExtensions)) {
                    $error = 'Invalid file type. Only JPG, JPEG, PNG, and PDF are allowed.';
                    continue;  // Skip file if invalid extension
                }

                // Validasi MIME type
                $mimeType = mime_content_type($tmp_name);
                if (!in_array($mimeType, ['image/jpeg', 'image/png', 'application/pdf'])) {
                    $error = 'Invalid file MIME type.';
                    continue;
                }

                // Memindahkan file ke direktori yang diinginkan
                if (move_uploaded_file($tmp_name, $filePath)) {
                    $stmt = $pdo->prepare("INSERT INTO document_uploads (submission_id, doc_type_id, file_name, file_path, file_size, file_type) 
                                     VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->execute([
                        $submission_id,
                        $doc_type_id,
                        $_FILES['documents']['name'][$doc_type_id],
                        $filePath,
                        $_FILES['documents']['size'][$doc_type_id],
                        $_FILES['documents']['type'][$doc_type_id]
                    ]);
                }
            }
        }
    }


    header("Location: index.php?success=1");
    exit();
}
?>