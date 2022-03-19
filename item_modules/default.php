<?php

function default_hook_process ( $item_hook , &$item ) {
	
	global $session,$item_hook_info;
	
	switch ( $item_hook ) {
		
		case 'newday':
									
			break;
			
		case 'pvp_victory':	// Angreifer (User) hat gewonnen
			
			// Wenn Wahrscheinlichkeit gegeben und im Inventar
			if ( $item ['pvp_victory_loose'] >= $item_hook_info ['min_chance'] && $item ['deposit1'] == 0 ) {	
				
				if ( sizeof ( $item_hook_info ['loose_str'] ) == 0 ) {	// Noch nichts verloren
					
					$where = ' id='.(int)$item ['id'];
					$item ['owner'] = $session['user']['acctid'];
										
					if ( item_set ( $where , $item ) ) {
						
						$item_hook_info ['loose_str'] .= '`$Außerdem vermisst du nun `^'.$item['name'].'`$!`n';
						$item_hook_info ['win_str'] .= '`2Du nimmst deinem Opfer `^'.$item['name'].'`2 ab!`n';
												
					}
					
				}
				
			}
									
			break;
			
		case 'pvp_defeat':	// Angreifer (User) hat verloren
				
			// Wenn Wahrscheinlichkeit gegeben und im Inventar
			if ( $item ['pvp_defeat_loose'] >= $item_hook_info ['min_chance'] && $item ['deposit1'] == 0 ) {	
				
				if ( sizeof ( $item_hook_info ['loose_str'] ) == 0 ) {	// Noch nichts verloren
					
					$where = ' id='.(int)$item ['id'];
					$item ['owner'] = $item_hook_info['badguy_acctid'];
										
					if ( item_set ( $where , $item ) ) {
						
						$item_hook_info ['loose_str'] .= '`$Außerdem vermisst du nun `^'.$item['name'].'`$!`n';
						$item_hook_info ['win_str'] .= '`2Du nimmst dem Angreifer `^'.$item['name'].'`2 ab!`n';
												
					}
					
				}
				
			}	
									
			break;
		
		case 'find_forest':
									
			if ( item_add( $session['user']['acctid'], 0, true, $item ) ) {
				output('`n`qBeim Durchsuchen von '.$badguy['creaturename'].' `qfindest du `&'.$item['tpl_name'].'`q! ('.$item['tpl_description'].')`n`n`#');
			}
		
			break;
			
		case 'forest_death':
									
			break;
			
		case 'dragon_death':
									
			break;
			
		case 'gift':	// Nach Versenden des Geschenks
															
			break;
			
		case 'use':
			
			break;
				
			
	}
		
	
}

?>