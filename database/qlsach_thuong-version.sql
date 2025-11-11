-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th10 11, 2025 lúc 07:43 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `qlsach`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `binh_luan`
--

CREATE TABLE `binh_luan` (
  `id_bl` varchar(5) NOT NULL,
  `id_sach` varchar(5) NOT NULL,
  `binh_luan` text NOT NULL,
  `so_sao` float(6,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chi_tiet_don_hang`
--

CREATE TABLE `chi_tiet_don_hang` (
  `id_don_hang` varchar(5) NOT NULL,
  `id_sach` varchar(5) NOT NULL,
  `so_luong_ban` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chi_tiet_phieu_nhap`
--

CREATE TABLE `chi_tiet_phieu_nhap` (
  `id_phieu_nhap` varchar(5) NOT NULL,
  `id_sach` varchar(5) NOT NULL,
  `so_luong_nhap` int(11) NOT NULL,
  `don_gia_nhap` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `don_hang`
--

CREATE TABLE `don_hang` (
  `id_don_hang` varchar(5) NOT NULL,
  `id_tk` varchar(5) NOT NULL,
  `id_trang_thai` int(11) DEFAULT NULL,
  `ngay_gio_tao_don` datetime NOT NULL,
  `dia_chi_nhan_hang` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `gia_sach`
--

CREATE TABLE `gia_sach` (
  `id_sach` varchar(5) NOT NULL,
  `ngay_gio_ban` datetime NOT NULL,
  `gia_sach_ban` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `gia_sach`
--

INSERT INTO `gia_sach` (`id_sach`, `ngay_gio_ban`, `gia_sach_ban`) VALUES
('S0001', '2025-11-11 13:25:01', 13000),
('S0002', '2025-11-11 13:33:00', 14000);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `khuyen_mai`
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
-- Đang đổ dữ liệu cho bảng `khuyen_mai`
--

INSERT INTO `khuyen_mai` (`id_km`, `ten_km`, `phan_tram_km`, `ngay_bat_dau`, `ngay_ket_thuc`, `trang_thai_km`) VALUES
('KM001', 'Khuyến mãi test', 1.00, '2025-11-11 13:19:00', '2025-12-31 13:19:00', 'active');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `loai_sach`
--

CREATE TABLE `loai_sach` (
  `id_loai` varchar(5) NOT NULL,
  `ten_loai` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `loai_sach`
--

INSERT INTO `loai_sach` (`id_loai`, `ten_loai`) VALUES
('LS001', 'Sách giáo khoa 1');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `ngon_ngu`
--

CREATE TABLE `ngon_ngu` (
  `id_ngon_ngu` varchar(5) NOT NULL,
  `ngon_ngu` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `ngon_ngu`
--

INSERT INTO `ngon_ngu` (`id_ngon_ngu`, `ngon_ngu`) VALUES
('NN001', 'Tiếng Việt'),
('NN002', 'Tiếng Anh');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nguoi_dung`
--

CREATE TABLE `nguoi_dung` (
  `id_nd` varchar(5) NOT NULL,
  `phan_quyen` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `nguoi_dung`
--

INSERT INTO `nguoi_dung` (`id_nd`, `phan_quyen`) VALUES
('AD', 'admin'),
('KH', 'khach_hang');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nha_cung_cap`
--

CREATE TABLE `nha_cung_cap` (
  `id_ncc` varchar(5) NOT NULL,
  `ten_ncc` varchar(250) NOT NULL,
  `dia_chi_ncc` varchar(250) NOT NULL,
  `sdt_ncc` varchar(15) NOT NULL,
  `email_ncc` varchar(100) NOT NULL,
  `trang_thai_ncc` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nxb`
--

CREATE TABLE `nxb` (
  `id_nxb` varchar(5) NOT NULL,
  `ten_nxb` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `nxb`
--

INSERT INTO `nxb` (`id_nxb`, `ten_nxb`) VALUES
('NXB01', 'NXB Giáo dục');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `phieu_nhap`
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
-- Cấu trúc bảng cho bảng `phuong_thuc_thanh_toan`
--

CREATE TABLE `phuong_thuc_thanh_toan` (
  `id_pttt` varchar(5) NOT NULL,
  `ten_pttt` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `sach`
--

CREATE TABLE `sach` (
  `id_sach` varchar(5) NOT NULL,
  `id_nxb` varchar(5) NOT NULL,
  `id_the_loai` varchar(5) DEFAULT NULL,
  `id_km` varchar(5) NOT NULL,
  `ten_sach` varchar(100) DEFAULT NULL,
  `mo_ta` text DEFAULT NULL,
  `trang_thai_sach` smallint(6) DEFAULT NULL,
  `so_luong_ton` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `sach`
--

INSERT INTO `sach` (`id_sach`, `id_nxb`, `id_the_loai`, `id_km`, `ten_sach`, `mo_ta`, `trang_thai_sach`, `so_luong_ton`) VALUES
('S0001', 'NXB01', NULL, 'KM001', 'Sách testing', 'thử nghiệm', 0, 50),
('S0002', 'NXB01', 'TL001', 'KM001', 'Sách newtest', 'adadada', 1, 60);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `s_ncc`
--

CREATE TABLE `s_ncc` (
  `id_sach` varchar(5) NOT NULL,
  `id_ncc` varchar(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `s_nns`
--

CREATE TABLE `s_nns` (
  `id_sach` varchar(5) NOT NULL,
  `id_ngon_ngu` varchar(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `s_nns`
--

INSERT INTO `s_nns` (`id_sach`, `id_ngon_ngu`) VALUES
('S0001', 'NN001'),
('S0001', 'NN002'),
('S0002', 'NN001'),
('S0002', 'NN002');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `s_tg`
--

CREATE TABLE `s_tg` (
  `id_sach` varchar(5) NOT NULL,
  `id_tac_gia` varchar(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `s_tg`
--

INSERT INTO `s_tg` (`id_sach`, `id_tac_gia`) VALUES
('S0001', 'TG001'),
('S0001', 'TG002'),
('S0002', 'TG001'),
('S0002', 'TG002');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tac_gia`
--

CREATE TABLE `tac_gia` (
  `id_tac_gia` varchar(5) NOT NULL,
  `ten_tac_gia` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `tac_gia`
--

INSERT INTO `tac_gia` (`id_tac_gia`, `ten_tac_gia`) VALUES
('TG001', 'Testing'),
('TG002', 'Thương');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tai_khoan`
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
-- Đang đổ dữ liệu cho bảng `tai_khoan`
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
-- Cấu trúc bảng cho bảng `thanh_toan`
--

CREATE TABLE `thanh_toan` (
  `id_pttt` varchar(5) NOT NULL,
  `id_don_hang` varchar(5) NOT NULL,
  `trang_thai_tt` smallint(6) NOT NULL,
  `ngay_gio_thanh_toan` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `the_loai`
--

CREATE TABLE `the_loai` (
  `id_the_loai` varchar(5) NOT NULL,
  `id_loai` varchar(5) NOT NULL,
  `ten_the_loai` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `the_loai`
--

INSERT INTO `the_loai` (`id_the_loai`, `id_loai`, `ten_the_loai`) VALUES
('TL001', 'LS001', 'Ngữ văn 6');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `thoi_diem`
--

CREATE TABLE `thoi_diem` (
  `ngay_gio_ban` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `thoi_diem`
--

INSERT INTO `thoi_diem` (`ngay_gio_ban`) VALUES
('2025-11-11 13:25:01'),
('2025-11-11 13:33:00');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `trang_thai_don_hang`
--

CREATE TABLE `trang_thai_don_hang` (
  `id_trang_thai` int(11) NOT NULL,
  `trang_thai_dh` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `binh_luan`
--
ALTER TABLE `binh_luan`
  ADD PRIMARY KEY (`id_bl`),
  ADD KEY `id_sach` (`id_sach`);

--
-- Chỉ mục cho bảng `chi_tiet_don_hang`
--
ALTER TABLE `chi_tiet_don_hang`
  ADD PRIMARY KEY (`id_don_hang`,`id_sach`),
  ADD KEY `id_sach` (`id_sach`);

--
-- Chỉ mục cho bảng `chi_tiet_phieu_nhap`
--
ALTER TABLE `chi_tiet_phieu_nhap`
  ADD PRIMARY KEY (`id_phieu_nhap`,`id_sach`),
  ADD KEY `id_sach` (`id_sach`);

--
-- Chỉ mục cho bảng `don_hang`
--
ALTER TABLE `don_hang`
  ADD PRIMARY KEY (`id_don_hang`),
  ADD KEY `id_tk` (`id_tk`),
  ADD KEY `id_trang_thai` (`id_trang_thai`);

--
-- Chỉ mục cho bảng `gia_sach`
--
ALTER TABLE `gia_sach`
  ADD PRIMARY KEY (`id_sach`,`ngay_gio_ban`),
  ADD KEY `ngay_gio_ban` (`ngay_gio_ban`);

--
-- Chỉ mục cho bảng `khuyen_mai`
--
ALTER TABLE `khuyen_mai`
  ADD PRIMARY KEY (`id_km`);

--
-- Chỉ mục cho bảng `loai_sach`
--
ALTER TABLE `loai_sach`
  ADD PRIMARY KEY (`id_loai`);

--
-- Chỉ mục cho bảng `ngon_ngu`
--
ALTER TABLE `ngon_ngu`
  ADD PRIMARY KEY (`id_ngon_ngu`);

--
-- Chỉ mục cho bảng `nguoi_dung`
--
ALTER TABLE `nguoi_dung`
  ADD PRIMARY KEY (`id_nd`);

--
-- Chỉ mục cho bảng `nha_cung_cap`
--
ALTER TABLE `nha_cung_cap`
  ADD PRIMARY KEY (`id_ncc`);

--
-- Chỉ mục cho bảng `nxb`
--
ALTER TABLE `nxb`
  ADD PRIMARY KEY (`id_nxb`);

--
-- Chỉ mục cho bảng `phieu_nhap`
--
ALTER TABLE `phieu_nhap`
  ADD PRIMARY KEY (`id_phieu_nhap`),
  ADD KEY `id_ncc` (`id_ncc`);

--
-- Chỉ mục cho bảng `phuong_thuc_thanh_toan`
--
ALTER TABLE `phuong_thuc_thanh_toan`
  ADD PRIMARY KEY (`id_pttt`);

--
-- Chỉ mục cho bảng `sach`
--
ALTER TABLE `sach`
  ADD PRIMARY KEY (`id_sach`),
  ADD KEY `id_nxb` (`id_nxb`),
  ADD KEY `id_km` (`id_km`),
  ADD KEY `fk_sach_the_loai` (`id_the_loai`);

--
-- Chỉ mục cho bảng `s_ncc`
--
ALTER TABLE `s_ncc`
  ADD PRIMARY KEY (`id_sach`,`id_ncc`),
  ADD KEY `id_ncc` (`id_ncc`);

--
-- Chỉ mục cho bảng `s_nns`
--
ALTER TABLE `s_nns`
  ADD PRIMARY KEY (`id_sach`,`id_ngon_ngu`),
  ADD KEY `id_ngon_ngu` (`id_ngon_ngu`);

--
-- Chỉ mục cho bảng `s_tg`
--
ALTER TABLE `s_tg`
  ADD PRIMARY KEY (`id_sach`,`id_tac_gia`),
  ADD KEY `id_tac_gia` (`id_tac_gia`);

--
-- Chỉ mục cho bảng `tac_gia`
--
ALTER TABLE `tac_gia`
  ADD PRIMARY KEY (`id_tac_gia`);

--
-- Chỉ mục cho bảng `tai_khoan`
--
ALTER TABLE `tai_khoan`
  ADD PRIMARY KEY (`id_tk`),
  ADD KEY `id_nd` (`id_nd`);

--
-- Chỉ mục cho bảng `thanh_toan`
--
ALTER TABLE `thanh_toan`
  ADD PRIMARY KEY (`id_pttt`,`id_don_hang`),
  ADD KEY `id_don_hang` (`id_don_hang`);

--
-- Chỉ mục cho bảng `the_loai`
--
ALTER TABLE `the_loai`
  ADD PRIMARY KEY (`id_the_loai`),
  ADD KEY `id_loai` (`id_loai`);

--
-- Chỉ mục cho bảng `thoi_diem`
--
ALTER TABLE `thoi_diem`
  ADD PRIMARY KEY (`ngay_gio_ban`);

--
-- Chỉ mục cho bảng `trang_thai_don_hang`
--
ALTER TABLE `trang_thai_don_hang`
  ADD PRIMARY KEY (`id_trang_thai`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `trang_thai_don_hang`
--
ALTER TABLE `trang_thai_don_hang`
  MODIFY `id_trang_thai` int(11) NOT NULL AUTO_INCREMENT;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `binh_luan`
--
ALTER TABLE `binh_luan`
  ADD CONSTRAINT `binh_luan_ibfk_1` FOREIGN KEY (`id_sach`) REFERENCES `sach` (`id_sach`);

--
-- Các ràng buộc cho bảng `chi_tiet_don_hang`
--
ALTER TABLE `chi_tiet_don_hang`
  ADD CONSTRAINT `chi_tiet_don_hang_ibfk_1` FOREIGN KEY (`id_don_hang`) REFERENCES `don_hang` (`id_don_hang`),
  ADD CONSTRAINT `chi_tiet_don_hang_ibfk_2` FOREIGN KEY (`id_sach`) REFERENCES `sach` (`id_sach`);

--
-- Các ràng buộc cho bảng `chi_tiet_phieu_nhap`
--
ALTER TABLE `chi_tiet_phieu_nhap`
  ADD CONSTRAINT `chi_tiet_phieu_nhap_ibfk_1` FOREIGN KEY (`id_phieu_nhap`) REFERENCES `phieu_nhap` (`id_phieu_nhap`),
  ADD CONSTRAINT `chi_tiet_phieu_nhap_ibfk_2` FOREIGN KEY (`id_sach`) REFERENCES `sach` (`id_sach`);

--
-- Các ràng buộc cho bảng `don_hang`
--
ALTER TABLE `don_hang`
  ADD CONSTRAINT `don_hang_ibfk_1` FOREIGN KEY (`id_tk`) REFERENCES `tai_khoan` (`id_tk`),
  ADD CONSTRAINT `don_hang_ibfk_2` FOREIGN KEY (`id_trang_thai`) REFERENCES `trang_thai_don_hang` (`id_trang_thai`);

--
-- Các ràng buộc cho bảng `gia_sach`
--
ALTER TABLE `gia_sach`
  ADD CONSTRAINT `gia_sach_ibfk_1` FOREIGN KEY (`id_sach`) REFERENCES `sach` (`id_sach`),
  ADD CONSTRAINT `gia_sach_ibfk_2` FOREIGN KEY (`ngay_gio_ban`) REFERENCES `thoi_diem` (`ngay_gio_ban`);

--
-- Các ràng buộc cho bảng `phieu_nhap`
--
ALTER TABLE `phieu_nhap`
  ADD CONSTRAINT `phieu_nhap_ibfk_1` FOREIGN KEY (`id_ncc`) REFERENCES `nha_cung_cap` (`id_ncc`);

--
-- Các ràng buộc cho bảng `sach`
--
ALTER TABLE `sach`
  ADD CONSTRAINT `fk_sach_the_loai` FOREIGN KEY (`id_the_loai`) REFERENCES `the_loai` (`id_the_loai`),
  ADD CONSTRAINT `sach_ibfk_2` FOREIGN KEY (`id_nxb`) REFERENCES `nxb` (`id_nxb`),
  ADD CONSTRAINT `sach_ibfk_3` FOREIGN KEY (`id_km`) REFERENCES `khuyen_mai` (`id_km`);

--
-- Các ràng buộc cho bảng `s_ncc`
--
ALTER TABLE `s_ncc`
  ADD CONSTRAINT `s_ncc_ibfk_1` FOREIGN KEY (`id_sach`) REFERENCES `sach` (`id_sach`),
  ADD CONSTRAINT `s_ncc_ibfk_2` FOREIGN KEY (`id_ncc`) REFERENCES `nha_cung_cap` (`id_ncc`);

--
-- Các ràng buộc cho bảng `s_nns`
--
ALTER TABLE `s_nns`
  ADD CONSTRAINT `s_nns_ibfk_1` FOREIGN KEY (`id_sach`) REFERENCES `sach` (`id_sach`),
  ADD CONSTRAINT `s_nns_ibfk_2` FOREIGN KEY (`id_ngon_ngu`) REFERENCES `ngon_ngu` (`id_ngon_ngu`);

--
-- Các ràng buộc cho bảng `s_tg`
--
ALTER TABLE `s_tg`
  ADD CONSTRAINT `s_tg_ibfk_1` FOREIGN KEY (`id_sach`) REFERENCES `sach` (`id_sach`),
  ADD CONSTRAINT `s_tg_ibfk_2` FOREIGN KEY (`id_tac_gia`) REFERENCES `tac_gia` (`id_tac_gia`);

--
-- Các ràng buộc cho bảng `tai_khoan`
--
ALTER TABLE `tai_khoan`
  ADD CONSTRAINT `tai_khoan_ibfk_1` FOREIGN KEY (`id_nd`) REFERENCES `nguoi_dung` (`id_nd`);

--
-- Các ràng buộc cho bảng `thanh_toan`
--
ALTER TABLE `thanh_toan`
  ADD CONSTRAINT `thanh_toan_ibfk_1` FOREIGN KEY (`id_pttt`) REFERENCES `phuong_thuc_thanh_toan` (`id_pttt`),
  ADD CONSTRAINT `thanh_toan_ibfk_2` FOREIGN KEY (`id_don_hang`) REFERENCES `don_hang` (`id_don_hang`);

--
-- Các ràng buộc cho bảng `the_loai`
--
ALTER TABLE `the_loai`
  ADD CONSTRAINT `the_loai_ibfk_1` FOREIGN KEY (`id_loai`) REFERENCES `loai_sach` (`id_loai`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
