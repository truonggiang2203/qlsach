<?php
header("Content-Type: application/json");
session_start();

// ====== 1. Nhận message từ client ======
$data = json_decode(file_get_contents("php://input"), true);
$msg = strtolower(trim($data["message"] ?? ""));
if ($msg === "") {
    echo json_encode(["reply" => "Bạn cần nhập nội dung câu hỏi nhé."]);
    exit;
}

// ====== 2. Hàm bỏ dấu tiếng Việt ======
function vn_no_sign($str) {
    $accents = [
        'a'=>'áàạảãâấầậẩẫăắằặẳẵ',
        'e'=>'éèẹẻẽêếềệểễ',
        'i'=>'íìịỉĩ',
        'o'=>'óòọỏõôốồộổỗơớờợởỡ',
        'u'=>'úùụủũưứừựửữ',
        'y'=>'ýỳỵỷỹ',
        'd'=>'đ'
    ];
    foreach ($accents as $non => $signs) {
        foreach (preg_split('//u', $signs, -1, PREG_SPLIT_NO_EMPTY) as $sign) {
            $str = str_replace($sign, $non, $str);
            $str = str_replace(strtoupper($sign), strtoupper($non), $str);
        }
    }
    return $str;
}

$msg_clean = vn_no_sign($msg);

// ====== 3. Kết nối DB ======
require __DIR__ . "/../config/db.php";
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    echo json_encode(["reply" => "Không kết nối được database!"]);
    exit;
}
$conn->set_charset("utf8mb4");

$reply = null;

// Helper: tạo HTML sách (tên + ảnh nếu có)
function render_book_item($id_sach, $ten_sach, $gia = null) {

    // Ảnh sách
    $imgPath = "uploads/" . $id_sach . ".jpg";
    $fullImg = __DIR__ . "/uploads/" . $id_sach . ".jpg";

    if (!file_exists($fullImg)) {
        $imgPath = "uploads/default-book.png"; // ảnh mặc định
    }

    // HTML sách
    $html  = "<div style='border:1px solid #ccc;padding:8px;border-radius:10px;margin:8px 0;background:white'>";
    $html .= "<strong>$ten_sach</strong> <span style='color:#777'>($id_sach)</span><br>";

    $html .= "<img src='$imgPath' style='max-width:120px;margin:6px 0;border-radius:8px'>";

    if ($gia !== null) {
        $html .= "<br><strong>Giá:</strong> " . number_format($gia) . "₫<br>";
    }

    // 2 nút chức năng
    $html .= "
        <div style='margin-top:8px;display:flex;gap:8px;'>
            <button class='chat-btn-detail' data-id='$id_sach'>Xem chi tiết</button>
            <button class='chat-btn-add' data-id='$id_sach'>Thêm vào giỏ</button>
        </div>
    ";

    $html .= "</div>";

    return $html;
}


// Để nhớ ngữ cảnh cuốn sách cuối cùng
$lastBookId = $_SESSION['last_book_id'] ?? null;

// ====== 4. Các xử lý thông minh dựa trên Database ======

// 4.1 Hỏi giá sách: "giá S0001", "gia sach s0001"
if (!$reply && preg_match('/gia.*(s[0-9]+)/i', $msg_clean, $m)) {
    $id = strtoupper($m[1]);
    $sql = "SELECT gia_sach_ban, s.ten_sach FROM gia_sach g
            JOIN sach s ON s.id_sach = g.id_sach
            WHERE g.id_sach='$id'
            ORDER BY tg_gia_bd DESC LIMIT 1";

    $res = $conn->query($sql);
    if ($res && $res->num_rows) {
        $row = $res->fetch_assoc();
        $reply = render_book_item($id, $row['ten_sach'], $row['gia_sach_ban']);
        $reply .= "Đây là giá mới nhất của sách $id.";
        $_SESSION['last_book_id'] = $id;
    } else {
        $reply = "Không tìm thấy mã sách $id.";
    }
}

// 4.2 Kiểm tra đơn hàng: "đơn DH176", "don dh176"
if (!$reply && preg_match('/(don|dh).*?(dh[0-9]+)/i', $msg_clean, $m)) {
    $id = strtoupper($m[2]);

    $sql = "SELECT trang_thai_dh FROM don_hang dh
            JOIN trang_thai_don_hang tt ON tt.id_trang_thai = dh.id_trang_thai
            WHERE id_don_hang='$id'";

    $res = $conn->query($sql);
    if ($res && $res->num_rows) {
        $reply = "Đơn hàng <strong>$id</strong> hiện đang: <strong>" . $res->fetch_assoc()['trang_thai_dh'] . "</strong>.";
    } else {
        $reply = "Mình không tìm thấy đơn hàng $id. Bạn kiểm tra lại mã giúp mình nhé.";
    }
}

// 4.3 Khuyến mãi
if (!$reply && (
    strpos($msg_clean, "khuyen") !== false ||
    strpos($msg_clean, "giam gia") !== false ||
    strpos($msg_clean, "khuyen mai") !== false
)) {
    $sql = "SELECT ten_km, phan_tram_km FROM khuyen_mai WHERE trang_thai_km='active'";
    $res = $conn->query($sql);

    if ($res && $res->num_rows > 0) {
        $reply = "Các khuyến mãi đang áp dụng:<br>";
        while ($row = $res->fetch_assoc()) {
            $reply .= "- {$row['ten_km']} (giảm {$row['phan_tram_km']}%)<br>";
        }
    } else {
        $reply = "Hiện tại chưa có chương trình khuyến mãi nào.";
    }
}

// 4.4 Kiểm tra tồn kho với mã: "S0001 còn hàng", "s0001 con hang"
if (!$reply && preg_match('/(s[0-9]+).*?(con hang|con khong|het|ton)/i', $msg_clean, $m)) {
    $id = strtoupper($m[1]);
    $sql = "SELECT ten_sach, so_luong_ton FROM sach WHERE id_sach='$id'";
    $res = $conn->query($sql);

    if ($res && $res->num_rows) {
        $row = $res->fetch_assoc();
        $sl = $row['so_luong_ton'];

        $reply = render_book_item($id, $row['ten_sach']);
        if ($sl > 0) {
            $reply .= "Sách $id hiện còn khoảng <strong>$sl</strong> cuốn trong kho.";
        } else {
            $reply .= "Sách $id hiện đã <strong>hết hàng</strong>.";
        }
        $_SESSION['last_book_id'] = $id;
    } else {
        $reply = "Không tìm thấy sách $id.";
    }
}

// 4.5 Hỏi kiểu "cuốn này còn không", "sách đó còn không" → dùng ngữ cảnh
if (!$reply && $lastBookId && (
    strpos($msg_clean, "cuon nay") !== false ||
    strpos($msg_clean, "sach nay") !== false ||
    (strpos($msg_clean, "con khong") !== false && strpos($msg_clean, "s") === false)
)) {
    $id = $lastBookId;
    $sql = "SELECT ten_sach, so_luong_ton FROM sach WHERE id_sach='$id'";
    $res = $conn->query($sql);

    if ($res && $res->num_rows) {
        $row = $res->fetch_assoc();
        $sl = $row['so_luong_ton'];
        $reply = render_book_item($id, $row['ten_sach']);
        if ($sl > 0) {
            $reply .= "Sách $id hiện còn khoảng <strong>$sl</strong> cuốn.";
        } else {
            $reply .= "Sách $id hiện đã <strong>hết hàng</strong>.";
        }
    } else {
        $reply = "Mình không nhớ rõ cuốn bạn nói tới, bạn nhập lại mã sách giúp mình nhé.";
    }
}

// 4.6 Gợi ý theo THỂ LOẠI (sách kinh tế, thiếu nhi, văn học, kỹ năng, khoa học...)
if (!$reply) {
    $categoryMap = [
        'kinh te'   => 'LS002',
        'thieu nhi' => 'LS004',
        'thieu-nhi' => 'LS004',
        'van hoc'   => 'LS001',
        'ky nang'   => 'LS003',
        'khoa hoc'  => 'LS005',
        'khoa-hoc'  => 'LS005'
    ];

    $foundLoai = null;
    foreach ($categoryMap as $kw => $id_loai) {
        if (strpos($msg_clean, $kw) !== false) {
            $foundLoai = $id_loai;
            break;
        }
    }

    if ($foundLoai) {
        $sql = "SELECT s.id_sach, s.ten_sach 
                FROM sach s
                JOIN sach_theloai st ON st.id_sach = s.id_sach
                JOIN the_loai tl ON tl.id_the_loai = st.id_the_loai
                WHERE tl.id_loai = '$foundLoai'
                LIMIT 5";
        $res = $conn->query($sql);
        if ($res && $res->num_rows) {
            $reply = "Một vài cuốn thuộc thể loại bạn quan tâm:<br>";
            while ($row = $res->fetch_assoc()) {
                $reply .= render_book_item($row['id_sach'], $row['ten_sach']);
                $_SESSION['last_book_id'] = $row['id_sach'];
            }
        }
    }
}

// 4.7 Gợi ý theo TÁC GIẢ
if (!$reply && (strpos($msg_clean, "tac gia") !== false || strpos($msg_clean, "sach cua") !== false)) {

    // Lấy toàn bộ tác giả, dò tên trong câu
    $tgRes = $conn->query("SELECT id_tac_gia, ten_tac_gia FROM tac_gia");
    $foundAuthor = null;

    if ($tgRes) {
        while ($tg = $tgRes->fetch_assoc()) {
            $nameClean = vn_no_sign(strtolower($tg['ten_tac_gia']));
            if (strpos($msg_clean, $nameClean) !== false) {
                $foundAuthor = $tg;
                break;
            }
        }
    }

    if ($foundAuthor) {
        $id_tg = $conn->real_escape_string($foundAuthor['id_tac_gia']);
        $sql = "SELECT s.id_sach, s.ten_sach 
                FROM sach s
                JOIN s_tg st ON st.id_sach = s.id_sach
                WHERE st.id_tac_gia = '$id_tg'
                LIMIT 5";
        $res = $conn->query($sql);
        if ($res && $res->num_rows) {
            $reply = "Một vài sách của tác giả <strong>{$foundAuthor['ten_tac_gia']}</strong>:<br>";
            while ($row = $res->fetch_assoc()) {
                $reply .= render_book_item($row['id_sach'], $row['ten_sach']);
                $_SESSION['last_book_id'] = $row['id_sach'];
            }
        } else {
            $reply = "Chưa tìm thấy sách nào của {$foundAuthor['ten_tac_gia']} trong hệ thống.";
        }
    }
}

// 4.8 Sách bán chạy (ban chay, best seller, nhieu nguoi mua)
if (!$reply && (
    strpos($msg_clean, "ban chay") !== false ||
    strpos($msg_clean, "bestseller") !== false ||
    strpos($msg_clean, "nhieu nguoi mua") !== false
)) {
    $sql = "SELECT c.id_sach, s.ten_sach, SUM(c.so_luong_ban) AS total
            FROM chi_tiet_don_hang c
            JOIN sach s ON s.id_sach = c.id_sach
            GROUP BY c.id_sach, s.ten_sach
            ORDER BY total DESC
            LIMIT 5";
    $res = $conn->query($sql);
    if ($res && $res->num_rows) {
        $reply = "Top sách bán chạy:<br>";
        while ($row = $res->fetch_assoc()) {
            $reply .= render_book_item($row['id_sach'], $row['ten_sach']);
            $reply .= "Đã bán: <strong>{$row['total']}</strong> cuốn<br><br>";
            $_SESSION['last_book_id'] = $row['id_sach'];
        }
    } else {
        $reply = "Hiện chưa có dữ liệu để thống kê sách bán chạy.";
    }
}

// 4.9 Tìm sách theo tên (fallback)
if (!$reply) {
    $q = $conn->real_escape_string($msg);
    $sql = "SELECT id_sach, ten_sach 
            FROM sach 
            WHERE ten_sach LIKE '%$q%' 
            LIMIT 5";

    $res = $conn->query($sql);
    if ($res && $res->num_rows > 0) {
        $reply = "Mình tìm thấy vài cuốn liên quan:<br>";
        while ($row = $res->fetch_assoc()) {
            $reply .= render_book_item($row['id_sach'], $row['ten_sach']);
            $_SESSION['last_book_id'] = $row['id_sach'];
        }
    }
}

// 4.10 Fallback cuối cùng
if (!$reply) {
    $reply = "Mình chưa hiểu câu hỏi này!<br>Bạn có thể hỏi:<br>
    - Giá sách (VD: <strong>Giá sách S0001</strong>)<br>
    - Đơn hàng (VD: <strong>Đơn DH176</strong>)<br>
    - Khuyến mãi<br>
    - Kiểm tra tồn kho (VD: <strong>S0018 còn hàng không</strong>)<br>
    - Tìm sách theo tên, thể loại, tác giả.";
}

// ====== 5. Trả kết quả ======
echo json_encode(["reply" => $reply]);
