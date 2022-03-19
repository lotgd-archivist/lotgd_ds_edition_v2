<?php
// Bellerophontes' Turm
//
// Bellerophontes' Turm birgt viele �berraschungen.
// Wohl dem, der es schafft, ihn zu erreichen!
// Wohl dem ... ?
//
// Erdacht und umgesetzt von Oliver Wellinghoff alias Harasim dalYkbar Drassim.
// E-Mail: wellinghoff@gmx.de
// Erstmals erschienen auf: http://www.green-dragon.info
//
//  - 25.06.2004 -
//  - Version vom 08.07.2004 -
//  - Mod by talion: Kommentare nur noch manchmal.

if (!isset($session)) exit();
$session[user][specialinc] = "bellerophontes.php";
switch($HTTP_GET_VARS[op]){

case "":
       output("`@Vor Dir liegt ein langer, gerader Waldweg, �ber dem die B�ume zu dicht wachsen, als dass man reiten k�nnte. Es ist schon seit langem nichts Aufregendes mehr passiert - da erblickst Du, als Du eine Kreuzung erreichst, pl�tzlich etwas am Ende des ausgetrampelten Pfades: einen Turm im dunstigen Zwielicht des Waldes.`n`n");
    output("Was wirst Du tun?`n`n <a href='forest.php?op=weiter'>Weitergehen und versuchen, den Turm zu finden,</a>`n oder <a href='forest.php?op=abbiegen1'>hier abbiegen und den Weg verlassen.</a>`n", true);
    addnav("","forest.php?op=weiter");
    addnav("","forest.php?op=abbiegen1");
    addnav("Weitergehen.","forest.php?op=weiter");
    addnav("Abbiegen.","forest.php?op=abbiegen1");

case "abbiegen1":
if ($HTTP_GET_VARS[op]=="abbiegen1"){
    output("`@Du biegst an der Kreuzung ab und verl�sst den Weg.");
    $session[user][specialinc]="";
}

case "weiter":
if ($HTTP_GET_VARS[op]=="weiter"){
      switch(e_rand(1,10)){
            case 1:
            case 2:
            case 3:
            case 4:
            case 5:
            case 6:
            output("`@Du folgst dem Pfad immer tiefer in den Wald hinein, stundenlang, doch der Turm bleibt fest am Horizont. Es ist, als k�nnte man nicht zu ihm gelangen .... Du willst schon aufgeben - als er pl�tzlich mit jedem weiteren Schritt einige Hundert Meter n�her kommt!`n`n");
            $turns2 = e_rand(1,5);
            output("`^Bis hierher zu gelangen hat Dich bereits ".$turns2." Waldk�mpfe gekostet!");
            $session[user][turns]-=$turns2;
    output("`n`n`@<a href='forest.php?op=turm'>Weiter.</a>", true);
    addnav("","forest.php?op=turm");
    addnav("Weiter.","forest.php?op=turm");
            break;
            case 7:
            case 8:
            case 9:
            case 10:
if ($session[user][turns]==0){
               output("`@Du folgst dem Pfad immer tiefer in den Wald, stundenlang. Er scheint nicht enden zu wollen - und immer siehst Du den Turm an seinem Ende. An der n�chsten Weggabelung bleibst Du stehen. `n`nDas war Dein `^letzter`@ Waldkampf und es ist schon dunkel geworden! `n`nDu machst Dich mit dem festen Vorsatz auf den Heimweg, morgen noch einmal zu versuchen, den Turm zu erreichen.");
            $session[user][specialinc]="";
            break;
}else {        output("`@Du folgst dem Pfad immer tiefer in den Wald, stundenlang. Er scheint nicht enden zu wollen - und immer siehst Du den Turm an seinem Ende. An der n�chsten Weggabelung bleibst Du stehen. Weiter nach dem Turm zu suchen wird Dich m�glicherweise alle Deine Waldk�mpfe kosten, aber Du sp�rst, dass Du `bganz dicht dran`b bist ...");
            output("`n`n`@<a href='forest.php?op=weiter2'>Weiter.</a>", true);
            output("`n`n`@<a href='forest.php?op=abbiegen2'>Abbiegen.</a>", true);
            addnav("","forest.php?op=weiter2");
            addnav("","forest.php?op=abbiegen2");
            addnav("Weitergehen.","forest.php?op=weiter2");
            addnav("Abbiegen.","forest.php?op=abbiegen2");
    break;
    $session[user][specialinc]="";
}}}

case "abbiegen2":
if ($HTTP_GET_VARS[op]=="abbiegen2"){
    output("`@Du biegst an der Kreuzung ab und verl�sst den Weg.`n`n");
    output("`^Bis hierher zu gelangen hat Dich jedoch bereits einen Waldkampf gekostet!");
    $session[user][turns]-=1;
    $session[user][specialinc]="";
}

case "weiter2":
if ($HTTP_GET_VARS[op]=="weiter2"){
    output("`@Du gibst nicht auf und folgst dem Pfad noch tiefer in den Wald hinein. Er scheint noch immer nicht enden zu wollen, und es wird immer dunkler. Noch etwa eine Stunde und auch das letzte Licht, das sich seinen Weg durch die B�ume k�mpft, wird erloschen sein - und immer siehst Du den Turm vor Dir, am Ende des Weges.`n`n");
      switch(e_rand(1,15)){
            case 1:
            case 2:
            case 3:
            case 4:
            case 5:
            output("`@Schlie�lich kannst Du Deine Hand kaum noch vor Augen sehen - doch der Turm bleibt am Horizont, als w�rde es dort niemals dunkel werden. Es hilft nichts; schwer entt�uscht nimmst Du die n�chste Abzweigung und gelangst sp�t in der Nacht und v�llig �berm�det zur�ck ins Dorf. Da Du im Dunkeln nichts sehen konntest, hast Du Dir einige derbe Schrammen eingehandelt. Immerhin eine Erfahrung, die man nicht jeden Tag macht.`n`n");
if ($session[user][turns]>=20){
               output("`n`nDu bekommst `^".$session[user][experience]*0.08."`@ Erfahrungspunkte hinzu, verlierst aber alle verbliebenen Waldk�mpfe!");
            $session[user][experience]=$session[user][experience]*1.08;
}else if($session[user][turns]>=13){
              output("`n`nDu bekommst `^".$session[user][experience]*0.07."`@ Erfahrungspunkte hinzu, verlierst aber alle verbliebenen Waldk�mpfe!");
            $session[user][experience]=$session[user][experience]*1.07;
}else if($session[user][turns]>=6){
              output("`n`nDu bekommst `^".$session[user][experience]*0.05."`@ Erfahrungspunkte hinzu, verlierst aber alle verbliebenen Waldk�mpfe!");
            $session[user][experience]=$session[user][experience]*1.05;
}else        output("Du bekommst `^".$session[user][experience]*0.04."`@ Erfahrungspunkte hinzu, verlierst aber `$".$session[user][hitpoints]*0.20."`@ Lebenspunkte und alle verbliebenen Waldk�mpfe!`n");
            $session[user][hitpoints]=$session[user][hitpoints]*0.80;
            $session[user][experience]=$session[user][experience]*1.04;
            $session[user][turns]=0;
            $session[user][specialinc]="";
            break;
            case 6:
            case 7:
            case 8:
            case 9:
            case 10:
            case 11:
            case 12:
            case 13:
            case 14:
            case 15:
            output("`@Schlie�lich kannst Du Deine Hand kaum noch vor Augen erkennen - doch der Turm bleibt am Horizont, als w�rde es dort niemals dunkel werden. Du willst schon an der n�chsten Abbiegung aufgeben - als der Turm beginnt, sich mit jedem weiteren Schritt um einige Hundert Meter zu n�hern! Er liegt trotz der sp�ten Stunde noch immer im Hellen ...`n`n");
            output("`^Die Suche hat Dich alle verbliebenen Waldk�mpfe gekostet!");
            $session[user][turns]=0;
            output("`n`n`@<a href='forest.php?op=turm'>Weiter.</a>", true);
            addnav("","forest.php?op=turm");
            addnav("Weiter.","forest.php?op=turm");
    break;
}}

case "turm":
if ($HTTP_GET_VARS[op]=="turm"){
      output("`@Nun stehst Du vor ihm, einem verwitterten, mit Efeu bewachsenen Wehrturm, der von den �berresten einer einstigen Mauer umgeben ist. Den Eingang bildet eine schwere Eichent�r, die kein Zeichen der Verwitterung aufweist. An einem Pfosten ist ein wei�es Pferd mit Fl�geln angebunden; ein Pegasus, der friedlich grast, und an dessen Sattel ein praller Lederbeutel h�ngt. Schaust Du nach oben, erblickst Du einen Balkon.");
      output("`n`nWas wirst Du tun?");
      output("`n`n<a href='forest.php?op=klopfen'>An die schwere Eichent�r klopfen.</a>",true);
      output("`n`n<a href='forest.php?op=rufen'>Zum Balkon hinaufrufen.</a>",true);
      output("`n`n<a href='forest.php?op=stehlen'>Zu dem Pegasus gehen und den Beutel stehlen.</a>",true);
      output("`n`n<a href='forest.php?op=oeffnen'>Versuchen, die Eichent�r zu �ffnen, um unbemerkt hineinzugelangen.</a>",true);
      output("`n`n<a href='forest.php?op=klettern'>�ber das Efeu zum Balkon hinaufklettern.</a>",true);
      output("`n`n<a href='forest.php?op=gehen'>Dem Ganzen den R�cken kehren - das sieht doch sehr verd�chtig aus ...</a>",true);
      addnav("","forest.php?op=klopfen");
      addnav("","forest.php?op=rufen");
      addnav("","forest.php?op=stehlen");
      addnav("","forest.php?op=oeffnen");
      addnav("","forest.php?op=klettern");
      addnav("","forest.php?op=gehen");
      addnav("Klopfen.","forest.php?op=klopfen");
      addnav("Rufen.","forest.php?op=rufen");
      addnav("Stehlen.","forest.php?op=stehlen");
      addnav("�ffnen.","forest.php?op=oeffnen");
      addnav("Klettern.","forest.php?op=klettern");
      addnav("Gehen.","forest.php?op=gehen");
      addnav("Hinterhof","forest.php?op=hhof");
}

case "klopfen":
if ($HTTP_GET_VARS[op]=="klopfen"){
output("`@Du nimmst all Deinen Mut zusammen und klopfst an die Eichent�r. Die Schritte schwerer Eisenstulpen ert�nen aus dem Innern des Turmes und werden immer lauter ...`n`n");
      switch(e_rand(1,13)){
            case 1:
            case 2:
            case 3:
            output("`@Jemand dr�ckt die T�r von innen auf - doch wer es war, sollst Du nie erfahren. Die Wucht muss jedenfalls gewaltig gewesen sein, sonst h�ttest Du es �berlebt.`n`n");
            output("`$ Du bist tot!`n");
            output("`@Du verlierst `$".$session[user][experience]*0.03."`@ Erfahrungspunkte und all Dein Gold!`n");
            output("Du kannst morgen weiterspielen.");
            $session[user][alive]=false;
            $session[user][hitpoints]=0;
            $session[user][gold]=0;
            $session[user][experience]=$session[user][experience]*0.97;
            addnav("T�gliche News","news.php");
            addnews("`\$`b".$session[user][name]."`b `\$wurde im Wald von einer schweren Eichent�r erschlagen.");
            $session[user][specialinc]="";
            break;
            case 4:
            case 5:
            case 6:
            case 7:
            case 8:
            case 9:
            case 10:
            output("Zumindest in Deiner Einbildung. Als sich Dein Herzschlag wieder beruhigt, musst Du zu Deiner Entt�uschung feststellen, dass wohl niemand zu Hause ist. Du gehst zur�ck in den Wald.");
            $session[user][specialinc]="";
            break;
            case 11:
            output("Die T�r �ffnet sich und Du stehst vor Bellerophontes, dem gro�en Heros und Chim�renbezwinger! Und tats�chlich, auf einem Tisch im Innern siehst Du das Mischwesen liegen; halb L�we, halb Skorpion. Aber Dein Blick wird sofort wieder auf den Helden gezogen, diesen �beraus stattlichen Mann mit langem, dunklem Haar, das von einem Reif gehalten wird. Er tr�gt eine strahlend wei�e Robe, die das Zeichen des Poseidon ziert, und hat den ehrfurchtgebietenden Blick eines Mannes, der den G�ttern entstammt ... `#'Das Orakel von Delphi hatte vorhergesagt, dass jemand kommen w�rde, um mich nach bestandenem Kampf zu ermorden.'");
            output("`@Er mustert Dich - und beginnt dann schallend zu lachen: `#'Aber damit kann es `bDich`b ja wohl kaum gemeint haben, Wurm!'`n`n `@Er nimmt sich etwas Zeit und zeigt Dir, wie man sich im Wald verteidigt, damit Du Deinen Weg zum Dorf sicher zur�cklegen kannst!");
            output("`n`n`^Du erh�ltst 1 Punkt Verteidigung!");
            $session[user][defence]++;
            $session[user][specialinc]="";
            break;
            case 12:
            case 13:
            output("Die T�r �ffnet sich und Du stehst vor Bellerophontes, dem gro�en Heros und Chim�renbezwinger! Und tats�chlich, auf einem Tisch im Innern siehst Du das Mischwesen liegen; halb L�we, halb Skorpion. Aber Dein Blick wird sofort wieder auf den Helden gezogen, diesen �beraus stattlichen Mann mit langem, dunklem Haar, das von einem Reif gehalten wird. Er tr�gt eine strahlend wei�e Robe, die das Zeichen des Poseidon ziert, und hat den ehrfurchtgebietenden Blick eines Mannes, der den G�ttern entstammt ... `#'Das Orakel von Delphi hatte vorhergesagt, dass jemand kommen w�rde, um mich nach bestandenem Kampf zu ermorden.'");
            output("`@Er mustert Dich - und beginnt dann schallend zu lachen: `#'Aber damit kann es `bDich`b ja wohl kaum gemeint haben, Wurm!'`@`n`n Er nimmt sich etwas Zeit und zeigt Dir, wie man gro� und stark wird!");
            output("`n`n`^Du erh�ltst 1 Punkt Angriff!");
            $session[user][attack]++;
            $session[user][specialinc]="";
            break;
}}

case "rufen":
if ($HTTP_GET_VARS[op]=="rufen"){
switch(e_rand(1,10)){
            case 1:
            case 2:
            output("`@Du r�usperst Dich und rufst so laut Du kannst hinauf: `#'Haaaalloooo! Ist da jemand?'");
            output("`@Nichts. Du willst gerade zu einem erneuten Rufen ansetzen ...`n`n ... als jemand zur�ckruft: `#'Nein, hier ist niemand!'");
            output("`@`n`nTja, das nenne ich ein Pech! Du findest es zwar seltsam, dass niemand zu Hause ist, schlie�lich steht ja drau�en der Pegasus, aber Dir bleibt wohl nichts anderes �brig, als diesen Ort zu verlassen.");
            $session[user][specialinc]="";
            break;
            case 3:
            case 4:
            case 5:
            case 6:
            output("`@Du r�usperst Dich und rufst so laut Du kannst hinauf: `#'Haaaalloooo! Ist da jemand?'");
            output("`@Du willst gerade zu einem erneuten Rufen ansetzen ...`n`n ... als jemand zur�ckruft: `#'Herakles, bist Du's? Nimm Dir von dem Gold in dem Beutel, es ist auch das Deine!'");
            output("`n`@Mit etwas dumpferer Stimme rufst Du zur�ck - `#'Danke!'`@ -, greifst in den Beutel auf dem R�cken des Pegasus und begibst Dich so schnell Du kannst zur�ck zum Dorf.`n`n");                        
            $gold = e_rand(400,1000);
            output("`@Du bekommst `^".$session[user][experience]*0.03." `@Erfahrungspunkte hinzu und `^".$gold * $session['user']['level']." `@Goldst�cke!");
            $session[user][experience]=$session[user][experience]*1.03;
            $session['user']['gold'] += $gold * $session['user']['level'];
            $session[user][specialinc]="";
            break;
            case 7:
            case 8:
            case 9:
            output("`@Du r�usperst Dich und rufst so laut Du kannst hinauf: `#'Haaaalloooo! Ist da jemand?'");
            output("`@Nichts. Du willst gerade zu einem erneuten Rufen ansetzen ...`n`n ... als jemand an den Balkon tritt: ein stattlicher Mann mit langem, dunklem Haar, das von einem Reif gehalten wird. Er tr�gt eine strahlend wei�e Robe, die das Zeichen des Poseidon ziert, und hat den ehrfurchtgebietenden Blick eines Mannes, der den G�ttern entstammt ...");
            output("`n`n`#'Sei gegr��t, Sterblicher! Du hast gro�e Entbehrungen auf Dich genommen, um meinen Turm zu erreichen. Daf�r hast Du Dir eine Belohnung redlich verdient! Nimm! Und berichte in aller Welt, dass ich, Bellerophontes, die Chim�re besiegt habe!'`&`n`n `@Er wirft Dir einen Beutel herunter!`n");
            $gems = e_rand(2,5);
            output("`nIn dem Beutel befanden sich `^$gems`@ Edelsteine!");
            $session[user][gems]+=$gems;
            addnav("Zur�ck zum Wald.","forest.php");
			
			if(e_rand(1,4) == 4) {
				addnav("T�gliche News","news.php");
				addnews("`@`b".$session[user][name]."`b `@hielt heute auf dem Dorfplatz einen langen Vortrag �ber `#Bellerophontes'`@ gro�artige Heldentaten!");
				$sql = "INSERT INTO commentary (postdate,section,author,comment) VALUES (now(),'village',".$session[user][acctid].",'/me `\@stellt sich in die N�he des Dorfbrunnens, r�uspert sich und h�lt einen langen Vortrag �ber die Heldentaten eines gewissen `#Bellerophontes`@!')";
				db_query($sql) or die(db_error(LINK));
			}
			
            $session[user][specialinc]="";
            break;
            case 10:
            output("`@Du r�usperst Dich und rufst so laut Du kannst hinauf: `#'Haaaalloooo! Ist da jemand?'");
            output("`@Nichts. Du willst gerade zu einem erneuten Rufen ansetzen ...`n`n ... als jemand an den Balkon tritt: ein stattlicher Mann mit langem, dunklem Haar, das von einem Reif gehalten wird. Er tr�gt eine strahlend wei�e Robe, die das Zeichen des Poseidon ziert, und hat den ehrfurchtgebietenden Blick eines Mannes, der den G�ttern entstammt ...");
            output("`#Ich habe viel von Deinen Heldentaten geh�rt, ".$session['user']['name']."! Hier, dies soll Dir auf Deinen Drachenjagden behilflich sein! Nach meinem Sieg �ber die Chim�re brauche ich es nicht mehr.'`@`n`n Er �berreicht Dir sein Amulett des Lebens!");
            output("`n`n`@Du erh�ltst `^5`@ permanente Lebenspunkte!");
            $session[user][maxhitpoints]+=5;
            $session[user][hitpoints]+=5;
            $session[user][specialinc]="";
            break;
}}

case "stehlen":
if ($HTTP_GET_VARS[op]=="stehlen"){
      switch(e_rand(1,10)){
            case 1:
            case 2:
            case 3:
            output("`@Ein wahrhaft edles Tier ... wei� wie Milch in der Sonne ... umgeben von einem blendenden Schimmer ...");
            output("`@Aber jetzt bleibt keine Zeit f�r Sentimentalit�ten! Du greifst nach dem Beutel und ... `n`n ... wirst von den Hufen des kr�ftigen Tiers gegen die Mauerreste geschleudert. Erschrocken, aber froh um Dein Leben rappelst Du Dich auf und rennst davon.");
            output("`n`n`@Du bekommst `^".$session[user][experience]*0.04."`@ Erfahrungspunkte hinzu, verlierst aber fast alle Deine Lebenspunkte!`n");
            $session[user][hitpoints]=1;
            $session[user][experience]=$session[user][experience]*1.04;
            $session[user][specialinc]="";
            break;
            case 4:
            case 5:
            output("`@Ein wahrhaft edles Tier ... wei� wie Milch in der Sonne ... umgeben von einem blendenden Schimmer ...");
            output("`@Aber jetzt bleibt keine Zeit f�r Sentimentalit�ten! Du greifst nach dem Beutel und ... `n`n ... wirst von seinem Gewicht zu Boden gerissen. Er ist voller Gold, wer h�tte das gedacht? Und je mehr du herausnimmst, desto schwerer scheint er zu werden! Gierig holst Du immer mehr heraus, und mehr, und mehr ... das Gold sprudelt nur so hervor - und hat Dich bald begraben.");    
            output("`$`n`nDu bist tot!");
            output("`n`n`@Du verlierst `$".$session[user][experience]*0.05."`@ Erfahrungspunkte und all Dein Gold!`n");
            output("`nDu kannst morgen weiterspielen.");
            $session[user][alive]=false;
            $session[user][hitpoints]=0;
            $session[user][gold]=0;
            $session[user][experience]=$session[user][experience]*0.95;
            addnav("T�gliche News","news.php");
            addnews("`\$`b".$session[user][name]."`b `\$wurde in ".($session[user][sex]?"ihrer":"seiner")." Gier unter einem riesigen Haufen griechischer Goldm�nzen begraben.");
            $session[user][specialinc]="";
            break;
            case 6:
            case 7:
            case 8:
            output("`@Ein wahrhaft edles Tier ... wei� wie Milch in der Sonne ... umgeben von einem blendenden Schimmer ...");
            output("`@Aber jetzt bleibt keine Zeit f�r Sentimentalit�ten! Du greifst nach dem Beutel und ... `n`n ... wirst von seinem Gewicht zu Boden gerissen. Er ist voller Gold, wer h�tte das gedacht? Und je mehr du herausnimmst, desto schwerer scheint er zu werden! Du nimmst soviel Gold mit, wie Du tragen kannst und verschwindest von diesem seltsamen Ort. Schade, dass man den Beutel nicht mitnehmen kann ...");
            $foundgold = e_rand(1000,4000) * $session['user']['level'];
            output("`n`n`@Du erh�ltst `^".$session[user][experience]*0.03."`@ Erfahrungspunkte und erbeutest `^".$foundgold." `@Goldst�cke!`n");
            $session['user']['gold'] += $foundgold;
            $session[user][experience]=$session[user][experience]*1.03;
            addnav("Zur�ck zum Wald.","forest.php");
            addnav("T�gliche News","news.php");
            addnews("`b`@".$session[user][name]."`b `@gelang es, dem griechischen Heros `#Bellerophontes`^ ".$foundgold."`@ Goldm�nzen zu stehlen!");
            $session[user][specialinc]="";
            break;
            case 9:
            case 10:
            output("`@Ein wahrhaft edles Tier ... wei� wie Milch in der Sonne ... umgeben von einem blendenden Schimmer ...");
            output("`@Aber jetzt bleibt keine Zeit f�r Sentimentalit�ten! Du greifst nach dem Beutel und ... `n`n ... h�ltst kurz bevor Du ihn ber�hren kannst inne. Der Turm, der Pegasus, der Beutel ... das alles kommt Dir doch sehr, sehr merkw�rdig vor. Du nimmst dieses Ereignis als wertvolle Erfahrung, von der Du noch Deinen Enkeln wirst erz�hlen k�nnen, und gehst Deines Weges.");
            output("`n`n`@Du erh�ltst `^".$session[user][experience]*0.35."`@ Erfahrungspunkte!`n");
            $session[user][experience]=$session[user][experience]*1.35;
            addnav("Zur�ck zum Wald.","forest.php");
			if(e_rand(1,4) == 1) {
				addnav("T�gliche News","news.php");
				addnews("`@`b".$session[user][name]."`b `@hat ein wundervolles M�rchen �ber einen seltsamen Turm im Wald geschrieben - und `balle`b Dorfbewohner schw�rmen davon!");
				$sql = "INSERT INTO commentary (postdate,section,author,comment) VALUES (now(),'village',".$session[user][acctid].",'/me `\@freut sich, als ".($session[user][sex]?"sie":"er")." einige Dorfbewohner �ber das M�rchen sprechen h�rt, das ".($session[user][sex]?"sie":"er")." geschrieben hat!')";
				db_query($sql) or die(db_error(LINK));
			}
            $session[user][specialinc]="";
            break;            
}}

case "oeffnen":
if ($HTTP_GET_VARS[op]=="oeffnen"){
      switch(e_rand(1,10)){
            case 1:
            case 2:
            output("`@Zu Deiner Freude bemerkst Du, dass die T�r unverschlossen ist! Vorsichtig versuchst Du sie aufzuschieben ... als sie pl�tzlich ... aus ... den ... Angeln ...`n`n `#'Neeeeeeeiiiiiiin ...!'");
            output("`$`n`nDu bist tot!");
            output("`n`@Du verlierst `$".$session[user][experience]*0.03."`@ Erfahrungspunkte und all Dein Gold!`n");
            output("`@Du kannst morgen weiterspielen.");
            $session[user][alive]=false;
            $session[user][hitpoints]=0;
            $session[user][gold]=0;
            $session[user][experience]=$session[user][experience]*0.97;
            addnav("T�gliche News","news.php");
            addnews("`\$`b".$session[user][name]."`b `\$wurde im Wald von einer schweren Eichent�r erschlagen.");
            $session[user][specialinc]="";
            break;
            case 3:
            case 4:
            case 5:
            case 6:
            case 7:
            case 8:
            case 9:
            case 10:
            output("`@Zu Deiner Freude bemerkst Du, dass die T�r unverschlossen ist! Vorsichtig schiebst Du sie auf ... und wirfst einen ersten Blick hinein. Du siehst einen gem�tlichen Vorraum, von dem aus eine Wendeltreppe nach oben f�hrt. Es gibt einen Holztisch, der sich unter der Last des schwerverletzten K�rper eines seltsamen Wesens biegt. Es ist halb L�we, halb Skorpion ... eine Chim�re! `n`nDas ist aber interessant ... Du gehst hinein, um Dir das Mischwesen genauer anzusehen.");
            addnav("Weiter.","forest.php?op=drinnen");
            break;
}}

case "drinnen":
if ($HTTP_GET_VARS[op]=="drinnen"){
      switch(e_rand(1,10)){             
            case 1:
            case 2:
            case 3:
            case 4:
            case 5:
            output("`@Das Wesen ist tot. Der Wunde nach muss es mit einem einzigen Schwertstreich erlegt worden sein. Wenn da nur nicht die Verbrennungen w�ren ... Als Du pl�tzlich die schnellen Schritte schwerer Eisenstulpen auf der Treppe vernimmst, greifst Du panisch nach dem ersten Gegenstand, den Du zu fassen bekommst - ganz ohne Beute willst Du diese Gefahr nicht auf Dich genommen haben. Es ist ein bronzenes Amulett ...");
            output("`n`n`@Du hast dem griechischen Heros Bellerophontes das Amulett des Lebens gestohlen!");
            $session[user][maxhitpoints]+=5;
            $session[user][hitpoints]+=5;
            output("`n`n`@Du erh�ltst `^".$session[user][experience]*0.05."`@ Erfahrungspunkte!");
            output("`n`n`@Du erh�ltst `^5`@ permanente Lebenspunkte!");
            $session[user][experience]=$session[user][experience]*1.05;
            $session[user][specialinc]="";
            break;
            case 6:
            case 7:
            output("`@Das Wesen ist tot. Der Wunde nach muss es mit einem einzigen Schwertstreich erlegt worden sein. Wenn da nur nicht die Verbrennungen w�ren ...");
            output("`@Als Du pl�tzlich die schnellen Schritte schwerer Eisenstulpen auf der Treppe vernimmst, greifst Du panisch nach dem ersten Gegenstand, den Du zu fassen bekommst - ganz ohne Beute willst Du diese Gefahr nicht auf Dich genommen haben. Es ist ein bronzenes Amulett - das Du w�nschtest, nun lieber nicht in der Hand zu halten. Vor Dir steht der griechische Heros Bellerophontes, Reiter des Pegasus und Bezwinger der Chim�ren!");
            output("`#'Wer bist Du, Wurm, dass Du es wagst, mich zu bestehlen?!' `@`n`n Er erweist sich als wahrer Meister der Rhetorik und streckt Dich kurzerhand mit seinem Flammenschwert nieder.");
            output("`$`n`nDu bist tot!");
            output("`n`@Du verlierst `$".$session[user][experience]*0.07."`@ Erfahrungspunkte und all Dein Gold!");
            output("`n`@Du kannst morgen weiterspielen.");
            $session[user][alive]=false;
            $session[user][hitpoints]=0;
            $session[user][gold]=0;
            $session[user][experience]=$session[user][experience]*0.93;
            addnav("T�gliche News","news.php");
            addnews("`\$Der ebenso gemeine wie unf�hige Dieb `b".$session[user][name]."`b `\$wurde von `#Bellerophontes`\$ mit einem Flammenschwert in der Mitte zerteilt.");
            $session[user][specialinc]="";
            break;
            case 8:
            case 9:
            case 10:
            output("`@Der Wunde nach muss das Wesen mit einem einzigen Schwertstreich erlegt worden sein. Wenn da nur nicht die Verbrennungen w�ren ... Na, Hauptsache es ist tot. Als Du pl�tzlich die schnellen Schritte schwerer Eisenstulpen auf der Treppe vernimmst, greifst Du panisch nach dem ersten Gegenstand, den Du zu fassen bekommst - ganz ohne Beute willst Du diese Gefahr nicht auf Dich genommen haben. Es ist ein bronzenes Amulett - das Dir aus der Hand rutscht, als Du Dich umdrehst. Vor Dir steht der griechische Heros Bellerophontes, Reiter des Pegasus und Bezwinger der Chim�ren! Er rei�t sein flammendes Schwert nach oben, um zum Schlag auszuholen. Jetzt ist es aus!");
            output("`#'Runter mit Dir, Du Wurm!'`@ Reflexartig tust Du, wie Dir gehei�en und sp�rst die Hitze des Schwertes an Deiner Wange entlangsausen. Wi-der-lich-es, gr�nes Chim�renblut bespritzt Dich �ber und �ber. Dankbar schaust Du auf, Deinem Retter ins Gesicht.`n`n `#'Das w�re beinahe Dein Tod gewesen, Du sch�biger Dieb. Aber diesmal sei Dir der Schrecken Lehre genug!' `@Bellerophontes ist gn�dig und jagt Dich mit Fu�tritten nach drau�en.");
            output("`n`n`@Du erh�ltst `^".$session[user][experience]*0.08."`@ Erfahrungspunkte!");
            output("`@`n`nDu verlierst `$2`@ Charmepunkte!");
            $session[user][charm]-=2;
            output("`n`n`@Auf der Flucht hast Du die H�lfte Deines Goldes verloren!`n");
            $session[user][experience]=$session[user][experience]*1.08;
            $session[user][gold]*0.50;
            $session[user][specialinc]="";
            break;            
}}

case "klettern":
if ($HTTP_GET_VARS[op]=="klettern"){
      switch(e_rand(1,10)){         
            case 1:
            case 2:
            output("`@Du greifst nach dem Efeu und ziehst einige Male daran. Alles in Ordnung, es scheint zu halten. Vorsichtig beginnst Du hinaufzuklettern ...");
            output("`@Du hast gerade die H�lfte des Weges bis zum Balkon erklommen, als Du pl�tzlich mit einem Fu� h�ngen bleibst. Du sch�ttelst ihn, um ihn freizubekommen, doch vergebens - die Pflanze scheint Dich bei sich behalten zu wollen! In Panik verfallen, wirst Du immer hektischer, aber alle M�he wird bestraft: schon bald kannst Du Dich �berhaupt nicht mehr bewegen. Die Pflanze h�lt Dich f�r die Ewigkeit gefangen.");        
            output("`$`n`nDu bist tot!");
            output("`@`n`nDu verlierst `$".$session[user][experience]*0.03."`@ Erfahrungspunkte und all Dein Gold!");
            output("`@`n`nDu kannst morgen weiterspielen.");
            $session[user][alive]=false;
            $session[user][hitpoints]=0;
            $session[user][gold]=0;
            $session[user][experience]=$session[user][experience]*0.97;
            addnav("T�gliche News","news.php");
            addnews("`\$`b".$session[user][name]."`b `\$verhedderte sich im Efeu von `#Bellerophontes'`\$ Turm und ist dort verhungert.");
            $session[user][specialinc]="";
            break;
            case 4:
            case 5:
            case 6:
            case 7:
            case 8:
            output("`@Du greifst nach dem Efeu und ziehst einige Male daran. Alles in Ordnung, es scheint zu halten. Vorsichtig beginnst Du hinaufzuklettern ...");
            output("`@Das ist aber einfach! Ohne Probleme erklimmst Du das Efeu bis zum Balkon. Mit einem letzten, kraftvollen Zug hievst Du Deinen edlen K�rper �ber die Br�stung und erblickst: Bellerophontes, den griechischen Heros!");
            output("`@Er tritt Dir mit gemessenen Schritten entgegen, w�hrend Du nichts empfindest als Bewunderung f�r seine gro�artige Erscheinung: langes, dunkles Haar, das von einem Reif gehalten wird; eine strahlend wei�e Robe, die das Zeichen des Poseidon ziert; der ehrfurchtgebietende Blick eines Mannes, der den G�ttern entstammt ...");
            output("`@Dein Bewusstsein schwindet und Du hast einen Traum, wie keinen je zuvor. Ein gro�es Mischwesen aus L�we und Skorpion kommt darin vor ... `n`nAls Du wieder erwachst, liegst Du irgendwo im Wald und schwelgst noch immer - mit genauer Erinnerung an Bellerophontes' �sthetische Kampftaktik!");
            output("`n`n`@Da Du von nun an anmutiger k�mpfen wirst, erh�ltst Du `^2`@ Charmepunkte!");
            $session[user][charm]+=2;
            output("`n`n`@Du erh�ltst `^1`@ Punkt Angriff!");
            $session[user][attack]++;
            $session[user][specialinc]="";
            break;
            case 3:
            case 9:
            case 10:
            output("`@Du greifst nach dem Efeu und ziehst einige Male daran. Alles in Ordnung, es scheint zu halten. Vorsichtig beginnst Du hinaufzuklettern ...");
            output("`@Das ist aber einfach! Ohne Probleme erklimmst Du das Efeu bis zum Balkon. Mit einem letzten, kraftvollen Zug hievst Du Deinen edlen K�rper �ber die Br�stung und erblickst: Bellerophontes, den griechischen Heros!");
            output("`@Er tritt Dir mit gemessenen Schritten entgegen, w�hrend Du nichts empfindest als Bewunderung f�r seine gro�artige Erscheinung: langes, dunkles Haar, das von einem Reif gehalten wird; eine strahlend wei�e Robe, die das Zeichen des Poseidon ziert; der ehrfurchtgebietende Blick eines Mannes, der den G�ttern entstammt ...");
            output("`@Kam erst der Schlag und dann der Flug? Oder war es umgekehrt?");
            output("`$`n`nDu bist tot!");
            output("`n`n`@Du verlierst `$".$session[user][experience]*0.07."`@ Erfahrungspunkte und w�hrend des Fluges all Dein Gold!`n");
            output("`n`@Du kannst morgen weiterspielen.");
            $session[user][alive]=false;
            $session[user][hitpoints]=0;
            $session[user][gold]=0;
            $session[user][experience]=$session[user][experience]*0.93;

            addnav("T�gliche News","news.php");
            addnews("`\$Es wurde beobachtet, wie `b".$session[user][name]."`b`\$ aus heiterem Himmel herab auf den Dorfplatz fiel und beim Aufprall zerplatzte.");
            
            $session[user][specialinc]="";
            break;
}}

case "gehen":
if ($HTTP_GET_VARS[op]=="gehen"){
               output("`@Du verl�sst diesen seltsamen Ort und kehrst in den Wald zur�ck. Eine vern�nftige Entscheidung! Aber Dein Entdeckerherz fragt sich, ob `bVernunft`b f�r einen Abenteurer die beste aller Eigenschaften ist ...");
            $session[user][specialinc]="";
}
case "hhof":
if ($HTTP_GET_VARS[op]=="hhof"){
output("Du betrittst den Hinterhof des Turmes. Hier reden einige Krieger �ber den Turm`n");
viewcommentary("hhof","Sprechen",25);
addnav("Zur�ck zum Turm","forest.php?op=turm");
}
}
?>