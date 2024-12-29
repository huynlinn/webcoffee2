<?php
require_once('../database/dbhelper.php');

if (isset($_POST['id'])) {
    $id = $_POST['id'];
    
    // Xóa các dòng liên quan trong product_size
    $sql = 'DELETE FROM product_size WHERE product_id = ?';
    execute($sql, [$id]);

    // Xóa sản phẩm trong bảng product
    $sql = 'DELETE FROM product WHERE id = ?';
    execute($sql, [$id]);

    header('Location: index.php'); // Chuyển hướng sau khi xóa
    exit();
}

?>