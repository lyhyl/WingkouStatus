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
		$errMsg = "服务器好像出了点问题……告诉他吧！";
		$stripIn = str_replace(" ","",$in);
		
		// empty?
		if(empty($in))
			return "倒是说话啊你……";
		
		// help
		if($in == "help")
			return "help s help a ";
		
		// wingkou's status
		$wrud = array("你在干嘛", "你在干什么", "你在做什么", "waud", "wayd", "wrud", "wryd");
		if(in_array(strtolower($stripIn),$wrud))
		{
			$snap = getSnap();
			if(is_null($snap) or empty($snap))
				return $errMsg;
			return $snap;
		}
		
		// accounting
		if(in_array($in[0],$GLOBALS['accountBegin']))
		{
			$acc = new Accountor();
			return $acc->account($usr,$in) ? "已记录~ :D" : "哎？有点错误哎……";
		}
		$lastAQ = "/最后(\d+)条消费记录/";
		if(preg_match($lastAQ,$stripIn))
		{
			$n = intval(preg_replace($lastAQ,"$1",$stripIn));
			$acc = new Accountor();
			return $acc->queryN($usr,$n);
		}
		if($stripIn == "今天消费记录")
			return $acc->queryDay($usr,0);
		if($stripIn == "昨天消费记录")
			return $acc->queryDay($usr,-1);
		if($stripIn == "前天消费记录")
			return $acc->queryDay($usr,-2);
		$accSum = "/.+月消费总结/";
		
		// easter egg
		$simpleMap = array("喵"=>"汪","汪"=>"喵");
		if(array_key_exists($in,$simpleMap))
			return $simpleMap[$in];
		
		// I do not understand
		return "虽然我不造你说什么，但是我已经记录下来了。另外，输入help可以查看帮助。";
	}
}
?>