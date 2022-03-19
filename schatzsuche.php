<?
// by talion (t@ssilo.de) f�r lotgd.drachenserver.de 

require_once('common.php');

page_header('Die alte Eiche nahe des Dorfbrunnens');
$link = 'schatzsuche.php?';

output('`2Dem Wispern des Windes in den Bl�ttern folgend gelangst du zu den Wurzeln eines uralten, gewaltigen Baumes. Seine dichte Krone scheint selbst noch die D�cher des nahen Wohnviertels zu �berragen.');

addnav('Zum Brunnen wenden','well.php');

if($_GET['op'] == 'grab' || $session['questvar1'] > 0) {
	
	if($session['questvar1'] == 0) {
		$gems = e_rand(24,25);
		$session['user']['gems']+=$gems;
		
		$dmon = item_get_tpl(' tpl_id="dmons" ');
		
		item_add($session['user']['acctid'],0,false,$dmon);
		
		debuglog('fand einen Schatz in H�he von '.$gems.' Edelsteinen und die D�monenklinge!');
						
		item_delete(' owner='.$session['user']['acctid'].' AND tpl_id="mapt" AND hvalue=0 ');
		
		$map_wertlos = item_get_tpl(' tpl_id="mapw" ');
		$map_wertlos['hvalue'] = 1;
		
		item_set(' tpl_id="mapt" AND hvalue=0 ',0,false,$map_wertlos);
		
		output('`n`n`@Schwei��berstr�mt willst du schon aufgeben, als dein Spaten mit einem Mal auf etwas festes trifft. Fieberhaft legst du mit den Fingern eine h�lzerne, eisenbeschlagene Truhe frei.`n`nNachdem du zitternd das Schloss aufgebrochen hast, funkelt dir der Schatz entgegen: `^'.$gems.'`@ Edelsteine sowie '.$dmon['tpl_name'].'`@. �bergl�cklich packst du den Fund in dein Inventar.`n`n`n');
		addnews($session['user']['name'].'`2 hat einen seltenen Schatz ausgegraben!');
		$sql = 'UPDATE account_extra_info SET treasure_f=treasure_f+1 WHERE acctid='.$session['user']['acctid'];
		db_query($sql);
		
		savesetting('treasurelastacc',$session['user']['acctid']);
						
	}
	else {
		addnav('Weitergraben (noch '.$session['questvar1'].' Fu�)',$link.'op=grab');
		if($_GET['op'] == 'grab') {
			output('`n`n`@M�hsam st��t du den Spaten wieder und wieder in das feste Erdreich. Du wei�t, bald wirst du f�r deine M�hen entlohnt werden..');
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
		
			output('`n`n`@Du wirfst einen genauen Blick auf die Teile der Schatzkarte, die du bei dir tr�gst.. schaust noch einmal.. und traust deinen Augen nicht: Hier m�sste es sein!`nWillst du nun danach graben?`n`n');
			
			if(getsetting('treasurelastacc',0) == $session['user']['acctid']) {
				output('`$Doch so sehr du auch nach einem geeigneten Ort zum Graben suchst - Nirgendwo will es dir gelingen!`nSchlie�lich f�llt dir eine Klausel am unteren Rand der Karte ins Auge, die besagt, dass kein Abenteurer zweimal hintereinander diesen Fund t�tigen wird!`nTja, sieht so aus, als bliebe dir nur der Verkauf der Teile..');
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
