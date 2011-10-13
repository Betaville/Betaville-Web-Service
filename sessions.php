<?php
	function createToken($username){
		session_start();
		
		// If the tokens array does not already exist, create it
		if(!isset($_SESSION['tokens'])){
			$tokensArray = array();
			$_SESSION['tokens'] = $tokensArray;
		}
		
		// create the hash that we will use and check that it is unique
		$hash = SHA1($this->createSalt().$username);
		while($this->checkForTokenMatch($hash)){
			$hash = SHA1($this->createSalt().$username);
		}
		
		// we now have a unique hash, let's create the token
		$newToken = array('hash'=>$hash, 'username'=>$username, 'time'=>time());
		$_SESSION['tokens'][] = $newToken;
		
		return $hash;
	}
	
	function checkForTokenMatch($hash){
		session_start();
		
		foreach($_SESSION['tokens'] as &$token){
				if($token['hash'] == $hash) return true;
		}
		
		return false;
	}
	
	function authorizeWithToken($token){
		session_start();
		
		foreach($_SESSION['tokens'] as &$t){
				if($token == $t['hash']){
					return $t['username'];
				}
		}
		
		return null;
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