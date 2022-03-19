<?php

// Rohger�st f�r ein sp�teres Add-on
// by Maris (Maraxxus@gmx.de)
// �nderungen: Rohger�st aufgef�llt

// Schl�ssel f�r DDL-location
// --------------------------
// 0 : Atrahor
// 1 : Zeltlager (Hauptplatz)
// 2 : Expeditionsleiter
// 3 : Gemeinschaftszelt
// 4 : Lagerarzt
// 5 : Lagerwache
// 6 : Hei�e Quellen
// 7 : Ein�de
// 8 : Tropfsteinh�hle
// 9 : Unterwegs
// 10 : Im Zelt
// 11 : Antreteplatz

require_once 'common.php';
addcommentary(false);
checkday();

$xstate = getsetting("DDL-state",6);
if ($xstate==1)
{
page_header('Expedition in die dunklen Lande');
output("`4Das Lager wurde vollkommen gepl�ndert und zerst�rt. Nur noch Tr�mmer und verkohltes Holz erinnern daran, dass hier einmal ein stolzer Au�enposten ".getsetting('townname','Atrahor')."s stand. Du schwelgst in kurzen wehm�tiger Erinnerung an bessere Zeiten, bevor du dich wieder auf dein
Reittier setzt und dich traurig zur�ck zur Stadt begibst...");
if (su_check(SU_RIGHT_EXPEDITION_ADMIN))
{
addnav('Mod-Aktionen');
addnav('Zustand erh�hen','expedition.php?op=risestate');
addnav('Zustand senken','expedition.php?op=lowerstate');
addnav('Zur�ck');
}
addnav('Zur�ck nach '.getsetting('townname','Atrahor'),'village.php');
}
else
{
if ($session['user']['alive']==0)
{
	redirect('shades.php');
}

$session['user']['specialinc']='';
$session['user']['specialmisc']='';

function get_DDL_location($location)
{
  switch ($location)
    {
      case 1 :
      $text="`&Zeltlager`0";
      break;
      case 2 :
      $text="`&Expeditionsleiter`0";
      break;
      case 3 :
      $text="`&Gemeinschaftszelt`0";
      break;
      case 4 :
      $text="`&Lagerarzt`0";
      break;
      case 5 :
      $text="`&Lagerwache`0";
      break;
      case 6 :
      $text="`&Hei�e Quellen`0";
      break;
      case 7 :
      $text="`&Ein�de`0";
      break;
      case 8 :
      $text="`&Tropfsteinh�hle`0";
      break;
      case 9 :
      $text="`&Unterwegs`0";
      break;
      case 10 :
      $text="`&In einem Privatzelt`0";
      break;
      case 11 :
      $text="`&Antreteplatz`0";
      break;
    }
return($text);
}

switch ($_GET[op]) {
case 'whosthere' :

$where = $_GET['where'];

$session['user']['ddl_location'] = $where;

page_header('Expedition in die dunklen Lande');

if ($where==1)
{
output('`2Folgende Helden befinden sich gerade mit dir in den R�umen der Expedition`n`n');
$sql = "SELECT name,level,login,loggedin,dragonkills,sex,DDL_location FROM accounts WHERE DDL_location>0 AND loggedin=1 ORDER BY dragonkills DESC, level DESC LIMIT 50";
}
else
{
$DDL_location=get_DDL_location($where);
output('`2Anwesende im Raum '.$DDL_location.':`n`n');
$sql = "SELECT name,level,login,loggedin,dragonkills,sex,DDL_location FROM accounts WHERE DDL_location=$where AND loggedin=1 ORDER BY dragonkills DESC, level DESC LIMIT 50";
}

$result = db_query($sql) or die(db_error(LINK));
output("<table border=0 cellpadding=2 cellspacing=1 bgcolor='#999999'>",true);
output("<tr class='trhead'><td><b>DKs</b><td><b>Level</b></td><td><b>Name</b></td><td><b><img src=\"images/female.gif\">/<img src=\"images/male.gif\"></b>",true);
if ($where==1)
{
output("<td><b>Wo?</b></tr>",true);
}
else
{
output("</tr>",true);
}
$max = db_num_rows($result);

for($i=0;$i<$max;$i++){
    $row = db_fetch_assoc($result);
    output("<tr class='".($i%2?"trdark":"trlight")."'><td>",true);
    output("`^$row[dragonkills]`0</td><td>",true);
    output("`^$row[level]`0</td><td>",true);
    if ($session[user][loggedin]) output("<a href=\"mail.php?op=write&to=".rawurlencode($row['login'])."\" target=\"_blank\" onClick=\"".popup("mail.php?op=write&to=".rawurlencode($row['login'])."").";return false;\"><img src='images/newscroll.GIF' width='16' height='16' alt='Mail schreiben' border='0'></a>",true);
    if ($session[user][loggedin]) output("<a href='bio.php?char=".rawurlencode($row['login'])."&ret=".URLEncode($_SERVER['REQUEST_URI'])."'>",true);
    if ($session[user][loggedin]) addnav("","bio.php?char=".rawurlencode($row['login'])."&ret=".URLEncode($_SERVER['REQUEST_URI'])."");
    output("$row[name]`0");
    if ($session[user][loggedin]) output("</a>",true);
    output("</td><td align=\"center\">",true);
    output($row[sex]?"<img src=\"images/female.gif\">":"<img src=\"images/male.gif\">",true);
    output("</td><td>",true);
if ($where==1)
    {
    $DDL_location=get_DDL_location($row['DDL_location']);
    output($DDL_location,true);
    output("</td><td>",true);
    }
}
output("</table>",true);
$return = preg_replace("'[&?]c=[[:digit:]-]+'","",$_GET[ret]);
$return = substr($return,strrpos($return,"/")+1);
addnav("Zur�ck",$return);
break;

case 'explore' :
$session['user']['ddl_location'] = 9;

page_header('Expedition in die dunklen Lande - Erkundung');
output('`2Gut versteckt hinter ein paar Str�uchern kannst du bei genauem Hinsehen einen kleinen, fast vollkommen zugewachsenen Pfad erkennen.
        M�hsam k�mpfst du dich durch das Gestr�pp und nimmst die kleinen Kratzer in Kauf, um zu sehen, was dich so angezogen hat: unz�hlige Pflanzen, Farne und sogar Pilze, die du noch nie zu Gesicht bekommen hast.
        Ab und an huschen auch sehr sonderbare Tiere vorbei. Neugierig aber doch mit leichtem Unbehagen beschlie�t du, dem Pfad zu folgen.
        Dir wird langsam klar, umso l�nger du diesem Weg folgen wirst, umso mehr neue Gesch�pfe und Pflanzen wirst du sehen und so Erfahrungen sammeln, wie du sie im Dorf niemals erlangen k�nntest.`n');
    if ($session[user][turns] < 1){
		output("`n`n`2Du hast nicht mehr die Kraft, heute noch auf Erkundungstour zu gehen!");
	}else{
		output("`2Wie lange willst du erkunden gehen?`n");
		output("<form action='expedition.php?op=explore2' method='POST'><input name='eround' id='eround'><input type='submit' class='button' value='Erkunden gehen'></form>",true);
		output("<script language='JavaScript'>document.getElementById('eround').focus();</script>",true);
		addnav("","expedition.php?op=explore2");
	}

addnav('Zur�ck');
addnav('Zum Zeltlager','expedition.php');
break;

case 'explore2' :
$session['user']['ddl_location'] = 9;
page_header('Expedition in die dunklen Lande - Auf Erkundung');
	$eround = abs((int)$HTTP_GET_VARS[eround] + (int)$HTTP_POST_VARS[eround]);
if ($session[user][turns] <= $eround){
	$eround = $session[user][turns];
    }
	
$session[user][turns]-=$eround;
$exp = (($session[user][level]*0.4)+2)*e_rand(10,20)+e_rand(5,10);
$totalexp = (int)($exp*$eround);
$session[user][experience]+=$totalexp;
output("`2Du kommst von deinem abenteuerlichen Ausflug zur�ck und f�hlst dich deutlich erfahrener!`n");
output("`2Du hast `^".$totalexp."`2 Erfahrung bekommen!`n");
debuglog('Hat die Erkundung genutzt um Erfahrung zu sammeln');

addnav('Zur�ck');
addnav('Zum Zeltlager','expedition.php');
break;

case 'search' :
$session['user']['ddl_location'] = 9;
page_header('Expedition in die dunklen Lande - Schatzsuche');
output('`2Jemand im Gemeinschaftszelt hat dir zugefl�stert, dass es in der Landschaft rund um die Expedition herum kleine Sch�tze und Annehmlichkeiten zu finden geben soll. Du findest das nur mehr als gerecht, wenn du schon so weit von deiner gewohnten Umgebung bist. Du versuchst dich aus dem Lager zu schleichen, als alle anderen schlafen und bist auf der Suche nach den Kostbarkeiten.
Hier wirst du zwar keine Monster antreffen, allerdings wird deine Suche derart lange dauern, dass du eine Runde verlierst!`n');
addnav('Aktionen');
addnav('Sch�tze suchen','expedition.php?op=search2');
addnav('Zur�ck');
addnav('Zum Zeltlager','expedition.php');
break;

case 'search2' :
$session['user']['ddl_location'] = 9;
page_header('Expedition in die dunklen Lande - Schatzsuche');
if ($session['user']['turns']>0)
{
$findit=e_rand(1,25);
		
if ($findit == 2)
        { //gem
			output('`^Du findest EINEN EDELSTEIN!`n`&');
			$session['user']['gems']++;
		}

elseif ($findit == 4)
        { //donation
            output('`&Du findest zwar keinen Scatz, aber die G�tter meinen es gut mit dir und gew�hren dir `^2 Donation Punkte`&.');
            $session['user']['donation']+=2;
        }

elseif ($findit == 6)
		{ // item
		
			// item
			$item_hook_info['min_chance'] = e_rand(1,255);
			
			// Beutebuff
			if($session['bufflist']['beutegeier']) {
				output($session['bufflist']['beutegeier']['effectmsg'].'`n');
				$item_hook_info['min_chance'] = max($item_hook_info['min_chance']-$session['bufflist']['beutegeier']['chancemin'],1);				
				$session['bufflist']['beutegeier']['rounds']--;
				if($session['bufflist']['beutegeier']['rounds'] <= 0) {
					output($session['bufflist']['beutegeier']['wearoff'].'`n');
					unset($session['bufflist']['beutegeier']);
				}
			}
					
			$res = item_tpl_list_get( 'find_forest>='.$item_hook_info['min_chance'] , 'ORDER BY RAND() LIMIT 1' );
			
			if( db_num_rows($res) ) {
				
				$item = db_fetch_assoc($res);
				
				if( $item['find_forest_hook'] != '' ) {
					item_load_hook( $item['find_forest_hook'] , 'find_forest' , $item );
				}
				else {				
					if ( item_add( $session['user']['acctid'], 0, true, $item ) ) {
						output('`&Du hast das Beutest�ck `q'.$item['tpl_name'].'`& gefunden! ('.$item['tpl_description'].')!`n`n`&');
					}
				}
											
			}
					
		}
elseif ($findit == 8 || $findit == 9)
        { // bone
		
			item_add($session['user']['acctid'],'abgenknch');

          output('`&Du hast einen `qabgenagten Knochen`& ausgebuddelt...`n`n`&');
        }
		
elseif ($findit == 10 && e_rand(1,4)==2)
		{ // armor
			$sql = 'SELECT * FROM armor WHERE defense<='.($session['user']['level']+5).' ORDER BY rand('.e_rand().') LIMIT 1';
			$result2 = db_query($sql) or die(db_error(LINK));
			if (db_num_rows($result2)>0)
			{
				$row2 = db_fetch_assoc($result2);
				$row2['value']=round($row2['value']/10);
												
				$item['tpl_name'] = addslashes($row2['armorname']);
				$item['tpl_value1'] = addslashes($row2['defense']);
				$item['tpl_gold'] = addslashes($row2['value']);
				$item['tpl_description'] = 'Gebrauchte Level '.$row2['level'].' R�stung mit '.$row2['defense'].' Verteidigung.';
				
				item_add($session['user']['acctid'],'rstdummy',true,$item);
				
				output('`n`&Du findest die R�stung `%'.$row2['armorname'].'`&!`n`n`#');
			}
		}
elseif ($findit == 12 && e_rand(1,4)==2)
		{ // weapon
			$sql = 'SELECT * FROM weapons WHERE damage<='.($session['user']['level']+5).' ORDER BY rand('.e_rand().') LIMIT 1';
			$result2 = db_query($sql) or die(db_error(LINK));
			if (db_num_rows($result2)>0)
			{
				$row2 = db_fetch_assoc($result2);
				$row2['value']=round($row2['value']/10);
				
				$item['tpl_name'] = addslashes($row2['weaponname']);
				$item['tpl_value1'] = addslashes($row2['attack']);
				$item['tpl_gold'] = addslashes($row2['value']);
				$item['tpl_description'] = 'Gebrauchte Level '.$row2['level'].' Waffe mit '.$row2['attack'].' Angriff.';
				
				item_add($session['user']['acctid'],'waffedummy',true,$item);
				output('`n`&Du findest die Waffe `%'.$row2['weaponname'].'`Q!`n`n`#');
			}
		}

elseif($findit == 18 && e_rand(1,5) == 5)
            { // antidote
			output("`6Du findest den seltenen Shurisa-Pilz, der eine starke Gift neutralisierende Wirkung hat. Du z�gerst keinen Moment seinen Saft zu gewinnen und erzeugst somit eine Phiole Truhenfallen-Antiserum!`n`n");
			
			item_add($session['user']['acctid'],'antiserum');
						
	    	}
else        {
              addnav('Aktionen');
              addnav('Nochmal!','expedition.php?op=search2');
              output('`&Leider hast du auf deiner Suche nichts von Wert gefunden...`n');
            }
$session['user']['turns']--;
}
else
{
  output ('`2Heute nicht mehr, du f�hlst dich einfach zu m�de.');
}
addnav('Zur�ck');
addnav('Zum Zeltlager','expedition.php');
break;

case 'claim' :
$session['user']['ddl_location'] = 9;
page_header('Expedition in die dunklen Lande - Gel�nde auskundschaften');
output('`6Wie alle anderen wei�t auch du, dass die Umgebung um das Lager herum sehr unfruchtbar ist. Umso mehr Belohnung haben die Leiter der Expedition ausgesetzt, sollte ein Teilnehmer einen rohstoffreichen Boden oder nutzbares Land finden. Du bist fest davon �berzeugt, dass du eine solche Quelle an Rohstoffen findest und machst dich sofort auf den Weg um der gesamten Expedition weiterzuhelfen und nat�rlich auch um das Gold einstecken zu k�nnen. Doch kurz nachdem du angefangen hast zu graben, wird dir klar, wie schwer diese Arbeit ist, sodass du heute sicher keinen Fu� mehr in das Verlassene Schloss setzen k�nntest.`n`n
Ganz am Anfang hat dir die Expeditionsleitung mehrere Gebiete auf der Karte gezeigt, die noch niemand untersucht hat. Allerdings wird vermutet, dass im Buschland am meisten zu finden ist. Dementsprechend gering wird hier deine Belohnung ausfallen. Dagegen sind die Leiter sich sicher, dass die Felsenw�ste fast keinen Nutzen f�r die Expedition hat. Solltest du dort wirklich etwas finden, werden sie dir sicher mehr Gold und Edelsteine �berreichen.`n');
addnav('Auskundschaften');
addnav('Buschland','expedition.php?op=claim2&what=1');
addnav('Sumpf','expedition.php?op=claim2&what=2');
addnav('Steppe','expedition.php?op=claim2&what=3');
addnav('Felsenw�ste','expedition.php?op=claim2&what=4');
addnav('Zur�ck');
addnav('Zum Zeltlager','expedition.php');
break;

case 'claim2' :
$session['user']['ddl_location'] = 9;
page_header('Expedition in die dunklen Lande - Bodenanalyse');
if ($session['user']['castleturns']>0)
{
$what=$_GET['what'];
  switch ($what)
  {
    case '1':
    $limit=80;
    $gold=1000;
    $gems=0;
    $text="`2Du hast brauchbares Weideland entdeckt!`0`n";
    break;
    case '2':
    $limit=60;
    $gold=1500;
    $gems=1;
    $text="`2Du hast Torfvorkommen entdeckt!`0`n";
    break;
    case '3':
    $limit=40;
    $gold=4000;
    $gems=2;
    $text="`2Du hast fruchtbare Ackerfl�che entdeckt!`0`n";
    break;
    case '4':
    $limit=20;
    $gold=10000;
    $gems=8;
    $text="`2Du hast Goldvorkommen entdeckt!`0`n";
    break;
  }
$chance=e_rand(1,100);
if ($chance<=$limit)
{
output ('`@Gl�ckwunsch!`n'.$text);
output ('`2Der Expeditionsleiter ist mit deiner Leistung derart zufrieden, dass er dir eine `@Belohnung von '.$gold.' Gold und '.$gems.' Edelsteinen `2�berreicht!`n`n');
$session['user']['gold']+=$gold;
$session['user']['gems']+=$gems;
} else output('`2Nach dem deine Arbeiten beendet sind musst du feststellen, dass dieses St�ck Land vollkommen unbrauchbar ist.`n');
}
else output ('`2Du kannst heute keine Analyse mehr durchf�hren!`n');
$session['user']['castleturns']--;
addnav('Zur�ck');
addnav('Zum Zeltlager','expedition.php');
break;

case 'letter' :
$session['user']['ddl_location'] = 9;
page_header('Expedition in die dunklen Lande - Brief schreiben');
output("`rDu �berlegst dir, deine".($session[user][sex]?"m Liebsten":"r Liebsten")." einen romantischen Brief aus der Ferne zu schicken und ih".($session[user][sex]?"m":"r")." auf diesem Wege deine Gef�hle zu gestehen. Durch diese Umst�nde spielt es keinerlei Rolle mehr, ob einer von euch beiden mehr Charme besitzt als der andere.`n`n");
if ($_GET[act]==""){
	if ($session[user][seenlover]){
  		$sql = "SELECT name FROM accounts WHERE locked=0 AND acctid=".$session[user][marriedto]."";
  		$result = db_query($sql) or die(db_error(LINK));
		$row = db_fetch_assoc($result);
		$partner=$row[name];
		if ($partner=="") $partner = $session[user][sex]?"`^Seth`0":"`5Violet`0";
		output("Du versuchst, deinen Brief in Gedanken zu formulieren, aber irgendwie bekommst du den Kopf nicht frei. Vielleicht solltest du bis morgen warten.");
 	} else {
		if (isset($_POST['search']) || $_GET['search']>""){
			if ($_GET['search']>"") $_POST['search']=$_GET['search'];
			$search="%";
			for ($x=0;$x<strlen($_POST['search']);$x++){
				$search .= substr($_POST['search'],$x,1)."%";
			}
			$search="name LIKE '".$search."' AND ";
		}else{
			$search="";
		}
		$ppp=25; // Player Per Page to display
		if (!$_GET[limit]){
			$page=0;
		}else{
			$page=(int)$_GET[limit];
			addnav("Vorherige Seite","expedition.php?op=letter&limit=".($page-1)."&search=$_POST[search]");
		}
		$limit="".($page*$ppp).",".($ppp+1);
		if ($session[user][marriedto]==4294967295) output("Du denkst nochmal �ber deine Ehe mit ".($session[user][sex]?"`^Seth`0":"`5Violet`0")." nach und �berlegst, ob du ".($session[user][sex]?"ihn":"sie")." in der Kneipe besuchen sollst, oder f�r wen du diese Ehe aufs Spiel setzen w�rdest.`n");
		if($session[user][charme]==4294967295) output("Du �berlegst dir, dass du dir mal wieder etwas Zeit f�r ".($session[user][sex]?"deinen Mann":"deine Frau")." nehmen solltest. W�hrend du ".($session[user][sex]?"ihn":"sie")." im Garten suchst, stellst du aber fest, dass der Rest der ".($session[user][sex]?"M�nner":"Frauen")." hier auch nicht zu verachten ist.`n");
		output("F�r wen entscheidest du dich?`n`n");
		output("<form action='expedition.php?op=letter' method='POST'>Nach Name suchen: <input name='search' value='$_POST[search]'><input type='submit' class='button' value='Suchen'></form>",true);
		addnav("","expedition.php?op=letter");
  		$sql = "SELECT acctid,name,sex,level,race,login,marriedto,charisma FROM accounts WHERE
		$search
		(locked=0) AND
		(sex <> ".$session[user][sex].") AND
		(alive=1) AND
		(acctid <> ".$session[user][acctid].") AND
		(laston > '".date("Y-m-d H:i:s",strtotime(date("r")."-346000 sec"))."' OR (charisma=4294967295 AND acctid=".$session[user][marriedto].") )
		ORDER BY (acctid=".$session['user']['marriedto'].") DESC, charm DESC LIMIT $limit";
  		$result = db_query($sql) or die(db_error(LINK));
		output("<table border='0' cellpadding='3' cellspacing='0'><tr><td>",true);
		output(($session[user][sex]?"<img src=\"images/male.gif\">":"<img src=\"images/female.gif\">")."</td><td><b>Name</b></td><td><b>Level</b></td><td><b>Rasse</b></td><td><b>Status</b><td><b>Ops</b></td></tr>",true);
		if (db_num_rows($result)>$ppp) addnav("N�chste Seite","expedition.php?op=letter&limit=".($page+1)."&search=$_POST[search]");
		for ($i=0;$i<db_num_rows($result);$i++){
			$row = db_fetch_assoc($result);
	  		$biolink="bio.php?char=".rawurlencode($row[login])."&ret=".urlencode($_SERVER['REQUEST_URI']);
	  		addnav("", $biolink);
			if ($session[user][charisma]<=$row[charisma]) $flirtnum=$session[user][charisma];
			if ($row[charisma]<$session[user][charisma]) $flirtnum=$row[charisma];
	  		output("<tr class='".($i%2?"trlight":"trdark")."'><td></td><td>$row[name]</td><td>$row[level]</td><td>",true);
			output($colraces[$row['race']]);
			output("</td><td>",true);
			if ($session[user][acctid]==$row[marriedto] && $session[user][marriedto]==$row[acctid]){
				if ($session[user][charisma]==4294967295 && $row[charisma]==4294967295){
					output("`@`bDein".($session[user][sex]?" Mann":"e Frau")."!`b`0");
				}else if ($flirtnum==999){
					output("`\$Heiratsantrag!`0");
				} else {
					output("`^$flirtnum von ".$session[user][charisma]." Flirts erwidert!`0");
				}
			} else if ($session[user][acctid]==$row[marriedto]) {
				output("Flirtet ".$row[charisma]." mal mit dir");
			} else if ($session[user][marriedto]==$row[acctid]) {
				output("Deine letzten ".$session[user][charisma]." Flirts");
			} else if ($row[marriedto]==4294967295 || $row[charisma]==4294967295){
				output("`iVerheiratet`i");
			} else {
				output("-");
			}
			output("</td><td>[ <a href='$biolink'>Bio</a> | <a href='expedition.php?op=letter&act=flirt_msg&name=".rawurlencode($row[login])."'>Schreiben</a> ]</td></tr>",true);
			addnav("","expedition.php?op=letter&act=flirt_msg&name=".rawurlencode($row[login]));
		}
		output("</table>",true);
	}
	
} else if ($_GET[act]=="flirt_msg"){
$link = 'expedition.php?op=letter&act=flirt&name='.$_GET['name'];
addnav('',$link);

output("`rDu kannst hier deinen Brief selbst verfassen. M�chtest du das nicht, lasse das Feld einfach frei.`n`n");
output("<form action='".$link."' method='POST'>",true);
output("Dein Brief: <input type='text' name='message' size='100' maxlength='500'>`n`n",true);
output("<input type='submit' class='button' value='Abschicken!'></form>",true);

} else if ($_GET[act]=="flirt"){
$buff = array("name"=>"`!Sehnsucht","rounds"=>60,"wearoff"=>"`!Du vermisst deine gro�e Liebe!`0","defmod"=>1.2,"roundmsg"=>"Deine gro�e Liebe l�sst dich an deine Sicherheit denken!","activate"=>"defense");
$message = $_POST['message'];
if($message != '')
{ $more = "`n`nDer Brief:`n".$message; }

 	$sql = "SELECT acctid,name,experience,charm,charisma,lastip,emailaddress,race,marriedto,uniqueid FROM accounts WHERE login=\"$_GET[name]\"";
	$result = db_query($sql) or die(db_error(LINK));
	if (db_num_rows($result)>0){
		$row = db_fetch_assoc($result);
		if ($session['user']['acctid']==$row['marriedto'] && $session['user']['marriedto']==$row['acctid']) {
		if ($session[user][charisma]<=$row[charisma]) $flirtnum=$session[user][charisma];
		if ($row[charisma]<$session[user][charisma]) $flirtnum=$row[charisma];	// gegens. Flirts
        } else { $flirtnum=0; }
        if (($session[user][marriedto]==4294967295 || $session[user][charisma]==4294967295) && ($row[marriedto]==4294967295 || $row[charisma]==4294967295)) { //beide verheiratet
			if ($session[user][marriedto]==$row[acctid] && $session[user][acctid]==$row[marriedto]){
                // miteinander
                output("`%Du schreibst ".($session[user][sex]?"deinen Mann":"deine Frau")." `6$row[name]`% einen wahnsinnig romantischen Liebesbrief und hoffst dass sich der Bote beeilt. ");
				output("`nDu bekommst einen Charmepunkt.");
				$session['bufflist']['lover']=$buff;
				$session['user']['charm']++;
				$session['user']['seenlover']=1;
                systemmail($row['acctid'],"`%Ein Brief!`0","`&{$session['user']['name']}`6 hat dir einen sehnsuchtsvollen Brief aus den fernen, dunklen Landen geschrieben.".$more);
            } else switch(e_rand(1,4)) {
                // mit wem anders
                case 1 :
                case 2 :
                case 3 :
				output("`%Du schreibst `6$row[name]`% einen romantischen Brief aus der Ferne. Da ihr beide verheiratet seid, hoffst du das dieser Brief nicht in falsche H�nde ger�t.");
				$session['user']['seenlover']=1;
				systemmail($row['acctid'],"`%Ein Brief!`0","`&{$session['user']['name']}`6 hat dir einen sehnsuchtsvollen Brief aus den fernen, dunklen Landen geschrieben. Da du wei�t wie riskant es ist, dass dein Partner diesen Brief zu sehen bekommt l�sst du ihn nach mehrmaligem Lesen schnell wieder verschwinden.".$more);
				break;
				case 4:
				output("`%Du schreibst `6$row[name]`% einen romantischen Brief aus der Ferne. Da ihr beide verheiratet seid, hoffst du das dieser Brief nicht in falsche H�nde ger�t.");
                output("Leider jedoch hat der Bote deinen Brief f�lschlicherweise zu ".($session[user][sex]?"deinem Mann":"deiner Frau")." gebracht.`nDie Situation war sofort klar.`0`n`n".($session[user][sex]?"Dein Mann":"Deine Frau")." verl�sst dich!");
				systemmail($session[user]['marriedto'],"`\$Scheidung!`0","`6Du hast einen Liebesbrief von `&{$session['user']['name']}`6 zugestellt bekommen, der eigentlich f�r `&{$row[name]}
 bestimmt war und verl�sst ".($session[user][sex]?"sie":"ihn").".");
                $sql = "UPDATE accounts SET marriedto=0,charisma=0 WHERE acctid='{$session['user']['marriedto']}'";
	            db_query($sql);
                $session[user][marriedto]=$row[acctid];
	            $session[user][charisma]=1;
	            $session['user']['seenlover']=1;
	            systemmail($row['acctid'],"`%Ein Brief!`0","`&{$session['user']['name']}`6 hat dir einen sehnsuchtsvollen Brief aus den fernen, dunklen Landen geschrieben. Leider hat ihn der schusselige Bote ".($session[user][sex]?"ihrem Mann �bergeben, der ":"seiner Frau �bergeben, der ")." sich nat�rlich sofort scheiden lie�. Jedoch hat ".($session[user][sex]?"ihr Ex-Mann ":"seine Ex-Frau ")."dir diesen Brief wortlos zukommen lassen.".$more);
                break;
              }
		} else if ($session[user][marriedto]==4294967295 || $session[user][charisma]==4294967295) {
              // Sender verheiratet
              if ($session[user][marriedto]==4294967295 && $session[user][charisma]>=5){
              // Mit Seth/Violet
				output("`%Du schreibst `6$row[name]`% einen romantischen Brief aus der Ferne. ".($session[user][sex]?"Seth":"Violet")." f�ngt jedoch diesen Brief ab und verl�sst dich.`nWarscheinlich hatte ".($session[user][sex]?"er":"sie")." dich schon l�nger in Verdacht...");
				$session[user][marriedto]=$row[acctid];
				$session['user']['seenlover']=1;
	     		} else {
				switch(e_rand(1,4)){
					case 1:
					case 2:
					case 3:
					output("`%Du schreibst `6$row[name]`% einen romantischen Brief aus der Ferne. Da du jedoch verheiratet bist, hoffst du das dieser Brief nicht in falsche H�nde ger�t.");
					systemmail($row['acctid'],"`%Ein Brief!`0","`&{$session['user']['name']}`6 hat dir einen sehnsuchtsvollen Brief aus den fernen, dunklen Landen geschrieben.".$more);
					$session['user']['seenlover']=1;
					break;
					case 4:
					output("`%Du schreibst `6$row[name]`% einen romantischen Brief aus der Ferne. Da du jedoch verheiratet bist, hoffst du das dieser Brief nicht in falsche H�nde ger�t.");
					output("Leider jedoch hat der Bote deinen Brief f�lschlicherweise zu ".($session[user][sex]?"deinem Mann":"deiner Frau")." gebracht.`nDie Situation war sofort klar.`0`n`n".($session[user][sex]?"Dein Mann":"Deine Frau")." verl�sst dich!");
					if ($session[user][charisma]==4294967295){
						$sql = "UPDATE accounts SET marriedto=0,charisma=0 WHERE acctid='{$session['user']['marriedto']}'";
						db_query($sql);
						systemmail($session[user]['marriedto'],"`\$Scheidung!`0","`6Du hast einen Liebesbrief von `&{$session['user']['name']}`6 zugestellt bekommen, der eigentlich f�r `&{$row[name]}
 bestimmt war und verl�sst ".($session[user][sex]?"sie":"ihn").".");
						
					}
					$session[user][marriedto]=$row[acctid];
					$session[user][charisma]=1;
					$session['user']['seenlover']=1;
					systemmail($row['acctid'],"`%Ein Brief!`0","`&{$session['user']['name']}`6 hat dir einen sehnsuchtsvollen Brief aus den fernen, dunklen Landen geschrieben. Leider hat ihn der schusselige Bote ".($session[user][sex]?"ihrem Mann �bergeben, der ":"seiner Frau �bergeben, der ")." sich nat�rlich sofort scheiden lie�. Jedoch hat ".($session[user][sex]?"ihr Ex-Mann ":"seine Ex-Frau ")."dir diesen Brief wortlos zukommen lassen.".$more);
					break;
				}
			}
		} else if ($row[marriedto]==4294967295 || $row[charisma]==4294967295) { 
          // Empf�nger verheiratet
			if ($session[user][marriedto]==$row[acctid]){
              // Nur Empf�nger verheiratet
				$session['user']['seenlover']=1;
				output("`%Du schon schreibst $row[name] `%einen romantischen Brief aus der Ferne, wei�t aber, dass er wohl nie beantwortet wird, da $row[name]`% verheiratet ist.");
			} else {
                output("`%Du schreibst `6$row[name]`% einen romantischen Brief aus der Ferne.");
				$session[user][charisma]=1;
				$session['user']['seenlover']=1;
			}
			systemmail($row['acctid'],"`%Ein Brief!`0","`&{$session['user']['name']}`6 hat dir einen sehnsuchtsvollen Brief aus den fernen, dunklen Landen geschrieben.".$more);
			$session[user][marriedto]=$row[acctid];
		} else { 
            // Singles ?
			if ($session[user][marriedto]==$row[acctid]){
              // Empf�nger ist Flitpartner des Senders
				if ($flirtnum>=5){
                  // Verlobung
					if($session['user']['charisma']!=999) {

						$session['user']['charisma']=999;
						$sql = "UPDATE accounts SET charisma='999' WHERE acctid='$row[acctid]'";
						db_query($sql);
						output("`&Der heutige Brief ist etwas Besonderes! Du fasst all deinen Mut zusammen `n");
						output("und schw�rst ".$row['name']."`& dass du ".($session[user][sex]?"sie ":"ihn ")."nach deiner R�ckkehr heiraten wirst!`n`n");
						output("Ihr seid jetzt verlobt. In n�chster Zeit wird ein Priester auf euch zukommen, um die Details eurer Hochzeit zu besprechen. Alternativ k�nntet nat�rlich auch ihr Kontakt mit den Priestern im Tempel aufnehmen!`n`n");

						$session[user][seenlover]=1;
						$session[user][donation]+=1;
						addhistory('Verlobung mit '.$row['name'],1,$session['user']['acctid']);
						addhistory('Verlobung mit '.$session['user']['name'],1,$row['acctid']);

						systemmail($row[acctid],"`&Verlobung!`0","`&".$session['user']['name']."`& hat dir heute in einem Brief aus den dunklen Landen mitgeteilt dich direkt nach ".($session[user][sex]?"ihrer ":"seiner ")."R�ckkehr zu heiraten!`nIn n�chster Zeit wird ein Priester auf euch zukommen, um die Details eurer Hochzeit zu besprechen. Alternativ k�nntet nat�rlich auch ihr Kontakt mit den Priestern im Tempel aufnehmen!".$more);
						$sql = "SELECT acctid FROM accounts WHERE profession=".PROF_PRIEST_HEAD." ORDER BY loggedin DESC,rand() LIMIT 1";
						$res = db_query($sql);
						if(db_num_rows($res)) {
							$p=db_fetch_assoc($res);
							systemmail($p['acctid'],"`&Heirat zu planen!`0","`&".$row['name']."`& und `&".$session['user']['name']."`& haben sich heute verlobt. Du als Priester solltest dich darum bem�hen, den beiden eine sch�ne Hochzeit zu verschaffen!");
						}
					}
					else {
						output("`&Du freust dich schon wahnsinnig auf Euer Wiedersehen!");
					}
				} else {
                  // Flirts
					$session[user][charisma]+=1;
                    if ($session['user']['charisma']>5) $session['user']['charisma']=5;
                    $session['user']['seenlover']=1;
					output("`%Du schreibst `6$row[name]`% einen romantischen Brief aus der Ferne.");
                    systemmail($row['acctid'],"`%Ein Brief!`0","`&{$session['user']['name']}`6 hat dir einen sehnsuchtsvollen Brief aus den fernen, dunklen Landen geschrieben.".$more);
				} 
				$session[user][marriedto]=$row[acctid];
			} else {
              // Sender hat noch nicht mit Empf�nger geflirtet
                $session['user']['charisma']=1;
                $session['user']['marriedto']=$row['acctid'];
			    $session['user']['seenlover']=1;
				output("`%Du schreibst `6$row[name]`% einen romantischen Brief aus der Ferne.");
                systemmail($row['acctid'],"`%Ein Brief!`0","`&{$session['user']['name']}`6 hat dir einen sehnsuchtsvollen Brief aus den fernen, dunklen Landen geschrieben.".$more);
				
			} 
		}
	}else{
		output("`\$Fehler:`4 Dieser Krieger wurde nicht gefunden. Darf ich fragen, wie du �berhaupt hierher gekommen bist?");
	}
}

addnav('Zur�ck');
addnav('Zum Zeltlager','expedition.php');
break;

case 'chief' :
$session['user']['ddl_location'] = 2;
page_header('Expedition in die dunklen Lande - Expeditionsleiter');
output('`2Du begibst dich in das Zelt des Expeditionsleiters und siehst, dass auch andere Helden bereits dort sind und sich angeregt unterhalten. Hinter einem improvisierten Tisch sitzt der Leiter dieser Expedition und wird dir Rede und Antwort stehen. An der Wand erkennst du eine Liste derer, die auch eingeladen wurden. Direkt daneben h�ngt eine weitere Liste, die Regeln f�r das Verhalten auf dieser Expedition festlegt. Der Expeditionsleiter nimmt auch Kritik entgegen, ebenso wie W�nsche und Anregungen.`n`^(OOC- und Feedbackraum)`2`n`n');
viewcommentary('expedition_chief','Sagen',25,"sagt");
addnav('OOC');
addnav('Regeln f�r die Expedition','expedition.php?op=rules');
addnav('Information');
addnav('Der Auftrag','expedition.php?op=briefing');
addnav('Rekrutierungsliste','expedition.php?op=recruit');
addnav('Wer ist hier?');
addnav('Umsehen','expedition.php?op=whosthere&where=2&ret='.URLEncode($_SERVER['REQUEST_URI']));
addnav('Zur�ck');
addnav('Zum Zeltlager','expedition.php');
break;

case 'rules' :
$session['user']['ddl_location'] = 2;
page_header('Expedition in die dunklen Lande - Expeditionsleiter');
output('`2Die Expedition ist ein kleiner Bonus f�r Spieler, die mehr Wert auf gutes RPG als auf Leveln und Drachenkills legen. Ihnen wird hier die M�glichkeit geboten, ungest�rt durch ~Anf�nger~ und St�rer RPG zu spielen und sich mit ein paar Features das zeitraubende Durchklicken durch Wald und Schloss zu ersparen, ohne dabei auf die Vorteile verzichten zu m�ssen.`n`n');
output('`2Regeln f�r das Spiel in der Expedition`n`n
1. Es herrscht absolutes OOC-Verbot im Spiel - Absprachen sind per yom zu f�hren.`n
2. Kinder sind nicht zugelassen, die Expedition ist ausschlie�lich Recken im wehrf�higen Alter und mit gesundem Geist vorbehalten.`n
3. Manga- und Animehelden haben hier nichts zu suchen.`n
4. Autoplay und Powerplay sind strengstens verboten.`n
5. Multichar- und Knappenspiel sind nicht zugelassen, es ist nur der eigene Charakter erlaubt.`n
6. Wechselbalge (= Chars mit vielen Rassenwechseln) sind hier unerw�nscht.`n
7. Das Ausnutzen der Features hier ist nur erlaubt wenn hier auch RPG gespielt wird.`n
8. Die "Qualifizierung" f�r die Expedition erfolgt �ber gutes RPG in �ffentlichen R�umen. Jeder Teilnehmer der Expedition kann Vorschl�ge einbringen, die diskutiert werden.`n
9. Das RPG soll rassengerecht sein (Verbot von Schmusevampiren, Tanzd�monen usw...)`n
10. Die Einladung zur Expedition kann bei Fehlverhalten jederzeit zur�ckgezogen werden.`n`n');
viewcommentary('expedition_rules','Sagen',25,"sagt");
addnav('Zur�ck','expedition.php?op=chief');
break;

case 'briefing' :
$session['user']['ddl_location'] = 2;
page_header('Expedition in die dunklen Lande - Expeditionsleiter');
output('`c`b`2Der Auftrag der Expedition`c`b`n
`b`2<u>Zum Hintergrund:`b</u>`n
`2Seher und andere magisch Begabte in '.getsetting('townname','Atrahor').' k�ndigten eine erschreckende Zukunft f�r die Stadt und ihre Bewohner an. Aus den verfluchten Ebenen n�rdlich des Regengebirges, im Folgenden die Dunklen Lande genannt, soll eine gewaltige Streitmacht finsterer Kreaturen in die befriedeten Gebiete einfallen und gewaltige Zerst�rung und Tod bringen.`nDiesen Warnungen folgend wurde eine stattliche Gruppe der ber�hmtesten Helden '.getsetting('townname','Atrahor').'s ausgesandt, um die Dunklen Lande zu erkunden und mehr �ber die Schrecken herauszufinden.`n`n
<u>`b`2Die Expedition:`n`b</u>
`2Das Vorkommando fand eine karge, unwirtliche Steppe vor und errichtete das Lager nahe eines gewaltigen Felsmassivs, eingebettet in steile Klippen. Gut gesch�tzt gegen Angriffe von mehreren Seiten kann es jedoch ebenso zur t�dlichen Falle werden, denn es gibt nur einen einzigen Zugang. Der Auftrag der Expedition besteht darin, die Umgebung zu erkunden, Informationen �ber Landschaft, Pflanzen und Tiere zu gewinnen, sowie das Lager gegen vermeintliche Angriffe zu sch�tzen. N�rdlich des Lagers dehnt sich eine weite Ein�de tief in die Dunklen Lande aus.`n`n
<u>`bDie Umgebung:`b`n</u>
`2In n�herer Umgebung des Lager sind Steppen, Sumpflandschaften, Buschland und eine Felsenw�ste vorzufinden, die insgesamt als unwirtlich einzustufen sind. Vereinzelte Oasen fruchtbaren Bodens stellen eine wichtige Grundlage f�r die Versorgung des Lagers dar. Die Tierwelt besteht, nach den ersten Erkenntnissen, aus Kleinechsen, Wildkatzen und Insekten, die keine direkte Bedrohung darstellen.`n`n
<u>`bDer Feind:`b`n</u>
`2Feindkontakt ist ausschlie�lich �ber die Ein�de n�rdlich des Lagers zu erwarten, welche den einzigen direkt passierbaren Weg tief in die Dunklen Lande darstellt. Zivile Expeditionsteilnehmer seien angewiesen, zu ihrer eigenen Sicherheit diesen Abschnitt zu meiden.`n
Bei den feindlichen Kreaturen handelt es sich um lose Kleingruppen, vermutlich verschiedenen Clans zugeh�rig. Es ist anzunehmen, dass diese Gruppen, bestehend aus Soldaten und einem Kommandanten, w�hrend ihrer Angriffe vereinzelt von R�uberbanden begleitet werden. Die Wesen sind im Kampf ungew�hnlich z�h und sind als gro�e Bedrohung anzusehen.`n`n');
addnav('Zur�ck','expedition.php?op=chief');
break;


case 'recruit' :
$session['user']['ddl_location'] = 2;
page_header('Expedition in die dunklen Lande - Expeditionsleiter');
output('`2Folgende Helden nehmen an der Expedition in die dunklen Lande teil:`n`n');
$sql = "SELECT name,level,login,loggedin,dragonkills,sex FROM accounts WHERE expedition!=0 ORDER BY dragonkills DESC, level DESC LIMIT 50";

$result = db_query($sql) or die(db_error(LINK));
output("<table border=0 cellpadding=2 cellspacing=1 bgcolor='#999999'>",true);
output("<tr class='trhead'><td><b>DKs</b><td><b>Level</b></td><td><b>Name</b></td><td><b><img src=\"images/female.gif\">/<img src=\"images/male.gif\"></b><td><b>Status</b></tr>",true);
$max = db_num_rows($result);

for($i=0;$i<$max;$i++){
    $row = db_fetch_assoc($result);
    output("<tr class='".($i%2?"trdark":"trlight")."'><td>",true);
    output("`^$row[dragonkills]`0</td><td>",true);
    output("`^$row[level]`0</td><td>",true);
    if ($session[user][loggedin]) output("<a href=\"mail.php?op=write&to=".rawurlencode($row['login'])."\" target=\"_blank\" onClick=\"".popup("mail.php?op=write&to=".rawurlencode($row['login'])."").";return false;\"><img src='images/newscroll.GIF' width='16' height='16' alt='Mail schreiben' border='0'></a>",true);
    if ($session[user][loggedin]) output("<a href='bio.php?char=".rawurlencode($row['login'])."&ret=".URLEncode($_SERVER['REQUEST_URI'])."'>",true);
    if ($session[user][loggedin]) addnav("","bio.php?char=".rawurlencode($row['login'])."&ret=".URLEncode($_SERVER['REQUEST_URI'])."");
    output("$row[name]`0");
    if ($session[user][loggedin]) output("</a>",true);
    output("</td><td align=\"center\">",true);
    output($row[sex]?"<img src=\"images/female.gif\">":"<img src=\"images/male.gif\">",true);
    output("</td><td>",true);
    output($row[loggedin]?"`@online`0":"`4offline",true);
    output("</td><td>",true);
    
}
output("</table>",true);
addnav('Aktionen');
addnav('Helden vorschlagen','expedition.php?op=propose');
addnav('Zur�ck');
addnav('Zum Expeditionsleiter','expedition.php?op=chief');
break;

case 'propose' :
$session['user']['ddl_location'] = 2;
page_header('Expedition in die dunklen Lande - Expeditionsleiter / Rekrutierungsliste');
viewcommentary('expedition_recruit','`nHier kannst du jemanden zur Teilnahme an der Expedition vorschlagen',25,"sagt");
addnav('Zur�ck','expedition.php?op=recruit');
break;

case 'inn' :
$session['user']['ddl_location'] = 3;
page_header('Expedition in die dunklen Lande - Gemeinschaftszelt');
output('`gBehutsam legst du die Stoffe des Zeltes, die den Eingang verh�llen, zur Seite und trittst in das gr��te Zelt, das hier im Lager aufgeschlagen wurde. Der Raum ist vollgestellt mit einfachen Tischen und B�nken und der Boden ist mit Holzdielen ausgelegt. Ganz am Ende ersp�hst du einen kleinen Tresen, hinter dem gerade die Schankmaid Gl�ser w�scht. Zu deiner �berraschung hat sie verbl�ffende �hnlichkeit mit Violet und so l�sst du dir von einem der anwesenden Teilnehmer an der Expedition ihren Namen zufl�stern - Scarlet! Du beobachtest sie einen kurzen Moment und l�sst dir dann von ihr etwas Wasser und eine warme Speise bringen. Anschlie�end lauscht du den Heldengeschichten und Sp��en, die hier lauthals erz�hlt werden. An einem runden Tisch am Rande des Zeltes kannst du zudem ein paar Brettspiele erkennen.`n`n');
viewcommentary('expedition_inn','Sagen',25,"sagt");
addnav('Wer ist hier?');
addnav('Umsehen','expedition.php?op=whosthere&where=3&ret='.URLEncode($_SERVER['REQUEST_URI']));
addnav('Zur�ck');
addnav('Zum Zeltlager','expedition.php');
break;

case 'doc' :
$session['user']['ddl_location'] = 4;
page_header('Expedition in die dunklen Lande - Lagerarzt');
output('`7Du betrittst mit zitternden Knien das Zelt des Arztes. Dir wurde zwar der Weg zum Lagerarzt gezeigt, allerdings von diesem Besuch abgeraten. Du kannst dir nicht vorstellen, weshalb man den Arzt nicht aufsuchen sollte, wenn man doch Hilfe ben�tigt. Als du das Zelt betrittst, zweifelst du pl�tzlich an deiner Entscheidung. An den Zeltstangen h�ngen �berall �bel aussehende Instrumente, die man auf jeden Fall nicht f�r eine Heilung ben�tigt...und die sonst eigentlich verboten sind. Mitten im Zelt steht eine gro�e Liege, an der - f�r deinen Geschmack - zu viel getrocknetes Blut klebt. H�ndereibend und mit einem erfreuten L�cheln winkt der Lagerarzt dich heran. Du hast das Gef�hl, er sieht dich an wie ein Versuchskaninchen...`n`n');

$sql = "SELECT wounds FROM account_extra_info WHERE acctid=".$session[user][acctid]."";
$result = db_query($sql) or die(db_error(LINK));
$row = db_fetch_assoc($result);
$wounds = $row['wounds'];

switch ($wounds) { //Verwundungsstatus
  case 0 :
  output ('`@Du erfreust dich bester Gesundheit!`0`n`n');
  break;
  case 1 :
  output ('`2Bis auf ein paar leichte Blessuren geht es dir ganz gut.`0`n`n');
  break;
  case 2 :
  output ('`^Du hast dir in der Schlacht eine leichte Verletzung zugezogen. Vielleicht sollte der Arzt mal einen Blick darauf werfen.`0`n`n');
  break;
  case 3 :
  output ('`qDu wurdest im Kampf verletzt. Zwar schmerzt die Wunde sehr, jedoch kannst du weiter k�mpfen.`0`n`n');
  break;
  case 4 :
  output ('`4Es geht dir nicht sehr gut. Deine Verwundung bereitet dir gro�e Schmerzen und hindert dich am erneuten Kampf.`0`n`n');
  break;
  case 5 :
  output ('`$Du wurdest sehr schwer verletzt und warst dem Tode nah. Doch dank der Hilfe deiner Kameraden und des Lagerarztes hast du nun das Schlimmste �berstanden. Dennoch wird es etwas dauern, bis du wieder k�mpfen kannst.`0`n`n');
  break;
}

viewcommentary('expedition_doc','Sagen',25,"sagt");
addnav('Aktionen');
addnav('Heilen lassen','expedition.php?op=heal');
addnav('Kopf gegen die Wand hauen','expedition.php?op=hurt');
addnav('Information');
addnav('�ber Verwundungen','expedition.php?op=woundinfo');
addnav('Wer ist hier?');
addnav('Umsehen','expedition.php?op=whosthere&where=4&ret='.URLEncode($_SERVER['REQUEST_URI']));
addnav('Zur�ck');
addnav('Zum Zeltlager','expedition.php');
break;

case 'heal' :
$session['user']['ddl_location'] = 4;
page_header('Expedition in die dunklen Lande - Lagerarzt');
$sql = "SELECT wounds,doc_visited FROM account_extra_info WHERE acctid=".$session[user][acctid]."";
$result = db_query($sql) or die(db_error(LINK));
$row = db_fetch_assoc($result);

if ($row['wounds']<1)
{
  output('`QEs geht dir blendend! Warum solltest du dich also der schmerzhaften Behandlung unterziehen wollen ?`0`n');
}
elseif ($row['doc_visited']==1)
{
  output('`&Du wurdest heute bereits behandelt. Der Doktor kann erstmal nichts mehr f�r dich tun!`n');
}
else
{
  output('`7Der Doktor reibt mit sadistischem Grinsen seine H�nde und beginnt die Behandlung.`nZwar vermisst du sehr stark die Sanftheit und Vorsicht von Golinda, jedoch bringt auch diese Therapie den gew�nschten Erfolg.`n`@Es geht dir etwas besser!`0`n');
  $sql = "UPDATE account_extra_info SET wounds=wounds-1, doc_visited=1 WHERE acctid=".$session[user][acctid]."";
  db_query($sql) or die(db_error(LINK));
  $session['user']['hitpoints']=$session['user']['maxhitpoints'];
}
addnav('Zur�ck','expedition.php?op=doc');
break;

case 'hurt' :
$session['user']['ddl_location'] = 4;
page_header('Expedition in die dunklen Lande - Lagerarzt');
$sql = "SELECT wounds FROM account_extra_info WHERE acctid=".$session[user][acctid]."";
$result = db_query($sql) or die(db_error(LINK));
$row = db_fetch_assoc($result);
output('`7Ein dumpfer Knall ist zu h�ren, als du deinen Hohlkopf gegen die Wand schl�gst!`n');

if ($row['wounds']<5)
{
  $sql = "UPDATE account_extra_info SET wounds=wounds+1 WHERE acctid=".$session[user][acctid]."";
  db_query($sql) or die(db_error(LINK));
}
addnav('Zur�ck','expedition.php?op=doc');
break;

case 'woundinfo' :
$session['user']['ddl_location'] = 4;
page_header('Expedition in die dunklen Lande - Lagerarzt');
output('`7`c�ber Verwundungen:`c`n
In den Dunklen Landen begegnest du gef�hrlichen Kreaturen. Diese f�gen dir im Kampf Verletzungen zu, die`n`^zum einen deine Lebenskraft reduzieren und dir zum anderen Verwundungen zuf�gen.`7`n
Den Verlust der Lebenskraft kann jeder �bliche Heiler wieder herstellen, die Verwundung selbst kannst du jedoch <u>nur hier beim Lagerarzt</u> behandeln lassen.`n
Es gibt `^5 Verwundungsstufen`7, von quicklebendig bis dem Tode nah. Eine `bleichte Verletzung`b im Kampf erh�ht deine Verwundung um `beine Stufe`b, wohingegen eine `bVerletzung`b (durch den Soldaten oder Kommandanten verursacht) diese um `bzwei Stufen`b erh�ht. Du l�ufst Gefahr eine Verwundung zu erleiden, sobald du den ersten Treffer kassiert hast, d.h. du kann auch bei einem Sieg verwundet werden, es sei denn du hattest einen perfekten Kampf. Eine `bNiederlage`b bef�rdert dich automatisch an den Tropf, also auf `bVerwundungsstufe 5`b.`n
Die Behandlung beim Lagerarzt ist einmal t�glich m�glich. Sie senkt deine Verwundung um `beine Stufe`b und regeneriert alle verlorene Lebenskraft.`n
Ab `bVerwundungsstufe 4`b kannst du dich nicht mehr in die Ein�de begeben!`n
�ber Nacht oder durch Wiedererweckung heilen diese Verwundungen <u>nicht</u>!`n
Deine aktuelle Verwundungsstufe kannst du nur im Zelt des Lagerarztes erfahren!`n`n
`bDiese sind im einzelnen`b :`n
`7Stufe 0: `@Du erfreust dich bester Gesundheit!`0`n
`7Stufe 1: `2Bis auf ein paar leichte Blessuren geht es dir ganz gut.`0`n
`7Stufe 2: `^Du hast dir in der Schlacht eine leichte Verletzung zugezogen. Vielleicht sollte der Arzt mal einen Blick darauf werfen.`0`n
`7Stufe 3: `qDu wurdest im Kampf verletzt. Zwar schmerzt die Wunde sehr, jedoch kannst du weiter k�mpfen.`0`n
`7Stufe 4: `4Es geht dir nicht sehr gut. Deine Verwundung bereitet dir gro�e Schmerzen und hindert dich am erneuten Kampf.`n
`7Stufe 5: `$Du wurdest sehr schwer verletzt und warst dem Tode nah. Doch dank der Hilfe deiner Kameraden und des Lagerarztes hast du nun das Schlimmste �berstanden. Dennoch wird es etwas dauern bis du wieder k�mpfen kannst.`0`n`n');
 
addnav('Zur�ck','expedition.php?op=doc');
break;

case 'pools' :
$session['user']['ddl_location'] = 6;
page_header('Expedition in die dunklen Lande - Hei�e Quellen');
output('`3Vom Zeltlager aus hast du den dampfenden Wasserfall gesehen. Nachdem du einen Weg auf die steinigen Felsen gefunden hast, machst du dich auf die Suche nach dem Ursprung des scheinbar hei�en Wassers. Pl�tzlich f�llt dir auf, dass die Steine unter deinen F��en immer feuchter werden und schlie�lich siehst du direkt vor dir, mitten im Fels, scheinbar eine Ebene, �bers�ht mit kleinen Seen, in denen lebhaft das Wasser sprudelt. Erst bei n�herem Betrachten glaubst du auf die Spur dieser ungew�hnlichen W�rme zu kommen, die auch den Stein unter deinen F��en erw�rmt: Nicht nur die Quellen dampfen, sondern auch aus einem Spalt im Fels steigt Dampf aus. Da er allerdings so eng ist, dass du nichts erkennen kannst, wendest du dich von den kleinen Quellen ab und folgst den kleinen B�chen, die alle zu einer abgesenkten Stelle flie�en; pl�tzlich stehst du an der Kante des kleinen Gebirges, unmittelbar am Ursprung des Wasserfalls und blickst hinab auf das Zeltlager.`n`n');

viewcommentary('expedition_pools','Blubbern',25,"blubbert");

if ($HTTP_POST_VARS['talkline'] && !$GLOBALS['doublepost'] && e_rand(1,5)==3)
{
addnav('Aktionen');
addnav('Umschauen','expedition.php?op=look');
addnav('Heimlich Wasser lassen','expedition.php?op=pee');
}
addnav('Wer ist hier?');
addnav('Umsehen','expedition.php?op=whosthere&where=6&ret='.URLEncode($_SERVER['REQUEST_URI']));
addnav('Zur�ck');
addnav('Zum Zeltlager','expedition.php');
break;

case 'look' :
$session['user']['ddl_location'] = 6;
page_header('Hei�e Quellen - Seltsames Loch');
$sql = 'SELECT poollook FROM account_extra_info WHERE acctid="'.$session['user']['acctid'].'"';
$res = db_query($sql);
$row_extra = db_fetch_assoc($res);
if($row_extra['poollook'] != 1) {
    output('`2Als dein Blick umherschweift entdeckst du pl�tzlich eine Art kleinen Wasserfall von dem das warme Wasser l�uft. Was dir vorher nicht aufgefallen ist - dahinter scheint ein Hohlraum zu sein.. was sich wohl dahinter verbirgt?`n`n');
    addnav('Was tun?');
    addnav('Hineintauchen','expedition.php?op=dive');
    addnav('Blick abwenden','expedition.php?op=pools');
    }
else {
    output('`2Du blickst kurz auf die H�hle, wei�t jedoch genau das du da nichts mehr finden wirst ..`n`n');
    addnav('Zur�ck');
    addnav('Blick abwenden','expedition.php?op=pools');
    }
break;

case 'dive' :
$session['user']['ddl_location'] = 6;
page_header('Hei�e Quellen - Unter Wasser');
output('`2Du tauchst vollen Mutes unter und schwimmst auf den kleinen Wasserfall zu ...`n`n');
$row_extra['poollook'] = 1;
switch(e_rand(1,6)){
        case 1 :
        output('`2... pl�tzlich sp�rst du einen Kopfschmerz .. du bist daneben geschwommen und hast die Wand getroffen .. blitzartig schwimmst du zur�ck. Als du wieder auftauchst hast du eine gro�e Beule am Kopf .. das kostet dich etwas Charme!`n`n');
        $session['user']['charm']--;
        addnav('Autsch..','expedition.php?op=pools');
        break;
        case 2 :
        case 3 :
        output('`2... als du angekommen bist greifst du in das Loch .. und findest ein kleines S�ckchen.. an der Oberfl�che �ffnest du es und findest etwas altes Gold!`n`n');
        $gold = e_rand($session['user']['level']*100,$session['user']['level']*250);
        $session['user']['gold']+=$gold;
        addnav('Juhu','expedition.php?op=pools');
        break;
        case 4 :
        output('`2... als du angekommen bist greifst du in das Loch .. und findest ein kleines S�ckchen .. an der Oberfl�che �ffnest du es und findest darin einen Edelstein!`n`n');
        $session['user']['gems']++;
        addnav('Juhu','expedition.php?op=pools');
        break;
        case 5 :
        case 6 :
        output('`2... als du angekommen bist greifst du in das Loch .. doch da findest du nichts darin, da war wohl jemand schneller..`n`n');
        addnav('Schade..','expedition.php?op=pools');
        break;
    }
$sql = 'UPDATE account_extra_info SET poollook="1" WHERE acctid="'.$session['user']['acctid'].'"';
db_query($sql);

break;

case 'pee' :
$session['user']['ddl_location'] = 6;

$rowe = user_get_aei('usedouthouse');

page_header('Hei�e Quelle - Wasser lassen');
switch ($_GET[op2]) {
        case '':
        if ($rowe['usedouthouse'] !=1) {
        output('`2Du sp�rst das deine Blase dr�ckt, willst jedoch das Becken nicht verlassen .. da kommt dir die b�se Idee .. du k�nntest ja einfach hier und jetzt Wasser lassen ..`n`n');
        addnav('Wirklich?');
        addnav('Ja klar!','expedition.php?op=pee&op2=doit');
        addnav('Nee..','expedition.php?op=pools');
        }
        else {
        output('`2Du sp�rst keinen Druck und au�erdem denkst du das einmal am Tag auch reicht ..`n`n');
        addnav('Zur�ck');
        addnav('Zur Quelle!','expedition.php?op=pools');
        }
        break;
        case 'doit':
        output('`2Schnell erledigst du das dringende Gesch�ft .. du f�hlst dich sichtlich frei und deutlich n�chterner!`n`n');
        user_set_aei(array('usedouthouse' => 1));
        if ($session['user']['drunkenness']>0){
			$session['user']['drunkenness'] *= .5;
			}
		switch(e_rand(1,10)){
        case 1 :
        output('`2Doch pl�tzlich beginnt es unter dir zu blubbern und brodeln .. h�ttest du das vielleicht doch nicht tun sollen? Die G�tter bestrafen dich und du f�hlst dich pl�tzlich so .. nackt ..`n`n');
        $sql = 'INSERT INTO commentary (section,author,comment, postdate) values ("expedition_pools","1","/msg `b`$Pl�tzlich blubbert es um '.$session['user']['name'].' `$ verd�chtig und kurz darauf treibt '.($session['user']['sex']?'ihre Badebekleidung' : 'seine Badebekleidung').' an die Oberfl�che w�hrend '.($session['user']['sex']?'sie' : 'er').' v�llig nackt da sitzt. Wie peinlich ..`0", NOW())';
        db_query($sql);
        addnav('Oh nein ..!','expedition.php?op=pools');
        break;
        case 2 :
        case 3 :
        case 4 :
        case 5 :
        output('`2Als du auf das Wasser schaust ob es Spuren gibt entdeckst du pl�tzlich einen Edelstein! Wenn sich das mal nicht gelohnt hat!`n`n');
        $session['user']['gems']++;
        addnav('Jippie','expedition.php?op=pools');
        break;
        default :
        addnav('Das tat gut','expedition.php?op=pools');
        break;
        }
    }
    
break;

case 'cave' :
$session['user']['ddl_location'] = 8;
page_header('Expedition in die dunklen Lande - Tropfsteinh�hle');
$color=getsetting("DDL-cristals",1);
switch ($color)
{
  case 1 :
  $col='`#';
  break;
  case 2 :
  $col='`8';
  break;
  case 3 :
  $col='`v';
  break;
  case 4 :
  $col='`7';
  break;
  case 5 :
  $col='`Q';
  break;
}
output($col.'Du hast ein wenig am See die frische Luft genossen, als dir hinter dem Wasserfall ein kleiner Spalt im Felsen auff�llt, gut verborgen hinter dem fallendem Strom. Zu deinem Gl�ck kannst du auch noch einen sehr schmalen Pfad erkennen, der genau auf den Spalt zuf�hrt. Schnell tauchst du durch den Wasserfall und findest dich in einem schmalen, kaum mannshohem Gang wieder. Neben dir flie�t ein kleiner Bach immer tiefer in das Gestein und du beschlie�t diesem zu folgen. Immer steiler und tiefer geht es in den Fels, ehe sich der Gang pl�tzlich in einer riesigen H�hle �ffnet. Ein unwirklich scheinendes Licht t�nzelt durch die ganze H�hle, dennoch kannst du das Ausma� nur erahnen. Immer wieder siehst du Tropfsteine an der Decke, den W�nden und auch aus dem Boden scheinen sie zu wachsen. �berall sind kleine Rinnsale, die ebenso wie der Bach zu einem unterirdischen See f�hren. Das Licht l�sst die Wassertropfen immer wieder funkeln und ebenso die unz�hligen Kristalle, die in allen verschiedenen Farben schillern!`nDu bemerkst, dass die Kristalle ihre Farben wechseln, je nach dem, wieviel Wasser sie auf dem Boden umsp�lt.`n`n');
viewcommentary('expedition_cave','Fl�stern',25,"fl�stert");
addnav('Wasser stauen');
addnav('Gar nicht','expedition.php?op=cristals&act=1');
addnav('Wenig','expedition.php?op=cristals&act=2');
addnav('Mittel','expedition.php?op=cristals&act=3');
addnav('Stark','expedition.php?op=cristals&act=4');
addnav('Komplett','expedition.php?op=cristals&act=5');
addnav('Wer ist hier?');
addnav('Umsehen','expedition.php?op=whosthere&where=8&ret='.URLEncode($_SERVER['REQUEST_URI']));
addnav('Zur�ck');
addnav('Zum Zeltlager','expedition.php');
break;

case 'cristals' :
$act=$_GET['act'];
savesetting("DDL-cristals",$act);
redirect("expedition.php?op=cave");
break;

case 'milplace' :
$session['user']['ddl_location'] = 11;
page_header('Expedition in die dunklen Lande - Antreteplatz');
output('`2Ein ungenutzter Platz am Rande des Lagers, der mit Kies bedeckt ist und so ein perfekter Ort f�r die Apelle der B�rgerwehr ist. Regelm��ig m�ssen hier alle Mitglieder der Lagerwache antreten und salutieren, wenn der Oberst besondere Auszeichnungen oder Orden zu vergeben hat. Aber der Platz wird auch mit Vorliebe von den rangh�heren Offizieren genutzt, um jungen, unerfahrenen Rekruten Disziplin einzusch�rfen oder sie mit schwei�treibendem Training in Form zu bringen. Die Flagge der Lagerwache weht lebhaft im Wind, gut sichtbar f�r die Rekruten, die ebenso durch den herrischen Klang von H�rnern angesport werden sollen.`n`n');
viewcommentary('expedition_mil','Sagen',25,"sagt");

if ($session['user']['profession']==49)
{
$pointsleft=getsetting("DDL-medal","0");
addnav('Ordenpunkte: '.$pointsleft);
addnav('Orden verleihen','expedition.php?op=give_medal');
}
addnav('Wer ist hier?');
addnav('Umsehen','expedition.php?op=whosthere&where=11&ret='.URLEncode($_SERVER['REQUEST_URI']));
addnav('Zur�ck');
addnav('Zum Zeltlager','expedition.php');
break;

case 'give_medal' :
page_header('Expedition in die dunklen Lande - Antreteplatz');
output('`2Zur Zeit bedinden sich auf dem Antreteplatz:`n`n');
$sql = "SELECT name,accounts.acctid,level,login,loggedin,dragonkills,sex,profession FROM accounts JOIN account_extra_info USING (acctid) WHERE DDL_location=11 AND loggedin=1 AND profession>40 AND profession<49 ORDER BY profession DESC, level";
$result = db_query($sql) or die(db_error(LINK));
output("<table border=0 cellpadding=2 cellspacing=1 bgcolor='#999999'>",true);
output("<tr class='trhead'><td><b>DKs</b><td><b>Level</b></td><td><b>Name</b></td><td><b><img src=\"images/female.gif\">/<img src=\"images/male.gif\"></b><td><b>Rang</b></tr>",true);
$max = db_num_rows($result);

for($i=0;$i<$max;$i++){
    $row = db_fetch_assoc($result);
    output("<tr class='".($i%2?"trdark":"trlight")."'><td>",true);
    output("`^$row[dragonkills]`0</td><td>",true);
    output("`^$row[level]`0</td><td>",true);
    if ($session[user][loggedin]) output("<a href=\"mail.php?op=write&to=".rawurlencode($row['login'])."\" target=\"_blank\" onClick=\"".popup("mail.php?op=write&to=".rawurlencode($row['login'])."").";return false;\"><img src='images/newscroll.GIF' width='16' height='16' alt='Mail schreiben' border='0'>",true);
    if ($session[user][loggedin]) output("<a href='expedition.php?op=give_medal2&char=".$row['acctid']."'>",true);
    if ($session[user][loggedin]) addnav("","expedition.php?op=give_medal2&char=".$row['acctid']);
    output("$row[name]`0");
    if ($session[user][loggedin]) output("</a>",true);
    output("</td><td align=\"center\">",true);
    output($row[sex]?"<img src=\"images/female.gif\">":"<img src=\"images/male.gif\">",true);
    output("</td><td>",true);
    $rank=getprofession($row['profession']);
    output("`^".$rank."`0",true);
    output("</td><td>",true);
}output("</table>",true);

addnav("Neu laden","expedition.php?op=give_medal");
addnav("Zur�ck","expedition.php?op=milplace");
break;

case 'give_medal2' :
// Kosten f�r Orden :
// Bestpreis : 3
// Verwundetenmedaille : 6
// Bronzenes Ehrenkreuz : 9
// Silbernes Ehrenkreuz : 12
// Goldenes Ehrenkreuz : 15
// Tapferkeitsmedaille : 18
// Ehrenmedaille : 21
// Verdienstorden der B�rgerwehr : 23
//
page_header('Expedition in die dunklen Lande - Antreteplatz');
$char=$_GET['char'];
$sql = "SELECT name,accounts.acctid,level,login,loggedin,dragonkills,sex,profession FROM accounts JOIN account_extra_info USING (acctid) WHERE accounts.acctid=".$char;
$result = db_query($sql) or die(db_error(LINK));
$row = db_fetch_assoc($result);
$pointsleft=getsetting("DDL-medal","0");
output('`2Welchen Orden willst du `^'.$row['name'].'`2 verleihen ?`n');
output('`2(Du hast `&'.$pointsleft.' Punkte �brig.)`n`n');
output("<table border=0 cellpadding=2 cellspacing=1 bgcolor='#999999'>",true);
output("<tr class='trhead'><td><b>Name</b><td><b>Beschreibung</b></td><td><b>Kosten</b></tr>",true);
output("<tr class=trlight><td>",true);
output("<a href='expedition.php?op=give_medal3&char=".$row['acctid']."&medal=1'>",true);
output("Bestpreis`0</a></td><td>Eine Auszeichnung f�r leistungsf�hige Rekruten</td><td>3</td></tr>");
addnav("","expedition.php?op=give_medal3&char=".$row['acctid']."&medal=1");
output("<tr class=trdark><td>",true);
output("<a href='expedition.php?op=give_medal3&char=".$row['acctid']."&medal=2'>",true);
output("Verwundetenmedaille`0</a></td><td>Eine Anerkennung f�r K�mpfer, die in der Schlacht schwer verwundet wurden</td><td>6</td></tr>");
addnav("","expedition.php?op=give_medal3&char=".$row['acctid']."&medal=2");
output("<tr class=trlight><td>",true);
output("<a href='expedition.php?op=give_medal3&char=".$row['acctid']."&medal=3'>",true);
output("Bronzenes Ehrenkreuz`0</a></td><td>Ein Orden f�r treue Dienste in der B�rgerwehr</td><td>9</td></tr>");
addnav("","expedition.php?op=give_medal3&char=".$row['acctid']."&medal=3");
output("<tr class=trdark><td>",true);
output("<a href='expedition.php?op=give_medal3&char=".$row['acctid']."&medal=4'>",true);
output("Silbernes Ehrenkreuz`0</a></td><td>Ein Orden f�r besonders treue Dienste in der B�rgerwehr</td><td>12</td></tr>");
addnav("","expedition.php?op=give_medal3&char=".$row['acctid']."&medal=4");
output("<tr class=trlight><td>",true);
output("<a href='expedition.php?op=give_medal3&char=".$row['acctid']."&medal=5'>",true);
output("Goldenes Ehrenkreuz`0</a></td><td>Ein Orden f�r aufopfernde Dienste in der B�rgerwehr</td><td>15</td></tr>");
addnav("","expedition.php?op=give_medal3&char=".$row['acctid']."&medal=5");
output("<tr class=trdark><td>",true);
output("<a href='expedition.php?op=give_medal3&char=".$row['acctid']."&medal=6'>",true);
output("Tapferkeitsmedaille`0</a></td><td>Die Medaille f�r h�chste Tapferkeit im Kampf</td><td>18</td></tr>");
addnav("","expedition.php?op=give_medal3&char=".$row['acctid']."&medal=6");
output("<tr class=trlight><td>",true);
output("<a href='expedition.php?op=give_medal3&char=".$row['acctid']."&medal=7'>",true);
output("Ehrenmedaille`0</a></td><td>Eine Auszeichnung f�r Krieger, die h�chste Ehren erlangt haben.</td><td>21</td></tr>");
addnav("","expedition.php?op=give_medal3&char=".$row['acctid']."&medal=7");
output("<tr class=trlight><td>",true);
output("<a href='expedition.php?op=give_medal3&char=".$row['acctid']."&medal=7'>",true);
output("Verdienstorden der B�rgerwehr`0</a></td><td>Die h�chste Auszeichnung der B�rgerwehr</td><td>23</td></tr>");
addnav("","expedition.php?op=give_medal3&char=".$row['acctid']."&medal=8");
output("</table>",true);
addnav("Zur�ck","expedition.php?op=milplace");
break;

case 'give_medal3' :
page_header('Expedition in die dunklen Lande - Antreteplatz');
$char=$_GET['char'];
$sql = "SELECT name,accounts.acctid,level,login,loggedin,dragonkills,sex,profession FROM accounts JOIN account_extra_info USING (acctid) WHERE accounts.acctid=".$char;
$result = db_query($sql) or die(db_error(LINK));
$row = db_fetch_assoc($result);

$medal=$_GET['medal'];
$pointsleft=getsetting("DDL-medal","0");
if ($pointsleft>=($medal*3))
{
switch ($medal)
{
  case 1 :
  $mname='`2Bestpreis`0';
  $msg='Eine Auszeichnung f�r leistungsf�hige Rekruten. ';
  break;
  case 2 :
  $mname='`4Verwundetenmedaille`0';
  $msg='Eine Anerkennung f�r K�mpfer, die in der Schlacht schwer verwundet wurden. ';
  break;
  case 3 :
  $mname='`tBronzenes Ehrenkreuz`0';
  $msg='Ein Orden f�r treue Dienste in der B�rgerwehr. ';
  break;
  case 4 :
  $mname='`&Silbernes Ehrenkreuz`0';
  $msg='Ein Orden f�r besonders treue Dienste in der B�rgerwehr. ';
  break;
  case 5 :
  $mname='�^Goldenes Ehrenkreuz`0';
  $msg='Ein Orden f�r aufopfernde Dienste in der B�rgerwehr. ';
  break;
  case 6 :
  $mname='`vTapferkeitsmedaille`0';
  $msg='Die Medaille f�r h�chste Tapferkeit im Kampf. ';
  break;
  case 7 :
  $mname='`#Verdienstorden der B�rgerwehr`0';
  $msg='Eine Auszeichnung f�r Krieger, die h�chste Ehren erlangt haben. ';
  break;
  case 8 :
  $mname='`1Verdienstorden der B�rgerwehr`0';
  $msg='Die h�chste Auszeichnung der B�rgerwehr. ';
  break;
}
$msg.='Verliehen an '.$row['name'];

$value=$medal*500;

$item['tpl_name'] = $mname;
$item['tpl_description'] = $msg;
$item['tpl_gold'] = $value;

item_add($row['acctid'],'medal',true,$item);

$sql = "SELECT name,accounts.acctid,level,login,loggedin,dragonkills,sex,profession FROM accounts JOIN account_extra_info USING (acctid) WHERE accounts.acctid=".$char;
$result = db_query($sql) or die(db_error(LINK));
$row = db_fetch_assoc($result);
$sql="INSERT INTO commentary (postdate,section,author,comment) VALUES (now(),'expedition_mil',".$session[user][acctid].",'/me `^verleiht `^".$row['name']."`^ die Auszeichnung `#".$mname."`0')";
db_query($sql) or die(db_error(LINK));
addnews_ddl($session['user']['name']." `2hat heute `^".$row['name']." `2die Auszeichnung ".$mname."`2 verliehen!");
output($mname.' `&wurde soeben an `^'.$row['name'].' `&verliehen.');
$cost=$medal*3;
$pointsleft-=$cost;
savesetting("DDL-medal",$pointsleft);
}
else
{
  output('`4Zu wenig Punkte f�r diese Medaille!');
}
addnav("Zur�ck","expedition.php?op=milplace");
break;

case 'mytent' :
$session['user']['ddl_location'] = 10;

$sql = "SELECT login FROM accounts JOIN account_extra_info ON accounts.acctid=account_extra_info.DDL_tent WHERE account_extra_info.acctid=".$session[user][acctid];
$result = db_query($sql) or die(db_error(LINK));

page_header('Expedition in die dunklen Lande - Privatzelt');
$account=$session['user']['acctid'];
output('`& Du gelangst zu deinem Zelt, das ebenso klein und eng ist, wie das der anderen Teilnehmer. Hierhin kannst du dich zur�ckziehen, falls du etwas Ruhe ben�tigst oder dich etwas von der anstrengenden Expedition ausruhen m�chtest. Dein Hab und Gut hast du gerade so in das kleine Zelte bekommen, sodass du kaum Platz zum Schlafen hast. Stehen ist ebenso nicht m�glich, da du dir eine Beule an den viel zu tiefen Stangen holen w�rdest. Allerdings wird es f�r kurze Zeit sicherlich gehen, dass du dich in deinem Zelt so klein machst, dass noch eine weitere Person hinein passt.`n`n');

if (db_num_rows($result)>0)
  {
    $row = db_fetch_assoc($result);
    output('Du hast `^'.$row['login'].'`& in dein Zelt eingeladen.`n`n');
    $visitor=1;
  }

$room='tent'.$account;
viewcommentary($room,'Fl�stern',25,"fl�stert");
addnav('Aktion');
addnav('Aufr�umen','expedition.php?op=sauber&where='.$room);
addnav('Unterredung');
addnav('Jemanden einladen','expedition.php?op=invite');
if ($visitor==1) addnav('Rauswerfen','expedition.php?op=invitationend');
addnav('Zur�ck');
addnav('Zum Zeltlager','expedition.php');
break;

case 'sauber' :
$room=$_GET['where'];
$roomcopy=$room.'copy';
$sql = "UPDATE commentary SET section='$roomcopy' WHERE section='$room'";
db_query($sql);
redirect('expedition.php?op=mytent');
break;

case 'othertent' :
$session['user']['ddl_location'] = 10;
page_header('Expedition in die dunklen Lande - Privatzelt');
$account=$_GET['who'];
$sql = "SELECT login FROM accounts WHERE acctid=".$account;
$result = db_query($sql) or die(db_error(LINK));
$row = db_fetch_assoc($result);
output('`&Du schl�gst die Plane auf Seite und krabbelst zu '.$row['login'].' in '.($row[sex]?"ihr ":"sein ").'Zelt. Ihr m�sst euch ziemlich eng aneinander kuscheln, da das Zelt eigentlich nur f�r eine Person ausgelegt ist. Auch solltet ihr eure Stimmen m��igen, da die Zeltplane d�nn ist und es draussen nur so vor neugierigen Ohren wimmelt.`n`n');
viewcommentary('tent'.$account,'Fl�stern',25,"fl�stert");
addnav('Zum Zeltlager','expedition.php');
break;

case 'invite' :
page_header('Expedition in die dunklen Lande - Privatzelt');
output("`&Du kannst einen Expeditionsteilnehmer in dein Zelt einladen. Sollte bereits jemand anderes eine Einladung von dir erhalten haben, so wird diese automatisch zur�ck genommen.`n`n");

if ($HTTP_GET_VARS[who]=="") {
            output("`&Wen willst du einladen?`n`&");
            if ($_GET['subop']!="search"){
                output("<form action='expedition.php?op=invite&subop=search' method='POST'><input name='name'><input type='submit' class='button' value='Suchen'></form>",true);
                addnav("","expedition.php?op=invite&subop=search");
            }else{
                addnav("Neue Suche","expedition.php?op=invite");
                $search = "%";
                for ($i=0;$i<strlen($_POST['name']);$i++){
                    $search.=substr($_POST['name'],$i,1)."%";
                }
                $sql = "SELECT name,alive,loggedin,login FROM accounts JOIN account_extra_info USING(acctid) WHERE (name LIKE '$search' and expedition>0)ORDER BY name DESC";
                //output($sql);
                $result = db_query($sql) or die(db_error(LINK));
                $max = db_num_rows($result);

                output("<table border=0 cellpadding=0><tr><td>Name</td><td>Status</td></tr>",true);
                for ($i=0;$i<$max;$i++){
                    $row = db_fetch_assoc($result);
                    output("<tr><td><a href='expedition.php?op=invite&who=".rawurlencode($row[login])."'>$row[name]</a></td><td>",true);
                if ($row['loggedin']) {output("`@online`&</td></tr>",true);} else
                                       output("`4offline`&</td></tr>",true);
                    addnav("","expedition.php?op=invite&who=".rawurlencode($row[login]));
                }
                output("</table>",true);
            }
        }else{
                $sql = "SELECT acctid,name,login FROM accounts WHERE login=\"$HTTP_GET_VARS[who]\"";
                $result = db_query($sql) or die(db_error(LINK));
                $row = db_fetch_assoc($result);

output("`&M�chtest du `^".$row['name']." `&zu einer privaten Unterredung in dein Zelt bitten?`n`n`n");
addnav('Ja','expedition.php?op=invite2&who='.rawurlencode($row[login]));
addnav('Nein');
addnav('Neue Suche','expedition.php?op=invite');
}
addnav('Zur�ck','expedition.php?op=mytent');
break;

case 'invite2' :
page_header('Expedition in die dunklen Lande - Privatzelt');
$sql = "SELECT acctid,name,login FROM accounts WHERE login=\"$HTTP_GET_VARS[who]\"";
$result = db_query($sql) or die(db_error(LINK));
$row = db_fetch_assoc($result);
output('`&Alles klar! '.$row['name'].' `&erh�lt eine Einladung in dein Zelt!`n`n');
$sql = 'UPDATE account_extra_info SET DDL_tent='.$row[acctid].' WHERE acctid='.$session[user][acctid];
db_query($sql) or die(db_error(LINK));
systemmail($row['acctid'],"`%DDL : Einladung ins Zelt von ".$session[user][login]."`%!`0","`&{$session['user']['name']}`6 w�nscht dich in ".($row[sex]?"ihrem ":"seinem ")." Zelt zu sprechen - unverz�glich und allein...");
addnav('Zur�ck','expedition.php?op=mytent');
break;

case 'invitationend' :
$sql = "UPDATE account_extra_info SET DDL_tent=0 WHERE acctid=".$session[user][acctid]."";
db_query($sql) or die(db_error(LINK));
redirect('expedition.php?op=mytent');
break;

case 'guards' :
$session['user']['ddl_location'] = 5;

page_header('Expedition in die dunklen Lande - Lagerwache');
output('`&Hier kannst du Informationen und Neuigkeiten �ber Feindkontakt in den Dunklen Landen erfahren.`n`n');
switch ($session['user']['profession'])
{
  case 41 :
  output('`2Du betrittst das Zelt der Wache. Kaum einen Schritt kannst du in den Raum hinein setzen, als man dir schon einen Eimer und einen Putzlappen in die Hand dr�ckt. Missmutig bringst du das Zelt in Ordung und hast nun eine kleine Pause, bevor dich dein Ausbilder aufs neue qu�len wird.`n`n');
  break;
  case 42 :
  output('`2Du betrittst das Zelt der Lagerwache. Dein ausbildender Sergeant blickt dich streng an und deutet wortlos auf die Waffen und R�stungsteile, die wohl dir geh�ren und dringed der Reinigung und Pflege bed�rfen. Alibim��ig machst du dich an die Arbeit um dann kurze Zeit sp�ter wieder etwas anderes zu tun.`n`n');
  break;
  case 43 :
  output('`2Als du das Zelt der Lagerwache betrittst, siehst du wie einige der Soldaten fr�hlich plaudernd Karten spielen. Du erkennst einige gute Freunde unter ihnen wieder, und einer r�ckt auf Seite um einen weiteren Stuhl heranzuziehen. Sie winken dir zu am Spiel teilzunehmen.`n`n');
  break;
  case 44 :
  output('`2Als du das Zelt der Wache betrittst, findest du die Soldaten in unterschiedlichen Besch�ftigungen vor. Dein Sergeant erhebt sich und geht auf dich zu.`n"`@Alles klar soweit! Die Rekruten geben ein gutes Bild ab und die Moral ist auch nicht zu beklagen. Sind halt nur alle etwas nerv�s wegen der ganzen Sache mit den dunklen Kreaturen.`2" sagt er dir und nach einer kurzen Unterhaltung geht er zur�ck an seine Arbeit.`n`n');
  break;
  case 45 :
  output('`2Als du das Zelt der Lagerwache betrittst, siehst du die Soldaten, wie sie mehr oder weniger sinnvollen Besch�ftigungen nachgehen. Kaum einer w�rdigt dich eines Blickes, und jene, die es tun, nicken dir nur knapp zu. Du glaubst, dass sie hinter deinem R�cken �ber dich reden.`n`n');
  break;
  case 46 :
  output('`2Als du dich in das Zelt der Lagerwache begibst, siehst du die Soldaten, wie sie ihre Waffen putzen, Kartenspielen und ausgelassen tratschen.`nEiner ruft dir zu : "`@Tach, '.($session[user][sex]?"Frau":"Herr").' Leutnant!`2" und gibt dir einen milit�rischen Gru�. Danach geht er  wieder seiner Besch�ftigung nach.`n`n');
  break;
  case 47 :
  output('`2Als du das Zelt der Lagerwache betrittst, siehst du die Soldaten, wie sie ihre Waffen putzen, Kartenspielen und ausgelassen tratschen.`nEiner ruft im halblauten Ton : "`@Offizier anwesend!`2" und die anderen erheben sich kurz und salutieren vor dir. Danach geht jeder wieder seiner Besch�ftigung nach.`n`n');
  break;
  case 48 :
  output('`2Als du das Zelt der betrittst, findest du einige der Soldaten vor, wie sie ihre Waffen putzen, sowie andere beim Kartenspielen und tratschen.`nNach einem kurzen Moment ruft einer : "`@Achtung!`2" und die Soldaten erheben sich und nehmen Haltung an. Dir wird die Lage gemeldet, und danach geht jeder wieder seiner Besch�ftigung nach.`n`n');
  break;
  case 49 :
  output('`2Als du das Zelt der Lagerwache betrittst, siehst du, wie einige deiner Soldaten ihre Waffen putzen, andere �ber Lagepl�nen br�ten und wieder andere mit Kartenspielen besch�ftigt sind.`nSofort br�llt einer laut : "`@Aaaaachtung!`2" und jeder l�sst augenblicklich alles fallen, was er
  gerade in H�nden h�lt und nimmt Haltung an. Dir wird die Lage gemeldet und alle blicken dich erwartungsvoll an.`n`n');
  break;
  default :
  output('`2 Im Zelt der Lagerwache triffst du besonders viele ehrenwerte Mitglieder der B�rgerwehr, zu der du nur allzu gern geh�ren w�rdest. Alle m�glichen "Zivilisten" berichten hier eifrig von ihren Erfolgen �ber die K�mpfer der Dunklen Lande um m�glichst schnell einen hohen Rang zu bekommen. Doch die Anf�hrer der B�rgerwehr scheinen sich daran keinesfalls zu st�ren, beziehungsweise dies zu beachten. Sie diskutieren nur die neusten Strategien und setzen auf einer gro�en Karte auf dem Tisch kleine Figuren hin und her. Was das bedeutet, findest du sicher nur heraus, wenn du genug Krieger besiegt hast und das bereit bist, das Lager zu verteidigen.`n`n');
  break;
}

viewcommentary('expedition_guards','Melden',25,"meldet");
addnav('Information');
addnav('Befehle','expedition.php?op=explain_orders');
addnav('�ber den Kampf','expedition.php?op=about_battle');
addnav('Mein Rang','expedition.php?op=myrank');
addnav('B�rgerwehr');
addnav('Neuigkeiten','expedition.php?op=news');
addnav('Mitglieder','expedition.php?op=ranks');

if (($session['user']['profession']>40 && $session['user']['profession']<50) || ($session['user']['superuser']>0))
{
addnav('Taktik');
addnav('Lagebericht','expedition.php?op=tactics');
}
addnav('Wer ist hier?');
addnav('Umsehen','expedition.php?op=whosthere&where=5&ret='.URLEncode($_SERVER['REQUEST_URI']));
addnav('Zur�ck');
addnav('Zum Zeltlager','expedition.php');
break;

case 'explain_orders' :
$session['user']['ddl_location'] = 5;
page_header('Expedition in die dunklen Lande - Lagerwache');
output('`2Die Tagesbefehle im Folgenden :`n`n
Wird der Befehl "`&Warten auf Weiteres!`2" ausgegeben, so hat dies keine Konsequenzen.`n`n
Lautet der Tagesbefehl "`^Angriff!`2", so besteht die M�glichkeit, durch erfolgreiche K�mpfe in der Ein�de, die Situation des Lagers zum Positiven zu ver�ndern.`n`n
Sollte der Befehl "`4Stellungen halten!`2" gegeben sein, so sind Feinde auf dem Vormarsch. Nur durch erfolgreiche K�mpfe in der Ein�de l�sst sich nun verhindern, dass das Lager in Bedr�ngnis gebracht wird.`0');
addnav('Zur�ck','expedition.php?op=guards');
break;

case 'news' :
$session['user']['ddl_location'] = 5;
page_header('Expedition in die dunklen Lande - Lagerwache');

$newsperpage=30;
if (su_check(SU_RIGHT_EXPEDITION)){
		output("`0<form action=\"expedition.php?op=news\" method='POST'>",true);
		output("[Admin] Meldung manuell eingeben? <input name='meldung' size='40'> ",true);
		output("<input type='submit' class='button' value='Eintragen'>`n`n",true);
		addnav("","expedition.php?op=news");
		if ($_POST[meldung]){
			$sql = "INSERT INTO ddlnews(newstext,newsdate,accountid) VALUES ('".addslashes($_POST[meldung])."',NOW(),0)";
			db_query($sql) or die(db_error($link));
			$_POST[meldung]="";
		}
		addnav("","expedition.php?op=news");
	}
    addnav("Zur�ck","expedition.php?op=guards");
    addnav("Bl�ttern");

    $offset = (int)$HTTP_GET_VARS[offset];
	$timestamp=strtotime((0-$offset)." days");
	$sql = "SELECT count(newsid) AS c FROM ddlnews WHERE newsdate='".date("Y-m-d",$timestamp)."'";
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	$totaltoday=$row['c'];
	$pageoffset = (int)$_GET['page'];
	if ($pageoffset>0) $pageoffset--;
	$pageoffset*=$newsperpage;
	$sql = "SELECT * FROM ddlnews WHERE newsdate='".date("Y-m-d",$timestamp)."' ORDER BY newsid DESC LIMIT $pageoffset,$newsperpage";
	$result = db_query($sql) or die(db_error(LINK));
	$date=strftime("%A, %e. %B",$timestamp);

	output("`c`b`!Neuigkeiten bei der Expedition am $date".($totaltoday>$newsperpage?" (Meldungen ".($pageoffset+1)." - ".min($pageoffset+$newsperpage,$totaltoday)." von $totaltoday)":"")."`c`b`0");

	for ($i=0;$i<db_num_rows($result);$i++){
		$row = db_fetch_assoc($result);
		output("`c`2-=-`@=-=`2-=-`@=-=`2-=-`@=-=`2-=-`0`c");
		if (su_check(SU_RIGHT_EXPEDITION)){
			output("[ <a href='superuser.php?op=newsdelete2&newsid=$row[newsid]&return=".URLEncode($_SERVER['REQUEST_URI'])."'>Del</a> ]&nbsp;",true);
			addnav("","superuser.php?op=newsdelete2&newsid=$row[newsid]&return=".URLEncode($_SERVER['REQUEST_URI']));
		}
		output("$row[newstext]`n");
	}
	if (db_num_rows($result)==0){
		output("`c`2-=-`@=-=`2-=-`@=-=`2-=-`@=-=`2-=-`0`c");
		output("`1`b`c Bislang nichts neues. Ein ruhiger Tag. `c`b`0");
	}
	output("`c`2-=-`@=-=`2-=-`@=-=`2-=-`@=-=`2-=-`0`c");
	if ($totaltoday>$newsperpage){
		addnav("Heutige Meldungen");
		for ($i=0;$i<$totaltoday;$i+=$newsperpage){
			addnav("Seite ".($i/$newsperpage+1),"expedition.php?op=news&offset=$offset&page=".($i/$newsperpage+1));
		}
	}

	addnav("Vorherige Meldungen","expedition.php?op=news&offset=".($offset+1));
	if ($offset>0){
		addnav("N�chste Meldungen","expedition.php?op=news&offset=".($offset-1));
	}
break;

case 'about_battle' :
$session['user']['ddl_location'] = 5;
page_header('Expedition in die dunklen Lande - Lagerwache');
output('`&In der `tEin�de`& hast du die M�glichkeit, dein Lager gegen ann�hernden Feind zu sch�tzen oder neues Territorium zu erobern.`n
�ber den `4Feind`& ist nicht viel bekannt. Es handelt sich um eine in Kasten gegliederte Kriegerrasse, die stets pl�tzlich und in gro�er Zahl angreift.`nDabei tragen diese Wesen `#keinerlei R�stung`&, sind sie doch durch eine nat�rliche, dick ledrige Haut gesch�tzt.`n
Als `#Waffe`& verwenden sie ihre blo�en F�uste, mit gef�hrlichen Stacheln versehene Kampfhandschuhe, Speere oder Schwerter.`n`n
Sie haben die Eigenart `qWunden`& zu schlagen, die nur sehr schwer zu behandeln sind.`n
Gegen eine derartige Verletzung hilft nur ein Besuch beim `^Lagerarzt`&, der `^einmal pro Tag`& eine leichte Wunde heilen kann.`nMit einer `qkleinen Wunde`& wird es noch m�glich sein, in der Ein�de zu k�mpfen, doch mit `bvier`b dieser Verletzungen, ebenso wie mit einer `qschweren Verwundung`&, welche f�nf kleinen Wunden entspricht, ist dies ausgeschlossen.`n`n
Eine `4Niederlage im Kampf`& bedeutet nicht gleich das Ende, da unsere Feldsanit�ter den Schwerverletzten sofort aus dem Gefahrengebiet schaffen und versorgen.`n
Auch bei einem `@Sieg`& gegen deine Widersacher kannst du leichte Verletzungen davon tragen.`n`n
Doch ganz gleich was dir auf der Expedition passiert, es wird dein Leben bei der R�ckkehr nach '.getsetting('townname','Atrahor').' kaum beeintr�chtigen.`n
Auch so schwer verletzt, dass du nicht mehr in der Ein�de k�mpfen kannst, wirst du immer noch den Drachen herausfordern k�nnen. Dazu heilt der Lagerarzt auch die Lebenskraft komplett.`n');
addnav("Zur�ck","expedition.php?op=guards");
break;

case 'ranks' :
$session['user']['ddl_location'] = 5;
page_header('Expedition in die dunklen Lande - Lagerwache');
output('`^`cFolgende Helden haben durch tapferen Einsatz im Kampf einen Rang in der B�rgerwehr erhalten :`c`0`n`n');

$sql = "SELECT name,level,login,loggedin,dragonkills,sex,profession FROM accounts JOIN account_extra_info USING (acctid) WHERE expedition!=0 AND profession>40 AND profession <50 ORDER BY profession DESC,dragonkills DESC, level DESC LIMIT 50";
$result = db_query($sql) or die(db_error(LINK));
output("<table border=0 cellpadding=2 cellspacing=1 bgcolor='#999999'>",true);
output("<tr class='trhead'><td><b>DKs</b><td><b>Level</b></td><td><b>Name</b></td><td><b><img src=\"images/female.gif\">/<img src=\"images/male.gif\"></b><td><b>Status</b><td><b>Rang</b></tr>",true);
$max = db_num_rows($result);
for($i=0;$i<$max;$i++){
    $row = db_fetch_assoc($result);
    output("<tr class='".($i%2?"trdark":"trlight")."'><td>",true);
    output("`^$row[dragonkills]`0</td><td>",true);
    output("`^$row[level]`0</td><td>",true);
    if ($session[user][loggedin]) output("<a href=\"mail.php?op=write&to=".rawurlencode($row['login'])."\" target=\"_blank\" onClick=\"".popup("mail.php?op=write&to=".rawurlencode($row['login'])."").";return false;\"><img src='images/newscroll.GIF' width='16' height='16' alt='Mail schreiben' border='0'></a>",true);
    if ($session[user][loggedin]) output("<a href='bio.php?char=".rawurlencode($row['login'])."&ret=".URLEncode($_SERVER['REQUEST_URI'])."'>",true);
    if ($session[user][loggedin]) addnav("","bio.php?char=".rawurlencode($row['login'])."&ret=".URLEncode($_SERVER['REQUEST_URI'])."");
    output("$row[name]`0");
    if ($session[user][loggedin]) output("</a>",true);
    output("</td><td align=\"center\">",true);
    output($row[sex]?"<img src=\"images/female.gif\">":"<img src=\"images/male.gif\">",true);
    output("</td><td>",true);
    output($row[loggedin]?"`@online`0":"`4offline",true);
    output("</td><td>",true);
    $rank=getprofession($row['profession']);
    output('`^'.$rank.'`0',true);
    output("</td><td>",true);

}
output("</table>",true);
addnav("Zur�ck","expedition.php?op=guards");
break;

case 'myrank' :
$session['user']['ddl_location'] = 5;
page_header('Expedition in die dunklen Lande - Lagerwache');
output('`2Du ziehst einen Feldwebel auf Seite und fragst ihn, was er denn so von dir und deinem Rang h�lt.`n`n
"`@Soso`2", brummt er,');

switch ($session['user']['profession'])
{
case 41 :
  output('`2"`@Du bistn Rekrut... Tut mir echt leid. Musst dich von jedem rumscheuchen lassen, hast �berhaupt nichts zu sagen und musst f�r alle die Drecksarbeit erledigen. Und dann ist da noch die Ausbildung... Oh je, wenn ich an meine Zeit zur�ckdenke, dann wird mir Angst und Bange.`nUnd jetzt geh zur�ck an die Arbeit und polier die R�stungen! Ich kontrolliere das gleich...`2"`n`n');
  break;
  case 42 :
  output('`2"`@Dem Corporal ist wohl langweilig. Dein Job hier ist es den Sergeants beim Terror... �h... beim Ausbilden der Rekruten zu helfen, zu sagen hast du den Frischlingen allerdings nichts. Aber wenn dich das nicht auslastet, dann gibt es auch noch ein paar Schwerter, die unbedingt gesch�rft werden m�ssen. Nat�rlich hast du auch ein wenig Zeit um dich beim Kartenspiel zu entspannen.`2"`n`n');
  break;
  case 43 :
  output('`2"`@Ein Sergeant in der Pause. Du bist mit der Ausbildung der Rekruten betraut, sowie mit der Weiterbildung der Corporals. Beide kannst du rumscheuchen, wie du es willst. Den Zivilisten hast du jedoch nichts zu sagen. Und wenn du mal nicht mit der Ausbildung besch�ftigt bist, dann nimmt man dich auch gern f�r unliebsame Wachschichten her. Oh, ich glaube da hinten stehen ein paar Rekruten rum und haben nichts zu tun... Los, los, oder willst du das tollerieren ?`2"`n`n');
  break;
  case 44 :
  output('`2"`@Du bistn Feldwebel, so wie ich. Tja... unser Job ist es Fragen aller Art zu beantworten und zu schauen ob die Sergeants unsre neuen Rekruten nicht rumgammeln lassen. Wir k�nnen Sergeants, Corporals und Rekruten Befehle erteilen, von Zivilisten haben wir die Finger zu lassen. Schade eigentlich, aber da kann man nix machen. Soweit alles klar?`2"`n`n');
  break;
  case 45 :
  output('`2"`@Ein F�hnrich! Haha, armes Schwein! M�chte nicht in deiner Haut stecken, denn du hast die Pflichten eines Offiziers und die Rechte eines Rekruten. Zwar kannst du Rekruten, Corporals und Sergeants Befehle erteilen, aber dazu musst du erstmal kommen! Der F�hnricht muss so ziemlich alles tun, wovor sich die Offiziere gern dr�cken, weil es einfach l�stig ist. Noch Fragen?`2"`n`n');
  break;
  case 46 :
  output('`2"`@Ihr seid Leutnant, ein frisch gebackener Offizier. Seid froh, denn Ihr habt die schlimmste Zeit hinter Euch gebracht, ab sofort kann es nur besser werden. Als Leutnant seid Ihr Stellvertreter f�r alles und jeden, und wenn �ber euch niemand mehr ist, so k�nnt ihr sogar mit der F�hrung des ganzen Lagers betraut werden. Seid Euch also Eurer Position bewusst und macht ihr alle Ehre!`nBefehlen k�nnt ihr �ber Rekruten, Corporals, Sergeants, Feldwebel und F�nriche.`2"`n`n');
  break;
  case 47 :
  output('`2"`@Hauptmann! Freue micht, dass Ihr meinen Rat sucht! Ihr seid vollwertiher Offizier und angesehenes Mitglied der B�rgerwehr. Ihr habt voll Befehlsgewalt �ber Rekruten, Corporals, Sergeants, Feldwebel, F�nriche und Leutnants. Auch liegt es an Euch neu eingtroffene Zivilisten im Lager herumzuf�hren und ihnen alles zu zeigen. Zu befehlen habt Ihr ihnen leider dennoch nichts.`2"`n`n');
  break;
  case 48 :
  output('`2"`@Als Major habt Ihr volle Befehlsgewalt �ber Rekruten, Corporals, Sergeants, Feldwebel, F�hnriche, sowie �ber die Leutnants und Hauptleute und sogar die Zivilisten! Ihr k�nnt Bef�rderungen und Degradierungen durchf�hren, jedoch nicht, wenn es Offiziere betrifft. �ber Euch steht nur noch der Rang Oberst, dem gegen�ber Ihr zu Gehorsam verpflichtet seit.`2"`n`n');
  break;
  case 49 :
  output('`2"`@Ihr seid Oberst und habt Euer Laufbahnziel hier erreicht. Als quasi Chef der Lagerwache habt Ihr Befehlgewalt �ber alle anderen R�nge und die Zivilisten, und k�nnt bis hin zum Rang des Majors Bef�rderungen und Degradierungen durchf�hren. Doch seid vorsichtig mit den Bef�rderungen. Denn wen wollt Ihr noch herumscheuchen, wenn es hier nur H�uptlinge gibt ?`2"`n`n');
  break;
  default :
  output('`2"`@Du bistn Zivilist. Tolle Sache. Zwar kann dir au�er jemandem im Rang Major oder Oberst keiner hier gro� was befehlen, jedoch bist du auch ein ziemlicher Au�enseiter, was die Wache hier betrifft. Pass bloss auf, dass man dich nicht zum Rekruten macht, dann dann haste ausgelacht!`n`n');
  break;
}
addnav("Zur�ck","expedition.php?op=guards");
break;

case 'tactics' :
$session['user']['ddl_location'] = 5;
page_header('Expedition in die dunklen Lande - Lagerwache');
output('`2Du begibst dich zu den Lagepl�nen und Karten, um dir einen groben �berblick �ber die Situation zu verschaffen.`n`nZur Zeit sieht es folgenderma�en aus :`n
Die aktuelle Tagesorder ist `^'.getsetting("DDL_act_order","0").'`2 Tage alt und wird vorraussichtlich bis zum `^'.getsetting("DDL_new_order",3).'.`2 Tag beibehalten.`n
Der taktische Fortschritt unserer K�mpfer liegt derzeit bei `^'.getsetting("DDL-balance","0").'`2.`n
Vorhaben wie "Angriff" oder "Stellungen halten" gelingen bei einem taktischen Fortschritt von mindestens
 `^'.getsetting("DDL_balance_win",25).'`2 und scheitern bei `^'.getsetting("DDL_balance_lose",-10).'`2.`n
Bei "Warten auf Weiteres" erh�ht ein Fortschritt von mindestens `^'.getsetting("DDL_balance_push",40).'`2 die Chance auf einen "Angriff" bei Ausgabe der n�chsten Order.`n
Ein Fortschritt von `^'.(getsetting("DDL_balance_lose",-10)*2).'`2 oder weniger verschiebt die Tendenz zu "Stellungen halten".`n
Jeden Tag verschlechtert sich die Lage um `^'.getsetting("DDL_balance_malus",5).'`2.`n`n');
addnav('Zur�ck','expedition.php?op=guards');
break;

case 'fight' :
page_header('Expedition in die dunklen Lande - Kampf');
$battle = true;
break;

case 'wastes' :
$session['user']['ddl_location'] = 7;
page_header('Expedition in die dunklen Lande - Ein�de');
$sql = "SELECT wounds FROM account_extra_info WHERE acctid=".$session[user][acctid]."";
$result = db_query($sql) or die(db_error(LINK));
$row = db_fetch_assoc($result);

if ($row['wounds']>3 && $session['user']['superuser']==0) // Kein Zutritt bei Verletzung
{
output('`tDeine Verletzungen sind zu stark, und du musst dich aus diesem gef�hrlichen Gebiet zur�ckziehen.`nSuche den Lagerarzt auf und lass dich behandeln!`n');
}
else
{
output('`tImmer weiter entfernst du dich vom Zeltlager und versuchst in der Ferne das Dorf zu ersp�hen. Doch du siehst es nicht, genauso wenig wie du etwas anderes siehst. Vereinzelt stehen hier und da halb verdorrte Str�ucher mitten in der Landschaft. Hier f�hlst du dich vollkommen ausgeliefert, denn auf der weiten Ebene kann man alles sehr gut einsehen und Verstecke gibt es kaum.`n`n');

$xstate = getsetting("DDL-state",6);
if ($xstate<11) {
// Anzahl Anwesender ermitteln
$sql = "SELECT acctid FROM accounts WHERE DDL_location=7";
$result2 = db_query($sql) or die(db_error(LINK));
$fighters = db_num_rows($result2);
$chance=e_rand(0,2);

// Anzahl n�tiger Posts ermitteln
$amount=getsetting("DDL_comments","0");
$amount_req=getsetting("DDL_comments_req_act",5);
$amount_req*=$fighters;
$DDL_opps=getsetting("DDL_opps","0");
if ($HTTP_POST_VARS['talkline'] && !$GLOBALS['doublepost'])  // Kommentare wurde geschrieben
{
if ($DDL_opps<=0) $amount++;
savesetting("DDL_comments",$amount);
$atkstate=$amount_req-$amount;

switch ($atkstate)
{
case 1 :
$sql="INSERT INTO commentary (postdate,section,author,comment) VALUES (now(),'expedition_wastes','1','/msg Wie aus dem Nichts taucht eine Gruppe feindlicher K�mpfer auf und st�rmt in Richtung Lager vor.`0')";
db_query($sql) or die(db_error(LINK));
break;
}

if ($atkstate<=0)
   {
     $DDL_opps=(e_rand(2,4)+$fighters);
     savesetting("DDL_opps",$DDL_opps);
     savesetting("DDL_comments","0");
     $chance=e_rand(0,3);
     $DDL_comments_req=getsetting("DDL_comments_req",5);
     savesetting("DDL_comments_req_act",($DDL_comments_req-1)+$chance);
   }
}

output('`4Anwesende: '.$fighters.'`n');
if ($DDL_opps>0)
  {
//    output('`$Anzahl der Gegner: '.$DDL_opps.'`n');
    addnav('K�mpfen','expedition.php?op=opponent');
  }
else
  {
//    output('`$Posts bis Erscheinen der n�chsten Gegnergruppe: '.($amount_req-$amount).'`n`n`&');
  }
}
viewcommentary('expedition_wastes','Sagen',25,"sagt");
}

addnav('Wer ist hier?');
addnav('Umsehen','expedition.php?op=whosthere&where=7&ret='.URLEncode($_SERVER['REQUEST_URI']));
addnav('Zur�ck');
addnav('Zum Zeltlager','expedition.php');
break;

case 'opponent' :
$session['user']['ddl_location'] = 7;
 $opponent=e_rand(1,8);
 if ($opponent<=3)
 {
 $name="Sp�her aus den Dunklen Landen";
 $weapon="F�uste";
 $atk=0.7;
 $def=0.85;
 $hp=0.7;
 }
 elseif ($opponent>3 && $opponent<=5)
 {
 $name="Pl�nderer aus den Dunklen Landen";
 $weapon="Klauenhandschuhe";
 $atk=0.85;
 $def=0.9;
 $hp=0.75;
 }
 elseif ($opponent>5 && $opponent<=7)
 {
 $name="Soldat aus den Dunklen Landen";
 $weapon="Kristallspeer";
 $atk=1.2;
 $def=1;
 $hp=0.8;
 }
 elseif ($opponent==8)
 {
 $name="Kommandant aus den Dunklen Landen";
 $weapon="Schattenklinge";
 $atk=1.4;
 $def=1.3;
 $hp=0.9;
 }

 $badguy = array(
	"creaturename"=>$name
	,"creaturelevel"=>$session['user']['level']
	,"creatureweapon"=>$weapon
	,"creatureattack"=>$session['user']['attack']*$atk
	,"creaturedefense"=>$session['user']['defence']*$def
	,"creaturehealth"=>$session['user']['maxhitpoints']*$hp
	,"diddamage"=>0);

	$session['user']['badguy']=createstring($badguy);

    $sql="INSERT INTO commentary (postdate,section,author,comment) VALUES (now(),'expedition_wastes',".$session[user][acctid].",'/me `&stellt sich `^".$badguy['creaturename']."`& entgegen.`0')";
    db_query($sql) or die(db_error(LINK));

redirect("expedition.php?op=fight");
break;

case 'risestate' :
$state = getsetting("DDL-state",6);
$newstate=$state+=1;
if ($newstate>11) $newstate=11;
savesetting('DDL-state',$newstate);
redirect('expedition.php');
break;

case 'lowerstate' :
$state = getsetting("DDL-state",6);
$newstate=$state-=1;
if ($newstate<1) $newstate=1;
savesetting("DDL-state",$newstate);
redirect('expedition.php');
break;

case 'order' :
$neworder=$_GET[nbr];
savesetting("DDL-order",$neworder);
redirect('expedition.php');
break;

case 'run' :
page_header('Expedition in die dunklen Lande - Kampf');
	if (e_rand()%3 == 0)
	{
        include ("battle.php");
		addnews_ddl($session['user']['name']." `that heute seinen Stellung verlassen und ist feige vor `^".$badguy['creaturename']." `tdavon gelaufen!");
		$sql="INSERT INTO commentary (postdate,section,author,comment) VALUES (now(),'expedition_wastes',".$session[user][acctid].",'/me `4fl�chtet aus der Schlacht!`0')";
        db_query($sql) or die(db_error(LINK));
		$badguy=array();
		$session['user']['badguy']="";

        $balance=getsetting("DDL-balance","0");
        $balance_lose=getsetting("DDL_balance_lose",-6);
        $balance-=3;
		savesetting("DDL-balance",$balance);
		$order=getsetting("DDL-order",2);
		
if ($balance<=$balance_lose && $order==1)
        {
          output('`4`n`nDie Verteidigung ist misslungen! Der Feind ist durchgebrochen!`n');
          addnews_ddl("`4Heute wurden wir vom Feind zur�ck gedr�ngt!`&`n`&Neuer Tagesbefehl : Warten auf Weiteres!`&");
          $sql="INSERT INTO commentary (postdate,section,author,comment) VALUES (now(),'expedition_wastes','1','/msg `2Unsere Verteidigung wurde �berrant! Der Feind ist durchgebrochen.`0')";
          db_query($sql) or die(db_error(LINK));
          // Rundmail ?
          savesetting("DDL-balance","0");
          savesetting("DDL-order",2);
          savesetting("DDL_act_order","0");
          savesetting("DDL_opps","0");
          $state=getsetting("DDL-state",6);
          $state--;
          if ($state<=1) // Niederlage ?
          {
             output('`4`n`nUnser Lager wurde zerst�rt!`n');
             addnews_ddl("`4Flieht um Euer Leben! Unser Lager wurde zerst�rt!`&");
             $sql="INSERT INTO commentary (postdate,section,author,comment) VALUES (now(),'expedition_wastes','1','/msg `4Unser Lager wurde vollst�ndig zerst�rt.`0')";
             db_query($sql) or die(db_error(LINK));
          }
          savesetting("DDL-state",$state);
            savesetting("DDL_opps","0");
        }
        elseif ($balance<=$balance_lose && $order==3)
        {
          output('`&`n`nUnser Angriff ist gescheitert! Der Feind hat die Stellungen gehalten!`n');
          $sql="INSERT INTO commentary (postdate,section,author,comment) VALUES (now(),'expedition_wastes','1','/msg `4Unser Angriff wurde abgewehrt!`0')";
          db_query($sql) or die(db_error(LINK));
          addnews_ddl("`@Heute wurde unser Angriff abgewehrt!`&`n`&Neuer Tagesbefehl : Warten auf Weiteres!`&");
          // Rundmail ?
          savesetting("DDL-balance","0");
          savesetting("DDL-order",2);
          savesetting("DDL_act_order","0");
          savesetting("DDL_opps","0");
          $state=getsetting("DDL-state",6);
        }
		
		redirect('expedition.php');

	}
	else
	{
		output('`c`b`$Dir ist es nicht gelungen deinem Gegner zu entkommen!`0`b`c');
		$battle = true;
	}
break;

default :
page_header('Expedition in die dunklen Lande - das Zeltlager');
$state = getsetting("DDL-state",6);
$order = getsetting("DDL-order",2);

$session['user']['ddl_location'] = 1;
$sql = "SELECT accounts.acctid,accounts.login FROM account_extra_info JOIN accounts USING (acctid)
 WHERE DDL_tent=".$session[user][acctid];
$resultt = db_query($sql) or die(db_error(LINK));
output("`8Nach langem Ritt, weit hinaus - weg von ".getsetting('townname','Atrahor')." - l�sst du dich ersch�pft vom R�cken des Reittieres gleiten und kommst sanft auf dem Grasboden auf. Ein Knappe eilt herbei und bringt dein Tier zu einem Unterstand. Endlich hast du Zeit, das Zeltlager n�her zu erkunden und n�herst dich zuerst der Stelle, von der aus du den meisten L�rm vernimmst: dem Gemeinschaftszelt. Auf dem Weg dorthin gehst du an mehreren Zelten vorbei, deren Eing�nge jeweils von zwei Wachen umstellt sind. Aus einem h�rst du ged�mpfte Gespr�che, die anscheinend von den Leitern der Expedition stammen und deshalb nicht f�r deine Ohre bestimmt sind. Aus einem anderen vernimmst du metallisches Klirren, so als w�rden Waffen und R�stungen gestapelt werden. Bevor du das gr��te Zelt erreichst, betrachtest du kurz die Umgebung, in der das Lager errichtet wurde: Die Zelte sind auf einer Seite umgeben von den Steilklippen eines kleinen Gebirges, von denen sich vereinzelt ein Wasserfall seinen Weg zu einem See am Fu�e des Felsens sucht. Als du den Blick zur anderen Seite wendest blickst du auf eine scheinbar endlos weite Ebene. Einzelne B�ume kannst du lediglich am Rande des Sees ausmachen. Doch am meisten verwirrt dich der immer wolkenverhangene, dunkle Himmel, der das ganze Land in einen unheimlichen Schatten h�llt...`n`n");
if ($state==11) // Feindliches Lager zerst�rt
{
output('`^Anders als sonst fallen dir diesmal viele bunte Flaggen auf, die rund um das Lager gehisst wurden. Auch die Wachen haben ihre Posten verlassen, von �berall her ist ausgelassener Gesang und euphorisches Jubeln zu h�ren - `@Ihr habt das feindliche Lager zerst�rt und den Sieg davon getragen!`n`^Doch schon bald wird der Feind wiederkehren und ein neues Lager errichten...`n`n');
}
$w = get_weather();
output('`2Das Wetter: `6'.$w['name'].'`0.`n');

switch ($order)
{
  case 1 :
  $otext=" `4Stellungen halten!`0";
  break;
  case 2 :
  $otext=" `&Warten auf Weiteres!`0";
  break;
  case 3 :
  $otext=" `^Angriff!`0";
  break;
}

switch ($state)
{
  case 1 :
  $text="`4Das Lager wurde zerst�rt und die Expedition ist gescheitert!`0";
  break;
  case 2 :
  $text="`\$Das Lager wird besetzt und steht unter heftigem Abgriff!`0";
  break;
  case 3 :
  $text="`\$Das Lager wird besetzt!`0";
  break;
  case 4 :
  $text="`^Die dunklen Scharen r�cken auf das Lager vor!`0";
  break;
  case 5 :
  $text="`^Die dunklen Scharen haben die Grenze passiert!`0";
  break;
  case 6 :
  $text="`@Alles ist ruhig, es gibt keine feindseligen Kr�fte in direkter N�he zum Lager.`0";
  break;
  case 7 :
  $text="`@Unsere Sp�her haben die Grenze passiert.`0";
  break;
  case 8 :
  $text="`#Unsere K�mpfer r�cken auf den Posten der dunklen Scharen vor!`0";
  break;
  case 9;
  $text="`#Unsere K�mpfer belagern den Posten der dunklen Scharen!`0";
  break;
  case 10 :
  $text="`#Unsere K�mpfern belagern den Posten der dunklen Scharen und greifen an!`0";
  break;
  case 11 :
  $text="`2Sieg! Der Posten der dunklen Scharen wurde vernichtet!`0";
  break;
}
output ('`&Lage: '.$text.'`n');
output ('`&Tagesbefehl:'.$otext.'`n');
$sql = "SELECT * FROM ddlnews ORDER BY newsid DESC LIMIT 1";
$result = db_query($sql) or die(db_error(LINK));
$rown = db_fetch_assoc($result);
output('`n`c`&Letzte Meldung: '.$rown['newstext'].'`c`n');
addnav('Aktionen');
addnav('Erkundung','expedition.php?op=explore');
addnav('Schatzsuche','expedition.php?op=search');
addnav('Gel�nde auskundschaften','expedition.php?op=claim');
addnav('Briefe in die Heimat','expedition.php?op=letter');
addnav('Zelte');
addnav('Expeditionsleiter','expedition.php?op=chief');
addnav('Gemeinschaftszelt','expedition.php?op=inn');
addnav('Lagerarzt','expedition.php?op=doc');
addnav('Lagerwache','expedition.php?op=guards');
addnav('Dein Zelt','expedition.php?op=mytent');
$max = db_num_rows($resultt);
if ($max>0)
  {
    for($i=0;$i<$max;$i++){
    $rowt = db_fetch_assoc($resultt);
    addnav($rowt['login'].'\'s Zelt','expedition.php?op=othertent&who='.$rowt[acctid]);
    }
  }
if (su_check(SU_RIGHT_EXPEDITION_ADMIN))
{
addnav('Mod-Aktionen');
addnav('Zustand erh�hen','expedition.php?op=risestate');
addnav('Zustand senken','expedition.php?op=lowerstate');
addnav('Befehl zum Angriff','expedition.php?op=order&nbr=3');
addnav('Befehl zum Nichtstun','expedition.php?op=order&nbr=2');
addnav('Befehl zur Verteidigung','expedition.php?op=order&nbr=1');
}
addnav('Besondere Orte');
addnav('Antreteplatz','expedition.php?op=milplace');
addnav('Hei�e Quellen','expedition.php?op=pools');
addnav('Ein�de','expedition.php?op=wastes');
addnav('Tropfsteinh�hle','expedition.php?op=cave');
addnav('Wer ist hier?');
addnav('Umsehen','expedition.php?op=whosthere&where=1&ret='.URLEncode($_SERVER['REQUEST_URI']));
addnav('Reisen');
addnav('Zur�ck nach '.getsetting('townname','Atrahor'),'village.php');
addnav('In die Felder (logout)','login.php?op=logout',true);
addnav('','user.php');
addnav('','bios.php');
output('`%`2Du h�rst einige der anderen Teilnehmer dieser Expedition schwatzen:`n');
viewcommentary('expedition_main','Mitreden',25);

break;
}

if ($battle) {
$session['user']['ddl_location'] = 7;
include ("battle.php");
	if ($victory)
	{
		output("`n`&Du hast `^".$badguy['creaturename']."`& geschlagen.`0");
		$DDL_opps=getsetting("DDL_opps","0");
		$DDL_opps--;
		if ($DDL_opps<0) $DDL_opps=0;
		savesetting("DDL_opps","$DDL_opps");

        if (e_rand(1,2)==1) {
        $sql="INSERT INTO commentary (postdate,section,author,comment) VALUES (now(),'expedition_wastes',".$session[user][acctid].",'/me `@hat `^".$badguy['creaturename']."`@ nieder gestreckt.`0')"; } else
        {
        $sql="INSERT INTO commentary (postdate,section,author,comment) VALUES (now(),'expedition_wastes',".$session[user][acctid].",'/me `@gelang es, `^".$badguy['creaturename']."`@ in die Flucht zu schlagen.`0')";
        }
        db_query($sql) or die(db_error(LINK));
		addnews_ddl($session['user']['name']." `&hat `#".$badguy['creaturename']." `&im Kampf geschlagen.`0");

        switch ($badguy['creaturename'])
        {
          case 'Kommandant aus den Dunklen Landen' :
          $points=3;
          $wounds=2;
          break;
          
          case 'Soldat aus den Dunklen Landen' :
          $points=2;
          $wounds=2;
          break;
          
          default :
          $points=1;
          $wounds=1;
          break;

        }

 	    if ($badguy['diddamage']==0)
         {
          output('`n`@Perfekter Kampf!`n');
          $points*=2;
         }
          else
         {
          if (e_rand(1,2)==2)
          {
            if ($wounds==1) { $attr="leichte "; }
          output('`n`^Du gewinnst den Kampf, erleidest aber eine '.$attr.'Verwundung!`0`n');

          $sql = "SELECT wounds FROM account_extra_info WHERE acctid=".$session[user][acctid]."";
          $result = db_query($sql) or die(db_error(LINK));
          $row = db_fetch_assoc($result);

             $new_wounds=$row['wounds']+$wounds;
             if ($new_wounds>5) { $new_wounds=5; }
             $sql = "UPDATE account_extra_info SET wounds=$new_wounds WHERE acctid=".$session[user][acctid]."";
             db_query($sql) or die(db_error(LINK));

          if ($new_wounds>=4) // 4x klein verletzt
          {
             $sql="INSERT INTO commentary (postdate,section,author,comment) VALUES (now(),'expedition_wastes',".$session[user][acctid].",'/me `&wird vom Feld-Sanit�ter verletzt aus der Gefahrenzone gebracht.`0')";
             db_query($sql) or die(db_error(LINK));
          }
        }
        }
        $badguy=array();
		$session['user']['badguy']="";
		$balance=getsetting("DDL-balance","0");
		$order=getsetting("DDL-order",2);
        $balance_win=getsetting("DDL_balance_win",25);
        $balance+=$points;
        savesetting("DDL-balance","$balance");
        if ($balance>=$balance_win && $order==3)
        {
          output('`&`n`nDer Angriff ist gegl�ckt! Der Feind wurde zur�ck gedr�ngt!`n');
          addnews_ddl("`@Heute gelang uns bei unserem Angriff ein Vorstoss!`&`n`&Neuer Tagesbefehl : Warten auf Weiteres!`&");
          $sql="INSERT INTO commentary (postdate,section,author,comment) VALUES (now(),'expedition_wastes','1','/msg `2Unser Angriff war ein Erfolg! Der Feind wurde zur�ck geworfen.`0')";
          db_query($sql) or die(db_error(LINK));
          $medalpoints=getsetting("DDL-medal",10);
          $medalpoints+=2;
          savesetting("DDL-medal",$medalpoints);
          // Rundmail ?
          savesetting("DDL-balance","0");
          savesetting("DDL-order",2);
          savesetting("DDL_act_order","0");
          savesetting("DDL_opps","0");
          $state=getsetting("DDL-state",6);
          $state++;
          if ($state>=11) // Sieg ?
          {
             output('`&`n`nDer feindliche Posten wurde zerst�rt!`n');
             addnews_ddl("`@Sieg! Der feindliche Posten wurde zerst�rt!`&");
             $sql="INSERT INTO commentary (postdate,section,author,comment) VALUES (now(),'expedition_wastes','1','/msg `^Sieg! Des feindliche Posten wurde zerst�rt. Von �berall her erklingen Fanfaren.`0')";
             db_query($sql) or die(db_error(LINK));
             $medalpoints=getsetting("DDL-medal",10);
             $medalpoints+=3;
             savesetting("DDL-medal",$medalpoints);
          }
          savesetting("DDL-state",$state);
            savesetting("DDL_opps","0");
        }
        elseif ($balance>=$balance_win && $order==1)
        {
          output('`&`n`nDer feindliche Angriff ist gescheitert! Wir haben die Stellungen gehalten!`n');
          $sql="INSERT INTO commentary (postdate,section,author,comment) VALUES (now(),'expedition_wastes','1','/msg `2Der Angriff des Feindes wurde erfolgreich abgewehrt!`0')";
          db_query($sql) or die(db_error(LINK));
          addnews_ddl("`@Heute wurde der Angriff des Feindes abgewehrt!`&`n`&Neuer Tagesbefehl : Warten auf Weiteres!`&");
          // Rundmail ?
          savesetting("DDL-balance","0");
          savesetting("DDL-order",2);
          savesetting("DDL_act_order","0");
          savesetting("DDL_opps","0");
          $state=getsetting("DDL-state",6);
        }
        addnav('Weiter','expedition.php?op=wastes');
    }
elseif($defeat)
	{
		output("`n`4Du verlierst den Kampf und wirst schwer verletzt.`0`n`&Als du aus der Ohnmacht erwachst, stellst du fest, dass du dich beim Lagerarzt befindest.`0`n");
		 if (e_rand(1,2)==1) {
        $sql="INSERT INTO commentary (postdate,section,author,comment) VALUES (now(),'expedition_wastes',".$session[user][acctid].",'/me `4wird von `^".$badguy['creaturename']."`4 niedergeschmettert und bleibt regungslos liegen.`0')"; } else
        {
        $sql="INSERT INTO commentary (postdate,section,author,comment) VALUES (now(),'expedition_wastes',".$session[user][acctid].",'/me `4geht schwer verletzt zu Boden!`0')";
        }
        db_query($sql) or die(db_error(LINK));
		$session['user']['hitpoints']=1;
		addnews_ddl($session['user']['name']." `4wurde heute im Kampf schwer verwundet!`0");
		$sql = "UPDATE account_extra_info SET wounds=5 WHERE acctid=".$session[user][acctid]."";
        db_query($sql) or die(db_error(LINK));

        $balance=getsetting("DDL-balance","0");
        $balance--;
        savesetting("DDL-balance",$balance);
		$order=getsetting("DDL-order",2);
        $balance_lose=getsetting("DDL_balance_lose",-6);

if ($balance<=$balance_lose && $order==1)
        {
          output('`4`n`nDie Verteidigung ist misslungen! Der Feind ist durchgebrochen!`n');
          addnews_ddl("`4Heute wurden wir vom Feind zur�ck gedr�ngt!`&`n`&Neuer Tagesbefehl : Warten auf Weiteres!`&");
          $sql="INSERT INTO commentary (postdate,section,author,comment) VALUES (now(),'expedition_wastes','1','/msg `2Unsere Verteidigung wurde �berrant! Der Feind ist durchgebrochen.`0')";
          db_query($sql) or die(db_error(LINK));
          // Rundmail ?
          savesetting("DDL-balance","0");
          savesetting("DDL-order",2);
          savesetting("DDL_act_order","0");
          savesetting("DDL_opps","0");
          $state=getsetting("DDL-state",6);
          $state--;
          if ($state<=1) // Niederlage ?
          {
             output('`4`n`nUnser Lager wurde zerst�rt!`n');
             addnews_ddl("`4Flieht um Euer Leben! Unser Lager wurde zerst�rt!`&");
             $sql="INSERT INTO commentary (postdate,section,author,comment) VALUES (now(),'expedition_wastes','1','/msg `4Unser Lager wurde vollst�ndig zerst�rt.`0')";
             db_query($sql) or die(db_error(LINK));
          }
          savesetting("DDL-state",$state);
            savesetting("DDL_opps","0");
        }
        elseif ($balance<=$balance_lose && $order==3)
        {
          output('`&`n`nUnser Angriff ist gescheitert! Der Feind hat die Stellungen gehalten!`n');
          $sql="INSERT INTO commentary (postdate,section,author,comment) VALUES (now(),'expedition_wastes','1','/msg `4Unser Angriff wurde abgewehrt!`0')";
          db_query($sql) or die(db_error(LINK));
          addnews_ddl("`@Heute wurde unser Angriff abgewehrt!`&`n`&Neuer Tagesbefehl : Warten auf Weiteres!`&");
          // Rundmail ?
          savesetting("DDL-balance","0");
          savesetting("DDL-order",2);
          savesetting("DDL_act_order","0");
          savesetting("DDL_opps","0");
          $state=getsetting("DDL-state",6);
        }

	    addnav('Weiter','expedition.php?op=doc');
	}
else
	{
		fightnav();
	}
}
}
page_footer();
?>
