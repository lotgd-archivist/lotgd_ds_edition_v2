<?php
// MightyE's Shop für besondere Kunden
// Waffen umbenennen für 500 DP einmalig, dann für jeweils 10 DP
//
// Erfordert : [rename_weapons] in [user]
// Modifiziert : weapons.php, bio.php, dragon.php
//
// by Maris(Maraxxus@gmx.de)
// (inspiriert durch : lodge.php)

require_once "common.php";

require_once(LIB_PATH.'dg_funcs.lib.php');
if ($session['user']['guildid'] && $session['user']['guildfunc'] != DG_FUNC_APPLICANT)
{
    $rebate = dg_calc_boni($session['user']['guildid'],'rebates_weapon',0);
}

checkday();

page_header("MightyE's Laden für besondere Kunden");
$tradeinvalue = round(($session[user][weaponvalue]*.75),0);
$pointsavailable=$session['user']['donation']-$session['user']['donationspent'];

if ($_GET[op]=="peruse")
{
    $sql = "SELECT max(level) AS level FROM weapons WHERE level<=".(int)$session[user][dragonkills];
    $result = db_query($sql) or die(db_error(LINK));
    $row = db_fetch_assoc($result);
    
    $sql = "SELECT * FROM weapons WHERE level = ".(int)$row[level]." ORDER BY damage ASC";
    $result = db_query($sql) or die(db_error(LINK));
    
    
    output("`!MightyE`7 winkt dich mit einem Lächeln herein und sagt \"`#Willkommen! Ich gebe dir `^$tradeinvalue`# ");
    output(" Gold für `5".$session[user][weapon]."`# ".($rebate?"und `^".$rebate." %`# Rabatt dank deiner Gildenmitgliedschaft.":"").". Dann suche dir mal eine Waffe aus und ich werde sie nach deinen Wünschen für dich gravieren. Ganz billig wird das aber nicht, selbst wenn es um jemanden wie dich geht`7.\". `n`n");
    
    output("<table border='0' cellpadding='0'>",true);
    output("<tr class='trhead'><td>`bName`b</td><td align='center'>`bSchaden`b</td><td align='right'>`bPreis`b</td></tr>",true);
    for ($i=0; $i<db_num_rows($result); $i++)
    {
        $row = db_fetch_assoc($result);
        
        $row['value'] = ceil($row['value'] * (100 - $rebate) * 0.01);
        
        $bgcolor=($i%2==1?"trlight":"trdark");
        if ($row[value]<=($session[user][gold]+$tradeinvalue))
        {
            output("<tr class='$bgcolor'><td>Kaufe <a href='xshop.php?op=buy&id=$row[weaponid]'>$row[weaponname]</a></td><td align='center'>$row[damage]</td><td align='right'>$row[value]</td></tr>",true);
            addnav("","xshop.php?op=buy&id=$row[weaponid]");
        }
        else
        {
            
            output("<tr class='$bgcolor'><td>- - - - <a href='xshop.php?op=buy&id=$row[weaponid]'>$row[weaponname]</a></td><td align='center'>$row[damage]</td><td align='right'>$row[value]</td></tr>",true);
            addnav("","xshop.php?op=buy&id=$row[weaponid]");
        }
    }
    output("</table>",true);
    addnav("Zurück zum Marktplatz","market.php");
    
}
else if ($_GET[op]=="buy")
{
    $sql = "SELECT * FROM weapons WHERE weaponid='$HTTP_GET_VARS[id]'";
    $result = db_query($sql) or die(db_error(LINK));
    if (db_num_rows($result)==0)
    {
        output("`!MightyE`7 schaut dich eine Sekunde lang verwirrt an und kommt zu dem Schluss, dass du ein paar Schläge zuviel auf den Kopf bekommen hast. Schließlich geleitet er dich nach draussen.");
        addnav("Zurück zum Marktplatz","market.php");
    }
    else
    {
        $row = db_fetch_assoc($result);
        
        $row['value'] = ceil($row['value'] * (100 - $rebate) * 0.01);
        
        if ($row[value]>($session[user][gold]+$tradeinvalue))
        {
            output("`!MightyE`7 schüttelt den Kopf als du auf eine Waffe deutest, die du dir beim besten Willen nicht leisten kannst. ");
            addnav("Nochmal","xshop.php?op=peruse");
            addnav("Zurück zum Marktplatz","market.php");
        }
        else
        {
            output("`!MightyE`7 nimmt dein `5".$session[user][weapon]."`7 in Zahlung. ");
            
			$arr_wpn['tpl_name'] = $row['weaponname'];
			$arr_wpn['tpl_value1'] = $row['damage'];
			$arr_wpn['tpl_gold'] = $row['value'];
			
            $session[user][gold]-=$row[value];
	        $session[user][gold]+=$tradeinvalue;

            output("`n`nIm Gegenzug händigt er dir ein glänzendes, neues `5$row[weaponname]`7 aus.`n");
            output("`!MightyE`7 fragt dich : \"`#Soll ich dir was darauf eingravieren?`n");
            output("`#Eine Gravur kostet dich zusätzlich nochmal 10 Donation Punkte, du könntest damit deiner Waffe einen eigenen Namen geben. Na wie wäre es ?");
            
            addnav("Gravieren (10 DP)","xshop.php?op=name");
            addnav("Och nee, lass mal","village.php");
        }
    }
}
else if ($_GET['op']=="name")
{
    output("`bEine Waffe benennen`b`n`n");
    
    output("`n`nDer Name deiner Waffe darf 30 Zeichen lang sein und Farbcodes enthalten.`nVermeide es schwarz zu verwenden, da diese Farbe auf dunklem Hintergrung gar nicht oder nur schlecht angezeigt wird.`n`n");
    $n = $session['user'][weapon];
    
    output("Deine Waffe heißt bisher : `n");
    $output.=$n;
    output("`n`n`0Wie soll deine Waffe heißen ?`n");
    $output.="<form action='xshop.php?op=namepreview' method='POST'><input name='newname' value=\"".HTMLEntities($newname)."\" size=\"30\" maxlength=\"30\"> <input type='submit' value='Vorschau'></form>";
    addnav("","xshop.php?op=namepreview");
    
}
else if ($_GET['op']=="namepreview")
{
    $n = $session[user][weapon];
    
    $_POST['newname']=str_replace("`0","",$_POST['newname']);
    
    $_POST['newname'] = preg_replace("/[`][c]/","",$_POST['newname']);
    
    if (strlen($_POST['newname'])>30)
    {
        $msg.="Der neuer Name ist zu lang, inklusive Farbcodes darf er nicht länger als 30 Zeichen sein.`n";
    }
    $colorcount=0;
    for ($x=0; $x<strlen($_POST['newname']); $x++)
    {
        if (substr($_POST['newname'],$x,1)=="`")
        {
            $x++;
            $colorcount++;
        }
    }
    if ($colorcount>getsetting("maxcolors",8))
    {
        $msg.="Du hast zu viele Farben im Namen benutzt. Du kannst maximal ".getsetting("maxcolors",8)." Farbcodes benutzen.`n";
    }
    if ($msg=="")
    {
        output("Deine Waffe wird so heißen: {$_POST['newname']}
        `n`n`0Ist es das was du willst?`n`n");
        $p = 10;
        $output.="<form action=\"xshop.php?op=changename\" method='POST'><input type='hidden' name='name' value=\"".HTMLEntities($_POST['newname'])."\"><input type='submit' value='Ja' class='button'>, meine Waffe heißt nun ".appoencode("{$_POST['newname']}
        `0")." für $p Punkte.</form>";
        output("`n`n<a href='xshop.php?op=name'>Nein, lass es mich nochmal versuchen!</a>",true);
        addnav("","xshop.php?op=name");
        addnav("","xshop.php?op=changename");
    }
    else
    {
        output("`bFalscher Name`b`n$msg");
        output("`n`nDeine Waffe heißt bisher : ");
        $output.=$n;
        output("`0, und wird so aussehen $newname");
        output("`n`nWie soll deine Waffe heißen?`n");
        $output.="<form action='xshop.php?op=namepreview' method='POST'><input name='newname' value=\"".HTMLEntities($regname)."\"size=\"30\" maxlength=\"30\"> <input type='submit' value='Vorschau'></form>";
        addnav("","xshop.php?op=namepreview");
    }
}
else if ($_GET['op']=="changename")
{
    $p = 10;
    if ($pointsavailable>=$p)
    {
        $session['user']['donationspent']+=$p;
		
		item_set_weapon($_POST['name'],-1,-1,0,0,1);
		
        output("Gratulation, deine neue Waffe heißt jetzt {$session['user']['weapon']}
        `0!`n`n");
    }
    else
    {
        output("Eine Gravur kostet $p Punkte, aber du hast nur $pointsavailable Punkte.");
    }
    addnav("Zurück zum Marktplatz","market.php");
}

if(is_array($arr_wpn)) {
	
	// Zu invent hinzufügen
	$int_wid = item_add($session['user']['acctid'],'waffedummy',true,$arr_wpn);
	// Als Waffe ausrüsten (dabei alte Waffe löschen)
	item_set_weapon($arr_wpn['tpl_name'],$arr_wpn['tpl_value1'],$arr_wpn['tpl_gold'],$int_wid,0,2);
	
}

page_footer();
?>