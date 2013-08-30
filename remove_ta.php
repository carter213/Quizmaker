<?php
session_start();

// Check session
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Instructor') {
	header('Location: /');
	exit();
}

// Check POST all set
if (!isset($_POST['ta_select']) || !isset($_POST['class_ta'])) {
	header('Location: /');
	exit();
}

$user_id = $_SESSION['user_id'];

require '../dbaccess/connect.php';

if (mysqli_connect_errno($con)) {
	header('Location: /');
	exit();
}

$ta_name = filter_var($_POST['ta_select'], FILTER_SANITIZE_STRING);
$ta_name = mysqli_real_escape_string($con, $ta_name);
$class_code = filter_var($_POST['class_ta'], FILTER_SANITIZE_STRING);
$class_code = mysqli_real_escape_string($con, $class_code);

// Validate POST
if (strlen($ta_name) == 0 || strlen($ta_name) > 40 ||
	strlen($class_code) == 0 || strlen($class_code) > 40) {
	header('Location: profmanagement');
	exit();
}

$exists = mysqli_query($con, "SELECT * FROM class NATURAL JOIN 
  class_member NATURAL JOIN user WHERE prof_id=${user_id} AND 
  role='Teaching Assistant' AND account_name='${ta_name}' AND
  class_code='${class_code}'");

if (mysqli_num_rows($exists) != 1) {
	header('Location: profmanagement');
	exit();
}

// Remove ta
$exists = mysqli_fetch_array($exists);

$class_id = $exists['class_id'];
$ta_id = $exists['user_id'];

mysqli_query($con, "DELETE FROM class_member WHERE class_id=${class_id} AND
	                user_id=${ta_id}");

header('Location: profmanagement');
exit();

?>