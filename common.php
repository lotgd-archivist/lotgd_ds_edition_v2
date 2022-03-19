<?php

// 12092004

//require_once "dbwrapper.php";
//require_once "anticheat.php";
define('LIB_PATH','lib/');
define('RELOAD_STOP_TIME',12);
// Debugmode: Setzt für angegebenen SU 
// verschiedene Mechanismen (Cheatschutz) außer Kraft 
define('DEBUGMODE',5);

require_once(LIB_PATH.'gametime.lib.php');

$pagestarttime = getmicrotime();

require_once(LIB_PATH.'db.lib.php');

if (file_exists('dbconnect.php'))
{
	require_once 'dbconnect.php';
}
else
{
	echo 'Du musst die benötigten Informationen in die Datei "dbconnect.php.dist" eintragen und sie unter dem Namen "dbconnect.php" speichern.';
	exit();
}

$link = db_pconnect($DB_HOST, $DB_USER, $DB_PASS) or die (db_error($link));
db_select_db ($DB_NAME) or die (db_error($link));
define('LINK',$link);


require_once(LIB_PATH.'news.lib.php');
require_once(LIB_PATH.'commentary.lib.php');
require_once(LIB_PATH.'mail.lib.php');
require_once(LIB_PATH.'security.lib.php');
require_once(LIB_PATH.'rand.lib.php');
require_once(LIB_PATH.'nav.lib.php');
require_once(LIB_PATH.'buffs.lib.php');
require_once(LIB_PATH.'user.lib.php');
require_once(LIB_PATH.'gameplay_misc.lib.php');
require_once(LIB_PATH.'house.lib.php');
require_once(LIB_PATH.'mount.lib.php');
require_once(LIB_PATH.'settings.lib.php');
require_once(LIB_PATH.'specialty.lib.php');
require_once(LIB_PATH.'profession.lib.php');
require_once(LIB_PATH.'wakeup.lib.php');
require_once(LIB_PATH.'chosen.lib.php');
require_once(LIB_PATH.'disciples.lib.php');
require_once(LIB_PATH.'items.lib.php');
require_once(LIB_PATH.'output.lib.php');


session_register('session');

$session =& $_SESSION['session'];

register_global($_SERVER);

$session['lasthit'] = time();

if (!empty($PATH_INFO))
{
	$SCRIPT_NAME=$PATH_INFO;
	$REQUEST_URI='';
}
if (empty($REQUEST_URI))
{
	//necessary for some IIS installations (CGI in particular)
	if (is_array($_GET) && count($_GET)>0)
	{
		$REQUEST_URI=$SCRIPT_NAME.'?';
		reset($_GET);
		$i=0;
		foreach($_GET as $key=>$val)
		{
			if ($i>0)
			{
				$REQUEST_URI.='&';
			}
			$REQUEST_URI.=$key.'='.URLEncode($val);
			$i++;
		}
	}
	else
	{
		$REQUEST_URI=$SCRIPT_NAME;
	}
	$_SERVER['REQUEST_URI'] = $REQUEST_URI;
}
$SCRIPT_NAME=substr($SCRIPT_NAME,strrpos($SCRIPT_NAME,'/')+1);
if (strpos($REQUEST_URI,'?'))
{
	$REQUEST_URI=$SCRIPT_NAME.substr($REQUEST_URI,strpos($REQUEST_URI,'?'));
}
else
{
	$REQUEST_URI=$SCRIPT_NAME;
}

// RELOADSTOP
if(!$session['loggedin']) 
{
	if($SCRIPT_NAME == 'index.php' && $SCRIPT_NAME != 'source.php') 
	{
		$timediff = time() - $_COOKIE['lasthit'];
		if( $timediff < RELOAD_STOP_TIME )
		{
			include_once('reload_stop.php');
			exit;
		}
		setcookie('lasthit',time(),strtotime(date('r').'+365 days'));
	}
	elseif($SCRIPT_NAME == 'login.php') // Probleme bei falscher Passworteingabe etc.
	{
		setcookie('lasthit',0,strtotime(date('r').'+365 days'));
	}
}

// END RELOADSTOP


$session['lastip']=$REMOTE_ADDR;
if (strlen($_COOKIE['lgi'])<32)
{
	if (strlen($session['uniqueid'])<32)
	{
		$u=md5(microtime());
		setcookie('lgi',$u,strtotime(date('r').'+365 days'));
		$_COOKIE['lgi']=$u;
		$session['uniqueid']=$u;
	}
	else
	{
		setcookie('lgi',$session['uniqueid'],strtotime(date('r').'+365 days'));
	}
}
else
{
	$session['uniqueid']=$_COOKIE['lgi'];
}


$revertsession=$session;

if (time() - getsetting('LOGINTIMEOUT',900) > $session['lasthit'] && $session['lasthit']>0 && ($session['loggedin'] && !$session['user']['superuser']>0))
{
	//force the abandoning of the session when the user should have been sent to the fields.
	//echo "Session abandon:".(strtotime("now")-$session[lasthit]);

	$session=array();
	$session['message'].='`nDeine Session ist abgelaufen!`n';
}

// NAVS checken, User laden.
// Einstellungsarrays nun in nav.lib!
if ($session['loggedin'] && (int)$session['user']['acctid'])
{
	setcookie('lasthit',0,strtotime(date('r').'+365 days'));

	// ACCOUNT laden
	user_load($session['user']['acctid']);
	
	if ( !user_get_online(0,$session['user'],true) )
	{
		$session=array();
		redirect('index.php?op=timeout','Account ist nicht eingeloggt, aber die Session denkt, er ist es.');
	}
			
	if ($session['allowednavs'][$REQUEST_URI] && !$allownonnav[$SCRIPT_NAME])
	{
		$session['allowednavs']=array();
	}
	else
	{
		if (!$allownonnav[$SCRIPT_NAME])
		{
			redirect('badnav.php','Navigation auf '.$REQUEST_URI.' nicht erlaubt');
		}
	}
	
	if ($session['user']['imprisoned']==-5 && $session['user']['alive'])	// Stadtwachen RPG-Einkerkerung
	{
		$session['user']['imprisoned']=1;
		redirect('prison.php');
	}
	
	// Auf unbestimmte Zeit im Kerker
	if ($session['user']['imprisoned']==-2)
	{
		$session['user']['imprisoned']=-1;
		redirect('prison.php');
	}	
}
else
{
	if (!$allowanonymous[$SCRIPT_NAME])
	{
		$session['message']='Du bist nicht eingeloggt. Wahrscheinlich ist deine Sessionzeit abgelaufen.';
		redirect('index.php?op=timeout','Not logged in: '.$REQUEST_URI);
	}
}

if ($session['user']['loggedin']!=true && (!$allowanonymous[$SCRIPT_NAME] && !$session['user']['superuser']>0))
{
	redirect('login.php?op=logout');
}

$session['counter']++;

// Wenn wir Seite in Restore setzen dürfen
if (!$nokeeprestore[$SCRIPT_NAME]) 
{ 
	$ret_page = calcreturnpath($session['user']['restorepage']);
	$session['user']['restorepage']=$REQUEST_URI;
}

// Inventar standardmäßig auf aus
$show_invent = false;		

// TEMPLATE setzen
if(!empty($overwrite_template)) {
	$session['user']['prefs']['template'] = $overwrite_template;
}
if (!empty($session['user']['prefs']['template']))
{
	setcookie("template",$session['user']['prefs']['template'],strtotime(date("r")."+45 days"));
	$_COOKIE['template']=$session['user']['prefs']['template'];
}
if (!empty($_COOKIE['template']))
{
	$templatename=$_COOKIE['template'];
}
if (empty($templatename) || !file_exists('templates/'.$templatename)) 
{
	$templatename=getsetting('defaultskin','yarbrough.htm');
}
$session['user']['prefs']['template'] = $templatename;

// TEMPLATE laden
$template = loadtemplate($templatename);

//tags that must appear in the header
$templatetags=array('title','headscript','script');
//Erstmal entfernt
/*foreach ($templatetags as $val)
{
	if (strpos($template['header'],'{'.$val.'}')===false) 
	{
		$templatemessage.='You do not have {'.$val.'} defined in your header';
	}
}*/
//tags that must appear in the footer
$templatetags=array();
//Erstmal entfernt
/*
foreach ($templatetags as $val) 
{
	if (strpos($template['footer'],'{'.$val.'}')===false) 
	{
		$templatemessage.='You do not have {'.$val.'} defined in your footer';
	}
}*/

//tags that may appear anywhere but must appear
//touch the copyright and we will force your server to be shut down
$templatetags=array('nav','stats','petition','motd','mail','paypal','copyright','source');

//Erstmal entfernt
/*foreach ($templatetags as $val) 
{
	if (strpos($template['header'],'{'.$val.'}')===false && strpos($template['footer'],'{'.$val.'}')===false) 
	{
		$templatemessage.='You do not have {'.$val.'} defined in either your header or footer';
	}
}*/

if (!empty($templatemessage))
{
	echo '<b>Du hast einen oder mehrere Fehler in deinem Template!</b><br>'.nl2br($templatemessage);
	$template=loadtemplate('yarbrough.htm');
}
// END Template laden

// Rassen: Sollte mal anders gemacht werden.
define('RACE_TROLL',1);
define('RACE_ELF',2);
define('RACE_MENSCH',3);
define('RACE_ZWERG',4);
define('RACE_ECHSE',5);
define('RACE_DUNKELELF',6);
define('RACE_WERWOLF',7);
define('RACE_GOBLIN',8);
define('RACE_ORK',9);
define('RACE_VAMPIR',10);
define('RACE_HALBLING',11);
define('RACE_DAEMON',12);
define('RACE_SCHELM',13);
define('RACE_ENGEL',14);
define('RACE_AVATAR',15);

$races=array(RACE_TROLL=>'Troll',RACE_ELF=>'Elf',RACE_MENSCH=>'Mensch',RACE_ZWERG=>'Zwerg',RACE_ECHSE=>'Echse',RACE_DUNKELELF=>'Dunkelelf',RACE_WERWOLF=>'Werwolf',RACE_GOBLIN=>'Goblin',RACE_ORK=>'Ork',RACE_VAMPIR=>'Vampir',RACE_HALBLING=>'Halbling',RACE_DAEMON=>'Dämon',RACE_SCHELM=>'Schelm',RACE_ENGEL=>'Engel',RACE_AVATAR=>'Avatar',0=>'Unbekannt',50=>'Hoverschaf');
$colraces=array(1=>'`2Troll`0',2=>'`^Elf`0',3=>'`0Mensch',4=>'`#Zwerg`0',5=>'`5Echse`0',6=>'`5Dunkelelf`0',7=>'`TWerwolf`0',8=>'`6Goblin`0',9=>'`2Ork`0',10=>'`4Vampir`0',11=>'`tHalbling`0',12=>'`4Dämon`0',13=>'`9Schelm`0',14=>'`^Engel',15=>'`&Avatar',0=>'`)Unbekannt`0',50=>'Hoverschaf');
// END Rassen

$beta = (getsetting('beta',0) == 1 || $session['user']['beta']==1);
$logd_version = '0.9.7+jt ext(GER) Dragonslayer Edition V/2';
?>