<?php
	require_once 'db.php';
	require_once 'user.php';
	ob_start();
	session_start();
	
	if(isset($_POST['source1']))
	{
		$_SESSION['buyId'] = $_POST['source1'];
	}
?>