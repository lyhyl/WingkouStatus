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
						$contentStr = "Snap";
						if(empty($contentStr))
							$contentStr = "Empty";
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
		$dbname = "IyropdxNjTcyGocxBRPJ";
		$host = 'sqld.duapp.com';
		$port = 4050;
		$user = '2bcb3e419d374573a1f30985225d9125';
		$pwd = '62dd83c50d4e4b7bb8e83adcc9df86d2';

		/*接着调用mysql_connect()连接服务器*/
		/*为了避免因MySQL数据库连接失败而导致程序异常中断，此处通过在mysql_connect()函数前添加@，来抑制错误信息，确保程序继续运行*/
		/*有关mysql_connect()函数的详细介绍，可参看http://php.net/manual/zh/function.mysql-connect.php*/
		$link = @mysql_connect("{$host}:{$port}",$user,$pwd,true);

		if(!$link)
		{
			return ("Connect Server Failed: " . mysql_error());
		}
		if(!mysql_select_db($dbname,$link))
		{
			return ("Select Database Failed: " . mysql_error($link));
		}

		$sql = "SELECT MAX(`Time`), `Desc` FROM `WkPCSnap`";
		$res = mysql_query($sql,$link);
		if ($ret === false)
		{
			return ("SQL Failed: " . mysql_error($link));
		}
		$r = mysql_fetch_row($res);
		if (count($r) == 0)
		{
			return ("SQL Failed2: " . mysql_error($link));
		}
		return $r[0];
	}
}

?>