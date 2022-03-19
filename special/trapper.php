<?
if (!isset($session)) exit();

if ($HTTP_GET_VARS['op']==""){
    $session['user']['specialinc']="trapper.php";
    output("`nDu begegnest einem `3Trapper`0, der allein durch den Wald streift. `0");
    $was =  e_rand(1,6) ;
    switch ( $was ) {
        case 1:
        output("Und während Du ihm freundlich winkst, gibt plötzlich der Boden unter
        Deinen Füssen nach. Du bist in eine Fallgrube gelaufen!`n
        Sofort eilt Dir der `3Trapper`0 zur Hilfe und zieht Dich heraus. \"Hier also war
        sie...\" hörst Du ihn murmeln. Er entschuldigt sich wortreich bei Dir, was
        Deine Schmerzen vom Sturz aber nicht lindern kann.`n
        `9Du verlierst einige Lebenspunkte.`0");
        $session['user']['hitpoints']=round( $session['user']['hitpoints']*0.9 );
		$session['user']['specialinc'] = "";
        break;
        case 2:
        output("\"HALT\" ruft er plötzlich, \"da ist eine Falle!\". Sofort bleibst
        Du stehen. Der Trapper kommt zu Dir herüber und führt Dich zurück auf einen
        sicheren Pfad.`n
        Die Sache ist ihm sichtlich unangenehm und zur Entschuldigung steckt er
        Dir ein `^paar Goldmünzen`0 zu.`0");
        $session['user']['gold']+=($session['user'][level]*10);
		$session['user']['specialinc'] = "";
        break;
        case 3:
        output("\"Sei vorsichtig, ich habe hier Fallen aufgestellt!\" ruft er Dir
        zu. Dankbar für diese Warnung beschliesst du, einen grossen Bogen um diese
        Gegend zu machen. `3Du büsst einen Waldkampf ein.`0");
        $session['user']['turns']--;
		$session['user']['specialinc'] = "";
        case 4:
        case 5:
        output("\"Schaut was ich habe\" ruft er Dir zu und eilt herüber. Er bietet
        Dir seine Funde an, verlangt aber etwas Gold dafür.`0");
        addnav("Kräuter für 100 Gold","forest.php?op=kraut");
        addnav("Pilze für 500 Gold","forest.php?op=pilz");
        addnav("Früchte für 1000 Gold","forest.php?op=beere");
        addnav("weitergehen","forest.php?op=cont");
        $session['user']['specialinc'] = "trapper.php";
        break;
        case 6:
        output("Sofort kommt er zu Dir und fragt Dich aus, ob Du seltene Tiere
        gesehen hast, wo und wann. Bereitwillg gibst Du Auskunft.`n
        Zum Dank für Deine Hilfe gibt Dir der Trapper `Qeinen Edelstein`0, den er
        irgendwo gefunden hat.`0");
        $session['user']['gems']++;
		$session['user']['specialinc'] = "";
        break;
    }
	
}
else if ($HTTP_GET_VARS['op']=="kraut"){
    if ( $session['user']['gold'] < 100 ) {
        output("`nAls der `3Trapper`0 mitbekommt, dass Du nicht genug Gold dabei hast,
        wird er wütend. Schnell steckt er die Kräuter wieder ein und schubst Dich
        ein Stück zurück. Du trittst nach hinten um nicht das Gleichgewicht zu
        verlieren - leider genau in eine Bärenfalle.`n`n
        `$ Du sitzt hier erstmal fest und kannst heute nicht mehr kämpfen!`0");
        $session['user']['turns']=0;
    }
    else {
        $was = e_rand (1,100);
        if ( $was < 25 ) {
            output("Du gibst dem `3Trapper`0 die 100 Gold und nimmst die Kräuter gleich
            in den Mund, um sie zu zerkauen.`n
            Eine Wirkung kannst Du jedoch nicht spüren.`n`n
            `9Der Trapper ist schon nicht mehr zu sehen, nur sein Lachen hörst Du noch...`0");
        }
        else {
            output("Nachdem Du die 100 Gold gegeben hast, zerkaust Du hastig die
            dargereichten Kräuter. Es kratzt etwas im Hals, aber Du spürst auch eine
            wohlige Wärme. `^Du regenerierst vollständig.`0");
            if ($session['user']['hitpoints'] < $session['user']['maxhitpoints'] )
                $session['user']['hitpoints'] = $session['user']['maxhitpoints'];
        }
        $session['user']['gold']-=100;
    }
    $session['user']['specialinc'] = "";
}
else if ($HTTP_GET_VARS['op']=="pilz"){
    if ( $session['user']['gold'] < 500 ) {
        output("`nAls der `3Trapper`0 mitbekommt, dass Du nicht genug Gold dabei hast,
        wird er wütend. Schnell steckt er die Pilze wieder ein und schubst Dich
        ein Stück zurück. Du trittst nach hinten um nicht das Gleichgewicht zu
        verlieren - leider genau in eine Bärenfalle.`n`n
        `$ Du sitzt hier erstmal fest und kannst heute nicht mehr kämpfen!`0");
        $session['user']['turns']=0;
    }
    else {
        $was = e_rand (1,100);
        if ( $was > 70 ) {
            output("Du gibst dem `3Trapper`0 die 500 Gold und nimmst die Pilze gleich
            in den Mund, um sie zu zerkauen.`n
            Eine Wirkung kannst Du jedoch nicht spüren.`n`n
            `9Der Trapper ist schon nicht mehr zu sehen, nur sein Lachen hörst Du noch...`0");
        }
        else {
            output("Nachdem Du die 500 Gold gegeben hast, zerkaust Du hastig die
            dargereichten Pilze. Es kratzt etwas im Hals, aber Du spürst auch eine
            wohlige Wärme. `^Deine Lebenspunkte verdoppeln sich.`0");
            $session['user']['hitpoints'] *= 2;
        }
        $session['user']['gold']-=500;
    }
    $session['user']['specialinc'] = "";
}
else if ($HTTP_GET_VARS['op']=="beere"){
    if ( $session['user']['gold'] < 1000 ) {
        output("`nAls der `3Trapper`0 mitbekommt, dass Du nicht genug Gold dabei hast,
        wird er wütend. Schnell steckt er die Beeren wieder ein und schubst Dich
        ein Stück zurück. Du trittst nach hinten um nicht das Gleichgewicht zu
        verlieren - leider genau in eine Bärenfalle.`n`n
        `$ Du sitzt hier erstmal fest und kannst heute nicht mehr kämpfen!`0");
        $session['user']['turns']=0;
    }
    else {
        $was = e_rand (1,100);
        if ( $was > 40 ) {
            output("Du gibst dem `3Trapper`0 die 1000 Gold und nimmst die Früchte gleich
            in den Mund, um sie zu zerkauen.`n
            Eine Wirkung kannst Du jedoch nicht spüren.`n`n
            `9Der Trapper ist schon nicht mehr zu sehen, nur sein Lachen hörst Du noch...`0");
        }
        else {
            output("Nachdem Du die 1000 Gold gegeben hast, zerkaust Du hastig die
            dargereichten Früchte. Es kratzt etwas im Hals, aber Du spürst auch eine
            wohlige Wärme. `^Du bekommst einen `bpermanenten`b Lebenspunkt!`0");
            $session['user'][maxhitpoints]++;
        }
        $session['user']['gold']-=1000;
    }
    $session['user']['specialinc'] = "";
}
else if ($HTTP_GET_VARS['op']=="cont"){   // einfach weitergehen
    output("`n`QDu lässt den Trapper stehen. Bestimmt eh ein Gauner...`0");
    $session['user']['specialinc']="";
}
?>