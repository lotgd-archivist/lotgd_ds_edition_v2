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

$file = "specialty_darkarts";

function specialty_darkarts_info()
{
    global $info,$file;
    $info = array(
    "author"=>"Eric Stevens, to module by Eliwood",
    "version"=>"1.0",
    "download"=>"",
    "filename"=>$file,
    "specname"=>"Dunkle K�nste",
    "color"=>"`\$",
    "category"=>"Magie",
    "fieldname"=>"darkart"
    );
}

function specialty_darkarts_install()
{
    global $info;
    $sql  = "INSERT INTO specialty (filename,usename,specname,category,author,active) ";
    $sql .= "VALUES ('".$info['filename']."','".$info['fieldname']."','".$info['specname']."','".$info['category']."','".$info['author']."','0')";
    db_query($sql);
}

function specialty_darkarts_uninstall()
{
    global $info;
    $sql  = "DELETE FROM specialty WHERE filename='".$info['filename']."'";
    db_query($sql);
}

function specialty_darkarts_run($underfunction,$mid=0,$beginlink="forest.php?op=fight",$varvar="session")
{
    global $session,$info,$script,$cost_low,$cost_medium,$cost_high,$resline;
    switch($underfunction)
    
    {
        case "fightnav":
        if (($varvar=="session"?$session['user']['specialtyuses']['darkartuses']:$GLOBALS[$varvar]['specialtyuses']['darkartuses'])>0)
        {
            addnav("`\$Dunkle K�nste`0", "");
            addnav("`\$&#149; Skelette herbeirufen`7 (1/".($varvar=="session"?$session['user']['specialtyuses']['darkartuses']:$GLOBALS[$varvar]['specialtyuses']['darkartuses']).")`0",""
            .$beginlink."&skill=darkarts&l=1",true);
        }
        if (($varvar=="session"?$session['user']['specialtyuses']['darkartuses']:$GLOBALS[$varvar]['specialtyuses']['darkartuses'])>1)
        addnav("`\$&#149; Voodoo`7 (2/".($varvar=="session"?$session['user']['specialtyuses']['darkartuses']:$GLOBALS[$varvar]['specialtyuses']['darkartuses']).")`0",
        $beginlink."&skill=darkarts&l=2",true);
        if (($varvar=="session"?$session['user']['specialtyuses']['darkartuses']:$GLOBALS[$varvar]['specialtyuses']['darkartuses'])>2)
        addnav("`\$&#149; Geist verfluchen`7 (3/".($varvar=="session"?$session['user']['specialtyuses']['darkartuses']:$GLOBALS[$varvar]['specialtyuses']['darkartuses']).")`0",
        $beginlink."&skill=darkarts&l=3",true);
        if (($varvar=="session"?$session['user']['specialtyuses']['darkartuses']:$GLOBALS[$varvar]['specialtyuses']['darkartuses'])>4)
        addnav("`\$&#149; Seele verdorren`7 (5/".($varvar=="session"?$session['user']['specialtyuses']['darkartuses']:$GLOBALS[$varvar]['specialtyuses']['darkartuses']).")`\$",
        $beginlink."&skill=darkarts&l=5",true);
        break;


        case "backgroundstory":
        output("`5Du erinnerst dich, dass du damit aufgewachsen bist, viele kleine Waldkreaturen zu t�ten, weil du davon �berzeugt warst, sie haben sich gegen dich verschworen. ");
        output("Deine Eltern haben dir einen idiotischen Zweig gekauft, weil sie besorgt dar�ber waren, dass du die Kreaturen des Waldes mit blo�en H�nden t�ten musst. ");
        output("Noch vor deinem Teenageralter hast du damit begonnen, finstere Rituale mit und an den Kreaturen durchzuf�hren, wobei du am Ende oft tagelang im Wald verschwunden bist. ");
        output("Niemand au�er dir wusste damals wirklich, was die Ursache f�r die seltsamen Ger�usche aus dem Wald war...");
        break;


        case "link":
        output("<a href='newday.php?setspecialty=".$mid.$resline."'>viele Kreaturen des Waldes get�tet hast (`\$Dunkle K�nste`0)</a>`n",true);
        addnav("","newday.php?setspecialty=".$mid.$resline);
        addnav($info['color'].$info['specname']."`0","newday.php?setspecialty=".$mid.$resline);
        break;


        case "buff":


$GLOBALS[$varvar]['specialtyuses']=unserialize($GLOBALS[$varvar]['specialtyuses']);

        if (($varvar== "session"?$session['user']['specialtyuses']['darkartuses']:$GLOBALS[$varvar]['specialtyuses']['darkartuses']) >= (int)$_GET['l']){
            $creaturedmg = 0;

            switch((int)$_GET['l']){

                case 1:
                $buff = array(
                "startmsg"=>"`n`\$Du rufst die Geister der Toten und skelettartige H�nde zerren an {badguy} aus den Tiefen ihrer Gr�ber.`n`n",
                "name"=>"`\$Skelettdiener",
                "rounds"=>5,
                "wearoff"=>"Deine Skelettdiener zerbr�ckeln zu staub.",
                "minioncount"=>round(($varvar== "session"?$session[user][level]:$GLOBALS[$varvar]['level'])/3)+1,
                "maxbadguydamage"=>round(($varvar== "session"?$session[user][level]:$GLOBALS[$varvar]['level'])/2,0)+1,
                "effectmsg"=>"`)Ein untoter Diener trifft {badguy} mit `^{damage}`) Schadenspunkten.",
                "effectnodmgmsg"=>"`)Ein untoter Diener versucht {badguy} zu treffen, aber `\$TRIFFT NICHT`)!",
                "activate"=>"roundstart"
                );
                if($varvar=="session") $session['bufflist']['da1'] = $buff;
                else $GLOBALS[$varvar]['bufflist']['da1'] = $buff;
                break;

                case 2:
                $buff = array(
                "startmsg"=>"`n`\$Du holst eine winzige Puppe, die aussieht wie {badguy}, hervor`n`n",
                "effectmsg"=>"Du st��t eine Nadel in die {badguy}-Puppe und machst damit `^{damage}`) Schadenspunkte!",
                "minioncount"=>1,
                "maxbadguydamage"=>round(($varvar== "session"?$session[user][attack]:$GLOBALS[$varvar]['level'])*3,0),
                "minbadguydamage"=>round(($varvar== "session"?$session[user][attack]:$GLOBALS[$varvar]['level'])*1.5,0),
                "activate"=>"roundstart"
                );
                if($varvar=="session") $session['bufflist']['da2'] = $buff;
                else $GLOBALS[$varvar]['bufflist']['da2'] = $buff;
                break;

                case 3:
                $buff = array(
                "startmsg"=>"`n`\$Du sprichst einen Fluch auf die Ahnen von {badguy}.`n`n",
                "name"=>"`\$Geist verfluchen",
                "rounds"=>5,
                "wearoff"=>"Dein Fluch ist gewichen.",
                "badguydmgmod"=>0.5,
                "roundmsg"=>"{badguy} taumelt unter der Gewalt deines Fluchs und macht nur halben Schaden.",
                "activate"=>"defense"
                );
                if($varvar=="session") $session['bufflist']['da3'] = $buff;
                else $GLOBALS[$varvar]['bufflist']['da3'] = $buff;
                break;

                case 5:
                $buff = array(
                "startmsg"=>"`n`\$Du streckst deine Hand aus und {badguy} f�ngt an aus den Ohren zu bluten.`n`n",
                "name"=>"`\$Seele verdorren",
                "rounds"=>5,
                "wearoff"=>"Die Seele deines Opfers hat sich erholt.",
                "badguyatkmod"=>0,
                "badguydefmod"=>0,
                "roundmsg"=>"{badguy} kratzt sich beim Versuch, die eigene Seele zu befreien, fast die Augen aus und kann nicht angreifen oder sich verteidigen.",
                "activate"=>"offense,defense"
                );
                if($varvar=="session")
                {
                    $session['user']['reputation']--;
                    $session['bufflist']['da5'] = $buff;
                }
                else $GLOBALS[$varvar]['bufflist']['da5'] = $buff;
                break;
            }
            if($varvar=="session") $session['user']['specialtyuses']['darkartuses']-=(int)$_GET[l];
            else $GLOBALS[$varvar]['specialtyuses'][darkartuses]-=$_GET[l];
        }else{
            $buff = array(
            "startmsg"=>"`nErsch�pft versuchst du deine dunkelste Magie: einen schlechten Witz.  {badguy} schaut dich nachdenklich eine Minute lang an. Endlich versteht er den Witz und st�rzt sich lachend wieder auf dich.`n`n",
            "rounds"=>1,
            "activate"=>"roundstart"
            );
            if($varvar=="session")
            {
                $session['user']['reputation']--;
                $session['bufflist']['da0'] = $buff;
            }
            else $GLOBALS[$varvar]['bufflist']['da0'] = $buff;
        }
        
        $GLOBALS[$varvar]['specialtyuses']=serialize($GLOBALS[$varvar]['specialtyuses']);

        break;

        case "academy_desc":
        output("`3Selbststudium der Dunklen K�nste: ");
        output("`$".$cost_low ."`^ Gold`n");
        output("`3Praktischer Unterricht im Tiere qu�len: ");
        output("`$".$cost_medium ."`^ Gold und `$1 Edelstein`^`n");
        output("`3Eine Lehrstunde beim Meister der dunklen K�nste, `$ Warchild `3selbst, nehmen: ");
        output("`$".$cost_high ."`^ Gold und `$2 Edelsteine`^`n");
        break;


        case "academy_pratice":
        output("`^Du betrittst den `7Tierk�fig`^!`n");
        output("Ein niedlich aussehendes, weisses Kaninchen sitzt in der Mitte des K�figs und glotzt");
        output("Dich an. Du holst zum Schlag aus, doch auf einmal springt es auf Dich zu und");
        output("`$ gr�bt seine Z�hne in Deine Hand!`^ Gl�cklicherweise bist Du noch zu betrunken um den Schmerz zu f�hlen...`n");
        output("aber daf�r wird Deine Hand morgen h�llisch weh tun!`n");
        output("Mit einer bandagierten Hand verl�sst Du den Ort.`n`n");
        output("`5Du verlierst ein paar Lebenspunkte!");
        $session[user][hitpoints] = $session[user][hitpoints] - $session[user][hitpoints] * 0.2;
        break;


        case "weather":
        if (getsetting('weather',0)==WEATHER_RAINY){
            output("`^`nDer Regen schl�gt dir aufs Gem�t, aber erweitert deine Dunklen K�nste. Du bekommst eine zus�tzliche Anwendung.`n");
            $session[user]['specialtyuses']['darkartuses']++;
        }
        break;
    }
}
?>
