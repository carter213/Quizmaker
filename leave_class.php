<?php
session_start();

// Check session
if (!isset($_SESSION['user_id']) || 
	 ($_SESSION['role'] !== 'Student' && 
	  $_SESSION['role'] !== 'Teaching Assistant')) {
	header('Location: /');
	exit();
}

// Check POST all set
if (!isset($_POST['class_select'])) {
	header('Location: /');
	exit();
}

$user_id = $_SESSION['user_id'];
if ($_SESSION['role'] === 'Student') {
	$prev_page = 'studentmanagement';
} else {
	$prev_page = 'tamanagement';
}

require '../dbaccess/connect.php';

if (mysqli_connect_errno($con)) {
	header('Location: /');
	exit();
}

$class_code = filter_var($_POST['class_select'], FILTER_SANITIZE_STRING);
$class_code = mysqli_real_escape_string($con, $class_code);

// Validate POST
if (strlen($class_code) == 0 || strlen($class_code) > 40) {
	header("Location: ${prev_page}");
	exit();
}

$exists = mysqli_query($con, "SELECT * FROM class NATURAL JOIN class_member
  WHERE user_id=${user_id} AND class_code='${class_code}'");

if (mysqli_num_rows($exists) != 1) {
	header("Location: ${prev_page}");
	exit();
}

// Leave class
$exists = mysqli_fetch_array($exists);

$class_id = $exists['class_id'];

mysqli_query($con, "DELETE FROM class_member WHERE class_id=${class_id} AND
	                user_id=${user_id}");

header("Location: ${prev_page}");
exit();

?>