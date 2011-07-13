<?php
class UserActions{
	private $_db;
	
	public function __construct($db==null){
		if(is_object($db)){
			$this->_db=$db;
		}
		else{
			$dsn = "mysql:host=".DB_HOST.";dbname=".DB_NAME;
			$this->_db = new PDO($dsn, DB_USER, DB_PASS);
		}
	}
	
	public function login($username, $password){
		$hashSQL = "SELECT email, passwordSalt, passwordHash from user where email=:user LIMIT 1";
		try{
			$stmt = $this->_db->prepare($hashSQL);
			$stmt->bindParam(":user", $username, PDO::PARAM_STR);
			$stmt->execute();
			$row=$stmt->fetch();

			$generatedHash=$row['passwordSalt'].$password;
			for($i=0; $i<1000; $i++){
				$generatedHash = SHA1($generatedHash);
			}

			if($generatedHash==$row['passwordHash']){
				$stmt->closeCursor();
				$_SESSION['email'] = $row['email'];
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
}
?>