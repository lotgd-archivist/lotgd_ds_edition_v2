<?
/*Wildsau
Autor: ???
Modifycated by Hadriel
Translation by Hadriel
*/ 
//if (!isset($session)) exit(); 

if($HTTP_GET_VARS[op]=="") 
{ 
output("`2W�hrend du durch den Wald l�ufst, sp�rst du pl�tzlich einen harten Schlag am Oberschenkel.`n"); 
output("Dann einen stechenden Schmerz. Als du nach unten schaust, siehst du den Verursacher: Es ist ein `^Wildschwein`2.`n"); 
output("Mit deiner/m/n ".$session[user][weapon]." schl�gst du solange auf das Tier ein, bis es sich grunzend verzieht.`n`n"); 

$session[user][hitpoints]*=0.8; 

{ 
$session[user][specialinc] = "pig.php"; 
output("Du verbindest gerade dein Bein, da f�llt dir ein, dass du ja ein `&J�ger `2bist. Doch von dem grunzenden Ungeheuer ist weit und breit nichts mehr zu sehen."); 
output("Willst du das Schwein wirklich jagen und dabei mit Sicherheit ein Waldkampf verlieren?"); 
addnav("J?Jage es!","forest.php?op=huntit"); 
addnav("L?Lass es bleiben","forest.php?op=return"); 
} 
} 
elseif($HTTP_GET_VARS[op]=="huntit") 
{ 
$session[user][specialinc] = "pig.php"; 
output("`^Du rennst so schnell du kannst in die ungef�hre Richtung, wo das Tier verschwunden ist. Dann st��t du auf einen Weg. Du glaubst links von dir ein Grunzen zu h�ren, doch dein Blut rauscht dir in den Ohren. Wohin wirst du dich wenden?"); 
addnav("Nach links","forest.php?op=left"); 
addnav("Nach rechts","forest.php?op=right"); 
addnav("Geradeaus ins dichte Gestr�pp","forest.php?op=forward"); 
} 
elseif($HTTP_GET_VARS[op]=="right" || $HTTP_GET_VARS[op]=="left" || $HTTP_GET_VARS[op]=="forward") 
{ 
$session[user][specialinc] = "pig.php"; 
switch($HTTP_GET_VARS[op]) 
{ 
case "left": 
$weg = "nach links und dann so schnell du kannst `7den Weg`^ entlang."; 
break; 
case "right": 
$weg = "nach rechts und dann so schnell du kannst `7den Weg`^ entlang."; 
break; 
case "forward": 
$weg = "geradeaus ins dichte `7Gestr�pp`^. Du kommst nur langsam vorw�rts, aber hier kannst du deinen Gegner nicht �berh�ren."; 
break; 
} 

output("`^Du rennst $weg`n`n"); 

$hunterlevel = $session[user][hunter]; 
$money=e_rand(10,60); 
switch(e_rand(0,(20-$hunterlevel))) 
{ 
case 0: 
case 1: 
case 2: 
case 3: 
case 4: 
case 5: 
output("Du st�rzt dich mit voller Wucht auf das, was du f�r das Schwein h�lst. Ein lautes, erschrecktes Quicken gibt dir recht, dann h�rst du noch den dumpfen `7*Plumps*`^, wie der K�rper umf�llt."); 
switch(e_rand(0,3)) 
{ 
case 0: 
output(" Als du das Schwein genauer untersuchst, findest du einen Edelstein! Das Tier muss ihn wohl mit einem Tr�ffel verwechselt haben..."); 
$session[user][gems]++; 
break; 
default: 
output(" Als du das Schwein genauer untersuchst, findest du in seinem Magen ".(e_rand(1,3)*$money*$session[user][level])." Gold."); 
$session[user][gold]+=e_rand(1,3)*$money*$session[user][level]; 
break; 
} 
break; 
default: 
output("Du st�rzt dich mit voller Wucht auf das, was du f�r das Schwein h�lst. Als du schaust, was du aufgespie�t hast, musst du entt�uscht feststellen, dass es wohl nur ein paar Bl�tter waren."); 
break; 
} 

addnav("Z?Zur�ck in den Wald","forest.php?op=return"); 
} 
else 
{ 
addnav("W?Weiter","forest.php"); 
output("`2Du machst dich mit schmerzendem Oberschenkel auf den Weg zur�ck in den Wald."); 
$session[user][specialinc]=""; 
} 
?>