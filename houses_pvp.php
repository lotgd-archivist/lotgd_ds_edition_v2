<?php
require_once("common.php");
addcommentary();
checkday();
is_new_day();

function check_guild()
{
	global $session;
	require_once(LIB_PATH.'dg_funcs.lib.php');
	$ids = '';
	if ($session['user']['guildid'] && $session['user']['guildfunc'] != DG_FUNC_APPLICANT)
	{
		$guild = &dg_load_guild($session['user']['guildid']);
		if (is_array($guild['treaties']))
		{
			foreach ($guild['treaties'] as $id=>$t)
			{
				if (dg_get_treaty($t)==1 )
				{
					// wenn Frieden mit dieser Gilde
					$ids .= ','.$id;
				}
			}
		}
	}
	return($ids);
}

page_header('Wohnviertel - Einbruch');

//Spieler verhaften
if ($_GET[op]=="catch")
{
	$bg=$_GET[bg];
	redirect("pvp.php?act=attack&bg=$bg&id=".$_GET[who]);
	addnav("Weiter","houses.php");
}
else if ($_GET[op]=="einbruch")
{
	if (!$_GET[id])
	{
		if ($_POST['search']>"" || $_GET['search']>"")
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
		$ppp=25;
		// Player Per Page to display
		if (!$_GET[limit])
		{
			$page=0;
		}
		else
		{
			$page=(int)$_GET[limit];
			addnav("Vorherige Strasse","houses_pvp.php?op=einbruch&limit=".($page-1)."&search=$_POST[search]");
		}
		$limit="".($page*$ppp).",".($ppp+1);
		$sql = "SELECT houses.*,accounts.name FROM houses 
				LEFT JOIN accounts ON houses.owner=accounts.acctid 
				WHERE $search (status>=1 AND (owner<>".$session[user][acctid].")) ORDER BY houseid ASC LIMIT $limit";
		if (($session['user']['profession']==0) || ($session['user']['profession']>2))
		{
			output("`c`b`^Einbruch`b`c`0`n");
			output("`@Du siehst dich um und suchst dir ein bewohntes Haus f�r einen Einbruch aus. ");
			output("Leider kannst du nicht erkennen, wieviele Bewohner sich gerade darin aufhalten und wie stark diese sind. So ein Einbruch ist also sehr riskant.`nF�r welches Haus entscheidest du dich?`n`n");
		}
		else
		{
			output("`c`b`^Razzia`b`c`0`n");
			output("`@Du siehst dich um und �berlegst in welchem Haus sich wohl die Unholde versteckt halten. ");
			output("Als Mitglied der Stadtwache wei�t du nat�rlich wer sich in welchem Haus aufh�lt und kannst die Ganoven gezielt festnehmen ohne unschuldige Bewohner zu gef�hrden. Allerdings wei�t du auch, dass sich die Spitzbuben nie unbesch�tzt irgendwo aufhalten, ein gewisses Risiko bleibt also.`nF�r welches Haus entscheidest du dich?`n`n");
		}
		output("<form action='houses_pvp.php?op=einbruch' method='POST'>Nach Hausname oder Nummer <input name='search' value='$_POST[search]'> <input type='submit' class='button' value='Suchen'></form>",true);
		addnav("","houses_pvp.php?op=einbruch");
		if ($session['user']['pvpflag']=="5013-10-06 00:42:00")
		{
			output("`n`&(Du hast PvP-Immunit�t gekauft. Diese verf�llt, wenn du jetzt angreifst!)`0`n`n");
		}
		output("<table cellpadding=2 cellspacing=1 bgcolor='#999999' align='center'><tr class='trhead'><td>`bHausNr.`b</td><td>`bName`b</td><td>`bEigent�mer`b</td><td>`bStatus`b</td></tr>",true);
		$result = db_query($sql) or die(db_error(LINK));
		if (db_num_rows($result)>$ppp)
		{
			addnav("N�chste Strasse","houses_pvp.php?op=einbruch&limit=".($page+1)."&search=$_POST[search]");
		}
		if (db_num_rows($result)==0)
		{
			output("<tr><td colspan=4 align='center'>`&`iEs gibt momentan keine bewohnten H�user`i`0</td></tr>",true);
		}
		else
		{
			$int_count = db_num_rows($result);
			for ($i=0; $i<$int_count; $i++)
			{
				$row = db_fetch_assoc($result);
				$bgcolor=($i%2==1?"trlight":"trdark");
				output("<tr class='$bgcolor'><td align='right'>$row[houseid]</td><td><a href='houses_pvp.php?op=einbruch&id=$row[houseid]'>$row[housename]</a></td><td>",true);
				output("$row[name]</td>",true);
				addnav("","houses_pvp.php?op=einbruch&id=$row[houseid]");
				output("$row[schluesselinhaber]</td><td>",true);
				output(get_house_state($row[status],false));
				output("</tr>",true);
			}
		}
		output("</table>",true);
		addnav("Umkehren","houses.php");
	}
	else
	{
		if ($session[user][turns]<1 || $session[user][playerfights]<=0)
		{
			if (($session['user']['profession']==0) || ($session['user']['profession']>2))
			{
				output("`nDu bist wirklich schon zu m�de, um ein Haus zu �berfallen.");
			}
			else
			{
				output("`nDu bist wirklich schon zu m�de, um noch jemanden festzunehmen.");
			}
			addnav("Zur�ck","houses.php");
		}
		else
		{
			output("`2Du n�herst dich vorsichtig Haus Nummer $_GET[id].");
			$session[housekey]=$_GET[id];
			// Abfrage, ob Schl�ssel vorhanden!!
			$sql = "SELECT id FROM keylist WHERE owner=".$session[user][acctid]." AND value1=".(int)$_GET[id]." ORDER BY id DESC";
			$result2 = db_query($sql) or die(db_error(LINK));
			$row2 = db_fetch_assoc($result2);
			if ((db_num_rows($result2)>0) && (($session['user']['profession']==0) || ($session['user']['profession']>2)))
			{
				output(" An der Haust�r angekommen suchst du etwas, um die T�r m�glichst unauff�llig zu �ffnen. Am besten d�rfte daf�r der Hausschl�ssel geeignet sein, ");
				output(" den du einstecken hast.`nWolltest du wirklich gerade in ein Haus einbrechen, f�r das du einen Schl�ssel hast?");
				addnav("Haus betreten","inside_houses.php?id=$_GET[id]");
				addnav("Zur�ck zum Dorf","village.php");
				addnav("Zur�ck zum Marktplatz","market.php");
			}
			else
			{
				$sql = "SELECT status,attacked FROM houses WHERE houseid=".$_GET[id]." ";
				$resultbg = db_query($sql) or die(db_error(LINK));
				$rowbg = db_fetch_assoc($resultbg);
				// Wache besiegen
				if ((($session['user']['profession']==0) || ($session['user']['profession']>2)) && (($rowbg['status']<30) || ($rowbg['status']>=40)))
				{
					output("Deine geb�ckte Haltung und der schleichende Gang machen eine Stadtwache aufmerksam...`n");
				}
				else
				{
					output("`@`nGerade willst du das Haus betreten und zur Tat schreiten als sich dir ein kleiner, kahlk�pfer Schl�ger in den Weg stellt. \"`3Du komms hier nich rein!`@\" knurrt er.`n");
				}
				$pvptime = getsetting("pvptimeout",600);
				$pvptimeout = date("Y-m-d H:i:s",strtotime(date("r")."-$pvptime seconds"));
				$days = getsetting("pvpimmunity", 5);
				$exp = getsetting("pvpminexp", 1500);
				$sql = "SELECT acctid,level,maxhitpoints,login,housekey FROM accounts WHERE
(restatlocation=".(int)$session[housekey].") AND 
(locked=0) AND
(alive=1 AND location=".USER_LOC_HOUSE.") AND
!(".user_get_online(0,0,true).") AND
(age > $days OR dragonkills > 0 OR pk > 0 OR experience > $exp) AND
(acctid <> ".$session[user][acctid].") AND
(pvpflag <> '5013-10-06 00:42:00') AND
(pvpflag < '$pvptimeout') ORDER BY maxhitpoints DESC";

				$result = db_query($sql) or die(db_error(LINK));
				$hp=0;
				$count=0;
				// count chars at home and find strongest
				if ((($session['user']['profession']==0) || ($session['user']['profession']>2)) && (($rowbg['status']<30) || ($rowbg['status']>=40)))
				{
					if (db_num_rows($result))
					{
						for ($i=0; $i<db_num_rows($result); $i++)
						{
							$row = db_fetch_assoc($result);
							
								if ($row[maxhitpoints]>$hp)
								{
									$hp=(int)$row[maxhitpoints];
									$count++;
								}
							
						
						}
					}
				}
				if ($count>0)
				{
					if (($hp)>= (max($session[user][maxhitpoints], $session[user][hitpoints])))
					{
						$hps=10;
					}
					else
					{
						$hps=abs($session[user][maxhitpoints]-$hp)+1;
					}
					if ((($session['user']['profession']==0) || ($session['user']['profession']>2)) && (($rowbg['status']<30) || ($rowbg['status']>=40)))
					{
						$badguy = array("creaturename"=>"Stadtwache","creaturelevel"=>$session[user][level],"creatureweapon"=>"Holzkn�ppel","creatureattack"=>$session[user][attack],"creaturedefense"=>$session[user][defence],"creaturehealth"=>$hps, "diddamage"=>0);
					}
					else
					{
						$hps=$session['user']['maxhitpoints']*0.6;
						$atk = $session['user']['attack'] * 0.8;
						$def = $session['user']['defence'] * 0.8;
						$badguy = array("creaturename"=>"Schl�ger","creaturelevel"=>$session[user][level],"creatureweapon"=>"Weinflasche","creatureattack"=>$atk,"creaturedefense"=>$def,"creaturehealth"=>$hps, "diddamage"=>0);
					}
				}
				else
				{
					if ((($session['user']['profession']==0) || ($session['user']['profession']>2)) && (($rowbg['status']<30) || ($rowbg['status']>=40)))
					{
						$hps=abs(max($session[user][maxhitpoints],$session[user][hitpoints]));
						$atk = $session['user']['attack'] + ($rowbg['attacked']*1);
						$def = $session['user']['defence'] + ($rowbg['attacked']*1);
						$badguy = array("creaturename"=>"Stadtwache","creaturelevel"=>$session[user][level],"creatureweapon"=>"starker Holzkn�ppel","creatureattack"=>$atk,"creaturedefense"=>$def,"creaturehealth"=>$hps, "diddamage"=>0);
						$session[user][reputation]-=7;
					}
					else
					{
						$hps = (int)(abs(max($session[user][maxhitpoints], $session[user][hitpoints])*0.5));
						$atk = $session['user']['attack'] * 0.75;
						$def = $session['user']['defence'] * 0.75;
						$badguy = array("creaturename"=>"Schl�ger","creaturelevel"=>$session[user][level],"creatureweapon"=>"Brett mit Nagel","creatureattack"=>$atk,"creaturedefense"=>$def,"creaturehealth"=>$hps, "diddamage"=>0);
					}
				}
				$session[user][badguy]=createstring($badguy);
				$fight=true;
			}
		}
	}
}
else if ($_GET['op'] == "fight")
{
	$fight=true;
}
else if ($_GET['op'] == "run")
{
	$badguy = createarray($session['user']['badguy']);
	// fight against guard
	if ($badguy['creaturename']=='Stadtwache')
	{
		output("`%Die Wache l�sst dich nicht entkommen!`n");
		$session[user][reputation]--;
	}
	// fight against pet
	else
	{
		output("`%".$badguy['creaturename']."`% l�sst dich nicht entkommen!`n");
	}
	$fight=true;
}
else if ($_GET[op]=="leave")
{
	$session[user][playerfights]--;
	redirect("village.php");
}
else if ($_GET[op]=="einbruch2")
{
	$badguy = createarray($session['user']['badguy']);
	$fightpet = false;
	// check for pet
	if ($badguy['creaturename']=='Stadtwache')
	{
		$sql = 'SELECT accounts.petid AS pet FROM accounts
WHERE accounts.house='.$session['housekey'].' AND accounts.petfeed > NOW()';
		$result = db_query($sql);
		if ($row = db_fetch_assoc($result))
		{
			if ($row['pet']>0)
			{
				$pet = item_get(' id="'.$row['pet'].'" ',true,'buff1,name');
				$petbuffs = item_get_buffs(ITEM_BUFF_PET,','.$pet['buff1']);
				$petbuff = $petbuffs[0];
				$badguy = array('creaturename'=>$pet['name'],
				'creaturelevel'=>$session['user']['level'],
				'creatureweapon'=>$petbuff['name'],
				'creatureattack'=>$petbuff['atkmod'],
				'creaturedefense'=>$petbuff['defmod'],
				'creaturehealth'=>$petbuff['regen'],
				'diddamage'=>0);
				$session['user']['badguy'] = createstring($badguy);
				$fight = $fightpet = true;
				output('`$Gerade willst du ins Haus gehen, als du hinter dir pl�tzlich ein Knurren vernimmst.`0`n');
			}
		}
	}
	if (!$fightpet)
	{
		// Versteck-Mod
		$sql = "SELECT status FROM houses WHERE houseid=".$_GET[id]." ";
		$resultbg = db_query($sql) or die(db_error(LINK));
		$rowbg = db_fetch_assoc($resultbg);
		if (
		($session['user']['profession']==0 || $session['user']['profession']>2)
		&& (($rowbg['status']>29 && $rowbg['status']<40 && e_rand(1,6)==4) || ($rowbg['status']<30 || $rowbg['status']>39))
		)
		{
									
			$pvptime = getsetting("pvptimeout",600);
			$pvptimeout = date("Y-m-d H:i:s",strtotime(date("r")."-$pvptime seconds"));
			$days = getsetting("pvpimmunity", 5);
			$exp = getsetting("pvpminexp", 1500);
			// Auf befreundete Gilden checken
			$ids = check_guild();
			if (strlen($ids) > 2 && !$_GET['ignore_guild'])
			{
				$sql = "SELECT COUNT(*) AS a
						FROM accounts
						WHERE
						(restatlocation=".(int)$session['housekey'].") AND
						(locked=0) AND
						(alive=1 AND location=".USER_LOC_HOUSE.") AND
						!(".user_get_online(0,0,true).") AND
						(age > $days OR dragonkills > 0 OR pk > 0 OR experience > $exp) AND
						(acctid <> ".$session[user][acctid].") AND
						(pvpflag <> '5013-10-06 00:42:00') AND
						(pvpflag < '$pvptimeout') AND
						(guildid IN (-1".$ids.") AND guildfunc!=".DG_FUNC_APPLICANT.")
						";
				$count = db_fetch_assoc(db_query($sql));

				if($count['a'] > 0) {
					output('`nDu entdeckst unter den Schlafenden, die im Notfall das Haus verteidigen w�rden, Angeh�rige befreundeter
							Gilden! Bist du dir sicher, dennoch angreifen zu wollen? ');
					addnav('Trotzdem angreifen!','houses_pvp.php?op=einbruch2&id='.$_GET['id'].'&ignore_guild=1');
					addnav('Umkehren','houses.php');
					page_footer();
					exit;
				}
			}
			// Spieler besiegen
			$sql = "SELECT 
			acctid,name,maxhitpoints,defence,attack,level,laston,loggedin,login,housekey
			FROM accounts
			WHERE
			(restatlocation=".(int)$session['housekey'].") AND
			(locked=0) AND
			(alive=1 AND location=".USER_LOC_HOUSE.") AND
			!(".user_get_online(0,0,true).") AND
			(age > $days OR dragonkills > 0 OR pk > 0 OR experience > $exp) AND
			(acctid <> ".$session[user][acctid].") AND
			(pvpflag <> '5013-10-06 00:42:00') AND
			(pvpflag < '$pvptimeout') ORDER BY maxhitpoints DESC
			";                
			$result = db_query($sql) or die(db_error(LINK));
			
			$athome=0;
			$name="";
			$hp=0;
			// count chars at home and find strongest
			for($i=0; $i<db_num_rows($result); $i++)
			{
				
				$row = db_fetch_assoc($result);
				
					$athome++;
					if ($row[maxhitpoints]>$hp)
					{
						$hp=$row[maxhitpoints];
						$name=$row[login];
					}
				
			}	// END Status
		}	// END keine Stadtwache / Gegner finden
		if (($athome>0) && (($session['user']['profession']==0) || ($session['user']['profession']>2)))
		{
			output("`n Dir kommen $athome misstrauische Bewohner schwer bewaffnet entgegen. Der wahrscheinlich St�rkste von ihnen wird sich jeden Augenblick auf dich st�rzen, ");
			output(" wenn du die Situation nicht sofort entsch�rfst.");
			if (($rowbg[status]>=20) && ($rowbg[status]<=23))
			{
				addnav("K�mpfe","pvp.php?act=attack&bg=3&name=".rawurlencode($name));
			}
			else if (($rowbg[status]>=24) && ($rowbg[status]<=26))
			{
				addnav("K�mpfe","pvp.php?act=attack&bg=4&name=".rawurlencode($name));
			}
			else if (($rowbg[status]>=27) && ($rowbg[status]<=29))
			{
				addnav("K�mpfe","pvp.php?act=attack&bg=5&name=".rawurlencode($name));
			}
			else
			{
				addnav("K�mpfe","pvp.php?act=attack&bg=2&name=".rawurlencode($name));
			}
			addnav("Fl�chte","houses.php");
		}
		else
		{
			//Spieler festnehmen
			if (($session['user']['profession']>0) && ($session['user']['profession']<3))
			{
				output("Folgende Personen halten sich gerade im Haus auf:`n`n");
				$athome=0;
				if ((($rowbg['status']>29) && ($rowbg['status']<40) && (e_rand(1,6)==6)) || (($rowbg['status']<30) || ($rowbg['status']>39)) )
				{
					output("<table border='0'><tr><td valign='top'>",true);
					$pvptime = getsetting("pvptimeout",600);
					$pvptimeout = date("Y-m-d H:i:s",strtotime(date("r")."-$pvptime seconds"));
					$days = getsetting("pvpimmunity", 5);
					$exp = getsetting("pvpminexp", 1500);
					$sql = "SELECT acctid,profession,bounty,name,defence,attack,level,laston,loggedin,login,housekey,guildid,guildfunc FROM accounts WHERE
(restatlocation=".(int)$session['housekey'].") AND
(locked=0) AND
(alive=1 AND location=".USER_LOC_HOUSE.") AND
(profession<>21) AND
(profession<>22) AND
!(".user_get_online(0,0,true).") AND
(age > $days OR dragonkills > 0 OR pk > 0 OR experience > $exp) AND
(acctid <> ".$session[user][acctid].")
ORDER BY bounty DESC, level DESC";
					$result = db_query($sql) or die(db_error(LINK));
					$athome=0;
					$name="";
					output("<table border='0' cellpadding='4' cellspacing='0'><tr><td>Name</td><td>Level</td><td></td><td>Kopfgeld</td></tr>",true);
					$lst=0;
					
					$int_count = db_num_rows($result);
					
					for ($i=0; $i<$int_count; $i++)
					{
						$row = db_fetch_assoc($result);
						
							$athome++;
							$lst+=1;
							output("<tr class='".($lst%2?"trlight":"trdark")."'><td><a href='houses_pvp.php?op=catch&bg=$bg&who=".$row['acctid']."'>$row[name]</a></td><td>$row[level]</td><td></td><td>$row[bounty]</td></tr>",true);
							addnav("","houses_pvp.php?op=catch&bg=$bg&who=".$row['acctid']);
						
					}
					
					output("</table>",true);
					output("</td><td valign='top'>",true);
					output("</td></tr></table>",true);
				}
				addnav("Zur�ck","houses.php");
				if ($athome==0)
				{
					output("`n`bWie �rgerlich! Das Haus ist leer. Da ist wohl jemand gerade noch entkommen.`b");
				}
			}
		}
		if ((($session['user']['profession']==0) || ($session['user']['profession']>2)) && ($athome==0))
		{
			$session[user][playerfights]--;
			output(" Du hast Gl�ck, denn es scheint niemand daheim zu sein. Das wird sicher ein Kinderspiel.");
			addnav("Einsteigen","houses_pvp.php?op=klauen&id=$session[housekey]");
			addnav("Fl�chten","houses.php");
		}
	}
}
else if ($_GET[op]=="klauen")
{
	if (!$_GET[id])
	{
		output("Und jetzt? Bitte benachrichtige den Admin. Ich wei� nicht, was ich jetzt tun soll...");
		addnav("Zur�ck zum Dorf","village.php");
		addnav("Zur�ck zum Marktplatz","market.php");
	}
	else
	{
		$sql = "SELECT * FROM houses WHERE houseid=".$session[housekey]." ORDER BY houseid ASC";
		$result = db_query($sql) or die(db_error(LINK));
		$row = db_fetch_assoc($result);
		if (e_rand(1,3)==1 || su_check(SU_RIGHT_DEBUG))
		{
			$rowtrap = item_get(' tpl_id="trfal" AND deposit1='.$row['houseid'], false);
			if ($rowtrap['id'] )
			{
				$potion = ( $session['bufflist']['Antiserum']['rounds'] > 0 ? true : false);
				if (!$potion)
				{
					output("`4Als du das Truhenschloss knackst und mit beiden H�nden hineingreifst vernimmst du ein zischendes Ger�usch, gefolgt von einem stechenden Schmerz in deiner Hand.`nDu taumelst durch den Raum, als ein heftiges Kribbeln deinen ganzen K�rper erfasst und du langsam das Bewusstsein verlierst.`nDas war wohl eine Giftfalle!`nDU BIST TOT!`n");
					$session['user']['hitpoints']=0;
					addnews("`@".$session['user']['name']."`@ wei� nun, dass Truhen auch mit Fallen versehen sein k�nnen und wird im n�chsten Leben sicher daran denken.");
					$sql="INSERT INTO commentary (postdate,section,author,comment) VALUES (now(),'house-".$row[houseid]."',".$session[user][acctid].",'/me `4wurde heute vergiftet vor der Schatztruhe des Hauses aufgefunden.')";
					db_query($sql) or die(db_error(LINK));
					addnav("R�chelnd sterben","shades.php");
				}
				else
				{
					// Gesch�tzt durch Trank
					output('`4Als du das Truhenschloss knackst und mit beiden H�nden hineingreifst, vernimmst du ein zischendes Ger�usch, gefolgt von einem stechenden Schmerz in deiner Hand.`n
`@Das war wohl eine Giftfalle! Zum Gl�ck bist du durch ein Gegenmittel davor gesch�tzt und �berstehst den Anschlag knapp.. Puh.`n`n');
					$session['bufflist']['poison_potion']['rounds']--;
					//					if ($session['bufflist']['poison_potion']['rounds'] <= 0)
					{
						unset($session['bufflist']['poison_potion']);
					}
				}
				$item_change['hvalue'] = $rowtrap['hvalue'] - 1;
				$item_change['value2'] = (!$potion ? $rowtrap['value2'] + 1 : $rowtrap['value2']);
				item_set('id='.$rowtrap['id'],$item_change);
			}
		}
		if($session['user']['hitpoints']>0)  // Tote klauen nicht
		{
			$wasnu=e_rand(1,3);
			switch ($wasnu)
			{
				case 1:
					$getgems=0;
					$getgold=e_rand(0,round($row[gold]/ (10 + $row['attacked']) ));
					//Maximum
					if ($getgold>6000)
					{
						$getgold=6000 ;
					}
					$pvptime_houses = getsetting("pvptimeout_houses",900);
					$pvptimeout_houses = date("Y-m-d H:i:s",strtotime(date("r")."-$pvptime_houses seconds"));
					if ($row[pvpflag_houses]>$pvptimeout_houses)
					{
					(int)$getgold=round($getgems*0.1);
					(int)$getgems=round($getgold*0.5);
					}
					if (($getgold>0) || ($getgems>0))
					{
						$sql = "UPDATE houses SET attacked=attacked+1,gold=gold-$getgold,pvpflag_houses=now() WHERE houseid=$row[houseid]";
					}
					break;
				case 2:
					$getgems=e_rand(0,round($row[gems]/(10 + $row['attacked'])));
					//Maximum
					if ($getgems>5)
					{
						$getgems=5 ;
					}
					$getgold=e_rand(0,round($row[gold]/(10 + $row['attacked'])));
					//Maximum
					if ($getgold>6000)
					{
						$getgold=6000 ;
					}
					$pvptime_houses = getsetting("pvptimeout_houses",900);
					$pvptimeout_houses = date("Y-m-d H:i:s",strtotime(date("r")."-$pvptime_houses seconds"));
					if ($row[pvpflag_houses]>$pvptimeout_houses)
					{
					(int)$getgold=round($getgems*0.1);
					}
					if (($getgold>0) || ($getgems>0))
					{
						$sql = "UPDATE houses SET attacked=attacked+1,gold=gold-$getgold,gems=gems-$getgems,pvpflag_houses=now() WHERE houseid=$row[houseid]";
					}
					break;
				case 3:
					$getgems=e_rand(0,round($row[gems]/(10 + $row['attacked'])));
					//Maximum
					if ($getgems>5)
					{
						$getgems=5 ;
					}
					$getgold=0;
					$pvptime_houses = getsetting("pvptimeout_houses",900);
					$pvptimeout_houses = date("Y-m-d H:i:s",strtotime(date("r")."-$pvptime_houses seconds"));
					if ($row[pvpflag_houses]>$pvptimeout_houses)
					{
					(int)$getgems=round($getgold*0.5);
					}
					if (($getgold>0) || ($getgems>0))
					{
						$sql = "UPDATE houses SET attacked=attacked+1,gems=gems-$getgems,pvpflag_houses=now() WHERE houseid=$row[houseid]";
					}
					break;
			}
			db_query($sql) or die(db_error(LINK));
			$session[user][gold]+=$getgold;
			$session[user][gems]+=$getgems;
			// �nderungen im Einbruchsystem, Dieb wird nicht leicht erkannt und kann Nachrichten im Haus hinterlassen
			$pvptime_houses = getsetting("pvptimeout_houses",900);
			$pvptimeout_houses = date("Y-m-d H:i:s",strtotime(date("r")."-$pvptime_houses seconds"));
			if ($row[pvpflag_houses]>$pvptimeout_houses)
			{
				output("`3Du bemerkst, dass hier vor Kurzem schon jemand vor dir t�tig war. Dementsprechend gering f�llt daher auch deine Beute aus!`n");
			}
			output("`@Es gelingt dir, `^$getgold `@Gold und  `#$getgems `@Edelsteine aus dem Schatz zu klauen!");
			$add_str = '';
			if ($_GET['hidden'])
			{
				$add_str = '`nMit einem schnellen Blick stellst du fest, dass scheinbar alle Wachen in tiefem Schlummer liegen und der Dieb deshalb ungehindert seinem Werk nachgehen konnte!';
			}
			switch (e_rand(1,4))
			{
				case 1:
					systemmail($row[owner],"`\$Einbruch!`0","Jemand ist in dein Haus eingebrochen und hat `^$getgold`\$ Gold und `#$getgems`\$ Edelsteine erbeutet!".$add_str);
					break;
				case 2:
					addnews("`6".$session[user][name]."`6 erbeutet `#$getgems`6 Edelsteine und `^$getgold`6 Gold bei einem Einbruch!");
					addcrimes("`6".$session[user][name]."`6 erbeutet `#$getgems`6 Edelsteine und `^$getgold`6 Gold bei einem Einbruch!");
					systemmail($row[owner],"`\$Einbruch!`0","`\${$session['user']['name']}
				`\$ ist in dein Haus eingebrochen und hat `^$getgold`\$ Gold und `#$getgems`\$ Edelsteine erbeutet!".$add_str);
					break;
				case 3:
					systemmail($row[owner],"`\$Einbruch!`0","Jemand ist in dein Haus eingebrochen und hat `^$getgold`\$ Gold und `#$getgems`\$ Edelsteine erbeutet!".$add_str);
					break;
				case 4:
					systemmail($row[owner],"`\$Einbruch!`0","Jemand ist in dein Haus eingebrochen und hat `^$getgold`\$ Gold und `#$getgems`\$ Edelsteine erbeutet!".$add_str);
					break;
			}
			output("Wenn du willst kannst du deinen Opfern eine Nachricht hinterlassen. Damit w�rdest du dich jedoch mit Sicherheit zu erkennen geben.`n");
			output("Sende ein leeres Feld wenn du nichts hinterlassen m�chtest.`n`n");
			output("<form action='houses.php?op=nachricht&id=$row[houseid]' method='POST'><input name='msg' id='msg'><input type='submit' class='button' value='Senden'></form>",true);
			output("<script language='JavaScript'>document.getElementById('msg').focus();</script>",true);
			addnav("","houses.php?op=nachricht&id=$row[houseid]");
			// �nderungen(Einbruchsystem)Ende
			addnav("Zur�ck zum Dorf","village.php");
		}
	}
}
else if ($_GET[op]=="fight")
{
	$battle=true;
}
else if ($_GET[op]=="run")
{
	output("`$Dein Stolz verbietet es dir, vor diesem Kampf wegzulaufen!`0");
	$_GET[op]="fight";
	$battle=true;
}
if ($fight)
{
	if (count($session[bufflist])>0 && is_array($session[bufflist]) || $_GET[skill]!="")
	{
		$_GET[skill]="";
		if ($_GET['skill']=="")
		{
			$session['user']['buffbackup']=serialize($session['bufflist']);
		}
		$session[bufflist]=array();
		output("`&Die ungewohnte Umgebung verhindert den Einsatz deiner besonderen F�higkeiten!`0");
	}
	include "battle.php";
	if ($victory)
	{
		addnav("Weiter zum Haus","houses_pvp.php?op=einbruch2&id=$session[housekey]");
		addnav("Zur�ck zum Dorf","houses.php?op=leave");
		addnav("Zur�ck zum Marktplatz","market.php");
		// check for pet
		if ($badguy['creaturename']=='Stadtwache')
		{
			output("`n`#Du hast die Stadtwache besiegt und der Weg zum Haus ist frei!`nDu bekommst ein paar Erfahrungspunkte.");
			$session['user']['experience'] += $session['user']['level']*10;
			$session['user']['turns']--;
		}
		else
		{
			output('`n`#'.$badguy['creaturename'].'`# zieht sich zur�ck und gibt den Weg zum Haus frei!');
		}
		$badguy=array();
	}
	else if ($defeat)
	{
		if ($badguy['creaturename']=='Stadtwache')
		{
			$chance=e_rand(1,2);
			//Bonus f�r Diebe
			if (($session['user']['specialty']==3) && ($chance==1))
			{
				output("`n`$Die Stadtwache hat dich besiegt. Doch als Dieb weist du dich zu erretten und stellst dich tot. Als die Wache geschockt einen Moment unaufmerksam wird rennst du schnell weg.");
				$session['user']['hitpoints']=1;
				addnav("Weiter","village.php");
				addnews("`%".$session[user][name]."`3 wurde von der Stadtwache bei einem Einbruch gestellt, konnte aber entkommen.");
			}
			else
			{
				if (($session['user']['profession']!=21) && ($session['user']['profession']!=22))
				{
					output("`n`$Die Stadtwache hat dich besiegt und nimmt dich fest. Du wirst wegen versuchten Einbruchs f�r 2 Tage in den Kerker geworfen!");
					$session['user']['hitpoints']=$session['user']['maxhitpoints'];
					$session['user']['imprisoned']=2;
					$session[user][badguy]="";
					addnav("Weiter","prison.php");
					addnews("`%".$session[user][name]."`3 wurde von der Stadtwache bei einem Einbruch festgenommen und in den Kerker geworfen.");
				}
				else
				{
					output("`n`$Die Stadtwache hat dich besiegt. Durch deine richterliche Immunit�t bleibt dir der Kerker erspart.");
					addnews("`%Richter ".$session[user][name]."`3 wurde von der Stadtwache bei einem Einbruch gefasst und entging dank der Immunit�t dem Kerker.");
					addcrimes("`%Richter ".$session[user][name]."`3 wurde von der Stadtwache bei einem Einbruch gefasst und entging dank der Immunit�t dem Kerker.");
					$session['user']['hitpoints']=1;
					addnav("Weiter","village.php");
				}
			}
		}
		else
		{
			output('`n`$'.$badguy['creaturename'].'`$ hat dich besiegt. Du liegst schwer verletzt am Boden!`nDu verlierst 3 Charmepunkte und fast alle Lebenspunkte.');
			$session['user']['hitpoints'] = 1;
			$session['user']['charm'] -= 3;
			addnews("`%".$session['user']['name']."`3 stie� bei einem Einbruch auf unerwartete Gegenwehr und verletzte sich schwer.");
			addnav('Davonkriechen',"houses.php?op=leave");
		}
	}
	else
	{
		fightnav(false,true);
	}
}

page_footer();
?>