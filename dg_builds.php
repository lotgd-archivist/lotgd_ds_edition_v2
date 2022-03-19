<?php
/*-------------------------------/
Name: dg_builds.php
Autor: tcb / talion für Drachenserver (mail: t@ssilo.de)
Erstellungsdatum: 6/05 - 9/05
Beschreibung:	Enthält Ausbauten, die einen eigenen Raum besitzen. Wird an passender Stelle in dg_main.php inkludiert
				Diese Datei ist Bestandteil des Drachenserver-Gildenmods (DG). 
				Copyright-Box muss intakt bleiben, bei Verwendung Mail an Autor mit Serveradresse.
				Besonderer Dank für Texte, Ideen und Tests gebührt folgenden Spielern und Spielerinnen des Drachenservers: 
				Sith, Sersee, Ibga, Salvan und LOKI!
/*-------------------------------*/

	
	switch($_GET['building_op']) {
			
			case 'na':	// Auflistung der Vorteile bei Ausbauten ohne eigenen Raum
				
//				output('`tIm Verborgenen wirken durch Ausbauten viele Effekte, die sonst nicht wahrnehmbar wären. Hier sind jene aufgelistet:`n`n');	
				output('`tDieser Bereich deines Gildenhauses ist zur Zeit versperrt. Hinter der Barrikade kannst du eifriges Hämmern und Sägen hören.`n`n');	
				
				/*if($guild['build_list'][DG_BUILD_HALLE]) { output('Deine `bVersammlungshalle`b erhöht die maximale Mitgliederzahl um '.dg_calc_boni($gid,'members',5).' Helden.`n'); }
				if($guild['build_list'][DG_BUILD_VERSTECK]) { output('Dein `bVersteck`b erhöht die maximale Mitgliederzahl um '.dg_calc_boni($gid,'members', ($guild['ptype'] == DG_GUILD_TYPE_WIZARD || $guild['ptype'] == DG_GUILD_TYPE_THIEVES ? 1 : 0) ).' Helden.`n'); }
				if($guild['build_list'][DG_BUILD_WACHTURM]) { output('Dein `bWachturm`b verstärkt die Basislebenskraft deiner Gildenwache um '.dg_calc_boni($gid,'guard_hp_max',0).' Punkte.`n'); }
				if($guild['build_list'][DG_BUILD_SCHATZKAMMER]) { $transfer_plus = (dg_calc_boni($gid,'maxgoldin',1) - 1) * 100;output('Deine `bSchatzkammer`b ermöglicht '.($transfer_plus > 0 ? ' um '.$transfer_plus.' % höhere Ein- und Auszahlungen und ' : '').' um '.( (dg_calc_boni($gid,'treasure_maxgold',1) - 1) * 100).' % vergrößerte Schatztruhen.`n'); }
				if($guild['build_list'][DG_BUILD_WALL]) { output('Dein `bMagischer Schutzwall`b schwächt den Angriff der Gegner um '.(dg_calc_boni($gid,'magic_guard',0)*100).' Prozent.`n'); }*/
				
				addnav('Zurück','dg_main.php?op=in');
			
			break;
			
			// AUSBAUTEN
			case 'library':
				
				// Einzelnes Buch anzeigen
				if($_GET['act'] == 'showbook' && $_GET['id']) {
					
					$sql = 'SELECT * FROM lib_books WHERE id='.(int)$_GET['id'];
					$res = db_query($sql);
					$arr_book = db_fetch_assoc($res);
					
				}
				else if($_GET['act'] == 'writebook') {
					
					$arr_form = array('title'=>'Titel des Buches',
									'book'=>'Inhalt des Buches,textarea');
					
					if($_REQUEST['id']) {
						$sql = 'SELECT * FROM lib_books WHERE id='.(int)$_REQUEST['id'];
						$res = db_query($sql);
						$arr_book = db_fetch_assoc($res);
					}
					
					// Buch einreichen
					if($_GET['save']) {
					
						$sql = ($arr_book['bookid'] ? 'UPDATE ' : 'INSERT INTO ');
						$sql .= ' lib_books SET ';
						
						$sql .= ' acctid='.$gid.',loc='.DG_GLD_BOOK.',title="'.$_POST['title'].'",
								book="'.$_POST['book'].'"';
						
						$sql .= ($arr_book['bookid'] ? ' WHERE bookid='.$arr_book['bookid'] : '');
						
						db_query($sql);
						
						redirect();
					}
					
					
					
				}
				// Buch löschen
				else if($_GET['act'] == 'delbook') {
					
					$sql = 'DELETE FROM lib_books WHERE bookid='.(int)$_GET['id'].' AND loc='.DG_GLD_BOOK;
					db_query($sql);
					
				}
				// Bücherliste anzeigen
				else {
					
					$sql = 'SELECT * FROM lib_books WHERE loc='.DG_GLD_BOOK.' AND acctid='.$gid.' AND activated="1"';
					db_query($sql);
					
				}
				
			break;		
			
			case 'explain':
				
				dg_show_header('Gildenalmanach');
				
				$str_manual = get_extended_text('leader_manual');
				$str_history = get_extended_text('regalia_history_ext');
				
				output('`8'.$str_history.'`n`n'.$str_manual);
				
				addnav('Zur Halle','dg_main.php?op=in');
				
			break;
						
			// Lagerhalle
			case 'deposit':
				
				$str_type = $_REQUEST['type'];
				$str_act = $_REQUEST['act'];
				
				// Max. Anzahl an Slots
				$int_max = 1000;
				// Bereits vorhanden
				$int_count = 0; 
				// Übriger Platz
				$int_left = 0;
				// Bewertungsfaktor für Edelsteine bei Itemtransferbegrenzung
				$int_gemsfactor = getsetting('maxitemsgemsfactor',5000);
				// BasisSQL
				$str_show_sql = ' owner='.ITEM_OWNER_GUILD.' AND deposit1='.$gid.' AND ';
				// Einlager-SQL
				$str_deposit_sql = ' owner='.$session['user']['acctid'].' AND deposit1=0 AND deposit2=0 AND ';
				// Anzahl der ben. Splitter für eine Insignie
				$int_parts_needed = 2;
						
				switch($str_type) {
					case 'regalia':
						$str_show_sql .= ' i.tpl_id="insgnteil" ';
						$str_deposit_sql .= ' i.tpl_id="insgnteil" ';										
						$str_header = 'Insigniensplitter:';
																		
						$int_max = 20;
						$int_count = item_count($str_show_sql); 
						$int_left = max($int_max - $int_count,0);
						
						$str_intro_txt = 
							'`8In einem weiteren geräumigen Kellergewölbe, das von Fackeln hell erleuchtet wird, befindet sich
							die Insignienschmiede deiner Gilde. Vor den Säulen am Eingang halten Tag und Nacht mehrere Söldner
							Wache, ohne die Insignien aus den Augen zu lassen. Auf Holzbalken, unter Decken verborgen lagern
							sie, das Kapital deiner Gilde, während Werktische und verschiedene komplizierte Apparaturen bereitstehen,
							um sie aus ihren Einzelteilen zusammenzusetzen.`n
							Zur Zeit stehen '.$guild['regalia'].' Insignien zum Verkauf bereit, es können noch `b'.$int_left.'`b Insigniensplitter hier deponiert werden.`n`n
							';
						
					break;
					
					case 'furniture':
						$str_show_sql .= ' deposit_guild>0 ';
						$str_deposit_sql .= ' deposit_guild>0 ';
						$str_header = 'Möbel:';
						
						$int_max = 30;
						$int_count = item_count($str_show_sql,true); 
						$int_left = max($int_max - $int_count,0);
						
						$str_intro_txt = '`8Den Möbelstücken deiner Gilde ist eine eigene Abteilung gewidmet. Kein Wunder, belegen
											sie doch selbst im gestapelten Zustand relativ viel Platz im Gewölbe.`n
											Ein Mitglied der Gildenführung kann anordnen, dass ein bestimmtes Möbelstück in die oberen
											Räume des Gildenhauses geschafft wird.`n`n
											`$Achtung: Einmal eingelagerte Möbelstücke gehen in den Besitz der Gilde über und können nicht mehr 
											zurückgegeben werden!`8`n`n';										
						
					break;
					
					case 'loot':
					default:
						$str_show_sql .= ' guildinvent>0 AND deposit_guild=0 AND i.tpl_id!="insgnteil" ';
						$str_deposit_sql .= ' guildinvent>0 AND deposit_guild=0 AND i.tpl_id!="insgnteil" ';
						$str_header = 'Beute:';
												
						$int_max = 30;
						$int_count = item_count($str_show_sql,true); 
						$int_left = max($int_max - $int_count,0);
						
						$str_intro_txt = '`8Auf alle Winkel und Ecken sind die Kleinteile, die Beutestücke und alchemistischen
											Erfolgserlebnisse der Gildenmitglieder verteilt. Du schätzt, dass der Platz noch für
											ungefähr `b'.$int_left.'`b Stücke ausreichen könnte.`n`n';
						
					break;
				}						
				
				dg_show_header('Lagerräume der Gilde - '.$str_header);
				
								
				switch($str_act) {
					
					// Einlagern
					case 'in':
						// Wenn Item gegeben
						if($_GET['id']) {
							
							$arr_item = item_get('id='.(int)$_GET['id']);
							
							$int_val = $arr_item['gold'] + $arr_item['gems'] * $int_gemsfactor;
							
							// Kontrolle, ob Grenze für heute noch nicht überschritten
	//						if($rowe['itemsout'] + $int_val > getsetting('maxitemsout',8000) && $rowe['itemsout'] > 0) {
							if(0) {
								output('`qLeider hast du heute bereits zu viele oder zu wertvolle Gegenstände
										verschenkt. Dein Geiz hindert dich daran, noch mehr wegzugeben.');
							}
							else {
								
								item_set(' id='.$arr_item['id'], 
									array(
										'owner'=>ITEM_OWNER_GUILD,
										'deposit1'=>$gid,
										'deposit2'=>$arr_item['owner']
										)
									);
									
								output('`qDu suchst für '.$arr_item['name'].'`q einen Platz in den weitläufigen
										Lagerräumen deiner Gilde.');
								
								dg_commentary($gid,':`@deponiert '.$arr_item['name'].'`@ im Gewölbe.','invent');
								
								$int_val = $arr_item['gold'] + $arr_item['gems'] * 500;
										
								user_set_aei(array('itemsout'=>$rowe['itemsout']+$int_val));
								
								if($int_left > 0) {
								
									addnav('Mehr einlagern','dg_main.php?op=in&subop=buildings&building_op=deposit&act=in&type='.$str_type);
								
								}
								
								//$session['user']['turns']--;
								
							}	// END Kontrolle
													
						}
						else if($int_left <= 0) {
							output('`8Dieser Bereich des Gildenlagers ist leider schon völlig überfüllt. Maximal können `b'.$int_max.'`b Gegenstände 
										an dieser Stelle gelagert werden, zur Zeit sind es jedoch `b'.$int_count.'`b! 
										Hier muss erst Platz geschaffen werden..');
						}
						else {
							
							// op bereits durch Baselink gegeben
							$arr_options = array('Einlagern'=>'');
				
							item_show_invent($str_deposit_sql, false, 0, 1, 1, 'Nach einer ausgiebigen Inspektion deines Beutels steht fest, dass du nichts dabei hast, das sich einzulagern lohnte.', $arr_options);
																			
						}
						
						addnav('Zurück');
						addnav('Zu den Lagerräumen','dg_main.php?op=in&subop=buildings&building_op=deposit&type='.$str_type);
					break;
					
					// Auslagern
					case 'out':
						
						$arr_item = item_get('id='.(int)$_GET['id']);
					
						$int_goldprice = round($arr_item['gold'] * 0.25);
						$int_gemprice = round($arr_item['gems'] * 0.25);
						
						$int_val = $arr_item['gold'] + $arr_item['gems'] * $int_gemsfactor;
						
						// Kontrolle, ob Grenze für heute noch nicht überschritten
						if($rowe['itemsin'] + $int_val > getsetting('maxitemsin',8000) && $rowe['itemsin'] > 0) {
							output('`qLeider hast du heute bereits zu viele oder zu wertvolle Gegenstände
									mit dir genommen. Dein schlechtes Gewissen gegenüber den Armen dieser
									Welt hindert dich daran, dich zu bedienen.');
						}
						else if($session['user']['gold'] < $int_goldprice || $session['user']['gems'] < $int_gemprice) {	// Vermögen ausreichend?
							output('`qDu musst bei genauerer Betrachtung feststellen, dass dein Vermögen noch nicht einmal
									ausreicht, dem Lagermeister die geforderten '.$int_goldprice.' Gold, '.$int_gemprice.' Edelsteine zu bezahlen!');					
						}
						else if($session['user']['turns'] <= 0) {	// Genug Runden?
							output('`qLeider bist du heute bereits zu geschwächt, um noch Krämerware durch die Gegend zu schleppen.`n
									Du beschließt, bis morgen zu warten.');
						}
						else {
							
							$session['user']['gold'] -= $int_goldprice;
							$session['user']['gems'] -= $int_gemprice;
						
							if($arr_item['deposit2'] != $session['user']['acctid'] && $arr_item['deposit2'] > 0) {
								
								$sql = 'SELECT name,acctid FROM accounts WHERE acctid='.$arr_item['deposit2'].' AND guildid='.$gid;
								$res = db_query($sql);
								
								if(db_num_rows($res)) {
								
									$str_msg = '`3Ein Bote bringt dir eine Nachricht:`n
												`#Ich habe mir erlaubt, den Gegenstand '.$arr_item['name'].'`#, welchen Ihr in der Lagerhalle
												unserer Gilde deponiert hattet, an mich zu nehmen.`n
												Gezeichnet`n
												'.$session['user']['name'].'`#';
									
									systemmail($arr_item['deposit2'],'`3Gegenstand entnommen!',$str_msg);
								}
								
							}
							
							dg_commentary($gid,':`$entnimmt '.$arr_item['name'].'`$ aus dem Gildenlager.','invent');
							
							item_set(' id='.$arr_item['id'], 
									array(
										'owner'=>$session['user']['acctid'],
										'deposit1'=>0,
										'deposit2'=>0
										)
									);
							
							
							
							user_set_aei(array('itemsout'=>$rowe['itemsin']+$int_val));
							
							$session['user']['turns']--;
									
							output('`qDu nimmst '.$arr_item['name'].'`q an dich.');
						}	// END wenn Grenze noch nicht überschritten
						
						addnav('Zurück');
						addnav('Zu den Lagerräumen','dg_main.php?op=in&subop=buildings&building_op=deposit&type='.$str_type);
						
					break;
					
					// Möbel in Gilde verstauen
					case 'furniture_set':
						// Kontrolle, wie viele dieser Items wir als Möbelstück verwenden dürfen
						$arr_item = item_get('id='.(int)$_GET['id']);						
						
						$bool_change = true; 
						
						$int_depo = ($_GET['what'] == 'ext' ? ITEM_LOC_GUILDEXT : ($_GET['what'] == 'hall' ? ITEM_LOC_GUILDHALL : 0));
						
						if($int_depo > 0) {
							if( item_count(' tpl_id="'.$arr_item['tpl_id'].'" AND owner='.ITEM_OWNER_GUILD.' AND deposit1='.$gid.' AND deposit2='.$int_depo) >= $arr_item['deposit_guild']) {
								addnav('Zurück');
								addnav('Zu den Lagerräumen','dg_main.php?op=in&subop=buildings&building_op=deposit&type='.$str_type);
								output('`8Du kannst maximal '.$arr_item['deposit_guild'].' Exemplare dieses Möbelstücks in einem Raum abstellen!');
								
								$bool_change = false;
							}
						}
						
						if($bool_change) {
							item_set(' id='.$arr_item['id'],
										array(
											'deposit2'=>$int_depo
											)
										);
							redirect('dg_main.php?op=in&subop=buildings&building_op=deposit&type=furniture');
						}
					break;
					
					// Möbel wegwerfen
					case 'remove':
						
						$arr_item = item_get('id='.(int)$_GET['id'],false);						
						
						dg_commentary($gid,':`$entfernt '.$arr_item['name'].'`$ aus dem Gildenlager.','invent');
						
						item_delete(' id='.(int)$_GET['id']);

						redirect('dg_main.php?op=in&subop=buildings&building_op=deposit&type=furniture');
					break;
					
					// Insignie produzieren
					case 'produce_regalia':
						item_delete(' owner='.ITEM_OWNER_GUILD.' AND tpl_id="insgnteil" AND deposit1='.$gid, $int_parts_needed);
					
						$guild['regalia']++;
						
						dg_commentary($gid,'/msg`@Eine neue Insignie wurde geschaffen!','invent',1);
						
						output('`8Eifrige, huschende Gnome wuseln hin und her, bringen die Apparaturen zum
								Dampfen. Bald schon liegt etwas Rauch in der Luft. Pfeifend walzen die Pressen
								die Insigniensplitter zusammen, um sie anschließend in sengender Hitze zu vereinen.`n
								Spritzelnde Funken prallen an die Mauern des Gewölbes, die fleißigen Arbeiter können gerade
								noch zur Seite springen, als sich der Deckel eines Kessels schwerfällig hebt und ein glitzerndes,
								pyramidenförmiges und fast schon blendend schönes Artefakt hervorhebt! Um es herum scheint die Luft 
								vor Magie zu vibrieren.. eine Insignie wurde geschaffen!`n`n
								Du kannst sie nun den regelmäßig erscheinenden Paladinen des Königs zum Verkauf anbieten oder im Lager 
								verschimmeln lassen.');
						
						addnav('Hurra!','dg_main.php?op=in&subop=buildings&building_op=deposit&type=regalia');
					break;
				
					// Eingangsansicht
					default:
												
						// Itemid gegeben = näher betrachen
						if($_GET['id']) {
							
							$arr_item = item_get('id='.(int)$_GET['id']);
																			
							output('`qDu wirfst einen genaueren Blick auf '.$arr_item['name'].'`q. Wenn du es beschreiben solltest,
									würdest du es in etwa so tun:`n'.$arr_item['description'].'`q.`n');
							
							if($arr_item['deposit2'] == $session['user']['acctid']) {
								output('`qDu erinnerst dich, dass dieser Gegenstand einmal dir gehört hat!`n');
							}
							else {
							
								$sql = 'SELECT name,acctid FROM accounts WHERE acctid='.$arr_item['deposit2'].' AND guildid='.$gid;
								$res = db_query($sql);
								
								if(db_num_rows($res)) {
									
									$arr_owner = db_fetch_assoc($res);
									output('`qAn dem Gegenstand baumelt ein Kärtchen, auf welchem ein Name verzeichnet ist: '.$arr_owner['name'].'`q`n');
									
								}
								else {
									output('`qDer Gegenstand scheint niemandem zu gehören.`n');
								}
							}
							
							
							
							if($str_type == 'loot') {
								addnav('Aktionen');
								output('`n`qDu hast die Möglichkeit, '.$arr_item['name'].'`q mit dir zu nehmen, benötigst dafür allerdings einen Waldkampf!');
								addnav('Mitnehmen','dg_main.php?op=in&subop=buildings&building_op=deposit&act=out&id='.$arr_item['id'].'&type='.$str_type);
							}
														
							if($str_type == 'furniture') {
								addnav('Aktionen');
								
								output('`n`qDu hast die Möglichkeit, '.$arr_item['name'].'`q endgültig aus der Gilde zu entfernen!');
								addnav('Wegwerfen','dg_main.php?op=in&subop=buildings&building_op=deposit&act=remove&id='.$arr_item['id'].'&type='.$str_type);
								
								// Wenn noch nicht eingelagert
								if($arr_item['deposit2'] != ITEM_LOC_GUILDHALL && $arr_item['deposit2'] != ITEM_LOC_GUILDEXT) {
									output('`n`qDu hast die Möglichkeit, '.$arr_item['name'].'`q als Möbelstück in den Gildenräumen zu verwenden!');
									if(!empty($guild['ext_room_name'])) {
										addnav('In '.$guild['ext_room_name'].'`0 packen!','dg_main.php?op=in&subop=buildings&building_op=deposit&act=furniture_set&id='.$arr_item['id'].'&what=ext');
									}
									addnav('In Gildenhalle packen!','dg_main.php?op=in&subop=buildings&building_op=deposit&act=furniture_set&id='.$arr_item['id'].'&what=hall');	
								}
								else {
									output('`n`qDu hast die Möglichkeit, '.$arr_item['name'].'`q von seinem aktuellen Standplatz als Möbelstück wieder in die Gewölbe zu verfrachten!');
									
									addnav('In Gewölbe packen!','dg_main.php?op=in&subop=buildings&building_op=deposit&act=furniture_set&id='.$arr_item['id'].'&what=out');
								}
							}
							
							addnav('Zurück');
							addnav('Zu den Lagerräumen','dg_main.php?op=in&subop=buildings&building_op=deposit&type='.$str_type);						
							
						}
						else {
																					
							output('`8Du steigst die breiten, abgetretenen Treppenstufen herab in den Keller des Gildenhauses.`n
									Neugierig inspizierst du das Lagergewölbe deiner Gilde. Schmale, in die Decke
									eingelassene und natürlich vergitterte Fensteröffnungen lassen etwas Licht in die unterirdischen 
									Räume und auf die sorgsam aufgestapelten Dinge fallen.`n
									`n');
							
							$str_lnk = 'dg_main.php?op=in&subop=buildings&building_op=deposit&type=';
							
							addnav('Gewölbe - Eingang');
							addnav((empty($str_type) ? '`^':'').'Inventur',$str_lnk);
							
							addnav('Gewölbe - Beutestücke');
							addnav('Einlagern',$str_lnk.'loot&act=in');
							addnav(($str_type=='loot' ? '`^':'').'Ansehen',$str_lnk.'loot');
							
							addnav('Gewölbe - Insignien');
							addnav('Einlagern',$str_lnk.'regalia&act=in');
							addnav(($str_type=='regalia' ? '`^':'').'Ansehen',$str_lnk.'regalia');						
							
							addnav('Gewölbe - Möbel');
							addnav('Einlagern',$str_lnk.'furniture&act=in');
							addnav(($str_type=='furniture' ? '`^':'').'Ansehen',$str_lnk.'furniture');
							
							if(!empty($str_type)) {
									
								output($str_intro_txt);
										
								if($str_type == 'regalia') {
									
									output('`8Bisher liegen in den angrenzenden Lagerkellern der Gilde '.$int_count.' Insigniensplitter bereit,
									die bereits zur Weiterverarbeitung auserkoren sind.`n');	
									
									$int_max_regalia = getsetting('dgmaxregalia',15);
									
									if($int_max_regalia <= $guild['regalia']) {
										output('`$Die Insignienlager der Gilde sind voll. Bis zum nächsten Erscheinen der Paladine kann die 
												Gilde keine Insignien mehr produzieren.`n');
									}
									else {
																		
										if($int_count >= $int_parts_needed) {
											output('Dies sollte reichen, um daraus eine Insignie zu schmieden! Sobald ein Mitglied
												der Führungsriege den Befehl dazu gibt, wird mit der Produktion begonnen.`n`n');
											if($team) {
												addnav('Aktionen');
												addnav('`@Insignie produzieren!`0','dg_main.php?op=in&subop=buildings&building_op=deposit&act=produce_regalia');
											}
										}
									}
								}
																								
								$arr_options = array('Näher betrachten'=>'');
								// Lagerliste anzeigen
								item_show_invent($str_show_sql , false, 0, 1, 1, 'Die Lagerhallen der Gilde sind in diesem Bereich völlig leer.', $arr_options);
							}
							else {
								addcommentary();
								viewcommentary('guild-'.$gid.'_invent',($team ? 'Etwas verkünden:':'Du solltest hier besser schweigen!'),25,'verkündet',false,($team?true:false));
							}
																																			
							addnav('Zurück');							
						}
												
					break; 
				
				}
				
				$rowe = user_get_aei('itemsin,itemsout');
		

				addnav('Zur Halle','dg_main.php?op=in');
				
			break;
			// END Lagerräume
				
			case 'waffenkammer':
				$lvl = $guild['build_list'][DG_BUILD_WAFFENKAMMER];				
				dg_show_header('Die Waffenkammer ('.$dg_build_levels[$lvl].')');
				
				$min = 65 - pow(1.3,$lvl) * 4;
				
				$item = $guild['building_vars'][DG_BUILD_WAFFENKAMMER]['itemlist'][$session['user']['acctid']];
				if(is_array($item)) {
					$price_factor = 2.5 - pow(1.05,$lvl);
					$price_gold = max(round($item['gold'] * $price_factor),1500);
					$price_gems = max(round($item['gems'] * $price_factor),2);
				}
							
				if($_GET['act'] == 'in') {
					
					if($guild['building_vars'][DG_BUILD_WAFFENKAMMER]['itemlist'][$session['user']['acctid']]) {
						output('`6"Du hast schon was deponiert. Hol das erst mal raus!"`3');
					}
					else {
					
						if($_GET['id']) {
							$item = item_get( ' id='.(int)$_GET['id'] , false );
									
							$guild['building_vars'][DG_BUILD_WAFFENKAMMER]['itemlist'][$session['user']['acctid']] = $item;
							
							output('`3Du lieferst '.$item['name'].'`3 ab. Der Ork wirft deine Waffe mehr in die Vitrine, als dass er sie legt und blickt sich dann weiter grimmig um. Insbesondere in deiner Richtung.');
							
							item_delete( ' id='.(int)$_GET['id'] );
						}
						else {					
							
							output('`6"Welche von den Dingern willst du da reintun?" `3raunzt er dir zu`n`n');
							
							$arr_options = array('Einlagern'=>'');
										
							item_show_invent(' equip='.ITEM_EQUIP_WEAPON.' AND deposit1!='.ITEM_LOC_EQUIPPED.' AND owner='.$session['user']['acctid'], false, 0, 1, 1, 'Leider findest du keine einzige Waffe in deinem Beutel.', $arr_options);
									
						}	
					}			
					
					addnav('Zurück','dg_main.php?op=in&subop=buildings&building_op=waffenkammer');
					
				}
				
				elseif($_GET['act'] == 'out') {
					
					if($session['user']['gold'] >= $price_gold && $session['user']['gems'] >= $price_gems) {
					
						$item = $guild['building_vars'][DG_BUILD_WAFFENKAMMER]['itemlist'][$session['user']['acctid']];
						unset($guild['building_vars'][DG_BUILD_WAFFENKAMMER]['itemlist'][$session['user']['acctid']]);
						
						if(e_rand(1,100) < $min) {
							output('`3Als du erneut die Kammer betrittst, um dein Eigentum abzuholen, drückt dir der Ork ziemlich hastig die Klinge in die Hand und nuschelt was von `6"Kampfübungen... hat ziemlich gelitten..Klinge ist leicht beschädigt"`3 Du betrachtest dein Schwert und stellst fest, dass das etwas kaputte Material ab sofort wohl etwas weniger Schaden machen wird. Als du dich gerade beschweren willst, fuchtelt der Ork nur angsteinflößend mit den Armen und gibt komische Laute von sich, so dass du schnell diesen Ort verlässt.
							`n`n
							`3Der Angriff scheint von '.$item['value1'].' auf '.($item['value1']-1).' gesunken zu sein!');
							$item['value1']--;
							if($item['value1'] <= 0) {
								output('`nDadurch ist die Waffe zu nichts mehr zu gebrauchen!');
								unset($item);
							}
													
						}
						
						if(is_array($item)) {
							
							$item['tpl_value1'] = $item['value1'];
							$item['tpl_value2'] = $item['value2'];
							$item['tpl_gold'] = $item['gold'];
							$item['tpl_gems'] = $item['gems'];
							$item['tpl_name'] = $item['name'];
							$item['tpl_id'] = isset($item['tpl_id']) ? $item['tpl_id'] : 'waffedummy';
							$item['tpl_description'] = $item['description'];
						
							item_add($session['user']['acctid'],0,false,$item);
																							
							$session['user']['gems'] -= $price_gems;
							$session['user']['gold'] -= $price_gold;
							
							output('`n`n`3Du zahlst dem Ork den Preis und nimmst '.$item['name'].'`3 wieder an dich.');
						}
					}
					else {
						output('`3Der Ork will gerade deine Waffe einer der Vitrinen entnehmen, als sein Blick auf die wenigen Goldstücke fällt, die du ihm hingelegt hast `6"Das ist alles? Da behalt ich das Ding lieber mal!"`3 Verärgert wirft er dir deine Münzen entgegen und schaut dich grimmig an, so dass du schnell das Weite suchen willst');
					}
					addnav('Zurück','dg_main.php?op=in&subop=buildings&building_op=waffenkammer');
				}	
				else {
					
					output('`3Du trittst durch eine düstere Eichentür, hinter der du bisher nur den Kerker vermutet hättest, denn hier im Kellergewölbe herrscht Dunkelheit, die nur von vereinzelten Fackeln durchbrochen wird und eine eisige Stille. Doch zu deiner Verwunderung trittst du in einem Raum, der einem Paradies für jeden Kämpfer gleicht. Überall stehen glänzende, aber auch schon rostende Rüstungen, stählerne Helme liegen sorgfältig geordnet auf einem Holzbalken, doch deine Aufmerksamkeit wird vor allem angezogen von den unzähligen Waffen, die hier bereitliegen. Zwischen all den Schwertern, Dolchen und Äxten fällt dir plötzlich ein missmutig gelaunter Ork ins Auge, der dich ungeduldig anraunzt 
					`6"Was willst\'n du hier?"`3 Schon der Gestank der Kreatur, aber auch der Anblick lässt den Ekel in dir herauf kriechen, doch du antwortest, ohne es dir anmerken zu lassen `9"Ich möchte meine Klinge in eure Obhut geben" `3Fordernd streckt der Ork seine Hand nach deinem Schwert aus `6"Für nen richtigen Preis, pass ich drauf auf"`3`n`n');					
					
					// Waffen von Nichtmehr-Existierenden bzw. Ausgetretenen entfernen
					if(count($guild['building_vars'][DG_BUILD_WAFFENKAMMER]['itemlist']) > 0) {
						$arr_member_list = dg_load_member_list($gid);
						
						foreach($guild['building_vars'][DG_BUILD_WAFFENKAMMER]['itemlist'] as $acctid=>$i) {
							if(!isset($arr_member_list[$acctid])) {
								unset($guild['building_vars'][DG_BUILD_WAFFENKAMMER]['itemlist'][$acctid]);
							}
						}
					}
					// END Redundanzcheck					
					
					if(is_array($item)) {
																		
						output('`n`3Bisher deponiert: '.$item['name'].'`3'.(!empty($item['description']) ? ' ('.$item['description'].'`3)' : '').'.`n');
						
						if($session['user']['level'] > 1) {
							output('`3Kosten für\'s Herausholen: `^'.$price_gold.'`3 Gold und `^'.$price_gems.'`3 Edelsteine.');
							addnav('Herausholen','dg_main.php?op=in&subop=buildings&building_op=waffenkammer&act=out');
						}
						else {
							output('`3Auf Level 1 kannst du die Waffe nicht herausholen!');
						}
						
					}
					else {
						
						// Vorerst mal auf Lvl 15 auch deponieren möglich		
						if($session['user']['level'] < 16) {
							addnav('Deponieren','dg_main.php?op=in&subop=buildings&building_op=waffenkammer&act=in');
						}
						else {
							output('`n`3Auf Level 15 kannst du keine Waffe deponieren!');
						}
					}
					
					
					addnav('Zur Halle','dg_main.php?op=in');
				}
				
				break;			
			
			case 'schmiede':
				$lvl = $guild['build_list'][DG_BUILD_SCHMIEDE];				
				dg_show_header('Die Schmiede ('.$dg_build_levels[$lvl].')');
				
				$impr_lvl = min($lvl,4);	// jeden Lvl ein Upgrade auf Verbesserung, max. 4
				$impr = $impr_lvl;
				$price_rebate = pow( 1.25 , max($lvl,0) ) * 300;
				$price_gold = round(4500 + ($impr_lvl * 800) - $price_rebate);
					
				if($_GET['act'] == 'ok') {
					
					if($price_gold > $session['user']['gold']) {
						output('`n`tDie Zornesröte steigt ihm ins Gesicht, nachdem du ihm voll Verlegenheit gestanden hast, nicht genug Gold dabeizuhaben: `T"Wie kannst du es wagen, mich zu belästigen?! Für umsonst "`t - dieses Wort spricht er voller Widerwillen aus - `T" arbeitet KEIN Zwerg! Verschwinde, du, bevor.."`t Drohend hebt er seine Axt und tut einen Schritt in deine Richtung. Du machst dich besser davon...');
					}
					else {
						
						$name = $session['user']['armor'].' G:'.$impr;
						$skill = $session['user']['armordef'] + $impr;
						$val = $session['user']['armorvalue'] + $price_gold * 0.5;
												
						item_set_armor($name, $skill, $val, 0, 0, 1);
						
						$session['user']['gold'] -= $price_gold;
						
						output('`n`tEr hämmert voller Inbrunst auf deiner Rüstung herum, so dass die Funken stieben! Befriedigt überreicht er dir gegen '.$price_gold.' Gold das gute Stück. `T"Hier hast du! Und nun lass mich weiterarbeiten.."');
					}
				}
				else {
					
					output('`tDu betrittst forschen Schrittes die hintere Gewölbeecke, die dem Schmied deiner Gilde bestimmt ist. Ein ohrenbetäubender Lärm erfüllt die stickige Luft, wenn Azaghal, der zwergene Schmied, seinen Hammer auf den Amboss niederfahren lässt. Nachdem du ihm vorsichtig auf die Schulter getippt hast, hält er inne und wendet dir sein bärtiges, verschwitztes Antlitz zu, etwas ergrimmt über die Unterbrechung: `T"'.$session['user']['name'].'`T, nehme ich an! Falls es dein Begehren ist, diese/s '.$session['user']['armor'].'`T zu verstärken, so sag es gleich.."');
								
					if(strpos($session['user']['armor'],' G:')) {
							
						output('`n`n`tDie Zornesröte steigt ihm ins Gesicht, nachdem er einen genaueren Blick auf deine Rüstung geworfen hat: `T"Wie kannst du es wagen, mich zu belästigen?! An diesem exzellenten Stück kann selbst ich nichts mehr tun! Verschwinde, du, bevor.."`t Drohend hebt er seine Axt und tut einen Schritt in deine Richtung. Du machst dich besser davon...');
							
					}
					else {
						
						output('`t Du willst gerade zum Sprechen ansetzen, als er in seinen Bart grummelt `T"`^'.$price_gold.' Gold`T, dafür mache ich daraus ein hervorragendes, hm.. ich nenne es mal `^'.$session['user']['armor'].' G:'.$impr.'`t! Also, was ist nun?"');
									
						$price_bonus = max($lvl - 5,0);
						$lvl -= $price_bonus;
						$link = 'dg_main.php?op=in&subop=buildings&building_op=schmiede&act=ok';
						output('`n`nJa, <a href="'.$link.'">verbesser\' meine Rüstung!</a>',true);
						
						addnav('',$link);
					}
					
				}
								
				addnav('Zur Halle','dg_main.php?op=in');
				
				break;	// END schmiede
				
			case 'juwelier':
				$lvl = $guild['build_list'][DG_BUILD_JUWELIER];				
				dg_show_header('Der Juwelier ('.$dg_build_levels[$lvl].')');
				
				$impr_lvl = ceil($lvl / 2);	// jeden 2. Lvl ein Upgrade auf Wahrscheinlichkeit, ansonsten nur auf Preis
				$price_rebate = max($lvl - $impr_lvl,0) * 200;
				$price_gold = 5500 + ($impr_lvl * 100) - $price_rebate;
				
				$transferred = user_get_aei('gemsin');

				$max = getsetting('dgmaxgemstransfer',2) + $impr_lvl;
				$max = max($max - $transferred['gemsin'],0);
													
				if($_GET['act'] == 'ok') {
						
					if($price_gold > $session['user']['gold']) {
						output('`rDie Elfe schüttelt nur stumm ihren hübschen Kopf, als du ihr deine Goldvorräte zeigst.');
					}
					else {
						$session['user']['gold'] -= $price_gold;
						$min = 50 - (pow(1.25,$impr_lvl) * 5);
						if(e_rand(1,100) >= $min) {
							$session['user']['gems']++;
							
							user_set_aei(array('gemsin'=>$transferred['gemsin']+1));
						
							output('`rLächelnd überreicht sie dir einen funkelnden Juwel: `5"Ich hoffe, ihr seid zufrieden!"');
						}
						else {
							output('`rMit einer Miene des Bedauerns erklärt sie dir, dass dein Gold leider verloren ist. Die Herstellung schlug fehl!');
						}
					}
				}
				
				else {
					
					output('`rSchon von weiten kannst du die Türen zum Juwelier ausmachen, denn ebenso wie der gesamte Raum dahinter, ist auch die Eingangstür prunkvoll mit Schmuck und Edelsteinen verziert.
					Als du in den recht kleinen Raum trittst, wirst du schier geblendet, angesichts all des Golds und der Juwelen. 
					Schließlich haben sich deine Augen daran gewöhnt und du kannst eine hübsche, junge Elfe hinter einem langen Verkaufstresen erkennen, die dir reich geschmückt mit Ketten, Ringen und Armreifen entgegenlächelt.
					In den Vitrinen liegen die seltensten und schönsten Schmuckstücke die du je gesehen hast. Plötzlich erhebt die Elfe ihre zarte Stimme:
					`5"Schau dich ruhig um, '.$session['user']['login'].'. '.($max > 0?'Für nur `^'.$price_gold.'`5 Gold können wir dir einen dieser hübschen Steine anfertigen! Aber es gibt selbstverständlich keine Garantie auf Erfolg..':'Doch leider können wir dir heute nichts mehr anbieten. Unsere Vorräte sind erschöpft!').'"');
									
					$link = 'dg_main.php?op=in&subop=buildings&building_op=juwelier&act=ok';
					output('`n`n'.($max > 0?'<a href="'.$link.'">Einen Edelstein herstellen!</a>':'Keine Edelsteine mehr machbar!'),true);
					
					addnav('',$link);
				}
								
				addnav('Zur Halle','dg_main.php?op=in');
				
				break;	// End juwelier
			
			case 'geheim':
				$lvl = $guild['build_list'][DG_BUILD_GEHEIM];				
				dg_show_header('Der Geheimdienst ('.$dg_build_levels[$lvl].')');
				
				$impr_lvl = ceil($lvl / 2);	// jeden 2. Lvl ein Upgrade auf Wahrscheinlichkeit, ansonsten nur auf Preis
				$price_rebate = max($lvl - $impr_lvl,0) * 120;
				$price_gold = 500 + ($impr_lvl * 300) - $price_rebate;
									
				if($_GET['act'] == 'ok') {
					
					$hid = (int)$_POST['hid'];
					
					$sql = 'SELECT housename,description,attacked,a.name,h.gold,h.gems,h.status FROM houses h LEFT JOIN accounts a ON a.acctid=owner WHERE houseid='.$hid;
					$res = db_query($sql);
					if(db_num_rows($res) == 0) {
						output('`!Der Spion schüttelt nur stumm den Kopf.. `1"Dieses Haus gibt es nicht!"`! raunt er dir zu');
						addnav('Neue Suche','dg_main.php?op=in&subop=buildings&building_op=geheim');
					}
					else {
						if($price_gold > $session['user']['gold']) {
							output('`!Der Spion schüttelt nur stumm den Kopf.. `1"Umsonst arbeiten tun nur die Dummen!"`! raunt er dir zu');
						}
						else {
							$house = db_fetch_assoc($res);
						
							$session['user']['gold'] -= $price_gold;
							
							$min = 40 - ($impr_lvl * 6);
							if(e_rand(1,100) >= $min && ($house['status'] < 30 || $house['status'] >= 40)  ) {
								
								// Code aus houses.php
								
								$pvptime = getsetting("pvptimeout",600);
	
								$pvptimeout = date("Y-m-d H:i:s",strtotime(date("r")."-$pvptime seconds"));
						
								$days = getsetting("pvpimmunity", 5);
						
								$exp = getsetting("pvpminexp", 1500);
								
								if(is_array($guild['treaties'])) {
									foreach($guild['treaties'] as $id=>$t) {
										if( dg_get_treaty($t)==1 ) {	// wenn Frieden mit dieser Gilde
											$ids .= ','.$id;
										}
									}		
								}
							
								$sql = "SELECT acctid,name,maxhitpoints,defence,attack,level,laston,loggedin,a.gold FROM keylist k LEFT JOIN accounts a ON a.acctid=k.owner
										WHERE (k.value1=".$hid." AND k.hvalue=".$hid.") AND 
										(locked=0) AND
										(alive=1 AND location=".USER_LOC_HOUSE.") AND 
										!(".user_get_online().") AND
										(age > $days OR dragonkills > 0 OR pk > 0 OR experience > $exp) AND
										(acctid <> ".$session[user][acctid].") AND
										(pvpflag <> '5013-10-06 00:42:00') AND
										(pvpflag < '$pvptimeout') AND 
										(guildid = 0 OR guildid NOT IN (0".$ids.") OR guildfunc=".DG_FUNC_APPLICANT.") ORDER BY maxhitpoints DESC
										";
								$res = db_query($sql);
								
								output('`!Nach einiger Zeit übergibt man dir eine Schriftrolle mit folgendem Inhalt:`&`n`n`b'.$house['housename'].'`b`0 (Besitzer: '.$house['name'].'`0)`n'.$house['description'].'`n`n'.($house['attacked'] > 0 ? '`&`n(Heute bereits '.$house['attacked'].'mal angegriffen!)':'`&`n(Wurde heute noch nicht beraubt)').'`&`n`n');
								if($lvl > 2) {
									output('Gold im Haus: '.$house['gold'].', Edelsteine im Haus: '.$house['gems'].'`n`n');	
								}
								
								output('Diese Helden stehen bereit, um gegen dich anzutreten:`n`n');	
								
								while($a = db_fetch_assoc($res)) {
									
									output('`n'.$a['name'].'`0 ');
									if($lvl > 3) {
										$dif = ($session['user']['attack'] - $a['attack']) + ($session['user']['defence'] - $a['defence']);
										output(' `i(');
										if($dif > 20) {
											output('Nicht der Rede wert');
										}
										elseif($dif > 10) {
											output('Schwächer');
										} 
										elseif($dif > 0) {
											output('Ähnlich stark');
										} 
										elseif($dif < -20) {
											output('Zu stark');
										} 
										elseif($dif < -10) {
											output('Stärker');
										} 
										elseif($dif < 0) {
											output('Ähnlich stark');
										} 
										output(')`i');
									}
									if($lvl > 4) {
										output(' - Hat `^'.$a['gold'].'`0 Gold dabei!');
									}	
									
								}
														
							}
							else {
								$sql = 'SELECT name FROM accounts ORDER BY dragonkills DESC LIMIT 0,10';
								$res = db_query($sql);
								
								output('`!Nach sehr kurzer Zeit übergibt man dir eine Schriftrolle mit folgendem Inhalt:`n`n');
								
								while($a = db_fetch_assoc($res)) {
									output($a['name'].'`n');
								}
								output('`n`n`!Irgendwas stimmt hier doch nicht ganz..`n');
								
								if($house['status'] >= 30 && $house['status'] < 40) {output('Vielleicht ist das Haus ja ein Versteck?!');}
								
							}
						}
					}	// END haus gefunden
				}
				
				else {
									
					$link = 'dg_main.php?op=in&subop=buildings&building_op=geheim&act=ok';
					output('`!Während du suchend dem dunkelsten Gang im ganzen Gebäude folgst, fällt dir plötzlich ein kaum merklicher, sehr schwacher Lichtschein auf, der durch eine nicht ganz geschlossene Tür fällt. 
					Du näherst dich der Tür, hörst gedämpfte Stimme miteinander flüstern. Langsam öffnest du die Holztür und trittst in einen düsteren Raum, der nur von einer Lampe, die in der Mitte des Raumes an der Decke angebracht ist, erhellt wird. 
					Die Blicke vieler Gestalten, deren Gesicht fast vollständig durch die Kapuzen der schwarzen Mäntel verborgen ist, richten sich auf dich. Einer der Männer tritt auf dich zu, nimmt dich beiseite und du erlaubst dir ohne Begrüßung die Frage:
					"Seid ihr die Ausgestoßenen? Die Spione, die für Gold fast alles herausfinden können?"
					Die vermummte Gestalt mustert dich lange und nickt schließlich: `1"Du musst uns nur die Hausnummer nennen und wir finden gegen `^'.$price_gold.'`1 Gold für dich heraus, wer sich dort in Sicherheit wiegt."
					`n`n
					<form action="'.$link.'" method="POST">Hausnr.: <input name="hid" type="text" size="3" maxlength="3"> <input type="submit" value="Haus ausspionieren!"></form>
					',true);
					
					addnav('',$link);
				}
								
				addnav('Zur Halle','dg_main.php?op=in');
				
				break;	// End 
				
			case 'bibli':
				$lvl = $guild['build_list'][DG_BUILD_BIBLI];				
				dg_show_header('Die Bibliothek ('.$dg_build_levels[$lvl].')');
				
				$allow_non_special = ($lvl > 3) ? true : false;
				$price_gold = ceil(8000 - ( 450 * pow(1.4,$lvl) ) );
				$price_non_special = $price_gold + 2000;
				
				$sql = 'SELECT specname,specid FROM specialty WHERE active="1"';
				$res = db_query($sql);
				
				$rowe = user_get_aei('seenacademy');
																									
				if($_GET['act'] == 'ok') {
						
					if($price_gold > $session['user']['gold']) {
						output('`gLeider besitzt du nicht genügend Gold, weswegen dir der Mann die Benutzung der Bücher verweigert!');
					}
					else {
						$temp_spec = 0;
						if($session['user']['specialty'] != $_GET['spec']) {
							$price_gold = $price_non_special;
							$temp_spec = $session['user']['specialty'];
							$session['user']['specialty'] = $_GET['spec'];
						}
						 
						$session['user']['gold'] -= $price_gold;
						
						increment_specialty();
												
						if($temp_spec) {
							$session['user']['specialty'] = $temp_spec;
						}
						
						user_set_aei(array('seenacademy'=>1));
						
						output('`gZufrieden und mit rauchendem Kopf lehnst du dich zurück - die Schufterei hat etwas gebracht! Du klappst das Buch zu und machst dich auf den Rückweg.');
												
					}
				}
				
				else {
									
					output('`gViele Wesen mit Büchern in den Händen kommen dir auf deinem Weg in die große Bibliothek entgegen. 
					Du öffnest langsam die riesigen Flügeltüren aus Holz, trittst in die weitläufige Halle ein und hast das Gefühl, 
					den Überblick zu verlieren. Der ganze Raum ist von Regalen durchzogen, in denen jedes Buch - genau beschriftet - 
					ordentlich eingeordnet ist. Langsam gehst du den Mittelgang entlang, wirfst immer wieder rechts und links einen Blick
					in die Regalreihen und gelangst schließlich zu einem Schreibtisch, hinter dem ein schon recht alt wirkender Mann sitzt,
					der dir aus seinem faltigen Gesicht entgegen schaut. Nachdem du ihm erklärt hast, dass du dein Wissen über deine
					besonderen Fähigkeiten erweitern möchtest, schreibt er dir in feiner Schrift mehrere Buchnummern auf ein Stück Pergament.
					Er zeigt dir noch den Weg und du machst dich, die Regalreihen genau abzählend auf den Weg. 
					Als du endlich vor den entsprechenden Büchern stehst, 
					'.($rowe['seenacademy']?'bemerkst du, dass dir heute nicht mehr danach ist, deine Fähigkeiten noch weiter zu üben.':
					'wirst du gleichzeitig vor die Wahl gestellt, welche Fähigkeit du vertiefen möchtest...') );
					
					if($rowe['seenacademy'] == 0) {
					
						while($spec = db_fetch_assoc($res)) {
							if($spec['specid'] == $session['user']['specialty'] || $allow_non_special) {
								$link = 'dg_main.php?op=in&subop=buildings&building_op=bibli&act=ok&spec='.$spec['specid'];	
								output('
								`n`n
								<a href="'.$link.'">'.$spec['specname'].' erlernen!</a> ('.(($spec['specid'] != $session['user']['specialty']) ? $price_non_special : $price_gold).' Gold)
								',true);
								addnav('',$link);
							}
						}
					}
						
				}

								
				addnav('Zur Halle','dg_main.php?op=in');
				
				break;	// End bibli
				
			case 'labor':
				
				// Zaubertränke herstellen für
				// Angriff (ab lvl 4), Def (ab lvl 1)
				// Je höher Lvl, desto mehr Prozent Zuwachs
				$lvl = $guild['build_list'][DG_BUILD_LABOR];								
				dg_show_header('Das Alchemielabor ('.$dg_build_levels[$lvl].')');
				
				$name = $_GET['what'] == 'angr' ? 'Angriff' : 'Verteidigung';
								
				$price_gold = 1000;
				$impr = round(pow(1.05,$lvl),2) * 100;
																				
				if($_GET['act'] == 'ok') {
						
					if($price_gold > $session['user']['gold']) {
						output('`V"Hat es noch nicht einmal Gold dabei, ist denn das zu glauben.. Was für ein nutzloses Wesen, nein, halt inne,.. ist das da nicht die fehlende Zutat?"`5 bei diesen Worten schaut er dich derart bedrohlich an, dass du es für besser hältst, zu verschwinden.');
					}
					else {
						$what = $_GET['what'];
																									
						// Zu bereits vorhandenen Zaubern der selben Art dazuzählen
						$item = item_get(' owner='.$session['user']['acctid'].' AND i.tpl_id="trnk'.$what.'" ');
						
						if($item['id']) {
							
							if($item['value1'] >= 5) {
								output('`R"Es bekommt wohl gar nich genug? Seine Taschen beulen schon vor Tränken!"');
							}
							else {
								item_set(' id='.$item['id'], array('value1'=>$item['value1']+5,'hvalue2'=>$impr) );
																
								$session['user']['gold'] -= $price_gold;
							
								output('`R"Erbärmlich für welch Grabszeuch manche Wesen doch Golddd ausgeben.. "`5 der Goblin keucht heftig, wirft dir den Zaubertrank zu und rührt weiter in seinem Kessel, ohne dich zu beachten.');
							}
						}
						else {
							
							$item['tpl_description'] = '`7Dieser exklusiv in der Gilde '.$guild['name'].'`7 gebraute Zaubertrank stärkt '.$name.' für eine gewisse Zeit.';
							$item['tpl_value1'] = 1;
							$item['tpl_value2'] = 1;
							$item['tpl_hvalue2'] = $impr;
							$item['tpl_gold'] = $price_gold*0.8;
							
							item_add($session['user']['acctid'],'trnk'.$what,true,$item);

							$session['user']['gold'] -= $price_gold;
							
							output('`R"Erbärmlich für welch Grabszeuch manche Wesen doch Golddd ausgeben.. "`5 der Goblin keucht heftig, wirft dir den Zaubertrank zu und rührt weiter in seinem Kessel, ohne dich zu beachten.');
						}
										
					}
				}	// END ok
				
				else {
					
					output('`5Du betrittst ein offenkundiges Arbeitszimmer, schwach erleuchtet von Öllampen, deren Schwaden die Luft stickig werden lassen und der beißende Geruch allerlei alchemistischer Tinkturen und Tränke lässt dir die Augen tränen. Benommen siehst du dich in dem kleinen Raum etwas genauer um und dein Blick schweift über Wände, bedeckt mit Regalen, voll von alten, staubigen Pergamenten und Büchern, kleinen Fläschchen und Phiolen, sowie allerlei Kräuter und Ingredentien. In der Mitte des Raumes befindet sich ein kleiner Tisch, an dem ein noch kleinerer Gnom vor einem Kessel hockt und hin und wieder kichernd und vor sich hin brabbelnd die eine oder andere Zutat nachwirft.
						Als schließlich auch Goblinaugen im Kessel verschwinden, lässt du ein kurzes Räuspern hören.
						`R"Was will es hier? Was stört es mich?"`5 brabbelt dir die kleine Kreatur entgegen ohne dich anzusehen. `R"Es wird wohl kaum für meine Zwergenbart-Suppe gekommen sein, also sprich!"
					`5');
					
					$percent = $impr - 100;
										
					$link = 'dg_main.php?op=in&subop=buildings&building_op=labor&act=ok&what=def';	
					output('`n`n<a href="'.$link.'">Verteidigungszaubertrank brauen!</a> `0('.$price_gold.' Gold, `R"Derr bietet dirr `^'.$percent.'`R Prrrozent Zuwachs!"`0)',true);
					addnav('',$link);
					
					if($lvl > 3) {
						$link = 'dg_main.php?op=in&subop=buildings&building_op=labor&act=ok&what=angr';	
						output('`n`n<a href="'.$link.'">Angriffszaubertrank brauen!</a> `0('.$price_gold.' Gold, `R"Derr bietet dirr `^'.$percent.'`R Prrrozent Zuwachs!"`0)',true);
						addnav('',$link);
					}	
						
				}
								
				addnav('Zur Halle','dg_main.php?op=in');
				
				break;	// End alchemie
				
			case 'gift':
				
				// Ermöglicht direkten Zugriff auf Hausschatz, ohne gegen Wache etc. kämpfen zu müssen 
				// Kostet Gold, PvP-Kämpfe und Ansehen
				// Mit gewisser Wahrscheinlichkeit: Tod und Expverlust
				$lvl = $guild['build_list'][DG_BUILD_GIFT];
				dg_show_header('Die Giftmischerei ('.$dg_build_levels[$lvl].')');
								
				$price_gold = ($lvl > 3) ? 500 : 750;
				$price_pvp = 2;
				$price_turns = 0;
									
				if($_GET['act'] == 'ok') {
					
					$hid = (int)$_POST['hid'];
															
					if(db_num_rows(db_query('SELECT id FROM keylist WHERE value1='.$hid.' AND owner='.$session['user']['acctid'])) || $session['user']['house'] == $hid) {
						output('`@Es gibt einfachere Methoden, um in dieses Haus zu gelangen.. Versuchs doch mal mit einem Schlüssel!');
					}
					else {
					
						$sql = 'SELECT housename,description,a.name,h.gold,h.gems FROM houses h LEFT JOIN accounts a ON a.acctid=owner WHERE houseid='.$hid;
						$res = db_query($sql);
						if(db_num_rows($res) == 0) {
							output('`@Ein Haus mit dieser Nummer existiert nicht!');
							addnav('Neue Suche','dg_main.php?op=in&subop=buildings&building_op=gift');
						}
						else {
							if($price_gold > $session['user']['gold']) {
								output('`@Verärgert stellst du fest, dass der Preis deine finanziellen Möglichkeiten übersteigt.');
							}
							else {
								if($price_pvp > $session['user']['playerfights']) {
									output('`@Heute hast du bereits zu viele deiner Spielerkämpfe aufgebraucht. Da lässt sich nichts mehr machen.');
								}
								elseif($price_turns > $session['user']['turns']) {
									output('`@Du verfügst leider über keinen Waldkampf mehr!');
								}
								else {
									$house = db_fetch_assoc($res);
								
									$session['user']['gold'] -= $price_gold;
									$session['user']['playerfights'] -= $price_pvp;
									$session['user']['turns'] -= $price_turns;
									
									output('`@Du holst die Phiole mit dem Gift hervor. Der Gestank betäubt dich selbst durch das Glas noch erheblich. Dann ziehst du langsam den Korken aus der Öffnung..');
									
									$min = 48 - 5 * pow(1.3,$lvl);
									if(e_rand(1,100) >= $min) {
										
										output('`n`n`@Tatsächlich! Es klappt: Du schläferst die patrouillierende Stadtwache ein und steigst durch ein Fenster ins Haus..`nLeider verbreiten sich schon bald Gerüchte über deine Giftbrauerei. Dein Ansehen sinkt!');
										$session['housekey'] = $hid;
										$session['user']['reputation'] -= 20;
										
//										addnews($session['user']['name'].'`7 machte erfolgreich Gebrauch von der Giftmischerei seiner Gilde.');
										
										addnav('Einsteigen..','houses_pvp.php?op=einbruch2&hidden=1&id='.$hid);
																
									}
									else {
											
										if(e_rand(1,5) == 1) {
											output('`n`n`@.. da merkst du auch schon, wie dir schwindlig wird und du zu Boden sinkst.`nDu bist tot und verlierst '.(round($session['user']['experience']*0.1)).' Erfahrung!');
											
											killplayer(0, 10, 0, '');
																						
											addnews($session['user']['name'].'`3 hat in der Giftmischerei '.($session['user']['sex'] ? 'ihrer':'seiner').' Gilde leider den falschen Trank erwischt..');
											addnav('Zu den News','news.php');
										}
										else {
											output('`n`n`@.. da merkst du auch schon, wie dir auf einmal speiübel wird.`nDu verlierst fast alle Lebenspunkte!');
											$session['user']['hitpoints'] = 1;
										}
									}
								}	// END genug Kämpfe
							}
						}	// END haus gefunden
					}	// END fremdes haus
				}
				
				else {
									
					$link = 'dg_main.php?op=in&subop=buildings&building_op=gift&act=ok';
					output('`@Aus dieser Ecke der Gilde treiben dir schon von weitem gelbgrüne Schwaden entgegen. Ein penetranter Geruch nach Schwefel lässt dich fast taumeln. Zu deiner Rechten köcheln verschiedenste
							Töpfe und Kessel munter vor sich hin. Dies ist die Giftmischerei: Deine Alchemisten werden dir hier vorzüglichste Tränke brauen, um jede noch so starke Stadtwache und Haustier in sanften Schlummer zu hüllen.`n
							Jedoch kostet dieses Unterfangen `^'.$price_gold.'`@ Gold und `^'.$price_pvp.'`@ Spielerkämpfe. '.($price_turns > 0 ? 'Einen Waldkampf sowieso. ':'').'Auch ist es nicht gewiss, ob das Gift nicht so stark ist, dass es dich selbst tötet!`n`n');
					output('<form action="'.$link.'" method="POST">Hausnr.: <input name="hid" type="text" size="3" maxlength="3"> <input type="submit" value="Wachen einschläfern!"></form>',true);
					
					addnav('',$link);
					
				}
				
				if($session['user']['hitpoints'] > 0) {addnav('Zur Halle','dg_main.php?op=in');}
								
				break;	// End giftmsicherei
				
			case 'kontor':
				
				// Erste 3 Lvl gibt es nur Rabatte auf: 
				//  - Wanderhändler
				//  - Mighty E / Zauberladen bei den Magiern
				// Ab Lvl 4: 
				//	- Zauberladen
				// Ab Lvl 7:
				//  - Pegasus
				// dabei bringt jeweils ein Lvl 1 % Rabatt
				$lvl = $guild['build_list'][DG_BUILD_KONTOR];				
				dg_show_header('Das Handelskontor ('.$dg_build_levels[$lvl].')');
				
				
				$arr = array('Pegasus'=>dg_calc_boni($gid,'rebates_armor',0), 
								'Wanderhändler'=>dg_calc_boni($gid,'rebates_vendor',0), 
								'Mighty E'=>dg_calc_boni($gid,'rebates_weapon',0), 
								'Zauberladen'=>dg_calc_boni($gid,'rebates_spells',0)); 
												
				output('`^Das sogenannte Handelskontor entpuppt sich als ein riesiger Lagerraum. Wo der ohnehin rare Platz nicht durch Säcke und Kisten belegt ist, drängen sich emsige Arbeiter, die ebendiese Waren durch die Gegend schleppen. An der Wand ist ein kleiner Raum für eine Tafel freigehalten. Auf dieser stehen in klaren Lettern sämtliche Rabatte geschrieben, die die Gilde auf dem derzeitigen Markt bekommt:`n`n');
				
				$out = '`c<table bgcolor="#999999" border="0" cellpadding="3" cellspacing="1"><tr class="trhead"><td>Händler</td><td>Rabatt</td></tr>';
				$i=0;
								
				foreach($arr as $name=>$reb) {
					$i++;
					$out .= '<tr class="'.($i%2?"trlight":"trdark").'"><td>`b`@'.$name.':`b </td><td>`^'.$reb.' %</td></tr>';
				}
				$out .= '</table>`c';
				output($out,true);
								
				addnav('Zur Halle','dg_main.php?op=in');
				
				break;	// End kontor
				
			case 'opfer':
				
				// Opferstätte bringt der Gilde Punkte.. und dem Opfer den Tod ;)
				$lvl = $guild['build_list'][DG_BUILD_OPFER];
				dg_show_header('Die Opferstätte ('.$dg_build_levels[$lvl].')');
				
				$points = round($lvl * 0.8);
				$exp_loose = 0.85 + max($lvl * 0.01,0.1);
				
				if($_GET['act'] == 'ok') {
					
					$session['user']['alive'] = 0;
					$session['user']['hitpoints'] = 0;
					$session['user']['gravefights'] = 0;
					$session['user']['experience'] *= 0.9;
					addnews($session['user']['name'].'`2 brachte seinem Guru voll Hingabe ein Menschenopfer dar - leider bemerkte er zu spät, WER das Opfer sein sollte.');   
					output('`$Ungeduldig seufzend lässt du dich auf den Stein sinken, der sehr glitschig ist - und auch noch abfärbt! Du blickst der einen Gestalt ins Gesicht und beschwerst dich über die Arbeitsbedingu.. Als letzter Gedanke (kurz bevor du dich gewundert hast, seit wann dein Kopf fliegen kann) fällt dir ein, was du dich vorhin gefragt hast: Wieso dir niemand sagen konnte, ob das Opfern ein guter Job ist.. Das kostet dich Erfahrung!`n`nDeine Gilde erhält dafür '.$points.' Gildenpunkte.');
					addnav('Ich fühl mich so tot..','news.php');
					$guild['points'] += $points;
										
				}
				else {
					output('`$Mit stolzgeschwellter Brust schreitest du durch einen nicht enden wollenden, abschüssigen Gang auf ein helles Licht zu. Hier werden Opferungen vorgenommen, hat man dir erzählt. Und deine Gilde bekommt dafür wertvolle Macht! 
							Wieso also solltest du nicht auch deinen Teil beitragen und beim Opfern helfen? Während du durch das Portal trittst und den blutverschmierten Opferstein betrachtest, spukt dir ein bestimmter Gedanke durch den Kopf. Leider ist dieser immer noch
							sehr in Mitleidenschaft gezogen.. Verdammte Zauberkräuter. Was dir dein Guru da aufgeschwatzt hat, kann ja gar nicht gesund sein. Egal. Die Stimme, die da schreit, sie kommt dir bekannt vor.. irgendwoher. Und wie der schreit.. als würde er geopfert, und nicht irgendein Schaf! Oder so, du hast nicht wirklich
							eine Ahnung, was Opfern angeht. Aber das kann ja noch werden. Diese Schreie.. wird ja immer schlimmer. Irgendwas müssen die falsch machen. Schluss. Aus. Du zeigst es ihnen jetzt, wie das geht!');
				
					if($session['user']['level'] == 1 && $session['user']['level'] == 15) {
						output('`nKomisch.. in deiner Magengegend meldet sich ein Gefühl.. es will dir so etwas sagen, wie.. dein Level.. passt.. *grummel*... nicht zum Rest..');
					}
					else {	
						output('`nLeicht schwankend kommst du vor einem Stein zu stehen. Der Stein hat eine Färbung, rot, so ähnlich wie Blut. Daneben stehen zwei bullige Typen, gekleidet in Nachthemden. Die haben die gleiche Farbe wie der Stein. Und was dir auch schon wieder absolut unverständlich ist:
						Was soll dieses Grinsen? Du hast ihnen schließlich sogar das Angebot gemacht, beim Opfern zu helfen. Während der eine unkontrolliert in sein Hemd prustet, fordert dich der andere auf, doch bitte auf dem Stein Platz zu nehmen, das Opfer komme gleich.');
						$link = 'dg_main.php?op=in&subop=buildings&building_op=opfer&act=ok';
						addnav('',$link);
						output('`n`n<a href="'.$link.'">Ja, ich will warten!</a>',true);
					}
									
					addnav('Zur Halle','dg_main.php?op=in');
				}
				
				break;	// End opfer
				
			case 'altar':
				
				// Altar lässt Gildenführer Massensegen vergeben
				$lvl = $guild['build_list'][DG_BUILD_ALTAR];
				dg_show_header('Der Altar ('.$dg_build_levels[$lvl].')');
				$points = 3;
				$impr = pow(1.2,$lvl);	// exponentielle Steigerung
				
				if($_GET['act'] == 'ok') {
					
					if($points > $guild['points']) {
						output('`#Der Altar lässt nur ein trockenes Knirschen vernehmen. Wahrscheinlich reichen die Punkte deiner Gilde nicht für ein solches Wunder!');
					}
					else {
															
						$sql = 'UPDATE accounts SET hitpoints=ROUND(hitpoints*'.$impr.'),spirits=ROUND(spirits*'.$impr.') WHERE guildid='.$gid.' AND guildfunc!='.DG_FUNC_APPLICANT.''; 
						db_query($sql);
						
						$session['user']['hitpoints'] *= $impr;
						
						dg_massmail($gid,'`@Ein Wunder!','`2Der Guru deiner Gilde hat ein gigantisches Wunder bewirkt, das zwar einige Gildenpunkte gekostet, dafür aber auch dir neue Lebenskraft gegeben hat!');
						
						//dg_addnews($guild['name'].'`2 hat sich entschlossen, einige Gildenpunkte gegen neue Kraft einzutauschen!');   
						
						output('`#Erschrocken stolperst du erstmal einige Schritte zurück. Die Kerzen flackern auf, im dichten Rauch kannst du Schemen erkennen (zumindest deine Phantasie) und ein Heulen ist zu hören, wie als würde der Wind durch Mauerritzen pfeifen.`n
								Es hat funktioniert! Du fühlst neue Lebenskraft in dir, genau wie all deine Mitstreiter.');
						
						$guild['points_spent'] += $points;
						$guild['points'] -= $points;
					}
										
				}
				else {
					output('`#Hübsch, ist dein erster Gedanke beim Anblick des Altars: Goldene und silberne Kelche, Kerzen, Geschnitzte Figuren von nackten Dämonen oder Engeln (Das kannst du nicht so genau ausmachen), in alle Richtungen drehbare Kreuze und Pentagramm-Schablonen zum Selbermalen. Eben alles, was so zu einem gescheiten Altar dazugehört!`n
							Du ahnst schon, damit lässt sich bestimmt was anfangen. Direkt daneben hängt ein vergilbtes Pergament: Lieber Kunde! Wir gratulieren Euch zum Kauf dieses einzigartigen Altars. Er eignet sich für jede Art von rituellem Massenereignis, sei es Liebeszauber oder Stinkmorchelfluch. Bitte nicht vergessen: Wir garantieren für keinerlei Funktionstüchtigkeit!`nGez. Königl. Zauberhafte Altarmanufaktur.');
				
					$link = 'dg_main.php?op=in&subop=buildings&building_op=altar&act=ok';
					addnav('',$link);
					output('`n`n<a href="'.$link.'">Lasst uns ein Lebenskraftwunder bewirken!</a> ('.$points.' Gildenpunkte)',true);
													
				}
				
				addnav('Zur Halle','dg_main.php?op=in');
				
				break;	// End altar
				
			case 'stall':
				
				$lvl = $guild['build_list'][DG_BUILD_STALL];				
				dg_show_header('Der Tierstall ('.$dg_build_levels[$lvl].')');
				
				$arr_animals = array(
					'goldschaf' => array('name'=>'`^Goldschaf`0','minlvl'=>1,'desc'=>'Das `^Goldschaf `& sucht für dich im Wald nach zusätzlichen Goldvorkommen. "Mähhh"','goldprice'=>399,'gemprice'=>0,'effectmsg'=>'`^Dein Goldschaf scharrt mit seinen Hufen gut verborgene Münzen frei!`0','wearoff'=>'`^Dein Goldschaf trabt blökend davon.`0','rounds'=>30,'goldfind'=>1.7)
					,'beutegeier' => array('name'=>'`6Beutegeier`0','minlvl'=>2,'desc'=>'Der `6Beutegeier`& rafft Beutestücke an sich. "Krrrrr"','goldprice'=>999,'gemprice'=>2,'effectmsg'=>'`6Dein Beutegeier pickt mit einem heiseren Krächzen auf deinem Gegner herum, um ihm ein Beutestück zu entlocken!`0','wearoff'=>'`6Dein Beutegeier schwingt sich schwerfällig in die Lüfte.`0','rounds'=>20,'failmsg'=>'`6Leider ist der Beutegeier erfolglos..`0')
					,'gemelster' => array('name'=>'`7Edelsteinelster`0','minlvl'=>3,'desc'=>'Die `7Edelsteinelster`& ist ein raffiniertes Biest, das im verlassenen Schloß noch den kleinsten Glitzer aufspürt.','goldprice'=>1199,'gemprice'=>2,'effectmsg'=>'`7Deine Edelsteinelster hüpft mit einem Glitzern in den Augen herum und hält Ausschau nach Gemmensteinen!`0','wearoff'=>'`7Deine Edelsteinelster flattert davon.`0','rounds'=>10,'failmsg'=>'`6Leider ist die Elster erfolglos..`0')					
					);
				
				// Tier mitnehmen
				if($_GET['act'] == 'get') {
					
					$str_animal = $_GET['animal'];
					$ok = true;
					
					output('`&Neugierig zeigst du auf die Stalltür, hinter der sich '.$arr_animals[$str_animal]['name'].'`& verbirgt!');
					
					foreach($arr_animals as $animal => $a) {
						if($session['bufflist'][$animal]) {
							output('`n`$'.$a['name'].'`$ hinter dir würde sich wohl kaum mit '.$arr_animals[$str_animal]['name'].'`$ vertragen!');
							$ok = false;
						}
					}
					
					if($session['user']['gold'] < $arr_animals[$str_animal]['goldprice'] || $session['user']['gems'] < $arr_animals[$str_animal]['gemprice']) {
						output('`n`$Beschämt musst du feststellen, dass deine Besitztümer nicht ausreichen, um das Futter für '.$arr_animals[$str_animal]['name'].'`$ bezahlen zu können!');
						$ok = false;
					}
										
					if($ok) {
						output(' Kurz darauf führst du deinen Begleiter an einer langen, reißsicheren Leine nach draußen.');
						
						$session['user']['gold'] -= $arr_animals[$str_animal]['goldprice'];
						$session['user']['gems'] -= $arr_animals[$str_animal]['gemprice'];
						
						$guild['building_vars']['stall'][$str_animal] = $session['user']['acctid'];					
						
						$session['bufflist'][$str_animal] = $arr_animals[$str_animal];
					}
						
					
				}
				else if($_GET['act'] == 'drop') {	// Tier abgeben
					
					$str_animal = $_GET['animal'];
					
					output('`&Widerwillig lässt du '.$arr_animals[$str_animal]['name'].'`& im Stall zurück.');
					
					unset($session['bufflist'][$str_animal]);  
					$guild['building_vars']['stall'][$str_animal] = 0;					
					
				}
				else {	// Startbildschirm
					
					output('`5Dies ist der Stall deiner Gilde, eine Bretterbude, der Boden ist mit festgestampftem 
							Stroh bedeckt, in abgetrennten Verschlägen schnauben die Tiere.');
					output('`nHier hast du die Möglichkeit, seltene Exemplare der Tierwelt mit dir zu nehmen!');
	
				
					foreach($arr_animals as $str_animal => $arr_info) {
						
						$usedby = '';
						
						if($session['bufflist'][$str_animal]) {
							addnav(''.$arr_info['name'].' zurückbringen!`0','dg_main.php?op=in&subop=buildings&building_op=stall&act=drop&animal='.$str_animal);
						}					
						
						if($lvl >= $arr_info['minlvl']) {
							
							output('`n`n`&'.$arr_info['desc'].'`&');
													
							$str_usedby = '';
													
							if($guild['building_vars']['stall'][$str_animal] > 0) {
							
								$sql = 'SELECT bufflist,name,loggedin,activated,laston,acctid FROM accounts WHERE acctid='.$guild['building_vars']['stall'][$str_animal];
								$user = db_fetch_assoc(db_query($sql));
								
								$user['bufflist'] = unserialize($user['bufflist']);
																						
								if(isset($user['bufflist'][$str_animal])) {
								
									$online = user_get_online(0,$user);
									
									if($online) {
									
										$usedby = $user['name'];
										
									}
									else {
										
										unset($user['bufflist'][$str_animal]);
										$sql = 'UPDATE accounts SET bufflist="'.addslashes(serialize($user['bufflist'])).'" WHERE acctid='.$user['acctid'];
										db_query($sql);
										
									}	
									
								}
								else {	// Buff bereits abgelaufen
									
									$guild['building_vars']['stall'][$str_animal] = 0;
									
								}
								
							}	// END Tier in use
							
							if($session['bufflist'][$str_animal]) {
								output('`n`&Du selbst hast '.$arr_info['name'].'`& noch bei dir.');
							}
							else if($usedby != '') {
								output('`&Doch leider ist '.$arr_info['name'].'`& gerade mit '.$usedby.'`& unterwegs! Du wirst wohl noch warten müssen.`n');
							}
							else {
								$link = 'dg_main.php?op=in&subop=buildings&building_op=stall&act=get&animal='.$str_animal;
								addnav('',$link);
								output('`n'.$arr_info['name'].'`& <a href="'.$link.'">mitnehmen</a> ('.$arr_info['goldprice'].' Gold'.($arr_info['gemprice']>0 ? ' '.$arr_info['gemprice'].' Edelsteine' : '').') !`n',true);							
							}
							
						}
					}	// END foreach
														
				}	// END wenn keine aktion
				
				addnav('Zurück zur Halle','dg_main.php?op=in');
				
				break;	// END stall

			// END AUSBAUTEN
		
	}	// END switch building_op

?>
