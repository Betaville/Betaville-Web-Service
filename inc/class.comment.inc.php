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
class CommentActions{
	
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
	
	public function addComment($designID, $user, $comment, $repliesTo){
		$sql = 'INSERT INTO '.COMMENT_TABLE.'(designID, user, comment, date, repliesTo) VALUES (:designID, :user, :comment, NOW(), :repliesTo)';
		try{
			$stmt = $this->_db->prepare($sql);
			$stmt->bindParam(":designID", $designID, PDO::PARAM_INT);
			$stmt->bindParam(":user", $user, PDO::PARAM_STR);
			$stmt->bindParam(":comment", stripslashes($comment), PDO::PARAM_STR);
			$stmt->bindParam(":repliesTo", $repliesTo, PDO::PARAM_INT);
			$stmt->execute();
			return true;
		}catch(PDOException $e){
			return false;
		}
	}


	public function getOnlyUserNotification($user){
		$sql = 'SELECT * FROM '.COMMENT_TABLE.' WHERE user="'.$user.'" ORDER BY commentid DESC LIMIT 5';
		try{
			$stmt = $this->_db->prepare($sql);
			$stmt->execute();
			$comments = array();
			while($row=$stmt->fetch()){
				// only include this result if it hasn't been verified as spam
				if($row[COMMENT_SPAMVERIFIED]==0){
					$comments[] = $this->commentFromRow($row, COMMENT_USER, COMMENT_DATE);
				}
			}
			return $comments;
		}catch(PDOException $e){
			echo'exception';
			return false;
		}
		return null;
	}
	
	public function getCommentsForDesign($id){
		$sql = 'SELECT * FROM '.COMMENT_TABLE.' WHERE '.COMMENT_SPAMVERIFIED.' = 0 AND '.COMMENT_DESIGN.'=:designID';
		try{
			$stmt = $this->_db->prepare($sql);
			$stmt->bindParam(":designID", $id, PDO::PARAM_INT);
			$stmt->execute();
			$comments = array();
			while($row=$stmt->fetch()){
				// only include this result if it hasn't been verified as spam
				if($row[COMMENT_SPAMVERIFIED]==0){
					$comments[] = $this->commentFromRow($row, COMMENT_USER, COMMENT_DATE);
				}
			}
			return $comments;
		}catch(PDOException $e){
			echo'exception';
			return false;
		}
		return null;
	}
	
	public function getRecentComments($start, $end){
		$sql = 'SELECT * FROM '.COMMENT_TABLE.' WHERE '.COMMENT_SPAMVERIFIED.' = 0 ORDER BY '.COMMENT_ID.' DESC LIMIT :start, :end';
		try{
			$stmt = $this->_db->prepare($sql);
			$stmt->bindParam(":start", $start, PDO::PARAM_INT);
			$stmt->bindParam(":end", $end, PDO::PARAM_INT);
			$stmt->execute();
			$comments = array();
			while($row=$stmt->fetch()){
				// only include this result if it hasn't been verified as spam
				if($row[COMMENT_SPAMVERIFIED]==0){
					$comments[] = $this->commentFromRow($row, COMMENT_USER, COMMENT_DATE);
				}
			}
			return $comments;
		}catch(PDOException $e){
			echo'exception';
			return false;
		}
		return null;
	}
	
	//Function to retrieve comments only for a specific user
	public function getNotificationsForUser($username){
		$sql = 'SELECT *, '.COMMENT_TABLE.'.'.COMMENT_USER.' AS commentuser, '.DESIGN_TABLE.'.'.DESIGN_USER.' AS designuser, '.COMMENT_TABLE.'.'.COMMENT_DATE.' AS commentdate FROM '.COMMENT_TABLE.' JOIN '.DESIGN_TABLE.' ON '.COMMENT_TABLE.'.'.COMMENT_DESIGN.' = '.DESIGN_TABLE.'.'.DESIGN_ID.' WHERE '.DESIGN_TABLE.'.'.DESIGN_IS_ALIVE.'=1 AND ('.DESIGN_TABLE.'.'.DESIGN_FAVE_LIST.' LIKE :wildcardname OR '.DESIGN_TABLE.'.'.DESIGN_USER.' LIKE :username OR '.COMMENT_TABLE.'.'.COMMENT_USER.' LIKE :username) ORDER BY '.COMMENT_ID.' DESC';
		$wildcardName = '%'.$username.'%';
			try{
			$stmt = $this->_db->prepare($sql);
			$stmt->bindParam(":wildcardname", $wildcardName, PDO::PARAM_STR);
			$stmt->bindParam(":username", $username, PDO::PARAM_STR);
			$stmt->execute();
			$comments = array();
			while($row=$stmt->fetch()){
				// only include this result if it hasn't been verified as spam
				if($row[COMMENT_SPAMVERIFIED]==0){
					$comments[] = $this->commentFromRow($row, 'commentuser', 'commentdate');
				}
			}
			return $comments;
		}catch(PDOException $e){
			echo'exception';
			return false;
		}
		return null;
	}
	
	private function commentFromSpecificRow($row) {
		return array(COMMENT_ID=>$row[COMMENT_ID], COMMENT_DESIGN=>$row[COMMENT_DESIGN], DESIGN_NAME=>$row[DESIGN_NAME], DESIGN_FILE=>$row[DESIGN_FILE], COMMENT_USER=>$row[COMMENT_USER], COMMENT_TEXT=>$row[COMMENT_TEXT], COMMENT_DATE=>$row[COMMENT_DATE], COMMENT_REPLIESTO=>$row[COMMENT_REPLIESTO]);
}
	private function commentFromRow($row, $commentUserAlias, $commentDateAlias){
		return array(COMMENT_ID=>$row[COMMENT_ID], COMMENT_DESIGN=>$row[COMMENT_DESIGN], COMMENT_USER=>$row[$commentUserAlias],COMMENT_TEXT=>$row[COMMENT_TEXT],
		COMMENT_DATE=>$row[$commentDateAlias],COMMENT_REPLIESTO=>$row[COMMENT_REPLIESTO]);
	}
}
?>
