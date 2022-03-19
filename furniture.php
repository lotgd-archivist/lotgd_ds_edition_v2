<?

/**
Lagert Möbel aus um houses.php zu entlasten
by Maris

modded by talion: new itemsys
**/
require_once("common.php");
page_header();

$id = (int)$_GET['item_id'];
$item = item_get('id='.$id);

if(!$item['id']) {addnav('Zurück','inside_houses.php');page_footer();exit;}

$item_hook_info['private'] = $item['deposit2'];
$item_hook_info['hid'] = $item['deposit1'];
$item_hook_info['back_msg'] = ($item['deposit2'] ? 'Zurück zum Gemach' : 'Zurück zum Haus');
$item_hook_info['back_link'] = ($item['deposit2'] ? 'houses_private.php' : 'inside_houses.php?id='.$item['deposit1']);
$item_hook_info['link'] = 'furniture.php?item_id='.$id.'&hid='.$item['deposit1'].'&private='.$item['deposit2'];

$item_hook_info['section'] = ($item['deposit2'] ? 'h'.$item['deposit1'].'-'.$item['deposit2'].'privat' : 'house-'.$item['deposit1']);
$item_hook_info['op'] = $_GET['op'];

$hook = 'furniture'.($item['deposit2'] ? '_private' : '');

if($item[ $hook.'_hook' ] != '') {
	
	item_load_hook($item[ $hook.'_hook' ],'furniture',$item);
	
}
else {
	addnav('Zurück','inside_houses.php');
}

page_footer();
?>
