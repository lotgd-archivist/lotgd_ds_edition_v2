<?php

function puppe_hook_process ( $item_hook , &$item ) {
	
	global $session,$item_hook_info;
	
	switch ( $item_hook ) {
		
		case 'furniture':
 
			$desc=substr($item[description],50);
			$desc=substr($desc,0,$desc-1);


			if ($session['user']['turns']>2) {
				output ("`&Du zückst deine Waffe und triffst $desc ");
				switch (e_rand(1,10)) {
					 case 1:
					 case 2:
					 case 3:
					 case 4:
					 output ("`&nicht. Viel Glück beim nächsten Mal.");
					 break;
					 case 5:
					 output ("`&am Arm. Naja... das können wir aber besser!");
					 break;
					 case 6:
					 output ("`&am Bein. Nicht schlecht...");
					 break;
					 case 7:
					 output ("`&am Bauch. Los, nochmal!");
					 break;
					 case 8:
					 output ("`&an der Brust. Sauberer Schlag!");
					 break;
					 case 9:
					 output ("`&am Kopf! Perfekter Hieb!");
					 break;
					 case 10:
 						if ($item[value2]==3) {
						 output ("`&mit vielen wilden Schlägen. `^Das war zu viel für die arme Puppe und sie bricht berstend vor deinen Füßen zusammen. Hoppla!`&");
							$sql="INSERT INTO commentary (postdate,section,author,comment) VALUES (now(),'".$item_hook_info['section']."',".$session[user][acctid].",'/me `@hat in wilder Raserei eine Strohpuppe zertrümmert!')";
							db_query($sql) or die(db_error(LINK));
							
							item_delete( 'id='.$item['id'] );


							output("`n`n`^Du hast einen neuen Schlag gelernt! Dein Angriff erhöht sich um `@2 Punkte `^!");
							$session['user']['attack']+=2;
						 } else {
							output ("`&mit vielen wilden Schlägen. `^Die Puppe gibt leise knirschende Geräusche von sich und kippt fast um.`&");
							$dam=$item[value2]+1;
							
							item_set( 'id='.$item['id'] , array('value2'=>$dam) );
													
						}
					break;
				}
			} else
				{ output ("`&So gern du $desc auch mit deiner Waffe perforieren willst, fühlst du dich leider viel zu müde dafür...");}
			
			addnav($item_hook_info['back_msg'],$item_hook_info['back_link']);
			
			break;
			
	}
		
	
}

?>