<?php
define("TOKEN", "wingkoulan");

require_once "helper.php";
require_once "accounting.php";
require_once "getSnap.php";
require_once "easterEgg.php";

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
			if($postObj->MsgType == "text")
			{
				$fromUsername = $postObj->FromUserName;
				$keyword = trim($postObj->Content);
				$contentStr = $this->getResponseTxt($fromUsername,$keyword);
				$this->responseText($postObj,$contentStr);
			}
			else if($postObj->MsgType == "image")
			{
				$this->responseText($postObj,"图片啊……或许我应该问问微软小冰怎么跟你斗图……");
			}
			else if($postObj->MsgType == "voice")
			{
				$this->responseText($postObj,"我……没听清……");
			}
			else if($postObj->MsgType == "shortvideo")
			{
				$this->responseText($postObj,"老板！换碟！这部我看过了！");
			}
			else if($postObj->MsgType == "location")
			{
				$this->responseText($postObj,"你在这？");
			}
			else if($postObj->MsgType == "link")
			{
				$textTpl = "%s？陌生的链接我一般不点……肯定有毒！";
				$contentStr = sprintf($textTpl,$postObj->Title);
				$this->responseText($postObj,$contentStr);
			}
			else if($postObj->MsgType == "event")
			{
				if($postObj->Event == "subscribe")
					$this->responseText($postObj,"欢迎~\n回复 help 可以查看帮助~");
			}
			else
			{
				$this->responseText($postObj,$postObj->MsgType);
			}
        }
		else
		{
        	echo "";
        	exit;
        }
    }
	
	function responseText($postObj,$contentStr)
	{
		$fromUsername = $postObj->FromUserName;
		$toUsername = $postObj->ToUserName;
		$msgType = "text";
		$time = time();
		$textTpl = "<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[%s]]></MsgType>
					<Content><![CDATA[%s]]></Content>
					<FuncFlag>0</FuncFlag>
					</xml>";
		$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
		echo $resultStr;
	}
	
	function getResponseTxt($usr,$in)
	{
		$stripIn = str_replace(" ","",$in);
		
		// empty?
		if(empty($in))
			return "倒是说话啊你……";
		
		// help
		$he = new Helper();
		if($he->isHelpMsg($in))
			return $he->getHelp($in);
		
		// wingkou's status
		$sg = new snapGetter();
		if($sg->isGetSnapMsg($stripIn))
			return $sg->getSnap();
		
		// accounting
		$acc = new Accountor();
		if($acc->isAccountingMsg($in))
			return $acc->account($usr,$in);
		$typeIdx = $acc->getQueryMsgIdx($stripIn);
		if($typeIdx > 0)
			return $acc->query($usr,$stripIn,$typeIdx);
		
		// easter egg
		$ee = new EasterEgg();
		if($ee->isEasterEggMsg($in))
			return $ee->bring($in);
		
		if($in == "【收到不支持的消息类型，暂无法显示】")
			return "懒得跟你斗图……";
		else
			// I do not understand
			return "虽然我不造你说什么，但是我已经记录下来了。另外，输入help可以查看帮助。有建议可直接留言。";
	}
}
?>