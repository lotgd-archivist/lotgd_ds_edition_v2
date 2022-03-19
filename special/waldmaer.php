<?php
/*
* Datei: waldmaer.php
* Waldereignis für LotGD 0.9.7 - www.atrahor.de
* Version: 1.3 ||letzte Änderung:  04.06.2006
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
				// Das Mädchen ist harmlos, für das Trösten gibt es Charmpunkte.
				output('`kDas Mädchen hat offensichtlich dein Mitleid geweckt und so trittst du näher an die kleine heran. Du hockst dich freundlich vor sie und sprichst sie an.`n`n
... Nachdem du dich ein wenig mit dem Mädchen unterhalten hast und nun weißt, dass sie sich verlaufen hatte, lässt sie sich von dir den Weg zurück ins Dorf schildern und läuft fröhlich und dankbar von dannen.`n
Du fühlst dich einfach super weil du dem Mädchen helfen konntest.`n`nFür diese Tat bekommst du einen Charmpunkt.');
				$session['user']['charm']++;
				addnav('Zurück in den Wald','forest.php');
				$session['user']['specialinc'] = '';
				break;
			case 7:
			case 8:
			case 9:
			case 10:
				// Das Mädchen entpuppt sich als Monster welches nur seine Gestallt verborgen hatte um unwissende heran zu locken. Jetzt ist dir klar warum hier kein Monster weit und breit war.
				output('`kDas Mädchen hat offensichtlich dein Mitleid geweckt und so trittst du näher an die kleine heran.`n`n
Sobald du nurnoch wenige Meter von dem Kind entfernt bist, hebt sie plötzlich ihren Blick und sieht dich direkt an - mit roten Augen! Die bisher menschliche Gestallt erhebt sich und verändert sich auf schreckliche Weise in ein großes haariges Ungetüm. Wie konntest du auf den Trick nur hereinfallen?`n`n');
				addnav('Auf in den Kampf!','forest.php?op=fighting&enemy=waldmaer');
				break;
		} // Ende der inneren Fallunterscheidung mit switch ob Mädchen harmlos oder nicht
		break;

	case 'gehen':
		output('`kDu machst dir keine weiteren Gedanken um das Mädchen und gehst einfach weiter in den Wald. Wenn du einfach alle Monster dort erledigst ist sie schließlich auch in Sicherheit! Ja, so ist es wohl das beste...`n`n');
		addnav('Weiter...','forest.php');
		$session['user']['specialinc'] = '';
		break;

	case 'fighting':
		$badguy = array(
		"creaturename" => 'Waldmär',
		"creatureweapon" => 'grässliche Erscheinung',
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
		output('`kAuf deinem Weg durch den Wald hörst du plötzlich ein leises Schluchtzen zu deiner linken Seite. Du wendest den Kopf und erblickst durch die Baumstämme hindurch ein kleines Wesen welches zusammengekauert an einem Baumstamm lehnt.`n
Neugierig wie du bist trittst du näher heran und erkennst, dass es sich bei dem kleinen Wesen scheinbar um ein junges Mädchen handelt. Ihr Gesicht in den Händen verborgen sitzt sie dort auf dem Waldboden und weint hörbar laut. Daher wunderst du dich, dass sie bisher noch keine Begegnung mit einem Monster gehabt zu haben scheint. Glück für sie!`n`n');
		addnav('Gehe zu dem Mädchen','forest.php?op=naehern');
		addnav('Zurück in den Wald','forest.php?op=gehen');

} // Ende schwitch mit get "op" - umfasst allen Inhalt der ausgegeben wird

if($battle == true)
{
	include_once('battle.php');
	if ($victory)
	{
		addnav('Zurück in den Wald','forest.php');
		$session['user']['specialinc'] = '';
		$exp_plus = round($session['user']['experience'] * 0.01);
		$session['user']['experience'] += $exp_plus;
		output('`n`kDu bekommst ' . $exp_plus . ' Erfahrungspunkte dafür, dass du das Mädchen von seinem Fluch befreit hast!');
	}
	else if ($defeat)
	{
		addnews('' . $session['user']['name'] . '`k wurde zerfleischt im Wald gefunden.');
		killplayer(100,5,0,'',0);
		output('`n`kDie Bestie hat dich überlistet.`nDu verlierst 5% deiner Erfahrung und all dein Gold!');
		addnav('Verdammt...','news.php');
		$session['user']['specialinc'] = '';
	}
	else
	{
		fightnav(true,true);
	}
}