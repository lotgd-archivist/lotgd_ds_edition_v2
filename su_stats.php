<?php
/**
* su_stats.php: Statistiken für die Superuser
* @author talion <t@ssilo.de>
* @version DS-E V/2
*/

$str_filename = basename(__FILE__);
require_once('common.php');
su_check(SU_RIGHT_STATS,true);

$arr_stats = array(		 'onlinetime'=>array('name'=>'Onlinezeit','field'=>'onlinetime','desc'=>'Unermüdlich, einfach unermüdlich!')
						,'mailsent'=>array('name'=>'Mails ausgehend','field'=>'mailsent','desc'=>'Die fleißigsten Mailschreiber:')						
						,'mailrec'=>array('name'=>'Mails eingehend','field'=>'mailreceived','desc'=>'Diese Spieler empfangen die meisten Mails:')						
						,'comments'=>array('name'=>'Kommentare','field'=>'comments','desc'=>'Die aktivsten Schreiberlinge:')
						,'commentlength'=>array('name'=>'Kommentarlänge','field'=>'commentlength','desc'=>'Diese Spieler schreiben die längsten Kommentare (Gesamtzeichenzahl):')						
						,'pvpkilled'=>array('name'=>'PvP-Tode','field'=>'pvpkilled','desc'=>'Beliebteste Opfer unter den PvPlern:')						
						,'pvpkills'=>array('name'=>'PvP-Kills','field'=>'pvpkills','desc'=>'Spieler mit den meisten siegreichen, aktiven PvP-Kämpfen:')						
						,'turns_not_used'=>array('name'=>'Rundenverbrauch','field'=>'turns_not_used','desc'=>'Diese Spieler lassen die meisten Waldkämpfe ungenutzt verstreichen:')						
					);

function stats_nav () {
	
	global $arr_stats,$str_filename;
	
	if(su_check(SU_RIGHT_DEV)) {
		addnav('Aktionen');
		addnav('`$Reset',$str_filename.'?op=reset');
	}
	
	addnav('Statistiken');
	
	foreach($arr_stats as $what=>$stat) {
		
		addnav($stat['name'], $str_filename . '?op=showstats&what='.$what );
		
	}
	
	addnav('Skins', $str_filename . '?op=skins' );
	addnav('Adressbuch-Einträge', $str_filename . '?op=addr' );
	addnav('Versch. Accountinfos', $str_filename . '?op=accountinfo' );
	addnav('Aufstellungen zu Werten', $str_filename . '?op=stats_ext' );
	addnav('Letzter Login', $str_filename . '?op=lastlogin' );
	addnav('Top-Referer', $str_filename . '?op=topref' );
	addnav('Runen', $str_filename . '?op=runes' );
	
}

page_header('Statistisches & Interessantes aus '.getsetting('townname','Atrahor'));

output('`c`b`&Statistik`&`b`c`n`n');

// Grundnavi erstellen
addnav('Zurück');
addnav('G?Zur Grotte','superuser.php');
addnav('W?Zum Weltlichen',$session['su_return']);
// END Grundnavi erstellen

// Evtl. Fehler / Erfolgsmeldungen anzeigen
if($session['message'] != '') {
	output('`n`b'.$session['message'].'`b`n`n');
	$session['message'] = '';
}
// END Evtl. Fehler / Erfolgsmeldungen anzeigen

output('`^Das statistische Amt '.getsetting('townname','Atrahor').'s zeichnet seit `b'.date('d. m. Y H:i:s',strtotime(getsetting('stats_start','2006-04-08 23:23:23')) ).'`b die folgenden Daten auf:`n`&');

// MAIN SWITCH
$op = ($_REQUEST['op'] ? $_REQUEST['op'] : '');

switch($op) {
	
	// Standardansicht
	case '':	
		
		output('`^Bitte wählen.');
							
		stats_nav();	
					
		break;
	
	// Statistiken anzeigen
	case 'showstats':
		
		stats_nav();
		
		$str_what 	= $_GET['what'];
		$int_amount = (int)$_GET['amount'];
		$int_amount = ($int_amount == 0 ? 25 : $int_amount);
		$bool_orderrev = (bool)$_GET['orderrev'];
		$str_out = '';
		
		if(!isset($arr_stats[$str_what])) {
			output('`$Eine solche Statistik gibt es nicht!');
			page_footer();
			exit;
		}
		
		$arr_stat = $arr_stats[$str_what];
		
		// Statistik abrufen
		$sql = 'SELECT 		a.name,a.login,a.acctid, a_s.'.$arr_stat['field'].' AS val
				FROM		accounts a
				LEFT JOIN	account_stats a_s
					USING	(acctid)
				ORDER BY	a_s.'.$arr_stat['field'].' '.($bool_orderrev ? 'ASC' : 'DESC').'
				LIMIT		'.$int_amount;
		$res = db_query($sql);
		
		// Gesamtanzahl abrufen
		$sql = 'SELECT 		SUM(a_s.'.$arr_stat['field'].') AS val_ges
				FROM		account_stats a_s';
		$arr_sum = db_fetch_assoc(db_query($sql));
		$int_sum = $arr_sum['val_ges'];
								
		// Introtext
		$str_out .= '`&'.$arr_stat['desc'].'`n`n';
		
		$str_out .= 'Gesamt: '.number_format($int_sum).'`n`n';
		
		// Tabelle
		$str_out .= '<table border="0">
						<tr class="trhead">
							<td>`bPlatz`b</td>
							<td>`bName`b</td>
							<td>`bWert`b</td>
						</tr>';
		
		// Inhalt
		$str_class 		= 'trlight';
		$int_counter 	= 1;
						
		while($s = db_fetch_assoc($res)) {
						
			$str_grafbar = grafbar($int_sum, $s['val'], 300,20);
			
			// Sonderformatierungen
			if($str_what == 'onlinetime') {
				$str_days = floor($s['val'] / 86400);
				$s['val'] %= 86400;
				$str_hours = floor($s['val'] / 3600);
				$s['val'] %= 3600;
				$str_mins = floor($s['val'] / 60);
				$s['val'] %= 60;
				
				$s['val'] = $str_days.' Tage, '.$str_hours.' Stunden, '.$str_mins.' Minuten';				
			}
			else {
				$s['val'] = number_format($s['val']);				
			}
			
			$str_out .= '<tr class="'.$str_class.'">
							<td>'.$int_counter	.'</td>
							<td>'.$s['name']	.'`&</td>
							<td>'.$str_grafbar.' `b'.$s['val'].'`b</td>	
						</tr>';
						
			$int_counter++;
		}		
		
		$str_out .= '</table>';
		
		output($str_out,true);
		
		addnav('Ergebnisse');	
		addnav('Zeige 25',$str_filename.'?op=showstats&what='.$str_what.'&amount=25');
		addnav('Zeige 50',$str_filename.'?op=showstats&what='.$str_what.'&amount=50');
		addnav('Zeige 75',$str_filename.'?op=showstats&what='.$str_what.'&amount=75');
		addnav('Zeige 100',$str_filename.'?op=showstats&what='.$str_what.'&amount=100');
		
	break;
	
	case 'addr':
		
		stats_nav();
		
		$int_amount = (int)$_GET['amount'];
		$int_amount = ($int_amount == 0 ? 25 : $int_amount);
		$bool_orderrev = (bool)$_GET['orderrev'];
		$str_out = '';
	
		// Statistik abrufen
		$sql = 'SELECT 		COUNT(player) AS anzahl, a.name 
				FROM 		yom_adressbuch 
				LEFT JOIN 	accounts a 
					ON 		a.acctid=player 
				GROUP BY player
				ORDER BY	anzahl '.($bool_orderrev ? 'ASC' : 'DESC').'
				LIMIT		'.$int_amount;
		$res = db_query($sql);
		
		// Gesamtanzahl abrufen
		$sql = 'SELECT 		COUNT(player) AS val_ges
				FROM		yom_adressbuch';
		$arr_sum = db_fetch_assoc(db_query($sql));
		$int_sum = $arr_sum['val_ges'];
								
		// Introtext
		$str_out .= '`&Diese Spieler tauchen am häufigsten in den Adressbüchern auf:`n`n';
		
		$str_out .= 'Gesamt: '.number_format($int_sum).'`n`n';
		
		// Tabelle
		$str_out .= '<table border="0">
						<tr class="trhead">
							<td>`bPlatz`b</td>
							<td>`bName`b</td>
							<td>`bWert`b</td>
						</tr>';
		
		// Inhalt
		$str_class 		= 'trlight';
		$int_counter 	= 1;
						
		while($s = db_fetch_assoc($res)) {
						
			$str_grafbar = grafbar($int_sum, $s['anzahl'], 300,20);
			
			$s['anzahl'] = number_format($s['anzahl']);				
						
			$str_out .= '<tr class="'.$str_class.'">
							<td>'.$int_counter	.'</td>
							<td>'.$s['name']	.'`&</td>
							<td>`b'.$s['anzahl'].'`b</td>	
						</tr>';
						
			$int_counter++;
		}		
		
		$str_out .= '</table>';
		
		output($str_out,true);
		
		addnav('Ergebnisse');	
		addnav('Zeige 25',$str_filename.'?op=addr&amount=25');
		addnav('Zeige 50',$str_filename.'?op=addr&amount=50');
		addnav('Zeige 75',$str_filename.'?op=addr&amount=75');
		addnav('Zeige 100',$str_filename.'?op=addr&amount=100');
		
	break;
	
	case 'skins':
		
		stats_nav();
		
		$str_out = '';
						
		// Statistik abrufen
		$sql = 'SELECT 		a.prefs
				FROM		accounts a';
		$res = db_query($sql);
		
		$arr_skins = array();
		$int_sum = 0;
		
		while($s = db_fetch_assoc($res)) {
			
			$arr_prefs = unserialize($s['prefs']);
			$arr_skins[$arr_prefs['template']]++; 
			$int_sum++;
				
		}
		
		arsort($arr_skins);
									
		// Introtext
		$str_out .= '`&'.$arr_stat['desc'].'`n`n';
		
		$str_out .= 'Gesamt: '.number_format($int_sum).'`n`n';
		
		// Tabelle
		$str_out .= '<table border="0">
						<tr class="trhead">
							<td>`bPlatz`b</td>
							<td>`bSkin`b</td>
							<td>`bAnzahl`b</td>
						</tr>';
		
		// Inhalt
		$str_class 		= 'trlight';
		$int_counter 	= 1;
						
		foreach($arr_skins as $name=>$val) {
			
			$name = (!empty($name) ? $name : 'Unbekannt');
						
			$str_grafbar = grafbar($int_sum, $val, 300,20);
			
			$val = number_format($val);				
						
			$str_out .= '<tr class="'.$str_class.'">
							<td>'.$int_counter	.'</td>
							<td>'.$name	.'`&</td>
							<td>'.$str_grafbar.' `b'.$val.'`b</td>	
						</tr>';
						
			$int_counter++;
		}		
		
		$str_out .= '</table>';
		
		output($str_out,true);
		
	break;
	
	case 'stats_ext':
		stats_nav();
	
		output('Erweiterte Statistiken:`n`n');

		$sql = 'SELECT dragonkills,gold,goldinbank,gems,maxhitpoints,attack,defence,level,age FROM accounts ORDER by dragonkills ASC, level ASC';
		$res = db_query($sql);
		
		$accounts_number = db_num_rows($res);
		$dks_ges = 0;
		$gold_ges = 0;
		$gems_ges = 0;
		
		while($a = db_fetch_assoc($res)) {
			
			if($a['dragonkills'] == 0) {$k = '0';}
			elseif($a['dragonkills'] > 0 && $a['dragonkills'] < 5) {$k = '1 - 5';}
			elseif($a['dragonkills'] >= 5 && $a['dragonkills'] < 10) {$k = '5 - 9';}
			elseif($a['dragonkills'] >= 10 && $a['dragonkills'] < 20) {$k = '10 - 19';}
			elseif($a['dragonkills'] >= 20 && $a['dragonkills'] < 40) {$k = '20 - 39';}
			elseif($a['dragonkills'] >= 40 && $a['dragonkills'] < 70) {$k = '40 - 69';}
			elseif($a['dragonkills'] >= 70 && $a['dragonkills'] < 100) {$k = '70 - 99';}
			elseif($a['dragonkills'] >= 100) {$k = '100 - x';}
		
			$data[$k]['dkdata']['gold'] += $a['gold'] + $a['goldinbank'];
			$data[$k]['dkdata']['gems'] += $a['gems'];
			$data[$k]['dkdata']['maxhitpoints'] += $a['maxhitpoints'];
			$data[$k]['dkdata']['attack'] += $a['attack'];
			$data[$k]['dkdata']['defence'] += $a['defence'];
			$data[$k]['dkdata']['age'] += $a['age'];
			$data[$k]['dkdata']['counter']++;
			
			if($a['level'] == 1) {$lk = '1';}
			elseif($a['level'] == 2) {$lk = '2';}
			elseif($a['level'] >= 3 && $a['level'] < 5) {$lk = '3 - 4';}
			elseif($a['level'] >= 5 && $a['level'] < 7) {$lk = '5 - 6';}
			elseif($a['level'] >= 7 && $a['level'] < 11) {$lk = '7 - 10';}
			elseif($a['level'] >= 11 && $a['level'] < 15) {$lk = '11 - 14';}
			elseif($a['level'] == 15) {$lk = '15';}
					
			$data[$k]['levels'][$lk]['gold'] += $a['gold'] + $a['goldinbank'];
			$data[$k]['levels'][$lk]['gems'] += $a['gems'];
			$data[$k]['levels'][$lk]['maxhitpoints'] += $a['maxhitpoints'];
			$data[$k]['levels'][$lk]['attack'] += $a['attack'];
			$data[$k]['levels'][$lk]['defence'] += $a['defence'];
			$data[$k]['levels'][$lk]['age'] += $a['age'];
			$data[$k]['levels'][$lk]['counter']++;
			
			$dks_ges += $a['dragonkills'];
			$gold_ges += $a['gold'];
			$gems_ges += $a['gems'];
			
		}
		
		output('<table cellspacing="3" cellpadding="3"><tr class="trhead"><td>DKs</td><td>Level</td><td>Anzahl abs./ %</td><td>Gold ges./durchschn.</td><td>Gems ges./durchschn.</td><td>LP durchschn.</td><td>Angriff durchschn.</td><td>Def durchschn.</td><td>Alter durchschn.</td></tr>',true);
		
		$class = 'trlight';
		
		foreach($data as $dk => $info) {
			
			$info['dkdata']['gold_avg'] = round($info['dkdata']['gold'] / $info['dkdata']['counter']);
			$info['dkdata']['gems_avg'] = round($info['dkdata']['gems'] / $info['dkdata']['counter']);
			$info['dkdata']['maxhitpoints_avg'] = round($info['dkdata']['maxhitpoints'] / $info['dkdata']['counter']);
			$info['dkdata']['defence_avg'] = round($info['dkdata']['defence'] / $info['dkdata']['counter']);
			$info['dkdata']['attack_avg'] = round($info['dkdata']['attack'] / $info['dkdata']['counter']);
			$info['dkdata']['age_avg'] = round($info['dkdata']['age'] / $info['dkdata']['counter']);
			
			$info['dkdata']['num_rel'] = round(($info['dkdata']['counter'] / $accounts_number) * 100);
			
			output('<tr class="trhead"><td>`b'.$dk.' DKs`b</td><td>Alle</td>
			<td>'.$info['dkdata']['counter'].' / '.$info['dkdata']['num_rel'].' %</td>
			<td>'.$info['dkdata']['gold'].' / '.$info['dkdata']['gold_avg'].'</td>
			<td>'.$info['dkdata']['gems'].' / '.$info['dkdata']['gems_avg'].'</td>
			<td>'.$info['dkdata']['maxhitpoints_avg'].'</td>
			<td>'.$info['dkdata']['attack_avg'].'</td>
			<td>'.$info['dkdata']['defence_avg'].'</td>
			<td>'.$info['dkdata']['age_avg'].'</td>
			</tr>'
			,true);
			
			ksort($info['levels'],SORT_NUMERIC);
			
			foreach($info['levels'] as $lvl => $i) {
						
				$i['gold_avg'] = round($i['gold'] / $i['counter']);
				$i['gems_avg'] = round($i['gems'] / $i['counter']);
				$i['maxhitpoints_avg'] = round($i['maxhitpoints'] / $i['counter']);
				$i['defence_avg'] = round($i['defence'] / $i['counter']);
				$i['attack_avg'] = round($i['attack'] / $i['counter']);
				$i['age_avg'] = round($i['age'] / $i['counter']);
				
				$i['num_rel'] = round( ($i['counter'] / $accounts_number) * 100);
				
				output('<tr class="'.$class.'"><td> - </td><td>'.$lvl.'</td>
				<td>'.$i['counter'].' / '.$i['num_rel'].' %</td>
				<td>'.$i['gold'].' / '.$i['gold_avg'].'</td>
				<td>'.$i['gems'].' / '.$i['gems_avg'].'</td>
				<td>'.$i['maxhitpoints_avg'].'</td>
				<td>'.$i['attack_avg'].'</td>
				<td>'.$i['defence_avg'].'</td>
				<td>'.$i['age_avg'].'</td>
				</tr>'
				,true);
				
				$class = ($class == 'trlight'?'trdark':'trlight');
				
			}
				
		}
		
		output('</table>',true);
	break;
	
	case 'lastlogin':
		
		stats_nav();
		
		$sql = "SELECT count(*) AS c, substring(laston,1,10) AS d FROM accounts GROUP BY d DESC ORDER BY d DESC";
		$result = db_query($sql);
		output("`n`%`bDatum des letzten Logins:`b");
		$output.="<table border='0' cellpadding='0' cellspacing='5'>";
		$class="trlight";
		$odate=date("Y-m-d");
		$j=0;
		for ($i=0;$i<db_num_rows($result);$i++){
			$row = db_fetch_assoc($result);
			$diff = (strtotime($odate)-strtotime($row['d']))/86400;
			for ($x=1;$x<$diff;$x++){
				//if ($j%7==0) $class=($class=="trlight"?"trdark":"trlight");
				//$j++;
				$class=(date("W",strtotime("$odate -$x days"))%2?"trlight":"trdark");
				$output.="<tr class='$class'><td>".date("Y-m-d",strtotime("$odate -$x days"))."</td><td>0</td><td>$cumul</td></tr>";
			}
		//	if ($j%7==0) $class=($class=="trlight"?"trdark":"trlight");
		//	$j++;
			$class=(date("W",strtotime($row['d']))%2?"trlight":"trdark");
			$cumul+=$row['c'];
			$output.="<tr class='$class'><td>{$row['d']}</td><td><img src='images/trans.gif' width='{$row['c']}' border='1' height='5'>{$row['c']}</td><td>$cumul</td></tr>";
			$odate = $row['d'];
		}
		$output.="</table>";	
		
	break;
	
	case 'topref':
		
		stats_nav();
		
		output("`n`%`bTop Referers:`b`0`n");
		output("<table border='0' cellpadding='2' cellspacing='1' bgcolor='#999999'>",true);
		output("<tr class='trhead'><td><b>Name</b></td><td><b>Referrals</b></td></tr>",true);
		$sql = "SELECT count(*) AS c, acct.acctid,acct.name AS referer FROM account_extra_info aei 
				INNER JOIN accounts AS acct ON acct.acctid = aei.referer 
				WHERE aei.referer>0 GROUP BY aei.referer DESC ORDER BY c DESC";
		$result = db_query($sql);
		for ($i=0;$i<db_num_rows($result);$i++){
			$row = db_fetch_assoc($result);
			output("<tr class='".($i%2?"trdark":"trlight")."'><td>",true);
			output("`@{$row['referer']}`0</td><td>`^{$row['c']}:`0  ", true);
			$sql = "SELECT name,refererawarded FROM accounts
					LEFT JOIN account_extra_info USING(acctid) 
					WHERE referer = ${row['acctid']} ORDER BY accounts.acctid ASC";
			$res2 = db_query($sql);
			for ($j = 0; $j < db_num_rows($res2); $j++) {
				$r = db_fetch_assoc($res2);
				output(($r['refererawarded']?"`&":"`$") . $r['name'] . "`0");
				if ($j != db_num_rows($res2)-1) output(",");
			}
			output("</td></tr>",true);
		}
		output("</table>",true);
		
	break;
	
	case 'accountinfo':
		
		stats_nav();
		
		$sql = "SELECT sum(gentimecount) AS c, sum(gentime) AS t, sum(gensize) AS s, count(*) AS a FROM accounts";
		$result = db_query($sql);
		$row = db_fetch_assoc($result);
		output("`b`%Für existierende Accounts:`b`n");
		output("`@Accounts insgesamt: `^".number_format($row['a'])."`n");
		output("`@Treffer insgesamt: `^".number_format($row['c'])."`n");
		output("`@Seitengenerierungszeit insgesamt: `^".dhms($row['t'])."`n");
		output("`@Seitengenerierungsgröße insgesamt: `^".number_format($row['s'])."b`n");
		output("`@Durchschnittliche Seitengenerierungszeit: `^".dhms($row['t']/$row['c'],true)."`n");
		output("`@Durchschnittliche Seitengröße: `^".number_format($row['s']/$row['c'])."`n");
		
	break;
	
	case 'reset':
		
		if($_GET['act'] == 'ok') {
			
			$sql = 'UPDATE account_stats SET 
						logintime=0,onlinetime=0,comments=0,pvpkilled=0,pvpkills=0,mailsent=0,mailreceived=0,turns_not_used=0,commentlength=0';
			db_query($sql);
			
			if(!db_error(LINK)) {
				$session['message'] = '`@Statistiken erfolgreich zurückgesetzt!';
				savesetting('stats_start',date('Y-m-d H:i:s'));
				systemlog('Setzte die Statistiken zurück',$session['user']['acctid']);
			}
			else {
				$session['message'] = '`$Fehler bei Zurücksetzen!';
			}
			redirect($str_filename);
			
		}
		else {
			
			output('`$`bBist du dir sicher, die bisher gesammelten Daten verwerfen und die Erhebung neu beginnen zu wollen?`b`&');
			
			addnav('Nein, zurück',$str_filename);
			addnav('Ja, Reset!',$str_filename.'?op=reset&act=ok');
			
		}
		
	break;
	
	
	
	case 'runes':
		require_once(LIB_PATH.'runes.lib.php'); 
		stats_nav();
		$res = db_query('SELECT id, name, tpl_id FROM '.RUNE_EI_TABLE);
		$str_out .= 'Runen`n`n<table>';
		$str_out .= '<tr class="trhead"><td>ID</td><td>Name</td><td>ident</td><td>unident</td><td>gesamt</td></tr>';
		$summe = 0;
		while( ($rune = db_fetch_assoc($res)) ){
			$class =( $class == 'trdark' ? 'trlight' : 'trdark');
			$ident = item_count('tpl_id="'.$rune['tpl_id'].'"');
			$unident = item_count('tpl_id="'.RUNE_DUMMY_TPL.'" AND value2='.$rune['id']);
			$summe += $ident+$unident;
			$str_out .= '<tr class="'.$class.'"><td>'.$rune['id'].'</td><td>'.$rune['name'].'</td><td>'.$ident.'</td><td>'.$unident.'</td><td>`b'.($ident+$unident).'`b</td></tr>';
		}
		$class =( $class == 'trdark' ? 'trlight' : 'trdark');
		$str_out .= '<tr class="'.$class.'"><td align="right" colspan="4">`bGesamt:`b</td><td>`b'.$summe.'`b</td></tr>';
		$str_out .= '</table>';
		output($str_out);
	break;
	
	// Hm..		
	default:
		output('Was hast du denn HIER verloren?! Op: '.$op);	
		addnav('Zurück',$str_filename . '');
	break;
}


page_footer();
?>
