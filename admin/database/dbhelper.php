<?php
require_once('config.php');

function execute($sql)
{
	//save data into table
	// open connection to database
	$con = mysqli_connect(HOST, USERNAME, PASSWORD, DATABASE);
	//insert, update, delete
	mysqli_set_charset($con, "utf8");
	mysqli_query($con, $sql);

	//close connection
	mysqli_close($con);
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

function executeSingleResult($sql)
{
	//save data into table
	// open connection to database
	$con = mysqli_connect(HOST, USERNAME, PASSWORD, DATABASE);
	mysqli_set_charset($con, "utf8");
	//insert, update, delete
	$result = mysqli_query($con, $sql);
	$row    = mysqli_fetch_array($result, 1);

	//close connection
	mysqli_close($con);

	return $row;
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
