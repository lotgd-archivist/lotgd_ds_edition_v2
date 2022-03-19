<?
// R�ume, die nur von Spielern bestimmter Rassen zug�nglich sind
// Modifiziert : houses.php, common.php
// by Maris (Maraxxus@gmx.de)

require_once "common.php";
checkday();
addcommentary();


function showcasulties($victim){
$victimtotal=$victim."total";

$total = getsetting($victimtotal,0);
$bytrolls = getsetting($victim."byTroll",0);
$byelfs = getsetting($victim."byElf",0);
$byhumans = getsetting($victim."byMensch",0);
$bydwarfs = getsetting($victim."byZwerg",0);
$byliz = getsetting($victim."byEchse",0);
$bydarkelf = getsetting($victim."byDunkelelf",0);
$bywerwolf = getsetting($victim."byWerwolf",0);
$bygoblins = getsetting($victim."byGoblin",0);
$byorcs = getsetting($victim."byOrk",0);
$byvampires = getsetting($victim."byVampir",0);
$byhalfling = getsetting($victim."byHalbling",0);
$bydemons = getsetting($victim."byD�mon",0);
$byschelm = getsetting($victim."bySchelm",0);
$byangel = getsetting($victim."byEngel",0);
$byavatar = getsetting($victim."byAvatar",0);
$byunknown = getsetting($victim."by",0);

output("Auf einem dunklen Stein steht geschrieben:`n");
output("`&Wir beklagen unsere `$".$total." `&ermordeten Schwestern und Br�der.`n");
output("`$".$bytrolls." `&wurden von Trollen niedergestreckt,`n");
output("`$".$byelfs." `&kamen durch Elfen ums Leben,`n");
output("`$".$byhumans." `&starben durch die H�nde von Menschen,`n");
output("`$".$bydwarfs." `&wurden von Zwergen erschlagen,`n");
output("`$".$byliz." `&wurden von Echsen get�tet,`n");
output("`$".$bydarkelf." `&gehen auf die Rechnung von Dunkelelfen,`n");
output("`$".$bywerwolf." `&hatten eine Begegnung mit einem Werwolf,`n");
output("`$".$bygoblins." `&wurden von Goblins niedergemetzelt,`n");
output("`$".$byorcs." `&fielen den Orks zum Opfer,`n");
output("`$".$byvampires." `&wurden von Vampiren erlegt,`n");
output("`$".$byhalfling." `&landeten unter den haarigen F��en von Halblingen ,`n");
output("`$".$bydemons." `&wurden von D�monen zerrissen,`n");
output("`$".$byschelm." `&wurden von Schelmen zu Tode genervt,`n");
output("`$".$byangel." `&fanden ihr Ende durch das flammende Schwert eines Engels und`n");
output("`$".$byavatar." `&fielen dem Richterspruch eines Avatars zum Opfer.`n");
output("`$".$byunknown." `&wurden von Unbekannten get�tet.`n`n");
}

if($_GET['op'] == 'show_list') {
	
	page_header("Die Rassenliste");
	
	output ('`&`c`bEine Liste am Rande dieses Ortes zeigt Dir auf magische Weise alle Bewohner dieses Dorfes mit derselben Rasse:`b`c`n`n');
	
	user_show_list(50,' race='.$_GET['race'],'dragonkills DESC, name ASC');
		
	addnav('Zur�ck','racesspecial.php?race='.$_GET['race']);
	
}

else {

	if ($_GET[race]==RACE_TROLL) {
	page_header("Die Trollfeste");
	output("`tDu betrittst die m�chtige Trollfeste. Endlich, aber auch endlich kannst du dich einmal frei bewegen, ohne die Angst bei jedem Schritt wogegen zu sto�en oder dir beim Durchschreiten einer T�rschwelle den Kopf anzuschlagen.`nHier in der Trollfeste bist du unter Deinesgleichen. Nur Trolle wohin du blickst, kein menschliches Gew�rm oder sonstiges st�rendes Getier.`nHier f�hlst du dich wohl und hier sind auch die meisten deiner Freunde zu finden. Das Ale flie�t in Str�men und es gibt so viel Elf am Spie� wie du magst. Die Stimmung ist herrlich.`n`n`&");

showcasulties($races[$_GET[race]]);

	viewcommentary("trollfeste","Br�llen:",30,"br�llt");
	addnav('Zur Rassenliste','racesspecial.php?op=show_list&race='.$_GET['race']);
	addnav("Den Ort verlassen","forest.php");
	}
	else if ($_GET[race]==RACE_ELF) {
	page_header("Der Elfenhain");
	output ("`tDu folgst deinen feinen Sinnen, in der Gewissheit, dass sie dich zu dem geheimen, verborgenen Ort f�hren m�gen. Und dann erblickst du auch schon die kleinen Baumh�user, die so elegant anmuten, als seien sie nat�rlich in den Baum gewachsten. Ein wohliges Kribbeln durchf�hrt deinen K�rper, als du die ersten Elfen erblickst, die dir freudig aus den H�hen der B�ume entgegen winken. Du bist froh hier zu sein, keine stinkenden Orks, keine lauten Trolle. Du geniesst die Stille und die Gesellschaft deiner Freunde, in der ruhigen Gewissheit, dass es nur Elfen m�glich ist diesen Ort zu finden.`&`n`n");

showcasulties($races[$_GET[race]]);

	viewcommentary("elfenhain","Fl�stern:",30,"fl�stert");
	addnav('Zur Rassenliste','racesspecial.php?op=show_list&race='.$_GET['race']);
	addnav("Den Ort verlassen","forest.php");
	}
	else if ($_GET[race]==RACE_MENSCH) {
	page_header("Der Versammlungsraum");
	output("`tDu gehst schnellen Schrittes durch die Strassen, in der Hoffnung dieses eine Haus wieder zu finden. Hier sehen alle H�user gleich aus, und w��test du nicht wo du hin musst, w�re es dir unm�glich dich hier zurecht zu finden. Dann pl�tzlich stehst du vor einem Gutshaus, von dem du glaubst, dass es das Richtige ist. Du schl�gst den wuchtigen T�rklopfer gegen die beschlagene Eicht�r - zweimal kurz, einmal lang, dreimal kurz. Eine kleine Klappe schiebt sich auf und ein Augenpaar mustert dich. Dann wird die T�r ge�ffnet und du wirst steinerne Stufen in ein gro�es Gew�lbe gef�hrt. Hier haben sich Menschen zusammen gefunden. Sie essen, trinken, am�sieren sich. Du wirst freudig begr��t und man weist dir einen Platz zu. Die Gewissheit, dass nur Menschen Zugang zu dieser Halle haben, erlaubt dir dich einmal zu entspannen und mit deinen Freunden zu feiern.`&`n`n");

showcasulties($races[$_GET[race]]);

	viewcommentary("versammlungsraum","Sagen:",30,"sagt");
	addnav('Zur Rassenliste','racesspecial.php?op=show_list&race='.$_GET['race']);
	addnav("Den Ort verlassen","houses.php");
	}
	else if ($_GET[race]==RACE_ZWERG) {
	page_header("Die Zwergenbinge");
	output ("`tDu eilst durch das Wohnviertel, vorbei an den Wohnh�usern und Lagerhallen, bis du in immer d�nner besiedeltes Gebiet kommst. Allm�hlich wird es immer h�geliger. Du steuerst zielsicher auf einen gro�en Strauch zu und schl�gst ihn auf Seite. Dahinter f�hrt eine steinerne Treppe mindestens 1000 Stufen tief ins Erdreich hinab. Du folgst der Treppe und sie endet vor einer kleinen st�hlernen T�re. Du rufst dein Losungswort und die T�r schwingt auf. Dahinter bietet sich dir ein wahrlich freudiger Anblick : Gem�tliche, kleine R�umchen, nicht so gro� und weit dass man sich drin verlieren k�nnte, wie sie es in der Menschenwelt sind. Wohin dein Auge auch nur blickt siehst du Zwerge, beim Saufen, Gr�hlen und beim Tratsch. Hier bist du richtig und hier kannst du es dir f�r eine Weile richtig gut gehen lassen. Du wei�t dass niemand au�er Zwergen hierhin Zugang hat und setzt dich auf ein gro�es Bierfass, was du zu leeren beabsichtigst.`&`n`n");

showcasulties($races[$_GET[race]]);

	viewcommentary("zwergenbinge","Gr�hlen:",30,"gr�hlt");
	addnav('Zur Rassenliste','racesspecial.php?op=show_list&race='.$_GET['race']);
	addnav("Den Ort verlassen","houses.php");
	}
	else if ($_GET[race]==RACE_ECHSE) {
	page_header("Die Echsens�mpfe");
	output("`tDu begibst dich in die tiefen S�mpfe. Der Boden unter deinen F��en wird immer feuchter und gibt immer mehr nach. Dank den feinen H�uten zwischen deinen Zehen gehst du jedoch nicht unter, sondern kannst dich sicher immer weiter und tiefer in die S�mpfe begeben. Jeder Andere, so bist du dir sicher, w�re schon l�ngst versunken und des Todes. Dann, nach einer Weile des sumpfigen Marsches erblickst du die erste Behausung, die aus dem Sumpfboden ragt. Hier bist du aufgewachsen und hierhin zieht es dich auch immer wieder zur�ck. Du wei�t, dass nur Echsen diesen Ort betreten k�nnen und schaust dich nach deinen Freunden und Bekannten um. Dann beschlie�t du f�r eine Weile hier zu bleiben und es dir hier gut gehen zu lassen. Der s��lich faulige Geruch der S�mpfe, den du im Dorf so sehr vermisst hast, dringt in deine Nase und du hast das sch�ne Gef�hl zuhause zu sein.`&`n`n");

showcasulties($races[$_GET[race]]);

	viewcommentary("echsens�mpfe","Zischen:",30,"zischt");
	addnav('Zur Rassenliste','racesspecial.php?op=show_list&race='.$_GET['race']);
	addnav("Den Ort verlassen","forest.php");
	}
	else if ($_GET[race]==RACE_DUNKELELF) {
	page_header("Der Finsterwald");
	output("`tDu verl�sst die festen Wege und gehst geradewegs in den Wald hinein. Anfangs ist der noch d�nn bewachsen und recht hell. Schnellen Schrittes huschst du durch durch diesen Ort der Lichtkriecher und allm�hlich gelangst du immer tiefer in den Wald. Die B�umen scheinen hier gr��er und �lter, ihr Bl�tterdach dichter. Du gehst weiter. Es wird immer dunkler und deine Augen leuchten violet auf. Du kannst nat�rlich auch bei dieses Dunkelheit hervorragend sehen und wei�t auch genau wo du hin musst. Dann erreichst du dein Ziel, den Ort, an dem du deine Kindheit verbracht hast. Und du bist hier nicht allein, viele andere Dunkelelfen haben sich auch hier eingefunden. Du wei�t dass es nur Dunkelelfen m�glich ist diesen Ort zu betreten und dass du die n�chste Zeit Ruhe vor den unw�rdigen Gesch�pfen des Dorfes haben wirst. Leise Stimmen und h�misches Kichern dringen an dein Ohr vor. Du beschlie�t eine Weile hier zu verweilen und mit Deinesgleichen zu verbringen.`&`n`n");

showcasulties($races[$_GET[race]]);

	viewcommentary("finsterwald","Sinnen:",30,"sinnt");
	addnav('Zur Rassenliste','racesspecial.php?op=show_list&race='.$_GET['race']);
	addnav("Den Ort verlassen","forest.php");
	}
	else if ($_GET[race]==RACE_WERWOLF) {
	page_header("Die Werwolflichtung");
	output ("`tDu folgst den Waldwegen ein kleines St�ck und l�sst dich von deinen Instinkten leiten.`nDer Wald wird immer dichter und die Sicht immer schlechter, doch irgendwie scheinst es dir nichts auszumachen. Du hast deine Witterung aufgenommen und eilst zielstrebig durch das dichte Unterholz.`nDann hast du sie erreicht : Eine kleine Lichtung mitten im tiefen Wald.`nNeugierig blickst du dich um und entdeckst weitere Wesen, die dir �hnlich scheinen.`nDu wei�t, dass sie wohl das selbe Schicksal wie du erleiden m�ssen und obwohl einige von ihnen furchterregend aussehen hast du keine Angst.`nAuch wei�t du, dass dieser Ort ein ganz besonderer Ort ist, zu dem nur Deinesgleichen Zugang hat.`nAlso setzt du dich f�r ein Weilchen auf einen Stein und suchst das Gespr�ch mit den Anderen.`&`n`n");

showcasulties($races[$_GET[race]]);

	viewcommentary("werwolflichtung","Heulen:",30,"heult");
	addnav('Zur Rassenliste','racesspecial.php?op=show_list&race='.$_GET['race']);
	addnav("Den Ort verlassen","forest.php");
	}
	else if ($_GET[race]==RACE_GOBLIN) {
	page_header("Der Goblinbau");
	output("`tDu flitzt durch das Wohnviertel, huschst an H�userecken vorbei und verl�sst das Wohnviertel, eilst zum h�geligen Gebiet abseits. Eine ganze Weile bist du unterwegs, bis du die gut versteckten H�gelh�user entdeckst, die deine Sippe errichtet hat. Du schl�pfst durch einen der schmalen kleinen Eing�nge und wei�t, dass nicht einmal ein Menschenkind diese schmalen Pforte passieren kann und dass sich hier nur Goblins aufhalten. Du begibst dich tief in das halbunterirdische Gew�lbe und triffst viel andere Goblins. Ein wohl bekanntes Geschreie tritt an dein Ohr und du f�hlst dich daheim. Hier bist du mit deinen Freunden allein und niemand wird eure Zusammenkunft st�ren k�nnen. Du beschlie�t eine Weile hier zu bleiben bevor du dich wieder in die gef�hrliche Welt der Menschen begibst.`&`n`n");

showcasulties($races[$_GET[race]]);

	viewcommentary("goblinbau","Kreischen:",30,"kreischt");
	addnav('Zur Rassenliste','racesspecial.php?op=show_list&race='.$_GET['race']);
	addnav("Den Ort verlassen","houses.php");
	}
	else if ($_GET[race]==RACE_ORK) {
	page_header("Die Orkfeste");
	output("`tDu holst die alte Karte hervor und folgst den verschlungenen Waldwegen, dein Ziel stets vor Augen. Obwohl es schwierig ist diesen Ort zu finden, erreichst du mit Hilfe der Karte endlich die Orkfeste. Du stellst dich vor das m�chtige Eingangsportal und st��t einen markersch�tternden Schrei aus. Schon �ffnet sich die T�re und du wirst herein gelassen. Die Atmosph�re im Inneren der Orkfeste gef�llt dir sehr. Viele andere Orks haben sich hier eingefunden und sind mit Speis und Trank, dem Erz�hlen wilder Kriegsgeschichten und Raufereien besch�ftigt. Kaum hast du dich auf einer Holzbank niedergelassen dr�ckt dir ein anderer Ork schon einen riesigen Krug Ale und eine Menschenkeule in die Hand. Hier kannst du es dir nun so richtig gut gehen lassen bevor du wieder zur�ck ins Dorf gehst.`&`n`n");

showcasulties($races[$_GET[race]]);

	viewcommentary("orkfeste","Gr�hlen:",30,"gr�hlen");
	addnav('Zur Rassenliste','racesspecial.php?op=show_list&race='.$_GET['race']);
	addnav("Den Ort verlassen","forest.php");
	}
	else if ($_GET[race]==RACE_VAMPIR) {
	page_header("Das Mausoleum");
	output ("`tDu schleichst durch Wohnviertel und n�herst dich zielsicher dem Friedhof. Vorbei an unz�hligen Gr�bern und Gruften bahnst du dir deinen Weg bis hin zu einem ganz bestimmten Bau. Du legst deine H�nde auf das steinerne T�rportal und es bewegt sich langsam auf Seite um kurz nachdem du das Mausoleum betreten hast, wieder hinter dir zuzuschlagen. Du gehst einen langen Gang entlang und steigst eine Treppe hinab. Deine Schritte hallen dumpf nacht. Dann gelangst du in einen gro�en Raum, in dem sich schon andere Vampire eingefunden haben. Viele von ihnen kennst du bereits. Sie h�ngen an der Decke, ruhen oder unterhalten sich. Ein paar der Vampire haben eine junge vor Schrecken starre Menschenfrau mitgebracht, die sie der Reihe nach durchreichen um von ihr zu trinken. Du wei�t dass sich hier nur Vampire aufhalten, und das verleiht dir ein Gef�hl von Sicherheit. Du setzt dich auf einen Sarg und winks den anderen Vampiren, die du kennst, zu.`&`n`n");

showcasulties($races[$_GET[race]]);

	viewcommentary("mausoleum","Fl�stern:",30,"fl�stert");
	addnav('Zur Rassenliste','racesspecial.php?op=show_list&race='.$_GET['race']);
	addnav("Den Ort verlassen","houses.php");
	}
	else if ($_GET[race]==RACE_HALBLING) {
	page_header("Die H�gelh�user");
	output ("`tDu schlendest durch das Wohnviertel und suchst die Weiten der gr�nen Ebenen weit abseits der dicht besiedelten Strassen. Dort ist es h�gelig und die Natur ist unber�hrt.`nSchliesslich gelangst du an einen gro�en hohlen Baum. Dort gibst du das geheime Klopfzeichen, dass dich deine Eltern einst gelehrt haben und wenig sp�ter f�llt dir eine winzig kleine T�r in einem der H�gel auf. Gerade gro� genug dass du hindurchschl�pfen kannst.`nDu betrittst das kleine H�uschen und entdeckst, dass sich viele andere Halblinge hier versammelt haben, die dich mit einem guten Becher Ale und einer frisch gestopften Pfeife begr��en.`nDu bist dir ganz sicher, dass ihr hier unter euch seid und entschliesst ein wenig mit deinen Artgenossen zu feiern.`&`n`n");

showcasulties($races[$_GET[race]]);

	viewcommentary("huegelhaeuser","Rufen:",30,"ruft");
	addnav('Zur Rassenliste','racesspecial.php?op=show_list&race='.$_GET['race']);
	addnav("Den Ort verlassen","houses.php");
    }
	else if ($_GET[race]==RACE_DAEMON) {
	page_header("Die Schwefelquellen");
	output ("`tDu schreitest durch den tiefen Wald, knickst d�nne B�ume und Str�ucher um und schl�gst eine Schneise durch den dichten Wuchs. Du wei�t genau wo du hin willst, und pl�tzlich vernimmst du auch diesen wohlig stechenden Geruch in deiner Nase. Nur ein paar Schritte sp�ter liegen sie vor dir : die blubbernden hei�en Quellen. Der bei�ende Schwefelegeruch ist so stark, dass kein Mensch und auch kein anderes diesseitiges Wesen hier �berleben kann.`nDu kannst dir ganz sicher sein, dass du hier nicht gest�rt wirst und erblickst auch schon ein paar andere D�monen, die sich ebenfalls hier hin zur�ck gezogen haben`&`n`n");

showcasulties($races[$_GET[race]]);

	viewcommentary("schwefelquelle","Fauchen:",30,"faucht");
	addnav('Zur Rassenliste','racesspecial.php?op=show_list&race='.$_GET['race']);
	addnav("Den Ort verlassen","forest.php");
	}
    else if ($_GET[race]==RACE_SCHELM) {
	page_header("Der Schelmenraum");
	output ("`tDu t�nzelst durch das Wohnviertel auf der Suche nach einem ganz bestimmten Haus.`nDann hast du es gefunden : gro� und seri�s wirkend nimmt es seinen Platz unter den anderen H�usern ein. Du kletterst durch ein Kellerfenster hinein, steigst eine Treppe hinauf und �ffnest eine schwere Eichent�re.`nUnd dann siehst du sie : Andere, die so sind wie du, f�llen einen gro�en Raum. L�rm und Gel�chter schl�gt dir entgegen und du f�hlst dich ein wenig an deine Kindheit bei den Feen erinnert. Du lauschst den Gespr�chen, die sich f�r dich schnell als totaler Unsinn oder Berichte �ber Scherze mit argw�hnischen Menschen und Elfen herausstellen.`nHier f�hlst du dich wohl, denn du wei�t, dass niemand, der kein Schelm auch nur f�r eine Minute in diesem Raum verweilen k�nnte`&`&`n`n");

showcasulties($races[$_GET[race]]);

	viewcommentary("schelmenraum","Scherzen:",30,"scherzt");
	addnav('Zur Rassenliste','racesspecial.php?op=show_list&race='.$_GET['race']);
	addnav("Den Ort verlassen","houses.php");
	}
	else if ($_GET[race]==RACE_ENGEL) {
	page_header("Die Wolkenfestung");
	output ("`tDu schreitest durch den Wald, frei und unbek�mmert. An einer Lichtung holst du dann tief Luft und l�st dich von der beklemmenden H�lle, die dir dein K�rper ist.`nDu schwebst hinauf wie du es gewohnt bist, hoch zu den Wolken. Die Welt unter dir wird immer kleiner und unwirklicher. Als du das Wolkendach durchbrochen hast stellst du fest, dass du nicht allein bist.`nAndere haben sich wie du hierher zur�ckgezogen, um sich von den Strapazen der Sterblichkeit zu erholen. Du wei�t dass euch hier niemand Anderes st�ren kann und verweilst ein wenig bei Deinesgleichen.`n`n");

showcasulties($races[$_GET[race]]);

	viewcommentary("wolkenfestung","Frohlocken:",30,"frohlockt");
	addnav('Zur Rassenliste','racesspecial.php?op=show_list&race='.$_GET['race']);
	addnav("Den Ort verlassen","forest.php");
	}
	else if ($_GET[race]==RACE_AVATAR) {
	page_header("Die Leere");
	output ("`tDu blickst dich kurz um, sicherstellend, dass die niemand beobachtet, und streckst deine H�nde aus um das d�nne Gewebe der Wirklichkeit zu zerreissen.`nDahinter ist nichts, die Leere, und du trittst ein in ein schwarzes Wabern.`nDu verl�sst die Grenzen von Zeit und Raum und kehrst zum Ursprung zur�ck. Langsam sp�rst du wie deine Kr�fte vollends zu dir zur�ck kehren. Du sp�rst die Pres�nz anderer Avatare.`nHier seid ihr nun unter Euch, da dieser Ort Ausserhalb jeglicher Vorstellungskraft anderer Wesen existiert.`n`n");

showcasulties($races[$_GET[race]]);

	viewcommentary("dieleere","Mitteilen:",30,"teilt mit");
	addnav('Zur Rassenliste','racesspecial.php?op=show_list&race='.$_GET['race']);
	addnav("Den Ort verlassen","forest.php");
	}
}

page_footer();
?>
