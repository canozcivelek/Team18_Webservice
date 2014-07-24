<?php 
require_once("../db/class.mysql.connect.php");
$application->get('/list(/)(/:cdevent)','listById');
$application->post('/create(/)(/:event)','createEvent');
$application->post('/search(/)(/:event)','searchEvent');
$application->delete('/delete/:cdevent','deleteEvent');
	
function listById($cdevent=""){
	$where = "";
	if($cdevent!=null){
		$where = "where e.cdevent= $cdevent";
	}
	else
	{
		$where = "where e.dtstartdate > NOW() - INTERVAL 8 DAY";
	}
	
	$sql = 	"SELECT e.cdevent,e.nmevent,e.nmexplanation,e.cduser,e.cdcategory,e.dtstartdate,e.tmstarttime,e.dtfinishdate,e.tmfinishtime,e.nmaddress,e.cdcounty ,e.blthumbnail, ACI.cdcity, a.numofattend,l.numoflike, u.numofunlike, l.numoflike-u.numofunlike rate ".
			" FROM evevent e".
			" INNER JOIN (SELECT COUNT(l.cdevent) as numoflike, e.cdevent FROM evevent e LEFT JOIN eveventlike l ON l.cdevent = e.cdevent group by e.cdevent) as l on l.cdevent = e.cdevent".
			" INNER JOIN (SELECT COUNT(u.cdevent) as numofunlike, e.cdevent FROM evevent e LEFT JOIN eveventunlike u ON u.cdevent = e.cdevent group by e.cdevent) as u on u.cdevent = e.cdevent".
			" INNER JOIN (SELECT COUNT(a.cdevent) as numofattend, e.cdevent FROM evevent e LEFT JOIN eveventattend a ON a.cdevent = e.cdevent group by e.cdevent) as a on a.cdevent = e.cdevent".
			" INNER JOIN ADCOUNTY  ACO ON ACO.CDCOUNTY = E.CDCOUNTY ".
			" INNER JOIN ADCITY ACI ON ACI.CDCITY = ACO.CDCITY ".
			" $where".
			" ORDER BY e.dtstartdate desc,e.tmstarttime desc";
	
	$data = runSql($sql);
	echo json_encode($data,JSON_UNESCAPED_UNICODE);
}



function createEvent($event=""){
		
		$nmevent = $event->nmevent;
		$nmexplanation = $event->nmexplanation;
		$dtstartdate = $event->dtstartdate;
		$tmstarttime = $event->tmstarttime;
		$dtfinishdate = $event->dtfinishdate;
		$tmfinishtime = $event->tmfinishtime;
		$nmaddress = $event->nmaddress;
		$cdcounty = $event->cdcounty;
		$cdcategory = $event->cdcategory;
		$cdowner = $event->cdowner;
		$db = new db;
		
		if(array_key_exists ( 'blthumbnail' , $event )){
			$blthumbnail = $event->blthumbnail;
			$sql = "INSERT INTO EVEVENT (nmevent,nmexplanation,cduser,cdcategory,dtstartdate,tmstarttime,dtfinishdate,tmfinishtime,nmaddress,cdcounty,blthumbnail) ".
				"values('$nmevent','$nmexplanation',$cdowner,$cdcategory,'$dtstartdate','$tmstarttime','$dtfinishdate','$tmfinishtime','$nmaddress',$cdcounty,FROM_BASE64('$blthumbnail'))";
		}
		else				
			$sql = "INSERT INTO EVEVENT (nmevent,nmexplanation,cduser,cdcategory,dtstartdate,tmstarttime,dtfinishdate,tmfinishtime,nmaddress,cdcounty,blthumbnail) ".
				"values('$nmevent','$nmexplanation',$cdowner,$cdcategory,'$dtstartdate','$tmstarttime','$dtfinishdate','$tmfinishtime','$nmaddress',$cdcounty,null)";
		
		
		$result=$db->runQuery($sql);
		
		$data = array(array("status"=>"Event successfully created"));
		echo json_encode($data,JSON_UNESCAPED_UNICODE);
	}



	function searchEvent($event=""){
		$request = Slim\Slim::getInstance()->request();
		$event = json_decode($request->getBody());
		
		$cdowner = $event->cdowner;
		$nmevent = $event->nmevent;
		$nmexplanation = $event->nmexplanation;
		$dtstartdate = explode('__', $event->dtstartdate);
		$tmstarttime = explode('__', $event->tmstarttime);
		$dtfinishdate = explode('__', $event->dtfinishdate);
		$tmfinishtime = explode('__', $event->tmfinishtime);
		$nmaddress = $event->nmaddress;
		$cdcity = $event->cdcity;
		$cdcounty = $event->cdcounty;
		$cdcategory = $event->cdcategory;
		$cduser = $event->cduser;
		
		/****************
		* where clouse	*
		*****************/
		$where = "where";
		
		
		if(trim($nmevent) != "" && $nmevent!=null)
			$where .= " lower(nmevent) like lower('%$nmevent%') and ";
			
		if(trim($nmexplanation) != "" && $nmexplanation!=null)
			$where .= " lower(nmexplanation) like lower('%$nmexplanation%')" ." and ";			
		if(trim($nmaddress) != "" && $nmaddress!=null)
			$where .= " lower(nmaddress) like lower('%$nmaddress%')" ." and ";
			
		if(trim($cdcounty) != "" && $cdcounty!=null && $cdcounty!=0)
			$where .= " cdcounty = $cdcounty" ." and ";
			
		if(trim($cdcategory) != "" && $cdcategory!=null && $cdcategory!=0)
			$where .= " cdcategory = $cdcategory" ." and ";
			
		if(trim($cdcity) != "" && $cdcity!=null && $cdcity!=0)
			$where .= " cdcity = $cdcity" ." and ";
			
		if(trim($cduser) != "" && $cduser!=null && $cduser!=0)
			$where .= " cduser = $cduser" ." and ";
			
		if(trim($dtstartdate[1]) != "" && $dtstartdate[1]!=null)
			$where .= " DATE_FORMAT(dtstartdate, '%d/%m/%Y')  $dtstartdate[0] '$dtstartdate[1]'" ." and ";
		
		if(trim($tmstarttime[1]) != "" && $tmstarttime[1]!=null)
			$where .= " tmstarttime $tmstarttime[0] '$tmstarttime[1]'" ." and ";
		
		if(trim($dtfinishdate[1]) != "" && $dtfinishdate[1]!=null)
			$where .= " DATE_FORMAT(dtfinishdate, '%d/%m/%Y') $dtfinishdate[0] '$dtfinishdate[1]'" ." and ";
			
		if(trim($tmfinishtime[1]) != "" && $tmfinishtime[1]!=null)
			$where .= " tmfinishtime $tmfinishtime[0] '$tmfinishtime[1]'" ." and ";
		
		$where = str_lreplace("and", " ", $where);
		
		$sql = "SELECT * FROM ( ".
				"SELECT 'successful' as requeststatus,e.cdevent,e.nmevent,e.nmexplanation,e.cduser,e.cdcategory,e.dtstartdate,e.tmstarttime,e.dtfinishdate,e.tmfinishtime,e.nmaddress,e.cdcounty,ACI.cdcity, e.blthumbnail FROM EVEVENT E ".
				"INNER JOIN ADCOUNTY ACO ON ACO.CDCOUNTY = E.CDCOUNTY ".
				"INNER JOIN ADCITY ACI ON ACI.CDCITY = ACO.CDCITY ".
				") T $where ".
				" ORDER BY dtstartdate desc,tmstarttime desc";
		
		
		
		$data = runSql($sql);
		
		if(count($data)<1)
				$data = array(array("requeststatus"=>"No event found!"));
		echo json_encode($data,JSON_UNESCAPED_UNICODE);
	}



	function deleteEvent($cdevent){
	
		$db = new db;
		
		$sql = "DELETE FROM EVEVENT WHERE cdevent = $cdevent";
		$result=$db->runQuery($sql);
		
		$data = array(array("requeststatus"=>"Event successfully deteleted"));
		echo json_encode($data,JSON_UNESCAPED_UNICODE);
	}


$application->run();

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

function str_lreplace($search, $replace, $subject)
{
    $pos = strrpos($subject, $search);

    if($pos !== false)
    {
        $subject = substr_replace($subject, $replace, $pos, strlen($search));
    }

    return $subject;
}
?> 

