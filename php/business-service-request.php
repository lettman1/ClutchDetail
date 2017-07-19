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
		}
	}
	
	//If the CEO of BMD attempts to add a user this method is used
	if(isset($_POST['hour']) && !empty($_POST['hour']) && isset($_POST['minute']) && !empty($_POST['minute']) && isset($_POST['frame']) && !empty($_POST['frame']) && isset($_POST['vehicleType']) && !empty($_POST['vehicleType']) && isset($_POST['detailType']) && !empty($_POST['detailType']) && isset($_POST['vehicleNumbers']) && !empty($_POST['vehicleNumbers']) && isset($_POST['month']) && !empty($_POST['month']) && isset($_POST['day']) && !empty($_POST['day']) && isset($_POST['year']) && !empty($_POST['year']) && isset($_POST['userId']) && !empty($_POST['userId']) && isset($_POST['agree']))
	{
		$hour = $_POST['hour'];
		$minute = $_POST['minute'];
		$frame = $_POST['frame'];
		$detailType = $_POST['detailType'];
		$vehicleType = $_POST['vehicleType'];
		$vehicleNumbers = $_POST['vehicleNumbers'];	
		$month = $_POST['month'];
		$day = $_POST['day'];
		$year = $_POST['year'];
		$location = $_SESSION['user']->getUserAddress();
		$userId = $_POST['userId'];
		$date = $month.'.'.$day.'.'.$year.' '.$hour.':'.$minute.' '.$frame;

		if($detailType == '--Please Select--')
		{
			if(!isset($error))
			{
				$error = "You have not choosen a type of detail";
			} else {
				$error = "You also have not choosen a type of detail";
			}	
		}
		
		if($vehicleType == '--Please Select--')
		{
			if(!isset($error))
			{
				$error = "You have not choosen a vehicle type";
			} else {
				$error .= ", vehicle type";
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
		
		if($location == '')
		{
			if(!isset($error))
			{
				$error = "You have not entered a location";
			} else {
				$error .= ". You also have not entered a location.";
			}
		}
		
		if(!is_numeric($vehicleNumbers))
		{
			if(!isset($error))
			{
				$error = "The Number of Vehicles must be a number";
			} else {
				$error .= ". The number of Vehicles must be a number";
			}
		}
		
		if($vehicleNumbers == '')
		{
			if(!isset($error))
			{
				$error = "You must enter a number of vehicles";
			} else {
				$error .= ". You must enter a number of vehicles.";
			}
		}
		
		if(!isset($error))
		{
			if(isset($_POST['save']) && !empty($_POST['save']))
			{
				$saveRequest = $db->saveBusinessRequest($_SESSION['user']->getUserId(), $vehicleType, $detailType, $vehicleNumbers);
			}
		}
		
		if(!isset($error))
		{
			$serviceRequested = $db->businessServiceRequest($vehicleType, $vehicleNumbers, $date, $location, $userId, $detailType);
			if($serviceRequested)
			{
				$hashId = $db->setServiceHash('businessServiceRequest');
				header('Location: business-confirm-order.php?id='.$hashId);
			}	
		}	
	}
	
  	if(isset($_SESSION['user']) && !empty($_SESSION['user']))
    {
    	$numberOfMessages = $db->getNumberOfMessages($_SESSION['user']->getUserId());
    	$savedCars = $db->getSavedCars($_SESSION['user']->getUserId(), 'savedBusinessRequest');
    	$numSavedCars = count($savedCars['userId']);
		include '../DeepBlueAdmin/business-service-request.html';
  	} else {
		header('Location: ../index.php');
  	}
?>