<?php

// 21072004

require_once 'common.php';

$w = get_weather();

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
if(($row['acctid1']==$session['user']['acctid'] && $row['turn']==1) || ($row['acctid2']==$session['user']['acctid'] && $row['turn']==2))
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
addnav('');
addnav('W?Wald','forest.php');
addnav('o?Wohnviertel','houses.php');
addnav('M?Marktplatz','market.php');

if (($session['user']['superuser']>0) || ($session['user']['expedition']>0)) 
{
	addnav('Expedition','expedition.php');
}
addnav('G?Gildenviertel','dg_main.php');
addnav('Klingengasse');
addnav('T?Trainingslager','train.php');
addnav("c?Warchilds Akademie","academy.php");
if (getsetting('pvp',1))
{
	addnav('A?Die Arena','pvparena.php');
}
addnav('K?Der Kerker','prison.php');

addnav('Tavernenstrasse');
addnav('E?Schenke zum Eberkopf','inn.php',true);
addnav('J?Jägerhütte','lodge.php');
addnav('r?Der Garten', 'gardens.php');
addnav('F?Seltsamer Felsen', 'rock.php');
//if ($session['user']['superuser']>1) addnav('Brunnenmonster','villageevents.php?event=2');

addnav('Abenteurergasse');
addnav('V?Verlassenes Schloss','abandoncastle.php',true);
addnav('s?Waldsee','pool.php');
addnav('-?Dorfamt','dorfamt.php');
//Adding the Villageparty
if(getsetting ('lastparty',0)>time())
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

if ($session['user']['superuser']>0)
{
	addnav('Admin');
	
	addnav('X?`bAdmin Grotte`b','superuser.php');
	
	if (su_check(SU_RIGHT_DEV)) {
		if (@file_exists('test.php'))
		{
			addnav('Test','test.php');
		}
	}
	if(su_check(SU_RIGHT_NEWDAY)) {
		addnav('Neuer Tag','newday.php');
	}

	addnav('Lemming spielen','superuser.php?op=iwilldie');
}
addnav('Logout');
addnav('#?In die Felder','login.php?op=logout',true);

page_header('Dorfplatz');
output('`@`c`bDorfplatz '.getsetting('townname','Atrahor').'s`b`c
Vor dir liegt der Dorfplatz: An seiner Nordseite grenzt er zwar direkt an den Wald, wird aber auch von großen Gebäuden umgeben. 
In alle Richtungen führen von verschiedensten Wesen bevölkerte Wege und Pfade, über die du zu anderen Orten und Häusern '.getsetting('townname','Atrahor').'s gelangst.
Unzählige Bänke bieten dir eine Gelegenheit zur Rast und in der Mitte des Platzes lädt ein Brunnen dazu ein, dich mit klarem Quellwasser zu erfrischen.`n
`^Ein Schild verbietet das Blankziehen von Waffen auf dem Dorfplatz unter Androhung von Kerkerhaft.`n
`@Ein ungewöhnlicher Felsen am Platzrand zeigt immer die neusten Geschehnisse im ganzen Dorf:');
$sql = "SELECT * FROM news ORDER BY newsid DESC LIMIT 1";
$result = db_query($sql) or die(db_error(LINK));
$row = db_fetch_assoc($result);
output('`n`n`c`i'.$row['newstext'].'`i`c`n');

switch(e_rand(1,1500))
{
	case 50 :
		//  case 51 :
		redirect('villageevents.php');
		break;
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
}
//Entfernt- diesen Satz fand ich ja nur dämlich
//output('`@Auf jeder Seite wird das Dorf von tiefem dunklem Wald umgeben.`n');

if (getsetting('activategamedate','0')==1) output('`@Wir schreiben den `^'.getgamedate().'`@ im Zeitalter des Drachen.`n');
output('`@Die magische Sonnenuhr zeigt `^'.getgametime().'`@. ');
output('`@Das heutige Wetter: `6'.$w['name'].'`@. ');

$sql = 'SELECT disciples.name AS name,disciples.level AS level ,accounts.name AS master FROM disciples LEFT JOIN accounts ON accounts.acctid=disciples.master WHERE best_one>0 LIMIT 1';
$result = db_query($sql) or die(db_error(LINK));
if (db_num_rows($result)>0) {
	$rowk = db_fetch_assoc($result);

	output("`n`n`@Eine kleine Statue ehrt `^".$rowk['name']."`@, einen Knappen der ".$rowk['level'].". Stufe, der zusammen mit ".$rowk['master']."`@ gegen den grünen Drachen auszog.");
}

output('`n`n`%`@In der Nähe reden einige Dorfbewohner:`n');
viewcommentary('village','Hinzufügen',25);
page_footer();
?>