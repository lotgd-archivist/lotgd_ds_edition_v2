<?php
/**
* su_petitions.php: Anfragenmanager
* @author 	partly LOGD-Core, modded and rewritten by talion <t@ssilo.de>
* @version DS-E V/2
*/

// 11092004

// modded by talion (t@ssilo.de) für den Drachenserver (lotgd.drachenserver.de): 
// kategorien, webmail, floskeln, Priorität, Permanente Kommentare, versch. Kleinigkeiten

$str_filename = basename(__FILE__);
require_once('common.php');
su_check(SU_RIGHT_PETITION,true);

page_header('Petition Viewer');

// Gruß
$str_greets = '
Schöne Grüße
Dein Drachenserver-Team';

// Standard-Navs
addnav("G?Zurück zur Grotte","superuser.php");
addnav('W?Zurück zum Weltlichen',$session['su_return']);
// END Standard-Navs

output('`&`b`c'.getsetting('townname','Atrahor').'-Callcenter : )`c`b`n`n');

// Kommentare dauerhaft in Anfrage einfügen
if ( sizeof($_POST['insertcommentary']) > 0 ) {
	
	$comment = str_replace('`n','',soap($_POST['insertcommentary'][$_POST['section']]));
	$comment = preg_replace("'([^[:space:]]{45,45})([^[:space:]])'","\\1 \\2",$comment);
	$comment = str_replace('/me','',$comment);
			
	$comment = '`n`b`#'.addslashes($session['user']['login']).' : `b`3'.$comment;

	$sql = 'UPDATE petitions SET lastact=NOW(),commentcount=commentcount+1,comments=CONCAT(comments,"'.$comment.'") WHERE petitionid="'.$_GET['id'].'"';
	db_query($sql);
}

// Zustandsbezeichnungen
$statuses=array(0=>"`bUngelesen`b","Gelesen","Geschlossen");

$str_op = (!empty($_REQUEST['op']) ? $_REQUEST['op'] : '');

switch($str_op) {
	
	// Anfrage löschen
	case 'del':
		$pid = (int)$_GET['id'];
		$sql = 'DELETE FROM petitions WHERE petitionid='.$pid;
		db_query($sql);
		$sql = 'DELETE FROM petitionmail WHERE petitionid='.$pid;
		db_query($sql);
		
		redirect($str_filename);			
	break;
	
	// Anfragenantwortmail löschen
	case 'delmessage':
		$pid = (int)$_GET['pid'];
		$mid = (int)$_GET['mid'];
		
		// Aus petitionmail löschen
		$sql = 'DELETE FROM petitionmail WHERE petitionid='.$pid.' AND messageid='.$mid;
		db_query($sql);
		
		// Aus Mailbox löschen
		$sql = 'DELETE FROM mail WHERE messageid='.$mid;
		db_query($sql);
			
		redirect($str_filename.'?op=view&id='.$pid);			
	break;
	
	// Navs eines Spielers reparieren
	case 'repairnavs':
		
		$pid = (int)$_GET['pid'];
		$uid = (int)$_GET['userid'];
		
		$sql = 'UPDATE accounts SET allowednavs="",output="",restorepage="" WHERE acctid='.$uid;
		db_query($sql);
			
		redirect($str_filename.'?op=view&id='.$pid);	
		
	break;

	// Spieleraccount aktivieren
	case 'validate_mail':
		
		$pid = (int)$_GET['pid'];
		$uid = (int)$_GET['userid'];
		
		$sql = 'UPDATE accounts SET emailvalidation="" WHERE acctid='.$uid;
		db_query($sql);
			
		redirect($str_filename.'?op=view&id='.$pid);	
		
	break;
	
	// AntwortYeOlde versenden
	case 'sendmessage':
		
		$pid = (int)$_GET['id'];
		
		$sql = 'SELECT author,body FROM petitions WHERE petitionid='.$pid;
		$row = db_fetch_assoc(db_query($sql));
		
		$_POST['subject']=closetags(str_replace("`n","",$_POST['subject']),'`c`i`b');
		$_POST['body']=str_replace("`n","\n",$_POST['body']);
		$_POST['body']=str_replace("\r\n","\n",$_POST['body']);
		$_POST['body']=str_replace("\r","\n",$_POST['body']);
		$_POST['body']=substr($_POST['body'],0,(int)getsetting("mailsizelimit",1024));
		$_POST['body'] = closetags($_POST['body'],'`c`i`b');
		
		systemmail($row['author'],$_POST['subject'],$_POST['body']);
		
		petitionmail($_POST['subject'],$_POST['body'],$pid,$session['user']['acctid'],1,$row['author'],db_insert_id(LINK));
		redirect($str_filename.'?op=view&id='.$pid);	
		
	break;
	
	// AntwortEMail versenden
	case 'sendmail':
		
		$pid = (int)$_GET['id'];
		
		$sql = 'SELECT body FROM petitions WHERE petitionid='.$pid;
		$row = db_fetch_assoc(db_query($sql));
		$subject=closetags(str_replace("`n","",$_POST['subject']),'`c`i`b');
		$body=str_replace("`n","\n",$_POST['body']);
		$body=str_replace("\r\n","\n",$body);
		$body=str_replace("\r","\n",$body);
		$body=substr($body,0,(int)getsetting("mailsizelimit",1024));
		$body = closetags($body,'`c`i`b');
		
		petitionmail($subject,$body,$pid,$session['user']['acctid'],1,0,0);
		
		$body = '
( ACHTUNG : Evtl. Antworten auf diese Mail bitte wieder per Anfrage! )
		
'.$body.'
	
( ACHTUNG : Evtl. Antworten auf diese Mail bitte wieder per Anfrage! )
';
		
		$mail = urldecode($_POST['mail']);
		
		$mails_sent = getsetting('petitionemailsent',0);
		
		$mails_nr = ceil($mails_sent/20);
		
		$from_mail = 'lotgd'.$mails_nr.'.30.lotgd@spamgourmet.com';
		
		savesetting('petitionemail',$from_mail);
		savesetting('petitionemailsent',$mails_sent+1);
		
		$headers = 'From: '.$from_mail;
			
		mail($mail,$subject,$body,$headers);
				
		redirect($str_filename.'?op=view&id='.$pid);
		
	break;
	// END Antwort-EMail
	
	// Einzelne Anfrage
	case 'view':
		
		$int_pid = (int)$_GET['id'];
		
		$sql = 'SELECT 		a.name,a.login,a.acctid,a.loggedin,a.laston,a.activated, 
							p.* 
				FROM 		petitions p
				LEFT JOIN 	accounts a 
					ON 		a.acctid = p.author
				WHERE 		p.petitionid='.$int_pid.'
				ORDER BY 	date ASC';
		$result = db_query($sql);
		$row = db_fetch_assoc($result);
	
		if ($_GET['viewpageinfo']==1){
			addnav('Details ausblenden',$str_filename.'?op=view&id='.$int_pid);
		}
		else{
			addnav('D?Details einblenden',$str_filename.'?op=view&id='.$int_pid.'&viewpageinfo=1');
		}
		
		addnav('Anfragen anzeigen',$str_filename.'?kat='.$_GET['kat']);
	
		addnav('Operationen');
		addnav('Anfrage schliessen',$str_filename.'?setstat=2&id='.$int_pid);
		addnav('U?Als Ungelesen markieren',$str_filename.'?setstat=0&id='.$int_pid);
		addnav('S?Als GeleSen markieren',$str_filename.'?setstat=1&id='.$int_pid);
		if($row['prio'] == 0) {addnav('P?Hohe Prio',$str_filename.'?setprio=1&id='.$int_pid);}
		else {addnav('N?Normale Prio',$str_filename.'?setprio=0&id='.$int_pid);}
		addnav('Eintrag in ToDo','todolist.php?op=newtask');
			
		if ($row['acctid']>0){
			
			addnav('Account - '.$row['login']);
			if(su_check(SU_RIGHT_EDITORUSER)) {
				addnav('Usereintrag bearbeiten','user.php?op=edit&userid='.$row['acctid'].'&returnpetition='.$int_pid);
			}
			if(su_check(SU_RIGHT_DEBUGLOG)) {
				addnav('Debuglog','su_logs.php?op=search&type=debuglog&account_id='.$row['acctid'].'&ret='.urlencode(calcreturnpath()) );
			}
			
			addnav('Zur Bio','bio.php?id='.$row['acctid'].'&ret='.urlencode($_SERVER['REQUEST_URI']) );
			addnav('Navs reparieren',$str_filename.'?op=repairnavs&userid='.$row['acctid'].'&pid='.$_GET['id']);
			addnav('Account aktivieren',$str_filename.'?op=validate_mail&userid='.$row['acctid'].'&pid='.$_GET['id']);
					
			$loggedin = user_get_online(0,$row);
		}
		else {
			$sql = 'SELECT login,acctid,uniqueid,lastip FROM accounts WHERE lastip = "'.addslashes($row['IP']).'" OR uniqueid = "'.addslashes($row['ID']).'" ORDER BY login, acctid';
			$res = db_query($sql);
			
			$sec_info = '';
			
			while($r = db_fetch_assoc($res) ) {
				
				addnav('Account - '.$r['login']);
				
				if(su_check(SU_RIGHT_EDITORUSER)) {
					addnav('Usereintrag bearbeiten','user.php?op=edit&userid='.$r['acctid'].'&returnpetition='.$int_pid);
				}
				if(su_check(SU_RIGHT_DEBUGLOG)) {
					addnav('Debuglog','su_logs.php?op=search&type=debuglog&account_id='.$r['acctid'].'&ret='.urlencode(calcreturnpath()) );
				}
				
				addnav('Zur Bio','bio.php?id='.$r['acctid'].'&ret='.urlencode($_SERVER['REQUEST_URI']) );
				addnav('Navs reparieren',$str_filename.'?op=repairnavs&userid='.$r['acctid'].'&pid='.$_GET['id']);
				addnav('Account aktivieren',$str_filename.'?op=validate_mail&userid='.$r['acctid'].'&pid='.$_GET['id']);
				
				$sec_info .= '`n'.$r['login'].' (AcctID '.$r['acctid'].', IP '.$r['lastip'].', ID '.$r['uniqueid'].')';
				
			}
			
		}
		
		addnav("Zu Kategorie:");
		foreach($ARR_PETITION_KATS as $k=>$v) {
			
			addnav($v,$str_filename.'?setkat='.$k.'&id='.$int_pid);		
		
		}
			
		$session['petitions'][$int_pid] = date('Y-m-d H:i:s');
		
		output('`@Von: ');
		
		$row['body']=stripslashes($row['body']);
				
		if (!empty($row['login'])) {
			$str_maillnk = 'mail.php?op=write&to='.rawurlencode($row['login']).'&body='.URLEncode('\n\n----- Deine Anfrage -----\n'.$row['body']).'&subject=RE:+Hilfeanfrage';
			output('<a href="'.$str_maillnk.'" target="_blank" onClick="'.popup($str_maillnk).'";return false;">
					<img src="images/newscroll.GIF" width="16" height="16" alt="Mail schreiben" border="0">
					</a>',true);
		}
		
		output('`^`b'.$row['name'].'`b'.($loggedin ? ' `@(online)`0 ' : '').'`n');
		output('`@Datum: `^`b'.$row['date'].'`b`n');
		output('`@Kategorie: `^`b'.$ARR_PETITION_KATS[$row[kat]].'`b`n');
		output('`@Body:`^`n');
		$body = HTMLEntities($row[body]);
		$body = preg_replace("'([[:alnum:]_.-]+[@][[:alnum:]_.-]{2,}([.][[:alnum:]_.-]{2,})+)'i","<a href='mailto:\\1?subject=RE: Hilfeanfrage&body=".str_replace("+"," ",URLEncode("\n\n----- Deine Anfrage -----\n".$row[body]))."'>\\1</a>",$body);
		$body = preg_replace("'([\\[][[:alnum:]_.-]+[\\]])'i","<span class='colLtRed'>\\1</span>",$body);
		$output.='<span style="font-family: fixed-width">'.nl2br($body).'</span>';
		
		if(!empty($sec_info)) {
			output('`n`n`qFolgende Accounts sind wahrscheinlich mit dieser Anfrage (IP '.$row['IP'].', ID '.$row['ID'].') verbunden:`^');
			output($sec_info.'`n `qZum Zeitpunkt der Anfragenstellung:'.$row['connected']);
		}
		
		output('`n`@Kommentare:`n');
		
		output($row['comments'].'`n',true);
		
		viewcommentary('pet-'.$int_pid,'Hinzufügen',200);
		
		// Antworten per Ye Olde
		if (!empty($row['login'])) {
			$answerbody = "\n\n".'----- Deine Anfrage -----'."\n".$row['body'];
			$answersubject = 'RE: Hilfeanfrage';
			output('`n`n`@Mailverkehr:`n<table><tr><td>',true);
			$sql = 'SELECT p.*, a.login,m.seen AS is_seen FROM petitionmail p
					LEFT JOIN accounts a ON p.msgfrom=a.acctid
					LEFT JOIN mail m ON m.messageid=p.messageid
					WHERE petitionid="'.$int_pid.'" ORDER BY sent ASC';
			$result = db_query($sql);
			while ($row2 = db_fetch_assoc($result)) {
				
				if($row2['messageid']) {
					if(!isset($row2['is_seen'])) { $row2['is_seen'] = 1; }
				}
						
				output('<table class="input" width="100%"><tr><td>',true);
				output('`4Datum:`& '.$row2['sent'].' '.($row2['messageid'] ? '(Gelesen: '.$row2['is_seen'].')' : '').'`n`4Von:`& '.$row2['login'].'`n`4Betreff:`& '.$row2['subject'].'`n`4Text:`& ');
				output(str_replace("\n","`n",$row2['body']));
				
				// Löschung von ungelesenen Admin-Antworten erlauben
				if(!$row2['is_seen'] && $row2['msgfrom'] != $row['acctid']) {
					
					$link = $str_filename.'?op=delmessage&pid='.$row2['petitionid'].'&mid='.$row2['messageid'];
					addnav('',$link);
					output('`n`n[ <a href="'.$link.'">Löschen</a> ]',true);
					
				}
				
				output('</td></tr></table>`n',true);
				$answerbody = "\n\n----- Deine Anfrage -----\n".$row2['body'];
				$answersubject = 'RE: '.$row2['subject'];
			}
			output('</td></tr></table>',true);
			
			if(db_num_rows($result) == 0) {
				
				$answerbody = "\n\n".$str_greets.$answerbody;
				
			}
			
			$str_lnk = $str_filename.'?op=sendmessage&id='.$int_pid;
			
			output('<form action="'.$str_lnk.'" method="post">',true);
			output('`@Ingame-Mail schreiben`n');
			output('Betreff: <input type="text" name="subject" value="'.$answersubject.'">`n
					Text:`n<textarea name="body" class="input" cols="40" rows="9">'.$answerbody.'</textarea>`n
					<input type="submit" class="button" value="Senden"></form>`n',true);
			addnav('',$str_lnk);
			
			$sql = 'UPDATE petitionmail SET seen=1 WHERE petitionid="'.$int_pid.'"';
			db_query($sql);
		}	
		// Antworten per EMail
		else {
			
			$mail = array();
			
			preg_match("'([[:alnum:]_.-]+[@][[:alnum:]_.-]{2,}([.][[:alnum:]_.-]{2,})+)'i",$body,$mail);
			
			if($mail[0] != '') {
		
				$answerbody = "\n\n".'----- Deine Anfrage -----'."\n\n".$row['body'];
				$answersubject = 'RE: Hilfeanfrage';
				
				output('`n`n`@Bisherige Antworten an '.$mail[0].':`n<table><tr><td>',true);
				$sql = 'SELECT petitionmail.*, accounts.login FROM petitionmail LEFT JOIN accounts ON petitionmail.msgfrom=accounts.acctid WHERE petitionid="'.$_GET['id'].'" ORDER BY sent ASC';
				$result = db_query($sql);
				while ($row2 = db_fetch_assoc($result)) {
					output('<table class="input" width="100%"><tr><td>',true);
					output('`4Datum:`& '.$row2['sent'].'`n`4Von:`& '.$row2['login'].'`n`4Betreff:`& '.$row2['subject'].'`n`4Text:`& ');
					output(str_replace("\n","`n",$row2['body']));
					output('</td></tr></table>`n',true);
					$answerbody = "\n\n----- Deine Anfrage -----\n".$row2['body'];
					$answersubject = 'RE: '.$row2['subject'];
				}
				output('</td></tr></table>',true);
				
				if(db_num_rows($result) == 0) {
				
					$answerbody = "\n\n".$str_greets.$answerbody;
				
				}
				
				$str_lnk = $str_filename.'?op=sendmail&id='.$int_pid;
				
				output('<form action="'.$str_lnk.'" method="post">',true);
				output('`@E-Mail an `b'.$mail[0].'`b von `b'.getsetting('petitionemail','').'`b schreiben:`n`n');
				output('Betreff: <input type="text" name="subject" value="'.$answersubject.'">`nText:`n<textarea name="body" class="input" cols="40" rows="9">'.$answerbody.'</textarea>`n
						<input type="hidden" name="mail" value="'.urlencode($mail[0]).'"><input type="submit" class="button" value="Senden"></form>`n',true);
				addnav('',$str_lnk);
				$sql = 'UPDATE petitionmail SET seen=1 WHERE petitionid='.$int_pid;
				db_query($sql);
				
			}
		}
			
		if ($_GET['viewpageinfo']){
			output('`n`n`@Seiten Info:`&`n');
			$row['pageinfo']=stripslashes($row['pageinfo']);
			$body = HTMLEntities($row['pageinfo']);
			$body = preg_replace("'([[:alnum:]_.-]+[@][[:alnum:]_.-]{2,}([.][[:alnum:]_.-]{2,})+)'i","<a href='mailto:\\1?subject=RE: Hilfeanfrage&body=".str_replace("+"," ",URLEncode("\n\n----- Deine Anfrage -----\n".$row[body]))."'>\\1</a>",$body);
			$body = preg_replace("'([\\[][[:alnum:]_.-]+[\\]])'i","<span class='colLtRed'>\\1</span>",$body);
			$output.='<span style="font-family: fixed-width">'.nl2br($body).'</span>';
		}	
		if ($row[status]==0) {
			$sql = 'UPDATE petitions SET status=1 WHERE petitionid='.$int_pid;
			$result = db_query($sql);
		}
		
	break;
	// END einzelne Anfrage anzeigen
	
	// Anfragenliste
	default:
		
		// Veraltete Anfragen löschen
		$sql = 'SELECT petitionid FROM petitions WHERE status=2 AND date<"'.date('Y-m-d H:i:s',strtotime(date("r")."-7 days")).'"';
		$result = db_query($sql);
		
		while ($row = db_fetch_assoc($result)) {
			db_query('DELETE FROM petitionmail WHERE petitionid="'.$row['petitionid'].'"');
		}
		
		$sql = 'DELETE FROM petitions WHERE status=2 AND date<"'.date('Y-m-d H:i:s',strtotime("-7 days")).'"';
		db_query($sql);
		// END veraltete Anfragen löschen
		
		// Anfragen-Statusänderungen
		if ( isset($_GET['setstat']) ) {
			$sql = 'UPDATE petitions SET status="'.(int)$_GET['setstat'].'" WHERE petitionid='.(int)$_GET['id'];
			db_query($sql);
		}
		
		if ( isset($_GET['setkat']) ) {
			$sql = 'UPDATE petitions SET kat="'.(int)$_GET['setkat'].'" WHERE petitionid='.(int)$_GET['id'];
			db_query($sql);
		}
		
		if ( isset($_GET['setprio']) ){
			$sql = 'UPDATE petitions SET prio="'.(int)$_GET['setprio'].'" WHERE petitionid='.(int)$_GET['id'];
			db_query($sql);
		}
		// END Anfragen-Statusänderungen
		
		// Liste:		
		$sql = 'SELECT 		p.*,
							a.name,
							IF(petitionmail.petitionid > 0,COUNT(*),0) AS petmails 
				FROM 		petitions p 
				LEFT JOIN 	petitionmail 
					USING	(petitionid)
				LEFT JOIN 	accounts a
					ON 		a.acctid = p.author 
				'.( $_GET['kat'] > -1 && !empty($_GET['kat']) ? 'WHERE p.kat='.(int)$_GET['kat']:'').'
				GROUP BY 	p.petitionid 
				ORDER BY 	p.status ASC, p.prio DESC, p.lastact DESC, p.date DESC';
			
		$result = db_query($sql);
		
		$kat_recent = ( !empty($_GET['kat']) ) ? $_GET['kat'] : -1;
		
		addnav('Aktualisieren',$str_filename.'?kat='.$_GET['kat']);
			
		output('`bKategorie:`b `i'.(($kat_recent>-1)?$ARR_PETITION_KATS[$kat_recent]:'Alle').'`i`n`n',true);
					
		addnav('Kategorien');
		addnav('Alle',$str_filename);		
		foreach($ARR_PETITION_KATS as $k=>$v) {
			
			addnav($v,$str_filename.'?kat='.$k);		
			
		}
		
		$str_trclass = 'trdark';
		
		$str_out = '`c<table border="0" cellspacing="3" cellpadding="3">
						<tr class="trhead">
							<td>Ops</td><td>Num</td><td>Von</td><td>Datum</td><td>Status</td><td>Komm.</td><td>IP</td><td>Kat</td>
						</tr>';
						
		while( $row = db_fetch_assoc($result) ){
			
			$str_trclass = ($str_trclass == 'trdark' ? 'trlight' : 'trdark');
			
			$str_out .= '<tr class="'.$str_trclass.'">
							<td>
								['.create_lnk('`bAnz.`b',$str_filename.'?op=view&id='.$row['petitionid'].'&kat='.$kat_recent).'|'
								.create_lnk('Del.',$str_filename.'?op=del&id='.$row['petitionid'],true,false,'Diese Anfrage wirklich löschen?').'|'
								.create_lnk('Ungel.',$str_filename.'?setstat=0&id='.$row['petitionid'].'&kat='.$kat_recent).'|'
								.create_lnk('Gel.',$str_filename.'?setstat=1&id='.$row['petitionid'].'&kat='.$kat_recent).'|'
								.create_lnk('Geschl.',$str_filename.'?setstat=2&id='.$row['petitionid'].'&kat='.$kat_recent).']
							</td>
							<td>'.($row['prio'] ? '`^' : '').$row['petitionid'].'</td>
							<td>';
		
			if (empty($row['name'])){
				$str_out .= preg_replace("'[^a-zA-Z0-91234567890\\[\\]= @.!,?-]'","",substr($row['body'],0,strpos($row['body'],"[email")));
			}
			else{
				$str_out .= $row['name'];
			}
			
			$str_out .= '	</td>
							<td>'.date('d.m.Y H:i:s',strtotime($row['date'])).'</td>
							<td>'.$statuses[$row['status']].($row['lastact']>max($session['lastlogoff'],$session['petitions'][$row['petitionid']])?'`4*`0':'').'</td>
							<td>'.$row['commentcount'].'</td>
							<td>'.$row['IP'].'</td>	
							<td>'.$ARR_PETITION_KATS[$row['kat']].'</td>
						</tr>';
			
		}
		// END Listenschleife
		
		$str_out .= '</table>`c
						`i(Geschlossene Anfragen werden nach 7 Tagen automatisch gelöscht)`i
						`n`bLegende:`b`nUngelesen: Niemand arbeitet bisher an diesem Problem.
						`nGelesen: Es arbeitet jemand an diesem Problem.
						`nGeschlossen: Diese Anfrage wurde bearbeitet. Es sollte keine weitere Arbeit mehr nötig sein.`n`n
						Wenn eine Anfrage gelesen wird, wird sie automatisch als gelesen markiert, wenn sie nicht schon als geschlossen markiert war. 
						Wenn du ein Problem nicht lösen kannst, markiere die Anfrage wieder als ungelesen, damit 
						ein anderer dem Spieler helfen kann.`n
						Wenn eine Anfrage erfolgreich bearbeitet wurde, markiere sie als geschlossen. Sie wird nach 7 Tagen dann automatisch gelöscht.';
						
		output($str_out,true);
		
	break;
	// END Anfragenliste
}

page_footer();
?>