<?php
require_once('../database/dbhelper.php');

$id = $title = $size = $price = $number = $thumbnail = $content = $id_category = "";
if (!empty($_POST['title'])) {
    // Lấy dữ liệu từ form
    if (isset($_POST['title'])) {
        $title = $_POST['title'];
        $title = str_replace('"', '\\"', $title);
    }
    if (isset($_POST['id'])) {
        $id = $_POST['id'];
        $id = str_replace('"', '\\"', $id);
    }
    if (isset($_POST['price'])) {
        $price = $_POST['price'];
        if (!is_numeric($price)) {
            die('Giá sản phẩm không hợp lệ');
        }
        $price = str_replace('"', '\\"', $price);
    }
    if (isset($_POST['number'])) {
        $number = $_POST['number'];
        $number = str_replace('"', '\\"', $number);
    }

    if (isset($_POST['size'])) {
        $size = $_POST['size'];
        $size = str_replace('"', '\\"', $size);
    }

    // Kiểm tra upload file thumbnail
    if (!isset($_FILES["thumbnail"])) {
        echo "Dữ liệu không đúng cấu trúc";
        die;
    }

    if (isset($_FILES["thumbnail"]) && $_FILES["thumbnail"]['name'] !== '' && $_FILES["thumbnail"]['error'] != 0) {
        echo "Dữ liệu upload bị lỗi";
        die;
    }

    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["thumbnail"]["name"]);
    $allowUpload = true;
    $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
    $maxfilesize = 800000;
    $allowtypes = array('jpg', 'png', 'jpeg', 'gif');

    if ($_FILES["thumbnail"]["size"] > $maxfilesize) {
        echo "Không được upload ảnh lớn hơn $maxfilesize (bytes).";
        $allowUpload = false;
    }

    if (!in_array($imageFileType, $allowtypes)) {
        echo "Chỉ được upload các định dạng JPG, PNG, JPEG, GIF";
        $allowUpload = false;
    }

    if ($allowUpload) {
        if (move_uploaded_file($_FILES["thumbnail"]["tmp_name"], $target_file)) {
        } else {
            echo "Có lỗi xảy ra khi upload file.";
        }
    } else {
        echo "Không upload được file, có thể do file lớn, kiểu file không đúng ...";
    }

    if (isset($_POST['content'])) {
        $content = $_POST['content'];
        $content = str_replace('"', '\\"', $content);
    }

    if (isset($_POST['id_category'])) {
        $id_category = $_POST['id_category'];
        $id_category = str_replace('"', '\\"', $id_category);
    }

    if (!empty($title)) {
        $created_at = $updated_at = date('Y-m-d H:s:i');
        // Lưu vào DB
        if ($id == '') {
            $queries = [
                [
                    'sql' => "INSERT INTO product (title, number, thumbnail, content, id_category, created_at, updated_at) 
                              VALUES (?, ?, ?, ?, ?, ?, ?)",
                    'params' => [$title, $number, $target_file, $content, $id_category, $created_at, $updated_at]
                ],
                [
                    'sql' => "INSERT INTO product_size (product_id, size, price) 
                              VALUES (LAST_INSERT_ID(), ?, ?)",
                    'params' => [$size, $price]
                ]
            ];
            executeBatch($queries);
            header('Location: index.php');
            exit();
        }
        if ($id != '') {
            // Sửa thông tin sản phẩm trong bảng `product`
            $sql = 'UPDATE product SET title=?, number=?, thumbnail=?, content=?, id_category=?, updated_at=? WHERE id=?';
            execute($sql, [$title, $number, $target_file, $content, $id_category, $updated_at, $id]);
        
            // Kiểm tra và sửa thông tin size và price
            $sql_check_size = 'SELECT * FROM product_size WHERE product_id=? AND size=?';
            $size_data = executeSingleResult($sql_check_size, [$id, $size]);
        
            if ($size_data) {
                // Nếu đã tồn tại size này, chỉ cần cập nhật giá
                $sql_update_price = 'UPDATE product_size SET price=? WHERE product_id=? AND size=?';
                execute($sql_update_price, [$price, $id, $size]);
            } else {
                // Nếu chưa tồn tại, thêm mới
                $sql_insert_size = 'INSERT INTO product_size (product_id, size, price) VALUES (?, ?, ?)';
                execute($sql_insert_size, [$id, $size, $price]);
            }
        
            // Chuyển hướng sau khi hoàn tất
            header('Location: index.php');
            exit();
        }
    }        
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = 'SELECT * FROM product WHERE id=' . $id;
    $product = executeSingleResult($sql);

    if ($product != null) {
        $title = $product['title'];
        $number = $product['number'];
        $thumbnail = $product['thumbnail'];
        $content = $product['content'];
        $id_category = $product['id_category'];
        $created_at = $product['created_at'];
        $updated_at = $product['updated_at'];
    }

    // Lấy thông tin size và price
    $sql_size_price = 'SELECT * FROM product_size WHERE product_id=' . $id . ' LIMIT 1';
    $size_price = executeSingleResult($sql_size_price);

    if ($size_price != null) {
        $size = $size_price['size'];
        $price = $size_price['price'];
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Thêm Sản Phẩm</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
</head>
<body>
    <ul class="nav nav-tabs">
        <li class="nav-item"><a class="nav-link" href="../index.php">Thống kê</a></li>
        <li class="nav-item"><a class="nav-link" href="index.php">Quản lý danh mục</a></li>
        <li class="nav-item"><a class="nav-link" href="../product/">Quản lý sản phẩm</a></li>
        <li class="nav-item"><a class="nav-link" href="../dashboard.php">Quản lý giỏ hàng</a></li>
        <li class="nav-item"><a class="nav-link" href="user/">Quản lý người dùng</a></li>
        <li class="nav-item"><a class="nav-link" href="../logout.php">Đăng xuất</a></li>
    </ul>
    <div class="container">
        <div class="panel panel-primary">
            <div class="panel-heading"><h2 class="text-center">Thêm/Sửa Sản Phẩm</h2></div>
            <div class="panel-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="name">Tên Sản Phẩm:</label>
                        <input type="hidden" id="id" name="id" value="<?= htmlspecialchars($id) ?>">

                        <input required="true" type="text" class="form-control" id="title" name="title" value="<?= $title ?>">
                    </div>
                    <div class="form-group">
                        <label for="exampleFormControlSelect1">Chọn Danh Mục</label>
                        <select class="form-control" id="id_category" name="id_category">
                            <option>Chọn danh mục</option>
                            <?php
                            $sql = 'select * from category';
                            $categoryList = executeResult($sql);
                            foreach ($categoryList as $item) {
                                echo '<option value="' . $item['id'] . '" ' . ($item['id'] == $id_category ? 'selected' : '') . '>' . $item['name'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="name">Giá Sản Phẩm:</label>
                        <input required="true" type="text" class="form-control" id="price" name="price" value="<?= $price ?>">
                    </div>
                    <div class="form-group">
                        <label for="size">Size Sản Phẩm:</label>
                        <select class="form-control" id="size" name="size">
                            <option value="S" <?= $size == 'S' ? 'selected' : '' ?>>S</option>
                            <option value="M" <?= $size == 'M' ? 'selected' : '' ?>>M</option>
                            <option value="L" <?= $size == 'L' ? 'selected' : '' ?>>L</option>
                            <option value="No Size" <?= $size == 'No Size' ? 'selected' : '' ?>>No Size</option>
                        </select>
                    </div>
                    <!-- <div class="form-group">
                        <label for="name">Số Lượng:</label>
                        <input required="true" type="text" class="form-control" id="number" name="number" value="<?= $number ?>">
                    </div> -->
                    <div class="form-group">
                        <label for="name">Hình Ảnh:</label>
                        <input type="file" class="form-control" id="thumbnail" name="thumbnail">
                        <img src="<?= $thumbnail ?>" style="max-width: 200px;" />
                    </div>
                    <div class="form-group">
                        <label for="content">Nội Dung Sản Phẩm:</label>
                        <textarea class="form-control" id="content" name="content" rows="5"><?= $content ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Lưu</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
