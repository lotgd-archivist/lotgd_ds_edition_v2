<?php

// 21072004

/*
* Author:	Chaosmaker
* Email:		webmaster@chaosonline.de
* 
* Purpose:	Well for throwing keys in
*		
* Features:	Throw key into well, chat
*
* Keys thrown into this well are lost
*/

require_once("common.php");
addcommentary();
checkday();

page_header("Der Dorfbrunnen");

addnav("Zurück ins Dorf","village.php");
addnav("Zurück ins Wohnviertel","houses.php");
if ($session[user][gold]>1) addnav("1 Gold hineinwerfen","well.php?op=throwgold");
// if ($session[user][turns]<5 && $session[user][turns]>0) addnav("Springen","bridgeofdoom.php?op=jump");

addnav('Zur alten Eiche','schatzsuche.php');

// Eigene Schlüssel einlesen
if($_GET['op']!='throwkey' || isset($_GET['comscroll']) || $_POST['section']!="" || $_GET['act'] == 'ok') {
	$result = db_query('SELECT keylist.value1,houses.housename FROM keylist LEFT JOIN houses ON houses.houseid=keylist.value1 WHERE keylist.owner='.$session[user][acctid].' AND houses.owner != '.$session[user][acctid].' ORDER BY houses.housename ASC');
	if (db_num_rows($result) > 0) {
		$num = 0;
		while ($row = db_fetch_assoc($result)) {		
			if ($_GET['op']=='throwkey' && $_GET['house']==$row['value1']) $throwname = $row['housename'];
			else {
				if ($num==0) {
					$num++;
					addnav('Schlüssel wegwerfen');
				}
				addnav($row['housename'],'well.php?op=throwkey&house='.$row['value1']);
			}
		}
	}
}

// Schlüssel wegwerfen
if ($_GET['op']=='throwkey' && !isset($_GET['comscroll']) && $_POST['section']=="") {
	if($_GET['act'] == 'ok') {
		output('`@Du wirfst den Schlüssel für `^'.$throwname.'`@ in den Brunnen und wartest lange auf das Platschen.`nDer Brunnen muss sehr tief sein.');
		db_query('UPDATE keylist SET owner=0 WHERE owner='.$session[user][acctid].' AND value1='.(int)$_GET['house']) or die(db_error(LINK));	
	}
	else {
		output('`@Willst du diesen Schlüssel wirklich wegwerfen?');
		addnav('Schlüssel');
		addnav('Ja, weg damit','well.php?op=throwkey&act=ok&house='.$_GET['house']);
		addnav('Nein!!','well.php');
	}
	
	
}elseif ($_GET['op']=="throwgold" && !isset($_GET['comscroll']) && $_POST['section']==""){
	output("`@Du wirfst eines deiner Goldstücke hinein und zählst die Sekunden bis zum Platsch. Nach `^".(e_rand(1,10)/2)."`@ Sekunden hörst du es.");
	$session[user][gold]--;
}
else {
	output('`@Du näherst Dich dem Dorfbrunnen und schaust hinein. Wie tief er wohl sein mag?');
}

	output('`n`n`@Um den Brunnen herum stehen einige Leute.`n');
	viewcommentary("well","Mit Umstehenden reden:",25,"sagt");


page_footer();
?>
