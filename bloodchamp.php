<?php

/*
Kampf gegen den Champion des Blutgottes
Ein Sieg erh�lt den Pakt, eine Niederlage kostet permanente LP

Ben�tigt : Erweiterung "Die Auserw�hlten"
By Maris (Maraxxus@gmx.de)
*/

require_once "common.php";
page_header("Die Blutarena");

if ($HTTP_GET_VARS['op']=="")
{
output("`&Du nimmst all deinen Mut zusammen und betrittst die schmale �ffnung in der Wand, bereit die Herausforderung anzunehmen. Alls sich hinter dir die �ffnung pl�tzlich wieder schlie�t stellst du fest, dass du dich in einer Art Arena befindest. Alles in diesem Raum ist in dunkles Rot gehalten.`nIm Zentrum des Kampfplatzes wartet bereits dein Gegner. Er �berragt dich um mehrere K�pfe, tr�gt eine schwarze, mit Stacheln versehene R�stung und ein langes, gezacktes Schwert. Langsam kommt er auf dich zu und es gibt keine Chance f�r dich dem Kampf zu entfliehen.");

if ($session['user']['level']<2)  { $start=0; $span=1;} else
if ($session['user']['level']<4)  { $start=0; $span=2;} else
if ($session['user']['level']<6)  { $start=1; $span=2;} else
if ($session['user']['level']<9)  { $start=2; $span=3;} else
if ($session['user']['level']<12) { $start=2; $span=4;} else
{ $start=3; $span=5;}

if ($HTTP_GET_VARS['test']==1) { $oppweapon="Gummischwert"; } else {$oppweapon="Blutschwert"; }
    $badguy = array(
	"creaturename"=>"`4Champion des Blutgottes`0"
	,"creaturelevel"=>$session['user']['level']+e_rand($start,$span)
	,"creatureweapon"=>$oppweapon
	,"creatureattack"=>$session['user']['attack']
	,"creaturedefense"=>$session['user']['defence']
	,"creaturehealth"=>((int)$session['user']['maxhitpoints']/100)*100+50
	,"diddamage"=>0);

	$session['user']['badguy']=createstring($badguy);
	$atkflux = e_rand(0,$session['user']['dragonkills']*2);
	$defflux = e_rand(0,($session['user']['dragonkills']*2-$atkflux));
	$hpflux = ($session['user']['dragonkills']*2 - ($atkflux+$defflux)) * 5;
	$badguy['creatureattack']+=$atkflux;
	$badguy['creaturedefense']+=$defflux;
	$badguy['creaturehealth']+=$hpflux;

addnav("Angriff!","bloodchamp.php?op=fight");

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
		output("`n`&Du hast den `^".$badguy['creaturename']." `&geschlagen.`n");
        if ($badguy['creatureweapon']=="Blutschwert") {
       	output("In Anerkennung dieser gro�artigen Leistung erh�lt der Blutgott seinen Pakt mit dir!`n");
		output("`&Ein Durchgang �ffnet sich und erlaubt es dir die Arena zu verlassen.");
        addnews("`@".$session['user']['name']."`^ hat den Champion des Blutgottes besiegt und sich als w�rdig erwiesen.");
        $session['user']['marks']=32; } else
        {  output ("`&`n`nDer Champion dankt dir f�r diesen Kampf.`n");
       	$session['user']['hitpoints']=$session['user']['maxhitpoints']; }
       	$badguy=array();
		$session['user']['badguy']="";
        addnav("Weiter","thepath.php?op=bg");
	}

	elseif($defeat)
	{
        
		output("`&Du wurdest vom Champion des Blutgottes geschlagen!");
        if ($badguy['creatureweapon']=="Blutschwert") {
        output("`n`n`4Als du am Boden liegst st�sst dieser sein Schwert in deinen K�rper, welches gierig dein Blut aufsaugt und dir einen Teil deiner Lebenskraft permanent raubt!`n");
		output("F�r deinen Mut diesen Kampf zu wagen erh�lt der Blutgott den Pakt mit dir.`n");
		output("`&Ein Durchgang �ffnet sich und erlaubt es dir die Arena zu verlassen.");
		addnews("`@".$session['user']['name']."`t wurde vom Champion des Blutgottes besiegt und hat die Niederlage teuer mit Blut bezahlt.");
		$session['user']['maxhitpoints']*=0.85;
		$session['user']['marks']=32; } else
		{  output ("`&`n`nDer Champion hilft dir auf dankt dir f�r diesen Kampf.`n"); }
		$session['user']['hitpoints']=$session['user']['maxhitpoints'];
		$badguy=array();
		$session['user']['badguy']="";
        addnav("Weiter","thepath.php?op=bg");
	}
	else
	{
		fightnav();
	}

}

page_footer();
?>
