<?php



// 15082004



require_once "common.php";

su_check(SU_RIGHT_GAMEOPTIONS,true);



if ($_GET[op]=="save"){

    if ($_POST[blockdupeemail]==1) $_POST[requirevalidemail]=1;

    if ($_POST[requirevalidemail]==1) $_POST[requireemail]=1;

    reset($_POST);

    while (list($key,$val)=each($_POST)){

        savesetting($key,stripslashes($val));

        output("Setze $key auf ".stripslashes($val)."`n");

    }

}



page_header("Spieleinstellungen");

addnav("G?Zurück zur Grotte","superuser.php");



addnav('W?Zurück zum Weltlichen',$session['su_return']);

addnav("",$REQUEST_URI);

//$nextnewday = ((gametime()%86400))/4 ; //abs(((86400- gametime())/getsetting("daysperday",4))%86400 );

//echo date("h:i:s a",strtotime("-$nextnewday seconds"))." (".($nextnewday/60)." minutes) ".date("h:i:s a",gametime()).gametime();

$time = (strtotime(date("1981-m-d H:i:s",strtotime(date("r")."-".getsetting("gameoffsetseconds",0)." seconds"))))*getsetting("daysperday",4) % strtotime("1981-01-01 00:00:00");

$time = gametime();

/*

$tomorrow = strtotime(date("Y-m-d H:i:s",$time)." + 1 day");

$tomorrow = strtotime(date("Y-m-d 00:00:00",$tomorrow));

$today = strtotime(date("Y-m-d 00:00:00",$time));

$dayduration = ($tomorrow-$today) / getsetting("daysperday",4);

$secstotomorrow = $tomorrow-$time;

$secssofartoday = $time - $today;

$realsecstotomorrow = $secstotomorrow / getsetting("daysperday",4);

$realsecssofartoday = $secssofartoday / getsetting("daysperday",4);

*/

$tomorrow = mktime(0,0,0,date('m',$time),date('d',$time)+1,date('Y',$time));

$today = mktime(0,0,0,date('m',$time),date('d',$time),date('Y',$time));

$dayduration = ($tomorrow-$today) / getsetting("daysperday",4);

$secstotomorrow = $tomorrow-$time;

$secssofartoday = $time - $today;

$realsecstotomorrow = round($secstotomorrow / getsetting("daysperday",4),0);

$realsecssofartoday = round($secssofartoday / getsetting("daysperday",4),0);

$enum="enum";

for ($i=0;$i<=86400;$i+=900){

    $enum.=",$i,".((int)($i/60/60)).":".($i/60 %60)."";

}

$weather_enum = 'radio';
foreach($weather as $id=>$w) {
	$w['name'] = str_replace(',','',$w['name']);
	$weather_enum.=','.$id.','.$w['name'].'.';
}

$setup = array(

    "Spieleinstellungen,title",
	
	"wartung"=>'Wartungsmodus an,bool|?Um einzelne Accounts für den Wartungsmodus freizuschalten, kannst du die Rechtesektion im Usereditor verwenden.',
			
    "blocknewchar"=>"Neuanmeldungen sperren?,bool",

    "loginbanner"=>"Login Banner (unterhalb der Login-Aufforderung; 255 Zeichen)",

    "impressum"=>"Server betrieben von: (255 Zeichen)",
	
	"defaultskin"=>"Standardskin ( + .htm(l) )",
	
	"townname"=>"Name des Dorfes:",

    "soap"=>"Userbeiträge säubern (filtert Gossensprache und trennt Wörter mit über 45 Zeichen),bool",

    "maxonline"=>"Maximal gleichzeitig online (0 für unbegrenzt),int",

    "maxcolors"=>"Maximale # erlaubter Farbwechsel in Userkommentaren,int",

	"longbiomaxlength"=>"Maximale Zeichenanzahl d. longbio,int",

    "gameadminemail"=>"Admin Email",
	
	"petitionemail"=>"Anfragen Email (Absender)",
		
    "paypalemail"=>"E-Mail Adresse für den PayPal Account des Admins",
	
	"emailonmail"=>"Email-Benachrichtigung bei YeOlde Eingang,bool",

    "defaultlanguage"=>"Voreingestellte Sprache (z. Zt nur de),enum,en,English,dk,Danish,de,Deutsch,es,Espanol,fr,French",

    "forum"=>"Link (URL) zum Forum",

    "automaster"=>"Meister jagt säumige Lehrlinge,bool",
	
    "multimaster"=>"Meister kann mehrmals pro Tag herausgefordert werden?,bool",

    "topwebid"=>"ID für Top Web Games (wenn du dort registriert bist),int",

    "beta"=>"Beta-Features für alle Spieler aktivieren?,bool",

    "paidales"=>"Ale das als 'Runde' spendiert wurde (Wert-1),int",
    
    "dragonmind_game"=>"Dragonmind Spiel aktivieren,bool",

    "memory_game"=>"Memory Spiel aktivieren,bool",

    "maxales"=>"Maximale Anzahl Ale die bei einer 'Runde' spendiert werden kann,int",

    "limithp"=>"Lebenpunkte maximal Level*12+5*DPinHP+x*DK (0=deaktiviert),int",

    "autofight"=>"Automatische Kampfrunden ermöglichen,bool",

    "witchvisits"=>"Erlaubte Besuche bei der Hexe,int",
    
    "max_symp"=>"Vergebbare Sympathiepunkte pro Monat,int",

    "dailyspecial"=>"Heutiges besonderes Ereignis",
	
	"enable_commentemail"=>"User dürfen Chatmitschnitte an ihre Mail senden,bool",
	
	"enable_modcall"=>"'Mod rufen'-Button unter Chats anbieten,bool",
	
    "Expedition,title",

    "DDL_new_order"=>"DDL-Lagenwechsel nach spätestens x Tagen,int",
	
    "DDL_balance_malus"=>"DDL-Punkteabzug pro Tag,int",
    
    "DDL_balance_push"=>"DDL-Punkteschwelle um Lageänderunge herbeizuführen,int",
    
    "DDL_balance_win"=>"DDL-Punkteschwelle damit Angriff gelingt,int",
    
    "DDL_balance_lose"=>"DDL-Negativpunkteschwelle zur Niederlage,int",
    
    "DDL-restart"=>"DDL-Lager nach x Tagen erneuern,int",
    
    "DDL_comments_req"=>"DDL-Anzahl der Posts in der Einöde bis neue Gegner erscheinen,int",
    
    "Büro des Fürsten,title",
    
    "taxrate"=>"Derzeitiger Steuersatz,int",
    
    "mintaxes"=>"Mindeststeuersatz,int",

    "maxtaxes"=>"Höchstmöglicher Steuersatz,int",
    
    "taxprison"=>"Derzeitige Anzahl Kerkertage für Steuerhinterziehung,int",
    
    "maxprison"=>"Höchststrafe für Steuerhinterziehung,int",
    
    "callvendormax"=>"Fürst kann wie oft in seiner Amtszeit den Wanderhändler holen,int",
    
    "beggarmax"=>"Maximales Fassungsvermögen des Bettelsteins,int",
    
    "maxbudget"=>"Maximale Größe der Staatskasse,int",
    
    "maxamtsgems"=>"Maximale Anzahl an Edelsteinen in den Tresoren,int",
    
    "lurevendor"=>"Kosten um den Wanderhändler anzulocken,int",
    
    "freeorkburg"=>"Kosten um die Orkburg freizulegen,int",

    "Account Erstellung,title",

    "superuser"=>"Default superuser level,enum,0,Keine Rechte,1,Zutritt zur Grotte,2,Admin creatures and taunts,3,Admin users",

    "newplayerstartgold"=>"Gold mit dem ein neuer Char startet,int",

    "requireemail"=>"E-Mail Adresse beim Anmelden verlangen,bool",

    "requirevalidemail"=>"E-Mail Adresse bestätigen lassen,bool",

    "blockdupeemail"=>"Nur ein Account pro E-Mail Adresse,bool",

    "spaceinname"=>"Erlaube Leerzeichen in Benutzernamen,bool",

    "selfdelete"=>"Erlaube den Spielern ihren Charakter zu löschen,bool",

    "avatare"=>"Erlaube den Spielern Avatare zu verlinken,bool",
    
    "recoveryage"=>"Tage ab denen ein Spieler täglich Extra-Erfahrung bekommt,int",
    
    "recoveryexp"=>"Anzahl der Extra-Erfahrungspunkte (*DKs) pro Tag,int",
	
    "cowardlevel"=>"Level den ein Spieler haben muss um Feigling zu werden,int",
	
	"cowardage"=>"Tageanzahl seit DK um Feigling zu werden,int",
	
	"maxagepvp"=>"Max Tageanzahl seit DK für PvP und Ruhmeshalle,int",
	
	"race_change_allowed"=>"Rassenwechsel in der Schenke erlauben,bool",
	
		    
    "Einstellungen für unsere Mods,title",

    "libdp"=>"Max. vergebbare Donationpoints pro angenommenem Buch,int",

    "numberofguards"=>"Maximale Zahl an Stadtwachen",    
	
	"numberofpriests"=>"Maximale Zahl an Priestern",
       
	"numberofwitches"=>"Maximale Zahl an Hexen",
	
	"numberofjudges"=>"Maximale Zahl an Richtern",
	
	"guardreq"=>"Nötige DKs um Stadtwache zu werden",
	
	"judgereq"=>"Nötige DKs um RIchter zu werden",
	
    "amtskasse"=>"Gold in der Amtskasse,int",
    
    "lastparty"=>"Wann war das letzte Bürgerfest",
    
    "min_party_level"=>"Wieviel Geld muss für eine Party vorhanden sein,int",
    
    "party_duration"=>"Wieviele Tage soll das Dorffest dauern (1;2;0.5;...),int",
    
    "wallchangetime"=>"Geschmiere an der Mauer kann erst nach x Sekunden geändert werden,int",
    
    "maxsentence"=>"Höchststrafe in Tagen",
    
    "locksentence"=>"Tage im Kerker ab denen es Sicherheitsverwahrung gibt",
	
	"user_rename"=>"Preis in DP für Namensänderung nach Erneuerung / Wiedergeburt",
	
	"deathjackpot"=>"Derzeitiger Stand des Tot-o-Lotto Jackpots,int",
	
	"deathjackpotmax"=>"Maximaler Stand des Tot-o-Lotto Jackpots,int",

    "Neue Tage,title",

    "fightsforinterest"=>"Höchste Anzahl an übrigen Waldkämpfen um Zinsen zu bekommen,int",

    "maxinterest"=>"Maximaler Zinssatz (%),int",

    "mininterest"=>"Minimaler Zinssatz (%),int",

    "daysperday"=>"Spieltage pro Kalendertag,int",

    "dispnextday"=>"Zeit zum nächsten Tag in Vital Info,bool",

    "specialtybonus"=>"Zusätzliche Einsätze der Spezialfertigkeit am Tag,int",

    "activategamedate"=>"Spieldatum aktiv,bool",    

    "gamedateformat"=>"Datumsformat (zusammengesetzt aus: %Y; %y; %m; %n; %d; %j)",

    "gametimeformat"=>"Zeitformat",

    

    "Wald,title",

    "turns"=>"Waldkämpfe pro Tag,int",

    "dropmingold"=>"Waldkreaturen lassen mindestens 1/4 des möglichen Goldes fallen,bool",

    "lowslumlevel"=>"Mindestlevel bei dem perfekte Kämpfe eine Extrarunde geben,int",
	
	"forestbal"=>"Prozentsatz der pro perfektem Kampf auf Monsterstärke aufgeschlagen wird",
	
	"forestdkbal"=>"Prozentsatz mit dem Drachenpunkteeinfluss auf Monsterstärke multipliziert wird",
	
	"foresthpbal"=>"Zahl durch die max. LP geteilt werden ehe sie auf DP-Einfluss addiert werden",

	
	"Schloss,title",
	
	"castle_turns_wk"=>"Anzahl an WKs die man für eine Schlossrunde erhält,int",
	
	"wk_castle_turns"=>"Anzahl an WKs die eine Schlossrunde kostet,int",
	
    "castle_turns"=>"Schlossrunden pro Tag ,int",

    "castlegemdesc"=>"Abweichung vom max. Edelsteingewinn / Runde über dem max.,int",
	
	"castlegolddesc"=>"Abweichung vom max. Goldgewinn / Runde über dem max.,int",

	
	"Gilden,title",

    "dgguildmax"=>"Max. Anzahl an Gilden,int",

    "dgguildfoundgems"=>"Gems zur Gründung,int",
	
	"dgguildfoundgold"=>"Gold zur Gründung,int",
	
	"dgguildfound_k"=>"DKs zur Gründung,int",
	
	"dgmaxmembers"=>"Max. Mitgliederzahl ohne Ausbauten,int",
	
	"dgminmembers"=>"Min. Mitgliederzahl,int",
			
	"dgplayerfights"=>"Gildenkämpfe eines Spielers pro Spieltag,int",
	
	"dgimmune"=>"Spieltage Immunität für eine neu gegründete Gilde,int",
	    
	"dggpgoldcost"=>"Kosten eines GP in Gold,int",
	
	"dgtaxdays"=>"Alle x Spieltage Steuern,int",
	
	"dgmaxtaxfails"=>"x mal Steuern nicht zahlen damit Gilde aufgelöst,int",
	
	"dgtaxgold"=>"Basis-Goldkosten der Steuer,int",
	
	"dgtaxgems"=>"Basis-Gemkosten der Steuer,int",
	
	"dgmaxgemstransfer"=>"Max. Edelsteineinzahlung pro Lvl,int",
	
	"dgmaxgoldtransfer"=>"Max. Goldeinzahlung pro Lvl,int",
	
	"dgmaxgoldin"=>"Max. Goldeinzahlung pro Spieltag,int",
	
	"dgmaxgemsin"=>"Max. Gemeinzahlung pro Spieltag,int",
	
	"dgtrsmaxgold"=>"Max Gold in Schatzkammer,int",
	
	"dgtrsmaxgems"=>"Max Gems in Schatzkammer,int",
	
	"dgminmembertribute"=>"Mindesttribut der Mitglieder in %,int",
	
	"dgmindkapply"=>"Mindestanzahl an DKs für Mitgliedschaft,int",
	
	"dgstartgold"=>"Startgold,int",
	
	"dgstartgems"=>"Startgems,int",
	
	"dgstartpoints"=>"StartGP,int",
	
	"dgstartregalia"=>"Startinsignien,int",	
	
	"dgbiomax"=>"Max. Zeichenanzahl der Bio,int",	

	
    "Kopfgeld,title",

    "bountymin"=>"Mindestbetrag pro Level der Zielperson,int",

    "bountymax"=>"Maximalbetrag pro Level der Zielperson,int",

    "bountylevel"=>"Mindestlevel um Opfer sein zu können,int",

    "bountyfee"=>"Gebühr für Dag Durnick in Prozent,int",

    "maxbounties"=>"Anzahl an Kopfgeldern die ein Spieler pro Tag aussetzen darf,int",

    

    "Handelseinstellungen,title",    

    "borrowperlevel"=>"Maximalwert den ein Spieler pro Level leihen kann (Bank),int",

    "maxinbank"=>"+/- Maximalbetrag für den noch Zinsen bezahlt/verlangt werden,int",
			
	"bankmaxgemstrf"=>"Max. Anzahl an Gemüberweisungen / Tag,int",

    "allowgoldtransfer"=>"Erlaube Überweisungen (Gold und Edelsteine),bool",

    "transferperlevel"=>"Maximalwert den ein Spieler pro Level empfangen oder nehmen kann,int",
    
    "mintransferlev"=>"Mindestlevel für Überweisungen (bei 0 DKs),int",
	
	"bankgemtrflvl"=>"Minimallvl um Edelsteinüberweisungen empfangen zu können,int",

    "maxtransferout"=>"Menge die ein Spieler an andere überweisen kann (Wert x Level),int",

    "innfee"=>"Gebühr für Expressbezahlung in der Kneipe (x oder x%),int",

    "selledgems"=>"Edelsteine die Vessa vorrätig hat,int",

    "vendor"=>"Händler heute in der Stadt?,bool",

    "paidgold"=>"Gold das in Bettlergasse spendiert wurde,int",
	
	"housemaxgemsout"=>"Max. Anzahl an Edelsteinen / Tag aus Haus entnehmbar,int",
	
    "newhouses"=>"Bauen neuer Häuser möglich ?,bool",
		
	"maxhouses"=>"Maximale Anzahl an Häusern ?,int",
			
	"housegetdks"=>"Min. DKs für Häuserbau / kauf?,int",
	
	"housekeylvl"=>"Min. Lvl (bei 0 DKs) für Schlüsselvergabe?,int",
	
	"houseextdks"=>"Min. DKs für Hausausbau?,int",
	
	"houseextsellenabled"=>"Ausgebaute Häuser zum Verkauf anbieten?,bool",
	
	"housegetdks"=>"Min. DKs für Häuserbau / kauf?,int",
	
	"housekeylvl"=>"Min. Lvl (bei 0 DKs) für Schlüsselvergabe?,int",
	
	"houseextdks"=>"Min. DKs für Hausausbau?,int",
	
	"houseextsellenabled"=>"Ausgebaute Häuser zum Verkauf anbieten?,bool",


    "Mail Einstellungen,title",

    "mailsizelimit"=>"Maximale Anzahl an Zeichen in einer Nachricht,int",

    "inboxlimit"=>"Anzahl an Nachrichten in der Inbox,int",

    "modinboxlimit"=>"Dergleichen für MODs,int",

    "oldmail"=>"Alte Nachrichten automatisch löschen nach x Tagen. x =,int",

    "modoldmail"=>"Dergleichen für MODs,int",
    
    "show_yom_contacts"=>"Zeige das Adressbuch in der YOM an,bool",
    
    "max_yom_contacts"=>"Maximale Anzahl an YOM Kontakten,int",
    
    "message2mail_activated"=>"Dürfen YoMs per Mail archiviert werden?,bool",

    

    "PvP,title",

    "pvp"=>"Spieler gegen Spieler aktivieren,bool",

    "pvpday"=>"Spielerkämpfe pro Tag,int",

    "pvpimmunity"=>"Tage die neue Spieler vor PvP sicher sind,int",

    "pvpminexp"=>"Mindest-Erfahrungspunkte für PvP-Opfer,int",

    "pvpattgain"=>"Prozentsatz der Erfahrung des Opfers den der Angreifer bei Sieg bekommt,int",

    "pvpattlose"=>"Prozentsatz an Erfahrung den der Angreifer bei Niederlage verliert,int",

    "pvpdefgain"=>"Prozentsatz an Erfahrung des Angreifers den der Verteiger bei einem Sieg gewinnt,int",

    "pvpdeflose"=>"Prozentsatz an Erfahrung den der Verteidiger bei Niederlage verliert,int",

    "pvpmindkxploss"=>"DKs Unterschied zwischen Täter und Opfer bis zu dem noch 0% XP abgezogen werden,int",



    "Inhalte löschen (0 für nie löschen),title",
	
	"lastcleanup"=>"Datetime der letzten Säuberung",
	
	"cleanupinterval"=>"Sekunden zwischen Säuberungen,int",

    "expirecontent"=>"Tage die Kommentare und News aufgehoben werden,int",

    "expiretrashacct"=>"Tage die Accounts gespeichert werden die nie eingeloggt waren,int",

    "expirenewacct"=>"Tage die Level 1 Accounts ohne Drachenkill aufgehoben werden,int",

    "expireoldacct"=>"Tage die alle anderen Accounts aufgehoben werden,int",

    "LOGINTIMEOUT"=>"Sekunden Inaktivität bis zum automatischen Logout,int",

    

    "Nützliche Informationen,title",

    "weather"=>"Heutiges Wetter:,".$weather_enum,

    "newplayer"=>"Neuster Spieler",

    "Letzter neuer Tag: ".date("h:i:s a",strtotime(date("r")."-$realsecssofartoday seconds")).",viewonly",

    "Nächster neuer Tag: ".date("h:i:s a",strtotime(date("r")."+$realsecstotomorrow seconds")).",viewonly",

    "Aktuelle Spielzeit: ".getgametime().",viewonly",

    "Tageslänge: ".($dayduration/60/60)." Stunden,viewonly",

    "Aktuelle Serveruhrzeit: ".date("Y-m-d h:i:s a").",viewonly",

    "gameoffsetseconds"=>"Offset der Spieltage,$enum",

    "gamedate"=>"aktuelles Spieldatum (Y-m-d)",

    

    "LoGD-Netz Einstellungen (file wrappers müssen in der PHP Konfiguration aktiviert sein!!),title",

    "logdnet"=>"Beim LoGD-Netz eintragen?,bool",

    "serverurl"=>"Server URL",

    "serverdesc"=>"Serverbeschreibung (255 Zeichen)",

    "logdnetserver"=>"LoGD-Netz Zentralserver (Default: http://lotgd.net)",
	
	
	
	"Forum Einstellungen,title",

    "ci_active"=>"Passierschein aktiv,bool",
	
	"ci_dk"=>"Anzahl der Dk für Passierschein?,int",
	
	"ci_su"=>"Superuser level >=,enum,0,0,1,1,2,2,3,3,4,4,5,5",

    "ci_dk_mail_active"=>"Mail bei Drachenkill?,bool",
	
	"ci_dk_mail_head"=>"Überschrift der Mail (Betreff)",

    "ci_dk_mail_text"=>"Text der Mail,textarea,30,5",
	
	"ci_std_pw_active"=>"Standard Passwort,bool",
	
	"ci_std_pw"=>"Standard Passwort (Klartext)",
	
	"ci_std_pwc"=>"Standard Passwort (so wie es eingetragen wird)"

);

    

if ($_GET[op]==""){

    loadsettings();

    output("<form action='configuration.php?op=save' method='POST'>",true);

    addnav("","configuration.php?op=save");

    showform($setup,$settings);

    output("</form>",true);

}

page_footer();

?>

