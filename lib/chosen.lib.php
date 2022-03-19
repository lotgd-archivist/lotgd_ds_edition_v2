<?php
define('CHOSEN_FULL',31);
define('CHOSEN_BLOODGOD',32);
define('CHOSEN_BLOODCHAMP',33);
define('CHOSEN_BLOODCHAMP_END',35);

function &get_marks_state ($marks) {
			
	$mark_array = array();
	
	if($marks >= 16) {$mark_array['spirit'] = true;$marks-=16;} else {$mark_array['spirit'] = false;}
	if($marks >= 8) {$mark_array['water'] = true;$marks-=8;} else {$mark_array['water'] = false;}
	if($marks >= 4) {$mark_array['fire'] = true;$marks-=4;} else {$mark_array['fire'] = false;}
	if($marks >= 2) {$mark_array['air'] = true;$marks-=2;} else {$mark_array['air'] = false;}
	if($marks >= 1) {$mark_array['earth'] = true;$marks-=1;} else {$mark_array['earth'] = false;}	
	
	return($mark_array);
	
}

function calc_marks_state ($marks,$state) {
		
	if($state == 'spirit' && $marks < 16) {$marks += 16;}	
	if($state == 'water' && $marks < 8) {$marks += 8;}	
	if($state == 'fire' && $marks < 4) {$marks += 4;}	
	if($state == 'air' && $marks < 2) {$marks += 2;}	
	if($state == 'earth' && $marks < 1) {$marks += 1;}	
	
	return($marks);
	
}

?>
