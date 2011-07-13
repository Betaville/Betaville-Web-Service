<?php
class DesignActions{
	include_once "class_names.php";
	include_once "db_constants.php";
	
	private $_db;

	public function __construct($db==null){
		if(is_object($db)){
			$this->_db=$db;
		}
		else{
			$dsn = "mysql:host=".DB_HOST.";dbname=".DB_NAME;
			$this->_db = new PDO($dsn, DB_USER, DB_PASS);
		}
	}

	public function findDesignByID($id){
		$sql = 'SELECT * FROM design WHERE designID=:designID';
		try{
			$stmt = $this->_db->prepare($sql);
			$stmt->bindParam(":designID", $id, PDO::PARAM_INT);
			$stmt->execute();
		}catch(PDOException $e){
			echo'exception';
			return false;
		}
		
		$designs = array();
		
		// put in a new entry
		$designs [] = array(
			'title' => 'PHP Hacks',
			'author' => 'Jack Herrington',
			'publisher' => "O'Reilly"
			);


		return $designs;
	}

	function createDesignXML($designs){
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