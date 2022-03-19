<?php
/**
* login.php:	Script zum Login in das Spiel. 
* @author LOGD-Core, modified by Drachenserver-Team
* @version DS-E V/2
*/

require_once('common.php');

// Abgelaufene Sessions ausloggen
$sql = 'UPDATE accounts SET loggedin=0 WHERE loggedin=1 AND superuser=0 AND !('.user_get_online().') ';
db_query($sql);

// Wenn Name gegeben ( = Loginversuch )
if ( !empty($_POST['name']) )
{
	// Wenn wir bereits eingeloggt sind
    if ($session['loggedin'])
    {
        redirect("badnav.php");
    }
	   
		
	$str_name = $_POST['name'];
	$str_pass = $_POST['hidden_pw'];
	
	// Filter auf diesen PC überprüfen
	checkban();
    
	// Nach Account mit eingegebenen Daten suchen        
    $sql = "SELECT acctid,login,emailvalidation,superuser,emailaddress,lastip,uniqueid FROM accounts WHERE login = '$str_name' AND password='$str_pass' AND locked=0";
    $result = db_query($sql);
	
	// Kein Account mit diesen Daten gefunden!
	if(!db_num_rows($result))
	{
		$session['message']="`4Fehler: Login-Daten waren ungültig.`0";
		
		// Wenn PW-Feld nicht auf NULL gesetzt wird: Lässt darauf schließen, dass JS deaktiviert
		if(strlen($_POST['password']) > 0) {
			$session['message'] .= '`n`^Für einen korrekten Login muss JavaScript aktiviert sein!`nUm weitere Informationen dazu zu erhalten, werfe einen Blick in die technische FAQ oder schreibe eine Anfrage ; )`0';
		}
						
		// Überprüfen, auf welchen Account sich Loginversuch bezogen haben könnte
		$sql = "SELECT acctid FROM accounts WHERE login='$str_name'";
		$result = db_query($sql);
		
		// Account gefunden
		if (db_num_rows($result)>0)
		{
			// Wenn wir mehrere Accounts unter diesem Namen haben
			while ($row=db_fetch_assoc($result))
			{
				// Faillog hinzufügen
				$sql = "INSERT INTO faillog VALUES (0,now(),'".addslashes(serialize($_POST))."','{$_SERVER['REMOTE_ADDR']}','{$row['acctid']}','{$_COOKIE['lgi']}')";
				db_query($sql);
				
				$sql = "SELECT faillog.*,accounts.superuser,name,login FROM faillog INNER JOIN accounts ON accounts.acctid=faillog.acctid WHERE ip='{$_SERVER['REMOTE_ADDR']}' AND date>'".date("Y-m-d H:i:s",strtotime(date("r")."-1 day"))."'";
				$result2 = db_query($sql);
				$c=0;
				$alert="";
				$su=false;
				while ($row2=db_fetch_assoc($result2))
				{
					if ($row2['superuser']>0)
					{
						$c+=1;
						$su=true;
					}
					$c+=1;
					$alert.="`3{$row2['date']}`7: Failed attempt from `&{$row2['ip']}`7 [`3{$row2['id']}`7] to log on to `^{$row2['login']}`7 ({$row2['name']}`7)`n";
				}
				if ($c>=20)
				{
					setban(0,'Automatischer Systembann: Zu viele fehlgeschlagene Loginversuche.',date("Y-m-d H:i:s",strtotime(date("r")."+".($c*3)." hours")),$_SERVER['REMOTE_ADDR']);
					
					systemlog('Systemban aufgrund zu vieler fehlgeschlagener Logins, IP: '.$_SERVER['REMOTE_ADDR']);
				
					// 10 failed attempts for superuser, 20 for regular user
					if ($su)
					{
						// send a system message to admins regarding this failed attempt if it includes superusers.
						$sql = "SELECT acctid FROM accounts WHERE superuser>0";
						$result2 = db_query($sql);
						$subj = "`#{$_SERVER['REMOTE_ADDR']} failed to log in too many times!";
						for ($i=0; $i<db_num_rows($result2); $i++)
						{
							$row2 = db_fetch_assoc($result2);
							//delete old messages that
							$sql = "DELETE FROM mail WHERE msgto={$row2['acctid']} AND msgfrom=0 AND subject = '$subj' AND seen=0";
							db_query($sql);
							if (db_affected_rows()>0)
							{
								$noemail = true;
							}
							else
							{
								$noemail = false;
							}
							systemmail($row2['acctid'],"$subj","This message is generated as a result of one or more of the accounts having been a superuser account.  Log Follows:`n`n$alert",0,$noemail);
						}
						//end for
					}
					//end if($su)
				}
				//end if($c>=20)
			}
			//end while
		}	
		// end wenn account mit diesem namen vorhanden
        redirect("index.php");
		exit;
	}	// END Login failed
	
	// Logindaten stimmen, wichtigste Accountdaten abrufen
	$arr_user = db_fetch_assoc($result);
	
	// Filter auf diesen Account?
	checkban($arr_user['login'], $arr_user['lastip'], $arr_user['uniqueid'], $arr_user['emailaddress']);
	
	// Check, ob Email schon bestätigt wurde
	if ($arr_user['emailvalidation']!="" && substr($arr_user['emailvalidation'],0,1)!="x")
	{
		unset($arr_user);
		$session['user'] = array();
		$session['message']="`4Fehler: Du musst deine E-Mail Adresse bestätigen lassen, bevor du dich einloggen kannst.";
		redirect("index.php");
		
	}
	
	// Anzahl der eingeloggten Spieler ermitteln    
    $result = db_fetch_assoc(db_query("SELECT COUNT(acctid) AS onlinecount FROM accounts WHERE locked=0 AND ".user_get_online() ));
	$onlinecount = $result['onlinecount'];
	
	// Auf max. Useranzahl checken
	if ($onlinecount>getsetting("maxonline",10) && getsetting("maxonline",10)!=0 && $arr_user['superuser']==0)
	{
		unset($arr_user);
		$session['user'] = array();
		$session['message']="`4Fehler: Der Server ist voll.`0";
		redirect("index.php");
	}
	
	// Vollständige Userdaten in Session laden
	user_load($arr_user['acctid']);
	
	// Auf Wartungsmodus checken
	if(!su_check(SU_RIGHT_WARTUNG) && getsetting('wartung',0)) {
						
		$session['user'] = array();
		$session['message']="`4Fehler: `^Der Server befindet sich derzeit im Wartungsmodus.`n
								Die Administration nimmt vermutlich gerade wichtige Änderungen 
								am System vor. Bitte warte, bis der Server wieder offen ist!`0";
		redirect("index.php");
		
	}
	
	$session['loggedin']=true;
	
	// Stats
	$arr_laststats = user_get_stats('logintime,onlinetime');
	// Wenn kein korrekter Logout
	if($arr_laststats['logintime'] > 0) {
		// Jetzt updaten
		$int_timeout = (int)getsetting('LOGINTIMEOUT',900) * 0.1;
		$int_timediff = min(strtotime($session['laston_back']) + $int_timeout,time()) - $arr_laststats['logintime'];
		$arr_laststats['onlinetime'] = $arr_laststats['onlinetime'] + $int_timediff;		
	}
	$arr_laststats['logintime'] = time();
	user_set_stats($arr_laststats);
	// END Stats 
	
	// Wenn wir bereits eingeloggt sind						
	if ($session['user']['loggedin'])
	{
		saveuser();
		header("Location: {$session['user']['restorepage']}");
		exit();
	}
	
	$session['user']['loggedin'] = true;
				
	if (getsetting("logdnet",0))
	{
		//register with LoGDnet
		@file(getsetting("logdnetserver","http://lotgd.net/")."logdnet.php?addy=".URLEncode(getsetting("serverurl","http://".$_SERVER['SERVER_NAME'].dirname($_SERVER['REQUEST_URI'])))."&desc=".URLEncode(getsetting("serverdesc","Another LoGD Server"))."&version=".URLEncode($logd_version)."");
	}
	
	debuglog("Login (Loc ".$session['user']['location'].", Restatloc ".$session['user']['restatlocation']."):".($session['user']['maxhitpoints'])."LP, ".($session['user']['charm'])."CH, ".($session['user']['gems'])."Gems",0,true);
			
	// Je nach Logoutort weiterleiten
	$location = $session['user']['location'];
	$session['user']['location']=0;
	
	switch($location) {
		case USER_LOC_FIELDS: // In den Feldern
			redirect("news.php");
		break;
		
		case USER_LOC_INN:	// In der Taverne
			redirect("inn.php?op=strolldown");
		break;
	
		case USER_LOC_HOUSE:	// Im Haus
			
			// RP-Wiedererweckung
			$getit = 1;
			if($session['user']['spirits'] == RP_RESURRECTION) {
				$getit = 0;
			}
					
			$hausnr=($session['user']['restatlocation']);
			
			$sqlh = "SELECT status FROM houses WHERE houseid=$hausnr ORDER BY houseid DESC";
			$resulth = db_query($sqlh) or die(db_error(LINK));
			$rowh = db_fetch_assoc($resulth);
			$typh = $rowh['status'];
						
			redirect("houses.php?op=newday&nr=$hausnr&statush=$typh&getit=$getit");
		break;
		
		case USER_LOC_PRISON:	// Kerker
			redirect("prison.php");
		break;
		
		default:	// Timeout	
			saveuser();
			header("Location: {$session['user']['restorepage']}");
			exit();
		break;
	}
	// END Ort feststellen

}	// END if Name gegeben

// LOGOUT
else if ($_GET['op']=="logout")
{
    
    if ($session['user']['loggedin'])
    {
		
		$int_loc = (int)$_GET['loc'];
		$int_restatloc = (int)$_GET['restatloc'];
		
		// Stats
		user_set_stats( array('onlinetime'=>'onlinetime + IF(logintime>0,(UNIX_TIMESTAMP(NOW())-logintime),0)','logintime'=>0) );
		// END Stats
				        
        $sql = "SELECT bufflist FROM accounts WHERE acctid=".$session['user']['acctid']."";
        $result = db_query($sql) or die(db_error(LINK));
        $row = db_fetch_assoc($result);
        
        if ($row['bufflist'])
        {
            $row['bufflist']=unserialize($row['bufflist']);
            if ($row['bufflist']['dodo']<>"")
            {
                unset($row['bufflist']['dodo']);
                $row['bufflist']=serialize($row['bufflist']);
                
                $sql = "UPDATE accounts SET bufflist='".$row['bufflist']."' WHERE acctid =".$session[user][acctid];
                db_query($sql) or die(sql_error($sql));
                
            }
        }
        
        debuglog("Logout (Loc ".$int_loc.", Restatloc ".$int_restatloc."): ".($session['user']['maxhitpoints'])."LP, ".($session['user']['charm'])."CH, ".($session['user']['gems'])."Gems");
						
        $sql = "UPDATE accounts SET location=".$int_loc.",loggedin=0,restatlocation=".$int_restatloc." WHERE acctid = ".$session[user][acctid];
        db_query($sql) or die(sql_error($sql));
    }
    
    $session=array();
    redirect("index.php");
}

// If you enter an empty username, don't just say oops.. do something useful.
$session=array();
$session[message]="`4Fehler: Die Login-Daten waren fehlerhaft.`0";
redirect("index.php");
?>