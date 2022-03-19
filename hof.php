<?php

// 22062004

// New Hall of Fame features by anpera
// http://www.anpera.net/forum/viewforum.php?f=27
// with code from centralserver for 0.9.8; re-imported to 0.9.7

require_once "common.php";

page_header("Ruhmeshalle");
checkday();

$playersperpage = 50;

$max_age = " AND age <= ".getsetting('maxagepvp',50);
$max_su = "";//" AND (name NOT LIKE '%*%' AND superuser < 3) ";
$max_su_punch = ' AND superuser < 3 ';

$op = "kills";
if ($_GET['op']) $op = $_GET['op'];
$subop = "most";
if ($_GET['subop']) $subop = $_GET['subop'];

$sql = "SELECT count(acctid) AS c FROM accounts WHERE locked=0";
if ($op == "kills") {
	$sql = "SELECT count(acctid) AS c FROM accounts WHERE locked=0 AND dragonkills>0".$max_age.$max_su;
} elseif ($op == "days") {
	//	$sql = "SELECT count(acctid) AS c FROM accounts WHERE locked=0 AND dragonkills>0 AND bestdragonage>0".$max_age.$max_su;
} elseif ($op == "abwesend") {
	$sql = "SELECT count(acctid) AS c FROM accounts WHERE locked=0 AND dragonkills>0 AND DATEDIFF(NOW(),laston) > 3";
}
elseif($op == 'profs') {
	$sql = '';
}

addnav("Bestenlisten");
addnav("Drachenkills", "hof.php?op=kills&subop=$subop&page=$page");
addnav("Reichtum", "hof.php?op=money&subop=$subop&page=$page");
addnav("Edelsteine", "hof.php?op=gems&subop=$subop&page=$page");
addnav("Schönheit", "hof.php?op=charm&subop=$subop&page=$page");
addnav("Stärke", "hof.php?op=tough&subop=$subop&page=$page");
addnav("Schlagkraft","hof.php?op=punch&subop=$subop&page=$page");
addnav("Tollpatsche", "hof.php?op=resurrects&subop=$subop&page=$page");
addnav("Geschwindigkeit", "hof.php?op=days&subop=$subop&page=$page");
addnav("Arenakämpfer","hof.php?op=battlepoints&subop=$subop&page=$page");
addnav("Verschollene","hof.php?op=abwesend&subop=$subop&page=$page");
addnav("Häftlinge","hof.php?op=kerker&subop=$subop&page=$page");
addnav("Alter","hof.php?op=birth&subop=$subop&page=$page");
addnav("Schatzsucher","hof.php?op=treasure&subop=$subop&page=$page");
addnav("Heizmeister","hof.php?op=spoil&subop=$subop&page=$page");
addnav("Knappen","hof.php?op=disciple&subop=$subop&page=$page");
addnav("Bettelstein","hof.php?op=beggar&subop=$subop&page=$page");
addnav("Bierkönige","hof.php?op=beer&subop=$subop&page=$page");
addnav("Uffs Maul!","hof.php?op=beatenup&subop=$subop&page=$page");
addnav("Goldener Joggingschuh","hof.php?op=runaway&subop=$subop&page=$page");
addnav("Bewaffnung","hof.php?op=weapon&subop=$subop&page=$page");
addnav("Puppenbesitzer","hof.php?op=doll&subop=$subop&page=$page");
addnav("Sympathie","hof.php?op=symp&subop=$subop&page=$page");
if ($session[user][alive]==0) addnav("Ramius' Lieblinge","hof.php?op=grave&subop=$subop&page=$page");

if($sql) {
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	$totalplayers = $row['c'];

	$page = 1;
	if ($_GET['page']) $page = (int)$_GET['page'];
	$pageoffset = $page;
	if ($pageoffset > 0) $pageoffset--;
	$pageoffset *= $playersperpage;
	$from = $pageoffset+1;
	$to = min($pageoffset+$playersperpage, $totalplayers);
	$limit = "$pageoffset,$playersperpage";

	addnav("Sortieren nach");
	addnav("Besten", "hof.php?op=$op&subop=most&page=$page");
	addnav("Schlechtesten", "hof.php?op=$op&subop=least&page=$page");
	addnav("Seiten");
	for($i = 0; $i < $totalplayers; $i+= $playersperpage) {
		$pnum = ($i/$playersperpage+1);
		$min = ($i+1);
		$max = min($i+$playersperpage,$totalplayers);
		addnav("Seite $pnum ($min-$max)", "hof.php?op=$op&subop=$subop&page=$pnum");
	}

}

addnav("Sonstiges");
addnav("Offizielle Ämter","hof.php?op=profs&subop=$subop&page=$page");
addnav("Paare dieser Welt","hof.php?op=paare");
if ($session[user][alive]){
	addnav("Zurück zum Dorf","village.php");
}else{
	addnav("Zurück zu den Schatten","shades.php");
}

function display_table($title, $sql, $none=false, $foot=false, $data_header=false, $tag=false){
	global $session, $from, $to, $page;
	output("`c`b`^$title`0`b `7(Seite $page: $from-$to)`0`c`n");
	output('<table cellspacing="0" cellpadding="2" align="center"><tr class="trhead">',true);
	output("<td>`bRang`b</td><td>`bName`b</td>", true);
	if ($data_header !== false) {
		for ($i = 0; $i < count($data_header); $i++) {
			output("<td>`b".$data_header[$i]."`b</td>", true);
		}
	}
	if(!is_array($sql)) {
		$result = db_query($sql) or die(db_error(LINK));
	}
	$count = (is_array($sql) ? sizeof($sql) : db_num_rows($result));
	if ($count == 0){
		$size = ($data_header === false) ? 2 : 2+count($data_header);
		//echo $size;
		if ($none === false) $none = "Keine Spieler gefunden";
		output('<tr class="trlight"><td colspan="'. $size .'" align="center">`&' . $none .'`0</td></tr>',true);
	} else {
		
		for ($i=0;$i<$count;$i++){
			if(!is_array($sql)) {$row = db_fetch_assoc($result);}
			else {$row = $sql[$i];}
			
			if ($row[name]==$session[user][name]){
				//output("<tr class='hilight'>",true);
				output("<tr bgcolor='#005500'>",true);
			} else {
				output('<tr class="'.($i%2?"trlight":"trdark").'">',true);
			}
			output("<td>".($i+$from).".</td><td>`&{$row[name]}`0</td>",true);
			if ($data_header !== false) {
				for ($j = 0; $j < count($data_header); $j++) {
					$id = "data" . ($j+1);
					$val = $row[$id];
					if ($tag !== false) $val = $val . " " . $tag[$j];
					output("<td align='right'>".$val."</td>",true);
				}
			}
			output("</tr>",true);
		}
	}
	output("</table>", true);
	if ($foot !== false) output("`n`c$foot`c");
}

$order = "DESC";
if ($_GET[subop] == "least") $order = "ASC";
$sexsel = "IF(sex,'<img src=\"images/female.gif\">&nbsp; &nbsp;','<img src=\"images/male.gif\">&nbsp; &nbsp;')";
$racesel = "CASE race WHEN 1 THEN '`2Troll`0' WHEN 2 THEN '`^Elf`0' WHEN 3 THEN '`&Mensch`0' WHEN 4 THEN '`#Zwerg`0' WHEN 5 THEN '`5Echse`0' WHEN 6 THEN '`5Dunkelelf`0' WHEN 7 THEN '`TWerwolf`0' WHEN 8 THEN '`6Goblin`0' WHEN 9 THEN '`2Ork`0' WHEN 10 THEN '`4Vampir`0' WHEN 11 THEN '`tHalbling`0' WHEN 12 THEN '`4Dämon`0' WHEN 13 THEN '`9Schelm`0' WHEN 14 THEN '`^Engel`0' WHEN 15 THEN '`&Avatar`0' ELSE '`7Unbekannt`0' END";

if ($_GET[op]=="money"){
	$sql = "SELECT name,(goldinbank+gold+round((((rand()*10)-5)/100)*(goldinbank+gold))) AS data1 FROM accounts WHERE locked=0 ".$max_age.$max_su." ORDER BY data1 $order, level $order, experience $order, acctid $order LIMIT $limit";
	$adverb = "reichsten";
	if ($_GET[subop] == "least") $adverb = "ärmsten";
	$title = "Die $adverb Krieger in diesem Land";
	$foot = "(Vermögen +/- 5%)";
	$headers = array("Geschätztes Vermögen");
	$tags = array("Gold");
	display_table($title, $sql, false, $foot, $headers, $tags);
} elseif ($_GET[op] == "gems") {
	$sql = "SELECT name FROM accounts WHERE locked=0 ".$max_age.$max_su." ORDER BY gems $order, level $order, experience $order, acctid $order LIMIT $limit";
	if ($_GET[subop] == "least") $adverb = "wenigsten";
	else $adverb = "meisten";
	$title = "Die Krieger mit den $adverb Edelsteinen";
	display_table($title, $sql);
} elseif ($_GET[op] == "birth") {
	
	if ($_GET[subop] == "least") {$adverb = "kürzesten";$order='DESC';}
	else {$adverb = "längsten";$order='ASC';}
	
	$sql = "SELECT name,birthday AS data1,DATEDIFF(NOW(),laston) AS data2, dragonkills AS data3 
			FROM accounts
			INNER JOIN account_extra_info USING(acctid) 
			WHERE birthday!='' ORDER BY data1 $order, data3 DESC LIMIT $limit";
	$res = db_query($sql);
	
	$arr = array();
	
	while($p = db_fetch_assoc($res)) {
		$p['data1'] = getgamedate($p['data1']);
		
		if($p['data2'] == 0) {$p['data2'] = 'Heute';}
		elseif($p['data2'] == 1) {$p['data2'] = 'Gestern';}
		else {$p['data2'] .= ' Tage';}
		$arr[] = $p;
	}
	
	$title = "Diese Krieger sind am $adverb im Dorf:";
	$headers = array('Ankunft','Zuletzt gesehen','Drachenkills');
	$tags = array('','');
	display_table($title, $arr, false, '', $headers, $tags);
} elseif ($_GET[op] == "treasure") {

	$sql = "SELECT accounts.name,treasure_f AS data1 FROM account_extra_info LEFT JOIN accounts ON accounts.acctid=account_extra_info.acctid WHERE treasure_f>0 ORDER BY data1 $order LIMIT $limit";

	if ($_GET[subop] == "least") $adverb = "wenigsten";
	else $adverb = "meisten";
	$title = "Diese Krieger haben die $adverb Schätze und Drachenreliquien gefunden:";
	$headers = array("Schätze");
	$tags = array("");
	display_table($title, $sql, false, '', $headers, $tags);
} elseif ($_GET[op] == "kerker") {

	$sql = "SELECT accounts.name,daysinjail AS data1 FROM account_extra_info LEFT JOIN accounts ON accounts.acctid=account_extra_info.acctid WHERE daysinjail>0 ORDER BY data1 $order LIMIT $limit";

	if ($_GET[subop] == "least") $adverb = "wenigsten";
	else $adverb = "meisten";
	$title = "Diese Krieger haben die $adverb Tage im Kerker gesessen:";
	$foot = "Es gelten nur die Tage die tatsächlich abgesessen wurden, nicht die Strafen";
	$headers = array("In Haft");
	$tags = array("Tage");
	display_table($title, $sql, false, $foot, $headers, $tags);

} elseif ($_GET[op] == "beer") {

	$sql = "SELECT accounts.name,beerspent AS data1 FROM account_extra_info LEFT JOIN accounts ON accounts.acctid=account_extra_info.acctid WHERE beerspent>0 ORDER BY data1 $order LIMIT $limit";

	if ($_GET[subop] == "least") $adverb = "wenigste";
	else $adverb = "meiste";
	$title = "Diese Krieger haben das $adverb Freibier spendiert:";
	$foot = "Auf ihr Wohl! Prost!";
	$headers = array("Freibier");
	$tags = array("Humpen");
	display_table($title, $sql, false, $foot, $headers, $tags);
	
} elseif ($_GET[op] == "beggar") {

	$sql = "SELECT accounts.name,beggar AS data1 FROM account_extra_info LEFT JOIN accounts ON accounts.acctid=account_extra_info.acctid WHERE beggar<>0 ORDER BY data1 $order LIMIT $limit";

	if ($_GET[subop] == "least") $adverb = "großzügigsten Spender";
	else $adverb = "gierigsten Bettler";
	$town=getsetting("townname","Atrahor")."s";
	$title = "Die $adverb ".$town.":";
	$foot = "(Hier erscheint was insgesamt vom Bettelstein genommen wurde.`n
    Negative Zahlen bedeuten, dass mehr gespendet als genommen wurde.)";
	$headers = array("entnommen");
	$tags = array("Gold");
	display_table($title, $sql, false, $foot, $headers, $tags);
	
} elseif ($_GET[op] == "symp") {

	$sql = "SELECT accounts.name,sympathy AS data1,dragonkills AS dks FROM account_extra_info LEFT JOIN accounts ON accounts.acctid=account_extra_info.acctid WHERE sympathy>0 ORDER BY data1 $order, dks $order LIMIT $limit";

	if ($_GET[subop] == "least") $adverb = "wenigsten";
	else $adverb = "meisten";
	$title = "Das sind die Helden mit $adverb Sympathiepunkten:";
	$headers = array("Sympathie");
	$tags = array("Punkte");
	display_table($title, $sql, false, false, $headers, $tags);
	
} elseif ($_GET[op] == "spoil") {

	$sql = "SELECT accounts.name,disciples_spoiled AS data1 FROM account_extra_info LEFT JOIN accounts ON accounts.acctid=account_extra_info.acctid WHERE disciples_spoiled>0 ORDER BY data1 $order LIMIT $limit";

	if ($_GET[subop] == "least") $adverb = "wenigsten";
	else $adverb = "meisten";
	$title = "Diese Krieger haben bislang die $adverb Knappen verheizt:";
	$foot = "Jünglinge, nehmt Euch in Acht!";
	$headers = array("Verloren");
	display_table($title, $sql, false, $foot, $headers, false);

} elseif ($_GET[op] == "beatenup") {

	$sql = "SELECT accounts.name,timesbeaten AS data1 FROM account_extra_info LEFT JOIN accounts ON accounts.acctid=account_extra_info.acctid WHERE timesbeaten>0 ORDER BY data1 $order LIMIT $limit";

	if ($_GET[subop] == "least") $adverb = "wenigste";
	else $adverb = "meiste";
	$title = "Diese Helden haben bislang die $adverb Prügel kassiert:";
	$foot = "(Es werden nur erfolgreiche Prügelattacken gezählt, bei denen die Angreifer nicht vertrieben wurden.)";
	$headers = array("Prügel");
	$tags = array("x vermöbelt");
	display_table($title, $sql, false, $foot, $headers, $tags);

} elseif ($_GET[op] == "runaway") {

	$sql = "SELECT accounts.name,runaway AS data1 FROM account_extra_info LEFT JOIN accounts ON accounts.acctid=account_extra_info.acctid WHERE runaway>0 ORDER BY data1 $order LIMIT $limit";

	if ($_GET[subop] == "least") $adverb = "Wenigsten";
	else $adverb = "Häufigsten";
	$title = "Diese Recken sind bislang am $adverb aus dem Kampf geflüchtet:";
	$foot = "(Es wird nur jeder erfolgreiche Fluchtversuch gewertet.)";
	$headers = array("davongelaufen");
	$tags = array("x geflohen");
	display_table($title, $sql, false, $foot, $headers, $tags);

} elseif ($_GET[op]=="weapon"){
	$sql = "SELECT name,weapon AS data1,weapondmg AS data2 FROM accounts WHERE locked=0 ".$max_su_punch." ORDER BY weapondmg $order, dragonkills $order, attack $order LIMIT $limit";
	$adverb = "mächtigsten";
	if ($_GET[subop] == "least") $adverb = "schlichtesten";
	$title = "Die $adverb Waffen in diesem Land";
	$headers = array("Waffe","Waffenstärke");
	display_table($title, $sql, false, false, $headers, false);

} elseif ($_GET[op]=="disciple"){
	$sql = "SELECT accounts.name,disciples.name AS data1,disciples.level AS data2 FROM disciples LEFT JOIN accounts ON accounts.acctid=disciples.master WHERE state>0 ORDER BY disciples.level $order, accounts.dragonkills $order LIMIT $limit";
	$adverb = "besten";
	if ($_GET[subop] == "least") $adverb = "unerfahrendsten";
	$title = "Diese Krieger haben die $adverb Knappen";
	$headers = array("Knappe","Level");
	display_table($title, $sql, false, false, $headers, false);

} elseif ($_GET[op]=="charm"){
	$sql = "SELECT name,$sexsel AS data1,$racesel AS data2 FROM accounts WHERE locked=0 ".$max_age.$max_su." ORDER BY charm $order, level $order, experience $order, acctid $order LIMIT $limit";
	$adverb = "schönsten";
	if ($_GET[subop] == "least") $adverb = "hässlichsten";
	$title = "Die $adverb Krieger in diesem Land.";
	$headers = array("<img src=\"images/female.gif\">/<img src=\"images/male.gif\">", "Rasse");
	display_table($title, $sql, false, false, $headers, false);
	
} elseif ($_GET[op]=="tough"){
	$sql = "SELECT name,level AS data2 ,$racesel as data1 FROM accounts WHERE locked=0 ".$max_age.$max_su." ORDER BY ((maxhitpoints/30)+(attack*1.5)+(defence)) $order, level $order, experience $order, acctid $order LIMIT $limit";
	$adverb = "stärksten";
	if ($_GET[subop] == "least") $adverb = "schwächsten";
	$title = "Die $adverb Krieger in diesem Land";
	$headers = array("Rasse", "Level");
	display_table($title, $sql, false, false, $headers, false);
}elseif ($_GET[op]=="punch"){
	$sql = "SELECT name,punch AS data1,$racesel AS data2 FROM accounts WHERE locked=0 ".$max_age.$max_su_punch." ORDER BY data1 $order, level $order, experience $order, acctid $order LIMIT $limit";
	$adverb = "härtesten";
	if ($_GET[subop] == "least") $adverb = "armseligsten";
	$title = "Die $adverb Schläge aller Zeiten";
	$headers = array("Punkte","Rasse");
	display_table($title, $sql, false, false, $headers, false);
} elseif ($_GET[op]=="resurrects"){
	$sql = "SELECT name,level AS data1 FROM accounts WHERE locked=0 ".$max_age.$max_su." ORDER BY resurrections $order, level $order, experience $order, acctid $order LIMIT $limit";
	$adverb = "tollpatschigsten";
	if ($_GET[subop] == "least") $adverb = "geschicktesten";
	$title = "Die $adverb Krieger in diesem Land";
	$headers = array("Level");
	display_table($title, $sql, false, false, $headers, false);
} elseif ($_GET[op]=="grave"){
	$sql = "SELECT name,deathpower,location,loggedin,laston,alive,activated FROM accounts WHERE locked=0 ".$max_age.$max_su." ORDER BY deathpower $order, level $order, experience $order, acctid $order LIMIT $limit";
	$adverb = "fleissigste";
	if ($_GET[subop] == "least") $adverb = "faulste";
	$title = "Ramius' $adverb Krieger";
	output("`c`b`^$title`0`b `7(Seite $page: $from-$to)`0`c`n");
	output('<table cellspacing="0" cellpadding="2" align="center"><tr class="trhead">',true);
	output("<td>`bRang`b</td><td>`bName`b</td><td>`bGefallen`b</td><td>`bOrt`b</td><td>`bStatus`b</td></tr>", true);
	$result = db_query($sql) or die(db_error(LINK));
	if (db_num_rows($result)==0){
		output('<tr class="trlight"><td colspan="5" align="center">`&Keine Spieler gefunden`0</td></tr>',true);
	} else {
		for ($i=0;$i<db_num_rows($result);$i++){
			$row = db_fetch_assoc($result);
			if ($row[name]==$session[user][name]){
				//output("<tr class='hilight'>",true);
				output("<tr bgcolor='#005500'>",true);
			} else {
				output('<tr class="'.($i%2?"trlight":"trdark").'">',true);
			}
			output("<td>".($i+$from).".</td><td>`&{$row[name]}`0</td><td align='right'>`){$row[deathpower]}`0</td><td>",true);
			$loggedin = user_get_online(0,$row);
			if ($row[location]==USER_LOC_FIELDS) output($loggedin?"`#Online`0":"`3Die Felder`0");
			if ($row[location]==USER_LOC_INN) output("`3Zimmer in Kneipe`0");
			if ($row[location]==USER_LOC_HOUSE) output("`3Im Haus`0");
			output("</td><td>",true);
			output($row[alive]?"`1Lebt`0":"`4Tot`0");
			output("</td></tr>",true);
		}
	}
	output("</table>", true);
} elseif ($_GET['op']=="days") {
	$order = "ASC";
	if ($_GET[subop] == "least") $order = "DESC";
	$sql = "SELECT accounts.name,bestdragonage AS data1, accounts.dragonkills FROM account_extra_info LEFT JOIN accounts ON accounts.acctid=account_extra_info.acctid WHERE bestdragonage>0 AND dragonkills>0 ORDER BY data1 $order LIMIT $limit";
	$adverb = "schnellsten";
	if ($_GET[subop] == "least") $adverb = "langsamsten";
	$title = "Helden mit den $adverb Drachenkills";
	$headers = array("Bestzeit Tage");
	$none = "Es gibt noch keine Helden in diesem Land";
	display_table($title, $sql, $none, false, $headers, false);
	
} elseif ($_GET['op']=="doll") {
	$order = "DESC";
	if ($_GET[subop] == "least") $order = "ASC";
	$sql = "SELECT accounts.name,hvalue AS data1 FROM items LEFT JOIN accounts ON accounts.acctid=items.owner WHERE items.tpl_id='kpuppe' ORDER BY data1 $order LIMIT $limit";
	$adverb = "wertvollsten";
	if ($_GET[subop] == "least") $adverb = "wertlosesten";
	$title = "Diese Sammler besitzen die $adverb Puppen";
	$headers = array("Wert der Puppe");
	$none = "Hier besitzt niemand eine Puppe.";
	$tags = array("DKs");
	display_table($title, $sql, $none, false, $headers, $tags);

} elseif ($_GET[op]=="battlepoints"){
	$sql = "SELECT name,battlepoints AS data1,dragonkills AS data2 FROM accounts WHERE locked=0 ".$max_age.$max_su." ORDER BY battlepoints $order, dragonkills $order, acctid $order LIMIT $limit";
	$adverb = "besten";
	if ($_GET[subop] == "least") $adverb = "schlechtesten";
	$title = "Die $adverb Arenakämpfer in diesem Land";
	$headers = array("Punkte","Drachenkills");
	display_table($title, $sql, false, false, $headers, false);
} elseif ($_GET[op]=="abwesend"){
	$sql = "SELECT name, DATEDIFF(NOW(),laston) AS data1,dragonkills AS data2 FROM accounts WHERE locked=0 AND DATEDIFF(NOW(),laston) > 3 AND dragonkills>0 ORDER BY data1 $order, dragonkills $order, acctid $order LIMIT $limit";
	$adverb = "am längsten";
	if ($_GET[subop] == "least") $adverb = "am kürzesten";
	$title = "Die $adverb Verschollenen in diesem Land";
	$headers = array("Tage","Drachenkills");
	display_table($title, $sql, false, false, $headers, false);
} elseif ($_GET[op]=="profs"){
	$arr_prof_list = array();
	
	$str_judges = '<tr class="trhead"><td>`bDie ehrenwerten Richter:`b</td></tr>';
	$str_priests = '<tr class="trhead"><td>`bDie würdigen Priester:`b</td></tr>';
	$str_guards = '<tr class="trhead"><td>`bDie tapferen Wachen:`b</td></tr>';
	$str_witches = '<tr class="trhead"><td>`bDie weisen Hexen und Hexer:`b</td></tr>';
	$str_txt = '';

	$sql = 'SELECT name, profession, sex, login FROM accounts WHERE profession > 0 ORDER BY profession DESC, dragonkills DESC, acctid ASC';
	$res = db_query($sql);
	while($a = db_fetch_assoc($res)) {
		// Wenn Beruf öffentlich angezeigt werden soll
		if($profs[$a['profession']][2]) {
			
			$biolink = 'bio.php?char='.rawurlencode($a['login']) . '&ret='.URLEncode($_SERVER['REQUEST_URI']);
			
			addnav('',$biolink);
			
			$str_txt = '<tr class="trlight"><td><a href="mail.php?op=write&to='.rawurlencode($a['login']).'" target="_blank" onClick="'.popup('mail.php?op=write&to='.rawurlencode($a['login'])).';return false;"><img src="images/newscroll.GIF" width="16" height="16" alt="Mail schreiben" border="0"></a>'.$profs[$a['profession']][3].$profs[$a['profession']][$a['sex']].' `0<a href="'.$biolink.'">`&'.$a['name'].'</a>`0</td></tr>';
			
			switch($a['profession']) {
												
				case PROF_JUDGE:
				case PROF_JUDGE_HEAD:
					$str_judges .= $str_txt;
					break;
					
				case PROF_GUARD:
				case PROF_GUARD_HEAD:
					$str_guards .= $str_txt;
					break;
					
				case PROF_PRIEST:
				case PROF_PRIEST_HEAD:
					$str_priests .= $str_txt;
					break;
					
				case PROF_WITCH:
				case PROF_WITCH_HEAD:
					$str_witches .= $str_txt;
					break;
			
			}
		}
	}
	
	output('`c`b`&Helden dieses Dorfes, die ein offizielles Amt innehaben:`c`b`n');

	$out = '`c<table cellspacing="2" cellpadding="2" align="center">';
	
	$out .= $str_judges.$str_priests.$str_witches.$str_guards;
		
	$out .= '</table>`c';

	output($out,true);

}else if ($_GET[op]=="paare"){
	output("In einem Nebenraum der Ruhmeshalle findest du eine Liste mit Helden ganz anderer Art. Diese Helden Meistern gemeinsam die Gefahren der Ehe!`n`n");
	$sql = "SELECT acctid,name,marriedto FROM accounts WHERE sex=0 AND charisma=4294967295 ORDER BY acctid DESC";
	output("`c`b`&Heldenpaare dieser Welt`b`c`n");
	output("<table cellspacing=0 cellpadding=2 align='center'><tr><td><img src=\"images/female.gif\">`b Name`b</td><td></td><td><img src=\"images/male.gif\">`b Name`b</td></tr>",true);
	$result = db_query($sql) or die(db_error(LINK));
	if (db_num_rows($result)==0){
		output("<tr><td colspan=4 align='center'>`&`iIn diesem Land gibt es keine Paare`i`0</td></tr>",true);
	}
	for ($i=0;$i<db_num_rows($result);$i++){
		$row = db_fetch_assoc($result);
		$sql2 = "SELECT name FROM accounts WHERE acctid=".$row[marriedto]."";
		$result2 = db_query($sql2) or die(db_error(LINK));
		$row2 = db_fetch_assoc($result2);
		output("<tr class='".($i%2?"trlight":"trdark")."'><td>`&$row2[name]`0</td><td>`) und `0</td><td>`&",true);
		output("$row[name]`0</td></tr>",true);
	}
	output("</table>",true);
} else {
	$sql = "SELECT name,dragonkills AS data1,level AS data2,'&nbsp;' AS data3, IF(dragonage,dragonage,'Unknown') AS data4, '&nbsp;' AS data5, IF(account_extra_info.bestdragonage,account_extra_info.bestdragonage,'Unknown') AS data6 FROM accounts LEFT JOIN account_extra_info ON account_extra_info.acctid=accounts.acctid WHERE dragonkills>0 AND locked=0 ".$max_age.$max_su." ORDER BY dragonkills $order,level $order,experience $order, accounts.acctid $order LIMIT $limit";
	$adverb = "meisten";
	if ($_GET[subop] == "least") $adverb = "wenigsten";
	$title = "Helden mit den $adverb Drachenkills";
	$headers = array("Kills", "Level", "&nbsp;", "Tage", "&nbsp;", "Bestzeit");
	$none = "Es gibt noch keine Helden in diesem Land";
	display_table($title, $sql, $none, false, $headers, false);
}

// $sql = "SELECT accounts.name,bestdragonage AS data1, accounts.dragonkills FROM account_extra_info LEFT JOIN accounts ON accounts.acctid=account_extra_info.acctid WHERE bestdragonage>0 AND dragonkills>0 ORDER BY data1 $order LIMIT $limit";


page_footer();
?>
