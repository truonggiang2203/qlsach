-- =========================================================
-- CẬP NHẬT BẢNG TÁC GIẢ - THÊM THÔNG TIN CHI TIẾT
-- =========================================================

-- Tạo bảng thông tin chi tiết tác giả
CREATE TABLE IF NOT EXISTS `thong_tin_tac_gia` (
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
  `ngay_cap_nhat` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_tac_gia`),
  CONSTRAINT `fk_ttg_tac_gia` FOREIGN KEY (`id_tac_gia`) REFERENCES `tac_gia` (`id_tac_gia`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Thêm dữ liệu mẫu cho một số tác giả nổi tiếng
INSERT INTO `thong_tin_tac_gia` (`id_tac_gia`, `tieu_su`, `ngay_sinh`, `quoc_tich`, `giai_thuong`, `tac_pham_noi_bat`) VALUES
('TG001', 'Nguyễn Nhật Ánh là nhà văn Việt Nam nổi tiếng với các tác phẩm văn học thiếu nhi và tuổi teen. Ông được biết đến với phong cách viết giản dị, gần gũi và đầy cảm xúc, khắc họa chân thực tâm hồn tuổi trẻ và những kỷ niệm tuổi thơ.', '1955-05-07', 'Việt Nam', 'Giải thưởng Văn học ASEAN 2010', 'Cho Tôi Xin Một Vé Đi Tuổi Thơ, Kính Vạn Hoa, Mắt Biếc, Tôi Thấy Hoa Vàng Trên Cỏ Xanh'),

('TG002', 'Haruki Murakami là tiểu thuyết gia và nhà văn người Nhật Bản. Tác phẩm của ông thường kết hợp yếu tố hiện thực và siêu thực, khám phá những chủ đề về cô đơn, tình yêu và sự tìm kiếm ý nghĩa cuộc sống.', '1949-01-12', 'Nhật Bản', 'Giải Franz Kafka, Giải Jerusalem, Giải Hans Christian Andersen', 'Rừng Na Uy, Kafka Bên Bờ Biển, 1Q84, Biên Niên Ký Chim Vặn Dây Cót'),

('TG003', 'Dale Carnegie là nhà văn và diễn giả người Mỹ, nổi tiếng với các tác phẩm về kỹ năng giao tiếp và phát triển bản thân. Cuốn sách "Đắc Nhân Tâm" của ông đã trở thành một trong những cuốn sách bán chạy nhất mọi thời đại.', '1888-11-24', 'Hoa Kỳ', NULL, 'Đắc Nhân Tâm, Dám Nghĩ Lớn, Đừng Bao Giờ Sợ Hãi'),

('TG004', 'J.K. Rowling là tiểu thuyết gia người Anh, tác giả của series Harry Potter - một trong những series sách bán chạy nhất trong lịch sử. Tác phẩm của bà đã được dịch ra hơn 80 ngôn ngữ và chuyển thể thành phim ăn khách.', '1965-07-31', 'Anh', 'Huân chương Đế chế Anh, Giải Hugo, Giải British Book Awards', 'Harry Potter (7 tập), The Casual Vacancy, Cormoran Strike series'),

('TG005', 'Yuval Noah Harari là sử học gia và tác giả người Israel. Ông nổi tiếng với các tác phẩm về lịch sử loài người, công nghệ và tương lai của nhân loại, được viết theo phong cách dễ hiểu và hấp dẫn.', '1976-02-24', 'Israel', NULL, 'Sapiens: Lược Sử Loài Người, Homo Deus, 21 Bài Học Cho Thế Kỷ 21'),

('TG006', 'Paulo Coelho là tiểu thuyết gia và nhà thơ người Brazil. Tác phẩm của ông thường mang tính triết lý sâu sắc, khám phá ý nghĩa cuộc sống và hành trình tìm kiếm bản thân.', '1947-08-24', 'Brazil', 'Giải Bambi, Giải Crystal Award', 'Nhà Giả Kim, Brida, Veronika Quyết Định Chết, Eleven Minutes'),

('TG007', 'Robert Kiyosaki là doanh nhân, nhà đầu tư và tác giả người Mỹ. Ông nổi tiếng với cuốn sách "Cha Giàu, Cha Nghèo" - một trong những cuốn sách tài chính cá nhân bán chạy nhất mọi thời đại.', '1947-04-08', 'Hoa Kỳ', NULL, 'Cha Giàu Cha Nghèo, Dòng Tiền Quyết Định Dòng Đời, Đầu Tư Vào Vàng Bạc'),

('TG011', 'Tô Hoài là nhà văn Việt Nam, một trong những cây bút lớn của nền văn học thiếu nhi Việt Nam. Tác phẩm nổi tiếng nhất của ông là "Dế Mèn Phiêu Lưu Ký" - một kiệt tác văn học thiếu nhi.', '1920-09-27', 'Việt Nam', 'Giải thưởng Hồ Chí Minh về Văn học Nghệ thuật', 'Dế Mèn Phiêu Lưu Ký, Trên Đường Đi Tìm Mặt Trời'),

('TG012', 'Antoine de Saint-Exupéry là phi công và nhà văn người Pháp. Tác phẩm nổi tiếng nhất của ông là "Hoàng Tử Bé" - một trong những cuốn sách được dịch và bán chạy nhất thế giới.', '1900-06-29', 'Pháp', 'Giải Grand Prix du roman de l''Académie française', 'Hoàng Tử Bé, Thành Lũy Bay, Thư Gửi Người Chưa Gặp'),

('TG014', 'Stephen Hawking là nhà vật lý lý thuyết, nhà vũ trụ học và tác giả người Anh. Ông được coi là một trong những nhà khoa học vĩ đại nhất thế kỷ 20, nổi tiếng với nghiên cứu về hố đen và vũ trụ học.', '1942-01-08', 'Anh', 'Giải Albert Einstein, Giải Wolf, Huân chương Tự do Tổng thống', 'Lược Sử Thời Gian, Vũ Trụ Trong Vỏ Hạt Dẻ, Thiết Kế Vĩ Đại');

-- Tạo index để tăng tốc độ truy vấn
CREATE INDEX idx_quoc_tich ON thong_tin_tac_gia(quoc_tich);
CREATE INDEX idx_ngay_sinh ON thong_tin_tac_gia(ngay_sinh);

-- =========================================================
-- HƯỚNG DẪN SỬ DỤNG
-- =========================================================
-- 1. Chạy file SQL này trong phpMyAdmin hoặc MySQL Workbench
-- 2. Bảng thong_tin_tac_gia sẽ được tạo với các trường thông tin chi tiết
-- 3. Dữ liệu mẫu cho 10 tác giả nổi tiếng đã được thêm vào
-- 4. Có thể thêm/sửa thông tin tác giả thông qua trang admin hoặc trực tiếp trong database
