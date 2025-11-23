-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th10 23, 2025 lúc 02:55 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.1.25

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
-- Cấu trúc bảng cho bảng `bai_viet`
--

CREATE TABLE `bai_viet` (
  `id_bai_viet` int(11) NOT NULL,
  `id_danh_muc` varchar(5) NOT NULL,
  `id_tk` varchar(5) NOT NULL COMMENT 'Tác giả bài viết (admin)',
  `tieu_de` varchar(250) NOT NULL,
  `slug` varchar(300) NOT NULL COMMENT 'URL-friendly title',
  `tom_tat` text DEFAULT NULL COMMENT 'Tóm tắt ngắn gọn',
  `noi_dung` longtext NOT NULL,
  `anh_dai_dien` varchar(255) DEFAULT NULL,
  `luot_xem` int(11) DEFAULT 0,
  `trang_thai` varchar(20) DEFAULT 'draft' COMMENT 'draft, published, archived',
  `noi_bat` tinyint(1) DEFAULT 0 COMMENT '1: Bài viết nổi bật',
  `meta_title` varchar(250) DEFAULT NULL COMMENT 'SEO title',
  `meta_description` text DEFAULT NULL COMMENT 'SEO description',
  `meta_keywords` varchar(250) DEFAULT NULL COMMENT 'SEO keywords',
  `ngay_tao` datetime DEFAULT current_timestamp(),
  `ngay_cap_nhat` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `ngay_xuat_ban` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `bai_viet`
--

INSERT INTO `bai_viet` (`id_bai_viet`, `id_danh_muc`, `id_tk`, `tieu_de`, `slug`, `tom_tat`, `noi_dung`, `anh_dai_dien`, `luot_xem`, `trang_thai`, `noi_bat`, `meta_title`, `meta_description`, `meta_keywords`, `ngay_tao`, `ngay_cap_nhat`, `ngay_xuat_ban`) VALUES
(1, 'DM001', 'AD001', 'Review: Rừng Na Uy - Kiệt tác của Haruki Murakami', 'review-rung-na-uy-kiet-tac-cua-haruki-murakami', 'Rừng Na Uy là một trong những tác phẩm nổi tiếng nhất của Haruki Murakami, kể về câu chuyện tình yêu đầy cảm xúc và nỗi đau của tuổi trẻ.', '<h2>Giới thiệu</h2><p>Rừng Na Uy (Norwegian Wood) là tiểu thuyết được xuất bản năm 1987 của nhà văn Nhật Bản Haruki Murakami. Đây là tác phẩm bán chạy nhất của ông với hơn 10 triệu bản được bán ra trên toàn thế giới.</p><h2>Nội dung</h2><p>Câu chuyện xoay quanh Watanabe Toru, một sinh viên đại học ở Tokyo vào cuối những năm 1960. Anh yêu Naoko, bạn gái của người bạn thân đã tự tử. Naoko cũng đang phải vật lộn với những vấn đề tâm lý của riêng mình...</p><h2>Đánh giá</h2><p>Rừng Na Uy là một tác phẩm đầy cảm xúc, khắc họa chân thực nỗi đau, sự mất mát và tình yêu của tuổi trẻ. Phong cách viết của Murakami vừa mộng mơ vừa chân thực, tạo nên một câu chuyện khó quên.</p>', NULL, 1250, 'published', 1, 'Review Rừng Na Uy - Haruki Murakami | Nhà Sách Online', 'Đánh giá chi tiết về tiểu thuyết Rừng Na Uy của Haruki Murakami - Tác phẩm kinh điển về tình yêu và tuổi trẻ', 'rừng na uy, haruki murakami, review sách, tiểu thuyết nhật bản', '2025-11-23 13:59:24', '2025-11-23 13:59:24', '2025-11-20 10:00:00'),
(2, 'DM002', 'AD001', 'Haruki Murakami đoạt giải văn học quốc tế 2024', 'haruki-murakami-doat-giai-van-hoc-quoc-te-2024', 'Nhà văn Nhật Bản Haruki Murakami vừa được trao giải văn học quốc tế danh giá cho những đóng góp xuất sắc trong sự nghiệp văn chương.', '<h2>Tin tức</h2><p>Haruki Murakami, một trong những nhà văn được yêu thích nhất thế giới, vừa được trao giải văn học quốc tế danh giá tại lễ trao giải diễn ra tại Paris, Pháp.</p><h2>Thành tựu</h2><p>Với hơn 40 năm cống hiến cho văn học, Murakami đã tạo ra những tác phẩm để đời như Rừng Na Uy, Kafka Bên Bờ Biển, 1Q84... Tác phẩm của ông đã được dịch ra hơn 50 ngôn ngữ.</p>', NULL, 851, 'published', 1, 'Haruki Murakami đoạt giải văn học quốc tế 2024', 'Tin tức mới nhất về giải thưởng văn học của Haruki Murakami năm 2024', 'haruki murakami, giải văn học, tin tức văn học', '2025-11-23 13:59:24', '2025-11-23 14:08:19', '2025-11-21 14:30:00'),
(3, 'DM003', 'AD001', 'Nguyễn Nhật Ánh - Người kể chuyện tuổi thơ', 'nguyen-nhat-anh-nguoi-ke-chuyen-tuoi-tho', 'Tìm hiểu về Nguyễn Nhật Ánh - nhà văn được yêu thích nhất Việt Nam với những tác phẩm gắn liền với tuổi thơ của nhiều thế hệ.', '<h2>Tiểu sử</h2><p>Nguyễn Nhật Ánh sinh năm 1955 tại Quảng Nam. Ông là một trong những nhà văn được yêu thích nhất Việt Nam, đặc biệt với độc giả trẻ.</p><h2>Tác phẩm nổi bật</h2><p>Các tác phẩm của Nguyễn Nhật Ánh như \"Cho Tôi Xin Một Vé Đi Tuổi Thơ\", \"Mắt Biếc\", \"Tôi Thấy Hoa Vàng Trên Cỏ Xanh\" đã trở thành những tác phẩm kinh điển của văn học Việt Nam.</p><h2>Phong cách</h2><p>Phong cách viết của Nguyễn Nhật Ánh giản dị, gần gũi nhưng đầy cảm xúc, khắc họa chân thực tâm hồn tuổi trẻ và những kỷ niệm tuổi thơ.</p>', NULL, 2100, 'published', 1, 'Nguyễn Nhật Ánh - Nhà văn tuổi thơ của người Việt', 'Tìm hiểu về cuộc đời và sự nghiệp của nhà văn Nguyễn Nhật Ánh', 'nguyễn nhật ánh, tác giả việt nam, văn học thiếu nhi', '2025-11-23 13:59:24', '2025-11-23 13:59:24', '2025-11-19 09:00:00'),
(4, 'DM004', 'AD001', '5 Mẹo Đọc Sách Hiệu Quả Cho Người Bận Rộn', '5-meo-doc-sach-hieu-qua-cho-nguoi-ban-ron', 'Chia sẻ những phương pháp đọc sách hiệu quả giúp bạn tiếp thu kiến thức tốt hơn ngay cả khi bận rộn.', '<h2>1. Đặt mục tiêu rõ ràng</h2><p>Trước khi đọc, hãy xác định rõ bạn muốn học được gì từ cuốn sách. Điều này giúp bạn tập trung vào những phần quan trọng.</p><h2>2. Đọc vào thời điểm phù hợp</h2><p>Chọn thời điểm trong ngày khi bạn tỉnh táo nhất để đọc. Có thể là buổi sáng sớm hoặc trước khi đi ngủ.</p><h2>3. Ghi chú và đánh dấu</h2><p>Đừng ngại ghi chú hoặc đánh dấu những đoạn quan trọng. Điều này giúp bạn dễ dàng ôn lại sau này.</p><h2>4. Áp dụng kỹ thuật Pomodoro</h2><p>Đọc trong 25 phút, nghỉ 5 phút. Kỹ thuật này giúp duy trì sự tập trung.</p><h2>5. Thảo luận và chia sẻ</h2><p>Chia sẻ những gì bạn đọc được với người khác giúp củng cố kiến thức và tạo động lực đọc tiếp.</p>', NULL, 1680, 'published', 0, '5 Mẹo Đọc Sách Hiệu Quả Cho Người Bận Rộn', 'Hướng dẫn cách đọc sách hiệu quả ngay cả khi bạn bận rộn', 'mẹo đọc sách, đọc sách hiệu quả, kỹ năng đọc', '2025-11-23 13:59:24', '2025-11-23 13:59:24', '2025-11-18 16:00:00'),
(5, 'DM005', 'AD001', 'Hội Sách TP.HCM 2025 - Sự Kiện Văn Hóa Lớn Nhất Năm', 'hoi-sach-tphcm-2025-su-kien-van-hoa-lon-nhat-nam', 'Hội sách TP.HCM 2025 sẽ diễn ra từ ngày 1-7/4/2025 tại Công viên Tao Đàn với hàng trăm gian hàng sách và nhiều hoạt động văn hóa hấp dẫn.', '<h2>Thông tin sự kiện</h2><p>Hội sách TP.HCM là sự kiện văn hóa lớn nhất trong năm, thu hút hàng triệu lượt khách tham quan.</p><h2>Thời gian và địa điểm</h2><ul><li>Thời gian: 1-7/4/2025</li><li>Địa điểm: Công viên Tao Đàn, Quận 1, TP.HCM</li><li>Giờ mở cửa: 8:00 - 21:00 hàng ngày</li></ul><h2>Hoạt động nổi bật</h2><ul><li>Gặp gỡ tác giả nổi tiếng</li><li>Ký tặng sách</li><li>Tọa đàm văn học</li><li>Biểu diễn nghệ thuật</li><li>Giảm giá sách lên đến 50%</li></ul>', NULL, 950, 'published', 1, 'Hội Sách TP.HCM 2025 - Lễ Hội Sách Lớn Nhất Năm', 'Thông tin chi tiết về Hội sách TP.HCM 2025 - Sự kiện văn hóa không thể bỏ lỡ', 'hội sách, hội sách tphcm, sự kiện văn hóa, lễ hội sách', '2025-11-23 13:59:24', '2025-11-23 13:59:24', '2025-11-22 08:00:00');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `bai_viet_tag`
--

CREATE TABLE `bai_viet_tag` (
  `id_bai_viet` int(11) NOT NULL,
  `id_tag` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `bai_viet_tag`
--

INSERT INTO `bai_viet_tag` (`id_bai_viet`, `id_tag`) VALUES
(1, 1),
(1, 3),
(1, 4),
(1, 5),
(2, 1),
(2, 5),
(2, 8),
(3, 2),
(3, 5),
(3, 10),
(4, 6),
(5, 7);

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
('BL176', 'S0001', 'TK727', 'hi', 5.00, '2025-11-18 12:56:33'),
('BL8f3', 'S0008', 'TK727', 'te', 3.00, '2025-11-23 20:04:09'),
('BLb07', 'S0004', 'TK727', '.', 5.00, '2025-11-23 20:01:03'),
('BLc21', 'S0002', 'TK727', 'te', 4.00, '2025-11-23 20:01:19');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `binh_luan_bai_viet`
--

CREATE TABLE `binh_luan_bai_viet` (
  `id_binh_luan` int(11) NOT NULL,
  `id_bai_viet` int(11) NOT NULL,
  `id_tk` varchar(5) DEFAULT NULL,
  `ten_nguoi_binh_luan` varchar(100) DEFAULT NULL COMMENT 'Nếu không đăng nhập',
  `email` varchar(100) DEFAULT NULL COMMENT 'Nếu không đăng nhập',
  `noi_dung` text NOT NULL,
  `trang_thai` varchar(20) DEFAULT 'pending' COMMENT 'pending, approved, rejected',
  `ngay_tao` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
('DH176344522644', 'S0002', 1, 68000),
('DH176390147618', 'S0003', 1, 90000),
('DH176390189554', 'S0004', 1, 120000),
('DH176390301349', 'S0008', 1, 80000);

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
-- Cấu trúc bảng cho bảng `danh_muc_bai_viet`
--

CREATE TABLE `danh_muc_bai_viet` (
  `id_danh_muc` varchar(5) NOT NULL,
  `ten_danh_muc` varchar(100) NOT NULL,
  `slug` varchar(150) NOT NULL COMMENT 'URL-friendly name',
  `mo_ta` text DEFAULT NULL,
  `thu_tu` int(11) DEFAULT 0,
  `trang_thai` tinyint(1) DEFAULT 1 COMMENT '1: Active, 0: Inactive',
  `ngay_tao` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `danh_muc_bai_viet`
--

INSERT INTO `danh_muc_bai_viet` (`id_danh_muc`, `ten_danh_muc`, `slug`, `mo_ta`, `thu_tu`, `trang_thai`, `ngay_tao`) VALUES
('DM001', 'Review Sách', 'review-sach', 'Đánh giá và nhận xét về các cuốn sách hay', 1, 1, '2025-11-23 13:59:24'),
('DM002', 'Tin Tức Văn Học', 'tin-tuc-van-hoc', 'Tin tức mới nhất về giới văn học', 2, 1, '2025-11-23 13:59:24'),
('DM003', 'Tác Giả & Tác Phẩm', 'tac-gia-tac-pham', 'Giới thiệu về tác giả và tác phẩm nổi tiếng', 3, 1, '2025-11-23 13:59:24'),
('DM004', 'Mẹo Đọc Sách', 'meo-doc-sach', 'Chia sẻ kinh nghiệm và phương pháp đọc sách hiệu quả', 4, 1, '2025-11-23 13:59:24'),
('DM005', 'Sự Kiện', 'su-kien', 'Thông tin về các sự kiện văn học, hội sách', 5, 1, '2025-11-23 13:59:24');

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
('DH176344522644', 'TK727', 4, '2025-11-18 12:53:46', 'Sóc Trăng'),
('DH176390147618', 'TK727', 4, '2025-11-23 19:37:56', 'Sóc Trăng'),
('DH176390189554', 'TK727', 4, '2025-11-23 19:44:55', 'Sóc Trăng'),
('DH176390301349', 'TK727', 4, '2025-11-23 20:03:33', 'Sóc Trăng');

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
('S0003', 'NXB05', 'KM000', 'Nhà Giả Kim', 'Câu chuyện triết lý sâu sắc của Paulo Coelho về việc theo đuổi vận mệnh.', 1, 119),
('S0004', 'NXB01', 'KM002', 'Harry Potter và Hòn Đá Phù Thủy', 'Tập đầu tiên trong series huyền thoại của J.K. Rowling, mở ra thế giới pháp thuật kỳ diệu.', 1, 299),
('S0005', 'NXB05', 'KM000', 'Kiêu Hãnh và Định Kiến', 'Tác phẩm lãng mạn kinh điển của Jane Austen (tác giả mới, cần thêm).', 1, 80),
('S0006', 'NXB03', 'KM000', 'Cha Giàu, Cha Nghèo', 'Cuốn sách thay đổi tư duy tài chính của Robert Kiyosaki.', 1, 250),
('S0007', 'NXB03', 'KM000', 'Nghĩ Giàu và Làm Giàu', 'Tác phẩm kinh điển về thành công của Napoleon Hill.', 1, 180),
('S0008', 'NXB01', 'KM002', 'Từ Tốt Đến Vĩ Đại', 'Nghiên cứu của Jim Collins về các công ty vĩ đại (tác giả mới).', 1, 89),
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
-- Cấu trúc bảng cho bảng `tag`
--

CREATE TABLE `tag` (
  `id_tag` int(11) NOT NULL,
  `ten_tag` varchar(50) NOT NULL,
  `slug` varchar(70) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `tag`
--

INSERT INTO `tag` (`id_tag`, `ten_tag`, `slug`) VALUES
(1, 'Văn học Nhật Bản', 'van-hoc-nhat-ban'),
(2, 'Văn học Việt Nam', 'van-hoc-viet-nam'),
(3, 'Tiểu thuyết', 'tieu-thuyet'),
(4, 'Review sách', 'review-sach'),
(5, 'Tác giả nổi tiếng', 'tac-gia-noi-tieng'),
(6, 'Kỹ năng đọc', 'ky-nang-doc'),
(7, 'Sự kiện', 'su-kien'),
(8, 'Giải thưởng', 'giai-thuong'),
(9, 'Bestseller', 'bestseller'),
(10, 'Văn học thiếu nhi', 'van-hoc-thieu-nhi');

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
('TK697', 'KH', 'Giang Trần', 'Khác', 'truonggiangtran0202@gmail.com', '', '$2y$10$0eQS6sPzqqWp69.9ZiP1Seh/72os5.Pz4zol4xBqGtGoeRRcsNMFy', '', '2025-11-23 19:16:34', 1),
('TK727', 'KH', 'Trần Trường Giang', 'Khác', 'giangtran@gmail.com', '0334880259', '$2y$10$5DG1RxEk8rAZr.ixDe/Qi.7H0Oe4wpdaGTSu9of1VmLrPq0Po.Gui', 'Sóc Trăng', '2025-11-16 10:49:32', 1),
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
('PT001', 'DH176390147618', 0, '2025-11-23 19:37:56'),
('PT001', 'DH176390189554', 0, '2025-11-23 19:44:55'),
('PT001', 'DH176390301349', 0, '2025-11-23 20:03:33'),
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
(2, 'TK727', 'Đặt hàng thành công', 'Đơn hàng DH176344522644 của bạn đã được đặt thành công. Bạn sẽ thanh toán khi nhận hàng.', 'success', '/qlsach/user/orders.php', 0, '2025-11-18 12:53:46'),
(3, 'TK727', 'Đặt hàng thành công', 'Đơn hàng DH176390147618 của bạn đã được đặt thành công. Bạn sẽ thanh toán khi nhận hàng.', 'success', '/qlsach/user/orders.php', 0, '2025-11-23 19:37:56'),
(4, 'TK727', 'Đặt hàng thành công', 'Đơn hàng DH176390189554 của bạn đã được đặt thành công. Bạn sẽ thanh toán khi nhận hàng.', 'success', '/qlsach/user/orders.php', 0, '2025-11-23 19:44:55'),
(5, 'TK727', 'Đặt hàng thành công', 'Đơn hàng DH176390301349 của bạn đã được đặt thành công. Bạn sẽ thanh toán khi nhận hàng.', 'success', '/qlsach/user/orders.php', 0, '2025-11-23 20:03:33');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `thong_tin_sach`
--

CREATE TABLE `thong_tin_sach` (
  `id_sach` varchar(5) NOT NULL,
  `so_trang` int(11) DEFAULT NULL,
  `trong_luong` int(11) DEFAULT NULL COMMENT 'Đơn vị: gram',
  `kich_thuoc` varchar(50) DEFAULT NULL COMMENT 'VD: 14.5 x 20.5 cm',
  `hinh_thuc` varchar(50) DEFAULT NULL COMMENT 'Bìa cứng, bìa mềm, ebook',
  `nam_xuat_ban` year(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `thong_tin_sach`
--

INSERT INTO `thong_tin_sach` (`id_sach`, `so_trang`, `trong_luong`, `kich_thuoc`, `hinh_thuc`, `nam_xuat_ban`) VALUES
('S0001', 420, 450, '14.5 x 20.5 cm', 'Bìa mềm', '2018'),
('S0002', 280, 320, '14 x 20 cm', 'Bìa mềm', '2015'),
('S0003', 224, 250, '13 x 20 cm', 'Bìa mềm', '2013'),
('S0004', 336, 380, '15 x 22 cm', 'Bìa cứng', '2020'),
('S0005', 432, 480, '14 x 21 cm', 'Bìa mềm', '2010'),
('S0006', 336, 360, '14.5 x 20.5 cm', 'Bìa mềm', '2017'),
('S0007', 320, 340, '14 x 20 cm', 'Bìa mềm', '2016'),
('S0008', 400, 420, '15 x 22 cm', 'Bìa cứng', '2019'),
('S0009', 256, 280, '14 x 20 cm', 'Bìa mềm', '2014'),
('S0010', 288, 310, '14 x 20 cm', 'Bìa mềm', '2015'),
('S0011', 320, 350, '14.5 x 20.5 cm', 'Bìa mềm', '2012'),
('S0012', 304, 330, '14 x 20 cm', 'Bìa mềm', '2018'),
('S0013', 384, 410, '14.5 x 20.5 cm', 'Bìa mềm', '2016'),
('S0014', 296, 320, '14 x 20 cm', 'Bìa mềm', '2019'),
('S0015', 240, 260, '13 x 19 cm', 'Bìa mềm', '2017'),
('S0016', 192, 220, '14 x 20 cm', 'Bìa mềm', '2010'),
('S0017', 128, 150, '13 x 19 cm', 'Bìa mềm', '2015'),
('S0018', 96, 120, '11.5 x 17.5 cm', 'Bìa mềm', '2020'),
('S0019', 240, 270, '14 x 20 cm', 'Bìa mềm', '2016'),
('S0020', 48, 80, '21 x 28 cm', 'Bìa mềm', '2021'),
('S0021', 512, 580, '15 x 23 cm', 'Bìa cứng', '2018'),
('S0022', 208, 240, '14 x 20 cm', 'Bìa mềm', '2016'),
('S0023', 528, 600, '15 x 23 cm', 'Bìa cứng', '2017'),
('S0024', 256, 290, '14 x 20 cm', 'Bìa mềm', '2015'),
('S0025', 368, 400, '14.5 x 20.5 cm', 'Bìa mềm', '2019');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `thong_tin_tac_gia`
--

CREATE TABLE `thong_tin_tac_gia` (
  `id_tac_gia` varchar(5) NOT NULL,
  `tieu_su` text DEFAULT NULL COMMENT 'Tiểu sử tác giả',
  `ngay_sinh` date DEFAULT NULL COMMENT 'Ngày sinh',
  `ngay_mat` date DEFAULT NULL COMMENT 'Ngày mất (nếu có)',
  `quoc_tich` varchar(100) DEFAULT NULL COMMENT 'Quốc tịch',
  `anh_dai_dien` varchar(255) DEFAULT NULL COMMENT 'Đường dẫn ảnh đại diện',
  `website` varchar(255) DEFAULT NULL COMMENT 'Website cá nhân',
  `facebook` varchar(255) DEFAULT NULL COMMENT 'Facebook',
  `twitter` varchar(255) DEFAULT NULL COMMENT 'Twitter',
  `instagram` varchar(255) DEFAULT NULL COMMENT 'Instagram',
  `giai_thuong` text DEFAULT NULL COMMENT 'Các giải thưởng đã đạt được',
  `tac_pham_noi_bat` text DEFAULT NULL COMMENT 'Các tác phẩm nổi bật',
  `ngay_tao` datetime DEFAULT current_timestamp(),
  `ngay_cap_nhat` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `thong_tin_tac_gia`
--

INSERT INTO `thong_tin_tac_gia` (`id_tac_gia`, `tieu_su`, `ngay_sinh`, `ngay_mat`, `quoc_tich`, `anh_dai_dien`, `website`, `facebook`, `twitter`, `instagram`, `giai_thuong`, `tac_pham_noi_bat`, `ngay_tao`, `ngay_cap_nhat`) VALUES
('TG001', 'Nguyễn Nhật Ánh là nhà văn Việt Nam nổi tiếng với các tác phẩm văn học thiếu nhi và tuổi teen. Ông được biết đến với phong cách viết giản dị, gần gũi và đầy cảm xúc, khắc họa chân thực tâm hồn tuổi trẻ và những kỷ niệm tuổi thơ. Các tác phẩm của ông thường mang đến cho độc giả cảm giác ấm áp, hoài niệm và sâu sắc về cuộc sống.', '1955-05-07', NULL, 'Việt Nam', NULL, NULL, NULL, NULL, NULL, 'Giải thưởng Văn học ASEAN 2010, Giải thưởng Hội Nhà văn Việt Nam', 'Cho Tôi Xin Một Vé Đi Tuổi Thơ, Kính Vạn Hoa, Mắt Biếc, Tôi Thấy Hoa Vàng Trên Cỏ Xanh, Cô Gái Đến Từ Hôm Qua', '2025-11-23 13:59:33', '2025-11-23 13:59:33'),
('TG002', 'Haruki Murakami là tiểu thuyết gia và nhà văn người Nhật Bản, một trong những tác giả được dịch và đọc nhiều nhất trên thế giới. Tác phẩm của ông thường kết hợp yếu tố hiện thực và siêu thực, khám phá những chủ đề về cô đơn, tình yêu, sự tìm kiếm ý nghĩa cuộc sống và những bí ẩn của tâm hồn con người. Phong cách viết của Murakami được đánh giá là độc đáo, mộng mơ và đầy chất thơ.', '1949-01-12', NULL, 'Nhật Bản', NULL, NULL, NULL, NULL, NULL, 'Giải Franz Kafka (2006), Giải Jerusalem (2009), Giải Hans Christian Andersen (2016)', 'Rừng Na Uy, Kafka Bên Bờ Biển, 1Q84, Biên Niên Ký Chim Vặn Dây Cót, Sau Cơn Động Đất', '2025-11-23 13:59:33', '2025-11-23 13:59:33'),
('TG003', 'Dale Carnegie (1888-1955) là nhà văn và diễn giả người Mỹ, nổi tiếng với các tác phẩm về kỹ năng giao tiếp, nghệ thuật ứng xử và phát triển bản thân. Cuốn sách \"Đắc Nhân Tâm\" (How to Win Friends and Influence People) của ông xuất bản năm 1936 đã trở thành một trong những cuốn sách bán chạy nhất mọi thời đại với hơn 30 triệu bản được bán ra trên toàn thế giới. Triết lý của Carnegie tập trung vào việc xây dựng mối quan hệ tích cực và phát triển kỹ năng lãnh đạo.', '1888-11-24', '1955-11-01', 'Hoa Kỳ', NULL, NULL, NULL, NULL, NULL, NULL, 'Đắc Nhân Tâm, Dám Nghĩ Lớn, Đừng Bao Giờ Sợ Hãi, Cách Để Không Lo Lắng Và Bắt Đầu Cuộc Sống', '2025-11-23 13:59:33', '2025-11-23 13:59:33'),
('TG004', 'J.K. Rowling là tiểu thuyết gia người Anh, tác giả của series Harry Potter - một trong những series sách bán chạy nhất trong lịch sử với hơn 500 triệu bản được bán ra trên toàn thế giới. Tác phẩm của bà đã được dịch ra hơn 80 ngôn ngữ và chuyển thể thành 8 bộ phim ăn khách. Rowling được biết đến với khả năng xây dựng thế giới phép thuật phong phú, các nhân vật sống động và cốt truyện hấp dẫn. Bà cũng là một trong những phụ nữ giàu nhất thế giới nhờ thành công của Harry Potter.', '1965-07-31', NULL, 'Anh', NULL, NULL, NULL, NULL, NULL, 'Huân chương Đế chế Anh (OBE), Giải Hugo, Giải British Book Awards, Giải Locus', 'Harry Potter (7 tập), The Casual Vacancy, Cormoran Strike series (dưới bút danh Robert Galbraith)', '2025-11-23 13:59:33', '2025-11-23 13:59:33'),
('TG005', 'Yuval Noah Harari là sử học gia, triết gia và tác giả người Israel. Ông là giáo sư tại Đại học Hebrew ở Jerusalem, chuyên về lịch sử thế giới và các quá trình vĩ mô. Harari nổi tiếng với các tác phẩm về lịch sử loài người, công nghệ và tương lai của nhân loại, được viết theo phong cách dễ hiểu, hấp dẫn và đầy tính tư duy phản biện. Cuốn \"Sapiens\" của ông đã trở thành hiện tượng xuất bản toàn cầu với hơn 20 triệu bản được bán ra.', '1976-02-24', NULL, 'Israel', NULL, NULL, NULL, NULL, NULL, 'Giải Polonsky Prize for Creativity and Originality (2009, 2012)', 'Sapiens: Lược Sử Loài Người, Homo Deus: Lược Sử Tương Lai, 21 Bài Học Cho Thế Kỷ 21', '2025-11-23 13:59:33', '2025-11-23 13:59:33'),
('TG006', 'Paulo Coelho là tiểu thuyết gia, nhà thơ và nhạc sĩ người Brazil. Ông là một trong những tác giả được đọc nhiều nhất thế giới với hơn 320 triệu sách được bán ra, được dịch sang 88 ngôn ngữ. Tác phẩm của ông thường mang tính triết lý sâu sắc, khám phá ý nghĩa cuộc sống, hành trình tìm kiếm bản thân và những bài học về tình yêu, số phận. \"Nhà Giả Kim\" là tác phẩm nổi tiếng nhất của ông, được coi là một trong những cuốn sách truyền cảm hứng vĩ đại nhất mọi thời đại.', '1947-08-24', NULL, 'Brazil', NULL, NULL, NULL, NULL, NULL, 'Giải Bambi (2001), Giải Crystal Award (2007), Chevalier de l\'Ordre National de la Légion d\'Honneur', 'Nhà Giả Kim, Brida, Veronika Quyết Định Chết, Eleven Minutes, Chiến Binh Ánh Sáng', '2025-11-23 13:59:33', '2025-11-23 13:59:33'),
('TG007', 'Robert Kiyosaki là doanh nhân, nhà đầu tư, diễn giả và tác giả người Mỹ gốc Nhật. Ông nổi tiếng với cuốn sách \"Cha Giàu, Cha Nghèo\" (Rich Dad Poor Dad) xuất bản năm 1997 - một trong những cuốn sách tài chính cá nhân bán chạy nhất mọi thời đại với hơn 40 triệu bản trên toàn thế giới. Triết lý của Kiyosaki tập trung vào giáo dục tài chính, đầu tư bất động sản và xây dựng tài sản thay vì chỉ làm việc vì lương. Ông là người sáng lập công ty Rich Dad và tổ chức nhiều khóa học về tài chính.', '1947-04-08', NULL, 'Hoa Kỳ', NULL, NULL, NULL, NULL, NULL, NULL, 'Cha Giàu Cha Nghèo, Dòng Tiền Quyết Định Dòng Đời, Đầu Tư Vào Vàng Bạc, Nhà Đầu Tư Thông Minh', '2025-11-23 13:59:33', '2025-11-23 13:59:33'),
('TG011', 'Tô Hoài (1920-2014) là nhà văn Việt Nam, một trong những cây bút lớn của nền văn học thiếu nhi Việt Nam. Ông được trao tặng Giải thưởng Hồ Chí Minh về Văn học Nghệ thuật. Tác phẩm nổi tiếng nhất của ông là \"Dế Mèn Phiêu Lưu Ký\" - một kiệt tác văn học thiếu nhi Việt Nam, được xuất bản lần đầu năm 1941 và đã trở thành tác phẩm kinh điển gắn liền với tuổi thơ của nhiều thế hệ người Việt. Phong cách viết của Tô Hoài giản dị, gần gũi nhưng sâu sắc, mang đậm tính nhân văn.', '1920-09-27', '2014-07-06', 'Việt Nam', NULL, NULL, NULL, NULL, NULL, 'Giải thưởng Hồ Chí Minh về Văn học Nghệ thuật (1996), Giải thưởng Nhà nước về Văn học Nghệ thuật', 'Dế Mèn Phiêu Lưu Ký, Trên Đường Đi Tìm Mặt Trời, Những Ngày Thơ Ấu', '2025-11-23 13:59:33', '2025-11-23 13:59:33'),
('TG012', 'Antoine de Saint-Exupéry (1900-1944) là phi công và nhà văn người Pháp. Ông nổi tiếng với tác phẩm \"Hoàng Tử Bé\" (Le Petit Prince) xuất bản năm 1943 - một trong những cuốn sách được dịch và bán chạy nhất thế giới với hơn 200 triệu bản, được dịch ra hơn 300 ngôn ngữ. Tác phẩm này là một câu chuyện triết lý sâu sắc về tình yêu, tình bạn và ý nghĩa cuộc sống, được kể qua góc nhìn của một hoàng tử bé đến từ hành tinh khác. Saint-Exupéry mất tích trong một chuyến bay năm 1944 và được coi là anh hùng của nước Pháp.', '1900-06-29', '1944-07-31', 'Pháp', NULL, NULL, NULL, NULL, NULL, 'Giải Grand Prix du roman de l\'Académie française (1939)', 'Hoàng Tử Bé, Thành Lũy Bay, Thư Gửi Người Chưa Gặp, Phi Công Và Hành Tinh Nhỏ', '2025-11-23 13:59:33', '2025-11-23 13:59:33'),
('TG014', 'Stephen Hawking (1942-2018) là nhà vật lý lý thuyết, nhà vũ trụ học và tác giả người Anh. Ông được coi là một trong những nhà khoa học vĩ đại nhất thế kỷ 20, nổi tiếng với nghiên cứu về hố đen, vũ trụ học và lý thuyết tương đối. Mặc dù bị bệnh xơ cứng teo cơ một bên (ALS) từ năm 21 tuổi và phải sống trên xe lăn, Hawking vẫn tiếp tục nghiên cứu và viết sách. Cuốn \"Lược Sử Thời Gian\" của ông đã bán được hơn 10 triệu bản, trở thành một trong những cuốn sách khoa học phổ biến nhất mọi thời đại.', '1942-01-08', '2018-03-14', 'Anh', NULL, NULL, NULL, NULL, NULL, 'Giải Albert Einstein (1978), Giải Wolf (1988), Huân chương Tự do Tổng thống (2009), Giải Copley (2006)', 'Lược Sử Thời Gian, Vũ Trụ Trong Vỏ Hạt Dẻ, Thiết Kế Vĩ Đại, Lược Sử Thời Gian Ngắn Hơn', '2025-11-23 13:59:33', '2025-11-23 13:59:33');

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
('TK727', 'S0003'),
('TK727', 'S0004'),
('TK727', 'S0008');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `bai_viet`
--
ALTER TABLE `bai_viet`
  ADD PRIMARY KEY (`id_bai_viet`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `id_danh_muc` (`id_danh_muc`),
  ADD KEY `id_tk` (`id_tk`),
  ADD KEY `trang_thai` (`trang_thai`),
  ADD KEY `noi_bat` (`noi_bat`),
  ADD KEY `ngay_xuat_ban` (`ngay_xuat_ban`),
  ADD KEY `idx_slug_bai_viet` (`slug`),
  ADD KEY `idx_trang_thai_bai_viet` (`trang_thai`),
  ADD KEY `idx_noi_bat` (`noi_bat`),
  ADD KEY `idx_luot_xem` (`luot_xem`);

--
-- Chỉ mục cho bảng `bai_viet_tag`
--
ALTER TABLE `bai_viet_tag`
  ADD PRIMARY KEY (`id_bai_viet`,`id_tag`),
  ADD KEY `id_tag` (`id_tag`);

--
-- Chỉ mục cho bảng `binh_luan`
--
ALTER TABLE `binh_luan`
  ADD PRIMARY KEY (`id_bl`),
  ADD KEY `id_sach` (`id_sach`),
  ADD KEY `id_tk` (`id_tk`);

--
-- Chỉ mục cho bảng `binh_luan_bai_viet`
--
ALTER TABLE `binh_luan_bai_viet`
  ADD PRIMARY KEY (`id_binh_luan`),
  ADD KEY `id_bai_viet` (`id_bai_viet`),
  ADD KEY `id_tk` (`id_tk`),
  ADD KEY `trang_thai` (`trang_thai`);

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
-- Chỉ mục cho bảng `danh_muc_bai_viet`
--
ALTER TABLE `danh_muc_bai_viet`
  ADD PRIMARY KEY (`id_danh_muc`),
  ADD UNIQUE KEY `slug` (`slug`);

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
-- Chỉ mục cho bảng `tag`
--
ALTER TABLE `tag`
  ADD PRIMARY KEY (`id_tag`),
  ADD UNIQUE KEY `slug` (`slug`);

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
-- Chỉ mục cho bảng `thong_tin_sach`
--
ALTER TABLE `thong_tin_sach`
  ADD PRIMARY KEY (`id_sach`);

--
-- Chỉ mục cho bảng `thong_tin_tac_gia`
--
ALTER TABLE `thong_tin_tac_gia`
  ADD PRIMARY KEY (`id_tac_gia`),
  ADD KEY `idx_quoc_tich` (`quoc_tich`),
  ADD KEY `idx_ngay_sinh` (`ngay_sinh`);

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
-- AUTO_INCREMENT cho bảng `bai_viet`
--
ALTER TABLE `bai_viet`
  MODIFY `id_bai_viet` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `binh_luan_bai_viet`
--
ALTER TABLE `binh_luan_bai_viet`
  MODIFY `id_binh_luan` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `tag`
--
ALTER TABLE `tag`
  MODIFY `id_tag` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `thong_bao`
--
ALTER TABLE `thong_bao`
  MODIFY `id_thong_bao` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `trang_thai_don_hang`
--
ALTER TABLE `trang_thai_don_hang`
  MODIFY `id_trang_thai` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `bai_viet`
--
ALTER TABLE `bai_viet`
  ADD CONSTRAINT `fk_bv_danh_muc` FOREIGN KEY (`id_danh_muc`) REFERENCES `danh_muc_bai_viet` (`id_danh_muc`),
  ADD CONSTRAINT `fk_bv_tai_khoan` FOREIGN KEY (`id_tk`) REFERENCES `tai_khoan` (`id_tk`);

--
-- Các ràng buộc cho bảng `bai_viet_tag`
--
ALTER TABLE `bai_viet_tag`
  ADD CONSTRAINT `fk_bvt_bai_viet` FOREIGN KEY (`id_bai_viet`) REFERENCES `bai_viet` (`id_bai_viet`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_bvt_tag` FOREIGN KEY (`id_tag`) REFERENCES `tag` (`id_tag`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `binh_luan`
--
ALTER TABLE `binh_luan`
  ADD CONSTRAINT `binh_luan_ibfk_1` FOREIGN KEY (`id_sach`) REFERENCES `sach` (`id_sach`),
  ADD CONSTRAINT `binh_luan_ibfk_2` FOREIGN KEY (`id_tk`) REFERENCES `tai_khoan` (`id_tk`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `binh_luan_bai_viet`
--
ALTER TABLE `binh_luan_bai_viet`
  ADD CONSTRAINT `fk_blbv_bai_viet` FOREIGN KEY (`id_bai_viet`) REFERENCES `bai_viet` (`id_bai_viet`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_blbv_tai_khoan` FOREIGN KEY (`id_tk`) REFERENCES `tai_khoan` (`id_tk`) ON DELETE SET NULL;

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
-- Các ràng buộc cho bảng `thong_tin_sach`
--
ALTER TABLE `thong_tin_sach`
  ADD CONSTRAINT `fk_tts_sach` FOREIGN KEY (`id_sach`) REFERENCES `sach` (`id_sach`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `thong_tin_tac_gia`
--
ALTER TABLE `thong_tin_tac_gia`
  ADD CONSTRAINT `fk_ttg_tac_gia` FOREIGN KEY (`id_tac_gia`) REFERENCES `tac_gia` (`id_tac_gia`) ON DELETE CASCADE;

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
