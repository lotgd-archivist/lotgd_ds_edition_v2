<?php
/*-------------------------------/
Name: immo_board.php
Autor: tcb / talion für Drachenserver (mail: t@ssilo.de)
Erstellungsdatum: 9/05
Beschreibung:	Auslagerung aller Schlüsselgesuche vom schwarzen Brett zu einer eigenen Gelegenheit dafür
/*-------------------------------*/

require_once("common.php");
require_once(LIB_PATH."board.lib.php");
page_header("Immobilienmarkt");

addcommentary();

checkday();

is_new_day();

$msgprice = ($session['user']['dragonkills'] < 10) ? 200 : 400;
$expire = 36;

if($_GET['op'] == '') {
	
	output('`c`b`&Immobilienmarkt`b`c`n`n');
	
	output('`5In der hintersten Ecke der Schenke findet sich ein weiteres schwarzes Brett, das von Cedrik dort aufgehängt wurde, da 
			das Hauptinformationsmedium des Dorfes ständig durch Immobilienangebote belagert wurde.`n
			Diese sind nun hier zu finden und werden vom Wirt wohl auch nirgends sonst mehr toleriert werden..`n
			Unter der Tafel steht eine Sammelbüchse mit der Beschriftung: `^Wer nicht hier zahlt seine '.$msgprice.' Gold, dem kein Rubel rollt!`5`n
			'.($session['user']['gold'] < $msgprice ? 'Du denkst dir, dass Cedrik ohne deine Bezahlung wahrscheinlich die Stadtwache alarmieren wird... also zahle lieber!':'Glücklich über deine Ehrlichkeit fühlst du nach den Münzen in deiner Tasche..')
			.'`n`n');		
	
	board_view('immo',(su_check(SU_RIGHT_COMMENT))?2:1,
				'An der Tafel mit den Immobilienangeboten hängen diese Pergamente:',
				'An der Tafel mit den Immobilienangeboten hängt kein Angebot oder Gesuch aus.');
	
	if($session['user']['house']) {addnav('Biete Schlüssel / Verkaufe Haus / Suche Ausbauhilfe','immo_board.php?op=new');addnav('-');}	
	addnav('Suche Schlüssel / Suche Haus / Biete Ausbauhilfe','immo_board.php?op=new&search=1');
	addnav('-');
	addnav('Zurück zur Schenke','inn.php');
	addnav('Zum Dorf','village.php');
	
}
elseif($_GET['op'] == 'new') {
	
	if($_GET['search'] == 0) {	
	
		output('`c`&`bAngebot aufhängen`c`b`n`n`5(Informationen über dein Haus werden automatisch angehängt)`n`n');
	
		$sql = 'SELECT * FROM houses WHERE houseid='.$session['user']['house'];
		$res = db_query($sql);
		$house = db_fetch_assoc($res);
		
		$txt = '`n`n`^Das Haus Nr. '.$house['houseid'].' '.$house['housename'].'`^: '.get_house_state($house['status'],0).'`^';
	}
	else {
		output('`c`&`bGesuch aufhängen`c`b`n`n');
	}	
		
	if($_GET['board_action'] == '') {
		
		output($txt);
		
		board_view_form('Auf den Immobilienmarkt',
						'`5Gib deine Nachricht ein:');		
						
		addnav('Zurück zu den Angeboten','immo_board.php');
	}
	else {
		if ($session['user']['gold']<$msgprice){
			$session['user']['imprisoned'] = 1;
			$session['user']['reputation'] -= 2;
			addnews($session['user']['name'].'`8 hat versucht, illegale Botschaften zu verbreiten.');
			output('`5Kaum willst du heimlich das Stück Pergament anheften ohne zu bezahlen, da packt dich auch schon ein fester Griff! Nun weißt du, wo du die Nacht verbringen wirst..');
			addnav('Zum Kerker','prison.php');
		}
		else {
						
			$_POST['msg'] .= addslashes($txt);
		
			if(board_add('immo',$expire,1) == -1) {
				output('`5Du bemerkst, dass da schon ein Zettel von dir hängt.. Es sieht bestimmt nicht gut aus, wenn du als einziger mehr aufhängen würdest!');
			}
			else {	
									
				output('`5Du wirfst die Münzen in die Büchse, heftest den Zettel zu den anderen und schlenderst davon.');
				$session['user']['gold']-=$msgprice;
			}
			addnav('Zurück zu den Angeboten','immo_board.php');
		}
	}
	
}
	

page_footer();

// END immo_board.php
?>

