<?

require_once(LIB_PATH.'runes.lib.php');

function runen_hook_process ( $item_hook , &$item ) {
	
	global $session,$item_hook_info;
	
	switch ( $item_hook ){
		case 'find_forest':
			$class_id 	= getsetting('runes_classid',0);
			$ident	  	= user_get_aei('runes_ident');
			$ident  	= explode(';',$ident['runes_ident']);
			$sql		= 'SELECT id FROM '.RUNE_EI_TABLE.' WHERE seltenheit<='.e_rand(1,255).' ORDER BY RAND() LIMIT 1';
			$res		= db_query( $sql );
			if( $res ){
				$rune	= db_fetch_assoc( $res );
				if( $rune ){			
					$item['tpl_value2'] = $rune[ 'id' ];
				}
			}			
			
			if( !$item['tpl_value2'] ){
				$item_hook_info['hookstop'] = 1;	
			}
			elseif( array_search($rune[ 'id' ], $ident)  ){
				$item	= item_get_tpl('tpl_class = '.$class_id.' AND tpl_value2='.$rune[ 'id' ]);
			}		
		break;	
		
		
		case 'send_hook':
			$dest 	= $item_hook_info['recipient']['acctid'];		
			$ident	= user_get_aei('runes_ident', $dest);
			$ident  = explode(';',$ident['runes_ident']);
			
			if( $item['tpl_id'] != RUNE_DUMMY_TPL && !array_search($item['value2'],$ident) ){
				$tpl					= item_get_tpl('tpl_id="'.RUNE_DUMMY_TPL.'"');
				$item['name'] 			= $tpl['tpl_name']; 	 	    	    	    	    	    	    	 							
				$item['description'] 	= $tpl['tpl_description']; 									
				$item['gold'] 			= $tpl['tpl_gold']; 							
				$item['gems'] 			= $tpl['tpl_gems']; 								
				$item['value1'] 		= $tpl['tpl_value1']; 								 								
				$item['hvalue'] 		= $tpl['tpl_hvalue']; 								
				$item['hvalue2'] 		= $tpl['tpl_hvalue2'];
				$item['tpl_id']			= RUNE_DUMMY_TPL;
				
			}
			else if($item['tpl_id'] == RUNE_DUMMY_TPL && array_search($item['value2'],$ident)){
				$tpl					= item_get_tpl('tpl_class = '.$item['tpl_class'].' AND tpl_value2='.$item['value2']);
				$item['name'] 			= $tpl['tpl_name'];  		 	 	    	    	    	    	    	    	 							
				$item['description'] 	= $tpl['tpl_description']; 									
				$item['gold'] 			= $tpl['tpl_gold']; 							
				$item['gems'] 			= $tpl['tpl_gems']; 								
				$item['value1'] 		= $tpl['tpl_value1']; 								 								
				$item['hvalue'] 		= $tpl['tpl_hvalue']; 								
				$item['hvalue2'] 		= $tpl['tpl_hvalue2'];
				$item['tpl_id']			= $tpl['tpl_id'];				
			}	
		
		break;
		
		
		default:
			echo $item_hook."<br>";
			print_r($item_hook_info);
			die();
			
		
		break;
	
	}
	
	
}
	
	
	
	
	
	
?>
