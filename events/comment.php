<?php 
require_once("../db/class.mysql.connect.php");

$application->get('/list/:cdevent','listComments');
$application->post('/add','addEvent');
$application->get('/delete/:cdcomment/:cdevent','deleteComment');

function listComments($cdevent){
	$where = "where c.cdevent = $cdevent";
	
	$sql =  "SELECT c.cdcomment,e.cdevent,c.nmcomment,c.dtcommentdate,c.numupvote, c.numdownvote, c.cduser, u.nmuser, u.blthumbnail as bluserthumb ".
			"FROM evcomment c ".
			"INNER JOIN aduser as u on c.cduser = u.cduser ".
			"INNER JOIN evevent as e on c.cdevent = e.cdevent ".
			"$where";
	
	$data = runSql($sql);
	echo json_encode($data,JSON_UNESCAPED_UNICODE);
}


function addEvent(){

	
	$cdevent = $comment->cdevent;
	$cduser = $comment->cduser;
	$nmcomment = $comment->nmcomment;
	$dtcommentdate = $comment->dtcommentdate;
	$numupvote = $comment->numupvote;
	$numdownvote = $comment->numdownvote;
	
	$sql =  "INSERT INTO EVCOMMENT (nmcomment,dtcommentdate,numupvote, numdownvote, cduser, cdevent) ".
			"VALUES('$nmcomment','$dtcommentdate',$numupvote, $numdownvote, $cduser, $cdevent)";
			
	$db = new db;
	$result=$db->runQuery($sql);
	
	listComments($cdevent);
}


function deleteComment($cdcomment,$cdevent){
			
	$sql =  "DELETE FROM EVCOMMENT WHERE cdcomment = $cdcomment";
	
	$db = new db;
	$result=$db->runQuery($sql);
	
	listComments($cdevent);
}

function runSql($sql){
	$db = new db;
	$result=$db->runQuery($sql);
	
	$data = array();
	$entity = array();
	while($row=$db->fetchArray($result)){
		
		$data[] = $row;
	}
	return $data;
}

$application->run();
?> 
