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

$file = "specialty_transmutation";

function specialty_transmutation_info()
{
    global $info,$file;
    $info = array(
    "author"=>"Maris",
    "version"=>"1.0",
    "download"=>"",
    "filename"=>$file,
    "specname"=>"Verwandlungsmagie",
    "color"=>"`4",
    "category"=>"Magie",
    "fieldname"=>"transmutation"
    );
}

function specialty_transmutation_install()
{
    global $info;
    $sql  = "INSERT INTO specialty (filename,usename,specname,category,author,active) ";
    $sql .= "VALUES ('".$info['filename']."','".$info['fieldname']."','".$info['specname']."','".$info['category']."','".$info['author']."','0')";
    db_query($sql);
}

function specialty_transmutation_uninstall()
{
    global $info;
    $sql  = "DELETE FROM specialty WHERE filename='".$info['filename']."'";
    db_query($sql);
}

function specialty_transmutation_run($underfunction,$mid=0,$beginlink="forest.php?op=fight",$varvar="session")
{
    global $session,$info,$script,$cost_low,$cost_medium,$cost_high,$resline;
    switch($underfunction)
    
    {
        case "fightnav":
        if (($varvar=="session"?$session['user']['specialtyuses']['transmutationuses']:$GLOBALS[$varvar]['specialtyuses']['transmutationuses'])>0)
        {
            addnav("`4Verwandlungsmagie`0", "");
            addnav("`4 Steinhaut`7 (1/".($varvar=="session"?$session['user']['specialtyuses']['transmutationuses']:$GLOBALS[$varvar]['specialtyuses']['transmutationuses']).")`0",""
            .$beginlink."&skill=transmutation&l=1",true);
        }
        if (($varvar=="session"?$session['user']['specialtyuses']['transmutationuses']:$GLOBALS[$varvar]['specialtyuses']['transmutationuses'])>1)
        addnav("`4 Klingenarme`7 (2/".($varvar=="session"?$session['user']['specialtyuses']['transmutationuses']:$GLOBALS[$varvar]['specialtyuses']['transmutationuses']).")`0",
        $beginlink."&skill=transmutation&l=2",true);
        if (($varvar=="session"?$session['user']['specialtyuses']['transmutationuses']:$GLOBALS[$varvar]['specialtyuses']['transmutationuses'])>2)
        addnav("`4 Flammenleib`7 (3/".($varvar=="session"?$session['user']['specialtyuses']['transmutationuses']:$GLOBALS[$varvar]['specialtyuses']['transmutationuses']).")`0",
        $beginlink."&skill=transmutation&l=3",true);
        if (($varvar=="session"?$session['user']['specialtyuses']['transmutationuses']:$GLOBALS[$varvar]['specialtyuses']['transmutationuses'])>4)
        addnav("`4 Kraken`7 (5/".($varvar=="session"?$session['user']['specialtyuses']['transmutationuses']:$GLOBALS[$varvar]['specialtyuses']['transmutationuses']).")`\$",
        $beginlink."&skill=transmutation&l=5",true);
        break;

        case "backgroundstory":
        output("`5Du erinnerst dich, dass du als Kind kaum Freunde hattest und die meiste Zeit des Tages allein verbacht hast.`nDa du dich selbst als hässlich und unannehmbar empfunden hast fingst du an die Geheimnissde der Verwandlungsmagie zu ergründen.`nNach Jahren harter Arbeit bist du nun am Ziel deiner Wünsche angelangt : du kannst deinen Körper nach deinem Willen formen! ");
        break;


        case "link":
        output("<a href='newday.php?setspecialty=".$mid.$resline."'>viele Stunden allein damit zugebracht hast dich selbst zu hassen und dir einen anderer Körper zu wünschen (`4Verwandlungsmagie`0)</a>`n",true);
        addnav("","newday.php?setspecialty=".$mid.$resline);
        addnav($info['color'].$info['specname']."`0","newday.php?setspecialty=".$mid.$resline);
        break;


        case "buff":


$GLOBALS[$varvar]['specialtyuses']=unserialize($GLOBALS[$varvar]['specialtyuses']);

        if (($varvar== "session"?$session['user']['specialtyuses']['transmutationuses']:$GLOBALS[$varvar]['specialtyuses']['transmutationuses']) >= (int)$_GET['l']){
            $creaturedmg = 0;

            switch((int)$_GET['l']){

                case 1:
                $buff = array(
                "startmsg"=>"`n`4Deine Haut wird hart wie Stein und lässt alle Angriffe abprallen.`n`n",
                "name"=>"`4Steinhaut",
                "rounds"=>2,
                "wearoff"=>"Deine Haut wird wieder weich und verwundbar.",
                "badguydmgmod"=>0,
                "badguyatkmod"=>0,
                "activate"=>"defense"
                );
                if($varvar=="session") $session['bufflist']['transmutation1'] = $buff;
                else $GLOBALS[$varvar]['bufflist']['transmutation1'] = $buff;
                break;

                case 2:
                $buff = array(
                "startmsg"=>"`n`4Du streckst deine Arme aus und sie verwandeln sich in lange und scharfe Klingen.`n`n",
                "name"=>"`4Klingenarme",
                "roundmsg"=>"Die Klingenarme erhöhen deinen Angriffswert!",
                "rounds"=>4,
                "wearoff"=>"Deine Arme sind nun wieder normal.",
                "atkmod"=>2.3,
                "activate"=>"offense"
                );
                if($varvar=="session") $session['bufflist']['transmutation2'] = $buff;
                else $GLOBALS[$varvar]['bufflist']['transmutation2'] = $buff;
                break;

                case 3:
                $buff = array(
                "startmsg"=>"`n`4Dein Körper entzündet sich und du stehst komplett in Flammen!`n`n",
                "name"=>"`4Flammenkörper",
                "rounds"=>5,
                "wearoff"=>"Deine Flammen sind erloschen.",
                "damageshield"=>1.5,
                "effectmsg"=>"`4{badguy} verbrennt sich an dir die Finger und bekommt `^{damage}`4 Schadenspunkte.",
                "effectnodmgmsg"=>"`4{badguy} scheint von deinen Flammen unbeeindruckt zu sein!",
                "effectfailmsg"=>"`4{badguy} scheint von deinen Flammen unbeeindruckt zu sein!",
                "activate"=>"roundstart"
                );
                if($varvar=="session") $session['bufflist']['transmutation3'] = $buff;
                else $GLOBALS[$varvar]['bufflist']['transmutation3'] = $buff;
                break;

                case 5:
                $buff = array(
                "startmsg"=>"`n`4Dir wachsen zusätzliche Arme!`n`n",
                "name"=>"`4Kraken",
                "rounds"=>e_rand(1,5),
                "wearoff"=>"Deine zusätzlichen Arme verschwinden.",
                "minioncount"=>e_rand(1,5),
                "effectmsg"=>"`4Du triffst triffst `^{badguy}`4 mit `^{damage}`4 Schadenspunkten.",
                "maxbadguydamage"=>round($varvar== "session"?$session[user][attack]*0.8:$GLOBALS[$varvar]['level']*3,0),
                "minbadguydamage"=>round($varvar== "session"?$session[user][attack]*0.6:$GLOBALS[$varvar]['level']*2,0),
                "activate"=>"roundstart"
                );
                if($varvar=="session")
                {
                    $session['bufflist']['transmutation5'] = $buff;
                }
                else $GLOBALS[$varvar]['bufflist']['transmutation5'] = $buff;
                break;
            }
            if($varvar=="session") $session['user']['specialtyuses']['transmutationuses']-=(int)$_GET[l];
            else $GLOBALS[$varvar]['specialtyuses'][transmutationuses]-=$_GET[l];
        }else{
            $buff = array(
            "startmsg"=>"`nDu verwandelst dich in einen feuerspuckenden Drachen!`n(Aber nur im Traum...).`n`n",
            "rounds"=>1,
            "activate"=>"roundstart"
            );
            if($varvar=="session")
            {
                $session['user']['reputation']--;
                $session['bufflist']['transmutation0'] = $buff;
            }
            else $GLOBALS[$varvar]['bufflist']['transmutation0'] = $buff;
        }
        
        $GLOBALS[$varvar]['specialtyuses']=serialize($GLOBALS[$varvar]['specialtyuses']);

        break;

        case "academy_desc":
        output("`3Selbststudium mit dem Buch der Verwandlung: ");
        output("`$".$cost_low ."`^ Gold`n");
        output("`3Praktischer Unterricht im Verwandeln: ");
        output("`$".$cost_medium ."`^ Gold und `$1 Edelstein`^`n");
        output("`3Eine Lehrstunde `$ Warchild `3 nehmen: ");
        output("`$".$cost_high ."`^ Gold und `$2 Edelsteine`^`n");
        break;


        case "academy_pratice":
        output("`3Du konzentrierst dich so gut es dir in deinem Zustand möglich ist, und mit einem lauten `^*Poff*`3 verwandelst du dich in eine riesengroße Ale-Flasche.`nDein Aussehen und deine deutlich bemerkbare Fahne ziehen immer mehr durstige Zuschauer an.`nIn Panik rennst du davon, gehetzt und getrieben, bis der Zauber endlich seine Wirkung verliert und du wieder du selbst bist.`nWenn das mal kein Erlebnis war!`n");
        break;

        case "weather":
            output("`^`nDas Wetter ist dir ziemlich egal, du bist immer motiviert deine Verwandlungsmagie zu nutzen. Du bekommst eine zusätzliche Anwendung.`n");
            $session[user]['specialtyuses']['transmutationuses']++;
        break;
    }
}
?>
