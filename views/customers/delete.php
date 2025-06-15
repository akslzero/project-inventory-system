<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../public/login.php");
    exit;
}

if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'staff_pelanggan') {
    
    header("Location: ../../public/forbidden.php");
    exit;
}

require_once "../../config/database.php";
require_once "../../models/Customer.php";

$database = new Database();
$db = $database->getConnection();

$customer = new Customer($db);

if (!isset($_GET['id'])) {
    header("Location: list.php");
    exit;
}

$customer->id = $_GET['id'];

if ($customer->delete()) {
    header("Location: list.php?success=Customer berhasil dihapus");
    exit;
} else {
    header("Location: list.php?error=Gagal menghapus customer");
    exit;
}
