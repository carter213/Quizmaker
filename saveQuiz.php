<?php
session_start();

if (!isset($_SESSION['user_id'])) {
	header('Location: /');
	exit();

}
if(!(isset($_SESSION['role'] || $_SESSION['role'] === 'Instructor')){
	header('Location: /');
	exit();
}

// Check POST data all set
if (!isset($_POST['quizname']) || !isset($_POST['timeLimit']) ||
	!isset($_POST['possiblePoints'])  || !isset($_POST['startDate']) || !isset($_POST['startTime']) 
	|| !isset($_POST['endDate']) || !isset($_POST['endTime']) || !isset($_POST['viewAnswers']) || 
	!isset($_POST['randomizeTaker']) || !isset($_POST['questionName[]'] ))
	) {
	header('Location: login');
    exit();
}

require '../dbaccess/connect.php';

if (mysqli_connect_errno($con)) {
	header('Location: /');
	exit();
}




$quizname = filter_var($_POST['quizname'], FILTER_SANITIZE_STRING);
$quizname = mysqli_real_escape_string($con, $quizname);
$timeLimit = filter_var($_POST['timeLimit'], FILTER_SANITIZE_NUMBER_INT);
$timeLimit = mysqli_real_escape_string($con, $timeLimit);
$possiblePoints = filter_var($_POST['possiblePoints'], FILTER_SANITIZE_NUMBER_INT);
$possiblePoints = mysqli_real_escape_string($con, $possiblePoints);
$start_date = filter_var($_POST['startDate'], FILTER_SANITIZE_STRING);
$start_date = mysqli_real_escape_string($con, $start_date);
$start_time = filter_var($_POST['startTime'], FILTER_SANITIZE_STRING);
$start_time = mysqli_real_escape_string($con, $start_time);
$end_date = filter_var($_POST['endDate'], FILTER_SANITIZE_STRING);
$end_date = mysqli_real_escape_string($con, $end_date);
$end_time = filter_var($_POST['endTime'], FILTER_SANITIZE_STRING);
$end_time = mysqli_real_escape_string($con, $end_time); 
$viewAnswers = filter_var($_POST['viewAnswers'], FILTER_SANITIZE_STRING);
$viewAnswers = mysqli_real_escape_string($con, $viewAnswers); 
$randomizeTaker = filter_var($_POST['randomizeTaker'], FILTER_SANITIZE_STRING);
$randomizeTaker = mysqli_real_escape_string($con, $randomizeTaker); 

function IsDateAndTimeValid ($Idate , $Itime) {
	$tmptime = is_object(DateTime::createFromFormat('h:i:s a', $Itime));
	$tmpdate = is_object(DateTime::createFromFormat('Y-m-d', $Idate));
	if($tmptime && $tmpdate ){
		return true;
	}else{
		return false;
	}  
}

//check valid date and time
if(!(IsDateAndTimeValid($start_date,$start_time) || IsDateAndTimeValid($end_date,$end_time )) || 
	empty($end_date) || empty($end_time) || empty($start_date) || empty($start_time)
 ){
	header('Location: /');
	exit();
}

//check the valid points ,quizname and  time limit
if( !is_int($timeLimit) ||  !is_int($possiblePoints) || 
	strlen($possiblePoints) > 5|| strlen($timeLimit) > 5
	  || strlen($quizname) > 40 || empty($timeLimit) || empty($possiblePoints) || empty($quizname)   ){
	header('Location: /');
	exit();
}

//check the valid reavel name & viewAnswer
if($viewAnswers !== "Never" || $viewAnswers !== "After Deadline" || $viewAnswers !== "On Turn-in" 
	 || $randomizeTaker !== "Fixed Order" || $randomizeTaker !== "Randomized Order" ){
	header('Location: /');
	exit();
}












?>