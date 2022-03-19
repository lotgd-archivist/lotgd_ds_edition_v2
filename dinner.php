<?

// Romantisches Dinner bei Cedrik
// Benötigt u. modifiziert : newgiftshop.php
// Modifiziert ggf. bio.php (check auf Tageswechsel)
// Voreingestellt : 1 freies Essen und 3 freie Getränke
//
// Aufschlüsselung [hvalue] in [item] :
// 0 -> Default
// 1 -> Partner anwesend
// Getränke
// 2 -> Wasser
// 3 -> Wein
// 4 -> Ale
// 5 -> Prosecco
// 6 -> Schnaps
// 7 -> Rhizinusöl
// Speisen
//
// 100 -> Wildschweinbraten
// 110 -> Rehrücken
// 120 -> Hirschgulasch
// 130 -> Nudelpfanne
// 140 -> Drachensteak
// 150 -> Tintenfisch
//
// by Maris (Maraxxus@gmx.de)

require_once "common.php";
page_header("Romantisches Dinner für Zwei");
addcommentary();

if ($_GET[op]=="") {
output ("`tDu schreitest in deinem ".($session['user']['sex']?"schönsten Kleid ":"besten Anzug ")." durch die Taverne und wirfst Cedrik einen auffordernden Blick zu. Als er stirnrunzelnd zu dir rüber schaut wedelst du angeberisch mit der Reservierung herum. Leise Seufzend sieht er dich an :`n \"`%Na, das ist ja mal ein Ding! ".($session['user']['sex']?"Die ":"Der ")."Kleine hat ne Verabredung. Ich hoffe doch dass ihr Beide von der Verabredung wißt? Denn wenn du jetzt da hinein gehst werde ich den Gutschein zerreissen und für mich hattest du dein Date, ob du es allein verbingst oder nicht ist mir egal. Also... bist du dir ganz sicher? `t\"");
$essen=1; $trinken=5;
addnav("Ja, auf zum Dinner","dinner.php?op=drin&essen=$essen&trinken=$trinken");
addnav("Öhm... ich warte lieber noch","inn.php"); }

elseif ($_GET[op]=="drin") {
$essen=$_GET[essen];
$trinken=$_GET[trinken];

$row1 = item_get(' tpl_id="dineinl" AND owner='.$session['user']['acctid'],false);

$row2 = item_get(' tpl_id="dineinl" AND owner='.$row1['value1'],false);


if  (is_array($row2))
{

if ($row1['hvalue']==0) {

item_set('id='.$row1['id'],array('hvalue'=>1) );

}

output ("`tCedrik nickt und führt dich in einen kleinen Nebenraum. Hier steht nur  Tisch und 2 Stühle. Der Tisch ist schön hergerichtet, stilecht mit einer langen Kerze. Wunderschöne Tischdecken hängen bis fast zum Boden, edles Geschirr ist darauf plaziert. Im hinteren Teil des Raumes steht eine gemütliche Couch. Von irgendwoher dringt leise Musik an dein Ohr.`n`n");


$sq3 = "SELECT name FROM accounts WHERE acctid=".$row1[value1]."";
$result3=db_query($sq3);
$row3 = db_fetch_assoc($result3);

if ($row2['hvalue']==0) {
output("`tFrohen Mutes nimmst du auf der Couch Platz und wartest, denn `^".$row3[name]."`t ist noch nirgendwo zu sehen... Du bekommst langsam Zweifel ob du dich nicht in der Zeit geirrt hast. Du könntest ja noch etwas länger warten und ein Glas Wasser trinken. Wenn du jetzt jedoch gehst ist dein Gutschein verfallen und dein Date endgültig geplatzt!`n");
addnav("Was nun?");
addnav("Warten...","dinner.php?op=drin&essen=$essen&trinken=$trinken");
addnav("Gehen","dinner.php?op=gehen");
} else {
output("`tDu freust dich enorm als du `^".$row3[name]."`t erblickst. Ihr lächelt euch an setzt euch an den Tisch. Ihr bekommt zur Begrüßung einen kleinen Aperitiv serviert. Ein hübsch gekleideter Knecht zündet für euch die Kerze an und nimmt eure Wünsche entgegen. Natürlich wird er auch auf ein Zeichen schnell verschwinden und euch beide allein lassen. Falls ihr noch etwas braucht oder mit dem Essen beginnen wollt steht ein Glöckchen zum Läuten bereit.`nEr informiert dich, dass du noch `^$essen`t Essen und `^$trinken`t Getränke bestellen kannst.`n");
addnav("Was willst du tun?");
addnav("Ernste Unterhaltung","dinner.php?op=unterhalten&essen=$essen&trinken=$trinken");
addnav("Zum Tanz bitten","dinner.php?op=tanzen&essen=$essen&trinken=$trinken");
addnav("Flirten","dinner.php?op=flirt&who=$row1[value1]&essen=$essen&trinken=$trinken");
addnav("Plaudern","dinner.php?op=reden&m1=$row1[value1]&m2=$row1[value2]&essen=$essen&trinken=$trinken&drink=$row1[havalue]");

addnav("Dinner beenden");
addnav("Gehen","dinner.php?op=gehen");
}
} else { output("`tDu sitzt allein da, weil dein Partner gegangen ist oder die Einladung vernichtet hat. Dir bleibt nichts anderes übrig als jetzt still und leise zu verschwinden.");
addnav("Gehen","dinner.php?op=gehen");
}
}
elseif ($_GET[op]=="reden") {
$essen=$_GET[essen];
$trinken=$_GET[trinken];

$row1 = item_get(' tpl_id="dineinl" AND owner='.$session['user']['acctid'],false);
$drink=$row1[hvalue];
$food=$drink;

output ("`tIhr sitzte euch im Kerzenschein gegenüber und schaut euch in die Augen. In diesem Moment der Stille hört ihr auch die leise Musik recht gut. Der Aperitiv hat euch warm werden lassen und eure Wangen sind leicht gerötet. `n`n");
if ($_GET[m1]<$_GET[m2]) { $temp = "Dinner_".$_GET[m1]."_".$_GET[m2];} else { $temp = "Dinner_".$_GET[m2]."_".$_GET[m1]; }
viewcommentary($temp,"Flüstern:",25,"flüstert");

addnav("Bestellen");
addnav("Getränk bestellen","dinner.php?op=trank&m1=$_GET[m1]&m2=$_GET[m2]&essen=$essen&trinken=$trinken");
addnav("Essen bestellen","dinner.php?op=speis&m1=$_GET[m1]&m2=$_GET[m2]&essen=$essen&trinken=$trinken");
while ($drink>10) {$drink-=10;}
 if ($drink>1) {
addnav("Essen & Trinken");
switch ($drink) {
 case 2 :
  $drink2="Wasser";
  $nbr=1;
 break;
 case 3 :
  $drink2="Wein";
  $nbr=2;
 break;
 case 4 :
  $drink2="Ale";
  $nbr=3;
 break;
 case 5 :
  $drink2="Prosecco";
  $nbr=4;
 break;
 case 6 :
  $drink2="Schnaps";
  $nbr=5;
 break;
 case 7 :
  $drink2="Rhizinusoel";
  $nbr=6;
 break;
 }
addnav($drink2." trinken","dinner.php?op=austrinken&m1=$m1&m2=$m2&essen=$essen&trinken=$trinken&what=$drink2&nbr=$nbr");
addnav($drink2." wegschütten","dinner.php?op=austrinken&subop=weg&m1=$m1&m2=$m2&essen=$essen&trinken=$trinken&what=$drink2&nbr=$nbr");
}
$food-=$drink;
 if ($food>1) {
addnav("Essen & Trinken");
switch ($food) {
 case 100 :
  $food2="Wildschweinbraten";
  $nbr=100;
 break;
 case 110 :
  $food2="Rehruecken";
  $nbr=110;
 break;
 case 120 :
  $food2="Hirschgulasch";
  $nbr=120;
 break;
 case 130 :
  $food2="Nudelpfanne";
  $nbr=130;
 break;
 case 140 :
  $food2="Drachensteak";
  $nbr=140;
 break;
 case 150 :
  $food2="Tintenfisch";
  $nbr=150;
 break;
 }
addnav($food2." essen","dinner.php?op=aufessen&m1=$m1&m2=$m2&essen=$essen&trinken=$trinken&what=$food2&nbr=$nbr");
addnav($food2." wegwerfen","dinner.php?op=aufessen&subop=weg&m1=$m1&m2=$m2&essen=$essen&trinken=$trinken&what=$food2&nbr=$nbr");
}
addnav("Sonstiges");
addnav("Etwas Anderes tun","dinner.php?op=drin&essen=$essen&trinken=$trinken");
}

elseif ($_GET[op]=="trank") {
$essen=$_GET[essen];
$trinken=$_GET[trinken];
$m1=$_GET[m1];
$m2=$_GET[m2];

$sq3 = "SELECT name FROM accounts WHERE acctid=".$m1."";
$result3=db_query($sq3);
$row3 = db_fetch_assoc($result3);
$who=$row3[name];

output ("`tDu läutest das Glöckchen und der Knecht eilt zu dir.`n");
if ($trinken>0) {

$row = item_get(' tpl_id="dineinl" AND owner='.$m1,false);

if (($row['hvalue']<=1) || ($row['hvalue']>100)) {
output ("`tEr sagt dir, dass du noch `^$trinken`t Getränke an diesem Abend bestellen kannst und zeige dir die Karte.`nWas willst du ".$row3[name]."`t denn Schönes gönnen?`n");
addnav("Getränkekarte");
addnav("Wasser","dinner.php?op=trank2&m1=$m1&m2=$m2&what=1&essen=$essen&trinken=$trinken");
addnav("Wein","dinner.php?op=trank2&what=2&m1=$m1&m2=$m2&essen=$essen&trinken=$trinken");
addnav("Ale","dinner.php?op=trank2&what=3&m1=$m1&m2=$m2&essen=$essen&trinken=$trinken");
addnav("Prosecco","dinner.php?op=trank2&what=4&m1=$m1&m2=$m2&essen=$essen&trinken=$trinken");
addnav("Schnaps","dinner.php?op=trank2&what=5&m1=$m1&m2=$m2&essen=$essen&trinken=$trinken");
addnav("Rhizinusoel","dinner.php?op=trank2&what=6&m1=$m1&m2=$m2&essen=$essen&trinken=$trinken");
 } else output ("`tEr teilt dir mit, dass ".$who."`t noch ein Getränk vor sich stehen hat und doch erst austrinken sollte bevor du eine Neues bestellst.");
} else
output ("`tEr teilt dir Bedauern mit, dass du keine Getränke mehr an diesem Abend bestellen kannst.`n");
addnav("Weiterplaudern","dinner.php?op=reden&m1=$m1&m2=$m2&essen=$essen&trinken=$trinken&drink=$hvalue");
}
elseif ($_GET[op]=="trank2") {
$essen=$_GET[essen];
$trinken=$_GET[trinken];
$what=$_GET[what];
$trinken--;
$m1=$_GET[m1];
$m2=$_GET[m2];

$sq3 = "SELECT name FROM accounts WHERE acctid=".$m1."";
$result3=db_query($sq3);
$row3 = db_fetch_assoc($result3);
$who=$row3[name];

switch ($what) {
case 1 :
$drink="`t ein Glas Wasser. Wie langweilig...";
break;
case 2 :
$drink="`t eine kleine Karaffe Wein. Schön rot und kräftig!";
break;
case 3 :
$drink="`t einen Humpen Ale. Na dann Prost!";
break;
case 4 :
$drink="`t ein Gläschen Prosecco. Ein prickelndes alkoholisches Getränk aus fernen Landen.";
break;
case 5 :
$drink="`t einen Fingerhut voll Schnaps. Allein der Geruch macht schon betrunken.";
break;
case 6 :
$drink="`t ein Schälchen Rhizinusöl. Absolut widerlich, aber gut für den Magen.";
break;
 }
output("`tDer Knecht nickt und erfüllt dir deinen Wunsch.`n");
if ($m1<$m2) { $temp = "Dinner_".$m1."_".$m2;} else { $temp = "Dinner_".$m2."_".$m1; }

$row = item_get(' tpl_id="dineinl" AND owner='.$m1,false);

$hvalue=$row['hvalue']+$what;

item_set('id='.$row['id'],array('hvalue'=>$hvalue) );

$sql = "INSERT INTO commentary (postdate,section,author,comment) VALUES (now(),'$temp',".$session[user][acctid].",'/me `tflüstert dem Knecht etwas ins Ohr und dieser serviert ".$who." ".$drink." ')";
db_query($sql) or die(db_error(LINK));
addnav("Weiterplaudern","dinner.php?op=reden&m1=$m1&m2=$m2&essen=$essen&trinken=$trinken&drink=$hvalue");
}

elseif ($_GET[op]=="speis") {
$essen=$_GET[essen];
$trinken=$_GET[trinken];
$m1=$_GET[m1];
$m2=$_GET[m2];

$sq3 = "SELECT name FROM accounts WHERE acctid=".$m1."";
$result3=db_query($sq3);
$row3 = db_fetch_assoc($result3);
$who=$row3[name];

output ("`tDu läutest das Glöckchen und der Knecht eilt zu dir.`n");
if ($essen>0) {

$row = item_get(' tpl_id="dineinl" AND owner='.$m1,false);

if ($row['hvalue']<=100) {
output ("`tEr sagt dir, dass du noch `^$essen`t Hauptgerichte an diesem Abend bestellen kannst und zeige dir die Karte.`nWas willst du ".$row3[name]."`t denn Leckeres bringen lassen?`n");
addnav("Speisekarte");
addnav("Wildschweinbraten","dinner.php?op=speis2&m1=$m1&m2=$m2&what=100&essen=$essen&trinken=$trinken");
addnav("Rehruecken","dinner.php?op=speis2&what=110&m1=$m1&m2=$m2&essen=$essen&trinken=$trinken");
addnav("Hirschgulasch","dinner.php?op=speis2&what=120&m1=$m1&m2=$m2&essen=$essen&trinken=$trinken");
addnav("Nudelpfanne","dinner.php?op=speis2&what=130&m1=$m1&m2=$m2&essen=$essen&trinken=$trinken");
addnav("Drachensteak","dinner.php?op=speis2&what=140&m1=$m1&m2=$m2&essen=$essen&trinken=$trinken");
addnav("Tintenfisch","dinner.php?op=speis2&what=150&m1=$m1&m2=$m2&essen=$essen&trinken=$trinken");
 } else output ("`tEr teilt dir mit, dass ".$who."`t bereits etwas zu Essen bekommen hat und der Teller noch auf dem Tisch steht.");
} else
output ("`tEr teilt dir Bedauern mit, dass du kein Essen mehr an diesem Abend bestellen kannst.`n");
addnav("Weiterplaudern","dinner.php?op=reden&m1=$m1&m2=$m2&essen=$essen&trinken=$trinken&drink=$hvalue");
}
elseif ($_GET[op]=="speis2") {
$essen=$_GET[essen];
$trinken=$_GET[trinken];
$what=$_GET[what];
$essen--;
$m1=$_GET[m1];
$m2=$_GET[m2];

$sq3 = "SELECT name FROM accounts WHERE acctid=".$m1."";
$result3=db_query($sq3);
$row3 = db_fetch_assoc($result3);
$who=$row3[name];

switch ($what) {
case 100 :
$drink="`t knusprigen Wildschweinbraten. Mjamm!";
break;
case 110 :
$drink="`t eine Portion zarten Rehrücken. Das arme Reh...";
break;
case 120 :
$drink="`t ein deftiges Hirschgulach. Es riecht köstlich!";
break;
case 130 :
$drink="`t eine leckere hausgemachte Nudelpfanne mit extra viel Käse.";
break;
case 140 :
$drink="`t ein unglaublich zähes Drachensteak. Wohl nur für Trolle genießbar!";
break;
case 150 :
$drink="`t einen widerlich glibberigen Tintenfisch mit extra langen Fangarmen.";
break;
 }
output("`tDer Knecht nickt und erfüllt dir deinen Wunsch.`n");
if ($m1<$m2) { $temp = "Dinner_".$m1."_".$m2;} else { $temp = "Dinner_".$m2."_".$m1; }

$row = item_get(' tpl_id="dineinl" AND owner='.$m1,false);

$hvalue=$row['hvalue']+$what;

item_set('id='.$row['id'],array('hvalue'=>$hvalue) );

$sql = "INSERT INTO commentary (postdate,section,author,comment) VALUES (now(),'$temp',".$session[user][acctid].",'/me `tflüstert dem Knecht etwas ins Ohr und dieser serviert ".$who." ".$drink." ')";
db_query($sql) or die(db_error(LINK));
addnav("Weiterplaudern","dinner.php?op=reden&m1=$m1&m2=$m2&essen=$essen&trinken=$trinken&drink=$hvalue");
}

elseif ($_GET[op]=="austrinken") {
$essen=$_GET[essen];
$trinken=$_GET[trinken];
$drink=$_GET[nbr];
$drink2=$_GET[what];
$m1=$_GET[m1];
$m2=$_GET[m2];

$row = item_get(' tpl_id="dineinl" AND owner='.$session['user']['acctid'],false);

$hvalue=$row['hvalue']-$drink;

item_set('id='.$row['id'],array('hvalue'=>$hvalue) );

if ($_GET[subop]=="weg") output ("`tHeimlich schüttest du $drink2 in eine Topfpflanze in der Nähe."); else output("`tDu nimmst $drink2 und trinkst es in einem Zug aus.");

if ($m1<$m2) { $temp = "Dinner_".$m1."_".$m2;} else { $temp = "Dinner_".$m2."_".$m1; }
$sql = "INSERT INTO commentary (postdate,section,author,comment) VALUES (now(),'$temp',".$session[user][acctid].",'/me `tist mit dem Getränk fertig.')";
db_query($sql) or die(db_error(LINK));
addnav("Weiterplaudern","dinner.php?op=reden&m1=$m1&m2=$m2&essen=$essen&trinken=$trinken&drink=$hvalue");
}

elseif ($_GET[op]=="aufessen") {
$essen=$_GET[essen];
$trinken=$_GET[trinken];
$food=$_GET[nbr];
$food2=$_GET[what];
$m1=$_GET[m1];
$m2=$_GET[m2];

$row = item_get(' tpl_id="dineinl" AND owner='.$session['user']['acctid'],false);

$hvalue=$row['hvalue']-$food;

item_set('id='.$row['id'],array('hvalue'=>$hvalue) );

if ($_GET[subop]=="weg") output ("`tHeimlich schaufelst du $food2 unter den Tisch."); else output("`tDu stürzt dich auf deine Portion $food2 und isst gierig auf.");

if ($m1<$m2) { $temp = "Dinner_".$m1."_".$m2;} else { $temp = "Dinner_".$m2."_".$m1; }
$sql = "INSERT INTO commentary (postdate,section,author,comment) VALUES (now(),'$temp',".$session[user][acctid].",'/me `tist mit dem Essen fertig.')";
db_query($sql) or die(db_error(LINK));
addnav("Weiterplaudern","dinner.php?op=reden&m1=$m1&m2=$m2&essen=$essen&trinken=$trinken&drink=$hvalue");
}

elseif ($_GET[op]=="flirt") {
$essen=$_GET[essen];
$trinken=$_GET[trinken];
$sql = "SELECT acctid,name,charm,charisma,marriedto FROM accounts WHERE acctid=$_GET[who]";
$result = db_query($sql) or die(db_error(LINK));
if (db_num_rows($result)>0){
$row = db_fetch_assoc($result); }

if  ($session['user']['seenlover']==1) {
output("`tSo gern du jetzt auch flirten möchtest... du hast heute schon und kannst dich irgendwie nicht so recht noch einmal dazu durchringen.`n");
addnav("Etwas Anderes tun","dinner.php?op=drin&essen=$essen&trinken=$trinken");
}
else {
output("`tDu geniesst die Zeit mit ".$row['name']."`t wirklich und ganz allmählich beginnst du deutliche Zeichen zu senden. Sofort entsteht ein heftiger Flirt und ein angeregtes Gespräch. Ihr lacht und fühlt euch wirklich zueinander hingezogen!`n");
output("Ihr schaut euch an und begebt euch für eine Weile zusammen auf die Couch.`n");

if ($session['user']['acctid']==$row['marriedto'] && $session['user']['marriedto']==$row['acctid']) {
		if ($session[user][charisma]<=$row[charisma]) $flirtnum=$session[user][charisma];
		if ($row[charisma]<$session[user][charisma]) $flirtnum=$row[charisma];	// gegens. Flirts
        } else { $flirtnum=0; }
        if (($session[user][marriedto]==4294967295 || $session[user][charisma]==4294967295) && ($row[marriedto]==4294967295 || $row[charisma]==4294967295)) { //beide verheiratet
			if ($session[user][marriedto]==$row[acctid] && $session[user][acctid]==$row[marriedto]){
                // miteinander
				$session['bufflist']['lover']=$buff;
				output("`n`nDu bekommst einen Charmepunkt!`n`n");
				$session['user']['charm']++;
				$session['user']['seenlover']=1;
                systemmail($row[acctid],"`%Flirt!`0","`&".$session['user']['name']."`6 hat gerade damit begonnen dich heftig anzuflirten.");
            } else switch(e_rand(1,4)) {
                // mit wem anders
                case 1 :
                case 2 :
                case 3 :
				$session['user']['seenlover']=1;
				systemmail($row[acctid],"`%Flirt!`0","`&".$session['user']['name']."`6 hat gerade damit begonnen dich heftig anzuflirten.");
				break;
				case 4:
                output("Leider hat euch irgendjemand zusammen im Hinterzimmer der Schenke verschwinden sehen und dies sofort ".($session[user][sex]?"deinem Mann":"deiner Frau")." berichtet.`nDie Situation war sofort klar.`0`n`n".($session[user][sex]?"Dein Mann":"Deine Frau")." verlässt dich!");
				systemmail($session[user]['marriedto'],"`\$Scheidung!`0","`&{$session['user']['name']}`6 wurde gesehen, wie ".($session[user][sex]?"sie":"er")." mit`&{$row[name]} kichernd im Hinterzimmer der Taverne verschwand und du verlässt ".($session[user][sex]?"sie":"ihn").".");
                $sql = "UPDATE accounts SET marriedto=0,charisma=0 WHERE acctid='{$session['user']['marriedto']}'";
	            db_query($sql);
                $session[user][marriedto]=$row[acctid];
	            $session[user][charisma]=1;
	            $session['user']['seenlover']=1;
                systemmail($row[acctid],"`%Flirt!`0","`&".$session['user']['name']."`6 hat gerade damit begonnen dich heftig anzuflirten.");
                break;
              }
		} else if ($session[user][marriedto]==4294967295 || $session[user][charisma]==4294967295) {
              // a verheiratet
              if ($session[user][marriedto]==4294967295 && $session[user][charisma]>=5){
              // Mit Seth/Violet
				output("`%Zu dumm, dass ".($session[user][sex]?"Seth":"Violet")." in dieser Schenke arbeitet und natürlich etwas von dem mitbekommst, was du im Hinterzimmer treibst. ".($session[user][sex]?"Er":"Sie")." verlässt dich...`n");
				$session[user][marriedto]=$row[acctid];
				$session['user']['seenlover']=1;
	     		} else {
				switch(e_rand(1,4)){
					case 1:
					case 2:
					case 3:
			        systemmail($row[acctid],"`%Flirt!`0","`&".$session['user']['name']."`6 hat gerade damit begonnen dich heftig anzuflirten.");
					$session['user']['seenlover']=1;
					break;
					case 4:
					output("Leider hat euch jemand gemeinsam im Hinterzimmer der Taverne verschwinden sehen und auch gleich alles ".($session[user][sex]?"deinem Mann":"deiner Frau")." verraten.`nDie Situation war sofort klar.`0`n`n".($session[user][sex]?"Dein Mann":"Deine Frau")." verlässt dich!");
					if ($session[user][charisma]==4294967295){
						$sql = "UPDATE accounts SET marriedto=0,charisma=0 WHERE acctid='{$session['user']['marriedto']}'";
						db_query($sql);
						systemmail($session[user]['marriedto'],"`\$Scheidung!`0","`&{$session['user']['name']}`6 wurde gesehen, wie ".($session[user][sex]?"sie":"er")." mit `&{$row[name]} im kichernd ins Hinterzimmer der Taverne verschwand. Du verlässt ".($session[user][sex]?"sie":"ihn").".");
					}
					$session[user][marriedto]=$row[acctid];
					$session[user][charisma]=1;
					$session['user']['seenlover']=1;
                    systemmail($row[acctid],"`%Flirt!`0","`&".$session['user']['name']."`6 hat gerade damit begonnen dich heftig anzuflirten.");
					break;
				}
			}
		} else if ($row[marriedto]==4294967295 || $row[charisma]==4294967295) {
          // b verheiratet
			if ($session[user][marriedto]==$row[acctid]){
              // nur b verheiratet
				$session['user']['seenlover']=1;
				output("`%Zu schade, dass $row[name]`% verheiratet ist.`n");
			} else {
                $session[user][charisma]=1;
				$session['user']['seenlover']=1;
			}
			 systemmail($row[acctid],"`%Flirt!`0","`&".$session['user']['name']."`6 hat gerade damit begonnen dich heftig anzuflirten.");
			$session[user][marriedto]=$row[acctid];
		} else {
            // Singles ?
			if ($session[user][marriedto]==$row[acctid]){
              // Flitpartner 
				if ($flirtnum>=5){
                  // Verlobung
					if($session['user']['charisma']!=999) {

						$session['user']['charisma']=999;
						$sql = "UPDATE accounts SET charisma='999' WHERE acctid='$row[acctid]'";
						db_query($sql);
						output("`&`n`n".$row['name']." `& schaut dich plötzlich an und nimmt deine Hand. \"".$session['user']['name']."`4, ich liebe dich und ich möchte mit dir den Rest meines Lebens verbringen! Möchtest du mich heiraten?`n`&\"");
					    output("Es verschlägt dir völlig die Sprache und du kannst nur nicken, mit Tränen in den Augen.`nVon nun an seid ihr beide ein Paar!`n`n");
						output("Ihr seid jetzt verlobt. In nächster Zeit wird ein Priester auf euch zukommen, um die Details eurer Hochzeit zu besprechen. Alternativ könntet natürlich auch ihr Kontakt mit den Priestern im Tempel aufnehmen!`n`n");
						$session[user][seenlover]=1;
						$session[user][donation]+=1;
						addhistory('Verlobung mit '.$row['name'],1,$session['user']['acctid']);
						addhistory('Verlobung mit '.$session['user']['name'],1,$row['acctid']);

						systemmail($row[acctid],"`&Verlobung!`0","`&".$session['user']['name']."`& hat dir heute bei einem romantischen Dinner einen Heiratsantrag gemacht!`nIn nächster Zeit wird ein Priester auf euch zukommen, um die Details eurer Hochzeit zu besprechen. Alternativ könntet natürlich auch ihr Kontakt mit den Priestern im Tempel aufnehmen!".$more);
						$sql = "SELECT acctid FROM accounts WHERE profession=".PROF_PRIEST_HEAD." ORDER BY loggedin DESC,rand() LIMIT 1";
						$res = db_query($sql);
						if(db_num_rows($res)) {
							$p=db_fetch_assoc($res);
							systemmail($p['acctid'],"`&Heirat zu planen!`0","`&".$row['name']."`& und `&".$session['user']['name']."`& haben sich heute verlobt. Du als Priester solltest dich darum bemühen, den beiden eine schöne Hochzeit zu verschaffen!");
						}
					}
					else {
					}
				} else {
                  // Flirts
					$session[user][charisma]+=1;
                    if ($session['user']['charisma']>5) $session['user']['charisma']=5;
                    $session['user']['seenlover']=1;
                   	systemmail($row[acctid],"`%Flirt!`0","`&".$session['user']['name']."`6 hat gerade damit begonnen dich heftig anzuflirten.");
				}
				$session[user][marriedto]=$row[acctid];
			} else {
              // noch nicht geflirtet
                $session['user']['charisma']=1;
                $session['user']['marriedto']=$row['acctid'];
			    $session['user']['seenlover']=1;
                systemmail($row[acctid],"`%Flirt!`0","`&".$session['user']['name']."`6 hat gerade damit begonnen dich heftig anzuflirten.");
			}
		}

addnav("Etwas Anderes tun","dinner.php?op=drin&essen=$essen&trinken=$trinken");
}
}
elseif ($_GET[op]=="tanzen") {
$essen=$_GET[essen];
$trinken=$_GET[trinken];
output("`tDu bittest dein Gegenüber zum Tanz und ihr beide begebt euch in die Mitte des kleinen Raumes, wo ihr eng umschlungen zu der leisen Musik zu tanzen beginnt.`n");

switch(e_rand(1,10))
            {
                case 2:
                    output("`tDa dein Blick auf die Augen deines Partners gerichtet ist und du vor dich hin träumst machst du einen Fehler und dein Partner tritt dir auf den Fuß! AUTSCH! Du verlierst ein paar Lebenspunkte`n");
                $session['user']['hitpoints']-=5;
                if ( $session['user']['hitpoints']<1) $session['user']['hitpoints']=1;
                break;
                case 4:
                    output("`tDu spürst dass die Hand deines Partners nicht da ist wo sie eigentlich sein sollte!`n");
                break;
                case 6:
                    output("`tDu geniesst die Nähe und die Wärme deines Partners und bekommst Lust auf - mehr!.");
                break;
                case 8:
                    output("`tUngeschickt rutscht du aus und machst einen weiten Ausfallschritt um dein Gleichgewicht zu halten! Du hörst ein leises RATSCH... wie peinlich!`n`@Du verlierst einen Charmpunkt`n`n`t");
                   $session['user']['charm']--;
                break;
                case 10:
                    output("`tDu tanzt einfach göttlich und schaffst es deinem Partner wirklich zu imponieren!`n`@Du erhälst einen Charmpunkt`n`n`t");
                    $session['user']['charm']++;
                break;
                default:
                    output("Ihr tanzt eine Weile recht eng und lasst euch schließlich erschöpft in die Couch fallen, um dort ein wenig auszuruhen bevor ihr euch wieder an den Tisch setzt.");
            }
addnav("Etwas Anderes tun","dinner.php?op=drin&essen=$essen&trinken=$trinken");
}
elseif ($_GET[op]=="unterhalten") {
$essen=$_GET[essen];
$trinken=$_GET[trinken];
output("`tDu überlegst kurz und leitest die Unterhaltung geschickt auf ein ernstes Thema um. Der Erfahrungsaustausch mit deinem Partner wird für euch beide von Vorteil sein.`n`n");
if ($session[user][turns] < 1){
        output("`nNur leider bist du so müde, dass du dich nicht auf ein ernstes Gespräch konzentrieren kannst!");
    }else{
        output("`tWieviele Runden willst du das Gespräch führen?`n");
        output("<form action='dinner.php?op=rain2&essen=$essen&trinken=$trinken' method='POST'><input name='trai' id='trai'><input type='submit' class='button' value='Unterhalten'></form>",true);
        output("<script language='JavaScript'>document.getElementById('trai').focus();</script>",true);
        addnav("","dinner.php?op=rain2&essen=$essen&trinken=$trinken");
}
addnav("Etwas Anderes tun","dinner.php?op=drin&essen=$essen&trinken=$trinken");
}
elseif ($_GET[op]=="rain2"){
  $essen=$_GET[essen];
$trinken=$_GET[trinken];
    $trai = abs((int)$HTTP_GET_VARS[trai] + (int)$HTTP_POST_VARS[trai]);
    if ($session[user][turns] <= $trai) $trai = $session[user][turns];
    
        $session[user][turns]-=$trai;
        $exp = $session[user][level]*e_rand(7,15)+e_rand(0,9);
        $totalexp = $exp*$trai;
        $session[user][experience]+=$totalexp;
        output("`^Ihr redet für $trai Runden und du bekommst $totalexp Erfahrungspunkte!`n");
addnav("Etwas Anderes tun","dinner.php?op=drin&essen=$essen&trinken=$trinken");
}
elseif ($_GET[op]=="gehen") {
Output ("`tCedrik zerreißt grinsend deinen Gutschein und wünscht dir noch einen schönen Tag.");

item_delete(' tpl_id="dineinl" AND owner='.$session[user][acctid]);

addnav("Zur Kneipe","inn.php");
}


page_footer();
?>
