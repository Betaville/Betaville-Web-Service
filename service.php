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
			echo json_encode(array('success'=>$response));
		}
		else if($request=='startsession'){}
		else if($request=='endsession'){}
		else if($request=='add'){}
		else if($request=='available'){
			$response = $userActions->isUsernameAvailable($_GET['username']);
			header('Content-type: application/json');
			echo json_encode(array('success'=>$response));
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
		else if($request=='requestfile'){}
		else if($request=='requestthumb'){}
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
	else if($section=='share'){
		// share does not currently use the request parameter
	}
	else if($section=='comment'){
		if($request=='add'){}
		else if($request=='delete'){}
		else if($request=='reportspam'){}
		else if($request=='getforid'){}
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