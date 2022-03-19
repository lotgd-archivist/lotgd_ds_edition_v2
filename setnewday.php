<?php

// 11092004

/*setweather.php
An element of the global weather mod Version 0.5
Written by Talisman
Latest version available at http://dragonprime.cawsquad.net

translation: anpera
*/



// Wenn es Zeit zum Löschen veralteter Inhalte ist:
$int_last_cleanup = strtotime(getsetting('lastcleanup','0000-00-00 00:00:00'));
$int_cleanup_interval = getsetting('cleanupinterval',43200);
$int_expected_cleanup = $int_last_cleanup + $int_cleanup_interval;

if( $int_expected_cleanup < time() ) {
		
	savesetting('lastcleanup',date('Y-m-d H:i:s',$int_expected_cleanup));
	cleanup();
		
}
// END cleanup



// Vendor in town?
$chance=e_rand(1,4);
if ($chance==2)
{
	savesetting("vendor",1);
	$sql = 'INSERT INTO news(newstext,newsdate,accountid) VALUES (\'`qDer Wanderhändler ist heute im Dorf!`0\',NOW(),0)';
	db_query($sql) or die(db_error($link));
}
else
{
	savesetting("vendor","0");
	$sql = 'INSERT INTO news(newstext,newsdate,accountid) VALUES (\'`qKeine Spur vom Wanderhändler...`0\',NOW(),0)';
	db_query($sql) or die(db_error($link));
}

// Other hidden paths
$spec='Keines';
$what=e_rand(1,3);
if ($what==1) $spec='Waldsee';
if ($what==3) $spec='Orkburg';
savesetting('dailyspecial',$spec);

// Gamedate-Mod by Chaosmaker
if (getsetting('activategamedate',0)==1) 
{
	$date = getsetting('gamedate','0000-01-01');
	$date = explode('-',$date);
	$date[2]++;
	switch ($date[2]) 
	{
		case 32:
			$date[2] = 1;
			$date[1]++;
			break;
		case 31:
			if (in_array($date[1], array(4,6,9,11))) 
			{
				$date[2] = 1;
				$date[1]++;
			}
			break;
		case 30:
			if ($date[1]==2) 
			{
				$date[2] = 1;
				$date[1]++;
			}
			break;
		case 29:
			if ($date[1]==2 && ($date[0]%4!=0 || ($date[0]%100==0 && $date[0]%400!=0))) 
			{
				$date[2] = 1;
				$date[1]++;
			}
	}
	if ($date[1]==13) 
	{
		$date[1] = 1;
		$date[0]++;
	}
	$date = sprintf('%04d-%02d-%02d',$date[0],$date[1],$date[2]);
	savesetting('gamedate',$date);
}

// Wetter (sollte nach Datum erfolgen)
set_weather();

// Häuserangriffe zurücksetzen
db_query('UPDATE houses SET attacked=0 WHERE attacked > 0');

// GILDENMOD
dg_update_guilds();
// END GILDENMOD

// Zufallskommentarhistory leeren
savesetting('rcomhistory',' ');


// Nutzt die Funktionen set_title und get_title (user.lib.php) zur Vergabe des Titel Fürst von Atrahor
// Die Schleife springt jeden monat genau einmal an und kürt den Spieler mit der meisten Sympathie zum Fürst von Atrahor, der alte bekommt natürlich seinen titel automatisch entzogen

$timestamp = time();
$month = date("n",$timestamp);
$saved_month = getsetting("saved_month",12);

// Ingame-monatliches Rücksetzen der Fürstenoptionen für Steuer und Haft
$igmonth_stamp = getsetting('gamedate','0005-01-01');
$igmonth = (int)substr($igmonth_stamp,5,2);
$saved_igmonth = getsetting("saved_igmonth",9);
if($igmonth != $saved_igmonth) {
savesetting("prisonchange",1);
savesetting("taxchange",1);
savesetting("saved_igmonth",$igmonth);
}

if($month != $saved_month) {
    savesetting("callvendor",getsetting("callvendormax",5));
    get_title('Fürst');
    $new = set_title('Fürst');
    $sql = 'UPDATE account_extra_info SET sympathy=0, symp_given=0, symp_votes=0';
			db_query($sql);
	$sql = 'DELETE FROM sympathy_votes';
			db_query($sql);
			
    savesetting("saved_month",$month);
	savesetting("fuerst",addslashes($new));
}


// Die Dunklen Lande
$state=getsetting("DDL-state",6);
if ($state>1 && $state<11) // Beide Lager intakt ?
{
$order_new=getsetting("DDL_new_order",6);
$order_act=getsetting("DDL_act_order","0");
$balance=getsetting("DDL-balance",5);
$balance_malus=getsetting("DDL_balance_malus",10);
$balance_push=getsetting("DDL_balance_push",50);
$balance_lose=getsetting("DDL_balance_lose",-10);
$order=getsetting("DDL-order",2);

$order_act++;

if ($balance<=$balance_lose) // Schlecht gekämpft
{
  if ($order==1) // Verteidigung fehlgeschlagen
  {
    addnews_ddl("`4Der Feind hat unsere Linien durchbrochen!`&");
    addnews_ddl("`&Heutiger Tagesbefehl : Warten auf Weiteres!`&");
    $state--;
    savesetting("DDL-state",$state);
    if ($state<=1) // Niederlage ?
          {
             output('`4`n`nUnser Lager wurde zerstört!`n');
             addnews_ddl("`4Flieht um Euer Leben! Unser Lager wurde zerstört!`&");
          }
          savesetting("DDL-state",$state);
          savesetting("DDL_opps","0");
  }
  elseif ($order==3) // Angriff fehlgeschlagen
  {
    addnews_ddl("`@Unser Angriff kam zum Erliegen!`&");
    addnews_ddl("`&Heutiger Tagesbefehl : Warten auf Weiteres!`&");
  }
  savesetting("DDL_act_order","0");
  savesetting("DDL-balance","0");
}
if ($state>1 && $state<11)
{

if ($order_act>=$order_new) // Neue Tagesorder
{
  $order = getsetting("DDL-order",2);
  $chance=e_rand(1,4); 
  
  if ($order==2)
  {
    if ($balance>=$balance_push)
    { $chance++; } //Fleißiges Kämpfen ohne Tagesbefehl erhöht die Chance auf Angriff
    if ($balance<=($balance_lose*2))
    { $chance--; } //Faulheit ohne Tagesbefehl erhöht die Chance auf feindlichen Angriff
  }
  
  if ($order==1) // Auf Defensive folgt nie direkt Angriff
  { $chance--; }
  elseif ($order==3) // Vice Versa
  { $chance++; }

  if ($chance<=1) // Defensiv
  {
    addnews_ddl("`&Heutiger Tagesbefehl : `4Stellungen halten!`&");
    savesetting("DDL-order",1);
    $medalpoints=getsetting("DDL-medal",10);
     if ($medalpoints<35) $medalpoints++;
    savesetting("DDL-medal",$medalpoints);
  }
  elseif ($chance>=4) // Attacke
  {
    addnews_ddl("`&Heutiger Tagesbefehl : `^Angriff!`&");
    savesetting("DDL-order",3);
    $medalpoints=getsetting("DDL-medal",10);
    if ($medalpoints<35) $medalpoints++;
    savesetting("DDL-medal",$medalpoints);
  }
  else // Nix tun
  {
    addnews_ddl("`&Heutiger Tagesbefehl : Warten auf Weiteres!`&");
    savesetting("DDL-order",2);
  }
  $balance-=$balance_malus; // Tages-Malus
  savesetting("DDL_act_order","0");
  savesetting("DDL-balance","0");
}
else //Alter Order bleibt
{
  $balance-=$balance_malus; // Tages-Malus
  savesetting("DDL_act_order",$order_act);
  savesetting("DDL-balance",$balance);
}

}
}
else // Count-Down für Neustart
{
 $to_restart=getsetting("DDL-restart",18);
 $days_passed=getsetting("DDL-days","0");
 if ($days_passed>=$to_restart) // Reset
 {
   if ($state<=1) addnews_ddl("`2Heute wurde ein neues Lager errichtet!`&");
   if ($state>=11) addnews_ddl("`2Heute hat der Feind ein neues Lager errichtet!`&");
   savesetting("DDL_act_order","0");
   savesetting("DDL-balance","0");
   savesetting("DDL-order",2);
   savesetting("DDL-state",6);
   savesetting("DDL-days","0");
   savesetting("DDL-medal","10");
 }
 else // Counter erhöhen
 {
   $days_passed++;
   savesetting("DDL-days",$days_passed);
 }
}

?>
