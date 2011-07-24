<?php

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
					$comments[] = $this->commentFromRow($row);
				}
			}
			return $comments;
		}catch(PDOException $e){
			echo'exception';
			return false;
		}
		return null;
	}
	
	public function getRecentComments($numberOfComments){
		// "SELECT * FROM " + DBConst.COMMENT_TABLE + " WHERE "+DBConst.COMMENT_SPAMVERIFIED+" = 0 ORDER BY "+DBConst.COMMENT_ID +" DESC LIMIT ?"
		$sql = 'SELECT * FROM '.COMMENT_TABLE.' WHERE '.COMMENT_SPAMVERIFIED.' = 0 ORDER BY '.COMMENT_ID.' DESC LIMIT :numberOfComments';
		try{
			$stmt = $this->_db->prepare($sql);
			$stmt->bindParam(":numberOfComments", $numberOfComments, PDO::PARAM_INT);
			$stmt->execute();
			$comments = array();
			while($row=$stmt->fetch()){
				// only include this result if it hasn't been verified as spam
				if($row[COMMENT_SPAMVERIFIED]==0){
					$comments[] = $this->commentFromRow($row);
				}
			}
			return $comments;
		}catch(PDOException $e){
			echo'exception';
			return false;
		}
		return null;
	}
	
	private function commentFromRow($row){
		$comment = array();
		$comment[] = array(COMMENT_ID=>$row[COMMENT_ID]);
		$comment[] = array(COMMENT_DESIGN=>$row[COMMENT_DESIGN]);
		$comment[] = array(COMMENT_USER=>$row[COMMENT_USER]);
		$comment[] = array(COMMENT_TEXT=>$row[COMMENT_TEXT]);
		$comment[] = array(COMMENT_DATE=>$row[COMMENT_DATE]);
		$comment[] = array(COMMENT_REPLIESTO=>$row[COMMENT_REPLIESTO]);
		return $comment;
	}
}
?>