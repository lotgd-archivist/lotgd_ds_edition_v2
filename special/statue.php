<?php
/*
statue
by Vaan
12//4//2004
*/

require_once"common.php";
page_header("Seltsame Statue");
if ($HTTP_GET_VARS[op]=="")
{
	$session[user][specialinc]="statue.php";
	output("W�hrend du deines Weges gehst, kommst du an einer riesigen Statue vorbei an der ein gro�es Schild angelehnt steht. Du versuchst zu entziffern was auf dem alten Schild steht.");
	output("Du liest: `n\"In mir ist etwas verborgen, `nin mir ist was versteckt, `nin mir ist nichts gutes nichts schlechtes,`nwer hat das wohl ausgeheckt?\"");
	output("Was willst du tun?");
	addnav("Um die Statue kriechen und nach irgend einem Gegenstand suchen","forest.php?op=such");
	addnav("Einfach weiter gehen","forest.php?op=gehe");
}
if($HTTP_GET_VARS[op]=="such")
{
	output("Du beginnst mit der Suche. Nach einiger Zeit findest du ein kleines Loch and der R�ckseite der Statue. Du steckst deinen Arm hindurch und bekommst etwas zu fassen...");
	switch(e_rand(1,13))
	{
		case 1:
		case 2:
			output("Es scheint so als ob der Gegenstand festgebunden sei. Es dauert eine Ewigkeit bis du den Gegenstand hinaus bekommst.");
			output("Da du so lange gebraucht hast verlierst du f�r heute einen Waldkampf");
			output("Doch jetzt liegt er endlich in deiner Hand. Du schaust dir den kleinen Gegenstand, der Dich an eine Golddublone erinnert, an und f�hlst dich gest�rkt.");
			$session[user][turns]-=1;
			$session[user][attack]+=3;
			$session[user][specialinc]="";
			addnav("Zur�ck in den Wald","forest.php");
		break;
		case 3:
		case 4:
			output("Es scheint so als ob der Gegenstand festgebunden sei. Es dauert eine Ewigkeit bis du den Gegenstand hinaus bekommen hast.");
			output("Da du so lange gebraucht hast verlierst du f�r heute einen Waldkampf");
			output("Doch jetzt liegt er endlich in deiner Hand. Du schaust dir den kleinen Gegenstand an und f�hlst dich gest�rkt.");
			$session[user][turns]-=1;
			$session[user][defence]+=3;
			$session[user][specialinc]="";
			addnav("Zur�ck in den Wald","forest.php");
		break;
		case 5:
		case 6:
			output("Du ziehst deinen Arm samt Gegenstand aus dem Loch und schaust ihn dir an, es ist ein kleines Steinchen.");
			output("Pl�tzlich durchf�hrt dich ein stechender Schmerz von deiner Hand bis in den Nacken. Du l�sst das kleine Steinchen wieder fallen. Als du wieder klar denken kannst f�hlst du dich geschw�cht.");
			$session[user][attack]-=3;
			$session[user][specialinc]="";
			addnav("Zur�ck in den Wald","forest.php");
		break;
		case 7:
		case 8:
			output("Du ziehst deinen Arm samt Gegenstand aus dem Loch und schaust ihn dir an, es ist ein kleines Steinchen.");
			output("Pl�tzlich durchf�hrt dich ein stechender Schmerz von deiner Hand bis in den Nacken. Du l�sst das kleine Steinchen wieder fallen. Als du wieder klar denken kannst f�hlst du dich geschw�cht.");
			$session[user][defence]-=3;
			$session[user][specialinc]="";
			addnav("Zur�ck in den Wald","forest.php");
		break;
		case 9:
		case 10:
			output("Als du dir das kleine Ding in deiner Hand anschaust und das vierbl�ttrige Kleeblatt erkennst, bekommst du aus irgendeinem Grund einen Adrenalinschub und kannst es kaum erwarten endlich wieder ein Monster zu verm�beln.`n");
			output("Du erh�lst einen zus�tzlichen Waldkampf.");
			$session[user][turns]+=1;
			$session[user][specialinc]="";
			addnav("Zur�ck in den Wald","forest.php");
		break;
		case 11:
		case 12:
			output("Du ziehst und ziehst und ziehst aber das kleine Ding in der Statue will einfach nicht raus kommen.");
			output("Du verlierst einen Waldkampf.");
			output("W�tend gehst du zur�ck in den Wald.");
			$session[user][turns]-=1;
			$session[user][specialinc]="";
			addnav("Zur�ck in den Wald","forest.php");
		break;
		case 13:
		case 14:
			output("Grade als du den Gegenstand aus der Statue rausziehen willst sp�rst du, dass du von etwas gebissen worden bist.");
			output("Du bist am Gift einer Giftigenschlange gestorben.");
			$session[user][alive]=false;
			$session[user][hitpoints]=0;
			$session[user][experience]*=0.95;
			addnav("T�gliche News","news.php");
			$session[user][specialinc]="";
			addnews($session[user][name]." starb durch eine Giftschlange");
		break;
	}
}
if($HTTP_GET_VARS[op]=="gehe")
{
	$session[user][specialinc]="";
	output("Mit schnellen Schritten verl�sst du den Ort.");
	addnav("Weiter","forest.php");
}
?>