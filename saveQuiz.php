<?php
session_start();

if (!isset($_SESSION['user_id'])) {
	header('Location: /');
	exit();

}
if(!isset($_SESSION['role']) || !$_SESSION['role'] === 'Instructor' || !isset($_POST['class_code']) ){
	header('Location: /');
	exit();
}

// Check POST data all set classcode
if (!isset($_POST['quizName']) || !isset($_POST['timeLimit']) ||
	!isset($_POST['possiblePoints'])  || !isset($_POST['startDate']) || !isset($_POST['startTime']) 
	|| !isset($_POST['endDate']) || !isset($_POST['endTime']) || !isset($_POST['viewAnswers']) || 
	!isset($_POST['randomizeTaker']) 
	 ) {
	header('Location: /');
    exit();
}

require '../dbaccess/connect.php';

if (mysqli_connect_errno($con)) {
	header('Location: /');
	exit();
}




$quizName = filter_var($_POST['quizName'], FILTER_SANITIZE_STRING);
$quizName = mysqli_real_escape_string($con, $quizName);
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


$getUserId = $_SESSION['user_id'];


function IsDateAndTimeValid ($Idate , $Itime) {
	//$pattern1 = '/^(0?\d|1\d|2[0-3]):[0-5]\d:[0-5]\d$/';
    //$pattern2 = '/^(0?\d|1[0-2]):[0-5]\d\s(am|pm)$/i';
    //$timevaild = preg_match($p1, $Itime) || preg_match($p2, $Itime);
    //$timevaild = date('H:i:s', strtotime($timevaild));
    date_default_timezone_set('America/Los_Angeles');
    $tmpTime = is_object(DateTime::createFromFormat('H:i:s' , $Itime));
	$tmpdate1 = is_object(DateTime::createFromFormat('Y-m-d', $Idate));
	$tmpdate2 = is_object(DateTime::createFromFormat('d/m/Y', $Idate));
	$tmpdate = tmpdate1 || tmpdate2;
	
	if($tmpTime &&  $tmpdate ){
		return true;
	}else{
		return false;
	}  
}

//check valid date and time
if(!IsDateAndTimeValid($start_date,$start_time) || !IsDateAndTimeValid($end_date,$end_time ) || 
	empty($end_date) || empty($end_time) || empty($start_date) || empty($start_time)
 ){
	header('Location: /');
    exit();
}

$getStartDateAndTime = $start_date . ' ' . $start_time;
$getEndDateAndTime = $end_date . ' ' . $end_time;

//check the valid points ,quizName and  time limit
if( !ctype_digit($timeLimit) ||  !ctype_digit($possiblePoints) || 
	strlen($possiblePoints) > 5|| strlen($timeLimit) > 5
	  || strlen($quizName) > 40 || empty($timeLimit) || empty($possiblePoints) || empty($quizName)   ){
	
	var_dump(!ctype_digit($timeLimit));var_dump(!ctype_digit($possiblePoints));var_dump(strlen($possiblePoints) > 5);
	var_dump(strlen($timeLimit) > 5);var_dump( strlen($quizName) > 40);var_dump(empty($timeLimit));
	var_dump(empty($possiblePoints));var_dump(empty($quizName)); exit();
	
	header('Location: /');
	exit();
}

//check the valid reavel name & viewAnswers
$flagviewAnswers = !($viewAnswers !== "Never" || $viewAnswers !== "After Deadline" || $viewAnswers !== "On Turn-in");
$flagrandomizeTaker = !($randomizeTaker !== "Fixed Order" || $randomizeTaker !== "Randomized Order" );
if($flagviewAnswers || $flagrandomizeTaker  ){
	header('Location: /');
	exit();
}


$classid = mysqli_query($con, "SELECT class_id FROM class WHERE class_code='${classcode}'");
$classid = mysqli_fetch_array($classid)[0];

$existQuiz = mysqli_query($con, "SELECT quiz_id,quiz_name FROM quiz WHERE class_id='${classid}' AND quiz_name = '${quizName}' ");
if(mysqli_num_rows($existQuiz)){
	$existQuiz = mysqli_fetch_array($existQuiz);
	$oldQuizId = $existQuiz[0];
	$oldQuizName = $exitQuiz[1];
	mysqli_query($con, "DELETE FROM quiz WHERE class_id='${classid}' AND quiz_name = '${oldQuizName}' ");
	mysqli_query($con, "DELETE FROM question WHERE quiz_id = '${oldQuizId}'");
}





//need to check the $i should be equal the the questionNUm
mysqli_query($con, "INSERT INTO quiz (prof_id, quiz_name, possible_points, class_id, time_limit,reveal_answers,
					 open_date,deadline, display_order ) VALUES 
	                ('${getUserId}', '${quizName}', '${possiblePoints}',  '${classid}','${timeLimit}', 
	                 '${viewAnswers}' , '${getStartDateAndTime}' , '${getEndDateAndTime}' , '${randomizeTaker}' )");

$getQuizId = mysqli_query($con, "SELECT quiz_id FROM quiz  WHERE class_id='${classid}' AND quiz_name = '${quizName}'");
$getQuizId = mysqli_fetch_array($getQuizId)[0];


$question_name_arr = $_POST['questionName']; 
$question_type_arr = $_POST['questionType'];
$question_body_arr = $_POST['questionBody'];
$question_point_arr = $_POST['points'];
$question_id_arr = $_POST['questionID'];

$question_name_num = count($question_name_arr);


//question start at 1
//array start at 0
if(!is_array($_POST['questionName']) || empty($_POST['questionName']) ||
  	   !is_array($_POST['questionType']) || empty($_POST['questionType']) ||
  	   !is_array($_POST['questionBody']) || empty($_POST['questionBody']) ||
       !is_array($_POST['points']) || empty($_POST['points']) ||
   		count($_POST['questionType']) !== count($_POST['questionName']) ||
   		count($_POST['questionType']) !== count($_POST['questionBody']) ||
   		count($_POST['questionType']) !== count($_POST['points']) ||
   		count($_POST['questionType']) !== count($_POST['questionID']) ){
		
		var_dump(!is_array($_POST['questionName'])); 	var_dump(empty($_POST['questionName'])); 
		var_dump(!is_array($_POST['questionType'])); 	var_dump(empty($_POST['questionType'])); 
		var_dump(!is_array($_POST['questionBody'])); 	var_dump(empty($_POST['questionBody'])); 
		var_dump(count($_POST['questionType']) !== count($_POST['questionName'])); 
		var_dump(count($_POST['questionType']) !== count($_POST['questionBody'])); 
		var_dump(count($_POST['questionType']) !== count($_POST['points'])); 
		var_dump(count($_POST['questionType']) !== count($_POST['questionID'])); exit();

		header('Location: login');
		exit();
	}

for($array_num = 0; $array_num < $question_name_num; $array_num++){
	
	$getQustionName = $question_name_arr[$array_num];
	$getQustionName = filter_var($getQustionName, FILTER_SANITIZE_STRING);
	$getQustionName = mysqli_real_escape_string($con, $getQustionName);
	$getQuestionType = $question_type_arr[$array_num];
	$getQuestionType = filter_var($getQuestionType, FILTER_SANITIZE_STRING);
	$getQuestionType = mysqli_real_escape_string($con, $getQuestionType);
	$getQuestionBody = $question_body_arr[$array_num];
	$getQuestionBody = filter_var($getQuestionBody, FILTER_SANITIZE_STRING);
	$getQuestionBody = mysqli_real_escape_string($con, $getQuestionBody);
	$getQuestionPoint = $question_point_arr[$array_num];
	$getQuestionPoint = filter_var($getQuestionPoint, FILTER_SANITIZE_NUMBER_INT);
	$getQuestionPoint = mysqli_real_escape_string($con, $getQuestionPoint);
	$count_question_num = $question_id_arr[$array_num];
	$count_question_num = filter_var($count_question_num, FILTER_SANITIZE_NUMBER_INT);
	$count_question_num = mysqli_real_escape_string($con, $count_question_num);


	if(!ctype_digit($getQuestionPoint)){
		header('Location: /');
		exit();
	}
	$getQuestionPoint = ctype_digit($getQuestionPoint);




	switch($getQuestionType){

		case "mc":
			$radio_value_arr = $_POST[strval($count_question_num) . '_numToChoose'];
			//check vaild radio input
			
			if(!is_array($radio_value_arr) || empty($radio_value_arr)){
				//skip ?
			}

			$getRadioValue = filter_var($radio_value_arr[0], FILTER_SANITIZE_STRING);
			$getRadioValue = mysqli_real_escape_string($con, $getRadioValue);

			$checked_value_arr = $_POST[strval($count_question_num) . '_mc_checked'];
			$ans_value_arr = $_POST[strval($count_question_num) . '_mc_ans'];
			$getCheckedValue;
			$getAnsValue;
			mysqli_query($con, "INSERT INTO question (quiz_id, type, label, question_num , body, points) VALUES 
	                ('${getQuizId}', '${getQuestionType}', '${getQustionName}',  '${count_question_num}',
	                '${getQuestionBody}', '${getQuestionPoint}'
	                )");
			if(count($checked_value_arr) !== count($ans_value_arr) || !is_array($checked_value_arr) ||
				!is_array($ans_value_arr)){
				//should fail
			}elseif( empty($checked_value_arr) || empty($ans_value_arr)){

				mysqli_query($con, "INSERT INTO mc (quiz_id, question_num, option_num) VALUES 
	                ('${getQuizId}', '${count_question_num}', '${x}'
		   			)");

			}else{ 

				//$count_check = count($checked_value_arr);

				//var_dump($checked_value_arr);


				$count_answer = count($ans_value_arr);
					
				for($x = 0; $x < $count_answer ; $x++){
					
					$checkPos = $x ;
					$getAnsValue = filter_var($ans_value_arr[$x], FILTER_SANITIZE_STRING);
					$getAnsValue = mysqli_real_escape_string($con, $getAnsValue);

					if(!is_bool(array_search($checkPos, $checked_value_arr)) )
					{
						$getCheckedValue = 1;
					}else{
						$getCheckedValue = 0;
					}

					mysqli_query($con, "INSERT INTO mc (quiz_id, question_num, option_num,option_val,is_correct) VALUES 
	                ('${getQuizId}', '${count_question_num}', '${x}', '${getAnsValue}', '${getCheckedValue}'
		   			)");
				}


			}
			//mysql save getQustionName,getQuestionType,getQuestionBody ,getQuestionPoint

			


			break;

		case "tf":
				//var_dump(strval($count_question_num) . '_tf');
				$radio_value_arr = $_POST[strval($count_question_num) . '_tf'];

				//var_dump($radio_value_arr);
				if(!is_array($radio_value_arr) || empty($radio_value_arr)){
					//skip ?
				}
			
				
				$getTFAnswer = filter_var($radio_value_arr[0], FILTER_SANITIZE_STRING);
				$getTFAnswer = mysqli_real_escape_string($con, $getTFAnswer);
				
				//var_dump($getTFAnswer);
				
				mysqli_query($con, "INSERT INTO question (quiz_id, type, label, question_num , body, answer, points) VALUES 
	                ('${getQuizId}', '${getQuestionType}', '${getQustionName}',  '${count_question_num}',
	                '${getQuestionBody}','${getTFAnswer}', '${getQuestionPoint}'
	                )");
				//mysql save getQustionName,getQuestionType,getQuestionBody ,getQuestionPoint,getRadioValue
			break;

		case "m":
			//missing body?
			$m_word_arr = $_POST[strval($count_question_num) . '_m_word'];
			$m_value_arr = $_POST[strval($count_question_num) . '_m_value'];
			$getMWord;
			$getMValue;
			mysqli_query($con, "INSERT INTO question (quiz_id, type, label, question_num , points) VALUES 
	                ('${getQuizId}', '${getQuestionType}', '${getQustionName}',  '${count_question_num}',
	                  '${getQuestionPoint}'
	                )");
			if(count($m_word_arr) !== count($m_value_arr) || !is_array($m_word_arr) ||
				!is_array($m_value_arr)){
				//should fail
			}elseif( empty($m_word_arr) || empty($m_value_arr)){

			}else{
				$count_answer = count($m_value_arr);
				for($x = 0; $x < $count_answer ; $x++){
					$getMWord = filter_var($m_word_arr[$x], FILTER_SANITIZE_STRING);
					$getMWord = mysqli_real_escape_string($con, $getMWord);
					$getMValue = filter_var($m_value_arr[$x], FILTER_SANITIZE_STRING);
					$getMValue = mysqli_real_escape_string($con, $getMValue);

					mysqli_query($con, "INSERT INTO matching (quiz_id, question_num, option_num, word, value) VALUES 
	                ('${getQuizId}', '${count_question_num}', '${x}',  '${getMWord}',' ${getMValue}'
	                )");
					
					//store to the mysql
					//save getMValue, getMValue
				}
			}

				//mysql save getQustionName,getQuestionType ,getQuestionPoint
				
			break;

		case "fi":


				mysqli_query($con, "INSERT INTO question (quiz_id, type, label, question_num , body, points) VALUES 
	                ('${getQuizId}', '${getQuestionType}', '${getQustionName}',  '${count_question_num}',
	                '${getQuestionBody}', '${getQuestionPoint}'
	                )");
			$ans_value_arr = $_POST[strval($count_question_num) . '_fi'];
			//var_dump($ans_value_arr);
			if(!is_array($ans_value_arr)){
				//should fail
			}elseif(empty($ans_value_arr)){

			}else{
				$count_answer = count($ans_value_arr);
				//var_dump($count_answer);
				for($x = 0; $x < $count_answer ; $x++){
					$getAnsValue = filter_var($ans_value_arr[$x], FILTER_SANITIZE_STRING);
					$getAnsValue = mysqli_real_escape_string($con, $getAnsValue);
					//var_dump($getAnsValue);
					mysqli_query($con, "INSERT INTO fill_in (quiz_id, question_num, option_num, answer) VALUES 
	                ('${getQuizId}', '${count_question_num}', '${x}', '${getAnsValue}'
	                )");
	                //printf("error %s" , mysqli_error($con));
					//store to the mysql
					//save getAnsValue, getCheckedValue
				}
			}
			
			//mysql save getQustionName,getQuestionType,getQuestionBody ,getQuestionPoint
		

			break;
		case "sa":
			mysqli_query($con, "INSERT INTO question (quiz_id, type, label, question_num , body, points) VALUES 
	                ('${getQuizId}', '${getQuestionType}', '${getQustionName}',  '${count_question_num}',
	                '${getQuestionBody}', '${getQuestionPoint}'
	                )");

			break;
		//don't know just skip this question or drop all the thing
		default:

	}

	
}

//class code & quiz_name unquie


header("Location: quizmaker?class_code=${classcode}");
exit();










?>
