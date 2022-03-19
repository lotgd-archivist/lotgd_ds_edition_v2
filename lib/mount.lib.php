<?php

/**
* @author LotGD Core / Anpera, edited by talion
* @desc Lädt Mount / Stalltier aus DB in Session, falls nötig (sprich: noch nicht in Session existent). 
* @param int ID des Tieres. Wenn == Spielertier, wird Session verwendet und Spielertier gesetzt.
* @param bool Wenn true, wird Array in Session auf jeden Fall überschrieben (Optional, Standard false)
* @return array Array mit Mount
*/
function getmount($int_horse,$bool_forcereload=false) {
	
	global $session,$playermount;
	
	$int_horse = (int)$int_horse;
	
	if($int_horse == 0) { return(array()); }
	
	$bool_playermount = ($int_horse == $session['user']['hashorse'] ? true : false);
	$arr_mount = array();

	// mod by talion: Mount ändert sich so selten, das können wir auch einmalig in die Sesssion laden
	if(!is_array($session['playermount']) || !$bool_playermount || $bool_forcereload) {
		$sql = 'SELECT * FROM mounts WHERE mountid="'.$int_horse.'"';
		$result = db_query($sql);
		
		if (db_num_rows($result)>0){
			$arr_mount = db_fetch_assoc($result);
		}
		
		if($bool_playermount) {
		
			$session['playermount'] = $arr_mount;
			$GLOBALS['playermount'] = &$session['playermount'];
					
		}
		else {
			return($arr_mount);
		}

	}
	
	$GLOBALS['playermount'] = &$session['playermount'];
				
	// Hier steht fest, dass wir Playermount haben
	return($session['playermount']);
				
}
?>
