<?php

/**
* @desc Steigert Spezialfähigkeiten des Spielers
* @param int ID der Spezialfähigkeit, die gesteigert werden soll. (Optional, Wenn 0: Spezialgebiet des Spielers)
* @return mixed Neuen Level, wenn erfolgreich, sonst FALSE
* @author LOGD-Core, modded by talion
*/ 
function increment_specialty ($int_specid = 0) {
	global $session;
	
	if($int_specid == 0) {
		if($session['user']['specialty'] == 0) {
  			return(false);
		}
		$int_specid = $session['user']['specialty'];	  
	}
	
	$sql = 'SELECT * FROM specialty WHERE specid='.$int_specid;
	$row = db_fetch_assoc(db_query($sql));
	
  	$skillnames = array($row['specid']=>$row['specname']);
	$skills = array($row['specid']=>$row['usename']);
	$skillpoints = array($row['specid']=>$row['usename']."uses");
		
	(int)$session['user']['specialtyuses'][ $skills[$int_specid] ]++;
	
	$int_newlvl = $session['user']['specialtyuses'][$skills[$int_specid]];

	output('`nDu steigst in `&'.$skillnames[$int_specid].'`# ein Level auf '.$int_newlvl.' auf. ');
		
	$x = ($session['user']['specialtyuses'][ $skills[$int_specid] ]) % 2;
	if ($x == 0)
	{
		output('Du bekommst eine zusätzliche Anwendung!`n');
		(int)$session['user']['specialtyuses'][ $skillpoints[$int_specid] ]++;
	}
	else
	{
		output('Nur noch '.(2-$x).' weitere Stufe(n), bis du eine zusätzliche Anwendung erhältst!`n');
	}
	
	return($int_newlvl);
}

/**
* @desc Regeneriert Spezialanwendungen des Spielers
* @param bool Wenn TRUE, wird nur Spezialgebiet wiederhergestellt (Optional, Standard FALSE)
* @param bool Wenn TRUE, bekommt Spezialgebiet Anwendungsbonus (Optional, Standard TRUE)
* @author talion
*/ 
function restore_specialty ($bool_specialty_only = false, $bool_specialty_bonus = true) {
    global $session;
      
    $sql = 'SELECT specid,specname,filename,usename FROM specialty WHERE active="1"';
    $sql .= ($bool_specialty_only ? ' AND specid='.$session['user']['specialty'] : '');
	$result = db_query($sql);
	
	$int_sb = 0;
	if($bool_specialty_bonus) {
        $int_sb = getsetting("specialtybonus",1);  
    }
		
	while ($row = db_fetch_assoc($result))
	{
		$session['user']['specialtyuses'][$row['usename'].'uses'] = (
		round(($session['user']['specialtyuses'][$row['usename']]/2)) +
		($session['user']['specialty']==$row['specid']?$int_sb:0));
	}
}

?>
