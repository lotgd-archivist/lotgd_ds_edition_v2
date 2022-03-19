<?
// Räume, die nur von Spielern bestimmter Rassen zugänglich sind
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
$bydemons = getsetting($victim."byDämon",0);
$byschelm = getsetting($victim."bySchelm",0);
$byangel = getsetting($victim."byEngel",0);
$byavatar = getsetting($victim."byAvatar",0);
$byunknown = getsetting($victim."by",0);

output("Auf einem dunklen Stein steht geschrieben:`n");
output("`&Wir beklagen unsere `$".$total." `&ermordeten Schwestern und Brüder.`n");
output("`$".$bytrolls." `&wurden von Trollen niedergestreckt,`n");
output("`$".$byelfs." `&kamen durch Elfen ums Leben,`n");
output("`$".$byhumans." `&starben durch die Hände von Menschen,`n");
output("`$".$bydwarfs." `&wurden von Zwergen erschlagen,`n");
output("`$".$byliz." `&wurden von Echsen getötet,`n");
output("`$".$bydarkelf." `&gehen auf die Rechnung von Dunkelelfen,`n");
output("`$".$bywerwolf." `&hatten eine Begegnung mit einem Werwolf,`n");
output("`$".$bygoblins." `&wurden von Goblins niedergemetzelt,`n");
output("`$".$byorcs." `&fielen den Orks zum Opfer,`n");
output("`$".$byvampires." `&wurden von Vampiren erlegt,`n");
output("`$".$byhalfling." `&landeten unter den haarigen Füßen von Halblingen ,`n");
output("`$".$bydemons." `&wurden von Dämonen zerrissen,`n");
output("`$".$byschelm." `&wurden von Schelmen zu Tode genervt,`n");
output("`$".$byangel." `&fanden ihr Ende durch das flammende Schwert eines Engels und`n");
output("`$".$byavatar." `&fielen dem Richterspruch eines Avatars zum Opfer.`n");
output("`$".$byunknown." `&wurden von Unbekannten getötet.`n`n");
}

if($_GET['op'] == 'show_list') {
	
	page_header("Die Rassenliste");
	
	output ('`&`c`bEine Liste am Rande dieses Ortes zeigt Dir auf magische Weise alle Bewohner dieses Dorfes mit derselben Rasse:`b`c`n`n');
	
	user_show_list(50,' race='.$_GET['race'],'dragonkills DESC, name ASC');
		
	addnav('Zurück','racesspecial.php?race='.$_GET['race']);
	
}

else {

	if ($_GET[race]==RACE_TROLL) {
	page_header("Die Trollfeste");
	output("`tDu betrittst die mächtige Trollfeste. Endlich, aber auch endlich kannst du dich einmal frei bewegen, ohne die Angst bei jedem Schritt wogegen zu stoßen oder dir beim Durchschreiten einer Türschwelle den Kopf anzuschlagen.`nHier in der Trollfeste bist du unter Deinesgleichen. Nur Trolle wohin du blickst, kein menschliches Gewürm oder sonstiges störendes Getier.`nHier fühlst du dich wohl und hier sind auch die meisten deiner Freunde zu finden. Das Ale fließt in Strömen und es gibt so viel Elf am Spieß wie du magst. Die Stimmung ist herrlich.`n`n`&");

showcasulties($races[$_GET[race]]);

	viewcommentary("trollfeste","Brüllen:",30,"brüllt");
	addnav('Zur Rassenliste','racesspecial.php?op=show_list&race='.$_GET['race']);
	addnav("Den Ort verlassen","forest.php");
	}
	else if ($_GET[race]==RACE_ELF) {
	page_header("Der Elfenhain");
	output ("`tDu folgst deinen feinen Sinnen, in der Gewissheit, dass sie dich zu dem geheimen, verborgenen Ort führen mögen. Und dann erblickst du auch schon die kleinen Baumhäuser, die so elegant anmuten, als seien sie natürlich in den Baum gewachsten. Ein wohliges Kribbeln durchfährt deinen Körper, als du die ersten Elfen erblickst, die dir freudig aus den Höhen der Bäume entgegen winken. Du bist froh hier zu sein, keine stinkenden Orks, keine lauten Trolle. Du geniesst die Stille und die Gesellschaft deiner Freunde, in der ruhigen Gewissheit, dass es nur Elfen möglich ist diesen Ort zu finden.`&`n`n");

showcasulties($races[$_GET[race]]);

	viewcommentary("elfenhain","Flüstern:",30,"flüstert");
	addnav('Zur Rassenliste','racesspecial.php?op=show_list&race='.$_GET['race']);
	addnav("Den Ort verlassen","forest.php");
	}
	else if ($_GET[race]==RACE_MENSCH) {
	page_header("Der Versammlungsraum");
	output("`tDu gehst schnellen Schrittes durch die Strassen, in der Hoffnung dieses eine Haus wieder zu finden. Hier sehen alle Häuser gleich aus, und wüßtest du nicht wo du hin musst, wäre es dir unmöglich dich hier zurecht zu finden. Dann plötzlich stehst du vor einem Gutshaus, von dem du glaubst, dass es das Richtige ist. Du schlägst den wuchtigen Türklopfer gegen die beschlagene Eichtür - zweimal kurz, einmal lang, dreimal kurz. Eine kleine Klappe schiebt sich auf und ein Augenpaar mustert dich. Dann wird die Tür geöffnet und du wirst steinerne Stufen in ein großes Gewölbe geführt. Hier haben sich Menschen zusammen gefunden. Sie essen, trinken, amüsieren sich. Du wirst freudig begrüßt und man weist dir einen Platz zu. Die Gewissheit, dass nur Menschen Zugang zu dieser Halle haben, erlaubt dir dich einmal zu entspannen und mit deinen Freunden zu feiern.`&`n`n");

showcasulties($races[$_GET[race]]);

	viewcommentary("versammlungsraum","Sagen:",30,"sagt");
	addnav('Zur Rassenliste','racesspecial.php?op=show_list&race='.$_GET['race']);
	addnav("Den Ort verlassen","houses.php");
	}
	else if ($_GET[race]==RACE_ZWERG) {
	page_header("Die Zwergenbinge");
	output ("`tDu eilst durch das Wohnviertel, vorbei an den Wohnhäusern und Lagerhallen, bis du in immer dünner besiedeltes Gebiet kommst. Allmählich wird es immer hügeliger. Du steuerst zielsicher auf einen großen Strauch zu und schlägst ihn auf Seite. Dahinter führt eine steinerne Treppe mindestens 1000 Stufen tief ins Erdreich hinab. Du folgst der Treppe und sie endet vor einer kleinen stählernen Türe. Du rufst dein Losungswort und die Tür schwingt auf. Dahinter bietet sich dir ein wahrlich freudiger Anblick : Gemütliche, kleine Räumchen, nicht so groß und weit dass man sich drin verlieren könnte, wie sie es in der Menschenwelt sind. Wohin dein Auge auch nur blickt siehst du Zwerge, beim Saufen, Gröhlen und beim Tratsch. Hier bist du richtig und hier kannst du es dir für eine Weile richtig gut gehen lassen. Du weißt dass niemand außer Zwergen hierhin Zugang hat und setzt dich auf ein großes Bierfass, was du zu leeren beabsichtigst.`&`n`n");

showcasulties($races[$_GET[race]]);

	viewcommentary("zwergenbinge","Gröhlen:",30,"gröhlt");
	addnav('Zur Rassenliste','racesspecial.php?op=show_list&race='.$_GET['race']);
	addnav("Den Ort verlassen","houses.php");
	}
	else if ($_GET[race]==RACE_ECHSE) {
	page_header("Die Echsensümpfe");
	output("`tDu begibst dich in die tiefen Sümpfe. Der Boden unter deinen Füßen wird immer feuchter und gibt immer mehr nach. Dank den feinen Häuten zwischen deinen Zehen gehst du jedoch nicht unter, sondern kannst dich sicher immer weiter und tiefer in die Sümpfe begeben. Jeder Andere, so bist du dir sicher, wäre schon längst versunken und des Todes. Dann, nach einer Weile des sumpfigen Marsches erblickst du die erste Behausung, die aus dem Sumpfboden ragt. Hier bist du aufgewachsen und hierhin zieht es dich auch immer wieder zurück. Du weißt, dass nur Echsen diesen Ort betreten können und schaust dich nach deinen Freunden und Bekannten um. Dann beschließt du für eine Weile hier zu bleiben und es dir hier gut gehen zu lassen. Der süßlich faulige Geruch der Sümpfe, den du im Dorf so sehr vermisst hast, dringt in deine Nase und du hast das schöne Gefühl zuhause zu sein.`&`n`n");

showcasulties($races[$_GET[race]]);

	viewcommentary("echsensümpfe","Zischen:",30,"zischt");
	addnav('Zur Rassenliste','racesspecial.php?op=show_list&race='.$_GET['race']);
	addnav("Den Ort verlassen","forest.php");
	}
	else if ($_GET[race]==RACE_DUNKELELF) {
	page_header("Der Finsterwald");
	output("`tDu verlässt die festen Wege und gehst geradewegs in den Wald hinein. Anfangs ist der noch dünn bewachsen und recht hell. Schnellen Schrittes huschst du durch durch diesen Ort der Lichtkriecher und allmählich gelangst du immer tiefer in den Wald. Die Bäumen scheinen hier größer und älter, ihr Blätterdach dichter. Du gehst weiter. Es wird immer dunkler und deine Augen leuchten violet auf. Du kannst natürlich auch bei dieses Dunkelheit hervorragend sehen und weißt auch genau wo du hin musst. Dann erreichst du dein Ziel, den Ort, an dem du deine Kindheit verbracht hast. Und du bist hier nicht allein, viele andere Dunkelelfen haben sich auch hier eingefunden. Du weißt dass es nur Dunkelelfen möglich ist diesen Ort zu betreten und dass du die nächste Zeit Ruhe vor den unwürdigen Geschöpfen des Dorfes haben wirst. Leise Stimmen und hämisches Kichern dringen an dein Ohr vor. Du beschließt eine Weile hier zu verweilen und mit Deinesgleichen zu verbringen.`&`n`n");

showcasulties($races[$_GET[race]]);

	viewcommentary("finsterwald","Sinnen:",30,"sinnt");
	addnav('Zur Rassenliste','racesspecial.php?op=show_list&race='.$_GET['race']);
	addnav("Den Ort verlassen","forest.php");
	}
	else if ($_GET[race]==RACE_WERWOLF) {
	page_header("Die Werwolflichtung");
	output ("`tDu folgst den Waldwegen ein kleines Stück und lässt dich von deinen Instinkten leiten.`nDer Wald wird immer dichter und die Sicht immer schlechter, doch irgendwie scheinst es dir nichts auszumachen. Du hast deine Witterung aufgenommen und eilst zielstrebig durch das dichte Unterholz.`nDann hast du sie erreicht : Eine kleine Lichtung mitten im tiefen Wald.`nNeugierig blickst du dich um und entdeckst weitere Wesen, die dir ähnlich scheinen.`nDu weißt, dass sie wohl das selbe Schicksal wie du erleiden müssen und obwohl einige von ihnen furchterregend aussehen hast du keine Angst.`nAuch weißt du, dass dieser Ort ein ganz besonderer Ort ist, zu dem nur Deinesgleichen Zugang hat.`nAlso setzt du dich für ein Weilchen auf einen Stein und suchst das Gespräch mit den Anderen.`&`n`n");

showcasulties($races[$_GET[race]]);

	viewcommentary("werwolflichtung","Heulen:",30,"heult");
	addnav('Zur Rassenliste','racesspecial.php?op=show_list&race='.$_GET['race']);
	addnav("Den Ort verlassen","forest.php");
	}
	else if ($_GET[race]==RACE_GOBLIN) {
	page_header("Der Goblinbau");
	output("`tDu flitzt durch das Wohnviertel, huschst an Häuserecken vorbei und verlässt das Wohnviertel, eilst zum hügeligen Gebiet abseits. Eine ganze Weile bist du unterwegs, bis du die gut versteckten Hügelhäuser entdeckst, die deine Sippe errichtet hat. Du schlüpfst durch einen der schmalen kleinen Eingänge und weißt, dass nicht einmal ein Menschenkind diese schmalen Pforte passieren kann und dass sich hier nur Goblins aufhalten. Du begibst dich tief in das halbunterirdische Gewölbe und triffst viel andere Goblins. Ein wohl bekanntes Geschreie tritt an dein Ohr und du fühlst dich daheim. Hier bist du mit deinen Freunden allein und niemand wird eure Zusammenkunft stören können. Du beschließt eine Weile hier zu bleiben bevor du dich wieder in die gefährliche Welt der Menschen begibst.`&`n`n");

showcasulties($races[$_GET[race]]);

	viewcommentary("goblinbau","Kreischen:",30,"kreischt");
	addnav('Zur Rassenliste','racesspecial.php?op=show_list&race='.$_GET['race']);
	addnav("Den Ort verlassen","houses.php");
	}
	else if ($_GET[race]==RACE_ORK) {
	page_header("Die Orkfeste");
	output("`tDu holst die alte Karte hervor und folgst den verschlungenen Waldwegen, dein Ziel stets vor Augen. Obwohl es schwierig ist diesen Ort zu finden, erreichst du mit Hilfe der Karte endlich die Orkfeste. Du stellst dich vor das mächtige Eingangsportal und stößt einen markerschütternden Schrei aus. Schon öffnet sich die Türe und du wirst herein gelassen. Die Atmosphäre im Inneren der Orkfeste gefällt dir sehr. Viele andere Orks haben sich hier eingefunden und sind mit Speis und Trank, dem Erzählen wilder Kriegsgeschichten und Raufereien beschäftigt. Kaum hast du dich auf einer Holzbank niedergelassen drückt dir ein anderer Ork schon einen riesigen Krug Ale und eine Menschenkeule in die Hand. Hier kannst du es dir nun so richtig gut gehen lassen bevor du wieder zurück ins Dorf gehst.`&`n`n");

showcasulties($races[$_GET[race]]);

	viewcommentary("orkfeste","Gröhlen:",30,"gröhlen");
	addnav('Zur Rassenliste','racesspecial.php?op=show_list&race='.$_GET['race']);
	addnav("Den Ort verlassen","forest.php");
	}
	else if ($_GET[race]==RACE_VAMPIR) {
	page_header("Das Mausoleum");
	output ("`tDu schleichst durch Wohnviertel und näherst dich zielsicher dem Friedhof. Vorbei an unzähligen Gräbern und Gruften bahnst du dir deinen Weg bis hin zu einem ganz bestimmten Bau. Du legst deine Hände auf das steinerne Türportal und es bewegt sich langsam auf Seite um kurz nachdem du das Mausoleum betreten hast, wieder hinter dir zuzuschlagen. Du gehst einen langen Gang entlang und steigst eine Treppe hinab. Deine Schritte hallen dumpf nacht. Dann gelangst du in einen großen Raum, in dem sich schon andere Vampire eingefunden haben. Viele von ihnen kennst du bereits. Sie hängen an der Decke, ruhen oder unterhalten sich. Ein paar der Vampire haben eine junge vor Schrecken starre Menschenfrau mitgebracht, die sie der Reihe nach durchreichen um von ihr zu trinken. Du weißt dass sich hier nur Vampire aufhalten, und das verleiht dir ein Gefühl von Sicherheit. Du setzt dich auf einen Sarg und winks den anderen Vampiren, die du kennst, zu.`&`n`n");

showcasulties($races[$_GET[race]]);

	viewcommentary("mausoleum","Flüstern:",30,"flüstert");
	addnav('Zur Rassenliste','racesspecial.php?op=show_list&race='.$_GET['race']);
	addnav("Den Ort verlassen","houses.php");
	}
	else if ($_GET[race]==RACE_HALBLING) {
	page_header("Die Hügelhäuser");
	output ("`tDu schlendest durch das Wohnviertel und suchst die Weiten der grünen Ebenen weit abseits der dicht besiedelten Strassen. Dort ist es hügelig und die Natur ist unberührt.`nSchliesslich gelangst du an einen großen hohlen Baum. Dort gibst du das geheime Klopfzeichen, dass dich deine Eltern einst gelehrt haben und wenig später fällt dir eine winzig kleine Tür in einem der Hügel auf. Gerade groß genug dass du hindurchschlüpfen kannst.`nDu betrittst das kleine Häuschen und entdeckst, dass sich viele andere Halblinge hier versammelt haben, die dich mit einem guten Becher Ale und einer frisch gestopften Pfeife begrüßen.`nDu bist dir ganz sicher, dass ihr hier unter euch seid und entschliesst ein wenig mit deinen Artgenossen zu feiern.`&`n`n");

showcasulties($races[$_GET[race]]);

	viewcommentary("huegelhaeuser","Rufen:",30,"ruft");
	addnav('Zur Rassenliste','racesspecial.php?op=show_list&race='.$_GET['race']);
	addnav("Den Ort verlassen","houses.php");
    }
	else if ($_GET[race]==RACE_DAEMON) {
	page_header("Die Schwefelquellen");
	output ("`tDu schreitest durch den tiefen Wald, knickst dünne Bäume und Sträucher um und schlägst eine Schneise durch den dichten Wuchs. Du weißt genau wo du hin willst, und plötzlich vernimmst du auch diesen wohlig stechenden Geruch in deiner Nase. Nur ein paar Schritte später liegen sie vor dir : die blubbernden heißen Quellen. Der beißende Schwefelegeruch ist so stark, dass kein Mensch und auch kein anderes diesseitiges Wesen hier überleben kann.`nDu kannst dir ganz sicher sein, dass du hier nicht gestört wirst und erblickst auch schon ein paar andere Dämonen, die sich ebenfalls hier hin zurück gezogen haben`&`n`n");

showcasulties($races[$_GET[race]]);

	viewcommentary("schwefelquelle","Fauchen:",30,"faucht");
	addnav('Zur Rassenliste','racesspecial.php?op=show_list&race='.$_GET['race']);
	addnav("Den Ort verlassen","forest.php");
	}
    else if ($_GET[race]==RACE_SCHELM) {
	page_header("Der Schelmenraum");
	output ("`tDu tänzelst durch das Wohnviertel auf der Suche nach einem ganz bestimmten Haus.`nDann hast du es gefunden : groß und seriös wirkend nimmt es seinen Platz unter den anderen Häusern ein. Du kletterst durch ein Kellerfenster hinein, steigst eine Treppe hinauf und öffnest eine schwere Eichentüre.`nUnd dann siehst du sie : Andere, die so sind wie du, füllen einen großen Raum. Lärm und Gelächter schlägt dir entgegen und du fühlst dich ein wenig an deine Kindheit bei den Feen erinnert. Du lauschst den Gesprächen, die sich für dich schnell als totaler Unsinn oder Berichte über Scherze mit argwöhnischen Menschen und Elfen herausstellen.`nHier fühlst du dich wohl, denn du weißt, dass niemand, der kein Schelm auch nur für eine Minute in diesem Raum verweilen könnte`&`&`n`n");

showcasulties($races[$_GET[race]]);

	viewcommentary("schelmenraum","Scherzen:",30,"scherzt");
	addnav('Zur Rassenliste','racesspecial.php?op=show_list&race='.$_GET['race']);
	addnav("Den Ort verlassen","houses.php");
	}
	else if ($_GET[race]==RACE_ENGEL) {
	page_header("Die Wolkenfestung");
	output ("`tDu schreitest durch den Wald, frei und unbekümmert. An einer Lichtung holst du dann tief Luft und löst dich von der beklemmenden Hülle, die dir dein Körper ist.`nDu schwebst hinauf wie du es gewohnt bist, hoch zu den Wolken. Die Welt unter dir wird immer kleiner und unwirklicher. Als du das Wolkendach durchbrochen hast stellst du fest, dass du nicht allein bist.`nAndere haben sich wie du hierher zurückgezogen, um sich von den Strapazen der Sterblichkeit zu erholen. Du weißt dass euch hier niemand Anderes stören kann und verweilst ein wenig bei Deinesgleichen.`n`n");

showcasulties($races[$_GET[race]]);

	viewcommentary("wolkenfestung","Frohlocken:",30,"frohlockt");
	addnav('Zur Rassenliste','racesspecial.php?op=show_list&race='.$_GET['race']);
	addnav("Den Ort verlassen","forest.php");
	}
	else if ($_GET[race]==RACE_AVATAR) {
	page_header("Die Leere");
	output ("`tDu blickst dich kurz um, sicherstellend, dass die niemand beobachtet, und streckst deine Hände aus um das dünne Gewebe der Wirklichkeit zu zerreissen.`nDahinter ist nichts, die Leere, und du trittst ein in ein schwarzes Wabern.`nDu verlässt die Grenzen von Zeit und Raum und kehrst zum Ursprung zurück. Langsam spürst du wie deine Kräfte vollends zu dir zurück kehren. Du spürst die Presänz anderer Avatare.`nHier seid ihr nun unter Euch, da dieser Ort Ausserhalb jeglicher Vorstellungskraft anderer Wesen existiert.`n`n");

showcasulties($races[$_GET[race]]);

	viewcommentary("dieleere","Mitteilen:",30,"teilt mit");
	addnav('Zur Rassenliste','racesspecial.php?op=show_list&race='.$_GET['race']);
	addnav("Den Ort verlassen","forest.php");
	}
}

page_footer();
?>
