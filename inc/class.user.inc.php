<?php
/**  
 *  Betaville Web Service - A service for accessing data from a Betaville server via HTTP requests
 *  Copyright (C) 2011 Skye Book <skye.book@gmail.com>
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
 
class UserActions{
	private $_db;
	
	public function __construct($db=null){
		include_once "config.php";
		include_once "class_names.php";
		include_once "db_constants.php";
		
		if(is_object($db)){
			$this->_db=$db;
		}
		else{
			$dsn = "mysql:host=".DB_HOST.";dbname=".DB_NAME;
			$this->_db = new PDO($dsn, DB_USER, DB_PASS);
		}
	}
	
	public function addUser($username, $password, $emailAddress){
		if($this->isEmailAddressInUse($emailAddress)) return -3;
		else if(!($this->isValidUsername($username))) return -4;
		else{
			$sql = "INSERT INTO user (username, strongpass, strongsalt, email) VALUES (:username, :strongpass, :strongsalt, :email)";
			
			try{
				$stmt = $this->_db->prepare($sql);
				
				
				$salt = $this->createSalt();
				$generatedHash=$salt.$password;
				for($i=0; $i<1000; $i++){
					$generatedHash = SHA1($generatedHash);
				}
				
				$stmt->bindParam(":username", $username, PDO::PARAM_STR);
				$stmt->bindParam(":strongpass", $generatedHash, PDO::PARAM_STR);
				$stmt->bindParam(":strongsalt", $salt, PDO::PARAM_STR);
				$stmt->bindParam(":email", $emailAddress, PDO::PARAM_STR);
				
				$stmt->execute();
				
				return true;
				
				
			}catch(PDOException $e){
				return false;
			}
		}
	}
	
	private function createToken($username){
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
	
	private function checkForTokenMatch($hash){
		foreach($_SESSION['tokens'] as &$token){
				if($token['hash'] == $hash) return true;
		}
		
		return false;
	}
	
	public function getPublicInfo($user){
		$sql = "SELECT * FROM user WHERE username LIKE :user";
			
			try{
				$stmt = $this->_db->prepare($sql);
				$stmt->bindParam(":user", $user, PDO::PARAM_STR);
				$stmt->execute();
				
				if($row=$stmt->fetch()){
					return array(USER_NAME=>$row[USER_NAME],USER_DISPLAY_NAME=>$row[USER_DISPLAY_NAME],USER_BIO=>$row[USER_BIO],
					USER_WEBSITE=>$row[USER_WEBSITE],USER_TYPE=>$row[USER_TYPE]);
				}
				
				
			}catch(PDOException $e){
				return false;
			}
	}
	
	public function login($username, $password){
		$hashSQL = "SELECT username, strongpass, strongsalt from user where username=:user LIMIT 1";
		try{
			$stmt = $this->_db->prepare($hashSQL);
			$stmt->bindParam(":user", $username, PDO::PARAM_STR);
			$stmt->execute();
			$row=$stmt->fetch();

			$generatedHash=$row[USER_STRONG_SALT].$password;
			for($i=0; $i<1000; $i++){
				$generatedHash = SHA1($generatedHash);
			}
			
			if($generatedHash==$row[USER_STRONG_PASS]){
				$stmt->closeCursor();
				//$_SESSION['username'] = $row['username'];
				//$_SESSION['LoggedIn']=1;
				return array('authenticationSuccess'=>true, 'token'=>$this->createToken($username));
			}
			else{
				$stmt->closeCursor();
				return array('authenticationSuccess'=>false);
			}
		}catch(PDOException $e){
			return false;
		}
	}
	
	private function createSalt(){
		$salt = "";
		
		for($i=0; $i<10; $i++){
			$random = rand(0, 9);
			$salt = $salt.$random;
		}
		
		return $salt;
	}
	
	public function isValidUsername(){
		return true;
	}
	
	public function isEmailAddressInUse($emailAddress){
		$userSQL = "SELECT email FROM user where email=:emailAddress  LIMIT 1";
		try{
			$stmt = $this->_db->prepare($userSQL);
			$stmt->bindParam(":emailAddress", $emailAddress, PDO::PARAM_STR);
			$stmt->execute();
			$row=$stmt->fetch();
			if($row['email']==$emailAddress){
				$stmt->closeCursor();
				return true;
			}
			else{
				$stmt->closeCursor();
				return false;
			}
		}catch(PDOException $e){
			return false;
		}
	}
	
	public function isUsernameAvailable($username){
		$userSQL = "SELECT username FROM user where username=:user  LIMIT 1";
		try{
			$stmt = $this->_db->prepare($userSQL);
			$stmt->bindParam(":user", $username, PDO::PARAM_STR);
			$stmt->execute();
			$row=$stmt->fetch();
			if($row['username']==$username){
				$stmt->closeCursor();
				return false;
			}
			else{
				$stmt->closeCursor();
				return true;
			}
		}catch(PDOException $e){
			return false;
		}
	}
}
?>