<?php require('layout/header.php') ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Về Coffee Shop</title>
    <!-- Thêm Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
</head>

<body>
    <!-- Section About Us -->
    <section class="about-us">
        <div class="overlay">
            <h3>Về Coffee Shop</h3>
            <h6>Chào mừng bạn đến với website chính thức của Coffee Shop! Đây là nơi chúng tôi mang đến cho bạn trải nghiệm thưởng thức cà phê và đồ uống tuyệt hảo cùng với không gian thư giãn đậm chất riêng. Hãy khám phá thực đơn đa dạng và dịch vụ tiện ích mà chúng tôi cung cấp!</h6>
        </div>
    </section>

    <div class="service">
        <h4>SINCE 2023</h4>
    </div>

    <!-- New Section with Image and Description -->
    <section class="about-details">
        <div class="container">
            <!-- Left Side: Description about Coffee Shop -->
            <div class="description">
                <h5>Thực đơn và Dịch vụ</h5>
                <p>Thực đơn đồ uống phong phú, từ cà phê nguyên chất đến các loại trà, sinh tố và bánh ngọt.</p>
                <p>Dịch vụ đặt hàng trực tuyến, giao hàng tận nơi nhanh chóng.</p>
                <p>Không gian quán phù hợp để làm việc, gặp gỡ bạn bè hoặc tổ chức sự kiện nhỏ.</p>
            </div>
            <div class="image-center">
            <img src="https://demo.htmlcodex.com/1528/coffee-shop-html-template/img/about.png" alt="About Coffee Shop Image">
        </div>
            <!-- Right Side: Vision and Mission -->
            <div class="vision">
                <h5>Tầm nhìn và Sứ mệnh</h5>
                <p>Chúng tôi không chỉ phục vụ đồ uống mà còn lan tỏa giá trị của sự gắn kết, niềm vui và cảm giác thân thuộc. Coffee Shop hướng tới trở thành điểm đến lý tưởng để bạn tận hưởng những khoảnh khắc đáng nhớ bên bạn bè và gia đình.</p>
            </div>
        </div>

        <!-- Image at the center -->
        
    </section>
</body>
</html>

<?php require('layout/footer.php') ?>
<style>
 /* Định dạng chung cho các phần tử */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: Arial, sans-serif;
}

/* Định dạng cho phần About Us */
.about-us {
    position: relative;
    width: 100%;
    height: 100vh;  /* Full màn hình */
    background-image: url('https://hoanghamobile.com/tin-tuc/wp-content/webp-express/webp-images/uploads/2024/08/anh-cafe.jpg.webp');
    background-size: cover;
    background-position: center;
    margin-top: 8%;
}

.about-us .overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.4);  /* Màu đen mờ */
    color: white;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    text-align: center;
    padding: 20px;
}

.about-us h3 {
    font-size: 36px;
    font-weight: bold;
    margin-bottom: 20px;
    font-family: 'Pacifico', cursive;
}

.about-us h6 {
    font-size: 18px;
    max-width: 800px;
    line-height: 1.6;
    color: burlywood;
}

.service h4 {
    font-weight: bold;
    font-size: 40px;
    color: brown;
    margin-top: 15%;
    text-align: center;
}

/* Section về chi tiết Coffee Shop */
.about-details {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin: 50px 0;
    padding: 0 20px;
}

/* Mô tả về quán (bên trái) */
.description {
    width: 30%;
}

.description h5 {
    font-size: 37px;
    font-weight: bold;
    margin-bottom: 10px;
    color: brown;
}

.description p {
    font-size: 16px;
    color: black;
    font-weight: 200;
    font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
}

/* Tầm nhìn và sứ mệnh (bên phải) */
.vision {
    width: 30%;
}
.container{
    display: flex;
    margin-top:10px;
}
.vision h5 {
    font-size: 37px;
    font-weight: bold;
    line-height: 1.6;
    margin-bottom: 10px;
    color: brown;
}

.vision p {
    font-size: 16px;
    color: black;
    font-weight: 200;
    font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
}

/* Hình ảnh nằm giữa 2 phần */
.image-center {
    text-align: center;
    width: 30%;
}

.image-center img {
    max-width: 100%;
    height: auto;
    border-radius: 10px;
}

</style>