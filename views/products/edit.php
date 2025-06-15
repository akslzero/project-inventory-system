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

require_once "../../config/database.php";
require_once "../../models/Product.php";
require_once "../../models/Category.php";


$database = new Database();
$db = $database->getConnection();
$product = new Product($db);


$category = new Category($db);
$allCategories = $category->readAll();

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<p>Produk tidak ditemukan.</p>";
    exit;
}

$product->id = $_GET['id'];
$data = $product->readOne();

if (!$data) {
    echo "<p>Produk tidak ditemukan.</p>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product->nama_produk = $_POST['nama_produk'];
    $product->merk = $_POST['merk'];
    $product->harga = $_POST['harga'];
    $product->deskripsi = $_POST['deskripsi'];
    $product->stok = $_POST['stok'];
    $product->kategori_id = $_POST['kategori_id'];

   
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = "../../public/assets/uploads/";
        $fileName = basename($_FILES['gambar']['name']);
        $targetFilePath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $targetFilePath)) {
            $product->gambar = $fileName;
        }
    } else {
        $product->gambar = $data['gambar'];
    }

    if ($product->update()) {
        echo "<div class='alert alert-success'>Produk berhasil diupdate.</div>";
        $data = $product->readOne(); 
    } else {
        echo "<div class='alert alert-danger'>Gagal mengupdate produk.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Edit Product</title>


 
  <link href='https://cdn.boxicons.com/fonts/basic/boxicons.min.css' rel='stylesheet'>
  <link href='https://cdn.boxicons.com/fonts/brands/boxicons-brands.min.css' rel='stylesheet'>
  <link href='../style/style.css' rel='stylesheet'>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" >

</head>

<style>
 @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap");

:root {
  --body-color: #e4e9f7;
  --sidebar-color: #fff;
  --primary-color: #695cfe;
  --primary-color-light: #f6f5ff;
  --toggle-color: #ddd;
  --text-color: #707070;
  --tran-02: all 0.2s ease;
  --tran-03: all 0.3s ease;
  --tran-04: all 0.4s ease;
  --tran-05: all 0.5s ease;
}

.home {
    
    padding-top: 20px;
    background: var(--body-color);
    
}


body {
  font-family: "Poppins", sans-serif;
  background: var(--body-color);
  transition: var(--tran-04);
  padding-left: 150px;
}

body.dark {
  --body-color: #18191a;
  --sidebar-color: #242526;
  --primary-color: #3a3b3c;
  --primary-color-light: #3a3b3c;
  --toggle-color: #fff;
  --text-color: #ccc;
}

.container {
  max-width: 800px;
  margin: 50px auto;
  padding: 20px;
  background: var(--sidebar-color);
  border-radius: 8px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

h2 {
  font-size: 24px;
  font-weight: 600;
  color: var(--primary-color);
  margin-bottom: 20px;
  text-align: center;
}

form {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

label, h2 {
  font-weight: 500;
  color: var(--text-color);
}

input[type="text"],
input[type="number"],
textarea,
select,
input[type="file"] {
  padding: 10px;
  font-size: 16px;
  border: 1px solid #ccc;
  border-radius: 6px;
  background: var(--primary-color-light);
  transition: var(--tran-02);
}

input[type="text"]:focus,
input[type="number"]:focus,
textarea:focus,
select:focus,
input[type="file"]:focus {
  border-color: var(--primary-color);
  outline: none;
}

textarea {
  resize: vertical;
  min-height: 100px;
}

button[type="submit"],
a.btn {
  padding: 10px 20px;
  font-size: 16px;
  border-radius: 6px;
  text-align: center;
  cursor: pointer;
  transition: var(--tran-02);
}

button[type="submit"] {
  background: var(--primary-color);
  color: #fff;
  border: none;
}

button[type="submit"]:hover {
  background: darken(var(--primary-color), 10%);
}

a.btn {
  display: inline-block;
  background: #ccc;
  color: #fff;
  text-decoration: none;
  text-align: center;
}

a.btn:hover {
  background: darken(#ccc, 10%);
}

.mb-3 {
  margin-bottom: 15px;
}

.page-container {
    display: flex;
    height: 100vh;
}

.sidebar {
    width: 250px;
    background: var(--sidebar-color);
   
}

.main-content {
    flex-grow: 1;
    padding: 20px;
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
<div class="container mt-5">>
<h2>Edit Produk</h2>

<form method="POST" enctype="multipart/form-data">
    <div class="mb-3">
        <label>Nama Produk</label>
        <input type="text" name="nama_produk" class="form-control" value="<?= htmlspecialchars($data['nama_produk']) ?>" required>
    </div>
    <div class="mb-3">
        <label>Merk</label>
        <input type="text" name="merk" class="form-control" value="<?= htmlspecialchars($data['merk']) ?>" required>
    </div>
    <div class="mb-3">
        <label>Harga</label>
        <input type="number" name="harga" class="form-control" value="<?= htmlspecialchars($data['harga']) ?>" required>
    </div>
    <div class="mb-3">
        <label>Deskripsi</label>
        <textarea name="deskripsi" class="form-control"><?= htmlspecialchars($data['deskripsi']) ?></textarea>
    </div>
    <div class="mb-3">
        <label>Stok</label>
        <input type="number" name="stok" class="form-control" value="<?= htmlspecialchars($data['stok']) ?>" required>
    </div>
    <div class="mb-3">
        <label>Kategori</label>
        <select name="kategori_id" class="form-control" required>
            <option value="">-- Pilih Kategori --</option>
            <?php foreach ($allCategories as $cat): ?>
                <option value="<?= $cat['id'] ?>">
                    <?= htmlspecialchars($cat['nama_kategori']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="mb-3">
        <label>Gambar Produk</label><br>
        <img src="../../public/assets/uploads/<?= htmlspecialchars($data['gambar']) ?>" width="100"><br>
        <input type="file" name="gambar" class="form-control mt-2">
    </div>
    <button type="submit" class="btn btn-primary">Update</button>
    <a href="list.php" class="btn btn-secondary">Batal</a>
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