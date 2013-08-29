<?php
session_destroy();

session_start();

// Check POST data all set
if (!isset($_POST['signup_account']) || !isset($_POST['signup_password']) ||
	!isset($_POST['signup_confirm_password']) || !isset($_POST['signup_email']) ||
	!isset($_POST['signup_role'])) {
	header('Location: login');
}

$signup_account = filter_var($_POST['signup_account'], FILTER_SANITIZE_STRING);
$signup_account = mysqli_real_escape_string($signup_account);
$signup_password = filter_var($_POST['signup_password'], FILTER_SANITIZE_STRING);
$signup_password = mysqli_real_escape_string($signup_password);
$signup_confirm_password = filter_var($_POST['signup_confirm_password'], FILTER_SANITIZE_STRING);
$signup_confirm_password = mysqli_real_escape_string($signup_confirm_password);
$signup_email = filter_var($_POST['signup_email'], FILTER_SANITIZE_EMAIL);
$signup_email = mysqli_real_escape_string($signup_email);
$signup_role = filter_var($_POST['signup_role'], FILTER_SANITIZE_STRING);
$signup_role = mysqli_real_escape_string($signup_role);

// Validate input
if (!filter_var($signup_email, FILTER_VALIDATE_EMAIL) ||
	$signup_account === '' ||
	$signup_password === '' ||
	$signup_confirm_password === '' ||
	($signup_role !== 'Student' && $signup_role !== 'Instructor' &&
	 $signup_role !== 'Teaching Assistant') ||
	$signup_password !== $signup_confirm_password) {
	header('Location: login');
}

require '../dbaccess/connect.php';

if (mysqli_connect_errno($con)) {
	header('Location: /');
}

// Check if info exists
// CURRENTLY DOESN'T RETURN ERROR MESSAGE
$exists = mysqli_query($con, "SELECT * FROM user WHERE account_name='${signup_account}'");
if (mysqli_num_rows($exists) > 0) {
	mysqli_close($con);
	header('Location: /');
}

// Encrypt password
$signup_password = crypt($signup_password);

// Insert new user data
mysqli_query($con, "INSERT INTO user (email, account_name, password, role) VALUES 
	                ('${signup_email}', '${signup_account}', '${signup_password}', 
	                 '${signup_role}')");

// Set user_id as session id
$user_id = mysqli_query($con, "SELECT * FROM user WHERE account_name='${signup_account}'");
$user_id = mysqli_fetch_array($user_id);
$user_id = $user_id['user_id'];

$_SESSION['user_id'] = $user_id;

if ($signup_role === 'Student') {
	$next_page = 'studentmanagement';
} else if ($signup_role === 'Instructor') {
	$next_page = 'profmanagement';
} else if ($signup_role === 'Teaching Assistant') {
	$next_page = 'tamanagement';
}
header("Location: ${next_page}");

?>