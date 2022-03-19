<?php

function goldenegg_hook_process ( $item_hook , &$item ) {
	
	global $session,$item_hook_info;
	
	switch ( $item_hook ) {
		
		case 'pvp_victory':
			
			$badguy = $item_hook_info['badguy'];
									
			savesetting("hasegg",stripslashes($session[user][acctid]));
			
			output("`n`^Du nimmst $badguy[creaturename] `^das goldene Ei ab!`0`n");
			
			addnews("`^".$session['user']['name']."`^ nimmt {$badguy['creaturename']}`^ das goldene Ei ab!");
			
			$session[user][reputation]+=2;
			
			item_set(' id='.$item['id'], array('owner'=>$session['user']['acctid']) );
			
			break;
			
		case 'newday':
			// Pro DK 0.5 % Chance, dass das goldene Ei verloren geht
			$eggloss=$session[user][dragonkills]*0.5;
			$chance = e_rand(0,100);
			if ($chance <= $eggloss) {
				output ("`n`5Du hattest das `^goldene Ei`5 nun lange genug! In der letzten Nacht hat es dich verlassen um von einem Anderen gefunden zu werden.");
				savesetting("hasegg","0");
				item_set(' id='.$item['id'], array('owner'=>0) );
			}
						
			break;
			
	}
		
	
}

?>