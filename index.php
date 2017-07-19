<?php
	require_once 'php/db.php';
	require 'php/user.php';
	ob_start();
	session_start();
	
	//If the Detailer has completed a job
	if(isset($_POST['completeDetail']) && !empty($_POST['completeDetail']))
	{
		$detailAcceptedList = $_POST['completeDetail'];
		$amountAccepted = count($detailAcceptedList);
		for($i = 0; $i < $amountAccepted; $i++)
		{
			$db->completeDetail($detailAcceptedList[$i], $_SESSION['user']->getUserId());
		}	
	}
	
	//If the Detailer has completed a company job
	if(isset($_POST['completeBusDetail']) && !empty($_POST['completeBusDetail']))
	{
		$detailCompletedList = $_POST['completeBusDetail'];
		$amountCompleted = count($detailCompletedList);
		for($i = 0; $i < $amountCompleted; $i++)
		{
			$db->completeBusDetail($detailCompletedList[$i], $_SESSION['user']->getUserId());
		}
	}
	
	//Log User into the system
	if(isset($_POST['username']) && !empty($_POST['username']) && isset($_POST['password']) && !empty($_POST['password']))
	{
		$username = $_POST['username'];
		$password = $_POST['password'];
		//$db = new DatabaseConnect('mysql:host=localhost;dbname=clutchde_site','clutchde', 'F8n;UDNovp');
		$isLoggedIn = $db->userLoginCheck($username, $password);
		if(isset($_SESSION['user']) && !empty($_SESSION['user'])){
			$error = false;
		} else {
			$error = true;
		}
	}
	
  	if(isset($_SESSION['user']) && !empty($_SESSION['user']))
    {
    	$accountType = $_SESSION['user']->getUserType();
    	if($accountType == 'admin' || $accountType == 'servicer')
    	{
    		//For Detailers
			$info = $db->getMyServices($_SESSION['user']->getUserId());
			$busInfo = $db->getMyBusServiceRequests($_SESSION['user']->getUserId());
			$numberOfRequests = count($info['id']);
			$numberOfBusRequests = count($busInfo['id']);
			$totalRequests = $numberOfRequests + $numberOfBusRequests;
			$income = $db->getIncome($_SESSION['user']->getUserId());
			$numberOfMessages = $db->getNumberOfMessages($_SESSION['user']->getUserId());
			include 'DeepBlueAdmin/index.html';
		} else if($accountType == 'business'){
			$info = $db->getMyBusinessRequests($_SESSION['user']->getUserId());
			$numberOfRequests = count($info['id']);
			$income = $db->getIncome($_SESSION['user']->getUserId());
			$numberOfMessages = $db->getNumberOfMessages($_SESSION['user']->getUserId());
			include 'DeepBlueAdmin/business-customer.html';
		} else {
			$info;
			$numberOfRequests = 0;
			$income = $db->getIncome($_SESSION['user']->getUserId());
			include 'DeepBlueAdmin/customer.html';
		}
		//echo 'You are logged in. <a href="logoutchat.php">Log Out</a>';
  	} else {
  		include 'DeepBlueAdmin/home-out2.html';
  	}
 // 	include 'DeepBlueAdmin/home-out2.html';
?>