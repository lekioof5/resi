<?php

session_start();
include "koneksi.php";
require 'vendor/autoload.php'; // Pastikan path autoload composer benar

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

// Proteksi Akses
if (!isset($_SESSION['status_login']) || $_SESSION['status_login'] !== "login") {
    die("Akses ditolak.");
}

$bulan = mysqli_real_escape_string($koneksi, $_GET['bulan']);
$tahun = mysqli_real_escape_string($koneksi, $_GET['tahun']);

// Create New Spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// --- A. HEADER LAPORAN ---
$sheet->setCellValue('A1', 'LAPORAN VALIDASI LOGISTIK');
$sheet->mergeCells('A1:J1');
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
$sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

$sheet->setCellValue('A2', "Periode: $bulan / $tahun");
$sheet->mergeCells('A2:J2');
$sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// --- B. HEADER TABEL ---
$headers = ['No', 'No Resi', 'Ekspedisi', 'Vendor', 'Qty', 'URN', 'Status', 'Waktu Keputusan', 'Petugas', 'Keterangan'];
$columnIndex = 'A';
foreach ($headers as $h) {
    $sheet->setCellValue($columnIndex . '4', $h);
    $columnIndex++;
}

// Styling Header Tabel (Warna Hitam, Teks Putih)
$headerStyle = [
    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '333333']],
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
];
$sheet->getStyle('A4:J4')->applyFromArray($headerStyle);

// --- C. ISI DATA ---
$query = "SELECT *,
          CASE
            WHEN status = 2 THEN 'VALID / PROSES'
            WHEN status = 3 THEN 'PENDING'
            WHEN status = 4 THEN 'REJECT / RETURN'
            WHEN status = 5 THEN 'LINKED (SUSULAN)'
            ELSE 'UNKNOWN'
          END as status_text
          FROM scans
          WHERE is_validated = 1
          AND (
            (MONTH(waktu_proses) = '$bulan' AND YEAR(waktu_proses) = '$tahun') OR
            (MONTH(waktu_pending) = '$bulan' AND YEAR(waktu_pending) = '$tahun') OR
            (MONTH(waktu_reject) = '$bulan' AND YEAR(waktu_reject) = '$tahun')
          )
          ORDER BY id DESC";
$result = mysqli_query($koneksi, $query);

$rowNum = 5;
$no = 1;
while ($row = mysqli_fetch_assoc($result)) {
    // Tentukan waktu berdasarkan status
    $waktu_display = "-";
    if ($row['status'] == 2 || $row['status'] == 5) {
        $waktu_display = $row['waktu_proses'];
    } elseif ($row['status'] == 3) {
        $waktu_display = $row['waktu_pending'];
    } elseif ($row['status'] == 4) {
        $waktu_display = $row['waktu_reject'];
    }

    $sheet->setCellValue('A' . $rowNum, $no++);
    $sheet->setCellValueExplicit('B' . $rowNum, $row['nomor_resi'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
    $sheet->setCellValue('C' . $rowNum, $row['ekspedisi']);
    $sheet->setCellValue('D' . $rowNum, $row['nama_vendor']);
    $sheet->setCellValue('E' . $rowNum, $row['jumlah']);
    $sheet->setCellValueExplicit('F' . $rowNum, $row['nomor_urn'] ?: '-', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
    $sheet->setCellValue('G' . $rowNum, $row['status_text']); // Kolom Status Baru
    $sheet->setCellValue('H' . $rowNum, $waktu_display);      // Waktu sesuai kondisi
    $sheet->setCellValue('I' . $rowNum, $row['nama_pic']);
    $sheet->setCellValue('J' . $rowNum, $row['keterangan_reject'] ?: $row['nomor_invoice']);

    // --- Tambahan: Styling Warna Berdasarkan Status ---
    if ($row['status'] == 4) { // REJECT
        $sheet->getStyle('G' . $rowNum)->getFont()->getColor()->setRGB('FF0000'); // Merah
    } elseif ($row['status'] == 3) { // PENDING
        $sheet->getStyle('G' . $rowNum)->getFont()->getColor()->setRGB('FF9900'); // Oranye
    } elseif ($row['status'] == 2) { // VALID
        $sheet->getStyle('G' . $rowNum)->getFont()->getColor()->setRGB('008000'); // Hijau
    }

    $sheet->getStyle('A' . $rowNum . ':J' . $rowNum)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
    $rowNum++;
}

// Autosize kolom agar rapi
foreach (range('A', 'J') as $columnID) {
    $sheet->getColumnDimension($columnID)->setAutoSize(true);
}

// --- D. OUTPUT FILE ---
$filename = "Laporan_Logistik_" . $bulan . "_" . $tahun . ".xlsx";

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
