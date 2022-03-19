<?php

// RPG im Haus

require_once("common.php");

addcommentary();

// base values for pricing and chest size:

$goldmax=15000;
$gemmax=50;

$goldcost=30000;
$gemcost=50;

page_header('Im Inneren eines Hauses');

addcommentary();
checkday();
is_new_day();

if ($_GET[id])
{
    $session[housekey]=(int)$_GET[id];
}

if (!$session[housekey])
{
    redirect("houses.php");
}

$sql = "SELECT * FROM houses WHERE houseid=".$session[housekey]." ORDER BY houseid DESC";

$result = db_query($sql) or die(db_error(LINK));

$row = db_fetch_assoc($result);

if ($_GET[act]=="takekey")
{
    
    if (!$_POST[ziel])
    {
        
        $sql = "SELECT owner FROM keylist WHERE value1=$row[houseid] ORDER BY id ASC";
        
        $result = db_query($sql) or die(db_error(LINK));
        
        output("<form action='inside_houses.php?act=takekey' method='POST'>",true);
        
        output("`2Wem willst du den Schlüssel wegnehmen? <select name='ziel'>",true);
        
        for ($i=0; $i<db_num_rows($result); $i++)
        {
            
            $item = db_fetch_assoc($result);
            
            $sql = "SELECT acctid,name,login FROM accounts WHERE acctid=$item[owner] ORDER BY login DESC";
            
            $result2 = db_query($sql) or die(db_error(LINK));
            
            $row2 = db_fetch_assoc($result2);
            
            if ($amt!=$row2[acctid] && $row2[acctid]!=$row[owner])
            {
                output("<option value=\"".rawurlencode($row2['name'])."\">".preg_replace("'[`].'","",$row2['name'])."</option>",true);
            }
            
            $amt=$row2[acctid];
            
        }
        
        output("</select>`n`n",true);
        
        output("<input type='submit' class='button' value='Schlüssel abnehmen'></form>",true);
        
        addnav("","inside_houses.php?act=takekey");
        
    }
    else
    {
        
        $sql = "SELECT acctid,name,location, restatlocation, login,gold,gems FROM accounts WHERE name='".addslashes(rawurldecode(stripslashes($_POST['ziel'])))."' AND locked=0";
        
        $result2 = db_query($sql);
        
        $row2  = db_fetch_assoc($result2);
        
        output("`2Du verlangst den Schlüssel von `&$row2[name]`2 zurück.`n");
        
        $sql = "SELECT owner FROM keylist WHERE value1=$row[houseid] AND owner<>$row[owner] ORDER BY id ASC";
        
        $result = db_query($sql) or die(db_error(LINK));
        
        if (($row[status]==5) or($row[status]==11) or($row[status]==21) or($row[status]==31) or($row[status]==41) or($row[status]==51) or($row[status]==61) or($row[status]==71) or($row[status]==81) or($row[status]==91) or($row[status]==101))
        {
            $ausbau=1;
        }
        
        $goldgive=round($row[gold]/(db_num_rows($result)+1));
        
        $gemsgive=round($row[gems]/(db_num_rows($result)+1));
        
        if ($ausbau!=1)
        {
            systemmail($row2[acctid],"`@Schlüssel zurückverlangt!`0","`&{$session['user']['name']}
            `2 hat den Schlüssel zu Haus Nummer `b$row[houseid]`b($row[housename]`2) zurückverlangt. Du bekommst `^$goldgive`2 Gold auf die Bank und `#$gemsgive`2 Edelsteine aus dem gemeinsamen Schatz ausbezahlt!");
            output("$row2[name]`2 bekommt `^$goldgive`2 Gold und `#$gemsgive`2 Edelsteine aus dem gemeinsamen Schatz.");
            // Überprüfen ob die Person auch in dem Haus liegt, von dem der Schlüssel war, sonst natürlich im Haus liegen lassen by Azura
            
            $row2['name'] = addslashes($row2['name']);
            
            if ($row2['restatlocation']==$row['houseid'])
            {
                $sql = "UPDATE accounts SET goldinbank=goldinbank+$goldgive,gems=gems+$gemsgive,location=0 WHERE acctid=$row2[acctid]";
                db_query($sql);
            }
            else
            {
                $sql = "UPDATE accounts SET goldinbank=goldinbank+$goldgive,gems=gems+$gemsgive WHERE acctid=$row2[acctid]";
                db_query($sql);
            }
            $sql = "UPDATE houses SET gold=gold-$goldgive,gems=gems-$gemsgive WHERE houseid=$row[houseid]";
            db_query($sql);
            
            $sql = "INSERT INTO commentary (postdate,section,author,comment) VALUES (now(),'house-".$row[houseid]."',".$session[user][acctid].",'/me `^nimmt $row2[name]`^ einen Schlüssel ab. $row2[name]`^ bekommt einen Teil aus dem Schatz.')";
            db_query($sql) or die(db_error(LINK));
            
        }
        else
        {
            systemmail($row2[acctid],"`@Schlüssel zurückverlangt!`0","`&{$session['user']['name']}
            `2 hat den Schlüssel zu Haus Nummer `b$row[houseid]`b($row[housename]`2) zurückverlangt. Da sich das Haus gerade im Ausbau befindet bekommst du nichts ausbezahlt!");
            $sql = "INSERT INTO commentary (postdate,section,author,comment) VALUES (now(),'house-".$row[houseid]."',".$session[user][acctid].",'/me `^nimmt $row2[name]`^ einen Schlüssel ab.')";
            db_query($sql) or die(db_error(LINK));
            
        }
        
        $sql = "UPDATE keylist SET owner=$row[owner],hvalue=0,value2=0,gold=0,gems=0,chestlock=0 WHERE owner=$row2[acctid] AND value1=$row[houseid]";
        db_query($sql);
        
        // An Hausbesitzer zurückgeben
        
        // Einladungen in Privatgemächer löschen
        item_delete(' tpl_id="prive" AND value1='.$row['houseid'].' AND value2='.$row2['acctid']);
        
        // Privatgemächer zurücksetzen
        item_set(' tpl_id="privb" AND value1='.$row['houseid'].' AND owner='.$row2['acctid'], array('owner'=>0,'description'=>'') );
        
        // Möbel für Privatgemächer zurücksetzen
        item_set(' deposit1='.$row['houseid'].' AND deposit2='.$row2['acctid'], array('deposit1'=>0,'deposit2'=>0) );
    }
    
    addnav("Zurück zum Haus","inside_houses.php");
    
}
else if ($_GET[act]=="givekey")
{
    
    if (!$_POST['ziel'])
    {
        
        output("`2Einen Schlüssel für dieses Haus hat:`n`n");
        
        $sql = "SELECT *,accounts.name AS besitzer FROM keylist LEFT JOIN accounts ON accounts.acctid=keylist.owner WHERE value1=$row[houseid] AND owner<>".$session[user][acctid]." ORDER BY id ASC";
        
        $result = db_query($sql) or die(db_error(LINK));
        
        for ($i=0; $i<db_num_rows($result); $i++)
        {
            
            $item = db_fetch_assoc($result);
            
            output("`c`& $item[besitzer]`0`c");
            
        }
        
        $sql = "SELECT id FROM keylist WHERE value1=$row[houseid] AND owner=$row[owner] ORDER BY id ASC";
        
        $result = db_query($sql) or die(db_error(LINK));
        
        if (db_num_rows($result)>0)
        {
            
			$session['keygiven_check'] = md5(time());
			
            output("`n`2Du kannst noch `b".db_num_rows($result)."`b Schlüssel vergeben.");
            
            output("<form action='inside_houses.php?act=givekey' method='POST'>",true);
            
            output("An wen willst du einen Schlüssel übergeben? <input name='ziel'>`n", true);
            
            output("<input type='submit' class='button' value='Übergeben'></form>",true);
            
            output("`n`nWenn du einen Schlüssel vergibst, wird der Schatz des Hauses gemeinsam genutzt. Du kannst einem Mitbewohner zwar jederzeit den Schlüssel wieder wegnehmen, ");
            
            output("aber er wird dann einen gerechten Anteil aus dem gemeinsamen Schatz bekommen.");
            
            addnav("","inside_houses.php?act=givekey");
            
        }
        else
        {
            
            output("`n`2Du hast keine Schlüssel mehr übrig. Vielleicht kannst du in der Jägerhütte noch einen nachmachen lassen?");
            
        }
        
    }
    else
    {
        
        if ($_GET['subfinal']==1)
        {
            
            $sql = "SELECT acctid,name,login,lastip,emailaddress,dragonkills,level,sex FROM accounts WHERE name='".addslashes(rawurldecode(stripslashes($_POST['ziel'])))."' AND locked=0";
            
        }
        else
        {
            
            $ziel = stripslashes(rawurldecode($_POST['ziel']));
            
            $name="%";
            
            for ($x=0; $x<strlen($ziel); $x++)
            {
                
                $name.=substr($ziel,$x,1)."%";
                
            }
            
            $sql = "SELECT acctid,name,login,lastip,emailaddress,dragonkills,level,sex FROM accounts WHERE name LIKE '".addslashes($name)."' AND locked=0";
            
        }
        
        $result2 = db_query($sql);
		if (db_num_rows($result2) == 0)
        {
            
            output("`2Es gibt niemanden mit einem solchen Namen. Versuchs nochmal.");
            
        }
        else if (db_num_rows($result2) > 100)
        {
            
            output("`2Es gibt über 100 Krieger mit einem ähnlichen Namen. Bitte sei etwas genauer.");
            
        }
        else if (db_num_rows($result2) > 1)
        {
            
            output("`2Es gibt mehrere mögliche Krieger, denen du einen Schlüssel übergeben kannst.`n");
            
            output("<form action='inside_houses.php?act=givekey&subfinal=1' method='POST'>",true);
            
            output("`2Wen genau meinst du? <select name='ziel'>",true);
            
            for ($i=0; $i<db_num_rows($result2); $i++)
            {
                
                $row2 = db_fetch_assoc($result2);
                
                output("<option value=\"".rawurlencode($row2['name'])."\">".preg_replace("'[`].'","",$row2['name'])."</option>",true);
                
            }
            
            output("</select>`n`n",true);
            
            output("<input type='submit' class='button' value='Schlüssel übergeben'></form>",true);
            
            addnav("","inside_houses.php?act=givekey&subfinal=1");
            
            //addnav("","inside_houses.php?act=givekey");
            // why the hell was this in there?
            
        }
        else
        {
            
            $row2  = db_fetch_assoc($result2);
            
            $sql = "SELECT owner FROM keylist WHERE owner=$row2[acctid] AND value1=$row[houseid] ORDER BY id ASC";
            
            $result = db_query($sql) or die(db_error(LINK));
            
            $item = db_fetch_assoc($result);
            
            if ($row2[login] == $session[user][login])
            {
                
                output("`2Du kannst dir nicht selbst einen Schlüssel geben.");
                
            }
            else if ($item[owner]==$row[owner])
            {
                
                output("`2$row2[name]`2 hat bereits einen Schlüssel!");
                
            }
            else if ($session['user']['lastip'] == $row2['lastip'] || ($session['user']['emailaddress'] == $row2['emailaddress'] && $row2[emailaddress]))
            {
                
                output("`2Deine Charaktere dürfen leider nicht miteinander interagieren!");
                
            }
            else if ($row2['level']<5 && $row2['dragonkills']<1)
            {
                
                output("`2$row2[name]`2 ist noch nicht lange genug um Dorf, als dass du ".($row2['sex']?"ihr":"ihm")." vertrauen könntest. Also beschließt du, noch eine Weile zu beobachten.");
                
            }
            else
            {
            	// Kleine Abfrage, um sicherzustellen dass durch Aktualisieren nicht mehrfach ein Schlüssel vergeben wird
				if($session['keygiven_check'] != '') {
				    
					unset($session['keygiven_check']);
					
					$sql = "SELECT id FROM keylist WHERE value1=$row[houseid] AND owner=$row[owner] ORDER BY id ASC LIMIT 1";
					
					$result = db_query($sql) or die(db_error(LINK));
					
					$knr = db_fetch_assoc($result);
					
					$knr=$knr['id'];
													
					systemmail($row2[acctid],"`@Schlüssel erhalten!`0","`&{$session['user']['name']}
					`2 hat dir einen Schlüssel zu Haus Nummer `b$row[houseid]`b($row[housename]`2) gegeben!");
					
					$row2['name'] = addslashes($row2['name']);
					
					$sql = "UPDATE keylist SET owner=$row2[acctid],hvalue=0,value2=0,gold=0,gems=0,chestlock=0 WHERE owner=$row[owner] AND value1=$row[houseid] AND id=$knr";
					
					db_query($sql);
					
					$sql = "INSERT INTO commentary (postdate,section,author,comment) VALUES (now(),'house-".$row[houseid]."',".$session[user][acctid].",'/me `^gibt $row2[name]`^ einen Schlüssel.')";
					
					db_query($sql) or die(db_error(LINK));
				}
				
				output("`2Du übergibst `&$row2[name]`2 einen Schlüssel für dein Haus. Du kannst den Schlüssel zum Haus jederzeit wieder wegnehmen, aber $row2[name]`2 wird dann ");
                
                output("einen gerechten Anteil aus dem gemeinsamen Schatz des Hauses bekommen.`n");
                
            }
            
        }
        
    }
    
    addnav("Zurück zum Haus","inside_houses.php");
    
}
else if ($_GET[act]=="takegold")
{
    
    if (($row[status]==5) || ($row[status]==11) || ($row[status]==21) || ($row[status]==31) || ($row[status]==41) || ($row[status]==51) || ($row[status]==61) || ($row[status]==71) || ($row[status]==81) || ($row[status]==91) || ($row[status]==101))
    {
        
        output("Hier wird gearbeitet! Du wirst dich doch wohl nicht an der Baukasse vergreifen...`n");
        
        addnav("Zurück ins Haus","inside_houses.php");
        
    }
    else
    {
        
		$rowe = user_get_aei('goldin');
		
        $maxtfer = $session[user][level]*getsetting("transferperlevel",25)*4;
								
		$maxtfer = max($maxtfer-$rowe['goldin'],0);  
        
        $sql = "SELECT gold,chestlock FROM keylist WHERE value1=$row[houseid] AND owner=".$session[user][acctid]." AND owner!=".$row['owner'];
        $res = db_query($sql) or die(db_error(LINK));
        $row2 = db_fetch_assoc($res);
        if ($row2[chestlock]!=1)
        {
            
            if (!isset($_POST['gold']))
            {
                                
                
                output("`2Es befindet sich `^$row[gold]`2 Gold in der Schatztruhe des Hauses.`nDu darfst heute noch `^$maxtfer`2 Gold mitnehmen.`n(Leerlassen, um Maximum zu entnehmen.)");
                
                output("`2<form action=\"inside_houses.php?act=takegold\" method='POST'>",true);
                
                output("`nWieviel Gold mitnehmen? <input type='gold' name='gold'>`n`n",true);
                
                output("<input type='submit' class='button' value='Mitnehmen'>",true);
                
                addnav("","inside_houses.php?act=takegold");
                
            }
            else
            {
                								
                $amt=abs((int)$_POST[gold]);
				
				if($amt == 0) {	// Maximum bei leerem Feld
					
					$amt = min($maxtfer,$row['gold']);
					
				}
                
                if ($amt>$row[gold])
                {
                    
                    output("`2So viel Gold ist nicht mehr da.");
                    
                }
                else if ($maxtfer<$amt)
                {
                    
                    output("`2Du darfst maximal `^$maxtfer`2 Gold auf einmal nehmen.");
                    
                }
                else if ($amt<0)
                {
                    
                    output("`2Wenn du etwas in den Schatz legen willst, versuche nicht, etwas negatives herauszunehmen.");
                    
                }
                else if($amt > 0)
                {
                    
                    $row[gold]-=$amt;
                    
                    $session[user][gold]+=$amt;
                    
                    user_set_aei(array('goldin'=>$rowe['goldin']+$amt));
                    
                    $sql = "UPDATE houses SET gold=$row[gold] WHERE houseid=$row[houseid]";
                    
                    db_query($sql) or die(db_error(LINK));
                    
                    output("`2Du hast `^$amt`2 Gold genommen. Insgesamt befindet sich jetzt noch `^$row[gold]`2 Gold im Haus.");
                    
                    $goldspent=$row2[gold];
                    $goldspent-=$amt;
                    
                    if (db_num_rows($res))
                    {
                        $sql = "UPDATE keylist SET gold=$goldspent WHERE value1=$row[houseid] AND owner=".$session[user][acctid]."";
                        db_query($sql) or die(db_error(LINK));
                    }
                    
                    $sql = 'INSERT INTO commentary 
									(postdate,section,author,comment) 
								VALUES 
									(now(),"house-'.$row['houseid'].'",'.$session['user']['acctid'].',"/me `\$nimmt `^'.$amt.'`\$ Gold.")';
                    
                    db_query($sql) or die(db_error(LINK));
                    
                }
                
            }
        }
        else
        {
            output("`&Der Hausherr hat ein schweres, doppeltes Sicherheitsschloss an der Truhe angebracht, dass diese vor unerwünschtem Zugriff schützt.`nDa du keinen Schlüssel für dieses Schloss hast, sieht es wohl so aus als ob der Hausherr nicht will, dass du dich weiterhin an seinen Reichtümern vergreifst.`nDas tut mir aber leid...");
        }
        
        addnav("Zurück zum Haus","inside_houses.php");
    }
    
}
else if ($_GET[act]=="givegold")
{
    
	$rowe = user_get_aei('goldout');
	
    $maxout = $session[user][level]*getsetting("maxtransferout",25);
    
	$transleft = max($maxout - $rowe['goldout'],0);
	
    $sql = "SELECT gold,chestlock FROM keylist WHERE value1=$row[houseid] AND owner=".$session[user][acctid]."";
    $res = db_query($sql) or die(db_error(LINK));
    $row2 = db_fetch_assoc($res);
    if ($row2[chestlock]!=1)
    {
        if (!isset($_POST['gold']))
        {
                                    
            output("`2Es befindet sich `^$row[gold]`2 Gold in der Schatztruhe des Hauses.`n
					Du darfst heute noch `^$transleft`2 Gold deponieren.`n(Feld leerlassen, um Maximum einzuzahlen)");
            
            output("`2<form action=\"inside_houses.php?act=givegold\" method='POST'>",true);
            
            output("`nWieviel Gold deponieren? <input type='gold' name='gold'>`n`n",true);
            
            output("<input type='submit' class='button' value='Deponieren'>",true);
            
            addnav("","inside_houses.php?act=givegold");
            
        }
        else
        {
                                    
            // Anwesen, Gasthaus
            if (($row['status']==10) || ($row['status']==12) || ($row['status']==13) ||
            ($row['status']==17) || ($row['status']==18) || ($row['status']==19))
            {
                $goldmax=75000;
            }
            else // Villa
            if (($row['status']==14) || ($row['status']==15) || ($row['status']==16))
            {
                $goldmax=150000;
            }
            else // Versteck, Refugium, Keller
            if (($row['status']==30) || (($row['status']>=32) && ($row['status']<=39)))
            {
                $goldmax=3000;
            }
            else // Ausbau Stufe 1
            if ($row[status]==5)
            {
                $goldmax=300000;
            }
            else // Ausbau Stufe 2
            if (($row[status]==11) || ($row[status]==21) || ($row[status]==31) || ($row[status]==41) || ($row[status]==51) || ($row[status]==61) || ($row[status]==71) || ($row[status]==81) || ($row[status]==91) || ($row[status]==101))
            {
                $goldmax=500000;
            }
            // Alles Andere
            else
            {
                $goldmax=round($goldcost/2);
            }
            
			$amt=abs((int)$_POST[gold]);
			
			if($amt == 0) {	// Maximum
				
				$amt = min($transleft, round($goldmax)-$row['gold']);
				$amt = min($amt,$session['user']['gold']);
				
			}
			
            if ($amt>$session[user][gold])
            {
                output("`2So viel Gold hast du nicht dabei.");
            }
            else if ($row[gold]>round($goldmax))
            {
                output("`2Der Schatz ist voll.");
            }
            else if ($amt>(round($goldmax)-$row[gold]))
            {
                
                output("`2Du gibst alles, aber du bekommst beim besten Willen nicht so viel in den Schatz.");
                
            }
            else if ($amt<0)
            {
                
                output("`2Wenn du etwas aus dem Schatz nehmen willst, versuche nicht, etwas negatives hineinzutun.");
                
            }
            else if ($rowe['goldout']+$amt > $maxout)
            {
                
                output("`2Du darfst nicht mehr als `^$maxout`2 Gold pro Tag deponieren.");
                
            }
            else if ($amt > 0) 
            {
                
                $row[gold]+=$amt;
                
                $session[user][gold]-=$amt;
                
                user_set_aei(array('goldout'=>$rowe['goldout']+$amt));
                
                output("`2Du hast `^$amt`2 Gold deponiert. Insgesamt befinden sich jetzt `^$row[gold]`2 Gold im Haus.");
                
                $goldspent=$row2[gold];
                $goldspent+=$amt;
                
                $sql = "UPDATE houses SET gold=$row[gold] WHERE houseid=$row[houseid]";
                db_query($sql) or die(db_error(LINK));
                
                if (db_num_rows($res))
                {
                    $sql = "UPDATE keylist SET gold=$goldspent WHERE value1=$row[houseid] AND owner=".$session[user][acctid]."";
                    db_query($sql) or die(db_error(LINK));
                }
                
                $sql="INSERT INTO commentary (postdate,section,author,comment) VALUES (now(),'house-".$row[houseid]."',".$session[user][acctid].",'/me `@deponiert `^$amt`@ Gold.')";
                
                db_query($sql) or die(db_error(LINK));
                
            }
			else {
				output('`2Irgendwie mag der Schatz nicht so ganz, wie du willst..');
			}
            
        }
    }
    else
    {
        output("`&Der Hausherr hat ein schweres, doppeltes Sicherheitsschloss an der Truhe angebracht, dass diese vor unerwünschtem Zugriff schützt.`nDa du keinen Schlüssel für dieses Schloss hast, sieht es wohl so aus als ob der Hausherr nicht will, dass du dich weiterhin an seinen Reichtümern vergreifst.`nDas tut mir aber leid...");
    }
    addnav("Zurück zum Haus","inside_houses.php");
    
    
    
}
else if ($_GET[act]=="takegems")
{
    		
    if (($row[status]==5) || ($row[status]==11) || ($row[status]==21) || ($row[status]==31) || ($row[status]==41) || ($row[status]==51) || ($row[status]==61) || ($row[status]==71) || ($row[status]==81) || ($row[status]==91) || ($row[status]==101))
    {
        
        output("Hier wird gearbeitet! Du wirst dich doch wohl nicht an der Baukasse vergreifen...`n");
        
        addnav("Zurück ins Haus","inside_houses.php");
        
    }
    else
    {	
	
		$rowe = user_get_aei('gemsin');
		
		$maxtfer = max(getsetting('housemaxgemsout',10) - $rowe['gemsin'],0);
        
        $sql = "SELECT gems,chestlock FROM keylist WHERE value1=$row[houseid] AND owner=".$session[user][acctid]."";
        $res = db_query($sql) or die(db_error(LINK));
        $row2 = db_fetch_assoc($res);
        if ($row2[chestlock]!=1)
        {
            
            if (!$_POST[gems])
            {
                
                output("`2Es befinden sich `#$row[gems]`2 Edelsteine in der Schatztruhe des Hauses.`nDu darfst heute noch `^$maxtfer`2 Edelsteine mitnehmen.`n");
                
                output("`2<form action=\"inside_houses.php?act=takegems\" method='POST'>",true);
                
                output("`nWieviele Edelsteine mitnehmen? <input type='gems' name='gems'>`n`n",true);
                
                output("<input type='submit' class='button' value='Mitnehmen'>",true);
                
                addnav("","inside_houses.php?act=takegems");
                
            }
            else
            {
                
                $amt=abs((int)$_POST[gems]);
                
                if ($amt>$row[gems])
                {
                    
                    output("`2So viele Edelsteine sind nicht mehr da.");
                    
                }
                else if ($amt<0)
                {
                    
                    output("`2Wenn du etwas in den Schatz legen willst, versuche nicht, etwas negatives herauszunehmen.");
                    
                }
				else if ($maxtfer<$amt)
                {
                    
                    output("`2Du darfst maximal `^$maxtfer`2 Edelsteine pro Tag nehmen.");
                    
                }
                else if($amt > 0)
                {
                    
                    $row[gems]-=$amt;
                    
                    $session[user][gems]+=$amt;
                    
					user_set_aei(array('gemsin'=>$rowe['gemsin']+$amt));
					
                    $sql = "UPDATE houses SET gems=$row[gems] WHERE houseid=$row[houseid]";
                    
                    db_query($sql);
                    
                    output("`2Du hast `#$amt`2 Edelsteine genommen. Insgesamt befinden sich jetzt noch `#$row[gems]`2 Edelsteine im Haus.");
                    
                    $gemsspent=$row2[gems];
                    $gemsspent-=$amt;
                    
                    if (db_num_rows($res))
                    {
                        $sql = "UPDATE keylist SET gems=$gemsspent WHERE value1=$row[houseid] AND owner=".$session[user][acctid]."";
                        db_query($sql) or die(db_error(LINK));
                    }
                    
                    $sql = "INSERT INTO commentary (postdate,section,author,comment) VALUES (now(),'house-".$row[houseid]."',".$session[user][acctid].",'/me `\$nimmt `#$amt`\$ Edelsteine.')";
                    
                    db_query($sql) or die(db_error(LINK));
                    
                }
                
            }
        }
        else
        {
            output("`&Der Hausherr hat ein schweres, doppeltes Sicherheitsschloss an der Truhe angebracht, dass diese vor unerwünschtem Zugriff schützt.`nDa du keinen Schlüssel für dieses Schloss hast, sieht es wohl so aus als ob der Hausherr nicht will, dass du dich weiterhin an seinen Reichtümern vergreifst.`nDas tut mir aber leid...");
        }
        addnav("Zurück zum Haus","inside_houses.php");
    }
    
}
else if ($_GET[act]=="givegems")
{
    
    $sql = "SELECT gems,chestlock FROM keylist WHERE value1=$row[houseid] AND owner=".$session[user][acctid]."";
    $res = db_query($sql) or die(db_error(LINK));
    $row2 = db_fetch_assoc($res);
    if ($row2[chestlock]!=1)
    {
        
        if (!$_POST[gems])
        {
            
            output("`2<form action=\"inside_houses.php?act=givegems\" method='POST'>",true);
            
            output("`nWieviele Edelsteine deponieren? <input type='gems' name='gems'>`n`n",true);
            
            output("<input type='submit' class='button' value='Deponieren'>",true);
            
            addnav("","inside_houses.php?act=givegems");
            
        }
        else
        {
            
            $amt=abs((int)$_POST[gems]);
            
            // Anwesen, Gasthaus
            if (($row['status']==10) || ($row['status']==12) || ($row['status']==13) ||
            ($row['status']==17) || ($row['status']==18) || ($row['status']==19))
            {
                $gemmax=150;
            }
            // Villa
            else if (($row['status']==14) || ($row['status']==15) || ($row['status']==16))
            {
                $gemmax=300;
            }
            // Versteck, Refugium, Keller
            else if (($row['status']==30) || (($row['status']>=32) && ($row['status']<=39)))
            {
                $gemmax=20;
            }
            // Ausbau Stufe 1
            else if ($row[status]==5)
            {
                $gemmax=200;
            }
            // Ausbau Stufe 2
            else if (($row[status]==11) || ($row[status]==21) || ($row[status]==31) || ($row[status]==41) || ($row[status]==51) || ($row[status]==61) || ($row[status]==71) || ($row[status]==81) || ($row[status]==91) || ($row[status]==101))
            {
                $gemmax=500;
            }
            // Alles Andere
            else
            {
                $gemmax=$gemcost;
            }
            
            if ($amt>$session[user][gems])
            {
                output("`2So viele Edelsteine hast du nicht.");
            }
            else if ($row[gems]>=round($gemmax))
            {
                output("`2Der Schatz ist voll.");
            }
            else if ($amt>(round($gemmax)-$row[gems]))
            {
                output("`2Du gibst alles, aber du bekommst beim besten Willen nicht so viel in den Schatz.");
                
            }
            else if ($amt<0)
            {
                
                output("`2Wenn du etwas aus dem Schatz nehmen willst, versuche nicht, etwas negatives hineinzutun.");
                
            }
            else if($amt > 0) 
            {
                
                $row[gems]+=$amt;
                
                $session[user][gems]-=$amt;
                
                $sql = "UPDATE houses SET gems=$row[gems] WHERE houseid=$row[houseid]";
                
                db_query($sql);
                
                output("`2Du hast `#$amt`2 Edelsteine deponiert. Insgesamt befinden sich jetzt `#$row[gems]`2 Edelsteine im Haus.");
                
                $gemsspent=$row2[gems];
                $gemsspent+=$amt;
                
                if (db_num_rows($res))
                {
                    $sql = "UPDATE keylist SET gems=$gemsspent WHERE value1=$row[houseid] AND owner=".$session[user][acctid]."";
                    db_query($sql) or die(db_error(LINK));
                }
                
                $sql = "INSERT INTO commentary (postdate,section,author,comment) VALUES (now(),'house-".$row[houseid]."',".$session[user][acctid].",'/me `@deponiert `#$amt`@ Edelsteine.')";
                
                db_query($sql) or die(db_error(LINK));
                
                if ($amt>20)
                {
                    debuglog("Deponiert $amt Edelsteine in einem Haus.");
                }
                
                
                
            }
            
        }
        
    }
    else
    {
        output("`&Der Hausherr hat ein schweres, doppeltes Sicherheitsschloss an der Truhe angebracht, dass diese vor unerwünschtem Zugriff schützt.`nDa du keinen Schlüssel für dieses Schloss hast, sieht es wohl so aus als ob der Hausherr nicht will, dass du dich weiterhin an seinen Reichtümern vergreifst.`nDas tut mir aber leid...");
    }
    addnav("Zurück zum Haus","inside_houses.php");
    
}
else if ($_GET[act]=="upgrade")
{
    
    
    if ($session['user']['dragonkills'] < getsetting('houseextdks',10) )
    {
        
        output('`2Du würdest ja dein Haus gerne weiter ausbauen, doch entspricht dein derzeitiger Rang
wohl noch nicht den Anforderungen! Die Stadtverwaltung '.getsetting('townname','Atrahor').'s
rät dir, erst noch einige Drachen zu töten - so ungefähr '.(getsetting('houseextdks',10)-$session['user']['dragonkills']).' -
und dann noch einmal um einen Ausbau zu ersuchen!');
        
        addnav('Zurück zum Haus','inside_houses.php');
        
        page_footer();
        exit;
        
    }
    
    $sql = "SELECT * FROM houses WHERE owner=".$session[user][acctid]." ORDER BY houseid DESC";
    $result = db_query($sql) or die(db_error(LINK));
    $row = db_fetch_assoc($result);
    
    
    // Upgrade-Kosten
    if ($row['status']<10)
    {
        $upgold=300000;
        $upgems=200;
    }
    else
    {
        $upgold=500000;
        $upgems=500;
    }
    
    if (($row['status']==30) || ($row['status']==31))
    {
        $upgold=100000;
        $upgems=100;
    }
    if ($_GET[form]=="start")
    {
        
        
        
        
        
        output("`@Du kannst nun beginnen deinen Hausausbau zu finanzieren.`n`n");
        
        output("`0<form action=\"inside_houses.php?act=upgrade&form=build2\" method='POST'>",true);
        
        output("`nWieviel Gold anzahlen? <input type='gold' name='gold'>`n",true);
        
        output("`nWieviele Edelsteine? <input type='gems' name='gems'>`n",true);
        
        output("<input type='submit' class='button' value='Ausbauen'>",true);
        
        if ($row['status']==1)
        {
            $newstate=5;
        }
        else
        {
            $newstate=$row['status']+1;
        }
        
        $sql = "UPDATE houses SET status=$newstate WHERE houseid=$row[houseid]";
        
        db_query($sql);
        
        
        addnav("","inside_houses.php?act=upgrade&form=build2");
        
        addnav("Zurück zum Haus","inside_houses.php");
        
        addnav("Zurück zum Wohnviertel","houses.php?op=enter");
        
    }
    // END - gerade erst beginnen
    else if ($_GET[form]=="done")
    {
        
        $newstate=($_GET[choice]);
        $row['status']=$newstate;
        
        $sql = "UPDATE houses SET status=$newstate,gold=0,gems=0 WHERE houseid=$row[houseid]";
        db_query($sql) or die(db_error(LINK));
        
        addnews("`2".$session[user][name]."`3 hat die Arbeiten am Haus `2$row[housename]`3 fertiggestellt.");
        addhistory("`3Hat die Arbeiten am Haus `2$row[housename]`3 fertiggestellt.");
        
        output("`@Du hast dein Haus ausgebaut. Du wirst nun sicher viel mehr Freude an ihm haben!`n`n");
        
        // Message und Effekt
        
        switch ($row['status'])
        {
            
        case 10 :
            output("`&Dein neues Anwesen wird viel mehr an Reichtümern aufnehmen können als dein altes Haus.`n`7Du erhälst `@5`7 weitere Schlüssel!");
            
            // Schlüssel zählen (wichtig für Numererierung)
            $sql = "SELECT * FROM keylist WHERE value1=".$session['user']['house']." ORDER BY id ASC";
            $result = db_query($sql) or die(db_error(LINK));
            $nummer=db_num_rows($result)+1;
            if (db_num_rows($result))
            {
                db_free_result($result);
            }
            
            // 5 Stück auffüllen
            for ($i=$nummer; $i<$nummer+5; $i++)
            {
                $sql = "INSERT INTO keylist (owner,value1,value2,gold,gems,description) VALUES (".$session[user][acctid].",$row[houseid],$i,0,0,'Schlüssel für Haus Nummer $row[houseid]')";
                db_query($sql);
            }
            break;
            
        case 14 :
            output("`&Deine neue Villa wird noch mehr Reichtümer horten können als das Anwesen.`n`7Du erhälst `@3`7 weitere Schlüssel!");
            
            // Schlüssel zählen (wichtig für Numererierung)
            $sql = "SELECT * FROM keylist WHERE value1=".$session['user']['house']." ORDER BY id ASC";
            $result = db_query($sql) or die(db_error(LINK));
            $nummer=db_num_rows($result)+1;
            if (db_num_rows($result))
            {
                db_free_result($result);
            }
            
            // 3 Stück auffüllen
            for ($i=$nummer; $i<$nummer+3; $i++)
            {
                $sql = "INSERT INTO keylist (owner,value1,value2,gold,gems,description) VALUES (".$session[user][acctid].",$row[houseid],$i,0,0,'Schlüssel für Haus Nummer $row[houseid]')";
                db_query($sql);
            }
            break;
            
        case 17 :
            output("`&Dein neues Gasthaus wird dir und deinen Mitbewohnern eine willkommene Möglichkeit zur Rast und Stärkung bieten.`n`7Du erhälst `@3`7 weitere Schlüssel!");
            
            // Schlüssel zählen (wichtig für Numererierung)
            $sql = "SELECT * FROM keylist WHERE value1=".$session['user']['house']." ORDER BY id ASC";
            $result = db_query($sql) or die(db_error(LINK));
            $nummer=db_num_rows($result)+1;
            if (db_num_rows($result))
            {
                db_free_result($result);
            }
            
            // 3 Stück auffüllen
            for ($i=$nummer; $i<$nummer+3; $i++)
            {
                $sql = "INSERT INTO keylist (owner,value1,value2,gold,gems,description) VALUES (".$session[user][acctid].",$row[houseid],$i,0,0,'Schlüssel für Haus Nummer $row[houseid]')";
                db_query($sql);
            }
            break;
            
        case 20 :
            output("`&Deine neue Festung wird ein sehr sicherer Ort für alle werden, die darin schlafen.`n");
            break;
            
        case 24 :
            output("`&Dein neuer Turm wird durch seine Höhe noch sicherer für dich und deine Mitbewohner sein und durch seine Nähe zu den Sternen der perfekte Ort für magische Praktiken.");
            break;
            
        case 27 :
            output("`&Deine neue Burg ist der Inbegriff von Sicherheit und Schutz. Es gibt praktisch keinen Ort, an dem du und deine Gäste unbesorgter und besser schlafen können.");
            break;
            
        case 30 :
            output("`&Dein neues Versteck wird jenen Unterschlupf bieten, die es sich mit Allem und Jedem verscherzt haben und nirgendwo mehr sicher sind. Der Hausschatz wird nur minimal, die Nacht nicht sehr erholsam sein, du hast nur noch 5 Schlüssel!`n");
            
            // Schlüssel zählen
            $sql = "SELECT * FROM keylist WHERE value1=".$session['user']['house']." ORDER BY id DESC";
            $result = db_query($sql) or die(db_error(LINK));
            
            // Von hinten beginnend alle Schlüssel bis auf 5 löschen
            $nummer=(db_num_rows($result)-5);
            for ($i=0; $i<$nummer; $i++)
            {
                $h = db_fetch_assoc($result);
                $sql="DELETE FROM keylist WHERE id=".$h['id'];
                db_query($sql);
            }
            break;
            
        case 34 :
            output("`&Dein neues Refugium nutzt seinen Keller um die Versteckmöglichkeiten seiner Gäste größer und komfortabler zu gestalten. Du und deine Mitbewohner können dort nun ohne Beeinträchtigung nächtigen.`n");
            break;
            
        case 37 :
            output("`&Dein neues Kellergewölbe ist so gestaltet, dass es einen Bewohner mehr aufnehmen kann, auch der Komfort wurde ein klein wenig gehoben. Dennoch ist es immer noch nicht die schönste Art zu übernachten.`n`7Du erhälst `@zwei`7 weitere Schlüssel!`n`&");
            
            // Schlüssel zählen (wichtig für Numererierung)
            $sql = "SELECT * FROM keylist WHERE value1=".$session['user']['house']." ORDER BY id ASC";
            $result = db_query($sql) or die(db_error(LINK));
            $nummer=db_num_rows($result)+1;
            if (db_num_rows($result))
            {
                db_free_result($result);
            }
            
            // 2 Stück auffüllen
            for ($i=$nummer; $i<$nummer+2; $i++)
            {
                $sql = "INSERT INTO keylist (owner,value1,value2,gold,gems,description) VALUES (".$session[user][acctid].",$row[houseid],$i,0,0,'Schlüssel für Haus Nummer $row[houseid]')";
                db_query($sql);
            }
            break;
            
        case 40 :
            output("`&Dein neues Gildenhaus wird seinen Bewohnern neue Anwendungen in ihren Spezialfähigkeiten gewähren, wenn diese aufgebraucht sind.`n");
            break;
            
        case 43 :
            output("`&Dein neues Zunfthaus wird künftig erfahrene Abenteurer anlocken, von deren Erfahrung alle profitieren können.`n");
            
        case 47 :
            output("`&Dein neues Handelshaus wird der zentrale Punkt des Handelns werden.`nGeschäftsmänner werden von weither kommen.`n");
            break;
            
        case 50 :
            output("Dein neuer Bauernhof wird beliebig oft die Tiere seiner Gäste versorgen können.`n");
            break;
            
        case 54 :
            output("`&Deine neue Tierfarm wird nicht nur die Versorgung der Tiere gewährleisten, sondern auch ihre Ausbildung.`n");
            break;
            
        case 57 :
            output("`&Dein neuer Gutshof wird die Arbeit derer, die ihn bewohnen, reichlich vergolden und bietet somit eine gute Einnahmequelle bei finanziellen Engpässen.`n");
            break;
            
        case 60 :
            output("`&Deine neue Gruft wird den dunklen Göttern sicherlich gut gefallen.`n");
            break;
            
        case 64 :
            output("`&Deine neue Krypta ermöglicht es dir und deinen Gästen mit kürzlich Verstorbenen in Kontakt zu treten und sie bei ihrer Suche nach Wiedererweckung zu unterstützen.`n");
            break;
            
        case 67 :
            output("`&Deine neuen Katakomben bergen dürstere Geheimnisse und sind mit Blut und Frevel befleckt.`n");
            break;
            
        case 70 :
            output("`&Dein neuer Kerker wird dir und deinen Mitbewohnern eine hohe Verantwortung über die Gefangenen übertragen.`n");
            break;
            
        case 74 :
            output("`&Dein neues Gefängnis lässt dich und deine Mitbewohner ein wenig mehr Kontrolle über die Haftdauer der Insassen ausüben, wenn auch zu einem gewissen Preis.`n");
            break;
            
        case 77 :
            output("`&Dein neues Verlies bietet dir und deinen Mitbewohnern eine weitere grausige Möglichkeit die Gefangenen zu disziplinieren.`n");
            break;
            
        case 80 :
            output("`&Dein neues Kloster wird seinen Bewohnern und allen Gästen stets Hilfe und Heilung bieten.`n");
            break;
            
        case 84 :
            output("`&Deine neue Abtei wird den Göttern sicherlich gut gefallen. Sei dir ihres Segens, bei eintsprechend hoher Opfergabe, gewiss.`n");
            break;
            
        case 87 :
            output("`&Dein neuer Ritterorden zieht Recken aus dem ganzen Land an, die Helden suchen, um sie auf ihren Abenteuern zu begleiten.`n");
            break;
            
        case 90 :
            output("`&Dein neues Trainingslager wird dir und deinen Mitbewohnern eine gute Kampfausbildung ermöglichen.`n");
            break;
            
        case 94 :
            output("`&Deine neue Kaserne wird dir und deinen Mitbewohnern die Möglichkeit geben durch harten und schmerzhaften Drill an Kampfeskraft zu gewinnen.`n");
            break;
            
        case 97 :
            output("`&Dein neues Söldnerlager zieht Schurken aller Art, darunter auch begabte Schmiede, an.`nDiese werden für geringes Entgelt deine Ausrüstung verbessern.`n");
            break;
            
        case 100 :
            output("`&Dein neues Bordell wird dir und deinen Gästen sicherlich sehr viel Freude bereiten.`n");
            break;
            
        case 104 :
            output("`&Dein neuer Rotlichtpalast bietet dir eine weitere Möglichkeit deine Stimmung zu verbessern.`n");
            break;
            
        case 107 :
            output("`&Die Spelunke hat neben der Möglichkeit des Amüsierens für dich auch noch einige schlagkräftige Argumente für deine Feinde übrig.`n");
            break;
        }
        
        addnav("Zurück zum Haus","inside_houses.php");
        addnav("Zurück zum Wohnviertel","houses.php");
    }
    // end fertig mit bauen
    
    else if ($_GET[form]=="build2")
    {
        
        //Gold
        $paidgold=(int)$_POST['gold'];
        $paidgems=(int)$_POST['gems'];
        
        if ($session[user][gold]<$paidgold || $session[user][gems]<$paidgems)
        {
            output("`@Du hast nicht genug dabei!");
            addnav("Zurück zum Haus","inside_houses.php");
            addnav("Zurück zum Wohnviertel","houses.php");
        }
        else if ($session[user][turns]<1)
        {
            output("`@Du bist zu müde, um heute noch an deinem Haus zu arbeiten!");
            addnav("Zurück zum Haus","inside_houses.php");
            addnav("Zurück zum Wohnviertel","houses.php");
        }
        else if ($paidgold<0 || $paidgems<0)
        {
            output("`@Versuch hier besser nicht zu beschummeln.");
            addnav("Zurück zum Haus","inside_houses.php");
            addnav("Zurück zum Wohnviertel","houses.php");
        }
        else
        {
            output("`@Du baust für `^$paidgold`@ Gold und `#$paidgems`@ Edelsteine an deinem Haus...`n");
            $row[gold]+=$paidgold;
            $session[user][gold]-=$paidgold;
            output("`nDu verlierst einen Waldkampf.");
            $session[user][turns]--;
            
            if ($row[gold]>$upgold)
            {
                output("`nDu hast die kompletten Goldkosten bezahlt und bekommst das überschüssige Gold zurück.");
                $session[user][gold]+=$row[gold]-$upgold;
                $row[gold]=$upgold;
            }
            
            $sql = "UPDATE houses SET gold=$row[gold] WHERE houseid=$row[houseid]";
            db_query($sql) or die(db_error(LINK));
            
            //Edelsteine
            $row[gems]+=$paidgems;
            $session[user][gems]-=$paidgems;
            
            if ($row[gems]>$upgems)
            {
                output("`nDu hast die kompletten Edelsteinkosten bezahlt und bekommst überschüssige Edelsteine zurück.");
                $session[user][gems]+=$row[gems]-$upgems;
                $row[gems]=$upgems;
            }
            
            if (($row[gems]<$upgems) or($row[gold]<$upgold))
            {
                addnav("Zurück zum Haus","inside_houses.php");
                addnav("Zurück zum Wohnviertel","houses.php");
            }
            
            $goldtopay=$upgold-$row[gold];
            $gemstopay=$upgems-$row[gems];
            
            $done=round(100-((100*$goldtopay/$upgold)+(100*$gemstopay/$upgems))/2);
            
            output("`nDein Ausbau ist damit zu `$$done%`@ fertig. Du musst noch `^$goldtopay`@ Gold und `#$gemstopay `@Edelsteine bezahlen, bis du fertig bist.");
            
            $sql = "UPDATE houses SET gems=$row[gems] WHERE houseid=$row[houseid]";
            db_query($sql) or die(db_error(LINK));
            
            //fertig
            if ($row[gems]>=$upgems && $row[gold]>=$upgold)
            {
                output("`n`n`bGlückwunsch!`b Dein Ausbau ist fertig. Was soll aus deinem schönen Haus werden?`n`n");
                
                if ($row['status']>=10)
                {
                    output("`^Du erweiterst dein Haus um die 2. Ausbaustufe.`nDir stehen 2 Möglichkeiten zu Wahl. Egal wofür du dich entscheidest, dein Haus wird keine seiner Möglichkeiten verlieren und in jedem Fall verbessert werden.`&`n`n");
                }
                switch ($row['status'])
                {
                    
                case 5 :
                    // Beschreibungen 1. Stufe
                    output("`7Ein `%Anwesen`7 würde sehr viel mehr an Reichtümern horten können als ein gewöhnliches Haus.`n`n");
                    output("`7Eine `QFestung`7 bietet zusätzlichen Schutz gegen Angriffe.`n`n");
                    output("`7Ein `tVersteck`7 ist kaum ein Ort zum bequemen wohnen. Wer sich hier verkriecht ist von niemandem aufzuspüren. Dafür gibt es allerding kaum Lagermöglichkeiten für Gold und Edelsteine.`n`^Ein Versteck kann höchstens 5 Zimmer haben. Alle Schlüssel bis auf 5 werden verloren gehen, solange der Ausbau besteht!`n`n");
                    output("`7Ein `5Gildenhaus`7 würde die Möglichkeit bieten zusätzlich Anwendungen im Spezialgebiet zu erhalten, wenn diese aufgebraucht sind.`n`n");
                    output("`7Ein `tBauernhof`7 ist ein Ort an dem sich Tiere besonders wohl fühlen und neue Kraft schöpfen können.`n`n");
                    output("`7Eine `TGruft`7 ist eine dunkle und finstre Unterkunft für dunkle und finstre Kreaturen. Hier kann man u.A. dem Blutgott huldigen. `n`n");
                    output("`7Ein `qKerker`7 hält üble Schurken und Verbrecher gefangen und erteilt ihnen ihre gerechte Strafe. `n`n");
                    output("`7Ein `&Kloster`7 ist ein Ort der Heilung und der Frömmigkeit. Hier wird selbstlos jeder armen Seele geholfen. `n`n");
                    output("`7Ein `vTrainingslager`7 beherbergt junge wie alte Krieger. Von den Veteranen kann man sehr viel lernen!`n`n");
                    output("Ein `4Bordell`7 ist ein Ort der Freude und der Lust. Nach einem Besuch ist so mancher Krieger erfolgreicher im Kampf. `n`n");
                    
                    addnav("Ein Anwesen!","inside_houses.php?act=upgrade&form=done&choice=10");
                    addnav("Eine Festung!","inside_houses.php?act=upgrade&form=done&choice=20");
                    addnav("Ein Versteck!","inside_houses.php?act=upgrade&form=done&choice=30");
                    addnav("Ein Gildenhaus!","inside_houses.php?act=upgrade&form=done&choice=40");
                    addnav("Ein Bauernhof!","inside_houses.php?act=upgrade&form=done&choice=50");
                    addnav("Eine Gruft!","inside_houses.php?act=upgrade&form=done&choice=60");
                    addnav("Ein Kerker!","inside_houses.php?act=upgrade&form=done&choice=70");
                    addnav("Ein Kloster!","inside_houses.php?act=upgrade&form=done&choice=80");
                    addnav("Ein Trainingslager!","inside_houses.php?act=upgrade&form=done&choice=90");
                    addnav("Ein Bordell!","inside_houses.php?act=upgrade&form=done&choice=100");
                    addnav("Muss ich mir noch überlegen","inside_houses.php");
                    break;
                    
                    // 2. Stufe
                case 11 :
                    output("Eine `%Villa`7 würde noch viel mehr an Reichtümern horten können als ein Anwesen.`n`n");
                    output("Ein `%Gasthaus`7 würde etwas mehr an Reichtümern horten können und zusätzlich die Möglichkeit der Stärkung bei einer guten Suppe bieten.`n`n");
                    addnav("Eine Villa!","inside_houses.php?act=upgrade&form=done&choice=14");
                    addnav("Ein Gasthaus!","inside_houses.php?act=upgrade&form=done&choice=17");
                    addnav("Muss ich mir noch überlegen","inside_houses.php");
                    break;
                    
                case 21 :
                    output("`7Ein `QMagierturm`7 bietet weiteren Schutz gegen Angriffe und ermöglicht ein Ritual zur Stärkung der mystischen Kräfte.`n`n");
                    output("`7Eine `QBurg`7 bietet extremen Schutz gegen Angriffe.`n`n");
                    addnav("Ein Turm!","inside_houses.php?act=upgrade&form=done&choice=24");
                    addnav("Eine Burg!","inside_houses.php?act=upgrade&form=done&choice=27");
                    addnav("Muss ich mir noch überlegen","inside_houses.php");
                    break;
                    
                case 31 :
                    output("`7Ein `tRefugium`7 verliert den Nachteil des schlechten Schlafes und bietet weiterhin Unangreifbarkeit.`n`n");
                    output("`7Ein `tKellergewölbe`7 mindert den Nachteil des schlechten Schlafes und bietet zusätzlich 2 weitere Schlüssel.`n`n");
                    addnav("Ein Refugium!","inside_houses.php?act=upgrade&form=done&choice=34");
                    addnav("Ein Kellergewölbe!","inside_houses.php?act=upgrade&form=done&choice=37");
                    addnav("Muss ich mir noch überlegen","inside_houses.php");
                    break;
                    
                case 41 :
                    output("`7Ein `5Zunfthaus`7 würde eine leichtere Möglichkeit bieten öfter ins Schloss zu können.`n`n");
                    output("`7Ein `5Handelshaus`7 ermöglicht dir den Kauf und Verkauf von Edelsteinen bei einem Händler von weit her.`n`n");
                    addnav("Ein Zunfthaus!","inside_houses.php?act=upgrade&form=done&choice=44");
                    addnav("Ein Handelshaus!","inside_houses.php?act=upgrade&form=done&choice=47");
                    addnav("Muss ich mir noch überlegen","inside_houses.php");
                    break;
                    
                case 51 :
                    output("`7Eine `tTierfarm`7 ermöglicht das fachgerechte Training von Tieren.`n`n");
                    output("`7Ein `tGutshof`7 ist ein Ort an dem sich schnell durch Arbeit Gold verdienen lässt.`n`n");
                    addnav("Eine Tierfarm!","inside_houses.php?act=upgrade&form=done&choice=54");
                    addnav("Ein Gutshof!","inside_houses.php?act=upgrade&form=done&choice=57");
                    addnav("Muss ich mir noch überlegen","inside_houses.php");
                    break;
                    
                case 61 :
                    output("`7Eine `TKrypta`7 ermöglicht es bei Ramius ein gutes Wort für Verstorbene einzulegen. `n`n");
                    output("`TKatakomben`7 beherbergen einen rituellen Opferschrein mit dem es möglich ist sich selbst ins Reich der Toten zu befördern. `n`n");
                    addnav("Eine Krypta!","inside_houses.php?act=upgrade&form=done&choice=64");
                    addnav("Katakomben!","inside_houses.php?act=upgrade&form=done&choice=67");
                    addnav("Muss ich mir noch überlegen","inside_houses.php");
                    break;
                    
                case 71 :
                    output("`7Ein `qGefängnis`7 macht es möglich Insassen zu befreien, allerdings zu einem hohen Preis.`n`n");
                    output("`7Ein `qVerlies`7 ermöglicht es Gefangene in brutalen Käfigkämpfen gegen Bestien antreten zu lassen und mit einer guten Wette noch etwas Gold zu verdienen. `n`n");
                    addnav("Ein Gefängnis!","inside_houses.php?act=upgrade&form=done&choice=74");
                    addnav("Ein Verlies!","inside_houses.php?act=upgrade&form=done&choice=77");
                    addnav("Muss ich mir noch überlegen","inside_houses.php");
                    break;
                    
                case 81 :
                    output("`7Eine `&Abtei`7 ist ein Ort des Segens. Bei ausreichend Spende und Gebet wird dieser Segen jedem gewährt. `n`n");
                    output("`7Ein `&Ritterorden`7 ermöglicht es einen jungen Knappen als treuen Wegbegleiter zu erhalten. `n`n");
                    addnav("Eine Abtei!","inside_houses.php?act=upgrade&form=done&choice=84");
                    addnav("Ein Ritterorden!","inside_houses.php?act=upgrade&form=done&choice=87");
                    addnav("Muss ich mir noch überlegen","inside_houses.php");
                    break;
                    
                case 91 :
                    output("`7In einer `vKaserne`7 lassen sich mit schweißtreibendem Training Angriff und Verteidigung verbessern!`n`n");
                    output("`7Im `vSöldnerlager`7 werten geschickte Schmiede Waffen und Rüstungen auf!`n`n");
                    addnav("Eine Kaserne!","inside_houses.php?act=upgrade&form=done&choice=94");
                    addnav("Ein Söldnerlager!","inside_houses.php?act=upgrade&form=done&choice=97");
                    addnav("Muss ich mir noch überlegen","inside_houses.php");
                    break;
                    
                case 101 :
                    output("Im `4Rotlichtpalast`7 lassen sich wilde, stimmungserheiternde Orgien feiern. `n`n");
                    output("Eine `4Spelunke`7 zieht Gauner, Ganoven und Schläger an, die sich gern für ein kleines Entgeld beauftragen lassen. `n`n");
                    addnav("Ein Rotlichtpalast!","inside_houses.php?act=upgrade&form=done&choice=104");
                    addnav("Eine Spelunke!","inside_houses.php?act=upgrade&form=done&choice=107");
                    addnav("Muss ich mir noch überlegen","inside_houses.php");
                    break;
                }
                
            }
            // END Einzahlung ok, Ausbaukosten komplett
            
            
            
        }
        // END - Einzahlung ok
        
    }
    // end get[form]=build2
    
    else
    {
        if ($session[user][turns]<1)
        {
            output("`@Du bist zu erschöpft, um heute noch irgendetwas zu bauen. Warte bis morgen.");
            addnav("Zurück zum Haus","inside_houses.php");
            addnav("Zurück zum Wohnviertel","houses.php");
        }
        
        // im Ausbau ?
        else if (($row[status]==5) || ($row[status]==11) || ($row[status]==21) || ($row[status]==31) ||
        ($row[status]==41) || ($row[status]==51) || ($row[status]==61) || ($row[status]==71) ||
        ($row[status]==81) || ($row[status]==91) || ($row[status]==101))
        {
            
            if ($row[gems]>=$upgems && $row[gold]>=$upgold)
            {
                // Kosten bezahlt ?
                redirect("inside_houses.php?act=upgrade&form=build2");
            }
            
            output("`@Du schaust wie weit du mit dem Ausbau bereits bist.`n`n");
            $goldtopay=$upgold-$row[gold];
            $gemstopay=$upgems-$row[gems];
            
            $done=round(100-((100*$goldtopay/$upgold)+(100*$gemstopay/$upgems))/2);
            
            output("`nEs ist zu `$$done%`@ fertig. Du musst noch `^$goldtopay`@ Gold und `#$gemstopay `@Edelsteine bezahlen.`nWillst du jetzt weiter bauen?`n`n");
            
            output("`0<form action=\"inside_houses.php?act=upgrade&form=build2\" method='POST'>",true);
            output("`nWieviel Gold zahlen? <input type='gold' name='gold'>`n",true);
            output("`nWieviele Edelsteine? <input type='gems' name='gems'>`n",true);
            output("<input type='submit' class='button' value='Bauen'>",true);
            addnav("","inside_houses.php?act=upgrade&form=build2");
            addnav("Zurück zum Haus","inside_houses.php");
            addnav("Zurück zum Wohnviertel","houses.php");
            
        }
        // End - Ausbau bereits begonnen
        
        else
        {
            
            output("`@Du überlegst ob du aus deinem schnöden normalen Wohnhaus nicht etwas Größeres, Schöneres machen könntest.");
            
            output(" Ein Ausbau würde dich `^$upgold Gold`@ und `#$upgems Edelsteine`@ kosten. Wie beim Hausbau kannst du es ansparen.`n");
            
            output(" `4Während des Ausbaus kann niemand aus dem Haus Gold oder Edelsteine aus der Truhe nehmen!`@");
            
            output(" Ein gestartetes Ausbauvorhaben kann nicht abgebrochen werden.`n`nWillst du mit dem Hausausbau beginnen?");
            
            addnav(" Ausbau beginnen","inside_houses.php?act=upgrade&form=start");
            
            addnav("Zurück zum Haus","inside_houses.php");
            
            addnav("Zurück zum Wohnviertel","houses.php");
            
        }
        
    }
    
    
    
    //Hausausbau Ende
    
    
    
    // Ausbau entfernen
    
}
else if ($_GET[act]=="removeupg")
{
    
    output("`#Die Kosten, die durch das Entfernen des Ausbaus entstehen werden gerade durch den Wert der Baumaterialien gedeck.`n");
    
    output("`@Du wirst also NICHTS von deinem investierten Gold oder von den Edelsteinen zurückerhalten.`n");
    
    if ($row[status]==7)
    {
        output("`7Alles Gold und alle Edelsteine, die danach zuviel in deiner Truhe sind gehen `4UNWIEDERBRINGLICH VERLOREN!`7`n");
    }
    
    output("Dein Haus wird wieder ein gewöhnliches Wohnhaus sein. Zusätzliche Räume der Villa werden entfernt, durch einen Umbau zum Versteck, Refugium oder Kellergewölbe verloren gegangene Räume werden wieder hergestellt. Bist du sicher, dass du das willst ??");
    
    addnav("Ja, weg damit!","inside_houses.php?act=rip");
    
    addnav("NEIN! Zurück zum Haus","inside_houses.php");
    
    
    
}
else if ($_GET[act]=="rip")
{
    
    
    
    if ($row[gold]>round($goldcost/2))
    {
        $row[gold]=round($goldcost/2);
    }
    
    if ($row[gems]>$gemcost)
    {
        $row[gems]=$gemcost;
    }
    
    $sql = "SELECT status,houseid,owner FROM houses WHERE owner=".$session[user][acctid]."";
    $res = db_query($sql) or die(db_error(LINK));
    $house = db_fetch_assoc($res);
    
    // Privatgemächer zurücksetzen
    // (außer Einladungen in Privatraum des Hausherrn)
    item_delete(' (tpl_id="prive" OR tpl_id="privb") AND value1='.$house['houseid'].' AND value2!='.$house['owner'].'' );
    
    // Anwesen - Schlüssel löschen
    if ($house[status]==10)
    {
        $sql = "SELECT id FROM keylist WHERE value1=".$session[user][house]." ORDER BY id DESC LIMIT 0,5";
        $result = db_query($sql) or die(db_error(LINK));
        
        while ($h=db_fetch_assoc($result))
        {
            $sql="DELETE FROM keylist WHERE id=".$h['id'];
            db_query($sql);
        }
    }
    
    // Villa, Gasthaus - Schlüssel löschen
    else if (($house[status]==14) || ($house[status]==17))
    {
        $sql = "SELECT id FROM keylist WHERE value1=".$session[user][house]." ORDER BY id DESC LIMIT 0,8";
        $result = db_query($sql) or die(db_error(LINK));
        
        while ($h=db_fetch_assoc($result))
        {
            $sql="DELETE FROM keylist WHERE id=".$h['id'];
            db_query($sql);
        }
    }
    
    // Versteck, Refugium, Kellergewölbe - Schlüssel zurück
    else if (($house[status]>29) && ($house[status]<40))
    {
        $sql = "SELECT * FROM keylist WHERE value1=".$session[user][house]." ORDER BY id ASC";
        $result = db_query($sql) or die(db_error(LINK));
        $nummer=db_num_rows($result)+1;
        if (db_num_rows($result))
        {
            db_free_result($result);
        }
        
        for ($i=$nummer; $i<10; $i++)
        {
            
            $sql = "INSERT INTO keylist (owner,value1,value2,gold,gems,description) VALUES (".$session[user][acctid].",$row[houseid],$i,0,0,'Schlüssel für Haus Nummer $row[houseid]')";
            db_query($sql);
        }
    }
    
    $sql = "UPDATE houses SET status=1,gems=$row[gems],gold=$row[gold] WHERE houseid=$row[houseid]";
    
    db_query($sql);
    
    redirect("inside_houses.php");
    
}
else if ($_GET[act]=="rename")
{
    
    if (!$_POST[housename])
    {
        
        output("`2Das Haus umbenennen kostet `^1000`2 Gold und `#1`2 Edelstein.`n`n");
        
        output("`0<form action=\"inside_houses.php?act=rename\" method='POST'>",true);
        
        output("`nGebe einen neuen Namen für dein Haus ein: <input name='housename' maxlength='40'>`n",true);
        
        output("<input type='submit' class='button' value='Umbenennen'>",true);
        
        addnav("","inside_houses.php?act=rename");
        
    }
    else
    {
        
        if ($session[user][gold]<1000 || $session[user][gems]<1)
        {
            
            output("`2Das kannst du nicht bezahlen.");
            
        }
        else
        {
            
            $fixed = preg_replace("/[`][bc]/","",$_POST[housename]);
            output("`2Dein Haus `@$row[housename]`2 heißt jetzt `@".stripslashes($fixed)."`2.");
            
            $sql = "UPDATE houses SET housename='".$fixed."`0' WHERE houseid=$row[houseid]";
            
            db_query($sql);
            
            $session[user][gold]-=1000;
            
            $session[user][gems]-=1;
            
        }
        
    }
    
    addnav("Zurück zum Haus","inside_houses.php");
    
}
else if ($_GET[act]=="desc")
{
    
    if (!$_POST[desc])
    {
        
        output("`2Hier kannst du die Beschreibung für dein Haus ändern.`n`nDie aktuelle Beschreibung lautet:`0$row[description]`0`n");
        
        output("`0<form action=\"inside_houses.php?act=desc\" method='POST'>",true);
        
        output("`n`2Gebe eine Beschreibung für dein Haus ein:`n<input name='desc' maxlength='500' value='",true);
        rawoutput($row[description]);
        output("' size='50'>`n",true);
        
        output("<input type='submit' class='button' value='Abschicken'>",true);
        
        addnav("","inside_houses.php?act=desc");
        
    }
    else
    {
        
        $fixed = preg_replace("/[`][bc]/","",$_POST[desc]);
        output("`2Die Beschreibung wurde geändert.`n`0".stripslashes($fixed)."`2.");
        $sql = "UPDATE houses SET description='".$fixed."`0' WHERE houseid=$row[houseid]";
        
        db_query($sql);
        
    }
    
    addnav("Zurück zum Haus","inside_houses.php");
    
}
else if ($_GET[act]=="logout")
{
    
    if ($session[user][housekey]!=$session[housekey])
    {
        
        $sql = "UPDATE keylist SET hvalue=".$session[housekey]." WHERE value1=".(int)$session[housekey]." AND owner=".$session[user][acctid]."";
        
        db_query($sql) or die(sql_error($sql));
        
    }
    	                        
    $sql = "UPDATE account_extra_info SET hadnewday=1 WHERE acctid = ".$session['user']['acctid'];
    db_query($sql) or die(sql_error($sql));
        
    redirect('login.php?op=logout&loc='.USER_LOC_HOUSE.'&restatloc='.$row['houseid']);    
    
    
}
else
{
    
    $show_invent = true;
    
    output("`2`b`c$row[housename]`c`b");
    output("`2`c( ".get_house_state($row[status],false)."`2)`0`c`n");
    
    if ($row[description])
    {
        output("`0`c$row[description]`c`n");
    }
    
    output("`2Du und deine Mitbewohner haben `^$row[gold]`2 Gold und `#$row[gems]`2 Edelsteine im Haus gelagert.`n");
    
    if (getsetting('activategamedate','0')==1)
    {
        output("Wir schreiben den `^".getgamedate()."`2.`n");
    }
    
    output("Es ist jetzt `^".getgametime()."`2 Uhr.`n`n");
    
    viewcommentary("house-".$row[houseid],"Mit Mitbewohnern reden:",30,"sagt",false,true,false,false,true);
    
    output("`n`n`n<table border='0'><tr><td>`2`bDie Schlüssel:`b `0</td><td>`2`bExtra Ausstattung`b</td></tr><tr><td valign='top'>",true);
    
    $sql = 'SELECT 	keylist.*,
					accounts.acctid AS aid,accounts.name AS besitzer, accounts.restatlocation
			FROM keylist 
			LEFT JOIN accounts ON accounts.acctid=keylist.owner 
			WHERE value1='.$row['houseid'].' ORDER BY id ASC';
    
    $result = db_query($sql) or die(db_error(LINK));
    
	$int_keycount = db_num_rows($result);
	
    for ($i=1; $i<=$int_keycount; $i++)
    {
        
        $item = db_fetch_assoc($result);
        
        if ($item[besitzer]=="")
        {
            
            output("`n`2".$i.": `4`iVerloren`i`0");
            
        }
        else
        {
            
            output("`n`2".$i.": `&$item[besitzer]`0");
            
        }
        
        if ($item[aid]==$row[owner])
        {
            output(" (der Eigentümer) ");
        }
        
        if ($item['restatlocation'] == $row['houseid'] && $item['owner']>0)
        {
            output(" `ischläft hier`i");
        }
        
        
    }
    
    if (db_num_rows(db_query('SELECT acctid FROM accounts WHERE acctid='.$row['owner'].' AND restatlocation='.$row['houseid'].' AND location='.USER_LOC_HOUSE))>0) {
		output("`nDer Eigentümer schläft hier");
	}
    
    output("</td><td valign='top'>",true);
    
    // Möbel mit besonderen Funktionen
    $properties = ' deposit>0 AND deposit1='.$session['housekey'].' AND deposit2=0 ';
    $extra = ' ORDER BY name DESC, id ASC';
    
    $res = item_list_get($properties , $extra , true , ' name,description,id,furniture_hook ' );
    
    $count = db_num_rows($res);
    
    $hooks = '';
    
    for($i=1; $i<=$count; $i++)
    {
        
        $item = db_fetch_assoc($res);
        output("`n`&$item[name]`0 (`i$item[description]`i)");
        
        if ($item['furniture_hook'] != '' && !$hooks[$item['furniture_hook']])
        {
            $hooks[$item['furniture_hook']] = true;
            addnav($item['name'],'furniture.php?item_id='.$item['id']);
        }
        
    }
    
    output("</td></tr></table>",true);
    
    addnav("Gold");
    
    addnav("Deponieren","inside_houses.php?act=givegold");
    
    addnav("Mitnehmen","inside_houses.php?act=takegold");
    
    addnav("Edelsteine");
    
    addnav("Deponieren","inside_houses.php?act=givegems");
    
    addnav("Mitnehmen","inside_houses.php?act=takegems");
    
    if ($session[user][house]==$session[housekey])
    {
        
        addnav("Schlüssel");
        
        addnav("Vergeben","inside_houses.php?act=givekey");
        
        addnav("n?Zurücknehmen","inside_houses.php?act=takekey");
        
    }
    
    ///////////////////////
    // Privatraumerweiterung (NEU)
    $sql = 'SELECT tpl_id,id,a.name AS playername,i.value2 AS private_owner,i.name FROM items i
LEFT JOIN accounts a ON a.acctid=i.value2
WHERE (i.tpl_id="prive" OR i.tpl_id="privb") AND i.value1='.$session['housekey'].' AND i.owner='.$session['user']['acctid'];
    $res = db_query($sql);
    
    $own_room = ($session['user']['house']==$session['housekey']) ? true : false;
    
    if (db_num_rows($res) > 0 || $own_room)
    {
        addnav("Privates");
        
        while ($p = db_fetch_assoc($res))
        {
            
            if ($p['tpl_id'] == 'privb')
            {
                // eigener Raum
                $own_room = true;
            }
            else
            {
                addnav('Privatgemach von '.$p['playername'],'houses_private.php?private='.$p['private_owner']);
            }
            
        }
        
        if ($own_room)
        {
            addnav('Eigenes Gemach','houses_private.php?private='.$session['user']['acctid']);
        }
    }
    
    if ($session['user']['house']==$session['housekey'] && $row['status'] >= 10)
    {
        
        addnav('Privatgemächer vergeben','houses_private.php?op=raum_geben&private='.$session['user']['acctid']);
        addnav('Privatgemächer abnehmen','houses_private.php?op=raum_nehmen&private='.$session['user']['acctid']);
        
    }
    //	}
    
    // ENDE Privatraumerweiterung
    /////////////////////
    
    
    if ($session[user][house]==$session[housekey])
    {
        
        addnav("Sonstiges");
        
        if ($row[status]<=5)
        {
            addnav("Haus ausbauen","inside_houses.php?act=upgrade");
        }
        
        if ((($row[status]==10) || ($row[status]==11) || ($row[status]==20) || ($row[status]==21) || ($row[status]==30) || ($row[status]==31) || ($row[status]==40) || ($row[status]==41) || ($row[status]==50) || ($row[status]==51) || ($row[status]==60) || ($row[status]==61) || ($row[status]==70) || ($row[status]==71) || ($row[status]==80) || ($row[status]==81) || ($row[status]==90) || ($row[status]==91) || ($row[status]==100) || ($row[status]==101)))
        {
            addnav("Haus weiter ausbauen","inside_houses.php?act=upgrade");
        }
        
        if ($row[status]>5)
        {
            addnav("Ausbau entfernen","inside_houses.php?act=removeupg");
        }
        
        addnav("Haus umbenennen","inside_houses.php?act=rename");
        
        addnav("Beschreibung ändern","inside_houses.php?act=desc");
        
    }
    
    if (($row[status]>=10) && (($row[status]<30) || ($row[status]>=40)))
    {
        addnav("Besonderes");
    }
    
    if (($row[status]>=40) && ($row[status]<50))
    {
        addnav("Mit den Gildenmeistern reden (1000 Gold)","housefeats.php?act=fill");
    }
    
    if (($row[status]>=20) && ($row[status]<30))
    {
        addnav("Ins Kellergewölbe","housefeats.php?act=cry");
    }
    
    if ((($row[status]>=50) && ($row[status]<60)) && ($session['user']['hashorse']>0))
    {
        addnav("Tier versorgen","housefeats.php?act=feed");
    }
    
    if (($row[status]>=100) && ($row[status]<110))
    {
        addnav("Amüsieren (2000 Gold)","housefeats.php?act=amuse");
    }
    
    if (($row[status]>=90) && ($row[status]<100))
    {
        addnav("Mit den Veteranen trainieren (3000 Gold)","housefeats.php?act=train");
    }
    
    if (($row[status]>=70) && ($row[status]<80))
    {
        addnav("Gefangene quälen","housefeats.php?act=torture");
    }
    
    if (($row[status]>=80) && ($row[status]<90))
    {
        addnav("Heilung erbitten","housefeats.php?act=healing");
    }
    
    if ((($row[status]>=60) && ($row[status]<70)) && ($session['user']['level']>1))
    {
        addnav("Dem Blutgott opfern","housefeats.php?act=sacrifice");
    }
    
    if (($row[status]>=17) && ($row[status]<20))
    {
        addnav("Mütterchens Kohlsuppe kosten","housefeats.php?act=soup");
    }
    
    if (($row[status]>=24) && ($row[status]<27))
    {
        addnav("Ritual abhalten (1 Edelstein)","housefeats.php?act=ritual");
    }
    
    if (($row[status]>=44) && ($row[status]<47))
    {
        addnav("Zu den Abenteurern","housefeats.php?act=adventure");
        
        // Knappe
        $sql = "SELECT name,state FROM disciples WHERE master=".$session['user']['acctid']."";
        $result = db_query($sql) or die(db_error(LINK));
        $rowk['state']=0;
        if (db_num_rows($result)>0)
        {
            $rowk = db_fetch_assoc($result);
        }
        
        if ($rowk['state'] == 0 && db_num_rows($result))
        {
            addnav("Verlorenen Knappen suchen","housefeats.php?act=searchdisciple");
        }
    }
    
    if (($row[status]>=47) && ($row[status]<50))
    {
        addnav("Zum Schmuckhändler","housefeats.php?act=gems");
        addnav("Zum Lieferanten","housefeats.php?act=sendtrophy");
    }
    
    if (($row[status]>=54) && ($row[status]<57))
    {
        addnav("Zum Tiertrainer","housefeats.php?act=trainanimal");
    }
    
    if (($row[status]>=57) && ($row[status]<60))
    {
        addnav("Hart arbeiten","housefeats.php?act=workhard");
    }
    
    if (($row[status]>=64) && ($row[status]<67))
    {
        addnav("Zum Ahnenschrein","housefeats.php?act=givepower");
    }
    
    if (($row[status]>=67) && ($row[status]<70))
    {
        addnav("Zum Opferschrein","housefeats.php?act=suicide");
    }
    
    if (($row[status]>=74) && ($row[status]<77))
    {
        addnav("Zum Oberaufseher","housefeats.php?act=exchange");
    }
    
    if (($row[status]>=77) && ($row[status]<80))
    {
        addnav("Zur Gefangenenarena","housefeats.php?act=arena");
    }
    
    if (($row[status]>=84) && ($row[status]<87))
    {
        addnav("Den Segen erbitten","housefeats.php?act=bless");
    }
    
    if (($row[status]>=87) && ($row[status]<90))
    {
        addnav("Einen Knappen annehmen (20 Edelsteine)","housefeats.php?act=disciple");
    }
    
    if (($row[status]>=94) && ($row[status]<97))
    {
        addnav("Mit den Meistern trainieren (".($session['user']['level']*750)." Gold)","housefeats.php?act=mastertrain");
    }
    
    if (($row[status]>=97) && ($row[status]<100))
    {
        addnav("Zum Lagerschmied","housefeats.php?act=smith");
    }
    
    if (($row[status]>=104) && ($row[status]<107))
    {
        addnav("Orgie (3000 Gold)","housefeats.php?act=orgy");
    }
    
    if (($row[status]>=107) && ($row[status]<110))
    {
        
        // "Mafia"-Special
        $sql = "SELECT beatenup FROM account_extra_info WHERE acctid=".$session['user']['acctid']."";
        $result = db_query($sql) or die(db_error(LINK));
        $rowb = db_fetch_assoc($result);
        
        addnav("\"Die Familie\" befragen","housefeats.php?act=checkfriend");
        
        if ($rowb['beatenup']>1)
        {
            addnav("Schläger anheuern","housefeats.php?act=beater");
        }
        else
        {
            addnav("\"Familie\" beschenken","housefeats.php?act=familygift");
        }
        addnav("Zum Orkisch Roulette","housefeats.php?act=roulette");
    }
    // Knappe
    $sql = "SELECT name,state FROM disciples WHERE master=".$session['user']['acctid']."";
    $result = db_query($sql) or die(db_error(LINK));
    $rowk['state']=0;
    if (db_num_rows($result)>0)
    {
        $rowk = db_fetch_assoc($result);
    }
    if (($row[status]>=60) && ($row[status]<70) && ($rowk['state']>0) && ($rowk['state']<20) && ($session['user']['race']==RACE_VAMPIR))
    {
        addnav("".$rowk['name']." beißen","housefeats.php?act=dbite");
    }
    
    addnav("Ausgang");
    addnav("L?Einschlafen (Log Out)","inside_houses.php?act=logout");
    
    addnav("W?Zurück zum Wohnviertel","houses.php?op=enter");
    
    addnav("Zurück zum Dorf","village.php");
    addnav("Zurück zum Marktplatz","market.php");
    
}

page_footer();
?>
