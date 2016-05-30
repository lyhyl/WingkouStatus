<?php
require_once "baesql.php";

class Accountor
{
	private $tbname = "Accounting";
	private $x = array("$","¥","￥","＄");
	
	function isAccountingMsg($in)
	{
		return preg_match("/^[\$¥￥＄]/",$in);
	}
	
	function account($usr,$in)
	{
		$rec = "";
		$in = str_replace($this->x,"",$in);
		$data = preg_split('/[ \r\n]/',$in,-1,PREG_SPLIT_NO_EMPTY);
		$datalen = count($data);
		$cmdTemp = "INSERT INTO `%s` (`User`,`Time`,`Type`,`Money`) VALUES ('%s',NOW(),'%s',%s)";
		for($i = 0; $i + 1 < $datalen; $i += 2)
		{
			$cmd = sprintf($cmdTemp,$this->tbname,$usr,$data[$i],$data[$i + 1]);
			if(!queryBAESQL($cmd))
				return "发生错误,只有部分已记录:" . $rec;
			else
				$rec .= "\n" . $data[$i] . ":" . $data[$i + 1];
		}
		if(empty($rec))
			$rec = "(无……)";
		return "已记录~ XD:" . $rec;
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
			$msg .= "\n" . $row["Type"] . ":" . $row["Money"] . "(" . $row["Time"] . ")";
			$i++;
		}
		return "总共{$i}条记录:" . $msg;
	}
	
	function queryDay($usr,$n)
	{
		$d = new DateTime("now",new DateTimeZone("Asia/Shanghai"));
		$nd = new DateTime("now",new DateTimeZone("Asia/Shanghai"));
		$nd->add(new DateInterval("P1D"));
		$d->sub(new DateInterval("P{$n}D"));
		$nd->sub(new DateInterval("P{$n}D"));
		$cmdTemp = "SELECT * FROM `%s` WHERE `User` = '%s' AND `Time` >= '%s' AND `Time` < '%s'";
		$cmd = sprintf($cmdTemp,$this->tbname,$usr,$d->format("Y-n-j"),$nd->format("Y-n-j"));
		$data = queryBAESQL($cmd);
		$sum = 0;
		$msg = "";
		while($row = mysql_fetch_assoc($data))
		{
			$msg .= $row["Type"] . ":" . $row["Money"] . "\n";
			$sum += $row["Money"];
		}
		return $msg . "总共{$sum}元";
	}
}
?>