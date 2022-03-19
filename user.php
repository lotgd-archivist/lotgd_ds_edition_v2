<?php
/**
* user.php: Zentrales Werkzeug für Superuser, um Spieleraccounts zu bearbeiten und zu verwalten
* @author Standardrelease by MightyE / Anpera, überarbeitet by talion <t@ssilo.de>
* @version DS-E V/2
*/

require_once "common.php";
require_once(LIB_PATH.'dg_funcs.lib.php');

su_check(SU_RIGHT_EDITORUSER,true);

function editnav () {
	global $row;
	
	addnav('Kontrolle');
	addnav('Verbannen','su_bans.php?op=edit_ban&ids[]='.$row['acctid']);
	addnav("Letzten Treffer anzeigen","user.php?op=lasthit&userid={$_GET['userid']}",false,true);
	if(su_check(SU_RIGHT_DEBUGLOG)) {
		addnav("Debug-Log anzeigen","su_logs.php?op=search&type=debuglog&account_id={$_GET['userid']}");
	}
	if(su_check(SU_RIGHT_MAILBOX)) {
		addnav("Mailbox zeigen","su_mails.php?op=search&to_id={$_GET['userid']}");
	}
	if(su_check(SU_RIGHT_COMMENT)) {
		addnav("Kommentare","su_comment.php?op=user&uid={$_GET['userid']}",false,true);
	}
	if(su_check(SU_RIGHT_EDITORITEMS)) {
		addnav("Inventar","itemsu.php?what=items&acctid={$_GET['userid']}");
	}
	if ($row['house'] && su_check(SU_RIGHT_EDITORHOUSES) ){
		addnav("Zum Hausmeister","suhouses.php?op=drin&id=".$row['house']);
	}
	addnav('Knappeneditor','user.php?op=disciple&userid='.$_GET['userid']);	
	if ($_GET['returnpetition']!=""){
		addnav("Zurück zur Anfrage","su_petitions.php?op=view&id={$_GET['returnpetition']}");
	}
		
}

// FORMULAR-ARRAY erstellen

if(su_check(SU_RIGHT_RIGHTS)) {		
	$str_grps = stripslashes(getsetting('sugroups',''));
	$arr_grps = unserialize($str_grps);
	
	$sugroups = ',0,Keine';
	if(is_array($arr_grps)) {
		foreach($arr_grps as $lvl=>$grp) {
			
			$sugroups .= ','.$lvl.','.$grp[0].'/'.$grp[1];
			
		}
	}
	
	$ugrp = array();
	
	$surights = array('Superuser-Rechte,title');
	foreach($ARR_SURIGHTS as $r=>$v) {
		
		$surights['surights['.$r.']'] = $v['desc'].',enum,-1,Gruppeneinstellung,0,Nein,1,Ja'.($ugrp[0] ? '|?Gruppe '.$ugrp[0].': '.$ugrp[2][$r] : '');
		
	}
}

$mounts=",0,Keins";
$sql = "SELECT mountid,mountname,mountcategory FROM mounts ORDER BY mountcategory";
$result = db_query($sql);
while ($row = db_fetch_assoc($result)){
	$mounts.=",{$row['mountid']},{$row['mountcategory']}: {$row['mountname']}";
}

$professions = ',0,Keiner';

foreach($profs as $k=>$p) {
	
	$professions .= ','.$k.','.$p[0].'/'.$p[1];
	
}

$guildfuncs = '';

foreach($dg_funcs as $k=>$f) {
	
	$guildfuncs .= ','.$k.','.$f[0].'/'.$f[1];
	
}
				
$userinfo = array(
	"Account Info,title",
	"acctid"=>"User ID,viewonly|?Die Accountid, unter der der Account in der DB gespeichert ist.",
	"login"=>"Login|?Loginname des Accounts.",
	"newpassword"=>"Neues Passwort",
	"emailaddress"=>"Email Adresse",
	"locked"=>"Account gesperrt,bool",
	"banoverride"=>"Verbannungen übergehen,bool",
	"superuser"=>"Superuser,".(su_check(SU_RIGHT_RIGHTS) ? "enum".$sugroups : "viewonly"),
	
	"User Infos,title",
	"name"=>"Display Name",
	"title"=>"Titel (muss auch in Display Name)",
	"sex"=>"Geschlecht,enum,0,Männlich,1,Weiblich",
// we can't change this this way or their stats will be wrong.
//	"race"=>"Race,enum,0,Unknown,1,Troll,2,Elf,3,Human,4,Dwarf,5,Echse",
	"age"=>"Tage seit Level 1,int",
	"dragonkills"=>"Drachenkills,int",
	"dragonage"=>"Alter beim letzten Drachenkill,int",
//	"bio"=>"Bio",
	"marks"=>"Male",
	"profession"=>"Beruf,enum".$professions,
	"guildid"=>"GildenID,int",
	"guildrank"=>"Gildenrang (1-".count($dg_default_ranks)."),int",
	"guildfunc"=>"Funktion in der Gilde,enum".$guildfuncs,
	
	"Werte,title",
	"level"=>"Level,int",
	"experience"=>"Erfahrung,int",
	"hitpoints"=>"Lebenspunkte (aktuell),int",
	"maxhitpoints"=>"Maximale Lebenspunkte,int",
	"turns"=>"Runden übrig,int",
	"castleturns"=>"Schlossrunden übrig,int",
	"playerfights"=>"Spielerkämpfe übrig,int",
	"battlepoints"=>"Arenapunkte,int",
	"attack"=>"Angriffswert (inkl. Waffenschaden),int",
	"defence"=>"Verteidigung (inkl. Rüstung),int",
	"spirits"=>"Stimmung (nur Anzeige),enum,".RP_RESURRECTION.",RP-Wiedererweckung,-2,Sehr schlecht,-1,Schlecht,0,Normal,1,Gut,2,Sehr gut",
	"resurrections"=>"Auferstehungen,int",
	"alive"=>"Lebendig,int",
	"reputation"=>"Ansehen (-50 - +50),int",
	"imprisoned"=>"Haftstrafe in Tagen,int",
	
	"Grabkämpfe,title",
	"deathpower"=>"Gefallen bei Ramius,int",
	"gravefights"=>"Grabkämpfe übrig,int",
	"soulpoints"=>"Seelenpunkte (HP im Tod),int",

	
	"Ausstattung,title",
	"gems"=>"Edelsteine,int",
	"gold"=>"Bargold,int",
	"goldinbank"=>"Gold auf der Bank,int",
	"weapon"=>"Name der Waffe",
	"weapondmg"=>"Waffenschaden,int",
	"weaponvalue"=>"Kaufwert der Waffe,int",
	"armor"=>"Name der Rüstung",
	"armordef"=>"Verteidigungswert,int",
	"armorvalue"=>"Kaufwert der Rüstung,int",
	
	"Sonderinfos,title",
	"house"=>"Haus-ID,int",
	"housekey"=>"Hausschlüssel?,int",
	"marriedto"=>"Partner-ID (4294967295 = Violet/Seth),int",
	"charisma"=>"Flirts (4294967295 = verheiratet mit Partner),int",
	"seenlover"=>"Geflirtet,bool",
	"charm"=>"Charme,int",
	"rename_weapons"=>"Darf Waffen umbenennen,bool",
	"seendragon"=>"Drachen heute gesucht,bool",
	"seenmaster"=>"Meister befragt,bool",
	"fedmount"=>"Tier gefüttert,bool",
	"hashorse"=>"Tier,enum$mounts",
	"drunkenness"=>"Betrunken (0-100),int",
	"pvpflag"=>"Pvp-Schutz (5013-10-06 00:42:00 = an)",
	"balance_forest"=>"Waldbalance|?-10 / +20, > 0 verstärkt Werte der Waldmonster, < 0 verringert sie.",
	"balance_dragon"=>"Drachenbalance|?-10 / +20, > 0 verstärkt Werte des Drachen, < 0 verringert sie.",
	
	"Weitere Infos,title",
	"beta"=>"Nimmt am Betatest teil,viewonly",
	"laston"=>"Zuletzt Online,viewonly",
	"lasthit"=>"Letzter neuer Tag,viewonly",
	"lastmotd"=>"Datum der letzten MOTD,viewonly",
	"lastip"=>"Letzte IP,viewonly",
	"uniqueid"=>"Unique ID,viewonly",
	"gentime"=>"Summe der Seitenerzeugungszeiten,viewonly",
	"gentimecount"=>"Seitentreffer,viewonly",
	"allowednavs"=>"Zulässige Navigation,viewonly",
	"dragonpoints"=>"Eingesetzte Drachenpunkte,viewonly",
	"bufflist"=>"Spruchliste,viewonly",
	"prefs"=>"Einstellungen,viewonly",
	"lastwebvote"=>"Zuletzt bei Top Wep Games gewählt,viewonly",
	"donationconfig"=>"Spendenkäufe,viewonly"
	);
	
$extrainfo = array(
	"EXTRA - INFOS,title",
	"ctitle"=>"Eigener Titel (muss auch in Display Name)",
	"cname"=>"Eigener (farbiger) Name (muss auch in Display Name)",
	"avatar"=>"Avatar:",
	"namecheckday"=>"Namensprüfungsalter",
	"namecheck"=>"Name geprüft von (acctid); 16777215=ok",
	"birthday"=>"Geburtsdatum (Format: YYYY-MM-DD)",
	"sentence"=>"Zu x Tagen Haft verurteilt,int",
	"daysinjail"=>"Tage im Kerker,int",
	"seenbard"=>"Barden gehört,bool",
	"usedouthouse"=>"Plumpsklo besucht,bool",
	"treepick"=>"Baum des Lebens besucht,bool",
	"minnows"=>"Fliegen im Beutel,int",
	"worms"=>"Würmer im Beutel,int",
	"fishturn"=>"Angelrunden,int",
	"gotfreeale"=>"Frei-Ale (MSB: getrunken - LSB: spendiert),int",
	"cage_action"=>"Käfigkämpfe heute angezettelt,int",
	"boughtroomtoday"=>"Zimmer für heute bezahlt,bool",
	"bio"=>"Bio",
	"long_bio"=>"Verlängerte Bio,textarea,30,15",
	"temple_servant"=>"Tempeldienertage(x20=heute geleistet)",
	"Ruhmeshalleneinträge,title",
	"daysinjail"=>"Verbrachte Tage im Kerker,int",
	"bestdragonage"=>"Jüngstes Alter bei einem Drachenkill,int",
	"beerspent"=>"Anzahl spendierter Ales,int",
	"disciples_spoiled"=>"Anzahl verheizter Knappen,int",
	"timesbeaten"=>"Verpügelt worden,int",
	"runaway"=>"Aus dem Kampf geflohen,int",
	"Gildeninfos,title",
	"guildtransferred_gold"=>"Gildentransfer (gold),int",
	"guildtransferred_gems"=>"Gildentransfer (gems),int",
	"guildfights"=>"Gildenkämpfe heute,int",
	"Freischaltungen für Donation Points,title",
	"has_long_bio"=>"Verlängerte Bio gekauft (1=ja 0=nein),int",
	"hasxmount"=>"Tier getauft (1=ja 0=nein),int",
	"trophyhunter"=>"Präparierset gekauft (1=ja 0=nein),int",
	"Sonstiges,title",
	"xmountname"=>"Name des Tieres",
	"goldin"=>"Goldeingang heute,int",
	"goldout"=>"Goldausgang heute,int",
	"gemsin"=>"Gemeingang heute,int",
	"gemsout"=>"Gemausgang heute,int",
	"incommunity"=>"Community ID (0=nicht eingetragen),int"
);

// END Formular-Array

page_header("User Editor");
output("<form action='user.php?op=search' method='POST'>Suche in allen Feldern: <input name='q' id='q'><input type='submit' class='button'></form>",true);
output("<script language='JavaScript'>document.getElementById('q').focus();</script>",true);
addnav("","user.php?op=search");
addnav("G?Zurück zur Grotte","superuser.php");


addnav('W?Zurück zum Weltlichen',$session['su_return']);
if (su_check(SU_RIGHT_EDITORUSER))
{
	
	addnav('Mechanik');
	addnav("Account-Tabellen abgleichen","user.php?op=extratable");
	//addnav("bestdragonage kopieren","user.php?op=copydata");
	addnav("Überflüssige Tabellen löschen","user.php?op=delextra");
	//addnav("Benutzereditor","user.php");
	addnav('Seiten');
	$sql = "SELECT count(acctid) AS count FROM accounts";
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	$page=0;
	while ($row[count]>0){
		$page++;
		addnav("$page Seite $page","user.php?page=".($page-1)."&sort=$_GET[sort]");
		$row[count]-=100;
	}
}

$str_op = ($_REQUEST['op'] ? $_REQUEST['op'] : '');


switch($str_op) {
		
	case 'search':
		
		$sql = "SELECT acctid FROM accounts WHERE ";
		$where="
		login LIKE '%{$_POST['q']}%' OR 
		acctid LIKE '%{$_POST['q']}%' OR 
		name LIKE '%{$_POST['q']}%' OR 
		emailaddress LIKE '%{$_POST['q']}%' OR 
		lastip LIKE '%{$_POST['q']}%' OR 
		uniqueid LIKE '%{$_POST['q']}%' OR 
		gentimecount LIKE '%{$_POST['q']}%' OR 
		level LIKE '%{$_POST['q']}%'";
		$result = db_query($sql.$where);
		if (db_num_rows($result)<=0){
			output("`\$Keine Ergebnisse gefunden`0");

			$where="";
		}elseif (db_num_rows($result)>100){
			output("`\$Zu viele Ergebnisse gefunden. Bitte Suche einengen.`0");

			$where="";
		}elseif (db_num_rows($result)==1){
			//$row = db_fetch_assoc($result);
			//redirect("user.php?op=edit&userid=$row[acctid]");

			$_GET['page']=0;
		}else{
			$_GET['page']=0;
		}	
		
	break;	// END search
		
	case 'lasthit':
		
		$output="";
		$sql = "SELECT output FROM accounts WHERE acctid='{$_GET['userid']}'";
		$result = db_query($sql);
		$row = db_fetch_assoc($result);
		echo str_replace("<iframe src=","<iframe Xsrc=",$row['output']);
		exit();
	
	break; // END lasthit
		
	case 'logout_all':
		
		if($_GET['act'] == 'ok') {
	
			$sql = "UPDATE accounts SET loggedin=0 WHERE superuser=0 AND loggedin=1";
			db_query($sql);
			output(db_affected_rows().' Spieler erfolgreich ausgeloggt!');
			
		}
		else {
			
			$sql = "SELECT COUNT(*) AS a FROM accounts WHERE loggedin=1 AND superuser=0";
			$count = mysql_fetch_row(db_query($sql));
			
			output($count[0].' Spieler wirklich ausloggen?`n`n'.create_lnk('Ab ins Körbchen!','user.php?op=logout_all&act=ok'),true);
			
		}
		
	break;	// END logout all
		
	case 'edit':
		
		$userinfo = array_merge($userinfo,$extrainfo,$surights);
				
		$result = db_query("SELECT * FROM accounts WHERE acctid='$_GET[userid]'") or die(db_error(LINK));
		$row = db_fetch_assoc($result) or die(db_error(LINK));
		
		debuglog("`&Benutzer ".$row['name']."`& im Usereditor geöffnet.");
		
		$result2 = db_query("SELECT * FROM account_extra_info WHERE acctid='$_GET[userid]'") or die(db_error(LINK));
		$row2 = db_fetch_assoc($result2) or die(db_error(LINK));
		
		$row['surights'] = unserialize(stripslashes($row['surights']));
	
				
		foreach($ARR_SURIGHTS as $r=>$v) {
			
			if(isset($row['surights'][$r])) {
				$row['surights['.$r.']'] = $row['surights'][$r];	
				unset($row['surights'][$r]);
			}
			else {
				$row['surights['.$r.']'] = -1;	
			}
			
		}
		
		$row2['long_bio'] = preg_replace('/\r\n|\r|\n/', '', $row2['long_bio']); // Zeilenumbrüche raus
		
		$row = array_merge($row,$row2);
				
		output("<form action='user.php?op=special&userid=$_GET[userid]".($_GET['returnpetition']!=""?"&returnpetition={$_GET['returnpetition']}":"")."' method='POST'>",true);
		addnav("","user.php?op=special&userid=$_GET[userid]".($_GET['returnpetition']!=""?"&returnpetition={$_GET['returnpetition']}":"")."");
		output("<input type='submit' class='button' name='newday' value='Neuen Tag gewähren'>",true);
		output("<input type='submit' class='button' name='fixnavs' value='Defekte Navs reparieren'>",true);
		output("<input type='submit' class='button' name='clearvalidation' value='E-Mail als gültig markieren'>",true);
		output("</form>",true);
			
		output("<form action='user.php?op=save&userid=$_GET[userid]".($_GET['returnpetition']!=""?"&returnpetition={$_GET['returnpetition']}":"")."' method='POST'>",true);
		addnav("","user.php?op=save&userid=$_GET[userid]".($_GET['returnpetition']!=""?"&returnpetition={$_GET['returnpetition']}":"")."");
		addnav("","user.php?op=edit&userid=$_GET[userid]".($_GET['returnpetition']!=""?"&returnpetition={$_GET['returnpetition']}":"")."");
		
		editnav();
		
		if( $row['incommunity'] == 0 ){
			addnav("ins Forum übertragen", "user.php?op=forum&userid={$_GET['userid']}&name=".urlencode($row['login'])."&pass=".urlencode($row['password'])."&mail=".urlencode($row['emailaddress']));
		}
		
		addnav("Usereditor");
		addnav("Specials Editor","user_special.php?op=edit&userid={$_GET['userid']}");

		output("<input type='submit' class='button' value='Speichern'>",true);
		showform($userinfo,$row);
		output("</form>",true);
		if($_GET['userid'] != $session['user']['acctid']) {
			output("<iframe src='user.php?op=lasthit&userid={$_GET['userid']}' width='100%' height='400'>Dein Browser muss iframes unterstützen, um die letzte Seite des Users anzeigen zu können. Benutze den Link im Menü stattdessen.</iframe>",true);
		}
		addnav("","user.php?op=lasthit&userid={$_GET['userid']}");
		
	break;	// END edit
		
	case 'special':
		
		if ($_POST[newday]!=""){
			$sql = "UPDATE accounts SET lasthit='".date("Y-m-d H:i:s",strtotime(date("r")."-".(86500/getsetting("daysperday",4))." seconds"))."' WHERE acctid='$_GET[userid]'";
		}elseif($_POST[fixnavs]!=""){
			$sql = "UPDATE accounts SET allowednavs='',output='',restorepage='' WHERE acctid=$_GET[userid]";
		}elseif($_POST[clearvalidation]!=""){
			$sql = "UPDATE accounts SET emailvalidation='' WHERE acctid='$_GET[userid]'";
		}
		db_query($sql);
		if ($_GET['returnpetition']==""){
			// redirect("user.php?".db_affected_rows());
			redirect("user.php?op=edit&userid=".$_GET['userid']);
		}else{
			redirect("su_petitions.php?op=view&id={$_GET['returnpetition']}");
		}
		
	break;	// END special
	
	// Knappeneditor
	case 'disciple':
		
		$int_uid = (int)$_GET['userid'];
		$int_did = (int)$_POST['id'];
		
		addnav('Zurück zum Useredit','user.php?op=edit&userid='.$int_uid);
		
		if(!empty($int_did)) {
			$sql = ($int_did == -1 ? 'INSERT INTO ' : 'UPDATE ');
			$sql .= ' disciples 
					SET name="'.$_POST['name'].'",state='.$_POST['state'].',oldstate='.$_POST['oldstate'].',level='.$_POST['level'].',best_one='.($_POST['best_one'] ? 1 : 0).',master='.$int_uid;
			$sql .= ($int_did > -1 ? ' WHERE id='.$int_did : '');
			db_query($sql);
			
			if(db_affected_rows()) {
				output('`@`b`cKnappe erfolgreich editiert!`c`b`0`n`n');
			}
			else {
				output('`$`b`cKnappe NICHT editiert!`c`b`0`n`n');
			}
		}
		
		$sql = 'SELECT * FROM disciples WHERE master='.$int_uid;
		$res = db_query($sql);
		
		if(db_num_rows($res) == 0) {
			$arr_data = array('id'=>-1);
		}
		else {		
			$arr_data = db_fetch_assoc($res);
		}
		
		$str_state_enum = ',0,tot/inaktiv';	
		for($i=1;$i<=20;$i++) {
			$str_state_enum .= ','.$i.','.get_disciple_stat($i);	
		}
			
		$arr_form = array(
							'id'=>',hidden',
							'name'=>'Name des Knappen:',
							'state'=>'Aktueller Status des Knappen:,enum'.$str_state_enum,
							'oldstate'=>'Status-Backup:,enum'.$str_state_enum,
							'level'=>'Level des Knappen:,enum_order,0,100',
							'best_one'=>'Bester Knappe im Lande:,checkbox,1'
							);
							
		$str_lnk = 'user.php?op=disciple&userid='.$int_uid;
		addnav('',$str_lnk);
		output('<form method="POST" action="'.$str_lnk.'">',true);							
							
		showform($arr_form,$arr_data,false,'Speichern');
		
		output('</form>',true);
		
		
	break;
		
	case 'save':
		
		$sql1 = "UPDATE accounts SET ";
		$sql2 = "UPDATE account_extra_info SET ";
				
		// Ein paar Sicherheiten für Änderungen
		// Gesamtname geändert
		/*if ($_POST['oldname']!=$_POST['name']) {
			$clearedname = preg_replace('/`./','',$_POST['name']);
			// Login bleibt gleich
			if (substr_count($clearedname,$_POST['login'])) {
				// Titel rausfinden
				$replace = '(`.)*';
				for ($i=0;$i<strlen($_POST['login']);$i++) {
					$replace .= $_POST['login']{$i}.'(`.)*';
				}
				$_POST['ctitle'] = rtrim(preg_replace('/'.$replace.'/','',$_POST['name']));
				if ($_POST['ctitle']=='') $_POST['title'] = '';
				elseif ($_POST['ctitle']==$_POST['title']) $_POST['ctitle'] = '';
			}
			// Neuer Login
			else {
				// Leerzeichen vorhanden
				if ($login = strrchr($_POST['name'],' ')) {
					$_POST['login'] = trim(strrchr($clearedname,' '));
					$_POST['ctitle'] = str_replace($login,'',$_POST['name']);
					if ($_POST['ctitle']==$_POST['title']) $_POST['ctitle'] = '';
				}
				// Kein Leerzeichen vorhanden
				else {
					$_POST['login'] = $clearedname;
					$_POST['title'] = $_POST['ctitle'] = '';
				}
			}
		}
		// Login geändert
		elseif ($_POST['oldlogin']!=$_POST['login']) {
			if ($_POST['ctitle']!='') $_POST['name'] = $_POST['ctitle'].' '.$_POST['login'];
			else $_POST['name'] = $_POST['title'].' '.$_POST['login'];
		}
		// Titel geändert
		elseif ($_POST['oldtitle']!=$_POST['title'] && $_POST['ctitle']=='') {
			if ($_POST['oldctitle']!='') $colname = str_replace($_POST['oldctitle'],'',$_POST['name']);
			else $colname = str_replace($_POST['oldtitle'],'',$_POST['name']);
			$_POST['name'] = $_POST['title'].$colname;
		}
		// Usertitel geändert
		elseif ($_POST['oldctitle']!=$_POST['ctitle']) {
			if ($_POST['oldctitle']!='') $colname = str_replace($_POST['oldctitle'],'',$_POST['name']);
			else $colname = str_replace($_POST['oldtitle'],'',$_POST['name']);
			if ($_POST['ctitle']=='') $_POST['name'] = $_POST['title'].$colname;
			else $_POST['name'] = $_POST['ctitle'].$colname;
		}*/
	
		reset($_POST);
		
		if(su_check(SU_RIGHT_RIGHTS)) {
			foreach($_POST['surights'] as $key=>$r) {
				if($r == -1) {
					unset($_POST['surights'][$key]);
				}
			}
			
			$_POST['surights'] = addslashes(serialize($_POST['surights']));
			$userinfo['surights'] = true;
		}
		
		while (list($key,$val)=each($_POST)){
			if (isset($userinfo[$key])){
				if ($key=="newpassword" ){
					if ($val>"") $sql1.="password = MD5(\"$val\"),";
				}
				else{
					$sql1.="$key = \"$val\",";
				}
			}
			elseif (isset($extrainfo[$key])){
				$sql2.="$key = \"$val\",";
			}

		}
		$sql1=substr($sql1,0,strlen($sql1)-1);
		$sql2=substr($sql2,0,strlen($sql2)-1);
		$sql1.=" WHERE acctid=\"$_GET[userid]\"";
		$sql2.=" WHERE acctid=\"$_GET[userid]\"";
						
		systemlog("Useredit - Editierte User ",$session['user']['acctid'],$_GET['userid']);
		
		//we must manually redirect so that our changes go in to effect *after* our user save.
		addnav("","su_petitions.php?op=view&id={$_GET['returnpetition']}");
		addnav("","user.php");
		saveuser();
		db_query($sql1) or die(db_error(LINK));
		db_query($sql2) or die(db_error(LINK));
		if ($_GET['returnpetition']!=""){
			header("Location: su_petitions.php?op=view&id={$_GET['returnpetition']}");
		}else{
			header("Location: user.php");
		}
	
		exit();
		
	break;	// END save
		
	case 'forum':
		
		$aUser = array();
		$aUser[ 0 ] = array(	'id'	=> $_GET['userid'], 
								'name'	=> urldecode($_GET['name']),
								'pass'	=> urldecode($_GET['pass']),
								'mail'	=> urldecode($_GET['mail'])
							); 
		include_once(LIB_PATH."communityinterface.lib.php");
		$ret = ci_importusers($aUser);
		if( !empty($ret) ){
			redirect("user.php?op=edit2&userid=".$_GET['userid']."&msg=ok");
		}
		else{
			redirect("user.php?op=edit2&userid=".$_GET['userid']."&msg=fail");
		}
		
	break;	// END forum
		
	case 'logoff':
		
		$id = $_GET['userid'];
		$sql = "UPDATE accounts set loggedin = 0, lasthit = 0 WHERE acctid = $id";
		
		addnav("User Info bearbeiten","user.php?op=edit&userid=$id");
		
		$result = db_query($sql);
		db_query($sql) or die(sql_error($sql));
		output("Der User wurde ausgelogged!");
		
	break;	// END logoff
		
	default:	// Standardanzeige
		
		
		
	break;	// END default

}	// END Main-Switch (op)

if (isset($_GET['page'])){
	$order = "acctid";
	if ($_GET[sort]!="") $order = "$_GET[sort]";
	$offset=(int)$_GET['page']*100;
	$sql = "SELECT acctid,login,name,level,laston,gentimecount,lastip,uniqueid,emailaddress,activated FROM accounts ".($where>""?"WHERE $where ":"")."ORDER BY \"$order\" LIMIT $offset,100";
	$result = db_query($sql) or die(db_error(LINK));
	output("<table>",true);
	output("<tr>
	<td>Ops</td>
	<td><a href='user.php?sort=login'>Login</a></td>
	<td><a href='user.php?sort=name'>Name</a></td>
	<td><a href='user.php?sort=acctid'>ID</a></td>
	<td><a href='user.php?sort=level'>Lev</a></td>
	<td><a href='user.php?sort=laston'>Zuletzt da</a></td>
	<td><a href='user.php?sort=gentimecount'>Treffer</a></td>
	<td><a href='user.php?sort=lastip'>IP</a></td>
	<td><a href='user.php?sort=uniqueid'>ID</a></td>
	<td><a href='user.php?sort=emailaddress'>E-Mail</a></td>
	</tr>",true);
	addnav("","user.php?sort=login");
	addnav("","user.php?sort=name");
	addnav("","user.php?sort=acctid");
	addnav("","user.php?sort=level");
	addnav("","user.php?sort=laston");
	addnav("","user.php?sort=gentimecount");
	addnav("","user.php?sort=lastip");
	addnav("","user.php?sort=uniqueid");
	$rn=0;
	for ($i=0;$i<db_num_rows($result);$i++){
		$row=db_fetch_assoc($result);
		$loggedin=user_get_online(0,$row,true);
		$laston=round((strtotime(date("r"))-strtotime($row[laston])) / 86400,0)." Tage";
		if (substr($laston,0,2)=="1 ") $laston="1 Tag";
		if (date("Y-m-d",strtotime($row[laston])) == date("Y-m-d")) $laston="Heute";
		if (date("Y-m-d",strtotime($row[laston])) == date("Y-m-d",strtotime(date("r")."-1 day"))) $laston="Gestern";
		if ($loggedin) $laston="Jetzt";
		$row[laston]=$laston;
		if ($row[$order]!=$oorder) $rn++;
		$oorder = $row[$order];
		output("<tr class='".($rn%2?"trlight":"trdark")."'>",true);
		
		output("<td>",true);
		
		//ADDED LOG OFF HERE
		output("[<a href='user.php?op=edit&userid=$row[acctid]'>Edit</a>|"
				.create_lnk('Del','su_delete.php?ids[]='.$row['acctid'].'&ret='.urlencode(calcreturnpath()) ).'|'.
				create_lnk('Ban','su_bans.php?op=edit_ban&ids[]='.$row['acctid'].'&ret='.urlencode(calcreturnpath()) ).'|'.
				create_lnk('Logs','su_logs.php?op=search&type=debuglog&account_id='.$row['acctid']).']'.
				"<a href='user.php?op=logoff&userid=$row[acctid]'>Log Off</a>|",true);
		addnav("","user.php?op=edit&userid=$row[acctid]");
		addnav("","user.php?op=setupban&userid=$row[acctid]");
		//ADDED LOG OFF HERE
		addnav("","user.php?op=logoff&userid=$row[acctid]");
		
		output("</td><td>",true);
		output($row['login']);
		output("</td><td>",true);
		output($row['name']);
		output("</td><td>",true);
		output($row['acctid']);
		output("</td><td>",true);
		output($row['level']);
		output("</td><td>",true);
		output($row['laston']);
		output("</td><td>",true);
		output($row['gentimecount']);
		output("</td><td>",true);
		output($row['lastip']);
		output("</td><td>",true);
		output($row['uniqueid']);
		output("</td><td>",true);
		output($row['emailaddress']);
		output("</td>",true);
		$gentimecount+=$row['gentimecount'];
		$gentime+=$row['gentime'];

		output("</tr>",true);
	}
	output("</table>",true);
	output("Treffer gesamt: $gentimecount`n");
	output("CPU-Zeit gesamt: ".round($gentime,3)."s`n");
	output("Durchschnittszeit für Seitenerzeugung: ".round($gentime/max($gentimecount,1),4)."s`n");
}

page_footer();
?>
