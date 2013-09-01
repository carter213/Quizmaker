<?php

if (!isset($_POST['username']) || !isset($_POST['email'])) {
	header('Location: /');
	exit();
}

require '../dbaccess/connect.php';

if (mysqli_connect_errno($con)) {
  header('Location: /');
  exit();
}

$username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
$username = mysqli_real_escape_string($con, $username);

if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
	header('Location: /');
	exit();
}

$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
$email = mysqli_real_escape_string($con, $email);

// Check if username and email combination exists
$exists = mysqli_query($con, "SELECT * FROM user WHERE account_name='${username}' 
	AND email='${email}'");
	
if (mysqli_num_rows($exists) != 1) {
  header('Location: /');
  exit();
}

// Change password

// password generator
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

$newpassword = randomPassword(10);
$encrypted_password = crypt($newpassword);

mysqli_query($con, "UPDATE user SET password='${encrypted_password}' 
	WHERE email='${email}' AND account_name='${username}'");
	
// Mail
$subject = "New password";
$message = "Your password for Quizmaker account ${username} has been changed to ${newpassword}. Please login and change the password";
$header = "From: Quizmaker@s15.level3.pint.com";

mail($email, $subject, $message, $header);

header('Location: newpassword');
exit();

?>