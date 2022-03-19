<?
require_once "common.php";
checkday();
page_header("Dorfplatz");

$event=$_GET[event];
if ($event=="") $event=e_rand(1,2);

switch ($event) {

// Gasse
case 1 :
if ($_GET[op]=="")
{
output ("`@Wie du so auf dem Dorfplatz stehst, fällt dir plötzlich eine Gestalt auf, die in gebückter Haltung rasch hinter einer Häuserecke in eine dunkle Gasse entschwindet.`nBei allem was dir heilig ist könntest du schwören, dass dieser Geselle etwas ausgefressen hat. Außer dir scheint niemand die Gestalt wahrgenommen zu haben. Ein bisschen mulmig ist dir schon zumute, immerhin könnte dich in dieser Gasse alles möglich erwarten, sogar der Tod! Doch andererseits könntest du durch die Ergreifung eines Schurken eine Heldentat begehen und so zu großem Ruhm und Ansehen gelangen.`nWie wirst du dich entscheiden ?");
addnav("In die Gasse folgen","villageevents.php?op=gasse2&event=$event");
addnav("Bleiben wo du bist","village.php");
} else

if ($_GET[op]=="gasse2")
{
output ("`tDu fasst dir ein Herz und huschst in die Gasse, der Gestalt hinterher. Es ist dunkel und du erkennst im ersten Moment rein gar nichts, orientierst dich nur an den leisen, schleichenden Geräuschen vor dir.`nDann auf einmal hörst du eine Tür zuschlagen und es wird still. Da sich deine Augen nun etwas besser an die Dunkelheit gewöhnt haben, siehst du links und rechts von dir jeweils eine Tür, beide scheinen nicht verschlossen zu sein.`nWas tust du?");
addnav("Nimm die linke Tür","villageevents.php?op=house&event=$event");
addnav("Nimm die rechte Tür","villageevents.php?op=house&event=$event");
addnav("Zurück zum Dorfplatz","village.php");
} else

if ($_GET[op]=="house")
{
switch(e_rand(1,5)) {
case 1 :
case 2 :
case 3 :
output ("`tDu bist im Inneren eines Hauses, schwaches Licht fällt von irgendwoher in den Raum. Du erkennst einen Mantel, achtlos in eine Ecke geworfen, sowie eine Treppe, die ein Stockwerk höher führt. Auch kannst du eine Falltüre ausmachen, die in den Keller führt.`nWofür entscheidest du dich?");
addnav("Nimm den Mantel","villageevents.php?op=coat&event=$event");
addnav("Treppe hoch","villageevents.php?op=upstairs&event=$event");
addnav("In den Keller","villageevents.php?op=down&event=$event");
addnav("Raus hier","village.php");
break;
case 4 :
output ("`tDu schlüpfst durch die Türe und findest dich in einem kleinen Raum wieder. Der Raum ist schwach beleuchtet und ein Tisch steht in der Mitte. Verdächtig aussehende Gestalten richten sich plötzlich vom Tisch auf, als du herein kommst. Und bevor du dich versiehst saust ein Wurfdolch vorbei an deinem Gesicht in die Türe. Die Gauner sehen nicht so aus als ob sie Spass verstehen.`nWas tust du nun?");
addnav("Kämpfe!","villageevents.php?op=fight&event=$event");
addnav("Rede dich raus","villageevents.php?op=argue&event=$event");
addnav("Flüchte","village.php");
break;
case 5 :
output("`tDu kommst in eine Waschküche und blickst in das völlig verdutzte Gesicht einer alten Frau, die gerade lange Unterhosen in einer kleinen hölzernen Wanne wäscht. Vollkommen fassungslos und auch ohne Worte starrt sie dich an. Anders ihre schwarze Katze, die dir dich anspringt und dir das Gesicht zerkratzt. Unter Schmerzensschreien schüttelst du das Tier ab und rennst raus. Das soll dir eine Lektion sein! Die Spuren dieses Abenteuers werden wohl ewig in deinem Gesicht zu erkennen sein!");
addnews("`@".$session['user']['name']."`@ hat seltsame Kratzer im Gesicht, schweigt sich darüber jedoch aus!");
$session['user']['charm']-=3;
addnav("Zurück","village.php");
break;
}
} else
if ($_GET[op]=="coat")
{
output("`tDu durchsuchst den Mantel, in der Hoffung etwas Wertvol...ähh Beweise zu finden.");
switch(e_rand(1,5)) {
case 1 :
case 2 :
output ("`t`nDu gehst dabei sehr gründlich vor, aber dennoch findest du nichts von Interesse. Langsam kommst du dir wirklich blöd vor, wie du in einem fremden Haus einen alten Mantel durchwühlst. Und bevor dich noch jemand erwischt ziehst du es vor schnell und unauffällig wieder zu verschwinden.");
addnav("Zum Dorfplatz","village.php");
break;
case 3 :
case 4 :
$chance=e_rand(1,3);
output ("`t`nEin schlechtes Gewissen hast du schon, aber als du plötzlich etwas Hartes in einer Innentasche fühlst, ist das schnell vergessen. Im Mantel waren doch tatsächlich `^$chance Edelsteine`t, die du natürlich sicherstellst. Doch irgendwie... kommt dir das Ganze nun etwas seltsam vor, und bevor man dich wegen Einbruchs verhaftet ziehst du es vor mit deinem Fund zu verschwinden.");
$session['user']['gems']+=$chance;
addnav("Zurück","village.php");
break;
case 5 :
output ("`t`nIn diesem Mantel ist nichts, so glaubst du... Doch als du das alte Ding schon zurück in die Ecke werfen willst bemerkst du ein Rascheln. Du öffnest eine Seitentasche und hälst einen `^Freibrief`t in deinen Händen. Hast du ein Glück! Du steckst das gute Stück ein und siehst zu dass du davon kommst.");

// Freibrief
item_add($session['user']['acctid'],'frbrf');

addnav("Zurück","village.php");
break;
}
} else
if ($_GET[op]=="upstairs")
{
output ("`tDu steigst die Stufen der Treppe hoch und findest dich in einer kleinen Kammer wieder. Ein Bett, eine Kommode und ein Tisch stehen darin. Ebenfalls kannst du eine große Truhe erkennen. Sie sieht allzu verlockend für dich aus. Wer weiß welche Reichtümer sie beinhaltet? Na was ist, willst du nicht mal nachsehen ?");
addnav ("Die Truhe öffnen","villageevents.php?op=chest&event=$event");
addnav ("Zurück zur Gasse und in die andere Tür","villageevents.php?op=house&event=$event");
addnav ("Zum Dorfplatz","village.php");
} else
if ($_GET[op]=="chest")
{
output ("`t`nMit gierigem Blick in deinen Augen eilst du zur Truhe und öffnest sie ohne nachzudenken.");
switch(e_rand(1,4)) {
case 1 :
case 2 :
$gold=e_rand(500,3000); $gems=e_rand(1,5);
output ("`t`nDu klappst den Deckel auf und dich lachen `^ $gold Gold und $gems Edelsteine`t an, die du hastig in deinen Taschen verschwinden lässt bevor du dich davon machst.");
$session['user']['gold']+=$gold;
$session['user']['gems']+=$gems;
addnav ("Schnell weg","village.php");
break;
case 3 :
output ("`t`nDu schaust in das Innere der Truhe und lachst vor Freude, also du `^Tausende Goldmünzen und Dutzende Edelsteine`t erblickst. Du greifst mit beiden Händen hinein und spürst plötzlich eine Hand auf deiner Schulter. Als du dich umdreht schaust du in das grinsende Gesicht einer Stadtwache");
 if (($session['user']['profession']!=21) && ($session['user']['profession']!=22)) {
output (", die mit einem leisen `RKlick`t die Handschellen einrasten lässt. Zwar wehrst du dich und redest dich raus, aber letztendlich landest du doch im Kerker!");
addnews("`@".$session['user']['name']."`@ wurde bei einem Einbruch gefasst und eingekerkert. Über der Zelle hängt ein Schild mit der Aufschrift `^Dümmster Einbrecher der Woche`@");
$session['user']['imprisoned']=2;
addnav ("In den Kerker","prison.php"); }
else {
output (", die dich als Richter zwar nicht festnehmen kann, dich aber an der Hand nimmt und dich zurück auf den Dorfplatz begleitet.");
addnews("`@Richter ".$session['user']['name']."`@ wurde bei einem Einbruch gefasst und entging dank der Immunität dem Kerker.");
addcrimes("`@Richter ".$session['user']['name']."`@ wurde bei einem Einbruch gefasst und entging dank der Immunität dem Kerker.");
addnav ("Nun denn...","village.php");
}
break;
case 4 :
output ("`t`nDen Anblick von `^Gold und Edelsteinen`t trübt lediglich der süße Schmerz, den ein Dorn verursacht, der beim Öffnen der Truhe in deine Hand einschlug. Und während du die Reichtümer der Truhe in deine Taschen schaufelst, nimmt dir das Gift, das sich in deinem Körper ausbreitet, das Leben! Du bist tot und verlierst 5% deiner Erfahrung!");
$session['user']['experience']*=0.95;
$session['user']['hitpoints']=0;
addnews("`@".$session['user']['name']."`@ weiß nun, dass Truhen auch mit Fallen versehen sein können und wird im nächsten Leben sicher daran denken.");
addnav("Mit einem Lächeln sterben","shades.php");
break;
}
} else
if ($_GET[op]=="down")
{
output ("`tDu klappst die Falltüre auf und kletterst eine schmale Leiter hinab. Schließlich bist du ganz unten angelangt. Schummeriges Licht erhellt den Keller ein wenig, du weißt nicht woher es kommt.");
switch(e_rand(1,3)) {
case 1 :
$gold=e_rand(1000,5000); $gems=e_rand(2,6);
output ("`nAls du dich etwas umblickst kannst du die Lichtquelle ausmachen : Eine fast abgebrannte Fackel an der Wand. Du nimmst die Fackel aus der Halterung und leuchtest die Ecken aus, denn von allein wird sie ja nicht hergekommen sein. Hinter einer Kiste hockt, unter einer Decke zusammengekauert, ein dürrer Mann. Seine Hände umklammern fest einen dicken Beutel, der mit Gold und Schmuck gefüllt sein muss. Er blickt resigniert zu dir auf und seufzt leise. Du packst den Strolch am Kragen und zerrst ihn nach oben. Als du mit ihm die Gasse wieder verlässt, begegnest du einer Stadtwache. Hoch erfreut über deinen Fang zahlt sie dir eine Belohnung von `^$gold Gold und $gems Edelsteinen`t aus. Außerdem stellt sie dir einen `^Freibrief`t aus!");
$session['user']['gold']+=$gold;
$session['user']['gems']+=$gems;

// Freibrief
item_add($session['user']['acctid'],'frbrf');

addnews("`@".$session['user']['name']."`@ hat einen Gauner geschnappt und wurde dafür reich belohnt.");
addnav("Hurra","village.php");
break;
case 2 :
output ("`t`nSchnell stellst du fest, dass das Licht von einer Türe kommt, die leicht angelehnt ist. Du näherst dich um zu lauschen, doch da fliegt die Türe schon auf, und eine Gruppe Halsabschneider starrt dich an, die Messer gezückt. Wie willst du dich nun da raus bringen ?");
addnav("Kämpfe!","villageevents.php?op=fight&event=$event");
addnav("Rede dich raus","villageevents.php?op=argue&event=$event");
addnav("Flüchte","village.php");
break;
case 3 :
output ("`t`nNeugierig schaust du dich nach der Lichtquelle um und als du dich ihr näherst spürst du nur noch einen Schlag auf deinen Hinterkopf und es wird dunkel. Du erwachst gefesselt und geknebelt, an den Füßen aufgehangen. Um dich herum haben sich ein paar Orks und Goblins versammelt, die sich nun an dir gütlich tun werden. Du verlierst 5% deiner Erfahrung und bist tot.");
$session['user']['experience']*=0.95;
$session['user']['hitpoints']=0;
addnews("`@".$session['user']['name']."`@ wurde von Orks und Goblins gefrühstückt.");
addnav("Mahlzeit!","shades.php");
break;
}
} else
if ($_GET[op]=="fight")
{
output ("`tDu bist deutlich in der Unterzahl, nutzt jedoch deine Kampfkünste und deine Erfahrung. In angeberichen Posen zeigst du kleine Kunststücke mit deiner Waffe und näherst dich der Gaunergruppe.");
switch(e_rand(1,4)) {
case 1 :
case 2 :
$gold=e_rand(3000,10000); $gems=e_rand(3,10);
output ("`t`nWie ein Meister der hohen Kampfkunst schlägst du Einen nach dem Anderen kampfunfähig. Deine Schläge sitzen perfekt und du überstehst den Kampf unverletzt als Sieger. Die lauten Kampfgeräusche und das Ächzen und Stöhnen deiner unterlegenen Gegner haben eine Stadtwache aufmerksam gemacht, die gerade in der Nähe auf Patrouille war. Sie teilt dir mit, dass du eine schon lange gesuchte Verbrecherbande gefasst hast und überreicht dir zum Dank `^$gold Gold und $gems Edelsteine`t, sowie einen `^Freibrief`t.");
$session['user']['gold']+=$gold;
$session['user']['gems']+=$gems;

// Freibrief
item_add($session['user']['acctid'],'frbrf');

addnews("`@".$session['user']['name']."`@ hat eine Diebesbande geschnappt und wurde zum Held des Tages erklärt.");
addnav("Hurra","village.php");
break;
case 3 :
output ("`t`nBereits dein erster Gegner schlägt dir hart mit der Faust ins Gesicht. Damit hast du nicht gerechnet. Unter Schlägen und Tritten sackst du zusammen bis du ohnmächtig bist. Die Gauner machen sich einen Spass und versehen dich mit vielen kleinen Indizien und Beutestücken, bevor sich dich bei der Stadtwache abliefern");
 if (($session['user']['profession']!=21) && ($session['user']['profession']!=22)) {
output ("und für deine Ergreifung sogar noch eine Belohnung erhalten! Du kommst in den Kerker.");
addnews("`@".$session['user']['name']."`@ wurde heute endlich nach wochenlangem Raubzug durch unser schönes Dorf von ehrenhaften Bürgern gefasst!");
$session['user']['imprisoned']=3;
addnav ("In den Kerker","prison.php"); }
else {
output (". Allerdings besitzt du Immunität, weswegen die Wache dich nicht einsperren kann.");
addnews("`@Richter ".$session['user']['name']."`@ wurde nach wochenlangem Raubzug durch unser schönes Dorf gefasst, entgeht dank der Immunität aber dem Kerker!");
addcrimes("`@Richter ".$session['user']['name']."`@ wurde nach wochenlangem Raubzug durch unser schönes Dorf gefasst, entgeht dank der Immunität aber dem Kerker!");
addnav ("Weiter","village.php");
}
break;
case 4 :
output ("`t`nDu streckst deinen ersten Gegner eiskalt nieder, näherst dich dann dem Zweiten. Auch ihn schickst du schnell zu Ramius. Doch dann trifft dich eine Klinge genau zwischen die Schulterblätter. Zwar schaffst du es noch einen Dritten zu töten, doch hauchst du selbst nach mehreren weiteren Treffern auch dein Leben aus. Du bist tot und verlierst 5% deiner Erfahrung!");
$session['user']['experience']*=0.95;
$session['user']['hitpoints']=0;
addnews("`@".$session['user']['name']."`@ wurde erstochen in einer Gasse gefunden.");
addnav("Heldentod!","shades.php");
break;
}
} else
if ($_GET[op]=="argue")
{
output ("`tDu versuchst durch geschickte Ausreden Herr der Lage zu werden und plapperst wie wild drauf los. Die Worte sprudeln aus deinem Mund wie Wasser aus einer Quelle. Die Halunken schauen dich fassungslos an.");
switch(e_rand(1,4)) {
case 1 :
output ("`t`nDann erheben sich die Schurken, in fester Absicht dich endgültig zum Schweigen zu bringen. Dir wird wohl nichts anderes übrig bleiben als zu kämpfen. Also atmest du einmal tief ein und ziehst deine Waffe.");
addnav("Kämpfen","villageevents.php?op=fight&event=$event");
break;
case 2 :
output ("`t`nDie Gauner stürmen auf dich zu, doch anstatt dich zu töten verpassen sie dir einen gewaltigen Tritt und werfen dich vor die Tür. Du hast wirklich Glück, dass sie dich nicht ernst genommen haben!");
addnav("Zum Dorfplatz","village.php");
break;
case 3 :
output ("`t`nDu redest dich um Kopf und Kragen, plapperst laut und unbeherrscht drauf los. Dann auf einmal spürst du einen Schlag in den Rücken und kippst nach vorn. Eine Gruppe von 5 Stadtwachen stürmt den kleinen Raum und nimmt alle fest, auch dich. Du hast sie geradewegs dorthin geführt, doch dass du nicht zu der Bande gehörst will dir auch so recht keiner glauben.");
 if (($session['user']['profession']!=21) && ($session['user']['profession']!=22)) {
output("Also verbringst du erstmal den Rest des Tages im Kerker.");
addnews("`@".$session['user']['name']."`@ wurde als Bandenmitglied gefasst und in den Kerker geworfen!");
$session['user']['imprisoned']=1;
addnav ("In den Kerker","prison.php"); }
else {
output("Da du Richter bist können sich dich aber auch nicht wegsperren. Glück gehabt!");
addnews("`@Richter ".$session['user']['name']."`@ wurde als Bandenmitglied gefasst, entgeht dank der Immunität aber dem Kerker!");
addcrimes("`@Richter ".$session['user']['name']."`@ wurde als Bandenmitglied gefasst, entgeht dank der Immunität aber dem Kerker!");
addnav ("Zurück","village.php");
}
break;
case 4 :
$gold=e_rand(1000,3000); $gems=e_rand(1,5);
output ("`t`nDie Gauner schauen sich gegenseitig an und springen plötzlich auf. Durch eine kleine Tür im hinteren Teil des Raumes gelangen sie in eine Gasse und rennen davon. Die haben doch tatsächlich geglaubt, du wärst nicht allein gekommen! Mit einem Schmunzeln machst du dich daran die Beute dieser Bande einzustecken. Insgesamt immerhin `^$gold Gold und $gems Edelsteine`t.");
$session['user']['gold']+=$gold;
$session['user']['gems']+=$gems;
addnav("Hurra","village.php");
break;
}
}
// end gasse
break;

case 2 :

if ($_GET[op]=="")
{

// Wenn neblig und Abendgrauen / Morgendämmerung
$time = gametime();
$hour = (int)date('H',$time);

if(getsetting('weather','') == WEATHER_FOGGY && ( ($hour < 10 && $hour > 4) || ($hour < 22 && $hour > 16) ) ) {

output ("`@Wie du auf dem Dorfplatz stehst, hörst du plötzlich ein lautes Gurgeln und ein dumpfes Dröhnen aus dem Schacht des Dorfbrunnens. Nahezu unheimlich dringen die Geräusche an dein Ohr und du bekommst eine Gänsehaut.`nAuch bemerkst du, dass es mit einem Mal neblig und kalt zu werden scheint.`nWas wirst du tun ?`0");

$sql = "INSERT INTO commentary (postdate,section,author,comment) VALUES (now(),'village',1,'/msg `7Der Brunnen gibt plötzlich seltsame gurgelnde und schabende Geräusche von sich. Leichter Nebel liegt in der Luft.`0')";
db_query($sql) or die(db_error(LINK));

addnav("Dich dem Brunnen nähern","villageevents.php?op=closer&event=$event");
addnav("Sicheren Abstand nehmen","villageevents.php?op=flee&event=$event");

}
else {	// Keine Bedingungen für ein ordentliches Brunnenmonster
	redirect('village.php');
}

} 

else

if ($_GET[op]=="flee")
{
sleep(5);
$sql = "INSERT INTO commentary (postdate,section,author,comment) VALUES (now(),'village','".$session['user']['acctid']."','/me weicht erschreckt und verunsichert zurück und hält einen deutlichen Abstand zum  Brunnen.`0')";
db_query($sql) or die(db_error(LINK));
redirect("village.php");
} else

if ($_GET[op]=="flee2")
{
sleep(5);
$sql = "INSERT INTO commentary (postdate,section,author,comment) VALUES (now(),'village','".$session['user']['acctid']."','/me hat es sich wohl doch anders überlegt und entfernt sich rasch vom Brunnen.`0')";
db_query($sql) or die(db_error(LINK));
redirect("village.php");
} else

if ($_GET[op]=="closer")
{
sleep(5);
$sql = "INSERT INTO commentary (postdate,section,author,comment) VALUES (now(),'village','".$session['user']['acctid']."','/me nähert sich neugierig dem Dorfbrunnen.`0')";
db_query($sql) or die(db_error(LINK));
output ("`@Nahezu bedrohlich wirkt dieser verfluchte Brunnen auf dich, doch hat er auch eine gewisse Anziehungskraft. Du glaubst zu hören wie etwas versucht den Brunnenschacht hinauf zu klettern.`nDeine Neugier ringt mit deiner Angst.`nWas tust du?`0");
addnav("Über den Brunnenrand schauen","villageevents.php?op=edge&event=$event");
addnav("Dich entfernen","villageevents.php?op=away&event=$event");
} else

if ($_GET[op]=="away")
{
sleep(5);
  $ops= e_rand(1,3);
  switch ($ops) {
   case 1 :
   case 2 :
   output("`@Du ziehst es vor, doch lieber in Sicherheit zu bleiben.`0");
   redirect("villageevents.php?op=flee2&event=$event");
   break;
   case 3 :
   $sql = "INSERT INTO commentary (postdate,section,author,comment) VALUES (now(),'village',1,'/msg `7Plötzlich schnellt ein langer, dürrer, tentakelähnlicher Strang aus dem Brunnen hervor und schlingt sich um den Hals von `&".$session['user']['name']."`7, um ".($session[user][sex]?"sie ":"ihn ")." in den Brunnen zu ziehen!`0')";
db_query($sql) or die(db_error(LINK));

   output("`@Du drehst dich um, bereit dich wieder zu entfernen, als plötzlich ein langes, schlankes Tentakel aus dem Brunnen herausschiesst und sich um deinen Hals schlingt.`nDu bekommst kaum Luft und bist wie gelähmt!`nWas nun ?`0");
   addnav("Um Hilfe rufen","villageevents.php?op=helpme&event=$event");
   addnav("Auf den Strang einschlagen","villageevents.php?op=hack&event=$event");
   break;
  }
} else

if ($_GET[op]=="helpme")
{
$sql = "INSERT INTO commentary (postdate,section,author,comment) VALUES (now(),'village','".$session['user']['acctid']."','/me ruft verzweifelt mit Armen und Beinen strampend und im Würgegriff des Stranges :\"Hilfe! So helft mir doch!\".`0')";
db_query($sql) or die(db_error(LINK));
  $ops= e_rand(1,2);
  switch ($ops) {
  case 1 :
  sleep(5);
  output("`@Durch das beherzte Eingreifen einiger Helden in deiner Nähe wirst du gerettet und der Strang zieht sich in den Brunnen zurück.`nDu hast Lebenspunkte verloren!");
  $session['user']['hitpoints']*=.7;
  $sql = "INSERT INTO commentary (postdate,section,author,comment) VALUES (now(),'village',1,'/msg `7Die schnelle Hilfe der Helden auf dem Dorfplatz rettet `&".$session['user']['name']."`7 das Leben und der Strang zieht sich in den Brunnen zurück.`0')";
  db_query($sql) or die(db_error(LINK));
  addnav("Glück gehabt!","village.php");
  break;
  case 2 :
  output("`@Dein Schreien und Wimmern hat dir nicht geholfen. Der Strang zieht dich in den Brunnen herab, wo dich ein sehr unangenehmer Tod erwartet.`0");
  addnav("weiter","villageevents.php?op=die&event=$event");
  break;
  }
} else

if ($_GET[op]=="die")
{
sleep(5);
  $sql = "INSERT INTO commentary (postdate,section,author,comment) VALUES (now(),'village',1,'/msg `7Der Strang zieht `&".$session['user']['name']."`7 gnadenlos in den Brunnen. Kurze Zeit später sind laute krachende und knirschende Geräusche zu hören, dann wird alles still.`0')";
  db_query($sql) or die(db_error(LINK));
  $session['user']['hitpoints']=0;
  addnews("`@".$session['user']['name']."`@ wurde von etwas in den Dorfbrunnen gezogen und starb!");
  redirect("shades.php");
} else

if ($_GET[op]=="hack")
{
  sleep(5);
  $ops= e_rand(1,3);
  switch ($ops) {
  case 1 :
  output("`@Es gelingt dir dich freizuschlagen und der Strang zieht sich in den Brunnen zurück.`nDu hast Lebenspunkte verloren!`0");
  $session['user']['hitpoints']*=.8;
  $sql = "INSERT INTO commentary (postdate,section,author,comment) VALUES (now(),'village',1,'/msg `7Der Strang lässt von `&".$session['user']['name']."`7 ab und zieht sich in den Brunnen zurück.`0')";
  db_query($sql) or die(db_error(LINK));
  addnav("Glück gehabt!","village.php");
  break;
  case 2 :
  case 3 :
  output("`@Du schlägst nach dem Strang und windest dich, allerdings erfolglos. Der Strang zieht dich in den Brunnen herab, wo dich ein sehr unangenehmer Tod erwartet.`0");
  addnav("weiter","villageevents.php?op=die&event=$event");
  break;
  }
} else

if ($_GET[op]=="edge")
{
$sql = "INSERT INTO commentary (postdate,section,author,comment) VALUES (now(),'village',1,'/msg `7Plötzlich schnellt ein langer, dürrer, tentakelähnlicher Strang aus dem Brunnen hervor und schlingt sich um den Hals von `&".$session['user']['name']."`7, um ".($session[user][sex]?"sie ":"ihn ")." in den Brunnen zu ziehen!`0')";
db_query($sql) or die(db_error(LINK));
sleep(5);
output("`@Du beugst dich über den Rand des Brunnens um hinein zu schauen und erkennst, dass in der Tiefe ein kleines rotes Lichtlein leutet.`nDu kneifst die Augen zusammen um es besser erkennen zu können, und stellst fest, dass es sich um irgendein Symbol handelt, dass du jedoch noch nie in deinem Leben zuvor gesehen hast.`n`n`0");
output("<IMG SRC=\"images/symbol.jpg\" align='middle'>",true);
output("`n`n`@Als Du dich aufrichtest um zu gehen schnellt plötzlich ein langer tentakelähnlicher Strang aus der Tiefe hinauf und schlingt sich um deinen Hals.`nWas nun?`0");
addnav("Um Hilfe rufen","villageevents.php?op=helpme&event=$event");
addnav("Auf den Strang einschlagen","villageevents.php?op=hack&event=$event");
}

// end switch
}
page_footer();
?>
