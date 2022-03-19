<?php

//27122004

//Pflanzenzucht
//Idee von Fichte, Texte von Kisa, Zusammengeschuster von Hecki )
//Version: 1.1
//Erstmals eschienen auf http://www.cirlce-of-prophets.de/logd
//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//Modifiziert von anpera: Nutzt items table
//
/**** EINBAUANLEITUNG (nur für LoGD 0.9.7 ext GER Release Nr. 3) ***

* In gardens.php finde:
addnav("Geschenkeladen","newgiftshop.php");

* Füge danach ein:

addnav("Blumenbeet","flowers.php");

* In newday.php finde:
$sql="SELECT * FROM items WHERE (class='Fluch' OR class='Geschenk' OR class='Zauber') AND owner=".$session[user][acctid]." ORDER BY id";

* und ersetze es durch:

$sql="SELECT * FROM items WHERE (class='Fluch' OR class='Geschenk' OR class='Zauber' OR class='Beet') AND owner=".$session[user][acctid]." ORDER BY id";

* Finde weiter:
if (strlen($row[buff])>8){

*Füge DAVOR ein:

if ($row['class']=="Beet" && $row['value1']>0) db_query("UPDATE items SET value1=0 WHERE class='Beet' and owner=".$session['user']['acctid']);

*Datei flowers.php in den Logd Ordern hochladen
*/
// Viel Spass ihr Hobbygärtner!

require_once "common.php";

$beet = item_get(' owner='.$session['user']['acctid'].' AND tpl_id="beet" ',false);

if($beet['id']) {$beet['bit']=1;}

page_header("Blumenbeet");

if ($HTTP_GET_VARS[op] == ""){
    output("`c`bPflanzenzucht`c`b");
    output("`n`n");
    if ($beet['bit']==0){
        output("`@ Hier kannst du dir ein Blumenbeet anlegen. Wenn du es täglich pflegst wird schon bald die erste Knospe zu einer wunderschönen Blüte werden.");
        output("`@ Sei sorgsam und liebevoll, dann wird dich deine Pflanze sicher belohnen! Denn der Samen enthält magische Zutaten!");
        output("`n`n");
        output("`@ Ein Beet kostet einmalig 4000 Gold und 10 Edelsteine!`n");
        output("`@ Auf dem Beet ist Platz für eine Blume, aber diese entwickelt unendlich viele Knospen und in jeder ihrer Knospen wartet eine kleine Überraschung auf dich!`n");
        addnav("Ein Beet anlegen","flowers.php?op=anlegen");
        addnav("Zurück zum Garten","gardens.php");
    }else{
        output("`@Voller Vorfreude betrittst du dein Beet. Du bist gespannt ob heute vielleicht etwas aus einer der Knospen spriest.`n`n");
        output("Du solltest etwas Zeit und Gold in die Aufzucht deiner Pflanze investieren, schliesslich braucht eine Pflanze, Liebe, Wasser und Dünger damit sie gedeiht!`n`n");
        if ($beet['value1']>0){
            output("`n`nDu hast dich heute schon um deine Pflanze gekümmert und siehst, dass es ihr gut geht.");
        }
        addnav("Um deine Pflanze kümmern (`^100`0 Gold)","flowers.php?op=kuemmern");
        addnav("Zurück zum Garten","gardens.php");
    }
}

if ($HTTP_GET_VARS[op] == "anlegen"){
    if ($session['user']['gold']>3999 && $session['user']['gems']>9){
        $session['user']['gold'] -= 4000;
        $session['user']['gems'] -= 10;
		
		item_add($session['user']['acctid'],'beet');

        output("`n`n`2Du hast jetzt ein schönes Blumenbeet, und kannst mit deiner Aufzucht beginnen!`n");
        addnav("Zurück zum Garten","gardens.php");
        addnav("Zu deinem Beet","flowers.php");
    }else{
        output("`n`n`2Leider hast du nicht genug Gold und/oder Gems dabei, komm doch später wieder vorbei!`n");
        output("`n`n");
        addnav("Zurück zum Garten","gardens.php");
    }
}

if ($HTTP_GET_VARS[op] == "kuemmern"){
    if($session['user']['gold']>99 && $beet['value1']==0 && $session['user']['turns']>0){
        $session['user']['turns'] --;
        $session['user']['gold'] -=100;
        $beet['value2'] ++;
        $beet['value1'] = 1;
        output("`@Du steckst viel Liebe und Energie in deine Arbeit, und hoffst das dich deine Pflanze in naher Zukunft für deine aufopferungsvollen Bemühungen belohnen wird!`n`n");
        addnav("Zurück zum Garten","gardens.php");
        if ($beet['value2']==10){
            $up = e_rand(1,5);
            $beet['value2']=0;
            switch ($up){
                case 1:
                output("`qVor deinen Augen öffnet sich plötzlich eine der Knospen und eine wunderschöne,");
                output("`qlecker riechende Frucht erblickt das Licht der Welt und danach die Dunkelheit deines Rachens.`n");
                output("`@ Diese Frucht bringt dir 1 permanenten Lebenspunkt!");
                $session['user']['maxhitpoints']++;
                break;
                case 2:
                output("`qAls du deine Blume hoffnungsvoll anschaust scheint sie sich doch tatsächlich zu bewegen.");
                output("`qJa, es ist wahr, die Blüte öffnet sich ganz langsam und als sie vollkommen aufgeblüht ist bist du dir ganz sicher, dass es die allerschönste Blume ist, die du je in deinem Leben gesehen hast.");
                output("`qVor lauter Begeisterung kannst du garnicht reagieren als dein Nachbar auf dich zugerannt kommt,");
                output("`qdir 500 Gold in die Hand drückt und mit deiner wunderschönen Blume hinter der nächsten Ecke verschwindet. Du stehst da mit offenem Mund und fragst dich ob du je wieder eine solch wundervolle Blume züchten kannst!!!");
                $session['user']['gold']+=500;
                break;
                case 3:
                output("`qVor deinen Augen öffnet sich plötzlich eine der Knospen und eine wunderschöne,");
                output("`qlecker riechende Frucht erblickt das Licht der Welt und danach die Dunkelheit deines Rachens.``n");
                output("`@ Diese Frucht bringt dir 5 weitere Waldkämpfe!");
                $session['user']['turns'] += 5;
                break;
                case 4:
                output("`5Veträumt schaust du dein Blümchen an und hoffst das du dich bald an ihrer wunderschönen Blüte erfreuen kannst.`n`n");
                output("`5Plötzlich reckt sich das kleine Blümchen und innerhalb von Sekunden erblüht eine ihrer Knospen in den schönsten Regenbogenfarben.`n");
                output("`5Sie scheint richtig zu glänzen, nur für dich. Du hältst sie an deine Nase um ihren lieblichen Duft in dir aufzunehmen und je näher du sie richtung Nase hältst desto heller leuchtet sie!`n`n");
                output("`5Heller, heller und immer heller strahlt sie dich an, du bist von Ihrer Schönheit wahrlich geblendet");
                output("`5und entdeckst erst als du die Blume ganz an deiner Nase hast, dass ihre Blüte mit Edelsteinen verziert ist.`n`n");
                output("`5Du steckst die 2 Edelsteine sorgsam ein und beschließt dich noch intensiver um dein kleines Pflänzchen zu kümmern - wer weiß was die nächste Blüte für wundersame Kräfte in sich verbirgt - ");
                $session['user']['gems']+=2;
                break;
                case 5:
                output("`qGespannt wartest du, wann deine Mühen endlich belohnt werden und tatsächlich, eine der größten Knospen an deiner Blume reckt und streckt sich und erblüht zu einer wahren Pracht.");
                output("`qDu bist so stolz wie noch nie zuvor auf dich selbst. Jetzt weißt du was einen richtigen Gärtner ausmacht.`n`n");
                output("`@Dieses Wissen lässt deine Erfahrung um 2% ansteigen!");
                $session['user']['experience']*=1.02;
                break;
            }
        }
    }else if ($session['user']['gold']<100){
        output("Du hast zuwenig Gold dabei.");
        addnav("Zurück zum Garten","gardens.php");
    }else{
        output("Du kannst dir heute keine Zeit mehr für deine Pflanze nehmen.");
        addnav("Zurück zum Garten","gardens.php");
    }
	
	item_set(' id='.$beet['id'], $beet);
	
}

page_footer();
?>