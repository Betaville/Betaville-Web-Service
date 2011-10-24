<?php
	function createToken($username){
		session_start();
		
		// create the hash that we will use and check that it is unique
		$hash = SHA1(createSalt().$username);
		while(checkForTokenMatch($hash)){
			$hash = SHA1(createSalt().$username);
		}
		
		$_SESSION[$hash] = $username;
		
		return $hash;
	}
	
	function checkForTokenMatch($hash){
		session_start();
		
		return isset($_SESSION[$hash]);
	}
	
	function authorizeWithToken($token){
		session_start();
		
		echo "size".sizeof($_SESSION);
		
		return $_SESSION[$token];
	}
	
	function createSalt(){
		$salt = "";
		
		for($i=0; $i<10; $i++){
			$random = rand(0, 9);
			$salt = $salt.$random;
		}
		
		return $salt;
	}
	
	
?>