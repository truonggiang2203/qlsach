# 📚 HƯỚNG DẪN SỬ DỤNG TÍNH NĂNG TÁC GIẢ

## 🎯 Tổng quan

Hệ thống đã được bổ sung tính năng quản lý và hiển thị thông tin chi tiết về tác giả, bao gồm:
- Danh sách tất cả tác giả
- Trang chi tiết tác giả với tiểu sử đầy đủ
- Danh sách tác phẩm của từng tác giả
- Tìm kiếm tác giả
- Link từ trang sách đến trang tác giả

## 📋 Cài đặt Database

### Bước 1: Chạy file SQL
1. Mở phpMyAdmin
2. Chọn database `qlsach`
3. Vào tab "SQL"
4. Copy nội dung file `database/update_tac_gia_table.sql`
5. Paste và click "Go"

### Bước 2: Kiểm tra
Sau khi chạy SQL, bạn sẽ có:
- Bảng `thong_tin_tac_gia` với các trường:
  - `id_tac_gia` (khóa chính, liên kết với bảng tac_gia)
  - `tieu_su` (tiểu sử tác giả)
  - `ngay_sinh` (ngày sinh)
  - `ngay_mat` (ngày mất - nếu có)
  - `quoc_tich` (quốc tịch)
  - `anh_dai_dien` (đường dẫn ảnh)
  - `website`, `facebook`, `twitter`, `instagram` (liên kết mạng xã hội)
  - `giai_thuong` (các giải thưởng)
  - `tac_pham_noi_bat` (tác phẩm nổi bật)

- Dữ liệu mẫu cho 10 tác giả nổi tiếng đã được thêm sẵn

## 🌐 Các trang mới

### 1. Danh sách tác giả
**URL:** `/public/authors.php`

**Tính năng:**
- Hiển thị tất cả tác giả trong hệ thống
- Tìm kiếm tác giả theo tên
- Hiển thị số lượng tác phẩm của mỗi tác giả
- Click vào tác giả để xem chi tiết

### 2. Chi tiết tác giả
**URL:** `/public/author_detail.php?id=TG001`

**Tính năng:**
- Hiển thị thông tin đầy đủ về tác giả:
  - Tiểu sử
  - Ngày sinh
  - Quốc tịch
  - Giải thưởng
  - Tác phẩm nổi bật
  - Liên kết mạng xã hội
- Danh sách tất cả sách của tác giả
- Tích hợp wishlist và giỏ hàng
- Hiển thị rating và giá sách

### 3. Link từ trang sách
Trong trang chi tiết sách (`book_detail.php`), tên tác giả giờ là link có thể click để đến trang chi tiết tác giả.

## 📝 Thêm/Sửa thông tin tác giả

### Cách 1: Qua phpMyAdmin
1. Mở phpMyAdmin
2. Chọn database `qlsach`
3. Chọn bảng `thong_tin_tac_gia`
4. Click "Insert" để thêm mới hoặc "Edit" để sửa

### Cách 2: Qua SQL
```sql
-- Thêm thông tin tác giả mới
INSERT INTO thong_tin_tac_gia (id_tac_gia, tieu_su, ngay_sinh, quoc_tich, giai_thuong, tac_pham_noi_bat)
VALUES ('TG999', 'Tiểu sử tác giả...', '1980-01-01', 'Việt Nam', 'Giải thưởng ABC', 'Tác phẩm XYZ');

-- Cập nhật thông tin tác giả
UPDATE thong_tin_tac_gia 
SET tieu_su = 'Tiểu sử mới...', 
    website = 'https://example.com'
WHERE id_tac_gia = 'TG001';
```

## 🎨 Tùy chỉnh giao diện

### File CSS
- `public/css/author.css` - Style cho trang chi tiết tác giả
- `public/css/authors.css` - Style cho trang danh sách tác giả

### Màu sắc
Hệ thống sử dụng biến CSS đồng nhất:
- `--primary`: #5DA2D5 (màu chủ đạo)
- `--primary-dark`: #4b8cc4 (màu hover)
- `--danger`: #F78888 (màu nhấn)
- `--light-bg`: #ECECEC (nền nhạt)
- `--border`: #DCDCDC (viền)

## 🔧 Mở rộng tính năng

### Thêm ảnh đại diện tác giả
1. Upload ảnh vào thư mục `public/uploads/authors/`
2. Đặt tên file theo format: `{id_tac_gia}.jpg` (ví dụ: `TG001.jpg`)
3. Cập nhật trường `anh_dai_dien` trong database:
```sql
UPDATE thong_tin_tac_gia 
SET anh_dai_dien = 'authors/TG001.jpg'
WHERE id_tac_gia = 'TG001';
```

### Thêm trang admin quản lý tác giả
Có thể tạo trang admin để:
- Thêm/sửa/xóa tác giả
- Upload ảnh đại diện
- Quản lý thông tin chi tiết
- Xem thống kê tác phẩm

## 📱 Responsive Design

Giao diện đã được tối ưu cho:
- Desktop (> 1024px)
- Tablet (768px - 1024px)
- Mobile (< 768px)

## 🐛 Xử lý lỗi thường gặp

### Lỗi: "Cannot declare class Database"
**Nguyên nhân:** Conflict giữa `config/db.php` và `models/Database.php`

**Giải pháp:** Model Author đã được cập nhật để sử dụng `models/Database.php`

### Lỗi: Không hiển thị thông tin tác giả
**Nguyên nhân:** Chưa chạy file SQL hoặc chưa có dữ liệu

**Giải pháp:** 
1. Chạy file `database/update_tac_gia_table.sql`
2. Kiểm tra bảng `thong_tin_tac_gia` có dữ liệu chưa

### Lỗi: Link tác giả không hoạt động
**Nguyên nhân:** Model Book chưa lấy `id_tac_gia`

**Giải pháp:** Đã được cập nhật trong `models/Book.php`

## 📞 Hỗ trợ

Nếu gặp vấn đề, kiểm tra:
1. Database đã có bảng `thong_tin_tac_gia` chưa
2. File `models/Author.php` đã tồn tại chưa
3. Các file CSS đã được load chưa
4. Console browser có lỗi JavaScript không

## 🎉 Tính năng đã hoàn thành

✅ Model Author với đầy đủ phương thức
✅ Trang danh sách tác giả với tìm kiếm
✅ Trang chi tiết tác giả với thông tin đầy đủ
✅ Bảng database thong_tin_tac_gia
✅ Dữ liệu mẫu cho 10 tác giả
✅ Link từ trang sách đến trang tác giả
✅ Responsive design
✅ Tích hợp wishlist và giỏ hàng
✅ CSS đồng bộ với hệ thống
