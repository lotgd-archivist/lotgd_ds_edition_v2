<?php
require_once('common.php');

checkday();

page_header('Debattierr�ume');

if ($_GET[op]=="diskus")
{
	output("`2Der Debattierraum liegt vor Dir!`n
	Hier bekommt das Volk Geh�r und die Admins h�ren sich W�nsche, Anregungen und Beschwerden an.");
	output("Wie Dir scheint ist schon eine rege Diskussion im Gange!`n`n");
	addcommentary(false);
	viewcommentary("rat","Rufen",30,"ruft");
	
	addnav('OOC - Raum','ooc.php?op=ooc');
	
	if($session['user']['alive']) {addnav("Zur�ck","dorfamt.php");}
	else {addnav("Zur�ck","shades.php");}
		
}

else if ($_GET[op]=="ooc")
{
	output("`2OOC Raum-komischer Name, denkst Du Dir, als Du die T�r zu diesem Raum aufst�sst!`n");
	output("�berall an den W�nden stehen leuchtende Scheiben und einige dir bekannte und weniger
	bekannte Gesichter starren wie gebannt darauf und klimpern auf bemalten Brettern herum-seltsame Runen`n`n");
	output("`^Du hast den OOC Raum betreten. Wenn Du Gespr�che f�hren m�chtest, die sich au�erhalb Deines Charakters befinden,
	so f�hre sie bitte hier! Sollten sich andere Mitspieler irgendwo anders OOC unterhalten, dann weise sie bitte freundlich
	per Ye Olde Mail darauf hin, dass dies hier der richtige Ort daf�r w�re!`n`n");
	addcommentary(false);
	viewcommentary("OOC","Tippen",30,"tippt");
	
	addnav('Diskussionsraum','ooc.php?op=diskus');
	
	if($session['user']['alive']) {addnav("Zur�ck","dorfamt.php");}
	else {addnav("Zur�ck","shades.php");}
}


page_footer();
?>
