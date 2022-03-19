<?php
/**
* dragon.php: Endgegner
* @author LOGD-Core, modded by Drachenserver-Team
* @version DS-E V/2
*/

// 24072004

require_once('common.php');

page_header("Der Grüne Drachen!");
if ($_GET['op']=="")
{
    output("`$Du erstickst jeden Drang zu fliehen und betrittst vorsichtig die Höhle. Du spekulierst ");
    output("darauf, den großen Drachen im Schlaf zu überraschen, um ihn mit einem Minimum an eigenem Schmerz ");
    output("zu erlegen. Leider ist das nicht der Fall. Du biegst in der Höhle um eine Ecke ");
    output("und entdeckst das Riesenbiest, das mit den Hinterbeinen auf einem gewaltigen Haufen Gold sitzt und ");
    output("seine Zähne mit etwas reinigt, das wie eine Rippe aussieht.");
    $badguy = array("creaturename"=>"`@Der Grüne Drachen`0","creaturelevel"=>18,"creatureweapon"=>"Gigantischer Flammenstoß","creatureattack"=>45,"creaturedefense"=>25,"creaturehealth"=>300, "diddamage"=>0);
    //toughen up each consecutive dragon.
    //      $atkflux = e_rand(0,$session['user']['dragonkills']*2);
    //      $defflux = e_rand(0,($session['user']['dragonkills']*2-$atkflux));
    //      $hpflux = ($session['user']['dragonkills']*2 - ($atkflux+$defflux)) * 5;
    //      $badguy['creatureattack']+=$atkflux;
    //      $badguy['creaturedefense']+=$defflux;
    //      $badguy['creaturehealth']+=$hpflux;
    
    // First, find out how each dragonpoint has been spent and count those
    // used on attack and defense.
    // Coded by JT, based on collaboration with MightyE
    $points = 0;
    while (list($key,$val)=each($session['user']['dragonpoints']))
    {
        if ($val=="at" || $val == "de")
        {
            $points++;
        }
    }
    // Now, add points for hitpoint buffs that have been done by the dragon
    // or by potions!
    $points += (int)(($session['user']['maxhitpoints']-150)/5);
    
    // Okay.. *now* buff the dragon a bit.
    if ($beta)
    {
        $points = round($points*1.5,0);
    }
    else
    {
        $points = round($points*.85,0);
    }
    
    $atkflux = e_rand(0, $points);
    $defflux = e_rand(0,$points-$atkflux);
    $hpflux = ($points - ($atkflux+$defflux)) * 5;
    $badguy['creatureattack']+=$atkflux;
    $badguy['creaturedefense']+=$defflux;
    $badguy['creaturehealth']+=$hpflux;
    $badguy['creaturehealth']*=1.65;
    
    // Endgegner
    $badguy['boss'] = true;
    
    $float_forest_bal = getsetting('forestbal',1.5);
    
    $badguy['creatureattack'] *= 1 + 0.01 * $float_forest_bal * $session['user']['balance_dragon'];
    $badguy['creaturedefense'] *= 1 + 0.01 * $float_forest_bal * $session['user']['balance_dragon'];
    $badguy['creaturehealth'] *= 1 + 0.01 * $float_forest_bal * $session['user']['balance_dragon'];
    
    $badguy['creaturehealth'] = round($badguy['creaturehealth']);
    
    $session['user']['badguy']=createstring($badguy);
    $battle=true;
}
else if ($_GET['op']=="autochallenge")
{
    output("`$Auf dem Weg zum Dorfplatz hörst du ein seltsames Geräusch aus Richtung Wald und spürst ein ebenso seltsames Verlangen, der Ursache für das Geräusch nachzugehen. ");
    output("Die Leute auf dem Dorfplatz scheinen in ihrer Unterhaltung nichts davon mitbekommen zu haben, also machst du dich alleine auf den Weg. Kaum im Wald hörst du das Geräusch erneut, diesmal schon wesentlich näher. ");
    output("`nIn der Ferne siehst du ihn: Den `@grünen Drachen`$! Gerade dabei, eine Höhle zu betreten. Er scheint müde zu sein. Das ist `bDIE`b Gelegenheit! Nie hast du dich stärker gefühlt...");
    addnav("Weiter...","dragon.php");
}
else if ($_GET['op']=="prologue1")
{
    output("`@Sieg!`n`n");
    $flawless = 0;
    if ($_GET['flawless'])
    {
        $flawless = 1;
        output("`b`c`&~~ Perfekter Kampf! ~~`0`c`b`n`n");
    }
    output("`2Vor dir liegt regungslos der große Drache. Sein schwerer Atem ist wie Säure für deine Lungen.  ");
    output("Du bist vom Kopf bis zu den Zehen mit dem dicken schwarzen Blut dieser stinkenden Kreatur bedeckt.  ");
    output("Das Riesenbiest fängt plötzlich an, den Mund zu bewegen. Verärgert über dich selbst, dass du dich von dem vorgetäuschten Tod ");
    output("der Kreatur reinlegen lassen hast, springst du zurück und erwartest, dass der riesige Schwanz auf dich zugeschossen kommt. Doch das passiert ");
    output("nicht. Stattdessen beginnt der Drachen zu sprechen.`n`n");
    output("\"`^Warum bist du hierher gekommen, Sterblicher? Was habe ich dir getan?`2\", sagt er mit sichtlicher Anstrengung.  ");
    output("\"`^Meinesgleichen wurde schon immer gesucht, um vernichtet zu werden. Warum? Wegen Geschichten aus fernen Ländern, ");
    output("die von Drachen erzählen, die Jagd auf die Schwachen machen? Ich sage dir, dass diese Märchen nur durch Missverständnisse ");
    output("über uns entstehen und nicht, weil wir eure Kinder fressen.`2\" Das Biest macht eine Pause um schwer zu atmen, dann fährt es fort: ");
    output("\"`^Ich werde dir jetzt ein Geheimnis verraten. Hinter mir liegen meine Eier. Meine Jungen werden schlüpfen und sich gegenseitig ");
    output("auffressen. Nur eins wird überleben, aber das wird das stärkste sein. Es wird sehr schnell wachsen und ");
    output("genauso stark werden wie ich.`2\" Der Atem des Drachen wird kürzer und flacher.`n`n");
    output("Du fragst: \"`#Warum erzählst du mir das? Kannst du dir nicht denken, dass ich deine Eier jetzt auch vernichten werde?`2\"");
    output("\"`^Nein, das wirst du nicht. Ich kenne noch ein weiteres Geheimnis, von dem du offensichtlich nichts weißt.`2\"`n`n");
    output("\"`#Bitte erzähle, oh mächtiges Wesen!`2\"`n`n");
    output("Das große Biest macht eine Pause, um seine letzten Kräfte zu sammeln. \"`^Eure Art verträgt das Blut Meinesgleichen nicht. ");
    output("Selbst wenn du überleben solltest, wirst du nur noch ein schwacher Mensch sein, kaum in der Lage, eine Waffe zu halten. Dein Geist ");
    output("wird vollständig geleert sein von allem, was du je gelernt hast. Nein, du bist keine Bedrohung für meine Kinder, denn du bist bereits tot!`2\"`n`n");
    output("Du bemerkst, dass deine Wahrnehmung tatsächlich bereits zu schwinden beginnt und fliehst Hals über Kopf aus der Höhle, nur darauf fixiert, ");
    output("die Hütte des Heilers zu erreichen, bevor es zu spät ist. Irgendwo unterwegs verlierst du deine Waffe und schließlich ");
    output("stolperst du über einen Stein in einem schmalen Bach. Deine Sicht ist inzwischen auf einen kleinen Kreis beschränkt, der in deinem Kopf ");
    output("herumzuwandern scheint. Während du so da liegst und in die Bäume starrst, glaubst du die Geräusche des Dorfes ");
    output("in der Nähe zu hören. Dein letzter ironischer Gedanke ist, dass, obwohl du den Drachen besiegt hast, er doch ");
    output("dich besiegt hat.`n`n");
    output("Während sich deine Wahrnehmung vollständig verabschiedet, fällt in der Drachenhöhle weit entfernt ein Ei auf die Seite und ein kleiner Riss ");
    output("erscheint in der dicken, lederartigen Schale.");
    
    if ($flawless)
    {
        output("`nDu fällst vorwärts um. Im Fallen erinnerst du sich, dass du es im letzten Moment doch noch geschafft hast, etwas von dem Schatz des Drachen einzustecken. Vielleicht war das alles ja doch kein totaler Verlust.");
    }
    
    
    
    
    // Account Extra Info laden
    $row_extra = user_get_aei();
    // END Account Extra Info laden
    
    // Knappe laden und steigern
    $sql = 'SELECT name,state,level FROM disciples WHERE master='.$session['user']['acctid'];
    $result = db_query($sql) or die(db_error(LINK));
    $rowk = db_fetch_assoc($result);
    if ($rowk['state']>0)
    {
        $newlevel=$rowk['level']+1;
        output("`^Dein Knappe ".$rowk['name']."`^ steigt auf Level ".$newlevel."`^ auf!`n");
        disciple_levelup();
    }
    
    addnav("Es ist ein neuer Tag","news.php");
    $sql = "describe accounts";
    $result = db_query($sql) or die(db_error(LINK));
    $hpgain = $session['user']['maxhitpoints'] - ($session['user']['level']*10);
    
    // Ausrüstung entfernen
    item_set_weapon('Fists',0,0,0,0,2);
    item_set_armor('T-Shirt',0,0,0,0,2);
    
    if ($session['user']['goldinbank']<0)
    {
        $session['user']['goldinbank']=round($session['user']['goldinbank']/10);
    }
    
    $nochange=array("acctid"=>1
    ,"name"=>1
    ,"sex"=>1
    ,"password"=>1
    ,"marriedto"=>1
    ,"charisma"=>1
    ,"title"=>1
    ,"login"=>1
    ,"dragonkills"=>1
    ,"locked"=>1
    ,"loggedin"=>1
    ,"superuser"=>1
    ,"gems"=>1
    ,"hashorse"=>1
    ,"gentime"=>1
    ,"gentimecount"=>1
    ,"lastip"=>1
    ,"uniqueid"=>1
    ,"dragonpoints"=>1
    ,"goldinbank"=>($session['user']['goldinbank']<0)?1:0
    ,"laston"=>1
    ,"prefs"=>1
    ,"lastmotd"=>1
    ,"emailaddress"=>1
    ,"emailvalidation"=>1
    ,"gensize"=>1
    ,"dragonage"=>1
    ,"donation"=>1
    ,"donationspent"=>1
    ,"donationconfig"=>1
    ,"pvpflag"=>1
    ,"charm"=>1
    ,"house"=>1
    ,"housekey"=>1
    ,"banoverride"=>1 // jt
    ,"beta"=>1
    ,"punch"=>1
    ,"battlepoints"=>1
    ,"reputation"=>1
    ,"petid"=>1
    ,"petfeed"=>1
    ,"rename_weapons"=>1
    ,"marks"=>1
    ,"profession"=>1
    ,"activated"=>1
    ,"guildid"=>1
    ,"guildfunc"=>1
    ,"guildrank"=>1
    ,"expedition"=>1
    ,"balance_dragon"=>1
    ,"surights"=>1
    );
    
    
    $bestage=$row_extra['bestdragonage'];
    
    $session['user']['dragonage'] = $session['user']['age'];
    if ($session['user']['dragonage'] <  $row_extra['bestdragonage'] ||	$row_extra['bestdragonage'] == 0)
    {
        $bestage = $session['user']['dragonage'];
    }
    for ($i=0; $i<db_num_rows($result); $i++)
    {
        $row = db_fetch_assoc($result);
        if ($nochange[$row['Field']])
        {
            
        }
        else
        {
            $session['user'][$row['Field']] = $row["Default"];
        }
    }
    
    $session['bufflist'] = array();
    $session['user']['gold']=	getsetting("newplayerstartgold",50);
    
    $session['user']['gold']+=getsetting("newplayerstartgold",50)*$session['user']['dragonkills'];
    if ($session['user']['gold']>(6*getsetting("newplayerstartgold",50)))
    {
        $session['user']['gold']=6*getsetting("newplayerstartgold",50);
        //	$session[user][gems]+=($session[user][dragonkills]-5);
    }
    
    $points = min($session['user']['dragonkills'], getsetting('maxdp_dk',50) );
    
    $log = 'DK: Erhält '.$points.' Punkte. Davor: '.$session['user']['donation'];
    
    $session['user']['donation'] += $points;
    
    $log .= ' Danach: '.$session['user']['donation'];
    
    if ($flawless)
    {
        $session['user']['gold'] += 3*getsetting("newplayerstartgold",50);
        $session['user']['gems'] += 1;
        $session['user']['donation']+=$points+5;
        $log .= ' +'.$points.' Zusatzpunkte für Perfekten Kampf';
        
        if ($session['user']['balance_dragon'] < 0)
        {
            $session['user']['balance_dragon'] = 1;
        }
        else
        {
            $session['user']['balance_dragon']+=2;
        }
        $session['user']['balance_dragon'] = min(20,$session['user']['balance_dragon']);
    }
    
    debuglog($log);
    
    // GILDENMOD
    require_once(LIB_PATH.'dg_funcs.lib.php');
    if ($session['user']['guildid'] && $session['user']['guildfunc'] != DG_FUNC_APPLICANT)
    {
        $g = &dg_load_guild($session['user']['guildid'],array('points','type','build_list'));
        $session['user']['gold'] = dg_calc_boni($session['user']['guildid'],'player_dkgold',$session['user']['gold']);
        $g['points'] += $dg_points['dk'];
        dg_log($session['user']['login'].' DK: '.$dg_points['dk'].' GP');
        dg_save_guild();
    }
    // END GILDENMOD
    
    // Drachenkillszähler gesamt inkrementieren
    savesetting('dkcounterges',getsetting('dkcounterges',0)+1);
    
    $session['user']['maxhitpoints']+=$hpgain;
    $session['user']['hitpoints']=$session['user']['maxhitpoints'];
    
    // Handle titles (modded by talion, get rid of these odd name / color code problems by adding additional backup fields for name and title in account_extra_info)
    
    $acctitle = '';
    $accname = '';
    
    $newtitle=$titles[$session['user']['dragonkills']][$session['user']['sex']];
    if ($newtitle=="")
    {
        $newtitle = $titles[sizeof($titles)-1][$session['user']['sex']];
    }
    
    $session['user']['title'] = $newtitle;
    
    if ($row_extra['cname'] != '')
    {
        // user has modified his name with colors
        $accname = $row_extra['cname'];
    }
    else
    {
        // otherwise, use his login
        $accname = $session['user']['login'];
    }
    
    if ($row_extra['ctitle'] != '')
    {
        // user has chosen his own title
        $acctitle = $row_extra['ctitle'];
    }
    else
    {
        // otherwise, use default title
        $acctitle = $newtitle;
    }
    
    $session['user']['name'] = trim($acctitle).' '.trim($accname);
    
    // END handle titles
    
    while (list($key,$val)=each($session['user']['dragonpoints']))
    {
        if ($val=="at")
        {
            $session['user']['attack']++;
        }
        if ($val=="de")
        {
            $session['user']['defence']++;
        }
    }
    
    $session['user']['laston']=date("Y-m-d H:i:s",time());
    
    output("`n`nDu erwachst umgeben von Bäumen. In der Nähe hörst du die Geräusche eines Dorfs.  ");
    output("Dunkel erinnerst du dich daran, dass du ein neuer Krieger bist, und an irgendwas von einem gefährlichen grünen Drachen, der die Gegend heimsucht. ");
    output("Du beschließt, dass du dir einen Namen verdienen könntest, wenn du dich vielleicht eines Tages dieser abscheulichen Kreatur stellst. ");
    addnews("`#".$accname."`# hat sich den Titel `&".$session['user']['title']."`# für den `^".$session['user']['dragonkills']."`#ten erfolgreichen Kampf gegen den `@Grünen Drachen`# verdient!");
    output("`n`n`^Du bist von nun an bekannt als `&".$session['user']['name']."`^!!");
    output("`n`n`&Weil du den Drachen ".$session['user']['dragonkills']." mal besiegt hast, startest du mit einigen Extras. Ausserdem behältst du alle zusätzlichen Lebenspunkte, die du verdient oder gekauft hast.`n");
    $session['user']['charm']+=5;
    output("`^Du bekommst FÜNF Charmepunkte für deinen Sieg über den Drachen!`n");
    $dkname = $session['user']['name'];
    savesetting("newdragonkill",addslashes($dkname));
    
    // ACCOUNT extra speichern
    user_set_aei(array('sentence'=>0,'mastertrain'=>0,'worms'=>0,'minnows'=>0,'bestdragonage'=>$bestage) );
    
    // dragonkill ends arenafight
    $sql = "DELETE FROM pvp WHERE acctid1=".$session['user']['acctid']." OR acctid2=".$session['user']['acctid'];
    db_query($sql) or die(db_error(LINK));
    
    
    $res = item_list_get(' owner='.$session['user']['acctid'].' AND (
(loose_dragon = 1 AND (deposit1='.ITEM_LOC_EQUIPPED.' OR deposit1=0))
OR
(loose_dragon = 2)) ' );
    $list = '-1';
    while ($i = db_fetch_assoc($res) )
    {
        $list .= ','.$i['id'];
    }
    item_delete(' id IN ( '.$list.' ) ' );
    
    if (getsetting("ci_active",0) && getsetting("ci_dk_mail_active",0) && $session['user']['superuser'] >= getsetting("ci_su",0))
    {
        if (getsetting("ci_dk",1) == $session['user']['dragonkills'] )
        {
            systemmail($session['user']['acctid'],getsetting("ci_dk_mail_head","`4Forum"), getsetting("ci_dk_mail_text",""));
        }
    }
    
    if ($session['user']['dragonkills'] == 1)
    {
        addhistory('`^Erster Drachenkill');
        // Temporär deaktiviert bis wir soweit sind
        
        //systemmail($session['user']['acctid'],"`4Forum","`&Herzlichen Glückwunsch zum ersten DK!`n`@Du hast jetzt die Möglichkeit Dich fürs Foum anmelden zu lassen.`n`nFalls Du dies willst, gehe einfach zur Vorzimmerdame des Magistrats im Dorfamt und hole dir den Passierschein A38.`n`nmfg Drachenserverteam");
    }
    else if ($session['user']['dragonkills'] == 10)
    {
        addhistory('`^Zehnter Drachenkill');
    }
    else if ($session['user']['dragonkills'] == 100)
    {
        addhistory('`^Hundertster Drachenkill');
    }
    
    
}

if ($_GET['op']=="run")
{
    output("Der Schwanz der Kreatur versperrt den einzigen Ausgang aus der Höhle!");
    $_GET['op']="fight";
}
if ($_GET['op']=="fight" || $_GET['op']=="run")
{
    $battle=true;
}
if ($battle)
{
    include("battle.php");
    if ($victory)
    {
        $flawless = 0;
        if ($badguy['diddamage'] != 1)
        {
            $flawless = 1;
        }
        $badguy=array();
        $session['user']['badguy']="";
        $session['user']['dragonkills']++;
        $session['user']['reputation']+=2;
        output("`&Mit einem letzten mächtigen Knall lässt `@der Grüne Drachen`& ein furchtbares Brüllen los und fällt dir vor die Füße, endlich tot.");
        addnews("`&".$session['user']['name']."`& hat die abscheuliche, als `@Grüner Drachen`& bekannte Kreatur besiegt. Über alle Länder freuen sich die Völker!");
        addnav("Weiter","dragon.php?op=prologue1&flawless=$flawless");
    }
    else
    {
        if ($defeat)
        {
            
            if ($session['user']['balance_dragon'] > 0)
            {
                $session['user']['balance_dragon']=round($session['user']['balance_dragon']*0.5);
            }
            else
            {
                $session['user']['balance_dragon']--;
            }
            $session['user']['balance_dragon'] = max(-10,$session['user']['balance_dragon']);
            
            addnav("Tägliche News","news.php");
            $sql = "SELECT taunt FROM taunts ORDER BY rand(".e_rand().") LIMIT 1";
            $result = db_query($sql) or die(db_error(LINK));
            $taunt = db_fetch_assoc($result);
            $taunt = str_replace("%s",($session['user']['sex']?"sie":"ihn"),$taunt['taunt']);
            $taunt = str_replace("%o",($session['user']['sex']?"sie":"er"),$taunt);
            $taunt = str_replace("%p",($session['user']['sex']?"ihre(r/m)":"seine(r/m)"),$taunt);
            $taunt = str_replace("%x",($session['user']['weapon']),$taunt);
            $taunt = str_replace("%X",$badguy['creatureweapon'],$taunt);
            $taunt = str_replace("%W",$badguy['creaturename'],$taunt);
            $taunt = str_replace("%w",$session['user']['name'],$taunt);
            $session['user']['reputation']--;
            addnews("`%".$session['user']['name']."`5 wurde gefressen, als ".($session['user']['sex']?"sie":"er")." dem `@Grünen Drachen`5 begegnete!!!  ".($session['user']['sex']?"Ihre":"Seine")." Knochen liegen nun am Eingang der Höhle, genau wie die der Krieger, die vorher kamen.`n$taunt");
            $session['user']['alive']=false;
			
            $str_loose_log = 'Gld: '.$session['user']['gold'];
			
            $session['user']['gold']=0;
            $session['user']['hitpoints']=0;
            $session['user']['badguy']="";
            output("`b`%$badguy[creaturename]`& hat dich gefressen!!!`n");
            output("`4Du hast dein ganzes Gold verloren!`n");
            output("Du kannst morgen wieder kämpfen.`0");
            
            // item
            $item_hook_info ['min_chance'] = e_rand(1,255);
            
            $res = item_list_get(' owner='.$session['user']['acctid'].' AND loose_dragon_death>='.$item_hook_info ['min_chance'] , 'ORDER BY RAND() LIMIT 1' );
            
            if (db_num_rows($res) )
            {
                
                $item = db_fetch_assoc($res);
                
                if (item_delete(' id='.$item['id'] ) )
                {
					$str_loose_log .= ',Item: '.$item['name'];
                    output('`n`4Du verlierst `^'.$item['name'].'`4!`n');
                }
                
            }
            
            $sql = 'SELECT name,state,level FROM disciples WHERE master='.$session['user']['acctid'];
            $result = db_query($sql) or die(db_error(LINK));
            $rowk = db_fetch_assoc($result);
            
            $kname=$rowk['name'];
            $kstate=$rowk['state'];
            
            if (($kstate>0) && ($kstate<20))
            {
                output("`^$kname `4 wird nun sein Leben als Sklave des grünen Drachen verbringen!`n`n");
                disciple_remove();
                $str_loose_log = ', Knappe';
            }
			
			debuglog("Drachentod: ".$str_loose_log);
			
            page_footer();
        }
        else
        {
            fightnav(true,false);
        }
    }
}
page_footer();
?>