<?php
require_once "common.php";

if ($_GET['op']=="primer"){
popup_header("Fibel für neue Spieler");
	output("
<a href='petition.php?op=faq'>Inhaltsverzeichnis</a>`n`n
`^`bDie Themen:`b`n`^
<a href='petition.php?op=primer#dp'>Dorfplatz</a>`n
<a href='petition.php?op=primer#ersttag'>Dein erster Tag</a>`n
<a href='petition.php?op=primer#tod'>Tod</a>`n
<a href='petition.php?op=primer#neutag'>Neue Tage</a>`n
<a href='petition.php?op=primer#pvp'>PvP</a>`n
<a href='petition.php?op=primer#ooc'>OOC</a>`n
<a href='petition.php?op=primer#sprachen'>Sprachen</a>`n
`n
`^Willkommen bei Legend of the Green Dragon - Fibel für neue Spieler`n`n
<a name=\"dp\"></a>
`^`bDer Dorfplatz`b`n`@
Legend of the Green Dragon (LotGD) wird langsam zu einem ordentlich ausgedehnten Spiel mit einer Menge zu erforschen. Es ist leicht sich bei all dem, was man da draussen tun kann, zu verirren,
deshalb sehe den Dorfplatz am besten als das Zentrum des Spiels an. Dieser Bereich ermöglicht dir den Zugang zu den meisten anderen Gebieten des Spiels - mit einigen Ausnahmen 
(wir behandeln diese in wenigen Augenblicken). Wenn du dich jemals irgendwo verlaufen hast, versuch zum Dorfplatz zurück zu kommen und versuche dort, der Lage wieder Herr zu werden.`n
`n
<a name=\"ersttag\"></a>
`^`bDein erster Tag`b`n`@
Dein erster Tag in dieser Welt kann sehr verwirrend sein! Du wirst von einer Menge Informationen erschlagen und brauchst dabei fast keine davon. Das ist wahr! Etwas, das du 
vielleicht im Auge behalten solltest, sind deine Lebenspunkte (Hitpoints). Diese Information findest du in der \"Vital Info\". Egal, welche Spezialität du gewählt hast, am Ende bist du eine Art Krieger 
oder Kämpfer, und musst lernen zu kämpfen. Der beste Weg ist dazu, im Wald Monster zum töten zu suchen. Wenn du einen Gegner gefunden hast, überprüfe ihn gut und stelle 
sicher, dass er kein höheres Level als du selbst hat. Denn in diesem Fall überlebst du den Kampf vielleicht nicht. Denke immer daran, dass du jederzeit versuchen kannst, zu fliehen
aber das klappt manchmal nicht auf Anhieb! Um eine bessere Chance gegen die Monster im Wald zu haben , kannst du im Dorf 
Rüstungen und Waffen kaufen.`n
`n
Wenn du eine Kreatur besiegt hast, wirst du feststellen, dass du möglicherweise verletzt bist. Gehe in die Hütte des Heilers, dort kannst du innerhalb kürzester Zeit wieder zusammengeflickt werden. 
Solang du Level 1 bist, kostet die Heilung nichts, aber wenn du aufsteigst, wird die Heilung teurer und teurer. Bedenke auch, dass es teurer ist, einzelne Lebenspunkte zu heilen, als später 
mehrere gleichzeitig. Wenn du also etwas Gold sparen willst und nicht allzu schwer verletzt bist, kannst durchaus mal mehr als 1 Kampf riskieren
bevor du zum Heiler rennst.`n
`n
Nachdem du ein paar Monster gekillt hast, solltest du mal im Dorf in Bluspring's Trainingslager vorbeischauen und mit deinem Meister reden. Er wird dir sagen, 
ob du bereit bist, ihn herauszufordern. Und wenn du bereit bist, sorge dafür, dass du ihn auch besiegst (als vorher heilen)! Dein Meister wird dich nicht töten wenn du verlierst,
stattdessen gibt er dir eine komplette Heilung und schickt dich wieder auf den Weg.",true);
	if (getsetting("multimaster",1) == 0) {
		output(" Du kannst deinen Meister nur einmal am Tag herausfordern.");
	}
output("
`n
`n
<a name=\"tod\"></a>
`^`bTod`b`n`@
Der Tod ist ein natürlicher Teil in jedem Spiel, das irgendwelche Kämpfe enthält. In Legend of the Green Dragon ist der Tod nur ein vorübergehender Zustand. Wenn du stirbst, verlierst 
du normalerweise alles Gold, das du dabei hast (Gold auf der Bank ist sicher!) und etwas von deiner gesammelten Erfahrung. Wenn du tot bist, kannst du das Land der Schatten und den Friedhof erforschen. 
Auf dem Friedhof wirst du Ramius, den Gott der Toten finden. Er hat einige Dinge, die du für ihn tun kannst, und als Gegenleistung 
wird er dir spezielle Kräfte oder Gefallen gewähren. Der Friedhof ist einer der Plätze, die du vom Dorfplatz aus nicht erreichen kannst. Umgekehrt kommst du nicht ins Dorf 
solange du tot bist!`n
`n
Solang es dir nicht gelingt, Ramius davon zu überzeugen, dich wieder zu erwecken, bleibst du tot - zumindest bis zum nächsten Spieltag. Es gibt ".getsetting("daysperday",2)." Spieltage pro echtem Tag. Diese Tage fangen an, 
sobald die Uhr im Dorf Mitternacht zeigt.`n
`n
<a name=\"neutag\"></a>
`^`bNeue Tage`b`n`@
Wie oben erwähnt, gibt es ".getsetting("daysperday",2)." Spieltage pro echtem Tag. Diese Tage fangen an, sobald die Uhr im Dorf Mitternacht zeigt.  Wenn dein neuer Tag anfängt 
werden dir neue Waldkämpfe (Runden), Zinsen bei der Bank (wenn der Bankier mit deiner Leistung zufrieden ist) gewährt, und viele deiner anderen 
Werte werden aufgefrischt. Ausserdem wirst du wiederbelebt, falls du tot warst. Wenn du ein paar Spieltage nicht einloggst, bekommst du die verpassten Spieltage 
nicht beim nächsten Login zurück. Du bist während deiner Abwesenheit sozusagen nicht am Geschehen dieser Welt beteiligt 
Waldkämpfe, PvP-Kämpfe, Spezielle Fähigkeiten und andere Dinge, die sich täglich zurücksetzen, summieren sich 
NICHT über mehrere Tage auf.`n
`n",true);
if (getsetting("pvp",1)){
output("
<a name=\"pvp\"></a>
`^`bPvP (Player versus Player - Spieler gegen Spieler)`b`n`@
Legend of the Green Dragon enthält ein PvP-Element (PvP=Player vs. Player = Spieler gegen Spieler), wo Spieler andere Spieler angreifen können. Als neuer Spieler bist du die ersten ".getsetting("pvpimmunity",5) . " Spieltage, oder bis du " . getsetting("pvpminexp",1500) . ", Erfahrungspunkte gesammelt hast immun gegen Angriffe - es sei denn, du 
greifst selbst einen anderen Spieler an, dann verfällt deine Immunität. Einige Server haben die PvP-Funktion deaktiviert, dort kannst du also überhaupt nicht angegriffen werden (und auch selbst nicht angreifen). Du 
kannst im Dorf erkennen, ob PvP möglich ist, wenn es dort \"Kämpfe gegen andere Spieler\" gibt. Gibt es das nicht, ist PvP deaktiviert.`n
Auf diesem Server hast du ausserdem die Möglichkeit, auch nach der Schonzeit Immunität vor PvP-Angriffen zu erlangen (nicht jeder mag PvP). Näheres dazu erfährst du in der Jägerhütte.`n
`n
Wenn du bei einem PvP-Kampf stirbst, verlierst du alles Gold, das du bei dir hast, und " . getsetting("pvpdeflose", 5) . "% deiner Erfahrungspunkte. Du verlierst keine Waldkämpfe und auch sonst nichts. Wenn du selbst jemanden angreifst, 
kannst du " . getsetting("pvpattgain", 10) . "% seiner Erfahrungspunkte und all sein Gold bekommen. Wenn du aber verlierst, verlierst du selbst " . getsetting("pvpattlose", 15) . "% deiner Erfahrung und alles Gold. 
Wenn dich jemand angreift und verliert, bekommst du sein Gold und " . getsetting("pvpdefgain", 10) . "% seiner Erfahrungspunkte. Du kannst nur jemanden angreifen, der etwa dein Level hat 
also keine Angst, dass dich mit Level 1 ein Level 15 Charakter niedermetzelt. Das geht nicht.`n
Du kannst auch nicht von Spielern angegriffen werden, die zwar dein Level, aber einen wesentlich höheren Titel haben.`n
`n
Wenn du dir in der Kneipe ein Zimmer nimmst, um dich auszuloggen, schützt du dich vor gewöhnlichen Angriffen. Der einzige Weg, jemanden in der Kneipe anzugreifen, ist 
den Barkeeper zu bestechen, was eine kostspielige Sache sein kann. Zum Ausloggen \"In die Felder verlassen\" (oder sich überhaupt nicht ausloggen) bedeutet, dass du von jedem angegriffen werden kannst, ohne dass er Gold dafür bezahlen müsste. Du 
kannst nicht angegriffen werden, solange du online bist, nur wenn du offline bist. Je länger du also spielst, umso sicherer bist du ;-). Ausserdem kann dich niemand mehr angreifen, wenn du bereits bei einem Angriff getötet worden bist, 
also brauchst du nicht zu befürchten, in einer Nacht 30 oder 40 mal niedergemetzelt zu werden. Erst wenn du dich wieder eingeloggt hast, wirst du wieder angreifbar 
wenn du getötet wurdest.`n
`n
<a name=\"ooc\"></a>
`^`b`iO`iut `iO`if `iC`iharacter`b`n`@
Als Out of Character, oder kurz OOC, werden alle Dinge bezeichnet, die du in den Spielräumen (Dorfplatz, Friedhof, Häuser...) als Person hinter deinem Charakter sagst. Das Gegenteil sind die sogenannten Ingame-Gespräche, die Rolle, welche du mit deinem Charakter ausgestaltest.`n
OOC ist unerwünscht, da es den Spielfluss stört, die Atmosphäre beeinträchtigt und meistens Belanglosigkeiten enthält. Solltest du einem anderen Spieler etwas mitteilen wollen und es dir unmöglich sein, dies im Rollenspiel zu tun, so nutze bitte die Ye-Olde-Mailfunktion.`n
Die einzigen Chats, in denen OOC offiziell erlaubt ist, sind der OOC-Raum im Dorfamt (wer hätte das gedacht.. ; ) ) und die Kommentarfunktion der MOTD.`n
`bOOC wird von den Moderatoren nur sehr ungern gesehen! Bei ausufernden Gesprächen außerhalb der Charaktere in Chats, die dem Rollenspiel vorbehalten sind, werden dementsprechende Maßnahmen ergriffen!`b
`n
`n
<a name=\"sprachen\"></a>
`^Sprachen`n`@
Die Amts- und Spielsprache dieses Servers ist Deutsch. Jedoch kommt es von Zeit zu Zeit vor, dass einige Spieler ihre Charaktere in Kunstsprachen reden lassen, z.B. Drow oder Sindarin. Dazu gelten folgende Einschränkungen:`n
 - Auf nicht-öffentlichen Plätzen (Häuser, Trampelpfad, natürlich auch Rassenräume) gilt keine Einschränkung
 - Ansonsten ist die Nutzung solcher Sprachen in Grenzen zu halten; keine seitenlangen Diskussionen, jedoch werden gelegentliche Einstreuungen (z.B. ab und zu ein Wort) toleriert. 
`n
`n",true);
}
output("
`^`bBereit für die neue Welt!`b`n`@
Du solltest jetzt eine ziemlich gute Vorstellung davon haben, wie dieses Spiel in den Grundzügen funktioniert, wie du weiterkommst und wie du dich selbst schützt. Es gibt aber noch eine Menge mehr in dieser Welt, also erforsche sie!
Hab keine Angst davor zu sterben, besonders dann nicht, wenn du noch jung bist. Selbst wenn du tot bist, gibt es noch eine Menge zu tun!
",true); 

}else if($_GET['op']=="faq3"){
popup_header("Spezielle und technische Fragen");
output('<a href="petition.php?op=faq">Inhaltsverzeichnis</a>`n`n`c`bSpezielle und technische Fragen`b`c'
		.get_extended_text('faq_technical'),true);
	
}else if ($_GET['op']=="faq"){
popup_header("Frequently Asked Questions (FAQ)");
output("
`^Willkommen bei Legend of the Green Dragon. `n
`n`@
Eines Tages wachst du in einem Dorf auf. Du weisst nicht warum. Verwirrt läufst du durch das Dorf, bis du schliesslich auf den Dorfplatz stolperst. Da du nun schonmal da bist, fängst du an, lauter dumme Fragen zu stellen. Die Leute (die aus irgendeinem Grund alle fast nackt sind) werfen dir alles mögliche an den Kopf. Du entkommst in eine Kneipe, wo du in der nähe des Eingangs ein Regal mit Flugblättern findest. Der Titel der Blätter lautet: \"Fragen, die schon immer fragen wolltest, es dich aber nie getraut hast\". Du schaust dich um, um sicherzu stellen, dass dich niemand beobachtet, und fängst an zu lesen:`n
`n
\"Du bist also ein Newbie. Willkommen im Club. Hier findest du Antworten auf Fragen, die dich quälen. Nun, zumindest findest du Antworten auf Fragen, die UNS quälten. So, und jetzt lese und lass uns in Ruhe!\" `n
`n
`bInhalt:`b`n
<a href='petition.php?op=rules_short'>Kurzfassung der Regeln</a>`n
<a href='petition.php?op=rules'>Regeln dieser Welt</a>`n
<a href='petition.php?op=primer'>Fibel für neue Spieler</a>`n
<a href='petition.php?op=faq1'>Fragen zum Gameplay (generell)</a>`n
<a href='petition.php?op=faq2'>Fragen zum Gameplay (Spoiler!)</a>`n
<a href='petition.php?op=faq3'>Technische Fragen und Probleme</a>`n
`n
~Danke,`n
das Management.`n
",true);

}else if($_GET['op']=="rules"){
popup_header("Regeln dieser Welt");
output('
<a href="petition.php?op=faq">Inhaltsverzeichnis</a>`n`n',true);

output( get_extended_text('rules_long') , true );

}else if($_GET['op']=="rules_short"){
popup_header("Kurzfassung der Regeln");
output('
<a href="petition.php?op=faq">Inhaltsverzeichnis</a>`n`n
'.get_extended_text('rules_short'),true);

}else if($_GET['op']=="faq1"){
popup_header("Allgemeine Fragen");
output('
<a href="petition.php?op=faq">Inhaltsverzeichnis</a>`n`n

`c`bAllgemeine Fragen`b`c
'.get_extended_text('faq_general'),true);
}else if($_GET['op']=="faq2"){
popup_header("Allgemeine Fragen mit Spoiler");
output('
<a href="petition.php?op=faq">Inhaltsverzeichnis</a>`n`n
`&(Warnung! Die folgenden FAQs könnten einige Spoiler enthalten. Wenn du also lieber auf eigene Faust entdecken willst, solltest du hier nicht weiter lesen. Dies ist keine Anleitung. Es ist eine Selbsthilfebroschüre.)`&
`n
`n
`n
`n
`n
`n
`n
`n
`n
`n
`n
`n
`n
`n
'.get_extended_text('faq_game'),true);

}else{
	popup_header("Anfrage für Hilfe");
	
	output('<script language="javascript">window.resizeTo(600,550);</script>
		`c`b`&Anfrage an die Administration`&`b`c`n`n');
	
	if (count($_POST)>0){
		$p = $session['user']['password'];
		unset($session['user']['password']);
				
		if(!$session['user']['loggedin']) {
			$sql = 'SELECT login,acctid,uniqueid,lastip FROM accounts WHERE lastip = "'.addslashes($session['lastip']).'" OR uniqueid = "'.addslashes($session['uniqueid']).'" ORDER BY login, acctid';
			$res = db_query($sql);
			
			$sec_info = '';
			
			while($r = db_fetch_assoc($res) ) {
				
				$sec_info .= '`n'.$r['login'].' (AcctID '.$r['acctid'].', IP '.$r['lastip'].', ID '.$r['uniqueid'].')';
				
			}
		}
		
		$sql = "INSERT INTO petitions (author,date,body,pageinfo,lastact,IP,ID,connected,kat) VALUES (".(int)$session[user][acctid].",now(),\"".addslashes(output_array($_POST))."\",\"".addslashes(output_array($session,"Session:"))."\",NOW(),\"".$session['lastip']."\",'".$session['uniqueid']."','".addslashes($sec_info)."',".(int)$_POST['kat'].")";
		db_query($sql);
		$session['user']['password']=$p;
		output("Deine Anfrage wurde an die Admins gesendet. Bitte hab etwas Geduld, die meisten Admins 
		haben Jobs und Verpflichtungen ausserhalb dieses Spiels. Antworten und Reaktionen können eine Weile dauern.");
		
	}
	else{
		
		$str_kat_enum = 'enum';
		
		foreach($ARR_PETITION_KATS as $id => $kat) {
			
			$str_kat_enum .= ','.$id.','.$kat;
			
		}
		
		$arr_data = array('charname'=>$session['user']['login'],
							'email'=>$session['user']['emailaddress']
							);
		$arr_form = array('charname'=>'Name deines Characters:',
							'email'=>'Deine E-Mail Adresse:',
							'kat'=>'Art der Anfrage:,'.$str_kat_enum,
							'description'=>'Beschreibe dein Problem:`n,textarea,35,8');
		
		output('Bitte beschreibe das Problem so präzise wie möglich. Versuche auch eine ungefähre Einordnung der Anfragenart, da das
			die Bearbeitung beschleunigen kann. Wenn du Fragen über das Spiel hast,
			check die <a href="petition.php?op=faq">`bFAQ`b</a>, die `bRegeln`b und die `bDrachenbücherei`b auf dem Dorfplatz. 
			`nAnfragen, die das Spielgeschehen betreffen, werden 
			nicht bearbeitet - es sei denn, sie haben etwas mit einem Fehler zu tun.
			<form action="petition.php?op=submit" method="POST">',true);
							
		showform($arr_form,$arr_data,false,'Absenden!');
		
		output('</form>',true);

	}
}
popup_footer();
?>
