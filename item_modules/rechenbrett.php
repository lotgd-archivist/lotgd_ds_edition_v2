<?php

function rechenbrett_hook_process ( $item_hook , &$item ) {
	
	global $session,$item_hook_info;
	
	switch ( $item_hook ) {
		
		case 'furniture':
			
			if($_GET['act'] == '') {
									
				$sql = "SELECT * FROM houses WHERE houseid=".$session[housekey]." ORDER BY houseid DESC";
				$result = db_query($sql) or die(db_error(LINK));
				$row = db_fetch_assoc($result);
				
				output("Du begibst dich zum Rechenbrett und beginnst fleißig die bunten Kügelchen hin und her zu schieben, um dir einen Überblick über das Edelstein- und Goldguthaben deiner Mitbewohner zu verschaffen.`n`n");
				output("<table border='0'><tr><td valign='top'>",true);
				
				$sql = "SELECT keylist.*,accounts.acctid AS aid,accounts.name AS besitzer FROM keylist LEFT JOIN accounts ON accounts.acctid=keylist.owner WHERE value1=$row[houseid] ORDER BY id ASC";
				$result = db_query($sql) or die(db_error(LINK));
				
				output("<table border='0' cellpadding='4' cellspacing='1'><tr><td>Name</td><td>Schlüssel</td><td>Gold</td><td>Edelsteine</td><td>Aktionen</td></tr>",true);
				$lst=1;
				
				$result = db_query($sql) or die(db_error(LINK));
				
				for ($i=1;$i<=db_num_rows($result);$i++){
					$item = db_fetch_assoc($result);
					if ($item[owner]<>$row[owner]) {
												
					   if ($item[besitzer]=="") { $stat="`4verloren`5";} else {$stat="vorhanden";}
						output("<tr class='".($lst%2?"trlight":"trdark")."'><td>$lst: $item[besitzer]</td><td>$stat</td><td>",true);
				if ($item[gold]>=0) { output("`@"); } else { output ("`$"); }
				output("$item[gold]</td><td>",true);
				if ($item[gems]>=0) { output("`@"); } else { output ("`$"); }
				output("$item[gems]</td><td>",true);
				
				if ($session['user']['housekey']==$row['houseid']) {
				output("<a href='".$item_hook_info['link']."&act=reset&who=$item[owner]&hid=$row[houseid]'>Reset</a></td><td>",true);
				addnav("",$item_hook_info['link']."&act=reset&who=$item[owner]&hid=$row[houseid]");
				
				if ($item[chestlock]==0) {
				output("<a href='".$item_hook_info['link']."&act=lock&who=$item[owner]&hid=$row[houseid]'>Sperren</a></td></tr>",true);
				addnav("",$item_hook_info['link']."&act=lock&who=$item[owner]&hid=$row[houseid]");
				} else {
				output("<a href=".$item_hook_info['link']."&act=unlock&who=$item[owner]&hid=$row[houseid]>Entsperren</a></td></tr>",true);
				addnav("",$item_hook_info['link']."&act=unlock&who=$item[owner]&hid=$row[houseid]");
				} } else output("</tr>",true);
				
				$lst+=1;
						} }
				
				output("</table>",true);
				
						output("</td><td valign='top'>",true);
						output("</td></tr></table>",true);
						
						
				}
			
			
			else if ($_GET[act]=="reset"){
				$sql = "UPDATE keylist SET gold=0,gems=0 WHERE owner = $_GET[who] AND value1 = $_GET[hid]";
				db_query($sql) or die(sql_error($sql));
				redirect($item_hook_info['link']);
			}
			else if ($_GET[act]=="lock"){
				$sql = "UPDATE keylist SET chestlock=1 WHERE owner = $_GET[who] AND value1 = $_GET[hid]";
				db_query($sql) or die(sql_error($sql));
				redirect($item_hook_info['link']);
			}
			else if ($_GET[act]=="unlock"){
				$sql = "UPDATE keylist SET chestlock=0 WHERE owner = $_GET[who] AND value1 = $_GET[hid]";
				db_query($sql) or die(sql_error($sql));
				redirect($item_hook_info['link']);
			}
			
			addnav($item_hook_info['back_msg'],$item_hook_info['back_link']);
			
			break;
			
	}
		
	
}

?>