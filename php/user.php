<?php
	class User
	{
		private $username;
		private $loggedIn;
		private $userId;
		private $userType;
		private $address;
		
		public function __construct($user, $pass, $id, $usertype, $Address)
		{
			$this->username = $user;
			$this->userId = $id;
			$this->loggedIn = true;
			$this->userType = $usertype;
			$this->address = $Address;
		}
		
		public function getUserAddress() {
			return $this->address;
		}
		
		public function getUsername() {
			return $this->username;
		}
		
		public function getUserId() {
			return $this->userId;
		}
		
		public function getUserType() {
			return $this->userType;
		}
		
		public function setLoggedIn() {
			$this->loggedIn = true;
		}
		
		public function setLoggedOut() {
			$this->loggedIn = false;
		}
		
		public function sendConfirmationEmail($sender, $message, $reciever, $subject) {
			// The message after line \r\n

			// In case any of our lines are larger than 70 characters, we should use wordwrap()
			$message = wordwrap($message, 70, "\r\n");

			// Send
			mail($reciever, $subject, $message);
		}
		
		public function loggedIn() {
			
		}
	}
?>