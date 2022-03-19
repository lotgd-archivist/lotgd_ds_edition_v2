<?
/**
* runemaster.php: Runenmeister
* @author Alucard <diablo3-clan[AT]web.de>
* @version DS-E V/2
* @TODO: 
*/
require_once('common.php');
require_once(LIB_PATH.'runes.lib.php');

checkday();
page_header('Runenmeister');

function get_seltenheitname( $val )
{
	if( $val > 230){
		return '`4sehr selten';
	}
	
	if( $val > 170){
		return '`qselten';
	}
	
	if( $val > 100){
		return '`^durchschnittlich';
	}
	
	if( $val > 50){
		return '`@oft';
	}
	
	return '`2sehr oft';
}

$rune_gods = array('Thor','Wotan','Tyr','Freyr','Idun');


$out = '`^';

switch( $_GET['op'] ){
	
	case 'master':
		$out .= 'Im Schatten eines knorrigen Baumes, der neben dem Felsen steht, hat sich ein düster dreinblickender Mann ';
		$out .= 'nieder gelassen. Auf seinem Arm hockt ein schwarzer Rabe und zu seinen Füßen liegen etliche mit Runen versehene ';
		$out .= 'Steine. Ohne aufzublicken spricht die Gestalt mit finsterer Stimme:`n';
		$out .= '`&Tritt näher! Doch gib Acht! '.$rune_gods[array_rand($rune_gods)].' wacht über uns!`n`n';
		$out .= '`^Du gehst auf die Gestalt zu und blickst ihn erfurchtsvoll an.';
		$out .= '`nEr mustert Dich und spricht zu Dir: ';
		$out .= '`&Ich bin der Runenmeister! Was treibt Dich zu mir?`n`n';
		$out .= '`^Was tust du?`n';
		addnav('Was tust du?');
		addnav('Runen?','runemaster.php?op=runes');
		addnav('','runemaster.php?op=runes');
		$out .= '<a href="runemaster.php?op=runes">Ihn auf Runen ansprechen</a>`n';
		
		if(!(e_rand(1,23)%5) && $session['user']['turns']){
			addnav('Den Raben anfassen','runemaster.php?op=raven');
			addnav('','runemaster.php?op=raven');
			$out .= '<a href="runemaster.php?op=raven">Seinen Raben anfassen</a>`n';
		}
		
		if( $session['user']['turns'] > 0 && item_count('tpl_id="r_raidho" AND owner='.$session['user']['acctid']) > 0  ){
			addnav('Zur Burg (Raidho Rune)','runemaster.php?op=castle');
			addnav('','runemaster.php?op=castle');
			$out .= '<a href="runemaster.php?op=castle">Erkläre mir den Weg zur Orgburg für eine Raidho - Rune!</a>`n';
		}
		
		if( item_count('tpl_id="r_dagaz" AND owner='.$session['user']['acctid']) > 0 ){
			addnav('Entzaubern (Dagaz Rune)','runemaster.php?op=unban');
			addnav('','runemaster.php?op=unban');
			$out .= '<a href="runemaster.php?op=unban">Entzaubere mich für eine Dagaz - Rune!</a>`n';
		}
		
		if( item_count('tpl_id="r_jera" AND owner='.$session['user']['acctid']) > 0 ){
			addnav('Runden auffüllen (Jera Rune)','runemaster.php?op=fillup');
			addnav('','runemaster.php?op=fillup');
			$out .= '<a href="runemaster.php?op=fillup">Fülle Runden auf für eine Jera - Rune!</a>`n';
		}
		
		if( item_count('tpl_id="r_sowilo" AND owner='.$session['user']['acctid']) > 0 ){
			addnav('Rüstung Gravieren (Sowilo Rune)','runemaster.php?op=armorrename');
			addnav('','runemaster.php?op=armorrename');
			$out .= '<a href="runemaster.php?op=armorrename">Graviere meine Rüstung für eine Sowilo - Rune!</a>`n';
		}
		
		addnav('Gehen','runemaster.php?op=leave1');
		addnav('','runemaster.php?op=leave1');
		$out .= '<a href="runemaster.php?op=leave1">Diesen Ort verlassen</a>`n';
	break;
	
	
	case 'leave1':
		$out .= 'Du sagst, dass Du Dich verlaufen hättest und kehrst ihm den Rücken zu.';
		addnav('Zurück ins Dorf','village.php');
	break;
	
	
/*SPECIAL ZEUG DER RUNEN*/

//Burg
	case 'castle':
		$out .= 'Du fragst den Runenmeister, ob er den Weg zur Orkburg kennt. Er nickt und spricht:`&`n';
		$out .= 'Ich kann Euch den Weg erklären, jedoch will ich eine Raidho - Rune dafür!`n';
		addnav('Was tust du?');
		addnav('Rune geben','runemaster.php?op=castle2');
		addnav('Nein, zurück!','runemaster.php?op=master');
	break;
	
	case 'castle2':
		$out .= 'Nach kurzem Überlegen stimmst du zu und gibst ihm die Rune.`n';
		$out .= 'Er verstaut sie in seinem Beutel und fängt dann an dir folgendes zu erkären:`&`n';
		$out .= 'Geht zunächst zum Wald! Dort findet Ihr ein Klohäuschen, an dessen Seite sich ein Schild befindet, was den weiteren Weg zur Orkburg weist.`n';
		$out .= '`^Du nickst und hörst aufmerksam zu.`n';
		$out .= 'Er spricht weiter: `&`n';
		$out .= 'Folgt einfach dem Pfad! Doch gebt acht, dass Ihr Euch nicht verlauft!`n';
		$out .= '`^Du winkst voller Selbstbewusstsein ab, ginst und machst dich auf den Weg.';
		item_delete('tpl_id="r_raidho" AND owner='.$session['user']['acctid'],1);
		$session['user']['specialinc']="castle.php";
		addnav('Auf zur Burg!', 'forest.php');
	break;
	
//Entzaubern (Flauschihasi, Kröte, Raminus Sklave)
	case 'unban':
		$out .= 'Du fragst den Runenmeister, ob er dich nicht von deinem Leiden deines Aussehens erlösen könne. Er nickt und spricht:`&`n';
		$out .= 'Ich kann Euch entzaubern, jedoch will ich eine Dagaz - Rune dafür!`n';
		addnav('Was tust du?');
		addnav('Rune geben','runemaster.php?op=unban2');
		addnav('Nein, zurück!','runemaster.php?op=master');
	break;
	
	case 'unban2':
		$out .= 'Nach kurzem Überlegen stimmst du zu und gibst ihm die Rune.`n';
		$out .= 'Er verstaut sie in seinem Beutel, holt einige Kräuter aus seinem Mantel, zerreibt sie in seinen Händen und schmiert sie dir auf die Stirn.`n';
		$out .= 'Als du im ersten Augenblick keine Veränderung -mit der Ausnahme, dass du nun auch noch Pampe im Gesicht hast- feststellst, willst du dein '.$session['user']['weapon'];
		$out .= ' ziehen und es dem Runenmeister heimzahlen. Jedoch merkst du, dass du dich nicht bewegen kannst. Plötzlich wird dir sehr warm und `#*PUFF* `^ein Lauter Knall mit viel Schwefelgestank ';
		$out .= 'und du merkst, dass du wieder der Alte bist.`n';
		$out .= 'Der Runenmeister grinst und du bedankst dich.';
		
		$sql = 'SELECT ctitle,cname FROM account_extra_info WHERE acctid='.$session['user']['acctid'];
		$res = db_query($sql);
		$row_extra = db_fetch_assoc($res);
		
		$name  = ($row_extra['cname'] ? $row_extra['cname'] : $session['user']['login']);		
		$title = (empty($row_extra['ctitle']) ? $titles[ min($session['user']['dragonkills'], sizeof($titles)-1) ][ $session['user']['sex'] ] : $row_extra['ctitle'] );
		$oldname = $session['user']['name'];
		$session['user']['title'] = $title;
		$session['user']['name']  = $title." ".$name;
		
		item_delete('tpl_id="r_dagaz" AND owner='.$session['user']['acctid'],1);
		addnews('`@'.$oldname.'`@ ist dank der `qRunenmagie`@ wieder bekannt als '.$session['user']['name'].'.');
		addnav('Juhu!', 'runemaster.php?op=master');
	break;
	
	
//Runden auffüllen
	case 'fillup':
		$resdisc = db_query("SELECT state FROM disciples WHERE master = ".$session['user']['acctid']);
		$disciple = db_fetch_assoc($resdisc);
		$out .= 'Du fragst den Runenmeister, ob er auch Erfrischen könne. Er nickt und spricht:`&`n';
		$out .= 'Ich kann Euch'.($session['user']['hashorse']?', Euer Tier':'').($disciple['state']?', Euern Knappen':'').' erfrischen, jedoch will ich eine Jera - Rune dafür!`n';
		addnav('Erfischen');
		addnav('Mich selbst','runemaster.php?op=fillup2&w=self');
		if ($session['user']['hashorse']){
			addnav('Tier','runemaster.php?op=fillup2&w=pet');
		}
		if( $disciple['state'] ){
			addnav('Knappen','runemaster.php?op=fillup2&w=disc');
		}
		addnav('Zurück');
		addnav('Nein, zurück!','runemaster.php?op=master');
	break;
	
	case 'fillup2':	
		switch( $_GET['w'] ){
			 
			
			
			case 'disc'://knappe
				$resdisc = db_query("SELECT name FROM disciples WHERE master = ".$session['user']['acctid']);
				$disciple = db_fetch_assoc($resdisc);
				$out .= 'Du gibst ihm die Rune und sagst, dass du Deinen Knappen eine Erfrischung gönnen willst.`n';
				$out .= 'Er verstaut sie in seinem Beutel und gibt deinem Knappen einen Zaubertrank, welchen `@'.$disciple['name'].'`^ sofort trinkt und sich wie neugeboren fühlt.';
				$sql = "SELECT name,state,level FROM disciples WHERE state>0 AND master=".$session['user']['acctid']."";
				$result = db_query($sql) or die(db_error(LINK));
			
				if (db_num_rows($result)>0){
					$rowk = db_fetch_assoc($result);
					$kname=$rowk['name'];
					$kstate=$rowk['state'];
				}		
				if (($kstate>0) || (db_num_rows($result)>0)) {
					$session['bufflist']['decbuff'] = set_disciple($kstate);
					if ($rowk['level']>0)
					{
					  $session['bufflist']['decbuff']['name'].=" ->Lvl ".$rowk['level']."`0";
					  $session['bufflist']['decbuff']['atkmod']+=($rowk['level']*0.005);
					  $session['bufflist']['decbuff']['defmod']+=($rowk['level']*0.005);
					  $session['bufflist']['decbuff']['rounds']+=($rowk['level']*2);
					}	
				}
			break;
			
			case 'self': //mich selbst
				$session['user']['turns'] += 10;
				$session['user']['hitpoints'] = $session['user']['maxhitpoints'];
				$out .= 'Du gibst ihm die Rune und sagst, dass du Dir selbst eine Erfrischung gönnen willst.`n';
				$out .= 'Er verstaut sie in seinem Beutel und gibt dir einen Zaubertrank, welchen du sofort trinkst und dich wie neugeboren fühlst.`n`n';
				$out .= '`@Du bekommst 10 Waldkämpfe und deine Lebenspunkte wurden vollständig aufgefüllt.';
			break;
			
			case 'pet': //tier
				
				getmount($session['user']['hashorse'],true);
				$sql = 'SELECT hasxmount,mountextrarounds,xmountname FROM account_extra_info WHERE acctid='.$session['user']['acctid'];
				$res = db_query($sql);
				$row_extra = db_fetch_assoc($res);	
				$session['bufflist']['mount']=unserialize($playermount['mountbuff']);
		
				if ($row_extra['hasxmount']==1) {
					$session['bufflist']['mount']['name']=$row_extra['xmountname']." `&({$session['bufflist']['mount']['name']}`&)"; 
					$tier = $row_extra['xmountname'];
				}
				else{
					$tier = 'Tier';	
				}
		
				$session['bufflist']['mount']['rounds']+=$row_extra['mountextrarounds'];
				
				
				$out .= 'Du gibst ihm die Rune und sagst, dass du deinem Tier eine Erfrischung gönnen willst.`n';
				$out .= 'Er verstaut sie in seinem Beutel und gibt deinem '.$tier.'`^ einen Zaubertrank, welchen dein `@'.$tier.'`^ sofort trinkt und sich wie neugeboren fühlt.';
			break;
			
		}
		
		
		item_delete('tpl_id="r_jera" AND owner='.$session['user']['acctid'],1);
		addnav('Juhu!', 'runemaster.php?op=master');
	break;
	
//Rüstung Gravieren
	case 'armorrename':
		$out .= 'Du fragst den Runenmeister, ob er dir Deine Rüstung verschönern könne. Er schaut deine Rüstugn an und sagt dann:`&`n';
		$out .= 'Für eine Sowilo - Rune kann ich Euch Eure Rüstung mit einer Gravur nach Wunsch prachtvoll verzieren!`n`n`n';		
		$out .= '`bEine Rüstung benennen`b`n';    
		$out .= '`^Der Name deiner Rüstung darf 30 Zeichen lang sein und Farbcodes enthalten.`nVermeide es schwarz zu verwenden, da diese Farbe auf dunklem Hintergrung gar nicht oder nur schlecht angezeigt wird.`n`n';
		$out .= 'Deine Rüstung heißt bisher: `&'.$session['user']['armor']; 
		$out .= '`n`n`^Wie soll deine Rüstung heißen ?`n';
		$out .= '<script type="text/javascript" language="JavaScript" src="templates/chat_prev.js"></script>';
		$out .= '<span id="newname_prev"></span>`n';
		$out .= '<form action="runemaster.php?op=armorrename2" method="POST"><input name="newname" value="" size="30" maxlength="30" onkeyup="input_prev(this);this.focus()"> <input type="submit" value="Gravieren"></form>';
		addnav('','runemaster.php?op=armorrename2');		
		addnav('Zurück?');
		addnav('Zurück!','runemaster.php?op=master');
	
	break;
	
	
	case 'armorrename2':
		item_set_armor($_POST['newname'],-1,-1,0,0,1);
		item_delete('tpl_id="r_sowilo" AND owner='.$session['user']['acctid'],1);
        $out .= 'Gratulation, deine Rüstung heißt jetzt '.$session['user']['armor'].'`0!`n`n';
		addnav('Wie schön!');
		addnav('Gut gemacht!','runemaster.php?op=master');
	break;
	
/*RUNEN*/	
	case 'runes':
		$out .= 'Was möchtest Du mit den Runen machen?`n';
		$out .= '-Ich möchte Die Runen benutzen um Ihre magischen Kräfte zu vereinen!`n';
		$out .= '-Ich benötige das Wissen des Runenmeisters über eine Rune, die ich nicht kenne!`n';
		$out .= '-Ich will mir alle Runen anschauen, die ich schon kenne!';
		addnav('Runenmagie','runemaster.php?op=magic');
		addnav('Runen identifizieren','runemaster.php?op=identify');
		addnav('Runen anzeigen','runemaster.php?op=showknown');
		addnav('Hilfe','runemaster.php?op=help&back=runes');
		addnav('Zurück ins Dorf','village.php');
	break;
	
	
	case 'magic':
		$out .= 'Bevor du beginnst lässt Dich der Runenmeister mit einer Handbewegung inne halten:`n';
		$out .= '`&Du weißt, für jeden Versuch verlange ich 100 Goldstücke von Dir!`n'; 
		$out .= '`^Du nickst verstehend.`n'; 
		$out .= '`&Wohlan, lege deine magischen Gegenstände einfach hier vor mir in diese Schalen.`n'; 
		$out .= 'Bedenke aber, du brauchst zwar nicht alle Schalen zu füllen,`n jedoch ist die Reihenfolge ihrer Beschwörung von essentieller Magischer Bedeutung!`n';
		
		
		$out .= '<style><!--@import url("templates/runes.css");--></style>
				<script language="javascript" src="./templates/dragdrop.js"></script>
				`n`n`c<table border="0" cellpadding="0" colspan="0" rowspan="0" cellspacing="5">
					<tr class="trhead">
						<td colspan="7" align="center"><b>Magieanordnung</b></td>
					</tr>
					<tr>
						<td id="drop_0">&nbsp;</td>
						<td valign="center">+</td>
						<td id="drop_1">&nbsp;</td>
						<td valign="center">+</td>
						<td id="drop_2">&nbsp;</td>
						<td valign="center">+</td>
						<td id="drop_3">&nbsp;</td>
					</tr>
					<tr>
						<td class="mix_placehold">&nbsp;</td>
					</tr>
				</table>';
				$link = 'runemaster.php?op=magic_try';
				addnav('',$link);
		$out .= '<form action="'.$link.'" method="POST">
				<input id="drop_0_id" type="hidden" name="drop_0_id"><input id="drop_0_tpl" type="hidden" name="drop_0_tpl">
				<input id="drop_1_id" type="hidden" name="drop_1_id"><input id="drop_1_tpl" type="hidden" name="drop_1_tpl">
				<input id="drop_2_id" type="hidden" name="drop_2_id"><input id="drop_2_tpl" type="hidden" name="drop_2_tpl">
				<input id="drop_3_id" type="hidden" name="drop_3_id"><input id="drop_3_tpl" type="hidden" name="drop_3_tpl">
				<input type="submit" class="button" value="Magie entfalten">
				</form>
				`c`n`n`n`n
				`bFolgendes trägst du bei dir:`b`n<div>
				<table border="0" cellpadding="3" colspan="0" rowspan="0" cellspacing="5">
					<tr class="trhead"><td align="center" colspan="6">Runen</td></tr>
					<tr>';	
	
		$res = runes_get();
		$i   = 0;
		$drop= 4;
		$drag= 0;
		$dragdiv='';
		$dragreg ='';
		if( db_num_rows($res) ){
			while( ($rune = db_fetch_assoc($res)) ){	
				$out .= '<td id="drop_'.$drop.'" valign="top">&nbsp;</td>';
				$name = str_replace('r_', '', $rune['tpl_id']); 
				$dragdiv .= '<div id="drag_'.$drag.'" align="center">
							<img style="display:block;padding-bottom: 5px;" src="./images/runes/'.$name.'.png">
							<i>'.$rune['name'].'</i>
						</div>';
				$dragreg .= 'registerDragObjByID("drag_'.$drag.'", "r_'.$name.'", '.$rune['id'].');
							 setDragToDrop( '.$drag.', '.$drop.' );
							 ';
				$i++;
				$drop++;
				$drag++;
				if( $i > 5 ){
					$out .= '</tr><tr><td class="mix_placehold">&nbsp;</td></tr><tr>';
					$i = 0;
				}
			}
			if( $i ){
				$out .= '</tr><tr><td class="mix_placehold">&nbsp;</td></tr><tr>';
			}
		}
		else{
			$out .= '<td align="center" colspan="6">`iDu trägst nichts brauchbares bei dir!`i</td>';
		}
		$i=0;
		$out .= '<tr class="trhead"><td align="center" colspan="6" nowrap>Waffen & Rüstungen (nicht angelegt)</td></tr>';
		$Ares['weapon'] = item_list_get('tpl_id="waffedummy" AND deposit1=0 AND deposit2=0 AND owner='.$session['user']['acctid'].' LIMIT 3', '', false);
		$Ares['amor']   = item_list_get('tpl_id="rstdummy" AND deposit1=0 AND deposit2=0 AND owner='.$session['user']['acctid'].' LIMIT 3', '', false);
		if( db_num_rows($Ares['weapon']) || db_num_rows($Ares['amor'])){
			foreach( $Ares as $res ){
				
				while( ($item = db_fetch_assoc($res)) ){	
					$out .= '<td id="drop_'.$drop.'" valign="top">&nbsp;</td>';
					$dragdiv .= '<div id="drag_'.$drag.'" align="center">
								<i>'.$item['name'].'</i>
							</div>';
					$dragreg .= 'registerDragObjByID("drag_'.$drag.'", "'.$item['tpl_id'].'", '.$item['id'].');
								 setDragToDrop( '.$drag.', '.$drop.' );
								 ';
					$i++;
					$drop++;
					$drag++;
					if( $i > 5 ){
						$out .= '</tr><tr><td class="mix_placehold">&nbsp;</td></tr><tr>';
						$i = 0;
					}
				}
				if( $i ){
					$out .= '</tr><tr><td class="mix_placehold">&nbsp;</td></tr><tr>';
				}
			}
		}
		else{
			$out .= '<td align="center" colspan="6">`iDu trägst nichts brauchbares bei dir!`i</td>';
		}
		
		$out .= '</tr></table></div>';
		$out .= $dragdiv;
		$out .= '<script language="javascript" defer=defer>
						function runes_ondrop( drp, drg ){
							document.getElementById( drp.id + "_tpl" ).value = drg.value1;
							document.getElementById( drp.id + "_id" ).value = drg.value2;	
						}
						function runes_onleave( drp ){
							document.getElementById( drp.id + "_tpl" ).value = "";
							document.getElementById( drp.id + "_id" ).value = "";	
						}
						initializeDragDrop();
						registerDropObjsByID("drop_", 64, 94, 2, 2, 0, 0);
						setDropOnDrop(0, runes_ondrop, 3);
						setDropOnLeave(0, runes_onleave, 3);
						'.
						$dragreg.
					'</script>';
		
		addnav('Hilfe','runemaster.php?op=help&back=magic');
		addnav('Zurück','runemaster.php?op=runes');
		
	break;
	
	
	case 'help':	
		$out .= get_extended_text('runen_help');
		addnav('Zurück','runemaster.php?op='.$_GET['back']);
	break;
	
	case 'magic_try':
		addnav('Zurück','runemaster.php?op=magic');
		$item = array();
		for($i=0;$i<4;++$i){
			if( !empty($_POST['drop_'.$i.'_tpl']) ){
				array_push($item, array('id'=>$_POST['drop_'.$i.'_id'], 'tpl'=>$_POST['drop_'.$i.'_tpl']));
			}
		}
		//reset($item);
		//print_r($item);
		$item_count = count($item);
		$cantmix = 0;
		if($item_count < 1){
			$out .= 'Als du Ihn bittest die magie zu entfalten, schaut er dich mistrauisch an: `n'.$session['user']['name'].'`&! Sagt dir das Sprichwort `7`i"Von Nichts kommt Nichts!"`i`& etwas? Nun, hier ist es genau so!';
		}
		elseif( $item_count < 2 ){
			$out .= 'Der Runenmeister schaut Dich an und fragt: `&Mit was willst Du das denn verbinden? Ein einzelner Gegenstand kann seine Kraft doch nicht einfach so vervielfachen oder erweitern!';
		}
		elseif( $session['user']['gold'] < 100 ){
			$out .= 'Der Runenmeister schaut Dich an und fragt: `&Komm wieder, wenn Du meine Dienste bezahlen kannst! Ein Versuch kostet Dich 100 Gold!';
		}
		else{
			$session['user']['gold'] -= 100;
			$out .= 'Der Runenmeister nimmt dir 100Gold und die '.$item_count.' Dinge ab und verschwindet mit den Worten: `&Mal sehen, ob dir die Götter wohl gesonnen sind!`^`n`n';
			$cbo1 = item_get_combo($item[0]['tpl'],$item[1]['tpl'],$item[2]['tpl'],ITEM_COMBO_RUNES);
			if( $cbo1 ){
				if( $item_count > 3 ){
					$cbo2 = item_get_combo($cbo1['result'],$item[3]['tpl'],'',ITEM_COMBO_RUNES);
					if( $cbo2 ){
						$newitem = $cbo2['result'];
					}
					else{
						$cantmix = 1;
					}
				}		
				else{
					if( strpos($cbo1['result'], 'r_mix') === false ){						
						$newitem = $cbo1['result'];	
					}
					else{
						$cantmix = 1;
					}
				}
			}
			else{
				$cantmix = 1;
			}
			
			if( $cantmix ){
				$out .= 'Nach einiger Zeit kommt er heraus und gibt dir die '.$item_count.' Dinge wieder:`n';
				$out .= '`&Diese Kombination ist wertlos!';
			}
			else{
				
				$out .= 'Nach einiger Zeit kommt er heraus und sagt:`n';
				$out .= '`&Die Kombination:`n';
				$noresult = 0;
				
				for( $i=0;$i<$item_count;$i++ ){
					$it = item_get ('id='.$item[$i]['id'], false, $what='name');;
					$in .= $item[$i]['id'];
					if( $i < ($item_count-1) ){
						$in .= ',';
						$runename .= substr(str_replace(' - Rune', '', $it['name']),0,3);
					}	
					$out .= '`^-`%'.$it['name'].'`n';
				}
				
				
				if( strpos($newitem, 'r_amrup_') !== false ){
					$add = str_replace('r_amrup_','',$newitem);
					$amor = item_get ('id='.$item[3]['id'], false, $what='value1, name');
					if( $amor ){
						$amor['name'] .= ' `&[`q'.$runename.'`&]';
						$amor['value1'] += $add;
						item_set('id='.$item[3]['id'],$amor);
						$out .= '`^verbesserte deine Rüstung um `#'.$add.'`^ Verteidigungsspunkte!`n';
						$out .= 'Als du genauer hinschaust, bemerkst du, dass die Schriftzeichen in deine Rüstung eingebrannt sind.';
					}else{
						die('FEHLER! Call alucard 0900/232323:>');
					}
					$in = str_replace(','.$item[3]['id'],'',$in);
				}
				elseif( strpos($newitem, 'r_wpnup_') !== false ){
					$add = str_replace('r_wpnup_','',$newitem);
					$weapon = item_get ('id='.$item[3]['id'], false, $what='value1, name');
					if( $weapon ){
						$weapon['name'] .= ' `&[`q'.$runename.'`&]';
						$weapon['value1'] += $add;
						item_set('id='.$item[3]['id'],$weapon);
						$out .= '`^verbesserte deine Waffe um `#'.$add.'`^ Schadenspunkte!`n';
						$out .= 'Als du genauer hinschaust, bemerkst du, dass die Schriftzeichen in deine Waffe eingebrannt sind.';
					}else{
						die('FEHLER! Call alucard 0900/232323:>');
					}
					$in = str_replace(','.$item[3]['id'],'',$in);
					
				}
				elseif( strpos($newitem, 'r_cmup_') !== false ){
					$add = str_replace('r_cmup_','',$newitem);
					$session['user']['charm'] += $add;
					$out .= '`^erhöhte Deinen Charme um `#'.$add.'`^ Punkte!';
				}
				elseif( strpos($newitem, 'r_lpup_') !== false ){
					$add = str_replace('r_lpup_','',$newitem);
					$session['user']['maxhitpoints'] += $add;
					$session['user']['hitpoints']    += $add;
					$out .= '`^erhöhte Deine permanenten Lebenspunkte um `#'.$add.'`^!';
				}
				else{				
					$sql = 'SELECT * FROM items_tpl WHERE tpl_id="'.addslashes($newitem).'"';
					$res = db_query( $sql );
					$newitem = db_fetch_assoc($res);
					
					$rune = db_fetch_assoc(db_query('SELECT id FROM '.RUNE_EI_TABLE.' WHERE tpl_id="'.$newitem['tpl_id'].'" LIMIT 1'));
					if( $rune ){
						$ident	  	= user_get_aei('runes_ident');
						$ident  	= explode(';',$ident['runes_ident']);
						if( !array_search($rune[ 'id' ], $ident) ){
							$noresult = 1;
							$out .= '`&würde einen Gegenstand hervorbringen, den du nicht beherrschst!`n`^Er gibt dir die Ausgangsgegenstände wieder!';
						}
					}
					
					if( !$noresult ){
						$out .= '`^brachte ein(e): `4'.$newitem['tpl_name'].' `^hervor!';				
						item_add( $session['user']['acctid'], 0, true, $newitem );
					}
				}
				if( !$noresult ){
					item_delete('id IN ('.$in.')');
				}
			}
		}
		
	break;
	
	
	case 'showknown':
		addnav('Zurück','runemaster.php?op=runes');
		
			
		$ident	  	= user_get_aei('runes_ident');
		$ident  	= explode(';',$ident['runes_ident']);
		sort( $ident, SORT_NUMERIC);
		reset($ident);
		if( count($ident) > 1 ){
			$out .= '`c`n`bDir sind folgende Runen bekannt:`n`n`b';	
			$out .= '<table style="width:400px" cellspacing="0" cellpadding="0"><tr class="trhead">
						<td colspan="3" align="center">`bRunen`b</td>		
					</tr>';
			
			foreach( $ident as $rid ){
				if( $rid > 0 ){
					$style = ($style == 'trlight' ? 'trdark' : 'trlight');
					$sql = 'SELECT * FROM '.RUNE_EI_TABLE.' WHERE id='.$rid;
					$res = db_query( $sql );
					$rune = db_fetch_assoc( $res );
					if( $rune ){
						$out .= '<tr class="'.$style.'"><td rowspan="5" align="center" valign="middle"><img style="display:block;padding-bottom: 5px;" src="./images/runes/'.strtolower($rune['name']).'.png"></td>';
						$out .= '<td>`#Name</td><td>`&'.$rune['name'].'</td></tr>';
						$out .= '<tr class="'.$style.'"><td>`#Häufigkeit</td><td>'.get_seltenheitname($rune['seltenheit']).'</td></tr>';
						$out .= '<tr class="'.$style.'"><td>`#Buchstabe</td><td>`&'.$rune['buchstabe'].'</td></tr>';
						$out .= '<tr class="'.$style.'"><td>`#Ausrichtung</td><td>`&'.$rune['ausrichtung'].'</td></tr>';
						$out .= '<tr class="'.$style.'"><td valign="top">`#Hinweis</td><td valign="top">`&'.$rune['hinweis'].'</td></tr>';
					}
				}
			}
			$out .= '</table>';
		}
		else{
			$out .= '`c`n`bDir sind noch keine Runen bekannt!`b';	
		}
		
	
	break;
	
	
	
	
	case 'identify':
		
		switch( $_GET['subop'] ){
			case 'pay':
				$canpay = false;
				$paytype = '';
				switch( $_GET['pay'] ){					
					case RUNE_IDENTPAY_GOLD:
						$canpay = ( $session['user']['gold'] >= RUNE_IDENTPAY_GOLD_VALUE );
						$paytype = 'Gold';
					break;
					
					case RUNE_IDENTPAY_GEMS:
						$canpay = ( $session['user']['gems'] >= RUNE_IDENTPAY_GEMS_VALUE );
						$paytype = 'Edelstein';
					break;
					
					case RUNE_IDENTPAY_RUNE:
						$canpay = 1;
					break;
				}	
				
				if( $canpay ){
					$out .= 'Du schaust in Deinen '.$paytype.'beutel und merkst, dass Du genug dabei hast!';
					addnav('Runen zeigen','runemaster.php?op=identify&subop=listunknown&pay='.$_GET['pay']);
				}
				else{
					$out .= 'Der Runenmeister beschaut Deinen '.$paytype.'beutel und fängt an zu lachen: `&Soviel hast du doch garnicht bei Dir!';
					addnav('Schade','runemaster.php?op=runes');	
				}
			break;
			
			
			case 'dontpay':
				
				$out .= 'Du sagst ihm, dass Du Ihm nichts geben möchtest und ';
				
				
				if(e_rand(1,100)==23 && empty($session['runemaster_dontpay'])){
					$out .= 'er mustert Dich und überlegt kurz.`n';
					$out .= 'Plötzlich spricht er zu Dir: `&Nun gut zeigt mir Eure Runen!';
					addnav('Habt Dank dafür!','runemaster.php?op=identify&subop=listunknown&pay=0');
				}
				else{
					$out .= 'als er anfängt zu lachen, senkst Du Deinen Kopf und überlegst.';
					addnav('Zurück','runemaster.php?op=runes');
				}	
				$session['runemaster_dontpay'] = '1';
			break;
			
			
			case 'listunknown':
				$res = runes_get_unidentified();
				$out .= 'Du hast folgende, Dir unbekannte Runen, dabei:`n';
				while( ($rune = db_fetch_assoc($res)) ){
					$link = 'runemaster.php?op=identify&subop=doit&pay='.$_GET['pay'].'&itemid='.$rune['id'];
					addnav('',$link);
					$out .= '<a href="'.$link.'">'.$rune['name'].'</a>`n';
				}
				addnav('Ach! Doch nicht', 'runemaster.php?op=runes');			
			break;
			
			
			case 'doit':
				$out .= 'Du gibst Ihm die ';				
				switch( $_GET['pay'] ){					
					case RUNE_IDENTPAY_GOLD:
						$session['user']['gold'] -= RUNE_IDENTPAY_GOLD_VALUE;
						$out .= RUNE_IDENTPAY_GOLD_VALUE.' Goldstücke';
					break;
					
					case RUNE_IDENTPAY_GEMS:
						$session['user']['gems'] -= RUNE_IDENTPAY_GEMS_VALUE; 
						$out .= RUNE_IDENTPAY_GEMS_VALUE.' Edelsteine';
					break;	
					
					case RUNE_IDENTPAY_RUNE:
						item_delete('tpl_id="r_kenaz" AND owner='.$session['user']['acctid'],1);
						$out .= 'Kenaz - Rune';
					break;
				}
				$out .= ', er nimmt die Rune und erklärt Dir folgendes:`n`&';
				
				$rune = item_get('id='.$_GET['itemid'], false, 'value2');
				if( $rune ){
					$identified = runes_identify( $rune['value2'] );
					
					$out .= '<table style="width:400px" cellspacing="0" cellpadding="0">';		
							
					$sql = 'SELECT * FROM '.RUNE_EI_TABLE.' WHERE id='.$rune['value2'];
					$res = db_query( $sql );
					$rune = db_fetch_assoc( $res );
					if( $rune ){
						$out .= '<tr class="trlight"><td rowspan="5" align="center" valign="middle"><img style="display:block;padding-bottom: 5px;" src="./images/runes/'.strtolower($rune['name']).'.png"></td>';
						$out .= '<td>Name</td><td>'.$rune['name'].'</td></tr>';
						$out .= '<tr class="trlight"><td>Häufigkeit</td><td>'.get_seltenheitname($rune['seltenheit']).'</td></tr>';
						$out .= '<tr class="trlight"><td>Buchstabe</td><td>'.$rune['buchstabe'].'</td></tr>';
						$out .= '<tr class="trlight"><td>Ausrichtung</td><td>'.$rune['ausrichtung'].'</td></tr>';
						$out .= '<tr class="trlight"><td valign="top">Hinweis</td><td valign="top">'.$rune['hinweis'].'</td></tr>';
					}
					$out .= '</table>';
					
					if( $identified > 1){
						$out .= '`n`n`%Als Du die Rune in deinem Beutel verstauen willst, bemerkst Du, dank Deines neuerlangten Wissens, dass Du noch ';
						$out .= ($identified-1).' '.$rune['name'].'-Rune'.( $identified > 2 ? 'n':'').' in Deinem Beutel hast!';
						
					}
				}
				
				addnav('Zurück', 'runemaster.php?op=runes');
			break;
			
			
			default:
				$out .= 'Der Mann räuspert sich, schaut Dich an und grinst: `n`&';
				if( runes_get_unidentified_count() ){
					$out .= 'Nun! Auch ich muss leben. Was bietet Ihr mir für die Identifikation? Entweder ihr zahlt mir 7500 Goldstücke für meine Arbeit, oder Ihr entlohnt mich mit zwei Edelsteinen!';
					addnav('Bezahlen');
					addnav(RUNE_IDENTPAY_GEMS_VALUE.' Edelsteine','runemaster.php?op=identify&subop=pay&pay='.RUNE_IDENTPAY_GEMS);
					addnav(RUNE_IDENTPAY_GOLD_VALUE.' Gold','runemaster.php?op=identify&subop=pay&pay='.RUNE_IDENTPAY_GOLD);
					if( item_count('tpl_id="r_kenaz" AND owner='.$session['user']['acctid']) > 0 ){
						addnav('Kenaz - Rune','runemaster.php?op=identify&subop=pay&pay='.RUNE_IDENTPAY_RUNE);	
						$out .= ' Eine Kenaz - Rune würde ich auch als Bezahlung akzeptieren!';
					}
					
					addnav('Ich bezahl nix','runemaster.php?op=identify&subop=dontpay');
				}
				else{
					$out .= 'Ich spüre keine Energie an Dir, die Du noch nicht beherrschst. Schau doch selbst!`n`^Er zeigt auf deinen Beutel und du schaust hinein. Tatsächlich, alle Runen die sich darin befinden sind dir wohl bekannt.';
					addnav('Zurück','runemaster.php?op=runes');
				}				
			break;
		}
		
	break;
	



/*RABE*/
	case 'raven':
		$out .= 'Du streckst dem Raben Deinen Zeigefinger entgegen';	
		switch( e_rand(1,25) ){
			case 1:
			case 2:
			case 3:
			case 4:
			case 5:
				$out .= ' und kraulst ihn am Gropf. Der Rabe krächzt und spuckt Dir `#einen Edelstein`^ auf die Hand.`n';
				$out .= 'Aus Freude über den Edelstein kraulst Du ihn noch etwas und vergisst dabei die Zeit. Du verlierst einen Waldkampf!';
				$session['user']['gems']++;
				$session['user']['turns']--;
				addnav('Dem Meister zuwenden','runemaster.php?op=master');
			break;
			
			case 6:
			case 7:
			case 8:
			case 9:
			case 10:
				$out .= ' und piekst ihm grob in die Seite. Der Rabe flattert auf und hackt Dir mit seinem Schnabel verärgert ein Auge aus.`n';
				$out .= 'Voller Schmerzen hälst Du Dir Deine Augenhöle. ';
				if( $session['user']['maxhitpoints'] > 1){
					$out .= 'Du `4verlierst`^ einen permanenten Lebenspunkt!';
					$session['user']['maxhitpoints']--;
					$session['user']['turns']--;
				}
				addnav('Dem Meister zuwenden','runemaster.php?op=master');
			break;
			
			case 11:
			case 12:
			case 13:
			case 14:
			case 15:
				$out .= ' und streichelst sein schimmerndes Gefieder. Die Augen des Raben blitzen auf.`n';
				$out .= '`$Du erhälst einen permanenten Lebenspunkt dazu!';
				$session['user']['maxhitpoints']++;
				$session['user']['turns']--;
				addnav('Dem Meister zuwenden','runemaster.php?op=master');
			break;
			
			case 16:
			case 17:
			case 18:
			case 19:
			case 20:
				$out .= ' und kratzt ihm sanft am Kopf. Der Rabe schließt genüsslich die Augen.`n';
				$out .= '`&Du erhälst einen Charmepunkt!';
				$session['user']['charm']++;
				$session['user']['turns']--;
				addnav('Dem Meister zuwenden','runemaster.php?op=master');
			break;
			
			case 21:
				$out .= ' und piekst dem Raben ins Auge. Der Rabe krächzt laut vor Schmerzen und flattert los.`n';
				$out .= 'Die Zeichen auf den Steinen fangen an zu glühen, als plötzlich ein gewaltiger Hammer auf Dich nieder fährt und Dich direkt zu Ramius befördert!`n';
				$out .= 'Du fielst dem Zorn eines Mächtigen Gottes zum Opfer.`nEin eisiger Hauch des Vergessens umhüllt Dich!';
				$session['user']['turns']--;
				killplayer(0,5,0,"");
				addnav('Verdammt!','shades.php');
			break;
			
			case 22:
			case 23:
			case 24:
			case 25:
				$out .= ' und zwickst den Raben am Fuß. Dem Raben missfällt das sehr und er kratzt Deine Hand.`n';
				$out .= '`&Du verlierst einen Charmepunkt!';
				$session['user']['charm']--;
				$session['user']['turns']--;
				addnav('Dem Meister zuwenden','runemaster.php?op=master');
			break;			
		}	
	break;
	
	default:
		$out .= '`4`c`bBADNAV`c`b';
		addnav('Meister','runemaster.php?op=master');
	break;

}


output( $out.'`0' );
page_footer();
?>
