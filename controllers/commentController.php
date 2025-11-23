<?php
session_start();
require_once '../models/Comment.php';

$commentModel = new Comment();
$action = $_GET['action'] ?? '';

// Kiểm tra AJAX request
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

switch ($action) {
    case 'add':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_SESSION['id_tk'])) {
                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'Bạn cần đăng nhập để bình luận']);
                    exit;
                }
                header('Location: ../guest/login.php?error=not_logged_in');
                exit;
            }

            $id_sach = $_POST['id_sach'] ?? '';
            $binh_luan = trim($_POST['binh_luan'] ?? '');
            $so_sao = (int)($_POST['so_sao'] ?? 5);
            $id_tk = $_SESSION['id_tk'];

            // Validation
            if (empty($id_sach) || empty($binh_luan)) {
                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'Vui lòng nhập đầy đủ thông tin']);
                    exit;
                }
                header("Location: ../public/book_detail.php?id_sach=$id_sach&comment=error");
                exit;
            }

            if ($so_sao < 1 || $so_sao > 5) {
                $so_sao = 5;
            }

            // ✅ KIỂM TRA ĐIỀU KIỆN: User phải có đơn hàng trạng thái 4 (Đã hoàn thành)
            if (!$commentModel->hasUserPurchasedBook($id_sach, $id_tk)) {
                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => false, 
                        'message' => 'Bạn chỉ có thể đánh giá sách sau khi đơn hàng đã hoàn thành (trạng thái: Đã hoàn thành)'
                    ]);
                    exit;
                }
                header("Location: ../public/book_detail.php?id_sach=$id_sach&comment=not_purchased");
                exit;
            }

            // Kiểm tra user đã bình luận chưa (nếu có id_tk trong bảng)
            $existingComment = $commentModel->getUserComment($id_sach, $id_tk);
            if ($existingComment) {
                // Cập nhật bình luận cũ
                $id_bl = $existingComment->id_bl;
                $result = $commentModel->updateComment($id_bl, $id_tk, $binh_luan, $so_sao);
                $message = $result ? 'Cập nhật bình luận thành công!' : 'Cập nhật bình luận thất bại!';
            } else {
                // Thêm bình luận mới - Tạo ID unique
                $id_bl = 'BL' . substr(uniqid(), -5) . rand(10, 99);
                
                // Kiểm tra ID đã tồn tại chưa, nếu có thì tạo lại
                $attempts = 0;
                while ($commentModel->commentExists($id_bl) && $attempts < 5) {
                    $id_bl = 'BL' . substr(uniqid(), -5) . rand(10, 99);
                    $attempts++;
                }
                
                $result = $commentModel->addComment($id_bl, $id_sach, $id_tk, $binh_luan, $so_sao);
                $message = $result ? 'Thêm bình luận thành công!' : 'Thêm bình luận thất bại!';
            }

            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => $result,
                    'message' => $message,
                    'id_sach' => $id_sach
                ]);
                exit;
            }

            if ($result) {
                header("Location: ../public/book_detail.php?id_sach=$id_sach&comment=success");
            } else {
                header("Location: ../public/book_detail.php?id_sach=$id_sach&comment=fail");
            }
        }
        break;

    case 'update':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_SESSION['id_tk'])) {
                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'Bạn cần đăng nhập']);
                    exit;
                }
                header('Location: ../guest/login.php');
                exit;
            }

            $id_bl = $_POST['id_bl'] ?? '';
            $id_sach = $_POST['id_sach'] ?? '';
            $binh_luan = trim($_POST['binh_luan'] ?? '');
            $so_sao = (int)($_POST['so_sao'] ?? 5);
            $id_tk = $_SESSION['id_tk'];

            if (empty($id_bl) || empty($binh_luan)) {
                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'Vui lòng nhập đầy đủ thông tin']);
                    exit;
                }
                header("Location: ../public/book_detail.php?id_sach=$id_sach&comment=error");
                exit;
            }

            $result = $commentModel->updateComment($id_bl, $id_tk, $binh_luan, $so_sao);

            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => $result,
                    'message' => $result ? 'Cập nhật thành công!' : 'Cập nhật thất bại!',
                    'id_sach' => $id_sach
                ]);
                exit;
            }

            header("Location: ../public/book_detail.php?id_sach=$id_sach&comment=" . ($result ? 'updated' : 'error'));
        }
        break;

    case 'delete':
        if (!isset($_SESSION['id_tk'])) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Bạn cần đăng nhập']);
                exit;
            }
            header('Location: ../guest/login.php');
            exit;
        }

        $id_bl = $_GET['id_bl'] ?? '';
        $id_sach = $_GET['id_sach'] ?? '';
        $id_tk = $_SESSION['id_tk'];

        if (empty($id_bl)) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Không tìm thấy bình luận']);
                exit;
            }
            header("Location: ../public/book_detail.php?id_sach=$id_sach");
            exit;
        }

        $result = $commentModel->deleteComment($id_bl, $id_tk);

        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => $result,
                'message' => $result ? 'Xóa thành công!' : 'Xóa thất bại!',
                'id_sach' => $id_sach
            ]);
            exit;
        }

        header("Location: ../public/book_detail.php?id_sach=$id_sach&comment=" . ($result ? 'deleted' : 'error'));
        break;

    default:
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
        } else {
            header('Location: ../public/index.php');
        }
        break;
}
?>
