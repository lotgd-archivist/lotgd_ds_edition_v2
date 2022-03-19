<?
// idea of gargamel @ www.rabenthal.de
if (!isset($session)) exit();

if ($HTTP_GET_VARS[op]==""){
    output("`nBeeindruckt schaust Du nach oben. Aus den gewaltigen Baumkronen
    hier h�ngen viele grosse `2Lianen`0 herunter. Sch�n und friedlich sieht es aus.`0");
    //abschluss intro
    addnav("Lianen ansehen","forest.php?op=look");
    addnav("klettern und schwingen","forest.php?op=use");
    addnav("weitergehen","forest.php?op=cont");
    $session[user][specialinc] = "liana.php";
}
else if ($HTTP_GET_VARS[op]=="look"){
    output("`nDu trittst n�her um Dir die `2Lianen`0 aus der N�he anzusehen. Du greifst
    Dir das Ende einer besonders h�bschen Liane, atmest den frischen Pflanzenduft
    ein und testest mal die Reissfestigkeit.`n`n`0");
    $chance = e_rand(1,100);
    if ( $chance < 30 ) {
        output("`2Pl�tzlich scheint die Liane lebendig zu werden.`0 Sie umschlingt Dich
        und auch die anderen Lianen scheinen sich pl�tzlich zu regen.`n
        `2Sie halten Dich fest!`0`n`n");
        switch ( e_rand (1,3) ) {
            case 1:
            output("Mit ganzer Kraft reisst Du Dich los und verschwindest.`0");
            break;
            case 2:
            output("Du bist geschockt und ben�tigst einen Moment, bevor Du Dich
            losreissen kannst.`n
            `@Du verlierst einen Waldkampf.`0");
            $session[user][turns]-=1;
            break;
            case 3:
            output("Ein eiskalter Schauer jagt �ber Deinen R�cken und Du bist
            geschockt. Du versuchst Dich loszureissen und erst mit letzter Kraft
            gelingt es Dir.`n
            `@Du verlierst einen Waldkampf und die H�lfte Deiner Lebenspunkte.`0");
            $session[user][turns]-=1;
            $session['user']['hitpoints'] = round ( $session['user']['hitpoints'] / 2 );
            break;
        }
    }
    else {
        output("Pl�tzlich schwingen die Lianen zur Seite, es schaut aus, also ob
        sich ein Vorhang �ffnet. `2Dadurch wird ein schmaler Pfad freigegeben, den
        Du mutig entlaggehst.`n`n`0");
        switch ( e_rand(1,3) ) {
            case 1:
            output("Der Pfad ist eine Abk�rzung in einen anderen Teil des Waldes.
            Du gewinnst Zeit und kannst deswegen heute `teinen zus�tzlichen Waldkampf`0
            absolvieren.`0");
            $session[user][turns]+=1;
            break;
            case 2:
            output("Der Pflanzenduft, den Du schon an der Liane gerochen hast, wird
            intensiver. Und er scheint eine positive Wirkung zu haben: `9Du regenerierst
            vollst�ndig.`0");
            if ($session['user']['hitpoints']<$session['user']['maxhitpoints'])
                $session[user][hitpoints]=$session[user][maxhitpoints];
            break;
            case 3:
            output("Du bist Dir recht sicher, dass hier lange niemend gegangen ist.
            Deshalb bist Du besonders wachsam und entdeckst so `^ein wenig Gold am
            Wegesrand.`0");
            $session[user][gold]+= 100;
            break;
        }
    }
    $session[user][specialinc]="";
}
else if ($HTTP_GET_VARS[op]=="use"){
    output("`nDu pr�fst mit einigen kr�ftigen Rucken die Festigkeit der `2Lianen`0. F�r
    Dich f�hlt sich alles fest und sicher an, und so startest Du eine Liane
    raufzuklettern.`n`n`0");
    switch ( e_rand(1,4) ) {
        case 1:
        output("Leider bis Du keine Lianen-Experte und so hast Du die Festigkeit
        v�llig falsch eingesch�tzt. `2Die Liane gibt nach und Du f�llst krachend zu
        Boden. Durch Deine Verletzung verlierst du viele Lebenspunkte.`0");
        $rest =  round ( $session['user']['hitpoints']*0.15 );
        if ( $rest == 0 ) $rest = 1;
        $session['user']['hitpoints'] = $rest;
        break;
        case 2:
        output("Dich packt sportlicher Ehrgeiz und Du probierst, von Liane zu Liane
        zu schwingen. Nach ein paar Versuchen gelingt es Dir. Nun kannst Du dieses
        Waldst�ck sehr z�gig durchqueren, `2dadurch gewinnst Du Zeit f�r einen zus�tzlichen
        Waldkampf.`0");
        $session[user][turns]+=1;
        break;
        case 3:
        output("Dich packt sportlicher Ehrgeiz und Du probierst, von Liane zu Liane
        zu schwingen. Nach ein paar Versuchen gelingt es Dir und mit kindlicher Freude
        vergn�gst Du Dich in luftiger H�he.`n
        Gl�cklich ziehst Du weiter.`0");
        break;
        case 4:
        $exp = round ( $session[user][experience]*0.05 );
        output("M�helos kletterst Du an der Liane in die Baumkrone hinauf. Du hast
        von dort oben einen wunderbaren �berblick �ber den Wald. Du pr�gst Dir den
        Verlauf einiger Wege ein und kannst das Wissen f�r Dich nutzen.`n
        `2Du erh�lst $exp Erfahrungspunkte.`0");
        $session[user][experience]+= $exp;
        break;
    }
    $session[user][specialinc]="";
}
else if ($HTTP_GET_VARS[op]=="cont"){   // einfach weitergehen
    output("`n`@Du verl�sst dieses Waldst�ck, die Lianen sich Dir nicht geheuer.`0");
    $session[user][specialinc]="";
}
?>