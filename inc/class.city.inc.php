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
 * Actions for interacting with comments
 * @author Skye Book
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

public function findCityByName($name){

	$name='%'.$name.'%';
	$sql='SELECT * FROM '.CITY_TABLE.' WHERE '.CITY_TABLE.'.'.CITY_NAME.' LIKE :cityName';
		try{
			$stmt = $this->_db->prepare($sql);
			$stmt->bindParam(":cityName", $name, PDO::PARAM_STR);
			$stmt->execute();
			$cities = array();
			while($row=$stmt->fetch()){
				$cities[] = $this->cityFromRow($row);
				}
			return $cities;
		}catch(PDOException $e){
			echo'exception';
			return false;
		}
		return null;

}

private function cityFromRow($row){

	return array(CITY_ID=>$row[CITY_ID], CITY_NAME=>$row[CITY_NAME], CITY_STATE=>$row[CITY_STATE],CITY_COUNTRY=>$row[CITY_COUNTRY]);

	}
}
