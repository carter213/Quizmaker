<?php
session_start();

if (!isset($_SESSION['user_id'])) {
	header('Location: /');
	exit();

}
if(!isset($_SESSION['role']) || !$_SESSION['role'] === 'Instructor'){
	header('Location: /');
	exit();
}

// Check POST data all set classcode
if (!isset($_POST['quizname']) || !isset($_POST['timeLimit']) ||
	!isset($_POST['possiblePoints'])  || !isset($_POST['startDate']) || !isset($_POST['startTime']) 
	|| !isset($_POST['endDate']) || !isset($_POST['endTime']) || !isset($_POST['viewAnswers']) || 
	!isset($_POST['randomizeTaker']) || !isset($_POST['questionName']) || !isset($_POST['class_code']) 
	|| !isset($_POST['questionType']) || !isset($_POST['questionBody']) ) {
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
$classcode = filter_var($_POST['class_code'], FILTER_SANITIZE_STRING);
$classcode = mysqli_real_escape_string($con, $classcode);



//check questionName is array or not and their number should be equal
if(!is_array($_POST['questionName']) || empty($_POST['questionName']) ||
   !is_array($_POST['questionType']) || empty($_POST['questionType']) ||
   !is_array($_POST['questionBody']) || empty($_POST['questionBody']) ||
   count($_POST['questionType']) !== count($_POST['questionName']) ||
   count($_POST['questionType']) !== count($_POST['questionBody'])){
	header('Location: /');
	exit();
}

$question_name = $_POST['questionName']; 
$question_type = $_POST['questionType'];
$question_body = $_POST['questionBody'];


function IsDateAndTimeValid ($Idate , $Itime) {
	$tmptime = is_object(DateTime::createFromFormat('h:i:s ', $Itime));
	$tmpdate = is_object(DateTime::createFromFormat('Y-m-d', $Idate));
	$tmpdate2 = is_object(DateTime::createFromFormat('d/m/Y', $Idate));
	if($tmptime && $tmpdate && $tmpdate2 ){
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


$question_name_num = count($question_name);


//question start at 1
$count_question_num = 1;
//array start at 0
$array_num = 0;

while($questionNum > 0){
	$getQustionName = $question_name[$array_num];
	$getQustionName = filter_var($getQustionName, FILTER_SANITIZE_STRING);
	$getQustionName = mysqli_real_escape_string($con, $getQustionName);
	$getQuestionType = $question_type[$array_num];
	$getQuestionType = filter_var($getQuestionType, FILTER_SANITIZE_STRING);
	$getQuestionType = mysqli_real_escape_string($con, $getQuestionType);
	$getQuestionBody = $question_body[$array_num];
	$getQuestionBody = filter_var($getQuestionBody, FILTER_SANITIZE_STRING);
	$getQuestionBody = mysqli_real_escape_string($con, $getQuestionBody);

	switch($getQuestionType){

		case "mc":
			$getRadioValue = $_POST[strval($count_question_num) . '_numToChoose'];
			$getCheckedValue = $_POST[strval($count_question_num) . '_mc_checked'];
			$getAnsValue = $_POST[strval($count_question_num) . '_mc_ans'];
			//check vaild radio input
			
			if(!is_array($getRadioValue]) || empty($getRadioValue)){
				//skip ?
			}

			$radioValue = filter_var($getRadioValue[0], FILTER_SANITIZE_STRING);
			$radioValue = mysqli_real_escape_string($con, $radioValue);
			
			if(count($getCheckedValue) !== count($getAnsValue) || !is_array($getCheckedValue) ||
				!is_array($getAnsValue)){
				//should fail
			}elseif( empty($getCheckedValue) || empty($getAnsValue)){

			}else{
				$count_answer = count($getAnsValue);

			}




			break;

		case "tf":

			break;

		case "m":

			break;

		case "fi":

			break;

		case "sa":

			break;


		//don't know just skip this question or drop all the thing
		default:

	}
	
	

	
	
	
	
}

//need to check the $i should be equal the the questionNUm


header("Location: quizmaker?class_code=${classcode}");
exit();










?>