<?php require('layout/header.php') ?>
<style>
    main {
        font-family: "Encode Sans SC", sans-serif;
    }

    h3 {
        color: #8B4513; /* Màu tiêu đề */
        font-size: 24px;
        margin-bottom: 10px;
        font-weight: bold;
    }

    

    p {
        
        font-size: 16px;
        line-height: 1.6;
        margin-bottom: 15px;
        font-weight: 100;
    }

    /* Thiết kế cho các liên kết và đoạn văn */
    a {
        color: #8B4513; /* Màu liên kết */
        text-decoration: none;
    }

    a:hover {
        text-decoration: underline;
    }
</style>
<main>
    <div class="container">
        <div id="ant-layout">
            <section class="main">
                <!-- Giới thiệu chung -->
                <div class="row">
                    <h3>Về Coffee Shop</h3>
                    <p>Chào mừng bạn đến với website chính thức của Coffee Shop! Đây là nơi chúng tôi mang đến cho bạn trải nghiệm thưởng thức cà phê và đồ uống tuyệt hảo cùng với không gian thư giãn đậm chất riêng. Hãy khám phá thực đơn đa dạng và dịch vụ tiện ích mà chúng tôi cung cấp!</p>
                </div>
                
                <!-- Tầm nhìn và sứ mệnh -->
                <div class="row">
                    <h3>Tầm nhìn và Sứ mệnh</h3>
                    <p>Chúng tôi không chỉ phục vụ đồ uống mà còn lan tỏa giá trị của sự gắn kết, niềm vui và cảm giác thân thuộc. Coffee Shop hướng tới trở thành điểm đến lý tưởng để bạn tận hưởng những khoảnh khắc đáng nhớ bên bạn bè và gia đình.</p>
                </div>
                
                <!-- Dịch vụ nổi bật -->
                <div class="row">
                    <h3>Dịch vụ của chúng tôi</h3>
                    <ul style="padding-left: 20px;">
                        <li>Thực đơn đồ uống phong phú, từ cà phê nguyên chất đến các loại trà, sinh tố và bánh ngọt.</li>
                        <li>Dịch vụ đặt hàng trực tuyến, giao hàng tận nơi nhanh chóng.</li>
                        <li>Không gian quán phù hợp để làm việc, gặp gỡ bạn bè hoặc tổ chức sự kiện nhỏ.</li>
                    </ul>
                </div>
                
                <!-- Hình ảnh minh họa -->
                <div class="row">
                    <img src="images/bg/coffee_shop.jpg" alt="Không gian quán Coffee Shop" style="max-width: 100%; border-radius: 8px; margin-top: 15px;">
                </div>
                
                <!-- Vị trí quán -->
                <section class="map-section">
                    <h3>Địa chỉ của chúng tôi</h3>
                    <p>Hãy ghé thăm quán của chúng tôi tại địa chỉ dưới đây hoặc đặt hàng trực tuyến để thưởng thức đồ uống yêu thích ngay tại nhà!</p>
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3820.280539447332!2d107.29400661486774!3d16.762712588455788!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x80f122cab2275a42!2zTmd1eeG7hW4gxJDEg25nIFRow6BuaA!5e0!3m2!1svi!2s!4v1629007864673!5m2!1svi!2s" 
                            width="100%" height="450" style="border:0; border-radius: 8px;" allowfullscreen="" loading="lazy"></iframe>
                </section>
                
                <!-- Video giới thiệu -->
                <section class="video-section">
                    <h3>Video Giới Thiệu Coffee Shop</h3>
                    <iframe width="100%" height="315" src="https://www.youtube.com/embed/jJoFCFcJHsI" title="Video giới thiệu Coffee Shop" frameborder="0" 
                            style="border-radius: 8px;" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                </section>
            </section>
        </div>
    </div>
</main>

<?php require('layout/footer.php') ?>