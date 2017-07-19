<?php
	require_once 'db.php';
	require_once 'user.php';
	ob_start();
	session_start();
	
	if(isset($_POST))
	{
		$thePost = $_POST;
	}
	
	if(isset($_POST) && !empty($_POST))
	{
		if(!isset($_POST['agree']))
		{
			$error = "You must agree to all the terms and conditions.";
		} else if(empty($_POST['location'])) {
			$error = "You must type in the address of the detail";
		}
	}	
	
	//If the CEO of BMD attempts to add a user this method is used
	if(isset($_POST['make']) && !empty($_POST['make']) && isset($_POST['hour']) && !empty($_POST['hour']) && isset($_POST['minute']) && !empty($_POST['minute']) && isset($_POST['frame']) && !empty($_POST['frame']) && isset($_POST['model']) && !empty($_POST['model']) && isset($_POST['vehicle']) && isset($_POST['month']) && !empty($_POST['month']) && isset($_POST['day']) && !empty($_POST['day']) && isset($_POST['year']) && !empty($_POST['year']) && isset($_POST['location']) && !empty($_POST['location']) && isset($_POST['userId']) && !empty($_POST['userId']) && isset($_POST['agree']) && isset($_POST['detailType']) && !empty($_POST['detailType']))
	{
		$here = "We made it here";
		$hour = $_POST['hour'];
		$minute = $_POST['minute'];
		$frame = $_POST['frame'];
		$detailType = $_POST['detailType'];
		$make = $_POST['make'];
		$model = $_POST['model'];
		if(empty($_POST['vehicle']))
		{
			$vehicle = 'N/A';
		} else {
			$vehicle = $_POST['vehicle'];
		}	
		$month = $_POST['month'];
		$day = $_POST['day'];
		$year = $_POST['year'];
		$location = $_POST['location'];
		$userId = $_POST['userId'];
		$date = $month.'.'.$day.'.'.$year.' '.$hour.':'.$minute.' '.$frame;

		if($make == '--Please Select--')
		{
			if(!isset($error))
			{
				$error = "You have not choosen a make";
			} else {
				$error = "You also have not choosen a make";
			}	
		}
		
		if($model == '--Please Select--')
		{
			if(!isset($error))
			{
				$error = "You have not choosen a model";
			} else {
				$error .= ", model";
			}
		}
		
		if($month == '--Month--')
		{
			if(!isset($error))
			{
				$error = "You have not choosen a month";
			} else {
				$error .= ", month";
			}
		}
		
		if($day == '--Day--')
		{
			if(!isset($error))
			{
				$error = "You have not choosen a day";
			} else {
				$error .= ", day";
			}
		}
		
		if($year == '--Year--')
		{
			if(!isset($error))
			{
				$error = "You have not choosen a year";
			} else {
				$error .= ", year";
			}
		}
		
		if($hour == '--hour--')
		{
			if(!isset($error))
			{
				$error = "You have not choosen an hour";
			} else {
				$error .= ", hour";
			}
		}
		
		if($minute == '--minute--')
		{
			if(!isset($error))
			{
				$error = "You have not choosen an minute";
			} else {
				$error .= ", minute";
			}
		}
		
		if($frame == '--frame--')
		{
			if(!isset($error))
			{
				$error = "You have not choosen an frame";
			} else {
				$error .= ", frame";
			}
		}
		
		if($location == '')
		{
			if(!isset($error))
			{
				$error = "You have not entered a location";
			} else {
				$error .= ". You also have not entered a location.";
			}
		}
		
		if(isset($_POST['save']))
		{
			$db->saveCar($make, $model, $userId);
		}
		
		if(!isset($error))
		{
			$serviceRequested = $db->userServiceRequest($make, $model, $vehicle, $date, $location, $userId, $detailType);
			if($serviceRequested)
			{
				$hashId = $db->setServiceHash('servicerequest');	
				header('Location: confirm-order-out.php?id='.$hashId);
			}	
		}	
	}
	
  	if(isset($_SESSION['user']) && !empty($_SESSION['user']))
    {
    	$numberOfMessages = $db->getNumberOfMessages($_SESSION['user']->getUserId());
		include '../DeepBlueAdmin/service-request.html';
  	} else {
		include '../DeepBlueAdmin/outside-service-request.html';
  	}
?>