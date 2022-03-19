<?php
/*-------------------------------/
Name: dg_main.php
Autor: tcb / talion f�r Drachenserver (mail: t@ssilo.de)
Erstellungsdatum: 6/05 - 9/05
Beschreibung:	Hauptbestandteil des Gildensystems: �bernimmt den gr��ten Teil der Benutzerf�hrung, stellt Gildenviertel etc. dar		
				Diese Datei ist Bestandteil des Drachenserver-Gildenmods (DG). 
				Copyright-Box muss intakt bleiben, bei Verwendung Mail an Autor mit Serveradresse.
/*-------------------------------*/

require_once('common.php');
require_once(LIB_PATH.'dg_funcs.lib.php');
require_once('dg_output.php');

checkday();
page_header('Das Gildenviertel');

if($session['user']['guildid']) {
	$leader = ($session['user']['guildfunc'] == DG_FUNC_LEADER) ? true : false;
	$treasure = ($session['user']['guildfunc'] == DG_FUNC_LEADER || $session['user']['guildfunc'] == DG_FUNC_TREASURE) ? true : false;
	$war = ($session['user']['guildfunc'] == DG_FUNC_LEADER || $session['user']['guildfunc'] == DG_FUNC_WAR) ? true : false;
	$members = ($session['user']['guildfunc'] == DG_FUNC_LEADER || $session['user']['guildfunc'] == DG_FUNC_MEMBERS) ? true : false;
	$team = ($leader || $treasure || $war || $members) ? true : false;
	$member = ($session['user']['guildfunc'] != DG_FUNC_APPLICANT && $session['user']['guildfunc']) ? true : false;
	$applicant = ($session['user']['guildfunc'] == DG_FUNC_APPLICANT) ? true : false;
}

$op = ($_GET['op']) ? $_GET['op'] : '';
$out = '';

switch($op) {

	case '':	// Gildenviertel
		
		dg_show_header('Das Gildenviertel');
						
		output('`8Durch ein festes, breites Tor vom Rest des Dorfes abgetrennt befindet sich das noble Viertel der Gilden. Rundum ist es von einer hohen, steinernen Mauer umgeben, die kein unbefugtes Eindringen zul�sst. Doch auch von hier kannst du schon die hohen D�cher und den offensichtlichen Reichtum bewundern. Vor dem Tor halten zwei schwer bewaffnete Paladine des K�nigs in gl�nzender R�stung Wache. Ihre Hellebarden haben sie so gekreuzt, dass niemand an ihnen vorbeikommt.`n');	
		
		$entry = true;
		
		if($session['user']['dragonkills'] < 1) {
			output('`&"Was willst du denn hier, Bauernt�lpel?! Scher dich weg!"`8 mit diesen Worten weist dich der Paladin harsch zur�ck. Vermutlich solltest du mindestens einen Drachen get�tet haben, um �berhaupt Zutritt zu erlangen!');
			$entry = false;
		}
		elseif($session['user']['dragonkills'] < getsetting('dgmindkapply',3)) {
			output('Nur nach genauer Pr�fung gew�hren dir die Wachen Zutritt. Misstrauisch verfolgen sie noch lange deine Schritte, genau wissend, dass du hier eigentlich nichts verloren hast.');
		}
		elseif(!$member) {
			output('Die Paladine trauen dir nicht ganz und halten dich lange mit bohrenden Fragen nach deinen Absichten auf, ehe du endlich weitergehen kannst.');
		}
		elseif($member && !$team) {
			output('Die Wachen schenken dir keine weitere Beachtung, als du durch das Tor eilst. Einem Gildenmitglied vertrauen sie vollkommen.');
		}
		elseif($team) {
			output('W�rdevoll salutieren die Wachen, als du gesch�ftig vorbeieilst.');
		}
		
		if($entry) {
			
			$guild = array();
			if($member) {
				$guild = &dg_load_guild($session['user']['guildid'],array('name','top_repu'));
			}
			
			addcommentary();
									
			output('`8`nEhrfurchtsvoll betrittst du das Gildenviertel. Zu deiner Linken befindet sich der Gildenrat, das h�chste Gremium der Gilden '.getsetting('townname','Atrahor').'s. 
					Dort hast du auch Gelegenheit, die verschiedensten Listen zu betrachten und eine eigene Gilde zu gr�nden. 
					Zu beiden Seiten der breiten Stra�en liegen all die Prachtbauten der einzelnen Gilden. 
					`nEin Schild k�ndet von der n�chsten Lieferung des K�nigs in `^'.getsetting('dgkingdays',30).'`8 Tagen!
					`nWas hast du nun vor?`n`n');
			
			viewcommentary('guildquarter','Sprechen:',20,'spricht');				
			
			addnav('Gildenrat');
			addnav('Zum Ratssaal','dg_council.php?op=council');
			//addnav('Zur Paladinfestung','dg_council.php?op=paladin');
			
			addnav('Gilden');
			
			// Auf Einladung zu Gildenfest checken
			foreach($session['guilds'] as $g) {
				if($g['building_vars']['party']['eaten'][$session['user']['acctid']] && $session['user']['guildid'] != $g['guildid']) {
					addnav('Zu Gildenfest von '.$g['name'],'dg_main.php?op=in&subop=party&gid='.$g['guildid']);
				}
			}
							
			if($guild['name']) {
				addnav($guild['name'].'`0 betreten','dg_main.php?op=in&gid='.$session['user']['guildid']);
			}
			addnav('Liste der Gilden','dg_council.php?op=list');
			addnav('Ruhmeshalle der Gilden','dg_council.php?op=hof');									
									
		}
										
        addnav('Zur�ck');
        addnav('Zum Dorfplatz','village.php');
        addnav('Zum Marktplatz','market.php');

			
		break;
			
	case 'show_guild_bio':
						
		dg_show_guild_bio($_GET['gid']);
		
		addnav('Geschichte');
		addnav('Die Aufzeichnungen','dg_main.php?op=show_guild_history&gid='.$_GET['gid'].'&return='.($_GET['return'] ? $_GET['return'] : urlencode($ret_page)) );
		
		addnav('Sonstiges');
		
		if($_GET['return']) {
			addnav('Zur�ck',$_GET['return']);
		}
		else {
			addnav('Zur�ck',$ret_page);
		}
		
		if(su_check(SU_RIGHT_EDITORGUILDS)) {
			addnav('MOD-Aktionen');
			addnav('Gildeneditor','dg_su.php?op=edit&gid='.$_GET['gid']);
		}
						
		break;
		
	case 'show_guild_history':
		
		$guild = &dg_load_guild($_GET['gid'],array('name'));
		
		dg_show_header('Bisherige Geschichte der Gilde '.$guild['name']);
		
		show_history(2,$_GET['gid']);
		
		addnav('Zur�ck','dg_main.php?op=show_guild_bio&gid='.$_GET['gid'].'&return='.urlencode($_GET['return']));
		
		break;
					
	case 'in':
						
		// ID-Check
		if($_GET['gid']) {
			$session['gid'] = (int)$_GET['gid'];
		}
				
		if(!$session['gid']) {
			$session['gid'] = $session['user']['guildid'];
		}
		
		$gid = $session['gid'];
		
		if(!$gid) {redirect('dg_main.php');}	
		// END ID-Check
		
		$subop = ($_GET['subop']) ? $_GET['subop'] : '';
		
		$guild = &dg_load_guild($gid,array(),true);
		
		$founder = ($session['user']['acctid'] == $guild['founder']) ? true : false;
						
		if($member) {	
			dg_show_state_info($gid);				
		}
		
		switch($subop) {
			
			case '':	// Gildenhalle
				
				$show_invent = true;
				
				addcommentary();
	
				$sql = 'SELECT n.newstext FROM news n WHERE n.guildid='.$gid.' ORDER BY newsid DESC LIMIT 1';
				$res = db_query($sql);
				$n = db_fetch_assoc($res);
															
				dg_show_header($guild['name'].'`8 - Gildenhalle');
				
				// Zutritt in Hinterzimmer?
				$private = (su_check(SU_RIGHT_DEBUG) ? true : false);
				if(!$team) {
														
					if(item_count(' tpl_id="gldprive" AND value1='.$gid.' AND owner='.$session['user']['acctid'])) {$private = true;}
					
				}
				
				
				if($guild['state'] == DG_STATE_INACTIVE) {
					output('`8`b`cDiese Gilde befindet sich im inaktiven Zustand, da entweder keine F�hrungsmitglieder oder weniger als '.getsetting('dgminmembers',3).' Mitglieder insgesamt vorhanden sind. Eventuell hat diese Gilde auch gegen die Regeln versto�en. Bei Fragen wendet euch per Anfrage an die Moderatoren. Versucht, etwaige Mi�st�nde zu beheben. Die Administration wird, falls keine L�sung m�glich ist, die Gilde l�schen. So lange bleiben alle Aktionen bis auf den Mitgliederbereich deaktiviert.`8`b`c`n');
					addnav('Mitglieder & R�nge','dg_main.php?op=in&subop=members');
					if($team || $private) { addnav('Hinterzimmer','dg_main.php?op=in&subop=private'); }
					addnav('Gildenprofil','dg_main.php?op=in&subop=bio');
				}
				else {
					addnav('Verwaltung');
					addnav('Mitglieder & R�nge','dg_main.php?op=in&subop=members');
					addnav('Diplomatie & Kampf','dg_main.php?op=in&subop=war');
					addnav('Schatz & Ausbau','dg_main.php?op=in&subop=treasure');
					addnav('Zum Gew�lbe hinabsteigen','dg_main.php?op=in&subop=buildings&building_op=deposit');
					if($guild['ext_room_name'] != '') {
						addnav($guild['ext_room_name'],'dg_main.php?op=in&subop=ext_room');
					}
					if($team || $private) { addnav('Hinterzimmer','dg_main.php?op=in&subop=private'); }
					addnav('Information');
					addnav('Schwarzes Brett','dg_main.php?op=in&subop=board');
					addnav('Gildenprofil','dg_main.php?op=in&subop=bio');
					addnav('Gildenalmanach','dg_main.php?op=in&subop=buildings&building_op=explain');
				}			
				addnav('Austreten','dg_main.php?op=in&subop=leave');
				
				// Weitesten Ausbau anzeigen
				$best_building = dg_get_max_build($gid);
								
				output('`8'.$dg_child_types[$guild['type']][2].($best_building?'`n`8Besonders sticht ein Teil des Geb�udes namens `i'.$dg_builds[$best_building]['color'].$dg_builds[$best_building]['name'].'`8`i im ungef�hren Zustand `i'.$dg_build_levels[ $guild['build_list'][$best_building] ].'`i hervor.':'').'`8`n`n');
				
				output('`c');
				if($n) {output('`8Letzte Neuigkeit: `i'.$n['newstext'].'`i`n`n');}
				else {output('`8Es gibt keine Neuigkeiten!`n`n');}
				output('`c');
				
				if($guild['vote']) {output('Ihre '.$guild['regalia'].' Stimmen gibt die Gilde f�r einen Steuersatz von '.$guild['vote'].' %.`n`n');}
				
				// M�bel
				dg_show_furniture('hall');
				// END M�bel
				
				viewcommentary('guild-'.$gid,'Mit den anderen Gildenmitgliedern sprechen: ',25,'spricht');
												
				addnav('Zur�ck');
				addnav('Zum Gildenviertel','dg_main.php');
				addnav('Zum Dorfplatz','village.php');
				addnav('Zum Marktplatz','market.php');
				
				// feststellen, welche Ausbauten verf�gbar sind
				$without_op = false;
				if($best_building > 0 && $guild['state'] != DG_STATE_INACTIVE) {
					addnav('Ausbauten');
					
					foreach($dg_builds as $id => $b) {
						if($guild['build_list'][$id]) {
							if($b['op'] != '') {addnav($b['name'],'dg_main.php?op=in&subop=buildings&building_op='.$b['op']);}
							else {$without_op = true;}
						}	
						
					}
					if($without_op) {addnav('Sonstige','dg_main.php?op=in&subop=buildings&building_op=na');}
			
				}
				
				if($guild['building_vars']['party']['gang']) {
					addnav('Besonderes');
					addnav('Zum Gildenfest!','dg_main.php?op=in&subop=party');
				}
																
				break;
			
			case 'party':
				
				$speisen = array(1=>'Pilzsuppe',2=>'Schweinebraten',3=>'Weintrauben');
				$getraenke = array(1=>'Cedriks\' Ale',2=>'LOKIs Zwergenmet',3=>'Drachenschnaps');
				
				dg_show_header('Gildenfest');
				
				if(addcommentary()) {	// Zufallskommentare
					switch(e_rand(1,20)) {
						case 1:
							if($guild['building_vars']['party']['musik'] == 1) {
								dg_commentary($gid,'/msg `^Es ert�nt eine malerische Harfenmelodie.','party',1);
							}
							break;
							
						case 2:
							if($guild['building_vars']['party']['tanz'] == 1) {
								dg_commentary($gid,'/msg `^Grazil bewegen sich die T�nzerinnen zur Musik.','party',1);
							}
							break;
							
						case 3:
							if($guild['building_vars']['party']['gaukler'] == 1) {
								dg_commentary($gid,'/msg `^Einer der Gaukler springt von Tisch zu Tisch und jongliert mit einigen B�llen.','party',1);
							}
							break;
						
					}
				}
								
				if($_GET['act'] == 'start') {
					
					$link = 'dg_main.php?op=in&subop=party&act=start';
																									
					if($guild['points'] < 3) {
						output('Das kann sich die Gilde nicht leisten!');
						addnav('Zur�ck','dg_main.php?op=in');
					}
					else {
						$last = $guild['building_vars']['party']['last'];
						$diff = strtotime(getsetting('gamedate','')) - strtotime($last);
						$diff /= 86400;
						if($diff < 30) {
							output('`8Die letzte Feier deiner Gilde war am '.getgamedate($last).', heute ist der '.getgamedate(getsetting('gamedate','')).'. Noch nicht mal ein Monat und du willst schon wieder feiern?! Arbeite erst mal!');
							addnav('Zur�ck','dg_main.php?op=in&subop=gpshop');
						}
						else {
							output('`8Auf deinen Befehl hin werden St�hle und Tische herangeschafft, selbige mit Unmengen an leckeren Speisen beladen. Weinf�sser rollt man herbei, auf dass es der Gesellschaft nicht an trinkfesten Gen�ssen mangele. Und zu guter Letzt halten sich auch noch einige Gaukler und �hnliches lustiges Gesinde im Hintergrund: Das Fest kann beginnen!');
							$guild['points'] -= 3;
							$guild['points_spent'] += 3;
							$guild['building_vars']['party']['gang'] = 1; 
							$guild['building_vars']['party']['last'] = getsetting('gamedate',''); 
							$guild['building_vars']['party']['musik'] = 0;
							$guild['building_vars']['party']['tanz'] = 0;
							$guild['building_vars']['party']['gaukler'] = 0;
							
							// Ehepartner, Verlobte d�rfen auch teilnehmen, so lange sie nicht in feindlicher Gilde sind
							// Mitglieder befreundeter Gilden sowieso
							$not = '';
							$yeah = '';
							if(is_array($guild['treaties'])) {
								foreach($guild['treaties'] as $id=>$t) {
									if(dg_get_treaty($t) == -1) {
										$not .= ','.$id;
									}
									elseif(dg_get_treaty($t) == 1) {
										$yeah .= ','.$id;
									}
								}
							}
							
							$sql = 'SELECT b.acctid FROM accounts a LEFT JOIN accounts b ON ( b.acctid=a.marriedto AND b.marriedto=a.acctid AND a.charisma>=999 AND (b.guildid = 0 OR b.guildid NOT IN (0'.$not.') ) WHERE a.guildid='.$gid;
							$res = db_query($sql);
							while($a = db_fetch_assoc($res)) {
								$guild['building_vars']['party']['eaten'][$a['acctid']] = 0;
							}
							
							$sql = 'SELECT a.acctid FROM accounts a WHERE guildid IN (-1'.$yeah.')';

							$res = db_query($sql);
							while($a = db_fetch_assoc($res)) {
								$guild['building_vars']['party']['eaten'][$a['acctid']] = 0;
							}
							
							addnav('Los!','dg_main.php?op=in&subop=party');
							dg_addnews('`8Die Gilde '.$guild['name'].'`8 feiert heute ein gro�es Fest!');
						}
					}
									
				}	// END if act
				
				elseif($_GET['act'] == 'stop') {
					
					$guild['building_vars']['party']['gang'] = 0;
					$guild['building_vars']['party']['musik'] = 0;
					$guild['building_vars']['party']['tanz'] = 0;
					$guild['building_vars']['party']['gaukler'] = 0;
					unset($guild['building_vars']['party']['eaten']);
					addnav('Zur�ck','dg_main.php?op=in');
				
				}
								
				elseif($_GET['act'] == 'eat') {
					
					$speise = $speisen[$guild['building_vars']['party']['gang']];
					
					if($guild['building_vars']['party']['eaten'][$session['user']['acctid']] >= 10) {
						output('`8Du gibst alles, bringst aber beim besten Willen keinen Bissen von '.$speise.' mehr rein!`n`n');
						addnav('Weiterfeiern','dg_main.php?op=in&subop=party');
					}
					else {
						$guild['building_vars']['party']['eaten'][$session['user']['acctid']] += $guild['building_vars']['party']['gang'];
						$session['user']['hitpoints'] *= 1.05;
						dg_commentary($gid,': bedient sich an '.$speise.'!','party');
						dg_save_guild();
						redirect('dg_main.php?op=in&subop=party');
					}
					
				}
				
				/*elseif($_GET['act'] == 'invite') {
					$not = '';
					if(is_array($guild['treaties'])) {
						foreach($guild['treaties'] as $id=>$t) {
							if(dg_get_treaty($t) == -1) {
								$not .= ','.$id;
							}
						}
					}
					
					$sql = 'SELECT name FROM accounts WHERE loggedin=1 AND guildid NOT IN (-1'.$not.') ORDER BY name ASC';
					
				}*/
				
				elseif($_GET['act'] == 'drink') {
					
					$prison = false;
					
					if($session['user']['drunkenness'] < 99) {				
						output('`8Du nimmst einen kr�ftigen Schluck von '.$getraenke[$_GET['what']].'. Kurze Zeit sp�ter bemerkst du die berauschende Wirkung!');
						$session['user']['drunkenness']+=5;
	
						if($session['user']['drunkenness'] >= 99) {
							if($session['user']['race'] == RACE_ZWERG) {
								if(e_rand(1,4) == 1) {
									$prison = true;
								}
							}
							else {
								if(e_rand(1,2) == 1) {
									$prison = true;
								}
							}
						}
												
					}
					else {
						output('`8Du solltest besser nichts mehr trinken. Jeder weitere Schluck w�rde dich unweigerlich dem Tode nahebringen!');
					}
					
					if($prison == true) {
						output("`8Du hast zwar zuviel gesoffen, es aber gerade noch �berlebt. Du erwachst in der Ausn�chterungszelle.`n");
						output("Du verlierst den Gro�teil Deiner Lebenspunkte!");
						$session['user']['hitpoints']=1;
						$session['user']['imprisoned']=1;
						addnews($session['user']['name']." entging nur knapp den Folgen einer Alkoholvergiftung und verbringt die Nacht in der Ausn�chterungszelle.");
						addnav("Weiter","prison.php"); 
					}
					else {
						addnav('Weiterfeiern','dg_main.php?op=in&subop=party');
					}
										
				}
				
				elseif($_GET['act'] == 'serve') {
							
					if(is_numeric($_GET['what'])) {
						$speise = $speisen[$_GET['what']];
						output('Du servierst '.$speise.'!');										
						$guild['building_vars']['party']['gang'] = $_GET['what'];										
						dg_commentary($gid,'/msg '.$speise.' wird aufgetragen!','party',1);
					}
					else {
						if($_GET['what'] == 'musik') {
							if($guild['building_vars']['party']['musik'] == 1) {
								dg_commentary($gid,'/msg Die Musiker packen ihre Instrumente und verschwinden.','party',1);
								$guild['building_vars']['party']['musik'] = 0;	
							}
							else {
								$guild['points']--;
								$guild['points_spent']++;
								dg_commentary($gid,'/msg `^Die Musiker kommen mit Harfen hereinstolziet.','party',1);
								$guild['building_vars']['party']['musik'] = 1;
							}	
																	
						}
						elseif($_GET['what'] == 'tanz') {
							if($guild['building_vars']['party']['tanz'] == 1) {
								dg_commentary($gid,'/msg Die T�nzerinnen verschwinden.','party',1);
								$guild['building_vars']['party']['tanz'] = 0;	
							}
							else {
								$guild['points']--;
								$guild['points_spent']++;
								dg_commentary($gid,'/msg `^Die T�nzerinnen erscheinen mit sanften Schritten.','party',1);
								$guild['building_vars']['party']['tanz'] = 1;
							}											
						}
						elseif($_GET['what'] == 'gaukler') {
							if($guild['building_vars']['party']['gaukler'] == 1) {
								dg_commentary($gid,'/msg Die Gaukler springen hinaus.','party',1);
								$guild['building_vars']['party']['gaukler'] = 0;	
							}
							else {
								$guild['points']--;
								$guild['points_spent']++;
								dg_commentary($gid,'/msg `^Die Gaukler kugeln herein.','party',1);
								$guild['building_vars']['party']['gaukler'] = 1;
							}											
						}
					
					}	// END special
										
					dg_save_guild();
					redirect('dg_main.php?op=in&subop=party');										
				}
				
				else {																
					$link = 'dg_main.php?op=in&subop=party&act=';
					
					output('`8Schon immer war deine Gilde ber�chtigt f�r ihre gro�en, prunkvollen und vor allem lustigen Feste und wieder musst du feststellen, dass sie sich noch immer blendend auf dieses Handwerk versteht.
							 Der ganze Raum ist gef�llt mit allen Gildenmitgliedern und du sitzt mitten an einer riesigen Tafel, die gedeckt ist mit dem feinsten Porzellan und den wertvollsten Kelchen, aus denen dir der Duft eines herrlichen Weins in die Nase steigt. Doch am meisten lassen dir die k�stlich aussehenden Speisen den Mund im Wasser zusammenlaufen.
							 W�hrend du dir reichlich auf den Teller packst und hastig beginnst, alles hinunter zu schlingen und gelegentlich ein wenig Wein zu trinken, tauchen pl�tzlich hinter unz�hligen Vorh�ngen luftig gekleidete T�nzer und T�nzerinnen auf, die anfangen elegant zu der fr�hlichen Musik des Volkes zu tanzen, die mehrere Musikanten angestimmt haben. 
							 V�llig ergriffen von dem sch�nen Tanz und nat�rlich den h�bschen T�nzern, aber besonders den T�nzerinnen, h�ttest du beinahe zwei Gaukler �bersehen, die etwas abseits der Tafel beginnen, Feuer zu speien und mit Keulen und B�llen zu jonglieren. Nur mit halbem Ohr verfolgst du nebenbei noch die vielen Gespr�che am Tisch, die Witze, die hier und da schallendes Gel�chter hervorrufen und nat�rlich die Erz�hlungen von ruhmreichen Taten.`n`n');
					
					$last = $guild['building_vars']['party']['last'];
					$diff = strtotime(getsetting('gamedate','')) - strtotime($last);
					$diff /= 86400;	// SpielTage
					
					if($diff >= 2) {
						output('`8Diese Feier dauert bereits zu lang. Ihr solltet euch mal wieder auf den Ernst des Lebens konzentrieren!`n`n');
					}
					else {
						addnav('Getr�nke');
					
						foreach($getraenke as $nr=>$g) {
							addnav($g,$link.'drink&what='.$nr);
						}
						
						addnav('Speisen');		
						
						if($guild['building_vars']['party']['gang']) {					
							addnav($speisen[ $guild['building_vars']['party']['gang'] ].' nehmen',$link.'eat&what='.$guild['building_vars']['party']['gang']);					
						}
					}
									
					viewcommentary('guild-'.$gid.'_party');
																						
					if($team) {
						
						addnav('Ablauf');
						
						if($diff >= 2) {
							addnav('Feier beenden!',$link.'stop');					
						}
						else {
						
							if($guild['building_vars']['party']['gang'] == 1) {					
								addnav($speisen[2].' hereinbringen!',$link.'serve&what=2');					
							}
							elseif($guild['building_vars']['party']['gang'] == 2) {					
								addnav($speisen[3].' hereinbringen!',$link.'serve&what=3');					
							}
							else {
								addnav('Feier beenden!',$link.'stop');					
							}						
													
							if($guild['points'] > 0) {
								addnav('Aktionen (1 Gildenpunkt)');
								if($guild['building_vars']['party']['musik'] == 0) {addnav('Musiker aufspielen lassen!',$link.'serve&what=musik');}
								else {addnav('Musiker wegschicken!',$link.'serve&what=musik');}					
								
								if($guild['building_vars']['party']['tanz'] == 0) {addnav('T�nzer aufspielen lassen!',$link.'serve&what=tanz');}
								else {addnav('T�nzerinnen wegschicken!',$link.'serve&what=tanz');}
								
								if($guild['building_vars']['party']['gaukler'] == 0) {addnav('Gaukler aufspielen lassen!',$link.'serve&what=gaukler');}
								else {addnav('Gaukler wegschicken!',$link.'serve&what=gaukler');}
							}
						}
																		
					}
					
					addnav('Wege');
					if($session['user']['guildid'] == $gid) {
						addnav('Zur Halle','dg_main.php?op=in');
					}
					else {
						addnav('Zum Gildenviertel','dg_main.php');
					}
				}
				
				break;
			
			case 'leave':
				
				if($_GET['act'] == 'ok') {
					
					dg_remove_member($gid,$session['user']['acctid']);
					dg_commentary($gid,': ist aus der Gilde ausgetreten.','',$session['user']['acctid']);	
					dg_addnews($session['user']['name'].'`8 hat die Gilde '.$guild['name'].'`8 verlassen.',$session['user']['acctid']);
					
					addhistory('`2Austritt aus Gilde '.$guild['name']);
					
					output('`8Mit einem leicht flauen Gef�hl im Bauch �berreichst du dein K�ndigungsschreiben. Suefzend denkst du an all die sch�nen Stunden zur�ck, die du hier verbracht hast, l�sst deinen Blick zum letzten Mal durch die Halle schweifen - und machst dich dann auf den Weg zur�ck ins Dorf.'); 
										
					addnav('Zum Dorf','village.php');
					
				}
				else {
					
					// �berpr�fen, ob es noch andere F�hrungsmitglieder gibt
					$ok = false;
					if($leader) {
						$sql = 'SELECT acctid FROM accounts WHERE guildid='.$gid.' AND guildfunc='.DG_FUNC_LEADER;
						$res = db_query($sql);
						if(db_num_rows($res) <= 1) {
							output('`8Du kannst diese Gilde nicht verlassen, da sie sonst ohne F�hrung w�re! Sorge erst f�r einen Nachfolger auf deinem Posten.');
							addnav('Zur�ck','dg_main.php?op=in');
						}
						else {
							$ok = true;
						}
					}
					else {$ok=true;}
					
					// Gr�nder darf nicht austreten
					if($session['user']['acctid'] == $guild['founder']) {
						output('`8Du hast diese Gilde gegr�ndet und nun willst du sie im Stich lassen? Das kannst du nicht tun!');
						addnav('Zur�ck','dg_main.php?op=in');
						$ok = false;
					}
					
					if($ok) {
					
						output('`4Willst du deine Mitgliedschaft in der Gilde wirklich k�ndigen?');
						
						addnav('Nein, zur�ck','dg_main.php?op=in');	
						addnav('Ja!','dg_main.php?op=in&subop=leave&act=ok');	
					}
					
				}
				
				break;
			
			// Spieler einladen, der Gilde beizutreten
			case 'invite':
				
				$min_dks = getsetting('dgmindkapply',3);
															
				if(strlen($_POST['search']) > 0) {	
													
					$count = strlen($_POST['search']);
					$search="%";
					for ($x=0;$x<$count;$x++){
						$search .= substr($_POST['search'],$x,1)."%";
					}
					
					$sql = 'SELECT name,acctid FROM accounts WHERE name LIKE "'.$search.'" 
							AND acctid!='.$session['user']['acctid'].' AND guildid=0 AND guildfunc=0 AND dragonkills >= '.$min_dks;
					$res = db_query($sql);
					
					if(db_num_rows($res) == 0) {
						output('`8Kein m�gliches Ziel mit diesem Namen gefunden!');
					}
					else {	
						output('`8Diese B�rger von '.getsetting('townname','Atrahor').'`8 treffen auf deine Suche zu und k�nnen Mitglied in deiner Gilde werden:`n`n');
					
						$link = 'dg_main.php?op=in&subop=invite';
						
						output('<form action="'.$link.'" method="POST">',true);
				
						output(' <select name="ziel">',true);
												
						while ( $p = db_fetch_assoc($res) ) {
				
							output('<option value="'.$p['acctid'].'">'.preg_replace("'[`].'","",$p['name']).'</option>',true);
				
						}
				
						output('</select>`n`n',true);
				
						output('<input type="submit" class="button" value="Einladen!"></form>',true);
						addnav('',$link);
					}
					addnav('Neue Suche','dg_main.php?op=in&subop=invite');
				}	// END if search
				
				elseif($_POST['ziel']) {
					
					$ziel = ($_POST['ziel'] ? (int)$_POST['ziel'] : (int)$_GET['ziel']);
					
					$rec = db_fetch_assoc(db_query('SELECT name,acctid FROM accounts WHERE acctid='.$ziel));
					
					if(!empty($_POST['msg'])) {
															
						$str_msg = '`8Die Botschaft scheint dir die Gilde '.$guild['name'].'`8 zu senden.. es handelt sich dabei wohl
										um ein Einladungsschreiben!`n 
										Als du das Siegel aufbrichst, kannst du lesen:`n`n
										'.$_POST['msg'].'`n`n
										`@Um diese Einladung anzunehmen, begib dich ins Gildenviertel.';
						
						systemmail($ziel,'`8Eine Botschaft!',$str_msg);
						
						$sql = 'UPDATE accounts SET guildfunc='.DG_FUNC_INVITED.',guildrank='.$gid.' WHERE acctid='.$ziel.' AND guildid=0 AND guildfunc=0 AND dragonkills >= '.$min_dks;
						db_query($sql);
						
						output('`8Der Bote macht sich eilends auf den Weg. Bald schon wird die Botschaft ihren Empf�nger erreichen!');					
					}
					else {
						
						$arr_form = array(	
											'name'=>'An:,viewonly',
											'msg'=>'Nachricht:'
										);
										
						$link = 'dg_main.php?op=in&subop=invite&ziel='.$ziel;
						addnav('',$link);
						output('<form action="'.$link.'" method="POST">',true);
						showform($arr_form,$rec,false,'Botschaft absenden!');
						output('</form>',true);
						
					}
					
					
				}	// END if ziel
				else {
					$link = 'dg_main.php?op=in&subop=hitlist&act=add';
					output('`8Du kannst nur diejenigen B�rger einladen, die bisher gildenlos sind,
							weder k�rzlich aus einer Gilde ausgetreten, noch von einer anderen eingeladen sind.
							Weiterhin m�ssen sie mindestens '.$min_dks.' Drachenkills besitzen!`n');
				
					output('<form action="'.$link.'" method="POST">',true);
		
					output('Name: <input type="input" name="search">',true);
											
					output('`n`n',true);
			
					output('<input type="submit" class="button" value="Suchen"></form>',true);
					addnav('',$link);
				}
				addnav('Zur�ck','dg_main.php?op=in&subop=hitlist');
			
			break;
					
			case 'treasure':
				
				if($treasure || $leader) {addcommentary();}
											
				$taxdays = (int)getsetting('dgtaxdays',12);
				$max_tax_fails = (int)getsetting('dgmaxtaxfails',12);
				
				$taxdays_left = $taxdays - ($guild['taxdays'] % $taxdays);
				$tax_fails = floor( $guild['taxdays'] / $taxdays );
							
				if($_GET['act'] == 'lock') {
					$guild['treasure_locked'] = 1;
					dg_commentary($gid,': `4sperrt die Schatzkammer f�r alle Auszahlungen!','treasure');
					dg_save_guild();
					redirect('dg_main.php?op=in&subop=treasure');
				}
				
				elseif($_GET['act'] == 'unlock') {
					$guild['treasure_locked'] = 0;
					dg_commentary($gid,': `@gibt die Schatzkammer wieder frei.','treasure');
					dg_save_guild();
					redirect('dg_main.php?op=in&subop=treasure');
				}
												
				dg_show_header('Schatzkammer');
				
				output('`8Du musst nur zielstrebig dem Funkeln folgen, das die Schatzkammer in den Gang aussendet, schon findest du jenen Raum, der von gro�er Bedeutung f�r die Gilde ist. Hier lagern all die Sch�tze, hier werden die wichtigsten Transaktionen getroffen. Sieh dich ruhig um, aber sei ma�voll bei dem was du mit dir nimmst:`n`n');
				
				output('`c`8<table bgcolor="#999999" border="0" cellpadding="3" cellspacing="1"><tr class="trhead"><td>Gold</td><td>Edelsteine</td><td>Gildenpunkte (ausgegeben)</td><td>Insignien</td></tr>',true);
											
				output('<tr class="trlight"><td>'.$guild['gold'].'</td><td>'.$guild['gems'].'</td><td>'.$guild['points'].' ('.$guild['points_spent'].')</td><td>'.$guild['regalia'].'</td></tr></table>`n`n',true);
				
				$tax = &dg_calc_tax($gid);
				
				if(!$guild['taxfree_allowed']) {
					output('`8Steuern (`^'.$tax['gold'].'`8 Gold, `^'.$tax['gems'].'`8 Edelsteine) sind f�llig in `4'.$taxdays_left.'`8 Tagen!`n');				
					if($tax_fails == 1) {
						output('`4`bDie Gilde hat bereits einmal vers�umt, ihre Steuern zu entrichten. Falls dies noch einmal geschieht, wird ein Ausbau gepf�ndet!`b`8`n`n');
					}
					elseif($tax_fails == 2) {
						output('`4`bDie Gilde hat bereits zweimal vers�umt, ihre Steuern zu entrichten. Falls dies noch einmal geschieht, wird `idie Gilde aufgel�st`i!`8`b`n`n');
					}
				}
				else {
					output('Die Gilde ist von s�mtlichen Steuerzahlungen befreit!`n`n');
				}
				
				$tribute = dg_member_tribute($gid,0,0,false);
				output('`8Zur Zeit m�sssen die Mitglieder `^'.$tribute.'`8 % ihrer Wald- und Schlossertr�ge als Tribut an die Gilde abtreten!`n'); 
				
				$maxgold = dg_calc_boni($gid,'treasure_maxgold',getsetting('dgtrsmaxgold',100000));
				$maxgems = dg_calc_boni($gid,'treasure_maxgems',getsetting('dgtrsmaxgems',1000));
				
				$maxgold_left = dg_calc_max_transfer_in($gid,'gold');
				$maxgems_left = dg_calc_max_transfer_in($gid,'gems');
								
				output('`8In den Truhen ist noch Platz f�r `^'.max($maxgold - $guild['gold'],0).'`8 Gold und `^'.max($maxgems - $guild['gems'],0).'`8 Edelsteine.`n
							Heute k�nnen noch `^'.$maxgold_left.'`8 Gold und `^'.$maxgems_left.'`8 Edelsteine eingezahlt werden!`n`n`c');
								
				viewcommentary('guild-'.$gid.'_treasure',($team ? 'Etwas verk�nden:':'Du solltest hier besser schweigen!'),25,'verk�ndet',false,($team?true:false));
				
				$link = 'dg_main.php?op=in&subop=treasure&transferlist_old=';
								
				output('`n`n');
				
				if($_GET['transferlist_old']) {
					$link .= '0';
					output('[ <a href="'.$link.'">Nicht-Mitglieder ausblenden</a> ]',true);
				}
				else {
					$link .= '1';
					output('[ <a href="'.$link.'">Nicht-Mitglieder anzeigen</a> ]',true);
				}
				
				addnav('',$link);
								
				// Transferliste
				dg_show_transfer_list($gid,0,$_GET['transferlist_old']);
												
				addnav('Gold');
				addnav('Einzahlen','dg_main.php?op=in&subop=transfer&act=gold&in=1');
				if($leader || $treasure) {
					addnav('Auszahlung','dg_main.php?op=in&subop=donate&what=Gold');
				}
								
				addnav('Edelsteine');
				addnav('Einzahlen','dg_main.php?op=in&subop=transfer&act=gems&in=1');
				if($leader || $treasure) {
					addnav('Auszahlung','dg_main.php?op=in&subop=donate&what=Edelsteine');
				}
								
				addnav('Gildenpunkte');
				addnav('Zum Gildenpunkth�ndler','dg_main.php?op=in&subop=gpshop');
												
				addnav('Ausbau');
				addnav('Ausbauten','dg_main.php?op=in&subop=builds');
				
				addnav('Verschiedenes');
				addnav('Zur Halle','dg_main.php?op=in');
								
				break;
				
			case 'gpshop':
				
				$gp_price_buy = getsetting('dggpgoldcost',30000) * 5;
				$gp_price_regalia = dg_calc_boni($gid,'regalia_buy', (getsetting('dgregaliagpcost',100) * ($guild['regalia']+1)) );
				$gp_price_guardhp = dg_calc_boni($gid,'guardhp_buy',0.25);
				//$gp_price_guardhp += floor($guild['guard_hp'] / 50) * 2;
				//$gp_price_guardhp += 5 - ceil($guild['reputation'] / 10);
								
				$int_maxguards = dg_calc_boni($gid,'maxguards',0);
								
				$gp_price_buy_room = 50;
				
				if($_GET['act'] == 'gp_buy') {
					
					$guild['points'] += 5;
					$guild['gold'] -= $gp_price_buy;
					dg_commentary($gid,': `4erwirbt `^5`4 Gildenpunkte f�r `^'.$gp_price_buy.'`4 Gold','treasure');
					dg_log('5 GP f�r '.$gp_price_buy.' Gold');
					dg_save_guild();
					redirect('dg_main.php?op=in&subop=treasure');
					
				}
				elseif($_GET['act'] == 'gp_buy_guardhp') {
					$int_count = (int)$_POST['count'];
					
					if($int_maxguards < $guild['guard_hp'] + $int_count) {
						output('`$Eine derartige Anzahl von S�ldnern f�nde in den Kasernen deiner Gilde keinen Platz!');				
					}
					else {
					
						$int_price = ceil($gp_price_guardhp * $int_count);
						if($int_price >= $guild['points']) {
							output('`$Eine derartige Anzahl von S�ldnern kann sich deine Gilde leider einfach nicht leisten!');
						}
						else {
					
							$guild['guard_hp'] += $int_count;
							$guild['points'] -= $int_price;
							$guild['points_spent'] += $int_price;
							dg_commentary($gid,': `4wirbt `^'.$int_count.'`4 Gildenwachen f�r `^'.$int_price.'`4 Gildenpunkte an, die ab sofort der Gilde zur Seite stehen.','treasure');
							dg_save_guild();
							redirect('dg_main.php?op=in&subop=treasure');										
						}
					}
				}
				elseif($_GET['act'] == 'gp_buy_room') {
					$guild['points'] -= $gp_price_buy_room;
					$guild['points_spent'] += $gp_price_buy_room;
					dg_commentary($gid,': `4erwirbt `^ein zus�tzliches Gemach`4 f�r `^'.$gp_price_buy_room.'`4 Gildenpunkte','treasure');
					
					$guild['ext_room_name'] = 'Salon';
					$guild['ext_room_desc'] = 'Ein zus�tzliches, vornehmes Gemach.';
					
					dg_save_guild();
					redirect('dg_main.php?op=in&subop=treasure');										
				}
							
				dg_show_header('Gildenpunkte: An- und Verkauf');
				
				output('`c`8<table bgcolor="#999999" border="0" cellpadding="3" cellspacing="1"><tr class="trhead"><td>Gold</td><td>Edelsteine</td><td>Gildenpunkte (ausgegegeben)</td><td>Insignien</td></tr>',true);
				output('<tr class="trlight"><td>'.$guild['gold'].'</td><td>'.$guild['gems'].'</td><td>'.$guild['points'].' ('.$guild['points_spent'].')</td><td>'.$guild['regalia'].'</td></tr></table>`n`n',true);
				
				output('`8Einige Tische der Schatzkammer wurden von Gold und Edelsteinen befreit, um Platz f�r den Gildenpunkth�ndler zu schaffen. Er ist die wichtigste Anlaufsstelle, wenn es um Angelegenheiten der offiziellen Gildenw�hrung '.getsetting('townname','Atrahor').'s geht. Hier siehst du seine Angebote:`n`n');
				
				$link = 'dg_main.php?op=in&subop=gpshop&act=gp_buy';
				output('`T`^F�nf`T Gildenpunkte f�r `^'.$gp_price_buy.'`T Gold: "`tDie beste Wahl, falls deiner Gilde die Schatzkammern �berquellen, du Gold wirklich nicht mehr sehen kannst und / oder ihr dringend Punkte ben�tigt!"`n');
				if($guild['gold'] >= $gp_price_buy) {
					if($leader || $treasure) {
						output('<a href="'.$link.'">Kaufen!</a>',true);
						addnav('',$link);
					}
				}
				else {output('Die Goldvorr�te reichen leider nicht aus!');}
				
				$link = 'dg_main.php?op=in&subop=gpshop&act=gp_buy_guardhp';
												
				output('`n`n`TMaximal `^'.($int_maxguards - $guild['guard_hp']).'`T S�ldner f�r `^'.$gp_price_guardhp.'`T Gildenpunkte pro Einheit anwerben (Mindestkosten 1 Punkt): "`tSo eine Gildenwache kann bekanntlich nie stark genug sein. Also habt ihr hier die M�glichkeit, auch k�nftig ungebetene Besucher aus der Schatzkammer drau�en zu halten!"`n');
				if($leader || $treasure) {
					output('<form method="POST" action="'.$link.'">
								Wie viele S�ldner willst du in den Dienst deiner Gilde stellen?`n <input type="text" size="3" maxlength="3" name="count" value="0">`n`n 
								<input type="submit" value="Anheuern!">
							</form>',true);
					addnav('',$link);
					
				}			
				
				$link = 'dg_main.php?op=in&subop=party&act=start';
				output('`n`n`T Gildenfest f�r `^3`T Gildenpunkte: "`tWas gibt es sch�neres, als z�nftiges Zusammensein bei einem gem�tlichen Fest?"`n');
				if($guild['points'] >= 3) {
					if($leader || $treasure) {
						output('<a href="'.$link.'">Beginnen!</a>',true);
						addnav('',$link);
					}
				}
				else {output('Die Anzahl der Gildenpunkte reicht leider nicht aus!');}
				
				if($guild['ext_room_name'] == '') {
					$link = 'dg_main.php?op=in&subop=gpshop&act=gp_buy_room';
					output('`n`n`T Zus�tzliches Gemach f�r `^'.$gp_price_buy_room.'`T Gildenpunkte: "`tEure Baumeister gestalten ein v�llig neues Gemach in der Gildenhalle nach euren W�nschen!"`n');
					if($guild['points'] >= $gp_price_buy_room) {
						if($leader || $treasure) {
							output('<a href="'.$link.'">Bauen!</a>',true);
							addnav('',$link);
						}
					}
					else {output('Die Anzahl der Gildenpunkte reicht leider nicht aus!');}
				}
				
				output('`n`n`b`tGildenpunkte erh�lt oder verbraucht die Gilde au�erdem bei folgenden Gelegenheiten:`b`n`n');
				output(' - `tDrachenkill : `^'.$dg_points['dk'].'`T Punkte`n');  
				output(' - `tEinmalige Kosten eines Gildenkrieges : `4 - `^'.$dg_points['war_cost'].'`T Punkte`n');  				
				output(' - `tHochzeit mit befreundeter Gilde : `^'.$dg_points['wedding_friendly'].'`T Punkte`n');  				
				output(' - `tHochzeit mit neutraler Gilde : `^'.$dg_points['wedding_neutral'].'`T Punkte`n');  				
				
				output('`c');		
				
				addnav('Zur�ck zur Schatzkammer','dg_main.php?op=in&subop=treasure');
								
				break;
			
			case 'transfer':
												
				$maxgold = dg_calc_boni($gid,'treasure_maxgold',getsetting('dgtrsmaxgold',100000));
				$maxgems = dg_calc_boni($gid,'treasure_maxgems',getsetting('dgtrsmaxgems',1000));
				
				dg_show_header('Gildenschatz - Transfer');
				
				$allowed = false;
				
				$_POST['count'] = (isset($_POST['count']) && $_POST['count'] == '' ? 99999999999999 : $_POST['count']);	// Maximum einzahlen
																									
				if($_GET['act'] == 'gold') {
				
					if($_POST['count']) {
				
						if($_GET['in']) {
							$count = dg_transfer($gid,(int)$_POST['count'],'gold');
							output('Du zahlst '.$count.' Gold ein');
							if($count > 0) {
								$msg = ': `@zahlt `^'.$count.' Gold`@ in die Gildenkasse';
							}
						}
											
					}
									
					if($_GET['in']) {
						$max_transfer = dg_calc_max_transfer_in($gid,'gold');
												
						if($max_transfer == 0 || $session['user']['gold'] == 0) {output('Heute kannst du nichts mehr einzahlen!');}
						else {
							output('Du kannst heute noch bis zu '.$max_transfer.' Gold einzahlen.');
							$allowed = true;
						}
						
						if($maxgold <= $guild['gold']) {
							output('`n`n`4Die Goldtruhen sind leider schon voll!');
							$allowed = false;
						}
						else {
							output('`n`n`0In den Truhen ist noch Raum f�r '.($maxgold - $guild['gold']).' Gold!');
						}
						
					}		
						

				}	// END if gold
				
				if($_GET['act'] == 'gems') {
				
					if($_POST['count']) {
				
						if($_GET['in']) {
							$count = dg_transfer($gid,(int)$_POST['count'],'gems');
							output('Du zahlst '.$count.' Edelsteine ein');
							if($count > 0) {	
								$msg = ': `@zahlt `^'.$count.' Edelsteine`@ in die Gildenkasse';
							}
						}
												
					}
				
				
					if($_GET['in']) {
						$max_transfer = dg_calc_max_transfer_in($gid,'gems');
																		
						if($max_transfer == 0 || $session['user']['gems'] == 0) {output('Heute kannst du nichts mehr einzahlen!');}
						else {
							output('Du kannst heute noch bis zu '.$max_transfer.' Edelsteine einzahlen.');
							$allowed = true;
						}
						
						if($maxgems <= $guild['gems']) {
							output('`n`n`4Die Edelsteintruhen sind leider schon voll!');
							$allowed = false;
						}
						else {
							output('`n`n`0In den Truhen ist noch Raum f�r '.($maxgems - $guild['gems']).' Edelsteine!');
						}
						
					}		

				}	// END if gems
				
				if($msg) {
					dg_commentary($gid,$msg,'treasure');
					dg_save_guild();
					redirect('dg_main.php?op=in&subop=treasure');		
				}
							
								
				if($_GET['in'] == 0 && $guild['treasure_locked']) {
					output('`n`n`4Die Schatzkammer wurde durch die Gildenf�hrung f�r alle Auszahlungen gesperrt!');
					$allowed = false;
				}
				
				if($guild['regalia'] == -1) {
					output('`n`n`4Die Gilde besitzt keine weiteren Insignien. Dadurch fehlt der Zugang zur Schatzkammer!');
					$allowed = false;
				}
								
				if($allowed) {
				
					$formlink = 'dg_main.php?op=in&subop=transfer&act='.$_GET['act'].'&in='.$_GET['in'];
												
					output('`n`i(Feld leer lassen, um das Maximum einzuzahlen)`i`n`n<form action="'.$formlink.'" method="POST"><input type="text" name="count" size="6" maxlength="6"><input type="submit" value="'.($_GET['in'] ? 'Einzahlen':'Abheben').'"></form>',true);
					addnav('',$formlink);	
				}
				
				addnav('Zur�ck zur Schatzkammer','dg_main.php?op=in&subop=treasure');
				
				break;
			
			case 'donate':
				
				$acctid = (int)$_POST['acctid'];
				$what = $_GET['what'] == 'Edelsteine' ? 'Edelsteine' : 'Gold';
				$count = (int)$_POST['count'];
				
				dg_show_header($what.' ausgeben');
				
				$link = 'dg_main.php?op=in&subop=donate&what='.$what;
				addnav('',$link);
				
				addnav('Zur�ck','dg_main.php?op=in&subop=treasure');
				
				output('<form method="POST" action="'.$link.'">');
				
				if($acctid) {
					
					$sql = 'SELECT name, a.acctid, level FROM accounts a LEFT JOIN account_extra_info ai USING(acctid) WHERE a.acctid='.$acctid;
					$res = db_query($sql);
					$m = db_fetch_assoc($res);
					
					if($_GET['what'] == 'Gold') {
						$left = dg_calc_max_transfer_out($gid,'gold',$m['level']);
						$what_obj = 'gold';
					}
					else {
						$left = dg_calc_max_transfer_out($gid,'gems',$m['level']);
						$what_obj = 'gems';
					}
					
					$count = min($left,$count);
					
					if($count > 0) {
						
						$count = dg_transfer($gid,-$count,$what_obj,$acctid,$m['level']);
						
						systemmail($acctid,'`2�berweisung',$session['user']['name'].'`2 hat dir im Auftrag deiner Gilde `^'.$count.'`2 '.$what.' �berwiesen!');
						
						if($count > 0) {
							dg_commentary($gid,':`4 hat an '.$m['name'].'`4 `^'.$count.'`4 '.$what.' �berwiesen!','treasure',$session['user']['acctid']);
						}
						
						redirect('dg_main.php?op=in&subop=treasure');
						
					}
					else {
																							
						if($left > 0) {
										
							output('`8Du kannst '.$m['name'].'`8 heute noch maximal `^'.$left.'`8 '.$what.' auszahlen:`n`n
										<input type="text" name="count" size="3" maxlength="4" value="0"> '.$what.' <input type="submit" value="auszahlen!">
										<input type="hidden" name="acctid" value="'.$acctid.'">',true);
										
						}
						else {
							output($m['name'].'`8 hat heute bereits genug '.$what.' von der Gilde erhalten!');
						}
					}	// END count <= 0					
				}
				else {
					
					$sql = 'SELECT login, acctid FROM accounts WHERE guildid='.$gid.' AND guildfunc!='.DG_FUNC_APPLICANT.' ORDER BY login ASC';
					$res = db_query($sql);
									
					output('`8Welchem Gildenmitglied willst du eine Auszahlung zukommen lassen?`n`n
								<select name="acctid" size="1">',true);
								
					while($m = db_fetch_assoc($res)) {
						
						output('<option value="'.$m['acctid'].'">'.$m['login'].'</option>',true);
						
					}
								
					output('</select> `n`n
							<input type="submit" value="Weiter!">',true);
					
				}	
				
				output('</form>',true);
				
				break;
			
			case 'ext_room':
				
				addcommentary();
				
				dg_show_header($guild['ext_room_name']);
									
				if($_GET['act'] == 'change') {
					
					if(strlen($_POST['ext_room_name']) > 3) {
						
						$guild['ext_room_desc'] = substr($guild['ext_room_desc'],0,600);
						
						$guild['ext_room_name'] = closetags($_POST['ext_room_name'],'`i`c`b');
						$guild['ext_room_desc'] = closetags($_POST['ext_room_desc'],'`i`c`b');
						
						dg_save_guild();
						redirect('dg_main.php?op=in&subop=ext_room');
						
					}
					else {
						
						$arr_form = array('ext_room_name'=>'Name des Gemachs (min. 4 Zeichen)',
											'ext_room_desc'=>'Beschreibung / Aussehen des Gemachs (max. 600 Zeichen)');
						
						$str_link = 'dg_main.php?op=in&subop=ext_room&act=change';
						
						addnav('',$str_link);
						
						output('<form method="POST" action="'.$str_link.'">',true);
						
						showform($arr_form,$guild);						
						
						addnav('Zur�ck');	
						addnav('Zum Gemach','dg_main.php?op=in&subop=ext_room');
						
					}	
					
				}
				else {
				
					addnav('Aktionen');
					if($team) {
						addnav('Aussehen �ndern','dg_main.php?op=in&subop=ext_room&act=change');
					}
					
					addnav('Zur�ck');
					addnav('Zur Halle','dg_main.php?op=in');
									
					output('`0'.closetags($guild['ext_room_desc'],'`i`c`b').'`n`n');				
					
					// M�bel
					dg_show_furniture('ext');
					// END M�bel
					
					viewcommentary('guild-'.$gid.'_xtrm','Etwas verk�nden:',25,'verk�ndet');
				}		
			
				break;
			
			case 'builds':
				
				dg_show_header('Die Ausbauten');
				
				$int_buildlvl_left = getsetting('dgmaxbuilds',30) - dg_get_ges_build($gid);
					
				if($_GET['act'] == 'start') {
					
					if($int_buildlvl_left <= 0) {
						output('`8Du musst erkennen, dass die Residenz leider keinen weiteren Platz f�r Ausbauten bietet.`nFalls ihr nicht auf
								dieses Geb�ude verzichten k�nnt, lasst einen vorhandenen Ausbau abrei�en!');
					}
					else {					
						output('`8Du bist im Begriff, den Ausbau '.$dg_builds[$_GET['type']]['name'].' in Auftrag zu geben. Dies ist eine ungef�hre Beschreibung, was dich erwartet:`n`n'.$dg_builds[$_GET['type']]['desc']);
						addnav('Ausbau beginnen!','dg_main.php?op=in&subop=builds&act=start_ok&type='.$_GET['type']);
					}
					addnav('Zur�ck','dg_main.php?op=in&subop=builds');
					
				}
				
				elseif($_GET['act'] == 'start_ok') {
										
					dg_build($gid,(int)$_GET['type']);
					dg_commentary($gid,': hat soeben den Ausbau `@'.$dg_builds[$_GET['type']]['name'].'`8 in Auftrag gegeben!','');
					dg_save_guild();
					redirect('dg_main.php?op=in&subop=builds');
					
				}
				
				elseif($_GET['act'] == 'del') {
										
					output('`$Du bist im Begriff, den Ausbau '.$dg_builds[$_GET['type']]['name'].' abrei�en zu lassen. Die Gilde wird keine Entsch�digung oder R�ckerstattung der Baukosten erhalten!');
					addnav('Ausbau abrei�en!','dg_main.php?op=in&subop=builds&act=del_ok&type='.$_GET['type']);
					addnav('Zur�ck','dg_main.php?op=in&subop=builds');
					
				}
				
				elseif($_GET['act'] == 'del_ok') {
										
					unset($guild['build_list'][$_GET['type']]);
					
					dg_commentary($gid,':`$ hat soeben den Ausbau `@'.$dg_builds[$_GET['type']]['name'].'`$ abrei�en lassen!','');
					dg_save_guild();
					redirect('dg_main.php?op=in&subop=builds');
					
				}
				else {
																
					output('`8Eine Auflistung aller Ausbauten der Gilde ist dringend n�tig, wer soll sonst den �berblick wahren k�nnen? Auch haben die F�hrungsmitglieder der Gilde hier die M�glichkeit, Ausbauten in Auftrag zu geben.`n`n
							Die Residenz der Gilde bietet `b'.($int_buildlvl_left>0?' noch Platz f�r etwa '.$int_buildlvl_left:'keinen weiteren Platz f�r').'`b Ausbaustufen.`n`n');
					
					$recent_build = $guild['build_list'][0];
					
					if($recent_build[0]) {
						output('Aktueller Ausbau: `b'.$dg_builds[$recent_build[0]]['name'].'`b (Noch `b'.$recent_build[1].'`b Tage bis zur Fertigstellung)`n`n');
					}
					
					dg_show_builds($gid,(($leader || $treasure)?true:false) );
					
					output('`n`n');
					
					foreach($dg_builds as $id=>$b) {
						if(dg_build_is_allowed($gid,$id)) {output('`8 - '.$b['desc'].'`n');}
					}
					
					addnav('Zur�ck zur Schatzkammer','dg_main.php?op=in&subop=treasure');
				}
												
				break;
			
			case 'private':
			
				if($_GET['act'] == 'invite') {
					$acctid = (int)$_POST['acctid'];
										
					item_add($acctid,'gldprive',true,array('tpl_value1'=>$gid));
					
					systemmail($acctid,'`8Einladung der Gildenf�hrung',$session['user']['name'].'`8 hat dir eine Einladung in das Hinterzimmer der Gilde '.$guild['name'].'`8 �berreicht. Vielleicht solltest du mal vorbeischauen.');
					redirect('dg_main.php?op=in&subop=private');
				}
				else if($_GET['act'] == 'cancel') {
					$itemid = (int)$_GET['itemid'];
					item_delete('id='.$itemid);
					redirect('dg_main.php?op=in&subop=private');
				}
			
				addcommentary();
				
				output('`8Nat�rlich existiert ein abgeschiedener, vom �brigen Gildengeb�ude abgetrennter Bereich, der nur f�r die Gildenf�hrer und ihre Minister zug�nglich ist. Hier ist Gelegenheit, �ber f�hrungsinterne Dinge zu beraten:`n`n');
				
				viewcommentary('guild-'.$gid.'_private','Mit den F�hrungsmitgliedern der Gilde sprechen:',25,'sagt');
				
				output('`n`n');
				
				output('`8Folgende Gildenmitglieder haben au�er dem F�hrungsteam noch Zutritt:`n`n');
					
				$sql = 'SELECT i.id,a.name,a.acctid FROM items i LEFT JOIN accounts a ON a.acctid=i.owner WHERE i.tpl_id="gldprive" AND i.value1='.$gid.' ORDER BY a.name ASC,a.acctid ASC';
				$res = db_query($sql);
				$counter = 0;
				$ids = '';
				
				while($i = db_fetch_assoc($res)) {
					$ids .= ','.$i['acctid'];
					$counter++;
					output('`n`8'.$counter.' : '.$i['name'].'`8');
					if($leader) {
						$link = 'dg_main.php?op=in&subop=private&act=cancel&itemid='.$i['id'];
						output(' [ <a href="'.$link.'">Ausladen</a> ]',true);
						addnav('',$link);
					}	
				}
				
				if($team) {
									
					if($leader) {
						
						addnav('Aktionen');addnav('`4Gilde aufl�sen','dg_main.php?op=in&subop=del');
						
						$sql = 'SELECT a.name,a.acctid FROM accounts a 
											WHERE a.guildid='.$gid.' AND a.acctid NOT IN (0'.$ids.') 
											AND a.guildfunc='.DG_FUNC_MEMBER.' ORDER BY a.name ASC,a.acctid ASC';
						$res = db_query($sql);
						
						if(db_num_rows($res) > 0) {
						
							$link = 'dg_main.php?op=in&subop=private&act=invite';
							addnav('',$link);
							
							output('<form method="POST" action="'.$link.'"><select name="acctid" size="1">',true);
							
							while($a = db_fetch_assoc($res)) {
								output('<option value="'.$a['acctid'].'.">'.$a['name'].'</option>',true);
							}
							
							output('</select> <input type="submit" value="Einladen!"></form>`n`n',true);
						}
						
					}
					
				}
				
				addnav('Zur�ck');
				addnav('Zur Halle','dg_main.php?op=in');
				
				break;
			
			case 'del':
				
				if($_GET['act'] == 'ok') {
					dg_massmail($gid,'`4Gilde aufgel�st!',$session['user']['name'].'`4 hat soeben die Gilde '.$guild['name'].'`4, in der du Mitglied warst, aufgel�st!');
					dg_addnews($session['user']['name'].'`4 hat soeben die Gilde '.$guild['name'].'`4 aufgel�st!',$session['user']['acctid']);
					dg_delete_guild($gid);
					redirect('dg_main.php');
				}
				
				output('`c`$Bist du dir wirklich sicher, deine Gilde aufl�sen zu wollen? Der gesamte Schatz und s�mtliche Ausbauten werden verlorengehen!`c`0');
				
				addnav('Nein, zur�ck.','dg_main.php?op=in&subop=private');
				addnav('Ja, aufl�sen!','dg_main.php?op=in&subop=del&act=ok');
				
				break;
				
			case 'members':
				
				$members_left = dg_guild_is_full($gid);
				
				if($_GET['act'] == 'refuse_applicant') {
					$acctid = (int)$_GET['acctid'];
					dg_remove_member($gid,$acctid,true);
					systemmail($acctid,'`4Bewerbung zur�ckgewiesen!','`4Deine Bewerbung auf Mitgliedschaft in der Gilde '.$guild['name'].'`4 wurde von '.$session['user']['name'].'`4 zur�ckgewiesen!');
					
					dg_save_guild();
					
					redirect('dg_main.php?op=in&subop=members');
					
				}
				elseif($_GET['act'] == 'accept_applicant') {
					
					if($members_left <= 0) { output('`$Die Gilde verf�gt bereits �ber die maximale Mitgliedsanzahl!`8');  }
					else {
						$acctid = (int)$_GET['acctid'];
						dg_add_member($gid,$acctid);
						
						$res = db_query('SELECT name FROM accounts WHERE acctid='.$acctid);
						$appl = db_fetch_assoc($res);
						
						systemmail($acctid,'`@Bewerbung angenommen!','`@Deine Bewerbung auf Mitgliedschaft in der Gilde '.$guild['name'].'`@ wurde von '.$session['user']['name'].'`@ angenommen!');
						
						dg_addnews($appl['name'].'`8 ist seit heute Mitglied der Gilde '.$guild['name'].'`8.',$acctid);
						
						addhistory('`2Beitritt zu Gilde '.$guild['name'],1,$acctid);
					
						dg_commentary($gid,': ist seit heute Mitglied dieser Gilde!','',$acctid);
						
						dg_save_guild();
						
						redirect('dg_main.php?op=in&subop=members');
					}
					
				}
				elseif($_GET['act'] == 'fire') {
					$acctid = (int)$_GET['acctid'];
					
					$res = db_query('SELECT name,sex FROM accounts WHERE acctid='.$acctid);
					$appl = db_fetch_assoc($res);
					
					dg_remove_member($gid,$acctid);
					systemmail($acctid,'`4Entlassung aus Gilde!','`4Deine Mitgliedschaft in der Gilde '.$guild['name'].'`4 wurde von '.$session['user']['name'].'`4 gek�ndigt!');
					
					dg_addnews($appl['name'].'`8 hat die Gilde '.$guild['name'].'`8 unfreiwillig verlassen.',$acctid);
					
					addhistory('`$Entlassung aus Gilde '.$guild['name'],1,$acctid);
					
					dg_commentary($gid,'/msg `5Kr�ftige Gildenwachen packen '.$appl['name'].'`5 und setzen '.($appl['sex']?'sie':'ihn').' vor die T�r!','',1);
					
					dg_save_guild();
					
					redirect('dg_main.php?op=in&subop=members');
					
				}
												
				$admin_mode = ($members) ? 2 : 0;
				$admin_mode = ($leader) ? 3 : $admin_mode;
				
				dg_show_header('Mitglieder & R�nge');
				
				output('`8Der Saal der Mitglieder ist ein kreisrundes Gemach mit einem riesigen Tisch. An der Wand erblickst du eine Tafel, auf der alle Angeh�rigen dieser Gilde verzeichnet sind:`n`n');
				
				if( strlen($guild['professions_allowed']) > 1) {
					$prof_list = explode(',',$guild['professions_allowed']);
					output('Die Gilde ist nur f�r Angeh�rige dieser Berufsgruppen zug�nglich:`n');
					foreach($prof_list as $p) {
						if($p) {
							output($profs[$p][0].'; ');
						}
					}
					output('`n`n');
				}	
				
				output( ($members_left>0?'Noch Platz f�r `b'.$members_left.'`b Mitglieder!':'Kein Platz mehr f�r Neuaufnahmen!').'`n`n' );
				
				dg_show_member_list($gid,$admin_mode);
				
				addnav('Die R�nge','dg_main.php?op=in&subop=ranks');				
				if($team) {addnav('Massenmail (`b1`b Gildenpunkt)','dg_main.php?op=in&subop=massmail');}
				
				addnav('Zur�ck zur Halle','dg_main.php?op=in');				
				
				break;
										
			case 'ranks':
				
				dg_show_header('Die R�nge');
											
				if($_GET['act'] == 'save') {
					$_POST['man'] = substr( str_replace('`0','',$_POST['man']) ,0,25);
					$_POST['woman'] = substr( str_replace('`0','',$_POST['woman']) ,0,25);
					$guild['ranks'][$_GET['nr']][0] = $_POST['man'].'`0';
					$guild['ranks'][$_GET['nr']][1] = $_POST['woman'].'`0';
					dg_save_guild();
					redirect('dg_main.php?op=in&subop=ranks');					
				}
				
				output('`8Ebenso kann man bei genauerem Hinsehen eine Schriftrolle entdecken, auf der alle R�nge dokumentiert sind, die man in dieser Gilde erreichen kann:`n`n');
				
				$admin_mode = ($members || $leader) ? 1 : 0;
				
				dg_show_ranks($gid,$admin_mode);
				
				addnav('Zur Mitgliederliste','dg_main.php?op=in&subop=members');
				
				break;
				
			case 'member_edit':
				
				$acctid = (int)$_GET['acctid'];
				
				$sql = 'SELECT guildrank,guildfunc,name,sex FROM accounts WHERE acctid='.$acctid;
				$res = db_query($sql);
				$act = db_fetch_assoc($res);
				
				if($_GET['act'] == 'save') {
					
					$func = (int)$_POST['func'];
					$rank = (int)$_POST['rank'];
										
					if($rank != $act['guildrank']) {
						if($act['guildrank'] < $rank) {
							$msg = ': wurde vom Rang '.$guild['ranks'][$act['guildrank']][$act['sex']].'`& degradiert zu '.$guild['ranks'][$rank][$act['sex']];						
							systemmail($acctid,'`4Degradierung!','`4Du hast nun in der Gilde '.$guild['name'].'`4 den Rang '.$guild['ranks'][$rank][$act['sex']].'`4 inne!');
						}
						else {
							$msg = ': wurde vom Rang '.$guild['ranks'][$act['guildrank']][$act['sex']].'`& zu '.$guild['ranks'][$rank][$act['sex']].'`& erhoben!';						
							systemmail($acctid,'`@Bef�rderung!','`@Du hast nun in der Gilde '.$guild['name'].'`@ den Rang '.$guild['ranks'][$rank][$act['sex']].'`@ inne!');
						}
						dg_commentary($gid,$msg,'',$acctid);
						
						if($acctid == $session['user']['acctid']) {
							$session['user']['guildrank'] = $rank;
						}
						else {																		
							$sql = 'UPDATE accounts SET guildrank='.$rank.' WHERE acctid='.$acctid;
							db_query($sql);
						}
						
					}
															
					if($func > 0) {
						if($func != $act['guildfunc']) {	
							$allowed = true;
							// �berpr�fen, ob es au�er uns noch einen F�hrer gibt
							if($acctid == $session['user']['acctid'] && ($func != DG_FUNC_LEADER) ) {
								$sql = 'SELECT COUNT(*) AS anzahl FROM accounts WHERE guildfunc='.DG_FUNC_LEADER.' AND acctid!='.$acctid.' AND guildid='.$gid.' GROUP BY acctid';
								$count = db_fetch_assoc(db_query($sql));
								if($count['anzahl'] == 0) {
									$allowed = false;
								}
							}
							
							// �berpr�fen, ob dieses Amt nicht schon besetzt ist
							if($allowed && ($func == DG_FUNC_WAR || $func == DG_FUNC_MEMBERS || $func == DG_FUNC_TREASURE) ) {
								
								$sql = 'SELECT COUNT(*) AS anzahl FROM accounts WHERE guildfunc='.$func.' AND acctid!='.$acctid.' AND guildid='.$gid.' GROUP BY acctid';
								$count = db_fetch_assoc(db_query($sql));
								
								if($count['anzahl'] > 0) {
									$allowed = false;
								}
								
							}
							
							if($allowed) {
							
								if($func != DG_FUNC_MEMBER) {					
									$msg = ': hat nun die Funktion '.$dg_funcs[$func][$act['sex']].' inne!';						
									systemmail($acctid,'`@Neues Amt!','`@Du hast nun in der Gilde '.$guild['name'].'`@ die Aufgabe '.$dg_funcs[$_POST['func']][$act['sex']].' inne!');
								
									addhistory('`2Amt '.$dg_funcs[$func][$act['sex']].' in Gilde '.$guild['name'],1,$acctid);
									
									if($func == DG_FUNC_LEADER) {
										addhistory('`2Neuer Gildenf�hrer '.$act['name'],2,$guild['guildid']);
									}
									
								}
								else {
									$msg = ': hat nun keine Aufgabe mehr!';						
									systemmail($acctid,'`4Kein Amt mehr!','`4Du hast nun in der Gilde '.$guild['name'].'`4 kein Amt mehr inne!');
									
									addhistory('`$Kein Amt mehr in Gilde '.$guild['name'],1,$acctid);
									
									if($act['func'] == DG_FUNC_LEADER) {
										addhistory('`$Gildenf�hrer '.$act['name'].'`$ abgesetzt',2,$guild['guildid']);
									}
									
								}
								dg_commentary($gid,$msg,'',$acctid);
								
								if($acctid == $session['user']['acctid']) {
									$session['user']['guildfunc'] = $func;
								}
								else {																		
									$sql = 'UPDATE accounts SET guildfunc='.$func.' WHERE acctid='.$acctid;
									db_query($sql);
								}
							}	// END if allowed
							
																				
						}	// END func modded
					}	// END func gegeben
				
					redirect('dg_main.php?op=in&subop=members');
															
				}	// END save
				
				dg_show_header($act['name'].'`8 �ndern:`n`n');
				
				$out = '<form method="POST" action="dg_main.php?op=in&subop=member_edit&act=save&acctid='.$acctid.'">'.
							(($leader)?'Aufgabe: <select name="func" size="1">
										<option value="'.DG_FUNC_MEMBER.'"  '.((DG_FUNC_MEMBER == $act['guildfunc'])?'selected="selected"':'').'>Keine</option>
										<option value="'.DG_FUNC_WAR.'"  '.((DG_FUNC_WAR == $act['guildfunc'])?'selected="selected"':'').'>'.$dg_funcs[DG_FUNC_WAR][$act['sex']].'</option>
										<option value="'.DG_FUNC_MEMBERS.'" '.((DG_FUNC_MEMBERS == $act['guildfunc'])?'selected="selected"':'').'>'.$dg_funcs[DG_FUNC_MEMBERS][$act['sex']].'</option>
										<option value="'.DG_FUNC_TREASURE.'" '.((DG_FUNC_TREASURE == $act['guildfunc'])?'selected="selected"':'').'>'.$dg_funcs[DG_FUNC_TREASURE][$act['sex']].'</option>
										<option value="'.DG_FUNC_LEADER.'" '.((DG_FUNC_LEADER == $act['guildfunc'])?'selected="selected"':'').'>'.$dg_funcs[DG_FUNC_LEADER][$act['sex']].'</option>
										</select>`n`n':'').
										'Rang: <select name="rank" size="1">';
										
				foreach($guild['ranks'] as $k=>$v) {
					$out .= '<option value="'.$k.'" '.(($k == $act['guildrank'])?'selected="selected"':'').'>'.$v[$act['sex']].'</option>';
				}
				
				$out .= '</select>`n`n<input type="submit" value="�bernehmen"></form>';										
				
				output($out,true);
				
				addnav('','dg_main.php?op=in&subop=member_edit&act=save&acctid='.$acctid);
				addnav('Zur�ck zu den Mitgliedern','dg_main.php?op=in&subop=members');
				
				break;
			
				
			case 'massmail':
				
				dg_show_header('Rundschreiben an alle Gildenmitglieder verfassen');
				
				if($guild['points'] >= 1) {
					if($_GET['act'] == 'send') {
						
						if(strlen($_POST['msg']) > 0) {
						
							$guild['points']--;
							
							dg_save_guild();
							
							$msg = '`8`c`bRundschreiben der Gilde '.$guild['name'].'`b`c`8:`n`n'.$_POST['msg'];
							
							dg_massmail($gid,$_POST['subject'],$msg);
							
							redirect('dg_main.php?op=in&subop=members');
														
						}
						
					}
					
					output('<form method="POST" action="dg_main.php?op=in&subop=massmail&act=send">Betreff: <input type="text" name="subject" size="50" maxlength="50">`n`nNachricht:`n <textarea name="msg" cols="40" rows="9" class="input"></textarea>`n`n<input type="submit" value="Abschicken!"></form>',true);
						
					addnav('','dg_main.php?op=in&subop=massmail&act=send');
				}
				else {
					output('Die Gilde kann ihre Boten ja nicht mal bezahlen!');
				}				
								
				addnav('Zur Mitgliederliste','dg_main.php?op=in&subop=members');
							
				break;
				
			case 'board':
				
				dg_show_header('Schwarzes Brett');
				
				output('`8Deine Gilde besitzt selbstverst�ndlich auch ein schwarzes Brett. An diesem verk�ndet die Gildenf�hrung wichtige Neuigkeiten, die nicht im allgemeinen Trubel untergehen sollen.`n`n');
				
				require_once(LIB_PATH.'board.lib.php');
				
				if($leader) {
			
					board_view_form('Aufh�ngen','`8Hier kannst Du als Gildenf�hrer eine Nachricht hinterlassen:');
					if($_GET['board_action'] == "add") {
						board_add('guild-'.$gid);
						redirect("dg_main.php?op=in&subop=board");
					}		
				}
				
				$del = ($leader) ? 2 : 1;
				board_view('guild-'.$gid,$del);
				
				addnav('Zur Halle','dg_main.php?op=in');
				
				break;
				
			case 'bio':
				
				$biomax = getsetting('dgbiomax',2048);
				
				if($_GET['act'] == 'save') {
					
					$guild['bio'] = substr($_POST['bio'],0,$biomax);
					$guild['rules'] = substr($_POST['rules'],0,$biomax);
					
					dg_save_guild();
					
					redirect('dg_main.php?op=in&subop=bio');
					
				}
				
				dg_show_header('Profil der Gilde');
				
				if($leader) {
					$str_lnk = 'dg_main.php?op=in&subop=bio&act=save';
					addnav('',$str_lnk);
					
					$arr_data = array('bio'=>$guild['bio'],
									'rules'=>$guild['rules']);
					$arr_form = array('bio'=>'Aktuelle Biographie der Gilde,textarea,40,20',
									'rules'=>'Aktuelle Regeln der Gilde,textarea,40,20');
					
					output('<form action="'.$str_lnk.'" method="POST">',true);
									
					showform($arr_form,$arr_data,false,'Niederschreiben!');
					
					output('</form>',true);
			
				}
								
				output('`8Aktuelle Bio: `n'.$guild['bio']);
				
				output('`n`n`8Aktuelle Regeln dieser Gilde: `n'.$guild['rules'].'`n`n');
										
				addnav('Zur Halle','dg_main.php?op=in');
				
				break;
			
			case 'hitlist':
				
				dg_show_header('Kopfgeldliste der Gilde');
				
				if($_GET['act'] == 'del') {
					dg_hitlist_remove($gid,(int)$_GET['acctid'],false);
					dg_save_guild();
					redirect('dg_main.php?op=in&subop=hitlist');
				}
				elseif($_GET['act'] == 'add') {
					
					$min_dks = getsetting('dgmindkapply',3);
															
					if(strlen($_POST['search']) > 0) {	
					
						$ids = $gid.'';
						if(is_array($guild['treaties'])) {
							foreach($guild['treaties'] as $guildid=>$t) {
								if(dg_get_treaty($t) == 1) {
									$ids .= ','.$guildid;
								}
							}
						}
					
						$count = strlen($_POST['search']);
						$search="%";
						for ($x=0;$x<$count;$x++){
							$search .= substr($_POST['search'],$x,1)."%";
						}
						
						$sql = 'SELECT name,acctid FROM accounts WHERE name LIKE "'.$search.'" AND acctid!='.$session['user']['acctid'].' AND guildid NOT IN ('.$ids.') AND dragonkills >= '.$min_dks;
						$res = db_query($sql);
						
						if(db_num_rows($res) == 0) {
							output('`8Kein m�gliches Ziel mit diesem Namen gefunden!');
						}
						else {	
							output('`8Diese Helden treffen auf deine Suche zu:`n`n');
						
							$link = 'dg_main.php?op=in&subop=hitlist&act=add';
							
							output('<form action="'.$link.'" method="POST">',true);
					
							output(' <select name="ziel">',true);
													
							while ( $p = db_fetch_assoc($res) ) {
					
								output('<option value="'.$p['acctid'].'">'.preg_replace("'[`].'","",$p['name']).'</option>',true);
					
							}
					
							output('</select>`n`n',true);
					
							output('<input type="submit" class="button" value="Hinzuf�gen"></form>',true);
							addnav('',$link);
						}
						addnav('Neue Suche','dg_main.php?op=in&subop=hitlist&act=add');
					}	// END if search
					
					elseif($_POST['ziel']) {
						
						$ziel = ($_POST['ziel'] ? (int)$_POST['ziel'] : (int)$_GET['ziel']);
						
						$sql = 'SELECT name,level FROM accounts WHERE acctid='.$ziel;
						$res = db_query($sql);
						$acc = db_fetch_assoc($res);
						
						$max_bounty = $acc['level'] * 500;
						$min_bounty = $acc['level'] * 100;
						
						if($_POST['bounty']) {
							$bounty = (int)$_POST['bounty'];
														
							$bounty = min($bounty,$max_bounty);
							$bounty = max($bounty,$min_bounty);
															
							$pay = round($bounty * 1.1);
														
							if($guild['gold'] < $pay) {
								output('`8Deine Gilde verf�gt nicht �ber die geforderten `^'.$pay.'`8 Gold!');
								addnav('Zur�ck','dg_main.php?op=in&subop=hitlist&act=add&ziel='.$ziel);
							}			
							else {
								dg_hitlist_add($gid,$ziel,$bounty);
								dg_save_guild();
								redirect('dg_main.php?op=in&subop=hitlist');	
							}
						}
						else {
													
							output('`8Kopfgeld auf '.$acc['name'].'`8 aussetzen (Mindestens '.$min_bounty.' Gold, maximal '.$max_bounty.', 10% Geb�hr!) :`n`n');
						
							$link = 'dg_main.php?op=in&subop=hitlist&act=add';
							
							output('<form action="'.$link.'" method="POST">',true);
							
							output('Kopfgeld: <input type="text" maxlength="4" name="bounty"> Gold ',true);
																			
							output('<input type="hidden" name="ziel" value="'.$ziel.'"><input type="submit" class="button" value="Hinzuf�gen"></form>',true);
							addnav('',$link);
						}
						
					}	// END if ziel
					else {
						$link = 'dg_main.php?op=in&subop=hitlist&act=add';
						output('`8Du kannst nur auf diejenigen Helden ein Kopfgeld aussetzen, die weder in deiner noch in einer befreundeten Gilde Mitglied sind. Weiterhin m�ssen sie mindestens '.$min_dks.' Drachenkills besitzen!`nGenerell gilt: 10% Geb�hr, Kopfgeld maximal das 800fache des Levels und minimal das 100fache.`n`n');
					
						output('<form action="'.$link.'" method="POST">',true);
			
						output('Name: <input type="input" name="search">',true);
												
						output('`n`n',true);
				
						output('<input type="submit" class="button" value="Suchen"></form>',true);
						addnav('',$link);
					}
					addnav('Zur�ck','dg_main.php?op=in&subop=hitlist');
				}	// END add
				else {
					
					$link = 'dg_main.php?op=in&subop=hitlist&act=add';
					if($leader || $war) {
						addnav('Neuer Auftrag',$link);
					}
					
					dg_show_hitlist($gid,($leader || $war ? true : false));
					addnav('Zur�ck','dg_main.php?op=in&subop=war');
					
				}
					
				break;
						
			case 'war':
				
				// Upgrade-Preise
				$int_def_upgr_price = ($guild['def_upgrade'] + 1) * 20;
				$int_atk_upgr_price = ($guild['atk_upgrade'] + 1) * 20;
				// Waffen- u. R�stungsbezeichnung
				$str_weapon = $guild['atk_upgrade']>0 ? ' mit der Waffe '.$arr_dg_weaponnames[ $guild['atk_upgrade'] ] : '';
				$str_armor = $guild['def_upgrade']>0 ? ' mit der R�stung '.$arr_dg_armornames[ $guild['def_upgrade'] ] : '';
				
				if($team) {addcommentary();}
				
				dg_show_header('Krieg & Diplomatie');
				
				if($_GET['act'] == 'cancel') {
					
					$guild['war_target'] = 0;

					//dg_commentary($gid,': `4erkl�rt den Angriff f�r beendet.','');						
					dg_save_guild();
					redirect('dg_main.php?op=in&subop=war');
				}
				elseif($_GET['act'] == 'upgrade') {
					
					$str_what = ($_GET['what'] == 'atk' ? 'atk' : 'def');
					$int_price = ($str_what == 'atk' ? $int_atk_upgr_price : $int_def_upgr_price);
					
					if($_GET['ok']) {
						$guild[$str_what.'_upgrade']++;
						$guild['points'] -= $int_price;
											
						dg_commentary($gid,':`@ verst�rkt die Gildenwachen mit '.($str_what == 'atk' ? 'neuen Waffen' : 'neuen R�stungen').'!','war');						
											
						dg_save_guild();
						redirect('dg_main.php?op=in&subop=war');
					}
					else {	// Sicherheitsabfrage
						addnav('Zur�ck zur Kriegskammer','dg_main.php?op=in&subop=war');
						
						$str_lnk = 'dg_main.php?op=in&subop=war&act=upgrade&ok=1&what='.$str_what;
						
						if($guild['points'] >= $int_price) {
							output( create_lnk('Aufr�sten!',$str_lnk), true );
						}
						else {	// Zu wenig Punkte
							output('Zu wenig Punkte');
						}
						
					}
				}
				elseif($_GET['act'] == 'downgrade') {
					
					$str_what = ($_GET['what'] == 'atk' ? 'atk' : 'def');
										
					if($_GET['ok']) {
						$guild[$str_what.'_upgrade'] = 0;
										
						dg_commentary($gid,':`# nimmt den Gildenwachen s�mtliche verbesserten '.($str_what == 'atk' ? 'Waffen' : 'R�stungen').' ab!','war');						
											
						dg_save_guild();
						redirect('dg_main.php?op=in&subop=war');
					}
					else {	// Sicherheitsabfrage
						addnav('Zur�ck zur Kriegskammer','dg_main.php?op=in&subop=war');
						
						$str_lnk = 'dg_main.php?op=in&subop=war&act=downgrade&ok=1&what='.$str_what;
						
						output( create_lnk('Abr�sten!',$str_lnk), true );
												
					}
														
				}
				elseif($_GET['act'] == 'start') {
					
					$enemy = &dg_load_guild($_GET['target'],array('name','fights_suffered','regalia','guard_hp','atk_upgrade','def_upgrade'));
					
					if($_GET['ok']) {
						
						$guild['war_target'] = (int)$_GET['target'];

						$guild['points'] -= $dg_points['war_cost'];
						$guild['points_spent'] += $dg_points['war_cost'];
						
						// Ansehensverlust
						$int_repuloose = 0;
						$arr_infl = dg_calc_strength(array($gid,$_GET['target']));
						
						$int_diff = round($arr_infl[$gid] - $arr_infl[$_GET['target']]);
						
						if($int_diff < 5) {
							$int_repuloose = 4;
						}
						else if($int_diff >= 5 && $int_diff < 10) {
							$int_repuloose = 6;
						}
						else if($int_diff >= 10 && $int_diff < 20) {
							$int_repuloose = 10;
						}
						else if($int_diff >= 20 && $int_diff < 30) {
							$int_repuloose = 16;
						}
						else {
							$int_repuloose = 22;
						}
						
						$guild['reputation'] = max($guild['reputation']-$int_repuloose,0);
						// END Ansehensverlust
						
						$guild['immune_days'] = 0;
													
						//dg_commentary($gid,': `4gibt das Signal zum Angriff auf '.$enemy['name'].'`4. Auf sie!','');						
						dg_save_guild();
						redirect('dg_main.php?op=in&subop=war');
						
					}			
					
					if($guild['points'] < $dg_points['war_cost']) {
						output('Die Gilde besitzt nicht die ben�tigten '.$dg_points['war_cost'].' Punkte, um einen Krieg bezahlen zu k�nnen!`n`n');
					}
					else {
																		
						if($enemy['guard_hp'] <= 0) {
							output('`8`bDeine Gilde will sich doch wohl nicht die H�nde an solchen Schw�chlingen schmutzig machen? Diese Gilde anzugreifen w�re nun wirklich unter ihrer W�rde!`b`n`n');
						} 
						elseif($enemy['fights_suffered'] > getsetting('dgfightssuf',2)) {
							output('`8`bDiese Gilde wurde heute bereits '.$enemy['fights_suffered'].' mal angegriffen. Da ist bestimmt nichts mehr zu holen!`b`n`n');
						}
						elseif($enemy['fights_suffered_period'] > getsetting('dgfightssufperiod',2)) {
							output('`8`bDiese Gilde wurde in der Vergangenheit bereits zu oft angegriffen. Da ist bestimmt nichts mehr zu holen!`b`n`n');
						}
						else {
							if($guild['atk_upgrade']) {
								$str_equipment_own = ', ausger�stet mit '.$arr_dg_weaponnames[$guild['atk_upgrade']].', ';
							}
							else {
								$str_equipment_own = '';
							}
							if($enemy['def_upgrade']) {
								$str_equipment_enemy = 'Es scheint, als w�ren die feindlichen Krieger mit '.$arr_dg_armornames[$enemy['def_upgrade']].' ausger�stet!';
							}
							else {
								$str_equipment_enemy = '';
							}							
							
							output('`8Ein Bote kehrt von einem Erkundungsgang zur�ck und berichtet dir:`n 
									`^"Die Tore der feindlichen Gilde werden von '.$enemy['guard_hp'].' gut bewaffneten 
									Kriegern bewacht. '.$str_equipment_enemy.'`n
									Dem stehen '.$guild['guard_hp'].' Mann'.$str_equipment_own.' auf unserer Seite gegen�ber.`n
									Seid ihr euch wirklich sicher, den Angriff befehlen zu wollen?`n`n
									Zun�chst k�nnten die Mitglieder unserer Gilde gegen den Feind vorr�cken,
									ehe ihr den Ansturm unserer Truppen anordnet!`n
									Doch beachtet, dass wir unseren Angriff bis sp�testens Mitternacht dieses
									Tages abgeschlossen haben sollten, andernfalls verf�llt er.."`8');
									
							addnav('Ja, zeigen wir es ihnen!','dg_main.php?op=in&subop=war&act=start&ok=1&target='.$_GET['target']);
							
						}				
												
					}
					
					addnav('Zur�ck zum Kriegszimmer','dg_main.php?op=in&subop=war');
					
				}
				else {						
					output('`8Du betrittst einen Raum, der vordergr�ndig der Kriegsf�hrung gewidmet ist. Nicht umsonst ist die Wand mit martialischen Symbolen und Gegenst�nden geschm�ckt. Die Mitte des Raumes wird vom Strategietisch eingenommen, auf dem der n�chste Feldzug geplant wird:`n`n');
					
					if($guild['guildwar_allowed']==0) {
						output('Die Gilde besitzt nicht das Recht, Gildenkriege zu f�hren!`n`n');
					}
					
					if($guild['immune_days']>0) {
						output('Die Gilde ist noch f�r `b'.$guild['immune_days'].'`b Tage vor Gildenkriegen gesch�tzt, so lange sie nicht selbst einen Angriff beginnt!`n`n');
					}	
					
					// WAFFEN + R�STUNGS - Update
					if($team) {
						if($guild['atk_upgrade'] > 0) {
							addnav('Alle Waffenverbesserungen zur�cknehmen!','dg_main.php?op=in&subop=war&act=downgrade&what=atk');	
						}
						else {
							if($guild['def_upgrade'] < 3) {
							
								addnav('Gildenwachen-R�stung verbessern ('.$int_def_upgr_price.' GP)!','dg_main.php?op=in&subop=war&act=upgrade&what=def');		
								
							}
						}
						if($guild['def_upgrade'] > 0) {
							addnav('Alle R�stungsverbesserungen zur�cknehmen!','dg_main.php?op=in&subop=war&act=downgrade&what=def');	
						}
						else {
							if($guild['atk_upgrade'] < 3) {
							
								addnav('Gildenwachen-Waffen verbessern ('.$int_atk_upgr_price.' GP)!','dg_main.php?op=in&subop=war&act=upgrade&what=atk');		
								
							}
						}
					}
					// END Upgrade
															
					output('Krieger in der Kaserne: `b'.$guild['guard_hp'].$str_weapon.$str_armor.'!`b`n`n');
					
					viewcommentary('guild-'.$gid.'_war',($team ? 'Etwas verk�nden:':'Du solltest hier besser schweigen!'),25,'verk�ndet',false,($team?true:false));
														
					// Infos �ber aktuell laufenden Krieg abrufen								
					if($guild['war_target']) {
						
						$enemy_guild = &dg_load_guild($guild['war_target'],array('name','state','guard_hp','build_list','type'));
						
						if($enemy_guild['state'] == DG_STATE_ACTIVE && $guild['guildwar_allowed']) {
								
							output('`n`n`bIm Krieg mit: '.$enemy_guild['name'].'`b`n',true);
							
							addnav('Krieg');
							
							if($war || $leader) {addnav('Krieg beenden','dg_main.php?op=in&subop=war&act=cancel');}
																																		
							if($enemy_guild['guard_hp'] <= 0) {
								addnav($enemy_guild['name'].' pl�ndern!','dg_battle.php?battle_op=attack3');
								output('`8`n`bDer Weg ist frei!`b`n');
							}
							else {
								$rowe = user_get_aei('guildfights');
								if($rowe['guildfights'] == 0) {
									addnav($enemy_guild['name'].' angreifen!','dg_battle.php?battle_op=attack1');
								}
								if($war || $leader) {
									addnav('Den Truppen Angriff auf '.$enemy_guild['name'].' befehlen!','dg_battle.php?battle_op=attack2');
								}
								output('`8Zur Zeit stehen unseren Truppen `^'.$enemy_guild['guard_hp'].'`8 Mann gegen�ber.');
							}
						}
																		
					}
					
					addnav('Aktionen');					
					addnav('Vertr�ge','dg_main.php?op=in&subop=treaties');
					addnav('Kopfgeldliste','dg_main.php?op=in&subop=hitlist');
					addnav('Zur Halle','dg_main.php?op=in');
				}			
								
				break;
										
			case 'treaties':
				if($_GET['target']) {
					$enemy = &dg_load_guild($_GET['target'],array('name','treaties','war_target'));
				}
				if($_GET['act'] == 'peace') {	// Friedensangebot
					
					dg_set_treaty($gid,$_GET['target'],DG_TREATY_PEACE_SELF);
					output('gemacht');
					dg_massmail($_GET['target'],'`8Friedensangebot',$guild['name'].'`8 hat deiner Gilde ein Friedensangebot unterbreitet!',200);
					dg_save_guild();
					redirect('dg_main.php?op=in&subop=treaties');
					
				}
				elseif($_GET['act'] == 'accept_peace') {	// Friedensangebot akzeptieren
					
					dg_set_treaty($gid,$_GET['target'],DG_TREATY_PEACE_OTHER,false);
					output('angenommen');
					dg_commentary($gid,': `@verk�ndet, dass diese Gilde einen Friedensvertrag mit '.$enemy['name'].'`@ geschlossen hat!','');
					dg_massmail($_GET['target'],'`8Friedensvertrag akzeptiert!',$guild['name'].'`8 hat das Friedensangebot deiner Gilde angenommen!',200);
					dg_save_guild();
					$newsmsg = '`2Die Gilde '.$guild['name'].'`2 schlie�t mit '.$enemy['name'].'`2 einen Friedensvertrag ab.';
					dg_addnews($newsmsg);
					addhistory($newsmsg,2,$guild['guildid']);
					addhistory($newsmsg,2,$enemy['guildid']);
					redirect('dg_main.php?op=in&subop=treaties');
					
				}
				elseif($_GET['act'] == 'neutral') {	// Neutral
					
					dg_set_treaty($gid,$_GET['target'],0,false);
					output('angenommen');
					dg_massmail($_GET['target'],'`8Neutral!',$guild['name'].'`8 verh�lt sich von nun an neutral zu deiner Gilde!',200);
					dg_save_guild();
					redirect('dg_main.php?op=in&subop=treaties');
					
				}
				elseif($_GET['act'] == 'refuse_peace') {	// Friedensangebot zur�ckweisen
					
					dg_set_treaty($gid,$_GET['target'],0,false);
					output('abgelehnt');
					dg_massmail($_GET['target'],'`8Friedensvertrag zur�ckgewiesen!',$guild['name'].'`8 hat das Friedensangebot deiner Gilde zur�ckgewiesen!',200);
					dg_save_guild();
					redirect('dg_main.php?op=in&subop=treaties');
					
				}
				elseif($_GET['act'] == 'war') {	// Kriegserkl�rung
					
					dg_set_treaty($gid,$_GET['target'],DG_TREATY_WAR_SELF,false);
					dg_commentary($gid,': `4verk�ndet, dass diese Gilde sich ab sofort im Krieg mit '.$enemy['name'].'`4 befindet!','');
					dg_massmail($_GET['target'],'`$Kriegserkl�rung!',$guild['name'].'`8 hat deiner Gilde den Krieg erkl�rt!',200);
					dg_save_guild();
					
					$newsmsg = '`$Die Gilde '.$guild['name'].'`$ erkl�rt '.$enemy['name'].'`$ den Krieg.';
					dg_addnews($newsmsg);
					addhistory($newsmsg,2,$guild['guildid']);
					addhistory($newsmsg,2,$enemy['guildid']);
					
					redirect('dg_main.php?op=in&subop=treaties');
										
				}				
				
				dg_show_header('Vertr�ge');
				$diplo = ($leader || $war) ? 3 : 1;
				dg_show_guild_list(0,false,'name ASC',true, $diplo);
				
				addnav('Zur�ck','dg_main.php?op=in&subop=war');
				
				break;
			
			case 'guild_talk':
				
				addcommentary();
				$target = (int)$_GET['target'];
				$section = 'guild-'.(($target > $gid) ? $target.'_'.$gid : $gid.'_'.$target).'-talk';
				
				$other_g = &dg_load_guild($target,array('name'));
				
				dg_show_header('Gildengespr�ch');
								
				output('`8In einem vornehmen Verhandlungsraum treffen sich Abgesandte der Gilden '.$other_g['name'].'`8 und '.$guild['name'].'`8, um �ber gemeinsame Aktionen und die Basis ihrer Freundschaft zu diskutieren:`n`n');
								
				viewcommentary($section,'Mit Abgesandten der anderen Gilde sprechen:',25,'spricht');
				
				addnav('Zu den Vertr�gen','dg_main.php?op=in&subop=treaties');
				addnav('Zur Halle','dg_main.php?op=in');
				
				break;
						
			case 'buildings':
				
				// AUSBAUTEN
				include_once('dg_builds.php');
				// END AUSBAUTEN
				
				break;
						
		}	// END subop in
						
		break;	// END in

}	// END main switch

// jegliche Ver�nderung speichern
dg_save_guild();

if(su_check(SU_RIGHT_EDITORGUILDS)) { 
	addnav('Admin');
	addnav('Zum Gildeneditor','dg_su.php');
	
	if(su_check(SU_RIGHT_DEV)) { 
		
		//addnav('K�nig rufen!','dg_su.php?op=callking');
		
	}
}

page_footer();
?>
