<?php

// 27052004

/*
* Brunnen der Edelsteine (edelsteinbrunnen.php)
* written by Reincarnationofdeath
* coded by anpera
*/

if ($_GET['op']=="weg") {
	output("`3Du h�ltst nichts von glitzerndem Wasser und gehst weiter.`n`n");
	$session[user][specialinc]="";
}else if ($_GET['op']=="doppeln"){
	$session[user][specialinc]="edelsteinbrunnen.php";
	if ($session[user][gems]<=0){
		output("`3Du hast keine Edelsteine dabei, so machst du dich wieder auf den Weg.`n`n");
		$session[user][specialinc]="";
	}else{
		output("`n`c`b`#Der Brunnen der Edelsteine`b`c`n`n");
		output("`3Du atmest nochmal kr�ftig durch und �berlegst dir, mit wie vielen Edelsteinen du dein Gl�ck versuchen willst.`n");
		output("<form action='forest.php?op=aufi' method='POST'>",true);
		output("`nWie viele Edelsteine riskierst du? <input type='text' id='zahl' name='zahl' maxlength='2' size='5'>",true);
		output("<input type='submit' class='button' value='Los'></form>",true);
		addnav("","forest.php?op=aufi");
		addnav("Zur�ck in den Wald","forest.php?op=weg");
	}
}else if ($_GET['op']=="aufi"){
	$session[user][specialinc]="edelsteinbrunnen.php";
	output("`n`c`b`#Der Brunnen der Edelsteine`b`c`n`n");
	if($_POST[zahl]<=0 || $_POST[zahl]>$session[user][gems]){
		output("`3Da du nicht genau wei�t, wieviele Edelsteine du dabei hast, z�hlst du sicherheitshalber nochmal nach.");
		addnav("Nochmal versuchen","forest.php?op=doppeln");
		addnav("Zur�ck in den Wald","forest.php?op=weg");
	}else if ($_POST[zahl]>5){
		output("`3Du f�ngst an, die Edelsteine vorsichtig und einzeln ins Wasser zu legen, aber beim $_POST[zahl]. Stein spukt der Brunnen pl�tzlich alle Steine wieder aus. Vielleicht waren es zu viele?");
		addnav("Nochmal versuchen","forest.php?op=doppeln");
		addnav("Zur�ck in den Wald","forest.php?op=weg");
	}else{
		if (e_rand(1,2)==2){
			output("`3Der Brunnen leuchtet und glitzert auf einmal unheimlich stark und um dich herum wird es dunkel. Als es sich wieder aufhellt, bemerkst du, dass vor dem Brunnen `#".($_POST[zahl]*2)." `3Edelsteine liegen! �bergl�cklich dar�ber, dass der Zauber funktioniert hat, kehrst du mit deiner erweiterten Edelsteinsammlung nach Hause.`n`n");
			$session[user][gems]+=$_POST[zahl];
		}else{
			output("`3Du legst deine Edelsteine ins Wasser, aber nichts passiert. Du wartest etwas, doch sie liegen immer noch still darin. Da entschlie�t du dich, sie wieder herauszuholen und von diesem Ort zu verschwinden. Doch als du nach deinen Edelsteinen greifst, schl�gt ein Blitz vor dir ein und schleudert dich zur�ck. Du kannst eine Stimme h�ren: \"`#Was ich einmal habe, gebe ich nicht mehr zur�ck, muahahahaha`3\"`nDu rennst so schnell wie m�glich weg, ohne deine Edelsteine, aber mit deinem Leben.`n`n");
			$session[user][gems]-=$_POST[zahl];
		}
		$session[user][specialinc]="";
	}
}else{
	output("`n`c`b`#Der Brunnen der Edelsteine`b`c`n`n");
	output("`3Als du durch den Wald l�ufst, siehst du pl�tzlich einen Weg, an dessen Ende etwas glitzert. Dort angekommen, siehst du einen wundersch�nen Brunnen, dessen Wasser bunt glitzert. Auf einer angebrachten Schrifttafel steht geschrieben: \"`^`iDas Wasser dieses Brunnens vermag Edelsteine
 zu verdoppeln. Jedoch ist der Brunnen aufgrund des launischen Geistes, der ihm die Magie verlieht, unberechenbar. Was jedoch einmal hergegeben wurde, ist nicht zur�ckzuholen.`i`3\"");
	output("`n`nWillst du...`n`n... deine kostbaren Edelsteine nicht aufs Spiel setzen und <a href='forest.php?op=weg'>diesen Ort verlassen?</a>`n
 ... in deiner Gier die Warnung unbeachtet lassen und versuchen, <a href='forest.php?op=doppeln'>einige deiner Edelsteine zu verdoppeln</a>?",true);
	$session[user][specialinc]="edelsteinbrunnen.php";
	addnav("","forest.php?op=doppeln");
	addnav("","forest.php?op=weg");
	addnav("Verdopplung versuchen","forest.php?op=doppeln");
	addnav("Zur�ck in den Wald","forest.php?op=weg");
}
?>