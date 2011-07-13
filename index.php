<?php

echo "The Service";
echo "<br>";

include "db_config.php";
$username = "user123";
$password = "lol123";

echo "running test";
echo "<br>";

// do a test
$dsn = "mysql:host=".DB_HOST.";dbname=".DB_NAME;
$_db = new PDO($dsn, DB_USER, DB_PASS);
$hashSQL = "SELECT email, passwordSalt, passwordHash from user where email=:user LIMIT 1";
try{
$stmt = $_db->prepare($hashSQL);
$stmt->bindParam(":user", $username, PDO::PARAM_STR);
$stmt->execute();
$row=$stmt->fetch();

$generatedHash=$row['passwordSalt'].$password;
for($i=0; $i<1000; $i++){
	$generatedHash = SHA1($generatedHash);
}

if($generatedHash==$row['passwordHash']){
	$stmt->closeCursor();
	//$_SESSION['email'] = $row['email'];
	//$_SESSION['LoggedIn']=1;
	echo "success";
	echo "<br>";
}
else{
	$stmt->closeCursor();
	echo "failed";
	echo "<br>";
}
}catch(PDOException $e){
}

$info = array();
$info[] = array('user'=>$username);
$info[] = array('pass'=>$password);

header('Content-type: application/json');
echo json_encode(array('data'=>$info, 'data'=>$info));



?>