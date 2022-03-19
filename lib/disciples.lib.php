<?php
// Knappen und co
// by Maris (Maraxxus@gmx.de)

function set_disciple($state) {
global $session;

$sql = "SELECT name,state FROM disciples WHERE master=".$session['user']['acctid']."";
$result = db_query($sql) or die(db_error(LINK));

$row = db_fetch_assoc($result);
$name=$row['name'];
$state=$row['state'];

switch ($state) {
  case 0 :
  $decbuff = array();
  break;
  
  case 1 :
  $decbuff = array(
                    "startmsg"=>"`n`^Dein Knappe steht dir zur Seite.`&`n`n",
                    "name"=>"`%Knappe $name`& ",
                    "regen"=>2,
                    "rounds"=>75,
                    "defmod"=>1.05,
                    "atkmod"=>1.05,
                    "roundmsg"=>"Dein Knappe versorgt deine Wunden.",
                    "wearoff"=>"Dein Knappe sackt erschöpft in sich zusammen.",
                    "activate"=>"roundstart");
  break;
  case 2 :
  $decbuff = array(
                    "startmsg"=>"`n`^Dein Knappe steht dir zur Seite.`&`n`n",
                    "name"=>"`%Knappe $name `&",
                    "badguyatkmod"=>0.85,
                    "rounds"=>150,
                    "defmod"=>1.05,
                    "atkmod"=>1.05,
                    "roundmsg"=>"Dein Knappe drängt den Gegner mit dem Schild ab.",
                    "wearoff"=>"Dein Knappe sackt erschöpft in sich zusammen.",
                    "activate"=>"roundstart");
  break;
  case 3 :
  $decbuff = array(
                    "startmsg"=>"`n`^Dein Knappe steht dir zur Seite.`&`n`n",
                    "name"=>"`%Knappe $name `&",
                    "rounds"=>170,
                    "defmod"=>1.10,
                    "atkmod"=>1.05,
                    "roundmsg"=>"Dein Knappe hält dir den Rücken frei.",
                    "wearoff"=>"Dein Knappe sackt erschöpft in sich zusammen.",
                    "activate"=>"roundstart");
  break;
  case 4 :
  $decbuff = array(
                    "startmsg"=>"`n`^Dein Knappe steht dir zur Seite.`&`n`n",
                    "name"=>"`%Knappe $name `&",
                    "rounds"=>300,
                    "defmod"=>1.05,
                    "atkmod"=>1.05,
                    "roundmsg"=>"Dein Knappe kämpft tapfer.",
                    "wearoff"=>"Dein Knappe sackt erschöpft in sich zusammen.",
                    "activate"=>"roundstart");
  break;
  case 5 :
  $decbuff = array(
                    "startmsg"=>"`n`^Dein Knappe steht dir zur Seite.`&`n`n",
                    "name"=>"`%Knappe $name `&",
                    "rounds"=>120,
                    "defmod"=>1.05,
                    "atkmod"=>1.05,
                    "badguydmgmod"=>0.9,
                    "roundmsg"=>"Dein Knappe lenkt den Gegner ab.",
                    "wearoff"=>"Dein Knappe sackt erschöpft in sich zusammen.",
                    "activate"=>"roundstart");
  break;
  case 6 :
  $decbuff = array(
                    "startmsg"=>"`n`^Dein Knappe steht dir zur Seite.`&`n`n",
                    "name"=>"`%Knappe $name `&",
                    "rounds"=>170,
                    "defmod"=>1.05,
                    "atkmod"=>1.10,
                    "roundmsg"=>"Dein Knappe stürzt sich tapfer in die Schlacht.",
                    "wearoff"=>"Dein Knappe sackt erschöpft in sich zusammen.",
                    "activate"=>"roundstart");
  break;
  case 7 :
  $decbuff = array(
                    "startmsg"=>"`n`^Dein Knappe steht dir zur Seite.`&`n`n",
                    "name"=>"`%Knappe $name `&",
                    "rounds"=>100,
                    "defmod"=>1.05,
                    "atkmod"=>1.05,
                    "badguydefmod"=>0.7,
                    "roundmsg"=>"Dein Knappe verspottet deinen Gegner und macht ihn unaufmerksam.",
                    "wearoff"=>"Dein Knappe sackt erschöpft in sich zusammen.",
                    "activate"=>"roundstart");
  break;
  case 8 :
  $decbuff = array(
                    "startmsg"=>"`n`^Dein Knappe steht dir zur Seite.`&`n`n",
                    "name"=>"`%Knappe $name `&",
                    "rounds"=>120,
                    "defmod"=>1.05,
                    "atkmod"=>1.05,
                    "badguyatkmod"=>0.7,
                    "roundmsg"=>"Dein Knappe klammert sich an deinen Gegner und behindert ihn bei der Atacke.",
                    "wearoff"=>"Dein Knappe sackt erschöpft in sich zusammen.",
                    "activate"=>"roundstart");
  break;
  case 9 :
  $decbuff = array(
                    "startmsg"=>"`n`^Dein Knappe steht dir wortreich zur Seite.`&`n`n",
                    "name"=>"`%Knappe $name `&",
                    "rounds"=>120,
                    "defmod"=>1.05,
                    "atkmod"=>1.05,
                    "badguyatkmod"=>0.9,
                    "badguydefmod"=>0.9,
                    "roundmsg"=>"Dein Knappe überfordert deinen Gegner mit klugen Sprüchen.",
                    "wearoff"=>"Dein Knappe sackt erschöpft in sich zusammen.",
                    "activate"=>"roundstart");
  break;
  case 10 :
  $decbuff = array(
                    "startmsg"=>"`n`^Dein Knappe bewirft deinen Gegner mit Steinen.`&`n`n",
                    "name"=>"`%Knappe $name `&",
                    "rounds"=>120,
                    "defmod"=>1.05,
                    "atkmod"=>1.05,
                    "wearoff"=>"Dein Knappe sackt erschöpft in sich zusammen.",
                   	"minioncount"=>1,
                    "minbadguydamage"=>round($session['user']['level']),
                    "maxbadguydamage"=>round($session['user']['level']*2),
					"effectmsg"=>"`&Dein Knappe trifft deinen Gegner für `4{damage}`& Schadenspunkte.",
                    "activate"=>"roundstart");
  break;
  case 11 :
  $decbuff = array(
                    "startmsg"=>"`n`^Dein Knappe ist zwar da, zeigt jedoch nicht viel Interesse an dir.`&`n`n",
                    "name"=>"`%Knappe $name `&",
                    "rounds"=>300,
                    "defmod"=>1.02,
                    "atkmod"=>1.02,
                    "roundmsg"=>"Dein Knappe steht untätig rum und bohrt in der Nase.",
                    "wearoff"=>"Dein Knappe setzt sich auf den Boden.",
                    "activate"=>"roundstart");
  break;
  case 12 :
  $decbuff = array(
                    "startmsg"=>"`n`^Dein Knappe steht dir treu zur Seite.`&`n`n",
                    "name"=>"`%Knappe $name `&",
                    "rounds"=>400,
                    "defmod"=>1.04,
                    "atkmod"=>1.04,
                    "roundmsg"=>"Dein Knappe unterstützt dich im Kampf.",
                    "wearoff"=>"Dein Knappe ist nun erschöpft.",
                    "activate"=>"roundstart");
  break;
  
  case 13 :
  $decbuff = array(
                    "startmsg"=>"`n`^Dein Knappe versteckt sich im Gebüsch und attackiert deinen Gegner mit seinem Blasrohr.`&`n`n",
                    "name"=>"`%Knappe $name `&",
                    "rounds"=>80,
                    "defmod"=>1.01,
                    "atkmod"=>1.01,
                    "wearoff"=>"Dein Knappe ist nun müde.",
                    "minioncount"=>1,
                    "minbadguydamage"=>round($session['user']['level']),
                    "maxbadguydamage"=>round($session['user']['level']*4),
                   	"effectmsg"=>"`&Dein Knappe trifft mit `4{damage}`& Schadenspunkten!",
                    "activate"=>"roundstart");
  break;
  
  case 14 :
  $decbuff = array(
                    "startmsg"=>"`n`^Dein Knappe hält sich bedeckt.`&`n`n",
                    "name"=>"`%Knappe $name `&",
                    "rounds"=>300,
                    "defmod"=>1.02,
                    "atkmod"=>1.02,
                    "roundmsg"=>"Dein Knappe ist dir kaum eine Hilfe.",
                    "wearoff"=>"Dein Knappe ist nun müde und lässt dich ganz allein.",
                    "activate"=>"roundstart");
  break;
  
  case 20 :
  $decbuff = array(
                    "startmsg"=>"`n`^Dein Knappe folgt dir willenlos.`&`n`n",
                    "name"=>"`%Knappe $name `&",
                    "rounds"=>500,
                    "minioncount"=>1,
                    "defmod"=>1.01,
                    "atkmod"=>1.01,
                    "minbadguydamage"=>round($session['user']['level']*0.5),
                    "maxbadguydamage"=>round($session['user']['level']),
                    "effectmsg"=>"`&Dein Knappe kaut deinen Gegner für `4{damage} `&Schadenspunkte an.",
                    "wearoff"=>"Dein Knappe kann nun nicht mehr und schont seine morschen Knochen.",
                    "activate"=>"roundstart");
  break;
  
 }
 return($decbuff);
}

function get_disciple_stat($state) {
switch ($state) {
 case 1 : $adj="jungen"; break;
 case 2 : $adj="dürren"; break;
 case 3 : $adj="langwüchsigen"; break;
 case 4 : $adj="kräftigen"; break;
 case 5 : $adj="hübschen"; break;
 case 6 : $adj="stolzen"; break;
 case 7 : $adj="vorlauten"; break;
 case 8 : $adj="verträumten"; break;
 case 9 : $adj="neunmalklugen"; break;
 case 10 : $adj="dicklichen"; break;
 case 11 : $adj="nichtsnutzigen"; break;
 case 12 : $adj="treuen"; break;
 case 13 : $adj="hinterhältigen"; break;
 case 14 : $adj="listigen"; break;
 case 20 : $adj="untoten"; break;
 }
return ($adj);
}

function disciple_levelup() {
global $session;

$sql = "SELECT name,state,level FROM disciples WHERE master=".$session['user']['acctid']."";
$result = db_query($sql) or die(db_error(LINK));
$rowk = db_fetch_assoc($result);
if (($rowk['level']>=30) && ($rowk['state']==20))
{ output ("`^Dein `4untoter Knappe`^ kann keinen weiteren Level aufsteigen.`0`n"); }
elseif ($rowk['level']>=45)
{ output ("`^Dein Knappe kann keinen weiteren Level aufsteigen.`0`n"); }
else
{
$level=$rowk['level'];
$rowk['level']++;
$sql = "UPDATE disciples SET level=".$rowk[level]." WHERE master=".$session[user][acctid]."";
db_query($sql);
$session['bufflist']['decbuff'] = set_disciple($rowk['state']);
$session['bufflist']['decbuff']['name'].=" ->Lvl ".$rowk['level']."`0";
$session['bufflist']['decbuff']['atkmod']+=($rowk['level']*0.005);
$session['bufflist']['decbuff']['defmod']+=($rowk['level']*0.005);
$session['bufflist']['decbuff']['rounds']+=($rowk['level']*2);
// check best one
$level=$rowk['level'];
$sql = "SELECT id,level FROM disciples WHERE best_one=1";
$result = db_query($sql) or die(db_error(LINK));
$rowb = db_fetch_assoc($result);
if ($level>$rowb['level']) {
output("`n`^".$rowk['name']." ist stärker als jeder andere Knappe im Land!`n");
$sql = "UPDATE disciples SET best_one=1 WHERE master=".$session['user']['acctid']."";
db_query($sql);
$sql = "UPDATE disciples SET best_one=0 WHERE master<>".$session['user']['acctid']."";
db_query($sql);
}
}
}
       
function disciple_remove() {
	global $session;
	
	$sql = "UPDATE disciples SET oldstate=state,state=0,best_one=0 WHERE master = ".$session['user']['acctid'];
	db_query($sql) or die(sql_error($sql));
	
	$sql = "UPDATE account_extra_info SET disciples_spoiled=disciples_spoiled+1 WHERE acctid = ".$session['user']['acctid'];
	db_query($sql) or die(sql_error($sql));
	
	unset($session['bufflist']['decbuff']);
	
	$sql = "SELECT master FROM disciples WHERE level>0 ORDER BY level DESC LIMIT 1";
	$result = db_query($sql) or die(db_error(LINK));
	$rowk = db_fetch_assoc($result);
	$sql = "UPDATE disciples SET best_one=1 WHERE master=".$rowk['master']."";
	db_query($sql);
}
?>
