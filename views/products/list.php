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

$role = $_SESSION['role'];
$nama = $_SESSION['username'];

require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../models/Product.php";
require_once __DIR__ . "/../../models/Category.php";

$database = new Database();
$db = $database->getConnection();

$product = new Product($db);
$category = new Category($db);


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_kategori'])) {
    $namaBaru = trim($_POST['nama_kategori']);
    if (!empty($namaBaru)) {
        $category->nama_kategori = $namaBaru;
        $category->create();


        header("Location: list.php");
        exit;
    }
}






$allCategories = $category->readAll();

$kategori_id = isset($_GET['kategori_id']) && $_GET['kategori_id'] !== '' ? $_GET['kategori_id'] : null;


$products = $product->readAll($kategori_id);

$kategori_id = isset($_GET['kategori_id']) && $_GET['kategori_id'] !== '' ? $_GET['kategori_id'] : null;
$search = isset($_GET['search']) ? $_GET['search'] : null;

$products = $product->readAll($kategori_id, $search);

?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Product</title>


 
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



h2, h4 {
  color: var(--text-color);
  margin-bottom: 20px;
}

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

.alert {
  padding: 12px;
  border-radius: 5px;
  margin-bottom: 15px;
}

.alert-danger {
  background-color: #f8d7da;
  color: #842029;
}

.alert-success {
  background-color: #d1e7dd;
  color: #0f5132;
}


form label {
  color: var(--text-color);
  font-weight: 500;
}

form input,
form select,
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


.btn {
  padding: 8px 16px;
  border-radius: 6px;
  margin-right: 5px;
  font-weight: 500;
}

.btn-success {
  background-color: #198754;
  color: white;
  border: none;
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
<div class="container mt-3">
    <h2>Daftar Produk</h2>


    <form method="GET" action="" class="mb-3">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Cari produk..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
            <button class="btn btn-primary" type="submit">Cari</button>
        </div>
    </form>


    <form method="GET" action="" class="mb-3">
        <label for="kategori_id" class="form-label">Filter Kategori</label>
        <select name="kategori_id" id="kategori_id" class="form-select" onchange="this.form.submit()">
            <option value="">-- Semua Kategori --</option>
            <?php foreach ($allCategories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= (isset($_GET['kategori_id']) && $_GET['kategori_id'] == $cat['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['nama_kategori']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>
        <h4>Tambah Kategori Baru</h4>
        <form method="POST" action="" class="d-flex mb-3">
            <input type="text" name="nama_kategori" class="form-control me-2" placeholder="Nama kategori baru" required>
            <button type="submit" name="tambah_kategori" class="btn btn-success">Tambah</button>
        </form>

        <a href="add.php" class="btn btn-primary mb-3">+ Tambah Produk</a>
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Gambar</th>
                    <th>Nama</th>
                    <th>Merk</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $p): ?>
                    <tr>
                        <td>
                            <?php if (!empty($p['gambar'])): ?>
                                <img src="../../public/assets/uploads/<?= htmlspecialchars($p['gambar']) ?>" width="80" alt="">
                            <?php else: ?>
                                <em>Tidak ada gambar</em>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($p['nama_produk']) ?></td>
                        <td><?= htmlspecialchars($p['merk']) ?></td>
                        <td>Rp <?= number_format($p['harga'], 0, ',', '.') ?></td>
                        <td><?= htmlspecialchars($p['stok']) ?></td>
                        <td>
                            <a href="edit.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="delete.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin mau hapus?')">Hapus</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
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