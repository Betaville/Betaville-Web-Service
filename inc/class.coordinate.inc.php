<?php

/**
 * Actions for interacting with coordinates
 * @author Skye Book
 */
class CoordinateActions{
	
	private $_db;

	public function __construct($db=null){
		include_once "config.php";
		include_once "class_names.php";
		include_once "db_constants.php";
		include_once "phpcoord/phpcoord-2.3.php";
		
		if(is_object($db)){
			$this->_db=$db;
		}
		else{
			$dsn = "mysql:host=".DB_HOST.";dbname=".DB_NAME;
			$this->_db = new PDO($dsn, DB_USER, DB_PASS);
		}
	}

	public function getUTMCoordinate($coordinateID){
		$sql = 'SELECT * FROM '.COORD_TABLE.' WHERE '.COORD_ID.'=:coordinateID';
		try{
			$stmt = $this->_db->prepare($sql);
			$stmt->bindParam(":coordinateID", $coordinateID, PDO::PARAM_INT);
			$stmt->execute();
			if($row=$stmt->fetch()){
				return $this->utmFromRow($row);
			}
		}catch(PDOException $e){
			echo'exception';
			return false;
		}
		return null;
	}
	
	public function getLatLonCoordinate($coordinateID){
		$utmCoordinate = $this->getUTMCoordinate($coordinateID);
		$utm = new UTMRef($utmCoordinate[COORD_EASTING],
		$utmCoordinate[COORD_NORTHING],
		$utmCoordinate[COORD_LATZONE],
		$utmCoordinate[COORD_LONZONE]);
		
        $latLon = $utm->toLatLng();
        //echo $latLon->lat;
        //echo "</br>";
        //echo $latLon->lng;
        return array("lat"=>$latLon->lat, "lon"=>$latLon->lng);
	}
	
	private function utmFromRow($row){
		return array(COORD_NORTHING=>$row[COORD_NORTHING],COORD_EASTING=>$row[COORD_EASTING],
			COORD_LATZONE=>$row[COORD_LATZONE],COORD_LONZONE=>$row[COORD_LONZONE]);
	}
}
?>