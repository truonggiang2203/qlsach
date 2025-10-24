-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 24, 2025 at 10:07 AM
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
-- Database: `qlsach`
--

-- --------------------------------------------------------

--
-- Table structure for table `binh_luan`
--

CREATE TABLE `binh_luan` (
  `id_bl` varchar(5) NOT NULL,
  `id_sach` varchar(5) NOT NULL,
  `binh_luan` text NOT NULL,
  `so_sao` float(6,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `binh_luan`
--

INSERT INTO `binh_luan` (`id_bl`, `id_sach`, `binh_luan`, `so_sao`) VALUES
('BL001', 'S0003', 'Sách rất hay và hữu ích cho công việc!', 5.00),
('BL002', 'S0002', 'Nội dung cảm động, đọc rất lôi cuốn.', 4.50);

-- --------------------------------------------------------

--
-- Table structure for table `chi_tiet_don_hang`
--

CREATE TABLE `chi_tiet_don_hang` (
  `id_don_hang` varchar(5) NOT NULL,
  `id_sach` varchar(5) NOT NULL,
  `so_luong_ban` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chi_tiet_don_hang`
--

INSERT INTO `chi_tiet_don_hang` (`id_don_hang`, `id_sach`, `so_luong_ban`) VALUES
('DH001', 'S0001', 2),
('DH001', 'S0003', 1),
('DH002', 'S0002', 1);

-- --------------------------------------------------------

--
-- Table structure for table `chi_tiet_phieu_nhap`
--

CREATE TABLE `chi_tiet_phieu_nhap` (
  `id_phieu_nhap` varchar(5) NOT NULL,
  `id_sach` varchar(5) NOT NULL,
  `so_luong_nhap` int(11) NOT NULL,
  `don_gia_nhap` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `don_hang`
--

CREATE TABLE `don_hang` (
  `id_don_hang` varchar(5) NOT NULL,
  `id_tk` varchar(5) NOT NULL,
  `id_trang_thai` int(11) DEFAULT NULL,
  `ngay_gio_tao_don` datetime NOT NULL,
  `dia_chi_nhan_hang` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `don_hang`
--

INSERT INTO `don_hang` (`id_don_hang`, `id_tk`, `id_trang_thai`, `ngay_gio_tao_don`, `dia_chi_nhan_hang`) VALUES
('DH001', 'KH001', 1, '2025-10-23 10:30:00', '456 Đường ABC, Q1, TP. HCM'),
('DH002', 'KH002', 3, '2025-10-24 11:00:00', '789 Đường XYZ, Q3, TP. HCM');

-- --------------------------------------------------------

--
-- Table structure for table `gia_sach`
--

CREATE TABLE `gia_sach` (
  `id_sach` varchar(5) NOT NULL,
  `ngay_gio_ban` datetime NOT NULL,
  `gia_sach_ban` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gia_sach`
--

INSERT INTO `gia_sach` (`id_sach`, `ngay_gio_ban`, `gia_sach_ban`) VALUES
('S0001', '2025-01-01 00:00:00', 20000),
('S0002', '2025-01-01 00:00:00', 120000),
('S0003', '2025-01-01 00:00:00', 150000),
('S0004', '2025-01-01 00:00:00', 250000),
('S0005', '2025-01-01 00:00:00', 180000),
('S0006', '2025-01-01 00:00:00', 300000),
('S0007', '2025-01-01 00:00:00', 18000),
('S0008', '2025-01-01 00:00:00', 135000),
('S0009', '2025-01-01 00:00:00', 110000),
('S0010', '2025-01-01 00:00:00', 450000),
('S0011', '2025-01-01 00:00:00', 80000),
('S0012', '2025-01-01 00:00:00', 160000),
('S0013', '2025-01-01 00:00:00', 190000),
('S0014', '2025-01-01 00:00:00', 95000);

-- --------------------------------------------------------

--
-- Table structure for table `khuyen_mai`
--

CREATE TABLE `khuyen_mai` (
  `id_km` varchar(5) NOT NULL,
  `ten_km` varchar(250) NOT NULL,
  `phan_tram_km` decimal(5,2) NOT NULL,
  `ngay_bat_dau` datetime NOT NULL,
  `ngay_ket_thuc` datetime NOT NULL,
  `trang_thai_km` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `khuyen_mai`
--

INSERT INTO `khuyen_mai` (`id_km`, `ten_km`, `phan_tram_km`, `ngay_bat_dau`, `ngay_ket_thuc`, `trang_thai_km`) VALUES
('KM000', 'Không khuyến mãi', 0.00, '2020-01-01 00:00:00', '2099-12-31 00:00:00', 'active'),
('KM001', 'Giảm 10% toàn cửa hàng', 10.00, '2025-10-01 00:00:00', '2025-10-31 00:00:00', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `loai_sach`
--

CREATE TABLE `loai_sach` (
  `id_loai` varchar(5) NOT NULL,
  `ten_loai` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `loai_sach`
--

INSERT INTO `loai_sach` (`id_loai`, `ten_loai`) VALUES
('LS001', 'Sách Văn Học'),
('LS002', 'Sách Kinh Tế'),
('LS003', 'Sách Kỹ Năng'),
('LS004', 'Sách Thiếu Nhi');

-- --------------------------------------------------------

--
-- Table structure for table `ngon_ngu`
--

CREATE TABLE `ngon_ngu` (
  `id_ngon_ngu` varchar(5) NOT NULL,
  `ngon_ngu` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ngon_ngu`
--

INSERT INTO `ngon_ngu` (`id_ngon_ngu`, `ngon_ngu`) VALUES
('NN001', 'Tiếng Việt'),
('NN002', 'Tiếng Anh');

-- --------------------------------------------------------

--
-- Table structure for table `nguoi_dung`
--

CREATE TABLE `nguoi_dung` (
  `id_nd` varchar(5) NOT NULL,
  `phan_quyen` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `nguoi_dung`
--

INSERT INTO `nguoi_dung` (`id_nd`, `phan_quyen`) VALUES
('AD', 'admin'),
('KH', 'khach_hang');

-- --------------------------------------------------------

--
-- Table structure for table `nha_cung_cap`
--

CREATE TABLE `nha_cung_cap` (
  `id_ncc` varchar(5) NOT NULL,
  `ten_ncc` varchar(250) NOT NULL,
  `dia_chi_ncc` varchar(250) NOT NULL,
  `sdt_ncc` varchar(15) NOT NULL,
  `email_ncc` varchar(100) NOT NULL,
  `trang_thai_ncc` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `nha_cung_cap`
--

INSERT INTO `nha_cung_cap` (`id_ncc`, `ten_ncc`, `dia_chi_ncc`, `sdt_ncc`, `email_ncc`, `trang_thai_ncc`) VALUES
('NCC01', 'Công ty Fahasa', '123 Lê Lợi, Q1, TPHCM', '02838123456', 'contact@fahasa.com', 'active'),
('NCC02', 'Công ty Phương Nam', '456 Nguyễn Huệ, Q1, TPHCM', '02838654321', 'info@phuongnam.com', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `nxb`
--

CREATE TABLE `nxb` (
  `id_nxb` varchar(5) NOT NULL,
  `ten_nxb` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `nxb`
--

INSERT INTO `nxb` (`id_nxb`, `ten_nxb`) VALUES
('NXB01', 'NXB Trẻ'),
('NXB02', 'NXB Kim Đồng'),
('NXB03', 'NXB Tổng hợp TP.HCM');

-- --------------------------------------------------------

--
-- Table structure for table `phieu_nhap`
--

CREATE TABLE `phieu_nhap` (
  `id_phieu_nhap` varchar(5) NOT NULL,
  `id_ncc` varchar(5) NOT NULL,
  `ngay_lap_phieu_nhap` datetime NOT NULL,
  `tong_tien_nhap` int(11) NOT NULL,
  `VAT` int(11) NOT NULL,
  `tong_gia_tri_phieu_nhap` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `phuong_thuc_thanh_toan`
--

CREATE TABLE `phuong_thuc_thanh_toan` (
  `id_pttt` varchar(5) NOT NULL,
  `ten_pttt` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `phuong_thuc_thanh_toan`
--

INSERT INTO `phuong_thuc_thanh_toan` (`id_pttt`, `ten_pttt`) VALUES
('PT001', 'Thanh toán khi nhận hàng (COD)'),
('PT002', 'Ví điện tử MoMo'),
('PT003', 'Thẻ ngân hàng');

-- --------------------------------------------------------

--
-- Table structure for table `sach`
--

CREATE TABLE `sach` (
  `id_sach` varchar(5) NOT NULL,
  `id_nxb` varchar(5) NOT NULL,
  `id_loai` varchar(5) NOT NULL,
  `id_km` varchar(5) NOT NULL,
  `ten_sach` varchar(100) DEFAULT NULL,
  `mo_ta` text DEFAULT NULL,
  `trang_thai_sach` smallint(6) DEFAULT NULL,
  `so_luong_ton` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sach`
--

INSERT INTO `sach` (`id_sach`, `id_nxb`, `id_loai`, `id_km`, `ten_sach`, `mo_ta`, `trang_thai_sach`, `so_luong_ton`) VALUES
('S0001', 'NXB02', 'LS004', 'KM000', 'Doraemon - Tập 1', 'Truyện tranh về chú mèo máy Doraemon...', 1, 100),
('S0002', 'NXB01', 'LS001', 'KM001', 'Tôi thấy hoa vàng trên cỏ xanh', 'Truyện dài của Nguyễn Nhật Ánh...', 1, 50),
('S0003', 'NXB03', 'LS003', 'KM000', 'Đắc Nhân Tâm', 'Nghệ thuật giao tiếp và ứng xử...', 1, 75),
('S0004', 'NXB01', 'LS002', 'KM000', 'Marketing Căn Bản', 'Nguyên lý Marketing của Philip Kotler...', 1, 30),
('S0005', 'NXB01', 'LS001', 'KM001', 'Harry Potter và Hòn đá Phù thủy', 'Tập 1 của series truyện fantasy nổi tiếng...', 1, 120),
('S0006', 'NXB03', 'LS002', 'KM000', 'Kinh tế học Vĩ mô', 'Sách giáo trình chuyên ngành kinh tế...', 1, 40),
('S0007', 'NXB02', 'LS004', 'KM000', 'Thám tử lừng danh Conan - Tập 1', 'Truyện tranh trinh thám Nhật Bản...', 1, 200),
('S0008', 'NXB03', 'LS003', 'KM001', 'Quẳng Gánh Lo Đi và Vui Sống', 'Nghệ thuật đối mặt với căng thẳng...', 1, 60),
('S0009', 'NXB01', 'LS001', 'KM000', 'Mắt biếc', 'Truyện dài lãng mạn của Nguyễn Nhật Ánh...', 1, 80),
('S0010', 'NXB03', 'LS003', 'KM000', 'English Grammar in Use', 'Sách học ngữ pháp Tiếng Anh cơ bản...', 1, 50),
('S0011', 'NXB01', 'LS001', 'KM001', 'Nhà Giả Kim', 'Tiểu thuyết phiêu lưu của Paulo Coelho...', 1, 100),
('S0012', 'NXB03', 'LS002', 'KM000', 'Cha giàu, Cha nghèo', 'Sách về tư duy tài chính cá nhân...', 1, 90),
('S0013', 'NXB01', 'LS003', 'KM001', 'Atomic Habits (Thói quen nguyên tử)', 'Thay đổi thói quen nhỏ, hiệu quả bất ngờ...', 1, 110),
('S0014', 'NXB01', 'LS001', 'KM000', 'Cây cam ngọt của tôi', 'Câu chuyện cảm động về tuổi thơ...', 1, 70);

-- --------------------------------------------------------

--
-- Table structure for table `s_ncc`
--

CREATE TABLE `s_ncc` (
  `id_sach` varchar(5) NOT NULL,
  `id_ncc` varchar(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `s_ncc`
--

INSERT INTO `s_ncc` (`id_sach`, `id_ncc`) VALUES
('S0001', 'NCC02'),
('S0002', 'NCC01'),
('S0003', 'NCC01'),
('S0004', 'NCC02'),
('S0005', 'NCC01'),
('S0006', 'NCC02'),
('S0007', 'NCC02'),
('S0008', 'NCC01'),
('S0009', 'NCC01'),
('S0010', 'NCC02'),
('S0011', 'NCC01'),
('S0012', 'NCC01'),
('S0013', 'NCC02'),
('S0014', 'NCC01');

-- --------------------------------------------------------

--
-- Table structure for table `s_nns`
--

CREATE TABLE `s_nns` (
  `id_sach` varchar(5) NOT NULL,
  `id_ngon_ngu` varchar(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `s_nns`
--

INSERT INTO `s_nns` (`id_sach`, `id_ngon_ngu`) VALUES
('S0001', 'NN001'),
('S0002', 'NN001'),
('S0003', 'NN001'),
('S0004', 'NN002'),
('S0005', 'NN001'),
('S0006', 'NN001'),
('S0007', 'NN001'),
('S0008', 'NN001'),
('S0009', 'NN001'),
('S0010', 'NN002'),
('S0011', 'NN001'),
('S0012', 'NN001'),
('S0013', 'NN002'),
('S0014', 'NN001');

-- --------------------------------------------------------

--
-- Table structure for table `s_tg`
--

CREATE TABLE `s_tg` (
  `id_sach` varchar(5) NOT NULL,
  `id_tac_gia` varchar(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `s_tg`
--

INSERT INTO `s_tg` (`id_sach`, `id_tac_gia`) VALUES
('S0002', 'TG001'),
('S0003', 'TG003'),
('S0004', 'TG002'),
('S0005', 'TG004'),
('S0006', 'TG002'),
('S0007', 'TG001'),
('S0008', 'TG003'),
('S0009', 'TG001'),
('S0010', 'TG004'),
('S0011', 'TG001'),
('S0012', 'TG003'),
('S0013', 'TG003'),
('S0014', 'TG001');

-- --------------------------------------------------------

--
-- Table structure for table `tac_gia`
--

CREATE TABLE `tac_gia` (
  `id_tac_gia` varchar(5) NOT NULL,
  `ten_tac_gia` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tac_gia`
--

INSERT INTO `tac_gia` (`id_tac_gia`, `ten_tac_gia`) VALUES
('TG001', 'Nguyễn Nhật Ánh'),
('TG002', 'Philip Kotler'),
('TG003', 'Dale Carnegie'),
('TG004', 'J.K. Rowling');

-- --------------------------------------------------------

--
-- Table structure for table `tai_khoan`
--

CREATE TABLE `tai_khoan` (
  `id_tk` varchar(5) NOT NULL,
  `id_nd` varchar(5) NOT NULL,
  `ho_ten` varchar(250) NOT NULL,
  `gioi_tinh` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `sdt` varchar(15) NOT NULL,
  `mat_khau` varchar(100) NOT NULL,
  `dia_chi_giao_hang` varchar(250) NOT NULL,
  `ngay_gio_tao_tk` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tai_khoan`
--

INSERT INTO `tai_khoan` (`id_tk`, `id_nd`, `ho_ten`, `gioi_tinh`, `email`, `sdt`, `mat_khau`, `dia_chi_giao_hang`, `ngay_gio_tao_tk`) VALUES
('AD001', 'AD', 'Tram123', 'Khác', 'admin@qlsach.com', '0000000001', '$2y$10$TfuNpwml2a5EmP8G1uBpPO5VQtpJ.OnRxvs3QvuldOAeaQFHPjg4S', '123 Server, TP. HCM', '2025-10-20 21:51:53'),
('AD002', 'AD', 'Thuong123', 'Khác', 'admin_kho@qlsach.com', '0000000002', '$2y$10$TfuNpwml2a5EmP8G1uBpPO5VQtpJ.OnRxvs3QvuldOAeaQFHPjg4S', '123 Server, TP. HCM', '2025-10-20 21:51:53'),
('AD003', 'AD', 'Giang123', 'Khác', 'admin_content@qlsach.com', '0000000003', '$2y$10$TfuNpwml2a5EmP8G1uBpPO5VQtpJ.OnRxvs3QvuldOAeaQFHPjg4S', '123 Server, TP. HCM', '2025-10-20 21:51:53'),
('KH001', 'KH', 'Nguyễn Văn A', 'Nam', 'khach1@gmail.com', '0901000111', '$2y$10$f4e.9UWG5FwITxwjLnG8uO7HLlgA4VkfYBlTwzW9hpldIFACj.QGO', '456 Đường ABC, Q1, TP. HCM', '2025-10-20 21:51:53'),
('KH002', 'KH', 'Trần Thị B', 'Nữ', 'khach2@gmail.com', '0902000222', '$2y$10$f4e.9UWG5FwITxwjLnG8uO7HLlgA4VkfYBlTwzW9hpldIFACj.QGO', '789 Đường XYZ, Q3, TP. HCM', '2025-10-20 21:51:53'),
('TK221', 'KH', 'tran ngoc lam', 'Khác', 'lam@gmail.com', '012345', '$2y$10$Z8LWwS9pmbjBZtu80eeyeeekG0lGqYHwzpXr4hu6o9gtoaE5K2wUG', 'Cần Thơ', '2025-10-20 21:58:04');

-- --------------------------------------------------------

--
-- Table structure for table `thanh_toan`
--

CREATE TABLE `thanh_toan` (
  `id_pttt` varchar(5) NOT NULL,
  `id_don_hang` varchar(5) NOT NULL,
  `trang_thai_tt` smallint(6) NOT NULL,
  `ngay_gio_thanh_toan` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `thanh_toan`
--

INSERT INTO `thanh_toan` (`id_pttt`, `id_don_hang`, `trang_thai_tt`, `ngay_gio_thanh_toan`) VALUES
('PT001', 'DH001', 0, '2025-10-23 10:30:00'),
('PT002', 'DH002', 1, '2025-10-24 11:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `the_loai`
--

CREATE TABLE `the_loai` (
  `id_the_loai` varchar(5) NOT NULL,
  `id_loai` varchar(5) NOT NULL,
  `ten_the_loai` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `the_loai`
--

INSERT INTO `the_loai` (`id_the_loai`, `id_loai`, `ten_the_loai`) VALUES
('TL001', 'LS001', 'Tiểu thuyết'),
('TL002', 'LS001', 'Truyện ngắn'),
('TL003', 'LS002', 'Marketing'),
('TL004', 'LS002', 'Quản trị'),
('TL005', 'LS003', 'Giao tiếp'),
('TL006', 'LS004', 'Truyện cổ tích');

-- --------------------------------------------------------

--
-- Table structure for table `thoi_diem`
--

CREATE TABLE `thoi_diem` (
  `ngay_gio_ban` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `thoi_diem`
--

INSERT INTO `thoi_diem` (`ngay_gio_ban`) VALUES
('2025-01-01 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `trang_thai_don_hang`
--

CREATE TABLE `trang_thai_don_hang` (
  `id_trang_thai` int(11) NOT NULL,
  `trang_thai_dh` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trang_thai_don_hang`
--

INSERT INTO `trang_thai_don_hang` (`id_trang_thai`, `trang_thai_dh`) VALUES
(1, 'Chờ xử lý'),
(2, 'Đã xác nhận'),
(3, 'Đang giao hàng'),
(4, 'Đã giao thành công'),
(5, 'Đã hủy');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `binh_luan`
--
ALTER TABLE `binh_luan`
  ADD PRIMARY KEY (`id_bl`),
  ADD KEY `id_sach` (`id_sach`);

--
-- Indexes for table `chi_tiet_don_hang`
--
ALTER TABLE `chi_tiet_don_hang`
  ADD PRIMARY KEY (`id_don_hang`,`id_sach`),
  ADD KEY `id_sach` (`id_sach`);

--
-- Indexes for table `chi_tiet_phieu_nhap`
--
ALTER TABLE `chi_tiet_phieu_nhap`
  ADD PRIMARY KEY (`id_phieu_nhap`,`id_sach`),
  ADD KEY `id_sach` (`id_sach`);

--
-- Indexes for table `don_hang`
--
ALTER TABLE `don_hang`
  ADD PRIMARY KEY (`id_don_hang`),
  ADD KEY `id_tk` (`id_tk`),
  ADD KEY `id_trang_thai` (`id_trang_thai`);

--
-- Indexes for table `gia_sach`
--
ALTER TABLE `gia_sach`
  ADD PRIMARY KEY (`id_sach`,`ngay_gio_ban`),
  ADD KEY `ngay_gio_ban` (`ngay_gio_ban`);

--
-- Indexes for table `khuyen_mai`
--
ALTER TABLE `khuyen_mai`
  ADD PRIMARY KEY (`id_km`);

--
-- Indexes for table `loai_sach`
--
ALTER TABLE `loai_sach`
  ADD PRIMARY KEY (`id_loai`);

--
-- Indexes for table `ngon_ngu`
--
ALTER TABLE `ngon_ngu`
  ADD PRIMARY KEY (`id_ngon_ngu`);

--
-- Indexes for table `nguoi_dung`
--
ALTER TABLE `nguoi_dung`
  ADD PRIMARY KEY (`id_nd`);

--
-- Indexes for table `nha_cung_cap`
--
ALTER TABLE `nha_cung_cap`
  ADD PRIMARY KEY (`id_ncc`);

--
-- Indexes for table `nxb`
--
ALTER TABLE `nxb`
  ADD PRIMARY KEY (`id_nxb`);

--
-- Indexes for table `phieu_nhap`
--
ALTER TABLE `phieu_nhap`
  ADD PRIMARY KEY (`id_phieu_nhap`),
  ADD KEY `id_ncc` (`id_ncc`);

--
-- Indexes for table `phuong_thuc_thanh_toan`
--
ALTER TABLE `phuong_thuc_thanh_toan`
  ADD PRIMARY KEY (`id_pttt`);

--
-- Indexes for table `sach`
--
ALTER TABLE `sach`
  ADD PRIMARY KEY (`id_sach`),
  ADD KEY `id_loai` (`id_loai`),
  ADD KEY `id_nxb` (`id_nxb`),
  ADD KEY `id_km` (`id_km`);

--
-- Indexes for table `s_ncc`
--
ALTER TABLE `s_ncc`
  ADD PRIMARY KEY (`id_sach`,`id_ncc`),
  ADD KEY `id_ncc` (`id_ncc`);

--
-- Indexes for table `s_nns`
--
ALTER TABLE `s_nns`
  ADD PRIMARY KEY (`id_sach`,`id_ngon_ngu`),
  ADD KEY `id_ngon_ngu` (`id_ngon_ngu`);

--
-- Indexes for table `s_tg`
--
ALTER TABLE `s_tg`
  ADD PRIMARY KEY (`id_sach`,`id_tac_gia`),
  ADD KEY `id_tac_gia` (`id_tac_gia`);

--
-- Indexes for table `tac_gia`
--
ALTER TABLE `tac_gia`
  ADD PRIMARY KEY (`id_tac_gia`);

--
-- Indexes for table `tai_khoan`
--
ALTER TABLE `tai_khoan`
  ADD PRIMARY KEY (`id_tk`),
  ADD KEY `id_nd` (`id_nd`);

--
-- Indexes for table `thanh_toan`
--
ALTER TABLE `thanh_toan`
  ADD PRIMARY KEY (`id_pttt`,`id_don_hang`),
  ADD KEY `id_don_hang` (`id_don_hang`);

--
-- Indexes for table `the_loai`
--
ALTER TABLE `the_loai`
  ADD PRIMARY KEY (`id_the_loai`),
  ADD KEY `id_loai` (`id_loai`);

--
-- Indexes for table `thoi_diem`
--
ALTER TABLE `thoi_diem`
  ADD PRIMARY KEY (`ngay_gio_ban`);

--
-- Indexes for table `trang_thai_don_hang`
--
ALTER TABLE `trang_thai_don_hang`
  ADD PRIMARY KEY (`id_trang_thai`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `trang_thai_don_hang`
--
ALTER TABLE `trang_thai_don_hang`
  MODIFY `id_trang_thai` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `binh_luan`
--
ALTER TABLE `binh_luan`
  ADD CONSTRAINT `binh_luan_ibfk_1` FOREIGN KEY (`id_sach`) REFERENCES `sach` (`id_sach`);

--
-- Constraints for table `chi_tiet_don_hang`
--
ALTER TABLE `chi_tiet_don_hang`
  ADD CONSTRAINT `chi_tiet_don_hang_ibfk_1` FOREIGN KEY (`id_don_hang`) REFERENCES `don_hang` (`id_don_hang`),
  ADD CONSTRAINT `chi_tiet_don_hang_ibfk_2` FOREIGN KEY (`id_sach`) REFERENCES `sach` (`id_sach`);

--
-- Constraints for table `chi_tiet_phieu_nhap`
--
ALTER TABLE `chi_tiet_phieu_nhap`
  ADD CONSTRAINT `chi_tiet_phieu_nhap_ibfk_1` FOREIGN KEY (`id_phieu_nhap`) REFERENCES `phieu_nhap` (`id_phieu_nhap`),
  ADD CONSTRAINT `chi_tiet_phieu_nhap_ibfk_2` FOREIGN KEY (`id_sach`) REFERENCES `sach` (`id_sach`);

--
-- Constraints for table `don_hang`
--
ALTER TABLE `don_hang`
  ADD CONSTRAINT `don_hang_ibfk_1` FOREIGN KEY (`id_tk`) REFERENCES `tai_khoan` (`id_tk`),
  ADD CONSTRAINT `don_hang_ibfk_2` FOREIGN KEY (`id_trang_thai`) REFERENCES `trang_thai_don_hang` (`id_trang_thai`);

--
-- Constraints for table `gia_sach`
--
ALTER TABLE `gia_sach`
  ADD CONSTRAINT `gia_sach_ibfk_1` FOREIGN KEY (`id_sach`) REFERENCES `sach` (`id_sach`),
  ADD CONSTRAINT `gia_sach_ibfk_2` FOREIGN KEY (`ngay_gio_ban`) REFERENCES `thoi_diem` (`ngay_gio_ban`);

--
-- Constraints for table `phieu_nhap`
--
ALTER TABLE `phieu_nhap`
  ADD CONSTRAINT `phieu_nhap_ibfk_1` FOREIGN KEY (`id_ncc`) REFERENCES `nha_cung_cap` (`id_ncc`);

--
-- Constraints for table `sach`
--
ALTER TABLE `sach`
  ADD CONSTRAINT `sach_ibfk_1` FOREIGN KEY (`id_loai`) REFERENCES `loai_sach` (`id_loai`),
  ADD CONSTRAINT `sach_ibfk_2` FOREIGN KEY (`id_nxb`) REFERENCES `nxb` (`id_nxb`),
  ADD CONSTRAINT `sach_ibfk_3` FOREIGN KEY (`id_km`) REFERENCES `khuyen_mai` (`id_km`);

--
-- Constraints for table `s_ncc`
--
ALTER TABLE `s_ncc`
  ADD CONSTRAINT `s_ncc_ibfk_1` FOREIGN KEY (`id_sach`) REFERENCES `sach` (`id_sach`),
  ADD CONSTRAINT `s_ncc_ibfk_2` FOREIGN KEY (`id_ncc`) REFERENCES `nha_cung_cap` (`id_ncc`);

--
-- Constraints for table `s_nns`
--
ALTER TABLE `s_nns`
  ADD CONSTRAINT `s_nns_ibfk_1` FOREIGN KEY (`id_sach`) REFERENCES `sach` (`id_sach`),
  ADD CONSTRAINT `s_nns_ibfk_2` FOREIGN KEY (`id_ngon_ngu`) REFERENCES `ngon_ngu` (`id_ngon_ngu`);

--
-- Constraints for table `s_tg`
--
ALTER TABLE `s_tg`
  ADD CONSTRAINT `s_tg_ibfk_1` FOREIGN KEY (`id_sach`) REFERENCES `sach` (`id_sach`),
  ADD CONSTRAINT `s_tg_ibfk_2` FOREIGN KEY (`id_tac_gia`) REFERENCES `tac_gia` (`id_tac_gia`);

--
-- Constraints for table `tai_khoan`
--
ALTER TABLE `tai_khoan`
  ADD CONSTRAINT `tai_khoan_ibfk_1` FOREIGN KEY (`id_nd`) REFERENCES `nguoi_dung` (`id_nd`);

--
-- Constraints for table `thanh_toan`
--
ALTER TABLE `thanh_toan`
  ADD CONSTRAINT `thanh_toan_ibfk_1` FOREIGN KEY (`id_pttt`) REFERENCES `phuong_thuc_thanh_toan` (`id_pttt`),
  ADD CONSTRAINT `thanh_toan_ibfk_2` FOREIGN KEY (`id_don_hang`) REFERENCES `don_hang` (`id_don_hang`);

--
-- Constraints for table `the_loai`
--
ALTER TABLE `the_loai`
  ADD CONSTRAINT `the_loai_ibfk_1` FOREIGN KEY (`id_loai`) REFERENCES `loai_sach` (`id_loai`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
