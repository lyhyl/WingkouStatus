<?php
/**
  * wechat php test
  */

define("TOKEN", "wingkoulan");
$wechatObj = new wechatCallbackapiTest();
$wechatObj->valid();

class wechatCallbackapiTest
{
	public function valid()
    {
		echo $_GET["echostr"];
    }
}

?>