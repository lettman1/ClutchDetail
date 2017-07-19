<?php
	require_once 'db.php';
	require_once 'user.php';
	ob_start();
	session_start();
	
  	if(isset($_SESSION['buyId']) && !empty($_SESSION['buyId']))
    {
    	$db->payBusinessSuccess($_SESSION['buyId']);
    	unset($_SESSION['buyId']);
		include '../DeepBlueAdmin/pay-success.html';
  	} else {
		include '../DeepBlueAdmin/pay-failed.html';
  	}
?>