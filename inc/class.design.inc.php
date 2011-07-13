<?php

/**
 * Actions for retrieving design information from the database
 * @author Skye Book
 */
class DesignActions{
	
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

	public function findDesignByID($id){
		$sql = 'SELECT * FROM '.DESIGN_TABLE.' WHERE '.DESIGN_ID.'=:designID';
		try{
			$stmt = $this->_db->prepare($sql);
			$stmt->bindParam(":designID", $id, PDO::PARAM_INT);
			$stmt->execute();
			return $this->designFromRow($stmt->fetch());
		}catch(PDOException $e){
			echo'exception';
			return false;
		}
		return null;
	}
	
	public function findDesignByName($name){
		$name = '%'.$name.'%';
		$sql = 'SELECT * FROM '.DESIGN_TABLE.' WHERE '.DESIGN_NAME.' LIKE :designName';
		try{
			$stmt = $this->_db->prepare($sql);
			$stmt->bindParam(":designName", $name, PDO::PARAM_STR);
			$stmt->execute();
			$designs = array();
			while($row=$stmt->fetch()){
				// only include this result if it hasn't been deleted
				if($row[DESIGN_IS_ALIVE]==1){
					$designs[] = $this->designFromRow($row);
				}
			}
			return $designs;
		}catch(PDOException $e){
			echo'exception';
			return false;
		}
		return null;
	}
	
	public function findDesignByUser($user){
		$user = '%'.$user.'%';
		$sql = 'SELECT * FROM '.DESIGN_TABLE.' WHERE '.DESIGN_USER.' LIKE :user';
		try{
			$stmt = $this->_db->prepare($sql);
			$stmt->bindParam(":user", $user, PDO::PARAM_STR);
			$stmt->execute();
			$designs = array();
			while($row=$stmt->fetch()){
				// only include this result if it hasn't been deleted
				if($row[DESIGN_IS_ALIVE]==1){
					$designs[] = $this->designFromRow($row);
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
		$sql = 'SELECT * FROM '.DESIGN_TABLE.' WHERE '.DESIGN_CITY.'=:city';
		try{
			$stmt = $this->_db->prepare($sql);
			$stmt->bindParam(":city", $city, PDO::PARAM_INT);
			$stmt->execute();
			$designs = array();
			while($row=$stmt->fetch()){
				// only include this result if it hasn't been deleted
				if($row[DESIGN_IS_ALIVE]==1){
					$designs[] = $this->designFromRow($row);
				}
			}
			return $designs;
		}catch(PDOException $e){
			echo'exception';
			return false;
		}
		return null;
	}
	
	private function designFromRow($row){
		$design = array();
		
		$design[] = array(DESIGN_ID=>$row[DESIGN_ID]);
		$design[] = array(DESIGN_NAME=>$row[DESIGN_NAME]);
		$design[] = array(DESIGN_FILE=>$row[DESIGN_FILE]);
		$design[] = array(DESIGN_CITY=>$row[DESIGN_CITY]);
		$design[] = array(DESIGN_ADDRESS=>$row[DESIGN_ADDRESS]);
		$design[] = array(DESIGN_USER=>$row[DESIGN_USER]);
		$design[] = array(DESIGN_COORDINATE=>$row[DESIGN_COORDINATE]);
		$design[] = array(DESIGN_DATE=>$row[DESIGN_DATE]);
		$design[] = array(DESIGN_DESCRIPTION=>$row[DESIGN_DESCRIPTION]);
		$design[] = array(DESIGN_URL=>$row[DESIGN_URL]);
		
		return $design;
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