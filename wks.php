<?php
define("TOKEN", "wingkoulan");
require "baesql.php";
$wechatObj = new WeChat();
$wechatObj->responseMsg();

class WeChat
{
    public function responseMsg()
    {
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

		if (!empty($postStr))
		{
                libxml_disable_entity_loader(true);
              	$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
                $fromUsername = $postObj->FromUserName;
                $toUsername = $postObj->ToUserName;
                $keyword = trim($postObj->Content);
                $time = time();
                $textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>";             
				if(!empty($keyword))
                {
              		$msgType = "text";
                	$contentStr = "";
					if($keyword == "你在干嘛" or $keyword == "waud" or $keyword == "wayd")
					{
						$contentStr = $this->getSnap();
						if(empty($contentStr) or is_null($contentStr))
							$contentStr = "好像出了点问题……我不知道……";
					}
					else
					{
						$simpleMap = array("喵"=>"汪","汪"=>"喵");
						if(array_key_exists($keyword,$simpleMap))
						{
							$contentStr = $simpleMap[$keyword];
						}
						else
						{
							$contentStr = "……help……";
						}
					}
                	$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                	echo $resultStr;
                }
				else
				{
                	echo "……";
                }
        }
		else
		{
        	echo "";
        	exit;
        }
    }
	
	function getSnap()
	{
		$ret = queryBAESQL("SELECT * FROM `{$GLOBALS['tbname']}` WHERE `Time` = (SELECT MAX(`Time`) FROM `{$GLOBALS['tbname']}`)");
		if ($ret === false)
		{
			return ("SQL Failed(Query): " . mysql_error($link));
		}
		$row = mysql_fetch_row($ret);
		if ($row === false)
		{
			return ("SQL Failed(No Result): " . mysql_error($link));
		}
		$time = $row[0];
		$summery = $this->genSummery($row[1]);
		return "最后记录于{$time}:\n{$summery}";
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