<?php
/**
* su_mails.php: Usermails kontrollieren
* @author talion <t@ssilo.de>
* @version DS-E V/2
*/

$str_filename = basename(__FILE__);
require_once('common.php');

su_check(SU_RIGHT_MAILBOX,true);

// Max. Zeit in Sekunden, die ein Logeintrag hält
define('MAX_LOG_TIME',3600);

// Suchmaske
$arr_form = array(
					'from_id'			=>'SenderID ODER Login,int',
					'to_id'				=>'EmpfängerID ODER Login,int',
					'mailtype'			=>'Mailtyp,enum,0,Alle,1,Systemmails,2,Usermails',
					'state'				=>'Status,enum,0,Alle,1,Gelesen,2,Ungelesen'
					);
$arr_data = array(
					'from_id'			=> $_REQUEST['from_id'],
					'to_id'				=> $_REQUEST['to_id'],
					'mailtype'			=> (int)$_REQUEST['mailtype'],
					'state'				=> (int)$_REQUEST['state'],
					'message'			=> $_REQUEST['message'],
					'results_per_page'	=> (empty($_REQUEST['results_per_page']) ? 50 : (int)$_REQUEST['results_per_page'])
					);
					
if( (int)$arr_data['from_id'] == 0 && !empty($arr_data['from_id']) ) {
	$arr_tmp = db_fetch_assoc(db_query('SELECT acctid FROM accounts WHERE login="'.$arr_data['from_id'].'" LIMIT 1'));
	if($arr_tmp['acctid'] > 0) {
		$arr_data['from_id'] = $arr_tmp['acctid'];
	}
	else {
		$arr_data['from_id'] = 0;
	}	
}
if( (int)$arr_data['to_id'] == 0 && !empty($arr_data['to_id']) ) {
	$arr_tmp = db_fetch_assoc(db_query('SELECT acctid FROM accounts WHERE login="'.$arr_data['to_id'].'" LIMIT 1'));
	if($arr_tmp['acctid'] > 0) {
		$arr_data['to_id'] = $arr_tmp['acctid'];
	}
	else {
		$arr_data['to_id'] = 0;
	}	
}

if($arr_data['to_id'] == 0 && $arr_data['from_id'] == 0) {
	$arr_data['mailtype'] = 1;
}

// Logeintrag schreiben
if($arr_data['to_id'] && $arr_data['mailtype'] != 1) {
	if(time() - $session['mailcheck_log']['to'][$arr_data['to_id']] > MAX_LOG_TIME) {
		debuglog('Mail-Kontrolle: Inbox',$arr_data['to_id']);
		$session['mailcheck_log']['to'][$arr_data['to_id']] = time();
	}
}
if($arr_data['from_id'] && $arr_data['mailtype'] != 1) {
	if(time() - $session['mailcheck_log']['from'][$arr_data['from_id']] > MAX_LOG_TIME) {
		debuglog('Mail-Kontrolle: Outbox',$arr_data['from_id']);
		$session['mailcheck_log']['from'][$arr_data['from_id']] = time();
	}
}
// END Logeintrag

// END Suchmaske						

page_header('Brieftauben - Inspektion');

output('`c`b`&Brieftauben - Inspektion`&`b`c`n`n');

// Grundnavi erstellen
addnav('Zurück');

addnav('G?Zur Grotte','superuser.php');
addnav('W?Zum Weltlichen',$session['su_return']);

addnav('Aktionen');
addnav('Start',$str_filename);
addnav('Suche');
addnav('Nach www suchen',$str_filename.'?op=search&message=www.');
addnav('Nach http suchen',$str_filename.'?op=search&message=http:');
addnav('Nach lotgd suchen',$str_filename.'?op=search&message=lotgd.');
// END Grundnavi erstellen
								


// Evtl. Fehler / Erfolgsmeldungen anzeigen
if($session['message'] != '') {
	output('`n`b'.$session['message'].'`b`n`n');
	$session['message'] = '';
}
// END Evtl. Fehler / Erfolgsmeldungen anzeigen

function show_mail_search () {
	
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
				
		$str_where = 	'	WHERE 		1  	
										'.($arr_data['mailtype'] == 1 	? ' AND m.msgfrom=0' : '').'
										'.($arr_data['mailtype'] == 2 	? ' AND m.msgfrom>0' : '').'
										'.($arr_data['state'] == 1 		? ' AND m.seen = 1' : '').'
										'.($arr_data['state'] == 2 		? ' AND m.seen = 0' : '').'
										'.($arr_data['from_id'] > 0 	? ' AND m.msgfrom = '.$arr_data['from_id'] : '').'
										'.($arr_data['to_id'] > 0 		? ' AND m.msgto = '.$arr_data['to_id'] : '').'
										'.(!empty($arr_data['message'])	
											? 'AND m.body LIKE "%'.$arr_data['message'].'%"' 
											: '');
		
		$str_count_sql = '	SELECT 		COUNT( * ) AS a
							FROM		mail m'
							.$str_where;
		
		$str_data_sql = '	SELECT m.*,t.name AS to_name,t.acctid AS to_acctid, f.name AS from_name, f.acctid AS from_acctid
							FROM		mail m		
							LEFT JOIN	accounts t ON t.acctid=m.msgto
							LEFT JOIN	accounts f ON f.acctid=m.msgfrom'
							.$str_where.'										
							ORDER BY 	sent DESC';
		
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
		
		$str_out .= show_mail_search();			
				
		$str_out .= '`n`c<table cellpadding="2" cellspacing="1">
						';
		
		$str_tr_class = 'trlight';
		
		$res = db_query($str_data_sql);
		
		if(db_num_rows($res) == 0) {
			
			$str_out .= '`iKeine Ergebnisse gefunden!`i';
			
		}
									
		// Ergebnisse zeigen
		while($l = db_fetch_assoc($res)) {
			
			$str_outbox_to_addon = '`& [ '.create_lnk('Out',$str_filename.'?op=search&from_id='.$l['to_acctid']).' ]`& ';
			$str_inbox_to_addon = ($arr_data['to_id'] != $l['to_acctid'] ? '`& [ '.create_lnk('In',$str_filename.'?op=search&to_id='.$l['to_acctid']).' ]`& ' : '');
			
			$str_outbox_from_addon = ($arr_data['from_id'] != $l['from_acctid'] ? '`& [ '.create_lnk('Out',$str_filename.'?op=search&from_id='.$l['from_acctid']).' ]`& ' : '');
			$str_inbox_from_addon = '`& [ '.create_lnk('In',$str_filename.'?op=search&to_id='.$l['from_acctid']).' ]`& ';
									
			$str_out .= '<tr class="trlight">
							<td>`&'.date('d. m. Y H:i:s',strtotime($l['sent'])).' ('.($l['seen'] ? 'Gelesen' : '`iUngelesen`i').')`nVon: '
								.( $l['msgfrom'] == 0 ? '`^System`&' : (!empty($l['from_name']) ? '`&'.$l['from_name'].$str_inbox_from_addon.$str_outbox_from_addon.'`&' : '`$Gelöscht`&') )
								.' -> An: '
								.( !empty($l['to_name']) ? '`&'.$l['to_name'].$str_inbox_to_addon.$str_outbox_to_addon.'`&' : '`$Gelöscht`&') 
								.'`n`&Betreff: '.(empty($l['subject']) ? '`iKeiner`i' : '`b'.closetags($l['subject'],'`b`i`c').'`b').'`&'
								.' - '.create_lnk('`$Del`0',$str_filename.'?op=del_mail&mid='.$l['messageid'].'&ret='.urlencode($str_baselnk.$page),true,false,'Diese Mail wirklich löschen?')
							.'</td>
						</tr>
						<tr>
							<td>`n';
			
			$l['body'] = nl2br(closetags($l['body'],'`b`c`i'));
															
			$str_out .= $l['body'];
								
			$str_out .= '	</td>
						</tr>
						<tr><td>&nbsp;</td></tr>';
		
		}
		
		$str_out .= '</table>`c';
		// END Ergebnisse zeigen
		
		output($str_out, true);
		
	break;
	// END Suchergebnisse
	
	case 'del_mail':
		
		$int_mid = (int)$_GET['mid'];
		
		$sql = 'SELECT body,msgfrom,msgto FROM mail WHERE messageid='.$int_mid;
		$m = db_fetch_assoc(db_query($sql));
		
		debuglog('Löschte Mail von AcctID '.$m['msgfrom'].' an '.$m['msgto'].'. Inhalt:`n'.$m['body']);
		
		$sql = 'DELETE FROM mail WHERE messageid='.$int_mid;
		db_query($sql);
		
		redirect( urldecode($_GET['ret']) );
		
	break;
					
	// Hm..		
	default:
		redirect($str_filename. '?op=search');

	break;
}


page_footer();
?>
