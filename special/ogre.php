<?php

// Verwertungsstelle für überflüssige Trophäen
// by Maris (Maraxxus@gmx.de)

if (!isset($session)) exit();
$session[user][specialinc]="ogre.php";

if ($_GET[op]=="leave"){

output("`tSchnellen Schrittes und mit pochendem Herzen entfernst du dich von diesem Ort und nimmst dir fest vor so schnell nicht wieder zu kommen!");
$session[user][specialinc]="";
addnav("Weiter","forest.php");
} else

if ($_GET[op]=="leave2"){

output("`tEinigermaßen zufrieden kehrst du dem Ort den Rücken zu und entfernst dich.`n");
$session[user][specialinc]="";
addnav("Weiter","forest.php");
}

else
if ($_GET[op]=="die"){
 output("`tDu trittst vor das Ungetüm und wühlst demonstrativ in deinem Rucksack.`nAls du jedoch nichts findest, was ihm schmecken könnte und dir schon der Schweiß auf der Stirn steht, beschliesst du langsam dich von diesem Ort zu entfernen.`n`4Doch da hat dir das Monster bereits den Kopf abgebissen.`nDU BIST TOT!`nDu kannst morgen weiterspielen.`&`n");
   $session['user']['specialinc']="";
   $session['user']['hitpoints']=0;
   addnews("`#".$session['user']['name']." `t wurde heute zu einem Snack für ein Ungetüm.");
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
    output("`tund nach einem langen und zähen Kampf bricht das Monstrum ächzend zusammen.`nDie Schatztruhe ist dein!`n");
    addnav("weiter","forest.php?op=reward");
  }
else
  {
    output("`tund wie ein alter Narr stehst du da, als dich das Monstrum mit einem Biss in Stücke reißt
    und mit Haut und Haaren verschluckt.`n`4DU BIST TOT!`nDu kannst morgen weiterspielen.`&`n");
    $session['user']['specialinc']="";
    $session['user']['hitpoints']=0;
    addnews("`#".$session['user']['name']." `t wurde heute zu einem Snack für ein Ungetüm.");
    addnav("weiter","shades.php");
  }
}

else
if ($_GET[op]=="reward"){
output("`tDu öffnest die Truhe und freust dich schon über die Schätze, die dich erwarten werden.`n`n`&");
$what=e_rand(1,4);
switch ($what)
{
  case 1 :
  $gold=5000+e_rand(1,5000);
  $gems=1+e_rand(1,4);
  output("`tDie Truhe ist randvoll mit Reichtümern!`nDu findest `^{$gold} Goldmünzen und {$gems} Edelsteine!`t`n");
  $session['user']['gold']+=$gold;
  $session['user']['gems']+=$gems;
  break;
  case 2 :
  $amount=e_rand(3,7);
  output("`tIn der Truhe lagert ein Satz Truhenfallengift mit dazu gehörigem Antiserum!`n`^Du erhälst jeweils {$amount} Stück!`t`n");
  for ($i=0;$i<$amount;$i++){
    
  item_add($session['user']['acctid'],'gftph');
  
  item_add($session['user']['acctid'],'antiserum');
  
  }
  break;
  case 3 :
  output("`tIn der Truhe findest du ein `^Kraftelixier`t.`nDu hast heute `^20 Waldkämpfe und 5 Spielerkämpfe`t mehr!`n");
  $session['user']['turns']+=20;
  $session['user']['playerfights']+=5;
  break;
  case 4 :
  output("`tIn der Truhe war eine Fee gefangen, die dir zum Dank für ihre Rettung `^10 Charmepunkte, sowie 5 permanente Lebenspunkte`t gibt!`n`n");
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

output("`tDie Kreatur reißt ihr riesiges Maul auf, und Sabberfäden tropfen an seinen langen Zähnen herab. Du solltest schleunigst etwas Essbares auftreiben!`nWas will die ihm aus deinem Rucksack vorwerfen?`&`n");
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

output("`t`nDie seltsame Kreatur schnuppert und mach sich über {$choice} `ther.`n");
$base=30+$value;
$chance=e_rand(1,100);

if ($chance<=$base)
{
output("`tDas hat die Kreatur gebraucht! Leise schmatzend schläft das Monstrum ein und überlässt dir die Truhe, die es eigentlich bewacht.`n`&");
addnav("weiter","forest.php?op=reward");
}
else
{
output("`tLeider war {$choice} für die Bestie nur etwas für den hohlen Zahn und bevor du dich versiehst hat dir das Monstrum schon den Arm ausgerissen und kaut genüsslich darauf herum. Doch du bekommst von all dem nicht mehr viel mit, da sich vor deinem geistigen Auge schon die Pforten zu Ramius' Reich auftun.`n`4DU BIST TOT!`nDu kannst morgen wieder spielen.`&`n");
$session['user']['specialinc']="";
$session['user']['hitpoints']=0;
addnews("`#".$session['user']['name']." `t wurde heute zu einem Snack für ein Ungetüm.");
addnav("Weiter","shades.php");
}
}

else
{
output("`tDu schlenderst durch den Wald, als du plötzlich ein lautes Schnauben abseits des Weges vernimmst. Entgegen jeglicher Warnung siehst du natürlich sofort nach, was da los ist, willst du doch deinen Freunden in der Schenke ein weiteres Mal eine breit ausgeschmückte Heldengeschichte erzählen können.`nAls du die Sträucher auf Seite schlägst, erkennt du ein riesiges Ungetüm, welches mit mehreren schweren Ketten, welche in den Boden ragen, gefesselt ist. Sie ermöglichen es ihm gerade so sich kaum einen Meter weit von der Stelle zu bewegen. Ringsherum liegen die Knochen von kleinen und großen (du hoffst) Tieren, auch das Gras ist vollständig abgefressen.`nDu schätzt die Bestie muss furchtbaren Hunger haben.`nWas dich jedoch an diesem Ort hält ist eine große funkelnde Schatztruhe, auf der die Kreatur zu liegen scheint.`nAls unter deinen Füßen ein Ästlein bricht schaut dich das Wesen an und schnuppert.`n");
addnav("Was tust du?");
addnav("Angreifen","forest.php?op=fight");
addnav("Etwas zu Fressen anbieten","forest.php?op=give");
addnav("Weglaufen","forest.php?op=leave");
}

?>
