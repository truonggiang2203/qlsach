# Hướng dẫn tích hợp Thanh toán Online

## 📋 Tổng quan

Hệ thống đã được tích hợp sẵn giao diện và logic xử lý thanh toán online cho các phương thức:
- **MoMo** (Ví điện tử)
- **VNPay** (Thẻ ngân hàng nội địa)
- **ZaloPay** (Ví điện tử)

Hiện tại code đang ở dạng **mock/demo**. Để tích hợp thực tế, bạn cần đăng ký tài khoản merchant và cấu hình API.

---

## 🔧 Cấu hình

### 1. MoMo Payment

#### Bước 1: Đăng ký tài khoản MoMo
1. Truy cập: https://developers.momo.vn/
2. Đăng ký tài khoản merchant
3. Lấy thông tin: `Partner Code`, `Access Key`, `Secret Key`

#### Bước 2: Cập nhật model Payment.php
Mở file `models/Payment.php`, tìm hàm `createMoMoPayment()` và cập nhật:

```php
$config = [
    'partnerCode' => 'YOUR_MOMO_PARTNER_CODE',  // Thay bằng partner code thực tế
    'accessKey' => 'YOUR_MOMO_ACCESS_KEY',      // Thay bằng access key thực tế
    'secretKey' => 'YOUR_MOMO_SECRET_KEY',      // Thay bằng secret key thực tế
    'returnUrl' => 'http://your-domain.com/qlsach/controllers/paymentController.php?method=momo&action=return',
    'notifyUrl' => 'http://your-domain.com/qlsach/controllers/paymentController.php?method=momo&action=notify',
    'endpoint' => 'https://test-payment.momo.vn/gw_payment/transactionProcessor' // Test
    // 'endpoint' => 'https://payment.momo.vn/gw_payment/transactionProcessor' // Production
];
```

#### Bước 3: Xử lý callback
File `controllers/paymentController.php` đã có sẵn hàm `handleMoMoCallback()`.

**Lưu ý**: Cần xác thực signature từ MoMo để đảm bảo tính bảo mật.

**Tài liệu**: https://developers.momo.vn/v3/docs/payment/api

---

### 2. VNPay Payment

#### Bước 1: Đăng ký tài khoản VNPay
1. Truy cập: https://sandbox.vnpayment.vn/
2. Đăng ký tài khoản merchant
3. Lấy thông tin: `TmnCode`, `HashSecret`

#### Bước 2: Cập nhật model Payment.php
Mở file `models/Payment.php`, tìm hàm `createVNPayPayment()` và cập nhật:

```php
$config = [
    'vnp_TmnCode' => 'YOUR_VNPAY_TMN_CODE',      // Thay bằng mã website
    'vnp_HashSecret' => 'YOUR_VNPAY_HASH_SECRET', // Thay bằng hash secret
    'vnp_Url' => 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html', // Test
    // 'vnp_Url' => 'https://www.vnpayment.vn/paymentv2/vpcpay.html', // Production
    'vnp_ReturnUrl' => 'http://your-domain.com/qlsach/controllers/paymentController.php?method=vnpay&action=return'
];
```

#### Bước 3: Xử lý callback
File `controllers/paymentController.php` đã có sẵn hàm `handleVNPayCallback()`.

**Tài liệu**: https://sandbox.vnpayment.vn/apis/

---

### 3. ZaloPay Payment

#### Bước 1: Đăng ký tài khoản ZaloPay
1. Truy cập: https://developers.zalopay.vn/
2. Đăng ký tài khoản merchant
3. Lấy thông tin API keys

#### Bước 2: Cập nhật model Payment.php
Mở file `models/Payment.php`, tìm hàm `createZaloPayPayment()` và tích hợp ZaloPay API.

**Tài liệu**: https://developers.zalopay.vn/docs

---

## 🔐 Bảo mật

### 1. Lưu trữ thông tin nhạy cảm
**KHÔNG** lưu API keys trực tiếp trong code. Sử dụng:

- **File cấu hình riêng** (không commit vào Git):
```php
// config/payment_config.php (thêm vào .gitignore)
return [
    'momo' => [
        'partner_code' => '...',
        'access_key' => '...',
        'secret_key' => '...',
    ],
    'vnpay' => [
        'tmn_code' => '...',
        'hash_secret' => '...',
    ],
];
```

- **Biến môi trường** (.env file):
```
MOMO_PARTNER_CODE=...
MOMO_ACCESS_KEY=...
MOMO_SECRET_KEY=...
VNPAY_TMN_CODE=...
VNPAY_HASH_SECRET=...
```

### 2. Xác thực callback
Luôn xác thực signature từ payment gateway trước khi cập nhật trạng thái thanh toán.

### 3. HTTPS
Đảm bảo website chạy trên HTTPS khi deploy production.

---

## 📊 Database

### Bảng `thanh_toan`
Hệ thống đã sử dụng bảng `thanh_toan` với các trường:
- `id_pttt`: ID phương thức thanh toán
- `id_don_hang`: ID đơn hàng
- `trang_thai_tt`: Trạng thái thanh toán (0: chưa thanh toán, 1: đã thanh toán)
- `ngay_gio_thanh_toan`: Thời gian thanh toán

### Thêm phương thức thanh toán mới
Nếu cần thêm phương thức thanh toán (ví dụ: PT004), thêm vào bảng `phuong_thuc_thanh_toan`:

```sql
INSERT INTO phuong_thuc_thanh_toan (id_pttt, ten_pttt) 
VALUES ('PT004', 'ZaloPay');
```

Sau đó cập nhật logic trong `controllers/orderController.php` và `models/Payment.php`.

---

## 🧪 Testing

### Test môi trường Sandbox
1. Sử dụng tài khoản sandbox từ các payment gateway
2. Test với số tiền nhỏ
3. Kiểm tra callback và xử lý lỗi

### Test flow thanh toán
1. Tạo đơn hàng với thanh toán online
2. Kiểm tra link thanh toán được tạo
3. Test thanh toán thành công
4. Test thanh toán thất bại
5. Kiểm tra callback xử lý đúng

---

## 📝 Checklist tích hợp

- [ ] Đăng ký tài khoản merchant MoMo
- [ ] Đăng ký tài khoản merchant VNPay
- [ ] Đăng ký tài khoản merchant ZaloPay (nếu cần)
- [ ] Cập nhật API keys trong `models/Payment.php`
- [ ] Test callback từ payment gateway
- [ ] Xác thực signature callback
- [ ] Cấu hình HTTPS cho production
- [ ] Test thanh toán thực tế với số tiền nhỏ
- [ ] Kiểm tra xử lý lỗi và edge cases

---

## 🔗 Tài liệu tham khảo

- **MoMo**: https://developers.momo.vn/v3/docs/payment/api
- **VNPay**: https://sandbox.vnpayment.vn/apis/
- **ZaloPay**: https://developers.zalopay.vn/docs

---

## 💡 Lưu ý

1. **Môi trường Test vs Production**:
   - Luôn test kỹ trên môi trường sandbox trước
   - Thay đổi endpoint từ test sang production khi deploy

2. **Xử lý lỗi**:
   - Luôn có xử lý lỗi khi gọi API
   - Log lỗi để debug sau này

3. **Callback**:
   - Payment gateway sẽ gọi callback khi thanh toán xong
   - Đảm bảo callback URL có thể truy cập từ internet (không phải localhost)

4. **Timeout**:
   - Có thể người dùng đóng trình duyệt trước khi thanh toán xong
   - Hệ thống vẫn nhận được callback và cập nhật trạng thái

---

## 🆘 Hỗ trợ

Nếu gặp vấn đề trong quá trình tích hợp:
1. Kiểm tra log lỗi
2. Xem tài liệu API của từng payment gateway
3. Test với số tiền nhỏ trước
4. Liên hệ support của payment gateway

