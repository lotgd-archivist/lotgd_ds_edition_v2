<?php
/**
* runes.lib.php: Funktionen fürs Runen-addon
* @author Alucard <diablo3-clan[AT]web.de>
* @version DS-E V/2
*/


define(RUNE_DUMMY_TPL,getsetting('runes_dummytpl','r_dummy'));
define(RUNE_EI_TABLE,'runes_extrainfo');

define(RUNE_IDENTPAY_NONE, 0);
define(RUNE_IDENTPAY_GOLD, 1);
define(RUNE_IDENTPAY_GEMS, 2);
define(RUNE_IDENTPAY_RUNE, 3);//not impl.

//wieviel muss bezahlt werden
define(RUNE_IDENTPAY_GEMS_VALUE, 2);
define(RUNE_IDENTPAY_GOLD_VALUE, 7500);

/**
* @author Alucard
* @desc Identifiziert die Rune vom typ x
* @param value1 der rune
* @return anzahl der identifizierten runen
*/
function runes_identify($runeid, $same=true, $set_uei=true){
	global $session;
	$res = item_list_get (	'i.owner='.$session['user']['acctid'].' AND i.tpl_id="'.RUNE_DUMMY_TPL.'" AND i.value2='.$runeid,
							'', true, 'i.id, it.tpl_class');
	$count 	= db_num_rows( $res );
	
	
	while( ($rune = db_fetch_assoc( $res )) ){
		if( !isset($tpl) ){
			$tpl	= item_get_tpl('tpl_class = '.$rune['tpl_class'].' AND tpl_value2='.$runeid);
			$tpl['name'] 		= $tpl['tpl_name'];  		 	 	    	    	    	    	    	    	 							
			$tpl['description'] = $tpl['tpl_description']; 									
			$tpl['gold'] 		= $tpl['tpl_gold']; 							
			$tpl['gems'] 		= $tpl['tpl_gems']; 								
			$tpl['value1'] 		= $tpl['tpl_value1']; 								
			$tpl['value2'] 		= $tpl['tpl_value2']; 								
			$tpl['hvalue'] 		= $tpl['tpl_hvalue']; 								
			$tpl['hvalue2'] 	= $tpl['tpl_hvalue2'];			
		}

		item_set('id='.$rune['id'],$tpl);	

	}
	if( $set_uei ){
		
		// Duplikate rausschmeißen, Mod by talion
		$str_runes_ident_expr = ' CONCAT( REPLACE( runes_ident, ";'.$runeid.';", "" ), ";'.$runeid.'")';
		
		db_query('UPDATE account_extra_info SET runes_ident='.$str_runes_ident_expr.' WHERE acctid='.$session['user']['acctid'].' LIMIT 1');
	}
	
	return $count;
}


/**
* @author Alucard
* @desc Liste der unidentifizierten Runen des Nutzers
* @return mixed SQL-Result
*/
function runes_get_unidentified(){
	global $session;
	return item_list_get (	'i.owner='.$session['user']['acctid'].' AND tpl_id=\''.RUNE_DUMMY_TPL.'\'',
							'', false, 'i.id, i.name, i.value2');	
}


/**
* @author Alucard
* @desc Zählt unidentifizierte Runen des Nutzers
* @return int Anzahl
*/
function runes_get_unidentified_count($rid=0){
	global $session;
	return item_count (	'i.owner='.$session['user']['acctid'].
						' AND tpl_id=\''.RUNE_DUMMY_TPL.'\''.
						($rid ? ' AND value2='.$rid : ''));	
}


/**
* @author Alucard
* @desc liefert alle Runen zurück
* @return mixed SQL-Result
*/
function runes_get($unidef=false){
	global $session;
	
	$sql = 'SELECT i.* FROM '.ITEMS_TABLE.' i'
		 .' JOIN items_tpl it ON it.tpl_id = i.tpl_id'
		// .' JOIN items_classes ic ON ic.id = it.tpl_class'
		 .' WHERE owner='.$session['user']['acctid']
		 .' AND it.tpl_class='.getsetting('runes_classid',0)
		 .($unidef ? '' : ' AND it.tpl_id <> \''.RUNE_DUMMY_TPL.'\'')
		 .' ORDER BY tpl_id';
	
	return db_query($sql);
}


/**
* @author Alucard
* @desc liefert extrainfos der rune zurück
* @return mixed SQL-Result
*/
function runes_get_ei( $id ){
	global $session;
	
	$sql = 'SELECT * FROM '.RUNES_EI_TABLE.' WHERE id='.$id;
	
	return db_query($sql);
}

?>
