<?php

function segen_hook_process ( $item_hook , &$item ) {
	
	global $session, $item_hook_info;
	
	switch ( $item_hook ) {
		
		case 'newday':
									
			$buffs = ','.$item['buff1'];
			$buffs .= ','.$item['buff2'];
			
			item_set_buffs ( ITEM_BUFF_NEWDAY , $buffs );
			
			output('`n`1'.$item['name'].'`1: '.$item['description']);
			
			if( $item['hvalue'] <= 1) {
				
				output('`n`Q'.$item['name'].'`Q hat seine Kraft verloren.');
				
				item_delete( 'id='.$item['id'] );
				
			}
			else {
				
				item_set(' id='.$item['id'], array('hvalue'=>$item['hvalue']-1) );

			}
						
			break;
					
	}
		
	
}

?>