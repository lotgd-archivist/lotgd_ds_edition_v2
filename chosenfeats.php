<?php

// 
// Weitere kleine Features für die Feste der Auserwählten
// Bestandteil von : Die Auserwählten
// Benötigt : castleevents.php, abandonedcastle.php, thepath.php
// Modifiziert : bio.php, prefs.php, inn.php, prison.php, newday.php, dorfamt.php, shades.php etc
// by Maris (Maraxxus@gmx.de)

// edit by talion: Umstellung der Wetterhexe auf neues System

require_once "common.php";
checkday();

if ($_GET[op]=="hag") {
page_header("Die Wetterhexe");
output("`tDu steigst ein paar schmale und steile Stufen hinaus und befindest dich im Obergeschoss der Feste. Neugierig erkundest du den Gang und die Räume, schaust mal hier hinein, mal dort hinein. Als du die Tür zu einer verwinkelten Kammer im Eck der Festung aufstößt, fällt dir auf, dass diese bewohnt ist.`nVor dir sitzt in einem Ohrensessel eine sehr alte Frau in ein sehr aufreizendes Kleid gewandt, welches mehr zeigt als verbirgt. Du bist dir sofort sicher, dass die Frau ZU alt und das Kleid ZU aufreizend ist und wendest ein wenig den Blick ab, doch nicht zu auffällig, denn du willst ja nicht unhöflich erscheinen. Die Frau spricht dich an:`n");
output("\"`RNaaa, mein".($session[user][sex]?"e Süße!":" Süßer!")." Hast du dich verlaufen? Oder wolltest du zu mir? Schönes Wetter draussen, nicht wahr? Oder etwa doch nicht? Weißt du, für jemanden wie DICH würde ich auch das ändern. Gib mir doch einfach `^2000 Gold`R und ich erfülle dir JEDEN Wunsch *zwinker*. Na, was ist?`t\"`nDie Alte lacht hysterisch und du bekommst es mit der Angst. Willst du ihr dein Gold geben?");
addnav("2000 Gold geben","chosenfeats.php?op=hag2");
addnav("Weg hier!","thepath.php");
}
if ($_GET[op]=="hag2") {
page_header("Die Wetterhexe");
 if ($session['user']['gold']<2000) {
   output("`tDu willst die Alte bezahlen, stellst aber schnell fest, dass du dir das momentan nicht leisten kannst. Also verabschiedest du dich und eilst davon.");
   addnav("Zurück","thepath.php");
} else {
output("`tDie Hexe nimmt dein Gold, betrachtet jede Münze einzeln und lässt das Säckchen dann in einem Eck ihrer Kammer verschwinden. Dann funkelt sich dich mit frivol belustigtem Blick an.`n\"`RNun mein".($session[user][sex]?"e Hübsche,":" Hübscher,")." was kann ich für dich tun?`t\"");
$session['user']['gold']-=2000;
addnav("Weise Frau,...");
addnav("...ändere das Wetter!","chosenfeats.php?op=hag3");
addnav("...du weißt doch genau was ich will!","chosenfeats.php?op=hag4");
addnav("...lass mich bitte gehen!","thepath.php");
}
}
if ($_GET[op]=="hag3") {
	page_header("Die Wetterhexe");
	$w = get_weather();
	output("`tDie Alte wirft einen Blick aus dem Fenster.`n\"`RSoso... `6".$w['name']."`R. Und wie hättest du es gern?`t\"`n");

	addnav("Wähle dein Wetter");

	foreach($weather as $id => $w) {
	
		addnav($w['name'],'chosenfeats.php?op=hag31&what='.$id);
	}
	addnav("Soll so bleiben...","thepath.php");
}


if ($_GET[op]=="hag31") {
	page_header("Die Wetterhexe");

	if(e_rand(1,4) == 1) {
		output("`tDie Hexe macht einige klägliche Gesten in Richtung Himmel, kann jedoch keinerlei Änderung herbeiführen. Offenbar peinlich berührt, vor dir versagt zu haben, fährt sie dich wütend an:`n");
		output("\"`RWas schaust du da noch so dumm? Sonst klappt das immer.. außer.. `t\" bei diesen Worten streckt sie dir anklagend ihre knorrigen Finger entgegen, \"`R.. DU bist schuld! Hast meine.. ähm.. Aura durcheinandergebracht, du Auswuchs des Bösen! RAUS MIT DIR!`t\"`n"); 
		output("Plötzlich findest du dich vor ihrer Tür wieder, auf dem Boden liegend. Dein Kopf brummt, als wäre ein Blitz hineingefahren.. und bietet auch einen dementsprechenden Anblick.`n `4Du hast fast alle Lebenspunkte und einen Charmepunkt verloren!");
		$session['user']['hitpoints'] = 1;
		addnav("Zurück","thepath.php");
	}
		
	else {

		$choice=(int)$_GET['what'];
		
		$w = set_weather($choice);
		
		output('`t"`^'.$w['name'].'`R, so wie du es wolltest. Wenn du mehr willst komm einfach wieder!`t"');
		$news="`@Es gab einen plözlichen Wetterumschwung!";
		$sql = "INSERT INTO news(newstext,newsdate,accountid) VALUES ('".addslashes($news)."',NOW(),0)";
		db_query($sql) or die(sql_error($sql));
		addnav("Zurück","thepath.php");
	}
}

if ($_GET[op]=="hag4") {
page_header("Die Wetterhexe");
 if ((($session['user']['hitpoints'])<($session['user']['maxhitpoints']/2)) || ($session['user']['turns']<1)) {
   output("`tDie Alte schaut dich an, nachdem du deinen Wunsch geäußert hast, und bricht in schallendem Gelächter aus.`n\"`RWas? Du ? Du würdest in deinem jetzigen Zustand nicht eine Minute überleben!`t\" schreit sie.`nDu hast das ungute Gefühl gerade rausgeworfen worden zu zun.");
   addnav("Zurück","thepath.php");
 } else {
   output ("Die Alte kichert leise und schaut dich an. \"`RDachte ich es mir doch... Nun, ich will es dir nicht allzu schwierig machen...`t\"`nWährend sie das sagt fährt sie sich mit den Handflächen über ihr Gesicht, und auf einmal sitzt keine uralte Frau mehr, sondern ein junges, bildhübsches Mädchen vor dir. Dann fällt sie über dich her...`nNach etwa einer Stunde krabbelst du auf allen Vieren aus der kleinen Kammer, zerkratzt, zerbissen und völlig erschöpft. Das schallende Lachen der Hexe verfolgt dich noch eine ganze Weile. Du hast fast alle deine Lebenspunkte verloren, aber einiges an Erfahrungen gewonnen was gewisse Bereiche des Lebens betrifft. `n`^Dein Charme erhöht sich um 1.`t`n");
$session['user']['hitpoints']=1;
$session['user']['charm']+=1;
$session['user']['turns']-=1;
addnews("`@".$session['user']['name']."`t hatte einen Quicky mit einer Hexe!");
addnav("Davonkriechen","thepath.php");
 }
}
if ($_GET[op]=="imp") {
page_header("Der Koboldspitzel");
output("`tWeiter hinten in der Feste, in einem dunklen Eck, glaubst du ein leises Rascheln zu vernehmen. Du näherst dich neugierig und schaust, wer sich da verborgen hält. Mutig greifst du ins Ungewisse und ziehst einen kreischenden Kobold hervor, der hilflos am Kragen gepackt wild hin und her zappelt. \"`@Garstiges Wesen, lass mich runter!`t\" quengelt das kleine Wesen und als du es vor dir auf den Boden setzt schaut es dich an. \"`@Wurde auch langsam Zeit. Da tut man keinem was zu Leide, hockt sich nur in eine Ecke um zu Lauschen und dann sowas!`t\".`nZu Lauschen... du überlegst kurz und schaust den Kobold mit verschlagenem Blick an. \"`@Oh nein, verplappert! Gut, ich weiß was du willst... weiß was du denkst... aber nur für `^1000 Goldmünzen`@ werde ich dir etwas verraten... mein letztes Wort!`t\"`nDu überlegst kurz und weißt dann was zu tun ist.`n`n");
addnav("1000 Gold hergeben","chosenfeats.php?op=imp2");
addnav("Kopfschüttelnd weggehen","thepath.php");
}
if ($_GET[op]=="imp2") {
page_header("Der Koboldspitzel");
 if ($session['user']['gold']<1000) {
   output("Du gibst dem Kobold das Gold, das du nicht hast, und er verrät dir Geheimnisse, die er nicht kennt. Du kommst dir dabei sehr, sehr dumm vor!`n");
   addnav("Zurück","thepath.php");
 } else {
output ("Du gibst dem Kobold 1000 Goldmünzen und er schaut dich erwartungsvoll an. Nach was willst du ihn fragen?`n");
addnav("Was treiben denn so...");
addnav("...die Trolle?","chosenfeats.php?op=imp3&where=trollfeste");
addnav("...die Elfen?","chosenfeats.php?op=imp3&where=elfenhain");
addnav("...die Menschen?","chosenfeats.php?op=imp3&where=versammlungsraum");
addnav("...die Zwerge?","chosenfeats.php?op=imp3&where=zwergenbinge");
addnav("...die Echsen?","chosenfeats.php?op=imp3&where=echsensümpfe");
addnav("...die Dunkelelfen?","chosenfeats.php?op=imp3&where=finsterwald");
addnav("...die Goblins?","chosenfeats.php?op=imp3&where=goblinbau");
addnav("...die Orks?","chosenfeats.php?op=imp3&where=orkfeste");
addnav("...die Vampire?","chosenfeats.php?op=imp3&where=mausoleum");
addnav("...die Halblinge?","chosenfeats.php?op=imp3&where=huegelhaeuser");
addnav("...die Dämonen?","chosenfeats.php?op=imp3&where=schwefelquelle");
addnav("...die Schelme?","chosenfeats.php?op=imp3&where=schelmenraum");
addnav("... ach nee. Doch nicht.","thepath.php");
}
}
if ($_GET[op]=="imp3") {
page_header("Der Koboldspitzel");
$session['user']['gold']-=1000;
$where=$_GET[where];
viewcommentary($where,"O",50);
addnav("Zurück zur Feste","thepath.php");
}
if ($_GET[op]=="list") {
	addnav("Zurück zur Feste","thepath.php");
	
	page_header("Die Halle der Statuen");
	
	output("`&Du betrittst eine große geräumige Halle. Überall stehen Statuen, die mächtige Krieger darstellen. Sie sind aus purem Gold. Zu deiner Verwunderung erblickst du eine Statue, die dir wie aus dem Gesicht geschnitten scheint. Auf einer kleinen Plakette ist dein Name eingraviert. Was das wohl zu bedeuten hat? Neben deiner eigenen Statue erblickst du auch sie Ebenbilder anderer Krieger, die du aus dem Dorf kennst. Du schwelgst ein wenig in deinem Ruhm und schaust dir die Ein oder Andere Statue näher an.`n`n");
	output ("`c`bDie Auserwählten :`b`c`n");
	
	user_show_list(30,'marks>=31');
		
}
else

if ($_GET[op]=="dodo") {
page_header("Dodo, der Kuscheldämon");

$sql = "SELECT name,bufflist FROM accounts WHERE marks>30 ORDER BY level";
$result = db_query($sql) or die(db_error(LINK));
if (db_num_rows($result)) {
  $max = db_num_rows($result);
for($i=0;$i<$max;$i++){
$row = db_fetch_assoc($result);

if ($row['bufflist']) $row['bufflist']=unserialize($row['bufflist']);
if ($row['bufflist']['dodo']<>"") { $friend=$row['name'];
if ($row['bufflist']) $row['bufflist']=serialize($row['bufflist']);}
}
} 

if ($friend<>"") {

 if ($session['bufflist']['dodo']<>"") {
output ("`tDu hast Dodo doch schon im Schlepptau. Was willst du also hier?");
}

else if ($_GET[act]=="") {
output("`tDu kommst in die Kammer, doch Dodo ist nicht da. Stattdessen findest du einen Zettel mit der Aufschrift `n\"`5Dodo ist mit ".($friend)."`5 zum Spielen raus gegangen.`t\""); } else
{
output("`tDu beschäftigst dich gerade so schön mit Dodo und ihr versteht Euch immer besser. Doch dann kommt auf einmal ".($friend)."`t dazu. Dodo schaut glucksend auf.`n\"`5Halluu!`t\" ruft Dodo und stampft auf die Türe zu. Die Beiden verlassen Hand in Pranke die Feste und lassen dich ganz allein zurück. Toll...");
}
addnav("Zurück zur Feste","thepath.php");

} else

{
if ($_GET[dodo]=="") {$dodo=0;} else { $dodo=$_GET[dodo];}
if ($dodo>=150) { output("`tDodo : \"`5Dodo`t hat ".($session['user']['name'])." gaaaanz doll lieb!`t\"`n`n"); }
 if ($_GET[act]=="") {

output("`tDu durchschreitest eine etwas größere Holztüre, die mit Stahlbeschlägen verstärkt wurde. Einige der Beschläge weisen Ausbeulungen auf und das Holz ist an einigen Stellen gerissen oder gesplittert. Du glaubst zu erahnen was dich dorthinter erwartet.`n Mit klopfendem Herzen drückst du die Türe auf und gelangst in einen viereckigen, kahlen Raum.`nUnd dann erblickst du ihn :`5Dodo, der Kuscheldämon`t! Ein Ungetüm von sowohl 2 meter Größe als auch Breite. Das Geschöpft ist über und über mit purpurnem, langem, zotteligen Fell bedeckt und ein breites, kurzes Horn ragt aus seiner Stirn. Gerade als du reinkommst sitzt er in der Mitte des Raumes und kaut an einem hölzernen Pferd. Als er dich erblick lässt es sein Spielzeug fallen und schau dich mit einem lieben, treudoofen Blick an.`n`n\"`5Halluu, duuuuu ?\"`t erklingt es.`n`n");
addnav("Was nun?");
addnav("Angreifen","chosenfeats.php?op=dodo&act=attack&dodo=$dodo");
addnav("Knuddeln","chosenfeats.php?op=dodo&act=knuddel&dodo=$dodo");
addnav("Tanzen","chosenfeats.php?op=dodo&act=dance&dodo=$dodo");
addnav("Eine Geschichte erzählen","chosenfeats.php?op=dodo&act=tale&dodo=$dodo");
addnav("Spielen","chosenfeats.php?op=dodo&act=play&dodo=$dodo");
addnav("Kitzeln","chosenfeats.php?op=dodo&act=tinkle&dodo=$dodo");
if ($dodo>=200) { addnav("Dodo mitnehmen","chosenfeats.php?op=dodo&act=takeout&dodo=$dodo");}
addnav("Dodo allein lassen","thepath.php");
} else {
if ($_GET[act]=="attack") {
output ("`tMit einem donnernden Kampfschrei stürmst du auf das Ungetüm zu, deine Waffe hoch erhoben.`n");
switch(e_rand(1,5)){
case 1 :
case 2 :
output("`tDoch Dodo hüpft dir entgegen, kurz bevor du zuschlagen willst, und du prallst gegen seinen weichen Bauch und landest auf deinem Allerwertesten.`n\"`5Nochmaaaaal!`t\" ruft dir Dodo zu.`nDu hast durch den Sturz ein paar Lebenspunkte verloren.");
$dodo+=20;
(int)$session['user']['hitpoints']*=0.90;
break;
case 3 :
case 4 :
output("`tDoch Dodo empfängt dich mir geöffneten Armen, und bevor du dich versiehst hat er seine Arme schon um dich geschlungen und drückt dich bis du blau anläufst.`n\"`5Huuuuuu!`t\" quiekt Dodo vergnügt.`nDu hast ein paar Lebenspunkte verloren.");
$dodo+=30;
(int)$session['user']['hitpoints']*=0.8;
break;
case 5 :
output("`tDu triffst Dodo hart an der Schulter und der Dämon heult laut auf. Dann packt er dich, drückt dich zu Boden und setzt sich auf dich drauf. Du bist tot und verlierst 8% deiner Erfahrung!");
$session['user']['hitpoints']=0;
$session['user']['experience'] *= 0.92;
addnews("`@".$session['user']['name']."`& wurde von `5Dodo, dem Kuscheldämon`& zerquetscht!");
addnav("Nein!","shades.php");
break;
}
} else
if ($_GET[act]=="knuddel") {
output ("`tDu näherst dich mit einem Grinsen Dodo und kuschelst dich in sein warmes, flauschiges Fell.`n");
switch(e_rand(1,5)){
case 1 :
case 2 :
output("`tDodo grunzt vergnügt und knuddelt eine Weile mit dir.`n\"`5Ohhh, das war schöööön!`t\" quiekt Dodo.`n");
$dodo+=10;
break;
case 3 :
case 4 :
output("`tDoch Dodo scheint dich beim Knuddeln irgendwie gar nicht so recht wahrzunehmem. Und bevor du dich versiehst hat er dich mit einer unbedachten Bewegung fast erdrückt. Du verlierst ein paar Lebenspunkte.");
$dodo+=10;
(int)$session['user']['hitpoints']*=0.90;
break;
case 5 :
output("`tDodo weiß deine Knuddelei nicht so recht einzuordnen und stösst dich locker von sich weg. So locker dieser Stoß auch war, du prallst dennoch gegen die gegenüberliegende Wand und verlierst ein paar Lebenspunkte.");
(int)$session['user']['hitpoints']*=0.90;
$dodo-=10;
break;
}

} else
if ($_GET[act]=="dance") {
output ("`tDu beginnst eine einprägsame Melodie zu summen und ergreifst Dodos Hände, beginnst langsam mit ihm zu tanzen.`n");
switch(e_rand(1,5)){
case 1 :
case 2 :
output("`tDodo scheint das sehr zu gefallen, und er quiekt und grunzt vergnügt, während ihr euch immer schneller dreht. Lachend fallt ihr beide irgendwann um.`n");
$dodo+=20;
break;
case 3 :
case 4 :
output("`tDoch Dodo interprätiert dein Vorhaben irgendwie falsch und beginnt sich immer schneller auf der Stelle zu drehen, dich an den Händen greifend. Irgendwann rutschst du ihm aus den großen Pranken und wirst in die Ecke des Raumes geschleudert, in einen Haufen Puppten. Du verlierst ein paar Lebenspunkte.");
$dodo+=10;
(int)$session['user']['hitpoints']*=0.95;
break;
case 5 :
output("Dodo tanzt eine Weile mit dir, doch immer wieder verhaspelt er sich mit seinen Schritten, stolpert unbeholfen umher. Schließlich hat er das Tanzen satt, reißt sich los und setzt sich schmollend in eine Ecke.");
$dodo-=15;
break;
}

} else
if ($_GET[act]=="tale") {
output ("`tDu setzt dich vor Dodo auf den Boden und erzählst ihm eine überaus spannende Heldengeschichte von großen Bellerophontes.`n");
switch(e_rand(1,5)){
case 1 :
case 2 :
output("`tDodo lauscht dir ganz angespannt, und als du fertig bist schauen dich seine riesigen Kulleraugen bewundernd an.`n");
$dodo+=25;
break;
case 3 :
case 4 :
output("Bereits nach kurzer Zeit ist Dodo eingeschlafen, und als du ihn weckst schaut er dich an, als würde er dich nicht mehr wiedererkennen.");
$dodo-=30;
break;
case 5 :
output("Dodo lauscht gespannt und als du fertig bist hüpft er aufgescheucht in seinem Zimmer herum und ruft laut \"`5Super-Dodo!`t\" In seinem Heldeneifer schubst er dich übermütig durch den Raum. Zuviel für deinen zarten Körper, du verlierst ein paar Lebenspunkt.");
$dodo+=10;
(int)$session['user']['hitpoints']*=0.9;
break;
}

} else
if ($_GET[act]=="play") {
output ("`tDu begibst dich zu dem großen Puppenhaufen in der Ecke des Raumes und nimmst eine der Puppen heraus. Dann setzt du dich vor Dodo und wackelst ein wenig mit der Puppe herum.`n");
switch(e_rand(1,5)){
case 1 :
case 2 :
output("`tDodo beugt sich zu der Puppe herab und bohrt seinen Finger in ihren Bauch. Ihr spielt eine Weile und habt jede Menge Spaß dabei.`n");
$dodo+=25;
break;
case 3 :
case 4 :
output("`tDodo schaut zuerst die Puppe, dann dich an. Dann schaut er wieder zu der Puppe und erneut zu dir. Wer macht sich hier gerade über wen lustig ?");
break;
case 5 :
output("Dodo wirft einen kurzen Blick auf die Puppe und schlägt mit seiner wuchtigen Faust darauf.\"`5Dich mochte ich noch nie!`t\" sagt er grollend. Leider traf er bei dem Schlag auch deine Hand und du verlierst ein paar Lebenspunkte.");
$dodo-=10;
(int)$session['user']['hitpoints']*=0.95;
break;
}

} else
if ($_GET[act]=="tinkle") {
output ("`tDu schleichst dich an Dodo heran und kitzelst ihn einmal kräftig durch.`n");
switch(e_rand(1,5)){
case 1 :
case 2 :
output("`tDodo lacht und jappst und strampelt wie wild mit Armen und Beinen. Danach knuddelt ihr noch ein wenig und Dodo brummt vergnügt.`n");
$dodo+=30;
break;
case 3 :
case 4 :
output("`tDodo lacht und strampelt wie wild als du ihn kitzelst. Dabei bekommst du einen leichten versehentlichen Streifschlag ab und verlierst ein paar Lebenspunkte.");
$dodo+=30;
(int)$session['user']['hitpoints']*=0.90;
break;
case 5 :
output("Dodo wertet deine plötzliche Kitzelattacke als einen ernsten Angriff und schleudert dich wie wild durch den Raum. Er schlägt dich solange gegen Wände un Boden bis du das Bewusstsein verlierst. Dann setzt er sich auf dich drauf und zerquetscht dich. Du bist tot und verlierst 8% deiner Erfahrung!");
$session['user']['hitpoints']=0;
$session['user']['experience'] *= 0.92;
addnews("`@".$session['user']['name']."`& wurde von `5Dodo, dem Kuscheldämon`& zerquetscht!");
addnav("Nein!","shades.php");
break;
}

} else
if ($_GET[act]=="takeout") {
output ("`tDu schaust Dodo an und fragst ihn : \"`&Dodo, willst du nicht mit nach draussen kommen und da mit mir weiter spielen?`t\"`n`n");
output ("`tDodo lacht laut und tappst dir hinterher...`n");
$dodobuff = array("name"=>"`5Dodo, der Kuscheldämon`!","rounds"=>100,"wearoff"=>"`5Dodo muss nach Hause!`0","atkmod"=>1.5,"roundmsg"=>"`5Dodo, der Kuscheldämon, packt deinen Gegner und würgt und schüttelt ihn!`!","activate"=>"offense");
$session['bufflist']['dodo']=$dodobuff;
addnav("Zurück","thepath.php");
}
if (($session['user']['hitpoints']>0) && ($session['bufflist']['dodo']=="")) {
addnav("Was nun?");
addnav("Angreifen","chosenfeats.php?op=dodo&act=attack&dodo=$dodo");
addnav("Knuddeln","chosenfeats.php?op=dodo&act=knuddel&dodo=$dodo");
addnav("Tanzen","chosenfeats.php?op=dodo&act=dance&dodo=$dodo");
addnav("Eine Geschichte erzählen","chosenfeats.php?op=dodo&act=tale&dodo=$dodo");
addnav("Spielen","chosenfeats.php?op=dodo&act=play&dodo=$dodo");
addnav("Kitzeln","chosenfeats.php?op=dodo&act=tinkle&dodo=$dodo");
if (($dodo>=200) || (su_check(SU_RIGHT_DEBUG))) { addnav("Dodo mitnehmen","chosenfeats.php?op=dodo&act=takeout&dodo=$dodo");}
addnav("Dodo allein lassen","thepath.php"); }
}
}
}

page_footer();
?>
