<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

$role = $_SESSION['role'];
$nama = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <title>Dashboard</title>


 
  <link href='https://cdn.boxicons.com/fonts/basic/boxicons.min.css' rel='stylesheet'>
  <link href='https://cdn.boxicons.com/fonts/brands/boxicons-brands.min.css' rel='stylesheet'>
  <link href='assets/style/style.css' rel='stylesheet'>
  


</head>



<body>
  <nav class="sidebar close">
    <header>
      <div class="image-text">
        <div class="image-text">
      <span class="header-text">
        <span class="name">Halo, <?= htmlspecialchars($nama) ?></span>
      </span>
    </div>

       
      </div>
      <i class='bx bx-chevron-right toggle'></i>
    </header>

    <div class="menu-bar">
      <div class="menu">
        


        <ul class="menu-links">
            <li class="nav-link">
              <a href="#">
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

          
          <?php if ($role === 'admin' ): ?>
            <li class="nav-link">
              <a href="register.php">
                <i class='bx bx-user-plus icon'></i>
                <span class="text nav-text">Registrasi</span>
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

  <section class="home">
    <div class="text">Dashboard </div>
    <div class="info">
      <p>Anda login sebagai: <strong><?= htmlspecialchars($role) ?></strong></p>
      <hr>
      <p>Selamat datang di Sistem Inventori Toko Komputer! Gunakan menu sidebar untuk navigasi.</p>
    </div>

    <!-- ini cuman dummy, bukan realtime sesuai data -->
    <main>
      <div class="date">
        <input type="date">
      </div>

      <div class="insights">
        <div class="sales">
          <i class='bx  bx-price-tag icon'></i>
          <div class="middle">
            <div class="left">
              <h3>Total Penjualan</h3>
              <h1>Rp 6.200.000</h1>
            </div>
            <div class="progress">
              <svg>
                <circle r="30" cy="40" cx="40"></circle>
              </svg>
              <div class="number">15%</div>
            </div>

          </div>
          <small> Last 24 hours</small>
        </div>

        <div class="expenses">
          <i class='bx  bx-trending-down icon'></i>
          <div class="middle">
            <div class="left">
              <h3>pengeluaran</h3>
              <h1>Rp 3.600.000</h1>
            </div>
            <div class="progress">
              <svg>
                <circle r="30" cy="40" cx="40"></circle>
              </svg>
              <div class="number">57%</div>
            </div>

          </div>
          <small> Last 24 hours</small>
        </div>

        <div class="pendapatan">
          <i class='bx  bx-trending-up icon'></i>
          <div class="middle">
            <div class="left">
              <h3>pendapatan</h3>
              <h1>Rp 41.200.000</h1>
            </div>
            <div class="progress">
              <svg>
                <circle r="30" cy="40" cx="40"></circle>
              </svg>
              <div class="number">34%</div>
            </div>

          </div>
          <small> Last a week</small>
        </div>
        
      </div>
      
      <div class="recent_order">
        <h1>Penjualan Terakhir</h1>
        <br>
        <table>
          <thead>
            <tr>
              <th>product name</th>
              <th>product number</th>
              <th>payment</th>
              <th>status</th>
            </tr>
          </thead>
          <tbody>

            <tr>
              <td>FlashDisk Samsung</td>
              <td>HR90533</td>
              <td>Due</td>
              <td class="warning">Pending</td>
              <td class="primary">detail</td>
            </tr>
            <tr>
              <td>Fantech Revolver WGP12 Joystick</td>
              <td>PJ53795</td>
              <td>Due</td>
              <td class="warning">Pending</td>
              <td class="primary">detail</td>
            </tr>
            <tr>
              <td>AirmouseV Xtreme gaming</td>
              <td>KN53622</td>
              <td>Due</td>
              <td class="warning">Pending</td>
              <td class="primary">detail</td>
            </tr>
            <tr>
              <td>Gigabyte AORUS X870 ELITE</td>
              <td>AB74822</td>
              <td>Due</td>
              <td class="warning">Pending</td>
              <td class="primary">detail</td>
            </tr>
            <tr>
              <td>ASUS ROG CROSSHAIR X670E HERO</td>
              <td>KR74823</td>
              <td>Due</td>
              <td class="warning">Pending</td>
              <td class="primary">detail</td>
            </tr>
            <tr>
              <td>MSI MEG X670E ACE</td>
              <td>BE74824</td>
              <td>Due</td>
              <td class="warning">Pending</td>
              <td class="primary">detail</td>
            </tr>
            <tr>
              <td>ASROCK X870E TAICHI</td>
              <td>LK74825</td>
              <td>Due</td>
              <td class="warning">Pending</td>
              <td class="primary">detail</td>
            </tr>
            <tr>
              <td>ASUS ROG CROSSHAIR X670E GENE</td>
              <td>VR74826</td>
              <td>Due</td>
              <td class="warning">Pending</td>
              <td class="primary">detail</td>
            </tr>

          </tbody>


        </table>
        <br>
      </div>
    </main>

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