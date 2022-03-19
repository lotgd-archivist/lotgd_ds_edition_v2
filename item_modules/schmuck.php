<?php

function schmuck_hook_process ( $item_hook , &$item ) {
	
	global $session,$item_hook_info;
	
	switch ( $item_hook ) {
		
		case 'furniture':
			
			$owner=$item['owner'];
			
			if($_GET['act'] == '') {
			
				if ($owner==$session['user']['acctid'])
				{			
					output("`&Du begibst dich zu deinem treuen Schmuckkästchen und öffnest es mit der nur dir bekannten Handbewegung. Dabei umgehst du geschickt die Fingerfalle und klappst es schliesslich auf.`nEs befinden sich `^".$item['hvalue']."`& Edelsteine darin.`n`nWas nun ?`n`n");
					addnav("Edelsteine verstauen",$item_hook_info['link']."&act=schmuckgeben&owner=$owner");
					addnav("Edelsteine nehmen",$item_hook_info['link']."&act=schmucknehmen&owner=$owner");
				}
				else
				{
					output("`&Ein wahrhaft hübsches Schmuckkästchen!`nLeider weißt du jedoch nicht wie man es öffnet, und die berüchtigte \"`4Finger dran-Finger ab!`&\"-Sicherung nimmt dir jede Motivation es dennoch zu probieren!`n`n");
				}
			}
						
			elseif ($_GET['act']=="schmuckgeben"){
						
				$capacity=25;
				$space=25-$item['hvalue'];
				output("`&Dein Schmuckkästchen fasst maximal `^25`& Edelsteine. Es befinden sich bereits `@".$dose['hvalue']."`& Edelsteine darin. Folglich kannst du noch `#".$space."`& Edelsteine hinein tun.`n`nWieviele möchtest du sicher verstauen?`n`n");
			
				output("<form action='".$item_hook_info['link']."&act=schmuckgeben2&owner=$owner' method='POST'><input name='trai' id='trai'><input type='submit' class='button' value='verstauen'></form>",true);
				output("<script language='JavaScript'>document.getElementById('trai').focus();</script>",true);
				addnav("",$item_hook_info['link']."&act=schmuckgeben2&owner=$owner");
				
			}
			
			elseif ($_GET['act']=="schmuckgeben2"){
	
				$saved= $item['hvalue'];
				$capacity=25;
				$left=25-$item['hvalue'];
				
				$save = abs((int)$_GET[trai] + (int)$_POST[trai]);
				
				if ($session[user][gems] <= $save) $save = $session[user][gems];
				if ($save>$left) $save = $left;
				if ($save<=0) {
					output("`&Du legst deinen unsichtbaren Edelstein in das Schmuckkästchen!`n");
				} else {
					output("`&Du legst `^$save`& Edelsteine in dein Schmuckkästchen.");
					$session['user']['gems']-=$save;
					$saved+=$save;
					$item['hvalue'] = $saved;
					item_set(' id='.$item['id'], $item);
										
				}
			
			}
			
			elseif ($_GET['act']=="schmucknehmen"){
			
				$left=$item['hvalue'];
				if ($left<=0) {
					output("`&Dein Schmuckkästchen ist genauso leer wie dein Kopf!`n`n");
				} else {
					output("`&Wieviele Edelsteine willst du nehmen?`n`n");
					output("<form action='".$item_hook_info['link']."&act=schmucknehmen2&owner=$owner' method='POST'><input name='trai' id='trai'><input type='submit' class='button' value='entnehmen'></form>",true);
					output("<script language='JavaScript'>document.getElementById('trai').focus();</script>",true);
					addnav("",$item_hook_info['link']."&act=schmucknehmen2&owner=$owner");
				}
	
			}
			
			elseif ($_GET['act']=="schmucknehmen2"){
	
				$left = $item['hvalue'];
			
				$take = abs((int)$_GET[trai] + (int)$_POST[trai]);
				if ($take>$left) $take = $left;
				if ($take<=0) {
					output("`&Du findest, dass die Edelsteine dort gut aufgehoben sind und nimmst erstmal keinen heraus!`n");
				} else {
					output("`&Du nimmst `^$take`& Edelsteine aus deinem Schmuckkästchen.");
					$session['user']['gems']+=$take;
					$left-=$take;
					$item['hvalue'] = $left;
					item_set(' id='.$item['id'], $item);
				}

			}
						
			addnav($item_hook_info['back_msg'],$item_hook_info['back_link']);
			
			break;
			
	}
		
	
}

?>