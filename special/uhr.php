<?php

// Die Standuhr
// Gecodet von Ventus
// www.Elfen-Portal.de
// nach einer Idee von Magix_Lady

//Kleinere Tweaks und �nderungen von Dragonslayer f�r lotgd.drachenserver.de

switch ($_GET['op'])
{

	case 'rechts':
	output('`# Du drehst den Zeiger vorw�rts. Der Zeiger wandert �ber Sekunden, �ber Minuten und �ber Stunden in die Zukunft.`n
	Helles, intensives Licht umschlie�t dich. Als das Licht verschwindet, ');

	switch (e_rand(1,3))
	{
		case 1:
		output('`%f�hlst du dich �lter und weiser. ');
		$session['user']['experience']*=1.1;
		$session['user']['age']+=5;
		addnav('Zur�ck in den Wald','forest.php');
		break;
		case 2:
		output('`%bist du nur noch ein lebloser Leichnam, da du um 1000 Jahre gealtert bist. Das n�chste mal solltest du die Uhr nicht ganz soweit drehen...`n
		`$ Du bist tot!');
		$session['user']['alive']=false;
		$session['user']['hitpoints']=0;
		$session['user']['gold']=0;
		addnews($session['user']['name'].' hat eine t�dliche Zeitreise unternommen.');
		addnav('T�gliche News','news.php');
		break;
		case 3:
		output('f�hlst du dich �lter als du sein m�sstest. Du stinkst, als h�ttest du dich eine Woche lang nicht gewaschen');
		$session['user']['age']+=3;
		$session['user']['charm']-=2;
		addnews($session['user']['name'].' hat eine Zeitreise unternommen.');
		break;
	}
	break;

	case 'links':
	$rand = e_rand(1,2);
	output('`# Du drehst den Zeiger zur�ck. Der Zeiger wandert �ber Sekunden, �ber Minuten und �ber Stunden in die Vergangenheit.
 	Helles, intensives Licht umschlie�t dich. Als das Licht verschwindet, ');
	switch ($rand)
	{

		case 1:
		output('`% scheint die Sonne tief, als w�re es noch fr�h am Morgen. Du f�hlst dich auscgeschlafen, als k�nntest du diesen Tag nun ein weiteres mal erleben!');
		$session['user']['turns']+=8;
		addnav('Zur�ck in den Wald','forest.php');
		break;

		case 2:
		output('`%bildest du dich langsam zur�ck zum Kind. Am Ende bleibt nur deine Ausr�stung �brig, die mit einer seltsamen weissen Fl�ssigkeit klebriger Konsistenz verschmiert ist...`n
		`$ Du bist tot!');
		$session['user']['alive']=false;
		$session['user']['hitpoints']=0;
		addnews($session['user']['name'].' hat eine t�dliche Zeitreise unternommen.');
		addnav('T�gliche News','news.php');
		break;
	}
	break;

	case 'raus':
	$session['user']['specialinc']='';
	redirect('forest.php');
	break;

	default:
	output('`#Du betrittst eine Lichtung, in deren Mitte sich eine seltsam glitzernde,
	uralt ausssehende Standuhr befindet. Du beschlie�t, sie dir n�her anzusehen und entdeckst einen goldenen Spruch auf ihrem Sockel: `n
	`$ Seit Jahrtausenden steh ich hier,`n
	was du dir w�nscht, geben kann ichs dir.`n
	Dreh meine Zeiger, vor oder zur�ck,`n
	vielleicht hast du Gl�ck...`n
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