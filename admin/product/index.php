<?php
require_once('../database/dbhelper.php');
?>

<!DOCTYPE html>
<html>

<head>
    <title>Quản Lý Sản Phẩm</title>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <!-- jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <!-- Popper JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <!-- Latest compiled JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
</head>

<body>
    <ul class="nav nav-tabs">
        <!-- Navigation items -->
        <li class="nav-item"><a class="nav-link" href="/coffeeshop/admin/index.php">Thống kê</a></li>
        <li class="nav-item"><a class="nav-link" href="/coffeeshop/admin/category/">Quản lý Danh Mục</a></li>
        <li class="nav-item"><a class="nav-link active" href="/coffeeshop/admin/product/">Quản lý sản phẩm</a></li>
        <li class="nav-item"><a class="nav-link" href="/coffeeshop/admin/dashboard.php">Quản lý giỏ hàng</a></li>
        <li class="nav-item"><a class="nav-link" href="/coffeeshop/admin/user">Quản lý người dùng</a></li>
        <li class="nav-item"><a class="nav-link" href="../logout.php">Đăng xuất</a></li>
    </ul>

    <div class="container">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2 class="text-center">Quản lý Sản Phẩm</h2>
            </div>
            <div class="panel-body">
                <a href="add.php">
                    <button class="btn btn-success" style="margin-bottom: 20px;">Thêm Sản Phẩm</button>
                </a>
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr style="font-weight: 500;">
                            <td width="70px">STT</td>
                            <td>Thumbnail</td>
                            <td>Tên Sản Phẩm</td>
                            <td>Giá</td>
                            <td>Nội dung</td>
                            <td>ID Danh Mục</td>
                            <td width="50px"></td>
                            <td width="50px"></td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $limit = 5;
                        $page = isset($_GET['page']) ? $_GET['page'] : 1;
                        $start = ($page - 1) * $limit;

                        // SQL Query: Lấy giá thấp nhất của sản phẩm dựa trên bảng product_size
                        $sql = "
    SELECT 
        product.id, 
        product.title, 
        product.thumbnail, 
        product.content, 
        product.id_category, 
        MIN(product_size.price) AS price 
    FROM product 
    LEFT JOIN product_size ON product.id = product_size.product_id 
    GROUP BY product.id 
    LIMIT $start, $limit
";

                        $productList = executeResult($sql);
                        

                        if (!empty($productList)) {
                            $index = $start + 1;
                            foreach ($productList as $item) {
                                echo '<tr>
                                    <td>' . $index++ . '</td>
                                    <td style="text-align: center;">
                                        <img src="' . htmlspecialchars($item['thumbnail']) . '" alt="" style="width: 50px;">
                                    </td>
                                    <td>' . htmlspecialchars($item['title']) . '</td>
                                    <td>' . number_format($item['price'], 0, ',', '.') . ' VNĐ</td>
                                    <td>' . htmlspecialchars($item['content']) . '</td>
                                    <td>' . htmlspecialchars($item['id_category']) . '</td>
                                    <td>
                                        <a href="add.php?id=' . $item['id'] . '">
                                            <button class="btn btn-warning">Sửa</button>
                                        </a>
                                    </td>
                                    <td>
                                        <button class="btn btn-danger" onclick="deleteProduct(' . $item['id'] . ')">Xoá</button>
                                    </td>
                                </tr>';
                            }
                        } else {
                            echo '<tr><td colspan="9" class="text-center">Không có sản phẩm nào</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <ul class="pagination">
                <?php
                $sql = "SELECT COUNT(*) AS total FROM product";
                $result = executeSingleResult($sql);
                $totalRecords = $result['total'];
                $totalPages = ceil($totalRecords / $limit);

                for ($i = 1; $i <= $totalPages; $i++) {
                    if ($i == $page) {
                        echo '<li class="page-item active"><span class="page-link">' . $i . '</span></li>';
                    } else {
                        echo '<li class="page-item"><a class="page-link" href="?page=' . $i . '">' . $i . '</a></li>';
                    }
                }
                ?>
            </ul>
        </div>
    </div>

    <script type="text/javascript">
        function deleteProduct(id) {
            var option = confirm('Bạn có chắc chắn muốn xoá sản phẩm này không?');
            if (!option) return;

            $.post('ajax.php', {
                id: id,
                action: 'delete'
            }, function(data) {
                location.reload();
            });
        }
    </script>
</body>

</html>
