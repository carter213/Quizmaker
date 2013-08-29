<?php
session_start();

// Check session
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Instructor') {
	header('Location: /');
	exit();
}

// Check POST data all set
if (!isset($_POST['new_class'])) {
	header('Location: login');
    exit();
}

$user_id = $_SESSION['user_id'];

require '../dbaccess/connect.php';

if (mysqli_connect_errno($con)) {
	header('Location: /');
	exit();
}

$new_class = filter_var($_POST['new_class'], FILTER_SANITIZE_STRING);
$new_class = mysqli_real_escape_string($con, $new_class);

// Validate input
if ($new_class === '') {
	header('Location: profmanagement');
	exit();
}

if (strlen($new_class) > 40) {
	header('Location: profmanagement');
	exit();
}

// Check if class exists
$exists = mysqli_query($con, "SELECT * FROM class WHERE prof_id=${user_id} AND 
                              class_name='${new_class}'");
if (mysqli_num_rows($exists) > 0) {
	header('Location: profmanagement');
	exit();
}
	

// Class code generator
function randomPassword($length) {
    $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
    $pass = array();
    $alphaLength = strlen($alphabet) - 1;
    for ($i = 0; $i < $length; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
}

$code = '';
$code_made = 0;

while ($code_made == 0) {
	$code = randomPassword(40);

	$code_exists = mysqli_query($con, "SELECT * FROM class WHERE class_code='${code}'");
	if (mysqli_num_rows($code_exists) == 0) {
		$code_made = 1;
	}
}

mysqli_query($con, "INSERT INTO class (prof_id, class_name, class_code) VALUES
                    (${user_id}, '${new_class}', '${code}')");

header('Location: profmanagement');
exit();

?>