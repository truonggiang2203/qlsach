-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th10 21, 2025 lúc 08:47 AM
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
  `id_tk` varchar(5) DEFAULT NULL,
  `binh_luan` text NOT NULL,
  `so_sao` float(6,2) NOT NULL,
  `ngay_gio_tao` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `binh_luan`
--

INSERT INTO `binh_luan` (`id_bl`, `id_sach`, `id_tk`, `binh_luan`, `so_sao`, `ngay_gio_tao`) VALUES
('BL176', 'S0001', 'TK727', 'hi', 5.00, '2025-11-18 12:56:33');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chi_tiet_don_hang`
--

CREATE TABLE `chi_tiet_don_hang` (
  `id_don_hang` varchar(20) NOT NULL,
  `id_sach` varchar(5) NOT NULL,
  `so_luong_ban` int(11) NOT NULL,
  `don_gia_ban` decimal(10,0) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `chi_tiet_don_hang`
--

INSERT INTO `chi_tiet_don_hang` (`id_don_hang`, `id_sach`, `so_luong_ban`, `don_gia_ban`) VALUES
('DH176', 'S0018', 1, 25000),
('DH176314357146', 'S0002', 1, 68000),
('DH176336033944', 'S0001', 1, 120000),
('DH176343145325', 'S0001', 1, 120000),
('DH176343148321', 'S0001', 3, 120000),
('DH176343154352', 'S0001', 2, 120000),
('DH176343189365', 'S0001', 1, 120000),
('DH176343189365', 'S0002', 1, 68000),
('DH176343203235', 'S0001', 1, 120000),
('DH176343216769', 'S0001', 1, 120000),
('DH176343312629', 'S0001', 2, 120000),
('DH176344232284', 'S0001', 1, 120000),
('DH176344248427', 'S0001', 1, 120000),
('DH176344308023', 'S0001', 1, 120000),
('DH176344522644', 'S0001', 1, 120000),
('DH176344522644', 'S0002', 1, 68000);

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
  `id_don_hang` varchar(20) NOT NULL,
  `id_tk` varchar(5) NOT NULL,
  `id_trang_thai` int(11) DEFAULT NULL,
  `ngay_gio_tao_don` datetime NOT NULL,
  `dia_chi_nhan_hang` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `don_hang`
--

INSERT INTO `don_hang` (`id_don_hang`, `id_tk`, `id_trang_thai`, `ngay_gio_tao_don`, `dia_chi_nhan_hang`) VALUES
('DH176', 'TK137', 5, '2025-11-15 00:53:42', 'CT'),
('DH176314357146', 'TK137', 1, '2025-11-15 01:06:11', 'CT'),
('DH176336033944', 'TK727', 1, '2025-11-17 13:18:59', 'Sóc Trăng'),
('DH176343145325', 'TK727', 1, '2025-11-18 09:04:13', '.'),
('DH176343148321', 'TK727', 1, '2025-11-18 09:04:43', 'Sóc Trăng'),
('DH176343154352', 'TK727', 1, '2025-11-18 09:05:43', '.'),
('DH176343189365', 'TK727', 1, '2025-11-18 09:11:33', '.'),
('DH176343203235', 'TK727', 1, '2025-11-18 09:13:52', '.'),
('DH176343216769', 'TK727', 1, '2025-11-18 09:16:07', '.'),
('DH176343312629', 'TK727', 1, '2025-11-18 09:32:06', 'Sóc Trăng'),
('DH176344232284', 'TK727', 1, '2025-11-18 12:05:22', 'Sóc Trăng'),
('DH176344248427', 'TK727', 1, '2025-11-18 12:08:04', '.'),
('DH176344308023', 'TK727', 1, '2025-11-18 12:18:00', 'Sóc Trăng'),
('DH176344522644', 'TK727', 1, '2025-11-18 12:53:46', 'Sóc Trăng');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `gia_sach`
--

CREATE TABLE `gia_sach` (
  `id_sach` varchar(5) NOT NULL,
  `tg_gia_bd` datetime NOT NULL,
  `gia_sach_ban` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `gia_sach`
--

INSERT INTO `gia_sach` (`id_sach`, `tg_gia_bd`, `gia_sach_ban`) VALUES
('S0001', '2025-11-14 00:00:00', 120000),
('S0002', '2025-11-14 00:00:00', 80000),
('S0003', '2025-11-14 00:00:00', 90000),
('S0004', '2025-11-14 00:00:00', 150000),
('S0005', '2025-11-21 14:28:35', 100000),
('S0006', '2025-11-14 00:00:00', 135000),
('S0007', '2025-11-21 14:28:35', 100000),
('S0008', '2025-11-21 14:28:35', 100000),
('S0009', '2025-11-21 14:28:35', 100000),
('S0010', '2025-11-21 14:28:35', 100000),
('S0011', '2025-11-14 00:00:00', 99000),
('S0012', '2025-11-21 14:28:35', 100000),
('S0013', '2025-11-21 14:28:35', 100000),
('S0014', '2025-11-21 14:28:35', 100000),
('S0015', '2025-11-21 14:28:35', 100000),
('S0016', '2025-11-14 00:00:00', 75000),
('S0017', '2025-11-21 14:28:35', 100000),
('S0018', '2025-11-14 00:00:00', 25000),
('S0019', '2025-11-21 14:28:35', 100000),
('S0020', '2025-11-21 14:28:35', 100000),
('S0021', '2025-11-14 00:00:00', 190000),
('S0022', '2025-11-21 14:28:35', 100000),
('S0023', '2025-11-21 14:28:35', 100000),
('S0024', '2025-11-21 14:28:35', 100000),
('S0025', '2025-11-14 00:00:00', 145000);

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
('KM000', 'Không khuyến mãi', 0.00, '2020-01-01 00:00:00', '2099-12-31 23:59:59', 'active'),
('KM001', 'Khuyến mãi test', 1.00, '2025-11-11 13:19:00', '2025-12-31 13:19:00', 'active'),
('KM002', 'Giảm giá Black Friday', 20.00, '2025-11-20 00:00:00', '2025-11-30 23:59:59', 'active'),
('KM003', 'Chào hè rực rỡ', 15.00, '2025-06-01 00:00:00', '2025-06-30 23:59:59', 'active'),
('KM004', 'Xả hàng tồn kho', 50.00, '2025-11-10 00:00:00', '2025-11-20 23:59:59', 'inactive');

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
('LS001', 'Sách Văn Học'),
('LS002', 'Sách Kinh Tế'),
('LS003', 'Sách Kỹ Năng Sống'),
('LS004', 'Sách Thiếu Nhi'),
('LS005', 'Sách Khoa học & Công nghệ');

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
('NN002', 'Tiếng Anh'),
('NN003', 'Tiếng Nhật'),
('NN004', 'Tiếng Pháp'),
('NN005', 'Song ngữ (Anh-Việt)');

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

--
-- Đang đổ dữ liệu cho bảng `nha_cung_cap`
--

INSERT INTO `nha_cung_cap` (`id_ncc`, `ten_ncc`, `dia_chi_ncc`, `sdt_ncc`, `email_ncc`, `trang_thai_ncc`) VALUES
('NCC01', 'Công ty Fahasa', '123 Lê Lợi, Q1, TPHCM', '02838227336', 'info@fahasa.com', 'active'),
('NCC02', 'Công ty Tiki Trading', '24 Lê Thánh Tôn, Q1, TPHCM', '19006035', 'contact@tiki.vn', 'active'),
('NCC03', 'Công ty Phương Nam', '456 Nguyễn Thị Minh Khai, Q3', '02838329065', 'info@phuongnambook.com', 'active'),
('NCC04', 'Công ty Anfabook', '789 Âu Cơ, Tân Bình', '02862652349', 'contact@anfabook.com', 'active'),
('NCC05', 'Đối tác NXB Trẻ', '161B Lý Chính Thắng, Q3', '02839316289', 'nxb@nxbtre.com.vn', 'active');

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
('NXB01', 'NXB Trẻ'),
('NXB02', 'NXB Kim Đồng'),
('NXB03', 'NXB Tổng Hợp TPHCM'),
('NXB04', 'NXB Lao Động'),
('NXB05', 'NXB Nhã Nam');

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

--
-- Đang đổ dữ liệu cho bảng `phuong_thuc_thanh_toan`
--

INSERT INTO `phuong_thuc_thanh_toan` (`id_pttt`, `ten_pttt`) VALUES
('PT001', 'Thanh toán khi nhận hàng (COD)'),
('PT002', 'Ví điện tử MoMo'),
('PT003', 'Thẻ ngân hàng'),
('PT004', 'ZaloPay');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `sach`
--

CREATE TABLE `sach` (
  `id_sach` varchar(5) NOT NULL,
  `id_nxb` varchar(5) NOT NULL,
  `id_km` varchar(5) NOT NULL,
  `ten_sach` varchar(100) DEFAULT NULL,
  `mo_ta` text DEFAULT NULL,
  `trang_thai_sach` smallint(6) DEFAULT NULL,
  `so_luong_ton` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `sach`
--

INSERT INTO `sach` (`id_sach`, `id_nxb`, `id_km`, `ten_sach`, `mo_ta`, `trang_thai_sach`, `so_luong_ton`) VALUES
('S0001', 'NXB05', 'KM000', 'Rừng Na Uy', 'Tiểu thuyết kinh điển của Haruki Murakami về tình yêu, mất mát và tuổi trẻ.', 1, 134),
('S0002', 'NXB01', 'KM003', 'Cho Tôi Xin Một Vé Đi Tuổi Thơ', 'Tác phẩm nổi tiếng của Nguyễn Nhật Ánh, đưa người đọc trở về với những ký ức tuổi thơ trong sáng.', 1, 197),
('S0003', 'NXB05', 'KM000', 'Nhà Giả Kim', 'Câu chuyện triết lý sâu sắc của Paulo Coelho về việc theo đuổi vận mệnh.', 1, 120),
('S0004', 'NXB01', 'KM002', 'Harry Potter và Hòn Đá Phù Thủy', 'Tập đầu tiên trong series huyền thoại của J.K. Rowling, mở ra thế giới pháp thuật kỳ diệu.', 1, 300),
('S0005', 'NXB05', 'KM000', 'Kiêu Hãnh và Định Kiến', 'Tác phẩm lãng mạn kinh điển của Jane Austen (tác giả mới, cần thêm).', 1, 80),
('S0006', 'NXB03', 'KM000', 'Cha Giàu, Cha Nghèo', 'Cuốn sách thay đổi tư duy tài chính của Robert Kiyosaki.', 1, 250),
('S0007', 'NXB03', 'KM000', 'Nghĩ Giàu và Làm Giàu', 'Tác phẩm kinh điển về thành công của Napoleon Hill.', 1, 180),
('S0008', 'NXB01', 'KM002', 'Từ Tốt Đến Vĩ Đại', 'Nghiên cứu của Jim Collins về các công ty vĩ đại (tác giả mới).', 1, 90),
('S0009', 'NXB04', 'KM000', '22 Quy Luật Bất Biến Của Marketing', 'Sách gối đầu giường cho các nhà tiếp thị.', 1, 70),
('S0010', 'NXB03', 'KM000', 'Dám Nghĩ Lớn', 'Cuốn sách truyền cảm hứng về việc đặt mục tiêu lớn.', 1, 110),
('S0011', 'NXB03', 'KM000', 'Đắc Nhân Tâm', 'Nghệ thuật ứng xử và giao tiếp kinh điển của Dale Carnegie.', 1, 500),
('S0012', 'NXB01', 'KM003', 'Nhà Lãnh Đạo Không Chức Danh', 'Câu chuyện về cách lãnh đạo từ bất kỳ vị trí nào.', 1, 130),
('S0013', 'NXB03', 'KM000', '7 Thói Quen Của Người Thành Đạt', 'Sách của Stephen Covey về việc xây dựng thói quen hiệu quả.', 1, 220),
('S0014', 'NXB04', 'KM002', 'Dám Bị Ghét', 'Một cái nhìn mới mẻ về tâm lý học Adler của Ichiro Kishimi.', 1, 160),
('S0015', 'NXB04', 'KM000', 'Sức Mạnh Của Sự Im Lặng', 'Cuốn sách về giá trị của sự tĩnh lặng trong thế giới ồn ào.', 1, 85),
('S0016', 'NXB02', 'KM000', 'Dế Mèn Phiêu Lưu Ký', 'Tác phẩm kinh điển của Tô Hoài cho thiếu nhi.', 1, 300),
('S0017', 'NXB05', 'KM000', 'Hoàng Tử Bé', 'Câu chuyện triết lý vượt thời gian của Antoine de Saint-Exupéry.', 1, 190),
('S0018', 'NXB02', 'KM000', 'Doraemon - Tập 1', 'Truyện tranh gắn liền với tuổi thơ của Fujiko F. Fujio.', 1, 399),
('S0019', 'NXB01', 'KM003', 'Kính Vạn Hoa - Tập 1', 'Series truyện thiếu nhi nổi tiếng khác của Nguyễn Nhật Ánh.', 1, 150),
('S0020', 'NXB02', 'KM000', 'Bộ Sách Tô Màu Công Chúa', 'Sách tô màu cho các bé gái.', 1, 250),
('S0021', 'NXB05', 'KM000', 'Sapiens: Lược Sử Loài Người', 'Cái nhìn tổng quan của Yuval Noah Harari về lịch sử nhân loại.', 1, 180),
('S0022', 'NXB01', 'KM000', 'Vũ Trụ Trong Vỏ Hạt Dẻ', 'Stephen Hawking giải thích các khái niệm vũ trụ phức tạp.', 1, 90),
('S0023', 'NXB04', 'KM000', 'Súng, Vi Trùng và Thép', 'Jared Diamond lý giải sự trỗi dậy của các nền văn minh.', 1, 60),
('S0024', 'NXB01', 'KM000', 'Lược Sử Thời Gian', 'Một tác phẩm kinh điển khác của Stephen Hawking.', 1, 100),
('S0025', 'NXB01', 'KM002', 'Code Dạo Ký Sự', 'Sách về lập trình của Phạm Huy Hoàng (Toidicodedao).', 1, 120);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `sach_theloai`
--

CREATE TABLE `sach_theloai` (
  `id_sach` varchar(5) NOT NULL,
  `id_the_loai` varchar(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `sach_theloai`
--

INSERT INTO `sach_theloai` (`id_sach`, `id_the_loai`) VALUES
('S0001', 'TL001'),
('S0002', 'TL002'),
('S0003', 'TL001'),
('S0004', 'TL001'),
('S0005', 'TL003'),
('S0006', 'TL007'),
('S0007', 'TL009'),
('S0008', 'TL005'),
('S0009', 'TL006'),
('S0010', 'TL009'),
('S0011', 'TL010'),
('S0012', 'TL005'),
('S0013', 'TL009'),
('S0014', 'TL011'),
('S0015', 'TL012'),
('S0016', 'TL002'),
('S0017', 'TL001'),
('S0018', 'TL013'),
('S0019', 'TL002'),
('S0020', 'TL015'),
('S0021', 'TL017'),
('S0022', 'TL018'),
('S0023', 'TL017'),
('S0024', 'TL018'),
('S0025', 'TL019');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `s_ncc`
--

CREATE TABLE `s_ncc` (
  `id_sach` varchar(5) NOT NULL,
  `id_ncc` varchar(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `s_ncc`
--

INSERT INTO `s_ncc` (`id_sach`, `id_ncc`) VALUES
('S0001', 'NCC01'),
('S0002', 'NCC05'),
('S0003', 'NCC02'),
('S0004', 'NCC01'),
('S0006', 'NCC03'),
('S0011', 'NCC04'),
('S0016', 'NCC05'),
('S0018', 'NCC02'),
('S0021', 'NCC01'),
('S0025', 'NCC04');

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
('S0002', 'NN001'),
('S0003', 'NN001'),
('S0004', 'NN001'),
('S0005', 'NN001'),
('S0006', 'NN001'),
('S0007', 'NN001'),
('S0008', 'NN002'),
('S0009', 'NN001'),
('S0010', 'NN001'),
('S0011', 'NN001'),
('S0012', 'NN001'),
('S0013', 'NN005'),
('S0014', 'NN001'),
('S0015', 'NN001'),
('S0016', 'NN001'),
('S0017', 'NN001'),
('S0018', 'NN001'),
('S0019', 'NN001'),
('S0020', 'NN001'),
('S0021', 'NN001'),
('S0022', 'NN001'),
('S0023', 'NN001'),
('S0024', 'NN001'),
('S0025', 'NN001');

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
('S0001', 'TG002'),
('S0002', 'TG001'),
('S0003', 'TG006'),
('S0004', 'TG004'),
('S0005', 'TG017'),
('S0006', 'TG007'),
('S0007', 'TG008'),
('S0008', 'TG018'),
('S0009', 'TG019'),
('S0010', 'TG020'),
('S0011', 'TG003'),
('S0012', 'TG021'),
('S0013', 'TG009'),
('S0014', 'TG010'),
('S0015', 'TG022'),
('S0016', 'TG011'),
('S0017', 'TG012'),
('S0018', 'TG013'),
('S0019', 'TG001'),
('S0020', 'TG023'),
('S0021', 'TG005'),
('S0022', 'TG014'),
('S0023', 'TG015'),
('S0024', 'TG014'),
('S0025', 'TG016');

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
('TG001', 'Nguyễn Nhật Ánh'),
('TG002', 'Haruki Murakami'),
('TG003', 'Dale Carnegie'),
('TG004', 'J.K. Rowling'),
('TG005', 'Yuval Noah Harari'),
('TG006', 'Paulo Coelho'),
('TG007', 'Robert Kiyosaki'),
('TG008', 'Napoleon Hill'),
('TG009', 'Stephen Covey'),
('TG010', 'Ichiro Kishimi'),
('TG011', 'Tô Hoài'),
('TG012', 'Antoine de Saint-Exupéry'),
('TG013', 'Fujiko F. Fujio'),
('TG014', 'Stephen Hawking'),
('TG015', 'Jared Diamond'),
('TG016', 'Phạm Huy Hoàng (Toidicodedao)'),
('TG017', 'Jane Austen'),
('TG018', 'Jim Collins'),
('TG019', 'Al Ries'),
('TG020', 'David J. Schwartz'),
('TG021', 'Robin Sharma'),
('TG022', 'Thích Nhất Hạnh'),
('TG023', 'Lê Văn');

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
  `ngay_gio_tao_tk` datetime NOT NULL,
  `trang_thai` tinyint(1) DEFAULT 1 COMMENT '1: Active, 0: Banned'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `tai_khoan`
--

INSERT INTO `tai_khoan` (`id_tk`, `id_nd`, `ho_ten`, `gioi_tinh`, `email`, `sdt`, `mat_khau`, `dia_chi_giao_hang`, `ngay_gio_tao_tk`, `trang_thai`) VALUES
('AD001', 'AD', 'Tram123', 'Khác', 'admin@qlsach.com', '0000000001', '$2y$10$TfuNpwml2a5EmP8G1uBpPO5VQtpJ.OnRxvs3QvuldOAeaQFHPjg4S', '123 Server, TP. HCM', '2025-10-20 21:51:53', 1),
('AD002', 'AD', 'Thuong123', 'Nam', 'admin_kho@qlsach.com', '0000000002', '$2y$10$TfuNpwml2a5EmP8G1uBpPO5VQtpJ.OnRxvs3QvuldOAeaQFHPjg4S', '123 Server, TP. HCM', '2025-10-20 21:51:53', 1),
('AD003', 'AD', 'Giang123', 'Khác', 'admin_content@qlsach.com', '0000000003', '$2y$10$TfuNpwml2a5EmP8G1uBpPO5VQtpJ.OnRxvs3QvuldOAeaQFHPjg4S', '123 Server, TP. HCM', '2025-10-20 21:51:53', 1),
('KH001', 'KH', 'Nguyễn Văn A', 'Nam', 'khach1@gmail.com', '0901000111', '$2y$10$f4e.9UWG5FwITxwjLnG8uO7HLlgA4VkfYBlTwzW9hpldIFACj.QGO', '456 Đường ABC, Q1, TP. HCM', '2025-10-20 21:51:53', 1),
('KH002', 'KH', 'Trần Thị B', 'Nữ', 'khach2@gmail.com', '0902000222', '$2y$10$f4e.9UWG5FwITxwjLnG8uO7HLlgA4VkfYBlTwzW9hpldIFACj.QGO', '789 Đường XYZ, Q3, TP. HCM', '2025-10-20 21:51:53', 1),
('TK137', 'KH', 'mai', 'Khác', 'mai@gmail.com', '012345678', '$2y$10$nGubzJPTD92QMCBGyo9z.OCOowQL0yTRI69/h86ypyBW7QWRyXC0C', 'Cần Thơ', '2025-11-15 00:38:47', 1),
('TK221', 'KH', 'tran ngoc lam', 'Khác', 'lam@gmail.com', '012345', '$2y$10$Z8LWwS9pmbjBZtu80eeyeeekG0lGqYHwzpXr4hu6o9gtoaE5K2wUG', 'Cần Thơ', '2025-10-20 21:58:04', 1),
('TK727', 'KH', 'Trần Trường Giang', 'Khác', 'giangtran@gmail.com', '0334880259', '$2y$10$KJx.YAxoliWs47xHtvNoV.0Ri9/RbbLPfYwmDWM7fJZs.sZ95OffO', 'Sóc Trăng', '2025-11-16 10:49:32', 1),
('TK896', 'KH', 'tran', 'Khác', 'tran@gmail.com', '0123456789', '$2y$10$o/6wtJ2IAXZyyEz4s1UnmODCa6kPGgHs0zpCevoSlXbzD5DP3UpFW', 'Cần Thơ', '2025-11-14 18:19:58', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `thanh_toan`
--

CREATE TABLE `thanh_toan` (
  `id_pttt` varchar(5) NOT NULL,
  `id_don_hang` varchar(20) NOT NULL,
  `trang_thai_tt` smallint(6) NOT NULL,
  `ngay_gio_thanh_toan` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `thanh_toan`
--

INSERT INTO `thanh_toan` (`id_pttt`, `id_don_hang`, `trang_thai_tt`, `ngay_gio_thanh_toan`) VALUES
('PT001', 'DH176', 0, '2025-11-15 00:53:42'),
('PT001', 'DH176314357146', 0, '2025-11-15 01:06:11'),
('PT001', 'DH176336033944', 0, '2025-11-17 13:18:59'),
('PT001', 'DH176343145325', 0, '2025-11-18 09:04:13'),
('PT001', 'DH176343148321', 0, '2025-11-18 09:04:43'),
('PT001', 'DH176343189365', 0, '2025-11-18 09:11:33'),
('PT001', 'DH176343203235', 0, '2025-11-18 09:13:52'),
('PT001', 'DH176343216769', 0, '2025-11-18 09:16:07'),
('PT001', 'DH176343312629', 0, '2025-11-18 09:32:06'),
('PT001', 'DH176344232284', 0, '2025-11-18 12:05:22'),
('PT001', 'DH176344248427', 0, '2025-11-18 12:08:04'),
('PT001', 'DH176344522644', 0, '2025-11-18 12:53:46'),
('PT002', 'DH176343154352', 0, '2025-11-18 09:05:43'),
('PT003', 'DH176344308023', 0, '2025-11-18 12:18:00');

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
('TL001', 'LS001', 'Tiểu thuyết'),
('TL002', 'LS001', 'Truyện ngắn'),
('TL003', 'LS001', 'Kinh điển'),
('TL004', 'LS001', 'Lãng mạn'),
('TL005', 'LS002', 'Quản trị - Lãnh đạo'),
('TL006', 'LS002', 'Marketing'),
('TL007', 'LS002', 'Tài chính cá nhân'),
('TL008', 'LS002', 'Khởi nghiệp'),
('TL009', 'LS003', 'Phát triển bản thân'),
('TL010', 'LS003', 'Giao tiếp & Ứng xử'),
('TL011', 'LS003', 'Tâm lý học'),
('TL012', 'LS003', 'Hạnh phúc'),
('TL013', 'LS004', 'Truyện tranh (Comic)'),
('TL014', 'LS004', 'Truyện cổ tích'),
('TL015', 'LS004', 'Sách tô màu'),
('TL016', 'LS004', 'Sách kiến thức'),
('TL017', 'LS005', 'Lịch sử'),
('TL018', 'LS005', 'Vũ trụ & Thiên văn'),
('TL019', 'LS005', 'Công nghệ thông tin'),
('TL020', 'LS005', 'Sinh học');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `thoi_diem`
--

CREATE TABLE `thoi_diem` (
  `tg_gia_bd` datetime NOT NULL,
  `tg_gia_kt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `thoi_diem`
--

INSERT INTO `thoi_diem` (`tg_gia_bd`, `tg_gia_kt`) VALUES
('2024-01-01 00:00:00', NULL),
('2024-06-01 00:00:00', NULL),
('2025-01-01 00:00:00', NULL),
('2025-10-15 00:00:00', NULL),
('2025-11-14 00:00:00', NULL),
('2025-11-21 14:28:35', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `thong_bao`
--

CREATE TABLE `thong_bao` (
  `id_thong_bao` int(11) NOT NULL,
  `id_tk` varchar(5) NOT NULL,
  `tieu_de` varchar(250) NOT NULL,
  `noi_dung` text NOT NULL,
  `loai` varchar(20) NOT NULL DEFAULT 'info' COMMENT 'info, success, warning, error',
  `lien_ket` varchar(250) DEFAULT NULL,
  `da_doc` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0: chưa đọc, 1: đã đọc',
  `ngay_gio_tao` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `thong_bao`
--

INSERT INTO `thong_bao` (`id_thong_bao`, `id_tk`, `tieu_de`, `noi_dung`, `loai`, `lien_ket`, `da_doc`, `ngay_gio_tao`) VALUES
(1, 'TK727', 'Đặt hàng thành công', 'Đơn hàng DH176344248427 của bạn đã được đặt thành công và đang được xử lý.', 'success', '/qlsach/user/orders.php', 0, '2025-11-18 12:08:04'),
(2, 'TK727', 'Đặt hàng thành công', 'Đơn hàng DH176344522644 của bạn đã được đặt thành công. Bạn sẽ thanh toán khi nhận hàng.', 'success', '/qlsach/user/orders.php', 0, '2025-11-18 12:53:46');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `trang_thai_don_hang`
--

CREATE TABLE `trang_thai_don_hang` (
  `id_trang_thai` int(11) NOT NULL,
  `trang_thai_dh` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `trang_thai_don_hang`
--

INSERT INTO `trang_thai_don_hang` (`id_trang_thai`, `trang_thai_dh`) VALUES
(1, 'Chờ xử lý'),
(2, 'Đã xác nhận'),
(3, 'Đang giao hàng'),
(4, 'Đã hoàn thành'),
(5, 'Đã hủy');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `wishlist`
--

CREATE TABLE `wishlist` (
  `id_tk` varchar(5) NOT NULL,
  `id_sach` varchar(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `wishlist`
--

INSERT INTO `wishlist` (`id_tk`, `id_sach`) VALUES
('TK727', 'S0002'),
('TK727', 'S0003');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `binh_luan`
--
ALTER TABLE `binh_luan`
  ADD PRIMARY KEY (`id_bl`),
  ADD KEY `id_sach` (`id_sach`),
  ADD KEY `id_tk` (`id_tk`);

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
  ADD PRIMARY KEY (`id_sach`,`tg_gia_bd`),
  ADD KEY `ngay_gio_ban` (`tg_gia_bd`);

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
  ADD KEY `id_km` (`id_km`);

--
-- Chỉ mục cho bảng `sach_theloai`
--
ALTER TABLE `sach_theloai`
  ADD PRIMARY KEY (`id_sach`,`id_the_loai`),
  ADD KEY `id_the_loai` (`id_the_loai`);

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
  ADD PRIMARY KEY (`tg_gia_bd`);

--
-- Chỉ mục cho bảng `thong_bao`
--
ALTER TABLE `thong_bao`
  ADD PRIMARY KEY (`id_thong_bao`),
  ADD KEY `id_tk` (`id_tk`),
  ADD KEY `da_doc` (`da_doc`),
  ADD KEY `ngay_gio_tao` (`ngay_gio_tao`);

--
-- Chỉ mục cho bảng `trang_thai_don_hang`
--
ALTER TABLE `trang_thai_don_hang`
  ADD PRIMARY KEY (`id_trang_thai`);

--
-- Chỉ mục cho bảng `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id_tk`,`id_sach`),
  ADD KEY `id_sach` (`id_sach`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `thong_bao`
--
ALTER TABLE `thong_bao`
  MODIFY `id_thong_bao` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `trang_thai_don_hang`
--
ALTER TABLE `trang_thai_don_hang`
  MODIFY `id_trang_thai` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `binh_luan`
--
ALTER TABLE `binh_luan`
  ADD CONSTRAINT `binh_luan_ibfk_1` FOREIGN KEY (`id_sach`) REFERENCES `sach` (`id_sach`),
  ADD CONSTRAINT `binh_luan_ibfk_2` FOREIGN KEY (`id_tk`) REFERENCES `tai_khoan` (`id_tk`) ON DELETE SET NULL;

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
  ADD CONSTRAINT `gia_sach_ibfk_2` FOREIGN KEY (`tg_gia_bd`) REFERENCES `thoi_diem` (`tg_gia_bd`);

--
-- Các ràng buộc cho bảng `phieu_nhap`
--
ALTER TABLE `phieu_nhap`
  ADD CONSTRAINT `phieu_nhap_ibfk_1` FOREIGN KEY (`id_ncc`) REFERENCES `nha_cung_cap` (`id_ncc`);

--
-- Các ràng buộc cho bảng `sach`
--
ALTER TABLE `sach`
  ADD CONSTRAINT `sach_ibfk_2` FOREIGN KEY (`id_nxb`) REFERENCES `nxb` (`id_nxb`),
  ADD CONSTRAINT `sach_ibfk_3` FOREIGN KEY (`id_km`) REFERENCES `khuyen_mai` (`id_km`);

--
-- Các ràng buộc cho bảng `sach_theloai`
--
ALTER TABLE `sach_theloai`
  ADD CONSTRAINT `sach_theloai_ibfk_1` FOREIGN KEY (`id_sach`) REFERENCES `sach` (`id_sach`),
  ADD CONSTRAINT `sach_theloai_ibfk_2` FOREIGN KEY (`id_the_loai`) REFERENCES `the_loai` (`id_the_loai`);

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

--
-- Các ràng buộc cho bảng `thong_bao`
--
ALTER TABLE `thong_bao`
  ADD CONSTRAINT `thong_bao_ibfk_1` FOREIGN KEY (`id_tk`) REFERENCES `tai_khoan` (`id_tk`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`id_tk`) REFERENCES `tai_khoan` (`id_tk`),
  ADD CONSTRAINT `wishlist_ibfk_2` FOREIGN KEY (`id_sach`) REFERENCES `sach` (`id_sach`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
