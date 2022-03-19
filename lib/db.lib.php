<?php

// 21072004

//do some cleanup here to make sure magic_quotes_gpc is ON, and magic_quotes_runtime is OFF, and error reporting is all but notice.
error_reporting (E_ALL ^ E_NOTICE);
if (!get_magic_quotes_gpc()){
	set_magic_quotes($_GET);
	set_magic_quotes($_POST);
	set_magic_quotes($_SESSION);
	set_magic_quotes($_COOKIE);
	set_magic_quotes($HTTP_GET_VARS);
	set_magic_quotes($HTTP_POST_VARS);
	set_magic_quotes($HTTP_COOKIE_VARS);
	ini_set('magic_quotes_gpc',1);
}
set_magic_quotes_runtime(0);

function set_magic_quotes(&$vars) 
{
	//eval('\$vars_val =& \$GLOBALS[$vars]$suffix;');
	if (is_array($vars)) 
	{
		reset($vars);
		while (list($key,$val) = each($vars))
			set_magic_quotes($vars[$key]);
	}
	else
	{
		$vars = addslashes($vars);
		//eval('\$GLOBALS$suffix = \$vars_val;');
	}
}

define('DBTYPE','mysql');

$dbqueriesthishit=0;
$dbtimethishit = 0;
// Wie lang soll die Liste der letzten SQL-Queries in der Session sein?
// 0 um Funktion ganz auszuschalten
$dbquerylog = 50;

// logquery: soll query temporär in session mitgeloggt werden?
function db_query($sql, $logquery=true)
{
	global $session,$dbqueriesthishit,$dbtimethishit,$dbquerylog;
	
	$dbqueriesthishit++;
	$dbtimethishit -= getmicrotime();
	$fname = DBTYPE.'_query';
	$r = $fname($sql);
	
	if(db_errno(LINK)) {
		$str_msg = '<i>Adresse: '.$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'].'</i>
					<pre>'.HTMLEntities($sql).'</pre>
					<b>'.db_errno(LINK).':</b> '.db_error(LINK);
		
		// Nur Systemlogeintrag vornehmen, wenn feststeht, dass nicht Systemlog den Fehler hervorruft
		if(!strpos($sql,'syslog')) {
			systemlog('`&DB-Fehler: `^'.$str_msg, 0, $session['user']['acctid']);
		}
		
		echo('<div style="font-family:Helvetica; color:darkblue;">
			<h2 align="center" style="color:green;">Don\'t Panic!</h2>
			Soeben ist durch eine äußerst unwahrscheinliche Dimensionslücke weit draußen in den unerforschten
			Einöden eines total aus der Mode gekommenen Ausläufers des westlichen Spiralarms der Galaxis
			ein Datenbankfehler im Innenleben dieses Servers aufgetreten.<br>
			Bitte kopiere den untenstehenden Fehlertext und teile ihn der Administration per Anfrage mit!
			Du solltest auch beschreiben, was du unmittelbar davor getan / angeklickt hast.<br>
			Danke für dein Verständnis!<br>
			Hier kommt die Meldung:
			<p>'.$str_msg.'</p>
			Um weiterspielen zu können, sollte ein Klick auf den Zurück-Button deines Browsers ausreichen. Falls nicht,
			schließe das Browserfenster und rufe die Adresse neu auf. Schreibe dann von der Startseite aus eine Anfrage.</div>'
			);
		exit;	
	}
		 
	$dbtimethishit += getmicrotime();
	
	if($logquery && $dbquerylog > 0) {
		if(is_array($session['debug_querylog']) && sizeof($session['debug_querylog']) >= $dbquerylog) {
			array_shift($session['debug_querylog']);
		}
		$session['debug_querylog'][] = $sql;
	}
				
	return $r;
}

function db_insert_id($link=false) 
{
	global $dbtimethishit;
	$dbtimethishit -= getmicrotime();
	$fname = DBTYPE.'_insert_id';	
	if ($link===false) 
	{
		$r = $fname();
	}
	else
	{
		$r = $fname($link);
	}
	$dbtimethishit += getmicrotime();
	return $r;
}

function db_error($link)
{
	$fname = DBTYPE.'_error';
	$r = $fname($link);
			
	return $r;
}

function db_errno($link)
{
	$fname = DBTYPE.'_errno';
	$r = $fname($link);
			
	return $r;
}

function db_fetch_assoc($result)
{
	global $dbtimethishit;
	$dbtimethishit -= getmicrotime();
	$fname = DBTYPE.'_fetch_assoc';
	$r = $fname($result);
	$dbtimethishit += getmicrotime();
	return $r;
}

function db_num_rows($result)
{
	global $dbtimethishit;
	$dbtimethishit -= getmicrotime();
	$fname = DBTYPE.'_num_rows';
	$r = $fname($result);
	$dbtimethishit += getmicrotime();
	return $r;
}

function db_affected_rows($link=false)
{
	global $dbtimethishit;
	$dbtimethishit -= getmicrotime();
	$fname = DBTYPE.'_affected_rows';
	if ($link===false) 
	{
		$r = $fname();
	}
	else
	{
		$r = $fname($link);
	}
	$dbtimethishit += getmicrotime();
	return $r;
}







function db_pconnect($host,$user,$pass)
{
	global $dbtimethishit;
	$dbtimethishit -= getmicrotime();
	$fname = DBTYPE.'_connect';
	$r = $fname($host,$user,$pass);
	$dbtimethishit += getmicrotime();
	return $r;
}

function db_select_db($dbname)
{
	global $dbtimethishit;
	$dbtimethishit -= getmicrotime();
	$fname = DBTYPE.'_select_db';
	$r = $fname($dbname);
	$dbtimethishit += getmicrotime();
	return $r;
}
function db_free_result($result)
{
	global $dbtimethishit;
	$dbtimethishit -= getmicrotime();
	$fname = DBTYPE.'_free_result';
	$r = $fname($result);
	$dbtimethishit += getmicrotime();
	return $r;
}

/**
* @author talion
* @desc Erstellt Listenarray aus SQL-Result, wahlweise mit numerischen oder assoziativen Schlüsseln
* @param result SQL-Result
* @param string Name des Feldes, das als Schlüssel verwendet werden soll (Optional, wenn nicht gesetzt: numerischer Schlüssel)
* @return array Array-Liste
*/
function db_create_list($result, $str_key = false)
{
	global $dbtimethishit;
	
	$dbtimethishit -= getmicrotime();
	
	$arr_list = array();
	
	while($row = db_fetch_assoc($result)) {
		
		// Mit Schlüssel
		if( false !== $str_key && isset($row[$str_key]) ) {
			$arr_list[ $row[$str_key] ] = $row;
		}
		else {
			$arr_list[] = $row;
		}
		
	}
	
	
	$dbtimethishit += getmicrotime();
	
	return($arr_list);
}

function sql_error($sql)
{
	global $session;
	return output_array($session).'SQL = <pre>$sql</pre>'.db_error(LINK);
}
?>
