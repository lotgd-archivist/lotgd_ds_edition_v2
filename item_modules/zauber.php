<?php

function zauber_hook_process ( $item_hook , &$item ) {
	
	global $session,$item_hook_info;
	
	switch ( $item_hook ) {
		
		case 'battle':
			
			if($item['buff1'] > 0) {$list .= ','.$item['buff1'];}
			if($item['buff2'] > 0) {$list .= ','.$item['buff2'];}
			
			item_set_buffs( ITEM_BUFF_FIGHT , $list );
					
			$item['gold']=round($item['gold']*($item['value1']/($item['value2']+1)));
			$item['gems']=round($item['gems']*($item['value1']/($item['value2']+1)));
			
			$item['value1']--;
			
			if ($item['value1']<=0 && $item['hvalue']<=0){
				item_delete(' id='.$item['id']);
			}else{
				item_set(' id='.$item['id'], $item);
			}
			
			break;
			
		case 'battle_arena':
			
			if($item['buff1'] > 0) {$list .= ','.$item['buff1'];}
			if($item['buff2'] > 0) {$list .= ','.$item['buff2'];}
			
			$buffs = item_get_buffs( ITEM_BUFF_FIGHT , $list );
			
			if(sizeof($buffs) > 0) {
				
				foreach($buffs as $b) {
				
					$GLOBALS['goodguy']['bufflist'][$b['name']] = $b;
				
				}
				
			}
					
			$item['gold']=round($item['gold']*($item['value1']/($item['value2']+1)));
			$item['gems']=round($item['gems']*($item['value1']/($item['value2']+1)));
			
			$item['value1']--;
			
			if ($item['value1']<=0 && $item['hvalue']<=0){
				item_delete(' id='.$item['id']);
			}else{
				item_set(' id='.$item['id'], $item);
			}
			
			break;
			
		case 'use':
									
			if($item['buff1'] > 0) {$list .= ','.$item['buff1'];}
			if($item['buff2'] > 0) {$list .= ','.$item['buff2'];}
			
			$buffs = item_get_buffs( ITEM_BUFF_USE , $list );
			
			output('`QDu benutzt '.$item['name'].'`Q!');
			
			if(sizeof($buffs) > 0) {
				
				output('`n`nKurz darauf bemerkst du schon die ersten Effekte..`n');
				
				foreach($buffs as $b) {
					
					output($b['roundmsg'].'`n');		
					
				}
			}
									
			item_set_buffs( ITEM_BUFF_USE , $buffs );
					
			$item['gold']=round($item['gold']*($item['value1']/($item['value2']+1)));
			$item['gems']=round($item['gems']*($item['value1']/($item['value2']+1)));
			
			$item['value1']--;
			
			if ($item['value1']<=0 && $item['hvalue']<=0){
				item_delete(' id='.$item['id']);
			}else{
				item_set(' id='.$item['id'], $item);
			}
			
			addnav('Zum Beutel',$item_hook_info['ret']);
			
			break;
	}
		
	
}

?>