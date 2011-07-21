<?php
/* require the user as the parameter */

if(isset($_GET['section']) && isset($_GET['request'])){
	$section = $_GET['section'];
	$request = $_GET['request'];
	if($section=='user'){
		include_once "inc/class.user.inc.php";
		$userActions = new UserActions($db);
		if($request=='auth'){
			$response = $userActions->login($_GET['username'], $_GET['password']);
			header('Content-type: application/json');
			echo json_encode(array('authenticationSuccess'=>$response));
		}
		else if($request=='startsession'){}
		else if($request=='endsession'){}
		else if($request=='add'){}
		else if($request=='available'){
			$response = $userActions->isUsernameAvailable($_GET['username']);
			header('Content-type: application/json');
			echo json_encode(array('usernameAvailable'=>$response));
		}
		else if($request=='changepass'){}
		else if($request=='changebio'){}
		else if($request=='getmail'){}
		else if($request=='checklevel'){}
		else if($request=='getlevel'){}
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
			header('Content-type: application/json');
			echo json_encode(array('design'=>$design));
		}
		else if($request=='findbyname'){
			$designs = $designActions->findDesignByName($_GET['name']);
			header('Content-type: application/json');
			echo json_encode(array('designs'=>$designs));
		}
		else if($request=='findbyuser'){
			$designs = $designActions->findDesignByUser($_GET['user']);
			header('Content-type: application/json');
			echo json_encode(array('designs'=>$designs));
		}
		else if($request=='findbydate'){}
		else if($request=='findbycity'){
			$designs = $designActions->findDesignByCity($_GET['city']);
			header('Content-type: application/json');
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
			header('Content-type: application/json');
			echo json_encode(array('fileLink'=>str_replace("\\", "", BETAVILLE_FILE_STORE_URL.'/designmedia/'.$fileName)));
			
			// we may or may not need this at some point
			if(get_magic_quotes_gpc()){	
			}
			else{
			}
		}
		else if($request=='requestthumb'){
			// returns an http link to a thumbnail
			header('Content-type: application/json');
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
	}
	else if($section=='version'){
		if($request=='versionsofproposal'){}
	}
	else if($section=='fave'){
		if($request=='add'){}
		else if($request=='remove'){}
	}
	else if($section=='activity'){
		if($request=='comments'){}
		else if($request=='designs'){}
		else if($request=='myactivity'){}
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
			header('Content-type: application/json');
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