-- =========================================================
-- CẬP NHẬT DATABASE - THÊM CÁC BẢNG MỚI
-- Chạy file này sau khi đã import qlsach-21-11-2025.sql
-- =========================================================

-- --------------------------------------------------------
-- 1. TẠO BẢNG THÔNG TIN SÁCH (nếu chưa có)
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `thong_tin_sach` (
  `id_sach` varchar(5) NOT NULL,
  `so_trang` int(11) DEFAULT NULL,
  `trong_luong` int(11) DEFAULT NULL COMMENT 'Đơn vị: gram',
  `kich_thuoc` varchar(50) DEFAULT NULL COMMENT 'VD: 14.5 x 20.5 cm',
  `hinh_thuc` varchar(50) DEFAULT NULL COMMENT 'Bìa cứng, bìa mềm, ebook',
  `nam_xuat_ban` year(4) DEFAULT NULL,
  PRIMARY KEY (`id_sach`),
  CONSTRAINT `fk_tts_sach` FOREIGN KEY (`id_sach`) REFERENCES `sach` (`id_sach`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Thêm dữ liệu mẫu cho bảng thong_tin_sach
INSERT INTO `thong_tin_sach` (`id_sach`, `so_trang`, `trong_luong`, `kich_thuoc`, `hinh_thuc`, `nam_xuat_ban`) VALUES
('S0001', 420, 450, '14.5 x 20.5 cm', 'Bìa mềm', 2018),
('S0002', 280, 320, '14 x 20 cm', 'Bìa mềm', 2015),
('S0003', 224, 250, '13 x 20 cm', 'Bìa mềm', 2013),
('S0004', 336, 380, '15 x 22 cm', 'Bìa cứng', 2020),
('S0005', 432, 480, '14 x 21 cm', 'Bìa mềm', 2010),
('S0006', 336, 360, '14.5 x 20.5 cm', 'Bìa mềm', 2017),
('S0007', 320, 340, '14 x 20 cm', 'Bìa mềm', 2016),
('S0008', 400, 420, '15 x 22 cm', 'Bìa cứng', 2019),
('S0009', 256, 280, '14 x 20 cm', 'Bìa mềm', 2014),
('S0010', 288, 310, '14 x 20 cm', 'Bìa mềm', 2015),
('S0011', 320, 350, '14.5 x 20.5 cm', 'Bìa mềm', 2012),
('S0012', 304, 330, '14 x 20 cm', 'Bìa mềm', 2018),
('S0013', 384, 410, '14.5 x 20.5 cm', 'Bìa mềm', 2016),
('S0014', 296, 320, '14 x 20 cm', 'Bìa mềm', 2019),
('S0015', 240, 260, '13 x 19 cm', 'Bìa mềm', 2017),
('S0016', 192, 220, '14 x 20 cm', 'Bìa mềm', 2010),
('S0017', 128, 150, '13 x 19 cm', 'Bìa mềm', 2015),
('S0018', 96, 120, '11.5 x 17.5 cm', 'Bìa mềm', 2020),
('S0019', 240, 270, '14 x 20 cm', 'Bìa mềm', 2016),
('S0020', 48, 80, '21 x 28 cm', 'Bìa mềm', 2021),
('S0021', 512, 580, '15 x 23 cm', 'Bìa cứng', 2018),
('S0022', 208, 240, '14 x 20 cm', 'Bìa mềm', 2016),
('S0023', 528, 600, '15 x 23 cm', 'Bìa cứng', 2017),
('S0024', 256, 290, '14 x 20 cm', 'Bìa mềm', 2015),
('S0025', 368, 400, '14.5 x 20.5 cm', 'Bìa mềm', 2019);

-- --------------------------------------------------------
-- 2. TẠO BẢNG THÔNG TIN TÁC GIẢ
-- --------------------------------------------------------

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

-- Thêm dữ liệu mẫu cho các tác giả nổi tiếng
INSERT INTO `thong_tin_tac_gia` (`id_tac_gia`, `tieu_su`, `ngay_sinh`, `ngay_mat`, `quoc_tich`, `giai_thuong`, `tac_pham_noi_bat`) VALUES
('TG001', 'Nguyễn Nhật Ánh là nhà văn Việt Nam nổi tiếng với các tác phẩm văn học thiếu nhi và tuổi teen. Ông được biết đến với phong cách viết giản dị, gần gũi và đầy cảm xúc, khắc họa chân thực tâm hồn tuổi trẻ và những kỷ niệm tuổi thơ. Các tác phẩm của ông thường mang đến cho độc giả cảm giác ấm áp, hoài niệm và sâu sắc về cuộc sống.', '1955-05-07', NULL, 'Việt Nam', 'Giải thưởng Văn học ASEAN 2010, Giải thưởng Hội Nhà văn Việt Nam', 'Cho Tôi Xin Một Vé Đi Tuổi Thơ, Kính Vạn Hoa, Mắt Biếc, Tôi Thấy Hoa Vàng Trên Cỏ Xanh, Cô Gái Đến Từ Hôm Qua'),

('TG002', 'Haruki Murakami là tiểu thuyết gia và nhà văn người Nhật Bản, một trong những tác giả được dịch và đọc nhiều nhất trên thế giới. Tác phẩm của ông thường kết hợp yếu tố hiện thực và siêu thực, khám phá những chủ đề về cô đơn, tình yêu, sự tìm kiếm ý nghĩa cuộc sống và những bí ẩn của tâm hồn con người. Phong cách viết của Murakami được đánh giá là độc đáo, mộng mơ và đầy chất thơ.', '1949-01-12', NULL, 'Nhật Bản', 'Giải Franz Kafka (2006), Giải Jerusalem (2009), Giải Hans Christian Andersen (2016)', 'Rừng Na Uy, Kafka Bên Bờ Biển, 1Q84, Biên Niên Ký Chim Vặn Dây Cót, Sau Cơn Động Đất'),

('TG003', 'Dale Carnegie (1888-1955) là nhà văn và diễn giả người Mỹ, nổi tiếng với các tác phẩm về kỹ năng giao tiếp, nghệ thuật ứng xử và phát triển bản thân. Cuốn sách "Đắc Nhân Tâm" (How to Win Friends and Influence People) của ông xuất bản năm 1936 đã trở thành một trong những cuốn sách bán chạy nhất mọi thời đại với hơn 30 triệu bản được bán ra trên toàn thế giới. Triết lý của Carnegie tập trung vào việc xây dựng mối quan hệ tích cực và phát triển kỹ năng lãnh đạo.', '1888-11-24', '1955-11-01', 'Hoa Kỳ', NULL, 'Đắc Nhân Tâm, Dám Nghĩ Lớn, Đừng Bao Giờ Sợ Hãi, Cách Để Không Lo Lắng Và Bắt Đầu Cuộc Sống'),

('TG004', 'J.K. Rowling là tiểu thuyết gia người Anh, tác giả của series Harry Potter - một trong những series sách bán chạy nhất trong lịch sử với hơn 500 triệu bản được bán ra trên toàn thế giới. Tác phẩm của bà đã được dịch ra hơn 80 ngôn ngữ và chuyển thể thành 8 bộ phim ăn khách. Rowling được biết đến với khả năng xây dựng thế giới phép thuật phong phú, các nhân vật sống động và cốt truyện hấp dẫn. Bà cũng là một trong những phụ nữ giàu nhất thế giới nhờ thành công của Harry Potter.', '1965-07-31', NULL, 'Anh', 'Huân chương Đế chế Anh (OBE), Giải Hugo, Giải British Book Awards, Giải Locus', 'Harry Potter (7 tập), The Casual Vacancy, Cormoran Strike series (dưới bút danh Robert Galbraith)'),

('TG005', 'Yuval Noah Harari là sử học gia, triết gia và tác giả người Israel. Ông là giáo sư tại Đại học Hebrew ở Jerusalem, chuyên về lịch sử thế giới và các quá trình vĩ mô. Harari nổi tiếng với các tác phẩm về lịch sử loài người, công nghệ và tương lai của nhân loại, được viết theo phong cách dễ hiểu, hấp dẫn và đầy tính tư duy phản biện. Cuốn "Sapiens" của ông đã trở thành hiện tượng xuất bản toàn cầu với hơn 20 triệu bản được bán ra.', '1976-02-24', NULL, 'Israel', 'Giải Polonsky Prize for Creativity and Originality (2009, 2012)', 'Sapiens: Lược Sử Loài Người, Homo Deus: Lược Sử Tương Lai, 21 Bài Học Cho Thế Kỷ 21'),

('TG006', 'Paulo Coelho là tiểu thuyết gia, nhà thơ và nhạc sĩ người Brazil. Ông là một trong những tác giả được đọc nhiều nhất thế giới với hơn 320 triệu sách được bán ra, được dịch sang 88 ngôn ngữ. Tác phẩm của ông thường mang tính triết lý sâu sắc, khám phá ý nghĩa cuộc sống, hành trình tìm kiếm bản thân và những bài học về tình yêu, số phận. "Nhà Giả Kim" là tác phẩm nổi tiếng nhất của ông, được coi là một trong những cuốn sách truyền cảm hứng vĩ đại nhất mọi thời đại.', '1947-08-24', NULL, 'Brazil', 'Giải Bambi (2001), Giải Crystal Award (2007), Chevalier de l''Ordre National de la Légion d''Honneur', 'Nhà Giả Kim, Brida, Veronika Quyết Định Chết, Eleven Minutes, Chiến Binh Ánh Sáng'),

('TG007', 'Robert Kiyosaki là doanh nhân, nhà đầu tư, diễn giả và tác giả người Mỹ gốc Nhật. Ông nổi tiếng với cuốn sách "Cha Giàu, Cha Nghèo" (Rich Dad Poor Dad) xuất bản năm 1997 - một trong những cuốn sách tài chính cá nhân bán chạy nhất mọi thời đại với hơn 40 triệu bản trên toàn thế giới. Triết lý của Kiyosaki tập trung vào giáo dục tài chính, đầu tư bất động sản và xây dựng tài sản thay vì chỉ làm việc vì lương. Ông là người sáng lập công ty Rich Dad và tổ chức nhiều khóa học về tài chính.', '1947-04-08', NULL, 'Hoa Kỳ', NULL, 'Cha Giàu Cha Nghèo, Dòng Tiền Quyết Định Dòng Đời, Đầu Tư Vào Vàng Bạc, Nhà Đầu Tư Thông Minh'),

('TG011', 'Tô Hoài (1920-2014) là nhà văn Việt Nam, một trong những cây bút lớn của nền văn học thiếu nhi Việt Nam. Ông được trao tặng Giải thưởng Hồ Chí Minh về Văn học Nghệ thuật. Tác phẩm nổi tiếng nhất của ông là "Dế Mèn Phiêu Lưu Ký" - một kiệt tác văn học thiếu nhi Việt Nam, được xuất bản lần đầu năm 1941 và đã trở thành tác phẩm kinh điển gắn liền với tuổi thơ của nhiều thế hệ người Việt. Phong cách viết của Tô Hoài giản dị, gần gũi nhưng sâu sắc, mang đậm tính nhân văn.', '1920-09-27', '2014-07-06', 'Việt Nam', 'Giải thưởng Hồ Chí Minh về Văn học Nghệ thuật (1996), Giải thưởng Nhà nước về Văn học Nghệ thuật', 'Dế Mèn Phiêu Lưu Ký, Trên Đường Đi Tìm Mặt Trời, Những Ngày Thơ Ấu'),

('TG012', 'Antoine de Saint-Exupéry (1900-1944) là phi công và nhà văn người Pháp. Ông nổi tiếng với tác phẩm "Hoàng Tử Bé" (Le Petit Prince) xuất bản năm 1943 - một trong những cuốn sách được dịch và bán chạy nhất thế giới với hơn 200 triệu bản, được dịch ra hơn 300 ngôn ngữ. Tác phẩm này là một câu chuyện triết lý sâu sắc về tình yêu, tình bạn và ý nghĩa cuộc sống, được kể qua góc nhìn của một hoàng tử bé đến từ hành tinh khác. Saint-Exupéry mất tích trong một chuyến bay năm 1944 và được coi là anh hùng của nước Pháp.', '1900-06-29', '1944-07-31', 'Pháp', 'Giải Grand Prix du roman de l''Académie française (1939)', 'Hoàng Tử Bé, Thành Lũy Bay, Thư Gửi Người Chưa Gặp, Phi Công Và Hành Tinh Nhỏ'),

('TG014', 'Stephen Hawking (1942-2018) là nhà vật lý lý thuyết, nhà vũ trụ học và tác giả người Anh. Ông được coi là một trong những nhà khoa học vĩ đại nhất thế kỷ 20, nổi tiếng với nghiên cứu về hố đen, vũ trụ học và lý thuyết tương đối. Mặc dù bị bệnh xơ cứng teo cơ một bên (ALS) từ năm 21 tuổi và phải sống trên xe lăn, Hawking vẫn tiếp tục nghiên cứu và viết sách. Cuốn "Lược Sử Thời Gian" của ông đã bán được hơn 10 triệu bản, trở thành một trong những cuốn sách khoa học phổ biến nhất mọi thời đại.', '1942-01-08', '2018-03-14', 'Anh', 'Giải Albert Einstein (1978), Giải Wolf (1988), Huân chương Tự do Tổng thống (2009), Giải Copley (2006)', 'Lược Sử Thời Gian, Vũ Trụ Trong Vỏ Hạt Dẻ, Thiết Kế Vĩ Đại, Lược Sử Thời Gian Ngắn Hơn');

-- Tạo index để tăng tốc độ truy vấn
CREATE INDEX IF NOT EXISTS idx_quoc_tich ON thong_tin_tac_gia(quoc_tich);
CREATE INDEX IF NOT EXISTS idx_ngay_sinh ON thong_tin_tac_gia(ngay_sinh);

-- =========================================================
-- HOÀN TẤT
-- =========================================================
-- Đã tạo thành công:
-- 1. Bảng thong_tin_sach với dữ liệu mẫu cho 25 cuốn sách
-- 2. Bảng thong_tin_tac_gia với dữ liệu chi tiết cho 10 tác giả nổi tiếng
-- 
-- Bạn có thể kiểm tra bằng cách chạy:
-- SELECT * FROM thong_tin_sach;
-- SELECT * FROM thong_tin_tac_gia;
