<?php
	require_once 'db.php';
	require 'user.php';
	ob_start();
	session_start();
	
	if(isset($_POST['firstname']) && !empty($_POST['firstname']) && isset($_POST['lastname']) && !empty($_POST['lastname']) && isset($_POST['email']) && !empty($_POST['email']) && isset($_POST['reemail']) && !empty($_POST['reemail']) && isset($_POST['reason']) && !empty($_POST['reason']) && isset($_POST['message']) && !empty($_POST['message']))
	{
		$firstname = $_POST['firstname'];
		$lastname = $_POST['lastname'];
		$email = $_POST['email'];
		$reemail = $_POST['reemail'];
		$reason = $_POST['reason'];
		$message = $_POST['message'];
		
		if($email == $reemail)
		{
			$return = $db->contactUs($firstname, $lastname, $email, $reason, $message);
		}	
	} else if(isset($_POST) && count($_POST) > 0) {
		$return = false;
	}
	
	
	include '../DeepBlueAdmin/contact-out2.html';
?>