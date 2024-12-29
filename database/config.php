<?php
define('HOST', 'localhost');
define('USERNAME', 'root');
define('PASSWORD', '');
define('DATABASE', 'asm_php');
// Cấu hình VNPay
$vnp_TmnCode = "TLJF1MV9"; // Mã website của bạn tại VNPay
$vnp_HashSecret = "4JWUX5MRN8HWL8DTDOMCXIXS5E8EABTB"; // Chuỗi bí mật dùng để tạo chữ ký
$vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html"; // URL thanh toán VNPay Sandbox
$vnp_Returnurl = "https://localhost/vnpay_php/vnpay_return.php"; 