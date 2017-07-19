<?php
	class DatabaseConnect 
	{
		public $connect;
		
		public function __construct($hd, $username, $password)
		{
			if(!@$this->connect($hd, $username, $password))
			{
				echo 'Connection has failed';
			} else if($this->connect($hd, $username, $password)){
				
			}
		}
		
		public function connect($hd, $username, $password)
		{
			try
			{
				$this->connect = new PDO($hd, $username, $password);
			} catch(PDOException $e) {
				echo 'ERROR: ' . $e->getMessage();
				return false;
			}
			return true;
		}
		
		/*
		*	Allows the User to loggin 
		*/
		public function userLoginCheck($username, $password)
		{
			$i = 0;
			$mdPass = md5($password);
			$addRequestString = "SELECT * FROM users WHERE username=:username AND password=:mdPass";
			$addRequestQuery = $this->connect->prepare($addRequestString);
			$addRequestQuery->bindParam(':username', $username);
			$addRequestQuery->bindParam(':mdPass', $mdPass);
			$addRequestQuery->execute();
			while($row = $addRequestQuery->fetch())
			{
				$user = $row['username'];
 				$pass = $row['password'];
 				$id = $row['id'];
 				$type = $row['type'];
 				$address = $row['address'];
 				$_SESSION['user'] = new User($user, $pass, $id, $type, $address);
				return true;
			}
			return false;
		}
		
		/*
		*	Get All information on a user based on id
		*/
		public function getUserInfo($identificationNumber)
		{
			$getInfoString = "SELECT * FROM users WHERE id=:idNum";
			$getInfoQuery = $this->connect->prepare($getInfoString);
			$getInfoQuery->bindParam(':idNum', $identificationNumber);
			$getInfoQuery->execute();
			$row = $getInfoQuery->fetch();
			return($row);
		}
		
		/*
		*	Change User Info
		*/
		public function changeUserInfo($newVal, $col, $userId, $table)
		{
			$changeInfoString = "UPDATE $table SET $col=:newVal WHERE id=:userId";
			$changeInfoQuery = $this->connect->prepare($changeInfoString);
			$changeInfoQuery->bindParam(':newVal', $newVal);
			$changeInfoQuery->bindParam(':userId', $userId);
			if($changeInfoQuery->execute())
			{
				return true;
			} else {
				return false;
			}
		}
		
		/*
		*	Allows the CEO of BMD to add users to the system whether they be employees or competitive business
		*/
		public function addUser($username, $password, $email, $firstname, $lastname, $type)
		{
			$mdPass = md5($password);
			$addUserString = "INSERT INTO users (username, password, email, firstname, lastname, type) VALUES (:username, :mdPass, :email, :firstname, :lastname, :type)";
			$addUserQuery = $this->connect->prepare($addUserString);
			$addUserQuery->bindParam(':username', $username);
			$addUserQuery->bindParam(':mdPass', $mdPass);
			$addUserQuery->bindParam(':email', $email);
			$addUserQuery->bindParam(':firstname', $firstname);
			$addUserQuery->bindParam(':lastname', $lastname);
			$addUserQuery->bindParam(':type', $type);
			$addUserQuery->execute();
		}
		
		/*
		*	Allows regular people to add users to the system whether they be customers or servicers
		*/
		public function addRegularUser($username, $password, $email, $firstname, $lastname, $type, $agree, $address)
		{
			$mdPass = md5($password);
			$addUserString = "INSERT INTO users (username, password, email, firstname, lastname, type, address) VALUES (:username, :mdPass, :email, :firstname, :lastname, :type, :address)";
			$addUserQuery = $this->connect->prepare($addUserString);
			$addUserQuery->bindParam(':username', $username);
			$addUserQuery->bindParam(':mdPass', $mdPass);
			$addUserQuery->bindParam(':email', $email);
			$addUserQuery->bindParam(':firstname', $firstname);
			$addUserQuery->bindParam(':lastname', $lastname);
			$addUserQuery->bindParam(':type', $type);
			$addUserQuery->bindParam(':address', $address);
			$addUserQuery->execute();
		}
		
		/*
		* Allow Users with a profile to add a service request	
		*/
		public function businessServiceRequest($vehicleType, $vehicleNumbers, $date, $location, $userId, $detailType)
		{
			$serviceRequestString = "INSERT INTO businessServiceRequest (vehicleType, numOfVehicles, dateNeeded, requesterId, detailType, location) VALUES (:vtype, :numVehicles, :dateNeeded, :requesterId, :detailType, :reqlocation)";
			$serviceRequestQuery = $this->connect->prepare($serviceRequestString);
			$serviceRequestQuery->bindParam(':vtype', $vehicleType);
			$serviceRequestQuery->bindParam(':numVehicles', $vehicleNumbers);
			$serviceRequestQuery->bindParam(':dateNeeded', $date);
			$serviceRequestQuery->bindParam(':requesterId', $userId);
			$serviceRequestQuery->bindParam(':detailType', $detailType);
			$serviceRequestQuery->bindParam(':reqlocation', $location);
			if($serviceRequestQuery->execute())
			{
				return true;
			} else {
				return false;
			}
		
		}
		
		/*
		*	Alert DataBase that the payment Has Been Recieved
		*/
		public function paySuccess($paidId)
		{
			$paidString = "UPDATE servicerequest SET payment='paid' WHERE id=:paidId";
			$paidQuery = $this->connect->prepare($paidString);
			$paidQuery->bindParam(':paidId', $paidId);
			$paidQuery->execute();
		}
		
		/*
		*
		*/
		public function payBusinessSuccess($paidId)
		{
			$paidString = "UPDATE businessServiceRequest SET payment='paid' WHERE id=:paidId";
			$paidQuery = $this->connect->prepare($paidString);
			$paidQuery->bindParam(':paidId', $paidId);
			$paidQuery->execute();
		}
		
		/*
		*	Check if the password is the current password
		*/
		public function checkPassword($currentPass, $userId)
		{
			$checkString = "SELECT password FROM users WHERE id=:userId";
			$checkQuery = $this->connect->prepare($checkString);
			$checkQuery->bindParam(':userId', $userId);
			$checkQuery->execute();
			$row = $checkQuery->fetch();
			if($row['password'] == md5($currentPass))
			{
				return true;
			} else {
				return false;
			}
		}
		
		/*
		*	Allow anyone to send us a message
		*/
		public function contactUs($firstname, $lastname, $email, $reason, $message)
		{
			$queryString = "INSERT INTO contact (firstname, lastname, email, reason, message) VALUES (:firstname, :lastname, :email, :reason, :message)";
			$query = $this->connect->prepare($queryString);
			$query->bindParam(':firstname', $firstname);
			$query->bindParam(':lastname', $lastname);
			$query->bindParam(':email', $email);
			$query->bindParam(':reason', $reason);
			$query->bindParam(':message', $message);
			if($query->execute())
			{
				return true;
			} else {
				return false;
			}
		}
		
		/*
		*	Get cars user saved
		*/
		public function getSavedCars($identification, $table)
		{
			$savedVehix = array();
			$i = 0;
			$savedString = "SELECT * FROM $table WHERE userId=:identification";
			$savedQuery = $this->connect->prepare($savedString);
			$savedQuery->bindParam(':identification', $identification);
			$savedQuery->execute();
			while($row = $savedQuery->fetch())
			{
				if($table == 'savedBusinessRequest')
				{
					$savedVehix['userId'][$i] = $row['userId'];
					$savedVehix['vehicleType'][$i] = $row['vehicleType'];
					$savedVehix['detailType'][$i] = $row['detailType'];
					$savedVehix['numberOfVehicles'][$i] = $row['numberOfVehicles'];
				} else {
					$savedVehix['userId'][$i] = $row['userId'];
					$savedVehix['make'][$i] = $row['make'];
					$savedVehix['model'][$i] = $row['model'];
					$savedVehix['licensePlate'][$i] = $row['licensePlate'];
				}
				$i++;
			}
			return $savedVehix;
		}
		
		/*
		* Allow Business Users with a profile to add a service request	
		*/
		public function userServiceRequest($make, $model, $vehicleNum, $date, $location, $userId, $detailType)
		{
			$serviceRequestString = "INSERT INTO servicerequest (user, make, model, vehicleNumber, dateNeeded, location, detailType) VALUES (:user, :make, :model, :vehiclenumber, :dateneeded, :location, :detailType)";
			$serviceRequestQuery = $this->connect->prepare($serviceRequestString);
			$serviceRequestQuery->bindParam(':user', $userId);
			$serviceRequestQuery->bindParam(':make', $make);
			$serviceRequestQuery->bindParam(':model', $model);
			$serviceRequestQuery->bindParam(':vehiclenumber', $vehicleNum);
			$serviceRequestQuery->bindParam(':dateneeded', $date);
			$serviceRequestQuery->bindParam(':location', $location);
			$serviceRequestQuery->bindParam(':detailType', $detailType);
			if($serviceRequestQuery->execute())
			{
				return true;
			} else {
				return false;
			}
		
		}
		
		/*
		* Set the hash to get service requests
		*/
		public function setServiceHash($tableName)
		{
			$getEmptyHashString = "SELECT id, hash_id FROM ".$tableName." WHERE hash_id=''";
			$getEmptyHashQuery = $this->connect->prepare($getEmptyHashString);
			$getEmptyHashQuery->execute();
			while($row = $getEmptyHashQuery->fetch())
			{
				$uniqueId = $row['id'];
				$hash = md5($uniqueId);
				$setHashString = "UPDATE ".$tableName." SET hash_id=:hash WHERE id=:uniqueId";
				$setHashQuery = $this->connect->prepare($setHashString);
				$setHashQuery->bindParam(':hash', $hash);
				$setHashQuery->bindParam(':uniqueId', $uniqueId);
				$setHashQuery->execute();
			}
			return($hash);
		}
		
		/*
		* Get the order information for invoice
		*/
		public function getOrderInfo($hashedId, $tableName)
		{
			$getRow = "SELECT * FROM ".$tableName." WHERE hash_id=:hashedId";
			$getRowQuery = $this->connect->prepare($getRow);
			$getRowQuery->bindParam(':hashedId', $hashedId);
			$getRowQuery->execute();
			$row = $getRowQuery->fetch();
			return($row);
		}
		
		/*
		* Get All Service Requests
		*/
		public function getAllServices()
		{
			$i = 0;
			$selectString = "SELECT c.id, c.user, c.make, c.model, c.vehicleNumber, c.dateRequested, c.dateNeeded, c.location, c.detailType FROM servicerequest c, users co WHERE c.user = co.id";
			$selectQuery = $this->connect->prepare($selectString);
			$selectQuery->execute();
			while($row = $selectQuery->fetch())
			{
				$info['id'][$i] = $row['id'];
				$info['user'][$i] = $row['user'];
				$info['make'][$i] = $row['make'];
				$info['model'][$i] = $row['model'];
				$info['vehicleNumber'][$i] = $row['vehicleNumber'];
				$info['dateRequested'][$i] = $row['dateRequested'];
				$info['dateNeeded'][$i] = $row['dateNeeded'];
				$info['location'][$i] = $row['location'];
				$info['detailType'][$i] = $row['detailType'];
				$i++;
			}
			return $info;
		}
		
		/*
		*  Add a service Request to a user
		*/
		public function acceptDetail($detailId, $servicerId)
		{
			$addUserString = "INSERT INTO pendingRequests (requestId, servicerId) VALUES (:detailId, :servicerId)";
			$addUserQuery = $this->connect->prepare($addUserString);
			$addUserQuery->bindParam(':detailId', $detailId);
			$addUserQuery->bindParam(':servicerId', $servicerId);
			$addUserQuery->execute();
			
			$selectRequest = "SELECT * FROM servicerequest WHERE id='$detailId'";
			$selectRequestQuery = $this->connect->prepare($selectRequest);
			$selectRequestQuery->execute();
			$row = $selectRequestQuery->fetch();
			$id = $row['id'];
			$user = $row['user'];
			$make = $row['make'];
			$model = $row['model'];
			$vehicleNumber = $row['vehicleNumber'];
			$dateRequested = $row['dateRequested'];
			$dateNeeded = $row['dateNeeded'];
			$location= $row['location'];
			$detailType = $row['detailType'];
			
			$addToTaken = "INSERT INTO takenRequests (requestId, user, make, model, vehicleNumber, dateRequested, dateNeeded, location, detailType) Values (:id, :user, :make, :model, :vehicleNumber, :dateRequested, :dateNeeded, :location, :detailType)";
			$addToTakenQuery = $this->connect->prepare($addToTaken);
			$addToTakenQuery->bindParam(':id', $id);
			$addToTakenQuery->bindParam(':user', $user);
			$addToTakenQuery->bindParam(':make', $make);
			$addToTakenQuery->bindParam(':model', $model);
			$addToTakenQuery->bindParam(':vehicleNumber', $vehicleNumber);
			$addToTakenQuery->bindParam(':dateRequested', $dateRequested);
			$addToTakenQuery->bindParam(':dateNeeded', $dateNeeded);
			$addToTakenQuery->bindParam(':location', $location);
			$addToTakenQuery->bindParam(':detailType', $detailType);
			$addToTakenQuery->execute();
			
			$deleteFromRequests = "DELETE FROM servicerequest WHERE id='$detailId'";
			$deleteFromRequestsQuery = $this->connect->prepare($deleteFromRequests);
			$deleteFromRequestsQuery->execute();
		}
		
		/*
		*	Add a business service Request to a servicer
		*/
		public function acceptBusinesDetail($detailId, $servicerId)
		{
			$selectBusinessString = "SELECT * FROM businessServiceRequest WHERE id='$detailId'";
			$selectBusinessQuery = $this->connect->prepare($selectBusinessString);
			$selectBusinessQuery->execute();
			$row = $selectBusinessQuery->fetch();
			$id = $row['id'];
			$vehicleType = $row['vehicleType'];
			$numOfVehicles = $row['numOfVehicles'];
			$dateRequested = $row['dateRequested'];
			$dateNeeded = $row['dateNeeded'];
			$requesterId = $row['requesterId'];
			$detailType = $row['detailType'];
			$location = $row['location'];
			
			$addToTaken = "INSERT INTO businessTakenRequests (vehicleType, numOfVehicles, dateRequested, dateNeeded, requesterId, detailType, location, servicerId, requestedId) Values (:vehicleType, :numOfVehicles, :dateRequested, :dateNeeded, :requesterId, :detailType, :location, :servicerId, :requestedId)";
			$addToTakenQuery = $this->connect->prepare($addToTaken);
			$addToTakenQuery->bindParam(':vehicleType', $vehicleType);
			$addToTakenQuery->bindParam(':numOfVehicles', $numOfVehicles);
			$addToTakenQuery->bindParam(':dateRequested', $dateRequested);
			$addToTakenQuery->bindParam(':dateNeeded', $dateNeeded);
			$addToTakenQuery->bindParam(':requesterId', $requesterId);
			$addToTakenQuery->bindParam(':detailType', $detailType);
			$addToTakenQuery->bindParam(':location', $location);
			$addToTakenQuery->bindParam(':servicerId', $servicerId);
			$addToTakenQuery->bindParam(':requestedId', $detailId);
			$addToTakenQuery->execute();
			
			$deleteFromBusString = "DELETE FROM businessServiceRequest WHERE id='$detailId'";
			$deleteFromBusQuery = $this->connect->prepare($deleteFromBusString);
			$deleteFromBusQuery->execute();
		}
		
		/*
		*  delete from all lists and send user email to confirm
		*/
		public function completeDetail($detailId, $serviceId)
		{
			$getDetailInfo = "SELECT * FROM takenRequests WHERE id='$detailId'";
			$getDetailQuery = $this->connect->prepare($getDetailInfo);
			$getDetailQuery->execute();
			$row = $getDetailQuery->fetch();
			$id = $row['id'];
			$requestId = $row['requestId'];
			$user = $row['user'];
			$make = $row['make'];
			$model = $row['model'];
			$vehicleNumber = $row['vehicleNumber'];
			$dateRequested = $row['dateRequested'];
			$dateNeeded = $row['dateNeeded'];
			$location = $row['location'];
			$detailType = $row['detailType'];
			
			echo 'detailId: '.$detailId.' ';
			$getCustomerEmail = "SELECT email, firstname FROM users WHERE id='$user'";
			$getCustomerEmailQuery = $this->connect->prepare($getCustomerEmail);
			$getCustomerEmailQuery->execute();
			$row2 = $getCustomerEmailQuery->fetch();
			$email = $row2['email'];
			$firstname = $row2['firstname'];
			
			echo 'Email: '.$email.' Firstname: '.$firstname;
			
			$addToCompleted = "INSERT INTO completedRequests (requestId, user, make, model, vehicleNumber, dateRequested, dateNeeded, location, detailType, servicerId) VALUES (:requestId, :user, :make, :model, :vehicleNumber, :dateRequested, :dateNeeded, :location, :detailType, :servicerId)";
			$addToCompletedQuery = $this->connect->prepare($addToCompleted);
			$addToCompletedQuery->bindParam(':requestId', $id);
			$addToCompletedQuery->bindParam(':user', $user);
			$addToCompletedQuery->bindParam(':make', $make);
			$addToCompletedQuery->bindParam(':model', $model);
			$addToCompletedQuery->bindParam(':vehicleNumber', $vehicleNumber);
			$addToCompletedQuery->bindParam(':dateRequested', $dateRequested);
			$addToCompletedQuery->bindParam(':dateNeeded', $dateNeeded);
			$addToCompletedQuery->bindParam(':location', $location);
			$addToCompletedQuery->bindParam(':detailType', $detailType);
			$addToCompletedQuery->bindParam(':servicerId', $serviceId);
			if($addToCompletedQuery->execute())
			{
				$deleteFromTaken = "DELETE FROM takenRequests WHERE id='$detailId'";
				$deleteFromTaken = $this->connect->prepare($deleteFromTaken);
				$deleteFromTaken->execute();
				//Send confirmation email to user that their detail has been completed
				$to = $email;
				$subject = "Car Detailed By DEG";
				$message = '
					<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
					<html>
					<head>
					<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
					<title>Detail Complete</title>
					<style type="text/css">
					a:hover { text-decoration: underline !important; }
					</style>
					</head>
					<body marginheight="0" topmargin="0" marginwidth="0" style="margin: 0px; background-color: #f7f2e4;" bgcolor="#f7f2e4" leftmargin="0">
						<!--100% body table-->
						<table cellspacing="0" border="0" cellpadding="0" width="100%" bgcolor="#f7f2e4">
							<tr>
								<td>
									<!--top links-->
									<table width="100%" border="0" cellspacing="0" cellpadding="0">
										<tr>
											<td valign="middle" align="center" height="45">
												<p style="font-size: 14px; line-height: 24px; font-family: Georgia, \'Times New Roman\', Times, serif; color: #b0a08b; margin: 0px;">
													Is this email not displaying correctly? <webversion style="color: #bc1f31; text-decoration: none;" href="#">Try the web version.</webversion>
												</p>
											</td>
										</tr>
									</table>
									<!--header-->
									<table style="background:url(images/header-bg.jpg); background-repeat: no-repeat; background-position: center; background-color: #fffdf9;" width="684" border="0" align="center" cellpadding="0" cellspacing="0">
										<tr>
											<td>
												<table width="100%" border="0" cellspacing="0" cellpadding="0">
													<tr>
														<td valign="top" width="173">
														<!--ribbon-->
															<table border="0" cellspacing="0" cellpadding="0">
																<tr>
																	<td height="120" width="45"></td>
																	<td background="images/ribbon.jpg" valign="top" bgcolor="#c72439" height="120" width="80">
																		<table width="100%" border="0" cellspacing="0" cellpadding="0">
																			<tr>
																				<td valign="bottom" align="center" height="35">
																					<p style="font-size: 14px; font-family: Georgia, \'Times New Roman\', Times, serif; color: #ffffff; margin-top: 0px; margin-bottom: 0px;">CONGRATS</p>
																				</td>
																			</tr>
																			<tr>
																				<td valign="top" align="center">
																					<p style="font-size: 36px; font-family: Georgia, \'Times New Roman\', Times, serif; color: #ffffff; margin-top: 0px; margin-bottom: 0px; text-shadow: 1px 1px 1px #333;">#1</p>
																				</td>
																			</tr>
																		</table>
																	</td>
																</tr>
															</table><!--ribbon-->
														</td>
														<td valign="middle" width="493">
															<table width="100%" border="0" cellspacing="0" cellpadding="0">
																<tr>
																	<td height="60"></td>
																</tr>
																<tr>
																	<td>
																		<h1 style="color: #333; margin-top: 0px; margin-bottom: 0px; font-weight: normal; font-size: 48px; font-family: Georgia, \'Times New Roman\', Times, serif">You\'re Car Is Complete!</em></h1>
																	</td>
																</tr>
																<tr>
																	<td height="40">
																	</td>
																</tr>
															</table>
															<!--date-->
															<table border="0" cellspacing="0" cellpadding="0">
																<tr>
																	<td valign="top" align="center" bgcolor="#312c26" background="images/date-bg.jpg" width="357" height="42">
																		<table width="100%" border="0" cellspacing="0" cellpadding="0">
																			<tr>
																				<td height="5"></td>
																			</tr>
																		</table>
																		<p style="font-size: 24px; font-family: Georgia, \'Times New Roman\', Times, serif; color: #ffffff; margin-top: 0px; margin-bottom: 0px;">Go For A Drive, You Deserve It</p>
																	</td>
																</tr>
															</table><!--/date-->
														</td>
														<td width="18"></td>
													</tr>
												</table>
											</td>
										</tr>
									</table><!--/header-->
									<!--email container-->
									<table bgcolor="#fffdf9" cellspacing="0" border="0" align="center" cellpadding="30" width="684">
										<tr>
											<td>
												<!--email content-->
												<table cellspacing="0" border="0" id="email-content" cellpadding="0" width="624">
													<tr>
														<td>
															<!--section 1-->
															<table cellspacing="0" border="0" cellpadding="0" width="100%">
																<tr>
																	<td valign="top" align="center">
	
																		<img src="images/360z.jpg" alt="image dsc" style="border: solid 1px #FFF; box-shadow: 2px 2px 6px #333; -webkit-box-shadow: 2px 2px 6px #333; -khtml-box-shadow: 2px 2px 6px #333; -moz-box-shadow: 2px 2px 6px #333;" width="622" />
																		<!--line break-->
																		<table width="100%" border="0" cellspacing="0" cellpadding="0">
																			<tr>
																				<td valign="bottom" height="50"><img src="images/line-break.jpg" width="622" height="27"></td>
																			</tr>
																		</table><!--/line break-->
																		<h1 style="font-size: 36px; font-weight: normal; color: #333333; font-family: Georgia, \'Times New Roman\', Times, serif; margin-top: 0px; margin-bottom: 20px;">DEG pays <em>attention to detail</em></h1>
																		<p style="font-size: 16px; line-height: 22px; font-family: Georgia, \'Times New Roman\', Times, serif; color: #333; margin: 0px;">
																			Here at DEG we pay attention to detail. We make sure that every detail we do is through so that every customer
																			can say they had a job well done. If you don\'t think we delivered on our promise please 
																			<a style="color: #bc1f31; text-decoration: none;" href="#">let us know</a> by clicking the orange text. We employ 
																			the very best detailers and make sure that each job is up to DEG standard. It would help us a lot if you could 
																			<a style="color: #bc1f31; text-decoration: none;" href="#">rate the service</a> that you just got. To Get the same 
																			servicer every time by <a style="color: #bc1f31; text-decoration: none;" href="#">logging in</a> and adding them to 
																			your list of favorite detailers!
																		</p>
																	</td>
																</tr>
															</table><!--/section 1-->
															<!--line break-->
															<table cellspacing="0" border="0" cellpadding="0" width="100%">
																<tr>
																	<td height="72"><img src="images/line-break-2.jpg" width="622" height="72">
																	</td>
																</tr>
																<tr>
																	<td align="center">
																		<h1 style="font-size: 36px; font-weight: normal; color: #333333; font-family: Georgia, \'Times New Roman\', Times, serif; margin-top: 0px; margin-bottom: 20px;">DEG\'s Top Picks</h1>
																	</td>	
																</tr>
															</table><!--/line break-->
															<!--section 2-->
															<table cellspacing="0" border="0" cellpadding="0" width="100%">
																<tr>
																	<td>
																		<table cellspacing="0" border="0" cellpadding="8" width="100%" style="margin-top: 10px;">
																			<tr>
																				<td valign="top">
																					<p style="font-size: 17px; line-height: 22px; font-family: Georgia, \'Times New Roman\', Times, serif; color: #333; margin: 0px;"><img src="images/crossfire.jpg" height="108" alt="img2" style="border: solid 1px #FFF; box-shadow: 2px 2px 6px #333; -webkit-box-shadow: 2px 2px 6px #333; -khtml-box-shadow: 2px 2px 6px #333; -moz-box-shadow: 2px 2px 6px #333;" width="138" /></p>
																					<p style="color: #333333; font-size: 18px; font-family: Georgia, \'Times New Roman\', Times, serif; margin: 12px 0px; font-weight: bold;">This Week</p>
																					<p style="font-size: 16px; line-height: 24px; font-family: Georgia, \'Times New Roman\', Times, serif; color: #333; margin: 0px;">This is the Chrysler Crossfire! Just looking at this stunning car should be enough to know why we love it. <a style="color: #bc1f31; text-decoration: none;" href="#">Learn more here!</a></p>
																				</td>
	
																				<td valign="top">
																					<p style="font-size: 17px; line-height: 22px; font-family: Georgia, \'Times New Roman\', Times, serif; color: #333; margin: 0px;"><img src="images/porsche.jpg" height="108" alt="img3" style="border: solid 1px #FFF; box-shadow: 2px 2px 6px #333; -webkit-box-shadow: 2px 2px 6px #333; -khtml-box-shadow: 2px 2px 6px #333; -moz-box-shadow: 2px 2px 6px #333;" width="138" /></p>
																					<p style="font-size: 18px; font-family: Georgia, \'Times New Roman\', Times, serif; color: #333333; margin: 12px 0px; font-weight: bold;">Last Week</p>
																					<p style="font-size: 16px; line-height: 24px; font-family: Georgia, \'Times New Roman\', Times, serif; color: #333; margin: 0px;">This is the Porsche 911 Turbo! Just looking at this stunning car should be enough to know why we love it. <a style="color: #bc1f31; text-decoration: none;" href="#">Learn more here!</a></p>
																				</td>
	
																				<td valign="top">
																					<p style="font-size: 17px; line-height: 22px; font-family: Georgia, \'Times New Roman\', Times, serif; color: #333; margin: 0px;"><img src="images/Shelby.jpg" height="108" alt="img4" style="border: solid 1px #FFF; box-shadow: 2px 2px 6px #333; -webkit-box-shadow: 2px 2px 6px #333; -khtml-box-shadow: 2px 2px 6px #333; -moz-box-shadow: 2px 2px 6px #333;" width="138" /></p>
																					<p style="font-size: 18px; font-family: Georgia, \'Times New Roman\', Times, serif; color: #333333; margin: 12px 0px; font-weight: bold;">This Year</p>
																					<p style="font-size: 16px; line-height: 24px; font-family: Georgia, \'Times New Roman\', Times, serif; color: #333; margin: 0px;">This is the Shelby Cobra! Just looking at this stunning car should be enough to know why you should love it. <a style="color: #bc1f31; text-decoration: none;" href="#">Learn more here!</a></p>
																				</td>
	
																				<td valign="top">
																					<p style="font-size: 17px; line-height: 22px; font-family: Georgia, \'Times New Roman\', Times, serif; color: #333; margin: 0px;"><img src="images/farrari.jpg" height="108" alt="img5" style="border: solid 1px #FFF; box-shadow: 2px 2px 6px #333; -webkit-box-shadow: 2px 2px 6px #333; -khtml-box-shadow: 2px 2px 6px #333; -moz-box-shadow: 2px 2px 6px #333;" width="138" /></p>
																					<p style="font-size: 18px; font-family: Georgia, \'Times New Roman\', Times, serif; color: #333333; margin: 12px 0px; font-weight: bold;">All Time</p>
																					<p style="font-size: 16px; line-height: 24px; font-family: Georgia, \'Times New Roman\', Times, serif; color: #333; margin: 0px;">This is the Ferrari Enzo! Just a glance at this great car will be enough to love it like us. <a style="color: #bc1f31; text-decoration: none;" href="#">Learn more here!</a></p>
																				</td>
																			</tr>
																		</table>
																	</td>
																</tr>
															</table><!--/section 2-->
															<!--section 3-->
															<table cellspacing="0" border="0" cellpadding="0" width="100%">
																<tr>
																	<td>
																		<!--line break-->
																		<table cellspacing="0" border="0" cellpadding="0" width="100%">
																			<tr>
																				<td height="72">
																					<img src="images/line-break-2.jpg" width="622" height="72">
																				</td>
																			</tr>
																			<tr>
																				<td align="center">
																					<h1 style="font-size: 36px; font-weight: normal; color: #333333; font-family: Georgia, \'Times New Roman\', Times, serif; margin-top: 0px; margin-bottom: 20px;">DEG\'s Top Stories</h1>
																				</td>	
																			</tr>
																		</table><!--/line break-->
																		<table cellspacing="0" border="0" cellpadding="0" width="100%">
																			<tr>
																				<td valign="top" width="378">
																					<h1 style="font-size: 24px; font-family: Georgia, \'Times New Roman\', Times, serif; color: #333333; margin-top: 0px; margin-bottom: 12px;">Your car can be DEG\'s next top pick!</h1>
																					<p style="font-size: 16px; line-height: 22px; font-family: Georgia, \'Times New Roman\', Times, serif; color: #333; margin: 0px;">
																						We choose our favorite details from all the cars that get detailed through our site. Every week we go through hundreds of
																						details and pic the cars that have the best stories. You don\'t have to drive a Ferrari, you just need to love your car.
																						Share the love with us and enter your car into the competition by checking the box that say\'s Enter my car, next time 
																						we come by to detail your car!
																						<a style="color: #bc1f31; text-decoration: none;" href="#">Read more &raquo;</a>
																					</p>
																				</td>
																				<td valign="top" width="246"><img src="images/mycar.jpg" align="right" alt="img8" style="border: solid 1px #FFF; box-shadow: 2px 2px 6px #333; -webkit-box-shadow: 2px 2px 6px #333; -khtml-box-shadow: 2px 2px 6px #333; -moz-box-shadow: 2px 2px 6px #333; float: right;" width="216" /></td>
																			</tr>
																		</table>
																		<!--line break-->
																		<table width="100%" border="0" cellspacing="0" cellpadding="0">
																			<tr>
																				<td valign="bottom" height="50"><img src="images/line-break.jpg" width="622" height="27"></td>
																			</tr>
																		</table><!--/line break-->
																		<table cellspacing="0" border="0" cellpadding="0" width="100%">
																			<tr>
																				<td valign="top" width="378">
																					<h1 style="font-size: 24px; font-family: Georgia, \'Times New Roman\', Times, serif; color: #333333; margin-top: 0px; margin-bottom: 12px;">Get a discount on your next DEG detail!</h1>
																					<p style="font-size: 16px; line-height: 22px; font-family: Georgia, \'Times New Roman\', Times, serif; color: #333; margin: 0px;">
																						Everyone who has a DEG profile and get\'s a detail done get\'s one count. Each count is a point towards
																						getting discounted details for the rest of the year! If you like the fast track, you can just buy the DEG discount cup
																						outright. When the detailer sees this they will know to give you the discount.
																						<a style="color: #bc1f31; text-decoration: none;" href="#">Read more &raquo;</a></p>
																				</td>
																				<td valign="top" width="246"><img src="images/babyonboard.jpg" align="right" alt="img8" style="border: solid 1px #FFF; box-shadow: 2px 2px 6px #333; -webkit-box-shadow: 2px 2px 6px #333; -khtml-box-shadow: 2px 2px 6px #333; -moz-box-shadow: 2px 2px 6px #333; float: right;" width="216" /></td>
																			</tr>
																		</table>
																		<!--line break-->
																		<table width="100%" border="0" cellspacing="0" cellpadding="0">
																			<tr>
																				<td valign="bottom" height="50"><img src="images/line-break.jpg" width="622" height="27"></td>
																			</tr>
																		</table><!--/line break-->
																		<table cellspacing="0" border="0" cellpadding="0" width="100%">
																			<tr>
																				<td valign="top" width="378">
																					<h1 style="font-size: 24px; font-family: Georgia, \'Times New Roman\', Times, serif; color: #333333; margin-top: 0px; margin-bottom: 12px;">Share your new detail with your friends, family, and enemys</h1>
																					<p style="font-size: 16px; line-height: 22px; font-family: Georgia, \'Times New Roman\', Times, serif; color: #333; margin: 0px;">
																						Show everyone how nice your car looks. We know you have the best looking car because you choose us and we are the best.
																						Everyone on our team has to pass a rigorous detail test. The test covers things like attention to detail and the top detailing
																						techniques used today. Our detailers not only know the best practices but they are on top of new techniques that are coming
																						out. We take detailing very seriously so you look your best everywhere you go.  
																						<a style="color: #bc1f31; text-decoration: none;" href="#">Read more &raquo;</a>
																					</p>
																				</td>
																				<td valign="top" width="246"><img src="images/parked.jpg" align="right" alt="img8" style="border: solid 1px #FFF; box-shadow: 2px 2px 6px #333; -webkit-box-shadow: 2px 2px 6px #333; -khtml-box-shadow: 2px 2px 6px #333; -moz-box-shadow: 2px 2px 6px #333; float: right;" width="216" /></td>
																			</tr>
																		</table>
																		<!--line break-->
																		<table cellspacing="0" border="0" cellpadding="0" width="100%">
																			<tr>
																				<td height="72">
																					<img src="images/line-break-2.jpg" width="622" height="72">
																				</td>
																			</tr>
																		</table><!--/line break-->
																	</td>
																</tr>
															</table><!--/section 3-->
														</td>
													</tr>
												</table><!--/email content-->
											</td>
										</tr>
									</table><!--/email container-->
									<!--footer-->
									<table width="680" border="0" align="center" cellpadding="30" cellspacing="0">
										<tr>
											<td valign="top">
												<p style="font-size: 14px; line-height: 24px; font-family: Georgia, \'Times New Roman\', Times, serif; color: #b0a08b; margin: 0px;">
													You’re receiving this newsletter because you received a DEG detail<br> 
													Want to subscribe? <unsubscribe style="color: #bc1f31; text-decoration: none;" href="#">Subscribe instantly.</unsubscribe>
												</p>
											</td>
											<td valign="top">
												<p style="font-size: 14px; line-height: 24px; font-family: Georgia, \'Times New Roman\', Times, serif; color: #b0a08b; margin: 0px;">
													1234 Detailers Way
													Gaithersburg, Maryland 20879
												</p>
											</td>
										</tr>
										<tr>
											<td height="30"></td>
											<td height="30"></td>
										</tr>
									</table><!--/footer-->
								</td>
							</tr>
						</table><!--/100% body table-->
					</body>
					</html>';
				// To send HTML mail, the Content-type header must be set
				$headers  = 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
				$headers .= 'To: '.$firstname.' <'.$email.'>' . "\r\n";
				$headers .= 'From: Birthday Reminder <birthday@example.com>' . "\r\n";
				$headers .= 'Cc: birthdayarchive@example.com' . "\r\n";
				$headers .= 'Bcc: birthdaycheck@example.com' . "\r\n";
				// Mail it
				mail($to, $subject, $message, $headers);
			}	
		}
		
		/*
		*	Accept completed Business detail, Delete from All lists, Log as completed Detail
		*/
		public function completeBusDetail($detailId, $servicerId)
		{
			$getDetailInfo = "SELECT * FROM businessTakenRequests WHERE id='$detailId'";
			$getDetailQuery = $this->connect->prepare($getDetailInfo);
			$getDetailQuery->execute();
			$row = $getDetailQuery->fetch();
			$id = $row['id'];
			$vehicleType = $row['vehicleType'];
			$numOfVehicles = $row['numOfVehicles'];
			$dateRequested = $row['dateRequested'];
			$dateNeeded = $row['dateNeeded'];
			$requesterId = $row['requesterId'];
			$detailType = $row['detailType'];
			$location = $row['location'];
			$servicerId = $row['servicerId'];
			$requestId = $row['requestedId'];
			$dateTaken = $row['dateTaken'];
			
			$addToCompleted = "INSERT INTO businessCompletedRequests (vehicleType, numOfVehicles, dateRequested, dateNeeded, requesterId, detailType, location, servicerId, requestedId, dateTaken, takenRequestId) VALUES (:vehicleType, :numOfVehicles, :dateRequested, :dateNeeded, :requesterId, :detailType, :location, :servicerId, :requestedId, :dateTaken, :takenRequestId )";
			$addToCompletedQuery = $this->connect->prepare($addToCompleted);
			$addToCompletedQuery->bindParam(':vehicleType', $vehicleType);
			$addToCompletedQuery->bindParam(':numOfVehicles', $numOfVehicles);
			$addToCompletedQuery->bindParam(':dateRequested', $dateRequested);
			$addToCompletedQuery->bindParam(':dateNeeded', $dateNeeded);
			$addToCompletedQuery->bindParam(':requesterId', $requesterId);
			$addToCompletedQuery->bindParam(':detailType', $detailType);
			$addToCompletedQuery->bindParam(':location', $location);
			$addToCompletedQuery->bindParam(':servicerId', $servicerId);
			$addToCompletedQuery->bindParam(':requestedId', $requestId);
			$addToCompletedQuery->bindParam(':dateTaken', $dateTaken);
			$addToCompletedQuery->bindParam(':takenRequestId', $id);	
			if($addToCompletedQuery->execute())
			{
				$deleteFromTaken = "DELETE FROM businessTakenRequests WHERE id='$detailId'";
				$deleteFromTakenQuery = $this->connect->prepare($deleteFromTaken);
				$deleteFromTakenQuery->execute();
			}	
			
			//Email The user and ask them how the service went and notify them that they have 72 hours to reply
		}
		
		/*
		*	Get the completed services for the month to calculate income
		*/
		public function getIncome($detailerId)
		{
			$income = 0;
			$i = 0;
			$j = 0;
			$k = 0;
			$lengthOfRequests = 0;
			$listOfRequests;
			$selectRequest = "SELECT dateCompleted, detailType FROM completedRequests WHERE servicerId='$detailerId'";
			$selectRequestQuery = $this->connect->prepare($selectRequest);
			$selectRequestQuery->execute();
			while($row = $selectRequestQuery->fetch())
			{
				$serviceRequestIds['dateCompleted'][$i] = $row['dateCompleted'];
				$serviceRequestIds['detailType'][$i] = $row['detailType'];
				if($serviceRequestIds['detailType'][$i] == 'Car Wash and Wax')
				{
					$income += 38;
				} else if($serviceRequestIds['detailType'][$i] == 'Interior Detail') {
					$income += 80;
				} else if($serviceRequestIds['detailType'][$i] == 'Exterior Detail') {
					$income += 70;
				} else if($serviceRequestIds['detailType'][$i] == 'BMD Express Detail') {
					$income += 110;
				} else if($serviceRequestIds['detailType'][$i] == 'BMD Total Detail') {
					$income += 160;
				} else if($serviceRequestIds['detailType'][$i] == 'Carpet Shampoo') {
					$income += 45;
				} else if($serviceRequestIds['detailType'][$i] == 'Headlight Restoration') {
					$income += 30;
				} else if($serviceRequestIds['detailType'][$i] == 'Engine Cleaning') {
					$income += 50;
				} else {
					$income += 0;
				}
				$i++;
			}
			
			$selectBusinessRequest = "SELECT dateCompleted, detailType FROM businessCompletedRequests WHERE servicerId='$detailerId'";
			$selectBusinessRequestQuery = $this->connect->prepare($selectBusinessRequest);
			$selectBusinessRequestQuery->execute();
			while($row2 = $selectBusinessRequestQuery->fetch())
			{
				$serviceRequestIds['dateCompleted'][$i] = $row2['dateCompleted'];
				$serviceRequestIds['detailType'][$i] = $row2['detailType'];
				if($serviceRequestIds['detailType'][$i] == 'Car Wash and Wax')
				{
					$income += 38;
				} else if($serviceRequestIds['detailType'][$i] == 'Interior Detail') {
					$income += 80;
				} else if($serviceRequestIds['detailType'][$i] == 'Exterior Detail') {
					$income += 70;
				} else if($serviceRequestIds['detailType'][$i] == 'BMD Express Detail') {
					$income += 110;
				} else if($serviceRequestIds['detailType'][$i] == 'BMD Total Detail') {
					$income += 160;
				} else if($serviceRequestIds['detailType'][$i] == 'Carpet Shampoo') {
					$income += 45;
				} else if($serviceRequestIds['detailType'][$i] == 'Headlight Restoration') {
					$income += 30;
				} else if($serviceRequestIds['detailType'][$i] == 'Engine Cleaning') {
					$income += 50;
				} else {
					$income += 0;
				}
				$i++;
			}
			return $income;
		}
		
		/*
		*  Get an individuals services they have signed up to do
		*/
		public function getMyServices($userId)
		{
			$serviceRequestIds = array();
			$i = 0;
			$j = 0;
			$k = 0;
			$lengthOfRequests;
			$listOfRequests;
			$selectRequest = "SELECT requestId FROM pendingRequests WHERE servicerId='$userId'";
			$selectRequestQuery = $this->connect->prepare($selectRequest);
			$selectRequestQuery->execute();
			while($row = $selectRequestQuery->fetch())
			{
				$serviceRequestIds[$i] = $row['requestId'];
				$i++;
			}
			$lengthOfRequests = count($serviceRequestIds);
			for($j = 0; $j < $lengthOfRequests; $j++)
			{
				$reqNumber = $serviceRequestIds[$j];
				$selectString = "SELECT c.id, c.requestId, c.user, c.make, c.model, c.vehicleNumber, c.dateRequested, c.dateNeeded, c.location, c.detailType FROM takenRequests c, users co WHERE c.requestId='$reqNumber' AND c.user = co.id";
				$selectQuery = $this->connect->prepare($selectString);
				$selectQuery->execute();
				while($srow = $selectQuery->fetch())
				{
					$listOfRequests['id'][$k] = $srow['id'];
					$listOfRequests['requestId'][$k] = $srow['requestId'];
					$listOfRequests['user'][$k] = $srow['user'];
					$listOfRequests['make'][$k] = $srow['make'];
					$listOfRequests['model'][$k] = $srow['model'];
					$listOfRequests['vehicleNumber'][$k] = $srow['vehicleNumber'];
					$listOfRequests['dateRequested'][$k] = $srow['dateRequested'];
					$listOfRequests['dateNeeded'][$k] = $srow['dateNeeded'];
					$listOfRequests['location'][$k] = $srow['location'];
					$listOfRequests['detailType'][$k] = $srow['detailType'];
					$k++;
				}
			}
			if(isset($listOfRequests))
			{
				return $listOfRequests;
			}
			return false;	
		}
		
		/*
		*	Get All Business Requests a servicer has accepted
		*/
		public function getMyBusServiceRequests($userId)
		{
			$i = 0; 
			$selectAcceptedRequests = "SELECT * FROM businessTakenRequests WHERE servicerId='$userId'";
			$acceptedRequestsQuery = $this->connect->prepare($selectAcceptedRequests);
			$acceptedRequestsQuery->execute();
			while($row = $acceptedRequestsQuery->fetch())
			{
				$listOfAcceptedRequests['id'][$i] = $row['id'];
				$listOfAcceptedRequests['detailType'][$i] = $row['detailType'];
				$listOfAcceptedRequests['vehicleType'][$i] = $row['vehicleType'];
				$listOfAcceptedRequests['numOfVehicles'][$i] = $row['numOfVehicles'];
				$listOfAcceptedRequests['dateNeeded'][$i] = $row['dateNeeded'];
				$i++;
			}
			
			if(isset($listOfAcceptedRequests))
			{
				return $listOfAcceptedRequests;
			}
			
			return 0;
		}
		
		/*
		*  Get a Business' services they have requested
		*/
		public function getMyBusinessRequests($userId)
		{
			$serviceRequestIds = array();
			$i = 0;
			$j = 0;
			$k = 0;
			$lengthOfRequests;
			$listOfRequests;
			$selectRequest = "SELECT * FROM businessServiceRequest WHERE requesterId='$userId'";
			$selectRequestQuery = $this->connect->prepare($selectRequest);
			$selectRequestQuery->execute();
			while($row = $selectRequestQuery->fetch())
			{
				$listOfRequests['id'][$i] = $row['id'];
				$listOfRequests['vehicleType'][$i] = $row['vehicleType'];
				$listOfRequests['numOfVehicles'][$i] = $row['numOfVehicles'];
				$listOfRequests['dateNeeded'][$i] = $row['dateNeeded'];
				$listOfRequests['detailType'][$i] = $row['detailType'];
				$i++;
			}
			
			if(isset($listOfRequests))
			{
				return $listOfRequests;
			}
			return false;	
		}
		
		/*
		*	Get all business services
		*/
		public function getAllBusinessRequests()
		{
			$i = 0;
			$selectRequest = "SELECT * FROM businessServiceRequest";
			$selectRequestQuery = $this->connect->prepare($selectRequest);
			$selectRequestQuery->execute();
			while($row = $selectRequestQuery->fetch())
			{
				$serviceRequests['id'][$i] = $row['id'];
				$serviceRequests['vehicleType'][$i] = $row['vehicleType'];
				$serviceRequests['numOfVehicles'][$i] = $row['numOfVehicles'];
				$serviceRequests['dateNeeded'][$i] = $row['dateNeeded'];
				$serviceRequests['detailType'][$i] = $row['detailType'];
				$serviceRequests['address'][$i] = $row['location'];
				$i++;
			}
			
			if(isset($serviceRequests))
			{
				return $serviceRequests;
			}
			return false;
		}
		
		/*
		*	Save a car
		*/
		public function saveCar($make, $model, $userId)
		{
			$prevSaved = false;
			$i = 0;
			$saveNewCarString = "SELECT * FROM savedCars WHERE userId='$userId'";
			$saveNewCarQuery = $this->connect->prepare($saveNewCarString);
			$saveNewCarQuery->execute();
			while($row = $saveNewCarQuery->fetch())
			{
				if($row['make'] == $make && $row['model'] == $model)
				{
					$prevSaved = true;
				}
			}
			
			if($prevSaved == false)
			{
				$enterNewCarString = "INSERT INTO savedCars (userId, make, model) VALUES (:userId, :make, :model)";
				$enterNewCarQuery = $this->connect->prepare($enterNewCarString);
				$enterNewCarQuery->bindParam(':userId', $userId);
				$enterNewCarQuery->bindParam(':make', $make);
				$enterNewCarQuery->bindParam(':model', $model);
				$enterNewCarQuery->execute();
			}
			
		}
		
		/*
		*	Save a business request
		*/
		public function saveBusinessRequest($userId, $vehicleType, $detailType, $vehicleNumbers)
		{
			$prevSaved = false;
			$i = 0;
			$saveNewCarString = "SELECT * FROM savedBusinessRequest WHERE userId='$userId'";
			$saveNewCarQuery = $this->connect->prepare($saveNewCarString);
			$saveNewCarQuery->execute();
			while($row = $saveNewCarQuery->fetch())
			{
				if($row['vehicleType'] == $vehicleType && $row['detailType'] == $detailType && $row['numberOfVehicles'] == $vehicleNumbers)
				{
					$prevSaved = true;
				}
			}
			
			if($prevSaved == false)
			{
				$enterNewCarString = "INSERT INTO savedBusinessRequest (userId, vehicleType, detailType, numberOfVehicles) VALUES (:userId, :vehicleType, :detailType, :numberOfVehicles)";
				$enterNewCarQuery = $this->connect->prepare($enterNewCarString);
				$enterNewCarQuery->bindParam(':userId', $userId);
				$enterNewCarQuery->bindParam(':vehicleType', $vehicleType);
				$enterNewCarQuery->bindParam(':detailType', $detailType);
				$enterNewCarQuery->bindParam(':numberOfVehicles', $vehicleNumbers);
				$enterNewCarQuery->execute();
			}
		}
		
		/*
		*	Get all messages for a user using their id
		*/
		public function getAllMessages($identification)
		{
			$messages = array();
			$i = 0;
			$getMessageString = "Select * FROM messages WHERE userTo=:identification";
			$getMessageQuery = $this->connect->prepare($getMessageString);
			$getMessageQuery->bindParam(':identification', $identification);
			$getMessageQuery->execute();
			while($row = $getMessageQuery->fetch())
			{
				$messages['from'][$i] = $row['userFrom'];
				$messages['message'][$i] = $row['message'];
				$messages['read'][$i] = $row['userRead'];
				$i++;
			}
			return $messages;
		}
		
		/*
		*	Get count of messages for user useing their id
		*/
		public function getNumberOfMessages($identification)
		{
			$getCountString = "SELECT count(*) FROM messages WHERE userTo=:identification";
			$getCountQuery = $this->connect->prepare($getCountString);
			$getCountQuery->bindParam(':identification', $identification);
			$getCountQuery->execute();
			$row = $getCountQuery->fetch();
			return $row[0];
		}
		
		/*
		*	Send a message to user
		*/
		public function sendMessage($to, $from, $message)
		{
			$sendMessageString = "INSERT INTO messages (userTo, userFrom, message) VALUES (:to, :from, :messages, :read)";
			$sendMessageQuery = $this->connect->prepare($sendMessageString);
			$sendMessageQuery->bindParam(':to', $to);
			$sendMessageQuery->bindParam(':from', $from);
			$sendMessageQuery->bindParam(':messages', $message);
			$sendMessageQuery->bindParam(':read', 'Unread');
			if($sendMessageQuery->execute())
			{
				return true;
			} else {
				return false;
			}
		}
	}
	
	$db = new DatabaseConnect('mysql:host=localhost;dbname=clutchde_site', 'clutchde', 'F8n;UDNovp');
?>








