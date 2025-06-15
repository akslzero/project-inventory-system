<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../public/login.php");
    exit;
}

if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'staff_produk') {
    
    header("Location: ../../public/forbidden.php");
    exit;
}


require_once "../../config/database.php";
require_once "../../models/Product.php";

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID produk tidak ditemukan.");
}

$database = new Database();
$db = $database->getConnection();

$product = new Product($db);
$product->id = $_GET['id'];

if ($product->delete()) {
    header("Location: list.php?msg=deleted");
    exit;
} else {
    echo "Gagal menghapus produk.";
}
