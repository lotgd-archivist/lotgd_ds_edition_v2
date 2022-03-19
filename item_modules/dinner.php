<?php

function dinner_hook_process ( $item_hook , &$item ) {
	
	global $session, $item_hook_info;
	
	switch ( $item_hook ) {
		
		case 'gift':
			
			if( item_count(' owner='.$item_hook_info['acctid'].' AND tpl_id="'.$item['tpl_id'].'" ') ) {
			
				output("`&Das geht leider nicht. ".$item_hook_info['rec_name']."`& hat bereits eine Verabredung. Da war wohl jemand schneller als du!");
				$item_hook_info['check'] = 1;
				return;
					
			}
			
			$session['user']['gold'] -= $item['tpl_gold'];
			$session['user']['gems'] -= $item['tpl_gems'];
			
			$item['tpl_gold'] = 0;
			$item['tpl_gems'] = 0;
						
			$item_hook_info['effect'] = '`&Einladung zu einem romantischen Abend mit '.$session[user][name].'.';			
			
			$item['tpl_description'] = 'Eine Einladung zu einem romantischen Abendessen mit '.$session['user']['name'].'.';
			$item['tpl_value1'] = $session['user']['acctid'];
			$item['tpl_value2'] = $item_hook_info['acctid'];
								
			item_add($item_hook_info['acctid'],0,false,$item);
			
			$item['tpl_description'] = 'Ein romantisches Abendessen mit '.$item_hook_info['rec_name'].'.';
			$item['tpl_value1'] = $item_hook_info['acctid'];
			$item['tpl_value2'] = $session['user']['acctid'];
			
			item_add($session['user']['acctid'],0,false,$item);
			
			// Standardversenden aufhalten
			$item_hook_info['hookstop'] = true;
												
			break;
					
	}
		
	
}

?>