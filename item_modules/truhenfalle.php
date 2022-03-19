<?php

function truhenfalle_hook_process ( $item_hook , &$item ) {
	
	global $session,$item_hook_info;
	
	switch ( $item_hook ) {
		
		case 'furniture':
									
			if($item_hook_info['op'] == 'refill') {
				
				$i = item_get(' tpl_id="gftph" AND owner='.$session['user']['acctid'],false);
							
				if($i['id']) {
									
					output('`2In deinen Taschen findest du eine Giftphiole. Willst du sie vielleicht dazufüllen, einem netten Dieb eine Freude bereiten und den Hausschatz etwas sichern?');
					
					addnav('1 Phiole einfüllen',$item_hook_info['link'].'&op=refill_ok&obj_id='.$i['id']);
				}
				else {
					output('`2In deinen Taschen findest du leider keine weiteren Giftphiolen.');
				}
						
			}
	
			elseif($item_hook_info['op'] == 'refill_ok') {
			
				item_delete( ' id='.(int)$_GET['obj_id'] );
			
				$count = db_affected_rows();
				
				if($count > 0) {
					$item_change['hvalue'] = $item['hvalue']+3;
					item_set('id='.$item['id'],$item_change);
				}
				
				output('`2Eine weitere Phiole mit absolut tödlichem Gift lauert nun auf ihr Opfer.`n`n');	
				addnav('Falle ist scharf');
				addnav('Zur Truhenfalle',$item_hook_info['link']);
				
			}
			elseif($item_hook_info['op'] == 'try') {
			
				$item_change['hvalue'] = $item['hvalue']-1;
				item_set('id='.$item['id'],$item_change);
				
				insertcommentary($session['user']['acctid'],': `$wollte heute einmal nachschauen, ob die Truhenfalle auch wirklich funktioniert: Sie tut es.','house-'.$item['deposit1']);
				addnews($session['user']['name'].'`$ hat gerade eine Truhenfalle getestet und weiß nun, dass sie funktioniert.');
								
				output('`tDu fühlst dich schon als richtiger Dieb, während du deine eigene Truhe "heimlich" zu öffnen versuchst. Jetzt, so weißt du,
						müsste eigentlich die Falle zuschnappen. Doch nichts passiert. Nichts? Genau: Du spürst noch, wie sich 
						deine Innereien in gefälliges Nichts auflösen und sich deine Seele vom Körper trennt. Sehr subtil, dieses Gift..`n
						Du verlierst 10% deiner Erfahrung und alles Gold, das du bei dir hattest..');								
								
				killplayer(100,10,0,'');
								
				addnav('Ramius, mein Freund!','news.php');				
			}
			else {
				
				$int_max_potions = 10;
				
				output('`2Vorsichtig wirfst du einen Blick auf die scharfgemachte Falle in der Schatztruhe. 
						Du erkennst noch `^'.$item['hvalue'].'`2 Giftladungen'.($item['hvalue'] >= $int_max_potions ? ' und damit das Maximum, welches in die kleine Truhe passt':'').'. 
						Direkt daneben verrotten die Überreste von `^'.$item['value2'].'`2 gemeinen, aber unfähigen Dieben.`n`n
						Je länger du die Truhenfalle anstarrst, desto neugieriger wirst du: Wie funktioniert das eigentlich?');
				
				if($item['hvalue'] < $int_max_potions) {
					addnav('Nachfüllen',$item_hook_info['link'].'&op=refill');
				}
				if($item['hvalue'] > 0) {
					addnav('Auch mal probieren!',$item_hook_info['link'].'&op=try');
				}
				
			}
			
			if($session['user']['hitpoints'] > 0) {		
				addnav($item_hook_info['back_msg'],$item_hook_info['back_link']);
			}
			
			break;
			
	}
		
	
}

?>