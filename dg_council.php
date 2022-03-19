<?php
/*-------------------------------/
Name: dg_council.php
Autor: tcb / talion f�r Drachenserver (mail: t@ssilo.de)
Erstellungsdatum: 6/05 - 9/05
Beschreibung:	Stellt alle anfallenden Bildschirme des Gildenrats dar (Abstimmung etc.)
				Au�erdem: Gildenliste, Ruhmeshalle, Bewerbung, Gr�ndung
				Diese Datei ist Bestandteil des Drachenserver-Gildenmods (DG). 
				Copyright-Box muss intakt bleiben, bei Verwendung Mail an Autor mit Serveradresse.
/*-------------------------------*/

require_once('common.php');
require_once(LIB_PATH.'dg_funcs.lib.php');
require_once('dg_output.php');

checkday();
page_header('Der Gildenrat');

if($session['user']['guildid']) {
	$leader = ($session['user']['guildfunc'] == DG_FUNC_LEADER) ? true : false;
	$treasure = ($session['user']['guildfunc'] == DG_FUNC_LEADER || $session['user']['guildfunc'] == DG_FUNC_TREASURE) ? true : false;
	$war = ($session['user']['guildfunc'] == DG_FUNC_LEADER || $session['user']['guildfunc'] == DG_FUNC_WAR) ? true : false;
	$members = ($session['user']['guildfunc'] == DG_FUNC_LEADER || $session['user']['guildfunc'] == DG_FUNC_MEMBERS) ? true : false;
	$team = ($leader || $treasure || $war || $members) ? true : false;
	$member = ($session['user']['guildfunc'] != DG_FUNC_APPLICANT && $session['user']['guildfunc']) ? true : false;
	$applicant = ($session['user']['guildfunc'] == DG_FUNC_APPLICANT) ? true : false;
	
	if($member) {$gid=$session['user']['guildid'];$guild = &dg_load_guild($gid);}
}

$op = ($_GET['op']) ? $_GET['op'] : '';
$out = '';

switch($op) {
			
	case 'list':
		
		dg_show_header('Liste der Gilden');
		
		output('`c');dg_show_guild_list(0,true);output('`c');
		
		if($member) {addnav($session['userguild']['name'].'`0 betreten','dg_main.php?op=in&gid='.$session['user']['guildid']);}
		else {
			if(!$applicant) {
				addnav('Gilde gr�nden','dg_council.php?op=found');
			}
		}
		
		addnav('Zur�ck');
        addnav('Zum Gildenviertel','dg_main.php');
	
		break;
	
	case 'paladin':
		
		dg_show_header('Paladinfestung');
		
		if(!$member) {output('`8Du willst gerade die Feste der Paladine betreten, als man dich schroff zur�ckweist: `&"Zutritt nur f�r Gildenangeh�rige!"`n');}	
		else {
										
			// Preis f�r die Dinger bestimmen
			$regalia_left = getsetting('dgregalialeft',10);
			$guild_count = dg_count_guilds();
			$member_count = dg_count_guild_members($gid);
						
			$percent = 30;
			
			if($guild['reputation'] < 30) {$percent += 5;}
			elseif($guild['reputation'] < 50) {$percent += 3;}
			elseif($guild['reputation'] < 70) {$percent += 2;}
			elseif($guild['reputation'] < 90) {$percent += 1;}
			
			$percent += ($member_count * 3);		
			
			if($regalia_left < $guild_count*0.25 ) {$percent += 10;}
			elseif($regalia_left < $guild_count*0.5) {$percent += 5;}
			elseif($regalia_left < $guild_count*0.75) {$percent += 2;}
			
			$percent += ($guild['regalia'] * 5);
					
			$percent = min($percent * 0.01,1);
					
			$regalia_price_gold = round(getsetting('dgtrsmaxgold',500000) * $percent);
			$regalia_price_gems = round(getsetting('dgtrsmaxgems',1000) * $percent);
			
			$bribe_price_points = 10;
			// END Preisbestimmung
						
					
			if($_GET['subop'] == 'buy_regalia') {
								
				if($_GET['act'] == 'ok') {
					$guild['regalia']++;
					$guild['gold'] -= $regalia_price_gold;
					$guild['gems'] -= $regalia_price_gems;
					dg_log('Erwirbt eine Insignie f�r '.$regalia_price_gold.' Gold und '.$regalia_price_gems.' Gems');
					savesetting('dgregalialeft',$regalia_left-1);
					dg_save_guild();
					redirect('dg_council.php?op=paladin&subop=buy_regalia&act=bought');
				}
				elseif($_GET['act'] == 'bought') {
					output('`8Ein kr�ftiger Paladin packt eine Insignie, wickelt sie sorgf�ltig in Stoff ein und meint dann zu dir: `&"Ich gratuliere euch, Meister '.$session['user']['login'].', eure Gilde hat eine gute Wahl getroffen! Wir werden das St�ck demn�chst liefern."');
					addnav('Zum Lager der Paladine','dg_council.php?op=paladin');
				}			
				else {
					if($guild['gold'] < $regalia_price_gold || $guild['gems'] < $regalia_price_gems) {
						output('`8Als du dir die Preise nochmal genauer betrachtest, stellst du fest, dass sie die finanziellen Mittel deiner Gilde �bersteigen. Schade..');
					}
					else {
						output('`8Gr�belnd stehst du vor dem Lager. Willst du f�r deine Gilde wirklich eine Insignie erwerben?');
						addnav('Ja','dg_council.php?op=paladin&subop=buy_regalia&act=ok');
					}
					addnav('Zum Lager der Paladine','dg_council.php?op=paladin');
				}
							
			}
			
			elseif($_GET['subop'] == 'bribe_king') {
				
				if($_GET['act'] == 'ok') {
				
					if(e_rand(1,2) == 1) {
						$guild['reputation'] = min($guild['reputation']+2,100);
						$guild['points'] -= $bribe_price_points;
						$guild['points_spent'] += $bribe_price_points;
						dg_save_guild();
						redirect('dg_council.php?op=paladin&subop=bribe_king&act=bribed');
					}
					else {
						output('`8Dich gemein anl�chelnd klopft dir der Paladin auf die Schulter. `&"Vielen Dank, aber ich habs mir anders �berlegt.."');		
						addnav('Zum Lager der Paladine','dg_council.php?op=paladin');
					}				
	
				}
				elseif($_GET['act'] == 'bribed') {
					output('`8Die Wache sieht wieder starr geradeaus. `&"Ich werde sehen, was sich tun l�sst.."');		
					addnav('Zum Lager der Paladine','dg_council.php?op=paladin');
				}						
				else {
					output('`8Verstohlen n�herst du dich einer scheinbar h�hergestellten Wache und raunst ihr ein Angebot ins Ohr. Nachdenklich wendet der Paladin sich dir zu, seine Miene verhei�t nichts Gutes. Du willst schon davonlaufen, als er dir grinsend zufl�stert: `&"'.$bribe_price_points.' Punkte, mein Freund.."`8`n');
					addnav('Zum Lager der Paladine','dg_council.php?op=paladin');
					if($guild['points'] < $bribe_price_points) {
						output('Schade.. So viele Punkte besitzt deine Gilde nicht. Du machst auf dem Absatz kehrt und verschwindest.');
					}
					else {
						output('Z�gernd �berlegst du, auf das Angebot einzugehen...');
						addnav('Bestechen..','dg_council.php?op=paladin&subop=bribe_king&act=ok');
					}
				}
	
			}
			
			else if($_GET['subop'] == 'ask_mood') {
				
				$king_mood = getsetting('dgkingmood',50);
				
				output('`8Du n�herst dich der Wache und gr��t sie ehrerbietig. Auf deine Frage, welcher Art denn die Stimmung des K�nigs zur Zeit sei, ');
				
				if($king_mood > 90) {
					output('nickt dir der Paladin freundlich zu: `&"Ausgezeichnet, mein Freund, ausgezeichnet!"');
				}
				else if($king_mood > 70) {
					output('sieht dich der Paladin kurz an und meint: `&"Ihre Majest�t pflegt gute Beziehungen zu den Gilden '.getsetting('townname','Atrahor').'s!"');
				}
				else if($king_mood > 50) {
					output('gr�belt der Paladin: `&"Nun, Ihre Majest�t ist mit den Gilden '.getsetting('townname','Atrahor').'s leidlich zufrieden. Sicherlich k�nnte es besser sein!"');
				}
				else if($king_mood > 30) {
					output('sieht dich der Paladin mitleidig an und lacht: `&"Eisig, um es so zu sagen!"');
				}
				else {
					output('blickt der Paladin starr geradeaus. Als du deine Frage wiederholst, knurrt er: `&"Ihre Majest�t ist �u�erst unzufrieden mit den Gilden '.getsetting('townname','Atrahor').'s! Gebt gut auf eure Insignien Acht.."');
				}
				
				addnav('Zum Lager der Paladine','dg_council.php?op=paladin');
				
			}		
			else {
				
				output('`8An die das Gildenviertel umgrenzende Mauer schmiegt sich die Feste der Paladine. Sie wachen im Auftrag des K�nigs �ber die Gilden, verwalten die Insignien und repr�sentieren den Herrscher.`n`n�berall stehen wohlger�stete Krieger herum und erf�llen ihre jeweilige Aufgabe. Von ihnen wei� sicherlich auch einer �ber die Stimmung des K�nigs Bescheid. Vielleicht sind einige auch bereit, bei diesem ein gutes Wort einzulegen..`n`n');
				
				addnav('Nach Stimmung des K�nigs fragen','dg_council.php?op=paladin&subop=ask_mood');
				
				addnav('Paladine bestechen','dg_council.php?op=paladin&subop=bribe_king');
												
				if($regalia_left) {
					output('Eine Tafel vor dem hohen Lagertor verk�ndet, dass noch `^'.$regalia_left.'`8 Insignien vorhanden sind. Diese kosten f�r deine Gilde `^'.$regalia_price_gold.'`8 Gold und `^'.$regalia_price_gems.'`8 Edelsteine.');
					if($leader) {addnav('Insignie kaufen','dg_council.php?op=paladin&subop=buy_regalia');}
				}
				else {
					output('Eine Tafel vor dem hohen Lagertor verk�ndet, dass bereits alle Insignien verkauft wurden! Deine Gilde wird wohl auf eine neue Lieferung warten m�ssen.');
				}
				
			}
		
		}	// END if member
		
		addnav('Zur�ck');
		addnav('Zum Gildenviertel','dg_main.php');
		
		break;
				
	case 'plead_king':
		
		$plead_price_turns = 3;
		$plead_price_gold = $session['user']['level'] * 100;
		
		if( $session['user']['guildid'] ) {
			$guild = &dg_load_guild($session['user']['guildid']);
		}
		
		if($_GET['act'] == 'try') {
		
			if($session['user']['gold'] < $plead_price_gold) {
			
			}
			else if($session['user']['turns'] < $plead_price_turns) {
				
			}
			else {
				if(e_rand(1,2) == 1) {
					$guild['reputation']++;
					$session['user']['gold'] -= $plead_price_gold;
					$session['user']['turns'] -= $plead_price_turns;
					output('');
				}
				else {
				
				}
			}
			
			addnav('Zur�ck');
			addnav('Zum Gildenviertel','dg_main.php');
		}
		
		break;
		
	case 'council':
		
		dg_show_header('Der Ratssaal');
		
		$council_days_left = 0;
		$vote_days_left = getsetting('dgvotedaysmax',170) - getsetting('dgvotedays',0) - DG_COUNCIL_TIME;
		if($vote_days_left <= 0) {
			$council_days_left = DG_COUNCIL_TIME + $vote_days_left;
		}
						
		if($_GET['subop'] == 'vote') {
			$vote = (int)$_GET['vote'];
						
			if($_GET['act'] == 'ok') {
			
				$reputation = $vote - 100;
				$reputation = round($reputation*0.25);
				$reputation -= e_rand(0,2);
				
				$guild['reputation'] = max($guild['reputation']+$reputation,0);
				$guild['reputation'] = min($guild['reputation'],100);
			
				$guild['vote'] = $vote;
				
				dg_save_guild();
				
				redirect('dg_council.php?op=council');
			}			
			else {
				addnav('Ja, Stimme abgeben!','dg_council.php?op=council&subop=vote&act=ok&vote='.$vote);
				addnav('Nein!','dg_council.php?op=council');
			}
			
		}
		elseif($_GET['voted']) {
			output('`8Du gibst die '.$guild['regalia'].' Stimmen deiner Gilde f�r einen Steuersatz von `^'.$_GET['voted'].' %`8!');
		}
		elseif($_GET['subop'] == 'results') {
		
			dg_load_guild(0,array('vote','regalia'));
			
			$votes = array();
			
			foreach($session['guilds'] as $g) {
				if($g['vote'] > 0) {
					$votes[ $g['vote'] ] += $g['regalia'];
				}
			}
			
			arsort($votes);
			reset($votes);
						
			output('`8Bisheriger Stand:`n`n');
			
			foreach($votes as $v=>$r) {
				
				output('`b`^'.$v.' %:`b `8'.$r.' Stimmen`n');
				
			}						
		
			addnav('Zum Ratssal','dg_council.php?op=council');
		}
		
		else {
				
			output('`8Sofort stechen dir am prachtvollen und beeindruckenden Geb�ude des Gildenrats zwei eicherne T�rfl�gel ins Auge. Hinter jenen, so erf�hrst du, beraten sich die Gilden �ber gemeinsame Unternehmungen und - wer wei� - vielleicht auch �ber die Zukunft des Dorfes. Eine Tafel verk�ndet:`n
					'.($council_days_left ? 'Der Gildenrat tagt gerade!' : '`nNoch `^'.$vote_days_left.'`8 Tage bis zur Einberufung des n�chsten Gildenrates.').
					' Der derzeitige Steuersatz betr�gt `^'.(getsetting('dgtaxmod',1)*100).'`8 %.`n`n'  
					);
			
			if(!$member) {
				output('`8Du stehst hier allerdings vor verschlossenen T�ren, die sich nur f�r Angeh�rige einer der Gilden �ffnen!');
			}
			else {
				addcommentary();
				
				output('Hufeisenf�rmig sind Tische angeordnet und mit gepolsterten St�hlen versehen. Dort nehmen die F�hrer der einzelnen Gilden Platz, wenn es um wichtige Beratungen geht. Am Rand sind ebenfalls St�hle aufgereiht, die f�r einfache Mitglieder und deren Zuschauerrolle gedacht sind. Sprechen d�rfen hier n�mlich nur die F�hrer.`n');
							
				if($council_days_left) {
					
					output('`nJetzt gerade herrscht hier rege Betriebsamkeit, Stimmengewirr durchzieht den Raum. 
					Offensichtlich wird hier noch f�r `^'.$council_days_left.'`8 Tage eine Abstimmung �ber den Steuersatz abgehalten.`n');
					
					addnav('Stand der Wahl','dg_council.php?op=council&subop=results');
									
					if($leader && $guild['vote'] == 0) {
						$link = 'dg_council.php?op=council&subop=vote&vote=';
						addnav('Abstimmung');
						addnav('50 % Steuersatz!',$link.'50');
						addnav('75 % Steuersatz!',$link.'75');
						addnav('100 % Steuersatz!',$link.'100');
						addnav('125 % Steuersatz!',$link.'125');
						addnav('150 % Steuersatz!',$link.'150');
					}
					
					output( ($guild['vote'] == 0 ? '`nDeine Gilde hat ihre `^'.$guild['regalia'].'`8 Stimmen noch nicht abgegeben!' : '`nDeine Gilde pl�diert mit ihren `^'.$guild['regalia'].'`8 Stimmen f�r einen Steuersatz von `^'.$guild['vote'].' %`8!') );
				}
				output('`n');												
				viewcommentary('guildcouncil',($team ? 'Etwas verk�nden:':'Du solltest hier besser schweigen!'),25,'verk�ndet',false,($team?true:false));				
								
			}
		}
		
		addnav('Zur�ck');
        addnav('Zum Gildenviertel','dg_main.php');
						
		break;
	
	case 'hof':
		$subop = ($_GET['subop']) ? $_GET['subop'] : 'gp';
		$order = ($_GET['order']=='asc') ? $_GET['order'] : 'DESC';
		
		addnav('Bestenlisten');
		addnav('Verkaufte Insignien','dg_council.php?op=hof&subop=regalia&order='.$order.'&page='.$page);
		addnav('Vorr�tige Insignien','dg_council.php?op=hof&subop=regalia_recent&order='.$order.'&page='.$page);
		addnav('Gildenpunkte','dg_council.php?op=hof&subop=gp&order='.$order.'&page='.$page);
		addnav('Reichtum','dg_council.php?op=hof&subop=gold&order='.$order.'&page='.$page);
		addnav('Edelsteine','dg_council.php?op=hof&subop=gems&order='.$order.'&page='.$page);
		//addnav('Ausbau','dg_council.php?op=hof&subop=build&order='.$order.'&page='.$page);
		addnav('St�rke','dg_council.php?op=hof&subop=strength&order='.$order.'&page='.$page);		
		addnav('Mitglieder','dg_council.php?op=hof&subop=member&order='.$order.'&page='.$page);
		addnav('Steuerzahler','dg_council.php?op=hof&subop=tax&order='.$order.'&page='.$page);
					
		switch($subop) {
			
			case 'gp':
				
				dg_show_hof('Die Gilden mit den '.(($order=='asc')?'wenigsten':'meisten').' Gildenpunkten in diesem Dorf:`n',
							'SELECT guildid,name,points AS data1 FROM dg_guilds ORDER BY points '.$order.', name ASC',
							false,false,array('Gildenpunkte'),array('Punkte'));
				
				break;
			
			case 'regalia':
				
				dg_show_hof('Die '.(($order=='asc')?'geringsten':'gr��ten').' Insignienlieferanten in diesem Dorf:`n',
							'SELECT guildid,name,regalia_sold AS data1 FROM dg_guilds ORDER BY regalia_sold '.$order.', name ASC',
							false,false,
							array('Insignien verkauft'));
				
				break;
			
			case 'regalia_recent':
				
				dg_show_hof('Die zur Zeit an Insignien '.(($order=='asc')?'�rmsten':'reichsten').' Gilden:`n',
							'SELECT guildid,name,regalia AS data1 FROM dg_guilds ORDER BY regalia '.$order.', name ASC',
							false,false,
							array('Insignien auf Lager'));
				
				break;
			
			case 'gold':
				
				dg_show_hof('Die '.(($order=='asc')?'�rmsten':'reichsten').' Gilden dieses Dorfes:`n',
							'SELECT guildid,name,gold AS data1 FROM dg_guilds ORDER BY gold '.$order.', name ASC',
							false,false,
							array('Verm�gen'),
							array('Gold'));
				
				break;
				
			case 'gems':
				
				dg_show_hof('Die an Edelsteinen '.(($order=='asc')?'�rmsten':'reichsten').' Gilden dieses Dorfes:`n',
							'SELECT guildid,name FROM dg_guilds ORDER BY gems '.$order.', name ASC');
				
				break;
				
			case 'build':
				
				$res = db_query('SELECT guildid,build_list,name FROM dg_guilds');
				$guilds = array();
				$builds = array();
				while($g = db_fetch_assoc($res)) {
					$g['build_list'] = unserialize($g['build_list']);
					$builds[$g['guildid']]['data1'] = 0;
					foreach($g['build_list'] as $id=>$b) {
						if($id > 0) {$builds[$g['guildid']]['data1']+=$b;}
					}
					$guilds[$g['guildid']] = $g;
				}
				$builds = sort($builds);
				$guilds = array_merge(guilds,$builds);
								
				dg_show_hof('Die am '.(($order=='asc')?'wenigsten':'weitesten').' ausgebauten Gilden dieses Dorfes:`n',
							$guilds);
				
				break;
			
			case 'strength':
				
				dg_show_hof('Die '.(($order=='asc')?'schw�chsten':'st�rksten').' Gilden in diesem Dorf:`n',
							'SELECT g.guildid,g.name,ROUND(AVG(a.dragonkills)) AS data1 FROM dg_guilds g,accounts a WHERE a.guildid=g.guildid GROUP BY g.guildid ORDER BY data1 '.$order.', name ASC',
							false,false,
							array('Durchschnitt DKs'),
							array('Drachenkills')
							);
				
				break;
				
			case 'member':
				
				dg_show_hof('Die Gilden mit den '.(($order=='asc')?'wenigsten':'meisten').' Mitgliedern in diesem Dorf:`n',
							'SELECT g.guildid,g.name,COUNT(acctid) AS data1 FROM dg_guilds g LEFT JOIN accounts a ON (a.guildid=g.guildid AND a.guildfunc!='.DG_FUNC_APPLICANT.') GROUP BY g.guildid ORDER BY data1 '.$order.', name ASC',
							false,false,
							array('Mitglieder')
							);
				
				break;
				
			case 'tax':
				
				dg_show_hof('Diese Gilden haben bisher am '.(($order=='asc')?'wenigsten':'meisten').' Steuern gezahlt:`n',
							'SELECT g.guildid,g.name,g.gold_tax AS data1, g.gems_tax AS data2 FROM dg_guilds g ORDER BY data1 '.$order.', data2 '.$order.', name ASC',
							false,'`cWeiter so!`c',
							array('Goldsumme','Gemmensumme'),
							array('Gold','Edelsteine')
							);
				
				break;
			}
		
		
		break;
		
	case 'found':
		
		$min_gold = getsetting('dgguildfoundgold',100000);
		$min_gems = getsetting('dgguildfoundgems',100);
		$min_dk = getsetting('dgguildfound_k',20);
		$max_guilds = getsetting('dgguildmax',100);
		
		$subop = ($_GET['subop']) ? $_GET['subop'] : '';
		
		dg_show_header('Gilde gr�nden');
		
		output('`8Ein edel gewandeter, w�rdevoller Elf - Gwenmarfar, so sein Name - begr��t dich im Verwaltungsoffizium der Gildengemeinschaft von '.getsetting('townname','Atrahor').'. ');
		
		switch($subop) {
								
			case '': // Gr�ndung allg. Info, Pr�fung auf Voraussetzungen
								
				$guilds = dg_count_guilds();
				$fail_count = 0;
								
				$out = '`q"Eine Gilde zu gr�nden - keine Aufgabe, die man auf die leichte Schulter nehmen sollte! Lasst euch mal n�her betrachten.." `8woraufhin er dich einer akribischen Inspektion unterzieht:`n`n`q"';
				
				if($max_guilds <= $guilds) {$out.='Leider gibt es bereits '.$max_guilds.' Gilden. Mehr sind zur Zeit nicht zugelassen!`n';$fail_count++;}		
				if($min_gold > $session['user']['gold']) {$out.='Du besitzt tragischerweise nicht die ben�tigten '.$min_gold.' Goldst�cke!`n';$fail_count++;}		
				if($min_gems > $session['user']['gems']) {$out.='Schade nur, dass du nicht die ben�tigten '.$min_gems.' Edelsteine besitzt!`n';$fail_count++;}		
				if($min_dk > $session['user']['dragonkills']) {$out.='Um eine Gilde zu gr�nden, musst du schon mindestens '.$min_dk.' Drachen auf dem Gewissen haben!`n';$fail_count++;}
												
				if($fail_count == 0) {
					$out .= 'Gratuliere, ihr erf�llt alle Voraussetzungen. Hier ist das Formular!"';
					addnav('Her mit dem Formular!','dg_council.php?op=found&subop=found');
				}
				else {
					$out .= '`nSo wird das nichts mit der eigenen Gilde!"';
				}
				
				output($out,true);
				
				addnav('Zum Gildenviertel','dg_main.php');
			
				break;
				
			case 'found': // Formular zur Gildengr�ndung
				
				$formlink = 'dg_council.php?op=found&subop=found_ok';
												
				$out = '`8Dieses Pergament solltest du mit ruhiger �berlegung ausf�llen, denn es ist endg�ltig:`n`n
						[ `^Zur Information:`n`&
						Die Beschreibung wird von der Administration herangezogen, um zu entscheiden, ob sie die Gilde freischaltet. Folgende
						Punkte sollten gekl�rt werden (In der Antwort bitte per Nummer auf die Frage verweisen):`n
						1. Welche Vorgeschichte hat zur Gildengr�ndung gef�hrt, warum gr�ndet gerade dein Charakter diese Gilde?`n
						2. Welche Ziele verfolgt die Gilde in '.getsetting('townname','Atrahor').', �ndern sich diese Ziele l�ngerfristig oder h�lt die Gilde um jeden Preis daran fest? 
							H�lt sie sich an besondere Prinzipien?`n
						3. Welche Vorgehensweise bevorzugt die Gilde zur Durchsetzung ihrer Ziele? Eher verborgene Machenschaften oder offene Aktivit�ten?
						4. Wie steht die Gilde zu offiziellen Autorit�ten in '.getsetting('townname','Atrahor').' (Richter, Stadtwache, Priester, Hexen, F�rst, K�nig..)?`n
						5. An welche Mitglieder-Zielgruppe richtet sich die Gilde?`n
						6. Welche Gr��e (Mitglieder, Einfluss) peilt die Gilde an?`n
						7. Wie schaut die innere Organisation der Vereinigung aus (Verwaltungsstruktur, �mterverteilung, Rangverteilung, Bef�rderung, Mitgliederwerbung..)?`n
						8. Wie stellst du als Gildengr�nder sicher, dass auch �ber l�ngere Zeitr�ume eine gewisse Mindestaktivit�t gewahrt bleibt?`n
						9. Was genau macht deine Gilde einzigartig, was hebt sie von evtl. �hnlichen Vereinigungen ab? ]`n`n
						';
				
				foreach($dg_child_types as $k=>$t) {
					$type_enum .= ','.$k.','.$t[0].' ('.$dg_types[$t[3]]['name'].')';						
				}	
				
				$arr_form = array(
									'name'=>'Name der Gilde:|?(max. 40 Zeichen inkl. Farbcodes, unver�nderlich)',
									'type'=>'Art der Gilde:,enum'.$type_enum,
									'guildinfo'=>'Beschreibung der Gilde:,textarea,40,20'
								);
				
				$out .= '`c<form action="'.$formlink.'" method="POST">';
				
				$out .= generateform($arr_form,array(),false,'Einreichen');			
				
				$out .= '</form>`c';
								
				addnav('',$formlink);
				
				foreach($dg_types as $t) {
					$out .= ('`n`b'.$t['name'].'`b:`n'.$t['desc'].'`n');
				}
				
				output($out,true);
								
				addnav('Doch lieber nicht!','dg_main.php');
			
				break;
				
			case 'found_ok': // Abschicken!
				
				// Alle anderen Tags als erlaubte Farbcodes rausschmei�en
				$name = preg_replace('/[`][^'.regex_appoencode(1,false).']/','',$_POST['name']).'`0';
								
				$desc = $_POST['guildinfo'];
				$type = $_POST['type'];
								
				$recent_date = getsetting('gamedate','');
				
				$sql = 'INSERT INTO dg_guilds SET 
							founder='.$session['user']['acctid'].',
							founded="'.$recent_date.'",
							name="'.$name.'",
							gold='.$min_gold.',
							gems='.$min_gems.',
							type='.$type.',
							immune_days='.getsetting('dgimmune',6).',
							ranks="'.addslashes(serialize($dg_default_ranks)).'"';
				db_query($sql);
				
				$gid = db_insert_id();
				
				$session['user']['guildid'] = $gid;				
				$session['user']['guildfunc'] = DG_FUNC_LEADER;				
				$session['user']['guildrank'] = 1;				
				$session['user']['gems'] -= $min_gems;				
				$session['user']['gold'] -= $min_gold;				
								
				debuglog('Gr�ndete f�r '.$min_gems.' Edels und '.$min_gold.' Gold die Gilde '.$name);									
								
				dg_log('Gegr�ndet f�r '.$min_gems.' Edels und '.$min_gold.' Gold');									
				
				$str_petition = "Gildengr�ndung: ".$name." (ID ".$gid.")\n\nBeschreibung:\n\n".$desc;
				
				$sql = 'INSERT INTO petitions SET author='.$session['user']['acctid'].',date=NOW(),IP="'.addslashes($session['user']['lastip']).'",ID="'.addslashes($session['user']['uniqueid']).'",body="'.addslashes($str_petition).'"';
				db_query($sql);
				
				output('`8Gwenmarfar sch�ttelt dir die Hand: `q"Willkommen in unserem d�nkelh.. will sagen, elit�ren - und das ist bestimmt nicht falsch ausgedr�ckt - Club! Ihr m�sst nur noch auf die Erteilung der Lizenz durch die G�tter warten.." `8seine Begeisterung erscheint fast �bersch�umend. Gl�cksstrahlend �berreicht er dir als Gildengr�nder die erste Insignie.');
				
				addnav('Zum Gildenviertel','dg_main.php');
			
				break;
			
		}
		
		break;	// END found
		
	case 'apply':	// Bewerbung bei einer Gilde
		
		$gid = (int)$_GET['gid'];
		
		if(!$gid) {redirect('dg_main.php');}
		
		$guild = dg_load_guild($gid);
				
		$subop = ($_GET['subop']) ? $_GET['subop'] : '';
		
		switch($subop) {
		
			case '':
				
				$min_dk = getsetting('dgmindkapply',3);
				
				output('`8Ein edel gewandeter, w�rdevoller Elf - Gwenmarfar, so sein Name - begr��t dich im Verwaltungsoffizium der Gildengemeinschaft von '.getsetting('townname','Atrahor').'. ');
				
				if($min_dk <= $session['user']['dragonkills']) {
					$left = dg_guild_is_full($gid);					
					if($left==0) {
						
						output(' Mit einem Ausdruck des Bedauerns erkl�rt er dir, dass diese Gilde bereits zu viele Mitglieder hat und deshalb keine weiteren gebrauchen kann.');
						addnav('Zum Gildenviertel','dg_main.php');
					}
					elseif($session['user']['guildfunc'] == DG_FUNC_CANCELLED && $session['user']['guildrank'] > 0) {
						output(' Kopfsch�ttelnd bedeutet er dir, besser schnell zu verschwinden. Du musst erst noch die Wartezeit von '.$session['user']['guildrank'].' Tagen abwarten, ehe du dich erneut bewerben darfst!');
						addnav('Zum Gildenviertel','dg_main.php');
					}
					else {
															
						output(' Freundlich legt er dir das Bewerbungsformular vor, weist dich jedoch zuerst auf die Informationen der Gilde hin.');
						output('`n`q"Ich hoffe, dies hilft euch bei eurer endg�ltigen Entscheidung.."`8 Gwenwarfar nickt dir zu und schiebt eine Feder auf deine Seite.`n`n');
						
						dg_show_guild_bio($gid);
															
						addnav('Ja, Bewerbung abgeben!','dg_council.php?op=apply&subop=ok&gid='.$gid);
						addnav('Nein, zur�ck!','dg_main.php');
					}
					
				}
				else {
					
					output('`8Stirnrunzelnd weist er dich zur�ck: `q"Du hast noch nicht gen�gend Drachen get�tet! Komm mit drei gr�nen Lindwurmk�pfen wieder, dann sehen wir weiter.."');
					addnav('Zum Gildenviertel','dg_main.php');
					
				}
				
				break;
				
			case 'ok':
				
				// Infomail verschicken
				$sql = 'SELECT acctid FROM accounts WHERE (guildfunc='.DG_FUNC_MEMBERS.' OR guildfunc='.DG_FUNC_LEADER.') AND guildid='.$gid.' ORDER BY guildfunc ASC, loggedin DESC LIMIT 1';
				$res = db_query($sql);
				$mailto = db_fetch_assoc($res);
				
				systemmail($mailto['acctid'],'`8Neue Gildenbewerbung!',$session['user']['name'].'`8 hat sich f�r die Mitgliedschaft bei deiner Gilde '.$guild['name'].'`8 beworben!');
				
				dg_add_member($gid,$session['user']['acctid'],true);
				
				output('`8Der Elf nimmt deine Bewerbung gleichm�tig entgegen und legt sie in das passende Fach. `q"Bis bald", `8verabschiedet er dich, `q", du wirst von der Gilde h�ren!"');
				
				addnav('Zum Gildenviertel','dg_main.php');
				
				break;
				
			case 'cancel':
				
				dg_remove_member($gid,$session['user']['acctid'],true);
				
				output('`q"Hm, deine Bewerbung zur�ckziehen also.. hmhm.."`8 seufzend greift Gwenmarfar in den Stapel und zerrei�t das Pergament vor deinen Augen `q"So in Ordnung? Dann auf bald!"');
				
				addnav('Zum Gildenviertel','dg_main.php');
				
				break;
		
		}	
		
		break;	// END apply

}	// END main switch

// jegliche Ver�nderung speichern
dg_save_guild();

if(su_check(SU_RIGHT_EDITORGUILDS)) { 
	addnav('Admin');
	addnav('Zum Gildeneditor','dg_su.php');
}

page_footer();
?>
