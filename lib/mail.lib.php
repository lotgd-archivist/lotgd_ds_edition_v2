<?php

// Kategoriebezeichnungen für Anfragen
$ARR_PETITION_KATS = array(0=>"Keine","Fehler","Fragen","Fragen zu Bans","Wünsche","Unterhaltsames","Diskussionen","Hilferuf");

//by Chaosmaker
function petitionmail($subject,$body,$petition,$from,$seen=0,$to=0,$messageid=0)
{
	$subject = safeescape($subject);
	$subject=str_replace("\n",'',$subject);
	$subject=str_replace("`n",'',$subject);
	$body = safeescape($body);

	$sql = 'INSERT INTO petitionmail (petitionid,messageid,msgfrom,msgto,subject,body,sent,seen) VALUES ('.(int)$petition.','.(int)$messageid.','.(int)$from.','.(int)$to.',"'.$subject.'","'.$body.'",now(),"'.$seen.'")';
	db_query($sql);
	$sql = 'UPDATE petitions SET lastact=NOW() WHERE petitionid="'.(int)$petition.'"';
	db_query($sql);
}
//end petitionmail

function systemmail($to,$subject,$body,$from=0,$noemail=false)
{
	$subject = safeescape($subject);
	$subject=str_replace("\n",'',$subject);
	$subject=str_replace('`n','',$subject);
	$body = safeescape($body);
	//echo $subject."<br>".$body;
	$sql = 'SELECT prefs,emailaddress FROM accounts WHERE acctid="'.$to.'"';
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	db_free_result($result);
	$prefs = unserialize($row['prefs']);

	if ($prefs['dirtyemail']==false)
	{
		$subject=soap($subject);
		$body=soap($body);
	}
	
	// Stats
	if($from > 0) {
		
		user_set_stats( array('mailsent'=>'mailsent+1'), $from );
		user_set_stats( array('mailreceived'=>'mailreceived+1'), $to );
		
	}
	// END Stats

	$sql = 'INSERT INTO mail (msgfrom,msgto,subject,body,sent,ip) VALUES ('.(int)$from.','.(int)$to.',"'.$subject.'","'.$body.'",now(),"'.$_SERVER['REMOTE_ADDR'].'")';
	admin_output($sql);
	db_query($sql,false);
	$email=false;
	if(getsetting('emailonmail',0)) {
		if ($prefs['emailonmail'] && $from>0)
		{
			$email=true;
		}
		elseif($prefs['emailonmail'] && $from==0 && $prefs['systemmail'])
		{
			$email=true;
		}
		if (!is_email($row['emailaddress'])) 
		{
			$email=false;
		}
	}
	if ($email && !$noemail)
	{
		$sql = 'SELECT name FROM accounts WHERE acctid='.$from;
		$result = db_query($sql);
		$row1=db_fetch_assoc($result);
		db_free_result($result);
		if ($row1['name']!='') $fromline='From: '.preg_replace('/[`]./','',$row1['name']);
		// We've inserted it into the database, so.. strip out any formatting
		// codes from the actual email we send out... they make things
		// unreadable
		$body = preg_replace('/[`]n/', "\n", $body);
		$body = preg_replace('/[`]./', '', $body);
		mail($row['emailaddress'],'Neue LoGD Mail','Du hast eine neue Nachricht von LoGD @ http://'.$_SERVER[HTTP_HOST].dirname($_SERVER[SCRIPT_NAME]).' empfangen.\n\n'.$fromline
		.'Betreff: '.preg_replace("'[`].'",'',stripslashes($subject))
		.'Body: '.stripslashes($body).'\n'
		.'\nDu kannst diese Meldungen in deinen Einstellungen abschalten.',
		'From: '.getsetting('gameadminemail','postmaster@localhost')
		);
	}
}

function is_email($email)
{
	return preg_match('/[[:alnum:]_.-]+[@][[:alnum:]_.-]{2,}.[[:alnum:]_.-]{2,}/',$email);
}

?>