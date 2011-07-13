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
	
	private function designFromRow($row){
		$design = array();
		$design[] = array(DESIGN_ID=>$row[DESIGN_ID]);
		$design[] = array(DESIGN_NAME=>$row[DESIGN_NAME]);
		$design[] = array(DESIGN_FILE=>$row[DESIGN_FILE]);
		$design[] = array(DESIGN_CITY=>$row[DESIGN_CITY]);
		$design[] = array(DESIGN_ADDRESS=>$row[DESIGN_ADDRESS]);
		
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