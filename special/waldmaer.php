<?php
/*
* Datei: waldmaer.php
* Waldereignis f�r LotGD 0.9.7 - www.atrahor.de
* Version: 1.3 ||letzte �nderung:  04.06.2006
* Autor: Fossla (mithraskatze@web.de || ICQ: 270-812-802)
*/

// Aufrufen der Seite ohne eingeloggt zu sein wird verhindert
if (!isset($session))
{
	exit();
}

$session['user']['specialinc'] = 'waldmaer.php';

switch($_GET['op'])
{
	case 'naehern':
		$fall = e_rand(1,10);
		switch ($fall)
		{
			case 1:
			case 2:
			case 3:
			case 4:
			case 5:
			case 6:
				// Das M�dchen ist harmlos, f�r das Tr�sten gibt es Charmpunkte.
				output('`kDas M�dchen hat offensichtlich dein Mitleid geweckt und so trittst du n�her an die kleine heran. Du hockst dich freundlich vor sie und sprichst sie an.`n`n
... Nachdem du dich ein wenig mit dem M�dchen unterhalten hast und nun wei�t, dass sie sich verlaufen hatte, l�sst sie sich von dir den Weg zur�ck ins Dorf schildern und l�uft fr�hlich und dankbar von dannen.`n
Du f�hlst dich einfach super weil du dem M�dchen helfen konntest.`n`nF�r diese Tat bekommst du einen Charmpunkt.');
				$session['user']['charm']++;
				addnav('Zur�ck in den Wald','forest.php');
				$session['user']['specialinc'] = '';
				break;
			case 7:
			case 8:
			case 9:
			case 10:
				// Das M�dchen entpuppt sich als Monster welches nur seine Gestallt verborgen hatte um unwissende heran zu locken. Jetzt ist dir klar warum hier kein Monster weit und breit war.
				output('`kDas M�dchen hat offensichtlich dein Mitleid geweckt und so trittst du n�her an die kleine heran.`n`n
Sobald du nurnoch wenige Meter von dem Kind entfernt bist, hebt sie pl�tzlich ihren Blick und sieht dich direkt an - mit roten Augen! Die bisher menschliche Gestallt erhebt sich und ver�ndert sich auf schreckliche Weise in ein gro�es haariges Unget�m. Wie konntest du auf den Trick nur hereinfallen?`n`n');
				addnav('Auf in den Kampf!','forest.php?op=fighting&enemy=waldmaer');
				break;
		} // Ende der inneren Fallunterscheidung mit switch ob M�dchen harmlos oder nicht
		break;

	case 'gehen':
		output('`kDu machst dir keine weiteren Gedanken um das M�dchen und gehst einfach weiter in den Wald. Wenn du einfach alle Monster dort erledigst ist sie schlie�lich auch in Sicherheit! Ja, so ist es wohl das beste...`n`n');
		addnav('Weiter...','forest.php');
		$session['user']['specialinc'] = '';
		break;

	case 'fighting':
		$badguy = array(
		"creaturename" => 'Waldm�r',
		"creatureweapon" => 'gr�ssliche Erscheinung',
		"creaturelevel" => $session['user']['level']+1,
		"creatureattack" => $session['user']['attack']+1,
		"creaturedefense" => $session['user']['defence']+1,
		"creaturehealth" => $session['user']['maxhitpoints']
		);
		$gegner['enemy_waldmaer'] = createstring($badguy);

		$session['user']['badguy'] = $gegner[ 'enemy_'.$_GET['enemy'] ];
		$_GET['op']="fight";
		$battle = true;
		break;
	case 'fight':
		$battle=true;
		break;
	default:
		output('`kAuf deinem Weg durch den Wald h�rst du pl�tzlich ein leises Schluchtzen zu deiner linken Seite. Du wendest den Kopf und erblickst durch die Baumst�mme hindurch ein kleines Wesen welches zusammengekauert an einem Baumstamm lehnt.`n
Neugierig wie du bist trittst du n�her heran und erkennst, dass es sich bei dem kleinen Wesen scheinbar um ein junges M�dchen handelt. Ihr Gesicht in den H�nden verborgen sitzt sie dort auf dem Waldboden und weint h�rbar laut. Daher wunderst du dich, dass sie bisher noch keine Begegnung mit einem Monster gehabt zu haben scheint. Gl�ck f�r sie!`n`n');
		addnav('Gehe zu dem M�dchen','forest.php?op=naehern');
		addnav('Zur�ck in den Wald','forest.php?op=gehen');

} // Ende schwitch mit get "op" - umfasst allen Inhalt der ausgegeben wird

if($battle == true)
{
	include_once('battle.php');
	if ($victory)
	{
		addnav('Zur�ck in den Wald','forest.php');
		$session['user']['specialinc'] = '';
		$exp_plus = round($session['user']['experience'] * 0.01);
		$session['user']['experience'] += $exp_plus;
		output('`n`kDu bekommst ' . $exp_plus . ' Erfahrungspunkte daf�r, dass du das M�dchen von seinem Fluch befreit hast!');
	}
	else if ($defeat)
	{
		addnews('' . $session['user']['name'] . '`k wurde zerfleischt im Wald gefunden.');
		killplayer(100,5,0,'',0);
		output('`n`kDie Bestie hat dich �berlistet.`nDu verlierst 5% deiner Erfahrung und all dein Gold!');
		addnav('Verdammt...','news.php');
		$session['user']['specialinc'] = '';
	}
	else
	{
		fightnav(true,true);
	}
}