<?php
// Name: tempel.php
// Autor: tcb / Talion f�r http://lotgd.drachenserver.de (mail: t@ssilo.de)
// Erstellungsdatum: 5.5.05 - 17.5.05
// Erfordert Mods in Dateien: gardens.php, rock.php, beggar.php, dorfamt.php, bio.php, newday.php, configure.php
// Beschreibung: 
//		F�hrt neuen Beruf Priester ein, zur Speicherung wird Var profession (Wertebereich von 11-13) genutzt. 
//		Priester k�nnen verheiraten, scheiden, Fl�che aufheben, Kopfgeldtr�ger verfluchen, bekommen Bonus auf mystische K�nste
//		Tempel-Location im Garten: Bettelstein hierherverlegt, Erl�sung von Kopfgeld gegen Gems m�glich, Heiratslocation
//		Neues Heiratssytem: 
//			- Bei >= 5 Flirts im Garten Verlobung
//			- Priester muss Heirat starten (Vorsicht: Darf nicht gleichzeitig einer der zu Verheiratenden sein)
//			- Priester schlie�t Heirat ab, Weiteres gleichbleibend
//			Statusvar: 1 = im Gange, 2 = verheiratet, 3 = abgeschlossen
// �nderungen:
// 		

require_once "common.php";
require_once(LIB_PATH.'board.lib.php');

page_header("Der Tempel");

/*$races_chance = array(RACE_WERWOLF => 70, RACE_VAMPIR => 40, RACE_DAEMON => 10);

$chance = 100;
if( isset($races_chance[$session['user']['race']]) && $session['user']['superuser'] == 0 ) { $chance = $races_chance[$session['user']['race']]; }
output($ret_page);
if($chance < 100 && $_GET['op'] == '' && strpos($ret_page,'tempel') === false ) {
	output('`gAls du dich der Pforte des heiligen Tempels n�herst, sp�rst du ein ungutes Ziehen in der linken unteren Bauchgegend.`n
			Du r�tselst, was das sein k�nnte. Hast du etwa verdorbenes Elfenfleisch gegessen? Oder gar zu viel Blut getrunken? Nein? Hm..
			Da! Die Antwort schie�t dir durch den Kopf:`n
			DU bist ja ein '.$colraces[$session['user']['race']].'`g! Und deine Rasse verhindert dir wohl den Zutritt zu diesem Tempel der guten G�ttter. Mist..`n
			Oder willst du es trotzdem versuchen?! Vielleicht hast du ja Gl�ck..
		'); 
	addnav('Einen Versuch ist es wert!','tempel.php?op=try_enter');
	addnav('Den G�ttern ist nich zu trauen..','gardens.php');
	page_footer();
	exit;
}

if($_GET['op'] == 'try_enter') {

	output('`gForschen Schrittes st��t du das Portal auf und trittst in den Tempel des Guten! Ungl�ubig siehst du dich um. Sch�n hier, denkst du dir.  
		'); 

	if( e_rand(1,100) > $chance ) {
	
		output('<h2>WHAM!</h2>
				Ein `bBlitz`b muss deinen Sch�del in zwei H�lften gespalten haben, oder war es eher ein �berdimensionaler `bHammer`b?`n
				Das einzige was feststeht: ES war definitv verdammt `bt�dlich`b.`n
				Du verlierst 10% deiner Erfahrung, bekommst allerdings 5 Gefallen bei Ramius f�r deine wagemutige Aktion!
				');
		$session['user']['hitpoints'] = 0;
		$session['user']['experience'] *= 0.9;
		$session['user']['deathpower'] += 5;
		
		addnews($session['user']['name'].'`g wurde bei seinem Versuch, den Tempel des Guten zu sch�nden, von g�ttlichem Unmut getroffen.');
		
		addnav('Ramius, mein Freund!','shades.php');	
		
	}
	else {
	
		addnav('Weiter gehts.','tempel.php');
	
	}
		
	page_footer();
	exit;
}*/

addcommentary();
checkday();

define("SCHNELLHOCHZ_KOSTEN",3000);
define("SCHNELLHOCHZ_ERLAUBT",0);
define("STATUS_START",1);
define("STATUS_VERHEIRATET",2);
define("STATUS_ABGESCHLOSSEN",3);
define("TEMPLE_SERVANT_TURNS",2);
define("TEMPLE_SERVANT_MINDAYS",10);
define("TEMPLE_SERVANT_MAX",5);


function show_rules () {
	
	output("`4I. `&Die Priesterkaste und das Amt des Priesters ist in Ehren zu halten. Keinesfalls darf irgendeine Aktion ergriffen werden, die die unbefleckte Ehre der Priester beschmutzen w�rde!`n");
	output("`4II. `&Den Anweisungen des Hohepriesters ist Folge zu leisten. Er repr�sentiert die oberste Autorit�t des Priesterstands!`n");
	output("`4III. `&Alle Gesetze dieses Dorfes gelten in besonderem Ma�e f�r Priester!`n`0");
	output("`4IV. `&Wer einen Priester bei einem Einbruch angreift und t�tet, muss damit rechnen, f�r einige Tage verflucht zu werden!`n`0");
	output("`4V. `&Priester d�rfen hilflosen Schutzsuchenden und Personen, die durch besonderen Edelmut hervorragen, einen Segen erteilen!`n`0");
	output("`4VI. `&Auf der anderen Seite ist es ihnen erlaubt, r�cksichtslose und blinde Barbarei mit Fl�chen zu ahnden!`n`0");
	output("`4VII. `&Niemals jedoch sollen Priester ihre pers�nlichen Angelegenheiten mit ihrer Berufung mischen!`n`0");
	
}

function show_priest_list ($admin_mode=0) {
	
	$sql = "SELECT a.name,a.profession,a.acctid,a.login,a.loggedin,a.activated,a.laston FROM accounts a 
			WHERE a.profession=".PROF_PRIEST_HEAD." OR a.profession=".PROF_PRIEST;
	$sql .= ($admin_mode>=1) ? " OR a.profession=".PROF_PRIEST_NEW : "";
	$sql .= " ORDER BY profession DESC, name";
	
	$res = db_query($sql) or die (db_error(LINK));
	
	if(db_num_rows($res) == 0) {
		output("`n`iEs gibt keine Priester/innen!`i`n");
	}
	else {	
	
		output('<table border="0" cellpadding="5" cellspacing="2" bgcolor="#999999"><tr class="trhead"><td>Nr.</td><td>Name</td><td>Funktion</td><td>Status</td></tr>',true);
		
		for($i=1; $i<=db_num_rows($res); $i++) {
			
			$p = db_fetch_assoc($res);
			
			$link = "bio.php?char=".rawurlencode($p['login']) . "&ret=".URLEncode($_SERVER['REQUEST_URI']);
			addnav("",$link);
			
			output('<tr class="'.($i%2?'trlight':'trdark').'"><td>'.$i.'</td><td><a href="mail.php?op=write&to='.rawurlencode($p['login']).'" target="_blank" onClick="'.popup("mail.php?op=write&to=".rawurlencode($p['login']) ).';return false;"><img src="images/newscroll.GIF" width="16" height="16" alt="Mail schreiben" border="0"></a><a href="'.$link.'">'.$p['name'].'</a></td><td>`7',true);
			
			switch( $p['profession'] ) {
			
				case PROF_PRIEST_HEAD:
					output('`bHohepriester/in`b');
					if($admin_mode>=4) {
						output('`n<a href="tempel.php?op=entlassen&id='.$p['acctid'].'">Entlassen</a>',true);
						addnav("","tempel.php?op=entlassen&id=".$p['acctid']);
						
						output('`n<a href="tempel.php?op=hohep_deg&id='.$p['acctid'].'">Degradieren</a>',true);
						addnav("","tempel.php?op=hohep_deg&id=".$p['acctid']);
					}		
					break;
					
				case PROF_PRIEST:
					output('Priester/in');		
					if($admin_mode>=3) {
						output('`n<a href="tempel.php?op=entlassen&id='.$p['acctid'].'">Entlassen</a>',true);
						addnav("","tempel.php?op=entlassen&id=".$p['acctid']);
						
						if($admin_mode>=4) {						
							output('`n<a href="tempel.php?op=hohep&id='.$p['acctid'].'">Zum Hohepriester machen</a>',true);
							addnav("","tempel.php?op=hohep&id=".$p['acctid']);
						}
					}
					break;
					
				case PROF_PRIEST_NEW:	
					output('Novize/in');		
					if($admin_mode>=3) {
						output('`n<a href="tempel.php?op=aufnehmen&id='.$p['acctid'].'">Aufnehmen</a>',true);
						addnav("","tempel.php?op=aufnehmen&id=".$p['acctid']);
						
						output('`n<a href="tempel.php?op=ablehnen&id='.$p['acctid'].'">Ablehnen</a>',true);
						addnav("","tempel.php?op=ablehnen&id=".$p['acctid']);
						
						if($admin_mode>=4) {												
							output('`n<a href="tempel.php?op=hohep&id='.$p['acctid'].'">Zum Hohepriester machen</a>',true);
							addnav("","tempel.php?op=hohep&id=".$p['acctid']);
						}
					}
					break;
					
				default:
					break;
			}
						
			output('</td><td>'.(user_get_online(0,$p)?'`@online`&':'`4offline`&').'</td></tr>',true);
			
		}	// END for
		
		output('</table>',true);
		
	}	// END priester vorhanden
	
}	// END show_priest_list

function show_servant_list ($admin_mode=0) {
		
	$sql = "SELECT a.name,a.profession,a.acctid,a.login,a.loggedin,i.daysinjail,i.temple_servant FROM accounts a 
				LEFT JOIN account_extra_info i ON i.acctid=a.acctid
				WHERE a.profession=".PROF_TEMPLE_SERVANT;
	$sql .= " ORDER BY profession DESC, name";
	$res = db_query($sql) or die (db_error(LINK));
	
	if(db_num_rows($res) == 0) {
		output("`n`iEs gibt keine Tempeldiener!`i`n");
	}
	else {	
	
		output('<table border="0" cellpadding="5" cellspacing="2" bgcolor="#999999"><tr class="trhead"><td>Nr.</td><td>Name</td><td>H�ftlingstage</td><td>Arbeitstage bisher</td><td>Status</td>'.($admin_mode ? '<td>Aktionen</td>' : '').'</tr>',true);
		
		for($i=1; $i<=db_num_rows($res); $i++) {
			
			$p = db_fetch_assoc($res);
			
			$p['temple_servant'] = ($p['temple_servant'] >= 20 ? $p['temple_servant']*0.05 : $p['temple_servant']);
							
			$link = "bio.php?char=".rawurlencode($p['login']) . "&ret=".URLEncode($_SERVER['REQUEST_URI']);
			addnav("",$link);
			
			output('<tr class="'.($i%2?'trlight':'trdark').'"><td>'.$i.'</td><td><a href="mail.php?op=write&to='.rawurlencode($p['login']).'" target="_blank" onClick="'.popup("mail.php?op=write&to=".rawurlencode($p['login']) ).';return false;"><img src="images/newscroll.GIF" width="16" height="16" alt="Mail schreiben" border="0"></a><a href="'.$link.'">'.$p['name'].'</a></td>',true);
			output('<td>'.$p['daysinjail'].'</td><td>'.$p['temple_servant'].'</td>',true);
			output('<td>'.(($p['loggedin'])?'`@online`&':'`4offline`&').'</td>',true);
												
			if($admin_mode) {
				output('<td><a href="tempel.php?op=servant_stop&id='.$p['acctid'].'">Entlassen</a></td>',true);
				addnav("","tempel.php?op=servant_stop&id=".$p['acctid']);
			}
												
			output('</tr>',true);
			
		}	// END for
		
		output('</table>',true);
		
	}	// END Diener vorhanden

}

function show_flirt_list ($admin_mode=0,$married=0) {
		
	$link = calcreturnpath();
	$link .= "&";
	
	$ppp = 30;
			
	$count_sql = "SELECT COUNT(*) AS anzahl FROM accounts a WHERE ";	
		
	if($married < 2) {
	
		$sql = "SELECT a.name AS name_a,a.acctid AS acctid_a,b.name AS name_b,b.acctid AS acctid_b, a.login AS login_a, b.login AS login_b FROM accounts a,accounts b
					WHERE
					a.marriedto=b.acctid AND
					a.sex=1 AND b.sex=0 AND ";
		if($married) {
			$sql .= "( a.charisma = 4294967295 AND b.charisma = 4294967295 )";	
			$count_sql .= "a.charisma=4294967295 AND a.marriedto>0 AND a.marriedto<4294967295";	
		}
		else {
			$sql .= "( a.charisma = 999 AND b.charisma = 999 )";
			$count_sql .= "a.charisma=999 AND a.marriedto>0 AND a.marriedto<4294967295";	
		}
		
		$sql .= "ORDER BY name_a, name_b";
		
	}
	else {
		$sql = "SELECT a.sex,a.name AS name_a,a.acctid AS acctid_a, a.login AS login_a FROM accounts a
					WHERE a.marriedto=4294967295 ";
		$sql .= "ORDER BY name_a";
		$count_sql .= "a.marriedto=4294967295";	
	}
	
	$count_res = db_query($count_sql) or die (db_error(LINK));
	$c = db_fetch_assoc($count_res);
				
	if($c['anzahl'] == 0) {
		output("`iEs gibt keine Paare!`i");
	}
	else {
	
		// wegen Paaren	
		if($married < 2) {$c['anzahl'] = floor($c['anzahl'] * 0.5);}
	
		$page = max((int)$_GET['page'],1);
								
		$last_page = ceil($c['anzahl'] / $ppp);
		
		for($i=1; $i<=$last_page; $i++) {
			
			$offs_max = min($i * $ppp,$c['anzahl']);
			$offs_min = ($i-1) * $ppp + 1;
				
			addnav("Seite ".$i." (".$offs_min." - ".$offs_max.")",$link."page=".$i);
			
		}
		
		$offs_min = ($page-1) * $ppp;
		
		$sql .= " LIMIT ".$offs_min.",".$ppp;
	
		$res = db_query($sql) or die (db_error(LINK));
	
		output('<table border="0" cellpadding="3"><tr class="trhead"><td>Nr.</td>',true);
		if($married < 2) {
			output('<td><img src="images/female.gif" alt="weiblich"> Name</td><td><img src="images/male.gif" alt="m�nnlich"> Name</td>',true);
		}
		else {
			output('<td> Spieler</td><td> NPC</td>',true);
		}	
		output( (($admin_mode)?'<td>Aktionen</td>':'').'</tr>',true);
		
		while($p = db_fetch_assoc($res)) {
			
			$offs_min++;
									
			$link_a = "bio.php?char=".rawurlencode($p['login_a']) . "&ret=".URLEncode($_SERVER['REQUEST_URI']);
			addnav("",$link_a);
			$link_b = "bio.php?char=".rawurlencode($p['login_b']) . "&ret=".URLEncode($_SERVER['REQUEST_URI']);
			addnav("",$link_b);
			$mail_a = ($admin_mode>=2) ? '<a href="mail.php?op=write&to='.rawurlencode($p['login_a']).'" target="_blank" onClick="'.popup("mail.php?op=write&to=".rawurlencode($p['login_a']) ).';return false;"><img src="images/newscroll.GIF" width="16" height="16" alt="Mail schreiben" border="0"></a>' : '';
			$mail_b = ($admin_mode>=2) ? '<a href="mail.php?op=write&to='.rawurlencode($p['login_b']).'" target="_blank" onClick="'.popup("mail.php?op=write&to=".rawurlencode($p['login_b']) ).';return false;"><img src="images/newscroll.GIF" width="16" height="16" alt="Mail schreiben" border="0"></a>' : '';
							
			output('<tr class="'.(($offs_min%2)?'trdark':'trlight').'"><td>'.$offs_min.'</td>',true);
			output('<td>'.$mail_a.'<a href="'.$link_a.'">'.$p['name_a'].'</a></td>',true);
			if($married < 2) {output('<td>'.$mail_b.'<a href="'.$link_b.'">'.$p['name_b'].'</a></td>',true);}
			else {output('<td>'.(($p['sex']==0)?'Violet':'Seth').'</td>',true);}
			
			if($admin_mode>=2) {
				output('<td>',true);
				if(!$married) {
					if(getsetting("temple_status",0) == 0 || getsetting("temple_status",0) == STATUS_ABGESCHLOSSEN) {
						output('<a href="tempel.php?op=hochz&id1='.$p['acctid_a'].'&id2='.$p['acctid_b'].'">Hochzeit beginnen</a>',true);
						addnav("","tempel.php?op=hochz&id1=".$p['acctid_a']."&id2=".$p['acctid_b']);
						output('`n<a href="tempel.php?op=trennung&id1='.$p['acctid_a'].'&id2='.$p['acctid_b'].'">Verlobung l�sen</a>',true);
						addnav("","tempel.php?op=trennung&id1=".$p['acctid_a']."&id2=".$p['acctid_b']);
					}
					elseif(getsetting("temple_id1",0) == $p['acctid_a'] || getsetting("temple_id2",0) == $p['acctid_b']) {
						output('`iHochzeit im Gange`i',true);
					}

				}
				else {
					if($married==2) {
						output('<a href="tempel.php?op=scheidung&id1='.$p['acctid_a'].'&npc=1">Trennen</a>',true);
						addnav("","tempel.php?op=scheidung&id1=".$p['acctid_a']."&npc=1");
					}
					else {
						output('<a href="tempel.php?op=scheidung&id1='.$p['acctid_a'].'&id2='.$p['acctid_b'].'">Trennen</a>',true);
						addnav("","tempel.php?op=scheidung&id1=".$p['acctid_a']."&id2=".$p['acctid_b']);
					}
					
				}
				output('</td>',true);
			}		
														
			output('</tr>',true);
			
		}	// END for
		
		output('</table>',true);
		
	}	// END paare vorhanden
	
}	// END show_flirt_list

function make_temple_commentary ($msg,$author=0) {
	
	$sql = "INSERT INTO commentary SET section='temple',author=".$author.",comment='".addslashes($msg)."',postdate=NOW()";
	db_query($sql) or die (db_error(LINK));	
	
}	// END make_temple_commentary

$op = (isset($_GET['op'])) ? $_GET['op'] : '';
$priest = 0;
if(su_check(SU_RIGHT_DEBUG)) {$priest = 4;}
elseif($session['user']['profession'] == PROF_PRIEST_NEW) {$priest = 1;}
elseif($session['user']['profession'] == PROF_PRIEST) {$priest = 2;}
elseif($session['user']['profession'] == PROF_PRIEST_HEAD) {$priest = 3;}

switch ($op) {

	case '':
		
		$show_invent = true;
				
		output("Ehrfurchtsvoll betrittst du den Tempel. Hoch �ber Dir spannt sich das kuppelf�rmige Dach wie ein Zelt �ber die weite, an der Frontseite in einen Rundbogen �bergehende Tempelhalle.`n");
		output("Durch hohe, schmale Rundbogenfenster an den Seitenw�nden f�llt etwas Tageslicht in den Raum. Darunter verl�uft ein quadratischer S�ulengang, hinter dem eine Pforte ins Allerheiligste f�hrt.`n");
		output("Den vorderen Teil dominiert ein erh�ht stehender, marmorner Tisch, verziert mit vielerlei magischen Symbolen. Dies scheint der Altar zu sein.`n");
		output("Auf der rechten Seite, hinter den S�ulen verborgen, erblickst Du einen mit Gold �berh�uften Stein. Nicht weit davon entfernt einen kleineren Altar, der f�r Opfer gedacht zu sein scheint.`n`n");
		
		if(getsetting("temple_status",0) > 0) {
		
			$sql = "SELECT name,acctid FROM accounts  
					WHERE acctid=".getsetting('temple_id1',0)." OR acctid=".getsetting('temple_id2',0)." ORDER BY sex";
			$res = db_query($sql);
			$p1 = db_fetch_assoc($res);
			$p2 = db_fetch_assoc($res);
		
			if(getsetting("temple_status",0) == STATUS_START) {
				output("`c`i`&Heute wird hier das wundersch�ne Fest der Hochzeit von ".$p1['name']."`& und ".$p2['name']."`& begangen!"); 
			}
			elseif(getsetting("temple_status",0) == STATUS_VERHEIRATET || getsetting("temple_status",0) == STATUS_ABGESCHLOSSEN) {
				output("`c`i`&".$p1['name']."`& und ".$p2['name']."`& haben gerade geheiratet! Herzlichen Gl�ckwunsch!"); 
			}
			output("`i`c`n`n");
		}

		viewcommentary("temple","Leise sprechen:",25,"raunt");
		
		if($priest >= 2) {
			addnav("Priester");
			addnav("Zum Allerheiligsten","tempel.php?op=secret");

			if(getsetting('temple_priest_id',0) == $session['user']['acctid']) {
				addnav("Aktionen");
				
				if(getsetting('temple_status',0) == STATUS_START) {
					addnav("`bVerheiraten`b","tempel.php?op=hochz_ok&heirat=1");
					}
				elseif(getsetting('temple_status',0) == STATUS_VERHEIRATET) {
					addnav("`bZeremonie abschlie�en`b","tempel.php?op=hochz_ende");
					}
				elseif(getsetting('temple_status',0) == STATUS_ABGESCHLOSSEN) {
				//	addnav("`bAufr�umen`b","tempel.php?op=sauber");
					}	
				
			}
			
		}
				
		addnav("Tempel");
		addnav("Opfern","tempel.php?op=opfer");
		addnav("Liste der Priester","tempel.php?op=priest_list");
		addnav("Liste der Diener","tempel.php?op=servant_list&public=1");
		addnav("Ehepaare","tempel.php?op=married_list_public");
		addnav("Der Bettelstein","beggar.php");
		addnav("Schwarzes Brett","tempel.php?op=board");
		if($session['user']['charisma']==999 && SCHNELLHOCHZ_ERLAUBT) {addnav("Schnellhochzeit (".SCHNELLHOCHZ_KOSTEN." Gold)","tempel.php?op=hochz_schnell");} 
		
		addnav("Erl�sung von S�nden");
		if($session['user']['profession'] == 0) {addnav('Als Tempeldiener anfangen!','tempel.php?op=servant_apply');}
		else if($session['user']['profession'] == PROF_TEMPLE_SERVANT) {
			addnav('Tempel fegen','tempel.php?op=serve');
			addnav('Priestern die Schuhe k�ssen','tempel.php?op=serve&what=kiss');
		}
		addnav('Kopfgeld','tempel.php?op=bounty_del');
		
		addnav("Verschiedenes");
		addnav("Zur�ck in den Garten","gardens.php");
		addnav("Zur�ck ins Dorf","village.php");
		
		break;
	
	case 'serve':
		
		$sql = 'SELECT temple_servant,daysinjail FROM account_extra_info WHERE acctid='.$session['user']['acctid'];
		$res = db_query($sql);
		$info = db_fetch_assoc($res);
				
		output('`gEifrig machst du dich auf, deinen Pflichten als Tempeldiener nachzukommen.');
		
		if($session['user']['turns'] < TEMPLE_SERVANT_TURNS) {
			output('`nDoch leider bist du schon zu ersch�pft daf�r!');
		}
		else if($info['temple_servant'] >= 20) {
			output('`nDoch dann denkst du dir, dass du heute schon genug geschuftet hast und kehrst wieder um.');
		}
		else {
			$session['user']['turns'] -= TEMPLE_SERVANT_TURNS;		
			$info['temple_servant'] *= 20; // harte Arbeit markieren
						
			if($_GET['what'] == 'kiss') {
				
				$sql = 'SELECT name,acctid,sex FROM accounts WHERE profession='.PROF_PRIEST.' OR profession='.PROF_PRIEST_HEAD.' ORDER BY RAND() LIMIT 1';
				$res = db_query($sql);
					
				if(db_num_rows($res)) {
					$acc = db_fetch_assoc($res);
					
					output('`nEilfertig l�sst du dich auf die Knie herab und beginnst, die Schuhe von Priester'.($acc['sex'] ? 'in':'').' '.$acc['name'].'`g auf Hochglanz zu bringen! ');
					
					if(e_rand(1,3) == 1) {
						output( ($acc['sex'] ? 'Sie':'Er').' ist mit Sicherheit zufrieden und gew�hrt dir zus�tzliche Erl�sung..');
						if(e_rand(1,2) == 1) {
							systemmail($acc['acctid'],'`VGute Arbeit des Tempeldieners!',$session['user']['name'].'`V hat deine Schuhe wirklich perfekt sauber gel.. geputzt! Ausgezeichnete Arbeit!');					
						}
						$lose = 2;
					}
					else {
						output( ($acc['sex'] ? 'Sie':'Er').' scheint allerdings etwas unzufrieden mit deiner Putzleistung zu sein.. das musst du noch �ben!');
						$lose = 1;
					}
																	
				}
												
			}
			else {	// Kehren
				output('`nNach Stunden m�hsamer Arbeit ist alles blitzblank. Die Priester werden sicher zufrieden sein!`n');		
				$lose = 1;
			}
			
			$info['daysinjail']-=$lose;
			
			$sql = 'UPDATE account_extra_info SET daysinjail='.$info['daysinjail'].',temple_servant='.$info['temple_servant'].' WHERE acctid='.$session['user']['acctid'];
			db_query($sql);
			
			output('`nDu verlierst '.TEMPLE_SERVANT_TURNS.' Waldk�mpfe und dein Strafregister vermindert sich um '.$lose.' Tag'.($lose > 1 ? 'e' : '').'! Es verbleiben '.($info['daysinjail']).' Tage. Noch genug zu tun..');
		}
		
		addnav('Zur�ck zum Tempel','tempel.php');
				
		break;
		
	case 'servant_apply':
		
		$sql = 'SELECT temple_servant,daysinjail FROM account_extra_info WHERE acctid='.$session['user']['acctid'];
		$res = db_query($sql);
		$info = db_fetch_assoc($res);
		
		$allowed = true;
		
		if($info['temple_servant'] > 0) {
			
			output('`gDie Priester wollen dich nicht schon wieder im Tempel sehen! Sie erkl�ren dir, dass
					du noch mindestens '.$info['temple_servant'].' Sonnenuml�ufe auf eine neuerliche Gelegenheit
					warten musst.');			
			$allowed = false;
			
		}
		
		if($session['user']['profession'] != 0) {
			$allowed = false;
		}		
		
		if($info['daysinjail'] < TEMPLE_SERVANT_MINDAYS) {
			$allowed = false;
			output('`gDeine S�nden sind wohl nicht ausreichend.. auf jeden Fall weigern sich die Priester hartn�ckig, dich als Tempeldiener anzunehmen!');
		}		
			
		if($allowed) {
			
			$sql = 'SELECT acctid FROM accounts WHERE profession='.PROF_TEMPLE_SERVANT;
			$res = db_query($sql);
				
			if(db_num_rows($res) > TEMPLE_SERVANT_MAX) {
				$allowed = false;	
				output('`gLeider, so erf�hrst du, gibt es bereits zu viele Tempeldiener. Versuch es sp�ter noch einmal!');
			}
				
		}
		
		if($allowed) {
		
			output('`gDie Priester begr��en dich als neuen Tempeldiener und �berreichen dir dein Gewand, das du die n�chsten Tage bei deiner harten Arbeit tragen wirst. Nicht sehr eindrucksvoll, sicher, aber nur so vergeben dir die G�tter einen Teil deiner S�nden..`nEs versteht sich wohl von selbst, dass du als Tempeldiener keinerlei Straftaten begehen darfst!');
		
			$session['user']['profession'] = PROF_TEMPLE_SERVANT;
			addnews($session['user']['name'].'`8 wird nun einige Zeit als Tempeldiener ehrliche Arbeit leisten.');
			$sql = 'UPDATE account_extra_info SET temple_servant=1 WHERE acctid='.$session['user']['acctid'];
			db_query($sql);
		}
		
		addnav('Zum Tempel','tempel.php');
				
		break;
		
	case 'servant_stop':
		
		$sql = 'SELECT name FROM accounts WHERE acctid='.(int)$_GET['id'];
		$acc = db_fetch_assoc(db_query($sql));
		
		$sql = 'UPDATE accounts SET profession = 0 WHERE acctid='.(int)$_GET['id'];
		db_query($sql);
		
		$sql = 'UPDATE account_extra_info SET temple_servant = 20 WHERE acctid='.(int)$_GET['id'];
		db_query($sql);
		
		systemmail($_GET['id'],'`4Entlassung!',$session['user']['name'].'`4 hat dich aus deinem Amt als Tempeldiener entlassen!');
		
		$sql = 'INSERT INTO news SET newstext = "'.addslashes($acc['name']).'`8s Zeit als Tempeldiener ist Vergangenheit.",newsdate=NOW(),accountid='.$_GET['id'];
		db_query($sql) or die (db_error(LINK));
				
		redirect('tempel.php?op=servant_list');				
		break;
		
	case 'servant_list':
		
		if(!$_GET['public'] && $priest) {
			show_servant_list(true);
			addnav('Zur�ck zum Allerheiligsten','tempel.php?op=secret');
		}
		else {
			show_servant_list();
		}
		
		addnav('Zur�ck zum Tempel','tempel.php');
					
		break;
		
	case 'secret':
		output("Du schl�pfst durch die versteckte Pforte in den prachtvollen, heiligsten Bereich des Tempels. Nur Priester haben hier Zutritt.`n`n");
		viewcommentary("temple_secret","Sprechen:",25,"spricht");
		
		addnav("Registratur");
		
		addnav("Liste der Priester","tempel.php?op=priest_list_admin");
		addnav("Liste der Verlobten","tempel.php?op=flirt_list");
		addnav("Liste der Verheirateten","tempel.php?op=married_list");
		addnav("Liste der Seth / Violetopfer","tempel.php?op=married_list_npc");
		addnav("Liste der Tempeldiener","tempel.php?op=servant_list");
		addnav("Zum schwarzen Brett","tempel.php?op=board");
		addnav("Die goldenen Regeln der Priester","tempel.php?op=rules");
		
		addnav("Aktionen");
		
		addnav("Fl�che / Segen","tempel.php?op=fluch_liste_auswahl");
		addnav("Verfluchen / Segnen","tempel.php?op=fluch");
		if(getsetting("temple_status",0) == 0) {addnav("Aufr�umen","tempel.php?op=sauber");}
		
		if($session['user']['profession'] == 11) {addnav("K�ndigen","tempel.php?op=aufh");}
		
		//if(getsetting("temple_spenden",0) >= 50) {addnav("Wunder wirken!","tempel.php?op=wunder");}
		
		addnav("Verschiedenes");
		
		addnav("Zur�ck zum Vorraum","tempel.php");
		addnav("Zur�ck ins Dorf","village.php");
		break;
	
	case 'rules':
		output("F�r die Ewigkeit bestimmt sind hier die Regeln der Priester festgehalten:`n`n");
		show_rules();
		
		addnav("Zur�ck","tempel.php?op=secret");
		break;
	
	case 'priest_list':
		output("In Stein gemei�elt erkennst Du eine Liste aller Priester/innen:`n`n");
		show_priest_list();
		
		if($session['user']['profession'] == 0) {addnav("Ich will Priester/in werden!","tempel.php?op=bewerben");}
		if($session['user']['profession'] == PROF_PRIEST_NEW) {addnav("Bewerbung zur�ckziehen","tempel.php?op=bewerben_abbr");}
		addnav("Zur�ck","tempel.php");
		break;
		
	case 'priest_list_admin':
		output("Auf einer Schriftrolle befindet sich eine Liste aller Priester/innen:`n`n");
		show_priest_list($priest);
		addnav("Zur�ck","tempel.php?op=secret");
		break;
			
	case 'bewerben':
		
		$sql = "SELECT COUNT(*) AS anzahl FROM accounts WHERE (profession=".PROF_PRIEST." OR profession=".PROF_PRIEST_HEAD.")";
		$res = db_query($sql);
		$p = db_fetch_assoc($res);
				
		if($session['user']['dragonkills'] < 15) {
			output("Du musst mindestens 15mal den gr�nen Drachen get�tet haben, um Priester werden zu k�nnen!");
			addnav("Zur�ck","tempel.php?op=priest_list");
		}
		elseif($p['anzahl'] >= getsetting("numberofpriests",3)) {
			output("Es gibt bereits ".$p['anzahl']." Priester. Mehr werden zur Zeit nicht ben�tigt!");
			addnav("Zur�ck","tempel.php?op=priest_list");
		}
		else {	
			output("Nach reiflicher �berlegung beschlie�t Du, das Amt des Priesters anzustreben. Weiterhin gelten f�r den Priesterstand die folgenden, unverletzbaren Regeln:`n`n");
			show_rules();
			output("`nAls Priester w�rst Du daran unbedingt gebunden!`nSteht Dein Entschluss immer noch fest?");
			addnav("Ja!","tempel.php?op=bewerben_ok&id=".$session['user']['acctid']);
			addnav("Nein, zur�ck!","tempel.php?op=priest_list");
		}
		break;
	
	case 'bewerben_ok':
		$session['user']['profession'] = PROF_PRIEST_NEW;
		
		$sql = "SELECT acctid FROM accounts WHERE profession=".PROF_PRIEST_HEAD." ORDER BY loggedin DESC, RAND() LIMIT 1";
		$res = db_query($sql);
		if(db_num_rows($res)) {
			$p=db_fetch_assoc($res);
			systemmail($p['acctid'],"`&Neue Bewerbung!`0","`&".$session['user']['name']."`& hat sich f�r den Posten des Priesters beworben. Du solltest seine Bewerbung �berpr�fen und ihn gegegebenfalls einstellen.");
			}
		
		output("Du reichst deine Bewerbung bei den Priestern ein, die diese gewissenhaft pr�fen und Dir dann Bescheid geben werden!`n");
		addnav("Zur�ck","tempel.php?op=priest_list");
		break;
		
	case 'bewerben_abbr':
		$session['user']['profession'] = 0;
						
		output("Du hast deine Bewerbung erfolgreich zur�ckgenommen!`n");
		addnav("Zur�ck","tempel.php?op=priest_list");
		break;
		
	case 'aufh':
		$session['user']['profession'] = 0;
		
		$sql = "SELECT acctid FROM accounts WHERE profession=".PROF_PRIEST_HEAD." ORDER BY loggedin DESC,RAND() LIMIT 1";
		$res = db_query($sql);
		if(db_num_rows($res)) {
			$p=db_fetch_assoc($res);
			systemmail($p['acctid'],"`&K�ndigung!`0","`&".$session['user']['name']."`& hat beschlossen, fortan kein Priester mehr zu sein.");
			}
		
		addnews($session['user']['name']." `&ist seit dem heutigen Tage kein".($session['user']['sex'] ? 'e Priesterin':' Priester')." mehr!");
		
		addhistory('`2Aufgabe des Priesteramts');
		
		output("Etwas wehm�tig legst Du die Insignien ab und bist ab sofort wieder ein normaler B�rger!`n");
		addnav("Zum Tempel","tempel.php");
		addnav("Zum Dorf","village.php");
		break;
	
	case 'entlassen':
		output("Diesen Priester wirklich entlassen?`n");
		addnav("Ja!","tempel.php?op=entlassen_ok&id=".$_GET['id']);
		addnav("Nein, zur�ck!","tempel.php?op=priest_list_admin");
		break;
		
	case 'entlassen_ok':
		$pid = (int)$_GET['id'];
	
		// F�r Debugzwecke
		if($session['user']['acctid'] == $pid) {$session['user']['profession'] = 0;}
	
		$sql = "UPDATE accounts SET profession = 0  
				WHERE acctid=".$pid;
		db_query($sql) or die (db_error(LINK));
		
		systemmail($pid,"Du wurdest entlassen!",$session['user']['name']."`& hat Dich aus dem Priesterstand entlassen.");
		
		$sql = "SELECT name FROM accounts WHERE acctid=".$pid;
		$res = db_query($sql);
		$p = db_fetch_assoc($res);
		
		$sql = "INSERT INTO news SET newstext = '".addslashes($p['name'])." `&wurde heute aus der ehrenvollen Gemeinschaft der Priester entlassen!',newsdate=NOW(),accountid=".$pid;
		db_query($sql) or die (db_error(LINK));
		
		addhistory('`$Entlassung aus dem Priesteramt',1,$pid);
		
		output("Priester wurde entlassen!`n");
		addnav("Zur�ck","tempel.php?op=priest_list_admin");
		break;
				
	case 'aufnehmen':
		$pid = (int)$_GET['id'];
		
		$sql = "SELECT COUNT(*) AS anzahl FROM accounts WHERE (profession=".PROF_PRIEST." OR profession=".PROF_PRIEST_HEAD.")";
		$res = db_query($sql);
		$p = db_fetch_assoc($res);
		
		if($p['anzahl'] >= getsetting("numberofpriests",3)) {
			output("Es gibt bereits ".$p['anzahl']." Priester! Mehr sind zur Zeit nicht m�glich.");
			addnav("Zur�ck","tempel.php?op=priest_list_admin");
		}
		else {
		
			// F�r Debugzwecke
			if($session['user']['acctid'] == $pid) {$session['user']['profession'] = 11;}
		
			$sql = "UPDATE accounts SET profession = ".PROF_PRIEST."  
					WHERE acctid=".$pid;
			db_query($sql) or die (db_error(LINK));
			
			$sql = "SELECT name FROM accounts WHERE acctid=".$pid;
			$res = db_query($sql);
			$p = db_fetch_assoc($res);
			
			systemmail($pid,"Du wurdest aufgenommen!",$session['user']['name']."`& hat deine Bewerbung zur Aufnahme in die Priesterkaste angenommen. Damit bist du vom heutigen Tage an offiziell Mitglied dieser ehrenwerten Kaste!");
			
			$sql = "INSERT INTO news SET newstext = '".addslashes($p['name'])." `&wurde heute offiziell in die ehrenvolle Gemeinschaft der Priester aufgenommen!',newsdate=NOW(),accountid=".$pid;
			db_query($sql) or die (db_error(LINK));
			
			addhistory('`2Aufnahme ins Priesteramt',1,$pid);
			
			addnav("Willkommen!","tempel.php?op=priest_list_admin");
			
			output("Der neue Priester ist jetzt aufgenommen!");
		}
		break;
		
	case 'ablehnen':
		$pid = (int)$_GET['id'];
	
		// F�r Debugzwecke
		if($session['user']['acctid'] == $pid) {$session['user']['profession'] = 0;}
	
		$sql = "UPDATE accounts SET profession = 0  
				WHERE acctid=".$pid;
		db_query($sql) or die (db_error(LINK));
		
		systemmail($pid,"Deine Bewerbung wurde abgelehnt!",$session['user']['name']."`& hat Deine Bewerbung zur Aufnahme in die Priesterkaste abgelehnt.");
		
		addnav("Zur�ck","tempel.php?op=priest_list_admin");
		break;
		
	case 'hohep':
		$pid = (int)$_GET['id'];
	
		// F�r Debugzwecke
		if($session['user']['acctid'] == $pid) {$session['user']['profession'] = 12;}
	
		$sql = "UPDATE accounts SET profession = ".PROF_PRIEST_HEAD."  
				WHERE acctid=".$pid;
		db_query($sql) or die (db_error(LINK));
		
		systemmail($pid,"Du wurdest bef�rdert!",$session['user']['name']."`& hat Dich zum Hohepriester ernannt.");
		
		addnav("Hallo Chef!","tempel.php?op=priest_list_admin");
		break;
		
	case 'hohep_deg':
		$pid = (int)$_GET['id'];
	
		// F�r Debugzwecke
		if($session['user']['acctid'] == $pid) {$session['user']['profession'] = PROF_PRIEST;}
	
		$sql = "UPDATE accounts SET profession = ".PROF_PRIEST."  
				WHERE acctid=".$pid;
		db_query($sql) or die (db_error(LINK));
		
		systemmail($pid,"Du wurdest degradiert!",$session['user']['name']."`& hat Dir die Hohepriesterw�rden entzogen.");
		
		addnav("Das wars dann!","tempel.php?op=priest_list_admin");
		break;
	
	case 'sauber':
		savesetting("temple_id1","0");	
		savesetting("temple_id2","0");
		savesetting("temple_status","0");
		savesetting("temple_priest_name"," ");
		savesetting("temple_priest_id","0");
				
		// Sicherung
		$sql = "UPDATE commentary SET section='temple_s' WHERE section='temple'";
		db_query($sql);
		// Sicherung Ende
		
		redirect("tempel.php");
		break;
		
	case 'hochz':
		
		if(getsetting("temple_status",0) != 0 && getsetting("temple_status",0) != STATUS_ABGESCHLOSSEN) {
			output("Gerade jetzt findet eine Hochzeit statt! Du willst doch da nicht st�ren?");
			addnav("Zur�ck","tempel.php?op=married_list_admin");
		}
		else {
					
			if(!$_GET['start']) {
				
				if($_GET['id1'] && $_GET['id2']) {
					savesetting("temple_id1",(int)$_GET['id1']);	// Partner 1
					savesetting("temple_id2",(int)$_GET['id2']);	// Partner 2
				}
				
				$name = ($_POST['name']) ? $_POST['name'] : $session['user']['name'];
				
				$name = stripslashes($name);
				$name = preg_replace("/`[^123456789!@#\$%^&QqRrVvGgTt]/","",$name);
				$name = str_replace('\'','',$name);
				$name = str_replace('"','',$name);
				
				output("Du hast die Gelegenheit, f�r diesen feierlichen Anlass Deinen Namen samt Titel zu �ndern!`n");
				output("Dein bisheriger Name lautet ".$session['user']['name'].", daraus wird: ".HTMLEntities($name)."`n",true);
				output('<form method="POST" action="tempel.php?op=hochz&start=1"><input type="hidden" name="name" value="',true);
				rawoutput($name);
				output('"><input type="submit" name="ok" value="Hochzeit starten!"></form>',true);
				addnav("","tempel.php?op=hochz&start=1");
				
				output("`n`0Anderer Name:");
				
				output('<form method="POST" action="tempel.php?op=hochz">`n',true);
				output('<input type="text" name="name" value="',true);
				rawoutput($name);
				output('"> <input type="submit" name="ok" value="Vorschau"></form>',true);
				addnav("","tempel.php?op=hochz");
			}
			else {
				$name = stripslashes($_POST['name']);
				$name = preg_replace("/`[^123456789!@#\$%^&QqRrVvGgTt]/","",$name);
				$name = str_replace('\'','',$name);
				$name = str_replace('"','',$name);
								
				savesetting("temple_status",1);	// Status
				savesetting("temple_priest_name",$session['user']['name']);
				savesetting("temple_priest_id",$session['user']['acctid']);
							
				$session['user']['name'] = $name;
				
				output("Du wirst als ".$session['user']['name']."`0 die Zeremonie leiten!");
				
				make_temple_commentary(": `ger�ffnet die Zeremonie!",$session['user']['acctid']);
								
				addnav("Los gehts!","tempel.php");
			}
		}
		
		break;
				
	case 'hochz_ok':
		
		if(getsetting('temple_id1',0) == getsetting('temple_priest_id',0) || getsetting('temple_id1',0) == getsetting('temple_priest_id',0)) {
		
			output("Du kannst dich nicht selbst verheiraten! Frage einen anderen Priester, ob er das f�r Dich �bernimmt.");
				
		}
		else {
			
//			hochz(getsetting('temple_id1',0),getsetting('temple_id2',0),true);
									
			$sql = "SELECT name,acctid,guildid,guildfunc FROM accounts  
					WHERE acctid=".getsetting('temple_id1',0)." OR acctid=".getsetting('temple_id2',0)." ORDER BY sex";
			$res = db_query($sql);
			$p1 = db_fetch_assoc($res);
			$p2 = db_fetch_assoc($res);
			
			// Hier evtl. LOCK TABLE...
						
			$sql = "UPDATE accounts SET charisma = 4294967295, charm=charm+1, donation=donation+1, gems=gems+1
					WHERE acctid=".getsetting('temple_id1',0)." OR acctid=".getsetting('temple_id2',0);
			db_query($sql) or die (db_error(LINK));
			
			$sql = "INSERT INTO news SET newstext = '`%".$p1['name']." `&und `%".$p2['name']."`& haben heute feierlich den Bund der Ehe geschlossen!!!',newsdate=NOW(),accountid=".$p1['acctid'];
			db_query($sql) or die (db_error(LINK));
			
			systemmail($p1['acctid'],"`&Verheiratet!`0","`& Du und `&".$p2['name']."`& habt im Rahmen einer feierlichen und wundersch�nen Zeremonie im Tempel geheiratet!`nGl�ckwunsch!`nAls Geschenk erh�lt jeder von euch einen Edelstein.");
			systemmail($p2['acctid'],"`&Verheiratet!`0","`& Du und `&".$p1['name']."`& habt im Rahmen einer feierlichen und wundersch�nen Zeremonie im Tempel geheiratet!`nGl�ckwunsch!`nAls Geschenk erh�lt jeder von euch einen Edelstein.");
			
			addhistory('`vHeirat mit '.$p1['name'],1,$p2['acctid']);
			addhistory('`vHeirat mit '.$p2['name'],1,$p1['acctid']);
									
			savesetting("temple_status",2);	// Status
			make_temple_commentary(": `gerkl�rt ".$p1['name']."`g und ".$p2['name']."`g offiziell zu Mann und Frau!",$session['user']['acctid']);
			
			// Gildensystem
			require_once(LIB_PATH.'dg_funcs.lib.php');
			$state = 0;
			if( ($p1['guildid']  && $p1['guildfunc'] != DG_FUNC_APPLICANT) ) {
				$guild1 = &dg_load_guild($p1['guildid'],array('treaties','points'));
			}
			if( ($p2['guildid']  && $p2['guildfunc'] != DG_FUNC_APPLICANT) ) {
				$guild2 = &dg_load_guild($p2['guildid'],array('treaties','points'));
			}
			if($guild1 && $guild2) {$state = dg_get_treaty($guild2['treaties'][$p1['guildid']]);}
			
			$points = ($state == 1 ? $dg_points['wedding_friendly'] : ($state == 0 ? $dg_points['wedding_neutral'] : 0) );
			
			if($guild1) {$guild1['points'] += $points;}
			if($guild2) {$guild2['points'] += $points;}
			
			dg_save_guild();
			// END Gildensystem			
			
			
		}
		
		redirect('tempel.php');
		break;
		
	case 'hochz_ende':
		
		make_temple_commentary(": `gschlie�t die Zeremonie ab.",$session['user']['acctid']);
		
		if(getsetting("temple_priest_id",0) == $session['user']['acctid'] && getsetting("temple_priest_name",'') != '') {$session['user']['name'] = getsetting("temple_priest_name",$session['user']['login']);}
		
		savesetting("temple_priest_name"," ");
		savesetting("temple_status",3);	
		savesetting("temple_priest_id","0");	// Status
			
		redirect('tempel.php');
		break;
	
	case 'hochz_schnell':
		
		if($session['user']['gold'] < SCHNELLHOCHZ_KOSTEN) {
		
			output("Du verf�gst leider nicht �ber genug Gold, weswegen die Priester deinen Antrag zur�ckweisen!");
			
		}
		else {
									
			output("Willst Du wirklich diesen Schritt gehen? Bedenke auch, dass eine Schnellhochzeit nicht die Vorteile einer priesterlichen Zeremonie bietet!");				
			addnav("Ja, ich will!","tempel.php?op=hochz_schnell_ok");
		}
		
		addnav("Zum Tempel","tempel.php");
		
		break;
		
	case 'hochz_schnell_ok':
		
		$session['user']['gold'] -= SCHNELLHOCHZ_KOSTEN;
			
		$sql = "SELECT name,acctid FROM accounts  
				WHERE acctid=".$session['user']['marriedto'];
		$res = db_query($sql);
		$p = db_fetch_assoc($res);
			
		$sql = "UPDATE accounts SET charisma = 4294967295 
					WHERE acctid=".$p['acctid'];
		db_query($sql) or die (db_error(LINK));
		$session['user']['charisma'] = 4294967295;
					
		addnews("`%".$session['user']['name']." `&und `%".$p['name']."`& haben heute mehr oder weniger feierlich den Bund der Ehe geschlossen!!!");
					
		systemmail($session['user']['acctid'],"`&Verheiratet!`0","`& Du und `&".$p['name']."`& habt im Rahmen einer eiligen, kleinen Feier geheiratet!`nGl�ckwunsch!");
		systemmail($p['acctid'],"`&Verheiratet!`0","`& Du und `&".$session['user']['name']."`& habt im Rahmen einer eiligen, kleinen Feier geheiratet!`nGl�ckwunsch!");
		
		output("Du hast ".$p['name']."`0 geheiratet. Herzlichen Gl�ckwunsch! Auch wenn die Zeremonie etwas lieblos war..");				
		
		addnav("Zum Tempel","tempel.php");
		addnav("Zum Dorf","village.php");
		
		break;
	
	case 'scheidung':
		
		if(!$_GET['npc']) {
		
			$id1 = (int)$_GET['id1'];
			$id2 = (int)$_GET['id2'];
			
			$sql = "SELECT name,acctid FROM accounts  
					WHERE acctid=".$id1." OR acctid=".$id2." ORDER BY sex";
			$res = db_query($sql);
			$p1 = db_fetch_assoc($res);
			$p2 = db_fetch_assoc($res);
			
			// Hier evtl. LOCK TABLE...
						
			$sql = "UPDATE accounts SET charisma = 0, marriedto=0
					WHERE acctid=".$id1." OR acctid=".$id2;
			db_query($sql) or die (db_error(LINK));
			
			$sql = "INSERT INTO news SET newstext = '`%".$p1['name']." `&und `%".$p2['name']."`& haben sich heute getrennt und ihre Ehe f�r nichtig erkl�rt!', newsdate=NOW(),accountid=".$p1['acctid'];
			db_query($sql) or die (db_error(LINK));
			
			addhistory('`tScheidung von '.$p1['name'],1,$p2['acctid']);
			addhistory('`tScheidung von '.$p2['name'],1,$p1['acctid']);
			
			systemmail($p1['acctid'],"`&Scheidung!`0","`& Du und `&".$p2['name']."`& habt Euch getrennt und Eure Ehe anulliert!");
			systemmail($p2['acctid'],"`&Scheidung!`0","`& Du und `&".$p1['name']."`& habt Euch getrennt und Eure Ehe anulliert!");
			
			make_temple_commentary(": `gerkl�rt ".$p1['name']."`g und ".$p2['name']."`g als geschieden!",$session['user']['acctid']);			
		}
		else {
			
			$id = (int)$_GET['id1'];
		
			$sql = "SELECT name,acctid,sex FROM accounts  
					WHERE acctid=".$id;
			$res = db_query($sql);
			$p = db_fetch_assoc($res);
												
			$sql = "UPDATE accounts SET charisma = 0, marriedto=0
					WHERE acctid=".$id;
			db_query($sql) or die (db_error(LINK));
			
			$npc_name = (($p['sex']==0)?"Violet":"Seth");
			
			$sql = "INSERT INTO news SET newstext = '`%".$p['name']." `&und `%".$npc_name."`& haben sich heute getrennt und ihre Ehe f�r nichtig erkl�rt!', newsdate=NOW(),accountid=".$p['acctid'];
			db_query($sql) or die (db_error(LINK));
								
			systemmail($p['acctid'],"`&Scheidung!`0","`& Du und `&".$npc_name."`& habt Euch getrennt und Eure Ehe anulliert!");
			make_temple_commentary(": `gerkl�rt ".$p['name']."`g und ".$npc_name."`g als geschieden!",$session['user']['acctid']);
			
		}
						
		output("Erfolgreich geschieden!");
						
		addnav("Zur�ck","tempel.php?op=secret");
		
		break;
		
	case 'trennung':
					
		$id1 = (int)$_GET['id1'];
		$id2 = (int)$_GET['id2'];
			
		$sql = "SELECT name,acctid FROM accounts  
				WHERE acctid=".$id1." OR acctid=".$id2." ORDER BY sex";
		$res = db_query($sql);
		$p1 = db_fetch_assoc($res);
		$p2 = db_fetch_assoc($res);
									
		$sql = "UPDATE accounts SET charisma = 0, marriedto=0
				WHERE acctid=".$id1." OR acctid=".$id2;
		db_query($sql) or die (db_error(LINK));
		
		//$sql = "INSERT INTO news SET newstext = '`%".$p1['name']." `&und `%".$p2['name']."`& haben sich heute getrennt und ihre Ehe f�r nichtig erkl�rt!', newsdate=NOW(),accountid=".$p1['acctid'];
		//db_query($sql) or die (db_error(LINK));
		
		systemmail($p1['acctid'],"`&Trennung!`0","`& Du und `&".$p2['name']."`& habt Euch getrennt und Eure Verlobung anulliert!");
		systemmail($p2['acctid'],"`&Trennung!`0","`& Du und `&".$p1['name']."`& habt Euch getrennt und Eure Verlobung anulliert!");
		
		make_temple_commentary(": `gerkl�rt ".$p1['name']."`gs und ".$p2['name']."`gs Verlobung als aufgel�st!",$session['user']['acctid']);			
						
		output("Verlobung gel�st!");
						
		addnav("Zur�ck","tempel.php?op=secret");
		
		break;
			
	case 'flirt_list':
		show_flirt_list($priest);
		
		addnav("Zur�ck","tempel.php?op=secret");
		break;
		
	case 'married_list':
		show_flirt_list($priest,1);
		
		addnav("Zur�ck","tempel.php?op=secret");
		break;
		
	case 'married_list_npc':
		show_flirt_list($priest,2);
		
		addnav("Zur�ck","tempel.php?op=secret");
		break;
		
	case 'married_list_public':
		show_flirt_list(0,1);
		
		addnav("Zur�ck","tempel.php");
		break;
	
	case 'opfer':
				
		output("Hier kannst Du in Meditation versinken, die G�tter um ein Geschenk bitten und daf�r ein Opfer bringen. Sie werden Dir entweder permanente Lebenskraft, Edelsteine oder Gold abnehmen - je nachdem, wonach ihnen der Sinn steht.`nWie viele Runden willst Du meditieren?");
		
		addnav("Wie lange?");
		if($session['user']['turns'] >= 2) addnav("... 2 Runden","tempel.php?op=opfer_ok&runden=2");
		if($session['user']['turns'] >= 5) addnav("... 5 Runden","tempel.php?op=opfer_ok&runden=5");
		if($session['user']['turns'] >= 10) addnav("... 10 Runden","tempel.php?op=opfer_ok&runden=10");
		
		addnav("Weg hier!");
		
		addnav("... Zur�ck!","tempel.php");
				
		break;
		
	case 'opfer_ok':
						
		$runden = $_GET['runden'];
		$glueck = e_rand ( 0, ( 20 - $runden ) );
		if($glueck == 0) { $glueck = 2; }
		elseif($glueck > 0 && $glueck < 10) {$glueck = 1;}
		else {$glueck = 0.1;}
		$was = e_rand(1,7);
		$menge = e_rand(1,10);
		$msg = "";
		$val1 = 0;
		$val_gold = 0;
		
		$session['user']['turns'] -= $runden;
		
		output("Du atmest ruhig ein und aus, ein und aus... f�hlst Deine Entspannung wachsen. Schlie�lich bist Du den G�ttern ganz nah und bietest Ihnen ein Opfer. Sie nehmen Dir..."); 
		
		switch($was) {
			
			case 1:
				$menge = ceil($menge * 0.5);
				
				if( ($session['user']['maxhitpoints']-$menge) > $session['user']['level'] * 10 ) {
					
					$session['user']['maxhitpoints'] -= $menge;
					debuglog("Opferte ".$menge." LP im Tempel!");
					
					$val1 = ceil($runden * $menge * 0.4 * e_rand(1,2) * $glueck);					
					$val1 = min($val1,min($session['user']['level']+10,20));
					$val_gold = $val1 * 200;					
										
					$item = array('tpl_name'=>"G�ttliche R�stung",'tpl_description'=>"Eine R�stung mit ".$val1." Verteidigung, die Du von den G�ttern als Dank f�r dein Opfer erhalten hast.",'tpl_value1'=>$val1,'tpl_gold'=>$val_gold);
					
					item_add($session['user']['acctid'],'rstdummy',true,$item);
					
					$msg = "`^".$menge."`0 permanente Lebenskraft.`nVor deinen F��en liegt nun eine neue, schimmernde R�stung mit ".$val1." Verteidigung!";
					
				}
				else {
					$msg = "`^".$menge."`0 permanente Lebenskraft, die Du leider nicht hast! Unbefriedigt erhebst Du Dich.";
					$menge = 0;
				}
							
				break;
				
			case 2:
			case 3:
				
				if( $menge <= $session['user']['gems'] ) {
					
					$session['user']['gems'] -= $menge;
					debuglog("Opferte ".$menge." Edels im Tempel!");
										
					$val1 = ceil($runden * $menge * 0.2 * e_rand(1,2) * $glueck);					
					$val1 = min($val1, min($session['user']['level']+10,20) );
					$val_gold = $val1 * 200;					
										
					$item = array('tpl_name'=>"G�ttliche Waffe",'tpl_description'=>"Eine Waffe mit ".$val1." Angriff, die Du von den G�ttern als Dank f�r dein Opfer erhalten hast.",'tpl_value1'=>$val1,'tpl_gold'=>$val_gold);
					
					item_add($session['user']['acctid'],'waffedummy',true,$item);
					
					$msg = "`^".$menge."`0 Edelsteine!`nVor deinen F��en liegt eine neue, gl�nzende Waffe mit ".$val1." Angriff!";
					
				}
				else {
					$msg = "`^".$menge."`0 Edelsteine, die Du leider nicht hast! Unbefriedigt erhebst Du Dich.";
					$menge = 0;
				}
			
				
				break;
				
			case 4:
			case 5:
										
				$menge *= 500;
				
				if( $menge <= $session['user']['gold'] ) {
					
					$session['user']['gold'] -= $menge;
														
					$val1 = ceil($runden * $menge * 0.001 * e_rand(1,3) * $glueck) * 0.01;
					$val1 = min(max($val1,1.1),1.6);
					$val_gold = floor($val1 * 1500);					
					
					$item = array('tpl_value1'=>$val1,'tpl_gold'=>$val_gold);
					
					item_add($session['user']['acctid'],'gtlschtzzbr',true,$item);
														
					$msg = "`^".$menge."`0 Gold!`nVor deinen F��en liegt ein seltener Zauberspruch!";
					
				}
				else {
					$msg = "`^".$menge."`0 Gold, das Du leider nicht hast! Unbefriedigt erhebst Du Dich.";
					$menge = 0;
				}
								
				break;
				
			case 6:	
			case 7:
				$msg = "gar nichts. Sie halten Dich f�r \"zu gierig\". Was immer das hei�en mag.";
				$menge = 0;
				break;			
		
		}
		
		if($menge > 0) {
					
			if($glueck < 1) { $msg.= "`nHeute ist wohl nicht dein Gl�ckstag.. Die G�tter scheinen von Deiner Ernsthaftigkeit nicht �berzeugt gewesen zu sein!`n";	}
			elseif($glueck > 1) { $msg.= "`nDu musst der Liebling der G�tter sein!`n";	}
		}
		
		output($msg);
		
		if($session['user']['turns'] >= 2) {addnav("Nochmal meditieren","tempel.php?op=opfer");}				
		addnav("Zum Tempel","tempel.php");
		
		break;
	
	case "wunder":
		output("");	
		
		addnav("Alle von den Toten erwecken!","tempel.php?op=wunder_ok&wunder=auferstehung");
		addnav("Sofortiges Dorffest!","tempel.php?op=wunder_ok&wunder=auferstehung");
		addnav("Sehr gute Stimmung f�r alle!","tempel.php?op=wunder_ok&wunder=auferstehung");
		addnav("!","tempel.php?op=wunder_ok&wunder=auferstehung");
	
		break;
		
	case 'wunder_ok':
		
		switch($_GET['wunder']) {
			
			case '':
			
				break;
				
			default:
				break;
		
			}
		
		break;
	
	case 'fluch':
				
		output("Als Priester kannst Du allen Helden einen Fluch aufzwingen, der sie beim Kampf beeintr�chtigt. Oder einen Segen, je nachdem. Beides verschwindet von selbst nach einiger Zeit.`n`n");
						
		if(!$_POST['name']) {
			output('<form action="tempel.php?op=fluch" method="POST">',true);
			output('<input type="text" size="20" name="name">',true);
			output('<input type="submit" size="20" name="ok" value="Suchen">',true);
			output('</form>',true);
			addnav("","tempel.php?op=fluch");			
		}
		else {
				
			$ziel = stripslashes(rawurldecode($_POST['name']));

            $name="%";

            for ($x=0;$x<strlen($ziel);$x++){

                $name.=substr($ziel,$x,1)."%";

            }

            $sql = "SELECT acctid,name FROM accounts WHERE name LIKE '".addslashes($name)."' AND locked=0";
			$res = db_query($sql);
			
			if(!db_num_rows($res)) {
				output("`iKeine �bereinstimmung gefunden!`i");					
			}
			elseif(db_num_rows($res) >= 100) {
				output("`iZu viele �bereinstimmungen! Grenze deinen Suchbegriff etwas ein.`i");					
			}
			else {
				output('<form action="tempel.php?op=fluch_ok" method="POST">',true);
				output('<select name="id" size="1">',true);
				while($p = db_fetch_assoc($res)){
					output("<option value=\"".$p['acctid']."\">".preg_replace("'[`].'","",$p['name'])."</option>",true);
                }
				output('</select> `n',true);
				output('<select name="buff" size="1"><option value="f1">Fluch</option><option value="f2">Schlimmer Fluch</option><option value="s1">Segen</option></select>`n',true);
				output('<input type="submit" size="20" name="ok" value="Los!">',true);
				output('</form>',true);
				addnav("","tempel.php?op=fluch_ok");			
				
			}
								
		}
											
		addnav("Zur�ck","tempel.php?op=secret");			
				
		break;
	
	case 'fluch_ok':
		
		if($_POST['buff'] == "f1") {
		
			$name = "Fluch der Tempelpriester";
			$desc = "Die Tempelpriester haben Dich wegen eines bestimmten Grunds verflucht..";
			$buff = array("name"=>$name,"rounds"=>500,"wearoff"=>"Der Fluch l�sst nach!",
			"atkmod"=>0.8,"defmod"=>0.8,"roundmsg"=>"Der Fluch behindert dich!","activate"=>"offense,defense");
			$sql = "INSERT INTO items SET gold=5000,gems=30,name='".$name."',description='".$desc."',hvalue=4,owner=".(int)$_POST['id'].",class='Fluch',buff='".serialize($buff)."'";
			
			item_add((int)$_POST['id'],'tmplflch1');
			
			systemmail((int)$_POST['id'],"`4Verflucht!",$session['user']['name']." `4hat Dich f�r Deine Freveltaten in seiner Eigenschaft als Priester mit dem Fluch der Tempelpriester belegt!");
			output("Du begibst Dich in eine tiefe Trance. Nachdem Du eine dem Opfer �hnelnde Stoffpuppe misshandelt hast, f�hlst du die Energie des Fluches!`n`n");
		}
		
		elseif($_POST['buff'] == "f2") {
		
			$name = "Schlimmer Fluch der Tempelpriester";
			$desc = "Die Tempelpriester haben Dich wegen eines bestimmten Grunds verflucht..";
			$buff = array("name"=>$name,
			"rounds"=>500,
			"wearoff"=>"Der Fluch l�sst nach!","atkmod"=>0.5,"defmod"=>0.5,"roundmsg"=>
			"Der Fluch behindert dich!","activate"=>"offense,defense");
			$sql = "INSERT INTO items SET gold=10000,gems=40,name='".$name."',description='".$desc."',hvalue=4,owner=".(int)$_POST['id'].",class='Fluch',buff='".serialize($buff)."'";
			
			item_add((int)$_POST['id'],'tmplflch2');
			
			systemmail((int)$_POST['id'],"`4Verflucht!",$session['user']['name']." `4hat Dich f�r Deine Freveltaten in seiner Eigenschaft als Priester mit dem schlimmen Fluch der Tempelpriester belegt!");
			output("Du begibst Dich in eine tiefe Trance. Nachdem Du eine dem Opfer �hnelnde Stoffpuppe misshandelt hast, f�hlst du die Energie des Fluches!`n`n");
		}
		
		elseif($_POST['buff'] == "s1") {
		
			$name = "Segen der Tempelpriester";
			$desc = "Die Tempelpriester gew�hren Dir diesen Segen..";
			$buff = array("name"=>$name,"rounds"=>120,"wearoff"=>"Der Segen l�sst nach!","atkmod"=>1.15,"defmod"=>1.15,"roundmsg"=>"Der Segen st�rkt Dich!","activate"=>"offense,defense");
			$sql = "INSERT INTO items SET name='".$name."',description='".$desc."',owner=".(int)$_POST['id'].",class='Geschenk',hvalue=4,buff='".serialize($buff)."'";
			
			item_add((int)$_POST['id'],'tmplsgn');
			
			systemmail((int)$_POST['id'],"`@Gesegnet!",$session['user']['name']." `@hat Dich in seiner Eigenschaft als Priester mit einem g�ttlichen Segen bedacht!");
			output("Du begibst Dich in eine tiefe Trance. Nachdem Du eine der Person �hnelnde Stoffpuppe gestreichelt hast, f�hlst du die Energie des Segens!`n`n");
		}
		
		addnav("Zur�ck","tempel.php?op=secret");
	
		break;
		
	case 'fluch_liste_auswahl':
		
		$sql = "SELECT a.name, a.acctid FROM items i
				INNER JOIN accounts a ON a.acctid = i.owner
				LEFT JOIN items_tpl it ON it.tpl_id=i.tpl_id 	
				WHERE (it.curse>0 OR i.tpl_id='tmplflch1' OR i.tpl_id='tmplflch2' OR i.tpl_id='tmplsgn') 
				GROUP BY i.owner ORDER BY a.name";
		
		$res = db_query($sql);
		
		output("Du schaust in den magischen Spiegel und erkennst auf einer langen Liste s�mtliche Helden, denen Fl�che oder Segen anh�ngen:`n`n");
		
		if(db_num_rows($res) == 0) {
			output("`iEs gibt keine Verfluchten oder Gesegneten!`i");
		}
		else {	
		
			output('<table border="0"  cellpadding="3"><tr class="trhead"><td>Nr.</td><td>Name</td><td>Aktionen</td></tr>',true);
			
			for($i=1; $i<=db_num_rows($res); $i++) {
				
				$p = db_fetch_assoc($res);
								
				output('<tr class="'.($i%2?'trlight':'trdark').'"><td>'.$i.'</td><td>'.$p['name'].'</td><td><a href="tempel.php?op=fluch_liste&id='.$p['acctid'].'">Erscheinungen anzeigen</a></td>',true);
																						
				output('</tr>',true);
				
				addnav("","tempel.php?op=fluch_liste&id=".$p['acctid']);
				
			}	// END for
			
			output('</table>',true);
			
		}	// END fl�che vorhanden
				
		output('',true);
		
		addnav("Zur�ck","tempel.php?op=secret");			
				
		break;
	
	case 'fluch_liste':
		
		$sql = "SELECT a.name, a.acctid, i.id, i.name AS fluchname, i.hvalue FROM items i
				INNER JOIN accounts a ON i.owner = a.acctid
				LEFT JOIN items_tpl it ON it.tpl_id=i.tpl_id 	
				WHERE (it.curse>0 OR i.tpl_id='tmplflch1' OR i.tpl_id='tmplflch2' OR i.tpl_id='tmplsgn') AND i.owner=".(int)$_GET['id']." ORDER BY i.name";
				
		$res = db_query($sql);
		
		output("Bald darauf werden diese Fl�che und Segen sichtbar:`n`n");
						
		output('<table border="0" cellpadding="3"><tr class="trhead"><td>Nr.</td><td>Name</td><td>Tage verbleibend</td><td>Aktionen</td></tr>',true);
			
		for($i=1; $i<=db_num_rows($res); $i++) {
				
			$p = db_fetch_assoc($res);
							
			output('<tr class="'.($i%2?'trlight':'trdark').'"><td>'.$i.'</td><td>'.$p['fluchname'].'</td><td>'.(($p['hvalue'] == 0) ? 'unbegrenzt':$p['hvalue']).'</td><td><a href="tempel.php?op=fluch_del&id='.$p['id'].'">Aufheben</a></td>',true);
																			
			output('</tr>',true);
			
			addnav("","tempel.php?op=fluch_del&id=".$p['id']);
			
		}	// END for
		
		output('</table>',true);
		
		addnav("Zur�ck","tempel.php?op=fluch_liste_auswahl");			
				
		break;
		
	case 'fluch_del':
		
		$sql = "SELECT i.name,i.id,i.owner FROM items i WHERE i.id=".(int)$_GET['id'];
		
		$i = item_get(' id='.(int)$_GET['id'],false);
				
		$sql = "DELETE FROM items WHERE id=".$i['id'];
		
		item_delete(' id='.(int)$_GET['id']);
						
		output("Du konzentrierst Dich auf den Fluch oder Segen und sp�rst bereits nach kurzer Zeit, wie er schw�cher und schw�cher wird. Schlie�lich wei�t Du:`nEr ist Vergangenheit!");
		
		if($i['tpl_id'] == "tmplsgn") {
			systemmail($i['owner'],"Segen aufgehoben!",$session['user']['name']." `@hat in seiner Eigenschaft als Priester den Segen von Dir genommen.");
		}
		else {
			systemmail($i['owner'],"Fluch aufgehoben!",$session['user']['name']." `@hat Dich in seiner Eigenschaft als Priester von Deinem schrecklichen Fluch \"".$i['name']."\" befreit.");
		}
		
		addnav("Zur�ck","tempel.php?op=fluch_liste_auswahl");			
						
		break;
	
	case 'bounty_del':
		
		$gemcount = floor($session['user']['bounty'] * 0.001) * $session['user']['level'];
		$gemcount = min( max($gemcount, 3) , 50);
				
		if($_GET['act'] == 1) {
		
			if($session['user']['gems'] < $gemcount) {
				output("Leider hast Du nicht so viele Edelsteine.");
			}
			else {
			
				$session['user']['gems'] -= $gemcount;
				
				if(e_rand(1,2)==1) {
					
					output("Die G�tter erlassen Dir Deine S�nden (Kopfgeld verfallen)!");
					$session['user']['bounty'] = 0;
					
				}
				else {
					
					output("Die G�tter gew�hren dir keine Entlastung!");
					
				}
				
			}
		}
		
		else {
		
			if($session['user']['bounty'] == 0) {
				output("Auf Dich ist kein Kopfgeld ausgesetzt. Was willst Du also hier?");
			}
			else {
				output("Willst du f�r `^".$gemcount." `&Edelsteine um Erl�sung von deinen S�nden (Kopfgeld in H�he von `^".$session['user']['bounty']."`& Gold) bitten? Wisse jedoch, dass auf die G�tter kein Verlass ist..");
				addnav("Ja!","tempel.php?op=bounty_del&act=1");
			}
		}
		
		addnav("Zum Tempel","tempel.php");
		
		break;
		
	case 'board':
		
		output("`&Neugierig betrachtest Du die Wand neben der Pforte n�her. Du erkennst Pergamente, die �ber bald anstehende Hochzeiten informieren.`n`n");
		
		board_view('tempel',($priest>=2)?2:0,'An der Wand sind folgende Nachrichten zu lesen:','Es scheinen keine Nachrichten vorhanden zu sein.');		
		
		output("`n`n");
		
		if($priest >= 2) {
			
			board_view_form("Aufh�ngen","`&Hier kannst Du als Priester eine Nachricht hinterlassen:");
			if($_GET['board_action'] == "add") {
				board_add('tempel');
				redirect("tempel.php?op=board");
			}		
		}
		
		addnav("Zur�ck","tempel.php");
		
		break;
	
	default:
		output("Hier d�rfte ich gar nicht sein.. op:".$op.",is_priest:".$priest);
		addnav("Zur�ck ins Dorf","village.php");
		break;
		
	}

page_footer();

// END tempel.php
?>