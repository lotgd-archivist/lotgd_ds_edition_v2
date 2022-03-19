<?php



// Original By Anpera  )(Aphrodite.php)(
//changes by Amerilion
// First try comments tosteffenmischnick@gmx.de
// small adjustments by by Hadriel @ hadrielnet.ch
// code cleanup and orthographic fixes by talion

require_once "common.php";


if (!isset($session)) exit();

if ($_GET['op']=="do"){

	output("`%Du ziehst deine Waffe und greifst die Kreatur an. ");
	output("Sie zerplatzt beim ersten Schlag mit einem lauten Knall - ");
	output("Als du wieder etwas hören kannst bemerkst du, dass ...`n`n`^");
	switch(e_rand(1,10)){
		case 1:
		case 2:
		output("an dem Ort wo die Kreatur stand, nur noch ein Baum ist. Du denkst, dass du dir nicht mehr so viel einbilden solltest und ziehst erschöpft weiter.");
		$session[user][turns]-=1;
		$session[user][experience]+=150;
		break;
		case 3:
		case 4:
		output("die Kreatur bis auf ihre Schuhe verschwunden ist. Du denkst, dass die Schuhe als Beweis reichen, dass du nicht alles geträumt hast und ziehst ermutigt weiter.");
		$session[user][turns]+=1;
		$session[user][experience]+=150;
		break;
		case 5:
		output("die Kreatur verschwunden ist. Anstelle von ihr liegen nur einige Edelsteine auf der Wiese.");
		$session[user][gems]+=3;
		$session[user][experience]+=150;
		break;
		case 6:
		output("du den Rauch, der entstand, eingeatmet hast und dich nun widerstandsfähiger fühlst.");
		$session[user][maxhitpoints]+=1;
		$session[user][experience]+=150;
		break;
		case 7:
		case 8:
		output("du durch den Knall wohl auch in Zukunft etwas schwächer sein wirst.");
		$session[user][maxhitpoints]-=1;
		$session[user][experience]+=100;
		case 9:
		case 10:
		increment_specialty();
		break;
	}
	addnews($session[user][name]."`r begegnete einer absonderlichen Kreatur.");
	$session[user][specialinc]="";
}
else if ($_GET['op']=="dont"){
	output("Du denkst an ".($session[user][sex]?"deinen Geliebten":"deine Geliebte")." und rennst zu ".($session[user][sex]?"ihr":"ihm")." da du dir nun doch nicht so sicher bist was ".($session[user][sex]?"ihm":"ihr")." passiert ist.Die Chimäre verschwindet spottend im Wald");
	$session[user][specialinc]="";
}
else{
	if ($session['user']['sex']>0){
		output("`%Als du nach Monstern suchend den Wald duchstreifst,siehst du eine seltsame Kreatur.Sie sieht aus wie eine Mischung aus einem Troll und einer Ratte und deinem Geliebten!!!\"`^Ich sein eine Chimäre und ich sein sehr mächtig und werde du töten wenn du mich auch nur antippst`%\"krächzt sie dir zu.Du vermutest das du vor Hexenwerk stehst und du stehst vor folgender Entscheidung?");
	}
	else{
		output("`%Als du nach Monstern suchend den Wald duchstreifst,siehst du eine seltsame Kreatur.Sie sieht aus wie eine Mischung aus einem Troll und einer Ratte und deiner Geliebten!!!\"`^Ich sein eine Chimäre und ich sein sehr mächtig und werde du töten wenn du mich auch nur antippst`%\"krächzt sie dir zu.Du vermutest das du vor Hexenwerk stehst und du stehst vor folgender Entscheidung?");
	}
	addnav("Greife an","forest.php?op=do");
	addnav("Laufe weg","forest.php?op=dont");
	$session[user][specialinc]="schimaere.php";
}
?>
