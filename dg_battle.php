<?php
/*-------------------------------/
Name: dg_battle.php
Autor: tcb / talion für Drachenserver (mail: t@ssilo.de)
Erstellungsdatum: 6/05 - 9/05
Beschreibung:	Zuständig für Kämpfe des Gildensystems, am besten per Weiterleitung nutzen! Inkludierung sollte prinzipiell auch funktionieren.		
				Diese Datei ist Bestandteil des Drachenserver-Gildenmods (DG). 
				Copyright-Box muss intakt bleiben, bei Verwendung Mail an Autor mit Serveradresse.
/*-------------------------------*/

require_once('common.php');
require_once(LIB_PATH.'dg_funcs.lib.php');
require_once('dg_output.php');

page_header('Gildenkrieg');

$our_guild = &dg_load_guild($session['user']['guildid']);
	
$gid = $our_guild['war_target'];

if($gid <= 0) {
	
	output('Kein Ziel gegeben, Abbruch!');
	
	addnav('Zum Kriegszimmer','dg_main.php?op=in&subop=war');
	
	page_footer();
	
	exit;
	
}


$enemy_guild = &dg_load_guild($gid);

$rowe = user_get_aei('guildfights');

$str_battle_op = (empty($_GET['battle_op']) ? 'attack1' : $_GET['battle_op']);

if($_GET['op'] == 'fight') {
	$bool_battle = true;
}
else {
	$bool_battle = false;
}

switch($str_battle_op) {

	// Phase 1: Ansturm der Mitglieder auf Gildenwache
	case 'attack1':
		
		if(!$bool_battle) {
			
			if($rowe['guildfights'] > 0) {
				
					output('Du fühlst dich bereits zu erschöpft, um gegen hartgesottene Gildenwachen anzutreten!');
	
					addnav('Zum Kriegszimmer','dg_main.php?op=in&subop=war');
					
					page_footer();
					
					exit;
				
			}
			
			// Stärke ermitteln
			$int_hpflux = round( (1 + $session['user']['dragonkills'] * 0.25 * 0.01) * $session['user']['maxhitpoints'] );
			
			$arr_badguy = array('creaturename'=>'Gildenwachen',
							'creatureweapon'=>'Hammer aus schwerem Eisen',
							'creaturehealth'=>$session['user']['maxhitpoints']+$int_hpflux,
							'creatureattack'=>$session['user']['attack']+e_rand(1,2),
							'creaturedefense'=>$session['user']['defence']+e_rand(1,2),
							'creaturelevel'=>$session['user']['level'],
							'playerstarthp'=>$session['user']['hitpoints']
							);
					
			// .. und Angriff!
			$session['user']['badguy'] = createstring($arr_badguy);
			
			user_set_aei(array('guildfights'=>$rowe['guildfights']+1));
			
			$bool_battle = true;		
		}
	
		break;
	
	// Phase 2: Schlacht der Gildenwachen
	case 'attack2':
		
		$int_numberofsoldiers = (int)$_POST['nos'];
		$int_numberofsoldiers = min($int_numberofsoldiers,$our_guild['guard_hp']);
		
		if($int_numberofsoldiers > 0) {
		
			// Einfluß der Mitglieder
			// rausgenommen
			//$arr_infl = dg_calc_strength(array($enemy_guild['guildid'],$our_guild['guildid']));
			
			// Einfluß von Ausbauten etc.
			$int_attack = dg_calc_boni($our_guild['guildid'],'guard_atk',1);
			$int_def = dg_calc_boni($enemy_guild['guildid'],'guard_def',1);
			
			$int_attack += $our_guild['atk_upgrade'];
			$int_def += $enemy_guild['def_upgrade'];
													
			$str_out = '`c`b`&~~ Beginn des Kampfes ~~`b`c`n`n';
			
			$int_ourguild_before = $our_guild['guard_hp'];
			$enemy_guild['guard_hp_before'] = $enemy_guild['guard_hp'];
										
			// Gildenwachen durchlaufen und Kampf abwickeln
			while($enemy_guild['guard_hp'] > 0 && $int_numberofsoldiers > 0) {
				
				// Startwert: 10
				$int_tmp_atk = 10;
				$int_tmp_def = 10;
				
				// Situation
				$int_tmp_def += e_rand(-8,8);
				$int_tmp_atk += e_rand(-8,8);
												
				// Fähigkeiten durch Ausbauten
				$int_tmp_atk += e_rand(round($int_attack*0.5),($int_attack*2));
				$int_tmp_def += e_rand(round($int_def*0.5),($int_def*2));
								
				// Und jetzt abwiegen
				if($int_tmp_atk == $int_tmp_def) {
					if(e_rand(1,2) == 2) {
						$int_tmp_def = 0;
					}
					else {
						$int_tmp_atk = 0;
					}
				}
												
				if($int_tmp_atk > $int_tmp_def) {						
					$str_out .= '`n`@Ein Gegner ist gefallen!`0';
					$enemy_guild['guard_hp']--;
				}
				else {
					$str_out .= '`n`$Einer unserer Männer ist gefallen!`0';
					$int_numberofsoldiers--;
					$our_guild['guard_hp']--;
				}
				
			}
			
			$int_atk_lost = $int_ourguild_before - $our_guild['guard_hp'];
			$int_def_lost = $enemy_guild['guard_hp_before'] - $enemy_guild['guard_hp'];
			
			// Ende des Kampfes
			// Verteidigung hat gewonnen
			if($enemy_guild['guard_hp'] > 0) {
				//dg_massmail($session['user']['guildid'],'`$Verlorene Schlacht`0','`$In einer tapferen Schlacht gegen die Verteidiger der Gilde 
				//			'.$enemy_guild['name'].'`$ haben unsere Mannen leider den Kürzeren gezogen!`n`n
				//			Insgesamt ließen auf unserer Seite '.$int_atk_lost.' Mann ihr Leben.`0');
							
				output($str_out.'`n<hr>`$`c`bNiederlage!`b`c`8`n`nUnsere Truppen wurden von den Verteidigern niedergeschlagen.`n
						Wir verloren '.$int_atk_lost.' Mann, während die Verteidiger Verluste von '.$int_def_lost.' Kriegern davontrugen.`n'
						, true);
				
				// Wenn keine Mann mehr übrig, ist der Angriff zuende
				if($our_guild['guard_hp'] <= 0) {
				
					$our_guild['war_target'] = 0;
					
				}
			}
			// Angreifer hat gewonnen
			else {
				$enemy_guild['fights_suffered']++;
				// Diese Var wird erst beim nächsten Playerupdate der Feindgilde langsam zurückgesetzt
				$enemy_guild['fights_suffered_period'] += 2;
			
				$int_heal = 0;
				
				$int_guards_to_replace = dg_calc_boni($gid,'guard_hp_before',$enemy_guild['guard_hp_before']);
				
				if( $int_guards_to_replace > $enemy_guild['guard_hp'] ) {
					$int_heal = $int_guards_to_replace;
				}
				
				output($str_out.'`n<hr>`@`c`bSieg!`b`c`8`n`nUnsere Truppen haben den Sieg über die Verteidiger davongetragen.`n
						Wir verloren '.$int_atk_lost.' Mann, während die Verteidiger Verluste von '.$int_def_lost.' Kriegern davontrugen.`n
						Der Weg zur Schatzkammer ist nun frei!', true);
			
				dg_massmail($gid,'`$Verlorene Schlacht`0','`$In einer tapferen Schlacht gegen die angreifenden Truppen der Gilde 
							'.$our_guild['name'].'`$ haben unsere Mannen leider den Kürzeren gezogen!`n`n
							Insgesamt ließen auf unserer Seite '.$int_def_lost.' Mann ihr Leben
							'.($int_heal>0?', nach der Heilung werden dennoch '.$int_heal.' wieder kampfbereit sein.`0':'.`0'));
	
			}
						
		}
		else {	// Anzahl der Angreifer bestimmen
			
			$link = 'dg_battle.php?battle_op=attack2';
			addnav('',$link);
			
			output('`8Wie viele unserer derzeit `^'.$our_guild['guard_hp'].'`8 Söldner wollen wir gegen
					die `^'.$enemy_guild['guard_hp'].'`8 Mann der Verteidiger in die Schlacht werfen?`n
					<form method="POST" action="'.$link.'"><input type="text" name="nos" value="'.$our_guild['guard_hp'].'">
						<input type="submit" value="Angriff!"></form>',true);
			
		}
		
		addnav('Zum Kriegszimmer','dg_main.php?op=in&subop=war');
		
		break;
		
	// Phase 3: Einbruch
	case 'attack3':
		
		$our_guild['war_target'] = 0;
		
		$nothing = false;
		
		output('`2Du schreitest an den toten Wachen vorbei ins Innere der Gilde. Nachdem du auch die letzte Tür aufgebrochen hast, erblickst du ');
		
		$decision = e_rand(1,100);
						
		if($decision >= 1 && $decision <= 5) {	// Insignien
			
			$min = min($enemy_guild['regalia'],7) * 25;
			
			if(e_rand(1,100) >= $min) {$nothing=true;}
			else {
				
				$bool_stolen = false;
				
				output('den Raum der `bInsignien`b!');
				
				if($enemy_guild['regalia'] > 0) {
				
					$enemy_guild['regalia']--;
					$our_guild['regalia']++;
					
					$bool_stolen = true;
	
				}
				else {
					
					output('`2`nDu kannst dein Pech kaum fassen: Keine einzige Insignie wartet hier noch auf Abholung!');
					
				}
				
				if($bool_stolen) {
					
					dg_commentary($our_guild['guildid'],': `@stiehlt tatsächlich eine Insignie von '.$enemy_guild['name'].'`@!','war');
					dg_commentary($enemy_guild['guildid'],': `4stiehlt im Namen '.(($session['user']['sex'])?'ihre':'seine').'r Gilde '.$our_guild['name'].'`4 eine Insignie!','');
					dg_addnews($session['user']['name'].'`@ raubt für '.(($session['user']['sex'])?'ihre':'seine').' Gilde '.$our_guild['name'].'`@ eine Insignie von '.$enemy_guild['name'].'`@!',$session['user']['acctid']); 
					
					dg_massmail($enemy_guild['guildid'],'`4Insignie geraubt!','`4'.$session['user']['name'].'`4 hat im Namen '.(($session['user']['sex'])?'ihre':'seine').'r Gilde '.$our_guild['name'].'`4 deiner Gilde eine Insignie geraubt!',200);
					
					output('`2`nGlücklicherweise schimmert im Dämmerlicht vor dir eines dieser wertvollen Exemplare. Du zögerst nicht lang, packst die Gelegenheit am Schopfe und verschwindest mit der Insignie!');
					
				}
				
			}
		}	// END insignien
						
		elseif($decision > 5 && $decision <= 95) {	// Einfacher Diebstahl
			
			output('einen Teil der Schatzkammern.');
										
			if($enemy_guild['gold'] > 0 || $enemy_guild['gems'] > 0) {
				
				$steal_gold = min( round($enemy_guild['gold']*0.1) , 25000);
				$steal_gems = min( round($enemy_guild['gems']*0.1) , 30);
				
				$our_guild['gold'] += $steal_gold;
				$our_guild['gems'] += $steal_gems;
				$enemy_guild['gold'] -= $steal_gold;
				$enemy_guild['gems'] -= $steal_gems;
				
				dg_commentary($our_guild['guildid'],': `@stiehlt '.$steal_gold.' Gold und '.$steal_gems.' Edelsteine von '.$enemy_guild['name'].'`@!','war');
				dg_commentary($enemy_guild['guildid'],': `4stiehlt im Namen '.(($session['user']['sex'])?'ihre':'seine').'r Gilde '.$our_guild['name'].'`4 '.$steal_gold.' Gold und '.$steal_gems.' Edelsteine!','war');
				output('`nGierig stopfst du so viel wie möglich in deine Taschen und erbeutest `^'.$steal_gold.'`2 Gold und `^'.$steal_gems.'`2 Edelsteine.');
				
			}
			else {
				output('`nLeider ist dieser Raum völlig leer und von Gold oder ähnlichem keine Spur.');
				
			}
			
		}	// END einfacher Diebstahl
		else {	
			$nothing = true;
		}
		
		if($nothing) {
			
			output('nur einen weiteren Raum voller nutzlosen Kitsches.. was für ein Reinfall!');
			dg_commentary($our_guild['guildid'],': `8war bei seinem Raubzug in der Gilde '.$enemy_guild['name'].'`8 leider erfolglos.','war');										

		}
		
		addnav('Zum Kriegszimmer','dg_main.php?op=in&subop=war');
			
		break;
		
	default:
	
		output('Hier hast du nichts verloren. Benachrichtige bitte den Admin. Op: '.$_GET['battle_op']);
		
		break;
		
}




// Kampf!
if($bool_battle) {
			
	include("battle.php");
							
	if($victory) {
				
		$int_hp_diff = round( ($session['user']['hitpoints'] / $badguy['playerstarthp']) * 100 );
	
		$int_loose = 0;
		
		if($int_hp_diff >= 100) {
			$int_loose = 3;
		}
		else if($int_hp_diff >= 90) {
			$int_loose = 2;
		}
		else {
			$int_loose = 1;
		}
		
		$enemy_guild['guard_hp'] -= $int_loose;
												
		output('`2Dir gelingt es, nach einem harten Kampf '.$int_loose.' Gildenwachen niederzustrecken!`n');
		
		dg_commentary($our_guild['guildid'],': `@versetzt '.$int_loose.' Gildenwache'.($int_loose>1?'n':'').'`@ den Todesstoß!','war');
		
		addnav('Zum Kriegszimmer','dg_main.php?op=in&subop=war');
					
	}	// END if victory
	elseif($defeat) {
			
		killplayer(0,0,0,'');
						
		dg_commentary($our_guild['guildid'],': `4stirbt im Kampf gegen '.$badguy['creaturename'].'`4!','war');
		dg_addnews($session['user']['name'].'`5 wurde im Krieg '.(($session['user']['sex'])?'ihre':'seine').'r Gilde '.$our_guild['name'].'`5 gegen '.$enemy_guild['name'].'`5 von '.$badguy['creaturename'].'`5 niedergeschmettert!',$session['user']['acctid']); 
		
		output('`2Das ist das Ende, du weißt es genau. Ramius wird dich hoffentlich gnädig aufnehmen.');
						
		addnav('Tägliche News','news.php');
	}
	else {
		fightnav(false,false);
	}
	
		
}	// END if battle

// jegliche Veränderung speichern
dg_save_guild();

page_footer();
?>