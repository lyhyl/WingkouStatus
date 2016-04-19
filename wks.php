<?php
define("TOKEN", "wingkoulan");
$wechatObj = new wechatCallbackapiTest();
$wechatObj->responseMsg();

class wechatCallbackapiTest
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
						$contentStr = getSnap();
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
		$server = "sqld.duapp.com:4050";
		$user = "2bcb3e419d374573a1f30985225d9125";
		$passw = "62dd83c50d4e4b7bb8e83adcc9df86d2";
		$dbname = "IyropdxNjTcyGocxBRPJ";
		$con = @mysql_connect($server,$user,$passw,true); 
		if(!$con)
		{
			die("Connect Server Failed: " . mysql_error($con)); 
		}
		if(!mysql_select_db($dbname,$con))
		{
			die("Select Database Failed: " . mysql_error($con)); 
		}
		$sql = "SELECT MAX(`Time`), `Desc` FROM `WkPCSnap`";
		$res = mysql_query($sql,$con);
		
		mysql_close($con);
	}
}

?>