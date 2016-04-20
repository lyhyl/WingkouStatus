<?php
require "baesql.php";
$taker = new SnapTaker();
$taker->take();

class SnapTaker
{
	public function take()
	{
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
		if (!empty($postStr))
		{
			queryBAESQL("INSERT INTO `{$dbname}`.`{$tbname}` (`Time` ,`Desc`)VALUES (NOW() ,  '{$postStr}');");
		}
	}
}
?>