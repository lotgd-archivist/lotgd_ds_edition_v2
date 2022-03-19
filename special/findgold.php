<?php
if($_GET['found'] == 0) {
	$gold = e_rand($session[user][level]*10,$session[user][level]*50);
	$session[user][gold]+=$gold;
	$session['user']['specialinc'] = 'findgold.php';
	$session['message'] = "`^Das Glück lächelt dich an. Du findest ".$gold." Gold!`0";
	redirect('forest.php?found=1');
}
else {
	output($session['message']);
	$session['message'] = '';	
	$session['user']['specialinc'] = '';
}

//debuglog("found $gold gold in the forest");
?>
