<?php

/*_____________________________________________________________
|�berlebenskampf, Idee aus Tales of Symphonia               |
|von Lord Eliwood                                           |
|Kampf der reiter.php entnommen                             |
|Version 1.0                                                |
|Grundger�st und Fehler beheboben                           |
|Version 1.1                                                |
|Belohung nach Kampfende bei einem merkw�rdigen Mann        |
|mit zuf�lligen Folgen                                      |
|Version 1.2                                                |
|Bugs aus Version 1.1 behoben von Skye                      |
|Getestet und Funktioniert auf www.funnetix.de/game/        |
|___________________________________________________________|
*/

/**
*@desc Story erweitert/Fehler behoben von dragonslayer 
*@url http://lotgd.drachenserever.de
*@author Kolja Engelmann - Dragonslayer
*/


require_once "common.php";
page_header("Seltsame Lichtung");

/*if($session['user']['superuser']<1)
{
	output("Du kommst an eine Stelle im Wald an der eine Schranke steht und hinter der Du den Admin schwer arbeiten siehst. Da willst Du lieber nicht st�ren und gehst vorerst weiter.");
	addnav("Zur�ck zum Wald",'forest.php');
	page_footer();

}*/

$session['user']['specialinc']='cruxis.php';
if ($HTTP_GET_VARS['op']=="")
{
	$_SESSION['engel']=0;
	output("Es gibt Ger�chte. Ger�chte von Kreaturen- gefallenen Engeln, wie sie genannt werden, b�sartige Gesch�pfe, die sich dem Licht abwanden um sich die Welten untertan zu machen. Ein wenig abseits der Stadt sollen sie vereinzelt gesichtet worden sein, und ihre Opfer so gr��lich zugerichtet haben, dass sich selbst Ramius eckelte. \"`%Alles Quatsch`0\" wenn man dich fragen w�rde, Schauerm�rchen, die am Lafgerfeuer erz�hlt werden und das wirst Du beweisen!`n`n Als Du durch den Wald streifst bemerkst Du einen unirdischen Lichtschimmer!");
	addnav("Weiter","forest.php?op=go");

	$badguy = array(
	"creaturename"=>"`6Gefallener Engel`0"
	,"creaturelevel"=>16
	,"creatureweapon"=>"Obsidianklinge"
	,"creatureattack"=>40
	,"creaturedefense"=>30
	,"creaturehealth"=>1000
	,"diddamage"=>0);

	/*$userlevel=$session['user']['level'];
	$userattack=$session['user']['attack'];
	$userhealth=$session['user']['hitpoints'];
	$userdefense=$session['user']['defense'];
	$badguy['creaturelevel']+=$userlevel;
	$badguy['creatureattack']+=$userattack;
	$badguy['creaturehealth']+=$userhealth;
	$badguy['creaturedefense']+=$userdefense;
	$session['user']['badguy']=createstring($badguy);*/

	$session['user']['badguy']=createstring($badguy);
	$atkflux = e_rand(0,$session['user']['dragonkills']*2);
	$defflux = e_rand(0,($session['user']['dragonkills']*2-$atkflux));
	$hpflux = ($session['user']['dragonkills']*2 - ($atkflux+$defflux)) * 5;
	$badguy['creatureattack']+=$atkflux;
	$badguy['creaturedefense']+=$defflux;
	$badguy['creaturehealth']+=$hpflux;

}


if ($HTTP_GET_VARS['op']=="go")
{
	output("Du betrittst die Lichtung und siehst dich um. Pl�tzlich ist kein Laut mehr zu vernehmen, kein L�ftchen weht, keine Tierlaute sind zu h�ren und am Himmel rasen violette Wolken dahin. Es scheint, als sei dieser Platz der Welt drumherum entr�ckt. Die Haare in deinem Nacken richten sich auf, irgendetwas stimmt hier ganz und gar nicht. Gehetzt ziehst Du deine Waffe, blickst dich um und f�hrst erschrocken aber bereit herum, als Du aus dem Augenwinkel eine Bewegung ausmachen kannst.`n Aus dem Schatten eines knorrigen Baumes l�st sich lautlos eine Gestalt, in einen dunklen Umhang mit tiefer Kapuze geh�llt.`n Nein...das ist kein Umhang, das..das sind Fl�gel, rabenschwarze Fl�gel �ber rabenschwarzer Haut! Verdammt es ist wahr! Die Gefallenen existieren tats�chlich!`n`n Gewappnet greifst Du den Griff Deiner Waffe fester.`nEinen moment lang steht Ihr Euch gegen�ber und dir scheint als s�hest Du in dem nach unten gekehrten Gesicht des Engels ein d�monisches L�cheln aufblitzen, als der Engel langsam seine Fl�gel zu entfalten beginnt und Dir sein dunkel gleissendes Schwert enth�llt.");

	if($session['user']['maxhitpoints']>2000)
	{
		addnav("K�mpfe","forest.php?op=fight");
	}
	else
	{
		output("Nein, daf�r f�hlst Du Dich nicht gewachsen, hier m�ssen richtige Helden ran, das sp�rst Du genau!!! Du machst auf dem Absatz kehrt und fl�chtest in den Wald-und das ist auch besser so!");
	}
	addnav("Fl�chte in Furcht","forest.php?op=flee1");
}

if ($HTTP_GET_VARS['op']=="flee1")
{
$session['user']['specialinc']='';
redirect("forest.php");
}

if ($HTTP_GET_VARS['op']=="run")
{
	output("\"`%Das wagst Du nicht!!!`0\"`n");
	$HTTP_GET_VARS['op']="fight";
}

if ($HTTP_GET_VARS['op']=="fight")
{
	$battle=true;
}

if ($battle)
{
	include ("battle.php");
	if ($victory)
	{
		output("`nDu hast `^".$badguy['creaturename']." geschlagen.");
		$badguy=array();
		$session['user']['badguy']="";
		$_SESSION['engel']+=1;
	}

	elseif($defeat)
	{
		output("Du wurdest durch die Hand eines gefallenen Engels get�tet, niemand anderes war Zeuge dieses ungleichen Kampfes au�er Ramius, dessen Fratze Dein Bewusstsein zu f�llen scheint");
		output("`n`4Du bist tot.`n");
		output("Du verlierst 10% deiner Erfahrung und all Dein Gold.`n");
		$session['user']['gold']=0;
		$session['user']['experience']=round($session['user']['experience']*.9,0); $session['user']['alive']=false;
		$session['user']['hitpoints']=0;
		$session['user']['specialinc']="";
		$session['user']['reputation']--;
		addnav("T�gliche News","news.php");
	}
	else
	{
		fightnav();
	}
}
if($_SESSION['engel']==1){
	$_SESSION['engel']+=1;
	addnav("Weiter","forest.php?op=goa");
}
if($_SESSION['engel']==3){
	$_SESSION['engel']+=1;
	addnav("Weiter","forest.php?op=end");
}
if($_SESSION['engel']==5){
	$_SESSION['engel']+=1;
	addnav("Weiter","forest.php?op=vil");
}

if ($HTTP_GET_VARS['op']=="goa")
{
	output("Als der Engel zu Boden sinkt, geht ein Ruck durch den Wald und es scheint, als w�rde sich ein Vorhang l�ften, der den Blick in ein Nichts frei gibt, wo sich eben noch ein St�ck des Waldes befand. Du verstehst und trittst n�her: Der Engel war nur ein Torw�chter...und dies ist ein Weg in eine andere Wirklichkeit.`n Als Du durch den Spalt trittst scheint sich dein weltliches Ich zu l�sen. Du sp�rst ein ziehen an deinen F�ssen und dir wird Schwarz vor Augen.`n`n");
	addnav("Weiter","forest.php?op=gob");
}
if ($HTTP_GET_VARS['op']=="gob")
{
	output("In deinem Kopf rasen k�rperlose Stimmen \"`%Wo bist Du?`0\",\"`%Was ist passiert?`0\", \"`%Wer ist dies?`0\",\"`%Weiss er nicht wo er sich befindet?`0\" umgeben von einer unbekannten Bosheit und Falschheit. Dir wird mit einem Male bewusst, dass Du in einem beinahe leeren Raum stehst. Verwundert siehst Du dich um. Dir ist, als w�rden dir Deine Augen Streiche spielen, denn in den Augenwinkeln kannst Du grausame Fratzen erkennen, die dich neugierig be�ugen und umwabern. Wendest Du ihnen jedoch den Blick zu, siehst Du nichts als der Dunkelheit selbst. Dich fr�stelt, die Luft ist kalt und d�nn. Das macht diesen Ort nicht gerade freundlicher, aber wenigstens sp�rst Du einen Luftzug und weisst, dass Du am Leben bist und einen Weg hier heraus finden kannst.");
	addnav("Weiter","forest.php?op=goc");
}
if ($HTTP_GET_VARS['op']=="goc")
{
	page_header("Die Dunkelheit");
	output("Du bewegst Dich vorsichtig durch die Schw�rze, die Dich umgibt. Endlos, zeitlos! -\"`%Wo will er hin?`0\", \"`%Er sieht so verloren aus!`0\"- St�ndig wirst Du begleitet durch den Schrecken in deinen Augenwinkeln und den Stimmen in Deinem Kopf. Nerv�s blickst Du Dich um, nur um Dir erneut Dein Unverm�gen etwas zu sehen oder zu h�ren einzugestehen.`nOh Gott, diese Stimmen...diese Leere, sie machen Dich noch ganz verr�ckt. -\"`%Es gibt keinen Ausgang, nur den Tod!`0\"- Sie nagen an Deinen Nerven! Du m�chtest ihnen entkommen, entfliehen! -\"`%Niemals!`0\"- Da, ein Licht! Ein Ausgang?`n Als sich Deinen Sinnen endlich ein Schimmer der Hoffnung offenbahrt, ist es um Deine Selbstkontrolle geschehen. Held hin oder her, Du rennst auf diesen Strohhalm Deiner Wahrnehmung zu!`n");	
	switch(e_rand(1,3))
	{
		case 1:
		
		output("Pl�tzlich, v�llig unerwartet, prallst du gegen ein k�rperloses Etwas. Schlimm genug, dass der Aufprall ziemlich heftig wahr, jetzt treten Dir auch noch die Tr�nen in die Augen, so dass Du Dich kurz setzen musst.`n");
		$session['user']['hitpoints']*=0.9;
		$session['user']['experience']*=1.01;
		
		break;
		case 2:
		output("Gerade noch rechtzeitig erkennst Du ein Hindernis in Deinem Weg und kommst schlitternd und rutschend zum stehen. Keuchend kniest Du kurz nieder, um Dich zu erholen`n");
		$session['user']['experience']*=1.01;
		
		break;
		case 3:
		output("Mit dem Fuss st�sst Du gegen etwas kleines! Fluchend kniest Du nieder, um nachzuforschen, was Du da eigentlich getreten hast. Das f�hlte sich ja fast an wie-ja, ein Heiltrank!`nDu trinkst diesen gierig, wer wei� was hier noch so passieren kann.`n");
		$session['user']['hitpoints'] = $session['user']['maxhitpoints'];
		
		break;
	}
	addnav("Durch die T�r","forest.php?op=god");
	output("Als du dich endlich wieder aufrichtest musst Du stutzen. Wollen Dich Deine Sinne L�gen strafen?`nDas Dunkel ist verschwunden, genau wie die Stimmen und die Furcht in Deinem Bewusstsein! Du stehst in einer Art leeren Kellerkammer mit einer verschlossenen alten T�r, die vom Schein einer Fackel erhellt wird und deren W�nde ru�geschw�rzt sind. Du betastest Die W�nde und ziehst die modrige Luft durch die Nase ein. Ja, es ist real! Du stehst tats�chlich hier und kannst Durch diese T�r treten, was Dir ,ganz ehrlich, im Moment auch sehr lieb ist.");
}
if ($HTTP_GET_VARS['op']=="god")
{
	page_header("Die Halle");
	output("Gl�ck gehabt, die T�r ist nicht verschlossen, knarrend schwingt sie nach innen auf und gibt den Blick auf eine gro�e Halle frei, deren W�nde ebenfalls von Fackeln erhellt werden. Der marmorierte Boden der H�hle ist �ber und �ber mit schwarzen Federn und blutigen �berresten bedeckt und an den W�nden finden sich im Fackelschein gespenstisch beleuchtete Fresken und Wandteppiche mit seltsamen Ornamenten und Bildern. Sie alle handeln von gefl�gelte Wesen, die auf die Erde hernieder fahren und dort Leid �ber die Lebewesen bringen. Angewidert wendest Du Dich ab. Selbst f�r Dich sind die hier dargestellten Grausamkeiten zu viel und Du schw�rst bei Deinem Leben, dass Du alles in Deiner macht stehende tun wirst, um diesem Treiben ein Ende zu setzen.");
	addnav("weiter","forest.php?op=goe");
}
if ($HTTP_GET_VARS['op']=="goe")
{
	output("Theatralisch ziehst Du Deine Waffe, wohl mehr um Dir Mut als anderen Angst einzujagen und beginnst auf die andere Seite der Halle zuzulaufen, wo Du in der Ferne eine Art Gang vermutest. Irgendwo hier muss das Machtzentrum dieser Wesen sein!`n\"`%Da ist er ja wieder!`0\",\"`%Was tut er nur?`0\", \"`%Armer Narr!`0\" - Die Stimmen! Beinahe vergessen sind sie pl�tzlich wieder da! Du erstarrst und die Schrecken der Dunkelheit kriechen erneut aus deinem Unterbewusstsein hervor. Aus den Augenwinkeln siehst Du die Schatten, die dir den Verstand rauben wollen. Diesmal jedoch bleiben es keine Schatten. Aus den finsteren Passagen der Halle treten lautlos mehr und mehr dunkle Gestalten auf Dich zu. Du f�hlst Dich unwillk�rlich an den Torw�chter erinnert. Was willst Du nun tun?");
	addnav("Wappne Dich","forest.php?specialinc=cruxis.php&op=f2");
	addnav("Fl�chte","forest.php?op=tot1");
}
if ($HTTP_GET_VARS['op']=="f2")
{
	output("Langsam und bedrohlich schlie�en die Kreaturen zu Dir auf und formieren sich in einem beinahe undurchdringlicher Kreis aus Fl�geln und K�rpern. Schatten auf ihren Gesichtern, den Blick gesenkt und v�llig lautlos verharren sie, so wie auch Du verharrst. Schwer atmest du in die unnat�rliche Stille und drehst Dich gerade noch rechtzeitig, um einer der d�monischen Fratzen direkt in das Gesicht zu starren, die nur wenige Schritte von Dir entfernt steht. Einige Augenblicke lang fesseln Dich die weissen, allwissenden Augen, die dich zu durchbohren scheinen. Du denkst an Daheim, an deine Freunde, an eine Wiese im Morgentau, das Rauschen des Windes in den Baumkronen des Waldes... \"`$ NEIN! `0\"Gerade noch rechtzeitig sch�ttelst Du diese Hypnose von Dir und h�rst aus den tiefen Deines Kopfes eine Stimme der Stimmen zu Dir sprechen`n\"`%Wer bist du nur, der du es wagst uns herauszufordern? In diesen geheiligten Hallen? Auf unserem Boden? Du musst wahrlich ein Narr sein, eine Seele die unserer nicht wert ist. Ziehe deine Waffe und beweise uns, dass Du wenigstens in Ehre stirbst!`0\"");
	addnav("K�mpfe","forest.php?op=fight");

	$badguy = array(
	"creaturename"=>"`6Unheiliger Engel`0"
	,"creaturelevel"=>17
	,"creatureweapon"=>"Unheilige Klinge"
	,"creatureattack"=>40
	,"creaturedefense"=>30
	,"creaturehealth"=>2000
	,"diddamage"=>0);

	/*$userlevel=$session['user']['level'];
	$userattack=$session['user']['attack'];
	$userhealth=$session['user']['hitpoints'];
	$userdefense=$session['user']['defense'];
	$badguy['creaturelevel']+=$userlevel;
	$badguy['creatureattack']+=$userattack;
	$badguy['creaturehealth']+=$userhealth;
	$badguy['creaturedefense']+=$userdefense;
	$session['user']['badguy']=createstring($badguy);*/

	$session['user']['badguy']=createstring($badguy);
	$atkflux = e_rand(0,$session['user']['dragonkills']*2);
	$defflux = e_rand(0,($session['user']['dragonkills']*2-$atkflux));
	$hpflux = ($session['user']['dragonkills']*2 - ($atkflux+$defflux)) * 5;
	$badguy['creatureattack']+=$atkflux;
	$badguy['creaturedefense']+=$defflux;
	$badguy['creaturehealth']+=$hpflux;

}

if ($HTTP_GET_VARS['op']=="tot1")
{
	addnav("T�gliche News","news.php");
	output("OH NEIN! So hast Du Dir das nicht vorgestellt. Du machst auf dem Absatz kehrt und rennst um dein Leben. So sehr Du Dich jedoch auch bem�hst, die fliegenden Unget�me sind schneller. Bald schon beginnst Du zu erlahmen und sp�rst Du die ersten Fl�gelschl�ge in Deinem Nacken. Was nun noch folgt ist ein weiterer widerlicher Akt der grausamen Kreaturen. Du bist tot! Und wieder einmal l�chelt Ramius auf Dich herab!`n");
	$session['user']['gold']=0;
	$session['user']['experience']*=0.8;
	$session['user']['alive']=false;
	$session['user']['specialinc']='';
}

if ($HTTP_GET_VARS['op']=="end")
{
	output("<embed src=\"media/mts.mid\" width=10 height=10 autostart=true loop=false hidden=true volume=100>",true);
	
	output("Als Du den Engel zu Boden streckst, erf�llt sein gellender Schrei die Hallen. Du h�ltst Dir die Ohren und kneifst Die Augen zusammen, damit das Gef�hl als Platze dein Kopf nicht �berhand nimmt. Als der Schrei verklingt, du Augen und Ohren wieder erlaubst die Umgebung wahrzunehmen, stehst Du allein. Allein in einem Thronsaal! Der Boden ist gefliest, aus den Fugen schlagen kleine Flammen und lecken nach Deinen F��en. Was dich jedoch viel mehr interessiert, ist der gro�e Thron, der gespenstisch im roten Schein des Feuers flackert.`nAuf ihm sitzt majest�tisch eine Gestalt, die dich selbst im Sitzen noch weit �berragt. Seine Fl�gel sind zu voller Spannweite ausgebreitet und auf seinem Schoss liegt ein gl�hendes Schwert. Dies muss der der Anf�hrer dieser Wesen sein und als Du Dich umsiehst und die vielen Gebeine gefallener Krieger entdeckst f�hlst Du Dich an Deinen Schwur erinnert: Diese Missgeburt muss fallen!");
	
	addnav("K�mpfe","forest.php?op=fight");
	addnav("Stirb lieber","forest.php?op=tot2");

	$badguy = array(
	"creaturename"=>"`6Unheilige Kraft`0"
	,"creaturelevel"=>20
	,"creatureweapon"=>"Judgement"
	,"creatureattack"=>60
	,"creaturedefense"=>50
	,"creaturehealth"=>2500
	,"diddamage"=>0);

	/*$userlevel=$session['user']['level'];
	$userattack=$session['user']['attack'];
	$userhealth=$session['user']['hitpoints'];
	$userdefense=$session['user']['defense'];
	$badguy['creaturelevel']+=$userlevel;
	$badguy['creatureattack']+=$userattack;
	$badguy['creaturehealth']+=$userhealth;
	$badguy['creaturedefense']+=$userdefense;
	$session['user']['badguy']=createstring($badguy);*/

	$session['user']['badguy']=createstring($badguy);
	$atkflux = e_rand(0,$session['user']['dragonkills']*2);
	$defflux = e_rand(0,($session['user']['dragonkills']*2-$atkflux));
	$hpflux = ($session['user']['dragonkills']*2 - ($atkflux+$defflux)) * 5;
	$badguy['creatureattack']+=$atkflux;
	$badguy['creaturedefense']+=$defflux;
	$badguy['creaturehealth']+=$hpflux;
}

if ($HTTP_GET_VARS['op']=="tot2")
{
	addnav("T�gliche News","news.php");
	output("Schwur hin oder her, Du bist zwar ein Held, aber nicht lebensm�de ,soll doch jemand anders diese Biester zur Raison bringen. Du kehrst der merkw�rdigen Person den R�cken und fliehst, so schnell dich deine Beine tragen, doch du hast dich zu fr�h gefreut.`n`n\"`%Judgement!`0\", erschallt eine unmenschliche Stimme hinter Dir und ohne weiteres wirst du von Energie aus reinem Licht niedergerissen.`nWas folgt ist nur die Dunkelheit, denn Du bist tot!");
	$session['user']['gold']=0;
	$session['user']['experience']*=0.8;
	$session['user']['alive']=false;
	$session['user']['specialinc']='';
}

if ($HTTP_GET_VARS['op']=="vil")
{
	$_SESSION['engel']=0;
	output("Mit einem letzten Kraftakt schwingst Du Deine Waffe und triffst das schwer angeschlagende Gesch�pf der Nacht an der Schl�fe. Kaum ber�hrt deine Klinge das unheilige Fleisch durchzuckt ein Blitz Deinen K�rper und in einem Strahl aus gleissendem Licht l�st sich die Kreatur auf.`n
	Deine Beine zittern vor Ersch�pfung und Du gibst der Versuchung nach dich niederzuknien und auszuruhen. Dein Geist und Dein K�rper sind geschunden, Dein Blick getr�bt, aber Du hast es geschafft! Du hast der Welt einen gro�en Gefallen getan, indem Du diese Wesen vorerst gestoppt hast...wenn Du doch nur nicht so m�de w�rst-so unendlich m�de... Als die Welt um Dich herum unter dem Schleier des Schlafes verschwindet, sinkst Du in einen tauben und traumlosen Schlaf.`n
	Viel sp�ter erst und nur ganz langsam beginnen einzelne Farben wie Gr�n und Blau wieder Einzug in deinen Geist zu halten. Bist Du tot? Oder bist Du am Leben? Wo bst Du? Du weisst es, als Du die Augen �ffnest. Du liegst auf einer Kreuzung im Wald und starrst in den blauen Himmel. Dein K�rper ist noch immer sehr geschunden, aber der helle Tag ist ein Labsaal f�r deine Seele nach der Zeit der langen Dunkelheit. Du kneiffst deine Augen zusammen und erkennst in einiger Entfernung das Dorf. Mit einem Seufzer beschliesst du, dass es jetzt erstmal Zeit ist Dich sinnlos zu betrinken und alles andere...sehen wir sp�ter weiter.`n");
	output("Stunden sp�ter und wohlig betrunken torkelst Du aus der Taverne. Der Mond scheint am Himmel udn eine Eule singt ihr einsames Lied. In Gedanken verloren biegst du auf dem Weg nach Hause, in eine Gasse ein, die dir g�nzlich unbekannt ist. Ein kalter Wind fegt durch die Gassen, und dir fr�stelt. Zwar setzt Du Deinen Weg fort, aber du sp�rst, dass hier etwas nicht stimmt...Pl�tzlich h�rst Du etwas. Eine alte, seltsam vetraute Stimme!`n");
	output("\"`%Ja, wen haben wird denn hier? Wenn das nicht der Held der Stadt ist. Komm doch einmal n�her-mein Freund`0\"`nWillenlos gehst Du n�her zu ihm und kannst durch deinen Alkoholschleier gerade noch seine Umrisse erkennen. Wenn Du nicht so betrunken w�rst k�nntest Du schw�ren in seiner Silhuette Fl�gel zu erkennen. Der alte legt Dir eine Hand auf die Schulter und ohne es richtig zu begreifen, gehst Du in die Knie...`n\"`%Jaa, du bist es tats�chlich`0\", h�rst du ihn sagen. \"`%Ich muss dir danken.`0\"\n\"`$F�r was denn?`0\", fragst du �ngstlich, die Hand an die Schl�fen gepresst.`n\"`%Du hast heute nicht nur der Welt, sondern auch mir einen `bgro�en`b Gefallen getan, mein Sohn.`0\"`n\"`$Sie... wissen von...`0\"`n\"`%Aber nat�rlich... Und daf�r bin ich dir sehr dankbar. Als Belohnung f�r deine ...nun...M�hen erlaube ich Dir die Wahl eines kleinen Geschenkes.`0\"");
	$session['user']['turns']=0;
	addnav("Eine kleine Schatulle","forest.php?op=b1");
	addnav("Eine mittlere Schatulle","forest.php?op=b2");
	addnav("Eine grosse Schatulle","forest.php?op=b3");
}

if ($HTTP_GET_VARS['op']=="b1")
{
	switch(e_rand(1,7))
	{
		case 1:
		case 2:
		output("Du entscheidest dich f�r die kleine Schatulle und �ffnest sie.`n");
		output("\"Ah ja,\", murmelt der Mann, \"du bekommst ein paar Donationpunkte. Viel Spass damit.\"`n`n");
		output("Mit diesen Worten l�sst sich der Mann auf und du brichst zusammen.");
		addnav("Du kommst wieder zu dir","houses.php");
		$session['user']['donation']+=25;
		break;
		case 3:
		case 4:
		case 5:
		output("Du entscheidest dich f�r die kleine Schatulle und �ffnest sie.`n");
		output("\"Ah ja,\", murmelt der Mann, \"du bekommst Gold. Kauf dir was sch�nes.\"`n`n");
		output("Mit diesen Worten l�sst sich der Mann auf und du brichst zusammen.");
		addnav("Du kommst wieder zu dir","houses.php");
		$session['user']['gold']+=1000;
		break;
		case 6:
		case 7:
		output("Du entscheidest dich f�r die kleine Schatulle und �ffnest sie.`n");
		output("\"Hahahaha,\", lacht der Mann und schl�gt dich nieder, \"Du bist mir auf den Leim gegangen.\"`n`n");
		output("Du brichst zusammen und merkst, als du wieder aufwachst, dass dir alles Gold gestohlen wurde.");
		addnav("Weiter","houses.php");
		$session['user']['gold']=0;
		$session['user']['experience']*=1.05;
		break;
	}
}
if ($HTTP_GET_VARS['op']=="b2")
{
	switch(e_rand(1,7))
	{
		case 1:
		output("Du entscheidest dich f�r die mittlere Schatulle und �ffnest sie. Im innern ist ein Fl�schchen, das du �ffnest und tinkst.`n");
		output("\"Hahahaha,\", lacht der Mann und schl�gt dich nieder, \"Du bist mir auf den Leim gegangen.\"`n`n");
		output("Du merkst, dass du schw�cher wie vorher bist und brichst dann bewusstlos zusammen");
		addnav("Du kommst wieder zu dir","houses.php");
		$session['user']['attack']-=5;
		break;
		case 2:
		output("Du entscheidest dich f�r die mittlere Schatulle und �ffnest sie. Im innern ist ein Fl�schchen, das du �ffnest und tinkst.`n");
		output("\"Ah, der Trank der St�rke,\", spricht der alte Mann, \"Dies ist eines meiner besten St�cke. Nun gut, jetzt bist du st�rker\"`n`n");
		addnav("Du kommst wieder zu dir","houses.php");
		$session['user']['attack']*=1.05;
		break;
		case 3:
		case 4:
		output("Du entscheidest dich f�r die mittlere Schatulle und �ffnest sie. Im innern ist ein St�ck Pergament.`n");
		output("\"Ah, der Gutschein der J�gerh�tte,\", seuzt der alte Mann, \"Du bekommst wohl oder �bel 100 Punkte gutgeschrieben.\"`n`n");
		addnav("Du kommst wieder zu dir","houses.php");
		$session['user']['donation']+=100;
		break;
		case 5:
		case 6:
		case 7:
		output("Du entscheidest dich f�r die mittlere Schatulle und �ffnest sie. Im innern ist ein St�ck Pergament.`n");
		output("\"Ah, das Papier der Edelsteine,\", seuzt der alte Mann, \"Du hast Gl�ck.\"`n`n");
		output("Du fragst dich, was das soll und brichst zusammen. Am n�chsten Tag wachst du auf und findest ein S�ckchen mit Edelsteine!");
		addnav("Du kommst wieder zu dir","houses.php");
		$session['user']['gems']+=10;
		break;
	}
}
if ($HTTP_GET_VARS['op']=="b3")
{
	switch(e_rand(1,7))
	{
		case 1:
		output("Du entscheidest dich f�r die gro�e Schatulle und �ffnest sie. Im innern ist ein Fl�schchen, das du �ffnest und tinkst.`n");
		output("\"Hahahaha,\", lacht der Mann und schl�gt dich nieder, \"Du bist mir auf den Leim gegangen.\"`n`n");
		addnav("Du kommst wieder zu dir","houses.php");
		$session['user']['defence']*=0.95;
		break;
		case 2:
		output("Du entscheidest dich f�r die gro�e Schatulle und �ffnest sie. Im innern ist ein Fl�schchen, das du �ffnest und tinkst.`n");
		output("\"Ah, der Trank des Schildes,\", spricht der alte Mann, \"Dies ist eines meiner besten St�cke. Nun gut, jetzt bist du st�rker\"`n`n");
		addnav("Du kommst wieder zu dir","houses.php");
		$session['user']['defence']+=10;
		break;
		case 3:
		case 4:
		output("Du entscheidest dich f�r die gro�e Schatulle und �ffnest sie. Im innern ist ein St�ck Pergament.`n");
		output("\"Ah, der Gutschein der J�gerh�tte,\", seuzt der alte Mann, \"Du bekommst wohl oder �berl 250 Punkte gutgeschrieben.\"`n`n");
		addnav("Du kommst wieder zu dir","houses.php");
		$session['user']['donation']+=250;
		break;
		case 5:
		case 6:
		case 7:
		output("Du entscheidest dich f�r die gro�e Schatulle und �ffnest sie. Im innern ist ein St�ck Pergament.`n");
		output("\"Ah, das Papier der Edelsteine,\", seuzt der alte Mann, \"Du hast Gl�ck.\"`n`n");
		addnav("Du kommst wieder zu dir","houses.php");
		$session['user']['gems']+=10;
		break;
	}
}
?>
