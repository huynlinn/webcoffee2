<?php
require_once('../utils/utility.php');
$action = getPost('action');
$id = getPost('id');
$size = getPost('size');
$num = getPost('num');
$price = getPost('price');  // Lấy giá từ POST

$cart = [];
if (isset($_POST['action'])) {
    $action = $_POST['action'];
    $cart = isset($_COOKIE['cart']) ? json_decode($_COOKIE['cart'], true) : [];

    switch ($action) {
        case 'add':
            $id = $_POST['id'];
            $size = $_POST['size'];  // Size sản phẩm
            $num = $_POST['num'];  // Số lượng sản phẩm
            $price = $_POST['price'];  // Giá sản phẩm

            // Kiểm tra nếu sản phẩm với ID và size đã tồn tại trong giỏ hàng
            $isFind = false;
            foreach ($cart as &$item) {
                // Kiểm tra sản phẩm với size cụ thể
                if ($item['id'] == $id && $item['size'] == $size) {
                    $item['num'] += $num;  // Cộng dồn số lượng nếu sản phẩm đã có trong giỏ
                    $isFind = true;
                    break;
                }
            }

            // Nếu sản phẩm chưa có trong giỏ, thêm mới
            if (!$isFind) {
                $cart[] = [
                    'id' => $id,
                    'size' => $size,
                    'num' => $num,
                    'price' => $price
                ];
            }

            // Lưu lại giỏ hàng vào cookie
            setcookie('cart', json_encode($cart), time() + 30 * 24 * 60 * 60, '/');
            break;

            case 'delete':
                $id = $_POST['id'];
                $size = $_POST['size'];
                
                foreach ($cart as $key => $item) {
                    if ($item['id'] == $id && $item['size'] == $size) {
                        unset($cart[$key]);  // Xóa sản phẩm khỏi giỏ
                        break;
                    }
                }
                
                // Cập nhật lại cookie sau khi xóa
                setcookie('cart', json_encode(array_values($cart)), time() + 30 * 24 * 60 * 60, '/');
                break;
            

        case 'update':
            // Cập nhật số lượng sản phẩm
            $id = $_POST['id'];
            $size = $_POST['size'];
            $num = $_POST['num'];  // Số lượng mới

            foreach ($cart as &$item) {
                if ($item['id'] == $id && $item['size'] == $size) {
                    $item['num'] = $num;  // Cập nhật số lượng
                    break;
                }
            }

            // Cập nhật lại giỏ hàng vào cookie
            setcookie('cart', json_encode($cart), time() + 30 * 24 * 60 * 60, '/');
            break;

        // Các case khác nếu có
    }
}
if ($action == 'update') {
    $id = getPost('id');
    $size = getPost('size');
    $quantity = getPost('quantity');

    if ($quantity > 0) {
        foreach ($cart as &$item) {
            if ($item['id'] == $id && $item['size'] == $size) {
                $item['num'] = $quantity; // Cập nhật số lượng
                break;
            }
        }
        setcookie('cart', json_encode($cart), time() + 30 * 24 * 60 * 60, '/');
    }
    echo 'Cập nhật thành công';
    die();
}

?>
