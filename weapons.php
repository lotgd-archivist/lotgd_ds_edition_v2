<?php

// 21072004

// modifications by anpera:
// stealing enabled with 1:15 success (thieves have 2:12 chance) and 'pay from bank'

// Anpassung f�rs Gildenmod durch Talion: rebate-Var

require_once "common.php";
checkday();

require_once(LIB_PATH.'dg_funcs.lib.php');
if($session['user']['guildid'] && $session['user']['guildfunc'] != DG_FUNC_APPLICANT) {	
	$rebate = dg_calc_boni($session['user']['guildid'],'rebates_weapon',0);
}

if ($HTTP_GET_VARS[op]=="fight") {$battle=true;}

page_header("MightyE's Waffenladen");
output("`c`b`&MightyE's Waffen`0`b`c");
$tradeinvalue = round(($session[user][weaponvalue]*.75),0);
if ($HTTP_GET_VARS[op]==""){
  output("`!MightyE `7steht hinter einem Ladentisch und scheint dir nur wenig Interesse entgegen zu bringen, als du eintrittst. ");
	output("Aus Erfahrung wei�t du aber, dass er jede deiner Bewegungen misstrauisch beobachtet. Er mag ein bescheidener ");
	output("Waffenh�ndler sein, aber er tr�gt immer noch die Grazie eines Mannes in sich, der seine Waffen gebraucht hat, ");
	output("um st�rkere ".($session[user]['sex']?"Frauen":"M�nner")." als dich zu t�ten.`n`n");
	output("Der massive Griff eines Claymore ragt hinter seiner Schulter hervor, dessen Schimmer im Licht der Fackeln ");
	output("viel heller wirkt, als seine Glatze, die er mehr zum strategischen Vorteil rasiert h�lt, ");
	output("obwohl auch die Natur bereits auf einem bestimmten Level der Kahlk�pfigkeit besteht. ");
	output("`n`n`!MightyE`7  nickt dir schlie�lich zu und w�nscht sich, w�hrend er seinen Spitzbart streichelt, ");
	output("eine Gelegenheit, um eine seiner Waffen benutzen zu k�nnen.`n`n");
	addnav("Waffen anschauen","weapons.php?op=peruse");
	addnav("Zur�ck zum Marktplatz","market.php");
	
	$show_invent = true;
	

// Duell f�r die Waffenumbenennung
}else if ($HTTP_GET_VARS[op]=="duel"){
$pointsavailable=$session['user']['donation']-$session['user']['donationspent'];
if ($pointsavailable<500) {
output("MightyE lacht aus vollem Halse als du ihm entgegentrittst und wendet sich dann auch wieder seiner Arbeit zu, nachdem er etwas Unverst�ndliches gemurmelt hat. Was immer es war, es klang nicht sehr freundlich.");
addnav("Zur�ck zum Marktplatz","market.php");
} else {
output("MightyE legt seine Sachen bei Seite und mustert dich eindringlich und nickt. Ihr steht euch nun gegen�ber. Einige Schaulustige haben sich bereits um euch versammelt. Noch kannst du weglaufen. Der Kampf kostet dich, egal wie er ausgeht, 100 Donation Punkte und weiter 400 wenn du gewinnst.");
addnav("Angreifen","weapons.php?op=duel2");
addnav("Zur�ck zum Marktplatz","market.php");
}

}else if ($HTTP_GET_VARS[op]=="duel2"){
$session['user']['donationspent']+=100;
if (!$session['user']['prefs']['nosounds']) output("<embed src=\"media/bigbong.wav\" width=10 height=10 autostart=true loop=false hidden=true volume=100>",true);
$battle=true;
            
             $badguy = array("creaturename"=>"`#MightyE`0","creaturelevel"=>$session[user][level],"creatureweapon"=>"Katana","creatureattack"=>$session[user][attack],"creaturedefense"=>$session[user][defence],"creaturehealth"=>$session[user][hitpoints], "diddamage"=>0);

$session[user][badguy]=createstring($badguy);

// Ende1

} else if ($HTTP_GET_VARS[op]=="peruse"){
	$sql = "SELECT max(level) AS level FROM weapons WHERE level<=".(int)$session[user][dragonkills];
	$result = db_query($sql) or die(db_error(LINK));
	$row = db_fetch_assoc($result);
		
    $sql = "SELECT * FROM weapons WHERE level = ".(int)$row[level]." ORDER BY damage ASC";
	$result = db_query($sql) or die(db_error(LINK));
	output("`7Du schlenderst durch den Laden und tust dein Bestes, so auszusehen, als ob du w��test, was die meisten dieser Objekte machen. ");
	output("`!MightyE`7 schaut dich an und sagt \"`#Ich gebe dir `^$tradeinvalue`# ");
	output(" Gold f�r `5".$session[user][weapon]."`# ".($rebate?"und `^".$rebate." %`# Rabatt dank deiner Gildenmitgliedschaft.":"").". Klicke einfach auf die Waffe, die du kaufen willst... was auch immer 'klick' bedeuten mag`7.\". ");
	output("Dabei schaut er v�llig verwirrt. Er steht ein paar Sekunden nur da, schnippt mit den Fingern und fragt sich, ob das ");
	output("mit 'klicken' gemeint sein k�nnte, bevor er sich wieder seiner Arbeit zuwendet: Herumstehen und gut aussehen.");
	if($session[user][reputation]<=-10) output("`nEr sieht dich misstrauisch an, als ob er w�sste, dass du hier hin und wieder versuchst, ihm seine sch�nen Waffen zu klauen.");
	output("<table border='0' cellpadding='0'>",true);
	output("<tr class='trhead'><td>`bName`b</td><td align='center'>`bSchaden`b</td><td align='right'>`bPreis`b</td></tr>",true);
	for ($i=0;$i<db_num_rows($result);$i++){
	  	$row = db_fetch_assoc($result);
		$row['value'] = ceil( $row['value'] * (100 - $rebate) * 0.01);
		$bgcolor=($i%2==1?"trlight":"trdark");
		if ($row[value]<=($session[user][gold]+$tradeinvalue)){
			output("<tr class='$bgcolor'><td>Kaufe <a href='weapons.php?op=buy&id=$row[weaponid]'>$row[weaponname]</a></td><td align='center'>$row[damage]</td><td align='right'>$row[value]</td></tr>",true);
			addnav("","weapons.php?op=buy&id=$row[weaponid]");
		}else{
//			output("<tr class='$bgcolor'><td>$row[weaponname]</td><td align='center'>$row[damage]</td><td align='right'>$row[value]</td></tr>",true);
//			addnav("","weapons.php?op=buy&id=$row[weaponid]");
			output("<tr class='$bgcolor'><td>- - - - <a href='weapons.php?op=buy&id=$row[weaponid]'>$row[weaponname]</a></td><td align='center'>$row[damage]</td><td align='right'>$row[value]</td></tr>",true);
			addnav("","weapons.php?op=buy&id=$row[weaponid]");
		}
	}
	output("</table>",true);
	
	$show_invent = true;

	if ($session[user][rename_weapons]==0)
	{addnav("MightyE zum Kampf herausfordern (500 DP)","weapons.php?op=duel");}
	else {addnav("In den geheimen Shop","xshop.php?op=peruse");}
	addnav("Zur�ck zum Marktplatz","market.php");

}else if ($HTTP_GET_VARS[op]=="buy"){
  	$sql = "SELECT * FROM weapons WHERE weaponid='$HTTP_GET_VARS[id]'";
	$result = db_query($sql) or die(db_error(LINK));
	if (db_num_rows($result)==0){
	  	output("`!MightyE`7 schaut dich eine Sekunde lang verwirrt an und kommt zu dem Schluss, dass du ein paar Schl�ge zuviel auf den Kopf bekommen hast. Schlie�lich nickt er und grinst.");
		addnav("Nochmal versuchen?","weapons.php");
		addnav("Zur�ck zum Marktplatz","market.php");
	}else{
	 	 $row = db_fetch_assoc($result);
		 $row['value'] = ceil( $row['value'] * (100 - $rebate) * 0.01);
		if ($row[value]>($session[user][gold]+$tradeinvalue)){
			if ($session[user]['specialtyuses'][thievery]>=2) {
				$klau=e_rand(1,15);
			} else {
				$klau=e_rand(2,18);
			}
			$session[user][reputation]-=10;
			if ($session[user][reputation]<=-10){
				if ($session[user][reputation]<=-20) $klau=10;
				if ($klau==1){ // Fall nur f�r Diebe
					output("`5Mit den Fertigkeiten eines erfahrenen Diebes tauschst du `%$row[weaponname]`5 gegen `%".$session[user][weapon]."`5 aus und verl�sst fr�hlich pfeifend den Laden. ");
					output(" `bGl�ck gehabt!`b  `!MightyE`5 war gerade durch irgendwas am Fenster abgelenkt. Aber nochmal passiert ihm das nicht! Stolz auf deine ");
					output("fette Beute stolzierst du �ber den Marktplatz - bis dir jemand mitteilt, dass dir da noch ein Preisschild herumbaumelt...`nDu verlierst einen Charmepunkt!");
					$arr_wpn['tpl_name'] = $row['weaponname'];
					$arr_wpn['tpl_value1'] = $row['damage'];
					$arr_wpn['tpl_gold'] = $row['weaponvalue'];
					if ($session[user][charm]) $session[user][charm]-=1;
					addnav("Zur�ck zum Marktplatz","market.php");
				} else if ($klau==2 || $klau==3) { // Diebstahl gelingt perfekt
					output("`5Da dir das n�tige Kleingold fehlt, grapschst du dir `%$row[weaponname]`5 und tauschst `%".$session[user][weapon]."`5 unauff�llig dagegen aus. ");
					output(" `bGl�ck gehabt!`b `!MightyE`5 war gerade durch irgendwas am Fenster abgelenkt. Aber nochmal wird ihm das nicht passieren! Stolz auf deine ");
					output("fette Beute stolzierst du �ber den Marktplatz - bis dir jemand mitteilt, dass dir da noch ein Preisschild herumbaumelt...`nDu verlierst einen Charmepunkt!");
					$arr_wpn['tpl_name'] = $row['weaponname'];
					$arr_wpn['tpl_value1'] = $row['damage'];
					$arr_wpn['tpl_gold'] = $row['weaponvalue'];
					if ($session[user][charm]) $session[user][charm]-=1;
					addnav("Zur�ck zum Marktplatz","market.php");
				} else if ($klau==4 || $klau==5) { // Diebstahl gelingt, aber nachher erwischt
					output("`5Du grapschst dir `%$row[weaponname]`5 und tauschst `%".$session[user][weapon]."`5 unauff�llig dagegen aus. ");
					output(" So schnell und unauff�llig wie du kannst verl�sst du den Laden. Geschafft! Als du mit deiner Beute �ber den Marktplatz stolzierst, siehst du aus dem ");
					output("Augenwinkel `#MightyE`5 auf dich zurauschen. Er packt dich mit einer Hand an ".$session[user][armor]." und zerrt dich mit zur Stadtbank...`n`n");
					output("`#MightyE`5 zwingt dich mit seinen H�nden eng um deinen Hals geschlungen dazu, die `^".($row['value']-$tradeinvalue)."`5 Gold, die du ihm schuldest, von der Bank zu zahlen!");
					if ($session[user][goldinbank]<0){
						output("Da du jedoch schon Schulden bei der Bank hast, bekommt er von dort nicht was er verlangt.`n");
						output("Er entrei�t dir $row[weaponname] gewaltsam, ");
						output(" dr�ckt dir dein(e/n) alte(n/s) ".$session[user][weapon]." in die Hand und schl�gt dich nieder. Er raunzt noch etwas, dass du Gl�ck hast, so arm zu sein, sonst h�tte er dich umgebracht und dass er dich beim n�chsten Diebstahl");
						output(" ganz sicher umbringen wird, bevor er in seinen Laden zur�ck geht, wo bereits ein Kunde wartet.`n");
						$session[user][hitpoints]=round($session[user][hitpoints]/2);
					}else{
						$session[user][goldinbank]-=($row[value]-$tradeinvalue);
						if ($session[user][goldinbank]<0) output("`nDu hast dadurch jetzt `^".abs($session[user][goldinbank])." Gold`5 Schulden bei der Bank!!");
						output("`nDas n�chste Mal bringt er dich um. Da bist du ganz sicher.");
						//debuglog("lost " . ($row['value']-$tradeinvalue) . " gold in bank for stealing the " . $row['weaponname'] . " weapon");
						$arr_wpn['tpl_name'] = $row['weaponname'];
						$arr_wpn['tpl_value1'] = $row['damage'];
						$arr_wpn['tpl_gold'] = $row['weaponvalue'];
					}
					addnav("Zur�ck zum Marktplatz","market.php");
				} else { // Diebstahl gelingt nicht
			  		output("W�hrend du wartest, bis `!MightyE`7 in eine andere Richtung schaut, n�herst du dich vorsichtig dem `5$row[weaponname]`7 und nimmst es leise vom Regal. ");
					output("Deiner fetten Beute gewiss drehst du dich leise, vorsichtig, wie ein Ninja, zur T�r, nur um zu entdecken, ");
					output("dass `!MightyE`7 drohend in der T�r steht und dir den Weg abschneidet. Du versuchst einen Flugtritt. Mitten im Flug h�rst du das \"SCHING\" eines Schwerts, ");
					output("das seine Scheide verl�sst.... dein Fu� ist weg. Du landest auf dem Beinstumpf und `!MightyE`7 steht immer noch im Torbogen, das Schwert ohne Gebrauchsspuren wieder im  Halfter und mit ");
					output("vor der st�mmigen Brust bedrohlich verschr�nkten Armen. \"`#Vielleicht willst du daf�r bezahlen?`7\" ist alles, was er sagt, ");
					output("w�hrend du vor seinen F��en zusammen brichst und deinen Lebenssaft unter deinem dir verbliebenen Fu� �ber den Boden aussch�ttest.`n");
					$session[user][alive]=false;
					//debuglog("lost " . $session['user']['gold'] . " gold on hand due to stealing from Pegasus");
					$session[user][gold]=0;
					$session[user][hitpoints]=0;
					$session[user][experience]=round($session[user][experience]*.9,0);
					$session[user][gravefights]=round($session[user][gravefights]*0.75);
					output("`b`&Du wurdest von `!MightyE`& umgebracht!!!`n");
					output("`4Das Gold, das du dabei hattest, hast du verloren!`n");
					output("`4Du hast 10% deiner Erfahrung verloren!`n");
					output("Du kannst morgen wieder k�mpfen.`n");
					output("`nWegen der Unehrenhaftigkeit deines Todes landest du im Fegefeuer und wirst das Reich der Schatten aus eigener Kraft heute nicht mehr verlassen k�nnen!");
					addnav("T�gliche News","news.php");
					addnews("`%".$session[user][name]."`5 wurde beim Versuch, in `!MightyE`5's Waffenladen zu stehlen, niedergemetzelt.");
				}
				if ($session[user][reputation]<=-10) $session[user][reputation]-=10;
			}else{
				$session[user][reputation]-=10;
				if ($klau==1){ // Fall nur f�r Diebe
					output("`5Mit den Fertigkeiten eines erfahrenen Diebes tauschst du `%$row[weaponname]`5 gegen `%".$session[user][weapon]."`5 aus und verl�sst fr�hlich pfeifend den Laden. ");
					output(" `bGl�ck gehabt!`b  `!MightyE`5 war gerade durch irgendwas am Fenster abgelenkt. Aber irgendwann wird er den Diebstahl bemerken und in Zukunft wesentlich besser aufpassen! Stolz auf deine ");
					output("fette Beute stolzierst du �ber den Marktplatz - bis dir jemand mitteilt, dass dir da noch ein Preisschild herumbaumelt...`nDu verlierst einen Charmepunkt!");
					
					$arr_wpn['tpl_name'] = $row['weaponname'];
					$arr_wpn['tpl_value1'] = $row['damage'];
					$arr_wpn['tpl_gold'] = $row['weaponvalue'];
					
					addnav("Zur�ck zum Marktplatz","market.php");
				} else if ($klau==2 || $klau==3) { // Diebstahl gelingt perfekt
					output("`5Da dir das n�tige Kleingold fehlt, grapschst du dir `%$row[weaponname]`5 und tauschst `%".$session[user][weapon]."`5 unauff�llig dagegen aus. ");
					output(" `bGl�ck gehabt!`b `!MightyE`5 war gerade durch irgendwas am Fenster abgelenkt. Aber irgendwann wird er den Diebstahl bemerken und in Zukunft besser aufpassen. Stolz auf deine ");
					output("fette Beute stolzierst du �ber den Marktplatz - bis dir jemand mitteilt, dass dir da noch ein Preisschild herumbaumelt...`nDu verlierst einen Charmepunkt!");
					
					$arr_wpn['tpl_name'] = $row['weaponname'];
					$arr_wpn['tpl_value1'] = $row['damage'];
					$arr_wpn['tpl_gold'] = $row['weaponvalue'];
					
					if ($session[user][charm]) $session[user][charm]-=1;
					addnav("Zur�ck zum Marktplatz","market.php");
				} else if ($klau==4 || $klau==5) { // Diebstahl gelingt, aber nachher erwischt
					output("`5Du grapschst dir `%$row[weaponname]`5 und tauschst `%".$session[user][weapon]."`5 unauff�llig dagegen aus. ");
					output(" So schnell und unauff�llig wie du kannst verl�sst du den Laden. Geschafft! Als du mit deiner Beute �ber den Marktplatz stolzierst, siehst du aus dem ");
					output("Augenwinkel `#MightyE`5 auf dich zurauschen. Er packt dich mit einer Hand an ".$session[user][armor]." und zerrt dich mit zur Stadtbank...`n`n");
					output("`#MightyE`5 zwingt dich mit seinen H�nden eng um deinen Hals geschlungen dazu, die `^".($row['value']-$tradeinvalue)."`5 Gold, die du ihm schuldest, von der Bank zu zahlen!");
					if ($session[user][goldinbank]<0){
						output("Da du jedoch schon Schulden bei der Bank hast, bekommt er von dort nicht was er verlangt.`n");
						output("Er entrei�t dir $row[weaponname] gewaltsam, ");
						output(" dr�ckt dir dein(e/n) alte(n/s) ".$session[user][weapon]." in die Hand und schl�gt dich nieder. Er raunzt noch etwas, dass du Gl�ck hast, so arm zu sein, sonst h�tte er dich umgebracht und dass er dich beim n�chsten Diebstahl");
						output(" ganz sicher umbringen wird, bevor er in seinen Laden zur�ck geht, wo bereits ein Kunde wartet.`n");
						$session[user][hitpoints]=round($session[user][hitpoints]/2);
					}else{
						$session[user][goldinbank]-=($row[value]-$tradeinvalue);
						if ($session[user][goldinbank]<0) output("`nDu hast dadurch jetzt `^".abs($session[user][goldinbank])." Gold`5 Schulden bei der Bank!!");
						//debuglog("lost " . ($row['value']-$tradeinvalue) . " gold in bank for stealing the " . $row['weaponname'] . " weapon");
						output("`nDas n�chste Mal bringt er dich wahrscheinlich um.");
						
						$arr_wpn['tpl_name'] = $row['weaponname'];
						$arr_wpn['tpl_value1'] = $row['damage'];
						$arr_wpn['tpl_gold'] = $row['weaponvalue'];
					}
					addnav("Zur�ck zum Marktplatz","market.php");
				} else { // Diebstahl gelingt nicht
					output("`5Du grapschst dir `%$row[weaponname]`5 und tauschst `%".$session[user][weapon]."`5 unauff�llig dagegen aus. ");
					output(" So schnell und unauff�llig wie du kannst verl�sst du den Laden. Geschafft! Als du mit deiner Beute �ber den Marktplatz stolzierst, siehst du aus dem ");
					output("Augenwinkel `#MightyE`5 auf dich zurauschen. Er packt dich mit einer Hand an ".$session[user][armor].".`n`n");
					output("Er entrei�t dir $row[weaponname] gewaltsam, ");
					output(" dr�ckt dir dein(e/n) alte(n/s) ".$session[user][weapon]." in die Hand und schl�gt dich nieder. Er raunzt noch etwas, dass er dich beim n�chsten Diebstahl");
					output(" ganz sicher umbringen wird, bevor er in seinen Laden zur�ck geht, wo bereits ein Kunde wartet.`n");
					$session[user][hitpoints]=1;
					if ($session[user][turns]>0){
						output("`n`4Du verlierst einen Waldkampf und fast alle Lebenspunkte.");
						$session[user][turns]-=1;
					}else{
						output("`n`4MightyE hat dich so schlimm erwischt, dass eine Narbe bleiben wird.`nDu verlierst 3 Charmepunkte und fast alle Lebenspunkte.");
						$session[user][charm]-=3;
						if ($session[user][charm]<0) $session[user][charm]=0;
					}
					addnav("Zur�ck zum Marktplatz","market.php");
				}
			}
		}else{
			output("`!MightyE`7 nimmt dein `5".$session[user][weapon]."`7 stellt es aus und h�ngt sofort ein neues Preisschild dran. ");
			//debuglog("spent " . ($row['value']-$tradeinvalue) . " gold on the " . $row['weaponname'] . " weapon");
			$session[user][gold]-=$row[value];
			$session[user][gold]+=$tradeinvalue;
			
			$arr_wpn['tpl_name'] = $row['weaponname'];
			$arr_wpn['tpl_value1'] = $row['damage'];
			$arr_wpn['tpl_gold'] = $row['value'];
						
			output("`n`nIm Gegenzug h�ndigt er dir ein gl�nzendes, neues `5$row[weaponname]`7 aus, das du probeweise im Raum schwingst. Dabei schl�gst du `!MightyE`7 fast den Kopf ab. ");
			output("Er duckt sich so, als ob du nicht der erste bist, der seine neue Waffe sofort ausprobieren will...");
			addnav("Zur�ck zum Marktplatz","market.php");
		}
	}
}

if(is_array($arr_wpn)) {
	
	// Zu invent hinzuf�gen
	$int_wid = item_add($session['user']['acctid'],'waffedummy',true,$arr_wpn);
	// Als Waffe ausr�sten (dabei alte Waffe l�schen)
	item_set_weapon($arr_wpn['tpl_name'],$arr_wpn['tpl_value1'],$arr_wpn['tpl_gold'],$int_wid,0,2);
	
}

if ($battle){
                include("battle.php");

                if ($victory) {
                $badguy=array();
                $session[user][badguy]="";
                $battle=false;
                    output("`7Bevor du zum letzten Schlag ansetzen kannst hebt MightyE eine Hand.`n");
                output ("`#Du hast dich wahrhaft w�rdig erwiesen. Komm mit mir, ich zeige dir einen Ort an dem ich besondere Arbeiten f�r ganz besondere Leute vollbringe.");
                addnews("`#".$session[user][name]."`5 hat `!MightyE`5 in einem fairen Zweikampf bezwungen.");
                    $session['user']['donationspent']+=400;
                    $session['user'][rename_weapons]=1;
                    $session[user][hitpoints]=$session[user][maxhitpoints];
                addnav("Mitgehen","xshop.php?op=peruse");
                addnav("Zur�ck zum Marktplatz","market.php");
                } else if ($defeat) {
                    output ("`7Anstatt dich in das Reich des Schlafes zu bef�rdern reicht `#MightyE`7 dir eine Hand und hilft dir auf. Das war wohl nichts!");
                    $session[user][hitpoints]=$session[user][maxhitpoints];
                    $battle=false;
                addnews("`%".$session[user][name]."`5 wurde von `!MightyE`5 in einem fairen Zweikampf windelweich geschlagen.");
                addnav("Zur�ck zum Marktplatz","market.php");
                } else { fightnav(false,false); }



//Duell Ende

}

page_footer();
?>
