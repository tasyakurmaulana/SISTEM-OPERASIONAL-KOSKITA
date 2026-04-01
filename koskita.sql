-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 01, 2026 at 08:54 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

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
  `id_kamar` int(11) NOT NULL,
  `id_penjaga` int(11) NOT NULL,
  `tanggal_cek` datetime DEFAULT current_timestamp(),
  `status_kebersihan` enum('Bersih','Kurang Bersih','Kotor') NOT NULL,
  `catatan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `id_kategori` int(11) NOT NULL,
  `deskripsi` text NOT NULL,
  `foto_bukti` varchar(255) NOT NULL,
  `tanggal_pengajuan` datetime DEFAULT current_timestamp(),
  `status` enum('Menunggu','Diterima','Diproses','Selesai') DEFAULT 'Menunggu',
  `tanggapan_penjaga` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
-- Table structure for table `pengaturan_harga`
--

CREATE TABLE `pengaturan_harga` (
  `id_pengaturan` int(11) NOT NULL,
  `nama_biaya` varchar(100) NOT NULL,
  `nominal` decimal(10,2) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `id_pesanan` int(11) NOT NULL,
  `id_penghuni` int(11) NOT NULL,
  `tanggal_pesan` datetime DEFAULT current_timestamp(),
  `status` enum('Menunggu Diambil','Diproses','Selesai') DEFAULT 'Menunggu Diambil',
  `total_biaya` decimal(10,2) DEFAULT 0.00,
  `id_tagihan` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
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
(1, 'penghuni1', '$2y$10$h9Nf.npQj2J1I8EcBNCNFeJivi21WBKQiQQfiAVwXkI6U/Qs/myoC', 'penghuni', '2026-02-26 18:21:20'),
(2, 'boskos', '$2y$10$h9Nf.npQj2J1I8EcBNCNFeJivi21WBKQiQQfiAVwXkI6U/Qs/myoC', 'pemilik', '2026-02-26 18:21:20'),
(3, 'penjaga1', '$2y$10$h9Nf.npQj2J1I8EcBNCNFeJivi21WBKQiQQfiAVwXkI6U/Qs/myoC', 'penjaga', '2026-02-26 18:21:20'),
(4, 'laundry1', '$2y$10$h9Nf.npQj2J1I8EcBNCNFeJivi21WBKQiQQfiAVwXkI6U/Qs/myoC', 'laundry', '2026-02-26 18:21:20');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `checklist_kebersihan`
--
ALTER TABLE `checklist_kebersihan`
  ADD PRIMARY KEY (`id_checklist`),
  ADD KEY `id_kamar` (`id_kamar`),
  ADD KEY `id_penjaga` (`id_penjaga`);

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
  ADD KEY `id_penghuni` (`id_penghuni`),
  ADD KEY `id_kategori` (`id_kategori`);

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
  ADD PRIMARY KEY (`id_pesanan`),
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
  MODIFY `id_checklist` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `detail_laundry`
--
ALTER TABLE `detail_laundry`
  MODIFY `id_detail` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `detail_tagihan`
--
ALTER TABLE `detail_tagihan`
  MODIFY `id_detail` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `id_keluhan` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `metode_pembayaran`
--
ALTER TABLE `metode_pembayaran`
  MODIFY `id_metode` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id_pembayaran` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pengaturan_harga`
--
ALTER TABLE `pengaturan_harga`
  MODIFY `id_pengaturan` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `penghuni`
--
ALTER TABLE `penghuni`
  MODIFY `id_penghuni` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pengingat`
--
ALTER TABLE `pengingat`
  MODIFY `id_pengingat` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pesanan_laundry`
--
ALTER TABLE `pesanan_laundry`
  MODIFY `id_pesanan` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `id_tagihan` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tarif_laundry`
--
ALTER TABLE `tarif_laundry`
  MODIFY `id_tarif` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `checklist_kebersihan`
--
ALTER TABLE `checklist_kebersihan`
  ADD CONSTRAINT `checklist_kebersihan_ibfk_1` FOREIGN KEY (`id_kamar`) REFERENCES `kamar` (`id_kamar`) ON DELETE CASCADE,
  ADD CONSTRAINT `checklist_kebersihan_ibfk_2` FOREIGN KEY (`id_penjaga`) REFERENCES `users` (`id_user`);

--
-- Constraints for table `detail_laundry`
--
ALTER TABLE `detail_laundry`
  ADD CONSTRAINT `detail_laundry_ibfk_1` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan_laundry` (`id_pesanan`) ON DELETE CASCADE,
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
  ADD CONSTRAINT `keluhan_ibfk_1` FOREIGN KEY (`id_penghuni`) REFERENCES `penghuni` (`id_penghuni`) ON DELETE CASCADE,
  ADD CONSTRAINT `keluhan_ibfk_2` FOREIGN KEY (`id_kategori`) REFERENCES `kategori_keluhan` (`id_kategori`);

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
