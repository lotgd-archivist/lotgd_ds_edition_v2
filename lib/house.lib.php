<?php
define('HOUSES_PRIVATE_ACCESS',999);
define('HOUSES_PRIVATE_FURNITURE',20); // Basiswert für Gemächer in unausgebauten Häusern
define('HOUSES_FURNITURE',35);	// Basiswert für unausgebaute Häuser
define('HOUSES_PRIVATE_I_CLASS','Dokumente');
define('HOUSES_PRIVATE_I_NAME','Zugang zu Privatraum');
define('HOUSES_PRIVATE_OI_NAME','Besitzurkunde für Privatraum');
define('HOUSES_PRIVATE_OI_CLASS','Dokumente');

$arr_links = array();

function furniture_links ($name,$hid,$owner,$itemid,$private=0) {
	
	global $session,$arr_links;
	
	if(!$arr_links[$name]) {			
		$link = '&hid='.$hid.'&owner='.$owner.'&itemid='.$itemid;
		if($private) {$link .= '&private='.$private;}
			
		if ($name=="Großer Spiegel") addnav("Spiegel","furniture.php?act=mirror".$link);
		if ($name=="Bücherregal") addnav("Bücherregal","furniture.php?act=books".$link);
		if ($name=="Edelsteinpresse") addnav("Edelsteinpresse","furniture.php?act=presse".$link);
		if ($name=="Rechenbrett") addnav("Rechenbrett","furniture.php?act=rbrett".$link);
		if ($name=="Abakus") addnav("Abakus","furniture.php?act=abakus".$link);
		if ($name=="Seth Standfigur") addnav("Seth Standfigur","furniture.php?act=seth".$link);
		if ($name=="Zielscheibe") addnav("Zielscheibe","furniture.php?act=scheibe".$link);
		if ($name=="Sandsack") addnav("Sandsack","furniture.php?act=sack".$link);
		if ($name=="Strohpuppe") addnav("Strohpuppe","furniture.php?act=puppe".$link);
		if ($name=="Schaukasten") addnav("Schaukasten","furniture.php?act=kasten".$link);
		if ($name=="Kadaverpuppe") addnav("Kadaverpuppe","furniture.php?act=kpuppe".$link);
		if ($name=='Truhenfalle') addnav('Truhenfalle',"furniture.php?act=tfalle".$link);
		if ($name=='Schmuckkästchen') addnav('Schmuckkästchen',"furniture.php?act=schmuck".$link);
	}
	
	$arr_links[$name] = true;
	
}

// Häuser verkaufen
function sell_house($id,$state,$makler=false) {
if ($state<10)
{
$newstate=2;
}
else
{
$zehner=floor($state*0.1)*10;
$einer=$state-$zehner;
switch ($einer)
{
  case 0:
  $newstate=$state+2;
  break;
  case 4:
  case 7:
  $newstate=$state+1;
  break;
}
}
if ($makler)
{
$owner="owner=0,";
}
$sql = "UPDATE houses SET ".$owner." status=".$newstate." WHERE houseid=$id";
db_query($sql);
}

// Häuser kaufen
function buy_house($id,$state) {
  global $session;
if ($state<10)
{
$newstate=1;
}
else
{
$zehner=floor($state*0.1)*10;
$einer=$state-$zehner;
switch ($einer)
{
  case 2:
  case 3:
  $newstate=$zehner;
  break;
  case 5:
  case 6:
  $newstate=$zehner+4;
  break;
  case 8:
  case 9:
  $newstate=$zehner+7;
  break;
}
}

$sql = "UPDATE houses SET owner=".$session[user][acctid].",status=".$newstate.",gold=0,gems=0 WHERE houseid=$id";
db_query($sql);
}

function get_max_furniture ($state=0,$private=false) {
	$var = ($private ? HOUSES_PRIVATE_FURNITURE : HOUSES_FURNITURE);
	
	if($state >= 10 && $state < 20) {$var += 5;}	// Villa etc.
	
	if($state >= 10) {$var += 5;}	// 1. Ausbaustufe		
	
	$state2 = $state * 0.1;
	$state2 -= (int)($state * 0.1);
	$state2 = round($state2,1);
	if($state >= 10) {
		if($state2 == 0.4 || $state2 == 0.7) {
			$var += 5;
		}	// 2. Ausbaustufe		
	}
	
	return($var);
	
}

function reset_private ($house_id,$new_owner=0,$old_owner=0,$delete=false) {
	
	if(!$delete) {
	
		if(!$house_id) {return;}
				
		$sql = "UPDATE items SET owner=".$new_owner.",description='Besitzurkunde für ein Privatgemach in Haus Nr. ".$house_id."' WHERE name='".HOUSES_PRIVATE_OI_NAME."' AND value1=".$house_id.($old_owner?" AND owner=".$old_owner:"");
		db_query($sql);
		$sql = "DELETE FROM items WHERE name='".HOUSES_PRIVATE_I_NAME."' AND value1=".$house_id.($old_owner?" AND value2=".$old_owner:"");
		db_query($sql);
				
		$sql = "UPDATE items SET value1=0,hvalue2=0 WHERE class='Möbel' AND value1=".$house_id.($old_owner?" AND owner=".$old_owner." AND hvalue2=".$old_owner:" AND hvalue2>0");
		db_query($sql);
		
	}
	
	else {
		
		if(!$old_owner && !$house_id) {return;}
		$sql="DELETE FROM items WHERE name='".HOUSES_PRIVATE_OI_NAME."'".($house_id?" AND value1=".$house_id : "").($old_owner?" AND owner=".$old_owner:"");
		db_query($sql);
		// Alle Einladungen löschen	
		$sql="DELETE FROM items WHERE name='".HOUSES_PRIVATE_I_NAME."'".($house_id?" AND value1=".$house_id : "").($old_owner?" AND value2=".$old_owner:"");
		db_query($sql);
		
		$sql = "UPDATE items SET value1=0,hvalue2=0 WHERE class='Möbel' AND value1=".$house_id.($old_owner?" AND owner=".$old_owner." AND hvalue2=".$old_owner:" AND value2>0");
		db_query($sql);
	}
	
}

// Funktion zur Ausgabe des Hausstatus
// tcb, 5.5.05
// Überarbeitet am 7.8.05 für weitere Haustypen by Maris

function get_house_state ($state,$check) {
  if ($state>=10) {

  $value1=(int)($state*0.1);
 switch($value1) {
   case 1 :
   $expr="`%Anwesen ";
   $expr2="ein `%Anwesen ";
   $typ11="`%Villa ";
   $typ12="eine `%Villa ";
   $typ21="`%Gasthaus ";
   $typ22="ein `%Gasthaus ";
   break;
   case 2 :
   $expr="`QFestung ";
   $expr2="eine `QFestung ";
   $typ11="`QTurm ";
   $typ12="ein `QTurm ";
   $typ21="`QBurg ";
   $typ22="eine `QBurg ";
   break;
   case 3 :
   $expr="`tVersteck ";
   $expr2="ein `tVersteck ";
   $typ11="`tRefugium ";
   $typ12="ein `tRefugium ";
   $typ21="`tKellergewölbe ";
   $typ22="ein `tKellergewölbe ";
   break;
   case 4 :
   $expr="`5Gildenhaus ";
   $expr2="ein `5Gildenhaus ";
   $typ11="`5Zunfthaus ";
   $typ12="ein `5Zunfthaus ";
   $typ21="`5Handelshaus ";
   $typ22="ein `5Handelshaus ";
   break;
   case 5 :
   $expr="`tBauernhof ";
   $expr2="ein `tBauernhof ";
   $typ11="`tTierfarm ";
   $typ12="eine `tTierfarm ";
   $typ21="`tGutshof ";
   $typ22="ein `tGutshof ";
   break;
   case 6 :
   $expr="`TGruft ";
   $expr2="eine `TGruft ";
   $typ11="`TKrypta ";
   $typ12="eine `TKrypta ";
   $typ21="`TKatakomben ";
   $typ22="ein `TKatakombenbau ";
   break;
   case 7 :
   $expr="`qKerker ";
   $expr2="ein `qKerker ";
   $typ11="`qGefängnis ";
   $typ12="ein `qGefängnis ";
   $typ21="`qVerlies ";
   $typ22="ein `qVerlies ";
   break;
   case 8 :
   $expr="`&Kloster ";
   $expr2="ein `&Kloster ";
   $typ11="`&Abtei ";
   $typ12="eine `&Abtei ";
   $typ21="`&Ritterorden ";
   $typ22="ein `&Ritterorden ";
   break;
   case 9 :
   $expr="`vTrainingslager ";
   $expr2="ein `vTrainingslager ";
   $typ11="`vKaserne ";
   $typ12="eine `vKaserne ";
   $typ21="`vSöldnerlager ";
   $typ22="ein `vSöldnerlager ";
   break;
   case 10 :
   $expr="`7Bordell ";
   $expr2="ein `7Bordell ";
   $typ11="`7Rotlichtpalast ";
   $typ12="ein `7Rotlichtpalast ";
   $typ21="`7üble Spelunke ";
   $typ22="eine `7üble Spelunke ";
   break;
 }

$value2=$state-($value1*10);

 switch($value2) {
   case 0 :
   break;
   case 1 :
   $expr=$expr."`6im Ausbau ";
   $expr2=$expr2."`6im Ausbau ";
   break;
   case 2 :
   $expr=$expr."`^zum Verkauf ";
   $expr2=$expr2."`^zum Verkauf ";
   break;
   case 3 :
   $expr=$expr."`4(verlassen) ";
   $expr2=$expr2."`4(verlassen) ";
   break;
   case 4 :
   $expr=$typ11;
   $expr2=$typ12;
   break;
   case 5 :
   $expr=$typ11."`^zum Verkauf ";
   $expr2=$typ12."`^zum Verkauf ";
   break;
   case 6 :
   $expr=$typ11."`4(verlassen) ";
   $expr2=$typ12."`4(verlassen) ";
   break;
   case 7 :
   $expr=$typ21;
   $expr2=$typ22;
   break;
   case 8 :
   $expr=$typ21."`^zum Verkauf ";
   $expr2=$typ22."`^zum Verkauf ";
   break;
   case 9 :
   $expr=$typ21."`4(verlassen) ";
   $expr2=$typ22."`4(verlassen) ";
   break;
   }

} else
 switch ($state) {
  case 0 :
  $expr="`6Baustelle";
  $expr2="eine `6Baustelle";
  break;
  case 1 :
  $expr="`!Wohnhaus";
  $expr2="ein `!Wohnhaus";
  break;
  case 2 :
  $expr="`^zum Verkauf";
  $expr2="`^zum Verkauf";
  break;
  case 3 :
  $expr="`4verlassen";
  $expr2="`4verlassen";
  break;
  case 4 :
  $expr="`\$Bauruine";
  $expr2="eine `\$Bauruine";
  break;
  case 5 :
  $expr="`6im Ausbau";
  $expr2="`6im Ausbau";
  break;
 }
$expr=$expr."`0";
if ($check) { return($expr2); } else return($expr);
 }
 
function get_opponent($number) {
  switch($number) {
case 1 :
$opp="`^ein Dackel`0";
break;
case 2 :
$opp="`^ein Waschbär`0";
break;
case 3 :
$opp="`^ein Schwarm Bienen`0";
break;
case 4 :
$opp="`^ein Noob`0";
break;
case 5 :
$opp="`^eine Würgeschlange`0";
break;
case 6 :
$opp="`^ein Rudel Frettchen`0";
break;
case 7 :
$opp="`^ein Schlagersänger`0";
break;
case 8 :
$opp="`^ein Schwarzbär`0";
break;
case 9 :
$opp="`^eine Schwiegermutter`0";
break;
case 10 :
$opp="`^Hunk, der Halboger`0";
break;
  }
return($opp);
}

?>
