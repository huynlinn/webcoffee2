<?php require "layout/header.php"; ?>
<?php
require_once('database/config.php');
require_once('database/dbhelper.php');
?>
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
            <!-- <section class="main-layout">
                <div class="row">
                    <?php
                    $sql = 'select * from category';
                    $categoryList = executeResult($sql);
                    $index = 1;
                    foreach ($categoryList as $item) {
                        echo '
                                    <div class="box">
                                        <a href="thucdon.php?id_category=' . $item['id'] . '">
                                            <p>' . $item['name'] . '</p>
                                            <div class="bg"></div>
                                            <img src="images/bg/gantoi.jpeg" alt="">
                                        </a>
                                    </div>
                                    ';
                    }
                    ?>
                </div>
            </section> -->
        </div>
        <section class="program-carousel">
    <h2 class="left-align" >Chương trình của quán</h2>
    <div id="programCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="3000">
        <div class="carousel-inner">
            <!-- Slide 1 -->
            <div class="carousel-item active">
                <a href="link-to-detail-page-1">
                    <img src="images/icon/ct1.png" class="d-block w-100" alt="Chương trình 1">
                </a>
            </div>
            <!-- Slide 2 -->
            <div class="carousel-item">
                <a href="link-to-detail-page-2">
                    <img src="images/icon/ct2.png" class="d-block w-100" alt="Chương trình 2">
                </a>
            </div>
            <!-- Slide 3 -->
            <div class="carousel-item">
                <a href="link-to-detail-page-3">
                    <img src="images/icon/ct3.png" class="d-block w-100" alt="Chương trình 3">
                </a>
            </div>
            <!-- Thêm nhiều slide nếu cần -->
        </div>
        <!-- Điều hướng carousel -->
        <button class="carousel-control-prev" type="button" data-bs-target="#programCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#programCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
</section>



       
        <!-- END LAYOUT  -->
        <section class="main">

            <section class="restaurants">
                <div class="title">
                    <h1>Thực đơn tại quán</h1>
                </div>
                <div class="product-restaurants">
                    <div class="row">
                        <?php
                        try {
                            if (isset($_GET['page'])) {
                                $page = $_GET['page'];
                            } else {
                                $page = 1;
                            }
                            $limit = 12;
                            $start = ($page - 1) * $limit;
                            //giá thấp nhất
                            $sql = "
                            SELECT 
                                product.id, 
                                product.title, 
                                product.thumbnail, 
                                MIN(product_size.price) AS price 
                            FROM product 
                            LEFT JOIN product_size ON product.id = product_size.product_id 
                            GROUP BY product.id 
                            LIMIT $start, $limit
                        ";
                        $productList = executeResult($sql);
                        
                           
                            $index = 1;
                            foreach ($productList as $item) {
                                echo '
                                <div class="col">
                                    <a href="details.php?id=' . $item['id'] . '">
                                        <img class="thumbnail" src="admin/product/'. $item['thumbnail'] . '" alt="">
                                        <div class="title">
                                            <p>' . $item['title'] . '</p>
                                        </div>
                                        <div class="price">
                                            <span>' . number_format($item['price'], 0, ',', '.') . ' VNĐ</span>
                                        </div>
                                        
                                    </a>
                                </div>
                                ';
                            }
                        } catch (Exception $e) {
                            die("Lỗi thực thi sql: " . $e->getMessage());
                        }
                        ?>
                    </div>
                    <div class="pagination">
                        <ul>
                            <?php
                            $sql = "SELECT * FROM `product`";
                            $conn = mysqli_connect(HOST, USERNAME, PASSWORD, DATABASE);
                            $result = mysqli_query($conn, $sql);
                            if (mysqli_num_rows($result)) {
                                $numrow = mysqli_num_rows($result);
                                $current_page = ceil($numrow / 12);
                                
                            }
                            for ($i = 1; $i <= $current_page; $i++) {
                                // Nếu là trang hiện tại thì hiển thị thẻ span
                                // ngược lại hiển thị thẻ a
                                if ($i == $current_page) {
                                    echo '
                                    <li><a href="?page=' . $i . '">' . $i . '</a></li>';
                                } else {
                                    echo '
                                    <li><a href="?page=' . $i . '">' . $i . '</a></li>';
                                }
                            }
                            ?>
                        </ul>
                    </div>
                </div>
            </section>
        </section>
    </div>
</main>
<?php require_once('layout/footer.php'); ?>
</div>
</body>

</html>