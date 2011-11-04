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

session_start();

if($_GET['gz']==1){
	// check if a zipped response is requested
	header('Content-Encoding: gzip');
	ob_start("ob_gzhandler");
}

if(isset($_GET['section']) && isset($_GET['request'])){
	include_once "sessions.php";
	//include_once "db_constants.php";

	// get the auth token if it exists
	$token = $_GET['token'];
	if(isset($_GET['token'])){
		$authorizedUser = authorizeWithToken($token);
	}

	$section = $_GET['section'];
	$request = $_GET['request'];
	
	if($section=='authcheck'){
		//echo $token;
		//echo $authorizedUser;
		//echo "size".sizeof($_SESSION);
		echo authorizeWithToken($token);
	}

	if($section=='user'){
		include_once "inc/class.user.inc.php";
		$userActions = new UserActions(null);
		if($request=='auth'){
			// NOTE: Same as startsession
			$response = $userActions->login($_GET['username'], $_GET['password']);
			if($response['authenticationSuccess']){
				$response['token']=createToken($_GET['username']);
				$response['size'] = sizeof($_SESSION);
			}
			header('Content-Type: application/json');
			echo json_encode($response);
		}
		else if($request=='startsession'){
			// NOTE: Same as auth
			$response = $userActions->login($_GET['username'], $_GET['password']);
			if($response['authenticationSuccess']){
				$response['token']=createToken($_GET['username']);
				$response['size'] = sizeof($_SESSION);
			}
			header('Content-Type: application/json');
			echo json_encode($response);
		}
		else if($request=='endsession'){
			header('Content-Type: application/json');
			echo json_encode(array('sessionended'=>endSession($token)));
		}
		else if($request=='add'){
			$response = $userActions->addUser($_GET['username'], $_GET['password'], $_GET['email']);
			header('Content-Type: application/json');
			echo json_encode(array('userAdded'=>$response));
		}
		else if($request=='activateuser'){
			$response = $userActions->activateUser($_GET['code']);
			header('Content-Type: application/json');
			echo json_encode(array('activationSuccess'=>$response));
		}
		else if($request=='activated'){
			$response = $userActions->isUserActivated($_GET['username']);
			header('Content-Type: application/json');
			echo json_encode(array('userActivated'=>$response));
		}
		else if($request=='available'){
			$response = $userActions->isUsernameAvailable($_GET['username']);
			header('Content-Type: application/json');
			echo json_encode(array('usernameAvailable'=>$response));
		}
		else if($request=='changepass'){
			$oldPass = $_GET['oldpass'];
			$newPass = $_GET['newPass'];
			if($authorizedUser!=null){
				if(isset($oldPass) && isset($newPass)) $userActions->changePass($authorizedUser, $oldPass, $newPass);
			}
			else{
				badTokenResponse('changepass');
			}
		}
		else if($request=='changebio'){
			$newBio = $_GET['bio'];
			if($authorizedUser!=null){
				if(isset($newBio)) $userActions->changeBio($authorizedUser, $newBio);
			}
			else{
				badTokenResponse('changebio');
			}
		}
		else if($request=='updateavatar'){
			if($authorizedUser!=null){
				$fileExtension = $_GET['extension'];
				
				// save the avatar file
				$filename = BETAVILLE_FILE_STORE_URL."/avatars/".$authorizedUser.".".$fileExtension;
				$fileData = file_get_contents('php://input');
				$fileHandle = fopen($filename, 'wb');
				fwrite($fileHandle, $fileData);
				fclose($fileHandle);
				header('Content-Type: application/json');
				echo json_encode(array('updateavatar'=>true));
			}
			else{
				badTokenResponse('updateavatar');
			}
		}
		else if($request=='getmail'){}
		else if($request=='checklevel'){}
		else if($request=='getlevel'){
			$response = $userActions->getUserType($_GET['username']);
			header('Content-Type: application/json');
			echo json_encode(array('userType'=>$response));
		}
		else if($request=='getpublicinfo'){
			$response = $userActions->getPublicInfo($_GET['username']);
			header('Content-Type: application/json');
			echo json_encode(array('userInfo'=>$response));
		}
	}
	else if($section=='coordinate'){
		include_once "inc/class.coordinate.inc.php";
		$coordinateActions = new CoordinateActions(null);
		if($request=='getutm'){
			$utm = $coordinateActions->getUTMCoordinate($_GET['id']);
			header('Content-Type: application/json');
			echo json_encode($utm);
		}
		else if($request=='getlatlon'){
			$latLon = $coordinateActions->getLatLonCoordinate($_GET['id']);
			header('Content-Type: application/json');
			echo json_encode($latLon);
		}
	}
	
	else if($section=='design'){
		include_once "inc/class.design.inc.php";
		include_once "inc/class.user.inc.php";
		$designActions = new DesignActions(null);
		$userActions = new UserActions(null);
		if($request=='synchronizedata'){}
		else if($request=='addempty'){}
		else if($request=='addproposal'){}
		else if($request=='addbase'){
		
			if($authorizedUser!=null){
				
				// is the user allowed to upload base models?
				$userType = $userActions->getUserType($authorizedUser);
				if($userType == USER_TYPE_BASE_COMMITTER
					|| $userType == USER_TYPE_MODERATOR
					|| $userType == USER_TYPE_ADMIN){
					
					// put metadata into the database
					
					// save the file
					$filename = "something.txt";
					$fileData = file_get_contents('php://input');
					$fileHandle = fopen($filename, 'wb');
					fwrite($fileHandle, $fileData);
					fclose($fileHandle);
				}
				else{
					// we are her because the user cannot upload base models
				}
			}
			else{
				badTokenResponse('addbase');
			}
		
		}
		else if($request=='addbasethumbnail'){
			if($authorizedUser!=null){
				$filename = BETAVILLE_FILE_STORE_URL."/designthumbs/".$_GET['designID'].".png";
				$fileData = file_get_contents('php://input');
				$fileHandle = fopen($filename, 'wb');
				fwrite($fileHandle, $fileData);
				fclose($fileHandle);
				//echo 'done';
			}
			else{
				badTokenResponse('addbasethumbnail');
			}
		}
		else if($request=='changename'){
			$id = $_GET['id'];
			$name = $_GET['name'];
			if($authorizedUser!=null){
				if(isset($id) && isset($name)){}
			}
			else{
				badTokenResponse('changename');
			}
		}
		else if($request=='changedescription'){
			$id = $_GET['id'];
			$description = $_GET['description'];
			if($authorizedUser!=null){
				if(isset($id) && isset($description)){}
			}
			else{
				badTokenResponse('changedescription');
			}
		}
		else if($request=='changeaddress'){
			$id = $_GET['id'];
			$address = $_GET['address'];
			if($authorizedUser!=null){
				if(isset($id) && isset($address)){}
			}
		}
		else if($request=='changeurl'){
			$id = $_GET['id'];
			$url = $_GET['url'];
			if($authorizedUser!=null){
				if(isset($id) && isset($url)){}
			}
			else{
				badTokenResponse('changeurl');
			}
		}
		else if($request=='changemodellocation'){}
		else if($request=='findbyid'){
			$design = $designActions->findDesignByID($_GET['id']);
			header('Content-Type: application/json');
			echo json_encode(array('design'=>$design));
		}
		else if($request=='findbyname'){
			$designs = $designActions->findDesignByName($_GET['name']);
			header('Content-Type: application/json');
			echo json_encode(array('designs'=>$designs));
		}
		else if($request=='findbyuser'){
			$designs = $designActions->findDesignByUser($_GET['user']);
			header('Content-Type: application/json');
			echo json_encode(array('designs'=>$designs));
		}
		else if($request=='findbydate'){}
		else if($request=='findbycity'){
			$designs = $designActions->findDesignByCity($_GET['city']);
			header('Content-Type: application/json');
			echo json_encode(array('designs'=>$designs));
		}
		else if($request=='findmodeledbycity'){}
		else if($request=='findaudiobycity'){}
		else if($request=='findimagebycity'){}
		else if($request=='findvideobycity'){}
		else if($request=='allproposals'){}
		else if($request=='requestfile'){
			// returns an http link to a file
			$fileName = $designActions->getFilenameForDesignMedia($_GET['id']);
			header('Content-Type: application/json');
			echo json_encode(array('fileLink'=>str_replace("\\", "", BETAVILLE_FILE_STORE_URL.'/designmedia/'.$fileName)));
			
			// we may or may not need this at some point
			if(get_magic_quotes_gpc()){	
			}
			else{
			}
		}
		else if($request=='requestthumb'){
			// returns an http link to a thumbnail
			header('Content-Type: application/json');
			echo json_encode(array('fileLink'=>str_replace("\\", "", BETAVILLE_FILE_STORE_URL.'/designthumbs/'.$_GET['id'].'png')));
		}
		else if($request=='changefile'){}
		else if($request=='reserve'){}
		else if($request=='remove'){}
		else if($request=='synchronize'){}
	}
	else if($section=='proposal'){
		if($request=='findinradius'){}
		else if($request=='getpermissions'){}
		else if($request=='addversion'){}
		else if($request=='getfeatured'){
			include_once "inc/class.design.inc.php";
			$designActions = new DesignActions(null);
			header('Content-Type: application/json');
			
			
			// set default values
			$start = 0;
			$end = 50;
			
			if(hasStartEnd()){
				$start = (int)$_GET['start'];
				$end = (int)$_GET['end'];
			}
			else if(!empty($_GET['quantity'])){
				$end = (int)$_GET['quantity'];
			}
			
			echo json_encode(array('designs'=>($designActions->getFeaturedProposals($start, $end))));
		}
	}
	else if($section=='version'){
		if($request=='versionsofproposal'){}
	}
	else if($section=='fave'){
		if($request=='add'){}
		else if($request=='remove'){}
	}
	else if($section=='activity'){
		if($request=='comments'){
			include_once "inc/class.comment.inc.php";
			$commentActions = new CommentActions(null);
			header('Content-Type: application/json');
			
			// set default values
			$start = 0;
			$end = 50;
			
			if(hasStartEnd()){
				$start = (int)$_GET['start'];
				$end = (int)$_GET['end'];
			}
			else if(!empty($_GET['quantity'])){
				$end = (int)$_GET['quantity'];
			}
			
			
			$comments = $commentActions->getRecentComments($start, $end);
			echo json_encode(array('comments'=>$comments));
		}
		else if($request=='designs'){
			include_once "inc/class.design.inc.php";
			$designActions = new DesignActions(null);
			header('Content-Type: application/json');
			
			$excludeEmpty = false;
			if(isset($_GET['excludeempty'])){
				$excludeEmpty = $_GET['excludeempty'];
			}
			
			// set default values
			$start = 0;
			$end = 50;
			
			if(hasStartEnd()){
				$start = (int)$_GET['start'];
				$end = (int)$_GET['end'];
				
			}
			else if(!empty($_GET['quantity'])){
				$end = (int)$_GET['quantity'];
			}
			
			echo json_encode(array('designs'=>($designActions->getRecentDesigns($start, $end, $excludeEmpty))));
		}
		else if($request=='proposals'){
			include_once "inc/class.design.inc.php";
			$designActions = new DesignActions(null);
			header('Content-Type: application/json');
			
			// set default values
			$start = 0;
			$end = 50;
			
			if(hasStartEnd()){
				$start = (int)$_GET['start'];
				$end = (int)$_GET['end'];
				
			}
			else if(!empty($_GET['quantity'])){
				$end = (int)$_GET['quantity'];
			}
			
			echo json_encode(array('designs'=>($designActions->getRecentProposals($start, $end))));
		}
		else if($request=='versions'){
			include_once "inc/class.design.inc.php";
			$designActions = new DesignActions(null);
			header('Content-Type: application/json');
			
			// set default values
			$start = 0;
			$end = 50;
			
			if(hasStartEnd()){
				$start = (int)$_GET['start'];
				$end = (int)$_GET['end'];
			}
			else if(!empty($_GET['quantity'])){
				$end = (int)$_GET['quantity'];
			}
			
			echo json_encode(array('designs'=>($designActions->getRecentVersions($start, $end))));
		}
		else if($request=='myactivity'){
			include_once "inc/class.comment.inc.php";
			$commentActions = new CommentActions(null);
			$comments = $commentActions->getNotificationsForUser($_GET['user']);
			header('Content-Type: application/json');
			echo json_encode(array('comments'=>$comments));
		}
	}
	else if($section=='share'){
		// share does not currently use the request parameter
	}
	else if($section=='comment'){
		include_once "inc/class.comment.inc.php";
		$commentActions = new CommentActions(null);
		if($request=='add'){
			$designID = $_GET['designID'];
			$comment = $_GET['comment'];
			$repliesTo = $_GET['repliesTo'];
			if(!isset($repliesTo)){
				$repliesTo=0;
			}
			if($authorizedUser!=null){
				if(isset($designID) && isset($comment)){
					$response = $commentActions->addComment($designID, $authorizedUser, $comment, $repliesTo);
					header('Content-Type: application/json');
					echo json_encode(array('addcomment'=>$response));
				}
			}
			else{
				badTokenResponse('addcomment');
			}
		}
		else if($request=='delete'){}
		else if($request=='reportspam'){}
		else if($request=='getforid'){
			$comments = $commentActions->getCommentsForDesign($_GET['id']);
			header('Content-Type: application/json');
			echo json_encode(array('comments'=>$comments));
		}
	}
	else if($section=='city'){
		//Not sure how we use this, but maybe if needed in the future		
		include_once "inc/class.city.inc.php";
		$cityActions=new CityActions(null);
		
		if($request=='add'){}
		else if($request=='findbyname'){
			$cities = $cityActions->findCityByName($_GET['name']);
			header('Content-Type: application/json');
			echo json_encode(array('cities'=>$cities));
		}
		else if($request=='findbystate'){}
		else if($request=='findbycountry'){}
		else if($request=='findbyid'){}
		else if($request=='findbyall'){}
		else if($request=='getall'){}
	}
	else if($section=='wormhole'){
		if($request=='add'){}
		else if($request=='delete'){}
		else if($request=='editname'){}
		else if($request=='editlocation'){}
		else if($request=='getwithin'){}
		else if($request=='getall'){}
		else if($request=='getallincity'){}
	}
	else if($section=='softwareversion'){
		// eh, I don't think we really need this for the web service
		if($request=='getdesign'){}
	}
	else if($section=='time'){
		if($request=='getformatted'){
			header('Content-Type: application/json');
			echo json_encode(array('serverTime'=>date("Y-m-d H:m:s")));
		}
			else if($request=='getdb'){
			include_once "inc/class.util.inc.php";
			$utilActions = new UtilActions(null);
			header('Content-Type: application/json');
			echo json_encode(array('serverTime'=>$utilActions->getDateTime()));
		}
	}


function badTokenResponse($requestName){
	header('Content-Type: application/json');
	echo json_encode(array($requestName=>"User authentication invalid or not supplied"));
}

function hasStartEnd(){
	return (isset($_GET['start']) && isset($_GET['end']));
}
}
?>
