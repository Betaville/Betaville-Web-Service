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
 * Representation of a UTM coordinate
 * @author Skye Book
 */
class UTM{
	
	private $latZone;
	private $lonZone;
	private $northing;
	private $easting;

	public function __construct(){
	}
	
	public function setLatZone($latZone){
		$this->latZone=$latZone;
	}
	
	public function setLonZone($lonZone){
		$this->lonZone=$lonZone;
	}
	
	public function setNorthing($northing){
		$this->northing=$northing;
	}
	
	public function setEasting($easting){
		$this->easting=$easting;
	}
	
	public function getLatZone(){
		return $this->latZone;
	}
	
	public function getLonZone(){
		return $this->lonZone;
	}
	
	public function getNorthing(){
		return $this->northing;
	}
	
	public function getEasting(){
		return $this->easting;
	}
	
	public function getEPSGCode(){
		$epsgCode = "EPSG:";
		
		// if the lat zone is N or higher, we're in the northern hemisphere
		if($this->latZone > "M"){
		
		}
		else{
		
		}
		echo "";
		return $epsgCode;
	}
}
?>