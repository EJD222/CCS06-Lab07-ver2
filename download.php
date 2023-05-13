<?php
require "vendor/autoload.php";
session_start();
use App\QuestionManager;

$manager = new QuestionManager;
$manager->initialize();

$score = $manager->computeScore($_SESSION['answers']);
$questionSize = $manager->getQuestionSize();

// Create, Open, and Write on Results.txt
$results_file = fopen('results.txt', 'w');

if (!$results_file) {
	echo 'An error occurred while creating the file.';
	exit;
}

// Write the user's information and score to the file
fwrite($results_file, "Complete Name: {$_SESSION['user_fullname']}\n");
fwrite($results_file, "Email: {$_SESSION['user_email']}\n");
fwrite($results_file, "Birthdate: {$_SESSION['user_birthdate']}\n");
fwrite($results_file, "Score: {$score} out of {$questionSize}\n");

// Write the user's answers to the file
fwrite($results_file, "Answers:\n");
foreach ($_SESSION['answers'] as $index => $answer) {
	$question = $manager->retrieveQuestion($index);
	if ($question !== null) {
		$is_correct = ($answer === $question->getAnswer()) ? 'correct' : 'incorrect';
		fwrite($results_file, ($index) . ". ". $answer . " (" . $is_correct . ")\n");
	}
}

// Close the results file and send it as a downloadabe file
fclose($results_file);
header('Content-Type: application/octet-stream');
header("Content-Disposition: attachment; filename=results.txt");
readfile('results.txt');
exit;
?>