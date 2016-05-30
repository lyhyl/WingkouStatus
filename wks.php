<?php
define("TOKEN", "wingkoulan");

require_once "accounting.php";
require_once "getSnap.php";

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
			$msgType = "text";
			$contentStr = $this->getResponseTxt($fromUsername,$keyword);
			$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
			echo $resultStr;
        }
		else
		{
        	echo "";
        	exit;
        }
    }
	
	function getResponseTxt($usr,$in)
	{
		$stripIn = str_replace(" ","",$in);
		
		// empty?
		if(empty($in))
			return "倒是说话啊你……";
		
		// help
		if($in == "help")
			return "输入 help s 查看状态帮助\n输入 help a 查看记账帮助";
		if($stripIn == "helps")
			return "输入\"你在干嘛\"/\"你在干什么\"/\"你在做什么\"/\"waud\"/\"wayd\"/\"wrud\"/\"wryd\"可以查看状态";
		if($stripIn == "helpa")
			return "以\$(或¥,￥,＄)开头的消息将作为账目记录\n项目名与金额之间用空格隔开哦~\n例如:\n$\n纸巾 5\n笔 1.99\n\n" . 
		"输入\"最后n条消费记录\"/\"今天消费记录\"/\"昨天消费记录\"/\"前天消费记录\"可以查询对应记录";
		
		// wingkou's status
		$sg = new snapGetter();
		if($sg->isGetSnapMsg($stripIn))
			return $sg->getSnap();
		
		// accounting
		$acc = new Accountor();
		if($acc->isAccountingMsg($in))
			return $acc->account($usr,$in);
		$lastAQ = "/最后(\d+)条消费记录/";
		if(preg_match($lastAQ,$stripIn))
		{
			$n = intval(preg_replace($lastAQ,"$1",$stripIn));
			return $acc->queryN($usr,$n);
		}
		if($stripIn == "今天消费记录")
			return $acc->queryDay($usr,0);
		if($stripIn == "昨天消费记录")
			return $acc->queryDay($usr,1);
		if($stripIn == "前天消费记录")
			return $acc->queryDay($usr,2);
		$ndAQ = "/前(\d+)天消费记录/";
		if(preg_match($ndAQ,$stripIn))
		{
			$n = intval(preg_replace($ndAQ,"$1",$stripIn));
			return $acc->queryDay($usr,$n);
		}
		$pdAQ = "/(\d+)月(\d+)日消费记录/";
		if(preg_match($pdAQ,$stripIn))
		{
		    $y = date("Y");
			try
			{
				$n = new DateTime(preg_replace($pdAQ,"{$y}-$1-$2",$stripIn));
			} catch (Exception $e)
			{
				return "日期错了吧?";
			}
			$now = new DateTime("now");
			$diff = intval(($now->getTimestamp() - $n->getTimestamp()) / 60 / 60 / 24);
			if($diff < 0)
				return "怎么可能查到……";
			return $acc->queryDay($usr,$diff);
		}
		$accSum = "/(\d+)月消费总结/";
		
		// easter egg
		if(preg_match("/^(喵 *)+$/",$in) or preg_match("/^(汪 *)+$/",$in))
		{
			$f = array("汪","喵");
			$t = array("喵","汪");
			return str_replace($f,$t,$in);
		}
		else if(preg_match("/^([喵汪] *)+$/",$in))
			return "你到底是汪还是喵啊……";
		
		// I do not understand
		return "虽然我不造你说什么，但是我已经记录下来了。另外，输入help可以查看帮助。有建议可直接留言，反正我都不打算改代码……";
	}
}
?>