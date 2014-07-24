<?php 
require_once("../db/class.mysql.connect.php");

$application->contentType("application/json; charset=utf-8");
$application->get('/liked/:cduser','likedList');
$application->get('/like/:cdevent/:cduser','likeEvent');
$application->get('/unlike/:cdevent/:cduser','unlikeEvent');
$application->get('/is_like/:cdevent/:cduser','isUserLike');
$application->get('/is_unlike/:cdevent/:cduser','isUserUnlike');

	function likeEvent($cdevent,$cduser){
		$db = new db;
		$where = "where cdevent = $cdevent and cduser = $cduser ";
		
		$sql = "SELECT * FROM EVEVENTLIKE $where";
		
		$data = runSql($sql);
		
		if(count($data)<1){
			$sql = "INSERT INTO EVEVENTLIKE(cduser,cdevent) VALUES($cduser, $cdevent)";
			$db->runQuery($sql);
			$sql = "DELETE FROM EVEVENTUNLIKE WHERE cduser=$cduser AND cdevent=$cdevent";
			$db->runQuery($sql);
			
		}
		listById($cdevent);
	}


	function unlikeEvent($cdevent,$cduser){
		$db = new db;
		$where = "where cdevent = $cdevent and cduser = $cduser ";
		
		$sql = "SELECT * FROM EVEVENTUNLIKE $where";
		
		$data = runSql($sql);
		
		if(count($data)<1){
			$sql = "INSERT INTO EVEVENTUNLIKE(cduser,cdevent) VALUES($cduser, $cdevent)";
			$db->runQuery($sql);
			$sql = "DELETE FROM EVEVENTLIKE WHERE cduser=$cduser AND cdevent=$cdevent";
			$db->runQuery($sql);
			
		}
		listById($cdevent);
	}


	function isUserLike($cdevent,$cduser){
		$db = new db;
		$where = "where cdevent = $cdevent and cduser = $cduser ";
		
		$sql = "SELECT cduser as cdautocomplete FROM EVEVENTLIKE $where";
		
		$data = runSql($sql);
		echo json_encode($data,JSON_UNESCAPED_UNICODE);
		
	}



	function isUserUnlike($cdevent,$cduser){
		$db = new db;
		$where = "where cdevent = $cdevent and cduser = $cduser ";
		
		$sql = "SELECT cduser as cdautocomplete FROM EVEVENTUNLIKE $where";
		
		$data = runSql($sql);
		echo json_encode($data,JSON_UNESCAPED_UNICODE);
		
	}



function likedList($cduser){
	$where = "";
	
	$where = "where EL.cduser= $cduser";
	
	$sql = 	"SELECT e.cdevent,e.nmevent,e.nmexplanation,e.cduser,e.cdcategory,e.dtstartdate,e.tmstarttime,e.dtfinishdate,e.tmfinishtime,e.nmaddress,e.cdcounty ,e.blthumbnail, ACI.cdcity, a.numofattend,l.numoflike, u.numofunlike, l.numoflike-u.numofunlike rate ".
			"FROM evevent e".
			" INNER JOIN (SELECT COUNT(l.cdevent) as numoflike, e.cdevent FROM evevent e LEFT JOIN eveventlike l ON l.cdevent = e.cdevent group by e.cdevent) as l on l.cdevent = e.cdevent".
			" INNER JOIN (SELECT COUNT(u.cdevent) as numofunlike, e.cdevent FROM evevent e LEFT JOIN eveventunlike u ON u.cdevent = e.cdevent group by e.cdevent) as u on u.cdevent = e.cdevent".
			" INNER JOIN (SELECT COUNT(a.cdevent) as numofattend, e.cdevent FROM evevent e LEFT JOIN eveventattend a ON a.cdevent = e.cdevent group by e.cdevent) as a on a.cdevent = e.cdevent".
			" INNER JOIN ADCOUNTY  ACO ON ACO.CDCOUNTY = E.CDCOUNTY ".
			" INNER JOIN ADCITY ACI ON ACI.CDCITY = ACO.CDCITY ".
			" INNER JOIN EVEVENTLIKE EL ON EL.CDEVENT = E.CDEVENT ".
			" $where";
	
	$data = runSql($sql);
	echo json_encode($data,JSON_UNESCAPED_UNICODE);
	
}
function runSql($sql){
	$db = new db;
	$result=$db->runQuery($sql);
	
	$data = array();
	$entity = array();
	while($row=$db->fetchArray($result)){
		foreach($row as $key => $value){
			if($key =="blthumbnail"||$key =="image")
				$entity[$key] = base64_encode($value);
			else
				$entity[$key] = $value;
		}
		$data[] = $entity;
	}
	return $data;
}

function str_lreplace($search, $replace, $subject)
{
    $pos = strrpos($subject, $search);

    if($pos !== false)
    {
        $subject = substr_replace($subject, $replace, $pos, strlen($search));
    }

    return $subject;
}
function listById($cdevent=""){
	$where = "";
	if($cdevent!=null){
		$where = "where e.cdevent= $cdevent";
	}
	$sql = 	"SELECT e.cdevent,e.nmevent,e.nmexplanation,e.cduser,e.cdcategory,e.dtstartdate,e.tmstarttime,e.dtfinishdate,e.tmfinishtime,e.nmaddress,e.cdcounty ,e.blthumbnail, ACI.cdcity, a.numofattend,l.numoflike, u.numofunlike, l.numoflike-u.numofunlike rate ".
			" FROM evevent e".
			" INNER JOIN (SELECT COUNT(l.cdevent) as numoflike, e.cdevent FROM evevent e LEFT JOIN eveventlike l ON l.cdevent = e.cdevent group by e.cdevent) as l on l.cdevent = e.cdevent".
			" INNER JOIN (SELECT COUNT(u.cdevent) as numofunlike, e.cdevent FROM evevent e LEFT JOIN eveventunlike u ON u.cdevent = e.cdevent group by e.cdevent) as u on u.cdevent = e.cdevent".
			" INNER JOIN (SELECT COUNT(a.cdevent) as numofattend, e.cdevent FROM evevent e LEFT JOIN eveventattend a ON a.cdevent = e.cdevent group by e.cdevent) as a on a.cdevent = e.cdevent".
			" INNER JOIN ADCOUNTY  ACO ON ACO.CDCOUNTY = E.CDCOUNTY ".
			" INNER JOIN ADCITY ACI ON ACI.CDCITY = ACO.CDCITY ".
			" $where";
	
	$data = runSql($sql);
	echo json_encode($data,JSON_UNESCAPED_UNICODE);
}
$application->run();
?> 
