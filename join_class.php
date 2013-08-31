<?php
session_start();

// Check session
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Student') {
	header('Location: /');
	exit();
}

// Check POST all set
if (!isset($_POST['join_class']) || !isset($_POST['join_class_code'])) {
	header('Location: /');
	exit();
}

$user_id = $_SESSION['user_id'];

require '../dbaccess/connect.php';

if (mysqli_connect_errno($con)) {
	header('Location: /');
	exit();
}

$class_name = filter_var($_POST['join_class'], FILTER_SANITIZE_STRING);
$class_name = mysqli_real_escape_string($con, $class_name);
$class_code = filter_var($_POST['join_class_code'], FILTER_SANITIZE_STRING);
$class_code = mysqli_real_escape_string($con, $class_code);

// Validate POST
if (strlen($class_name) == 0 || strlen($class_name) > 40 ||
	strlen($class_code) == 0 || strlen($class_code) > 40) {
	header("Location: studentmanagement");
	exit();
}

$exists = mysqli_query($con, "SELECT * FROM class WHERE 
	class_code='${class_code}' AND class_name='${class_name}'");

if (mysqli_num_rows($exists) != 1) {
	header("Location: studentmanagement");
	exit();
}

$exists = mysqli_fetch_array($exists);
$class_id = $exists['class_id'];

$not_exists = mysqli_query($con, "SELECT * FROM class_member WHERE 
	user_id=${user_id} AND class_id=${class_id}");

if (mysqli_num_rows($not_exists) != 0) {
	header("Location: studentmanagement");
	exit();
}

// Join class
mysqli_query($con, "INSERT INTO class_member VALUES (${class_id}, ${user_id})");

header("Location: studentmanagement");
exit();


?>