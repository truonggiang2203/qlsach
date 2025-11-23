# 📰 HƯỚNG DẪN SỬ DỤNG HỆ THỐNG BLOG / TIN TỨC

## 🎯 Tổng quan

Hệ thống Blog/Tin tức giúp website:
- Tăng SEO với nội dung chất lượng
- Tăng engagement với khách hàng
- Xây dựng thương hiệu chuyên nghiệp
- Review sách, chia sẻ kiến thức văn học

## 📋 Cài đặt Database

### Bước 1: Chạy file SQL
1. Mở phpMyAdmin
2. Chọn database `qlsach`
3. Vào tab "SQL"
4. Copy nội dung file `database/create_blog_tables.sql`
5. Paste và click "Go"

### Bước 2: Kiểm tra
Sau khi chạy SQL, bạn sẽ có:
- Bảng `danh_muc_bai_viet` (5 danh mục mẫu)
- Bảng `bai_viet` (5 bài viết mẫu)
- Bảng `tag` (10 tag mẫu)
- Bảng `bai_viet_tag` (liên kết)
- Bảng `binh_luan_bai_viet` (cho tương lai)

## 🌐 Các trang đã tạo

### 1. Trang danh sách blog
**URL:** `/public/blog.php`

**Tính năng:**
- Hiển thị bài viết nổi bật (featured)
- Grid hiển thị tất cả bài viết
- Pagination (phân trang)
- Sidebar với danh mục và bài viết phổ biến
- Responsive design

### 2. Trang chi tiết bài viết
**URL:** `/public/blog_detail.php?slug=ten-bai-viet`

**Tính năng:**
- Hiển thị nội dung đầy đủ
- Breadcrumb navigation
- Meta information (tác giả, ngày, lượt xem)
- Tags
- Nút chia sẻ mạng xã hội
- Bài viết liên quan
- SEO-friendly với meta tags

## 📝 Quản lý nội dung

### Thêm bài viết mới

```sql
INSERT INTO bai_viet (
    id_danh_muc, 
    id_tk, 
    tieu_de, 
    slug, 
    tom_tat, 
    noi_dung, 
    trang_thai, 
    noi_bat,
    meta_title,
    meta_description,
    meta_keywords,
    ngay_xuat_ban
) VALUES (
    'DM001',  -- ID danh mục
    'AD001',  -- ID tài khoản admin
    'Tiêu đề bài viết',
    'tieu-de-bai-viet',  -- URL-friendly slug
    'Tóm tắt ngắn gọn...',
    '<h2>Nội dung</h2><p>Nội dung chi tiết...</p>',
    'published',  -- draft, published, archived
    1,  -- 1: nổi bật, 0: bình thường
    'SEO Title',
    'SEO Description',
    'keyword1, keyword2',
    NOW()
);
```

### Tạo slug từ tiêu đề

Slug phải:
- Viết thường
- Không dấu
- Thay khoảng trắng bằng dấu gạch ngang
- Ví dụ: "Review Sách Hay" → "review-sach-hay"

### Thêm ảnh đại diện

1. Upload ảnh vào `public/uploads/blog/`
2. Đặt tên file: `ten-bai-viet.jpg`
3. Cập nhật trường `anh_dai_dien`:

```sql
UPDATE bai_viet 
SET anh_dai_dien = 'ten-bai-viet.jpg'
WHERE id_bai_viet = 1;
```

### Thêm tags cho bài viết

```sql
-- Thêm tag mới
INSERT INTO tag (ten_tag, slug) VALUES ('Văn học Pháp', 'van-hoc-phap');

-- Liên kết bài viết với tag
INSERT INTO bai_viet_tag (id_bai_viet, id_tag) VALUES (1, 1);
```

## 🎨 Tùy chỉnh giao diện

### File CSS
- `public/css/blog.css` - Style cho trang blog

### Màu sắc
Sử dụng biến CSS đồng nhất với hệ thống:
```css
--primary: #5DA2D5
--primary-dark: #4b8cc4
--danger: #F78888
--light-bg: #ECECEC
--border: #DCDCDC
```

## 🔍 Tối ưu SEO

### 1. Meta Tags
Mỗi bài viết có:
- `meta_title`: Tiêu đề SEO (50-60 ký tự)
- `meta_description`: Mô tả SEO (150-160 ký tự)
- `meta_keywords`: Từ khóa (5-10 từ khóa)

### 2. URL-Friendly Slug
- Sử dụng slug thay vì ID
- Dễ đọc, dễ nhớ
- Tốt cho SEO

### 3. Structured Data
Có thể thêm JSON-LD schema cho bài viết:

```html
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "BlogPosting",
  "headline": "<?= $post->tieu_de ?>",
  "datePublished": "<?= $post->ngay_xuat_ban ?>",
  "author": {
    "@type": "Person",
    "name": "<?= $post->tac_gia ?>"
  }
}
</script>
```

### 4. Sitemap
Tạo sitemap cho blog:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <url>
    <loc>https://yourdomain.com/public/blog_detail.php?slug=bai-viet</loc>
    <lastmod>2025-11-23</lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.8</priority>
  </url>
</urlset>
```

## 📊 Thống kê

### Lượt xem
Hệ thống tự động đếm lượt xem mỗi khi người dùng truy cập bài viết.

### Bài viết phổ biến
Hiển thị top bài viết có lượt xem cao nhất.

## 🚀 Mở rộng tính năng

### 1. Hệ thống bình luận
Bảng `binh_luan_bai_viet` đã được tạo sẵn, có thể phát triển:
- Cho phép người dùng bình luận
- Duyệt bình luận (pending/approved)
- Reply bình luận

### 2. Tìm kiếm blog
Thêm thanh tìm kiếm:

```php
$keyword = $_GET['search'] ?? '';
$posts = $blogModel->searchPosts($keyword);
```

### 3. RSS Feed
Tạo RSS feed cho blog:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0">
  <channel>
    <title>Blog - Nhà Sách Online</title>
    <link>https://yourdomain.com/public/blog.php</link>
    <description>Tin tức và review sách mới nhất</description>
    <!-- Items -->
  </channel>
</rss>
```

### 4. Newsletter
Tích hợp gửi email thông báo bài viết mới.

## 📱 Responsive Design

Giao diện đã được tối ưu cho:
- Desktop (> 1024px)
- Tablet (768px - 1024px)
- Mobile (< 768px)

## 🔧 Troubleshooting

### Lỗi: Không hiển thị bài viết
**Kiểm tra:**
1. Trạng thái bài viết phải là 'published'
2. Ngày xuất bản phải <= ngày hiện tại
3. Database đã chạy SQL chưa

### Lỗi: Ảnh không hiển thị
**Kiểm tra:**
1. Thư mục `public/uploads/blog/` đã tồn tại chưa
2. Tên file ảnh trong database đúng chưa
3. Quyền truy cập thư mục

### Lỗi: Slug bị trùng
**Giải pháp:**
- Slug phải unique
- Thêm số vào cuối: `bai-viet-1`, `bai-viet-2`

## 📈 Best Practices

### 1. Viết nội dung chất lượng
- Độ dài: 800-1500 từ
- Có hình ảnh minh họa
- Chia đoạn rõ ràng
- Sử dụng heading (h2, h3)

### 2. Tối ưu ảnh
- Kích thước: < 200KB
- Định dạng: JPG, PNG, WebP
- Kích thước khuyến nghị: 1200x630px

### 3. Đăng bài đều đặn
- Ít nhất 2-3 bài/tuần
- Lên lịch trước

### 4. Tương tác
- Trả lời bình luận
- Chia sẻ lên mạng xã hội
- Liên kết nội bộ

## 🎉 Tính năng đã hoàn thành

✅ Database với 5 bảng
✅ Model Blog với đầy đủ phương thức
✅ Trang danh sách blog
✅ Trang chi tiết bài viết
✅ Hệ thống danh mục
✅ Hệ thống tag
✅ Bài viết nổi bật
✅ Bài viết phổ biến
✅ Pagination
✅ SEO-friendly URLs
✅ Meta tags
✅ Responsive design
✅ Share buttons
✅ Related posts
✅ Breadcrumb navigation

## 📞 Tiếp theo

Có thể phát triển thêm:
- Trang admin quản lý blog
- Editor WYSIWYG
- Upload ảnh trực tiếp
- Hệ thống bình luận
- Tìm kiếm nâng cao
- RSS feed
- Newsletter
