<?php
	require_once 'db.php';
	require_once 'user.php';
	ob_start();
	session_start();
	
	if(isset($_POST['completeDetail']) && !empty($_POST['completeDetail']))
	{
		$detailAcceptedList = $_POST['completeDetail'];
		$amountAccepted = count($detailAcceptedList);
		for($i = 0; $i < $amountAccepted; $i++)
		{
			$db->completeDetail($detailAcceptedList[$i], $_SESSION['user']->getUserId());
		}	
	}
	
	//For users
	$info = $db->getMyServices($_SESSION['user']->getUserId());
	$numberOfRequests = count($info['id']);
	
  	if(isset($_SESSION['user']) && !empty($_SESSION['user']))
    {
    	if($_SESSION['user']->getUserType() == 'servicer' || $_SESSION['user']->getUserType() == 'admin')
    	{
			include '../myRequests.html';
		} else {
			$noAccess = true;
			include '../error.html';
		}	
		
  	} else {
		header('Location: ../index.php');
  	}
?>