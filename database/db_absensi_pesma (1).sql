-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 23 Okt 2026 pada 05.36
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
-- Database: `db_absensi_pesma`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `absensi`
--

CREATE TABLE `absensi` (
  `id_absensi` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `waktu` enum('Subuh','Magrib','Isya') NOT NULL,
  `id_user` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `absensi`
--

INSERT INTO `absensi` (`id_absensi`, `tanggal`, `waktu`, `id_user`, `created_at`) VALUES
(1, '2026-10-10', 'Magrib', 2, '2026-10-11 20:11:13'),
(2, '2026-10-13', 'Subuh', 3, '2026-10-13 14:24:44'),
(3, '2026-10-14', 'Subuh', 2, '2026-10-14 06:15:41'),
(4, '2026-10-14', 'Magrib', 2, '2026-10-14 08:48:53'),
(5, '2026-10-14', 'Isya', 3, '2026-10-14 08:52:31'),
(6, '2026-10-17', 'Subuh', 2, '2026-10-17 14:11:38'),
(7, '2026-10-19', 'Subuh', 2, '2026-10-19 09:18:36');

-- --------------------------------------------------------

--
-- Struktur dari tabel `detail_absensi`
--

CREATE TABLE `detail_absensi` (
  `id_detail` int(11) NOT NULL,
  `id_absensi` int(11) NOT NULL,
  `id_mahasiswi` int(11) NOT NULL,
  `status` enum('Hadir','Sakit','Izin','Alpa') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `detail_absensi`
--

INSERT INTO `detail_absensi` (`id_detail`, `id_absensi`, `id_mahasiswi`, `status`, `created_at`) VALUES
(3, 2, 3, 'Hadir', '2026-10-13 14:24:44'),
(4, 3, 3, 'Hadir', '2026-10-14 06:15:41'),
(5, 3, 4, 'Hadir', '2026-10-14 06:15:41'),
(6, 3, 11, 'Hadir', '2026-10-14 06:15:41'),
(7, 3, 9, 'Izin', '2026-10-14 06:15:41'),
(8, 3, 10, 'Izin', '2026-10-14 06:15:41'),
(9, 3, 8, 'Hadir', '2026-10-14 06:15:41'),
(10, 3, 5, 'Hadir', '2026-10-14 06:15:41'),
(11, 3, 7, 'Hadir', '2026-10-14 06:15:41'),
(12, 3, 6, 'Hadir', '2026-10-14 06:15:41'),
(13, 4, 3, 'Sakit', '2026-10-14 08:48:53'),
(14, 4, 4, 'Hadir', '2026-10-14 08:48:53'),
(15, 4, 11, 'Hadir', '2026-10-14 08:48:53'),
(16, 4, 9, 'Hadir', '2026-10-14 08:48:53'),
(17, 4, 10, 'Hadir', '2026-10-14 08:48:53'),
(18, 4, 8, 'Hadir', '2026-10-14 08:48:53'),
(19, 4, 5, 'Hadir', '2026-10-14 08:48:53'),
(20, 4, 7, 'Izin', '2026-10-14 08:48:53'),
(21, 4, 6, 'Hadir', '2026-10-14 08:48:53'),
(22, 5, 3, 'Hadir', '2026-10-14 08:52:31'),
(23, 5, 4, 'Izin', '2026-10-14 08:52:31'),
(24, 5, 11, 'Hadir', '2026-10-14 08:52:31'),
(25, 5, 9, 'Sakit', '2026-10-14 08:52:31'),
(26, 5, 10, 'Hadir', '2026-10-14 08:52:31'),
(27, 5, 8, 'Hadir', '2026-10-14 08:52:31'),
(28, 5, 5, 'Hadir', '2026-10-14 08:52:31'),
(29, 5, 7, 'Hadir', '2026-10-14 08:52:31'),
(30, 5, 6, 'Hadir', '2026-10-14 08:52:31'),
(31, 6, 13, 'Hadir', '2026-10-17 14:11:38'),
(32, 6, 3, 'Hadir', '2026-10-17 14:11:38'),
(33, 6, 4, 'Hadir', '2026-10-17 14:11:38'),
(34, 6, 11, 'Hadir', '2026-10-17 14:11:38'),
(35, 6, 9, 'Hadir', '2026-10-17 14:11:38'),
(36, 6, 10, 'Hadir', '2026-10-17 14:11:38'),
(37, 6, 8, 'Hadir', '2026-10-17 14:11:38'),
(38, 6, 5, 'Hadir', '2026-10-17 14:11:38'),
(39, 6, 7, 'Hadir', '2026-10-17 14:11:38'),
(40, 6, 6, 'Hadir', '2026-10-17 14:11:38'),
(41, 7, 13, 'Hadir', '2026-10-19 09:18:36'),
(42, 7, 3, 'Izin', '2026-10-19 09:18:36'),
(43, 7, 4, 'Hadir', '2026-10-19 09:18:36'),
(44, 7, 11, 'Hadir', '2026-10-19 09:18:36'),
(45, 7, 9, 'Hadir', '2026-10-19 09:18:36'),
(46, 7, 8, 'Hadir', '2026-10-19 09:18:36'),
(47, 7, 5, 'Hadir', '2026-10-19 09:18:36'),
(48, 7, 7, 'Hadir', '2026-10-19 09:18:36'),
(49, 7, 6, 'Hadir', '2026-10-19 09:18:36');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kamar`
--

CREATE TABLE `kamar` (
  `id_kamar` int(11) NOT NULL,
  `nomor_kamar` varchar(10) NOT NULL,
  `lantai` int(11) NOT NULL,
  `kapasitas` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `kamar`
--

INSERT INTO `kamar` (`id_kamar`, `nomor_kamar`, `lantai`, `kapasitas`, `created_at`) VALUES
(1, '201', 2, 3, '2026-10-11 15:42:03'),
(3, '203', 2, 4, '2026-10-11 15:43:38'),
(4, '204', 2, 4, '2026-10-11 21:20:49'),
(5, '325', 3, 4, '2026-10-12 14:46:00'),
(6, '326', 3, 4, '2026-10-12 14:46:20'),
(7, '205', 2, 4, '2026-10-13 13:34:25'),
(8, '206', 2, 4, '2026-10-13 13:43:21'),
(9, '207', 2, 4, '2026-10-13 13:43:35'),
(10, '208', 2, 4, '2026-10-13 13:44:14'),
(11, '209', 2, 4, '2026-10-13 13:44:36'),
(12, '210', 2, 4, '2026-10-13 14:19:19'),
(13, '327', 3, 4, '2026-10-13 14:19:36'),
(14, '328', 3, 4, '2026-10-14 06:07:21'),
(15, '329', 3, 4, '2026-10-14 06:08:34'),
(16, '330', 3, 4, '2026-10-14 06:08:50'),
(17, '331', 3, 4, '2026-10-14 06:09:11'),
(18, '332', 3, 4, '2026-10-14 06:09:21'),
(19, '333', 3, 4, '2026-10-14 06:09:34'),
(20, '334', 3, 4, '2026-10-14 06:09:45');

-- --------------------------------------------------------

--
-- Struktur dari tabel `mahasiswi`
--

CREATE TABLE `mahasiswi` (
  `id_mahasiswi` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `prodi` varchar(100) DEFAULT NULL,
  `angkatan` year(4) DEFAULT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `id_kamar` int(11) NOT NULL,
  `status` enum('Aktif','Nonaktif') NOT NULL DEFAULT 'Aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `mahasiswi`
--

INSERT INTO `mahasiswi` (`id_mahasiswi`, `nama`, `prodi`, `angkatan`, `no_hp`, `id_kamar`, `status`, `created_at`) VALUES
(3, 'Karyamah', 'Sistem Informasi', '2024', '085720419386', 1, 'Aktif', '2026-10-13 14:20:18'),
(4, 'Raisa Nurul', 'Sistem Informasi', '2025', '085720419386', 1, 'Aktif', '2026-10-14 06:10:44'),
(5, 'Rifa Nur', 'Bisnis Digital', '2024', '085720419377', 19, 'Aktif', '2026-10-14 06:11:18'),
(6, 'Vikaa', 'Teknologi Pangan', '2024', '085720419385', 19, 'Aktif', '2026-10-14 06:11:53'),
(7, 'Seni Amel', 'Perbankan Syariah', '2024', '085720419389', 19, 'Aktif', '2026-10-14 06:12:19'),
(8, 'Listya Nur Fadilah', 'Sistem Informasi', '2024', '085720419388', 18, 'Aktif', '2026-10-14 06:12:54'),
(9, 'Euis', 'Bimbingan Konseling', '2023', '085720419389', 18, 'Aktif', '2026-10-14 06:13:36'),
(10, 'Hidya', 'Bimbingan Konseling', '2023', '085720419388', 18, 'Aktif', '2026-10-14 06:14:09'),
(11, 'Putri Salsabila', 'Bimbingan Konseling', '2025', '085720419389', 5, 'Aktif', '2026-10-14 06:14:58'),
(13, 'Andi Devany', 'Manajemen Bisnis Syariah', '2024', '085720419389', 1, 'Aktif', '2026-10-16 09:57:03');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `kode_user` varchar(20) DEFAULT NULL,
  `nama` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('Super Admin','Admin','Wali Santri') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id_user`, `kode_user`, `nama`, `username`, `password`, `role`, `created_at`) VALUES
(1, 'SAD001', 'Super Admin PESMA', 'superadmin', '$2y$10$nmXQzrEooebp63jdsLvMxOexBqxZwEDzj3p73.XC0jD6iEXIwEqtK', 'Super Admin', '2026-10-11 02:38:28'),
(2, 'ADM002', 'Admin', 'admin1', '$2y$10$zqx967o6uQMSVvnm7qrvMOV16wOL89/z6qNl77715db6e8ZmqoOQK', 'Admin', '2026-10-11 03:11:30'),
(3, 'WST003', 'Nurlaela Rahman', 'walisantri1', '$2y$10$rl4mmx.SeX0/H4PTi43dQuUy4s0tNeonJhE.siFrLxTBNeyD0W8tS', 'Wali Santri', '2026-10-11 20:27:12'),
(5, 'WST004', 'Tias', 'walisantri2', '$2y$10$fEb8.20/DOZ2FyEqf5u14.YPtamo3xMDpfxMnuQ6yFSTs4um6gRue', 'Wali Santri', '2026-10-13 18:49:31');

--
-- Trigger `users`
--
DELIMITER $$
CREATE TRIGGER `trg_kode_user` BEFORE INSERT ON `users` FOR EACH ROW BEGIN
    DECLARE v_prefix VARCHAR(3);
    DECLARE v_nomor INT;

    SET v_prefix = CASE NEW.role
        WHEN 'Admin' THEN 'ADM'
        WHEN 'Wali Santri' THEN 'WST'
        WHEN 'Super Admin' THEN 'SAD'
    END;

    SELECT COALESCE(
        MAX(CAST(SUBSTRING(kode_user, 4) AS UNSIGNED)),
        0
    ) + 1
    INTO v_nomor
    FROM users
    WHERE role = NEW.role
      AND kode_user IS NOT NULL;

    SET NEW.kode_user = CONCAT(
        v_prefix,
        LPAD(v_nomor, 3, '0')
    );
END
$$
DELIMITER ;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `absensi`
--
ALTER TABLE `absensi`
  ADD PRIMARY KEY (`id_absensi`),
  ADD UNIQUE KEY `uq_absensi_tanggal_waktu` (`tanggal`,`waktu`),
  ADD KEY `fk_absensi_user` (`id_user`);

--
-- Indeks untuk tabel `detail_absensi`
--
ALTER TABLE `detail_absensi`
  ADD PRIMARY KEY (`id_detail`),
  ADD UNIQUE KEY `uq_detail_absensi_mahasiswi` (`id_absensi`,`id_mahasiswi`),
  ADD KEY `fk_detail_mahasiswi` (`id_mahasiswi`);

--
-- Indeks untuk tabel `kamar`
--
ALTER TABLE `kamar`
  ADD PRIMARY KEY (`id_kamar`),
  ADD UNIQUE KEY `nomor_kamar` (`nomor_kamar`);

--
-- Indeks untuk tabel `mahasiswi`
--
ALTER TABLE `mahasiswi`
  ADD PRIMARY KEY (`id_mahasiswi`),
  ADD KEY `fk_mahasiswi_kamar` (`id_kamar`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `absensi`
--
ALTER TABLE `absensi`
  MODIFY `id_absensi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `detail_absensi`
--
ALTER TABLE `detail_absensi`
  MODIFY `id_detail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT untuk tabel `kamar`
--
ALTER TABLE `kamar`
  MODIFY `id_kamar` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT untuk tabel `mahasiswi`
--
ALTER TABLE `mahasiswi`
  MODIFY `id_mahasiswi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `absensi`
--
ALTER TABLE `absensi`
  ADD CONSTRAINT `fk_absensi_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `detail_absensi`
--
ALTER TABLE `detail_absensi`
  ADD CONSTRAINT `fk_detail_absensi` FOREIGN KEY (`id_absensi`) REFERENCES `absensi` (`id_absensi`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_detail_mahasiswi` FOREIGN KEY (`id_mahasiswi`) REFERENCES `mahasiswi` (`id_mahasiswi`) ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `mahasiswi`
--
ALTER TABLE `mahasiswi`
  ADD CONSTRAINT `fk_mahasiswi_kamar` FOREIGN KEY (`id_kamar`) REFERENCES `kamar` (`id_kamar`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
