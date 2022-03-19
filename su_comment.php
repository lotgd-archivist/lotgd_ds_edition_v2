<?php
/**
* su_comment.php: Kommentarüberwachung
* @author talion <t@ssilo.de>
* @version DS-E V/2
*/

$str_filename = basename(__FILE__);
require_once('common.php');

define('MAX_LOG_TIME',1800);

function show_section_select ($section='') {

	global $REQUEST_URI,$session;

	$sql = 'SELECT section FROM commentary WHERE su_min'.(su_check(SU_RIGHT_COMMENTPRIV) ? '>=0' : '<=1').' GROUP BY section ORDER BY section ASC';
	$res = db_query($sql);

	$link = $str_filename.'?op=single_section';

	output('`&`c<form method="post" action="'.$link.'"><select name="section" size="1" onchange="this.form.submit()">',true);
	
	if($section == 'U' || !empty($session['su_comment']['uid'])) {
		output('<option value="U" '.($section == 'U' ? 'selected="selected"' : '').'>Bestimmter Benutzer</option>',true);
	}
	
	output('<option value="" '.($section == '' ? 'selected="selected"' : '').'>Aktuelle Kommentare</option>',true);
	output('<option value="">~~~~~~</option>',true);
	
	while($s = db_fetch_assoc($res)) {

		output('<option value="'.$s['section'].'" '.($section == $s['section'] ? 'selected="selected"' : '').'>'.$s['section'].'</option>',true);

	}
	output('</select></form>`c`n',true);

	if(!empty($_POST['section']) && empty($_GET['section'])) {
		$REQUEST_URI .= '&section='.$_POST['section'];
	}

}

if(!$session['user']['loggedin']) {exit;}

su_check(SU_RIGHT_COMMENT,true);

popup_header('Kommentar - Kontrolle');

output('<script language="javascript">window.resizeTo(800,600);</script>
		`c`b`&Kommentar-Kontrolle`&`b`c`n`n');

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

// MAIN SWITCH
$op = ($_REQUEST['op'] ? $_REQUEST['op'] : '');

switch($op) {
	
	// Userkommentare
	case 'user':	
		
		show_section_select('U');
		
		$author = (!empty($_GET['uid']) ? (int)$_GET['uid'] : $session['su_comment']['uid']);
		$session['su_comment']['uid'] = $author;
					
		viewcommentary("' OR 1 AND su_min".(su_check(SU_RIGHT_COMMENTPRIV) ? '>=0' : '<=1')." AND author='".$author."",'X',100,true,false,false,false,false,false,false);
		
		if(time()-$session['logs']['suwatch']['u'.$author] > MAX_LOG_TIME) {
			debuglog("`&Kommentare geprüft:",$author);
			$session['logs']['suwatch']['u'.$author] = time();
		}
        
	break;
		
	// Einzelne Section
	case 'single_section':	
	
		$section = $_REQUEST['section'];
						
		if(empty($section)) {
			header('Location:'.$str_filename.'?op=recent_comments');
			exit();
		}
		
		if($section == 'U') {
			header('Location:'.$str_filename.'?op=user');
			exit();
		}

		show_section_select($section);
	
		viewcommentary("' or '1'='1' AND section='".$section."' AND su_min".(su_check(SU_RIGHT_COMMENTPRIV) ? ">='0" : "<='1"),'X',100,true,false,false,false,false,false,false);
		
		if(time()-$session['logs']['suwatch'][$section] > MAX_LOG_TIME) {
			debuglog("`&Kommentare in Section ".$section." `&geprüft.");
			$session['logs']['suwatch'][$section] = time();
		}
			
	break;
		
	// Aktuelle Kommentare
	case 'recent_comments':	
	
		show_section_select();

		viewcommentary("' or '1'='1' AND su_min".(su_check(SU_RIGHT_COMMENTPRIV) ? ">='0" : "<='1"),'X',200,true,false,false,false,false,false,false);
			
	break;
		
	// Kommentar löschen
	case 'del_comment':
		
		$ids = '';

		if(is_array($_POST['commentid'])) {
			$ids = implode(',',$_POST['commentid']);
		}
		
		// Kommentarbeweissicherung
		if ($_POST['commentback']=='1')
		{
			$sql = "UPDATE commentary SET section=CONCAT('_cback_',section),postdate=NOW(),comment=CONCAT('[ Datum: '+postdate+' ]',comment) WHERE commentid ".($ids != '' ? ' IN(-1,'.$ids.')' : '='.$_GET['commentid']);
			db_query($sql);
		}
		else {
			$sql = "DELETE FROM commentary WHERE commentid ".($ids != '' ? ' IN(-1,'.$ids.')' : '='.$_GET['commentid']);
			db_query($sql);
		}
		
		$return = calcreturnpath(urldecode($_GET['return']));
		header('Location:'.$return);
		exit();
		
	break;
	
	// Hm..		
	default:
		output('Was hast du denn HIER verloren?! Op: '.$op);	
	break;
}


popup_footer();
?>
