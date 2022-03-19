<?php
function addnews($news)
{
	global $session;
	$sql = 'INSERT INTO news(newstext,newsdate,accountid) VALUES ("'.addslashes($news).'",NOW(),'.$session['user']['acctid'].')';
	return db_query($sql) or die(db_error($link));
}

function addcrimes($crimes)
{
	global $session;
	$sql = 'INSERT INTO crimes(newstext,newsdate,accountid) VALUES ("'.addslashes($crimes).'",NOW(),'.$session['user']['acctid'].')';
	return db_query($sql) or die(db_error($link));
}

function addtocases($case,$suspect)
{
	global $session;
	$sql = 'INSERT INTO cases(newstext,accountid,judgeid,court) VALUES ("'.addslashes($case).'",'.$_GET[suspect].','.$session['user']['acctid'].',0)';
	return db_query($sql) or die(db_error($link));
}

function addnews_ddl($news)
{
	global $session;
	$sql = 'INSERT INTO ddlnews(newstext,newsdate,accountid) VALUES ("'.addslashes($news).'",NOW(),'.$session['user']['acctid'].')';
	return db_query($sql) or die(db_error($link));
}
?>
