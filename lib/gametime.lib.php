<?php
// LocalE-Zeiteinstellungen setzen
setlocale(LC_TIME, "de_DE");

function getmicrotime(){
	list($usec, $sec) = explode(' ',microtime());
	return ((float)$usec + (float)$sec);
}

function checkday() {
	global $session,$revertsession,$REQUEST_URI;

	if ($session['user']['loggedin']){
		output('<!--CheckNewDay()-->',true);
		if(is_new_day()){
		
			// Evtl. noch herumschwirrende Kommentare abfangen. by talion
			addcommentary();
		
			$session=$revertsession;
			$session['user']['restorepage']=$REQUEST_URI;
			$session['allowednavs']=array();
			redirect('newday.php');
		}
	}
}

function is_new_day(){
	global $session;
	$t1 = gametime();
	$t2 = convertgametime(strtotime($session[user][lasthit]));
	$d1 = date('Y-m-d',$t1);
	$d2 = date('Y-m-d',$t2);
	if ($d1!=$d2){
		return true;
	}else{
		return false;
	}
}

function getgametime(){
	//	return date('g:i a',gametime());
	$time = convertgametime(strtotime(date('r')));
	return date(getsetting('gametimeformat','g:i a'), $time );
}

// Gamedate-Mod by Chaosmaker
// modded by talion: monate hinzugefügt, beliebiges Datum kann übergeben werden
function getgamedate($indate='') {
	$months = array(1=>'Januar','Februar','März','April','Mai','Juni','Juli','August','September','Oktober','November','Dezember');
	$indate = ($indate != '') ? $indate : getsetting('gamedate','0005-01-01');
	$date = explode('-',$indate);
	$find = array('%Y','%y','%m','%n','%d','%j','%F');
	$replace = array($date[0],sprintf('%02d',$date[0]%100),sprintf('%02d',$date[1]),(int)$date[1],sprintf('%02d',$date[2]),(int)$date[2],$months[(int)$date[1]]);
	return str_replace($find,$replace,getsetting('gamedateformat','%Y-%m-%d'));
}

function gametime(){
	$time = convertgametime(strtotime(date('r')));
	return $time;
}

function convertgametime($intime){
	// Hehe, einen hamwa noch, einen hamwa noch: by JT & anpera
	$multi = getsetting('daysperday',4);
	$offset = getsetting('gameoffsetseconds',0);
	$fixtime = mktime(0,0,0-$offset,date('m')-$multi,date('d'),date('Y'));
	$time=$multi*(strtotime(date('Y-m-d H:i:s',$intime))-$fixtime);
	$time=strtotime(date('Y-m-d H:i:s',$time).'+'.($multi*date('I',$intime)).' hour');
	$time=strtotime(date('Y-m-d H:i:s',$time).'-'.date('I',$time). ' hour');
	$time=strtotime(date('Y-m-d H:i:s',$time).'+'.(23-$multi).' hour');
	return $time;
}

function dhms($secs,$dec=false){
	if ($dec===false) $secs=round($secs,0);
	return (int)($secs/86400).'d'.(int)($secs/3600%24).'h'.(int)($secs/60%60).'m'.($secs%60).($dec?substr($secs-(int)$secs,1):'').'s';
}

// mode: 1 = Spieler
//		 2 = Gilde
//		 0 = allgemein
function addhistory ($msg,$mode=1,$id=0) {
	global $session;
	
	$id = (int)$id;
	
	$id = ($mode == 1 && $id == 0) ? $session['user']['acctid'] : $id;
	$id = ($mode == 2 && $id == 0 && $session['user']['guildid']) ? $session['user']['guildid'] : $id;
	
	if($mode > 0 && $id == 0) {return;}
		
	$sql = 'INSERT INTO history SET msg="'.addslashes($msg).'",date=NOW(),gamedate="'.getsetting('gamedate','').'"'.($mode==1 ? ',acctid='.$id : ($mode == 2 ? ',guildid='.$id : '') );
	db_query($sql);	
}

function show_history ($mode=0,$id=0,$header=false) {
	
	global $session;
	
	$id = (int)$id;
	if($mode > 0 && $id == 0) {return;}
	
	if($header) {
		$header = 'Geschichte ';
		
		if($mode == 1) {
			$sql = 'SELECT name FROM accounts WHERE acctid='.$id;
			$res = db_query($sql);
			$player = db_fetch_assoc($res);
			$header .= 'von '.$player['name'];
		}
		elseif($mode == 2) {
			$guild = &dg_load_guild($id,array('name'));
			$header .= 'der Gilde '.$guild['name'];
		}
		else {
			$header .= 'des Dorfes';
		}
		output('`b'.$header.':`b`n`n<li>',true);
	}
	
	$sql = 'SELECT * FROM history WHERE '.($mode == 0 ? 'acctid=0 AND guildid=0' : ($mode == 1 ? 'acctid='.$id : 'guildid='.$id) ).' ORDER BY gamedate DESC, id DESC';
	$res = db_query($sql);	
	
	if(db_num_rows($res) == 0) {
		output('<ul>`iNoch keine besonderen Ereignisse überliefert!`i</ul>',true);
		return;
	}
	
	$lastyear = 0;
	$year = 0;
	
	while($h = db_fetch_assoc($res)) {
		
		$year = (int)date('y',strtotime($h['gamedate']));
		if($year != $lastyear) {
			
			output('`b`&Ereignisse des Jahres '.$year.'`b`n');
			$lastyear = $year;
		}
		
		output('<ul>`@'.getgamedate($h['gamedate']).': `^'.$h['msg'],true);
		
		if(su_check(SU_RIGHT_NEWS)) {
			$link = 'history.php?op=del&id='.$h['id'];
			addnav('',$link);
			output(' `&[ <a href="'.$link.'">Del</a> ]',true);
		}
		
		output('</ul>',true);
		
	}	
	
	output('</li>',true);
	
}
?>
