<?php
require_once('common.php');

page_header('Test');

addnav('Zurück','village.php');

function test_rand($min=false,$max=false)
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

output(test_rand(0,0));

page_footer();
?>
