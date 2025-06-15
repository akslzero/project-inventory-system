<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../public/login.php");
    exit;
}

if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'staff_laporan') {
    
    header("Location: ../../public/forbidden.php");
    exit;
}


require_once "../../../config/database.php";
require_once "../../../models/Transaction.php";

$database = new Database();
$db = $database->getConnection();

$transaction = new Transaction($db);


$start_date = $_GET['start_date'] ?? null;
$end_date = $_GET['end_date'] ?? null;
$tipe = $_GET['tipe'] ?? null;
$user_id = $_GET['user_id'] ?? null;
$supplier_id = $_GET['supplier_id'] ?? null;
$customer_id = $_GET['customer_id'] ?? null;


$data = $transaction->getReport($start_date, $end_date, $tipe, $user_id, $supplier_id, $customer_id);


$total = $transaction->getTotalByType($start_date, $end_date, $tipe);


header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=laporan_transaksi_" . date('Ymd_His') . ".xls");

echo "<table border='1'>";
echo "<tr><th colspan='6' style='font-size:16pt;'>Laporan Transaksi</th></tr>";
//echo "<tr><td colspan='6'>Periode: " . ($start_date ?? '-') . " s/d " . ($end_date ?? '-') . "</td></tr>";
//echo "<tr><td colspan='6'>Tipe: " . ($tipe ?? 'Semua') . "</td></tr>";
echo "<tr><td colspan='6'>Total Transaksi: Rp " . number_format($total, 0, ',', '.') . "</td></tr>";
echo "<tr></tr>"; 

echo "<tr>
        <th>Kode Transaksi</th>
        <th>Tipe</th>
        <th>Tanggal</th>
        <th>User</th>
        <th>Supplier</th>
        <th>Customer</th>
      </tr>";

foreach ($data as $row) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['kode_transaksi']) . "</td>";
    echo "<td>" . htmlspecialchars($row['tipe']) . "</td>";
    echo "<td>" . htmlspecialchars($row['tanggal']) . "</td>";
    echo "<td>" . htmlspecialchars($row['username'] ?? '-') . "</td>";
    echo "<td>" . htmlspecialchars($row['nama_supplier'] ?? '-') . "</td>";
    echo "<td>" . htmlspecialchars($row['nama_customer'] ?? '-') . "</td>";
    echo "</tr>";

    echo "<tr><td colspan='6'>";
    echo "<table border='1' style='margin-left:20px;'>";
    echo "<tr style='background-color:#f0f0f0;'>
            <th>Produk</th>
            <th>Jumlah</th>
            <th>Harga Satuan</th>
            <th>Subtotal</th>
          </tr>";

    
    $details = $transaction->getDetailsByTransactionId($row['id']);
    foreach ($details as $d) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($d['nama_produk']) . "</td>";
        echo "<td>" . $d['jumlah'] . "</td>";
        echo "<td>Rp " . number_format($d['harga_satuan'], 0, ',', '.') . "</td>";
        echo "<td>Rp " . number_format($d['jumlah'] * $d['harga_satuan'], 0, ',', '.') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "</td></tr>";
}

echo "</table>";
exit;
