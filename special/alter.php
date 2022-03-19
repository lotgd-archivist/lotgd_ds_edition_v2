<?php
if (!isset($session)) exit();
if ($HTTP_GET_VARS[op]==""){
  output("`@Du stolperst über eine Lichtung und bemerkst einen Altar mit 5 Seiten vor dir. Auf jeder Seite liegt ein anderer Gegenstand. Du siehst `#einen Dolch, `\$einen Schädel,`% einen juwelenbesetzten Stab, `^ein Rechenbrett `7und ein schlicht aussehendes Buch. `@In der Mitte über dem Altar befindet sich ein `&Kristallblitz.`n`n");
	output("  `@Du weißt, daß es dich Zeit für einen ganzen Waldkampf kosten wird, einen der Gegenstände näher zu untersuchen.`n`n`n");
	addnav("Nimm den Dolch","forest.php?op=dagger");
	addnav("Nimm den Schädel","forest.php?op=skull");
	addnav("Nimm den Stab","forest.php?op=wand");
	addnav("Nimm das Rechenbrett","forest.php?op=abacus");
	addnav("Nimm das Buch","forest.php?op=book");
	addnav("Nimm den Kristallblitz","forest.php?op=bolt");
	addnav("Verlasse den Altar unberührt","forest.php?op=forgetit");
	$session[user][specialinc] = "alter.php";

}else if ($HTTP_GET_VARS[op]=="dagger"){
  $session[user][turns]--; 
	if (e_rand(0,1)==0){
	  output("`#Du nimmst den Dolch von seinem Platz. Doch er löst sich in deinen Händen in Luft auf und du fühlst eine Welle von Energie in deinen Körper strömen!`n`n  `&Du erhältst 10 zusätzliche Anwendungen in Diebeskünsten.`n`n`#Aber du bist auch etwas traurig, denn diese Kraft wird morgen wieder verschwunden sein.");
		$session['user']['specialtyuses'][thieveryuses] = $session['user']['specialtyuses'][thieveryuses] + 10;
	}else{
    output("`#Du nimmst den Dolch von seinem Platz. Doch er löst sich in deinen Händen in Luft auf und du fühlst eine Welle von Energie in deinen Körper strömen!`n`n  `&Du erhältst 3 Level in Diebeskünsten!");
		$session['user']['specialtyuses'][thievery] = $session['user']['specialtyuses'][thievery] + 3;
		$session['user']['specialtyuses'][thieveryuses]++;
	}
	addnav("Zurück in den Wald","forest.php");
	$session[user][specialinc]="";

}else if ($HTTP_GET_VARS[op]=="skull"){
  $session[user][turns]--; 
	if (e_rand(0,1)==0){
	  output("`#Du greifst nach dem Schädel. Vor deinen Augen löst sich der Schädel auf und du fühlst eine Energiewelle in deinen Körper fahren!`n`n  `&Du erhältst 10 zusätzliche Anwendungen der Dunklen Künste.`n`n`#Aber du bist auch etwas traurig, denn diese Kraft wird morgen wieder verschwunden sein.");
		$session[user][specialtyuses][darkartuses] = $session[user][specialtyuses][darkartuses] + 10;
	}else{
    output("`#Du greifst nach dem Schädel. Vor deinen Augen löst sich der Schädel auf und du fühlst eine Energiewelle in deinen Körper fahren!`n`n  `&Du erhältst 3 Levels in Dunklen Künsten!");
		$session['user']['specialtyuses'][darkarts] = $session['user']['specialtyuses'][darkarts] + 3;
		$session[user][specialtyuses][darkartuses]++;
	}
	addnav("Zurück in den Wald","forest.php");
	$session[user][specialinc]="";

}else if ($HTTP_GET_VARS[op]=="wand"){
  $session[user][turns]--; 
	if (e_rand(0,1)==0){
	  output("`#Du hebst den Stab von seinem Platz auf. In einem Lichtblitz verschwindet er und eine seltsame Kraft durchströmt deinen Körper!`n`n  `&Du erhältst 10 zusätzliche Anwendungen in Mystischen Kräften.`n`n`#Aber du bist auch etwas traurig, denn diese Kraft wird morgen wieder verschwunden sein.");
		$session['user']['specialtyuses'][magicuses] = $session['user']['specialtyuses'][magicuses] + 10;
	}else{
    output("`#Du hebst den Stab von seinem Platz auf. In einem Lichtblitz verschwindet er und eine seltsame Kraft durchströmt deinen Körper!`n`n  `&Du erhältst 3 Levels in Mystischen Kräften!");
		$session['user']['specialtyuses'][magic] = $session['user']['specialtyuses'][magic] + 3;
		$session['user']['specialtyuses'][magicuses]++;
	}
	addnav("Zurück in den Wald","forest.php");
	$session[user][specialinc]="";

}else if ($HTTP_GET_VARS[op]=="abacus"){
  $session[user][turns]--; 
	if (e_rand(0,1)==0){
	  $gold = e_rand($session[user][level]*30,$session[user][level]*90);
	  $gems = e_rand(1,4);
	  output("`#Du nimmst das Rechenbrett von seinem Platz.  Das Rechenbrett verwandelt sich in einen Beutel voller Gold und Edelsteine!`n`n Du bekommst $gold Goldstücke und $gems Edelsteine!");
		$session[user][gold]+=$gold;
		$session[user][gems]+=$gems;
	}else{
		$gold = $session[user][gold]+($session[user][level]*20);
    output("`@`#Du nimmst das Rechenbrett von seinem Platz.  Das Rechenbrett verwandelt sich in einen Beutel voller Gold!`n`n Du bekommst $gold Goldstücke!");
		$session[user][gold]+=$gold;
		}
	addnav("Zurück in den Wald","forest.php");
	$session[user][specialinc]="";

}else if ($HTTP_GET_VARS[op]=="book"){
  $session[user][turns]--; 
	if (e_rand(0,1)==0){
	  $exp=$session[user][experience]*0.15;
	  output("`#Du nimmst das Buch und beginnst darin zu lesen. Das Wissen in diesem Buch hilft dir viel weiter und du legst es an seinen Platz zurück, damit ein anderer auch noch davon profitieren kann.`n`nDu bekommst $exp Erfahrungspunkte!");
		$session[user][experience]+=$exp;
	}else{
		$ffights = e_rand(1,5);
    output("`@`#Du nimmst das Buch und beginnst darin zu lesen.  Das Buch enthält ein Geheimnis, wie du deine heutigen Streifzüge durch den Wald profitabler gestalten kannst.  Du legst das Buch an seinen Platz zurück, damit ein anderer auch noch davon profitieren kann.`n`nDu bekommst $ffights zusätzliche Waldkämpfe!");
		$session[user][turns]+=$ffights;
		}
	addnav("Zurück in den Wald","forest.php");
	$session[user][specialinc]="";

}else if ($HTTP_GET_VARS[op]=="bolt"){
  $session[user][turns]--; 
	$bchance=e_rand(0,7);
	if ($bchance==0){
		    output("`#Du greifst nach dem Kristallblitz.  Der Blitz verschwindet aus deinen Händen und erscheint wieder auf dem Altar. Nach einigen Versuchen, den Blitz zu bekommen, hast du keine Lust mehr, noch mehr Zeit damit zu vergeuden. Du fürchtest auch, die Götter dadurch herauszufordern.");
			addnav("Zurück in den Wald","forest.php");
		}elseif ($bchance==1){
			output("`#Du greifst nach dem Kristallblitz. Als du den Blitz gerade berührst, wirst du rückwärts auf den Boden geschleudert. Du kommst schnell wieder auf die Beine und fühlst dich sehr mächtig!`n`nDu bekommst 10 Anwendungen in allen Fertigkeiten! Leider spürst du, daß diese Macht nicht einmal bis zum nächsten Morgen halten wird.");
			$session[user][specialtyuses][thieveryuses]+=10;
			$session[user][specialtyuses][darkartuses]+=10;
			$session[user][specialtyuses][magicuses]+=10;
			addnav("Zurück in den Wald","forest.php");
		}elseif($bchance==2){
			output("`#Du greifst nach dem Kristallblitz. Als du den Blitz gerade berührst, wirst du rückwärts auf den Boden geschleudert. Du kommst schnell wieder auf die Beine und fühlst dich sehr mächtig!`n`nDu steigst in jeder Fertigkeit 3 Level auf!");
			$session[user][specialtyuses][thievery]+=3;
			$session[user][specialtyuses][darkarts]+=3;
			$session[user][specialtyuses][magic]+=3;
			$session[user][specialtyuses][thieveryuses]++;
			$session[user][specialtyuses][darkartuses]++;
			$session[user][specialtyuses][magicuses]++;
			addnav("Zurück in den Wald","forest.php");
		}elseif($bchance==3){
			output("`#Du greifst nach dem Kristallblitz. Als du den Blitz gerade berührst, wirst du rückwärts auf den Boden geschleudert. Du kommst schnell wieder auf die Beine und fühlst dich sehr mächtig!`n`nDu bekommst 5 zusätzliche Lebenspunkte!");
			$session[user][maxhitpoints]+=5;
			$session[user][hitpoints]+=5;
			addnav("Zurück in den Wald","forest.php");
		}elseif($bchance==4){
			output("`#Du greifst nach dem Kristallblitz. Als du den Blitz gerade berührst, wirst du rückwärts auf den Boden geschleudert. Du kommst schnell wieder auf die Beine und fühlst dich sehr mächtig!`n`nDu bekommst 2 Angriffspunkte und 2 Verteidigungspunkte dazu!");
			$session[user][attack]+=2;
			$session[user][defence]+=2;
			addnav("Zurück in den Wald","forest.php");
		}elseif($bchance==5){
			$exp=$session[user][experience]*0.2;
			output("`#Du greifst nach dem Kristallblitz. Als du den Blitz gerade berührst, wirst du rückwärts auf den Boden geschleudert. Du kommst schnell wieder auf die Beine und fühlst dich sehr mächtig!`n`nDu bekommst $exp Erfahrungspunkte!");
			$session[user][experience]+=$exp;
			addnav("Zurück in den Wald","forest.php");
		}elseif($bchance==6){
			$exp=$session[user][experience]*.2;
			output("`#Deine Hand nähert sich dem Kristallblitz, als der Himmel plötzlich vor Wolken überkocht. Du fürchtest, die Götter verärgert zu haben und beginnst zu rennen. Doch noch bevor du die Lichtung verlassen kannst, wirst du von einem Blitz getroffen.`n`nDu fühlst dich dümmer!  Du verlierst $exp Erfahrungspunkte!");
			$session[user][experience]-=$exp;
			addnav("Zurück in den Wald","forest.php");
		}else{
			output("`#Deine Hand nähert sich dem Kristallblitz, als der Himmel plötzlich vor Wolken überkocht. Du fürchtest, die Götter verärgert zu haben und beginnst zu rennen. Doch noch bevor du die Lichtung verlassen kannst, wirst du von einem Blitz getroffen.`n`nDu bist tot!");
			output("Du verlierst 5% deiner Erfahrungspunkte und all dein Gold!`n`n");
			output("Du kannst morgen wieder spielen.");
			$session[user][alive]=false;
            $session[user][hitpoints]=0;
			$session[user][gold]=0;
            $session[user][experience]=$session[user][experience]*0.95;
			addnav("Tägliche News","news.php");
            addnews($session[user][name]." wurde von den Göttern niedergeschmettert, da ".($session[user][sex]?"sie":"er")." von Gier zerfressen war!");
		}
	$session[user][specialinc]="";

}else if ($HTTP_GET_VARS[op]=="forgetit"){
  output("`@Du beschließt, das Schicksal lieber nicht herauszufordern und dadurch womöglich die Götter zu verärgern. Du läßt den Altar in Ruhe.");
	output("Als du die Lichtung gerade verlassen willst, stolperst du über ein Beutelchen mit einem Edelstein! Die Götter müssen dir wohlgesonnen sein!");
	$session[user][gems]+=1;
	//addnav("Zurück in den Wald","forest.php");
	$session[user][specialinc]="";
}
?>
