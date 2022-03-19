<?php
require_once('common.php');

checkday();

page_header('Debattierräume');

if ($_GET[op]=="diskus")
{
	output("`2Der Debattierraum liegt vor Dir!`n
	Hier bekommt das Volk Gehör und die Admins hören sich Wünsche, Anregungen und Beschwerden an.");
	output("Wie Dir scheint ist schon eine rege Diskussion im Gange!`n`n");
	addcommentary(false);
	viewcommentary("rat","Rufen",30,"ruft");
	
	addnav('OOC - Raum','ooc.php?op=ooc');
	
	if($session['user']['alive']) {addnav("Zurück","dorfamt.php");}
	else {addnav("Zurück","shades.php");}
		
}

else if ($_GET[op]=="ooc")
{
	output("`2OOC Raum-komischer Name, denkst Du Dir, als Du die Tür zu diesem Raum aufstösst!`n");
	output("Überall an den Wänden stehen leuchtende Scheiben und einige dir bekannte und weniger
	bekannte Gesichter starren wie gebannt darauf und klimpern auf bemalten Brettern herum-seltsame Runen`n`n");
	output("`^Du hast den OOC Raum betreten. Wenn Du Gespräche führen möchtest, die sich außerhalb Deines Charakters befinden,
	so führe sie bitte hier! Sollten sich andere Mitspieler irgendwo anders OOC unterhalten, dann weise sie bitte freundlich
	per Ye Olde Mail darauf hin, dass dies hier der richtige Ort dafür wäre!`n`n");
	addcommentary(false);
	viewcommentary("OOC","Tippen",30,"tippt");
	
	addnav('Diskussionsraum','ooc.php?op=diskus');
	
	if($session['user']['alive']) {addnav("Zurück","dorfamt.php");}
	else {addnav("Zurück","shades.php");}
}


page_footer();
?>
