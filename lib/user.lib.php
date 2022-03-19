<?php
/**
* user.lib.php: Funktionsbibliothek für Methoden, die zur Modifizierung / Anzeige von Accountdaten benötigt werden
* @author LOGD-Core / Drachenserver-Team
* @version DS-E V/2
*/

// Konstantendefs für die activated-Spalte
define('USER_ACTIVATED_STEALTH',42);
define('USER_ACTIVATED_MUTE',200);
define('USER_ACTIVATED_MUTE_AUTO',201);
define('USER_ACTIVATED_VACATION',100);
define('USER_ACTIVATED_SENTNOTICE',11);
define('USER_ACTIVATED_FIRSTINFO',2);
define('USER_ACTIVATED_DEFAULT',1);

// Konstantendefs für die Orte
define('USER_LOC_FIELDS',0);
define('USER_LOC_INN',1);
define('USER_LOC_HOUSE',2);
define('USER_LOC_PRISON',3);

// Flag der biotime-Spalte für gesperrte Bio
define('BIO_LOCKED','9999-12-31 00:00:00');

// Wert der spirits-Spalte für RP-Wiedererweckung
define('RP_RESURRECTION',-42);


/**
*@desc Ruft einzelnen Datensatz aus Account-Extra-Info-Tabelle ab
*@param string SQL-konforme (durch Kommata getrennte) Angabe der abzurufenden Felder. Optional.
*@param int Accountid, wenn 0, acctid des Users. Optional.
*@param string Zusätzliche SQL-WHERE Bedingungen. Optional
*@return array Assoziativen Array mit Datensatz
*@author talion
*/
function user_get_aei ($fields='*',$acctid=0,$where='') {
	
	global $session;
	
	$acctid = ($acctid == 0 ? $session['user']['acctid'] : $acctid);
	
	$sql = 'SELECT '.$fields.' FROM account_extra_info WHERE '.($acctid > 0 ? ' acctid='.$acctid : '').($where != '' ? $where : '').' ORDER BY acctid ASC LIMIT 1';
	
	$res = db_query($sql);
	
	return( db_fetch_assoc($res) );
	
}

/**
*@desc Verändert einzelnen Datensatz aus Account-Extra-Info-Tablle
*@param array Assoziativer Array (feld => Wert) der zu verändernden Daten
*@param int Accountid, wenn 0, acctid des Users. Optional.
*@param where (string) Zusätzliche SQL-WHERE Bedingungen. Optional
*@return int Anzahl der betroffenen Datensätze
*@author talion
*/
function user_set_aei ($changes,$acctid=0,$where='') {
	
	global $session;
	
	if(!sizeof($changes)) {return(false);}
	
	$acctid = ($acctid == 0 ? $session['user']['acctid'] : $acctid);
	
	$sql = 'UPDATE account_extra_info SET acctid=acctid';
	
	foreach($changes as $field => $val) {
		
		$sql .= ','.$field.' = "'.$val.'"';
		
	}
	
	$sql .= ' WHERE '.($acctid > 0 ? ' acctid='.$acctid : '').($where != '' ? $where : '').' LIMIT 1';
	
	$res = db_query($sql);
	
	return( db_affected_rows() );
	
}

/**
*@desc Ruft einzelnen Datensatz aus Spieler-Statistik ab
*@param string SQL-konforme (durch Kommata getrennte) Angabe der abzurufenden Felder. Optional.
*@param int Accountid, wenn 0, acctid des Users. Optional.
*@param string Zusätzliche SQL-WHERE Bedingungen. Optional
*@return array Assoziativen Array mit Datensatz
*@author talion
*/
function user_get_stats ($fields='*',$acctid=0,$where='') {
	
	global $session;
	
	$acctid = ($acctid == 0 ? $session['user']['acctid'] : $acctid);
	
	$sql = 'SELECT '.$fields.' FROM account_stats WHERE '.($acctid > 0 ? ' acctid='.$acctid : '').($where != '' ? $where : '').' ORDER BY acctid ASC LIMIT 1';
	
	$res = db_query($sql);
	
	return( db_fetch_assoc($res) );
	
}

/**
*@desc Verändert Wert in Spieler-Statistik
*@param array Assoziativer Array (feld => Wert) der zu verändernden Daten
*@param int Accountid, wenn 0, acctid des Users. Optional.
*@param where (string) Zusätzliche SQL-WHERE Bedingungen. Optional
*@return int Anzahl der betroffenen Datensätze
*@author talion
*/
function user_set_stats ($changes,$acctid=0,$where='') {
	
	global $session;
	
	if(!sizeof($changes)) {return(false);}
	
	$acctid = ($acctid == 0 ? $session['user']['acctid'] : $acctid);
	
	$sql = 'UPDATE account_stats SET acctid=acctid';
	
	foreach($changes as $field => $val) {
		
		$sql .= ','.$field.' = '.$val.'';
		
	}
	
	$sql .= ' WHERE '.($acctid > 0 ? ' acctid='.$acctid : '').($where != '' ? $where : '').' LIMIT 1';
	
	$res = db_query($sql) or die(db_error(LINK));
	
	return( db_affected_rows() );
	
}

/**
*@desc Legt temp. Session-Kopie zwecks Check auf veränderte Felder an
*@author talion
*/
function user_create_session_copy () {
	global $session_copy,$session;

	$session_copy['charm'] = $session['user']['charm'];
	$session_copy['charisma'] = $session['user']['charisma'];
	$session_copy['marriedto'] = $session['user']['marriedto'];
	$session_copy['alive'] = $session['user']['alive'];
	$session_copy['imprisoned'] = $session['user']['imprisoned'];
	$session_copy['loggedin'] = $session['user']['loggedin'];
	$session_copy['deathpower'] = $session['user']['deathpower'];
	$session_copy['login'] = $session['user']['login'];
	$session_copy['password'] = $session['user']['password'];

}

/**
*@desc Lädt Superuser-Rechte des aktuellen Benutzers
*@author talion
*/
function user_load_surights () {
	global $session;

	$arr_surights = unserialize( stripslashes($session['user']['surights']) );
	$arr_groups = unserialize( stripslashes(getsetting('sugroups','')) );
	
	if(isset($arr_groups[ $session['user']['superuser'] ])) {
		$arr_usergroup = $arr_groups[ $session['user']['superuser'] ];
		
		$arr_grprights = explode(';',$arr_usergroup[2]);
		//$arr_grprights = $arr_usergroup[2];
		
		// Einzelrechte überschreiben Gruppenrechte		
		$session['surights'] = $arr_grprights;
		if(is_array($arr_surights)) {		
			foreach($arr_surights as $key=>$r) {
				$session['surights'][$key] = $r;
			}
		}
	}
	else {
		$session['surights'] = $arr_surights;
	}
}

/**
*@desc Berechnet maximales Ansehen
*@author maris
*/
function max_reputation () {
	global $session;
	$dks=$session['user']['dragonkills'];
	$sql = "SELECT daysinjail FROM account_extra_info WHERE acctid = '".$session['user']['acctid']."'";
	$result = db_query($sql);
	$row=db_fetch_assoc($result);
	$penalty=$row['daysinjail']-$dks;
	if ($penalty<0) $penalty=0;
	If ($penalty>50) $penalty=50;
	$maximum=50-$penalty;
	return($maximum);
}

/**
*@desc Speichert Accountdaten des aktuellen Users
*@author LOGD-Core, modified by Drachenserver-Team
*/
function saveuser(){
	global $session,$dbqueriesthishit,$session_copy;

	$modified = true;

	//	$cmd = date("Y-m-d H:i:s")." $dbqueriesthishit ".$_SERVER['REQUEST_URI'];
	//	@exec("echo $cmd >> /home/groups/l/lo/lotgd/sessiondata/data/queryusage-".$session['user']['login'].".txt");
	if ($session['loggedin'] && $session['user']['acctid']!=''){
	
		buff_unset();
	
		$session['user']['output']=$session['output'];
		$session['user']['allowednavs']=serialize($session['allowednavs']);
		$session['user']['bufflist']=serialize($session['bufflist']);
		if (is_array($session['user']['prefs'])) $session['user']['prefs']=serialize($session['user']['prefs']);
		if (is_array($session['user']['specialtyuses'])) $session['user']['specialtyuses'] = serialize($session['user']['specialtyuses']);
		if (is_array($session['user']['dragonpoints'])) $session['user']['dragonpoints']=serialize($session['user']['dragonpoints']);
		//$session[user][laston] = date("Y-m-d H:i:s");
		$sql='UPDATE accounts SET ';
		reset($session['user']);
		while(list($key,$val)=each($session['user']))
		{
			$modified = true;

			if( isset($session_copy[$key]) )
			{
				$modified = ($session_copy[$key] == $val) ? false : true;
			}

			if($modified)
			{
				
				if($key=='login'){
					if(getsetting("ci_active",0)){
						$inc = user_get_aei("incommunity");
						if( $inc['incommunity'] > 0 ){
							include_once(LIB_PATH.'communityinterface.lib.php');
							ci_rename($inc['incommunity'], $val);
						}
					}
				}	
				else if($key=='password'){
					if(getsetting("ci_active",0)){
						$inc = user_get_aei("incommunity");
						if( $inc['incommunity'] > 0 ){
							include_once(LIB_PATH.'communityinterface.lib.php');
							ci_setpw($inc['incommunity'], $val);
						}
					}
				}
				
				if (is_array($val))
				{
					$sql.= $key.'="'.addslashes(serialize($val)).'", ';
				}
				else
				{
					$sql.=$key.'="'.addslashes($val).'", ';
				}

			}	// END if modded
		}

		if(strlen($sql) > 22)
		{
		
			$sql = substr($sql,0,strlen($sql)-2);
			$sql.=' WHERE acctid = '.$session['user']['acctid'];
			db_query($sql,false);
		}
	}
}

$maximumrep = 0;

/**
*@desc Lädt Accountdaten des angegebenen Users in die Session
*@param int AccountID
*@author talion, using LOGDCore code
*/
function user_load ($int_acctid) {
	global $session,$session_copy,$titles,$maximumrep;
	
	$int_acctid = (int)$int_acctid;
	
	if(!$int_acctid) { return(false); }
	
	$sql = "SELECT * FROM accounts WHERE acctid = '".$int_acctid."'";
	$result = db_query($sql,false);
	
	// Account vorhanden
	if (db_num_rows($result)==1)
	{
		
		// Reinladen
		$session['user']=db_fetch_assoc($result);

		// Array mit Kopie der Vars anlegen, die oft und gerne überschrieben werden
		$session_copy = array();
		user_create_session_copy();
		// END Kopie

		$session['output'] = $session['user']['output'];
		
		// Spezialrechte laden
		if($session['user']['superuser'] > 0) {
			
			user_load_surights();
		
		}		
		else {
			unset($session['surights']);
			$session['user']['surights'] = '';
		}
		
		// Wenn Benachrichtigung wegen Accountverfall an User geschickt wurde, Status zurücksetzen
		if ($session['user']['activated'] == USER_ACTIVATED_SENTNOTICE)
		{
			$session['user']['activated'] = USER_ACTIVATED_DEFAULT;
		}
		
		$session['user']['dragonpoints']=unserialize($session['user']['dragonpoints']);
		$session['user']['prefs']=unserialize($session['user']['prefs']);
		$session['user']['specialtyuses'] = unserialize($session['user']['specialtyuses']);
		$session['allowednavs'] = unserialize($session['user']['allowednavs']);
				
		if (!is_array($session['user']['dragonpoints'])) 
		{
			$session['user']['dragonpoints']=array();
		}
		
		$session['bufflist']=unserialize($session['user']['bufflist']);
		if (!is_array($session['bufflist']))
		{
			$session['bufflist']=array();
		}
		
		if ($session['user']['hitpoints']>0)
		{
			$session['user']['alive']=true;
		}
		else
		{
			$session['user']['alive']=false;
		}
		
		if($session['user']['superuser']) {
			if($session['su_return'] == calcreturnpath()) {
				$session['su_return'] = '';
			}
			
			if($session['su_return'] == '') {
				$session['su_return'] = 'village.php';
			}
		}
		
		// DDL-location bei jedem Klick resetten
		$session['user']['ddl_location'] = 0;
	
		$maximumrep=max_reputation();
		if ($session['user']['reputation']>$maximumrep)
		{ 
			$session['user']['reputation']=$maximumrep;
		}
						
		// RP-Wiedererweckung
		if($session['user']['spirits'] == RP_RESURRECTION) {
			$session['user']['turns'] = 0;
			$session['user']['castleturns'] = 0;
			$session['user']['playerfights'] = 0;
			$session['user']['fedmount'] = 1;
			$session['user']['seenmaster'] = 2;
			$session['user']['seendragon'] = 1;
			$session['user']['seenlover'] = 1;
			$session['bufflist'] = array();
			$session['user']['hitpoints'] = min($session['user']['hitpoints'],1);			
		}		
				
		if($session['user']['hashorse']) {
	
			getmount($session['user']['hashorse']);
			
		}
				
		//Getting the titles from the settings table
		//Dragonslayer
		$titles = unserialize(getsetting('title_array',null));
		
		// Für die Statistik
		$session['laston_back'] = $session['user']['laston'];
		
		$session['user']['laston']=date('Y-m-d H:i:s');
		$session['user']['uniqueid'] = $session['uniqueid'];
		$session['user']['lastip'] = $session['lastip'];
										
	}	
	// END Account vorhanden
	// Account nicht gefunden
	else {
		$session=array();
		$session['message']='`4Fehler! Dein Login war falsch.`0';
		redirect('index.php','Account verschwunden!');
	}
	
	db_free_result($result);
}

/**
*@desc Benennt einen User um, validiert Namen, kümmert sich auch um Umbenennung in Forum
*@param int AccountID
*@param string Loginname / Farbiger Name
*@param bool Speicherung vornehmen oder nur validieren (optional, Standard true)
*@param bool Änderung des Forenlogins vornehmen (optional, Standard true)
*@author talion
*@return string Fehlercode bzw. neuen Namen
*/
function user_rename ($int_acctid, $str_name, $bool_save = true, $bool_boardlogin = true) {
	global $session;
	
	$bool_player = false;
	
	// Wichtige Infos abrufen
	if($int_acctid == $session['user']['acctid'] || $int_acctid == 0) {
		$bool_player = true;
		$int_acctid = $session['user']['acctid'];
		$arr_info['login'] = $session['user']['login'];
	}
	else {
		$arr_info = db_fetch_assoc( db_query( 'SELECT login FROM accounts WHERE acctid='.$int_acctid ) );
	}
		
	// Feststellen, ob es sich bei Param um farbigen Namen handelt 
	$str_name = stripslashes($str_name);
	$str_login = trim(strip_appoencode($str_name,3));
	$str_cname = false;
	if($str_login != $str_name) {
		$str_cname = trim($str_name);
	}
	
	// Wenn Login leer ist
	if(empty($str_login)) {
        return('login_tooshort');
	}
	
	// Unterschied bei Login?
	if( $arr_info['login'] != $str_login ) {
		// Check auf Ban
		if( checkban($str_login) ) {
			return('login_banned');			
		}
		
		// Check auf Eintrag in BlackList
		if( check_blacklist(BLACKLIST_LOGIN, $str_login) ) {
			return('login_blacklist');
		}	
		
		// Check auf Duplikat
		if( db_num_rows(db_query('SELECT acctid FROM accounts WHERE login="'.addslashes($str_login).'"')) ) {
			return('login_dupe');
		}
		
		// Login validieren
	
		// Keine Leerzeichen erlaubt
		if(getsetting("spaceinname",0) == 0) {
			if( preg_match("/([\s])/",$str_login) ) { 
				return('login_spaceinname');
			}
		} 
		
		// Wenn keine Sonderzeichen in Namen erlaubt
		if(getsetting("specialkeys",0) == 0) {
			if( preg_match("/([^[:alpha:]\s_-])/",$str_login) ) {
				return('login_specialcharinname');
			}
		} 
		
		// Generell Zeichen, die nichts in einem Namen zu suchen haben
		if(getsetting("criticalchars",0) == 0) {
			if( preg_match("/([0-9_|\/\.,;])/",$str_login) ) {
				return('login_criticalcharinname');
			}
		}
		
		// Prüfen, ob wir einen offiziellen Titel im Namen haben 
		$titles = unserialize( stripslashes(getsetting('title_array',null)) );
		if(is_array($titles)) {
			$str_checklogin = strtolower($str_login);
			foreach($titles as $t) {
				
				if(strpos($str_checklogin,strtolower($t[0])) !== false || strpos($str_checklogin,strtolower($t[1])) !== false) {
					return('login_titleinname');
				}
			}
		}
		
		// Länge checken
		$int_min_len = getsetting('nameminlen',3);
		$int_max_len = getsetting('namemaxlen',25);
		if (strlen($str_login) < $int_min_len){
			return('login_tooshort');			
		}
		if (strlen($str_login) > $int_max_len){
			return('login_toolong');
		}
					
		// Böse Sachen im Namen				
		if (soap($str_login)!=$str_login){
			return('login_badword');			
		}
		
		if($bool_save) {
			// Login: Passt!
			// Login Speichern
			if($bool_player) {
				$session['user']['login'] = $str_login;
			}
			else {
				$sql = 'UPDATE accounts SET login="'.addslashes($str_login).'"
						WHERE acctid = '.$int_acctid;
				db_query($sql);
			}
		}
		// END LOGIN
					
		// Name ist unterschiedlich, farbigen Namen löschen
		$str_cname = '';
		// Forenlogin ändern
		if($bool_boardlogin) {
			$arr_info_e = user_get_aei('incommunity',$int_acctid);
			if($arr_info_e['incommunity']) {
				require_once(LIB_PATH.'communityinterface.lib.php');
			
				ci_rename($int_acctid, $str_login);
			}
		}
							
	}
	
			
	// TODO: Validierung des farbigen Namens
	if(is_string($str_cname)) {
		
		// Max. Anzahl der Farbcodes
		$int_colorcount = getsetting("maxcolors",10);
		if(substr_count($str_cname,'`') > $int_colorcount) {
			return('cname_toomuchcolors');
		}
				
		// Speichern
		if($bool_save) {
			$str_cname = addslashes($str_cname);
			user_set_aei( array('cname'=>$str_cname), $int_acctid );	
		}
	}
		
	return(true);
}


/**
*@desc Ändert Titel eines Users, validiert diesen
*@param int AccountID
*@param string Regulärer Titel
*@param string Eigener Titel
*@param bool Speichern? (Optional, Standard true)
*@author talion
*@return string Fehlercode bzw. true
*/
function user_retitle ($int_acctid, $str_title, $str_ctitle, $bool_save = true) {
	global $session,$arr_titles_nochange;
	
	$bool_player = false;
	
	// Wichtige Infos abrufen
	if($int_acctid == $session['user']['acctid'] || $int_acctid == 0) {
		$bool_player = true;
		$int_acctid = $session['user']['acctid'];
		$arr_info = user_get_aei('ctitle');
		$arr_info['title'] = $session['user']['title'];
	}
	else {
		$arr_info = db_fetch_assoc( db_query( 'SELECT title FROM accounts WHERE acctid='.$int_acctid ) );
		$arr_info = array_merge( $arr_info, user_get_aei('ctitle',$int_acctid) );
	}
			
	$str_title = stripslashes(trim($str_title));
	$str_ctitle = stripslashes(trim($str_ctitle));
				
	// Eigenen Titel validieren
	if(is_string($str_ctitle)) {
		
		if(!empty($str_ctitle)) {
			$str_checktitle = strip_appoencode(strtolower($str_ctitle),3);
			
			if(strpos($str_checktitle,'*')) {
				return('ctitle_blacklist');
			}
					
			// Check auf Eintrag in BlackList
			if( check_blacklist(BLACKLIST_TITLE, $str_checktitle) ) {
				return('ctitle_blacklist');
			}
			
			// Länge checken
			$int_min_len = getsetting('nameminlen',3);
			$int_max_len = getsetting('namemaxlen',25);
			if (strlen($str_ctitle) < $int_min_len){
				return('ctitle_tooshort');			
			}
			if (strlen($str_ctitle) > $int_max_len){
				return('ctitle_toolong');
			}
						
			// Böse Sachen im Titel
			if (soap($str_ctitle)!=$str_ctitle){
				return('ctitle_badword');			
			}
						
			// Prüfen, ob wir einen offiziellen Titel verwenden
			$titles = unserialize( stripslashes(getsetting('title_array',null)) );
			if(is_array($titles)) {
				foreach($titles as $t) {
					
					if(strpos($str_checktitle,strtolower($t[0])) !== false || strpos($str_checktitle,strtolower($t[1])) !== false) {
						return('ctitle_officialtitle');
					}
				}
			}
		}
						
		if($bool_save) {
			// ctitle: Passt!
			user_set_aei(array('ctitle'=>addslashes($str_ctitle)),$int_acctid);
		}
	}
	// END CTITLE
	
	// regulärer Titel
	if(is_string($str_title) && !empty($str_title) && $bool_save) {
		// Speichern
		if($bool_player) {
			$session['user']['title'] = $str_title;
		}
		else {
			$sql = 'UPDATE accounts SET title="'.addslashes($str_title).'"
					WHERE acctid = '.$int_acctid;
			db_query($sql);
		}
	}
		
			
	return(true);
}

/**
*@desc 	Erstellt aus bereits vorher gegebenen Daten vollwertigen Spielernamen
*		Nimmt selbst keinerlei Validierung vor!		
*@param int AccountID
*@param bool Speichern? (Optional, Standard true)
*@author talion
*@return string Fehlercode bzw. neuen Namen
*/
function user_set_name ($int_acctid, $bool_save = true) {
	
	global $session;
	
	$bool_player = false;
	
	// Wichtige Infos abrufen
	if($int_acctid == $session['user']['acctid'] || $int_acctid == 0) {
		$bool_player = true;
		$int_acctid = $session['user']['acctid'];
		$arr_info['login'] = $session['user']['login'];
		$arr_info['title'] = $session['user']['title'];
	}
	else {
		$arr_info = db_fetch_assoc( db_query( 'SELECT login, title FROM accounts WHERE acctid='.$int_acctid ) );
	}
	$arr_info_e = user_get_aei('ctitle,cname',$int_acctid);
	// END infos abrufen
	
	$str_realtitle 	= !empty($arr_info_e['ctitle']) ? $arr_info_e['ctitle'] : $arr_info['title'];
	$str_realname 	= !empty($arr_info_e['cname']) 	? $arr_info_e['cname'] 	: $arr_info['login'];
	$str_name 		= trim($str_realtitle).' '.trim($str_realname);
	// Speichern
	if($bool_save) {
		if($bool_player) {
			$session['user']['name'] = $str_name;
		}
		else {
			$sql = 'UPDATE accounts SET name="'.addslashes($str_name).'"
					WHERE acctid = '.$int_acctid;
			db_query($sql);
		}
	}
	
	return($str_name);
	
}

/**
*@desc Löscht einen User komplett aus der Datenbank.
*@param int Accountid des zu löschenden Users, muss gegeben sein.
*@return TRUE / FALSE
*@author Drachenserver-Team
*/
function user_delete ($uid) {
	require_once(LIB_PATH.'communityinterface.lib.php');
	
	$uid = (int)$uid;

	if(!$uid) {return(false);}
	
	$sql = 'SELECT guildid,dragonkills,cname,login FROM accounts
			LEFT JOIN account_extra_info USING(acctid) WHERE accounts.acctid='.$uid;
	$acc = db_fetch_assoc(db_query($sql));
	
	$acc['tmpname'] = ($acc['cname'] ? $acc['cname'] : $acc['login']);
	
	// Fürstentitel vakant setzen
	$fuerst = stripslashes(getsetting('fuerst',''));
	if($fuerst == $acc['tmpname']) {
		savesetting('fuerst','');
	}
	
	// inventar und haus löschen und partner und ei freigeben
	if ($uid==getsetting('hasegg',0)) {
		savesetting('hasegg',stripslashes(0));
		$sql = 'UPDATE items SET owner=0 WHERE tpl_id="goldenegg"';
		db_query($sql);
	}
	
	$sql = 'UPDATE keylist SET owner=0 WHERE owner='.$uid;
    db_query($sql) or die(db_error(LINK));
	if ($session['user']['superuser']>1) output($sql."`n");

    $sql = 'UPDATE houses SET owner=0,status=3 WHERE owner='.$uid.' AND status=1';
    db_query($sql) or die(db_error(LINK));
	if ($session['user']['superuser']>1) output($sql."`n");

    $sql = 'UPDATE houses SET owner=0,status=4 WHERE owner='.$uid.' AND (status=0 OR status=5)';
    db_query($sql) or die(db_error(LINK));
	if ($session['user']['superuser']>1) output($sql."`n");

    // Ausgebaute Häuser als verlassen markieren
    $sql = 'UPDATE houses SET owner=0,status=status+2 WHERE owner='.$uid.' AND (status=10 or status=14 or status=17 or status=20 or status=24 or status=27 or status=30 or status=34 or status=37 or status=40 or status=44 or status=47 or status=50 or status=54 or status=57 or status=60 or status=64 or status=67 or status=70 or status=74 or status=77 or status=80 or status=84 or status=87 or status=90 or status=94 or status=97 or status=100 or status=104 or status=107)';
    db_query($sql) or die(db_error(LINK));
	if ($session['user']['superuser']>1) output($sql."`n");

    $sql = 'UPDATE accounts SET charisma=0,marriedto=0 WHERE marriedto='.$uid;
    db_query($sql) or die(db_error(LINK));
	if ($session['user']['superuser']>1) output($sql."`n");

    $sql = 'DELETE FROM yom_adressbuch WHERE player='.$uid.' OR acctid='.$uid;
    db_query($sql) or die(db_error(LINK));
	if ($session['user']['superuser']>1) output($sql."`n");

    $sql = 'DELETE FROM boards WHERE author='.$uid;
    db_query($sql) or die(db_error(LINK));
	if ($session['user']['superuser']>1) output($sql."`n");

    $sql = 'DELETE FROM disciples WHERE master='.$uid;
    db_query($sql) or die(db_error(LINK));
	if ($session['user']['superuser']>1) output($sql."`n");
	
	// VOR Items löschen: Privatgemächer zurücksetzen
	$sql = 'UPDATE items SET owner=0 WHERE tpl_id="privb" AND owner='.$uid;
	db_query($sql);
	$sql = 'DELETE FROM items WHERE tpl_id="prive" AND value2='.$uid;
	db_query($sql);
			 
	// Items löschen
	$sql = 'DELETE FROM items WHERE owner='.$uid;
    db_query($sql) or die(db_error(LINK));
	if ($session['user']['superuser']>1) output($sql."`n");

    $sql = 'DELETE FROM pvp WHERE acctid2='.$uid.' OR acctid1='.$uid;
	db_query($sql) or die(db_error(LINK));
	if ($session['user']['superuser']>1) output($sql."`n");

    $sql = 'DELETE FROM accounts WHERE acctid='.$uid;
    db_query($sql) or die(db_error(LINK));
	if ($session['user']['superuser']>1) output($sql."`n");
 
	//aus forum löschen
	$inc = user_get_aei("incommunity", $uid);
	if( $inc['incommunity'] > 0 ){ 
		ci_deleteuser($inc['incommunity']);
	}
	
    $sql = 'DELETE FROM account_extra_info WHERE acctid='.$uid;
	if ($session['user']['superuser']>1)	output($sql."`n");
    db_query($sql) or die(db_error(LINK));
    
    $sql = 'DELETE FROM goldpartner WHERE acctid='.$uid;
	if ($session['user']['superuser']>1)	output($sql."`n");
    db_query($sql) or die(db_error(LINK));
	
	// Statistiken löschen
	$sql = 'DELETE FROM account_stats WHERE acctid='.$uid;
	if ($session['user']['superuser']>1)	output($sql."`n");
    db_query($sql) or die(db_error(LINK));
	
	// Aus Flirtliste löschen
	flirt_set(0,$uid,0,-1,0);
			
	$sql = 'DELETE FROM history WHERE acctid='.$uid;
	db_query($sql);
		
	return(true);	
	
}

/**
*@desc Ermittelt Online-Status eines Spielers
*		Entweder ruft Funktion die dazu benötigten Accountdaten per acctid ab oder verwendet die per Param
*		übergebenen. Falls keines davon gegeben: Gibt sie den Queryteil zurück, der zu einem Check benötigt wird
*@param int Accountid des Users. Optional.
*@param array Accountdaten des Users. Optional. Enthalten muss sein: loggedin, laston, activated
*@param bool User im Stealthmode anzeigen ja / nein. Für SU > 0 sind diese immer sichtbar.
*@return mixed Entweder bool (User online / offline) oder string (SQL-String)
*@author talion
*/
function user_get_online ($acctid=0,$acctinfo=false,$show_stealth=false) {
	
	global $session;
	
	$acctid = (int)$acctid;
	$is_su = $session['user']['superuser'];
	$timeout = getsetting('LOGINTIMEOUT',900);
	$timeout_date = date( 'Y-m-d H:i:s' , time() - $timeout );
	
	if($acctid) {
		
		$sql = 'SELECT loggedin,laston,activated FROM accounts WHERE acctid='.$acctid;
		$res = db_query($sql);
		$acctinfo = db_fetch_assoc($res);
		
	}
	
	if(is_array($acctinfo)) {
		$online = ($acctinfo['loggedin'] == 1 && $acctinfo['laston'] > $timeout_date && ($is_su || $show_stealth || $acctinfo['activated'] != USER_ACTIVATED_STEALTH) ? true : false);
		return($online);
		
	}
	
	return( ' loggedin=1 AND laston>"'.$timeout_date.'" '.(!$is_su && !$show_stealth ? ' AND activated!='.USER_ACTIVATED_STEALTH : '') );
	
}

/**
*@desc Zeigt eine Userliste in Tabellenform an
*@param int Spieler pro Seite (Optional, Standard 50)
*@param string SQL-WHERE-Konditionen (Optional, Standard keine)
*@param string SQL-ORDER BY-Anweisungen (Optional, Standard level, dks etc.)
*@param bool Suchmaske anzeigen (Optional, Standard false)
*@param int Maximale Anzahl an Spielern, die angezeigt werden sollen
*@author talion unter Verwendung von Core-Code
*/
function user_show_list ($playersperpage=50,
$where='',
$orderby=' level DESC, dragonkills DESC, name ASC',
$show_search=false,
$max_show = 100
) {

	global $session,$colraces;

	$link = calcreturnpath();
	$link = $link .= (strstr($link,'?')?'&':'?');

	$where = ($where != '') ? $where : '1';
	$search = '';

	$sql = 'SELECT count(acctid) AS c FROM accounts WHERE locked=0 AND '.$where;
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	$totalplayers = $row['c'];

	if ($_GET['op']=="search" && strlen($_POST['name']) > 2 ){
		$search="%";
		for ($x=0;$x<strlen($_POST['name']);$x++){
			$search .= substr($_POST['name'],$x,1)."%";
		}
		$search=' AND a.name LIKE "'.addslashes($search).'"';
	}
	else{
		$pageoffset = (int)$_GET['page'];
		if ($pageoffset>0) $pageoffset--;
		$pageoffset*=$playersperpage;
		$from = $pageoffset+1;
		$to = min($pageoffset+$playersperpage,$totalplayers);
		$limit=' LIMIT '.$pageoffset.','.$playersperpage;
	}
	addnav("Seiten");
	for ($i=0;$i<$totalplayers;$i+=$playersperpage)
	{
		addnav('Seite '.($i/$playersperpage+1).' ('.($i+1).'-'.min($i+$playersperpage,$totalplayers).')',$link.'page='.($i/$playersperpage+1));
	}

	// Order the list by level, dragonkills, name so that the ordering is total!
	// Without this, some users would show up on multiple pages and some users
	// wouldn't show up
	/*if ($_GET['page']=="" && $_GET['op']==""){
	output("`c`bDiese Krieger sind gerade online`b`c");
	$sql = "SELECT acctid,name,login,alive,location,sex,level,laston,loggedin,lastip,uniqueid,race FROM accounts WHERE locked=0 AND loggedin=1 AND laston>'".date("Y-m-d H:i:s",strtotime(date("r")."-".getsetting("LOGINTIMEOUT",900)." seconds"))."' ORDER BY level DESC, dragonkills DESC, login ASC";
	}else{
	output("`c`bKrieger in dieser Welt (Seite ".($pageoffset/$playersperpage+1).": $from-$to von $totalplayers)`b`c");
	$sql = "SELECT acctid,name,login,alive,location,sex,level,laston,loggedin,lastip,uniqueid,race FROM accounts WHERE locked=0 $search ORDER BY level DESC, dragonkills DESC, login ASC $limit";
	}*/

	$sql = 'SELECT superuser,activated,acctid,a.name,login,alive,location,sex,level,laston,loggedin,lastip,uniqueid,race,g.name AS guildname FROM accounts a LEFT JOIN dg_guilds g ON g.guildid=a.guildid AND a.guildfunc!=1 WHERE locked=0 AND '.$where.' '.$search.' ORDER BY '.$orderby.' '.$limit;

	if ($session['user']['loggedin'] && $show_search){
		output('<form action="'.$link.'op=search" method="POST">Nach Name suchen: <input name="name"><input type="submit" class="button" value="Suchen"></form>',true);
		addnav('',$link.'op=search');
	}

	$result = db_query($sql) or die(sql_error($sql));
	$max = db_num_rows($result);
	if ($max>$max_show) {
		output('`$Es treffen zu viele Namen auf diese Suche zu. Nur die ersten 100 werden angezeigt.`0`n');
	}

	if($totalplayers > $playersperpage && $from) {
		output('`bSeite '.($pageoffset/$playersperpage+1).': '.$from.'-'.$to.' von '.$totalplayers.'`b`n');
	}
	
	$arr_groups = unserialize( stripslashes(getsetting('sugroups','')) );
	
	output('<table border=0 cellpadding=2 cellspacing=1 bgcolor="#999999">',true);
	output('<tr class="trhead"><td><b>Level</b></td><td><b>Name</b></td><td><b>Rasse</b></td><td><b><img src="images/female.gif">/<img src="images/male.gif"></b></td><td><b>Ort</b></td><td><b>Status</b></td><td><b>Zuletzt da</b></td><td width="90"><b>Gilde</b></td></tr>',true);
	for($i=0;$i<$max;$i++)
	{
		$row = db_fetch_assoc($result);
		
		$row['guildname'] = ($row['guildname']) ? $row['guildname'] : 'Keine';
		
		output("<tr class='".($i%2?"trdark":"trlight")."'><td>",true);
		output("`^$row[level]`0");
		output("</td><td>",true);
		if ($session['user']['loggedin']) {
			$biolink = 'bio.php?id='.$row['acctid'].'&ret='.URLEncode($_SERVER['REQUEST_URI']);
			output("<a href=\"mail.php?op=write&to=".rawurlencode($row['login'])."\" target=\"_blank\" onClick=\"".popup("mail.php?op=write&to=".rawurlencode($row['login'])."").";return false;\"><img src='images/newscroll.GIF' width='16' height='16' alt='Mail schreiben' border='0'></a>",true);
			output('<a href="'.$biolink.'">',true);
			addnav('',$biolink);
		}

		output("`".($row[acctid]==getsetting("hasegg",0)?"^":"&")."$row[name]`0");
		
		if($row['superuser'] > 0) {
			if($arr_groups[$row['superuser']][3]) {
				output(' `n`7'.$arr_groups[$row['superuser']][0].'`0');
			}			
		}

		if($session['user']['loggedin']) output("</a>",true);
		
		if(su_check(SU_RIGHT_WATCHSU)) {
			output(' [<a href="#" target="_blank" onClick="'.popup('watchsu.php?userid='.$row['acctid']).';return false;" >BB</a>]',true);
		}
		
		output("</td><td>",true);
		output($colraces[$row['race']]);
		output("</td><td align=\"center\">",true);
		output($row[sex]?"<img src=\"images/female.gif\">":"<img src=\"images/male.gif\">",true);
		output("</td><td>",true);
		$loggedin=user_get_online(0,$row);
		if ($row[location]==USER_LOC_FIELDS) output($loggedin?"`#Online`0":"`3Die Felder`0");
		if ($row[location]==USER_LOC_INN) output("`3Zimmer in Kneipe`0");
		if ($row[location]==USER_LOC_HOUSE) output("`3Im Haus`0");
		if ($row[location]==USER_LOC_PRISON) output("`3Im Kerker`0");
		output("</td><td>",true);
		output($row[alive]?"`1Lebt`0":"`4Tot`0");
		output("</td><td>",true);
		//$laston=round((strtotime("0 days")-strtotime($row[laston])) / 86400,0)." Tage";
		$laston=round((strtotime(date("r"))-strtotime($row[laston])) / 86400,0)." Tage";
		if (substr($laston,0,2)=="1 ") $laston="1 Tag";
		if (date("Y-m-d",strtotime($row[laston])) == date("Y-m-d")) $laston="Heute";
		if (date("Y-m-d",strtotime($row[laston])) == date("Y-m-d",strtotime(date("r")."-1 day"))) $laston="Gestern";
		if ($loggedin) $laston="Jetzt";
		output($laston);
		output("</td><td>",true);
		output('`c'.$row['guildname'].'`c');		
		output("</td></tr>",true);
	}
	output("</table>",true);
}

// Folgende zwei Funktionen automatisieren die Vergabe besonderer Titel wie Fürst von Atrahor oder auch Ramius Sklave und Flauschihase usw.
// In der Klammer der Funktion braucht nur der Titel zu stehen zum Aufruf - der Rest funktioniert automatisch, müsste auch bei jeder beliebigen Neueinführung funktionieren

function set_title($special_title)
{
    global $session;

    if($special_title == 'Fürst') {
        $sql = 'SELECT ai.acctid, a.login, a.sex FROM account_extra_info ai LEFT JOIN accounts a USING(acctid) ORDER BY ai.sympathy DESC, a.dragonkills DESC LIMIT 1';
	    $res = db_query($sql);
	    $row_extra = db_fetch_assoc($res);
	    $accountid = $row_extra['acctid'];
	    $accountlogin = $row_extra['login'];
	    $accountsex = $row_extra['sex'];
    }
    else {
        $accountid = $session['user']['acctid'];
        $accountlogin = $session['user']['login'];
    }

    $sql = 'SELECT ctitle,cname FROM account_extra_info WHERE acctid="'.$accountid.'"';
	$res = db_query($sql);
	$row_extra = db_fetch_assoc($res);

    if($row_extra['ctitle']!='') {
        $backup_title = $row_extra['ctitle'];
    }
    else {
        $backup_title = '';
    }

    if($row_extra['cname']!='') {
        $name = $row_extra['cname'];
    }
    else {
        $name = $accountlogin;
    }

    if($accountsex == "1") {
        $special_title = "Fürstin von ".getsetting('townname','Atrahor');
    }
    else
    {
        $special_title = "Fürst von ".getsetting('townname','Atrahor');
    }

    $new = trim($special_title).' '.trim($name);
    if($accountid != $session['user']['acctid']) {
    $sql = 'UPDATE accounts SET name="'.$new.'" WHERE acctid="'.$accountid.'"';
			db_query($sql);
	}
    else {
	    $session['user']['name'] = $new;
	}

    $sql = 'UPDATE account_extra_info SET ctitle_backup="'.$backup_title.'", ctitle="'.$special_title.'" WHERE acctid="'.$accountid.'"';
			db_query($sql);
	$sql = 'INSERT INTO news (newstext,newsdate,accountid) values ("Heute wurde '.$name.' `@aufgrund '.($accountsex ? 'ihrer' : 'seiner').' Beliebtheit '.($accountsex ? 'zur neuen Fürstin' : 'zum neuen Fürsten').' von '.getsetting('townname','Atrahor').' gekürt", NOW(),'.$accountid.')';
            db_query($sql);
			
	addhistory('Wahl zu'.($accountsex ? 'r Fürstin von '.getsetting('townname','Atrahor').'!' : 'm Fürsten von '.getsetting('townname','Atrahor').'!'),1,$accountid);
	systemmail($accountid,'`^Du BIST Fürst'.($accountsex ? 'in' : '').'!','`^Deine immense Beliebtheit unter den Bürgern '.getsetting('townname','Atrahor').'s hat dir zum Fürstentitel verholfen! Herzlichen Glückwunsch.');
	
	return($name);
	
}
function get_title($special_title)
{
    global $session;

    if($special_title == 'Fürst') {
        $title1='Fürst von '.getsetting('townname','Atrahor');
        $title2='Fürstin von '.getsetting('townname','Atrahor');
        $sql = 'SELECT a.acctid,title,login FROM account_extra_info ai LEFT JOIN accounts a using (acctid) WHERE ctitle="'.$title1.'" OR ctitle="'.$title2.'" LIMIT 1';
	    $res = db_query($sql);
	    $row_extra = db_fetch_assoc($res);
	    $accountid = $row_extra['acctid'];
	    $accounttitle = $row_extra['title'];
	    $accountlogin = $row_extra['login'];
    }
    else {
        $accountid = $session['user']['acctid'];
        $accounttitle = $session['user']['title'];
	    $accountlogin = $session['user']['login'];
    }

    $sql = 'SELECT ctitle,cname,ctitle_backup FROM account_extra_info WHERE acctid="'.$accountid.'"';
	$res = db_query($sql);
	$row_extra = db_fetch_assoc($res);

    if($row_extra['ctitle_backup']!='') {
        $retitle = $row_extra['ctitle_backup'];
    }
    else {
        $retitle = $accounttitle;
    }

    if($row_extra['cname']!='') {
        $name = $row_extra['cname'];
    }
    else {
        $name = $accountlogin;
    }

    $new = trim($retitle).' '.trim($name);

    if($accountid != $session['user']['acctid']) {
            $sql = 'UPDATE accounts SET name="'.$new.'" WHERE acctid="'.$accountid.'"';
			db_query($sql);
    }
    else {
          $session['user']['name'] = $new;
    }
    $sql = 'UPDATE account_extra_info SET ctitle="'.$retitle.'", ctitle_backup="" WHERE acctid="'.$accountid.'"';
			db_query($sql);
			
	return($new);
}


define("LOSEGOLD_VALUE",   	1);  //anzahl an gold verlieren
define("LOSEEXP_VALUE", 	2);	//bestimmten wert an exp verlieren

/**
*@desc Killt nen User
* Prozente in form: [0-100]
*@param losegold (int) wert vom goldverlust (standardmäßig 100%)
*@param loseexp (int) wert vom expverlust (standardmäßig %)
*@param killdisciple (int) wird der knppe sterben? 0 = nein; !=0 = jupp
*@param redirect (string) was kommt nach dem tod?
*@param killflags (int) flags, die das verhalten bestimmen (ODER-VERKNÜPFT)
*@return array Verluste des Spielers, nach dem Muster: 'gold'=>Wert, 'disciple'=>Knappen-Datensatz...
*@author Alucard, modded by talion
*/

function killplayer( $losegold		= 100, 	
					 $loseexp		= 0, 
					 $killdisciple	= 0, 
					 $redirect		= "shades.php", 
					 $killflags		= 0)
{
	global $session;
	
	$results = array();
	
	//goldverlust
	if( $session['user']['gold'] && $losegold > 0 ){
	
		if( !($killflags & LOSEGOLD_VALUE) ){
			$lostgold = round($session['user']['gold']*($losegold/100));
			$session['user']['gold'] -= $lostgold;
			$results['gold'] = $lostgold;
		}	
		elseif( $losegold > $session['user']['gold'] ){
			$results['gold'] = $session['user']['gold'];
			$session['user']['gold'] = 0;
		}
		else{
			$results['gold'] = $losegold;
			$session['user']['gold'] -= $losegold; 
		}
	}
	
	
	//erfahrungsverlust
	if( $session['user']['experience'] && $loseexp > 0 ){
	
		if( !($killflags & LOSEEXP_VALUE) ){
			$lostexp = round($session['user']['experience']*($loseexp/100),0);
			$session['user']['experience'] -= $lostexp;
			$results['experience'] = $lostexp;
		}	
		elseif( $loseexp > $session['user']['experience'] ){
			$session['user']['experience'] = 0;
			$results['experience'] = $session['user']['experience'];
		}
		else{
			$session['user']['experience'] -= $loseexp; 
			$results['experience'] = $loseexp;
		}
	}
	
	//knappenzeug
	$sql = "SELECT state, level, name FROM disciples WHERE master=".$session['user']['acctid']." LIMIT 1";
	$res = db_query($sql);
	$disciple_buff = array();
	// Wenn Knappe vorhanden
	if(db_num_rows($res)) {
		$disciple = db_fetch_assoc($res);
		
		// Wenn kein untoter Knappe
		if ($disciple['state'] > 0 && $disciple['state'] < 20){
			if($killdisciple){
				disciple_remove();
				$results['disciple'] = $disciple;
			}		
		}
		// Sonst: 
		else {
			// Buff im Totenreich behalten
			$disciple_buff = $session['bufflist']['decbuff'];
		}
	}
	
	//sonstige weltliche werte zurücksetzen :> 
	$session['bufflist'] 				= array();
	$session['user']['maze_visited'] 	= "";
	$session['user']['badguy']			= "";
	
	if(!empty($disciple_buff)) {
		$session['bufflist']['decbuff'] = $disciple_buff;
	}
			
	//Gnadenstoß :P		
	$session['user']['alive'] 		 = 0;
	$session['user']['hitpoints'] 	 = 0;
	
	if( !empty($redirect) ){
		$session['user']['specialinc'] 	 = "";
		redirect($redirect);
	}
	else{
		saveuser();
		return( $results );
	}
		
}
?>
