<?php
/*
Eaoden's Kettenhemde
Idee by Chaos
Skript by Vaan & Hecki
Erstmals erschienen auf http://www.cop-logd.de
14//12//2004
*/

require_once"common.php";
page_header('Eaoden\'s Kettenhemde');
if ($HTTP_GET_VARS[op]==""){
$session[user][specialinc]="eaoden.php";
if ($session[user][armor]=='D�nnes Kettenhemd'){

output('`2Du triffst im Wald wieder den Krieger der dir dieses dumme Kettenhemd f�r unversch�mte 10 Edelsteine gegeben hat!`n`n');
output('`2Ver�rgert �ber das lausige Gesch�ft, das du gemacht hast, w�rdest du ihm am liebsten das Kettenhemd an den Kopf werfen!`n');
output('`@"Eigentlich keine schlechte Idee", `@denkst du dir!`n');
addnav("Z?Dem Krieger eine verpassen","forest.php?op=werfen");
addnav("Nee lieber nicht","forest.php?op=gehe2");
}
else if ($session[user][armor]=='Kettenhemd eines Kriegers'){
output('`2Du triffst im Wald wieder den Krieger der dir ein starkes Kettenhemd f�r nur 10 Edelsteine gegeben hat!`n`n');
output('`2Du bist immer noch absolut zufrieden mit deinem Kettenhemd und k�nntest ihm nochmal daf�r danken, ein Edelstein w�rde schon ausreichen!`n`n');
addnav("Gib dem Krieger einen Edelstein","forest.php?op=gem");
addnav("Z?Zur�ck in den Wald","forest.php?op=gehe2");
}else{

output('`2Als du so durch den Wald streifst kommst du an einer kleinen H�tte vor bei, vor der ein alter Krieger steht. Der Krieger spricht dich an:');
output('`2"`9Ahh, du musst `@'.$session[user][name].' `9sein!? Ich habe von dir geh�rt... aber ich vermute, dass dir dein/deine '.$session[user][armor].' nicht gen�gend sch�tzt, nicht wahr!?`2"`n`n');
output('`2Du musst feststellen, dass er Recht hat. Er redet weiter. "`9Ich habe hier 2 Kisten, in beiden Kisten ist ein Kettenhemd. Wenn du in die richtige Kiste greifst bekommt du ein gutes Kettenhemd! Ist es aber die falsche Kiste... nun sagen wir so... bekommst du eine... schlechtes Kettenhemd! Der Spa� kostet 10 Edelsteine! Das ist heute ein Sonderangebot! Nur f�r dich alleine! Also? was ist?`2"`n`n');
output('`2Du �berlegst "`@Hmmm... also 10 Edelsteine... das ist wahrhaftig nicht viel.`2"`n`n');
output('`2Was willst du machen?');
addnav("D?Dein Gl�ck versuchen","forest.php?op=vers");
addnav("T?Tss, ich gehe","forest.php?op=gehe");
}
}
else if($HTTP_GET_VARS[op]=="werfen"){
output('`2Du schleuderst das Kettenhemd mit voller Wucht in Richtung des Kriegers, aber Eoaden f�ngt es mit einem gekonnten R�ckw�rtssalto ab und h�lt es nun in seinen H�nden.`n`n');
output('`@"Netter Versuch kleiner, aber untersch�tze niemals einen alten Krieger mit seinen Kettenhemden!"`n`n');
output('`2Durch diese dumme Tat hast du nun auch dein letztes Hemd verloren und stehst wieder mit einem T-Shirt da!');

item_set_armor();

$session[user][specialinc]="";
}

else if($HTTP_GET_VARS[op]=="vers"){
if ($session[user][gems]<5){
output('`2Du bemerkst, dass du nicht gen�gend Edelseteine hast und gehst zur�ck in den Wald.');
addnav("Z?Zur�ck in den Wald","forest.php");
$session[user][specialinc]="";
}
else if ($session[user][gems]>9){
output('`2Du gibst dem Krieger die Edelsteine. Er schiebt dir 2 Kisten hin.');
$session[user][gems]-=10;
switch(e_rand(1,4)){
case 1:
case 2:
output('`2Nach kurzem �berlegen greifst du in die rechte Kiste und ziehst ein d�nnes Kettenhemd hervor.');
output('`2Na toll, du hast das schlechte Kettenhemd gezogen! Beleidigt gehst du zur�ck in den Wald.');
//$sql="INSERT INTO items (name,class,owner,value1,gold,description) VALUES ('`7D�nnes Kettenhemd','R�stung','".$session[user][acctid]."','27','800','R�stung mit 3 Verteidigungswert')";
//db_query($sql);

$arr_armor = array('tpl_name'=>'`7D�nnes Kettenhemd`0','tpl_gold'=>800,'tpl_value1'=>3,'tpl_description'=>'R�stung mit 3 Verteidigungswert.');

$int_id = item_add($session['user']['acctid'],'rstdummy',true,$arr_armor);

item_set_armor($arr_armor['tpl_name'], $arr_armor['tpl_value1'], $arr_armor['tpl_gold'], $int_id, 0, 2);

//addnav("W?Weiter","forest.php');
$session[user][specialinc]="";
break;
case 3:
case 4:
output('`2Nach kurzem �berlegen greifst du in die linke Kiste und ziehst ein dickes, schweres Kettenhemd hervor.');
output('`2Super, du hast das gute Kettenhemd gezogen! Freudig gehst du zur�ck in den Wald.');
//$sql="INSERT INTO items (name,class,owner,value1,gold,description) VALUES ('`7Kettenhemd eines Kriegers','R�stung','".$session[user][acctid]."','27','3800','R�stung mit 30 Verteidigungswert')";
//db_query($sql);

$arr_armor = array('tpl_name'=>'`7Kettenhemd eines Kriegers`0','tpl_gold'=>3800,'tpl_value1'=>25,'tpl_description'=>'R�stung mit 25 Verteidigungswert.');

$int_id = item_add($session['user']['acctid'],'rstdummy',true,$arr_armor);

item_set_armor($arr_armor['tpl_name'], $arr_armor['tpl_value1'], $arr_armor['tpl_gold'], $int_id, 0, 2);

//addnav("W?Weiter","forest.php');
}
}
//break;
$session[user][specialinc]="";
}
else if($HTTP_GET_VARS[op]=="gem"){
if ($session[user][gems]<1){
output('`2Du bemerkst, dass du nicht gen�gend Edelsteine dabei hast und gehst zur�ck in den Wald.');
addnav("Z?Zur�ck in den Wald","forest.php");
$session[user][specialinc]="";
}else{
output('`2Du gibst dem Krieger noch einen Edelstein weil du dein neues Kettenhemd so klasse findest!`n');
output('`2Daraufhin spricht der Krieger eine kleine Zauberformel und dein Kettenhemd bietet dir noch einen Verteidigungspunkt mehr Schutz an!');

item_set_armor('', $session['user']['armordef']+1, -1, 0, 0, 1);

$session[user][gems]--;
$session[user][specialinc]="";
}
}else if($HTTP_GET_VARS[op]=="gehe"){
output('`2Du findest den Preis zu hoch, wendest dich von `9Eaoden `2ab und gehst weiter.');
//addnav("Weiter","forest.php');
}
else if($HTTP_GET_VARS[op]=="gehe2"){
output('`2Du l�sst den Krieger lieber in Ruhe und gehst weiter.');

}

?>