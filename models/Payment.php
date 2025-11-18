<?php
require_once __DIR__ . '/Database.php';

class Payment {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Tạo URL thanh toán MoMo
     */
    public function createMoMoPayment($id_don_hang, $amount, $orderInfo = '') {
        // TODO: Tích hợp MoMo API
        // Đây là mock - bạn cần tích hợp MoMo API thực tế
        // Xem tài liệu: https://developers.momo.vn/
        
        $config = [
            'partnerCode' => 'MOMO_PARTNER_CODE', // Thay bằng partner code thực tế
            'accessKey' => 'MOMO_ACCESS_KEY',     // Thay bằng access key thực tế
            'secretKey' => 'MOMO_SECRET_KEY',     // Thay bằng secret key thực tế
            'returnUrl' => 'http://localhost/qlsach/controllers/paymentController.php?method=momo&action=return',
            'notifyUrl' => 'http://localhost/qlsach/controllers/paymentController.php?method=momo&action=notify',
            'endpoint' => 'https://test-payment.momo.vn/gw_payment/transactionProcessor'
        ];

        $orderId = $id_don_hang;
        $orderInfo = $orderInfo ?: 'Thanh toán đơn hàng ' . $id_don_hang;
        $amount = (int)$amount;
        $extraData = '';

        // Tạo signature
        $rawHash = "partnerCode=" . $config['partnerCode'] . 
                   "&accessKey=" . $config['accessKey'] . 
                   "&requestId=" . $orderId . 
                   "&amount=" . $amount . 
                   "&orderId=" . $orderId . 
                   "&orderInfo=" . $orderInfo . 
                   "&returnUrl=" . $config['returnUrl'] . 
                   "&notifyUrl=" . $config['notifyUrl'] . 
                   "&extraData=" . $extraData;
        
        $signature = hash_hmac("sha256", $rawHash, $config['secretKey']);

        // Tạo request
        $data = array(
            'partnerCode' => $config['partnerCode'],
            'accessKey' => $config['accessKey'],
            'requestId' => $orderId,
            'amount' => $amount,
            'orderId' => $orderId,
            'orderInfo' => $orderInfo,
            'returnUrl' => $config['returnUrl'],
            'notifyUrl' => $config['notifyUrl'],
            'extraData' => $extraData,
            'requestType' => 'captureMoMoWallet',
            'signature' => $signature
        );

        // Trong môi trường thực tế, gửi request đến MoMo API
        // $result = $this->sendPostRequest($config['endpoint'], json_encode($data));
        
        // Mock: Trả về URL test
        return [
            'success' => true,
            'payment_url' => $config['returnUrl'] . '&orderId=' . $orderId . '&amount=' . $amount,
            'message' => 'Đã tạo link thanh toán MoMo'
        ];
    }

    /**
     * Tạo URL thanh toán VNPay
     */
    public function createVNPayPayment($id_don_hang, $amount, $orderInfo = '') {
        // TODO: Tích hợp VNPay API
        // Đây là mock - bạn cần tích hợp VNPay API thực tế
        // Xem tài liệu: https://sandbox.vnpayment.vn/apis/
        
        $config = [
            'vnp_TmnCode' => 'VNPAY_TMN_CODE',  // Thay bằng mã website
            'vnp_HashSecret' => 'VNPAY_HASH_SECRET', // Thay bằng hash secret
            'vnp_Url' => 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html',
            'vnp_ReturnUrl' => 'http://localhost/qlsach/controllers/paymentController.php?method=vnpay&action=return'
        ];

        $vnp_TxnRef = $id_don_hang;
        $vnp_OrderInfo = $orderInfo ?: 'Thanh toan don hang ' . $id_don_hang;
        $vnp_OrderType = 'other';
        $vnp_Amount = $amount * 100; // VNPay yêu cầu số tiền nhân 100
        $vnp_Locale = 'vn';
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $vnp_CreateDate = date('YmdHis');

        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $config['vnp_TmnCode'],
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => $vnp_CreateDate,
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $config['vnp_ReturnUrl'],
            "vnp_TxnRef" => $vnp_TxnRef,
        );

        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $config['vnp_Url'] . "?" . $query;
        if (isset($config['vnp_HashSecret'])) {
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $config['vnp_HashSecret']);
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }

        return [
            'success' => true,
            'payment_url' => $vnp_Url,
            'message' => 'Đã tạo link thanh toán VNPay'
        ];
    }

    /**
     * Tạo URL thanh toán ZaloPay
     */
    public function createZaloPayPayment($id_don_hang, $amount, $orderInfo = '') {
        // TODO: Tích hợp ZaloPay API
        // Đây là mock - bạn cần tích hợp ZaloPay API thực tế
        // Xem tài liệu: https://developers.zalopay.vn/
        
        return [
            'success' => true,
            'payment_url' => 'http://localhost/qlsach/controllers/paymentController.php?method=zalopay&action=return&orderId=' . $id_don_hang . '&amount=' . $amount,
            'message' => 'Đã tạo link thanh toán ZaloPay'
        ];
    }

    /**
     * Cập nhật trạng thái thanh toán
     */
    public function updatePaymentStatus($id_don_hang, $status, $payment_method = '') {
        $sql = "UPDATE thanh_toan 
                SET trang_thai_tt = ?, ngay_gio_thanh_toan = NOW() 
                WHERE id_don_hang = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$status, $id_don_hang]);
        
        return $stmt->rowCount() > 0;
    }

    /**
     * Lấy thông tin thanh toán
     */
    public function getPaymentInfo($id_don_hang) {
        $sql = "SELECT tt.*, pttt.ten_pttt 
                FROM thanh_toan tt
                LEFT JOIN phuong_thuc_thanh_toan pttt ON tt.id_pttt = pttt.id_pttt
                WHERE tt.id_don_hang = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_don_hang]);
        return $stmt->fetch();
    }

    /**
     * Gửi POST request (helper method)
     */
    private function sendPostRequest($url, $data) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data)
        ]);
        $result = curl_exec($ch);
        curl_close($ch);
        return json_decode($result, true);
    }
}
?>

