<?php

function buff_add ($buff) {
	
	global $session;

	if(is_array($buff) && sizeof($buff) > 0) {
		$session['bufflist'][ $buff['name'] ] = $buff;
		buff_unset();
		buff_set();
	}
	
}

function buff_remove ($buff) {
	
	global $session;
	
	if($buff === true) {
		unset($session['bufflist']);
	}
	else {
		unset($session['bufflist'][ $buff ]);
	}
	
}

function buff_backup ($buff) {
	
	global $session;
	
	if($buff === true) {
		$session['user']['buffbackup'] = serialize($session['bufflist']);
	}
	else {
		$buffback = unserialize($session['user']['buffbackup']);
		$buffback[$buff] = $session['bufflist'][$buff];
		$session['user']['buffbackup'] = serialize($buffback);
	}
	
}

// für NICHT-Battle-Buffs
function buff_set () {
	
	global $session;
			
	$buffs_applied = array();
	
	foreach($session['bufflist'] as $b) {

		$buffs_applied['charm'] += $b['plus_charm'];
		$buffs_applied['attack'] += $b['plus_attack'];
		$buffs_applied['defence'] += $b['plus_defence'];
		
	}
	
	$session['buffs_applied'] = $buffs_applied;
	
}

function buff_unset () {

	global $session;
	
	$buffs_applied = $session['buffs_applied'];
	
	unset($session['buffs_applied']);
	
	if(sizeof($buffs_applied) > 0) {
			
		foreach($buffs_applied as $key=>$b) {
			
			$session['user'][$key] = max($session['user'][$key]-$b,0);
							
		}
	}
	
	unset($buffs_applied);
		
}

?>