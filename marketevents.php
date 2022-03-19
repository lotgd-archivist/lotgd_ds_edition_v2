<?
require_once('common.php');

page_header('Marktplatz');

// Drachenreliquien

if($_GET['op'] == 'rel_get') {
	
	if($session['user']['gems'] >= 2) {
		$session['user']['gems'] -= 2;
		$str_what = 'zwei Edelsteine';
	}
	else {
		$session['user']['gold'] = 0;
		$str_what = 'dein Gold';
	}
	
	// Inaktive Rels entfernen
	// value1 enthält Timestamp des Empfangzeitpunkts
	$int_max_interval = 86400 * 14;
	$int_min_time = time() - $int_max_interval;
		
	$sql = 'SELECT a.acctid,i.name,i.id FROM items i 
			LEFT JOIN accounts a ON owner=acctid 
			WHERE (tpl_id="drrel_ksn" OR tpl_id="drrel_gld") AND value1 < '.$int_min_time;
	$res = db_query($sql);
	
	if(db_num_rows($res)) {
		
		while($a = db_fetch_assoc($res)) {
			
			// Reliquie entfernen und Mail schicken
			$str_body = '`5Urplötzlich raschelt '.$a['name'].'`5 verdächtig.. und löst sich schließlich in Staub auf! Das Ding war wohl nicht mehr so frisch..';
			systemmail($a['acctid'],'`5Reliquie vermisst!`0',$str_body);
			
			item_delete(' id='.$a['id']);
			
		}
		
	}				
	
	
	// Steckbrief einfügen
	item_add($session['user']['acctid'],'drstb');	
	
	output('`3Gierig grabscht der Mann nach dem Beutel, den du ihm entgegenstreckst und nimmt '.$str_what.'
			Mit der anderen Hand presst er dir ein Pergament in die Hand, tätschelt dich kurz mit einem undeutbaren
			Grinsen und verschwindet.');
	
	addnav('Zurück zum Marktplatz','market.php');		
		
}
else if($_GET['op'] == 'rel_leave') {
	
	$session['reloffered'] = true;
	redirect('market.php');
	
}
else if($_GET['op'] == 'rel') {
	
	$str_what = ($session['user']['gems'] >= 2 ? 'zwei Edelsteine' : 'dein Gold');
				
	// Meldung ausgeben		
	output('`3Als du, immer noch geschwächt von deinem letzten Kampf gegen den `@Grünen Drachen`3, auf den Marktplatz
			'.getsetting('townname','Atrahor').'s trittst, um dich bei den örtlichen Händlern mit neuer Ausrüstung einzudecken,
			schnellt mit einem Mal eine alte, runzlige Hand zwischen einem der Marktstände hervor und zupft
			unsanft an deinem Mantelärmel! Du drehst dich unwirsch um und willst den Störer schon zur Rede stellen,
			doch kaum setzt du an, dem Wesen die Meinung zu sagen, da raunt es dir schon zu: `#"Pssscchht.. ganz ruich.. 
			wir wollen ja nich, dass uns jemand auf de Schliche kommt, nech?!"`3 Mittlerweile haben sich deine Augen
			an das Zwielicht gewöhnt: Vor dir steht ein buckliger Mann unbestimmten Alters in schmuddligen Klamotten, 
			der dich aus nur einem, dafür aber umso wacher funkelnden Auge anblickt. `#"Also, ich heb dir n Angebot zu machn.
			Du gibscht mir '.$str_what.' und ich.. geb dir dafür n Steckbrief für ne "Drachenreliquie". Ist das
			nech n dolles Angebot?!`n`n'
			.create_lnk('Klingt phantastisch!','marketevents.php?op=rel_get').'`n`n'
			.create_lnk('Naja.. beim nächsten Mal vielleicht..','marketevents.php?op=rel_leave') , true );

}


page_footer();
?>