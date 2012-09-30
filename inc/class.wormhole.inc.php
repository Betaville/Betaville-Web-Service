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
 * Actions for interacting with wormholes
 * @author Skye Book
 */
class WormholeActions{
	
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
	
	public function addWormhole($cityID, $coordinateID, $name){
		$sql = 'INSERT INTO '.WORMHOLE_TABLE.'(coordinateid, cityid, name, isAlive) VALUES (:coordinateID, :cityID, :name, 1)';
		try{
			$stmt = $this->_db->prepare($sql);
			$stmt->bindParam(":cityID", $cityID, PDO::PARAM_INT);
			$stmt->bindParam(":coordinateID", $coordinateID, PDO::PARAM_INT);
			$stmt->bindParam(":name", stripslashes($name), PDO::PARAM_STR);
			$stmt->execute();
			return true;
		}catch(PDOException $e){
			return false;
		}
	}
}
?>
