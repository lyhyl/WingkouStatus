<?php
function queryBAESQL($sql)
{
	$dbname = "IyropdxNjTcyGocxBRPJ";
	$host = 'sqld.duapp.com';
	$port = 4050;
	$user = '2bcb3e419d374573a1f30985225d9125';
	$pwd = '62dd83c50d4e4b7bb8e83adcc9df86d2';
	
	$link = @mysql_connect("{$host}:{$port}",$user,$pwd,true);
	if(!$link)
	{
		return ("Connect Server Failed: " . mysql_error());
	}
	if(!mysql_select_db($dbname,$link))
	{
		return ("Select Database Failed: " . mysql_error($link));
	}
	return mysql_query($sql,$link);
}
?>