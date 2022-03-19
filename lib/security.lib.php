<?php
/**
* security.lib.php: Funktionsbibliothek für Methoden, die diversen Sicherheitszwecken dienen
* @author LOGD-Core / Drachenserver-Team
* @version DS-E V/2
*/

// Konstantendefs für SU-Rechte
define('SU_RIGHT_PETITION',1);
define('SU_RIGHT_CASTLECHOOSE',2);
define('SU_RIGHT_NEWDAY',3);
define('SU_RIGHT_FORESTSPECIAL',4);
define('SU_RIGHT_STEALTH',5);
define('SU_RIGHT_EDITORUSER',6);
define('SU_RIGHT_EDITORGUILDS',7);
define('SU_RIGHT_DEBUG',8);
define('SU_RIGHT_COMMENT',9);
define('SU_RIGHT_RIGHTS',10);
define('SU_RIGHT_FAILLOG',11);
define('SU_RIGHT_EDITORHOUSES',12);
define('SU_RIGHT_EDITORITEMS',13);
define('SU_RIGHT_EDITORCASTLES',14);
define('SU_RIGHT_EDITORLIBRARY',15);
define('SU_RIGHT_GAMEOPTIONS',16);
define('SU_RIGHT_DONATIONS',17);
define('SU_RIGHT_LOGOUTALL',18);
define('SU_RIGHT_CHECKBOARDS',19);
define('SU_RIGHT_MAILBOX',20);
define('SU_RIGHT_EDITORTITLES',21);
define('SU_RIGHT_EDITORCOLORS',22);
define('SU_RIGHT_EDITOREXTTXT',23);
define('SU_RIGHT_EDITORSPECIALTIES',24);
define('SU_RIGHT_EDITORWORLD',25);
define('SU_RIGHT_EDITORMOUNTS',26);
define('SU_RIGHT_MOTD',27);
define('SU_RIGHT_RETITLE',28);
define('SU_RIGHT_WARTUNG',29);
define('SU_RIGHT_WATCHSU',30);
define('SU_RIGHT_EXPEDITION',31);
define('SU_RIGHT_MUTE',32);
define('SU_RIGHT_PRISON',33);
define('SU_RIGHT_LOCKHTML',34);
define('SU_RIGHT_NEWS',35);
define('SU_RIGHT_COMMENTPRIV',36);
define('SU_RIGHT_SULVL',37);
define('SU_RIGHT_GODMODE',38);
define('SU_RIGHT_EDITORRANDOMCOM',39);
define('SU_RIGHT_EDITORFORESTSPECIAL',40);
define('SU_RIGHT_CASTLEMAP',41);
define('SU_RIGHT_DEV',42);
define('SU_RIGHT_EXPEDITION_ADMIN',43);
define('SU_RIGHT_REGISTRATUR',44);
define('SU_RIGHT_STATS',45);
define('SU_RIGHT_DEBUGLOG',46);
define('SU_RIGHT_SYSLOG',47);

// Namen / Beschreibungen für SU-Rechte
$ARR_SURIGHTS = array( SU_RIGHT_PETITION => array('desc'=>'Darf Anfragen bearbeiten?')
						,SU_RIGHT_CASTLECHOOSE => array('desc'=>'Darf Schloss wählen?')
						,SU_RIGHT_CASTLEMAP => array('desc'=>'Sieht Superusermap im Schloß?')
						,SU_RIGHT_NEWDAY => array('desc'=>'Darf Neuen Tag auslösen?')
						,SU_RIGHT_FORESTSPECIAL => array('desc'=>'Waldspecialauswahl?')
						,SU_RIGHT_STEALTH => array('desc'=>'Darf Stealthmodus nutzen?')
						,SU_RIGHT_EDITORUSER => array('desc'=>'Darf User bearbeiten?')
						,SU_RIGHT_EDITORGUILDS => array('desc'=>'Darf Gilden bearbeiten?')
						,SU_RIGHT_DEBUG => array('desc'=>'Allgemeine Debug-Funktionen (Mal testen etc.)?')
						,SU_RIGHT_COMMENT => array('desc'=>'Darf Kommentarsektionen überwachen?')
						,SU_RIGHT_RIGHTS => array('desc'=>'Darf Superuser-Rechte einstellen?')
						,SU_RIGHT_FAILLOG => array('desc'=>'Darf Faillog ansehen?')
						,SU_RIGHT_EDITORHOUSES => array('desc'=>'Darf Häuser bearbeiten?')
						,SU_RIGHT_EDITORITEMS => array('desc'=>'Darf Items bearbeiten?')
						,SU_RIGHT_EDITORCASTLES => array('desc'=>'Darf Schlösser bearbeiten?')
						,SU_RIGHT_EDITORLIBRARY => array('desc'=>'Darf Bibiliothek bearbeiten?')
						,SU_RIGHT_GAMEOPTIONS => array('desc'=>'Darf Spieleinstellungen bearbeiten?')
						,SU_RIGHT_DONATIONS => array('desc'=>'Darf DP vergeben?')
						,SU_RIGHT_LOGOUTALL => array('desc'=>'Darf alle Spieler ausloggen?')
						,SU_RIGHT_CHECKBOARDS => array('desc'=>'Darf Nachrichtenbretter überwachen?')
						,SU_RIGHT_MAILBOX => array('desc'=>'Darf Spieler-Mails überwachen?')
						,SU_RIGHT_EDITORTITLES => array('desc'=>'Darf Titel bearbeiten?')
						,SU_RIGHT_EDITORCOLORS => array('desc'=>'Darf Farbtags bearbeiten?')
						,SU_RIGHT_EDITOREXTTXT => array('desc'=>'Darf Ext. Texte bearbeiten?')
						,SU_RIGHT_EDITORSPECIALTIES => array('desc'=>'Darf Fähigkeiten bearbeiten?')
						,SU_RIGHT_EDITORWORLD => array('desc'=>'Darf Welt-Editoren benutzen (Waffen; Rüstungen etc.)?')
						,SU_RIGHT_EDITORMOUNTS => array('desc'=>'Darf Stalltiere bearbeiten?')
						,SU_RIGHT_MOTD => array('desc'=>'Darf MOTD bearbeiten?')
						,SU_RIGHT_RETITLE => array('desc'=>'Darf Retitler nutzen?')
						,SU_RIGHT_WARTUNG => array('desc'=>'Übergeht Wartungsmodus?')
						,SU_RIGHT_WATCHSU => array('desc'=>'Livebeobachtung des Outputs?')
						,SU_RIGHT_EXPEDITION => array('desc'=>'Ein- / Ausladungen der Expedition?')
						,SU_RIGHT_EXPEDITION_ADMIN => array('desc'=>'Administration der Expedition?')
						,SU_RIGHT_MUTE => array('desc'=>'Knebelfunktion?')
						,SU_RIGHT_PRISON => array('desc'=>'Kerkerfunktion?')
						,SU_RIGHT_LOCKHTML => array('desc'=>'Darf HTML sperren?')
						,SU_RIGHT_NEWS => array('desc'=>'Darf News eintragen / löschen?')
						,SU_RIGHT_COMMENTPRIV => array('desc'=>'Darf Private Kommentare überwachen?')
						,SU_RIGHT_SULVL => array('desc'=>'Darf SU-Levelbutton nutzen?')
						,SU_RIGHT_GODMODE => array('desc'=>'Darf GODMODE nutzen?')
						,SU_RIGHT_EDITORRANDOMCOM => array('desc'=>'Darf Zufallskommentare bearbeiten?')
						,SU_RIGHT_EDITORFORESTSPECIAL => array('desc'=>'Darf Waldereignisse bearbeiten?')
						,SU_RIGHT_DEV => array('desc'=>'Darf versch. Entwicklerrechte ausüben?')
						,SU_RIGHT_REGISTRATUR => array('desc'=>'Darf Registratur verwenden (ohne Löschfunktion)?')
						,SU_RIGHT_STATS => array('desc'=>'Darf Statistiken einsehen?')
						,SU_RIGHT_SYSLOG => array('desc'=>'Darf Systemlog einsehen?')
						,SU_RIGHT_DEBUGLOG => array('desc'=>'Darf Debuglog einsehen?')
						);

/**
* @author talion
* @desc Überprüft, ob der aktuelle User angegebenes Superuser-Recht besitzt
* @param int ID des Rechts (durch Konstanten gegeben)
* @param bool Wenn true, wird Anticheat-Maßnahme durchgeführt. (Optional, Standard false)
* @return bool True oder False.				
*/
function su_check ($int_rid, $bool_becruel = false) {
	
	global $session;
	
	if(!isset($session['surights']) && $session['user']['superuser']) {
		user_load_surights();
	}
	
	if($session['surights'][$int_rid]) {
		return(true);
	}
	else {
		
		if(false === $bool_becruel) {
			return(false);
		}
		else {	
			
			kill_cheater();
								
			return(false);
		}
	}
	
}

/**
* @author talion
* @desc Überprüft, ob der aktuelle User (min) angegebenen Superuser-Lvl besitzt
* @param int Level 
* @param bool Wenn true, wird 1. Param als Min.Lvl gesehen (Optional, Standard true)
* @param bool Wenn true, wird Anticheat-Maßnahme durchgeführt. (Optional, Standard false)
* @return bool True oder False.				
*/
function su_lvl_check ($int_su, $bool_min = true, $bool_becruel = false) {
	
	global $session;
	
	if($bool_min) {
		if($session['user']['superuser'] >= $int_su) {
			return(true);	
		}
	}
	else {
		if($session['user']['superuser'] == $int_su) {
			return(true);	
		}
	}
	
	// Ab hier steht fest: Bedingung nicht gegeben.
	
	if(false === $bool_becruel) {
		return(false);
	}
	else {	
		
		kill_cheater();
							
		return(false);
	}
	
}

/**
* @author talion
* @desc Massakriert Cheater und "Hacker" ; )
*/
function kill_cheater () {
	
	global $session;
			
	clearnav();
	$session['output']='';
	
	$session['bufflist']['angrygods']=array(
	'name'=>'`^Die Götter sind wütend!',
	'rounds'=>10,
	'wearoff'=>'`^Es ist den Göttern langweilig geworden, dich zu quälen.',
	'minioncount'=>$session['user']['level'],
	'maxgoodguydamage'=> 2,
	'effectmsg'=>'`7Die Götter verfluchen dich und machen dir `^{damage}`7 Schaden!',
	'effectnodmgmsg'=>'`7Die Götter haben beschlossen, dich erstmal nicht zu quälen.',
	'activate'=>'roundstart',
	'survivenewday'=>1,
	'newdaymessage'=>'`6Die Götter sind dir immer noch böse!'
	);
	output('Für den Versuch, die Götter zu betrügen, wurdest du niedergeschmettert!`n`n');
	output('`$Ramius, der Gott der Toten`) erscheint dir in einer Vision. Dafür, dass du versucht hast, deinen Geist mit seinem zu messen, sagt er dir wortlos, dass du keinen Gefallen mehr bei ihm hast.`n`n');
	addnews('`&Für den Versuch, die Götter zu besudeln, wurde '.$session['user']['name'].' zu Tode gequält! (Hackversuch gescheitert).');
	$session['user']['hitpoints']=0;
	$session['user']['alive']=0;
	$session['user']['soulpoints']=0;
	$session['user']['gravefights']=0;
	$session['user']['deathpower']=0;
	$session['user']['experience']*=0.75;
	
	saveuser();
	
	systemlog(' - HACKVERSUCH -',$session['user']['acctid']);
	
	$sql = 'SELECT acctid FROM accounts WHERE superuser>0';
	$result = db_query($sql);
	while ($row = db_fetch_assoc($result)) {
		systemmail($row['acctid'],'`#'.$session['user']['login'].'`# bei Hackversuch ertappt','Böse(r), böse(r), böse(r) '.$session['user']['name'].', du bist ein Hacker!');
	}
	exit();
						
	return(false);

}

/**
* @desc return a given parameter which has been checked and altered in order not
* to be dangerous for SQL Queries
* @param string the parameter
* @param bool remove html tags
* @param bool remove sql commands
* @param bool remove html special chars
* @return returns the corrected parameter or false if the parameter was empty, else true
*/
function mixed_check_parameter($str_parameter, $bool_remove_tags = true, $bool_remove_sql = true,
$bool_no_html_special_chars = true)
{
	if($str_parameter == null)
	{
		return false;
	}
	if($str_parameter == '')
	{
		return true;
	}
	
	if (get_magic_quotes_gpc())
	{
		$str_parameter = stripslashes($str_parameter);
	}
	
	$str_parameter = mysql_real_escape_string($str_parameter);
	if($bool_remove_tags == true)
	{
		$str_parameter = strip_tags($str_parameter);
	}
	if($bool_no_html_special_chars == true)
	{
		$str_parameter = htmlentities($str_parameter);
	}
	//Not fully functional right now, dos not do anything
	//Is planned to remove SQL statements by a regular expression
	if($bool_remove_sql == true)
	{
		$str_regex = '#((select.*from.*(where)?.*)|(insert.*into.*values.*)|'.
		'(delete.*from.*|create.*(table|database)))#iu';

		//Remove what was defined in the regular expression above
		$str_parameter = preg_replace($str_regex, '',$str_parameter);
	}
	//Return the cleaned parameter
	return $str_parameter;
}

// Konstantendefs für check_blacklist
define('BLACKLIST_LOGIN',1);
define('BLACKLIST_TITLE',2);
define('BLACKLIST_EMAIL',4);

/**
* @desc Prüft, ob ein bestimmter Wert in der Blacklist vorhanden ist
* @param int Blacklist-Typ; Angabe mit obigen Flags und bitweiser ODER-Verknüpfung 
* @param string Wert, auf den geprüft werden soll
* @return TRUE, wenn Eintrag auf Blacklist besteht, sonst FALSE
* @author talion
*/
function check_blacklist ($int_type, $str_val)
{
	
	$str_where = '';
	
	// Accents ersetzen
	$arr_srch = array( 'è','é','ê','à','á','â','ì','í','î','ò','ó','ô','ù','ú','û' );
	$arr_repl = array( 'e','e','e','a','a','a','i','i','i','o','o','o','u','u','u' );
	$str_val = str_replace($arr_srch, $arr_repl, $str_val);
	
	// Wortbestandteile ermitteln
	$arr_words = preg_split('/[_\W]|([A-Z](?:[a-z]+))/',$str_val,-1,PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
	
	if(sizeof($arr_words) > 1) {
		foreach($arr_words as $w) {
			$str_where .= ' OR (LOWER(value) = "'.addslashes(strtolower($w)).'") ';
		}
	}
		
	$sql = 'SELECT id FROM blacklist WHERE type & '.$int_type.' 
			AND (
					(LOWER(value)="'.addslashes(strtolower($str_val)).'") 
					'.$str_where.'
				)
				 LIMIT 1';
	$res = db_query($sql);
	
	if(db_num_rows($res)) {
		
		return(true);
		
	}	
	
	return(false);
	
}

/**
* @desc Prüft, ob ein Ban auf Account / PC gesetzt ist und Account diesen nicht übergeht.
*		Dazu können entweder die zu checkenden Daten direkt übergeben werden 
*		Oder, falls keine davon gegeben ist: Wenn Acctid gegeben, verwendet Func
*		den damit adressierten Datensatz. Sonst: Infos aus Session / Server-Vars
* @param string Login-Filter
* @param string IP-Filter
* @param string ID-Filter
* @param string EMail-Filter
* @param int AccountID (Optional; Standard 0)
* @param bool Auf Index-Seite mit Fehlermeldung weiterleiten (Optional, Standard true)
* @return TRUE, wenn Ban besteht, sonst FALSE
* @author talion using LOGD-CORE code
*/
function checkban ($str_login = false, $str_ip = false, $str_id = false, $str_mail = false, $int_acctid = 0, $bool_errormsg = true)
{
	global $session,$SCRIPT_NAME;
	
	$bool_banoverride = false;
	
	// Daten zusammenstellen
	// Wenn Account gegeben
	if($int_acctid > 0) {
		$sql = 'SELECT 
						'.($str_ip===false ? 'lastip AS str_ip,':'').'
						'.($str_id===false ? 'uniqueid AS str_id,':'').'
						'.($str_mail===false ? 'emailaddress AS str_mail,':'').'
						'.($str_login===false ? 'login AS str_login,':'').'
						banoverride AS bool_banoverride 
				 FROM accounts WHERE acctid='.$int_acctid;
		$res = db_query($sql);
		
		$arr_data = db_fetch_assoc($res);
		db_free_result($result);
		extract($arr_data);
	}
	// Wenn keine Daten unmittelbar übergeben, session-Daten übernehmen
	if($str_login === false && $str_ip === false && $str_id === false && $str_mail === false) {
		$str_login = (!empty($session['user']['login']) ? $session['user']['login'] : false);
		$str_ip = (!empty($session['user']['lastip']) ? $session['user']['lastip'] : $_SERVER['REMOTE_ADDR']);
		$str_id = (!empty($session['user']['uniqueid']) ? $session['user']['uniqueid'] : $_COOKIE['lgi']);
		$str_mail = (!empty($session['user']['emailaddress']) ? $session['user']['emailaddress'] : false);
		
		$bool_banoverride = $session['banoverride'];
	}
	
	if ($bool_banoverride) {
		return false;
	}
			
	// Wenn effektiv keine zu prüfende Bedingung vorhanden
	if(empty($str_ip) && empty($str_id) && empty($str_mail) && empty($str_login)) {
		return(false);
	}
	
	// Auf Ban prüfen	
	$sql = 'SELECT * FROM bans WHERE  
			(	 0 
				'.($str_ip !== false ? 'OR ("'.$str_ip.'"=ipfilter AND ipfilter<>"") ' : '').'
				'.($str_id !== false ? 'OR (uniqueid="'.$str_id.'" AND uniqueid<>"") ' : '').'
				'.($str_mail !== false ? 'OR (mailfilter="'.$str_mail.'" AND mailfilter != "") ' : '').'
				'.($str_login !== false ? 'OR (LOWER(loginfilter)="'.strtolower($str_login).'" AND loginfilter != "") ' : '').' 
			) 
			AND (banexpire="0000-00-00" OR banexpire>"'.date('Y-m-d').'")
			LIMIT 1';
	$result = db_query($sql) or die(db_error(LINK));
	
	if (db_num_rows($result)>0)
	{
	
		if($bool_errormsg) {	
			
			$row = db_fetch_assoc($result);
			
			$sql = 'UPDATE bans SET last_try = NOW() WHERE id='.$row['id'];
			db_query($sql);
					
			$session=array();
			$session['message'].='`n`4Du bist einer Verbannung zum Opfer gefallen:`n';
			
			$session['user']['lastip'] = $_SERVER['REMOTE_ADDR'];
			$session['user']['uniqueid'] = $id;
						
			$session['message'].=$row['banreason'];
			if ($row['banexpire']=='0000-00-00') {
				$session['message'].='`n  `$Die Verbannung ist permanent!`0';
			}
			if ($row['banexpire']!='0000-00-00') {
				$session['message'].='`n  `^Der Bann wird am '.strftime('%e. %B %Y',strtotime($row['banexpire'])).' aufgehoben `0';
			}
			$session['message'].='`n';
			
			$session['message'].='`n`4Wenn dir die Gründe unklar sind, kannst du mit einer Anfrage in einem höflichen Ton nach dem Grund fragen, aber gib deinen Charakternamen und eine Emailadresse an, sonst können wir keine Auskunft geben.';
	
			if($SCRIPT_NAME != 'index.php' && $SCRIPT_NAME != 'petition.php') 
			{
				header('Location: index.php');
				exit();
			}
		}
		
		db_free_result($result);
		
		return(true);
	}
	
	return(false);
}

/**
* @author talion
* @desc Trägt einen Ban in die Datenbank ein und loggt davon betroffene User automatisch aus.
* @param int Accountid des Accounts, dessen Daten gebannt werden sollen. Überschreibt evtl.
	andere Parameter. Falls 0, müssen die anderen Parameter gegeben sein.
* @param string Bangrund.
* @param date Ablaufzeit.
* @param mixed Zu bannende IP (String). Wenn auf false, wird dieser Wert beim Ban nicht verwendet.
* @param mixed Zu bannende ID (String). Wenn auf false, wird dieser Wert beim Ban nicht verwendet.
* @param mixed Zu bannende Mailadresse (String). Wenn auf false, wird dieser Wert beim Ban nicht verwendet.
* @param mixed Zu bannender Login (String). Wenn auf false, wird dieser Wert beim Ban nicht verwendet.
* @return array Liste mit AccountIDs, die vom Ban betroffen sind. Bei Fehler: bool false. Setzt zusätzlich
*			session['error'] auf Grund für Abbruch.				
*/
function setban($int_acctid,$str_reason,$date_expire,$str_ip=false,$str_id=false,$str_mail=false,$str_login=false)
{
	global $session;
	
	$int_acctid = (int)$int_acctid;
	$arr_data = array();
	$arr_users = array();
	$str_ids = '';
	$str_where = '';
	
	// Ist Ban zeitlich überhaupt konsistent?
	if(strtotime($date_expire) <= time() && $date_expire != '0000-00-00') {
		
		$session['error'] = 'setban_expire_invalid';
		return(false);
		
	}
	
	// einzutragende Daten ermitteln
	if($int_acctid > 0) {
		
		$sql = 'SELECT 
						'.($str_ip!==false ? 'lastip AS str_ip,':'').'
						'.($str_id!==false ? 'uniqueid AS str_id,':'').'
						'.($str_mail!==false ? 'emailaddress AS str_mail,':'').'
						'.($str_login!==false ? 'login AS str_login,':'').'
						acctid AS int_acctid 
				 FROM accounts WHERE acctid='.$int_acctid;
		$res = db_query($sql);
		
		// Gegebener Account existiert nicht
		if(!db_num_rows($res)) {
			
			$session['error'] = 'setban_account_notfound';
			return(false);
			
		}
		
		$arr_data = db_fetch_assoc($res);
		extract($arr_data);
		
	}
	
	if(empty($str_ip) && empty($str_id) && empty($str_mail) && empty($str_login)) {
		$session['error'] = 'setban_noconditions';
		return(false);
	}
				
	// User ermitteln, die der Ban betreffen könnte
	$sql = 'SELECT a.acctid,a.name,incommunity FROM accounts a LEFT JOIN account_extra_info USING(acctid) WHERE 
				'.(!empty($str_ip) ? 'lastip = "'.addslashes($str_ip).'" OR ':'').'
				'.(!empty($str_id) ? 'uniqueid = "'.addslashes($str_id).'" OR ':'').'
				'.(!empty($str_mail) ? 'emailaddress = "'.addslashes($str_mail).'" OR ':'').'
				'.(!empty($str_login) ? 'login = "'.addslashes($str_login).'" OR ':'').'
				0';
	$res = db_query($sql);
	
	while($a = db_fetch_assoc($res)) {
		$arr_users[$a['acctid']] = $a;
		$str_ids .= ','.$a['acctid'];
		
		if( $a['incommunity'] > 0 ){ 
			require_once(LIB_PATH.'communityinterface.lib.php');
			ci_banuser($a['incommunity'], true);
		}
	}	
	
	if(strlen($str_ids) > 1) {
	
		// betroffene User ausloggen
		$sql = 'UPDATE accounts SET loggedin=0 WHERE acctid IN ( -1'.$str_ids.' )';
		if( !db_query($sql) ) {
			
			$session['error'] = 'setban_account_logout_failed';
			return(false);
			
		}
		if( db_error(LINK) ) {
			
			$session['error'] = 'setban_account_logout_failed';
			return(false);
			
		}
		
	}
		
	$sql = 'INSERT INTO bans SET '
				.'banreason="'.$str_reason.'",'
				.'banexpire="'.$date_expire.'",'
				.'ipfilter="'.addslashes($str_ip).'",'
				.'uniqueid="'.addslashes($str_id).'",'
				.'loginfilter="'.addslashes(strtolower($str_login)).'",'
				.'mailfilter="'.addslashes($str_mail).'"';
	if( !db_query($sql) || db_error(LINK) ) {
		$session['error'] = 'setban_insert_failed';
		return(false);
	}		
			
	return($arr_users);
	
}

/**
* @author talion
* @desc Entfernt einen Ban aus der Datenbank.
* @param int BanID des Bans, der entfernt werden soll.
* @return array Liste mit AccountIDs, die von Entfernung des Bans betroffen sind. Bei Fehler: bool false. Setzt zusätzlich
*			session['error'] auf Grund für Abbruch.				
*/
function delban($int_banid)
{
	global $session;
	
	$int_banid = (int)$int_banid;
	$arr_users = array();
	
	if($int_banid == 0) {
		
		$session['error'] = 'delban_ban_notfound';
		return(false);
		
	}
			
	// Ban abrufen
	$sql = 'SELECT ipfilter AS str_ip, uniqueid AS str_id, mailfilter AS str_mail, loginfilter AS str_login FROM bans WHERE id='.$int_banid;
	$res = db_query($sql);
	
	// Gegebener Ban existiert nicht
	if(!db_num_rows($res)) {
		
		$session['error'] = 'delban_ban_notfound';
		return(false);
		
	}
		
	$arr_data = db_fetch_assoc($res);
	extract($arr_data);
				
	// User ermitteln, die der Ban betreffen könnte
	$sql = 'SELECT a.acctid,incommunity FROM accounts a LEFT JOIN account_extra_info USING(acctid) WHERE 
				'.(!empty($str_ip) ? 'lastip = "'.addslashes($str_ip).'" OR ':'').'
				'.(!empty($str_id) ? 'uniqueid = "'.addslashes($str_id).'" OR ':'').'
				'.(!empty($str_mail) ? 'emailaddress = "'.addslashes($str_mail).'" OR ':'').'
				'.(!empty($str_login) ? 'login = "'.addslashes($str_login).'" OR ':'').'
				0';
	$res = db_query($sql);
	
	while($a = db_fetch_assoc($res)) {
		$arr_users[$a['acctid']] = $a['acctid'];
		
		if( $a['incommunity'] > 0 ){ 
			require_once(LIB_PATH.'communityinterface.lib.php');
			ci_banuser($a['incommunity'], false);
		}

	}	
			
	$sql = 'DELETE FROM bans WHERE id='.$int_banid;
	if( !db_query($sql) || db_error(LINK) ) {
		$session['error'] = 'delban_delete_failed';
		return(false);
	}		
			
	return($arr_users);
	
}


/**
*@desc 	Schreibt Eintrag ins Debuglog
*@param string Nachricht
*@param int Acctid des Ziels (Optional, Standard 0 = Kein Ziel)			
*@param bool Wenn true, wird aktuelle IP und ID des Accounts mitgeloggt (Optional, Standard false)
*@author LOGD-Core
*/
function debuglog($message,$target=0,$bool_log_all=false)
{
	global $session;
	
	$message = stripslashes($message);
	
	$sql = 'INSERT INTO debuglog 
						(date,actor,target,message,ip,uid)
			VALUES 		(NOW(),'.$session['user']['acctid'].','.$target.',"'.addslashes($message).'"';
	if($bool_log_all) {
		$sql .= ',"'.addslashes($session['user']['lastip']).'","'.addslashes($session['user']['uniqueid']).'"';
	}
	else {
		$sql .= ',"",""';
	}	
	$sql .= ')';
	db_query($sql);
}

/**
*@desc 	Schreibt Eintrag ins Systemlog
*@param string Nachricht
*@param int Acctid des Urhebers (Optional, Standard 0 = System)			
*@param int Acctid des Ziels (Optional, Standard 0 = Kein Ziel)			
*@author talion
*/
function systemlog($message,$actor=0,$target=0)
{

	$sql = 'INSERT INTO syslog
						(date,actor,target,message)
			VALUES 		(NOW(),'.$actor.','.$target.',"'.addslashes($message).'")';
	db_query($sql);

}

/**
*@desc Erzeugt aus LOGD-Adresse einen Link ohne für Rückkehrfunktionen störende Params
*@param string Zu bearbeitender Link (Optional; Wenn nicht gegeben: Aktuelle Seite)
*@author Original from the LOTGD.NET/MightyE, modded by Dasher for the Guilds/Clans Code, modded by talion for Drachenserver
*/
function calcreturnpath($ret='')
{
	//
	//  Work out the return url
	//  Allows functions to be called from different source URL's and promotes reuse
	//  Original from the LOTGD.NET/MightyE, modded by Dasher for the Guilds/Clans Code
	//

    $return = ($ret!='') ? $ret : $_SERVER['REQUEST_URI'];
    $return = preg_replace("'([?&]c=[[:digit:]-]*)|([?&]vital=[[:digit:]-]*)'",'',$return);
	$pos = strrpos($return,'/');
    if($pos !== false) {$return = substr($return,$pos+1);}

	return($return);
} 


function register_global(&$var)
{
	@reset($var);
	while (list($key,$val)=@each($var))
	{
		global $$key;
		$$key = $val;
	}
	@reset($var);
}

// anti cheat module for custom methods to detect players who are cheating
// 
// function ac_check returns true, if the user seems to be trying to cheat
// and false if everything seems fine

function ac_check($row)
{
	global $session;
	
	if($session['user']['superuser'] >= DEBUGMODE) 
	{
		return(false);
	}
	
	if (isset($row['acctid'])) 
	{
		if (!isset($row['uniqueid'])) 
		{
			$sql = 'SELECT uniqueid FROM accounts WHERE acctid = '.$row['acctid'];
			$result = db_query($sql);
			if (db_num_rows($result)>0) 
			{
				$row = db_fetch_assoc($result);
			} 
			else 
			{
				return false;
			}
		}
		if ($session['user']['uniqueid'] == $row['uniqueid']) 
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	else 
	{
		return false;
	}
}
?>
