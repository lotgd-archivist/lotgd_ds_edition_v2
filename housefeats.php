<?

/**
Auslagerung der Sonderf�higkeiten ausgebauter Haustypen
Erfordert : houses.php
Beinhaltet Knappen-Erweiterung (erfordet : disciples.lib.php)
by Maris (Maraxxus@gmx.de)
**/

require_once("common.php");
page_header();

//Tier f�ttern auf dem Bauernhof
if ($_GET[act]=="feed")
{
    output("Dein {$playermount['mountname']}
l�sst es sich hier richtig gut gehen und tollt herum, frisst sich satt und ist voll regeneriert.`n");
    if ($session['user']['gems']>0)
    {
        output("Ein Edelstein d�rfte gen�gen um die kleinen Sch�den zu bezahlen, die dein Tier im �bermut verursacht hat.`n");
        $session['user']['gems']--;
    }
    else
    {
        output("Du hast nichts dabei um die kleinen Sch�den zu begleichen, die dein Tier im �bermut verursacht hat. Wie peinlich!`n");
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
    addnav("Zur�ck zum Haus","inside_houses.php");
    
    // Bordellbesuch
}
else if ($_GET[act]=="amuse")
{
    
    $happy = array("name"=>"`!Extrem gute Laune","rounds"=>45,"wearoff"=>"`!Deine gute Laune vergeht allm�hlich wieder.`0","defmod"=>1.15,"roundmsg"=>"Du schwelgst in Erinnerung an den Bordellbesuch und tust alles daf�r dass es nicht dein Letzter war!","activate"=>"defense");
    
    if ($session['user'][seenlover]==0)
    {
        
        output("`7Du ziehst dich zur�ck und willst dich einmal so richtig verw�hnen lassen.`n");
        if ($session[user][gold]<2000)
        {
            output("Doch leider wird daraus nichts, denn du hast keine `#2000 Gold`7, die du daf�r brauchst!`n");
        }
        else
        {
            output("`7Du l�sst es dir so richtig gut gehen und wirst wohl f�r den Rest des Tages dieses Grinsen nicht mehr aus dem Gesicht bekommen.`n");
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
                    `6 wurde gesehen, wie ".($session[user][sex]?"sie":"er")." sich im Bordell vergn�gt hat. Willst du dir das gefallen lassen ?");
                }
                
                break;
            case 3:
                break;
            }
        }
    }
    else
    {
        output("Schon wieder ?! Nein, f�r heute hast du dich schon genug vergn�gt!");
    }
    addnav("Zur�ck zum Haus","inside_houses.php");
    
    
}
else if ($_GET[act]=="fill")
{
    // Anwendungen im Gildenhaus nachf�llen
    
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
        output("`7Du ziehst dich zur�ck und philosophierst mit den Gildenmeistern �ber ");
        if ($session[user][gold]<1000)
        {
            output("`@deine Armut`7. Denn du hast keine `#1000 Gold`7, die du daf�r brauchst!");
        }
        else
        {
            {
                output("`@".$skills[$session['user']['specialty']]."`7. Die Einsichten, die du dabei gewinnst, sind �berw�ltigend!");
                
                $session['user']['specialtyuses'][$row['usename']."uses"] = 5;
                
            }
            $session[user][gold]-=1000;
            output("`n`n`7Du erh�ltst weitere `^5`7 Anwendungen f�r heute!`n");
        }
        
        
        
        
        
    }
    
    
    
    addnav("Zur�ck zum Haus","inside_houses.php");
    
}
else if ($_GET[act]=="cry")
{
    // Im Festungskeller austoben
    output("`7Der Keller ist gro� und von dickem Mauerwerk umgeben. Hier bist du ganz ungest�rt und kannst einmal so richtig Dampf ablassen.`n");
    if ($session['user'][seenmaster]==1)
    {
        if ($session['user'][turns]<=0)
        {
            output("`7Doch irgendwie fehlt dir dazu gerade die Kraft...");
        }
        else
        {
            output("`7Du gibst dich ungehemmt deiner Schmach hin und heulst dich einmal so richtig aus. Nachdem du dich wieder beruhigt hast stellst du fest, dass du wieder den Mut gefunden hast deinem Meister erneut unter die Augen zu treten.`n");
            output("`7`nDu verlierst `#3`7 Waldk�mpfe und kannst erneut gegen deinen Meister antreten!");
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
        output("`7Doch irgendwie geht es dir gerade recht gut und du versp�rst nicht den Drang danach. Doch du beschlie�t nach der n�chsten Dem�tigung deines Meisters genau hierhin zu kommen...");
    }
    addnav("Zur�ck zum Haus","inside_houses.php");
    
}
else if ($_GET[act]=="train")
{
    // In der Kaserne k�mpfen �ben
    $training = array("name"=>"`!Kampf�bungen","rounds"=>50,"wearoff"=>"`!Du hast die Lektionen der Veteranen vergessen und kehrst wieder zu deinem unkonventionellen Kampfstil zur�ck.`0","defmod"=>1.1,"atkmod"=>1.1,"roundmsg"=>"Du wendest an was du in den �bungen gelernt hast!","activate"=>"defense","activate"=>"offense");
    
    output("`7Du holst tief Luft und bittest die alten Veteranen dir die hohe Kunst des K�mpfens n�her zu bringen.`n");
    if ($session[user][gold]<3000)
    {
        output("Doch das kostet `#3000 Gold`7, die du nicht dabei hast. Allerdings gibt es daf�r eine Tracht Pr�gel umsonst!`n");
    }
    else
    {
        output("`7Die alten Veteranen nehmen dich ganz sch�n hart ran. Doch du kannst dir einen sehr guten Kampfstil dabei aneignen!`n");
        $penality= e_rand(1,3);
        $session[user][gold]-=3000;
        $session[user][turns]-=$penality;
        output("`7Du verlierst dabei `#$penality`7 Waldk�mpfe.");
        $session['bufflist']['training']=$training;
    }
    addnav("Zur�ck zum Haus","inside_houses.php");
    
    
}
else if ($_GET[act]=="torture")
{
    // Im Kerker Gefangene qu�len
    output("`7Du steigst die steinernen Stufen zu den Kerkerzellen hinab, bewaffnet mit Brenneisen und Kneifzange.`n`n");
    if ($session[user][turns]<4)
    {
        output("Allerdings f�hlst du dich schon viel zu m�de um heute noch irgendwen zu qu�len.`n`&Du musst mindestens 4 Waldk�mpfe �brig haben.");
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
            output($loggedin?"`#Wach`0":"`3Schl�ft`0");
            output("</td><td>",true);
            if ($row[abused]==0)
            {
                output("`@Ok`0");
            }
            else if ($row[abused]==1)
            {
                output("`4gequ�lt`0");
            }
            else if ($row[abused]==2)
            {
                output("`4abgek�mpft`0");
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
        addnav("Wahllos qu�len","housefeats.php?act=torture2");
    }
    addnav("Zur�ck zum Haus","inside_houses.php");
}
else if ($_GET[act]=="torture2")
{
    output("`7Du suchst dir wahllos einen der Gefangenen aus und beginnst dein sadistisches Werk.`n");
    output("`7Dabei steigerst du dich regelrecht in einen Blutrausch!`n");
    output("`7Als du fertig bist versp�rst du gro�e Lust dich nun mit jemandem zu befassen, der sich auch wehren kann.`n`n");
    $penality= e_rand(2,4);
    output("`7Du verlierst `#$penality`7 Waldk�mpfe und erh�lst einen weiteren Spielerkampf dazu!`n");
    $session[user][playerfights]+=1;
    $session[user][turns]-=$penality;
    $session[user][charm]--;
    addnav("Zur�ck zum Haus","inside_houses.php");
}
else if ($_GET[act]=="torture3")
{
    $result = db_query("SELECT name,accounts.acctid,sex,level,imprisoned,account_extra_info.abused FROM accounts LEFT JOIN account_extra_info ON account_extra_info.acctid=accounts.acctid WHERE login='$_GET[char]'");
    $row = db_fetch_assoc($result);
    
    if ($row['abused']==0)
    {
        
        output("`7Du schleichst dich zu ".($row['name'])."`7 in die Zelle und qu�lst ".($row[sex]?"sie":"ihn")." f�r ein paar Stunden. Als du fertig bist versp�rst du gro�e Lust dich nun mit jemandem zu befassen, der sich auch wehren kann.`n`n ");
        
        $mail="`@{$session['user']['name']}
        `& ist, w�hrend du im Kerker eingesessen hast, in deine Zelle gekommen und hat dich ";
        
        switch (e_rand(1,10))
        {
        case 1 :
            $mail=$mail."mit einem selbstgebackenen Kuchen ";
            break;
        case 2 :
            $mail=$mail."mit dem Erz�hlen ".($session['user']['sex']?"ihrer":"seiner")." Lebensgeschichte ";
            break;
        case 3 :
            $mail=$mail."durch stundenlanges Anstarren ";
            break;
        case 4 :
            $mail=$mail."mit heftigen Kitzelattacken ";
            break;
        case 5 :
            $mail=$mail."durch ".($session['user']['sex']?"ihre":"seine")." blo�e Anwesenheit ";
            break;
        case 6 :
            $mail=$mail."mit dem Erz�hlen dummer Witze ";
            break;
        case 7 :
            $mail=$mail."mit einem langen Vortrag �ber Bellerophontes Heldentaten ";
            break;
        case 8 :
            $mail=$mail."mit einem Rezept f�r L�wenzahnsalat ";
            break;
        case 9 :
            $mail=$mail."mit lautem Gesang ";
            break;
        case 10 :
            $mail=$mail."mit einer Feder ";
            break;
        }
        $mail=$mail."�belst gequ�lt. Dieses traumatische Erlebnis wird dich noch sehr lang verfolgen!";
        systemmail($row['acctid'],"`$Folterung!`0",$mail);
        
        $sql = "UPDATE account_extra_info SET abused=1 WHERE acctid = ".$row['acctid'];
        db_query($sql) or die(sql_error($sql));
        
        $penality= e_rand(2,4);
        output("`7Du verlierst `#$penality`7 Waldk�mpfe und erh�ltst einen weiteren Spielerkampf dazu!`n");
        $session[user][playerfights]+=1;
        $session[user][turns]-=$penality;
        $session[user][charm]--;
        addnav("Zur�ck zum Haus","inside_houses.php");
        
    }
    else
    {
        output("`&".($session['user']['sex']?"Diese K�mpferin":"Dieser K�mpfer")." kommt leider heute f�r eine Peinigung nicht mehr in Frage.`nBei ".($session['user']['sex']?"ihrem":"seinem")." jetzigen Zustand h�ttest du wahrlich keine Freude an deinem Werk und w�rdest nur unn�tig Zeit verschwenden.`n`nWillst du dir nicht jemand anderes stattdessen aussuchen ?");
        addnav("Ja...ok","housefeats.php?act=torture");
        addnav("Nee, keine Lust mehr","inside_houses.php");
    }
    
    
}
else if ($_GET[act]=="healing")
{
    // Sich im Kloster heilen lassen
    output("Du schleichst die h�lzernen Stufen zum Krankensaal hinauf und klagst den Nonnen dein Leid.`n");
    if ($session[user][hitpoints]>=$session[user][maxhitpoints]*0.9)
    {
        output("`7Doch die Ordenschwestern scheinen mit Wichtigerem besch�ftigt zu sein und schenken deinen Wehwehchen nur wenig Beachtung.`n");
    }
    else
    {
        output("`7Die Ordensschwestern legen dich sofort auf ein Bett und versorgen deine Verwundungen. Nach einiger Zeit f�hlst du dich wieder gesund.`n");
        output("`7Du verlierst einige Waldk�mpfe, regenerierst aber wieder komplett!");
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
    addnav("Zur�ck zum Haus","inside_houses.php");
    // dem Blutgott opfern
}
else if ($_GET[act]=="sacrifice")
{
    output("`7Du begibst dich mit schnellen festen Schritten zum Blutaltar...`n");
    if (($session[user][hitpoints]<($session[user][maxhitpoints]*0.5)) || ($session['user']['turns']<2))
    {
        output("`7...Und erntest nur schallendes Lachen vom finstren Herrn. Er will nur dein Blut und nicht dein Leben! Und wenn du ehrlich bist f�hlst du dich auch wirklich viel zu schlapp.`n`n`&Du solltest schon mehr als 50% deiner Lebenskraft und noch mindestens 2 Waldk�mpfe �brig haben!`n");
    }
    else
    {
        if ($session['user']['specialtyuses'][darkartuses]<9)
        {
            output("`7Und opferst dem Blutgott etwas deiner Lebenskraft. In Anerkennung deiner Gabe gew�hrt er dir `#1`7 zus�tzliche Anwendung in Dunklen K�nsten.`n");
            $session[user][hitpoints]*=0.85;
            $session['user']['specialtyuses'][darkartuses]+=1;
        }
        else
        {
            output("`7Und opferst dem Blutgott etwas deiner Lebenskraft. Jedoch scheint er dein Opfer aus irgendeinem Grund nicht angenommen zu haben.`n");
        }
    }
    addnav("Zur�ck zum Haus","inside_houses.php");
    
    //M�tterchens leckere (?) Kohlsuppe
}
else if ($_GET[act]=="soup")
{
    if ($session['user']['turns']>0)
    {
        output("`&Du stellst dich an den Tresen und orderst eine Sch�ssel Kohlsuppe.`nM�tterchen erkl�rt mit zittriger Stimme  : `n");
        output("`5Mein Geheimrezept beinhaltet einen ganz besonderen und seltenen magischen Pilz. Daher muss ich von dir pro Teller `^100 Goldm�nzen`5 verlangen.`nAuch gebe ich zu, dass meine Seekraft nicht mehr die Beste ist, und dass ich mich beim Pilzepfl�cken ab und an mal leicht vertue...`n");
        output("`&Willst du immer noch die Suppe kosten ?");
        addnav("Klar!","housefeats.php?act=soup2");
        addnav("Nein, zur�ck zum Haus","inside_houses.php");
    }
    else
    {
        output("`&F�r dich ist es schon zu sp�t etwas Warmes zu essen. Eigentlich willst du nur noch ins Bett...");
        addnav("Nagut...","inside_houses.php");
    }
}
else if ($_GET[act]=="soup2")
{
    if ($session['user']['gold']<100)
    {
        output("`&Die Alte kr�chzt :`n`5So schlecht ist meine Sehkraft auch noch nicht, dass ich Bettler und Schnorrer verkenne. Scher dich von dannen, sonst gibts was mit dem Nudelholz!");
        addnav("Schnell weg hier","inside_houses.php");
    }
    else
    {
        output("`&Die Alte nimmt dein Gold und serviert dir einen Suppenteller mit einer leicht gr�nlich blubbernden Br�he. Nach kurzem Z�gern probierst du einen gro�en L�ffel voll und ");
        switch (e_rand(1,25))
        {
        case 1 :
        case 2 :
        case 3 :
        case 4 :
            output("`&stellst fest, dass die Suppe gar nicht mal so �bel schmeckt. Aber eine besondere Wirkung stellst du auch nicht fest.");
            $session['user']['gold']-=100;
            $session['user']['turns']-=1;
            addnav("Zur�ck zum Haus","inside_houses.php");
            break;
        case 5 :
        case 6 :
        case 7 :
        case 8 :
            output("`&f�hlst dich kr�ftig und gest�rkt. Diese Suppe hat es in sich! Du bekommst `^einen Waldkampf`& dazu!");
            $session['user']['gold']-=100;
            $session['user']['turns']+=1;
            addnav("Zur�ck zum Haus","inside_houses.php");
            break;
        case 9 :
        case 10 :
        case 11 :
        case 12 :
        case 13 :
            output("`&bist von ihrem guten Geschmack derart �berw�ltigt, dass du den ganzen Teller bis auf den letzten Tropfen leerschl�rfst. Und schon bald versp�rst du das dringende Bed�rfnis deine Blase zu entleeren...`n@Du kannst heute nochmal das Toilettenh�uschen aufsuchen!`0");
            $session['user']['gold']-=100;
            $session['user']['turns']-=1;
            user_set_aei(array('usedouthouse' => 0));
            addnav("Zur�ck zum Haus","inside_houses.php");
            break;
        case 14 :
        case 15 :
            output("`&kommst zu dem Schluss, dass das alte M�tterchen diesmal wohl die Pilze mit Chilichoten verwechselt haben muss.`nDu schreist wie am Spiess und rennst wild mit den Armen rudernd durch den Ausgang des Gasthauses. Draussen angekommen wirfst du dich mit vollem Anlauf in die Pferdetr�nke.");
            output("`&Dabei wirst du von so ziemlich jedem gesehen, der dich hier kennt!`nDu verlierst `41 Charmpunkt`& und bist pitschnass...");
            $session['user']['gold']-=100;
            $session['user']['turns']-=1;
            $session['user']['charm']-=1;
            addnews("`^".$session['user']['name']."`^ nahm heute ein Bad in einer Pferdetr�nke. Wie peinlich!");
            addnav("Na toll","inside_houses.php");
            break;
        case 16 :
        case 17 :
            output("`&kommst zu dem Schluss, dass das alte M�tterchen diesmal wohl die Pilze mit Chilichoten verwechselt haben muss.`nDu schreist wie am Spiess und rennst wild mit den Armen rudernd durch den Ausgang des Gasthauses. Draussen angekommen wirfst du dich mit vollem Anlauf in die Pferdetr�nke.");
            output("`&Dabei st�sst du dir an einer Kante so heftig den Kopf, dass du das Bewusstsein verlierst. Wie so oft ist in solchen Momenten niemand sonst anwesend und so hauchst du langsam dein Leben aus.`nEs gibt wahrlich bessere M�glichkeiten zu sterben!`n`n`4Du verlierst 10% deiner Erfahrung!`&");
            $session['user']['gold']-=100;
            $session['user']['turns']-=1;
            $session['user']['hitpoints']=0;
            $session['user']['experience']*=0.9;
            $session['user']['alive']=0;
            addnews("`^".$session['user']['name']."`^ ist heute in einer Pferdetr�nke ersoffen. Dar�ber h�rt man sogar die Toten lachen.");
            addnav("Arrrrgh","shades.php");
            break;
        case 18:
        case 19:

        case 20:
        case 21:
            output("`&bist angenehm �berrascht durch den leckeren Geschmack. Du f�hlst dich satt.`nKohlsuppe ist gesund und h�lt schlank! Deswegen bekommst du `^2 Charmpunkte`& dazu.");
            $session['user']['charm']+=2;
            $session['user']['gold']-=100;
            $session['user']['turns']-=1;
            addnav("Juhu!","inside_houses.php");
            break;
        case 22:
        case 23:
        case 24:
        case 25:
            output("`&Irgendwie schmeckt die Suppe leicht nach Fisch... Du bekommst wahnsinnige Lust heute nochmal angeln zu gehen.`n`@Du erh�lst 3 zus�tzliche Angelrunden f�r heute!`0");
            $sql = "UPDATE account_extra_info SET fishturn=fishturn+3 WHERE acctid = ".$session['user']['acctid'];
            db_query($sql) or die(sql_error($sql));
            addnav("Petri heil!","inside_houses.php");
            break;
        }
    }
}
else if ($_GET[act]=="ritual")
{
    output("`&Du steigst die mindestens 5000 Stufen deines Turmes empor, um ein Ritual zur St�rkung deiner mystischen Kr�fte abzuhalten.`n");
    if ($session['user']['turns']<1)
    {
        output("`&Aber schon nach 5 Stufen bist du dir sicher, dass du es heute auf keinen Fall mehr bis zur Ebene auf der Spitze des Turmes schaffen wirst. Du bist einfach zu m�de.");
        addnav("Zur�ck","inside_houses.php");
    }
    else
    {
        if ($session['user']['gems']<1)
        {
            output("`&Oben angekommen bereitest du alles vor und stellst dann fest, dass du den Edelstein, den du unbedingt brauchst, nicht dabei hast.`nAlso bleibt dir nichts anderes �brig als die 5000 Stufen wieder hinabzusteigen.`nDu verlierst `4einen Waldkampf`&.");
            $session['user']['turns']-=1;
            addnav("Zur�ck","inside_houses.php");
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
                output("`&Dein Ritual hatte Erfolg!`nDeine mystischen Kr�ften wurden um `^5 Anwendungen`& aufgef�llt!");
                $session['user']['turns']-=1;
                $session['user']['gems']-=1;
                $session['user']['specialtyuses']['magicuses']+=5;
                break;
            case 5 :
            case 6 :
                output("`&Dein Ritual ging v�llig in die Hose. Nicht nur dass der Edelstein fort ist, auch hast du dich in deiner Machtbesessenheit derart verausgabt, dass du alle deine Anwendungen in mystischen Kr�ften f�r heute `4verloren`& hast!");
                $session['user']['turns']-=1;
                $session['user']['gems']-=1;
                $session['user']['specialtyuses']['magicuses']=0;
                break;
            case 7 :
                output("`&Dein Ritual verlief ausgezeichnet! Du `@erh�lst`& eine Stufe in mystischen Kr�ften dazu, sowie 3 Anwendungen!`n`n");
                $session['user']['turns']-=1;
                $session['user']['gems']-=1;
                $session['user']['specialtyuses']['magicuses']+=3;
                increment_specialty();
                break;
            }
            addnav("Zur�ck","inside_houses.php");
        }
    }
}
else if ($_GET[act]=="adventure")
{
    output("`&Du begibst dich zu den wackeren Abenteurern, die in einem kleinen Saal im Keller umgeben von allen m�glichen Karten und viel Plunder ihre Heldentaten zum Besten geben.`nIhr kommt schnell ins Gespr�ch und als das Thema beim `^Verlassenen Schloss`& angekommen ist, fl�stert dir einer der alten Abenteurer eine Geschichte in dein Ohr.`nWillst du dieser Erz�hlung lauschen (5 Waldk�mpfe opfern), um heute einmal mehr ins Schloss zu k�nnen ?");
    addnav("Ja","housefeats.php?act=adventure2");
    addnav("Nein","inside_houses.php");
}
else if ($_GET[act]=="adventure2")
{
    if ($session['user']['turns']<5)
    {
        output("`&W�hrend der langen Erz�hlung des Mannes schl�fst du pl�tzlich ein.`nSo wird es dir auch nichts bringen!");
        addnav("Zur�ck","inside_houses.php");
    }
    else
    {
        output("`&Der alte Abenteurer redet stundenlang, aber du kannst seiner Erz�hlung viele n�tzlich Informationen entnehmen.`n`^Du kannst heute ein weiteres Mal ins Schloss!`&");
        $session['user']['castleturns']++;
        $session['user']['turns']-=5;
        addnav("Zur�ck","inside_houses.php");
    }
}
else if ($_GET[act]=="gems")
{
    if (($session['user']['gold']>50000) || ($session['user']['gems']>1000))
    {
        output("`&Du machst dich auf zum Edelsteinh�ndler und musst feststellen, dass er gerade nicht da ist.`nVersuch es doch sp�ter noch einmal.`n");
    }
    else
    {
        output("`&Du n�herst dich dem etwas befremdlich aussehenden Edelsteinh�ndler, der gerade aus �bersee wieder angekommen ist.`nSofort zeigt er dir seine Waren und deutet an, dass er ebenso nicht abgeneigt ist etwas von dir anzukaufen.`n`nPro Edelstein verlangt er `^6000 Gold`&.`n`nPro Edelstein zahlt er `^600 Gold`&.");
        addnav("Kaufen");
        addnav("Einen kaufen","housefeats.php?act=gemsb&nmb=1");
        addnav("Drei kaufen","housefeats.php?act=gemsb&nmb=3");
        addnav("F�nf kaufen","housefeats.php?act=gemsb&nmb=5");
        addnav("Verkaufen");
        addnav("Einen verkaufen","housefeats.php?act=gemss&nmb=1");
        addnav("Drei verkaufen","housefeats.php?act=gemss&nmb=3");
        addnav("F�nf verkaufen","housefeats.php?act=gemss&nmb=5");
        addnav("Sonstiges");
    }
    addnav("Zur�ck zum Haus","inside_houses.php");
}
else if ($_GET[act]=="gemsb")
{
    $nmb=$_GET[nmb];
    output("`&Du teilst dem Mann mit, dass du gern `^$nmb ".($nmb==1?"Edelstein":"Edelsteine")."`& kaufen m�chtest.`n");
    $cost=6000*$nmb;
    if ($session['user']['gold']<$cost)
    {
        output("`&Aber das �bersteigt leider deine finanziellen F�higkeiten!");
    }
    else
    {
        output("`&Und nachdem du deine $cost Goldm�nzen auf den Tisch gelegt hast �berreicht er dir `^$nmb ".($nmb==1?"funkelnden Edelstein":"funkelnde Edelsteine")."`&.");
        $session['user']['gold']-=$cost;
        $session['user']['gems']+=$nmb;
    }
    addnav("Zur�ck zum Haus","inside_houses.php");
}
else if ($_GET[act]=="gemss")
{
    $nmb=$_GET[nmb];
    output("`&Du teilst dem Mann mit, dass du gern `^$nmb ".($nmb==1?"Edelstein":"Edelsteine")."`& verkaufen m�chtest.`n");
    $cost=600*$nmb;
    if ($session['user']['gems']<$nmb)
    {
        output("`&Aber leider hast du nicht genug Edelsteine dabei!");
    }
    else
    {
        output("`&Und nachdem du ihm $nmb ".($nmb==1?"Edelstein":"Edelsteine")." auf den Tisch gelegt hast �berreicht er dir `^$cost Goldm�nzen`&.");
        $session['user']['gold']+=$cost;
        $session['user']['gems']-=$nmb;
    }
    addnav("Zur�ck zum Haus","inside_houses.php");
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
        output("`&Die Tiertrainerin verlangt `^".$cost." Edelsteine`& f�r eine Stunde Ausdauertraining mit {$playermount['mountname']}.`n`&Auch weist sie dich darauf hin, dass dein Tier danach sehr ersch�pft und zu nichts mehr zu gebrauchen sein wird. Willst du es dennoch dem harten Training unterziehen ?");
        addnav("Ja","housefeats.php?act=trainanimal2");
        addnav("Nein","inside_houses.php");
    }
    else if ($_GET[act]=="trainanimal2")
    {
        $buff = unserialize($playermount['mountbuff']);
        if ($session['bufflist']['mount']['rounds'] < $buff['rounds'])
        {
            output("Die junge Frau schaut dein Tierchen mitleidig an.`n`6Tut mir leid, aber in dem Zustand wird es mir nach 5 Minuten zusammenbrechen.`nSorge bitte daf�r dass dein Tier gut erholt und gef�ttert ist bevor du es zu mir bringst!`&`nMit diesen Worten wendet sie sich ab.");
            addnav("Zur�ck zum Haus","inside_houses.php");
        }
        else if ($session['user']['gems']<$cost)
        {
            output("`&Peinlich ber�hrt stellst du fest, dass du dir diesen Luxus nicht leisten kannst...");
            addnav("Zur�ck zum Haus","inside_houses.php");
        }
        else
        {
            output("`&Die Trainerin nimmt {$playermount['mountname']} mit und verschwindet. Nach einer Stunde erh�lst du dein Tier zur�ck.`n`^Es wird nun t�glich eine Runde l�nger an deiner Seite k�mpfen!`&");
            $session['bufflist']['mount']['rounds'] = 0;
            $session['user']['gems']-=$cost;
            
            $newrounds=$rowm['mountextrarounds']+1;
            
            $sql = "UPDATE account_extra_info SET mountextrarounds=$newrounds WHERE acctid = ".$session[user][acctid];
            db_query($sql) or die(sql_error($sql));
            
            addnav("Zur�ck zum Haus","inside_houses.php");
        }
    }
    
}
else if ($_GET[act]=="workhard")
{
    output("`&Du trittst dem Gutshofverwalter entgegen und erkl�rst ihm, dass du kr�ftige H�nde hast und gern bei der Arbeit mithelfen w�rdest.");
    if ($session['user']['turns']<1)
    {
        output("`&Dieser grinst dich an und sagt : \"`tNeee, lass mal lieber. Du siehst schon ziemlich m�de aus!`&\"");
        addnav("Recht hat er...","inside_houses.php");
    }
    else
    {
        output("`&Dieser mustert dich erfreut und sagt : \"`tGut gut gut... Arbeit gibts hier immer zu Gen�ge. Such dir aus, was dir gef�llt.`nUnd ganz umsonst wirst du auch nicht arbeiten. Ich zahle dir einen fairen Anteil vom Gewinn aus... sagen wir 100 Goldm�nzen pro Runde, die du hier schuftest... Abgemacht?`n`n`n");
        output("`&Wieviele Runden m�chtest du hart arbeiten ?");
        
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
    output("`&Du rackerst f�r $trai Runden und erh�lst deinen gerechten Lohn von `^$reward`& Gold.");
    addnav("Zur�ck","inside_houses.php");
}
else if ($_GET[act]=="givepower")
{
    output("`&Du kniest dich vor den Ahnenschrein und versinkst in stiller Meditation.`nSchon bald siehst du Gesichter vor deinem geistigen Auge, die nach Erl�sung schreien. Du blickst ins Totenreich und kannst viele dir bekannte Krieger sehen.`n`n`n");
    
    if ($_GET[who]=="")
    {
        output("`&Nach wem m�chtest du suchen?`n`&");
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
        output("`&Du erblickst {$row['name']} `&unter den Toten.`n{$row['name']} `&hat insgesamt `^{$powers} Gefallen`&.`nWieviel deiner Gefallen m�chtest du bei einer Rate von 2:1 �bertragen ?`n`n`&Du hast derzeit `^".$session['user']['deathpower']." Gefallen`& bei Ramius.`n`n`n");
        output("<form action='housefeats.php?act=givepower2&id=".$row[acctid]."&dp=".$row[deathpower]."&who=".rawurlencode($row[login])."' method='POST'><input name='trai' id='trai'><input type='submit' class='button' value='Gefallen �bertragen'></form>",true);
        output("<script language='JavaScript'>document.getElementById('trai').focus();</script>",true);
        addnav("","housefeats.php?act=givepower2&id=".$row[acctid]."&dp=".$row[deathpower]."&who=".rawurlencode($row[login])."");
    }
    addnav("Zur�ck","inside_houses.php");
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
    output("`&Du opferst $powers Gefallen und ".$who."`& erh�lt $got Gefallen dazu.");
    $session['user']['deathpower']-=$powers;
    
    $sql = "UPDATE account_extra_info SET dpower=dpower+$got WHERE acctid = ".$id;
    db_query($sql) or die(sql_error($sql));
    systemmail($id,"`^Gefallen erhalten!`0","`&{$session['user']['name']}
    `6 hat am Ahnenschrein meditiert und dabei {$got}
    Gefallen bei Ramius f�r dich hinterlassen. Du solltest dich bei Gelegenheit erkenntlich zeigen.
    `nVergiss nicht dir die Gefallen am Ahnenschrein des Totenreichs abzuholen, da sie morgen sonst verloren sind.");
	
	debuglog('hat '.$got.' Gefallen �bertragen auf ',$id);
    }
    else
    {
    output("`&Du solltest schon etwas haben, bevor du ans opfern denkst.");
    }
    addnav("Zur�ck","inside_houses.php");
}
else if ($_GET[act]=="suicide")
{
    output("`&Du n�herst dich langsam und vorsichtig dem Opferschrein, wo du dich selbst ins Totenreich zu bef�rdern gedenkst.`n");
    if ($session['user']['turns']<1)
    {
        output("`&Doch leider kleben immer noch die �berreste des letzten Benutzers auf dem Opferschrein und bist weder bereit das Ding zu schrubben, noch dein Ableben auf diese Art zu gestalten.`n");
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
        addnews("`@".$session['user']['name']."`& hat sich an einem Opferschrein selbst get�tet!");
        addnav("Weiter","shades.php");
        break;
    case 4 :
    case 5 :
    case 6 :
    case 7 :
        output("`&Als es um dich herum schwarz wird driftet deine Seele langsam ins Totenreich hinab.`n`4Du wusstest, dass Selbstmord eine extrem feige Art ist vor seinen Problemen zu fl�chten, und so sieht das auch Ramius. Dein Ruf sinkt kr�ftig!`nDu bist tot!");
        $session['user']['reputation']=-40;
        $session['user']['hitpoints']=0;
        $session['user']['alive']=0;
        $session['user']['turns']-=1;
        addnews("`@".$session['user']['name']."`& nahm sich feige das Leben an einem Opferschrein!");
        addnav("Weiter","shades.php");
        break;
    case 8 :
        output("`@Zu dumm, dass es in solchen Situationen ab und an auch mal einen Retter gibt.`n`n`&Du wirst geheilt, aber zu deinem eigenen Schutz f�r den Rest des Tages in den Kerker geworfen.");
        $session['user']['turns']-=1;
        $session['user']['imprisonded']=1;
        addnews("`@".$session['user']['name']."`& hat versucht sich selbst zu t�ten, wurde aber gerettet und zum eigenen Schutz in den Kerker verbracht.");
        addnav("Waaaaas?!","prison.php");
        break;
    case 9 :
        output("`&Irgendwie gelingt dir das mit deinen Gegnern aber besser...`n`4Du �berlebst schwer verletzt.`n");
        $session['user']['hitpoints']=1;
        $session['user']['turns']-=1;
        addnav("Narf!","inside_houses.php");
        break;
    }
}
else if ($_GET[act]=="exchange")
{
    output("`&Der Oberaufseher f�hrt genau Buch �ber die H�ftlinge, die hier einsitzen.`nEr gibt dir aber deutlich zu verstehen, dass es ihm eigentlich recht egal ist wer in den Zellen sitzt, solange die Zahl der Kerkerinsassen stimmt und du ihm `^1 Edelstein`& gibst.`n`nDu �berlegst eine Weile und wirfst einen Blick auf das Gefangenenbuch`n`n");
    
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
        output($loggedin?"`#Wach`0":"`3Schl�ft`0");
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
    addnav("Zur�ck zum Haus","inside_houses.php");
    
}
else if ($_GET[act]=="exchange2")
{
    $sql = "SELECT xchangedtoday FROM account_extra_info WHERE acctid=".$session[user][acctid];
    $result = db_query($sql) or die(db_error(LINK));
    $rowx=db_fetch_assoc($result);
    if ($rowx['xchangedtoday']>0)
    {
        output("`&Der Oberaufseher kneift die Augen zusammen und f�hrt dich harsch an:`n`4\"Dich habe ich doch heute schonmal hier gesehen! Scher dich bloss fort!\"`n`&");
        addnav("�h ja","inside_houses.php");
    }
    else
    {
        if ($session['user']['gems']<1)
        {
            output("`&Leider kannst du dir es nicht leisten den Oberaufseher zu bestechen.`n");
            addnav("Zur�ck zum Haus","inside_houses.php");
        }
        else
        {
            $result = db_query("SELECT name,acctid,level,imprisoned FROM accounts WHERE acctid=$_GET[char]");
            $row = db_fetch_assoc($result);
            $lockup=getsetting("locksentence",4);
            
            if (($row['imprisoned']>0) && ($row['imprisoned']<$lockup))
            {
                output("`&".$row['name']."`& muss noch f�r `^".$row['imprisoned']." Tage`& im Kerker bleiben.`nDu �berlegst dich in die Zelle zu schleichen und f�r ".$row['name']." `& den Rest der Haftstrafe abzusitzen.`nWillst du das ?");
                addnav("Ja","housefeats.php?act=exchange3&days=$row[imprisoned]$&char=$row[acctid]");
                addnav("Bin ich denn bl�d ?","inside_houses.php");
            }
            else if (($row['imprisoned']<0) || ($row['imprisoned']>=$lockup))
            {
                output("".($row['name'])."`4 befindet sich im Hochsicherheitstrakt des Kerkers. Hier kannst du nichts tun.`&`n`n");
                addnav("Zur�ck zum Haus","inside_houses.php");
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
        output("`&Der Oberaufseher sch�ttelt nur belustig den Kopf`n`4\"Was ist das hier f�r ein Spiel?Meinst du das f�llt niemandem auf wenn immer die selben pl�tzlich erscheinen oder verschwinden ?\"`n`&");
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
            
            output("`&Du �ffnest ".($row['name'])."`& die Zellent�re und schl�pfst selbst hinein.");
            $session['user']['gems']--;
            $session['user']['imprisoned']=$days;
            
            // Beide als f�r heute ausgetauscht markieren
            $sql = "UPDATE account_extra_info SET xchangedtoday=1 WHERE acctid=".$session[user][acctid]." or acctid=".$row[acctid]."";
            db_query($sql);
            
            $sql = "UPDATE accounts SET imprisoned=0,location=0 WHERE acctid = ".$row['acctid']."";
            db_query($sql) or die(sql_error($sql));
            systemmail($row['acctid'],"`^Gefangenenaustausch!`0","`@{$session['user']['name']}
            `& hat sich bereit erkl�rt deine Haftstrafe f�r dich abzusitzen. Du bist frei! Du solltest dich dankbar erweisen!");
            $sql="INSERT INTO commentary (postdate,section,author,comment) VALUES (now(),'prison',".$row[acctid].",'/me `tverschwindet auf unerkl�rliche Weise.')";
            debuglog("�bernimmt die Haftstrafe von", $row['acctid']);
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
        output("`&Grinsend n�herst du dich den Zellen, um wieder einen der armen Gefangenen mit in den Keller zu schleppen.`nDoch der Oberaufsehen blickt dich nur an und sch�ttelt den Kopf.`n Vielleicht reicht es f�r heute mal..?`n");
        addnav("Bis Morgen!!","inside_houses.php");
    }
    else
    {
        output("`&Du steigst die Treppen des Kerkers hinab, bis tief in die Keller. Irgendwann glaubst du laute Stimmen zu h�ren, und als du n�her kommst erblickst du mehrere W�chter, die sich um einen riesigen K�fig versammelt haben, in dem sie die armen H�ftlinge zu blutigen K�mpfen zwingen.`nAls du dich entsetzt abwenden willst h�rst du einen der W�chter rufen : \"`@Ja! Gewonnen! Das Gold ist mein!`&\"`n\"`^Soso...`&\",denkst du dir,\"`^Ein Wettb�ro? Das macht die Sache schon wieder ganz anders!`&\"`n`n`&Du �berlegst auch eine Wette zu wagen.");
        addnav("Wetten","housefeats.php?act=arena2");
        addnav("Nichts f�r mich","inside_houses.php");
    }
}
else if ($_GET[act]=="arena2")
{
    output("`&W�hle deinen K�mpfer aus der Liste der H�ftlinge:`n`n");
    
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
        output($loggedin?"`#Wach`0":"`3Schl�ft`0");
        output("</td><td>",true);
        if ($row[abused]==0)
        {
            output("`@Ok`0");
        }
        else if ($row[abused]==1)
        {
            output("`4gequ�lt`0");
        }
        else if ($row[abused]==2)
        {
            output("`4abgek�mpft`0");
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
    addnav("Zur�ck zum Haus","inside_houses.php");
}
else if ($_GET[act]=="arena3")
{
    
    $result = db_query("SELECT name,accounts.acctid,dragonkills,sex,imprisoned,account_extra_info.abused FROM accounts LEFT JOIN account_extra_info ON account_extra_info.acctid=accounts.acctid WHERE login='$_GET[char]'");
    $row = db_fetch_assoc($result);
    
    if ($row['abused']==0)
    {
        
        output("`7Du zerrst ".($row['name'])."`7 grob aus der Zelle und bringst ".($row[sex]?"sie":"ihn")." runter in den Keller. Suche dir nun einen Gegner f�r ".($row['name'])." aus.`n`n ");
        
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
        
        
        addnav("Zur�ck zum Haus","inside_houses.php");
        
    }
    else
    {
        output("`&".($session['user']['sex']?"Diese K�mperin":"Dieser K�mpfer")." kommt leider heute f�r einen K�figkampf nicht mehr in Frage.`nBei ".($session['user']['sex']?"ihrem":"seinem")." jetzigen Zustand w�re dein Gold sicher verloren.`n`nWillst du dir nicht jemand Anderes stattdessen aussuchen ?");
        addnav("Ja","housefeats.php?act=arena2");
        addnav("Nein, zur�ck zum Haus","inside_houses.php");
    }
}
else if ($_GET[act]=="arena4")
{
    $result = db_query("SELECT login,name,accounts.acctid,dragonkills,sex,imprisoned,account_extra_info.abused FROM accounts LEFT JOIN account_extra_info ON account_extra_info.acctid=accounts.acctid WHERE accounts.acctid='$_GET[char]'");
    $row = db_fetch_assoc($result);
    
    
    $chance=$_GET[chance];
    $quote=5.5-($chance/20);
    output("`&Willst du ".$row['name']." `&wirklich im K�fig gegen ".(get_opponent($_GET[opp]))." `&antreten lassen?`n Die Siegeserwartung wird auf `@$chance %`& gesch�tzt, die Quote liegt bei `^$quote`&.`nWenn du wetten m�chtest, w�hle nun deinen Einsatz.");
    
    addnav("100 Gold setzen","housefeats.php?act=arena5&opp=$_GET[opp]&chance=$chance&set=100&char=".$row['acctid']."");
    addnav("200 Gold setzen","housefeats.php?act=arena5&opp=$_GET[opp]&chance=$chance&set=200&char=".$row['acctid']."");
    addnav("500 Gold setzen","housefeats.php?act=arena5&opp=$_GET[opp]&chance=$chance&set=500&char=".$row['acctid']."");
    addnav("1000 Gold setzen","housefeats.php?act=arena5&opp=$_GET[opp]&chance=$chance&set=1000&char=".$row['acctid']."");
    addnav("Anderer Gegner","housefeats.php?act=arena3&char=".$row['login']."");
    addnav("Anderer Gefangener","housefeats.php?act=arena2");
    addnav("Zur�ck zum Haus","inside_houses.php");
    
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
            output("`&Dabei schl�gt sich ".$row['name']."`& wacker und entscheidet den Kampf f�r sich!`n`@SIEG! ".($set*$quote)." Gold sind dein!`&");
            $session['user']['gold']+=($set*$quote);
            $mail="`@{$session['user']['name']}
            `& hat dich, w�hrend du im Kerker eingesessen hast, in den Keller gezerrt und zu einem blutigen K�figkampf gezwungen! Dein Gegner war ".get_opponent($opp)."`&. Du hast tapfer gek�mpft und gewonnen!`nDennoch war das nicht sehr nett...";
            addnav("Zur�ck zum Haus","inside_houses.php");
        }
        else
        {
            output("`&Dabei macht ".$row['name']."`& sichtlich keine gute Figur und verliert den Kampf.`n`4NIEDERLAGE! Du hast $set Gold verloren!`& ");
            $mail="`@{$session['user']['name']}
            `& hat dich, w�hrend du im Kerker eingesessen hast, in den Keller gezerrt und zu einem blutigen K�figkampf gezwungen! Dein Gegner war ".get_opponent($opp)."`&. Du wurdest windelweich gepr�gelt!`nVielleicht tr�stet es dich zu erfahren, dass ".$session['user']['name']." `&bei ".($session['user']['sex']?"ihrer":"seiner")." Wette auf deinen Sieg `^$set`& Gold verloren hat...";
            addnav("Zur�ck zum Haus","inside_houses.php");
        }
        $sql = "UPDATE account_extra_info SET cage_action=cage_action+1 WHERE acctid = ".$session['user']['acctid'];
        db_query($sql) or die(sql_error($sql));
        systemmail($row['acctid'],"`$K�figkampf!`0",$mail);
    }
    else
    {
        output("`&Zum Wetten braucht man Gold. Und das hast du leider nicht.`nAlso bleibt dir nichts Anderes �brig als dich peinlich ber�hrt davon zu schleichen.");
        addnav("Zur�ck zum Haus","inside_houses.php");
    }
}
else if ($_GET[act]=="bless")
{
    output("`&And�chtig kniest du vor dem gro�en, prunkvollen Altar nieder und willst gerade mit deinem Gebet beginnen, als dir ein ebenso gro�er und prunkvoller Opferstock auff�llt.`nNa vielleicht sind die G�tter mit ihrem Segen ja etwas spendabler, wenn der Kasten voll ist?`n`n`nWieviel m�chtest du spenden ?");
    
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
        output("`n`n`&Die G�tter meinen es heute gut mit dir.`nDeine Gebete wurden `@erh�rt`& und sie segnen dich mit `@andauernder Gesundheit`&!");
        
        $session['user']['gold'] -= $donate;
        
        $session[bufflist]['bless'] = array("startmsg"=>"`n`^Der g�ttliche Segen heilt deine Wunden !`n`n",
        "name"=>"`@G�ttlicher Segen",
        "rounds"=>20,
        "wearoff"=>"Der Segen ist vor�ber",
        "regen"=>$session['user']['level'],
        "effectmsg"=>"Der g�ttliche Segen l�sst einige deiner Wunden heilen.",
        "effectnodmgmsg"=>"Der Segen sch�tzt dich.",
        "activate"=>"roundstart");
        
    }
    else
    {
        output("`n`n`&Entweder war deine Spende nicht gro�z�gig genug, oder die G�tter hegen Groll gegen dich... wie auch immer, deine Gebete wurden `4nicht erh�rt`&!`n");
    }
    
    addnav("Zur�ck zum Haus","inside_houses.php");
    
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
        
        output("`&Du blickst dich interessiert nach einem Knappen um, der von nun f�r dich die Drecksar... �h.. der dich von nun an bei deinen Abenteuern begleiten und von dir lernen soll.`n`n");
        
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
                    $adj="d�rrer";
                    break;
                case 3 :
                    $adj="langw�chsiger";
                    break;
                case 4 :
                    $adj="kr�ftiger";
                    break;
                case 5 :
                    $adj="h�bscher";
                    break;
                case 6 :
                    $adj="stolzer";
                    break;
                case 7 :
                    $adj="vorlauter";
                    break;
                case 8 :
                    $adj="vertr�umter";
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
                    $adj="hinterh�ltiger";
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
                    $name="J��rg";
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
                    $name="Bj�rn";
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
                    $name="S�ren";
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
                
                output("\"`@Mein".($session[user][sex]?"e edle Dame,":" edler Herr,")." mein Name ist $name und ich habe schon viel von Eurer Tapferkeit geh�rt und m�chte Euch gern zur Seite stehen.`&\"`n`nWillst du diesen jungen Recken als deinen Knappen annehmen?`n");
                $name=urlencode($name);
                addnav("Jawohl","housefeats.php?act=disciple2&name=$name&state=$state");
                addnav("Nein!","inside_houses.php");
            }
            else
            {
                output("`&Nur leider will sich keiner mit einem so mittellosen Versager wie dir einlassen!`nBesorg dir erstmal die 20 Edelsteine, dann klappt es vielleicht...");
                addnav("Zur�ck","inside_houses.php");
            }
        }
        else
        {
            output("`&Doch leider will keiner der J�nglinge das Risiko eingehen von dir verheizt zu werden.`nDu solltest dingend etwas f�r dein Ansehen tun!");
            addnav("Zur�ck","inside_houses.php");
        }
    }
    else
    {
        output("`&Und die Tr�ume und Hoffnungen des armen `@$name`& entt�uschen?`nAlso wenn du ihn unbedingt loswerden willst musst du dir das schon etwas kosten lassen...`n`@$name`& braucht `^10 Edelsteine`& wenn er von nun allein allein klar kommen soll.`n`n`4Willst du `@$name`4 wirklich verstossen ?");
        $name=urlencode($name);
        addnav("Ja, 10 Edelsteine zahlen","housefeats.php?act=kickdisciple&name=$name");
        addnav("Nein! Zur�ck!","inside_houses.php");
    }
    
}
else if ($_GET[act]=="kickdisciple")
{
    $name=urldecode($_GET[name]);
    
    if ($session['user']['gems']<10)
    {
        output("`&Tja, das h�ttest du wohl gern... Du es dir nicht leisten `@$name`& einen angemessenen Start in seine Zukunft zu erm�glichen und so wirst du ihn noch eine weitere Weile an der Backe kleben haben...`n`nUnd versuch nicht ihn andersweitig \"loszuwerden\", der Orden hat ein Auge auf dich...");
        addnav("Zur�ck","inside_houses.php");
    }
    else
    {
        $session['user']['gems']-=10;
        output("`&So sei es! `@$name`& nimmt mit Tr�nen in den Augen die Edelsteine an und sucht nun allein in der Welt sein Gl�ck.");
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
    
    output("`&Du spendest deine 20 Edelsteine an den Orden und nimmst $name in deine Dienste auf.`n`n`@Du hast jetzt einen Knappen, der dich durch dick und d�nn begleiten wird. Aber gib gut auf ihn acht, da er nach einer Niederlage im Kampf verschleppt werden k�nnte!`&");
    
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
    addnav("Zur�ck zum Haus","inside_houses.php");
    
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
            output("`&Die Meister hie�en sicherlich nicht Meister, wenn ihre Ausbildung nicht das H�chste der Kampfeskunst darstellen w�rde, die es gibt.`nNat�rlich hat das auch seinen Preis, und in deinem Fall liegt dieser wohl ein wenig �ber deinen F�higkeiten...");
        }
        else
        {
            $session['user']['gold']-=$cost;
            output("`&Die Meister geben dir wenig Zeit zum Eingew�hnen, sondern fangen gleich mit ihrem Training an.`nNach mehreren Stunden schwei�treibender Arbeit und dutzenden blauen Flecken stellst du fest, ");
            
            $chance=e_rand(1,5);
            if ($session['user']['age']>50)
            {
                $chance=5;
            }
            
            switch ($chance)
            {
            case 1 :
            case 2 :
                output("`&dass du in der Offensive st�rker geworden bist.`n`@Dein Angriff steigt um 1 Punkt.`&`n");
                $session['user']['attack']+=1;
                break;
            case 3 :
            case 4 :
                output("`&dass du in der Defensive st�rker geworden bist.`n`@Deine Verteidigung steigt um 1 Punkt.`&`n");
                $session['user']['defence']+=1;
                break;
            case 5 :
                output("`&dass die ganze Schinderei vergebens war.`nDu konntest dir keine neuen Fertigkeiten aneignen.`n");
                break;
            }
            $sql = "UPDATE account_extra_info SET mastertrain=mastertrain+1 WHERE acctid = ".$session['user']['acctid'];
            db_query($sql) or die(sql_error($sql));
            
            output("`&`nDie Strapazen und Belastungen des harten Trainings sind an dir nicht spurlos v�r�ber gegangen : `4Du alterst k�rperlich um 1 Tag!`&`n");
            $session['user']['hitpoints']*=0.5;
            $session['user']['age']+=1;
        }
    }
    else
    {
        output("`&Hier kannst du keine neuen Fertigkeiten mehr erlangen. Die Meister haben dich genug geschunden und dir alles vermittelt was sie k�nnen.");
    }
    addnav("Zur�ck zum Haus","inside_houses.php");
    
}
else if ($_GET[act]=="smith")
{
    output("`&Hier in der Schmiede des S�ldnerlagers arbeiten qualifizierte Kr�fte Tag und Nach um die Waffen ihrer Kumpanen noch heimt�ckischer und ihre R�stungen noch stabiler zu machen.`nF�r einen relativ geringen Preis von `^3 Edelsteinen`& kannst auch du ihre Dienste in Anspruch nehmen.`n" );
    addnav("Waffe verbessern","housefeats.php?act=smith2&what=weapon");
    addnav("R�stung verbessern","housefeats.php?act=smith2&what=armor");
    addnav("Zur�ck zum Haus","inside_houses.php");
    
}
else if ($_GET[act]=="smith2")
{
    if ($session['user']['gems']<3)
    {
        output("`&Sch�m dich, das kannst du dir doch gar nicht leisten!");
        addnav("Zur�ck zum Haus","inside_houses.php");
    }
    else
    {
        output("`&Du �bergibst ".$session['user'][$_GET['what']]." an die Schmiede, ");
        
        if (strpos($session['user'][$_GET['what']],"+"))
        {
            output("`&doch sie k�nnen nichts weiter tun, da bereits daran geabeitet wurde.`nEntt�uscht wendest du dich ab.");
            addnav("Zur�ck zum Haus","inside_houses.php");
        }
        else
        {
            output("`&welche sich sofort flei�ig an die Arbeit machen.`n`n`^".$session['user'][$_GET['what']]."`^ wurde verbessert!`&");
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
            addnav("Zur�ck zum Haus","inside_houses.php");
        }
    }
    
}
else if ($_GET[act]=="orgy")
{
    if ($session['user']['gold']<3000)
    {
        output("`&Bei deiner jetzigen finanziellen Situation sind die Einzigen, die eine Orgie mit dir haben wollen die Ratten im Keller!`nAlso besorg schnell mal etwas Gold, sonst l�uft hier gar nichts...");
        addnav("Zur�ck zum Haus","inside_houses.php");
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
                addnav("Zur�ck zum Haus","inside_houses.php");
            }
            else
            {
                output("`@Du am�sierst dich so pr�chtig, dass deine Stimmung `^sehr gut`@ wird. Dadurch erh�lst du `^$bonus`@ zus�tzliche Waldk�mpfe!");
                $session['user']['spirits']=2;
                $session['user']['turns']+=$bonus;
                addnav("Yeah!","inside_houses.php");
            }
        }
        else
        {
            output("`&Da du gerade von den Toten auferstanden bist und du immer noch diesen fauligen Geschmack im Mund versp�rst, steht dir �berhaupt nicht der Sinn nach so etwas.");
            addnav("Zur�ck zum Haus","inside_houses.php");
        }
    }
    
    
}
else if ($_GET[act]=="beater")

{
    if ($session['user']['reputation']>0)
    {
        Output("`&Du suchst die N�he der �bel und finster aussehendsten Gestalten und blickst sie betroffen an. Der Kleinste von ihnen raunzt dir heiser zu`n\"`6So ? Hast du Probleme mit jemandem? Komm ruhig zum alten Onkel... denn Feind von irgendwem aus der Familie ist auch Feind vom alten Onkel... Gibst du `^2000 Gold und 2 Edelsteine`6 und Problem wird beseitigt...`&\"");
        addnav("Ja, Onkel","housefeats.php?act=beater2");
        addnav("Nein danke","inside_houses.php");
    }
    else
    {
        output("`&Du suchst die N�he einiger �bel und finster aussehender Gestalten und blickst sie betroffen an.`nDoch da dir dein schlechter Ruf vorauseilt zeigen diese relativ wenig Interesse an dir.");
        addnav("Zur�ck","inside_houses.php");
    }
    
}
else if ($_GET[act]=="beater2")
{
    If (($session['user']['gold']>=2000) && ($session['user']['gems']>=2))
    {
        Output("`&3 breitschultrige, eklig vernarbte Schl�ger wenden sich dir zu und schenken dir ihre Aufmerksamkeit.`nSobald du ihnen gesagt hast wen sie verkloppen sollen werden sie sich auf den Weg machen.`nBedenke jedoch, dass sie nur jemanden verpr�geln werden, der gerade schl�ft, lebt und der sich momentan nicht im Kerker befindet.`n`n");
        
        
        
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
                        output($row['beatenup']?"`4verpr�gelt`0":"`@Ok`0");
                    }
                    output("</td></tr>",true);
                }
                output("</table>",true);
            }
            addnav("Zur�ck ins Haus","inside_houses.php");
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
                output("`n`&\"`6Ehhhh! Was erlaube?? `&".$row['name']."`6 ist Freund von die Familie. Warum du spucke mitten in g�tige Gesicht von Onkel?`&\"");
                addnav("Ok ok","housefeats.php?act=beater2");
            }
            else
            {
                
                output("`6Alles klar! ein kleiner Trupp der �belsten Schl�ger macht sich bewaffnet mit Schlagring und Kn�ppel auf die Suche nach ".$row['name']." `&!`n`n");
                $session['user']['gold']-=2000;
                $session['user']['gems']-=2;
                
                $roll1 = e_rand(0,$row['level']);
                $roll2 = e_rand(0,$session['user']['level']);
                
                // Chance
                if ($roll2>$roll1)
                {
                    output("`6Sie �berraschen `7{$row['name']}
                    `6 im Schlaf und hinterlassen ein paar deftige Gr��e von dir!");
                    
                    
                    $mail="`&Letzte Nacht haben dich 3 Schl�ger im Schlaf �berrascht und sofort auf dich eingeschlagen. Du hattest nicht mal den Ansatz einer Chance dich zu wehren. Sie haben dir folgenden Zettel auf dein angeschwollenes Haupt geheftet : \"`^Leg dich nicht mit `&
					{$session['user']['login']}
                    `^ an!`&\"`nSie haben dich ziemlich �bel erwischt.";
                    systemmail($row['acctid'],"`$Du wurdest verpr�gelt!`0",$mail);
                    
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
                        $mail="`&Letzte Nacht haben dich 3 Schl�ger �berfallen und b�se auf dich eingeschlagen. Du hast dich tapfer gewehrt und konntest sie letztendlich vertreiben. Bei ihrer Flucht haben sie folgenden Zettel fallen gelassen : \"`^Leg dich nicht mit `&
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
                        `6irgendwie nicht finden konnten. Deine Bezahlung zur�ck zu verlangen traust du dich nat�rlich jetzt nicht.");
                        break;
                    case 3:
                    case 4:
                        output("`6Nach einer gewissen Zeit kommen sie wieder und erz�hlen dir stolz wie sie `7
						{$row['name']}
                        `6praktisch ohne Gegenwehr windelweich geschlagen haben. Als du stutzig wirst und nachfragst erh�rtet sich nach und nach der Verdacht, dass sich die Schl�ger den Falschen gegriffen haben. Du lobst sie nat�rlich f�r ihre Arbeit, bist aber schon ein wenig besch�mt.");
                        break;
                    case 5:
                        output("`6Die Zeit vergeht und vergeht und vergeht...`nUnd vergeht. Irgendwann akzeptierst du �bers Ohr gehauen worden zu sein und gehst betr�bt.");
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
        output("`&Die Schl�ger bestehen zun�chst darauf das Gold und die Edelsteine zu sehen.`nAber da du ihnen nicht geben kannst was sie wollen verpassen sie dir eine und lassen dich auf dem Boden liegen");
        $session['user']['hitpoints']*=0.8;
        addnav("Das tat weh!","inside_houses.php");
    }
    
}
else if ($_GET[act]=="dbite")
{
    $sql = "SELECT name,state FROM disciples WHERE master=".$session['user']['acctid']."";
    $result = db_query($sql) or die(db_error(LINK));
    $rowk = db_fetch_assoc($result);
    
    output("`4Alles klar!`nDu gehst mit `&".$rowk['name']." `4 in eine dunkle Ecke und st�rzt dich durstig auf den armen Kerl...`nEs wird eine Weile brauchen bis die Verwandlung abgeschlossen ist!");
    unset($session['bufflist']['decbuff']);
    $sql = "UPDATE disciples SET state=20 WHERE master = ".$session['user']['acctid'];
    db_query($sql) or die(sql_error($sql));
    addnav("Zur�ck","inside_houses.php");
    
}
else if ($_GET[act]=="checkfriend")
{
    $sql = "SELECT beatenup FROM account_extra_info WHERE acctid=".$session['user']['acctid']."";
    $result = db_query($sql) or die(db_error(LINK));
    $rowb = db_fetch_assoc($result);
    output("`&Du begibst dich zu den hochgef�hrlichen Schl�gertypen und fragst mit zittriger Stimme was denn die Familie so von dir h�lt. Der Kleinste von ihnen wendet sich dir zu nd kr�chzt heiser:`n");
    
    if ($rowb['beatenup']==0)
    {
        output("`6\"Oh weh, oh weh... sehe nicht aus gut f�r dich. Du nicht Freund von die Familie...\"`n`n`&Sieht so aus als w�re ein kleines \"Geschenk\" n�tig um ihre Gunst zu gewinnen.");
    }
    else if ($rowb['beatenup']==1)
    {
        output("`6\"Ahhh, ich sehe man hat getanzt wilde Polka auf deinem Gesicht! Du sollte wissen dass sich nicht gut lebt ohne Freundschaft von die Familie...\"`n`n`&Recht hat er...");
    }
    else if ($rowb['beatenup']>1)
    {
        output("`6\"Oh... `&".$session['user']['name']."`6, meine beste ".($session[user][sex]?"M�dchen ":"Junge ")."! Du gute Freund von die Familie.`nAber passe auf, nur noch Freund f�r `&".($rowb['beatenup']-1)." Tage`6!\"`&`n");
    }
    addnav("Zur�ck","inside_houses.php");
    
}
else if ($_GET[act]=="familygift")
{
    output("`&Aus dem Gekl�ngel der �bel vernarbten Schl�ger l�st sich ein kleiner, edel gekleideter Mann. Er spricht dich mit heiserer Stimme an:`n`6\"Soo ? Du wolle werde Freund von die Familie? Das ist sehr weise Entschluss und gut f�r deine h�bsch Gesicht, wenn du verstehe...`nWenn du schenke `^10 Edelsteine`6 du sein Freund von die Familie f�r eine Woche und du mich k�nne nenne deine Onkel! Ehh, was sage?\"`&`n");
    addnav("Si! Si!","housefeats.php?act=familygift2");
    addnav("Sissy...","inside_houses.php");
    
}
else if ($_GET[act]=="familygift2")
{
    if ($session['user']['gems']>=10)
    {
        output("`&Der Kleine, Alte l�chelt dich v�terlich an als du ihm die Edelsteine gibst. Er ruft laut :`n`6\"Ehh, h�rt alle her! `&".$session['user']['name']."`6 ist nun Freund von die Familie f�r eine Woche!");
        $session['user']['gems']-=10;
        $sql = "UPDATE account_extra_info SET beatenup=8 WHERE acctid = ".$session['user']['acctid'];
        db_query($sql) or die(sql_error($sql));
        addnav("Zur�ck","inside_houses.php");
    }
    else
    {
        output("`&Der Kleine, Alte sieht dass du dir das beim besten Willen nicht leisten kannst und dreht dir wortlos den R�cken zu.`nAnders seine breitschultigen Schl�ger, die erstmal eine Weile mit deinem Gesicht den Boden wischen, bevor sie von dir ablassen.`n`4Du verlierst Lebenspunkte!");
        $session['user']['hitpoints']*=0.8;
        addnav("Pfur�ck","inside_houses.php");
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
            output("`&Ein mageres M�nnlein mit blassem Gesicht sitzt an einem Tisch, weit abseits der Menge und nippt ab und zu an einem schalen Ale.`nAls du zu ihm gehts schaut er dich mit stechendem Blick an und erkl�rt dir die Regeln:`n`3\"Vor Euch seht Ihr Phiolen mit klarer Fl�ssigkeit. Allesamt gleichen sie sich �u�erlich. In diesen Phiolen befindet sich Wasser, allerdings in Einer auch ein geschmackloses Gift, das Euch innerhalb von Sekunden t�ten wird.`nWenn Ihr spielen wollt nehmt einfach eine der Phiolen und trinkt sie in einem Zug aus.\"`n`n");
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
            output("`&Du hast heute schon beim Orkisch Roulette abger�umt.`nWas willst du also hier?");
        }
    }
    else
    {
        output("`&Der magere Kerl ist nirgends zu sehen, obwohl du schw�ren k�nntest, dass er eben noch da war!");
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
            addnav("Zur�ck","housefeats.php?act=roulette");
        }
        else
        {
            addnav("Ins Haus","inside_houses.php");
        }
    }
    
}
else if ($_GET[act]=="sendtrophy")
{
    output("`3Der freundliche Herr vom Versandhandel ist bestens dazu geeignet Lieferungen f�r dich auszuf�hren.`n
			Pro St�ck verlangt er daf�r `^500 Goldm�nzen`3.`n
			Einen Waldkampf wirst du wahrscheinlich ben�tigen, um ihm die n�tigen Instruktionen zu erteilen.`n`n");
    if ($session['user']['gold']<500)
    {
        output("`3So viel Gold hast du nat�rlich gerade nicht bei dir, aber der gute Mann kann warten, bis du dir diesen Service leisten kannst.`n`n");
    }
	else if ($session['user']['turns'] <= 0)
    {
        output("`3Leider bist du gerade zu ersch�pft, um dich mit dem Versenden von Krimskram auseinanderzusetzen.`n`n");
    }
    else
    {
        output("`n`3Nach einem Blick auf deine Finanzen erkl�rt sich der Lieferant einverstanden.`nFolgende Dinge kann er f�r dich verschicken:`n`n");
            		
		$str_msg = 'Er deutet auf die paar altbackenen Kr�mel, die du aus deinen Taschen hervorkramst. Du wirst das Gef�hl nicht los, dass er dich damit veralbern will..';
		
		$arr_options = array('Versenden!'=>'&act=sendtrophy2');
				        
		item_show_invent(' owner='.$session['user']['acctid'].' AND distributor=1 AND deposit1=0', false, 0, 1, 1, $str_msg, $arr_options);
		
    }
	addnav('Zur�ck');
    addnav("Ins Haus","inside_houses.php");
    
}
else if ($_GET[act]=="sendtrophy2")
{
    $itemid=$_GET['id'];
    output("`3An wen soll denn das gute St�ck gehen ?`n`n");
    
    if ($_GET[who]=="")
    {
        output("`&Empf�nger suchen:`n`&");
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
        addnav("NEIN! ZUR�CK!","housefeats.php?act=sendtrophy2&id=$itemid");
    }
    addnav("Abbrechen");
    addnav("Anderes St�ck","housefeats.php?act=sendtrophy");
    addnav("Zur�ck ins Haus","inside_houses.php");
    
}
else if ($_GET[act]=="sendtrophy3")
{
    $itemid=$_GET['itemid'];
    $userid=$_GET['userid'];
    
    $sql = "SELECT acctid,name,login FROM accounts WHERE acctid=".$userid;
    $result = db_query($sql) or die(db_error(LINK));
    $row = db_fetch_assoc($result);
    
    $item = item_get(' id='.$itemid);
	
	addnav("Zur�ck ins Haus","inside_houses.php");
	
	if ($item['tpl_id'] == 'trph' && $row['acctid']==$item['hvalue'])
	{
		output("`&Also bitte! Du wirst ".$row['name']."`& doch nicht seine eigenen K�rperteile schicken wollen?!");
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
		`6 hat dir ein P�ckchen zukommen lassen. Darin befindet sich ".$item['name']."`6 und ein paar Fliegen.");
		
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
    output("`&Du n�herst dich den Abenteurern, die in einem kleinen Saal im Keller umgeben von allen m�glichen Karten und viel Plunder ihre Heldentaten zum Besten geben.`nDir kommt in den Sinn, dass diese M�nner vielleicht wissen k�nnten wohin man deinen armen Knappen verschleppt hat.`nDu beginnst ein Gespr�ch und sehr schnell wird deutlich, dass es dich einiges kosten wird Informationen zu erlangen.`nDie M�nner wollen `^".$cost."`& Goldst�cke sehen, bevor sie auch nur einen Ton von sich geben.`nWas tust du ?");
    addnav("".$cost." Gold geben","housefeats.php?act=searchdisciple2");
    addnav("Zur�ck ins Haus","inside_houses.php");
    
}
else if ($_GET[act]=="searchdisciple2")
{
    $cost=$session['user']['level']*1000;
    if ($session['user']['gold']<$cost)
    {
        output("`&Das �bersteige deine Mittel. Obwohl dein Schicksal den M�nnern sehr leid tut, werden sie dir dennoch nicht helfen!`n");
        addnav("Zur�ck ins Haus","inside_houses.php");
    }
    else
    {
        $sql = "SELECT name FROM disciples WHERE master=".$session['user']['acctid']."";
        $result = db_query($sql) or die(db_error(LINK));
        $rowk = db_fetch_assoc($result);
        $session['user']['gold']-=$cost;
        output("`&Mit kalter Miene legst du einen Sack mit ".$cost." Goldm�nzen auf den Tisch.`nSofort werden die M�nner aktiv. Nach einer ganzen Weile sagt dir einer von ihnen :`n\"`3Nun, mein Herr, es sieht so aus als ob Euer Sch�tzling in einer H�hle im Wald festgehalten und �belst gequ�lt wird.`nEr wird von sehr starken magischen Kreaturen bewacht! Nun, wir sind nur einfache Abenteurer und keine K�mpfer, und lebensm�de sind wir auch nicht. Ihr m�sstest ihn schon selbst dort herausholen. Aber eilt Euch, lange macht es der Knabe nicht mehr mit!`&\"`nEr zeigt dir eine gro�e Karte und deutet auf den Punkt an dem sich die H�hle befindet.`n`nWenn du `^".$rowk['name']."`& retten willst, dann musst du es jetzt tun!");
        addnav("Zur Rettung!","housefeats.php?act=searchdisciple4");
        addnav("Zur�ck ins Haus","housefeats.php?act=searchdisciple3");
    }
    
}
else if ($_GET[act]=="searchdisciple3")
{
    output("`&Damit hast du deine letzte Chance vertan deinen Knappen jemals lebend wieder zu sehen.");
    $sql="DELETE FROM disciples WHERE master=".$session['user']['acctid'];
    db_query($sql);
    addnav("Zur�ck ins Haus","inside_houses.php");
    
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
    output("`&Du nimmst deine Waffe, trinkst noch ein gutes Ale und machst dich auf in den Wald, zu der dir beschriebenen Stelle.`nTats�chlich findest du eine H�hle vor, die Abenteurer haben dich also nicht reingelegt.`nDoch was dich vor der H�hle erwartet ist einfach nur grauenvoll :`nEin �berm�ssig gro�er schwarzer Hund mit langen scharfen Z�hnen, dem kleine Flammen aus dem Maul schlagen. W�tend knurrt er dich an.`nWas nun ?");
    $badguy = array("creaturename"=>"`TH�llenhund`0"
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
    addnav("K�mpfen!","housefeats.php?op=fight");
    addnav("Fl�chten","housefeats.php?act=searchdisciple31");
    
}
else if ($_GET[act]=="searchdisciple5")
{
    output("`&Du l�sst die Kreatur hinter dir und steigst �ber ihren Kadaver in die dunkel H�hle.`nEine Fackel spendet dir etwas Licht. Langsam gehst du einen Gang hinab und erkennst voller Schrecken, dass dir eine gro�e menschen�hnliche Kreatur aus Lehm den Weg versperrt. Sofort beginnt der Klotz sich zu bewegen und seine schweren F�use gegen dich zu heben.`nWas tust du?");
    $badguy = array("creaturename"=>"`TLehmgolem-W�chter`0"
    ,"creaturelevel"=>$session['user']['level']
    ,"creatureweapon"=>"F�uste"
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
    addnav("K�mpfen!","housefeats.php?op=fight");
    addnav("Fl�chten","housefeats.php?act=searchdisciple31");
    
}
else if ($_GET[act]=="searchdisciple6")
{
    output("`&Nachdem du auch dieses Monstrum hinter dir gelassen hast folgst du dem Gang weiter. Es kommt dir etwas seltsam vor, dass die W�nde und der Boden gut gearbeitet sind. Wer k�nnte diese H�hle angelegt haben?`nDann stehst du vor einer gro�en T�r aus Holz, hinter der du Stimmen h�rst.`nWas tust du?");
    $badguy = array("creaturename"=>"`#Sklavenj�ger`0"
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
    addnav("Die T�r eintreten!","housefeats.php?op=fight");
    addnav("Fl�chten","housefeats.php?act=searchdisciple31");
    
}
else if ($_GET[act]=="searchdisciple7")
{
    $badguy = array("creaturename"=>"`#Anf�hrer der Sklavenj�ger`0"
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
    output("`&Du hast es geschafft! Du hast es tats�chlich geschafft!`nNachdem du diesen miesen Schurken den Rest gegeben hast erblickst du `^".$rowk['name']."`& abgemagert und mit deutlichen Spuren seiner schlechten Behandlung in einen kleinen Vogelk�fig gew�ngt in einer Ecke des Raumes.`nNachdem du ihn befreit hast f�llt er dir in die Arme.`n`nDu hast `^".$rowk['name']."`& zwar zur�ck bekommen, allerdings ist er nicht mehr so stark wie vorher.`nSein neuer Level ist ".$newlevel.".`n`nIn einer Kiste findest du 5000 Goldm�nzen.`n");
    $session['user']['gold']+=5000;
    $sql = "UPDATE disciples SET level=".$newlevel.",state=$newstate WHERE master=".$session[user][acctid]."";
    db_query($sql);
    $sql = "SELECT disciples_spoiled FROM account_extra_info WHERE acctid=".$session['user']['acctid']."";
    $result = db_query($sql) or die(db_error(LINK));
    $rows = db_fetch_assoc($result);
    $spoil=$rows['disciples_spoiled']-1;
    $sql = "UPDATE account_extra_info SET disciples_spoiled=$spoil WHERE acctid = ".$session['user']['acctid'];
    db_query($sql) or die(sql_error($sql));
    addnews($session['user']['name']."`2 hat ".($session[user][sex]?"ihren":"seinen")." Knappen `^".$rowk['name']."`2 aus den H�nden von Sklavenh�ndlern befreit!`0`n");
    // check best one
    $level=$rowk['level'];
    $sql = "SELECT id,level FROM disciples WHERE best_one=1";
    $result = db_query($sql) or die(db_error(LINK));
    $rowb = db_fetch_assoc($result);
    
    if ($level>$rowb['level'])
    {
        output("`n`^".$rowk['name']." ist st�rker als jeder andere Knappe im Land!`n");
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
            output("Die Kreatur spielt dir �bel mit und nimmt dich nach allen Regeln der Kunst auseinander.`n");
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
            addnav("T�gliche News","news.php");
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
