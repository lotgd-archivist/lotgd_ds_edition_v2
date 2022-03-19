<?php
/***************************************************
  Waldevent - "Der Goblin im Wald"
  by Alexander Glatho
  gecodet f�r www.lotgd.drachenserver.de
  Version 1.0
***************************************************/


if (!isset($session)) exit();

$session['user']['specialinc'] = 'goblin.php';

if ($_GET['op']==''){

    //Der misteri�se Goblin - Wahl ob man trinken will oder nicht
    
    output('`@Du streifst etwas geschafft durch den Wald. Da entdeckst du durch Zufall einen kleinen Stand aus Holz, hinter welchem ein ebenso kleiner `QGoblin `@steht. Als du ihn ansprichst und fragst was er denn hier mache, antwortet er dir nur barsch, dass er `QErfrischungsgetr�nke `@verkauft. Das kommt dir wie gerufen denkst du. Dennoch, was macht hier ein Goblin mitten im Wald und ganz alleine?`n`n');
	output('`@Du denkst kurz nach, was willst du tun?`n`n');
	addnav('Nimm ein Getr�nk (100Gold)','forest.php?op=drink');
	addnav('Ich gehe lieber wieder ...','forest.php?op=goback');
	$session['user']['specialinc'] = 'goblin.php';
	
}

elseif ($_GET['op']=='drink'){

    // Abfangen wenn der User das Gold gar nicht besitzt ..

    if ($session['user']['gold']<100) {

        output('`@Wie peinlich, du hast gar nicht genug Gold dabei. Der `QGoblin `@zieht �rgerlich das Getr�nk zur�ck und jagt dich davon.');
        addnav('Schnell weg hier','forest.php');
        $session['user']['specialinc']='';
        
    }
    else {

    $session['user']['gold']-=100;
    output('`@Du trinkst das `QErfrischungsgetr�nk `@ mit einem Zug leer und ... `n`n`n');
    $session['user']['specialinc']='';

    // Zufallsm�glichkeiten, was nach Einnahme des Getr�nkes passiert
    // Geht von +- drei Waldk�mpfen, �ber erh�hte oder erniedrigte Lebenspunkte bisthin zu extra Erfahrung oder dem Tod ohne Verlust von Erfahrung

    switch(e_rand(1,6)){
        case 1 :
        output('`@... f�hlst dich richtig erfrischt, du kannst f�r heute dreimal mehr im Wald k�mpfen. Du bedankst dich bei dem Goblin `@ und rennst schnell in den Wald zur�ck.');
        $session['user']['turns']+=3;
        addnav('Juhu','forest.php');
	    break;
	    case 2 :
        output('`@... pl�tzlich wirst du m�de. Du f�llst um und wachst nach einer kurzen Zeit wieder auf. Das hat dich drei Waldrunden gekostet. H�ttest du nur auf dein Gef�hl geh�rt. Der `QGoblin `@ist mittlerweile �ber dich lachend in den Wald geflohen.');
        $session['user']['turns']-=3;
        addnav('Na toll...','forest.php');
	    break;
	    case 3 :
        output('`@... f�hlst dich richtig erfrischt, deine Lebenspunkte sind vorr�bergehend leicht erh�ht . Du bedankst dich bei dem Goblin `@ und rennst schnell in den Wald zur�ck.');
        $session['user']['hitpoints']=$session['user']['maxhitpoints']*1.25;
        addnav('Juhu','forest.php');
	    break;
	    case 4 :
        output('`@... pl�tzlich wird dir schlecht. Du beginnst zu taumeln und f�llst um. Als du nach kurzer Zeit wieder aufwachst ist dein gesamtes Gold weg und du hast eine gro�e Beule am Kopf. H�ttest du nur auf dein Gef�hl geh�rt. Der `QGoblin `@ist mittlerweile �ber dich lachend in den Wald geflohen.');
        $session['user']['hitpoints']=1;
        $session['user']['turns']--;
        $session['user']['gold']=0;
        addnav('Na toll...','forest.php');
	    break;
	    case 5 :
        output('`@... f�hlst du dich ein wenig erfahrener . Du bedankst dich bei dem Goblin `@ und rennst schnell in den Wald zur�ck.');
        $session['user']['experience']=$session['user']['experience']*1.05;
        addnav('Juhu','forest.php');
	    break;
	    case 6 :
        output('`@... pl�tzlich bekommst du brutale Magenschmerzen. Kurz darauf f�llst du um. `$Du bist tot. `@Der `QGoblin `@verschwindet auch mit deinem restlichen Gold im Wald und du kannst erst morgen wieder weiterspielen.');
        $session['user']['alive']=false;
        $session['user']['hitpoints']=0;
		$session['user']['gold']=0;
        addnav('Na toll...','news.php');
	    break;
	   }
    
    }
}

// Man kann ja niemanden zwingen etwas zu trinken .. also ist das hier die R�ckkehr in den Wald

elseif ($_GET['op']=='goback'){

    output('`@Ein `QGoblin `@allein im Wald der dir irgendwas verkaufen will? Das kann nichts Gutes bedeuten. Du beschlie�t, doch lieber weiterzugehen.`n`n');
	addnav('Schnell weg hier','forest.php');
    $session['user']['specialinc']='';
	
}

?>
