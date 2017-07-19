<?php
	require_once 'user.php';
	ob_start();
	session_start();
	require_once 'db.php';
	

	if(isset($_POST['acceptDetail']) && !empty($_POST['acceptDetail']))
	{
		$detailAcceptedList = $_POST['acceptDetail'];
		$amountAccepted = count($detailAcceptedList);
		for($i = 0; $i < $amountAccepted; $i++)
		{
			$db->acceptDetail($detailAcceptedList[$i], $_SESSION['user']->getUserId());
		}	
	}
	
	if(isset($_POST['acceptBusDetail']) && !empty($_POST['acceptBusDetail']))
	{
		$busDetailAcceptedList = $_POST['acceptBusDetail'];
		$amountAccepted = count($busDetailAcceptedList);
		for($j = 0; $j < $amountAccepted; $j++)
		{
			$db->acceptBusinesDetail($busDetailAcceptedList[$j], $_SESSION['user']->getUserId());
		}	
	}
	
	//For users
	$info = $db->getAllServices();
	$businfo = $db->getAllBusinessRequests();
	$numberOfRequests = count($info['id']);
	$numberOfBusinessRequests = count($businfo['id']);
	
  	if(isset($_SESSION['user']) && !empty($_SESSION['user']))
    {
    	if($_SESSION['user']->getUserType() == 'servicer' || $_SESSION['user']->getUserType() == 'admin')
    	{
    		$numberOfMessages = $db->getNumberOfMessages($_SESSION['user']->getUserId());
			include '../DeepBlueAdmin/requests.html';
		} else {
			$noAccess = true;
			include '../error.html';
		}	
		
  	} else {
		header('Location: ../index.php');
  	}
?>