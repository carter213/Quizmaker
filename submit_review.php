<?php
session_start();

// Check session
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Student') {
  header('Location: /');
  exit();
}

if (!isset($_POST['class_code']) || !isset($_POST['quiz_name']) ||
    !isset($_POST['student_response'])) {
  header('Location: /');
  exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

require '../dbaccess/connect.php';

if (mysqli_connect_errno($con)) {
  header('Location: /');
  exit();
}

$class_code = filter_var($_POST['class_code'], FILTER_SANITIZE_STRING);
$class_code = mysqli_real_escape_string($con, $class_code);
$quiz_name = filter_var($_POST['quiz_name'], FILTER_SANITIZE_STRING);
$quiz_name = mysqli_real_escape_string($con, $quiz_name);

// Check if class and quiz are valid
if (strlen($class_code) != 40 || strlen($quiz_name) == 0 || 
    strlen($quiz_name) > 40) {
  header('Location: studentmanagement');
  exit();
}

// Check valid quiz
$valid_quiz = mysqli_query($con, "SELECT * FROM quiz NATURAL JOIN class 
  NATURAL JOIN student_quiz WHERE 
  class_code='${class_code}' AND quiz_name='${quiz_name}' AND 
  user_id='${user_id}' AND graded=1");

if (mysqli_num_rows($valid_quiz) == 1) {
  $valid_quiz = mysqli_fetch_array($valid_quiz);
  $quiz_id = $valid_quiz['quiz_id'];
} else {
  header("Location: studentmanagement");
  exit();
}

// Check responses
$questions = mysqli_query($con, "SELECT * FROM question WHERE quiz_id=${quiz_id}
	ORDER BY question_num");

$student_response = $_POST['student_response'];

if (mysqli_num_rows($questions) != count($student_response)) {
  header("Location: review?class_code=${class_code}&quiz_name=${quiz_name}");
  exit();
}

// Store in database
for ($x = 0; $x < count($student_response); $x++) {
	$response = filter_var($student_response[$x], FILTER_SANITIZE_STRING);
	$response = mysqli_real_escape_string($con, $response);

	mysqli_query($con, "UPDATE student_question SET student_response='${response}' 
	    WHERE user_id=${user_id} AND 
		quiz_id=${quiz_id} AND question_num=${x}");
}

header("Location: review?class_code=${class_code}&quiz_name=${quiz_name}");
exit();

?>