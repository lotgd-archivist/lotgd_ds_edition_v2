<?php
// by talion für lotgd.drachenserver.de
// Stellt Funktion bereit, um Output eines Users unabhängig von eigener Position verfolgen zu können

require_once "common.php";

if(!su_check(SU_RIGHT_WATCHSU) || !$session['user']['loggedin'] || !isset($session) || !isset($_GET['userid']) ) {exit;}

$output="";
$sql = "SELECT acctid,login,output,superuser,laston FROM accounts WHERE acctid='".(int)$_GET['userid']."'";
$result = db_query($sql);
$row = db_fetch_assoc($result);

if(!$row['acctid']) {exit;}

if(!$session['logs']['suwatch'][$row['acctid']]) {
	debuglog("`&Big Brother benutzt an User:",$row['acctid']);
	$session['logs']['suwatch'][$row['acctid']] = true;
}

//if($row['superuser'] >= $session['user']['superuser']) {exit;}

session_write_close();

echo('<span style="color:white;"><b>Big Brother is watching you: '.$row['login'].' ( AcctID: '.$row['acctid'].' )</b>
			<br>LastAct: '.$row['laston'].'<br><br></span>');

echo str_replace("<iframe src=","<iframe Xsrc=",$row['output']);

exit();	
?>
