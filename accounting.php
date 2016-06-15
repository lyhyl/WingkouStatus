<?php
require_once "baesql.php";

class Accountor
{
	private $tbname = "Accounting";
	private $x = array("$","¥","￥","＄");
	private $lastAQ = "/最后(\d+)条消费记录/";
	private $ndAQ = "/前(\d+)天消费记录/";
	private $pdAQ = "/(\d+)月(\d+)日消费记录/";
	private $pmAQ = "/(\d+)月消费总结/";
	private $d0AQ = "今天消费记录";
	private $d1AQ = "昨天消费记录";
	private $d2AQ = "前天消费记录";
	
	function isAccountingMsg($in)
	{
		return preg_match("/^[\$¥￥＄]/",$in);
	}
	
	function getQueryMsgIdx($stripIn)
	{
		if(preg_match($this->lastAQ,$stripIn))
			return 1;
		if($stripIn == $this->d0AQ)
			return 2;
		if($stripIn == $this->d1AQ)
			return 3;
		if($stripIn == $this->d2AQ)
			return 4;
		if(preg_match($this->ndAQ,$stripIn))
			return 5;
		if(preg_match($this->pdAQ,$stripIn))
			return 6;
		return -1;
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
	
	function query($usr,$stripIn,$type)
	{
		switch($type)
		{
			case 1:
				$n = intval(preg_replace($this->lastAQ,"$1",$stripIn));
				return $this->queryN($usr,$n);
			case 2:
				return $this->queryDay($usr,0);
			case 3:
				return $this->queryDay($usr,1);
			case 4:
				return $this->queryDay($usr,2);
			case 5:
				$n = intval(preg_replace($this->ndAQ,"$1",$stripIn));
				return $this->queryDay($usr,$n);
			case 6:
				$y = date("Y");
				try {
					$n = new DateTime(preg_replace($this->pdAQ,"{$y}-$1-$2",$stripIn));
				} catch (Exception $e) {
					return "日期错了吧?";
				}
				$now = new DateTime("now");
				$diff = intval(($now->getTimestamp() - $n->getTimestamp()) / 60 / 60 / 24);
				if($diff < 0)
					return "怎么可能查到……";
				else
					return $this->queryDay($usr,$diff);
			default:
				return "error!acc.query({$type})";
		}
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