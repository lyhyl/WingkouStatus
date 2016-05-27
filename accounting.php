<?php
require_once "baesql.php";

class Accountor
{
	private $tbname = "Accounting";
	function account($usr,$in)
	{
		$in = str_replace(array("$","¥","￥","＄"),"",$in);
		$data = preg_split('/[ \r\n]/',$in,-1,PREG_SPLIT_NO_EMPTY);
		$datalen = count($data);
		$cmdTemp = "INSERT INTO `%s` (`User`,`Time`,`Type`,`Money`) VALUES ('%s',NOW(),'%s',%s)";
		for($i = 0; $i + 1 < $datalen; $i += 2)
		{
			$cmd = sprintf($cmdTemp,$this->tbname,$usr,$data[$i],$data[$i + 1]);
			if(!queryBAESQL($cmd))
				return false;
		}
		return true;
	}
	function queryN($usr,$n)
	{
		$n = min(30,$n);
		$cmd = "SELECT * FROM `{$this->tbname}` WHERE `User` = '{$usr}' ORDER BY `Time` DESC LIMIT {$n}";
		$data = queryBAESQL($cmd);
		$msg = "";
		$i = 0;
		while($row = mysql_fetch_assoc($data))
		{
			$msg .= "\n" . $row["Type"] . ":" . $row["Money"];
			$i++;
		}
		return "总共{$i}条记录:" . $msg;
	}
	function queryDay($usr,$n)
	{
		return "未实现……";
	}
}
?>