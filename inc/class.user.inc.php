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
 


	class UserActions{
		private $_db;
		// private $selectFromWhat = "design LEFT JOIN modeldesign ON design.designID=modeldesign.designid LEFT JOIN audiodesign ON design.designID=audiodesign.designid LEFT JOIN videodesign ON design.designID=videodesign.designid LEFT JOIN sketchdesign ON design.designID=sketchdesign.designid";//GROUP BY design.designID
		
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
		
	public function passwordChange($code,$password) {
		$sql = "UPDATE user SET strongpass=:strongpass, strongsalt=:strongsalt, confirmcode=:newCode WHERE confirmcode=:codeVeri";
		$set="";
		try{
			$stmt = $this->_db->prepare($sql);
			$salt = $this->createSalt();
			$generatedHash=$salt.$password;
			for($i=0; $i<1000; $i++){
				$generatedHash = SHA1($generatedHash);
			}
			//Set confirm code back to "" if it matches and generate the new password
			$stmt->bindParam(":strongpass", $generatedHash, PDO::PARAM_STR);
			$stmt->bindParam(":strongsalt", $salt, PDO::PARAM_STR);
			$stmt->bindParam(":newCode", $set, PDO::PARAM_STR);
			$stmt->bindParam(":codeVeri",$code, PDO::PARAM_STR);
			$stmt->execute();
			return true;
		}
		catch(PDOException $e){return false;}	
	
	}
	
	public function passwordChangeNew($username, $password) {						
		if(!($this->isUserActivated($username))) {
			return $username." has not been activated";			
		}
		else {
			$sql = "UPDATE user SET strongpass=:strongpass, strongsalt=:strongsalt WHERE userName=:username";		
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

				$stmt->execute();
				return true;
			} 
			catch(PDOException $e){return false;}
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
		$message .= "<a href='robert.betaville.net?section=user&request=activateuser&code=".$confirmCode."'> Please click on this link to activate your account now </a> <br />";
			return @mail($to,$subject,$message,$headers);
		}
	
	
	
	private function sendUpdatePassMail($emailAddress,$username,$confirmcode,$sendUserTo) {
			if(!($this->isUserActivated($username))) {
				return "you have not been activated";
				}
			else {
				$to = $emailAddress;
				$subject = $username."'s ";
				$subject .= "Password change request";
				$eol = "\n";
				$headers = 'From: Betaville <donotreply@betaville.net>'. $eol;
				//$headers .= "Reply-To: Please don't reply to this email".$eol;
				//$headers .= "Message-ID:< TheSystem@" . $_SERVER['SERVER_NAME'].">".$eol;
				$headers .= "X-Mailer: PHP v" .phpversion() . $eol;
				$message = "Hello " . $username . ", <br />";
				$message .= "Password update request <br />";
				$message .= "<a href=".$sendUserTo.$confirmcode."> Please click on this link to change your password </a> <br />";
				return @mail($to,$subject,$message,$headers);
				
			}
	}
	
	
	//Change the code for the given email address, check if address is in use, if yes check if user is activated, if yes go thru, else fail
	public function changeCode($emailAddress,$sendUserTo) {
				if(($this->isEmailAddressInUse($emailAddress))) {
					$checkUser = "SELECT userName FROM user WHERE email=:email";
					try {
						$stmt = $this->_db->prepare($checkUser);
						$stmt->bindParam(":email", $emailAddress, PDO::PARAM_STR);
						$stmt->execute();
						$row=$stmt->fetch();
						$username=$row['userName'];
							if(isset($username)){
								if(!($this->isUserActivated($username))) return "false";
								
							}
							else{
								$stmt->closeCursor();
								return false;
							}
						}
						catch(PDOException $e) {
							return false;
						}
						
				}
				else {
					return false;				
				}
				//generate a new random code and add it to the database
				$confirmCode = md5(uniqid(rand()));
				$sql = "UPDATE user SET confirmcode=:code WHERE email=:email";
				try{
					$stmt = $this->_db->prepare($sql);
					$stmt->bindParam(":email", $emailAddress, PDO::PARAM_STR);
					$stmt->bindParam(":code", $confirmCode, PDO::PARAM_STR);
					$stmt->execute();
					if($this->sendUpdatePassMail($emailAddress, $username, $confirmCode,$sendUserTo)){
						return true;
					}
					else{
						return false;
					}
					
				}catch(PDOException $e){
					return false;
				}
				
	}
		
	
	//Service request to check if the code exists
	public function checkCode($confirmCode) {
					$codeCheck= "SELECT confirmcode FROM user WHERE confirmcode=:code";
					try{
						$stmt = $this->_db->prepare($codeCheck);
						$stmt->bindParam(":code", $confirmCode, PDO::PARAM_STR);
						$stmt->execute();
						$row=$stmt->fetch();
						$trueCheck=$row['confirmcode'];
							if(isset($trueCheck)){
								return true;
							}
							else{
								return false;
							}
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
	
	
	private function checkForTokenMatch($hash){
		foreach($_SESSION['tokens'] as &$token){
				if($token['hash'] == $hash) return true;
			}
			
			return false;
		}
		
	public function searchForUser($user){
		$userString = "%".$user."%";
		$sql = "SELECT userName from user WHERE  userName LIKE :user";
		try{
			$stmt = $this->_db->prepare($sql);
			$stmt->bindParam(":user", $userString, PDO::PARAM_STR);
			$stmt->execute();
			
			$users = array();
			
			while($row = $stmt->fetch()){
				$users[] = $row['userName'];
			}
			
			return $users;
		}catch(PDOException $e){
			return false;
		}
	}

	public function searchForUserInfo($user){
		$userString = "%".$user."%";
		$sql = "SELECT * from user WHERE userName LIKE :user";
		try{
			$stmt = $this->_db->prepare($sql);
			$stmt->bindParam(":user", $userString, PDO::PARAM_STR);
			$stmt->execute();
			
			$users = array();

			
			while($row = $stmt->fetch()){


				$sqlDesign = 'SELECT COUNT(*) AS DesignCount FROM '.DESIGN_TABLE.' WHERE '.DESIGN_TABLE.'.'.DESIGN_USER.' LIKE "'.$row[USER_NAME].'" AND '.DESIGN_TABLE.'.'.DESIGN_IS_ALIVE.'=1 AND '.DESIGN_TYPE.'!= "empty"';
				$stmtDesign = $this->_db->prepare($sqlDesign);
				$stmtDesign->execute();				
				// print_r($stmtDesign->fetch());
				$fetchArr = $stmtDesign->fetch();

				$count = count($fetchArr)>0 ? $fetchArr[DesignCount] : 0;

				$users[] = array(USER_NAME=>$row[USER_NAME],USER_TYPE=>$row[USER_TYPE], USER_DESIGN=>$count, USER_ACTIVATED=>$row[USER_ACTIVATED], USER_EMAIL=>$row[USER_EMAIL]);
			}
						
			return $users;

		}catch(PDOException $e){
			return false;
		}
	}


public function getPublicInfo($user){
		$sql = "SELECT * FROM user WHERE username LIKE :user";
			
			try{
				$stmt = $this->_db->prepare($sql);
				$stmt->bindParam(":user", $user, PDO::PARAM_STR);
				$stmt->execute();
				
				if($row=$stmt->fetch()){
					// get the user's avatar link:
					
					$gravatarLink = $this->get_gravatar($row[USER_EMAIL]);
					
					return array(USER_NAME=>$row[USER_NAME],USER_DISPLAY_NAME=>$row[USER_DISPLAY_NAME],USER_BIO=>$row[USER_BIO],
					USER_WEBSITE=>$row[USER_WEBSITE],USER_TYPE=>$row[USER_TYPE], "avatar"=>$gravatarLink);
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
	
	public function changeType($user, $type){
		$userSQL = "UPDATE user set type = :newType WHERE userName=:user";
		try{
			$stmt = $this->_db->prepare($userSQL);
			$stmt->bindParam(":newType", $type, PDO::PARAM_STR);
			$stmt->bindParam(":user", $user, PDO::PARAM_STR);
			$stmt->execute();
			$row=$stmt->fetch();
			return true;
		}catch(PDOException $e){
			return false;
		}
	}
	
	public function UpdatePass($user,$newPass){
		$sql = "UPDATE user SET strongpass=:strongpass, strongsalt=:strongsalt WHERE userName=:user";
			
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
	public function changePass($user, $oldPass, $newPass){
	
		if(!$this->authenticate($user, $oldPass)) return false;
	
		$sql = "UPDATE user SET strongpass=:strongpass, strongsalt=:strongsalt WHERE userName=:user";
			
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
				return false;
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
	
	public function activateUserByName($username){
		$codeSQL = "UPDATE user SET activated=1 where username=:user";
		try{
			$stmt = $this->_db->prepare($codeSQL);
			$stmt->bindParam(":user", $username, PDO::PARAM_STR);
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

	/**
 	* Get either a Gravatar URL or complete image tag for a specified email address.
 	* 
 	* @param string $email The email address
 	* @param string $s Size in pixels, defaults to 80px [ 1 - 512 ]
 	* @param string $d Default imageset to use [ 404 | mm | identicon | monsterid | wavatar ]
 	* @param string $r Maximum rating (inclusive) [ g | pg | r | x ]
 	* @param boole $img True to return a complete IMG tag False for just the URL
 	* @param array $atts Optional, additional key/value attributes to include in the IMG tag
 	* @return String containing either just a URL or a complete image tag
 	* @source http://gravatar.com/site/implement/images/php/
 	*/
	
	function get_gravatar( $email, $s = 80, $d = 'mm', $r = 'g', $img = false, $atts = array() ) {
		$url = 'http://www.gravatar.com/avatar/';
		$url .= md5( strtolower( trim( $email ) ) );
		$url .= "?s=$s&d=$d&r=$r";
		if ( $img ) {
			$url = '<img src="' . $url . '"';
			foreach ( $atts as $key => $val ){
				$url .= ' ' . $key . '="' . $val . '"';
			}
			$url .= ' />';
		}
	return $url;
	}
	
	public function deleteUserFromGroup($authorizedUser,$designID) {
			$sql = 'UPDATE '.PROPOSAL_TABLE.' SET user_group =:name WHERE destinationID = :designid AND type = "proposal"';
				try {
					$stmt = $this->_db->prepare($sql);
					$stmt->bindParam(":name", $authorizedUser, PDO::PARAM_STR);
					$stmt->bindParam(":designid", $designID, PDO::PARAM_STR);
					$stmt->execute();
					return true;
				}	
				catch(PDOException $e){
					return false;
				}
			
		
	}

	public function addUserToGroup($authorizedUser,$designID) {
			$sql = 'UPDATE '.PROPOSAL_TABLE.' SET user_group = CONCAT(user_group,"":name"") WHERE (destinationID =:designid AND type="proposal")';
				try {
					$stmt = $this->_db->prepare($sql);
					$stmt->bindParam(":name", $authorizedUser, PDO::PARAM_STR);
					$stmt->bindParam(":designid", $designID, PDO::PARAM_STR);
					$stmt->execute();
					return true;
				}	
				catch(PDOException $e){
					return false;
				}
			
		
	}
	
	public function getAllInGroup($designID) {
			$sql = 'SELECT user_group FROM '.PROPOSAL_TABLE.' WHERE '.PROPOSAL_DEST.' =:designid AND type ="proposal"';
				try {
					$stmt = $this->_db->prepare($sql);
					$stmt->bindParam(":designid", $designID, PDO::PARAM_STR);
					$stmt->execute();
					$users = array();
						while($row=$stmt->fetch()){
							$users[] = $row[PROPOSAL_PERMISSIONS_GROUP_ARRAY];
						}
						$usy = explode(',',$users[0]);
						array_pop($usy);
						array_shift($usy);
						if($usy) { 
						return $usy;
						}
						else {
						return null;
						}
				}	
				catch(PDOException $e){
					return false;
				}
	}

	public function getAllFave($designID) {
	$sql = 'SELECT '.DESIGN_FAVE_LIST.' FROM '.DESIGN_TABLE.' WHERE '.DESIGN_ID.'=:designid';
				try {
					$stmt = $this->_db->prepare($sql);
					$stmt->bindParam(":designid", $designID, PDO::PARAM_STR);				
					$stmt->execute();
					$users = array();
						while($row=$stmt->fetch()){
							$users[] = $row[DESIGN_FAVE_LIST];
						}
						$usy = explode(',',$users[0]);
						array_pop($usy);
						array_shift($usy);
						if($usy) { 
						return $usy;
						}
						else {
						return null;
						}
				}	
				catch(PDOException $e){
					return false;
				}
	}

	public function addUserToFave($name,$designID) {
			$name = $name.',';
			$sql = 'UPDATE '.DESIGN_TABLE.' SET favelist = CONCAT(favelist,"":name"") WHERE designID =:designid';
				try {
					$stmt = $this->_db->prepare($sql);
					$stmt->bindParam(":name", $name, PDO::PARAM_STR);
					$stmt->bindParam(":designid", $designID, PDO::PARAM_STR);
					$stmt->execute();
					return true;
				}	
				catch(PDOException $e){
					return false;
				}
			
		
	}

	public function deleteUserFromProposalGroup($listGroup,$designID) {
			$sql = 'UPDATE '.DESIGN_TABLE.' SET favelist =:listGroup WHERE designID =:designid';
				try {
					$stmt = $this->_db->prepare($sql);
					$stmt->bindParam(":listGroup", $listGroup, PDO::PARAM_STR);
					$stmt->bindParam(":designid", $designID, PDO::PARAM_STR);
					$stmt->execute();
					return true;
				}	
				catch(PDOException $e){
					return false;
				}
			
		
	}
	
	private function allUserInfo($row){
			return array(PROPOSAL_PERMISSIONS_GROUP_ARRAY=>$row[PROPOSAL_PERMISSIONS_GROUP_ARRAY]);
	}
}


?>
