<?
// Addon : Die Auserw�hlten
// Ein Raum f�r Tr�ger aller Male mit einigen Extras
// Ben�tigt : abandonedcastle.php, castleevents.php
// Modifiziert : newday.php, prison.php, inn.php, prefs.php, bio.php, special\vampire.php, graveyard.php
// by Maris (Maraxxus@gmx.de)

require_once "common.php";
checkday();

if ($session['user']['marks']>=31 || su_check(SU_RIGHT_COMMENT)) addcommentary();


if ($session['user']['marks']>=31 || su_check(SU_RIGHT_DEBUG)){

if ($_GET[op]=="bg") {
  page_header("Der Opferaltar des Blutgottes");
output ("`tDu erkundest die sonderbare Feste ein wenig und gelangst in einen Bereich dessen Boden und W�nde komplett mit schwarzem Marmor versehen sind. Im hinteren Bereich steht ein blutverkrusteter Altar, Gravuren in der Wand zeugen von grausigen Szenen.`n");
if (su_check(SU_RIGHT_DEBUG)) { addnav("Blutchamp testen","bloodchamp.php?test=1"); }

if ($session['user']['marks']==31) {
output ("Pl�tzlich spricht dich eine dunkle Stimme an : \"`$  ".($session['user']['name'])." ! `4H�re meine Stimme. Wisse, dass ich der bin, den du den Blutgott nennst. Wisse auch, dass ich nur die M�chtigen und W�rdigen um mich schare, und dass du auserw�hlt bist in meinem Namen Schrecken zu verbreiten! Ich fordere ein Zehnt deiner gesamten Lebenskraft und biete dir daf�r die Gewissheit nie wieder von einem meiner blutsaugenden Diener behelligt zu werden!\"`n");
output ("`n`n`&Willst du f�r ein Zehntel deiner Lebenspunkte einen Pakt mit dem Blutgott eingehen und daf�r Immunit�t gegen Vampire erhalten ?");
addnav("Was nun?");
addnav("Pakt eingehen","thepath.php?op=bg2");
addnav("Lieber nicht","thepath.php");
page_footer();
}
 else if ($session['user']['marks']==32)
 { output ("Zu dir spricht die dunkle Stimme : \"`$  ".($session['user']['name'])." ! `4H�re meine Stimme. Du, " . ($session[user][sex]?"die":"der") . " du auserw�hlt bist unter den Auserw�hlten, und " . ($session[user][sex]?"die":"der") . " du m�chtig bist unter den M�chtigen und " . ($session[user][sex]?"die":"der") . " du in meiner Gunst stehst. Gehe hinaus in die Welt und k�nde von meiner Herrlichkeit! Sage Allen, dass in mir die Ewigkeit ruht! Schare sie um dich, mein Kind, zu meinem Gefallen!\"`n");
output ("`n`n`&Du nimmst dir ein wenig Zeit und verharrst im stillen Gebet.`n");

addnav("Was willst du tun?");
addnav("Pakt brechen","thepath.php?op=bg3"); }

else if ($session['user']['marks']>32)
 { output ("Zu dir spricht die dunkle Stimme : \"`$  ".($session['user']['name'])." ! `4So hast du es nun gewagt der Herausforderung nachzukommen. Dies ist sehr l�blich! Wisse, dass mein Champion auf dich wartet, bereit mir zur Freude ein blutiges Schauspiel zu veranstalten. So gehe nun zu ihm und zeige mir, dass du meiner Gunst w�rdig bist!\"`n");
output ("`n`n`&Es �ffnet sich ein Durchgang in der Wand neben dem Altar.`n");

addnav("Was willst du tun?");
addnav("Pakt brechen","thepath.php?op=bg3");
addnav("Zum Durchgang","bloodchamp.php"); }
addnav("Zur�ck zur Feste","thepath.php");

page_footer();
} else
if ($_GET[op]=="bg3") {
page_header("Der Opferaltar des Blutgottes");
output ("Willst du wirklich deinen Pakt mit dem Blutgott brechen?`n Deine Immunit�t gegen Vampirbisse w�re erloschen und deine geopferte Lebenskraft w�rdest du auch nicht zur�ck bekommen!");
addnav("Sicher?");
addnav("JA! Pakt brechen","thepath.php?op=bg4");
addnav("NEIN! Verklickt...","thepath.php");
page_footer();
}
if ($_GET[op]=="bg4") {
page_header("Der Opferaltar des Blutgottes");
output ("In der festen �berzeugung, dich nicht zum Handlanger irgendwelcher G�tter zu machen zu lassen l�st du den Pakt und der Blutgott wendet sich von dir ab.Du f�hlst dich nun freier.");
$session['user']['marks']=31;
addnav("Zur�ck zur Feste","thepath.php");
page_footer();

} else
if ($_GET[op]=="bg2") {
 page_header("Der Opferaltar des Blutgottes");
output ("`tDu nimmst den Opferdolch und gibst dem Blutgott ein Zehntel deiner Lebenskraft als Opfer dar. In Anerkennung dessen trifft dich ein roter Blitz und brennt winzig klein das Zeichen des Blutgottes in deinen Hals, auf dass jeder Vampir erkenne, dass du in der Gunst des Blutgottes stehst!`n");
$session['user']['marks']=32;
$losthp=$session['user']['maxhitpoints']*0.1;
debuglog("Opferte f�r einen Pakt mit dem Blutgott $losthp permantene LP.");
$session['user']['maxhitpoints']*=0.9;
addnav("Zur�ck zur Feste","thepath.php");
page_footer();
}

else if ($_GET[op]=="charta") {
 page_header("Die Charta der Auserw�hlten");
output ("`tDu steigst eine schmale Treppe hinauf und begibst dich in eine fast quadratische Kammer. An der Stirnwand h�ngt ein riesiger handgefertigter Wandteppich. Es sieht so aus als sei er aus Goldf�den erstellt worden. Auf dem Wandteppich kannst du folgende Schrift lesen :`n");
output ("`n`^Die Charta der Auserw�hlten:`n");
output ("`4I`^    Das Geheimnis um die Auserw�hlten und ihre Macht ist zu h�ten, auf das kein Gew�hnlicher davon erfahre!`n");
output ("`4II`^   Die Auserw�hlten sind Beg�nstigte der G�tter und haben den Gew�hnlichen in jeglicher Hinsicht Vorbild zu sein!`n");
output ("`4III`^  Die den Auserw�hlten verliehenen Kr�fte sind von diesen weise und bedacht einzusetzen!`n");
output ("`4IV`^   Kein Auserw�hlter treibe Schindluder mit der Gunst der G�tter!`n");
output ("`4V`^    Das Geheimnis um die Elementschreine und ihre Male h�te der Auserw�hlte wie sein eigenes Leben!`n");
output ("`4VI`^   Der Auserw�hlte versinke weder in Selbstgef�lligkeit, noch stelle er sich �ber die Gew�hnlichen!`n");
output ("`4VII`^  Er lebe in Demut und ehre die G�tter, die ihm ihre Gunst schenkten!`n");
output ("`4VIII`^ So wie die Gunst der G�tter verg�nglich ist, so vergehen auch die Male der Elemente, sollte der Auserw�hlte freveln!`n");
output ("`n`tDer untere Teil des Gobelins ist frei und kann um einige Punkte erg�nzt werden.`n`n`n");

addnav("Zur�ck zur Feste","thepath.php");
viewcommentary("charta","Den Gobelin besudeln:",30,"schmierte");

} else
if ($_GET[op]=="key") {
 page_header("Der Schl�sselmeister");
output ("`tAls du die Treppen in das Kellergew�lbe der Feste hinabsteigst f�llt dir ein kleiner verwinkelter Holztisch auf, hinter dem ein kauziger Gnom sitzt. Du wei�t nicht wie lange er schon hier unten hockt, jedoch schaut er auf als er Gesellschaft wittert.`n `#\"Willkommen beim Schl�sselmeister!\"`t ,sagt er mit kr�chzender Stimme, \"`#F�r nur `^500 Goldm�nzen `# kannst du von mir erfahren, zu welchen H�usern ein Krieger deiner Wahl Zugang hat!\"`t `nSein Angebot klingt verlockend, und du musst zugeben dass dich ein wenig die Neugier plagt, wer denn wo ein und aus geht.`n");
addnav("500 Gold bezahlen","thepath.php?op=ke2");
addnav("Zur�ck zur Feste","thepath.php");
page_footer();
}
else
if ($_GET[op]=="ke2") {
 page_header("Der Schl�sselmeister");

  if (($session['user']['gold']<500) && ($_GET[who]=="")) {
    output ("`&Peinlich ber�hrt stellst du fest, dass du so viel Gold gar nicht bei dir hast. Also verl�sst du schweigend den Keller.");
    addnav("Zur�ck zur Feste","thepath.php");
    page_footer();
    } else

{
        if ($HTTP_GET_VARS[who]=="") {
            output("\"`#Nun, um wen geht es denn ?`&\"");
            if ($_GET['subop']!="search"){
                output("<form action='thepath.php?op=ke2&subop=search' method='POST'><input name='name'><input type='submit' class='button' value='Suchen'></form>",true);
                addnav("","thepath.php?op=ke2&subop=search");
            }else{
                addnav("Neue Suche","thepath.php?op=ke2");
                $search = "%";
                for ($i=0;$i<strlen($_POST['name']);$i++){
                    $search.=substr($_POST['name'],$i,1)."%";
                }
                $sql = "SELECT name,alive,location,sex,level,laston,loggedin,login FROM accounts WHERE (locked=0 AND name LIKE '$search') ORDER BY level DESC";
                //output($sql);
                $result = db_query($sql) or die(db_error(LINK));
                $max = db_num_rows($result);
                if ($max > 100) {
                    output("`n`n\"`#Geht das vielleicht auch ein klein bisschen genauer? Damit k�nnte ja jeder gemeint sein!`&`n");
                    $max = 100;
                }
                output("<table border=0 cellpadding=0><tr><td>Name</td><td>Level</td></tr>",true);
                for ($i=0;$i<$max;$i++){
                    $row = db_fetch_assoc($result);
                    output("<tr><td><a href='thepath.php?op=ke2&who=".rawurlencode($row[login])."'>$row[name]</a></td><td>$row[level]</td></tr>",true);
                    addnav("","thepath.php?op=ke2&who=".rawurlencode($row[login]));
                }
                output("</table>",true);
            }
        }else{
                $sql = "SELECT acctid,name,house,housekey FROM accounts WHERE login=\"$HTTP_GET_VARS[who]\"";
                $result = db_query($sql) or die(db_error(LINK));
                
                    $row = db_fetch_assoc($result);
                    
                    output("\"`#Dann schauen wir mal wo sich ".($row[name])."`# so �berall rumtreibt...`&\"`n`n");
                    
$sql = "SELECT * FROM keylist WHERE owner=".$row[acctid]." ORDER BY id ASC";
$result = db_query($sql) or die (db_error(LINK));
output("<table cellpadding=2 align='center'><tr><td>`bHausNr.`b</td><td>`bName`b</td></tr>",true);

    if ($row[house]>0 && $row[housekey]>0){

        $sql = "SELECT houseid,housename FROM houses WHERE houseid=".$row[house]." ORDER BY houseid DESC";
        $result2 = db_query($sql) or die(db_error(LINK));
        $row2 = db_fetch_assoc($result2);
if (!$_GET[limit]) { output("<tr><td align='center'>`3$row2[houseid]</td><td>$row2[housename] `&(Eigent�mer)</td></tr>",true); }
    }

if (db_num_rows($result)==0){

        output("<tr><td colspan=4 align='center'>`& ".($row[name])." `i ist obdachlos!`i`0</td></tr>",true);
}else{
        $rebuy=0;
        for ($i=0;$i<db_num_rows($result);$i++){
            $item = db_fetch_assoc($result);
            if ($item[value1]==$row[house] && $row[housekey]==0) $rebuy=1;
            $bgcolor=($i%2==1?"trlight":"trdark");
            $sql = "SELECT houseid,housename FROM houses WHERE houseid=$item[value1] ORDER BY houseid DESC";
            $result2 = db_query($sql) or die(db_error(LINK));
            $row2 = db_fetch_assoc($result2);
            if ($amt!=$item[value1] && $item[value1]!=$row[house]){
                output("<tr class='$bgcolor'><td align='center'>`3$row2[houseid]</td><td>$row2[housename]</td></tr>",true);

            }
            $amt=$item[value1];
        }
    }
                    if (!$_GET[limit]) { $session[user][gold]-=500;
                     }
                    addnav("Zur�ck zur Feste","thepath.php");
output("</table>",true);
output("</span>",true);
            }
        }

page_footer();
}

else {

        page_header("Die Feste der Auserw�hlten");

        output("`b`c`2Die Feste der Auserw�hlten`0`c`b");
        output ("`tDu entdeckst einen verschlungenen Pfad, der tief in den dunklen Wald f�hrt. Je mehr Schritte du diesem Pfad folgst, umso mulmiger wird dir zumute. Doch pl�tzlich beginnen deine 5 Male wie wild zu jucken und du glaubst eine leise Stimme zu h�ren, die dich lockend noch tiefer in den Wald bittet. ");
        output("Entgegen allen Warnungen deines Verstandes folgst du der Stimme und gelangst nach einiger Zeit zu einem kubusf�rmigen, unscheinbaren Geb�ude, das unter dem dichten Bl�tterdach des Waldes kaum sichtbar ist. Du betrittst die kleine Festung in freudiger Erwartung.`n Gl�ckwunsch! Du hast die Feste der Auserw�hlten erreicht. Hier bist du unter Deinesgleichen und kannst von deinen ruhmreichen Taten erz�hlen.");

addnav("Zum goldenen Gobelin","thepath.php?op=charta");
addnav("Der Blutaltar","thepath.php?op=bg");
addnav("Die Halle der Statuen","chosenfeats.php?op=list");
addnav("Der Schl�sselmeister","thepath.php?op=key");
addnav("Dodos Kammer","chosenfeats.php?op=dodo");
addnav("Zum Koboldspitzel","chosenfeats.php?op=imp");
addnav("Zur Wetterhexe","chosenfeats.php?op=hag");
output("`n`n");

        viewcommentary("chosen","Verk�nden:",30,"verk�ndet");

addnav("Zur�ck zum Wald","forest.php");
        
    } } else{

page_header("Der Wald");
output ("`&Du entdeckst einen verschlungenen Pfad, der tief in den dunklen Wald f�hrt. Je mehr Schritte du diesem Pfad folgst, umso mulmiger wird dir zumute. Irgendwann h�lst du es nicht mehr aus und kehrst um. Du bist einfach noch nicht bereit, wei�t aber, dass du es eines Tages sein wirst!");
addnav("Zur�ck","forest.php");
page_footer();
        
    }
page_footer();
?>
