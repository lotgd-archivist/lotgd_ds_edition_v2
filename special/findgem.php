<?php

// idea with ape by manweru
// coding by anpera
// bugfix by talion

if($_GET['found'] == 0) {
	$session['user']['specialinc'] = 'findgem.php';
	switch(e_rand(1,3)){
		case 1:
		$session['message'] ="`^Das Glück lächelt dich an. Du findest einen Edelstein!`0";
		$session[user][gems]++;
		//debuglog("found 1 gem in the forest");
		break;
		case 2:
		$session['message'] ="`^Du hörst ein lautes Kreischen und spürst einen leichten Ruck in der Nähe deiner Edelsteinsammlung.";
		if ($session[user][gems]>0){
			$session[user][gems]--;
			//debuglog("lost 1 gem in the forest");
			$session['message'] .=" Kurz darauf siehst du ein Äffchen mit einem deiner Edelsteine im Wald verschwinden.`0";
		}else{
			$session['message'] .=" Glücklicherweise hast du keine Edelsteine dabei und machst dir darum auch keine Sorgen wegen dem Äffchen, das scheinbar enttäuscht zurück in den Wald läuft.`0";
		}
		break;
		case 3:
		$session['message'] ="`^Ein kleines Äffchen wirft dir einen Edelstein an den Kopf und verschwindet im Wald. Du verlierst ein paar Lebenspunkte, aber der Edelstein lässt dich den Ärger darüber vergessen.`0";
		$session[user][gems]++;
		//debuglog("found 1 gem in the forest");
		$session[user][hitpoints]*=0.9;
		break;
	}
	redirect('forest.php?found=1');
}
else {
	output($session['message']);
	$session['message'] = '';
	$session['user']['specialinc'] = '';
}

?>
