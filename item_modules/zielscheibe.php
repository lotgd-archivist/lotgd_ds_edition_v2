<?php

function zielscheibe_hook_process ( $item_hook , &$item ) {
	
	global $session,$item_hook_info;
	
	switch ( $item_hook ) {
		
		case 'furniture':

			$desc=substr($item[description],53);
			$desc=substr($desc,0,$desc-1);


			if ($session['user']['turns']>0) {

				output ("`&Du schleuderst ein Messer auf die Zielscheibe und triffst $desc ");
				switch (e_rand(1,10)) {
				 case 1:
				 case 2:
				 case 3:
				 case 4:
				 output ("`&leider nicht. Aber du kannst es ja nochmal versuchen...");
				 break;
				 case 5:
				 output ("`&mitten ins Gesicht! Ja, nochmal!");
				 break;
				 case 6:
				 output ("`&genau ins Auge! Gut gezielt!");
				 break;
				 case 7:
				 output ("`&in den Hals. Etwas zu tief, aber der Gedanke zählt...");
				 break;
				 case 8:
				 output ("`&knapp oberhalb der Stirn. Ja... recht gut für den Anfang.");
				 break;
				 case 9:
				 output ("`&am Kinn. Wenn das mal kein Grübchen gibt...");
				 break;
				 case 10:
					 if ($item[value2]==1) {
							 output ("`&so gewaltig, `^dass die Wucht des Aufpralls die ohnehin schon leicht rissige Scheibe in der Mitte teilt!`&");
							$sql="INSERT INTO commentary (postdate,section,author,comment) VALUES (now(),'".$item_hook_info['section']."',".$session[user][acctid].",'/me `@hat in einem Wutausbrauch eine Zielscheibe gespalten!')";
							db_query($sql) or die(db_error(LINK));
							
							item_delete( 'id='.$item['id'] );
														
							output("`n`n`^Das muntert dich jedoch derart auf, dass du `@".(($session['user']['level'])*100)."`^ Erfahrungspunkte dazu bekommst!");
							$session['user']['experience']+=$session['user']['level']*50;
					 } else {
						output ("`&so gewaltig, `^dass die Scheibe ein leises, berstendes Geräusch von sich gibt.`&");
						
						$dam=$item[value2]+1;
						
						item_set( 'id='.$item['id'] , array('value2'=>$dam) );
				
					}
				
				break;
				}
				} else
				{ output ("`&So gern du $desc `&auch mal wieder ein Messer zwischen die Augen setzen würdest, jetzt bist du leider schon viel zu müde dafür!"); }
											
				
				
			addnav($item_hook_info['back_msg'],$item_hook_info['back_link']);
			
			break;
		}
		
	
}

?>