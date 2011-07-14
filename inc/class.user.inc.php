<?php
class UserActions{
	private $_db;
	
	public function __construct($db=null){
		include_once "db_config.php";
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
				$_SESSION['username'] = $row['username'];
				$_SESSION['LoggedIn']=1;
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