<?php

require_once("common.php");

page_header('Inventar');

output('`&`c`bGesammelter Besitz von '.$session['user']['name'].'`&:`c`b`n`n');

$ret = calcreturnpath(urldecode($_GET['r']));

if($session['user']['weapon'] != 'Fists' ||  $session['user']['armor'] != 'T-Shirt') {
	
	addnav('Ausrüstung');
	
	if($session['user']['weapon'] != 'Fists') { addnav( $session['user']['weapon'].'`0 ablegen!' , 'invhandler.php?op=abl&what=weapon&ret='.urlencode(calcreturnpath()) ); }
	if($session['user']['armor'] != 'T-Shirt') { addnav( $session['user']['armor'].'`0 ablegen!' , 'invhandler.php?op=abl&what=armor&ret='.urlencode(calcreturnpath()) ); }
	
}

item_show_invent( ' showinvent=1 AND owner='.$session['user']['acctid'], false, 0, 1, 1, '`iDein Beutel ist leer!`i' );

addnav('Sonstiges');

if($ret) {
	addnav('Zurück',$ret);
}
else {
	addnav('Zu den News','news.php');
}


page_footer();
?>
