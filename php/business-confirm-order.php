<?php
	require_once 'db.php';
	require_once 'user.php';
	ob_start();
	session_start();
	
	if(isset($_POST))
	{
		$thePost = $_POST;
	}
	
	if(isset($_GET) && !empty($_GET))
	{
		if(isset($_GET['id']))
		{
			$mdID = $_GET['id'];
			$detailInfo = $db->getOrderInfo($mdID, 'businessServiceRequest');
			$userInfo = $db->getUserInfo($detailInfo['requesterId']);
			$address = explode(",", $detailInfo['location']);
			$needed = explode(" ", $detailInfo['dateNeeded']);
			$date = explode(".", $needed[0]);
			switch($date[0])
			{
				case '1':
					$month = 'January';
					break;
					
				case '2':
					$month = 'February';
					break;
					
				case '3':
					$month = 'March';
					break;		
				
				case '4':
					$month = 'April';
					break;
					
				case '5':
					$month = 'May';
					break;
					
				case '6':
					$month = 'June';
					break;
					
				case '7':
					$month = 'July';
					break;
					
				case '8':
					$month = 'August';
					break;
					
				case '9':
					$month = 'September';
					break;
					
				case '10':
					$month = 'October';
					break;
					
				case '11':
					$month = 'November';
					break;
					
				case '12':
					$month = 'December';
					break;								
			}
			$detailType = $detailInfo['detailType'];
			switch($detailType)
			{
				case 'Car Wash and Wax':
					$price = 35;
					break;
					
				case 'Interior Detail':
					$price = 50;
					break;
					
				case 'Exterior Detail':
					$price = 50;
					break;
					
				case 'BMD Express Detail':		
					$price = 110;
					break;
					
				case 'BMD Total Detail':
					$price = 160;
					break;
					
				case 'Carpet Shampoo':
					$price = 45;
					break;
					
				case 'Headlight Restoration':	
					$price = 30;
					break;
					
				case 'Engine Cleaning';
					$price = 50;
					break;	
			}			
			
			$ship = 0;
			$qty = $detailInfo['numOfVehicles'];
			$finalPrice = ($price * $qty) + $ship;
			$total = '$'.$price * $qty;
			$price = '$'.$price;
			$shipping = 'FREE';
			$shipping = '<span style="color: #D72A2A;">'.$shipping.'</span>';
		}
	}
	
	
  	if(isset($_SESSION['user']) && !empty($_SESSION['user']) && isset($_GET['id']))
    {
    	$numberOfMessages = $db->getNumberOfMessages($_SESSION['user']->getUserId());
		include '../DeepBlueAdmin/business-confirm-order.html';
  	} else {
		include '../DeepBlueAdmin/outside-service-request.html';
  	}
?>