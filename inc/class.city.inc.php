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
* Actions for interaction with city Table
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

	public function addCity($name, $state, $country){
		$sql = 'INSERT INTO '.CITY_TABLE.'(cityName, state, country) VALUES (:name, :state, :country)';
		try{
			$stmt = $this->_db->prepare($sql);
			$stmt->bindParam(":name", $name, PDO::PARAM_STR);
			$stmt->bindParam(":state", $state, PDO::PARAM_STR);
			$stmt->bindParam(":country", $country, PDO::PARAM_STR);
			$stmt->execute();
			return true;
		}catch(PDOException $e){
			return false;
		}
	}

	//Returns array of all the city parameters querying the entire city table
	public function findAllCity(){

		$sql = 'SELECT * FROM '.CITY_TABLE;
		try{
			$stmt = $this->_db->prepare($sql);
			$stmt->execute();
			$cities = array();
			while($row=$stmt->fetch()){
				$cities[] = $this->AllCityParameters($row);
			}
			return $cities;
		}catch(PDOException $e){
			echo'exception';
			return false;
		}
		return null;
	}

	//Returns array of cityname querying on cityId
	public function findCityByID($id){

		$sql = 'SELECT * FROM '.CITY_TABLE.' WHERE '.CITY_TABLE.'.'.CITY_ID.' = '.$id;
		try{
			$stmt = $this->_db->prepare($sql);
			$stmt->execute();
			$cities = array();
			while($row=$stmt->fetch()){
				$cities[] = $this->cityName($row);
			}
			return $cities;
		}catch(PDOException $e){
			echo'exception';
			return false;
		}
		return null;
	}

	//Returns array of cityname querying on cityname,statename and countryname
	public function findCityByAll($cityname,$statename,$countryname){

		$cityname = '%'.$cityname.'%';
		$statename = '%'.$statename.'%';
		$countryname = '%'.$countryname.'%';
		$sql = 'SELECT * FROM '.CITY_TABLE.' WHERE '.CITY_TABLE.'.'.CITY_NAME.' LIKE :cityName OR '.CITY_TABLE.'.'.CITY_STATE.' LIKE :cityState OR '.CITY_TABLE.'.'.CITY_COUNTRY.' LIKE :cityCountry';
		try{
			$stmt = $this->_db->prepare($sql);
			$stmt->bindParam(":cityName", $cityname, PDO::PARAM_STR);
			$stmt->bindParam(":cityState", $statename, PDO::PARAM_STR);
			$stmt->bindParam(":cityCountry", $countryname, PDO::PARAM_STR);
			$stmt->execute();
			$cities = array();
			while($row=$stmt->fetch()){
				$cities[] = $this->cityName($row);
			}
			return $cities;
		}catch(PDOException $e){
			echo'exception';
			return false;
		}
		return null;
	}


	//Returns array of cityID's querying on statename
	public function findCityByState($name){

		$name = '%'.$name.'%';
		$sql = 'SELECT * FROM '.CITY_TABLE.' WHERE '.CITY_TABLE.'.'.CITY_STATE.' LIKE :cityState';
		try{
			$stmt = $this->_db->prepare($sql);
			$stmt->bindParam(":cityState", $name, PDO::PARAM_STR);
			$stmt->execute();
			$cities = array();
			while($row=$stmt->fetch()){
				$cities[] = $this->cityID($row);
			}
			return $cities;
		}catch(PDOException $e){
			echo'exception';
			return false;
		}
		return null;

	}

	//Returns array of cityID's querying on country name
	public function findCityByCountry($name){

		$name = '%'.$name.'%';
		$sql = 'SELECT * FROM '.CITY_TABLE.' WHERE '.CITY_TABLE.'.'.CITY_COUNTRY.' LIKE :cityCountry';
		try{
			$stmt = $this->_db->prepare($sql);
			$stmt->bindParam(":cityCountry", $name, PDO::PARAM_STR);
			$stmt->execute();
			$cities = array();
			while($row=$stmt->fetch()){
				$cities[] = $this->cityID($row);
			}
			return $cities;
		}catch(PDOException $e){
			echo'exception';
			return false;
		}
		return null;

	}


	//Returns array of cityID's querying on cityname
	public function findCityByName($name){

		$name = '%'.$name.'%';
		$sql = 'SELECT * FROM '.CITY_TABLE.' WHERE '.CITY_TABLE.'.'.CITY_NAME.' LIKE :cityName';
		try{
			$stmt = $this->_db->prepare($sql);
			$stmt->bindParam(":cityName", $name, PDO::PARAM_STR);
			$stmt->execute();
			$cities = array();
			while($row=$stmt->fetch()){
				$cities[] = $this->cityID($row);
			}
			return $cities;
		}catch(PDOException $e){
			echo'exception';
			return false;
		}
		return null;

	}

	//Private functions from used by above functions depending on request by JSONClientManager
	private function cityName($row){

		return array(CITY_NAME=>$row[CITY_NAME]);

	}

	private function cityID($row){

		return array(CITY_ID=>$row[CITY_ID]);

	}

	private function allCityParameters($row){

		return array(CITY_ID=>$row[CITY_ID], CITY_NAME=>$row[CITY_NAME], CITY_STATE=>$row[CITY_STATE],CITY_COUNTRY=>$row[CITY_COUNTRY]);

	}
}
