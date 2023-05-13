<?php

require "vendor/autoload.php";
session_start();
use App\QuestionManager;

$number = null;
$question = null;

try {
	$manager = new QuestionManager;
	$manager->initialize();

	if (isset($_SESSION['is_quiz_started'])) {
		$number = $_SESSION['current_question_number'];
	} else {
		// Marker for a started quiz
		$_SESSION['is_quiz_started'] = true;
		$_SESSION['answers'] = [];
		$number = 1;
	}

	if (isset($_POST['answer'])) {
		$_SESSION['answers'][$number] = $_POST['answer'];
		$number++;
	}

	// Has user answered all items
	if ($number > $manager->getQuestionSize()) {
		header("Location: result.php");
		exit;
	}

	// Marker for question number
	$_SESSION['current_question_number'] = $number;

	$question = $manager->retrieveQuestion($number);
} catch (Exception $e) {
	echo '<h1>An error occurred:</h1>';
	echo '<p>' . $e->getMessage() . '</p>';
	exit;
}
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Quiz</title>

	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">

	<style>
		* {
			box-sizing: border-box;
			margin: 0;
			padding: 0;
			font-family: 'Inter', sans-serif;
		}

		body {
			background-color: #F5F5F5;
		}

		.maincontainer {
			display: flex;
			justify-content: center; 
			align-items: center; 
			height: 100vh;
		}

		.quesandchoices {
			background-color: #FFFFFF;
			border-radius: 8px;
			box-shadow: 0 4px 8px rgba(0, 0, 0, 0.04);
			padding: 32px;
			width: 550px;
		}

		.instructions {
			font-size: 16px;
			color: #555555;
		}

		h1 {
			margin-top: 20px;
			text-align: center;
		}

		h2 {
			font-size: 24px;
			font-weight: 500;
			margin-top: 20px;
			margin-bottom: 10px;
		}

		h3 {
			margin-top: 20px;
			margin-bottom: 10px;
		}

		form {
			margin-top: 15px;
		}

		input[type="submit"] {
			background-color: #4CAF50;
			color: #FFFFFF;
			border: none;
			border-radius: 4px;
			padding: 12px 24px;
			font-size: 16px;
			cursor: pointer;
			margin-top: 15px;
			margin-left: auto;
			display: block;
			margin-bottom: 10px;
		}

		input[type="submit"]:hover {
			background-color: #3E8E41;
		}

		label {
			font-size: 18px;
			font-weight: 500;
			color: #555555;
		}

		input[type="radio"] {
			margin-right: 10px;
			-webkit-appearance: none;
			-moz-appearance: none;
			appearance: none;
			width: 20px;
			height: 20px;
			border: 2px solid #ccc;
			border-radius: 50%;
			outline: none;
			cursor: pointer;
		}

		input[type="radio"]:checked {
			border: 2px solid #4CAF50;
		}

		.choice {
			margin-bottom: 5px;
		}

		.choice-label {
			font-size: 16px;
		}

		.choice-radio {
			display: none;
		}

		.choice-label::before {
			content: "";
			display: inline-block;
			width: 15px;
			height: 15px;
			margin-right: 15px;
			border: 2px solid #666666;
			border-radius: 50%;
			vertical-align: middle;
		}

		.choice-radio:checked + .choice-label::before {
			background-color: #666666;
		}
			
	</style>

</head>
<body>

<div class="maincontainer">
	<div class="quesandchoices">
		<h1>Analogy Questions</h1>
		<h3>Instructions</h3>
		<p class="instructions">
			There is a certain relationship between two given words on one side of : : and one word is given on another side of : : 
			while another word is to be found from the given alternatives, having the same relation with this word as the words of 
			the given pair bear. Choose the correct alternative.
		</p>

		<h1>Question #<?php echo $question->getNumber(); ?></h1>
		<h2 style="color: blue"><?php echo $question->getQuestion(); ?></h2>
		<h4 style="color: blue">Choices</h4>

		<form method="POST" action="quiz.php">
			<input type="hidden" name="number" value="<?php echo $question->getNumber();?>" />

			<?php foreach ($question->getChoices() as $choice): ?>
				<div class="choice">
					<input type="radio" name="answer" value="<?php echo $choice->letter; ?>" id="choice_<?php echo $choice->letter; ?>" class="choice-radio"/> 
					<label class="choice-label" for="choice_<?php echo $choice->letter; ?>"><?php echo $choice->letter . ') ' . $choice->label; ?></label>
				</div>
			<?php endforeach; ?>

		<input type="submit" value="Next">
		</form>
	</div>
</div>
</body>
</html>

<!-- DEBUG MODE
<pre>
<?php
// var_dump($_SESSION);
?>
</pre>
-->