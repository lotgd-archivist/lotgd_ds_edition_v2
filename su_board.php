<?php
/**
* su_board.php: Adminfunktionen zur Überwachung der Nachrichtenbretter
*				Ausgehend von innboard.php: Admin part of inn messageboard by anpera
* @author Anpera, talion <t@ssilo.de>
* @version DS-E V/2
*/

require_once('common.php');

page_header('Schwarze Bretter');

if ($_GET['op']=="del"){
	$sql = 'DELETE FROM boards WHERE id='.(int)$_GET['id'];
	db_query($sql);
}

$sql = 'SELECT b.*,a.name FROM boards b LEFT JOIN accounts a ON a.acctid=b.author ORDER BY section ASC, id ASC';
$result = db_query($sql) or die(db_error(LINK));
if (db_num_rows($result)<=0){
	output('Es gibt keine Nachrichten an schwarzen Brettern.');
}
else{
	
	while($row = db_fetch_assoc($result)){
		output("`n`n");
		output("`^Autor: `&".$row['name']."`n");
		output("`^Start- / Enddatum: `&".$row['postdate']." `^/`& ".$row['expire']."`n");
		output("`^Sektion: `&".$row['section']."`n");
		output("`^Nachricht:`n `&".$row['message']);
		output('`n`&['.create_lnk('Entfernen','su_board.php?op=del&id='.$row['id']).']',true);
	}
}

db_free_result($result);
addnav("G?Zurück zur Grotte","superuser.php");
addnav('W?Zurück zum Weltlichen',$session['su_return']);

addnav("Aktualisieren","su_board.php");
page_footer();
?>