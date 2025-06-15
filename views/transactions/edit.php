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
require_once "../../models/User.php";
require_once "../../models/Supplier.php";
require_once "../../models/Customer.php";

$database = new Database();
$db = $database->getConnection();

$transaction = new Transaction($db);
$user = new User($db);
$supplier = new Supplier($db);
$customer = new Customer($db);

if (!isset($_GET['id'])) {
    header("Location: list.php");
    exit;
}

$id = $_GET['id'];
$transaction->id = $id;

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $transaction->tipe = $_POST['tipe'];
    $transaction->user_id = $_POST['user_id'];
    $transaction->supplier_id = !empty($_POST['supplier_id']) ? $_POST['supplier_id'] : null;
    $transaction->customer_id = !empty($_POST['customer_id']) ? $_POST['customer_id'] : null;
    $transaction->tanggal = $_POST['tanggal'];

    if ($transaction->update()) {
        header("Location: list.php?success=Data berhasil diupdate");
        exit;
    } else {
        $error = "Gagal update data transaksi.";
    }
} else {
    $data = $transaction->readOne();
    if (!$data) {
        header("Location: list.php");
        exit;
    }
}

$users = $user->readAll();
$suppliers = $supplier->readAll();
$customers = $customer->readAll();


?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Edit Transaction</title>


 
  <link href='https://cdn.boxicons.com/fonts/basic/boxicons.min.css' rel='stylesheet'>
  <link href='https://cdn.boxicons.com/fonts/brands/boxicons-brands.min.css' rel='stylesheet'>
  <link href='../style/style.css' rel='stylesheet'>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" >

</head>
<style>
   
.container {
  
  padding: 30px;
  background-color: var(--body-color);
  transition: var(--tran-05);
}

.home {
    
    padding-top: 20px;
    background: var(--body-color);
    
}



form {
  background-color: var(--sidebar-color);
  padding: 25px;
  border-radius: 8px;
  box-shadow: 0 0 10px rgba(0,0,0,0.1);
}


input.form-control,
select.form-control {
  border-radius: 6px;
  border: 1px solid #ccc;
  padding: 10px;
  font-size: 16px;
  margin-bottom: 15px;
  transition: border-color 0.3s ease;
}

input.form-control:focus,
select.form-control:focus {
  border-color: var(--primary-color);
  outline: none;
}


h2 {
  color: var(--text-color);
  margin-bottom: 20px;
}


.btn {
  border-radius: 6px;
  padding: 10px 20px;
  font-size: 15px;
  transition: background-color 0.3s ease, box-shadow 0.3s ease;
  margin-right: 10px;
}

.btn-primary {
  background-color: var(--primary-color);
  color: #fff;
  border: none;
}

.btn-primary:hover {
  background-color: #5848e5;
}

.btn-secondary {
  background-color: #6c757d;
  color: #fff;
  border: none;
}

.btn-secondary:hover {
  background-color: #5a6268;
}

.alert {
  padding: 15px;
  border-radius: 5px;
  margin-bottom: 20px;
  font-size: 14px;
}

.alert-danger {
  background-color: #f8d7da;
  color: #721c24;
}

label {
  color: var(--text-color);
  font-weight: 500;
  margin-bottom: 5px;
}

</style>

<body>
<nav class="sidebar close">
  <header>
    <div class="image-text">
      <span class="header-text">
        <span class="name">Halo, <?= htmlspecialchars($nama) ?></span>
      </span>
    </div>
    <i class='bx bx-chevron-right toggle'></i>
  </header>
  <div class="menu-bar">
    <div class="menu">
      <ul class="menu-links">
        <li class="nav-link">
          <a href="../../public/index.php">
            <i class='bx bx-home-alt icon'></i>
            <span class="text nav-text">Dashboard</span>
          </a>
        </li>
        <?php if ($role === 'admin' || $role === 'staff_produk'): ?>
        <li class="nav-link">
          <a href="../../views/products/list.php">
            <i class='bx bx-computer icon'></i>
            <span class="text nav-text">Manajemen Produk</span>
          </a>
        </li>
        <?php endif; ?>
        <?php if ($role === 'admin' || $role === 'staff_transaksi'): ?>
        <li class="nav-link">
          <a href="../../views/transactions/list.php">
            <i class='bx bx-wallet icon'></i>
            <span class="text nav-text">Transaksi</span>
          </a>
        </li>
        <?php endif; ?>
        <?php if ($role === 'admin' || $role === 'staff_pelanggan'): ?>
        <li class="nav-link">
          <a href="../../views/customers/list.php">
            <i class='bx bx-user icon'></i>
            <span class="text nav-text">Pelanggan</span>
          </a>
        </li>
        <?php endif; ?>
        <?php if ($role === 'admin' || $role === 'staff_supplier'): ?>
        <li class="nav-link">
          <a href="../../views/suppliers/list.php">
            <i class='bx bx-truck icon'></i>
            <span class="text nav-text">Supplier</span>
          </a>
        </li>
        <?php endif; ?>
        <?php if ($role === 'admin' || $role === 'staff_laporan'): ?>
        <li class="nav-link">
          <a href="../../views/reports/report.php">
            <i class='bx bx-book icon'></i>
            <span class="text nav-text">Laporan</span>
          </a>
        </li>
        <?php endif; ?>
        <?php if ($role === 'admin'): ?>
        <li class="nav-link">
          <a href="../../public/register.php">
            <i class='bx bx-user-plus icon'></i>
            <span class="text nav-text">Register</span>
          </a>
        </li>
        <?php endif; ?>
        <li class="nav-link">
          <a href="../../public/logout.php" class="text-danger">
            <i class='bx  bx-arrow-out-left-square-half icon'></i>
            <span class="text nav-text">Logout</span>
          </a>
        </li>
        
      </ul>
    </div>

    
    <div class="bottom-content">
        <li class="mode">
          <div class="moon-sun">
            <i class='bx  bx-moon icon moon'></i>
            <i class='bx  bx-sun icon sun'></i>
          </div>
          <span class="mode-text text">Dark Mode</span>

          <div class="toggle-switch">
            <span class="switch"></span>
          </div>

        </li>
      </div>



  </div>
</nav>




<section class="home">
<div class="container mt-4">
    <h2>Edit Transaksi</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="mb-3">
            <label for="tipe">Tipe</label>
            <select name="tipe" id="tipe" class="form-control" required>
                <option value="penjualan" <?= $data['tipe'] == 'penjualan' ? 'selected' : '' ?>>Penjualan</option>
                <option value="pembelian" <?= $data['tipe'] == 'pembelian' ? 'selected' : '' ?>>Pembelian</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="tanggal">Tanggal</label>
            <input type="datetime-local" name="tanggal" id="tanggal" class="form-control"
                value="<?= date('Y-m-d\TH:i', strtotime($data['tanggal'])) ?>" required>
        </div>


        <div class="mb-3">
    <label>User</label>
    <select name="user_id" class="form-control" required>
        <option value="">-- Pilih User --</option>
        <?php foreach ($users as $u): ?>
            <option value="<?= $u['id'] ?>" <?= ($data['user_id'] == $u['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($u['username']) ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>


        <div class="mb-3">
            <label for="supplier_id">Supplier (optional)</label>
            <select name="supplier_id" id="supplier_id" class="form-control">
                <option value="">-- Pilih Supplier --</option>
                <?php foreach ($suppliers as $s): ?>
                    <option value="<?= $s['id'] ?>" <?= $data['supplier_id'] == $s['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($s['nama_supplier']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="customer_id">Customer (optional)</label>
            <select name="customer_id" id="customer_id" class="form-control">
                <option value="">-- Pilih Customer --</option>
                <?php foreach ($customers as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= $data['customer_id'] == $c['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['nama_customer']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Update Transaksi</button>
        <a href="list.php" class="btn btn-secondary">Kembali</a>
    </form>
</div>
</section>

<script>
    const body = document.querySelector("body")
    sidebar = body.querySelector(".sidebar")
    toggle = body.querySelector(".toggle")
    searchBtn = body.querySelector(".search-box")
    modeSwitch = body.querySelector(".toggle-switch")
    modeText = body.querySelector(".mode-text")

    toggle.addEventListener("click", () => {
      sidebar.classList.toggle("close");
    });

    modeSwitch.addEventListener("click", () => {
      body.classList.toggle("dark");

      if (body.classList.contains("dark")) {
        modeText.innerText = "Light Mode"
      } else {
        modeText.innerText = "Dark Mode"
      }
    });
  </script>
</body>
</html>

