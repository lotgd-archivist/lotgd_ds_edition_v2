<?php

// 24072004

// The vendor Aeki sells furniture for houses and buys items found at beaten monsters in the forest.
//
// Vendor only appears on a few (game) days in village
// This is controlled by weather mod by Talisman
//
// by anpera (2004) while listening to music by 'The Sweet' ;)

// modded and rewritten completely by talion while listening to music by 'The Lemonheads' ; )
// to fit into the new drachenserver-itemsystem

require_once "common.php";

$show_invent = true;

require_once(LIB_PATH.'dg_funcs.lib.php');
if($session['user']['guildid'] && $session['user']['guildfunc'] != DG_FUNC_APPLICANT) {	
	$rebate = dg_calc_boni($session['user']['guildid'],'rebates_vendor',0);
}

page_header("Wanderh�ndler");

if($_GET['op'] == 'buy_do') {
	
	$show_invent = false;
					
	if($_GET['tpl_id']) {
		$item = item_get_tpl(' tpl_id="'.$_GET['tpl_id'].'" ');
		
		$name = $item['tpl_name'];
		
		$goldprice = round($item['tpl_gold'] * $_GET['gold_r']);
		$gemsprice = round($item['tpl_gems'] * $_GET['gems_r']);
		
		$item['tpl_gold'] = round($goldprice * 0.5);
		$item['tpl_gems'] = round($gemsprice * 0.5);
										
		item_add($session['user']['acctid'],0,false,$item);
		
		addnav("Mehr kaufen","vendor.php?op=buy&act=new");
	}
	else {
		$item = item_get(' id="'.$_GET['id'].'" ', false);
		
		$name = $item['name'];
		
		$goldprice = round($item['gold'] * $_GET['gold_r']);
		$gemsprice = round($item['gems'] * $_GET['gems_r']);
		
		$item['gold'] = round($goldprice * 0.5);
		$item['gems'] = round($gemsprice * 0.5);
						
		item_set('id='.(int)$_GET['id'],array('deposit1'=>0,'deposit2'=>0,'owner'=>$session['user']['acctid'],'gold'=>$item['gold'],'gems'=>$item['gems']) );
		
		addnav("Mehr kaufen","vendor.php?op=buy&act=old");
	}
			
	output("`qDer H�ndler reibt sich die H�nde und �bergibt dir ".$name.", w�hrend du ".($goldprice?"`^".$goldprice." `qGold":"")." ".($gemsprice?"`#".$gemsprice."`q Edelsteine":"")." abz�hlst. ");
	
	$session['user']['gold'] -= $goldprice;
	$session['user']['gems'] -= $gemsprice;
	
}
else if($_GET['op'] == 'sell_do') {		
	
	$show_invent = false;
			
	$item = item_get(' id="'.$_GET['id'].'" ');
		
	$goldprice = round($item['gold'] * $_GET['gold_r']);
	$gemsprice = round($item['gems'] * $_GET['gems_r']);
	
	// Wenn Gebraucht-Ankauf bei Wanderh�ndler m�glich
	if($item['vendor'] == 1 || $item['vendor'] == 3) {
		// Der Wanderh�ndler kann auch nicht unendlich viel aufnehmen, irgendwann muss er aussortieren!
		// Doppelt Vorhandenes kommt weg
		item_delete(' tpl_id="'.$item['tpl_id'].'" AND owner='.ITEM_OWNER_VENDOR);
	
		item_set(' id='.(int)$_GET['id'],array('deposit1'=>0,'deposit2'=>0,'gold'=>$goldprice,'gems'=>$gemsprice,'owner'=>ITEM_OWNER_VENDOR) );
	}
	else {	// Neuware
		item_delete(' id='.$item['id']);
	}	
					
	$session['user']['gold'] += $goldprice;
	$session['user']['gems'] += $gemsprice;
	
	output("`qMit einem breiten und siegessicheren Grinsen gibt er dir die vereinbarten ".($goldprice?"`^".$goldprice." `qGold":"")." ".($gemsprice?"`#".$gemsprice."`q Edelsteine":"")." und schnappt sich ".$item['name']);
	
	addnav('Mehr verkaufen','vendor.php?op=sell');
			
}		

else if ($_GET[op]=="buy"){ // Wig-Wam Bam
	
	output("`qStolz pr�sentiert dir der H�ndler `tAeki`q seinen Wagen. Zu jedem der seltsamen Gegenst�nde, Artefakte und Zauber scheint er eine kleine Geschichte zu kennen. Dabei scheint er auff�llig oft darauf ");
	output("hinzuweisen, dass viele Leute, von denen er etwas gekauft hat, den wahren Wert dieser Dinge nicht zu kennen scheinen. `n".($rebate?"Zufrieden teilt er dir mit, dass du aufgrund deiner Gildenmitgliedschaft `^".$rebate." %`q Rabatt erh�ltst!":"")." ");
			
	addnav('Neuwaren','vendor.php?op=buy&act=new');
	addnav('Gebrauchtwaren','vendor.php?op=buy&act=old');
	
	$rebate = (100 - $rebate) * 0.01;
	
	if($_GET['act'] == 'old') {
		
		output('`n`nSeine Gebrauchtwaren:`n`n');
		
		// Nur Waren anzeigen, die nicht als Neuware erh�ltlich sind
		item_show_invent(' owner='.ITEM_OWNER_VENDOR.' 
				AND ( 
				( (vendor=1 OR vendor=3) AND (vendor_new=0) )
				 OR ( i.tpl_id="trph" AND hvalue!='.$session['user']['acctid'].' ) 
				 ) ', false, 1, $rebate, $rebate );
	}
	else {
		
		output('`n`nSeine Neuwaren:`n`n');
		item_show_invent(' vendor_new=1 ', true, 1, $rebate, $rebate);
		
	}
	addnav('Zur�ck');
	addnav("Zum H�ndler","vendor.php");
	
}

else if ($_GET[op]=="sell"){ // Ballroom Blitz

	output("`qDer H�ndler begutachtet deinen Besitz. Mit dem ge�bten Auge eines Kenners sortiert er die Dinge aus, die ihn interessieren w�rden und nennt dir einen Preis daf�r.`n`n");

	item_show_invent(' owner='.$session['user']['acctid'].' AND (vendor=2 OR vendor=3) ', false, 2);
	
	addnav('Zur�ck');
	addnav("Zum H�ndler","vendor.php");
	
}
else if($_GET['op'] == 'rel') {
	$show_invent = false;
	
	$arr_item = item_get_tpl(' tpl_id="drrel_gld" ');
	
	$session['user']['gold'] -= $arr_item['tpl_value1'];
	debuglog('gab '.$arr_item['tpl_value1'].' Gold f�r Drachenreliquie');
	
	$arr_item['tpl_value1'] = time();
	
	item_add($session['user']['acctid'],0,false,$arr_item);
	item_delete(' (tpl_id="drstb") AND owner='.$session['user']['acctid']);

	output('`n`n`qAeki �berreicht dir vorsichtig ein schweres, fast g�nzlich schwarz gef�rbtes Horn.
				Bewundernd und gleichzeitig unschl�ssig streichst du dar�ber. 
				Schlie�lich verschwindet die kostbare Reliquie in deinem Rucksack.');
				
	addnews('`^Soeben wurde '.$session['user']['name'].'`^ dabei beobachtet, wie er Aekis Stand mit einer Drachenreliquie verlie�!');
	
	addnav('Zur�ck');
	addnav("Zum H�ndler","vendor.php");

}
else{ // Teenage Rampage
	checkday();
	if (!getsetting("vendor",0)) {
		
		if(!$session['user']['superuser']) {
			redirect("market.php");
		}
		else {
			output('`c`bWanderh�ndler - heute nur f�r DICH als Gott ; )`b`c`n`n');
		}
		
	}
		
	output("`qHeute ist der Wanderh�ndler `tAeki `qwieder im Dorf! Direkt vor `!MightyE`qs Waffenladen hat er seinen Wagen aufgebaut, was MightyE sichtlich missf�llt. Da er aber ");
	output(" selbst hin und wieder Handel mit ihm betreibt, l��t er ihn gew�hren.`nNeugierig n�herst du dich dem Wagen, um zu sehen, ob der H�ndler diesmal etwas Interessantes");
	output(" f�r dich dabei hat. Vielleicht hast du aber auch etwas, das du ihm verkaufen kannst?`n");
	
	if( item_count(' (tpl_id="drstb") AND owner='.$session['user']['acctid']) >= 1 ) {
		
		$sql = 'SELECT a.name FROM items LEFT JOIN accounts a ON owner=acctid WHERE tpl_id="drrel_gld"';
		$res = db_query($sql);
		$int_count = db_num_rows($res);
					
		if(0 == $int_count) {	// Noch keiner hat die Reliquie			
				
			// value1 enth�lt Preis
			$arr_item = item_get_tpl(' tpl_id="drrel_gld" ','tpl_name,tpl_description,tpl_value1');
		
			output(" Besonders sticht dir ein handbeschriebenes, ziemlich krakeliges Schild neben seinem Stand ins Auge: `Q\"Verkaufe Drachenreliquie, Modell '".$arr_item['tpl_name']."`q'. Nur heute, nur hier, nur mit mir! `b".$arr_item['tpl_value1']."`b Gold.\"`n");
			if($session['user']['gold'] >= $arr_item['tpl_value1']) {
				addnav($arr_item['tpl_name'],'vendor.php?op=rel');
			}
			
		}
		// END noch keiner hat Rel
		else {
			
			$arr_owner = db_fetch_assoc($res);
			
			output('`n`n`0Unter beredsamen Beschwichtigungen teilt dir Aeki mit, dass sich '.$arr_owner['name'].'`0 noch 
					vor dir die Drachenreliquie unter den Nagel gerissen hat.');
		}
	}
	

	addnav("Waren durchst�bern","vendor.php?op=buy");
	addnav("Etwas verkaufen","vendor.php?op=sell");
	addnav('Zur�ck');

}

addnav("Zum Marktplatz","market.php");

page_footer();
// reading source code can seriously damage your eyes! Well, at least it can take out the fun of a game...
?>
