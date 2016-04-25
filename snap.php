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
			$json=json_decode($postStr,true);
			$tasks=$json["tasks"];
			$atime=$json["atime"];
			queryBAESQL("INSERT INTO `{$GLOBALS['dbname']}`.`{$GLOBALS['tbname']}` (`Time`,`Desc`,`ATime`) VALUES (NOW(),'{$tasks}','{$atime}');");
			echo "succ";
		}
		else
		{
			echo $postStr;
		}
	}
}
?>