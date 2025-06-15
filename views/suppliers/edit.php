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

$role = $_SESSION['role'];
$nama = $_SESSION['username'];

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
$data = $supplier->readOne();

if (!$data) {
    header("Location: list.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $supplier->nama_supplier = $_POST['nama_supplier'];
    $supplier->alamat = $_POST['alamat'];
    $supplier->telp = $_POST['telp'];
    $supplier->email = $_POST['email'];

    if ($supplier->update()) {
        header("Location: list.php?success=Data berhasil diupdate");
        exit;
    } else {
        $error = "Gagal update data.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Edit Supplier</title>


 
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




h2 {
  color: var(--text-color);
  margin-bottom: 20px;
}


.alert {
  padding: 10px 15px;
  border-radius: 6px;
  margin-bottom: 20px;
}

.alert-danger {
  background-color: #f8d7da;
  color: #721c24;
}


form label {
  color: var(--text-color);
  font-weight: 500;
}

form input,
form textarea {
  background-color: var(--sidebar-color);
  color: var(--text-color);
  border: 1px solid #ced4da;
  border-radius: 6px;
  padding: 8px;
  width: 100%;
}

form .form-control:focus {
  border-color: var(--primary-color);
  box-shadow: 0 0 0 0.2rem rgba(13,110,253,.25);
}

.btn-primary {
  background-color: var(--primary-color);
  border: none;
  padding: 8px 16px;
  color: white;
  border-radius: 6px;
}

.btn-secondary {
  background-color: #6c757d;
  border: none;
  padding: 8px 16px;
  color: white;
  border-radius: 6px;
}

.btn-primary:hover {
  background-color: #0d6efd;
}

.btn-secondary:hover {
  background-color: #5a6268;
}

.home {
    
    padding-top: 20px;
    background: var(--body-color);
    
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
    <h2>Edit Supplier</h2>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form action="" method="POST">
        <div class="mb-3">
            <label>Nama Supplier</label>
            <input type="text" name="nama_supplier" class="form-control" value="<?= htmlspecialchars($data['nama_supplier']) ?>" required>
        </div>
        <div class="mb-3">
            <label>Alamat</label>
            <textarea name="alamat" class="form-control" rows="3" required><?= htmlspecialchars($data['alamat']) ?></textarea>
        </div>
        <div class="mb-3">
            <label>Telepon</label>
            <input type="text" name="telp" class="form-control" value="<?= htmlspecialchars($data['telp']) ?>" required>
        </div>
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($data['email']) ?>" required>
        </div>

        <button type="submit" class="btn btn-primary">Simpan</button>
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