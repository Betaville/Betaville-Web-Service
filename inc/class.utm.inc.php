<?php

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