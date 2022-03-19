<?php
/*-------------------------------/
Name: comment_funcs.php
Autor: tcb / talion für Drachenserver (mail: t@ssilo.de)
Erstellungsdatum: 01 - 02 / 2006
Beschreibung:	Stellt erweiterte Funktionalität für Chatareas zur Verfügung:
				Kommentare an EMail versenden, Hilferuf an Mods
				Copyright-Box muss intakt bleiben, bei Verwendung Mail an Autor mit Serveradresse.
/*-------------------------------*/

require_once('common.php');

// Mindestzeitabstand zwischen zwei Sendungen in Sekunden
define('COMMENTMAIL_MIN_INTERVAL',900);

// Enthält CSS-Anweisungen für Kommentaremail
// [bgcolor] etc. sind Platzhalter und werden unten durch Angaben des Users ersetzt
$str_css = '
			* {
				padding:0px;
				margin:0px;
			}
			body {
				background-color:[bgcolor];
				padding:0px;
				margin:0px;
			}
			body *{
				font-family: Verdana, Arial, Helvetica, sans-serif;
				font-size: [fontsize]px;
				color: [fontcolor];
			}
			'.write_appoencode_css().'
';


if(!$session['user']['loggedin']) {exit;}

if($_GET['callmod']) {
	popup_header('Hilferuf');
	
	if( time() - $session['modcalled'] < 300 && $session['user']['superuser'] == 0) {
	
		output('`&Du hast bereits einen Hilferuf gesendet. Gedulde dich bitte etwas.');
		
	}
	else {
		
		$timeout = date('Y-m-d H:i:s',time() - 3600);
		
		$sql = 'SELECT author,login,comment FROM commentary, accounts 
				WHERE acctid=author AND postdate>"'.$timeout.'" AND section="'.$_GET['section'].'" LIMIT 70';
		$res = db_query($sql);
		
		if(db_num_rows($res)) {		
			
			$msg = 'OOC in Sektion '.$_GET['section'].':\n\n';
			
			while($c = db_fetch_assoc($res)) {
				$msg .= $c['login'].' : '.$c['comment'].'\n';
			}
			
			$msg = addcslashes($msg,'\'');
			
			$sql = 'INSERT INTO petitions SET author='.$session['user']['acctid'].',date=NOW(),kat=7,body=\''.$msg.'\',lastact=NOW(),IP="'.$session['user']['lastip'].'"';
			db_query($sql);
			
	
			output('`&`c`bDein Hilferuf wurde gesendet! Sobald ein Mod online kommt, wird er sich darum kümmern.`b`c`n');
		}
		else {
			output('`&An diesem Ort wurde in der letzten Stunde nichts geschrieben.');
		}
		
		
		$session['modcalled'] = time();
	}
	$ret = calcreturnpath(urldecode($_GET['ret']));	
	addnav('Zurück',$ret);

}

else if($_POST['commentlimit']) {
	
	$commentlimit = (int)$_POST['commentlimit'];
	
	if($commentlimit > 0 && !empty($_GET['section'])) {
		$session['user']['prefs']['commentlimit'][$_GET['section']] = $commentlimit;
	}
		
	$ret = calcreturnpath(urldecode($_GET['ret']));	
		
	//redirect($ret);
	addnav('',$ret);
	header('Location:'.$ret);

}

else if($_GET['npc_off']) {
	
	$session['disable_npc_comment'] = $session['disable_npc_comment'] ? false : true;
			
	$ret = calcreturnpath(urldecode($_GET['ret']));	
	
	addnav('',$ret);
	header('Location:'.$ret);

}

else if($_GET['vitalchange']) {
	
	$session['disablevital'] = $session['disablevital'] ? false : true;
		
	$ret = calcreturnpath(urldecode($_GET['ret']));	
	
	addnav('',$ret);
	header('Location:'.$ret);
}

else if($_GET['comment_sendmail']) {
	popup_header('Kommentare an EMail versenden');
	
	if(!empty($_GET['section'])) {
		$section = $_GET['section'];
		$session['comsend_section'] = $section;
	}
	else {
		if(!empty($session['comsend_section'])) {
			$section = $session['comsend_section'];
		}
	}
					
	// Wird in der viewcommentary gesetzt, um zu verhindern, dass sensible Sections angezeigt werden
	if( empty($section) ) {
		output('`n`b`$Kein Bereich ausgewählt, aus dem Kommentare versendet werden sollen!`b`0`n`n');
		popup_footer();
		exit;
	}
	
	if( empty($session['user']['prefs']['comsection_nav'][$section]) && empty($session['user']['prefs']['comsection_nonav'][$section]) ) {
		output('`n`b`$Kein Bereich ausgewählt, aus dem Kommentare versendet werden sollen!`b`0`n`n');
		popup_footer();
		exit;
	}
	
	if(!is_email($session['user']['emailaddress'])) {
		output('`$Du hast in deinem Profil keine gültige Emailadresse angegeben!');
		popup_footer();
		exit;
	}
					
	
	// Wie viele Kommentare / Seite?
	$sitecount = ($_GET['limit'] ? $_GET['limit'] : 50);
	
	$int_lasttime = 0;				
	$int_lastid = (int)$session['comsendmail'][$section];
	if($int_lastid > 0) {
	
		$arr_lastcomment = getcommentary(' commentid='.$int_lastid);
		$arr_lastcomment = $arr_lastcomment[0];
		$int_lasttime = $session['comsendmail']['lastsent'];
						
	}
	
	// 15 Mins warten
	if((time() - $int_lasttime) < COMMENTMAIL_MIN_INTERVAL && $session['user']['superuser'] == 0) {
		
		output('`n`$`bDu mußt noch eine Weile warten, ehe du erneut Kommentare versenden darfst!`b`0`n');
		popup_footer();
		exit;
		
	}
	
	$start=(int)$_REQUEST['start'];
	$count = (int)$_REQUEST['count'];
	$fontsize = (int)$_REQUEST['fontsize'];
	$fontcolor = urldecode($_REQUEST['fontcolor']);
	$bgcolor = urldecode($_REQUEST['bgcolor']);
	$act = $_REQUEST['act'];
					
	// Gesamtanzahl der Beiträge zählen
	$sql = 'SELECT COUNT(*) AS a
		  FROM commentary
		 INNER JOIN accounts
			ON accounts.acctid = commentary.author
		 WHERE section="'.$section.'" AND self=1';
		 
	$arr_count = db_fetch_assoc(db_query($sql));
	
	$str_error = '';
	
	$str_css = str_replace('[bgcolor]',$bgcolor,$str_css);
	$str_css = str_replace('[fontcolor]',$fontcolor,$str_css);
	$str_css = str_replace('[fontsize]',$fontsize,$str_css);
									
	if($arr_count['a'] == 0) {
		output('`n`b`$Im gewünschten Bereich sind leider keine Kommentare vorhanden!`b`0`n`n');
		popup_footer();
		exit;
	}
	
	// Wenn keine Anzahl gewählt
	// -> Startformular anzeigen
	if($count == 0) {$act = '';}
	
	if($act == 'send') {
								
		$arr_comments = getcommentary(' section="'.$section.'"  AND self=1 '.($start == 1234567 && $int_lastid>0 ? ' AND commentid>'.$int_lastid : ''),($start != 1234567 ? $start : 0),$count);
		
		if(sizeof($arr_comments) == 0) {
			output('`b`$Gähnende Leere zu versenden, kann langweilen ; )`0`b`n');
			popup_footer();
			exit;
		}
		
		$body = '<html>
			<head>
				<title>Chatmitschnitt</title>
				<style>
				<!--
					'.$str_css.'
				-->
				</style>
			</head>
			<body>
				<b>`&Chatmitschnitt vom '.date('d. m. Y H:i:s',time()).':</b><br><br>
				';

		foreach($arr_comments as $c) {
		
			$body .= $c['comment'];
			$int_lastid = $c['commentid'];
			
		}
		
		$body = appoencode($body);
		
		$body .= '</body></html>';
										
		$session['comsendmail'][$section] = $int_lastid;
		$session['comsendmail']['lastsent'] = time();
						
		$maili = getsetting('gameadminemail','');
		
		$headers="";
		$headers .="From: $maili <$maili>\n";
		$headers .= "Reply-To: $maili <$maili>\n";
		$headers .= "Date: ".date("r")."\n";
		$headers .= "Return-Path: $maili <$maili>\n";
		$headers .= "Delivered-to: $maili <$maili>\n";
		$headers .= "MIME-Version: 1.0\n";
		$headers .= "Content-type: text/html;charset=ISO-8859-9\n";
				
		mail($session['user']['emailaddress'],'Chatausschnitt',$body,$headers);
		
		unset($session['comsend_section']);
		
		output('`@Der von dir gewünschte Ausschnitt wurde an `b'.$session['user']['emailaddress'].'`b versandt!`n
				Du kannst dieses Fenster nun schließen.');
	}
	
	else if($act == 'preview') {
		
		output('`&Kommentare werden gesendet an: `b'.$session['user']['emailaddress'].'`0`b`n');
		
		output('`&`bVorschau:`0`b`n`n',true);
		
		$link = 'comment_funcs.php?comment_sendmail=1&count='.$count.'&start='.$start.'&fontsize='.$fontsize.'&fontcolor='.urlencode($fontcolor).'&bgcolor='.urlencode($bgcolor);
		
		output('<iframe src="'.$link.'&act=preview_iframe" width="450" height="200">Um die Vorschau zu sehen, müsste dein Browser Iframes unterstützen!</iframe>');
								
		output('`n`n`&Bist du mit diesem Ergebnis einverstanden?`n');
		output('`n`bJawoll, <a href="'.$link.'&act=send">passt so!</a>`b',true);
		output('`n`bNee, <a href="'.$link.'">lass mich nochmal nachdenken!</a>`b',true);
					
	}
	else if($act == 'preview_iframe') {
		
		$arr_comments = getcommentary(' section="'.$section.'"  AND self=1 '.($start == 1234567 && $int_lastid>0 ? ' AND commentid>'.$int_lastid : ''),($start != 1234567 ? $start : 0),$count);
		
		$body = '<html>
				<head>
					<title>Chatmitschnitt</title>
					<style>
					<!--
						'.$str_css.'
					-->
					</style>
				</head>
				<body>
					<b>`&Chatmitschnitt vom '.date('d. m. Y H:i:s',time()).':</b><br><br>
					';
		
		
		if(sizeof($arr_comments) == 0) {
			$body .= '`b`$Seitdem ist nichts mehr passiert. Da gibt es nichts zu verschicken!`0`b`n';
		}
		else {
			foreach($arr_comments as $c) {
			
				$body .= $c['comment'];
											
			}
		}
		
		$body = appoencode($body);
		
		$body .= '</body></html>';
		
		echo($body);						
		exit;
										
	}
	else {	// Ausgangspunkt
		
		output('`&Kommentare werden gesendet an: `b'.$session['user']['emailaddress'].'`0`b`n');
		
		if(is_array($arr_lastcomment)) {
			
			output('`&Letzter versandter Kommentar aus dieser Sektion:`n
					`i'.$arr_lastcomment['comment'].'`i `& am '.date('d. m. Y H:i:s',$int_lasttime));
							
		}
		
		output('<form action="comment_funcs.php?comment_sendmail=1&act=preview" method="POST">',true);
		
		$str_start_radio = ',radio';
							
		// Standard: Bei erster Seite starten
		$int_maxsite = ceil($arr_count['a']/$sitecount);
		
		for($i=0;$i<$int_maxsite;$i++) {
									
			$int_sitestart = $arr_count['a'] - ($sitecount * ($i+1));
			
			$int_sitestart = max($int_sitestart,0);
			
			$str_start_radio .= ','.$int_sitestart.','.$i.' Seite(n) zurück';
			
			$data['start'] = $int_sitestart;
			
		}		
		
		if($int_lastid) {
			$str_start_radio .= ',1234567,Letzter Speicherpunkt';
			$data['start'] = 1234567;
		}
		
		$str_count_radio = ',radio';
		
		$data['count'] = min($arr_count['a'],50);
		
		$int_maxcount = min($arr_count['a'],500);
		
		for($i=$data['count'];$i<=$int_maxcount;$i+=50) {
			
			$int_count = ($arr_count['a']<$i ? $arr_count['a'] : $i);
						
			$str_count_radio .= ','.$int_count.','.$int_count.'';
										
		}	
						
		$str_fontsize_radio = ',radio,11,normal,12,groß,13,sehr groß,14,riesig';
		
		$str_fontcolor_radio = ',radio,#FFFFFF,weiß,#000000,schwarz';
		
		$form = array('start'=>'Von welchem Punkt an willst du die Kommentare speichern?'.$str_start_radio,
						'count'=>'Wie viele Kommentare willst du speichern?'.$str_count_radio,
						'fontsize'=>'Schriftgröße?'.$str_fontsize_radio,
						'fontcolor'=>'Schriftfarbe?'.$str_fontcolor_radio,
						'bgcolor'=>'Hintergrundfarbe?'.$str_fontcolor_radio
					);
						
		
		$data['fontsize'] = 11;
		$data['fontcolor'] = '#FFFFFF';
		$data['bgcolor'] = '#000000';
		
		$data = array_merge($data,$_REQUEST);
					
		showform($form,$data,false,'Vorschau!');
		
		output('</form>',true);
											
	}
	

		
}

popup_footer();
?>