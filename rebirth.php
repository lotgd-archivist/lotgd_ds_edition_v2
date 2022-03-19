<?php

// 15082004

// Altar of Rebirth
// Idea by Luke
// recoding and german version by anpera

// modded by talion: Umbenennung f�r die Spieler gegen DP

require_once("common.php");

page_header("Schrein der Erneuerung");

$int_rename_dp = getsetting('user_rename',1000);

output("`b`c`6Der Schrein der Erneuerung`0`c`b");
if ($_GET[op]=="rebirth1"){

	$sql = 'SELECT ctitle,cname FROM account_extra_info WHERE acctid='.$session['user']['acctid'];
	$res = db_query($sql);
	$row_extra = db_fetch_assoc($res);

	$what=$_GET[full];
	$n=$session[user][name];
	$neu = ($row_extra['cname'] ? $row_extra['cname'] : $session['user']['login']);
	
	if ($what=="true"){
		output("`n`6Du legst alle deine Besitzt�mer ab und beginnst mit dem beschriebenen Ritual. Noch einmal wollen die G�tter von dir die Best�tigung, dass du dir ");
		output("diesen Schritt gut �berlegt hast. Du wirst `balles`b verlieren, wenn du fortf�hrst. Du wirst zu:`n`n");
		if ($row_extra['ctitle']){
			output("`6Name: `4$n`n");
		}else{
			output("`6Name: `4".($session[user][sex]?"Bauernm�dchen":"Bauernjunge")." $neu`n");
		}
		output("`6Lebenspunkte: `410`n");
		output("`6Level: `41`n");
		output("`6Angriff: `41`n");
		output("`6Verteidigung: `41`n");
		output("`6Erfahrung: `40`n");
		output("`6Gold: `4".getsetting("newplayerstartgold",10)."`n");
		output("`6Edelsteine: `40`n");
		output("`6Du verlierst deine Waffe, deine R�stung und dein gesamtes Inventar.`n");
		output("`6Du vergisst deine Rasse und alle besonderen F�higkeiten.`n");
		if ($session[user][house]) output("Du verlierst dein Haus.`n");
		if ($session[user][hashorse]) output("Du verlierst dein Tier.`n");
		if ($session[user][guildid]) output("Du verlierst deine Gilde.`n");
		if ($session[user][profession]) output("Du verlierst deinen Beruf.`n");
		output("Du verlierst alle Drachenpunkte.`n`n`bBist du zu diesem Schritt wirklich bereit?`b");
		output("`n`n`n<form action='rebirth.php?op=rebirth2&full=$what' method='POST'>",true);
		output("<input type='submit' class='button' value='Charakter neu beginnen' onClick='return confirm(\"Willst du deinen Charakter wirklich neu starten?\");'>", true);
		output("</form>",true);
		addnav("","rebirth.php?op=rebirth2&full=$what");
	}
	if ($what=="false"){
		output("`n`6Du legst alle deine Besitzt�mer ab und beginnst mit dem beschriebenen Ritual. Noch einmal wollen die G�tter von dir die Best�tigung, dass du dir ");
		output("diesen Schritt gut �berlegt hast. Du wirst `beiniges`b verlieren, wenn du fortf�hrst. Du wirst zu:`n`n");
		output("`6Name: `4".$session[user][name]."`n");
		output("`6Lebenspunkte: `4".($session[user][level]*10)."`n");
		output("`6Level: `4".$session[user][level]."`n");
		output("`6Angriff: `4".$session[user][level]."`n");
		output("`6Verteidigung: `4".$session[user][level]."`n");
		output("`6Erfahrung: `4".$session[user][experience]."`n");
		output("`6Gold: `40`n");
		output("`6Edelsteine: `40`n");
		output("`6Du verlierst deine Waffe, deine R�stung und dein gesamtes Inventar.`n");
		output("`6Du vergisst deine Rasse und alle besonderen F�higkeiten.`n");
		if ($session[user][house]) output("Du verlierst dein Haus.`n");
		if ($session[user][hashorse]) output("Du verlierst dein Tier.`n");
		if ($session[user][guildid]) output("Du verlierst deine Gilde.`n");
		if ($session[user][profession]) output("Du verlierst deinen Beruf.`n");
		output("Du kannst alle Drachenpunkte neu vergeben.`n`n`bBist du zu diesem Schritt wirklich bereit?`b");
		output("`n`n`n<form action='rebirth.php?op=rebirth2&full=$what' method='POST'>",true);
		output("<input type='submit' class='button' value='Charakter zur�cksetzen' onClick='return confirm(\"Willst du die Werte deines Charakters wirklich neu verteilen?\");'>", true);
		output("</form>",true);
		addnav("","rebirth.php?op=rebirth2&full=$what");
		addnav("*?Erneuerung best�tigen","rebirth.php?op=rebirth2&full=$what");
	}
	addnav("Zur�ck zum Club","rock.php");
}else if($_GET[op]=="rebirth2"){

	$sql = 'SELECT ctitle,cname FROM account_extra_info WHERE acctid='.$session['user']['acctid'];
	$res = db_query($sql);
	$row_extra = db_fetch_assoc($res);

	$neu = ($row_extra['cname'] ? $row_extra['cname'] : $session['user']['login']);
	
	// Gemeinsamkeiten
	if($session['user']['guildid'] > 0) {
		require_once(LIB_PATH.'dg_funcs.lib.php');
		dg_remove_member($session['user']['guildid'],$session['user']['acctid'],true);
	}
	$session[user]['guildid']=0;
	$session[user]['guildfunc']=1;
	$session[user]['guildrank']=10;
	
	$session[user][hashorse]=0;
	$session[user][deathpower]=0;
	$session[user][profession]=0;
	$session[user][bounty]=0;
	$session[user][bufflist]="";
	$session[user][goldinbank]=0;
	$session[user][gems]=0;
	
	$session[user][battlepoints]=0;
	$session[user][drunkenness]=0;
	
	$session[user][punch]=1;
	
	$session[user][dragonpoints]="";
	
	// Goldenes Ei
	if ($session['user']['acctid']==getsetting('hasegg',0)) {
		savesetting('hasegg',stripslashes(0));
		$sql = 'UPDATE items SET owner=0 WHERE tpl_id="goldenegg"';
		db_query($sql);
	}
	
	if ($session[user][house]){
		if ($session[user][housekey]){
			$sql="UPDATE houses SET owner=0,status=3 WHERE owner=".$session[user][acctid]."";
		}else{
			$sql="UPDATE houses SET owner=0,status=4 WHERE owner=".$session[user][acctid]."";
		}
	db_query($sql);
	}
	$session[user][house]=0;
	$session[user][housekey]=0;
	$sql="UPDATE keylist SET owner=0 WHERE owner=".$session[user][acctid];
	db_query($sql);
	
	// Einladungen in Privatgem�cher entfernen
	item_delete(' tpl_id="prive" AND value2='.$session['user']['acctid']);
	
	// Besitzukrunden f�r Privatgem�cher zur�cksetzen
	item_set(' tpl_id="privb" AND owner='.$session['user']['acctid'], array('owner'=>0) );
	
	// Inventar l�schen
	item_delete(' owner='.$session[user][acctid] );
	
	// F�rstentitel vakant setzen
	$fuerst = stripslashes(getsetting('fuerst',''));
	if($fuerst == $neu) {
		savesetting('fuerst','');
	}
	
		
	$what=$_GET[full];
	if ($what=="true"){
		addnews("`#".$session[user][name]."`# hat seinem bisherigen Leben ein Ende gesetzt und einen Neuanfang beschlossen.");
		if (!$row_extra['ctitle']){
			$session[user][name]=($session[user][sex]?"Bauernm�dchen":"Bauernjunge").' '.$neu;
		}
		$session[user][title]=($session[user][sex]?"Bauernm�dchen":"Bauernjunge");
		
		user_set_aei(array('ctitle'=>'','cname'=>'','ctitle_backup'=>''));
		
		$session[user][level]=1;
		$session[user][maxhitpoints]=10;
		$session[user][hitpoints]=$session[user]['maxhitpoints'];
		$session[user][attack]=1;
		$session[user][defence]=1;
		$session[user][gold]=getsetting("newplayerstartgold",0);
		$session[user][experience]=0;
		
		$session[user][age]=0;
		$session[user][reputation]+=25;
		
		$session[user][dragonkills]=0;
		$session[user][specialty]=0;
		
		$session['user']['specialtyuses'][darkarts]=0;
		$session['user']['specialtyuses'][thievery]=0;
		$session['user']['specialtyuses'][magic]=0;
		$session[user][weapon]="Fists";
		$session[user][armor]="T-Shirt";
		
		if ($session[user][marriedto]>0 && $session[user][marriedto]<4294967295 && $session[user][charisma]==4294967295){
			$sql="UPDATE accounts SET marriedto=0,charisma=0 WHERE acctid=".$session[user][marriedto]."";
			db_query($sql);
			systemmail($session[user][marriedto],"`6".$session[user][name]." ist nicht mehr der selbe`0","`6{$session['user']['name']}`6 hat sich ein neues Leben gegeben. Ihr seid nicht l�nger verheiratet.");
		}
		$session[user][charisma]=0;
		$session[user][marriedto]=0;
		$session[user][weaponvalue]=0;
		$session[user][armorvalue]=0;
		$session[user][resurrections]=0;
		$session[user][weapondmg]=0;
		$session[user][armordef]=0;
		$session[user][charm]=0;
		$session[user][race]=0;
		$session[user][dragonage]=0;
										
		debuglog("REBIRTH ".date("Y-m-d H:i:s")."");
				
		addhistory('`^`b'.addslashes($session['user']['login']).' hat ein neues Leben begonnen!`b');
		
		$session[user][laston]="";
		$session[user][lasthit]=date("Y-m-d H:i:s",strtotime(date("r")."-".(86500/getsetting("daysperday",4))." seconds")); 
		output("`n`6Du stimmst zu.`nW�hrend du das Ritual durchf�hrst und dich von deinem Besitz l�st, sp�rst du auch deine Lebenkraft, deine Erfahrung und schlie�lich all deine F�higkeiten ");
		output("schwinden. Du vergisst dein ganzes bisheriges Leben. Du f�llst in eine lange Ohnmacht...");
	}
	if ($what=="false"){
		addnews("`#".$session[user][name]."`# hat einen radikalen Lebenswandel beschlossen.");
		$session[user][maxhitpoints]=$session[user][level]*10;
		$session[user][attack]=$session[user][level];
		$session[user][defence]=$session[user][level];
		
		$session[user][hitpoints]=$session[user]['maxhitpoints'];
		
		$session['user']['gold'] = 0;
		
		$session[user][reputation]-=25;
						
		$session[user][specialty]=0;
		$session['user']['specialtyuses'][darkarts]=0;
		$session['user']['specialtyuses'][thievery]=0;
		$session['user']['specialtyuses'][magic]=0;
		$session[user][weapon]="F�uste der Erneuerung";
		$session[user][armor]="Haut der Erneuerung";
		
		$session[user][weaponvalue]=0;
		$session[user][armorvalue]=0;
		$session[user][weapondmg]=$session[user][level];
		$session[user][armordef]=$session[user][level];
		$session[user][charm]=1;
		$session[user][race]=0;
								
		debuglog("RENEWAL ".date("Y-m-d H:i:s")."");
				
		$session[user][lasthit]=date("Y-m-d H:i:s",strtotime(date("r")."-".(86500/getsetting("daysperday",4))." seconds")); 
		output("`n`6Du stimmst zu.`nW�hrend du das Ritual durchf�hrst und dich von deinem Besitz l�st, sp�rst du auch deine Lebenkraft und all deine F�higkeiten ");
		output("schwinden. Du vergisst vieles aus deinem bisherigen Leben und f�llst in eine lange Ohnmacht...");
		
		addhistory('`^`bHat '.($session['user']['sex'] ? 'ihr' : 'sein').' Leben radikal gewandelt!`b');
	}
	
	if($int_rename_dp && ($session['user']['donation'] - $session['user']['donationspent']) >= $int_rename_dp) {
		output('`n`nGenau jetzt er�ffnet sich dir die M�glichkeit einer Umbenennung:`n`n'.create_lnk('Umbenennung!','rebirth.php?op=rename'),true);
	}
	
}
else if($_GET['op'] == 'rename') {
	
	$str_msg = '';
	
	$shortname = $session['user']['login'];
	$str_name = $shortname;
	
	if($_GET['act'] == 'save') {
		
		$bool_save = true; 
					
		// Name checken
		// Auf jeden Fall Formatierungstags raus
		$str_name = strip_appoencode(trim($_POST['newname']),3);
						
		// Auf Korrektheit pr�fen
		$str_valid = user_rename(0, stripslashes($str_name));
								
		if(true !== $str_valid) {
			
			switch($str_valid) {
				
				case 'login_banned':
					$str_msg .= 'Dieser Name ist gebannt!';						
				break;
				
				case 'login_blacklist':
					$str_msg .= 'Dieser Name ist verboten!';						
				break;
				
				case 'login_dupe':
					$str_msg .= 'Diesen Namen gibt es leider schon!';						
				break;
				
				case 'login_tooshort':
					$str_msg .= 'Dein gew�hlter Name ist zu kurz (Min. '.getsetting('nameminlen',3).' Zeichen)!';						
				break;
				
				case 'login_toolong':
					$str_msg .= 'Dein gew�hlter Name ist zu lang (Max. '.getsetting('namemaxlen',3).' Zeichen)!';						
				break;
				
				case 'login_badword':
					$str_msg .= 'Dein gew�hlter Name enth�lt unzul�ssige Begriffe!';						
				break;
				
				case 'login_spaceinname':
					$str_msg .= 'Dein gew�hlter Name enth�lt Leerzeichen, was leider nicht erlaubt ist!';						
				break;
				
				case 'login_specialcharinname':
					$str_msg .= 'Dein gew�hlter Name enth�lt Sonderzeichen, was leider nicht erlaubt ist!';						
				break;
				
				case 'login_criticalcharinname':
					$str_msg .= 'Dein gew�hlter Name enth�lt Zeichen, die f�r einen Namen nicht geeignet sind (z.B. Zahlen oder der Unterstrich)!';						
				break;
				
				case 'login_titleinname':
					$str_msg .= 'Dein gew�hlter Name enth�lt einen Titel, der ein Teil des Spiels ist!';						
				break;
				
				default:
					$str_msg .= 'Irgendwas stimmt mit deinem Namen nicht, ich wei� nur nicht was ; ) Schreibe bitte eine Anfrage!';
				break;
				
			}
			
			output('`n`n`c`$`bFehler:`b `^'.$str_msg.'`c');
			$bool_save = false;
			
		}
		
		if($bool_save) {
									
			$session['user']['donationspent'] += $int_rename_dp;
			
			// eintrag in history
			addhistory('`^`b'.$shortname.' hat einen neuen Namen angenommen!`b');
			
			debuglog(' �nderte Namen. Vorher: '.$shortname);
			
			require_once(LIB_PATH.'board.lib.php');
			board_add ('namechange',100,0,'Fr�herer Name: '.$shortname);
			
			// User in Registratur setzen
			user_set_aei(array('ctitle'=>'','namecheck'=>0,'namecheckday'=>0,'ctitle_backup'=>''));
			
			// Gesamtname aktualisieren
			user_set_name(0);
									
			output('`n`@`cGratuliere!`n`&Du bezahlst '.$int_rename_dp.' Donationpoints und bist von nun an bekannt unter dem Namen `b'.$session['user']['name'].'`b!`c');						
									
		}
		
	}
	
	if(!$bool_save) {
	
		$str_lnk = 'rebirth.php?op=rename&act=save';
		addnav('',$str_lnk);
		
		output('<form action="'.$str_lnk.'" method="POST">
				`n`n`&Falls du dir nun f�r `b'.$int_rename_dp.' Donationpoints`b einen neuen Namen suchen m�chtest, 
					gib ihn in diesem Feld ein (ohne Farbcodes und Titel!):`n`n
					<input type="text" value="',true);
		rawoutput($str_name);
		output('" name="newname" size="25" maxlength="25">`n`n
					<input type="submit" value="Name �ndern!">
				</form>');
			
		addnav('Abbruch','news.php');
	}
	
}
else{
	checkday();

	output("`n`6Du gehst zu einer bedrohlich wirkenden T�r im hinteren Bereich des Clubs. ");
	if ($session[user][dragonkills]>=10){
		addnav("Vollst�ndige Wiedergeburt","rebirth.php?op=rebirth1&full=true");
		addnav("Erneuerung","rebirth.php?op=rebirth1&full=false");
		output("Wie von selbst �ffnet sich die T�r. Dahinter siehst du einen m�chtigen Altar der G�tter. Du sp�rst f�rmlich, dass sich hier dein Leben grundlegend �ndern kann.");
		output(" Eine Tafel vor dem Altar best�tigt dieses Gef�hl: \"`4Hier kannst du die Fehler deiner Vergangenheit r�ckg�ngig machen und um einen Neuanfang bitten. Wisse aber, dass diese ");
		output("Entscheidung dazu die letzte deines Lebens darstellt. Du wirst morgen ohne deine weltlichen G�ter und ohne Erinnerung auf dem Dorfplatz aufwachen. Nur mit ");
		output(" Chance ausger�stet, es noch einmal besser zu machen.`6\"`n`nWillst du neu beginnen?`n`n");
		output("`bVollst�ndige Wiedergeburt:`b`n");
		output("Du w�rdest wieder als ".($session[user][sex]?"Bauernm�dchen":"Bauernjunge")." mit nichts als den gesammelten Donationpoints im Dorf aufwachen. Dein Leben "); 
		output("w�rde beendet und im selben Moment von vorne beginnen.`n`\$Diese Option ist f�r Krieger gedacht, die bereits alles erreicht haben, ");
		output("oder die keinen Sinn mehr in ihrem einsamen Leben oberhalb der normalen Gesellschaft sehen.`n`n");
// Bad idea for balance...?
		output("`6`bErneuerung:`b`n");
		output("Drachenkills, Titel, Ehepartner und deine Erinnerung bleiben dir erhalten, jedoch legst du alle anderen weltlichen Besitzt�mer ab und wirst es sehr schwer haben, dich wieder ");
		output(" an das knallharte Leben mit dem Drachen zu gew�hnen. Daf�r kannst du alle Drachenpunkte neu vergeben.");
		
		if($int_rename_dp) {
			
			output('`n`nZus�tzlich hast du bei beiden Optionen die M�glichkeit, gegen `b'.$int_rename_dp.' Donationpoints`b
					deinen Namen zu �ndern!');
			if($int_rename_dp > ($session['user']['donation'] - $session['user']['donationspent'])) {
				output('`nDas kannst du dir zur Zeit jedoch nicht leisten.');
			}		
			
		}

	}else{
		output("Doch alle Versuche, diese T�r zu �ffnen, schlagen fehl. Du erkundigst dich im Club nach dieser T�r und bekommst tats�chlich eine Antwort: \"`4");
		output("Hinter dieser T�r steht ein m�chtiger Altar der G�tter. Es ist ein Altar des Vergesssens, des Todes und der Erneuerung. Nur sehr m�chtigen Kriegern ");
		output("ist es gestattet, diesen Altar zu benutzen. Dort k�nnen sie �ber ihr bisheriges Leben nachdenken und um einen Neuanfang bitten. Du wirst noch ");
		if ($session[user][dragonkills]<5) output("sehr viele");
		if ($session[user][dragonkills]>=5 && $session[user][dragonkills]<9) output("ein paar");
		if ($session[user][dragonkills]>=9) output("einen");
		output(" Drachen erschlagen m�ssen, bevor du den Schrein betreten kannst.`6\"");
	}
	addnav("Zur�ck zum Club","rock.php");
}
addnav("Zur�ck zum Dorf","village.php");

page_footer();
?>
