<?php
// 08092004
/*
* Author:    anpera
* Email:        logd@anpera.de
*
* Purpose:    Houses for storing gold and gems and for a save place to sleep (logout)
*
* Features:    Build house, sell house, buy house, share house with others, private chat-area, PvP
*
* Every warrior can have his own house. He can build it with his own hands or buy one that was sold (or left) before.
* In a house he can store some of his gems and gold and houses are the savest place for log out.
* The player can give keys to other players. So he is able to share his gems and gold for example
* with his wife or he can make up a clan house. A player can only have one house but unlimited keys.
* Each house has its own private chat area.
* Other players can rob a house if they beat the guard and all players that are sleeping in the house.
*
*
* SEE  INSTRUCTIONS  FOR  INSTALLATION  AT  http://www.anpera.net/forum/viewtopic.php?t=323
* English translation available at DragonPrime
*
* Added furniture 05/25/2004
*  (Buy at vendor - vendor.php)
* Added Durandil's hidden path 05/30/2004
*
* Ok, lets do the code...
*/
/**
Ausbau-Erweiterung (1. Stufe):
Für bestimmte Kosten (upgold, upgems) kann ein Haus erweitert werden.
Anwesen -> größerer Schatz und mehr Schlüssel
Festung -> bessere Verteidigung bei Angriffen (Änderung des Hausbuffs in Battle.php!)
Gildenhaus -> neue Anwendungen, wenn Alte aufgebraucht sind
Bauernhof -> immer wieder das Tier füttern
Gruft -> Gefallen bei Ramius
Kerker -> Aktuelle Gefangene quälen
Trainingslager -> Trainingsmöglichkeit
Kloster -> Barmherzige Schwestern heilen alle Wunden
Versteck -> (fast)Unangreifbarkeit
Bordell -> Für Spass und gute Laune
**/
/*******************************************/
// Privatraum-Mod by talion
//
//
/*******************************************/
// Ausgliederung der Schlüssel in die Tabelle keylist by Maris
// 07.08.05 Update auf neue Haustypen by Maris
// 09.01.06 Ausgliederung der Chaträume in "inside_houses.php" by Maris
// 10.03.06 Ausgliederung des PvPs / Einbruchs in "houses_pvp.php" by talion

require_once("common.php");

checkday();
is_new_day();

// base values for pricing and chest size:
$goldmax=15000;
$gemmax=50;
$goldcost=30000;
$gemcost=50;
// all other values are controlled by banksettings


	// Diebe können Nachrichten oder Erkennungszeichen im Haus hinterlassen
	if ($_GET[op]=="nachricht")
	{
		$msg = $_POST[msg];
		if ($msg!="")
		{
			$sql="INSERT INTO commentary (postdate,section,author,comment) VALUES (now(),'house-".$_GET[id]."',".$session[user][acctid].",'`@$msg`V')";
			db_query($sql) or die(db_error(LINK));
		}
	}
	page_header("Das Wohnviertel");
	if ($_GET[op]=="newday")
	{
		if ($session['user']['imprisoned']>0)
		{
			redirect("prison.php");
		}
		$hausnr=$_GET[nr];
		// Ermittlung des Haustyps künftig über "statush"
		// XXX
		
		// RP-Wiedererweckung
		if($_GET['getit']) {
			$sql = "SELECT hadnewday FROM account_extra_info WHERE acctid=".$session['user']['acctid'];
			$resultnd = db_query($sql) or die(db_error(LINK));
			$rownd = db_fetch_assoc($resultnd);
			if ($rownd['hadnewday']==0)
			{
				$nd=1;
			}
			else
			{
				$nd=0;
			}
		}
		else {
			$nd = 0;
		}
		
		output(wakeupinhouse($_GET[statush],$nd));
		$sql = "UPDATE account_extra_info SET hadnewday=1 WHERE acctid = ".$session['user']['acctid'];
		db_query($sql) or die(sql_error($sql));
		$session['user']['restatlocation']=0;
		$sql = "UPDATE accounts SET restatlocation=".$session['user']['restatlocation']." WHERE acctid = ".$session[user][acctid];
		db_query($sql) or die(sql_error($sql));
		// output($_GET[statush]);
		$session[user][location]=0;
		$sql = "UPDATE keylist SET hvalue=0 WHERE hvalue>0 AND owner=".$session[user][acctid]."";
		db_query($sql) or die(sql_error($sql));
		addnav("Tägliche News","news.php");
		addnav("Zurück ins Dorf","village.php");
		addnav("Zurück ins Haus","inside_houses.php?id=$hausnr");
	}
	else if ($_GET[op]=="bio")
	{
		if (!$_GET[id])
		{
			redirect("houses.php");
		}
		$sql="SELECT houses.*,accounts.name AS besitzer FROM houses LEFT JOIN accounts ON accounts.acctid=houses.owner WHERE houseid=$_GET[id]";
		$result = db_query($sql) or die(db_error(LINK));
		$row = db_fetch_assoc($result);
		output("`c`b`@Infos über Haus Nummer $row[houseid]`b`c`n`n`2Du näherst dich Haus Nummer $row[houseid], um es aus der Nähe zu betrachten. ");
		if ($row[description])
		{
			output("Über dem Eingang  von $row[housename]`2 steht geschrieben:`n`& $row[description]`n`n");
		}
		else
		{
			output("Das Haus trägt den Namen \"`&$row[housename]`2\".`n");
		}
		$properties = ' deposit>0 AND deposit_show=1 AND deposit1='.$row['houseid'].' AND deposit2=0 ';
		$extra = '  ORDER BY gems DESC, gold DESC, id ASC ';
		$result = item_list_get($properties , $extra );
		if ($row[besitzer]=="")
		{
			$row[besitzer]="niemandem";
		}
		output("`2Das Haus gehört `^$row[besitzer]`2 und ist ");
		output(get_house_state($row['status'],true));
		output("`2`n`nDu riskierst einen Blick durch eines der Fenster");
		if (($row[status]!=15) && ($row[status]!=26) && ($row[status]!=37))
		{
			$maxcount=db_num_rows($result);
			if ($maxcount>0)
			{
				output(" und erkennst ");
				for ($i=0; $i<$maxcount; $i++)
				{
					$row2 = db_fetch_assoc($result);
					output("`@$row2[name]");
					if ($i+1<$maxcount)
					{
						output(", ");
					}
				}
			}
			else
			{
				output(" und siehst, dass das Haus sonst nichts weiter zu bieten hat.");
			}
			output(".`n");
		}
		else
		{
			output(" und siehst, dass alle Fensteröffnungen mit dicken Brettern vernagelt wurden.");
		}
		$pvptime_houses = getsetting("pvptimeout_houses",900);
		$pvptimeout_houses = date("Y-m-d H:i:s",strtotime(date("r")."-$pvptime_houses seconds"));
		if ($row[pvpflag_houses]>$pvptimeout_houses)
		{
			output("`4Du erkennst Einbruchsspuren an diesem Haus. Vermutlich gibt es dort nicht mehr viel zu holen.`0`n");
		}
		output("`n`n`@Während du dir das Haus genau ansiehst, fällt dir ein kleiner Trampelpfad auf...");
		addnav("Trampelpfad","paths.php?ziel=forestlake");
		if ($_GET[id]==$session[user][housekey])
		{
			addnav("Haus betreten","inside_houses.php?id=$_GET[id]");
		}
		addnav("Zurück","houses.php");
	}
	else if ($_GET[op]=="build")
	{
		if ($_GET[act]=="start")
		{
			$newhouses = getsetting("newhouses",true);
			$max_houses = getsetting("maxhouses",300);
			$sql = 'SELECT COUNT(houseid) AS c FROM houses h';
			$res = db_query($sql);
			$anzahl = db_fetch_assoc($res);
			if (!$newhouses || $anzahl['c'] >= $max_houses)
			{
				output("Der Mann vom Grundstücksamt schaut dich betroffen an und erklärt dir wortreich, dass alle ".$max_houses." Grundstücke bereits bebaut sind.`n");
				output("Du wirst dir wohl einen Schlüssel zu einem bereits bestehenden Haus besorgen oder ein Haus kaufen müssen.");
			}
			else
			{
				$sql = "INSERT INTO houses (owner,status,gold,gems,housename) VALUES (".$session[user][acctid].",0,0,0,'".$session[user][login]."s Haus')";
				db_query($sql) or die(db_error(LINK));
				if (db_affected_rows(LINK)<=0)
				{
					redirect("houses.php");
				}
				$sql = "SELECT * FROM houses WHERE status=0 AND owner=".$session[user][acctid]." ORDER BY houseid DESC";
				$result = db_query($sql) or die(db_error(LINK));
				$row = db_fetch_assoc($result);
				$session[user][house]=$row[houseid];
				output("`@Du erklärst das Fleckchen Erde zu deinem Besitz und kannst mit dem Bau von Hausnummer `^$row[houseid]`@ beginnen.`n`n");
				output("`0<form action=\"houses.php?op=build&act=build2\" method='POST'>",true);
				output("`nGebe einen Namen für dein Haus ein: <input name='housename' maxlength='25'>`n",true);
				output("`nWieviel Gold anzahlen? <input type='gold' name='gold'>`n",true);
				output("`nWieviele Edelsteine? <input type='gems' name='gems'>`n",true);
				output("<input type='submit' class='button' value='Bauen'>",true);
				addnav("","houses.php?op=build&act=build2");
			}
		}
		else if ($_GET[act]=="build2")
		{
			$sql = "SELECT * FROM houses WHERE status=0 AND owner=".$session[user][acctid]." ORDER BY houseid DESC";
			$result = db_query($sql) or die(db_error(LINK));
			$row = db_fetch_assoc($result);
			$paidgold=(int)$_POST['gold'];
			if ($_POST['housename']>"")
			{
				$housename=stripslashes($_POST['housename']);
			}
			else
			{
				$housename=stripslashes($row[housename]);
			}
			$paidgems=(int)$_POST['gems'];
			if ($session[user][gold]<$paidgold || $session[user][gems]<$paidgems)
			{
				output("`@Du hast nicht genug dabei!");
				addnav("Nochmal","houses.php?op=build");
			}
			else if ($session[user][turns]<1)
			{
				output("`@Du bist zu müde, um heute noch an deinem Haus zu arbeiten!");
			}
			else if ($paidgold<0 || $paidgems<0)
			{
				output("`@Versuch hier besser nicht zu beschummeln.");
			}
			else
			{
				output("`@Du baust für `^$paidgold`@ Gold und `#$paidgems`@ Edelsteine an deinem Haus \"`&$housename`@\"...`n");
				$row[gold]+=$paidgold;
				$session[user][gold]-=$paidgold;
				output("`nDu verlierst einen Waldkampf.");
				$session[user][turns]--;
				if ($row[gold]>$goldcost)
				{
					output("`nDu hast die kompletten Goldkosten bezahlt und bekommst das überschüssige Gold zurück.");
					$session[user][gold]+=$row[gold]-$goldcost;
					$row[gold]=$goldcost;
				}
				$row[gems]+=$paidgems;
				$session[user][gems]-=$paidgems;
				if ($row[gems]>$gemcost)
				{
					output("`nDu hast die kompletten Edelsteinkosten bezahlt und bekommst überschüssige Edelsteine zurück.");
					$session[user][gems]+=$row[gems]-$gemcost;
					$row[gems]=$gemcost;
				}
				$goldtopay=$goldcost-$row[gold];
				$gemstopay=$gemcost-$row[gems];
				$done=round(100-((100*$goldtopay/$goldcost)+(100*$gemstopay/$gemcost))/2);
				output("`nDein Haus ist damit zu `$$done%`@ fertig. Du musst noch `^$goldtopay`@ Gold und `#$gemstopay `@Edelsteine bezahlen, bis du einziehen kannst.");
				if ($row[gems]>=$gemcost && $row[gold]>=$goldcost)
				{
					output("`n`n`bGlückwunsch!`b Dein Haus ist fertig. Du bekommst `b10`b Schlüssel überreicht, von denen du 9 an andere weitergeben kannst, und besitzt nun deine eigene kleine Burg.");
					$row[gems]=0;
					$row[gold]=0;
					$session[user][housekey]=$row[houseid];
					$row[status]=1;
					addnews("`2".$session[user][name]."`3 hat das Haus `2$row[housename]`3 fertiggestellt.");
					addhistory("`3Hat das Haus `2$row[housename]`3 fertiggestellt.");
					//$sql="";
					for ($i=1; $i<10; $i++)
					{
						$sql = "INSERT INTO keylist (owner,value1,value2,gold,gems,description) VALUES (".$session[user][acctid].",$row[houseid],$i,0,0,'Schlüssel für Haus Nummer $row[houseid]')";
						db_query($sql);
						if (db_affected_rows(LINK)<=0)
						{
							output("`$Fehler`^: Dein Inventar konnte nicht aktualisiert werden! Bitte benachrichtige den Admin. ");
						}
					}
				}
				$sql = "UPDATE houses SET gold=$row[gold],gems=$row[gems],housename='".addslashes($housename)."',status=".(int)$row[status]." WHERE houseid=$row[houseid]";
				db_query($sql);
			}
		}
		else
		{
			if ($session[user][housekey]>0)
			{
				output("`@Du hast bereits Zugang zu einem fertigen Haus und brauchst kein zweites. Wenn du ein neues oder ein eigenes Haus bauen willst, musst du erst aus deinem jetzigen Zuhause ausziehen.");
			}
			else if ($session['user']['dragonkills']<getsetting('housegetdks',1) )
			{
				output("`@Du hast noch nicht genug Erfahrung, um ein eigenes Haus bauen zu können. Du kannst aber bei einem Freund einziehen, wenn er dir einen Schlüssel für sein Haus gibt.");
			}
			else if ($session[user][turns]<1)
			{
				output("`@Du bist zu erschöpft, um heute noch irgendetwas zu bauen. Warte bis morgen.");
			}
			else if ($session[user][house]>0)
			{
				$sql = "SELECT * FROM houses WHERE status=0 AND owner=".$session[user][acctid]." ORDER BY houseid DESC";
				$result = db_query($sql) or die(db_error(LINK));
				$row = db_fetch_assoc($result);
				output("`@Du besichtigst die Baustelle deines neuen Hauses mit der Hausnummer `3$row[houseid]`@.`n`n");
				$goldtopay=$goldcost-$row[gold];
				$gemstopay=$gemcost-$row[gems];
				$done=round(100-((100*$goldtopay/$goldcost)+(100*$gemstopay/$gemcost))/2);
				output(grafbar(100,$done,"100%",20),true);
				output("`nEs ist zu `$$done%`@ fertig. Du musst noch `^$goldtopay`@ Gold und `#$gemstopay `@Edelsteine bezahlen.`nWillst du jetzt weiter bauen?`n`n");
				output("`0<form action=\"houses.php?op=build&act=build2\" method='POST'>",true);
				output("`nWieviel Gold zahlen? <input type='gold' name='gold'>`n",true);
				output("`nWieviele Edelsteine? <input type='gems' name='gems'>`n",true);
				output("<input type='submit' class='button' value='Bauen'>",true);
				addnav("","houses.php?op=build&act=build2");
			}
			else
			{
				output("`@Du siehst ein schönes Fleckchen für ein Haus und überlegst dir, ob du nicht selbst eines bauen solltest, anstatt ein vorhandenes zu kaufen oder noch länger in Kneipe und Feldern zu übernachten.");
				output(" Ein Haus zu bauen würde dich `^$goldcost Gold`@ und `#$gemcost Edelsteine`@ kosten. Du mußt das nicht auf einmal bezahlen, sondern könntest immer wieder mal für einen kleineren Betrag ein Stück ");
				output("weiter bauen. Wie schnell du zu deinem Haus kommst, hängt also davon ab, wie oft und wieviel du bezahlst.`n");
				output("Du kannst in deinem zukünftigen Haus alleine wohnen, oder es mit anderen teilen. Es bietet einen sicheren Platz zum Übernachten und einen Lagerplatz für einen Teil deiner Reichtümer.");
				output(" Ein gestartetes Bauvorhaben kann nicht abgebrochen werden.`n`nWillst du mit dem Hausbau beginnen?");
				addnav("Hausbau beginnen","houses.php?op=build&act=start");
			}
		}
		addnav("Zurück zum Wohnviertel","houses.php");
		addnav("Zurück zum Dorf","village.php");
		addnav("Zurück zum Marktplatz","market.php");
	}
	else if ($_GET[op]=="buy")
	{
		if ($session['user']['dragonkills']<getsetting('housegetdks',1) )
		{
			output("`@Der Mann vom Amt lacht dich nur schallend aus und bittet dich wieder zu kommen wenn du groß bist.");
		}
		else if (!$_GET[id])
		{
			$ppp=10;
			// Player Per Page to display
			if (!$_GET[limit])
			{
				$page=0;
			}
			else
			{
				$page=(int)$_GET[limit];
				addnav("Vorherige Seite","houses.php?op=buy&limit=".($page-1)."");
			}
			$limit="".($page*$ppp).",".($ppp+1);
			// Häuser zum Verkauf
			// Ausgebaute nur abrufen, wenn DK-technisch für User erlaubt
			// (und in einstellungen aktiviert)
			$sql = "SELECT * FROM houses WHERE
status=2 OR status=3 OR status=4 ".
( $session['user']['dragonkills'] >= getsetting('houseextdks',10) &&
getsetting('houseextsellenabled',0) ?
"OR status=12 OR status=13 OR status=15 OR status=16
OR status=18 OR status=19 OR status=22 OR status=23 OR status=25 OR status=26 OR status=28
OR status=29 OR status=32 OR status=33 OR status=35 OR status=36 OR status=38 OR status=39
OR status=42 OR status=43 OR status=45 or status=46 OR status=48 OR status=49 OR status=52
OR status=53 OR status=55 OR status=56 OR status=58 OR status=59 OR status=62 OR status=63
OR status=65 or status=66 OR status=68 OR status=69 OR status=72 OR status=73 OR status=75
OR status=76 OR status=78 OR status=79 OR status=82 OR status=83 OR status=85 or status=86
OR status=88 OR status=89 OR status=92 OR status=93 OR status=95 OR status=96 OR status=98
OR status=99 OR status=102 OR status=103 OR status=105 or status=106 OR status=108
OR status=109 " : '' ).
" ORDER BY houseid ASC LIMIT $limit";
output("`c`b`^Unbewohnte Häuser`b`c`0`n");
output("<table cellspacing=0 cellpadding=2 align='center'><tr><td>`bHausNr.`b</td><td>`bName`b</td><td>`bStatus`b</td><td>`bGold`b</td><td>`bEdelsteine`b</td><td>`bBemerkung`b</td></tr>",true);
$result = db_query($sql) or die(db_error(LINK));
if (db_num_rows($result)>$ppp)
{
	addnav("Nächste Seite","houses.php?op=buy&limit=".($page+1)."");
}
if (db_num_rows($result)==0)
{
	output("<tr><td colspan=4 align='center'>`&`iEs stehen momentan keine Häuser zum Verkauf`i`0</td></tr>",true);
}
else
{
	for ($i=0; $i<db_num_rows($result); $i++)
	{
		$row = db_fetch_assoc($result);
		$bgcolor=($i%2==1?"trlight":"trdark");
		output("<tr class='$bgcolor'><td align='right'>$row[houseid]</td><td><a href='houses.php?op=buy&id=$row[houseid]'>$row[housename]</a></td><td>",true);
		output(get_house_state($row['status'],false),true);
		output("</td>",true);
		If ($row['owner'])
		{
			$basegold=0;
			$basegems=0;
		}
		else
		{
			If ($row['status']<10)
			{
				$basegold=30000;
				$basegems=30;
			}
			else
			{
				$basegold=100000;
				$basegems=100;
			}
		}
		$goldbuycost=$basegold+$row['gold'];
		$gemsbuycost=$basegems+$row['gems'];
		output("<td align='right'>$goldbuycost</td><td align='right'>$gemsbuycost</td><td>",true);
		if (($row[status]==3) or($row[status]==13) or($row[status]==16) or($row[status]==19) or($row[status]==23) or($row[status]==26) or($row[status]==29) or($row[status]==33) or($row[status]==36) or($row[status]==39) or($row[status]==43) or($row[status]==46) or($row[status]==49) or($row[status]==53) or($row[status]==56) or($row[status]==59) or($row[status]==63) or($row[status]==66) or($row[status]==69) or($row[status]==73) or($row[status]==76) or($row[status]==79) or($row[status]==83) or($row[status]==86) or($row[status]==89) or($row[status]==93) or($row[status]==96) or($row[status]==99) or($row[status]==103) or($row[status]==106) or($row[status]==109))
		{
			output("`4Verlassen`0");
		}
		else if ($row[status]==4)
		{
			output("`$Bauruine`0");
		}
        	else if ($row[status]==0 && $row[owner]==0)
		{
			output("`&Grundstück unbebaut`0");
		}
        else if ($row[owner]==0)
		{
			output("`^Maklerverkauf`0");
		}
		else
		{
			output("`6Privatverkauf`0");
		}
		output("</td></tr>",true);
		addnav("","houses.php?op=buy&id=$row[houseid]");
	}
}
output("</table>",true);
		}
		else
		{
			$sql = "SELECT * FROM houses WHERE houseid=".(int)$_GET[id]." ORDER BY houseid DESC";
			$result = db_query($sql) or die(db_error(LINK));
			$row = db_fetch_assoc($result);
			If ($row['owner'])
			{
				$basegold=0;
				$basegems=0;
			}
			else
			{
				If ($row['status']<10)
				{
					$basegold=30000;
					$basegems=30;
				}
				else
				{
					$basegold=100000;
					$basegems=100;
				}
			}
			$goldbuycost=$basegold+$row['gold'];
			$gemsbuycost=$basegems+$row['gems'];
			if ($session[user][acctid]==$row[owner])
			{
				output("`@du hängst doch zu sehr an deinem Haus und beschließt, es noch nicht zu verkaufen.");
				$session[user][housekey]=$row[houseid];
                buy_house($row[houseid],$row[status]);
			}
			else if ($session[user][gold]<$goldbuycost || $session[user][gems]<$gemsbuycost)
			{
				output("`@Dieses edle Haus übersteigt wohl deine finanziellen Mittel.");
			}
			else
			{
				output("`@Glückwunsch zu deinem neuen Haus!`n`n");
				addhistory("`3Hat das Haus `2$row[housename]`3 erworben.");
				$session[user][gold]-=$goldbuycost;
				$session[user][gems]-=$gemsbuycost;
				$session[user][house]=$row[houseid];
				output("Du übergibst `^$goldbuycost`@ Gold und `#$gemsbuycost`@ Edelsteine an den Verkäufer, und dieser händigt dir dafür einen Satz Schlüssel für Haus `b$row[houseid]`b aus.");
				if ($row[owner]>0)
				{
					$sql = "UPDATE accounts SET goldinbank=goldinbank+$goldbuycost,gems=gems+$gemsbuycost,house=0,housekey=0 WHERE acctid=$row[owner]";
					db_query($sql);
					systemmail($row[owner],"`@Haus verkauft!`0","`&{$session['user']['name']}
                    `2 hat dein Haus gekauft. Du bekommst `^$goldbuycost`2 Gold auf die Bank und `#$gemsbuycost`2!");
					$session[user][housekey]=$row[houseid];
				}
				
   	            $session[user][housekey]=$row[houseid];
                buy_house($row[houseid],$row[status]);
			}
		}
		addnav("W?Zurück zum Wohnviertel","houses.php");
		addnav("Zurück zum Dorf","village.php");
		addnav("Zurück zum Marktplatz","market.php");
	}
	else if ($_GET[op]=="sell")
	{
		$sql = "SELECT * FROM houses WHERE houseid=".$session[user][housekey]." ORDER BY houseid DESC";
		$result = db_query($sql) or die(db_error(LINK));
		$row = db_fetch_assoc($result);
		If ($row['status']<10)
		{
			$basegold=30000;
			$basegems=30;
		}
		else
		{
			$basegold=100000;
			$basegems=100;
		}
		$halfgold=round($goldcost/3);
		$halfgems=round($gemcost/3);
		if ($_GET[act]=="sold")
		{
			if (!$_POST[gold] && !$_POST[gems])
			{
				output("`@Du denkst ernsthaft darüber nach, dein Häuschen zu verkaufen. Wenn du selbst einen Preis festlegst, bedenke, daß er auf einmal bezahlt werden muss ");
				output(" und vom Käufer nicht in Raten abgezahlt werden kann. Außerdem kannst du weder ein neues Haus bauen, noch in diesem Haus wohnen, bis es verkauft ist.");
				output(" Du bekommst dein Geld erst, wenn das Haus verkauft ist. Der Verkauf läßt sich abbrechen, indem du selbst das Haus von dir kaufst.");
				output("`nWenn du sofort Geld sehen willst, musst du dein Haus für `^$basegold`@ Gold und `#$basegems`@ Edelsteine an einen Makler verkaufen.");
				output("`0<form action=\"houses.php?op=sell&act=sold\" method='POST'>",true);
				output("`nWieviel Gold willst du verlangen? <input type='gold' name='gold'>`n",true);
				output("`nWieviele Edelsteine soll das Haus kosten? <input type='gems' name='gems'>`n",true);
				output("<input type='submit' class='button' value='Anbieten'>",true);
				addnav("","houses.php?op=sell&act=sold");
				addnav("An den Makler","houses.php?op=sell&act=makler");
			}
			else
			{
				$halfgold=(int)$_POST[gold];
				$halfgems=(int)$_POST[gems];
				if (($halfgold<$basegold/20 && $halfgems<$basegems/5) || ($halfgold==0 && $halfgems<$basegems) || ($halfgold<$basegold && $halfgems==0))
				{
					output("`@Du solltest vielleicht erst deinen Ale-Rausch ausschlafen, bevor du über einen Preis nachdenkst. Wie? Du bist nüchtern? Das glaubt dir so kein Mensch.");
					addnav("Neuer Preis","houses.php?op=sell&act=sold");
				}
				else if ($halfgold>$basegold*2 || $halfgems>$basegems*4)
				{
					output("`@Bei so einem hohen Preis bist du dir nicht sicher, ob du wirklich verkaufen sollst. Überlege es dir nochmal.");
					addnav("Neuer Preis","houses.php?op=sell&act=sold");
				}
				else
				{
					output("`@Dein Haus steht ab sofort für `^$halfgold`@ Gold und `#$halfgems`@ Edelsteine zum Verkauf. Du und alle Mitbewohner habt den Schatz des Hauses gleichmäßig ");
					output(" unter euch aufgeteilt und deine Untermieter haben ihre Schlüssel abgegeben.");
					// Gold und Edelsteine an Bewohner verteilen und Schlüssel einziehen
					$sql = "SELECT owner FROM keylist WHERE value1=$row[houseid] AND owner<>$row[owner] ORDER BY id ASC";
					$result = db_query($sql) or die(db_error(LINK));
					$amt=db_num_rows($result);
					$goldgive=round($row[gold]/($amt+1));
					$gemsgive=round($row[gems]/($amt+1));
					$session[user][gold]+=$goldgive;
					$session[user][gems]+=$gemsgive;
					// $sql="";
					for ($i=0; $i<db_num_rows($result); $i++)
					{
						$item = db_fetch_assoc($result);
						$sql = "UPDATE accounts SET goldinbank=goldinbank+$goldgive,gems=gems+$gemsgive WHERE acctid=$item[owner]";
						db_query($sql);
						systemmail($item[owner],"`@Rauswurf!`0","`&{$session['user']['name']}
                        `2 hat das Haus `b$row[housename]`b`2 verkauft, in dem du als Untermieter gewohnt hast. Du bekommst `^$goldgive`2 Gold auf die Bank und `#$gemsgive`2 Edelsteine aus dem gemeinsamen Schatz ausbezahlt!");
					}
					$sql = "UPDATE keylist SET owner=$row[owner] WHERE value1=$row[houseid]";
					db_query($sql);
					// Variablen setzen und Datenbank updaten
					$row[gold]=$halfgold;
					$row[gems]=$halfgems;
					$session[user][housekey]=0;

					// Privatgemächer zurücksetzen
					// Einladungen in Privatgemächer löschen
					item_delete(' tpl_id="prive" AND value1='.$row['houseid']);
					// Privatgemächer löschen
					item_delete(' tpl_id="privb" AND value1='.$row['houseid']);
					// Möbel für Privatgemächer zurücksetzen
					item_set(' deposit1='.$row['houseid'], array('deposit1'=>0,'deposit2'=>0) );

                    sell_house($row[houseid],$row[status]);
					$sql = "UPDATE houses SET gold=$row[gold],gems=$row[gems] WHERE houseid=$row[houseid]";
					db_query($sql);
				}
			}
		}
		else if ($_GET[act]=="makler")
		{
			output("`@Dem Makler entfährt ungewollt ein freudiges Glucksen, als er dir `^$basegold`@ Gold und die `#$basegems`@ Edelsteine vorzählt.`n`n");
			output("Ab sofort steht dein Haus zum Verkauf und du kannst ein neues bauen, woanders mit einziehen, oder ein anderes Haus kaufen.");
			// Gold und Edelsteine an Bewohner verteilen und Schlüssel einziehen
			$sql = "SELECT owner FROM keylist WHERE value1=$row[houseid] AND owner<>$row[owner] ORDER BY id ASC";
			$result = db_query($sql) or die(db_error(LINK));
			$goldgive=round($row[gold]/(db_num_rows($result)+1));
			$gemsgive=round($row[gems]/(db_num_rows($result)+1));
			$session[user][gold]+=$goldgive;
			$session[user][gems]+=$gemsgive;
			//$sql="";
			for ($i=0; $i<db_num_rows($result); $i++)
			{
				$item = db_fetch_assoc($result);
				$sql = "UPDATE accounts SET goldinbank=goldinbank+$goldgive,gems=gems+$gemsgive WHERE acctid=$item[owner]";
				db_query($sql);
				systemmail($item[owner],"`@Rauswurf!`0","`&{$session['user']['name']}
                `2 hat das Haus `b$row[housename]`b`2 verkauft, in dem du als Untermieter gewohnt hast. Du bekommst `^$goldgive`2 Gold auf die Bank und `#$gemsgive`2 Edelsteine aus dem gemeinsamen Schatz ausbezahlt!");
			}
			$sql = "UPDATE keylist SET owner=0 WHERE value1=$row[houseid]";
			db_query($sql);
			// Variablen setzen und Datenbank updaten
			$row[gold]=$goldcost-$halfgold;
			$row[gems]=$gemcost;
			$session[user][gold]+=$basegold;
			$session[user][gems]+=$basegems;
			$session[user][house]=0;
			$session[user][housekey]=0;
			//$session[user][donation]+=1;
			// Privatgemächer zurücksetzen
			// Einladungen in Privatgemächer löschen
			item_delete(' tpl_id="prive" AND value1='.$row['houseid']);
			// Privatgemächer löschen
			item_delete(' tpl_id="privb" AND value1='.$row['houseid']);
			// Möbel für Privatgemächer zurücksetzen
			item_set(' deposit1='.$row['houseid'], array('deposit1'=>0,'deposit2'=>0) );
            sell_house($row[houseid],$row[status],true);
            $sql = "UPDATE houses SET gold=$row[gold],gems=$row[gems] WHERE houseid=$row[houseid]";
			db_query($sql);
		}
		else
		{
			output("`@Gib einen Preis für dein Haus ein, oder lass einen Makler den Verkauf übernehmen. Der schmierige Makler würde dir sofort `^$basegold`@ Gold und `#$basegems`@ Edelsteine geben. ");
			output("Wenn du selbst verkaufst, kannst du vielleicht einen höheren Preis erzielen, musst aber auf dein Geld warten, bis jemand kauft.`nAlles, was sich noch im Haus befindet, wird ");
			output("gleichmässig unter allen Bewohnern aufgeteilt.`n`n");
			output("`0<form action=\"houses.php?op=sell&act=sold\" method='POST'>",true);
			output("`nWieviel Gold verlangen? <input type='gold' name='gold'>`n",true);
			output("`nWieviele Edelsteine? <input type='gems' name='gems'>`n`n",true);
			output("<input type='submit' class='button' value='Für diesen Preis verkaufen'></form>",true);
			addnav("","houses.php?op=sell&act=sold");
			addnav("An den Makler","houses.php?op=sell&act=makler");
		}
		addnav("W?Zurück zum Wohnviertel","houses.php");
		addnav("Zurück zum Dorf","village.php");
		addnav("Zurück zum Marktplatz","market.php");
	}
	else if ($_GET[op]=="enter")
	{
			
		$show_invent = true;
	
		output("`c`b`@Du hast Zugang zu folgenden Häusern:`b`n`n");
		
		addnav('Haus betreten');
		
		$sql = "SELECT k.*,h.status,h.houseid,h.housename,a.acctid,a.name AS ownername FROM keylist k
				LEFT JOIN houses h ON h.houseid=k.value1
				LEFT JOIN accounts a ON a.acctid=h.owner
				WHERE k.owner=".$session['user']['acctid']." ORDER BY h.houseid ASC";
		$result = db_query($sql) or die(db_error(LINK));
		
		output("<table cellpadding=2 align='center'><tr><td>`bHausNr.`b</td><td>`bName`b</td><td>`bStatus`b</td><td>`bBesitzer`b</td></tr>",true);
		
		if ($session[user][house]>0 && $session[user][housekey]>0)
		{
								
			$sql = "SELECT houseid,housename,status FROM houses WHERE houseid=".$session['user']['house']." ORDER BY houseid DESC";
			$result2 = db_query($sql) or die(db_error(LINK));
			$row2 = db_fetch_assoc($result2);
			
			$str_lnk = 'inside_houses.php?id='.$row2['houseid'];
					
			addnav( strip_appoencode($row2['housename'],3), $str_lnk );
			
			output("<tr><td align='center'>$row2[houseid]</td><td colspan='3'>".create_lnk($row2['housename'],$str_lnk)." (dein eigenes)</td></tr>",true);
		}
		else if ($session[user][house]>0 && $session[user][housekey]==0)
		{
			output("<tr><td colspan=4 align='center'>`&`iDein Haus ist noch im Bau oder steht zum Verkauf`i`0</td></tr>",true);
		}
		
		$int_nr_of_houses = db_num_rows($result);
		
		if ($int_nr_of_houses==0)
		{
			output("<tr><td colspan=4 align='center'>`&`iDu hast keinen Schlüssel`i`0</td></tr>",true);
		}
		else
		{
			$rebuy=0;
			for ($i=0; $i<$int_nr_of_houses; $i++)
			{
				$item = db_fetch_assoc($result);
				if ($item[value1]==$session[user][house] && $session[user][housekey]==0)
				{
					$rebuy=1;
				}
				$bgcolor=($i%2==1?"trlight":"trdark");
				// Schleifenquery raus, wird oben durch Join erledigt.. talion
				/*$sql = "SELECT houseid,housename,status FROM houses WHERE houseid=$item[value1] ORDER BY houseid DESC";
				$result2 = db_query($sql) or die(db_error(LINK));
				$row2 = db_fetch_assoc($result2);*/
				if ($amt!=$item[value1] && $item[value1]!=$session[user][house])
				{
					$str_lnk = 'inside_houses.php?id='.$item['houseid'];
					
					addnav( strip_appoencode($item['housename'],3), $str_lnk );
										
					output("<tr class='$bgcolor'>
								<td align='center'>$item[houseid]</td>
								<td>".create_lnk($item['housename'],$str_lnk)."</td><td>",true);
					
					output(get_house_state($item[status],false));
					output("</td><td>".$item['ownername']."</td></tr>",true);
				}
				$amt=$item[value1];
			}
		}
		output("</table>`c",true);
		if ($rebuy==1)
		{
			addnav("Verkauf rückgängig","houses.php?op=buy&id=".$session[user][house]."");
		}
		if (getsetting("dailyspecial",0)=="Waldsee")
		{
			output("`n`n`@Während du deine Schlüssel suchst, fällt dir ein kleiner Trampelpfad auf...");
			addnav("Trampelpfad","paths.php?ziel=forestlake");
		}
		addnav('Zurück');
		addnav("Zum Dorf","village.php");
		addnav("Zum Marktplatz","market.php");
		addnav("W?Zum Wohnviertel","houses.php");
	}
	else
	{
		$show_invent = true;
	
		output("`@`b`cDas Wohnviertel`c`b`n`n");
		$session['housekey']=0;
		
		$bool_houseenter_nav = false;
		if($session['user']['housekey'] > 0) {
			$bool_houseenter_nav = true;
		}
		else {
			$sql = 'SELECT id FROM keylist WHERE owner='.$session['user']['acctid'].' ORDER BY id ASC LIMIT 1';
			if(db_num_rows(db_query($sql))) {
				$bool_houseenter_nav = true;
			}	
		}
		
		
		if ($bool_houseenter_nav)
		{
			addnav("Haus betreten","houses.php?op=enter");
		}
		
		// Privatraum-Mod
		addnav('Privatgemächer','houses_private.php?op=einladungen');
		// END Privatraum-Mod
		
		output("Du verlässt den Dorfplatz und schlenderst Richtung Wohnviertel. In diesem schön angelegten Teil des Dorfes siehst du einige Baustellen zwischen bewohnten ");
		output("und unbewohnten Häusern. Hier wohnen also die Helden...`n");
		
		if ($session['user']['housekey'])
		{
			output("`nStolz schwingst du den Schlüssel zu deinem eigenen Haus im Gehen hin und her.`n");
		}
		
		if ($_POST['search']>"")
		{
			if ($_GET['search']>"")
			{
				$_POST['search']=$_GET['search'];
			}
			if (strcspn($_POST['search'],"0123456789")<=1)
			{
				$search="houseid=".intval($_POST[search])." AND ";
			}
			else
			{
				$search="%";
				for ($x=0; $x<strlen($_POST['search']); $x++)
				{
					$search .= substr($_POST['search'],$x,1)."%";
				}
				$search="housename LIKE '".$search."' AND ";
			}
		}
		else
		{
			$search="";
		}
		$ppp=30;
		// Player Per Page +1 to display
		if (!$_GET[limit])
		{
			$page=0;
		}
		else
		{
			$page=(int)$_GET[limit];
			addnav("Vorherige Straße","houses.php?limit=".($page-1)."&search=$_POST[search]");
		}
		$limit="".($page*$ppp).",".($ppp+1);

		//DOC JOKUS/TWEANS: Performancekillenden Schleifenquery entsorgt
		$sql = "SELECT houses.*,accounts.name AS schluesselinhaber FROM houses
				LEFT JOIN accounts ON accounts.acctid=houses.owner
				WHERE $search status<120 ORDER BY houseid ASC LIMIT $limit";
				
		output("`n`c<form action='houses.php' method='POST'>Nach Hausname oder Nummer <input name='search' value='$_POST[search]'> <input type='submit' class='button' value='Suchen'></form>",true);
		addnav("","houses.php");
		output("<table cellpadding=2 cellspacing=1 bgcolor='#999999' align='center'><tr class='trhead'><td>`bHausNr.`b</td><td>`bName`b</td><td>`bEigentümer`b</td><td>`bStatus`b</td></tr>",true);
		$result = db_query($sql) or die(db_error(LINK));
		
		$int_count = db_num_rows($result);
		
		if ($int_count>$ppp)
		{
			addnav("Nächste Straße","houses.php?limit=".($page+1)."&search=$_POST[search]");
		}
		if ($int_count==0)
		{
			output("<tr><td colspan=4 align='center'>`&`iEs gibt noch keine Häuser`i`0</td></tr>",true);
		}
		else
		{
			for ($i=0; $i<$int_count; $i++)
			{
				$row = db_fetch_assoc($result);
				$bgcolor=($i%2==1?"trlight":"trdark");
				output("<tr class='$bgcolor'><td align='center'>$row[houseid]</td><td><a href='houses.php?op=bio&id=$row[houseid]'>$row[housename]</a></td><td>",true);
				addnav("","houses.php?op=bio&id=$row[houseid]");
				//DOC JOKUS/TWEANS: Performancekillenden Schleifenquery entsorgt
				output("$row[schluesselinhaber]</td><td>",true);
				output(get_house_state($row[status],false));
				output("</tr>",true);
			}
		}
		output("</table>`c",true);
		
		addnav("Aktionen");
		if ($session[user][house] && $session[user][housekey])
		{
			addnav("Haus verkaufen","houses.php?op=sell");
		}
		else
		{
			if (!$session[user][house] )
			{
				addnav("Haus kaufen","houses.php?op=buy");
			}
			addnav("Haus bauen","houses.php?op=build");
		}
		if (getsetting("pvp",1)==1)
		{
			if (($session['user']['profession']>0) && ($session['user']['profession']<3))
			{
				addnav("Razzia","houses_pvp.php?op=einbruch");
			}
			else
			{
				if (( ($session['user']['profession'] != PROF_TEMPLE_SERVANT) && ($session['user']['age'] <= getsetting('maxagepvp',50)) ) || (su_check(SU_RIGHT_DEBUG)))
				{
					addnav("Einbrechen","houses_pvp.php?op=einbruch");
				}
			}
		}
				
		addnav('Zurück');
		addnav("Zum Dorf","village.php");
		addnav("Zum Marktplatz","market.php");
		
		addnav("Sonderbare Orte");
		if (@file_exists("well.php"))
		{
			addnav("Dorfbrunnen","well.php");
		}
		
		$bool_admin = su_check(SU_RIGHT_COMMENT);
				
		if ($session['user']['race']==RACE_MENSCH || $session['user']['race']==RACE_AVATAR || $bool_admin)
		{
			addnav("Zum Versammlungsraum","racesspecial.php?race=".RACE_MENSCH);
		}
		if ($session['user']['race']==RACE_ZWERG || $session['user']['race']==RACE_AVATAR || $bool_admin)
		{
			addnav("Zur Zwergenbinge","racesspecial.php?race=".RACE_ZWERG);
		}
		if ($session['user']['race']==RACE_GOBLIN || $session['user']['race']==RACE_AVATAR || $bool_admin)
		{
			addnav("Zum Goblinbau","racesspecial.php?race=".RACE_GOBLIN);
		}
		if ($session['user']['race']==RACE_VAMPIR || $session['user']['race']==RACE_AVATAR || $bool_admin)
		{
			addnav("Zum Mausoleum","racesspecial.php?race=".RACE_VAMPIR);
		}
		if ($session['user']['race']==RACE_HALBLING || $session['user']['race']==RACE_AVATAR || $bool_admin)
		{
			addnav("Zu den Hügelhäusern","racesspecial.php?race=".RACE_HALBLING);
		}
		if ($session['user']['race']==RACE_SCHELM || $session['user']['race']==RACE_AVATAR || $bool_admin)
		{
			addnav("Zum Schelmenraum","racesspecial.php?race=".RACE_SCHELM);
		}
	}


page_footer();
?>
