<?php
// Terronville - last modified 2004.05.21, Craig Stimme
// Modifycations by Hadriel @ hadrielnet.ch
if (!isset($session)) exit();

// tname allows Terronville to become Craugville or whatever :)
$tname = "Lichtung der Schn�ppchen";
$tname2 = "Carmino";

if ($HTTP_GET_VARS[op]==""){
    output("`8Als du nichts B�ses ahnend durch den Wald wanderst, kommst du auf einmal auf einer Lichtung an, ");
    output("auf der ein Zelt aufgebaut wurde. Daneben wurde ein ziemlich schlecht gezimmerts Schild ");
    output("in den Boden gerannt, auf dem in krakeligen Buchstaben `q\"$tname\"`8 zu lesen ist. ");
    switch(e_rand(1,2)){
        case 1:
            output("`q$tname2 `8 steht von seinem Campingstuhl auf, den er vor dem Zelt platziert hat, ");
            output("und sagt, \"`qEy du, ya? Willst du nicht einen Nestaffen kaufen? Nur 50 ");
            output("Goldst�cke!`8\" ");
            addnav("Kaufe einen Nestaffen", "forest.php?op=buymonkey");
            addnav("Kaufe keinen Nestaffen", "forest.php?op=nomonkey");
            $session[user][specialinc]="terronville.php";
            break;
        case 2:
            output("`q$tname2 `8und ein verd�chtig aussehender junger Mann, der auf den Namen Chappu zu h�ren scheint,");
            output("erheben sich von ihren Campingst�hlen, die vor dem Zelt stehen. `q$tname2 ");
            output("`8spricht dich an, \"`qHallo, willkommen in den $tname, ya? Willst du nicht ");
            output("reinkommen?`8\" ");
            addnav("Betritt das Zelt", "forest.php?op=enter");
            addnav("Kehre in den Wald zur�ck", "forest.php?op=dont");
            $session[user][specialinc]="terronville.php";
            break;
    }
}else if ($HTTP_GET_VARS[op]=="buymonkey"){
    if ($session[user][gold]>=50){
        $session[user][gold]-=50;
        switch(e_rand(1,2)){
            case 1:
                output("`q$tname2 `8holt den Nestaffen aus dem K�fig und ");
                output("gibt ihn dir. Der Nestaffe bleibt aber nicht lange auf deiner Hand sitzen, ");
                output("sondern springt auf deien Schulter, wo er erstaunlich ruhig sitzen bleibt. Du hast ");
                output("`q die $tname `8gerade erst hinter dir gelassen, da f�ngt das Tier ");
                output("laut an zu kreischen und verschwindet im Wald. ");
                output("Etwas ver�rgert kehrst du zur�ck, um `q$tname2 ");
                output("`8zur Rede zu stellen, aber `q$tname2 `8besitzt doch tats�chlich die Frechheit zu behaupten, ");
                output("er h�tte dir niemals einen Nestaffen verkauft.`n`n `8Deine 50 Goldst�cke sind verloren!");
                break;
            case 2:
                output("`8Du nimmst dir die Zeit, um ein wenig mit dem Nestaffen zu spielen. ");
                output("Besonders intelligent stellt sich das kleine Vieh aber nicht an. Es rennt mehrmals beim Aportieren gegen einen Baum. ");
                output("Du fragst dich, wie dieses Tier im Wald �berleben kann. Richtig, der Nestaffe ist ein Waldtier. Irgendwie �berkommt dich ein schlechtes Gewissen ");
                output("und du l�sst ihn frei. Hier ist er sicher besser aufgehoben und wenn nicht... dann schont es zumindest deine Nerven. ");
                switch(e_rand(1,2)){
                    case 1:
                        output("`8Der Nestaffe schaut dich einen Moment vollkommen irritiert an, dann scheint ");
                        output("er aber doch verstanden zu haben, dass du ihn gehen l�sst und klettert auf einen Baum. ");
                        output("Beim Herumtollen springt er mit dem Kopf gegen einen Ast, so fest dass der ganze Baum wackelt. ");
                        output("Er f�llt herunter und will gerade wieder aufspringen, als etwas auf seinen Kopf f�llt und ihn k.o. schl�gt.`n`nNeben ");
                        output("dem bewusstlosen Nestaffen findest du 1 Edelstein!");
                        $session[user][gems]+=1;
                        break;
                    case 2:
                        output("`8Die Zeit, die du f�r den Nestaffen geopfert hast, ");
                        output("und das gute Gef�hl, etwas richtiges getan zu haben, ");
                        output("erf�llen dich mit Kampflust.`n`nDu erh�ltst ");
                        output("1 Waldkampf!");
                        $session[user][turns]+=1;
                        break;
                }
        }
    }else{
        output("`8\"`qHey, willst du mich veralbern? So geht das aber nicht!`8\" ");
        output("`q$tname2`8 zieht an einem Seil und pl�tzlich �ffnen sich alle Nestaffenk�fige gleichzeitig. Die kleinen Biester ");
        output("springen dich an, ziehen an deine Haaren und kratzen dich mit ihren kleinen, aber schmerzhaften Krallen. ");
        output("W�hrend du versuchst, sie abzusch�tteln und dich dabei selbst zum Affen machst, verlierst du das Gleichgewicht und f�llst hin.`n`n");
        output("Du verlierst 1 Waldkampf! ");
        $session[user][turns]--;
    }
}else if ($HTTP_GET_VARS[op]=="nomonkey"){
    output("`8Was in aller Welt sollst du mit einem Nestaffen anfangen? Die Viecher sind klein, nervig und d�mmer als das Brot in der Mensa. ");
    output("Mit einem h�flichen L�cheln, lehnst du `q$tname2`q's `8 Angebot ab und kehrst in den Wald zur�ck. ");
}else if ($HTTP_GET_VARS[op]=="enter"){
    output("`q$tname2 `8h�lt dir die Zeltt�r auf und bittet dich mit einer freundlichen Geste herein. ");
    output("Gem�� jeder H�flichkeitsregel darfst du zuerst eintreten. ");
    switch(e_rand(1,2)){
        case 1:
            output("`8Du hast das Zelt gerade betreten, da f�llt dein Blick auf seltsame S�cke, die auf dem Boden verteilt sind. ");
            output("Pl�tzlich f�llt dir auf, dass es eine komplette Blitzballmannschaft sein muss. Bewusstlos! ");
            output("Schnell drehst du dich zu `q$tname2`8 um, bereit deine Waffe gegen ihn einzusetzen. ");
            output("`q$tname2 `8hebt beide H�nde: \"`qGanz ruhig, ja? ");
            output("Ich werde dir eine Geschichte erz�hlen. ");
            output("Aus meiner Vergangenheit.`8\" ");
            output("`8Dir f�llt auf, dass die Leute gar nicht bewusstlos, sondern eingeschlafen sind, aber dir f�llt leider keiner Ausrede ein, mit der du verschwinden k�nntest. Es bleibt dir nichts weiter �brig, als dir die Geschichte anzuh�ren.`n`n");
            output("`8Als wieder aufwachst, merkst du, dass du trotz allem etwas gelernt hat. Du erh�ltst Erfahrungspunkte!");
            $session[user][experience] *= 1.1;
            break;
        case 2:
            $stolen = e_rand(0,$session[user][gold]);
            output("`8Du hast das Zelt gerade betreten, da f�llt dein Blick auf seltsame S�cke, die auf dem Boden verteilt sind. ");
            output("Pl�tzlich f�llt dir auf, dass es eine komplette Blitzballmannschaft sein muss. Bewusstlos! ");
            output(" Du willst dich gerade zu `q$tname2 `8umdrehen, aber `qKLONG`8...`n`n");
            output("`8Als du wieder aufwachst, merkt du, dass man dich um $stolen Gold ");
            output("erleichert hat!");
            $session[user][gold]-=$stolen;
    }
}else if ($HTTP_GET_VARS[op]=="dont"){
    $stolen = e_rand(0,$session[user][gold]);
    output("`8Deine Mama hat dir beigebracht, dass man nicht mit Fremden gehen soll. ");
    output("Also lehnst du ab und kehrst in den Wald zur�ck, als pl�tzlich `qKLONG `8etwas ");
    output("mit deinem Kopf kollidiert...`n`n");
    output("`8Als du wieder aufwachst, sind sowohl das Zelt, als auch `%$tname2 ");
    output("`8und das h�bsche Schild verschwunden. Du untersuchst deine Taschen und musst feststellen, dass dir ");
    output("$stolen Gold gestohlen wurde.");
    $session[user][gold]-=$stolen;
}else{
    output("`\$Nach so einem seltsamen Erlebnis bist du froh, als du wieder in den dir vertrauten Wald kommst. ");
}
?>