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

$error = '';
$success = '';

$category = new Category($db);
$allCategories = $category->readAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $db = $database->getConnection();

    $product = new Product($db);
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

        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($fileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES['gambar']['tmp_name'], $targetFilePath)) {
                $product->gambar = $fileName;
            } else {
                $error = "Gagal meng-upload gambar.";
            }
        } else {
            $error = "Format gambar tidak diperbolehkan. Hanya jpg, jpeg, png, gif.";
        }
    } else {
        $product->gambar = null; 
    }

    if (empty($error)) {
        if ($product->create()) {
            $success = "Produk berhasil ditambahkan!";
        } else {
            $error = "Gagal menambahkan produk.";
        }
    }
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Add Product</title>


 
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




h2 {
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
<h2>Tambah Produk Baru</h2>

<?php if ($error): ?>
    <div class="alert alert-danger"><?= $error ?></div>
<?php elseif ($success): ?>
    <div class="alert alert-success"><?= $success ?></div>
<?php endif; ?>

<form action="" method="POST" enctype="multipart/form-data">
    <div class="mb-3">
        <label>Nama Produk</label>
        <input type="text" name="nama_produk" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Merk</label>
        <input type="text" name="merk" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Harga</label>
        <input type="number" name="harga" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Deskripsi</label>
        <textarea name="deskripsi" class="form-control" rows="3"></textarea>
    </div>

    <div class="mb-3">
        <label>Stok</label>
        <input type="number" name="stok" class="form-control" required>
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
        <label>Gambar Produk</label>
        <input type="file" name="gambar" class="form-control" accept="image/*">
    </div>

    <button type="submit" class="btn btn-primary">Tambah Produk</button>
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
