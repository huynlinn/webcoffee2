<?php require "layout/header.php"; ?>
<?php
require_once('database/config.php');
require_once('database/dbhelper.php');
require_once('utils/utility.php');
// Lấy id từ trang index.php truyền sang rồi hiển thị nó
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = 'select * from product where id=' . $id;
    $product = executeSingleResult($sql);
    // Kiểm tra nếu ko có id sp đó thì trả về index.php
    if ($product == null) {
        header('Location: index.php');
        die();
    }
    $sqlSizes = 'SELECT size, price FROM product_size WHERE product_id=' . $id;
    $sizes = executeResult($sqlSizes);
}
//buy now

?>
<div id="fb-root"></div>
<script async defer crossorigin="anonymous" src="https://connect.facebook.net/vi_VN/sdk.js#xfbml=1&version=v11.0&appId=264339598396676&autoLogAppEvents=1" nonce="8sTfFiF4"></script>
<!-- END HEADR -->
<main>
    <div class="container">
        <div id="ant-layout">
        <section class="search-quan">
                <i class="fas fa-search"></i>
                <form action="thucdon.php" method="GET">
                    <input name="search" type="text" placeholder="Tìm món hoặc thức ăn">
                </form>
            </section>
        </div>
        <!-- <div class="bg-grey">

        </div> -->
        <!-- END LAYOUT  -->
        <section class="main">
            <section class="oder-product">
                <div class="title">
                    <section class="main-order">
                        <h1><?= $product['title'] ?></h1>
                        <div class="box">
                            <img src="<?='admin/product/'.$product['thumbnail'] ?>" alt="">
                            <div class="about">
                                <p><?= $product['content'] ?></p>
                                <!-- <div class="size">
                                    <p>Size:</p>
                                    <ul>
                                        <li><a href="">S</a></li>
                                        <li><a href="">M</a></li>
                                        <li><a href="">L</a></li>
                                    </ul>
                                </div> -->
                                <div class="size">
    <p>Size:</p>
    <ul>
        <?php if (count($sizes) > 0): ?>
            <!-- Sản phẩm có size, cho phép người dùng chọn size -->
            <?php foreach ($sizes as $size): ?>
                <li>
                    <a href="javascript:void(0)" onclick="selectSize('<?= $size['size'] ?>', <?= $size['price'] ?>)">
                        <?= $size['size'] ?>
                    </a>
                </li>
            <?php endforeach; ?>
        <?php else: ?>
            <!-- Sản phẩm không có size, hiển thị 'No Size' -->
            <li><a href="javascript:void(0)" class="selected">No Size</a></li>
        <?php endif; ?>
    </ul>
</div>


                                <div class="number">
                                    <span class="number-buy">Số lượng</span>
                                    <input id="num" type="number" value="1" min="1" onchange="updatePrice()">
                                </div>
                                <p class="price">
                                 Giá: <span id="price"><?= number_format($sizes[0]['price'], 0, ',', '.') ?></span>
                                  <span> VNĐ</span>
                                 <span class="gia none"><?= $sizes[0]['price'] ?></span>
                                </p>


                                <!-- <p class="price">Giá: <span id="price"><?= number_format($product['price'], 0, ',', '.') ?></span><span> VNĐ</span><span class="gia none"><?= $product['price'] ?></span></p> -->
                                <!-- <a class="add-cart" href="" onclick="addToCart(<?= $id ?>)"><i class="fas fa-cart-plus"></i>Thêm vào giỏ hàng</a> -->
                                <button class="add-cart" onclick="addToCart(<?= $id ?>)"><i class="fas fa-cart-plus"></i>Thêm vào giỏ hàng</button>
                                
                                <!-- <a href= checkout.php><button class="buy-now" onclick="buyNow(<?= $id ?>)">Mua ngay</button></a> -->

                               
    <script>
      
    function selectSize(size, price) {
    // Cập nhật giá khi chọn size
    document.getElementById('price').innerText = price.toLocaleString();
    document.querySelector('.gia').innerText = price;

    // Đánh dấu size đã chọn
    const sizeLinks = document.querySelectorAll('.size a');
    sizeLinks.forEach(link => link.classList.remove('selected'));
    event.target.classList.add('selected');
}
function selectSize(size, price) {
    // Cập nhật giá khi chọn size
    document.getElementById('price').innerText = price.toLocaleString();
    document.querySelector('.gia').innerText = price;

    // Đánh dấu size đã chọn
    const sizeLinks = document.querySelectorAll('.size a');
    sizeLinks.forEach(link => link.classList.remove('selected'));
    event.target.classList.add('selected');
}
let isAddingToCart = false; // Biến trạng thái kiểm tra xem có đang thêm sản phẩm vào giỏ không
function addToCart(id) {
    if (isAddingToCart) return; // Nếu đang thêm vào giỏ, không làm gì cả
    isAddingToCart = true; // Đánh dấu là đang thêm sản phẩm vào giỏ hàng

    var num = document.querySelector('#num').value; // Lấy số lượng
    var size = document.querySelector('.selected') ? document.querySelector('.selected').innerText : 'No Size'; // Lấy size đã chọn hoặc mặc định là 'No Size'
    var price = document.querySelector('.gia').innerText; // Lấy giá sản phẩm đã chọn

    // Kiểm tra nếu sản phẩm có size mà chưa chọn
    if (size === 'No Size' && !document.querySelector('.selected')) {
        alert("Vui lòng chọn size sản phẩm.");
        isAddingToCart = false; // Reset lại trạng thái sau khi kiểm tra
        return;
    }

    // Gửi request thêm sản phẩm vào giỏ
    $.post('api/cookie.php', {
        'action': 'add',
        'id': id,
        'num': num,
        'size': size,  // Gửi size (No Size hoặc size đã chọn)
        'price': price  // Gửi giá sản phẩm
    }, function(data) {
        alert("Sản phẩm đã được thêm vào giỏ hàng!");
        location.reload(); // Tải lại trang sau khi thêm sản phẩm
        isAddingToCart = false; // Reset lại trạng thái sau khi hoàn thành
    }).fail(function() {
        alert("Có lỗi xảy ra khi thêm sản phẩm vào giỏ hàng.");
        isAddingToCart = false; // Reset trạng thái khi có lỗi
    });
}


    function updatePrice() {
        var price = parseFloat(document.querySelector('.gia').innerText); // Giá đã chọn
        var num = document.querySelector('#num').value; // Số lượng
        var totalPrice = price * num;
        document.getElementById('price').innerText = totalPrice.toLocaleString() ;
    }
    //buy now
    function buyNow(id) {
    var num = document.querySelector('#num').value; // Lấy số lượng
    var size = document.querySelector('.selected') ? document.querySelector('.selected').innerText : 'No Size'; // Lấy size đã chọn hoặc mặc định là 'No Size'
    var price = document.querySelector('.gia').innerText; // Lấy giá sản phẩm đã chọn

    // Kiểm tra nếu sản phẩm có size mà chưa chọn
    if (size === 'No Size' && !document.querySelector('.selected')) {
        alert("Vui lòng chọn size sản phẩm.");
        return;
    }

    // Chuyển hướng đến trang thanh toán và truyền các thông tin qua query string
    var checkoutUrl = 'checkout.php?id=' + id + '&num=' + num + '&size=' + encodeURIComponent(size) + '&price=' + price;
    window.location.href = checkoutUrl; // Chuyển hướng đến trang thanh toán
}

   



                                </script>
                            </div>
                        </div>
                        <div class="fb-comments" data-href="http://localhost/PROJECT/details.php" data-width="750" data-numposts="5"></div>

                    </section>
                </div>
                <aside>
                    <h1>Gợi ý cho bạn</h1>
                    <div class="row">
                        <?php
                       $sql = "
                       SELECT 
                           product.id, 
                           product.title, 
                           product.thumbnail, 
                           MIN(product_size.price) AS price
                       FROM product 
                       LEFT JOIN product_size ON product.id = product_size.product_id 
                       WHERE product.id != $id
                       GROUP BY product.id
                       LIMIT 6"; 
                        
                        $productList = executeResult($sql);
                        $index = 1;
                        foreach ($productList as $item) {
                            echo '
                                    <div class="col">
                                    <a href="details.php?id=' . $item['id'] . '">
                                        <img src="admin/product/' . $item['thumbnail'] . '" alt="' . $item['title'] . '">
                                        <div class="about">
                                            <div class="title">
                                                <p>' . $item['title'] . '</p>
                                                 <span>Giá: ' . number_format($item['price'], 0, ',', '.') . ' VNĐ</span>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                ';
                        }
                        ?>
                    </div>
                </aside>
            </section>
            
        </section>
    </div>
</main>
<?php require_once('layout/footer.php'); ?>
</div>
<!-- <script type="text/javascript">
function addToCart(id) {
    var num = document.querySelector('#num').value; // số lượng
    var size = document.querySelector('.selected') ? document.querySelector('.selected').innerText : 'No Size'; // Lấy size đã chọn
    var price = document.querySelector('.gia').innerText; // Lấy giá sản phẩm đã chọn

    // Gửi request thêm sản phẩm vào giỏ
    $.post('api/cookie.php', {
        'action': 'add',
        'id': id,
        'num': num,
        'size': size,  // gửi size
        'price': price  // gửi giá
    }, function(data) {
        alert("Sản phẩm đã được thêm vào giỏ hàng!");
        location.reload(); // Tải lại trang sau khi thêm sản phẩm
    });
}


</script> -->
</body>

</html>
<style>
    .size a.selected {
    color: #007bff;  /* Màu xanh cho size đã chọn */
    font-weight: bold;
}
/* Gợi ý sản phẩm */
aside h1 {
    font-size: 20px;
    margin-bottom: 15px;
    font-weight: bold;
}

.row {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
}

.col {
    flex: 1 1 calc(33.33% - 20px); /* Ba cột cho màn hình lớn, điều chỉnh cho phù hợp với thiết bị nhỏ hơn */
    box-sizing: border-box;
    text-align: center;
}

.col img {
    width: 100%;
    height: auto;
    border-radius: 8px;
}

.col .about {
    margin-top: 10px;
}

.col .title p {
    font-size: 16px;
    font-weight: bold;
}

.col .title span {
    font-size: 14px;
    color: #888;
}


</style>