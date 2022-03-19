<?
// idea of gargamel @ www.rabenthal.de
if (!isset($session)) exit();

    $jetzt = getgametime();
    if ( $jetzt < 4 ) {
        output("`nDer Mond ist von Wolken verdeckt, kein Lichtstrahl erhellt den
        Wald. Und Du selbst hast ja auch keine Lampe dabei, mitten in der Nacht.
        Leichtsinnig....`n`n
        Schon trittst Du auf einen am Boden liegenden Ast, den Du übersehen hast.
        Unglücklicherweise rutscht Du auf dem moosbewachsenen Ast aus und fällst
        auf Deinen Hosenboden.`n`n`0");
        $bis = 2;
        if ( $session[user][gold] > 100 ) $bis = 3;
        $was = e_rand(1,$bis);
        switch ( $was ){
            case 1:
            output("Das musste nicht sein... Traurig klopfst Du Dir den Dreck von Deiner
            Kleidung. Ist da nicht auch etwas eingerissen?`n
            `3Du verlierst einen Charmepunkt.`0");
            $session[user][charm]-=1;
            break;
            case 2:
            output("Beim Aufstehen spürst Du einen leichten Schmerz im Fuss. Wahrscheinlich
            hast Du Dir Deinen Knöchel leicht verstaucht.`n
            `2Du verlierst ein paar Lebenspunkte.`0");
            $session[user][hitpoints] =round ($session[user][hitpoints]*0.97);
            break;
            case 3:
            output("Ein wenig später bemerkst du, dass Dir bei dem Sturz etwas Gold
            aus der Tasche gefallen sein muss.`n
            `$ In der Dunkelheit kannst Du es nicht wiederfinden.`0");
            $session[user][gold]= round ($session[user][gold]*0.90);
            break;
        }
    }
    else if ( $jetzt < 8 ) {
        output("`nAuf Deinem Weg durch den Wald störst Du die Feen, die zu so früher
        Stunde den Tau auflesen.");
        $spec = $session['user']['specialty'];
        switch ( $spec ) {
            case 1:
            if ( $session[user][specialtyuses][darkartuses] > 0 ) {
                output("`nDie Feen rächen sich für die Störung und nehmen Dir eine
                Anwendung Deiner dunklen Künste für heute weg.`0");
                $session[user][specialtyuses][darkartuses]--;
            } else {
                output("`nDu entschuldigst Dich wortreich, die Feen verzeihen Dir
                und Du kannst weiterziehen.`0");
            }
            break;
            case 2:
            if ( $session['user']['specialtyuses'][magicuses] > 0 ) {
                output("`nDie Feen rächen sich für die Störung und nehmen Dir eine
                Anwendung Deiner magischen Kräfte für heute weg.`0");
                $session['user']['specialtyuses'][magicuses]--;
            } else {
                output("`nDu entschuldigst Dich wortreich, die Feen verzeihen Dir
                und Du kannst weiterziehen.`0");
            }
            break;
            case 3:
            if ( $session[user][specialtyuses][thieveryuses] > 0 ) {
                output("`nDie Feen rächen sich für die Störung und nehmen Dir eine
                Anwendung Deiner Diebesfähigkeit für heute weg.`0");
                $session[user][specialtyuses][thieveryuses]--;
            } else {
                output("`nDu entschuldigst Dich wortreich, die Feen verzeihen Dir
                und Du kannst weiterziehen.`0");
            }
            break;
        }
    }
    else if ( $jetzt < 15 ) {
        output("`nDu bemerkst, dass auch andere Bewohner den Tag im Wald verbringen.`n`n`0");
        $was = e_rand(1,3);
        switch ( $was ) {
            case 1:
            output("Du triffst einen Förster und fragst ihn, ob er auf seinem Weg einigen
            Gegnern für Dich begnet ist.`n
            Du hast Glück und der Förster weist Dir die Richtung. `^Dadurch gewinnst Du
            einen Waldkampf!`0");
            $session[user][turns]++;
            break;
            case 2:
            output("Du triffst auf eine Gruppe Schulkinder, die Dich schnell umringen.
            Sie strahlen Dich mit ihren grossen Augen an und Dir wird warm ums Herz.`n
            Gerne kommst Du ihrer Bitte nach, gemeinsam mit ihnen ein Lied zu singen.
            \"Alle Vögel sind schon da....\"`n`n
            `QDu vertrödelst einen Waldkampf, aber Du hast Kinderherzen glücklich gemacht.`0");
            $session[user][turns]--;
            break;
            case 3:
            output("Du triffst einen Heiler, der offensichtlich aus einem anderen Dorf
            stammt und hier einige seltene Pflanzen sucht.`n`0");
            if ( $session[user][hitpoints] < $session[user][maxhitpoints] ) {
                if ( $session[user][gold] > 10 ) {
                    output("`^Für nur einen Zehnten Deines Goldes heilt er Dich.`0");
                    $session[user][hitpoints]=$session[user][maxhitpoints];
                    $session[user][gold]=round($session[user][gold]*0.9);
                }
                else {
                    output("`9Aber da Du nur wenig Gold bei Dir hast, kann er nichts für
                    Dich tun.`0");
                }
            }
            else {
                output("`9Aber da Du Gesund bist, kann er nichts für Dich tun.`0");
            }
            break;
        }
    }
    else if ( $jetzt < 21 ) {
        output("`nEs ist schon Abend geworden und Du rastest ein wenig, um Dich auszuruhen
        und über den Tag nachzudenken.`n`n`0");
        $was = e_rand(1,3);
        switch ( $was ) {
            case 1:
            case 2:
            output("Die Pause hat Dir gut getan, Du erhälst einige Lebenspunkte.`0");
            if ( $session[user][hitpoints] < 20 ) {
                $session[user][hitpoints]+= 5;
            }
            else {
                $session[user][hitpoints]*=1.1;
            }
            break;
            case 3:
            output("Dabei versinkst Du immer mehr ins Reich der Träume und verschläfst
            einen Waldkampf.`0");
            $session[user][turns]--;
            break;
        }
    }
    else {
        output("`nEin Schwarm Glühwürmchen findet Dich. In ihrem Licht kannst Du auch
        zu später Stunde gut sehen.`n
        `^Du bekommst einen Waldkampf hinzu.`0");
        $session[user][turns]++;
    }
?>
