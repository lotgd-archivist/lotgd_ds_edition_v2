<?php

$nestedtags=array();
$output='';
$BOOL_POPUP = false;

function pvpwarning($dokill=false)
{
	global $session;
	$days = getsetting('pvpimmunity', 5);
	$exp = getsetting('pvpminexp', 1500);
	if ($session['user']['age'] <= $days &&
	$session['user']['dragonkills'] == 0 &&
	$session['user']['user']['pk'] == 0 &&
	$session['user']['experience'] <= $exp)
	{
		if ($dokill)
		{
			output('`$Warnung!`^ Da du selbst noch vor PvP geschützt warst, aber jetzt einen anderen Spieler angreifst, hast du deine Immunität verloren!!`n`n');
			$session['user']['pk'] = 1;
		}
		else
		{
			output('`$Warnung!`^ Innerhalb der ersten '.$days.'  Tage in dieser Welt, oder bis sie '.$exp.' Erfahrungspunkte gesammelt haben, sind alle Spieler vor PvP-Angriffen geschützt. Wenn du einen anderen Spieler angreifst, verfällt diese Immunität für dich!`n`n');
		}
	}
}

function rawoutput($indata)
{
	global $output;
	$output .= $indata . "\n";
}

function admin_output($str_output)
{
	global $session;
	if($session['user']['superuser']>0)
	{
		rawoutput($str_output);
	}
}

function output($indata,$priv=true){
	global $output;
	//refactoring: nested tags wird nicht gebraucht
	$output.=appoencode($indata,$priv);
}

function headoutput($indata,$priv=true){
	global $output;
	//refactoring: nested tags wird nicht gebraucht
	$output=appoencode($indata,$priv).$output;
}

/**
* @author talion
* @desc Fügt Liedtitel zu Playlist des 'Barden' hinzu. Gibt dazu entsprechendes JScript aus
*		Falls entsprechendes setting des Player auf false, Abbruch!
* @param string 	Kategorie; Falls leer, wird Playlist geleert
* @return -
*/
function music_set ($str_cat) {

	global $session;

	// Eindeutige ID, die markiert, dass Playlist zu diesem Seitenaufruf gehört
	$int_requestid = $session['counter'];

	// Aktueller Aufruf
	$arr_session_pl = $session['playlist'][$int_requestid];

	// Versuchen, einen Zeiger aufs Playerfenster zu erhalten, wenn wir noch keinen haben
	$str_out = '
			<script type="text/javascript">

			';

	// Komplette Liste leeren
	if( empty($str_cat) ) {
		$session['playlist'] = array();
		$str_out .= '</script>';
		output($str_out, true);
		return(true);
	}

	$str_out .= '';

	// Erstmal ohne DB, um zu sehen, ob es überhaupt funktioniert

	$arr_debug = array(
						 'test' => array('title'=>'sandman','filename'=>'sandman.mid')
						,'test2' => array('title'=>'lala','filename'=>'gedanken.mp3')
						);

	$m = $arr_debug[$str_cat];

	$str_sql = 'SELECT * FROM media
				WHERE type="music" AND cat="'.addslashes($str_cat).'" AND active=1
				ORDER BY title ASC';
	//$res = db_query($str_sql);
	//while( $m = db_fetch_assoc($res) ) {

		// Client
		//$str_out .= 'music_player.playlist.add_entry("'.$m['filename'].'","'.$m['title'].'");';

		// Server
		$session['playlist'][] = $m;

	//}

	$str_out .= '</script>';

	output($str_out, true);

}

/**
* @author talion, Verwendet Eliwoods appoencode Erweiterung
* @desc Lädt Farben aus DB in Session, falls nötig (sprich: noch nicht in Session existent).
* @param bool Wenn true, wird Array in Session auf jeden Fall überschrieben (Optional, Standard false)
* @return array Array mit Farben / Tags
*/
function get_appoencode ($bool_forcereload=false) {

	global $session;

	$int_cache = getsetting('cachereloadtime','0');

	if( $int_cache != $session['cachereloadtime'] ) {
		$session['cachereloadtime'] = $int_cache;
		$bool_forcereload = true;
	}

	if(!isset($session['appoencode']) || sizeof($session['appoencode']) == 0 || $bool_forcereload) {

		$str_sql = 'SELECT * FROM appoencode WHERE active="1"';
		$res = db_query($str_sql);

		if(db_num_rows($res)) {

			$session['appoencode'] = array();

			while($c = db_fetch_assoc($res)) {

				$session['appoencode'][$c['code']] = $c;

			}

		}
		else {
			return( array() );
		}

	}

	return($session['appoencode']);

}

/**
* @author talion
* @desc Entfernt Formatierungstags aus gegebenem String
* @param string String, aus dem Tags entfernt werden sollen.
* @param int Siehe bei regex_appoencode
* @param bool Unerlaubte Farbcodes streichen
* @return string Ergebnisstring.
*/
function strip_appoencode ($str_input,$int_mode=1,$bool_forbidden=true) {

	$str_regex = regex_appoencode($int_mode);
	$arr_tags = get_appoencode();

	$str_input = preg_replace('/[`]['.$str_regex.']/','',$str_input);

	return($str_input);

}

/**
* @author talion
* @desc Erstellt einen regulären Ausdruck zur Entfernung/Modifizierung der Formatierungstags
* @param int 1: Nur Farbtags, 2: Nur Sonstige, 3: Alle (Optional, Standard 1)
* @param bool Unerlaubte Codes in Regex aufnehmen (Optional, Standard true)
* @return string Regex
*/
function regex_appoencode ($int_mode=1,$bool_forbidden=true) {

	$arr_tags = get_appoencode();

	foreach($arr_tags as $tag => $c) {

		if( (	($int_mode == 1 && $c['color'] != '') ||
				($int_mode == 2 && $c['color'] == '') ||
				($int_mode == 3)
			) && ($bool_forbidden || $c['allowed'])
		) {

			$str_regex .= $tag;

		}

	}

	return( preg_quote($str_regex) );

}

/**
* @author talion
* @desc Erzeugt einen HTML-Link und fügt auf Wunsch Navimöglichkeit hinzu.
* @param string Text, der verlinkt werden soll.
* @param string Linkpfad
* @param bool Zu allowednavs hinzufügen? (Standard true)
* @param bool Zur Seitennavi links hinzufügen? (Standard false)
* @param string	Sicherheitsabfrage; wenn nicht gegeben, keine
* @return string HTML-Text mit fertigem Link
*/
function create_lnk ($str_txt, $str_lnk, $bool_allownav=true, $bool_leftnav=false, $str_sure='') {

	if(!empty($str_sure)) {

		$str_sure = ' onClick="return confirm(\''.$str_sure.'\');" ';

	}

	$str_out = '<a href="'.$str_lnk.'" '.$str_sure.' >'.$str_txt.'</a>';

	if($bool_allownav) {
		if($bool_leftnav) {
			addnav( $str_txt , $str_lnk );
		}
		addnav( '' , $str_lnk );
	}

	return($str_out);

}

function compress_out ($input)
{
	//Based on old YaBBSE code (c)
	//Open-Source Project by Zef Hemel (zef@zefnet.com <mailto:zef@zefnet.com>)
	//Copyright (c) 2001-2002 The YaBB Development Team
	if((function_exists('gzcompress')) && (function_exists('crc32')))
	{
		if(strpos(' ' . $_SERVER['HTTP_ACCEPT_ENCODING'], 'x-gzip'))
		{
			$encode = 'x-gzip';
		}
		elseif(strpos(' ' . $_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip'))
		{
			$encode = 'gzip';
		}
		if (isset($encode))
		{
			header('Content-Encoding:'. $encode);
			$encode_size = strlen($input);
			$encode_crc = crc32($input);
			$out = "\x1f\x8b\x08\x00\x00\x00\x00\x00";
			$out .= substr(gzcompress($input, 1), 0, -4);
			$out .= pack('V', $encode_crc);
			$out .= pack('V', $encode_size);
		}
		else
		{
			$out = $input;
		}
	}
	else
	{
		$out = $input;
	}
	return ($out);
}


function safeescape($input)
{
	return preg_replace('/([^\\\\])(["\'])/s',"\\1\\\\\\2",$input);
}

function fightnav($allowspecial=true, $allowflee=true){
	global $PHP_SELF,$session;
	//$script = str_replace("/","",$PHP_SELF);
	$script = substr($PHP_SELF,strrpos($PHP_SELF,'/')+1);
	addnav('Kämpfen',$script.'?op=fight');
	if ($allowflee) {
		addnav('Wegrennen',$script.'?op=run');
	}
	if (getsetting('autofight',0)){
		addnav('AutoFight');
		addnav('5 Runden kämpfen',$script.'?op=fight&auto=five');
		addnav('Bis zum bitteren Ende',$script.'?op=fight&auto=full');
	}
	if ($allowspecial) {
		addnav('`bBesondere Fähigkeiten`b');

		if(!isset($session['specialties'])) {

			$sql = "SELECT * FROM specialty WHERE active='1';";
			$result = db_query($sql);

			while($row = db_fetch_assoc($result))
			{
				$session['specialties'][] = $row;
			}
		}

		foreach($session['specialties'] as $row) {

			require_once 'module/'.$row['filename'].'.php';
			$f2 = $row['filename'].'_run';
			$f2('fightnav',0,$script.'?op=fight');

		}

		if (su_check(SU_RIGHT_GODMODE)) {
			addnav('`&Superuser`0','');
			addnav('!?`&&#149; __GOD MODE',$script.'?op=fight&skill=godmode',true);
		}
		// spells by anpera, modded by talion
		$result = item_list_get( ' owner='.$session['user']['acctid'].' AND value1>0
				AND (battle_mode = 1 OR battle_mode = 3) ', ' GROUP BY name ORDER BY value1 ASC, name ASC, id ASC LIMIT 9', true , ' SUM(value1) AS anzahl, name, id ' );

		$int_count = db_num_rows($result);

		if ($int_count>0) addnav(' ~ Dein Beutel ~ ');

		for ($i=1;$i<=$int_count;$i++)
		{
			$row = db_fetch_assoc($result);

			addnav($i.'?'.$row['name'].' `0('.$row['anzahl'].'x)',$script.'?op=fight&skill=zauber&itemid='.$row['id']);
		}
		// end spells
	}
}

/**
* @author talion
* @desc Erstellt CSS-Code für Farbformatierungen
* @return string CSS
*/
function write_appoencode_css () {

	$sql = "SELECT * FROM appoencode WHERE color!=''";
	$result = db_query($sql);

	$str_out = '';
	$int_code = 0;

	while($c = db_fetch_assoc($result)) {

		// Numerischer ASCII-Code für den Tag, um gültige CSS-Class zu erhalten
		$int_code = ord($c['code']);

		$str_out .= '.c'.$int_code;
		$str_out .= '{color:#'.$c['color'].';}';

	}

	return($str_out);
}

/**
* @author LOGD-Core, modded by Eliwood und talion
* @desc Wandelt Formatierungstags in einem String zu HTML/CSS-Äquivalents um
* @param string Input, Text der bearbeitet werden soll
* @param bool Wenn true, werden HTML-Codes nicht escaped
* @return string Bearbeiteter Text
*/
function appoencode($data,$priv=true)
{
    global $nestedtags,$session;
    /* Überarbeitet und verkleinert von Eliwood =D */

    $appoencode = get_appoencode();

    $output = "";
    while (!(($x=strpos($data,"`")) === false) )
    {
        $tag=substr($data,$x+1,1);
        $append=substr($data,0,$x);
        $output.=($priv?$append:htmlentities($append));
        $data=substr($data,$x+2);
        if ($tag == "0")
        {
            if ($nestedtags['color'])
            {
                $output.="</span>";
            }
            unset($nestedtags['color']);
        }
        else if ($tag == "`")
        {
            $output.="`";
        }
        else
        {
            if (isset($appoencode[$tag]))
            {
                $tagrow = $appoencode[$tag];
                if ($tagrow['color'] === NULL)
                {
                    if ($nestedtags[$tagrow['tag']] && strchr($tagrow['tag']," /")==false)
                    {
                        $output.="</".$tagrow['tag'].">";
                        unset($nestedtags[$tagrow['tag']]);
                    }
                    else if (strchr($tagrow['tag']," /")==true)
                    {
                        $output.="<".$tagrow['tag'].">\n";
                    }
                    else
                    {
                        $output.="<".$tagrow['tag']." ".$tagrow['style'].">";
                        $nestedtags[$tagrow['tag']] = true;
                    }
                }
                else
                {
                    if ($nestedtags['color'])
                    {
                        $output.="</span>";
                    }
                    else
                    {
                        $nestedtags['color']=true;
                    }
					// ASCII-Code
                    $output.='<span class="c'.ord($tag).'">';
                }
            }
            else
            {
                $output.=$tag;
            }
        }
    }

    $output.=$data;
    return $output;
}


// Angegebene Tags am Ende des Strings schließen
// (macht keinen Sinn bei Farben, da die nicht geschlossen werden)
function closetags($string, $tags)
{
	$tags = explode('`',$tags);
	$tag_count = count($tags);
	for($i = 0; $i<$tag_count; $i++)
	{
		$tags[$i] = trim($tags[$i]);
		if ($tags[$i]=='')
		{
			continue;
		}
		if (substr_count($string,'`'.$tags[$i])%2)
		{
			$string .= '`'.$tags[$i];
		}
	}
	return $string;
}

function templatereplace($itemname,$vals=false)
{
	global $template, $output;
	//refactoring: Ist sowieso am Anfang, braucht nicht resetted zu werden
	@reset($vals);
	if (!isset($template[$itemname]))
	{
		$output.=('<b>Warnung:<b> Das <i>'.$itemname.'</i> Template wurde nicht gefunden!<br />');
	}
	$out = $template[$itemname];
	if(count($vals)<1)
	{
		return $out;
	}
	//foreach ($vals as $key=>$val)
	while (list($key,$val)=@each($vals))
	{
		/*if (strpos($out,'{'.$key.'}')===false)
		{
			$output.=('<b>Warnung:</b> Das <i>'.$key.'</i> Teil wurde im <i>'.$itemname.'</i> Template nicht gefunden! ('.$out.')<br />');
		}*/
		//refactoring: Diese Überprüfung ist nicht nötig
		$out = str_replace('{'.$key.'}',$val,$out);
	}
	return $out;
}

function charstats()
{
	global $session,$show_invent;

	$u =& $session['user'];
	if ($session['loggedin']){

		$invent = ' - ';

		if($show_invent) {
			$link = 'invent.php?r='.urlencode($_SERVER['REQUEST_URI']);
			$invent = '<a href="'.$link.'">Beutel</a>';
			addnav('',$link);
		}

		$profile = '<a href="#" target="_blank" onClick="'.popup('prefs_new.php').';return false;">Profil</a>';

		$u['hitpoints']=round($u['hitpoints'],0);
		$u['experience']=round($u['experience'],0);
		$u['maxhitpoints']=round($u['maxhitpoints'],0);
		$spirits=array(RP_RESURRECTION=>'Halbtot','-6'=>'Wiedererweckt','-2'=>'Sehr schlecht','-1'=>'Schlecht','0'=>'Normal','1'=>'Gut','2'=>'Sehr gut');
		if ($u['alive']==0)
		{
			$spirits[$u['spirits']] = 'TOT';
		}
		reset($session['bufflist']);
		$atk=$u['attack'];
		$def=$u['defence'];
		foreach($session['bufflist'] as $val)
		{
			$buffs.=appoencode('`#'.$val['name'].' `7('.$val['rounds'].' Runden übrig)`n',true);
			if (isset($val['atkmod'])) {
				$atk *= $val['atkmod'];
			}
			if (isset($val['defmod'])) {
				$def *= $val['defmod'];
			}
		}
		$atk = round($atk, 2);
		$def = round($def, 2);
		$atk = ($atk == $u['attack'] ? '`^' : ($atk > $u['attack'] ? '`@' : '`$')) . '`b'.$atk.'`b`0';
		$def = ($def == $u['defence'] ? '`^' : ($def > $u['defence'] ? '`@' : '`$')) . '`b'.$def.'`b`0';

		if (count($session['bufflist'])==0)
		{
			$buffs.=appoencode('`^Keine`0',true);
		}

		$charstat=appoencode(templatereplace('statstart')
		.templatereplace('stathead',array('title'=>'Vital-Info'))
		.templatereplace('statrow',array('title'=>'Name','value'=>appoencode($u['name'],false)))
		,true);
		if ($session['user']['alive']){

			$charstat.=appoencode(
			templatereplace('statrow',array('title'=>'Lebenspunkte','value'=>$u['hitpoints'].'`0/'.$u['maxhitpoints'].grafbar($u['maxhitpoints'],$u['hitpoints'])))
			.templatereplace('statrow',array('title'=>'Runden','value'=>$u['turns']))
			.(($u['dragonkills']>9)?templatereplace('statrow',array('title'=>'Schlossrunden','value'=>$u['castleturns'])):'')
			,true);
		}
		else
		{
			$charstat.=appoencode(
			templatereplace('statrow',array('title'=>'Seelenpunkte','value'=>$u['soulpoints'].grafbar((5*$u['level']+50),$u['soulpoints'])))
			.templatereplace('statrow',array('title'=>'Foltern','value'=>$u['gravefights']))
			,true);
		}
		$charstat.=appoencode(
		templatereplace('statrow',array('title'=>'Stimmung','value'=>'`b'.$spirits[(string)$u['spirits']].'`b'))
		.templatereplace('statrow',array('title'=>'Level','value'=>'`b'.$u['level'].'`b'))
		.($session['user']['alive']?
			templatereplace('statrow',array('title'=>'Angriff','value'=>$atk))
			.templatereplace('statrow',array('title'=>'Verteidigung','value'=>$def))
			:
			templatereplace('statrow',array('title'=>'Psyche','value'=>10 + round(($u['level']-1)*1.5)))
			.templatereplace('statrow',array('title'=>'Geist','value'=>10 + round(($u['level']-1)*1.5)))
		)
		.templatereplace('statrow',array('title'=>'Edelsteine','value'=>$u['gems']))
		.templatereplace('stathead',array('title'=>'Weitere Infos'))
		.templatereplace('statrow',array('title'=>'Gold','value'=>$u['gold']))
		.($session['user']['alive']?
			templatereplace('statrow',array('title'=>'Erfahrung','value'=>expbar()))
			.($show_invent ? templatereplace('statrow',array('title'=>'Inventar','value'=>$invent)) : '')
			:
			templatereplace('statrow',array('title'=>'Gefallen','value'=>'`b'.$u['deathpower']))
		)
		.templatereplace('statrow',array('title'=>'Profil','value'=>$profile))
		.templatereplace('statrow',array('title'=>'Waffe','value'=>$u['weapon']))
		.templatereplace('statrow',array('title'=>'Rüstung','value'=>$u['armor']))
		,true);
		if ($u['petid']>0) {
			$pettime = strtotime($u['petfeed'])-time();

			// by Maris for Ibga ;)
			$days = ceil($pettime / (3600*24 / getsetting("daysperday",4)));
			if ($days<0) $days=0;
			$charstat .= appoencode(
			templatereplace('statrow',
			array('title'=>'Haustier',
			'value'=>'<font face="verdana" size=1>'.$days.'<br>'.grafbar(24*3600,$pettime).'</font>'

			)
			)
			,true);
		}
		if (getsetting('dispnextday',0)){
			$time = gametime();
			$tomorrow = strtotime(date('Y-m-d H:i:s',$time).' + 1 day');
			$tomorrow = strtotime(date('Y-m-d 00:00:00',$tomorrow));
			$secstotomorrow = $tomorrow-$time;
			$realsecstotomorrow = round($secstotomorrow / (int)getsetting('daysperday',4));
			$charstat.=appoencode(templatereplace('statrow',array('title'=>'Nächster Tag','value'=>date('G\\h, i\\m, s\\s \\',strtotime('1980-01-01 00:00:00 + '.$realsecstotomorrow.' seconds')))),true);
		}
		if (!is_array($session['bufflist'])) $session['bufflist']=array();
		$charstat.=appoencode(templatereplace('statbuff',array('title'=>'Aktionen','value'=>$buffs)),true);
		$charstat.=appoencode(templatereplace('statend'),true);

		return $charstat;
	}
	else
	{
		//Administrator Addon by Hadriel @ anaras.ch
		//modded by Talion: nur noch ein Query
		$sql='SELECT name,superuser FROM accounts WHERE locked=0 AND '.user_get_online().' ORDER BY superuser ASC, level AND dragonkills DESC';
		$result = db_query($sql) or die(sql_error($sql));

		$arr_usergroups = unserialize( stripslashes(getsetting('sugroups','')) );

		$count = array(0=>0,1=>0,2=>0,3=>0);
		$out = array(0=>'',1=>'',2=>'',3=>'');

		while($row = db_fetch_assoc($result))
		{
			$type = 0;

			// In Liste gesondert zeigen?
			if($arr_usergroups[$row['superuser']][3]) {
				$type = $row['superuser'];
			}

			$out[$type] .= '`^'.$row['name'].'`n';
			$count[$type]++;
		}

		$ret.='`&`b'.$count[0].' Spieler Online:`b`n';
		if($out[0]) {$ret.=$out[0];}
		else {$ret.='`i`0Es sind keine Spieler Online!`i';}

		unset($out[0]);

		$ret .= '`n';

		$str_what = '';

		foreach($out as $lvl => $lst) {

			if($lst) {
				$str_what = ($count[$lvl]>1 ? $arr_usergroups[$lvl][1] : $arr_usergroups[$lvl][0]);
				$ret .= '`n';
				$ret.='`&`b'.$count[$lvl].' '.$str_what.' Online:`b`n';
				$ret.=$lst;
			}

		}

		$onlinecount = db_num_rows($result);

		$ret = appoencode($ret);
		$ret.=grafbar(getsetting('maxonline',10),(getsetting('maxonline',10)-$onlinecount),'100%');

		if($onlinecount > getsetting('onlinetop',0)) {
			savesetting('onlinetop',$onlinecount);
			savesetting('onlinetoptime',time());
		}

		db_free_result($result);
		return $ret;
	}
}

// showform by eliwood, modded by talion (tabellen statt divs für Formdarstellung)
// modded by Alucard showform prints return of generateform
function generateform($layout,$row,$nosave = false,$savebutton='Speichern')
{
	global $output;


	$js = '
		<script language="JavaScript">
		var setting = 0;
		function show(setting)
		{
		  if (document.getElementById
		  && document.getElementById(setting).style.visibility == "hidden")
		  {
		    document.getElementById(setting).style.visibility = "visible";
		    document.getElementById(setting).style.display = "inline";
		    document.getElementById("link_"+setting).style.color = "#FFFF00";
		    document.getElementById("link_"+setting).style.background = "#550000";
		  }
		  else
		  {
		    document.getElementById(setting).style.visibility = "hidden";
		    document.getElementById(setting).style.display = "none";
		    document.getElementById("link_"+setting).style.color = "#0099FF";
		    document.getElementById("link_"+setting).style.background = "#303030";
		  }
		}

		function hidden()
		{
		  for(var x = 1; x <{count}; x++)
		  {
		    var elements = document.getElementById(x);
		    elements.style.visibility = "hidden";
		    elements.style.display = "none";
		    document.getElementById("link_"+x).style.color = "#0099FF";
		    document.getElementById("link_"+x).style.background = "#303030";
		  }
		}

		function set_setting(setting)
		{
		  hidden();
		  show(setting);
		}
		</script>';
	// $reiter = "<table cellpadding='0' cellspacing='0' border='0'><tr>";
	$countt = 1;

	$int_reiterlen = 0;

	foreach($layout as $key=>$val)
	{

		$extra_info = explode('|?',$val);
		$info = explode(",",$extra_info[0]);

		// Wenn wir Tooltips für dieses Element benötigen
		// Prüfen, ob wir das JS nicht bereits eingebunden haben
		// copyright for tooltips-script: Dustin Diaz

		$str_tooltip = '';

		if($extra_info[1]) {

			$str_tooltip = ' title="'.$extra_info[1].'" ';

			if(!$bool_tooltip_js) {
				$bool_tooltip_js = true;

				$str_tooltip_js = '<style type="text/css">
									/* Fading Tooltips By Dustin Diaz*/
									body iframe#toolTip_ifrm { position:absolute;z-index:999;width:220px;height:100px;background:#000;border-width:0px;border-style:none; }
									body div#toolTip { position:absolute;z-index:1000;width:220px;height:100px;background:#000;border:2px double #fff;text-align:left;padding:5px;min-height:1em;-moz-border-radius:5px; }
									body div#toolTip p { margin:0;padding:0;color:#fff;font:11px/12px verdana,arial,sans-serif; }
									body div#toolTip p em { display:block;margin-top:3px;color:#f60;font-style:normal;font-weight:bold; }
									body div#toolTip p em span { font-weight:bold;color:#fff; }
									</style>';
				$str_tooltip_js .= '<script type="text/javascript" src="templates/sweetTitles.js"></script>';

				$table = $str_tooltip_js.$table;

			}
		}
		// END tooltips

		if ($info[1]=="title"
			|| $countt == 1)	// Bei jedem Titelfeld ODER wenn noch kein Titelfeld geöffnet wurde
		{

			$str_txt = ($info[1] != 'title' ? '~~~' : $info[0]);

			if($countt > 1) {	// Letztgeöffneten Container samt table schließen
				$table.="</table></div>";
			}

			// Neuen Container erstellen
			$table.="<div id='$countt' style='"
			.($countt>1?"visibility: hidden; display: none;":"visibility: visible; display: block;")
			."'>";

			// Tabelle für Formular erstellen
			$table .= '<table cellspacing="4"><tr><td colspan="2">';

			//$table.="<div style=' background: #666666;'>";
			$table.=appoencode("`b`^$str_txt`0`b");

			// Reiter

			if( ($countt-1) % 4 == 0) {	// jeden 4. Reiter

				$reiter .= '</tr><tr>';

			}

			// Länge der Zeile bestimmen
			$int_len = strlen($str_txt);
			if($int_len > 25) {
				$str_txt = substr($str_txt,0,23).'..';
			}

			$reiter.="<td id='link_".$countt."' onClick=\"javascript&#058;set_setting('".$countt."')\" style='cursor: pointer; padding: 2px; width:40px; border:1px solid #000000; color: ".($countt>1?"#0099FF":"#FFFF00")."; background: ".($countt>1?"#303030":"#550000").";'>";
			$reiter.='&nbsp;'.$str_txt.'&nbsp;';
			$reiter.='</td>';

			if($info[1] != 'title') {

				// Eigentlich normales Feld!

				// Neue Zeile
				$table .= '</td></tr>
							<tr><td valign="top" '.$str_tooltip.' '.
							(strlen($info[0]) <= 55 ? 'nowrap' : '').
							'>';
				// Labeltext
				$table.=appoencode($info[0]);

				if($str_tooltip != '') {
					$table .= ' <small>[?]</small> ';
				}

				// Zelle für Formularfeld öffnen
				$table .= '</td><td>';

			}

			$countt++;
		}
		else	// Bei Nicht-Titelfeldern
		{

			// Neue Zeile
			$table .= '</td></tr>
						<tr><td valign="top" '.$str_tooltip.' '.
						(strlen($info[0]) <= 55 ? 'nowrap' : '').
						'>';
			// Labeltext
			$table.=appoencode($info[0]);

			if($str_tooltip != '') {
				$table .= ' <small><b>[?]</b></small> ';
			}

			// Zelle für Formularfeld öffnen
			$table .= '</td><td>';

		}

		// Prüfen, welchen Typ von Formularfeld
		switch($info[1])
		{
			case "title":	// wird oben erledigt
				break;
			case "textarea":
				// Restzeichenanzeige
				$str_rv = '';
				if($info[4] > 0) {
					$table.="Noch <input type='text' id='".$key."_jscounter' size='4' value='".$info[4]."' readonly> Zeichen übrig.<br>";
					$str_rv = "onchange='CountMax(".$info[4].",\"".$key."\");' onfocus='CountMax(".$info[4].",\"".$key."\");' onkeydown='CountMax(".$info[4].",\"".$key."\");' onkeyup='CountMax(".$info[4].",\"".$key."\");'";
					// Restzeichenanzeige, einbinden falls noch nicht vorhanden
					if(!$bool_leftchars_js) {
						$bool_leftchars_js = true;
						//Javascript für die Restzeichenanzeige der nachrichten, entnommen aus mail.php
						$table =
						'<script language="JavaScript">
						<!--
						function CountMax(wert,el)
						{
							var max = wert;
							var handler_counter = document.getElementById(el+"_jscounter");
							var handler = document.getElementById(el);
							var str = handler.value;
							wert = max - str.length;

							if (wert < 0)
							{
								handler.value = str.substring(0,max);
								wert = max-str.length;
								handler_counter.value = wert;
							}
							else
							{
								handler_counter.value = max - str.length;
							}
						}
						//-->
						</script> ' . $table;

					}
				}

				$table.="<textarea name='$key' id='$key' class='input' cols='$info[2]' rows='$info[3]'
						".$str_rv.">".$row[$key]."</textarea>";

				break;
			case 'file':
				$table .= "<input name='$key' type='file'>";
			break;
			case "enum":
				reset($info);
				list($k,$v)=each($info);
				list($k,$v)=each($info);

				$table.="<select name='$key'>";
				while (list($k,$v)=each($info)){
					$optval = $v;
					list($k,$v)=each($info);
					$optdis = $v;
					$table.="<option value='$optval'".($row[$key]==$optval?" selected":"").">".HTMLEntities("$optval : $optdis")."</option>";
				}
				$table.="</select>";
			break;
			// Aufsteigende Liste von Zahlen by talion
			// info[2]: Von-Wert, info[3]: Bis-Wert
			case "enum_order":
				$bool_order = true;

				// Absteigend
				if($info[2] > $info[3]) {
					$bool_order = false;
				}

				$table.="<select name='$key'>";

				for($i = $info[2]; $i <= $info[3]; $i+=($bool_order ? 1 : -1) ) {

					$table.='<option value="'.$i.'" '.($row[$key]==$i?' selected':'').'> '.$i.' </option>';

				}

				$table.="</select>";
			break;
			case "select":
				reset($info);
				list($k,$v)=each($info);
				list($k,$v)=each($info);
				$table.="<select name='$key'>";
				while (list($k,$v)=each($info)){
					$optval = $v;
					list($k,$v)=each($info);
					$optdis = $v;
					$table.="<option value='$optval'".($row[$key]==$optval?" selected":"").">".HTMLEntities("$optdis")."</option>";
				}
				$table.="</select>";
				break;
			// added by talion
			case 'radio':
				reset($info);
				list($k,$v)=each($info);
				list($k,$v)=each($info);
				while (list($k,$v)=each($info))
				{
					$optval = $v;
					list($k,$v)=each($info);
					$optdis = $v;
					$table.='<input type="radio" name="'.$key.'" value="'.$optval.'"'.($row[$key]==$optval?' checked':'').'> '.$optdis.'<br>';
				}
				break;
			// added by talion
			case 'checkbox':
				$table.='<input class="input" type="checkbox" name="'.$key.'" value="'.HTMLEntities($info[2]).'" '.($row[$key]==$info[2] ? ' checked':'').'>';
				break;
			case "password":
				$table.="<input type='password' name='$key' value='".HTMLEntities($row[$key])."'>";
				break;
			case "bool":
				$table.="<select name='$key'>";
				$table.="<option value='0'".($row[$key]==0?" selected":"").">Nein</option>";
				$table.="<option value='1'".($row[$key]==1?" selected":"").">Ja</option>";
				$table.="</select>";
				break;
			case "hidden":
				$table.="<input type='hidden' name='$key' value=\"".HTMLEntities($row[$key])."\">".HTMLEntities($row[$key]);
				break;
			case "viewonly":
				$table.= appoencode(dump_item($row[$key]));
				break;
			case "int":
				$table.="<input name='$key' value=\"".HTMLEntities($row[$key])."\" size='5'>";
				break;
			default:
				$table.=("<input size='30' name='$key' value=\"".HTMLEntities($row[$key])."\">");
		}	// END formfeld-typ

		// Zelle für Formfeld und Zeile schließen
		$table .= '</td></tr>';
	}	// END Layout durchgehen

	// Letztgeöffneten Container schließen
	$table .= '</table></div>';

	$reiter.="<div style='clear: both;'></div><br>";

	$table = str_replace("{count}",$countt,$js).$table;
	//$table = str_replace("><",">\n<",$table);
	$reiter = str_replace("><",">\n<",$reiter);

	if ($nosave==false) {
		$table .= "<div align='center'><input type='submit' class='button' value='".$savebutton."'></div>";
	}

	// Wenn nur ein Bereich vorhanden
	if($countt <= 2) {
		$reiter = '';
	}

	$reiter = '<table><tr>'.$reiter.'</tr></table>';
	$table = ''.$table.'';

//	print_r($reiter.$table);

//	rawoutput($reiter.$table);
	return $reiter.$table;
}


function showform($layout,$row,$nosave = false,$savebutton='Speichern')
{
	rawoutput(generateform($layout, $row, $nosave, $savebutton));
}

function loadtemplate($templatename)
{
	if (!file_exists('templates/'.$templatename) || $templatename=='') $templatename='dragonslayer_1.html';
	$fulltemplate = join('',file('templates/'.$templatename));
	$fulltemplate = explode('<!--!',$fulltemplate);
	while (list($key,$val)=each($fulltemplate))
	{
		$fieldname=substr($val,0,strpos($val,'-->'));
		if ($fieldname!='')
		{
			$template[$fieldname]=substr($val,strpos($val,'-->')+3);
		}
	}
	return $template;
}

function maillink()
{
	global $session;
	$sql = 'SELECT sum(if(seen=1,1,0)) AS seencount, sum(if(seen=0,1,0)) AS notseen FROM mail WHERE msgto=\''.$session['user']['acctid'].'\'';
	$result = db_query($sql) or die(mysql_error(LINK));
	$row = db_fetch_assoc($result);
	db_free_result($result);
	$row['seencount']=(int)$row['seencount'];
	$row['notseen']=(int)$row['notseen'];

	$return = '';
	if($row['seencount']>=getsetting('inboxlimit',50) && $session['user']['superuser']<1)
	{
		$return .= '
		<div style="z-index:5; float:left; padding-bottom:10px; padding-top:10px; background-color:black; vertical-align:middle; width:100%; height:auto; font-size:20px; top:2px; left:0px; position:relative; border:2px solid red; background-image: url(templates/dragonslayer_1/bg_tile_stressedmetal.jpg);">

			Zu viele Mails!<br />
			Du hast zu viele Mails in Deiner Inbox. Bitte lösche einige!<br />
			<a href="#" target="_blank" onClick="'.popup('mail.php').';return false;" >Ye Olde Mail öffnen</a>

		</div>';
	}

	if ($row['notseen']>0)
	{
		$return .= '<a href="mail.php" target="_blank" onClick="'.popup('mail.php').';return false;" class="hotmotd">Brieftauben: '.$row['notseen'].' neu, '.$row['seencount'].' alt</a>';
	}
	else
	{
		$return .= '<a href="mail.php" target="_blank" onClick="'.popup('mail.php').';return false;" class="motd">Brieftauben: '.$row['notseen'].' neu, '.$row['seencount'].' alt</a>';
	}
	return $return;
}

function motdlink()
{
	// missing $session caused unread motd's to never highlight the link
	global $session;
	if ($session['needtoviewmotd'])
	{
		return '<a href="motd.php" target="_blank" onClick="'.popup('motd.php').';return false;" class="hotmotd"><b>MoTD</b></a>';
	}
	else
	{
		return '<a href="motd.php" target="_blank" onClick="'.popup('motd.php').';return false;" class="motd"><b>MoTD</b></a>';
	}
}

function page_header($title='LoGD 0.9.7 +jt ext (GER) 3 Dragonslayer edition')
{
	global $header,$SCRIPT_NAME,$session,$template;
	$nopopups['login.php']=1;
	$nopopups['motd.php']=1;
	$nopopups['index.php']=1;
	$nopopups['create.php']=1;
	$nopopups['about.php']=1;
	$nopopups['mail.php']=1;
	$nopopups['chat.php']=1;

	$header = $template['header'];

	$str_colors_css = '<style type="text/css">
							@import url(templates/colors.css);
						</style>';

	if($session['user']['lastmotd'] == '0000-00-00 00:00:00')
	{
		$header=str_replace('{headscript}',$str_colors_css.'<script language="JavaScript" type="text/javascript">'.popup('motd.php').'</script>',$header);
		$session['needtoviewmotd']=true;
	}
	else
	{
		$header=str_replace('{headscript}',$str_colors_css.'',$header);
		$session['needtoviewmotd']=false;
	}
	$header=str_replace('{title}',$title,$header);

}

function popup($page)
{
	return "window.open('$page','".preg_replace("([^[:alnum:]])","",$page)."','scrollbars=yes,resizable=yes,width=550,height=300')";
}

function page_footer()
{
	$forumlink=getsetting("forum","http://lotgd.net/forum");
	global $output,$nestedtags,$header,$nav,$session,$REMOTE_ADDR,$REQUEST_URI,$pagestarttime,$dbtimethishit,$dbqueriesthishit,$quickkeys,$template,$logd_version,$BOOL_COMMENTAREA;

	$bool_vitalout = false;
	// Vitalinfo im Dragonslayer-Tpl ausblenden, wenn gewünscht by talion
	if($session['user']['prefs']['template'] == 'dragonslayer_1.html' && $BOOL_COMMENTAREA && $session['disablevital']) {
		$bool_vitalout = true;
	}

	foreach($nestedtags as $key=>$val)
	{
		$output.='</'.$key.'>';

		unset($nestedtags[$key]);
	}
	$script.='<script language="JavaScript" type="text/javascript" src="templates/md5.js"></script>';

	$footer = $template['footer'];
	if (strpos($footer,'{paypal}') || strpos($header,'{paypal}')){ $palreplace='{paypal}'; }else{ $palreplace='{stats}'; }

	//NOTICE
	//NOTICE Although I will not deny you the ability to remove the below paypal link, I do request, as the author of this software
	//NOTICE that you leave it in.
	//NOTICE
	$paypalstr = '<table align="center"><tr><td>';
	$paypalstr .= '
	<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
		<input type="hidden" name="cmd" value="_xclick">
		<input type="hidden" name="business" value="nahdude81@hotmail.com">
		<input type="hidden" name="item_name" value="Legend of the Green Dragon Author Donation from '.preg_replace("/[`]./","",$session['user']['name']).'">
		<input type="hidden" name="item_number" value="'.htmlentities($session['user']['login']).":".$_SERVER['HTTP_HOST']."/".$_SERVER['REQUEST_URI'].'">
		<input type="hidden" name="no_shipping" value="1">
		<input type="hidden" name="cn" value="Your Character Name">
		<input type="hidden" name="cs" value="1">
		<input type="hidden" name="currency_code" value="USD">
		<input type="hidden" name="tax" value="0">
		<input type="image" src="images/paypal1.gif" border="0" name="submit" alt="Donate!">
	</form>';
	$paysite = getsetting('paypalemail', '');
	if ($paysite != '') {
		$paypalstr .= '</td><td>';
		$paypalstr .= '
		<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
			<input type="hidden" name="cmd" value="_xclick">
			<input type="hidden" name="business" value="'.$paysite.'">
			<input type="hidden" name="item_name" value="Legend of the Green Dragon Site Donation from '.preg_replace("/[`]./","",$session['user']['name']).'">
			<input type="hidden" name="item_number" value="'.htmlentities($session['user']['login']).":".$_SERVER['HTTP_HOST']."/".$_SERVER['REQUEST_URI'].'">
			<input type="hidden" name="no_shipping" value="1">
			<input type="hidden" name="cn" value="Your Character Name">
			<input type="hidden" name="cs" value="1">
			<input type="hidden" name="currency_code" value="USD">
			<input type="hidden" name="tax" value="0">
			<input type="image" src="images/paypal2.gif" border="0" name="submit" alt="Donate!">
		</form>';
	}
	$paypalstr .= '</td></tr></table>';
	$footer=str_replace($palreplace,(strpos($palreplace,'paypal')?'':'{stats}').$paypalstr,$footer);
	$header=str_replace($palreplace,(strpos($palreplace,'paypal')?'':'{stats}').$paypalstr,$header);
	//NOTICE
	//NOTICE Although I will not deny you the ability to remove the above paypal link, I do request, as the author of this software
	//NOTICE that you leave it in.
	//NOTICE

	if(!$bool_vitalout) {
		$header=str_replace('{nav}',$nav,$header);
		$footer=str_replace('{nav}',$nav,$footer);
	}
	else {
		$header=str_replace('{nav}','',$header);
		$footer=str_replace('{nav}','',$footer);
	}

	$motdlnk = motdlink();

	$header = str_replace('{motd}', $motdlnk, $header);
	$footer = str_replace('{motd}', $motdlnk, $footer);
	$header = str_replace('{forum}', '<a href="petition.php?op=faq" target="_blank" class="motd" onClick="'.popup('petition.php?op=faq').';return false;"><b>Regeln</b> und FAQ</a>', $header);
	$footer = str_replace('{forum}', '<a href="petition.php?op=faq" target="_blank" class="motd" onClick="'.popup('petition.php?op=faq').';return false;"><b>Regeln</b> und FAQ</a>', $footer);

	if ($session['user']['acctid']>0)
	{
		$maillnk = maillink();
		$header=str_replace('{mail}',$maillnk,$header);
		$footer=str_replace('{mail}',$maillnk,$footer);
		$header=str_replace('{chat}','<a href="chat.php" target="_blank" class="motd" onClick="'.popup('chat.php').';return false;">Chat</a>',$header);
		$footer=str_replace('{chat}','<a href="chat.php" target="_blank" class="motd" onClick="'.popup('chat.php').';return false;">Chat</a>',$footer);
	}
	else
	{
		$header=str_replace('{mail}','',$header);
		$footer=str_replace('{mail}','',$footer);
		$header=str_replace('{chat}','',$header);
		$footer=str_replace('{chat}','',$footer);
	}
	$header=str_replace('{petition}','<a href="petition.php" onClick="'.popup('petition.php').';return false;" target="_blank" align="right" class="motd">Anfrage schreiben</a>',$header);
	$footer=str_replace('{petition}','<a href="petition.php" onClick="'.popup('petition.php').';return false;" target="_blank" align="right" class="motd">Anfrage schreiben</a>',$footer);

	if($session['user']['superuser'] > 0) {

		$str_append = "<table border='0' cellpadding='5' cellspacing='0' align='right'>";

		if (su_check(SU_RIGHT_PETITION))
		{
			$sql = 'SELECT max(lastact) AS lastact, count(petitionid) AS c,status FROM petitions GROUP BY status';
			$result = db_query($sql);
			$petitions=array(0=>0,1=>0,2=>0);
			$petitions['unread'] = false;
			$int_count = db_num_rows($result);
			for ($i=0;$i<$int_count;$i++)
			{
				$row = db_fetch_assoc($result);
				$petitions[(int)$row['status']] = $row['c'];
				if ($row['lastact']>$session['lastlogoff']) $petitions['unread'] = true;
			}
			db_free_result($result);
			// Neue Petitionen; schauen, ob Sternchen nötig ist
			$petitions['star'] = '';
			if ($petitions['unread'])
			{
				$sql = 'SELECT petitionid, lastact FROM petitions WHERE lastact > "'.$session['lastlogoff'].'"';
				$result = db_query($sql);
				while ($row = db_fetch_assoc($result))
				{
					if (!$session['petitions'][$row['petitionid']])
					{
						$petitions['star'] = appoencode('`$*`0');
					}
				}
				db_free_result($result);
			}

			$pet_link = 'superuser.php?op=intro_pet&su_return='.urlencode(calcreturnpath());
			$str_append .= '<tr><td align="right"><b>'.create_lnk('Anfragen',$pet_link).' '.$petitions[star].':</b> '.$petitions[0].' Ungelesen, '.$petitions[1].' Gelesen, '.$petitions[2].' Geschlossen.</td></tr>';
		}

		$grotte_link = 'superuser.php?op=intro_grotte&su_return='.urlencode(calcreturnpath());
		$str_lnk_full = addnav('',$grotte_link);
		$str_append .= '<tr><td align="right"><b>'.create_lnk('Admingrotte',$grotte_link).'</b></td></tr>';

		$quickkeys['<']="window.location='$str_lnk_full'";

		if(su_check(SU_RIGHT_STEALTH)) {

			$stealth_link = 'superuser.php?op=stealth';
			$str_append .= '<tr><td align="right"><b>'.create_lnk('Stealthmode '.($session['user']['activated'] == USER_ACTIVATED_STEALTH ? 'Ausschalten' : 'Anschalten').' !',$stealth_link).'</b></td></tr>';

		}
		$str_append .= "</table>";
		$footer = $str_append.$footer;

	}

	if($bool_vitalout) {

		$footer.='<script language="JavaScript" type="text/javascript">

						document.getElementById("border_right").style.display = "none";
						document.getElementById("border_left").style.display = "none";
						document.getElementsByTagName("body")[0].style.width = "98%";

					</script>';

		$footer=str_replace('{stats}','',$footer);
		$header=str_replace('{stats}','',$header);
	}
	else {

		$stats = charstats();
		$footer=str_replace('{stats}',$stats,$footer);
		$header=str_replace('{stats}',$stats,$header);
	}

	$script.='<script language="JavaScript" type="text/javascript">
	<!--
	document.onkeydown=keyevent;
	document.onkeypress=keyevent;
	function keyevent(e){
		var c,trg,altk,ctrlk,ev,cd;

		if (window.event != null) {
			ev = window.event;
			trg=ev.srcElement;
			cd =  (ev.keyCode ? ev.keyCode : ev.which);
		}
		else {
			ev = e;
			trg=ev.originalTarget;
			cd =  (ev.charCode ? ev.charCode : ev.which);
		}

		c=String.fromCharCode(cd).toUpperCase();
		altk=ev.altKey;
		ctrlk=ev.ctrlKey;

		if (trg.nodeName.toUpperCase()=="INPUT" || trg.nodeName.toUpperCase()=="TEXTAREA" || altk || ctrlk)
		{

		}
		else {
		if(ev.type=="keydown") {
		';

	// Wenn Pfeiltasten als Quickkeys genutzt werden sollen, by talion
	if($quickkeys['arrowleft']) {
		$val = $quickkeys['arrowleft'];
		$script .= '
							if (cd==37) { '.$val.';return false; }';
		unset($quickkeys['arrowleft']);
	}
	if($quickkeys['arrowup']) {
		$val = $quickkeys['arrowup'];
		$script .= '
							if (cd==38) { '.$val.';return false; }';
		unset($quickkeys['arrowup']);
	}
	if($quickkeys['arrowright']) {
		$val = $quickkeys['arrowright'];
		$script .= '
							if (cd==39) { '.$val.';return false; }';
		unset($quickkeys['arrowright']);
	}
	if($quickkeys['arrowdown']) {
		$val = $quickkeys['arrowdown'];
		$script .= '
							if (cd==40) { '.$val.';return false; }';
		unset($quickkeys['arrowdown']);
	}
	// END Pfeiltasten als Hotkeys

	// Wenn kein keydown
	$script .= '}
				else
				{';

	reset($quickkeys);
	foreach($quickkeys as $key=>$val)
	{
		$script.='
							if (c == "'.strtoupper($key).'") { '.$val.';return false; }';
	}
	$script.='
		}
		}
	}
	//-->
	</script>';

	$header=str_replace('{script}',$script,$header);
	if ($session['user']['loggedin'])
	{
		$footer=str_replace('{source}','<a href="source.php?url='.preg_replace('/[?].*/','',($_SERVER['REQUEST_URI'])).'" target="_blank">Source</a>',$footer);
		$header=str_replace('{source}','<a href="source.php?url='.preg_replace('/[?].*/','',($_SERVER['REQUEST_URI'])).'" target="_blank">Source</a>',$header);
	}
	else
	{
		$footer=str_replace('{source}','<a href="source.php" target="_blank">Source</a>',$footer);
		$header=str_replace('{source}','<a href="source.php" target="_blank">Source</a>',$header);
	}
	$footer=str_replace('{copyright}','Copyright 2002-2003, Game: Eric Stevens',$footer);
	$footer=str_replace('{version}', 'Version: '.$logd_version, $footer);
	$gentime = getmicrotime()-$pagestarttime;

	//removed mean generation time - absolutely useless - dragonslayer
	//$session['user']['gentime']+=$gentime;
	//$session['user']['gentimecount']++;
	$dbtimethishit=round($dbtimethishit,2);
	//$footer=str_replace('{pagegen}','Seitengenerierung: '.round($gentime,2).'s, Schnitt: '.round($session['user']['gentime']/$session['user']['gentimecount'],2).'s'.($session['user']['superuser']>1?'; DB: '.$dbqueriesthishit.' in '.$dbtimethishit.'s':'').'',$footer);
	$footer=str_replace('{pagegen}','Seitengenerierung: '.round($gentime,2).'s'.($session['user']['superuser']>0?'; DB: '.$dbqueriesthishit.' in '.$dbtimethishit.'s':'').'',$footer);

	$output=$header.$output.$footer;

	$session['user']['gensize']+=strlen($output);
	$session['output']=$output;

	saveuser();

	session_write_close();
	echo $output;
	exit();
}


function popup_header($title="Legend of the Green Dragon")
{
	global $header,$BOOL_POPUP;

	$BOOL_POPUP = true;

	$header.='<html><head><title>'.$title.'</title>';
	$header.='<link href="newstyle.css" rel="stylesheet" type="text/css">';
	$header.='<style type="text/css">
					@import url(templates/colors.css);
				</style>';
	$header.='</head><body bgcolor="#000000" text="#CCCCCC"><table cellpadding=5 cellspacing=0 width="100%">';
	$header.='<tr><td class="popupheader"><b>'.$title.'</b></td></tr>';
	$header.='<tr><td valign="top" width="100%">';
}

function popup_footer($bool_saveuser = true)
{
	global $output,$nestedtags,$header,$nav,$session;
	foreach($nestedtags as $key=>$val)
	{
		$output.='</'.$key.'>';
		unset($nestedtags[$key]);
	}
	$output.='</td></tr><tr><td bgcolor="#330000" align="center">Copyright 2002, Eric Stevens</td></tr></table></body></html>';
	$output=$header.$output;

	if($bool_saveuser) {
		saveuser();
	}

	echo $output;
	exit();
}

function clearoutput()
{
	global $output,$nestedtags,$header,$nav,$session;
	$session['allowednavs']=null;
	$output=null;
	unset($nestedtags);
	$header=null;
	$nav=null;
}

function soap($input){
	if (getsetting('soap',1)){
		$sql = 'SELECT * FROM nastywords';
		$result = db_query($sql);
		$row = db_fetch_assoc($result);
		$search = $row['words'];
		$search = str_replace('a','[a4@]',$search);
		$search = str_replace('l','[l1!]',$search);
		$search = str_replace('i','[li1!]',$search);
		$search = str_replace('e','[e3]',$search);
		$search = str_replace('t','[t7+]',$search);
		$search = str_replace('o','[o0]',$search);
		$search = str_replace('s','[sz$]',$search);
		$search = str_replace('k','c',$search);
		$search = str_replace('c','[c(k]',$search);
		$start = "'(\s|\A)";
		$end = "(\s|\Z)'iU";
		$search = str_replace('*','([[:alnum:]]*)',$search);
		$search = str_replace(' ',$end.' '.$start, $search);
		$search = $start.$search.$end;
		$search = explode(' ',$search);

		return preg_replace($search,'\1`i$@#%`i\2',$input);
	}
	else
	{
		return $input;
	}
}

function createstring($array)
{
	if (is_array($array))
	{
		reset($array);
		while (list($key,$val)=each($array))
		{
			$output.=rawurlencode( rawurlencode($key).'"'.rawurlencode($val) ).'"';
		}
		$output=substr($output,0,strlen($output)-1);
	}
	return $output;
}

function createarray($string)
{
	$arr1 = split("\"",$string);
	$output = array();
	while (list($key,$val)=each($arr1))
	{
		$arr2=split("\"",rawurldecode($val));
		$output[rawurldecode($arr2[0])] = rawurldecode($arr2[1]);
	}
	return $output;
}

function output_array($array,$prefix=''){

	reset($array);

	while (list($key,$val)=@each($array))
	{
		$output.=$prefix.'['.$key.'] = ';
		if (is_array($val))
		{
			$output.="array{\n".output_array($val,$prefix."[$key]")."\n}\n";
		}
		else
		{
			$output.=$val."\n";
		}
	}
	return $output;
}

function dump_item($item)
{
	$output = '';
	if (is_array($item)) $temp = $item;
	else $temp = unserialize($item);
	if (is_array($temp))
	{
		$output .= "array(" . count($temp) . ") {<blockquote>";
		while(list($key, $val) = @each($temp)) {
			$output .= "'$key' = '" . dump_item($val) . "'`n";
		}
		$output .= "</blockquote>}";
	} else {
		$output .= $item;
	}
	return $output;
}

function ordinal($val){
	$exceptions = array(1=>'ten',2=>'ten',3=>'ten',11=>'ten',12=>'ten',13=>'ten');
	$x = ($val % 100);
	if (isset($exceptions[$x]))
	{
		return $val.$exceptions[$x];
	}
	else
	{
		$x = ($val % 10);
		if (isset($exceptions[$x]))
		{
			return $val.$exceptions[$x];
		}
		else
		{
			return $val.'ten';
		}
	}
}

// exp bar mod coded by: dvd871 with modifications by: anpera

function expbar() {
	global $session;
	/*$exparray=array(1=>100,400,1002,1912,3140,4707,6641,8985,11795,15143,19121,23840,29437,36071,43930,55000);
	while (list($key,$val)=each($exparray))
	{
	$exparray[$key]= round($val + ($session['user']['dragonkills']/4) * $key * 100,0);
	}
	$exp = $session['user']['experience']-$exparray[$session['user']['level']-1];*/

	$last_exp = get_exp_required($session['user']['level']-1,$session['user']['dragonkills']);
	$exp_req = get_exp_required($session['user']['level'],$session['user']['dragonkills']);
	$left = $session['user']['experience'] - $last_exp;
	$full = $exp_req - $last_exp;

	$req=$exparray[$session['user']['level']]-$exparray[$session['user']['level']-1];
	$u='<font face="verdana" size=1>'.$session['user']['experience'].' / '.($exp_req).'<br>'.grafbar($full,$left).'</font>';
	return($u);
}


// end exp bar mod

function grafbar($full,$left,$width=70,$height=5)
{
	$col2='#000000';
	if ($left<=0)
	{
		$col='#000000';
	}
	else if ($left<$full/4)
	{
		$col='#FF0000';
	}
	else if ($left<$full/2)
	{
		$col='yellow';
	}
	else if ($left>=$full)
	{
		$col='#00AA00';
		$col2='#00AA00';
	}
	else
	{
		$col='#00FF00';
	}
	if ($full==0) $full=1;
	$u = '<table cellspacing="0" style="border: solid 1px #000000" width="'.$width.'" height="'.$height.'"><tr><td width="' . ($left / $full * 100) . '%" style="background-color:'.$col.'" height="3"></td><td height="3" width="'.(100-($left / $full * 100)) .'%" style="background-color:'.$col2.'"></td></tr></table>';
	return($u);
}
?>
