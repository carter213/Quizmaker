<?php
session_start();

// Check session
if (!isset($_SESSION['user_id']) || 
    ($_SESSION['role'] !== 'Instructor' && 
	 $_SESSION['role'] !== 'Teaching Assistant')) {
  header('Location: /');
  exit();
}

if (!isset($_POST['class_code']) || !isset($_POST['quiz_name']) ||
	!isset($_POST['loadStudentQuiz']) || !isset($_POST['points']) ||
	!isset($_POST['ta_comment']) || !isset($_POST['submit'])) {
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
$student_name = filter_var($_POST['loadStudentQuiz'], FILTER_SANITIZE_STRING);
$student_name = mysqli_real_escape_string($con, $student_name);

// Check if class and quiz are valid
if (strlen($class_code) != 40 || strlen($quiz_name) == 0 || strlen($quiz_name) > 40 ||
	strlen($student_name) == 0 || strlen($student_name) > 40) {
  if ($role == 'Instructor') {
    header('Location: profmanagement');
    exit();
  } else {
    header('Location: tamanagement');
	exit();
  }
}

if ($role == 'Instructor') {
  $valid_class = mysqli_query($con, "SELECT * FROM class NATURAL JOIN quiz WHERE 
    prof_id=${user_id} AND class_code='${class_code}' AND quiz_name='${quiz_name}'");

  if (mysqli_num_rows($valid_class) != 1) {
    header('Location: profmanagement');
    exit();
  }
} else {
  $valid_class = mysqli_query($con, "SELECT * FROM class_member NATURAL JOIN
    class NATURAL JOIN quiz WHERE user_id=${user_id} AND 
    class_code='${class_code}' AND quiz_name='${quiz_name}'");
	
  if (mysqli_num_rows($valid_class) != 1) {
    header('Location: tamanagement');
    exit();
  }
}

// Check valid student
$valid_quiz = mysqli_query($con, "SELECT * FROM quiz NATURAL JOIN class 
  NATURAL JOIN student_quiz NATURAL JOIN user WHERE 
  class_code='${class_code}' AND quiz_name='${quiz_name}' AND 
  account_name='${student_name}'");

if (mysqli_num_rows($valid_quiz) == 1) {
  $valid_quiz = mysqli_fetch_array($valid_quiz);
  $quiz_id = $valid_quiz['quiz_id'];
  $student_id = $valid_quiz['user_id'];
} else {
  header("Location: grading?class_code=${class_code}&quiz_name=${quiz_name}");
  exit();
}

// Check points and comments
$questions = mysqli_query($con, "SELECT * FROM question WHERE quiz_id=${quiz_id}
	ORDER BY question_num");

$ta_comment = $_POST['ta_comment'];
$points = $_POST['points'];

if (mysqli_num_rows($questions) != count($ta_comment) ||
	mysqli_num_rows($questions) != count($points)) {
  header("Location: grading?class_code=${class_code}&quiz_name=${quiz_name}");
  exit();
}

$x = 0;
while ($question = mysqli_fetch_array($questions)) {
	$max_points = $question['points'];

	$submit_points = filter_var($points[$x], FILTER_SANITIZE_NUMBER_INT);

	if ($submit_points > $max_points) {
	  header("Location: grading?class_code=${class_code}&quiz_name=${quiz_name}");
      exit();
	}

	$x++;
}

// Store in database
$final_points = 0;
for ($x = 0; $x < count($ta_comment); $x++) {
	$pt = filter_var($points[$x], FILTER_SANITIZE_INT);
	$comment = filter_var($ta_comment[$x], FILTER_SANITIZE_STRING);
	$comment = mysqli_real_escape_string($comment);

	mysqli_query($con, "UPDATE student_question SET student_points=${pt}, 
		ta_comment='${comment}' WHERE user_id=${student_id} AND 
		quiz_id=${quiz_id} AND question_num=${x}");

    $final_points += $pt;
}

if ($_POST['submit'] == 1) {
  mysqli_query($con, "UPDATE student_quiz SET earned_points=${final_points}, graded=1 WHERE 
   user_id=${student_id} AND quiz_id=${quiz_id}");
} else {
  mysqli_query($con, "UPDATE student_quiz SET earned_points=${final_points} WHERE 
   user_id=${student_id} AND quiz_id=${quiz_id}");
}

header("Location: grading?class_code=${class_code}&quiz_name=${quiz_name}");
exit();

?>