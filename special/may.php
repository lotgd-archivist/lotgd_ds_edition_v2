<?php

// modded by talion: neues itemsys

if (!isset($session))
{
    exit();
}
$session[user][specialinc]="may.php";

if ($_GET[op]=="leave")
{
    $random=e_rand(1,5);
    switch ($random)
    {
    case 1 :
        $what="deinen H�nden";
        break;
    case 2 :
        $what="deinem Hals";
        break;
    case 3 :
        $what="deinen Beinen";
        break;
    case 4 :
        $what="deinen Augen";
        break;
    case 5 :
        $what="deinen F��en";
        break;
    }
    output("`3Ihr Blick, der seid Beginn eures Gespr�ches best�ndig mit eiskalter Faszination auf $what lag, macht dir nun doch etwas Angst und du entfernst dich rasch`nDeine Schritte werden allm�hlich immer schneller, schliesslich rennst du.`nNach einigen Minuten bist du v�llig au�er Puste, f�hlst dich aber in Sicherheit.");
    
    item_delete(' tpl_id="kpuppe" AND owner='.$session['user']['acctid']);
    
    $session[user][specialinc]="";
    addnav("Weiter","forest.php");
}
else if ($_GET[op]=="leave2")
{
    
    output("`3Die seltsame Frau winkt dir zaghaft mit ihren blutverschmierten H�nden hinterher als du deinen neuen Freund nimmst und gehst.`n");
    $session[user][specialinc]="";
    addnav("Weiter","forest.php");
}
else if ($_GET[op]=="make")
{
    $break=0;
    for ($i=1; $i<7; $i++)
    {
        
        if (item_count(' tpl_id="trph" AND owner='.$session['user']['acctid'].' AND value2='.$i)<2)
        {
            $break=1;
            $i=7;
        }
    }
    
    if ($break==0)
    {
        for ($i=7; $i<9; $i++)
        {
            
            if (item_count(' tpl_id="trph" AND owner='.$session['user']['acctid'].' AND value2='.$i)<1)
            {
                $break=1;
                $i=9;
            }
        }
    }
    
    if ($break==0)
    {
        
        item_add($session['user']['acctid'],'kpuppe');
        
        redirect("forest.php?op=make2&index=1");
    }
    else
    {
        output("`3Die junge Frau schaut dich eine Weile durchdringend an, dann sagt sie mit leiser Stimme`n\"`8Du hast nicht was ich daf�r brauche...`3\"`n`nDu beschliesst zu gehen, da dir das Ganze nun doch sehr suspekt vorkommt.`n");
        $session[user][specialinc]="";
        addnav("Weiter","forest.php");
    }
}
else if ($_GET[op]=="make2")
{
    $index=$_GET['index'];
    $lastindex=$index-1;
    $name=rawurldecode($_GET['choice']);
    $value=$_GET['value'];
    $itemid=$_GET['itemid'];
    
    $doll = item_get(' tpl_id="kpuppe" AND owner='.$session['user']['acctid'], false);
    
    $newvalue=$doll['hvalue']+$value;
    
    if ($lastindex==1)
    {
        $newdesc=$name;
    }
    else
    {
        $newdesc=$doll['description']."`0, ".$name;
    }
    
    if ($lastindex==14)
    {
        $newdesc=$newdesc."`nWert : ".$newvalue." Drachenkills`0";
    }
    
    item_set(' id='.$doll['id'] , array('hvalue'=>$newvalue,'description'=>$newdesc) );
    
	if($itemid > 0) {
	    item_delete(' tpl_id="trph" AND owner='.$session['user']['acctid'].' AND id='.$itemid);
	}
    
    if ($index>8)
    {
        $index2=$index-8;
    }
    else
    {
        $index2=$index;
    }
    
    $result = item_list_get(' owner='.$session['user']['acctid'].' AND tpl_id="trph" AND value2='.$index2, '', false);
    $amount=(db_num_rows($result));
    
    if ($index<15)
    {
        output("`3Die junge Frau blickt dich gleichg�ltig an und sagt mit leiser Stimme:`n`3\"`8Entscheidet Euch, was ich als Teil $index verwenden soll.`3\"`n`n");
        $index++;
        for ($j=1; $j<=$amount; $j++)
        {
            $partsname=db_fetch_assoc($result);
            $choice=rawurlencode($partsname[name]);
            $value=$partsname[value1];
            $itemid=$partsname[id];
            output("`n<a href='forest.php?op=make2&index=$index&choice=$choice&value=$value&itemid=$itemid'>$partsname[name]</a>",true);
            
            addnav("","forest.php?op=make2&index=$index&choice=$choice&value=$value&itemid=$itemid");
        }
        addnav("Weg hier!","forest.php?op=leave");
    }
    else
    {
        output("`3`n`nDie seltsame Frau l�chelt f�r einen kurzen Moment und zieht sich zur�ck um sich an die Arbeit zu machen.`nEinige Stunden sp�ter kehrt sie zur�ck und �berreicht dir deinen neuen Freund.`n`n");
        
        $doll = item_get(' tpl_id="kpuppe" AND owner='.$session['user']['acctid'], false);
        
        output("`n`3Die Bestandteile deiner Puppe sind : ".$doll['description']." `3`n`nSie hat einen Wert von `^".$doll['hvalue']."`3 Drachenkills.`n`n");
        
        addnews("`#".$session['user']['name']." `4 hat nun einen neuen Freund...");
        
        addnav("Weitergehen","forest.php?op=leave2");
    }
    
    
    
}
else
{
    
    if (item_count(' tpl_id="kpuppe" AND owner='.$session['user']['acctid'])==0)
    {
        output("`3Du kommst auf deiner Suche nach Gegnern an einer kleinen Holzbank vorbei, auf der bereits eine junge Frau sitz, die verloren ins Leere starrt. Du setzt dich zu ihr um ein wenig zu Verschnaufen und blickst sie an.`nIhr Gesicht ist blass, fast schon wei� und ihre Haare sind tiefdunkel und reichen ihr bis knapp �ber die Schultern. Die Lippen sind in kr�ftigem Rot gef�rbt und auf der rechten Seite tr�gt sie eine Augenklappe aus d�nnem schwarzen Stoff.`n`n\"`#Wie hei�t Ihr?`3\" fragst du sie h�flich.`n`n\"`8Was spielt das f�r eine Rolle?`3\" antwortet sie mit trauriger Stimme.`n`nDu wartest einen Augenblick, dann versuchst du es erneut.`n\"`#Was macht Ihr?`3\"`n`n`3\"`8Ich n�he.`3\" sagt sie leise und ihre Stimme klingt kalt.`n`n\"`#Ihr n�ht?`3\" fragst du nach.`n`n\"`8Ich n�he Puppen.`3\"`n`n\"`#Was f�r Puppen?`3\"`n`n\"`8Freunde...`3\"`n`nDu verstehst es nicht und deine Stirn legt sich in Falten.`n`n\"`8Wenn Du keine Freunde findest, mach Dir welche!`3\" sagt sie und bei dem Ausdruck in ihrer Stimme und in ihrem Blick l�uft es dir eiskalt den R�cken herunter.`n`nDu �berlegst eine Weile was du antworten sollst.`n`n");
        
        addnav("Antworte");
        addnav("Fertigt mir eine Puppe","forest.php?op=make");
        addnav("�h... ich muss los","forest.php?op=leave");
    }
    else
    {
        output("`3Du kommst erneut an der Bank vorbei, auf der wieder die junge Frau sitzt, die dir die Puppe gefertigt hat.`nAls sie dich erblickt, hellt ihr Gesicht f�r einem Moment auf und sie winkt dir zu.`nDu hast jedoch gerade Besseres zu tun und gehst schnell weiter.`n");
        $session[user][specialinc]="";
        addnav("Weiter","forest.php");
    }
}

?>

