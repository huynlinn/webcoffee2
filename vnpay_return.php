<?php
// Lấy dữ liệu phản hồi từ VNPay
$vnp_SecureHash = $_GET['vnp_SecureHash'];
unset($_GET['vnp_SecureHash']);
$inputData = $_GET;

// Tạo chuỗi hash để kiểm tra tính hợp lệ của phản hồi
$vnp_HashSecret = "4JWUX5MRN8HWL8DTDOMCXIXS5E8EABTB"; // Chuỗi bí mật
ksort($inputData);
$hashdata = "";
foreach ($inputData as $key => $value) {
    $hashdata .= urlencode($key) . "=" . urlencode($value) . '&';
}
$secureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret); // Tính toán mã hash

// Kiểm tra mã hash
if ($secureHash == $vnp_SecureHash) {
    $order_id = $_GET['vnp_TxnRef']; // Mã đơn hàng
    $transaction_status = $_GET['vnp_TransactionStatus']; // Trạng thái giao dịch

    if ($transaction_status == '00') {
        echo '<script>alert("Thanh toán thành công!"); window.location="history.php";</script>';
    } else {
        echo '<script>alert("Thanh toán thất bại!"); window.location="checkout.php";</script>';
    }
} else {
    echo '<script>alert("Sai mã xác thực!"); window.location="checkout.php";</script>';
}
?>
