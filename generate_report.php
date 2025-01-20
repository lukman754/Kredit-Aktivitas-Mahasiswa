<?php
require_once 'config/database.php';
require_once('tcpdf/tcpdf.php');

// Check if user is logged in and user_id is provided
if (!isset($_SESSION['user_id']) || !isset($_GET['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$_GET['user_id']]);
$user = $stmt->fetch();

// Fetch activities
$stmt = $pdo->prepare("
    SELECT ua.*, a.activity_name, al.level_name, al.points, ua.submission_date, ua.status
    FROM user_activities ua 
    JOIN activities a ON ua.activity_id = a.activity_id 
    JOIN activity_levels al ON ua.activity_level_id = al.level_id 
    WHERE ua.user_id = ? AND ua.status = 'approved'
    ORDER BY ua.submission_date DESC
");
$stmt->execute([$_GET['user_id']]);
$activities = $stmt->fetchAll();

// Fetch violations
$stmt = $pdo->prepare("
    SELECT uv.*, vt.type_name, vt.points, uv.description AS violation_description
    FROM user_violations uv 
    JOIN violation_types vt ON uv.violation_type_id = vt.violation_type_id 
    WHERE uv.user_id = ? AND uv.status = 'approved'
    ORDER BY uv.violation_date DESC
");
$stmt->execute([$_GET['user_id']]);
$violations = $stmt->fetchAll();

// Calculate total points
$activity_points = array_sum(array_column($activities, 'points'));
$violation_points = array_sum(array_column($violations, 'points'));
$total_points = $activity_points - $violation_points;

// Create new PDF document
class MYPDF extends TCPDF
{
    public function Header()
    {
        $this->Image('assets/img/unpam-logo.png', 15, 10, 25);
        $this->SetFont('helvetica', 'B', 16);
        $this->Cell(0, 10, 'UNIVERSITAS PAMULANG', 0, 1, 'C');
        $this->SetFont('helvetica', 'B', 12);
        $this->Cell(0, 10, 'LAPORAN KREDIT AKTIVITAS KEMAHASISWAAN', 0, 1, 'C');
        $this->Ln(5);
    }

    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0, 10, 'Halaman ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, 0, 'C');
    }
}

// Create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator('UNPAM KAK System');
$pdf->SetAuthor('UNPAM');
$pdf->SetTitle('KAK Report - ' . $user['full_name']);

// Set margins and add a page
$pdf->SetMargins(15, 40, 15);
$pdf->AddPage();

// Set font for content
$pdf->SetFont('helvetica', '', 12);

// Student Information
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 10, 'Informasi Mahasiswa', 0, 1, 'L');
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(40, 7, 'Nama', 0, 0);
$pdf->Cell(5, 7, ':', 0, 0);
$pdf->Cell(0, 7, $user['full_name'], 0, 1);
$pdf->Cell(40, 7, 'NIM', 0, 0);
$pdf->Cell(5, 7, ':', 0, 0);
$pdf->Cell(0, 7, $user['nim'], 0, 1);

// Points Summary
$pdf->Ln(5);
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 10, 'Ringkasan Poin', 0, 1, 'L');
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(40, 7, 'Total Poin', 0, 0);
$pdf->Cell(5, 7, ':', 0, 0);
$pdf->Cell(0, 7, $total_points, 0, 1);
$pdf->Cell(40, 7, 'Poin Kegiatan', 0, 0);
$pdf->Cell(5, 7, ':', 0, 0);
$pdf->Cell(0, 7, $activity_points, 0, 1);
$pdf->Cell(40, 7, 'Poin Pelanggaran', 0, 0);
$pdf->Cell(5, 7, ':', 0, 0);
$pdf->Cell(0, 7, $violation_points, 0, 1);

// Activities Table
$pdf->Ln(10);
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 10, 'Daftar Kegiatan', 0, 1, 'L');
$pdf->SetFont('helvetica', '', 10);

// Table header
$pdf->SetFillColor(230, 230, 230);
$pdf->Cell(80, 7, 'Nama Kegiatan', 1, 0, 'C', true);
$pdf->Cell(40, 7, 'Kategori', 1, 0, 'C', true);
$pdf->Cell(25, 7, 'Poin', 1, 0, 'C', true);
$pdf->Cell(35, 7, 'Tanggal', 1, 1, 'C', true);

// Table content
foreach ($activities as $activity) {
    $pdf->MultiCell(80, 6, $activity['activity_name'], 1, 'L', false, 0, '', '', true);
    $pdf->MultiCell(40, 6, $activity['level_name'], 1, 'L', false, 0, '', '', true);
    $pdf->Cell(25, 6, $activity['points'], 1, 0, 'C');
    $pdf->Cell(35, 6, date('d/m/Y', strtotime($activity['submission_date'])), 1, 1, 'C');
}

// Violations Table
$pdf->Ln(10);
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 10, 'Daftar Pelanggaran', 0, 1, 'L');
$pdf->SetFont('helvetica', '', 10);

// Table header
$pdf->SetFillColor(230, 230, 230);
$pdf->Cell(50, 7, 'Jenis Pelanggaran', 1, 0, 'C', true);
$pdf->Cell(85, 7, 'Deskripsi', 1, 0, 'C', true);
$pdf->Cell(20, 7, 'Poin', 1, 0, 'C', true);
$pdf->Cell(25, 7, 'Tanggal', 1, 1, 'C', true);

// Table content
foreach ($violations as $violation) {
    $pdf->MultiCell(50, 6, $violation['type_name'], 1, 'L', false, 0, '', '', true);
    $pdf->MultiCell(85, 6, $violation['violation_description'], 1, 'L', false, 0, '', '', true);
    $pdf->Cell(20, 6, $violation['points'], 1, 0, 'C');
    $pdf->Cell(25, 6, date('d/m/Y', strtotime($violation['violation_date'])), 1, 1, 'C');
}

// Output the PDF
$pdf->Output('KAK_Report_' . $user['nim'] . '.pdf', 'I');
?>