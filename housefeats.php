<?

/**
Auslagerung der Sonderfähigkeiten ausgebauter Haustypen
Erfordert : houses.php
Beinhaltet Knappen-Erweiterung (erfordet : disciples.lib.php)
by Maris (Maraxxus@gmx.de)
**/

require_once("common.php");
page_header();

//Tier füttern auf dem Bauernhof
if ($_GET[act]=="feed")
{
    output("Dein {$playermount['mountname']}
lässt es sich hier richtig gut gehen und tollt herum, frisst sich satt und ist voll regeneriert.`n");
    if ($session['user']['gems']>0)
    {
        output("Ein Edelstein dürfte genügen um die kleinen Schäden zu bezahlen, die dein Tier im Übermut verursacht hat.`n");
        $session['user']['gems']--;
    }
    else
    {
        output("Du hast nichts dabei um die kleinen Schäden zu begleichen, die dein Tier im Übermut verursacht hat. Wie peinlich!`n");
        $session['user']['charm']-=2;
        
    }
    
    $sql = "SELECT mountextrarounds,hasxmount,xmountname FROM account_extra_info WHERE acctid=".$session[user][acctid]."";
    $result = db_query($sql) or die(db_error(LINK));
    $rowm = db_fetch_assoc($result);
    
    $buff = unserialize($playermount['mountbuff']);
    $session['bufflist']['mount']=$buff;
    $session['bufflist']['mount']['rounds']+=$rowm['mountextrarounds'];
    
    if ($rowm['hasxmount']==1)
    {
        $session['bufflist']['mount']['name']=$rowm['xmountname']." `&({$session['bufflist']['mount']['name']}`&)";
    }
    addnav("Zurück zum Haus","inside_houses.php");
    
    // Bordellbesuch
}
else if ($_GET[act]=="amuse")
{
    
    $happy = array("name"=>"`!Extrem gute Laune","rounds"=>45,"wearoff"=>"`!Deine gute Laune vergeht allmählich wieder.`0","defmod"=>1.15,"roundmsg"=>"Du schwelgst in Erinnerung an den Bordellbesuch und tust alles dafür dass es nicht dein Letzter war!","activate"=>"defense");
    
    if ($session['user'][seenlover]==0)
    {
        
        output("`7Du ziehst dich zurück und willst dich einmal so richtig verwöhnen lassen.`n");
        if ($session[user][gold]<2000)
        {
            output("Doch leider wird daraus nichts, denn du hast keine `#2000 Gold`7, die du dafür brauchst!`n");
        }
        else
        {
            output("`7Du lässt es dir so richtig gut gehen und wirst wohl für den Rest des Tages dieses Grinsen nicht mehr aus dem Gesicht bekommen.`n");
            $session[user][gold]-=2000;
            $session['bufflist']['happy']=$happy;
            $session['user']['seenlover']=1;
            
            switch (e_rand(1,3))
            {
            case 1:
                break;
            case 2:
                //News-Eintrag und Mail an den Partner... so gehts ja nicht
                addnews("`@".$session['user']['name']."`@ wurde gesehen, wie  ".($session[user][sex]?"sie":"er")." mit einem breiten Grinsen ein Bordell verliess!");
                
                if ($session['user'][charisma]==4294967295)
                {
                    $sql = "SELECT acctid,name FROM accounts WHERE locked=0 AND acctid=".$session['user'][marriedto]."";
                    $result = db_query($sql) or die(db_error(LINK));
                    $row = db_fetch_assoc($result);
                    $partner=$row['name'];
                    systemmail($row['acctid'],"`$Bordellbesuch!`0","`&{$session['user']['name']}
                    `6 wurde gesehen, wie ".($session[user][sex]?"sie":"er")." sich im Bordell vergnügt hat. Willst du dir das gefallen lassen ?");
                }
                
                break;
            case 3:
                break;
            }
        }
    }
    else
    {
        output("Schon wieder ?! Nein, für heute hast du dich schon genug vergnügt!");
    }
    addnav("Zurück zum Haus","inside_houses.php");
    
    
}
else if ($_GET[act]=="fill")
{
    // Anwendungen im Gildenhaus nachfüllen
    
    $sql = "SELECT specid,specname,filename,usename FROM specialty WHERE active='1'AND specid='".$session[user][specialty]."'";
    $result = db_query($sql);
    $row = db_fetch_assoc($result);
    $skills = array($row['specid']=>$row['specname']);
    
    if ($session['user']['specialtyuses'][$row['usename']."uses"]>0)
    {
        output("`5Der Tag ist noch jung und du hast noch nicht alle Anwendungen in `@".$skills[$session['user']['specialty']]."`5 aufgebraucht! Was willst du also hier?`n");
    }
    else
    {
        output("`7Du ziehst dich zurück und philosophierst mit den Gildenmeistern über ");
        if ($session[user][gold]<1000)
        {
            output("`@deine Armut`7. Denn du hast keine `#1000 Gold`7, die du dafür brauchst!");
        }
        else
        {
            {
                output("`@".$skills[$session['user']['specialty']]."`7. Die Einsichten, die du dabei gewinnst, sind überwältigend!");
                
                $session['user']['specialtyuses'][$row['usename']."uses"] = 5;
                
            }
            $session[user][gold]-=1000;
            output("`n`n`7Du erhältst weitere `^5`7 Anwendungen für heute!`n");
        }
        
        
        
        
        
    }
    
    
    
    addnav("Zurück zum Haus","inside_houses.php");
    
}
else if ($_GET[act]=="cry")
{
    // Im Festungskeller austoben
    output("`7Der Keller ist groß und von dickem Mauerwerk umgeben. Hier bist du ganz ungestört und kannst einmal so richtig Dampf ablassen.`n");
    if ($session['user'][seenmaster]==1)
    {
        if ($session['user'][turns]<=0)
        {
            output("`7Doch irgendwie fehlt dir dazu gerade die Kraft...");
        }
        else
        {
            output("`7Du gibst dich ungehemmt deiner Schmach hin und heulst dich einmal so richtig aus. Nachdem du dich wieder beruhigt hast stellst du fest, dass du wieder den Mut gefunden hast deinem Meister erneut unter die Augen zu treten.`n");
            output("`7`nDu verlierst `#3`7 Waldkämpfe und kannst erneut gegen deinen Meister antreten!");
            $session['user'][seenmaster]=0;
            $session[user][turns]-=3;
            if ($session['user']['turns']<0)
            {
                $session['user']['turns']=0;
            }
        }
    }
    else
    {
        output("`7Doch irgendwie geht es dir gerade recht gut und du verspürst nicht den Drang danach. Doch du beschließt nach der nächsten Demütigung deines Meisters genau hierhin zu kommen...");
    }
    addnav("Zurück zum Haus","inside_houses.php");
    
}
else if ($_GET[act]=="train")
{
    // In der Kaserne kämpfen üben
    $training = array("name"=>"`!Kampfübungen","rounds"=>50,"wearoff"=>"`!Du hast die Lektionen der Veteranen vergessen und kehrst wieder zu deinem unkonventionellen Kampfstil zurück.`0","defmod"=>1.1,"atkmod"=>1.1,"roundmsg"=>"Du wendest an was du in den Übungen gelernt hast!","activate"=>"defense","activate"=>"offense");
    
    output("`7Du holst tief Luft und bittest die alten Veteranen dir die hohe Kunst des Kämpfens näher zu bringen.`n");
    if ($session[user][gold]<3000)
    {
        output("Doch das kostet `#3000 Gold`7, die du nicht dabei hast. Allerdings gibt es dafür eine Tracht Prügel umsonst!`n");
    }
    else
    {
        output("`7Die alten Veteranen nehmen dich ganz schön hart ran. Doch du kannst dir einen sehr guten Kampfstil dabei aneignen!`n");
        $penality= e_rand(1,3);
        $session[user][gold]-=3000;
        $session[user][turns]-=$penality;
        output("`7Du verlierst dabei `#$penality`7 Waldkämpfe.");
        $session['bufflist']['training']=$training;
    }
    addnav("Zurück zum Haus","inside_houses.php");
    
    
}
else if ($_GET[act]=="torture")
{
    // Im Kerker Gefangene quälen
    output("`7Du steigst die steinernen Stufen zu den Kerkerzellen hinab, bewaffnet mit Brenneisen und Kneifzange.`n`n");
    if ($session[user][turns]<4)
    {
        output("Allerdings fühlst du dich schon viel zu müde um heute noch irgendwen zu quälen.`n`&Du musst mindestens 4 Waldkämpfe übrig haben.");
    }
    else
    {
        
        output("Folgende Gefangenen glaubst du irgendwo im Dorf schonmal gesehen zu haben:`n`n");
        $sql = "SELECT accounts.acctid,name,race,imprisoned,login,sex,level,laston,loggedin,activated,account_extra_info.abused FROM accounts LEFT JOIN account_extra_info ON account_extra_info.acctid=accounts.acctid WHERE imprisoned!=0 ORDER BY level DESC, dragonkills DESC, login ASC";
        $result = db_query($sql) or die(db_error(LINK));
        output("<table border=0 cellpadding=2 cellspacing=1 bgcolor='#999999'>",true);
        output("<tr class='trhead'><td><b>Level</b></td><td><b>Name</b></td><td><b>Rasse</b></td><td><b><img src=\"images/female.gif\">/<img src=\"images/male.gif\"></b></td><td><b>Status</b></td><td><b>Zustand</b></td><td><b>Strafe in Tagen</b></tr>",true);
        $max = db_num_rows($result);
        for ($i=0; $i<$max; $i++)
        {
            $row = db_fetch_assoc($result);
            output("<tr class='".($i%2?"trdark":"trlight")."'><td>",true);
            output("`^$row[level]`0");
            output("</td><td>",true);
            if ($session[user][loggedin])
            {
                output("<a href=\"mail.php?op=write&to=".rawurlencode($row['login'])."\" target=\"_blank\" onClick=\"".popup("mail.php?op=write&to=".rawurlencode($row['login'])."").";return false;\"><img src='images/newscroll.GIF' width='16' height='16' alt='Mail schreiben' border='0'></a>",true);
            }
            if ($session[user][loggedin])
            {
                output("<a href='housefeats.php?act=torture3&char=".rawurlencode($row['login'])."'>",true);
            }
            if ($session[user][loggedin])
            {
                addnav("","housefeats.php?act=torture3&char=".rawurlencode($row['login'])."");
            }
            output("`".($row[acctid]==getsetting("hasegg",0)?"^":"&")."$row[name]`0");
            if ($session[user][loggedin])
            {
                output("</a>",true);
            }
            output("</td><td>",true);
            output($colraces[$row['race']]);
            output("</td><td align=\"center\">",true);
            output($row[sex]?"<img src=\"images/female.gif\">":"<img src=\"images/male.gif\">",true);
            output("</td><td>",true);
            $loggedin= user_get_online(0,$row);
            output($loggedin?"`#Wach`0":"`3Schläft`0");
            output("</td><td>",true);
            if ($row[abused]==0)
            {
                output("`@Ok`0");
            }
            else if ($row[abused]==1)
            {
                output("`4gequält`0");
            }
            else if ($row[abused]==2)
            {
                output("`4abgekämpft`0");
            }
            output("</td><td>",true);
            
            if ($row['imprisoned']>0)
            {
                output($row['imprisoned']);
            }
            else
            {
                output("unbestimmt");
            }
            output("</td></tr>",true);
            
        }
        output("</table>",true);
        addnav("Wahllos quälen","housefeats.php?act=torture2");
    }
    addnav("Zurück zum Haus","inside_houses.php");
}
else if ($_GET[act]=="torture2")
{
    output("`7Du suchst dir wahllos einen der Gefangenen aus und beginnst dein sadistisches Werk.`n");
    output("`7Dabei steigerst du dich regelrecht in einen Blutrausch!`n");
    output("`7Als du fertig bist verspürst du große Lust dich nun mit jemandem zu befassen, der sich auch wehren kann.`n`n");
    $penality= e_rand(2,4);
    output("`7Du verlierst `#$penality`7 Waldkämpfe und erhälst einen weiteren Spielerkampf dazu!`n");
    $session[user][playerfights]+=1;
    $session[user][turns]-=$penality;
    $session[user][charm]--;
    addnav("Zurück zum Haus","inside_houses.php");
}
else if ($_GET[act]=="torture3")
{
    $result = db_query("SELECT name,accounts.acctid,sex,level,imprisoned,account_extra_info.abused FROM accounts LEFT JOIN account_extra_info ON account_extra_info.acctid=accounts.acctid WHERE login='$_GET[char]'");
    $row = db_fetch_assoc($result);
    
    if ($row['abused']==0)
    {
        
        output("`7Du schleichst dich zu ".($row['name'])."`7 in die Zelle und quälst ".($row[sex]?"sie":"ihn")." für ein paar Stunden. Als du fertig bist verspürst du große Lust dich nun mit jemandem zu befassen, der sich auch wehren kann.`n`n ");
        
        $mail="`@{$session['user']['name']}
        `& ist, während du im Kerker eingesessen hast, in deine Zelle gekommen und hat dich ";
        
        switch (e_rand(1,10))
        {
        case 1 :
            $mail=$mail."mit einem selbstgebackenen Kuchen ";
            break;
        case 2 :
            $mail=$mail."mit dem Erzählen ".($session['user']['sex']?"ihrer":"seiner")." Lebensgeschichte ";
            break;
        case 3 :
            $mail=$mail."durch stundenlanges Anstarren ";
            break;
        case 4 :
            $mail=$mail."mit heftigen Kitzelattacken ";
            break;
        case 5 :
            $mail=$mail."durch ".($session['user']['sex']?"ihre":"seine")." bloße Anwesenheit ";
            break;
        case 6 :
            $mail=$mail."mit dem Erzählen dummer Witze ";
            break;
        case 7 :
            $mail=$mail."mit einem langen Vortrag über Bellerophontes Heldentaten ";
            break;
        case 8 :
            $mail=$mail."mit einem Rezept für Löwenzahnsalat ";
            break;
        case 9 :
            $mail=$mail."mit lautem Gesang ";
            break;
        case 10 :
            $mail=$mail."mit einer Feder ";
            break;
        }
        $mail=$mail."übelst gequält. Dieses traumatische Erlebnis wird dich noch sehr lang verfolgen!";
        systemmail($row['acctid'],"`$Folterung!`0",$mail);
        
        $sql = "UPDATE account_extra_info SET abused=1 WHERE acctid = ".$row['acctid'];
        db_query($sql) or die(sql_error($sql));
        
        $penality= e_rand(2,4);
        output("`7Du verlierst `#$penality`7 Waldkämpfe und erhältst einen weiteren Spielerkampf dazu!`n");
        $session[user][playerfights]+=1;
        $session[user][turns]-=$penality;
        $session[user][charm]--;
        addnav("Zurück zum Haus","inside_houses.php");
        
    }
    else
    {
        output("`&".($session['user']['sex']?"Diese Kämpferin":"Dieser Kämpfer")." kommt leider heute für eine Peinigung nicht mehr in Frage.`nBei ".($session['user']['sex']?"ihrem":"seinem")." jetzigen Zustand hättest du wahrlich keine Freude an deinem Werk und würdest nur unnötig Zeit verschwenden.`n`nWillst du dir nicht jemand anderes stattdessen aussuchen ?");
        addnav("Ja...ok","housefeats.php?act=torture");
        addnav("Nee, keine Lust mehr","inside_houses.php");
    }
    
    
}
else if ($_GET[act]=="healing")
{
    // Sich im Kloster heilen lassen
    output("Du schleichst die hölzernen Stufen zum Krankensaal hinauf und klagst den Nonnen dein Leid.`n");
    if ($session[user][hitpoints]>=$session[user][maxhitpoints]*0.9)
    {
        output("`7Doch die Ordenschwestern scheinen mit Wichtigerem beschäftigt zu sein und schenken deinen Wehwehchen nur wenig Beachtung.`n");
    }
    else
    {
        output("`7Die Ordensschwestern legen dich sofort auf ein Bett und versorgen deine Verwundungen. Nach einiger Zeit fühlst du dich wieder gesund.`n");
        output("`7Du verlierst einige Waldkämpfe, regenerierst aber wieder komplett!");
        $session[user][hitpoints]=$session[user][maxhitpoints];
        if (($session[user][maxhitpoints])<=400)
        {
            $session[user][turns]-=1;
        }
        else if (($session[user][maxhitpoints])<=1000)
        {
            $session[user][turns]-=2;
        }
        else if (($session[user][maxhitpoints])<=2000)
        {
            $session[user][turns]-=3;
        }
        else if (($session[user][maxhitpoints])<=5000)
        {
            $session[user][turns]-=4;
        }
        else
        {
            $session[user][turns]-=5;
        }
        $session[user][reputation]-=5;
        
        
    }
    addnav("Zurück zum Haus","inside_houses.php");
    // dem Blutgott opfern
}
else if ($_GET[act]=="sacrifice")
{
    output("`7Du begibst dich mit schnellen festen Schritten zum Blutaltar...`n");
    if (($session[user][hitpoints]<($session[user][maxhitpoints]*0.5)) || ($session['user']['turns']<2))
    {
        output("`7...Und erntest nur schallendes Lachen vom finstren Herrn. Er will nur dein Blut und nicht dein Leben! Und wenn du ehrlich bist fühlst du dich auch wirklich viel zu schlapp.`n`n`&Du solltest schon mehr als 50% deiner Lebenskraft und noch mindestens 2 Waldkämpfe übrig haben!`n");
    }
    else
    {
        if ($session['user']['specialtyuses'][darkartuses]<9)
        {
            output("`7Und opferst dem Blutgott etwas deiner Lebenskraft. In Anerkennung deiner Gabe gewährt er dir `#1`7 zusätzliche Anwendung in Dunklen Künsten.`n");
            $session[user][hitpoints]*=0.85;
            $session['user']['specialtyuses'][darkartuses]+=1;
        }
        else
        {
            output("`7Und opferst dem Blutgott etwas deiner Lebenskraft. Jedoch scheint er dein Opfer aus irgendeinem Grund nicht angenommen zu haben.`n");
        }
    }
    addnav("Zurück zum Haus","inside_houses.php");
    
    //Mütterchens leckere (?) Kohlsuppe
}
else if ($_GET[act]=="soup")
{
    if ($session['user']['turns']>0)
    {
        output("`&Du stellst dich an den Tresen und orderst eine Schüssel Kohlsuppe.`nMütterchen erklärt mit zittriger Stimme  : `n");
        output("`5Mein Geheimrezept beinhaltet einen ganz besonderen und seltenen magischen Pilz. Daher muss ich von dir pro Teller `^100 Goldmünzen`5 verlangen.`nAuch gebe ich zu, dass meine Seekraft nicht mehr die Beste ist, und dass ich mich beim Pilzepflücken ab und an mal leicht vertue...`n");
        output("`&Willst du immer noch die Suppe kosten ?");
        addnav("Klar!","housefeats.php?act=soup2");
        addnav("Nein, zurück zum Haus","inside_houses.php");
    }
    else
    {
        output("`&Für dich ist es schon zu spät etwas Warmes zu essen. Eigentlich willst du nur noch ins Bett...");
        addnav("Nagut...","inside_houses.php");
    }
}
else if ($_GET[act]=="soup2")
{
    if ($session['user']['gold']<100)
    {
        output("`&Die Alte krächzt :`n`5So schlecht ist meine Sehkraft auch noch nicht, dass ich Bettler und Schnorrer verkenne. Scher dich von dannen, sonst gibts was mit dem Nudelholz!");
        addnav("Schnell weg hier","inside_houses.php");
    }
    else
    {
        output("`&Die Alte nimmt dein Gold und serviert dir einen Suppenteller mit einer leicht grünlich blubbernden Brühe. Nach kurzem Zögern probierst du einen großen Löffel voll und ");
        switch (e_rand(1,25))
        {
        case 1 :
        case 2 :
        case 3 :
        case 4 :
            output("`&stellst fest, dass die Suppe gar nicht mal so übel schmeckt. Aber eine besondere Wirkung stellst du auch nicht fest.");
            $session['user']['gold']-=100;
            $session['user']['turns']-=1;
            addnav("Zurück zum Haus","inside_houses.php");
            break;
        case 5 :
        case 6 :
        case 7 :
        case 8 :
            output("`&fühlst dich kräftig und gestärkt. Diese Suppe hat es in sich! Du bekommst `^einen Waldkampf`& dazu!");
            $session['user']['gold']-=100;
            $session['user']['turns']+=1;
            addnav("Zurück zum Haus","inside_houses.php");
            break;
        case 9 :
        case 10 :
        case 11 :
        case 12 :
        case 13 :
            output("`&bist von ihrem guten Geschmack derart überwältigt, dass du den ganzen Teller bis auf den letzten Tropfen leerschlürfst. Und schon bald verspürst du das dringende Bedürfnis deine Blase zu entleeren...`n@Du kannst heute nochmal das Toilettenhäuschen aufsuchen!`0");
            $session['user']['gold']-=100;
            $session['user']['turns']-=1;
            user_set_aei(array('usedouthouse' => 0));
            addnav("Zurück zum Haus","inside_houses.php");
            break;
        case 14 :
        case 15 :
            output("`&kommst zu dem Schluss, dass das alte Mütterchen diesmal wohl die Pilze mit Chilichoten verwechselt haben muss.`nDu schreist wie am Spiess und rennst wild mit den Armen rudernd durch den Ausgang des Gasthauses. Draussen angekommen wirfst du dich mit vollem Anlauf in die Pferdetränke.");
            output("`&Dabei wirst du von so ziemlich jedem gesehen, der dich hier kennt!`nDu verlierst `41 Charmpunkt`& und bist pitschnass...");
            $session['user']['gold']-=100;
            $session['user']['turns']-=1;
            $session['user']['charm']-=1;
            addnews("`^".$session['user']['name']."`^ nahm heute ein Bad in einer Pferdetränke. Wie peinlich!");
            addnav("Na toll","inside_houses.php");
            break;
        case 16 :
        case 17 :
            output("`&kommst zu dem Schluss, dass das alte Mütterchen diesmal wohl die Pilze mit Chilichoten verwechselt haben muss.`nDu schreist wie am Spiess und rennst wild mit den Armen rudernd durch den Ausgang des Gasthauses. Draussen angekommen wirfst du dich mit vollem Anlauf in die Pferdetränke.");
            output("`&Dabei stösst du dir an einer Kante so heftig den Kopf, dass du das Bewusstsein verlierst. Wie so oft ist in solchen Momenten niemand sonst anwesend und so hauchst du langsam dein Leben aus.`nEs gibt wahrlich bessere Möglichkeiten zu sterben!`n`n`4Du verlierst 10% deiner Erfahrung!`&");
            $session['user']['gold']-=100;
            $session['user']['turns']-=1;
            $session['user']['hitpoints']=0;
            $session['user']['experience']*=0.9;
            $session['user']['alive']=0;
            addnews("`^".$session['user']['name']."`^ ist heute in einer Pferdetränke ersoffen. Darüber hört man sogar die Toten lachen.");
            addnav("Arrrrgh","shades.php");
            break;
        case 18:
        case 19:

        case 20:
        case 21:
            output("`&bist angenehm überrascht durch den leckeren Geschmack. Du fühlst dich satt.`nKohlsuppe ist gesund und hält schlank! Deswegen bekommst du `^2 Charmpunkte`& dazu.");
            $session['user']['charm']+=2;
            $session['user']['gold']-=100;
            $session['user']['turns']-=1;
            addnav("Juhu!","inside_houses.php");
            break;
        case 22:
        case 23:
        case 24:
        case 25:
            output("`&Irgendwie schmeckt die Suppe leicht nach Fisch... Du bekommst wahnsinnige Lust heute nochmal angeln zu gehen.`n`@Du erhälst 3 zusätzliche Angelrunden für heute!`0");
            $sql = "UPDATE account_extra_info SET fishturn=fishturn+3 WHERE acctid = ".$session['user']['acctid'];
            db_query($sql) or die(sql_error($sql));
            addnav("Petri heil!","inside_houses.php");
            break;
        }
    }
}
else if ($_GET[act]=="ritual")
{
    output("`&Du steigst die mindestens 5000 Stufen deines Turmes empor, um ein Ritual zur Stärkung deiner mystischen Kräfte abzuhalten.`n");
    if ($session['user']['turns']<1)
    {
        output("`&Aber schon nach 5 Stufen bist du dir sicher, dass du es heute auf keinen Fall mehr bis zur Ebene auf der Spitze des Turmes schaffen wirst. Du bist einfach zu müde.");
        addnav("Zurück","inside_houses.php");
    }
    else
    {
        if ($session['user']['gems']<1)
        {
            output("`&Oben angekommen bereitest du alles vor und stellst dann fest, dass du den Edelstein, den du unbedingt brauchst, nicht dabei hast.`nAlso bleibt dir nichts anderes übrig als die 5000 Stufen wieder hinabzusteigen.`nDu verlierst `4einen Waldkampf`&.");
            $session['user']['turns']-=1;
            addnav("Zurück","inside_houses.php");
        }
        else
        {
            output("`&Oben angekommen zeichnest du nach einer kleinen Verschnaufpause ein Pentagramm auf den Boden, legst den Edelstein in die Mitte und beginnst dein Ritual.`n`n");
            $rand=e_rand(1,7);
            if ($session['user']['specialtyuses']['magicuses']>10)
            {
                $rand=5;
            }
            
            switch ($rand)
            {
            case 1 :
            case 2 :
            case 3 :
            case 4 :
                output("`&Dein Ritual hatte Erfolg!`nDeine mystischen Kräften wurden um `^5 Anwendungen`& aufgefüllt!");
                $session['user']['turns']-=1;
                $session['user']['gems']-=1;
                $session['user']['specialtyuses']['magicuses']+=5;
                break;
            case 5 :
            case 6 :
                output("`&Dein Ritual ging völlig in die Hose. Nicht nur dass der Edelstein fort ist, auch hast du dich in deiner Machtbesessenheit derart verausgabt, dass du alle deine Anwendungen in mystischen Kräften für heute `4verloren`& hast!");
                $session['user']['turns']-=1;
                $session['user']['gems']-=1;
                $session['user']['specialtyuses']['magicuses']=0;
                break;
            case 7 :
                output("`&Dein Ritual verlief ausgezeichnet! Du `@erhälst`& eine Stufe in mystischen Kräften dazu, sowie 3 Anwendungen!`n`n");
                $session['user']['turns']-=1;
                $session['user']['gems']-=1;
                $session['user']['specialtyuses']['magicuses']+=3;
                increment_specialty();
                break;
            }
            addnav("Zurück","inside_houses.php");
        }
    }
}
else if ($_GET[act]=="adventure")
{
    output("`&Du begibst dich zu den wackeren Abenteurern, die in einem kleinen Saal im Keller umgeben von allen möglichen Karten und viel Plunder ihre Heldentaten zum Besten geben.`nIhr kommt schnell ins Gespräch und als das Thema beim `^Verlassenen Schloss`& angekommen ist, flüstert dir einer der alten Abenteurer eine Geschichte in dein Ohr.`nWillst du dieser Erzählung lauschen (5 Waldkämpfe opfern), um heute einmal mehr ins Schloss zu können ?");
    addnav("Ja","housefeats.php?act=adventure2");
    addnav("Nein","inside_houses.php");
}
else if ($_GET[act]=="adventure2")
{
    if ($session['user']['turns']<5)
    {
        output("`&Während der langen Erzählung des Mannes schläfst du plötzlich ein.`nSo wird es dir auch nichts bringen!");
        addnav("Zurück","inside_houses.php");
    }
    else
    {
        output("`&Der alte Abenteurer redet stundenlang, aber du kannst seiner Erzählung viele nützlich Informationen entnehmen.`n`^Du kannst heute ein weiteres Mal ins Schloss!`&");
        $session['user']['castleturns']++;
        $session['user']['turns']-=5;
        addnav("Zurück","inside_houses.php");
    }
}
else if ($_GET[act]=="gems")
{
    if (($session['user']['gold']>50000) || ($session['user']['gems']>1000))
    {
        output("`&Du machst dich auf zum Edelsteinhändler und musst feststellen, dass er gerade nicht da ist.`nVersuch es doch später noch einmal.`n");
    }
    else
    {
        output("`&Du näherst dich dem etwas befremdlich aussehenden Edelsteinhändler, der gerade aus Übersee wieder angekommen ist.`nSofort zeigt er dir seine Waren und deutet an, dass er ebenso nicht abgeneigt ist etwas von dir anzukaufen.`n`nPro Edelstein verlangt er `^6000 Gold`&.`n`nPro Edelstein zahlt er `^600 Gold`&.");
        addnav("Kaufen");
        addnav("Einen kaufen","housefeats.php?act=gemsb&nmb=1");
        addnav("Drei kaufen","housefeats.php?act=gemsb&nmb=3");
        addnav("Fünf kaufen","housefeats.php?act=gemsb&nmb=5");
        addnav("Verkaufen");
        addnav("Einen verkaufen","housefeats.php?act=gemss&nmb=1");
        addnav("Drei verkaufen","housefeats.php?act=gemss&nmb=3");
        addnav("Fünf verkaufen","housefeats.php?act=gemss&nmb=5");
        addnav("Sonstiges");
    }
    addnav("Zurück zum Haus","inside_houses.php");
}
else if ($_GET[act]=="gemsb")
{
    $nmb=$_GET[nmb];
    output("`&Du teilst dem Mann mit, dass du gern `^$nmb ".($nmb==1?"Edelstein":"Edelsteine")."`& kaufen möchtest.`n");
    $cost=6000*$nmb;
    if ($session['user']['gold']<$cost)
    {
        output("`&Aber das übersteigt leider deine finanziellen Fähigkeiten!");
    }
    else
    {
        output("`&Und nachdem du deine $cost Goldmünzen auf den Tisch gelegt hast überreicht er dir `^$nmb ".($nmb==1?"funkelnden Edelstein":"funkelnde Edelsteine")."`&.");
        $session['user']['gold']-=$cost;
        $session['user']['gems']+=$nmb;
    }
    addnav("Zurück zum Haus","inside_houses.php");
}
else if ($_GET[act]=="gemss")
{
    $nmb=$_GET[nmb];
    output("`&Du teilst dem Mann mit, dass du gern `^$nmb ".($nmb==1?"Edelstein":"Edelsteine")."`& verkaufen möchtest.`n");
    $cost=600*$nmb;
    if ($session['user']['gems']<$nmb)
    {
        output("`&Aber leider hast du nicht genug Edelsteine dabei!");
    }
    else
    {
        output("`&Und nachdem du ihm $nmb ".($nmb==1?"Edelstein":"Edelsteine")." auf den Tisch gelegt hast überreicht er dir `^$cost Goldmünzen`&.");
        $session['user']['gold']+=$cost;
        $session['user']['gems']-=$nmb;
    }
    addnav("Zurück zum Haus","inside_houses.php");
}
else if ($_GET[act]=="trainanimal" || $_GET[act]=="trainanimal2")
{
    
	// Mount neu laden
	getmount($session['user']['hashorse'],true);
	
    $sql = "SELECT mountextrarounds FROM account_extra_info WHERE acctid=".$session[user][acctid]."";
    $result = db_query($sql) or die(db_error(LINK));
    $rowm = db_fetch_assoc($result);
    
	$float_factor = max($playermount['trainingcost'],1);
	
	// exponentielle Steigerung
	$cost = round ( pow($float_factor,$rowm['mountextrarounds']) );
	// Bei 120 abriegeln ; )
	$cost = min($cost,120);
	  		    
    if ($_GET[act]=="trainanimal")
    {
        output("`&Die Tiertrainerin verlangt `^".$cost." Edelsteine`& für eine Stunde Ausdauertraining mit {$playermount['mountname']}.`n`&Auch weist sie dich darauf hin, dass dein Tier danach sehr erschöpft und zu nichts mehr zu gebrauchen sein wird. Willst du es dennoch dem harten Training unterziehen ?");
        addnav("Ja","housefeats.php?act=trainanimal2");
        addnav("Nein","inside_houses.php");
    }
    else if ($_GET[act]=="trainanimal2")
    {
        $buff = unserialize($playermount['mountbuff']);
        if ($session['bufflist']['mount']['rounds'] < $buff['rounds'])
        {
            output("Die junge Frau schaut dein Tierchen mitleidig an.`n`6Tut mir leid, aber in dem Zustand wird es mir nach 5 Minuten zusammenbrechen.`nSorge bitte dafür dass dein Tier gut erholt und gefüttert ist bevor du es zu mir bringst!`&`nMit diesen Worten wendet sie sich ab.");
            addnav("Zurück zum Haus","inside_houses.php");
        }
        else if ($session['user']['gems']<$cost)
        {
            output("`&Peinlich berührt stellst du fest, dass du dir diesen Luxus nicht leisten kannst...");
            addnav("Zurück zum Haus","inside_houses.php");
        }
        else
        {
            output("`&Die Trainerin nimmt {$playermount['mountname']} mit und verschwindet. Nach einer Stunde erhälst du dein Tier zurück.`n`^Es wird nun täglich eine Runde länger an deiner Seite kämpfen!`&");
            $session['bufflist']['mount']['rounds'] = 0;
            $session['user']['gems']-=$cost;
            
            $newrounds=$rowm['mountextrarounds']+1;
            
            $sql = "UPDATE account_extra_info SET mountextrarounds=$newrounds WHERE acctid = ".$session[user][acctid];
            db_query($sql) or die(sql_error($sql));
            
            addnav("Zurück zum Haus","inside_houses.php");
        }
    }
    
}
else if ($_GET[act]=="workhard")
{
    output("`&Du trittst dem Gutshofverwalter entgegen und erklärst ihm, dass du kräftige Hände hast und gern bei der Arbeit mithelfen würdest.");
    if ($session['user']['turns']<1)
    {
        output("`&Dieser grinst dich an und sagt : \"`tNeee, lass mal lieber. Du siehst schon ziemlich müde aus!`&\"");
        addnav("Recht hat er...","inside_houses.php");
    }
    else
    {
        output("`&Dieser mustert dich erfreut und sagt : \"`tGut gut gut... Arbeit gibts hier immer zu Genüge. Such dir aus, was dir gefällt.`nUnd ganz umsonst wirst du auch nicht arbeiten. Ich zahle dir einen fairen Anteil vom Gewinn aus... sagen wir 100 Goldmünzen pro Runde, die du hier schuftest... Abgemacht?`n`n`n");
        output("`&Wieviele Runden möchtest du hart arbeiten ?");
        
        output("<form action='housefeats.php?act=workhard2' method='POST'><input name='trai' id='trai'><input type='submit' class='button' value='Runden arbeiten'></form>",true);
        output("<script language='JavaScript'>document.getElementById('trai').focus();</script>",true);
        addnav("","housefeats.php?act=workhard2");
        
        addnav("Vergiss es!","inside_houses.php");
    }
}
else if ($_GET[act]=="workhard2")
{
    $trai = abs((int)$_GET[trai] + (int)$_POST[trai]);
    if ($session[user][turns] <= $trai)
    {
        $trai = $session[user][turns];
    }
    $session[user][turns]-=$trai;
    $reward=$trai*100;
    $session['user']['gold']+=$reward;
    output("`&Du rackerst für $trai Runden und erhälst deinen gerechten Lohn von `^$reward`& Gold.");
    addnav("Zurück","inside_houses.php");
}
else if ($_GET[act]=="givepower")
{
    output("`&Du kniest dich vor den Ahnenschrein und versinkst in stiller Meditation.`nSchon bald siehst du Gesichter vor deinem geistigen Auge, die nach Erlösung schreien. Du blickst ins Totenreich und kannst viele dir bekannte Krieger sehen.`n`n`n");
    
    if ($_GET[who]=="")
    {
        output("`&Nach wem möchtest du suchen?`n`&");
        if ($_GET['subop']!="search")
        {
            output("<form action='housefeats.php?act=givepower&subop=search' method='POST'><input name='name'><input type='submit' class='button' value='Suchen'></form>",true);
            addnav("","housefeats.php?act=givepower&subop=search");
        }
        else
        {
            addnav("Neue Suche","housefeats.php?act=givepower");
            $search = "%";
            for ($i=0; $i<strlen($_POST['name']); $i++)
            {
                $search.=substr($_POST['name'],$i,1)."%";
            }
            $sql = "SELECT name,alive,loggedin,login,deathpower,dpower FROM accounts LEFT JOIN account_extra_info USING(acctid) WHERE (name LIKE '$search' and alive=0)ORDER BY deathpower DESC";
            //output($sql);
            $result = db_query($sql) or die(db_error(LINK));
            $max = db_num_rows($result);
            
            output("<table border=0 cellpadding=0><tr><td>Name</td><td>Status</td><td>Gefallen</td></tr>",true);
            for ($i=0; $i<$max; $i++)
            {
                $row = db_fetch_assoc($result);
                output("<tr><td><a href='housefeats.php?act=givepower&who=".rawurlencode($row[login])."'>$row[name]</a></td><td>",true);
                if ($row['loggedin'])
                {
                    output("`@online`&</td><td>",true);
                }
                else
                {
                    output("`4offline`&</td><td>",true);
                }
                output("".$row['deathpower']."`@+".$row['dpower']."</td></tr>",true);
                addnav("","housefeats.php?act=givepower&who=".rawurlencode($row[login]));
            }
            output("</table>",true);
        }
    }
    else
    {
        $sql = "SELECT accounts.acctid,name,login,deathpower,dpower FROM accounts LEFT JOIN account_extra_info USING(acctid) WHERE login=\"$_GET[who]\"";
        $result = db_query($sql) or die(db_error(LINK));
        $row = db_fetch_assoc($result);
        $powers=$row['deathpower']+$row['dpower'];
        output("`&Du erblickst {$row['name']} `&unter den Toten.`n{$row['name']} `&hat insgesamt `^{$powers} Gefallen`&.`nWieviel deiner Gefallen möchtest du bei einer Rate von 2:1 übertragen ?`n`n`&Du hast derzeit `^".$session['user']['deathpower']." Gefallen`& bei Ramius.`n`n`n");
        output("<form action='housefeats.php?act=givepower2&id=".$row[acctid]."&dp=".$row[deathpower]."&who=".rawurlencode($row[login])."' method='POST'><input name='trai' id='trai'><input type='submit' class='button' value='Gefallen übertragen'></form>",true);
        output("<script language='JavaScript'>document.getElementById('trai').focus();</script>",true);
        addnav("","housefeats.php?act=givepower2&id=".$row[acctid]."&dp=".$row[deathpower]."&who=".rawurlencode($row[login])."");
    }
    addnav("Zurück","inside_houses.php");
}
else if ($_GET[act]=="givepower2")
{
    $powers = abs((int)$_GET[trai] + (int)$_POST[trai]);
    if ($session['user']['deathpower'] < $powers)
    {
        $powers = $session['user']['deathpower'];
    }
    $got = (int)($powers/2);
    if ($got>0)
    {
    $powers = $got*2;
    $who=$_GET[who];
    $dp=$_GET[dp];
    $id=$_GET[id];
    output("`&Du opferst $powers Gefallen und ".$who."`& erhält $got Gefallen dazu.");
    $session['user']['deathpower']-=$powers;
    
    $sql = "UPDATE account_extra_info SET dpower=dpower+$got WHERE acctid = ".$id;
    db_query($sql) or die(sql_error($sql));
    systemmail($id,"`^Gefallen erhalten!`0","`&{$session['user']['name']}
    `6 hat am Ahnenschrein meditiert und dabei {$got}
    Gefallen bei Ramius für dich hinterlassen. Du solltest dich bei Gelegenheit erkenntlich zeigen.
    `nVergiss nicht dir die Gefallen am Ahnenschrein des Totenreichs abzuholen, da sie morgen sonst verloren sind.");
	
	debuglog('hat '.$got.' Gefallen übertragen auf ',$id);
    }
    else
    {
    output("`&Du solltest schon etwas haben, bevor du ans opfern denkst.");
    }
    addnav("Zurück","inside_houses.php");
}
else if ($_GET[act]=="suicide")
{
    output("`&Du näherst dich langsam und vorsichtig dem Opferschrein, wo du dich selbst ins Totenreich zu befördern gedenkst.`n");
    if ($session['user']['turns']<1)
    {
        output("`&Doch leider kleben immer noch die Überreste des letzten Benutzers auf dem Opferschrein und bist weder bereit das Ding zu schrubben, noch dein Ableben auf diese Art zu gestalten.`n");
        addnav("Umkehren","inside_houses.php");
    }
    else
    {
        Output("`&Jetzt ist es deine Wahl ob du leben oder sterben willst...`n");
        addnav("Selbst opfern","housefeats.php?act=suicide2");
        addnav("Leben!","inside_houses.php");
    }
}
else if ($_GET[act]=="suicide2")
{
    output("`&Du nimmst den gezackten Opferdolch und stichst auf dich ein.`n");
    switch (e_rand(1,5))
    {
    case 1 :
    case 2 :
    case 3 :
        output("`&Als es um dich herum schwarz wird driftet deine Seele langsam ins Totenreich hinab.`n`4Du bist tot, so wie du es wolltest!`&");
        $session['user']['hitpoints']=0;
        $session['user']['alive']=0;
        $session['user']['turns']-=1;
        addnews("`@".$session['user']['name']."`& hat sich an einem Opferschrein selbst getötet!");
        addnav("Weiter","shades.php");
        break;
    case 4 :
    case 5 :
    case 6 :
    case 7 :
        output("`&Als es um dich herum schwarz wird driftet deine Seele langsam ins Totenreich hinab.`n`4Du wusstest, dass Selbstmord eine extrem feige Art ist vor seinen Problemen zu flüchten, und so sieht das auch Ramius. Dein Ruf sinkt kräftig!`nDu bist tot!");
        $session['user']['reputation']=-40;
        $session['user']['hitpoints']=0;
        $session['user']['alive']=0;
        $session['user']['turns']-=1;
        addnews("`@".$session['user']['name']."`& nahm sich feige das Leben an einem Opferschrein!");
        addnav("Weiter","shades.php");
        break;
    case 8 :
        output("`@Zu dumm, dass es in solchen Situationen ab und an auch mal einen Retter gibt.`n`n`&Du wirst geheilt, aber zu deinem eigenen Schutz für den Rest des Tages in den Kerker geworfen.");
        $session['user']['turns']-=1;
        $session['user']['imprisonded']=1;
        addnews("`@".$session['user']['name']."`& hat versucht sich selbst zu töten, wurde aber gerettet und zum eigenen Schutz in den Kerker verbracht.");
        addnav("Waaaaas?!","prison.php");
        break;
    case 9 :
        output("`&Irgendwie gelingt dir das mit deinen Gegnern aber besser...`n`4Du überlebst schwer verletzt.`n");
        $session['user']['hitpoints']=1;
        $session['user']['turns']-=1;
        addnav("Narf!","inside_houses.php");
        break;
    }
}
else if ($_GET[act]=="exchange")
{
    output("`&Der Oberaufseher führt genau Buch über die Häftlinge, die hier einsitzen.`nEr gibt dir aber deutlich zu verstehen, dass es ihm eigentlich recht egal ist wer in den Zellen sitzt, solange die Zahl der Kerkerinsassen stimmt und du ihm `^1 Edelstein`& gibst.`n`nDu überlegst eine Weile und wirfst einen Blick auf das Gefangenenbuch`n`n");
    
    $sql = "SELECT acctid,name,race,imprisoned,login,sex,level,laston,loggedin,activated FROM accounts WHERE imprisoned!=0 ORDER BY level DESC, dragonkills DESC, login ASC";
    $result = db_query($sql) or die(db_error(LINK));
    output("<table border=0 cellpadding=2 cellspacing=1 bgcolor='#999999'>",true);
    output("<tr class='trhead'><td><b>Level</b></td><td><b>Name</b></td><td><b>Rasse</b></td><td><b><img src=\"images/female.gif\">/<img src=\"images/male.gif\"></b></td><td><b>Status</b></td><td><b>Strafe in Tagen</b></tr>",true);
    $max = db_num_rows($result);
    
    for ($i=0; $i<$max; $i++)
    {
        $row = db_fetch_assoc($result);
        output("<tr class='".($i%2?"trdark":"trlight")."'><td>",true);
        output("`^$row[level]`0");
        output("</td><td>",true);
        if ($session[user][loggedin])
        {
            output("<a href=\"mail.php?op=write&to=".rawurlencode($row['login'])."\" target=\"_blank\" onClick=\"".popup("mail.php?op=write&to=".rawurlencode($row['login'])."").";return false;\"><img src='images/newscroll.GIF' width='16' height='16' alt='Mail schreiben' border='0'></a>",true);
        }
        if ($session[user][loggedin])
        {
            output("<a href='housefeats.php?act=exchange2&char=".$row['acctid']."'>",true);
        }
        if ($session[user][loggedin])
        {
            addnav("","housefeats.php?act=exchange2&char=".$row['acctid']."");
        }
        output("`".($row[acctid]==getsetting("hasegg",0)?"^":"&")."$row[name]`0");
        if ($session[user][loggedin])
        {
            output("</a>",true);
        }
        output("</td><td>",true);
        output($colraces[$row['race']]);
        output("</td><td align=\"center\">",true);
        output($row[sex]?"<img src=\"images/female.gif\">":"<img src=\"images/male.gif\">",true);
        output("</td><td>",true);
        $loggedin=user_get_online(0,$row);
        output($loggedin?"`#Wach`0":"`3Schläft`0");
        output("</td><td>",true);
        if ($row['imprisoned']>0)
        {
            output($row['imprisoned']);
        }
        else
        {
            output("unbestimmt");
        }
        output("</td></tr>",true);
        
    }
    output("</table>",true);
    addnav("Zurück zum Haus","inside_houses.php");
    
}
else if ($_GET[act]=="exchange2")
{
    $sql = "SELECT xchangedtoday FROM account_extra_info WHERE acctid=".$session[user][acctid];
    $result = db_query($sql) or die(db_error(LINK));
    $rowx=db_fetch_assoc($result);
    if ($rowx['xchangedtoday']>0)
    {
        output("`&Der Oberaufseher kneift die Augen zusammen und fährt dich harsch an:`n`4\"Dich habe ich doch heute schonmal hier gesehen! Scher dich bloss fort!\"`n`&");
        addnav("Äh ja","inside_houses.php");
    }
    else
    {
        if ($session['user']['gems']<1)
        {
            output("`&Leider kannst du dir es nicht leisten den Oberaufseher zu bestechen.`n");
            addnav("Zurück zum Haus","inside_houses.php");
        }
        else
        {
            $result = db_query("SELECT name,acctid,level,imprisoned FROM accounts WHERE acctid=$_GET[char]");
            $row = db_fetch_assoc($result);
            $lockup=getsetting("locksentence",4);
            
            if (($row['imprisoned']>0) && ($row['imprisoned']<$lockup))
            {
                output("`&".$row['name']."`& muss noch für `^".$row['imprisoned']." Tage`& im Kerker bleiben.`nDu überlegst dich in die Zelle zu schleichen und für ".$row['name']." `& den Rest der Haftstrafe abzusitzen.`nWillst du das ?");
                addnav("Ja","housefeats.php?act=exchange3&days=$row[imprisoned]$&char=$row[acctid]");
                addnav("Bin ich denn blöd ?","inside_houses.php");
            }
            else if (($row['imprisoned']<0) || ($row['imprisoned']>=$lockup))
            {
                output("".($row['name'])."`4 befindet sich im Hochsicherheitstrakt des Kerkers. Hier kannst du nichts tun.`&`n`n");
                addnav("Zurück zum Haus","inside_houses.php");
            }
        }
    }
}
else if ($_GET[act]=="exchange3")
{
    $result = db_query("SELECT name,acctid,imprisoned,lastip,emailaddress FROM accounts WHERE acctid=$_GET[char]");
    $row = db_fetch_assoc($result);
    $days=$_GET[days];
    
    $sql = "SELECT xchangedtoday FROM account_extra_info WHERE acctid=".$row[acctid];
    $result = db_query($sql) or die(db_error(LINK));
    $rowx=db_fetch_assoc($result);
    if ($rowx['xchangedtoday']>0)
    {
        output("`&Der Oberaufseher schüttelt nur belustig den Kopf`n`4\"Was ist das hier für ein Spiel?Meinst du das fällt niemandem auf wenn immer die selben plötzlich erscheinen oder verschwinden ?\"`n`&");
        addnav("Na dann","inside_houses.php");
    }
    else
    {
        if ($session['user']['lastip'] == $row['lastip'] || ($session['user']['emailaddress'] == $row['emailaddress'] && $row['emailaddress']))
        {
            output("`&Dir ist die Interaktionen mit diesem Charakter untersagt!");
            addnav("Hoppla...","inside_houses.php");
        }
        else
        {
            
            output("`&Du öffnest ".($row['name'])."`& die Zellentüre und schlüpfst selbst hinein.");
            $session['user']['gems']--;
            $session['user']['imprisoned']=$days;
            
            // Beide als für heute ausgetauscht markieren
            $sql = "UPDATE account_extra_info SET xchangedtoday=1 WHERE acctid=".$session[user][acctid]." or acctid=".$row[acctid]."";
            db_query($sql);
            
            $sql = "UPDATE accounts SET imprisoned=0,location=0 WHERE acctid = ".$row['acctid']."";
            db_query($sql) or die(sql_error($sql));
            systemmail($row['acctid'],"`^Gefangenenaustausch!`0","`@{$session['user']['name']}
            `& hat sich bereit erklärt deine Haftstrafe für dich abzusitzen. Du bist frei! Du solltest dich dankbar erweisen!");
            $sql="INSERT INTO commentary (postdate,section,author,comment) VALUES (now(),'prison',".$row[acctid].",'/me `tverschwindet auf unerklärliche Weise.')";
            debuglog("Übernimmt die Haftstrafe von", $row['acctid']);
            db_query($sql) or die(db_error(LINK));
            addnav("Weiter","prison.php");
        }
    }
}
else if ($_GET[act]=="arena")
{
    $sql = "SELECT cage_action FROM account_extra_info WHERE acctid=".$session['user']['acctid'];
    $result = db_query($sql) or die(db_error(LINK));
    $rowx=db_fetch_assoc($result);
    if ($rowx['cage_action']>=1)
    {
        output("`&Grinsend näherst du dich den Zellen, um wieder einen der armen Gefangenen mit in den Keller zu schleppen.`nDoch der Oberaufsehen blickt dich nur an und schüttelt den Kopf.`n Vielleicht reicht es für heute mal..?`n");
        addnav("Bis Morgen!!","inside_houses.php");
    }
    else
    {
        output("`&Du steigst die Treppen des Kerkers hinab, bis tief in die Keller. Irgendwann glaubst du laute Stimmen zu hören, und als du näher kommst erblickst du mehrere Wächter, die sich um einen riesigen Käfig versammelt haben, in dem sie die armen Häftlinge zu blutigen Kämpfen zwingen.`nAls du dich entsetzt abwenden willst hörst du einen der Wächter rufen : \"`@Ja! Gewonnen! Das Gold ist mein!`&\"`n\"`^Soso...`&\",denkst du dir,\"`^Ein Wettbüro? Das macht die Sache schon wieder ganz anders!`&\"`n`n`&Du überlegst auch eine Wette zu wagen.");
        addnav("Wetten","housefeats.php?act=arena2");
        addnav("Nichts für mich","inside_houses.php");
    }
}
else if ($_GET[act]=="arena2")
{
    output("`&Wähle deinen Kämpfer aus der Liste der Häftlinge:`n`n");
    
    $sql = "SELECT accounts.acctid,name,race,imprisoned,dragonkills,login,sex,laston,loggedin,activated,account_extra_info.abused FROM accounts LEFT JOIN account_extra_info ON account_extra_info.acctid=accounts.acctid WHERE imprisoned!=0 ORDER BY dragonkills DESC, login ASC";
    $result = db_query($sql) or die(db_error(LINK));
    output("<table border=0 cellpadding=2 cellspacing=1 bgcolor='#999999'>",true);
    output("<tr class='trhead'><td><b>Drachenkills</b></td><td><b>Name</b></td><td><b>Rasse</b></td><td><b><img src=\"images/female.gif\">/<img src=\"images/male.gif\"></b></td><td><b>Status</b></td><td><b>Zustand</b></td><td><b>Strafe in Tagen</b></tr>",true);
    $max = db_num_rows($result);
    for ($i=0; $i<$max; $i++)
    {
        $row = db_fetch_assoc($result);
        output("<tr class='".($i%2?"trdark":"trlight")."'><td>",true);
        output("`^".$row['dragonkills']."`0");
        output("</td><td>",true);
        if ($session[user][loggedin])
        {
            output("<a href=\"mail.php?op=write&to=".rawurlencode($row['login'])."\" target=\"_blank\" onClick=\"".popup("mail.php?op=write&to=".rawurlencode($row['login'])."").";return false;\"><img src='images/newscroll.GIF' width='16' height='16' alt='Mail schreiben' border='0'></a>",true);
        }
        if ($session[user][loggedin])
        {
            output("<a href='housefeats.php?act=arena3&char=".rawurlencode($row['login'])."'>",true);
        }
        if ($session[user][loggedin])
        {
            addnav("","housefeats.php?act=arena3&char=".rawurlencode($row['login'])."");
        }
        output("`".($row[acctid]==getsetting("hasegg",0)?"^":"&")."$row[name]`0");
        if ($session[user][loggedin])
        {
            output("</a>",true);
        }
        output("</td><td>",true);
        output($colraces[$row['race']]);
        output("</td><td align=\"center\">",true);
        output($row[sex]?"<img src=\"images/female.gif\">":"<img src=\"images/male.gif\">",true);
        output("</td><td>",true);
        $loggedin=user_get_online(0,$row);
        output($loggedin?"`#Wach`0":"`3Schläft`0");
        output("</td><td>",true);
        if ($row[abused]==0)
        {
            output("`@Ok`0");
        }
        else if ($row[abused]==1)
        {
            output("`4gequält`0");
        }
        else if ($row[abused]==2)
        {
            output("`4abgekämpft`0");
        }
        output("</td><td>",true);
        
        if ($row['imprisoned']>0)
        {
            output($row['imprisoned']);
        }
        else
        {
            output("unbestimmt");
        }
        output("</td></tr>",true);
        
    }
    output("</table>",true);
    addnav("Zurück zum Haus","inside_houses.php");
}
else if ($_GET[act]=="arena3")
{
    
    $result = db_query("SELECT name,accounts.acctid,dragonkills,sex,imprisoned,account_extra_info.abused FROM accounts LEFT JOIN account_extra_info ON account_extra_info.acctid=accounts.acctid WHERE login='$_GET[char]'");
    $row = db_fetch_assoc($result);
    
    if ($row['abused']==0)
    {
        
        output("`7Du zerrst ".($row['name'])."`7 grob aus der Zelle und bringst ".($row[sex]?"sie":"ihn")." runter in den Keller. Suche dir nun einen Gegner für ".($row['name'])." aus.`n`n ");
        
        output("<table border=0 cellpadding=2 cellspacing=1 bgcolor='#999999'>",true);
        output("<tr class='trhead'><td><b>Gegner</b></td><td><b>Gefahr</b></td><td><b>Chance</b></td><td><b>Quote</b></td></tr>",true);
        
        for ($i=1; $i<11; $i++)
        {
            
            $chance=(60-10*$i)+$row['dragonkills'];
            if ($chance<10)
            {
                $chance=10;
            }
            if ($chance>90)
            {
                $chance=90;
            }
            $quote=5.5-($chance/20);
            
            output("<tr class='".($i%2?"trdark":"trlight")."'><td>",true);
            
            if ($session[user][loggedin])
            {
                output("<a href='housefeats.php?act=arena4&opp=$i&chance=$chance&char=".$row['acctid']."'>",true);
            }
            if ($session[user][loggedin])
            {
                addnav("","housefeats.php?act=arena4&opp=$i&chance=$chance&char=".$row['acctid']."");
            }
            output(get_opponent($i));

            if ($session[user][loggedin])
            {
                output("</a>",true);
            }
            output("</td><td>",true);
            output($i);
            output("</td><td align=\"center\">",true);
            output("`&$chance %`0");
            output("</td><td>",true);
            output("`@$quote`&");
            output("</td></tr>",true);
        }
        
        
        addnav("Zurück zum Haus","inside_houses.php");
        
    }
    else
    {
        output("`&".($session['user']['sex']?"Diese Kämperin":"Dieser Kämpfer")." kommt leider heute für einen Käfigkampf nicht mehr in Frage.`nBei ".($session['user']['sex']?"ihrem":"seinem")." jetzigen Zustand wäre dein Gold sicher verloren.`n`nWillst du dir nicht jemand Anderes stattdessen aussuchen ?");
        addnav("Ja","housefeats.php?act=arena2");
        addnav("Nein, zurück zum Haus","inside_houses.php");
    }
}
else if ($_GET[act]=="arena4")
{
    $result = db_query("SELECT login,name,accounts.acctid,dragonkills,sex,imprisoned,account_extra_info.abused FROM accounts LEFT JOIN account_extra_info ON account_extra_info.acctid=accounts.acctid WHERE accounts.acctid='$_GET[char]'");
    $row = db_fetch_assoc($result);
    
    
    $chance=$_GET[chance];
    $quote=5.5-($chance/20);
    output("`&Willst du ".$row['name']." `&wirklich im Käfig gegen ".(get_opponent($_GET[opp]))." `&antreten lassen?`n Die Siegeserwartung wird auf `@$chance %`& geschätzt, die Quote liegt bei `^$quote`&.`nWenn du wetten möchtest, wähle nun deinen Einsatz.");
    
    addnav("100 Gold setzen","housefeats.php?act=arena5&opp=$_GET[opp]&chance=$chance&set=100&char=".$row['acctid']."");
    addnav("200 Gold setzen","housefeats.php?act=arena5&opp=$_GET[opp]&chance=$chance&set=200&char=".$row['acctid']."");
    addnav("500 Gold setzen","housefeats.php?act=arena5&opp=$_GET[opp]&chance=$chance&set=500&char=".$row['acctid']."");
    addnav("1000 Gold setzen","housefeats.php?act=arena5&opp=$_GET[opp]&chance=$chance&set=1000&char=".$row['acctid']."");
    addnav("Anderer Gegner","housefeats.php?act=arena3&char=".$row['login']."");
    addnav("Anderer Gefangener","housefeats.php?act=arena2");
    addnav("Zurück zum Haus","inside_houses.php");
    
}
else if ($_GET[act]=="arena5")
{
    $set=$_GET['set'];
    
    if ($session['user']['gold']>=$set)
    {
        $session['user']['gold']-=$set;
        $chance=$_GET['chance'];
        $opp=$_GET['opp'];
        $quote=5.5-($chance/20);
        
        $result = db_query("SELECT login,name,accounts.acctid,dragonkills,sex,imprisoned,account_extra_info.abused FROM accounts LEFT JOIN account_extra_info ON account_extra_info.acctid=accounts.acctid WHERE accounts.acctid='$_GET[char]'");
        $row = db_fetch_assoc($result);
        $sql = "UPDATE account_extra_info SET abused=2 WHERE acctid = ".$row['acctid'];
        db_query($sql) or die(sql_error($sql));
        
        output("`&".$row['name']." `&beginnt mehr oder weniger freiwillig den Kampf.`n".($session['user']['sex']?"Ihr":"Sein")." Gegner ist ".get_opponent($opp)."`&.`n");
        
        $result=(e_rand(1,100));
        if ($result<=$chance)
        {
            output("`&Dabei schlägt sich ".$row['name']."`& wacker und entscheidet den Kampf für sich!`n`@SIEG! ".($set*$quote)." Gold sind dein!`&");
            $session['user']['gold']+=($set*$quote);
            $mail="`@{$session['user']['name']}
            `& hat dich, während du im Kerker eingesessen hast, in den Keller gezerrt und zu einem blutigen Käfigkampf gezwungen! Dein Gegner war ".get_opponent($opp)."`&. Du hast tapfer gekämpft und gewonnen!`nDennoch war das nicht sehr nett...";
            addnav("Zurück zum Haus","inside_houses.php");
        }
        else
        {
            output("`&Dabei macht ".$row['name']."`& sichtlich keine gute Figur und verliert den Kampf.`n`4NIEDERLAGE! Du hast $set Gold verloren!`& ");
            $mail="`@{$session['user']['name']}
            `& hat dich, während du im Kerker eingesessen hast, in den Keller gezerrt und zu einem blutigen Käfigkampf gezwungen! Dein Gegner war ".get_opponent($opp)."`&. Du wurdest windelweich geprügelt!`nVielleicht tröstet es dich zu erfahren, dass ".$session['user']['name']." `&bei ".($session['user']['sex']?"ihrer":"seiner")." Wette auf deinen Sieg `^$set`& Gold verloren hat...";
            addnav("Zurück zum Haus","inside_houses.php");
        }
        $sql = "UPDATE account_extra_info SET cage_action=cage_action+1 WHERE acctid = ".$session['user']['acctid'];
        db_query($sql) or die(sql_error($sql));
        systemmail($row['acctid'],"`$Käfigkampf!`0",$mail);
    }
    else
    {
        output("`&Zum Wetten braucht man Gold. Und das hast du leider nicht.`nAlso bleibt dir nichts Anderes übrig als dich peinlich berührt davon zu schleichen.");
        addnav("Zurück zum Haus","inside_houses.php");
    }
}
else if ($_GET[act]=="bless")
{
    output("`&Andächtig kniest du vor dem großen, prunkvollen Altar nieder und willst gerade mit deinem Gebet beginnen, als dir ein ebenso großer und prunkvoller Opferstock auffällt.`nNa vielleicht sind die Götter mit ihrem Segen ja etwas spendabler, wenn der Kasten voll ist?`n`n`nWieviel möchtest du spenden ?");
    
    output("<form action='housefeats.php?act=bless2' method='POST'><input name='trai' id='trai'><input type='submit' class='button' value='Gold hergeben'></form>",true);
    output("<script language='JavaScript'>document.getElementById('trai').focus();</script>",true);
    addnav("","housefeats.php?act=bless2");
    
    addnav("Nicht mal einen Knopf!","inside_houses.php");
    
}
else if ($_GET[act]=="bless2")
{
    $donate = abs((int)$_GET[trai] + (int)$_POST[trai]);
    if ($session['user']['gold'] < $donate)
    {
        $donate = $session['user']['gold'];
    }
    
    $tresh=e_rand(($session['user']['dragonkills']+$session['user'][Level])*5,($session['user']['dragonkills']+$session['user']['level'])*200);
    
    if ($tresh>15000)
    {
        $tresh=15000;
    }
    
    if ($donate>=$tresh)
    {
        output("`n`n`&Die Götter meinen es heute gut mit dir.`nDeine Gebete wurden `@erhört`& und sie segnen dich mit `@andauernder Gesundheit`&!");
        
        $session['user']['gold'] -= $donate;
        
        $session[bufflist]['bless'] = array("startmsg"=>"`n`^Der göttliche Segen heilt deine Wunden !`n`n",
        "name"=>"`@Göttlicher Segen",
        "rounds"=>20,
        "wearoff"=>"Der Segen ist vorüber",
        "regen"=>$session['user']['level'],
        "effectmsg"=>"Der göttliche Segen lässt einige deiner Wunden heilen.",
        "effectnodmgmsg"=>"Der Segen schützt dich.",
        "activate"=>"roundstart");
        
    }
    else
    {
        output("`n`n`&Entweder war deine Spende nicht großzügig genug, oder die Götter hegen Groll gegen dich... wie auch immer, deine Gebete wurden `4nicht erhört`&!`n");
    }
    
    addnav("Zurück zum Haus","inside_houses.php");
    
}
else if ($_GET[act]=="disciple")
{
    
    $sql = "SELECT name,state FROM disciples WHERE master=".$session['user']['acctid']."";
    $result = db_query($sql) or die(db_error(LINK));
    
    if (db_num_rows($result)>0)
    {
        $row = db_fetch_assoc($result);
        $name=$row['name'];
        $state=$row['state'];
    }
    
    if (($state==0) || (db_num_rows($result)==0))
    {
        
        output("`&Du blickst dich interessiert nach einem Knappen um, der von nun für dich die Drecksar... äh.. der dich von nun an bei deinen Abenteuern begleiten und von dir lernen soll.`n`n");
        
        if ($session['user']['reputation']>=40)
        {
            if ($session['user']['gems']>=20)
            {
                $state= e_rand(1,14);
                switch ($state)
                {
                case 1 :
                    $adj="junger";
                    break;
                case 2 :
                    $adj="dürrer";
                    break;
                case 3 :
                    $adj="langwüchsiger";
                    break;
                case 4 :
                    $adj="kräftiger";
                    break;
                case 5 :
                    $adj="hübscher";
                    break;
                case 6 :
                    $adj="stolzer";
                    break;
                case 7 :
                    $adj="vorlauter";
                    break;
                case 8 :
                    $adj="verträumter";
                    break;
                case 9 :
                    $adj="neunmalkluger";
                    break;
                case 10 :
                    $adj="dicklicher";
                    break;
                case 11 :
                    $adj="nichtsnutziger";
                    break;
                case 12 :
                    $adj="treuer";
                    break;
                case 13 :
                    $adj="hinterhältiger";
                    break;
                case 14 :
                    $adj="listiger";
                    break;
                }
                
                output("`&Ein $adj Knabe tritt vor dich heran und verneigt sich tief.`n");
                $nam= e_rand(1,100);
                switch ($nam)
                {
                case 1 :
                    $name="Erik";
                    break;
                case 2 :
                    $name="Mark";
                    break;
                case 3 :
                    $name="Siegfried";
                    break;
                case 4 :
                    $name="Ulrich";
                    break;
                case 5 :
                    $name="Dieter";
                    break;
                case 6 :
                    $name="Johannes";
                    break;
                case 7 :
                    $name="Jason";
                    break;
                case 8 :
                    $name="Merik";
                    break;
                case 9 :
                    $name="Hagen";
                    break;
                case 10 :
                    $name="Jöörg";
                    break;
                case 11 :
                    $name="Martin";
                    break;
                case 12 :
                    $name="Benjamin";
                    break;
                case 13 :
                    $name="Friedrich";
                    break;
                case 14 :
                    $name="Jens";
                    break;
                case 15 :
                    $name="Hogan";
                    break;
                case 16 :
                    $name="Alrik";
                    break;
                case 17 :
                    $name="Jakob";
                    break;
                case 18 :
                    $name="Artus";
                    break;
                case 19 :
                    $name="Henry";
                    break;
                case 20 :
                    $name="Jean";
                    break;
                case 21 :
                    $name="Roland";
                    break;
                case 22 :
                    $name="Holger";
                    break;
                case 23 :
                    $name="Lenny";
                    break;
                case 24 :
                    $name="Stefan";
                    break;
                case 25 :
                    $name="Robin";
                    break;
                case 26 :
                    $name="Kevin";
                    break;
                case 27 :
                    $name="Jost";
                    break;
                case 28 :
                    $name="Torben";
                    break;
                case 29 :
                    $name="Mario";
                    break;
                case 30 :
                    $name="Philipp";
                    break;
                case 31 :
                    $name="Maik";
                    break;
                case 32 :
                    $name="Ernst";
                    break;
                case 33 :
                    $name="John";
                    break;
                case 34 :
                    $name="William";
                    break;
                case 35 :
                    $name="Angus";
                    break;
                case 36 :
                    $name="Gottlieb";
                    break;
                case 37 :
                    $name="Bruno";
                    break;
                case 38 :
                    $name="Claudius";
                    break;
                case 39 :
                    $name="Antonius";
                    break;
                case 40 :
                    $name="Leonard";
                    break;
                case 41 :
                    $name="Janus";
                    break;
                case 42 :
                    $name="Raphael";
                    break;
                case 43 :
                    $name="Cedrick";
                    break;
                case 44 :
                    $name="Vincent";
                    break;
                case 45 :
                    $name="Frank";
                    break;
                case 46 :
                    $name="Sam";
                    break;
                case 47 :
                    $name="Gregor";
                    break;
                case 48 :
                    $name="Benedikt";
                    break;
                case 49 :
                    $name="Wilbur";
                    break;
                case 50 :
                    $name="Kenny";
                    break;
                case 51 :
                    $name="Lars";
                    break;
                case 52 :
                    $name="Björn";
                    break;
                case 53 :
                    $name="Hans";
                    break;
                case 54 :
                    $name="Hugo";
                    break;
                case 55 :
                    $name="Christopher";
                    break;
                case 56 :
                    $name="Vladimir";
                    break;
                case 57 :
                    $name="Harry";
                    break;
                case 58 :
                    $name="Hendrik";
                    break;
                case 59 :
                    $name="Simon";
                    break;
                case 60 :
                    $name="Max";
                    break;
                case 61 :
                    $name="Alexander";
                    break;
                case 62 :
                    $name="Olaf";
                    break;
                case 63 :
                    $name="Baltasar";
                    break;
                case 64 :
                    $name="Julius";
                    break;
                case 65 :
                    $name="Justus";
                    break;
                case 66 :
                    $name="Ferdinand";
                    break;
                case 67 :
                    $name="Manuel";
                    break;
                case 68 :
                    $name="Sebastian";
                    break;
                case 69 :
                    $name="Kay";
                    break;
                case 70 :
                    $name="Jan";
                    break;
                case 71 :
                    $name="Peter";
                    break;
                case 72 :
                    $name="Michael";
                    break;
                case 73 :
                    $name="Sinclair";
                    break;
                case 74 :
                    $name="Robert";
                    break;
                case 75 :
                    $name="Till";
                    break;
                case 76 :
                    $name="Jonas";
                    break;
                case 77 :
                    $name="Jim";
                    break;
                case 78 :
                    $name="Bob";
                    break;
                case 79 :
                    $name="Barney";
                    break;
                case 80 :
                    $name="Stuart";
                    break;
                case 81 :
                    $name="Charles";
                    break;
                case 82:
                    $name="Detlev";
                    break;
                case 83 :
                    $name="Hektor";
                    break;
                case 84 :
                    $name="Andre";
                    break;
                case 85 :
                    $name="Josef";
                    break;
                case 86 :
                    $name="Melchior";
                    break;
                case 87 :
                    $name="Viktor";
                    break;
                case 88 :
                    $name="Werner";
                    break;
                case 89 :
                    $name="Fabian";
                    break;
                case 90 :
                    $name="Theo";
                    break;
                case 91 :
                    $name="Carsten";
                    break;
                case 92 :
                    $name="Andreas";
                    break;
                case 93 :
                    $name="Thomas";
                    break;
                case 94 :
                    $name="Mathias";
                    break;
                case 95 :
                    $name="Sören";
                    break;
                case 96 :
                    $name="Wenzel";
                    break;
                case 97 :
                    $name="Ali";
                    break;
                case 98 :
                    $name="Edmund";
                    break;
                case 99 :
                    $name="Boris";
                    break;
                case 100 :
                    $name="Clemens";
                    break;
                }
                
                output("\"`@Mein".($session[user][sex]?"e edle Dame,":" edler Herr,")." mein Name ist $name und ich habe schon viel von Eurer Tapferkeit gehört und möchte Euch gern zur Seite stehen.`&\"`n`nWillst du diesen jungen Recken als deinen Knappen annehmen?`n");
                $name=urlencode($name);
                addnav("Jawohl","housefeats.php?act=disciple2&name=$name&state=$state");
                addnav("Nein!","inside_houses.php");
            }
            else
            {
                output("`&Nur leider will sich keiner mit einem so mittellosen Versager wie dir einlassen!`nBesorg dir erstmal die 20 Edelsteine, dann klappt es vielleicht...");
                addnav("Zurück","inside_houses.php");
            }
        }
        else
        {
            output("`&Doch leider will keiner der Jünglinge das Risiko eingehen von dir verheizt zu werden.`nDu solltest dingend etwas für dein Ansehen tun!");
            addnav("Zurück","inside_houses.php");
        }
    }
    else
    {
        output("`&Und die Träume und Hoffnungen des armen `@$name`& enttäuschen?`nAlso wenn du ihn unbedingt loswerden willst musst du dir das schon etwas kosten lassen...`n`@$name`& braucht `^10 Edelsteine`& wenn er von nun allein allein klar kommen soll.`n`n`4Willst du `@$name`4 wirklich verstossen ?");
        $name=urlencode($name);
        addnav("Ja, 10 Edelsteine zahlen","housefeats.php?act=kickdisciple&name=$name");
        addnav("Nein! Zurück!","inside_houses.php");
    }
    
}
else if ($_GET[act]=="kickdisciple")
{
    $name=urldecode($_GET[name]);
    
    if ($session['user']['gems']<10)
    {
        output("`&Tja, das hättest du wohl gern... Du es dir nicht leisten `@$name`& einen angemessenen Start in seine Zukunft zu ermöglichen und so wirst du ihn noch eine weitere Weile an der Backe kleben haben...`n`nUnd versuch nicht ihn andersweitig \"loszuwerden\", der Orden hat ein Auge auf dich...");
        addnav("Zurück","inside_houses.php");
    }
    else
    {
        $session['user']['gems']-=10;
        output("`&So sei es! `@$name`& nimmt mit Tränen in den Augen die Edelsteine an und sucht nun allein in der Welt sein Glück.");
        $session['user']['reputation']-=20;
        
        $sql = "UPDATE disciples SET state=0 WHERE master = ".$session['user']['acctid'];
        db_query($sql) or die(sql_error($sql));
        unset($session['bufflist']['decbuff']);
        addnav("Machs gut!","inside_houses.php");
    }
    
}
else if ($_GET[act]=="disciple2")
{
    $session['user']['gems']-=20;
    $name=urldecode($_GET[name]);
    $state=$_GET[state];
    
    output("`&Du spendest deine 20 Edelsteine an den Orden und nimmst $name in deine Dienste auf.`n`n`@Du hast jetzt einen Knappen, der dich durch dick und dünn begleiten wird. Aber gib gut auf ihn acht, da er nach einer Niederlage im Kampf verschleppt werden könnte!`&");
    
    $sql = "SELECT state FROM disciples WHERE master=".$session['user']['acctid']."";
    $result = db_query($sql) or die(db_error(LINK));
    
    if (db_num_rows($result)==0)
    {
        
        $sql = "INSERT INTO disciples (name,state,level,master) VALUES ('".$name."',$state,'0','".$session['user']['acctid']."')";
        db_query($sql) or die(sql_error($sql));
    }
    else
    {
        $sql = "UPDATE disciples SET name='".$name."',state=$state,level='0',best_one='0' WHERE master = ".$session['user']['acctid'];
        db_query($sql) or die(sql_error($sql));
    }
    
    $session['bufflist']['decbuff'] = set_disciple($state);
    addnav("Zurück zum Haus","inside_houses.php");
    
}
else if ($_GET[act]=="mastertrain")
{
    $sql = "SELECT mastertrain FROM account_extra_info WHERE acctid=".$session['user']['acctid']."";
    $result = db_query($sql) or die(db_error(LINK));
    $rowt = db_fetch_assoc($result);
    if ($rowt['mastertrain']<5)
    {
        $cost=($session['user']['level']*750);
        if ($session['user']['gold']<$cost)
        {
            output("`&Die Meister hießen sicherlich nicht Meister, wenn ihre Ausbildung nicht das Höchste der Kampfeskunst darstellen würde, die es gibt.`nNatürlich hat das auch seinen Preis, und in deinem Fall liegt dieser wohl ein wenig über deinen Fähigkeiten...");
        }
        else
        {
            $session['user']['gold']-=$cost;
            output("`&Die Meister geben dir wenig Zeit zum Eingewöhnen, sondern fangen gleich mit ihrem Training an.`nNach mehreren Stunden schweißtreibender Arbeit und dutzenden blauen Flecken stellst du fest, ");
            
            $chance=e_rand(1,5);
            if ($session['user']['age']>50)
            {
                $chance=5;
            }
            
            switch ($chance)
            {
            case 1 :
            case 2 :
                output("`&dass du in der Offensive stärker geworden bist.`n`@Dein Angriff steigt um 1 Punkt.`&`n");
                $session['user']['attack']+=1;
                break;
            case 3 :
            case 4 :
                output("`&dass du in der Defensive stärker geworden bist.`n`@Deine Verteidigung steigt um 1 Punkt.`&`n");
                $session['user']['defence']+=1;
                break;
            case 5 :
                output("`&dass die ganze Schinderei vergebens war.`nDu konntest dir keine neuen Fertigkeiten aneignen.`n");
                break;
            }
            $sql = "UPDATE account_extra_info SET mastertrain=mastertrain+1 WHERE acctid = ".$session['user']['acctid'];
            db_query($sql) or die(sql_error($sql));
            
            output("`&`nDie Strapazen und Belastungen des harten Trainings sind an dir nicht spurlos vörüber gegangen : `4Du alterst körperlich um 1 Tag!`&`n");
            $session['user']['hitpoints']*=0.5;
            $session['user']['age']+=1;
        }
    }
    else
    {
        output("`&Hier kannst du keine neuen Fertigkeiten mehr erlangen. Die Meister haben dich genug geschunden und dir alles vermittelt was sie können.");
    }
    addnav("Zurück zum Haus","inside_houses.php");
    
}
else if ($_GET[act]=="smith")
{
    output("`&Hier in der Schmiede des Söldnerlagers arbeiten qualifizierte Kräfte Tag und Nach um die Waffen ihrer Kumpanen noch heimtückischer und ihre Rüstungen noch stabiler zu machen.`nFür einen relativ geringen Preis von `^3 Edelsteinen`& kannst auch du ihre Dienste in Anspruch nehmen.`n" );
    addnav("Waffe verbessern","housefeats.php?act=smith2&what=weapon");
    addnav("Rüstung verbessern","housefeats.php?act=smith2&what=armor");
    addnav("Zurück zum Haus","inside_houses.php");
    
}
else if ($_GET[act]=="smith2")
{
    if ($session['user']['gems']<3)
    {
        output("`&Schäm dich, das kannst du dir doch gar nicht leisten!");
        addnav("Zurück zum Haus","inside_houses.php");
    }
    else
    {
        output("`&Du übergibst ".$session['user'][$_GET['what']]." an die Schmiede, ");
        
        if (strpos($session['user'][$_GET['what']],"+"))
        {
            output("`&doch sie können nichts weiter tun, da bereits daran geabeitet wurde.`nEnttäuscht wendest du dich ab.");
            addnav("Zurück zum Haus","inside_houses.php");
        }
        else
        {
            output("`&welche sich sofort fleißig an die Arbeit machen.`n`n`^".$session['user'][$_GET['what']]."`^ wurde verbessert!`&");
            if (strpos($session['user'][$_GET['what']]," -1"))
            {
                
                $name = $session['user'][$_GET['what']]." +1";
                $skill = $session['user'][$_GET['what'].($_GET['what']=="weapon"?"dmg":"def")] + 1;
                $val = $session['user'][$_GET['what']."value"] * 1.1;
                
            }
            else
            {
                $name = $session['user'][$_GET['what']]." +2";
                $skill = $session['user'][$_GET['what'].($_GET['what']=="weapon"?"dmg":"def")] + 2;
                $val = $session['user'][$_GET['what']."value"] * 1.2;
            }
            
            $func_name = 'item_set_'.$_GET['what'];
            $func_name($name, $skill, $val, 0, 0, 1);
            
            $session['user']['gems']-=3;
            addnav("Zurück zum Haus","inside_houses.php");
        }
    }
    
}
else if ($_GET[act]=="orgy")
{
    if ($session['user']['gold']<3000)
    {
        output("`&Bei deiner jetzigen finanziellen Situation sind die Einzigen, die eine Orgie mit dir haben wollen die Ratten im Keller!`nAlso besorg schnell mal etwas Gold, sonst läuft hier gar nichts...");
        addnav("Zurück zum Haus","inside_houses.php");
    }
    else
    {
        
        if ($session['user']['spirits']!=(-6))
        {
            $session['user']['gold']-=3000;
            output("`&Du tobst dich nach Herzenslust aus und hast jede Menge Spass.`n");
            $bonus=2-$session['user']['spirits'];
            if ($bonus==0)
            {
                output("`&Doch da deine Stimmung bereits sehr gut ist bringt dir das keinen weiteren Vorteil...`n");
                addnav("Zurück zum Haus","inside_houses.php");
            }
            else
            {
                output("`@Du amüsierst dich so prächtig, dass deine Stimmung `^sehr gut`@ wird. Dadurch erhälst du `^$bonus`@ zusätzliche Waldkämpfe!");
                $session['user']['spirits']=2;
                $session['user']['turns']+=$bonus;
                addnav("Yeah!","inside_houses.php");
            }
        }
        else
        {
            output("`&Da du gerade von den Toten auferstanden bist und du immer noch diesen fauligen Geschmack im Mund verspürst, steht dir überhaupt nicht der Sinn nach so etwas.");
            addnav("Zurück zum Haus","inside_houses.php");
        }
    }
    
    
}
else if ($_GET[act]=="beater")

{
    if ($session['user']['reputation']>0)
    {
        Output("`&Du suchst die Nähe der übel und finster aussehendsten Gestalten und blickst sie betroffen an. Der Kleinste von ihnen raunzt dir heiser zu`n\"`6So ? Hast du Probleme mit jemandem? Komm ruhig zum alten Onkel... denn Feind von irgendwem aus der Familie ist auch Feind vom alten Onkel... Gibst du `^2000 Gold und 2 Edelsteine`6 und Problem wird beseitigt...`&\"");
        addnav("Ja, Onkel","housefeats.php?act=beater2");
        addnav("Nein danke","inside_houses.php");
    }
    else
    {
        output("`&Du suchst die Nähe einiger übel und finster aussehender Gestalten und blickst sie betroffen an.`nDoch da dir dein schlechter Ruf vorauseilt zeigen diese relativ wenig Interesse an dir.");
        addnav("Zurück","inside_houses.php");
    }
    
}
else if ($_GET[act]=="beater2")
{
    If (($session['user']['gold']>=2000) && ($session['user']['gems']>=2))
    {
        Output("`&3 breitschultrige, eklig vernarbte Schläger wenden sich dir zu und schenken dir ihre Aufmerksamkeit.`nSobald du ihnen gesagt hast wen sie verkloppen sollen werden sie sich auf den Weg machen.`nBedenke jedoch, dass sie nur jemanden verprügeln werden, der gerade schläft, lebt und der sich momentan nicht im Kerker befindet.`n`n");
        
        
        
        if ($_GET[who]=="")
        {
            output("`&Wer soll Besuch bekommen?`n`&");
            if ($_GET['subop']!="search")
            {
                output("<form action='housefeats.php?act=beater2&subop=search' method='POST'><input name='name'><input type='submit' class='button' value='Suchen'></form>",true);
                addnav("","housefeats.php?act=beater2&subop=search");
            }
            else
            {
                addnav("Neue Suche","housefeats.php?act=beater2");
                $search = "%";
                for ($i=0; $i<strlen($_POST['name']); $i++)
                {
                    $search.=substr($_POST['name'],$i,1)."%";
                }
                $sql = "SELECT accounts.acctid,name,hitpoints,loggedin,login,account_extra_info.beatenup FROM accounts LEFT JOIN account_extra_info ON account_extra_info.acctid=accounts.acctid WHERE (name LIKE '$search' and alive=1 and loggedin=0 and imprisoned=0) ORDER BY name DESC";
                //output($sql);
                $result = db_query($sql) or die(db_error(LINK));
                $max = db_num_rows($result);
                
                output("<table border=0 cellpadding=0><tr><td>Name</td><td>Status</td></tr>",true);
                for ($i=0; $i<$max; $i++)
                {
                    $row = db_fetch_assoc($result);
                    output("<tr><td><a href='housefeats.php?act=beater2&who=".rawurlencode($row[acctid])."'>$row[name]</a></td><td>",true);
                    
                    addnav("","housefeats.php?act=beater2&who=".rawurlencode($row[acctid]));
                    if ($row['beatenup']>1)
                    {
                        output("`4Freund`0");
                    }
                    else
                    {
                        output($row['beatenup']?"`4verprügelt`0":"`@Ok`0");
                    }
                    output("</td></tr>",true);
                }
                output("</table>",true);
            }
            addnav("Zurück ins Haus","inside_houses.php");
        }
        else
        {
            $sql = "SELECT accounts.acctid,name,hitpoints,login,loggedin,account_extra_info.beatenup,account_extra_info.timesbeaten FROM accounts LEFT JOIN account_extra_info ON account_extra_info.acctid=accounts.acctid WHERE accounts.acctid='$HTTP_GET_VARS[who]'";
            $result = db_query($sql) or die(db_error(LINK));
            $row = db_fetch_assoc($result);
            
            If ($row[beatenup]==1)
            {
                output("`n`&\"`6Nein, `&".$row['name']."`6 hatte bereits Besuch. Warte bis morgen!`&\"");
                addnav("Ok ok","housefeats.php?act=beater2");
            }
            else If ($row[beatenup]>1)
            {
                output("`n`&\"`6Ehhhh! Was erlaube?? `&".$row['name']."`6 ist Freund von die Familie. Warum du spucke mitten in gütige Gesicht von Onkel?`&\"");
                addnav("Ok ok","housefeats.php?act=beater2");
            }
            else
            {
                
                output("`6Alles klar! ein kleiner Trupp der übelsten Schläger macht sich bewaffnet mit Schlagring und Knüppel auf die Suche nach ".$row['name']." `&!`n`n");
                $session['user']['gold']-=2000;
                $session['user']['gems']-=2;
                
                $roll1 = e_rand(0,$row['level']);
                $roll2 = e_rand(0,$session['user']['level']);
                
                // Chance
                if ($roll2>$roll1)
                {
                    output("`6Sie überraschen `7{$row['name']}
                    `6 im Schlaf und hinterlassen ein paar deftige Grüße von dir!");
                    
                    
                    $mail="`&Letzte Nacht haben dich 3 Schläger im Schlaf überrascht und sofort auf dich eingeschlagen. Du hattest nicht mal den Ansatz einer Chance dich zu wehren. Sie haben dir folgenden Zettel auf dein angeschwollenes Haupt geheftet : \"`^Leg dich nicht mit `&
					{$session['user']['login']}
                    `^ an!`&\"`nSie haben dich ziemlich übel erwischt.";
                    systemmail($row['acctid'],"`$Du wurdest verprügelt!`0",$mail);
                    
                    $timesbeaten=$row['timesbeaten']+1;
                    $sql = "UPDATE account_extra_info SET beatenup=1,timesbeaten=$timesbeaten WHERE acctid = ".$row['acctid'];
                    db_query($sql) or die(sql_error($sql));
                    
                    $ouch = (e_rand(30,60)/100);
                    $newhp = $row['hitpoints']*$ouch;
                    
                    $sql = "UPDATE accounts SET hitpoints=$newhp WHERE acctid = ".$row['acctid'];
                    db_query($sql) or die(sql_error($sql));
                    
                }
                else
                {
                    switch (e_rand(0,5))
                    {
                    case 0:
                        output("`6Sie greifen `7{$row['name']}
                        `6an, doch sind sie im entstehenden Handgemenge unterlegen.");
                        $mail="`&Letzte Nacht haben dich 3 Schläger überfallen und böse auf dich eingeschlagen. Du hast dich tapfer gewehrt und konntest sie letztendlich vertreiben. Bei ihrer Flucht haben sie folgenden Zettel fallen gelassen : \"`^Leg dich nicht mit `&
						{$session['user']['login']}
                        `^ an!`&\"`nDu hast einige Blessuren davon getragen.";
                        systemmail($row['acctid'],"`$Du wurdest angegriffen!`0",$mail);
                        $sql = "UPDATE account_extra_info SET beatenup=1 WHERE acctid = ".$row['acctid'];
                        db_query($sql) or die(sql_error($sql));
                        $ouch = (e_rand(50,80)/100);
                        $newhp = $row['hitpoints']*$ouch;
                        $sql = "UPDATE accounts SET hitpoints=$newhp WHERE acctid = ".$row['acctid'];
                        db_query($sql) or die(sql_error($sql));
                        break;
                    case 1:
                    case 2:
                        output("`6Nach einer Weile kommen sie wieder, weil sie `7{$row['name']}
                        `6irgendwie nicht finden konnten. Deine Bezahlung zurück zu verlangen traust du dich natürlich jetzt nicht.");
                        break;
                    case 3:
                    case 4:
                        output("`6Nach einer gewissen Zeit kommen sie wieder und erzählen dir stolz wie sie `7
						{$row['name']}
                        `6praktisch ohne Gegenwehr windelweich geschlagen haben. Als du stutzig wirst und nachfragst erhärtet sich nach und nach der Verdacht, dass sich die Schläger den Falschen gegriffen haben. Du lobst sie natürlich für ihre Arbeit, bist aber schon ein wenig beschämt.");
                        break;
                    case 5:
                        output("`6Die Zeit vergeht und vergeht und vergeht...`nUnd vergeht. Irgendwann akzeptierst du übers Ohr gehauen worden zu sein und gehst betrübt.");
                        break;
                    }
                }
                $session['user']['reputation']-=25;
                addnav("Ins Haus","inside_houses.php");
                
            }
        }
    }
    else
    {
        output("`&Die Schläger bestehen zunächst darauf das Gold und die Edelsteine zu sehen.`nAber da du ihnen nicht geben kannst was sie wollen verpassen sie dir eine und lassen dich auf dem Boden liegen");
        $session['user']['hitpoints']*=0.8;
        addnav("Das tat weh!","inside_houses.php");
    }
    
}
else if ($_GET[act]=="dbite")
{
    $sql = "SELECT name,state FROM disciples WHERE master=".$session['user']['acctid']."";
    $result = db_query($sql) or die(db_error(LINK));
    $rowk = db_fetch_assoc($result);
    
    output("`4Alles klar!`nDu gehst mit `&".$rowk['name']." `4 in eine dunkle Ecke und stürzt dich durstig auf den armen Kerl...`nEs wird eine Weile brauchen bis die Verwandlung abgeschlossen ist!");
    unset($session['bufflist']['decbuff']);
    $sql = "UPDATE disciples SET state=20 WHERE master = ".$session['user']['acctid'];
    db_query($sql) or die(sql_error($sql));
    addnav("Zurück","inside_houses.php");
    
}
else if ($_GET[act]=="checkfriend")
{
    $sql = "SELECT beatenup FROM account_extra_info WHERE acctid=".$session['user']['acctid']."";
    $result = db_query($sql) or die(db_error(LINK));
    $rowb = db_fetch_assoc($result);
    output("`&Du begibst dich zu den hochgefährlichen Schlägertypen und fragst mit zittriger Stimme was denn die Familie so von dir hält. Der Kleinste von ihnen wendet sich dir zu nd krächzt heiser:`n");
    
    if ($rowb['beatenup']==0)
    {
        output("`6\"Oh weh, oh weh... sehe nicht aus gut für dich. Du nicht Freund von die Familie...\"`n`n`&Sieht so aus als wäre ein kleines \"Geschenk\" nötig um ihre Gunst zu gewinnen.");
    }
    else if ($rowb['beatenup']==1)
    {
        output("`6\"Ahhh, ich sehe man hat getanzt wilde Polka auf deinem Gesicht! Du sollte wissen dass sich nicht gut lebt ohne Freundschaft von die Familie...\"`n`n`&Recht hat er...");
    }
    else if ($rowb['beatenup']>1)
    {
        output("`6\"Oh... `&".$session['user']['name']."`6, meine beste ".($session[user][sex]?"Mädchen ":"Junge ")."! Du gute Freund von die Familie.`nAber passe auf, nur noch Freund für `&".($rowb['beatenup']-1)." Tage`6!\"`&`n");
    }
    addnav("Zurück","inside_houses.php");
    
}
else if ($_GET[act]=="familygift")
{
    output("`&Aus dem Geklüngel der übel vernarbten Schläger löst sich ein kleiner, edel gekleideter Mann. Er spricht dich mit heiserer Stimme an:`n`6\"Soo ? Du wolle werde Freund von die Familie? Das ist sehr weise Entschluss und gut für deine hübsch Gesicht, wenn du verstehe...`nWenn du schenke `^10 Edelsteine`6 du sein Freund von die Familie für eine Woche und du mich könne nenne deine Onkel! Ehh, was sage?\"`&`n");
    addnav("Si! Si!","housefeats.php?act=familygift2");
    addnav("Sissy...","inside_houses.php");
    
}
else if ($_GET[act]=="familygift2")
{
    if ($session['user']['gems']>=10)
    {
        output("`&Der Kleine, Alte lächelt dich väterlich an als du ihm die Edelsteine gibst. Er ruft laut :`n`6\"Ehh, hört alle her! `&".$session['user']['name']."`6 ist nun Freund von die Familie für eine Woche!");
        $session['user']['gems']-=10;
        $sql = "UPDATE account_extra_info SET beatenup=8 WHERE acctid = ".$session['user']['acctid'];
        db_query($sql) or die(sql_error($sql));
        addnav("Zurück","inside_houses.php");
    }
    else
    {
        output("`&Der Kleine, Alte sieht dass du dir das beim besten Willen nicht leisten kannst und dreht dir wortlos den Rücken zu.`nAnders seine breitschultigen Schläger, die erstmal eine Weile mit deinem Gesicht den Boden wischen, bevor sie von dir ablassen.`n`4Du verlierst Lebenspunkte!");
        $session['user']['hitpoints']*=0.8;
        addnav("Pfurück","inside_houses.php");
    }
    
}
else if ($_GET[act]=="roulette")
{
    $sql = "SELECT rouletterounds FROM account_extra_info WHERE acctid=".$session['user']['acctid']."";
    $result = db_query($sql) or die(db_error(LINK));
    $rowr = db_fetch_assoc($result);
    $bottles=$rowr['rouletterounds'];
    output("`3Orkisch Roulette:`n`n");
    if ($session['user']['turns']>1)
    {
        if ($bottles>0)
        {
            output("`&Ein mageres Männlein mit blassem Gesicht sitzt an einem Tisch, weit abseits der Menge und nippt ab und zu an einem schalen Ale.`nAls du zu ihm gehts schaut er dich mit stechendem Blick an und erklärt dir die Regeln:`n`3\"Vor Euch seht Ihr Phiolen mit klarer Flüssigkeit. Allesamt gleichen sie sich äußerlich. In diesen Phiolen befindet sich Wasser, allerdings in Einer auch ein geschmackloses Gift, das Euch innerhalb von Sekunden töten wird.`nWenn Ihr spielen wollt nehmt einfach eine der Phiolen und trinkt sie in einem Zug aus.\"`n`n");
            switch ($bottles)
            {
            case 5 :
                $price=(50*$session['user']['level'])." Gold";
                break;
            case 4 :
                $price=(100*$session['user']['level'])." Gold";
                break;
            case 3 :
                $price="1 Edelstein";
                break;
            case 2 :
                $price="2 Edelsteine";
                break;
            case 1 :
                $price="1 Freibrief";
                break;
            }
            
            output("`&Vor dir auf dem Tisch stehen `^".($bottles+1)."`& Phiolen.`nDein Preis ist: `^".$price."`&");
            
            for ($i=1; $i<=($bottles+1); $i++)
            {
                addnav("Phiole","housefeats.php?act=roulette2&bottles=$bottles");
            }
            
        }
        else
        {
            output("`&Du hast heute schon beim Orkisch Roulette abgeräumt.`nWas willst du also hier?");
        }
    }
    else
    {
        output("`&Der magere Kerl ist nirgends zu sehen, obwohl du schwören könntest, dass er eben noch da war!");
    }
    addnav("Weg hier!","inside_houses.php");
    
    
}
else if ($_GET[act]=="roulette2")
{
    $bottles=$_GET['bottles'];
    $chance=e_rand(1,($bottles+1));
    
    if ($chance==1)
    {
        output("`n`4Tja... im Leben gibt es Gewinner... und es gibt.... DICH!`nDu hast die vergiftete Phiole erwischt und bist nun TOT!");
        $session['user']['hitpoints']=0;
        $session['user']['alive']=0;
        addnews("`2".$session['user']['name']."`2 hat beim Orkisch Roulette verloren!");
        addnav("Gurgelnd sterben","shades.php");
    }
    else
    {
        
        $text="`^`nDu hast GEWONNEN!`nHier ist dein Preis : ";
        
        switch ($bottles)
        {
        case 5 :
            output($text.(50*$session['user']['level'])." Gold.`&");
            $session['user']['gold']+=(50*$session['user']['level']);
            break;
        case 4 :
            output($text.(100*$session['user']['level'])." Gold.`&");
            $session['user']['gold']+=(100*$session['user']['level']);
            break;
        case 3 :
            output($text."1 Edelstein.`&");
            $session['user']['gems']+=1;
            break;
        case 2 :
            output($text."2 Edelsteine`&");
            $session['user']['gems']+=2;
            break;
        case 1 :
            output($text."1 Freibrief`&");
            $name=$session['user']['acctid'];
            
            item_add($session['user']['acctid'],'frbrf');
            
            break;
        }
        
        $bottles--;
        $sql = "UPDATE account_extra_info SET rouletterounds=$bottles WHERE acctid = ".$session['user']['acctid'];
        db_query($sql) or die(sql_error($sql));
        
        $session['user']['turns']--;
        if ($bottles>0)
        {
            addnav("Zurück","housefeats.php?act=roulette");
        }
        else
        {
            addnav("Ins Haus","inside_houses.php");
        }
    }
    
}
else if ($_GET[act]=="sendtrophy")
{
    output("`3Der freundliche Herr vom Versandhandel ist bestens dazu geeignet Lieferungen für dich auszuführen.`n
			Pro Stück verlangt er dafür `^500 Goldmünzen`3.`n
			Einen Waldkampf wirst du wahrscheinlich benötigen, um ihm die nötigen Instruktionen zu erteilen.`n`n");
    if ($session['user']['gold']<500)
    {
        output("`3So viel Gold hast du natürlich gerade nicht bei dir, aber der gute Mann kann warten, bis du dir diesen Service leisten kannst.`n`n");
    }
	else if ($session['user']['turns'] <= 0)
    {
        output("`3Leider bist du gerade zu erschöpft, um dich mit dem Versenden von Krimskram auseinanderzusetzen.`n`n");
    }
    else
    {
        output("`n`3Nach einem Blick auf deine Finanzen erklärt sich der Lieferant einverstanden.`nFolgende Dinge kann er für dich verschicken:`n`n");
            		
		$str_msg = 'Er deutet auf die paar altbackenen Krümel, die du aus deinen Taschen hervorkramst. Du wirst das Gefühl nicht los, dass er dich damit veralbern will..';
		
		$arr_options = array('Versenden!'=>'&act=sendtrophy2');
				        
		item_show_invent(' owner='.$session['user']['acctid'].' AND distributor=1 AND deposit1=0', false, 0, 1, 1, $str_msg, $arr_options);
		
    }
	addnav('Zurück');
    addnav("Ins Haus","inside_houses.php");
    
}
else if ($_GET[act]=="sendtrophy2")
{
    $itemid=$_GET['id'];
    output("`3An wen soll denn das gute Stück gehen ?`n`n");
    
    if ($_GET[who]=="")
    {
        output("`&Empfänger suchen:`n`&");
        if ($_GET['subop']!="search")
        {
            output("<form action='housefeats.php?act=sendtrophy2&id=$itemid&subop=search' method='POST'><input name='name'><input type='submit' class='button' value='Suchen'></form>",true);
            addnav("","housefeats.php?act=sendtrophy2&id=$itemid&subop=search");
        }
        else
        {
            addnav("Neue Suche","housefeats.php?act=sendtrophy2&id=$itemid");
            $search = "%";
            for ($i=0; $i<strlen($_POST['name']); $i++)
            {
                $search.=substr($_POST['name'],$i,1)."%";
            }
            $sql = "SELECT name,alive,loggedin,login FROM accounts WHERE (name LIKE '$search') ORDER BY name ASC";
            //output($sql);
            $result = db_query($sql) or die(db_error(LINK));
            $max = db_num_rows($result);
            
            output("<table border=0 cellpadding=0><tr><td>Name</td><td>Status</td></tr>",true);
            for ($i=0; $i<$max; $i++)
            {
                $row = db_fetch_assoc($result);
                output("<tr><td><a href='housefeats.php?act=sendtrophy2&id=$itemid&who=".rawurlencode($row[login])."'>$row[name]</a></td><td>",true);
                if ($row['loggedin'])
                {
                    output("`@online`&</td></tr>",true);
                }
                else
                {
                    output("`4offline`&</td></tr>",true);
                }
                addnav("","housefeats.php?act=sendtrophy2&id=$itemid&who=".rawurlencode($row[login]));
            }
            output("</table>",true);
        }
    }
    else
    {
        $sql = "SELECT acctid,name,login FROM accounts WHERE login=\"$_GET[who]\"";
        $result = db_query($sql) or die(db_error(LINK));
        $row = db_fetch_assoc($result);
        
        $item = item_get(' id='.$itemid , false);
        
        output("`3Alles klar! ".$item['name']."`3 verschwindet in einer Schachtel und der Lieferant macht sich auf den Weg zu ".$row['name']."`3 !`n");
        $userid=$row['acctid'];
        
        addnav("Losschicken");
        addnav("Ok","housefeats.php?act=sendtrophy3&itemid=$itemid&userid=$userid");
        addnav("NEIN! ZURÜCK!","housefeats.php?act=sendtrophy2&id=$itemid");
    }
    addnav("Abbrechen");
    addnav("Anderes Stück","housefeats.php?act=sendtrophy");
    addnav("Zurück ins Haus","inside_houses.php");
    
}
else if ($_GET[act]=="sendtrophy3")
{
    $itemid=$_GET['itemid'];
    $userid=$_GET['userid'];
    
    $sql = "SELECT acctid,name,login FROM accounts WHERE acctid=".$userid;
    $result = db_query($sql) or die(db_error(LINK));
    $row = db_fetch_assoc($result);
    
    $item = item_get(' id='.$itemid);
	
	addnav("Zurück ins Haus","inside_houses.php");
	
	if ($item['tpl_id'] == 'trph' && $row['acctid']==$item['hvalue'])
	{
		output("`&Also bitte! Du wirst ".$row['name']."`& doch nicht seine eigenen Körperteile schicken wollen?!");
		page_footer();
		exit;
	}
	
	if(!empty($item['send_hook'])) {
		$item_hook_info['recipient'] = $row;
	
		item_load_hook($item['send_hook'],'send_hook',$item);
	}
	
	if(!$item_hook_info['hookstop']) {
    	
		$session['user']['gold']-=500;
		output("`3Deine Lieferung ist auf dem Weg!`n`n");
		
		systemmail($row['acctid'],"`@Lieferung erhalten!`0","`&{$session['user']['name']}
		`6 hat dir ein Päckchen zukommen lassen. Darin befindet sich ".$item['name']."`6 und ein paar Fliegen.");
		
		$item['owner'] = $userid;
		//print_r($item);	
		item_set(' id='.$itemid, $item );
		
		debuglog('versandte '.$item['name'].'`0 an ',$row['acctid']);
		
	}
	
	if(e_rand(1,10) != 1) {
		$session['user']['turns']--;
		output('Du verlierst einen Waldkampf.');
	}	
    
    
}
else if ($_GET[act]=="searchdisciple")
{
    $cost=$session['user']['level']*1000;
    output("`&Du näherst dich den Abenteurern, die in einem kleinen Saal im Keller umgeben von allen möglichen Karten und viel Plunder ihre Heldentaten zum Besten geben.`nDir kommt in den Sinn, dass diese Männer vielleicht wissen könnten wohin man deinen armen Knappen verschleppt hat.`nDu beginnst ein Gespräch und sehr schnell wird deutlich, dass es dich einiges kosten wird Informationen zu erlangen.`nDie Männer wollen `^".$cost."`& Goldstücke sehen, bevor sie auch nur einen Ton von sich geben.`nWas tust du ?");
    addnav("".$cost." Gold geben","housefeats.php?act=searchdisciple2");
    addnav("Zurück ins Haus","inside_houses.php");
    
}
else if ($_GET[act]=="searchdisciple2")
{
    $cost=$session['user']['level']*1000;
    if ($session['user']['gold']<$cost)
    {
        output("`&Das übersteige deine Mittel. Obwohl dein Schicksal den Männern sehr leid tut, werden sie dir dennoch nicht helfen!`n");
        addnav("Zurück ins Haus","inside_houses.php");
    }
    else
    {
        $sql = "SELECT name FROM disciples WHERE master=".$session['user']['acctid']."";
        $result = db_query($sql) or die(db_error(LINK));
        $rowk = db_fetch_assoc($result);
        $session['user']['gold']-=$cost;
        output("`&Mit kalter Miene legst du einen Sack mit ".$cost." Goldmünzen auf den Tisch.`nSofort werden die Männer aktiv. Nach einer ganzen Weile sagt dir einer von ihnen :`n\"`3Nun, mein Herr, es sieht so aus als ob Euer Schützling in einer Höhle im Wald festgehalten und übelst gequält wird.`nEr wird von sehr starken magischen Kreaturen bewacht! Nun, wir sind nur einfache Abenteurer und keine Kämpfer, und lebensmüde sind wir auch nicht. Ihr müsstest ihn schon selbst dort herausholen. Aber eilt Euch, lange macht es der Knabe nicht mehr mit!`&\"`nEr zeigt dir eine große Karte und deutet auf den Punkt an dem sich die Höhle befindet.`n`nWenn du `^".$rowk['name']."`& retten willst, dann musst du es jetzt tun!");
        addnav("Zur Rettung!","housefeats.php?act=searchdisciple4");
        addnav("Zurück ins Haus","housefeats.php?act=searchdisciple3");
    }
    
}
else if ($_GET[act]=="searchdisciple3")
{
    output("`&Damit hast du deine letzte Chance vertan deinen Knappen jemals lebend wieder zu sehen.");
    $sql="DELETE FROM disciples WHERE master=".$session['user']['acctid'];
    db_query($sql);
    addnav("Zurück ins Haus","inside_houses.php");
    
}
else if ($_GET[act]=="searchdisciple31")
{
    output("`&Damit hast du deine letzte Chance vertan deinen Knappen jemals lebend wieder zu sehen.");
    $session['user']['badguy']="";
    $sql="DELETE FROM disciples WHERE master=".$session['user']['acctid'];
    db_query($sql);
    addnav("Weiter","forest.php");
    
}
else if ($_GET[act]=="searchdisciple4")
{
    output("`&Du nimmst deine Waffe, trinkst noch ein gutes Ale und machst dich auf in den Wald, zu der dir beschriebenen Stelle.`nTatsächlich findest du eine Höhle vor, die Abenteurer haben dich also nicht reingelegt.`nDoch was dich vor der Höhle erwartet ist einfach nur grauenvoll :`nEin übermässig großer schwarzer Hund mit langen scharfen Zähnen, dem kleine Flammen aus dem Maul schlagen. Wütend knurrt er dich an.`nWas nun ?");
    $badguy = array("creaturename"=>"`THöllenhund`0"
    ,"creaturelevel"=>$session['user']['level']
    ,"creatureweapon"=>"Flammenbiss"
    ,"creatureattack"=>$session['user']['attack']*1.4
    ,"creaturedefense"=>$session['user']['defence']*0.95
    ,"creaturehealth"=>$session['user']['maxhitpoints']*0.4
    ,"diddamage"=>0);
    
    $session['user']['badguy']=createstring($badguy);
    $atkflux = e_rand(0,$session['user']['dragonkills']*2);
    $defflux = e_rand(0,($session['user']['dragonkills']*2-$atkflux));
    $hpflux = ($session['user']['dragonkills']*2 - ($atkflux+$defflux)) * 5;
    $badguy['creatureattack']+=$atkflux;
    $badguy['creaturedefense']+=$defflux;
    $badguy['creaturehealth']+=$hpflux;
    $_SESSION['discopps']=0;
    addnav("Kämpfen!","housefeats.php?op=fight");
    addnav("Flüchten","housefeats.php?act=searchdisciple31");
    
}
else if ($_GET[act]=="searchdisciple5")
{
    output("`&Du lässt die Kreatur hinter dir und steigst über ihren Kadaver in die dunkel Höhle.`nEine Fackel spendet dir etwas Licht. Langsam gehst du einen Gang hinab und erkennst voller Schrecken, dass dir eine große menschenähnliche Kreatur aus Lehm den Weg versperrt. Sofort beginnt der Klotz sich zu bewegen und seine schweren Fäuse gegen dich zu heben.`nWas tust du?");
    $badguy = array("creaturename"=>"`TLehmgolem-Wächter`0"
    ,"creaturelevel"=>$session['user']['level']
    ,"creatureweapon"=>"Fäuste"
    ,"creatureattack"=>$session['user']['defence']*0.85
    ,"creaturedefense"=>$session['user']['attack']*0.95
    ,"creaturehealth"=>$session['user']['maxhitpoints']*0.8
    ,"diddamage"=>0);
    
    $session['user']['badguy']=createstring($badguy);
    $atkflux = e_rand(0,$session['user']['dragonkills']*2);
    $defflux = e_rand(0,($session['user']['dragonkills']*2-$atkflux));
    $hpflux = ($session['user']['dragonkills']*2 - ($atkflux+$defflux)) * 5;
    $badguy['creatureattack']+=$atkflux;
    $badguy['creaturedefense']+=$defflux;
    $badguy['creaturehealth']+=$hpflux;
    addnav("Kämpfen!","housefeats.php?op=fight");
    addnav("Flüchten","housefeats.php?act=searchdisciple31");
    
}
else if ($_GET[act]=="searchdisciple6")
{
    output("`&Nachdem du auch dieses Monstrum hinter dir gelassen hast folgst du dem Gang weiter. Es kommt dir etwas seltsam vor, dass die Wände und der Boden gut gearbeitet sind. Wer könnte diese Höhle angelegt haben?`nDann stehst du vor einer großen Tür aus Holz, hinter der du Stimmen hörst.`nWas tust du?");
    $badguy = array("creaturename"=>"`#Sklavenjäger`0"
    ,"creaturelevel"=>$session['user']['level']
    ,"creatureweapon"=>"Peitsche"
    ,"creatureattack"=>$session['user']['attack']*0.75
    ,"creaturedefense"=>$session['user']['defence']*0.85
    ,"creaturehealth"=>$session['user']['maxhitpoints']*0.9
    ,"diddamage"=>0);
    
    $session['user']['badguy']=createstring($badguy);
    $atkflux = e_rand(0,$session['user']['dragonkills']*2);
    $defflux = e_rand(0,($session['user']['dragonkills']*2-$atkflux));
    $hpflux = ($session['user']['dragonkills']*2 - ($atkflux+$defflux)) * 5;
    $badguy['creatureattack']+=$atkflux;
    $badguy['creaturedefense']+=$defflux;
    $badguy['creaturehealth']+=$hpflux;
    addnav("Die Tür eintreten!","housefeats.php?op=fight");
    addnav("Flüchten","housefeats.php?act=searchdisciple31");
    
}
else if ($_GET[act]=="searchdisciple7")
{
    $badguy = array("creaturename"=>"`#Anführer der Sklavenjäger`0"
    ,"creaturelevel"=>$session['user']['level']
    ,"creatureweapon"=>"Starke Peitsche"
    ,"creatureattack"=>$session['user']['attack']*0.85
    ,"creaturedefense"=>$session['user']['defence']*0.95
    ,"creaturehealth"=>$session['user']['maxhitpoints']*0.9
    ,"diddamage"=>0);
    
    $session['user']['badguy']=createstring($badguy);
    $atkflux = e_rand(0,$session['user']['dragonkills']*2);
    $defflux = e_rand(0,($session['user']['dragonkills']*2-$atkflux));
    $hpflux = ($session['user']['dragonkills']*2 - ($atkflux+$defflux)) * 5;
    $badguy['creatureattack']+=$atkflux;
    $badguy['creaturedefense']+=$defflux;
    $badguy['creaturehealth']+=$hpflux;
    redirect("housefeats.php?op=fight");
    
}
else if ($_GET[act]=="searchdisciple8")
{
    $sql = "SELECT name,oldstate,level FROM disciples WHERE master=".$session['user']['acctid']."";
    $result = db_query($sql) or die(db_error(LINK));
    $rowk = db_fetch_assoc($result);
    $newlevel=(int)($rowk['level']*0.75);
    $newstate=$rowk['oldstate'];
    output("`&Du hast es geschafft! Du hast es tatsächlich geschafft!`nNachdem du diesen miesen Schurken den Rest gegeben hast erblickst du `^".$rowk['name']."`& abgemagert und mit deutlichen Spuren seiner schlechten Behandlung in einen kleinen Vogelkäfig gewängt in einer Ecke des Raumes.`nNachdem du ihn befreit hast fällt er dir in die Arme.`n`nDu hast `^".$rowk['name']."`& zwar zurück bekommen, allerdings ist er nicht mehr so stark wie vorher.`nSein neuer Level ist ".$newlevel.".`n`nIn einer Kiste findest du 5000 Goldmünzen.`n");
    $session['user']['gold']+=5000;
    $sql = "UPDATE disciples SET level=".$newlevel.",state=$newstate WHERE master=".$session[user][acctid]."";
    db_query($sql);
    $sql = "SELECT disciples_spoiled FROM account_extra_info WHERE acctid=".$session['user']['acctid']."";
    $result = db_query($sql) or die(db_error(LINK));
    $rows = db_fetch_assoc($result);
    $spoil=$rows['disciples_spoiled']-1;
    $sql = "UPDATE account_extra_info SET disciples_spoiled=$spoil WHERE acctid = ".$session['user']['acctid'];
    db_query($sql) or die(sql_error($sql));
    addnews($session['user']['name']."`2 hat ".($session[user][sex]?"ihren":"seinen")." Knappen `^".$rowk['name']."`2 aus den Händen von Sklavenhändlern befreit!`0`n");
    // check best one
    $level=$rowk['level'];
    $sql = "SELECT id,level FROM disciples WHERE best_one=1";
    $result = db_query($sql) or die(db_error(LINK));
    $rowb = db_fetch_assoc($result);
    
    if ($level>$rowb['level'])
    {
        output("`n`^".$rowk['name']." ist stärker als jeder andere Knappe im Land!`n");
        $sql = "UPDATE disciples SET best_one=1 WHERE master=".$session['user']['acctid']."";
        db_query($sql);
        $sql = "UPDATE disciples SET best_one=0 WHERE master<>".$session['user']['acctid']."";
        db_query($sql);
    }
    addnav("Fort von hier!","forest.php");
}
if ($_GET['op']=="fight")
{
    $battle=true;
}
if ($battle)
{
    {
        include("battle.php");
        if ($victory)
        {
            output("`nDu hast `^".$badguy['creaturename']." geschlagen.");
            $badguy=array();
            $session['user']['badguy']="";
            $_SESSION['discopps']+=1;
        }
        else if ($defeat)
        {
            output("Die Kreatur spielt dir übel mit und nimmt dich nach allen Regeln der Kunst auseinander.`n");
            output("`n`4Du bist tot.`n");
            output("Du verlierst 10% deiner Erfahrung und all Dein Gold.`n");
            output("Deinen Knappen wirst du nie wieder sehen!`n`0");
            $session['user']['gold']=0;
            $session['user']['experience']=round($session['user']['experience']*.9,0);
            $session['user']['alive']=false;
            $session['user']['hitpoints']=0;
            $session['user']['reputation']--;
            $sql="DELETE FROM disciples WHERE master=".$session['user']['acctid'];
            db_query($sql);
            addnews($session['user']['name']."`4 machte sich auf die Suche nach seinem verschleppten Knappen, ward aber nie wieder gesehen!`0`n");
            addnav("Tägliche News","news.php");
        }
        else
        {
            fightnav();
        }
    }
    if ($_SESSION['discopps']==1)
    {
        $_SESSION['discopps']++;
        addnav("Weiter","housefeats.php?act=searchdisciple5");
    }
    if ($_SESSION['discopps']==3)
    {
        $_SESSION['discopps']++;
        addnav("Weiter","housefeats.php?act=searchdisciple6");
    }
    if ($_SESSION['discopps']==5)
    {
        $_SESSION['discopps']++;
        addnav("Weiter","housefeats.php?act=searchdisciple7");
    }
    if ($_SESSION['discopps']==7)
    {
        $_SESSION['discopps']++;
        addnav("Weiter","housefeats.php?act=searchdisciple8");
    }
}
page_footer();
?>
