<?php
	require_once 'db.php';
	require_once 'user.php';
	ob_start();
	session_start();
	$userIdentification = $_SESSION['user']->getUserId();
	$table = "users";
	
	if(isset($_POST['firstname']) && !empty($_POST['firstname']))
	{
		$newVal = $_POST['firstname'];
		$col = "firstname";
		$success = $db->changeUserInfo($newVal, $col, $userIdentification, $table);
	}
	
	if(isset($_POST['lastname']) && !empty($_POST['lastname']))
	{
		$newVal = $_POST['lastname'];
		$col = "lastname";
		$success = $db->changeUserInfo($newVal, $col, $userIdentification, $table);
	}
	
	if(isset($_POST['companyname']) && !empty($_POST['companyname']))
	{
		$newVal = $_POST['companyname'];
		$col = "username";
		$success = $db->changeUserInfo($newVal, $col, $userIdentification, $table);
	}
	
	if(isset($_POST['email']) && !empty($_POST['email']))
	{
		$newVal = $_POST['email'];
		$col = "email";
		$success = $db->changeUserInfo($newVal, $col, $userIdentification, $table);
	}
	
	if(isset($_POST['address']) && !empty($_POST['address']))
	{
		$newVal = $_POST['address'];
		$col = "address";
		$success = $db->changeUserInfo($newVal, $col, $userIdentification, $table);
	}
	
	if(isset($_POST['aboutcompany']) && !empty($_POST['aboutcompany']))
	{
		$newVal = $_POST['aboutcompany'];
		$col = "aboutcompany";
	}
	
	if(isset($_POST['curpass']) && !empty($_POST['curpass']) && isset($_POST['newpass']) && !empty($_POST['newpass']) && isset($_POST['newpass2']) && !empty($_POST['newpass2']))
	{
		$currentPass = $_POST['curpass'];
		$newPassword = $_POST['newpass'];
		$checkPassword = $_POST['newpass2'];
		$newVal = md5($newPassword);
		$col = "password";
		
		if($newPassword == $checkPassword)
		{
			if($db->checkPassword($currentPass, $userIdentification))
			{
				$success = $db->changeUserInfo($newVal, $col, $userIdentification, $table);
			} else {
				$success = false;
			}
		} else {
			$success = false;
		}
	}
	
	$userInfo = $db->getUserInfo($_SESSION['user']->getUserId());
	
	
  	if(isset($_SESSION['user']) && !empty($_SESSION['user']))
    {
    	$accountType = $_SESSION['user']->getUserType();
    	if($accountType == 'admin' || $accountType == 'servicer')
    	{
    		//For Detailers
			$numberOfMessages = $db->getNumberOfMessages($_SESSION['user']->getUserId());
			include '../DeepBlueAdmin/settings.html';
		} else if($accountType == 'business'){
			$numberOfMessages = $db->getNumberOfMessages($_SESSION['user']->getUserId());
			include '../DeepBlueAdmin/business-settings.html';
		}
  	} else {
		header('Location: ../index.php');
  	}
?>