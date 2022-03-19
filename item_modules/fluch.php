<?php

function fluch_hook_process ( $item_hook , &$item ) {
	
	global $session;
	
	switch ( $item_hook ) {
		
		case 'newday':
						
			// $buffs = item_get_combo_buffs ( $item['tpl_id'] );
			
			if($item['buff1'] > 0) {$buffs .= ','.$item['buff1'];}
			if($item['buff2'] > 0) {$buffs .= ','.$item['buff2'];}
			
			item_set_buffs ( ITEM_BUFF_NEWDAY , $buffs );
			
			output('`n'.$item['name'].'`G nagt an dir!');
			
			if( $item['hvalue'] <= 1) {
				
				output(' Aber nur noch heute.');
				
				item_delete( ' id='.$item['id'] );
				
			}
			else {
				
				item_set(' id='.$item['id'] , array('hvalue'=>$item['hvalue']-1));				
				
			}
						
			break;
			
		
	}
		
	
}

?>