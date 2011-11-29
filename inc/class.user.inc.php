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
		if(!($this->isUsernameAvailable($username))) return "This username is already in use";
		if(filter_var($username, FILTER_VALIDATE_EMAIL)) return "This is not a valid email address";
		if($this->isEmailAddressInUse($emailAddress)) return "This email address is already in use";
		
		else if(!($this->isValidUsername($username))) return "This is not a valid username";
		else{
			$confirmCode = md5(uniqid(rand()));
			$sql = "INSERT INTO user (userName, strongpass, strongsalt, email, confirmcode) VALUES (:username, :strongpass, :strongsalt, :email, :code)";
			
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
				$stmt->bindParam(":code", $confirmCode, PDO::PARAM_STR);
				
				$stmt->execute();
				
				if($this->sendVerificationMail($emailAddress, $username, $confirmCode)){
					return true;
				}
				else{
					return false;
				}
				
			}catch(PDOException $e){
				return false;
			}
		}
	}
	
	private function sendVerificationMail($emailAddress, $username, $confirmCode){
		$from = "Quilvin@gmail.com";
		$to = $emailAddress;
		$subject = $username."'s ";
		$subject .= "Betaville Activation link";
		$eol = "\n";
		$headers = 'From: Betaville <donotreply@betaville.net>'. $eol;
		//$headers .= "Reply-To: Please don't reply to this email".$eol;
		//$headers .= "Message-ID:< TheSystem@" . $_SERVER['SERVER_NAME'].">".$eol;
		$headers .= "X-Mailer: PHP v" .phpversion() . $eol;
		$message = "Hello " . $username . ", <br />";
		$message .= "Thank you for signing up to Betaville. <br />";
		$message .= "<a href='".SERVICE_URL."?section=user&request=activateuser&code=".$confirmCode."> Please click on this link to activate your account now </a> <br />";
		return @mail($to,$subject,$message,$headers);
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
	
	private function authenticate($username, $password){
		$hashSQL = "SELECT username, strongpass, strongsalt, activated from user where username=:user LIMIT 1";
		try{
			$stmt = $this->_db->prepare($hashSQL);
			$stmt->bindParam(":user", $username, PDO::PARAM_STR);
			$stmt->execute();
			$row=$stmt->fetch();

			$generatedHash=$row[USER_STRONG_SALT].$password;
			for($i=0; $i<1000; $i++){
				$generatedHash = SHA1($generatedHash);
			}
			
			if($generatedHash==$row[USER_STRONG_PASS] && $row['activated']==1){
				$stmt->closeCursor();
				//$_SESSION['username'] = $row['username'];
				//$_SESSION['LoggedIn']=1;
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
	
	public function login($username, $password){
		$authResult = $this->authenticate($username, $password);
		if($authResult) return array('authenticationSuccess'=>true);
		else return array('authenticationSuccess'=>false);
	
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
				
			}
			else{
				$stmt->closeCursor();
				
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
	
	public function isValidUsername($username){
		return true;
	}
	
	public function getUserType($username){
		$sql = "SELECT ".USER_TYPE." FROM ".USER_TABLE." WHERE ".USER_NAME."=:user";
		try{
			$stmt = $this->_db->prepare($sql);
			$stmt->bindParam(":user", $username, PDO::PARAM_STR);
			$stmt->execute();
			$row = $stmt->fetch();
			return $row[USER_TYPE];
		}catch(PDOException $e){
			return false;
		}
	}
	
	public function changeBio($user, $bio){
		$userSQL = "UPDATE user set bio = :newBio WHERE userName=:user";
		try{
			$stmt = $this->_db->prepare($userSQL);
			$stmt->bindParam(":newBio", $bio, PDO::PARAM_STR);
			$stmt->bindParam(":user", $user, PDO::PARAM_STR);
			$stmt->execute();
			$row=$stmt->fetch();
			return true;
		}catch(PDOException $e){
			return false;
		}
	}
	
	//website
	public function changeWebsite($user, $website){
		$userSQL = "UPDATE user set website = :newWebsite WHERE userName=:user";
		try{
			$stmt = $this->_db->prepare($userSQL);
			$stmt->bindParam(":newWebsite", $website, PDO::PARAM_STR);
			$stmt->bindParam(":user", $user, PDO::PARAM_STR);
			$stmt->execute();
			$row=$stmt->fetch();
			return true;
		}catch(PDOException $e){
			return false;
		}
	}
	
	public function changePass($user, $oldPass, $newPass){
	
		if(!$this->authenticate($user, $oldPass)) return false;
	
		$sql = "UPDATE user SET strongpass=:strongpass AND strongsalt=:strongsalt WHERE userName=:user";
			
			try{
				$stmt = $this->_db->prepare($sql);
				
				
				$salt = $this->createSalt();
				$generatedHash=$salt.$newPass;
				for($i=0; $i<1000; $i++){
					$generatedHash = SHA1($generatedHash);
				}
				
				$stmt->bindParam(":user", $user, PDO::PARAM_STR);
				$stmt->bindParam(":strongpass", $generatedHash, PDO::PARAM_STR);
				$stmt->bindParam(":strongsalt", $salt, PDO::PARAM_STR);
				
				$stmt->execute();
				return true;
				
			}catch(PDOException $e){
				return false;
			}
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
		$userSQL = "SELECT userName FROM user where userName=:user LIMIT 1";
		try{
			$stmt = $this->_db->prepare($userSQL);
			$stmt->bindParam(":user", $username, PDO::PARAM_STR);
			$stmt->execute();
			$row=$stmt->fetch();
			if(isset($row['userName'])){
			
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
	
	public function isUserActivated($username){
		$userSQL = "SELECT activated FROM user where username=:user LIMIT 1";
		try{
			$stmt = $this->_db->prepare($userSQL);
			$stmt->bindParam(":user", $username, PDO::PARAM_STR);
			$stmt->execute();
			$row=$stmt->fetch();
			if($row['activated']==1){
				$stmt->closeCursor();
				return true;
			}
			else{
				$stmt->closeCursor();
				return true;
			}
		}catch(PDOException $e){
			return false;
		}
	}
	
	public function activateUser($sCode){
		$codeSQL = "UPDATE user SET activated=1 where confirmcode=:secretCode";
		try{
			$stmt = $this->_db->prepare($codeSQL);
			$stmt->bindParam(":secretCode", $sCode, PDO::PARAM_STR);
			$stmt->execute();
			$row=$stmt->fetch();
			if ($row == 1 ){
				$stmt->closeCursor();
				return true;
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