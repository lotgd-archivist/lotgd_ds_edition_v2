<?

/*********************************************

Lots of Code from: lonnyl69 - Big thanks for the help.

By: Kevin Hatfield - Arune v1.0

06-19-04 - Public Release

Written for Fishing Add-On - Poseidon Pool



Translation and simple modifications by deZent deZent@onetimepad.de





ALTER TABLE accounts ADD wormprice int(11) unsigned not null default '0';

ALTER TABLE accounts ADD minnowprice int(11) unsigned not null default '0';

ALTER TABLE accounts ADD wormavail int(11) unsigned not null default '0';

ALTER TABLE accounts ADD minnowavail int(11) unsigned not null default '0';

ALTER TABLE accounts ADD trades int(11) unsigned not null default '0';

ALTER TABLE accounts ADD worms int(11) unsigned not null default '0';

ALTER TABLE accounts ADD minnows int(11) unsigned not null default '0';

ALTER TABLE accounts ADD fishturn int(11) unsigned not null default '0';

add to newday.php

$session['user']['trades'] = 10;

if ($session['user'][dragonkills]>1)$session['user']['fishturn'] = 3;

if ($session['user'][dragonkills]>3)$session['user']['fishturn'] = 4;

if ($session['user'][dragonkills]>5)$session['user']['fishturn'] = 5;

Now in village.php:

addnav("Poseidon Pool","pool.php");

********************************************/



// Modifikationen und Bugfix by Maris (Maraxxus@gmx.de)
// noch etwas ver�ndert von Talion, um das Reload-Prob zu l�sen. Trotzdem hei�er Kandidat f�r komplettes Neuschreiben.



require_once "common.php";

checkday();

addcommentary();

$sql = "SELECT worms,minnows,fishturn FROM account_extra_info WHERE acctid=".$session['user']['acctid']."";
$result = db_query($sql) or die(db_error(LINK));
$rowf = db_fetch_assoc($result);

if ($_GET['op']=="erhangen")

{

    $session['user']['alive']=false;

    $session['user']['hitpoints']=0;

    addnav("�tsch, erwischt...");

    addnav("Na toll!","shades.php");

}



page_header("Der magische See");

//check and display inventory



if ($_GET['op']=="wormsplus"){

$counter=$_GET['wp'];
$counter+=$rowf['worms'];
$sql = "UPDATE account_extra_info SET worms=$counter WHERE acctid=".$session['user']['acctid']."";
db_query($sql);

}

if ($_GET['op']=="minnowsplus"){

$counter=$_GET['mp'];
$counter+=$rowf['minnows'];
$sql = "UPDATE account_extra_info SET minnows=$counter WHERE acctid=".$session['user']['acctid']."";
db_query($sql);

}



output("`2Du hast in deinem Beutel.`n");



//Minnows

if ($_GET['op']=="check1"){

$mcounter=($rowf['minnows'])-1;
$fturns=($rowf['fishturn'])-1;
$sql = "UPDATE account_extra_info SET minnows=$mcounter, fishturn=$fturns WHERE acctid=".$session['user']['acctid']."";
db_query($sql);

$sql = "SELECT worms,minnows,fishturn FROM account_extra_info WHERE acctid=".$session['user']['acctid']."";
$result = db_query($sql) or die(db_error(LINK));
$rowf = db_fetch_assoc($result);

}

$minnow=$rowf['minnows'];

if ($rowf['minnows']>0){ //These were added due to counters going into negative.

output("`!Fliegen - $minnow`n");

}else{

$minnow=0;

output("`!Fliegen - 0`n");

}



//Worms

if ($_GET['op']=="check2"){

$wcounter=$rowf['worms']-1;
$fturns=$rowf['fishturn']-1;
$sql = "UPDATE account_extra_info SET worms=$wcounter, fishturn=$fturns WHERE acctid=".$session['user']['acctid']."";
db_query($sql);

$sql = "SELECT worms,minnows,fishturn FROM account_extra_info WHERE acctid=".$session['user']['acctid']."";
$result = db_query($sql) or die(db_error(LINK));
$rowf = db_fetch_assoc($result);

}

$worms=$rowf['worms'];

if ($rowf['worms']>0){ //These were added due to counters going into negative.

output("`!W�rmer - $worms`n");

}else{

$worms=0;

output("`!W�rmer - 0`n");

}

$inventory=$rowf['worms'];



// Golden Egg

if ($_GET['op']=="check3"){



 if ($session['user']['acctid']==getsetting("hasegg",0)){



  savesetting("hasegg","0");
  
  item_set(' tpl_id="goldenegg"', array('owner'=>0) );

  addnews("`@".$session['user']['name']."`@ hat das `^Goldene Ei`@ beim Angeln verloren`V!");

 $fturns=0;
  
  } else { $fturns=$rowf['fishturn']-5; }

$sql = "UPDATE account_extra_info SET fishturn=$fturns WHERE acctid=".$session['user']['acctid']."";
db_query($sql);

$sql = "SELECT worms,minnows,fishturn FROM account_extra_info WHERE acctid=".$session['user']['acctid']."";
$result = db_query($sql) or die(db_error(LINK));
$rowf = db_fetch_assoc($result);

}



$inventory+=$rowf['minnows'];

$fishturns=$rowf['fishturn'];

if ($rowf['fishturn']>0){ //These were added due to counters going into negative.

output("`!Runden zum fischen - $fishturns`n");

}else{

$fishturns=0;

output("`!Runden zum fischen - 0`n");

}

if ($_GET['op'] == "" ){
	
	output("`n`2-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-`n");
	
	$show_invent = true;

	viewcommentary("fishing", "Etwas schreiben", 25, "says");

}
else {
	
	if ($session['user']['alive']!=false) { addnav("R?Zur�ck zum Ufer","fish.php"); }
	
}

//output("`c<img src='images/fishing.jpg''>`c", true);

if ($rowf['minnows'] > 0 and $rowf['fishturn'] > 0 and $session['user']['alive']!=false) addnav("Fliege auswerfen","fish.php?op=check1");

if ($rowf['worms'] > 0 and $rowf['fishturn'] > 0 and $session['user']['alive']!=false ) addnav("Wurm auswerfen","fish.php?op=check2");

if ($session['user']['acctid']==getsetting("hasegg",0) and $rowf['fishturn'] > 0 and $session['user']['alive']!=false) addnav("Das `^goldene Ei`V als K�der verwenden","fish.php?op=check3");

if (su_check(SU_RIGHT_DEBUG)) { addnav("Wasserschrein","watershrine.php"); }



if ($session['user']['alive']!=false) {
		
    addnav("R?Zur�ck zum See","pool.php");

    addnav("B?Angelshop","bait.php");

}



output("`n`n`7Du folgst dem Weg um den See...`n");

output("Wenn du dich umschaust, siehst Du andere Dorfbewohner, die sich am See aufhalten.`n");

output("Du bist Dir sicher, dass Dir heute der gro�e Wurf gelingt.`n`n");

if(su_check(SU_RIGHT_DEBUG)) {
	addnav('Gl�hend','fish.php?op=check1&su_action=10');
	addnav('Geh�rtet','fish.php?op=check2&su_action=14');
}

if ($_GET['op']=="check1"){

        output("`n`nDu wirfst Deine Angel aus...`n`n");

              check1();

}

if ($_GET['op']=="check2"){

        output("`n`nDu wirfst Deine Angel aus...`n`n");

              check2();

}



if ($_GET['op']=="check3"){

        output("`n`nDu wickelst Deine Leine sorgf�ltig um das `^goldene Ei`V und l�sst es vorsichtig zu Wasser...`n`n");

              check3();

}



if ($_GET['op']=="erhangen"){

output("`4Der Wind erfasst deine Angelschnur und wickelt sie um deinen Hals...Der Haken verf�ngt sich in deinem Mund!`n`n");

output("`3In Panik ziehst Du an deiner Angel!`n");

output("`7Dabei ziehst Du die Schlinge noch fester zu und f�llst auf den Boden!`n");

//addnews("`@".$session['user']['name']."`@ wollte Fische fangen und hat sich dabei selbst erhangen.");

}

/*******************

Fishing With Minnows

*******************/

function check1(){

global $session;

if($rowf['fishturn']<0)

{

    output("... oder lieber doch nicht. F�r heute hast du vom Angeln definitiv genug!`n`n");

    return;

}



if($rowf['minnows']<0)

{

    output("Kurze Zeit sp�ter siehst du ein, dass sich die Fische nicht mit einem blanken Haken zufrieden geben.`n`n");

    return;

}


$int_decide = e_rand(1,29);

if($_GET['su_action']) {
	$int_decide = $_GET['su_action'];
}


switch ($int_decide){

case 1:

output("Ein Boot?!`n");

output("Du brauchst gut 10 Minuten, bis du endlich den Knoten vom Steg gel�st hast...`n");

output("Das hat gedauert....`n`n");

output("`bDu verlierst diese Angelrunde`n`n");



break;



case 2:

output("`@ Du f�ngst einen kleinen Beutel... `n`n");

$a=e_rand(2,75);

output("In dem Beutel findest du `^$a`^ Gold !!`n");

$session['user']['gold']+= $a;



break;



case 3:

output("Beim Auswerfen verf�ngt sich der Angelhaken in deinem Ohr!!!! `n`n");

$b=e_rand(10,20);

output("Du verlierst `^$b`^ Lebenspunkte`n");

$session['user']['hitpoints'] -= $b;

if ($session['user']['hitpoints']<=0)

{

     $session['user']['hitpoints']=1;

     output("`$ Ramius akzeptiert deinen j�mmerlichen Anglertod nicht!`n Er gibt dir einen Lebenspunkt, da er sein Schattenreich nicht mit unf�higen Weicheiern f�llen m�chte! `n");

};

output("`!So ein gef�hrlicher See!`!`n");

output("`4Du entscheidest dich heute lieber nicht mehr zu angeln...`n`n");

$sql = "UPDATE account_extra_info SET fishturn=0 WHERE acctid=".$session['user']['acctid']."";
db_query($sql);

break;



case 4:

output("Mit all deinem K�nnen hast du nichts gefangen! `n`n");



break;



case 5:

output("`@Du bist dir sicher, dass du einen schweren Fisch am Haken hast!!!`n`n");

output("`@.........`n");

output("`@Leider war es doch nur ein alter Stiefel`n");



break;



case 6:

output("`2Dein Haken verf�ngt sich in deiner Hand!! `nDu verlierst 12 Lebenspunkte. `n`n");

$session['user']['hitpoints']-=12;

if ($session['user']['hitpoints']<=0)

{

     $session['user']['hitpoints']=1;

     output("`$ Ramius akzeptiert deinen j�mmerlichen Anglertod nicht!`n Er gibt dir einen Lebenspunkt, da er sein Schattenreich nicht mit unf�higen Weicheiern f�llen m�chte! ");

};



break;



case 7:

output("Leider bist du beim Fischen eingeschlafen, und hast nicht mitbekommen ob etwas angebissen hat!`n`n");



break;



case 8:

output("Gerade als du deine Leine einholst siehst du im feuchten Gras etwas schimmern.....`n`n");

output("`^`bDu findest einen Edelstein !!! `^`b`n`n");

$session['user']['gems']+=1;



break;



case 9:

output("`@Du f�ngst etwas... `n`n");

output("`!Ein kleiner Beutel h�ngt an deinem Angelhaken...`n`n");

output("`&`bDu findest 3 W�rmer!`n `b`n`n");

addnav("W�rmer behalten?");

addnav("In den Beutel!","fish.php?op=wormsplus&wp=3");



break;



case 10:

output("`!Du f�ngst ein seltsames Silberkreuz! `n`n");

output("`7Als du es vom Haken nimmst beginnt es leicht zu leuchten`n");

output("Ein pulsierendes Leuchten erhellt das Ufer!!!`n`n");

if(strchr($session['user']['weapon'],"gl�hend"))

{

    output("`b`4Deine Waffe gl�ht bereits...");

    break;

}

else

{

    output(" Du f�hlst dich st�rker und auch etwas z�her!");

    output(" Deine Verteidigung erh�ht sich um `#einen`V Punkt.");

    output(" Deine Waffe wird um `#einen`V Punkt st�rker.");

    output(" Deine Lebenspunkte erh�hen sich permanent um `#einen`V Punkt.");

    debuglog("Weapon - Glowing enhancement from pool");

    $session['user']['maxhitpoints']+=1;
    
    $session['user']['defence']+=1;

    $newweapon = "gl�hend - ".$session['user']['weapon'];

    $atk = $session['user']['weapondmg']+2;
	
	item_set_weapon($newweapon, $atk, -1, 0, 0, 1);

}

break;



case 11:

output("`4Der Wind erfasst deine Angelschnur und wickelt sie um deinen Hals...Der Haken verf�ngt sich in deinem Mund!`n`n");

output("`3In Panik ziehst du an deiner Angel!`n");

output("`7Dabei ziehst du die Schlinge noch fester zu und f�llst auf den Boden!`n");

//addnews("`@".$session['user']['name']."`@ wollte Fische fangen und hat sich dabei selbst erhangen.");

$sql = "UPDATE account_extra_info SET fishturn=0 WHERE acctid=".$session['user']['acctid']."";
db_query($sql);

$session['user']['hitpoints']=1;

output("`$ Ramius akzeptiert deinen j�mmerlichen Anglertod nicht!`n Er gibt dir einen Lebenspunkt, da er sein Schattenreich nicht mit unf�higen Weicheiern f�llen m�chte! ");



break;



case 12:

output("`3Deine Fliege ist dir vom Haken geh�pft und freut sich ihres Lebens.. `n`n `$ Seit wann k�nnen Fliegen springen?!?!`3 `n`n");



break;



case 13:

output("Du hast nichts gefangen!`n`n");



break;



case 14:

output("`7Du rutschst aus und f�llst ins Wasser !`n");

output("Da du nicht gut schwimmen kannst, kannst du dich gerade noch an Land retten.`n");

output("`^Durch diese peinliche Vorstellung verlierst du 2 Charmpunkte!`n`n");

$session['user']['charm']-=2;



break;



case 15:

output("`3Du hast Mitleid mit dem der Fliege und schenkst Ihr die Freiheit!`3 `n");

output("Dabei f�hlst du dich sehr gut und erh�lst `#einen`V Charmepunkt.`n`n");

$session['user']['charm']+=1;



break;



case 16:

output("Du f�ngst einen enormen Barsch!`n`n");

output("`7Da du eh Hunger hast isst du ihn noch am See`n");

$session['user']['hitpoints']=$session['user']['maxhitpoints'];



break;



case 17:

output("`@Du sp�rst einen Ruck an der Angel!`n`n");

output("`6Du ziehst mit einem Ruck...stolperst zur�ck und wirfst deinen Beutel mit K�dern um`n");

output("`4Du verlierst alle deine K�der!`n`n");

$sql = "UPDATE account_extra_info SET minnows=0,worms=0 WHERE acctid=".$session['user']['acctid']."";
db_query($sql);

break;



case 18:

output("`@Du sp�rst einen Ruck an der Angel!`n`n");

output("Du springst zur�ck und zerrst mit all deiner Kraft an der Rute!`n");

output("`7ZUVIEL f�r deine Rute! Sie bricht und schl�gt dir ins Gesicht!`n");

output("`4AUTSCH! Direkt ins Auge.... das hat weh getan`n`n");

$session['user']['hitpoints']-=10;

if ($session['user']['hitpoints']<=0)

{

     //addnews("`@".$session['user']['name']."`@ hat sich mit einer Angelrute selbst erschlagen!");

     $session['user']['hitpoints']=1;

     output("`$ Ramius akzeptiert deinen j�mmerlichen Anglertod nicht!`n Er gibt dir einen Lebenspunkt, da er sein Schattenreich nicht mit unf�higen Weicheiern f�llen m�chte! ");

};



break;



case 19:

output("`2Du ziehst eine verfaulte Wasserleiche an Land! `2`n`n");

output("`7...`n");

output("Nach kurzem �berlegen untersuchst du ihren Goldbeutel,`n");

output("`^und findest 351 Gold!`n`n");

output("`2 Die Seejungfrau des Sees findet deine Aktion jedoch nicht sehr nett und verflucht dich!`n");

output("`^ Du verlierst `4einen`^ Punkt Angriff und `4einen`^ Punkt Verteidigung.`n`n");

$sql = "UPDATE account_extra_info SET fishturn=0 WHERE acctid=".$session['user']['acctid']."";
db_query($sql);

$session['user']['gold']+=351;

$session['user']['attack']-=1;

if ($session['user']['attack']<=0)

{

$session['user']['attack']=1;

};

$session['user']['defence']-=1;

if ($session['user']['defence']<=0)

{

$session['user']['defence']=1;

};



break;



case 20:

output("Du f�ngst leider nichts!`n`n Eine Erfahrung mehr in deinem Leben..`n `$ Du lernst, dass man nicht immer gewinnen kann");

$session['user']['experience']+=100;



break;



case 21:

output("`2Beim auswerfen der Leine siehst du eine Box mit W�rmern neben dir im Geb�sch!`2`n`n");

output("`^Du findest 5 W�rmer!`n`n");

addnav("W�rmer behalten?");

addnav("In den Beutel!","fish.php?op=wormsplus&wp=5");



break;



case 22:

output("Du f�ngst einen kleinen Lederbeutel! `n`n");

output("`^Darin findest du 2 Edelsteine!`n");

$session['user']['gems']+=2;



break;



case 23:

output("`2Du siehst eine kleine Welle, die sich sehr schnell auf deinen K�der zubewegt!`n`n");

output("`$ ZU `2 schnell f�r deinen Geschmack!`n");

output("Sicherheitshalber ziehst du deine Angel schnell wieder ein!`n");
break;



case 24:

output("Ein kleiner Goldfisch springt ans Ufer und bei�t dir in den Zeh!`n AUTSCH!");

$session['user']['hitpoints']-=5;

if ($session['user']['hitpoints']<=0)

{

     //addnews("`@".$session['user']['name']."`@ wurde beim Angeln von einem `5Goldfisch`@ massakriert.");

     $session['user']['hitpoints']=1;

     output("`$ Ramius akzeptiert deinen j�mmerlichen Anglertod nicht!`n Er gibt dir einen Lebenspunkt, da er sein Schattenreich nicht mit unf�higen Weicheiern f�llen m�chte! ");

};



break;



case 25:

/* Ein Traum f�r Seepusher.. ;)
output("Du triffst genau ins Zentrum des Sees!`n`n Ein Blitz durchf�hrt deinen K�rper`n");

output("Die G�tter meinen es heute gut mit dir!");

output("`^Du f�hlst dich st�rker! Dein Angriff steigt um `#2`V Punkte.");

$session['user']['attack']+=2;

*/

break;



case 26:

output("Du f�ngst pr�chtigen Fisch, der in allen Farben des Regenbogens leuchtet!`n");

output("Pech! Dieser Fisch war wohl einem Gott heilig, der dich nun f�r deinen Frevel straft!`n");

output("Ein Blitz durchzuckt deinen K�rper und du f�hlst dich schw�cher!`n");

output("`^Du verlierst einen Angriffspunkt.`n`n");

$session['user']['attack']-=1;

if ($session['user']['attack']<=0)

{

$session['user']['attack']=1;

};



break;



case 27:

output("`4Du stolperst �ber einen Stein und f�llst ins Wasser! `0!`n`n");

output("Nat�rlich landest du an der seichtesten Stelle des Sees und knallst mit dem Kopf auf einen Stein`n");

output("Als du wieder aufwachst stellst du fest, dass dir jemand dein ganzes Gold gestohlen hat!`n`n");

$session['user']['hitpoints']=1;

$sql = "UPDATE account_extra_info SET fishturn=0 WHERE acctid=".$session['user']['acctid']."";
db_query($sql);

$session['user']['gold']=0;



break;



case 28:

output("Du hast nichts gefangen!`n`n");



break;



case 29 :

redirect ("watershrine.php");

break;
}

}

/************************

Fishing with worms

************************/

function check2(){



global $session;

if($rowf['fishturn']<0)

{

    output("... oder lieber doch nicht. F�r heute hast du vom Angeln definitiv genug!`n`n");

    return;

}



if($rowf['worms']<0)

{

    output("Du wirfst deinen unsichtbaren Wurm aus und f�ngst einen unsichtbaren, prachtvollen Fisch! Tja, sch�n w�rs...`n`n");

    return;

}

$int_decide = e_rand(1,27);

if($_GET['su_action']) {
	$int_decide = $_GET['su_action'];
}


switch ($int_decide){
case 1:



output("Du hast, wenn man es genauer betrachtet, NICHTS gefangen! `n`n");



break;



case 2:



output("Du hast nichts gefangen! `n`n");



break;



case 3:



output("Du f�ngst einen schweren Lederbeutel...`n");

output("Darin findest du `n");

output("`^3 Edelsteine!`0`n`n");

$session['user']['gems']+=3;



break;



case 4:



output("Du f�ngst einen enormen Fisch!`n");

output("Viele Fischer werden auf dich neidisch sein.`n");

output("`^Du bekommst 1 Charmpunkt!`0`n`n");

$session['user']['charm']+=1;



break;



case 5:



output("Deine Angelschnur ist gerissen!`n");

output("Du verlierst deinen K�der`n`n");

break;



case 6:



output("Als du deinen Haken einholst siehst du, dass du einen B�schel Seegras gefangen hast.`n");

output("Der B�schel stinkt so sehr, dass sofort  `^15 Fliegen dran h�ngen`0!`n`n");

addnav("Fliegen behalten?");

addnav("In den Beutel!","fish.php?op=minnowsplus&mp=15");



break;



case 7:



output("Auch nach einer Stunde hast du noch nichts gefangen! `n`n");



break;



case 8:



output("Du siehst jemanden hinter dem Geb�sch und rufst ihm laut `iHALLO!`i zu. `n In diesem Moment f�llt dir ein wie dumm das von dir war.... `n`n Nat�rlich wei�t du, dass f�r die n�chste Stunde alle Fische verscheucht hast! `n`n");



break;



case 9:

case 10:



output("Du hast nichts gefangen! `n`n");



break;



case 11:



output("Du hast den K�dern neben den See geworfen... Eine Stunde sp�ter bist du dir endlich sicher, dass man an Land keine Fische fangen kann.. `n`n");



break;



case 12:



output("Du hast nichts gefangen! `n`n");



break;



case 13:

output("Als du deine Leine einholst siehst du etwas Gl�hendes am Haken h�ngen`n");

output("Ein schwacher Energiesto� trifft deinen K�rper`n`n");

output("`^Deine Verteidigung steigt um `#2`^ Punkte!`0`n`n");

$session['user']['defence']+=2;



break;



case 14:

output("`!Du f�ngst einen Kristall! `n`n");

output("`7Als du den Kristall in deiner Hand h�lst..`n");

output("beginnt das schwarze Wasser blau zu leuchten!!!`n`n");

if(strchr($session['user']['weapon'],"geh�rtet"))

{

    output("`b`4Deine Waffe ist immernoch geh�rtet!");

    break;

}

else

{

    output("Deine Waffe wird schwerer und irgendwie f�hlt sie sich m�chtiger an.`n");

    output("Die St�rke deiner Waffe erh�ht sich um `#5`V Punkte!`n");

     output("Deine Verteidigung steigt um `#3`V Punkte.`n`n");

    debuglog("Weapon - Crystalized enhancement from pool");

    $session['user']['hitpoints']+=10;

    $session['user']['defence']+=3;

    $newweapon = "geh�rtet - ".$session['user']['weapon'];
    
    $atk = $session['user']['weapondmg']+5;
	
	item_set_weapon($newweapon, $atk, -1, 0, 0, 1);

$sql = "UPDATE account_extra_info SET fishturn=0 WHERE acctid=".$session['user']['acctid']."";
db_query($sql);

addnews("`@".$session['user']['name']."`@ hat heute beim Angeln einen gro�en Fang gemacht!");

}

break;



case 15:



output("Du f�ngst einen gigantischen Fisch!!`n");

output("Zappelnd ziehst du ihn ans Ufer!`n");

output("Als du ihn mit all deinen Kr�ften an Land gezogen hast und feststellst, dass er nicht zur�ck ins Wasser will, sondern sich schnappend in deine Richtung bewegt, ziehst du schnell deine Waffe.!`n");

output("Unsicher stellst du dich dem Fisch..`n");

if ($session['user']['attack']<25)

{

output("`4Gerade als du zustechen willst, packt dich der Fisch unerwartet am Fu� und zieht dich ins Wasser.`n`n Du wehrst dich mit all deiner Kraft, doch das pechschwarze Wasser raubt dir bereits den Blick zur Sonne. `n Der Fisch zieht dich immer weiter in die Tiefen des Sees..");

$session['user']['experience']-=500;

$session['user']['hitpoints']-=250;

}

else

{

$waffe1=$session['user']['weapon'];

output("`!Der Fisch packt dich am Fu�, du nutzt deine Chance und erlegst ihn gekonnt mit deine(m) $waffe1 !`n`n");

$session['user']['experience']+=500;

}

if ($session['user']['hitpoints']<=0)

{

     $session['user']['hitpoints']=1;

     output("`$ Ramius akzeptiert deinen j�mmerlichen Anglertod nicht!`n Er gibt dir einen Lebenspunkt, da er sein Schattenreich nicht mit unf�higen Weicheiern f�llen m�chte! ");

};



break;



case 16:



output("Du bist beim fischen eingeschlafen.... `n Als du wieder aufwachst stellst du fest, dass dein ganzes Gold verschwunden ist`n");

$session['user']['gold']=0;

break;



case 17:



output("Du hast nichts gefangen! `n`n");

break;



case 18:

If ($session['user']['sex']==0)

{

     output("Weit entfernt siehst du den Umriss einer Gestalt durch den dichten Nebel schimmern... Es k�nnte eine Seejungfrau sein... `n`n");

     output("Es ist eine Seejungfrau!! `^ Du bekommst einen Charmpunkt`n");

}

If ($session['user']['sex']==1)

{

     output("Weit entfernt siehst du den Umriss einer Gestalt durch den dichten Nebel schimmern... Es k�nnte ein Seejungmann sein... `n`n");

     output("Es ist ein Seejungmann!! `^ Du bekommst einen Charmpunkt`n");

}

$session['user']['charm']+=1;



break;



case 19:

output("Als du deine Leine einholst siehst du etwas bedrohlich Gl�hendes am Haken h�ngen`n");

output("Ein schmerzhafter Energiesto� trifft deinen K�rper`n`n");

output("`^Deine Verteidigung sinkt um `4einen`^ Punkt!`0`n`n");

$session['user']['defence']-=1;

if ($session['user']['defence']<=0)

{

$session['user']['defence']=1;

};



break;



case 20:
$sql = "UPDATE account_extra_info SET fishturn=0 WHERE acctid=".$session['user']['acctid']."";
db_query($sql);

redirect('fish.php?op=erhangen');

break;



case 21:



output("`0Du hast einen Beutel  `^Gold`0 gefangen!`n");

output("Ganz auf all das Gold fixiert z�hlst du die M�nzen!`n");

output("`4BOOM! `0Du wurdest von etwas stumpfen getroffen...Und gehst zu Boden!`n`n");

output("`i Wieder einer auf den alten Goldbeuteltrick reingefallen`i h�rst du gerade noch als bei dir das Licht ausgeht!`n`n");

$session['user']['hitpoints']=1;

$sql = "UPDATE account_extra_info SET fishturn=0 WHERE acctid=".$session['user']['acctid']."";
db_query($sql);

$session['user']['gold']=0;
break;

case 22:

output("Du denkst dir, dass es schon sehr unw�rdig f�r einen Krieger ist in aller Ruhe zu angeln w�hrend der Drache sein Unwesen treibt.`n");

output("Diese Ansicht teilen auch die G�tter.`n`n");

output("`4Sie verfluchen dich! Dein Angriff und deine Verteidigung sinken um jeweils 2 Punkte!`0`n`n");

addnews("`@".$session['user']['name']."`@ bekam heute beim Angeln von den G�ttern eine Lektion erteilt.");

$session['user']['defence']-=2;

if ($session['user']['defence']<=0)

{

$session['user']['defence']=1;

};

$session['user']['attack']-=2;

if ($session['user']['attack']<=0)

{

$session['user']['attack']=1;

};

$sql = "UPDATE account_extra_info SET fishturn=0 WHERE acctid=".$session['user']['acctid']."";
db_query($sql);
break;

case 23:
output("Du hast etwas gefangen ...`n");

output("`^Eine Mei�el!`n");

output("`&Als du �ber die vielf�ltigen Einsatzgebiete einer Mei�el nachdenkst ber�hrst du versehntlich deine R�stung.`n");

output("`0Wow..irgendwie passt deine R�stung jetzt viel besser als zuvor. Sie wirkt auch irgendwie stabiler!`n");

if(strchr($session['user']['armor'],"verst�rkt"))

{

    output("`b`4Leider war deine R�stung auch zuvor schon ver�ndert und du stellst fest, dass du dir das Ganze nur eingebildet hast!`n`n");

    break;

}

else

{

    output(" Deine R�stung wurde verbessert! Vor lauter Freude wirfst du die Mei�el wieder in den See`n");

    output("Deine R�stung wird um `#3`V Punkte st�rker!`n`n");

    debuglog("Armor - Chisel enhancement from pool");

    $newarmor = "verst�rkt ".$session['user']['armor'];

    $session['user']['charm']+=1;
	
	item_set_armor($newarmor, $session['user']['armordef']+3, -1, 0, 0, 1);

    output("Mit der neuen R�stung siehst du viel besser aus!`n");

    output("`^Du bekommst 1 Charmpunkt!`n`n");

}



break;



case 24:



output("Du hast nichts gefangen! `n`n");



break;



case 25:

redirect ("watershrine.php");



break;

}

}



/************************

Fishing with Golden Egg

************************/

function check3(){

global $session;

$chance=(e_rand(1,10));

$chance = 4;

switch ($chance) {

case 1:



output("Gerade als du in Tr�men um den Reichtum schwelgst, der dich erwartet... `n");

output("macht es `n`n");

output("`4BOOM`V `n`n");

output("Halb bewusstlos siehst du gerade noch jemanden mit dem `^goldenen Ei`V unter dem Arm davonlaufen. `n");

$session['user']['hitpoints']=1;



break;



case 2:



output("Das `^Ei`V geht unter... aber pl�tzlich beginnt das Wasser an der Stelle wild zu blubbern und zu sch�umen. `n");

output("Etwas SEHR gro�es n�hert sich aus den Tiefen des Sees!!!! `n");

output("Vor lauter Schreck l�sst du die Angel fallen, die vom Gewicht des `^Eis`V sofort untergeht. `n`n");



break;



case 3:



output("Du sp�rst einen Ruck und bevor du dich versehen kannst h�lst du nur noch eine abgebissene Leine in der Hand! `n`n");



break;



case 4:



output("Schon bald beginnt etwas heftig an deinem K�der zu zerren. `n");

output("Emsig ziehst du es an Land... `n");

output("Zwar ist das `^Ei`V fort, jedoch befindet sich stattdessen eine seltsame Waffe an deiner Leine.`n`n");

output("Du hast das legend�re Schwert `4Ausweider`V gefunden!!!`n`n");

output("In Angst man k�nne es dir wieder wegnehmen eilst du sofort zum Dorfplatz und vergi�t vor lauter Aufregung deine alte Waffe am Ufer. `n`n");


$item_ausw = item_get_tpl(' tpl_id="ausweider" ');

$ausw_id = item_add($session['user']['acctid'],0,false,$item_ausw);

item_set_weapon($item_ausw['tpl_name'], $item_ausw['tpl_value1'], $item_ausw['tpl_gold'], $ausw_id, 0, 2);

break;



case 5:



output("Du sp�rst einen Ruck an deiner Leine! `n");

output("Als du die Angel einholst entdeckst du anstelle des K�der einen kleinen Eimer`n");

output("gef�llt mit `@50`V Edelsteinen!!! `n`n");

$session['user']['gems']+=50;

break;



case 6:



output("Die Fee des Sees nimmt dein Geschenk dankend an.`n");

output("Als Zeichen ihrer Wertsch�tzung belohnt sie dich reich.`n`n");

output("Du erh�ltst : `n");

output("`@10 permanente Lebenspunkte, `n");

output("5 Punkte Angriff und 5 Punkte Verteidigung, `n");

output("10 Punkte Charme `n");

output("und 200 Gefallen bei Ramius! `n`n");

$session['user']['maxhitpoints']+=10;

$session['user']['attack']+=5;

$session['user']['defence']+=5;

$session['user']['charm']+=10;

$session['user']['deathpower']+=200;

break;



case 7:



output("Du hast etwas sehr Schweres an der Leine! `n");

output("Emsig ziehst du es an Land... `n");

output("Pl�tzlich beginnt deine Leine zu gl�hen und das Gl�hen geht auch auf dich �ber.`n");

output("Das `^Ei`V ist zwar fort, doch eine `%Schutzaura`V umgibt dich!`n`n");

output("�bergl�cklich rei�t du dir deine alte, schwere R�stung vom Leib und wirfst sie fort. Die wirst du nicht wieder brauchen. `n`n");


$item_ausw = item_get_tpl(' tpl_id="schtzaura" ');

$ausw_id = item_add($session['user']['acctid'],0,false,$item_ausw);

item_set_armor($item_ausw['tpl_name'], $item_ausw['tpl_value1'], $item_ausw['tpl_gold'], $ausw_id);

break;



case 8:



output("Das `^Ei`V geht unter wie ein Stein! `n");

output("Die schlaff herabh�ngene Leine gibt dir das ungute Gef�hl, dass diese Aktion nicht besonders clever war. `n`n");



break;



case 9:



output("Etwas hat angebissen! `n");

output("Doch mit gewaltiger Kraft wird dir deine Angelrute mitsamt `^Ei`V aus den H�nden gerissen... So ein Pech aber auch... `n`n");



break;



case 10:



output("Es tut sich absolut gar nichts... `n");

output("Als du nach einer ganzen Weile deinen wertvollen K�der wieder an Land ziehen willst merkst du, dass nur noch ein wertloser Stein an deiner Leine h�ngt! `n`n");



break;



}

}



page_footer();

?> 

