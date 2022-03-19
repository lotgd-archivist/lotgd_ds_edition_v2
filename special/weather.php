<?
// idea of gargamel @ www.rabenthal.de
if (!isset($session)) exit();

if ($HTTP_GET_VARS[op]==""){
	$w = get_weather();
    output("`nSag mal, ".$session[user][name].", hast Du eigentlich heute schon zum
    Himmel geschaut? Das Wetter ist \"`^".$w['name']."`0\" !!`n`0");

    if ( $settings['weather'] == WEATHER_COLD ) {
        output("\"K�nnte besser sein\" denkst Du Dir und gehst weiter.`0");
    }
    else if ( $settings['weather']==WEATHER_WARM ) {
        output("Du bist hier ganz in der N�he von einem kleinen Waldsee. Und so
        wundert es nicht, dass bei diesem Wetter eine ware M�ckenplage herrscht.`n`0");
        $case = e_rand(1,2);
        switch ( $case ) {
            case 1:
            output("Du musste die Plagegeister st�ndig wegscheuchen, was Dich etwas
            Aufmerksamkeit im n�chsten Kampf kostet. `n`^Deine Verteidigung wird schw�cher.`n`0");
            $session[bufflist]['muecken'] = array("name"=>"`4M�cken",
                                        "rounds"=>10,
                                        "wearoff"=>"Die M�cken haben sich verzogen.",
                                        "defmod"=>0.92,
                                        "atkmod"=>1,
                                        "roundmsg"=>"Die M�cken behindern Dich.",
                                        "activate"=>"defense");
            break;

            case 2:
            output("Bei dem st�ndigen Geschwirre kannst Du Dich kaum auf den n�chsten
            Kampf konzentrieren. `n`^Deine Angriffsf�higkeit ist daher eingeschr�nkt.`0");
            $session[bufflist]['muecken'] = array("name"=>"`4M�cken",
                                        "rounds"=>10,
                                        "wearoff"=>"Die M�cken haben sich verzogen.",
                                        "defmod"=>1,
                                        "atkmod"=>0.92,
                                        "roundmsg"=>"Die M�cken behindern Dich.",
                                        "activate"=>"offense");
            break;
        }
    }
    else if ( $settings['weather']==WEATHER_RAINY ) {
        if ( $session['user']['specialty'] == 1 ) {
            output("Als Du nun bei dem miesen Wetter durch den Wald stapfst, wird
            Deine Stimmung nochmal schlechter.`n
            Deinen F�higkeiten tut dies jedoch gut und `^Du steigst eine Stufe auf.`0");
            increment_specialty();
        } else {
            output("Als nun ein weiteres Schauer niedergeht, ziehst Du Dir erstmal
            schnell Deinen Regenschutz �ber.`n
            `^Leider behindert er Dich etwas beim k�mpfen...`0");
            $session[bufflist]['regenjacke'] = array("name"=>"`4Regenschutz",
                                        "rounds"=>25,
                                        "wearoff"=>"Gut! Der Regenschauer ist vorbei.",
                                        "defmod"=>0.96,
                                        "atkmod"=>0.92,
                                        "roundmsg"=>"Der Regenschutz behindert Dich.",
                                        "activate"=>"defense");
        }
    }
    else if ( $settings['weather']==WEATHER_FOGGY ) {
        if ( $session['user']['specialty'] == 3 ) {
            output("Das kommt Dir mit Deinen Diebesf�higkeiten nat�rlich entgegen.
            `^Du erh�lst einen zus�tzlichen Waldkampf!`0");
            $session['user']['turns']++;
        } else {
            output("Da ist es noch schwieriger, sich im Wald zurechtzufinden. Und
            prompt nimmst Du einen falschen Abzweig vom Waldweg.`n
            `^Du verlierst einen Waldkampf.`0");
            $session['user']['turns']--;
        }
    }
    else if ( $settings['weather']==WEATHER_COLDCLEAR ) {
       output("Meinst Du wirklich, ".$session[user][armor]." ist da die richtige
        Kleidung?`n`0");
        $case = e_rand(1,2);
        switch ( $case ) {
            case 1:
            output("`^Du handelst Dir einen Schnupfen ein und verlierst ein paar
            Lebenspunkte.`0");
            $session[user][hitpoints]=round($session[user][hitpoints]*0.95);
            break;
             
            case 2:
            output("Du sammelst etwas Reisig im Unterholz und w�rmst Dich erstmal
            an einem kleinen Feuerchen.`n
            `^Die Pause kostet Dich einen Waldkampf.`0");
            $session['user']['turns']--;
        }
    }
    else if ( $settings['weather']==WEATHER_HOT ) {
        output("Im Dorf hast Du es sogar als schw�l empfunden und geniesst daher
        die Zeit im schattigen, k�hlen Wald.`n
        `^Du bekommst einen Waldkampf.`0");
        $session['user']['turns']++;
    }
    else if ( $settings['weather']==WEATHER_WINDY ) {
        output("Die gro�en alten B�ume hier biegen sich unter der Wucht einzelner
        Windb�en. Ein gro�er Ast kann dem Wind nicht mehr standhalten und kracht zu
        Boden.`0");
        $case = e_rand(1,2);
        switch ( $case ) {
            case 1:
            output("Du hast mehr Gl�ck als Verstand! Der m�chtige Ast schl�gt nur
            wenige Schritte von Dir entfernt auf. Dir ist nichts passiert.`n
            `^Etwas eingesch�chtert gehst Du weiter.`0");
            break;
             
            case 2:
            output("Zum Gl�ck schl�gt der Ast neben Dir ein, aber ein paar kleinere
            �ste treffen Dich doch. `^Du b�sst Lebenspunkte ein!`0");
            $hp = e_rand(1,$session[user][hitpoints]);
            $session[user][hitpoints]=$hp;
            break;
        }
    }
    else if ( $settings['weather']==WEATHER_TSTORM ) {
        if ( $session['user']['specialty'] == 2 ) {
            output("Um Dich herum zucken die Blitze durch den verdunkelten Himmel.
            Genau richtig, um die magischen Kr�fte aufzuladen.`n
            `^Du kannst Deine F�higkeiten wieder einsetzen.`0");
            //-> f�higkeiten aktivieren
            restore_specialty();
        } else {
            output("Gerade im Wald ist das nicht ungef�hrlich!`n`n
            Um Dich vor Blitzschlag zu sch�tzen, stellst Du Dich in einer H�hle
            unter.`n
            `^Du verlierst einen Waldkampf.`0");
            $session['user']['turns']--;
        }
    }
}
?>