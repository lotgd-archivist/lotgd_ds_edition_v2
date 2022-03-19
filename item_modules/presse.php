<?php

function presse_hook_process ( $item_hook , &$item ) {
	
	global $session,$item_hook_info;
	
	switch ( $item_hook ) {
		
		case 'furniture':
									
			if ($session[user][turns]<=0) {
			  output("`2Du fühlst dich wirklich schon zu müde um heute noch irgendetwas auszupressen.`2`n ");
			  
			  } else {
			
			if ($session[user][gems]<=0) {
			  output("`2Du stellst dich vor das Gerät und drückst mit spielender Leichtigkeit den langen Hebel herunter.`2`n ");
			  output("`2Natürlich kann man auch `#Luft`2 pressen, aber ob das so viel Sinn macht?`2`n ");
			  
			} else {
			  $session[user][gems]-=1;
			  output("`2Du stellst dich vor das Gerät, legt einen deiner kostbaren Edelsteine in die dafür vorgesehene Vertiefung und drückst langsam und mit Kraft den langen Hebel herunter.`2`n ");
			  output("`2Unter leisem Knirschen tropft eine trübe Flüssigkeit in ein Schälchen am unteren Ende der Maschine.`2`n`n ");
			  output("`2Ohne zu zögern trinkst du diesen kleinen Schluck und`2`n ");
			  switch(e_rand(1,10)){
			  case 1 :
			  case 2 :
			  case 3 :
			  output("`2fühlst dich gestärkt. Deine Lebenspunkte werden permanent um 1 `@erhöht`2.`n ");
			  $session[user][maxhitpoints]+=1;
			  
			  break;
			  case 4 :
			  output("`2verbringst einige Zeit mit würgen und erbrechen. Also das ging total daneben.`n ");
			  output("`2Du fühlst dich geschwächt. Deine Lebenspunkte werden permanent um 1 `4verringert`2.`n ");
			  output("`2Ausserdem verlierst du einen Waldkampf.`n ");
			  $session[user][maxhitpoints]-=1;
			  $session[user][turns]-=1;
			  
			  break;
			  case 5 :
			  output("`2freust dich wahnsinnig, dass du deine Lebenspunkte permanent um 2 `@erhöht`2 werden.`n ");
			  output("`2Dabei verlierst du einen Waldkampf.`n ");
			  output("`2Ob dir solch eine Meisterleistung nochmal gelingt?`n ");
			  
			  $session[user][maxhitpoints]+=2;
			  $session[user][turns]--;
			  break;
			  case 6 :
			  case 7 :
			  case 8 :
			  case 9 :
			  case 10 :
			  output("`2stellst fest, dass du kein sehr guter Edelsteinpresser bist. Es passiert absolut nichts.`n ");
			  
			  break;
			 
			}
			}
			}
					
			addnav($item_hook_info['back_msg'],$item_hook_info['back_link']);
			
			break;
			
	}
		
	
}

?>