<?
/*-------------------------------/
Name: stats_ext.php
Autor: tcb / talion für Drachenserver (mail: t@ssilo.de)
Erstellungsdatum: 9/05
Beschreibung:	Stellt genaue Statistiken zu versch. Charwerten gruppiert nach DK und Level dar
/*-------------------------------*/

require_once "common.php";

page_header("Erweiterte Stats");
addnav("G?Zurück zur Grotte","superuser.php");


addnav('W?Zurück zum Weltlichen',$session['su_return']);
addnav("Aktualisieren","stats_ext.php");

output('`c`bErweiterte Statistiken`b`c`n`n');

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

page_footer();
?>
