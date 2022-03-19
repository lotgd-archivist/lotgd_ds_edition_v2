<?php
// Richter-Addon : Ergänzung zu Dorfamt u. Stadtwache
// Benötigt : [profession] (shortint, unsigned) in [accounts]
//             Tabellen [crimes],[cases]

// by Maris (Maraxxus@gmx.de)

require_once "common.php";
require_once(LIB_PATH.'board.lib.php');

page_header("Der Gerichtshof");

if (!isset($session)) exit();

$op = ($_GET['op']) ? $_GET['op'] : "court";

if ($_GET[op]=="newsdelete"){
	$sql = "DELETE FROM crimes WHERE newsid='$_GET[newsid]'";
	db_query($sql);
	$return = $_GET['return'];
	$return = preg_replace("'[?&]c=[[:digit:]-]*'","",$return);
	$return = substr($return,strrpos($return,"/")+1);
	redirect($return);
}

switch($op) {
		
	case 'bewerben':
	
		output("`&Du holst tief Luft und öffnest langsam die schwere Eichentüre. Ein betagter Mann mit dichtem Backenbart sitzt hinter einem Tisch aus dunklem Holz und ist gerade in seine Arbeit vertieft. Als die Geräusche deiner Schritte auf dem Holzboden zu ihm dringen blickt er auf. \"`#Wen haben wir denn hier?`&\" fragt er mit einem sadistischem Grinsen. Nachdem du dich vorgestellt und ihm dein Anliegen mitgetielt hast kneift er die Augen zusammen.`n`n");
		$maxamount = getsetting("numberofjudges",10);
		$reqdk = getsetting("judgereq",50);
		
		$sql = "SELECT profession FROM accounts WHERE profession=".PROF_JUDGE_HEAD." OR profession=".PROF_JUDGE;
		$result = db_query($sql) or die(db_error(LINK));
		if ((db_num_rows($result)) < $maxamount) {
		
			if (($session['user']['profession']==PROF_JUDGE_ENT) || ($session['user']['profession']==24)) {
				output("\"`# ".($session['user']['name'])."! So sehr ich Euren Wunsch nachempfinden kann wieder richten zu dürfen muss ich Euch jedoch enttäuschen. Ihr hattet Eure Chance! Und nun verlasst mein Büro!`&\"");
			}
			else {
				output("\"`# ".($session['user']['name'])."!`# Ich hoffe Ihr wisst worauf Ihr Euch hier einlasst? Das Amt des Richters ist hart und entbehrungsreich. Und an Euch werden besondere Forderungen gestellt : Ihr müsst sowohl ruhmreich wie auch von höchstem Ansehen sein und in Eurem Verhalten ein Vorbild!`&\"`n`n");
		
				if (($session['user']['dragonkills']) >= $reqdk) {
					if ($session['user']['reputation']>=50) {
						output ("\"`#Ich sehe, ich sehe... Ihr seid sowohl ruhmreich, wie auch von allerhöchstem Ansehen! Das ist gut, sehr gut. Meinetwegen könnt Ihr sofort anfangen. Doch wisset, dass Ihr als Richter nicht nur Rechte, sondern auch Pflichten habt. Es ist Euch strengstens untersagt mit zwielichtigen Gesellen Kontakte zu knüpfen, auch nicht zur Täuschung! Jedes Eurer Urteile muss gerecht und nachvollziehbar sein! Geschenke anzunehmen ist Euch strengstens untersagt!`n Dem obersten Richter habt Ihr Folge zu leisten! Sollte man Euch bei irgendeinem Verstoß oder irgendeiner Unehrenhaftigkeit erwischen, seid Ihr für lange Zeit Richter gewesen! Sind wir uns da einige?`nAlso, wollt Ihr noch immer ?`&\"");
						addnav("Ja, Richter werden","court.php?op=bewerben_ok");
						addnav("Nein, falsche Tür...","dorfamt.php");
		  			}
		   			else {
						output ("\"`#Ruhmreich seid mehr als es von Nöten wäre, doch fürchte ich, dass Euch die Leute nicht trauen würden, wenn Ihr plötzlich in Richterrobe daher kämet. Tut mal etwas für Euer Ansehen und versucht es dann noch einmal!`&\"");
		   			}
				}
				else {
					output ("\"`#Ihr seid zwar ruhmreich, doch wie es mir scheint nicht ruhmreich genug. Ihr solltet noch mehr Ruhm im Kampf gegen den Drachen erlangen und es dann noch einmal versuchen!`&\"");
				}
			}	// Kein entlassener 
		}	// Noch nicht zu viele 
		else {
			output ("\"`#Es tut mir sehr leid, aber das Dorf hat zur Zeit genügend Richter. Versucht es doch später noch einmal!`&\"");
		}
		
		addnav("Zurück","dorfamt.php");
			
		break;
		
	case 'bewerben_ok':
		
		output("`&Du überreichst dem alten Mann dein Bewerbungsschreiben. Dieser verstaut es unter einem hohen Stapel Pergamenten und meint: \"Wir werden auf dich zurückkommen!\"");
		$session['user']['profession']=PROF_JUDGE_NEW;
		$sql = "SELECT acctid FROM accounts WHERE profession=".PROF_JUDGE_HEAD." ORDER BY loggedin DESC, RAND() LIMIT 1";
		$res = db_query($sql);
		if(db_num_rows($res)) {
			$w = db_fetch_assoc($res);
			systemmail($w['acctid'],"`&Neue Bewerbung!`0","`&".$session['user']['name']."`& hat sich als Richter beworben. Du solltest die Bewerbung überprüfen und eine Entscheidung treffen.");
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
		
		$sql = "SELECT COUNT(*) AS anzahl FROM accounts WHERE (profession=".PROF_JUDGE_HEAD." OR profession=".PROF_JUDGE.")";
		$res = db_query($sql);
		$p = db_fetch_assoc($res);
		
		if($p['anzahl'] >= getsetting("numberofjudges",10)) {
			output("Es gibt bereits ".$p['anzahl']." Richter! Mehr sind zur Zeit nicht möglich.");
			addnav("Zurück","court.php?op=listj");
		}
		else {
		
			$sql = "UPDATE accounts SET profession = ".PROF_JUDGE."
					WHERE acctid=".$pid;
			db_query($sql) or die (db_error(LINK));
			
			$sql = "SELECT name FROM accounts WHERE acctid=".$pid;
			$res = db_query($sql);
			$p = db_fetch_assoc($res);
			
			systemmail($pid,"Du wurdest aufgenommen!",$session['user']['name']."`& hat deine Bewerbung zum Richter angenommen. Damit bist du vom heutigen Tage an offiziell Hüter für Recht und Ordnung!");
			
			$sql = "INSERT INTO news SET newstext = '".addslashes($p['name'])." `&wurde heute offiziell das ehrenvolle Amt eines Richters zugewiesen!',newsdate=NOW(),accountid=".$pid;
			db_query($sql) or die (db_error(LINK));
			
			addhistory('`2Aufnahme ins Richteramt',1,$pid);
			
			addnav("Willkommen!","court.php?op=listj");
			
			output("Der neue Richter ist jetzt aufgenommen!");
		}
		
		break;
		
	case 'abl':
		
		$pid = (int)$_GET['id'];
		
		$sql = "UPDATE accounts SET profession = 0  
				WHERE acctid=".$pid;
		db_query($sql) or die (db_error(LINK));
							
		systemmail($pid,"Deine Bewerbung wurde abgelehnt!",$session['user']['name']."`& hat deine Bewerbung als Richter abgelehnt.");
			
		addnav("Zurück","court.php?op=listj");
		
		break;
	
	case 'entlassen':
		
		$pid = (int)$_GET['id'];
	
		$sql = "UPDATE accounts SET profession = 0
				WHERE acctid=".$pid;
		db_query($sql) or die (db_error(LINK));
			
		$sql = "SELECT name FROM accounts WHERE acctid=".$pid;
		$res = db_query($sql);
		$p = db_fetch_assoc($res);
			
		systemmail($pid,"Du wurdest entlassen!",$session['user']['name']."`& hat dich als Richter entlassen!");
			
		$sql = "INSERT INTO news SET newstext = '".addslashes($p['name'])." `&wurde heute vom Amt eines Richters enthoben!',newsdate=NOW(),accountid=".$pid;
		db_query($sql) or die (db_error(LINK));
		
		addhistory('`$Entlassung aus dem Richteramt',1,$pid);
			
		addnav("Weiter","court.php?op=listj");
			
		output("Der Richter wurde entlassen!");
				
		break;
	
	case 'leave':
		
		output ("`&Mit schlotternden Knien betrittst du das Zimmer, in dem der ältere Herr mit dem Backenbart wie gewohnt hinter seinem Schreibtisch sitzt. Als du eintrittst und ihm die Hand reichst bittet er dich Platz zu nehmen und schau dich erwartungsvoll an.`nWillst du wirklich dein Richteramt aufgeben?");
		addnav("Ja, austreten!","court.php?op=leave_ok");
		addnav("NEIN. Dabei bleiben","dorfamt.php");
		
		break;
		
	case 'leave_ok':
		
		output ("`&Du bittest um deine Entlassung und der ältere Herr erledigt sichtlich schweren Herzens alle Formalitäten \"`#Wirklich schade, dass Ihr geht! Ich danke Euch vielmals für die treuen Dienste, die Ihr dem Dorf geleistet habt und werde Euch nie vergessen! Beachtet, dass Eure Entlassung erst mit Beginn des morgigen Tages wirksam wird. Für heute seid Ihr jedoch beurlaubt.`&\"");
		addnews("".$session[user][name]."`@ hat das Richteramt niedergelegt. Die Gaunerwelt atmet auf.");
		$session['user']['profession'] = PROF_JUDGE_ENT;
		
		addhistory('`2Aufgabe des Richteramts');
		
		addnav("Zurück ins Zivilleben","dorfamt.php");		
		
		break;
	
	case 'court':
		
		if ($session['user']['profession']==PROF_JUDGE || $session['user']['profession']==PROF_JUDGE_HEAD || su_check(SU_RIGHT_COMMENT)) addcommentary();
		output("`b`c`2Der Gerichtshof von ".getsetting('townname','Atrahor')."`b`c");
		output("`&Dieser Teil des Gebäudes ist dem Gerichtswesen zugeteilt. Mehrere Türen sind links und rechts des breiten Ganges zu erkennen und auf großen Holztäfelchen steht geschrieben was sich dahinter verbirgt.`nManche Türen sind für dich verschlossen, andere zugänglich.");
		addnav("Öffentliches");
		addnav("Verhandlungsraum","court.php?op=thecourt");
		addnav("Liste der Richter","court.php?op=listj");
        addnav("Gerichtsschreiber");
        addnav("Zum Gerichtsschreiber","court.php?op=schreiber");
if ($session['user']['profession']==PROF_JUDGE || $session['user']['profession']==PROF_JUDGE_HEAD
 || su_check(SU_RIGHT_DEBUG) ) {

        addnav("Arbeit");
        addnav("Verdächtige Taten","court.php?op=news");
        addnav("Aktuelle Fälle","court.php?op=cases");
        //addnav("Kopfgeldliste","court.php?op=listh");
		addnav("Schwarzes Brett","court.php?op=board");
        addnav("Diskussionsraum","court.php?op=judgesdisc");
		addnav("Archiv");
		addnav("Urteile","court.php?op=archiv");
		addnav("Handbuch für Jungrichter","court.php?op=faq"); }
        addnav("Sonstiges");
        addnav("Zurück","dorfamt.php");
		output("`n`n");
		viewcommentary("court","Sprechen:",30,"spricht");
			
		break;
		
	case 'board':
		
		output ("`&Du stellst dich vor das große Brett und schaust ob eine neue Mitteilung vorliegt.`n");
		//addcommentary();
		// if (($session['user']['profession']==2) || ($session['user']['superuser']>1)) {
		output ("`tDu kannst eine Notiz hinterlassen oder entfernen.`n`n");
		
		if($_GET['board_action'] == "add") {
			
			board_add('richter');
			
			redirect("court.php?op=board&ret=$_GET[ret]");
			
		}
		else {
								
			board_view_form('Hinzufügen','');
						
			board_view('richter',2,'','',true,true,true);
		}

        if ($_GET[ret]==1) { addnav("Zurück","court.php?op=judgesdisc"); } else {
        addnav("Zurück","court.php"); }
		
		break;
	
	case 'listj':
		$admin = ($session['user']['profession'] == 22 || su_check(SU_RIGHT_DEBUG)) ? true : false;
			
		output("<span style='color: #9900FF'>",true);
		$sql = "SELECT name,acctid,loggedin,dragonkills,login,level,profession,activated,laston FROM accounts WHERE profession=21 OR profession=22 OR profession=23 OR profession=25
				ORDER BY profession DESC, level DESC";
				$result = db_query($sql) or die(db_error(LINK));
		output ("`&Folgende Helden sind Richter:`n`n");
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
			if ($row['profession']==21) {
				
				output("`#Richter`&</td><td>",true);
				if($admin) {output('<a href="court.php?op=entlassen&id='.$row['acctid'].'">Entlassen</a>',true);addnav("","court.php?op=entlassen&id=".$row['acctid']);}
				}
			if ($row['profession']==PROF_JUDGE_HEAD) {output("`4Oberster Richter`&</td><td>",true);}
			if ($row['profession']==PROF_JUDGE_ENT) {output("`6Entlassung läuft`&</td><td>",true);}
			if ($row['profession']==PROF_JUDGE_NEW) {
			
				output("`@Bittet um Aufnahme`&</td><td>",true);
				if($admin) {
					output('<a href="court.php?op=aufn&id='.$row['acctid'].'">Aufnehmen</a>`n',true);
					addnav("","court.php?op=aufn&id=".$row['acctid']);
					output('<a href="court.php?op=abl&id='.$row['acctid'].'">Ablehnen</a>',true);
					addnav("","court.php?op=abl&id=".$row['acctid']);
					}
				
				}
			output("</td><td>",true);
			if (user_get_online(0,$row)) { output("`@online`&",true);} else { output("`4offline`&",true);}
			output("</td></tr>",true); 
		}
		db_free_result($result);
		output("</table>",true);
		output("</span>",true);
		output("<big>`n`@Gemeinsame Drachenkills der Richter : `^$dks`n`n`&<small>",true);

        if ($_GET[ret]==1) { addnav("Zurück","court.php?op=judgesdisc"); } else {
        addnav("Zurück","court.php"); }
		
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
			
			$lst+=1;
			output("<tr class='".($lst%2?"trlight":"trdark")."'><td>".($row['bounty'])."</td><td>".($row['level'])."</td><td><a href='bio.php?char=".rawurlencode($row['login'])."&ret=".URLEncode($_SERVER['REQUEST_URI'])."'>$row[name]</a>",true);
			addnav("","bio.php?char=".rawurlencode($row['login'])."&ret=".URLEncode($_SERVER['REQUEST_URI']));
			output("</td><td>",true);
			
			if ($row['location'] == USER_LOC_FIELDS) output(user_get_online(0,$row)?"`@online":"`3Die Felder",true);
			
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
        if ($_GET[ret]==1) { addnav("Zurück","court.php?op=judgesdisc"); } else {
        addnav("Zurück","court.php"); }
	
		db_free_result($result);
		output("</table>",true);
		output("</span>",true);		
		
		break;
	
	case 'news':
		
		$daydiff = ($_GET['daydiff']) ? $_GET['daydiff'] : 0;
		$min = $daydiff-1;
			
		$sql = "SELECT newstext,newsdate,newsid,accountid FROM crimes WHERE (DATEDIFF(NOW(),newsdate) <= ".$daydiff." AND DATEDIFF(NOW(),newsdate) > ".$min.")
					ORDER BY newsid DESC
					LIMIT 0,200";

/** If you are using mysql < ver 4.1.1 try using the following query :
SELECT newstext,newsdate FROM news WHERE
(newstext LIKE '%freigesprochen%' OR newstext LIKE '%verurteilt%')
AND (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(newsdate)) <= 86400
ORDER BY newsid DESC
LIMIT 0,200 **/

        $res = db_query($sql);
		
		output("`&Die verdächtigen Taten von ".(($daydiff==0)?"heute":(($daydiff==1)?"gestern":"vor ".$daydiff." Tagen")).":`n");
		

        for ($i=0;$i<db_num_rows($res);$i++){
		$row = db_fetch_assoc($res);
		output("`c`2-=-`@=-=`2-=-`@=-=`2-=-`@=-=`2-=-`0`c");
        output("$row[newstext]`n");

            output("[ <a href='court.php?op=inspect&accountid=$row[accountid]&daydiff=".$daydiff."'>Ermitteln</a> ]&nbsp;",true);
			addnav("","court.php?op=inspect&accountid=$row[accountid]&daydiff=".$daydiff);
			

			output("[ <a href='court.php?op=newsdelete&newsid=$row[newsid]&return=".URLEncode($_SERVER['REQUEST_URI'])."'>Löschen</a> ]&nbsp;",true);
			addnav("","court.php?op=newsdelete&newsid=$row[newsid]&return=".URLEncode($_SERVER['REQUEST_URI']));
			

	    }
	    if (db_num_rows($res)==0){
		output("`n`1`b`c Keine offenen Fälle an diesem Tag. `c`b`0");
	}
						
		addnav("Aktualisieren","court.php?op=news");
		addnav("Heute","court.php?op=news");
		addnav("Gestern","court.php?op=news&daydiff=1");
		addnav("Vor 2 Tagen","court.php?op=news&daydiff=2");
		addnav("Vor 3 Tagen","court.php?op=news&daydiff=3");

        if ($_GET[ret]==1) { addnav("Zurück","court.php?op=judgesdisc"); } else {
        addnav("Zurück","court.php"); }
		
		break;

    case 'inspect':

		$sql = "SELECT newstext,newsdate,newsid FROM crimes WHERE accountid=".$_GET['accountid']."
					ORDER BY newsid DESC
					LIMIT 0,200";
		$res = db_query($sql);

		output("`&Eine genauere Betrachtung bringt folgendes Ergebnis :`n");

        for ($i=0;$i<db_num_rows($res);$i++){
		$row = db_fetch_assoc($res);
		output("`c`2-=-`@=-=`2-=-`@=-=`2-=-`@=-=`2-=-`0`c");
        output("$row[newstext]`n");

	    }
        addnav("Anklage erheben","court.php?op=accuse&ret=$_GET[ret]&suspect=".$_GET['accountid']."&daydiff=".$daydiff);
		addnav("Zurück","court.php?op=news&ret=$_GET[ret]");

		break;
		
	case 'caseinfo':

		$sql = "SELECT newstext,newsid,accountid,judgeid,court FROM cases WHERE accountid=".$_GET['accountid']."
					ORDER BY newsid DESC
					LIMIT 0,200";
		$res = db_query($sql);

		output("`&Folgende Tatbestände werden verhandelt :`n");

        for ($i=0;$i<db_num_rows($res);$i++){
		$row = db_fetch_assoc($res);
		output("`c`2-=-`@=-=`2-=-`@=-=`2-=-`@=-=`2-=-`0`c");
        output("$row[newstext]`n");

	    }
	    
	    if ($row[court]==0) {
	    
	    output("`n`nVerfahren wurde eröffnet von:`n");

        $sql2 = "SELECT name FROM accounts WHERE acctid=$row[judgeid]";
	    	$res2 = db_query($sql2);
            $row2 = db_fetch_assoc($res2);
            output($row2[name]);
            output("`n`nEin anderer Richter muss das Urteil verkünden.");
            
            
	    if (($session['user']['acctid']!=$row[judgeid]) && ($session['user']['acctid']!=$row[accountid])) {
        addnav("Mit Prozess");
        addnav("Prozess führen","court.php?op=prozess&ret=$_GET[ret]&who=".$row[accountid]."");
        addnav("Aktenlage");
        addnav("Verurteilen","court.php?op=guilty&ret=$_GET[ret]&suspect=".$_GET['accountid']."");
        addnav("Freisprechen","court.php?op=notguilty&ret=$_GET[ret]&suspect=".$_GET['accountid']."");
        addnav("Sonstiges"); } }
        else output("`n`n`&Es läuft ein Prozess zu diesem Fall!");
        if ($_GET[proc]==1) { addnav("Zurück","court.php?op=thecourt2&accountid=$_GET[accountid]"); } else {
        addnav("Zurück","court.php?op=cases&ret=$_GET[ret]"); }
        
		break;
		
	case 'accuse':

		$sql = "SELECT newstext,newsdate,newsid FROM crimes WHERE accountid=".$_GET['suspect']."
					ORDER BY newsid DESC
					LIMIT 0,200";
		$res = db_query($sql);

		output("`&Die Verbrechen wurde soeben zur Anklage gebracht.`n");

        
        
        for ($i=0;$i<db_num_rows($res);$i++){
		$row = db_fetch_assoc($res);
		
		
        addtocases("$row[newstext]",$_GET['accountid']);
        $sql = "DELETE FROM crimes WHERE newsid='$row[newsid]'";
    	db_query($sql);

	    }
        
		redirect('court.php?op=news&daydiff='.$_GET['daydiff']);
		
		addnav("Zurück","court.php?op=news&daydiff=$_GET[daydiff]");

		break;
		
	case 'cases':

        $cache=niemand;
		$sql = "SELECT newstext,newsid,accountid,judgeid,court FROM cases
					ORDER BY accountid DESC
					LIMIT 0,200";
		$res = db_query($sql);

		output("`&Derzeit wird folgenden Verbechern der Prozess gemacht :`n`n");

    
        for ($i=0;$i<db_num_rows($res);$i++){
		$row = db_fetch_assoc($res);

          $sql2 = "SELECT name FROM accounts WHERE acctid=$row[accountid]
					ORDER BY name DESC";
	    	$res2 = db_query($sql2);

            $row2 = db_fetch_assoc($res2);
            
          if ($cache!=$row2[name])


          output("<a href='court.php?op=caseinfo&ret=$_GET[ret]&accountid=$row[accountid]'>".$row2[name]."`n</a>",true);
          addnav("","court.php?op=caseinfo&ret=$_GET[ret]&accountid=$row[accountid]");

          $cache=$row2[name];


	    }
	    
	    if (db_num_rows($res)==0){
		output("`n`1`b`c Zurzeit werden keine Fälle verhandelt. `c`b`0");
	}   if ($_GET[ret]==1) { addnav("Zurück","court.php?op=judgesdisc"); } else {
		addnav("Zurück","court.php"); }

		break;
		
	case 'guilty':
        Output ("Wie lautet dein Strafmaß?`n");

        $suspect=$_GET[suspect];
        $ret=$_GET[ret];
        $proc=$_GET[proc];

               output('<form method="POST" action="court.php?op=guilty2&ret='.$ret.'&suspect='.$suspect.'&proc='.$proc.'">',true);
                output('`n<input type="text" name="count"><input type="hidden" name="count2"> <input type="submit" value="Tage Haft"></form>',true);
                addnav('','court.php?op=guilty2&ret='.$ret.'&suspect='.$suspect.'&proc='.$proc.'');


        if ($_GET[proc]!=1) {
          addnav("Zurück","court.php?op=caseinfo&ret=$_GET[ret]&accountid=$_GET[suspect]"); }
        else
        { 
          addnav("Zurück","court.php?op=thecourt2&ret=$_GET[ret]&accountid=$_GET[suspect]");
        }
        break;
        
	case 'guilty2':
        
        $count = $_POST['count'];
     //   $count = abs((int)$HTTP_GET_VARS[count] + (int)$HTTP_POST_VARS[count]);
        $maxsentence=getsetting("maxsentence",5);
        if ($count>$maxsentence) { output("Na, wir wollen es mal nicht übertreiben. Findest du nicht, dass ".$maxsentence." Tage ausreichend wären ?");} else
        {
        $sql2 = "SELECT name,acctid FROM accounts WHERE acctid=$_GET[suspect]";
    	$res2 = db_query($sql2);
        $row2 = db_fetch_assoc($res2);
        $sql3 = "SELECT sentence FROM account_extra_info WHERE acctid=$_GET[suspect]";
    	$res3 = db_query($sql3);
        $row3 = db_fetch_assoc($res3);
        
        $count2=$count+$row3[sentence];
        if ($count2>$maxsentence) { $count2=$maxsentence; }

        output ("`&Alles klar! ".$count." Tage Haft. Die Stadtwachen wurden informiert. ".$row2[name]." `&soll nun für ".$count2." `&Tage hinter Gitter!");
        addnews("`#Richter ".$session['user']['name']." hat `@".$row2['name']."`& zu ".$count." `&Tagen Kerker verurteilt!");

$mailtext="`@{$session['user']['name']}`& hat dich für deine Vergehen zu ".$count." Tagen Kerker verurteilt!`nDiese Strafe wird zu eventuell anderen Strafen hinzugerechnet, jedoch kann deine Haft dadurch nicht länger als ".$maxsentence." Tage werden.`nDeine Vergehen im Einzelnen :`n`n";

$sql3 = "SELECT newstext FROM cases WHERE accountid=".$row2['acctid']."
					ORDER BY newsid DESC
					LIMIT 0,200";
		$res3 = db_query($sql3);

        for ($j=0;$j<db_num_rows($res3);$j++){
		$row3 = db_fetch_assoc($res3);
$mailtext=$mailtext.$row3[newstext]."`n";
}

        systemmail($row2['acctid'],"`\$Du wurdest verurteilt!`0",$mailtext);

        $sql = "DELETE FROM cases WHERE accountid='$_GET[suspect]'";
	    db_query($sql);
	    $sql = "UPDATE account_extra_info SET sentence=$count2 WHERE acctid='$_GET[suspect]'";
				db_query($sql);
				
        if ($_GET[proc]==1) {
          $roomname="court".$_GET[suspect];
          
          $sql="INSERT INTO commentary (postdate,section,author,comment) VALUES (now(),'$roomname',".$session[user][acctid].",'/me `^verurteilt ".$row2['name']."`^ zu $count Tagen Kerker und beendet den Prozess.`V')";

        db_query($sql) or die(db_error(LINK));
        
        }
        }
        
        if ($_GET[proc]==1) {
          		  
		  item_delete(' tpl_id="vorl" AND value1='.$_GET['suspect']);		  
          
        }
        if ($_GET[ret]==1) { addnav("Zurück","court.php?op=judgesdisc"); } else {
        addnav("Zurück","court.php?op=cases"); }
        break;
		
	case 'notguilty':
        output("Du entscheidest zugunsten des Angeklagten.");

         $sql2 = "SELECT name FROM accounts WHERE acctid=$_GET[suspect]";
         $res2 = db_query($sql2);
         $row2 = db_fetch_assoc($res2);

        addnews("`#Richter ".$session['user']['name']." hat `@".$row2['name']."`& freigesprochen!");

       	$sql = "DELETE FROM cases WHERE accountid='$_GET[suspect]'";
	    db_query($sql);

        if ($_GET[proc]==1) {
          $roomname="court".$_GET[suspect];

          $sql="INSERT INTO commentary (postdate,section,author,comment) VALUES (now(),'$roomname',".$session[user][acctid].",'/me `@spricht ".$row2['name']."`@ in allen Anklagepunkten frei und beendet den Prozess.`V')";

        db_query($sql) or die(db_error(LINK));

      }

	    if ($_GET[proc]==1) {
          item_delete(' tpl_id="vorl" AND value1='.$_GET['suspect']);		  
        }
        
        if ($_GET[ret]==1) { addnav("Zurück","court.php?op=judgesdisc"); } else {
        addnav("Zurück","court.php?op=cases"); }
        break;
        
	case 'archiv':

		$daydiff = ($_GET['daydiff']) ? $_GET['daydiff'] : 0;
		$min = $daydiff-1;

		$sql = "SELECT newstext,newsdate FROM news WHERE
					(newstext LIKE '%freigesprochen%' OR newstext LIKE '%verurteilt%')
					AND (DATEDIFF(NOW(),newsdate) <= ".$daydiff." AND DATEDIFF(NOW(),newsdate) > ".$min.")
					ORDER BY newsid DESC
					LIMIT 0,200";
		$res = db_query($sql);

		output("`&Urteile von ".(($daydiff==0)?"heute":(($daydiff==1)?"gestern":"vor ".$daydiff." Tagen")).":`n");

		while($n = db_fetch_assoc($res)) {

			output('`n`n'.$n['newstext']);

		}
		
		if (db_num_rows($res)==0){
		output("`n`1`b`c Keine Urteile an diesem Tag. `c`b`0");
	}

		addnav("Aktualisieren","court.php?op=archiv");
		addnav("Heute","court.php?op=archiv");
		addnav("Gestern","court.php?op=archiv&daydiff=1");
		addnav("Vor 2 Tagen","court.php?ret=$_GET[ret]&op=archiv&daydiff=2");
		addnav("Vor 3 Tagen","court.php?ret=$_GET[ret]&op=archiv&daydiff=3");
		addnav("Vor 4 Tagen","court.php?ret=$_GET[ret]&op=archiv&daydiff=4");
		addnav("Vor 5 Tagen","court.php?ret=$_GET[ret]&op=archiv&daydiff=5");
        if ($_GET[ret]==1) { addnav("Zurück","court.php?op=judgesdisc"); } else {
        addnav("Zurück","court.php"); }
        break;
        
        case 'faq':
        $maxsentence=getsetting("maxsentence",5);
        output("Handbuch für Jungrichter - wie richte ich richtig`n`n");
        output("1. Verdächtige Taten : Hier werden alle Missetaten aller Spieler aufgelistet.`n");
        output("2. Ermitteln : Zeigt alle Taten eines bestimmten Spielers. Klicke auf Anklage erheben.`n");
        output("3. Aktuelle Fälle : Spieler, gegen die ermittelt wird, werden hier aufgeführt.`n");
        output("4. Ein Klick auf den Namen zeigt die Taten des Verdächtigen.`n");
        output("5. Verurteilen : Man lege die Haftstrafe (bis ".$maxsentence." Tage) fest und schon werden die Wachen aktiv.`n");
        output("`n`nHinweise : Der Richter, der das Verfahren eröffnet hat darf nicht das Urteil fällen!`n");
        output("Richtet stets fair und unbestechlich, sonst droht Rauswurf (oder Schlimmeres).`n");
        output("Sollten während einer Verhandlung weitere Straftaten geschehen können sie wie in Punkt 1-2 hinzugefügt werden.`n");
        output("`n`n`n/Anstatt eines Urteils kann auch ein `@Prozess`& begonnen werden.`n");
        output("`&Aber Vorsicht : Ein Prozess bedeutet `^RPG`& und kostet Zeit, eure Zeit udn die der Angeklagten und Zeugen. Ordnet deswegen nicht wegen alltäglichen Dingen jedesmal einen neuen Prozess an.`nBesser ist es auf Anzeigen von Spielern mit einem Prozess zu reagieren.`n");
        output("`&Die `^Höchststrafe`& für einen Spieler beträgt `4".$maxsentence." Tage`&.");
        if ($_GET[ret]==1) { addnav("Zurück","court.php?op=judgesdisc"); } else {
        addnav("Zurück","court.php"); }
        break;
        
        case 'schreiber':
        output("`&In einem viel zu kleinen Raum sitzt ein karges Männlein hinter einem kleinen Tisch, der meterhoch mit Unterlagen zugestellt ist. Irgendwo dazwischen steht eine kleine eiserne Kassette auf dem Tisch, die ein paar Goldmünzen enthält. Der Schreiber schaut dich an als du eintrittst.'");
        addnav("Anzeige erstatten","court.php?op=anzeige&ret=$_GET[ret]");
        if ($_GET[ret]==1) { addnav("Zurück","court.php?op=judgesdisc"); } else {
        addnav("Zurück","court.php"); }
        break;
        
        case 'anzeige':
        output("`&Der Schreiberling schaut dich an. \"`#Na, wer hat Eucht denn Schlimmes angetan?`&\" fragt er.`n`n");

if ($HTTP_GET_VARS[who]==""){
addnav("Äh.. niemand!","court.php?op=schreiber&ret=$_GET[ret]");
if ($_GET['subop']!="search"){
                output("<form action='court.php?op=anzeige&ret=$_GET[ret]&subop=search' method='POST'><input name='name'><input type='submit' class='button' value='Suchen'></form>",true);
                addnav("","court.php?op=anzeige&ret=$_GET[ret]&subop=search");
            }else{
                addnav("Neue Suche","court.php?op=anzeige&ret=$_GET[ret]");
                $search = "%";
                for ($i=0;$i<strlen($_POST['name']);$i++){
                    $search.=substr($_POST['name'],$i,1)."%";
                }
                $sql = "SELECT name,alive,location,sex,level,reputation,laston,loggedin,login FROM accounts WHERE (locked=0 AND name LIKE '$search') ORDER BY level DESC";
                $result = db_query($sql) or die(db_error(LINK));
                $max = db_num_rows($result);
                if ($max > 50) {
                    output("`n`n\"`#Geht es vielleicht ein bisschen genauer ?`&`n");
                    $max = 50;
                }
                output("<table border=0 cellpadding=0><tr><td>Name</td><td>Level</td></tr>",true);
                for ($i=0;$i<$max;$i++){
                    $row = db_fetch_assoc($result);
                    output("<tr><td><a href='court.php?op=anzeige&ret=$_GET[ret]&who=".rawurlencode($row[login])."'>$row[name]</a></td><td>$row[level]</td></tr>",true);
                    addnav("","court.php?op=anzeige&ret=$_GET[ret]&who=".rawurlencode($row[login]));
                }
                output("</table>",true);
            }
        }else{

                $sql = "SELECT acctid,login,name FROM accounts WHERE login=\"$HTTP_GET_VARS[who]\"";
                $result = db_query($sql) or die(db_error(LINK));
                if (db_num_rows($result)>0){
                    $row = db_fetch_assoc($result);
$costs=$session['user']['level']*100;

output ("`&Der Schreiber nickt. \"`&Ja, der Name ".($row['name'])." `& ist mir ein Begriff... Die Gebühren für eine Anzeige liegen für Euch bei `^".$costs." Gold.`#\"`&`n`n");
output ("`n`&Wie lautet deine Anzeige?");
output("<form action='court.php?op=anzeige2&ret=$_GET[ret]&who=".rawurlencode($row[login])."' method='POST'><input name='text' id='text'><input type='submit' class='button' value='diktieren'></form>",true);
output("<script language='JavaScript'>document.getElementById('text').focus();</script>",true);
addnav("","court.php?op=anzeige2&ret=$_GET[ret]&who=".rawurlencode($row[login])."");
addnav("Abbrechen","court.php?ret=$_GET[ret]&op=schreiber");
                }else{
                    output("\"`#Ich kenne niemanden mit diesem Namen.`&\"");
                }
            }
            
        break;
		
		case 'anzeige2':

$text = $HTTP_POST_VARS[text];

$sql = "SELECT acctid,login,name FROM accounts WHERE login=\"$HTTP_GET_VARS[who]\"";
                $result = db_query($sql) or die(db_error(LINK));
                $row = db_fetch_assoc($result);

output("`&Die Anzeige lautet :`n`n");
$pretext="`&Anzeige von ".$session['user']['name']." `&gegen ".$row['name']." `&: ";
$text2=$pretext.$text;
output($text2);
output("`n`n`&Zufrieden?");
addnav("Sehr gut!","court.php?op=anzeige3&ret=$_GET[ret]&who=$row[acctid]&text=".rawurlencode($text)."");
addnav("Nein, nochmal!","court.php?op=anzeige&ret=$_GET[ret]&who=".rawurlencode($row[login])."");

     break;
     
     case 'anzeige3':

$sql = "SELECT acctid,login,name FROM accounts WHERE acctid=\"$HTTP_GET_VARS[who]\"";
                $result = db_query($sql) or die(db_error(LINK));
                $row = db_fetch_assoc($result);
                
$text = $_GET[text];
$pretext="`&Anzeige von ".$session['user']['name']." `&gegen ".$row['name']." `&: ";
$text=$pretext.$text;
output("`&Du hast zu Protokoll gegeben :`n");
output($text);

$buy=$session['user']['level']*100;
if ($buy>$session['user']['gold']) {
output("`&`n`nWas glaubst du wo du hier bist? Die Mühlen der Justiz mahlen sicherlich nicht umsonst. Also besorg dir ein wenig Kleingeld bevor du wiederkommst.`nDer Gerichtsdiener befördert dich mit einem Tritt nach draussen.");
addnav("Autsch!","village.php");
} else {
output("`&`n`nDeine $buy Goldmünzen versinken leise klirrend in der eisernen Kassette auf des Schreiberlings Tisch.`n");
$session['user']['gold']-=$buy;

$sql = "INSERT INTO crimes(newstext,newsdate,accountid) VALUES ('".addslashes($text)."',NOW(),".$row['acctid'].")";
db_query($sql) or die(db_error($link));

if ($_GET[ret]==1) { addnav("Hehe...","court.php?op=judgesdisc"); } else {
        addnav("Hehe...","court.php"); }
}


    break;
    
    case 'thecourt':
    $cache=niemand;
    
if ($session['user']['profession']==PROF_JUDGE || $session['user']['profession']==PROF_JUDGE_HEAD || su_check(SU_RIGHT_DEBUG) ) {

	$res= item_list_get(' i.tpl_id="vorl" ',' ORDER BY value1 DESC LIMIT 0,200 ');
}	
	else {
    	$res= item_list_get(' i.tpl_id="vorl" AND owner='.$session['user']['acctid'],' ORDER BY value1 DESC LIMIT 0,200 ');

	 }
	if (db_num_rows($res)){

		output("`&Zu welchem Prozess möchtest du gehen ?`n`n");


        for ($i=0;$i<db_num_rows($res);$i++){
		$row = db_fetch_assoc($res);

          $sql2 = "SELECT name FROM accounts WHERE acctid=$row[value1]
					ORDER BY name DESC";
	    	$res2 = db_query($sql2);

            $row2 = db_fetch_assoc($res2);

          if ($cache!=$row2[name])


          output("<a href='court.php?op=entrymsg&ret=$_GET[ret]&accountid=$row[value1]'>".$row2[name]."`n</a>",true);
          addnav("","court.php?op=entrymsg&ret=$_GET[ret]&accountid=$row[value1]");

          $cache=$row2[name];
	    }
	    
 addnav("Zurück","court.php");
	
	} else {
    
    if ($session['user']['profession']==PROF_JUDGE || $session['user']['profession']==PROF_JUDGE_HEAD || su_check(SU_RIGHT_DEBUG) ) { output("`&Derzeit werden hier keine Fälle verhandelt und du bist gewiss nicht gekommen um den Boden zu schrubben...`n`n");
    if ($_GET[ret]==1) { addnav("Zurück","court.php?op=judgesdisc"); } else {
        addnav("Zurück","court.php"); }  }
      else { output("`&Du hast keine Vorladung und die Verhandlungen sind nicht öffentlich.`nWas willst du also hier ?`n`n");
    addnav("Zurück","court.php"); }
    }
    break;
    
    case 'thecourt2':
	output("`&Du öffnest die schwere Eichentüre und betrittst den Gerichtssaal. Stühle und Bänke sind im hinteren Teil des großen Raumen ordentlich aufgestellt worden, eine Absperrung trennt diesen Teil von der Richterkanzel. Türen im hinteren Teil des Raumes führen zum Archiv und zum Besprechungsraum. Du stellst fest, dass dieser Raum sehr gepflegt und der Boden gut poliert ist.`n`n");

	$roomname="court".$_GET[accountid];

	$accountid=substr($roomname,5);

	addcommentary();
    viewcommentary($roomname,"Sagen:",30,"sagt");

if ($session['user']['profession']==PROF_JUDGE || $session['user']['profession']==PROF_JUDGE_HEAD || su_check(SU_RIGHT_DEBUG) ) {
addnav("Zeugen vorladen");
addnav("Vorladen","court.php?op=witn&ret=$_GET[ret]&accountid=$_GET[accountid]");
addnav("Anklageschrift");
addnav("Lesen","court.php?op=caseinfo&ret=$_GET[ret]&accountid=$_GET[accountid]&proc=1");
if ($session['user']['acctid']!=$_GET[accountid]) {
addnav("Prozess beenden");
addnav("Schuldig","court.php?op=guilty&ret=$_GET[ret]&proc=1&suspect=$accountid");
addnav("Nicht schuldig","court.php?op=notguilty&ret=$_GET[ret]&proc=1&suspect=$accountid");}
addnav("Prozesspause");
addnav("Saal verlassen","court.php?op=leavemsg&ret=$_GET[ret]&accountid=$_GET[accountid]");
} else addnav("Raus hier","court.php?op=leavemsg&ret=$_GET[ret]&accountid=$_GET[accountid]");
	break;
    
    case 'judgesdisc':
    output("`&Hier im kleinen Hinterzimmer des großes Verhandlungsraumes kannst du dich mit den anderen Richtern treffen. Ungestört von Plebs und Pöbel könnt ihr hier wichtige Fälle diskutieren oder einfach nur mal kurz ausspannen.`nEin großer runder Tisch in der Mitte des Raumes bietet allen Richtern Platz und sieht sehr gemütlich aus.`n`n");
    if ($session['user']['profession']==PROF_JUDGE || $session['user']['profession']==PROF_JUDGE_HEAD || su_check(SU_RIGHT_DEBUG) ) addcommentary();
    viewcommentary("judges","Deine Meinung sagen:",30,"meint");

        addnav("Öffentliches");
		addnav("Verhandlungsraum","court.php?op=thecourt&ret=1");
		addnav("Liste der Richter","court.php?op=listj&ret=1");
        addnav("Gerichtsschreiber");
        addnav("Zum Gerichtsschreiber","court.php?op=schreiber&ret=1");
        addnav("Arbeit");
        addnav("Verdächtige Taten","court.php?op=news&ret=1");
        addnav("Aktuelle Fälle","court.php?op=cases&ret=1");
        addnav("Kopfgeldliste","court.php?op=listh&ret=1");
		addnav("Schwarzes Brett","court.php?op=board&ret=1");
		addnav("Archiv");
		addnav("Urteile","court.php?op=archiv&ret=1");
		addnav("Handbuch für Jungrichter","court.php?op=faq&ret=1");
        addnav("Sonstiges");
        addnav("Zurück","court.php");
    break;
    
    case 'prozess':
           
     $sql = "SELECT name FROM accounts WHERE acctid=$_GET[who]";
     $res = db_query($sql);
     $row = db_fetch_assoc($res);
    	
	$item['tpl_value1'] = $_GET[who];
	$item['tpl_description'] = '`&Du wirst zum Gericht befohlen! Es betrifft das Verfahren gegen `4DICH!`&. Solltest du dem nicht nachkommen, droht dir eine harte Strafe.';
	
	item_add($_GET[who], 'vorl', true, $item );

     systemmail($_GET[who],"`4Vorladung!`2",$effekt);
     	
     output($row[name]."`& hat eine Vorladung erhalten und wird sich (hoffentlich) bald im Gerichtssaal einfinden.`n");

     $sql = "UPDATE cases SET court=1 WHERE accountid=$_GET[who]";
     db_query($sql) or die(sql_error($sql));

     if ($_GET[ret]==1) { addnav("Zurück","court.php?op=judgesdisc"); } else {
        addnav("Zurück","court.php"); }
    
    break;
    
    case 'witn':
    output("`&Wen möchtest du zu diesem Prozess vorladen?`n`n");

if ($HTTP_GET_VARS[who]==""){
addnav("Niemanden!","court.php?op=thecourt2&accountid=$_GET[accountid]");
if ($_GET['subop']!="search"){
                output("<form action='court.php?op=witn&ret=$_GET[ret]&accountid=$_GET[accountid]&subop=search' method='POST'><input name='name'><input type='submit' class='button' value='Suchen'></form>",true);
                addnav("","court.php?op=witn&ret=$_GET[ret]&accountid=$_GET[accountid]&subop=search");
            }else{
                addnav("Neue Suche","court.php?op=witn&ret=$_GET[ret]&accountid=$_GET[accountid]");
                $search = "%";
                for ($i=0;$i<strlen($_POST['name']);$i++){
                    $search.=substr($_POST['name'],$i,1)."%";
                }
                $sql = "SELECT name,alive,location,sex,level,reputation,laston,loggedin,login FROM accounts WHERE (locked=0 AND name LIKE '$search') ORDER BY level DESC";
                $result = db_query($sql) or die(db_error(LINK));
                $max = db_num_rows($result);
                if ($max > 50) {
                    output("`n`n\"`&Zu viele Suchergebnisse`&`n");
                    $max = 50;
                }
                output("<table border=0 cellpadding=0><tr><td>Name</td><td>Level</td></tr>",true);
                for ($i=0;$i<$max;$i++){
                    $row = db_fetch_assoc($result);
                    output("<tr><td><a href='court.php?op=witn&ret=$_GET[ret]&accountid=$_GET[accountid]&who=".rawurlencode($row[login])."'>$row[name]</a></td><td>$row[level]</td></tr>",true);
                    addnav("","court.php?op=witn&ret=$_GET[ret]&accountid=$_GET[accountid]&who=".rawurlencode($row[login]));
                }
                output("</table>",true);
            }
        }else{

                $sql = "SELECT acctid,login,name FROM accounts WHERE login=\"$HTTP_GET_VARS[who]\"";
                $result = db_query($sql) or die(db_error(LINK));
                if (db_num_rows($result)>0){
                    $row = db_fetch_assoc($result);


output ($row['name']." `& als Zeugen vorladen ?`n`n");

addnav("Ja","court.php?op=witn2&ret=$_GET[ret]&accountid=$_GET[accountid]&who=$row[acctid]");
addnav("Nein","court.php?op=thecourt2&ret=$_GET[ret]&accountid=$_GET[accountid]");
                }else{
                    output("\"`#Name wurde nicht gefunden.`&\"");
                }
            }

    break;

    case 'witn2':
    
     $sql = "SELECT name FROM accounts WHERE acctid=$_GET[accountid]";
     $res = db_query($sql);
     $row = db_fetch_assoc($res);
     
     $sql2 = "SELECT name FROM accounts WHERE acctid=$_GET[who]";
     $res2 = db_query($sql2);
     $row2 = db_fetch_assoc($res2);

	$item['tpl_value1'] = $_GET['accountid'];
	$item['tpl_description'] = '`&Du wirst zum Gericht befohlen! Es betrifft das Verfahren gegen '.$row[name].'`&. Solltest du dem nicht nachkommen, droht dir eine harte Strafe.';
	
	item_add($_GET[who], 'vorl', true, $item );

     systemmail($_GET[who],"`4Vorladung!`2",$effekt);

     output($row2[name]."`& hat eine Vorladung erhalten und wird sich (hoffentlich) bald im Gerichtssaal einfinden.`n");
     
     $roomname="court".$_GET[accountid];
    $sql="INSERT INTO commentary (postdate,section,author,comment) VALUES (now(),'$roomname',".$session[user][acctid].",'/me `&hat ".$row2['name']." `&als Zeugen vorgeladen.`V')";
    db_query($sql) or die(db_error(LINK));
    
     addnav("Zurück","court.php?op=thecourt2&ret=$_GET[ret]&accountid=$_GET[accountid]");
    break;
    
    case 'entrymsg':
    $roomname="court".$_GET[accountid];
    $sql="INSERT INTO commentary (postdate,section,author,comment) VALUES (now(),'$roomname',".$session[user][acctid].",'/me `&betritt den Gerichtssaal.`V')";
    db_query($sql) or die(db_error(LINK));
    redirect("court.php?op=thecourt2&ret=$_GET[ret]&accountid=$_GET[accountid]");
    break;
    
    case 'leavemsg':
    $roomname="court".$_GET[accountid];
    $sql="INSERT INTO commentary (postdate,section,author,comment) VALUES (now(),'$roomname',".$session[user][acctid].",'/me `&verlässt den Gerichtssaal.`V')";
    db_query($sql) or die(db_error(LINK));

    if ($_GET[ret]==1) { redirect("court.php?op=judgesdisc"); } else {
        redirect("court.php"); }
    break;
    
    default:
    break;
    
}

page_footer();
?>
