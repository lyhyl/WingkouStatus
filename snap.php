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
			queryBAESQL("INSERT INTO `{$GLOBALS['dbname']}`.`{$GLOBALS['tbname']}` (`Time`,`Desc`) VALUES (NOW(),'{$postStr}');");
			echo "succ";
		}
		else
		{
			echo "err";
		}
	}
}
?>