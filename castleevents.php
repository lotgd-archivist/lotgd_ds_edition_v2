<?
/**
* castleevents.php: Erweiterung zum Schloss, mit Zufallsbegegnungen und Ereignissen.
*			"Die Auserw�hlten" : erforfert thepath.php, modifiziert common.php, inn.php, prison.php,
*				abandonedcastle.php, castleevents.php, bio.php, prefs.php, special\vampire.php.
*				MOD by tcb, 15.5.05: Neues Ereignis "Corpse"
* @author Maris <Maraxxus@gmx.de>
* @version DS-E V/2
*/

require_once "common.php";
checkday();
page_header("Verlassenes Schloss");

if ($_GET['op']=="wham")
{
    output("<big><big><big>`4Wham!</big></big></big>`n",true);
    output("`3Als die Welt um Dich herum dunkel wird... siehst Du die langen Dornen die aus dem Boden hervorschnellten und dich aufspiessten`n");
    $session['user']['hitpoints']=0;
    $session['user']['experience']-=100;
    addnews("`%".$session['user']['name']."`5 ging in das verlassene Schloss, kam aber nie wieder lebendig heraus!.");
    addnav("Mist!","shades.php");
}
else if ($_GET['op']=="shoop")
{
    output("<big><big><big>`4Shoop!</big></big></big>`n",true);
    output("`3Als die Welt um dich herum dunkel wird, sieht Du Deinen K�rper langsam auf den Boden fallen. Dorthin, wo auch schon Dein Kopf liegt.`n");
    $session['user']['hitpoints']=0;
    $session['user']['experience']-=100;
    addnews("`%".$session['user']['name']."`5 ging in das verlassene Schloss, kam aber nie wieder lebendig heraus!.");
    addnav("Argh!","shades.php");
}
else if ($_GET['op']=="teleport")
{
    output("<big><big><big>`4Wusch!</big></big></big>`n",true);
    output("`3Dir wird pl�tzlich so schwindelig... alles um dich herum dreht sich.... als du wieder zu die kommst stehst du... auf dem Dorfplatz!!`n");
    
    /*$int_turns_to_take = (int)($session['user']['mazeturn']/12);
    output("Da der Besuch des Schlosses sehr langwierig und ersch�pfend war, verlierst Du $int_turns_to_take Waldk�mpfe`n");
    $session['user']['turns']-=$int_turns_to_take;
    if ($session['user']['turns']<0)
    {
        $int_turns_to_take -= $session['user']['turns'];
        $session['user']['turns']=0;
    }
    */
    
    addnews("`%".$session['user']['name']."`5 kam aus dem verlassenen Schloss mit leeren H�nden heraus!");
    $session['user']['mazeturn']=0;
    addnav("weiter","village.php");
}
else if ($_GET['op']=="hebel")
{
    $locale=$_GET['loc'];
    output("<big><big><big>`#3 Hebel!</big></big></big>`n",true);
    output("`&Pl�tzlich endet dein Weg in einer Sackgasse. Doch du kannst mitten in der Wand 3 Hebel erkennen : einen `4Roten`&, einen `9Blauen`& und einen `^Gelben`&. Welchen willst du davon bewegen ?  `n");
    addnav("Ziehe den...");
    addnav("roten Hebel.","castleevents.php?op=hebel2&loc=$locale");
    addnav("blauen Hebel","castleevents.php?op=hebel2&loc=$locale");
    addnav("gelben Hebel","castleevents.php?op=hebel2&loc=$locale");
}
else if ($_GET['op']=="hebel2")
{
    $locale=$_GET['loc'];
    switch (e_rand(1,4))
    {
    case 1 :
        output("`&Die Wand bewegt sich rumpelnd zur Seite. Im dahinter liegenden Gang erwartet dich ein praller Beutel gef�llt mit `#1500`& Goldm�nzen!`n");
        $session['user']['gold']+=1500;
        
        addnav("Jippie!","abandoncastle.php?loc=$locale");
        break;
    case 2 :
    case 3 :
        output("`&Die Wand bewegt sich rumpelnd zur Seite und gibt den Durchgang frei.`n");
        addnav("Puh!","abandoncastle.php?loc=$locale");
        break;
    case 4 :
        redirect("castleevents.php?op=wham");
        break;
    }
    
}
else if ($_GET['op']=="vamp")
{
    $locale=$_GET['loc'];
    output("<big><big><big>`#Eine Begegnung!</big></big></big>`n",true);
    output("`&Du irrst durch das verlassene Schloss, gut bedacht wohin du trittst und was du ber�hrst und h�rst ein leises Schaben. Als du aufschaust, blickst du in das Gesicht eines sehr alten Vampirs, der dich sp�ttich angrinst. Er scheint �ber und �ber mit Edelsteinen behangen.");
    if ($session['user']['marks']<32)
    {
        output("Du kannst ihn angreifen, versuchen zu fliehen und dich zu verstecken oder auf die Knie fallen und um dein Leben betteln. Was tust du? `n");
        addnav("Was nun?");
        addnav("Angreifen!","castleevents.php?op=vamp2&loc=$locale&cho=1");
        addnav("Fl�chten.","castleevents.php?op=vamp2&loc=$locale&cho=2");
        addnav("Betteln.","castleevents.php?op=vamp2&loc=$locale&cho=3");
    }
    else
    {
        output("`nDer Vampir erkennt an dir das Zeichen des Blutgottes und verneigt sich in Ehrfurcht vor dir. Dann gibt er dir 2 seiner Edelsteine und entfernt sich rasch.");
        $session['user']['gems']+=2;
        addnav("weiter","abandoncastle.php?loc=$locale");
    }
}
else if ($_GET['op']=="vamp2")
{
    $locale=$_GET['loc'];
    $choice=$_GET['cho'];
    
    $maxhp = get_max_hp();
    
    
    if ($choice==1)
    {
        $res=e_rand(1,10);
        if ($res>=6)
        {
            output("`&Ohne zu �berlegen greifst du an, doch bevor du dich versehen kannst befindest du dich schon hoffnungslos im Griff des Vampiren,");
            If ($session['user']['maxhitpoints'] <= $maxhp)
            {
                output(" der jedoch schnell wieder von dir abl�sst, da DU gewiss seinen Durst nicht stillen kannst. Er l�sst dich mit dem Schrecken allein und verschwindet.");
                addnav("Puh!","abandoncastle.php?loc=$locale");
            }
            else
            {
                output(" der dir in deinen Hals bei�t und dein Blut trinkt. Du sp�rst wie ein Teil deiner enormen Lebenskraft auf ihn �bergeht.");
                $losthp=$session['user']['maxhitpoints']*0.15;
                debuglog("verlor $losthp permanente LP beim Vampir im Schloss.");
                $session['user']['maxhitpoints']*=0.85;
                addnav("Argh!","abandoncastle.php?loc=$locale");
            }
        }
        else
        {
            
            output("`&Ohne lange zu �berlegen greifst du an und schl�gst ihm deine Waffe direkt ins Herz! Unter lauten Fauchen und Kreisen l�st sich der Vampir auf und du findest im Staubhaufen `^7 Edelsteine`& !");
            $session['user']['gems']+=7;
            
            
            
            
            
            
            addnav("Puh!","abandoncastle.php?loc=$locale");
        }
    }
    else if ($choice==2)
    {
        $res=e_rand(1,10);
        if ($res>=5)
        {
            output("Du drehst dich um und rennst so schnell du kannst, kauerst dich in einer Ecke zusammen, nur um zu bemerkten, dass der Vampir erneut direkt neben dir steht.`n");
            output("Er nimmt dir einen Teil deiner Lebenskraft, murmelt etwas, was wie Schande und j�mmerlich klingt, und gibt dir einen schnellen Tod.");
            $losthp=$session['user']['maxhitpoints']*0.01;
            debuglog("verlor $losthp permanente LP beim Vampir im Schloss.");
            $session['user']['maxhitpoints']*=0.99;
            $session['user']['hitpoints']=0;
            $session['user']['experience']-=100;
            addnews("`%".$session['user']['name']."`5 ging in das verlassene Schloss, kam aber nie wieder lebendig heraus!.");
            addnav("Nein!","shades.php");
        }
        else
        {
            if ($session['user']['hitpoints']>= $session['user']['maxhitpoints'])
            {
                output("Du rennst so schnell du kannst und kauerst dich in einer Ecke zusammen. Scheinbar uninteressiert an dir geht der Vampir weiter.");
                addnav("Puh!","abandoncastle.php?loc=$locale");
            }
            else
            {
                output("Du rennst so schnell du kannst und kauerst dich in einer Ecke zusammen. Angelockt durch das frische Blut deiner Verletzungen findet dich der Vampir nat�rlich und macht sich an sein finstres Werk.");
                $losthp=$session['user']['maxhitpoints']*0.01;
                debuglog("verlor $losthp permanente LP beim Vampir im Schloss.");
                $session['user']['maxhitpoints']*=0.99;
                $session['user']['hitpoints']=0;
                addnav("Neeeein!","shades.php");
            }
        }
    }
    else if ($choice==3)
    {
        output("Du kauerst dich auf dem Boden zusammen und bettelst um dein Leben.`n");
        $res=e_rand(1,10);
        if ($res>=5)
        {
            output("Der Vampir scheint dich vollkommen zu ignorieren und geht an dir vorbei.");
            addnav("Puh!","abandoncastle.php?loc=$locale");
        }
        else
        {
            output("Belustigt �ber eine derartige Vorstellung gew�hrt der Vampir deinen Wunsch und l�sst dich am Leben.");
            if ($session['user']['maxhitpoints'] <= $maxhp)
            {
                $losthp=$session['user']['maxhitpoints']*0.05;
                debuglog("verlor $losthp permanente LP beim Vampir im Schloss.");
                $session['user']['maxhitpoints']*=0.95;
                output("Allerdings bei�t er dich und trinkt eines wenig deines Blutes.`n");
                
            }
            addnav("Nein!","abandoncastle.php?loc=$locale");
        }
    }
    
}
else if ($_GET['op']=="truhe")
{
    $locale=$_GET['loc'];
    output("<big><big><big>`#Eine Truhe!</big></big></big>`n",true);
    output("`&Vor dir mitten im Gang erblickst du eine ge�ffnete Truhe, voll mit Gold und Edelsteinen! Aber irgendwie kommt dir die Sache seltsam vor... vielleicht eine Falle?`n");
    addnav("Was tun ?");
    addnav("Zugreifen","castleevents.php?op=truhe2&loc=$locale");
    addnav("Vorbei gehen","abandoncastle.php?loc=$locale");
}
else if ($_GET['op']=="truhe2")
{
    $locale=$_GET['loc'];
    switch (e_rand(1,5))
    {
    case 1 :
    case 2 :
        output("`&Du stopft dir die Taschen voll und gehst weiter. Insgesamt hast du `#1000`& Goldm�nzen und `#2`& Edelsteine erbeutet!`n");
        $session['user']['gold']+=1000;
        $session['user']['gems']+=2;
        
        
        
        addnav("Hurra!","abandoncastle.php?loc=$locale");
        break;
    case 3 :
    case 4 :
        output("`&Irgendwie scheinst du dir das alles nur eingebildet zu haben, denn die Truhe verschwindet als du hinein greifst.`n");
        addnav("Schade","abandoncastle.php?loc=$locale");
        break;
    case 5 :
        redirect("castleevents.php?op=shoop");
        break;
    }
}
else if ($_GET['op']=="web")
{
    $locale=$_GET['loc'];
    output("<big><big><big>`#Ein Spinnennetz!</big></big></big>`n",true);
    output("`&Schon von Weitem erkennst du ein �bermannsgro�es Spinnennetz mitten im vor dir liegenden Gang. Staub und Dreck, sowie die �berreste von Ratten, Flederm�usen und sogar das Skelett eines Menschen machen es gut sichtbar. Trotz Allem ist es dir im Weg und du musst dir etwas einfallen lassen. Entweder schl�gst du dir mit deiner Waffe den Weg frei, brennst es nieder oder du versuchst durch die gro�en Maschen des Netzes zu kriechen.`n");
    addnav("Was wirst du tun ?");
    addnav("Zerschlagen","castleevents.php?op=web2&cho=1&loc=$locale");
    addnav("Durchkriechen","castleevents.php?op=web2&cho=2&loc=$locale");
    addnav("Verbrennen","castleevents.php?op=web2&cho=3&loc=$locale");
}
else if ($_GET['op']=="web2")
{
    $locale=$_GET['loc'];
    $choice=$_GET['cho'];
    if ($choice==1)
    {
        output("`&Mit m�chtigen Hieben schl�gst du auf das Spinnennetz ein,");
        switch (e_rand(1,3))
        {
        case 1 :
        case 2 :
            output(" `&und es gelingt dir es komplett zu zerschlagen. Berge von Knochen und M�ll prasseln auf dich herab, doch unter dem ganzen Unrat kannst du immerhin noch insgesamt `^3000 Goldm�nzen`&zusammen raffen.");
            $session['user']['gold']+=3000;
            
            
            
            addnav("Her damit!","abandoncastle.php?loc=$locale");
            break;
        case 3 :
            output(" `&doch schon nach wenigen Schl�gen hat sich deine Waffe hoffnungslos im klebrigen Netz verheddert. Wie fest du auch ziehst und wie sehr du dich auch abm�hst, du bekommst sie nicht frei. Immerhin hast du er geschafft einen kleinen Durchgang in das Netz zu schlagen, deine Waffe kannst du allerdings vergessen!");
            
            item_set_weapon('Fists',0,0,0,0,2);
            
            $session['user']['donationspent']-=5;
            
            addnav("Weiter","abandoncastle.php?loc=$locale");
            break;
        }
        
    }
    else if ($choice==2)
    {
        output("`&Du suchst dir eine geeignete Stelle und krabbelst hindurch, ");
        switch (e_rand(1,3))
        {
        case 1 :
        case 2 :
            output("`&ein Schauder �berkommt dich, als du die armen Kreaturen siehst, die dabei nicht so erfolgreich waren wie du.");
            addnav("Weiter","abandoncastle.php?loc=$locale");
            break;
        case 3 :
            output("`&doch schon bald bleibt du an den klebrigen F�den des Netzes h�ngen und verstrickst dich hoffnungslos. Zu allem �bel n�hert sich dir auch noch die Spinne, die das Netz gewebt hat und macht kurzen Prozess mit dir.");
            $session['user']['hitpoints']=0;
            addnews("`%".$session['user']['name']."`^ ist im verlassenen Schloss als Spinnenfutter geendet.");
            addnav("Args!","shades.php");
            break;
        }
    }
    else
    {
        output("`&Du h�lst deine Fackel an das Netz, ");
        switch (e_rand(1,2))
        {
        case 1 :
            output("`&und der Durchgang ist frei.");
            addnav("Weiter","abandoncastle.php?loc=$locale");
            break;
        case 2 :
            output("`&und viele kleine brennende St�cke des Netzes legen sich auf deine Haut und verbrennen dich ein wenig.");
            $session['user']['hitpoints']*=0.9;
            addnav("Autsch!","abandoncastle.php?loc=$locale");
            break;
        }
    }
}
else if ($_GET['op']=="gang")
{
    $locale=$_GET['loc'];
    output("<big><big><big>`#Ein Geheimgang!</big></big></big>`n",true);
    output("`&Du entdeckst ein paar lose Bretter, die einen kniehohen Geheimgang verbergen. Nachdem du die Bretter entfernt hast kannst du erkennen, dass der Gang schier endlos ins Dunkel f�hrt. Wer wei� wo du da rauskommen wirst...`n");
    addnav("Was tun ?");
    addnav("Den Geheimgang nehmen","castleevents.php?op=gang2&loc=$locale");
    addnav("Vorbei gehen","abandoncastle.php?loc=$locale");
}
else if ($_GET['op']=="gang2")
{
    $locale=e_rand(1,143);
    redirect("abandoncastle.php?loc=$locale");
}
else if ($_GET['op']=="hole")
{
    $locale=$_GET['loc'];
    output("<big><big><big>`#Ein Loch in der Wand!<small><small><small>`n",true);
    output("`&Neben dir in der staubigen Wand entdeckst du pl�tzlich ein faustgro�es Loch, das scheinbar sehr tief in die Wand hineinragt. Es ist so tief, dass es gut und gern deinen ganzen Arm in sich aufnehmen k�nnte. Was sich darin wohl verbirgt ?`n");
    addnav("Was tun ?");
    addnav("Ins Loch greifen","castleevents.php?op=hole2&loc=$locale");
    addnav("Vorbei gehen","abandoncastle.php?loc=$locale");
}
else if ($_GET['op']=="hole2")
{
    $locale=$_GET['loc'];
    output("`&Du greifst in das Loch. Es ist widerlich glitschig und schleimig. Als dein Arm fast g�nzlich in dem Loch verschwunden ist, ");
    switch (e_rand(1,4))
    {
    case 1 :
    case 2 :
        output("`&f�hlt du etwas Weiches, das du schnell herausziehst. Es ist ein Beutel mit `#3 Edelsteinen`&!`n");
        $session['user']['gems']+=3;
        
        
        
        addnav("Hurra!","abandoncastle.php?loc=$locale");
        break;
    case 3 :
        output("`&�berkommt dich ein eiskalter Schauer. Du glaubst irgendetwas deine Handfl�che entlang gleiten gesp�rt zu haben. Schnell ziehst du den Arm wieder heraus, gl�cklich ihn noch zu haben.`n");
        addnav("�rgs","abandoncastle.php?loc=$locale");
        break;
    case 4 :
        output("`&bleibt dein Herz fast stehen als du einen eiskalten Griff an deinem Handgelenkt sp�rst. Irgendetwas oder jemand scheint deinen Arm im Loch festzuhalten! Dicke Schwei�perlen rinnen dein Gesicht herab als du sp�rst wie sich eine Klinge in deinen Arm bohrt. Du kannst nichts tun als abzuwarten bis du verblutet bist.`n");
        $session['user']['hitpoints']=0;
        addnews("`%".$session['user']['name']."`4 ist im verlassenen Schloss ausgeblutet!");
        addnav("Autsch!","shades.php");
        break;
    }
    
}
else if ($_GET['op']=="well")
{
    $locale=$_GET['loc'];
    output("<big><big><big>`#Eine Quelle!</big></big></big>`n",true);
    output("`&Vor dir sprudelt umgeben von einem kleinen marmornen Becken klares Wasser aus dem Boden und schimmert in allen Farben. Diese Quelle sieht sehr erfrischend aus. Vielleicht m�chtest du ja etwas trinken ?`n");
    addnav("Was tun ?");
    addnav("Trinken","castleevents.php?op=well2&loc=$locale");
    addnav("Lieber nicht","abandoncastle.php?loc=$locale");
}
else if ($_GET['op']=="well2")
{
    $locale=$_GET['loc'];
    switch (e_rand(1,4))
    {
    case 1 :
        output("`&Du nimmst einen Schluck aus der Quelle und f�hlst dich erfrischt. Gut erholt gehst du weiter.`n");
        $session['user']['hitpoints']=$session['user']['maxhitpoints'];
        addnav("Sehr sch�n!","abandoncastle.php?loc=$locale");
        break;
    case 2 :
        output("`&Der Schluck den du zu dir nimmst erf�llt deinen K�rper und deinen Geist mit neuer Kraft. Du f�hlst dich einfach gro�artig! Deine Lebenspunkte erh�hen sich `#permanent`& um 3 Punkte!`n");
        $session['user']['maxhitpoints']+=3;
        $session['user']['hitpoints']=$session['user']['maxhitpoints']*2;
        
        
        
        addnav("Herrlich!","abandoncastle.php?loc=$locale");
        break;
    case 3 :
        output("`&Das Wasser aus der Quelle schmeckt einfach nur furchtbar! Unter W�rgen und Erbrechen hustest du erst das Wasser wieder heraus, dann wird es dunkel...`n");
        $session['user']['hitpoints']=0;
        addnews("`%".$session['user']['name']."`^ hat im verlassenen Schloss vergiftetes Wasser getrunken!");
        addnav("Hallo Ramius!","shades.php");
        break;
    case 4 :
        output("`&Du trinkst einen Schluck, sp�rst aber keine besondere Ver�nderung an dir. Also gehst du weiter und l�sst die Quelle zur�ck...`n");
        addnav("Immerhin...","abandoncastle.php?loc=$locale");
        break;
    }
}
else if ($_GET['op']=="fairy")
{
    $locale=$_GET['loc'];
    output("<big><big><big>`#Eine Begegnung!</big></big></big>`n",true);
    output("`&Vor dir sind Ketten quer durch den Gang gespannt und versperren dir den Weg. Auch kannst du erkennen, dass die Ketten wohl nur den Zweck erf�llen eine kleine Fee in der Mitte des Ganges unter gro�en Qualen festzuhalten. Jetzt ereilen dich ihre schrillen Schreie. Du k�nntest die Ketten von der Wand losmachen und das Wesen befreien oder seinem Leben mit deiner Waffe ein Ende setzen um vorbeit zu kommen.`n");
    addnav("Willst du ?");
    addnav("Die Ketten l�sen","castleevents.php?op=fairy2&cho=1&loc=$locale");
    addnav("Die Fee zerteilen","castleevents.php?op=fairy2&cho=2&loc=$locale");
}
else if ($_GET['op']=="fairy2")
{
    $locale=$_GET['loc'];
    if ($_GET['cho']==1)
    {
        output("`&Du entfernst die Ketten von der Wand und befreist das kleine Wesen. Die Fee hebt ihren Kopf und schaut dich an.`n");
        switch (e_rand(1,4))
        {
        case 1 :
        case 2 :
        case 3 :
            output("`#Habt Dank f�r meine Rettung!`& fiepst sie dir leise und geschw�cht zu, `#Ich hing hier nun schon viele Tage und Wochen! Ohne Euch h�tte ich das nicht �berstanden!`&. Zum Dank f�r ihre Rettung schenkt sie dir `^3 Edelsteine`&.");
            $session['user']['gems']+=3;
            
            
            
            addnav("Weitergehen","abandoncastle.php?loc=$locale");
            break;
        case 4 :
            output("`4 Wahahaha!!! `& grollt das Wesen mit finstrer Stimme, `4 Du und Deinesgleichen seid an Dummheit und Gutgl�ubigkeit kaum zu �bertreffen!`& W�hrend die Fee diese Worte spricht beginnt sie unnat�rlich zu wachsen, sie wird immer gr��er und h�sslicher, lange spitze H�rner ragen aus ihrem Kopf und schlie�lich greift dich das Wesen an.");
            addnav("Na toll...","mazemonster.php?op=minotaur");
            break;
        }
    }
    else
    {
        output("Mit einem sehr schlechten Gef�hl im Bauch sch�lgst du die schwache, angekettete Fee in St�cke.");
        switch (e_rand(1,4))
        {
        case 1 :
        case 2 :
            output("Ihre Todesschreie werden dich noch sehr lange das Nachts verfolgen! Du f�hlt dich nur noch h�sslich!");
            $session['user']['charm']-=2;
            debuglog("verlor 2 Charme f�r das Zerhacken einer Fee.");
            addnav("Weitergehen","abandoncastle.php?loc=$locale");
            break;
        case 3 :
        case 4 :
            output("Dir stehen Tr�nen in den Augen, doch du bist dir sicher, dass dies das Beste f�r das arme geschundene Wesen war. Schwerm�tig gehst du weiter.");
            addnav("Weitergehen","abandoncastle.php?loc=$locale");
            break;
            
        }
    }
}
else if ($_GET['op'] == "corpse")
{
    
    output("<big><big><big>`#Etwas Besonderes!</big></big></big>`n",true);
    
    $locale = $session['user']['pqtemp'];
    
    if ($_GET['cho'] == "")
    {
        
        output("`&In einem dunklen Gang stolperst du �ber ein am Boden liegendes Etwas. Schaudernd ziehst du deinen Fu� zur�ck und dr�ckst dich an die Wand. Aber, so denkst du nach einiger Zeit, vielleicht ist das ein Schatz? Mit vielen, vielen Edelsteinen?`n Was willst du tun?");
        
        addnav("Das Etwas n�her betrachten","castleevents.php?op=corpse&cho=1&loc=".$locale);
        
        addnav("Weitergehen","abandoncastle.php?loc=".$locale);
    }
    else if ($_GET['cho'] == 1)
    {
        
        output("`&Du b�ckst dich z�gernd, tippst es mit deinem ".$session['user']['weapon']." an und...");
        
        switch (e_rand(1,5) )
        {
        case 1:
        case 2:
            
            $sql = "SELECT name, weapon FROM accounts WHERE alive=0 AND dragonkills > 9 ORDER BY rand() LIMIT 1";
            $res = db_query($sql);
            
            $gems = e_rand(2,4);
            
            if (!db_num_rows($res))
            {
                output("`&bemerkst: Das war mal die Leiche eines unvorsichtigen Abenteurers! Daneben liegen Knochen und, was dich besonders freut, `^".$gems."`& Edelsteine!");
            }
            else
            {
                $p = db_fetch_assoc($res);
                
                output("`&bemerkst: Das ist die Leiche eines unvorsichtigen Abenteurers! Nach einer genaueren Untersuchung erkennst du ".$p['name']."`&... neben der Leiche liegen ein ".$p['weapon']."`& und, was dich besonders freut, `^".$gems."`& Edelsteine!");
            }
            
            $session['user']['gems'] += $gems;
            
            addnav("Sehr Sch�n!","abandoncastle.php?loc=".$locale);
            
            break;
            
        case 3:
            
            output("`&f�hrst erschrocken zur�ck, als es dich anfaucht! Eine verdorrte Seele st�rzt sich auf dich.");
            
            addnav("Auf in den Kampf!","mazemonster.php?op=devoured_soul");
            
            break;
            
        case 4:
        case 5:
            
            redirect("castleevents.php?op=wham&loc=".$locale);
            
            break;
            
        }
        
    }
    
}
else if ($_GET['op']=="earthshrine")
{
    $mark=$session['user']['marks'];
    if ($mark>=16)
    {
        $mark-=16;
    }
    if ($mark>=8)
    {
        $mark-=8;
    }
    if ($mark>=4)
    {
        $mark-=4;
    }
    if ($mark>=2)
    {
        $mark-=2;
    }
    
    $locale=$_GET['loc'];
    output("<big><big><big>`#Ein Schrein!</big></big></big>`n",true);
    output("`&Du siehst am Rande des Ganges einen kleinen `^Erdschrein`&. Ein Knistern liegt in der Luft als du dich ihm n�herst. An der Wand �ber dem Schrein prangt eine Glyphe. Sie gl�ht so stark und strahlt eine derartige Hitze aus, dass du meinen k�nntest, sie sei aus fl�ssigem Metall!`n");
    addnav("Was tust du ?");
    if ($mark<1)
    {
        addnav("Die Glyphe ber�hren","castleevents.php?op=earthshrine2&loc=$locale");
    }
    addnav("Vorbei gehen","abandoncastle.php?loc=$locale");
}
else if ($_GET['op']=="earthshrine2")
{
    $locale=$_GET['loc'];
    output("`&Mit stark pochendem Herzen trittst du an den Schrein und dr�ckt deinen Arm gegen die Glyphe. Ein grauenvoller Schmerz durchzuckt deinen ganzen K�rper als sich die Glyphe in dein Fleisch brennt.");
    $session['user']['maxhitpoints']-=5;
    debuglog("Gab 5 permanente LP am Erdschrein.");
    switch (e_rand(1,2))
    {
    case 1 :
        output("`&Obwohl du mit aller Gewalt ziehst kannst du deinen Arm nicht von der Glyphe l�sen.`n `4 Unw�rdiger Narr! `& h�rst du es noch dumpf schallen bevor dein ganzer K�rper vergl�ht... ");
        $session['user']['hitpoints']=0;
        addnews("`%".$session['user']['name']."`^ ist im verlassenen Schloss zu Asche verbrannt!");
        addnav("Weiter","shades.php");
        break;
    case 2 :
        output("`&Unter gewaltigen Schmerzen bleibst du dennoch standhaft und als du deinen Arm von der Glyphe l�sen kannst, stellst du ein Mal fest, dass sich tief in dein Fleisch gebrannt hat.`n");
        output("Du hast das `^Mal der Erde`& erlangt!");
        $session['user']['marks']+=1;
        addnews("`@".$session['user']['name']."`& hat das `^Mal der Erde`& erlangt!");
        addnav("Weiter","abandoncastle.php?loc=$locale");
        break;
        
    }
    
}
else if ($_GET['op']=="airshrine")
{
    $mark=$session['user']['marks'];
    if ($mark>=16)
    {
        $mark-=16;
    }
    if ($mark>=8)
    {
        $mark-=8;
    }
    if ($mark>=4)
    {
        $mark-=4;
    }
    
    $locale=$_GET['loc'];
    output("<big><big><big>`#Ein Schrein!</big></big></big>`n",true);
    output("`&Du siehst am Rande des Ganges einen kleinen `9Luftschrein`&. Ein Knistern liegt in der Luft als du dich ihm n�herst. An der Wand �ber dem Schrein prangt eine Glyphe. Sie gl�ht so stark und strahlt eine derartige Hitze aus, dass du meinen k�nntest, sie sei aus fl�ssigem Metall!`n");
    addnav("Was tust du ?");
    if ($mark<2)
    {
        addnav("Die Glyphe ber�hren","castleevents.php?op=airshrine2&loc=$locale");
    }
    addnav("Vorbei gehen","abandoncastle.php?loc=$locale");
}
else if ($_GET['op']=="airshrine2")
{
    $locale=$_GET['loc'];
    output("`&Mit stark pochendem Herzen trittst du an den Schrein und dr�ckt deinen Arm gegen die Glyphe. Ein grauenvoller Schmerz durchzuckt deinen ganzen K�rper als sich die Glyphe in dein Fleisch brennt.");
    $session['user']['maxhitpoints']-=5;
    debuglog("Gab 5 permanente LP am Luftschrein.");
    switch (e_rand(1,5))
    {
    case 1 :
    case 2 :
    case 3 :
    case 4 :
        output("`&Obwohl du mit aller Gewalt ziehst kannst du deinen Arm nicht von der Glyphe l�sen.`n `4 Unw�rdiger Narr! `& h�rst du es noch dumpf schallen bevor dein ganzer K�rper vergl�ht... ");
        $session['user']['hitpoints']=0;
        addnews("`%".$session['user']['name']."`^ ist im verlassenen Schloss zu Asche verbrannt!");
        addnav("Weiter","shades.php");
        break;
    case 5 :
        output("`&Unter gewaltigen Schmerzen bleibst du dennoch standhaft und als du deinen Arm von der Glyphe l�sen kannst, stellst du ein Mal fest, dass sich tief in dein Fleisch gebrannt hat.`n");
        output("Du hast das `9Mal der Luft`& erlangt!");
        $session['user']['marks']+=2;
        addnews("`@".$session['user']['name']."`& hat das `9Mal der Luft`& erlangt!");
        addnav("Weiter","abandoncastle.php?loc=$locale");
        break;
        
    }
    
}
else if ($_GET['op']=="fireshrine")
{
    $mark=$session['user']['marks'];
    if ($mark>=16)
    {
        $mark-=16;
    }
    if ($mark>=8)
    {
        $mark-=8;
    }
    
    $locale=$_GET['loc'];
    output("<big><big><big>`#Ein Schrein!</big></big></big>`n",true);
    output("`&Du siehst am Rande des Ganges einen kleinen `4Feuerschrein`&. Ein Knistern liegt in der Luft als du dich ihm n�herst. An der Wand �ber dem Schrein prangt eine Glyphe. Sie gl�ht so stark und strahlt eine derartige Hitze aus, dass du meinen k�nntest, sie sei aus fl�ssigem Metall!`n");
    addnav("Was tust du ?");
    if ($mark<4)
    {
        addnav("Die Glyphe ber�hren","castleevents.php?op=fireshrine2&loc=$locale");
    }
    addnav("Vorbei gehen","abandoncastle.php?loc=$locale");
}
else if ($_GET['op']=="fireshrine2")
{
    $locale=$_GET['loc'];
    output("`&Mit stark pochendem Herzen trittst du an den Schrein und dr�ckt deinen Arm gegen die Glyphe. Ein grauenvoller Schmerz durchzuckt deinen ganzen K�rper als sich die Glyphe in dein Fleisch brennt.");
    debuglog("Gab 5 permanente LP am Feuerschrein.");
    $session['user']['maxhitpoints']-=5;
    switch (e_rand(1,3))
    {
    case 1 :
    case 2 :
    case 3 :
    case 4 :
        output("`&Obwohl du mit aller Gewalt ziehst kannst du deinen Arm nicht von der Glyphe l�sen.`n `4 Unw�rdiger Narr! `& h�rst du es noch dumpf schallen bevor dein ganzer K�rper vergl�ht... ");
        $session['user']['hitpoints']=0;
        addnews("`%".$session['user']['name']."`^ ist im verlassenen Schloss zu Asche verbrannt!");
        addnav("Weiter","shades.php");
        break;
    case 5 :
        output("`&Unter gewaltigen Schmerzen bleibst du dennoch standhaft und als du deinen Arm von der Glyphe l�sen kannst, stellst du ein Mal fest, dass sich tief in dein Fleisch gebrannt hat.`n");
        output("Du hast das `4Mal des Feuers`& erlangt!");
        $session['user']['marks']+=4;
        addnews("`@".$session['user']['name']."`& hat das `4Mal des Feuers`& erlangt!");
        addnav("Weiter","abandoncastle.php?loc=$locale");
        break;
        
    }
    
}
else if ($_GET['op']=="watershrine")
{
    $mark=$session['user']['marks'];
    if ($mark>=16)
    {
        $mark-=16;
    }
    
    $locale=$_GET['loc'];
    output("<big><big><big>`#Ein Schrein!</big></big></big>`n",true);
    output("`&Du siehst am Rande des Ganges einen kleinen `@Wasserschrein`&. Ein Knistern liegt in der Luft als du dich ihm n�herst. An der Wand �ber dem Schrein prangt eine Glyphe. Sie gl�ht so stark und strahlt eine derartige Hitze aus, dass du meinen k�nntest, sie sei aus fl�ssigem Metall!`n");
    addnav("Was tust du ?");
    if ($mark<8)
    {
        addnav("Die Glyphe ber�hren","castleevents.php?op=watershrine2&loc=$locale");
    }
    addnav("Vorbei gehen","abandoncastle.php?loc=$locale");
}
else if ($_GET['op']=="watershrine2")
{
    $locale=$_GET['loc'];
    output("`&Mit stark pochendem Herzen trittst du an den Schrein und dr�ckt deinen Arm gegen die Glyphe. Ein grauenvoller Schmerz durchzuckt deinen ganzen K�rper als sich die Glyphe in dein Fleisch brennt.");
    debuglog("Gab 5 permanente LP am Wasserschrein.");
    $session['user']['maxhitpoints']-=5;
    switch (e_rand(1,5))
    {
    case 1 :
    case 2 :
    case 3 :
    case 4 :
        output("`&Obwohl du mit aller Gewalt ziehst kannst du deinen Arm nicht von der Glyphe l�sen.`n `4 Unw�rdiger Narr! `& h�rst du es noch dumpf schallen bevor dein ganzer K�rper vergl�ht... ");
        $session['user']['hitpoints']=0;
        addnews("`%".$session['user']['name']."`^ ist im verlassenen Schloss zu Asche verbrannt!");
        addnav("Weiter","shades.php");
        break;
    case 5 :
        output("`&Unter gewaltigen Schmerzen bleibst du dennoch standhaft und als du deinen Arm von der Glyphe l�sen kannst, stellst du ein Mal fest, dass sich tief in dein Fleisch gebrannt hat.`n");
        output("Du hast das `@Mal des Wassers`& erlangt!");
        $session['user']['marks']+=8;
        addnews("`@".$session['user']['name']."`& hat das `@Mal des Wassers`& erlangt!");
        addnav("Weitergehen","abandoncastle.php?loc=$locale");
        break;
        
    }
    
}
else if ($_GET['op']=="spiritshrine")
{
    $mark=$session['user']['marks'];
    
    $locale=$_GET['loc'];
    output("<big><big><big>`#Ein Schrein!</big></big></big>`n",true);
    output("`&Du siehst am Rande des Ganges einen kleinen `tGeistschrein`&. Ein Knistern liegt in der Luft als du dich ihm n�herst. An der Wand �ber dem Schrein prangt eine Glyphe. Sie gl�ht so stark und strahlt eine derartige Hitze aus, dass du meinen k�nntest, sie sei aus fl�ssigem Metall!`n");
    addnav("Was tust du ?");
    if ($mark<16)
    {
        addnav("Die Glyphe ber�hren","castleevents.php?op=spiritshrine2&loc=$locale");
    }
    addnav("Vorbei gehen","abandoncastle.php?loc=$locale");
}
else if ($_GET['op']=="spiritshrine2")
{
    $locale=$_GET['loc'];
    output("`&Mit stark pochendem Herzen trittst du an den Schrein und dr�ckt deinen Arm gegen die Glyphe. Ein grauenvoller Schmerz durchzuckt deinen ganzen K�rper als sich die Glyphe in dein Fleisch brennt.");
    debuglog("Gab 5 permanente LP am Geistschrein.");
    $session['user']['maxhitpoints']-=5;
    switch (e_rand(1,5))
    {
    case 1 :
    case 2 :
    case 3 :
    case 4 :
        output("`&Obwohl du mit aller Gewalt ziehst kannst du deinen Arm nicht von der Glyphe l�sen.`n `4 Unw�rdiger Narr! `& h�rst du es noch dumpf schallen bevor dein ganzer K�rper vergl�ht... ");
        $session['user']['hitpoints']=0;
        addnews("`%".$session['user']['name']."`^ ist im verlassenen Schloss zu Asche verbrannt!");
        addnav("Weiter","shades.php");
        break;
    case 5 :
        output("`&Unter gewaltigen Schmerzen bleibst du dennoch standhaft und als du deinen Arm von der Glyphe l�sen kannst, stellst du ein Mal fest, dass sich tief in dein Fleisch gebrannt hat.`n");
        output("Du hast das `tMal des Geistes`& erlangt!");
        $session['user']['marks']+=16;
        addnews("`@".$session['user']['name']."`& hat das `tMal des Geistes`& erlangt!");
        addnav("Weitergehen","abandoncastle.php?loc=$locale");
        break;
        
    }
    
}

page_footer();
?>