<?php 
require_once("../db/class.mysql.connect.php");
$application->contentType("x-www-form-urlencoded; charset=utf-8");
$application->get('/city/:code','cityNiceName');
$application->get('/county/:code','countyNiceName');
$application->get('/category/:code','categoryNiceName');

function cityNiceName($code){
	
	$where = "where ct.cdcity = $code";
	
	$data = runSql("SELECT ct.nmcity as nmautocomplete, ct.cdcity as cdautocomplete FROM adcity ct $where");
	echo json_encode($data,JSON_UNESCAPED_UNICODE);
}

function countyNiceName($code){
	
	$where = "where ct.cdcounty = $code";
					
	$data = runSql("SELECT ct.nmcounty as nmautocomplete,ct.cdcounty as cdautocomplete FROM adcounty ct $where");
	echo json_encode($data,JSON_UNESCAPED_UNICODE);
	
}

function categoryNiceName($code){
	
	$where = "where ct.cdcategory = $code";
					
	$data = runSql("SELECT ct.nmcategory as nmautocomplete, ct.cdcategory as cdautocomplete FROM evcategory ct $where");
	echo json_encode($data,JSON_UNESCAPED_UNICODE);
}

function runSql($sql){
	$db = new db;
	$result=$db->runQuery($sql);
		
	$data = array();
	while($row=$db->fetchArray($result)){
		$data[] = $row;
	}
	return $data;
}
$application->run();
?> 

