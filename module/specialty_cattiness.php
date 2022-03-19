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

$file = "specialty_cattiness";

function specialty_cattiness_info()
{
    global $info,$file;
    $info = array(
    "author"=>"Maris",
    "version"=>"0.8",
    "download"=>"",
    "filename"=>$file,
    "specname"=>"Heimtücke",
    "color"=>"`5",
    "category"=>"Künste",
    "fieldname"=>"cattiness"
    );
}

function specialty_cattiness_install()
{
    global $info;
    $sql  = "INSERT INTO specialty (filename,usename,specname,category,author,active) ";
    $sql .= "VALUES ('".$info['filename']."','".$info['fieldname']."','".$info['specname']."','".$info['category']."','".$info['author']."','0')";
    db_query($sql);
}

function specialty_cattiness_uninstall()
{
    global $info;
    $sql  = "DELETE FROM specialty WHERE filename='".$info['filename']."'";
    db_query($sql);
}

function specialty_cattiness_run($underfunction,$mid=0,$beginlink="forest.php?op=fight",$varvar="session")
{
    global $session,$info,$script,$cost_low,$cost_medium,$cost_high,$resline;
    switch($underfunction)
    {
        case "fightnav":
        if (($varvar=="session"?$session['user']['specialtyuses']['cattinessuses']:$GLOBALS[$varvar]['specialtyuses']['cattinessuses'])>0)
        {
            addnav("`5Heimtücke`0", "");
            addnav("`^&#149; Tot stellen`7 (0/".($varvar=="session"?$session['user']['specialtyuses']['cattinessuses']:$GLOBALS[$varvar]['specialtyuses']['cattinessuses']).")`0",""
            .$beginlink."&skill=cattiness&l=0",true);
        }
        if (($varvar=="session"?$session['user']['specialtyuses']['cattinessuses']:$GLOBALS[$varvar]['specialtyuses']['cattinessuses'])>0)
        addnav("`^&#149; Hohn`7 (1/".($varvar=="session"?$session['user']['specialtyuses']['cattinessuses']:$GLOBALS[$varvar]['specialtyuses']['cattinessuses']).")`0",
        $beginlink."&skill=cattiness&l=1",true);
        if (($varvar=="session"?$session['user']['specialtyuses']['cattinessuses']:$GLOBALS[$varvar]['specialtyuses']['cattinessuses'])>1)
        addnav("`^&#149; Lebender Schild`7 (2/".($varvar=="session"?$session['user']['specialtyuses']['cattinessuses']:$GLOBALS[$varvar]['specialtyuses']['cattinessuses']).")`0",
        $beginlink."&skill=cattiness&l=2",true);
        if (($varvar=="session"?$session['user']['specialtyuses']['cattinessuses']:$GLOBALS[$varvar]['specialtyuses']['cattinessuses'])>2)
        addnav("`^&#149; Präzision`7 (3/".($varvar=="session"?$session['user']['specialtyuses']['cattinessuses']:$GLOBALS[$varvar]['specialtyuses']['cattinessuses']).")`0",
        $beginlink."&skill=cattiness&l=3",true);
        break;


        case "backgroundstory":
        output("`6Du hast dich schon immer über die Gutgläubigkeit der Menschen lustig gemacht und ihre Hilfsbereitschaft auszunutzen gewusst. ");
        output("Schnell war dir klar, dass du nur hilflos und schwach tun musst, um sie im rechten Moment zu überraschen und ihnen in den Rücken zu fallen.");
        break;


        case "link":
        output("<a href='newday.php?setspecialty=".$mid.$resline."'>herausgefunden hast, dass List und Tücke stärker sind als jede Waffe (`^$info[specname]`0)</a>`n",true);
        addnav("","newday.php?setspecialty=".$mid.$resline);
        addnav($info['color'].$info['specname']."`0","newday.php?setspecialty=".$mid.$resline);
        break;


        case "buff":

$GLOBALS[$varvar]['specialtyuses']=unserialize($GLOBALS[$varvar]['specialtyuses']);

        if (($varvar== "session"?$session['user']['specialtyuses']['cattinessuses']:$GLOBALS[$varvar]['specialtyuses']['cattinessuses']) >= (int)$_GET['l']){
            $creaturedmg = 0;
            
            switch((int)$_GET['l']){

                case 0:
                $buff = array(
                "startmsg"=>"`n`^Du lässt dich scheinbar hart getroffen zu Boden sinken und riskierst dabei einen Treffer.`n`n",
                "name"=>"`^Tot stellen",
                "rounds"=>1,
                "wearoff"=>"Während dein Gegner keine Bedrohung mehr in dir sieht schmiedest du neue Pläne ihn zu überlisten.",
                "atkmod"=>0,
                "defmod"=>0.3,
                "activate"=>"defense"
                );
                if($varvar=="session")
                {
                    $session['bufflist']['cu1'] = $buff;
                    if ($session[user]['specialtyuses'][cattinessuses]<=6)
                    $session[user]['specialtyuses'][cattinessuses]+=1;
                }
                else
                {
                    $GLOBALS[$varvar]['bufflist']['cu1'] = $buff;
                    if ($GLOBALS[$varvar]['specialtyuses'][cattinessuses]<=6)
                    $GLOBALS[$varvar]['specialtyuses'][cattinessuses]+=1;
                }
                break;

                case 1:
                $buff = array(
                "startmsg"=>"`n`^Du verspottest die Mutter deines Gegners und machst ihn sehr wütend und unvorsichtig.`n`n",
                "name"=>"`^Hohn",
                "rounds"=>5,
                "wearoff"=>"Dein Gegner hat sich beruhigt.",
                "badguyatkmod"=>1.5,
                "badguydefmod"=>0.3,
                "roundmsg"=>"Dein Gegner prügelt blind auf dich ein und vernachlässigt seine Verteidigung!",
                "activate"=>"offense"
                );
                if($varvar=="session")
                {
                    $session['bufflist']['cu2'] = $buff;
                }
                else
                {
                    $GLOBALS[$varvar]['bufflist']['cu2'] = $buff;
                }
                break;

                case 2:
                $buff = array(
                "startmsg"=>"`n`^Du greifst in deine Taschen und ziehst eine gefesselte und gemarterte Blütenfee hervor, die du schützend vor dich hälst.`n`n",
                "name"=>"`^Lebender Schild",
                "rounds"=>5,
                "wearoff"=>"Deine Fee konnte entkommen.",
                "roundmsg"=>"{badguy} attackiert dich zögerlicher, um die Fee nicht zu verletzen.",
                "badguyatkmod"=>0.2,
                "activate"=>"defense"
                );
                if($varvar=="session") $session['bufflist']['cu3'] = $buff;
                else $GLOBALS[$varvar]['bufflist']['cu3'] = $buff;
                break;


                case 3:
                $buff = array(
                "startmsg"=>"`n`^Du fixierst deinen Gegner und zielst auf die Stellen seines Körpers, an denen er besonders empfindlich ist.`n`n",
                "name"=>"`^Präzision",
                "rounds"=>5,
                "wearoff"=>"{badguy} ist nun so zerstochen, dass du dir andere empfindliche Stellen suchen musst!",
                "atkmod"=>2.5,
                "roundmsg"=>"{badguy} heult vor Schmerz auf!",
                "activate"=>"offense,defense"
                );
                if($varvar=="session") $session['bufflist']['cu5'] = $buff;
                else $GLOBALS[$varvar]['bufflist']['cu5'] = $buff;
                break;
            }
            if($varvar=="session") $session[user]['specialtyuses'][cattinessuses]-=$_GET[l];
            else $GLOBALS[$varvar]['specialtyuses'][cattinessuses]-=$_GET[l];
        }else{
            $session[bufflist]['cu0'] = array(
            "startmsg"=>"`nDu versuchst, {badguy} auszutricksen, wirst aber leider durchschaut.`n`n",
            "rounds"=>1,
            "activate"=>"roundstart"
            );
            if($varvar=="session")
            {
                $session['bufflist']['cu0'] = $buff;
                $session['user']['reputation']--;
            }
            else $GLOBALS[$varvar]['bufflist']['cu0'] = $buff;
        }

        $GLOBALS[$varvar]['specialtyuses']=serialize($GLOBALS[$varvar]['specialtyuses']);

        break;

        case "academy_desc":
        output("`3Selbststudium mit Büchern über perfide Leute der Geschichte: ");
        output("`$".$cost_low ."`^ Gold`n");
        output("`3Praktische Übung im Parcours der Heimtücke: ");
        output("`$".$cost_medium ."`^ Gold und `$1 Edelstein`^`n");
        output("`$ Warchilds `3Lehrstunde für Ekelpakete: ");
        output("`$".$cost_high ."`^ Gold und `$2 Edelsteine`^`n");
        break;


        case "academy_pratice":
        output("`^Du betrittst den `7Parcours der Heimtücke`^!`n");
        output("Du torkelst sturzbetrunken umher und machst deine Sache wirklich gut! Doch leider ist dein gespieltes Elend echt und du sackst halb bewusstlos irgendwo am Rande des Parcours zusammen.");
        output("Warchild hat Erbarmen und schleppt dich nach draussen ins Dorf.`n");
        break;

        case "weather":
        if (getsetting('weather',0)==WEATHER_FOGGY){
            output("`^`nDer Nebel bietet Fieslingen einen zusätzlichen Vorteil. Du bekommst zwei zusätzliche Anwendungen.`n");
            $session[user]['specialtyuses'][cattinessuses]+=2;
        }
        break;
    }
}
?>
