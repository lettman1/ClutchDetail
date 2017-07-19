<?php
	require_once 'db.php';
	require 'user.php';
	ob_start();
	session_start();
	
	if(isset($_POST['review']) && !empty($_POST['review']))
	{
		$review = $_POST['review'];
		
		if($email == $reemail)
		{
			$return = $db->postReview($firstname, $lastname, $email, $reason, $message);
		}	
	} else if(isset($_POST) && count($_POST) > 0) {
		$return = false;
	}
	
	
	include '../DeepBlueAdmin/contact-out2.html';
?>