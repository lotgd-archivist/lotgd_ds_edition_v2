<?
/*
communityinterface.lib.php

Funktionen & globale Werte um User ins Forum zu übertragen

by Alucard 
*/
$GLOBALS['ci_conn_host'] 	= "localhost";			//Host
$GLOBALS['ci_conn_user'] 	= "ds_lotgd_forum";		//user
$GLOBALS['ci_conn_pass']	= "$"."lotgd%forum&";	//password
$GLOBALS['ci_conn_db']		= "ds_lotgd_forum";		//datenbankname

$GLOBALS['ci_usertable'] 	= "phpbb_users"; 		//tabelle für user
$GLOBALS['ci_row_id'] 		= "user_id";			//spaltenname-id
$GLOBALS['ci_row_name'] 	= "username";			//spaltenname-username
$GLOBALS['ci_row_pass'] 	= "user_password";		//spaltenname-password
$GLOBALS['ci_row_mail'] 	= "user_email";			//spaltenname-emailadresse
$GLOBALS['ci_row_active'] 	= "user_active";		//spaltenname-user ist aktiv
$GLOBALS['ci_row_reg'] 		= "user_regdate";		//spaltenname-registrations datum

global $ci_conn_host, $ci_conn_user, $ci_conn_pass, $ci_conn_db, $ci_usertable, $ci_row_id, $ci_row_name, $ci_row_pass, $ci_row_mail, $ci_row_active, $ci_row_reg;


$GLOBALS['ci_sql'] = array( 	'getnextid' => "SELECT ".$ci_row_id." AS startid FROM ".$ci_usertable." ORDER BY ".$ci_row_id." DESC LIMIT 1",
					'insert' 	=> "INSERT INTO ".$ci_usertable." (".$ci_row_id.",".$ci_row_name.",".$ci_row_pass.",".$ci_row_mail.",".$ci_row_reg.",".$ci_row_active.") VALUES (%d, '%s', '%s', '%s', %d, %d)",
					'ban'		=> "UPDATE ".$ci_usertable." SET ".$ci_row_active."=%d WHERE ".$ci_row_id."=%d LIMIT 1",
					'delete'	=> "DELETE FROM ".$ci_usertable." WHERE ".$ci_row_id."=%d LIMIT 1",
					'rename' 	=> "UPDATE ".$ci_usertable." SET ".$ci_row_name."='%s' WHERE ".$ci_row_id."=%d LIMIT 1",
					'changepw'	=> "UPDATE ".$ci_usertable." SET ".$ci_row_pass."='%s' WHERE ".$ci_row_id."=%d LIMIT 1",
					'checkname' => "SELECT ".$ci_row_id." FROM ".$ci_usertable." WHERE LOWER(".$ci_row_name.")='%s' LIMIT 1"
				);

$GLOBALS['G_CI_CONN'] = 0;


/**
*@desc disconnected von der Community-db
*@return none
*@author Alucard
*/
function ci_disconnect()
{
	global $G_CI_CONN; 
	if( $G_CI_CONN ){
		mysql_close($G_CI_CONN);
	}
}

/**
*@desc verbindet wieder zur LOTGD-DB
*@return none
*@author Alucard
*/
function ci_reconnect()
{
	global $DB_HOST, $DB_USER, $DB_PASS, $DB_NAME;
	
	ci_disconnect();
	
	$link = db_pconnect($DB_HOST, $DB_USER, $DB_PASS) or die (db_error($link));
	db_select_db ($DB_NAME) or die (db_error($link));
}


/**
*@desc verbindet zur Community-DB
*@return erfolgreich connected?
*@author Alucard
*/
function ci_connect()
{
	global $G_CI_CONN;
	global $ci_conn_host, $ci_conn_user, $ci_conn_pass, $ci_conn_db;
	
	$ret = 0;
	//echo 'Alucard testet etwas!';
	//die($ci_conn_host."__".$ci_conn_user."__".$ci_conn_pass);
	$G_CI_CONN = db_pconnect( $ci_conn_host, $ci_conn_user, $ci_conn_pass);
	
	if( $G_CI_CONN ){ 
		if( db_select_db( $ci_conn_db ) ){
			$ret = 1;
		}
		else{
			ci_reconnect();
		}
	}
	return $ret;
}


/**
*@desc Importiert User in das Forum
*@param aUser 2D-Array -> $aUser[ n ][	'id' 	=> ID des Users,
*										'name' 	=> login name
*										'pass' 	=> Passwort,
*										'mail' 	=> emailadresse]
*@param setactive User aktivieren?
*@return Anzahl der übertragenen User
*@author Alucard
*/
function ci_importusers($aUser, $setactive=1)  
{
	global $ci_sql, $G_CI_CONN;	
	
	$ret = 0;
	if(getsetting("ci_std_pw_active",0)){
		$stdpw = getsetting("ci_std_pwc",'');
	}
	
	if(!ci_connect()){
		return $ret;
	}
	
	$start = db_fetch_assoc(db_query($ci_sql['getnextid']));
	$start = $start['startid'];	

	
	$count = count($aUser);
	for($i=0;$i<$count; ++$i){
		
		$res = db_query(sprintf($ci_sql['checkname'], addslashes(strtolower($aUser[ $i ]['name'])))) or die (db_error($G_CI_CONN));
		if(db_num_rows($res)){
			$aUser[ $i ]['id'] = 0;
			continue;
		}
		
		if(!empty($stdpw)){
			$aUser[ $i ]['pass'] = $stdpw;
		}
		$qry = sprintf($ci_sql['insert'], ++$start, addslashes($aUser[ $i ]['name']), addslashes($aUser[ $i ]['pass']), $aUser[ $i ]['mail'], time(), $setactive);
		if( !db_query($qry) ){
			$aUser[ $i ]['id'] = 0;
		}
		else{
			$aUser[ $i ]['ci_id'] = $start;
		}
	}
	
	ci_reconnect();
	
	for($i=0;$i<$count; ++$i){
		if( $aUser[ $i ]['id'] ){
			db_query("UPDATE account_extra_info SET incommunity=".$aUser[ $i ]['ci_id']." WHERE acctid=".$aUser[ $i ]['id']." LIMIT 1");
			$ret++;
		}
	}
	
	return $ret;
}


/**
*@desc setzt den user inaktiv im Forum, weil gebant ;>
*@return none
*@author Alucard
*/
function ci_banuser($id, $bBanned)
{
	global $ci_sql, $G_CI_CONN;
	
	$ret = "";
	
	if(!ci_connect()){
		return;
	}
	
	$active = $bBanned ? 0 : 1;
	
	db_query(sprintf($ci_sql['ban'], $active, $id));
	
	ci_reconnect();
}


/**
*@desc löscht den user im Forum
*@return none
*@author Alucard
*/
function ci_deleteuser($id)
{
	global $ci_sql, $G_CI_CONN;
	
	if(!ci_connect()){
		return;
	}
	
	db_query(sprintf($ci_sql['delete'], $id)) or die (db_error($G_CI_CONN));
	
	ci_reconnect();
}


/**
*@desc benennt den user um
*@return none
*@author Alucard
*/
function ci_rename($id, $name)
{
	global $ci_sql, $G_CI_CONN;
	
	if(!ci_connect()){
		return;
	}
	db_query(sprintf($ci_sql['rename'], addslashes(strtolower($name)), $id)) or die (db_error($G_CI_CONN));
	
	ci_reconnect();
}


/**
*@desc benennt den user um
*@return none
*@author Alucard
*/
function ci_setpw($id, $pass)
{
	global $ci_sql, $G_CI_CONN;
	
	
	if(!ci_connect()){
		return;
	}
	
	db_query(sprintf($ci_sql['changepw'], $pass, $id)) or die (db_error($G_CI_CONN));
	
	ci_reconnect();
}





?>
