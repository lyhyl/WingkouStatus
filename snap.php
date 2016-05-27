<?php
require_once "baesql.php";

$taker = new SnapTaker();
$taker->take();

class SnapTaker
{
	public function take()
	{
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
		if (!empty($postStr))
		{
			$json = json_decode($postStr,true);
			$tasks = $json["tasks"];
			$atime = $json["atime"];
			$cmdTemp = "INSERT INTO `%s` (`Time`,`Desc`,`ATime`) VALUES (NOW(),'%s','%s')";
			$cmd = sprintf($cmdTemp,$GLOBALS['snapTbName'],$tasks,$atime);
			queryBAESQL($cmd);
			echo "succ";
		}
		else
		{
			echo "err";
		}
	}
}
?>