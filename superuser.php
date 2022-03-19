<?php
require_once 'common.php';
su_lvl_check(1,true,true);
addcommentary(false);

addnav('W?Zurück zum Weltlichen',$session['su_return']);
if ($_GET['op']=='newsdelete')
{
	$sql = "DELETE FROM news WHERE newsid='$_GET[newsid]'";
	db_query($sql);
	$return = $_GET['return'];
	$return = preg_replace("'[?&]c=[[:digit:]-]*'",'',$return);
	$return = substr($return,strrpos($return,'/')+1);
	redirect($return);
}
if ($_GET['op']=='newsdelete2')
{
	$sql = "DELETE FROM ddlnews WHERE newsid='$_GET[newsid]'";
	db_query($sql);
	$return = $_GET['return'];
	$return = preg_replace("'[?&]c=[[:digit:]-]*'",'',$return);
	$return = substr($return,strrpos($return,'/')+1);
	redirect($return);
}

if ($_GET['op']=='iwilldie')
{
	$session['user']['alive'] = ($session['user']['alive'] ? 0 : 1);
	$session['user']['hitpoints'] = ($session['user']['alive'] ? $session['user']['maxhitpoints'] : 0);
	redirect('shades.php');
}

if ($_GET['op']=='stealth')
{
	$session['user']['activated'] = ($session['user']['activated'] == USER_ACTIVATED_STEALTH ? 0 : USER_ACTIVATED_STEALTH);
	saveuser();
	redirect('superuser.php');
}
if ($_GET['op']=='dbrepair')
{
	$result = db_query('SHOW TABLES');
	$count = db_num_rows($result);
	$arr_table_list = array();
	for ($i=0;$i<$count;$i++)
	{
		list($key,$val)=each(db_fetch_assoc($result));
		$arr_table_list[] = '`'.$val.'`';
	}

	db_query('REPAIR TABLE '.implode(',',$arr_table_list));
}

if ($_GET['op']=='intro_pet')
{
	$session['su_return'] = ($_GET['su_return'] != 'superuser.php' && $_GET['su_return'] != 'su_petitions.php' ? urldecode($_GET['su_return']) : '');
	redirect('su_petitions.php');
}

if ($_GET['op']=='intro_grotte')
{
	$session['su_return'] = ($_GET['su_return'] != 'superuser.php' && $_GET['su_return'] != 'su_petitions.php' ? urldecode($_GET['su_return']) : '');
	redirect('superuser.php');
}


page_header('Admin Grotte');
if ($_GET['op']=='board')
{

	addnav('G?Zurück zur Grotte','superuser.php');

	require_once(LIB_PATH.'board.lib.php');

	board_view_form('Aufhängen','`&Deine `bgöttliche`b Nachricht:');
	if($_GET['board_action'] == "add") {
		board_add('su_grotte');
		redirect('superuser.php?op=board');
	}
	output('`n`n');
	board_view('su_grotte',2,'Folgende `bgöttliche`b Zettel hängen an der Grottenwand:','Keine `bgöttlichen`b Nachrichten vorhanden!',true,true,true);
}
else if ($_GET['op']=='discuss')
{

	addnav('G?Zurück zur Grotte','superuser.php');
	output('Zu folgenden Usern läuft gerade eine Diskussion :`n`n');

	$sql = "SELECT accounts.name, accounts.acctid, accounts.login FROM account_extra_info LEFT JOIN accounts ON accounts.acctid=account_extra_info.acctid WHERE discussion>0 ORDER BY accounts.name";
	$result = db_query($sql);
	$max=db_num_rows($result);

	for ($i=0;$i<$max;$i++){
		$row = db_fetch_assoc($result);
		output("`c`2-=-`@=-=`2-=-`@=-=`2-=-`@=-=`2-=-`0`c");
		output("`n<a href='discuss.php?who=".$row['acctid']."&char=".urlencode($row['login'])."&ret=".URLEncode($_SERVER['REQUEST_URI'])."'>$row[name]</a>`n");
		addnav("","discuss.php?who=".$row['acctid']."&char=".urlencode($row['login'])."&ret=".URLEncode($_SERVER['REQUEST_URI']));
	}


}
else if ($_GET['op']=='restore_rights')
{
		
	$arr_rights = array();
	
	end($ARR_SURIGHTS);
	$int_lastkey = (int)key($ARR_SURIGHTS);
	ksort($ARR_SURIGHTS);
		
	for($i=0; $i<=$int_lastkey; $i++) {
		if(!isset($ARR_SURIGHTS[$i])) {
			$arr_rights[$i] = 0;
		}
		else {
			$arr_rights[$i] = 1;
		}			
	}
	ksort($arr_rights);

	$str_rights = implode(';',$arr_rights);
			
	$arr_groups = array( 1 => array(0 => 'Admin', 1 => 'Admins', 2 => $str_rights, 3 => 1) );
	
	$session['user']['superuser'] = 1;
	
	systemlog('`5Superuser-Gruppen zurückgesetzt!`0',$session['user']['acctid']);
	
	savesetting( 'sugroups', addslashes(serialize($arr_groups)) );
	
	redirect('superuser.php');
	
}
else if ($_GET['op']=='sympathy')
{

	output ('`cStimmenanalyse zur Fürstenwahl`c`n`n');
	if ($_GET[who]=="")
	{ output('Allgemeine Übersicht`n`n');
	$user="";
	$source='sympathy_votes';
	$source2='sympathy_votes';
	}
	else
	{
		$who=$_GET['who'];
		$sql = 'SELECT accounts.name AS name, accounts.acctid AS acctid,sympathy AS sympathy,accounts.dragonkills AS dragonkills FROM account_extra_info JOIN accounts USING (acctid) WHERE accounts.acctid='.$who;
		$result = db_query($sql) or die(sql_error($sql));
		$rowwho = db_fetch_assoc($result);
		output ('Für '.$rowwho['name'].'`0`n`n');
		$user=' AND to_user='.$who;
		$source='(SELECT * FROM sympathy_votes WHERE to_user='.$who.') AS ab';
		$source2='sympathy_votes';
		$source3='(SELECT * FROM sympathy_votes) AS ac';
	}

	$sql = 'SELECT * FROM '.$source;
	$result = db_query($sql) or die(sql_error($sql));
	$count = db_num_rows($result);
	output ('Stimmen gesamt : '.$count.'`n');

	$sql = 'SELECT * FROM '.$source.' GROUP BY from_user';
	$result = db_query($sql) or die(sql_error($sql));
	$count = db_num_rows($result);
	output ('Anzahl Wähler : '.$count.'`n');

	$sql = 'SELECT * FROM '.$source.' GROUP BY to_user';
	$result = db_query($sql) or die(sql_error($sql));
	$count = db_num_rows($result);
	output ('Anzahl Gewählte User : '.$count.'`n`n');

	if (!$_GET[who]) {
		output ('Beteiligung`n');
		for ($i=10;$i>0;$i--)
		{
			$sql = 'SELECT * FROM sympathy_votes GROUP BY from_user HAVING COUNT(from_user)='.$i.'';
			$result = db_query($sql) or die(sql_error($sql));
			$count = db_num_rows($result);
			output ('Anzahl abgegebene Stimmen='.$i.' : '.$count.'`n');
		}
	}
	else
	{
		output ('`nStimmbündelung');
		for ($i=10;$i>0;$i--)
		{
			$sql = "SELECT COUNT(voteid) AS Anzahl FROM sympathy_votes WHERE to_user='$who' GROUP BY from_user HAVING Anzahl='$i'";
			$result = db_query($sql) or die(sql_error($sql));
			$count = db_num_rows($result);
			output ('`nErhaltene Stimmen = '.$i.' : '.$count.'x');
			if ($i==1) $mass=$count;
		}

		$sql = "SELECT * FROM sympathy_votes GROUP BY from_user HAVING COUNT(to_user)=1".$user;
		$result = db_query($sql) or die(sql_error($sql));
		$saving = db_num_rows($result);
		output (' (abzüglich '.$saving.' Wählern mit einer Stimme total : '.($mass-$saving).')`n`n');

		output ('`&Wer '.$rowwho['name'].' `&wählt, der wählt auch :`n');

		$sql= "SELECT a.name as uname,sv2.to_user FROM sympathy_votes sv2 LEFT JOIN accounts a ON a.acctid = sv2.to_user WHERE from_user IN (SELECT sv.from_user FROM sympathy_votes sv WHERE sv.to_user ='$who' GROUP BY sv.from_user)
AND sv2.to_user != '$who'
GROUP BY sv2.to_user ORDER BY sv2.to_user ASC LIMIT 5";

		//$sql= "SELECT *,COUNT( sv2.voteid ) AS anzahl , a.name as uname FROM sympathy_votes sv2 LEFT JOIN accounts a ON a.acctid = sv2.to_user WHERE to_user IN (SELECT sv.from_user FROM sympathy_votes sv WHERE sv.to_user ='$who' GROUP BY sv.from_user) GROUP BY sv2.to_user ORDER BY anzahl DESC LIMIT 5";
		$result = db_query($sql) or die(sql_error($sql));

		if ($_GET[who]==531) output('`4NPD`& ;)`n`n');
		output("<table><tr><td>Name</td></tr>",true);
		for($i=1;$i<=5;$i++)
		{
			$rowl = db_fetch_assoc($result);
			output("<td>$rowl[uname]</a></td></tr>",true);
		}
		output("</table>`n",true);
	}

	output ('`nDerzeitiger Stand - Top Ten`n');
	$sql = 'SELECT accounts.name AS name, accounts.acctid AS acctid,sympathy AS sympathy,accounts.dragonkills AS dragonkills FROM account_extra_info JOIN accounts USING (acctid) ORDER BY sympathy DESC, dragonkills DESC';
	$result = db_query($sql) or die(sql_error($sql));
	$count = db_num_rows($result);
	if ($count>10) $count=10;

	output("<table><tr><td>Name</td><td>Punkte</td></tr>",true);
	for($i=1;$i<=$count;$i++)
	{
		$row = db_fetch_assoc($result);
		output("<td>".$i.". <a href='superuser.php?op=sympathy&who=".$row[acctid]."'>$row[name]</a></td><td>$row[sympathy]</td></tr>",true);
		addnav("","superuser.php?op=sympathy&who=".$row[acctid]);
	}
	output("</table>",true);
	addnav('G?Zurück zur Grotte','superuser.php');
	addnav('G?Allgemeinübersicht','superuser.php?op=sympathy');

}
else if ($_GET['op'] == 'bounties')
{
	addnav('G?Zurück zur Grotte','superuser.php');
	output('`c`bDie Kopfgeldliste`b`c`n');
	$sql = 'SELECT name,alive,sex,level,laston,loggedin,lastip,uniqueid,bounty,activated FROM accounts WHERE bounty>0 ORDER BY bounty DESC';
	$result = db_query($sql) or die(sql_error($sql));
	output("<table border=0 cellpadding=2 cellspacing=1 bgcolor='#999999'>",true);
	output("<tr class='trhead'><td><b>Kopfgeld</b></td><td><b>Level</b></td><td><b>Name</b></td><td><b>Ort</b></td><td><b>Geschlecht</b></td><td><b>Status</b></td><td><b>Zuletzt da</b></tr>",true);
	$count = db_num_rows($result);
	for($i=0;$i<$count;$i++)
	{
		$row = db_fetch_assoc($result);
		output("<tr class='".($i%2?"trdark":"trlight")."'><td>",true);
		output("`^$row[bounty]`0");
		output("</td><td>",true);
		output("`^$row[level]`0");
		output("</td><td>",true);
		output("`&$row[name]`0");
		if ($session['user']['loggedin'])
		{
			output("</a>",true);
		}
		output("</td><td>",true);
		$loggedin=user_get_online(0,$row);
		output($row[location] ?"`3Kneipe`0" :($loggedin ?"`#Online`0" :"`3Die Felder`0"));
		output("</td><td>",true);
		output($row[sex]?"`!Weiblich`0":"`!Männlich`0");
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
		output("</td></tr>",true);
	}
	output("</table>",true);
}
else
{
	if ($session['user']['sex'])
	{
		output("`^Du tauchst in eine geheime Höhle unter, die nur wenige kennen. Dort wirst du von ");
		output("einigen muskulösen Männern mit nacktem Oberkörper empfangen, die ");
		output("dir mit Palmwedeln entgegen winken und dir anbieten, dich mit Trauben zu füttern, während du auf einer ");
		output("mit Seide bedeckten griechisch-römischen Liege faulenzt.`n`n");
	}
	else
	{
		output("`^Du tauchst in eine geheime Höhle unter, die nur wenige kennen. Dort wirst du von ");
		output("einigen spärlich bekleideten Frauen empfangen, die ");
		output("dir mit Palmwedeln entgegen winken und dir anbieten, dich mit Trauben zu füttern, während du auf einer ");
		output("mit Seide bedeckten griechisch-römischen Liege faulenzt.`n`n");
	}
				
	//Weitere Angaben
	output("`^Auf einer ebenhölzernen Leuchttafel steht zur Information der allwissenden Götter geschrieben:`n");
	//Schlossdaten
	output("`@Seit dem 16.09.2005, 16:40 wurde das verlassene Schloss `^".getsetting("CASTLEVISITS",0)."`@ mal betreten. Es wurden `^".getsetting("CASTLEMOVES",0)."`@ Schritte dort gemacht.`n");
	// Neuester Spieler
	$newplayer=stripslashes(getsetting('newplayer',''));
	if(!empty($newplayer)) {
		output('Letzte Neuanmeldung: `^'.$newplayer.'`@`n');
	}
	//Wieviele User sind online
	$result = db_fetch_assoc(db_query("SELECT COUNT(acctid) AS onlinecount FROM accounts WHERE locked=0 AND ".user_get_online() ));
	$onlinecount = $result['onlinecount'];
	
	if($onlinecount > getsetting('onlinetop',0)) {
		savesetting('onlinetop',$onlinecount);
		savesetting('onlinetoptime',time());
	}
	if($onlinecount == 23){
		$onlinecount = '`4`bDreiundzwanzig`b`@';	
	}
	elseif($onlinecount == 42) {
		$onlinecount = '`x`bZweiundvierzig`b`@';
	}
	output('Momentan sind `^'.$onlinecount.'`@ Spieler online, Rekord `^'.getsetting('onlinetop',0).'`@ am '.date('d.m.Y, H:i:s',getsetting('onlinetoptime',0)).'.`n');
	$result = db_fetch_assoc(db_query("SELECT COUNT(acctid) AS forumcount FROM account_extra_info WHERE incommunity<>0" ));
	$forumcount = $result['forumcount'];
	output('Es sind `^'.$forumcount.'`@ Spieler ins Forum eingetragen.`n`n`^');
	
	viewcommentary('superuser','Mit anderen Göttern unterhalten:',25,'sagt');

	// Prüfung, ob SU-Rechte vorhanden
	$arr_groups = unserialize( stripslashes(getsetting('sugroups','')) );
	if(empty($arr_groups) || sizeof($arr_groups) == 0) {
		addnav('`^SU-Rechte reparieren!`0','superuser.php?op=restore_rights');
	}
	// END Prüfung auf Rechte

	addnav('Aktionen');
	if (su_check(SU_RIGHT_PETITION)) addnav('Anfragen','su_petitions.php');
	addnav('Das göttlich-`~schwarze `&Brett','superuser.php?op=board');
	addnav('User-Diskussionen','superuser.php?op=discuss');
	addnav('ToDo','todolist.php');
	if (su_check(SU_RIGHT_REGISTRATUR)) {addnav('`@Registratur`0','registratur.php');}

	addnav('Kontrolle');
	if (su_check(SU_RIGHT_COMMENT)) addnav('K?Aktuelle Kommentare','su_comment.php?op=recent_comments',false,true);
	if (su_check(SU_RIGHT_FAILLOG)) addnav('Multis','logs.php');
	if (su_check(SU_RIGHT_MAILBOX)) addnav('Brieftauben-Amt','su_mails.php');
	if ($session['user']['superuser']>=1) addnav('B?SpielerBiografien','su_bios.php');
	if (su_check(SU_RIGHT_CHECKBOARDS)) addnav('Nachrichtenbretter','su_board.php');
	if (su_check(SU_RIGHT_DONATIONS)) addnav('Donationpoints','donators.php');
	if ($session['user']['superuser']>=1) addnav('Kopfgeldliste', 'superuser.php?op=bounties');
	if ($session['user']['superuser']>=1) addnav('Wahlanalyse', 'superuser.php?op=sympathy');

	addnav('Editoren - User');
	if (su_check(SU_RIGHT_RIGHTS)) addnav('Gruppeneditor','usergroupsu.php');
	if (su_check(SU_RIGHT_EDITORUSER)) {addnav('User Editor','user.php');}
	if (su_check(SU_RIGHT_EDITORUSER)) {addnav('Verbannungen','su_bans.php');}
	if (su_check(SU_RIGHT_EDITORUSER)) {addnav('`~Schwarze`0 Liste','su_blacklist.php');}
	if (su_check(SU_RIGHT_EDITORGUILDS)) addnav('Gilden Editor','dg_su.php');
	if (su_check(SU_RIGHT_EDITORHOUSES)) addnav('Hausmeister','suhouses.php');
	if (su_check(SU_RIGHT_EDITORTITLES)) addnav('Titel Editor','titleeditor.php');
	if (su_check(SU_RIGHT_EDITORLIBRARY)) addnav('Bibliothek Editor','sulib.php');

	addnav('Editoren - Spielwelt');
	if (su_check(SU_RIGHT_EDITORITEMS)) addnav('Item Editor','itemsu.php');
	if (su_check(SU_RIGHT_EDITORCASTLES)) addnav('Schloss Editor','su_mazeedit.php');
	if (su_check(SU_RIGHT_EDITORRANDOMCOM)) addnav('Zufallskommentar-Editor','randomcommentsu.php');
	if (su_check(SU_RIGHT_EDITORWORLD)) addnav('E?Monster Editor','creatures.php');
	if (su_check(SU_RIGHT_EDITORWORLD)) addnav('Runen Editor','su_runeedit.php');
	if (su_check(SU_RIGHT_EDITORFORESTSPECIAL)) addnav('Waldspecial Editor','waldspecialeditor.php');
	if (su_check(SU_RIGHT_EDITORSPECIALTIES)) addnav('Spezialitäten Editor (beta)','suspec.php');
	if (su_check(SU_RIGHT_EDITORMOUNTS)) addnav('Stalltier Editor','mounts.php');
	if (su_check(SU_RIGHT_EDITORWORLD)) addnav('Spott Editor','taunt.php');
	if (su_check(SU_RIGHT_EDITORWORLD)) addnav('Waffen Editor','weaponeditor.php');
	if (su_check(SU_RIGHT_EDITORWORLD)) addnav('Rüstungs Editor','armoreditor.php');
	if (su_check(SU_RIGHT_EDITORWORLD)) addnav('Rätsel Editor','riddleeditor.php');
	if (su_check(SU_RIGHT_EDITORCOLORS)) addnav('Farben Editor','colors.php');
	if (su_check(SU_RIGHT_EDITOREXTTXT)) addnav('Extended Texts Editor','su_extended_text.php');

	addnav('Mechanik');
	if (su_check(SU_RIGHT_GAMEOPTIONS)) addnav('Spieleinstellungen','configuration.php');
	if (su_check(SU_RIGHT_DEV)) addnav('Code-Beautifier','code_beautifier/beautify.html',false,true);
	if (su_check(SU_RIGHT_GAMEOPTIONS)) addnav('Wortfilter','badword.php');
	if (su_check(SU_RIGHT_DEV)) addnav('Datenbank reparieren','superuser.php?op=dbrepair');
	if (su_check(SU_RIGHT_RETITLE)) addnav('Retitler','retitle.php');
	if (su_check(SU_RIGHT_LOGOUTALL)) addnav('Alle Spieler ausloggen','user.php?op=logout_all');
	
	addnav('Aufzeichnungen');
	if (su_check(SU_RIGHT_SYSLOG)) addnav('Systemlog','su_logs.php?type=syslog');
	if (su_check(SU_RIGHT_FAILLOG)) addnav('Faillog','su_logs.php?type=faillog');
	if (su_check(SU_RIGHT_DEBUGLOG)) addnav('Debuglog','su_logs.php?type=debuglog');
	if (su_check(SU_RIGHT_STATS)) addnav('Statistiken++','su_stats.php');
	
}
page_footer();
?>
