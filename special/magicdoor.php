<?php

// idea and coding by: Joshua Schmidtke
//
// more applaction at: www.jumanet.info
// 
//build: 2006-01-11

if (!isset($session)) exit();

$session[user][specialinc]="magicdoor.php";

switch($HTTP_GET_VARS[op])
	{
	case "check":
		output("`@Du schaust dir die T�r genauer an und bemerkst einen goldenen T�rgriff. Alles in dir verlang diese T�r zu �ffnen. Als du den T�rgriff ber�hrst f�ngt er hell an zu leuchten und du h�rst eine Stimme in deinem Kopf sagen:`n`n");
		output("`^\"Ich gehe erst auf, wenn du mir einen Tribut zahlst!\"`n`n");
		output("`@Was m�chtest du der T�r zahlen?`n`n");
		addnav("Nichts", "forest.php?op=0");
		addnav("1000 Gold", "forest.php?op=1");
		addnav("1 Edelstein", "forest.php?op=2");
		addnav("Blut opfern", "forest.php?op=3");
		addnav("Fl�chten", "forest.php?op=away");
		break;

	case "0":
		output("`@Du gedenkst, nichts zu opfern, als du merkst, wie deine Hand immer w�rmer wird.`n`n");
		output("`^\"Du bist ein schlechter Mensch. Baal hat Verlangen nach dir!\"`n`n");
		output("`@Mit diesen Worten entz�ndet sich deine Hand und dein ganzer K�rper geht in Flammen auf.");
		addnav("T�gliche News","news.php");
		$session[user][alive]=false;
		$session[user][hitpoints]=0;
		$session[user][specialinc]="";
		addnews("`4".$session[user][name]."`& ist bei dem Versuch, eine T�r zu �ffnen, in Flammen aufgegangen.");
		break;
		
	case "1":
		if ($session[user][gold]>=1000)
			{
			output("`@Du denkst daran, ein bisschen Gold zu opfern, als der Beutel auch schon in Flammen aufgeht.`n`n");
			output("`^\"Du hast ein teures Opfer gebracht! Ich lasse dich eintreten.\"");
			addnav("Eintreten", "forest.php?op=magicroom");
			$session[user][gold]=$session[user][gold]-1000;
			}
			
		else
			{
			output("`@Du m�chtest Gold opfern, doch es f�llt dir ein, dass du gar keines mehr hast. Du merkst auf einmal, wie deine Hand warm wird und Verbrennungen davontr�gt.`n`n");
			output("`^\"Das soll dir eine Lehre sein! Nun geh!\"");
			addnav("Wegrennen", "forest.php");
			$session[user][specialinc]="";
			
			if ($session[user][hitpoints]>1)
				{
				$session[user][hitpoints]=intval($session[user][hitpoints]/2);
				}
			}
		break;
		
	case "2":
		if ($session[user][gems]>=1)
			{
			output("`@Du denkst daran, einen Edelstein zu opfern, als der Beutel auch schon in Flammen aufgeht.`n`n");
			output("`^\"Du hast ein edles Opfer gebracht! Ich lasse dich eintreten.\"");
			addnav("Eintreten", "forest.php?op=magicroom");
			$session[user][gems]--;
			}
			
		else
			{
			output("`@Du m�chtest einen Edelstein opfern, doch es f�llt dir ein, dass du gar keine mehr hast. Du merkst auf einmal, wie deine Hand warm wird und Verbrennungen davontr�gt.`n`n");
			output("`^\"Das soll dir eine Lehre sein! Nun geh!\"");
			addnav("Wegrennen", "forest.php");
			$session[user][specialinc]="";
			
			if ($session[user][hitpoints]>1)
				{
				$session[user][hitpoints]=intval($session[user][hitpoints]/2);
				}
			}
		break;
		
	case "3":
		$failingvictim=rand(2,3);
	
		if (($session[user][hitpoints]>1) and ($session[user][turns]>=$failingvictim))
			{
			output("`@Du denkst daran, dein Blut zu opfern als du merkst, wie du dich immer schw�cher f�hlst.`nDer T�rgriff f�rbt sich rot und die Stimme sagt:`n`n");
			output("`^\"Du hast ein gro�es Opfer abgelegt. Ich lasse dich eintreten.\"`n`n");
			output("`4`bDurch den Schw�cheanfall hast du $failingvictim Waldk�mpfe weniger.`b");
			addnav("Eintreten", "forest.php?op=magicroom");
			$session[user][hitpoints]=1;
			$session[user][turns]=$session[user][turns]-$failingvictim;
			}
			
		else
			{
			output("`@Du denkst daran, dein Blut zu opfern, doch du bist zu ersch�pft daf�r.");
			addnav("Zur�ck", "forest.php?op=check");
			}
		break;
		
	case "magicroom":
		page_header("Baal's magischer Raum");
		output("`@Die T�r �ffnet sich und du machst einen Schrit in den Baum. Sofort schlie�t sich die T�r hinter dir und du fragst dich, was es hier wohl geben wird. Du gehst weiter und...`n`n");
		
		switch(rand(0,10))
			{
			case 0:
				output("`@betrittst den `4Raum des Todes`@. �berall wandeln Seelen von gefallenen Kriegern und Monstern herum und in der Mitte des Raums steht ein Altar mit einem Trank. Du gehts zu dem Trank und trinkst ihn aus. Dabei bemerkst du, wie er deine Seele s�ubert.`n`n");
				output("`4`bDu hast 5 Gefallen im Totenreich bekommen.`b`n`n");
				output("`@Der Raum f�ngt an sich zu drehen. Dir wird ganz schwindelig und als der Raum zum Stehen kommt, befindest du dich wieder im Wald. Du glaubst kaum, dass dies nur ein Traum war.");
				addnav("Weiter", "forest.php");
				$session[user][specialinc]="";
				$session['user']['deathpower']=$session['user']['deathpower']+5;
				break;
				
			case 1:
				output("`@betrittst den `4Raum des Wissens`@. �berall stehen Regale mit B�chern herum. Du l�sst dir ein wenig Zeit um dir ein paar B�cher durchzulesen.`n`n");
				output("`4`bDu hast deine Erfahrung um 5% verbessert.`b`n`n");
				output("`@Der Raum f�ngt an sich zu drehen. Dir wird ganz schwindelig und als der Raum zum Stehen kommt, befindest du dich wieder im Wald. Du glaubst kaum, dass dies nur ein Traum war.");
				addnav("Weiter", "forest.php");
				$session[user][specialinc]="";
				$session[user][experience]=intval($session[user][experience]*1.05);
				break;
				
			case 2:
				output("`@betrittst den `4Raum der Sch�tze`@. �berall stehen Kisten mit Gold herum. Du denkst, dass es schon keiner merken wird, wenn du ein wenig Gold mitnimmst.`n`n");
				$goldtreasure=rand(100,2000);
				output("`4`bDu hast dir $goldtreasure Gold genommen.`b`n`n");
				output("`@Der Raum f�ngt an sich zu drehen. Dir wird ganz schwindelig und als der Raum zum Stehen kommt, befindest du dich wieder im Wald. Du glaubst kaum, dass dies nur ein Traum war.");
				addnav("Weiter", "forest.php");
				$session[user][specialinc]="";
				$session[user][gold]=intval($session[user][gold]+$goldtreasure);
				break;
				
			case 3:
				output("`@betrittst den `4Raum des Juweliers`@. �berall stehen Kisten mit Diamanten, Rubinen und Smaragden herum. In der Mitte des Raumes steht ein Handwerkertisch mit einem Beutel. Beim n�heren betrachten des Beutels merkst du, dass dein Name auf dem Beutel steht und du denkst dir, dass der Beutel wohl f�r dich ist.`n`n");
				$gemtreasure=rand(2,3);
				output("`4`bDu hast $gemtreasure Edelsteine im Beutel gefunden.`b`n`n");
				output("`@Der Raum f�ngt an sich zu drehen. Dir wird ganz schwindelig und als der Raum zum Stehen kommt, befindest du dich wieder im Wald. Du glaubst kaum, dass dies nur ein Traum war.");
				addnav("Weiter", "forest.php");
				$session[user][specialinc]="";
				$session[user][gems]=intval($session[user][gems]+$gemtreasure);
				break;
				
			case 4:
			case 5:
			case 6:
			case 7:
			case 8:
			case 9:
			case 10:
				output("`@betrittst den `4Raum der unsterblichen Ruhe`@. �berall stehen Betten und Brunnen mit klarem k�hlem Wasser herum. In der Mitte des Raumes steht ein prachtvoller Brunnen mit gr�n-blauem Wasser. Du bist ersch�pft und trinkst von dem Wasser.`n`n");
				output("`4`bDeine Lebenspunkte regenerieren und du erh�ltst 2 Waldk�mpfe.`b`n`n");
				output("`@Der Raum f�ngt an sich zu drehen. Dir wird ganz schwindelig und als der Raum zum Stehen kommt, befindest du dich wieder im Wald. Du glaubst kaum, dass dies nur ein Traum war.");
				addnav("Weiter", "forest.php");
				$session[user][specialinc]="";
				$session[user][hitpoints]=$session[user][maxhitpoints];
				$session[user][turns]=$session[user][turns]+2;
				break;
			}
		break;
	
	case "back":
		output("`@Du h�lst nicht viel von dieser Sache und meinst, dass es besser ist, die T�r einfach zu vergessen.");
		addnav("Zur�ck in den Wald", "forest.php");
		$session[user][specialinc]="";
		break;
		
	case "away":
		switch(rand(0,10))
			{
			case 0: case 1: case 2: case 3: case 4: case 5: case 8: case 9: case 10:
				output("`@Du versuchst der T�r zu entkommen, aber deine Hand ist wie festgeklebt.");
				addnav("Ahh,... Mist!", "forest.php?op=check");
				break;
				
			case 6: case 7:
				output("`@Du schafftst es, deine Hand von dem T�rgriff loszurei�en und rennst so schnell du kannst.");
				addnav("Lauf, Johnny!", "forest.php");
				$session[user][specialinc]="";
				break;
			}
		break;

	default:
		output("`@Auf der Suche nach weiteren Gegnern bemerkst du nicht, dass du den Weg verlassen hast und auf einer gro�en Lichtung gelandet bist. In der Mitte dieser Lichtung steht eine gro�e Eiche.`nDu gehst auf die Eiche zu und bemerkst, dass in ihr eine T�r eingelassen ist. Neben der T�r steht ein Schild:`n`n");
		output("`^Dies ist die gro�e T�r des Gottes `QBaal`^. Sie offenbart jedem eine andere Dimension und hat die F�higkeit, Freude oder Leid �ber die Person zu bringen, die es wagt, einzutreten.`n`nGezeichnet `QBaal`n`n");
		output("`@Was willst du machen?`n`n");
		addnav("T�r untersuchen", "forest.php?op=check");
		addnav("Wegrennen", "forest.php?op=back");
		break;
	}

?>