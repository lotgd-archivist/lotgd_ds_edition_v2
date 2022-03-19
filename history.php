<?php
require_once('common.php');

if($_GET['op'] == 'del') {
	$sql = 'DELETE FROM history WHERE id='.(int)$_GET['id'];
	db_query($sql);
	redirect($ret_page);
}

$sql = 'SELECT name,login FROM accounts WHERE acctid='.(int)$_GET['acctid'];
$res = db_query($sql);
$act = db_fetch_assoc($res);

page_header('Geschichte von '.$act['login']);

output('`c`&`bGeschichte von '.$act['name'].'`&`b`c`n`n`n');

show_history(1,$_GET['acctid']);

addnav('Zurück zur Bio','bio.php?char='.rawurlencode($act['login']).'&op='.$_GET['op'].'&ret='.urlencode($_GET['ret']));

page_footer();
?>
