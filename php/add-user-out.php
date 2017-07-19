<?php
	require_once 'db.php';
	require 'user.php';
	ob_start();
	session_start();
	
	//If the CEO of BMD attempts to add a user this method is used
	if(isset($_POST['address']) && isset($_POST['addUsername']) && !empty($_POST['addUsername']) && isset($_POST['addPassword']) && !empty($_POST['addPassword']) && isset($_POST['addPassword2']) && !empty($_POST['addPassword2']) && isset($_POST['email']) && !empty($_POST['email']) && isset($_POST['firstname']) && !empty($_POST['firstname']) && isset($_POST['lastname']) && !empty($_POST['lastname']) && isset($_POST['type']) && !empty($_POST['type']) && isset($_POST['agree']))
	{
		$address = $_POST['address'];
		$username = $_POST['addUsername'];
		$password = $_POST['addPassword'];
		$repassword = $_POST['addPassword2'];
		$email = $_POST['email'];
		$firstname = $_POST['firstname'];
		$lastname = $_POST['lastname'];
		$type = $_POST['type'];
		$agree = $_POST['agree'];
		
		if($type == 'business' && empty($_POST['address']))
		{
			$error = 'To be a business you must have a location';
		} else if(empty($_POST['address'])) {
			$address = 'N/A';
		}
		
		if($password != $repassword)
		{
			$error = 'Sorry, password doesn\'t match.';
		}
		
		if($type == 'customer')
		{
			$type = 'active';
		}
		
		if(!isset($error))
		{
			$db->addRegularUser($username, $password, $email, $firstname, $lastname, $type, $agree, $address);
			$isLoggedIn = $db->userLoginCheck($username, $password);
			$urlTo = 'http://www.clutchdetailers.com';
			include '../redirect.html';
//			header('Location: ../index.php');
		}	
// 		$message = 'Thank You for your business\r\n We are confirming that you would like to be in this.';
// 		$_SESSION['user']->sendConfirmationEmail($email, $message, $email, $subject);
	}
	
  	if(!isset($_SESSION['user']) && empty($_SESSION['user']))
    {
    	$urlTo = 'http://www.clutchdetailers.com';
    	include '../redirect.html';
//		include '../DeepBlueAdmin/register-out.html';
  	} else {
		header('Location: ../index.php');
  	}
?>