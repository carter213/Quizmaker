<?php
session_start();

// Check user_id
if (!isset($_SESSION['user_id'])) {
	header('Location: /');
	exit();
}

$user_id = $_SESSION['user_id'];

// Check POST data all set
if (!isset($_POST['username']) || !isset($_POST['email']) || 
    !isset($_POST['password']) || !isset($_POST['confirm_password'])) {
    header('Location: /');
    exit();
}

require '../dbaccess/connect.php';

if (mysqli_connect_errno($con)) {
	header('Location: /');
	exit();
}

// Get user role and management page
$account = mysqli_query($con, "SELECT role FROM user WHERE user_id=${user_id}");
$account = mysqli_fetch_array($account);

switch ($account['role']) {
	case 'Student':
		$prev_page = 'studentmanagement';
		break;
	case 'Instructor':
		$prev_page = 'profmanagement';
		break;
	case 'Teaching Assistant':
		$prev_page = 'tamanagement';
		break;
}

// Sanitize input
$username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
$username = mysqli_real_escape_string($con, $username);
$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
$email = mysqli_real_escape_string($con, $email);
$password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);
$password = mysqli_real_escape_string($con, $password);
$confirm_password = filter_var($_POST['confirm_password'], FILTER_SANITIZE_STRING);
$confirm_password = mysqli_real_escape_string($con, $confirm_password);


// Validate input
if ($password !== $confirm_password) {
	header("Location: ${prev_page}");
	exit();
}
if ($username === '' && $email === '' && 
    $password === '' && $confirm_password === '') {
	header("Location: ${prev_page}");
	exit();	
}

if ($username === '') {
	$username = $account['account_name'];
}
if ($email === '') {
	$email = $account['email'];
}
if ($password === '') {
	$password = $account['password'];
} else {
	$password = crypt($password);
}

// Update user info
mysqli_query($con, "UPDATE user SET account_name='${username}', 
                    email='${email}', password='${password}' WHERE
                    user_id=${user_id}");

header("Location: ${prev_page}");
exit();

?>