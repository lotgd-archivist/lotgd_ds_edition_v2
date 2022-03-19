<?
// idea of gargamel @ www.rabenthal.de
if (!isset($session)) exit();

if ($HTTP_GET_VARS[op]==""){
    output("`nVor Dir erstreckt sich ein Gebiet, übersät mit Felsen. Etwas entfernt
    ist sogar ein kleiner Berg. Das Gebiet zu umgehen wird etwas dauern, aber über
    die Felsen zu steigen ist bestimmt nicht ungefährlich...`0");
    //abschluss intro
    addnav("Gebiet umgehen","forest.php?op=umgehen");
    addnav("Felsen erklimmen","forest.php?op=fels");
    $session[user][specialinc] = "cliff.php";
}
else if ($HTTP_GET_VARS[op]=="umgehen"){
    output("`nDu umgehst das Gebiet und verlierst dabei `#einen Waldkampf.`0");
    $session[user][turns]--;
    $session[user][specialinc] = "";
}
else if ($HTTP_GET_VARS[op]=="fels"){
    output("`nDu machst Dich auf, den Weg über die Felsen zu nehmen. Anfangs noch
    ein wenig unsicher, aber dann recht schnell und mit einer bemerkenswerten Sicherheit
    balancierst Du über die Felsen.`n`0");
    switch(e_rand(1,5)){
        case 1:
        output("Dann passiert es: Du trittst auf loses Geröll und stürzt schwer in
        eine Felsspalte. Mit allerletzter Kraft kannst Du Dich daraus befreien.`n`n
        `%Du hast fast alle Lebenspunkte verloren und solltest dringend einen Heiler
        aufsuchen!`0");
        $session[user][hitpoints]=1;
        break;
        case 2:
        output("Du stutzt, als Du plötzlich etwas glitzern siehst. Voller Freude
        steckst Du `Qeinen Edelstein`0 ein.`0");
        $session[user][gems]++;
        break;
        case 3:
        if ( $session[user][hashorse] > 0 && $session[bufflist][mount][rounds] > 1 ){
            output("Auf dem unebenen Gelände kommt Dein Tier ins straucheln und
            rutscht weg. `0");
            output(" `%Es hat sich dabei verletzt und muss bei Merrick gepflegt werden!`0");
            $session[bufflist][mount][rounds]=1;
        }
        else {
            output("Dann passiert es: Du trittst auf loses Geröll und stürzt schwer
            in eine Felsspalte. Mit allerletzter Kraft kannst Du Dich daraus befreien.`n`n
            `%Du hast fast alle Lebenspunkte verloren und solltest dringend einen Heiler
            aufsuchen!`0");
            $session[user][hitpoints]=1;
        }
        break;
        case 4:
        output("Du findest einen einzeln stehenden Strauch, der grosse `$ rote`0 Früchte
        trägt. Neugierig pflückst du Dir eine Frucht und isst diese.`n`0");
        $was = e_rand(1,100);
        if ( $was < 33 ) {
            output("Die `$ Frucht`0 schmeckt bitter und es zieht Dir den Magen zusammen.`n
            `%Du verlierst Lebenspunkte.`0");
            $session[user][hitpoints]=round( $session[user][hitpoints]*0.5 );
        }
        else {
            output("Die `$ Frucht`0 schmeckt sehr süss und Du spürst, wie Deine Lebensgeister
            neu erwachen.`n
            `9Du gewinnst Lebenspunkte hinzu.`0");
            $session[user][hitpoints]*=2;
        }
        break;
        case 5:
        output("Ohne grössere Probleme meisterst Du diesen felsigen Abschnitt und
        setzt Deinen Weg fort.`0");
        break;
    }
    $session[user][specialinc]="";
}
?>