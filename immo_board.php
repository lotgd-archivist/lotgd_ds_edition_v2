<?php
/*-------------------------------/
Name: immo_board.php
Autor: tcb / talion f�r Drachenserver (mail: t@ssilo.de)
Erstellungsdatum: 9/05
Beschreibung:	Auslagerung aller Schl�sselgesuche vom schwarzen Brett zu einer eigenen Gelegenheit daf�r
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
	
	output('`5In der hintersten Ecke der Schenke findet sich ein weiteres schwarzes Brett, das von Cedrik dort aufgeh�ngt wurde, da 
			das Hauptinformationsmedium des Dorfes st�ndig durch Immobilienangebote belagert wurde.`n
			Diese sind nun hier zu finden und werden vom Wirt wohl auch nirgends sonst mehr toleriert werden..`n
			Unter der Tafel steht eine Sammelb�chse mit der Beschriftung: `^Wer nicht hier zahlt seine '.$msgprice.' Gold, dem kein Rubel rollt!`5`n
			'.($session['user']['gold'] < $msgprice ? 'Du denkst dir, dass Cedrik ohne deine Bezahlung wahrscheinlich die Stadtwache alarmieren wird... also zahle lieber!':'Gl�cklich �ber deine Ehrlichkeit f�hlst du nach den M�nzen in deiner Tasche..')
			.'`n`n');		
	
	board_view('immo',(su_check(SU_RIGHT_COMMENT))?2:1,
				'An der Tafel mit den Immobilienangeboten h�ngen diese Pergamente:',
				'An der Tafel mit den Immobilienangeboten h�ngt kein Angebot oder Gesuch aus.');
	
	if($session['user']['house']) {addnav('Biete Schl�ssel / Verkaufe Haus / Suche Ausbauhilfe','immo_board.php?op=new');addnav('-');}	
	addnav('Suche Schl�ssel / Suche Haus / Biete Ausbauhilfe','immo_board.php?op=new&search=1');
	addnav('-');
	addnav('Zur�ck zur Schenke','inn.php');
	addnav('Zum Dorf','village.php');
	
}
elseif($_GET['op'] == 'new') {
	
	if($_GET['search'] == 0) {	
	
		output('`c`&`bAngebot aufh�ngen`c`b`n`n`5(Informationen �ber dein Haus werden automatisch angeh�ngt)`n`n');
	
		$sql = 'SELECT * FROM houses WHERE houseid='.$session['user']['house'];
		$res = db_query($sql);
		$house = db_fetch_assoc($res);
		
		$txt = '`n`n`^Das Haus Nr. '.$house['houseid'].' '.$house['housename'].'`^: '.get_house_state($house['status'],0).'`^';
	}
	else {
		output('`c`&`bGesuch aufh�ngen`c`b`n`n');
	}	
		
	if($_GET['board_action'] == '') {
		
		output($txt);
		
		board_view_form('Auf den Immobilienmarkt',
						'`5Gib deine Nachricht ein:');		
						
		addnav('Zur�ck zu den Angeboten','immo_board.php');
	}
	else {
		if ($session['user']['gold']<$msgprice){
			$session['user']['imprisoned'] = 1;
			$session['user']['reputation'] -= 2;
			addnews($session['user']['name'].'`8 hat versucht, illegale Botschaften zu verbreiten.');
			output('`5Kaum willst du heimlich das St�ck Pergament anheften ohne zu bezahlen, da packt dich auch schon ein fester Griff! Nun wei�t du, wo du die Nacht verbringen wirst..');
			addnav('Zum Kerker','prison.php');
		}
		else {
						
			$_POST['msg'] .= addslashes($txt);
		
			if(board_add('immo',$expire,1) == -1) {
				output('`5Du bemerkst, dass da schon ein Zettel von dir h�ngt.. Es sieht bestimmt nicht gut aus, wenn du als einziger mehr aufh�ngen w�rdest!');
			}
			else {	
									
				output('`5Du wirfst die M�nzen in die B�chse, heftest den Zettel zu den anderen und schlenderst davon.');
				$session['user']['gold']-=$msgprice;
			}
			addnav('Zur�ck zu den Angeboten','immo_board.php');
		}
	}
	
}
	

page_footer();

// END immo_board.php
?>

