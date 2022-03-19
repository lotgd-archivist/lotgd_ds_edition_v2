<?php
// gruft.php
// Waldereignis
// by talion (t@ssilo.de) f�r lotgd.drachenserver.de

if (!isset($session)) exit();
$session['user']['specialinc'] = 'gruft.php';

$gruft_info = unserialize($session['user']['specialmisc']);

$battle = false;

switch($_GET['op']){
	
	case 'gruft_in':
		
		if($_GET['act'] == 'enter') {
			output('`!Z�gernd entschlie�t du dich, einen Blick hineinzuwerfen. Du steigst die verwitterten Stufen herab, 
				wischt einige Spinnweben beiseite und l�sst den moosbewachsenen Eingang hinter dir.`n`n');
			
			if( sizeof($session['bufflist']['mount']) > 0 ) {
				$session['user']['buffbackup']=serialize($session['bufflist']); 
				unset($session['bufflist']['mount']);
				output('Dein Tier muss leider aufgrund der Enge drau�en warten.`n`n');	
			}
			
			
		}
				
		output('`!Nun befindest du dich in einem quadratischen, niedrigen Raum, der im Durchmesser etwa 5 Schritt gro� ist.
				An den W�nden entdeckst du Fackelhalter, die jedoch schon lange ungenutzt sind. Nur noch ein schmaler Lichtschimmer
				dringt von der hinter dir liegenden T�r�ffnung herein.`n
				Gegen�ber befindet sich eine Treppe, die in untere Sektionen zu f�hren scheint, links geht ein finsterer Gang weiter hinein.`n`n
				Wo willst du hin?');
		
		addnav('Dem Gang folgen','forest.php?op=gruft_gang');
		addnav('Treppe nach unten nehmen','forest.php?op=gruft_treppe&act=down');
		addnav('Nach drau�en','forest.php?op=gruft_out');
		
		break;
		
	case 'gruft_gang':
		
		if( sizeof($gruft_info['enemy_gang']) > 0 ) {
			output('`!Du hast dich kaum an die Dunkelheit gew�hnt, da h�rst du auch schon ein Kratzen und Rascheln, das rasch n�her kommt!
						Hungrig fauchend taucht ein kaum h�ftgro�er, allerdings mit scharfen Krallen und vier Armen bewehrter untoter Goblin vor dir 
						auf!');
			addnav('Na warte!','forest.php?op=gruft_kampf&enemy=gang' );			
		}
		else {
			output('`!Dieser Gang scheint noch enger als der Eingangsraum. Du kannst kaum aufrecht stehen, die W�nde dr�cken dir entgegen. 
					Am Ende allerdings erweitert sich der Durchgang zu einer Kammer.');
					
			addnav('Zur Kammer','forest.php?op=gruft_kammer_klein');
			addnav('Zum Eingang','forest.php?op=gruft_in');
		}
				
		break;
		
	case 'gruft_treppe':
		
		$frei = true;
		
		if(e_rand(1,2) == 1) {
		
			if( sizeof($gruft_info['enemy_treppe']) > 0 ) {
				$frei = false;
				output('`!Schritt f�r Schritt tastest du dich die glitschige Treppe '.($_GET['act'] == 'down' ? 'herab' : 'herauf').'. Da, auf einmal, st�rzt sich ein 
						monstr�ses, stinkendes und knochiges Wesen auf dich! Gerade noch rechtzeitig kannst du seinem Kreischen ausweichen,
						ehe es erneut zum Sprung ansetzt.');
				addnav('Komm nur her!','forest.php?op=gruft_kampf&enemy=treppe&act='.$_GET['act']);			
			}
			
		}
		else {
			
			if($gruft_info['treppe_falle']) {
				$frei = false;
				
				output('`!Schritt f�r Schritt tastest du dich die glitschige Treppe '.($_GET['act'] == 'down' ? 'herab' : 'herauf').'. Da, auf einmal, verl�uft ein von unangenehmem Knirschen
				begleiteter Riss durch die Decke. Sekunden sp�ter st�rzen auch schon m�chtige Steinbrocken herab und begraben dich fast!
				Als sich der Staub gelegt hat, erkennst du, dass es hier so leicht kein Durchkommen mehr gibt. Du wirst dich entweder durchgraben m�ssen,
				was mit Anstrengung und Gefahr verbunden ist, oder aber umkehren.');
				
				if($session['user']['turns'] > 0) {
					addnav('Durchgraben','forest.php?op=gruft_grab&act='.$_GET['act']);
				}
				else {
					output('`nDu bist allerdings bereits so ersch�pft, dass dir wohl nur die Umkehr bleibt!');
				}	
				
				if($_GET['act'] == 'down') {
					addnav('Umkehren','forest.php?op=gruft_in');
				}
				else {
					addnav('Umkehren','forest.php?op=gruft_kammer_gross');
				}
				
			}
			
		}
		
		if($frei) {
			
			output('`!Ohne Probleme kannst du die Treppe passieren. Unten siehst du eine gr��ere Kammer, oben den Eingangsraum.');
			
			addnav('Zur Kammer','forest.php?op=gruft_kammer_gross');
			addnav('Nach oben','forest.php?op=gruft_in');
			
		}
						
		break;
		
	case 'gruft_grab':
		
		$session['user']['turns']--;
		
		if(e_rand(1,3) == 1) {
			output('`!Du steckst gerade in der Mitte, als die Decke hinter dir v�llig in sich zusammenkracht. Zum Gl�ck
					hast du nicht lange Zeit, dir Gedanken darum zu machen, wie du wieder rauskommst. Ein gro�er Felsblock,
					der auf unangenehme Weise mit deinem Kopf kollidiert, nimmt dir diese Sorge.`n`nDu bist tot und verlierst 
					7% deiner Erfahrung sowie alles Gold, das du dabeihattest.');
					
			if($session['user']['race'] == RACE_ZWERG) {
				$session['user']['hitpoints'] = 1;	
				$session['user']['gold'] = 0;
				output('`!... zumindest wenn du kein Zwerg w�rst! Der Gott deines Volkes errettet dich jedoch verletzt. Dein Gold musst du allerdings zur�cklassen.');
				addnav('Weiter','forest.php?op=gruft_treppe&act='.$_GET['act']);
				$gruft_info['treppe_falle'] = false;
			}
			else {
				$session['user']['hitpoints'] = 0;
				$session['user']['gold'] = 0;
				$session['user']['experience'] *= 0.93;
				
				addnews('`3'.$session['user']['name'].'`3 wurde in einer Gruft fr�hzeitig begraben.');
				
				addnav('Mist..','news.php');
			}			
			
		}
		else {
			output('`!Nach Luft schnappend und hustend krabbelst du aus dem gegrabenen Spalt. Du hast es geschafft!');
			$gruft_info['treppe_falle'] = false;
			addnav('Weiter','forest.php?op=gruft_treppe&act='.$_GET['act']);
		}
				
		break;
	
	case 'gruft_kammer_klein':
		
		if($_GET['act'] == 'gems') {
			
			$session['user']['gems'] += $gruft_info['gems'];
			output('`!Nach dem hart erk�mpften Sieg raffst du die Edelsteine an dich und z�hlst sie: '.$gruft_info['gems'].' St�ck!`n`n');		
			$gruft_info['gems'] = 0;
			
		}
										
		if( sizeof($gruft_info['enemy_kammer_klein']) > 0 ) {		
		
			$victim = stripslashes(getsetting('forestspecial_gruft_lastkilled',''));
				
			output('`!Wie erstarrt h�ltst du inne: Im Dunklen patroulliert ein schwer bewaffneter Skelettkrieger. 
					Seine Knochen knacken unheilvoll. Er bewacht scheinbar die von einer flackernden Fackel beleuchtete Leiche ');
					
			if($victim != '') {
				output('einer Gestalt, die (vor der Verwesung) �hnlichkeit mit '.$victim.'`! gehabt haben muss..');
			}
			else {
				output('eines Unbekannten.');
			}
			output('`nDaneben stapelt sich im Halbdunkel ein ganzer Haufen funkelnder Edelsteine. Hm..`n`nAllerdings k�nntest du auch an der Wache vorbeischleichen
					und einen schr�gen Gang nach unten nehmen!');
		
			addnav('K�mpfen!','forest.php?op=gruft_kampf&enemy=kammer_klein');
			addnav('Zum Durchgang nach unten','forest.php?op=gruft_kammer_gross');
		}
		else {
			output('`!Du kannst nur noch eine kleine Kammer erkennen, die sp�rlich von einer Fackel erleuchtet wird.');
		
			addnav('Zum Durchgang nach unten','forest.php?op=gruft_kammer_gross');
		}		
		
		addnav('Zur�ck in den Gang','forest.php?op=gruft_gang');
						
		break;
		
	case 'gruft_kammer_gross':
		
		output('`!Du trittst in eine ausgedehnte Kammer, die im Inneren mit k�nstlerischen Ornamenten verziert ist.`n
				Wer auch immer der K�nstler war: Das Primat der Linie ist deutlich erkennbar!`n
				Unter der sch�nsten Linie steht eine wuchtige Truhe, die allerdings mit einem magischen Schloss gesichert ist. 
				An der hintersten Wand erkennst du eine Zeichnung, die so aussieht, als w�re sie mit Blut entstanden.. und 
				als h�tte sie eine Bedeutung!');
		
		addnav('Truhe betrachten','forest.php?op=gruft_truhe');		
		addnav('Zeichnung betrachten','forest.php?op=gruft_zeichnung');
		
		addnav('Zur�ck');
		addnav('Aufgang nach oben','forest.php?op=gruft_kammer_klein');
		addnav('Treppe nach oben','forest.php?op=gruft_treppe&act=up');
						
		break;
	
	case 'gruft_zeichnung':
		
		output('`4Blutig`! verlaufen die Linien, dunkelrot, und durchaus furchteinfl��end. Sie erinnern dich an... `4');
		
		if($gruft_info['right_pass'] == 1) {
			output(' sachten');
		}
		elseif($gruft_info['right_pass'] == 2) {
			output(' gem��igten');
		}
		elseif($gruft_info['right_pass'] == 3) {
			output(' heftigen');
		}
		elseif($gruft_info['right_pass'] == 4) {
			output(' m�rderischen');
		}
		
		output(' `!Wind. Sonst kannst du nichts besonderes entdecken.');
		
		addnav('Zur�ck zur Kammer','forest.php?op=gruft_kammer_gross');
		
		break;
		
	case 'gruft_truhe':
		
		output('`!Respektvoll und neugierig kniest du dich vor das Schloss der Truhe. Du kippst fast nach hinten in den Staub, als 
				es zu sprechen beginnt:`n`n');
		
		if($gruft_info['truhe_falle'] == false) {
			output('`4Duhu.. was.. willst... duhu... noch... hier..');
		
			addnav('Zur�ck zur Kammer','forest.php?op=gruft_kammer_gross');
		}
		
		else {
		
			if($_GET['pass'] == 0) {
			
				output('`4Duhu.. willst... an... meine... Sch�tzze... ihihich.. nehme an... so.... verrat ... mir ... doch.... wie ....
						gehehet.... der... Wind.... heute....?`n`n`!Was wirst du ihm antworten?');
					
				addnav('Laues L�ftchen','forest.php?op=gruft_truhe&pass=1');
				addnav('Gem��igter Wind','forest.php?op=gruft_truhe&pass=2');
				addnav('Sturm','forest.php?op=gruft_truhe&pass=3');
				addnav('Orkan','forest.php?op=gruft_truhe&pass=4');
				
				addnav('Zur�ck');
				addnav('..zur Kammer','forest.php?op=gruft_kammer_gross');
				
			}
			else {
				
				if($_GET['pass'] == $gruft_info['right_pass']) {
					
					$gruft_info['truhe_falle'] = false;
					
					output('`4Duhu.. hast... wohohohol... recht..`n`n`!Knarzend �ffnet sich der Deckel und du kannst die Sch�tze vor dir sehen:`n`n');
															
					// Belohnung
					switch(e_rand(1,5)) {
						
						case 2:	// Jackpot
							
							require_once(LIB_PATH.'dg_funcs.lib.php');
							
							if($session['user']['guildid'] && $session['user']['guildfunc'] != DG_FUNC_APPLICANT) {
							
								output('Zwei Splitter einer Insignie! Damit kann deine Gilde neue Insignien zusammensetzen.. Welch Segen!');
								
								$g = &dg_load_guild($session['user']['guildid']);
																									
								debuglog(' fand zwei Insignienbauteile');
									
								item_add($session['user']['acctid'],'insgnteil');
								item_add($session['user']['acctid'],'insgnteil');
																							
							}
							else {
								output('`^3`! Edelsteine und Schriftrollen, die deine Erfahrung anwachsen lassen!');
							
								$session['user']['experience'] *= 1.03;	
								$session['user']['gems'] += 3;	
							}
							
							break;
							
						case 1:	
						case 3:	
						case 4:	
							
							output('`^3`! Edelsteine!');
							
							$session['user']['gems'] += 3;	
							
							break;
							
						case 5:
							
							output('Ein `^paar`! Spinnweben und Kekskr�mel!');
							
							break;
						
					}
					
					addnav('Zur�ck zur Kammer','forest.php?op=gruft_kammer_gross');
					
				}
				else {	// falsches L�sungswort
					
					output('`4Duhu.. hast... wohohohol... NICHT.... recht..`n`n`!Das war es dann wohl... den Giftpfeilen auszuweichen, die dich gerade durchbohren, ist aussichtslos.`n`n
							Du verlierst 7% deiner Erfahrung und all das Gold, das du bei dir hattest!');
	
					$session['user']['hitpoints'] = 0;
					$session['user']['gold'] = 0;
					$session['user']['experience'] *= 0.93;
					
					addnews('`3'.$session['user']['name'].'`3 wurde von einer Truhe ermordet.');
					
					addnav('Mist..','news.php');
					
				}
				
			}			
			
		}
		
		break;
	
	case 'gruft_kampf':
										
		$session['user']['badguy'] = $gruft_info[ 'enemy_'.$_GET['enemy'] ];
		$gruft_info['recent_enemy'] = $_GET['enemy'];
		
		$battle = true;
		
		break;
	
	case 'gruft_out':
		
		if($_GET['act'] == 'leave') {
			output('`!Unverrichteter Dinge drehst du dich um und verschwindest wieder im Wald, um ein paar unschuldige Tiere zu t�ten. 
					 Besser kein Risiko eingehen..');
		}
		else {
			output('`!Langsam r�ckw�rtsgehend entfernst du dich von diesem schauerlichen Ort.');
			if($session['user']['buffbackup']) {
				$session['bufflist'] = unserialize($session['user']['buffbackup']);
			}
		}
		
		$session['user']['specialinc'] = '';
		unset($gruft_info);
				
		break;
	
	case 'fight':
	case 'run':
		
		$battle = true;
		
		break;	
	
	default:
			
		output('`!Vor dir erhebt sich mit einem Mal ein dunkler, furchterregender Steinhaufen, der sich bei n�herem Hinsehen als Eingang
				zu einer Gruft entpuppt. Dich schaudert, als du vorsichtig durch das hohe, knochige Gras schleichst. Totenstill ist es hier, und doch... 
				irgendetwas ist nicht geheuer..`n`n
				Was willst du tun? Davonlaufen oder dich einmal im Inneren umsehen?');
		
		$gruft_info['right_pass'] = e_rand(1,4);
		
		$badguy = array(
					'creaturename' => 'F�rst der Skelettkrieger',
					'creatureweapon' => 'Magischer Zweih�nder',
					'creaturelevel' => $session['user']['level']+1,
					'creatureattack' => $session['user']['attack']+1,
					'creaturedefense' => $session['user']['defence']+2,
					'creaturehealth' => $session['user']['maxhitpoints']
					);
		$gruft_info['enemy_kammer_klein'] = createstring($badguy);	
						
		$badguy['creaturename'] = 'Untoter Goblin mit vier Armen und Krallen';
		$badguy['creatureweapon'] = 'Richtig scharfe Krallen';
		$badguy['creatureattack'] = $session['user']['attack']-1;
		$badguy['creaturedefense'] = $session['user']['defence']-1;
		$badguy['creaturelevel'] = (max($session['user']['level']-1,1));
				
		$gruft_info['enemy_gang'] = createstring($badguy);	
		
		$badguy['creaturename'] = 'Harpyie';
		$badguy['creatureweapon'] = 'Furchtbares Kreischen';
		$badguy['creatureattack'] = $session['user']['attack']-1;
		$badguy['creaturedefense'] = $session['user']['defence']-1;
		$badguy['creaturelevel'] = (max($session['user']['level']-1,1));
						
		$gruft_info['enemy_treppe'] = createstring($badguy);	
		
		$gruft_info['treppe_falle'] = true;
		$gruft_info['truhe_falle'] = true;
			
		$gruft_info['recent_enemy'] = 0;
		
		$gruft_info['gems'] = e_rand(3,5);		
										
		addnav('Gruft betreten..','forest.php?op=gruft_in&act=enter');	
		addnav('Nichts wie weg!','forest.php?op=gruft_out&act=leave');
			
		break;	
	

}

if($battle) {
	
	/*if ( ($gruft_info['recent_enemy'] == 'gang' || $gruft_info['recent_enemy'] == 'treppe') && (count($session['bufflist'])>0 && is_array($session['bufflist']) || $_GET['skill']!="") ){ 
		$_GET['skill']=""; 
		if ($_GET['skill']=="") $session['user']['buffbackup']=serialize($session['bufflist']); 
		$session['bufflist']=array(); 
		output("`&Die Dunkelheit und Enge l�sst dir keine besonderen F�higkeiten!`0"); 
	}*/
	
	include_once('battle.php');
	
	if($victory) {

		unset($session['bufflist']['mount']);
						
		if($gruft_info['recent_enemy'] == 'kammer_klein') {
			addnav('Weiter zu den Edelsteinen','forest.php?op=gruft_kammer_klein&act=gems');
		}
		else if($gruft_info['recent_enemy'] == 'treppe') {
			addnav('Weiter','forest.php?op=gruft_treppe&act='.$_GET['act']);
			
		}
		else if($gruft_info['recent_enemy'] == 'gang') {
			addnav('Weiter','forest.php?op=gruft_gang');
		}
		else {
			addnav('Weiter','forest.php?op=gruft_in');
		}
		
		unset($gruft_info['enemy_'.$gruft_info['recent_enemy']]);
		$gruft_info['recent_enemy'] = '';
		
		$exp_plus = round($session['user']['experience'] * 0.01);
		
		$session['user']['experience'] += $exp_plus;
		
		output('`n`3F�r den Sieg �ber die Bestie erh�ltst du '.$exp_plus.' Erfahrungspunkte!');
				
	}
	else if($defeat) {
		
		if($gruft_info['recent_enemy'] == 'kammer_klein') {
			savesetting('forestspecial_gruft_lastkilled',addslashes($session['user']['name']));
		}
		
		addnews('`3'.$session['user']['name'].'`3 starb bei K�mpfen gegen Untote.');
		
		$exp_minus = round($session['user']['experience'] * 0.05);
		
		$session['user']['experience'] -= $exp_minus;
		$session['user']['gold'] = 0;
		
		output('`n`3Die Niederlage gegen die Bestie kostet dich '.$exp_minus.' Erfahrungspunkte und all dein Gold!');
		
		addnav('ARGH!!','news.php');
		
	}
	
	else {
		fightnav();
	}
	
}

if($session['user']['hitpoints'] == 0) {unset($gruft_info);$session['user']['specialinc'] = '';}

$session['user']['specialmisc'] = serialize($gruft_info);
?>