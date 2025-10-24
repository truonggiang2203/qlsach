<?php
// tao_tai_khoan_mau.php

// Gọi lớp Database để kết nối CSDL
require_once 'models/Database.php';

echo "<h1>Đang tạo tài khoản mẫu...</h1>";

try {
    $db = new Database(); // Kết nối CSDL

    // ---- Mật khẩu mẫu ----
    // Mật khẩu cho admin: admin123
    // Mật khẩu cho khách: user123
    
    // Băm mật khẩu
    $hash_pass_admin = password_hash('admin123', PASSWORD_DEFAULT);
    $hash_pass_user = password_hash('user123', PASSWORD_DEFAULT);

    // ---- Danh sách tài khoản ----
    $accounts = [
        // 3 TÀI KHOẢN ADMIN (id_nd = 'AD')
        [
            'id_tk' => 'AD001', 'id_nd' => 'AD', 'ho_ten' => 'Tram123', 
            'email' => 'admin@qlsach.com', 'sdt' => '0000000001', 'mat_khau' => $hash_pass_admin,
            'dia_chi' => '123 Server, TP. HCM', 'gioi_tinh' => 'Khác'
        ],
        [
            'id_tk' => 'AD002', 'id_nd' => 'AD', 'ho_ten' => 'Thuong123', 
            'email' => 'admin_kho@qlsach.com', 'sdt' => '0000000002', 'mat_khau' => $hash_pass_admin,
            'dia_chi' => '123 Server, TP. HCM', 'gioi_tinh' => 'Khác'
        ],
        [
            'id_tk' => 'AD003', 'id_nd' => 'AD', 'ho_ten' => 'Giang123', 
            'email' => 'admin_content@qlsach.com', 'sdt' => '0000000003', 'mat_khau' => $hash_pass_admin,
            'dia_chi' => '123 Server, TP. HCM', 'gioi_tinh' => 'Khác'
        ],

        // 2 TÀI KHOẢN KHÁCH HÀNG (id_nd = 'KH')
        [
            'id_tk' => 'KH001', 'id_nd' => 'KH', 'ho_ten' => 'Nguyễn Văn A', 
            'email' => 'khach1@gmail.com', 'sdt' => '0901000111', 'mat_khau' => $hash_pass_user,
            'dia_chi' => '456 Đường ABC, Q1, TP. HCM', 'gioi_tinh' => 'Nam'
        ],
        [
            'id_tk' => 'KH002', 'id_nd' => 'KH', 'ho_ten' => 'Trần Thị B', 
            'email' => 'khach2@gmail.com', 'sdt' => '0902000222', 'mat_khau' => $hash_pass_user,
            'dia_chi' => '789 Đường XYZ, Q3, TP. HCM', 'gioi_tinh' => 'Nữ'
        ]
    ];

    // Câu lệnh SQL (đảm bảo khớp với các cột trong CSDL của bạn)
    $sql = "INSERT INTO tai_khoan 
                (id_tk, id_nd, ho_ten, email, sdt, mat_khau, dia_chi_giao_hang, gioi_tinh, ngay_gio_tao_tk) 
            VALUES 
                (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    
    $stmt = $db->prepare($sql);

    // Chạy vòng lặp để thêm
    foreach ($accounts as $acc) {
        try {
            $stmt->execute([
                $acc['id_tk'], $acc['id_nd'], $acc['ho_ten'], $acc['email'], 
                $acc['sdt'], $acc['mat_khau'], $acc['dia_chi'], $acc['gioi_tinh']
            ]);
            echo "<p style='color: green;'>Tạo thành công: " . $acc['email'] . "</p>";
        } catch (PDOException $e) {
            // Bắt lỗi nếu ID hoặc Email bị trùng
            echo "<p style='color: red;'>Lỗi khi tạo " . $acc['email'] . ": " . $e->getMessage() . "</p>";
        }
    }

    echo "<h3>Hoàn tất!</h3>";

} catch (Exception $e) {
    echo "Kết nối CSDL thất bại: " . $e->getMessage();
}
?>