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
 
/**
 * Actions for retrieving design information from the database
 * @author Skye Book
 */
class DesignActions{
	
	private $_db;
	private $_coordinateActions;

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
		$sql = 'SELECT * FROM '.DESIGN_TABLE.' WHERE '.DESIGN_ID.'=:designID AND '.DESIGN_IS_ALIVE.'=1';
		try{
			$stmt = $this->_db->prepare($sql);
			$stmt->bindParam(":designID", $id, PDO::PARAM_INT);
			$stmt->execute();
			return $this->designFromRow($stmt->fetch(), $false);
		}catch(PDOException $e){
			echo'exception';
			return false;
		}
		return null;
	}
	
	public function findDesignByName($name){
		$name = '%'.$name.'%';
		$sql = 'SELECT * FROM '.DESIGN_TABLE.' WHERE '.DESIGN_NAME.' LIKE :designName AND '.DESIGN_IS_ALIVE.'=1';
		try{
			$stmt = $this->_db->prepare($sql);
			$stmt->bindParam(":designName", $name, PDO::PARAM_STR);
			$stmt->execute();
			$designs = array();
			while($row=$stmt->fetch()){
				$designs[] = $this->designFromRow($row, $false);
			}
			return $designs;
		}catch(PDOException $e){
			echo'exception';
			return false;
		}
		return null;
	}
	
	public function findDesignByUser($user){
		$sql = 'SELECT * FROM '.DESIGN_TABLE.' WHERE '.DESIGN_USER.' LIKE :user AND '.DESIGN_IS_ALIVE.'=1';
		try{
			$stmt = $this->_db->prepare($sql);
			$stmt->bindParam(":user", $user, PDO::PARAM_STR);
			$stmt->execute();
			$designs = array();
			while($row=$stmt->fetch()){
				$designs[] = $this->designFromRow($row, false);
			}
			return $designs;
		}catch(PDOException $e){
			echo'exception';
			return false;
		}
		return null;
	}
	
	public function findDesignByCity($city){
		$sql = 'SELECT * FROM '.DESIGN_TABLE.' WHERE '.DESIGN_CITY.'=:city AND '.DESIGN_IS_ALIVE.'=1';
		try{
			$stmt = $this->_db->prepare($sql);
			$stmt->bindParam(":city", $city, PDO::PARAM_INT);
			$stmt->execute();
			$designs = array();
			while($row=$stmt->fetch()){
				$designs[] = $this->designFromRow($row, false);
			}
			return $designs;
		}catch(PDOException $e){
			echo'exception';
			return false;
		}
		return null;
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
	
	public function getRecentDesigns($numberToReturn){
		$sql = 'SELECT * FROM ' . DESIGN_TABLE . ' WHERE '.DESIGN_IS_ALIVE.' = 1  AND '.DESIGN_IS_ALIVE.'=1 ORDER BY '.DESIGN_ID .' DESC LIMIT :numberToReturn';
		try{
			$stmt = $this->_db->prepare($sql);
			$stmt->bindParam(":numberToReturn", $numberToReturn, PDO::PARAM_INT);
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
	
	public function getFeaturedProposals($numberToReturn){
		$sql = "SELECT * FROM design JOIN proposal ON designid=destinationid WHERE type='proposal' AND isAlive=1 AND featured IS NOT NULL ORDER BY featured DESC, designid DESC LIMIT :numberToReturn;";
		try{
			$stmt = $this->_db->prepare($sql);
			$stmt->bindParam(":numberToReturn", $numberToReturn, PDO::PARAM_INT);
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
	
	public function getRecentProposals($numberToReturn){
		$sql = "SELECT * FROM design JOIN proposal ON designid=destinationid WHERE type='proposal' AND isAlive=1 ORDER BY designid DESC LIMIT :numberToReturn;";
		try{
			$stmt = $this->_db->prepare($sql);
			$stmt->bindParam(":numberToReturn", $numberToReturn, PDO::PARAM_INT);
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
	
	public function getRecentVersions($numberToReturn){
		$sql = "SELECT * FROM design JOIN proposal ON designid=destinationid WHERE type='version' AND isAlive=1 ORDER BY designid DESC LIMIT :numberToReturn;";
		try{
			$stmt = $this->_db->prepare($sql);
			$stmt->bindParam(":numberToReturn", $numberToReturn, PDO::PARAM_INT);
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

	function createJSON(){
	}
}
?>