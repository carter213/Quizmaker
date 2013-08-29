<?php
session_start();

// Check user_id
if (!isset($_SESSION['user_id'])) {
	header('Location: /');
	exit();
}

$user_id = $_SESSION['user_id'];

require '../dbaccess/connect.php';

if (mysqli_connect_errno($con)) {
	header('Location: /');
	exit();
}

mysqli_query($con, "DELETE FROM user WHERE user_id=${user_id}");

session_destroy();

header('Location: /');
exit();

?>