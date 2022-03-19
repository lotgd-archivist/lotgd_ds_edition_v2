<?php
/*
 * cedrick.php
 * Version:   18.09.2004
 * Author:   bibir
 * Email:   logd_bibir@email.de
 *
 * Purpose: a special for cheaper healing
 */

output("\"Keine Zeit, keine Zeit!\" Tönt es aus den Baumwipfeln
- verwirrt schaust du etwas genauer nach.
Ach ja, der Heiler - fleißig bei der Arbeit.`n");
output("`3\"`#Tut mir leid, aber ich hab absolut keine Zeit.`3\" `0
sagt er, als er kurz runterkommt und den nächsten Baum hinaufklettert.");

if(e_rand(1,100)> 70) {
    output("Er steckt dir noch ungefragt einen Coupon für Golinda zu.
    Du schaust ihn zwar fragend an, wagst es aber nicht, etwas zu sagen.
    Außerdem - so überlegst du dir - ist Golinda doch ohnehin günstiger.");
    $config = unserialize($session['user']['donationconfig']);
    $config['healer'] ++;
    $session['user']['donationconfig'] = serialize($config);
} else {
    output("`3\"`0Hier nimm das und belästige mich bloß nicht noch länger!`3\"`0`n
    Er wirft dir eine kleine Phiole,
    die ganz wie ein Heiltrank aussieht, zu. Du öffnest sie und trinkst.`n");
    if(e_rand(1,100) >50){
        output("Du regenerierst beinahe ganz.");
        if ($session['user']['hitpoints'] < $session['user']['maxhitpoints']*0.8) {
            $session['user']['hitpoints'] = $session['user']['maxhitpoints']*0.8;
        }
    } else {
        output("Dir wird schlecht und du verlierst viele Lebenspunkte
        - Da hat der Gute sich wohl vertan");
        $session['user']['hitpoints'] *= 0.3;
        if ($session['user']['hitpoints']<=1) $session['user']['hitpoints']=1;
    }
}
?>