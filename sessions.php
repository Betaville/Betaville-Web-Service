<?php
/**  
 *  Betaville Web Service - A service for accessing data from a Betaville server via HTTP requests
 *  Copyright (C) 2011-2012 Betaville
 *  
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *  
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *  
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
 
 	include_once "config.php";
 	$sessionTTL = BETAVILLE_SESSION_TTL;

	if(!isset($_SESSION['sessionDB'])){
		
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
		$sql = 'SELECT user FROM live_sessions WHERE session_token=:token';// AND NOW() < DATE_ADD(last_touched, INTERVAL :sessionTTL MINUTE)
		try{
			$stmt = $_SESSION['sessionDB']->prepare($sql);
			$stmt->bindParam(":token", $token, PDO::PARAM_STR);
			//$stmt->bindParam(":sessionTTL", $sessionTTL, PDO::PARAM_INT);
			$stmt->execute();
			if($row=$stmt->fetch()){
				updateLastTouched($token);
				pruneOldSessions();
				return $row['user'];
			}
			else{
				return false;
			}
		}catch(PDOException $e){
			return false;
		}
	}
	
	function updateLastTouched($token){
		$sql = 'UPDATE live_sessions SET last_touched = NOW() WHERE session_token=:token';
		try{
			$stmt = $_SESSION['sessionDB']->prepare($sql);
			$stmt->bindParam(":token", $token, PDO::PARAM_STR);
			$stmt->execute();
		}catch(PDOException $e){
			return false;
		}
	}
	
	function endSession($token){
		$sql = 'DELETE FROM live_sessions WHERE session_token=:token';
		try{
			$stmt = $_SESSION['sessionDB']->prepare($sql);
			$stmt->bindParam(":token", $token, PDO::PARAM_STR);
			$stmt->execute();
			return true;
		}catch(PDOException $e){
			return false;
		}
	}
	
	function pruneOldSessions(){
		$sql = "DELETE FROM live_sessions WHERE DATE_ADD(session_start, INTERVAL :ttl SECOND) < NOW()";
		try{
			$stmt = $_SESSION['sessionDB']->prepare($sql);
			$stmt->bindParam(":ttl", $sessionTTL, PDO::PARAM_INT);
			$stmt->execute();
			return true;
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