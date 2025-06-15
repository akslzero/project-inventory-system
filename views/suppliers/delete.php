<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../public/login.php");
    exit;
}

if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'staff_supplier') {
    
    header("Location: ../../public/forbidden.php");
    exit;
}


require_once "../../config/database.php";
require_once "../../models/Supplier.php";

$database = new Database();
$db = $database->getConnection();

$supplier = new Supplier($db);

if (!isset($_GET['id'])) {
    header("Location: list.php");
    exit;
}

$supplier->id = $_GET['id'];

if ($supplier->delete()) {
    header("Location: list.php?success=Supplier berhasil dihapus");
    exit;
} else {
    header("Location: list.php?error=Gagal menghapus supplier");
    exit;
}
