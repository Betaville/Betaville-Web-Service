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
 
/* require the user as the parameter */

// Should we be doing session start here?  How will it work?!
//session_start();

if($_GET['gz']==1){
	// check if a zipped response is requested
	header('Content-Encoding: gzip');
	ob_start("ob_gzhandler");
}

if(isset($_GET['section']) && isset($_GET['request'])){
	$section = $_GET['section'];
	$request = $_GET['request'];
	if($section=='user'){
		include_once "inc/class.user.inc.php";
		$userActions = new UserActions($db);
		if($request=='auth'){
			$response = $userActions->login($_GET['username'], $_GET['password']);
			header('Content-Type: application/json');
			echo json_encode(array('authenticationSuccess'=>$response));
		}
		else if($request=='startsession'){
			
		}
		else if($request=='endsession'){
			
		}
		else if($request=='add'){
			$response = $userActions->addUser($_GET['username'], $_GET['password'], $_GET['email']);
			header('Content-Type: application/json');
			echo json_encode(array('userAdded'=>$response));
		}
		else if($request=='available'){
			$response = $userActions->isUsernameAvailable($_GET['username']);
			header('Content-Type: application/json');
			echo json_encode(array('usernameAvailable'=>$response));
		}
		else if($request=='changepass'){}
		else if($request=='changebio'){}
		else if($request=='getmail'){}
		else if($request=='checklevel'){}
		else if($request=='getlevel'){}
		else if($request=='getpublicinfo'){
			$response = $userActions->getPublicInfo($_GET['username']);
			header('Content-Type: application/json');
			echo json_encode(array('userInfo'=>$response));
		}
	}
	else if($section=='coordinate'){
		include_once "inc/class.coordinate.inc.php";
		$coordinateActions = new CoordinateActions($db);
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
		$designActions = new DesignActions($db);
		if($request=='synchronizedata'){}
		else if($request=='addempty'){}
		else if($request=='addproposal'){}
		else if($request=='addbase'){}
		else if($request=='changename'){}
		else if($request=='changedescription'){}
		else if($request=='changeaddress'){}
		else if($request=='changeurl'){}
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
			$designActions = new DesignActions($db);
			header('Content-Type: application/json');
			$quantity = 50;
			if(empty($_GET['quantity'])) $quantity = 50;
			else $quantity = (int)$_GET['quantity'];
			echo json_encode(array('designs'=>($designActions->getFeaturedProposals($quantity))));
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
			$commentActions = new CommentActions($db);
			$quantity = 50;
			if(empty($_GET['quantity'])) $quantity = 50;
			else $quantity = (int)$_GET['quantity'];
			$comments = $commentActions->getRecentComments($quantity);
			header('Content-Type: application/json');
			echo json_encode(array('comments'=>$comments));
		}
		else if($request=='designs'){
			include_once "inc/class.design.inc.php";
			$designActions = new DesignActions($db);
			header('Content-Type: application/json');
			$quantity = 50;
			if(empty($_GET['quantity'])) $quantity = 50;
			else $quantity = (int)$_GET['quantity'];
			echo json_encode(array('designs'=>($designActions->getRecentDesigns($quantity))));
		}
		else if($request=='proposals'){
			include_once "inc/class.design.inc.php";
			$designActions = new DesignActions($db);
			header('Content-Type: application/json');
			$quantity = 50;
			if(empty($_GET['quantity'])) $quantity = 50;
			else $quantity = (int)$_GET['quantity'];
			echo json_encode(array('designs'=>($designActions->getRecentProposals($quantity))));
		}
		else if($request=='versions'){
			include_once "inc/class.design.inc.php";
			$designActions = new DesignActions($db);
			header('Content-Type: application/json');
			$quantity = 50;
			if(empty($_GET['quantity'])) $quantity = 50;
			else $quantity = (int)$_GET['quantity'];
			echo json_encode(array('designs'=>($designActions->getRecentVersions($quantity))));
		}
		else if($request=='myactivity'){
			include_once "inc/class.comment.inc.php";
			$commentActions = new CommentActions($db);
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
		$commentActions = new CommentActions($db);
		if($request=='add'){}
		else if($request=='delete'){}
		else if($request=='reportspam'){}
		else if($request=='getforid'){
			$comments = $commentActions->getCommentsForDesign($_GET['id']);
			header('Content-Type: application/json');
			echo json_encode(array('comments'=>$comments));
		}
	}
	else if($section=='city'){
		if($request=='add'){}
		else if($request=='findbyname'){}
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
}
?>