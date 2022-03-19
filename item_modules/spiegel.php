<?php

function spiegel_hook_process ( $item_hook , &$item ) {
	
	global $session,$item_hook_info;
	
	switch ( $item_hook ) {
		
		case 'furniture':
									
			switch(e_rand(1,5)){
				case 1:
				output("`2Du blickst in den Spiegel, in der Hoffnung etwas erfreuliches zu sehen. ");
				break;
				case 2:
				output("`2Dir starrt ".($session[user][sex]?"eine völlig unbekannte Frau":"ein völlig unbekannter Mann")." entgegen. ");
				break;
				case 3:
				if ($session[user][charm]<10) output("`2Du fragst dich, warum dieses häßliche Bild im Haus hängt. ");
				if ($session[user][charm]>=10 and $session[user][charm]<50) output("`2Naja, wer auch immer dieses Bild gemalt hat, hat Talent, sollte aber noch etwas üben. ");
				if ($session[user][charm]>=50) output("`2Das ist ein wirklich tolles Bild von ".($session[user][sex]?"einer Frau":"einem Mann")."! ");
				break;
				case 4:
				output("`2Erstaunlicher Apparat. ");
				break;
				case 5:
				output("`2Du verbringst eine ganze Weile vor dem Spiegel. ");
				if (e_rand(1,3)==2 && $sesion[user][turns]>0){
					output("Dabei merkst du nicht, wie die Zeit vergeht. Du vertrödelst einen Waldkampf! ");
					$session[user][turns]--;
				}
				break;
			}
			$was=e_rand(1,3);
			if ($was==1 && $session[user][turns]>0){
				$session[user][charm]--;
				if ($session[user][charm]<=0) $session[user][charm]=0;
				output("`nDu `4verlierst`2 einen Charmepunkt!");
			}else if ($was==3 && $session[user][turns]>0){
				$session[user][charm]++;
				output("`nDu `@bekommst`2 einen Charmepunkt.");
			}else if ($session[user][turns]<=0){
				output("`nDu hast heute keine Zeit mehr, dich um dein Äußeres zu kümmern.");
			}else{
			}
					
			addnav($item_hook_info['back_msg'],$item_hook_info['back_link']);
			
			break;
			
	}
		
	
}

?>