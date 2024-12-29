<?php
require_once('../database/dbhelper.php');

?>
<!DOCTYPE html>
<html>

<head>
    <title>Quản Lý Người Dùng</title>
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
    <li class="nav-item">
        <a class="nav-link" href="../index.php">Thống kê</a>
    </li>
    <li class="nav-item">
        <a class="nav-link active" href="../category/">Quản lý danh mục</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="../product/">Quản lý sản phẩm</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="../dashboard.php">Quản lý giỏ hàng</a>
    </li>
    <li class="nav-item ">
        <a class="nav-link " href="../user/">Quản lý người dùng</a>
    </li>
    <li class="nav-item ">
        <a class="nav-link " href="../logout.php">Đăng xuất</a>
    </li>
</ul>
<div class="container">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h2 class="text-center">Quản lý người dùng</h2>
        </div>
        <div class="panel-body"></div>
        <table class="table table-bordered table-hover">
            <thead>
            <tr>
                <td width="70px">STT</td>
                <td>Họ tên</td>
                <td>Tên đăng nhập</td>
                <td>Số điện thoại</td>
                <td>Email</td>
                <td width="50px"></td>
            </tr>
            </thead>
            <tbody>
            <?php
            // Lấy danh sách danh mục
            $sql = 'select * from user';
            $users = executeResult($sql);
            $index = 1;
            foreach ($users as $item) {
               
                echo '  <tr>
                    <td>' . ($index++) . '</td>
                    <td>' . $item['hoten'] . '</td>
                    <td>' . $item['username'] . '</td>
                    <td>' . $item['phone'] . '</td>
                    <td>' . $item['email'] . '</td>
                   
                    <td>            
                    <button class="btn btn-danger" onclick="deleteCategory('.$item['id_user'].')">Xoá</button>
                    </td>
                </tr>';
            }
            ?>
            </tbody>
        </table>
    </div>
</div>
</div>
<script type="text/javascript">
    function deleteCategory(id) {
        var option = confirm('Bạn có chắc chắn muốn xoá danh mục này không?')
        if(!option) {
            return;
        }
        console.log(id)
        $.post('ajax.php', {
            'id': id,
            'action': 'delete'
        }, function(data) {
            location.reload()
        })
    }
</script>
</body>

</html>