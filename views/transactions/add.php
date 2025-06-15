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
require_once "../../models/Supplier.php";
require_once "../../models/Customer.php";



$database = new Database();
$db = $database->getConnection();

$transaction = new Transaction($db);
$transactionDetail = new TransactionDetail($db);
$product = new Product($db);
$supplier = new Supplier($db);
$customer = new Customer($db);

$error = '';
$success = '';


$allSuppliers = $supplier->readAll();
$allCustomers = $customer->readAll();
$allProducts = $product->readAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $transaction->kode_transaksi = $_POST['kode_transaksi'];
    $transaction->tipe = $_POST['tipe'];
    $transaction->tanggal = $_POST['tanggal'];
    $transaction->user_id = $_SESSION['user_id'];
    $transaction->supplier_id = !empty($_POST['supplier_id']) ? $_POST['supplier_id'] : null;
    $transaction->customer_id = !empty($_POST['customer_id']) ? $_POST['customer_id'] : null;

    
    $db->beginTransaction();

    try {
        
        if (!$transaction->create()) {
            throw new Exception("Gagal membuat transaksi");
        }

        $transaction_id = $db->lastInsertId();
     
        $product_ids = $_POST['product_id'];
        $jumlahs = $_POST['jumlah'];
        $harga_satuans = $_POST['harga_satuan'];

        for ($i = 0; $i < count($product_ids); $i++) {
            $transactionDetail->transaction_id = $transaction_id;
            $transactionDetail->product_id = $product_ids[$i];
            $transactionDetail->jumlah = $jumlahs[$i];
            $transactionDetail->harga_satuan = $harga_satuans[$i];

            if (!$transactionDetail->create()) {
                throw new Exception("Gagal menyimpan detail transaksi");
            }

           
            if ($_POST['tipe'] === 'penjualan') {
            
                $stmt = $db->prepare("SELECT stok FROM products WHERE id = :id");
                $stmt->execute([':id' => $product_ids[$i]]);
                $stok = $stmt->fetchColumn();
                if ($stok < $jumlahs[$i]) {
                    throw new Exception("Stok produk tidak cukup untuk transaksi.");
                }
               
                $stmt = $db->prepare("UPDATE products SET stok = stok - :jumlah WHERE id = :id");
                $stmt->execute([
                    ':jumlah' => $jumlahs[$i],
                    ':id' => $product_ids[$i]
                ]);
            }
          
            if ($_POST['tipe'] === 'pembelian') {
                $stmt = $db->prepare("UPDATE products SET stok = stok + :jumlah WHERE id = :id");
                $stmt->execute([
                    ':jumlah' => $jumlahs[$i],
                    ':id' => $product_ids[$i]
                ]);
            }
        }

        $db->commit();
        $success = "Transaksi berhasil disimpan!";
    } catch (Exception $e) {
        $db->rollBack();
        $error = "Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Add Transaction</title>


 
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



form {
  background-color: var(--sidebar-color);
  padding: 25px;
  border-radius: 8px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}


input.form-control,
select.form-select {
  border-radius: 6px;
  border: 1px solid #ccc;
  padding: 10px;
  font-size: 16px;
  transition: border-color 0.3s ease;
  margin-bottom: 10px;
}

input.form-control:focus,
select.form-select:focus {
  border-color: var(--primary-color);
  outline: none;
}


h2, h5 {
  color: var(--text-color);
}


.btn {
  border-radius: 6px;
  padding: 10px 20px;
  font-size: 15px;
  transition: background-color 0.3s ease, box-shadow 0.3s ease;
}

.btn-primary {
  background-color: var(--primary-color);
  color: white;
  border: none;
}

.btn-primary:hover {
  background-color: #5848e5;
}

.btn-secondary {
  background-color: #6c757d;
  color: white;
  border: none;
}

.btn-secondary:hover {
  background-color: #5a6268;
}

.btn-danger {
  background-color: #dc3545;
  border: none;
  color: white;
}

.btn-danger:hover {
  background-color: #c82333;
}


.product-row {
  align-items: center;
}

.product-row .col-md-1 {
  display: flex;
  align-items: center;
  justify-content: center;
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

.alert-success {
  background-color: #d4edda;
  color: #155724;
}

label {
  color: var(--text-color);
  font-weight: 500;
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
    <h2>Tambah Transaksi Baru</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php elseif ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="mb-3">
            <label>Kode Transaksi</label>
            <input type="text" name="kode_transaksi" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Tipe</label>
            <select name="tipe" class="form-select" required onchange="toggleSupplierCustomer(this.value)">
                <option value="">-- Pilih Tipe --</option>
                <option value="pembelian">Pembelian</option>
                <option value="penjualan">Penjualan</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Tanggal</label>
            <input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required>
        </div>

        <div class="mb-3" id="supplierDiv" style="display:none;">
            <label>Supplier</label>
            <select name="supplier_id" class="form-select">
                <option value="">-- Pilih Supplier --</option>
                <?php foreach ($allSuppliers as $s): ?>
                    <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['nama_supplier']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3" id="customerDiv" style="display:none;">
            <label>Customer</label>
            <select name="customer_id" class="form-select">
                <option value="">-- Pilih Customer --</option>
                <?php foreach ($allCustomers as $c): ?>
                    <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nama_customer']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <hr>

        <h5>Detail Produk</h5>

        <div id="productDetails">
            <div class="row mb-3 product-row">
                <div class="col-md-5">
                    <select name="product_id[]" class="form-select" required>
                        <option value="">-- Pilih Produk --</option>
                        <?php foreach ($allProducts as $p): ?>
                            <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nama_produk']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="number" name="jumlah[]" class="form-control" placeholder="Jumlah" min="1" required>
                </div>
                <div class="col-md-3">
                    <input type="number" name="harga_satuan[]" class="form-control" placeholder="Harga Satuan" min="0" required>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger btn-sm" onclick="removeProductRow(this)">-</button>
                </div>
            </div>
        </div>

        <button type="button" class="btn btn-secondary mb-3" onclick="addProductRow()">+ Tambah Produk</button>

        <br>

        <button type="submit" class="btn btn-primary">Tambah Transaksi</button>
        <a href="list.php" class="btn btn-secondary">Kembali</a>
    </form>
</div>
</section>

<script>
function toggleSupplierCustomer(tipe) {
    document.getElementById('supplierDiv').style.display = tipe === 'pembelian' ? 'block' : 'none';
    document.getElementById('customerDiv').style.display = tipe === 'penjualan' ? 'block' : 'none';
}

function addProductRow() {
    const container = document.getElementById('productDetails');
    const row = document.createElement('div');
    row.classList.add('row', 'mb-3', 'product-row');

    row.innerHTML = `
        <div class="col-md-5">
            <select name="product_id[]" class="form-select" required>
                <option value="">-- Pilih Produk --</option>
                <?php foreach ($allProducts as $p): ?>
                    <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nama_produk']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <input type="number" name="jumlah[]" class="form-control" placeholder="Jumlah" min="1" required>
        </div>
        <div class="col-md-3">
            <input type="number" name="harga_satuan[]" class="form-control" placeholder="Harga Satuan" min="0" required>
        </div>
        <div class="col-md-1">
            <button type="button" class="btn btn-danger btn-sm" onclick="removeProductRow(this)">-</button>
        </div>`;
    container.appendChild(row);
}

function removeProductRow(btn) {
    btn.closest('.product-row').remove();
}



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

