<?php
session_start();

if (!isset($_SESSION['user_id'])) {
  header('Location: /');
  exit();
}
if(!isset($_SESSION['role']) || !$_SESSION['role'] === 'Student' ){
  header('Location: /');
  exit();
}

if (!isset($_POST['quiz_id']) || !isset($_POST['class_id'])) {
  header('Location: /');
    exit();
}

require '../dbaccess/connect.php';

if (mysqli_connect_errno($con)) {
  header('Location: /');
  exit();
}

$user_id = $_SESSION['user_id'];

$quiz_id = filter_var($_POST['quiz_id'], FILTER_SANITIZE_STRING);
$quiz_id = mysqli_real_escape_string($con, $quiz_id);
$class_id = filter_var($_POST['class_id'], FILTER_SANITIZE_STRING);
$class_id = mysqli_real_escape_string($con, $class_id);

$bool = False;
$members=mysqli_query($con, "SELECT user_id FROM class_member WHERE class_id = '${class_id}'");
while($member=mysqli_fetch_array($members)) {
    if($user_id == $member['user_id']) {
        $bool = True;
        break;
    }
}
if ($bool == False) {
    header('Location: studentmanagement');
    exit();
}

function array_map_callback($a) {
  return mysqli_real_escape_string($con, $a);
}

//validate all info
$Q_Num = filter_var_array($_POST['Q_Num'], FILTER_SANITIZE_NUMBER_INT);
$Q_Num = array_map('array_map_callback', $Q_Num);
$Q_Type = filter_var_array($_POST['Q_Type'], FILTER_SANITIZE_STRING);
$Q_Type = array_map('array_map_callback', $Q_Type);


$total = count($Q_Num);
for ($i=0; $i<$total; $i++) {
  $question_num = $Q_Num[$i];
  
  switch ($Q_Type[$i]) {
    case 'mc':
      $answer = filter_var_array($_POST['$i'], FILTER_SANITIZE_STRING);
      $answer = array_map('mysqli_real_escape_string', $answer);
      var_dump(implode($answer));
      mysqli_query($con, "UPDATE student_mc SET option_num='${answer}' WHERE user_id = ${user_id} AND quiz_id = ${quiz_id} AND question_num = ${question_num}");
      break;
    case 'm':
      $answer = filter_var_array($_POST['$i'], FILTER_SANITIZE_STRING);
      $answer = array_map('mysqli_real_escape_string', $answer);
      var_dump($answer);      
      for ($j=1; $j<=count($answer); $j++) {
        mysqli_query($con, "UPDATE student_matching SET answer='${answer}[$j]' WHERE user_id = ${user_id} AND quiz_id = ${quiz_id} AND question_num = ${question_num} AND option_num = ${j}");        
      }
      break;
    //for T/F, Fill-in and Short Answer
    default:
      $answer = filter_var($_POST['$i'], FILTER_SANITIZE_STRING);
      $answer = mysqli_real_escape_string($con, $answer);
      var_dump($answer);
      mysqli_query($con, "UPDATE student_question SET answer='${answer}' WHERE user_id = ${user_id} AND quiz_id = ${quiz_id} AND question_num = ${question_num}");
      break;
  }

}

$status = filter_var($_POST['status'], FILTER_SANITIZE_STRING);
$status = mysqli_real_escape_string($con, $status);

if ($status=='Submit') {
  mysqli_query($con, "UPDATE student_quiz SET finished='1' WHERE user_id = ${user_id} AND quiz_id = ${quiz_id}");
  header("Location: studentmanagement");
  exit();
}

exit();  

?>