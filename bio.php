<?php
/**
* bio.php: 		Zeigt Biographie + News eines Spielers an		
*				Sympathie-Addon by Maris
*				erfordert Felder "sympathy", "symp_votes" und "symp_given" in der Tabelle account_extra_info,
*				entsprechenden Eintrag in hof.php und Rücksetzung von symp_given in newday.php				
* @author LOGD-Core, modded and partly rewritten by Drachenserver-Team: maris <maraxxus@gmx.de>, talion <t@ssilo.de>
* @version DS-E V/2
*/

require_once('common.php');
$maxsymp=getsetting("max_symp","10");

// BIO-ANFANG

// Charakter per Accountid
$int_acctid = (int)$_GET['id'];
// Charakter per Loginname
$str_char = $_GET['char'];

// Daten abrufen
$result = db_query("SELECT 
						activated, ".($session['user']['profession'] == PROF_GUARD || $session['user']['profession'] == PROF_GUARD_HEAD ? '((maxhitpoints/30)+(attack*1.5)+(defence)) AS strength, ' : '')."
						login,loggedin,laston,name,level,sex,title,specialty,hashorse,acctid,age,marriedto,pvpflag,charisma,weapon,
						armor,imprisoned,profession,resurrections,dragonkills,race,housekey,punch,reputation,marks,
						guildid,guildfunc,guildrank,expedition  
						FROM accounts WHERE ".
						($int_acctid>0 ? 'acctid='.$int_acctid : 'login="'.$str_char.'"') );
						
$row = db_fetch_assoc($result);

$str_char = $row['login'];

$resextra = db_query("SELECT symp_given, symp_votes, sympathy,acctid,bio,
							has_long_bio,xmountname,hasxmount,charclass, html_locked, birthday, avatar, runes_ident
						 FROM account_extra_info WHERE acctid=$row[acctid]");
$rowextra = db_fetch_assoc($resextra);

$resextra2 = db_query("SELECT symp_given, symp_votes, profession_tmp FROM account_extra_info WHERE acctid = ".$session['user']['acctid']."");
$rowextra2 = db_fetch_assoc($resextra2);

$resdisc = db_query("SELECT * FROM disciples WHERE master = ".$row['acctid']);
$disciple = db_fetch_assoc($resdisc);

$sql = "SELECT specid,specname FROM specialty WHERE specid='".$row['specialty']."'";
$rowskill = db_fetch_assoc(db_query($sql));

$marks = get_marks_state($row['marks']);

if($row['hashorse']) {
	$sql = "SELECT mountname FROM mounts WHERE mountid='{$row['hashorse']}'";
	$result = db_query($sql);
	$mount = db_fetch_assoc($result);
	if ($mount['mountname']=='') {$mount['mountname'] = '`iKeines`i';}
}

if($row['housekey'] > 0) {
	$sql = "SELECT housename FROM houses WHERE houseid='{$row['housekey']}'";
	$result = db_query($sql);
	$house = db_fetch_assoc($result);
}

$skillnames = array($rowskill['specid']=>$rowskill['specname']);

$row['login'] = rawurlencode($row['login']);

$loggedin = user_get_online(0,$row);

$laston=round((strtotime(date('r'))-strtotime($row['laston'])) / 86400,0).' Tage';
if (substr($laston,0,2)=='1 ') $laston='1 Tag';
if (date('Y-m-d',strtotime($row['laston'])) == date('Y-m-d')) $laston='Heute';
if (date('Y-m-d',strtotime($row['laston'])) == date('Y-m-d',strtotime(date('r').'-1 day'))) $laston='Gestern';
if ($loggedin) {$laston = 'Jetzt';}
// END Daten abrufen

// Navi erstellen
addnav('Geschichte');
addnav('Die Aufzeichnungen','history.php?acctid='.$row['acctid'].'&op='.$_GET['op'].'&ret='.urlencode($_GET['ret']));

// Sympathie
if ($session['user']['acctid']!=$row['acctid'])
{
	if (($rowextra2['symp_given']==0) && ($rowextra2['symp_votes']<$maxsymp) && (($session['user']['dragonkills']>0) && (getsetting("symp_dk_lock","1")==1)))
	{
		addnav('Sympathiepunkte');
		$spoints=$maxsymp-$rowextra2['symp_votes'];
		addnav($spoints.' übrig');
		addnav("Vergeben","bio.php?char=".urlencode($str_char)."&act=symp&op=".$_GET['op']."&ret=".$_GET['ret']);
	}
}

addnav('Sonstiges');

if ($_GET[ret]==""){

	if ($_GET[op]==pri) {
		addnav("Zum Kerker","prison.php?op=look");
	}
	else {
		addnav("Zur Liste der Krieger","list.php");
	}
}
else{
	$return = preg_replace("'[&?]c=[[:digit:]-]+'","",$_GET[ret]);
	$return = substr($return,strrpos($return,"/")+1);
	addnav("Zurück",$return);
}
if ($session['user']['superuser']>0) {
	addnav("MOD-Aktionen");
	if(su_check(SU_RIGHT_PRISON)) {
		if ($row['imprisoned']==0) {addnav("Einkerkern","bio.php?char=".urlencode($str_char)."&act=prison&ret=".$_GET['ret']."&op=".$_GET['op']);}
		else {addnav("Befreien","bio.php?char=".urlencode($str_char)."&act=free&ret=".$_GET['ret']."&op=".$_GET['op']);}
	}
	if(su_check(SU_RIGHT_EDITORUSER)) {addnav('Usereditor','user.php?op=edit&userid='.$row['acctid']);}
	if(su_check(SU_RIGHT_COMMENT)) {addnav('Kommentare','su_comment.php?op=user&uid='.$row['acctid'],false,true);}
	if(su_check(SU_RIGHT_MUTE)) {
		if($row['activated'] != USER_ACTIVATED_MUTE) {addnav('Knebeln','bio.php?char='.urlencode($str_char).'&act=mute&ret='.$_GET['ret'].'&op='.$_GET['op']);}
		else {addnav('Entknebeln','bio.php?char='.urlencode($str_char).'&act=demute&ret='.$_GET['ret'].'&op='.$_GET['op']);}
	}
	
	if(su_check(SU_RIGHT_LOCKHTML)) {
		if($rowextra['html_locked'] == 0) {addnav('HTML sperren','bio.php?char='.urlencode($str_char).'&act=lock_html&ret='.$_GET['ret'].'&op='.$_GET['op']);}
		else {addnav('HTML Entsperren','bio.php?char='.urlencode($str_char).'&act=unlock_html&ret='.$_GET['ret'].'&op='.$_GET['op']);}
	}
	addnav('Diskussion','bio.php?act=discuss&char='.urlencode($str_char).'&ret='.$_GET['ret']);

 if(su_check(SU_RIGHT_EXPEDITION)) {
	 addnav("Expedition");
	 if ($row['expedition']==0)
	 {
	 addnav("Einladen","bio.php?char=".urlencode($str_char)."&act=ehire");
	 } else
	 {
	 addnav("Rauswerfen","bio.php?char=".urlencode($str_char)."&act=efire");
	 }
	}
 
}
if (($session['user']['profession']==PROF_GUARD_HEAD || $session['user']['profession']==PROF_GUARD) AND $session['user']['superuser'] == 0) {
	addnav("Stadtwache");
	
	if($row['profession']==PROF_GUARD_HEAD) {
		addnav("Entlassen","bio.php?char=".urlencode($str_char)."&act=fire");
	}
	
	$strength = (($session['user']['maxhitpoints']/30)+($session['user']['attack']*1.5)+($session['user']['defence']));
	
	if($strength >= $row['strength'] && $rowextra2['profession_tmp'] == 0 && $row['imprisoned'] == 0 && $row['loggedin']) {
			
		addnav('Einkerkern!','bio.php?act=guard_prison&ret='.$_GET['ret'].'&char='.urlencode($str_char).'&op='.$_GET['op']);	
			
	}
	
}
// Bürgerwehr
if ($row['expedition']!=0 && $session['user']['acctid']!=$row['acctid'])
{
if (($session['user']['profession']==PROF_DDL_COLONEL || $session['user']['profession']==PROF_DDL_MAJOR || su_check(SU_RIGHT_EXPEDITION)) && (($row['profession']>40 && $row['profession']<50) || $row['profession']==0)) {
addnav("Bürgerwehr");

  if ($row['profession']==0 || ($row['profession']>40 && $row['profession']<46) || ($session['user']['profession']==49 && $row['profession']>40 && $row['profession']<48)) //Beförderung
  {
  addnav("Befördern","bio.php?char=".urlencode($str_char)."&ret=".$_GET['ret']."&act=promote");
  }

  if (($row['profession']>40 && $row['profession']<47) || ($session['user']['profession']==49 && $row['profession']>40 && $row['profession']<49) || (su_check(SU_RIGHT_EXPEDITION) && $row['profession']<50)) //Degradierung
  {
  addnav("Degradieren","bio.php?char=".urlencode($str_char)."&ret=".$_GET['ret']."&act=degrade");
  }

}
}
// END Navi erstellen


page_header('Charakter Biographie: '.preg_replace("'[`].'",'',$row['name']));

output('`&Biographie : '.($loggedin?'<img src="images/new-online.gif" alt="&gt;" width="4" height="6" align="absmiddle">':'<img src="images/new.gif" alt="&gt;" width="4" height="6" align="absmiddle">').' '.$row['name'],true);
if ($row['marks'] >= CHOSEN_FULL) { output('`^, '.($row['sex']?'die':'der').' Auserwählte`&');}
if ($session['user']['loggedin']) { output('<a href="mail.php?op=write&to=$row[login]" target="_blank" onClick="'.popup("mail.php?op=write&to=$row[login]").';return false;"><img src="images/newscroll.GIF" width="16" height="16" alt="Mail schreiben" border="0"></a>',true); }

if($row['profession']) {

	$prof = &$profs[$row['profession']];
	if($prof[2]) {
		output('`n`n`&`bBeruf: ');
		output($prof[3].$prof[$row['sex']]);
		output('`b`n`&');
	}

}

if ($row['imprisoned']>0) {output("`n(Im Kerker für ".($row['imprisoned'])." Tage.)");}
if ($row['imprisoned']<0) {output("`n(Auf unbestimmte Zeit im Kerker.)");}
if($row['activated'] == USER_ACTIVATED_MUTE) {output("`n(Von einem Mod geknebelt.)");}
if($rowextra['html_locked'] == 1) {output("`n(HTML gesperrt.)");}

output('<table><tr><td width="210" align="center">',true);

if (substr($row['name'],0,13)=="Flauschihase ")
{
  output('`n`n<img src="images/fluffy.jpg" ',true);
}
elseif (substr($row['name'],0,10)=="`2Kröte`0 ")
{
  output('`n`n<img src="images/toad.jpg" ',true);
}
elseif (substr($row['name'],0,11)=="`2Frosch`0 ")
{
  output('`n`n<img src="images/kermit.jpg" ',true);
}
elseif (getsetting("avatare",0)==1){
	if ($rowextra[avatar]){
		$pic_size = @getimagesize($rowextra[avatar]);
		$pic_width = $pic_size[0];
		$pic_height = $pic_size[1];
		output('`n`n<img src="'.$rowextra['avatar'].'" ',true);
		if ($pic_width > 200) output('width="200" ',true );
		if ($pic_height > 200) output('height="200" ',true );
		output('alt="'.preg_replace("'[`].'",'',$row['name']).'">&nbsp;',true);
	} else {
		output('`n`n(kein Bild)&nbsp;&nbsp;&nbsp;',true);
	}
}

output('</td><td valign="top" width="160">',true);

output('`n`bAllgemeines:`b');
output('`n`^Titel: `@'.$row['title'].'`n');

if (getsetting('activategamedate','0')==1 && $rowextra['birthday']!='') { output('`^Ankunft: `@'.getgamedate($rowextra['birthday']).'`n'); }

output('`^Zuletzt gesehen: `@'.$laston.'`n');
output('`n`&`bInteressantes:`b`n');

if ($row['dragonkills']>0) {output('`^Drachenkills: `@'.$row['dragonkills'].'`n');}

output('`^Level: `@'.$row['level'].'`n');
output('`^Rasse: `@'.$races[$row['race']].'`n');

if(strlen($rowextra['charclass']) > 0) {output('`^Klasse: `@'.closetags($rowextra['charclass'],'`b`c`i').'`n');}

output('`^Geschlecht: `@'.($row[sex]?'Weiblich':'Männlich').'`n');
output('`^Spezialgebiet: `@'.$skillnames[$row['specialty']].'`n');

output('<table border="0" cellspacing="0" cellpadding="0"><tr><td>`^Ansehen:&nbsp;</td><td>'.grafbar(100, ($row['reputation']+50),50,12).'</td></tr></table>',true);
	
output('`n`&`bBesitz:`b`n');

if($row['hashorse']) {		
	if ($rowextra['hasxmount']==1) {
		output('`^Tier: '.$rowextra['xmountname'].' `&('.closetags($mount['mountname'],'`b`c`i').'`&)`n'); 
	}
	else {
		output('`^Tier: `@'.$mount['mountname'].'`n'); 
	}
}
if ($row['housekey']) {
	output("`^Haus: `@".closetags($house['housename'],'`b`c`i')."`@`n(Nr. $row[housekey])`n");
}

output("`^Waffe: `@".closetags($row['weapon'],'`b`i`c')."`n");
output("`^Rüstung: `@{$row[armor]}`n");

if ($row['pvpflag']=="5013-10-06 00:42:00") {output("`4`iSteht unter besonderem Schutz`i");}

output('</td><td width="200" valign="top">',true);
		
if($row['guildid']) {
	require_once(LIB_PATH.'dg_funcs.lib.php');
	require_once('dg_output.php');
	
	output('`n`bGilde:`b');
	dg_show_bio($row);
	output('`n');
}
			
if ($row['marks']>0) {
	output("`n`&`bTrägt:`b`n ");
	if ($marks['spirit']) output ("`tMal des Geistes`n`& ");
	if ($marks['water']) output ("`@Mal des Wassers`n`&");
	if ($marks['fire']) output ("`4Mal des Feuers`n`&");
	if ($marks['air']) output ("`9Mal der Luft`&`n");
	if ($marks['earth']) output ("`^Mal der Erde`&.`n");
	if ($row['marks'] >= CHOSEN_BLOODGOD) output ("`n`4Hat einen Pakt mit dem Blutgott.`n");
}
		
output('`n`&`bSonstiges:`b`n');

output('`^Bester Angriff: `@'.$row['punch'].'`n');
		
if ($row['marriedto']){
	if ($row[marriedto]==4294967295) {
		output('`^Verheiratet mit: `@'.($row['sex']?'Seth':'Violet').'`n');
	}
	elseif ($row['charisma']==4294967295 || $row['charisma']==999){
		$sql = "SELECT name FROM accounts WHERE acctid='{$row['marriedto']}'";
		$result = db_query($sql);
		$partner = db_fetch_assoc($result);
		output("`^".(($row[charisma]==999)?"Verlobt":"Verheiratet")." mit: `@{$partner['name']}`n");
	}
}
		
output("`^Alter seit DK: `@$row[age]`^ Tage`n");
output("`^Wiedererweckt: `@$row[resurrections]x`n");
output("`^Sympathiepunkte: `@$rowextra[sympathy]`n");

//Runenrang
$knownrunes = count(explode(';',$rowextra['runes_ident']))-1;
if( !$knownrunes ){
	$runerang = 'Unwissende';
	if( !$row['sex'] ){
		$runerang .= 'r';
	}
}
else if( $knownrunes < 5 ){
	$runerang = 'Lehrling';
}
else if( $knownrunes < 10 ){
	$runerang = 'Forscher';
	if( $row['sex'] ){
		$runerang .= 'in';
	}
}
else if( $knownrunes < 15 ){
	$runerang = 'Wissende';
	if( !$row['sex'] ){
		$runerang .= 'r';
	}
}
else if( $knownrunes < 20 ){
	$runerang = 'Eingeweihte';
	if( !$row['sex'] ){
		$runerang .= 'r';
	}
}
else if( $knownrunes < 24 ){
	$runerang = 'Seneschall';
}
else if( $knownrunes == 24 ){
	$runerang = $row['sex'] ? 'Matriarch' : 'Patriarch';
}
output('`^Runenrang: `q'.$runerang.'`n');


output("</td></tr></table>",true);

if ($disciple['state']>0) {
	output("`n".$row['name']." `3 wird begleitet von ".($row['sex']?"ihrem":"seinem")." ".get_disciple_stat($disciple['state'])." `3Knappen ".$disciple['name'].".`n");
}

$bio = '';
		
if (($row['marks']<CHOSEN_FULL) && ($rowextra['has_long_bio']!=1)) {
	if ($rowextra['bio']>"") {
		$bio = $rowextra['bio'];		
	} 
}
else {
	$result2 = db_query("SELECT long_bio FROM account_extra_info WHERE acctid=$row[acctid]");
	$row2 = db_fetch_assoc($result2);
	
	$max_l = getsetting('longbiomaxlength',4096);
	
	if ($row2['long_bio']>"") {
		$bio = substr($row2['long_bio'],0,$max_l);		
	}

}

$bio = soap(closetags($bio,'`b`c`i'));

$allow_tags = ($rowextra['html_locked'] ? '' : '<img>');

$bio = strip_tags($bio,$allow_tags);

output('`n`^Bio: `@`n'.$bio.'`n'); 

// NEWS
output('`n`^Letzte Leistungen (und Niederlagen) von '.$row['name'].'`^');
$result = db_query("SELECT newstext,newsdate FROM news WHERE accountid=$row[acctid] ORDER BY newsdate DESC,newsid ASC LIMIT 70");
$odate="";

$news_count = db_num_rows($result);

$news_out = '';

for ($i=0;$i<$news_count;$i++){
	$news_row = db_fetch_assoc($result);
	if ($odate!=$news_row[newsdate]){
		$news_out.='`n`b`@'.strftime('%A, %e. %B',strtotime($news_row['newsdate'])).'`b`n';
		$odate=$news_row['newsdate'];
	}
	$news_out .= $news_row['newstext'].'`n';

}
output($news_out);
// END NEWS


// BIO ENDE

// Sympathie
if ($_GET[act]=="symp") {
$sres = db_query("SELECT symp_given,symp_votes FROM account_extra_info WHERE acctid=".$session['user']['acctid']."");
$rowsy = db_fetch_assoc($sres);
if (($rowsy['symp_given']==0) && ($rowsy['symp_votes']<$maxsymp) && (($session['user']['dragonkills']>0) && (getsetting("symp_dk_lock","1")==1)))
{
$sql = "UPDATE account_extra_info SET sympathy=sympathy+1 WHERE acctid = ".$row['acctid']."";
db_query($sql) or die(sql_error($sql));
$sql = "UPDATE account_extra_info SET symp_given=1, symp_votes=symp_votes+1 WHERE acctid = ".$session['user']['acctid']."";
db_query($sql) or die(sql_error($sql));

$sql="INSERT INTO sympathy_votes (timestamp,from_user,to_user) VALUES (now(),".$session['user']['acctid'].",".$row['acctid'].")";
db_query($sql) or die(sql_error($sql));

debuglog("Vergibt einen Sympathiepunkt an ",$row['acctid']);
redirect("bio.php?char=".urlencode($str_char)."&act=0&op=".$_GET['op']."&ret=".$_GET['ret']);

}
}


// Stadtwacheneinkerkerung
if($_GET['act'] == 'guard_prison') {
		
	$ok = 0;
	
	$time = date('Y-m-d H:i:s',time()-600);
	
	// Auf Aktivität in derselben Chatarea prüfen
	$sql = 'SELECT c1.section FROM commentary c1
			INNER JOIN commentary c2 ON c1.section=c2.section AND c2.author='.$row['acctid'].' AND c2.postdate>"'.$time.'"
			WHERE c1.author='.$session['user']['acctid'].' AND c1.postdate>"'.$time.'" AND 
			(c1.section = "village" OR c1.section = "marketplace" OR c1.section = "garden") 
			ORDER BY c1.commentid DESC LIMIT 1';
			
	$ok = db_fetch_assoc(db_query($sql));
	
	if($ok['section'] != '') {
		
		$sql = 'UPDATE accounts SET imprisoned=-5,location='.USER_LOC_PRISON.',restatlocation=0 WHERE acctid='.$row['acctid'].' AND imprisoned=0';
		db_query($sql);
		
		$msg = ': `5überwindet '.$row['login'].', packt ihn mit eisernem Griff und führt ihn Richtung Kerker!';
		
		$sql = 'INSERT INTO commentary SET comment="'.addslashes($msg).'",postdate=NOW(),author='.$session['user']['acctid'].',section="'.addslashes($ok['section']).'"';
		db_query($sql);
				
		$sql = 'UPDATE account_extra_info SET profession_tmp=1 WHERE acctid='.$session['user']['acctid'];
		db_query($sql);
		
		debuglog('nutzte seine Stadtwachenfähigkeiten und verhaftete ',$row['acctid']);
		addnews($session['user']['name'].'`# hat '.$row['name'].'`# in seiner Eigenschaft als Stadtwache festgenommen und in den Kerker gesteckt!');
				
		systemmail($row['acctid'],'`$Verhaftet!',$session['user']['name'].'`$ hat dich soeben in seiner Eigenschaft als Stadtwache festgenommen. Du darfst nun einen Tag im Kerker verbringen!');
		
		redirect("bio.php?char=".urlencode($str_char)."&act=0&ret=".$_GET['ret']."&op=".$_GET['op']);
				
	}
	
	
	
}	


// MOD-AKTIONEN
if ($_GET[act]=="prison") {
	$sql = "UPDATE accounts SET location=".USER_LOC_PRISON.",restatlocation=0,imprisoned=-2 WHERE acctid = ".$row['acctid']."";
	db_query($sql) or die(sql_error($sql));
	systemmail($row['acctid'],"`\$Eingekerkert!`0","`@{$session['user']['name']}`& hat dich in den Kerker sperren lassen. Warscheinlich hast du dich schlecht benommen oder gegen die Regeln verstoßen. Wenn du dir nicht sicher bist, solltest du vielleicht mal in einer Mail nach dem Grund fragen.");
	$sql = "UPDATE keylist SET hvalue=0 WHERE owner=".$row[acctid];
	db_query($sql) or die(sql_error($sql));
	
	systemlog('`qEinkerkerung von:`0 ',$session['user']['acctid'],$row['acctid']);
	
	redirect("bio.php?char=".urlencode($str_char)."&act=0&ret=".$_GET['ret']."&op=".$_GET['op']);
}

else if ($_GET[act]=="free") {
	$sql = "UPDATE accounts SET imprisoned=0,location=0 WHERE acctid = ".$row['acctid']."";
	db_query($sql) or die(sql_error($sql));
	systemmail($row['acctid'],"`@Freilassung!`0","`@{$session['user']['name']}`& hat dich wieder aus dem Kerker befreit.");
	$sql = "UPDATE keylist SET hvalue=0 WHERE owner=".$row[acctid];
	db_query($sql) or die(sql_error($sql));
	
	systemlog('`qFreilassung von:`0 ',$session['user']['acctid'],$row['acctid']);
	
	redirect("bio.php?char=".urlencode($str_char)."&act=0&ret=".$_GET['ret']."&op=".$_GET['op']);
}
else if ($_GET[act]=="mute") {
	$sql = "UPDATE accounts SET activated=".USER_ACTIVATED_MUTE." WHERE acctid = ".$row['acctid']."";
	db_query($sql) or die(sql_error($sql));
	systemmail($row['acctid'],"`\$Geknebelt!`0","`@{$session['user']['name']}`& hat dich geknebelt, so dass du nun keine Kommentare mehr schreiben kannst. Warscheinlich hast du dich schlecht benommen oder gegen die Regeln verstoßen. Wenn du dir nicht sicher bist, solltest du vielleicht mal in einer Mail nach dem Grund fragen.");
	
	systemlog('`qKnebelung von:`0 ',$session['user']['acctid'],$row['acctid']);
	
	redirect("bio.php?char=".urlencode($str_char)."&act=0&ret=".$_GET['ret']."&op=".$_GET['op']);
}
else if ($_GET[act]=="demute") {
	$sql = "UPDATE accounts SET activated=0 WHERE acctid = ".$row['acctid']."";
	db_query($sql) or die(sql_error($sql));
	systemmail($row['acctid'],"`@Knebel entfernt!`0","`@{$session['user']['name']}`& hat dich wieder von deinem Knebel befreit.");
	
	systemlog('`qEntKnebelung von:`0 ',$session['user']['acctid'],$row['acctid']);
	
	redirect("bio.php?char=".urlencode($str_char)."&act=0&ret=".$_GET['ret']."&op=".$_GET['op']);
}

else if ($_GET[act]=="lock_html") {
	$sql = "UPDATE account_extra_info SET html_locked=1 WHERE acctid = ".$row['acctid']."";
	db_query($sql) or die(sql_error($sql));
	systemmail($row['acctid'],"`\$HTML gesperrt!`0","`@{$session['user']['name']}`& hat HTML für deine Bio deaktiviert. Wahrscheinlich hast du es mit der Nutzung von Bildern übertrieben. Wenn du dir nicht sicher bist, solltest du vielleicht mal in einer Mail nach dem Grund fragen.");
	
	systemlog('`qSperrung des Bio-HTML für:`0 ',$session['user']['acctid'],$row['acctid']);
	
	redirect("bio.php?char=".urlencode($str_char)."&act=0&ret=".$_GET['ret']."&op=".$_GET['op']);
}
else if ($_GET[act]=="unlock_html") {
	$sql = "UPDATE account_extra_info SET html_locked=0 WHERE acctid = ".$row['acctid']."";
	db_query($sql) or die(sql_error($sql));
	
	systemlog('`qEntSperrung des Bio-HTML für:`0 ',$session['user']['acctid'],$row['acctid']);
	
	redirect("bio.php?char=".urlencode($str_char)."&act=0&ret=".$_GET['ret']."&op=".$_GET['op']);
}

else if ($_GET[act]=="fire") {
	$sql = "UPDATE accounts SET profession=".PROF_GUARD_ENT." WHERE acctid = ".$row['acctid']."";
	db_query($sql) or die(sql_error($sql));
	systemmail($row['acctid'],"`\$Entlassen!`0","`@Hauptmann {$session['user']['name']}`& hat dich aus dem Dienst der Stadtwache entlassen.");
	addnews("".$session['user']['name']."`& hat ".$row['acctid']." aus der Stadtwache entlassen!");
	redirect("bio.php?char=".urlencode($str_char)."&act=0&ret=".$_GET['ret']."&op=".$_GET['op']);
}

else if ($_GET[act]=="promote") {
    $profession=$row['profession'];
    if ($profession==0) { $profession=41; }
    elseif ($profession>=41 && $profession<49) { $profession++; }
    $rank=getprofession($profession);
	$sql = "UPDATE accounts SET profession=$profession WHERE acctid = ".$row['acctid']."";
	db_query($sql) or die(sql_error($sql));
	systemmail($row['acctid'],"`\$DDL: Beförderung!`0","`@{$session['user']['name']}`& hat dich zum {$rank} der Bürgerwehr befördert!");
    addnews_ddl("".$session['user']['name']."`& hat ".$row['name']." zum `^{$rank}`& befördert!");
	redirect("bio.php?char=".urlencode($str_char)."&act=0&ret=".$_GET['ret']."&op=".$_GET['op']);
}

else if ($_GET[act]=="degrade") {
    $profession=$row['profession'];
    if ($profession==41) { $profession=0; }
    elseif ($profession>41 && $profession<50) { $profession--; }
    $rank=getprofession($profession);
	$sql = "UPDATE accounts SET profession=$profession WHERE acctid = ".$row['acctid']."";
	db_query($sql) or die(sql_error($sql));
	systemmail($row['acctid'],"`\$DDL: Degradierung!`0","`@{$session['user']['name']}`& hat dich zum {$rank} der Bürgerwehr degradiert!");
    addnews_ddl("".$session['user']['name']."`& hat ".$row['name']." zum `^{$rank}`& degradiert!");
	redirect("bio.php?char=".urlencode($str_char)."&act=0&ret=".$_GET['ret']."&op=".$_GET['op']);
}

else if ($_GET[act]=="efire") {
	$sql = "UPDATE accounts SET expedition=0 WHERE acctid = ".$row['acctid']."";
	db_query($sql) or die(sql_error($sql));
	systemmail($row['acctid'],"`\$Die Expedition ist für dich beendet!`0","`@Deine Einladung zur Expedition wurde dir entzogen. Du kannst in einer Anfrage nach dem Grund fragen.");
	redirect("bio.php?char=".urlencode($str_char)."&act=0&ret=".$_GET['ret']."&op=".$_GET['op']);
}

else if ($_GET[act]=="ehire") {
	$sql = "UPDATE accounts SET expedition=1 WHERE acctid = ".$row['acctid']."";
	db_query($sql) or die(sql_error($sql));
	systemmail($row['acctid'],"`\$Einladung zur Expedition!`0","`@Du wurdest zu einer Expedition in die dunklen Lande eingeladen. Du kannst das Lager über den Dorfplatz erreichen.");
	redirect("bio.php?char=".urlencode($str_char)."&act=0&ret=".$_GET['ret']."&op=".$_GET['op']);
}

else if ($_GET[act]=="discuss") {
$sql = "UPDATE account_extra_info SET discussion=1 WHERE acctid = ".$row['acctid']."";
db_query($sql) or die(sql_error($sql));
redirect("discuss.php?who=".$row['acctid']."&char=".urlencode($_GET[char])."&ret=".$_GET['ret']);
}

// END MOD-AKTIONEN

page_footer();

?>
