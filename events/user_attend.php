<?php 
require_once("../db/class.mysql.connect.php");

$application->contentType("application/json; charset=utf-8");
$application->get('/attended/:cduser','attendedList');
$application->get('/attend/:cdevent/:cduser','attendEvent');
$application->get('/is_attend/:cdevent/:cduser','isUserAttend');


	function attendEvent($cdevent,$cduser){
		$db = new db;
		$where = "where cdevent = $cdevent and cduser = $cduser ";
		
		$sql = "SELECT * FROM EVEVENTATTEND $where";
		
		$data = runSql($sql);
		
		if(count($data)<1){
			$sql = "INSERT INTO EVEVENTATTEND(cduser,cdevent) VALUES($cduser, $cdevent)";
			$db->runQuery($sql);
			
		}
		else{
			$sql = "DELETE FROM EVEVENTATTEND WHERE cduser=$cduser AND cdevent=$cdevent";
			$db->runQuery($sql);
		}
		listById($cdevent);
	}


	function isUserAttend($cdevent,$cduser){
		$db = new db;
		$where = "where cdevent = $cdevent and cduser = $cduser ";
		
		$sql = "SELECT cduser as cdautocomplete FROM EVEVENTATTEND $where";
		
		$data = runSql($sql);
		echo json_encode($data,JSON_UNESCAPED_UNICODE);
		
	}

function attendedList($cduser){
	$where = "";
	
	$where = "where EA.cduser= $cduser";
	
	$sql = 	"SELECT e.cdevent,e.nmevent,e.nmexplanation,e.cduser,e.cdcategory,e.dtstartdate,e.tmstarttime,e.dtfinishdate,e.tmfinishtime,e.nmaddress,e.cdcounty ,e.blthumbnail, ACI.cdcity, a.numofattend,l.numoflike, u.numofunlike, l.numoflike-u.numofunlike rate ".
			" FROM EVEVENT E ".
			" INNER JOIN (SELECT COUNT(l.cdevent) as numoflike, e.cdevent FROM evevent e LEFT JOIN eveventlike l ON l.cdevent = e.cdevent group by e.cdevent) as l on l.cdevent = e.cdevent".
			" INNER JOIN (SELECT COUNT(u.cdevent) as numofunlike, e.cdevent FROM evevent e LEFT JOIN eveventunlike u ON u.cdevent = e.cdevent group by e.cdevent) as u on u.cdevent = e.cdevent".
			" INNER JOIN (SELECT COUNT(a.cdevent) as numofattend, e.cdevent FROM evevent e LEFT JOIN eveventattend a ON a.cdevent = e.cdevent group by e.cdevent) as a on a.cdevent = e.cdevent".
			" INNER JOIN ADCOUNTY  ACO ON ACO.CDCOUNTY = E.CDCOUNTY ".
			" INNER JOIN ADCITY ACI ON ACI.CDCITY = ACO.CDCITY ".
			" INNER JOIN EVEVENTATTEND EA ON EA.CDEVENT = E.CDEVENT ".
			" $where".
			" ORDER BY e.dtstartdate desc,e.tmstarttime desc";
	
	
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
