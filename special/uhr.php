<?php

// Die Standuhr
// Gecodet von Ventus
// www.Elfen-Portal.de
// nach einer Idee von Magix_Lady

//Kleinere Tweaks und Änderungen von Dragonslayer für lotgd.drachenserver.de

switch ($_GET['op'])
{

	case 'rechts':
	output('`# Du drehst den Zeiger vorwärts. Der Zeiger wandert über Sekunden, über Minuten und über Stunden in die Zukunft.`n
	Helles, intensives Licht umschließt dich. Als das Licht verschwindet, ');

	switch (e_rand(1,3))
	{
		case 1:
		output('`%fühlst du dich älter und weiser. ');
		$session['user']['experience']*=1.1;
		$session['user']['age']+=5;
		addnav('Zurück in den Wald','forest.php');
		break;
		case 2:
		output('`%bist du nur noch ein lebloser Leichnam, da du um 1000 Jahre gealtert bist. Das nächste mal solltest du die Uhr nicht ganz soweit drehen...`n
		`$ Du bist tot!');
		$session['user']['alive']=false;
		$session['user']['hitpoints']=0;
		$session['user']['gold']=0;
		addnews($session['user']['name'].' hat eine tödliche Zeitreise unternommen.');
		addnav('Tägliche News','news.php');
		break;
		case 3:
		output('fühlst du dich älter als du sein müsstest. Du stinkst, als hättest du dich eine Woche lang nicht gewaschen');
		$session['user']['age']+=3;
		$session['user']['charm']-=2;
		addnews($session['user']['name'].' hat eine Zeitreise unternommen.');
		break;
	}
	break;

	case 'links':
	$rand = e_rand(1,2);
	output('`# Du drehst den Zeiger zurück. Der Zeiger wandert über Sekunden, über Minuten und über Stunden in die Vergangenheit.
 	Helles, intensives Licht umschließt dich. Als das Licht verschwindet, ');
	switch ($rand)
	{

		case 1:
		output('`% scheint die Sonne tief, als wäre es noch früh am Morgen. Du fühlst dich auscgeschlafen, als könntest du diesen Tag nun ein weiteres mal erleben!');
		$session['user']['turns']+=8;
		addnav('Zurück in den Wald','forest.php');
		break;

		case 2:
		output('`%bildest du dich langsam zurück zum Kind. Am Ende bleibt nur deine Ausrüstung übrig, die mit einer seltsamen weissen Flüssigkeit klebriger Konsistenz verschmiert ist...`n
		`$ Du bist tot!');
		$session['user']['alive']=false;
		$session['user']['hitpoints']=0;
		addnews($session['user']['name'].' hat eine tödliche Zeitreise unternommen.');
		addnav('Tägliche News','news.php');
		break;
	}
	break;

	case 'raus':
	$session['user']['specialinc']='';
	redirect('forest.php');
	break;

	default:
	output('`#Du betrittst eine Lichtung, in deren Mitte sich eine seltsam glitzernde,
	uralt ausssehende Standuhr befindet. Du beschließt, sie dir näher anzusehen und entdeckst einen goldenen Spruch auf ihrem Sockel: `n
	`$ Seit Jahrtausenden steh ich hier,`n
	was du dir wünscht, geben kann ichs dir.`n
	Dreh meine Zeiger, vor oder zurück,`n
	vielleicht hast du Glück...`n
	Doch sei gewarnt!`n
	Drehst du falschherum,`n
	ist deine Zeit bald um!`n
	`%Was wirst du tun?');

	addnav('Dreh die Uhrzeiger nach rechts','forest.php?op=rechts');
	addnav('Dreh die Uhrzeiger nach links','forest.php?op=links');
	addnav('Renn um dein Leben!!','forest.php?op=raus');
	$session['user']['specialinc']='uhr.php';
	break;
}
?>