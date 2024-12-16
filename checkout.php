<?php
require_once('database/dbhelper.php');
require_once('utils/utility.php');

//buynow

// Kiểm tra giỏ hàng
$cart = isset($_COOKIE['cart']) ? json_decode($_COOKIE['cart'], true) : [];
if (!$cart) {
    $cart = [];
}

$username = isset($_COOKIE['username']) ? filter_var($_COOKIE['username'], FILTER_SANITIZE_STRING) : '';
if (empty($username)) {
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
            if (isset($value['id'], $value['size'], $value['num']) &&
                $value['id'] == $item['id'] && 
                $value['size'] == $item['size']) {
                $total += intval($value['num']) * floatval($item['price']);
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
    $fullname = trim(mysqli_real_escape_string($conn, $_POST['fullname']));
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $phone_number = preg_match('/^[0-9]{10,11}$/', $_POST['phone_number']) ? $_POST['phone_number'] : '';
    $address = trim(mysqli_real_escape_string($conn, $_POST['address']));
    $note = trim(mysqli_real_escape_string($conn, $_POST['note']));

    if (!$email || !$phone_number) {
        echo '<script>alert("Thông tin email hoặc số điện thoại không hợp lệ!");</script>';
        exit();
    }

    $payment_method = $_POST['payment_method'];  // Lấy phương thức thanh toán

    // Thêm đơn hàng vào cơ sở dữ liệu
    $orderSql = "INSERT INTO orders (fullname, email, phone_number, address, note, id_user, payment_method) 
    VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($orderSql);
    $stmt->bind_param('sssssis', $fullname, $email, $phone_number, $address, $note, $id_user, $payment_method);
    $stmt->execute();
    $orderId = $stmt->insert_id;

    // Lưu chi tiết đơn hàng vào bảng order_details
    foreach ($cartList as $item) {
        foreach ($cart as $value) {
            if ($value['id'] == $item['id'] && $value['size'] == $item['size']) {
                $quantity = $value['num'];
                $orderDetailSql = "INSERT INTO order_details (order_id, product_id, size, num, price, id_user) 
                VALUES ($orderId, {$item['id']}, '{$item['size']}', $quantity, {$item['price']}, $id_user)";  

                executeInsert($orderDetailSql);
            }
        }
    }

    // Kiểm tra phương thức thanh toán và hiển thị thông báo
    if ($payment_method == 'COD') {
        // Xóa giỏ hàng (cookie)
        setcookie('cart', '', time() - 3600, '/'); // Xóa giỏ hàng trong cookie

        // Hiển thị thông báo thành công khi chọn thanh toán khi nhận hàng (COD)
        echo '<script>
                alert("Đặt hàng thành công! Chúng tôi sẽ liên hệ với bạn để giao hàng.");
                window.location = "cart.php"; 
              </script>';
        exit();
    }
    // Kiểm tra phương thức thanh toán và hiển thị thông báo
    if ($payment_method == 'COD') {
        // Hiển thị thông báo thành công khi chọn thanh toán khi nhận hàng (COD)
        echo '<script>
                alert("Đặt hàng thành công! Chúng tôi sẽ liên hệ với bạn để giao hàng.");
                window.location = "index.php"; 
              </script>';
        exit();
    }
    // $hashdata = urldecode(http_build_query($inputData));
    if ($payment_method == 'VNPay') {
        date_default_timezone_set('Asia/Ho_Chi_Minh');

        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_Returnurl = "https://localhost/thanks";  // Địa chỉ trả về của bạn sau khi thanh toán
        $vnp_TmnCode = "TLJF1MV9";  // Mã website tại VNPAY
        $vnp_HashSecret = " 4JWUX5MRN8HWL8DTDOMCXIXS5E8EABTB";  // Chuỗi bí mật từ VNPAY
    
        $vnp_TxnRef = $orderId;  // Mã đơn hàng
        $vnp_OrderInfo = 'Thanh toán đơn hàng ' . $orderId;
        $vnp_OrderType = 'billpayment';
        $vnp_Amount = $total * 100;  // VNPay yêu cầu giá trị là đồng (100 VNĐ = 1VND)
        $vnp_Locale = 'vn';  // Ngôn ngữ
        $vnp_BankCode = 'NCB';  // Mã ngân hàng, ví dụ 'NCB'
    
        // Thêm địa chỉ IP
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];
        
        // Tạo các tham số của VNPay
        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef
        );
    
        // Thêm mã ngân hàng nếu có
        if (isset($vnp_BankCode) && $vnp_BankCode != "") {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }
       
        // Sắp xếp các tham số theo thứ tự
       // Sắp xếp các tham số theo thứ tự từ A-Z
       ksort($inputData);
       $hashdata = "";
       foreach ($inputData as $key => $value) {
           $hashdata .= $key . '=' . $value . '&'; // Nối tham số theo đúng định dạng
       }
       $hashdata = rtrim($hashdata, '&'); // Xóa ký tự '&' thừa ở cuối
       
       // Tính toán chữ ký HMAC-SHA512
       $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
       
       // Thêm chữ ký vào URL thanh toán
       $vnp_Url .= '?' . $hashdata . '&vnp_SecureHash=' . $vnpSecureHash;

    
        // Chuyển hướng đến VNPay
        header('Location: ' . $vnp_Url);
        exit();  // Dừng mã PHP để không thực thi thêm mã phía dưới
    }
    
    
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
                <h4 style="text-align: center; font-size: 35px; font-weight: bold;">Tiến hành thanh toán</h4>
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
                                <div class="form-group">
    <label for="payment_method">Chọn hình thức thanh toán:</label><br>
    <input type="radio" id="cod" name="payment_method" value="COD" checked>
    <label for="cod">Thanh toán khi nhận hàng</label><br>
    <input type="radio" id="vnpay" name="payment_method" value="VNPay">
    <label for="vnpay">Thanh toán VNPay</label><br>
</div>

<div class="form-group">
    <button type="submit" class="btn btn-success">Đặt hàng</button>
</div>

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