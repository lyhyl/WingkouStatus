<?php
class Helper
{
	private $reg = "/help(.*)/";
	
	function isHelpMsg($in)
	{
		return preg_match($this->reg,$in);
	}
	
	function getHelp($in)
	{
		$arg = strtolower(trim(preg_replace($this->reg,"$1",$in)));
		switch($arg)
		{
			case "":
				return "输入 help s 查看状态帮助\n输入 help a 查看记账帮助";
			case "s":
				return "输入:\n\"你在干嘛\"\n\"你在干什么\"\n\"你在做什么\"\n" .
					"\"waud\"\n\"wayd\"\n\"wrud\"\n\"wryd\"\n可以查看状态";
			case "a":
				return "以\$(或¥,￥,＄)开头的消息将作为账目记录。" .
					"\n项目名与金额之间用空格隔开哦~\n例如:\n$\n纸巾 5\n笔 1.99\n" .
					"或者:\n¥A+B 3.99 C 2.09\n\n" .
					"输入:\n\"最后n条消费记录\"\n\"前n天条消费记录\"\n\"m月d日消费记录\"\n" .
					"\"今天消费记录\"\n\"昨天消费记录\"\n\"前天消费记录\"\n可以查询对应记录";
			default:
				return "未知指令";
		}
	}
}
?>