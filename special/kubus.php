<?
// idea of baldawin @ www.rabenthal.de
// programmed by gargamel @ www.rabenthal.de

if (!isset($session)) exit();


if ($HTTP_GET_VARS['op']==""){
    output("`nAls Du Deinen alltäglichen Rundgang im Wald machst, hörst Du in Deiner
    unmittelbaren Nähe plötzlich das Knacken eines zertretenen Zweiges. Als Du Dich
    bereit für das schlimmste schnell in die Richtung des Geräusches drehst,
    erwartet Dich eine angenehme Überraschung:`n`^"
    .($session['user']['sex']?"Ein wunderschöner junger Mann ":"Eine wunderschöne junge Frau ").
    "`0wirft Dir stumm ein verführerisches Lächeln zu...`n
    Einen Moment überlegst Du, was Du davon halten sollst... `n
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
    "zu und kurze Zeit später seid ihr beide auch schon im nächsten Gebüsch
    verschwunden...`n`n`0");
    $grenzwert = 70;
    if ($session['user']['charisma']==4294967295)
       $grenzwert = 40;

    $chance = e_rand(1,100);
    //output("chance $chance < grenzwert $grenzwert`n"); //debug
    if ( $chance < $grenzwert ) {     // positiv
        output("Als Du wieder Herr Deiner Sinne bist, ist "
        .($session['user']['sex']?"der mysteriöse Mann ":"die mysteriöse Frau ").
        "wie vom Erdboden verschluckt.`n
        \"Schade...\" denkst Du, ziehst Dich wieder an und setzt Deinen Weg fort.`n`n
        `^Du fühlst Dich grossartig, darum bekommst Du 2 Charmepunkte und regenerierst
        vollständig.`0");
        $session['user']['charm']+=2;
         if ($session['user']['hitpoints']<$session['user']['maxhitpoints'])
             $session['user']['hitpoints']=$session['user']['maxhitpoints'];
    } else {                  // negativ
        output("Während Eures rauschhaften Liebesspieles spürst Du eine Veränderung an "
        .($session['user']['sex']?"ihm":"ihr").
        "...Du schliesst Deine Augen...`n
        Als Du sie wieder öffnest, möchtest Du nicht wahrhaben was Du siehst:`n`n"
        .($session['user']['sex']?"Der Unbekannte ":"Die Unbekannte ").
        "starrt Dich mit einer hässlichen Fratze an und verfällt in wildes Kichern!
        Scheinbar bist Du einem "
        .($session['user']['sex']?"Inkubus ":"Sukkubus ").
        "auf den Leim gegangen! Nachdem Du das Liebesspiel überstanden hast,
        verkriecht sich der Dämon schallernd lachend im Wald, auf das ihm "
        .($session['user']['sex']?"der nächste unvorsichtige Abenteurer ":"die nächste unvorsichtige Abenteuerin ").
        "über den Weg läuft...`n`n
        `^Du fühlst Dich benutzt und ausgelaugt, daher verlierst Du 2 Charmepunkte
        und bist sehr schwach!`0");
        $session['user']['charm']-=2;
        $session['user']['hitpoints']=1;
    }
    $session['user']['specialinc']="";
}
else if ($HTTP_GET_VARS['op']=="weg"){   // einfach weitergehen
    output("`nEin wenig komisch fühlst Du Dich schon...daher verneigst Du Dich nur
    höflich und wünschst "
    .($session['user']['sex']?"dem Mann ":"der Frau ").
    "einen angenehmen Tag und gehst weiter deines Weges.`0");
    $session['user']['specialinc']="";
}
?>