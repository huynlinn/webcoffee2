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
        <li class="nav-item"><a class="nav-link" href="/coffeeshop/index.php">Đăng xuất</a></li>
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
                <?php
                    $limit = 5;
                    $page = isset($_GET['page']) ? $_GET['page'] : 1;
                    $start = ($page - 1) * $limit;
                    
                    $sql = "
                    SELECT 
                        p.id, 
                        p.title, 
                        p.thumbnail, 
                        p.content, 
                        p.id_category, 
                        ps.size, 
                        ps.price,
                        c.name AS category_name
                    FROM 
                        (SELECT * FROM product LIMIT $start, $limit) AS p_ids  -- Truy vấn con để phân trang sản phẩm
                    JOIN product p ON p.id = p_ids.id  -- JOIN lại với bảng product
                    LEFT JOIN product_size ps ON p.id = ps.product_id  -- JOIN với bảng product_size
                    LEFT JOIN category c ON p.id_category = c.id  -- JOIN với bảng category
                    ORDER BY p.id;

                    ";
                    
                    $productList = executeResult($sql);
                    
                    // Xử lý kết quả để nhóm các size cùng một sản phẩm
                    $groupedProducts = [];
                    foreach ($productList as $item) {
                        // Nếu sản phẩm chưa có trong mảng thì tạo mới
                        if (!isset($groupedProducts[$item['id']])) {
                            $groupedProducts[$item['id']] = [
                                'id' => $item['id'],
                                'title' => $item['title'],
                                'thumbnail' => $item['thumbnail'],
                                'content' => $item['content'],
                                'id_category' => $item['id_category'],
                                'category_name' => $item['category_name'],
                                'sizes' => [] // Mảng chứa các size của sản phẩm
                            ];
                        }
                    
                        // Thêm size vào sản phẩm
                        $groupedProducts[$item['id']]['sizes'][] = [
                            'size' => $item['size'],
                            'price' => $item['price']
                        ];
                    }
                
               
                ?>
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr style="font-weight: 500;">
                            <td width="70px">STT</td>
                            <td>Thumbnail</td>
                            <td>Tên Sản Phẩm</td>
                            <td>Giá (Small)</td>
                            <td>Nội dung</td>
                            <td>Danh Mục</td>
                            <td>Size</td>
                            <td width="50px"></td>
                            <td width="50px"></td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($groupedProducts)) {
                            $index = $start + 1;
                            
                        
                            foreach ($groupedProducts as $product) {
                                $smallestPrice = min(array_column($product['sizes'], 'price'));
                                echo '<tr>
                                   <td>' . htmlspecialchars($product['id']) . '</td>
                                    <td style="text-align: center;">
                                        <img src="' . htmlspecialchars($product['thumbnail']) . '" alt="" style="width: 50px;">
                                    </td>
                                    <td>' . htmlspecialchars($product['title']) . '</td>
                                     <td>' . number_format($smallestPrice, 0, ',', '.') . ' VNĐ</td> 
                                    <td>' . htmlspecialchars($product['content']) . '</td>
                                    <td>' . htmlspecialchars($product['category_name']) . '</td>
                                    <td>';

                                // Hiển thị tất cả các size cho sản phẩm này
                                foreach ($product['sizes'] as $size) {
                                    echo htmlspecialchars($size['size']) . ' (' . number_format($size['price'], 0, ',', '.') . ' VNĐ)<br>';
                                }

                                echo '</td>
                                    <td>
                                        <a href="add.php?id=' . $product['id'] . '">
                                            <button class="btn btn-warning">Sửa</button>
                                        </a>
                                    </td>
                                    <td>
                                        <button class="btn btn-danger" onclick="deleteProduct(' . $product['id'] . ')">Xoá</button>
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
        console.log(id)
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
