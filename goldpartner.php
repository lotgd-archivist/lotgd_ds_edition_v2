<?php

// "Partnervermittlung" - finde RPG- und Lebenspartner, die zu dir passen.
//
// erste Gedanken und kleine Testumsetzung 18.01.2006
// by Maris (Maraxxus@gmx.de)

require_once "common.php";

function get_match ($rowa,$idb) {

$sql = "SELECT * FROM goldpartner WHERE acctid=$idb";
$result = db_query($sql) or die(db_error(LINK));
$rowb = db_fetch_assoc($result);

$match_count=0;

if ($rowa['sex']==$rowb['lookingfor']) $match_count++;
if ($rowa['lookingfor']==$rowb['sex']) $match_count++;

for($i=1;$i<10;$i++){
if ($rowa['quest'.$i]==$rowb['quest'.$i]) $match_count++;
}

return($match_count);

}

page_header("Goldpartner");

if ($HTTP_GET_VARS[op]=="create"){
	$shortname = preg_replace("([^[:alpha:] _-])","",$HTTP_POST_VARS[name]);
		
		if (soap($shortname)!=$shortname){
			output("`\$Fehler`^: Unzulässiger Name.`n");
			$HTTP_GET_VARS[op]="entry";
		}else{
			$blockaccount=false;
			
			}
			if (strlen($shortname)<3){
				output("Dein Name muss mindestens 3 Buchstaben lang sein.`n");
				$blockaccount=true;
				$HTTP_GET_VARS[op]="entry";
			}
			if (strlen($shortname)>20){
				output("Der Name ist zu lang. Maximal 20 Buchstaben sind erlaubt.`n");
				$blockaccount=true;
				$HTTP_GET_VARS[op]="entry";
			}

			if (!$blockaccount){
				$sql = "SELECT name FROM goldpartner WHERE name='$shortname'";
				$result = db_query($sql) or die(db_error(LINK));
				if (db_num_rows($result)>0){
					output("`\$Fehler`^: Diesen Namen gibt es schon. Bitte versuchs nochmal.`n`n");
					$HTTP_GET_VARS[op]="entry";
				}else{
					$sql = "INSERT INTO goldpartner
						(name,
						lookingfor,
						sex,
						acctid,
						description,
						quest1,quest2,quest3,quest4,quest5,quest6,quest7,quest8,quest9
					) VALUES (
                        '$shortname',
						'$HTTP_POST_VARS[sex]',
						'".$session['user']['sex']."',
						'".$session['user']['acctid']."',
						'$HTTP_POST_VARS[desc]',
						'$_POST[quest1]','$_POST[quest2]','$_POST[quest3]','$_POST[quest4]',
						'$_POST[quest5]','$_POST[quest6]','$_POST[quest7]','$_POST[quest8]',
						'$_POST[quest9]'
					)";
					db_query($sql) or die(db_error(LINK));
					output('`&Du wurdest in die Kartei aufgenommen.`0`n');
					$session['user']['goldinbank']-=1500;
					output('`^Goldpartner hat sich die 1500 Gold dafür von deinem Bankkonto genommen!`n`0');
					debuglog("Aufnahme in die Kartei von Goldpartner unter dem Namen ".$shortname);
					addnav("zurück","goldpartner.php");
				}
			}
		
		}
		
if ($HTTP_GET_VARS[op]=="leave"){
output('`&Möchtest du wirklich aus der Kartei entfernt werden ?`nNiemand könnte dich dann mehr als Partner zugewiesen bekommen!`n`n');
addnav('Ja','goldpartner.php?op=leave_confirmed');
addnav('Nein','goldpartner.php');
}
if ($HTTP_GET_VARS[op]=="leave_confirmed"){
$sql="DELETE FROM goldpartner WHERE acctid=".$session['user']['acctid'];
db_query($sql);
redirect('goldpartner.php');
}

if ($HTTP_GET_VARS[op]=="search"){
            output("`&Nach wem möchtest du suchen?`n(Klicke auf den Namen um mehr zu erfahren, oder auf die Schriftrolle um eine Nachricht zu schreiben)`n`n`&");
            if ($_GET['subop']!="search"){
                output("<form action='goldpartner.php?op=search&subop=search' method='POST'><input name='name'><input type='submit' class='button' value='Suchen'></form>",true);
                addnav("","goldpartner.php?op=search&subop=search");
            }else{
                addnav("Neue Suche","goldpartner.php?op=search");
                $search = "%";
                for ($i=0;$i<strlen($_POST['name']);$i++){
                    $search.=substr($_POST['name'],$i,1)."%";
                }
                $sql = "SELECT name FROM goldpartner WHERE name LIKE '$search' AND acctid<>".$session[user][acctid]." ORDER BY name";
                $result = db_query($sql) or die(db_error(LINK));

output("<table border=0 cellpadding=2 cellspacing=1 bgcolor='#999999'>",true);
output("<tr class='trhead'><td><b>Name</b></td><td><b><img src=\"images/female.gif\">/<img src=\"images/male.gif\"></b></tr>",true);
$max = db_num_rows($result);

for($i=0;$i<$max;$i++){
    $row = db_fetch_assoc($result);
    output("<tr class='".($i%2?"trdark":"trlight")."'><td>",true);
    output("<a href=\"goldpartner.php?op=write&ret=".URLEncode($_SERVER['REQUEST_URI'])."&to=".rawurlencode($row['name'])." \"><img src='images/newscroll.GIF' width='16' height='16' alt='Mail schreiben' border='0'></a>",true);
    addnav("","goldpartner.php?op=write&ret=".URLEncode($_SERVER['REQUEST_URI'])."&to=".rawurlencode($row['name']));
    output("<a href='goldpartner.php?op=seedesc&char=".$row['name']."&ret=".URLEncode($_SERVER['REQUEST_URI'])."'>$row[name]`0",true);
    addnav("","goldpartner.php?op=seedesc&char=".$row['name']."&ret=".URLEncode($_SERVER['REQUEST_URI'])."");
    output("</a>",true);
    output("</td><td align=\"center\">",true);
    output($row[sex]?"<img src=\"images/female.gif\">":"<img src=\"images/male.gif\">",true);
    output("</td></tr>",true);
}
output("</table>",true);
if ($max==0) output('`&`iEs ist niemand derartiges in der Kartei zu finden!`i`0`n');
            }

addnav("Zurück","goldpartner.php");
}

if ($HTTP_GET_VARS[op]=="matching"){
output('`&Der Zwerg bedankt sich für das Gold und beginnt eifrig die Kartei nach Personen zu durchsuchen, die zu dir passen könnte:`n(Klicke auf den Namen um mehr zu erfahren, oder auf die Schriftrolle um eine Nachricht zu schreiben)`n`n');

$sql = "SELECT name,sex,acctid FROM goldpartner WHERE acctid<>".$session[user][acctid];
$result = db_query($sql) or die(db_error(LINK));

output("<table border=0 cellpadding=2 cellspacing=1 bgcolor='#999999'>",true);
output("<tr class='trhead'><td><b>Name</b></td><td><b><img src=\"images/female.gif\">/<img src=\"images/male.gif\"></b></td><td><b>Matching</b></tr>",true);

$sql = "SELECT * FROM goldpartner WHERE acctid=".$session[user][acctid];

$results = db_query($sql) or die(db_error(LINK));
$rowacct = db_fetch_assoc($results);

$max = db_num_rows($result);
$res=0;

for($j=0;$j<10;$j++){
$row2[$j]['match']=0;
  }

for($i=0;$i<$max;$i++){
$row = db_fetch_assoc($result);
$match=get_match($rowacct,$row['acctid']);

  for($j=0;$j<10;$j++){
   if ($match>$row2[$j]['match'])
        {
          $row2[$j-1]=$row2[$j];
          $row2[$j-1]['match']=$row2[$j]['match'];
          $row2[$j]=$row;
          $row2[$j]['match']=$match;
        }
    }
$res++;
}
if ($res>10) $res=10;

for($i=$res-1;$i>-1;$i--){
 output("<tr class='".($i%2?"trdark":"trlight")."'><td>",true);
    if ($session[user][loggedin]) output("<a href=\"goldpartner.php?op=write&ret=".URLEncode($_SERVER['REQUEST_URI'])."&to=".rawurlencode($row2[$i]['name'])." \"><img src='images/newscroll.GIF' width='16' height='16' alt='Mail schreiben' border='0'></a>",true);
    if ($session[user][loggedin]) addnav("","goldpartner.php?op=write&ret=".URLEncode($_SERVER['REQUEST_URI'])."&to=".rawurlencode($row2[$i]['name']));
    if ($session[user][loggedin]) output("<a href='goldpartner.php?op=seedesc&char=".$row2[$i]['name']."&ret=".URLEncode($_SERVER['REQUEST_URI'])."'>",true);
    if ($session[user][loggedin]) addnav("","goldpartner.php?op=seedesc&char=".$row2[$i]['name']."&ret=".URLEncode($_SERVER['REQUEST_URI'])."");
    output($row2[$i][name].'`0');
    if ($session[user][loggedin]) output("</a>",true);
    output("</td><td align=\"center\">",true);
    output($row2[$i][sex]?"<img src=\"images/female.gif\">":"<img src=\"images/male.gif\">",true);
    output("</td><td>",true);
    $matchp=round(($row2[$i]['match']/11)*100);
    output($matchp."%</td><td>",true);
}
output("</table>",true);
if ($res==0) output('`&`iLeider wurde kein Matchingpartner gefunden! Versuche es später noch einmal!`i`n');

if ($_GET['act']!="nopay")
{
$session['user']['goldinbank']-=300;
output('`^`iGoldpartner hat sich die 300 Gold dafür von deinem Bankkonto genommen!`i`0`n');
$_GET['act']=="";
}
addnav("Zurück","goldpartner.php");
}

if ($HTTP_GET_VARS[op]=="listing"){

$ppp=30;
addnav("Blättern");
if (!$_GET[limit])
   {
  $page=0;
   }else{
  $page=(int)$_GET[limit];
  addnav("Zurück blättern","goldpartner.php?op=listing&limit=".($page-1));
  }

$limit="".($page*$ppp).",".($ppp+1);

output('`&Ein Blick in die Kartei zeigt dir folgende Mitglieder:`n(Klicke auf den Namen um mehr zu erfahren, oder auf die Schriftrolle um eine Nachricht zu schreiben)`n`n');
$sql = "SELECT name,sex FROM goldpartner WHERE acctid<>".$session[user][acctid]." ORDER BY name LIMIT $limit";
$result = db_query($sql) or die(db_error(LINK));

output("<table border=0 cellpadding=2 cellspacing=1 bgcolor='#999999'>",true);
output("<tr class='trhead'><td><b>Name</b></td><td><b><img src=\"images/female.gif\">/<img src=\"images/male.gif\"></b></tr>",true);
$max = db_num_rows($result);

for($i=0;$i<$max;$i++){
    $row = db_fetch_assoc($result);
    output("<tr class='".($i%2?"trdark":"trlight")."'><td>",true);
    output("<a href=\"goldpartner.php?op=write&ret=".URLEncode($_SERVER['REQUEST_URI'])."&to=".rawurlencode($row['name'])." \"><img src='images/newscroll.GIF' width='16' height='16' alt='Mail schreiben' border='0'></a>",true);
    addnav("","goldpartner.php?op=write&ret=".URLEncode($_SERVER['REQUEST_URI'])."&to=".rawurlencode($row['name']));
    output("<a href='goldpartner.php?op=seedesc&char=".$row['name']."&ret=".URLEncode($_SERVER['REQUEST_URI'])."'>$row[name]`0",true);
    addnav("","goldpartner.php?op=seedesc&char=".$row['name']."&ret=".URLEncode($_SERVER['REQUEST_URI'])."");
    output("</a>",true);
    output("</td><td align=\"center\">",true);
    output($row[sex]?"<img src=\"images/female.gif\">":"<img src=\"images/male.gif\">",true);
    output("</td></tr>",true);
}
output("</table>",true);

$sql = "SELECT name,sex FROM goldpartner WHERE acctid<>".$session[user][acctid]." ORDER BY name";
$resultl = db_query($sql) or die(db_error(LINK));
$maxl = db_num_rows($resultl);
output('`n`n`^Die Kartei enthält derzeit '.($maxl+1).' Einträge.`n`&');

if ($max>$ppp) addnav("Weiter blättern","goldpartner.php?op=listing&limit=".($page+1));
addnav("Sonstiges");
addnav("Neu laden","goldpartner.php?op=listing");
addnav("Zurück","goldpartner.php");
}

if ($HTTP_GET_VARS[op]=="seedesc"){
$who=rawurldecode($_GET['char']);
$sql = "SELECT name,acctid,description FROM goldpartner WHERE name='$who'";
$result = db_query($sql) or die(db_error(LINK));
$row = db_fetch_assoc($result);

$sql = "SELECT * FROM goldpartner WHERE acctid=".$session[user][acctid];
$results = db_query($sql) or die(db_error(LINK));
$rowacct = db_fetch_assoc($results);

$match=get_match($rowacct,$row['acctid']);
$matchp=round(($match/11)*100);
output('Matching mit dir: '.$matchp.'%`n`n');
if ($row['description'])
{
output($row['name'].' `0beschreibt sich folgendermaßen:`n'.$row['description']);
}

$return = preg_replace("'[&?]c=[[:digit:]-]+'","",$_GET[ret]);
$return = substr($return,strrpos($return,"/")+1);
addnav("Zurück",$return."&act=nopay");
}

if ($HTTP_GET_VARS[op]=="write"){
$who=rawurldecode($_GET['to']);
$sql = "SELECT name FROM goldpartner WHERE name='$who'";
$result = db_query($sql) or die(db_error(LINK));
$row = db_fetch_assoc($result);
output('`&Mail schreiben an '.$row['name'].'`0.`n`n');

$link = 'goldpartner.php?op=write2&ret='.$_GET[ret].'&char='.$_GET[to];
output("<form action='".$link."' method='POST'>",true);
output("Deine Botschaft: <input type='text' name='message' size='100' maxlength='500'>`n`n",true);
output("<input type='submit' class='button' value='Nachricht losschicken!'></form>",true);
addnav('',$link);

$return = preg_replace("'[&?]c=[[:digit:]-]+'","",$_GET[ret]);
$return = substr($return,strrpos($return,"/")+1);
addnav("Zurück",$return."&act=nopay");
}

if ($HTTP_GET_VARS[op]=="write2"){
$who=rawurldecode($_GET['char']);
$msg = $_POST['message'];
$sql = "SELECT acctid FROM goldpartner WHERE name='$who'";
$result = db_query($sql) or die(db_error(LINK));
$row = db_fetch_assoc($result);

$sql = "SELECT name FROM goldpartner WHERE acctid=".$session[user][acctid];
$result = db_query($sql) or die(db_error(LINK));
$rowb = db_fetch_assoc($result);

systemmail($row[acctid],"`rGoldpartner : Nachricht von `^".$rowb['name']."`0!",$msg."`n`n`&(Antworte nicht auf diese Mail! Sie würde nie ankommen... Benutze lieber den kostengünstigen Service von `^Goldpartner`0)");
output('`n`&Deine Nachricht wurde verschickt.`n');
$session['user']['goldinbank']-=20;
output('`^`iGoldpartner hat sich die 20 Gold dafür von deinem Bankkonto genommen!`i`0`n');
$return = preg_replace("'[&?]c=[[:digit:]-]+'","",$_GET[ret]);
$return = substr($return,strrpos($return,"/")+1);
addnav("Zurück",$return."&act=nopay");
}

if ($HTTP_GET_VARS[op]=="costs"){
output('`&`c`^Goldpartner`&s Preisliste`c`n
Goldpartner führt viele exklusive Dienste für dich durch, die aber leider nicht alle umsonst sind. Hiereine kleine Liste des geringen Obulus, den der Zwerg von dir beansprucht :`n`n
- Aufnahme in die Kartei: `^1500 Gold`&`n
- Beschreibung ändern: `^500 Gold`&`n
- Matchingsuche: `^300 Gold`&`n
- Nachricht versenden: `^20 Gold`&`n
- Einsicht in die Kartei: `^Gratis!`&`n
- Personensuche: `^Gratis!`&`n
- Abmeldung: `^Gratis!`&`n`n
`iDamit niemand unnötig sein Gold hierher tragen muss, holt `^Goldpartner`& es direkt von eurem Bankkonto. Und das ohne Aufpreis!`i`n');
addnav("Zurück","goldpartner.php");
}

if ($HTTP_GET_VARS[op]=="entry"){
		output("`&`c`bAufnahme in die Kartei`b`c`n");
		output("`^Bevor der Zwerg dich in die Liste seiner zahlungskräftigen Kunden aufnimmt und deinen Geldbeuter erleichtert, möchte er gern noch ein paar Dinge von dir wissen, die es leichter machen sollen einen passenden Partner für dich zu finden, wozu auch immer...`n`n`&Zuerst Grundlegendes :`n`n");
        output("`0<form action=\"goldpartner.php?op=create\" method='POST'>",true);
		output("`nWie willst du in dieser Kartei heissen? <input name='name'>`n",true);
		output("`&(Du kannst hier deinen Namen eingeben oder einen Phantasienamen wählen.)`n`n");

        output("`nDu suchst bevorzugt
        <input type='radio' name='sex' value='1' ".($_POST[sex]?'checked="checked"' : '')." >weibliche oder
        <input type='radio' name='sex' value='0' ".(!$_POST[sex]?'checked="checked"' : '')." >männliche Kontakte?`n`n",true);

        output("`&`n<u>Nun ein paar Fragen zu deinem Wesen, die du so ehrlich wie möglich beantworten solltest :</u>`n`n");
		output("`n`^Wie spendabel bist du?`0`n
        <input type='radio' name='quest1' value='3' ".($_POST[quest1]==3?'checked="checked"' : '')." >Überhaupt nicht, ganz im Gegenteil.`n
        <input type='radio' name='quest1' value='2' ".($_POST[quest1]==2?'checked="checked"' : '')." >Ich bin eher geizig.`n
        <input type='radio' name='quest1' value='1' ".($_POST[quest1]==1?'checked="checked"' : '')." >Wenn es sich lohnt gebe ich schon gern mal etwas aus.`n
        <input type='radio' name='quest1' value='0' ".($_POST[quest1]==0?'checked="checked"' : '')." >Am Liebsten würde ich die ganze Welt beschenken!`n
        `n",true);
		
		output("`n`^Wenn du die Wahl hättest, wärst du lieber...`0`n
        <input type='radio' name='quest2' value='4' ".($_POST[quest2]==4?'checked="checked"' : '')." >...ein mächtiger Herrscher.`n
        <input type='radio' name='quest2' value='3' ".($_POST[quest2]==3?'checked="checked"' : '')." >...ein gefürchteter Kriegsfürst.`n
        <input type='radio' name='quest2' value='2' ".($_POST[quest2]==2?'checked="checked"' : '')." >...ein reicher Kaufmann.`n
        <input type='radio' name='quest2' value='1' ".($_POST[quest2]==1?'checked="checked"' : '')." >...ein unauffälliger Bürger.`n
        <input type='radio' name='quest2' value='0' ".($_POST[quest2]==0?'checked="checked"' : '')." >...ein demütier Mönch.`n
        `n",true);

        output("`n`^Wo hälst du dich am Liebsten auf?`0`n
        <input type='radio' name='quest3' value='3' ".($_POST[quest3]==3?'checked="checked"' : '')." >In verlassenen Häusern, Privatgemächern oder auf einsamen Lichtungen.`n
        <input type='radio' name='quest3' value='2' ".($_POST[quest3]==2?'checked="checked"' : '')." >In belebten Häusern oder Gilden.`n
        <input type='radio' name='quest3' value='1' ".($_POST[quest3]==1?'checked="checked"' : '')." >Auf öffentlichen Plätzen.`n
        <input type='radio' name='quest3' value='0' ".($_POST[quest3]==0?'checked="checked"' : '')." >Überall dort, wo etwas los ist.`n
        `n",true);

        output("`n`^Wie friedfertig bist du?`0`n
        <input type='radio' name='quest4' value='3' ".($_POST[quest4]==3?'checked="checked"' : '')." >Ich meide jede Art von Konflikt, auch wenn es zu meinem Nachteil ist.`n
        <input type='radio' name='quest4' value='2' ".($_POST[quest4]==2?'checked="checked"' : '')." >Ich gehe Konflikten aus dem Weg, scheue mich aber nicht davor sie wahrzunehmen.`n
        <input type='radio' name='quest4' value='1' ".($_POST[quest4]==1?'checked="checked"' : '')." >Wenn es sein muss werde ich auch mal handgreiflich, um mich und meine Freunde zu verteidigen.`n
        <input type='radio' name='quest4' value='0' ".($_POST[quest4]==0?'checked="checked"' : '')." >Ein Tag ohne Prügelei ist kein guter Tag!`n
        `n",true);
        
        output("`n`^Wer hat deiner Meinung nach in einer Beziehung das Sagen?`0`n
        <input type='radio' name='quest5' value='3' ".($_POST[quest5]==3?'checked="checked"' : '')." >Das kommt auf die Personen an.`n
        <input type='radio' name='quest5' value='2' ".($_POST[quest5]==2?'checked="checked"' : '')." >Die Frau.`n
        <input type='radio' name='quest5' value='1' ".($_POST[quest5]==1?'checked="checked"' : '')." >Der Mann.`n
        <input type='radio' name='quest5' value='0' ".($_POST[quest5]==0?'checked="checked"' : '')." >Beide sollten gleichberechtigt sein.`n
        `n",true);
        
        output("`n`^Was findest du an Anderen anziehend?`0`n
        <input type='radio' name='quest6' value='3' ".($_POST[quest6]==3?'checked="checked"' : '')." >Reichtum und Macht.`n
        <input type='radio' name='quest6' value='2' ".($_POST[quest6]==2?'checked="checked"' : '')." >Aussehen und Ausstrahlung.`n
        <input type='radio' name='quest6' value='1' ".($_POST[quest6]==1?'checked="checked"' : '')." >Verlässlichkeit und Treue.`n
        <input type='radio' name='quest6' value='0' ".($_POST[quest6]==0?'checked="checked"' : '')." >Verhalten und Wesensart.`n
        `n",true);
        
        output("`n`^Wofür interessierst du dich mehr?`0`n
        <input type='radio' name='quest7' value='3' ".($_POST[quest7]==3?'checked="checked"' : '')." >Kunst.`n
        <input type='radio' name='quest7' value='2' ".($_POST[quest7]==2?'checked="checked"' : '')." >Wissen.`n
        <input type='radio' name='quest7' value='1' ".($_POST[quest7]==1?'checked="checked"' : '')." >Kampf.`n
        <input type='radio' name='quest7' value='0' ".($_POST[quest7]==0?'checked="checked"' : '')." >Andere Wesen.`n
        `n",true);
        
        output("`n`^Wie stehst du zu Lügen?`0`n
        <input type='radio' name='quest8' value='3' ".($_POST[quest8]==3?'checked="checked"' : '')." >Wer alles glaubt, ist selbst Schuld!`n
        <input type='radio' name='quest8' value='2' ".($_POST[quest8]==2?'checked="checked"' : '')." >Nötig, da die Wahrheit oft unnötig schmerzen kann.`n
        <input type='radio' name='quest8' value='1' ".($_POST[quest8]==1?'checked="checked"' : '')." >In Ausnahmefällen eine Notlüge ist schon in Ordnung.`n
        <input type='radio' name='quest8' value='0' ".($_POST[quest8]==0?'checked="checked"' : '')." >Niemals würde ich lügen.`n
        `n",true);

        output("`n`^Wie leicht bist du reizbar?`0`n
        <input type='radio' name='quest9' value='3' ".($_POST[quest9]==3?'checked="checked"' : '')." >Ich bin die Ruhe in Person.`n
        <input type='radio' name='quest9' value='2' ".($_POST[quest9]==2?'checked="checked"' : '')." >So schnell bringt mich nichts in Rage.`n
        <input type='radio' name='quest9' value='1' ".($_POST[quest9]==1?'checked="checked"' : '')." >Ich bin schnell reizbar.`n
        <input type='radio' name='quest9' value='0' ".($_POST[quest9]==0?'checked="checked"' : '')." >DAS GEHT DICH GAR NICHTS AN, DU WURM!!!`n
        `n",true);
        
        output("`n`^Zuletzt kann du dich noch kurz (200 Zeichen) beschreiben:`0`n
        <input name='desc' size='80' maxlength='200' value='".$_POST[desc]."'>
       `n",true);
         
        output("<input type='submit' class='button' value='Beitreten'>",true);
		addnav('','goldpartner.php?op=create');

addnav("Zurück","goldpartner.php");
}

if ($HTTP_GET_VARS[op]=="change")
{
$sql = "SELECT description FROM goldpartner WHERE acctid=".$session[user][acctid];
$result = db_query($sql) or die(db_error(LINK));
$row = db_fetch_assoc($result);
output('`&Deine bisherige Beschreibung sieht folgendermaßen aus:`n`n'.$row['description'].'`n`n');
output("`0<form action=\"goldpartner.php?op=preview\" method='POST'>",true);
output("`n`^Du kannst deine Beschreibung (200 Zeichen) hier ändern:`0`n
<input name='desc' size='80' maxlength='200' value='".$row['description']."'>",true);
output("<input type='submit' class='button' value='Vorschau'>",true);
addnav('','goldpartner.php?op=preview');
addnav("Zurück","goldpartner.php");
}

if ($HTTP_GET_VARS[op]=="preview")
{
$desc=$HTTP_POST_VARS[desc];

if (isset($_POST['confirm']))
{
$desc=$HTTP_POST_VARS[desc];
$sql = 'UPDATE goldpartner SET description="'.$desc.'" WHERE acctid='.$session[user][acctid];
output($sql);
db_query($sql);
output('`&Deine neue Beschreibung wurde übernommen.`0`n');
$session['user']['goldinbank']-=500;
output('`^Goldpartner hat sich die 500 Gold dafür von deinem Bankkonto genommen!`n`0');
addnav("Zurück","goldpartner.php");
}
else
{
output('`&Deine Beschreibung wird nach Bestätigung folgendermaßen aussehen:`n`n'.$desc);
output("`0<form action=\"goldpartner.php?op=preview\" method='POST'>",true);
output("`n`^Du kannst deine Beschreibung (200 Zeichen) hier ändern:`0`n
<input name='desc' size='80' maxlength='200' value='".$desc."'>",true);
output("<input type='submit' class='button' name='preview' value='Vorschau'>",true);
output("`n<input type='submit' class='button' name='confirm' value='Annehmen'></form>",true);
addnav('','goldpartner.php?op=preview');
addnav("Zurück","goldpartner.php");
}
}

if ($HTTP_GET_VARS[op]=="rules")
{
output('`&Verhaltensregeln bei `^Goldpartner`&`n`n
1. Für die Wahl des Namens gibt es (fast) keine Einschränkungen. Beleidigende, obszöne oder sonstwie unpassende Namen sind jedoch verboten.`n
2. Der Name muss keinen Hinweis auf den Charakter enthalten. Die Kontaktaufnahme kann zunächst anonym erfolgen.`n
3. Für die Kurzbeschreibung gilt ebenso Punkt 1, HTML und Links sind erlaubt.`n
4. Die Mails sind zwar für den Empfänger "anonym", jedoch ist es im Falle von Beleidungenen etc einfach den Verfasser ausfindig zu machen.`n
5. Das Aufdecken von Namen ist STRENG VERBOTEN! Wer herausfindet, wer sich hinter einem bestimmten Namen verbirgt, der behalte es für sich!`n
6. Es ist nicht möglich die Anmeldedaten bzw Kurzbeschreibung zu ändern. Wer etwas ändern will, der lösche sich und melde sich erneut an!`n
7. Verstöße gegen diese Regeln führen nicht nur zum Ausschluss von Goldpartner, sondern können auch zu Serverbann, Kerker etc führen!`n`n
(Anmerkung: Hier können nicht nur Lebenspartner gesucht und gefunden werden, sondern auch neue Mitspieler fürs RPG. Daher gibt es auch keinen geschlechterspezifischen Filter.)');
addnav("zurück","goldpartner.php");
}

if ($HTTP_GET_VARS[op]=="")
{
  output('`&Du betrittst die kleine Hütte am Rande des Marktplatzes. `^Goldpartner`& steht in großen kitschig wirkenden Lettern über der Tür geschrieben. Sofort fällt dir ein gut beleibter Zwerg auf, der dich auch gleich anspricht :`n"`6Willkommen bei Goldpartner!`&", sagt er mit dunkler Stimme,"`6Das Geschäft ist schnell erklärt : Ihr gebt mir `^Gold`6 und ich beschaffe Euch einen `^Partner`6! Klingt doch gar nicht so kompliziert, oder ?`&"`nWährend er zu dir spricht reibt er sich den dicken Bauch und du denkst dir, dass dieser Zwerg nicht so gut im Futter stände, wenn seine Geschäftsidee erfolglos geblieben wäre.');
addnav('Formalitäten');
$sql = "SELECT name FROM goldpartner WHERE acctid=".$session[user][acctid];
$result = db_query($sql) or die(db_error(LINK));
if (db_num_rows($result)>0){
addnav('Abmelden','goldpartner.php?op=leave');
addnav('Beschreibung','goldpartner.php?op=change');
addnav('Goldpartner');
addnav('Kartei durchstöbern','goldpartner.php?op=listing');
addnav('Jemanden suchen','goldpartner.php?op=search');
addnav('Matching','goldpartner.php?op=matching');
}
else addnav('Aufnahmeantrag','goldpartner.php?op=entry');
addnav('Informationen');
addnav('Verhaltensregeln','goldpartner.php?op=rules');
addnav('Preisliste','goldpartner.php?op=costs');
addnav('Raus hier');
addnav("zurück","market.php");
}
page_footer();
?>
