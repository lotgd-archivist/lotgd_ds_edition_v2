<?php

// Knappen k�nnen n�tzlich sein und eine Stufe aufsteigen... oder auch sterben
// By Maris (Maraxxus@gmx.de)

if (!isset($session)) exit();

$specialinc_file = "wolves.php";

if ( $HTTP_GET_VARS[op] == "askforhelp"){
$sql = "SELECT name FROM disciples WHERE master=".$session['user']['acctid']."";
$result = db_query($sql) or die(db_error(LINK));
$rowk = db_fetch_assoc($result);

output("`n`#Du rufst nach deinem treuen Knappen und ".$rowk['name']." zieht sein kleines Schwertchen und stellt sich damit sch�tzend vor dich.`n ");
	output("`#Dann kommen die W�lfe... und der Gr��te von ihnen st�rzt sich auf deinen Knappen.`n");
	$session['user']['specialinc'] = ""; 
	switch(e_rand(1,6)){
		case 1 :
		case 2 :
		case 3 :
		case 4 :
		case 5 :
		output("`#Irgendwie gelingt es ".$rowk['name']." dem Leitwolf im Sprung sein kleines Schwert in die Kehle zu rammen.`nDas Tier w�lzt sich eine Weile zuckend und jaulend auf dem Boden, dann bleibt es regungslos liegen.`nDie anderen W�lfe entfernen sich rasch.`nGemeinsam schafft ihr es dein Bein aus der Falle zu befreien und ihr seht zu, dass ihr das Weite sucht bevor die Bestien wiederkommen.`n`n`4Du hast fast alle deine Lebenspunkte verloren.`n`@Dein Knappe steigt durch diese besondere Erfahrung im Kampf eine Stufe auf!`0`n");
$session['user']['hitpoints']=1;
disciple_levelup();

        addnav("Zur�ck zum Wald","forest.php");
        break;
        case 6 :
        output("`#Dein kleiner Begleiter hat keine Chance, und du kannst nichts f�r ihn tun.`nDie W�lfe zerreissen ".$rowk['name']." und fressen sich vor deinen Augen an ihm satt.`nDoch dann lassen sie dich links liegen und trotten zur�ck in den tieferen Wald.`nDu brauchst eine halbe Ewigkeit dich zu befreien.`n`4Du hast fast alle deine Lebenspunkte und 5 Waldk�mpfe verloren. Dein Knappe ist tot!`0`n");
        $session['user']['hitpoints']=1;
        $session['user']['turns']-=5;
        if ($session['user']['turns']<0) $session['user']['turns']=0;

        $sql = "SELECT disciples_spoiled FROM account_extra_info WHERE acctid=".$session['user']['acctid']."";
        $result = db_query($sql) or die(db_error(LINK));
        $rowk = db_fetch_assoc($result);
        $spoil=$rowk['disciples_spoiled']+1;
        $sql = "UPDATE account_extra_info SET disciples_spoiled=$spoil WHERE acctid = ".$session['user']['acctid'];
        db_query($sql) or die(sql_error($sql));

        $sql="DELETE FROM disciples WHERE master=".$session['user']['acctid'];
        db_query($sql);
        debuglog("Verlor einen Knappen beim Wolf-Event im Wald.");
        break;
		
	}
}else if ( $HTTP_GET_VARS[op] == "sendaway"){
$sql = "SELECT name,state,level FROM disciples WHERE master=".$session['user']['acctid']."";
$result = db_query($sql) or die(db_error(LINK));
$rowk = db_fetch_assoc($result);
output("`n`#Du rufst laut : \"`5".$rowk['name']."`5, lauf! Hol Hilfe!`#\"`n");
output("Dein Knappe l�uft so schnell er kann fort.`n`n");
switch (e_rand(1,3)) {
    case 1 :
    case 2 :
	output("`#Die Zeit vergeht, es kommt dir vor wie Stunden.`nDie W�lfe kommen immer n�her, du kannst schon ihre stechenden Augen im Unterholz erkennen.`nDann pl�tzlich schrecken die Tiere auf und rennen davon als `^".$rowk['name']."`# mit einer kleinen Gruppe Feldarbeiter erscheint.`nDie kr�ftigen M�nner helfen dir dich von der Falle zu befreien und st�tzen dich auf deinem Weg fort von hier.`n`4Du hast fast alle deine Lebenspunkte und 3 Waldk�mpfe verloren!`0`n");
	$session['user']['hitpoints']=1;
    $session['user']['turns']-=3;
    if ($session['user']['turns']<0) $session['user']['turns']=0;
	$session['user']['specialinc'] = "";
	addnav("Zur�ck zum Wald","forest.php");
    break;
    case 3 :
    output("`#Doch als er mit Hilfe wiederkehrt findet er nur noch deine gr�ndlich abgenagten Knochen und deine zerrissene Ausr�stung bei der Falle wieder.`n`4Du bist tot!`0`n");
    $session['user']['hitpoints']=0;
    $session['user']['specialinc'] = "";
   	addnews($session['user']['name']."`@ wurde im Wald von W�lfen gefressen!`n");
    addnav("Weiter","shades.php");
    break;
    }
}else{
	output("`n");
	output("`#Du gehst ahnungslos deines Weges, als du pl�tzlich ein lauten, peitschen�hnliches Ger�usch direkt unter dir vernimmst. Sofort steigt ein brennender Schmerz dein Bein hinauf und deine Knie knicken weg. Halb bewusstlos vor Schmerz erkennst du die scharf gezackten B�gel einer unter Bl�ttern vernorgenen gro�en Wildfalle, in die du gerade gelaufen bist.`0`n`n");

$sql = "SELECT name,state,level FROM disciples WHERE state>0 AND master=".$session['user']['acctid']."";
$result = db_query($sql) or die(db_error(LINK));

if (db_num_rows($result)>0){
$rowk = db_fetch_assoc($result);
}

if (($rowk['state']>0) || (db_num_rows($result)>0))
{
output("`#Zu allem �bel h�rst du noch das Heulen mehrerer W�lfe, die wohl die Witterung deines Blutes aufgenommen haben und bald bei dir sein werden.`nAber zum Gl�ck ist ja dein Knappe `^".$rowk['name']."`# in deiner N�he.`nObwohl er fast noch ein Kind ist k�nnte er dich aus dieser Notlage befreien, allerdings k�nnte er auch genausogut sein Leben hierbei verlieren...`n`nWas tust du?`0");

$session['user']['specialinc'] = $specialinc_file;
addnav($rowk['name']." rufen","forest.php?op=askforhelp");
addnav($rowk['name']." fortschicken","forest.php?op=sendaway");
} else {
output("`#Es dauert eine halbe Ewigkeit bis du dich aus der Falle befreit hast.`nDu greifst dir einen langen Stock als St�tze und humpelst davon.`n`4Du hast fast alle deine Lebenspunkte und 5 Waldk�mpfe verloren!`0`n");
$session['user']['hitpoints']=1;
$session['user']['turns']-=5;
if ($session['user']['turns']<0) $session['user']['turns']=0;
addnav("Zur�ck in den Wald","forest.php");
}
}
?>
