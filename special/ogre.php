<?php

// Verwertungsstelle f�r �berfl�ssige Troph�en
// by Maris (Maraxxus@gmx.de)

if (!isset($session)) exit();
$session[user][specialinc]="ogre.php";

if ($_GET[op]=="leave"){

output("`tSchnellen Schrittes und mit pochendem Herzen entfernst du dich von diesem Ort und nimmst dir fest vor so schnell nicht wieder zu kommen!");
$session[user][specialinc]="";
addnav("Weiter","forest.php");
} else

if ($_GET[op]=="leave2"){

output("`tEinigerma�en zufrieden kehrst du dem Ort den R�cken zu und entfernst dich.`n");
$session[user][specialinc]="";
addnav("Weiter","forest.php");
}

else
if ($_GET[op]=="die"){
 output("`tDu trittst vor das Unget�m und w�hlst demonstrativ in deinem Rucksack.`nAls du jedoch nichts findest, was ihm schmecken k�nnte und dir schon der Schwei� auf der Stirn steht, beschliesst du langsam dich von diesem Ort zu entfernen.`n`4Doch da hat dir das Monster bereits den Kopf abgebissen.`nDU BIST TOT!`nDu kannst morgen weiterspielen.`&`n");
   $session['user']['specialinc']="";
   $session['user']['hitpoints']=0;
   addnews("`#".$session['user']['name']." `t wurde heute zu einem Snack f�r ein Unget�m.");
   addnav("Weiter","shades.php");
}

else
if ($_GET[op]=="give"){

if (item_count(' owner='.$session['user']['acctid'].' AND tpl_id="trph" '))
{
redirect("forest.php?op=give2");
}
else
   {
   redirect("forest.php?op=die");
   }
}

else
if ($_GET[op]=="fight"){
  output("`tWie ein junger Gott wirbelst du um die Kreatur herum, einen Schlag nach dem Anderen austeilend`n");
  $chance=e_rand(1,5);
  if ($chance==2)
  {
    output("`tund nach einem langen und z�hen Kampf bricht das Monstrum �chzend zusammen.`nDie Schatztruhe ist dein!`n");
    addnav("weiter","forest.php?op=reward");
  }
else
  {
    output("`tund wie ein alter Narr stehst du da, als dich das Monstrum mit einem Biss in St�cke rei�t
    und mit Haut und Haaren verschluckt.`n`4DU BIST TOT!`nDu kannst morgen weiterspielen.`&`n");
    $session['user']['specialinc']="";
    $session['user']['hitpoints']=0;
    addnews("`#".$session['user']['name']." `t wurde heute zu einem Snack f�r ein Unget�m.");
    addnav("weiter","shades.php");
  }
}

else
if ($_GET[op]=="reward"){
output("`tDu �ffnest die Truhe und freust dich schon �ber die Sch�tze, die dich erwarten werden.`n`n`&");
$what=e_rand(1,4);
switch ($what)
{
  case 1 :
  $gold=5000+e_rand(1,5000);
  $gems=1+e_rand(1,4);
  output("`tDie Truhe ist randvoll mit Reicht�mern!`nDu findest `^{$gold} Goldm�nzen und {$gems} Edelsteine!`t`n");
  $session['user']['gold']+=$gold;
  $session['user']['gems']+=$gems;
  break;
  case 2 :
  $amount=e_rand(3,7);
  output("`tIn der Truhe lagert ein Satz Truhenfallengift mit dazu geh�rigem Antiserum!`n`^Du erh�lst jeweils {$amount} St�ck!`t`n");
  for ($i=0;$i<$amount;$i++){
    
  item_add($session['user']['acctid'],'gftph');
  
  item_add($session['user']['acctid'],'antiserum');
  
  }
  break;
  case 3 :
  output("`tIn der Truhe findest du ein `^Kraftelixier`t.`nDu hast heute `^20 Waldk�mpfe und 5 Spielerk�mpfe`t mehr!`n");
  $session['user']['turns']+=20;
  $session['user']['playerfights']+=5;
  break;
  case 4 :
  output("`tIn der Truhe war eine Fee gefangen, die dir zum Dank f�r ihre Rettung `^10 Charmepunkte, sowie 5 permanente Lebenspunkte`t gibt!`n`n");
  $session['user']['charm']+=10;
  $session['user']['maxhitpoints']+=5;
  break;
}
  addnav("weiter","forest.php?op=leave2");
}

else
if ($_GET[op]=="give2"){

$result = item_list_get(' owner='.$session['user']['acctid'].' AND tpl_id="trph" ','',false);

$amount=(db_num_rows($result));

output("`tDie Kreatur rei�t ihr riesiges Maul auf, und Sabberf�den tropfen an seinen langen Z�hnen herab. Du solltest schleunigst etwas Essbares auftreiben!`nWas will die ihm aus deinem Rucksack vorwerfen?`&`n");
    $index++;
for ($j=1;$j<=$amount;$j++) {
    $partsname=db_fetch_assoc($result);
    $choice=rawurlencode($partsname[name]);
    $value=$partsname[value1];
    $itemid=$partsname[id];
    output("`n<a href='forest.php?op=give3&choice=$choice&value=$value&itemid=$itemid'>$partsname[name]</a>",true);
    addnav("","forest.php?op=give3&choice=$choice&value=$value&itemid=$itemid");
  }
addnav("Nichts!","forest.php?op=die");
}

else
if ($_GET[op]=="give3")
{

$value=$_GET['value'];
$itemid=$_GET['itemid'];
$choice=rawurldecode($_GET['choice']);

item_delete(' id='.$itemid);

output("`t`nDie seltsame Kreatur schnuppert und mach sich �ber {$choice} `ther.`n");
$base=30+$value;
$chance=e_rand(1,100);

if ($chance<=$base)
{
output("`tDas hat die Kreatur gebraucht! Leise schmatzend schl�ft das Monstrum ein und �berl�sst dir die Truhe, die es eigentlich bewacht.`n`&");
addnav("weiter","forest.php?op=reward");
}
else
{
output("`tLeider war {$choice} f�r die Bestie nur etwas f�r den hohlen Zahn und bevor du dich versiehst hat dir das Monstrum schon den Arm ausgerissen und kaut gen�sslich darauf herum. Doch du bekommst von all dem nicht mehr viel mit, da sich vor deinem geistigen Auge schon die Pforten zu Ramius' Reich auftun.`n`4DU BIST TOT!`nDu kannst morgen wieder spielen.`&`n");
$session['user']['specialinc']="";
$session['user']['hitpoints']=0;
addnews("`#".$session['user']['name']." `t wurde heute zu einem Snack f�r ein Unget�m.");
addnav("Weiter","shades.php");
}
}

else
{
output("`tDu schlenderst durch den Wald, als du pl�tzlich ein lautes Schnauben abseits des Weges vernimmst. Entgegen jeglicher Warnung siehst du nat�rlich sofort nach, was da los ist, willst du doch deinen Freunden in der Schenke ein weiteres Mal eine breit ausgeschm�ckte Heldengeschichte erz�hlen k�nnen.`nAls du die Str�ucher auf Seite schl�gst, erkennt du ein riesiges Unget�m, welches mit mehreren schweren Ketten, welche in den Boden ragen, gefesselt ist. Sie erm�glichen es ihm gerade so sich kaum einen Meter weit von der Stelle zu bewegen. Ringsherum liegen die Knochen von kleinen und gro�en (du hoffst) Tieren, auch das Gras ist vollst�ndig abgefressen.`nDu sch�tzt die Bestie muss furchtbaren Hunger haben.`nWas dich jedoch an diesem Ort h�lt ist eine gro�e funkelnde Schatztruhe, auf der die Kreatur zu liegen scheint.`nAls unter deinen F��en ein �stlein bricht schaut dich das Wesen an und schnuppert.`n");
addnav("Was tust du?");
addnav("Angreifen","forest.php?op=fight");
addnav("Etwas zu Fressen anbieten","forest.php?op=give");
addnav("Weglaufen","forest.php?op=leave");
}

?>
