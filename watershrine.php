<?php

// Wasserschrein. Hier kann das Mal des Wassers erworben werden.
// Ben�tigt : Erweiterung "Die Auserw�hlten"
//
// by Maris (Maraxxus@gmx.de)

require_once "common.php";
checkday();
page_header("Beim Angeln...");

$mark=$session['user']['marks'];
if ($mark>=16) {$mark-=16;}

if ($HTTP_GET_VARS['op']=="") {
  if ($mark<8) {
output ("`&Du merkst wie etwas an deiner Angelschnur zerrt und dich unweigerlich auf den See zu zieht. Wie sehr du dich auch dagegen wehrst, du schaffst es nicht dagegen anzukommen. Du rutschst immer weiter auf den See zu.`nWillst du die Angel loslassen und dich retten ?");
addnav("Loslassen","fish.php");
addnav("Weiter ziehen","watershrine.php?op=zieh1");
} else
{
output ("Erneut versp�rst du diesen starken Ruck an deiner Angel und vertrauensvoll st�rzt du dich in die Tiefe. Mit dem Mal des Wassers bist du hier gesch�tzt und gelangst unbehelligt zum Wasserschrein. Dort verbringst du einige Zeit und sammelst neue Kraft.`nDu erh�lst `^2 Waldk�mpfe`&!");
$session['user']['turns']+=2;
addnav("Zur�ck","fish.php");
}
}

if ($HTTP_GET_VARS['op']=="zieh1") {
output ("`&Du h�lst die Angelrute fest in beiden H�nden und kommst dem Wasser immer n�her. Nach einem kr�ftigen Ruck landest du schlie�lich im Wasser und wirst in die Tiefe gezogen.`nDu schaffst es nicht noch einmal kr�ftig Luft zu holen und verlierst Lebenspunkte. Du rast der schwarzen Tiefe des Sees entgegen. Noch k�nntest du loslassen und dich retten.");
$chance=e_rand(4,9);
$chance=$chance*0.1;
(int)$session['user']['hitpoints']*=$chance;
addnav ("Loslassen","fish.php");
addnav ("Festhalten","watershrine.php?op=shrine");
}
if ($HTTP_GET_VARS['op']=="shrine") {
output ("`&Schlie�lich l�sst das Ziehen nach und du erkennst, dass du in einer H�hle auf dem Grund des Sees bist. Zum Gl�ck kannst du hier atmen");
output ("`&Doch bevor du dich deiner Rettung freuen kannst, erblickst du schon einen gro�en, belebten Wirbel aus Wasser, der sich dir bedrohlich n�hert.");

$badguy = array(
	"creaturename"=>"`&Wasserelementar`0"
	,"creaturelevel"=>$session['user']['level']+e_rand(1,3)
	,"creatureweapon"=>"Frisches, k�hles Wasser"
	,"creatureattack"=>$session['user']['attack']*0.75
	,"creaturedefense"=>$session['user']['defence']*0.8
	,"creaturehealth"=>500+$session['user']['dragonkills']*10
	,"diddamage"=>0);

	$session['user']['badguy']=createstring($badguy);
	$atkflux = e_rand(0,$session['user']['dragonkills']*2);
	$defflux = e_rand(0,($session['user']['dragonkills']*2-$atkflux));
	$hpflux = ($session['user']['dragonkills']*2 - ($atkflux+$defflux)) * 5;
	$badguy['creatureattack']+=$atkflux;
	$badguy['creaturedefense']+=$defflux;
	$badguy['creaturehealth']+=$hpflux;
addnav("K�mpfen","watershrine.php?op=fight");
}

if ($HTTP_GET_VARS['op']=="run")
{
	output("\"`%Keine Chance zu fliehen!`0\"`n");
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
        addnav("Weiter","watershrine.php?op=goon");
	}

	elseif($defeat)
	{
		output("Du wurdest vom Wasserelementar weggesp�lt!");
		output("`n`4Du bist tot.`n");
		output("Du verlierst 10% deiner Erfahrung und all Dein Gold.`n");
		$session['user']['gold']=0;
		$session['user']['experience']=round($session['user']['experience']*.9,0); $session['user']['alive']=false;
		$session['user']['hitpoints']=0;
		addnews("`@".$session['user']['name']."`t wurde von einem Wasserelementar weggesp�lt.");
        addnav("T�gliche News","news.php");
	}
	else
	{
		fightnav();
	}

}

if ($HTTP_GET_VARS['op']=="goon")
{
output("`&Du bezwingst das seltsame Wesen und gehst tiefer in die H�hle hinein. Dann entdeckst du schlie�lich am Rande der H�hle einen kleinen `^Wasserschrein`&. Ein Knistern liegt in der Luft als du dich ihm n�herst. An der Wand �ber dem Schrein prangt eine Glyphe. Sie gl�ht so stark und strahlt eine derartige Hitze aus, dass du meinen k�nntest, sie sei aus fl�ssigem Metall!`n");
addnav("Was tust du ?");
addnav("Die Glyphe ber�hren","watershrine.php?op=watershrine");
addnav("Die H�hle verlassen","fish.php");
}
if ($_GET[op]=="watershrine")
{
output ("`&Mit stark pochendem Herzen trittst du an den Schrein und dr�ckt deinen Arm gegen die Glyphe. Ein grauenvoller Schmerz durchzuckt deinen ganzen K�rper als sich die Glyphe in dein Fleisch brennt.");
$session['user'][maxhitpoints]-=5;
debuglog("Gab 5 permanente LP am Wasserschrein.");
   switch(e_rand(1,3)){
      case 1 :
      case 2 :
output ("`&Obwohl du mit aller Gewalt ziehst kannst du deinen Arm nicht von der Glyphe l�sen.`n `4 Unw�rdiger Narr! `& h�rst du es noch dumpf schallen bevor dein ganzer K�rper vergl�ht... ");
$session['user']['hitpoints']=0;
addnews("`%".$session[user][name]."`^ ist in einer H�hle zu Asche verbrannt!");
addnav("Weiter","shades.php");
      break;
      case 3 :
output ("`&Unter gewaltigen Schmerzen bleibst du dennoch standhaft und als du deinen Arm von der Glyphe l�sen kannst, stellst du ein Mal fest, dass sich tief in dein Fleisch gebrannt hat.`n");
output ("Du hast das `^Mal des Wassers`& erlangt!");
$session['user']['marks']+=8;
addnews("`@".$session['user']['name']."`& hat das `^Mal des Wassers`& erlangt!");
addnav("Weiter","fish.php");
      break;

   }
}



page_footer();
?>
