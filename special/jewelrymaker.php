<?php

// 27062004

/*
* jewelrymaker.php - die seltsame Elfenkunst
* 
* coded by Warchild ( warchild@gmx.org )
* based on the items-table introduced by anpera
* 6/2004
* Version 0.91a dt
* Letzte Änderungen: 
* 
*/

if ($HTTP_GET_VARS[op]=="")
{
    output("`n`@Du schlenderst auf Deinem Weg an einem riesigen Baumstamm vorbei. Sprossen führen am Stamm wie eine Leiter direkt nach oben und eine `&weisse Kordel `@baumelt daneben. Du ziehst daran - irgendwo in dem Wipfel über Dir läutet eine Glocke und eine Stimme ruft: `#\"Oh, Kundschaft! Klettere nur herauf!\"`@`nDu weisst mittlererweile, dass allerhand seltsame Gestalten im Wald hausen - willst Du hinaufklettern?");
    addnav("Zum Baumhaus klettern","forest.php?op=climbtree");
    addnav("Den Ort verlassen","forest.php?op=notree");
	$session[user][specialinc]="jewelrymaker.php";
}
else if ($HTTP_GET_VARS[op]=="climbtree")
{
    $session[user][specialinc]="jewelrymaker.php";
    output("`@Sprosse für Sprosse erklimmst Du den Baum und stehst bald auf einer Art Plattform, wo Dich ein hagerer `2Elf`@ - der ein braunes Gewand trägt und seine `6goldblonden Haare`@ zu einem Pferdeschwanz nach hinten gebunden hat - begrüßt.`n");
    output("`#\"Willkommen in `!Feinfingers`# - meinem - Hause! Meine Profession ist die Schönheit, mein Leben die Ästhetik! Ich kann Dir aus Deinem `6Gold ein Kunstwerk`# schaffen, was seinesgleichen sucht. Du musst mir nur `^all Dein Gold `#geben und ich schaffe Dir etwas Unvergleichliches, etwas, das noch kein Auge je erblickt hat! Möchtest Du das?\"`n`@Du zögerst. Dein ganzes Gold?");
    addnav("Alles Gold hergeben!", "forest.php?op=givegold");
    addnav("Nix is! Ich geh!", "forest.php?op=noway");
}
else if ($HTTP_GET_VARS[op]=="givegold")
{
    // User hat schon ein "Kunstwerk" ?

    if ( item_count(' owner='.$session['user']['acctid'].' AND tpl_id="elfknst" ') >0) // User hat schon Schmuck
    {
        $session[user][specialinc]="jewelrymaker.php";
        output("`@Der Elf mustert Dich mit moosgrünen Augen durchdringend.`n`#\"Hm... ich hab doch für Dich schon ein unsterbliches Kunstwerk geschaffen! So etwas kann ich nicht zweimal tun! Ich muss Dich bitten zu gehen!\"");
        addnav("Schade!","forest.php?op=noway");
    }
    else
    {        
        if ($session[user][gold] > 0)
        {
            $session[user][specialinc]="jewelrymaker.php";
            output("`@Der Elf nimmt all Dein Gold und spricht einen Zauber darüber. Es verwandelt sich...`n`n");
            output("`6in ein wunderschönes `&Etwas `6was Du leider nicht identifizieren kannst. Aber schön ist es. Irgendwie.");
            output("`n`n`@Du nimmst das Gebilde und staunst eine Weile darüber. Dann steckst Du es ein. Vielleicht gibt Dir ja ein Händler was dafür...");
            // Goldwert randomisieren und Edelsteinwert randomisieren
            $item['tpl_gold'] = e_rand(1, $session[user][gold] * 2);
            $item['tpl_gems'] = e_rand(0,2);
			
			if (!item_add($session[user][acctid],'elfknst',true,$item)) 
            {
                output("`\$Fehler`^: Dein Inventar konnte nicht aktualisiert werden! Bitte benachrichtige den Admin.");
            }
            else // Alles ok, Gold auf 0 setzen
                $session[user][gold] = 0;
            addnav("Danke! Auf Wiedersehen!","forest.php?op=noway");
        }
        else // User pleite
        {
            $session[user][specialinc]="";
            output("`@Du willst dem Elfen gerade Deine Taschen ausleeren, da fällt Dir auf, dass Du gar kein Gold mit hast! Da Dir das peinlich ist wartest Du, bis er sich umdreht, dann flüchtest Du in den Wald zurück...");
        }
    }

}
else if ($HTTP_GET_VARS[op]=="noway")
{
    $session[user][specialinc]="";
    output("`@Du machst Dich wieder auf den Weg nach unten und verschwindest im Grün des Waldes, diesen seltsamen Elfen hast Du bald vergessen...");
    
}
else
{
    $session[user][specialinc]="";
    output("`n`@Du hast keine Lust, mühsam nach oben zu kraxeln. Was eine Zeitverschwendung! Du gehst lieber zum Monsterkillen zurück in den Wald...");
}
?>