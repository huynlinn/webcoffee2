<?php
require_once('config.php');

function execute($sql, $params = [])
{
    // mở kết nối đến cơ sở dữ liệu
    $con = mysqli_connect(HOST, USERNAME, PASSWORD, DATABASE);
    mysqli_set_charset($con, "utf8");

    // sử dụng prepared statements để tránh SQL injection
    $stmt = mysqli_prepare($con, $sql);

    // Gán các tham số vào câu lệnh SQL nếu có
    if ($params) {
        // Gán tham số dựa trên kiểu dữ liệu: "s" cho string, "i" cho integer, "d" cho double, ...
        $types = str_repeat("s", count($params));  // giả sử tất cả tham số là kiểu string
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }

    // thực thi câu lệnh
    mysqli_stmt_execute($stmt);

    // đóng kết nối
    mysqli_close($con);
}

// Trước khi thực hiện INSERT vào bảng product_size, kiểm tra xem product_id có tồn tại không
function isProductExist($product_id)
{
    $sql = "SELECT COUNT(*) as count FROM product WHERE id = ?";
    $result = executeSingleResult($sql, [$product_id]);

    // Kiểm tra kết quả trả về
    if ($result && isset($result['count'])) {
        return $result['count'] > 0;
    } else {
        return false;
    }
}


function addProductSize($product_id, $size, $price)
{
    // Kiểm tra xem product_id có tồn tại không
    if (!isProductExist($product_id)) {
        echo "Product does not exist.";
        return false; // Dừng hàm nếu không tìm thấy product
    }

    // Nếu tồn tại, thực hiện câu lệnh INSERT vào product_size
    $sql = "INSERT INTO product_size (product_id, size, price) VALUES (?, ?, ?)";
    execute($sql, [$product_id, $size, $price]);

    return true;
}



function executeResult($sql)
{
	//save data into table
	// open connection to database
	$con = mysqli_connect(HOST, USERNAME, PASSWORD, DATABASE);
	mysqli_set_charset($con, "utf8");
	//insert, update, delete
	$result = mysqli_query($con, $sql);
	$data   = [];
	while ($row = mysqli_fetch_array($result, 1)) {
		$data[] = $row;
	}

	//close connection
	mysqli_close($con);

	return $data;
}

function executeSingleResult($sql, $params = [])
{
    // mở kết nối đến cơ sở dữ liệu
    $con = mysqli_connect(HOST, USERNAME, PASSWORD, DATABASE);
    mysqli_set_charset($con, "utf8");

    // sử dụng prepared statements để tránh SQL injection
    $stmt = mysqli_prepare($con, $sql);

    // Gán các tham số vào câu lệnh SQL nếu có
    if ($params) {
        $types = str_repeat("s", count($params)); // Giả sử tất cả tham số là kiểu string
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }

    // Thực thi câu lệnh
    mysqli_stmt_execute($stmt);

    // Lấy kết quả
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

    // Đảm bảo nếu không có kết quả thì trả về mảng rỗng
    if ($row) {
        return $row;
    } else {
        return [];
    }

    // đóng kết nối
    mysqli_stmt_close($stmt);
    mysqli_close($con);
}


function checkLogin()
{
    if (!isset($_SESSION['user'])) {
        header("Location: ../../index.php");
    }
    $user = isset($_SESSION['user']) ? $_SESSION['user'] : [];

    if ($user['level'] == 2) {
        header("Location: ../../index.php");
    }
}
