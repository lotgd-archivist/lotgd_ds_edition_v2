<?php

// 25072004

/* Warchild's Magic Academy of the Dark Arts
* Die Akademie der geheimen K�nste
* coded by Kriegskind/Warchild
* Email: warchild@gmx.org
* February/March 2004
* v0.961dt
*
* Modifikationen von LotGD n�tig: 
* DB - Neues Feld seenAcademy(TINYINT(3)) in accounts, default 0
* newday.php - Zur�cksetzen von $session['user']['seenAcademy'] = 0 an jedem Tag
* village.php - Link auf die Akademie einbauen
*
* letzte Modifikation
* 18.3.2004, 17:35 Bibliothekserfolgswahrscheinlichkeit wieder auf 33% erh�ht (Warchild)
* Adminzugang entfernt
* Zauberladen eingebaut (25.07.2004, anpera)
*/

// Talion: Anpassung ans Gildensystem (Rabatte)

require_once("common.php");
addcommentary();
// Entscheidungsvariablen op1: Akademie betreten oder nicht, op2: Bezahlungsart und Studienart

page_header("Warchilds Akademie der geheimen K�nste");

$sql = "SELECT * FROM specialty WHERE specid='".$session['user']['specialty']."'";
$row = db_fetch_assoc(db_query($sql));

if(file_exists("module/".$row['filename'].".php"))
{
  require_once "module/".$row['filename'].".php";
  $f1 = $row['filename']."_info";
  $f1();
  $f2 = $row['filename']."_run";
}
else
{
  function blank(){ return false;}
  $f2 = "blank";
}
// Kosten: gestaffelt nach Skillevel
$skills = array($row['specid']=>$row['usename']);
$akt_magiclevel = (int)($session['user']['specialtyuses'][$skills[$session[user][specialty]]] + 1); // man faengt bei 0 an ;o)

$cost_low = (($akt_magiclevel + 1) * 50)-$session['user']['reputation'];
$cost_medium =(($akt_magiclevel + 1)* 60)-$session['user']['reputation']; //plus ein Edelstein
$cost_high = (($akt_magiclevel + 1) * 75)-$session['user']['reputation']; //plus 2 Edelsteine

// tcb: Gesamtanzahl an Anwendungen darf 20 nicht �berschreiten
$uses_ges = $session['user']['specialtyuses']['darkartuses'] + $session['user']['specialtyuses']['thieveryuses'] + $session['user']['specialtyuses']['magicuses'];

$min_dk = 1; // wieviele DKs muss ein User haben um eintreten zu d�rfen?

$rowe = user_get_aei('seenacademy');

if($_GET['op'] == 'buy_do') {
	
	$_GET['op1'] = 'blank';
	
	$item = item_get_tpl(' tpl_id="'.$_GET['tpl_id'].'" ');
		
	$name = $item['tpl_name'];
	
	$goldprice = round($item['tpl_gold'] * $_GET['gold_r']);
	$gemsprice = round($item['tpl_gems'] * $_GET['gems_r']);
	
	$item['tpl_gold'] = round($goldprice * 0.8);
	$item['tpl_gems'] = 0;
			
	if ( item_count( ' tpl_id="'.$_GET['tpl_id'].'" AND owner='.$session['user']['acctid'] ) > 0){
		output("`vDiesen Zauber hast du schon. Du musst ihn entweder aufbrauchen, oder verkaufen, bevor du ihn neu kaufen kannst.");
		addnav("Etwas anderes kaufen","academy.php?op1=bringmetolife&action=buy");
	}else{
		output("`vDu deutest auf den Namen in der Liste. Bis auf den Namen des Zaubers \"`V$name`v\" verschwinden alle anderen Worte von der Liste und der Zauberer gibt dir, was du verlangst. Gerade, als du bezahlen willst, schweben ".($goldprice?"`^".$goldprice." `vGold":"")." ".($gemsprice?"`#".$gemsprice."`v Edelsteine":"")." aus deinen Vorr�ten in die Hand des Zauberers. ");
				
		$session['user']['gold'] -= $goldprice;
		$session['user']['gems'] -= $gemsprice;
		
		item_add($session['user']['acctid'],0,false,$item);			

		addnav("Mehr kaufen","academy.php?op1=bringmetolife&action=buy");
	}
	
}
else if($_GET['op'] == 'sell_do') {		
	
	$_GET['op1'] = 'blank';
	
	$item = item_get(' id="'.$_GET['id'].'" ', false);
	
	$name = $item['name'];
	
	$goldprice = round($item['gold'] * $_GET['gold_r']);
	$gemsprice = round($item['gems'] * $_GET['gems_r']);
		
	output("`vDer alte Zauberer begutachtet $name`v. Dann �berreicht er dir sorgf�ltig abgez�hlt ".($goldprice?"`^".$goldprice." `vGold":"")." ".($gemsprice?"`#".$gemsprice."`v Edelsteine":"")." und l�sst den Zauber verschwinden. W�rtlich. ");
	addnav("Mehr verkaufen","academy.php?op1=bringmetolife&action=sell");
									
	item_delete(' id='.(int)$_GET['id'] );
			
	$session['user']['gold'] += $goldprice;
	$session['user']['gems'] += $gemsprice;

}		

// zwei op-Variablen gesetzt und User erfuellt Bedingungen
if (($HTTP_GET_VARS['op1'] <> "" && $HTTP_GET_VARS['op2'] <> "") && $session['user']['dragonkills']>= $min_dk && $rowe['seenacademy'] == 0 && $session['user']['turns']>0)
{
	// op1='enter' und op2=0 dann eintreten
	if ($HTTP_GET_VARS['op1'] == "enter" && $HTTP_GET_VARS['op2'] == "0")
	{
		output("`$`b`c Das Innere der Akademie`c`b`n`n");
		output("`^Im Inneren ist es recht k�hl und ihr schreitet einen dicken schwarz/roten Teppich");
		output("mit seltsamen magischen Symbolen entlang.`n");
		output("\"Du kannst hier versuchen, Deine`7");
output(" `i".$info['specname']."`i");





		output(" `^allein zu verbessern, oder Du nimmst eine Stunde bei mir.`n");
		output("Ich werde sicherstellen, dass Du nicht versagst...\"`n");
		output("Warchild f�hrt Dich zu einem kleinen Tischchen, auf dem ein dickes ledernes Buch liegt und");
		output("�ffnet es f�r Dich. Es enth�lt eine Preisliste:`n`n<ul>",true);
		$f2("academy_desc");


























		output("</ul>`nDirekt unter den Preisen steht sehr klein geschrieben, ein wenig verwischt und kaum lesbar:`n",true);
		output("`3Da Magie `bunberechenbar`b ist, handelt jeder Sch�ler auf eigene Gefahr und die Akademie erstattet keine Kosten im Falle von Lernversagen oder anderen Ungl�cken!");
		output("`^Dir ist klar, dass du w�hrend des Lernens nat�rlich nicht im Wald k�mpfen kannst.");
		output("`n`n`3Falls dir das Lernen zu m�hsam sein sollte, steht in der Akademie auch ein Zauberladen zur Verf�gung, in dem man Fertigzauber mit begrenzter Lebensdauer, die von den Meistern des Geistes und der Materie erschaffen wurden,  kaufen kann.");
		if($uses_ges > 20) {
			output("`n`n`3Leider hast DU bereits zu viel gelernt. Niemand hier kann Dir noch etwas beibringen!");
		}
		else {
			addnav("Selbststudium","academy.php?op1=enter&op2=study");
			addnav("Praktische �bung","academy.php?op1=enter&op2=practice");
			addnav("Stunde bei Warchild","academy.php?op1=enter&op2=warchild");
		}
		addnav("Mit anderen Studenten reden","academy.php?op1=enter&op2=chat");
		addnav("Zauberladen","academy.php?op1=bringmetolife");
		addnav("Zur�ck ins Dorf","village.php");
	}
	// n�chster Fall: Chat in der Akademie
	/*
	* DEBUG: FUNZT?
	*/
	if ($HTTP_GET_VARS['op1'] == "enter" && $HTTP_GET_VARS['op2'] == "chat")
	{
		output("Du gesellst Dich zu einer Gruppe Studenten, die um ein Pentagramm herumstehen.`n");
		output("Sie er�rtern die fiesen Konsequenzen einer misslungenen D�monenbeschw�rung...");
		output("`n`nZuletzt sagten sie:");
		output("`n`n");
		addnav("Wieder hineingehen","academy.php?op1=enter&op2=0");
		viewcommentary("academy","Sprich",25);
	}
	
	// check if User has enough gems/gold if he wants to learn
	// 1st Case: STUDY
	if ($HTTP_GET_VARS['op1'] == "enter" &&
	$HTTP_GET_VARS['op2'] == "study" &&
	$session['user'][gold] < $cost_low)
	{
		output("`$`b`c Das Innere der Akademie`c`b`n`n");
		output("`n`$ Leider kannst Du den geforderten Preis nicht bezahlen.`^");
		addnav("Nochmal nachschauen","academy.php?op1=enter&op2=0");
	}
	else if ($HTTP_GET_VARS['op1'] == "enter" &&
	$HTTP_GET_VARS['op2'] == "study" &&
	$session['user'][gold] >= $cost_low)
	{
		// subtract costs
		$session['user'][gold] = $session['user'][gold] - $cost_low;
		$goldpaid = $cost_low;
		//debuglog("paid $goldpaid to the academy");

		// war heute schonmal hier...
		user_set_aei(array('seenacademy'=>1));
		
		$session['user']['turns']--;

		if ($session['user']['drunkenness'] > 0) // too drunk to learn
		{
			output("`$`b`c Bibliothek der Akademie`c`b`n`n");
			output("`^Ver*hic*dammt! Du h�ttest Dich mit dem...`$ ale`^... zur�ckhalten sollen! Du kannst Dich einfach");
			output("nicht genug konzentrieren um irgendetwas zu lernen.`n");
			output("Frustriert verl�sst Du die Akademie nach einiger Zeit und stapfst ins Dorf zur�ck.");
			addnav("Zur�ck ins Dorf","village.php");
		}
		else // hier geht das Train los
		{
			output("`$`b`c Bibliothek der Akademie`c`b`n`n");
			$rand = e_rand(1,3);
			switch ($rand)
			{
				case 1:
				output("`^Du sitzt in der Bibliothek mit dem Buch in der Hand, als es pl�tzlich");
				output("nach Dir schnappt und Dir in die Hand `4beisst! `6Der Schmerz ist furchtbar!`^`n");
				output("Du versuchst verzweifelt das Buch wieder abzusch�tteln w�hrend einige andere ");
				output("Studenten einen kleinen Kreis um Dich bilden und sich schlapplachen.`n");
				output("Frustiert und fluchend verl�sst Du die Akademie.`n`n");
				output("`5Du verlierst einige Lebenspunkte!");
				$session['user']['hitpoints'] = $session['user']['hitpoints'] - $session['user']['hitpoints'] * 0.2;
				break;
				case 2:
				output("`^Du verbringst einige Zeit in der Akademie und liest intensiv, doch schon bald ergeben");
				output("die W�rter irgendwie keinen Sinn mehr. Schliesslich gibst Du auf.`n");
				output("Frustiert verl�sst Du die Akademie.");
				break;
				case 3:
				output("`7Du nimmst Dir einen grossen, ledergebundenen Folianten und �ffnest ihn.");
				output("Zun�chst geschieht nichts, doch pl�tzlich `2redet das Buch mit Dir!`7`n");
				output("Fasziniert lauschst Du den geheimen Worten und lernst wirklich etwas �ber");
				output(" `i".$info['specname']."`i");





				output(". Breit grinsend und stolz auf Dein neues Wissen verl�sst Du die Akademie.`n`n");
				increment_specialty();
				break;
			}
			addnav("Zur�ck ins Dorf","village.php");
		}
	}
	
	// 2nd Case: PRACTICE
	if ($HTTP_GET_VARS['op1'] == "enter" &&
	$HTTP_GET_VARS['op2'] == "practice" &&
	($session['user'][gold] < $cost_medium ||
	$session['user'][gems] < 1
	))
	{
		output("`$`b`c Das Innere der Akademie`c`b`n`n");
		output("`n`$ Leider kannst Du den geforderten Preis nicht bezahlen.`^");
		addnav("Nochmal nachschauen","academy.php?op1=enter&op2=0");
	}
	else if ($HTTP_GET_VARS['op1'] == "enter" &&
	$HTTP_GET_VARS['op2'] == "practice" &&
	($session['user'][gold] >= $cost_medium ||
	$session['user'][gems] >= 1))
	{
		// subtract costs
		$session['user'][gold] = $session['user'][gold] - $cost_medium;
		$session['user'][gems]--;
		$goldpaid = $cost_medium;
		//debuglog("paid $goldpaid and 1 gem to the academy");

		// war heute schonmal hier...
		user_set_aei(array('seenacademy'=>1));
		
		$session['user']['turns']--;

		if ($session['user']['drunkenness'] > 0) // too drunk to learn
		{
			output("`$`b`c Das Innere der Akademie`c`b`n`n");
			$f2("academy_pratice");






























			addnav("Zur�ck ins Dorf","village.php");
		}
		else // hier geht das Train los
		{
			output("`$`b`c Bibliothek der Akademie`c`b`n`n");
			$rand = e_rand(1,3);
			switch ($rand)
			{
				case 1:
				output("`^Du verl�sst den Trainingsbereich geschlagen und mit einigen blutenden Wunden.`n");
				output("Gesenkten Hauptes gehst Du ins Dorf zur�ck.`n`n");
				output("`5Du verlierst ein paar Lebenspunkte!");
				$session['user']['hitpoints'] = $session['user']['hitpoints']  * 0.9;
				break;
				case 2:
				case 3:
				output("`7Nach einer forderndern Trainingsstunde, die Du souver�n meisterst, machst Du Dich auf den Heimweg.`n");
				output("Bevor Du gehst, gratuliert Dir Warchild zu dem erfolgreichen Training.`n`n");
				increment_specialty();
				break;
			}
			addnav("Zur�ck ins Dorf","village.php");
		}
	}
	
	// 3rd Case: WARCHILD
	if ($HTTP_GET_VARS['op1'] == "enter" &&
	$HTTP_GET_VARS['op2'] == "warchild" &&
	($session['user'][gold] < $cost_high ||
	$session['user'][gems] < 2))
	{
		output("`$`b`c Das Innere der Akademie`c`b`n`n");
		output("`n`$ Leider kannst Du den geforderten Preis nicht bezahlen.`^");
		addnav("Nochmal nachschauen","academy.php?op1=enter&op2=0");
	}
	else if ($HTTP_GET_VARS['op1'] == "enter" &&
	$HTTP_GET_VARS['op2'] == "warchild" &&
	($session['user'][gold] >= $cost_high ||
	$session['user'][gems] >= 2))
	{
		// subtract costs
		$session['user'][gold] = $session['user'][gold] - $cost_high;
		$session['user'][gems] = $session['user'][gems] - 2;
		$goldpaid = $cost_high;

		// war heute schonmal hier...
		user_set_aei(array('seenacademy'=>1));
		
		$session['user']['turns']--;
		
		//debuglog("paid $goldpaid and 2 gems to the academy");		
		output("`$`b`c Das Innere der Akademie`c`b`n`n");
		if ($session['user']['drunkenness'] > 0) // too drunk to learn
		{
			output("`^Als Warchild Deine Fahne riecht schaut er Dich angewidert an.`n");
			output("`7`i\"Betrunkene Kreatur! Von mir wirst Du nichts lernen!\"`i`^`n");
			output("Er wirft Dich hinaus und Dein Geld und Deine Edelsteine hinter Dir her.`n");
			output("Bem�ht, die kullernden Edelsteine aufzusammeln, kannst Du am Ende einige M�nzen nicht mehr finden.`n`n");
			output("`5Du verlierst etwas Gold des Lehrgelds!");
			$session['user'][gold] +=  $cost_high * 0.67;
			$session['user'][gems] = $session['user'][gems] + 2;
		}
		else // hier geht das Train los
		{
			output("`7Du verbringst einige Zeit im schwarzen Turm der Akademie in der h�chsten Kammer");
			output("und `4Warchild`7 er�ffnet Dir eine neue Dimension Deiner F�higkeiten.`n");
			output("Du verl�sst den Ort zufrieden und wissender als zuvor!`n`n");
			increment_specialty();
		}
		addnav("Zur�ck ins Dorf","village.php");
	}
}else if($_GET['op1']=="bringmetolife"){ // Zauberladen (written on a cassiopeia while taking a bath)
	output("`b`c`VInstant-Zauber aller Art`c`b`0`n");
	
	$show_invent = true;

	require_once(LIB_PATH.'dg_funcs.lib.php');
	if($session['user']['guildid'] && $session['user']['guildfunc'] != DG_FUNC_APPLICANT) {	
		$rebate = dg_calc_boni($session['user']['guildid'],'rebates_spells',0);
	}



	if ($_GET[action]=="sell"){
						
		output("`vDu zeigst dem alten Zauberer alle deine Zauber und er sagt dir, was er daf�r bezahlen w�rde.`n`n");
			
		item_show_invent(' (spellshop = 2 OR spellshop = 3) AND owner='.$session['user']['acctid'], false, 2, 1, 1, '`vDu hast keine Zauber, die du dem Alten anbieten k�nntest.');
		
		addnav('Zur�ck');			
		addnav("Zum Laden","academy.php?op1=bringmetolife");
	}else if ($_GET[action]=="buy"){ // ok, water's getting cold ^^
						
		output("`v\"`RDu willst Magie nutzen, ohne sie m�hsam studieren zu m�ssen? Dann bist du hier genau richtig.`v\" Mit diesen Worten �berreicht dir der Alte eine Liste aller Zauber, die er dir anbieten kann. \"`RBitte sehr. W�hle dir etwas aus.".($rebate?" Achja, dank deiner Gildenmitgliedschaft gew�hre ich dir `^".$rebate." %`R Rabatt!":"")."`v\"`n`n");
		
		$rebate = (100 - $rebate) * 0.01;
		
		item_show_invent(' (spellshop = 1 OR spellshop = 3) ', true, 1, $rebate, $rebate, '`v"`RTut mir Leid, mein Freund. Wir haben keine Zauber f�r dich.`v"');
		
		addnav('Zur�ck');					
		addnav("Zum Laden","academy.php?op1=bringmetolife");
	}else{ 
		output("`vDurch schwere und reich verzierte Holzt�ren betrittst du den Zauberladen der Akademie. Hier bietet ein �lterer Zauberer die Werke verschiedenster Akademie-Magier an, denen es gelungen ist, selbst magisch unbegabten ".($races[$session['user'][race]])."en wie dir die Anwendung ihrer Zauber zu erm�glichen.");
		output(" Nat�rlich geht bei Magiern nichts ohne entsprechende Bezahlung, so rechnest du auch hier mit saftigen Preisen, um die hohen Entwicklungskosten, die wohl durch zahlreiche Fehlschl�ge und unz�hlige zu Bruch gegangene Zauberutensilien zu erkl�ren sind, auszugleichen.");
		addnav("Zauber verkaufen","academy.php?op1=bringmetolife&action=sell");
		addnav("Zauber kaufen","academy.php?op1=bringmetolife&action=buy");
	}
	addnav("Zur Akademie","academy.php".($session['user']['dragonkills']>$min_dk?"?op1=enter&op2=0":"")."");
}
// auf jeden Fall Begr��ung und Einleitung wenn keine Params gesetzt
else if($_GET['op'] != 'buy_do' && $_GET['op'] != 'sell_do')
{
	output("`$`b`c Warchilds Akademie der geheimen K�nste`c`b`n`n");
	output("`^Vorsichtig n�herst Du Dich dem riesigen Tor der Akademie und verharrst einen Augenblick,");
	output("um die Inschrift �ber dem Torbogen zu betrachten.`n");
	output(" \"`8`iAuch diese Worte werden vergehen`i`^\" steht dort f�r die Ewigkeit in geschwungenen goldenen Lettern.`n");
	output("Das zweifl�gelige dunkelgraue Gem�uer mit vergitterten Fenstern und dem drohend in den Himmel ragenden");
	output("schwarzen Turm scheint die Worte �ber Deinem Kopf noch zu unterstreichen.");
	output("Ein kleines Schild neben dem Eingang warnt vor den �blen Konsequenzen von Magie und Alkohol.`n");

	// Heute schonmal hier gewesen? Dann wird's wohl nix :P
	if ($rowe['seenacademy'] == 1 || $session['user']['turns']<1)
	{
		output ("`n`7Du versp�rst irgendwie kein sonderlich grosses Bed�rfnis, heute noch einmal die Schulbank zu dr�cken, ");
		output("also schlenderst Du zum Dorf zur�ck.");
		addnav("Zur�ck ins Dorf","village.php");
	}elseif ($session['user']['reputation']<-5){
		output ("`n`7Doch als du dich n�herst, scheint dich ein Mann in einem schwarzen Mantel zu erkennen und dir auf jeden Fall den Eintritt verweigern zu wollen. ");
		output(" Dein schlechter Ruf eilt dir voraus. Es w�rde dir ja nichts ausmachen, den Kerl niederzumetzeln, aber er d�rfte um einiges besser in ");
		if ($session['user']['specialty'] == 1) output("den `\$Dunklen K�nsten`7");
		if ($session['user']['specialty'] == 2) output("den `%Mystischen Kr�ften`7");
		if ($session['user']['specialty'] == 3) output("`^Diebesk�nsten`7");
		output(" sein als du. So gehst du murrend ins Dorf zur�ck.");
		addnav("Zur�ck ins Dorf","village.php");
	}else	// User darf heute noch hier rein
		// Wenn User genug Dragonkills hat, Zutritt erlauben
		if ($session['user']['dragonkills']> $min_dk)
		{
			output("`^Warchild steht in der N�he des Eingangs zur Akademie und wartet, bis Du den Hof �berquert hast, um Dich anzureden.`n");
			output("\"`9Ich h�rte bereits von Deinen grossen Taten. Tritt doch ein...`^\" sagt er und l�chelt d�nn.`n");
			output("Dann winkt er Dich herein.`n`n");
			addnav("Eintreten","academy.php?op1=enter&op2=0");
			addnav("Zur�ck ins Dorf","village.php");
		}
		// wenn User nicht ausreichend Dragonkills hat, Zutritt ablehnen
		else
		{
			output("In dem ausladenden Innenhof steht ein Mann in einem schwarzen Mantel, der leicht im Wind flattert. Er starrt Dich so eindringlich an, dass es Dir unertr�glich wird, ihn weiter anzusehen.");
			output("Als Du den Blick senkst flattert eine einzelne Kr�he vom Dachfirst herunter und landet zwischen");
			output("den F�ssen des Mannes, wo sie einige Blumensamen aufpickt, die dort hingeweht wurden.`n");
			output("\"`9Komm wieder, wenn Du bereit f�r meinen Unterricht bist. Bis dahin kannst du dich h�chstens im Zauberladen umsehen.`^\" sagt Warchild ruhig zu Dir.`n");
			output("Eingesch�chtert schleichst Du zur�ck ins Dorf.`n`n");
			addnav("Zauberladen betreten","academy.php?op1=bringmetolife");
			addnav("Zur�ck ins Dorf","village.php");
		}
}
page_footer();
?>
