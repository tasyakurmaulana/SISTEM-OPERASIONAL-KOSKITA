-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 22, 2026 at 07:07 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `koskita`
--

-- --------------------------------------------------------

--
-- Table structure for table `checklist_kebersihan`
--

CREATE TABLE `checklist_kebersihan` (
  `id_checklist` int(11) NOT NULL,
  `id_penghuni` int(11) NOT NULL,
  `status` enum('Bersih','Kurang Bersih','Kotor') NOT NULL,
  `catatan` text DEFAULT NULL,
  `tanggal_cek` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `checklist_kebersihan`
--

INSERT INTO `checklist_kebersihan` (`id_checklist`, `id_penghuni`, `status`, `catatan`, `tanggal_cek`) VALUES
(1, 1, 'Bersih', 'bersih', '2026-04-19 14:59:35'),
(2, 2, 'Kurang Bersih', 'jorok', '2026-04-19 14:59:47');

-- --------------------------------------------------------

--
-- Table structure for table `detail_laundry`
--

CREATE TABLE `detail_laundry` (
  `id_detail` int(11) NOT NULL,
  `id_pesanan` int(11) NOT NULL,
  `id_tarif` int(11) NOT NULL,
  `jumlah` decimal(5,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `detail_tagihan`
--

CREATE TABLE `detail_tagihan` (
  `id_detail` int(11) NOT NULL,
  `id_tagihan` int(11) NOT NULL,
  `nama_item` varchar(100) NOT NULL,
  `nominal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kamar`
--

CREATE TABLE `kamar` (
  `id_kamar` int(11) NOT NULL,
  `nomor_kamar` varchar(10) NOT NULL,
  `tipe_kamar` varchar(50) DEFAULT 'Standar',
  `status_kamar` enum('Terisi','Kosong','Perbaikan') DEFAULT 'Kosong'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kategori_keluhan`
--

CREATE TABLE `kategori_keluhan` (
  `id_kategori` int(11) NOT NULL,
  `nama_kategori` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `keluhan`
--

CREATE TABLE `keluhan` (
  `id_keluhan` int(11) NOT NULL,
  `id_penghuni` int(11) NOT NULL,
  `kategori` varchar(50) NOT NULL,
  `deskripsi` text NOT NULL,
  `foto_bukti` varchar(255) NOT NULL,
  `tanggal_pengajuan` datetime DEFAULT current_timestamp(),
  `status` enum('Menunggu','Diproses','Selesai') DEFAULT 'Menunggu',
  `tanggapan_penjaga` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `keluhan`
--

INSERT INTO `keluhan` (`id_keluhan`, `id_penghuni`, `kategori`, `deskripsi`, `foto_bukti`, `tanggal_pengajuan`, `status`, `tanggapan_penjaga`) VALUES
(1, 1, 'Listrik', 'lampu kamar sayaaa ajep ajepp', 'keluhan_1_1776583891.jpg', '2026-04-19 14:31:31', 'Selesai', 'donee yaa, silahkan di cek'),
(2, 2, 'Air', 'air saya mati', 'keluhan_2_1776584976.jpg', '2026-04-19 14:49:36', 'Diproses', 'okee');

-- --------------------------------------------------------

--
-- Table structure for table `metode_pembayaran`
--

CREATE TABLE `metode_pembayaran` (
  `id_metode` int(11) NOT NULL,
  `kategori` enum('Transfer Bank','E-Wallet') NOT NULL,
  `nama_provider` varchar(50) NOT NULL,
  `nomor_rekening` varchar(50) NOT NULL,
  `atas_nama` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `metode_pembayaran`
--

INSERT INTO `metode_pembayaran` (`id_metode`, `kategori`, `nama_provider`, `nomor_rekening`, `atas_nama`) VALUES
(1, 'Transfer Bank', 'BCA', '1234567890', 'Kevin Maulana'),
(2, 'E-Wallet', 'OVO', '08123456789', 'Kevin Maulana'),
(3, 'E-Wallet', 'SHOPEEPAY', '08123456789', 'Kevin Maulana'),
(4, 'E-Wallet', 'GOPAY', '08123456789', 'Kevin Maulana'),
(5, 'E-Wallet', 'DANA', '08123456789', 'Kevin Maulana');

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id_pembayaran` int(11) NOT NULL,
  `id_tagihan` int(11) NOT NULL,
  `id_penghuni` int(11) NOT NULL,
  `id_metode` int(11) NOT NULL,
  `tanggal_bayar` datetime DEFAULT current_timestamp(),
  `jumlah_bayar` decimal(10,2) NOT NULL,
  `bukti_transfer` varchar(255) NOT NULL,
  `status_pembayaran` enum('Pending','Berhasil','Ditolak') DEFAULT 'Pending',
  `catatan_pemilik` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pengaturan_aturan`
--

CREATE TABLE `pengaturan_aturan` (
  `id` int(11) NOT NULL,
  `aturan_kos` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengaturan_aturan`
--

INSERT INTO `pengaturan_aturan` (`id`, `aturan_kos`) VALUES
(1, 'Jam malam maksimal pukul 22.00 WIB.\r\nDilarang membawa hewan peliharaan.\r\nTamu menginap wajib lapor.\r\nTidak boleh bawa anjing.');

-- --------------------------------------------------------

--
-- Table structure for table `pengaturan_harga`
--

CREATE TABLE `pengaturan_harga` (
  `id_pengaturan` int(11) NOT NULL,
  `nama_biaya` varchar(100) NOT NULL,
  `nominal` decimal(10,2) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengaturan_harga`
--

INSERT INTO `pengaturan_harga` (`id_pengaturan`, `nama_biaya`, `nominal`, `keterangan`, `updated_at`) VALUES
(1, 'Tarif Dasar Kamar', 600000.00, 'Tarif sewa bulanan standar', '2026-04-22 04:16:29'),
(3, 'parkir', 19997.00, NULL, '2026-04-22 04:24:29'),
(4, 'listrik', 25000.00, NULL, '2026-04-22 05:02:35');

-- --------------------------------------------------------

--
-- Table structure for table `penghuni`
--

CREATE TABLE `penghuni` (
  `id_penghuni` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `nomor_kamar` varchar(10) NOT NULL,
  `foto_profil` varchar(255) DEFAULT 'default.jpg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `penghuni`
--

INSERT INTO `penghuni` (`id_penghuni`, `id_user`, `nama_lengkap`, `nomor_kamar`, `foto_profil`) VALUES
(1, 5, 'Maulana Istna', 'A-01', 'default.jpg'),
(2, 6, 'Najasyi Nugroho', 'A-02', 'default.jpg'),
(3, 7, 'Septo Danu', 'A-03', 'default.jpg'),
(4, 8, 'Rahmat Agung', 'A-04', 'default.jpg'),
(5, 11, 'Arya Al', 'A-10', 'default.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `pengingat`
--

CREATE TABLE `pengingat` (
  `id_pengingat` int(11) NOT NULL,
  `id_tagihan` int(11) NOT NULL,
  `pesan` text NOT NULL,
  `tanggal_kirim` datetime DEFAULT current_timestamp(),
  `status_baca` enum('Belum Dibaca','Sudah Dibaca') DEFAULT 'Belum Dibaca'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pesanan_laundry`
--

CREATE TABLE `pesanan_laundry` (
  `id_laundry` int(11) NOT NULL,
  `id_penghuni` int(11) NOT NULL,
  `tanggal_pesan` datetime DEFAULT current_timestamp(),
  `status` enum('Menunggu','Diproses','Selesai') DEFAULT 'Menunggu',
  `total_biaya` decimal(10,2) DEFAULT 0.00,
  `id_tagihan` int(11) DEFAULT NULL,
  `berat` decimal(5,2) DEFAULT 0.00,
  `status_bayar` enum('Belum Bayar','Lunas') DEFAULT 'Belum Bayar'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pesanan_laundry`
--

INSERT INTO `pesanan_laundry` (`id_laundry`, `id_penghuni`, `tanggal_pesan`, `status`, `total_biaya`, `id_tagihan`, `berat`, `status_bayar`) VALUES
(5, 1, '2026-04-21 09:55:59', 'Selesai', 20.00, NULL, 3.20, 'Lunas'),
(6, 2, '2026-04-21 10:03:40', 'Selesai', 15000.00, NULL, 1.50, 'Belum Bayar'),
(7, 1, '2026-04-21 10:09:58', 'Selesai', 40000.00, NULL, 3.40, 'Lunas'),
(8, 1, '2026-04-21 10:16:03', 'Selesai', 22200.00, NULL, 3.70, 'Lunas'),
(9, 1, '2026-04-21 12:49:21', 'Selesai', 27000.00, NULL, 4.50, 'Belum Bayar'),
(10, 1, '2026-04-21 12:56:48', 'Selesai', 25800.00, NULL, 4.30, 'Belum Bayar');

-- --------------------------------------------------------

--
-- Table structure for table `receipt`
--

CREATE TABLE `receipt` (
  `id_receipt` int(11) NOT NULL,
  `id_pembayaran` int(11) NOT NULL,
  `nomor_receipt` varchar(50) NOT NULL,
  `tanggal_dibuat` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `staf`
--

CREATE TABLE `staf` (
  `id_staf` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `foto_profil` varchar(255) DEFAULT 'default.jpg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tagihan`
--

CREATE TABLE `tagihan` (
  `id_tagihan` int(11) NOT NULL,
  `id_penghuni` int(11) NOT NULL,
  `bulan` varchar(20) NOT NULL,
  `tahun` year(4) NOT NULL,
  `total_tagihan` decimal(10,2) NOT NULL,
  `status` enum('Belum Lunas','Menunggu Konfirmasi','Lunas') DEFAULT 'Belum Lunas',
  `jatuh_tempo` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `jumlah_diingatkan` int(11) DEFAULT 0,
  `pesan_pengingat` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tarif_laundry`
--

CREATE TABLE `tarif_laundry` (
  `id_tarif` int(11) NOT NULL,
  `jenis_layanan` varchar(100) NOT NULL,
  `harga` decimal(10,2) NOT NULL,
  `satuan` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('penghuni','pemilik','penjaga','laundry') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `username`, `password`, `role`, `created_at`) VALUES
(5, 'maulana', '$2y$10$w9RoxWtpgN3/QldoiN7OteOUBFLeD.hivBNlMjrEYKRgqIg5p9V3e', 'penghuni', '2026-04-14 13:12:49'),
(6, 'najasyi', '$2y$10$CeRTxEMBpgImOGTajCefgOlWrKEv2S/eqFCbly/HuS6/3hv3xRGva', 'penghuni', '2026-04-14 15:22:58'),
(7, 'danu', '$2y$10$ZZ6ex.YY5qXXh0ny9qv9EOozO7RQjAKONbjlZ0K5JSggdFPlXGjP2', 'penghuni', '2026-04-19 06:59:55'),
(8, 'rahmat', '$2y$10$5wWIMrtsj.haizEIST6R7ufw5m0O3l.sXykQBLal5Sa.XGq9Ok3c2', 'penghuni', '2026-04-19 07:00:13'),
(10, 'alip', '$2y$10$T5q6mJj.2g1fsoYeERmcfeub05BDP8iOuil13RUiMtkodybo.F4YW', 'penghuni', '2026-04-19 15:34:43'),
(11, 'arya', '$2y$10$yA6OSWzIQyd5MErcig6nj.c6dc2wchSCU4.8dmVxYUYIw7yl6F06a', 'penghuni', '2026-04-19 15:38:21'),
(12, 'kevin', '$2y$10$3Cpc7PGVkJghv4lUDLu/LecQqCA4rsKXoKw8BfGTKU5gt.EKuwAIC', 'pemilik', '2026-04-22 05:06:59'),
(13, 'yatman', '$2y$10$3Cpc7PGVkJghv4lUDLu/LecQqCA4rsKXoKw8BfGTKU5gt.EKuwAIC', 'penjaga', '2026-04-22 05:06:59'),
(14, 'susi', '$2y$10$3Cpc7PGVkJghv4lUDLu/LecQqCA4rsKXoKw8BfGTKU5gt.EKuwAIC', 'laundry', '2026-04-22 05:06:59');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `checklist_kebersihan`
--
ALTER TABLE `checklist_kebersihan`
  ADD PRIMARY KEY (`id_checklist`),
  ADD KEY `id_penghuni` (`id_penghuni`);

--
-- Indexes for table `detail_laundry`
--
ALTER TABLE `detail_laundry`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `id_pesanan` (`id_pesanan`),
  ADD KEY `id_tarif` (`id_tarif`);

--
-- Indexes for table `detail_tagihan`
--
ALTER TABLE `detail_tagihan`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `id_tagihan` (`id_tagihan`);

--
-- Indexes for table `kamar`
--
ALTER TABLE `kamar`
  ADD PRIMARY KEY (`id_kamar`),
  ADD UNIQUE KEY `nomor_kamar` (`nomor_kamar`);

--
-- Indexes for table `kategori_keluhan`
--
ALTER TABLE `kategori_keluhan`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indexes for table `keluhan`
--
ALTER TABLE `keluhan`
  ADD PRIMARY KEY (`id_keluhan`),
  ADD KEY `id_penghuni` (`id_penghuni`);

--
-- Indexes for table `metode_pembayaran`
--
ALTER TABLE `metode_pembayaran`
  ADD PRIMARY KEY (`id_metode`);

--
-- Indexes for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id_pembayaran`),
  ADD KEY `id_tagihan` (`id_tagihan`),
  ADD KEY `id_penghuni` (`id_penghuni`),
  ADD KEY `id_metode` (`id_metode`);

--
-- Indexes for table `pengaturan_aturan`
--
ALTER TABLE `pengaturan_aturan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pengaturan_harga`
--
ALTER TABLE `pengaturan_harga`
  ADD PRIMARY KEY (`id_pengaturan`);

--
-- Indexes for table `penghuni`
--
ALTER TABLE `penghuni`
  ADD PRIMARY KEY (`id_penghuni`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `pengingat`
--
ALTER TABLE `pengingat`
  ADD PRIMARY KEY (`id_pengingat`),
  ADD KEY `id_tagihan` (`id_tagihan`);

--
-- Indexes for table `pesanan_laundry`
--
ALTER TABLE `pesanan_laundry`
  ADD PRIMARY KEY (`id_laundry`),
  ADD KEY `id_penghuni` (`id_penghuni`),
  ADD KEY `id_tagihan` (`id_tagihan`);

--
-- Indexes for table `receipt`
--
ALTER TABLE `receipt`
  ADD PRIMARY KEY (`id_receipt`),
  ADD UNIQUE KEY `nomor_receipt` (`nomor_receipt`),
  ADD KEY `id_pembayaran` (`id_pembayaran`);

--
-- Indexes for table `staf`
--
ALTER TABLE `staf`
  ADD PRIMARY KEY (`id_staf`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `tagihan`
--
ALTER TABLE `tagihan`
  ADD PRIMARY KEY (`id_tagihan`),
  ADD KEY `id_penghuni` (`id_penghuni`);

--
-- Indexes for table `tarif_laundry`
--
ALTER TABLE `tarif_laundry`
  ADD PRIMARY KEY (`id_tarif`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `checklist_kebersihan`
--
ALTER TABLE `checklist_kebersihan`
  MODIFY `id_checklist` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `detail_laundry`
--
ALTER TABLE `detail_laundry`
  MODIFY `id_detail` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `detail_tagihan`
--
ALTER TABLE `detail_tagihan`
  MODIFY `id_detail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `kamar`
--
ALTER TABLE `kamar`
  MODIFY `id_kamar` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kategori_keluhan`
--
ALTER TABLE `kategori_keluhan`
  MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `keluhan`
--
ALTER TABLE `keluhan`
  MODIFY `id_keluhan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `metode_pembayaran`
--
ALTER TABLE `metode_pembayaran`
  MODIFY `id_metode` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id_pembayaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `pengaturan_aturan`
--
ALTER TABLE `pengaturan_aturan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `pengaturan_harga`
--
ALTER TABLE `pengaturan_harga`
  MODIFY `id_pengaturan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `penghuni`
--
ALTER TABLE `penghuni`
  MODIFY `id_penghuni` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `pengingat`
--
ALTER TABLE `pengingat`
  MODIFY `id_pengingat` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pesanan_laundry`
--
ALTER TABLE `pesanan_laundry`
  MODIFY `id_laundry` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `receipt`
--
ALTER TABLE `receipt`
  MODIFY `id_receipt` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `staf`
--
ALTER TABLE `staf`
  MODIFY `id_staf` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tagihan`
--
ALTER TABLE `tagihan`
  MODIFY `id_tagihan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `tarif_laundry`
--
ALTER TABLE `tarif_laundry`
  MODIFY `id_tarif` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `checklist_kebersihan`
--
ALTER TABLE `checklist_kebersihan`
  ADD CONSTRAINT `checklist_kebersihan_ibfk_1` FOREIGN KEY (`id_penghuni`) REFERENCES `penghuni` (`id_penghuni`) ON DELETE CASCADE;

--
-- Constraints for table `detail_laundry`
--
ALTER TABLE `detail_laundry`
  ADD CONSTRAINT `detail_laundry_ibfk_1` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan_laundry` (`id_laundry`) ON DELETE CASCADE,
  ADD CONSTRAINT `detail_laundry_ibfk_2` FOREIGN KEY (`id_tarif`) REFERENCES `tarif_laundry` (`id_tarif`);

--
-- Constraints for table `detail_tagihan`
--
ALTER TABLE `detail_tagihan`
  ADD CONSTRAINT `detail_tagihan_ibfk_1` FOREIGN KEY (`id_tagihan`) REFERENCES `tagihan` (`id_tagihan`) ON DELETE CASCADE;

--
-- Constraints for table `keluhan`
--
ALTER TABLE `keluhan`
  ADD CONSTRAINT `keluhan_ibfk_1` FOREIGN KEY (`id_penghuni`) REFERENCES `penghuni` (`id_penghuni`) ON DELETE CASCADE;

--
-- Constraints for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`id_tagihan`) REFERENCES `tagihan` (`id_tagihan`) ON DELETE CASCADE,
  ADD CONSTRAINT `pembayaran_ibfk_2` FOREIGN KEY (`id_penghuni`) REFERENCES `penghuni` (`id_penghuni`) ON DELETE CASCADE,
  ADD CONSTRAINT `pembayaran_ibfk_3` FOREIGN KEY (`id_metode`) REFERENCES `metode_pembayaran` (`id_metode`);

--
-- Constraints for table `penghuni`
--
ALTER TABLE `penghuni`
  ADD CONSTRAINT `penghuni_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

--
-- Constraints for table `pengingat`
--
ALTER TABLE `pengingat`
  ADD CONSTRAINT `pengingat_ibfk_1` FOREIGN KEY (`id_tagihan`) REFERENCES `tagihan` (`id_tagihan`) ON DELETE CASCADE;

--
-- Constraints for table `pesanan_laundry`
--
ALTER TABLE `pesanan_laundry`
  ADD CONSTRAINT `pesanan_laundry_ibfk_1` FOREIGN KEY (`id_penghuni`) REFERENCES `penghuni` (`id_penghuni`) ON DELETE CASCADE,
  ADD CONSTRAINT `pesanan_laundry_ibfk_2` FOREIGN KEY (`id_tagihan`) REFERENCES `tagihan` (`id_tagihan`) ON DELETE SET NULL;

--
-- Constraints for table `receipt`
--
ALTER TABLE `receipt`
  ADD CONSTRAINT `receipt_ibfk_1` FOREIGN KEY (`id_pembayaran`) REFERENCES `pembayaran` (`id_pembayaran`) ON DELETE CASCADE;

--
-- Constraints for table `staf`
--
ALTER TABLE `staf`
  ADD CONSTRAINT `staf_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

--
-- Constraints for table `tagihan`
--
ALTER TABLE `tagihan`
  ADD CONSTRAINT `tagihan_ibfk_1` FOREIGN KEY (`id_penghuni`) REFERENCES `penghuni` (`id_penghuni`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
