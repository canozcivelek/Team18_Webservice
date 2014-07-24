<?php 
require_once("../db/class.mysql.connect.php");

$application->contentType("x-www-form-urlencoded; charset=utf-8");
$application->get('/cities(/)(/:nmcity)','citiesAuthocomplete');
$application->get('/categories(/)(/:cdcategory)','categoriesAuthocomplete');
$application->get('/counties(/)(/:nmcounty)/:cdcity','countiesAuthocomplete');

function citiesAuthocomplete($nmcity=""){
	$where = "";
	
	if($nmcity!=null){
		$nmcity = str_replace("+"," ",$nmcity);
		$where = "where lower(ct.nmcity) like lower('$nmcity%')";
	}
			
	$data = runSql("SELECT ct.nmcity as nmautocomplete,ct.cdcity as cdautocomplete FROM adcity ct $where");
	echo json_encode($data,JSON_UNESCAPED_UNICODE);
}


function categoriesAuthocomplete($cdcategory=""){
	$where = "";		
	if($cdcategory!=null){
		$cdcategory = str_replace("+"," ",$cdcategory);
		$where = "where ect.cdcategory=$cdcategory";
	}
			
	$data = runSql("SELECT ect.nmcategory as nmautocomplete,ect.cdcategory as cdautocomplete FROM evcategory ect $where");
	echo json_encode($data,JSON_UNESCAPED_UNICODE);
}


function countiesAuthocomplete($nmcounty="",$cdcity){
	$where = "";
	
	if($nmcounty!=null){
		$nmcounty = str_replace("+"," ",$nmcounty);
		$where = "where lower(co.nmcounty) like lower('$nmcounty%') and ct.cdcity=$cdcity";
	}
	else			
		$where = "where ct.cdcity=$cdcity";
	
	$sql =  "SELECT co.nmcounty as nmautocomplete,co.cdcounty as cdautocomplete FROM adcounty co ".
			"INNER JOIN adcity ct on ct.cdcity = co.cdcity ". 
			"$where";
	
	$data = runSql($sql);
	echo json_encode($data,JSON_UNESCAPED_UNICODE);
}


function runSql($sql){
	$db = new db;
		$result=$db->runQuery($sql);
		
		$data = array();
		while($row=$db->fetchArray($result)){
			$data = $row;
		}
		return $data;
}
$application->run();
?> 

