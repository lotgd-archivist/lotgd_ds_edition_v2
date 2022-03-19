<?php

/*
Der Erdschrein. Hier erh�lt man das Mal der Erde

Ben�tigt : Erweiterung "Die Auserw�hlten"
Bye Maris (Maraxxus@gmx.de)
*/

require_once "common.php";
page_header("Eine H�hle");

$session['user']['specialinc']='earthshrine.php';
$mark=$session['user']['marks'];
if ($mark>=16) {$mark-=16;}
if ($mark>=8) {$mark-=8;}
if ($mark>=4) {$mark-=4;}
if ($mark>=2) {$mark-=2;}

if ($HTTP_GET_VARS['op']=="")
{

if ($mark<1) {
output("`&Du gehst durch den Wald und entdeckst pl�tzlich, dass das Gras neben dir niedergetreten scheint. Neugierig folgst du der Spur und gelangst immer tieder und tiefer in den Wald, an Orte, die du noch nie vorher gesehen hast. Dann stolperst du - �ber einen Knochen! Du hoffst, dass es die �berreste eines Tieres sind und gehst weiter. Dann gelangst du an eine Stelle an der auf breiter Fl�che s�mtliche B�ume umgeknickt sind, und eine gewaltige �ffnung zu einer H�hle tut sich vor dir auf. Alle deine Sinne dr�ngen dich umzukehren, doch deine Neugier lockt dich in den H�hleneingang. Auf wen willst du h�ren ?");

addnav("Was nun?");
addnav("Weiter","forest.php?op=go");
addnav("Umkehren","forest.php?op=leave");
}
else
{
output("`&Du kommst rein zuf�llig an der H�hle mit dem Erdschrein vorbei. Da du sein Mal tr�gst werden dir die Wesen darin nichts tun. Also beschlie�t du die H�hle erneut zu betreten und l�sst dich vor dem Erdschrein zu einem kurzen, stillen Gebet nieder.`n");
output("Dies gibt dir neue Kraft und du erh�lst `^2 Waldk�mpfe`&!");
$session['user']['turns']+=2;
addnav("Weiter","forest.php?op=leave");
}
}

if ($HTTP_GET_VARS['op']=="go")
{
	output("Du nimmst all deinen Mut zusammen und gehst langsam in die H�hle hinein. Eine kleine Fackel spendet dir Licht. Kaum hast du ein paar Schritte gemacht, erkennst du schon die Konturen einer gewaltigen Kreatur, die dir den Weg versperrt. Ihre Ausma�e sind einfach gigantisch. Spielend einfach l�sst das Wesen dicken Felsbrocken unter seinen Klauen zerbr�seln, und die vielen kleinen R�stungs- und Waffenteile, die den Boden s�umen lassen dich nichts Gutes erahnen. Zu deinem Gl�ck ist die Kreatur recht langsam in ihren Bewegungen, was dir jetzt noch die M�glichkeit zur Flucht erlaubt.`n");

	if($session['user']['dragonkills']>=10)
	{
		addnav("K�mpfe","forest.php?op=fight");

    $badguy = array(
	"creaturename"=>"`4Behemoth`0"
	,"creaturelevel"=>$session['user']['level']+1
	,"creatureweapon"=>"Maul und Klauen"
	,"creatureattack"=>20
	,"creaturedefense"=>40
	,"creaturehealth"=>1000
	,"diddamage"=>0);

	$session['user']['badguy']=createstring($badguy);
	$atkflux = e_rand(0,$session['user']['dragonkills']*2);
	$defflux = e_rand(0,($session['user']['dragonkills']*2-$atkflux));
	$hpflux = ($session['user']['dragonkills']*2 - ($atkflux+$defflux)) * 5;
	$badguy['creatureattack']+=$atkflux;
	$badguy['creaturedefense']+=$defflux;
	$badguy['creaturehealth']+=$hpflux;

    }
	else
	{
		output("Und daf�r bist du auch dankbar, denn du w�rdest nie den Kampf mit einer solchen Bestie �berleben. Soviel ist dir sicher!");
	}
	addnav("Fl�chte","forest.php?op=leave");
}

if ($HTTP_GET_VARS['op']=="leave")
{
$session['user']['specialinc']='';
redirect("forest.php");
}

if ($HTTP_GET_VARS['op']=="run")
{
	output("\"`%Zu sp�t, du hattest deine Chance!`0\"`n");
	$HTTP_GET_VARS['op']="fight";
}

if ($HTTP_GET_VARS['op']=="fight")
{
	$battle=true;
}

if ($battle)
{
	include ("battle.php");
	if ($victory)
	{
		output("`nDu hast `^".$badguy['creaturename']." geschlagen.");
		$badguy=array();
		$session['user']['badguy']="";
        addnav("Weiter","forest.php?op=goon");
	}

	elseif($defeat)
	{
		output("Du wurdest von der �berm�chtigen Kreatur gefressen. Wahrlich kein sch�ner Tod!");
		output("`n`4Das war es nun f�r dich.`n");
		output("Du verlierst 10% deiner Erfahrung und all Dein Gold.`n");
		$session['user']['gold']=0;
		$session['user']['experience']=round($session['user']['experience']*.9,0); $session['user']['alive']=false;
		$session['user']['hitpoints']=0;
		$session['user']['specialinc']="";
        addnews("`@".$session['user']['name']."`t wurde von einem Unget�m verspeist.");
        addnav("T�gliche News","news.php");
	}
	else
	{
		fightnav();
	}

}

if ($HTTP_GET_VARS['op']=="goon")
{
output("`&Du l�sst die Reste des Unget�ms hinter dir und folgst der H�hle weiter, neugierig darauf was die Kreatur bewacht hat. Dann entdeckst du schlie�lich am Rande der H�hle einen kleinen `^Erdschrein`&. Ein Knistern liegt in der Luft als du dich ihm n�herst. An der Wand �ber dem Schrein prangt eine Glyphe. Sie gl�ht so stark und strahlt eine derartige Hitze aus, dass du meinen k�nntest, sie sei aus fl�ssigem Metall!`n");
addnav("Was tust du ?");
addnav("Die Glyphe ber�hren","forest.php?op=earthshrine");
addnav("Die H�hle verlassen","forest.php?op=leave");
}
if ($_GET[op]=="earthshrine")
{
output ("`&Mit stark pochendem Herzen trittst du an den Schrein und dr�ckt deinen Arm gegen die Glyphe. Ein grauenvoller Schmerz durchzuckt deinen ganzen K�rper als sich die Glyphe in dein Fleisch brennt.");
$session['user'][maxhitpoints]-=5;
debuglog("Gab 5 permanente LP am Erdschrein.");
   switch(e_rand(1,2)){
      case 1 :
output ("`&Obwohl du mit aller Gewalt ziehst kannst du deinen Arm nicht von der Glyphe l�sen.`n `4 Unw�rdiger Narr! `& h�rst du es noch dumpf schallen bevor dein ganzer K�rper vergl�ht... ");
$session['user']['hitpoints']=0;
addnews("`%".$session[user][name]."`^ ist in einer H�hle zu Asche verbrannt!");
addnav("Weiter","shades.php");
      break;
      case 2 :
output ("`&Unter gewaltigen Schmerzen bleibst du dennoch standhaft und als du deinen Arm von der Glyphe l�sen kannst, stellst du ein Mal fest, dass sich tief in dein Fleisch gebrannt hat.`n");
output ("Du hast das `^Mal der Erde`& erlangt!");
$session['user']['marks']+=1;
addnews("`@".$session['user']['name']."`& hat das `^Mal der Erde`& erlangt!");
addnav("Weiter","forest.php?op=leave");
      break;

   }

}
?>
