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
  require '../dbaccess/connect.php';
  return mysqli_real_escape_string($con, $a);
}


mysqli_query($con, "INSERT INTO student_quiz (user_id, quiz_id, finished) VALUES ('${user_id}', '${quiz_id}', '0')");

//validate all info
$Q_Num = filter_var_array($_POST['Q_Num'], FILTER_SANITIZE_NUMBER_INT);
$Q_Num = array_map('array_map_callback', $Q_Num);
$Q_Type = filter_var_array($_POST['Q_Type'], FILTER_SANITIZE_STRING);
$Q_Type = array_map('array_map_callback', $Q_Type);

$total = count($Q_Num);
for ($i=1; $i<=$total; $i++) {
  $question_num = $Q_Num[$i-1];
  $temp_i = $_POST["${question_num}"];
  
  mysqli_query($con, "INSERT INTO student_question (user_id, quiz_id, question_num, student_answer) VALUES ('${user_id}', '${quiz_id}', '${question_num}','NULL')");

  switch ($Q_Type[$i-1]) {
    
    case 'mc':
      $answer = filter_var_array($temp_i, FILTER_SANITIZE_STRING);
      $answer = array_map('array_map_callback', $answer);
      for ($j=0; $j<count($answer); $j++) {
        $ans = $answer[$j];
        mysqli_query($con, "INSERT INTO student_mc (user_id, quiz_id, question_num, option_num) VALUES ('${user_id}', '${quiz_id}', '${question_num}', '${ans}')");      
      }
      break;
    case 'm':
      $answer = filter_var_array($temp_i, FILTER_SANITIZE_STRING);
      $answer = array_map('array_map_callback', $answer);
      //var_dump($answer);      
      for ($j=0; $j<count($answer); $j++) {
        $ans = $answer[$j];
        mysqli_query($con, "INSERT INTO student_matching (user_id, quiz_id, question_num, option_num, answer) VALUES ('${user_id}', '${quiz_id}', '${question_num}', '${j}', '${ans}')");      
      }
      break;
    //for T/F, Fill-in and Short Answer
    default:
      $answer = filter_var($temp_i, FILTER_SANITIZE_STRING);
      $answer = mysqli_real_escape_string($con, $answer);
      //var_dump($answer);
      mysqli_query($con, "UPDATE student_question SET student_answer='${answer}' WHERE user_id = ${user_id} AND quiz_id = ${quiz_id} AND question_num = ${question_num}");
      break;
  }

}

$status = filter_var($_POST['status'], FILTER_SANITIZE_STRING);
$status = mysqli_real_escape_string($con, $status);

if ($status=='Submit') {
  mysqli_query($con, "UPDATE student_quiz SET finished='1' WHERE user_id = ${user_id} AND quiz_id = ${quiz_id};");
  header("Location: studentmanagement");
  exit();
}

exit();  

?>
