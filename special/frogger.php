<?php
// Der verwandelte Frosch
// In Anlehnung an "fairy1.php"
// by Maris (Maraxxus@gmx.de)

if (!isset($session))
{
    exit();
}

$sql = 'SELECT ctitle,cname FROM account_extra_info WHERE acctid='.$session['user']['acctid'];
$res = db_query($sql);
$row_extra = db_fetch_assoc($res);

if ($_GET['op']=="kiss")
{
    output("`2Du nimmst ".($session[user][sex]?"den Frosch ":"die Kr�te ")."auf deine Hand und gibst ".($session[user][sex]?"ihm":"ihr")." einen dicken Kuss.`0`n`n");
    
    switch (e_rand(1,10))
    {
    case 1:
    case 2:
        output("`2".($session[user][sex]?"Der Frosch ":"Die Kr�te ")."verwandelt sich auch augenblicklich, allerdings jedoch nicht in ".($session[user][sex]?"den erwarteten Prinzen":"die erwartete Prinzessin").", sondern in ein".($session[user][sex]?"en Waldgeist, der seinen ":"e Fee, die ihren ")."Schabernack mit dir trieb.`nZornig �ber die ausgebliebene Belohnung und deine Besch�mung steigert sich deine Motivation zu k�mpfen.`n");
        output("Du bekommst einen zus�tzlichen Waldkampf!");
        $session[user][turns]++;
        break;
    case 3:
    case 4:
        output("`2".($session[user][sex]?"Der Frosch ":"Die Kr�te ")."verwandelt sich augenblicklich, und vor dir steht ".($session[user][sex]?"ein schmucker Prinz":"eine h�bsche Prinzessin").".`n".($session[user][sex]?"Er ":"Sie ")."bedankt sich h�flich bei dir und �berreicht dir ".($session[user][sex]?"seine ":"ihre ")."Halskette. Den `^Edelstein`2, der sich daran befindet, l�st du nat�rlich sofort mit deinem Dolch ab und l�sst ihn in deiner Tasche verschwinden, den wertlosen Rest wirfst du fort.`0`n");
        $session[user][gems]+=1;
        break;
    case 5:
        output("`2".($session[user][sex]?"Der Frosch ":"Die Kr�te ")."verwandelt sich tats�chlich, und vor dir steht ".($session[user][sex]?"ein strahlender Prinz":"eine bildh�bsche Prinzessin").".`n".($session[user][sex]?"Er ":"Sie ")."bedankt sich �berschw�ngloch bei dir und verk�ndet deine heldenhafte Tat im ganzen Reich, was dir `^maximales Ansehen`2 einbringt!`0`n");
        addnews("`@".$session['user']['name']."`# hat ".($session[user][sex]?"einen Prinzen ":"eine Prinzessin ")."von einem Fluch erl�st und sich damit ein hohes Ansehen verdient.");
        $session[user][reputation]=50;
        break;
    case 6:
    case 7:
        output("`2".($session[user][sex]?"Der Frosch ":"Die Kr�te ")."verwandelt sich tats�chlich, und vor dir steht ".($session[user][sex]?"ein Prinz":"eine Prinzessin").".`n".($session[user][sex]?"Er ":"Sie ")."bedankt sich aufrichtig bei dir und belohnt dich mit `^2500 Goldm�nzen`2!`0`n");
        $session['user']['gold']+=2500;
        break;
    case 8:
    case 9:
    case 10:
        if (e_rand(1,6)!=4)
        {
            output("Doch die dumme Kreatur denkt ja gar nicht daran sich zu verwandeln.`nLangsam aber sicher musst du dir eingestehen, dass du von eine".($session[user][sex]?"m sprechenden Frosch ":"r sprechenden Kr�te ")."hereingelegt wurdest, ".($session[user][sex]?"der ":"die ")."nun eiligst davon h�pft.`nDu verbringst die n�chste Zeit damit dir fluchend den Mund auszusp�len. Pfui!`n`4Du verlierst einen Waldkampf!");
            $session['user']['turns']--;
            if ($session['user']['turns']<0)
            {
                $session['user']['turns']=0;
            }
        }
        else
        {	
			item_set_weapon('Klebrige Zunge', -1, -1, -1, 0, 1);
			item_set_armor('Schleimige Haut', -1, -1, -1, 0, 1);

            if ($session[user][title]!="`2Kr�te`0" && $session[user][title]!="`2Frosch`0" )
            {
                output("`2".($session[user][sex]?"Der Frosch ":"Die Kr�te ")."verwandelt sich tats�chlich, und vor dir steht ".($session[user][sex]?"ein Prinz":"eine Prinzessin").".`nDoch irgendwie hat nicht nur ".($session[user][sex]?"er ":"sie ")."sich verwandelt, sondern auch du ver�nderst deine Gestalt!`n`#Du wurdest in ein".($session[user][sex]?"e Kr�te ":"en Frosch ")."verwandelt und musst jetzt in dieser Form dein Dasein fristen, w�hrend ".($session[user][sex]?"der Prinz ":"die Prinzessin ")."von dannen eilt, gl�cklich dar�ber jemanden gefunden zu haben, der ".($session[user][sex]?"ihn ":"sie ")."aus diesem Schicksal abl�st.`0`n");
                addnews("`@".$session['user']['name']."`@ hat heute einen Imagewandel erfahren.");
                if ($session['user']['sex'])
                {
                    $newtitle="`2Kr�te`0";
                }
                else
                {
                    $newtitle="`2Frosch`0";
                }
                
                $regname = ($row_extra['cname'] ? $row_extra['cname'] : $session['user']['login']);
                
                if ($session[user][title]!="")
                {
                    $session['user']['title'] = $newtitle;
                    $session[user][name] = $newtitle." ".$regname;
                    $session[user][title] = $newtitle;
                    
                }
            }
            else
            {
                output("`2Ihr beide turtelt eine Weile, aber keiner von euch beiden verwandelt sich.`nDu bist weiterhin ein".($session[user][sex]?"e Kr�te ":" Frosch ")."!`0`n");
            }
            
            
        }
        break;
        
    }
    
	$session[user][specialinc]="";    
}
else if($_GET['op'] == 'dont') {
	output("`2Du willst dich nicht auf so ein Spiel einlassen und zertrittst die Kreatur auf dem Boden.`0");
    $session[user][specialinc]="";
}
else
{

	output("`2Dir h�pft ein".($session[user][sex]?" Frosch ":"e Kr�te ")."vor die F��e. \"`^So helft mir, edle".($session[user][sex]?" Dame! ":"r Recke! ")."Ich bin ein".($session[user][sex]?" verzauberter Prinz ":"e verzauberte Prinzessin ")."und kann nur durch einen Kuss zur�ckverwandelt werden!`2\", klagt ".($session[user][sex]?"er":"sie").".`nWas wirst du tun?");
    addnav("K�ssen","forest.php?op=kiss");
    addnav("Vergiss es!","forest.php?op=dont");
    $session[user][specialinc]="frogger.php";
    
}


?>