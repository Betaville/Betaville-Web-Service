<?
	public function passwordChangeNew($username, $password) {						
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
		catch(PDOException $e){
			return false;
		}
	}


	else if($request=='changePasswordNew'){			
		$username = $_GET['username'];
		$password= $_GET['password'];
		$response = $userActions->passwordChangeNew($username, $password);
		header('Content-Type: application/json');
		echo json_encode(array('PassChangedNew'=>$response));
	}