<?

/*********************************************

Lots of Code from: lonnyl69 - Thanks Lonny !

Also Thanks to Excalibur @ dragonprime for your help.

By: Kevin Hatfield - Arune v1.0

06-19-04 - Public Release

Written for Fishing Add-On - Poseidon Pool



Translation and simple modifications by deZent deZent@onetimepad.de







ALTER TABLE accounts ADD wormprice int(11) unsigned not null default '0';

ALTER TABLE accounts ADD minnowprice int(11) unsigned not null default '0';

ALTER TABLE accounts ADD wormavail int(11) unsigned not null default '0';

ALTER TABLE accounts ADD minnowavail int(11) unsigned not null default '0';

ALTER TABLE accounts ADD trades int(11) unsigned not null default '0';

ALTER TABLE accounts ADD worms int(11) unsigned not null default '0';

ALTER TABLE accounts ADD minnows int(11) unsigned not null default '0';

ALTER TABLE accounts ADD fishturn int(11) unsigned not null default '0';

add to newday.php

$session['user']['trades'] = 10;

if ($session['user'][dragonkills]>1)$session['user'][fishturn] = 3;

if ($session['user'][dragonkills]>3)$session['user'][fishturn] = 4;

if ($session['user'][dragonkills]>5)$session['user'][fishturn] = 5;



Now in village.php:

addnav("Poseidon Pool","pool.php");

********************************************/

require_once "common.php";

define('MAX_ITEMS',100);

checkday();

page_header("Kerras Angelladen");

output("`@`c`bKerras Angelladen`b`c`n");

$sql = "SELECT worms,minnows FROM account_extra_info WHERE acctid=".$session['user']['acctid']."";
$result = db_query($sql) or die(db_error(LINK));
$rowf = db_fetch_assoc($result);

if($session['user']['dragonkills'] == 0) {
	
	output('`5Kerra mustert Dich und meint dann: `6"Du solltest erst mehr Erfahrung sammeln, ehe Du dich an die Herausforderung des Angelns machst!"`n');
	addnav('Zurück zum See','pool.php');
	
}
else {

	$inventory=$rowf['worms'];
	$inventory+=$rowf['minnows'];
	
	$space= max(MAX_ITEMS - $inventory,0);
	
	$cost = array();
	$max = array();
	
	$cost['worms'] = 5;
	$cost['minnows'] = 6;
	$max['worms'] = min( floor($session['user']['gold'] / $cost['worms']) , $space );
	$max['minnows'] = min( floor($session['user']['gold'] / $cost['minnows']) , $space );
	
	$op = ($_GET['op']) ? $_GET['op'] : '';
	
	switch($op) {
		
		case '':
			
			output('`5Hinter der Theke des anheimelnden, kleinen Angelladens steht der Besitzer: Kerra. Er begrüßt Dich freundlich und fragt dann nach Deinen Wünschen.`n`n');
		
			output('`5In deinem Beutel siehst Du:`n');
			output('`!'.$rowf['minnows'].' Fliegen`5 und `1'.$rowf['worms'].' Angelwürmer.`n');
			if ($inventory > MAX_ITEMS) {
				output('`4Du bemerkst, dass dein Beutel schon voll ist.`n`n');
			}
			else {
				output('`5Du hast noch für '.$space.' Dinge Platz im Beutel.`n`n');
						
				output('`5Was möchtest Du kaufen?`n`n');
							
				output('Fliegen zum Preis von `@'.$cost['minnows'].' Gold `5das Stück.');
				output('<form method="POST" action="bait.php?op=trade&what=minnows">',true);
				output('`n<input type="text" name="count" value="'.$max['minnows'].'"><input type="hidden" name="cost" value="'.$cost['minnows'].'"> <input type="submit" value="Fliegen kaufen"></form>',true);
				addnav('','bait.php?op=trade&what=minnows');
				
				if($session['user']['dragonkills'] >= 2) {
				
					output('Würmer zum Preis von `@'.$cost['worms'].' Gold `5das Stück.');				
					output('<form method="POST" action="bait.php?op=trade&what=worms">',true);
					output('`n<input type="text" name="count" value="'.$max['worms'].'"><input type="hidden" name="cost" value="'.$cost['worms'].'"> <input type="submit" value="Würmer kaufen"></form>',true);
					addnav('','bait.php?op=trade&what=worms');
					
				}
				else {
					
					output('Um mit Würmern zu angeln, solltest Du schon erfahrener sein!');
					
				}
			}
			
			addnav('Zurück zum See','pool.php');
			
			break;
					
		case 'trade':
		
		$sql = "SELECT worms,minnows FROM account_extra_info WHERE acctid=".$session['user']['acctid']."";
$result = db_query($sql) or die(db_error(LINK));
$rowf = db_fetch_assoc($result);
			
			$what = ($_GET['what'] == 'worms') ? 'worms' : 'minnows';
			$count = min($max[$what],$_POST['count']);
			$cost = $_POST['cost'] * $count;		
			$totalcount=$rowf[$what];
			$totalcount+=$count;
			
  $sql = "UPDATE account_extra_info SET $what=$totalcount WHERE acctid=".$session['user']['acctid']."";
  db_query($sql);

			$session['user']['gold'] -= $cost;			
							
			output('`5Du kaufst `&'.$count.' '.(($what == 'worms') ? 'Würmer' : 'Fliegen').'`5 für `^'.$cost.'`5 Gold!`nKerra schiebt Dir einen kleinen Beutel herüber, nimmt das Gold entgegegen und schaut Dich abwartend an.');
			
			addnav('Noch mehr kaufen','bait.php');
			addnav('Auf zum See!','pool.php');
			
			break;
		
		
	}
}

page_footer();
?> 
