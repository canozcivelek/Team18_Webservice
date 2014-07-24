<?php 
require_once("../db/class.mysql.connect.php");

$application->contentType("application/json; charset=utf-8");
$application->post('/authenticate','authenticateUser');


function authenticateUser(){
	
	$user = json_decode($request->getBody());
	
	$nmloginname = $user->nmloginname;
	$nmpassword = $user->nmpassword;
	
	$data = array();		
	if($nmloginname!=null&&$nmpassword){
		
		$db = new db;
		$result=$db->runQuery("select nmuser from aduser where nmloginname='$nmloginname'");
		while($row=$db->fetchArray($result)){
			$data[] = $row;
		}
		
		if(count($data)<1)
			$data = array(array("requeststatus"=>"User not found!"));
		else{
			$data = array();
			$where = "where u.nmpassword='$nmpassword' and u.nmloginname = '$nmloginname' ";
			$sql = "SELECT 'successful' as requeststatus,u.cduser,u.nmuser,u.cdrole,u.nmloginname,r.nmrole,u.blthumbnail FROM aduser u ".
			"inner join adrole r on r.cdrole = u.cdrole ".
			"$where";
			
			$data=runSql($sql);
			if(count($data)<1)
				$data = array(array("requeststatus"=>"Invalid password!"));
		}
	}
	
	echo json_encode($data,JSON_UNESCAPED_UNICODE);		
	//echo json_last_error();//json_encode($data,JSON_UNESCAPED_UNICODE);
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
