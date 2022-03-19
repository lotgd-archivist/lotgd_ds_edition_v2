<?php

// Knappen können nützlich sein und eine Stufe aufsteigen... oder auch sterben
// By Maris (Maraxxus@gmx.de)

if (!isset($session)) exit();

$specialinc_file = "wolves.php";

if ( $HTTP_GET_VARS[op] == "askforhelp"){
$sql = "SELECT name FROM disciples WHERE master=".$session['user']['acctid']."";
$result = db_query($sql) or die(db_error(LINK));
$rowk = db_fetch_assoc($result);

output("`n`#Du rufst nach deinem treuen Knappen und ".$rowk['name']." zieht sein kleines Schwertchen und stellt sich damit schützend vor dich.`n ");
	output("`#Dann kommen die Wölfe... und der Größte von ihnen stürzt sich auf deinen Knappen.`n");
	$session['user']['specialinc'] = ""; 
	switch(e_rand(1,6)){
		case 1 :
		case 2 :
		case 3 :
		case 4 :
		case 5 :
		output("`#Irgendwie gelingt es ".$rowk['name']." dem Leitwolf im Sprung sein kleines Schwert in die Kehle zu rammen.`nDas Tier wälzt sich eine Weile zuckend und jaulend auf dem Boden, dann bleibt es regungslos liegen.`nDie anderen Wölfe entfernen sich rasch.`nGemeinsam schafft ihr es dein Bein aus der Falle zu befreien und ihr seht zu, dass ihr das Weite sucht bevor die Bestien wiederkommen.`n`n`4Du hast fast alle deine Lebenspunkte verloren.`n`@Dein Knappe steigt durch diese besondere Erfahrung im Kampf eine Stufe auf!`0`n");
$session['user']['hitpoints']=1;
disciple_levelup();

        addnav("Zurück zum Wald","forest.php");
        break;
        case 6 :
        output("`#Dein kleiner Begleiter hat keine Chance, und du kannst nichts für ihn tun.`nDie Wölfe zerreissen ".$rowk['name']." und fressen sich vor deinen Augen an ihm satt.`nDoch dann lassen sie dich links liegen und trotten zurück in den tieferen Wald.`nDu brauchst eine halbe Ewigkeit dich zu befreien.`n`4Du hast fast alle deine Lebenspunkte und 5 Waldkämpfe verloren. Dein Knappe ist tot!`0`n");
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
output("Dein Knappe läuft so schnell er kann fort.`n`n");
switch (e_rand(1,3)) {
    case 1 :
    case 2 :
	output("`#Die Zeit vergeht, es kommt dir vor wie Stunden.`nDie Wölfe kommen immer näher, du kannst schon ihre stechenden Augen im Unterholz erkennen.`nDann plötzlich schrecken die Tiere auf und rennen davon als `^".$rowk['name']."`# mit einer kleinen Gruppe Feldarbeiter erscheint.`nDie kräftigen Männer helfen dir dich von der Falle zu befreien und stützen dich auf deinem Weg fort von hier.`n`4Du hast fast alle deine Lebenspunkte und 3 Waldkämpfe verloren!`0`n");
	$session['user']['hitpoints']=1;
    $session['user']['turns']-=3;
    if ($session['user']['turns']<0) $session['user']['turns']=0;
	$session['user']['specialinc'] = "";
	addnav("Zurück zum Wald","forest.php");
    break;
    case 3 :
    output("`#Doch als er mit Hilfe wiederkehrt findet er nur noch deine gründlich abgenagten Knochen und deine zerrissene Ausrüstung bei der Falle wieder.`n`4Du bist tot!`0`n");
    $session['user']['hitpoints']=0;
    $session['user']['specialinc'] = "";
   	addnews($session['user']['name']."`@ wurde im Wald von Wölfen gefressen!`n");
    addnav("Weiter","shades.php");
    break;
    }
}else{
	output("`n");
	output("`#Du gehst ahnungslos deines Weges, als du plötzlich ein lauten, peitschenähnliches Geräusch direkt unter dir vernimmst. Sofort steigt ein brennender Schmerz dein Bein hinauf und deine Knie knicken weg. Halb bewusstlos vor Schmerz erkennst du die scharf gezackten Bügel einer unter Blättern vernorgenen großen Wildfalle, in die du gerade gelaufen bist.`0`n`n");

$sql = "SELECT name,state,level FROM disciples WHERE state>0 AND master=".$session['user']['acctid']."";
$result = db_query($sql) or die(db_error(LINK));

if (db_num_rows($result)>0){
$rowk = db_fetch_assoc($result);
}

if (($rowk['state']>0) || (db_num_rows($result)>0))
{
output("`#Zu allem Übel hörst du noch das Heulen mehrerer Wölfe, die wohl die Witterung deines Blutes aufgenommen haben und bald bei dir sein werden.`nAber zum Glück ist ja dein Knappe `^".$rowk['name']."`# in deiner Nähe.`nObwohl er fast noch ein Kind ist könnte er dich aus dieser Notlage befreien, allerdings könnte er auch genausogut sein Leben hierbei verlieren...`n`nWas tust du?`0");

$session['user']['specialinc'] = $specialinc_file;
addnav($rowk['name']." rufen","forest.php?op=askforhelp");
addnav($rowk['name']." fortschicken","forest.php?op=sendaway");
} else {
output("`#Es dauert eine halbe Ewigkeit bis du dich aus der Falle befreit hast.`nDu greifst dir einen langen Stock als Stütze und humpelst davon.`n`4Du hast fast alle deine Lebenspunkte und 5 Waldkämpfe verloren!`0`n");
$session['user']['hitpoints']=1;
$session['user']['turns']-=5;
if ($session['user']['turns']<0) $session['user']['turns']=0;
addnav("Zurück in den Wald","forest.php");
}
}
?>
