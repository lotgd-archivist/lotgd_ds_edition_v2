<?php

// idea with ape by manweru
// coding by anpera
// bugfix by talion

if($_GET['found'] == 0) {
	$session['user']['specialinc'] = 'findgem.php';
	switch(e_rand(1,3)){
		case 1:
		$session['message'] ="`^Das Gl�ck l�chelt dich an. Du findest einen Edelstein!`0";
		$session[user][gems]++;
		//debuglog("found 1 gem in the forest");
		break;
		case 2:
		$session['message'] ="`^Du h�rst ein lautes Kreischen und sp�rst einen leichten Ruck in der N�he deiner Edelsteinsammlung.";
		if ($session[user][gems]>0){
			$session[user][gems]--;
			//debuglog("lost 1 gem in the forest");
			$session['message'] .=" Kurz darauf siehst du ein �ffchen mit einem deiner Edelsteine im Wald verschwinden.`0";
		}else{
			$session['message'] .=" Gl�cklicherweise hast du keine Edelsteine dabei und machst dir darum auch keine Sorgen wegen dem �ffchen, das scheinbar entt�uscht zur�ck in den Wald l�uft.`0";
		}
		break;
		case 3:
		$session['message'] ="`^Ein kleines �ffchen wirft dir einen Edelstein an den Kopf und verschwindet im Wald. Du verlierst ein paar Lebenspunkte, aber der Edelstein l�sst dich den �rger dar�ber vergessen.`0";
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
