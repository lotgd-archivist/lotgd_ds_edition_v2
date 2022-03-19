<?php

/*
Der Erdschrein. Hier erhält man das Mal der Erde

Benötigt : Erweiterung "Die Auserwählten"
Bye Maris (Maraxxus@gmx.de)
*/

require_once "common.php";
page_header("Eine Höhle");

$session['user']['specialinc']='earthshrine.php';
$mark=$session['user']['marks'];
if ($mark>=16) {$mark-=16;}
if ($mark>=8) {$mark-=8;}
if ($mark>=4) {$mark-=4;}
if ($mark>=2) {$mark-=2;}

if ($HTTP_GET_VARS['op']=="")
{

if ($mark<1) {
output("`&Du gehst durch den Wald und entdeckst plötzlich, dass das Gras neben dir niedergetreten scheint. Neugierig folgst du der Spur und gelangst immer tieder und tiefer in den Wald, an Orte, die du noch nie vorher gesehen hast. Dann stolperst du - über einen Knochen! Du hoffst, dass es die Überreste eines Tieres sind und gehst weiter. Dann gelangst du an eine Stelle an der auf breiter Fläche sämtliche Bäume umgeknickt sind, und eine gewaltige Öffnung zu einer Höhle tut sich vor dir auf. Alle deine Sinne drängen dich umzukehren, doch deine Neugier lockt dich in den Höhleneingang. Auf wen willst du hören ?");

addnav("Was nun?");
addnav("Weiter","forest.php?op=go");
addnav("Umkehren","forest.php?op=leave");
}
else
{
output("`&Du kommst rein zufällig an der Höhle mit dem Erdschrein vorbei. Da du sein Mal trägst werden dir die Wesen darin nichts tun. Also beschließt du die Höhle erneut zu betreten und lässt dich vor dem Erdschrein zu einem kurzen, stillen Gebet nieder.`n");
output("Dies gibt dir neue Kraft und du erhälst `^2 Waldkämpfe`&!");
$session['user']['turns']+=2;
addnav("Weiter","forest.php?op=leave");
}
}

if ($HTTP_GET_VARS['op']=="go")
{
	output("Du nimmst all deinen Mut zusammen und gehst langsam in die Höhle hinein. Eine kleine Fackel spendet dir Licht. Kaum hast du ein paar Schritte gemacht, erkennst du schon die Konturen einer gewaltigen Kreatur, die dir den Weg versperrt. Ihre Ausmaße sind einfach gigantisch. Spielend einfach lässt das Wesen dicken Felsbrocken unter seinen Klauen zerbröseln, und die vielen kleinen Rüstungs- und Waffenteile, die den Boden säumen lassen dich nichts Gutes erahnen. Zu deinem Glück ist die Kreatur recht langsam in ihren Bewegungen, was dir jetzt noch die Möglichkeit zur Flucht erlaubt.`n");

	if($session['user']['dragonkills']>=10)
	{
		addnav("Kämpfe","forest.php?op=fight");

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
		output("Und dafür bist du auch dankbar, denn du würdest nie den Kampf mit einer solchen Bestie überleben. Soviel ist dir sicher!");
	}
	addnav("Flüchte","forest.php?op=leave");
}

if ($HTTP_GET_VARS['op']=="leave")
{
$session['user']['specialinc']='';
redirect("forest.php");
}

if ($HTTP_GET_VARS['op']=="run")
{
	output("\"`%Zu spät, du hattest deine Chance!`0\"`n");
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
		output("Du wurdest von der übermächtigen Kreatur gefressen. Wahrlich kein schöner Tod!");
		output("`n`4Das war es nun für dich.`n");
		output("Du verlierst 10% deiner Erfahrung und all Dein Gold.`n");
		$session['user']['gold']=0;
		$session['user']['experience']=round($session['user']['experience']*.9,0); $session['user']['alive']=false;
		$session['user']['hitpoints']=0;
		$session['user']['specialinc']="";
        addnews("`@".$session['user']['name']."`t wurde von einem Ungetüm verspeist.");
        addnav("Tägliche News","news.php");
	}
	else
	{
		fightnav();
	}

}

if ($HTTP_GET_VARS['op']=="goon")
{
output("`&Du lässt die Reste des Ungetüms hinter dir und folgst der Höhle weiter, neugierig darauf was die Kreatur bewacht hat. Dann entdeckst du schließlich am Rande der Höhle einen kleinen `^Erdschrein`&. Ein Knistern liegt in der Luft als du dich ihm näherst. An der Wand über dem Schrein prangt eine Glyphe. Sie glüht so stark und strahlt eine derartige Hitze aus, dass du meinen könntest, sie sei aus flüssigem Metall!`n");
addnav("Was tust du ?");
addnav("Die Glyphe berühren","forest.php?op=earthshrine");
addnav("Die Höhle verlassen","forest.php?op=leave");
}
if ($_GET[op]=="earthshrine")
{
output ("`&Mit stark pochendem Herzen trittst du an den Schrein und drückt deinen Arm gegen die Glyphe. Ein grauenvoller Schmerz durchzuckt deinen ganzen Körper als sich die Glyphe in dein Fleisch brennt.");
$session['user'][maxhitpoints]-=5;
debuglog("Gab 5 permanente LP am Erdschrein.");
   switch(e_rand(1,2)){
      case 1 :
output ("`&Obwohl du mit aller Gewalt ziehst kannst du deinen Arm nicht von der Glyphe lösen.`n `4 Unwürdiger Narr! `& hörst du es noch dumpf schallen bevor dein ganzer Körper verglüht... ");
$session['user']['hitpoints']=0;
addnews("`%".$session[user][name]."`^ ist in einer Höhle zu Asche verbrannt!");
addnav("Weiter","shades.php");
      break;
      case 2 :
output ("`&Unter gewaltigen Schmerzen bleibst du dennoch standhaft und als du deinen Arm von der Glyphe lösen kannst, stellst du ein Mal fest, dass sich tief in dein Fleisch gebrannt hat.`n");
output ("Du hast das `^Mal der Erde`& erlangt!");
$session['user']['marks']+=1;
addnews("`@".$session['user']['name']."`& hat das `^Mal der Erde`& erlangt!");
addnav("Weiter","forest.php?op=leave");
      break;

   }

}
?>
