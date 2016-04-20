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
					if($keyword == "喵")
					{
						$contentStr = "汪";
					}
					else if($keyword == "汪")
					{
						$contentStr = "喵";
					}
					else
					{
						$contentStr = $this->getSnap();
						if(empty($contentStr) or is_null($contentStr))
							$contentStr = "……";
					}
                	$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                	echo $resultStr;
                }
				else
				{
                	echo "Input something...";
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
		$ret = queryBAESQL("SELECT * FROM `{$tbname}` WHERE `Time` = (SELECT MAX(`Time`) FROM `{$tbname}`)");
		if ($ret === false)
		{
			return ("SQL Failed(Query): " . mysql_error($link));
		}
		$row = mysql_fetch_row($ret);
		if ($row === false)
		{
			return ("SQL Failed(No Result): " . mysql_error($link));
		}
		return implode($row);
	}
}

?>