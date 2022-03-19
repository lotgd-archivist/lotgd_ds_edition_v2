<?
// Addon : Die Auserwählten
// Ein Raum für Träger aller Male mit einigen Extras
// Benötigt : abandonedcastle.php, castleevents.php
// Modifiziert : newday.php, prison.php, inn.php, prefs.php, bio.php, special\vampire.php, graveyard.php
// by Maris (Maraxxus@gmx.de)

require_once "common.php";
checkday();

if ($session['user']['marks']>=31 || su_check(SU_RIGHT_COMMENT)) addcommentary();


if ($session['user']['marks']>=31 || su_check(SU_RIGHT_DEBUG)){

if ($_GET[op]=="bg") {
  page_header("Der Opferaltar des Blutgottes");
output ("`tDu erkundest die sonderbare Feste ein wenig und gelangst in einen Bereich dessen Boden und Wände komplett mit schwarzem Marmor versehen sind. Im hinteren Bereich steht ein blutverkrusteter Altar, Gravuren in der Wand zeugen von grausigen Szenen.`n");
if (su_check(SU_RIGHT_DEBUG)) { addnav("Blutchamp testen","bloodchamp.php?test=1"); }

if ($session['user']['marks']==31) {
output ("Plötzlich spricht dich eine dunkle Stimme an : \"`$  ".($session['user']['name'])." ! `4Höre meine Stimme. Wisse, dass ich der bin, den du den Blutgott nennst. Wisse auch, dass ich nur die Mächtigen und Würdigen um mich schare, und dass du auserwählt bist in meinem Namen Schrecken zu verbreiten! Ich fordere ein Zehnt deiner gesamten Lebenskraft und biete dir dafür die Gewissheit nie wieder von einem meiner blutsaugenden Diener behelligt zu werden!\"`n");
output ("`n`n`&Willst du für ein Zehntel deiner Lebenspunkte einen Pakt mit dem Blutgott eingehen und dafür Immunität gegen Vampire erhalten ?");
addnav("Was nun?");
addnav("Pakt eingehen","thepath.php?op=bg2");
addnav("Lieber nicht","thepath.php");
page_footer();
}
 else if ($session['user']['marks']==32)
 { output ("Zu dir spricht die dunkle Stimme : \"`$  ".($session['user']['name'])." ! `4Höre meine Stimme. Du, " . ($session[user][sex]?"die":"der") . " du auserwählt bist unter den Auserwählten, und " . ($session[user][sex]?"die":"der") . " du mächtig bist unter den Mächtigen und " . ($session[user][sex]?"die":"der") . " du in meiner Gunst stehst. Gehe hinaus in die Welt und künde von meiner Herrlichkeit! Sage Allen, dass in mir die Ewigkeit ruht! Schare sie um dich, mein Kind, zu meinem Gefallen!\"`n");
output ("`n`n`&Du nimmst dir ein wenig Zeit und verharrst im stillen Gebet.`n");

addnav("Was willst du tun?");
addnav("Pakt brechen","thepath.php?op=bg3"); }

else if ($session['user']['marks']>32)
 { output ("Zu dir spricht die dunkle Stimme : \"`$  ".($session['user']['name'])." ! `4So hast du es nun gewagt der Herausforderung nachzukommen. Dies ist sehr löblich! Wisse, dass mein Champion auf dich wartet, bereit mir zur Freude ein blutiges Schauspiel zu veranstalten. So gehe nun zu ihm und zeige mir, dass du meiner Gunst würdig bist!\"`n");
output ("`n`n`&Es öffnet sich ein Durchgang in der Wand neben dem Altar.`n");

addnav("Was willst du tun?");
addnav("Pakt brechen","thepath.php?op=bg3");
addnav("Zum Durchgang","bloodchamp.php"); }
addnav("Zurück zur Feste","thepath.php");

page_footer();
} else
if ($_GET[op]=="bg3") {
page_header("Der Opferaltar des Blutgottes");
output ("Willst du wirklich deinen Pakt mit dem Blutgott brechen?`n Deine Immunität gegen Vampirbisse wäre erloschen und deine geopferte Lebenskraft würdest du auch nicht zurück bekommen!");
addnav("Sicher?");
addnav("JA! Pakt brechen","thepath.php?op=bg4");
addnav("NEIN! Verklickt...","thepath.php");
page_footer();
}
if ($_GET[op]=="bg4") {
page_header("Der Opferaltar des Blutgottes");
output ("In der festen Überzeugung, dich nicht zum Handlanger irgendwelcher Götter zu machen zu lassen löst du den Pakt und der Blutgott wendet sich von dir ab.Du fühlst dich nun freier.");
$session['user']['marks']=31;
addnav("Zurück zur Feste","thepath.php");
page_footer();

} else
if ($_GET[op]=="bg2") {
 page_header("Der Opferaltar des Blutgottes");
output ("`tDu nimmst den Opferdolch und gibst dem Blutgott ein Zehntel deiner Lebenskraft als Opfer dar. In Anerkennung dessen trifft dich ein roter Blitz und brennt winzig klein das Zeichen des Blutgottes in deinen Hals, auf dass jeder Vampir erkenne, dass du in der Gunst des Blutgottes stehst!`n");
$session['user']['marks']=32;
$losthp=$session['user']['maxhitpoints']*0.1;
debuglog("Opferte für einen Pakt mit dem Blutgott $losthp permantene LP.");
$session['user']['maxhitpoints']*=0.9;
addnav("Zurück zur Feste","thepath.php");
page_footer();
}

else if ($_GET[op]=="charta") {
 page_header("Die Charta der Auserwählten");
output ("`tDu steigst eine schmale Treppe hinauf und begibst dich in eine fast quadratische Kammer. An der Stirnwand hängt ein riesiger handgefertigter Wandteppich. Es sieht so aus als sei er aus Goldfäden erstellt worden. Auf dem Wandteppich kannst du folgende Schrift lesen :`n");
output ("`n`^Die Charta der Auserwählten:`n");
output ("`4I`^    Das Geheimnis um die Auserwählten und ihre Macht ist zu hüten, auf das kein Gewöhnlicher davon erfahre!`n");
output ("`4II`^   Die Auserwählten sind Begünstigte der Götter und haben den Gewöhnlichen in jeglicher Hinsicht Vorbild zu sein!`n");
output ("`4III`^  Die den Auserwählten verliehenen Kräfte sind von diesen weise und bedacht einzusetzen!`n");
output ("`4IV`^   Kein Auserwählter treibe Schindluder mit der Gunst der Götter!`n");
output ("`4V`^    Das Geheimnis um die Elementschreine und ihre Male hüte der Auserwählte wie sein eigenes Leben!`n");
output ("`4VI`^   Der Auserwählte versinke weder in Selbstgefälligkeit, noch stelle er sich über die Gewöhnlichen!`n");
output ("`4VII`^  Er lebe in Demut und ehre die Götter, die ihm ihre Gunst schenkten!`n");
output ("`4VIII`^ So wie die Gunst der Götter vergänglich ist, so vergehen auch die Male der Elemente, sollte der Auserwählte freveln!`n");
output ("`n`tDer untere Teil des Gobelins ist frei und kann um einige Punkte ergänzt werden.`n`n`n");

addnav("Zurück zur Feste","thepath.php");
viewcommentary("charta","Den Gobelin besudeln:",30,"schmierte");

} else
if ($_GET[op]=="key") {
 page_header("Der Schlüsselmeister");
output ("`tAls du die Treppen in das Kellergewölbe der Feste hinabsteigst fällt dir ein kleiner verwinkelter Holztisch auf, hinter dem ein kauziger Gnom sitzt. Du weißt nicht wie lange er schon hier unten hockt, jedoch schaut er auf als er Gesellschaft wittert.`n `#\"Willkommen beim Schlüsselmeister!\"`t ,sagt er mit krächzender Stimme, \"`#Für nur `^500 Goldmünzen `# kannst du von mir erfahren, zu welchen Häusern ein Krieger deiner Wahl Zugang hat!\"`t `nSein Angebot klingt verlockend, und du musst zugeben dass dich ein wenig die Neugier plagt, wer denn wo ein und aus geht.`n");
addnav("500 Gold bezahlen","thepath.php?op=ke2");
addnav("Zurück zur Feste","thepath.php");
page_footer();
}
else
if ($_GET[op]=="ke2") {
 page_header("Der Schlüsselmeister");

  if (($session['user']['gold']<500) && ($_GET[who]=="")) {
    output ("`&Peinlich berührt stellst du fest, dass du so viel Gold gar nicht bei dir hast. Also verlässt du schweigend den Keller.");
    addnav("Zurück zur Feste","thepath.php");
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
                    output("`n`n\"`#Geht das vielleicht auch ein klein bisschen genauer? Damit könnte ja jeder gemeint sein!`&`n");
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
                    
                    output("\"`#Dann schauen wir mal wo sich ".($row[name])."`# so überall rumtreibt...`&\"`n`n");
                    
$sql = "SELECT * FROM keylist WHERE owner=".$row[acctid]." ORDER BY id ASC";
$result = db_query($sql) or die (db_error(LINK));
output("<table cellpadding=2 align='center'><tr><td>`bHausNr.`b</td><td>`bName`b</td></tr>",true);

    if ($row[house]>0 && $row[housekey]>0){

        $sql = "SELECT houseid,housename FROM houses WHERE houseid=".$row[house]." ORDER BY houseid DESC";
        $result2 = db_query($sql) or die(db_error(LINK));
        $row2 = db_fetch_assoc($result2);
if (!$_GET[limit]) { output("<tr><td align='center'>`3$row2[houseid]</td><td>$row2[housename] `&(Eigentümer)</td></tr>",true); }
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
                    addnav("Zurück zur Feste","thepath.php");
output("</table>",true);
output("</span>",true);
            }
        }

page_footer();
}

else {

        page_header("Die Feste der Auserwählten");

        output("`b`c`2Die Feste der Auserwählten`0`c`b");
        output ("`tDu entdeckst einen verschlungenen Pfad, der tief in den dunklen Wald führt. Je mehr Schritte du diesem Pfad folgst, umso mulmiger wird dir zumute. Doch plötzlich beginnen deine 5 Male wie wild zu jucken und du glaubst eine leise Stimme zu hören, die dich lockend noch tiefer in den Wald bittet. ");
        output("Entgegen allen Warnungen deines Verstandes folgst du der Stimme und gelangst nach einiger Zeit zu einem kubusförmigen, unscheinbaren Gebäude, das unter dem dichten Blätterdach des Waldes kaum sichtbar ist. Du betrittst die kleine Festung in freudiger Erwartung.`n Glückwunsch! Du hast die Feste der Auserwählten erreicht. Hier bist du unter Deinesgleichen und kannst von deinen ruhmreichen Taten erzählen.");

addnav("Zum goldenen Gobelin","thepath.php?op=charta");
addnav("Der Blutaltar","thepath.php?op=bg");
addnav("Die Halle der Statuen","chosenfeats.php?op=list");
addnav("Der Schlüsselmeister","thepath.php?op=key");
addnav("Dodos Kammer","chosenfeats.php?op=dodo");
addnav("Zum Koboldspitzel","chosenfeats.php?op=imp");
addnav("Zur Wetterhexe","chosenfeats.php?op=hag");
output("`n`n");

        viewcommentary("chosen","Verkünden:",30,"verkündet");

addnav("Zurück zum Wald","forest.php");
        
    } } else{

page_header("Der Wald");
output ("`&Du entdeckst einen verschlungenen Pfad, der tief in den dunklen Wald führt. Je mehr Schritte du diesem Pfad folgst, umso mulmiger wird dir zumute. Irgendwann hälst du es nicht mehr aus und kehrst um. Du bist einfach noch nicht bereit, weißt aber, dass du es eines Tages sein wirst!");
addnav("Zurück","forest.php");
page_footer();
        
    }
page_footer();
?>
