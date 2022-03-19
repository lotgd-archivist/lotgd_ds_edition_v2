<?php

function waffe_hook_process ( $item_hook , &$item ) {
	
	global $session,$item_hook_info;
	
	switch ( $item_hook ) {
		
		case 'use':
									
			item_set_weapon($item['name'],$item['value1'],$item['gold'],$item['id']);
			
			output('`QDu gürtest dich mit '.$item['name'].'`Q.');			
			
			addnav('Zum Inventar',$item_hook_info['ret']);
						
			break;
			
			
	}
		
	
}

?>