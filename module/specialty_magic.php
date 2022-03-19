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

$file = "specialty_magic";

function specialty_magic_info()
{
    global $info,$file;
    $info = array(
    "author"=>"Eric Stevens, to module by Eliwood",
    "version"=>"1.2",
    "download"=>"",
    "filename"=>$file,
    "specname"=>"Mystische Kräfte",
    "color"=>"`%",
    "category"=>"Magie",
    "fieldname"=>"magic"
    );
}

function specialty_magic_install()
{
    global $info;
    $sql  = "INSERT INTO specialty (filename,usename,specname,category,author,active) ";
    $sql .= "VALUES ('".$info['filename']."','".$info['fieldname']."','".$info['specname']."','".$info['category']."','".$info['author']."','0')";
    db_query($sql);
}

function specialty_magic_uninstall()
{
    global $info;
    $sql  = "DELETE FROM specialty WHERE filename='".$info['filename']."'";
    db_query($sql);
}

function specialty_magic_run($underfunction,$mid=0,$beginlink="forest.php?op=fight",$varvar="session")
{
    global $session,$info,$script,$cost_low,$cost_medium,$cost_high,$resline;
    switch($underfunction)
    {
        case "fightnav":
        if (($varvar=="session"?$session['user']['specialtyuses']['magicuses']:$GLOBALS[$varvar]['specialtyuses']['magicuses'])>0)
        {
            addnav("`%Mystische Kräfte`0", "");
            addnav("g?`%&#149; Regeneration`7 (1/".($varvar=="session"?$session['user']['specialtyuses']['magicuses']:$GLOBALS[$varvar]['specialtyuses']['magicuses']).")`0",""
            .$beginlink."&skill=magic&l=1",true);
        }
        if (($varvar=="session"?$session['user']['specialtyuses']['magicuses']:$GLOBALS[$varvar]['specialtyuses']['magicuses'])>1)
        addnav("`%&#149; Erdenfaust`7 (2/".($varvar=="session"?$session['user']['specialtyuses']['magicuses']:$GLOBALS[$varvar]['specialtyuses']['magicuses']).")`0",
        $beginlink."&skill=magic&l=2",true);
        if (($varvar=="session"?$session['user']['specialtyuses']['magicuses']:$GLOBALS[$varvar]['specialtyuses']['magicuses'])>2)
        addnav("L?`%&#149; Leben absaugen`7 (3/".($varvar=="session"?$session['user']['specialtyuses']['magicuses']:$GLOBALS[$varvar]['specialtyuses']['magicuses']).")`0",
        $beginlink."&skill=magic&l=3",true);
        if (($varvar=="session"?$session['user']['specialtyuses']['magicuses']:$GLOBALS[$varvar]['specialtyuses']['magicuses'])>4)
        addnav("A?`%&#149; Blitz Aura`7 (5/".($varvar=="session"?$session['user']['specialtyuses']['magicuses']:$GLOBALS[$varvar]['specialtyuses']['magicuses']).")`0",
        $beginlink."&skill=magic&l=5",true);
        break;
        case "backgroundstory":
        output("`3Du hast schon als Kind gewusst, dass diese Welt mehr als das Physische bietet, woran du herumspielen konntest. ");
        output("Du hast erkannt, dass du mit etwas Training deinen Geist selbst in eine Waffe verwandeln kannst. ");
        output("Mit der Zeit hast du gelernt, die Gedanken kleiner Kreaturen zu kontrollieren und ihnen deinen Willen aufzuzwingen. ");
        output("Du bist auch auf die mystische Kraft namens Mana gestossen, die du in die Form von Feuer, Wasser, Eis, Erde, Wind bringen und sogar als Waffe gegen deine Feinde einsetzen kannst.");
        break;
        case "link":
        output("<a href='newday.php?setspecialty=".$mid.$resline."'>die Kraft der Magie entdeckt hast (`%$info[specname]`0)</a>`n",true);
        addnav("","newday.php?setspecialty=".$mid.$resline);
        addnav($info['color'].$info['specname']."`0","newday.php?setspecialty=".$mid.$resline);
        break;
        case "buff":

$GLOBALS[$varvar]['specialtyuses']=unserialize($GLOBALS[$varvar]['specialtyuses']);

        if (($varvar== "session"?$session[user]['specialtyuses'][magicuses]:$GLOBALS[$varvar]['specialtyuses']['magicuses']) >= (int)$_GET[l]){
            $creaturedmg = 0;
            switch((int)$_GET['l']){
                case 1:
                $buff = array(
                "startmsg"=>"`n`^Du fängst an zu regenerieren!`n`n",
                "name"=>"`%Regeneration",
                "rounds"=>5,
                "wearoff"=>"Deine Regeneration hat aufgehört",
                "regen"=>($varvar== "session"?$session['user']['level']:$varvar['specialtyuses']['darkartuses']),
                "effectmsg"=>"Du regenerierst um {damage} Punkte.",
                "effectnodmgmsg"=>"Du bist völlig gesund.",
                "activate"=>"roundstart");
                if($varvar=="session")
                {
                    $session['user']['reputation']--;
                    $session['bufflist']['mp1'] = $buff;
                }
                else $GLOBALS[$varvar]['bufflist']['mp1'] = $buff;
                break;
                case 2:
                $buff = array(
                "startmsg"=>"`n`^{badguy}`% wird von einer Klaue aus Erde gepackt und auf den Boden geschleudert!`n`n",
                "name"=>"`%Erdenfaust",
                "rounds"=>5,
                "wearoff"=>"Die erdene Faust zerfällt zu staub.",
                "minioncount"=>1,
                "effectmsg"=>"Eine gewaltige Faust aus Erde trifft {badguy} mit `^{damage}`) Schadenspunkten.",
                "minbadguydamage"=>1,
                "maxbadguydamage"=>($varvar== "session"?$session['user']['level']:$varvar['specialtyuses']['darkartuses'])*3,
                "activate"=>"roundstart"
                );
                if($varvar=="session") $session['bufflist']['mp2'] = $buff;
                else $GLOBALS[$varvar]['bufflist']['mp2'] = $buff;
                break;
                case 3:
                $buff = array(
                "startmsg"=>"`n`^Deine Waffe glüht in einem überirdischen Schein.`n`n",
                "name"=>"`%Leben absaugen",
                "rounds"=>5,
                "wearoff"=>"Die Aura deiner Waffe verschwindet.",
                "lifetap"=>1, //ratio of damage healed to damage dealt
                "effectmsg"=>"Du wirst um {damage} Punkte geheilt.",
                "effectnodmgmsg"=>"Du fühlst ein Prickeln, als deine Waffe versucht, deinen vollständig gesunden Körper zu heilen.",
                "effectfailmsg"=>"Deine Waffe scheint zu jammern, als du deinem Gegner keinen Schaden machst.",
                "activate"=>"offense,defense",
                );
                if($varvar=="session") $session['bufflist']['mp3'] = $buff;
                else $GLOBALS[$varvar]['bufflist']['mp3'] = $buff;
                break;
                case 5:
                $buff = array(
                "startmsg"=>"`n`^Deine Haut glitzert, als du dir eine Aura aus Blitzen zulegst`n`n",
                "name"=>"`%Blitzaura",
                "rounds"=>5,
                "wearoff"=>"Mit einem Zischen wird deine Haut wieder normal.",
                "damageshield"=>2,
                "effectmsg"=>"{badguy}wird von einem Blitzbogen aus deiner Haut mit `^{damage}`) Schadenspunkten zurückgeworfen.",
                "effectnodmg"=>"{badguy} ist von deinen Blitzen leicht geblendet, ansonsten aber unverletzt.",
                "effectfailmsg"=>"{badguy} ist von deinen Blitzen leicht geblendet, ansonsten aber unverletzt.",
                "activate"=>"offense,defense"
                );
                if($varvar=="session") $session['bufflist']['mp5'] = $buff;
                else $GLOBALS[$varvar]['bufflist']['mp5'] = $buff;
                break;
            }
            if($varvar=="session") $session[user]['specialtyuses'][magicuses]-=(int)$_GET[l];
            else $GLOBALS[$varvar]['specialtyuses'][magicuses]-=$_GET[l];
        }else{
            $buff = array(
            "startmsg"=>"`nDu legst deine Stirn in Falten und beschwörst die Elemente.  Eine kleine Flamme erscheint. {badguy} zündet sich eine Zigarette daran an, dankt dir und stürzt sich wieder auf dich.`n`n",
            "rounds"=>1,
            "activate"=>"roundstart"
            );
            if($varvar=="session")
            {
                $session['user']['reputation']--;
                $session['bufflist']['mp0'] = $buff;
            }
            else $GLOBALS[$varvar]['bufflist']['mp0'] = $buff;
        }
        
        $GLOBALS[$varvar]['specialtyuses']=serialize($GLOBALS[$varvar]['specialtyuses']);

        break;

        case "academy_desc":
        output("`3Selbststudium in der Bibliothek: ");
        output("`$".$cost_low ."`^ Gold`n");
        output("`3Praktische Übung in der Magiekammer: ");
        output("`$".$cost_medium ."`^ Gold und `$1 Edelstein`^`n");
        output("`$ Warchilds `3Mystikstunde: ");
        output("`$".$cost_high ."`^ Gold und `$2 Edelsteine`^`n");
        break;
        case "academy_pratice":
        output("`^Du betrittst die `7Magiekammer`^!`n");
        output("Ein Golem marschiert auf Dich zu, doch Deine Sicht ist vom Alkohol noch so verschwommen, dass Dein Spruch ihn verfehlt!`n");
        output("Statt dessen trifft er Dich mit einer grossen Keule und Du verlierst das Bewusstsein.`n");
        output("Nach ein paar Minuten wachst Du vor der Akademie mit fiesen Kopfschmerzen wieder auf und torkelst zurück in die Stadt.`n`n");
        output("`5Du verlierst ein paar Lebenspunkte!");
        $session[user][hitpoints] = $session[user][hitpoints] - $session[user][hitpoints] * 0.2;
        break;
        case "weather":
        if (getsetting('weather',0)==WEATHER_TSTORM){
            output("`^`nDie Blitze fördern deine Mystischen Kräfte. Du bekommst eine zusätzliche Anwendung.`n");
            $session[user]['specialtyuses'][magicuses]++;
        }
        break;
    }
}
?>
