<?php

session_start();

if (isset($_SESSION['user_id'])) {
	unset($_SESSION['user_id']);
}

// Check POST data all set
if (!isset($_POST['login_account']) || !isset($_POST['login_password'])) {
	header('Location: login');
    exit();
}

require '../dbaccess/connect.php';

if (mysqli_connect_errno($con)) {
	header('Location: /');
	exit();
}

// Sanitize input
$login_account = filter_var($_POST['login_account'], FILTER_SANITIZE_STRING);
$login_account = mysqli_real_escape_string($con, $login_account);
$login_password = filter_var($_POST['login_password'], FILTER_SANITIZE_STRING);
$login_password = mysqli_real_escape_string($con, $login_password);

// Attempt login
$account = mysqli_query($con, "SELECT * FROM user WHERE account_name='${login_account}'");

if (mysqli_num_rows($account) != 1) {
	mysqli_close($con);
	header('Location: login');
	exit();
}

$account = mysqli_fetch_array($account);

if (crypt($login_password, $account['password']) == $account['password']) {
	$_SESSION['user_id'] = $account['user_id'];
    $_SESSION['role'] = $account['role'];

    switch ($account['role']) {
    	case 'Student':
    		header('Location: studentmanagement');
    		exit();
    		break;
    	case 'Instructor':
    		header('Location: profmanagement');
    		exit();
    		break;
    	case 'Teaching Assistant':
    		header('Location: tamanagement');
    		exit();
    		break;
    }

} else {
	header('Location: login');
	exit();
}