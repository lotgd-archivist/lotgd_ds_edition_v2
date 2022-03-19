<?php
require_once "common.php";

page_header("Land der Schatten");
addcommentary();
checkday();

$session['user']['mazeturn']=0;
if ($session['user']['alive']) redirect("village.php");

$sql = "SELECT name,state FROM disciples WHERE master=".$session['user']['acctid']."";
$result = db_query($sql) or die(db_error(LINK));
if (db_num_rows($result)>0){
$rowk = db_fetch_assoc($result);
}

if ($rowk['state']==20) { $buffsave=$session['bufflist']['decbuff'];
$session['bufflist']=array(); $session['bufflist']['decbuff']=$buffsave; }
else
{ $session['bufflist']=array();}


//RUNEN MOD
//wenn man eine eiwazrune hat, kommt man wieder nach oben
if( item_count('tpl_id="r_eiwaz" AND owner='.$session['user']['acctid']) > 0 ){
	addnav('Runenkraft');
	addnav('Benutze eine Eiwaz-Rune','newday.php?resurrection=rune');	
}
//RUNEN END


if ($session['user']['acctid']==getsetting('hasegg',0)){ 
	addnav('Benutze das goldene Ei','newday.php?resurrection=egg');
}
output("`\$Du wandelst jetzt unter den Toten, du bist nur noch ein Schatten. Überall um dich herum sind die Seelen der in alten Schlachten und bei  
gelegentlichen Unfällen gefallenen Kämpfer. Jede trägt Anzeichen der Niedertracht, durch welche sie ihr Ende gefunden haben.`n`n
Im Dorf dürfte es jetzt etwa `^".getgametime()."`\$ sein, aber hier herrscht die Ewigkeit und Zeit gibt es mehr als genug.`n`n
Die verlorenen Seelen flüstern ihre Qualen und plagen deinen Geist mit ihrer Verzweiflung.`n");

viewcommentary("shade","Verzweifeln",25,"jammert");
addnav('Das Totenreich');
addnav("Der Friedhof","graveyard.php");
addnav('Sonstiges');
addnav('`^Drachenbücherei`0','library.php');
addnav("In Diskussionsräume geistern","ooc.php?op=ooc");
addnav("Kriegerliste","list.php");
addnav("In Ruhmeshalle spuken","hof.php");
addnav('Zurück');
addnav("Zu den News","news.php");

if ($session['user']['superuser'] > 0)
{
	addnav("X?Admin Grotte","superuser.php");			
	addnav('Back to Life','superuser.php?op=iwilldie');
}
  

page_footer();
?>
