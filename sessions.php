<?php

	$sessionTTL = 60;

	if(!isset($_SESSION['sessionDB'])){
		include_once "config.php";
		include_once "class_names.php";
		include_once "db_constants.php";
		$dsn = "mysql:host=".DB_HOST.";dbname=".DB_NAME;
		$sessionDB = new PDO($dsn, DB_USER, DB_PASS);
		$_SESSION['sessionDB'] = $sessionDB;
	}


	function createToken($username){
		session_start();
		
		// create the hash that we will use and check that it is unique
		$hash = SHA1(createSalt().$username);
		while(checkForTokenMatch($hash)){
			$hash = SHA1(createSalt().$username);
		}
		
		$sql = 'INSERT INTO live_sessions (session_token, user, session_start, last_touched) VALUES (:token, :user, NOW(), NOW())';
		try{
			$stmt = $_SESSION['sessionDB']->prepare($sql);
			$stmt->bindParam(":token", $hash, PDO::PARAM_STR);
			$stmt->bindParam(":user", $username, PDO::PARAM_STR);
			$stmt->execute();
		}catch(PDOException $e){
			return false;
		}
		
		return $hash;
	}
	
	function checkForTokenMatch($hash){
		$sql = 'SELECT session_token FROM live_sessions WHERE session_token=:token';
		try{
			$stmt = $_SESSION['sessionDB']->prepare($sql);
			$stmt->bindParam(":token", $hash, PDO::PARAM_STR);
			$stmt->execute();
			if($row=$stmt->fetch()){
				return true;
			}
			else{
				return false;
			}
		}catch(PDOException $e){
			return false;
		}
	}
	
	function authorizeWithToken($token){
		$sql = 'SELECT user FROM live_sessions WHERE session_token=:token AND NOW() < DATE_ADD(last_touched, INTERVAL :sessionTTL MINUTE)';
		try{
			$stmt = $_SESSION['sessionDB']->prepare($sql);
			$stmt->bindParam(":token", $hash, PDO::PARAM_STR);
			$stmt->bindParam(":sessionTTL", $sessionTTL, PDO::PARAM_INT);
			$stmt->execute();
			if($row=$stmt->fetch()){
				return $row['user'];
			}
			else{
				return false;
			}
		}catch(PDOException $e){
			return false;
		}
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