<?
// idea of baldawin @ www.rabenthal.de
// programmed by gargamel @ www.rabenthal.de

if (!isset($session)) exit();


if ($HTTP_GET_VARS['op']==""){
    output("`nAls Du Deinen allt�glichen Rundgang im Wald machst, h�rst Du in Deiner
    unmittelbaren N�he pl�tzlich das Knacken eines zertretenen Zweiges. Als Du Dich
    bereit f�r das schlimmste schnell in die Richtung des Ger�usches drehst,
    erwartet Dich eine angenehme �berraschung:`n`^"
    .($session['user']['sex']?"Ein wundersch�ner junger Mann ":"Eine wundersch�ne junge Frau ").
    "`0wirft Dir stumm ein verf�hrerisches L�cheln zu...`n
    Einen Moment �berlegst Du, was Du davon halten sollst... `n
    Du hast "
    .($session['user']['sex']?"diesen Mann ":"diese Frau ").
    "noch nie gesehen und kennst nichtmal "
    .($session['user']['sex']?"seinen ":"ihren ").
    "Namen...`0");
    //abschluss intro
    if ($session['user']['sex']>0){ //frau
        addnav("Gebe Dich ihm hin","forest.php?op=hin");
        addnav("Lass ihn stehen","forest.php?op=weg");
    } else {
        addnav("Gebe Dich ihr hin","forest.php?op=hin");
        addnav("Lass sie stehen","forest.php?op=weg");
    }
    $session['user']['specialinc'] = "kubus.php";
}
else if ($HTTP_GET_VARS['op']=="hin"){
    output("`nDu zwinkerst ".($session['user']['sex']?"dem Fremden ":"der Fremden ").
    "zu und kurze Zeit sp�ter seid ihr beide auch schon im n�chsten Geb�sch
    verschwunden...`n`n`0");
    $grenzwert = 70;
    if ($session['user']['charisma']==4294967295)
       $grenzwert = 40;

    $chance = e_rand(1,100);
    //output("chance $chance < grenzwert $grenzwert`n"); //debug
    if ( $chance < $grenzwert ) {     // positiv
        output("Als Du wieder Herr Deiner Sinne bist, ist "
        .($session['user']['sex']?"der mysteri�se Mann ":"die mysteri�se Frau ").
        "wie vom Erdboden verschluckt.`n
        \"Schade...\" denkst Du, ziehst Dich wieder an und setzt Deinen Weg fort.`n`n
        `^Du f�hlst Dich grossartig, darum bekommst Du 2 Charmepunkte und regenerierst
        vollst�ndig.`0");
        $session['user']['charm']+=2;
         if ($session['user']['hitpoints']<$session['user']['maxhitpoints'])
             $session['user']['hitpoints']=$session['user']['maxhitpoints'];
    } else {                  // negativ
        output("W�hrend Eures rauschhaften Liebesspieles sp�rst Du eine Ver�nderung an "
        .($session['user']['sex']?"ihm":"ihr").
        "...Du schliesst Deine Augen...`n
        Als Du sie wieder �ffnest, m�chtest Du nicht wahrhaben was Du siehst:`n`n"
        .($session['user']['sex']?"Der Unbekannte ":"Die Unbekannte ").
        "starrt Dich mit einer h�sslichen Fratze an und verf�llt in wildes Kichern!
        Scheinbar bist Du einem "
        .($session['user']['sex']?"Inkubus ":"Sukkubus ").
        "auf den Leim gegangen! Nachdem Du das Liebesspiel �berstanden hast,
        verkriecht sich der D�mon schallernd lachend im Wald, auf das ihm "
        .($session['user']['sex']?"der n�chste unvorsichtige Abenteurer ":"die n�chste unvorsichtige Abenteuerin ").
        "�ber den Weg l�uft...`n`n
        `^Du f�hlst Dich benutzt und ausgelaugt, daher verlierst Du 2 Charmepunkte
        und bist sehr schwach!`0");
        $session['user']['charm']-=2;
        $session['user']['hitpoints']=1;
    }
    $session['user']['specialinc']="";
}
else if ($HTTP_GET_VARS['op']=="weg"){   // einfach weitergehen
    output("`nEin wenig komisch f�hlst Du Dich schon...daher verneigst Du Dich nur
    h�flich und w�nschst "
    .($session['user']['sex']?"dem Mann ":"der Frau ").
    "einen angenehmen Tag und gehst weiter deines Weges.`0");
    $session['user']['specialinc']="";
}
?>