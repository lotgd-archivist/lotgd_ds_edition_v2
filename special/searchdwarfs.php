<?
/*
* Zwergenjagd - Dwarf-Hunting
* written by Warchild ( warchild@gmx.org )
* 4 www.lotgd.de
* ---
* Feel free to include this special but please ask the author before doing any modifications oder publications!
* Comments, Ideas welcome (email above).
* Thank you, regards
* Warchild
* ---
* 2/2004
* Version 0.91 ger
* Letzte Änderungen/last changes: translated by kK
* 20.03.2004-00:33-warchild     Wahrscheinlichkeiten geändert. Chance auf 3 gems ist jetzt niedriger
* 26.07.2004-14:36-warchild     Debuglog in comments. Feel free to change this if you like
*
*/

if ($HTTP_GET_VARS[op]=="")
{
    output("`#Du fühlst dich schläfrig als du durch den Wald wanderst und legst dich unter einen Baum. ");
	output("In deinem Traum siehst du ein paar Zwerge die um einen `7Riesenhaufen Edelsteine`# tanzen.");
	output("Sie lachen und singen! Plötzlich wachst du auf. `n");

	// Player is a dwarf
	if ((int)$session[user][race] == 4)
	{
		output("Du fühlst dich an zuhause erinnert und dir fällt ein, dass ein Freund von dir in der Nähe wohnt.");
		output("`n`n<a href='forest.php?op=friend'>Besuch ihn</a>`n<a href='forest.php?op=nofriend'>Weitergehen</a>",true);
		addnav("Besuch ihn","forest.php?op=friend");
		addnav("Weitergehen","forest.php?op=nofriend");
		addnav("","forest.php?op=friend");
		addnav("","forest.php?op=nofriend");
	}
	else
	{
		output("Wage erinnerst du dich an deinen Traum, du denkst: Wenn das stimmt, dann...!!!");
		output("`n`n<a href='forest.php?op=dwarf'>Auf Zwergenjagd gehen!</a>`n<a href='forest.php?op=nodwarf'>Faulenzen</a>",true);
		addnav("Auf Zwergenjagd gehen!","forest.php?op=dwarf");
		addnav("Faulenzen","forest.php?op=nodwarf");
		addnav("","forest.php?op=dwarf");
		addnav("","forest.php?op=nodwarf");
	}
	$session[user][specialinc]="searchdwarfs.php";
}
else
{
  $session[user][specialinc]="";
	if ($HTTP_GET_VARS[op]=="friend")
	{
	  $rand = e_rand(1,7);
		output("`n`#Du verlässt deinen Weg und gehst zur Höhle deines Freundes.");
		output("Du klopfst an die runde Tür und ");
		switch ($rand)
			{
			case 1:
			case 2:
			case 3:
				output("`idein guter alter Kumpel begrüßt dich herzlich indem er dir am Bart zieht, als Zeichen für eure Freundschaft.`i Zusammen unterhaltet ihr euch ");
				output("über eure guten alten Zeiten und trinkt und esst am Kaminfeuer. Als Du endlich beschliesst zu gehen fühlst du dich");
				output("erholt und fit genug für weitere Gefahren!");
				output("`n`n`^Du bekommst einen extra Waldkampf!`n");
				$session[user][turns]++;
				break;
			case 4:
			case 5:
				output("`ifindest das Haus verlassen vor.`i Achselzuckend gehst du wieder deiner Wege.");
				break;
			case 6:
				output("niemand antwortet dir. Vorsichtig öffnest du die Tür und siehst, auf einem Tisch, eine Nachricht.");
				output("`i`n\"An meinen Freund `7".$session[user][name]."`#. Leider musste ich den Wald verlassen, da ich ");
				output("den Gestank von Menschen einfach nicht mehr ertragen kann. Da ich nicht genug Zeit hatte, mich richtig zu verabschieden, nimm bitte ");
				output("dieses `7kleine Präsent`# damit du dich immer an mich erinnerst.\"`i");
				output("`n`#Als du gehst überkommt dich ein Gefühl des Stolzes, so einen guten Freund zu haben!");
				$session[user][gems]++;   
				output("`n`n`^Du erhälst 1 Edelstein!`n");
//				debuglog("got 1 gem from a friend");
				break;
			case 7:
				output("`iplötzlich kommt dein Freund aus der Tür 'rausgeflogen. Er steht auf, sieht dich und kommt torkelnd auf dich zu, um dich zu umarmen.`i Er lallt: \"Innen geht die Party ab, komm rein!\"`nDu verbringst");
				output("Stunden um Stunden mit tanzen und saufen. Irgendwann fällst du um und bleibst laut schnarchend liegen.");
				output("`n`7Man kann dein Schnarchen sogar im Dorf hören!");
				output("`n`n`^Du verlierst all deine heutigen Waldkämpfe.`n");
				$session[user][turns] = 0;
				$session[user][drunkenness]+=50;
				addnews("`^".$session[user][name]." `7feierte kräftig bei der Zwergenparty und schnarchte so laut, dass niemand im Dorf schlafen konnte!");
				break;
			}
	}
	else if ($HTTP_GET_VARS[op]=="dwarf")
	{
	  $rand = e_rand(1,15);
		output("`#Du verlässt deinen Weg und gehst in die nahen Hügel. Erstaunt findest du schon bald eine Höhle mit einer runden Tür. Voller Erwartung und Gier ");
		output("ziehst du deine Waffe und bereitest dich darauf vor einen kleinen Mann umzubringen damit du an seinen glitzernden Reichtum kommst!`n");
		output("Du trittst die Tür ein und siehst ");
		switch ($rand)
			{
			case 1:
			case 2:
			case 3:
				output("ein kleines Zwergenmädchen, das auf dem Fussboden sitzt und `7mit einem Edelstein spielt`#! Grinsend reißt du ");
				output("ihr den Stein aus den Händen und lässt ein schreiendes Mädchen zurück.");
				output("`n`n`^Du erhälst einen Edelstein!`n`n");
				$session[user][gems]++;
//				debuglog("got 1 gem from a dwarf");
				$rand2 = e_rand(1,2);
				switch ($rand2)
					{    
						case 1:  
						output("`#Vom Weinen angelockt, kommt ein `7wütender Zwergenvater `# angerannt, ");
						output("`igreift nach seiner Axt`i und stürzt sich auf dich. Von den Schreien seiner Tochter angestachelt, hast du keine Chance gegen ihn!`n");
						output("Du fliehst und dabei verlierst du deinen erbeuteten Edelstein wieder.");
						output("`n`n`^Du verlierst 1 Edelstein!`n");
						if ($session[user][gems]>0) $session[user][gems]--;
//						debuglog("lost 1 gem to a dwarf");
						break;
						case 2:  output("`#Lachend gehst du von dannen, nur die Schreie des Mädchens hallen noch in deinen Ohren...`n");
                        output("`7Doch plötzlich überkommt dich Mitleid mit dem Mädchen und du versuchst vergeblich dein Gewissen zu beruhigen.");
						output("`n`n`^Du verlierst 1 Charmpunkt!`n");
                        if ($session[user][charm]>0) $session[user][charm]--;
                        break;
					}
					break;
			case 4:
				output("einen grimmigen Zwerg an einem Tisch sitzen, auf dem `7ein paar Edelsteine`# liegen. Als er dich bemerkt ");
				output("greift er nach seiner `7riesigen Doppelaxt`# und versucht seinen Schatz zu verteidigen, aber nach einem erbitterten Kampf, den natürlich");
				output(" du gewinnst, flieht er. Als du mit deinen blutigen Händen nach den `i Edelsteinen`i grabschst, kommt dir ein fröhliches Kichern aus der Kehle!");
				output("`n`n`^Du erhälst 3 Edelsteine!`n");
				$session[user][gems]++;
				$session[user][gems]++;
				$session[user][gems]++;
//				debuglog("got 3 gems from a dwarf");
				break;
			case 5:
			case 6:
			case 7:
				output("nichts! Frustriert murmelst du:`7\"Mist, leer!\" `#Es war wohl doch nur ein schöner Traum...");
				break;
			case 8:
			case 9:
				output("`710 fürchterlich kämpferisch aussehende Zwerge`#, die bei Kaffee und Kuchen Karten spielen. Jedoch als du eintrittst, springen");
				output(" sie auf und umstellen dich mit ihren riesigen Kampfäxten! Sie schlagen dir vor `idich am Leben zu lassen, wenn du ihnen einen Edelstein gibst.`i Sichtlich bedrückt aber ohne Wahl ");
				output("gibst du ihnen einen und verschwindest so schnell du kannst.");
				output("`n`n`^Du verlierst 1 Edelstein.`n");
				if ($session[user][gems]>0) $session[user][gems]--;
//				debuglog("had to give 1 gem to the dwarfs");
				break;
			case 10:
			case 11:
			case 12:
			case 13:
			case 14:
				output("eine hastig verlassene und unordentliche Höhle. `7Die Asche im Kamin ist noch warm! `#Du schaust dich um");
				output(" und suchst nach einem Tresor oder sonst einem Versteck. Tatsächlich findest du ein kleines Golddepot hinter einem verstaubten Bild von einer Zwergenoma.`n");
				output("Erfreut verlässt du die Höhle.");
				$gold = e_rand($session[user][level]*5,$session[user][level]*15);
				output("`n`n`^Du erhälst ".$gold." Gold.`n");
				$session[user][gold]+=$gold;
				break;
			case 15:
				output("eine verlassene Höhle. Aber als du unbeschwert hinein rennst um nach zurückgelassenen Schätzen zu suchen, ");
				output("`7fällst du über einen,in Kniehöhe, gespannten Draht.`# Du wirst ohnmächtig, als dein Kopf mit großem ");
				output("Schwung auf den Boden knallt. Als du endlich aufwachst bemerkst du, unter großen Kopfschmerzen, ");
				output("dass jemand `idein ganzes Gold gestohlen hat!`i");
				output("`nFluchend schleppst du dich in den Wald zurück.`n");
				$session[user][gold] = 0;
				output("`n`n`^Du verlierst alles Gold, was Du bei Dir hattest!`n");
//				debuglog("lost all gold to thieves");
				break;
			}
		}
		else
		{
		  output("`n`#Du schüttelst dich und gehst zum Wald zurück, den Traum hast du schon wieder vergessen.");
		}
}
?>
