<?
/*********************************************
Lots of Code from: lonnyl69 - Big thanks for help!
By: Kevin Hatfield - Arune v1.0
Written for Fishing Add-On - Poseidon Pool
06-19-04 - Public Release

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
if ($session[user][dragonkills]>1)$session[user][fishturn] = 3;
if ($session[user][dragonkills]>3)$session[user][fishturn] = 4;
if ($session[user][dragonkills]>5)$session[user][fishturn] = 5;
Now in village.php:
addnav("Poseidon Pool","pool.php");

translated into german by deZent
********************************************/

require_once "common.php";
checkday();
addcommentary();

$show_invent = true;

page_header("Poseidon Pool");
output("`c`b`&Poseidon Pool`0`b`c`n`n");
if ($_GET[op] == "" ){
        redirect("pool.php?op=chat");
}
if ($_GET[op] == "quit" ){
        redirect("kiosk.php");
}
if ($_GET[op] == "chat" ){
output("`7Dies ist ein düsterer Ort, in ein seltsam magisches Licht gehüllt. Ein sanfter Nebel schützt den See vor fremden Blicken. Du erkennst nur den Rand des Sees, das schwarze Wasser verschingt jeden Sonnenstrahl. Nur in der Mitte des Sees erkennst du etwas leuchtend blaues unter der Wasseroberfläche schimmern..`n Bei jedem Besuch des Sees wunderst du dich, dass er stehts in das selbe Licht gehüllt ist... `n Nicht einmal das Wetter ändert sich...`n`n");
output("So unwirklich und gefährlich das Ufer auch sein mag, die verborgenen Schätze des Sees sind zu verlockend...`n`n");
}
output("`n`2-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-`n");
viewcommentary("pool", "Sag was", 25, "flüstert");
addnav("Poseidons See");
addnav("Angelshop","bait.php");
if ($session['user']['dragonkills']>1) addnav("Zum See","fish.php");
output("`n`7Die Seite muss aktualisiert werden, um zu sehen, was die anderen sagen.`n");
addnav("Seite aktualisieren","pool.php?op=chat");
addnav("Zurück zum Dorf");
addnav("Zurück zum Dorf","village.php");
page_footer();
?>