<?php
/**
* news.php:	Anzeige der aktuellen Spielernews 
* @author LOGD-Core, modified by Drachenserver-Team
* @version DS-E V/2
*/

require_once('common.php');

if ($session['user']['imprisoned']>0) {
	redirect("prison.php");
}
	
if ($session['user']['loggedin']) {
	checkday();
}

$newsperpage=30;
	
page_header('Neuigkeiten aus '.getsetting('townname','Atrahor'));

if (su_check(SU_RIGHT_NEWS)){
	output("`0<form action=\"news.php\" method='POST'>",true);
	output("[Admin] Meldung manuell eingeben? <input name='meldung' size='40'> ",true);
	output("<input type='submit' class='button' value='Eintragen'>`n`n",true);
	addnav("","news.php");
	if ($_POST['meldung']){
		$sql = "INSERT INTO news(newstext,newsdate,accountid) VALUES ('".addslashes($_POST['meldung'])."',NOW(),0)";
		db_query($sql) or die(db_error($link));
		$_POST['meldung']="";
	}
}

if (!$session[user][loggedin]) {
	addnav("Login", "index.php");
} 
else if ($session[user][alive]){
	addnav("Zurück");
	addnav("Zum Dorfplatz","village.php");
	addnav("Zum Marktplatz","market.php");
	
}
else{
	if($session['user']['sex'] == 1) {
		addnav("`!`bDu bist tot, Jane!`b`0");
	} else {
		addnav("`!`bDu bist tot, Jim!`b`0");
	}
	addnav("Land der Schatten","shades.php");
	addnav("Log out","login.php?op=logout");

}

addnav("Information");
addnav("Über das Spiel","about.php");
if (su_check(SU_RIGHT_NEWDAY)){
	addnav("Neuer Tag","newday.php");
}

$offset = (int)$_GET['offset'];

$timestamp = time() - 86400 * $offset;
$date_from = date("Y-m-d",$timestamp);

$sql = "SELECT count(newsid) AS c FROM news WHERE newsdate='".$date_from."'";
$result = db_query($sql);
$row = db_fetch_assoc($result);
$totaltoday=$row['c'];

$pageoffset = (int)$_GET['page'];

if ($pageoffset>0) {
	$pageoffset--;
}

$pageoffset*=$newsperpage;

$sql = "SELECT * FROM news WHERE newsdate='".$date_from."' ORDER BY newsid DESC LIMIT $pageoffset,$newsperpage";
$result = db_query($sql) or die(db_error(LINK));

$date = strftime("%A, %e. %B %Y",$timestamp);

output("`c`b`!Neuigkeiten für $date".($totaltoday>$newsperpage?" (Meldungen ".($pageoffset+1)." - ".min($pageoffset+$newsperpage,$totaltoday)." von $totaltoday)":"")."`c`b`0");

for ($i=0;$i<db_num_rows($result);$i++){
	$row = db_fetch_assoc($result);
	output("`c`2-=-`@=-=`2-=-`@=-=`2-=-`@=-=`2-=-`0`c");
	if (su_check(SU_RIGHT_NEWS)){
		output( '[ '.create_lnk('Del','superuser.php?op=newsdelete&newsid='.$row['newsid'].'&return='.URLEncode($_SERVER['REQUEST_URI'])).' ]&nbsp;' , true );
	}
	output($row['newstext'].'`n');
}
if (db_num_rows($result)==0){
	output("`c`2-=-`@=-=`2-=-`@=-=`2-=-`@=-=`2-=-`0`c");
	output("`1`b`c Es ist nichts Erwähnenswertes passiert. Alles in allem bisher ein langweiliger Tag. `c`b`0");
}
output("`c`2-=-`@=-=`2-=-`@=-=`2-=-`@=-=`2-=-`0`c");
if ($totaltoday>$newsperpage){
	addnav("Heutige Neuigkeiten");
	for ($i=0;$i<$totaltoday;$i+=$newsperpage){
		addnav("Seite ".($i/$newsperpage+1),"news.php?offset=$offset&page=".($i/$newsperpage+1));
	}

}

addnav('Vergangene Tage');
addnav('Tag zurück',"news.php?offset=".($offset+1));
if ($offset>0){
	addnav('Tag vor',"news.php?offset=".($offset-1));
}

page_footer();
?>