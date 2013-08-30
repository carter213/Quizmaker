<?php
session_start();

// Check session
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Instructor') {
	header('Location: /');
	exit();
}

// Check POST all set
if (!isset($_POST['new_ta']) || !isset($_POST['class_code'])) {
	header('Location: /');
	exit();
}

$user_id = $_SESSION['user_id'];

require '../dbaccess/connect.php';

if (mysqli_connect_errno($con)) {
	header('Location: /');
	exit();
}

$ta_name = filter_var($_POST['new_ta'], FILTER_SANITIZE_STRING);
$ta_name = mysqli_real_escape_string($con, $ta_name);
$class_code = filter_var($_POST['class_code'], FILTER_SANITIZE_STRING);
$class_code = mysqli_real_escape_string($con, $class_code);

// Validate POST
if (strlen($ta_name) > 40 || strlen($ta_name) == 0 ||
    strlen($class_code) != 40) {
	header('Location: profmanagement');
	exit();
}

$ta_exists = mysqli_query($con, "SELECT * FROM user WHERE 
					role='Teaching Assistant' AND account_name='${ta_name}'");

if (mysqli_num_rows($ta_exists) != 1) {
	header('Location: profmanagement');
	exit();
}

$class_exists = mysqli_query($con, "SELECT * FROM class WHERE 
                           prof_id=${user_id} AND class_code='${class_code}'");

if (mysqli_num_rows($class_exists) != 1) {
	header('Location: profmanagement');
	exit();
}

$ta = mysqli_fetch_array($ta_exists);
$class = mysqli_fetch_array($class_exists);

$ta_id = $ta['user_id'];
$class_id = $class['class_id'];

// Assign ta
mysqli_query($con, "INSERT INTO class_member VALUES (${class_id}, ${ta_id})");

header('Location: profmanagement');
exit();

?>