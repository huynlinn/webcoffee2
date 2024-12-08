<?php
require_once('database/dbhelper.php');
require_once('utils/utility.php');

// Kiểm tra giỏ hàng
$cart = isset($_COOKIE['cart']) ? json_decode($_COOKIE['cart'], true) : [];

if (!isset($_COOKIE['username']) || empty($_COOKIE['username'])) {
    echo '<script>
            alert("Vui lòng đăng nhập để tiến hành mua hàng");
            window.location="login/login.php";
          </script>';
    exit();
}

// Lấy id_user từ username trong cookie
$username = $_COOKIE['username'];  // Lấy tên đăng nhập từ cookie

// Lấy id_user từ bảng user dựa trên username
$sqlUser = "SELECT id_user FROM user WHERE username = '$username'";
$resultUser = executeResult($sqlUser);

if (count($resultUser) == 0) {
    // Nếu không tìm thấy user, báo lỗi
    echo '<script>alert("Người dùng không hợp lệ!"); window.location="login/login.php";</script>';
    exit();
}

$id_user = $resultUser[0]['id_user'];  // Lấy id_user của người dùng

// Lấy danh sách sản phẩm trong giỏ hàng
$idList = [];
foreach ($cart as $item) {
    $idList[] = $item['id'];
}

$cartList = [];
if (count($idList) > 0) {
    $idList = implode(',', $idList);
    $sql = "SELECT p.id, p.title, p.thumbnail, ps.size, ps.price FROM product p
            JOIN product_size ps ON p.id = ps.product_id
            WHERE p.id IN ($idList)";
    $cartList = executeResult($sql);
} else {
    $cartList = [];
}

// Tính tổng số tiền đơn hàng
function calculateTotal($cart, $cartList) {
    $total = 0;
    foreach ($cartList as $item) {
        foreach ($cart as $value) {
            if ($value['id'] == $item['id'] && $value['size'] == $item['size']) {
                $total += $value['num'] * $item['price'];
            }
        }
    }
    return $total;
}

$total = calculateTotal($cart, $cartList);

// Kết nối cơ sở dữ liệu
$conn = getConnection();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Lọc dữ liệu người dùng và bảo vệ khỏi SQL Injection
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone_number = mysqli_real_escape_string($conn, $_POST['phone_number']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $note = mysqli_real_escape_string($conn, $_POST['note']);

    // Thêm đơn hàng vào cơ sở dữ liệu
    $orderSql = "INSERT INTO orders (fullname, email, phone_number, address, note, id_user) 
                 VALUES ('$fullname', '$email', '$phone_number', '$address', '$note', '$id_user')";
    $orderId = executeInsert($orderSql);

    // Lưu chi tiết đơn hàng vào bảng order_details
    foreach ($cartList as $item) {
        foreach ($cart as $value) {
            if ($value['id'] == $item['id'] && $value['size'] == $item['size']) {
                $quantity = $value['num'];
                $orderDetailSql = "INSERT INTO order_details (order_id, product_id, size, num, price, id_user) 
                VALUES ($orderId, {$item['id']}, '{$item['size']}', $quantity, {$item['price']}, $id_user)";  // Thêm id_user vào đây

                executeInsert($orderDetailSql);
            }
        }
    }

    // Xóa giỏ hàng sau khi hoàn tất đơn hàng
    setcookie('cart', '', time() - 3600, '/');
    echo '<script>
            alert("Đặt hàng thành công! Chúng tôi sẽ liên hệ với bạn trong thời gian sớm nhất.");
            window.location="history.php";  // Chuyển tới trang lịch sử đơn hàng
          </script>';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="plugin/fontawesome/css/all.css">
    <link rel="stylesheet" href="css/cart.css">
    <title>Thanh toán</title>
</head>

<body>
    <div id="wrapper">
        <?php require_once('layout/header.php'); ?>

        <main style="padding-bottom: 4rem;">
            <section class="cart">
                <div class="container">
                    <h3 style="text-align: center;">Tiến hành thanh toán</h3>
                    <div class="row">
                        <div class="panel panel-primary col-md-6">
                            <h4 style="padding: 2rem 0; border-bottom: 1px solid black;">Nhập thông tin mua hàng</h4>
                            <form action="checkout.php" method="POST">
                                <div class="form-group">
                                    <label for="usr">Họ và tên:</label>
                                    <input required="true" type="text" class="form-control" id="usr" name="fullname" placeholder="Nhập họ và tên">
                                </div>
                                <div class="form-group">
                                    <label for="email">Email:</label>
                                    <input required="true" type="email" class="form-control" id="email" name="email" placeholder="Nhập email">
                                </div>
                                <div class="form-group">
                                    <label for="phone_number">Số điện thoại:</label>
                                    <input required="true" type="text" class="form-control" id="phone_number" name="phone_number" placeholder="Nhập số điện thoại">
                                </div>
                                <div class="form-group">
                                    <label for="address">Địa chỉ:</label>
                                    <input required="true" type="text" class="form-control" id="address" name="address" placeholder="Nhập địa chỉ">
                                </div>
                                <div class="form-group">
                                    <label for="note">Ghi chú:</label>
                                    <textarea class="form-control" rows="3" name="note" id="note" placeholder="Ghi chú nếu có"></textarea>
                                </div>
                                <button type="submit" class="btn btn-success">Đặt hàng</button>
                            </form>
                        </div>

                        <div class="panel panel-primary col-md-6">
                            <h4 style="padding: 2rem 0; border-bottom: 1px solid black;">Đơn hàng của bạn</h4>
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr style="font-weight: 500;text-align: center;">
                                        <td width="50px">STT</td>
                                        <td>Tên Sản Phẩm</td>
                                        <td>Size</td>
                                        <td>Giá</td>
                                        <td>Số lượng</td>
                                        <td>Tổng tiền</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $count = 0;
                                    foreach ($cartList as $item) {
                                        foreach ($cart as $value) {
                                            if ($value['id'] == $item['id'] && $value['size'] == $item['size']) {
                                                $num = $value['num'];
                                                echo '
                                                <tr style="text-align: center;">
                                                    <td>' . (++$count) . '</td>
                                                    <td>' . $item['title'] . '</td>
                                                    <td>' . $item['size'] . '</td>
                                                    <td>' . number_format($item['price'], 0, ',', '.') . ' VNĐ</td>
                                                    <td>' . $num . '</td>
                                                    <td>' . number_format($num * $item['price'], 0, ',', '.') . ' VNĐ</td>
                                                </tr>';
                                            }
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                            <h3>Tổng cộng: <?= number_format($total, 0, ',', '.') ?> VNĐ</h3>
                        </div>
                    </div>
                </div>
            </section>
        </main>
        <?php require_once('layout/footer.php'); ?>
    </div>
</body>

</html>


<style>
    .xemlai {
        font-size: 18px;
        font-weight: 500;
        color: blue;
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
</style>

</html>