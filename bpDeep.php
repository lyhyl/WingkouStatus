<?php
function getSnapWithBpDeep($in)
{
	$file = fopen("a.txt","r");
	$data = array();
	while(($line = fgets($file)) !== false)
	{
		$d = preg_split('/[ \r\n]/',$line,-1,PREG_SPLIT_NO_EMPTY);
		$data = array_merge($data, $d);
	}
	$bp = new BpDeep($data, [10,16,8,4,2,1], 0.15, 0.8);
	$out = $bp->computeOut($in);
	return $out;
}
class BpDeep
{
    private $layer;
    private $layer_weight;

    public function __construct($data, $layernum)
	{
		$dx = 0;
		$nc = count($layernum);
        $this->layer = array();
        $this->layer_weight = array();
        for($l = 0; $l < $nc; $l++)
		{
            $this->layer[$l] = array();
            if($l + 1 < $nc)
			{
				$this->layer_weight[$l] = array();
                for($j = 0; $j < $layernum[$l] + 1; $j++)
                    for($i = 0; $i < $layernum[$l + 1]; $i++)
                        $this->layer_weight[$l][$j][$i] = $data[$dx++];
			}
        }
    }
	
    public function computeOut($in)
	{
		$layerlen = count($this->layer);
        for($l = 1; $l < $layerlen; $l++)
		{
			$layerllen = count($this->layer[$l]);
            for($j = 0; $j < $layerllen; $j++)
			{
				$z = $this->layer_weight[$l - 1][$this->layer[$l - 1].length][$j];
				$layerlsolen = count($this->layer[$l - 1]);
                for($i = 0; $i < $layerlsolen; $i++)
				{
                    $this->layer[$l - 1][$i] = $l == 1 ? $in[$i] : $this->layer[$l - 1][$i];
                    $z += $this->layer_weight[$l - 1][$i][$j] * $this->layer[$l - 1][$i];
                }
                $this->layer[$l][$j] = 1 / (1 + exp(-$z));
            }
        }
        return $this->layer[$layerlen - 1];
    }
}
?>