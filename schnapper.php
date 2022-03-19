<?
/* *******************
TMSIDR Schnapper
Written by Romulus von Grauhaar
    Visit http://www.scheibenwelt-logd.de.vu

Schnapper ist ein kleines Zusatzskript f�r das Dorf. Wer die Scheibenwelt-Romane kennt,
wird auch mit dieser Figur etwas anfangen k�nnen. Schnapper bietet die unterschiedlichsten Waren feil,
allerdings wei� man nie so genau, ob sie etwas positives oder negatives bewirken. Au�erdem hat er
zuf�llig gew�hlte Waren und sein Sortiment schwankt (das einzige, was man immer
bei ihm bekommt, sind seine W�rstchen und Fleischpasteten).
Man kann Schnapper als "normales" Gesch�ft im Dorf (oder an beliebiger anderer Stelle) einbauen,
oder aber man f�gt in der village.php eine Zufallsaktion hinzu, dass einem Schnapper �ber den Weg l�uft.
Dazu erg�nzt man im Skript der village.php einfach folgenden Code:

_____finde:_____________________
if ($session['user']['alive']){ }else{
	redirect("shades.php");
}
_____f�ge danach ein:_____________

// Schnapper Mod by Romulus
if ($HTTP_GET_VARS['op']!="schnapper") {
switch(e_rand(1,10))
{
 case 1:
redirect("schnapper.php");
break;
case 2:
case 3:
case 4:
case 5:
case 6:
case 7:
case 8:
case 9:
case 10:
break;
}
}

Die Preise der einzelnen Waren lassen sich nat�rlich am Skriptanfang anpassen.

******************* */

$sausagecost="70";
$piecost="100";
$dragoncost="500";
$detectorcost="150";
$polishcost="1000";
$cremecost="500";
$stonecost="1000";
$potioncost="50";

require_once "common.php";
page_header("T.M.S.I.D.R. Schnapper");
output("`^`c`bT.M.S.I.D.R. Schnapper`b`c`6");

if ($HTTP_GET_VARS['op']==""){
  checkday();
output("`@ Auf deinem Weg durch die Stra�en l�uft dir der ber�hmt-ber�chtigte H�ndler
Treibe-mich-selbst-in-den-Ruin Schnapper �ber den Weg und beginnt sofort, seine Waren anzupreisen:
`5\"Hallo ".($session['user'][sex]?"meine Teuerste":"mein Freund")."! Ich habe heute einige besonders ausgefallene Waren im Angebot.
Oder willst du vielleicht den Klassiker, ein echtes Schnapper-W�rstchen zum Preis von nur $sausagecost Goldm�nzen? Und damit
treibe ich mich selbst in den Ruin!\"`@");
addnav("Kaufen");
addnav("W? Schnappers W�rstchen ($sausagecost Gold)","schnapper.php?op=w");
addnav("F? Schnappers Fleischpastete ($piecost Gold)","schnapper.php?op=f");
$shop1=e_rand(1,3);
$shop2=e_rand(1,3);
if($shop1==1) addnav("D? Schnappers Drachent�terelixier ($dragoncost Gold)","schnapper.php?op=d");
if($shop1==2) addnav("H? Schnappers extrag�nstiger Heiltrank ($potioncost Gold)","schnapper.php?op=h");
if($shop1==3) addnav("A? Schnappers Drachendetektor ($detectorcost Gold)","schnapper.php?op=a");
if($shop2==1) addnav("R? Schnappers R�stungspolitur ($polishcost Gold)","schnapper.php?op=r");
if($shop3==2) addnav("C? Schnappers Sch�nheitscreme ($cremecost Gold)","schnapper.php?op=c");
if($shop4==3) addnav("S? Schnappers Schleifstein ($stonecost Gold)","schnapper.php?op=s");
addnav("Nichts kaufen","market.php?op=schnapper");
}
if ($HTTP_GET_VARS['op']=="w"){
	if ($session['user']['gold']<$sausagecost)
	{
	output("`@Schnapper schaut dich b�se an. `5\"Da bietet man dir das Gesch�ft deines Lebens an und du hast noch nichtmal genug Gold dabei!\"`@");
	addnav("Zur�ck in die Stadt","market.php?op=schnapper");
	}
	else
	{
	$session['user']['gold']-=$sausagecost;
	output("`@Schnapper gibt dir eines seiner typischen W�rstchen aus seinem Bauchladen und schmiert etwas Senf darauf.`n
	`5\"Ich wusste doch, dass ich einen Kenner vor mir habe. Lass es dir gut schmecken!\"`@`n
	Misstrauisch be�ugst du das W�rstchen und beisst mutig hinein.`n");
	switch(e_rand(1,10)){
	case 1:
	case 2:
	case 3:
	case 4:
	case 5:
	case 6:
	output("`@Du w�rgst und spuckst das W�rstchen wieder aus. Es hat dermassen ekelhaft geschmeckt, dass du nur noch �belkeit
	versp�rst. Als du dich w�tend nach Schnapper umdrehst, ist er schon l�ngst in einer Menschenmenge verschwunden.`n`n
	`&Du verlierst einige Trefferpunkte!");
	if ($session['user']['hitpoints']<=5) { $session['user']['hitpoints']=1; }
	else { $session['user']['hitpoints']-=5; }
	addnav("Zur�ck in die Stadt","market.php?op=schnapper");
	break;
	case 7:
	case 8:
	case 9:
	output("`@Du w�rgst vor �belkeit, aber das W�rstchen bleibt in deinem Magen. Es hat zwar furchtbar ekelhaft geschmeckt, aber du
	f�hlst dich trotzdem gest�rkt.`n`n
	`&Du erh�lst einige Trefferpunkte!");
	$session['user']['hitpoints']+=5;
	addnav("Zur�ck in die Stadt","market.php?op=schnapper");
	break;
	case 10:
	output("`@Als du das W�rstchen herunterw�rgst, �berkommt dich ein derartig gro�er Schwall �belkeit, dass du umkippst und keine Luft mehr bekommst.
	Schnapper sieht derweil zu, dass er Land gewinnt, w�hrend du dein leben aushauchst. Und das an einem W�rstchen! `n`n
	`&Das war's wohl! Du bist tot und verlierst 5% deiner Erfahrung und all dein mitgef�hrtes Gold!");
	addnews("`%".$session['user'][name]."`7 starb an einem von Schnappers ber�chtigten W�rstchen.");
	$session['user']['hitpoints']=0;
	$session['user'][alive]=false;
          $session['user'][experience]*=0.95;
            $session['user']['gold'] = 0;
            addnav("Ankh-Morpork Times","news.php");
	} //switch
	} //else (genug Gold)
} // W�rstchen
if ($HTTP_GET_VARS['op']=="f"){
	if ($session['user']['gold']<$piecost)
	{
	output("`@Schnapper schaut dich b�se an. `5\"Da bietet man dir das Gesch�ft deines Lebens an und du hast noch nichtmal genug Gold dabei!\"`@");
	addnav("Zur�ck in die Stadt","market.php?op=schnapper");
	}
	else
	{
	$session['user']['gold']-=$piecost;
	output("`@Schnapper gibt dir eine seiner typischen Fleischpasteten aus seinem Bauchladen.`n
	`5\"Ich wusste doch, dass ich einen Kenner vor mir habe. Lass sie dir gut schmecken!\"`@`n
	Misstrauisch be�ugst du die Pastete und beisst mutig hinein.`n");
	switch(e_rand(1,10)){
	case 1:
	case 2:
	case 3:
	case 4:
	case 5:
	case 6:
	output("`@Du w�rgst und spuckst die Pastete wieder aus. Sie hat dermassen ekelhaft geschmeckt, dass du nur noch �belkeit
	versp�rst. Als du dich w�tend nach Schnapper umdrehst, ist er schon l�ngst in einer Menschenmenge verschwunden.`n`n
	`&Du verlierst einige Trefferpunkte!");
	if ($session['user']['hitpoints']<=10) { $session['user']['hitpoints']=1; }
	else { $session['user']['hitpoints']-=10; }
	addnav("Zur�ck in die Stadt","market.php?op=schnapper");
	break;
	case 7:
	case 8:
	case 9:
	output("`@Du w�rgst vor �belkeit, aber die Pastete bleibt in deinem Magen. Sie hat zwar furchtbar ekelhaft geschmeckt, aber du
	f�hlst dich trotzdem gest�rkt.`n`n
	`&Du erh�lst einige Trefferpunkte!");
	$session['user']['hitpoints']+=10;
	addnav("Zur�ck in die Stadt","market.php?op=schnapper");
	break;
	case 10:
	output("`@Als du die Pastete herunterw�rgst, �berkommt dich ein derartig gro�er Schwall �belkeit, dass du umkippst und keine Luft mehr bekommst.
	Schnapper sieht derweil zu, dass er Land gewinnt, w�hrend du dein leben aushauchst. Und das an einer Fleischpastete! `n`n
	`&Das war's wohl! DU bist tot und verlierst 5% deiner Erfahrung und all dein mitgef�hrtes Gold!");
	addnews("`%".$session['user'][name]."`7 starb an einer von Schnappers ber�chtigten Fleischpasteten.");
	$session['user']['hitpoints']=0;
	$session['user'][alive]=false;
          $session['user'][experience]*=0.95;
            $session['user']['gold'] = 0;
            addnav("Ankh-Morpork Times","news.php");
	} //switch
	} //else (genug Gold)
} // Fleischpastete
if ($HTTP_GET_VARS['op']=="d"){
	if ($session['user']['gold']<$dragoncost)
	{
	output("`@Schnapper schaut dich b�se an. `5\"Da bietet man dir das Gesch�ft deines Lebens an und du hast noch nichtmal genug Gold dabei!\"`@");
	addnav("Zur�ck in die Stadt","market.php?op=schnapper");
	}
	else
	{
	$session['user']['gold']-=$dragoncost;
	output("`@Schnapper �ffnet seinen Bauchladen und holt eine kleine Flasche hervor. Geheimnisvoll blickt er dich an`n
	`5\"Das ist die geheime Medizin der Drachent�ter. Trinkst du sie, wirst du stark wie ein Drache!\"`@`n
	Misstrauisch be�ugst du die Flasche und nimmst mutig einen kr�ftigen Schluck.`n");
	switch(e_rand(1,3)){
	case 1:
	output("`@Das Zeug schmeckt widerlich. Von dem ekelhaften Ges�ff f�hlst du dich eher schwach als stark.
	Als du dich w�tend nach Schnapper umdrehst, ist er schon l�ngst in einer Menschenmenge verschwunden.`n`n
	`&Du verlierst eine Kampfrunde!");
	if ($session['user']['turns']==0) { }
	else { $session['user']['turns']-=1; }
	addnav("Zur�ck in die Stadt","market.php?op=schnapper");
	break;
	case 2:
	output("`@Das Zeug schmeckt widerlich, aber es scheint dir trotzdem nichts schlimmes passiert zu sein. Allerdings auch nichts gutes.
	Als du dich w�tend nach Schnapper umdrehst, ist er schon l�ngst in einer Menschenmenge verschwunden.`n`n
	`&Das Drachent�terelixier hatte keine Wirkung!");
	addnav("Zur�ck in die Stadt","market.php?op=schnapper");
	break;
	case 3:
	output("`@Das Zeug schmeckt widerlich. Trotzdem merkst du, wie du dich wieder st�rker und kampfbereit f�hlst.`n`n
	`&Du erh�lst eine zus�tzliche Kampfrunde!");
	$session['user'][turns]+=1;
	addnav("Zur�ck in die Stadt","market.php?op=schnapper");
	} //switch
	} //else (genug Gold)
} // Drachent�terelixir
if ($HTTP_GET_VARS['op']=="h"){
	if ($session['user']['gold']<$potioncost)
	{
	output("`@Schnapper schaut dich b�se an. `5\"Da bietet man dir das Gesch�ft deines Lebens an und du hast noch nichtmal genug Gold dabei!\"`@");
	addnav("Zur�ck in die Stadt","market.php?op=schnapper");
	}
	else
	{
	$session['user']['gold']-=$potioncost;
	output("`@Schnapper �ffnet seinen Bauchladen und holt eine Flasche mit einem roten Kreuz darauf hervor.`n
	`5\"Das ist die extrag�nstige Schnapper-Spezialmedizin. Sie wird dich von deinen Wunden heilen!\"`@`n
	Misstrauisch be�ugst du die Flasche und nimmst mutig einen kr�ftigen Schluck.`n");
	switch(e_rand(1,3)){
	case 1:
	output("`@Der Trank schmeckt nach gar nichts. Allerdings macht sich trotzdem schon bald eine Wirkung breit: Du f�hlst dich irgendwie nicht mehr so gut wie vor der Medizin.
	Als du dich w�tend nach Schnapper umdrehst, ist er schon l�ngst in einer Menschenmenge verschwunden.`n`n
	`&Du verlierst einige Trefferpunkte!");
	if ($session['user']['hitpoints']<=3) { $session['user']['hitpoints']=1; }
	else { $session['user']['hitpoints']-=3; }
	addnav("Zur�ck in die Stadt","market.php?op=schnapper");
	break;
	case 2:
	output("`@Der Trank schmeckt nach gar nichts und offenbar ist es auch fast gar nichts, n�mlich nur gef�rbtes Wasser.
	Als du dich w�tend nach Schnapper umdrehst, ist er schon l�ngst in einer Menschenmenge verschwunden.`n`n
	`&Der Heiltrank hatte keine Wirkung!");
	addnav("Zur�ck in die Stadt","market.php?op=schnapper");
	break;
	case 3:
	output("`@Der Trank schmeckt nach gar nichts. Allerdings macht sich trotzdem schon bald eine Wirkung breit: Du f�hlst dich ges�nder als zuvor.
	Wer h�tte gedacht, dass der superg�nstige Schnappertrank wirkt.`n`n
	`&Du erh�lst einige Trefferpunkte!");
	$session['user']['hitpoints']+=8;
	addnav("Zur�ck in die Stadt","market.php?op=schnapper");
	} //switch
	} //else (genug Gold)
} // Heiltrank
if ($HTTP_GET_VARS['op']=="a"){
	if ($session['user']['gold']<$detectorcost)
	{
	output("`@Schnapper schaut dich b�se an. `5\"Da bietet man dir das Gesch�ft deines Lebens an und du hast noch nichtmal genug Gold dabei!\"`@");
	addnav("Zur�ck in die Stadt","market.php?op=schnapper");
	}
	else
	{
	$session['user']['gold']-=$detectorcost;
	output("`@Schnapper �ffnet seinen Bauchladen und holt eine Art W�nschelrute mit seltsamen metallenen verzierungen hervor.`n
	`5\"Mit diesem Ger�t kannst du ganz einfach Drachen in der Umgebung aufsp�ren!\"`@`n
	Mit diesen Worten verschwindet er in einer Menschenmenge.`n Irgendwie hast du das Gef�hl, dass dich Schnapper reingelegt hat, denn
	du hast gar keine Ahnung was du mit diesem St�ck Holz anfangen sollst.");
	addnav("Zur�ck in die Stadt","market.php?op=schnapper");
	} //else (genug Gold)
} // Detektor
if ($HTTP_GET_VARS['op']=="r"){
	if ($session['user']['gold']<$polishcost)
	{
	output("`@Schnapper schaut dich b�se an. `5\"Da bietet man dir das Gesch�ft deines Lebens an und du hast noch nichtmal genug Gold dabei!\"`@");
	addnav("Zur�ck in die Stadt","market.php?op=schnapper");
	}
	else
	{
	$session['user']['gold']-=$polishcost;
	output("`@Schnapper �ffnet seinen Bauchladen und holt eine kleine Flasche mit einer milchigen Tinktur hervor.`n
	`5\"Poliere deine R�stung mit diesem Mittel und du wirst ein �berw�ltigendes Ergebnis haben! Und dieses Poliertuch gibt's gratis dazu. Und damit treibe ich mich selbst in den Ruin!\"`@`n
	Misstrauisch be�ugst du die Tinktur, f�ngst dann aber trotzdem an, deine R�stung zu polieren.`n");
	switch(e_rand(1,3)){
	case 1:
	output("`@Nachdem du die Politur gr�ndlich mit dem Tuch verrieben hast, bekommt dein `4".$session['user'][armor]."`@ schwarze Flecken und scheint nicht mehr so stabil wie vorher zu sein.
	W�hrend du noch am polieren warst, hat sich Schnapper mucksm�uschenstill davongeschlichen.`n`n
	`&Deine R�stung wird schw�cher!");
	if ($session['user'][armordef]==0) { }
	else { $session['user'][armordef]-=1; }
	addnav("Zur�ck in die Stadt","market.php?op=schnapper");
	break;
	case 2:
	output("`@Nachdem du die Politur gr�ndlich mit dem Tuch verrieben hast, sieht dein `4".$session['user'][armor]."`@ noch genauso aus wie vorher.
	W�hrend du noch am polieren warst, hat sich Schnapper mucksm�uschenstill davongeschlichen.`n`n
	`&Die R�stungspolitur zeigt keine Wirkung!");
	addnav("Zur�ck in die Stadt","market.php?op=schnapper");
	break;
	case 3:
	output("`@Nachdem du die Politur gr�ndlich mit dem Tuch verrieben hast, erstrahlt dein `4".$session['user'][armor]."`@ in neuem Glanz.
	Du willst Schnapper f�r die hervorragende Qualit�t seiner Politur danken, doch er ist schon weitergegangen und verkauft seine W�rstchen auf der anderen Seite des Platzes.`n`n
	`&Deine R�stung wird st�rker!");
	$session['user'][armordef]+=1;
	addnav("Zur�ck in die Stadt","market.php?op=schnapper");
	} //switch
	} //else (genug Gold)
} // Politur
if ($HTTP_GET_VARS['op']=="c"){
	if ($session['user']['gold']<$cremecost)
	{
	output("`@Schnapper schaut dich b�se an. `5\"Da bietet man dir das Gesch�ft deines Lebens an und du hast noch nichtmal genug Gold dabei!\"`@");
	addnav("Zur�ck in die Stadt","market.php?op=schnapper");
	}
	else
	{
	$session['user']['gold']-=$cremecost;
	output("`@Schnapper �ffnet seinen Bauchladen und holt eine kleine Tube hervor, die eine blassrosafarbene Creme enth�lt.`n
	`5\"Das Rezept dieser Creme habe ich ".($session['user'][sex]?"vom Liebesgott Phallis":"von der Liebesg�ttin Aphrodante")." pers�nlich erhalten!\"`@`n
	Misstrauisch beginnst du, dein Gesicht mit der Creme einzureiben.`n");
	switch(e_rand(1,3)){
	case 1:
	output("`@Die Creme klebt furchtbar und als dein Blick in eine Wasserpf�tze f�llt, erschrickst du �ber dein entstelltes Gesicht.
	Als du dich w�tend nach Schnapper umdrehst, ist er schon l�ngst in einer Menschenmenge verschwunden.`n`n
	`&Du verlierst drei Charmepunkte!");
	if ($session['user'][charm]<=3) { $session['user'][charm]==0;}
	else { $session['user'][charm]-=3; }
	addnav("Zur�ck in die Stadt","market.php?op=schnapper");
	break;
	case 2:
	output("`@Die Creme klebt furchtbar und als dein Blick in eine Wasserpf�tze f�llt, merkst du, dass sich �berhaupt nichts getan hat.
	Als du dich w�tend nach Schnapper umdrehst, ist er schon l�ngst in einer Menschenmenge verschwunden.`n`n
	`&Die Sch�nheitscreme hatte keine Wirkung!");
	addnav("Zur�ck in die Stadt","market.php?op=schnapper");
	break;
	case 3:
	output("`@Die Creme klebt furchtbar und als dein Blick in eine Wasserpf�tze f�llt, bist du erstaunt, wie gut du pl�tzlich aussieht.`n`n
	`&Du erh�lst drei Charmepunkte!");
	$session['user'][charm]+=3;
	addnav("Zur�ck in die Stadt","market.php?op=schnapper");
	} //switch
	} //else (genug Gold)
} // Sch�nheitscreme
if ($HTTP_GET_VARS['op']=="s"){
	if ($session['user']['gold']<$stonecost)
	{
	output("`@Schnapper schaut dich b�se an. `5\"Da bietet man dir das Gesch�ft deines Lebens an und du hast noch nichtmal genug Gold dabei!\"`@");
	addnav("Zur�ck in die Stadt","market.php?op=schnapper");
	}
	else
	{
	$session['user']['gold']-=$stonecost;
	output("`@Schnapper �ffnet seinen Bauchladen und holt einen mit magischen Symbolen verzeirten, rauhen Stein hervor.`n
	`5\"Schleife damit deine Waffe und du wirst ein �berw�ltigendes Ergebnis feststellen!\"`@`n
	Misstrauisch be�ugst du den Schleifstein, f�ngst dann aber trotzdem an, deine Waffe zu schleifen.`n");
	switch(e_rand(1,3)){
	case 1:
	output("`@Nachdem du einige Minuten an deinem `4".$session['user'][weapon]."`@ geschliffen hast, bricht davon ein kleines St�ck ab und scheint nicht mehr so scharf wie vorher zu sein.
	W�hrend du noch am schleifen warst, hat sich Schnapper mucksm�uschenstill davongeschlichen.`n`n
	`&Deine Waffe wird schw�cher!");
	if ($session['user']['attack']==0) { }
	else { $session['user']['attack']-=1; }
	addnav("Zur�ck in die Stadt","market.php?op=schnapper");
	break;
	case 2:
	output("`@Nachdem du etliche Minuten an deinem `4".$session['user'][weapon]."`@ geschliffen hast, scheint sich seltsamerweisegenauso �berhaupt nichts getan zu haben.
	W�hrend du noch am schleifen warst, hat sich Schnapper mucksm�uschenstill davongeschlichen.`n`n
	`&Die R�stungspolitur zeigt keine Wirkung!");
	addnav("Zur�ck in die Stadt","market.php?op=schnapper");
	break;
	case 3:
	output("`@Nachdem du einige Minuten an deinem `4".$session['user'][weapon]."`@ geschliffen hast, merkst du deutlich eine Zunahme der ANgriffskraft.
	Du willst Schnapper f�r die hervorragende Qualit�t seiner Politur danken, doch er ist schon weitergegangen und verkauft seine W�rstchen auf der anderen Seite des Platzes.`n`n
	`&Deine Waffe wird st�rker!");
	$session['user']['attack']+=1;
	addnav("Zur�ck in die Stadt","market.php?op=schnapper");
	} //switch
	} //else (genug Gold)
} // Schleifstein
page_footer();
?>
