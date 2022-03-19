<?php
if (!isset($session))
{
    exit();
}

$sql = 'SELECT ctitle,cname FROM account_extra_info WHERE acctid='.$session['user']['acctid'];
$res = db_query($sql);
$row_extra = db_fetch_assoc($res);

if ($_GET['op']=="give")
{
    if ($session[user][gems]>0)
    {
        output("`%Du gibst der Fee einen deiner schwer verdienten Edelsteine. Sie schaut ihn an, quickt vor Entz�ckung und, ");
        output("verspricht dir als Gegenleistung ein Geschenk. Sie schwebt dir �ber den Kopf und streut goldenen Feenstaub auf  ");
        output("dich herab, bevor sie davon huscht. Du stellst fest.... `n`n`^");
        $session[user][gems]--;
        //debuglog("gave 1 gem to a fairy");
        switch (e_rand(1,10))
        {
        case 1:
            output("Du bekommst einen zus�tzlichen Waldkampf!");
            $session[user][turns]++;
            break;
        case 2:
        case 3:
            output("Du f�hlst deine Sinne gesch�rft und bemerkst `%ZWEI`^ Edelsteine in der N�he!");
            $session[user][gems]+=2;
            //debuglog("found 2 gem from a fairy");
            break;
        case 4:
        case 5:
            output("Deine maximalen Lebenspunkte sind `bpermanent`b um 1 erh�ht!");
            $session[user][maxhitpoints]++;
            $session[user][hitpoints]++;
            break;
        case 6:
        case 7:
            increment_specialty();
            break;
        case 8:
        case 9:
        case 10:
            output("Dass der Staub sch�n glitzert, mehr aber nicht. Und bevor du die Fee noch einmal fragen kannst ist sie schon l�ngst verschwunden.");
            break;
        }
    }
    else
    {
        output("`%Du versprichst der Fee einen Edelstein, aber als du dein Golds�ckchen �ffnest, entdeckst du, ");
        output("da� du gar keinen Edelstein hast. Die kleine Fee schwebt vor dir, die Arme in die H�fte gestemmt und mit dem Fu� in der Luft klopfend, ");
        output("w�hrend du ihr zu erkl�ren versuchst, warum du sie angelogen hast.");
        output("`n`nAls sie genug von deinem Gestammel hat, streut sie �rgerlich etwas roten Feenstaub auf dich.  ");
        output("Du wirst ohnm�chtig und als du wieder zu dir kommst, hast du keine Ahnung, wo du bist. Du brauchst ");
        output("so viel Zeit, um den Weg zur�ck ins Dorf zu finden, da� du einen ganzen Waldkampf verlierst.");
        $session[user][turns]--;
    }
}
else if($_GET['op'] == 'dont')
{
    output("`%Du willst dich nicht von einem deiner kostbaren Edelsteine trennen und schmetterst das kleine Gesch�pf im ");
    output("Vorbeigehen auf den Boden.`n");
    
    switch (e_rand(1,25))
    {
    case 1:
    case 2:
    case 3:
    case 4:
        break;
    case 25:
        output("`%Vollkommen erbost �ber diese respektlose Handlung ");
		
		item_set_weapon('Samtpf�tchen', -1, -1, -1, 0, 1);
		item_set_armor('Kuscheliges Fell', -1, -1, -1, 0, 1);

        $session['user']['charm']++;
        if ($session[user][title]!="Flauschihase")
        {
            addnews("`@".$session['user']['name']."`@ hat heute einen unfreiwilligen Imagewandel erfahren.");
            output("schimft dir die kleine Fee hinterher und belegt dich mit einem sehr sonderbaren Fluch...");
            $newtitle="Flauschihase";
            
            $regname = ($row_extra['cname'] ? $row_extra['cname'] : $session['user']['login']);
            
            if ($session[user][title]!="")
            {
                $session['user']['title'] = $newtitle;
                $session[user][name] = $newtitle." ".$regname;
                $session[user][title] = $newtitle;
                
            }
            else
            {
                output("wirft sie dir eine M�hre an den Kopf. Autsch!");
                $session[user][hitpoints]-=5;
                addnews("`@".$session['user']['name']."`# wurde von einer �rgerlichen Fee mit einer M�hre beworfen.");
                
            }
        }
        break;
    }
}
else {

	output("`%Du begegnest einer Fee. \"`^Gib mir einen Edelstein!`%\", verlangt sie. Was tust du?");
    addnav("Gib ihr einen Edelstein","forest.php?op=give");
    addnav("Gib ihr keinen Edelstein","forest.php?op=dont");
    $session[user][specialinc]="fairy1.php";

}


?>