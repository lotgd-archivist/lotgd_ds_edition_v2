<?php

function zaubertrnk_guild_hook_process ( $item_hook , &$item ) {
	
	global $session,$item_hook_info;
			
	switch ( $item_hook ) {
		
		case 'battle':
			
			$item_hook_info['hookstop'] = true;
			
			if($item['buff1'] > 0) {$list .= ','.$item['buff1'];}
			if($item['buff2'] > 0) {$list .= ','.$item['buff2'];}
			
			$buffs = item_get_buffs( ITEM_BUFF_FIGHT , $list );
			
			// Improvement durch Ausbaulvl
			// Durch 100 teilen, da es hier als Int gespeichert wird
			$buffs[0]['atkmod'] = max($item['hvalue2']*0.01*$buffs[0]['atkmod'],1);
			$buffs[0]['defmod'] = max($item['hvalue2']*0.01*$buffs[0]['defmod'],1);
			
			item_set_buffs (ITEM_BUFF_FIGHT , $buffs);
					
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
			
			$item_hook_info['hookstop'] = true;
			
			if($item['buff1'] > 0) {$list .= ','.$item['buff1'];}
			if($item['buff2'] > 0) {$list .= ','.$item['buff2'];}
			
			$buffs = item_get_buffs( ITEM_BUFF_FIGHT , $list );
			
			// Improvement durch Ausbaulvl
			// Durch 100 teilen, da es hier als Int gespeichert wird
			$buffs[0]['atkmod'] = max($item['hvalue2']*0.01*$buffs[0]['atkmod'],1);
			$buffs[0]['defmod'] = max($item['hvalue2']*0.01*$buffs[0]['defmod'],1);
			
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
			
		
	}
		
	
}

?>