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


$user = new User($db);
$supplier = new Supplier($db);
$customer = new Customer($db);


$transaction = new Transaction($db);
$transactions = $transaction->readAll();

?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Transaction</title>


 
  <link href='https://cdn.boxicons.com/fonts/basic/boxicons.min.css' rel='stylesheet'>
  <link href='https://cdn.boxicons.com/fonts/brands/boxicons-brands.min.css' rel='stylesheet'>
  <link href='../style/style.css' rel='stylesheet'>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" >

</head>
<style>
   
.container {
  
  padding: 20px;
  background-color: var(--body-color);
  transition: var(--tran-05);
}





form {
  background-color: var(--sidebar-color);
  border-radius: 8px;
  padding: 20px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}


input.form-control,
textarea.form-control,
select.form-control {
  border-radius: 6px;
  border: 1px solid #ccc;
  padding: 10px;
  font-size: 16px;
  margin-bottom: 15px;
  width: 100%;
  box-sizing: border-box;
  transition: border-color 0.3s ease;
}


input.form-control:focus,
textarea.form-control:focus,
select.form-control:focus {
  border-color: var(--primary-color);
  outline: none;
}


button.btn,
a.btn {
  border-radius: 6px;
  padding: 10px 20px;
  font-size: 16px;
  text-align: center;
  display: inline-block;
  cursor: pointer;
  transition: background-color 0.3s ease;
}


button.btn-primary,
a.btn-primary {
  background-color: var(--primary-color);
  color: white;
  border: none;
}

button.btn-primary:hover,
a.btn-primary:hover {
  background-color: darken(var(--primary-color), 10%);
}


button.btn-secondary,
a.btn-secondary {
  background-color: #6c757d;
  color: white;
  border: none;
}

button.btn-secondary:hover,
a.btn-secondary:hover {
  background-color: darken(#6c757d, 10%);
}


img {
  border-radius: 6px;
  margin-bottom: 10px;
}

h2 {
  color: var(--text-color);
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
    <h2>Daftar Transaksi</h2>

    <a href="add.php" class="btn btn-primary mb-3">+ Tambah Transaksi</a>

    <table class="table table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <th>Kode Transaksi</th>
                <th>Tipe</th>
                <th>Tanggal</th>
                <th>User</th>
                <th>Supplier</th>
                <th>Customer</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($transactions): ?>
                <?php foreach ($transactions as $t): ?>
                    <tr>
                        <td><?= htmlspecialchars($t['kode_transaksi']) ?></td>
                        <td><?= htmlspecialchars($t['tipe']) ?></td>
                        <td><?= htmlspecialchars($t['tanggal']) ?></td>
                        <td>
                            <?php 
                                $user->id = $t['user_id'];
                                $u = $user->readOne();
                                echo $u ? htmlspecialchars($u['username']) : '-';
                            ?>
                        </td>
                        <td>
                            <?php
                                if ($t['supplier_id']) {
                                    $supplier->id = $t['supplier_id'];
                                    $s = $supplier->readOne();
                                    echo $s ? htmlspecialchars($s['nama_supplier']) : '-';
                                } else {
                                    echo '-';
                                }
                            ?>
                        </td>
                        <td>
                            <?php
                                if ($t['customer_id']) {
                                    $customer->id = $t['customer_id'];
                                    $c = $customer->readOne();
                                    echo $c ? htmlspecialchars($c['nama_customer']) : '-';
                                } else {
                                    echo '-';
                                }
                            ?>
                        </td>
                        <td>
                            <a href="view.php?id=<?= $t['id'] ?>" class="btn btn-sm btn-info">Detail</a>
                            <a href="edit.php?id=<?= $t['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="delete.php?id=<?= $t['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin mau hapus transaksi ini?')">Hapus</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="7" class="text-center">Belum ada transaksi</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
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
