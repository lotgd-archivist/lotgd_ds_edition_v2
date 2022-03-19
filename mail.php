<?php
/*
* @desc Diese Datei ist eine Umstrukturierung der ursprünglichen mail.php
* Sie enthält alle meines Wissens verfügbaren Addons für die ursprüngliche Mail.php, wurde jedoch auf
* Geschwindigkeit getrimmt
* Folgende Features wurden implementiert:
* - Adressbuch: Adressbuch kann in den Settings an und ausgeschaltet werden (deZent und draKarr)
* - Maximale Anzahl an Kontakten festgelegt (Dragonslayer,Talion)
* - Löschen von ungelesenen/gelesenen/Systemnachrichten (Eliwood)
* - Anzeige der noch verfügbaren Zeichen in einer zu schreibenden Mail
* - Rückruf von gesendeten Nachrichten, wenn diese bisher noch nicht vom Empfänger gelesen wurden (Dragonslayer)
* - Versand aller Messages an die Mailadresse des Users
* - Es wurde außerdem versucht die Mail.php zu entschlacken und performanter
*   zu realisieren.
* leicht modifiziert von Talion, kleine Tweaks hier und dort.
* @author Kolja Engelmann for lotgd.drachenserver.de
*/
 
require_once 'common.php';

define('MAIL_DATE_FORMAT','d. M, H:i');

//Speichern der Output Variablen in einer temporären Variable
//Die Funktion output() wird nur einmal am Ende des Skripts aufgerufen, um den Overhead zu sparen
$str_output_backup = $output;
echo $str_output_backup;
unset($output);

//Javascript für die Restzeichenanzeige der nachrichten
$output = '<script language="JavaScript">
<!--
function CountMax($var) 
{
	var wert,max;
	max = $var;
	wert = max-document.mail.body.value.length;
	if (wert < 0) 
	{
		alert("Es dürfen nicht mehr als " + max + " Zeichen eingegeben werden!");
		document.mail.body.value = document.mail.body.value.substring(0,max);
		wert = max-document.mail.body.value.length;
		document.mail.rv_counter.value = wert;
	} 
	else 
	{
		document.mail.rv_counter.value = max - document.mail.body.value.length;
	}
}
//-->
</script> ';

//Bearbeite alle möglichen Optionen die von dieser Datei durchgeführt werden können
switch ($_GET['op'])
{
	//Lösche eine Nachricht mit einer speziellen ID
	case 'del':
	{
		$sql = 'DELETE FROM mail WHERE msgto=\''.$session['user']['acctid'].'\' AND messageid=\''.$_GET['id'].'\'';
		db_query($sql);
		$session['message'] = 'Die Nachricht wurde erfolgreich gelöscht';
		header('Location: mail.php');
		exit();
	}
	break;
	//Lösche mehrere Nachrichten
	case 'process':
	{
		if(isset($_POST['deletemarked']))
		{
			//Überprüfen, ob überhaupt mehrere Nachrichten zum löschen markiert wurden
			if (!is_array($_POST['msg']) || count($_POST['msg'])<1)
			{
				$session['message'] = '`$`bEs wurden keine Nachrichten ausgewählt, es wurde nichts gelöscht`b`0';
			}
			else
			{
				//Wenn die gewählten Nachrichten solche sind, die vom Verfasser zurückgerufen werden sollen
				if($_GET['revoke_messages']==1)
				{
					//Lösche alle markierten Nachrichten
					$sql = 'DELETE FROM mail WHERE msgfrom=\''.$session['user']['acctid'].'\' AND seen=0 AND messageid IN (\''.join('\',\'',$_POST['msg']).'\')';
				}
				else
				{
					//Lösche alle markierten Nachrichten
					$sql = 'DELETE FROM mail WHERE msgto=\''.$session['user']['acctid'].'\' AND messageid IN (\''.join('\',\'',$_POST['msg']).'\')';
				}
				db_query($sql);
				$int_affected = db_affected_rows();
				$session['message'] = 'Die '.$int_affected.' markierten Nachrichten wurden erfolgreich gelöscht';
				
			}
			header('Location: mail.php');
			exit();
		}
		elseif (isset($_POST['message2mail']))
		{
			//Überprüfen, ob überhaupt mehrere Nachrichten zum Versand markiert wurden
			if (!is_array($_POST['msg']) || count($_POST['msg'])<1)
			{
				$session['message'] = '`$`bEs wurden keine Nachrichten ausgewählt, es konnte somit nichts versendet werden!`b`0';
			}
			elseif(is_email($session['user']['emailaddress'])==false)
			{
				$session['message'] = '`$`bDu hast keine gültige E-Mail-Adresse hinterlegt, deswegen können Dir keine Mails zugesendet werden.`b`0';
				header('Location: mail.php');
				exit();
			}
			else 
			{
				//Mails selektieren
				$sql = 'SELECT mail.subject,mail.body,mail.sent,accounts_from.login AS msg_from, accounts_to.login AS msg_to FROM mail LEFT JOIN accounts accounts_from ON accounts_from.acctid=mail.msgfrom LEFT JOIN accounts accounts_to ON accounts_to.acctid=mail.msgto WHERE mail.messageid in (\''.join('\',\'',$_POST['msg']).'\')';
				//Wenn die gewählten Nachrichten solche sind, die vom Verfasser zurückgerufen werden sollen

				$query_result = db_query($sql);
				$int_affected = db_affected_rows();

				$str_mailbody = "Deine YoMs per Mail:\n\n";
				
				//Mailbody erzeugen
				while ($arr_message = db_fetch_assoc($query_result))
				{
					$str_mailbody .= 'Datum: '.$arr_message['sent']."\n";
					$str_mailbody .= 'Von: '.$arr_message['msg_from']."\n";
					$str_mailbody .= 'An: '.$arr_message['msg_to']."\n";
					$str_mailbody .= 'Betreff: '.$arr_message['subject']."\n";
					$str_mailbody .= "---\n";
					$str_mailbody .= $arr_message['body']."\n";
					$str_mailbody .= "---\n\n\n";
				}
				if(mail($session['user']['emailaddress'],'Deine Yoms per Mail',$str_mailbody,'From: '.getsetting('gameadminemail','postmaster@localhost')))
				{
					$session['message'] = 'Die '.$int_affected.' markierten Nachrichten wurden erfolgreich an Deine Mailadresse versendet.';
					//Lösche alle markierten Nachrichten
					$sql = 'DELETE FROM mail WHERE msgto=\''.$session['user']['acctid'].'\' AND messageid IN (\''.join('\',\'',$_POST['msg']).'\')';
					db_query($sql);
				}
				else 
				{
					$session['message'] = 'Leider trat beim versenden der Mail ein Fehler auf!';
				}				
			}
			header('Location: mail.php');
			exit();
		}
	}
	break;
	//Lösche spezielle Mails: Gelesene/Ungelesene/Systemmails
	case 'del_special_mails':
	{
		//Lösche Systemnachrichten
		switch($_POST['delart'])
		{
			//Lösche Systemmails
			case 'sys':
			{
				$sql = 'DELETE FROM mail WHERE msgto=\''.$session['user']['acctid'].'\' AND msgfrom=0';
				db_query($sql);
				$int_affected = db_affected_rows();
				$session['message'] = 'Alle '.$int_affected.' Systemnachrichten wurden gelöscht';
			}
			break;
			//Lösche ungelesene nachrichten
			case 'ugdel':
			{
				$sql = 'DELETE FROM mail WHERE msgto=\''.$session['user']['acctid'].'\' AND seen=0';
				db_query($sql);
				$int_affected = db_affected_rows();
				$session['message'] = 'Alle '.$int_affected.' ungelesenen Nachrichten wurden erfolgreich gelöscht';
			}
			break;
			//Lösche gelesene Nachrichten
			case 'gdel':
			{
				$sql = 'DELETE FROM mail WHERE msgto=\''.$session['user']['acctid'].'\' AND seen=1';
				db_query($sql);
				$int_affected = db_affected_rows();
				$session['message'] = 'Alle '.$int_affected.' gelesenen Nachrichten wurden erfolgreich gelöscht';
			}
			break;
		}
		header('Location: mail.php');
		exit();
	}
	break;
	//Sende eine Mail
	case 'send':
	{
		
		// Dieses Formular wurde bereits einmal abgeschickt
		if($_POST['mailcounter'] != $session['mailcounter']) {
			
			$session['message'] = 'Deine Nachricht wurde gesendet!`n';
			header('Location:mail.php');
			exit;
			
		}
		
		$session['mailcounter'] = '';
		
		// LASTON aktualisieren
		$sql = 'UPDATE accounts SET laston=NOW() WHERE acctid='.$session['user']['acctid'];
		db_query($sql);
		
		//Wenn es sich nicht um eine Antwort auf eine Anfrage handelt
		if (isset($_POST['petitionid'])==false)
		{
			//Suche nach dem Empfänger
			$sql = 'SELECT acctid,superuser FROM accounts WHERE login=\''.$_POST['to'].'\'';
			$result = db_query($sql);
			//Existiert der Empfänger?
			if (db_num_rows($result)>0)
			{
				$row1 = db_fetch_assoc($result);
				//Überprüfen, ob dem Benutzer noch Mails geschickt werden können (Mailbox voll)
				$sql = 'SELECT count(messageid) AS count FROM mail WHERE msgto=\''.$row1['acctid'].'\' and seen=0 ';
				$result = db_query($sql);
				$row = db_fetch_assoc($result);
				//Zuviele Mails in der Inbox des Empfängers
				if (($row['count']>getsetting('inboxlimit',50)) || (($row1['superuser']>0) && ($row['count']>getsetting('modinboxlimit',50))))
				{
					$output.='Die Mailbox des Empfängers ist voll! Du kannst ihm keine Nachricht schicken.';
				}
				//Mail kann geschrieben werden
				else
				{
					$_POST['subject']=closetags(str_replace('`n','',$_POST['subject']),'`c`i`b');
					$_POST['body']=str_replace('`n',"\n",$_POST['body']);
					$_POST['body']=str_replace("\r\n","\n",$_POST['body']);
					$_POST['body']=str_replace("\r","\n",$_POST['body']);
					$_POST['body']=addslashes(substr(stripslashes($_POST['body']),0,(int)getsetting('mailsizelimit',1024)));
					$_POST['body'] = closetags($_POST['body'],'`c`i`b');
					systemmail($row1['acctid'],$_POST['subject'],$_POST['body'],$session['user']['acctid']);
					$session['message'] = 'Deine Nachricht wurde gesendet!`n';
					header('Location:mail.php');
					exit;
				}
			}
			//Der Empfänger konnte nicht gefunden werden
			else
			{
				$output.='Der Empfänger konnte nicht gefunden werden. Bitte versuche es nochmal.';
			}
		}
		//Es handelt sich um die Antwort auf eine Anfrage
		else
		{
			$sql = 'SELECT count(messageid) AS count FROM petitionmail WHERE petitionid=\''.$_POST['petitionid'].'\' AND msgto=\''.$session['user']['acctid'].'\'';
			$row = db_fetch_assoc(db_query($sql));
			//Handelt es sich um die Anfrage des Users?
			if ($row['count']==0)
			{
				$output .= 'Du kannst nur zu deinen eigenen Anfragen etwas schreiben!';
			}
			//Der User darf antworten
			else
			{
				$_POST['subject']=closetags(str_replace('`n','',$_POST['subject']),'`c`i`b');
				$_POST['body']=str_replace('`n',"\n",$_POST['body']);
				$_POST['body']=str_replace("\r\n","\n",$_POST['body']);
				$_POST['body']=str_replace("\r","\n",$_POST['body']);
				$_POST['body']=substr($_POST['body'],0,(int)getsetting('mailsizelimit',1024));
				$_POST['body'] = closetags($_POST['body'],'`c`i`b');
				petitionmail($_POST['subject'],$_POST['body'],$_POST['petitionid'],$session['user']['acctid']);
				$output.='Deine Nachricht wurde gesendet!`n';
			}
		}
	}
	break;
	//Zeige alle gesendeten Nachrichten des Nutzers, die noch nicht vom Empfänger gelesen wurden
	case 'outbox':
	{
		$output .= 'Hier kannst Du von Dir gesendete Mails zurückrufen, wenn diese vom Empfänger noch nicht gelesen wurden`n';
		//Selektiere alle Mails
		$sql = 'SELECT mail.subject,mail.messageid,accounts.name,mail.msgto,mail.seen,mail.sent, petitionmail.petitionid FROM mail LEFT JOIN petitionmail USING(messageid) LEFT JOIN accounts ON accounts.acctid=mail.msgto WHERE mail.msgfrom=\''.$session['user']['acctid'].'\' and mail.seen=0 ORDER BY mail.sent';
		$result = db_query($sql);
		//Anzahl der Tupel bestimmen
		$int_mails = db_num_rows($result);
		//Wenn mindestens eine Mail vorhanden ist
		if ($int_mails>0)
		{
			$output.='<form action="mail.php?op=process&revoke_messages=1" method="POST">';
			$output.='<table>';
			
			$recent_date = date('dm',time());
			
			//Stelle jede Mail dar
			for ($i=0;$i<$int_mails;$i++)
			{
				$row = db_fetch_assoc($result);
				
				$senttime = strtotime($row['sent']);
												
				if( date('dm',$senttime) == $recent_date ) 
				{
					$sent = 'Heute, '.date('H:i',$senttime);
				}
				else 
				{
					$sent = date(MAIL_DATE_FORMAT,$senttime);
				}
																
				//Gib alle Details aus
				$output.='
				<tr>
					<td nowrap><input id="checkbox'.$i.'" type="checkbox" name="msg[]" value="'.$row['messageid'].'"><img src="images/newscroll.GIF" width="16" height="16" alt="ungelesen"></td>
					<td>'.$row['subject'].'</td>
					<td>'.$row['name'].'</td>
					<td>'.$sent.'</td>
				</tr>';
			}
			$output.='</table>';
			//Bei einem Klick auf den Button wird jede Mail markiert
			$output.='<input type="button" value="Alle markieren" class="button" onClick="';
			for ($i=$i-1;$i>=0;$i--)
			{
				$output.='document.getElementById(\'checkbox'.$i.'\').checked=true;';
			}
			$output.='">';
			$output.='<input type="submit" class="button" name="deletemarked" value="Markierte löschen"></form>';
		}
		else
		{
			$output.='`iEntweder Du hast noch keine Mails versandt oder sie wurden bereits alle gelesen.`i';
		}
	}
	break;
	//Lies eine angegebene Nachricht
	case 'read':
	{
			
		//Setze die Mail auf den Status "gelesen"
		$sql = 'UPDATE mail SET seen=1 WHERE  msgto=\''.$session['user']['acctid'].'\' AND messageid=\''.$_GET['id'].'\'';
		db_query($sql);
		//Hole Daten zur Mail und deren Erzeuger und Empfänger aus der DB
		$sql = 'SELECT mail.*,accounts.name,accounts.acctid, petitionmail.petitionid FROM mail LEFT JOIN petitionmail USING(messageid) LEFT JOIN accounts ON accounts.acctid=mail.msgfrom WHERE mail.msgto=\''.$session['user']['acctid'].'\' AND mail.messageid=\''.$_GET['id'].'\'';
		$result = db_query($sql) or die(db_error(LINK));
		if (db_num_rows($result)>0)
		{
			$row = db_fetch_assoc($result);
			if ((int)$row['msgfrom']==0)
			{
				if ((int)$row['petitionid']==0)
				{
					$row['name']='`i`^System`0`i';
				}
				else
				{
					$row['name'] = '`i`^Admin`0`i';
				}
			}
			
			$row['body'] = soap(closetags($row['body'],'`b`c`i'));
			$row['body'] = strip_tags($row['body']);
			
			$output.='`b`2Absender:`b `^'.$row['name'].'`n
			`b`2Betreff:`b `^'.$row['subject'].'`n
			`b`2Gesendet:`b `^'.$row["sent"].'`n
			<img src="images/uscroll.GIF" width="182" height="11" alt="" align="center">`n
			'.str_replace("\n",'`n',$row['body']).'`n
			<img src="images/lscroll.GIF" width="182" height="11" alt="" align="center">`n
			<a href="mail.php?op=write&replyto='.$row['messageid'].'" class="motd">Antworten</a>
			<a href="mail.php?op=del&id='.$row['messageid'].'" class="motd">Löschen</a>';

			//Wenn das Adressbuch angeschaltet ist dann wird der folgende Link angezeigt
			if(getsetting('show_yom_contacts',1)==1 || $session['user']['superuser']>0)
			{
				$output.='<a href="mail.php?op=neuerkontakt2&id='.$row['acctid'].'" class="motd">Zu Kontakten</a>';
			}
		}
		//Die Nachricht konnte nicht gefunden werden
		else
		{
			$output.='Diese Nachricht wurde nicht gefunden!';
		}
	}
	break;
	//Schreib eine Mail
	case 'write':
	{
		$bool_write_allowed = true;
		$subject='';
		$body='';
		$output.='<form action="mail.php?op=send" method="POST" name="mail">';
		//Wenn die Mail eine Antwort auf eine vorhergehende Mail ist
		if ($_GET['replyto']!='')
		{
			//Lade alle Infomationen über die zu beantwortende Mail
			$sql = 'SELECT mail.body,mail.subject,accounts.login,accounts.name, accounts.laston,accounts.loggedin,accounts.activated,
							 petitionmail.petitionid FROM mail LEFT JOIN petitionmail USING(messageid) LEFT JOIN accounts ON accounts.acctid=mail.msgfrom WHERE mail.msgto=\''.$session['user']['acctid'].'\' AND mail.messageid=\''.$_GET['replyto'].'\'';
			$result = db_query($sql) or die(db_error(LINK));
			//Wenn die gesuchte Mail existiert
			if (db_num_rows($result)>0)
			{
				$row = db_fetch_assoc($result);
				if ($row['login']=='' && (int)$row['petitionid']==0)
				{
					$output.='Du kannst nicht auf eine Systemnachricht antworten.`n';
					$row=array();
					$bool_write_allowed = false;
				}
			}
			else
			{
				$output.='Die Nachricht, auf die Du antworten willst existiert nicht!`n';
				$bool_write_allowed = false;
			}
		}
		//Wenn eine neue Nachricht erstellt werden soll
		if ($_GET['to']!='')
		{
			//Überprüfe, ob der Empfänger existiert
			$sql = 'SELECT login,name,laston,loggedin,activated FROM accounts WHERE login=\''.$_GET['to'].'\'';
			$result = db_query($sql) or die(db_error(LINK));
			//Wurde die Person gefunden?
			if (db_num_rows($result)>0)
			{
				$row = db_fetch_assoc($result);
			}
			else
			{
				$output.='Diese Person konte nicht gefunden werden`n';
				$bool_write_allowed = false;
			}
		}
		//Wenn der Empfänger gefunden wurde
		if (is_array($row))
		{
			//Check whether this mail is an answer to aMail, and avoid this RE: RE: RE: stuff
			if ($row['subject']!='')
			{
				$subject=$row['subject'];
				if (substr($subject,0,4)!='RE: ') 
				{
					$subject='RE: '.$subject;
				}
			}
			if ($row['body']!='')
			{
				$body="\n\n---Vorherige Botschaft---\n".$row['body'];
			}
		}
		if ($row['petitionid']>0)
		{
			$output.='`2An: `^`iAdmin`i`n';
		}
		elseif ($row['login']!='')
		{
			$str_online = '';
			if($row['activated'] != USER_ACTIVATED_STEALTH) {
				$str_online = (user_get_online(0,$row) ? ' `@(online)`0' : ' `4(offline)`0');
			}
			
			$output.='<input type="hidden" name="to" value="'.HTMLEntities($row['login']).'">
			`2An: `^'.$row['name'].$str_online.'`n';
		}
		else
		{
			$output.='`2An: ';
			$string='%';
			$int_length = strlen($_POST['to']);
			//The 256 is a security setting, so nobody can enter to high values
			for ($x=0;$x<$int_length && $x < 256;$x++)
			{
				$string .= substr($_POST['to'],$x,1).'%';
			}
			$sql = 'SELECT login,name,laston,loggedin,activated FROM accounts WHERE name LIKE \''.addslashes($string).'\' AND locked=0 ORDER BY login';
			$result = db_query($sql);
			
			$int_result_count = db_num_rows($result);
			
			if ($int_result_count==1)
			{
				$row = db_fetch_assoc($result);
				
				$str_online = '';
				if($row['activated'] != USER_ACTIVATED_STEALTH) {
					$str_online = (user_get_online(0,$row) ? ' `@(online)`0' : ' `4(offline)`0');
				}
				
				$output.='<input type="hidden" name="to" value="'.HTMLEntities($row['login']).'">
				`^'.$row['name'].$str_online.'`n';
			}
			else if($int_result_count == 0) {
				
				$output.='Diese Person konte nicht gefunden werden`n';
				$bool_write_allowed = false;
			}
			else
			{
				$output.='<select name="to">';
				$int_x = db_num_rows($result);
				for ($i=0;$i<$int_x;$i++)
				{
					$row = db_fetch_assoc($result);
					$str_online = '';
					if($row['activated'] != USER_ACTIVATED_STEALTH) {
						$str_online = (user_get_online(0,$row) ? ' (online)' : ' (offline)');
					}
					$output .= '<option value="'.HTMLEntities($row['login']).'">'.preg_replace('/[`]./','',$row['name']).$str_online;
				}
				$output.='</select>`n';
			}
		}
		
		if($bool_write_allowed) {
			// Doppeltes Verschicken einer Mail verhindern by talion
			$session['mailcounter'] = md5(time());
			
			// Formatierungstags in Betreff und Mail maskieren
			$subject = str_replace('`','{#96}',$subject);
			$body = str_replace('`','{#96}',$body);
						
			$output.='<input type="hidden" name="mailcounter" value="'.$session['mailcounter'].'">';
			
			$output.='`2Betreff:';
			$output.=('<input name="subject" value="'.HTMLEntities($subject).HTMLEntities(stripslashes($_GET['subject'])).'">&nbsp;&nbsp;noch <input typ="hidden" name="rv_counter" size="'.strlen(getsetting('mailsizelimit' ,0)).'" value="'.getsetting('mailsizelimit' ,0).'" readonly> Zeichen übrig.');
			$output.='`n`2Text:`n';
			$output.='<textarea name="body" class="input" cols="40" rows="9" OnFocus="CountMax('.getsetting('mailsizelimit' ,0).');" OnClick="CountMax('.getsetting('mailsizelimit' ,0).');" OnChange="CountMax('.getsetting('mailsizelimit' ,0).');" onKeydown="CountMax('.getsetting('mailsizelimit' ,0).');" onKeyup="CountMax('.getsetting('mailsizelimit' ,0).');" wrap="virtual">'.HTMLEntities($body).HTMLEntities(stripslashes($_GET['body'])).'</textarea><br>';
			$output.='<input type="submit" class="button" value="Senden">`n';
			if ($row['petitionid']>0)
			{
				$output.='<input type="hidden" name="petitionid" value="'.$row['petitionid'].'">';
			}
			$output.='</form>';
		}
	}
	break;
	//Empfängersuche
	case 'address':
	{
		$output.='<form action="mail.php?op=write" method="POST">
		`b`2Empfänger:`b`n
		`2<u>A</u>n: <input name="to" accesskey="a"> <input type="submit" class="button" value="Search"></form>';
	}
	break;
	case 'buch':
	{
		/**********************************************
		*Diese Box darf nicht entfernt werden!        *
		*-------------------------------------        *
		*Adressbuch von deZent und draKarr            *
		*Version: 0.5                                 *
		*www.plueschdrache.de                         *
		*etwas verändert von talion..				  *
		**********************************************/

		$sql = 'SELECT y.row_id,y.player,y.descr,a.login,a.name,a.loggedin,a.laston,a.activated FROM yom_adressbuch y LEFT JOIN accounts a ON a.acctid=y.player WHERE y.acctid='.$session['user']['acctid'].' ORDER BY login;';
		$result = db_query($sql);
		$menge = db_num_rows($result);
		$max_yom_contacts = getsetting('max_yom_contacts',1);
		//Number of contacts left;
		$yom_contacts_left = $max_yom_contacts;

		$output.='`c`bAdressbuch`b`c`n`n';
		if (!$menge)
		{
			$output.='`n`$ Du hast noch keine Kontakte gespeichert`7`n';
		}
		else
		{
			$yom_contacts_left-=$menge;
			$output.='<table>';
			for ($i=0;$i<$menge;$i++)
			{
				$k = db_fetch_assoc($result);
				$loggedin=user_get_online(0,$k);
				$output.='
				<tr>
				   <td><a href="mail.php?op=write&to='.$k['login'].'">&raquo '.$k['name'].'</a></td>
                   <td>&nbsp;&nbsp;</td>
                   <td> '.$k['descr'].'</td>
				   <td>&nbsp;&nbsp;</td>
				   <td>'.(($loggedin)?'`@online':'`4offline').'</td>
				   <td>&nbsp;&nbsp;</td>
                   <td><a href="mail.php?op=editkontakt1&row='.$k['row_id'].'">`$[edit]`7</a> </td>
				   <td><a href="mail.php?op=delkontakt&row='.$k['row_id'].'">`$[del]`7</a> </td>
              	</tr>';
			}
			$output.='</table>';
		}
		$output.='`n`n';
		if($yom_contacts_left>0)
		{
			$output.='<a href="mail.php?op=neuerkontakt" class="motd">Neuer Kontakt('.$yom_contacts_left.'/'.$max_yom_contacts.')</a>';
		}
		else
		{
			$output.='Du darfst leider keine Kontakte mehr hinzufügen, Du hast das Maximum von '.$max_yom_contacts.' bereits überschritten.';
		}
	}
	break;
	//Eingabe eines neuen Kontakts in das Adressbuch
	case 'neuerkontakt':
	{
		$output.='<form action="mail.php?op=neuerkontakt2" method="POST">
		`b`2Name:`b`n
		`2<u>A</u>n: <input name="to" accesskey="a" value="'.$_GET['name'].'"> <input type="submit" class="button" value="Kontakt suchen"></form>';	
	}
	break;
	//Suche des Kontakts in der Datenbank
	case 'neuerkontakt2':
	{
		$sql = 'SELECT COUNT(*) AS anzahl FROM yom_adressbuch WHERE acctid='.$session['user']['acctid'];
		$res = db_query($sql);
		$a = db_fetch_assoc($res);

		if($a['anzahl'] >= getsetting('max_yom_contacts',1))
		{
			$output.='`4Du hast mit '.$a['anzahl'].' bereits das Limit von '.getsetting('max_yom_contacts',1).' Kontakten erreicht!';
		}
		else
		{
			if($_POST['to'])
			{
				$to = $_POST['to'];

				$output.='`2Name: ';
				$string='%';
				$int_length = strlen($to);
				for ($x=0;$x<$int_length;$x++)
				{
					$string .= substr($to,$x,1).'%';
				}
				$sql = 'SELECT name,acctid FROM accounts WHERE name LIKE \''.addslashes($string).'\' AND locked=0 ORDER BY login';
				$result = db_query($sql);
			}
			else
			{
				$sql = 'SELECT name,acctid FROM accounts WHERE acctid='.(int)$_GET['id'].' AND locked=0';
				$result = db_query($sql);
			}

			$output .= '<form action="mail.php?op=neuerkontakt3" method="POST">';
			$int_rows = db_num_rows($result);
			if ($int_rows==1)
			{
				$row = db_fetch_assoc($result);
				$output .= '<input type="hidden" name="to" value="'.$row['acctid'].'">';
				$output.='`^'.$row['name'].'`n';
			}
			elseif($int_rows == 0)
			{
				$output.='`4Es gibt keinen Spieler mit diesem Namen!';
			}
			else
			{
				$output .= '<select name="to">';
				for ($i=0;$i<$int_rows;$i++)
				{
					$row = db_fetch_assoc($result);
					$output.='<option value="'.$row['acctid'].'">';
					$output.= preg_replace('/[`]./','',$row['name']);
				}
				$output.='</select><br>`n';
			}
			$output.='<br>Beschreibung [max.80]:<input type="text" name="descr" maxlenght="80" size="13">
			<br><br><input type="submit" name="s1" value="Kontakt speichern"></form><br />';
		}
	}
	break;
	//Speichern des Kontakts im Adressbuch
	case 'neuerkontakt3':
	{
		$sql = 'SELECT COUNT(*) as menge FROM yom_adressbuch WHERE acctid='.$session['user']['acctid'].' AND player='.(int)$HTTP_POST_VARS['to'];
		$result = db_query($sql);
		$anzahl = mysql_result($result,0,'menge');
		if ($anzahl>0)
		{
			$output.='`n`n`$`bDieser Kontakt ist bereits gespeichert!`7`b';
		}
		else
		{
			$descr = mysql_escape_string($_POST['descr']);
			$sql='INSERT INTO yom_adressbuch SET acctid='.$session['user']['acctid'].', player='.(int)$_POST['to'].', descr=\''.$descr.'\'';
			db_query($sql);
			$output.='`n`n`@`bDer Kontakt wurde gespeichert.`b`7';
		}
	}
	break;
	//Editieren des Kontakts, Ausgabe der Maske
	case 'editkontakt1':
	{
		if($_GET['row'])
		{
			$sql = "SELECT y.row_id,y.descr,a.name FROM yom_adressbuch y, accounts a WHERE y.row_id=".(int)$_GET['row']." AND a.acctid=y.player";
			$res = db_query($sql);

			if(db_num_rows($res))
			{
				$k = db_fetch_assoc($res);

				$output.='<br>Name: '.$k['name'].'
				<form action="mail.php?op=editkontakt2&row='.$k['row_id'].'" method="POST">
				<br>Beschreibung [max.80]:<input type="text" name="descr" maxlenght="80" size="13" value="'.$k['descr'].'">
				<br><br><input type="submit" name="s1" value="Kontakt speichern">`n
				</form>`n';
			}
		}
	}
	break;
	//Speichern des editierten Kontaktes
	case 'editkontakt2':
	{
		$descr = mysql_escape_string($_POST['descr']);
		$sql='UPDATE yom_adressbuch SET descr = \''.$descr.'\' WHERE row_id='.(int)$_GET['row'];
		db_query($sql);
		$output.='`n`n`@`bDer Kontakt wurde gespeichert.`b`7';
	}
	break;
	//Löschen eines Kontaktes aus dem Adressbuch
	case 'delkontakt':
	{
		$sql='DELETE FROM yom_adressbuch WHERE row_id='.(int)$_GET['row'].' LIMIT 1 ';
		db_query($sql);
		$output.='`n`n`@`bDer Kontakt wurde gelöscht.`b`7';
	}
	break;
	//Wenn keine Operation angegeben wurde
	default:
	{
		//Ausgabe einer Statusnachricht wenn diese vorhanden ist
		$output.='`b`iMail Box`i`b`n'.$session['message'];
		//Statusnachricht löschen
		unset($session['message']);

		$sql = 'SELECT mail.subject,mail.messageid,accounts.name,mail.msgfrom,mail.seen,mail.sent, petitionmail.petitionid FROM mail LEFT JOIN petitionmail USING(messageid) LEFT JOIN accounts ON accounts.acctid=mail.msgfrom WHERE mail.msgto=\''.$session['user']['acctid'].'\' ORDER BY mail.seen,mail.sent DESC';
		$result = db_query($sql);
		
		// Anzahl der ungelesenen Nachrichten bestimmen
		$int_unseen = 0;
		
		//Anzahl der Tupel bestimmen
		$int_mails = db_num_rows($result);
		//Wenn mindestens eine Mail vorhanden ist
		if ($int_mails>0)
		{
			$output.='<form action="mail.php?op=process" method="POST">';
			$output.='<table>';
			
			$recent_date = date('dm',time());
			
			//Stelle jede Mail dar
			for ($i=0;$i<$int_mails;$i++)
			{
				$row = db_fetch_assoc($result);
				//Falls die Nachricht von System stammt
				if ((int)$row['msgfrom']==0)
				{
					//Stammt die nahcricht vom System?
					if ((int)$row['petitionid']==0)
					{
						$row['name']='`i`^System`0`i';
					}
					//Stammt die nachricht von einem Admin
					else
					{
						$row['name']='`i`^Admin`0`i';
					}
				}
				
				$senttime = strtotime($row['sent']);
												
				if( date('dm',$senttime) == $recent_date ) 
				{
					$sent = 'Heute, '.date('H:i',$senttime);
				}
				else 
				{
					$sent = date(MAIL_DATE_FORMAT,$senttime);
				}
				
				if(!$row['seen']) {
					$int_unseen++;
				}
				
				//Gib alle Details aus
				$output.='
				<tr>
					<td nowrap>
					<input id="checkbox'.$i.'" type="checkbox" name="msg[]" value="'.$row['messageid'].'">
					<img src="images/'.($row['seen']?'old':'new').'scroll.GIF" width="16" height="16" alt="'.($row['seen']?'Alt':'Neu').'">
					</td>
					<td><a href="mail.php?op=read&id='.$row['messageid'].'">'.$row['subject'].'</a></td>
					<td><a href="mail.php?op=read&id='.$row['messageid'].'">'.$row['name'].'</a></td>
					<td><a href="mail.php?op=read&id='.$row['messageid'].'">'.($row['seen']?'':'`^').$sent.'</a></td>
				</tr>';
			}
			$output.='</table>';
			//Bei einem Klick auf den Button wird jede Mail markiert
			$output.='<input type="button" value="Alle markieren" class="button" onClick="';
			for ($i=$i-1;$i>=0;$i--)
			{
				$output.='document.getElementById(\'checkbox'.$i.'\').checked=true;';
			}
			$output.='">';
			$output.='<input type="submit" class="button" name="deletemarked" value="Markierte löschen">';
			//Show this link only if it is activated or for admins
			if($session['user']['superuser']>0 || getsetting('message2mail_activated',false)==true)
			{
				$output.='<input type="submit" class="button" name="message2mail" value="Markierte per Mail zusenden (aus YoM löschen)">';
			}
			$output .= '</form>';

			// Readed/Unreaded/Systemmails delete, based on a Idea from Amerilion, Code by Eliwood
			// Version 1.1
			$output.='
			<form action="mail.php?op=del_special_mails" method="POST">
				<select name="delart">
					<option value="sys">Lösche Systemnachrichten</option>
					<option value="ugdel">Lösche ungelesene Nachrichten</option>
					<option value="gdel">Lösche gelesene Nachrichten</option>
				</select>
				<input type="submit" class="button" value="Submit">
			</form>';			
		}
		else
		{
			$output.='`iDu hast momentan keine Mails!`i';
		}

		//Zeige das Nachrichtenlimit an
		//Für Admins
		if ($session['user']['superuser']>0)
		{
			if($int_unseen>=getsetting('modinboxlimit',50))
			{
				$output.='`n`n`b`4Du hast '.$int_unseen.' ungelesene Nachrichten in deiner Mailbox.`nDu kannst höchstens '.getsetting('modinboxlimit',50).' ungelesene Nachrichten hier speichern. Solange Du zu viele Nachrichten hast, kann dir niemand etwas schicken!`n';
			}
			else
			{
				$output.='`n`n`iDu hast insgesamt '.$int_mails.' Nachrichten, davon '.$int_unseen.' ungelesen.`nDu kannst höchstens '.getsetting('modinboxlimit',50).' ungelesene Nachrichten hier speichern.`nNachrichten werden nach '.getsetting('modoldmail',14).' Tagen gelöscht.';
			}
		}
		//Für normale Benutzer
		else
		{
			if($int_unseen>=getsetting('inboxlimit',50))
			{
				$output.='`n`n`b`4Du hast '.$int_unseen.' Nachrichten in deiner Mailbox.`nDu kannst höchstens '.getsetting('inboxlimit',50).' ungelesene Nachrichten hier speichern. Solange Du zu viele Nachrichten hast, kann dir niemand etwas schicken!`n';
			}
			else
			{
				$output.='`n`n`iDu hast insgesamt '.$int_mails.' Nachrichten, davon '.$int_unseen.' ungelesen.`nDu kannst höchstens '.getsetting('inboxlimit',50).' ungelesene Nachrichten hier speichern.`nNachrichten werden nach '.getsetting('oldmail',14).' Tagen gelöscht.';
			}
		}
	}
	break;
}

//Header einstellen
$str_mailname = 'Taubenschlag von '.getsetting('townname','Atrahor').'';

popup_header($str_mailname);
//Erste Tabelle auf der Seite einstellen
$main_output .= '
<table>
	<tr>
		<td><a href="mail.php" class="motd">Inbox</a></td>
		<td><a href="mail.php?op=address" class="motd">Mail schreiben</a></td>';

//Adressbuch anzeigen wenn es eingeschaltet ist, oder wenn der User ein Admin ist
if(getsetting('show_yom_contacts',1)==1 || $session['user']['superuser']>0)
{
	$main_output .= '
		<td>
			<script language="javascript">window.resizeTo(800,400);</script>
			<a href="mail.php?op=buch" class="motd">Adressbuch</a>
		</td>';
}
$main_output .= '
		<td><a href="mail.php?op=outbox" class="motd">Mail zurückrufen</a></td>';
$main_output.='
	</tr>
</table>';

//Dieser Teil hier ist lotgd.drachenserver.de spezifisch, er enthält etwas Werbung
$tail_output .= '
<div style="text-align:center; margin:30px;">
	<script type="text/javascript"><!--
	google_ad_client = "pub-3924728103525542";
	google_ad_width = 468;
	google_ad_height = 60;
	google_ad_format = "468x60_as";
	google_ad_type = "text_image";
	google_ad_channel ="8622662812";
	google_color_border = "333333";
	google_color_bg = "000000";
	google_color_link = "FFFFFF";
	google_color_url = "999999";
	google_color_text = "CCCCCC";
	//--></script>
	<script type="text/javascript"
		src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
	</script>
</div>';


//Der gesammelte HTML Quelltext wird jetzt noch einmal durch den Parser gejagd, damit alle
//Farben und Formatierungen übernommen werden
$output = appoencode($main_output.$output.$tail_output,true);

// Maskierte Formatierungstags wieder zurückverwandeln
// Workaround, nicht optimal.
$output = str_replace('{#96}','`',$output);

//Anschließend wird die Seite geschlossen!
// mod by talion: 	Userdaten nicht speichern
popup_footer(false);
?>