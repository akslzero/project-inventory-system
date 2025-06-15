<?php
require_once "../config/database.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$role = $_SESSION['role'];
$nama = $_SESSION['username'];



$database = new Database();
$db = $database->getConnection();

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $username = $_POST["username"];
    $password = $_POST["password"];
    $nama_lengkap = $_POST["nama_lengkap"];
    $role = $_POST["role"];

   
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $query = "INSERT INTO users (username, password, role, nama_lengkap) 
              VALUES (:username, :password, :role, :nama_lengkap)";
    $stmt = $db->prepare($query);

    $stmt->bindParam(":username", $username);
    $stmt->bindParam(":password", $password_hash);
    $stmt->bindParam(":role", $role);
    $stmt->bindParam(":nama_lengkap", $nama_lengkap);

    if ($stmt->execute()) {
        $message = '<div class="alert alert-success">User berhasil didaftarkan!</div>';
    } else {
        $message = '<div class="alert alert-danger">Gagal mendaftarkan user!</div>';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://cdn.boxicons.com/fonts/basic/boxicons.min.css' rel='stylesheet'>
    <link href='https://cdn.boxicons.com/fonts/brands/boxicons-brands.min.css' rel='stylesheet'>
    <link href='style/style.css' rel='stylesheet'>
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
  body {
    font-family: "Poppins", sans-serif;
    background: var(--body-color);
    transition: var(--tran-04);
  }
  .sidebar {
    position: fixed;
    top: 0;
    left: 0;
    height: 100vh;
    width: 250px;
    padding: 10px 14px;
    background: var(--sidebar-color);
    transition: var(--tran-05);
    z-index: 100;
    box-shadow: 0 0 10px rgba(0,0,0,0.04);
  }
  .sidebar.close {
    width: 88px;
  }
  .sidebar .text {
    font-size: 16px;
    font-weight: 500;
    color: var(--text-color);
    transition: var(--tran-04);
    white-space: nowrap;
    opacity: 1;
  }
  .sidebar.close .text {
    opacity: 0;
  }
  .sidebar header {
    position: relative;
    margin-bottom: 20px;
  }
  .sidebar .image-text {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 10px;
  }
  .sidebar .image-text .header-text {
    display: flex;
    flex-direction: column;
  }
  .header-text .name {
    font-weight: 600;
    color: var(--primary-color);
  }
  
  .sidebar .menu-bar {
    display: flex;
    flex-direction: column;
    height: calc(100vh - 80px);
    justify-content: space-between;
  }
  .sidebar .menu-links {
    padding: 0;
    margin: 0;
    list-style: none;
  }
  .sidebar li {
    height: 50px;
    margin-top: 10px;
    display: flex;
    align-items: center;
  }
  .sidebar li a {
    display: flex;
    align-items: center;
    width: 100%;
    height: 100%;
    text-decoration: none;
    border-radius: 6px;
    transition: var(--tran-04);
    color: var(--text-color);
    padding-left: 10px;
  }
  .sidebar li a .icon {
    min-width: 30px;
    font-size: 20px;
    margin-right: 10px;
  }
  .sidebar li a:hover {
    background: var(--primary-color);
    color: #fff;
  }
  .sidebar li a:hover .icon,
  .sidebar li a:hover .text {
    color: #fff;
  }
  .sidebar .bottom-content {
    margin-top: 30px;
  }
  .sidebar .mode {
    position: relative;
    border-radius: 6px;
    background: var(--primary-color-light);
    display: flex;
    align-items: center;
    padding: 0 10px;
    cursor: pointer;
  }
  .sidebar .mode .moon-sun {
    height: 50px;
    width: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .sidebar .mode .mode-text {
    margin-left: 10px;
    font-size: 15px;
  }
  
  .switch::before {
    content: '';
    position: absolute;
    height: 15px;
    width: 15px;
    border-radius: 50%;
    top: 50%;
    left: 5px;
    transform: translateY(-50%);
    background: var(--sidebar-color);
    transition: var(--tran-03);
  }
  body.dark .switch::before {
    left: 24px;
  }
  body.dark {
    --body-color: #18191a;
    --sidebar-color: #242526;
    --primary-color: #3a3b3c;
    --primary-color-light: #3a3b3c;
    --toggle-color: #fff;
    --text-color: #ccc;
  }

  .sidebar header .toggle {
      position: absolute;
      top: 50%;
      right: -30px;
      transform: translateY(-50%) rotate(180deg);
      height: 25px;
      width: 25px;
      background: var(--primary-color);
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 50%;
      color: var(--sidebar-color);
      font-size: 22px;
      transition: var(--tran-03);
    }

    .sidebar.close header .toggle {
      transform: translateY(-50%);
    }

    body.dark .sidebar header .toggle {
      color: var(--text-color);
    }

    .menu-bar .mode .toggle-switch {
      position: absolute;
      right: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100%;
      min-width: 60px;
      cursor: pointer;
      border-radius: 6px;
      background: var(--primary-color-light);
      transition: var(--tran-05);
    }

    .menu-bar .mode .switch {
      position: relative;
      height: 22px;
      width: 44px;
      border-radius: 25px;
      background: var(--toggle-color);
    }

    .switch::before {
      content: '';
      position: absolute;
      height: 15px;
      width: 15px;
      border-radius: 50%;
      top: 50%;
      left: 5px;
      transform: translateY(-50%);
      background: var(--sidebar-color);
      transition: var(--tran-03);
    }

    body.dark .switch::before {
      left: 24px;
    }

    .home {
      position: relative;
      height: 100vh;
      left: 250px;
      width: calc(100% - 250px);
      background: var(--body-color);
      transition: var(--tran-05);

    }

    .home .text {
      font-size: 30px;
      font-weight: 500;
      color: var(--text-color);
      padding: 8px 40px;
    }

    .sidebar.close~.home {
      left: 88px;
      width: calc(100% - 88px);
    }

    body.dark .header-text .name {
      color: #fff !important;
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
          <a href="index.php">
            <i class='bx bx-home-alt icon'></i>
            <span class="text nav-text">Dashboard</span>
          </a>
        </li>
        <?php if ($role === 'admin' || $role === 'staff_produk'): ?>
        <li class="nav-link">
          <a href="../views/products/list.php">
            <i class='bx bx-computer icon'></i>
            <span class="text nav-text">Manajemen Produk</span>
          </a>
        </li>
        <?php endif; ?>
        <?php if ($role === 'admin' || $role === 'staff_transaksi'): ?>
        <li class="nav-link">
          <a href="../views/transactions/list.php">
            <i class='bx bx-wallet icon'></i>
            <span class="text nav-text">Transaksi</span>
          </a>
        </li>
        <?php endif; ?>
        <?php if ($role === 'admin' || $role === 'staff_pelanggan'): ?>
        <li class="nav-link">
          <a href="../views/customers/list.php">
            <i class='bx bx-user icon'></i>
            <span class="text nav-text">Pelanggan</span>
          </a>
        </li>
        <?php endif; ?>
        <?php if ($role === 'admin' || $role === 'staff_supplier'): ?>
        <li class="nav-link">
          <a href="../views/suppliers/list.php">
            <i class='bx bx-truck icon'></i>
            <span class="text nav-text">Supplier</span>
          </a>
        </li>
        <?php endif; ?>
        <?php if ($role === 'admin' || $role === 'staff_laporan'): ?>
        <li class="nav-link">
          <a href="../views/reports/report.php">
            <i class='bx bx-book icon'></i>
            <span class="text nav-text">Laporan</span>
          </a>
        </li>
        <?php endif; ?>
        <?php if ($role === 'admin'): ?>
        <li class="nav-link">
          <a href="register.php">
            <i class='bx bx-user-plus icon'></i>
            <span class="text nav-text">Register</span>
          </a>
        </li>
        <?php endif; ?>
        <li class="nav-link">
          <a href="logout.php" class="text-danger">
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
</body>


<section class="home">
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Register Akun Baru Untuk Karyawan</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($message) echo $message; ?>
                        <form method="post" action="">
                            <div class="mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" name="username" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nama Lengkap (Nama yang tampil)</label>
                                <input type="text" name="nama_lengkap" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Role</label>
                                <select name="role" class="form-select" required>
                                    <option value="admin">admin</option>
                                    <option value="staff_produk">staff_produk</option>
                                    <option value="staff_transaksi">staff_transaksi</option>
                                    <option value="staff_pelanggan">staff_pelanggan</option>
                                    <option value="staff_supplier">staff_supplier</option>
                                    <option value="staff_laporan">staff_laporan</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Register</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
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

</html>