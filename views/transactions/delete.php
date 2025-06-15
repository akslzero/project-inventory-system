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

require_once "../../config/database.php";
require_once "../../models/Transaction.php";

$database = new Database();
$db = $database->getConnection();

$transaction = new Transaction($db);

if (isset($_GET['id'])) {
    $transaction->id = $_GET['id'];
    if ($transaction->delete()) {
        header("Location: list.php?success=Transaksi berhasil dihapus");
    } else {
        header("Location: list.php?error=Gagal menghapus transaksi");
    }
} else {
    header("Location: list.php");
}
exit;
