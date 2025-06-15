-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 04 Jun 2025 pada 01.50
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_inventory_toko_komputer`
--

CREATE DATABASE IF NOT EXISTS `db_inventory_toko_komputer` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `db_inventory_toko_komputer`;

-- --------------------------------------------------------

--
-- Struktur dari tabel `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `nama_kategori` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `categories`
--

INSERT INTO `categories` (`id`, `nama_kategori`) VALUES
(13, 'VGA'),
(15, 'Power Supply'),
(18, 'Casing'),
(19, 'Processor'),
(20, 'RAM'),
(21, 'SSD'),
(22, 'Motherboard'),
(23, 'Fan');

-- --------------------------------------------------------

--
-- Struktur dari tabel `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `nama_customer` varchar(100) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `telp` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `customers`
--

INSERT INTO `customers` (`id`, `nama_customer`, `alamat`, `telp`, `email`) VALUES
(5, 'Rusdi', 'Solo', '089236471226', 'rusdi95@gmail.com');

-- --------------------------------------------------------

--
-- Struktur dari tabel `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `nama_produk` varchar(100) DEFAULT NULL,
  `merk` varchar(100) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `harga` decimal(15,2) DEFAULT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `stok` int(11) DEFAULT NULL,
  `kategori_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `products`
--

INSERT INTO `products` (`id`, `nama_produk`, `merk`, `deskripsi`, `harga`, `gambar`, `stok`, `kategori_id`) VALUES
(27, 'GTX 1660 TI', 'ASUS TUF', 'Like New', 1999999.00, 'asus-tuf-gaming-geforce-gtx-1660-ti-evo-6gb-gddr6.jpg', 4, 13),
(28, 'AMD Ryzen 7 5700X3D', 'AMD', '', 4000000.00, 'aaaa-18.png', 15, 19),
(29, 'Gigabyte AORUS X870 ELITE', 'Gigabyte', '', 6000000.00, 'Png.png', 2, 22),
(30, 'HYTE Y70 TOUCH', 'HYTE', '', 6500000.00, 'id-11134207-7r98v-lx9bvr4lgl4tf1.jpg', 8, 18);

-- --------------------------------------------------------

--
-- Struktur dari tabel `suppliers`
--

CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL,
  `nama_supplier` varchar(100) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `telp` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `suppliers`
--

INSERT INTO `suppliers` (`id`, `nama_supplier`, `alamat`, `telp`, `email`) VALUES
(6, 'Fuad', 'Klaten', '086271738193', 'fuadi1990@gmail.com');

-- --------------------------------------------------------

--
-- Struktur dari tabel `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `kode_transaksi` varchar(50) NOT NULL,
  `tipe` enum('Pembelian','Penjualan') NOT NULL,
  `tanggal` datetime NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `transactions`
--

INSERT INTO `transactions` (`id`, `kode_transaksi`, `tipe`, `tanggal`, `user_id`, `supplier_id`, `customer_id`) VALUES
(12, 'PEN20250602168', 'Penjualan', '2025-06-02 07:43:06', 6, NULL, 5),
(13, 'PEM20250602182', 'Pembelian', '2025-06-02 07:45:16', 8, 6, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `transaction_details`
--

CREATE TABLE `transaction_details` (
  `id` int(11) NOT NULL,
  `transaction_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `jumlah` int(11) DEFAULT NULL,
  `harga_satuan` decimal(15,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `transaction_details`
--

INSERT INTO `transaction_details` (`id`, `transaction_id`, `product_id`, `jumlah`, `harga_satuan`) VALUES
(22, 12, 29, 1, 6000000.00),
(23, 12, 27, 1, 1999999.00),
(24, 13, 28, 10, 4000000.00),
(25, 13, 30, 2, 6500000.00);

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','staff_produk','staff_transaksi','staff_pelanggan','staff_supplier','staff_laporan') DEFAULT NULL,
  `nama_lengkap` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `nama_lengkap`, `created_at`) VALUES
(6, 'admin', '$2y$10$XQQuUFKHace.YC9t0cLpB.fGCK9WZhl/xJkrivmZ0lLOFjTFZrYE6', 'admin', 'admin', '2025-05-29 05:31:08'),
(7, 'Budi', '$2y$10$PGkM6jtPYXBjap63ZMVjzupAROTRUb0ohrNhNgQdbUaqd7c6slslu', 'staff_produk', 'Budi Staff Produk', '2025-06-02 04:44:07'),
(8, 'Adi', '$2y$10$az47LHijcje8AJqOYPXVWOJNZn9uPHmBenmwu7EvJ92TOMbycD4Dy', 'staff_transaksi', 'Adi Staff Transaksi', '2025-06-02 04:49:00'),
(10, 'Roni', '$2y$10$ob2eQfnFFjvj80po53ztfuPfYxjlTlqrQg.M5GGHWR9Stz5UJsNcS', 'staff_supplier', 'Roni Staff Supplier', '2025-06-02 04:50:09'),
(11, 'Alex', '$2y$10$aYAtHFP5kX61iBR0AifzIuh9mrrir9CV20yzNYrlGPVyzAG2repaS', 'staff_laporan', 'Alex Staff Laporan', '2025-06-02 04:50:40'),
(12, 'Ari', '$2y$10$EF70n1fUO2HyN5rXK8Nlb.gJN0eLP7nL9eGBuSN2Bu1f4h1DPd4FC', 'staff_pelanggan', 'Ari Staff Pelanggan', '2025-06-02 04:55:50');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kategori_id` (`kategori_id`);

--
-- Indeks untuk tabel `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indeks untuk tabel `transaction_details`
--
ALTER TABLE `transaction_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transaction_id` (`transaction_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT untuk tabel `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT untuk tabel `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT untuk tabel `transaction_details`
--
ALTER TABLE `transaction_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`kategori_id`) REFERENCES `categories` (`id`);

--
-- Ketidakleluasaan untuk tabel `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`),
  ADD CONSTRAINT `transactions_ibfk_3` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`);

--
-- Ketidakleluasaan untuk tabel `transaction_details`
--
ALTER TABLE `transaction_details`
  ADD CONSTRAINT `transaction_details_ibfk_1` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`),
  ADD CONSTRAINT `transaction_details_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
