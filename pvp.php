<?php

// 15082004

require_once "common.php";
require_once(LIB_PATH.'dg_funcs.lib.php');

$pvptime = getsetting("pvptimeout",600);
$pvptimeout = date("Y-m-d H:i:s",strtotime(date("r")."-$pvptime seconds"));
// $pvptimeout = date("Y-m-d H:i:s",strtotime("-$pvptime seconds"));
page_header("Spielerkampf!");
if ($HTTP_GET_VARS[op]=="" && $HTTP_GET_VARS[act]!="attack"){
	//if ($session['user']['age']<=5 && $session['user']['dragonkills']==0){
	//  output("`\$Warning!`^ Players are immune from Player vs Player (PvP) combat for their first 5 days in the game.  If you choose to attack another player, you will lose this immunity!`n`n");
	//}
	checkday();
	pvpwarning();
  	output("`4Du machst dich auf in die Felder, wo einige unwissende Krieger schlafen.`n`nDu hast noch `^".$session[user][playerfights]."`4 PvP Kämpfe übrig für heute.");
	if($session['user']['age'] > getsetting('maxagepvp',50) ) {
		output("`nDu spürst allerdings instinktiv, dass es wohl besser wäre, sich erst um die Drachenplage zu kümmern.");	
	}
	elseif($session['user']['profession'] == PROF_TEMPLE_SERVANT ) {
		output("`nAls Tempeldiener kehrst du jedoch besser gleich wieder um..");	
	}
	else {
		addnav("Krieger auflisten","pvp.php?op=list");
	}
	addnav("Zurück");
  	addnav("Zum Dorfplatz","village.php");
  	addnav("Zum Marktplatz","market.php");
}else if ($HTTP_GET_VARS[op]=="list"){
	checkday();
	pvpwarning();
	$days = getsetting("pvpimmunity", 5);
	$exp = getsetting("pvpminexp", 1500);

if (($session['user']['profession']==0) || ($session['user']['profession']>2)) {
$sql = "SELECT accounts.name,alive,race,location,sex,level,laston,loggedin,login,pvpflag,acctid,g.name AS guildname,accounts.guildid,accounts.guildfunc FROM accounts LEFT JOIN dg_guilds g ON (g.guildid=accounts.guildid AND guildfunc!=".DG_FUNC_APPLICANT.") WHERE
	(locked=0) AND 
	(age > $days OR dragonkills > 0 OR pk > 0 OR experience > $exp) AND
	(level >= ".($session[user][level]-1)." AND level <= ".($session[user][level]+2).") AND 
	(alive=1 AND location=".USER_LOC_FIELDS.") AND
	(race>0 AND specialty>0) AND
	(dragonkills >= ".($session[user][dragonkills]-5).") AND
	!(".user_get_online(0,0,true).") AND
	(acctid <> ".$session[user][acctid].")
	ORDER BY level DESC";
	//echo ("<pre>$sql</pre>");
    }
else {

$sql = "SELECT accounts.name,alive,race,location,profession,sex,level,laston,loggedin,login,pvpflag,acctid,g.name AS guildname,accounts.guildid,accounts.guildfunc FROM accounts LEFT JOIN dg_guilds g ON (g.guildid=accounts.guildid AND guildfunc!=".DG_FUNC_APPLICANT.") WHERE
	(locked=0) AND
	(age > $days OR dragonkills > 0 OR pk > 0 OR experience > $exp) AND
	(profession <> 21 AND profession <>22 ) AND
	(level >= ".($session[user][level]-1)." AND level <= ".($session[user][level]+2).") AND
	(alive=1 AND location=".USER_LOC_FIELDS.") AND
	(race>0 AND specialty>0) AND
	!(".user_get_online(0,0,true).") AND
	(acctid <> ".$session[user][acctid].")
	ORDER BY level DESC";
}

  	$result = db_query($sql) or die(db_error(LINK));
	if ($session['user']['pvpflag']=="5013-10-06 00:42:00"){
		output("`n`&Du hast PvP-Immunität gekauft. Diese verfällt, wenn du jetzt angreifst!`0`n`n");
	}
	
	if($session['user']['guildid']) {
		
		$guild = &dg_load_guild($session['user']['guildid'],array('treaties'));
		
	}
	
	output("`c<table bgcolor='#999999' border='0' cellpadding='3' cellspacing='0'><tr class='trhead'><td>Name</td><td>Level</td><td>Gilde</td><td>Ops</td></tr>",true);
	
	$count = db_num_rows($result);
	
	if($count == 0) {
		output('<tr><td colspan="4" class="trlight">`iLeider erblickst du niemanden, der für dich in Frage käme!`0`i</td></tr>',true);
	}	
	
	for ($i=0;$i<$count;$i++){
		$row = db_fetch_assoc($result);
		
		$row['guildname'] = ($row['guildname']) ? $row['guildname'] : ' - ';

$sql2 = "SELECT acctid,sentence FROM account_extra_info WHERE acctid=".$row['acctid']."";
$result2 = db_query($sql2) or die(db_error(LINK));
$row2 = db_fetch_assoc($result2);
		
	  	$biolink="bio.php?char=".rawurlencode($row[login])."&ret=".urlencode($_SERVER['REQUEST_URI']);
	  	addnav("", $biolink);
		$state_str = '';
		if($row['guildid'] && $guild) {
			$state = dg_get_treaty($guild['treaties'][$row['guildid']]);
			if($state==1) {
				$state_str = ' `@(befreundet)';	
			}
			elseif($state==-1) {
				$state_str = ' `4(Feind)';	
			}
		}
		output("<tr class='".($i%2?"trlight":"trdark")."'><td>$row[name]</td><td>$row[level]</td><td>".$row['guildname'].$state_str."</td><td>[ <a href='$biolink'>Bio</a> |",true); 

        if ((($row[pvpflag]>$pvptimeout) && (($session['user']['profession']==0) || ($session['user']['profession']>2))) || (($session['user']['guildid']>0) && ($session['user']['guildid'] == $row['guildid']))){

              output("`iimmun`i ]</td></tr>",true);
		}else{
		  	output("<a href='pvp.php?act=attack&id=".$row['acctid']."'>Angriff</a> ]</td></tr>",true);
			addnav("","pvp.php?act=attack&id=".$row['acctid']);
		}
	}
	output("</table>`c",true);
	addnav("Krieger auflisten","pvp.php?op=list");
  	addnav("Zurück zum Dorf","village.php");
   	if (getsetting("hasegg",0)>0){
		$sql = "SELECT name FROM accounts WHERE acctid = ".getsetting("hasegg",0);
		$result = db_query($sql) or die(db_error(LINK));
		$row = db_fetch_assoc($result);
		output("`n`n$row[name] hat das goldene Ei!");
  	}
	
} else if ($HTTP_GET_VARS[act] == "attack") {
  	$sql = "SELECT name AS creaturename,
	 level AS creaturelevel,
	 weapon AS creatureweapon,
	 gold AS creaturegold,
	 experience AS creatureexp,
	 maxhitpoints AS creaturehealth,
	 attack AS creatureattack,
	 defence AS creaturedefense,
	 bounty AS creaturebounty,
	 loggedin,
	 location,
	 dragonkills,
	 laston,
	 alive,
	 race,
	 acctid,
	 lastip,
	 emailaddress,
	 pvpflag,
	 uniqueid,
	 guildid,
	 guildfunc  
	 FROM accounts
	 WHERE ";
	 $sql .= ($_GET['name']) ? " login='".$_GET['name']."'" : " acctid=".$_GET['id'];
	 
	$result = db_query($sql) or die(db_error(LINK));
	if (db_num_rows($result)>0){
		$row = db_fetch_assoc($result);
		
$sql2 = "SELECT acctid,sentence FROM account_extra_info WHERE acctid=".$row['acctid']."";
$result2 = db_query($sql2) or die(db_error(LINK));
$row2 = db_fetch_assoc($result2);

		if (abs($session[user][level]-$row[creaturelevel])>2 && $row[location]!=USER_LOC_HOUSE){
		  	output("`\$Fehler:`4 Dieser Spieler ist nicht in deinem Levelbereich!");
		}elseif (($row[pvpflag] > $pvptimeout) && (($session['user']['profession']==0) || ($session['user']['profession']>2))) {
			output("`\$Uuuups:`4 Dieser Krieger ist gerade anderweitig ... beschäftigt. Du wirst etwas auf deine Chance warten müssen! $row[pvpflag] : $pvptimeout");
		}elseif ((($session['user'][dragonkills] > $row[dragonkills]+5) && ($row[location]!=USER_LOC_HOUSE)) && (($session['user']['profession']==0) || ($session['user']['profession']>2))) {

        	output("`\$Mööööp:`4 Dieser Gegner ist unter deiner Würde!");
//		} elseif ($session['user']['lastip'] == $row['lastip'] || ($session[user][emailaddress]==$row[emailaddress] && $row[emailaddress])){
		} elseif (ac_check($row)){
			output("`\$`bNicht schummeln!!`b Du darfst deinen eigenen Charakter nicht angreifen!"); 
		}else{
		  	if (strtotime($row[laston]) > strtotime(date("r")."-".getsetting("LOGINTIMEOUT",900)." sec") && $row[loggedin]){
//		  	if (strtotime($row[laston]) > strtotime("-".getsetting("LOGINTIMEOUT",900)." sec") && $row[loggedin]){
			  	output("`\$Fehler:`4 Dieser Krieger ist inzwischen online.");
			}else{
			  	if ((int)$row[location]!=0 && 0 && $row[location]!=USER_LOC_HOUSE){
				  	output("`\$ Fehler:`4 Dieser Krieger befindet sich nicht an einem Ort, wo du ihn angreifen kannst.");
				}else{
				  	if((int)$row[alive]!=1){
					  	output("`\$Fehler:`4 Dieser Krieger lebt nicht.");
					}else{

if (($session['user']['profession']==0) || ($session['user']['profession']>2)) {

                          if ($session[user][playerfights]>0){
							$sql = "UPDATE accounts SET pvpflag=now() WHERE acctid=$row[acctid]";
							db_query($sql);
							$battle=true;
							$row[pvp]=1;
							$row[creatureexp] = round($row[creatureexp],0);
							$row[playerstarthp] = $session[user][hitpoints];
							$session[user][badguy]=createstring($row);
							$session[user][playerfights]--;
							$session['user']['buffbackup']="";
							if ($session['user']['pvpflag']=="5013-10-06 00:42:00"){
								$session['user']['pvpflag']="1986-10-06 00:42:00";
								output("`n`4`bDeine Immunität ist hiermit verfallen!`b`0`n");
							}
							pvpwarning(true);
							if (!$session['user']['prefs']['nosounds']) output("<embed src=\"media/bigbong.wav\" width=10 height=10 autostart=true loop=false hidden=true volume=100>",true);
						}else{
						  	output("`4Du bist zu müde, um heute einen weiteren Kampf mit einem Krieger zu riskieren.");
						}
						


					}
else {
  if ($session[user][playerfights]>0){
				//			$sql = "UPDATE accounts SET pvpflag=now() WHERE acctid=$row[acctid]";
				//			db_query($sql);
							$battle=true;
							$row[pvp]=1;
							$row[creatureexp] = round($row[creatureexp],0);
							$row[playerstarthp] = $session[user][hitpoints];
							$session[user][badguy]=createstring($row);
							$session[user][playerfights]--;
							$session['user']['buffbackup']="";
							if ($session['user']['pvpflag']=="5013-10-06 00:42:00"){
								$session['user']['pvpflag']="1986-10-06 00:42:00";
								output("`n`4`bDeine Immunität ist hiermit verfallen!`b`0`n");
							}
							pvpwarning(true);
							if (!$session['user']['prefs']['nosounds']) output("<embed src=\"media/bigbong.wav\" width=10 height=10 autostart=true loop=false hidden=true volume=100>",true);
						}else{
						  	output("`4Du bist zu müde, um heute einen weiteren Kampf mit einem Krieger zu riskieren.");
						}



					}
}
				}
			}
		}
	}else{
	  	output("`\$Fehler:`4 Dieser Krieger wurde nicht gefunden. Darf ich fragen, wie du überhaupt hier her gekommen bist?");
	}
  	if ($battle){
	  
	}else{
	  	addnav("Zurück zum Dorf","village.php");
	}
}
if ($HTTP_GET_VARS[op]=="run"){
  	output("Deine Ehre verbietet es dir wegzulaufen.");
	$HTTP_GET_VARS[op]="fight";
}
if ($HTTP_GET_VARS[skill]!=""){
  	output("Deine Ehre verbietet es dir, deine besonderen Fähigkeiten einzusetzen.");
	$HTTP_GET_VARS[skill]="";
}
if ($HTTP_GET_VARS[op]=="fight" || $HTTP_GET_VARS[op]=="run"){
	$battle=true;
}
if ($battle){
  	include("battle.php");
	if ($victory){
		//$badguy[creaturegold]=e_rand(0,$badguy[creaturegold]);
		$exp = round(getsetting("pvpattgain",10)*$badguy[creatureexp]/100,0);
		$expbonus = round(($exp * (1+.1*($badguy[creaturelevel]-$session[user][level]))) - $exp,0);
		output("`b`&$badguy[creaturelose]`0`b`n"); 
		if (($session['user']['profession']==1) || ($session['user']['profession']==2))  {
			output("`b`\$Du hast $badguy[creaturename] festgenommen und in den Kerker gesperrt!`0`b`n");

			$sql2 = "SELECT acctid,sentence FROM account_extra_info WHERE acctid=".$badguy['acctid']."";
			$result2 = db_query($sql2) or die(db_error(LINK));
			$row2 = db_fetch_assoc($result2);

			if ($row2['sentence']==0) {
				addcrimes("`^HINWEIS`3 : `3Stadtwache `4".$session['user']['name']."`3 nimmt `4{$badguy['creaturename']}`$ ohne Haftbefehl `3fest.`n`&(Verfahren nur bei mehrfacher Wiederholung eröffnen!)");
			}

		}
		else { 
			output("`b`\$Du hast $badguy[creaturename] besiegt!`0`b`n");
			output("`#Du erbeutest `^$badguy[creaturegold]`# Gold!`n");

			//Rassenzähler
			
			$victim=$races[$badguy['race']];
			$killer=$races[$session['user']['race']];
			$victimtotal=$victim."total";
			$total = getsetting($victimtotal,0);
			$total+=1;
			savesetting($victimtotal,$total);
			
			$whobywhom=$victim."by".$killer;
			$wbwtotal = getsetting($whobywhom,0);
			$wbwtotal+=1;
			savesetting($whobywhom,$wbwtotal);
			
			//Rassenzähler Ende
			
		}

		// Bounty Check - Darrell Morrone
		if ($badguy[creaturebounty]>0){
		   	output("`#Außerdem erhältst du das Kopfgeld in Höhe von `^$badguy[creaturebounty]`# Gold!`n");
			$session[user][reputation]+=2;
            $bountyrew=1;
        }
	    	// End Bounty Check - Darrell Morrone
		if ($expbonus>0){
		  	output("`#*** Durch die hohe Schwierigkeit des Kampfes erhältst du zusätzlich `^$expbonus`# Erfahrungspunkte!`n");
			$session[user][reputation]++;
		}else if ($expbonus<0){
		  	output("`#*** Weil dieser Kampf so leicht war, verlierst du `^".abs($expbonus)."`# Erfahrungspunkte!`n");
			$session[user][reputation]--;
		}
		output("Du bekommst insgesamt `^".($exp+$expbonus)."`# Erfahrungspunkte!`n`0");
		// start: xp-loss for killing lowdk players
		$xplossfactor = 0;
		$mindks = getsetting("pvpmindkxploss",10);
		$dksdiff = $session['user']['dragonkills'] - $badguy['dragonkills'];
		if ($dksdiff>$mindks){
			$xplossfactor = 1 - (($badguy['dragonkills'] + 3) / ($session['user']['dragonkills']));
if (($session['user']['profession']==0) || ($session['user']['profession']>2)) { $session[user][reputation]--;}
			$loss = round(($exp+$expbonus) * $xplossfactor);
			output("`#Davon werden dir `\$$loss `#Erfahrungspunkte abgezogen, weil dein Gegner $dksdiff Drachenkills weniger als du hat.");
		}
		
		
		// GILDENMOD
		if($session['user']['guildid'] && $session['user']['guildfunc'] != DG_FUNC_APPLICANT) {
			
			$our_guild = &dg_load_guild($session['user']['guildid'],array('hitlist'));
			
			// Gildenkopfgeld:
			if($our_guild['hitlist'][$badguy['acctid']]) {
				$bounty = dg_hitlist_remove($session['user']['guildid'],$badguy['acctid']);
				output('`n`n`8Da '.$badguy['creaturename'].'`8 auf der Kofpgeldliste deiner Gilde stand, erhältst du `^'.$bounty.'`8 Gold als Belohnung!');
			}
		
			if($badguy['guildid'] && $badguy['guildfunc'] != DG_FUNC_APPLICANT && 
			($session['user']['profession'] != PROF_GUARD && $session['user']['profession'] != PROF_GUARD_HEAD) ) {
			
				output(dg_pvp_kill($badguy,1));		
			
			}
						
		}
		// END GILDENMOD
						
		
		// end: xp-loss for killing lowdk players
		if (($session['user']['profession']==0) || ($session['user']['profession']>2)) {
				$session['user']['gold']+=$badguy['creaturegold'];
				if ($badguy['creaturegold']) {
					debuglog("gained {$badguy['creaturegold']} gold for killing ", $badguy['acctid']);
				} }
		// Add Bounty Gold - Darrell Morrone
		$session['user']['gold']+=$badguy['creaturebounty'];
		if ($badguy['creaturebounty']) {
			//debuglog("gained {$badguy['creaturebounty']} gold bounty for killing ", $badguy['acctid']);
		}
		$session['user']['experience']+=($exp+$expbonus-$loss);
		
		$badguy_news = addslashes('`4'.$badguy['creaturename'].'`3 hat dank der Stadtwache '.$session['user']['name'].'`3 eine gerechte Strafe erhalten!');
			
		if ($badguy['location']==USER_LOC_INN){
           if (($session['user']['profession']==0) || ($session['user']['profession']>2)) {
              addnews("`4".$session['user']['name']."`3 besiegt `4{$badguy['creaturename']}`3 brutal in einem Zimmer in der Kneipe!");
              if ($bountyrew!=1) {addcrimes("`4".$session['user']['name']."`3 besiegt `4{$badguy['creaturename']}`3 brutal in einem Zimmer in der Kneipe!"); }
			$killedin="`6der Kneipe";
			$session[user][reputation]-=2; } else {
            //addnews("`4".$session['user']['name']."`3, im Dienste der Stadtwache, hat `4{$badguy['creaturename']}`3 in der Kneipe festgenommen!");
			  
			$sql = "INSERT INTO news SET newstext='".$badguy_news."',newsdate=NOW(),accountid=".$badguy['acctid'];
			
			db_query($sql) or die (db_error($link));
			  
			$killedin="`6der Kneipe";
              }
		} else if ($badguy['location']==USER_LOC_HOUSE){
              if (($session['user']['profession']==0) || ($session['user']['profession']>2)) {
		  	addnews("`4".$session['user']['name']."`3 besiegt `4{$badguy['creaturename']}`3 bei einem Einbruch ins Haus!");
		  	if ($bountyrew!=1) {addcrimes("`4".$session['user']['name']."`3 besiegt `4{$badguy['creaturename']}`3 bei einem Einbruch ins Haus!"); }
			$killedin="`6`2einem Haus";
			$session[user][reputation]-=5; } else {
            //addnews("`4".$session['user']['name']."`3, im Dienste der Stadtwache, hat `4{$badguy['creaturename']}`3 in einem Haus gestellt und festgenommen!");
			
			$sql = "INSERT INTO news SET newstext='".$badguy_news."',newsdate=NOW(),accountid=".$badguy['acctid'];
			db_query($sql) or die (db_error($link));
			
			$killedin="`6`2einem Haus";
             }
		}else{
              if (($session['user']['profession']==0) || ($session['user']['profession']>2)) {
		  	addnews("`4".$session['user']['name']."`3 besiegt `4{$badguy['creaturename']}`3 in einem Kampf in den Feldern.");
		  	if ($bountyrew!=1) {addcrimes("`4".$session['user']['name']."`3 besiegt `4{$badguy['creaturename']}`3 in einem Kampf in den Feldern."); }
			$killedin="`@den Feldern";
			
			$session[user][reputation]--;  } else {
            //  addnews("`4".$session['user']['name']."`3, im Dienste der Stadtwache, hat `4{$badguy['creaturename']}`3 in den Feldern festgenommen!");
			
			$sql = "INSERT INTO news SET newstext='".$badguy_news."',newsdate=NOW(),accountid=".$badguy['acctid'];
			db_query($sql) or die (db_error($link));
			  
			$killedin="`@den Feldern";
          }
		}
		// Add Bounty Kill to the News - Darrell Mororne
		if ($badguy['creaturebounty']>0){
            if (($session['user']['profession']==0) || ($session['user']['profession']>2)) {

                addnews("`4".$session['user']['name']."`3 verdient `4{$badguy['creaturebounty']} Gold`3 für den Kopf von `4{$badguy['creaturename']}`3!");
			$session[user][reputation]++;

	
    	}  else {
addnews("`4".$session['user']['name']."`3 erhält `4{$badguy['creaturebounty']} Gold`3 als Lohn für die Ergreifung von `4{$badguy['creaturename']}`3!");
			$session[user][reputation]++; }
  }

		if (($session['user']['profession']==0) || ($session['user']['profession']>2)) {
 
			// Items verlieren / gewinnen, PvP-Gewinn
			$item_hook_info ['min_chance'] = e_rand( 1,100 );
			$item_hook_info ['loose_str'] = '';
			$item_hook_info ['win_str'] = '';
			$item_hook_info ['badguy'] = $badguy;
			
			$res = item_list_get( ' owner='.$badguy['acctid'].' AND pvp_victory_hook != "" ' );
			
			while ( $i = db_fetch_assoc ( $res ) ) 
			{
				
				item_load_hook ( $i['pvp_victory_hook'] , 'pvp_victory' , $i );	
				
			}
			
			output ( '`n' . $item_hook_info['win_str'] );
			// END Item Hook
			
			$sql = "SELECT gold FROM accounts WHERE acctid='".(int)$badguy['acctid']."'";
			$result = db_query($sql);
			$row = db_fetch_assoc($result);
			$badguy[creaturegold]=((int)$row[gold]>(int)$badguy[creaturegold]?(int)$badguy[creaturegold]:(int)$row[gold]); 
							
		
		}
		//$sql = "UPDATE accounts SET alive=0, killedin='$killedin', goldinbank=goldinbank-IF(gold<$badguy[creaturegold],gold-$badguy[creaturegold],0),gold=gold-$badguy[creaturegold], experience=experience*.95, slainby=\"".addslashes($session[user][name])."\" WHERE acctid=$badguy[acctid]";
		// \/- Gunnar Kreitz
		$lostexp = round($badguy['creatureexp']*getsetting("pvpdeflose",5)/100,0);
		// start: xp-loss for killing lowdk players
		$lostexp -= round($lostexp*$xplossfactor,0);
		// end: xp-loss for killing lowdk players
		
		// Stats
		user_set_stats( array('pvpkilled'=>'pvpkilled+1'), $badguy['acctid'] );
		user_set_stats( array('pvpkills'=>'pvpkills+1') );
		// END Stats		

		 if (($session['user']['profession']==0) || ($session['user']['profession']>2)) {
				$mailmessage = "`^".$session['user']['name']."`2 hat dich mit %p `^".$session['user']['weapon']."`2 in $killedin`2 angegriffen und gewonnen!"
						." `n`n%o hatte anfangs `^".$badguy['playerstarthp']."`2 Lebenspunkte und kurz bevor du gestorben bist, hatte %o noch `^".$session['user']['hitpoints']."`2 Lebenspunkte übrig."
						." `n`nDu hast `\$".(round(getsetting("pvpdeflose",5)-$xplossfactor*getsetting("pvpdeflose",5)))."%`2 deiner Erfahrungspunkte (etwa $lostexp Punkte) und `^".$badguy[creaturegold]."`2 Gold verloren. Dein Angreifer kassierte ausserdem das Kopfgeld in Höhe von `^".$badguy[creaturebounty]." `2Gold ein."
						. $item_hook_info['loose_str']
						." `n`nGlaubst du nicht auch, es ist Zeit dich zu rächen?";
				$mailmessage = str_replace("%p",($session['user']['sex']?"ihre(r/m)":"seine(r/m)"),$mailmessage);
				$mailmessage = str_replace("%o",($session['user']['sex']?"sie":"er"),$mailmessage);
				systemmail($badguy['acctid'],"`2Du wurdest in $killedin`2 umgebracht",$mailmessage); 
				// /\- Gunnar Kreitz
		} else
		{
				$mailmessage = "`^".$session['user']['name']."`2 hat dich mit %p `^".$session['user']['weapon']."`2 in $killedin`2 gestellt und festgenommen!"
						." `n`n%o hatte anfangs `^".$badguy['playerstarthp']."`2 Lebenspunkte und kurz bevor du gestorben bist, hatte %o noch `^".$session['user']['hitpoints']."`2 Lebenspunkte übrig."
						." `n`n Dein Angreifer kassierte `^".$badguy[creaturebounty]." `2Gold für deine Festnahme. Nun sitzt du im Kerker!"
						." `n`nGlaubst du nicht auch, es ist Zeit dich zu rächen?";
				$mailmessage = str_replace("%p",($session['user']['sex']?"ihre(r/m)":"seine(r/m)"),$mailmessage);
				$mailmessage = str_replace("%o",($session['user']['sex']?"sie":"er"),$mailmessage);
				systemmail($badguy['acctid'],"`2Du wurdest in $killedin`2 festgenommen!",$mailmessage);
		}

		if (($session['user']['profession']==0) || ($session['user']['profession']>2)) {
			$sql = "UPDATE accounts SET alive=0, bounty=0, goldinbank=goldinbank-IF(gold<$badguy[creaturegold],gold-$badguy[creaturegold],0),gold=gold-$badguy[creaturegold], experience=experience-$lostexp WHERE acctid=".(int)$badguy[acctid]."";		
			db_query($sql); 
		}
		else {
          	if ($row2['sentence']==0) {$sentence=2;} else { $sentence=$row2['sentence'];}
			$sql = "UPDATE accounts SET alive=1, bounty=0, location=".USER_LOC_PRISON.", imprisoned=$sentence, restatlocation=0 WHERE acctid=".(int)$badguy[acctid]."";
			db_query($sql);
		
			$sql = "UPDATE account_extra_info SET sentence=0 WHERE acctid=".(int)$badguy[acctid]."";
			db_query($sql);
		
			$sql = "UPDATE keylist SET hvalue=0 WHERE hvalue>0 AND owner=".$badguy[acctid]."";
			db_query($sql) or die(sql_error($sql));

		  }
		
		$HTTP_GET_VARS[op]="";
		if ($badguy['location']==USER_LOC_INN){
			addnav("Zurück zur Kneipe","inn.php");
		} else if ($badguy['location']==USER_LOC_HOUSE){
			addnav("Zurück zum Wohnviertel","houses_pvp.php?op=einbruch");
		} else {
			addnav("Zurück zum Dorf","village.php");
		}
		//Trophäensammler
		$resextra = db_query("SELECT trophyhunter FROM account_extra_info WHERE acctid=".$session['user']['acctid']);
		$rowextra = db_fetch_assoc($resextra);
		
		if ($rowextra['trophyhunter']==1)
		{
		  if (($session['user']['profession']==0) || ($session['user']['profession']>2)) {
		Output("`n`n`^Du überlegst dir, ob du dir nicht vielleicht ein Andenken an diesen Kampf mitnehmen solltest...");
		addnav("Trophäe");
		$who=rawurlencode($badguy[creaturename]);
		$id=$badguy['acctid'];
		addnav("Trophäe mitnehmen","trophy.php?op=look&who=$who&id=$id&dks=$badguy[dragonkills]&where=$badguy[location]");
		  }
		}
        $badguy=array();

    }else{
		if($defeat){
			addnav("Tägliche News","news.php");
			$sql = "SELECT taunt FROM taunts ORDER BY rand(".e_rand().") LIMIT 1";
			$result = db_query($sql) or die(db_error(LINK));
			$taunt = db_fetch_assoc($result);
			$taunt = str_replace("%s",($session[user][sex]?"sie":"ihn"),$taunt[taunt]);
			$taunt = str_replace("%o",($session[user][sex]?"sie":"er"),$taunt);
			$taunt = str_replace("%p",($session[user][sex]?"ihr(e/n)":"sein(e/n)"),$taunt);
			$taunt = str_replace("%x",($session[user][weapon]),$taunt);
			$taunt = str_replace("%X",$badguy[creatureweapon],$taunt);
			$taunt = str_replace("%W",$badguy[creaturename],$taunt);
			$taunt = str_replace("%w",$session[user][name],$taunt);
			if ($badguy[location]==USER_LOC_INN){
				$killedin="`6der Kneipe";
			} else if ($badguy[location]==USER_LOC_HOUSE){
				$killedin="`2einem Haus";
			}else{
				$killedin="`@den Feldern";
			}
			
			$badguy[acctid]=(int)$badguy[acctid];
			$badguy[creaturegold]=(int)$badguy[creaturegold];
			
			// GILDENMOD
			$gp_add_msg = '';
			if($session['user']['guildid'] && $session['user']['guildfunc'] != DG_FUNC_APPLICANT 
				&& $badguy['guildid'] && $badguy['guildfunc'] != DG_FUNC_APPLICANT)  {
				
				$gp_add_msg = dg_pvp_kill($badguy,0);
				
			}			
			// END GILDENMOD
			
			
			// Items verlieren, PvP-Verlust
			$item_hook_info ['min_chance'] = e_rand( 1,100 );
			$item_hook_info ['badguy_acctid'] = $badguy ['acctid'];
			$item_hook_info ['loose_str'] = '';
			$item_hook_info ['win_str'] = '';
			
			$res = item_list_get( ' owner= '.$session['user']['acctid'].' AND pvp_defeat_hook != "" ' );
			
			while ( $i = db_fetch_assoc ( $res ) ) 
			{
				
				item_load_hook ( $i['pvp_defeat_hook'] , 'pvp_defeat' , $i );	
				
			}
			// END Item Hook
			
			systemmail($badguy['acctid'],"`2Du warst in $killedin`2 erfolgreich! ","`^".$session[user][name]."`2 hat dich in $killedin`2 angegriffen, aber du hast gewonnen!`n`nDafür hast du `^".round($session[user][experience]*getsetting("pvpdefgain",10)/100,0)."`2 Erfahrungspunkte und `^".$session[user][gold]."`2 Gold erhalten!"
						. $gp_add_msg . $item_hook_info['win_str'] 
						); 
			
			addnews("`%".$session[user][name]."`5 wurde bei ".($session[user][sex]?"ihrem":"seinem")."`5 Angriff auf`% $badguy[creaturename] `5  in $killedin `5getötet.`n$taunt");
													
						
			$sql = "UPDATE accounts SET gold=gold+".(int)$session[user][gold].", experience=experience+".round($session[user][experience]*getsetting("pvpdefgain",10)/100,0)." WHERE acctid=".(int)$badguy[acctid]."";
			db_query($sql);
			
			$session[user][alive]=false;
			
			debuglog("PvP: Items: '".$item_hook_info['loose_str']."', Gold: {$session['user']['gold']}, Mörder: ", $badguy['acctid']);
			
			$session[user][gold]=0;
			$session[user][hitpoints]=0;
			$session[user][experience]=round($session[user][experience]*(100-getsetting("pvpattlose",15))/100,0);
			$session[user][badguy]="";
						
			output("`b`&Du wurdest von `%$badguy[creaturename] `&besiegt!!!`n");
			output("`4Alles Gold, das du bei dir hattest, hast du verloren!`n" . $item_hook_info['loose_str'] );
			output("`4".getsetting("pvpattlose",15)."%  deiner Erfahrung ging verloren!`n");
			output("Du kannst morgen wieder kämpfen.");
			$session[user][reputation]--;
			page_footer();
		}else{
		  	fightnav(false,false);
		}
	}
}

dg_save_guild();

page_footer();
?>
