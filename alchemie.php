<?php
/**
* alchemie.php: Stellt Basisanwendung nebst Userführung für Alchemie / Misch / Komboaktionen 
*				des Dragonslayeredition-Itemsystems bereit. Ist mehr oder minder sehr vielseitig einsetz-
				bar: Häuser, Inventar etc.
* @author talion <t@ssilo.de>
* @version DS-E V/2
*/

require_once('common.php');
page_header('Alchemistischer Schmelztiegel');

output('`&`b`cAlchemistischer Schmelztiegel`b`c`n`n');

// Navitext für 'Beenden'-Button
$str_back_txt = 'Kammer schließen';
// Rückkehr-Link nach Beenden
$ret = $_REQUEST['ret'];

if($session['user']['turns'] <= 0) {
	output('`&Heute bist du leider bereits zu erschöpft, um alchemistische Experimente durchzuführen.');
	addnav($str_back_txt,urldecode($ret));
	page_footer();
	exit;
}

if($_GET['act'] == 'mix') {
	
	$arr_ids = array();
	$str_del_ids = '';
		
	if(sizeof($session['items_alchemie']) > 1) {
		
		$str_del_ids = '-1';
		
		foreach($session['items_alchemie'] as $item) {		
			$str_del_ids .= ','.$item['id'];
		}
		
		$combo = item_get_combo($session['items_alchemie'][0]['tpl_id'],$session['items_alchemie'][1]['tpl_id'],$session['items_alchemie'][2]['tpl_id'],ITEM_COMBO_ALCHEMY);
		
		if($combo['combo_id'] > 0) {

			if(!empty($combo['result'])) {
				$product = item_get_tpl(' tpl_id="'.$combo['result'].'" ');
			}
			
			$item_hook_info['min_chance'] = e_rand(1,255);
			$item_hook_info['victory_msg'] = '`c`b`@Es hat geklappt!`&`b`c`n`n
										Du hast die knifflige alchemistische Prozedur erfolgreich zu Ende gebracht und
										'.$product['tpl_name'].'`& hergestellt!`nWeiter so, Meister!`n
										Du verlierst einen Waldkampf.';
										
			$item_hook_info['fail_msg'] = '`c`b`4Das war wohl nichts!`&`b`c`n`n
										Mitten in der Prozedur rutscht dir ein Kolben aus der Hand und zerspringt auf dem Boden
										in 1000 Scherben! Schade, so gibt das natürlich kein '.$product['tpl_name'].'`&..
										Die Zutaten sind leider nicht mehr zu gebrauchen.`n
										Du verlierst einen Waldkampf.';
			
			if($session['user']['superuser']) {
				output('`nmin_chance: '.$item_hook_info['min_chance'].'`n`n');
			}
			
			$session['user']['turns']--;
			
			if(!empty($combo['hook'])) {
				item_load_hook($combo['hook'],'alchemy',$combo);
			}
			
			if(!$item_hook_info['hookstop'] && is_array($product)) {
															
				if($item_hook_info['min_chance'] < $combo['chance']) {
									
					output($item_hook_info['victory_msg']);
					
					item_add($session['user']['acctid'],0,false,$product);
				}
				else {
									
					output($item_hook_info['fail_msg']);
					
				}
										
				item_delete(' id IN ('.$str_del_ids.') AND owner='.$session['user']['acctid']);
			}
			
			unset($session['items_alchemie']);
            
		}
		else {	// Keine Combo gefunden
			
			output('`&Du versuchst eine halbe Ewigkeit die unterschiedlichen Gegenstände irgendwie in 
					eine sinnvolle Verbindung miteinander zu bringen, doch nichts passiert.');
			
		}	
		
	}
			
	addnav('Zurück','alchemie.php?ret='.urlencode($ret));
	
}
else if($_GET['act'] == 'end') {
	
	unset($session['items_alchemie']);
	
	redirect($ret);
	
}
else if($_GET['act'] == 'empty') {
	
	unset($session['items_alchemie']);
	
	redirect('alchemie.php?ret='.urlencode($ret));
	
}
else if($_GET['act'] == 'insert') {
		
	$item = item_get(' id='.(int)$_GET['id']);	
	$bool_exists = false;	
	
	if($item['id'] > 0) {	
		if(is_array($session['items_alchemie'])) {
			foreach($session['items_alchemie'] as $pos=>$i) {
				if($i['id'] == $item['id']) {
					$bool_exists = true;	
				}
			}	
		}
		if(!$bool_exists) {
			$session['items_alchemie'][] = $item;
		}
	}
	
	redirect('alchemie.php?ret='.urlencode($ret).'&cat='.$_REQUEST['cat'].'&page='.$_REQUEST['page']);
		
}
else if($_GET['act'] == 'change_pos') {
		
	$int_pos = (int)$_GET['pos'];
	$int_new_pos = (int)$_GET['new_pos'];
	
	if(!empty($session['items_alchemie'][$int_pos]) && !empty($session['items_alchemie'][$int_new_pos])) {	
		$arr_item_tmp = $session['items_alchemie'][$int_pos];
		$session['items_alchemie'][$int_pos] = $session['items_alchemie'][$int_new_pos];
		$session['items_alchemie'][$int_new_pos] = $arr_item_tmp;
	}

	redirect('alchemie.php?ret='.urlencode($ret).'&cat='.$_REQUEST['cat'].'&page='.$_REQUEST['page']);
		
}
else if($_GET['act'] == 'out') {
		
	array_splice($session['items_alchemie'],$_GET['pos'],1);	
	
	redirect('alchemie.php?ret='.urlencode($ret).'&cat='.$_REQUEST['cat'].'&page='.$_REQUEST['page']);
		
}
else {

	output('In dieser magischen, eher beengten Kammer, in der sich die Tinkturen und Extrakte bis an die Decke stapeln,
			kannst du alchemistische Experimente durchführen. Auf dem Werktisch ist Platz für bis zu drei Zutaten - die
			richtige Mixtur musst du selbst finden. Dabei spielt natürlich auch die Reihenfolge eine Rolle..`n
			Falls dein Rezept ein Ergebnis hervorbringt, wirst du einen Waldkampf benötigen. Ansonsten kannst du ohne Gefahr
			mischen und versuchen.`n`c');
			
	$str_ids = '0';
	
	// Standard, Inventar mit mögl. Items anzeigen
	if(is_array($session['items_alchemie']) && sizeof($session['items_alchemie']) > 0) {
		
		$str_ids .= ','.implode(',',array_keys($session['items_alchemie']));
		
		output('`&`bBisher im Schmelztiegel:`n`b`i `n');
		
		$int_pos = 0;
		
		foreach($session['items_alchemie'] as $i) {
									
			output(' ~~~~ Zutat '.($int_pos+1).': '.$i['name'].'`&'
				.(!empty($session['items_alchemie'][$int_pos+1]) ? ' [ '.create_lnk('`b&darr;`b','alchemie.php?act=change_pos&pos='.$int_pos.'&new_pos='.($int_pos+1).'&ret='.urlencode($ret).'&cat='.$_REQUEST['cat'].'&page='.$_REQUEST['page']).' ]' : '')
				.(!empty($session['items_alchemie'][$int_pos-1]) ? ' [ '.create_lnk('`b&uarr;`b','alchemie.php?act=change_pos&pos='.$int_pos.'&new_pos='.($int_pos-1).'&ret='.urlencode($ret).'&cat='.$_REQUEST['cat'].'&page='.$_REQUEST['page']).' ]' : '')
				.' [ '.create_lnk('Herausnehmen','alchemie.php?act=out&pos='.$int_pos.'&ret='.urlencode($ret).'&cat='.$_REQUEST['cat'].'&page='.$_REQUEST['page']).' ]`&
				 `n ');
			$int_pos++;			
		}
		output('`i ');
		
		if(sizeof($session['items_alchemie']) > 1) {
			$link = 'alchemie.php?act=mix&ret='.urlencode($ret);
			addnav('',$link);
			output(''.create_lnk('Leeren!','alchemie.php?act=empty&ret='.urlencode($ret)),true);				
			output(' -------------- <a href="'.$link.'">Mischen!</a>',true);
		}
		
		output('`n`n');
	}
	
	if(sizeof($session['items_alchemie']) >= 3) {
		output('`nMehr bringst du in den alchemistischen Schmelztiegel leider nicht hinein!`n`n');
		$options = array(''=>'');
	}
	else {
		$options = array('Mischen'=>'&act=insert&cat='.$_REQUEST['cat'].'&page='.$_REQUEST['page']);
	}
	output('`c');
			
	item_show_invent(' owner='.$session['user']['acctid'].' AND showinvent=1 AND
						i.tpl_id!="alchemtgl" AND ( (deposit1=0 AND
                        deposit2=0) AND alchemy=1 ) AND i.id NOT IN ('.$str_ids.')
						', false, 0, 1, 1,
                        '`iLeider bietet sich kein Gegenstand aus deinem Beutel für eine solche
                        Mischung an..`i',$options);
	
	addnav('Zurück');
	addnav($str_back_txt,'alchemie.php?act=end&ret='.urlencode($ret));
	
}

page_footer();
?>
