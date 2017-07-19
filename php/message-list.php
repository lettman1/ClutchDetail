<?php
	require_once 'db.php';
	require_once 'user.php';
	ob_start();
	session_start();
	
	//For users
	$info = $db->getAllServices();
	$businfo = $db->getAllBusinessRequests();
	$numberOfRequests = count($info['id']);
	$numberOfBusinessRequests = count($businfo['id']);
	
	$messageInfo = $db->getAllMessages($_SESSION['user']->getUserId());
	if(isset($messageInfo['message']))
	{
		$numberOfMessages = count($messageInfo['message']);
	} else {
		$numberOfMessages = 0;
	}
	
  	if(isset($_SESSION['user']) && !empty($_SESSION['user']))
    {
    	$accountType = $_SESSION['user']->getUserType();
    	if($accountType == 'admin' || $accountType == 'servicer')
    	{
    		//For Detailers
			include '../DeepBlueAdmin/message-list.html';
		} else if($accountType == 'business'){
			include '../DeepBlueAdmin/business-message-list.html';
		}
  	} else {
		header('Location: ../index.php');
  	}
?>