<?
/*/ Projekt Special-Modules

SQL:

DROP TABLE IF EXISTS `specialty`;
CREATE TABLE `specialty` (
`specid` int(5) UNSIGNED NOT NULL auto_increment,
`filename` varchar(50) NOT NULL,
`specname` varchar(50) NOT NULL,
`usename` varchar(50) NOT NULL,
`author` varchar(50) NOT NULL,
`activ` enum('0','1'),
PRIMARY KEY (`specid`)
) TYPE=MyISAM;

ALTER TABLE accounts ADD `specialtyuses` text;
/*/

// Modified by Maris

$file = "specialty_thievery";

function specialty_thievery_info()
{
    global $info,$file;
    $info = array(
    "author"=>"Eric Stevens, to module by Eliwood",
    "version"=>"1.2",
    "download"=>"",
    "filename"=>$file,
    "specname"=>"Diebeskünste",
    "color"=>"`^",
    "category"=>"Künste",
    "fieldname"=>"thievery"
    );
}

function specialty_thievery_install()
{
    global $info;
    $sql  = "INSERT INTO specialty (filename,usename,specname,category,author,active) ";
    $sql .= "VALUES ('".$info['filename']."','".$info['fieldname']."','".$info['specname']."','".$info['category']."','".$info['author']."','0')";
    db_query($sql);
}

function specialty_thievery_uninstall()
{
    global $info;
    $sql  = "DELETE FROM specialty WHERE filename='".$info['filename']."'";
    db_query($sql);
}

function specialty_thievery_run($underfunction,$mid=0,$beginlink="forest.php?op=fight",$varvar="session")
{
    global $session,$info,$script,$cost_low,$cost_medium,$cost_high,$resline;
    switch($underfunction)
    {
        case "fightnav":
        if (($varvar=="session"?$session['user']['specialtyuses']['thieveryuses']:$GLOBALS[$varvar]['specialtyuses']['thieveryuses'])>0)
        {
            addnav("`^Diebeskünste`0", "");
            addnav("`^&#149; Beleidigen`7 (1/".($varvar=="session"?$session['user']['specialtyuses']['thieveryuses']:$GLOBALS[$varvar]['specialtyuses']['thieveryuses']).")`0",""
            .$beginlink."&skill=thievery&l=1",true);
        }
        if (($varvar=="session"?$session['user']['specialtyuses']['thieveryuses']:$GLOBALS[$varvar]['specialtyuses']['thieveryuses'])>1)
        addnav("`^&#149; Waffe vergiften`7 (2/".($varvar=="session"?$session['user']['specialtyuses']['thieveryuses']:$GLOBALS[$varvar]['specialtyuses']['thieveryuses']).")`0",
        $beginlink."&skill=thievery&l=2",true);
        if (($varvar=="session"?$session['user']['specialtyuses']['thieveryuses']:$GLOBALS[$varvar]['specialtyuses']['thieveryuses'])>2)
        addnav("`^&#149; Versteckter Angriff`7 (3/".($varvar=="session"?$session['user']['specialtyuses']['thieveryuses']:$GLOBALS[$varvar]['specialtyuses']['thieveryuses']).")`0",
        $beginlink."&skill=thievery&l=3",true);
        if (($varvar=="session"?$session['user']['specialtyuses']['thieveryuses']:$GLOBALS[$varvar]['specialtyuses']['thieveryuses'])>4)
        addnav("`^&#149; Angriff von hinten`7 (5/".($varvar=="session"?$session['user']['specialtyuses']['thieveryuses']:$GLOBALS[$varvar]['specialtyuses']['thieveryuses']).")`0",
        $beginlink."&skill=thievery&l=5",true);
        break;


        case "backgroundstory":
        output("`6Du hast schon sehr früh bemerkt, dass ein gewöhnlicher Rempler im Gedränge dir das Gold eines vom Glück bevorzugteren Menschen einbringen kann. ");
        output("Auch weisst du, dass die Rücken deiner Feinde anfälliger gegenüber kleinen Waffen sind als die Vorderseite gegebüber grossen.");
        break;


        case "link":
        output("<a href='newday.php?setspecialty=".$mid.$resline."'>gelernt hast, zu stehlen und dich zu verstecken (`^$info[specname]`0)</a>`n",true);
        addnav("","newday.php?setspecialty=".$mid.$resline);
        addnav($info['color'].$info['specname']."`0","newday.php?setspecialty=".$mid.$resline);
        break;


        case "buff":

$GLOBALS[$varvar]['specialtyuses']=unserialize($GLOBALS[$varvar]['specialtyuses']);

        if (($varvar== "session"?$session['user']['specialtyuses']['thieveryuses']:$GLOBALS[$varvar]['specialtyuses']['thieveryuses']) >= (int)$_GET['l']){
            $creaturedmg = 0;
            
            switch((int)$_GET['l']){

                case 1:
                $buff = array(
                "startmsg"=>"`n`^Du gibst deinem Gegner einen schlimmen Namen und bringst {badguy} zum Weinen.`n`n",
                "name"=>"`^Beleidigung",
                "rounds"=>5,
                "wearoff"=>"Dein Gegner putzt sich die Nase und hört auf zu weinen.",
                "roundmsg"=>"{badguy} ist deprimiert und kann nicht so gut angreifen.",
                "badguyatkmod"=>0.5,
                "activate"=>"defense"
                );
                if($varvar=="session") $session['bufflist']['ts1'] = $buff;
                else $GLOBALS[$varvar]['bufflist']['ts1'] = $buff;
                break;


                case 2:
                $buff = array(
                "startmsg"=>"`n`^Du reibst Gift auf dein(e/n) ".($varvar== "session"?$session[user][weapon]:$varvar['specialtyuses']['darkartuses']).".`n`n",
                "name"=>"`^Vergiftete Waffe",
                "rounds"=>5,
                "wearoff"=>"Das Blut deines Gegners hat das Gift von deiner Waffe gewaschen.",
                "atkmod"=>2,
                "roundmsg"=>"Dein Angriffswert vervielfacht sich!",
                "activate"=>"offense"
                );
                if($varvar=="session")
                {
                    $session['bufflist']['ts2'] = $buff;
                    $session['user']['reputation']--;
                }
                else $GLOBALS[$varvar]['bufflist']['ts2'] = $buff;
                break;


                case 3:
                $buff = array(
                "startmsg"=>"`n`^Mit dem Geschick eines erfahrenen Diebs scheinst du zu verschwinden und kannst {badguy} aus einer günstigeren und sichereren Position angreifen.`n`n",
                "name"=>"`^Versteckter Angriff",
                "rounds"=>5,
                "wearoff"=>"Dein Opfer hat dich gefunden.",
                "roundmsg"=>"{badguy} kann dich nicht finden.",
                "badguyatkmod"=>0,
                "activate"=>"defense"
                );
                if($varvar=="session") $session['bufflist']['ts3'] = $buff;
                else $GLOBALS[$varvar]['bufflist']['ts3'] = $buff;
                break;


                case 5:
                $buff = array(
                "startmsg"=>"`n`^Mit deinen Fähigkeiten als Dieb verschwindest du und schiebst {badguy} von hinten eine dünne Klinge zwischen die Rückenwirbel!`n`n",
                "name"=>"`^Angriff von hinten",
                "rounds"=>5,
                "wearoff"=>"Dein Opfer ist nicht mehr so nett, dich hinter sich zu lassen!",
                "atkmod"=>3,
                "defmod"=>3,
                "roundmsg"=>"Dein Angriffswert und deine Verteidigung vervielfachen sich!",
                "activate"=>"offense,defense"
                );
                if($varvar=="session") $session['bufflist']['ts5'] = $buff;
                else $GLOBALS[$varvar]['bufflist']['ts5'] = $buff;
                break;
            }
            if($varvar=="session") $session[user]['specialtyuses'][thieveryuses]-=$_GET[l];
            else $GLOBALS[$varvar]['specialtyuses'][thieveryuses]-=$_GET[l];
        }else{
            $session[bufflist]['ts0'] = array(
            "startmsg"=>"`nDu versuchst, {badguy} anzugreifen, indem du deine besten Diebeskünste in die Praxis umsetzt - aber du stolperst über deine eigenen Füsse.`n`n",
            "rounds"=>1,
            "activate"=>"roundstart"
            );
            if($varvar=="session")
            {
                $session['bufflist']['ts0'] = $buff;
                $session['user']['reputation']--;
            }
            else $GLOBALS[$varvar]['bufflist']['ts0'] = $buff;
        }

        $GLOBALS[$varvar]['specialtyuses']=serialize($GLOBALS[$varvar]['specialtyuses']);

        break;

        case "academy_desc":
        output("`3Selbststudium mit Büchern über das stille Handwerk: ");
        output("`$".$cost_low ."`^ Gold`n");
        output("`3Praktische Übung im Diebeslabyrinth: ");
        output("`$".$cost_medium ."`^ Gold und `$1 Edelstein`^`n");
        output("`$ Warchilds `3Lehrstunde für Nachwuchsdiebe: ");
        output("`$".$cost_high ."`^ Gold und `$2 Edelsteine`^`n");
        break;


        case "academy_pratice":
        output("`^Du betrittst das `7Labyrinth der Fallen`^!`n");
        output("Während Du, immer langsam an der Wand lang wegen des Alkohols, Dich in Richtung des Eingangs bewegst (oh Mann Du bist betrunken!), kann Warchild ein grausames Lächeln nicht unterdrücken.`n");
        output("Um es kurz zu machen: Du wirst dreimal von einer vergifteten Nadel gestochen, schneidest Dich zweimal an einem");
        output(" versteckten Draht und einmal übersiehst Du die grosse Falltür, durch die man direkt in den Müllkübel fällt,");
        output(" der vor der Akademie steht.`n");
        output("Halbtot sammelst Du die Reste von Dir wieder zusammen und wankst zurück ins Dorf.`n`n");
        output("`5Du verlierst einige Lebenspunkte!");
        $session[user][hitpoints] = $session[user][hitpoints]  * 0.1;
        break;


        case "weather":
        if (getsetting('weather',0)==WEATHER_FOGGY){
            output("`^`nDer Nebel bietet Dieben einen zusätzlichen Vorteil. Du bekommst eine zusätzliche Anwendung.`n");
            $session[user]['specialtyuses'][thieveryuses]++;
        }
        break;
    }
}
?>
