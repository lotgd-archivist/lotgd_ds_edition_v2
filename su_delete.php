<?php
/**
* su_delete.php: Maske zum vereinheitlichten, administrativen Löschen eines Users
* @author talion <t@ssilo.de>
* @version DS-E V/2
*/

$str_filename = basename(__FILE__);
require_once('common.php');
su_check(SU_RIGHT_EDITORUSER,true);

page_header('Spieler löschen');

output('`c`b`&Spieler löschen`&`b`c`n`n');

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

$str_ret = urldecode($_REQUEST['ret']);

// MAIN SWITCH
$str_op = ($_REQUEST['op'] ? $_REQUEST['op'] : '');

switch($str_op) {
	
	// Standardansicht
	case '':	
		
		if(!empty($str_ret)) {
			addnav('Zurück', $str_ret);
		}
		
		if(empty($_GET['ids']) || !is_array($_GET['ids'])) {
			output('Keine Accounts zur Löschung gewählt!');
		}
		else {
						
			$str_ids = implode(',',$_GET['ids']);
		
			$str_lnk = $str_filename.'?op=del&ret='.urlencode($str_ret);
			
			addnav('',$str_lnk);
			
			$str_enum_reasons = '	Administrative Gründe,Administrative Gründe,	
									Verstoß gegen Namensregelung,Verstoß gegen Namensregelung,
									Verstoß gegen Multiregeln,Verstoß gegen Multiregeln';
			
			$arr_form = array(
									'reason_custom'=>'Grund (Hier eingeben oder aus untenstehender Liste wählen):',
									'reason_given'=>'Grund:,enum,'.$str_enum_reasons									
								);
			
			$str_out = '<form method="POST" action="'.$str_lnk.'">
						`$Die im folgenden markierten Accounts werden `bunwiederbringlich`b gelöscht!`n';
			
			$sql = 'SELECT login,acctid,emailaddress FROM accounts WHERE acctid IN ('.$str_ids.')';
			$res = db_query($sql);
			
			while($a = db_fetch_assoc($res)) {
				
				$arr_form['ids['.$a['acctid'].']'] = $a['login'].' löschen,checkbox,'.$a['acctid'];
				$arr_form['bl_login['.$a['acctid'].']'] = $a['login'].' auf Blacklist,checkbox,'.$a['login'];
				$arr_form['bl_mail['.$a['acctid'].']'] = $a['emailaddress'].' auf Blacklist,checkbox,'.$a['emailaddress'];
						
			}
			
			$str_out .= '`n`&Jeder gelöschte Spieler erhält automatisch eine Benachrichtigungs-EMail mit dem Grund der Löschung.`n';
							
			$arr_data = array();
			
			$str_out .= generateform($arr_form,$arr_data,false,'Diese Accounts löschen!');
			
			$str_out .= '</form>';
			
			output($str_out, true);
		}
			
		break;
	
	// Löschung
	case 'del':
		
		if(empty($_POST['ids'])) {
			redirect($str_filename.'?ret='.urlencode($str_ret));
		}
		
		$str_reason = (!empty($_POST['reason_custom']) ? $_POST['reason_custom'] : $_POST['reason_given']);
		
		$str_ids = implode(',',$_POST['ids']);
		$str_deleted = '';
		$int_deleted = 0;
		
		$sql = 'SELECT login,acctid,emailaddress FROM accounts WHERE acctid IN ('.$str_ids.')';
		$res = db_query($sql);
				
		while($a = db_fetch_assoc($res)) {
			
			if( user_delete($a['acctid']) ) {
				systemlog('`$Account '.$a['login'].', ID '.$a['acctid'].' gelöscht. Grund: `&'.$str_reason, $session['user']['acctid']);
				$str_deleted .= $a['login'].'`n';
				$int_deleted++;
				
				if( is_email($a['emailaddress']) ) {
					mail($a['emailaddress'], getsetting('townname','Atrahor').' - Account gelöscht',
						'Dein Charakter "'.$a['login'].'" in '.getsetting('townname','Atrahor').' ( http://'.$_SERVER['SERVER_NAME'].' ) wurde soeben von der Administration gelöscht! Grund: " '.$str_reason.' ". Solltest du Fragen zu dieser Löschung haben, schreibe bitte eine Anfrage.',
						'From: '.getsetting('gameadminemail','postmaster@localhost.com')
						);
				}
				
				// Blacklist
				if(!empty($_POST['bl_login'][$a['acctid']])) {
					$str_login = trim(
									addslashes(
										stripslashes(
											strtolower($_POST['bl_login'][$a['acctid']])
										)
									)
								);
		
					// Duplikate vermeiden
					$sql = 'DELETE FROM blacklist WHERE value="'.$str_login.'" AND type=(0 ^ '.BLACKLIST_LOGIN.')';
					db_query($sql);
				
					$sql = 'INSERT INTO blacklist SET value="'.$str_login.'",type='.BLACKLIST_LOGIN;
					db_query($sql);
				}
				if(!empty($_POST['bl_mail'][$a['acctid']])) {
					$str_mail = trim(
									addslashes(
										stripslashes(
											strtolower($_POST['bl_mail'][$a['acctid']])
										)
									)
								);
		
					// Duplikate vermeiden
					$sql = 'DELETE FROM blacklist WHERE value="'.$str_mail.'" AND type=(0 ^ '.BLACKLIST_EMAIL.')';
					db_query($sql);
				
					$sql = 'INSERT INTO blacklist SET value="'.$str_mail.'",type='.BLACKLIST_EMAIL.',remarks="Anlässl. Löschung des Spielers '.addslashes($a['login']).'"';
					db_query($sql);
				}
				
			}
			else {
				
			}
		
		}
				
		$session['message'] = '`@`b'.$int_deleted.'`b Spieler gelöscht:`$`n'.$str_deleted.'`0';
		redirect($str_filename.'?ret='.urlencode($str_ret));
		
	break;
		
	// Hm..		
	default:
		output('Was hast du denn HIER verloren?! Op: '.$op);	
		addnav('Zurück',$str_filename . '');
	break;
}


page_footer();
?>
