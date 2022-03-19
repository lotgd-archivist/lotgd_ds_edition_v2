<?php

function schaukasten_hook_process ( $item_hook , &$item ) {
	
	global $session,$item_hook_info;
	
	switch ( $item_hook ) {
		
		case 'furniture':
					  
			$sql = "SELECT name FROM accounts WHERE acctid=".$item['owner'];
			$result2 = db_query($sql) or die(db_error(LINK));
			$rowo = db_fetch_assoc($result2);
			
			output("`&In einer herrlich groﬂen Vitrine im rustikalen Landhaus-Stil hat ".$rowo['name']."`& ein paar ganz besondere Dinge zum Betrachten ausgestellt.`n`n");
			output("`n<table border='0'><tr><td>`2`bVon ".$rowo['name']." `2gesammelte Troph‰en:`b</td></tr><tr><td valign='top'>",true);
		
			
			$result = item_list_get( 'owner='.$item['owner'].' AND i.tpl_id="trph" ' , ' ORDER BY hvalue,id ASC ' );
			
			
			$amount=db_num_rows($result);
			
			$dkvalue=0;
			if (!$amount) { output("`iLeider gibt es hier auﬂer einem abgekauten Apfel nichts zu sehen."); }
			
			for ($i=1;$i<=$amount;$i++){
			
						$item = db_fetch_assoc($result);
			
						output("`&-$item[name]`0`n");
						$dkvalue+=$item[value1];
			}
						 output("</td></tr></table>",true);
			output("`n`&Die Troph‰en haben einen Gesamtwert von `^$dkvalue`& Drachenkills!`n");
			
			addnav($item_hook_info['back_msg'],$item_hook_info['back_link']);
			
			break;
			
	}
		
	
}

?>