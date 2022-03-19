<?php
/**
* su_bios.php: Userbiographien kontrollieren
* @author talion <t@ssilo.de>
* @version DS-E V/2
*/

$str_filename = basename(__FILE__);
require_once('common.php');

// Suchmaske
$arr_form = array(
					'account_id'		=>'AccountID ODER Login,int',
					'biotype'			=>'Biotyp,enum,0,Alle,1,Standardbios,2,Verlängerte Bios',
					'biolocked'			=>'Gesperrt?,enum,0,Alle,1,Nur ungesperrte,2,Nur gesperrte',
					'message'			=>'Stichwortsuche in Bios',
					'orderby'			=>'Sortieren nach,enum,biotime DESC,Letzter Edit absteigend,biotime ASC,Letzter Edit aufsteigend,acctid ASC,AcctID aufsteigend,acctid DESC,AcctID absteigend,name ASC,Name',
					'results_per_page'	=>'Ergebnisse pro Seite,enum,5,5,10,10,25,25,50,50,75,75,100,100'
					);
$arr_data = array(
					'account_id'		=> $_REQUEST['account_id'],
					'biotype'			=> (int)$_REQUEST['biotype'],
					'biolocked'			=> (int)$_REQUEST['biolocked'],
					'message'			=> $_REQUEST['message'],
					'orderby'			=> (empty($_REQUEST['orderby']) ? 'acctid ASC' : substr($_REQUEST['orderby'],0,12)),
					'results_per_page'	=> (empty($_REQUEST['results_per_page']) ? 5 : (int)$_REQUEST['results_per_page'])
					);
					
if( (int)$arr_data['account_id'] == 0 && !empty($arr_data['account_id']) ) {
	$arr_tmp = db_fetch_assoc(db_query('SELECT acctid FROM accounts WHERE login="'.$arr_data['account_id'].'" LIMIT 1'));
	if($arr_tmp['acctid'] > 0) {
		$arr_data['account_id'] = $arr_tmp['acctid'];
	}
	else {
		$arr_data['account_id'] = 0;
	}	
}
// END Suchmaske						

page_header('Bio-Check');

output('`c`b`&Bio - Kontrolle`&`b`c`n`n');

// Grundnavi erstellen
addnav('Zurück');

addnav('G?Zur Grotte','superuser.php');
addnav('W?Zum Weltlichen',$session['su_return']);

addnav('Aktionen');
addnav('Start',$str_filename);
// END Grundnavi erstellen
								


// Evtl. Fehler / Erfolgsmeldungen anzeigen
if($session['message'] != '') {
	output('`n`b'.$session['message'].'`b`n`n');
	$session['message'] = '';
}
// END Evtl. Fehler / Erfolgsmeldungen anzeigen

function show_bio_search () {
	
	global $str_filename,$arr_form,$arr_data,$str_type;
	
	$str_out = '';
	
	$str_lnk = $str_filename.'?op=search';
		
	addnav('',$str_lnk);
				
	$str_out .= '<form method="POST" action="'.$str_lnk.'">';
				
	// Suchmaske zeigen
	$str_out .= generateform($arr_form,$arr_data,false,'Suchen!');
			
	$str_out .= '</form><hr />';
	
	return($str_out);
	
}

// MAIN SWITCH
$str_op = ($_REQUEST['op'] ? $_REQUEST['op'] : '');

switch($str_op) {
	
	// Suchergebisse
	case 'search':	
				
		$str_baselnk = $str_filename . '?op=search&';
		foreach($arr_data as $key => $val) {
			$str_baselnk .= $key.'='.urlencode($val).'&';			
		}
		$str_baselnk .= 'page=';
		
		$str_has_long_bio = '(a.marks>='.CHOSEN_FULL.' OR aei.has_long_bio)';

		$str_where = 	'	WHERE 		1  	
										'.($arr_data['biotype'] == 1 	? ' AND !'.$str_has_long_bio : '').'
										'.($arr_data['biotype'] == 2 	? ' AND '.$str_has_long_bio : '').'
										'.($arr_data['biolocked'] == 1 	? ' AND aei.biotime < "'.BIO_LOCKED.'" ' : '').'
										'.($arr_data['biolocked'] == 2 	? ' AND aei.biotime = "'.BIO_LOCKED.'" ' : '').'
										'.($arr_data['account_id'] > 0 	? ' AND aei.acctid = '.$arr_data['account_id'] : '').'
										'.(!empty($arr_data['message'])	
											? ' AND IF( '.$str_has_long_bio.', aei.long_bio LIKE "%'.$arr_data['message'].'%", aei.bio LIKE "%'.$arr_data['message'].'%")' 
											: '');
		
		$str_count_sql = '	SELECT 		COUNT( * ) AS a
							FROM		account_extra_info aei
							LEFT JOIN	accounts a USING( acctid )'
							.$str_where;
		
		$str_data_sql = '	SELECT 		IF('.$str_has_long_bio.',long_bio,bio) AS bio, aei.biotime, aei.has_long_bio, aei.html_locked, 
										IF(a.marks>='.CHOSEN_FULL.',1,0) AS chosen, a.name, a.acctid
							FROM		account_extra_info aei
							LEFT JOIN	accounts a USING( acctid )'
							.$str_where.'										
							ORDER BY 	'.$arr_data['orderby'];
		
		$count = mysql_fetch_row(db_query($str_count_sql));
		
		$page = (int)$_REQUEST['page'];			
		$page = ($page == 0 ? 1 : $page);
			
		$from = ($page-1) * $arr_data['results_per_page'];
		
		$to = $page * $arr_data['results_per_page'];			
		$to = min($count[0],$to);
		$max_page = ceil($count[0] / $arr_data['results_per_page']);		
		
		// Navi erzeugen
		if($max_page) {
			addnav('Seiten');
			for($i=1; $i<=$max_page; $i++) {
				
				addnav( ($i == $page ? '`^' : '').'Seite '.$i, $str_baselnk.$i);
				
			}
		}	
		
		$str_data_sql .= ' LIMIT '.$from.','.$arr_data['results_per_page'];
		
		$str_out .= show_bio_search();			
				
		$str_out .= '`n`c<table cellpadding="2" cellspacing="1">
						';
		
		$str_tr_class = 'trlight';
		
		$res = db_query($str_data_sql);
		
		if(db_num_rows($res) == 0) {
			
			$str_out .= '`iKeine Ergebnisse gefunden!`i';
			
		}
		
		// Rechte ermitteln
		$bool_useredit = su_check(SU_RIGHT_EDITORUSER);
		$bool_lockhtml = su_check(SU_RIGHT_LOCKHTML);
		$bool_lockbio = $bool_lockhtml;
									
		// Ergebnisse zeigen
		while($l = db_fetch_assoc($res)) {
									
			$str_out .= '<tr class="trlight">
							<td><a name="#'.$l['acctid'].'"></a>`bID '.$l['acctid'].': '.$l['name'].
								' | '.( $l['biotime'] == BIO_LOCKED ? '`$gesperrt`0' : ($l['biotime'] != '0000-00-00 00:00:00' ? '`&'.date('d.m.Y H:i',strtotime($l['biotime'])) : '') ).'`b'
								.($bool_useredit ? ' - '.create_lnk('Usereditor','user.php?op=edit&userid='.$l['acctid']) : '')
								.($bool_lockhtml ? ' - '.create_lnk('HTML '.($l['html_locked'] ? 'entsperren' : 'sperren'),$str_filename.'?op=lock_html&uid='.$l['acctid'].'&ret='.urlencode($str_baselnk.$page)) : '')
								.($bool_lockbio ? ' - '.create_lnk('Bio '.($l['biotime'] == BIO_LOCKED ? 'entsperren' : 'sperren'),$str_filename.'?op=lock_bio&uid='.$l['acctid'].'&ret='.urlencode($str_baselnk.$page)) : '')
							.'</td>
						</tr>
						<tr>
							<td>`n';
			
			$l['bio'] = soap(closetags($l['bio'],'`b`c`i'));

			$allow_tags = ($l['html_locked'] ? '' : '<img>');
			
			$l['bio'] = strip_tags($l['bio'],$allow_tags);
												
			$str_out .= $l['bio'];
								
			$str_out .= '	</td>
						</tr>
						<tr><td>&nbsp;</td></tr>';
		
		}
		
		$str_out .= '</table>`c';
		// END Ergebnisse zeigen
		
		output($str_out, true);
		
	break;
	// END Suchergebnisse
	
	case 'lock_html':
		
		$int_uid = (int)$_GET['uid'];
		
		$arr_user = user_get_aei('html_locked', $int_uid);
		
		$arr_user['html_locked'] = ($arr_user['html_locked'] ? 0 : 1);
		
		user_set_aei( array('html_locked'=>$arr_user['html_locked']), $int_uid);
		
		if($arr_user['html_locked']) {
			systemmail($int_uid,'`$HTML gesperrt!`0','`@'.$session['user']['name'].'`& hat HTML für deine Bio deaktiviert. Wahrscheinlich hast du es mit der Nutzung von Bildern übertrieben. Wenn du dir nicht sicher bist, solltest du vielleicht mal in einer Mail nach dem Grund fragen.');
			systemlog('`qSperrung des Bio-HTML für:`0 ',$session['user']['acctid'],$int_uid);
			$session['message'] = '`$HTML für AcctID '.$int_uid.' gesperrt!';
		}
		else {
			systemmail($int_uid,'`@HTML entsperrt!`0','`@'.$session['user']['name'].'`& hat HTML für deine Bio wieder aktiviert.');
			systemlog('`qEntSperrung des Bio-HTML für:`0 ',$session['user']['acctid'],$int_uid);
			$session['message'] = '`@HTML für AcctID '.$int_uid.' entsperrt!';
		}
		redirect( urldecode($_GET['ret']) );
		
	break;
	
	case 'lock_bio':
		
		$int_uid = (int)$_GET['uid'];
		
		$arr_user = user_get_aei('biotime', $int_uid);
		
		$arr_user['biotime'] = ($arr_user['biotime'] == BIO_LOCKED ? '0000-00-00' : BIO_LOCKED);
		
		$str_biotxt = ($arr_user['biotime'] == BIO_LOCKED ? '`b`c`$Bio von der Administration gesperrt!`0`c`b' : '');
		
		user_set_aei( array('biotime'=>$arr_user['biotime'], 'bio'=>$str_biotxt, 'long_bio'=>$str_biotxt), $int_uid);
		
		if($arr_user['biotime'] == BIO_LOCKED) {
			systemmail($int_uid,'`$Bio gesperrt!`0','`@'.$session['user']['name'].'`& hat deine komplette Bio deaktiviert. Wahrscheinlich hast du unpassende oder gegen das Urheberrecht verstoßende Inhalte eingefügt! Wenn du dir nicht sicher bist, solltest du vielleicht mal in einer Mail nach dem Grund fragen.');
			systemlog('`qSperrung der Bio für:`0 ',$session['user']['acctid'],$int_uid);
			$session['message'] = '`$Bio für AcctID '.$int_uid.' gesperrt!';
		}
		else {
			systemmail($int_uid,'`@Bio entsperrt!`0','`@'.$session['user']['name'].'`& hat deine Bio wieder aktiviert.');
			systemlog('`qEntSperrung der Bio für:`0 ',$session['user']['acctid'],$int_uid);
			$session['message'] = '`@Bio für AcctID '.$int_uid.' entsperrt!';
		}
		redirect( urldecode($_GET['ret']) );
		
	break;
				
	// Hm..		
	default:
		redirect($str_filename. '?op=search');

	break;
}


page_footer();
?>
