<?php
// Dorfamt von Atrahor
// Basiert auf:
// Dorfamt
// Make by Kev
// Make for www.logd.de.to
// 05-09-2004 September
// E-Mail: logd@gmx.net
// Website: www.logd.de.to
// Copyright 2004 by Kev

// Ergänzungen und Erweiterungen von Dragonslayer, Maris, Talion

require_once "common.php";
page_header("Das Dorfamt");

if ($_GET['op']=="")
{
    output("`c`b`&Das Dorfamt`0`b`c`n`n");
    output("`2
			Du trittst in eine große Halle, die an beiden Seiten von weißen Marmorsäulen gesäumt wird.
			Gegenüber des Eingangstores befindet sich ein freundlich aussehender Schreibtisch und dahinter eine
			noch freundlicher aussehende Dame, die sich mit einigen Papieren beschäftigt.
			An der Wand hinter dem Schreibtisch hängt ein Schild mit der Aufschrift`n");
    output("`c`2In der Amtskasse befinden sich `^" .number_format(getsetting("amtskasse", 0),0,'',' '). " `2Goldstücke !`c", true);
    output("`n`2 Als Du näher trittst hebt die Empfangsdame den Blick, sieht Dich an und fragt nach Deinem Begehr!`n
    \"`@Willkommen, bitte nicht wundern, die Amtssprache wird Euch seltsam erscheinen. Was kann ich für Euch tun? \"");
    
    require_once(LIB_PATH.'board.lib.php');
    
    output('`n`n`c`7Die letzten Namensänderungen in '.getsetting('townname','Atrahor').':`&`n');
    
    board_view('namechange',(su_check(SU_RIGHT_DEBUG) > 1 ? 2 : 0),'','In letzter Zeit hat niemand seinen Namen geändert!',false);
    
    output('`c');
    
    addnav("Informationen zu...");
    addnav("Steuern?","dorfamt.php?op=steuern");
    addnav("Ruhmeshalle","hof.php");
    
    addnav("Wichtiges...");
    addnav("Steuern Zahlen","dorfamt.php?op=steuernzahlen");
    addnav("Die Amtskasse","dorfamt.php?op=amtskasse");
    addnav("Der Magistrat","dorfamt.php?op=magistrat");
    
    if (($session['user']['profession']==1) || ($session['user']['profession']==2) || (su_check(SU_RIGHT_DEBUG)) )
    {
        addnav("Stadtwache");
        addnav("Hauptquartier betreten","wache.php?op=hq");
    }
    addnav("Gerichtshof");
    addnav("Gericht betreten","court.php?op=court");
    
    addnav("Sonstiges...");
    addnav("Diskussionen","ooc.php?op=diskus");
    addnav("OOC Diskussionen","ooc.php?op=ooc");
    
    addnav("Zum Dorf");
    addnav("Z?Zurück zum Dorf","village.php");
}
else if ($_GET['op']=="steuern")
{
    $taxrate=getsetting("taxrate",750);
    $doubletax=2*$taxrate;
    $taxprison=getsetting("taxprison",1);
    output("`2Du erkundigst dich nach den Steuern...`n
			Die Karte, die Dir von der Empfangsdame gegeben wird, zeigt Dir folgendes:
			`n`n
			");
    
    if ($taxrate>0)
    {
        output("`^Steuern für Neuankömmlinge und Auserwählte:`n`2
				Es müssen keine Steuern entrichtet werden!
				`n`n`n
				`^Steuern zwischen Level 5 und 10:`n`2
				Die Steuer beträgt derzeit `^{$taxrate} Gold`2!
				`n`n`n
				`^Steuern über Level 10:`n`2
				Die Steuer beträgt derzeit `^{$doubletax} Gold`2!
				`n`n`n");
        if ($taxprison==1)
        {
            output("`4Auf Steuerhinterziehung steht ein Tag Kerker!`0");
        }
        else
        {
            output("`4Auf Steuerhinterziehung stehen {$taxprison} Tage Kerker!`0");
        }
    }
    else
    {
        output("`^Derzeit werden keine Steuern erhoben!`n`n");
    }
    
    addnav("Wege");
    addnav("Z?Zurück","dorfamt.php");
}
else if ($_GET['op']=="magistrat")
{
    output("`c`b`&Der Magistrat`0`b`c`n`n");
    output("`2Du steigst die Stufen zum 2. Stock des Amtshauses hinauf und schreitest über den holzvertäfelten Gang zu den Räumen des Magistrats. Hier tagen die hohen Herren und entscheiden über das Wohl des Dorfes. Ein angenehmer Duft liegt in der Luft und du hörst gedämpfte Stimmen aus den Räumen zu dir hervor dringen. Mit klopfendem Herzen gehst du weiter.`n`n");
    addnav("Magistrat");
	
    addnav("Vorzimmerdame","dorfamt.php?op=dame1");
    addnav("Fürstliches Büro","dorfamt.php?op=office_entry");
    
    if ($session['user']['profession']==0)
    {
        addnav("Stadtwache werden","wache.php?op=bewerben");
    }
    if ($session['user']['profession']==5)
    {
        addnav("Bewerbung zurückziehen","wache.php?op=bewerben_abbr");
    }
    
    if ($session['user']['profession']==0)
    {
        addnav("Richter werden","court.php?op=bewerben");
    }
    if ($session['user']['profession']==25)
    {
        addnav("Bewerbung zurückziehen","court.php?op=bewerben_abbr");
    }
    
    if (($session['user']['profession']==1) || ($session['user']['profession']==2))
    {
        addnav("Austreten?");
        addnav("Entlassung erbitten","wache.php?op=leave");
    }
    
    if (($session['user']['profession']==21) || ($session['user']['profession']==22))
    {
        addnav("Austreten?");
        addnav("Entlassung erbitten","court.php?op=leave");
    }
    
    addnav("Sonstiges");
    addnav("Zurück","dorfamt.php");
}
else if ($_GET['op']=="amtskasse")
{
    output("`2Du läufst durch die Gänge des Amtes...`n
			Als Du einige Herren über die Amtskasse reden hörst, stellst Du Dich wie beiläufig daneben
			und erfährst, dass die Amtskasse zur Zeit `^" .getsetting("amtskasse" ,0). " `2Goldstücke enthält!");
    
    addnav("Wege");
    addnav("Zurück","dorfamt.php");
}
else if ($_GET['op']=="office_entry")
{
    output("`^Die Tür zum Zimmer des Fürsten wird dir von seinem persönlichen Leibwächter geöffnet, der sich zu jeder Zeit im Raum befinden und loyal zu seinem Herren stehen wird, sollte es zu Unstimmigkeiten kommen. Du solltest es dir also gut überlegen, wie du den Fürsten von ".getsetting('townname','Atrahor').",`^ der hinter seinem breiten Schreibtisch aus Eichenholz sitzt, ansprechen wirst. Direkt vor dem Schreibtisch siehst du einen ebenso fein von Hand gearbeiteten Stuhl, der schon für dich bereitsteht. Doch bevor du dich setzt, erlaubst du dir einen raschen Blick durch das Zimmer. Überall stehen Möbel, die auf Hochglanz poliert und reichlich mit Edelsteinen und Schmuck ausgeschmückt sind. Doch das edelste Stück im ganzen Zimmer ist der riesige Kronleuchter, den die Zwerge in langwähriger Arbeit gänzlich aus Kristallen hergestellt haben.`n`n");
    $sql = 'SELECT ctitle FROM account_extra_info WHERE acctid='.$session['user']['acctid'];
    $res = db_query($sql);
    $row_extra = db_fetch_assoc($res);
    if (($row_extra['ctitle']=="Fürst von ".getsetting('townname','Atrahor')) || ($row_extra['ctitle']=="Fürstin von ".getsetting('townname','Atrahor')) || $session['user']['superuser']>0)
    {
        addnav("Fürstliches");
        addnav("Steuern","dorfamt.php?op=office_taxes");
        addnav("Strafe für Steuersünder","dorfamt.php?op=office_prison");
        addnav("Wanderhändler herbefehlen","dorfamt.php?op=office_vendor");
        addnav("Amtskasse","dorfamt.php?op=office_budget");
        addnav("Verlassen");
    }
    addnav("Zurück","dorfamt.php?op=magistrat");
    addcommentary();
    viewcommentary("office_sovereign","Sagen",30,"sagt");
}
else if ($_GET['op']=="office_taxes")
{
    $taxrate=getsetting("taxrate",750);
    $doubletax=$taxrate*2;
    $taxchange=getsetting("taxchange",1);
    output("`c`&Steuern`c`n`n
			Dein Finanzminister flüstert dir zu:`n\"
			`2Bedenkt bei Eurer Steuerpolitik stets, dass ein zu hoher Steuersatz unzufriedene Untertanen schafft.`n
			Ein zu niedriger Steuersatz hingegen zwingt uns zu Einsparungen. Das Dorffest könnte dann beispielweise
			nicht mehr so oft stattfinden, was die Untertanen natürlich auch wieder unzufrieden macht.`n`n
			Bislang gilt:`n
			Neuankömmlinge (bis Level 4) und Auserwählte zahlen `^keine Steuern`2.`n
			Bewohner (Level 5 bis Level 10) zahlen den einfachen Steuersatz. Dieser beträgt derzeit `^{$taxrate} Gold`2.`n
			Alteingesessene (Level 11 bis Level 15) zahlen den doppelten Steuersatz. Dieser beträgt `^{$doubletax} Gold.`&\"`n`n`n");
    if ($taxchange==1)
    {
        output("Diesen Monat kannst du den Steuersatz `@noch einmal`& ändern!`n");
        addnav("Ändern");
        addnav("Steuersatz ändern","dorfamt.php?op=office_change_taxes");
    }
    else
    {
        output("Diesen Monat kannst du den Steuersatz `4nicht mehr`& ändern.`n");
        if ($session['user']['superuser']>0)
        {
            addnav("Mods");
            addnav("Änderung zulassen","dorfamt.php?op=mod_taxes");
        }
    }
    addnav("Zurück");
    addnav("Ins Büro","dorfamt.php?op=office_entry");
}
else if ($_GET['op']=="office_change_taxes")
{
    $taxrate=getsetting("taxrate",750);
    $taxchange=getsetting("taxchange",1);
    $maxtaxes=getsetting("maxtaxes",2000);
    if ($taxchange==1)
    {
        output("<form action='dorfamt.php?op=office_change_taxes2' method='POST'>`&Der Steuersatz liegt bei `^{$taxrate}`& Gold.`n",true);
        output("`&Wie hoch hättest du ihn gern? (Maximal {$maxtaxes} Gold)<input id='input' name='amount' width=4 value='$taxrate'> <input type='submit' class='button' value='festlegen'>`n</form>",true);
        output("<script language='javascript'>document.getElementById('input').focus();</script>",true);
        addnav("","dorfamt.php?op=office_change_taxes2");
        addnav("Doch nicht ändern","dorfamt.php?op=office_entry");
    }
    else
    {
        output("Du kannst diesen Monat den Steuersatz nicht mehr verändern.");
        addnav("Ins Büro","dorfamt.php?op=office_entry");
    }
}
else if ($_GET['op']=="mod_taxes")
{
    savesetting("taxchange",1);
    redirect("dorfamt.php?op=office_taxes");
}
else if ($_GET['op']=="mod_prison")
{
    savesetting("prisonchange",1);
    redirect("dorfamt.php?op=office_prison");
}
else if ($_GET['op']=="mod_vendor")
{
    savesetting("callvendor",getsetting("callvendormax",5));
    redirect("dorfamt.php?op=office_vendor");
}
else if ($_GET['op']=="office_change_taxes2")
{
    $taxrate=getsetting("taxrate",750);
    $maxtaxes=getsetting("maxtaxes",2000);
    $mintaxes=getsetting("mintaxes","0");
    
    // Man kann ja nie wissen...
    if ($mintaxes<0)
    {
        $mintaxes=0;
        savesetting("mintaxes","0");
    }
    
    if ($maxtaxes<$mintaxes)
    {
        $maxtaxes=$mintaxes;
        savesetting("maxtaxes",$mintaxes);
    }
    
    $_POST['amount']=floor((int)$_POST['amount']);
    if ($_POST['amount']<$mintaxes)
    {
        output("`&Dein Finazminister schaut dich skeptisch an.`n
				\"`2Wollt Ihr etwa Gold verschenken?`&\" fragt er ungläubig.");
        addnav("Nochmal","dorfamt.php?op=office_change_taxes");
    }
    else if ($_POST['amount']>$maxtaxes)
    {
        output("`&Dein Finazminister schaut dich skeptisch an.`n
				\"`2Wollt Ihr eine Revolte provozieren?`&\" fragt er ungläubig.");
        addnav("Nochmal","dorfamt.php?op=office_change_taxes");
    }
    else if ($_POST['amount']==$taxrate)
    {
        output("`&Dein Finazminister nick bestätigend.`n
				\"`2Damit bliebe also alles beim alten.`&\" sagt er.");
        addnav("Ins Büro","dorfamt.php?op=office_entry");
    }
    else
    {
        output("`&Dein Finanzminister fragt nochmal nach.`n
				\"`2Seid Ihr Euch sicher, dass Ihr den Steuersatz auf `^{$_POST['amount']}
				Gold`2 ändern wollt?`&\"");
        addnav("Ja","dorfamt.php?op=office_change_taxes3&amount=$_POST[amount]");
        addnav("Nein","dorfamt.php?op=office_change_taxes");
    }
}
else if ($_GET['op']=="office_change_taxes3")
{
    $taxrate=getsetting("taxrate",750);
    $newtax=$_GET['amount'];
    output("`&Der neue Steuersatz beträgt von nun an `^{$newtax} Gold`&!");
    savesetting("taxrate",$newtax);
    savesetting("taxchange","0");
    if ($newtax>0)
    {
        addnews("{$session['user']['name']}
        `^ hat heute den Steuersatz auf {$newtax}
        Gold ".($newtax>$taxrate?"erhöht":"gesenkt").".");
    }
    else
    {
        addnews("{$session['user']['name']}`^ hat heute die Steuern abgeschafft!");
    }
    addnav("Ins Büro","dorfamt.php?op=office_entry");
}
else if ($_GET['op']=="office_prison")
{
    $taxprison=getsetting("taxprison",1);
    $prisonchange=getsetting("prisonchange",1);
    output("`c`&Steuerhinterziehung`c`n`n
			Dein Finanzminister ranut dir zu:`n\"
			`2Steuerhinterzieher wandern derzeit ");
    if ($taxprison==0)
    {
        output("nicht ");
    }
    if ($taxprison==1)
    {
        output("für einen Tag ");
    }
    if ($taxprison>1)
    {
        output("für {$taxprison} Tage ");
    }
    output("hinter Gitter.`nViel zu wenig wenn Ihr mich fragt.`&\"`n`n`n");
    if ($prisonchange==1)
    {
        output("Diesen Monat kannst du das Strafmaß für Steuerhinterziehung `@noch einmal`& ändern!`n");
        addnav("Ändern");
        addnav("Strafmaß ändern","dorfamt.php?op=office_change_prison");
    }
    else
    {
        output("Diesen Monat kannst du das Strafmaß für Steuerhinterziehung `4nicht mehr`& ändern.`n");
        if ($session['user']['superuser']>0)
        {
            addnav("Mods");
            addnav("Änderung zulassen","dorfamt.php?op=mod_prison");
        }
    }
    addnav("Zurück");
    addnav("Ins Büro","dorfamt.php?op=office_entry");
}
else if ($_GET['op']=="office_change_prison")
{
    $prisonchange=getsetting("prisonchange",1);
    $maxprison=getsetting("maxprison",2);
    $taxprison=getsetting("taxprison",1);
    if ($prisonchange==1)
    {
        output("<form action='dorfamt.php?op=office_change_prison2' method='POST'>`&Das Strafmaß liegt bei `^{$taxprison}`& Tagen Haft. Darüberhinaus wird das Doppelte der hinterzogenen Steuer gepfändet.`n",true);
        output("`&Wie hoch hättest du das Strafmaß gern? (Maximal {$maxprison} Tage)<input id='input' name='amount' width=4 value='$taxprison'> <input type='submit' class='button' value='festlegen'>`n</form>",true);
        output("<script language='javascript'>document.getElementById('input').focus();</script>",true);
        addnav("","dorfamt.php?op=office_change_prison2");
        addnav("Doch nicht ändern","dorfamt.php?op=office_entry");
    }
    else
    {
        output("Du kannst diesen Monat das Strafmaß für Steuerhinterziehung nicht mehr verändern.");
        addnav("Ins Büro","dorfamt.php?op=office_entry");
    }
}
else if ($_GET['op']=="office_change_prison2")
{
    $prisonchange=getsetting("prisonchange",1);
    $maxprison=getsetting("maxprison",3);
    $taxprison=getsetting("taxprison",1);
    
    $_POST['amount']=floor((int)$_POST['amount']);
    if ($_POST['amount']<0)
    {
        output("`&Dein Finazminister schaut dich skeptisch an.`n
				\"`2Wollt Ihr die Verbrecher auch noch belohnen?`&\" fragt er ungläubig.");
        addnav("Nochmal","dorfamt.php?op=office_change_prison");
    }
    else if ($_POST['amount']>$maxprison)
    {
        output("`&Dein Finazminister seufzt.`n
				\"`2Das lässt sich mit der allgemeinen Gesetzgebung nicht vereinbaren.`&\" sagt er missmutig.");
        addnav("Nochmal","dorfamt.php?op=office_change_prison");
    }
    else if ($_POST['amount']==$taxprison)
    {
        output("`&Dein Finazminister nick bestätigend.`n
				\"`2Damit bliebe also alles beim alten.`&\" sagt er.");
        addnav("Ins Büro","dorfamt.php?op=office_entry");
    }
    else
    {
        output("`&Dein Finazminister fragt nochmal nach.`n
				\"`2Seid Ihr Euch sicher, dass Ihr das Strafmaß für Steuerhinterziehung auf `^{$_POST['amount']}
				Tage`2 ändern wollt?`&\"");
        addnav("Ja","dorfamt.php?op=office_change_prison3&amount=$_POST[amount]");
        addnav("Nein","dorfamt.php?op=office_change_prison");
    }
}
else if ($_GET['op']=="office_change_prison3")
{
    $taxprison=getsetting("taxprison",1);
    $newprison=$_GET['amount'];
    output("`&Das neue Stafmaß beträgt von nun an `^{$newprison} Tage Kerker`&!");
    savesetting("taxprison",$newprison);
    savesetting("prisonchange","0");
    if ($newprison>0)
    {
        addnews("{$session['user']['name']}`^ hat heute das Strafmaß für Steuerhinterziehung auf {$newprison} Tage Kerker ".($newprison>$taxprison?"erhöht":"gesenkt").".");
    }
    else
    {
        addnews("{$session['user']['name']}`^ hat heute die Kerkerhaft für Steuerhinterziehung abgeschafft!");
    }
    
    addnav("Ins Büro","dorfamt.php?op=office_entry");
}
else if ($_GET['op']=="office_vendor")
{
    output("`c`&Wanderhändler`c`n
			Hier kannst du einen Eilboten in die umliegenden Städte schicken und Aeki damit drohen, ihm die Lizens zu entziehen, wenn er sich nicht augenblicklich im Dorf sehen lässt.`n`n");
    if (getsetting("vendor",0)==1)
    {
        output("Aber da der Wanderhändler derzeit auf dem Markplatz seine Zelte aufgeschlagen hat, würde eine solche Drohung nichts nützen.`n`n");
        addnav("Ins Büro","dorfamt.php?op=office_entry");
    }
    else
    {
        $callvendor=getsetting("callvendor",5);
        if ($callvendor>0)
        {
            output("`&Du kannst dies in deiner derzeitigen Amtszeit noch `^{$callvendor}`&mal tun.");
            addnav("Herbeordern");
            addnav("Wanderhändler rufen","dorfamt.php?op=office_call_vendor");
            addnav("Zurück");
            addnav("Ins Büro","dorfamt.php?op=office_entry");
        }
        else
        {
            output("`&Leider hast du dies schon so oft gemacht, dass er es gar nicht mehr einsieht auf deine Drohungen einzugegen. Im Nachbardorf verdient er sowieso mehr!");
            addnav("Ins Büro","dorfamt.php?op=office_entry");
            if ($session['user']['superuser']>0)
            {
                addnav("Mods");
                addnav("Rufen zulassen","dorfamt.php?op=mod_vendor");
            }
        }
    }
}
else if ($_GET['op']=="office_call_vendor")
{
    $callvendor=getsetting("callvendor",5);
    output("`&Dein schnellster Bote macht sich auf den Weg und schleift den Wanderhändler mitsamt seinem Gerümpel auf den Marktplatz.`n`n");
    $callvendor--;
    savesetting("callvendor",$callvendor);
    savesetting("vendor",1);
    addnav("Ins Büro","dorfamt.php?op=office_entry");
}
else if ($_GET['op']=="office_budget")
{
    $party=getsetting("min_party_level", 500000);
    $stone=getsetting("paidgold","0");
    $stonemax=getsetting("beggarmax","25000");
    $budget=getsetting("amtskasse","0");
    $amtsgems=getsetting("amtsgems","0");
    $lurevendor=getsetting("lurevendor","40000");
    $freeorkburg=getsetting("freeorkburg","30000");
    output("`n`2Die Amtskasse ist mit `^".$budget. " `2Goldstücken gefüllt.`n
			Die Truhen fassen maximal `^".getsetting("maxbudget","2000000")." `2Gold.`n`n
			In den Tresoren lagern `^".$amtsgems." `2Edelsteine.`n
			Maximal fassen die Tresore `^".getsetting("maxamtsgems","100")." `2Edelsteine.`n`n`n`n
			Auf dem Bettelstein sind derzeit `^".$stone." `2Gold hinterlegt.`n
			Sein Fassungsvermögen beträgt `^".$stonemax." `2Gold.`n`n
			Den Weg zur Orkburg freizuräumen kostet `^".$freeorkburg." `2Gold.`n
			Du kannst den Wanderhändler für `^".$lurevendor." `2Gold herlocken.`n
			Ein Dorffest kostet `^".$party." Gold`2.`n`n");
    if ($budget>=$party)
    {
        addnav("Dorffest");
        addnav("Dorffest ausrichten","dorfamt.php?op=office_budget_party");
    }
    if ($budget>=$lurevendor)
    {
        addnav("Wanderhändler");
        addnav("Herlocken","dorfamt.php?op=office_budget_lurevendor");
    }
    if ($budget>=$freeorkburg)
    {
        addnav("Weg zur Orkburg");
        addnav("Freilegen lassen","dorfamt.php?op=office_budget_orkburg");
    }
    if ($budget>=5000)
    {
        addnav("Auf den Bettelstein");
        addnav("5000 Gold","dorfamt.php?op=office_budget2&amount=5000");
        if ($budget>=10000)
        {
            addnav("10000 Gold","dorfamt.php?op=office_budget2&amount=10000");
        }
    }
    else
    {
        addnav("Wir sind pleite!");
    }
    
    $selledgems=getsetting("selledgems",0);
    $costs=(4000-3*$selledgems);
    if (($budget>=$costs && $selledgems>0) || ($amtsgems>0 && $selledgems<100))
    {
        addnav("Edelsteine");
        if ($budget>=$costs && $selledgems>0)
        {
            addnav("Kaufen","dorfamt.php?op=office_budget_buygems");
        }
        if ($amtsgems>0 && $selledgems<100)
        {
            addnav("Verkaufen","dorfamt.php?op=office_budget_sellgems");
        }
    }
    
    addnav("Zurück");
    addnav("Ins Büro","dorfamt.php?op=office_entry");
}
else if ($_GET['op']=="office_budget2")
{
    $amount=$_GET['amount'];
    $budget=getsetting("amtskasse" ,0);
    $stone=getsetting("paidgold","0");
    $max=getsetting("beggarmax","25000");
    if ($budget>=$amount)
    {
        if ($stone+$amount>$max)
        {
            $amount=$max-$stone;
            output("`2Der Bettelstein kann leider nur `^{$max}`2 Gold fassen.`n");
            if ($amount>0)
            {
                output("`2Also transferierst du lediglich `^{$amount}`2 Gold!");
            }
            else
            {
                output("`2Demnach kannst du auch nichts mehr auf ihn transferieren.");
            }
        }
        else
        {
            output("`2Du transferierst `2{$amount}`^ Gold auf den Bettelstein.");
        }
        
        if ($amount>0)
        {
            addnews("`@Armenspeisung!`& {$session['user']['name']}`2 hat soeben `^{$amount}`2 Gold auf den Bettelstein transferiert.");
            savesetting("amtskasse",$budget-$amount);
            savesetting("paidgold",$stone+$amount);
        }
    }
    else
    {
        output("Hoppla, das können wir uns aber gerade überhaupt nicht leisten.");
    }
    addnav("Zurück");
    addnav("Zur Kasse","dorfamt.php?op=office_budget");
}
else if ($_GET['op']=="office_budget_party")
{
    $amtskasse = getsetting("amtskasse", 0);
    $min_party_level = getsetting("min_party_level", 500000);
    $lastparty = getsetting("lastparty", 0);
    $party_duration= getsetting("party_duration", 1);
    if ($amtskasse>=$min_party_level)
    {
        savesetting("amtskasse",$amtskasse- $min_party_level);
        savesetting("lastparty",time()+86400*$party_duration);
        output("`2So sei es! Möge das Dorffest beginnen!");
        addnews("`&{$session['user']['name']}
        `^ hat heute ein Dorffest veranstaltet!");
    }
    else
    {
        output("Hoppla, das können wir uns aber gerade gar nicht leisten.");
    }
    addnav("Zurück");
    addnav("Zur Kasse","dorfamt.php?op=office_budget");
}
else if ($_GET['op']=="office_budget_lurevendor")
{
    $budget=getsetting("amtskasse" ,0);
    $lurevendor=getsetting("lurevendor","40000");
    $vendor=getsetting("vendor","0");
    if ($budget>=$lurevendor)
    {
        if ($vendor==1)
        {
            output("`2Nicht nötig, er ist doch schon da.`n
					Oder willst du ihm etwa die hart verdienten Steuergelder auch noch in den Rachen werfen?`n`n");
        }
        else
        {
            output("`2Du schickst deinen schnellsten Boten in die Nachbardörfer und bietest dem Wanderhändler `^{$lurevendor}`2 Gold an, wenn er sich sofort auf deinem Marktplatz blicken lässt.`n
					Das Angebot lässt er sich natürlich nicht zweimal machen.");
            savesetting("amtskasse",$budget-$lurevendor);
            savesetting("vendor","1");
            addnews("`&{$session['user']['name']}
            `^ hat den Wanderhändler ins Dorf gelockt!");
        }
    }
    else
    {
        output("Hoppla, das können wir uns jetzt aber gar nicht leisten...");
    }
    addnav("Zurück");
    addnav("Zur Kasse","dorfamt.php?op=office_budget");
}
else if ($_GET['op']=="office_budget_orkburg")
{
    $budget=getsetting("amtskasse" ,0);
    $freeorkburg=getsetting("freeorkburg","30000");
    $orkburg=getsetting("dailyspecial","Keines");
    if ($budget>=$lurevendor)
    {
        if ($orkburg=="Orkburg")
        {
            output("`2Nicht nötig, der Weg ist gut freigetreten.`n
					Oder willst du die hart verdienten Steuergelder unnötig an Waldarbeiter verfeuern?`n`n");
        }
        else
        {
            output("`2Du schickst eine Horde Waldarbeiter mit den `^{$freeorkburg}`2 Gold zum Toilettenhäuschen, die sich in Windeseile durch das Unterholz hacken und einen schönen, breiten Weg zur Orkburg freilegen.`n
					Leider wird dieser schon morgen wieder total zugewuchert sein.");
            savesetting("amtskasse",$budget-$freeorkburg);
            savesetting("dailyspecial","Orkburg");
            addnews("`&{$session['user']['name']}
            `^ hat den Weg zur Orkburg freilegen lassen!");
        }
    }
    else
    {
        output("Hoppla, das können wir uns jetzt aber gar nicht leisten...");
    }
    addnav("Zurück");
    addnav("Zur Kasse","dorfamt.php?op=office_budget");
}
else if ($_GET['op']=="office_budget_buygems")
{
    $budget=getsetting("amtskasse" ,0);
    $amtsgems=getsetting("amtsgems","0");
    $selledgems=getsetting("selledgems",0);
    $costs=(4000-3*$selledgems);
    $maxgems=getsetting("maxamtsgems","100");
    $spaceleft=$maxgems-$amtsgems;
    output("<form action='dorfamt.php?op=office_budget_buygems2' method='POST'>`2Die Zigeunerin hat derzeit `^{$selledgems}`2 Edelsteine auf Lager, zu einem Preis von jeweils `^{$costs} `2Gold.`n",true);
    output("`2Wieviele Edelsteine hättest du gern? (Die Tresore fassen noch {$spaceleft}
    Edelsteine)<input id='input' name='amount' width=4> <input type='submit' class='button' value='kaufen'>`n</form>",true);
    output("<script language='javascript'>document.getElementById('input').focus();</script>",true);
    addnav("","dorfamt.php?op=office_budget_buygems2");
    addnav("Doch nichts kaufen","dorfamt.php?op=office_budget");
}
else if ($_GET['op']=="office_budget_buygems2")
{
    $budget=getsetting("amtskasse" ,0);
    $amtsgems=getsetting("amtsgems","0");
    $selledgems=getsetting("selledgems",0);
    $costs=(4000-3*$selledgems);
    $maxgems=getsetting("maxamtsgems","100");
    $spaceleft=$maxgems-$amtsgems;
    $_POST['amount']=floor((int)$_POST['amount']);
    
    if ($_POST['amount']<0)
    {
        output("`2Du kannst auf diese Art keine Edelsteine verkaufen!");
    }
    else if ($_POST['amount']==0)
    {
        output("`2Du entscheidest dich doch nichts zu kaufen.");
    }
    else if ($_POST['amount']>$selledgems)
    {
        output("`2So viele Edelsteine hat die Zigeunerin im Moment nicht.");
    }
    else if (($_POST['amount']*$costs)>$budget)
    {
        output("`2Das übersteigt deine finanziellen Fähigkeiten!");
    }
    else if ($_POST['amount']>$spaceleft)
    {
        output("`2So viele Edelsteine können die Tresore leider nicht mehr fassen!");
    }
    else
    {
        $amount=$_POST['amount'];
        output("`2Du kaufst `^{$amount} `2Edelsteine von der Zigeunerin und deponierst sie in den Tresoren.");
        $selledgems-=$amount;
        if ($selledgems>0)
        {
            savesetting("selledgems",$selledgems);
        }
        else
        {
            savesetting("selledgems","0");
        }
        $amtsgems+=$amount;
        savesetting("amtsgems",$amtsgems);
        $budget-=$amount*$costs;
        savesetting("amtskasse",$budget);
    }
    addnav("Zurück","dorfamt.php?op=office_budget");
}
else if ($_GET['op']=="office_budget_sellgems")
{
    $budget=getsetting("amtskasse","0");
    $amtsgems=getsetting("amtsgems","0");
    $selledgems=getsetting("selledgems","0");
    $spaceleft=100-$selledgems;
    $scost=(3000-$selledgems);
    output("<form action='dorfamt.php?op=office_budget_sellgems2' method='POST'>`2Die Zigeunerin hat derzeit `^{$selledgems}`2 Edelsteine auf Lager und kauft bis zu `^{$spaceleft}`2 weitere Steine zu einem Preis von jeweils `^{$scost} `2Gold an.`n",true);
    output("`2Wieviele Edelsteine willst du verkaufen? (Du hast noch {$amtsgems}
    Edelsteine)<input id='input' name='amount' width=4> <input type='submit' class='button' value='verkaufen'>`n</form>",true);
    output("<script language='javascript'>document.getElementById('input').focus();</script>",true);
    addnav("","dorfamt.php?op=office_budget_sellgems2");
    addnav("Doch nichts kaufen","dorfamt.php?op=office_budget");
}
else if ($_GET['op']=="office_budget_sellgems2")
{
    $budget=getsetting("amtskasse","0");
    $amtsgems=getsetting("amtsgems","0");
    $selledgems=getsetting("selledgems","0");
    $scost=(3000-$selledgems);
    $spaceleft=100-$selledgems;
    $_POST['amount']=floor((int)$_POST['amount']);
    
    if ($_POST['amount']<0)
    {
        output("`2Du kannst auf diese Art keine Edelsteine kaufen!");
    }
    else if ($_POST['amount']==0)
    {
        output("`2Du entscheidest dich doch nichts zu verkaufen.");
    }
    else if ($_POST['amount']>$spaceleft)
    {
        output("`2So viele Edelsteine will die Zigeunerin im Moment nicht.");
    }
    else if ($_POST['amount']>$amtsgems)
    {
        output("`2So viele Edelsteine hast du gar nicht!");
    }
    else
    {
        $amount=$_POST['amount'];
        output("`2Du verkaufst der Zigeunerin `^{$amount} `2Edelsteine.");
        $selledgems+=$amount;
        savesetting("selledgems",$selledgems);
        $amtsgems-=$amount;
        if ($amtsgems>0)
        {
            savesetting("amtsgems",$amtsgems);
        }
        else
        {
            savesetting("amtsgems","0");
        }
        $budget+=$amount*$scost;
        savesetting("amtskasse",$budget);
    }
    addnav("Zurück","dorfamt.php?op=office_budget");
}
else if ($_GET['op']=="office_budget_award")
{
    $budget=getsetting("amtskasse","0");
    
    
    $_POST['amount']=floor((int)$_POST['amount']);
    
    if ($_POST['amount']<0)
    {
        output("`2Du kannst auf diese Art keine Edelsteine kaufen!");
    }
    else if ($_POST['amount']==0)
    {
        output("`2Du entscheidest dich doch nichts zu verkaufen.");
    }
    else if ($_POST['amount']>$spaceleft)
    {
        output("`2So viele Edelsteine will die Zigeunerin im Moment nicht.");
    }
    else if ($_POST['amount']>$amtsgems)
    {
        output("`2So viele Edelsteine hast du gar nicht!");
    }
    else
    {
        $amount=$_POST['amount'];
        output("`2Du verkaufst der Zigeunerin `^{$amount} `2Edelsteine.");
        $selledgems+=$amount;
        savesetting("selledgems",$selledgems);
        $amtsgems-=$amount;
        if ($amtsgems>0)
        {
            savesetting("amtsgems",$amtsgems);
        }
        else
        {
            savesetting("amtsgems","0");
        }
        $budget+=$amount*$scost;
        savesetting("amtskasse",$budget);
    }
    addnav("Zurück","dorfamt.php?op=office_budget");
}
else if ($_GET['op']=="dame1")
{
    output("`&Du schaust dich ein wenig in den Vorzimmern der hohen Herren um und entdeckst, hübsch geschminkt und über und über mit Ringen, Ketten und Broschen behangen, das furchteinflößendsde und gefährlichste Wesen, dass dir je begegnet ist : `^die Vorzimmerdame`&!`n");
    output("`&Sie ist es, die in vornehmen Kreisen die neuesten Gerüchte an den Mann bringt und dabei auch gut und gern ihr schlechtes Gedächtnis mit ihrer Phantasie unterstützt.`nDir bleibt fast das Herz stehen, als sie dich ansieht und erwartungsvoll mit den Wimpern klimpert.");
    addnav("Was nun ?");
    addnav("Ansehen steigern","dorfamt.php?op=dame2");
    addnav("Gerüchte streuen","dorfamt.php?op=dame3");
    $inc = user_get_aei("incommunity");
    
    if ($session['user']['dragonkills'] >= getsetting("ci_dk",1) && //nötige dk-anzahl erreicht?
    !$inc['incommunity'] && //noch nicht eingetragen?
    $session['user']['superuser'] >= getsetting("ci_su",0)&& // muss dann wieder weg
    getsetting("ci_active",0) //forumszeug aktiv?
    )
    {
        addnav("Passierschein A38","dorfamt.php?op=passier1");
    }
    addnav("Laufen!","dorfamt.php");
}
else if ($_GET['op']=="dame2")
{
    output("Nachdem du der Vorzimmerdame mitgeteilt hast, dass du gern ein wenig beliebter wärst und dass dich keiner so richtig leiden kann, wischt sie sich demonstrativ ein Tränchen von der Wange und schaut dich an. \"`#Na das dürfte nicht allzu schwer sein. Ich kann den Leuten ja mal erzählen was für ein tolle".($session['user']['sex']?"s Mädel ":"r Bursche ")."du bist.`nSo etwas aber seinen Preis... Einen Edelstein für zwei nette Heldengeschichten !\"`n`n");
    output("`&Wieviele Edelsteine willst du ihr geben?");
    output("<form action='dorfamt.php?op=dame21' method='POST'><input name='buy' id='buy'><input type='submit' class='button' value='Geben'></form>",true);
    output("<script language='JavaScript'>document.getElementById('buy').focus();</script>",true);
    addnav("","dorfamt.php?op=dame21");
    addnav("Lieber doch nicht","dorfamt.php?op=dame1");
}
else if ($_GET['op']=="dame21")
{
    $buy = $_POST['buy'];
    if (($buy>$session['user']['gems']) || ($buy<1))
    {
        output("`&Na das ging nach hinten los... Du bietest ihr Edelsteine an, die du nicht hast. In der Hoffnung, dass sie nun keine Gerüchte über deine Armut streut, eilst du davon.");
        addnav("Weg hier!","village.php");
    }
    else
    {
        $eff=$buy*2;
        output("`&Die Dame lässt deine $buy Edelsteine in ihrem Handtäschchen verschwinden und lächelt dich an. Dein Ansehen steigt um $eff.`n");
        $session['user']['gems']-=$buy;
        if ($buy>4)
        {
            debuglog("Gibt $amt Edelsteine im Dorfamt für Ansehen.");
        }
        $session['user']['reputation']+=$eff;
        if ($session['user']['reputation']>50)
        {
            $session['user']['reputation']=50;
        }
        addnav("Zurück","dorfamt.php?op=dame1");
    }
}
else if ($_GET['op']=="dame3")
{
    output("`&Die Frau schaut dich an. \"`#Sooo... und um wen geht es?`&\" fragt sie.`n`n");
    
    if ($_GET['who']=="")
    {
        addnav("Äh.. um niemanden!","dorfamt.php");
        if ($_GET['subop']!="search")
        {
            output("<form action='dorfamt.php?op=dame3&subop=search' method='POST'><input name='name'><input type='submit' class='button' value='Suchen'></form>",true);
            addnav("","dorfamt.php?op=dame3&subop=search");
        }
        else
        {
            addnav("Neue Suche","dorfamt.php?op=dame3");
            $search = "%";
            for ($i=0; $i<strlen($_POST['name']); $i++)
            {
                $search.=substr($_POST['name'],$i,1)."%";
            }
            $sql = "SELECT name,alive,location,sex,level,reputation,laston,loggedin,login FROM accounts WHERE (locked=0 AND name LIKE '$search') ORDER BY level DESC";
            $result = db_query($sql) or die(db_error(LINK));
            $max = db_num_rows($result);
            if ($max > 50)
            {
                output("`n`n\"`#Na... damit könnte ja jeder gemeint sein..`&`n");
                $max = 50;
            }
            output("<table border=0 cellpadding=0><tr><td>Name</td><td>Level</td></tr>",true);
            for ($i=0; $i<$max; $i++)
            {
                $row = db_fetch_assoc($result);
                output("<tr><td><a href='dorfamt.php?op=dame3&who=".rawurlencode($row['login'])."'>$row[name]</a></td><td>$row[level]</td></tr>",true);
                addnav("","dorfamt.php?op=dame3&who=".rawurlencode($row['login']));
            }
            output("</table>",true);
        }
    }
    else
    {
        
        $sql = "SELECT acctid,login,name,reputation FROM accounts WHERE login='".$_GET['who']."'";
        $result = db_query($sql) or die(db_error(LINK));
        if (db_num_rows($result)>0)
        {
            $row = db_fetch_assoc($result);
            
            output("`&Die Vorzimmerdame lächelt. \"`#Aber natürlich! ".($row['name'])." `#! Der Name ist mir ein Begriff... Ich denke dass ich sicherlich ein paar alte Geschichten bekannt werden lassen kann.`nDie Leute sollen ruhig wissen mit wem sie es da zu tun haben! Aber... die Sache wird nicht ganz billig werden, denn ich muss sehr viel in den Akten suchen... und...so.`nZwei kleine Gerüchte würde einen Edelsteine kosten..\"`&`n`n");
            output("`n`&Wieviele Edelsteine willst du ihr geben?");
            output("<form action='dorfamt.php?op=dame31&who=".rawurlencode($row['login'])."' method='POST'><input name='buy' id='buy'><input type='submit' class='button' value='Geben'></form>",true);
            output("<script language='JavaScript'>document.getElementById('buy').focus();</script>",true);
            addnav("","dorfamt.php?op=dame31&who=".rawurlencode($row['login'])."");
            addnav("Lieber doch nicht","dorfamt.php?op=dame1");
        }
        else
        {
            output("\"`#Ich kenne niemanden mit diesem Namen.`&\"");
        }
    }
}
else if ($_GET['op']=="dame31")
{
    $buy = $_POST['buy'];
    $sql = "SELECT acctid,name,reputation,login,sex FROM accounts WHERE login='".$_GET['who']."'";
    $result = db_query($sql) or die(db_error(LINK));
    if (db_num_rows($result)>0)
    {
        $row = db_fetch_assoc($result);
        
        if (($buy>$session['user']['gems']) || ($buy<1))
        {
            output("`&Na das ging nach hinten los... Du bietest ihr Edelsteine an, die du nicht hast. In der Hoffnung, dass sie nun keine Gerüchte über DICH verstreut, eilst du davon.");
            addnav("Weg hier!","village.php");
        }
        else
        {
            $eff=$buy*2;
            output("`&Die Dame lässt deine $buy Edelsteine in ihrem Handtäschchen verschwinden und lächelt dich an. Das Ansehen von ".($row['name'])."`& sinkt um $eff.`n");
            $session['user']['gems']-=$buy;
            if ($buy>4)
            {
                debuglog("Gibt $amt Edelsteine im Dorfamt für Gerüchte.");
            }
            $rep=$row['reputation']-$eff;
            if ($rep<-50)
            {
                $rep=-50;
            }
            
            $sql = "UPDATE accounts SET reputation=$rep WHERE acctid = ".$row['acctid']."";
            db_query($sql) or die(sql_error($sql));
            
            $chance=e_rand(1,3);
            if ($chance==1)
            {
                systemmail($row['acctid'],"`$Gerüchte!`0","`@{$session['user']['name']}`& hat die Vorzimmerdame im Dorfamt bestochen, damit diese üble Gerüchte über dich verbreitet! Dein Ansehen ist um $eff Punkte gesunken! Willst du dir sowas gefallen lassen ?");
            }
            else
            {
                systemmail($row['acctid'],"`$Gerüchte!`0","`&Jemand hat die Vorzimmerdame im Dorfamt bestochen, damit diese üble Gerüchte über dich verbreitet! Dein Ansehen ist um $eff Punkte gesunken! Willst du dir sowas gefallen lassen ?");
            }
            if ($buy >= 5)
            {
                $news="`@Gerüchte besagen, dass `^".$row['name']."";
                switch (e_rand(1,15))
                {
                case 1 :
                    $news=$news." `@heimlich in der Nase bohrt!";
                    break;
                case 2 :
                    $news=$news." `@nicht ohne ".($row['sex']?"ihr":"sein")." Kuscheltier einschlafen kann!";
                    break;
                case 3 :
                    $news=$news." `@etwas mit ".($row['sex']?"Violet":"Seth")." am Laufen haben soll!";
                    break;
                case 4 :
                    $news=$news." `@ganz übel aus dem Mund riechen soll.";
                    break;
                case 5 :
                    $news=$news." `@mehr Haare ".($row['sex']?"an den Beinen ":"auf dem Rücken ")."haben soll als ein Bär!";
                    break;
                case 6 :
                    $news=$news." `@sich regelmäßig am Bettelstein bedienen soll!";
                    break;
                case 7 :
                    $news=$news." `@sich bei Angst die Hosen vollmachen soll!";
                    break;
                case 8 :
                    $news=$news." `@im Bett eine Niete sein soll!";
                    break;
                case 9 :
                    $news=$news." `@für Geld die Hüllen fallen lassen soll!";
                    break;
                case 10 :
                    $news=$news." `@ein Alkoholproblem haben soll!";
                    break;
                case 11 :
                    $news=$news." `@Angst im Dunkeln haben soll!";
                    break;
                case 12 :
                    $news=$news." `@einen Hintern wie ein Ackergaul haben soll!";
                    break;
                case 13 :
                    $news=$news." `@sehr oft bitterlich weinen soll!";
                    break;
                case 14 :
                    $news=$news." `@eine feuchte Aussprache haben soll!";
                    break;
                case 15 :
                    $news=$news." `@eine Perücke tragen soll!";
                    break;
                }
                
                // In die News und in die Bio des Opfers
                $sql = "INSERT INTO news(newstext,newsdate,accountid) VALUES ('".addslashes($news)."',NOW(),".$row['acctid'].")";
                db_query($sql) or die(sql_error($sql));
            }
            addnav("Zurück","dorfamt.php?op=dame1");
        }
        
    }
}
else if ($_GET['op']=='passier1')
{
    output('Möchtest du den Schein wirklich beantragen?');
    addnav('Ja, klar!','dorfamt.php?op=passier2');
    addnav('Nein!','dorfamt.php');
}
else if ($_GET['op']=='passier2')
{
    include_once(LIB_PATH.'communityinterface.lib.php');
    $aUser = array();
    $aUser[ 0 ] = array('id'	=> $session['user']['acctid'],
						'name'	=> $session['user']['login'],
						'pass'	=> $session['user']['password'],
						'mail'	=> $session['user']['emailaddress']
						);
    
    
    if (ci_importusers($aUser) )
    {
        $count = db_fetch_assoc(db_query("SELECT COUNT(incommunity) AS cinc FROM account_extra_info WHERE incommunity<>0"));
        $count = $count['cinc'];
        
        $out  = '`2Die Dame Stempelt die Nummer '.$count.' auf ein grünes Blatt Papier und drückt es Dir in die Hand. "`@Bitte sehr.`2"';
        $out .= '`n`nDu ließt dir die Angaben durch:`n';
        $out .= '`n<big>`7Passierschein A38</big>';
        $out .= '`n`&Nummer: #'.$count;
        $out .= '`nAntragssteller: '.$session['user']['name'];
        $out .= '`n`&Zugangsname: '.$session['user']['login'];
        $out .= '`nZugangspasswort: ';
        if (!getsetting('ci_std_pw_active',0) )
        {
            $out .= '`n`2Du schaust das Feld erschrocken an und wendest Dich der Dame zu: `n"`@Entschuldigt, aber Sie haben ein Feld vergessen.`2"';
            $out .= '`nSie schaut Dich an: `n"`@Dieses Feld wird aus Sicherheitsgründen nicht ausgefüllt und ausserdem wisst Ihr es schon!`2"';
            $out .= '`nDu wendest Dich wieder dem Formular zu "`@Aha....`2" und ließt:';
        }
        else
        {
            $out .= getsetting('ci_std_pw','');
        }
        $out .= '`n`&Portal: forum.atrahor.de';
        $out .= '`n<big><big><big>`4`bGENEHMIGT</big></big></big>';
        debuglog('hat sich ins froum eingetragen!');
    }
    else
    {
        $out = '`2Die Dame Schaut Dich an: "`@Es tut mir leid. Ich habe keine Formulare zur Zeit`2"';
    }
    
    output($out);
    addnav('Zurück','dorfamt.php');
}
else if ($_GET['op']=="steuernzahlen")
{
    output("\"`@Steuern zahlen könnt Ihr dritten Gang rechts...\"`n
`2Als Du zu einem kleinen alten Mann kommst, blickt dieser auf und sagt:`n
`@\"Also du willst steuern Zahlen?`n
Hm, ich guck ma deine Akte durch! Moment bitte...Da ist sie ja\"`n");
    
    if ($session['user']['marks']<31)
    {
        
        output("`^Privatakte...`n`n");
        output("`2Name: `^".$session['user']['name']."`n");
        output("`2Alter: `^".$session['user']['age']."`^ Tage`n");
        output("`2Level: `^".$session['user']['level']."`n`n");
        
        output("`^Sonstige Informationen...`n`n");
        output("`2Gold: `^".$session['user']['gold']."`n");
        output("`2Edelsteine: `^".$session['user']['gems']."`n");
        output("`2Gold auf der Bank: `^".$session['user']['goldinbank']."`n");
        
        addnav("Steuern");
        if ($session['user']['level']>=5)
        {
            addnav("Steuern zahlen","dorfamt.php?op=steuernzahlen_ok");
        }
        
    }
    else
    {
        output("`n`n`2Der alte Mann lächelt dich plötzlich ganz fürsorglich an und sagt:`n");
        output("	`@\"Euren Großmut in Ehren, aber Auserwählte zahlen keine Steuern...\"`n");
    }
    addnav("Wege");
    addnav("Zurück","dorfamt.php");
}
else if ($_GET['op']=="steuernzahlen_ok")
{
    $taxrate=getsetting("taxrate",750);
    
    $cost = ($session['user']['level'] >= 11) ? $taxrate*2 : $taxrate;
    
    if ($cost>0)
    {
        
        if ($session['user']['steuertage']<=1)
        {
            if ($session['user']['gold']>=$cost)
            {
                output("`2Du zahlst deine `^".$cost." Gold`2 ein!`n
				`^Wenigstens einer der die Steuern hier bezahlt...`n
				`2Der Kassier grinst dich an und verabschiedet dich! ");
                $session['user']['gold']-=$cost;
                savesetting("amtskasse" ,getsetting("amtskasse",0)+ $cost);
                
            }
            else
            {
                output("`2Der Mann sagt: `^Du hast nicht genug Gold dabei, wie willst Du da die ".$cost." zahlen?`n");
                output("`^Gut, dann nehmen wir halt etwas von der Bank, hm?`n");
                if ($session['user']['goldinbank']<$cost)
                {
                    output("`^Auch nicht? Dann halt Edelsteine!`n");
                    if ($session['user']['gems']<1)
                    {
                        output("`^Du armer Tropf, Du hast ja gar nichts! Na gut, dieses mal sehe ich noch darüber hinweg! Troll Dich`n");
                    }
                    else
                    {
                        output("`^Na wenigstens etwas...jetzt troll Dich!`n");
                        $session['user']['gems']--;
                        savesetting("amtskasse" ,getsetting("amtskasse",0)+ $cost);
                    }
                }
                else
                {
                    output("`^Na wenigstens etwas...jetzt troll Dich!`n");
                    $session['user']['goldinbank']-=$cost;
                    savesetting("amtskasse" ,getsetting("amtskasse",0)+ $cost);
                }
                
            }
            // END nicht genug Gold in Hand
            
            debuglog('zahlte Steuern');
            if (getsetting("amtskasse","0")>getsetting("maxbudget","2000000"))
            {
                savesetting("amtskasse",getsetting("maxbudget","2000000"));
            }
            
            $session['user']['steuertage']=7;
            
        }
        else
        {
            output("`2Der Mann sagt: `^Du brauchst heute noch keine Steuern zahlen");
        }
    }
    else
    {
        output("`^Derzeit werden keine Steuern erhoben!`0`n");
        $session['user']['steuertage']=7;
    }
    addnav("Zurück","dorfamt.php");
}

//If there is enough money in the bank the party expiration date will be set
/**$amtskasse = getsetting("amtskasse", 0);
$min_party_level = getsetting("min_party_level", 500000);
$lastparty = getsetting("lastparty", 0);
$party_duration= getsetting("party_duration", 1);
if ($amtskasse>$min_party_level)
{
    savesetting("amtskasse",$amtskasse- $min_party_level);
    savesetting("lastparty",time()+86400*$party_duration);
}
**/
page_footer();
?>
