<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../public/login.php");
    exit;
}

if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'staff_transaksi') {
    
    header("Location: ../../public/forbidden.php");
    exit;
}

$role = $_SESSION['role'];
$nama = $_SESSION['username'];

require_once "../../config/database.php";
require_once "../../models/Transaction.php";
require_once "../../models/TransactionDetails.php";
require_once "../../models/Product.php";
require_once "../../models/User.php";
require_once "../../models/Supplier.php";
require_once "../../models/Customer.php";

$database = new Database();
$db = $database->getConnection();

$transaction = new Transaction($db);
$transactionDetail = new TransactionDetail($db);
$product = new Product($db);
$user = new User($db);
$supplier = new Supplier($db);
$customer = new Customer($db);

if (!isset($_GET['id'])) {
    header("Location: list.php");
    exit;
}

$transaction->id = $_GET['id'];
$data = $transaction->readOne();
if (!$data) {
    header("Location: list.php");
    exit;
}


$user->id = $data['user_id'];
$userData = $user->readOne();

$supplierName = '-';
if ($data['supplier_id']) {
    $supplier->id = $data['supplier_id'];
    $s = $supplier->readOne();
    if ($s) $supplierName = $s['nama_supplier'];
}

$customerName = '-';
if ($data['customer_id']) {
    $customer->id = $data['customer_id'];
    $c = $customer->readOne();
    if ($c) $customerName = $c['nama_customer'];
}


$details = $transactionDetail->readByTransactionId($transaction->id);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Detail Transaksi</title>


 
  <link href='https://cdn.boxicons.com/fonts/basic/boxicons.min.css' rel='stylesheet'>
  <link href='https://cdn.boxicons.com/fonts/brands/boxicons-brands.min.css' rel='stylesheet'>
  <link href='../style/style.css' rel='stylesheet'>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" >

</head>




<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h2 class="mb-4 text-center">Detail Transaksi</h2>
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <strong>Kode Transaksi: <?= htmlspecialchars($data['kode_transaksi']) ?></strong>
                </div>
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-5 col-md-4 fw-bold">Tipe</div>
                        <div class="col-7 col-md-8"><?= htmlspecialchars($data['tipe']) ?></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 col-md-4 fw-bold">Tanggal</div>
                        <div class="col-7 col-md-8"><?= htmlspecialchars($data['tanggal']) ?></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 col-md-4 fw-bold">User</div>
                        <div class="col-7 col-md-8"><?= $userData ? htmlspecialchars($userData['username']) : '-' ?></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 col-md-4 fw-bold">Supplier</div>
                        <div class="col-7 col-md-8"><?= htmlspecialchars($supplierName) ?></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 col-md-4 fw-bold">Customer</div>
                        <div class="col-7 col-md-8"><?= htmlspecialchars($customerName) ?></div>
                    </div>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header bg-success text-white">
                    <strong>Detail Produk</strong>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama Produk</th>
                                    <th class="text-center">Jumlah</th>
                                    <th class="text-end">Harga Satuan</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $total = 0;
                                foreach ($details as $d): 
                                    $subtotal = $d['jumlah'] * $d['harga_satuan'];
                                    $total += $subtotal;
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($d['nama_produk']) ?></td>
                                    <td class="text-center"><?= htmlspecialchars($d['jumlah']) ?></td>
                                    <td class="text-end"><?= number_format($d['harga_satuan'], 0, ',', '.') ?></td>
                                    <td class="text-end"><?= number_format($subtotal, 0, ',', '.') ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-end">Total</th>
                                    <th class="text-end"><?= number_format($total, 0, ',', '.') ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <div class="text-center">
                <a href="list.php" class="btn btn-secondary px-4">Kembali</a>
            </div>
        </div>
    </div>
</div>

</html>
