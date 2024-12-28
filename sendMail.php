<?php session_start() ?>
<?php require_once('layout/header.php'); ?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css" />
    <style>
        body {
            background-color: #f9f9f9;
            font-family: 'Arial', sans-serif;
        }

        .contact-container {
            max-width: 800px;
            margin: 100px auto 50px; /* Đẩy form xuống xa hơn header */
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-top:10%;
        }

        .contact-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .contact-header h2 {
            color: brown;
            font-size: 28px;
            font-weight: bold;
        }

        .contact-header p {
            color: #777;
            font-size: 16px;
        }

        .form-group label {
            font-weight: bold;
            color: #555;
        }

        .form-group input,
        .form-group textarea {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px;
            font-size: 14px;
        }

        .form-group textarea {
            resize: none;
        }

        .btn-submit {
            margin-top:20px;
            background-color: burlywood; /* Thay đổi màu nút */
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .btn-submit:hover {
            background-color: #d2b48c; /* Màu sáng hơn khi hover */
            cursor: pointer;
        }

        .contact-info {
            margin-top: 40px;
            text-align: center;
        }

        .contact-info h4 {
            color: #555;
            font-size: 18px;
        }

        .contact-info p {
            color: #777;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <div class="contact-container">
        <div class="contact-header">
            <h2>Liên hệ với chúng tôi</h2>
            <p>Gửi yêu cầu của bạn, chúng tôi sẽ phản hồi sớm nhất có thể!</p>
        </div>
        <form method="POST" action="">
            <div class="form-group">
                <label for="name">Tên của bạn:</label>
                <input type="text" name="name" class="form-control" id="name" placeholder="Nhập tên của bạn" required>
            </div>
            <div class="form-group">
                <label for="email">Email của bạn:</label>
                <input type="email" name="email" class="form-control" id="email" placeholder="Nhập email của bạn" required>
            </div>
            <div class="form-group">
                <label for="subject">Tiêu đề yêu cầu:</label>
                <input type="text" name="subject" class="form-control" id="subject" placeholder="Nhập tiêu đề" required>
            </div>
            <div class="form-group">
                <label for="message">Nội dung mail:</label>
                <textarea name="message" class="form-control" id="message" rows="5" placeholder="Nhập nội dung" required></textarea>
            </div>
            <div class="text-center">
                <button type="submit" name="send" class="btn-submit">Gửi yêu cầu</button>
            </div>
        </form>
        <div class="contact-info">
            <h4>Thông tin liên hệ</h4>
            <p>Email: support@coffeeshop.com</p>
            <p>Điện thoại: 0123-456-789</p>
            <p>Địa chỉ: 123 Đường ABC, Quận XYZ, TP. Hồ Chí Minh</p>
        </div>
    </div>

    <?php
    require "libs/PHPMailer-master/src/PHPMailer.php";
    require "libs/PHPMailer-master/src/SMTP.php";
    require "libs/PHPMailer-master/src/Exception.php";

    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $subject = $_POST['subject'];
        $message = $_POST['message'];

        $mail = new PHPMailer\PHPMailer\PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->CharSet = "utf-8";
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $nguoigui = 'kqtran123@gmail.com';
            $matkhau = 'ksvt azaw xqzi thjl';
            $mail->SMTPSecure = 'ssl';
            $mail->Port = 465;

            $mail->Username = $nguoigui;
            $mail->Password = $matkhau;
            $mail->setFrom($nguoigui, "Coffee Shop");
            $mail->addAddress('ldinh2025@gmail.com', "Coffee Shop Admin");
            $mail->addReplyTo($email, $name);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = "Bạn có một yêu cầu mới từ khách hàng: <br><br>" .
                "Tên khách hàng: $name<br>" .
                "Email khách hàng: $email<br>" .
                "Tiêu đề yêu cầu: $subject<br>" .
                "Nội dung: <br>" . nl2br($message);

            $mail->send();
            echo '<center style="color: green; font-weight: bold;">Yêu cầu của bạn đã được gửi đi thành công!</center>';
        } catch (Exception $e) {
            echo '<center style="color: red;">Mail không gửi được. Lỗi: ' . $mail->ErrorInfo . '</center>';
        }
    }
    ?>
</body>
<?php require_once('layout/footer.php'); ?>

</html>
