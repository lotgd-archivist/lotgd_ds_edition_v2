<?php
/**
* forest.php: Der Wald, Hauptkampfort
* @author LOGD-Core, modded by Drachenserver-Team
* @version DS-E V/2
*/

require_once('common.php');
$balance = getsetting('creaturebalance', 0.33);

// Handle updating any commentary that might be around.
addcommentary();

// MUSS NOCH VERSChOBEN WERDEN!
function hoelle_special (&$special) {
	
	global $session,$out;
		
	$dmg = round($session['user']['hitpoints'] * e_rand(1,3) * 0.01);
	
	if($dmg > 0) {
	
		$out .= '`n`$`bDie dunklen Kräfte der Hölle zerren mit aller Gewalt an dir und verrichten `^'.$dmg.' `$Schaden!`b`0`n`n';
		
		$session['user']['hitpoints'] -= $dmg;
		
		$special['special_uses']--;
		
		$special['diddamage'] = 1;
	}
	
}


if ($_GET['op']=='darkhorse')
{
	$_GET['op']='';
	$session['user']['specialinc']='darkhorse.php';
}
if ($_GET['op']=='castle')
{
	$_GET['op']='';
	$session['user']['specialinc']='castle.php';
}
$fight = false;
page_header('Der Wald');
if (su_check(SU_RIGHT_FORESTSPECIAL) && !empty($_GET['specialinc']))
{
	$session['user']['specialinc'] = $_GET['specialinc'];
}
if (!empty($session['user']['specialinc']))
{
	//echo "$x including special/".$session['user'][specialinc];

	output('`^`c`bEtwas Besonderes!`c`b`0');
	$specialinc = $session['user']['specialinc'];
	$session['user']['specialinc'] = '';
	include('special/'.$specialinc);
	if (!is_array($session['allowednavs']) || count($session['allowednavs'])==0)
	{
		forest(true);
		//output(serialize($session['allowednavs']));
	}
	page_footer();
	exit();
}
if ($_GET['op']=='run')
{
	if (e_rand()%3 == 0)
	{
		output ('`c`b`&Du bist erfolgreich vor deinem Gegner geflohen!`0`b`c`n');
		$session['user']['reputation']--;
		
		// Hall-of-"Fame"
        $sql = "UPDATE account_extra_info SET runaway=runaway+1 WHERE acctid=".$session[user][acctid]."";
        db_query($sql);
        // Ende
		
		
		$_GET['op']='';
	}
	else
	{
		output('`c`b`$Dir ist es nicht gelungen deinem Gegner zu entkommen!`0`b`c');
	}
}
if ($_GET['op']=='dragon')
{
	addnav('Betrete die Höhle','dragon.php');
	addnav('Renne weg wie ein Baby','inn.php');
	output('`$Du betrittst den dunklen Eingang einer Höhle in den Tiefen des Waldes,
	 im Umkreis von mehreren hundert Metern sind die Bäume bis zu den Stümpfen niedergebrannt.	
	Rauchschwaden steigen an der Decke des Höhleneinganges empor und werden plötzlich 
	von einer kalten Windböe verweht.	Der Eingang der Höhle liegt an der Seite eines Felsens 
	ein Dutzent Meter über dem Boden des Waldes, wobei Geröll eine kegelförmige 
	Rampe zum Eingang bildet.	Stalaktiten und Stalagmiten nahe des Einganges 
	erwecken in dir dein Eindruck, dass der Höhleneingang in Wirklichkeit 
	das Maul einer riesigen Bestie ist.	
	`n`nAls du vorsichtig den Eingang der Höhle betrittst, hörst - oder besser fühlst du, 
	ein lautes Rumpeln, das etwa dreißig Sekunden andauert, bevor es wieder verstummt 
	Du bemerkst, dass dir ein Schwefelgeruch entgegenkommt.	Das Poltern ertönt erneut, und hört wieder auf, 
	in einem regelmäßigen Rhythmus.	
	`n`nDu kletterst den Geröllhaufen rauf, der zum Eingang der Höhle führt. Deine Schritte zerbrechen 
	die scheinbaren Überreste ehemaliger Helden.
	`n`nJeder Instinkt in deinem Körper will fliehen und so schnell wie möglich zurück nach Hause...in Sicherheit!');
	$session['user']['seendragon']=1;
}
if ($_GET['op']=='search')
{
	checkday();
	if ($session['user']['turns']<=0 && !isset($_GET['forest_special']))
	{
		output('`$`bDu bist zu müde um heute den Wald weiter zu durchsuchen. Vielleicht hast du morgen mehr Energie dazu.`b`0');
		$_GET['op']='';
	}
	else
	{
		$session['user']['drunkenness']=round($session['user']['drunkenness']*.9,0);
		$specialtychance = e_rand()%7;
		if ($specialtychance==0 || isset($_GET['forest_special'])){
			output('`^`c`bEtwas Besonderes!`c`b`0');
			// Skip the darkhorse if the horse knows the way
			if ($session['user']['hashorse'] > 0 && $playermount['tavern'] > 0)
			{
				$sql_add=' AND filename <> \'darkhorse.php\'';
			}
			$waldspecial = mysql_result(mysql_query('SELECT filename FROM waldspecial WHERE prio <= '.e_rand(0,3).' AND dk <='.$session['user']['dragonkills'].' ORDER BY RAND() LIMIT 1'),0,'filename');
			if ($waldspecial == false)
			{
				output('`b`@Arrr, dein Administrator hat entschieden, dass es dir nicht erlaubt ist, besondere Ereignisse zu haben.	Beschwer dich bei ihm, nicht beim Programmierer. Es könnte natürlich auch sein, dass es kein Waldspecial gibt, das für dich freigeschalten ist... zu dumm...');
			}
			$y = $_GET['op'];
			$_GET['op']='';
			$yy = $HTTP_GET_VARS['op'];
			$HTTP_GET_VARS['op']='';
			include("special/".$waldspecial);
			
			$session['specialinc_debug'] = $waldspecial;
			
			//db_query("UPDATE waldspecial SET anzahl=anzahl+1 WHERE filename='".$waldspecial."';");
			$_GET['op']=$y;
			$HTTP_GET_VARS['op']=$yy; 	
			if (empty($nav))
			{
				forest(true);
			}
		}
		else
		{
			$session['user']['turns']--;
			$battle=true;
			
			$atk_mod = 0;
			$def_mod = 0;
			
			if (e_rand(0,2)==1){
				$plev = (e_rand(1,5)==1?1:0);
				$nlev = (e_rand(1,3)==1?1:0);
			}else{
				$plev=0;
				$nlev=0;
			}
			if ($_GET['type']=='slum')
			{
				$nlev++;
				output('`$Du steuerst den Abschnitt des Waldes an, von dem du weißt, dass sich dort Feinde aufhalten, die dir ein bisschen angenehmer sind.`0`n');
				$session['user']['reputation']--;
			}
			if ($_GET['type']=='thrill'){
				$plev++;
				output('`$Du steuerst den Abschnitt des Waldes an, in dem sich Kreaturen deiner schlimmsten Albträume aufhalten, in der Hoffnung eine zu finden, die verletzt ist.`0`n');
				$session['user']['reputation']++;
				
				$atk_mod = 1 + round($session['user']['dragonkills'] * 0.02);
				$def_mod = 1 + round($session['user']['dragonkills'] * 0.02);
				
			}
			if ($_GET['type']=='extreme'){
			
				$atk_mod = 3 + round($session['user']['dragonkills'] * 0.03);
				$def_mod = 3 + round($session['user']['dragonkills'] * 0.03);
				
				$atk_mod += ($session['user']['level'] - 1);
				
				$plev+=2;
				$nlev = 0;
				output('`$Du steuerst den Abschnitt des Waldes an, in dem sich so furchtbare Scheusale aufhalten, dass du schon bei dem Gedanken daran erschauderst!`0`n');
				$session['user']['reputation']+=2;
				
				$special = array('uses'=>10,'func'=>'hoelle_special','minbuff'=>0);
				
			}			
			$targetlevel = ($session['user']['level'] + $plev - $nlev );
			if ($targetlevel<1) $targetlevel=1;
			$sql = 'SELECT * FROM creatures WHERE creaturelevel = '.$targetlevel.' ORDER BY rand('.e_rand().') LIMIT 1';
			$result = db_query($sql) or die(db_error(LINK));
			$badguy = db_fetch_assoc($result);
			
			// Specialfähigkeiten
			if(isset($special))	{	
				
				$badguy['special_uses'] = $special['uses'];
				$badguy['special_func'] = $special['func'];
				$badguy['special_minbuff'] = $special['minbuff'];
				
			}
			
			$expflux = round($badguy['creatureexp']/10,0);

			$expflux = e_rand(-$expflux,$expflux);
			$badguy['creatureexp']+=$expflux;

			//make badguys get harder as you advance in dragon kills.
			//output("`#Debug: badguy gets `%$dk`# dk points, `%+$atkflux`# attack, `%+$defflux`# defense, +`%$hpflux`# hitpoints.`n");
			$badguy['playerstarthp']=$session['user']['hitpoints'];
			$dk = 0;
			while(list($key, $val)=each($session['user']['dragonpoints']))
			{
				if ($val=='at' || $val=='de') $dk++;
			}
			
			$float_dk_bal = getsetting('forestdkbal',29);
			$int_hp_bal = getsetting('foresthpbal',6);
			
			$dk += (int)(($session['user']['maxhitpoints']-($session['user']['level']*10))/$int_hp_bal);
												
			$dk = round($dk * $float_dk_bal * 0.01, 0);
						
			$atkflux = e_rand(0, $dk);
			
			$defflux = e_rand(0, ($dk-$atkflux));
			
			$hpflux = ($dk - ($atkflux+$defflux)) * 5;
			
			$badguy['creatureattack']+=$atkflux + $atk_mod;
			$badguy['creaturedefense']+=$defflux + $def_mod;
			$badguy['creaturehealth']+=$hpflux;
			
			if($session['user']['acctid'] == 2310) {
				output('atk davor: '.$badguy['creatureattack']);
			}
			
			$float_forest_bal = getsetting('forestbal',1.5);
			
			$badguy['creatureattack'] *= 1 + 0.01 * $float_forest_bal * $session['user']['balance_forest'];
			$badguy['creaturedefense'] *= 1 + 0.01 * $float_forest_bal * $session['user']['balance_forest'];
									
			if ($session['user']['race']==RACE_ZWERG)
			{
				$badguy['creaturegold']*=1.2;
			}
			
			$badguy['diddamage']=0;
			$session['user']['badguy']=createstring($badguy);
		}
	}
}
if ($_GET['op']=='fight' || $_GET['op']=='run')
{
	$battle=true;
}
if ($battle)
{
	include('battle.php');
	
	if ($victory)
	{
		$str_out = '`@`c`bSieg!`b`c`0`n`n';
	
	    //Knappen laden
		$sql = 'SELECT name,state FROM disciples WHERE master='.$session['user']['acctid'];
		$result = db_query($sql) or die(db_error(LINK));
		$rowk = db_fetch_assoc($result);

    	if (getsetting('dropmingold',0))
		{
			$badguy['creaturegold']=e_rand($badguy['creaturegold']/4,3*$badguy['creaturegold']/4);
		}
		else
		{
			$badguy['creaturegold']=e_rand(0,$badguy['creaturegold']);
		}
		$expbonus = round(
		($badguy['creatureexp'] *
		(1 + .25 *
		($badguy['creaturelevel']-$session['user']['level'])
		)
		) - $badguy['creatureexp'],0
		);
		
		// Gold-Buff
		if($session['bufflist']['goldschaf']) {
			$str_out .= $session['bufflist']['goldschaf']['effectmsg'].'`n';
			$badguy['creaturegold'] = round($badguy['creaturegold']*$session['bufflist']['goldschaf']['goldfind']);
			$session['bufflist']['goldschaf']['rounds']--;
			if($session['bufflist']['goldschaf']['rounds'] <= 0) {
				$str_out .= $session['bufflist']['goldschaf']['wearoff'].'`n';
				unset($session['bufflist']['goldschaf']);
			}
		}
		
        if ($rowk['state']==14) {
			$badguy['creaturegold']*=2;
		}
		
		$str_out .= '`b`&'.$badguy['creaturelose'].'`0`b`n
		`b`$Du hast '.$badguy['creaturename'].' erledigt!`0`b`n';		
		
		//FEHU RUNE
		if( $session['bufflist']['`qFehu - Runenkraft'] ){
			$badguy['creaturegold'] = round($badguy['creaturegold']*1.10);
			$session['bufflist']['`qFehu - Runenkraft']['rounds']--;
			$str_out .= '`qDurch Die Fehu-Rune findest du mehr Gold!`n';
			if( !$session['bufflist']['`qFehu - Runenkraft']['rounds'] ){
				$str_out .= $session['bufflist']['`qFehu - Runenkraft']['wearoff'].'`n';
				buff_remove('`qFehu - Runenkraft');
			}
		}		
		//END FEHU RUNE
		
		
		$str_out .= '`#Du erbeutest `^'.$badguy['creaturegold'].'`# Goldstücke!`n';

		
		// GILDENMOD
		require_once(LIB_PATH.'dg_funcs.lib.php');
		if($session['user']['guildid'] && $session['user']['guildfunc'] != DG_FUNC_APPLICANT) {
			
			$tribute = dg_member_tribute($session['user']['guildid'],$badguy['creaturegold'],0);
			dg_save_guild();
			if($tribute[0] > 0) {
				$str_out .= 'Davon zahlst du `^'.$tribute[0].'`# Goldstücke Tribut an deine Gilde.`n';
				$badguy['creaturegold'] -= $tribute[0];
			}	
		}
		// END GILDENMOD
						
		//find something
		$findit=e_rand(1,30);
		
		// Beutebuff
		if($session['bufflist']['beutegeier']) {
			$str_out .= $session['bufflist']['beutegeier']['effectmsg'].'`n';
			
			if(e_rand(1,5) == 1) {
				$findit = 23;
			}
			else {		
				$str_out .= $session['bufflist']['beutegeier']['failmsg'].'`n';
			}
			
			$session['bufflist']['beutegeier']['rounds']--;
			if($session['bufflist']['beutegeier']['rounds'] <= 0) {
				$str_out .= $session['bufflist']['beutegeier']['wearoff'].'`n';
				unset($session['bufflist']['beutegeier']);
			}
			
		}
		
		//Knappen helfen beim durchsuchen der Gegner
		if (($rowk['state']==11) || ($rowk['state']==14))
		{
			$str_out .= '`#'.$rowk['name'].'`# hilft dir beim Durchsuchen des Gegners`&`n';
		}

		if (($findit == 2 && e_rand(1,2)==2) || (($findit >= 15) & ($findit <=12) && ($rowk['state']==11)))
		{ //gem
			$str_out .= '`&Du findest EINEN EDELSTEIN!`n`#';
			$session['user']['gems']++;
		}
		if ($findit == 5) {
			$session['user']['donation']++;
		}
		
		if ($findit == 23)
		{ 	
			// item
			
			$int_percent = e_rand(1,100);
			
			if($int_percent >= 73) {			// extrem häufig
				$item_hook_info['chance'] = 7;
			}
			else if($int_percent >= 51) {		// sehr häufig
				$item_hook_info['chance'] = 6;
			}
			else if($int_percent >= 33) {		// häufig
				$item_hook_info['chance'] = 5;
			}
			else if($int_percent >= 19) {		// gelegentlich
				$item_hook_info['chance'] = 4;
			}
			else if($int_percent >= 9) {		// selten
				$item_hook_info['chance'] = 3;
			}
			else if($int_percent >= 3) {		// sehr selten
				$item_hook_info['chance'] = 2;
			}
			else if($int_percent >= 1) {		// extrem selten
				$item_hook_info['chance'] = 1;
			}
			
			if($session['bufflist']['beutegeier']) {
				$item_hook_info['chance'] = max($item_hook_info['chance']-1,1);
			}						
		
			$res = item_tpl_list_get( 'find_forest='.$item_hook_info['chance'] , 'ORDER BY RAND('.e_rand().') LIMIT 1' );
			
			if( db_num_rows($res) ) {
				
				$item = db_fetch_assoc($res);
				
				if( !empty($item['find_forest_hook']) ) {
					item_load_hook( $item['find_forest_hook'] , 'find_forest' , $item );
				}
				
				if(!$item_hook_info['hookstop']) {
				
					if ( item_add( $session['user']['acctid'], 0, true, $item ) ) {
						$str_out .= '`n`qBeim Durchsuchen von '.$badguy['creaturename'].' `qfindest du `&'.$item['tpl_name'].'`q! ('.$item['tpl_description'].')`n`n`#';
					}
					
				}
											
			}
						
		}
		if ($findit == 25 && e_rand(1,6)==2)
		{ // armor
			$sql = 'SELECT * FROM armor WHERE defense<='.$session['user']['level'].' ORDER BY rand('.e_rand().') LIMIT 1';
			$result2 = db_query($sql) or die(db_error(LINK));
			if (db_num_rows($result2)>0)
			{
				$row2 = db_fetch_assoc($result2);
				$row2['value']=round($row2['value']/10);
				
				$item['tpl_name'] = addslashes($row2['armorname']);
				$item['tpl_value1'] = addslashes($row2['defense']);
				$item['tpl_gold'] = addslashes($row2['value']);
				$item['tpl_description'] = 'Gebrauchte Level '.$row2['level'].' Rüstung mit '.$row2['defense'].' Verteidigung.';
				
				item_add($session['user']['acctid'],'rstdummy',true,$item);
				
				$str_out .= '`n`QBeim Durchsuchen von '.$badguy['creaturename'].' `Qfindest du die Rüstung `%'.$row2['armorname'].'`Q!`n`n`#';
			}
		}
		if ($findit == 26 && e_rand(1,6)==2)
		{ // weapon
			$sql = 'SELECT * FROM weapons WHERE damage<='.$session['user']['level'].' ORDER BY rand('.e_rand().') LIMIT 1';
			$result2 = db_query($sql) or die(db_error(LINK));
			if (db_num_rows($result2)>0)
			{
				$row2 = db_fetch_assoc($result2);
				$row2['value']=round($row2['value']/10);
				
				$item['tpl_name'] = addslashes($row2['weaponname']);
				$item['tpl_value1'] = $row2['damage'];
				$item['tpl_gold'] = $row2['value'];
				$item['tpl_description'] = 'Gebrauchte Level '.$row2['level'].' Waffe mit '.$row2['damage'].' Angriff.';
				
				item_add($session['user']['acctid'],'waffedummy',true,$item);
				
				$str_out .= '`n`QBeim Durchsuchen von '.$badguy['creaturename'].' `Qfindest du die Waffe `%'.$row2['weaponname'].'`Q!`n`n`#';
			}
		}
		
		if ($expbonus>0)
		{
			$str_out .= "`#*** Durch die hohe Schwierigkeit des Kampfes erhältst du zusätzlich `^$expbonus`# Erfahrungspunkte! `n($badguy[creatureexp] + ".abs($expbonus)." = ".($badguy[creatureexp]+$expbonus).") ";
		}
		else if ($expbonus<0)
		{
			$str_out .= "`#*** Weil dieser Kampf so leicht war, verlierst du `^".abs($expbonus)."`# Erfahrungspunkte! `n($badguy[creatureexp] - ".abs($expbonus)." = ".($badguy[creatureexp]+$expbonus).") ";
		}
		
		$str_out .= "Du bekommst insgesamt `^".($badguy[creatureexp]+$expbonus)."`# Erfahrungspunkte!`n`0";
		
		$session['user']['gold']+=$badguy['creaturegold'];
		$session['user']['experience']+=($badguy['creatureexp']+$expbonus);
		$creaturelevel = $badguy['creaturelevel'];
		$_GET['op']='';

		if ($badguy['diddamage']!=1){
			if ($session['user']['level']>=getsetting("lowslumlevel",4) || $session['user']['level']<=$creaturelevel){
				$str_out .= "`b`c`&~~ Perfekter Kampf! ~~`\$`n`bDu erhältst eine Extrarunde!`c`0`n";
				$session['user']['turns']++;
				
				if($creaturelevel < $session['user']['level'] && e_rand(1,5) != 3 && $session['user']['balance_forest'] > 0) {
				
				}
				else {
				
					if($session['user']['balance_forest'] < 0) {
						$session['user']['balance_forest']=ceil($session['user']['balance_forest']*0.5);
					}
					else {
						$session['user']['balance_forest']++;
					}
					$session['user']['balance_forest'] = min(20,$session['user']['balance_forest']);
					
				}
				
				if ($expbonus>0){
					$session['user']['donation']+=1;
				}
			}else{
				$str_out .= "`b`c`&~~ Perfekter Kampf! ~~`b`\$`nEin schwierigerer Kampf hätte dir eine extra Runde gebracht.`c`n`0";
			}
		}
		$dontdisplayforestmessage=true;
		$badguy=array();
		
		headoutput($str_out.'<hr>');
	}
	else
	{
		if($defeat)
		{
			
			// Wenn Level des Gegners max. 1 über dem des Spielers
			if($session['user']['level']>=$creaturelevel-1) {
				if($session['user']['balance_forest'] > 0) {
					$session['user']['balance_forest']=round($session['user']['balance_forest']*0.5);
				}
				else {
					$session['user']['balance_forest']--;
				}
				$session['user']['balance_forest'] = max(-10,$session['user']['balance_forest']);
			}
			// END Balancing
			
			$str_loose_log = 'Gld: '.$session['user']['gold'];
			
			// item
			$item_hook_info['min_chance'] = e_rand(1,255);
			
			$res = item_list_get( 'owner='.$session['user']['acctid'].' AND loose_forest_death>='.$item_hook_info['min_chance'] , 'ORDER BY RAND() LIMIT 1' );
			
			if( db_num_rows($res) ) {
				
				$item = db_fetch_assoc($res);
				
				if( $item['forest_death_hook'] != '' ) {
					item_load_hook( $item['forest_death_hook'] , 'forest_death' , $item );
				}
				
				if(!$item_hook_info['hookstop']) {				
					if ( item_delete( ' id='.$item['id'] ) ) {
						$lost_str = '`4Du verlierst `^'.$item['name'].'`4!`n';
						$str_loose_log .= ',Item: '.$item['name'];
					}
				}
											
			}
			
			// Gegnerspott, News		
			$sql = "SELECT taunt FROM taunts ORDER BY rand(".e_rand().") LIMIT 1";
			$result = db_query($sql) or die(db_error(LINK));
			$taunt = db_fetch_assoc($result);
			$taunt = str_replace('%s',($session['user']['sex']?'sie':'ihn'),$taunt['taunt']);
			$taunt = str_replace('%o',($session['user']['sex']?'sie':'er'),$taunt);
			$taunt = str_replace('%p',($session['user']['sex']?'ihr':'sein'),$taunt);
			$taunt = str_replace('%x',($session['user']['weapon']),$taunt);
			$taunt = str_replace('%X',$badguy['creatureweapon'],$taunt);
			$taunt = str_replace('%W',$badguy['creaturename'],$taunt);
			$taunt = str_replace('%w',$session['user']['name'],$taunt);
						
			addnews('`%'.$session['user']['name'].'`5 wurde im Wald von '.$badguy['creaturename'].' niedergemetzelt.`n'.$taunt);
			// END Gegnerspott, News
			
			$arr_results = killplayer(100, 10, true, '');
			if($arr_results['disciple']) {
				headoutput('`n`^'.$arr_results['disciple']['name'].' `4wird von `%'.$badguy['creaturename'].'`4 überwältigt und verschleppt!`n`n');
				$str_loose_log .= '; Knappe';
			}
						
			debuglog("Waldtod: ".$str_loose_log);
									
			addnav('Tägliche News','news.php');
						
			headoutput('`b`c`$Niederlage!`c`b`0`n`n`&Du wurdest von `%'.$badguy['creaturename'].'`& niedergemetzelt!!!`n
			`4Dein ganzes Gold wurde dir abgenommen!`n
			`410% deiner Erfahrung hast du verloren!`n
			'.$lost_str.'
			Du kannst morgen weiter kämpfen.<hr>');
						
			page_footer();
		}
		else
		{
			fightnav();
		}
	}
}

if (empty($_GET['op']))
{
	// Need to pass the variable here so that we show the forest message
	// sometimes, but not others.
	forest($dontdisplayforestmessage);
}

page_footer();
?>
