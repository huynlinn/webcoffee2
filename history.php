<?php
require_once('database/dbhelper.php');
require_once('utils/utility.php');

// $order_id = $order_details_List['order_id'];
// $product_id = $order_details_List['product_id'];
// $num = $order_details_List['num'];
// $price = $order_details_List['price'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <!-- jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <!-- Popper JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <!-- Latest compiled JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
    <!-- <link rel="stylesheet" href="css/index.css"> -->
    <link rel="stylesheet" href="plugin/fontawesome/css/all.css">
    <link rel="stylesheet" href="css/cart.css">
    <title>Giỏ hàng</title>
</head>

<body>
    <div id="wrapper">
        <?php require('layout/header.php') ?>

        <!-- END HEADR -->
        <main>
            <section class="cart">
                <div class="container-top">
                    <div class="panel panel-primary">
                        <div class="panel-heading" style="padding: 1rem 0;">
                            <ul class="nav nav-tabs">
                                <li class="nav-item">
                                    <a class="nav-link" href="cart.php">Giỏ hàng</a>
                                </li>
                                <li class="nav-item ">
                                    <a class="nav-link active" href="dashboard.php">Lịch sử mua hàng</a>
                                </li>
                            </ul>
                            <h2 style="padding-top:2rem" class="">Lịch sử mua hàng</h2>
                        </div>
                        <div class="panel-body"></div>
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr style="font-weight: 500;text-align: center;">
                                    <td width="50px">STT</td>
                                    <td>Ảnh</td>
                                    <td>Tên sản phẩm</td>
                                    <td>Giá</td>
                                    <td>Số lượng</td>
                                    <td>Tổng cộng</td>
                                    <td>Thời gian đặt</td>
                                    <td>Trạng thái</td>
                                    <!-- <td width="50px"></td> -->
                                </tr>
                            </thead>
                            <tbody>
                                <?php

                                if (isset($_COOKIE['username'])) {
                                    $username = $_COOKIE['username'];

                                    $sql = "SELECT * FROM user WHERE username = '$username'";
                                    $user = executeResult($sql); // in ra 1 dòng 
                                    foreach ($user as $item) {
                                        $userId = $item['id_user'];
                                    }

                                    $sql = "SELECT o.id AS order_id, o.fullname, o.phone_number, o.email, o.address, o.note, o.order_date,
       od.product_id, od.num, od.price, od.status, p.title, p.thumbnail
FROM order_details od
JOIN product p ON p.id = od.product_id
JOIN orders o ON o.id = od.order_id
WHERE od.id_user = '$userId'
ORDER BY o.id DESC;


";


$order_details_List = executeResult($sql);
$orders = [];

foreach ($order_details_List as $item) {
    // Tạo mảng nhóm đơn hàng theo order_id
    if (!isset($orders[$item['order_id']])) {
        $orders[$item['order_id']] = [
            'order_id' => $item['order_id'],
            'status' => $item['status'],
            'order_date' => $item['order_date'], // Thêm order_date vào mảng
            'items' => []
        ];
        
    }
    
    // Thêm sản phẩm vào đơn hàng
    $orders[$item['order_id']]['items'][] = [
        'product_id' => $item['product_id'],
        'title' => $item['title'],
        'thumbnail' => $item['thumbnail'],
        'price' => $item['price'],
        'num' => $item['num']
    ];
        }$orderCount = 0; // Đếm số thứ tự đơn hàng
        foreach ($orders as $order) {
            echo '<tr><td colspan="7"><strong>Đơn hàng ' . (++$orderCount) . ' (Mã đơn: ' . $order['order_id'] . ')</strong></td></tr>';

            $itemCount = 0; // Đếm số thứ tự sản phẩm cho từng đơn hàng
            foreach ($order['items'] as $item) {
                echo '
                <tr style="text-align: center;">
                    <td width="50px">' . (++$itemCount) . '</td>
                    <td style="text-align:center">
                        <img width="50px" src="admin/product/' . $item['thumbnail'] . '">
                    </td>
                    <td>' . $item['title'] . '</td>
                    <td class="b-500 orange">' . number_format($item['price'], 0, ',', '.') . ' VNĐ</td>
                    <td width="100px">' . $item['num'] . '</td>
                    <td class="b-500 red">' . number_format($item['num'] * $item['price'], 0, ',', '.') . ' VNĐ</td>
                    <td>' . (isset($order['order_date']) ? date("d-m-Y H:i:s", strtotime($order['order_date'])) : 'Không xác định') . '</td>
                    <td style="color:green; font-weight:600;">' . $order['status'] . '</td>
                </tr>
                ';
            }
        }
                                        }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </main>
        <?php require_once('layout/footer.php'); ?>
    </div>
    </script>
</body>
<style>
    main{
        padding-bottom: 4rem;
    }
    .b-500 {
        font-weight: 500;
    }

    .bold {
        font-weight: bold;
    }

    .red {
        color: rgba(207, 16, 16, 0.815);
    }

    .orange {
        color: #a25437;
    }
</style>

</html>