<?php
require_once('database/dbhelper.php');

$rawData = file_get_contents("php://input"); // Lấy dữ liệu thô từ Momo
$data = json_decode($rawData, true);

if (!empty($data)) {
    $partnerCode = $data['partnerCode'];
    $orderId = $data['orderId'];
    $amount = $data['amount'];
    $orderInfo = $data['orderInfo'];
    $resultCode = $data['resultCode'];
    $message = $data['message'];

    if ($resultCode == 0) {
        // Thanh toán thành công
        // Cập nhật trạng thái đơn hàng trong cơ sở dữ liệu
        $sql = "UPDATE orders SET status = 'PAID' WHERE id = $orderId";
        execute($sql);
    } else {
        // Thanh toán thất bại
        error_log("Thanh toán Momo thất bại: $message");
    }
}
