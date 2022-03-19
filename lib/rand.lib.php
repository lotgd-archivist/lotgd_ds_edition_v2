<?php

function make_seed() 
{
	list($usec, $sec) = explode(' ', microtime());
	return (float) $sec + ((float) $usec * 100000);
}
mt_srand(make_seed());

// modded by talion: checks for datatype and behaves as it should
function e_rand($min=false,$max=false)
{
	$float = false;

	if ($min===false) return mt_rand();
	
	if(is_float($min)) {
		$min*=1000;
		$float = true;
	}
	
	if ($max===false) return round(mt_rand($min)/1000,0);
	
	if(is_float($max) || $float) {
		$max*=1000;
		$float = true;
	}
	
	if ($min==$max) {
		
		if($float) {
			$min /= 1000;
		}			
		return round($min,0);
	}
	
	if ($min<$max)
	{
		$ret = @mt_rand($min,$max);
	}
	else if($min>$max)
	{
		$ret = @mt_rand($min,$max);
	}
	if($float) {
		$ret = round($ret/1000,0);
	}
	return $ret;
}

function e_rand_old($min=false,$max=false)
{
	if ($min===false) return mt_rand();
	$min*=1000;
	if ($max===false) return round(mt_rand($min)/1000,0);
	$max*=1000;
	if ($min==$max) return round($min/1000,0);
	// if ($min==0 && $max==0) return 0; //do NOT as me why this line can be executed, it makes no sense, but it *does* get executed.
	if ($min<$max)
	{
		return round(@mt_rand($min,$max)/1000,0);
	}
	else if($min>$max)
	{
		return round(@mt_rand($max,$min)/1000,0);
	}
}

?>
