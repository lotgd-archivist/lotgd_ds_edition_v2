<?php
require_once('common.php');

page_header();

addnav('Zur Startseite','index.php');

output('	
	`@`c`bWichtig!`c`b`&`n
	
	Auf dieser Seite findest du die für '.getsetting('townname','Atrahor').' geltenden `$Regeln`&. Du solltest
	sie VOR deiner Anmeldung genau lesen und verstehen! Bei Löschungen aufgrund von Verstößen dagegen 
	gibt es keinerlei Ansprüche auf langwierige Erklärungen oder gar Wiederherstellung.`n
	Gleich zu Beginn die sehr wichtige `$Namensregelung`&. Charakter, die offensichtlich dagegen 
	verstoßen, werden ohne vorherige Warnung gelöscht:`n`n
	`&`iDein Name darf keinen Titel (Lord, Graf, Meister etc.) und keine Beschreibung (ScharfesSchwert, grünerHund etc.) enthalten. 
	Er sollte nach Mittelalter klingen, mindestens jedoch nach Mythen und Sagen. 
	Englische Namen sind dafür nur bedingt geeignet. 
	Namen von Prominenten, Personen der Zeitgeschichte oder Film-Helden sind ebenfalls nicht erwünscht.`n
	Absolute Negativbeispiele:`n
	`$d-503, willi, einsamerWolf, KnechtRuprecht, MegaBong, Red, Green etc., Dragonhunter.`&`i`n
	Falls du keine Vorstellung hast, welcher Name geeignet ist, kannst du dich von der Einwohnerliste
	inspirieren lassen (NICHT kopieren!)`n`n	
	`$Im Folgenden eine Kurzfassung der <b>Regeln</b>:`i`n`n
	'.get_extended_text('rules_short').'`n`n
	`i`&Bei Fragen zum Spiel solltest du zunächst die `^FAQ`&, danach die `^Drachenbibliothek`& auf dem Dorfplatz
	konsultieren. Falls dann noch Fragen bestehen, wende dich an einen Moderator oder Administrator 
	( mit * Sternchen gekennzeichnet ; ) ) oder schreibe eine Anfrage.`n
	`^BITTE`& mülle keinen öffentlichen Platz (Dorfplatz, Marktplatz, Reich der Schatten etc.) mit Fragen wie
	"Wo finde ich den Drachen?" oder "Kann mir wer Gold leihen?" zu! Allerhöchstens ist der OOC-Raum im Dorfamt für
	solche Angelegenheiten vorgesehen.`nRegelmäßig solltest du einen Blick in die MoTD werfen. Dort kündigt
	die Verwaltung dieses Servers aktuelle Änderungen oder Neuerungen an.`n
	So, das war\'s! ; )`n`n
	Ein spannendes Leben und viel Spaß in '.getsetting('townname','').' wünscht Dir`n
	Das Drachenserver-Team!',true);
	
	output('`n	
	`c<input id="ok_button" type="button" value="Ich habe die Regeln gelesen und akzeptiere sie, Weiter!" onclick=\'document.location="create.php?r='.$_GET['r'].'"\'>`c

		<script type="text/javascript" language="JavaScript">
			var count = 20;
			counter();
			function counter () {
				if(count == 0) {
					document.getElementById("ok_button").value = "Ich habe die Regeln gelesen und akzeptiere sie, Weiter!";
					document.getElementById("ok_button").disabled = false;
				}
				else {
					document.getElementById("ok_button").value = "Weiter! (noch "+count+" Sekunden)";
					document.getElementById("ok_button").disabled = true;
					count--;
					setTimeout("counter()",1000);
				}
			}	
		</script>
	',true);
	
page_footer();
	
?>