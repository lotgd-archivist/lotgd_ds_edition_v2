<?php

function kpuppe_hook_process ( $item_hook , &$item ) {
	
	global $session,$item_hook_info;
	
	switch ( $item_hook ) {
		
		case 'furniture':
			
			if($_GET['act'] == '') {
					  
				output("`&Diese Puppe kann, wenngleich sie auch übel riecht, ein treuer Freund in schweren Zeiten sein, wenn man sich nur gut genug um sie kümmert.`nEtwas ganz Besonderes liegt auf diesem aus zusammengenähtem Fleisch bestehenden Ding. Du weißt nicht was es ist, aber es scheint dir gleichsam Kraft zu nehmen wie zu geben.`nVielleicht solltest du dich einmal etwas näher mit der Puppe befassen.`n`n");
	
				$capacity=round($item['hvalue']/25)+1;
				$left=$capacity-$item['value2'];
				output("Gespeicherte Kraft in der Puppe: ".$item[value2]."`n");
				output("Kapazität der Puppe: ".$capacity."`n");
				addnav("Kraft geben",$item_hook_info['link']."&act=puppegeben");
				addnav("Kraft nehmen",$item_hook_info['link']."&act=puppenehmen");
				
			}
			else if($_GET[act]=="puppegeben"){
				
				$capacity=round($item['hvalue']/25)+1;
				$left=$capacity-$item['value2'];
				
				output("Gespeicherte Kraft in der Puppe: ".$item[value2]."`n");
				output("Kapazität der Puppe: ".$capacity."`n`n");
				
				output("Wie viele Runden möchtest du in der Puppe speichern?`n`n");
				output("<form action='".$item_hook_info['link']."&act=puppegeben2' method='POST'><input name='trai' id='trai'><input type='submit' class='button' value='Runden opfern'></form>",true);
				output("<script language='JavaScript'>document.getElementById('trai').focus();</script>",true);
				addnav("",$item_hook_info['link']."&act=puppegeben2");
				
			}

			else if($_GET[act]=="puppegeben2"){
								
				$saved= $item['value2'];
				$capacity=round($item['hvalue']/25)+1;
				$left=$capacity-$item['value2'];
				
				$save = abs((int)$_GET[trai] + (int)$_POST[trai]);
				if ($session[user][turns] <= $save) $save = $session[user][turns];
				if ($save>$left) $save = $left;
				
				if ($save<=0) {
				  output("`&So sehr du dich bemühst, du bekommst keine Kraft in die Puppe hinein!`n");
				} else {
					output("`&Du speicherst `^$save`& Runden in der Kadaverpuppe.");
					$session['user']['turns']-=$save;
					$saved+=$save;
					
					item_set( ' id="'.$item['id'].'"' , array('value2'=>$saved) );
					
				}
				
			}

			else if ($_GET[act]=="puppenehmen"){
						
				$sql = "SELECT dollturns FROM account_extra_info WHERE acctid=".$session['user']['acctid']."";
				$result = db_query($sql) or die(db_error(LINK));
				$dturns = db_fetch_assoc($result);
				$turnsleft=$dturns['dollturns'];
				if ($turnsleft<=0)
				{
					output("`&Du warst heute schon oft genug an der Kadaverpuppe und ekelst dich einfach nur noch.`nVersuchs morgen nochmal!");
				}
				else {
					if ($session['user']['turns']<2 && $item['owner'] != $session['user']['acctid']) {
					  output("`&Heute hast du keine Kraft mehr um dich noch mit der Puppe zu befassen.`nDu solltest mindestens noch 2 Runden übrig haben bevor du dich dem stinkenden Kameraden hier widmest!`n");
				  	}
					else {
			
						$available=$item['value2'];
						$capacity=round($item['hvalue']/25)+1;
						if ($available==0) {
						  output("`&In der Puppe ist zur Zeit keine Kraft gespeichert!");
						}
						else
			  			{
							output("`&Du begibst dich zur Kadaverpuppe um die in ihr gespeicherte Kraft in dich aufzunehmen.`n");
							$chance=e_rand(1,5);
						   // Nachteil nicht für den Besitzer
						   if ($session['user']['acctid']==$item['owner']) { $chance+=2; }
							if ($chance==2)
				   			{
							 // Kann mal daneben gehen
							 output("`&Doch anstatt dir Kraft zu geben enzzieht dir die verfluchte Puppe diese sogar!`nDu reisst dich los und ziehst dich verschreckt zurück.`n`nDu `4verlierst 2 Waldkämpfe!`&");
							 $session['user']['turns']-=2;
							 $available+=2;
							 if ($available>$capacity) { $available=$capacity; }
							 
				   			}
				  			else
					 		// postivies Ergebnis
							 {
							   output("`&Du fühlst neue Kräfte in dich strömen und `@erhälst einen Waldkampf zurück!`&`n`n");
							   $session['user']['turns']++;
							   $available--;

							 }
			
					 $turnsleft--;
					 
					 item_set(' id='.$item['id'] , array('value2'=>$available) );
					 
					 $sql = "UPDATE account_extra_info SET dollturns=$turnsleft WHERE acctid=".$session['user']['acctid']."";
					 db_query($sql) or die(sql_error($sql));
			  		}
				}
			}
			}
			
			addnav($item_hook_info['back_msg'],$item_hook_info['back_link']);
			
			break;
			
	}
		
	
}

?>