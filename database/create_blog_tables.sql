-- =========================================================
-- TẠO HỆ THỐNG TIN TỨC / BLOG
-- =========================================================

-- --------------------------------------------------------
-- 1. BẢNG DANH MỤC BÀI VIẾT
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `danh_muc_bai_viet` (
  `id_danh_muc` varchar(5) NOT NULL,
  `ten_danh_muc` varchar(100) NOT NULL,
  `slug` varchar(150) NOT NULL COMMENT 'URL-friendly name',
  `mo_ta` text DEFAULT NULL,
  `thu_tu` int(11) DEFAULT 0,
  `trang_thai` tinyint(1) DEFAULT 1 COMMENT '1: Active, 0: Inactive',
  `ngay_tao` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id_danh_muc`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Thêm dữ liệu mẫu cho danh mục
INSERT INTO `danh_muc_bai_viet` (`id_danh_muc`, `ten_danh_muc`, `slug`, `mo_ta`, `thu_tu`) VALUES
('DM001', 'Review Sách', 'review-sach', 'Đánh giá và nhận xét về các cuốn sách hay', 1),
('DM002', 'Tin Tức Văn Học', 'tin-tuc-van-hoc', 'Tin tức mới nhất về giới văn học', 2),
('DM003', 'Tác Giả & Tác Phẩm', 'tac-gia-tac-pham', 'Giới thiệu về tác giả và tác phẩm nổi tiếng', 3),
('DM004', 'Mẹo Đọc Sách', 'meo-doc-sach', 'Chia sẻ kinh nghiệm và phương pháp đọc sách hiệu quả', 4),
('DM005', 'Sự Kiện', 'su-kien', 'Thông tin về các sự kiện văn học, hội sách', 5);

-- --------------------------------------------------------
-- 2. BẢNG BÀI VIẾT
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `bai_viet` (
  `id_bai_viet` int(11) NOT NULL AUTO_INCREMENT,
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
  `ngay_xuat_ban` datetime DEFAULT NULL,
  PRIMARY KEY (`id_bai_viet`),
  UNIQUE KEY `slug` (`slug`),
  KEY `id_danh_muc` (`id_danh_muc`),
  KEY `id_tk` (`id_tk`),
  KEY `trang_thai` (`trang_thai`),
  KEY `noi_bat` (`noi_bat`),
  KEY `ngay_xuat_ban` (`ngay_xuat_ban`),
  CONSTRAINT `fk_bv_danh_muc` FOREIGN KEY (`id_danh_muc`) REFERENCES `danh_muc_bai_viet` (`id_danh_muc`),
  CONSTRAINT `fk_bv_tai_khoan` FOREIGN KEY (`id_tk`) REFERENCES `tai_khoan` (`id_tk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Thêm dữ liệu mẫu cho bài viết
INSERT INTO `bai_viet` (`id_danh_muc`, `id_tk`, `tieu_de`, `slug`, `tom_tat`, `noi_dung`, `luot_xem`, `trang_thai`, `noi_bat`, `meta_title`, `meta_description`, `meta_keywords`, `ngay_xuat_ban`) VALUES
('DM001', 'AD001', 'Review: Rừng Na Uy - Kiệt tác của Haruki Murakami', 'review-rung-na-uy-kiet-tac-cua-haruki-murakami', 'Rừng Na Uy là một trong những tác phẩm nổi tiếng nhất của Haruki Murakami, kể về câu chuyện tình yêu đầy cảm xúc và nỗi đau của tuổi trẻ.', '<h2>Giới thiệu</h2><p>Rừng Na Uy (Norwegian Wood) là tiểu thuyết được xuất bản năm 1987 của nhà văn Nhật Bản Haruki Murakami. Đây là tác phẩm bán chạy nhất của ông với hơn 10 triệu bản được bán ra trên toàn thế giới.</p><h2>Nội dung</h2><p>Câu chuyện xoay quanh Watanabe Toru, một sinh viên đại học ở Tokyo vào cuối những năm 1960. Anh yêu Naoko, bạn gái của người bạn thân đã tự tử. Naoko cũng đang phải vật lộn với những vấn đề tâm lý của riêng mình...</p><h2>Đánh giá</h2><p>Rừng Na Uy là một tác phẩm đầy cảm xúc, khắc họa chân thực nỗi đau, sự mất mát và tình yêu của tuổi trẻ. Phong cách viết của Murakami vừa mộng mơ vừa chân thực, tạo nên một câu chuyện khó quên.</p>', 1250, 'published', 1, 'Review Rừng Na Uy - Haruki Murakami | Nhà Sách Online', 'Đánh giá chi tiết về tiểu thuyết Rừng Na Uy của Haruki Murakami - Tác phẩm kinh điển về tình yêu và tuổi trẻ', 'rừng na uy, haruki murakami, review sách, tiểu thuyết nhật bản', '2025-11-20 10:00:00'),

('DM002', 'AD001', 'Haruki Murakami đoạt giải văn học quốc tế 2024', 'haruki-murakami-doat-giai-van-hoc-quoc-te-2024', 'Nhà văn Nhật Bản Haruki Murakami vừa được trao giải văn học quốc tế danh giá cho những đóng góp xuất sắc trong sự nghiệp văn chương.', '<h2>Tin tức</h2><p>Haruki Murakami, một trong những nhà văn được yêu thích nhất thế giới, vừa được trao giải văn học quốc tế danh giá tại lễ trao giải diễn ra tại Paris, Pháp.</p><h2>Thành tựu</h2><p>Với hơn 40 năm cống hiến cho văn học, Murakami đã tạo ra những tác phẩm để đời như Rừng Na Uy, Kafka Bên Bờ Biển, 1Q84... Tác phẩm của ông đã được dịch ra hơn 50 ngôn ngữ.</p>', 850, 'published', 1, 'Haruki Murakami đoạt giải văn học quốc tế 2024', 'Tin tức mới nhất về giải thưởng văn học của Haruki Murakami năm 2024', 'haruki murakami, giải văn học, tin tức văn học', '2025-11-21 14:30:00'),

('DM003', 'AD001', 'Nguyễn Nhật Ánh - Người kể chuyện tuổi thơ', 'nguyen-nhat-anh-nguoi-ke-chuyen-tuoi-tho', 'Tìm hiểu về Nguyễn Nhật Ánh - nhà văn được yêu thích nhất Việt Nam với những tác phẩm gắn liền với tuổi thơ của nhiều thế hệ.', '<h2>Tiểu sử</h2><p>Nguyễn Nhật Ánh sinh năm 1955 tại Quảng Nam. Ông là một trong những nhà văn được yêu thích nhất Việt Nam, đặc biệt với độc giả trẻ.</p><h2>Tác phẩm nổi bật</h2><p>Các tác phẩm của Nguyễn Nhật Ánh như "Cho Tôi Xin Một Vé Đi Tuổi Thơ", "Mắt Biếc", "Tôi Thấy Hoa Vàng Trên Cỏ Xanh" đã trở thành những tác phẩm kinh điển của văn học Việt Nam.</p><h2>Phong cách</h2><p>Phong cách viết của Nguyễn Nhật Ánh giản dị, gần gũi nhưng đầy cảm xúc, khắc họa chân thực tâm hồn tuổi trẻ và những kỷ niệm tuổi thơ.</p>', 2100, 'published', 1, 'Nguyễn Nhật Ánh - Nhà văn tuổi thơ của người Việt', 'Tìm hiểu về cuộc đời và sự nghiệp của nhà văn Nguyễn Nhật Ánh', 'nguyễn nhật ánh, tác giả việt nam, văn học thiếu nhi', '2025-11-19 09:00:00'),

('DM004', 'AD001', '5 Mẹo Đọc Sách Hiệu Quả Cho Người Bận Rộn', '5-meo-doc-sach-hieu-qua-cho-nguoi-ban-ron', 'Chia sẻ những phương pháp đọc sách hiệu quả giúp bạn tiếp thu kiến thức tốt hơn ngay cả khi bận rộn.', '<h2>1. Đặt mục tiêu rõ ràng</h2><p>Trước khi đọc, hãy xác định rõ bạn muốn học được gì từ cuốn sách. Điều này giúp bạn tập trung vào những phần quan trọng.</p><h2>2. Đọc vào thời điểm phù hợp</h2><p>Chọn thời điểm trong ngày khi bạn tỉnh táo nhất để đọc. Có thể là buổi sáng sớm hoặc trước khi đi ngủ.</p><h2>3. Ghi chú và đánh dấu</h2><p>Đừng ngại ghi chú hoặc đánh dấu những đoạn quan trọng. Điều này giúp bạn dễ dàng ôn lại sau này.</p><h2>4. Áp dụng kỹ thuật Pomodoro</h2><p>Đọc trong 25 phút, nghỉ 5 phút. Kỹ thuật này giúp duy trì sự tập trung.</p><h2>5. Thảo luận và chia sẻ</h2><p>Chia sẻ những gì bạn đọc được với người khác giúp củng cố kiến thức và tạo động lực đọc tiếp.</p>', 1680, 'published', 0, '5 Mẹo Đọc Sách Hiệu Quả Cho Người Bận Rộn', 'Hướng dẫn cách đọc sách hiệu quả ngay cả khi bạn bận rộn', 'mẹo đọc sách, đọc sách hiệu quả, kỹ năng đọc', '2025-11-18 16:00:00'),

('DM005', 'AD001', 'Hội Sách TP.HCM 2025 - Sự Kiện Văn Hóa Lớn Nhất Năm', 'hoi-sach-tphcm-2025-su-kien-van-hoa-lon-nhat-nam', 'Hội sách TP.HCM 2025 sẽ diễn ra từ ngày 1-7/4/2025 tại Công viên Tao Đàn với hàng trăm gian hàng sách và nhiều hoạt động văn hóa hấp dẫn.', '<h2>Thông tin sự kiện</h2><p>Hội sách TP.HCM là sự kiện văn hóa lớn nhất trong năm, thu hút hàng triệu lượt khách tham quan.</p><h2>Thời gian và địa điểm</h2><ul><li>Thời gian: 1-7/4/2025</li><li>Địa điểm: Công viên Tao Đàn, Quận 1, TP.HCM</li><li>Giờ mở cửa: 8:00 - 21:00 hàng ngày</li></ul><h2>Hoạt động nổi bật</h2><ul><li>Gặp gỡ tác giả nổi tiếng</li><li>Ký tặng sách</li><li>Tọa đàm văn học</li><li>Biểu diễn nghệ thuật</li><li>Giảm giá sách lên đến 50%</li></ul>', 950, 'published', 1, 'Hội Sách TP.HCM 2025 - Lễ Hội Sách Lớn Nhất Năm', 'Thông tin chi tiết về Hội sách TP.HCM 2025 - Sự kiện văn hóa không thể bỏ lỡ', 'hội sách, hội sách tphcm, sự kiện văn hóa, lễ hội sách', '2025-11-22 08:00:00');

-- --------------------------------------------------------
-- 3. BẢNG TAG (NHÃN)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `tag` (
  `id_tag` int(11) NOT NULL AUTO_INCREMENT,
  `ten_tag` varchar(50) NOT NULL,
  `slug` varchar(70) NOT NULL,
  PRIMARY KEY (`id_tag`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Thêm dữ liệu mẫu cho tag
INSERT INTO `tag` (`ten_tag`, `slug`) VALUES
('Văn học Nhật Bản', 'van-hoc-nhat-ban'),
('Văn học Việt Nam', 'van-hoc-viet-nam'),
('Tiểu thuyết', 'tieu-thuyet'),
('Review sách', 'review-sach'),
('Tác giả nổi tiếng', 'tac-gia-noi-tieng'),
('Kỹ năng đọc', 'ky-nang-doc'),
('Sự kiện', 'su-kien'),
('Giải thưởng', 'giai-thuong'),
('Bestseller', 'bestseller'),
('Văn học thiếu nhi', 'van-hoc-thieu-nhi');

-- --------------------------------------------------------
-- 4. BẢNG LIÊN KẾT BÀI VIẾT - TAG
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `bai_viet_tag` (
  `id_bai_viet` int(11) NOT NULL,
  `id_tag` int(11) NOT NULL,
  PRIMARY KEY (`id_bai_viet`, `id_tag`),
  KEY `id_tag` (`id_tag`),
  CONSTRAINT `fk_bvt_bai_viet` FOREIGN KEY (`id_bai_viet`) REFERENCES `bai_viet` (`id_bai_viet`) ON DELETE CASCADE,
  CONSTRAINT `fk_bvt_tag` FOREIGN KEY (`id_tag`) REFERENCES `tag` (`id_tag`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Thêm dữ liệu mẫu cho bài viết - tag
INSERT INTO `bai_viet_tag` (`id_bai_viet`, `id_tag`) VALUES
(1, 1), (1, 3), (1, 4), (1, 5),
(2, 1), (2, 5), (2, 8),
(3, 2), (3, 5), (3, 10),
(4, 6),
(5, 7);

-- --------------------------------------------------------
-- 5. BẢNG BÌNH LUẬN BÀI VIẾT
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `binh_luan_bai_viet` (
  `id_binh_luan` int(11) NOT NULL AUTO_INCREMENT,
  `id_bai_viet` int(11) NOT NULL,
  `id_tk` varchar(5) DEFAULT NULL,
  `ten_nguoi_binh_luan` varchar(100) DEFAULT NULL COMMENT 'Nếu không đăng nhập',
  `email` varchar(100) DEFAULT NULL COMMENT 'Nếu không đăng nhập',
  `noi_dung` text NOT NULL,
  `trang_thai` varchar(20) DEFAULT 'pending' COMMENT 'pending, approved, rejected',
  `ngay_tao` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id_binh_luan`),
  KEY `id_bai_viet` (`id_bai_viet`),
  KEY `id_tk` (`id_tk`),
  KEY `trang_thai` (`trang_thai`),
  CONSTRAINT `fk_blbv_bai_viet` FOREIGN KEY (`id_bai_viet`) REFERENCES `bai_viet` (`id_bai_viet`) ON DELETE CASCADE,
  CONSTRAINT `fk_blbv_tai_khoan` FOREIGN KEY (`id_tk`) REFERENCES `tai_khoan` (`id_tk`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tạo index để tăng tốc độ truy vấn
CREATE INDEX idx_slug_bai_viet ON bai_viet(slug);
CREATE INDEX idx_trang_thai_bai_viet ON bai_viet(trang_thai);
CREATE INDEX idx_noi_bat ON bai_viet(noi_bat);
CREATE INDEX idx_luot_xem ON bai_viet(luot_xem);

-- =========================================================
-- HOÀN TẤT
-- =========================================================
-- Đã tạo thành công hệ thống Blog/Tin tức với:
-- 1. Bảng danh_muc_bai_viet (5 danh mục)
-- 2. Bảng bai_viet (5 bài viết mẫu)
-- 3. Bảng tag (10 tag)
-- 4. Bảng bai_viet_tag (liên kết)
-- 5. Bảng binh_luan_bai_viet
-- 
-- Tính năng SEO:
-- - Meta title, description, keywords
-- - Slug URL-friendly
-- - Bài viết nổi bật
-- - Lượt xem
-- - Tag system
