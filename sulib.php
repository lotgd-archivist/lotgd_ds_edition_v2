<?php
/* author: bibir (logd_bibir@email.de)
*      and Chaosmaker (webmaster@chaosonline.de)
*      for http://logd.chaosonline.de
*
*     a library with text from users to help other
* 	  a bit like faq
*
* details:
*  (15.11.04) start of idea
*  (15.01.05) project finished
*/
/*
CREATE TABLE lib_themes (
  themeid int(10) unsigned NOT NULL auto_increment,
  theme varchar(100) default NULL,
  listorder INT(10) UNSIGNED DEFAULT '1' NOT NULL,
  PRIMARY KEY (themeid)
) TYPE=MyISAM;


CREATE TABLE lib_books (
  bookid int(10) unsigned NOT NULL auto_increment,
  themeid int(10) default NULL,
  acctid int(10) unsigned NOT NULL default '0',
  author varchar(60) NOT NULL,
  title varchar(100) default NULL,
  book text default NULL,
  activated enum('0','1') NOT NULL default '0',
  listorder INT(10) UNSIGNED DEFAULT '1' NOT NULL,
  PRIMARY KEY (bookid),
  KEY themeid (themeid)
) TYPE=MyISAM;

*/

require_once "common.php";
if(!isset($_GET['op'])) $_GET['op'] = "";

// settings
$int_maxdp = getsetting("libdp","5");



addnav('W?Zurück zum Weltlichen',$session['su_return']);
addnav("G?Zurück zur Grotte","superuser.php");
addnav("Bibliotheksfunktionen");
page_header("Bibliothek-Editor");
output("`c`b`9Editor für Drachen Bibliothek`0`b`c`n`n");


switch($_GET['op']){
case "theme":
	addnav("Zur Themenübersicht","sulib.php");
	$sql = "SELECT theme FROM lib_themes WHERE themeid=".$_GET['themeid'];
	$result = db_query($sql) or die(db_error(LINK));
	$row = db_fetch_assoc($result);

	if (!empty($_GET['saveorder'])) {
		asort($_POST['order']);
		$keys = array_keys($_POST['order']);
		$i = 0;
		foreach ($keys AS $key) {
			$i++;
			$sql = 'UPDATE lib_books SET listorder="'.$i.'" WHERE bookid="'.$key.'"';
			db_query($sql);
		}
	}
	output("Alle Bücher zum Thema: ".$row['theme']."`0`n`n");
	$sql = "SELECT bookid, title, author, listorder, recommended, views FROM lib_books
			WHERE themeid=".$_GET['themeid']." ORDER BY listorder ASC";
	$result = db_query($sql) or die(db_error(LINK));
	output('<form action="sulib.php?op=theme&amp;themeid='.$_GET['themeid'].'&amp;saveorder=1" method="post">',true);
	addnav('','sulib.php?op=theme&themeid='.$_GET['themeid'].'&saveorder=1');
	output("<table cellpadding=2 cellspacing=1 bgcolor='#999999' align='center'><tr class='trhead'><td>Titel</td><td>Autor</td><td>Empfohlen?</td><td>Views</td><td>Sortierung</td></tr>",true);
	if (db_num_rows($result)==0) {
		output("<tr class='trdark'><td colspan=3>Es sind keine Bücher vorhanden</td></tr>",true);
	}
	else {
		$bgclass = '';
		while ($row = db_fetch_assoc($result)) {
			$bgclass = ($bgclass=='trdark'?'trlight':'trdark');
			output("<tr class='$bgclass'><td><a href=\"sulib.php?op=edit_post&id=".$row['bookid']."\">",true);
			output($row['title']);
			output("`0</a></td><td>",true);
			output($row['author']);
			output("`0</a></td><td>",true);
			output( ($row['recommended'] ? 'Ja' : 'Nein') . '</td><td>',true );
			output( $row['views'] . '</td><td>',true );
			$order_options = '';
			for ($i=1; $i<=db_num_rows($result); $i++) $order_options .= '<option value="'.$i.'"'.($i==$row['listorder']?' selected="selected"':'').'>'.$i.'</option>';
			output('<select name="order['.$row['bookid'].']">'.$order_options.'</select>',true);
			output("</td></tr>",true);
			addnav("","sulib.php?op=edit_post&id=".$row['bookid']);
		}
		output('<tr><td colspan="3" style="text-align:right"><input type="submit" class="button" value="Sortierung speichern!" /></td></tr>',true);
	}
	output("</table>",true);
	output('</form>',true);
	break;

case "new_theme":
	addnav("Zur Themenübersicht","sulib.php");
	if ($_GET['subop']=="save") {
		$sql = 'SELECT MAX(listorder) AS maxtheme FROM lib_themes';
		$result = db_query($sql);
		$row = db_fetch_assoc($result);
		$row['maxtheme']++;
		$sql = "INSERT INTO lib_themes (theme,listorder) VALUES ('".$_POST['theme']."', {$row['maxtheme']})";
		$result = db_query($sql) or die(db_error(LINK));
		output("`\$Neues Thema wurde angelegt.`0`n`n");
	}
	output("<form action=\"sulib.php?op=new_theme&subop=save\" method='POST'>",true);
	output("<table><tr><td>Thema </td><td><input name='theme' maxlength='100'></td></tr>",true);
	output("</table><input type='submit' class='button' value='Speichern'></form>",true);
	addnav("","sulib.php?op=new_theme&subop=save");
	output("`n");

	output("`bVorhandene Themen:`b`n`n");
	$sql = "SELECT theme FROM lib_themes ORDER BY themeid ASC";
	$result = db_query($sql) or die(db_error(LINK));
	if(db_num_rows($result)==0) {
		output("Es gibt keine Themen.");
	}
	else while($row = db_fetch_assoc($result)) {
		output($row['theme']."`0`n");
	}
	break;

case "edit_theme":
	addnav("Zur Themenübersicht","sulib.php");
	if ($_GET['subop']=="save") {
		$_POST['theme'] = addslashes(closetags(stripslashes($_POST['theme']),'`i`b`c`H'));
		$sql = "UPDATE lib_themes SET theme='".$_POST['theme']."' WHERE themeid=".$_GET['themeid'];
		$result = db_query($sql) or die(db_error(LINK));
		//output("Thema wurde geändert.`n`n");
		redirect("sulib.php?op=browse");
	}
	else {
		output("Hier kann das Thema geändert werden.");
		$sql = "SELECT theme FROM lib_themes WHERE themeid=".$_GET['themeid'];
		$result = db_query($sql) or die(db_error(LINK));
		$row = db_fetch_assoc($result);
		output("<form action=\"sulib.php?op=edit_theme&subop=save&themeid=".$_GET['themeid']."\" method='POST'>
			Thema: <input name='theme' value='".htmlentities(str_replace("`","``",$row['theme']),ENT_QUOTES)."' maxlength='100' size='80'>
			`n<input type='submit' class='button' value='Speichern'></form>",true);
		addnav("","sulib.php?op=edit_theme&subop=save&themeid=".$_GET['themeid']);
	}
	break;

case "del_theme":
	addnav("Zur Themenübersicht","sulib.php");
	//buecher, die zu diesem thema gehoeren:
	//a) mitloeschen
	//b) anderem Thema zuordnen
	//c) auf themeid 0 setzen
	$sql = "SELECT COUNT(bookid) AS anz FROM lib_books WHERE themeid=".$_GET['themeid'];
	$result = db_query($sql) or die(db_error(LINK));
	$row = db_fetch_assoc($result);
	if ($row['anz']==0) {
		output("Es sind keine Bücher zu diesem Thema vorhanden, das Thema wird gelöscht.");
		$sql = "DELETE FROM lib_themes WHERE themeid=".$_GET['themeid'];
		db_query($sql) or die(db_error(LINK));
	}
	else {
		output("Es sind ".$row['anz']." Bücher vorhanden, was soll mit diesen passieren?`n`n");
		output("<form action=\"sulib.php?op=del_theme2&themeid=".$_GET['themeid']."\" method='POST'>
		<input type='radio' name='del' value='del_choice'>ebenfalls löschen`n
		<input type='radio' name='del' value='other_theme'>einem anderen Thema zuordnen`n
		<input type='radio' name='del' value='no_theme'>keinem Thema zuordnen`n
		<input type='submit' class='button' value='Löschen'></form>",true);
		addnav("","sulib.php?op=del_theme2&themeid=".$_GET['themeid']);
	}
	break;

case "del_theme2":
	addnav("Zur Themenübersicht","sulib.php");
	if ($_POST['del']=="del_choice") {
		$sql = "DELETE FROM lib_books WHERE themeid=".$_GET['themeid'];
		db_query($sql) or die(db_error(LINK));
		$sql = "DELETE FROM lib_themes WHERE themeid=".$_GET['themeid'];
		db_query($sql) or die(db_error(LINK));
		output("Bücher und Thema gelöscht.");
	}
	elseif ($_POST['del']=="other_theme") {
		output("Folgende Bücher einem anderen Thema zuordnen:`n");
		$sql = "SELECT title FROM lib_books WHERE themeid=".$_GET['themeid'];
		$result= db_query($sql) or die(db_error(LINK));
		while($row = db_fetch_assoc($result)) {
			output($row['title']."`0`n");
		}
		output("`nWelches Thema sollen die Bücher nun haben?");
		$sql = "SELECT * FROM lib_themes WHERE themeid!=".$_GET['themeid'];
		$result = db_query($sql) or die(db_error(LINK));
		output("<form action=\"sulib.php?op=del_theme3&old_themeid=".$_GET['themeid']."\" method='POST'>
		<select name='new_themeid'>",true);
		while($row = db_fetch_assoc($result)) {
			output("<option value='".$row['themeid']."'>",true);
			output(preg_replace('/`./','',$row['theme']));
			output("</option>",true);
		}
		output("</select><input type='submit' class='button' value='Thema zuordnen'></form>",true);
		addnav("","sulib.php?op=del_theme3&old_themeid=".$_GET['themeid']);
	}
	else {
		$sql = "UPDATE lib_books SET themeid='0' WHERE themeid=".$_GET['themeid'];
		db_query($sql) or die(db_error(LINK));
		$sql = "DELETE FROM lib_themes WHERE themeid=".$_GET['themeid'];
		db_query($sql) or die(db_error(LINK));
		output("Thema-ID der Bücher entfernt und Thema gelöscht.");
	}
	break;

case "del_theme3":
	addnav("Zur Themenübersicht","sulib.php");
	$sql = "UPDATE lib_books SET themeid='".$_POST['new_themeid']."' WHERE themeid=".$_GET['old_themeid'] ;
	db_query($sql) or die(db_error(LINK));
	$sql = "DELETE FROM lib_themes WHERE themeid=".$_GET['old_themeid'];
	db_query($sql) or die(db_error(LINK));
	output("Bücher neu zugeordnet und das Thema gelöscht.");
break;

case "new_books":
    addcommentary(false);
	
	addnav("Zur Themenübersicht","sulib.php");
	
	if ($_GET['subop']=="activate") {
				
		output('<hr>`n',true);
		
		$sql = 'SELECT accounts.acctid, lib_books.title, accounts.login FROM lib_books LEFT JOIN accounts USING(acctid) WHERE bookid='.$_GET['id'];
		$result = db_query($sql);
		$row = db_fetch_assoc($result);
		
		output('Buch: `b'.$row['title'].'`0 von '.$row['login'].'`b freischalten`0`n`n');
		
		$dpts4book = (int)$_POST['dp'];
		$dpts4book = min($int_maxdp,$dpts4book);
		$dpts4book = max(0,$dpts4book);
		
		$int_id = (int)$_GET['id'];
		
		$str_comment = $_POST['comment'];
		
		if($_GET['act'] == 'ok') {
								
			if($session['formsec'] != '') {
				unset($session['formsec']);
				
				output("Dieses Buch ist jetzt für alle in der Bibliothek einsehbar. Du vergibst an den Autor ".$dpts4book." DP!`n");
											
				$sql = "UPDATE lib_books SET activated='1' WHERE bookid=".$int_id;
				$result = db_query($sql);
				
				$sql = 'UPDATE accounts SET donation=donation+'.$dpts4book.' WHERE acctid='.$row['acctid'];
				db_query($sql);
				debuglog("gab $dpts4book Donationpoints für Buch ".$row['title']."`0 an ",$row['acctid']);
				systemmail($row['acctid'],'Dein Buch wurde angenommen!','`0Das Buch mit dem Titel "'.$row['title'].'`0", das du eingereicht hattest, wurde in die Bibliothek aufgenommen. Du bekommst dafür '.$dpts4book.' Donationpoints.'.(!empty($str_comment) ? '`n'.$str_comment : '') );
				
				
			}
									
		}
		else {
			
			viewcommentary("sulib_new","sagen:",40,"sagt");
			
			$session['formsec'] = md5(time());
			
			$arr_form = array('dp'=>'DP (Max. `b'.$int_maxdp.'`b Punkte),int',
								'comment'=>'Kurze Begründung / Nachricht an den Autor (optional)');
								
			$arr_data = array('dp'=>round($int_maxdp*0.5));
			
			$str_lnk = 'sulib.php?op=new_books&subop=activate&act=ok&id='.$int_id;
			addnav('',$str_lnk);			
			output('Wie viele Donationpoints möchtest du dem Autor für seine Arbeit gewähren?`n
					<form method="POST" action="'.$str_lnk.'">',true);
					
			showform($arr_form,$arr_data,false,'Freischalten!');
			
			output('</form>`n`n',true);
			
		}
		
		addnav("Weitere neue Bücher","sulib.php?op=new_books");
	}
	else {	// Keine Aktion
	
		viewcommentary("sulib_new","sagen:",40,"sagt");
	
		output("Das sind die Bücher, die eingereicht aber noch nicht freigegeben wurden:`n`^`bUngelesene`0`b`n`n");
		$sql = "SELECT b.bookid, b.title, t.theme, b.author, b.seen
			FROM lib_books b
			LEFT JOIN lib_themes t USING(themeid)
			WHERE b.activated='0'
			ORDER BY b.seen ASC";
		$result = db_query($sql) or die(db_error(LINK));
		output("<table cellpadding=2 cellspacing=1 bgcolor='#999999' align='center'><tr class='trhead'><td>Option</td><td>ID</td><td>Thema</td><td>Titel</td><td>Autor</td></tr>",true);
		$bgclass = '';
		if (db_num_rows($result)==0) {
			output("<tr class='trdark'><td colspan=5>Es gibt keine Bücher, die aktiviert werden müßten.</td></tr>",true);
		}
		else {
			while ($row = db_fetch_assoc($result)) {
				$bgclass = ($bgclass=='trdark'?'trlight':'trdark');
				output("<tr class='$bgclass'><td><a href=\"sulib.php?op=new_books&subop=activate&id=".$row['bookid']."\">Aktivieren</a>|
				<a href=\"sulib.php?op=del_post&id=".$row['bookid']."\">Löschen</a></td>",true);
				output("<td>".(!$row['seen'] ? ' `^`b ~ ' : ' - ').$row['bookid'].(!$row['seen'] ? '`0`b ~ ' : ' - ')."</td><td>",true);
				output($row['theme']);
				output("`0</td><td><a href=\"sulib.php?op=edit_post&id=".$row['bookid']."\">",true);
				output($row['title']);
				output("`0</a></td><td>",true);
				output($row['author']);
				output("`0</td></tr>",true);
				addnav("","sulib.php?op=new_books&subop=activate&id=".$row['bookid']);	//activate
				addnav("","sulib.php?op=del_post&id=".$row['bookid']);	//delete
				addnav("","sulib.php?op=edit_post&id=".$row['bookid']);	//view
			}
		}
		
	}
	
break;

case "del_post":

	$sql = 'SELECT acctid, title, activated FROM lib_books WHERE bookid='.$_GET['id'];
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	
	output('Buch: `b'.$row['title'].'`b löschen`0`n`n');
			
	$str_comment = $_POST['comment'];
	
	$int_id = (int)$_GET['id'];
	
	if($_GET['act'] == 'ok') {
								
		output("Dieses Buch ist für immer verschwunden - und das ist auch gut so.");
		
		if($session['formsec'] != '') {
			unset($session['formsec']);
										
			$sql = 'SELECT acctid, title, activated FROM lib_books WHERE bookid='.$int_id;
			$result = db_query($sql);
			$row = db_fetch_assoc($result);
			if ($row['activated']==1) {
				systemmail($row['acctid'],'Dein Buch wurde verbrannt!','`0Das Buch mit dem Titel "'.$row['title'].'`0", das in der Bibliothek stand, wurde im Kamin verbrannt.'.(!empty($str_comment) ? '`n'.$str_comment : ''));
			}
			else {
				systemmail($row['acctid'],'Dein Buch wurde abgelehnt!','`0Das Buch mit dem Titel "'.$row['title'].'`0", das du eingereicht hattest, wurde nicht in die Bibliothek aufgenommen.'.(!empty($str_comment) ? '`n'.$str_comment : ''));
			}
			
			debuglog("Biblio - Buch mit Namen \"".$row['title']."\" gelöscht");
			
			$sql = "DELETE FROM lib_books WHERE bookid=".$_GET['id'];
			db_query($sql);
			
			
		}
								
	}
	else {
		
		$session['formsec'] = md5(time());
		
		$arr_form = array('comment'=>'Kurze Begründung / Nachricht an den Autor (optional)');
							
		$arr_data = array();
		
		$str_lnk = 'sulib.php?op=del_post&act=ok&id='.$int_id;
		addnav('',$str_lnk);			
		output('Buch löschen?`n
				<form method="POST" action="'.$str_lnk.'">',true);
				
		showform($arr_form,$arr_data,false,'Die Ratten warten schon!');
		
		output('</form>`n`n',true);
		
	}
	
	addnav("Weitere neue Bücher","sulib.php?op=new_books");
	addnav("Zur Themenübersicht","sulib.php");
break;

case "edit_post":
	$sql = 'UPDATE lib_books SET seen=1 WHERE bookid='.$_GET['id'];
	db_query($sql);

	if ($_GET['subop']=="button") {
		if (isset($_POST['save'])) {
		
			debuglog("Biblio - Buch editiert");
			
			output("Buch bearbeitet.");
			$sql="UPDATE lib_books set themeid='".$_POST['themeid']."',
									title='".$_POST['title']."',
									book='".$_POST['book']."'
				  WHERE bookid=".$_GET['id'];
			db_query($sql);
		}
		elseif (isset($_POST['activate'])) {
			redirect("sulib.php?op=new_books&subop=activate&id=".$_GET['id']);
		}
		elseif (isset($_POST['recommend_on'])) {
			$sql = 'UPDATE lib_books SET recommended=1 WHERE bookid='.$_GET['id'];
			db_query($sql);
			redirect('sulib.php?op=edit_post&id='.$_GET['id']);
		}
		elseif (isset($_POST['recommend_off'])) {
			$sql = 'UPDATE lib_books SET recommended=0 WHERE bookid='.$_GET['id'];
			db_query($sql);
			redirect('sulib.php?op=edit_post&id='.$_GET['id']);
		}
		elseif (isset($_GET['notseen'])) {
			$sql = 'UPDATE lib_books SET seen=0 WHERE bookid='.$_GET['id'];
			db_query($sql);
			redirect('sulib.php?op=new_books');
		}
		elseif (isset($_POST['del'])) {
			redirect("sulib.php?op=del_post&id=".$_GET['id']);
		}
	}

	$sql = "SELECT themeid, title, book, activated, author, recommended FROM lib_books
			WHERE bookid=".$_GET['id'];
	$result = db_query($sql) or die(db_error(LINK));
	$row = db_fetch_assoc($result);

	output("<form action=\"sulib.php?op=edit_post&subop=button&id=".$_GET['id']."\" method='POST'>
	`nThema: <select name='themeid'>",true);
	$sql2 = "SELECT * FROM lib_themes ORDER BY themeid ASC";
	$result2 = db_query($sql2) or die(db_error(LINK));
	while ($row2 = db_fetch_assoc($result2)) {
		output("<option value='".$row2['themeid']."' ".($row2['themeid']==$row['themeid']?" selected":"").">",true);
		output($row2['theme']);
		output("`0</option>",true);
	}
	output("</select>`nTitel: <input type='text' name='title' value='",true);
	output(str_replace('`','``',$row['title']));
	output("' size='50' maxlength='50'>`nAutor: ".$row['author']."`0 hat in dem Buch geschrieben:`n
	<textarea name='book' class='input' cols='60' rows='10'>",true);
	output(str_replace('`','``',$row['book']));
	output("</textarea>`n<input type='submit' class='button' name='save' value='Speichern'>",true);
	if ($row['activated']=='0') {
		output("<input type='submit' class='button' name='activate' value='Aktivieren'>",true);
		addnav("Aktivieren","sulib.php?op=new_books&subop=activate&id=".$_GET['id']);
		addnav("Ungelesen markieren","sulib.php?op=edit_post&subop=button&notseen=1&id=".$_GET['id']);
	}
	if($row['recommended']) {
		output("<input type='submit' class='button' name='recommend_off' value='Empfehlen AUS'>",true);
	}
	else {
		output("<input type='submit' class='button' name='recommend_on' value='Empfehlen AN'>",true);
	}
	
	output("<input type='submit' class='button' name='del' value='Löschen'></form>`n",true);
	addnav("","sulib.php?op=edit_post&subop=button&id=".$_GET['id']);
	

	output("Das Buch:`n`n".str_replace("\n",'`n',$row['book']).'`0');

	addnav("Zu neuen Büchern","sulib.php?op=new_books");
	addnav("Zu Büchern ohne Thema","sulib.php?op=no_theme_books");
	if($row['themeid']) {
		addnav("Zum Themenbereich","sulib.php?op=theme&themeid=".$row['themeid']);
	}
	addnav("Zur Themenübersicht","sulib.php");
break;

case "no_theme_books":
	addnav("Zur Themenübersicht","sulib.php");
	$sql = "SELECT bookid, title, author FROM lib_books
			WHERE themeid='0'";
	$result = db_query($sql) or die(db_error(LINK));
	output("<table cellpadding=2 cellspacing=1 bgcolor='#999999' align='center'><tr class='trhead'><td>Option</td><td>ID</td><td>Titel</td><td>Autor</td></tr>",true);
	$bgclass = '';
	if (db_num_rows($result)==0) {
		output("<tr class='trdark'><td colspan=4>Es gibt keine Bücher, die einem Thema zugeordnet werden müßten.</td></tr>",true);
	}
	else while($row = db_fetch_assoc($result)) {
		$bgclass = ($bgclass=='trdark'?'trlight':'trdark');
		output("<tr class='$bgclass'><td><a href=\"sulib.php?op=del_post&id=".$row['bookid']."\">Löschen</a></td>",true);
		output("<td>".$row['bookid']."</td><td><a href=\"sulib.php?op=edit_post&id=".$row['bookid']."\">",true);
		output($row['title']);
		output("`0</a></td><td>",true);
		output($row['author']);
		output("`0</td></tr>",true);
		addnav("","sulib.php?op=del_post&id=".$row['bookid']);	//delete
		addnav("","sulib.php?op=edit_post&id=".$row['bookid']);	//ansehen
	}
break;


default:
	if (!empty($_GET['saveorder'])) {
		asort($_POST['order']);
		$keys = array_keys($_POST['order']);
		$i = 0;
		foreach ($keys AS $key) {
			$i++;
			$sql = 'UPDATE lib_themes SET listorder="'.$i.'" WHERE themeid="'.$key.'"';
			db_query($sql);
		}
	}

	output("Übersicht der Themen`n`n");
	output('<form action="sulib.php?saveorder=1" method="post">',true);
	addnav('','sulib.php?saveorder=1');
	output("<table cellpadding=2 cellspacing=1 bgcolor='#999999' align='center'><tr class='trhead'><td>Option</td><td>ID</td><td>Thema</td><td>Anzahl Bücher</td><td>Sortierung</td></tr>",true);
	$sql = "SELECT t.*, COUNT(b.bookid) AS anz FROM lib_themes t
			LEFT JOIN lib_books b USING(themeid)
			GROUP BY themeid
			ORDER BY listorder ASC";
	$result = db_query($sql) or die(db_error(LINK));
	if (db_num_rows($result)==0) {
		output("<tr class='trdark'><td colspan=5 align='center'>`&`iEs gibt keine Themen`i`0</td></tr>",true);
	}
	else {
		$bgclass = '';
		while ($row = db_fetch_assoc($result)) {
			$bgclass = ($bgclass=='trdark'?'trlight':'trdark');
			output("<tr class='$bgclass'><td><a href=\"sulib.php?op=edit_theme&themeid=".$row['themeid']."\">Edit</a> |
			<a href=\"sulib.php?op=del_theme&themeid=".$row['themeid']."\">Löschen</a></td><td>".$row['themeid']."</td><td>
			<a href=\"sulib.php?op=theme&themeid=".$row['themeid']."\">",true);
			output($row['theme']);
			output("`0</a></td><td>".$row['anz']."</td><td>",true);
			$order_options = '';
		for ($i=1; $i<=db_num_rows($result); $i++) $order_options .= '<option value="'.$i.'"'.($i==$row['listorder']?' selected="selected"':'').'>'.$i.'</option>';
			output('<select name="order['.$row['themeid'].']">'.$order_options.'</select>',true);
			output('</td></tr>',true);

			addnav("","sulib.php?op=edit_theme&themeid=".$row['themeid']);
			addnav("","sulib.php?op=del_theme&themeid=".$row['themeid']);
			addnav("","sulib.php?op=theme&themeid=".$row['themeid']);
		}
		output('<tr><td colspan="5" style="text-align:right"><input type="submit" class="button" value="Sortierung speichern!" /></td></tr>',true);
	}
	output("</table>",true);
	output('</form>',true);
	addnav("Neues Thema erstellen","sulib.php?op=new_theme");
	addnav("Neue Bücher ansehen","sulib.php?op=new_books");
	addnav("Bücher ohne Thema","sulib.php?op=no_theme_books");
}


page_footer();
?>
