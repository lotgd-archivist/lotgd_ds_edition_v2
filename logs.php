<?php

// 09072004

/*

View user mail
Find multi accounts and cheaters 

by anpera

*/

	
require_once("common.php");

page_header("Multi");

if ($_GET['op']=='multi') {
	if (!empty($_POST['setupban']) && count($_POST['userid'])>0) {
		
		$str_lnk = 'su_bans.php?op=edit_ban&ids[]='.implode('&ids[]=',$_POST['userid']).'&ret='.urlencode('logs.php?op=multi');
	
		redirect($str_lnk);
		
	}
	elseif (!empty($_POST['deleteuser']) && count($_POST['userid'])>0) {
	
		$str_lnk = 'su_delete.php?ids[]='.implode('&ids[]=',$_POST['userid']).'&ret='.urlencode('logs.php?op=multi');
	
		redirect($str_lnk);
			
	}
	
	else { output('`n'); }


	$in_ip = $in_id = '';
	if ($_GET['searchby']!='id') {
		$sql = 'SELECT lastip FROM accounts WHERE lastip!="" GROUP BY lastip HAVING COUNT(*) > 1';
		$result = db_query($sql) or die(db_error(LINK));
		while ($row = db_fetch_assoc($result)) {
			$in_ip .= ',"'.$row['lastip'].'"';
		}
	}
	if ($_GET['searchby']!='ip') {
		$sql = 'SELECT uniqueid FROM accounts WHERE uniqueid!="" GROUP BY uniqueid HAVING COUNT(*) > 1';
		$result = db_query($sql) or die(db_error(LINK));
		while ($row = db_fetch_assoc($result)) {
			$in_id .= ',"'.$row['uniqueid'].'"';
		}
	}
	
	$minaccs = ($_POST['minaccs']) ? $_POST['minaccs'] : $_GET['minaccs'];
	$minaccs = ($minaccs) ? $minaccs : 3;
	
	$ip = $id = $users = array();
	$sql = 'SELECT a.acctid,name,lastip,uniqueid,dragonkills,level,laston,referer,guildid 
			FROM accounts a
			LEFT JOIN account_extra_info aei USING(acctid) 
			WHERE (lastip IN (-1'.$in_ip.') OR uniqueid IN (-1'.$in_id.')) AND locked="0" ORDER BY dragonkills ASC, level ASC';
	$result = db_query($sql) or die(db_error(LINK));
	while ($row = db_fetch_assoc($result)) {
		if ((!isset($id[$row['uniqueid']]) || $_GET['searchby']=='ip') && (!isset($ip[$row['lastip']]) || $_GET['searchby']=='id')) {
			if ($_GET['searchby']!='id') $ip[$row['lastip']] = count($users);
			if ($_GET['searchby']!='ip') $id[$row['uniqueid']] = count($users);
			$users[] = array($row);
		}
		elseif (isset($id[$row['uniqueid']])) {
			$ip[$row['lastip']] = $id[$row['uniqueid']];
			$users[$id[$row['uniqueid']]][] = $row;
		}
		else {
			$id[$row['uniqueid']] = $ip[$row['lastip']];
			$users[$ip[$row['lastip']]][] = $row;
		}
	}
	
	addnav("","logs.php?op=multi&searchby=".$_GET['searchby']);
	
	output('`n`bMultiaccounts`b`nNaaa, wer spielt denn hier noch wen?`n`n');
	output('<form method="POST" action="logs.php?op=multi&searchby='.$_GET['searchby'].'">Spieler mit ',true);
	output('<select onchange="this.form.submit()" name="minaccs" size="1">',true);
	output('<option value="2" '.(($minaccs==2)?'selected="selected"':'').'>2</option>',true);
	output('<option value="3" '.(($minaccs==3)?'selected="selected"':'').'>3</option>',true);
	output('<option value="4" '.(($minaccs==4)?'selected="selected"':'').'>4</option>',true);
	output('</select></form>',true);
	output(' oder mehr Multiaccounts suchen nach: ');
	
	if ($_GET['searchby']!='ip') {
		output('<a href="logs.php?op=multi&searchby=ip&minaccs='.$minaccs.'">IP</a> ',true);
		addnav('','logs.php?op=multi&searchby=ip&minaccs='.$minaccs);
	}
	else output('`&`bIP`b`0 ');
	if ($_GET['searchby']!='id') {
		output('<a href="logs.php?op=multi&searchby=id&minaccs='.$minaccs.'">ID</a> ',true);
		addnav('','logs.php?op=multi&searchby=id&minaccs='.$minaccs);
	}
	else output('`&`bID`b`0 ');
	if (!empty($_GET['searchby'])) {
		output('<a href="logs.php?op=multi&searchby=&minaccs='.$minaccs.'">Beidem</a> ',true);
		addnav('','logs.php?op=multi&searchby=&minaccs='.$minaccs);
	}
	else output('`&`bBeidem`b`0 ');
	
	$counter = 0;
			
	output('<table><tr><td>',true);
	foreach ($users AS $list) {
		if (count($list)<$minaccs) continue;
		$tmpstr = $linkstr =  '';
		$ips = $ids = $accts = array();
		foreach ($list AS $this) {
			$tmpstr .= ('<tr><td><input type="checkbox" name="userid[]" value="'.$this['acctid'].'"><input type="hidden" name="multi_id[]" value="'.$this['acctid'].'"></td>
							<td>'.$this['acctid'].'</td>
							<td>'.$this['name'].'</td>
							<td>'.$this['lastip'].'</td>
							<td>'.$this['uniqueid'].'</td>
							<td>'.$this['dragonkills'].'</td>
							<td>'.$this['level'].'</td>
							<td>'.$this['laston'].'</td>
							<td>'.$this['referer'].'</td>
							<td>'.$this['guildid'].'</td>
							</tr>');
			$linkstr .= '&multi_id[]='.$this['acctid'];
			$counter++;
		}
		output('<form action="logs.php?op=multi&searchby='.$_GET['searchby'].'" method="post">',true);
		addnav('','logs.php?op=multi&searchby='.$_GET['searchby']);
		output("<table align='center' class='input' width='100%'><tr><td>&nbsp;</td>
						<td>`bAcctID`b</td>
						<td>`bName`b</td>
						<td>`bIP`b</td>
						<td>`bID`b</td>
						<td>`bDK`b</td>
						<td>`bLevel`b</td>
						<td>`bZuletzt da`b</td>
						<td>`bGew. von`b</td>
						<td>`bG-ID`b</td>
						</tr>",true);
		output($tmpstr,true);
		
		$linkstr = 'multi.php?ret='.URLEncode('logs.php?op=multi&searchby='.$_GET['searchby'].'&minaccs='.$minaccs).$linkstr;
		
		output('<tr><td colspan="6" align="left">
						<input type="submit" name="deleteuser" value="löschen">
						<input type="submit" name="setupban" value="Accounts bannen">
						<a href="'.$linkstr.'">Analyse</a>
					</td></tr>',true);
		output('</table>`n`n',true);
		output('</form>',true);
		addnav('',$linkstr);
	}
	output('</td></tr></table>',true);
	output('`b'.$counter.'`b Multis`n');
	addnav('Aktualisieren','logs.php?op=multi&searchby='.$_GET['searchby'].'&minaccs='.$minaccs);
	addnav('Zurück','logs.php');
}
else{
	output("`n`nDie 5 letzten Systemmails:`n`n",true);
	$sql = "SELECT mail.*,accounts.name AS empfaenger FROM mail LEFT JOIN accounts ON accounts.acctid=mail.msgto WHERE msgfrom=0 ORDER BY sent DESC LIMIT 5";
	$result = db_query($sql) or die(db_error(LINK));
	output("<table align='center'><tr><td>`bDatum`b</td><td>`bEmpfänger`b</td><td>`bBetreff`b</td></tr>",true);
	for ($i=0;$i<db_num_rows($result);$i++){
		$row = db_fetch_assoc($result);
		output("<tr><td>$row[sent]</td><td>$row[empfaenger]</td><td>$row[subject]</td></tr>",true);
	}
	output("</table>`n",true);
	addnav("Usermails","logs.php?op=mail");
	addnav("Multiaccounts","logs.php?op=multi");
	addnav("Aktualisieren","logs.php");
}
addnav("Zurück zur Grotte","superuser.php");
addnav("Zurück zum Weltlichen","village.php");
output("`n<div align='right'>`)2004 by anpera & Chaosmaker</div>",true);
page_footer();
?>
