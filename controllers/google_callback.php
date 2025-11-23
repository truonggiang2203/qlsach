<?php
session_start();
require_once '../config/google_config.php';
require_once '../models/User.php';

// Kiểm tra có code từ Google không
if (!isset($_GET['code'])) {
    header('Location: ../guest/login.php?error=google_auth_failed');
    exit;
}

$code = $_GET['code'];

// Đổi code lấy access token
$token_url = 'https://oauth2.googleapis.com/token';
$token_data = [
    'code' => $code,
    'client_id' => GOOGLE_CLIENT_ID,
    'client_secret' => GOOGLE_CLIENT_SECRET,
    'redirect_uri' => GOOGLE_REDIRECT_URI,
    'grant_type' => 'authorization_code'
];

$ch = curl_init($token_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($token_data));
$response = curl_exec($ch);
curl_close($ch);

$token_info = json_decode($response, true);

if (!isset($token_info['access_token'])) {
    header('Location: ../guest/login.php?error=token_failed');
    exit;
}

// Lấy thông tin user từ Google
$user_info_url = 'https://www.googleapis.com/oauth2/v2/userinfo';
$ch = curl_init($user_info_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $token_info['access_token']
]);
$user_response = curl_exec($ch);
curl_close($ch);

$google_user = json_decode($user_response, true);

if (!isset($google_user['email'])) {
    header('Location: ../guest/login.php?error=user_info_failed');
    exit;
}

// Xử lý đăng nhập/đăng ký
$userModel = new User();

// Kiểm tra user đã tồn tại chưa
$existing_user = $userModel->getUserByEmail($google_user['email']);

if ($existing_user) {
    // User đã tồn tại - Đăng nhập
    $_SESSION['id_tk'] = $existing_user->id_tk;
    $_SESSION['ho_ten'] = $existing_user->ho_ten;
    $_SESSION['email'] = $existing_user->email;
    $_SESSION['id_nd'] = $existing_user->id_nd;
    $_SESSION['sdt'] = $existing_user->sdt ?? '';
    $_SESSION['dia_chi'] = $existing_user->dia_chi_giao_hang ?? '';
    
    header('Location: ../public/index.php?login=success');
    exit;
} else {
    // User mới - Tạo tài khoản
    $ho_ten = $google_user['name'] ?? 'User Google';
    $email = $google_user['email'];
    
    // Tạo ID tài khoản mới
    $id_tk = 'TK' . rand(100, 999);
    
    // Tạo mật khẩu ngẫu nhiên (user không cần biết vì đăng nhập bằng Google)
    $random_password = bin2hex(random_bytes(16));
    $hashed_password = password_hash($random_password, PASSWORD_DEFAULT);
    
    // Tạo tài khoản mới
    $result = $userModel->createGoogleUser(
        $id_tk,
        $ho_ten,
        $email,
        $hashed_password,
        $google_user['picture'] ?? null
    );
    
    if ($result) {
        // Đăng nhập luôn
        $_SESSION['id_tk'] = $id_tk;
        $_SESSION['ho_ten'] = $ho_ten;
        $_SESSION['email'] = $email;
        $_SESSION['id_nd'] = 'KH';
        $_SESSION['sdt'] = '';
        $_SESSION['dia_chi'] = '';
        
        header('Location: ../public/index.php?register=success');
        exit;
    } else {
        header('Location: ../guest/login.php?error=create_account_failed');
        exit;
    }
}
