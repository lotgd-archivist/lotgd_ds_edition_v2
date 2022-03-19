<?
/* Format for translator.php
 Each translatable page has its own entry below, locate the page where the text you want
 to translate is, and populate the $replace array with "From"=>"To" translation combinations.
 Only one translation per output() or addnav() call will occur, so if you have multiple
 translations that have to occur on the same call, place them in to their own array
 as an element in the $replace array.  This entire sub array will be replaced, and if any
 matches are found, further replacements will not be made.
 
 If you are replacing a single output() or addnav() call that uses variables in the middle,
 you will have to follow the above stated process for each piece of text between the variables.
 Example, 
 output("MightyE rules`nOh yes he does`n");
 output("MightyE is Awesome $i times a day, and Superawesome $j times a day.");
 you will need a replace array like this:
 $replace = array(
   "MightyE rules`nOh yes he does`n"=>"MightyE rulezors`nOh my yes`n"
   ,array(
     "MightyE is Awesome"=>"MightyE is Awesomezor"
     ,"times a day, and Superawesome"=>"timez a dayzor, and Superawesomezor"
     ,"times a day."=>"timez a dayzor."
     )
 );
 
*/
$translate_page = $_SERVER['PHP_SELF'];
$translate_page = substr($translate_page,strrpos($translate_page,"/")+1);
function translate($input){
	global $translate_page;
	//echo $_SERVER['SCRIPT_FILENAME'];
	//echo $translate_page;
	switch ($translate_page){
	case "index.php":
		$replace = array(
			"The current time in the village is"=>"Die gegenw�rtige Zeit im Dorf ist"
			,"About LoGD"=>"�ber LoGD"
			,"List Warriors"=>"Liste der K�mpfer"
			,"LoGD Net"=>"LoGD Netz"
			,"FAQ (for new players)"=>"F.A.Q. (F�r neue Spieler)"
			,"Create a character"=>"Neuen Charakter machen"
			,"Forgotten Password"=>"Passwort vergessen?"
			,"New to LoGD?"=>"Neu hier?"
			,array(
				"<u>U</u>sername:"=>"Name:"
				,"<u>P</u>assword:"=>"<u>P</u>asswort:"
				)
		);
		break;
	case "create.php":
		$replace = array(
			"How will you be known to this world?"=>"Wie willst du heissen in dieser Welt?"
			,"Enter a password:"=>"Dein Passwort:"
			,"Re-enter it for confirmation:"=>"Und nocheinmal:"
			,"Enter your email address:"=>"Deine Email Adresse:"
			,array(
				"And are you a"=>"Du bist"
				,"Female or a"=>"Weiblich oder"
				,"Male?"=>"M�nnlich?"
				)
			,"Create your character"=>"Erstelle deinen Charakter"
			,array(
				"Characters that have never been logged in to will be deleted after"=>"Charakter welche nie einloggen werden nachr"
				,"day(s) of no activity."=>"Tag(en) gel�scht."
				,"Characters that have never reached level 2 will be deleted after"=>"Charakter welche nie Level 2 erreichen werden nach"
				,"days of no activity."=>"Tagen ohne Aktivit�t gel�scht."
				,"Characters that have reached level 2 at least once will be deleted after"=>"Charakter welche Level 2 erreicht haben werden nach"
				)
			,"Your account was created, your login name is"=>"Dein Charaker wurde erstellt dein Login Name ist"
			,"Click here to log in"=>"Hier geht es rein"
			,"Login"=>"Komm rein"
		);
		break;
	case "village.php":
		$replace = array(
			"`bCombat`b"=>"`bK�mpfe`b"
			,"Forest"=>"Wald"
			,"Bluspring's Warrior Training"=>"Bluspring's K�mpfer Training"
			,"Slay Other Players"=>"T�te anderen Spieler"
			,"Commerce"=>"Commerce"
			,"MightyE's Weaponry"=>"MightyE's Waffen"
			,"Pegasus Armor"=>"Pegasus R�stungen"
			,"Ye Olde Bank"=>"Ye Olde Bank"
			,"The Inn"=>"Die Kneipe"
			,"Stables"=>"Stall"
			,"Gypsy Tent"=>"Zigeuner Zelt"
			,"`bOther`b"=>"`bAnderes`b"
			,"F.A.Q. (newbies start here)"=>"F.A.Q. (F�r neue Spieler)"
			,"Daily News"=>"T�gliche News"
			,"Preferences"=>"Einstellungen"
			,"List Warriors"=>"K�mpfer Liste"
			,"Hall of Fame"=>"Halle des Ruhmes"
			,"<font color='#FF00FF'>Quit</font> to the fields"=>"<font color='#FF00FF'>Verlasse</font> die Felder"
			,"Superuser Grotto"=>"Superuser Grotto"
			,"New Day"=>"Neuer Tag"
			,"`@`c`bVillage Square`b`cThe village hustles and bustles.  No one really notices that you're standing there."=>"`@`c`bDorfplatz`b`cDie Einwohner rennen gesch�ftig umher.  Keiner bemerkt wirklich, dass Du dort stehst."
			,"You see various shops and businesses along main street.  There is a curious looking rock to one side."=>"Du siehst verschiedene Gesch�fte und L�den entlang der Strasse.  Es gibt einen merkw�rdig aussehenden Felsen auf einer Seite."
			,"On every side the village is surrounded by deep dark forest.`n`n"=>"Auf jeder Seite wird das Dorf durch tiefen dunklen Wald umgeben.`n`n"
			,"The clock on the inn reads"=>"Die Uhrzeit auf der Kneipe zeigt"
			,"Village"=>"Dorfplatz"
			,"`n`n`%`@Nearby some villagers talk:`n"=>"`n`n`%`@In der N�he reden einige Dorfbewohner:`n"
			,"Add"=>"hinzuf�gen"
			,"says"=>"says"
		);
		break;
	case "about.php":
		$replace = array(
		
		);
		break;
	case "armor.php":
		$replace = array(
		
		);
		break;
	case "armoreditor.php":
		$replace = array(
		
		);
		break;
	case "bank.php":
		$replace = array(
		
		);
		break;
	case "battle.php":
		$replace = array(
		
		);
		break;
	case "bio.php":
		$replace = array(
		
		);
		break;
	case "configuration.php":
		$replace = array(
		
		);
		break;
	case "creatures.php":
		$replace = array(
		
		);
		break;
	case "dragon.php":
		$replace = array(
		
		);
		break;
	case "forest.php":
		$replace = array(
			"Something Special"=>"Etwas Besonderes"
			,"Return to the forest"=>"Zur�ck in den Wald"
			,"You have successfully fled your oponent!"=>"Du bist erfolgreich vor deinem Gegen�ber geflohen!"
			,"You failed to flee your oponent!"=>"Dir ist es nicht gelungen deinem Gegen�ber zu entkommen!"
			,"Enter the cave"=>"Betrete die H�hle"
			,"Run away like a baby"=>"Renne weg wie ein Baby"
			,"You approach the blackened entrance of a cave deep in the forest, though"=>"Du betrittst den dunkelnen Eingang einer H�hle, in den Tiefen des Waldes, jedoch"
			,"the trees are scorched to stumps for a hundred yards all around."=>"im Umkreis von mehreren hundert Metern sind die B�ume bis zu den St�mpfen niedergebrannt."
			,"A thin tendril of smoke escapes the roof of the cave's entrance, and is whisked away"=>"Rauchschwaden steigen an der Decke des H�hleneinganges empor, und werden pl�tzlich"
			,"by a suddenly cold and brisk wind.  The mouth of the cave lies up a dozen"=>"von einer kalten Windb�e verweht.  Der Eingang der H�hle liegt in der Seite eines Felsens,"
			,"feet from the forest floor, set in the side of a cliff, with debris making a"=>"ein Dutzent Meter �ber dem Boden des Waldes, wobei Ger�ll eine kegelf�rmige"
			,"conical ramp to the opening.  Stalactites and stalagmites near the entrance"=>"Rampe zum Eingang bildet.  Stalaktiten und Stalagmiten, nahe des Einganges"
			,"trigger your imagination to inspire thoughts that the opening is really"=>"erwecken dich aus deinen Gedanken. Dir wird klar,"
			,"the mouth of a great leach."=>"dass diese �ffnung wirklich der Eingang einer gro�en Grotte ist."
			,"You cautiously approach the entrance of the cave, and as you do, you hear,"=>"Als du vorsichtig den Eingang der H�hle betrittst, h�rst bzw. f�hlst du,"
			,"or perhaps feel a deep rumble that lasts thirty seconds or so, before silencing"=>"ein lautes Rumpeln, dass drei�ig Sekunden andauert, bevor wieder Ruhe eintritt"
			,"to a breeze of sulfur-air which wafts out of the cave.  The sound starts again, and stops"=>"Du bemerkst das dir ein Schwefelgeruch entgegenkommt.  Das Poltern ert�nt erneut, und h�rt wieder auf,"
			,"again in a regular rhythm."=>"in einem regelm��igen Rhytmus."
			,"You clamber up the debris pile leading to the mouth of the cave, your feet crunching"=>"Du kletterst den Ger�llhaufen rauf, der zum Eingang der H�hle f�hrt. Deine Schritte zerbrechen"
			,"on the apparent remains of previous heroes, or herhaps hors d'ouvers."=>"die scheinbaren �berreste der vorigen Helden."
			,"Every instinct in your body wants to run, and run quickly, back to the warm inn, and"=>"Jeder Instinkt in deinem K�rper will fliehen und so schnell wie m�glich zur�ck ins Wirtshaus"
			,array(
				"the even warmer"=>"in die W�rme"
				,"What do you do?"=>"Was gedenkst du zu tun?"
			)
			,"You are too tired to search the forest any longer today.  Perhaps tomorrow you will have more energy."=>"Du bist zu m�de um heute den Wald weiter zu durchsuchen. Vielleicht hast du morgen mehr Energie dazu."
			,"Something Special!"=>"Etwas Besonderes!"
			,"Aww, your administrator has decided you're not allowed to have any special events.  Complain to them, not me."=>"Arrr, dein Administrator hat entschieden, dass es dir nicht erlaubt ist, besondere Ereignisse zu haben.  Beschwer dich bei ihm, nicht bei mir."
			,"ERROR!!!`b`c`&Unable to open the special events!  Please notify the administrator!!"=>"ERROR!!!`b`c`&Es ist nicht m�glich die Speziellen Ereignisse zu �ffnen! Bitte benachrichtige den Administrator!!"
			,"Return to the forest"=>"Zur�ck in den Wald"
			,"You head for the section of forest you know to contain foes that you're a bit more comfortable with."=>"Du steuerst den Abschnitt des Waldes an, von dem du wei�t, dass sich dort Feinde aufhalten,  was dir ein bisschen angenehemer ist."
			,"You head for the section of forest which contains creatures of your nightmares, hoping to find one of them injured."=>"Du steuerst den Abschnitt des Waldes an, in dem sich Kreaturen deiner schlimmsten Alptr�ume aufhalten, in der Hoffnung das Du eine findest die verletzt ist."
			,array(
				"You have slain"=>"Du hast"
				,"!`0`b`n"=>"get�tet!`0`b`n"
				)
			,"You receive"=>"Du bekommst"
			,array(
				"Because of the difficult nature of this fight, you are awarded an additional"=>"Durch die Schwierigkeit des Kampfes bekommst du extra"
				,"experience!"=>"Erfahrungspunkte!"
				)
			,array(
				"Because of the simplistic nature of this fight, you are penalized"=>"Da der Kampf so leicht war bekommst du"
				,"experience!"=>"Erfahrungspunkte abgezogen"
				)
			,array(
				"You receive"=>"Du bekommst insgesamt"
				,"total experience!"=>"Erfahrungspunkte!"
				)
			,"~~ Flawless Fight! ~~`\$`n`bYou receive an extra turn!"=>"~~ Fehlerloser Kampf! ~~`\$`n`bDu erh�ltst eine extra Runde!"
			,"~~ Flawless Fight! ~~`b`\$`nA more difficult fight would have yielded an extra turn."=>"~~ Fehlerloser Kampf! ~~`b`\$`nDieser schwierige kampf hat dir eine extra Runde gebracht !"
			,"Daily news"=>"T�gliche News"
			,"has been slain in the forest by"=>"w�rde im Wald get�tet von"
			,"You have been slain by"=>"Du wurdest get�tet von"
			,"All gold on hand has been lost!"=>"Alles an Gold welches mitgef�hrt wurde hast du verloren!"
			,"10% of experience has been lost!"=>"10% an Erfahrung ging verloren!"
			,"You may begin fighting again tomorrow."=>"Du solltest vielleicht morgen weiterk�mpfen."
			,"Healer's Hut"=>"H�tte des Heilers"
			,"Look for Something to kill"=>"Du suchst etwas zum bek�mpfen"
			,"Go Slumming"=>"Herumziehen"
			,"Go Thrillseeking"=>"Nervenkitzel suchen"
			,"Take horse to Dark Horse Tavern"=>"Reiten zu der Dark Horse Kneipe"
			,"Return to the Village"=>"Zur�ck zur Stadt"
			,"Seek out the Green Dragon"=>"Suche den Green Dragon"
			,"The Forest, home to evil creatures and evil doers of all sorts."=>"Der Wald, Heimat von b�sartigen Kraturen und b�sartigen �belt�tern jeder Art.."
			,"The thick foliage of the forest restricts view to only a few yards in most places."=>"Die dichten Bl�tter des Waldes erlauben der Sicht nur ein paar meter zu den meisten Pl�tzen."
			,"The paths would be imperceptible except for your trained eye.  You move as silently as"=>"Die Wege w�rden unbemerkbar sein, h�ttest du nicht so ein trainiertes Auge. Du bewegst dich so ruhig wie"
			,"a soft breeze across the thick mould covering the ground, wary to avoid stepping on"=>"eine milde Briese �ber den dicken Schimmel, der den Boden bedeckt, du versuchst es zu vermeiden,"
			,"a twig or any of numerous bleached pieces of bone that perforate the forest floor, lest"=>"auf d�nne Zweige oder auf Knochenst�cke zu treten welche den Waldboden durchl�chern k�nnten."
			,"you belie your presence to one of the vile beasts that wander the forest."=>"Du verleugnest deine Gegenwart zu einen der vielen abscheulichen Monstern, die den Wald durchqueren."
			,"The Forest"=>"Der Wald"
		);
		break;
	case "gypsy.php":
		$replace = array(
		
		);
		break;
	case "healer.php":
		$replace = array(
		
		);
		break;
	case "hof.php":
		$replace = array(
		
		);
		break;
	case "inn.php":
		$replace = array(
		
		);
		break;
	case "list.php":
		$replace = array(
		
		);
		break;
	case "logdnet.php":
		$replace = array(
		
		);
		break;
	case "mail.php":
		$replace = array(
		
		);
		break;
	case "masters.php":
		$replace = array(
		
		);
		break;
	case "motd.php":
		$replace = array(
		
		);
		break;
	case "newday.php":
		$replace = array(
		
		);
		break;
	case "news.php":
		$replace = array(
		
		);
		break;
	case "petition.php":
		$replace = array(
		
		);
		break;
	case "prefs.php":
		$replace = array(
		
		);
		break;
	case "pvp.php":
		$replace = array(
		
		);
		break;
	case "shades.php":
		$replace = array(
		
		);
		break;
	case "stables.php":
		$replace = array(
		
		);
		break;
	case "superuser.php":
		$replace = array(
		
		);
		break;
	case "taunt.php":
		$replace = array(
		
		);
		break;
	case "train.php":
		$replace = array(
		
		);
		break;
	case "user.php":
		$replace = array(
		
		);
		break;
	case "viewpetition.php":
		$replace = array(
		
		);
		break;
	case "weaponeditor.php":
		$replace = array(
		
		);
		break;
	case "weapons.php":
		$replace = array(
		
		);
		break;
	}
	return replacer($input,$replace);
}

?>