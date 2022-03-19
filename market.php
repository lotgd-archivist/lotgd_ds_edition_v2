<?php

// Splittet den Dorfplatz
// by Maris (Maraxxus@gmx.de)

require_once 'common.php';

$show_invent = true;

addcommentary();
checkday();

if ($session['user']['alive']==0)
{
    redirect('shades.php');
}

$sql='SELECT acctid1,acctid2,turn FROM pvp WHERE acctid1='.$session['user']['acctid'].' OR acctid2='.$session['user']['acctid'];
$result = db_query($sql) or die(db_error(LINK));
$row = db_fetch_assoc($result);
if (($row['acctid1']==$session['user']['acctid'] && $row['turn']==1) || ($row['acctid2']==$session['user']['acctid'] && $row['turn']==2))
{
    redirect('pvparena.php');
}

if (getsetting('automaster',1) && $session['user']['seenmaster']!=2)
{
    $expreqd = get_exp_required($session['user']['level'],$session['user']['dragonkills'],true);
    if ($session['user']['experience']>$expreqd && $session['user']['level']<15)
    {
        redirect('train.php?op=autochallenge');
    }
    else if ($session['user']['experience']>$expreqd && $session['user']['level']>=15 && e_rand(1,3) == 3 )
    {
        redirect('dragon.php?op=autochallenge');
    }
}
$session['user']['specialinc']='';
$session['user']['specialmisc']='';
addnav('W?Wald','forest.php');
addnav('o?Wohnviertel','houses.php');
addnav('D?Dorfplatz','village.php');
if ($session['user']['superuser']>0)
{
    addnav('Expedition','expedition.php');
}
addnav('G?Gildenviertel','dg_main.php');

addnav('Marktplatz');
if (getsetting("vendor",0)==1 || $session['user']['superuser'])
{
    addnav('h?Wanderhändler','vendor.php');
}
addnav('M?MightyEs Waffen','weapons.php');
addnav('P?Pegasus Rüstungen','armor.php');
addnav('S?Mericks Ställe','stables.php');
addnav('B?Die alte Bank','bank.php');
addnav('Z?Zigeunerzelt','gypsy.php');
addnav('Goldpartner','goldpartner.php');

// Schnapper Mod by Romulus
if ($_GET['op']!='schnapper')
{
    if (e_rand(1,10)<=3)
    {
        addnav('c?Schnapper, der Händler','schnapper.php');
    }
}

//Adding the Villageparty
if (getsetting('lastparty',0)>time())
{
    addnav('P?Das Dorffest','dorffest.php');
}

addnav('Information');
addnav('D?`^Drachenbücherei`0','library.php');
addnav('l?Einwohnerliste','list.php');
addnav('N?Neuigkeiten','news.php');

addnav('`bSonstiges`b');
if (getsetting('pvp',1))
{
    addnav('+?Spieler töten','pvp.php');
}

if($session['user']['superuser'] > 0) {
	addnav('X?`bAdmin Grotte`b','superuser.php');
	
	if(su_check(SU_RIGHT_NEWDAY)) {
		addnav('Neuer Tag','newday.php');
	}
}

addnav('Logout');
addnav('#?In die Felder','login.php?op=logout',true);

page_header('Marktplatz');
output('`@`c`bMarktplatz '.getsetting('townname','Atrahor').'s`b`cDie Bewohner sind damit beschäftigt sich bei den Verkaufsständen nach Waren umzusehen und Handel zu betreiben. Interessiert blickst auch Du Dich um. Du siehst Verkaufsstände und Einrichtungen entlang des Platzes.`n');
$sql = "SELECT * FROM news ORDER BY newsid DESC LIMIT 1";
$result = db_query($sql) or die(db_error(LINK));
$row = db_fetch_assoc($result);
output('`@An einem merkwürdig aussehenden Felsen kannst du die neueste Meldung lesen:`n`n`c`i'.$row['newstext'].'`i`c`n');

switch (e_rand(1,1500))
{
    //  case 50 :
    //  case 51 :
    //  redirect('villageevents.php');
    //  break;
case 100 :
case 101 :
    output('`n`^Du findest einen Edelstein vor dir auf dem Boden, den du natürlich sofort einsteckst!`n`n`@');
    $session['user']['gems']++;
    break;
case 150 :
case 151 :
case 152 :
    if ($session['user']['gold']>0)
    {
        output('`n`4Jemand rempelt dich an und entfernt sich unter wortreicher Entschuldigung rasch. Dann stellst du fest, dass man dir '.(int)($session['user']['gold']*0.15).' Gold gestohlen hat!`n`n`@');
    }
    (int)$session['user']['gold']*=0.85;
    break;
case 200 :
case 201 :
case 202 :
    if ($session['user']['turns']>0)
    {
        output('`n`^Jemand kommt dir gut gelaunt entgegen gelaufen und reicht dir ein Ale. Deine Laune bessert sich dadurch und du hast heute eine Runde mehr!`n`n`@');
        $session['user']['turns']++;
    }
    break;
case 250 :
case 251 :
    output('`n`4Jemand rennt eilig vor einer Stadtwache davon und stößt dich grob bei Seite, da du ihm im Weg stehst. Du stürzt und landest mit dem Gesicht in einem Kuhfladen. Leute drehen sich zu dir um und zeigen lachend auf dich. Du verlierst einen Charmepunkt!`@`n`n');
    $session['user']['charm']--;
    break;

default:
	if($session['user']['age'] == 1 && !$session['reloffered'] && e_rand(1,4) == 1) {	// Gerade Drachenkill gemacht
		// Überprüfen, ob noch nicht alle Reliquien vergeben, unser Freund noch keinen Steckbrief und auch noch kein Angebot bekommen hat
		if( item_count('tpl_id="drstb" AND owner='.$session['user']['acctid']) == 0 && item_count('tpl_id="drrel_ksn" OR tpl_id="drrel_gld"') < 2) {
			redirect('marketevents.php?op=rel');	
		}
	}
	
}

$w = get_weather();

if (getsetting('activategamedate','0')==1)
{
    output('`@Wir schreiben den `^'.getgamedate().'`@ im Zeitalter des Drachen.`n');
}
output('`@Die Uhr an einer großen Säule zeigt `^'.getgametime().'`@.');
output('`@Das heutige Wetter: `6'.$w['name'].'`@.');


// Die Mauer (by Maris)
$message=getsetting('wall_msg','0');
$time=getsetting('wall_chgtime','0');

$oldtime=(strtotime($time));
$acttime=(strtotime(date('H:i:s')));
$newtime=$acttime-$oldtime;
//Farbe bereits trocken ?

$wallchangetime=getsetting('wallchangetime','300');
//Zeit zwischen den Änderungen

output('`n`n`&Dein Blick fällt auf eine hüfthohe Mauer aus weißen Ziegeln. ');
if ($message=='0')
{
    output('Sie muss gerade frisch angestrichen worden sein.`n');
    if ($newtime>$wallchangetime)
    {
        output("<a href='whitewall.php?op=write'>Die Mauer beschmieren</a>");
        addnav('','whitewall.php?op=write');
    }
}
else
{
    output('Jemand hat Folgendes in großen Buchstaben darauf geschmiert:`n`^'.$message.'`n`0');
    if ($newtime>$wallchangetime)
    {
        output('<a href="whitewall.php?op=write">Überschmieren</a>');
        $author=getsetting('wall_author','0');
        if ($session['user']['login']!=$author)
        {
            output(' | ');
            output('<a href="whitewall.php?op=change">Verändern</a>');
            addnav('','whitewall.php?op=change');
        }
        addnav('','whitewall.php?op=write');
    }
}
if($newtime>7200) //Nach 2 Stunden wird die Mauer ne gestrichen (gelöscht)
{
    savesetting('wall_author','0');
    savesetting('wall_chgtime',date('Y-m-d H:i:s'));
    savesetting('wall_msg','0');
}
if ($_GET['op']=='toolate')
{
    if ($newtime<60)
    {
        output('`4Es muss dir jemand zuvor gekommen sein. Die Farbe ist zu feucht um jetzt überschrieben zu werden.`0`n');
    }
    else
    {
        redirect('market.php');
    }
}
// Mauer Ende

output('`n`n`%`@In der Nähe hörst du einige Leute schwatzen:`n');
viewcommentary('marketplace','Hinzufügen',25);
page_footer();
?>
