<?php

// Informativer Austausch- und Einf�hrungsort f�r Neuank�mmlinge
// by talion

require_once('common.php');

checkday();

page_header('Nebenraum der Schenke');

$op = ($_GET['op'] ? $_GET['op'] : '');

switch($op) {
	
	case '':
		
		addcommentary();
		
		output('`c`&`b Melwins Refugium f�r Neuank�mmlinge `c`&`b`n`n');
		
		output('`8Sch�chtern betrittst du das gem�tliche Nebenzimmer der Schenke. Hier hat der rauscheb�rtige Melwin sein Domizil, ein Druide wie er im Buche steht:
				Eine lange, silbergraue M�hne umrahmt sein hageres Gesicht, aus dem dir g�tige Augen entgegenblicken. Bekleidet ist er mit einer kostbaren, wenn auch 
				schon etwas	zerschlissenen feuerroten Robe.');
				
		if($_GET['first_login']) {
			output('`n`9"Wie hei�t du, mein '.($session['user']['sex'] ? 'M�dchen' : 'Junge').'?"`8`nLeise nennst du ihm deinen Namen.`nEr nickt freundlich `9"Willkommen, '.$session['user']['name'].'! F�hl dich hier wie zuhause.. wenn du Fragen hast, wende dich an mich."`8 mit einem Augenzwinkern meint er `9"Und probier unbedingt mal ');
		}
		else {
			output('`nAls du eintrittst, mustert er dich kurz pr�fend. Wohlwollend nickt er dir zu.');
		}
		
		viewcommentary('new_tut');
		
		break;
		
	case 'meal':
	
		if($session['user']['seenAcademy']) {
		
		}
		else {
		
			$session['user']['seenAcademy'] = 1;
			
			switch(e_rand(1,7)) {
				
				case 1:
					
					$session['user']['gems']++;
					
					break;
					
				case 2:
				case 3: 
					
					$session['user']['gold'] += 50 * $session['user']['level'];
					
					break;
					
				case 4:
				case 5: 
					
					$session['user']['turns']++;
					
					break;
					
				case 6:
				case 7:
					
					
					
					break;
				
			}
			
		}
			
		break;
	
	case 'ask':
		
		switch($_GET['question']) {
			
			case 'dragon':
				
				output('Den Drachen findest du im Wald
				
				break;
			
			
			default:
				
				output('Das wei� ich leider auch nicht!');
				
				break;
			
		}
		
		break;
		
	case 'try':
		
		addcommentary();
		
		viewcommentary('newtut_try'.$session['user']['acctid']);		
	
		break;
		

}

page_footer();
?>
