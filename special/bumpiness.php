<?
// idea of gargamel @ www.rabenthal.de
if (!isset($session)) exit();

if ($HTTP_GET_VARS[op]==""){
    if ( $session[user][hashorse]>0 ) {
        if ( $session[bufflist][mount][rounds] > 0 ) {
            $keep = e_rand(10,33)/100;
            output("`n`QVerflucht!`0 Auf dem Streifzug durch den Wald ist Dein ".
            $playermount[mountname]." offenbar in ein `8Loch`0 getreten. Vermutlich war es der
            Eingang zu einem Hasenbau.`n`nDu hast Mitleid mit Deinem humpelnden Tier, dass
            durch seine Verletzung `Qerheblich an Kraft verloren`0 hat.`0");
            //die sache mit dem buff
            $session[bufflist][mount][rounds] = round($session[bufflist][mount][rounds]*$keep);
            if ( $session[bufflist][mount][rounds] == 0 ) $session[bufflist][mount][rounds] = 1;
        }
        else {
            output("`nAuf deinem Streifzug durch den Wald trittst Du in ein `1Loch`0, das
            Du übersehen hast. Vermutlich der Eingang zu einem Hasenbau.`n `8Du verstauchst
            Dir den Fuss und solltest den Heiler aufsuchen.`0 Er wird Deinen
            Gesundheitsverlust mit edlen Kräuterzubereitungen ausgleichen können.`0");
            $session[user][hitpoints] = round($session[user][hitpoints]*0.95);
        }
    }
    else {        // kein Pferd
        output("`nAuf deinem Streifzug durch den Wald trittst Du in ein `1Loch`0, das
        Du übersehen hast. Vermutlich der Eingang zu einem Hasenbau.`n
        `8Du verstauchst Dir den Fuss und solltest dringend den Heiler aufsuchen.`0 Er
        wird Deinen Gesundheitsverlust mit edlen Kräuterzubereitungen ausgleichen
        können.`0");
        $session[user][hitpoints] = round($session[user][hitpoints]*0.85);
    }
}
?>