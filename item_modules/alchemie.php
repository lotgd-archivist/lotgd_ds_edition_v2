<?php

function alchemie_hook_process ( $item_hook , &$item ) {
	
	global $session,$item_hook_info;
	
	switch ( $item_hook ) {
		
		case 'furniture':
			
			// Weiterleitung zu Extra-Datei
			redirect( 'alchemie.php?ret='.urlencode($item_hook_info['back_link']) );
												
			break;
						
	}
		
	
}

?>