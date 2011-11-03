<?php
/** 
File for retrieving city information and for further required queries in the future
*/
class CityActions{
	
	private $_db;

	public function __construct($db=null){
		include_once "config.php";
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

/**public function findAllCities(){

	$sql='SELECT * FROM'.CITY_TABLE;
	}
*/
public function findCityByName($name){

	$name='%'.$name.'%';
	$sql='SELECT * FROM'.CITY_TABLE.' WHERE '.CITY_TABLE.CITY_NAME.' LIKE: cityName';
	
	$stmt = $this->_db->prepare($sql);
			$stmt->bindParam(":cityName", $name, PDO::PARAM_STR);
			$stmt->execute();
			$cities = array();
			while($row=$stmt->fetch()){
				$cities[] = $this->commentFromRow($row);
				}
			return $cities;
		}catch(PDOException $e){
			echo'exception';
			return false;
		}
		return null;

}

public function returnCityFromRow(){

	return array(CITY_ID=>$row[CITY_ID], CITY_NAME=>$row[CITY_NAME], CITY_STATE=>$row[CITY_STATE],CITY_COUNTRY=>$row[CITY_COUNTRY]);

	}

