<?php

// 09072004

/*************************************************************
HUNTER'S LODGE for LoGD 0.9.7 ext (GER)
by weasel and anpera

mod by tcb - Spezielle M�belst�cke

*************************************************************/

// Auslagerung der Schl�ssel by Maris
// Unterbinden der Eigenvergabe von besonderen Titeln by Azura

require_once "common.php";


define('DP_KOSTEN_SPECIAL_ITEM',350);
define('DP_KOSTEN_LONG_BIO',300);
define('DP_MAX_SPECIAL_ITEMS',20);

addcommentary();
page_header("J�gerh�tte");
addnav("Zur�ck zum Dorf","village.php");
if ($_GET['op']!="points")
{
    addnav("Punkte","lodge.php?op=points");
}
if ($_GET['op']=="points")
{
    addnav("Empfehlungen","referral.php");
}

$config = unserialize($session['user']['donationconfig']);
$pointsavailable=$session['user']['donation']-$session['user']['donationspent'];

if ($_GET['op']=="")
{
    output("`b`cDie J�gerh�tte`c`b");
    //output("Moo.  *chuckle*  Yeah, you talk to him, this is what it's like.  Well this can be fun.  Boy, that michele, she is one sexy chick.  And she's so much smarter than Eric.  That's what I like about her most, her sharp intelligence.  Ok, should we start helping him now?  No.  You could be a stenographer, Eric.  Can you even spell Stenagorapher.  I can, cause I'm smart.  SMRT.  We're giving him obliging pauses in our converstaion now.  Allright, text for the hunting lodge.  Well, shouldn't it be similar to the superuser grotto?  AAAAH, CAN EPEE BE IN CHARGE OF THE HUNTING LODGE?  ");
    output("`0Du folgst einem schmalen Pfad, der hinter den St�llen entlang f�hrt. Am Ende dieses Pfades steht die J�gerh�tte. Ein T�rsteher stoppt dich und m�chte deine Mitgliedskarte sehen `n`n ");
    addnav("Empfehlungen","referral.php");
    if ($session['user']['donation']>=10)
    {
        output("Nach dem Zeigen deiner Mitgliedskarte sagt er, \"`7Sehr sch�n, willkommen in der J. C. Petersen J�gerh�tte.  Du hast noch `$`b$pointsavailable`b`7 Punkte zur Verf�gung,`0\" und l�sst dich rein.
`n`n
Du betritts einen Raum, der durch einen grossen Kamin am anderen Ende beherrscht wird. Die holzget�felten W�nde werden mit Waffen, Schilden und angebrachten Jagdtroph�en einschliesslich den K�pfen von einigen Drachen bedeckt, die im flackernden Licht des Kamines zu leben scheinen.
`n`n
Viele hohe St�hle f�llen den Raum.  In dem Stuhl der am n�chsten beim Feuer ist, sitzt J. C. Petersen und liest
\"Alchemie Heute.\"
`n`n
W�hrend du dich n�herst, hebt ein grosser Jagdhund, der zu seinen F�ssen liegt, den Kopf und �berlegt ob er dich kennt.
Als er dich als vertrauensw�rdig einstuft legt er sich wieder hin und schl�ft weiter. `b`4Solltest Du allerdings auch nur auf die Idee kommen,
die Anwesenden mit Protzereien oder Gejammer �ber die Anzahl deiner Punkte zu langweilen, wird er deine Mitgliedskarte gen�sslich zwischen seinen rasiermesserscharfen Z�hnen zerfetzen. Mindestens...`4`b
`n`n
In der N�he ein schroffes J�gergerede:`n");
        viewcommentary("hunterlodge","Hinzuf�gen",25);
        addnav("Punkte einsetzen");
        addnav("Charmepunkte abfragen (20 Punkte)","lodge.php?op=charm");
        addnav("Giftphiole erwerben (20 Punkte)","lodge.php?op=poison");
        if ($config['namechange']==1)
        {
            addnav("Farbiger Name (25 Punkte)","lodge.php?op=namechange");
        }
        else
        {
            addnav("Farbiger Name (300 Punkte)","lodge.php?op=namechange");
        }
        addnav("10 N�chte in der Kneipe (30 Punkte)","lodge.php?op=innstays");
        addnav("2 Edelsteine (50 Punkte)","lodge.php?op=gems");
        addnav("Extra Waldk�mpfe f�r 30 Tage (100 Punkte)","lodge.php?op=forestfights");
        addnav("Heilerin Golinda f�r 30 Tage (100 Punkte)","lodge.php?op=golinda");
        
        addnav("Zur Burg reiten (100 Punkte)","lodge.php?op=reiten1");
        addnav("Shortcuts kaufen (100 Punkte)","lodge.php?op=shortcut1");
        addnav('Pr�parierset (200 Punkte)','lodge.php?op=trophy');
        addnav("PvP-Immunit�t (300 Punkte)","lodge.php?op=immun");
        addnav('L�ngere Bio ('.DP_KOSTEN_LONG_BIO.' Punkte)','lodge.php?op=bio');
        
        addnav('Rund ums Haus');
        if ($session['user']['house']>0 && $session['user']['housekey']==$session['user']['house'])
        {
            
            addnav("Hausschl�ssel","lodge.php?op=keys1");
            addnav("Privatgem�cher","lodge.php?op=private_keys1");
            
        }
        addnav('Einzigartiges M�belst�ck ('.DP_KOSTEN_SPECIAL_ITEM.' Punkte)','lodge.php?op=item');
        
        if ($session['user']['donation']>=2000)
        {
            addnav("Sonderbonus");
        }
        if ($session['user']['donation']>=2000 && $pointsavailable>=50)
        {
            addnav("Titel �ndern (50 Punkte)","lodge.php?op=title");
        }
    }
    else
    {
        output("Du ziehst die Karte deines Lieblingsgasthauses heraus, wo 9 von 10 Slots mit dem kleinen Profil von Cederik abgestempelt sind.
`n`n
Der T�rsteher schaut fl�chtig auf deine Karte, r�t dir nicht soviel zu trinken und weist dir den Weg zur�ck.");
    }
}
else if ($_GET['op']=="points")
{
    addnav("Zur�ck zur Lodge","lodge.php");
    output("`bPunkte:`b`n`n
Legend of the Green Dragon bietet dir die M�glichkeit, spezielle \"Donationpoints\" zu sammeln, mit denen du Sonderfunktionen freischalten kannst.`n
Diese Punkte gibt es f�r besondere (geheime) Leistungen und f�r sogenannte \"Referrals\" (Empfehlungen) als Belohnung gesammelt werden. Erst wenn du mindestens 10 Donationpoints hast, kommst du in die Jagdh�tte.`n`n
Klicke im Eingangsbereich der J�gerh�tte auf \"Empfehlungen\", wenn du wissen willst, wie du auf diesem Weg an Donationpoints kommst.");
    
    
    output("`nWenn du den Programmierer von LoGD belohnen willst, kannst du pro gespendetem US-$ ebenfalls 100 Punkte kassieren.
Schicke dazu irgendeinen Beweis deiner Spende, z.B. einen Screenshot der PayPal-Best�tigung, an ".getsetting("gameadminemail","").". F�r eine Spende an den Programmierer (Eric Stevens a.k.a. MightyE) benutze den PayPal-Link, der auf jeder Seite oben rechts zu finden ist.");
    output("`n`n
`bDas kannst du mit diesen Punkten anstellen:`b`n
- Umsonst in der Kneipe wohnen (10 N�chte f�r 30 Punkte).`n
- Edelsteine kaufen (2 St�ck f�r 50 Punkte)`n
- Zus�tzliche Waldk�mpfe kaufen (100 Punkte f�r 30 Tage lang 1 extra Kampf; maximal 5 mehr pro Tag)!`n
- 'Zur Burg reiten' im Wald freischalten (100 Punkte),`n
- Zus�tzliche Shortcuts erwerben (100 Punkte),`n
- Ein Pr�parierset kaufen (200 Punkte),`n
- PvP-Immunit�t kaufen (300 Punkte f�r permanente Immunit�t),`n
- Die Zeichenbegrenzung deiner Bio von 250 auf �ber 4000 erh�hen!,`n
- Einen farbigen Namen machen (300 Punkte). Umf�rben kostet nur noch 25 Punkte. `n
- Anzeige der Charmepunkte (20 Punkte)`n
- T�dliches Gift erwerben (20 Punkte)`n
- Ersatzschl�ssel (10) und zus�tzliche Schl�ssel (100 Punkte + 10 Edelsteine) f�r dein Haus kaufen.`n
- Besondere, von dir gestaltete M�bel (".DP_KOSTEN_SPECIAL_ITEM." Punkte) f�r dein Haus kaufen.`n
- Ab 2000 gesammelten Punkten (ob ausgegeben oder nicht) kannst du dir f�r 50 Punkte einen eigenen Titel aussuchen.`n
`n`n`7Du hast noch `$`b$pointsavailable`b`7 Punkte von insgesamt `4".$session['user']['donation']." `7gesammelten Punkten �brig.
");
}
else if ($_GET['op']=="golinda")
{
    output("30 Tage Zugang zu Golinda der Heilerin kosten 100 Punkte. Golinda heilt zum halben Preis.");
    if ($pointsavailable<100)
    {
        output("`n`n`$Du hast nicht genug Punkte!`0");
    }
    else
    {
        addnav("Bet�tige Zugang zu Golinda");
        addnav("JA","lodge.php?op=golindaconfirm");
    }
    addnav("Zur�ck zur Lodge","lodge.php");
}
else if ($_GET['op']=="golindaconfirm")
{
    if ($pointsavailable >= 100)
    {
        $config['healer'] += 30;
        output("J. C. Peterson gibt dir eine Karte und sagt \"Mit dieser Karte kannst du an 30 verschiedenen Tagen bei Golinda vorstellig werden.\"");
        $session['user']['donationspent']+=100;
    }
    addnav("Zur�ck zur Lodge","lodge.php");
    
}
else if ($_GET['op']=="reiten1")
{
    if ($config['castle'])
    {
        output("Du hast diese Option bereits gekauft. Um zur Burg zu kommen, brauchst du ansonsten nur ein `bPferd`b. Ein `iPferd`i ist ein Tier der Kategorie 'Pferde' in Mericks Stall.");
    }
    else
    {
        output("Hiermit schaffst du dir die M�glichkeit, mit einem Reittier im Wald auch zur Burg reiten zu k�nnen. Du kannst nur auf Pferden reiten, also die Tiere in Merick's Stall, die in der Kategorie 'Pferde' stehen.");
        if ($pointsavailable<100)
        {
            output("`n`n`$Du hast nicht genug Punkte!`0");
        }
        else
        {
            addnav("Bet�tige Freischaltung");
            addnav("JA","lodge.php?op=reiten2");
        }
    }
    addnav("Zur�ck zur Lodge","lodge.php");
}
else if ($_GET['op']=="reiten2")
{
    if ($pointsavailable >= 100)
    {
        $config['castle'] = 100;
        output("J. C. Peterson gibt dir eine Karte und sagt \"Mit dieser Karte findest du den Weg zur Burg, wenn du ein Pferd hast.\"");
        $session['user']['donationspent']+=100;
    }
    addnav("Zur�ck zur Lodge","lodge.php");
    
}
else if ($_GET['op']=="shortcut1")
{
    $sqlex = "SELECT shortcuts FROM account_extra_info WHERE acctid=".$session['user']['acctid'];
    $resex = db_query($sqlex) or die(db_error(LINK));
    $rowex = db_fetch_assoc($resex);
    
    if ($rowex['shortcuts']>=9)
    {
        output("Du hast bereits 10 Shortcuts.`nMehr kannst du nicht erwerben!");
    }
    else
    {
        output("Hiermit kannst du dir einen weiteren Shortcut erwerben.`n
Shortcuts belegst du in deinen Einstellungen mit kurzen Texten (Namen, h�ufig verwendete Begriffe etc.) und kannst sie im RPG mit den K�rzeln %x0 - %x9 aufrufen, wodurch sie durch den von dir voreingestellten Text ersetzt werden.`nSie d�rfen farbig sein, aber keine anderen Shortcuts enthalten.`n`n
Du hast bereits `^".($rowex['shortcuts']+1)."`& von `^10 m�glichen`& Shortcuts.`n`0");
        if ($pointsavailable<100)
        {
            output("`n`n`$Du hast nicht genug Punkte!`0");
        }
        else
        {
            addnav("Bet�tige Freischaltung");
            addnav("JA","lodge.php?op=shortcut2");
        }
    }
    addnav("Zur�ck zur Lodge","lodge.php");
}
else if ($_GET['op']=="shortcut2")
{
    if ($pointsavailable >= 100)
    {
        $sql = "UPDATE account_extra_info SET shortcuts=shortcuts+1 WHERE acctid=".$session['user']['acctid']."";
        db_query($sql) or die(db_error(LINK));
        output("J. C. Peterson gew�hrt dir einen weiteren Shortcut und gibt dir die M�glichkeit dich eleganter auszudr�cken.");
        $session['user']['donationspent']+=100;
        $config['shortcuts']+=1;
    }
    addnav("Zur�ck zur Lodge","lodge.php");
    
}
else if ($_GET['op']=="forestfights")
{
    if (!is_array($config['forestfights']))
    {
        $config['forestfights']=array();
    }
    output("1 Extra Waldkampf pro Tag f�r 30 Tage kostet 100 Punkte. Du bekommst einen extra Waldkampf an jedem Tag, an dem du spielst.`n");
    if ($pointsavailable<100)
    {
        output("`n`n`$Du hast nicht genug Punkte!`0");
    }
    else
    {
        addnav("Best�tige Extra Waldk�mpfe");
        addnav("JA","lodge.php?op=fightbuy");
    }
    addnav("Zur�ck zur Lodge","lodge.php");
    reset($config['forestfights']);
    while (list($key,$val)=each($config['forestfights']))
    {
        //output("Du hast noch {$val['left']} Tage, an denen zu einen zus�tzlichen Waldkampf f�r deine am {$val['bought]} bekommst.`n");
        output("Du hast noch {$val['left']} Tage, an denen zu einen zus�tzlichen Waldkampf f�r deine am {$val['bought']} bekommst.`n");
    }
}
else if ($_GET['op']=="fightbuy")
{
    if (count($config['forestfights'])>=5)
    {
        output("Du Kannst maximal 5 extra Waldk�mpfe haben pro Tag.`n");
    }
    else
    {
        if ($pointsavailable>0)
        {
            array_push($config['forestfights'],array("left"=>30,"bought"=>date("M d")));
            output("Du wirst in den n�chsten 30 Tagen, an denen du spielst, einen extra Waldkampf haben.");
            $session['user']['donationspent']+=100;
        }
        else
        {
            output("Extra Waldk�mpfe zu kaufen kostet 100 Punkte, aber du hast nicht so viele.");
        }
    }
    addnav("Zur�ck zur Lodge","lodge.php");
}
else if ($_GET['op']=="innstays")
{
    output("10 freie �bernachtungen in der Kneipe kosten 30 Punkte. Bist du dir sicher, dass du das willst?");
    if ($pointsavailable<30)
    {
        output("`n`n`$Du hast nicht genug Punkte!`0");
    }
    else
    {
        addnav("Best�tige 10 freie �bernachtungen");
        addnav("JA","lodge.php?op=innconfirm");
    }
    addnav("Zur�ck zur Lodge","lodge.php");
}
else if ($_GET['op']=="innconfirm")
{
    if ($pointsavailable>=30)
    {
        output("J. C. Petersen gibt dir eine Karte und sagt \"Coupon: Gut f�r 10 �bernachtungen in der Boar's Head Kneipe\"");
        $config['innstays']+=10;
        $session['user']['donationspent']+=30;
    }
    addnav("Zur�ck zur Lodge","lodge.php");
    
}
else if ($_GET['op']=="charm")
{
    output("Du fragst J. C. Petersen, ob er dein Aussehen beurteilen kann. Er mustert dich kurz und verspricht dir dann, dass er dir f�r die Kleinigkeit von 20 Punkten eine ehrliche Antwort geben wird.");
    if ($pointsavailable<20)
    {
        output("`n`n`$Du hast nicht genug Punkte!`0");
    }
    else
    {
        addnav("Best�tige Charmepunkt-Anzeige");
        addnav("JA","lodge.php?op=charmconfirm");
    }
    addnav("Zur�ck zur Lodge","lodge.php");
}
else if ($_GET['op']=="charmconfirm")
{
    if ($pointsavailable>=20)
    {
        if ($session['user']['charm']<=0)
        {
            output("J. C. Petersen schaut dich angewidert an und sagt \"Du bist h�sslich wie die Nacht, ich kann einfach nichts Sch�nes an dir finden.\"");
        }
        else if ($session['user']['charm']==1)
        {
            output("J. C. Petersen schaut dich kurz an und sagt \"Du bist genauso h��lich wie jeder gemeine B�rger, mehr als `^1 Punkt`0 wird dir kein Preisrichter geben.\"");
        }
        else
        {
            output("J. C. Petersen mustert dich noch einmal ganz genau und sagt \"Du bist `^".$session['user']['charm']."`0mal so sch�n wie der gemeine B�rger.\"");
        }
        $session['user']['donationspent']+=20;
    }
    addnav("Zur�ck zur Lodge","lodge.php");
    
}
else if ($_GET['op']=="poison")
{
    output("Du fragst J. C. Petersen frei heraus, ob er dir nicht etwas seines t�dlichen und verbotenen Giftes aush�ndigen kann kann. Sofort packt er dich am Kragen und h�lt dir den Mund zu, dann zieht er dich in eine Ecke und gibt dir zu verstehen, dass dich eine Phiole das 20 Punkte kosten wird und 3 Ladungen enth�lt. Weiterhin macht er dir klar,dass dir sein Jagdhund dorthin beissen wird, wo es besonders weh tut, solltest du noch einmal auf die Idee kommen dieses Thema laut anzusprechen.");
    if ($pointsavailable<20)
    {
        output("`n`n`$Du hast nicht genug Punkte!`0");
    }
    else
    {
        addnav("Best�tige Erwerb von Gift");
        addnav("JA","lodge.php?op=poisonconfirm");
    }
    addnav("Zur�ck zur Lodge","lodge.php");
    
}
else if ($_GET['op']=="poisonconfirm")
{
    if ($pointsavailable>=20)
    {
        output("Petersen �ffnet ein kleines Wandschr�nkchen und holt eine winzige Phiole mit gr�nem Inhalt heraus.`nDieses Gift reicht f�r 3 Ladungen, schau dir einfach eine Truhenfalle deiner Wahl im Haus an und f�lle sie damit auf!`n");
        
        item_add($session['user']['acctid'],'gftph');
        
        
        $session['user']['donationspent']+=20;
    }
    addnav("Zur�ck zur Lodge","lodge.php");
    
}
else if ($_GET['op']=="gems")
{
    output("2 Edelsteine f�r 50 Punkte. Bist du dir sicher, dass du das willst?");
    if ($pointsavailable<50)
    {
        output("`n`n`$Du hast nicht genug Punkte!`0");
    }
    else
    {
        addnav("Best�tige 2 Edelsteine");
        addnav("JA","lodge.php?op=gemsconfirm");
    }
    addnav("Zur�ck zur Lodge","lodge.php");
}
else if ($_GET['op']=="gemsconfirm")
{
    if ($pointsavailable>=50)
    {
        output("J. C. Petersen gibt dir 2 Edelsteine und sagt \"Damit, mein Freund, wird dein Leben leichter werden\"");
        $session['user']['gems']+=2;
        $session['user']['donationspent']+=50;
    }
    
}
else if ($_GET['op']=="titeel1")
{
    addnav("Zur�ck zur Lodge","lodge.php");
    
    $n=$session['user']['name'];
    
    $sql = 'SELECT ctitle,cname FROM account_extra_info WHERE acctid='.$session['user']['acctid'];
    $res = db_query($sql);
    $row_extra = db_fetch_assoc($res);
    
    if ($row_extra['ctitle']!="" AND($session['user']['title']!="Flauschihase" AND $session['user']['title']!="Feigling"))
    {
        $teil=$row_extra['ctitle'];
    }
    else
    {
        $teil=$session['user']['title'];
    }
    //Ab sofort wird hier auf spezielle Titel wie Feigling gepr�ft und die M�glichkeit sie zu �ndern untersagt
    
    if (($teil == "Flauschihase") OR($teil == "Feigling") OR($teil == "`4Ramius Sklave") OR($teil == "`4Ramius Sklavin") OR($teil == "Tempeldiener") OR($teil == "Flauschihase") OR($teil == "`2Frosch`0") OR($teil == "`2Kr�te`0") OR($teil == "F�rst von ".getsetting('townname','Atrahor')) OR($teil == "F�rstin von ".getsetting('townname','Atrahor')))
    {
        output("Leider kannst du diesen Titel nicht auf diese Weise �ndern!");
        addnav("Zur�ck zur Lodge","lodge.php");
    }
    else
    {
        output("Dein bisheriger Titel lautet: `b$teil`b, dein kompletter Name: `b$n`b`n`nWie soll dein Titel von nun an lauten?`n(Sende ein leeres Feld ab, wenn du deinen regul�ren Titel wieder haben willst.)`n");
        $output.="<form action='lodge.php?op=titeel2' method='POST'><input name='teil' size='25' maxlength='25' value=\"".HTMLEntities($teil)."\"> <input type='submit' value='Vorschau'></form>";
        addnav("","lodge.php?op=titeel2");
    }
}
else if ($_GET['op']=="titeel2")
{
    addnav("Zur�ck zur Lodge","lodge.php");
    $falsetitle = false;
    
    $sql = 'SELECT ctitle,cname FROM account_extra_info WHERE acctid='.$session['user']['acctid'];
    $res = db_query($sql);
    $row_extra = db_fetch_assoc($res);
    $cname = $row_extra['cname'];
    
    
    if ($_POST['teil']=='')
    {
        $teil=$session['user']['title'];
    }
    else
    {
        $teil=stripslashes($_POST['teil']);
        // Alle anderen Tags als erlaubte Farbcodes rausschmei�en
        $_POST['name'] = preg_replace('/[`][^'.regex_appoencode(1,false).']/','',$_POST['name']);
        // Anf�hrungszeichen machen nur Probleme...
        $teil = str_replace('\'','',$teil);
        $teil = str_replace('"','',$teil);
        
        // Titel nicht leer, aber auch nix regul�res drin?
        if (trim(preg_replace('/`./','',$teil))=='')
        {
            $teil=$session['user']['title'];
            $_POST['teil'] = '';
        }
        else
        {
            // Offene Tags zumachen
            // nicht mehr n�tig, weil die Tags verboten sind
            //$teil = closetags($teil,'`c`i`b');
            
            $cleartitle = strtolower(preg_replace("/`./","",$teil));
            foreach ($titles AS $this)
            {
                if (strtolower($this[0])==$cleartitle || strtolower($this[1])==$cleartitle)
                {
                    $falsetitle = true;
                    break;
                }
            }
        }
        //Titel die nur durch Ereignisse erlange werden k�nnen, werden nun nicht mehr w�hlbar sein
        $check_teil = strip_appoencode($teil,3);
        $check_teil = trim($check_teil);
        $check_teil = ereg_replace("  "," ",$check_teil);
        $check_teil = ereg_replace("  "," ",$check_teil);
        if (($check_teil == "Flauschihase") OR($check_teil == "Feigling") OR($check_teil == "Flauschihase") OR($check_teil == "Frosch") OR($check_teil == "Kr�te") OR($check_teil == "Ramius Sklave") OR($check_teil == "Ramius Sklavin") OR($check_teil == "Tempeldiener") OR($check_teil == "F�rst von ".getsetting('townname','Atrahor')) OR($check_teil == "F�rstin von ".getsetting('townname','Atrahor')))
        {
            output("Diesen Titel darfst du nicht nehmen!");
            //addnav("Zur�ck zur Lodge","lodge.php");
        }
        else
        {
            // Schauen, ob der neue Titel nich mehr als 25 Zeichen hat
            if (strlen($teil)>25)
            {
                output("Du hast dir zwar einen neuen Titel verdient, aber so lang muss er ja nun wirklich nicht sein.");
                output("`n`n<a href='lodge.php?op=titeel1'>Lass es mich nochmal probieren</a>",true);
                addnav("","lodge.php?op=titeel1");
            }
            else if ($falsetitle)
            {
                output('Diesen Titel hast du nicht verdient. Bitte w�hle einen eigenen.');
                output("`n`n<a href='lodge.php?op=titeel1'>Lass es mich nochmal probieren</a>",true);
                addnav("","lodge.php?op=titeel1");
            }
            else
            {
                
                //$name = ($row_extra['cname']!='' ? $row_extra['cname'] : $session['user']['login']);
                
                if ($row_extra['cname']!='')
                {
                    $name = $row_extra['cname'];
                }
                else
                {
                    $name = $session['user']['login'];
                }
                
                $teil = $teil.'`0';
                
                $neu = trim($teil).' '.trim($name);
                
                output("Dein neuer Titel soll $teil`0 sein, dein Name also $neu`0 ?");
                if ($_POST['teil']=="")
                {
                    // $teil=$session[user][title];
                    $output.="<form action=\"lodge.php?op=titeel3\" method='POST'><input type='hidden' name='teil' value=\"\"><input type='submit' value='Ja' class='button'>, �ndere meinen Titel zur�ck auf $teil f�r 50 Punkte.</form>";
                }
                else
                {
                    //	$teil=stripslashes($_POST['teil']);
                    $output.="<form action=\"lodge.php?op=titeel3\" method='POST'><input type='hidden' name='teil' value=\"$teil\"><input type='submit' value='Ja' class='button'>, �ndere meinen Titel auf $teil f�r 50 Punkte.</form>";
                }
                output("`n`n<a href='lodge.php?op=titeel1'>Nein, lass es mich nochmal probieren</a>",true);
                addnav("","lodge.php?op=titeel1");
                addnav("","lodge.php?op=titeel3");
            }
        }
    }
}
else if ($_GET['op']=="titeel3")
{
    addnav("Zur�ck zur Lodge","lodge.php");
    
    if ($pointsavailable>=50)
    {
        
        $sql = 'SELECT ctitle,cname FROM account_extra_info WHERE acctid='.$session['user']['acctid'];
        $res = db_query($sql);
        $row_extra = db_fetch_assoc($res);
        
        $teil=stripslashes($_POST['teil']);
        // Alle anderen Tags als erlaubte Farbcodes rausschmei�en
        $_POST['name'] = preg_replace('/[`][^'.regex_appoencode(1,false).']/','',$_POST['name']);
        // Anf�hrungszeichen machen nur Probleme...
        $teil = str_replace('\'','',$teil);
        $teil = str_replace('"','',$teil);
        
        // Titel nicht leer, aber auch nix regul�res drin?
        if (trim(preg_replace('/`./','',$teil))=='')
        {
            $teil=$session['user']['title'];
            $_POST['teil'] = '';
        }
        
        // Offene Tags zumachen
        // nicht mehr n�tig, weil die Tags verboten sind
        //$teil = closetags($teil,'`c`i`b');
        
        // Schauen, ob der neue Titel nich mehr als 25 Zeichen hat
        if (strlen($teil)>25)
        {
            output("Du hast dir zwar einen neuen Titel verdient, aber so lang muss er ja nun wirklich nicht sein.");
            output("`n`n<a href='lodge.php?op=titeel1'>Lass es mich nochmal probieren</a>",true);
            addnav("","lodge.php?op=titeel1");
        }
        else
        {
            $news = "`&{$session['user']['name']}
            `^ ist nun bekannt als `^";
            
            //$name = ($row_extra['cname']!='' ? $row_extra['cname'] : $session['user']['login']);
            if ($row_extra['cname']!='')
            {
                $name = $row_extra['cname'];
            }
            else
            {
                $name = $session['user']['login'];
            }
            
            //$title = (strlen($teil) > 0 ? $teil : $session['user']['title']);
            
            if (strlen($teil) > 0)
            {
                $title = $teil.'`0';
            }
            else
            {
                $title = $session['user']['title'];
                $title = $title.'`0';
            }
            
            $neu = trim($title).' '.trim($name);
            
            $session['user']['name'] = $neu;
            
            $session['user']['donationspent']+=50;
            
            $sql = 'UPDATE account_extra_info SET ctitle="'.$teil.'" WHERE acctid='.$session['user']['acctid'];
            db_query($sql);
            
            $news.=$session['user']['name'].'`&!';
            addnews($news);
            
            output("Gratulation, dein neuer Name ist jetzt  {$session['user']['name']}
            `0!`n`n");
            
        }
    }
    else
    {
        output("Den Titel zu �ndern kostet 50 Punkte, aber du hast nur $pointsavailable Punkte.");
    }
    
}
else if ($_GET['op'] == 'title')
{
    
	// Titel, deren �nderung nicht erlaubt ist
	// Case insensitive, ohne Farbcodes!
	$arr_titles_nochange = array(
									'flauschihase','kr�te','frosch','ramius sklave','ramius sklavin','feigling','tempeldiener',
									'f�rst von '.strtolower(getsetting('townname','Atrahor')),'f�rstin von '.strtolower(getsetting('townname','Atrahor'))								
								);
								
	$arr_tmp = user_get_aei('ctitle');
	$str_ctitle = $arr_tmp['ctitle'];
	unset($arr_tmp);
	        
    addnav('Zur�ck zur H�tte','lodge.php');
			
    output('`c`bTitel �ndern`b`c`n`n');
			
    $int_cost = 50;
    output('Den Titel zu �ndern kostet '.$int_cost.' Punkte.');
	
	if($_GET['finished']) {
		output('`n`n`c`@`bGratulation, du besitzt hiermit den eigenen Titel '.$str_ctitle.'`@! Zusammen ergibt das '.$session['user']['name'].'`@!`b`c`0`n`n');
		
		$session['user']['donationspent'] += $int_cost;
        		
		page_footer();
		exit;
	}
	
	if($pointsavailable < $int_cost) {
		output('`nLeider verf�gst du �ber zu wenig Punkte, um dir das leisten zu k�nnen!');
		page_footer();
		exit;
	}
	               
    output('`n`n`0Wie soll dein eigener Titel aussehen? (Lasse das Feld leer, um deinen normalen Titel wiederherzustellen)`n`n');
	
	$str_newtitle = stripslashes($_POST['newtitle']);
		
	if(isset($_POST['newtitle'])) {
		
		$str_msg = '';
		$bool_ok = (bool)$_GET['ok'];
		
		$str_newtitle = str_replace('`0','',$str_newtitle);
		// Alle anderen Tags als erlaubte Farbcodes rausschmei�en
		$str_newtitle = preg_replace('/[`][^'.regex_appoencode(1,false).']/','',$str_newtitle);
		
		output('Du w�hlst: `b'.$str_newtitle.'`b`n`n');
		
		// Validieren und gegebenenfalls �ndern
		// Pr�fen, ob wir Titel �berhaupt �ndern d�rfen
		if(is_array($arr_titles_nochange)) {
			if( in_array( strip_appoencode(strtolower($str_ctitle),3) ,$arr_titles_nochange)
			 || in_array( strip_appoencode(strtolower($session['user']['title']),3) ,$arr_titles_nochange)
			 ) {
				$str_result = 'ctitle_changeforbidden';
			}
		}
		
		// Pr�fen, ob dieser Titel nicht exklusiv
		if(is_array($arr_titles_nochange)) {
			if( in_array(strip_appoencode(strtolower($str_newtitle),3),$arr_titles_nochange) ) {
				$str_result = 'ctitle_exclusive';
			}
		}
		
		if(empty($str_result)) {
			$str_result = user_retitle(0,false,$str_newtitle,$bool_ok);
		}
						
		if(true !== $str_result) {
				
			switch($str_result) {
				
				case 'ctitle_blacklist':
					$str_msg .= 'Diesen Titel darfst du leider nicht w�hlen, da er von den G�ttern verboten wurde.`n';		
				break;
				
				case 'ctitle_tooshort':
					$str_msg .= 'Dieser Titel ist zu kurz (Mindestens '.getsetting('nameminlen',3).' Zeichen).`n';		
				break;
				
				case 'ctitle_toolong':
					$str_msg .= 'Dieser Titel ist zu lang (Maximal '.getsetting('namemaxlen',3).' Zeichen).`n';		
				break;
				
				case 'ctitle_badword':
					$str_msg .= 'Dieser Titel enth�lt verbotene oder anst��ige W�rter.`n';		
				break;
				
				case 'ctitle_officialtitle':
				case 'ctitle_exclusive':
					$str_msg .= 'Diesen Titel darfst du nicht nehmen.`n';		
				break;
				
				case 'ctitle_changeforbidden':
					$str_msg .= 'Deinen aktuellen Titel darfst du leider nicht auf diese Weise �ndern.`n';		
				break;
				
				default:
					$str_msg .= '';
				break;
				
			}
			
			output($str_msg);
						
		}
		else {
			if($bool_ok) {
				user_set_name(0);
				redirect('lodge.php?op=title&finished=1');
			}
			else {
				output('`@Diesen Titel kannst du verwenden!`n`n');
				$str_lnk = 'lodge.php?op=title&ok=1';
				addnav('',$str_lnk);
				
				output('<form action="'.$str_lnk.'" method="POST">
							<input type="hidden" name="newtitle" value="',true);
				rawoutput($str_newtitle);
				output('"><input type="submit" value="Diesen Titel �bernehmen!"></form>',true);
			}
		}
						
	}
	
	$str_lnk = 'lodge.php?op=title';
	addnav('',$str_lnk);
	
	$arr_form = array('newtitle'=>'Dein neuer Titel mit oder ohne Farbcodes:');
	$arr_data = array('newtitle'=>$str_newtitle);
	
    output('`n<form action="'.$str_lnk.'" method="POST">',true);
	
	showform($arr_form,$arr_data,false,'Vorschau!');
	
	output('</form>',true);

}
else if ($_GET['op']=="namechange")
{
            
    addnav('Zur�ck zur H�tte','lodge.php');
			
    output('`c`bNamensfarbe �ndern`b`c`n`n');
			
    if ($config['namechange']==1)
    {
		$int_cost = 25;
        output('Da du schon vorher viele Punkte f�r die Farb�nderung gegeben hast kostet es dich diesmal nur 25 Punkte.');
    }
    else
    {
		$int_cost = 300;
        output('Da es deine erste Farb�nderung ist kostet es dich 300 Punkte . Beim n�chsten Wechsel fallen nur 25 Punkte Kosten an.');
    }
	
	if($_GET['finished']) {
		output('`n`n`c`@`bGratulation, du �nderst deinen Namen in '.$session['user']['name'].'!`b`c`0`n`n');
		
		$session['user']['donationspent'] += $int_cost;
                
        $config['namechange']=1;
        		
		page_footer();
		exit;
	}
	
	if($pointsavailable < $int_cost) {
		output('`nLeider verf�gst du �ber zu wenig Punkte, um dir das leisten zu k�nnen!');
		page_footer();
		exit;
	}
			
    output('`n`nDein ge�nderter Name muss der selbe Name sein wie vor der Farb�nderung, nur dass er jetzt die Farbcodes enthalten darf.`n`n');
            
    output('Dein Name bisher ist: ');
    $output.=$session['user']['name'];
    output(', und so wird er aussehen: '.$session['user']['name']);
    output('`n`n`0Wie soll dein farbiger Name aussehen?`n`n');
	
	$str_newname = stripslashes($_POST['newname']);
		
	if(!empty($str_newname)) {
		
		$str_msg = '';
		$bool_ok = (bool)$_GET['ok'];
		
		$str_newname = str_replace('`0','',$str_newname);
		// Alle anderen Tags als erlaubte Farbcodes rausschmei�en
		$str_newname = preg_replace('/[`][^'.regex_appoencode(1,false).']/','',$str_newname);
		
		output('Du w�hlst: `b'.$str_newname.'`b`n`n');
		
		// Validieren und gegebenenfalls �ndern
		$str_comp1 = strtolower(strip_appoencode(trim($session['user']['login']),3) );
		$str_comp2 = strtolower(strip_appoencode(trim($str_newname),3) );
		
		if($str_comp1 != $str_comp2) {
			output('Dein neuer Name muss genauso bleiben wie dein alter Name. Du kannst die Gross-/Kleinschreibung �ndern,
						Farbcodes entfernen oder hinzuf�gen, aber ansonsten muss alles gleichbleiben.`n');
			$str_result = 'failname';
		}
		else {
			$str_result = user_rename(0,$str_newname,$bool_ok);
		}
		
		if(true !== $str_result) {
				
			switch($str_result) {
				
				case 'cname_toomuchcolors':
					$str_msg .= 'Du hast zu viele Farben in deinem Namen benutzt. Du kannst maximal '.getsetting('maxcolors',10).' Farbcodes benutzen.`n';		
				break;
				
				default:
					$str_msg .= '';
				break;
				
			}
			
			output($str_msg);
						
		}
		else {
			if($bool_ok) {
				user_set_name(0);
				redirect('lodge.php?op=namechange&finished=1');
			}
			else {
				output('`@Diesen Namen kannst du verwenden!`n`n');
				$str_lnk = 'lodge.php?op=namechange&ok=1';
				addnav('',$str_lnk);
				
				output('<form action="'.$str_lnk.'" method="POST">
							<input type="hidden" name="newname" value="',true);
				rawoutput($str_newname);
				output('"><input type="submit" value="Diese F�rbung �bernehmen!"></form>',true);
			}
		}
						
	}
	
	$str_lnk = 'lodge.php?op=namechange';
	addnav('',$str_lnk);
	
	$arr_form = array('newname'=>'Dein neuer Name mit Farbcodes:');
	$arr_data = array('newname'=>$str_newname);
	
    output('`n<form action="'.$str_lnk.'" method="POST">',true);
	
	showform($arr_form,$arr_data,false,'Vorschau!');
	
	output('</form>',true);

}
else if ($_GET['op']=="immun")
{
    if ($session['user']['pvpflag']=="5013-10-06 00:42:00")
    {
        output("J. C. Petersen nickt dir zu und gibt dir zu verstehen, dass du noch immer unter seinem Schutz stehst.");
    }
    else if (($session['user']['pvpflag']=="1986-10-06 00:42:00") && ($session['user']['marks']<31))
    {
        output("J. C. Petersen zeigt dir einen Vogel und macht dir sehr schnell klar, dass er vorerst nichts mehr f�r dich tun kann. Er kann niemanden sch�tzen, der selbst mordend durchs Land zieht.");
    }
    else
    {
        output("Du fragst J. C. Petersen, ob er deinen Aufenthaltsort vor herumstreifenden Dieben und M�rdern verbergen kann.");
        output(" Er nickt und verspricht dir, dass dir f�r die Kleinigkeit von 300 Punkten niemand mehr ein Haar kr�mmen wird. Er wird auch mit Dag Durnick reden. Allerdings kann er f�r nichts mehr garantieren, wenn du selbst einen Mord begehst!`n`n");
        output("300 Punkte f�r permanente PvP Immunit�t ausgeben?`n(Die Immunit�t verf�llt, sobald du selbst PvP machst, oder ein Kopfgeld auf jemanden aussetzt und kann dann `bnicht`b mehr so schnell erneuert werden!)");
        addnav("Immunit�t best�tigen?");
        addnav("JA","lodge.php?op=immunconfirm");
    }
    addnav("Zur�ck zur Lodge","lodge.php");
}
else if ($_GET['op']=="immunconfirm")
{
    if ($pointsavailable>=300)
    {
        output("J. C. Petersen nutzt seinen Einfluss, um dich f�r PvP-Spieler unangreifbar zu machen. Es kann auch kein (weiteres) Kopfgeld auf dich ausgesetzt werden.`nDenke daran, dass du nur so lange gesch�tzt bist, bis du selbst jemanden angreifst, oder jemanden auf Dag's ");
        output(" Kopfgeldliste setzt. Tust du das, kann selbst Petersen dir in Zukunft nicht mehr helfen.");
        $session['user']['pvpflag']="5013-10-06 00:42:00";
        $session['user']['donationspent']+=300;
    }
    else
    {
        output("Du hast nicht genug Punkte!");
    }
    
}
else if ($_GET['op']=="keys1")
{
    $sql = "SELECT k.*,a.acctid FROM keylist k
LEFT JOIN accounts a ON a.acctid=k.owner
WHERE value1=".$session['user']['house']." ORDER BY id ASC";
    $result = db_query($sql) or die(db_error(LINK));
    
    $lost = array();
    
    while ($k = db_fetch_assoc($result))
    {
        
        if ($k['owner'] == 0 || $k['acctid'] == 0)
        {
            $lost[] = $k;
        }
    }
    
    if (sizeof($lost))
    {
        output("`b`c`&Verlorene Schl�ssel:`c`b<table cellpadding=2 align='center'><tr><td>`bNr.`b</td><td>`bAktion`b</td></tr>",true);
        for ($i=0; $i<sizeof($lost); $i++)
        {
            $row = $lost[$i];
            $bgcolor=($i%2==1?"trlight":"trdark");
            output("<tr class='$bgcolor'><td>".$session['user']['house']."</td><td><a href='lodge.php?op=keys2&id=$row[id]'>Ersetzen (10 Punkte)</a></td></tr>",true);
            addnav("","lodge.php?op=keys2&id=$row[id]");
        }
        output("</table>",true);
    }
    else
    {
        
        $sql = "SELECT status FROM houses WHERE owner=".$session['user']['acctid']."";
        $res = db_query($sql) or die(db_error(LINK));
        
        $house = db_fetch_assoc($res);
        if (($house['status']<30) || ($house['status']>=40))
        {
            
            output("Der Schl�sselsatz f�r dein Haus ist komplett. Willst du einen zus�tzlichen Schl�ssel f�r 100 Punkte und 10 Edelsteine kaufen?");
            addnav("Zus�tzlicher Schl�ssel (100 Punkte + 10 Edelsteine)","lodge.php?op=keys2&id=new");
        }
        else
        {
            output("Du hast alle Schl�ssel und vergr��ern kannst du dein ".get_house_state($house['status'],false)." auch nicht! Was willst du also hier?");
        }
    }
    addnav("Zur�ck zur Lodge","lodge.php");
}
else if ($_GET['op']=="keys2")
{
    if ($_GET['id']=="new")
    {
        output("`b100`b ");
    }
    else
    {
        output("`b10`b ");
    }
    output("Punkte f�r diesen Schl�ssel ausgeben?");
    addnav("Schl�sselkauf best�tigen?");
    addnav("JA","lodge.php?op=keys3&id=".$_GET['id']."");
    addnav("Zur�ck zur Lodge","lodge.php");
}
else if ($_GET['op']=="keys3")
{
    if ($_GET['id']=="new")
    {
        if ($pointsavailable<100)
        {
            output("Du hast nicht genug Punkte �brig.");
        }
        else if ($session['user']['gems']<10)
        {
            output("Du hast nicht genug Edelsteine dabei.");
        }
        else
        {
            $sql = "SELECT * FROM keylist WHERE value1=".$session['user']['house']." ORDER BY id ASC";
            $result = db_query($sql) or die(db_error(LINK));
            $nummer=db_num_rows($result)+1;
            db_free_result($result);
            $sql="INSERT INTO keylist (owner,value1,value2,gold,gems,description) VALUES (".$session['user']['acctid'].",".$session['user']['house'].",$nummer,0,0,'Schl�ssel f�r Haus Nummer ".$session['user']['house']."')";
            db_query($sql) or die(db_error(LINK));
            $session['user']['donationspent']+=100;
            $session['user']['gems']-=10;
            output("Du hast jetzt `b$nummer`b Schl�ssel f�r dein Haus! �berlege gut, an wen du sie vergibst.");
        }
    }
    else
    {
        if ($pointsavailable<10)
        {
            output("Du hast nicht genug Punkte �brig.");
        }
        else
        {
            $nummer=$_GET['id'];
            $sql="UPDATE keylist SET owner=".$session['user']['acctid'].",hvalue=0,chestlock=0,gold=0,gems=0 WHERE id=$nummer";
            db_query($sql);
            $session['user']['donationspent']+=10;
            output("Der Schl�ssel wurde ersetzt.");
        }
    }
    addnav("Zur�ck zur Lodge","lodge.php");
}
else if ($_GET['op']=="private_keys1")
{
    
    $sql = 'SELECT status FROM houses WHERE houseid='.$session['user']['house'];
    $res = db_query($sql);
    $house = db_fetch_assoc($res);
    
    if ($house['status'] < 10)
    {
        output('Du musst dein Haus erst ausbauen, um Platz f�r Privatgem�cher zu schaffen!');
    }
    else
    {
        output("`b40`b Punkte und `b10`b Edelsteine f�r ein zus�tzliches Privatgemach f�r die Bewohner deines Hauses ausgeben?");
        addnav("Privatgemachkauf best�tigen?");
        addnav("JA","lodge.php?op=private_keys2");
    }
    addnav("Zur�ck zur Lodge","lodge.php");
    
}
else if ($_GET['op']=="private_keys2")
{
    
    if ($pointsavailable<40)
    {
        output("Du hast nicht genug Punkte �brig.");
    }
    else
    {
        if ($session['user']['gems']<10)
        {
            output("Du hast nicht genug Edelsteine dabei.");
        }
        else
        {
            
            $nummer=item_count(' tpl_id="privb" AND value1='.$session['user']['house'] ) + 1;
            
            $item['tpl_description'] = 'Besitzurkunde f�r ein Privatgemach in Haus Nr. '.$session['user']['house'];
            $item['tpl_value1'] = $session['user']['house'];
            
            item_add($session['user']['acctid'],'privb',true,$item);
            
            $session['user']['donationspent']+=40;
            $session['user']['gems']-=10;
            output("Du hast jetzt `b$nummer`b Privatgem�cher f�r dein Haus! �berlege gut, an wen du sie vergibst.");
        }
    }
    
    addnav("Zur�ck zur Lodge","lodge.php");
}
else if ($_GET['op'] == 'item')
{
    
    $res = item_list_get(" tpl_id='unikat' AND owner=".$session['user']['acctid'],'',false);
    $anzahl = db_num_rows($res);
    
    output('Hier hast Du die M�glichkeit, Dir f�r '.DP_KOSTEN_SPECIAL_ITEM.' Punkte ein einzigartiges, nach Deinen W�nschen gestaltetes M�belst�ck fertigen zu lassen.`n');
    output('Au�erdem bietet Petersen dir auch an, dieses M�belst�ck an andere Einwohner '.getsetting('townname','Atrahor').'s zu versenden.`n');
    output('Bisher wurden f�r Dich '.$anzahl.' besondere(s) M�bel hergestellt:`n');
    
    while ($item = db_fetch_assoc($res))
    {
        
        output('`i`n'.$item['name'].'`i');
        
    }
    
    if ($anzahl < DP_MAX_SPECIAL_ITEMS && $pointsavailable >= DP_KOSTEN_SPECIAL_ITEM)
    {
        
        output('`n`nPetersen ben�tigt nun die folgenden Informationen von Dir:`n`n');
        
        output('<form method="POST" action="lodge.php?op=item_confirm">Name des M�belst�cks: <input type="text" name="name" size="40" maxlength="90">`n`nBeschreibung: <input type="text" name="desc" size="60" maxlength="200">`n`n<input type="submit" name="ok" value="Kaufen">`n</form>',true);
        addnav('','lodge.php?op=item_confirm');
        
    }
    else
    {
        output('`n`nLeider ist Petersen nicht bereit, Dir noch weitere M�belst�cke fertigen zu lassen!');
        
    }
    
}
else if ($_GET['op'] == "item_confirm")
{
    
    addnav('Besonderes M�belst�ck');
    
    // warum auch immer da mehrfach escaped wird..
    $name = '`^Unikat - '.trim(stripslashes($_POST['name']));
    $desc = trim(stripslashes($_POST['desc']));
    output('Wirklich `b'.DP_KOSTEN_SPECIAL_ITEM.'`b Punkte f�r dieses einzigartige M�belst�ck ausgeben? Es wird ungef�hr so aussehen:`n`n');
    output($name.' `&('.$desc.'`&)');
    
    output('`nWillst du es selbst verwenden oder an jemanden verschenken?`n`n');
    
    output('<form method="POST" action="lodge.php?op=item_ok">`n<input type="hidden" name="name" value="',true);
    rawoutput(htmlentities($name));
    output('"><input type="hidden" name="desc" value="',true);
    rawoutput(htmlentities($desc));
    output('"><input type="submit" name="ok_selbst" value="Selbst verwenden!"> <input type="submit" name="ok_geschenk" value="Verschenken">`n</form>',true);
    addnav('','lodge.php?op=item_ok');
    
    addnav('Nein, zur�ck!','lodge.php');
    
}
else if ($_GET['op'] == "item_ok")
{
    
    $name = stripslashes($_POST['name']);
    $desc = stripslashes($_POST['desc']);
    
    if ($_GET['act'] == 'search' && strlen($_POST['search']) > 2)
    {
        
        output($name.' `&('.$desc.'`&)`n`n');
        
        $count = strlen($_POST['search']);
        $search="%";
        for ($x=0; $x<$count; $x++)
        {
            $search .= substr($_POST['search'],$x,1)."%";
        }
        
        $sql = 'SELECT name,acctid FROM accounts WHERE name LIKE "'.$search.'" AND acctid!='.$session['user']['acctid'];
        $res = db_query($sql);
        
        $link = 'lodge.php?op=item_ok';
        
        output('<form action="'.$link.'" method="POST">',true);
        
        output('<input type="hidden" name="name" value="',true);
        rawoutput(htmlentities($name));
        output('"><input type="hidden" name="desc" value="',true);
        rawoutput(htmlentities($desc));
        output('">',true);
        
        output(' <select name="acctid">',true);
        
        while ($p = db_fetch_assoc($res) )
        {
            
            output('<option value="'.$p['acctid'].'">'.preg_replace("'[`].'","",$p['name']).'</option>',true);
            
        }
        
        output('</select>`n`n',true);
        
        output('<input type="submit" class="button" value="Ausw�hlen!"></form>',true);
        addnav('',$link);
    }
    else if ($_POST['ok_geschenk'])
    {
        
        output($name.' `&('.$desc.'`&)`n`n');
        
        $link = 'lodge.php?op=item_ok&act=search';
        
        output('An wen willst du das Unikat versenden?`n`n');
        
        output('<form action="'.$link.'" method="POST">',true);
        
        output('<input type="hidden" name="name" value="',true);
        rawoutput(htmlentities($name));
        output('"><input type="hidden" name="desc" value="',true);
        rawoutput(htmlentities($desc));
        output('">',true);
        
        output('Name: <input type="text" name="search"> ',true);
        output('<input type="submit" class="button" value="Suchen!"></form>',true);
        addnav('',$link);
        
    }
    // END Geschenk
    else
    {
        
        $acctid = (int)$_POST['acctid'];
        
        $session['user']['donationspent'] += DP_KOSTEN_SPECIAL_ITEM;
        debuglog('Gab '.DP_KOSTEN_SPECIAL_ITEM.' DP f�r Specialitem '.$name);
        
        $item['tpl_name'] = html_entity_decode($name);
        $item['tpl_description'] = html_entity_decode($desc);
        $item['tpl_gold'] = 0;
        $item['tpl_gems'] = 10;
        
        item_add(($acctid ? $acctid : $session['user']['acctid']) , 'unikat' , true , $item );
        
        output('Petersen protokolliert gewissenhaft diesen Wunsch und meint dann:`n');
        if (!$acctid)
        {
            output('`7"Dein besonderes M�belst�ck steht nun f�r dich bereit. Viel Spa� damit..."');
            addnav('Besonderes M�belst�ck');
        }
        else
        {
            systemmail($acctid,'`2Ein Geschenk!',$session['user']['name'].'`2 hat dir ein Unikat namens '.$name.'`2 zum Geschenk gemacht. Du kannst es mit dir rumtragen, es anbeten oder einfach in ein Haus oder Privatgemach stellen! Ist das nicht nett?`n(Kleiner Tipp: Du findest es in deinem Inventar.)');
            output('`7"Dein besonderes M�belst�ck wurde an die gew�nschte Person geliefert. Hoffentlich gef�llt es..."');
        }
        output('`0, woraufhin er sich wieder seinem Buch zuwendet.');
        
        
    }
    
}
else if ($_GET['op']=="bio")
{
    $resextra = db_query("SELECT has_long_bio FROM account_extra_info WHERE acctid=".$session['user']['acctid']);
    $rowextra = db_fetch_assoc($resextra);
    
    if ($rowextra['has_long_bio']==1)
    {
        output("Du hast diese Option bereits gekauft und hast in deiner Bio Platz f�r ".getsetting('longbiomaxlength',4096)." Zeichen.");
    }
    else
    {
        output("Hiermit schaffst du den ersten Schritt aus deiner Unbedeutenheit heraus. Die anderen K�mpfer werden viel mehr �ber dich erfahren k�nnen, wenn du diese Option freischaltest.");
        if ($pointsavailable<('DP_KOSTEN_LONG_BIO'))
        {
            output("`n`n`$Du hast nicht genug Punkte!`0");
        }
        else
        {
            addnav("Bet�tige Freischaltung");
            addnav("JA","lodge.php?op=bio2");
        }
    }
    addnav("Zur�ck zur Lodge","lodge.php");
}
else if ($_GET['op']=="bio2")
{
    if ($pointsavailable >= DP_KOSTEN_LONG_BIO)
    {
        $sql = "UPDATE account_extra_info SET has_long_bio=1 WHERE acctid = ".$session['user']['acctid'];
        db_query($sql) or die(sql_error($sql));
        output("J. C. Peterson erf�llt dir deinen Wunsch und macht dich zu einem bedeutenderem B�rger.`nDeine Bio fasst nun ".getsetting('longbiomaxlength',4096)." Zeichen.");
        $session['user']['donationspent']+= DP_KOSTEN_LONG_BIO;
        debuglog('Gab '.DP_KOSTEN_LONG_BIO.' DP f�r eine lange bio ');
    }
    addnav("Zur�ck zur Lodge","lodge.php");
}
else if ($_GET['op']=="trophy")
{
    $resextra = db_query("SELECT trophyhunter FROM account_extra_info WHERE acctid=".$session['user']['acctid']);
    $rowextra = db_fetch_assoc($resextra);
    
    if ($rowextra['trophyhunter']==1)
    {
        output("Du hast doch bereits dein eigenes von J. C. Petersen signiertes Pr�parierset.`nOder wei�t du etwa nicht was du damit anstellen sollst ?`n");
    }
    else
    {
        output("J. C. Petersen zeigt dir die vielen Jagdtroph�en in seiner H�tte, die er selbst herstestellt hat. Nun bietet er dir sein pers�nliches Pr�parierset f�r l�ppiche 200 Punkte an.");
        if ($pointsavailable<200)
        {
            output("`n`n`$Du hast nicht genug Punkte!`0");
        }
        else
        {
            addnav("Bet�tige Freischaltung");
            addnav("JA","lodge.php?op=trophy2");
        }
    }
    addnav("Zur�ck zur Lodge","lodge.php");
}
else if ($_GET['op']=="trophy2")
{
    if ($pointsavailable >= 200)
    {
        $sql = "UPDATE account_extra_info SET trophyhunter=1 WHERE acctid = ".$session['user']['acctid'];
        db_query($sql) or die(sql_error($sql));
        output("Gratulation! Du besitzt nun dein eigenes Pr�parierset und bist somit im Stand deine eigenen Troph�en herzustellen.");
        $session['user']['donationspent']+=200;
    }
    addnav("Zur�ck zur Lodge","lodge.php");
}

$session['user']['donationconfig'] = serialize($config);

page_footer();

?>
