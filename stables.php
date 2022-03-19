<?php

// 24062004

require_once "common.php";
page_header("Mericks Ställe");

// Haustier-Mod by Chaosmaker <webmaster@chaosonline.de>
// http://logd.chaosonline.de

// Anpassung, Bugfixes etc bye Maris (Maraxxus@gmx.de)
// Anpassung ans neue Itemsystem by talion

function getpet($petid) {
	
	if(is_int($petid)) {
		$row = item_get(' id="'.$petid.'"' );	
	}
	else {
		$row = item_get_tpl(' tpl_id="'.$petid.'"' );
	}
		
	if ($row['tpl_id']!='') {
		return $row;
	}
	else {
		return array();
	}
}

// Mount neu laden
getmount($session['user']['hashorse'],true);

$pointsavailable=$session['user']['donation']-$session['user']['donationspent'];

$playerpet = getpet((int)$session['user']['petid']);
$petrepaygems = round($playerpet['gems']*2/3);


$repaygold = round($playermount['mountcostgold']*2/3,0);
$repaygems = round($playermount['mountcostgems']*2/3,0);
$futtercost = $session[user][level]*20;

addnav("Zurück zum Marktplatz","market.php");

if ($session['user']['hashorse']>0 && $session[user][fedmount]==0) {
	addnav("Futter");
	addnav("f?{$playermount['mountname']} füttern (`^$futtercost`0 Gold)","stables.php?op=futter");
}

if ($session['user']['petid']>0) addnav("t?{$playerpet['name']} füttern","stables.php?op=futterpet");


if (($pointsavailable>100) && ($session['user']['hashorse']>0)) {
	addnav("Spezial");
	addnav("{$playermount['mountname']}`0 taufen (100 DP)","stables.php?op=name");
}

if ($_GET[op]==""){
	checkday();
	output("`7Hinter der Kneipe, etwas links von Pegasus' Rüstungen, befindet sich ein Stall, 
	wie man ihn in jedem Dorf erwartungsgemäß findet. 
	Darin kümmert sich Merick, ein stämmig wirkender Zwerg, um verschiedene Tiere.
	`n`n
	Du näherst dich ihm, als er plötzlich herumwirbelt und seine Heugabel in deine ungefähre Richtung streckt. \"`&Ach, 
	'tschuldigung min ".($session[user][sex]?"Mädl":"Jung").", heb dich nit kommen hörn un heb gedenkt,
	du bischt sicha Cedrik, der ma widda sein Zwergenweitwurf ufbessern will. Naaahw, wat 
	kann ich für disch tun?`7\"");
}
elseif ($_GET['op']=="examinepet")
{
	$pet = getpet($_GET['id']);
	if (count($pet)==0)
	{
		output("`7\"`&Ach, ich heb keen solches Tier da!`7\" ruft der Zwerg!");
	}
	else
	{
		
		output("`7\"`&Ai, ich heb wirklich n paar feine Viecher hier!`7\" kommentiert der Zwerg.`n`n");
		output("`7Kreatur: `&{$pet['tpl_name']}`n");
		output("`7Beschreibung: `&{$pet['tpl_description']}`n");
		output("`7Preis: `^{$pet['tpl_gold']}`& Gold, `%{$pet['tpl_gems']}`& Edelstein".($pet['tpl_gems']==1?"":"e")."`n");
		output("`n");
		addnav("Kaufen");
		addnav("Dieses Tier kaufen","stables.php?op=buypet&id={$pet['tpl_id']}");
	}
	
}elseif($_GET['op']=="examine"){
	$sql = "SELECT * FROM mounts WHERE mountid='{$_GET['id']}'";
	$result = db_query($sql);
	if (db_num_rows($result)<=0){
		output("`7\"`&Ach, ich heb keen solches Tier da!`7\" ruft der Zwerg!");
	}
	else{
		
		output("`7\"`&Ai, ich heb wirklich n paar feine Viecher hier!`7\" kommentiert der Zwerg.`n`n");
		$mount = db_fetch_assoc($result);
		
		$int_dksleft = $mount['mindk'] - $session['user']['dragonkills'];
		
		output("`7Kreatur: `&{$mount['mountname']}`n");
		output("`7Beschreibung: `&{$mount['mountdesc']}`n");
		output("`7Preis: `^{$mount['mountcostgold']}`& Gold, `%{$mount['mountcostgems']}`& Edelstein".($mount['mountcostgems']==1?"":"e")."`n");
		output("`n");
		if($int_dksleft > 0) {
			output('`7Ach, da bischt du noch zu unerfahren für, min '.($session['user']['sex']?'Mädl':'Jung').'! 
					Wennscht noch `b'.$int_dksleft.'`b Drachen mehr \'tötet hascht, kannscht widderkommn!`n');
		}
		else {		
			addnav("Dieses Tier kaufen","stables.php?op=buymount&id={$mount['mountid']}");
		}
	}
} elseif ($_GET['op']=='buypet')
{
	$tpl_id = $_GET['id'];
	
	$pet = getpet($tpl_id);
	
	if (count($pet)==0) 
	{
		output("`7\"`&Ach, ich heb keen solches Tier da!`7\" ruft der Zwerg!");
	}
	else 
	{
		if (
			$session['user']['gold'] < $pet['tpl_gold']
			 ||
			($session['user']['gems']+$petrepaygems) < $pet['tpl_gems']
		)
		{
			output("`7Merick schaut dich schief von der Seite an. \"`&Ähm, was gläubst du was du hier machst? Kanns u nich sehen, dass {$pet['name']} `^{$pet['gold']}`& Gold und `%{$pet['gems']}`& Edelsteine kostet?`7\"");
		}
		else
		{
			$feeddays = getsetting("daysperday",4);
			if ($session['user']['petid']>0) 
			{
				output("`7Du übergibst dein(e/n) {$playerpet['tpl_name']} und bezahlst den Preis für dein neues Tier. Merick führt ein(e/n) schöne(n/s) neue(n/s) `&{$pet['tpl_name']}`7  für dich heraus und gibt dir Futter für $feeddays Tage dazu!`n`n");
			}
			else 
			{
				output("`7Du bezahlst den Preis für dein neues Tier und Merick führt ein(e/n) schöne(n/s) neue(n/s) `&{$pet['tpl_name']}`7 für dich heraus und gibt dir Futter für $feeddays Tage dazu!`n`n");
			}
			// delete old pet
			if($session['user']['petid'] > 0) {
				item_delete(' id='.$session['user']['petid']);
			}

			// insert new pet
			$pet['tpl_hvalue'] = $session['user']['house'];
			item_add($session['user']['acctid'], $tpl_id, true, $pet);
					
			$session['user']['petid'] = db_insert_id(LINK);
			
			$session['user']['petfeed'] = date('Y-m-d H:i:s',time() + $feeddays * (3600*24 / getsetting("daysperday",4)));
			$goldcost = -$pet['tpl_gold'];
			$session['user']['gold'] += $goldcost;
			$gemcost = $petrepaygems - $pet['tpl_gems'];
			$session['user']['gems'] += $gemcost;
			debuglog(($goldcost <= 0?"spent ":"gained ") . abs($goldcost) . " gold and " . ($gemcost <= 0?"spent ":"gained ") . abs($gemcost) . " gems trading for a new pet");
			// Recalculate so the selling stuff works right
			$playerpet = getpet((int)$session['user']['petid']);
			$petrepaygems = round($playerpet['gems']*2/3,0);
		}
	}
	
}elseif($_GET['op']=='buymount'){
	$sql = "SELECT * FROM mounts WHERE mountid='{$_GET['id']}'";
	$result = db_query($sql);
	if (db_num_rows($result)<=0){
		output("`7\"`&Ach, ich heb keen solches Tier da!`7\" ruft der Zwerg!");
	}else{
		$mount = db_fetch_assoc($result);
		if ( 
			($session['user']['gold']+$repaygold) < $mount['mountcostgold']
			 || 
			($session['user']['gems']+$repaygems) < $mount['mountcostgems']
		){
			output("`7Merick schaut dich schief von der Seite an. \"`&Ähm, was gläubst du was du hier machst? Kanns u nich sehen, dass {$mount['mountname']} `^{$mount['mountcostgold']}`& Gold und `%{$mount['mountcostgems']}`& Edelsteine kostet?`7\"");
		}else{
			if ($session['user']['hashorse']>0){
				output("`7Du übergibst dein(e/n) {$playermount['mountname']} und bezahlst den Preis für dein neues Tier. Merick führt ein(e/n) schöne(n/s) neue(n/s) `&{$mount['mountname']}`7  für dich heraus!`n`n");
				$session[user][reputation]--;
					
			}
			else{
				output("`7Du bezahlst den Preis für dein neues Tier und Merick führt ein(e/n) schöne(n/s) neue(n/s) `&{$mount['mountname']}`7 für dich heraus!`n`n");
		    }
			
			$sql = "UPDATE account_extra_info SET hasxmount=0,mountextrarounds=0 WHERE acctid=".$session[user][acctid]."";
			db_query($sql);
			
			$session['user']['hashorse']=$mount['mountid'];
			$goldcost = $repaygold-$mount['mountcostgold'];
			$session['user']['gold']+=$goldcost;
			$gemcost = $repaygems-$mount['mountcostgems'];
			$session['user']['gems']+=$gemcost;
			debuglog(($goldcost <= 0?"spent ":"gained ") . abs($goldcost) . " gold and " . ($gemcost <= 0?"spent ":"gained ") . abs($gemcost) . " gems trading for a new mount");
			$session['bufflist']['mount']=unserialize($mount['mountbuff']);
			// Recalculate so the selling stuff works right
									
			$repaygold = round($playermount['mountcostgold']*2/3,0);
			$repaygems = round($playermount['mountcostgems']*2/3,0);
		}
	}
} elseif ($_GET['op']=='sellpet')
{
	
	item_delete(' id='.$session['user']['petid']);
	
	$session['user']['gems'] += $petrepaygems;
	debuglog("gained $petrepaygems gems selling their pet");
	$session['user']['petid'] = 0;
	$session['user']['petfeed'] = '0000-00-00 00:00:00';
	output("`7So schwer es dir auch fällt, dich von dein(er/em) {$playerpet['name']} zu trennen, tust du es doch und eine einsame Träne entkommt deinen Augen.`n`n");
	output("Aber in dem Moment, in dem du die `%$petrepaygems`7 Edelsteine erblickst, fühlst du dich gleich ein wenig besser.");	
}elseif($_GET['op']=='sellmount')
{
	$session['user']['gold']+=$repaygold;
	$session['user']['gems']+=$repaygems;
	debuglog("gained $repaygold gold and $repaygems gems selling their mount");
	unset($session['bufflist']['mount']);
	unset($session['playermount']);
	$session['user']['hashorse']=0;
	$sql = "UPDATE account_extra_info SET hasxmount=0,mountextrarounds=0 WHERE acctid=".$session[user][acctid]."";
	db_query($sql);

	output("`7So schwer es dir auch fällt, dich von dein(er/em) {$playermount['mountname']} zu trennen, tust du es doch und eine einsame Träne entkommt deinen Augen.`n`n");
	output("Aber in dem Moment, in dem du die ".($repaygold>0?"`^$repaygold`7 Gold ".($repaygems>0?" und ":""):"").($repaygems>0?"`%$repaygems`7 Edelsteine":"")." erblickst, fühlst du dich gleich ein wenig besser.");
	$session[user][reputation]-=2;
} elseif ($_GET['op']=='futterpet') {
	if (empty($_POST['days'])) {
		output('Das Futter kostet `^'.$playerpet['value1'].' Gold`0 und
				`%'.$playerpet['value2'].' Edelsteine`0 pro Tag.`n');
		output('<form action="stables.php?op=futterpet" method="post">',true);
		output('Für wie viele Tage möchtest du Futter kaufen?');
		output('<input type="text" name="days" value="0"> <input type="submit" value="Kaufen!">',true);
		output('</form>',true);
		addnav('','stables.php?op=futterpet');
	}
	else {
		$days = (int)$_POST['days'];
		if ($session['user']['gold']>=$playerpet['value1']*$days && $session['user']['gems']>=$playerpet['value2']*$days) {
			$session['user']['gold'] -= $playerpet['value1']*$days;
			$session['user']['gems'] -= $playerpet['value2']*$days;
			if ($playerpet['value1']>0) {
				if ($playerpet['value2']>0) {
					$coststr = '`^'.($playerpet['value1']*$days).' Gold`0 und `%'.($playerpet['value2']*$days).' Edelsteine`0';
				}
				else $coststr = '`^'.($playerpet['value1']*$days).' Gold`0';
			}
			else {
				$coststr = '`%'.($playerpet['value2']*$days).' Edelsteine`0';
			}
			output('Merick nimmt die '.$coststr.' und gibt dir genug Futter, um dein(e/n) '.$playerpet['name'].' die nächsten '.$days.' Tage zu versorgen.`n');
			$oldtime = strtotime($session['user']['petfeed']);
			if ($oldtime < time()) $oldtime = time();
			$newtime = $oldtime + $days * (3600*24 / getsetting("daysperday",4));
			$session['user']['petfeed'] = date('Y-m-d H:i:s',$newtime);
		}
		else {
			output('`7Du kannst das Futter nicht bezahlen. Merick weigert sich, dein Tier für dich durchzufüttern.');
		}
	}	
}elseif($_GET['op']=='futter'){
	if ($session[user][gold]>=$futtercost) {


$sql = "SELECT mountextrarounds,hasxmount,xmountname FROM account_extra_info WHERE acctid=".$session[user][acctid]."";
$result = db_query($sql) or die(db_error(LINK));
$rowm = db_fetch_assoc($result);

        		$buff = unserialize($playermount['mountbuff']);
        		if ($session['bufflist']['mount']['rounds']-$rowm['mountextrarounds'] == $buff['rounds']) {
			output("Dein {$playermount['mountname']} ist satt und rührt das vorgesetzte Futter nicht an. Darum gibt Merick dir dein Gold zurück.");
		}else if ($session['bufflist']['mount']['rounds']-$rowm['mountextrarounds'] > $buff['rounds']*.5) {
			$futtercost=$futtercost/2;
			output("Dein {$playermount['mountname']} nascht etwas von dem vorgesetzten Futter und lässt den Rest stehen. {$playermount['mountname']} ist voll regeneriert. ");
			output("Da aber noch über die Hälfte des Futters übrig ist, gibt dir Merick 50% Preisnachlass.`nDu bezahlst nur $futtercost Gold.");
			$session[user][gold]-=$futtercost;
			$session[user][reputation]--;
		}else{
			$session[user][gold]-=$futtercost;
			output("Dein {$playermount['mountname']} macht sich gierig über das Futter her und frisst es bis auf den letzten Krümel.`n");
			output("Dein {$playermount['mountname']} ist vollständig regeneriert und du gibst Merick die $futtercost Gold."); 
			$session[user][reputation]--;
		}
       		$session['bufflist']['mount']=$buff;
       		$session['bufflist']['mount']['rounds']+=$rowm['mountextrarounds'];
  if ($rowm['hasxmount']==1) {
       		$session['bufflist']['mount']['name']=$rowm['xmountname']." `&({$session['bufflist']['mount']['name']}`&)"; }
       		
		$session[user][fedmount]=1;
	} else {
		output("`7Du hast nicht genug Gold dabei, um das Futter zu bezahlen. Merick weigert sich dein Tier für dich durchzufüttern und empfiehlt dir, im Wald nach einer grasbewachsenen Lichtung zu suchen.");
	}
} elseif($_GET['op']=='name'){

        output("`bDein Tier taufen`b`n`n");

        output("`n`nDer Name deines treuen Freundes darf 20 Zeichen lang sein und Farbcodes enthalten.`n`n");
        $n = $playermount['mountname'];

        output("Dein Tier heißt bisher : `n");
        $output.="{$n}";
        output("`n`n`0Wie soll dein Tier ab sofort heißen ?`n");
        $output.="<form action='stables.php?op=namepreview' method='POST'><input name='newname' value=\"".HTMLEntities($newname)."\" size=\"30\" maxlength=\"20\"> <input type='submit' value='Vorschau'></form>";
        addnav("","stables.php?op=namepreview");

}elseif ($_GET['op']=="namepreview"){
        $n = $session[user][name];

        $_POST['newname']=str_replace("`0","",$_POST['newname']);
		
		// Alle anderen Tags als erlaubte Farbcodes rausschmeißen
		$_POST['newname'] = preg_replace('/[`][^'.regex_appoencode(1,false).']/','',$_POST['newname']);

        if (strlen($_POST['newname'])>20) $msg.="Der neuer Name ist zu lang, inklusive Farbcodes darf er nicht länger als 20 Zeichen sein.`n";
        $colorcount=0;
        for ($x=0;$x<strlen($_POST['newname']);$x++){
            if (substr($_POST['newname'],$x,1)=="`"){
                        $x++;
                        $colorcount++;
                }
        }
        if ($colorcount>getsetting("maxcolors",10)){
                $msg.="Du hast zu viele Farben im Namen benutzt. Du kannst maximal ".getsetting("maxcolors",10)." Farbcodes benutzen.`n";
        }
        if ($msg==""){
				$_POST['newname'] = stripslashes($_POST['newname']);
                output("Dein Tier wird so heißen: {$_POST['newname']}`n`n`0Ist es das was du willst?`n`n");
                $p = 100;
                $output.="<form action=\"stables.php?op=changename\" method='POST'><input type='hidden' name='name' value=\"".HTMLEntities($_POST['newname'])."\"><input type='submit' value='Ja' class='button'>, mein Tier heißt nun ".appoencode("{$_POST['newname']}`0")." für $p Punkte.</form>";
                output("`n`n<a href='stables.php?op=name'>Nein, ich will nochmal </a>",true);
                addnav("","stables.php?op=name");
                addnav("","stables.php?op=changename");
        }else{
                output("`bFalscher Name`b`n$msg");
                output("`n`nDein Tier heißt bisher : ");
                $output.=$n;
                output("`0, und wird so heißen : $newname");
                output("`n`nWie soll dein Tier heißen?`n");
                $output.="<form action='stables.php?op=namepreview' method='POST'><input name='newname' value=\"".HTMLEntities($regname)."\"size=\"30\" maxlength=\"20\"> <input type='submit' value='Vorschau'></form>";
                addnav("","stables.php?op=namepreview");
        }

} else
if ($_GET['op']=="changename"){
        $newname=stripslashes($_POST['name']);
		
		// Alle anderen Tags als erlaubte Farbcodes rausschmeißen
		$newname = preg_replace('/[`][^'.regex_appoencode(1,false).']/','',$newname);
		
        $p = 100;
        if ($pointsavailable>=$p){
            $session['user']['donationspent']+=$p;
             
$sql = "UPDATE account_extra_info SET hasxmount=1,xmountname='".addslashes($newname)."' WHERE acctid=".$session[user][acctid]."";
db_query($sql);

             output("`&Merick hebt zeremoniell seine Peitsche und verkündet:`n\"`3Und im Namen von Epona, Fury und Lassie taufe ich dich auf den Name {$newname}`0!`&\"`n`n");
             $session['bufflist']['mount']['name']=$newname." `&({$session['bufflist']['mount']['name']}`&)";
        }else{
                output("Eine Taufe kostet $p Punkte, aber du hast nur $pointsavailable Punkte.");
        }
        addnav("Zurück zum Marktplatz","market.php");
}

$sql = "SELECT mountname,mountid,mountcategory FROM mounts WHERE mountactive=1 ORDER BY mountcategory,mountcostgems,mountcostgold";
$result = db_query($sql);
$category="";

$count = db_num_rows($result);

for ($i=0;$i<$count;$i++){
	$row = db_fetch_assoc($result);
	if ($category!=$row['mountcategory']){
		addnav($row['mountcategory']);
		$category = $row['mountcategory'];
	}
	addnav("Betrachte {$row['mountname']}`0","stables.php?op=examine&id={$row['mountid']}");
}
if ($session['user']['housekey']>0) {

	$result = item_tpl_list_get(' stables_pet>0 ', ' ORDER BY tpl_gold ASC, tpl_gems ASC ');
	
	if (db_num_rows($result)>0) {
		addnav('Haustiere');
		while ($row = db_fetch_assoc($result)) {
			addnav("Betrachte {$row['tpl_name']}`0",'stables.php?op=examinepet&id='.$row['tpl_id']);
		}
	}
}
if ($session['user']['hashorse']>0){
	output("`n`nMerick bietet dir `^$repaygold`& Gold und `%$repaygems`& Edelsteine für dein(e/n) {$playermount['mountname']}.");
	addnav("Sonstiges");
	addnav("Verkaufe {$playermount['mountname']}","stables.php?op=sellmount");
}
if ($session['user']['petid']>0) {
	if ($session['user']['hashorse']==0) addnav("Sonstiges");
	output("`n`nMerick bietet dir `%$petrepaygems`7 Edelsteine für dein(e/n) {$playerpet['name']}.");
	addnav("Verkaufe {$playerpet['name']}","stables.php?op=sellpet");
}
page_footer();
?>
