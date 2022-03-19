<?php
// Stadtwachen-Addon : Änderungen in houses.php, bio.php, pvp.php, inn.php, configuration.php, dorfamt.php
// Benötigt [profession] (shortint, unsigned) in [User]
// by Maris (Maraxxus@gmx.de)
// 28.5.05: mod by tcb: Verlagert aus Dorfamt in eigene Datei, Bewerbungssystem, Schwarzes Brett verändert

require_once "common.php";
require_once(LIB_PATH.'board.lib.php');

page_header("Die Stadtwache");

if (!isset($session)) exit();

$op = ($_GET['op']) ? $_GET['op'] : "hq";

switch($op) {
		
	case 'bewerben':
	
		output("`&Mit zittrigen Händen nimmst du die Klinke einer schweren Eichentür in die Hand und stößt sie auf. Ein alter Mann mit Backenbart sitzt hinter einem Schreibtisch und mustert dich eindringlich. \"`#Name! Rang!`&\" ruft er dir scharf entgegen. Nachdem du ihm gesagt hast was er wissen wollte kneift er die Augen zusammen.`n`n");
		$maxamount = getsetting("numberofguards",10);
		$reqdk = getsetting("guardreq",30);
		
		$sql = "SELECT profession FROM accounts WHERE profession=".PROF_GUARD_HEAD." OR profession=".PROF_GUARD;
		$result = db_query($sql) or die(db_error(LINK));
		if ((db_num_rows($result)) < $maxamount) {
		
			if (($session['user']['profession']==PROF_GUARD_ENT) || ($session['user']['profession']==4)) {
				output("\"`# ".($session['user']['name'])."! So sehr ich Euren Wunsch nachempfinden kann wieder dienen zu dürfen muss ich Euch jedoch enttäuschen. Ihr hattet Eure Chance! Und nun verlasst mein Büro!`&\""); 
			}
			else {
				output("\"`# ".($session['user']['name'])."!`# Ich hoffe Ihr wisst worauf Ihr Euch hier einlasst? Der Dienst in der Stadtwache ist hart und entbehrungsreich. Und an Euch werden besondere Forderungen gestellt : Ihr müsst sowohl ruhmreich wie auch von höchstem Ansehen sein und in Eurem Verhalten ein Vorbild!`&\"`n");
		
				if (($session['user']['dragonkills']) >= $reqdk) {
					if ($session['user']['reputation']>=50) {
						output ("\"`#Ich sehe, ich sehe... Ihr seid sowohl ruhmreich, wie auch von allerhöchstem Ansehen! Das ist gut, sehr gut. Meinetwegen könnt Ihr sofort anfangen. Doch wisset, dass Ihr als Stadtwache nicht nur Recht, sondern auch Pflichten habt. Es ist Euch strengstens untersagt mit zwielichtigen Gesellen Kontakte zu knüpfen, auch nicht zur Täuschung! Ihr müsst Euch weiterhin mit Kopfgeldern zufrieden geben und dürft keine Beute an Euren Gegnern machen! Eurem Hauptmann habt Ihr Folge zu leisten! Sollte man Euch bei irgendeinem Verstoß oder irgendeiner Unehrenhaftigkeit erwischen, seid Ihr für lange Zeit Stadtwache gewesen! Sind wir uns da einige?`nAlso, wollt Ihr noch immer ?`&\"");
						addnav("Ja, Wache werden","wache.php?op=bewerben_ok");
						addnav("Nein, falsche Tür...","dorfamt.php");
		  			}
		   			else {
						output ("\"`#Ruhmreich seid mehr als es von Nöten wäre, doch fürchte ich, dass Euch die Leute nicht trauen würden, wenn Ihr plötzlich in Uniform daher kämet. Tut mal etwas für Euer Ansehen und versucht es dann noch einmal!`&\"");
		   			}
				}
				else {
					output ("\"`#Ihr seid zwar ruhmreich, doch wie es mir scheint nicht ruhmreich genug. Ihr solltet noch mehr Ruhm im Kampf gegen den Drachen erlangen und es dann noch einmal versuchen!`&\"");
				}
			}	// Kein entlassener 
		}	// Noch nicht zu viele 
		else {
			output ("\"`#Es tut mir sehr leid, aber das Dorf hat zur Zeit genügend Stadtwachen. Versucht es doch später noch einmal!`&\"");
		}
		
		addnav("Zurück","dorfamt.php");
			
		break;
		
	case 'bewerben_ok':
		
		output("`&Du überreichst dem alten Mann dein Bewerbungsschreiben. Dieser verstaut es unter einem hohen Stapel Pergamenten und meint: \"Wir werden auf dich zurückkommen!\"");
		$session['user']['profession']=PROF_GUARD_NEW;
		$sql = "SELECT acctid FROM accounts WHERE profession=".PROF_GUARD_HEAD." ORDER BY loggedin DESC, RAND() LIMIT 1";
		$res = db_query($sql);
		if(db_num_rows($res)) {
			$w = db_fetch_assoc($res);
			systemmail($w['acctid'],"`&Neue Bewerbung!`0","`&".$session['user']['name']."`& hat sich für die Stadtwache beworben. Du solltest seine Bewerbung überprüfen und ihn gegegebenfalls einstellen.");
		}
				
		addnav("Zurück","dorfamt.php");		
		
		break;
		
	case 'bewerben_abbr':
		
		$session['user']['profession'] = 0;
		output("Du ziehst deine Bewerbung zurück.");
		addnav("Zurück","dorfamt.php");
		
		break;
		
	case 'aufn':
		
		$pid = (int)$_GET['id'];
		
		$sql = "SELECT COUNT(*) AS anzahl FROM accounts WHERE (profession=".PROF_GUARD_HEAD." OR profession=".PROF_GUARD.")";
		$res = db_query($sql);
		$p = db_fetch_assoc($res);
		
		if($p['anzahl'] >= getsetting("numberofguards",10)) {
			output("Es gibt bereits ".$p['anzahl']." Wachen! Mehr sind zur Zeit nicht möglich.");
			addnav("Zurück","wache.php?op=listg");
		}
		else {
		
			$sql = "UPDATE accounts SET profession = ".PROF_GUARD."  
					WHERE acctid=".$pid;
			db_query($sql) or die (db_error(LINK));
			
			$sql = "SELECT name FROM accounts WHERE acctid=".$pid;
			$res = db_query($sql);
			$p = db_fetch_assoc($res);
			
			systemmail($pid,"Du wurdest aufgenommen!",$session['user']['name']."`& hat deine Bewerbung zur Aufnahme in die Stadtwache angenommen. Damit bist du vom heutigen Tage an offiziell Hüter für Recht und Ordnung!");
			
			$sql = "INSERT INTO news SET newstext = '".addslashes($p['name'])." `&wurde heute offiziell in die ehrenvolle Gemeinschaft der Stadtwachen aufgenommen!',newsdate=NOW(),accountid=".$pid;
			db_query($sql) or die (db_error(LINK));
			
			addhistory('`2Aufnahme in die Stadtwache',1,$pid);
			
			addnav("Willkommen!","wache.php?op=listg");
			
			output("Die neue Stadtwache ist jetzt aufgenommen!");
		}
		
		break;
		
	case 'abl':
		
		$pid = (int)$_GET['id'];
		
		$sql = "UPDATE accounts SET profession = 0  
				WHERE acctid=".$pid;
		db_query($sql) or die (db_error(LINK));
							
		systemmail($pid,"Deine Bewerbung wurde abgelehnt!",$session['user']['name']."`& hat deine Bewerbung zur Aufnahme in die Stadtwache abgelehnt.");
			
		addnav("Zurück","wache.php?op=listg");
		
		break;
	
	case 'entlassen':
		
		$pid = (int)$_GET['id'];
	
		$sql = "UPDATE accounts SET profession = 0  
				WHERE acctid=".$pid;
		db_query($sql) or die (db_error(LINK));
			
		$sql = "SELECT name FROM accounts WHERE acctid=".$pid;
		$res = db_query($sql);
		$p = db_fetch_assoc($res);
			
		systemmail($pid,"Du wurdest entlassen!",$session['user']['name']."`& hat dich aus der Stadtwache entlassen!");
			
		$sql = "INSERT INTO news SET newstext = '".addslashes($p['name'])." `&wurde heute aus der ehrenvollen Gemeinschaft der Stadtwachen entlassen!',newsdate=NOW(),accountid=".$pid;
		db_query($sql) or die (db_error(LINK));
		
		addhistory('`$Entlassung aus der Stadtwache',1,$pid);
			
		addnav("Weiter","wache.php?op=listg");
			
		output("Die Wache wurde entlassen!");
				
		break;
	
	case 'leave':
		
		output ("`&Mit zitternden Knien betrittst du das Zimmer, in der der ältere Herr mit dem Backenbart wie gewohnt hinter seinem Schreibtisch sitzt. Als du eintrittst und ihm Meldung machst bittet er dich Platz zu nehmen und schau dich erwartungsvoll an.`nWillst du wirklich die Stadtwache verlassen?");
		addnav("Ja, austreten!","wache.php?op=leave_ok");
		addnav("NEIN. Dabei bleiben","dorfamt.php");
		
		break;
		
	case 'leave_ok':
		
		output ("`&Du bittest um deine Entlassung und der ältere Herr erledigt sichtlich schweren Herzens alle Formalitäten \"`#Wirklich schade, dass Ihr geht! Ich danke Euch vielmals für die treuen Dienste, die Ihr dem Dorf geleistet habt und werde Euch nie vergessen! Beachtet, dass Eure Entlassung erst mit Beginn des morgigen Tages wirksam wird. Für heute seid Ihr jedoch beurlaubt.`&\"");
		addnews("".$session[user][name]."`@ hat die Stadtwache verlassen. Die Gaunerwelt atmet auf.");
		
		addhistory('`2Austritt aus der Stadtwache');
		
		$session['user']['profession'] = PROF_GUARD_ENT;
		addnav("Zurück ins Zivilleben","dorfamt.php");		
		
		break;
	
	case 'hq':
		
		if ($session['user']['profession']==PROF_GUARD || $session['user']['profession']==PROF_GUARD_HEAD || su_check(SU_RIGHT_COMMENT)) addcommentary();
		output("`b`c`2Das Hauptquartier der Stadtwachen`0`c`b");
		output("`&Du betrittst vornehme Räumlichkeiten, die dir ein gewisses Gefühl von Ehrfurcht und auch Respekt vermitteln. An den Wänden hängen Schwerter und Trophäen. Ritterrüstungen säumen den holzvertäfelten Raum. Ein großer runder, edler Eichentisch steht genau in der Mitte des Hauptraumes. Umhänge und Rüstungsteile, achtlos über Stühle gehängt, kannst du aus den Augenwinkeln erkennen. Ein großer Kupferstich an der Stirnwand des Hauptraumes erinnert dich an deine Pflichten als Wächter dieses Dorfes :`n`n ");
		output ("`#Ehre, Gerechtigkeit, Ritterlichkeit, Beständigkeit und Disziplin sollen den Wächter der Stadt ".getsetting('townname','Atrahor')." zu einem Symbol der Sicherheit für ihre Bürger machen!`&");
		addnav("Rekrutierungsliste","wache.php?op=listg");
//		addnav("Kopfgeldliste","wache.php?op=listh");
		addnav("Urteile","wache.php?op=sentences");
		addnav("Schwarzes Brett","wache.php?op=board");
		// addnav("Letzte Neuigkeiten","wache.php?op=news");
		addnav("Zurück","dorfamt.php");
		output("`n`n");
		viewcommentary("guards","Melden:",30,"meldet");
			
		break;
		
	case 'board':
		
		output ("`&Du stellst dich vor das große Brett und schaust ob eine neue Mitteilung vorliegt.`n");
		//addcommentary();
		// if (($session['user']['profession']==2) || ($session['user']['superuser']>1)) {
		output ("`tDu kannst eine Notiz hinterlassen oder entfernen.`n`n");
		//viewcommentary("guardboard"," ",25,"schrieb");
		// }
		// if ($session['user']['profession']==1) { viewcommentary("guardboard","Notiz hinterlassen:",25,"schrieb"); }
		
		if($_GET['board_action'] == "add") {
			
			board_add('wache');
			
			redirect("wache.php?op=board");
			
		}
		else {
								
			board_view_form('Hinzufügen','');
						
			board_view('wache',2,'','',true,true,true);
		}
				
		addnav("Zurück","wache.php?op=hq");		
		
		break;
	
	case 'listg':
		$admin = ($session['user']['profession'] == 2 || su_check(SU_RIGHT_DEBUG)) ? true : false;	
			
		output("<span style='color: #9900FF'>",true);
		$sql = "SELECT name,acctid,loggedin,dragonkills,login,level,profession FROM accounts WHERE profession=1 OR profession=2 OR profession=3 OR profession=5
				ORDER BY profession DESC, level DESC";
				$result = db_query($sql) or die(db_error(LINK));
		output ("`&Folgende Helden haben sich der Stadtwache angeschlossen:`n`n");
		output("<table border='0' cellpadding='5' cellspacing='2' bgcolor='#999999'><tr class='trhead'><td>Name</td><td>Level</td><td>Funktion</td><td>",true);
		if($admin) {output('Aktionen',true);}
		output("</td><td>Status</td></tr>",true);
		$lst=0;
		$dks=0;
		for ($i=0;$i<db_num_rows($result);$i++){
			$row = db_fetch_assoc($result);
			$lst+=1;
			$dks+=$row['dragonkills'];
			output("<tr class='".($lst%2?"trlight":"trdark")."'><td><a href=\"mail.php?op=write&to=".rawurlencode($row['login'])."\" target=\"_blank\" onClick=\"".popup("mail.php?op=write&to=".rawurlencode($row['login'])."").";return false;\"><img src='images/newscroll.GIF' width='16' height='16' alt='Mail schreiben' border='0'></a><a href='bio.php?char=".rawurlencode($row['login'])."&ret=".URLEncode($_SERVER['REQUEST_URI'])."'>$row[name]</a></td><td>$row[level]</td><td>",true);
			addnav("","bio.php?char=".rawurlencode($row['login'])."&ret=".URLEncode($_SERVER['REQUEST_URI']));
			if ($row['profession']==1) {
				
				output("`#Stadtwache`&</td><td>",true);
				if($admin) {output('<a href="wache.php?op=entlassen&id='.$row['acctid'].'">Entlassen</a>',true);addnav("","wache.php?op=entlassen&id=".$row['acctid']);}
				}
			if ($row['profession']==PROF_GUARD_HEAD) {output("`4Hauptmann`&</td><td>",true);}
			if ($row['profession']==PROF_GUARD_ENT) {output("`6Entlassung läuft`&</td><td>",true);}
			if ($row['profession']==PROF_GUARD_NEW) {
			
				output("`@Bittet um Aufnahme`&</td><td>",true);
				if($admin) {
					output('<a href="wache.php?op=aufn&id='.$row['acctid'].'">Aufnehmen</a>`n',true);
					addnav("","wache.php?op=aufn&id=".$row['acctid']);
					output('<a href="wache.php?op=abl&id='.$row['acctid'].'">Ablehnen</a>',true);
					addnav("","wache.php?op=abl&id=".$row['acctid']);
					}
				
				}
			output("</td><td>",true);
			if ($row['loggedin']) { output("`@online`&",true);} else { output("`4offline`&",true);}
			output("</td></tr>",true); 
		}
		db_free_result($result);
		output("</table>",true);
		output("</span>",true);
		output("<big>`n`@Gemeinsame Drachenkills der Stadtwache : `^$dks`n`n`&<small>",true);
		addnav("Zurück","wache.php?op=hq");		
		
		break;
		
	case 'listh':
		
		output("<span style='color: #9900FF'>",true);
		output ("`&Die Kopfgeldliste:`n`n");
		
		$sql = "SELECT name,acctid,location,bounty,laston,alive,housekey,loggedin,login,level,activated FROM accounts WHERE bounty>0
				ORDER BY bounty DESC";
		$result = db_query($sql) or die(db_error(LINK));
		
		output("<table border='0' cellpadding='4' cellspacing='1' bgcolor='#999999'><tr class='trhead'><td>Kopfgeld</td><td>Level</td><td>Name</td><td>Ort</td><td>Lebt?</td></tr>",true);
		$lst=0;
		
		for ($i=0;$i<db_num_rows($result);$i++){
			$row = db_fetch_assoc($result);
			$loggedin=user_get_online(0,$row);
			$lst+=1;
			output("<tr class='".($lst%2?"trlight":"trdark")."'><td>".($row['bounty'])."</td><td>".($row['level'])."</td><td><a href='bio.php?char=".rawurlencode($row['login'])."&ret=".URLEncode($_SERVER['REQUEST_URI'])."'>$row[name]</a>",true);
			addnav("","bio.php?char=".rawurlencode($row['login'])."&ret=".URLEncode($_SERVER['REQUEST_URI']));
			output("</td><td>",true);
			
			if ($row['location']==USER_LOC_FIELDS) output($loggedin?"`@online":"`3Die Felder",true);
			
			if ($row['location']==USER_LOC_INN) output("`3Zimmer in Kneipe`0",true);
			if ($row['location']==USER_LOC_PRISON) output("`3Im Kerker`0",true);
			if ($row['location']==USER_LOC_HOUSE){
						$sql="SELECT hvalue FROM keylist WHERE owner=$row[acctid] AND hvalue>0";
						$result2 = db_query($sql) or die(db_error(LINK));
						$row2 = db_fetch_assoc($result2);
						$loc=$row2[hvalue]?$row2[hvalue]:$row[housekey];
			output ("Haus Nr. $loc",true);
		}
		output("</td><td>",true);
		if ($row['alive']) { output("`@lebt`&",true);} else { output("`4tot`&",true);}
		output("</td></tr>",true);
		}
		addnav("Zurück","wache.php?op=hq");
		db_free_result($result);
		output("</table>",true);
		output("</span>",true);		
		
		break;
	
	case 'sentences':

		output("<span style='color: #9900FF'>",true);
		output ("`&Die Richter haben folgende Urteile verhängt:`n`n");

        $sql = "SELECT account_extra_info.acctid,accounts.bounty,sentence FROM account_extra_info LEFT JOIN accounts ON accounts.acctid=account_extra_info.acctid WHERE sentence>0
				ORDER BY sentence DESC";
		$result = db_query($sql) or die(db_error(LINK));

		output("<table border='0' cellpadding='4' cellspacing='1' bgcolor='#999999'><tr class='trhead'><td>Strafe</td><td>Level</td><td>Name</td><td>Ort</td><td>Lebt?</td><td>Kopfgeld</td></tr>",true);
		$lst=0;

		for ($i=0;$i<db_num_rows($result);$i++){
			$row = db_fetch_assoc($result);
			
$sql1 = "SELECT name,acctid,location,bounty,laston,alive,housekey,loggedin,login,level,activated FROM accounts WHERE acctid=".$row['acctid']."";
$result1 = db_query($sql1) or die(db_error(LINK));
$row1 = db_fetch_assoc($result1);
			
			$loggedin=user_get_online(0,$row);
			$lst+=1;
			output("<tr class='".($lst%2?"trlight":"trdark")."'><td>".($row['sentence'])." Tage</td><td>".($row1['level'])."</td><td><a href='bio.php?char=".rawurlencode($row1['login'])."&ret=".URLEncode($_SERVER['REQUEST_URI'])."'>$row1[name]</a>",true);
			addnav("","bio.php?char=".rawurlencode($row1['login'])."&ret=".URLEncode($_SERVER['REQUEST_URI']));
			output("</td><td>",true);

			if ($row1['location']==USER_LOC_FIELDS) output($loggedin?"`@online":"`3Die Felder",true);

			if ($row1['location']==USER_LOC_INN) output("`3Zimmer in Kneipe`0",true);
			if ($row1['location']==USER_LOC_PRISON) output("`3Im Kerker`0",true);
			if ($row1['location']==USER_LOC_HOUSE){
						$sql="SELECT hvalue FROM keylist WHERE owner=$row[acctid] AND hvalue>0";
						$result2 = db_query($sql) or die(db_error(LINK));
						$row2 = db_fetch_assoc($result2);
						$loc=$row2[hvalue]?$row2[hvalue]:$row1[housekey];
						
     	                $sql="SELECT status FROM houses WHERE houseid=$loc ";
						$result3 = db_query($sql) or die(db_error(LINK));
						$row3 = db_fetch_assoc($result3);
                        $loc2= $row3['status'];
            if (($loc2<30) || ($loc2>39))
            { output ("Haus Nr. $loc",true); }
            else
            // Versteck, Refugium etc..
            { output ("untergetaucht",true); }
		}
		output("</td><td>",true);
		if ($row1['alive']) { output("`@lebt`&",true);} else { output("`4tot`&",true);}
		output("</td><td>".$row['bounty']."</td></tr>",true);
		}
		addnav("Zurück","wache.php?op=hq");
		db_free_result($result);
		output("</table>",true);
		output("</span>",true);

		break;
	
	case 'news':
		
		$daydiff = ($_GET['daydiff']) ? $_GET['daydiff'] : 0;
		$min = $daydiff-1;
			
		$sql = "SELECT newstext,newsdate FROM news WHERE 
					(newstext LIKE '%geflohen%' OR newstext LIKE '%einbruch%' OR newstext LIKE '%Zimmer in der Kneipe%' OR newstext LIKE '%in einem fairen Kampf in den Feldern%' OR newstext LIKE '%eine gerechte Strafe erhalten%')
					AND (DATEDIFF(NOW(),newsdate) <= ".$daydiff." AND DATEDIFF(NOW(),newsdate) > ".$min.")
					ORDER BY newsid DESC
					LIMIT 0,200";
		$res = db_query($sql);
		
		output("`&Die verdächtigen Taten von ".(($daydiff==0)?"heute":(($daydiff==1)?"gestern":"vor ".$daydiff." Tagen")).":`n");
		
		while($n = db_fetch_assoc($res)) {
			
			output('`n`n'.$n['newstext']);
			
		}
						
		addnav("Aktualisieren","wache.php?op=news");
		addnav("Heute","wache.php?op=news");
		addnav("Gestern","wache.php?op=news&daydiff=1");
		addnav("Vor 2 Tagen","wache.php?op=news&daydiff=2");
		addnav("Vor 3 Tagen","wache.php?op=news&daydiff=3");
		addnav("Zurück","wache.php");
		
		break;
		
	default:
		break;
}

page_footer();
?>
