<?php
	ob_start();
	session_start();
	require_once 'db.php';
	
	//If the CEO of BMD attempts to add a user this method is used
	if(isset($_POST['addUsername']) && !empty($_POST['addUsername']) && isset($_POST['addPassword']) && !empty($_POST['addPassword']) && isset($_POST['email']) && !empty($_POST['email']) && isset($_POST['firstname']) && !empty($_POST['firstname']) && isset($_POST['lastname']) && !empty($_POST['lastname']) && isset($_POST['type']) && !empty($_POST['type']))
	{
		$username = $_POST['addUsername'];
		$password = $_POST['addPassword'];
		$email = $_POST['email'];
		$firstname = $_POST['firstname'];
		$lastname = $_POST['lastname'];
		$type = $_POST['type'];
		if($type == 'customer')
		{
			$type = 'active';
		}
		$db->addUser($username, $password, $email, $firstname, $lastname, $type);
		$message = 'Thank You for your business\r\n We are confirming that you would like to be in this.';
		$_SESSION['user']->sendConfirmationEmail($email, $message, $email, $subject);
	}
	
  	if(isset($_SESSION['user']) && !empty($_SESSION['user']))
    {
		include '../add-user.html';
  	} else {
		header('Location: ../index.php');
  	}
?>