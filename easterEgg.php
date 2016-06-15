<?php
class EasterEgg
{
	private $m = "/^(喵 *)+$/";
	private $w = "/^(汪 *)+$/";
	private $mw = "/^([喵汪] *)+$/";
	
	function isEasterEggMsg($in)
	{
		return preg_match($this->m,$in) or preg_match($this->w,$in) or preg_match($this->mw,$in);
	}
	
	function bring($in)
	{
		if(preg_match($this->m,$in) or preg_match($this->w,$in))
		{
			$f = array("汪","喵");
			$t = array("喵","汪");
			return str_replace($f,$t,$in);
		}
		else if(preg_match($this->mw,$in))
			return "你到底是汪还是喵啊……";
	}
}
?>