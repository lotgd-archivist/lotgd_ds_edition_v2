<?php
// 09092004

require_once 'common.php';

if ($session['loggedin'])
{
	redirect('badnav.php');
}
page_header();
output('`cWillkommen bei Legend of the Green Dragon in der Dragonslayer-Edition, schamlos abgekupfert von Seth Able\'s Legend of the Red Dragon.`n`n');
if (getsetting('activategamedate','0')==1)
{
	output('`@Wir schreiben den `%'.getgamedate().'`@.`0`n');
}
output('`@Die gegenwärtige Zeit in '.getsetting('townname','Atrahor').' ist `%'.getgametime().'`@.`0`n');

//Next New Day in ... is by JT from logd.dragoncat.net
$time = gametime();
// $tomorrow = strtotime(date("Y-m-d H:i:s",$time)." + 1 day");
$tomorrow = mktime(0,0,0,date('m',$time),date('d',$time)+1,date('Y',$time));
// $tomorrow = strtotime(date("Y-m-d 00:00:00",$tomorrow));
$secstotomorrow = $tomorrow-$time;
$realsecstotomorrow = round($secstotomorrow / (int)getsetting("daysperday",4));
$nextdattime = date('G \\S\\t\\u\\n\\d\\e\\n, i \\M\\i\\n\\u\\t\\e\\n, s \\S\\e\\k\\u\\n\\d\\e\\n\\ \\(\\E\\c\\h\\t\\z\\e\\i\\t\\)',strtotime('1980-01-01 00:00:00 + '.$realsecstotomorrow.' seconds'));
output('`@Nächster neuer Tag in: `3<div id="index_time">'.$nextdattime.'</div>`0`n`n');
output(
'<script language="javascript">
/*Kleines Schmankerl by Alucard 
	www.atrahor.de
*/

var index_time_div = document.getElementById("index_time");
var index_time_day = Math.ceil(24/'.(int)getsetting("daysperday",4).');
var index_dest_time = 0;


function index_act_time()
{
	var jetzt = new Date();
	var tm = jetzt.getTime();
	if( tm > index_dest_time ){
		index_dest_time += index_time_day*3600000+ (tm-index_dest_time);
	}
	var diff = index_dest_time - tm;
	var s = Math.floor(diff / 3600000);
	diff %= 3600000;
	var m = Math.floor(diff / 60000);
	diff %= 60000;
	var sek = Math.floor(diff / 1000);
	

	
	index_time_div.innerHTML = s+" Stunde"+(s!=1 ? "n":"")+", "+(m<10 ? "0"+m : (m==23 || m==42 ? "<font color=\"#FFFFFF\"><b>"+m+"</b></font>" : m))+" Minute"+(m!=1 ? "n" : "")+", "+(sek<10 ? "0"+sek : sek)+" Sekunde"+(sek!=1 ? "n" : "")+" (Echtzeit)"; 
	window.setTimeout("index_act_time()", 1000);
}

function index_set_time(s,m,sek)
{
	if( !index_dest_time ){
		var jetzt = new Date();
		index_dest_time = jetzt.getTime() + 1000*sek + 60000*m + 3600000*s;
	}
	window.setTimeout("index_act_time()", 1);
}

if( index_time_div ){
	index_set_time('.date('G, i, s',strtotime('1980-01-01 00:00:00 + '.$realsecstotomorrow.' seconds')).');
}

</script>
'
,true);

$newplayer=stripslashes(getsetting('newplayer',''));
if ($newplayer!='')
{
	//output('`@Unser jüngster Spieler ist `^'.$newplayer.'`@!`0`n');
}
$newdk=stripslashes(getsetting('newdragonkill',''));
if ($newdk!='')
{
	output('`@Der letzte Drachentöter war: `&'.$newdk.'`@!`0`n');
}

$guild=stripslashes(getsetting('dgtopguild',''));
if ($guild!='')
{
	output('`@Die angesehenste Gilde '.getsetting('townname','Atrahor').'s heißt zur Zeit: `&'.$guild.'`@!`0`n`n');
}

$dkcounter = number_format( (int)getsetting('dkcounterges',0) , 0 , ' ', ' ' );
if ($dkcounter>0)
{
	output('`@Insgesamt haben unsere Helden bereits `&'.$dkcounter.'`@ Drachen erschlagen!`0`n`n');
}

$fuerst=stripslashes(getsetting('fuerst',''));
if ($fuerst!='')
{
	output('`@Den Fürstentitel '.getsetting('townname','Atrahor').'s trägt zur Zeit: `b`&'.$fuerst.'`@`b!`0`n`n');
}

if(getsetting('wartung',0) > 0) {
	output("`^`bDer Server befindet sich im Moment im Wartungsmodus, um Änderungen am Spiel oder dem Server störungsfrei vornehmen zu können.`b`nBitte warte, bis sich dies ändert.`n`n`0");
}

$result = db_fetch_assoc(db_query("SELECT COUNT(acctid) AS onlinecount FROM accounts WHERE locked=0 AND ".user_get_online() ));
$onlinecount = $result['onlinecount'];

// do not check if playerlimit is not reached!
if (( $onlinecount >= getsetting('maxonline',10) && getsetting('maxonline',10)!=0) || getsetting('wartung',0) > 0 )
{
	$id=$_COOKIE['lgi'];
	$sql = "SELECT superuser,uniqueid FROM accounts WHERE uniqueid='$id' AND superuser>0";
	$result = db_query($sql) or die(db_error(LINK));
	if (db_num_rows($result)>0)
	{
		$row = db_fetch_assoc($result);
		$is_superuser=$row['superuser'];
	}
	else
	{
		$is_superuser=0;
	}
}
else
{
	$is_superuser = 0;
}

if ( ($onlinecount<getsetting('maxonline',10) || getsetting('maxonline',10)==0 || $is_superuser) )
{
	output('Gib deinen Namen und dein Passwort ein, um '.getsetting('townname','Atrahor').' zu betreten.`n');
	if ($_GET['op']=='timeout')
	{
		$session['message'].=' Deine Sessionzeit ist abgelaufen. Bitte neu einloggen.`n';
		if (!isset($_COOKIE['PHPSESSID']))
		{
			$session['message'].=' Es scheint, als ob die Cookies dieser Seite von deinem System blockiert werden.  Zumindest Sessioncookies müssen für diese Seite zugelassen werden.`n';
		}
	}
	if ($session['message']!='')
	{
		output('`b`$'.$session['message'].'`b`n');
	}
	$encoded_password_transfer_script = 'onSubmit="document.forms.loginform.hidden_pw.value = calcMD5(document.forms.loginform.password.value);document.forms.loginform.password.value=\'\';"';
	output("<form action='login.php' name='loginform' method='POST' $encoded_password_transfer_script><input type='hidden' name='hidden_pw' />"
	.templatereplace("login",array("username"=>"<u>N</u>ame","password"=>"<u>P</u>asswort","button"=>"Einloggen"))
	."</form>`c",true);
	// Without this, I had one user constantly get 'badnav.php' :/  Everyone else worked, but he didn't
	addnav("","login.php");
}
else
{
	output("`^`bDer Server ist im Moment ausgelastet, die maximale Anzahl an Usern ist bereits online.`b`nBitte warte, bis wieder ein Platz frei ist.`n`n");
	if ($_GET['op']=='timeout')
	{
		$session['message'].=' Deine Sessionzeit ist abgelaufen. Bitte neu einloggen.`n';
		if (!isset($_COOKIE['PHPSESSID']))
		{
			$session['message'].=' Es scheint, als ob die Cookies dieser Seite von deinem System blockiert werden.  Zumindest Sessioncookies müssen für diese Seite zugelassen werden.`n';
		}
	}
	if ($session['message']!='')
	{
		output('`b`$'.$session['message'].'`b`n');
	}
	output(templatereplace('full').'`c',true);
}


output('`n`b`&'.getsetting('loginbanner','').'`0`b`n');
$session['message']='';
output('`c`2'.getsetting('townname','Atrahor').' läuft unter: `@'.$logd_version.'`0`c');

// Hotkeys auf Startseite?
$bool_hotkeys = false;

clearnav();
addnav('Neu hier?');
addnav('Über LoGD','about.php',false,false,false,$bool_hotkeys);
addnav('F.A.Q.','petition.php?op=faq',false,true,false,$bool_hotkeys);
addnav('Charakter erstellen','create_rules.php',false,false,false,$bool_hotkeys);
addnav('Das Spiel');
addnav('Liste der Einwohner','list.php',false,false,false,$bool_hotkeys);
addnav('Neuigkeiten', 'news.php',false,false,false,$bool_hotkeys);
addnav('Spieleinstellungen', 'about.php?op=setup',false,false,false,$bool_hotkeys);
addnav('Passwort vergessen?','create.php?op=forgot',false,false,false,$bool_hotkeys);
//addnav('Die LoGD-Welt');
addnav('Sonstiges rund um '.getsetting('townname','Atrahor'));
addnav(getsetting('townname','Atrahor').' Forum','http://forum.atrahor.de',false,false,true,$bool_hotkeys);
//addnav('LoGD Netz','logdnet.php?op=list');
//addnav('DragonPrime','http://www.dragonprime.net',false,false,true);

page_footer();
?>
