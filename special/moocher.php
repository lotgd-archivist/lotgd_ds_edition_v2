<?
// idea of gargamel @ www.rabenthal.de
if (!isset($session)) exit();

if ($HTTP_GET_VARS[op]==""){
    output("`nDu folgst dem Waldweg und bist besonders wachsam, weil Du in einem
    düsteren Abschnitt des Waldes gelandet bist. Dann passiert es:`n
    Hinter einer Wegbiegung wirst Du plötzlich von Dieben umringt!`n`n
    `%Die Typen sehen wirklich furchterregend aus und fordern mit gezogenen Waffen
    Dein Gold.`0`nBleibt Dir eine andere Wahl?`0");
    //abschluss intro
    addnav("Gold herausgeben","forest.php?op=give");
    addnav("Kämpfen","forest.php?op=fight");
    
    $session[user][specialinc] = "moocher.php";
}
else if ($HTTP_GET_VARS[op]=="give"){   // Gold geben
    $gold = $session[user][gold];
    if ( $session[user][gold] > 0 ) {
        output("`nAngesichts der Übermacht der Diebe entschließt Du Dich, Dein Gold
        herauszugeben.`nDich schmerzt der `QVerlust von $gold Gold`0, aber noch schlimmer
        wäre der Verlust Deines Lebens gewesen.`0");
        $session[user][gold]=0;
    }
    else {  // aber nix dabei
        output("`nDu erklärst dem Anführer, dass Du zahlen willst.`nAls Du ihm jedoch
        Deine leere Geldbörse hinhälst, findet er das gar nicht komisch.`n`QEr gibt seiner
        wilden Truppe ein Zeichen.... `0Die ganze Meute prügelt nun auf Dich ein und sie
        lassen erst von Dir ab, als Du schon tot scheinst.`n`n
        `9Du bist aber gerade noch mit dem Leben davon gekommen und verlierst einen
        permanenten Lebenspunkt.`0");
        $session[user][maxhitpoints]-=1;
        $session[user][hitpoints]=1;
    }
    $session[user][specialinc] = "";
}
else if ($HTTP_GET_VARS[op]=="fight"){   // kämpfen
    output("`n`%Du entschließt Dich zu kämpfen und ziehst blitzschnell Deine Waffe.`n`n`0");
    $hp = $session[user][hitpoints] * 2;
    $dam = e_rand(1,$hp);
    if ( $session[user][hitpoints] > $dam ) { //sieg
        output("In einem unübersichtlichen Getümmel wirst Du hart getroffen, aber Du
        führst Deine Waffe auch erfolgreich. Nach einer ganzen Weile steht fest:`n`n
        `@Du hast gewonnen!`n`n
        Schwer gezeichnet feierst Du Deinen letzten Sieg des heutigen Tages. Wenigstens
        hast Du einiges an Erfahrung gewonnen.`0");
        $session[user][hitpoints]-= $dam;
        $session[user][turns]=0;
        $session[user][experience]+= round($session[user][experience]*0.08);
    }
    else {  //niederlage
        output("Die Entscheidung, gegen die Übermacht der Diebe zu kämpfen, war sicher
        nicht Deine Beste! `QDu hast einfach keine Chance!`n`n
        `6Nach einem kurzen, heftigen Kampf verabschiedest du Dich vom Leben.`n`n
        Für Deinen Mut wird Dich jedoch Ramius belohnen.`0");
        $session[user][alive]=false;
        $session[user][gold]=0;
        $session[user][hitpoints]=0;
        $session[user][gravefights]+=2;
        addnews("`^".$session[user][name]."`# hatte keine Chance im Kampf gegen die Diebesbande. Auf Wiedersehen!");
        addnav("Tägliche News","news.php");
    }
    $session[user][specialinc] = "";
}
else if ($HTTP_GET_VARS[op]=="help"){   // hilfe
    $needed = 6;  // im wald benötigte helfer
    output("Du rufst um Hilfe. Ganz laut.`n
    Die Diebensbande ist erstaunt, greift Dich aber nicht an. Offensichtlich wollen sie
    sich einwenig an Deiner Angst weiden. Und sie sind sicher, dass Dir eh niemand hilft.`n`n`0");

    $sql = "SELECT name,level,title,locate FROM accounts WHERE locked=0 AND locate=5 AND loggedin=1 AND laston>'".date("Y-m-d H:i:s",strtotime("-".getsetting("LOGINTIMEOUT",900)." seconds"))."' ORDER BY level DESC";
    $result = db_query($sql) or die(sql_error($sql));
    $max = db_num_rows($result);
    $anz = db_num_rows($result)-1; // -1 weil du selbst im wald bist!
     
    if ( $anz >= $needed ) {  // genug helfer da
        output("`6Aber die Diebe haben nicht mit Deinen Freunden aus dem Dorf gerechnet!
        Die folgenden Bewohner von Rabenthal sind nämlich gerade im Wald und eilen Dir
        zu Hilfe:`n`0");
        for($i=0;$i<$max;$i++){
            $row = db_fetch_assoc($result);
            if ( $row[name] != $session[user][name] )
                output("$row[tile] $row[name]`n");
        }
        output("`n`2Gemeinsam besiegt ihr die Diebesbande. `0Du bedankst Dich bei allen
        Helfern und versprichst, in der Taverne eine Runde zu schmeissen.`n
        Du verlierst zwar einen Waldkampf, ziehst aber trotzdem glücklich weiter.`0");
        $session[user][turns]-= 1;
    }
    else if ( $anz = 0 ) {
        output("`3Leider behalten die Diebe recht, denn im Moment sind keine anderen
        Bewohner im Wald. Sie schauen noch einen Moment zu, wie Du verzweifelt auf
        Hilfe wartest und greifen Dich dann an.`n`n
        `QNach einem kurzen, heftigen Kampf verabschiedest du Dich vom Leben.`0");
        $session[user][alive]=false;
        $session[user][gold]=0;
        $session[user][gems]=0;
        $session[user][hitpoints]=0;
        addnews("`^".$session[user][name]."`# hatte keine Chance im Kampf gegen die Diebesbande. Auf Wiedersehen!");
        addnav("Tägliche News","news.php");
    }
    else {  // nicht genug bewohner
        if ( $anz = 1 ) {
            output("`#Zwar werden Deine Hilferufe gehört. Aber der einzige Bewohner, der
            zur Zeit auch noch im Wald ist, erreicht Dich nicht rechtzeitig.`n`0");
        }
        else {
            output("`#Deine Hilferufe werden zwar von $max Bewohnern gehört, die auch
            gerade im Wald sind, aber unglücklicherweise können die den Ort des
            Überfalls nicht rechtzeitig erreichen. Du bleibst auf Dich allein gestellt.`n`0");
        }
        output("Damit behalten sie Diebe leider recht. Sie schauen noch einen Moment
        zu, wie Du verzweifelt auf Hilfe wartest und greifen Dich dann an.`n`n
        `QNach einem kurzen, heftigen Kampf verabschiedest du Dich vom Leben.`0");
        $session[user][alive]=false;
        $session[user][gold]=0;
        $session[user][gems]=0;
        $session[user][hitpoints]=0;
        addnews("`^".$session[user][name]."`# hatte keine Chance im Kampf gegen die Diebesbande. Auf Wiedersehen!");
        addnav("Tägliche News","news.php");
    }
    $session[user][specialinc]="";
}
?>