<?php

function beet_hook_process ( $item_hook , &$item ) {
	
	global $session;
	
	switch ( $item_hook ) {
		
		case 'newday':
			
			item_set(' id='.$item['id'], array('value1'=>0));
												
			break;
		
			
	}
		
	
}

?>