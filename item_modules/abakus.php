<?php

function abakus_hook_process ( $item_hook , &$item ) {
	
	global $session,$item_hook_info;
	
	switch ( $item_hook ) {
		
		case 'furniture':
									
			output ("`7Du stellst dich vor das seltsame Gerät und beginnst ein wenig zu rechnen.`n`n");
			$result=0;
			for ($i=1;$i<=10;$i++){
            $value[$i]=0;
            }

			for ($i=1;$i<=10000;$i++){
				$dice  = e_rand(1,10);
				$result+=$dice;
				$value[$dice]++;
   			}

			$result*=0.0001;
			output ("`7Du erhältst das Ergebnis `@$result`7 !`n`n");

            if (($result>=5.498) && ($result<=5.502)) { output ("`7Erfreut über ein so gutes Ergebnis erhältst du einen zusätzlichen Waldkampf!`n");
			$session[user][turns]++; }

            if ($session['user']['superuser']>0)
            {
            output("`710000 Würfe von 1-10 ergaben:`n");
           	for ($i=1;$i<=10;$i++){
            output($i.": ".$value[$i]."`n");
            }
            }
			addnav($item_hook_info['back_msg'],$item_hook_info['back_link']);
			
			break;
			
	}
		
	
}

?>
