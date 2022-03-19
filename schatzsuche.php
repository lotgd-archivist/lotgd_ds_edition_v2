<?
// by talion (t@ssilo.de) für lotgd.drachenserver.de 

require_once('common.php');

page_header('Die alte Eiche nahe des Dorfbrunnens');
$link = 'schatzsuche.php?';

output('`2Dem Wispern des Windes in den Blättern folgend gelangst du zu den Wurzeln eines uralten, gewaltigen Baumes. Seine dichte Krone scheint selbst noch die Dächer des nahen Wohnviertels zu überragen.');

addnav('Zum Brunnen wenden','well.php');

if($_GET['op'] == 'grab' || $session['questvar1'] > 0) {
	
	if($session['questvar1'] == 0) {
		$gems = e_rand(24,25);
		$session['user']['gems']+=$gems;
		
		$dmon = item_get_tpl(' tpl_id="dmons" ');
		
		item_add($session['user']['acctid'],0,false,$dmon);
		
		debuglog('fand einen Schatz in Höhe von '.$gems.' Edelsteinen und die Dämonenklinge!');
						
		item_delete(' owner='.$session['user']['acctid'].' AND tpl_id="mapt" AND hvalue=0 ');
		
		$map_wertlos = item_get_tpl(' tpl_id="mapw" ');
		$map_wertlos['hvalue'] = 1;
		
		item_set(' tpl_id="mapt" AND hvalue=0 ',0,false,$map_wertlos);
		
		output('`n`n`@Schweißüberströmt willst du schon aufgeben, als dein Spaten mit einem Mal auf etwas festes trifft. Fieberhaft legst du mit den Fingern eine hölzerne, eisenbeschlagene Truhe frei.`n`nNachdem du zitternd das Schloss aufgebrochen hast, funkelt dir der Schatz entgegen: `^'.$gems.'`@ Edelsteine sowie '.$dmon['tpl_name'].'`@. Überglücklich packst du den Fund in dein Inventar.`n`n`n');
		addnews($session['user']['name'].'`2 hat einen seltenen Schatz ausgegraben!');
		$sql = 'UPDATE account_extra_info SET treasure_f=treasure_f+1 WHERE acctid='.$session['user']['acctid'];
		db_query($sql);
		
		savesetting('treasurelastacc',$session['user']['acctid']);
						
	}
	else {
		addnav('Weitergraben (noch '.$session['questvar1'].' Fuß)',$link.'op=grab');
		if($_GET['op'] == 'grab') {
			output('`n`n`@Mühsam stößt du den Spaten wieder und wieder in das feste Erdreich. Du weißt, bald wirst du für deine Mühen entlohnt werden..');
			$session['questvar1']--;
		}
		else {
			output('`n`n`@Du betrachtest vom Rand des Loches aus das bisher Erreichte. Willst du nicht weiter graben?');
		}
		
	}
		
}
else {
		
		$show_invent = true;
						
		$count = item_count(' tpl_id="mapt" AND owner='.$session['user']['acctid'].' AND hvalue=0 ');

		$found = false;
		
		if($count >= 4) {
			$found = true;
		}
		
		if($found) {
		
			output('`n`n`@Du wirfst einen genauen Blick auf die Teile der Schatzkarte, die du bei dir trägst.. schaust noch einmal.. und traust deinen Augen nicht: Hier müsste es sein!`nWillst du nun danach graben?`n`n');
			
			if(getsetting('treasurelastacc',0) == $session['user']['acctid']) {
				output('`$Doch so sehr du auch nach einem geeigneten Ort zum Graben suchst - Nirgendwo will es dir gelingen!`nSchließlich fällt dir eine Klausel am unteren Rand der Karte ins Auge, die besagt, dass kein Abenteurer zweimal hintereinander diesen Fund tätigen wird!`nTja, sieht so aus, als bliebe dir nur der Verkauf der Teile..');
			}
			else {
				$session['questvar1'] = 5;
				addnav('Graben!',$link.'op=grab');
			}
			
		}
		else {
			output('`nDu rastest eine Weile, an den Stamm gelehnt und die Wolken beobachtend.');
		}
		
	

}

page_footer();
?>
