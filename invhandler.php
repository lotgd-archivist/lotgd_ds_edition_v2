<?php
// neuer Itemhandler für Drachenserver-Itemsystem
// by talion (t@ssilo.de)

require_once('common.php');

$id = (int)$_REQUEST['id'];
$ret = $_REQUEST['ret'];

$base_link = 'invhandler.php?ret='.urlencode($ret).'&id='.$id;

if ( isset($id) ){
	$item = item_get('id='.$id);
}


page_header('Inventar');

switch($_GET['op']) {
	
	// Inventar, Benutzen
	case 'use':
		
		$item_hook_info['link'] = $base_link;
		$item_hook_info['ret'] = $ret;
		
		item_load_hook($item['use_hook'],'use',$item);
		
		break;
	
	// Wegwerfen	
	case 'wegw':	
		
		if($_GET['act'] != 'ok') {
			output('`QBist du dir sicher, '.$item['name'].'`Q unwiederbringlich aufzugeben?');		
			
			addnav('Nein, zurück!',$ret);
			
			addnav('Ja, weg damit!',$base_link.'&op=wegw&act=ok');
		}
		else {
			
			output('`QDu schleppst '.$item['name'].'`Q in eine dunkle Seitengasse und lässt es dort stehen und liegen. Da wird sich schon jemand drum kümmern..');		
			
			item_delete('id='.$id);
			
			addnav('Zum Inventar',$ret);		
			
		}		
	
		break;
	
	// Einlagern in Haus o. Gemach	
	case 'einl':	
		
		if($_GET['act'] == 'house') {
		
			$sql = 'SELECT k.*,h.status,h.houseid,h.owner,h.housename FROM keylist k LEFT JOIN houses h ON h.houseid=k.value1 WHERE k.owner='.$session['user']['acctid'].' ORDER BY id ASC';
			$res = db_query($sql);
				
			while($k = db_fetch_assoc($res)) {
				$link = $base_link.'&op=einl&act=ok&housenr='.$k['houseid'];
				output('<a href="'.$link.'">'.$k['housename'].'</a>`n',true);
				addnav('',$link);			
	
			}
			addnav('Zurück','invhandler.php?op=house&id='.$id);
			
		}
		elseif($_GET['act'] == 'private') {
			
			output('`QDu besitzt Schlüssel zu Privatgemächern in diesen Häusern:`n`n');
			
			$sql = 'SELECT i.*,h.status,h.houseid,h.owner,h.housename FROM items i LEFT JOIN houses h ON h.houseid=i.value1 WHERE i.name="'.HOUSES_PRIVATE_OI_NAME.'" AND i.owner='.$session['user']['acctid'].' AND i.value1!='.$session['user']['house'].' ORDER BY id ASC';
			$res = db_query($sql);
			
			if($session['user']['house']) {
				
				$link = $base_link.'&op=einl&act=ok&housenr='.$session['user']['house'].'&private='.$session['user']['acctid'];
				output('<a href="'.$link.'">Privatgemach in eigenem Haus</a>`n',true);
				addnav('',$link);
				
			}
								
			while($k = db_fetch_assoc($res)) {
				$link = $base_link.'&op=einl&act=ok&housenr='.$k['houseid'].'&private='.$session['user']['acctid'];
				output('<a href="'.$link.'">'.$k['housename'].'</a>`n',true);
				addnav('',$link);			
	
			}
			addnav('Zurück',$base_link.'&op=einl');
			
		}
		elseif($_GET['act'] == 'ok') {
			
			$housenr = (int)$_GET['housenr'];
			$private = (int)$_GET['private'];
			
			// Check auf Gesamtzahl dieses Stücks
			$max_count = $item['deposit'.($private ? '_private' : '')];
			$count = item_count( ' deposit1 = '.$housenr.' AND deposit2 = '.$private.' AND tpl_id="'.$item['tpl_id'].'"' );
			
			if($count >= $max_count) {
				output('`QDu darfst von diesem edlen Stück maximal '.$max_count.' Exemplare eingelagert haben!');
			}
			else {
			
				$sql = 'SELECT status FROM houses WHERE houseid='.$housenr;
				$res = db_query($sql);
				$house = db_fetch_assoc($res);
							
				if($private) {
					$max_count = get_max_furniture($house['status'],true);
				}
				else {
					$max_count = get_max_furniture($house['status']);
				}
				
				$count = item_count(' deposit1 = '.$housenr.' AND deposit2 = '.$private);
							
				if($count > $max_count) {
					output('`QDu hast dort bereits `q'.$count.'`Q Möbel deponiert. Mehr passt einfach nicht rein!');
				}
				else {
					output("`QDu suchst für `q$item[name]`Q einen Ehrenplatz in deinem Haus, an dem `q$item[name]`Q von jetzt an den Staub fangen wird.");
										
					item_set(' id='.$item['id'] , array('deposit1'=>$housenr,'deposit2'=>$private) );
				}
			}			
					
		}
		else {
			
			output('`QWohin willst du `q'.$item['name'].'`Q bringen?');
			
			if($session['user']['house']) {addnav('Ins Haus',$base_link.'&op=einl&act=ok&housenr='.$session['user']['house']);}
			if($session['user']['house'] || db_num_rows(db_query("SELECT i.id FROM items i WHERE i.name='".HOUSES_PRIVATE_OI_NAME."' AND i.owner=".$session['user']['acctid']." AND i.value1!=".$session['user']['house'])) > 0) {
				addnav('In Privatgemächer',$base_link.'&op=einl&act=private');
			}
			
		}
		
		addnav('Zum Inventar',$ret);
	
		break;
	
	// Auslagern aus Haus o. Gemach	
	case 'ausl':
		
		output('`QDu packst '.$item['name'].'`Q wieder in dein Inventar.');
		
		item_set('id='.$id,array('deposit1'=>0,'deposit2'=>0) );
		
		addnav('Zum Inventar',$ret);
		
		break;
		
	// Waffe, Rüstung o.ä. anlegen
	case 'ausr':
		
		if($item['equip'] == ITEM_EQUIP_WEAPON) {
			
			$w_old = item_set_weapon($item['name'],$item['value1'],$item['gold'],$id);
			
			$old_name = $w_old['name'];
			
			$old_attack = $session['user']['attack'] - $session['user']['weapondmg'] + $w_old['tpl_value1'];
						
			output('`QDu tauschst `q'.$old_name.'`Q gegen '.$item['name'].'. 
					Dein Angriff verändert sich dadurch von '.$old_attack.' auf '.$session['user']['attack'].'!');
						
		}		
		
		else if($item['equip'] == ITEM_EQUIP_ARMOR) {
			
			$a_old = item_set_armor($item['name'],$item['value1'],$item['gold'],$id);
			
			$old_name = $a_old['name'];
			
			$old_defence = $session['user']['defence'] - $session['user']['armordef'] + $a_old['tpl_value1'];
						
			output('`QDu tauschst `q'.$old_name.'`Q gegen '.$item['name'].'. 
					Deine Verteidigung verändert sich dadurch von '.$old_defence.' auf '.$session['user']['defence'].'!');
						
		}		
		
		addnav('Zum Inventar',$ret);
		
		break;
	
	// Angelegtes Item ablegen und in Invent zurückpacken	
	case 'abl':
		
		if($_GET['what'] != '') {
			$what = $_GET['what'];
		}
		else {
			if($item['equip'] == ITEM_EQUIP_WEAPON) {
				$what = 'weapon';
			}
			else if($item['equip'] == ITEM_EQUIP_ARMOR) {
				$what = 'armor';
			}
		}		
	
			
		if($what == 'weapon') {
			
			$old = $session['user']['attack'];
				
			// ohne Params, um Fists zu setzen
			$w_old = item_set_weapon();
			
			$old_name = $w_old['name'];
			
			output('`QDu legst `q'.$old_name.'`Q ab. 
					Dein Angriff verändert sich dadurch von '.$old.' auf '.$session['user']['attack'].'!');
			
		}
		
		else if($what == 'armor') {
			
			$old = $session['user']['defence'];
				
			// ohne Params, um T-Shirt zu setzen
			$a_old = item_set_armor();
			
			$old_name = $a_old['name'];
			
			output('`QDu legst `q'.$old_name.'`Q ab. 
					Deine Verteidigung verändert sich dadurch von '.$old.' auf '.$session['user']['defence'].'!');
			
		}
		
		addnav('Zum Inventar',$ret);		
				
		break;

	
}

page_footer();
?>