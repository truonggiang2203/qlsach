<?php
// =========================================================
// CẤU HÌNH GOOGLE OAUTH 2.0
// =========================================================

// Thông tin OAuth từ Google Cloud Console

define('GOOGLE_CLIENT_ID', getenv('GOOGLE_CLIENT_ID'));
define('GOOGLE_CLIENT_SECRET', getenv('GOOGLE_CLIENT_SECRET'));

// URL callback sau khi đăng nhập Google
define('GOOGLE_REDIRECT_URI', 'http://localhost/qlsach/controllers/google_callback.php');

// Các quyền cần thiết
define('GOOGLE_SCOPES', 'email profile');

// =========================================================
// HƯỚNG DẪN LẤY GOOGLE CLIENT ID & SECRET:
// =========================================================
// 1. Truy cập: https://console.cloud.google.com/
// 2. Tạo project mới hoặc chọn project có sẵn
// 3. Vào "APIs & Services" > "Credentials"
// 4. Click "Create Credentials" > "OAuth client ID"
// 5. Chọn "Web application"
// 6. Thêm Authorized redirect URIs:
//    - http://localhost/qlsach/controllers/google_callback.php
//    - http://localhost:3000/qlsach/controllers/google_callback.php (nếu dùng port khác)
// 7. Copy Client ID và Client Secret vào file này
// =========================================================
