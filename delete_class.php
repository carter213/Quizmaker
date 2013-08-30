<?php
session_start();

// Check session
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Instructor') {
	header('Location: /');
	exit();
}

// Check POST all set
if (!isset($_POST['class_select'])) {
	header('Location: /');
	exit();
}

$user_id = $_SESSION['user_id'];

require '../dbaccess/connect.php';

if (mysqli_connect_errno($con)) {
	header('Location: /');
	exit();
}

$class_code = filter_var($_POST['class_select'], FILTER_SANITIZE_STRING);
$class_code = mysqli_real_escape_string($con, $class_code);

if (strlen($class_code) != 40) {
	header('Location: profmanagement');
	exit();
}

// Confirm class belongs to user
$exists = mysqli_query($con, "SELECT * FROM class WHERE prof_id=${user_id} AND
                              class_code='${class_code}'");

if (mysqli_num_rows($exists) != 1) {
	header('Location: profmanagement');
	exit();
}

// Delete class
mysqli_query($con, "DELETE FROM class WHERE prof_id=${user_id} AND 
                    class_code='${class_code}'");

header('Location: profmanagement');
exit();

?>