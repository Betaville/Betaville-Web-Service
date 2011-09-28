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
		include_once "../Betaville-Web-Service/config.php";
		include_once "../Betaville-Web-Service/class_names.php";
		include_once "../Betaville-Web-Service/db_constants.php";
		
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
	
	public function login($username, $password, $encrypt = true ){
		$hashSQL = "SELECT username, strongpass, strongsalt from user where username=:user LIMIT 1";
		try{
			$stmt = $this->_db->prepare($hashSQL);
			$stmt->bindParam(":user", $username, PDO::PARAM_STR);
			$stmt->execute();
			$row=$stmt->fetch();
			if ( $encrypt ){
				$generatedHash=$row[USER_STRONG_SALT].$password;
				for($i=0; $i<1000; $i++){
					$generatedHash = SHA1($generatedHash);
				}
			}
			else 
				$generatedHash=$password;
			if($generatedHash==$row[USER_STRONG_PASS]){
				$stmt->closeCursor();
				if ( $encrypt ) 
					$this->_setSession($row,false);
				else 
					$this->_setSession($row);
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
	private function _setSession ($values, $storeSession = true ) {
		session_start();
		$_SESSION['uid'] = sha1($this->createSalt());
		//$_SESSION['uid'] = the database value
		$_SESSION['username'] = htmlspecialchars($values['username']);
		$_SESSION['hashpass'] = $values[USER_STRONG_PASS];
		$_SESSION['logged'] = true;
		if ( !$storeSession )  {
			//setcookie("user", htmlspecialchars($values['username']),time()+3600, "/", "localhost" );
			//setcookie("pass", $values[USER_STRONG_PASS],time()+3600, "/", "localhost");
		}
	}
	public function _logout(){
		session_start();
		session_unset();
		session_destroy();
		//setcookie("user", "", time()-3601, "/", "localhost");
		//setcookie("pass", "", time()-3601, "/", "localhost");
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