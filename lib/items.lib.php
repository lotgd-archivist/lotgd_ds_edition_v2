<?php
/**
* items.lib.php: LIB-Datei des neuen Drachenserver-Itemsystems. Enthält Basisfunktionen und Konstantendefs
* @author talion <t@ssilo.de>
* @version DS-E V/2
*/

define('ITEM_BUFF_NEWDAY',1);
define('ITEM_BUFF_FIGHT',2);
define('ITEM_BUFF_USE',4);
define('ITEM_BUFF_PET',8);

define('ITEM_COMBO_NEWDAY',1);
define('ITEM_COMBO_ALCHEMY',2);
define('ITEM_COMBO_RUNES',4);

define('ITEM_OWNER_VENDOR',1234567);
define('ITEM_OWNER_SPELLSHOP',1234568);
define('ITEM_OWNER_GUILD',1234569);

define('ITEM_EQUIP_WEAPON',1);
define('ITEM_EQUIP_ARMOR',2);

// deposit1
define('ITEM_LOC_EQUIPPED',9999999);
// deposit2
define('ITEM_LOC_GUILDHALL',1234567);
define('ITEM_LOC_GUILDEXT',1234568);

define('ITEM_MOD_PATH','item_modules/');

define('ITEMS_TABLE','items');

// Array: Enthält Informationen für einen evtl. Hook
$item_hook_info = array();
// Array: Enthält Referenz auf Item für einen evtl. Code-Hook
$hook_item = array();

/**
* @author talion
* @desc Lädt Moduldatei und führt Hook-Funktion aus bzw. führt Codehook aus
*			Funktioniert auch für Kombo-Hooks
* @param string Name des Itemmoduls (_codehook_ bei Codehooks)
* @param string Der Hook-Case welcher ausgeführt wird
* @param array Assoziativer Array mit Daten der an den Hook übergeben wird, also entweder Item oder Kombo
* @return mixed Rückgabe der jeweiligen Hook-Funktion bzw. false
*/
function item_load_hook ( $hook , $type , &$item ) {
	
	// Wenn Codehook
	if($hook == '_codehook_') {
		
		$GLOBALS['hook_item'] = &$item;
				
		$str_code = 'global $item_hook_info,$hook_item; '.$item['hookcode'];
		
		if(strlen($str_code) > 3 && strpos($str_code,';')) {
			$bool_correct = true;
			$bool_correct = eval( $str_code );
			
			if(false === $bool_correct) {
				
				output('`n`n`b`$FEHLER in Hook-Ausführung: '.$hook.' '.$type.'`0`n`n');
				
			}
			
			return($bool_correct);
		
		}
		else {
			return(false);
		}
		
		return(true);
		
	}
	
	$func = $hook.'_hook_process';
	
	if( !function_exists($func) ) { 
	
		$path = ITEM_MOD_PATH . $hook . '.php';
								
		if( !is_file($path) ) { return(false); }
		
		require_once( $path );
		
	}
	
	$result = $func( $type , $item );
			
	return ( $result );
	
}

/**
* @author talion
* @desc Ermittelt einzelne Itemschablone
* @param string SQL-WHERE String
* @param string SQL-String, Welche Felder sollen selektiert werden (Optional, Standard auf alle)
* @return mixed Assoz. Array mit Schablone bzw. false, wenn keine Schablone gefunden
*/
function item_get_tpl ($where,$what='*') {
	
	$sql = 'SELECT '.$what.' FROM items_tpl WHERE '.$where;
		
	$sql .= ' LIMIT 1';
	
	$res = db_query($sql);
	
	if(db_num_rows($res)) {
		return(db_fetch_assoc($res));
	}
	else {
		return(false);
	}
	
}

/**
* @author talion
* @desc Ermittelt Itemkombo, in der angegebene Items enthalten sind. Reihenfolge ist wichtig!
* @param string ItemTPLid 1
* @param string ItemTPLid 2
* @param string ItemTPLid 3
* @param int Der Kombo-Typ (Alchemie, Newday..) bzw. 0 wenn Typ keine Rolle spielt
* @return mixed Assoz. Array mit Kombo bzw. false, wenn keine Kombo gefunden
*/
function item_get_combo ($id1,$id2,$id3,$type) {
	
	global $session;
	
	$sql = 'SELECT * FROM items_combos WHERE '.($type > 0 ? 'type='.$type.' AND ': '').'
				( id1 = "'.addslashes($id1).'" ) AND ( id2 = "'.addslashes($id2).'" ) AND ( id3 = "'.addslashes($id3).'" )';
	$res = db_query($sql);
	
	if($session['user']['superuser'] >= 4) {
		output($sql);
	}
	
	if(db_num_rows($res)) {
		return(db_fetch_assoc($res));
	}
	else {
		return(false);
	}
	
}

/**
* @author talion
* @desc Ermittelt ein einzelnes Item (sowie dazugehörige Schablone)
* @param string SQL-WHERE-String: Vorsicht bei Spalten, die sowohl in items_tpl als auch items vorhanden sind!
*					Für items kann Alias 'i', für items_tpl 'it' verwendet werden.
* @param bool Schablone mit abrufen ja / nein. Optional, Standard auf true. Muss mit abgerufen werden, wenn nach
				Schablonenwerten im 1. Param gesucht wird!
* @param string SQL-String, welche Felder sollen abgerufen werden. (Optional, Standard auf alle)
* @return mixed Assoz. Array mit Item bzw. false, wenn kein Item gefunden
*/
function item_get ($where, $tpl=true, $what='*') {
	
	$sql = 'SELECT '.$what.' FROM '.ITEMS_TABLE.' i'
			.($tpl ? ' LEFT JOIN items_tpl it USING( tpl_id ) ' : '').'
			WHERE '.$where;
			
	$sql .= ' LIMIT 1 ';
	
	$res = db_query($sql);
	
	if(db_num_rows($res)) {
		return(db_fetch_assoc($res));
	}
	else {
		return(false);
	}
	
}

/**
* @author talion
* @desc Ermittelt eine Liste von Items (sowie dazugehörige Schablonen)
* @param string SQL-WHERE-String: Vorsicht bei Spalten, die sowohl in items_tpl als auch items vorhanden sind!
*					Für items kann Alias 'i', für items_tpl 'it' verwendet werden.
* @param string Zusätzliche SQL-Bedingungen / Anweisungen (Limit o.ä.) (Optional, Standard keine)
* @param bool Schablone mit abrufen ja / nein. Optional, Standard auf true. Muss mit abgerufen werden, wenn nach
				Schablonenwerten im 1. Param gesucht wird!
* @param string SQL-String, welche Felder sollen abgerufen werden. (Optional, Standard auf alle)
* @return mixed SQL-Result
*/
function item_list_get ($where, $extra='', $tpl=true, $what='*') {
	
	$sql = 'SELECT '.$what.' FROM '.ITEMS_TABLE.' i '
			.($tpl ? ' LEFT JOIN items_tpl it USING( tpl_id ) ' : '').'
			WHERE ';
	
	$sql .= $where.' ';
	
	$sql .= $extra;
	
	$res = db_query($sql);
	
	return( $res );
	
}


/**
* @author talion
* @desc Ermittelt eine Liste von Itemschablonen
* @param string SQL-WHERE-String
* @param string Zusätzliche SQL-Bedingungen / Anweisungen (Limit o.ä.) (Optional, Standard keine)
* @return mixed SQL-Result
*/
function item_tpl_list_get ($where, $extra='') {
	
	$sql = 'SELECT it.* FROM items_tpl it
			WHERE ';
		
	$sql .= $where.' ';
	
	$sql .= $extra;
	
	$res = db_query($sql);
	
	return( $res );
	
}

/**
* @author talion
* @desc Ruft Liste mit ItemBuffs ab OHNE sie anzuwenden
* @param int Typenwert des Buffs (Newday, fight..) bzw. 0 wenn egal
* @param string CSV-Liste der Buff-IDs 
* @return array Buffliste
*/
function item_get_buffs ($type , $buff_ids) {
	
	global $session;
	
	$buffs = array();
	
	// Komma vorne dranklemmen, falls nicht vorhanden
	$buff_ids = (substr($buff_ids,0,1) != ',' && strlen($buff_ids > 0) ? ',' : '') . $buff_ids;
	
	// BUFF-Liste abrufen
	if(sizeof($buff_ids > 1)) {
		$sql = 'SELECT * FROM items_buffs WHERE '.($type > 0 ? 'type='.$type.' AND ' : '').' id IN (-1'.$buff_ids.')';
		$res = db_query($sql);
	}
	
	if(db_num_rows($res)) {	
	
		while($b = db_fetch_assoc($res)) {
			
			$buffs[] = $b;		
							
		}
		
	}
	
	return($buffs);
	
}

/**
* @author talion
* @desc Ruft Liste mit ItemBuffs ab und wendet sie auf Spieler an
* @param int Typenwert des Buffs (Newday, fight..) bzw. 0 wenn egal
* @param mixed CSV-Liste der Buff-IDs ODER Array mit Buffliste 
* @param int Optionale Accountid, ansonsten session_user
* @return -
*/
function item_set_buffs ($type , $buff_ids , $acctid=0) {
	
	global $session;
	
	$acctid = ( $acctid == 0 ? $session['user']['acctid'] : $acctid );
	
	// BUFF-Liste abrufen
	if(is_string($buff_ids)) {
		$buffs = item_get_buffs($type,$buff_ids);
	}
	else {
		$buffs = $buff_ids;
	}
			
	if(is_array($buffs)) {	
		foreach($buffs as $b) {
			
			unset($b['buff_name']);
			unset($b['id']);
			unset($b['type']);
			
			buff_add($b);
			
		}
	}
	
}

/**
* @author talion
* @desc Ermittelt die aktuell in Umlauf befindliche Anzahl von Items
* @param string SQL-WHERE-String: Vorsicht bei Spalten, die sowohl in items_tpl als auch items vorhanden sind!
*					Für items kann Alias 'i', für items_tpl 'it' verwendet werden.
* @param bool Schablone mit abrufen ja / nein. Wenn nach TPL-Werten gesucht werden soll, muss Param true sein!
*				(Optional, Standard false)
* @return int Anzahl
*/
function item_count ( $where , $tpl=false ) {
	global $session;
	
	$tpl_id = (int)$tpl_id;
	
	$sql = 'SELECT COUNT(*) AS a FROM '.ITEMS_TABLE.' i '.($tpl ? ' LEFT JOIN items_tpl it USING(tpl_id) ' : '').'
			 WHERE '.$where;
	$res = db_query($sql);
	$count = mysql_fetch_row($res);
	
	return($count[0]);		
	
}

/**
* @author talion
* @desc Fügt ein Item zu Inventar eines Users hinzu, prüft auch auf evtl. Begrenzungen
* @param int Accountid des neuen Besitzers
* @param string ID der Schablone
* @param bool Mit item_info überschreiben (true) oder nur diese Info an sich verwenden (false) ?
* @param array Assoz. Array (Feldname => Inhalt)
*				 Dient zum Überschreiben der Schablonenwerte. Namenskonvention: tpl_...., (tpl_value1 etc.) bis auf deposit
* @param bool Wenn true, wird auf versch. Voraussetzungen geprüft (z.B. max. Anzahl)
* @return ID des neu eingefügten Items bzw. false bei Fehler
*/
function item_add ( $acctid, $item, $overwrite = true, $item_info=array(), $check = true ) {
	global $session;
	
	if(!$acctid) {return(false);}
	
	if( $overwrite ) 
	{
		// Hinterer Wert überschreibt vorderen	
		$item_info = array_merge ( item_get_tpl( ' tpl_id="'.$item.'" ',' tpl_id,tpl_name,tpl_description,tpl_value1,tpl_value2,tpl_hvalue,tpl_hvalue2,tpl_gold,tpl_gems ' )
								 , $item_info );
	}
			
	if( $item_info['tpl_id'] ) 
	{
		
		// Prüfungen
		if ( $check ) 
		{
			// Wenn max. Anzahl gegeben, darauf prüfen
			if( $item_info ['maxcount'] > 0 )
			{
				
				$count = item_count( 'tpl_id="'.$item_info['tpl_id'].'"' );
				
				if( $item_info ['maxcount'] <= $count ) { return(false); }
				
			}
		}
	
		$sql = 'INSERT INTO '.ITEMS_TABLE.' 
					(	
						name, tpl_id, owner, description, value1, value2, hvalue,
						hvalue2, gold, gems, deposit1, deposit2 
					) 
					VALUES
					(
						"'.addslashes($item_info['tpl_name']).'",
						"'.$item_info['tpl_id'].'",
						"'.$acctid.'",
						"'.addslashes($item_info['tpl_description']).'",
						"'.$item_info['tpl_value1'].'",
						"'.$item_info['tpl_value2'].'",
						"'.$item_info['tpl_hvalue'].'",
						"'.$item_info['tpl_hvalue2'].'",
						"'.$item_info['tpl_gold'].'",
						"'.$item_info['tpl_gems'].'",
						"'.$item_info['deposit1'].'",
						"'.$item_info['deposit2'].'"
					)';
	
					
		db_query($sql);
		
		if(db_affected_rows()) {
			return(db_insert_id());
		}
	}
	
	return(false);
	
}

/**
* @author talion
* @desc Verändert die Werte eines bestehenden Items
* @param string SQL-WHERE Konditionen (Achtung: nur Felder der ITEMS-Table verfügbar!)
* @param array Assoz. Array (Feldname => Inhalt)
*				 Enthält zu verändernde Werte.
* @param bool Wenn true, wird auf versch. Voraussetzungen geprüft (z.B. max. Anzahl)
* @return TRUE bei Erfolg, sonst FALSE
*/
function item_set ( $item, $item_info, $check=true ) {
	global $session;	
	
	if( $item == '' ) { return(false); }
					
	if( $check ) 
	{		
		// Wenn max. Anzahl gegeben, darauf prüfen
		/*if( $item_info ['maxcount'] > 0 && $item_info ['tpl_id'] )
		{
			
			$count = item_count( ' tpl_id="'.$item_info['tpl_id'].'"' );
			
			if( $item_info ['maxcount'] <= $count ) { return(false); }
			
		}*/
		
	}
					
	$sql = 'UPDATE '.ITEMS_TABLE.' SET '.
				( isset($item_info['name']) ? 'name="'.addslashes($item_info['name']).'",' : '').
				( isset($item_info['tpl_id']) ? 'tpl_id="'.$item_info['tpl_id'].'",' : '').
				( isset($item_info['owner']) ? 'owner="'.$item_info['owner'].'",' : '').
				( isset($item_info['description']) ? 'description="'.addslashes($item_info['description']).'",' : '').
				( isset($item_info['value1']) ? 'value1="'.$item_info['value1'].'",' : '').
				( isset($item_info['value2']) ? 'value2="'.$item_info['value2'].'",' : '').
				( isset($item_info['hvalue']) ? 'hvalue="'.$item_info['hvalue'].'",' : '').
				( isset($item_info['hvalue2']) ? 'hvalue2="'.$item_info['hvalue2'].'",' : '').
				( isset($item_info['gold']) ? 'gold="'.$item_info['gold'].'",' : '').
				( isset($item_info['gems']) ? 'gems="'.$item_info['gems'].'",' : '').
				( isset($item_info['deposit1']) ? 'deposit1="'.$item_info['deposit1'].'",' : '').
				( isset($item_info['deposit2']) ? 'deposit2="'.$item_info['deposit2'].'",' : '').
			' id=id WHERE ' . $item;
				
	db_query($sql);
	
	if(db_affected_rows()) {
		return(true);
	}	
	
	return(false);
	
}


/**
* @author talion
* @desc Zeigt Inventaransicht eines bestimmten Users, verwendet dazu auch REQUEST-Daten
*		Kümmert sich um Navi und Seitenaufteilung.
* @param string SQL-WHERE - String (Kategorien werden automatisch angefügt)
* @param bool Wenn TRUE, werden Schablonen statt Items abgerufen; Parameter 1 wird als WHERE verwendet
* @param int Handelsmodus 0=keiner, 1=Kauf, 2=Verkauf, 3=Beides
* @param float Rabatt auf Goldpreis
* @param float Rabatt auf Gempreis
* @param string Nichts gefunden Meldung
* @param array ('Linktext'=>'Linkop') um vorgegebene Aktionslinks bei jedem Item zu überschreiben
* @return ID des neu eingefügten Items bzw. false bei Fehler
*/
function item_show_invent ( $where,
							$tpl_only=false,
							$shop=0,
							$gold_rebate=1,
							$gems_rebate=1,
							$nothing_msg='`iKeine Gegenstände gefunden`i',
							$options=array() ) {

	global $session,$SCRIPT_NAME;

	$ret_link = calcreturnpath();
			
	$base_link = preg_replace('/([?&]cat=[0-9]*)|([?&]page=[0-9]*)/','',$ret_link);
	$base_link .= (strpos($base_link,'?') ? '&' : '?');
					
	$cat = (int)$_REQUEST['cat'];
	$page = (int)$_REQUEST['page'];
	
	$style = 'trlight';
	$last_cat = 0;
	$out = '';
		
	if($tpl_only) {		
		$cat_sql = 'SELECT ic.id,ic.class_name
		FROM items_tpl it
		LEFT JOIN items_classes ic ON it.tpl_class=ic.id';
		$count_sql = 'SELECT COUNT(*) FROM items_tpl it WHERE '.$where.($cat > 0 ? ' AND it.tpl_class='.$cat : '');
		
		$data_sql = 'SELECT it.tpl_id, it.tpl_class, it.deposit, it.deposit_private, it.use_hook,
							it.tpl_value1 AS value1, it.tpl_value2 AS value2, 
							it.tpl_hvalue AS hvalue, it.tpl_hvalue2 AS hvalue2, 
							it.tpl_gold AS gold, it.tpl_gems AS gems,
							it.tpl_name AS name, it.tpl_description AS description, 
							ic.class_name,ic.class_value1,ic.class_value2,ic.class_hvalue,ic.class_hvalue2,
							ic.class_gold,ic.class_gems 
					FROM items_tpl it
					LEFT JOIN items_classes ic ON it.tpl_class=ic.id 
					WHERE ';
		$data_sql .= ' '.$where.($cat > 0 ? ' AND it.tpl_class='.$cat : '');
		$data_sql .= ' ORDER BY ic.class_order DESC, ic.class_name ASC, it.tpl_name ASC ';
	}
	else {
		$cat_sql = 'SELECT ic.id,ic.class_name
			FROM '.ITEMS_TABLE.' i
			INNER JOIN items_tpl it ON i.tpl_id = it.tpl_id
			LEFT JOIN items_classes ic ON it.tpl_class=ic.id';
		$count_sql = 'SELECT COUNT(id) FROM '.ITEMS_TABLE.' i LEFT JOIN items_tpl it USING(tpl_id) WHERE '.$where.' AND i.tpl_id != "" '.($cat > 0 ? ' AND it.tpl_class='.$cat : '');
		
		$data_sql = 'SELECT i.*, it.tpl_id, it.tpl_class, it.deposit, it.deposit_private, it.use_hook, it.equip, it.throw,
					ic.class_name,ic.class_value1,ic.class_value2,ic.class_hvalue,ic.class_hvalue2,
					ic.class_gold,ic.class_gems 
				FROM '.ITEMS_TABLE.' i
				LEFT JOIN items_tpl it ON i.tpl_id = it.tpl_id
				LEFT JOIN items_classes ic ON it.tpl_class=ic.id 
				WHERE i.tpl_id!="" AND ';
		$data_sql .= ' '.$where.($cat > 0 ? ' AND it.tpl_class='.$cat : '');
		$data_sql .= ' ORDER BY ic.class_order DESC, ic.class_name ASC, it.tpl_name ASC ';
							
	}
	
	$cat_sql .= ' WHERE '.$where;
	$cat_sql .= ' GROUP BY it.tpl_class ORDER BY ic.class_order DESC, ic.class_name ASC';
	$res = db_query($cat_sql);
	
	if($cat != 0 || db_num_rows($res) > 0) {
	
		addnav('Kategorien');
		addnav( ($cat==0 ? '`^' : '').'Alle',$base_link.'cat=0');
				
		while($c = db_fetch_assoc($res)) {
			
			if($c['id'] == $cat) {
				$out .= '`bKategorie: '.$c['class_name'].'`b`n';
			}
			
			addnav(($c['id'] == $cat ? '`^' : '').$c['class_name'],$base_link.'cat='.$c['id'].'&page=1');
			
		}		
	}
	
	$res = db_query($count_sql);
	$count = mysql_fetch_row($res);
			
	$page = ($page == 0 ? 1 : $page);
	$ipp = ($ipp == 0 ? 10 : $ipp);
			
	$from = ($page-1) * $ipp;	
	$to = $page * $ipp;			
	$to = min($count[0],$to);
	$max_page = ceil($count[0] / $ipp);		
	
	$limit = ' LIMIT '.$from.','.$ipp;		
												
	if($max_page > 0) {
		addnav('Seiten');
		for($i=1; $i<=$max_page; $i++) {
			
			addnav( ($i == $page ? '`^' : '').'Seite '.$i, $base_link.'page='.$i.'&cat='.$cat);
			
		}
	}
				
	$res = db_query($data_sql . $limit);
	
	$count = db_num_rows($res);
	
	$out .= '`c<table>';
	
	if($count == 0) {
		$out .= '<tr><td>'.$nothing_msg.'</td></tr>';
	}
	else {
		
		if(sizeof($options) > 0) {
			
			$options_link = $ret_link;
			$options_link .= (strpos($options_link,'?') ? '&' : '?');
															
		}
				
		while($i = db_fetch_assoc($res)) {
			
			$i['gold'] = round($i['gold'] * $gold_rebate);
			$i['gems'] = round($i['gems'] * $gems_rebate);
			
			$handler_link = 'invhandler.php?ret='.urlencode($ret_link).'&';
			
			if($cat > 0 && $cat != $i['tpl_class']) {
				$show_cat = false;
			}
			else {
				$show_cat = true;
				
				$handler_link .= 'id='.$i['id'].'&';
			}
			
			if($last_cat != $i['tpl_class']) {
				
				//addnav($i['class_name'],$base_link.'cat='.$i['tpl_class']);
				
				$last_cat = $i['tpl_class'];
								
				if($show_cat) {
				
					$out .= '<tr class="trhead"><td colspan="2">`b'.$i['class_name'].'`b</td></tr>';
																				
				}
				
			}
			
			if($show_cat) {
				
				$i['class_gold'] = ($i['class_gold'] == '' ? 'Gold' : $i['class_gold']);
				$i['class_gems'] = ($i['class_gems'] == '' ? 'Edelsteine' : $i['class_gold']);
			
				$style = ($style == 'trlight' ? 'trdark' : 'trlight');
				
				$out .= '<tr class="'.$style.'">';
				
				$out .= '<td align="left">`&`b'.$i['name'].'`b</td>';
				$out .= '<td align="right">`q';
				
				if( ($shop == 1 || $shop == 3) && $i['owner'] != $session['user']['acctid'] ) {
					$out .= 'Vor deinen Augen';
				}		
				else {		
					if($i['deposit1'] == ITEM_LOC_EQUIPPED) {
						$out .= 'Angelegt';
					} 
					else if($i['owner'] == ITEM_OWNER_GUILD) {
						if($i['deposit2'] == ITEM_LOC_GUILDHALL) {
							$out .= 'Gildenhalle';
						}
						else if($i['deposit2'] == ITEM_LOC_GUILDEXT) {
							$out .= 'Gildenräume';
						}
						else {
							$out .= 'Gewölbe der Gilde';
						}
					}
					else if($i['deposit1'] > 0) {
						$out .= 'Haus Nr. '.$i['deposit1'];
						if($i['deposit2'] > 0) {
							$out .= ', Privatgemach';
						}
					}
					else {
						$out .= 'Im Inventar';
					}
				}
				
				$out .= '</td>';
				
				$out .= '</tr><tr class="'.$style.'"><td colspan="2" width="400">`&';
				
				$out .= ($i['description'] != '' ? ''.$i['description'].'`n`&' : '');
				
				if($i['class_value1'] != '') {$out .= ' [ '.$i['class_value1'].' : '.$i['value1'].' ] ';}
				if($i['class_value2'] != '') {$out .= ' [ '.$i['class_value2'].' : '.$i['value2'].' ] ';}
				if($i['class_hvalue'] != '') {$out .= ' [ '.$i['class_hvalue'].' : '.$i['hvalue'].' ] ';}
				if($i['class_hvalue2'] != '') {$out .= ' [ '.$i['class_hvalue2'].' : '.$i['hvalue2'].' ]';}
				
				$out .= '`n'.$i['class_gold'].' : '.$i['gold'].' / ';
				$out .= $i['class_gems'].' : '.$i['gems'].'`n';
				//$out .= '</tr><tr class="style"><td colspan="2">';
				// Mögliche Aktionen
												
				if(sizeof($options) > 0) {
																
					foreach($options as $txt=>$op) {
						$link = $options_link;
						
						$link .= ($op != '' ? 'op='.$op.'&' : '');
						
						$link .= ($tpl_only ? 'tpl_id='.$i['tpl_id'] : 'id='.$i['id']);
						
						$out .= ' [ <a href="'.$link.'">'.$txt.'</a> ] ';
						addnav('',$link);
					}
						
				}				
				else if($shop == 0) {
				
					if($i['deposit'] > 0 || $i['deposit_private'] > 0) {
					
						if( ($i['deposit1'] > 0 && $i['deposit1'] != ITEM_LOC_EQUIPPED && $i['deposit1'] != ITEM_LOC_GUILD) 
							|| $i['deposit2'] > 0) {
							
							$link = $handler_link.'op=ausl';
							
							addnav('',$link);
							$out .= '[ <a href="'.$link.'">Auslagern</a> ] ';	
						}
						else {
							$link = $handler_link.'op=einl';
							
							addnav('',$link);
							$out .= '[ <a href="'.$link.'">Einlagern</a> ] ';
						}
						
					}
					
					if($i['throw'] && $i['deposit1'] != ITEM_LOC_EQUIPPED) {
						$link = $handler_link.'op=wegw';
						
						addnav('',$link);
						$out .= '[ <a href="'.$link.'">Wegwerfen</a> ] ';
					}
					
					if($i['equip']) {
					
						if($i['deposit1'] != ITEM_LOC_EQUIPPED) {
					
							$link = $handler_link.'op=ausr';
							
							addnav('',$link);
							$out .= '[ <a href="'.$link.'">Ausrüsten</a> ] ';
							
						}
						else {
							
							$link = $handler_link.'op=abl';
							
							addnav('',$link);
							$out .= '[ <a href="'.$link.'">Ablegen</a> ] ';
							
						}
					}
					
					if($i['use_hook'] != '' && $i['deposit1'] == 0) {
						$link = $handler_link.'op=use';
						
						addnav('',$link);
						$out .= '[ <a href="'.$link.'">Benutzen</a> ] ';
					}
					
				}
				else {	// shop
					
					if( $i['owner'] != $session['user']['acctid'] && ($shop == 1 || $shop == 3) && $i['deposit1'] != ITEM_LOC_EQUIPPED ) {
						if($session['user']['gold'] >= $i['gold'] && $session['user']['gems'] >= $i['gems']) { 
							$link = $SCRIPT_NAME.'?op=buy_do&gold_r='.$gold_rebate.'&gems_r='.$gems_rebate;
							
							if($tpl_only) {$link .= '&tpl_id='.$i['tpl_id'];}
							else {$link .= '&id='.$i['id'];}
							
							addnav('',$link);
							$out .= '[ <a href="'.$link.'">Kaufen</a> ] ';	
						}
					}
					
					if( $i['owner'] == $session['user']['acctid'] && ($shop == 2 || $shop == 3) && $i['deposit1'] != ITEM_LOC_EQUIPPED ) {
						$link = $SCRIPT_NAME.'?op=sell_do&gold_r='.$gold_rebate.'&gems_r='.$gems_rebate;
						
						$link .= '&id='.$i['id'];
						
						addnav('',$link);
						$out .= '[ <a href="'.$link.'">Verkaufen</a> ] ';	
					}			
					
				}	// END shop
				
				$out .= '</td></tr>';
				
			}	// END show_cat
			
		}	// END while
		
	}	// END items vorhanden
	
	$out .= '</table>`c';
	output($out,true);
	
	
}

/**
* @author talion
* @desc Löscht ein oder mehrere Items.
* @param string SQL-where-String (Nur mit ITEMS-Spalten!)
* @param int LIMIT der Löschung std: 100
* @return TRUE o. FALSE
*/
function item_delete ( $where, $limit=100 ) {
	global $session;
		
	$sql = 'DELETE FROM '.ITEMS_TABLE.' WHERE '.$where.' LIMIT '.$limit;
	
	db_query($sql);
	
//	$session['itemsys_queries'][] = date('His').':'.$sql;
	
	if( db_affected_rows() ) { return(true); }
	
	return(false);
	
}

/**
* @author talion
* @desc Nimmt Änderungen an Werten der Spieler-Ausrüstung (z.Zeit Waffe + Rüstung) vor.
*		Wird von item_set_weapon bzw. item_set_armor gewrappt. Bitte diese Funktionen nutzen!
* @param string 'weapon' für Waffe, 'armor' für Rüstung
* @param string Name des Ausrüstungsgegenstands
* @param int 'Fähigkeit' des Gegenstands (Bei Waffen: Angriffswert)
* @param int Goldwert des Gegenstands
* @param int AccountID. Wenn 0, wird Spielerid verwendet
* @return array Altes Item.
*/
function item_change_equipment ($type,$item_name,$item_skill,$item_value,$acctid) {
	
	global $session;
			
	$item_old = array();
	
	$user = false;
	
	// Spalten / Wertebezeichnungen setzen
	if($type == 'weapon') {
		$e_name = 'weapon';
		$e_skill = 'weapondmg';
		$e_user = 'attack';
		$e_val = 'weaponvalue';
		$e_tpl = 'waffedummy';
	}
	else if($type == 'armor') {
		$e_name = 'armor';
		$e_skill = 'armordef';
		$e_user = 'defence';
		$e_val = 'armorvalue';
		$e_tpl = 'rstdummy';
	}
	
	// Wenn aktueller User betroffen
	if($acctid == 0 || $acctid == $session['user']['acctid']) {
	
		$acctid = $session['user']['acctid'];
		$user = true;
		$item_old['name'] = $session['user'][$e_name];
		$item_old['gold'] = $session['user'][$e_val];
		$item_old['value1'] = $session['user'][$e_skill];
			
	}
	else {	// Wenn and. User betroffen
		
		$sql = 'SELECT '.$e_name.','.$e_skill.','.$e_user.','.$e_val.' FROM accounts WHERE acctid='.$acctid;
		$it = db_fetch_assoc(db_query($sql));
		$item_old['name'] = $it[$e_name];
		$item_old['gold'] = $it[$e_val];
		$item_old['value1'] = $it[$e_skill];		
	}
	
	// Altes Item abrufen
	$item_old = array_merge($item_old,item_get(' name="'.addslashes($item_old['name']).'" AND owner='.$acctid.' AND deposit1='.ITEM_LOC_EQUIPPED));
	
	// Wenn gegeben: Verändern
	if($item_old['id'] > 0) {
						
		$arr_changes = array('deposit1'=>ITEM_LOC_EQUIPPED,'deposit2'=>0,
			'value1'=>($item_skill>-1?$item_skill:$item_old['value1']),
			'gold'=>($item_value>-1?$item_value:$item_old['gold']),
			'name'=>($item_name!=''?$item_name:$item_old['name']),
			);
		
		item_set(' id='.$item_old['id'], $arr_changes);
		
	}
	else {	// Sonst: Neu erstellen		
		
		if($item_old['value1'] > 0 && $item_old['gold'] > 0) {
		
			if($user) {	// Wenn akt. User
				$item_old['name'] = $session['user'][$e_name];
				$item_old['gold'] = $session['user'][$e_value];
				$item_old['value1'] = $session['user'][$e_skill];
			}
			else {	// Sonst: Aus DB holen
				$sql = 'SELECT '.$e_name.' AS name,'.$e_skill.' AS value1,'.$e_value.' AS gold FROM accounts WHERE acctid='.(int)$acctid;
				$res = db_query($sql);				
				$item_old = db_fetch_assoc($res);
			}					
			
			$arr_changes = array('deposit'=>ITEM_LOC_EQUIPPED,'deposit2'=>0,
			'tpl_value1'=>($item_skill>-1?$item_skill:$item_old['value1']),
			'tpl_gold'=>($item_value>-1?$item_value:$item_old['gold']),
			'tpl_name'=>($item_name!=''?$item_name:$item_old['name']),
			);
			
			// ... und hinzufügen
			item_add($session['user']['acctid'],$e_tpl,true,$arr_changes);
		}
		
	}
			
	// Werte bei Spieler setzen
	if($user) {
		
		if($item_name != '') {$session['user'][$e_name] = $item_name;}
		if($item_value > -1) {$session['user'][$e_val] = $item_value;}
		if($item_skill > -1) {
			$session['user'][$e_user] += $item_skill - $session['user'][$e_skill];
			$session['user'][$e_skill] = $item_skill;			
		}
		
	}
	else {	// Bei Fremdaccount
		
		$sql = 'UPDATE accounts SET ';
		
		if($item_name != '') {$sql .= $e_name.' = "'.$item_name.'" ';}
		if($item_value > -1) {$sql .= $e_val.' = "'.$item_value.'" ';}
		if($item_skill > -1) {
			$sql .= $e_user.' = '.$item_skill.' - '.$e_skill.', '.$e_skill.' = '.$item_skill;
		}
		
		$sql .= ' WHERE acctid='.$acctid;
		
		db_query($sql);		
	}
	
	return($item_old);
	
}

/**
* @author talion
* @desc Setzt Spieler-Ausrüstung (z.Zeit Waffe + Rüstung) neu.
*		Wird von item_set_weapon bzw. item_set_armor gewrappt. Bitte diese Funktionen nutzen!
* @param string 'weapon' für Waffe, 'armor' für Rüstung
* @param string Name des Ausrüstungsgegenstands
* @param int 'Fähigkeit' des Gegenstands (Bei Waffen: Angriffswert)
* @param int Goldwert des Gegenstands
* @param int ItemID des zu verwendenden Gegenstands (falls gegeben)
* @param int AccountID. Wenn 0, wird Spielerid verwendet
* @param int Veränderungsmodus: 0 = Standard, 2 = Aktuelle Ausrüstung ersetzen
* @return array Altes Item.
*/
function item_set_equipment ($type,$item_name,$item_skill,$item_value,$item_id,$acctid,$change) {
	
	global $session;
	
	// Wenn Equipment nicht nur editiert werden soll
	if($item_name == '' || $item_skill == -1 || $item_value == -1) {return(false);}
		
	$item_old = array();
	
	$user = false;
	
	// Spalten / Wertebezeichnungen setzen
	if($type == 'weapon') {
		$e_name = 'weapon';
		$e_skill = 'weapondmg';
		$e_user = 'attack';
		$e_val = 'weaponvalue';
		$e_tpl = 'waffedummy';
	}
	else if($type == 'armor') {
		$e_name = 'armor';
		$e_skill = 'armordef';
		$e_user = 'defence';
		$e_val = 'armorvalue';
		$e_tpl = 'rstdummy';
	}
	
	// Wenn aktueller User betroffen
	if($acctid == 0 || $acctid == $session['user']['acctid']) {
	
		$acctid = $session['user']['acctid'];
		$user = true;
		$item_old['name'] = $session['user'][$e_name];
		$item_old['gold'] = $session['user'][$e_val];
		$item_old['value1'] = $session['user'][$e_skill];
			
	}
	else {	// Wenn and. User betroffen
		
		$sql = 'SELECT '.$e_name.','.$e_skill.','.$e_user.','.$e_val.' FROM accounts WHERE acctid='.$acctid;
		$it = db_fetch_assoc(db_query($sql));
		$item_old['name'] = $it[$e_name];
		$item_old['gold'] = $it[$e_val];
		$item_old['value1'] = $it[$e_skill];		
	}
	
	
	if($change == 2) {	// Komplett ersetzen
		
		item_delete(' name="'.addslashes($item_old['name']).'" AND owner='.$acctid.' AND deposit1='.ITEM_LOC_EQUIPPED);
		
	}
	else {
	
		// Altes Item abrufen
		$item_old = array_merge($item_old,item_get(' name="'.addslashes($item_old['name']).'" AND owner='.$acctid.' AND deposit1='.ITEM_LOC_EQUIPPED));
		
		// Wenn gegeben: Verändern
		if($item_old['id'] > 0) {
			
			$deposit = ($change==0 ? 0 : ITEM_LOC_EQUIPPED);
			
			item_set(' id='.$item_old['id'], array('deposit1'=>$deposit,'deposit2'=>0,
										'value1'=>$item_old['value1'],'gold'=>$item_old['gold'],
										'name'=>$item_old['name'] ) );
			
		}
		else {	// Sonst: Neu erstellen		
			
			if($item_old['value1'] > 0 && $item_old['gold'] > 0) {
			
				if($user) {	// Wenn akt. User
					$item_old['tpl_name'] = $session['user'][$e_name];
					$item_old['tpl_gold'] = $session['user'][$e_value];
					$item_old['tpl_value1'] = $session['user'][$e_skill];
				}
				else {	// Sonst: Aus DB holen
					$sql = 'SELECT '.$e_name.' AS tpl_name,'.$e_skill.' AS tpl_value1,'.$e_value.' AS tpl_gold FROM accounts WHERE acctid='.(int)$acctid;
					$res = db_query($sql);				
					$item_old = db_fetch_assoc($res);
				}
				
				$item_old['tpl_name'] = $item_old['tpl_name'];					
				
				// ... und hinzufügen
				item_add($session['user']['acctid'],$e_tpl,true,$item_old);
			}
			
		}
	
	}
			
	// Neues Item setzen, wenn dafür ID gegeben
	if($item_id > 0) {
			
		item_set(' id='.$item_id, array('deposit1'=>ITEM_LOC_EQUIPPED,'deposit2'=>0,
											'value1'=>$item_skill,'gold'=>$item_value,
											'name'=>$item_name ) );
			
	
	}
	// END Items setzen
	
	// Werte bei Spieler setzen
	if($user) {
		
		if($item_name != '') {$session['user'][$e_name] = $item_name;}
		if($item_value > -1) {$session['user'][$e_val] = $item_value;}
		if($item_skill > -1) {
			$session['user'][$e_user] += $item_skill - $session['user'][$e_skill];
			$session['user'][$e_skill] = $item_skill;			
		}
		
	}
	else {	// Bei Fremdaccount
		
		$sql = 'UPDATE accounts SET ';
		
		if($item_name != '') {$sql .= $e_name.' = "'.$item_name.'" ';}
		if($item_value > -1) {$sql .= $e_val.' = "'.$item_value.'" ';}
		if($item_skill > -1) {
			$sql .= $e_user.' = '.$item_skill.' - '.$e_skill.', '.$e_skill.' = '.$item_skill;
		}
		
		$sql .= ' WHERE acctid='.$acctid;
		
		db_query($sql);		
	}
	
	return($item_old);	
	
}

/**
* @author talion
* @desc Setzt Waffe des Spielers und dazugehörige Werte. Wrapper-Funktion für item_set_ + change_equipment
* @param string Name der Waffe
* @param int Angriffswert der Waffe
* @param int Goldwert der Waffe
* @param int ID des Items, das zur Waffe wird (optional)
* @param int AccountID. Wenn 0, wird Spielerid verwendet (Optional)
* @param int Änderungsmodus - 0: Standard, Aktuelle wird ersetzt und in Inventar eingelagert
*								 1: aktuelle wird verändert, 2: aktuelle wird komplett durch neue ersetzt
* @return array Altes Item.
*/
function item_set_weapon ( $item_name='Fists' , $item_attack=0 , $item_value=0 , $item_id=0 , $acctid=0 , $change=0 ) {
		
	$arr_result = array();
	
	if($change == 1) {
		$arr_result = item_change_equipment('weapon',$item_name,$item_attack,$item_value,$acctid);
	}	
	else {
		$arr_result = item_set_equipment('weapon',$item_name,$item_attack,$item_value,$item_id,$acctid,$change);
	}	
		
	return( $arr_result );
	
}

/**
* @author talion
* @desc Setzt Rüstung des Spielers und dazugehörige Werte. Wrapper-Funktion für item_set_ + change_equipment
* @param string Name der Rüstung
* @param int Defwert der Rüstung
* @param int Goldwert der Rüstung
* @param int ID des Items, das zur Rüstung wird (optional)
* @param int AccountID. Wenn 0, wird Spielerid verwendet (Optional)
* @param int Änderungsmodus - 0: Standard, Aktuelle wird ersetzt und in Inventar eingelagert
*								 1: aktuelle wird verändert, 2: aktuelle wird komplett durch neue ersetzt
* @return array Altes Item.
*/
function item_set_armor ( $item_name='T-Shirt' , $item_defence=0 , $item_value=1 , $item_id=0 , $acctid=0 , $change=0 ) {
	
	$arr_result = array();
	
	if($change == 1) {
		$arr_result = item_change_equipment('armor',$item_name,$item_defence,$item_value,$acctid);
	}	
	else {
		$arr_result = item_set_equipment('armor',$item_name,$item_defence,$item_value,$item_id,$acctid,$change);
	}	
		
	return( $arr_result );
	
}

?>
