<?php

// Geistschrein. Hier kann das Mal des Geister erworben werden.
// Benötigt : Erweiterung "Die Auserwählten"
//
// by Maris (Maraxxus@gmx.de)

require_once "common.php";
checkday();
page_header("Der Geistschrein");

$mark=$session['user']['marks'];

if ($HTTP_GET_VARS['op']=="run")
{
	output("\"`%Aus diesem Kampf kannst du nicht fliehen!`0\"`n");
	$HTTP_GET_VARS['op']="fight";
}

if ($HTTP_GET_VARS['op']=="fight1")
{
    $session['user']['soulpoints']= $session['user']['level'] * 5 + 50;
    redirect("spiritshrine.php?op=fight");
}

if ($HTTP_GET_VARS['op']=="fight")
{
	$battle=true;
}

if ($battle)
{
	$originalhitpoints = $session['user']['hitpoints'];
	$session['user']['hitpoints'] = $session['user']['soulpoints'];
	$originalattack = $session['user']['attack'];
	$originaldefense = $session['user']['defence'];
	$session['user']['attack'] = 10 + round(($session['user']['level'] - 1) * 1.5);
	$session['user']['defence'] = 10 + round(($session['user']['level'] - 1) * 1.5);
	include("battle.php");
	
	$session['user']['attack'] = $originalattack;
	$session['user']['defence'] = $originaldefense;
	$session['user']['soulpoints'] = $session['user']['hitpoints'];
	$session['user']['hitpoints'] = $originalhitpoints;

	if ($victory)
	{
		output("`nDu hast `^".$badguy['creaturename']." bezwungen.");
		$badguy=array();
		$session['user']['badguy']="";
        addnav("Weiter zum Schrein","spiritshrine.php?op=goon");
	}

	elseif($defeat)
	{
		output("Du wurdest vom untoten Wächter besiegt und wirst für den Rest dieses Nachlebens sein Sklave sein!");
		output("`nDu verlierst alle verbleibenden Grabkämpfe!`n");
		$session['user']['gravefights']=0;
        addnews("`@".$session['user']['name']."`t wurde von einem untoten Wächter versklavt.");
        addnav("Toll...","graveyard.php");
	}
	else
	{
			addnav("Kämpfen","spiritshrine.php?op=fight");
			if (getsetting("autofight",0)){
			addnav("AutoFight");
			addnav("5 Runden quälen","spiritshrine.php?op=fight&auto=five");
			addnav("Bis zum bitteren Ende","spiritshrine.php?op=fight&auto=full");}
	}

}

if ($HTTP_GET_VARS['op']=="goon")
{
output("`&Du passiersr den Eingang zum Schrein. Ein Knistern liegt in der Luft als du dich ihm näherst. An der Wand über dem Schrein hängt ein Spiegel. Auf dem Rand steht geschrieben, dass ein Blick in diesen Spiegel sowohl Segen wie auch verderbende Hässlichkeit für deinen toten Körper bedeuten kann!`n");
addnav("Was tust du ?");
addnav("In den Spiegel schauen","spiritshrine.php?op=shrine");
addnav("Den Schrein verlassen","graveyard.php");
}
if ($_GET[op]=="shrine")
{
output ("`&Mit stark pochendem Herzen trittst du an den Schrein und schaust in den Spiegel. Ein stechender Schmerz durchzuckt dich als du im Spiegel deinen toten Körper erblickst.");
$session['user']['charm']-=5;
debuglog("Gab 5 Charme am Geistschrein.");
   switch(e_rand(1,3)){
      case 1 :
      case 2 :
output ("`&Obwohl du mit aller Gewalt deinen Blick abwenden willst gelingt es dir nicht.`n `4 Unwürdiger Narr! `& hörst du es dumpf ertönen und du siehst wie dein Körper im Spiegel unansehbar hässlich wird... ");
addnews("`%".$session[user][name]."`^ ist nun nicht mehr so hübsch!");
addnav("Weiter","graveyard.php");
      break;
      case 3 :
output ("`&Du hälst deinen Blick fest auf dein Spiegelbild gerichtet und plötzlich stellst du ein Mal fest, dass dein Spiegelbild auf dem Arm trägt.`n");
output ("Du hast das `^Mal des Geistes`& erlangt!");
$session['user']['marks']+=16;
addnews("`@".$session['user']['name']."`& hat das `^Mal des Geistes`& erlangt!");
addnav("Weiter","graveyard.php");
      break;

   }
}

if ($_GET[op]==""){
    if (!$session[user][alive]){
if ($mark<16) {
		output("`9Du bemerkst einen kleinen, seltsamen Schrein irgendwo fernab des Friedhofs. Dieser kleine Schrein erscheint so unwirklich, dass du ihn erst nicht wahrnimmst. Doch dann gehst du etwas näher heran und stellst zu deinem Schrecken fest, dass der Zugang zu diesem Schrein von einem untoten Wächter geschützt wird. Du wirst ihn wohl herausfordern müssen, wenn du den Schrein betreten willst.`nRamius wird dir, sofern du kämpfst, neue Kraft schenken!");
		
	$badguy = array(
	"creaturename"=>"`&Untoter Wächter`0"
	,"creaturelevel"=>$session['user']['level']+e_rand(0,3)
	,"creatureweapon"=>"Geisterschwert"
	,"creatureattack"=>30
	,"creaturedefense"=>20
	,"creaturehealth"=>$session['user']['level']*10
	,"diddamage"=>0);

    $level = $session['user']['level'];
	$shift = 0;
	if ($level < 5) $shift = -1;
	$badguy['creatureattack'] = 10 + $shift + (int)(($level-1) * 1.5);
    $badguy['creaturedefense'] = (int)((10 + $shift + (($level-1) * 1.5)) * .7);
    $badguy['creaturehealth'] = $level * 13;
	$badguy['creaturelevel'] = $level+1;

	$session['user']['badguy']=createstring($badguy);
	$atkflux = e_rand(0,$session['user']['dragonkills']*2);
	$defflux = e_rand(0,($session['user']['dragonkills']*2-$atkflux));
	$hpflux = ($session['user']['dragonkills']*2 - ($atkflux+$defflux)) * 5;
	$badguy['creatureattack']+=$atkflux;
	$badguy['creaturedefense']+=$defflux;
	$badguy['creaturehealth']+=$hpflux;
	
		addnav("Kämpfen","spiritshrine.php?op=fight1");
		addnav("Zurück zum Friedhof","graveyard.php");
}
else
{
output ("`9Du bemerkst wieder den kleinen Geistschrein fernab des Friedhofs. Da du das Mal des Geistes trägst passierst du den Wächter und verbringst einige Zeit vor dem Schrein.`nDer Schrein erfüllt deine Seele mit neuer Kraft und du bekommst `^2 Grabkämpfe`9.");
$session['user']['gravefights']+=2;
addnav("Zurück zum Friedhof","graveyard.php");
}
	}else{
		redirect("village.php");
	}
}

page_footer();
?>
