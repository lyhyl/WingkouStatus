<?php
require_once "baesql.php";
require_once "bpDeep.php";

class snapGetter
{
	function isGetSnapMsg($in)
	{
		$wrud = array("你在干嘛", "你在干什么", "你在做什么", "waud", "wayd", "wrud", "wryd");
		return in_array(strtolower($in),$wrud);
	}
	
	function getSnap()
	{
		$cmd = "SELECT * FROM `{$GLOBALS['snapTbName']}` WHERE `Time` = (SELECT MAX(`Time`) FROM `{$GLOBALS['snapTbName']}`)";
		$ret = queryBAESQL($cmd);
		if ($ret === false)
			return ("SQL Failed(Query): " . mysql_error($link));
		$row = mysql_fetch_row($ret);
		if ($row === false)
			return ("SQL Failed(No Result): " . mysql_error($link));
		$time = $row[0];
		$summery = $this->genSummery($row[1]);
		$atime = $row[2];
		$dt = floor((time() - $atime) / 60);
		$atxt = "";
		if($dt > 30)
			$atxt = "\n(他的电脑已经有{$dt}分钟没有动过了……)";
		return "最后记录于{$time}:\n{$summery}{$atxt}";
	}

	function genSummery($data)
	{
		$gstatus = array("boot"=>"刚开机呢……","shutdown"=>"啊……他关机了");
		if(array_key_exists($data,$gstatus))
			return $gstatus[$data];
		$id = array(
			"csgo" => 1,
			"dontstarve_steam" => 1,
			"hl2" => 1,
			"cmd" => 2,
			"notepad++" => 2,
			"devenv" => 2,
			"idea" => 2,
			"pythonw" => 2,
			"chrome" => 4,
			"wps" => 8,
			"wpp" => 8,
			"et" => 8,
			"eviews6" => 8,
			"vmplayer" => 16
		);
		$procs = explode(",",$data);
		$status = 0;
		$ps = "";
		for($i = 0; $i < count($procs); $i++)
		{
			$proc = strtolower(trim($procs[$i]));
			if(array_key_exists($proc,$id))
			{
				$status |= $id[$proc];
				$ps = $ps . (empty($ps) ? "" : ",") . $proc;
			}
		}
		$desc = "";
		if(($status & 1)!=0)
			$desc = "啊！他在打游戏！";
		else if(($status & 2)!=0)
			$desc = "他在编程~";
		else if(($status & 4)!=0)
			$desc = "在看网页吧……";
		else if(($status & 8)!=0)
			$desc = "在做作业啦";
		else if(($status & 16)!=0)
			$desc = "在用虚拟机……可能是在看股票~";
		else
			$desc = "哎？什么都没发现……";
		return "{$desc}\n(发现了以下进程{$ps})";
	}
}
?>