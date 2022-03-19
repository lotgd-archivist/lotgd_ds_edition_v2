<?

/**
Troph�enjagd im Pvp
modifiziert : pvp.php
by Maris (Maraxxus@gmx.de)
**/

// modded by talion f�rs neue itemsys

require_once("common.php");
page_header();

//�berblick
if ($_GET[op]=="look"){
$name=rawurldecode($_GET[who]);
$who=rawurlencode($name);
$dks=$_GET[dks];
$where=$_GET[where];
$id=$_GET[id];

Output("`3Vor dir liegt `&".$name."`3 ausgestreckt auf dem Boden.`nDie Gelegenheit erscheint dir g�nstig eine kleine Troph�e von deinem Opfer f�r deine Sammlung mitzunehmen.`nWas m�chtest du dir mitnehmen ?`n");
addnav("Troph�e");

// 2 Ohren dieses Spieler bereits vorhanden ?

if ( item_count("owner=".$session['user']['acctid']." AND tpl_id='trph' AND value2='1' AND hvalue='$id'") <=1)
{
addnav("Ein Ohr","trophy.php?op=take&who=$who&id=$id&where=$where&nmb=1&dks=$dks");
}

// 2 Augen dieses Spielers bereits vorhanden ?

if ( item_count("owner=".$session['user']['acctid']." AND tpl_id='trph' AND value2='2' AND hvalue='$id'") <=1)
{
addnav("Ein Auge","trophy.php?op=take&who=$who&id=$id&where=$where&nmb=2&dks=$dks");
}

// 2 H�nde dieses Spielers bereits vorhanden ?

if ( item_count("owner=".$session['user']['acctid']." AND tpl_id='trph' AND value2='3' AND hvalue='$id'") <=1)
{
addnav("Eine Hand","trophy.php?op=take&who=$who&id=$id&where=$where&nmb=3&dks=$dks");
}

// 2 F��e dieses Spielers bereits vorhanden ?

if ( item_count("owner=".$session['user']['acctid']." AND tpl_id='trph' AND value2='4' AND hvalue='$id'") <=1)
{
addnav("Ein Fu�","trophy.php?op=take&who=$who&id=$id&where=$where&nmb=4&dks=$dks");
}

// 2 Beine dieses Spielers bereits vorhanden ?

if ( item_count("owner=".$session['user']['acctid']." AND tpl_id='trph' AND value2='5' AND hvalue='$id'") <=1)
{
addnav("Ein Bein","trophy.php?op=take&who=$who&id=$id&where=$where&nmb=5&dks=$dks");
}

// 2 Arme dieses Spielers bereits vorhanden ?

if ( item_count("owner=".$session['user']['acctid']." AND tpl_id='trph' AND value2='6' AND hvalue='$id'") <=1)
{
addnav("Einen Arm","trophy.php?op=take&who=$who&id=$id&where=$where&nmb=6&dks=$dks");
}

// Kopf dieses Spielers bereits vorhanden ?

if ( item_count("owner=".$session['user']['acctid']." AND tpl_id='trph' AND value2='7' AND hvalue='$id'") == 0)
{
addnav("Den Kopf","trophy.php?op=take&who=$who&id=$id&where=$where&nmb=7&dks=$dks");
}

// Rumpf dieses Spielers bereits vorhanden ?

if ( item_count("owner=".$session['user']['acctid']." AND tpl_id='trph' AND value2='8' AND hvalue='$id'") == 0)
{
addnav("Den Rumpf","trophy.php?op=take&who=$who&id=$id&where=$where&nmb=8&dks=$dks");
}

addnav("Nichts");
}
else if ($_GET[op]=="take"){
$name=rawurldecode($_GET[who]);
$dks=$_GET[dks];
$where=$_GET[where];
// $what=$_GET[what];
$nmb=$_GET[nmb];
$id=$_GET[id];

switch ($nmb) {
  case 1 :
  $what="Ein Ohr";
  break;
  case 2 :
  $what="Ein Auge";
  break;
  case 3 :
  $what="Eine Hand";
  break;
  case 4 :
  $what="Ein Fu�";
  break;
  case 5 :
  $what="Ein Bein";
  break;
  case 6 :
  $what="Ein Arm";
  break;
  case 7 :
  $what="Der Kopf";
  break;
  case 8 :
  $what="Der Rumpf";
  break;
              }

$value=($dks+1)*25;
output("`3Du machst dich an deine blutige Arbeit...`n$what `3von $name`3 verschwindet kurze Zeit sp�ter in deinem Rucksack und du machst dich schnell davon.`n`n`4Dein Ansehen leidet nat�rlich gewaltig und du f�hlst dich nach solch einer Tat auch unattraktiver!`n`n");

debuglog("verlor 25 Ansehen und 2 Charme wegen Leichenfledderei");
$session['user']['reputation']-=25;
$session['user']['charm']-=2;

$item['tpl_name'] = addslashes($what." von ".$name);
$item['tpl_gold'] = $value;
$item['tpl_value1'] = $dks;
$item['tpl_value2'] = $nmb;
$item['tpl_hvalue'] = $id;
$item['tpl_description'] = addslashes($what." von ".$name."`0. Erworben in einem fairen Kampf.");

item_add($session['user']['acctid'],'trph',true,$item);

$session['user']['reputation']-=25;

}
// Zur�ck, wohin auch immer
if ($where==1){
			addnav("Zur�ck zur Kneipe","inn.php");
		} else if ($where==2){
			addnav("Zur�ck zum Wohnviertel","houses.php?op=einbruch");
		} else {
			addnav("Zur�ck zum Dorf","village.php");
		}
		
page_footer();
?>
