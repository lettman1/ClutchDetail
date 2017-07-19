<?php
	// Start the session
	session_start();

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
		*	Add a current Token that can be used for OAuth 2.0 Accuess
		*/
		public function addAccessToken($access_token, $refresh_token) {
			//check for an existing access_token
			$access_query = "UPDATE access_tokens SET access_token = :access_token, refresh_token = :refresh_token";
			$rs = $this->connect->prepare($access_query);
			$rs->bindParam(':access_token', $access_token);
			$rs->bindParam(':refresh_token', $refresh_token);
			if($rs->execute()) {
				return true;
			}
		}
		
		/*
		*	Add line for the Cold Call Section
		*/
		public function addColdCallLead($surname, $name, $phone, $company, $website, $location) {
			$called = 0;
			$dontcall = 0;
			$addColdCallString = "INSERT INTO coldcall_leads (surname, customer_name, phone, company, website, location, called, nocall_list) VALUES (:surname, :customer_name, :phone, :company, :website, :location, :called, :nocall_list)";
			$addColdCallQuery = $this->connect->prepare($addColdCallString);
			$addColdCallQuery->bindParam(':surname', $surname);
			$addColdCallQuery->bindParam(':customer_name', $name);
			$addColdCallQuery->bindParam(':phone', $phone);
			$addColdCallQuery->bindParam(':company', $company);
			$addColdCallQuery->bindParam(':website', $website);
			$addColdCallQuery->bindParam(':location', $location);
			$addColdCallQuery->bindParam(':called', $called);
			$addColdCallQuery->bindParam(':nocall_list', $dontcall);
			if($addColdCallQuery->execute()) {
				return true;
			} else {
				return false;
			}
		}
		
		/*
		*	Add Submission of a Mission
		*/
		public function addSubmission($mission_id, $user_id, $email, $vote_id, $vzaar_id) {
			$addMissionString = "INSERT INTO mission_submissions (mission_id, user_id, email, vote_id, tokens, vzaar_id) VALUES (:mission_id, :user_id, :email, :vote_id, 0, :vzaar_id)";
			$addMissionQuery = $this->connect->prepare($addMissionString);
			$addMissionQuery->bindParam(':mission_id', $mission_id);
			$addMissionQuery->bindParam(':user_id', $user_id);
			$addMissionQuery->bindParam(':email', $email);
			$addMissionQuery->bindParam(':vote_id', $vote_id);
			$addMissionQuery->bindParam(':vzaar_id', $vzaar_id);
			if($addMissionQuery->execute()) {
				return true;
			}
		}
		
		/*
		*	Add tokens to a mission
		*/
		public function addToCurrentWage($mission_id, $add_num, $user_id) {
			$selectQueryString = "SELECT token_amount FROM user_tokens WHERE user_id=:user_id";
			$rs = $this->connect->prepare($selectQueryString);
			$rs->bindParam(':user_id', $user_id);
			$rs->execute();
			if($row = $rs->fetch()) {
				$token_amount = $row['token_amount'];
			}
			if($add_num <= $token_amount) {
				$add_string = "UPDATE missions SET current_wage = current_wage + :add_num WHERE id = :mission_id";
				$rs = $this->connect->prepare($add_string);
				$rs->bindParam(':add_num', $add_num);
				$rs->bindParam(':mission_id', $mission_id);
				if($rs->execute()) {
					return true;
				} else {
					return false;
				}
			} else {
				return 'in_funds';
			}
		}
		
		/*
		*	Add to 
		*/
		public function addToSubmissionWage($mission_id, $add_num, $user_id, $submission_id) {
			$selectQueryString = "SELECT token_amount FROM user_tokens WHERE user_id=:user_id";
			$rs = $this->connect->prepare($selectQueryString);
			$rs->bindParam(':user_id', $user_id);
			$rs->execute();
			if($row = $rs->fetch()) {
				$token_amount = $row['token_amount'];
			}
			if($add_num <= $token_amount) {
				$add_string = "UPDATE mission_submissions SET tokens = tokens + :add_num WHERE vote_id = :submission_id";
				$rs = $this->connect->prepare($add_string);
				$rs->bindParam(':add_num', $add_num);
				$rs->bindParam(':submission_id', $submission_id);
				if($rs->execute()) {
					return true;
				} else {
					return false;
				}
			} else {
				return 'in_funds';
			}
		}
		
		/*
		*	Adds A User to The Database
		*/
		public function addUser($firstName, $lastName, $email, $password, $username)
		{
			$hashPass = md5($password);
			$addUserString = "INSERT INTO users (email, username, firstname, lastname, password) VALUES (:email, :username, :firstname, :lastname, :password)";
			$addUserQuery = $this->connect->prepare($addUserString);
			$addUserQuery->bindParam(':email', $email);
			$addUserQuery->bindParam(':username', $username);
			$addUserQuery->bindParam(':firstname', $firstName);
			$addUserQuery->bindParam(':lastname', $lastName);
			$addUserQuery->bindParam(':password', $hashPass);
			if($addUserQuery->execute()) {
				if($this->userLoginCheck($email, $password)) {
					//Give the user 50 tokens to start
					//Get the new users id
					$getUserString = "SELECT id FROM users WHERE email = ':email' AND password = ':password'";
					$rs = $this->connect->prepare($getUserString);
					$rs->bindParam(':email', $email);
					$rs->bindParam(':password', $hashPass);
					$rs->execute();
					if($row = $rs->fetch()) {
						$userId = $row['id'];
						//Add 50 tokens to new user
						$tokenAmount = 50;
						$giveTokens = "INSERT INTO user_tokens (user_id, token_amount) VALUES (:userId, :token_amount)";
						$rsn = $this->connect->prepare($giveTokens);
						$rsn->bindParam(':userId', $userId);
						$rsn->bindParam(':token_amount', $tokenAmount);
						$rsn->execute();
					}
					return true;
				} else {
					return "no login";
				}
			} else {
				return "no add";
			}
			return false;
		}
		
		/*
		*	edit a cold call lead
		*/
		public function editColdCallLead($ccid, $surname, $name, $phone, $company, $website, $location) {
			$editColdCallString = "UPDATE coldcall_leads SET surname = :surname, customer_name = :customer_name, phone = :phone, company = :company, website = :website, location = :location WHERE id = :ccid";
			$editColdCallQuery = $this->connect->prepare($editColdCallString);
			$editColdCallQuery->bindParam(':surname', $surname);
			$editColdCallQuery->bindParam(':customer_name', $name);
			$editColdCallQuery->bindParam(':phone', $phone);
			$editColdCallQuery->bindParam(':company', $company);
			$editColdCallQuery->bindParam(':website', $website);
			$editColdCallQuery->bindParam(':location', $location);
			$editColdCallQuery->bindParam(':ccid', $ccid);
			if($editColdCallQuery->execute()) {
				return true;
			} else {
				return false;
			}
		}
		
		/*
		*	Get Access Token
		*/
		public function getAccessToken() {
			$access_token = '';
			$access_string = "SELECT access_token FROM access_tokens ORDER BY id DESC LIMIT 1";
			$rs = $this->connect->prepare($access_string);
			$rs->execute();
			while($row = $rs->fetch()) {
				$access_token = $row['access_token'];
			}
			return $access_token;
		}
		
		/*
		*	Gets all ads from database
		*/
		public function getAds() {
			$i = 0;
			$adsArray = array();
			$string = "SELECT id, url, type, value FROM ads WHERE max > used";
			$rs = $this->connect->prepare($string);
			$rs->execute();
			while($row = $rs->fetch()) {
				$adsArray[$i]['id'] = $row['id'];
				$adsArray[$i]['url'] = $row['url'];
				$adsArray[$i]['type'] = $row['type'];
				$adsArray[$i]['value'] = $row['value'];
				$i++;
			}
			
			return($adsArray);
		}
		
		/*
		*	Get Cold Call Info from id
		*/
		public function getColdCallLead($search_id) {
			$lead_info = array();
			$i = 0;
			$infoString = "SELECT * FROM coldcall_leads WHERE id = :id_search";
			$rs = $this->connect->prepare($infoString);
			$rs->bindParam(':id_search', $search_id);
			$rs->execute();
			while($row = $rs->fetch()) {
				$lead_info[$i]['surname'] = $row['surname'];
				$lead_info[$i]['customer_name'] = $row['customer_name'];
				$lead_info[$i]['phone'] = $row['phone'];
				$lead_info[$i]['company'] = $row['company'];
				$lead_info[$i]['website'] = $row['website'];
				$lead_info[$i]['location'] = $row['location'];
				$lead_info[$i]['called'] = $row['called'];
				$lead_info[$i]['nocall_list'] = $row['nocall_list'];
				$i++;
			}
			return $lead_info;
		}
		
		/*
		*	Get Cold Call list
		*/
		public function getColdCallList() {
			$call_list = array();
			$i = 0;
			$callString = "SELECT * FROM coldcall_leads WHERE nocall_list = 0 AND called = 0";
			$rs = $this->connect->prepare($callString);
			$rs->execute();
			while($row = $rs->fetch()) {
				$call_list[$i]['id'] = $row['id'];
				$call_list[$i]['surname'] = $row['surname'];
				$call_list[$i]['customer_name'] = $row['customer_name'];
				$call_list[$i]['phone'] = $row['phone'];
				$call_list[$i]['company'] = $row['company'];
				$call_list[$i]['website'] = $row['website'];
				$call_list[$i]['location'] = $row['location'];
				$i++;
			}
			return $call_list;
		}
		
		/*
		*	Gets all missions that have been complete, approved, and uploaded by the Ruzzed team
		*/
		public function getCompletedMissions() {
			$i = 0;
			$winnersArray = array();
			$string = "SELECT mw.user_id, mw.mission_id, mw.youtube_id, nm.title FROM mission_winners mw, missions nm WHERE mw.mission_id = nm.id";
			$rs = $this->connect->prepare($string);
			$rs->execute();
			while($row = $rs->fetch()) {
				$winnersArray[$i]['user_id'] = $row['user_id'];
				$winnersArray[$i]['mission_id'] = $row['mission_id'];
				$winnersArray[$i]['youtube_id'] = $row['youtube_id'];
				$winnersArray[$i]['title'] = $row['title'];
				$i++;
			}
			
			return($winnersArray);
		}
		
		/*
		*	Gets all open missions users can complete
		*/
		public function getOpenMissions() {
			$missions = array();
			$i = 0;
			$openMissions = "SELECT nm.id, nm.title, nm.summary, nm.current_wage FROM missions nm, open_missions om WHERE om.mission_id = nm.id AND om.open = 1";
			$rs = $this->connect->prepare($openMissions);
			$rs->execute();
			while($row = $rs->fetch()) {
				$missions[$i]['id']    = $row['id'];
				$missions[$i]['title'] = $row['title'];
				$missions[$i]['summary'] = $row['summary'];
				$missions[$i]['wage'] = $row['current_wage'];
				$i++;
			}
			
			return $missions;
		}
		
		/*
		*	Get redeem Value
		*/
		public function getRedeemValue() {
			$qString = "SELECT redeem FROM token_value LIMIT 1";
			$rs = $this->connect->prepare($qString);
			$rs->execute();
			$row = $rs->fetch();
			return $row['redeem'];
		}
		
		/*
		*	Get Refresh Token
		*/
		public function getRefreshToken() {
			$refresh_token = '';
			$refresh_string = "SELECT refresh_token FROM access_tokens";
			$rs = $this->connect->prepare($refresh_string);
			$rs->execute();
			while($row = $rs->fetch()) {
				$refresh_token = $row['refresh_token'];
			}
			return $refresh_token;
		}
		
		/*
		*	Get Google Access Token
		*/
		public function getgoogleAccessToken() {
			$refresh_token = '';
			$refresh_string = "SELECT access_token FROM access_tokens";
			$rs = $this->connect->prepare($refresh_string);
			$rs->execute();
			while($row = $rs->fetch()) {
				$refresh_token = $row['access_token'];
			}
			return $refresh_token;
		}
		
		/*
		*	Get cold call signups
		*/
		public function getSignedUpCustomers() {
			$signedup = array();
			$i = 0;
			$rsString = "SELECT id, surname, customer_name, phone, company, website, location FROM coldcall_leads WHERE signed_up = 1";
			$rs = $this->connect->prepare($rsString);
			$rs->execute();
			while($row = $rs->fetch()) {
				$signedup[$i]['id'] = $row['id'];
				$signedup[$i]['surname'] = $row['surname'];
				$signedup[$i]['customer_name'] = $row['customer_name'];
				$signedup[$i]['phone'] = $row['phone'];
				$signedup[$i]['company'] = $row['company'];
				$signedup[$i]['website'] = $row['website'];
				$signedup[$i]['location'] = $row['location'];
			}
			return $signedup;
		}
		
		/*
		*  Get Info for a single video
		*/
		public function getSingleVideoInfo($video_id) {
			$vid_info = array();
			$qString = "SELECT m.title, m.summary, m.current_wage, u.username FROM mission_winners mw, missions m, users u WHERE mw.youtube_id = :video_id AND mw.mission_id = m.id AND mw.user_id = u.id";
			$rs = $this->connect->prepare($qString);
			$rs->bindParam(':video_id', $video_id);
			$rs->execute();
			while($row = $rs->fetch()) {
				$vid_info['username'] = $row['username'];
				$vid_info['wage'] = $row['current_wage'];
				$vid_info['summary'] = $row['summary'];
				$vid_info['title'] = $row['title'];
			}
			return $vid_info;
		}
		
		/*
		*	Get Submission of a Mission
		*/
		public function getSubmissionVideo($vote_id) {
			$qString = "SELECT id, mission_id, email, tokens FROM mission_submissions WHERE vote_id = :voteId";
			$rs = $this->connect->prepare($qString);
			$rs->bindParam(':voteId', $vote_id);
			$rs->execute();
			$row = $rs->fetch();
			return $row;
		}
		
		/*
		*	Gets Amount of tokens User has
		*/
		public function getTokenAmount($uId) {
			$qString = "SELECT token_amount FROM user_tokens WHERE user_id = :uId";
			$rs = $this->connect->prepare($qString);
			$rs->bindParam(':uId', $uId);
			$rs->execute();
			$row = $rs->fetch();
			return $row['token_amount'];
		}
		
		/*
		*	Get All users who would like to redeem their tokens
		*/
		public function getUnpaidRedeemLog() {
			$redeemArray = array();
			$i = 0;
			$qString = "SELECT rl.id AS rid, rl.amount, u.email, u.id FROM redeem_log rl, users u WHERE rl.user_id = u.id AND rl.paid = 0";
			$rs = $this->connect->prepare($qString);
			$rs->execute();
			while($row = $rs->fetch()) {
				$redeemArray[$i]['amount'] = $row['amount'];
				$redeemArray[$i]['email'] = $row['email'];
				$redeemArray[$i]['id'] = $row['id'];
				$redeemArray[$i]['redeemID'] = $row['rid'];
				$i++;
			}
			
			return $redeemArray;
			
		}
		
		/*
		*	Gets all open missions users can complete
		*/
		public function getVotingMissions() {
			$missions = array();
			$i = 0;
			$openMissions = "SELECT nm.id, nm.title, nm.summary, nm.current_wage FROM missions nm, open_missions om WHERE om.mission_id = nm.id AND om.in_voting = 1";
			$rs = $this->connect->prepare($openMissions);
			$rs->execute();
			while($row = $rs->fetch()) {
				$missions[$i]['id']    = $row['id'];
				$missions[$i]['title'] = $row['title'];
				$missions[$i]['summary'] = $row['summary'];
				$missions[$i]['wage'] = $row['current_wage'];
				$i++;
			}
			
			return $missions;
		}
		
		/*
		*	Give tokens to user in database
		*/
		public function awardTokens($tokens_to_add, $user_id, $ad_id) {
			$i = 0;
			$selectQueryString = "SELECT token_amount FROM user_tokens WHERE user_id=:user_id";
			$rs = $this->connect->prepare($selectQueryString);
			$rs->bindParam(':user_id', $user_id);
			$rs->execute();
			if($row = $rs->fetch()) {
				$token_amount = $row['token_amount'];
				$query_string = "UPDATE user_tokens SET token_amount=:token_amount WHERE user_id=:user_id";
			} else {
				$token_amount = 0;
				$query_string = "INSERT INTO user_tokens (token_amount, user_id) VALUES (:token_amount, :user_id)";
			}
			$token_amount += $tokens_to_add;
			$rs_ii = $this->connect->prepare($query_string);
			$rs_ii->bindParam(':token_amount', $token_amount);
			$rs_ii->bindParam(':user_id', $user_id);
			if($rs_ii->execute() && $ad_id != -1) {
				$update_ads = "UPDATE ads SET used = (used + 1) WHERE id=:ad_id";
				$rs_u = $this->connect->prepare($update_ads);
				$rs_u->bindParam(':ad_id', $ad_id);
				$rs_u->execute();
				return true;
			} else {
				return false;
			}
		}
		
		/*
		*	User Redeems All Tokens
		*/
		public function redeemTokens($userId)
		{
			//get token exchange rate
			$exchangeRate = $this->getRedeemValue();
			
			//get amount of tokens user has
			$selectTokens = "SELECT token_amount FROM user_tokens WHERE user_id = :userId";
			$rs = $this->connect->prepare($selectTokens);
			$rs->bindParam(':userId', $userId);
			$rs->execute();
			$row = $rs->fetch();
			$tokenAmount = $row['token_amount'];
			$userTokenNum = $tokenAmount;
			
			//check if user has made at least 1 cent
			if(($exchangeRate * $tokenAmount) <= 0.01)
			{
				return "in_funds";
			}
			
			//change the token amount into the cost
			$tokenAmount *= $exchangeRate;
			
			//add redeem to log of redeemed tokens
			$addRedeemLog = "INSERT INTO redeem_log (user_id, amount, paid) VALUES (:userId, :tokenAmount, 0)";
			$rs2 = $this->connect->prepare($addRedeemLog);
			$rs2->bindParam(':userId', $userId);
			$rs2->bindParam(':tokenAmount', $tokenAmount);
			if($rs2->execute())
			{
				$this->subtractTokensFromUser($userId, $userTokenNum);
				return true;
			} else {
				return false;
			}
		}
		
		/*
		*	User Redeems All Tokens
		*/
		public function redeemSomeTokens($userId, $tokenNum)
		{
			//get token exchange rate
			$exchangeRate = $this->getRedeemValue();
			
			//get amount of tokens user has
			$selectTokens = "SELECT token_amount FROM user_tokens WHERE user_id = :userId";
			$rs = $this->connect->prepare($selectTokens);
			$rs->bindParam(':userId', $userId);
			$rs->execute();
			$row = $rs->fetch();
			$tokenAmount = $row['token_amount'];
			
			//check if user has made at least 1 cent
			if(($exchangeRate * $tokenNum) <= 0.01 || $tokenNum > $tokenAmount)
			{
				return "in_funds";
			}
			
			//change the token amount into the cost
			$tokenAmount = $tokenNum * $exchangeRate;
			
			//add redeem to log of redeemed tokens
			$addRedeemLog = "INSERT INTO redeem_log (user_id, amount, paid) VALUES (:userId, :tokenAmount, 0)";
			$rs2 = $this->connect->prepare($addRedeemLog);
			$rs2->bindParam(':userId', $userId);
			$rs2->bindParam(':tokenAmount', $tokenAmount);
			if($rs2->execute())
			{
				$this->subtractTokensFromUser($userId, $tokenNum);
				return true;
			} else {
				return false;
			}
		}
		
		/*
		*	Redeems All Tokens Without Being Logged In
		*/
		public function redeemTokensOut($tokenAmount, $email)
		{
			//get token exchange rate
			$exchangeRate = $this->getRedeemValue();
			
			//check if user has made at least 1 cent
			if(($exchangeRate * $tokenAmount) <= 0.01)
			{
				return "in_funds";
			}
			
			//change the token amount into the cost
			$tokenAmount *= $exchangeRate;
			
			//add redeem to log of redeemed tokens
			$addRedeemLog = "INSERT INTO redeem_out_log (email, amount, paid) VALUES (:email, :tokenAmount, 0)";
			$rs2 = $this->connect->prepare($addRedeemLog);
			$rs2->bindParam(':email', $email);
			$rs2->bindParam(':tokenAmount', $tokenAmount);
			if($rs2->execute())
			{
				return true;
			} else {
				return false;
			}
		}
		
		/*
		*	Search for person in cold call list
		*/
		public function searchColdCallList($field, $search) {
			$search_ids = array();
			$i = 0;
			switch($field) {
				case 'all':
					$searchString = "SELECT id FROM coldcall_leads WHERE surname='".$search['surname']."' OR customer_name='".$search['customer_name']."' OR phone='".$search['phone']."' OR company='".$search['company']."' OR website='".$search['website']."' OR location='".$search['location']."'";
				break;
				case 'phone':
					$searchString = "SELECT id FROM coldcall_leads WHERE phone='".$search['phone']."'";
				break;
				default:
					return 'No field selected';
				break;
			}
			
			$searchQuery = $this->connect->prepare($searchString);
			$searchQuery->execute();
			while($row = $searchQuery->fetch()) {
				$search_ids[$i] = $row['id'];
				$i++;
			}
			return $search_ids;
		}
		
		/*
		*	Subtract Tokens from a user
		*/
		public function subtractTokensFromUser($user_id, $sub_num) {
			$string = "UPDATE user_tokens SET token_amount = token_amount - :sub_num WHERE user_id = :user_id";
			$rs = $this->connect->prepare($string);
			$rs->bindParam(':sub_num', $sub_num);
			$rs->bindParam(':user_id', $user_id);
			if($rs->execute()) {
				return true;
			} else {
				return false;
			}
		}
		
		/*
		*	Suggest a Mission for 10 or more Tokens
		*/
		public function suggestMission($missionTitle, $missionSummary, $startingWage, $user_id)
		{
			//get info about tokens
			$getTokenId = "SELECT token_amount FROM user_tokens WHERE  user_id = :user_id";
			$getTokenQuery = $this->connect->prepare($getTokenId);
			$getTokenQuery->bindParam(':user_id', $user_id);
			$getTokenQuery->execute();
			while($row = $getTokenQuery->fetch())
			{
				$tokenAmount = $row['token_amount'];
			}
			//echo 'The user id is:'.$user_id.'<br />';
			//echo 'The token amount is:'.$tokenAmount.'<br />';
			//echo 'The starting wage is:'.$startingWage.'<br />';
			if($tokenAmount >= $startingWage)
			{
				//echo 'Should be inserted';
				$addRequestString = "INSERT INTO missions (title, summary, starting_wage, current_wage) VALUES (:missionTitle, :missionSummary, :startingWage, :currentWage)";
				$addRequestQuery = $this->connect->prepare($addRequestString);
				$addRequestQuery->bindParam(':missionTitle', $missionTitle);
				$addRequestQuery->bindParam(':missionSummary', $missionSummary);
				$addRequestQuery->bindParam(':startingWage', $startingWage);
				$addRequestQuery->bindParam(':currentWage', $startingWage);
				if($addRequestQuery->execute()) {
					$mission_id = $this->connect->lastInsertId();
					$open = 0;
					$in_voting = 1;
					$open_missions = "INSERT INTO open_missions (mission_id, open, in_voting) VALUES (:mission_id, :open, :in_voting)";
					$query = $this->connect->prepare($open_missions);
					$query->bindParam(':mission_id', $mission_id);
					$query->bindParam(':open', $open);
					$query->bindParam(':in_voting', $in_voting);
					$query->execute();
					//subtract tokens from user
					$this->subtractTokensFromUser($user_id, $startingWage);
					return true;
				} else {
					return false;
				}
			}		
		}
		
		/*
		*	Update that a lead has been called
		*/
		public function updateCalledLead($called_id, $type) {
			switch($type) {
				case 'no':
					$calledString = "UPDATE coldcall_leads SET called = 1 WHERE id = :called_id";
				break;
				case 'maybe':
					$calledString = "UPDATE coldcall_leads SET called = 1, callback = 1 WHERE id = :called_id";
				break;
				case 'yes':
					$calledString = "UPDATE coldcall_leads SET called = 1, signed_up = 1 WHERE id = :called_id";
				break;
			}
			
			$rs = $this->connect->prepare($calledString);
			$rs->bindParam(':called_id', $called_id);
			if($rs->execute()) {
				return true;
			} else {
				return false;
			}
		}
		
		/*
		*	Change the option for videos that users can post to
		*/
		public function updateWinningIdea($winningIdea)
		{
			//disable all old winners
			$noWinnerString = "UPDATE open_missions SET open = 0";
			$noWinnerQuery = $this->connect->prepare($noWinnerString);
			//update new winners
			$successful = false;
			if($noWinnerQuery->execute())
			{
				echo "here";
				$countWinners = count($winningIdea);
				for($i = 0; $i < $countWinners; $i++)
				{
					$winnerId = $winningIdea[$i];
					$updateWinnerString = "UPDATE open_missions SET open=1, in_voting=0 WHERE mission_id=:winnerId";
					$updateWinnerQuery = $this->connect->prepare($updateWinnerString);
					$updateWinnerQuery->bindParam(':winnerId', $winnerId);
					if($updateWinnerQuery->execute())
					{
						$successful = true;
					} else {
						$successful = false;
					}
				}
			}
			return $successful;
		}
		
		/*
		*	Allows the User to loggin 
		*/
		public function userLoginCheck($email, $password)
		{
			$i = 0;
			$mdPass = md5($password);
			$loginString = "SELECT * FROM users WHERE email=:email AND password=:mdPass";
			$loginQuery = $this->connect->prepare($loginString);
			$loginQuery->bindParam(':email', $email);
			$loginQuery->bindParam(':mdPass', $mdPass);
			$loginQuery->execute();
			while($row = $loginQuery->fetch())
			{
				$user = $row['firstname'];
 				$pass = $row['password'];
 				$id = $row['id'];
 				$type = $row['type'];
 				$_SESSION['user'] = new User($user, $pass, $id, $type);
				return true;
			} 
			
			return false;	
		}
		
		/*
		*
		*/
		public function updateUnpaidRedeemLog($paidRedeems) {
			$i = 0;
			$numRedeems = count($paidRedeems);
			$qString = "UPDATE redeem_log SET paid = 1 WHERE 1 = 1";
			for($i = 0; $i < $numRedeems; $i++) {
				if($i == 0) { 
					$qString .= " AND ";
				} else {
					$qString .= " OR ";
				}
				$qString .= " id = ".$paidRedeems[$i];
			}
			//echo $qString;
			$rs = $this->connect->prepare($qString);
			if($rs->execute()) {
				return true;
			} else {
				return false;
			}
		}
	}	

$db = new DatabaseConnect('mysql:host=localhost;dbname=ruzzedco_site', 'ruzzedco', 'PJ6|9AgB$GUza3OZ9vp&#Q$p;');

?>