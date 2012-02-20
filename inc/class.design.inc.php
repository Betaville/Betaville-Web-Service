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
 
/**
 * Actions for retrieving design information from the database
 * @author Skye Book
 */
class DesignActions{
	
	private $_db;
	private $_coordinateActions;
	private $selectFromWhat = "design LEFT JOIN modeldesign ON design.designID=modeldesign.designid LEFT JOIN audiodesign ON design.designID=audiodesign.designid LEFT JOIN videodesign ON design.designID=videodesign.designid LEFT JOIN sketchdesign ON design.designID=sketchdesign.designid";//GROUP BY design.designID

	public function __construct($db=null){
		include_once "config.php";
		include_once "class_names.php";
		include_once "db_constants.php";
		include_once "inc/class.coordinate.inc.php";
		
		if(is_object($db)){
			$this->_db=$db;
		}
		else{
			$dsn = "mysql:host=".DB_HOST.";dbname=".DB_NAME;
			$this->_db = new PDO($dsn, DB_USER, DB_PASS);
		}
		
		$this->_coordinateActions = new CoordinateActions($db);
	}

	public function findDesignByID($id){
	//echo $this->selectFromWhat;
	$sql = "SELECT * FROM ".$this->selectFromWhat." WHERE design.designID=:designID AND design.isAlive=1";
	//echo $sql;
		//$sql = 'SELECT * FROM '.DESIGN_TABLE.' JOIN modeldesign ON design.designID=modeldesign.designid WHERE '.DESIGN_ID.'=:designID AND '.DESIGN_IS_ALIVE.'=1';
		try{
			$stmt = $this->_db->prepare($sql);
			$stmt->bindParam(":designID", $id, PDO::PARAM_INT);
			$stmt->execute();
			$row = $stmt->fetch();
			
			if($row[DESIGN_TYPE]===DESIGN_TYPE_AUDIO){
				return $this->audibleDesignFromRow($row, $false);
			}
			else if($row[DESIGN_TYPE]===DESIGN_TYPE_VIDEO){
				return $this->videoDesignFromRow($row, $false);
			}
			else if($row[DESIGN_TYPE]===DESIGN_TYPE_MODEL){
				return $this->modeledDesignFromRow($row, $false);
			}
			else if($row[DESIGN_TYPE]===DESIGN_TYPE_SKETCH){
				return $this->sketchDesignFromRow($row, $false);
			}
			else if($row[DESIGN_TYPE]===DESIGN_TYPE_EMPTY){
				return $this->emptyDesignFromRow($row, $false);
			}
		}catch(PDOException $e){
			echo'exception';
			return false;
		}
		return null;
	}
	
	public function findDesignByName($name){
		$name = '%'.$name.'%';
		$sql = 'SELECT * FROM '.$this->selectFromWhat.' WHERE '.DESIGN_TABLE.'.'.DESIGN_NAME.' LIKE :designName AND '.DESIGN_TABLE.'.'.DESIGN_IS_ALIVE.'=1';
		try{
			$stmt = $this->_db->prepare($sql);
			$stmt->bindParam(":designName", $name, PDO::PARAM_STR);
			$stmt->execute();
			$designs = array();
			while($row=$stmt->fetch()){
				if($row[DESIGN_TYPE]===DESIGN_TYPE_AUDIO){
					$designs[] = $this->audibleDesignFromRow($row, $false);
				}
				else if($row[DESIGN_TYPE]===DESIGN_TYPE_VIDEO){
					$designs[] = $this->videoDesignFromRow($row, $false);
				}
				else if($row[DESIGN_TYPE]===DESIGN_TYPE_MODEL){
					$designs[] = $this->modeledDesignFromRow($row, $false);
				}
				else if($row[DESIGN_TYPE]===DESIGN_TYPE_SKETCH){
					$designs[] = $this->sketchDesignFromRow($row, $false);
				}
				else if($row[DESIGN_TYPE]===DESIGN_TYPE_EMPTY){
					$designs[] = $this->emptyDesignFromRow($row, $false);
				}
			}
			return $designs;
		}catch(PDOException $e){
			echo'exception';
			return false;
		}
		return null;
	}
	
	public function findDesignByUser($user, $start, $end, $excludeEmpty){
		$excludeAddIn = "";
		if($excludeEmpty){
			$excludeAddIn = " AND ".DESIGN_TYPE." != 'empty' ";
		}
		$sql = 'SELECT * FROM '.$this->selectFromWhat.' WHERE '.DESIGN_TABLE.'.'.DESIGN_USER.' LIKE :user AND '.DESIGN_TABLE.'.'.DESIGN_IS_ALIVE.'=1 '.$excludeAddIn.' ORDER BY design.designID DESC LIMIT :start, :end';
		try{
			$stmt = $this->_db->prepare($sql);
			$stmt->bindParam(":user", $user, PDO::PARAM_STR);
			$stmt->bindParam(":start", $start, PDO::PARAM_INT);
			$stmt->bindParam(":end", $end, PDO::PARAM_INT);
			$stmt->execute();
			$designs = array();
			while($row=$stmt->fetch()){
				
				if($row[DESIGN_TYPE]===DESIGN_TYPE_AUDIO){
					$designs[] = $this->audibleDesignFromRow($row, $false);
				}
				else if($row[DESIGN_TYPE]===DESIGN_TYPE_VIDEO){
					$designs[] = $this->videoDesignFromRow($row, $false);
				}
				else if($row[DESIGN_TYPE]===DESIGN_TYPE_MODEL){
					$designs[] = $this->modeledDesignFromRow($row, $false);
				}
				else if($row[DESIGN_TYPE]===DESIGN_TYPE_SKETCH){
					$designs[] = $this->sketchDesignFromRow($row, $false);
				}
				else if($row[DESIGN_TYPE]===DESIGN_TYPE_EMPTY){
					$designs[] = $this->emptyDesignFromRow($row, $false);
				}
				
			}
			return $designs;
		}catch(PDOException $e){
			echo'exception';
			return false;
		}
		return null;
	}
	
	public function findDesignByCity($city){
		$sql = 'SELECT * FROM '.$this->selectFromWhat.' WHERE '.DESIGN_TABLE.'.'.DESIGN_CITY.'=:city AND '.DESIGN_TABLE.'.'.DESIGN_IS_ALIVE.'=1';
		try{
			$stmt = $this->_db->prepare($sql);
			$stmt->bindParam(":city", $city, PDO::PARAM_INT);
			$stmt->execute();
			$designs = array();
			while($row=$stmt->fetch()){
				if($row[DESIGN_TYPE]===DESIGN_TYPE_AUDIO){
					$designs[] = $this->audibleDesignFromRow($row, $false);
				}
				else if($row[DESIGN_TYPE]===DESIGN_TYPE_VIDEO){
					$designs[] = $this->videoDesignFromRow($row, $false);
				}
				else if($row[DESIGN_TYPE]===DESIGN_TYPE_MODEL){
					$designs[] = $this->modeledDesignFromRow($row, $false);
				}
				else if($row[DESIGN_TYPE]===DESIGN_TYPE_SKETCH){
					$designs[] = $this->sketchDesignFromRow($row, $false);
				}
				else if($row[DESIGN_TYPE]===DESIGN_TYPE_EMPTY){
					$designs[] = $this->emptyDesignFromRow($row, $false);
				}
			}
			return $designs;
		}catch(PDOException $e){
			echo'exception';
			return false;
		}
		return null;
	}
	
	public function didUserCreateDesign($username, $designID){
		$sql = 'SELECT '.DESIGN_USER.' FROM '.DESIGN_TABLE.' WHERE '.DESIGN_ID.'=:designID';
		try{
			$stmt = $this->_db->prepare($sql);
			$stmt->bindParam(":designID", $designID, PDO::PARAM_INT);
			$stmt->execute();
			$row=$stmt->fetch();
			return $row[DESIGN_USER]==$username;
		}catch(PDOException $e){
			echo'exception';
			return false;
		}
		return false;
	}
	
	public function getFilenameForDesignMedia($designID){
		$sql = 'SELECT '.DESIGN_FILE.' FROM '.DESIGN_TABLE.' WHERE '.DESIGN_ID.'=:designID';
		try{
			$stmt = $this->_db->prepare($sql);
			$stmt->bindParam(":designID", $designID, PDO::PARAM_INT);
			$stmt->execute();
			$row=$stmt->fetch();
			return $row[DESIGN_FILE];
		}catch(PDOException $e){
			echo'exception';
			return null;
		}
		return null;
	}
	
	public function getRecentDesignIDs($numberToReturn){
		$sql = 'SELECT '.DESIGN_ID.' FROM ' . DESIGN_TABLE . ' WHERE '.DESIGN_IS_ALIVE.' = 1 ORDER BY '.DESIGN_ID .' DESC LIMIT :numberToReturn';
		try{
			$stmt = $this->_db->prepare($sql);
			$stmt->bindParam(":numberToReturn", $numberToReturn, PDO::PARAM_INT);
			$stmt->execute();
			$designIDS = array();
			while($row=$stmt->fetch()){
				$designIDS[]=$row[DESIGN_ID];
			}
			return $designIDS;
		}catch(PDOException $e){
			echo'exception';
			return null;
		}
		return null;
	}
	
	public function getRecentDesigns($start, $end, $excludeEmpty){
		$excludeAddIn = "";
		if($excludeEmpty){
			$excludeAddIn = " AND ".DESIGN_TYPE." != 'empty' ";
		}
		$sql = 'SELECT * FROM ' . DESIGN_TABLE . ' WHERE '.DESIGN_IS_ALIVE.' = 1 '.$excludeAddIn.' AND '.DESIGN_IS_ALIVE.'=1 ORDER BY '.DESIGN_ID .' DESC LIMIT :start, :end';
		try{
			$stmt = $this->_db->prepare($sql);
			$stmt->bindParam(":start", $start, PDO::PARAM_INT);
			$stmt->bindParam(":end", $end, PDO::PARAM_INT);
			$stmt->execute();
			$designs = array();
			while($row=$stmt->fetch()){
				$designs[] = $this->designFromRow($row, false);
			}
			return $designs;
		}catch(PDOException $e){
			echo'exception';
			return null;
		}
		return null;
	}
	
	public function getFeaturedProposals($start, $end){
		$sql = "SELECT * FROM design JOIN proposal ON designid=destinationid WHERE type='proposal' AND isAlive=1 AND featured IS NOT NULL ORDER BY featured DESC, designid DESC LIMIT :start, :end";
		try{
			$stmt = $this->_db->prepare($sql);
			$stmt->bindParam(":start", $start, PDO::PARAM_INT);
			$stmt->bindParam(":end", $end, PDO::PARAM_INT);
			$stmt->execute();
			$designs = array();
			while($row=$stmt->fetch()){
				$designs[] = $this->designFromRow($row, false);
			}
			return $designs;
		}catch(PDOException $e){
			echo'exception';
			return null;
		}
		return null;
	}
	
	public function getRecentProposals($start, $end){
		$sql = "SELECT * FROM design JOIN proposal ON designid=destinationid WHERE type='proposal' AND isAlive=1 ORDER BY designid DESC LIMIT :start, :end";
		try{
			$stmt = $this->_db->prepare($sql);
			$stmt->bindParam(":start", $start, PDO::PARAM_INT);
			$stmt->bindParam(":end", $end, PDO::PARAM_INT);
			$stmt->execute();
			$designs = array();
			while($row=$stmt->fetch()){
				$designs[] = $this->designFromRow($row, false);
			}
			return $designs;
		}catch(PDOException $e){
			echo'exception';
			return null;
		}
		return null;
	}
	
	public function getRecentVersions($start, $end){
		$sql = "SELECT * FROM design JOIN proposal ON designid=destinationid WHERE type='version' AND isAlive=1 ORDER BY designid DESC LIMIT :start, :end";
		try{
			$stmt = $this->_db->prepare($sql);
			$stmt->bindParam(":start", $start, PDO::PARAM_INT);
			$stmt->bindParam(":end", $end, PDO::PARAM_INT);
			$stmt->execute();
			$designs = array();
			while($row=$stmt->fetch()){
				$designs[] = $this->designFromRow($row, false);
			}
			return $designs;
		}catch(PDOException $e){
			echo'exception';
			return null;
		}
		return null;
	}
	
	private function designFromRow($row, $utmRequested){
		
		// pre-pack coordinate array
		$coordinateArray;
		if($utmRequested){
			$coordinateArray = $this->_coordinateActions->getUTMCoordinate($row[DESIGN_COORDINATE]);
		}
		else{
			$coordinateArray = $this->_coordinateActions->getLatLonCoordinate($row[DESIGN_COORDINATE]);
		}
		
		return array(DESIGN_ID=>$row[DESIGN_ID],DESIGN_NAME=>$row[DESIGN_NAME],DESIGN_FILE=>$row[DESIGN_FILE],
			DESIGN_CITY=>$row[DESIGN_CITY],DESIGN_ADDRESS=>$row[DESIGN_ADDRESS],DESIGN_USER=>$row[DESIGN_USER],
			"coordinate"=>$coordinateArray,DESIGN_DATE=>$row[DESIGN_DATE],DESIGN_DESCRIPTION=>$row[DESIGN_DESCRIPTION],
			DESIGN_URL=>$row[DESIGN_URL], DESIGN_TYPE=>$row[DESIGN_TYPE]);
	}
	
	private function modeledDesignFromRow($row, $utmRequested){
		$designArray = $this->designFromRow($row, $utmRequested);
		
		$auxArray = array(MODEL_ROTATION_X=>$row[MODEL_ROTATION_X], MODEL_ROTATION_Y=>$row[MODEL_ROTATION_Y], MODEL_ROTATION_Z=>$row[MODEL_ROTATION_Z],
		MODEL_LENGTH=>$row[MODEL_LENGTH], MODEL_WIDTH=>$row[MODEL_WIDTH], MODEL_HEIGHT=>$row[MODEL_HEIGHT], MODEL_TEX=>$row[MODEL_TEX]);
		
		return array_merge((array)$designArray, (array)$auxArray);
	}
	
	
	private function audibleDesignFromRow($row, $utmRequested){
		$designArray = $this->designFromRow($row, $utmRequested);
		
		$auxArray = array(AUDIO_LENGTH=>$row[AUDIO_LENGTH], AUDIO_VOLUME=>$row[AUDIO_VOLUME], AUDIO_DIRECTIONX=>$row[AUDIO_DIRECTIONX],
		AUDIO_DIRECTIONY=>$row[AUDIO_DIRECTIONY], AUDIO_DIRECTIONZ=>$row[AUDIO_DIRECTIONZ]);
		
		return array_merge((array)$designArray, (array)$auxArray);
	}
	
	
	private function sketchDesignFromRow($row, $utmRequested){
		$designArray = $this->designFromRow($row, $utmRequested);
		
		$auxArray = array(SKETCH_ROTATION=>$row[SKETCH_ROTATION], SKETCH_LENGTH=>$row[SKETCH_LENGTH], SKETCH_WIDTH=>$row[SKETCH_WIDTH],
		SKETCH_UPPLANE=>$row[SKETCH_UPPLANE]);
		
		return array_merge((array)$designArray, (array)$auxArray);
	}
	
	
	private function videoDesignFromRow($row, $utmRequested){
		$designArray = $this->designFromRow($row, $utmRequested);
		
		$auxArray = array(VIDEO_LENGTH=>$row[VIDEO_LENGTH], VIDEO_VOLUME=>$row[VIDEO_VOLUME], VIDEO_DIRECTIONX=>$row[VIDEO_DIRECTIONX],
		VIDEO_DIRECTIONY=>$row[VIDEO_DIRECTIONY], VIDEO_DIRECTIONZ=>$row[VIDEO_DIRECTIONZ], VIDEO_FORMAT=>$row[VIDEO_FORMAT]);
		
		return array_merge((array)$designArray, (array)$auxArray);
	}
	
	private function emptyDesignFromRow($row, $utmRequested){
		$designArray = $this->designFromRow($row, $utmRequested);
		
		$auxArray = array(EMPTY_LENGTH=>$row[EMPTY_LENGTH], EMPTY_WIDTH=>$row[EMPTY_WIDTH]);
		
		return array_merge((array)$designArray, (array)$auxArray);
	}
	
	/**
	 * Not used at the moment
	 *
	 */
	private function createDesignXML($designs){
		$doc = new DOMDocument();
		$doc->formatOutput = true;

		$r = $doc->createElement( "designs" );
		$doc->appendChild( $r );

		foreach( $designs as $design )
		{
			$b = $doc->createElement("design");

			$author = $doc->createElement( "author" );
			$author->appendChild(
				$doc->createTextNode( $book['author'] )
				);
			$b->appendChild( $author );

			$title = $doc->createElement( "title" );
			$title->appendChild(
				$doc->createTextNode( $book['title'] )
				);
			$b->appendChild( $title );

			$publisher = $doc->createElement( "publisher" );
			$publisher->appendChild(
				$doc->createTextNode( $book['publisher'] )
				);
			$b->appendChild( $publisher );

			$r->appendChild( $b );
		}

		echo $doc->saveXML();
	}
	
	public function userHasWriteAccessToDesign($designID, $user){
		//$sql = "SELECT * FROM design JOIN proposal ON designID=destinationID WHERE designID = :designID OR proposal.user_group LIKE %".$user.",%";  // eventually we will need to check if the user is part of the group
		$sql = "SELECT * FROM design JOIN proposal ON design.designID=proposal.destinationID WHERE design.user='".$user."' OR proposal.user_group LIKE  '%,".$user.",%'";
				
		try{
			$stmt = $this->_db->prepare($sql);
			$stmt->execute();
			if($stmt->fetch()){
				return true;
			}
			else return false;
		}catch(PDOException $e){
			return false;
		}
	}
	
	public function setDesignPermission($designID,$permission) {
		$sql = 'UPDATE design SET viewability ="'.$permission.'" WHERE designID = :designID';

		try{
			$stmt = $this->_db->prepare($sql);
			$stmt->bindParam(":designID", $designID, PDO::PARAM_INT);
			$stmt->execute();
			$designs = array();
			return true;
		}catch(PDOException $e){
			return false;
		}
		

	}
	
	public function changeDesignDescription($designID, $newDescription){
		$sql = "UPDATE design SET description = :newDescription WHERE designID = :designID";
		try{
			$stmt = $this->_db->prepare($sql);
			$stmt->bindParam(":newDescription", $newDescription, PDO::PARAM_STR);
			$stmt->bindParam(":designID", $designID, PDO::PARAM_INT);
			$stmt->execute();
			$designs = array();
			return true;
		}catch(PDOException $e){
			return false;
		}
	}
	
	public function changeDesignName($designID, $newName){
		$sql = "UPDATE design SET name = :newName WHERE designID = :designID";
		try{
			$stmt = $this->_db->prepare($sql);
			$stmt->bindParam(":newName", $newName, PDO::PARAM_STR);
			$stmt->bindParam(":designID", $designID, PDO::PARAM_INT);
			$stmt->execute();
			$designs = array();
			return true;
		}catch(PDOException $e){
			return false;
		}
	}
	
	public function changeDesignAddress($designID, $newAddress){
		$sql = "UPDATE design SET address = :newAddress WHERE designID = :designID";
		try{
			$stmt = $this->_db->prepare($sql);
			$stmt->bindParam(":newAddress", $newAddress, PDO::PARAM_STR);
			$stmt->bindParam(":designID", $designID, PDO::PARAM_INT);
			$stmt->execute();
			$designs = array();
			return true;
		}catch(PDOException $e){
			return false;
		}
	}

	public function deleteDesign($designID, $authorizedUser) {
		$sql = "UPDATE design SET isAlive=0,lastModified=NOW() WHERE designID = :designID AND user LIKE :user";
		try{
			$stmt = $this->_db->prepare($sql);
			$stmt->bindParam(":designID", $designID, PDO::PARAM_INT);
			$stmt->bindParam(":user", $authorizedUser, PDO::PARAM_STR);
			$stmt->execute();
			return true;
		}catch(PDOException $e) {
			return false;
		}
	}

	//Check if the design is a proposal, used in the Add user to functionality group
	public function checkIfProposal($designID) {
		$sql = 'SELECT * FROM proposal WHERE (destinationID="'.$designID.'" OR sourceID="'.$designID.'") AND type = "proposal"';
		try {
			$stmt = $this->_db->prepare($sql);
			$stmt->execute();
			if($stmt->fetch()){
				return true;
			}
			else return false;
		}
		catch(PDOException $e) {
			return false;
		}
		
	}
}
?>
