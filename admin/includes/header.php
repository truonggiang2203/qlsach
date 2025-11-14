<?php
session_start();
if (!isset($_SESSION['phan_quyen']) || $_SESSION['phan_quyen'] != 'admin') {
    header('Location: ../public/index.php'); // Điều chỉnh đường dẫn nếu cần
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang Quản Trị</title>
    
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    
    <style>
        /* Bạn có thể thêm CSS tùy chỉnh tại đây */
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
        </ul>

        <ul class="navbar-nav ml-auto">
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#">
                    <i class="far fa-user"></i>
                    <span class="d-none d-md-inline ml-1"><?php echo htmlspecialchars($_SESSION['ho_ten']); ?></span>
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                    <a href="#" class="dropdown-item">
                        <i class="fas fa-user mr-2"></i> Thông tin cá nhân
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="../controllers/AuthController.php?action=logout" class="dropdown-item">
                        <i class="fas fa-sign-out-alt mr-2"></i> Đăng xuất
                    </a>
                </div>
            </li>
        </ul>
    </nav>