<?php
/*-------------------------------/
Name: houses_private.php
Autor: tcb / talion für Drachenserver (mail: t@ssilo.de)
Erstellungsdatum: 5/05, 9/05
Beschreibung:	Privatgemächer für Fremdbewohner und Hausbesitzer. Einladungen in diese. Sämtl. EIgentumschecks über items, Konstanten, Funktionen etc. in houses.lib
				Besitzurkunden für Privatgemächer werden in lodge.php verkauft.
				Copyright-Box muss intakt bleiben, bei Verwendung Mail an Autor mit Serveradresse.
Mod: houses.php, lodge.php (Konstantendefinitionen und Funktionen in houses.lib.php)

Änderungen: Auslagerung der Schlüssel in keylist by Maris
/*-------------------------------*/
require_once("common.php");
page_header("Privatgemächer");

checkday();

is_new_day();

if($_GET['op'] != 'einladungen') {

	if(!isset($session['housekey']) || isset($_GET['housekey'])) {
	
		if($_GET['housekey']) {
			$session['housekey'] = (int)$_GET['housekey'];
		}
		else {
			redirect("houses.php");
		}
		
	}
	
	if(!isset($session['private']) || isset($_GET['private'])) {
		
		if($_GET['private']) {
			$session['private'] = (int)$_GET['private'];
		}
		else {
			redirect("inside_houses.php");
		}	
	}
	
	if($_GET['invited']) {$session['housekey'] = $_GET['housekey'];$session['invited'] = $_GET['invited'];}
}

$op = (isset($_GET['op'])) ? $_GET['op'] : '';

switch($op) {

	case '':	// Chat anzeigen
		
		$show_invent = true;
		
		addcommentary(true);
							
		$sql = 'SELECT name,acctid,house FROM accounts WHERE acctid='.$session['private'];
		$res = db_query($sql);
		$private = db_fetch_assoc($res);
		
		// Beschreibung abholen
		if($private['house'] == $session['housekey']) {	// Wenn Privatraum des Hausbesitzers
			$sql = 'SELECT private_description AS description FROM houses WHERE houseid='.$session['housekey'];
			$res = db_query($sql);
		}
		else {
			$sql = 'SELECT description FROM items WHERE tpl_id="privb" AND value1='.$session['housekey'].' AND owner='.$private['acctid'];
			$res = db_query($sql);
		}	
		
		$house = db_fetch_assoc($res);
				
        output('`2`b`cPrivatgemächer '.$private['name'].'`2\'s`b`c`n');
		
		output(closetags($house['description'],'`i`c`b').'`n`n`n');
   
		viewcommentary('h'.$session['housekey'].'-'.$session['private'].'privat',"Mit Mitbewohnern reden:",30,"sagt",false,true,false,true,true,true,2);
		
		output("`n`n`n<table border='0' cellpadding='5'><tr><td width='25%'>`2`bZutritt haben:`b `0</td><td>`2`bExtra Ausstattung`b</td></tr><tr><td valign='top'>",true);
						
         $sql = 'SELECT a.acctid AS aid,a.name AS besitzer FROM items i LEFT JOIN accounts a ON a.acctid=i.owner WHERE i.tpl_id="prive" AND value1='.$session['housekey'].' AND i.value2='.$session['private'].' ORDER BY id ASC';
				
        $result = db_query($sql) or die(db_error(LINK));
		
		$count = db_num_rows($result);
		
		if($count == 0) {
			output('`nNoch niemand!&nbsp;&nbsp;&nbsp;',true);
		}
		else {
			for ($i=1;$i<=$count;$i++){
	
				$item = db_fetch_assoc($result);
				
				output("`n`2".$i.": `&$item[besitzer]`0");
		
			}
		}
		
		output('</td><td valign="top">&nbsp;',true);
		
		$properties = ' deposit_private>0 AND deposit1='.$session['housekey'].' AND deposit2='.$session['private'];
		$extra = ' ORDER BY name DESC, id ASC';
		
		$res = item_list_get ( $properties , $extra , true , ' name,description,id,furniture_private_hook ' );
						
		$count = db_num_rows($res);
		
		$hooks = array();
		
        for ($i=1;$i<=$count;$i++){

            $item = db_fetch_assoc($res);
			output("`n`&$item[name]`0 (`i$item[description]`i)");

			if($item['furniture_private_hook'] != '' && $session['invited'] != $session['private'] && !$hooks[$item['furniture_private_hook']]) {
				$hooks[$item['furniture_private_hook']] = true;
				addnav($item['name'],'furniture.php?item_id='.$item['id']);
			}
	
		}
		
		output('</td></tr></table>',true);
		
		if ($session['user']['acctid']==$session['private']) {
			
			addnav("Aktionen");
			
			addnav("Beschreibung ändern","houses_private.php?op=desc");
			addnav("Einladen","houses_private.php?op=geben");
			addnav("Ausladen","houses_private.php?op=nehmen");
			addnav("Aufräumen","houses_private.php?op=sauber");
		}
		
		addnav("Verschiedenes");
		
		if($session['invited'] != $session['private']) {
			addnav("Einschlafen (Log Out)","inside_houses.php?act=logout");				
			addnav("Zurück zum Haus","inside_houses.php");				
		}
				
		addnav("Zum Wohnviertel","houses.php");				
		addnav("Zum Dorf","village.php");				
						
		break;
	
	case 'desc':
	
		$owner= false;
		// Feststellen, ob Hausbesitzer oder nicht
		if($session['user']['housekey'] == $session['housekey']) {
			$owner = true;
			$sql = 'SELECT private_description AS description FROM houses WHERE houseid='.$session['housekey'];
		}
		else {
			$sql = 'SELECT description,id FROM items WHERE tpl_id="privb" AND value1='.$session['housekey'].' AND owner='.$session['private'];
		}
		$res = db_query($sql);
		$house = db_fetch_assoc($res);
		
		if($_GET['act'] == 'ok') {
			$desc = $_POST['desc'];
			if(!$owner) {
				item_set(' id='.$house['id'], array('description'=>$desc) );

			}
			else {
				$sql = 'UPDATE houses SET private_description="'.$desc.'" WHERE houseid='.$session['housekey'];
				db_query($sql);
			}
						

			redirect('houses_private.php');
			
		}
		
		$link = 'houses_private.php?op=desc&act=ok';
		addnav('',$link);
		addnav('Zurück','houses_private.php');
		
		output('<form action="'.$link.'" method="POST"><input type="text" name="desc" value="',true);
		rawoutput($house['description']);
		output('" maxlength="300"> <input type="submit" value="Übernehmen"></form>',true);
		
		break;
			
	case 'einladungen':
		
		output('`c`b`@Privatgemächer`0`c`b`n`n');
			
		output('`@Du durchwühlst deinen Beutel auf der Suche nach Einladungen in Privatgemächer und findest folgende Exemplare:`n`n`c');
		/*		
		$sql = 'SELECT value1 FROM keylist WHERE owner='.$session['user']['acctid'];
		$res = db_query($sql);
		$list = ','.$session['user']['housekey'];
		while($k = db_fetch_assoc($res)) {
			$list .= ','.$k['value1'];
		}
		*/
		$list = '';
		
		$sql = 'SELECT id,housename,a.name AS playername,a.login AS playerlogin,i.value2 AS owner, houseid AS housekey FROM items i
				LEFT JOIN houses h ON h.houseid=i.value1
				LEFT JOIN accounts a ON a.acctid=i.value2
				WHERE i.tpl_id="prive" AND i.owner='.$session['user']['acctid'].' AND value1 NOT IN (-1'.$list.')';
		$res = db_query($sql);
		
		if(db_num_rows($res) == 0) {
			output('`iKeine Einladungen in Privatgemächer vorhanden!`i');
		}
		else {
			
			addnav('Einladungen');
		
			while($i = db_fetch_assoc($res)) {
				$link = 'houses_private.php?invited='.$i['owner'].'&private='.$i['owner'].'&housekey='.$i['housekey'];
				
				addnav($i['playerlogin'].' in '.strip_appoencode($i['housename'],3),$link);
				
				output('`&Einladung von '.$i['playername'].'`0 in '.$i['housename'].'`0: '.create_lnk('Betreten',$link).'`n',true);
				output('`n~~~`n');
			}
		}
		
		output('`c');
		
		addnav('Zurück');
		addnav('Zum Wohnviertel','houses.php');
		break;
	
	case 'raum_geben':	// Privatraum vergeben
								
		if($_GET['act'] == 'ok') {
			$ziel = (int)$_POST['ziel'];
			
			if(item_count(' tpl_id="privb" AND value1='.$session['housekey'].' AND owner='.$ziel ) > 0) {
				output('`2Diese Person besitzt bereits ein eigenes Privatgemach in deinem Haus!');
			}
			else {	
				
				$sql = 'SELECT name FROM accounts WHERE acctid='.$ziel;
				$res = db_query($sql);
				$name = db_fetch_assoc($res);
							
				$oi = item_get(' tpl_id="privb" AND value1='.$session['housekey'].' AND (owner='.$session['user']['acctid'].' OR owner=0) ',false);
				
				$item_tpl['tpl_value1'] = $session['housekey'];
				$item_tpl['tpl_value2'] = $session['private'];
				$item_tpl['owner'] = $ziel;
				$item_tpl['tpl_description'] = 'Ein Privatgemach in Haus Nr. '.$session['housekey'].'.';
								
				item_set('id='.$oi['id'],$item_tpl);
				
				output('`2Du übergibst '.$name['name'].'`2 den Schlüssel zu einem eigenen Privatgemach in deinem Haus!');
				
				systemmail($ziel,'`2Privatgemach erhalten',$session['user']['name'].'`2 hat dir den Schlüssel zu einem eigenen Privatgemach in '.($session['user']['sex'] ? 'ihrem' : 'seinem').' Haus übergeben.');
				
			}
			
		}
		else {
			
			$sql = 'SELECT id,a.name FROM items i LEFT JOIN accounts a ON a.acctid=i.owner WHERE i.tpl_id="privb" AND value1='.$session['housekey'].' AND owner!='.$session['user']['acctid'].' AND owner>0';
			$res = db_query($sql);
			
			$number = db_num_rows($res);			
															
			output('`2Du hast '.$number.' Privatgemächer bereits an die folgenden Personen vergeben:`n`n');
			
			while($p = db_fetch_assoc($res)) {
				output('`n'.$p['name']);
			}
			
			$max_number = item_count(' tpl_id="privb" AND (owner='.$session['user']['acctid'].' OR owner=0) AND value1='.$session['housekey']);
			
			if($max_number == 0) {
				output('`n`n`2Du kannst keine weiteren Privatgemächer vergeben. Falls du noch mehr benötigst, hast du in der Jägerhütte die Möglichkeit, Zimmer dazuzukaufen.');
			}
			else {
				
				output('`n`n`2Du hast noch '.$max_number.' Plätze frei!`n`n');		
													
				$sql = "SELECT a.name, a.acctid FROM keylist i LEFT JOIN accounts a ON a.acctid=i.owner WHERE i.owner!=".$session['user']['acctid']." AND i.owner>0 AND i.value1=".$session['housekey']." GROUP BY i.owner ORDER BY a.name";
				$res = db_query($sql);		
				
				if(!db_num_rows($res)) {
					output("`2Es gibt keine Personen, denen du ein Privatgemach gewähren könntest!");		
				}
				else {
					$link = 'houses_private.php?op=raum_geben&act=ok';
				
					output('<form action="'.$link.'" method="POST">',true);
			
					output("`2Wem willst du ein eigenes Privatgemach gewähren? <select name='ziel'>",true);
											
					while ( $p = db_fetch_assoc($res) ) {
						if($p['acctid']) {
							output("<option value=\"".$p['acctid']."\">".preg_replace("'[`].'","",$p['name'])."</option>",true);
						}
			
					}
			
					output("</select>`n`n",true);
			
					output("<input type='submit' class='button' value='Und los..'></form>",true);
					addnav('',$link);
				}
			}
		}
		
		addnav("Zurück","inside_houses.php");				
		
		break;
		
	case 'raum_nehmen':	// Person ausladen
		
		if($_GET['act'] == 'ok') {
			$ziel = (int)$_POST['ziel'];
						
			// Einladungen in Privatgemächer löschen
			item_delete(' tpl_id="prive" AND value1='.$session['housekey'].' AND value2='.$ziel);
		
			// Privatgemächer zurücksetzen
			item_set(' tpl_id="privb" AND value1='.$session['housekey'].' AND owner='.$ziel, array('owner'=>$session['user']['acctid'],'description'=>'') );
		
			// Möbel für Privatgemächer zurücksetzen
			item_set(' deposit1='.$session['housekey'].' AND deposit2='.$ziel, array('deposit1'=>0,'deposit2'=>0) );
						
			output('`2Du nimmst dieses Gemach wieder selbst unter Beschlag.');
			
			systemmail($ziel,'`8Privatgemach abgenommen',$session['user']['name'].'`8 hat dir den Schlüssel zu einem eigenen Privatgemach in '.($session['user']['sex'] ? 'ihrem' : 'seinem').' Haus wieder abgenommen.');
			
		}
		else {
		
			$sql = 'SELECT a.name, a.acctid, i.id AS itemid FROM items i LEFT JOIN accounts a ON a.acctid=i.owner WHERE i.tpl_id="privb" AND i.value1='.$session['housekey'].' AND i.owner!='.$session['user']['acctid'].' ORDER BY a.name';
			$res = db_query($sql);		
					
			if(!db_num_rows($res)) {
				output("`2Es hat noch keiner deiner Bewohner ein Privatgemach!");		
			}
			else {
				
				$link = 'houses_private.php?op=raum_nehmen&act=ok';
				
				output('<form action="'.$link.'" method="POST">',true);
		
				output("`2Wem willst du sein Privatgemach entziehen? <select name='ziel'>",true);
										
				while ( $p = db_fetch_assoc($res) ) {
		
					output("<option value=\"".$p['acctid']."\">".preg_replace("'[`].'","",$p['name'])."</option>",true);
		
				}
		
				output("</select>`n`n",true);
		
				output("<input type='submit' class='button' value='Und los..'></form>",true);
				addnav('',$link);
			}
		}
		
		addnav("Zurück","inside_houses.php");				
		
		break;
		
	case 'geben':	// Person autorisieren
				
		// Allgemeiner Autorisierungsbildschirm für ALLE Privatgemächer (auch die des Hausherrn)
		$house_owner = ($session['user']['housekey'] == $session['housekey'] ? true : false);
		$max_number = ($house_owner ? 1000000 : 4);
								
		if($_GET['act'] == 'search' && strlen($_POST['search']) > 0) {
			$count = strlen($_POST['search']);
			$search="%";
			for ($x=0;$x<$count;$x++){
				$search .= substr($_POST['search'],$x,1)."%";
			}
			
			$sql = 'SELECT name,acctid FROM accounts WHERE name LIKE "'.$search.'" AND acctid!='.$session['user']['acctid'];
			$res = db_query($sql);
			
			$link = 'houses_private.php?op=geben&act=id';
			
			output('<form action="'.$link.'" method="POST">',true);
	
			output(' <select name="ziel">',true);
									
			while ( $p = db_fetch_assoc($res) ) {
	
				output('<option value="'.$p['acctid'].'">'.preg_replace("'[`].'","",$p['name']).'</option>',true);
	
			}
	
			output('</select>`n`n',true);
	
			output('<input type="submit" class="button" value="Einladen"></form>',true);
			addnav('',$link);
		}
		elseif($_GET['act'] == 'id' && $_POST['ziel']) {
			$ziel = (int)$_POST['ziel'];
			
			// Überprüfen, ob Spieler nicht bereits autorisiert
			if(item_count(' tpl_id="prive" AND value1='.$session['housekey'].' AND owner='.$ziel.' AND value2='.$session['user']['acctid']) > 0) {
				output('`2Diese Person hat bereits Zugang zu deinem Gemach!');
				addnav('Neue Suche','houses_private.php?op=geben');
			}
			else {	// autorisieren
				
				$item_tpl['tpl_value1'] = $session['housekey'];
				$item_tpl['tpl_value2'] = $session['user']['acctid'];
				$item_tpl['tpl_description'] = 'Einladung in die Privatgemächer '.$session['user']['name'].'`0s in Haus Nr. '.$session['housekey'];
								
				item_add($ziel,'prive',true,$item_tpl);
												
				$sql = 'SELECT name FROM accounts WHERE acctid='.$ziel;
				$res = db_query($sql);
				$name = db_fetch_assoc($res);
				
				output('`2Du übergibst '.$name['name'].'`2 einen Schlüssel zu deinem Privatgemach!');
				
				systemmail($ziel,'`2Schlüssel zu Privatgemach',$session['user']['name'].'`2 hat dir freundlicherweise einen Schlüssel zu '.($session['user']['sex'] ? 'ihren' : 'seinen').' Privatgemächern in Haus Nr. '.$session['housekey'].' überreicht.');
								
			}
						
		}
		else {
			// Auf max. Anzahl prüfen
			$int_count = item_count(' tpl_id="prive" AND value1='.$session['housekey'].' AND value2='.$session['user']['acctid'] );
			if($int_count > $max_number) {
				output('`2Es hat bereits die maximale Personenzahl Zugang zu deinem Gemach!');
				addnav('Neue Suche','houses_private.php?op=geben');
			}
			else {
				output('`2Wem willst du Zugang zu deinen Privatgemächern gewähren?`nDu hast noch '.(!$house_owner ? $max_number - $int_count : 'viele').' Plätze frei!');		
				
				$link = 'houses_private.php?op=geben&act=search';
			
				output('<form action="'.$link.'" method="POST">',true);
		
				output('Name: <input type="input" name="search">',true);
										
				output('`n`n',true);
		
				output('<input type="submit" class="button" value="Suchen"></form>',true);
				addnav('',$link);
				
			}
		}
		
		addnav("Zurück","houses_private.php");
		
		break;
	
	case 'nehmen':	// Person ausladen
		
		if($_GET['act'] == 'ok') {
			$ziel = (int)$_POST['ziel'];
								
			if($ziel) {
			
				$sql = "SELECT i.owner, a.name FROM items i, accounts a WHERE a.acctid=i.owner AND i.id=".$ziel;
				$res = db_query($sql);
				$p = db_fetch_assoc($res);
				
				$sql = 'DELETE FROM items WHERE id='.$ziel;
				db_query($sql);
			
				//systemmail($p['owner'],"`@Zugang zu Privatgemächern entzogen!`0","`&{$session['user']['name']}`& hat dir den Schlüssel zu ".(($session['user']['sex'])?"ihren":"seinen")." Privatgemächern wieder abgenommen.");
				
				output("`2Du nimmst ".$p['name']." `2den Schlüssel zu deinen privaten Räumen wieder ab!");
												
			}
				
			else {
				redirect("houses_private.php?op=nehmen");
			}
		}
		else {
			$sql = 'SELECT a.name, i.id AS itemid FROM items i LEFT JOIN accounts a ON a.acctid=i.owner WHERE i.tpl_id="prive" AND i.value1='.$session['housekey'].' AND i.value2='.$session['user']['acctid'].' ORDER BY a.name';
			$res = db_query($sql);		
			
			if(!db_num_rows($res)) {
				output("`2Es hat noch niemand Zugang zu deinen Privatgemächern!");		
			}
			else {
				$link = 'houses_private.php?op=nehmen&act=ok';
				output('<form action="'.$link.'" method="POST">',true);
		
				output("`2Wem willst du den Zugang zu deinen Privatgemächern entziehen? <select name='ziel'>",true);
										
				while ( $p = db_fetch_assoc($res) ) {
		
					output("<option value=\"".$p['itemid']."\">".preg_replace("'[`].'","",$p['name'])."</option>",true);
		
				}
		
				output("</select>`n`n",true);
							
				output("<input type='submit' class='button' value='Ausladen'></form>",true);
				addnav('',$link);
			}
		}
		
		addnav("Zurück","houses_private.php");				
		
		break;
				
	case 'sauber':	// Kommentare entfernen
		
		output("`2Du entschließt dich, in deinen Privatgemächern etwas aufzuräumen. Doch sei dir darüber im Klaren, dass dann alle Ereignisse der letzten Zeit hier drin in Vergessenheit geraten!");
		
		addnav("Ja, aufräumen!","houses_private.php?op=sauber_ok");
		addnav("Nein, zurück!","houses_private.php");
		
		break;
		
	case 'sauber_ok':	// Kommentare entfernen 2
		
		// Sicherung
		$sql = "UPDATE commentary SET section='h".$session['housekey']."-".$session['user']['acctid']."_p' WHERE section='h".$session['housekey']."-".$session['user']['acctid']."privat'";
		db_query($sql);
		// Sicherung Ende
				
		//$sql = "DELETE FROM commentary WHERE section='house-".$session['housekey']."_private'";
		//db_query($sql) or die (db_error(LINK));			
		redirect("houses_private.php");
				
		break;
		
	default:
		output('Hier dürfte ich nicht sein.. op: '.$op);
		addnav("Zurück","village.php");
		break;
}

page_footer();

// END houses_private.php
?>

