<?php
/**
* findregalia.php: 	Zufallsereignis, bei dem Insigniensplitter aufgest�bert werden k�nnen. F�r Nicht-Gildenmitglieder gibt es nichts	
*				Diese Datei ist Bestandteil des Drachenserver-Gildenmods (DG). 
*				Copyright-Box muss intakt bleiben, bei Verwendung Mail an Autor mit Serveradresse.
* @author talion <t@ssilo.de>
* @version DS-E V/2
*/

$session['user']['specialinc'] = 'findregalia.php';

switch($_GET['op']) {
	
	case 'plant':
		
		$session['user']['specialinc'] = '';
		
		item_delete(' tpl_id="blmntpf" AND owner='.$session['user']['acctid']);
		
		switch(e_rand(1,5)) {
			
			case 1:
			case 2:
			case 3:
				
				output('`8Als du die Petunie in den verzauberten Blumentopf setzt, geschieht...`n`n
						nichts. Zun�chst. Doch mit einem Mal wird dir schwummerig vor den Augen!`n
						Du siehst `7graue, `9blaue, `^gelbe `8 Farben .. und schlie�lich.. `$rot!`n
						Durch deine unbedachte Aktion hast du eine fleischfressende Pflanze herangez�chtet,
						die dich bet�ubt und dann in einem Moment der Unachtsamkeit die Last deines Kopfes 
						abnimmt. Du bist tot und verlierst 10% deiner Erfahrung!`0');
				
				killplayer(0, 10, 0, '');
				
				addnav('Zu den Schatten','shades.php');
				
				addnews('`7Eine fleischfressende Petunie konnte heute dabei beobachtet werden, wie sie '.$session['user']['name'].'`7
							unter lautem Schmatzen die Vorz�ge vegetarischer Ern�hrung aufzeigte.');
				
			break;
			
			default:
				
				output('`8Als du die Petunie in den verzauberten Blumentopf setzt, f�hlst du dich mit einem Mal m�de, sehr m�de..`n`n
						Du gleitest zu Boden.`n`n
						Erst zum Anbruch des neuen Tages wachst du wieder auf, f�hlst dich frisch und erholt!`0');
				
				$session['user']['restorepage'] = 'forest.php';
				
				addnav('Es ist ein neuer Tag!','newday.php');
				
				debuglog('erhielt durch Petunie im Wald neuen Tag');
				
				addnews('`@Eine unheimlich faszinierende Petunie verzauberte '.$session['user']['name'].'`@ so sehr, dass '.($session['user']['sex'] ? 'sie':'er').'
							in einen erfrischenden Schlaf fiel..');
				
			break;
			
		}
						
	break;
	
	case 'drop':
		
		item_delete(' tpl_id="blmntpf" AND owner='.$session['user']['acctid']);
		
		$session['message'] = '`8Du wirfst die Pflanze zusammen mit dem alten Topf hinter den n�chstbesten Busch.`0';
		redirect('forest.php?found=1');
		
	break;
	
	default:

		if($_GET['found'] == 0) {
						
			require_once(LIB_PATH.'dg_funcs.lib.php');
			
			// Nur Insigniensplitter vergeben, wenn Gilde nicht bereits volle Kammern hat
			if($session['user']['guildid'] > 0 && $session['user']['guildfunc'] != DG_FUNC_APPLICANT && 
				(item_count(' owner='.ITEM_OWNER_GUILD.' AND tpl_id="insgnteil" AND deposit1='.$session['user']['guildid']) < getsetting('dgmaxregaliaparts',20) || e_rand(1,3) == 1)
				) 
			{
				
				$int_user_splitter = item_count(' owner='.$session['user']['acctid'].' AND tpl_id="insgnteil" ');
								
				$session['message'] = '`8Du stolperst �ber einen moosbewachsenen Stein. Als du deinen Blick fluchend in Richtung deines
										schmerzenden Fu�es wendest, erblickst du unter dem Laub ein mattes Schimmern.. das sich bei n�herer
										Betrachtung als Insigniensplitter erweist!`n';
				
				if($int_user_splitter < 2) {
					$session['message'] .= 'Du packst das Ding in deinen Beutel und machst dich wieder auf den Weg, die W�lder '.getsetting('townname','Atrahor').'s
											zu erkunden.`n`n
											Leider hat dich das Freilegen des Splitters eine Runde gekostet!`0';			
					if($int_user_splitter > 0) {
						$session['message'] .= '`n`n`8Als der Splitter in deinem Gep�ck verschwindet, entf�hrt dir bereits ein �chzen.
												Du solltest den Splitter baldm�glichst in deiner Gilde abliefern, denn viel mehr wirst
												du nicht tragen k�nnen..`0';
					}
					
					item_add($session['user']['acctid'],'insgnteil');
				
					$session['user']['turns']--;
					
				}
				else {
					$session['message'] .= '`8Leider tr�gst du in deinem Gep�ck bereits '.$int_user_splitter.' Insigniensplitter mit dir herum! Noch mehr
											kannst du nicht tragen. So schwer es dir f�llt, du musst den Splitter liegen lassen..`0';
				}
				
				redirect('forest.php?found=1');
				
			}
			else {
			
				if(item_count(' tpl_id="blmntpf" AND owner='.$session['user']['acctid'])) {
					output('`8Auf einer bunt bl�henden Lichtung am Rand des Waldes erblickst du unter all den Blumen eine Petunie. Endlich,
							denkst du dir, hast du doch schon lange nach einem Inhalt f�r deinen verzauberten Blumentopf gesucht!`n`nVorsichtig pfl�ckst du
							die Blume und �berlegst, was du nun damit machen sollst:`n`n');
					$str_lnk = 'forest.php?op=';
					output( '`2'.create_lnk('In Topf pflanzen: ',$str_lnk.'plant').'`&W�re eigentlich logisch..`n`n', true );
					output( '`6'.create_lnk('Beides wegwerfen: ',$str_lnk.'drop').'`&Wer wei�, was f�r giftiges Zeug sonst dabei rauskommen kann..`n`n', true );
				}
				else {
					$session['message'] = '`8Du stolperst �ber einen moosbewachsenen Stein. Als du deinen Blick fluchend in Richtung deines
						schmerzenden Fu�es wendest, erblickst du unter dem Laub ein mattes Schimmern.. das sich bei n�herer
						Betrachtung als Topf erweist, in dem einmal eine Petunie ihren Platz gefunden haben muss!`n
						In der Hoffnung, irgendwann auch noch den Inhalt zu finden, packst du das Ding in deinen Beutel und machst dich wieder auf den Weg, die W�lder '.getsetting('townname','Atrahor').'s
						zu erkunden.`0';
					item_add($session['user']['acctid'],'blmntpf');
					redirect('forest.php?found=1');
				}
												
			}
			
		}
		else {
			output($session['message']);
			$session['message'] = '';
			$session['user']['specialinc'] = '';
		}
	break;
}

?>
