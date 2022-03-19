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

$file = "specialty_heroism";

function specialty_heroism_info()
{
    global $info,$file;
    $info = array(
    "author"=>"Maris",
    "version"=>"1.0",
    "download"=>"",
    "filename"=>$file,
    "specname"=>"Heldentum",
    "color"=>"`3",
    "category"=>"Fähigkeiten",
    "fieldname"=>"heroism"
    );
}

function specialty_heroism_install()
{
    global $info;
    $sql  = "INSERT INTO specialty (filename,usename,specname,category,author,active) ";
    $sql .= "VALUES ('".$info['filename']."','".$info['fieldname']."','".$info['specname']."','".$info['category']."','".$info['author']."','0')";
    db_query($sql);
}

function specialty_heroism_uninstall()
{
    global $info;
    $sql  = "DELETE FROM specialty WHERE filename='".$info['filename']."'";
    db_query($sql);
}

function specialty_heroism_run($underfunction,$mid=0,$beginlink="forest.php?op=fight",$varvar="session")
{
    global $session,$info,$script,$cost_low,$cost_medium,$cost_high,$resline;
    switch($underfunction)
    {
        case "fightnav":
        if (($varvar=="session"?$session['user']['specialtyuses']['heroismuses']:$GLOBALS[$varvar]['specialtyuses']['heroismuses'])>0)
        {
            addnav("`3Heldentum`0", "");
            addnav("`^&#149; Posieren`7 (1/".($varvar=="session"?$session['user']['specialtyuses']['heroismuses']:$GLOBALS[$varvar]['specialtyuses']['heroismuses']).")`0",""
            .$beginlink."&skill=heroism&l=1",true);
        }
        if (($varvar=="session"?$session['user']['specialtyuses']['heroismuses']:$GLOBALS[$varvar]['specialtyuses']['heroismuses'])>1)
        addnav("`^&#149; Tapferkeit`7 (2/".($varvar=="session"?$session['user']['specialtyuses']['heroismuses']:$GLOBALS[$varvar]['specialtyuses']['heroismuses']).")`0",
        $beginlink."&skill=heroism&l=2",true);
        if (($varvar=="session"?$session['user']['specialtyuses']['heroismuses']:$GLOBALS[$varvar]['specialtyuses']['heroismuses'])>2)
        addnav("`^&#149; Kühner Angriff`7 (3/".($varvar=="session"?$session['user']['specialtyuses']['heroismuses']:$GLOBALS[$varvar]['specialtyuses']['heroismuses']).")`0",
        $beginlink."&skill=heroism&l=3",true);
        if (($varvar=="session"?$session['user']['specialtyuses']['heroismuses']:$GLOBALS[$varvar]['specialtyuses']['heroismuses'])>4)
        addnav("`^&#149; Führungstalent`7 (5/".($varvar=="session"?$session['user']['specialtyuses']['heroismuses']:$GLOBALS[$varvar]['specialtyuses']['heroismuses']).")`0",
        $beginlink."&skill=heroism&l=5",true);
        break;


        case "backgroundstory":
        output("`6Du hast es schon immer geliebt im Mittelpunkt zu stehen und warst stets für Andere da, wenn sie in Not waren. ");
        output("Auch hast du früh bemerkt, dass du eine besondere Begabung besitzt Andere zu motivieren. Angst war dir immer ein Fremdwort.");
        break;


        case "link":
        output("<a href='newday.php?setspecialty=".$mid.$resline."'>immer der wackere Anführer warst, der mutig und entschlossen gegen jedes Unrecht vorging (`^$info[specname]`0)</a>`n",true);
        addnav("","newday.php?setspecialty=".$mid.$resline);
        addnav($info['color'].$info['specname']."`0","newday.php?setspecialty=".$mid.$resline);
        break;


        case "buff":

$GLOBALS[$varvar]['specialtyuses']=unserialize($GLOBALS[$varvar]['specialtyuses']);

        if (($varvar== "session"?$session['user']['specialtyuses']['heroismuses']:$GLOBALS[$varvar]['specialtyuses']['heroismuses']) >= (int)$_GET['l']){
            $creaturedmg = 0;
            
            switch((int)$_GET['l']){

                case 1:
                $buff = array(
                "startmsg"=>"`n`^Du stemmst deine Fäuse in deine Hüften und wirfst dein Haar in den Wind, was {badguy} ziemlich beeindruckt.`n`n",
                "name"=>"`3Posieren",
                "rounds"=>5,
                "wearoff"=>"Dein Gegner zeigt sich nun wieder unbeeindruckt.",
                "roundmsg"=>"{badguy} ist von deinem Auftreten stark beeindruckt und kann sich nicht so gut wehren.",
                "badguydefmod"=>0.5,
                "activate"=>"offense"
                );
                if($varvar=="session") $session['bufflist']['hs1'] = $buff;
                else $GLOBALS[$varvar]['bufflist']['hs1'] = $buff;
                break;


                case 2:
                $buff = array(
                "startmsg"=>"`n`^Du kneifst deine Augen zusammen und gehst in Abwehrposition.`n`n",
                "name"=>"`3Tapferkeit",
                "rounds"=>5,
                "wearoff"=>"Dein Gegner weicht etwas zurück und lockt dich aus der Verteidigung.",
                "defmod"=>3,
                "roundmsg"=>"Dein Verteidigungswert steigt!",
                "activate"=>"defense"
                );
                if($varvar=="session")
                {
                    $session['bufflist']['hs2'] = $buff;
                }
                else $GLOBALS[$varvar]['bufflist']['hs2'] = $buff;
                break;


                case 3:
                $buff = array(
                "startmsg"=>"`n`^Du spannst deinen Kinnmuskel an stürmst auf {badguy} zu.`n`n",
                "name"=>"`3Kühner Angriff",
                "rounds"=>2,
                "wearoff"=>"Dein Angriffsschwung kam zun Erliegen.",
                "roundmsg"=>"{badguy} reisst ängstlich die Augen auf als du zuschlägst.",
                "atkmod"=>4,
                "activate"=>"offense"
                );
                if($varvar=="session") $session['bufflist']['hs3'] = $buff;
                else $GLOBALS[$varvar]['bufflist']['hs3'] = $buff;
                break;


                case 5:
                $buff = array(
                "startmsg"=>"`n`^Du rufst laut \"`3Zu den Waffen!`^\" und ein wütender Mob eilt dir zur Hilfe.`n`n",
                "name"=>"`3Mob",
                "rounds"=>10,
                "wearoff"=>"Der Mob hat sich nun verstreut.",
                "minioncount"=>round(($varvar== "session"?$session[user][level]:$GLOBALS[$varvar]['level'])/2)+2,
                "maxbadguydamage"=>round(($varvar== "session"?$session[user][level]:$GLOBALS[$varvar]['level'])/2,0)+2,
                "roundmsg"=>"Der Mob stürzt sich unter deiner Führung auf {badguy}!",
                "effectmsg"=>"`)Einer deiner Gefolgsleute trifft {badguy} mit `^{damage}`) Schadenspunkten.",
                "effectnodmgmsg"=>"`)Einer deiner Gefolgsleute versucht {badguy} zu treffen, `\$TRIFFT ABER NICHT`)!",
                "activate"=>"roundstart"
                
                );
                if($varvar=="session") $session['bufflist']['hs5'] = $buff;
                else $GLOBALS[$varvar]['bufflist']['hs5'] = $buff;
                break;
            }
            if($varvar=="session") $session[user]['specialtyuses'][heroismuses]-=$_GET[l];
            else $GLOBALS[$varvar]['specialtyuses'][heroismuses]-=$_GET[l];
        }else{
            $session[bufflist]['hs0'] = array(
            "startmsg"=>"`nDu versuchst, {badguy} mit dem schallenden Klang deiner Stimme in die Flucht zu jagen, doch leider hast du gerade einen Frosch im Hals.`n`n",
            "rounds"=>1,
            "activate"=>"roundstart"
            );
            if($varvar=="session")
            {
                $session['bufflist']['hs0'] = $buff;
                $session['user']['reputation']--;
            }
            else $GLOBALS[$varvar]['bufflist']['hs0'] = $buff;
        }

        $GLOBALS[$varvar]['specialtyuses']=serialize($GLOBALS[$varvar]['specialtyuses']);

        break;

        case "academy_desc":
        output("`3Selbststudium mit Büchern über die Taten großer Leute: ");
        output("`$".$cost_low ."`^ Gold`n");
        output("`3Praktische Übung auf der Bühne: ");
        output("`$".$cost_medium ."`^ Gold und `$1 Edelstein`^`n");
        output("`$ Warchilds `3Lehrstunde für kleine und große Helden: ");
        output("`$".$cost_high ."`^ Gold und `$2 Edelsteine`^`n");
        break;


        case "academy_pratice":
        output("`^Du betrittst die große `7Showbühne`^!`n");
        output("Du torkelst unbeholfen über die Bühne und hast dank des Alkohols deine Sinne kaum mehr unter Kontrolle.`n");
        output("Mehrere Male fällst du der Länge nach hin, und bei angeberischen Paradeübungen rammst du dir den Säbel selbst ins Bein.`n");
        output("Was für ein Glück, dass dir niemand zusieht, denn deine Alkoholfahne hat den letzten Zuschauer schon lange vertrieben.`n");
        output("Du humpelst gedemütigt nach draussen.`n`n");
        output("`5Du verlierst ein paar Lebenspunkte!");
        $session[user][hitpoints] = $session[user][hitpoints]  * 0.8;
        break;


        case "weather":
        if (getsetting('weather',0)==WEATHER_WARM){
            output("`^`nDer Sonnenschein lässt dich heute noch imposanter Erscheinen. Du bekommst eine zusätzliche Anwendung.`n");
            $session[user]['specialtyuses'][heroismuses]++;
        }
        break;
    }
}
?>
