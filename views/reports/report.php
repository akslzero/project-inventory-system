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

$start_date = $_POST['start_date'] ?? null;
$end_date = $_POST['end_date'] ?? null;
$tipe = $_POST['tipe'] ?? null;
$user_id = $_POST['user_id'] ?? null;
$supplier_id = $_POST['supplier_id'] ?? null;
$customer_id = $_POST['customer_id'] ?? null;


$transactions = $transaction->getReport($start_date, $end_date, $tipe, $user_id, $supplier_id, $customer_id);
$total = $transaction->getTotalByType($start_date, $end_date, $tipe);


$totalItem = $transaction->getTotalItemsByType($start_date, $end_date, $tipe);
$totalMasuk = $transaction->getTotalItemsByType($start_date, $end_date, 'masuk');
$totalKeluar = $transaction->getTotalItemsByType($start_date, $end_date, 'keluar');





$start_date = $_POST['start_date'] ?? '';
$end_date = $_POST['end_date'] ?? '';
$tipe = $_POST['tipe'] ?? '';
$user_id = $_POST['user_id'] ?? '';
$supplier_id = $_POST['supplier_id'] ?? '';
$customer_id = $_POST['customer_id'] ?? '';

$results = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $results = $transaction->getReport($start_date, $end_date, $tipe, $user_id, $supplier_id, $customer_id);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Report</title>


 
  <link href='https://cdn.boxicons.com/fonts/basic/boxicons.min.css' rel='stylesheet'>
  <link href='https://cdn.boxicons.com/fonts/brands/boxicons-brands.min.css' rel='stylesheet'>
  <link href='../style/style.css' rel='stylesheet'>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" >

</head>


<style>
    /* Container */
.container {
  
  padding: 30px;
  background-color: var(--body-color);
  transition: var(--tran-05);
}

.home {
    
    padding-top: 20px;
    background: var(--body-color);
    
}


/* Headings */
h2, h5 {
  color: var(--text-color);
  margin-bottom: 20px;
}

/* Alerts */
.alert {
  padding: 10px 15px;
  border-radius: 6px;
  margin-bottom: 20px;
}

.alert-warning {
  background-color: #fff3cd;
  color: #856404;
}

/* Form */
form label {
  color: var(--text-color);
  font-weight: 500;
}

form input,
form select {
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

/* Buttons */
.btn {
  padding: 8px 16px;
  border-radius: 6px;
  margin-right: 5px;
}

.btn-primary {
  background-color: var(--primary-color);
  color: white;
  border: none;
}

.btn-secondary {
  background-color: #6c757d;
  color: white;
  border: none;
}

.btn-success {
  background-color: #198754;
  color: white;
  border: none;
}

/* Table */
table.table {
  background-color: var(--sidebar-color);
  color: var(--text-color);
}

table.table-bordered {
  border-color: #dee2e6;
}

table thead.table-dark th {
  background-color: #343a40;
  color: white;
}

table thead.table-light th {
  background-color: #f8f9fa;
  color: #212529;
}

.table-sm td, .table-sm th {
  padding: 0.3rem;
}

body.dark td.kd {
    color: #fff !important;
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
    <h2>Laporan Transaksi</h2>
    <form method="POST" class="mb-4">
        <div class="row">
            <div class="col-md-2">
                <label>Tanggal Mulai</label>
                <input type="date" name="start_date" class="form-control" value="<?= htmlspecialchars($start_date) ?>">
            </div>
            <div class="col-md-2">
                <label>Tanggal Selesai</label>
                <input type="date" name="end_date" class="form-control" value="<?= htmlspecialchars($end_date) ?>">
            </div>
            <div class="col-md-2">
                <label>Tipe</label>
                <select name="tipe" class="form-control">
                    <option value="">-- Semua --</option>
                    <option value="penjualan" <?= $tipe == 'penjualan' ? 'selected' : '' ?>>Penjualan</option>
                    <option value="pembelian" <?= $tipe == 'pembelian' ? 'selected' : '' ?>>Pembelian</option>
                </select>
            </div>
            <div class="col-md-2">
                <label>User</label>
                <select name="user_id" class="form-control">
                    <option value="">-- Semua --</option>
                    <?php
                    $users = $user->readAll();
                    foreach ($users as $u) {
                        $selected = ($user_id == $u['id']) ? 'selected' : '';
                        echo "<option value=\"{$u['id']}\" $selected>" . htmlspecialchars($u['username']) . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-2">
                <label>Supplier</label>
                <select name="supplier_id" class="form-control">
                    <option value="">-- Semua --</option>
                    <?php
                    $suppliers = $supplier->readAll();
                    foreach ($suppliers as $s) {
                        $selected = ($supplier_id == $s['id']) ? 'selected' : '';
                        echo "<option value=\"{$s['id']}\" $selected>" . htmlspecialchars($s['nama_supplier']) . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-2">
                <label>Customer</label>
                <select name="customer_id" class="form-control">
                    <option value="">-- Semua --</option>
                    <?php
                    $customers = $customer->readAll();
                    foreach ($customers as $c) {
                        $selected = ($customer_id == $c['id']) ? 'selected' : '';
                        echo "<option value=\"{$c['id']}\" $selected>" . htmlspecialchars($c['nama_customer']) . "</option>";
                    }
                    ?>
                </select>
            </div>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Filter</button>
        <a href="report.php" class="btn btn-secondary mt-3">Reset</a>
        <a href="export.php?start_date=<?= urlencode($start_date) ?>&end_date=<?= urlencode($end_date) ?>&tipe=<?= urlencode($tipe) ?>&user_id=<?= urlencode($user_id) ?>&supplier_id=<?= urlencode($supplier_id) ?>&customer_id=<?= urlencode($customer_id) ?>" class="btn btn-success mt-3">Export ke Excel</a>
        
    </form>
    

    <?php if ($results): ?>
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Kode Transaksi</th>
                    <th>Tipe</th>
                    <th>Tanggal</th>
                    <th>User</th>
                    <th>Supplier</th>
                    <th>Customer</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $r): ?>
                <tr>
                    <td><?= htmlspecialchars($r['kode_transaksi']) ?></td>
                    <td><?= htmlspecialchars($r['tipe']) ?></td>
                    <td><?= htmlspecialchars($r['tanggal']) ?></td>
                    <td><?= htmlspecialchars($r['username'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($r['nama_supplier'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($r['nama_customer'] ?? '-') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="mt-3">
    <h5>Total Transaksi: Rp <?= number_format($total, 0, ',', '.') ?></h5>
</div>
<?php foreach ($transactions as $t): ?>
    <tr>
        <td class="kd"><?= htmlspecialchars($t['kode_transaksi']) ?></td>
        <td><?= htmlspecialchars($t['tipe']) ?></td>
        <td><?= htmlspecialchars($t['tanggal']) ?></td>
        <td><?= htmlspecialchars($t['username']) ?></td>
        <td><?= $t['nama_supplier'] ?? '-' ?></td>
        <td><?= $t['nama_customer'] ?? '-' ?></td>
    </tr>

  
    <tr>
        <td colspan="6">
            <table class="table table-sm table-bordered mb-2">
                <thead class="table-light">
                    <tr>
                        <th>Produk</th>
                        <th>Jumlah</th>
                        <th>Harga Satuan</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        $details = $transaction->getDetailsByTransactionId($t['id']);
                        foreach ($details as $d): 
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($d['nama_produk']) ?></td>
                        <td><?= $d['jumlah'] ?></td>
                        <td>Rp <?= number_format($d['harga_satuan']) ?></td>
                        <td>Rp <?= number_format($d['jumlah'] * $d['harga_satuan']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </td>
    </tr>
<?php endforeach; ?>
<div class="mt-4">
    <h5>Total Item <?= $tipe ? ucfirst($tipe) : 'Transaksi' ?>: <?= $totalItem ?> pcs</h5>
</div>





    <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <div class="alert alert-warning">Tidak ada data transaksi sesuai filter.</div>
    <?php endif; ?>
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