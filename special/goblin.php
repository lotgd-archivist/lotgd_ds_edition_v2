<?php
/***************************************************
  Waldevent - "Der Goblin im Wald"
  by Alexander Glatho
  gecodet für www.lotgd.drachenserver.de
  Version 1.0
***************************************************/


if (!isset($session)) exit();

$session['user']['specialinc'] = 'goblin.php';

if ($_GET['op']==''){

    //Der misteriöse Goblin - Wahl ob man trinken will oder nicht
    
    output('`@Du streifst etwas geschafft durch den Wald. Da entdeckst du durch Zufall einen kleinen Stand aus Holz, hinter welchem ein ebenso kleiner `QGoblin `@steht. Als du ihn ansprichst und fragst was er denn hier mache, antwortet er dir nur barsch, dass er `QErfrischungsgetränke `@verkauft. Das kommt dir wie gerufen denkst du. Dennoch, was macht hier ein Goblin mitten im Wald und ganz alleine?`n`n');
	output('`@Du denkst kurz nach, was willst du tun?`n`n');
	addnav('Nimm ein Getränk (100Gold)','forest.php?op=drink');
	addnav('Ich gehe lieber wieder ...','forest.php?op=goback');
	$session['user']['specialinc'] = 'goblin.php';
	
}

elseif ($_GET['op']=='drink'){

    // Abfangen wenn der User das Gold gar nicht besitzt ..

    if ($session['user']['gold']<100) {

        output('`@Wie peinlich, du hast gar nicht genug Gold dabei. Der `QGoblin `@zieht ärgerlich das Getränk zurück und jagt dich davon.');
        addnav('Schnell weg hier','forest.php');
        $session['user']['specialinc']='';
        
    }
    else {

    $session['user']['gold']-=100;
    output('`@Du trinkst das `QErfrischungsgetränk `@ mit einem Zug leer und ... `n`n`n');
    $session['user']['specialinc']='';

    // Zufallsmöglichkeiten, was nach Einnahme des Getränkes passiert
    // Geht von +- drei Waldkämpfen, über erhöhte oder erniedrigte Lebenspunkte bisthin zu extra Erfahrung oder dem Tod ohne Verlust von Erfahrung

    switch(e_rand(1,6)){
        case 1 :
        output('`@... fühlst dich richtig erfrischt, du kannst für heute dreimal mehr im Wald kämpfen. Du bedankst dich bei dem Goblin `@ und rennst schnell in den Wald zurück.');
        $session['user']['turns']+=3;
        addnav('Juhu','forest.php');
	    break;
	    case 2 :
        output('`@... plötzlich wirst du müde. Du fällst um und wachst nach einer kurzen Zeit wieder auf. Das hat dich drei Waldrunden gekostet. Hättest du nur auf dein Gefühl gehört. Der `QGoblin `@ist mittlerweile über dich lachend in den Wald geflohen.');
        $session['user']['turns']-=3;
        addnav('Na toll...','forest.php');
	    break;
	    case 3 :
        output('`@... fühlst dich richtig erfrischt, deine Lebenspunkte sind vorrübergehend leicht erhöht . Du bedankst dich bei dem Goblin `@ und rennst schnell in den Wald zurück.');
        $session['user']['hitpoints']=$session['user']['maxhitpoints']*1.25;
        addnav('Juhu','forest.php');
	    break;
	    case 4 :
        output('`@... plötzlich wird dir schlecht. Du beginnst zu taumeln und fällst um. Als du nach kurzer Zeit wieder aufwachst ist dein gesamtes Gold weg und du hast eine große Beule am Kopf. Hättest du nur auf dein Gefühl gehört. Der `QGoblin `@ist mittlerweile über dich lachend in den Wald geflohen.');
        $session['user']['hitpoints']=1;
        $session['user']['turns']--;
        $session['user']['gold']=0;
        addnav('Na toll...','forest.php');
	    break;
	    case 5 :
        output('`@... fühlst du dich ein wenig erfahrener . Du bedankst dich bei dem Goblin `@ und rennst schnell in den Wald zurück.');
        $session['user']['experience']=$session['user']['experience']*1.05;
        addnav('Juhu','forest.php');
	    break;
	    case 6 :
        output('`@... plötzlich bekommst du brutale Magenschmerzen. Kurz darauf fällst du um. `$Du bist tot. `@Der `QGoblin `@verschwindet auch mit deinem restlichen Gold im Wald und du kannst erst morgen wieder weiterspielen.');
        $session['user']['alive']=false;
        $session['user']['hitpoints']=0;
		$session['user']['gold']=0;
        addnav('Na toll...','news.php');
	    break;
	   }
    
    }
}

// Man kann ja niemanden zwingen etwas zu trinken .. also ist das hier die Rückkehr in den Wald

elseif ($_GET['op']=='goback'){

    output('`@Ein `QGoblin `@allein im Wald der dir irgendwas verkaufen will? Das kann nichts Gutes bedeuten. Du beschließt, doch lieber weiterzugehen.`n`n');
	addnav('Schnell weg hier','forest.php');
    $session['user']['specialinc']='';
	
}

?>
