<?php
include 'includes/header.php';
include 'includes/db.php';

// 1. Lấy ID người dùng hiện tại từ Session
// (Giả sử khi login bạn đã lưu $_SESSION['id_tk'])
if (!isset($_SESSION['id_tk'])) {
    header('Location: ../login.php'); // Hoặc trang login của bạn
    exit;
}

$current_user_id = $_SESSION['id_tk'];
$success = '';
$error = '';

// --- XỬ LÝ FORM: CẬP NHẬT THÔNG TIN ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_info') {
    $ho_ten = $_POST['ho_ten'];
    $email = $_POST['email'];
    $sdt = $_POST['sdt'];
    $gioi_tinh = $_POST['gioi_tinh'];
    $dia_chi = $_POST['dia_chi'];

    try {
        $stmt = $pdo->prepare("
            UPDATE tai_khoan 
            SET ho_ten = ?, email = ?, sdt = ?, gioi_tinh = ?, dia_chi_giao_hang = ? 
            WHERE id_tk = ?
        ");
        $stmt->execute([$ho_ten, $email, $sdt, $gioi_tinh, $dia_chi, $current_user_id]);
        
        // Cập nhật lại Session tên hiển thị
        $_SESSION['ho_ten'] = $ho_ten;
        
        $success = "Cập nhật thông tin thành công!";
    } catch (Exception $e) {
        $error = "Lỗi: " . $e->getMessage();
    }
}

// --- XỬ LÝ FORM: ĐỔI MẬT KHẨU ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'change_pass') {
    $current_pass = $_POST['current_pass'];
    $new_pass = $_POST['new_pass'];
    $confirm_pass = $_POST['confirm_pass'];

    if ($new_pass !== $confirm_pass) {
        $error = "Mật khẩu mới và xác nhận mật khẩu không khớp.";
    } else {
        // Lấy mật khẩu hiện tại trong DB để kiểm tra
        $stmt = $pdo->prepare("SELECT mat_khau FROM tai_khoan WHERE id_tk = ?");
        $stmt->execute([$current_user_id]);
        $user_data = $stmt->fetch();

        if ($user_data && password_verify($current_pass, $user_data['mat_khau'])) {
            // Mật khẩu cũ đúng -> Tiến hành đổi
            $new_hashed_pass = password_hash($new_pass, PASSWORD_DEFAULT);
            
            $stmt_update = $pdo->prepare("UPDATE tai_khoan SET mat_khau = ? WHERE id_tk = ?");
            $stmt_update->execute([$new_hashed_pass, $current_user_id]);
            
            $success = "Đổi mật khẩu thành công!";
        } else {
            $error = "Mật khẩu hiện tại không chính xác.";
        }
    }
}

// --- LẤY DỮ LIỆU NGƯỜI DÙNG MỚI NHẤT ---
$stmt = $pdo->prepare("SELECT * FROM tai_khoan WHERE id_tk = ?");
$stmt->execute([$current_user_id]);
$user = $stmt->fetch();

if (!$user) {
    echo "Không tìm thấy tài khoản.";
    exit;
}
?>

<?php include 'includes/sidebar.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Thông Tin Cá Nhân</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Profile</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            
            <?php if ($success): ?>
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-3">
                    <div class="card card-primary card-outline">
                        <div class="card-body box-profile">
                            <div class="text-center">
                                <img class="profile-user-img img-fluid img-circle"
                                     src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png"
                                     alt="User profile picture">
                            </div>

                            <h3 class="profile-username text-center"><?php echo htmlspecialchars($user['ho_ten']); ?></h3>
                            <p class="text-muted text-center">Quản Trị Viên (<?php echo htmlspecialchars($user['id_tk']); ?>)</p>

                            <ul class="list-group list-group-unbordered mb-3">
                                <li class="list-group-item">
                                    <b>Email</b> <a class="float-right"><?php echo htmlspecialchars($user['email']); ?></a>
                                </li>
                                <li class="list-group-item">
                                    <b>SĐT</b> <a class="float-right"><?php echo htmlspecialchars($user['sdt']); ?></a>
                                </li>
                                <li class="list-group-item">
                                    <b>Ngày tham gia</b> <a class="float-right"><?php echo date('d/m/Y', strtotime($user['ngay_gio_tao_tk'])); ?></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-md-9">
                    <div class="card">
                        <div class="card-header p-2">
                            <ul class="nav nav-pills">
                                <li class="nav-item"><a class="nav-link active" href="#settings" data-toggle="tab">Cập Nhật Thông Tin</a></li>
                                <li class="nav-item"><a class="nav-link" href="#password" data-toggle="tab">Đổi Mật Khẩu</a></li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                
                                <div class="active tab-pane" id="settings">
                                    <form class="form-horizontal" method="POST" action="">
                                        <input type="hidden" name="action" value="update_info">
                                        
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label">Họ Tên</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" name="ho_ten" value="<?php echo htmlspecialchars($user['ho_ten']); ?>" required>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label">Email</label>
                                            <div class="col-sm-10">
                                                <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label">Số ĐT</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" name="sdt" value="<?php echo htmlspecialchars($user['sdt']); ?>" required>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label">Giới Tính</label>
                                            <div class="col-sm-10">
                                                <select class="form-control" name="gioi_tinh">
                                                    <option value="Nam" <?php echo ($user['gioi_tinh'] == 'Nam') ? 'selected' : ''; ?>>Nam</option>
                                                    <option value="Nữ" <?php echo ($user['gioi_tinh'] == 'Nữ') ? 'selected' : ''; ?>>Nữ</option>
                                                    <option value="Khác" <?php echo ($user['gioi_tinh'] == 'Khác') ? 'selected' : ''; ?>>Khác</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label">Địa Chỉ</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" name="dia_chi" value="<?php echo htmlspecialchars($user['dia_chi_giao_hang']); ?>">
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row">
                                            <div class="offset-sm-2 col-sm-10">
                                                <button type="submit" class="btn btn-primary">Lưu Thay Đổi</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                                <div class="tab-pane" id="password">
                                    <form class="form-horizontal" method="POST" action="">
                                        <input type="hidden" name="action" value="change_pass">
                                        
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Mật khẩu hiện tại</label>
                                            <div class="col-sm-9">
                                                <input type="password" class="form-control" name="current_pass" required placeholder="Nhập mật khẩu cũ">
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Mật khẩu mới</label>
                                            <div class="col-sm-9">
                                                <input type="password" class="form-control" name="new_pass" required placeholder="Nhập mật khẩu mới">
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Xác nhận mật khẩu</label>
                                            <div class="col-sm-9">
                                                <input type="password" class="form-control" name="confirm_pass" required placeholder="Nhập lại mật khẩu mới">
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row">
                                            <div class="offset-sm-3 col-sm-9">
                                                <button type="submit" class="btn btn-danger">Đổi Mật Khẩu</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                </div>
                            </div></div>
                    </div>
            </div>
        </div>
    </section>
</div>

<?php include 'includes/footer.php'; ?>