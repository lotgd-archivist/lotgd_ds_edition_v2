<?php
$rcomment_sections = array('village'=>1,'marketplace'=>1,'garden'=>1,'expedition_wastes'=>1,'prison'=>1);
$rcomment_sections_inside = array('prison'=>1);
$rcomment_sections_public = array('village'=>1,'marketplace'=>1,'garden'=>1,'expedition_wastes'=>1,'prison'=>1);
// Wenn auf true, wird eine Kommentarsektion auf dieser Seite angezeigt
$BOOL_COMMENTAREA = false;

// Wenn auf true, wurde vom User ein Kommentar geschrieben
$bool_comment_written = false;

// alc auf false setzten für alkoholfreie Posts (by Maris)
function addcommentary($alc=true)
{
	global $session,$REQUEST_URI,$doublepost,$rcomment_sections,$bool_comment_written;
	
	$doublepost=0;
	
	// Sektion abholen
	$section = $_POST['section'];
	
	$su_min = 1;
	
				
	if(!empty($session['user']['prefs']['comsection_nonav'][$section])) {

		$su_min = $session['user']['prefs']['comsection_nonav'][$section];	
		unset($session['user']['prefs']['comsection_nonav']);
	}
	else if(!empty($session['user']['prefs']['comsection_nav'][$section])) {

		$su_min = $session['user']['prefs']['comsection_nav'][$section];	
		unset($session['user']['prefs']['comsection_nav']);
	}
	else {
		return(false);
	}	
			
	$talkline = $_POST['talkline'];
	
	if ($_POST['insertcommentary'][$section]!==NULL &&
	trim($_POST['insertcommentary'][$section])!="") 
	{
	
		$commentary = str_replace('`n','',soap($_POST['insertcommentary'][$section]));
		
		unset($_POST['insertcommentary'][$section]);
		
		$y = strlen($commentary);
		for ($x=0;$x<$y;$x++)
		{
			if (substr($commentary,$x,1)=='`')
			{
				$colorcount++;
				if ($colorcount>=getsetting('maxcolors',10))
				{
					$commentary = substr($commentary,0,$x).preg_replace("'[`].'",'',substr($commentary,$x));
					$x=$y;
				}
				$x++;
			}
		}
				
		//Names from Userweapons & co (Code by Hadriel, seen at www.serverschlampe.de)
		//Erweitert und verändert von Maris
		if(substr($commentary,0,3)=='/me' || substr($commentary,0,1)==':' || substr($commentary,0,2)=='::')
		{
			$comcol= ($session['user']['prefs']['commentemotecolor']) ? $session['user']['prefs']['commentemotecolor'] : '&';
		}
		else
		{
			$comcol= ($session['user']['prefs']['commenttalkcolor']) ? $session['user']['prefs']['commenttalkcolor'] : '#';
		}
        // Tierart
        if($session['user']['hashorse']>0 && strstr($commentary,'%ta')!== false )
		{
			$sql='SELECT mountname FROM mounts WHERE mountid=\''.$session['user']['hashorse'].'\'';
			$mount=db_fetch_assoc(db_query($sql));
			$commentary=str_replace('%ta',$mount['mountname'].'`'.$comcol,$commentary);
		}
		else
		{
			$commentary=str_replace('%ta','',$commentary);
		}
		
		// Tiername
		if (strstr($commentary,'%tn')!== false )
		{
		$sql='SELECT hasxmount, xmountname FROM account_extra_info WHERE acctid='.$session['user']['acctid'];
		$mount=db_fetch_assoc(db_query($sql));
        if($session['user']['hashorse']>0 && $mount['hasxmount']==1)
		{
			$commentary=str_replace('%tn',$mount['xmountname'].'`'.$comcol,$commentary);
		}
		else
		{
			$commentary=str_replace('%tn','',$commentary);
		}
        }
        
        // Knappenname
		if (strstr($commentary,'%kn')!== false )
		{
		$sql='SELECT name FROM disciples WHERE master='.$session['user']['acctid'];
        $resultkn = db_query($sql) or die(db_error(LINK));
        if(db_num_rows($resultkn)>0)
		{
        $kn=db_fetch_assoc($resultkn);
        $commentary=str_replace('%kn',$kn['name'].'`'.$comcol,$commentary);
        }
        else
		{
			$commentary=str_replace('%kn','',$commentary);
		}
		}

		//Waffe
		$commentary=str_replace('%wn',$session['user']['weapon'].'`'.$comcol,$commentary);

		//Armor
		$commentary=str_replace('%rn',$session['user']['armor'].'`'.$comcol,$commentary);

		//Name
		$commentary=str_replace('%en',$session['user']['name'].'`'.$comcol,$commentary);

		//Haus
		if($session['user']['house']>0 && strstr($commentary,'%hn')!== false)
		{
			$sql='SELECT housename FROM houses WHERE houseid=\''.$session['user']['house'].'\'';
			$house=db_fetch_assoc(db_query($sql));
			$commentary=str_replace('%hn',$house['housename'].'`'.$comcol,$commentary);
		}
		else
		{
			$commentary=str_replace('%hn','',$commentary);
		}
		
		//Gilde
		if($session['user']['guildid']>0 && strstr($commentary,'%gn')!== false)
		{
 	        require_once(LIB_PATH.'dg_funcs.lib.php');
            require_once('dg_output.php');
			$guild = dg_load_guild($session['user']['guildid'],array('name','ranks','guildid','founder'));
			$commentary=str_replace('%gn',$guild['name'].'`'.$comcol,$commentary);
		}
		else
		{
			$commentary=str_replace('%gn','',$commentary);
		}

		//Partner
		if($session['user']['marriedto']>0 && strstr($commentary,'%pn')!== false)
		{
			$sql='SELECT name FROM accounts WHERE acctid=\''.$session['user']['marriedto'].'\'';
			$partner=db_fetch_assoc(db_query($sql));
			$commentary=str_replace('%pn',''.$partner['name'].'`'.$comcol,$commentary);
		}
		else
		{
			$commentary=str_replace('%pn','',$commentary);
		}
		
		//Konfigurierbare Shortcuts
		if (strstr($commentary,'%x')!== false)
		{
        $sqlex = "SELECT shortcuts FROM account_extra_info WHERE acctid=".$session[user][acctid];
		$resex = db_query($sqlex) or die(db_error(LINK));
		$rowex = db_fetch_assoc($resex);
        for ($i=0;$i<=$rowex['shortcuts'];$i++){
        if (strstr($commentary,'%x'.$i)!== false)
        {
		$commentary=str_replace('%x'.$i,$session['user']['prefs']['sx'.$i].'`'.$comcol,$commentary);
        }
        }
        }

		//END NAMES

		if (substr($commentary,0,1)!=':' &&
		substr($commentary,0,2)!='::' &&
		substr($commentary,0,3)!='/me' &&
		substr($commentary,0,4)!='/msg' &&
		($session['user']['drunkenness']>0 && $alc))
		{
			//drunk people shouldn't talk very straight.
			$straight = $commentary;
			$replacements=0;
			$int_strlen_straight = strlen($straight);
			while ($replacements/$int_strlen_straight < ($session['user']['drunkenness'])/500 )
			{
				$slurs = array('a'=>'aa','e'=>'ee','f'=>'ff','h'=>'hh','i'=>'ij','l'=>'ll','m'=>'mm','n'=>'nn','o'=>'oo','r'=>'rr','s'=>'sh','u'=>'uu','v'=>'vv','w'=>'ww','y'=>'yy','z'=>'zz');
				if (e_rand(0,9)) 
				{
					srand(e_rand());
					$letter = array_rand($slurs);
					$x = strpos(strtolower($commentary),$letter);
					if ($x!==false &&
					substr($comentary,$x,5)!='*hic*' &&
					substr($commentary,max($x-1,0),5)!='*hic*' &&
					substr($commentary,max($x-2,0),5)!='*hic*' &&
					substr($commentary,max($x-3,0),5)!='*hic*' &&
					substr($commentary,max($x-4,0),5)!='*hic*'
					)
					{
						if (substr($commentary,$x,1)<>strtolower($letter))
						{
							$slurs[$letter] = strtoupper($slurs[$letter]); 
						}
						else 
						{
							$slurs[$letter] = strtolower($slurs[$letter]);
						}
						$commentary = substr($commentary,0,$x).$slurs[$letter].substr($commentary,$x+1);
						$replacements++;
					}
				}
				else
				{
					$x = e_rand(0,strlen($commentary));
					if (substr($commentary,$x,5)=='*hic*') {$x+=5; } //output('moved 5 to $x ');
					if (substr($commentary,max($x-1,0),5)=='*hic*') {$x+=4; } //output('moved 4 to $x ');
					if (substr($commentary,max($x-2,0),5)=='*hic*') {$x+=3; } //output('moved 3 to $x ');
					if (substr($commentary,max($x-3,0),5)=='*hic*') {$x+=2; } //output('moved 2 to $x ');
					if (substr($commentary,max($x-4,0),5)=='*hic*') {$x+=1; } //output('moved 1 to $x ');
					$commentary = substr($commentary,0,$x).'*hic*'.substr($commentary,$x);
					//output($commentary."`n");
					$replacements++;
				}//end if
			}//end while
			//output("$replacements replacements (".($replacements/strlen($straight)).")`n");
			$commentary = str_replace('*hic**hic*','*hic*hic*',$commentary);
			
		}//end if
		$commentary = preg_replace("'([^[:space:]]{45,45})([^[:space:]])'","\\1 \\2",$commentary);
		if ($session['user']['drunkenness']>50 && $alc) {
			$talkline = 'lallt';
		}
		
		// do an emote if the user isn't trying to emote already.		
		// /me und :: durch : ersetzen, commentemotecolor dazu.
		$str_rest = '';
		if (substr($commentary,0,2)=='::') {
			$str_rest = substr($commentary,2);
			$commentary = ':'.'`'.$comcol.$str_rest;
		}
		else if(substr($commentary,0,1)==':') {
			$str_rest = substr($commentary,1);
			$commentary = ':'.'`'.$comcol.$str_rest;
		}
		else if(substr($commentary,0,3)=='/me') {
			$str_rest = substr($commentary,3);
			$commentary = ':'.'`'.$comcol.$str_rest;
		}
		else if (substr($commentary,0,4)=='/msg') {
			
		}
		else {
			$commentary = ":`3$talkline: `".$comcol."\\\"".$commentary."\\\"";
		}
		
		$sql = 'SELECT commentary.comment,commentary.author FROM commentary WHERE section="'.$section.'" ORDER BY commentid DESC LIMIT 1';
		$result = db_query($sql) or die(db_error(LINK));
		$row = db_fetch_assoc($result);
		db_free_result($result);
		if ($row['comment']!=stripslashes($commentary) || $row['author']!=$session['user']['acctid'])
		{
		
			// Zufallskommentare
			// by talion
			if(e_rand(1,2) == 1 && $rcomment_sections[$section]) {
				
				$weather_id = (int)getsetting('weather',1);
				
				$time = gametime();
				$hour = (int)date('H',$time);
				$month = (int)date('m',strtotime(getsetting('gamedate','')) );
				
				$section_inside = $rcomment_sections_inside[$section];
				$section_public = $rcomment_sections_public[$section];
				
				$sql = 'SELECT comment,gap,id,chance FROM random_commentary WHERE 
							(section="'.$section.'" OR 
							section="" '
							.($section_inside ? ' OR (section = "all_inside")' : ' OR (section = "all_outside")')
							.($section_public ? ' OR (section = "all_public")' : ' OR (section = "all_private")'). 
							') AND (chance > 0) AND
							(weather = '.$weather_id.' OR weather=0) AND
							(month_min <= '.$month.' AND month_max >= '.$month.') AND 
							(hour_min <= '.$hour.' AND hour_max >= '.$hour.') AND
							(rldate = CURDATE() OR rldate = "0000-00-00")
							ORDER BY RAND()';

				$res = db_query($sql);
			
				if( db_num_rows($res) ) {
					
					$history = unserialize(getsetting('rcomhistory',''));
														
					$random = e_rand(1,250);
										
					while( $c = db_fetch_assoc($res) ) {
																									
						if($c['chance'] >= $random) {	// nehmen wir
																	
							$last = false;
							
							// Keine "Doppelposts", gap bestimmt die Anzahl anderer Zufallsposts dazwischen
							if(is_array($history[$section])) {
								$start_count = sizeof($history[$section])-1;
								$max_count = max($start_count - $c['gap'],-1);
								for($i = $start_count; $i > $max_count; $i--) {
									if($history[$section][$i] == $c['id']) {$last=true;}
								}
								$i = 0;				
							}
												
							if($last == false) { 
								$sql = 'INSERT INTO commentary SET postdate=NOW(),author=1,section="'.$section.'",comment="'.addslashes($c['comment']).'"';
								db_query($sql);
								
								$history[$section][] = $c['id'];
																				
								savesetting('rcomhistory',serialize($history));
								
								break;
							}
							
						}
						
					}	// END while
					
					db_free_result($res);
						
				}
			}
			// END Zufallskommentare
		
		
			// if ($row[comment]!=$commentary || $row[author]!=$session[user][acctid]){
			$sql = "INSERT INTO commentary (postdate,section,author,comment,su_min,self)
					VALUES (now(),'$section',".$session['user']['acctid'].",\"$commentary\",".$su_min.",1)";
			db_query($sql) or die(db_error(LINK));
			
			// Stats
			user_set_stats( array( 'comments'=>'comments+1','commentlength'=>'commentlength+'.strlen($commentary) ) );
			// END Stats
			
			$bool_comment_written = true;
									
			return true;
		}
		else
		{
			$doublepost = 1;
		}
	}
	return false;
}

function viewcommentary($section,
						$message="Kommentar hinzufügen?",
						$limit=10,
						$talkline="sagt",
						$showdate=false,
						$show_addform=true,
						$specialfuncs=false,
						$long_posting=false,
						$only_rpg=false,
						$linkbios=true,
						$su_min=1) {
	global $session,$REQUEST_URI,$doublepost,$BOOL_COMMENTAREA,$BOOL_POPUP,$allownonnav,$SCRIPT_NAME;
	
	$BOOL_COMMENTAREA = true;
		
	if ($doublepost) {
		output("`\$`bDoppelpost?`b`0`n");
	}
	
	// Wenn Voreinstellung für Kommentaranzahl in den Prefs
	if(isset($session['user']['prefs']['commentlimit'][$section]) && (int)$session['user']['prefs']['commentlimit'][$section] > 0) {
		
		// Überschreibe Vorgabe
		$limit = $session['user']['prefs']['commentlimit'][$section];
		
	}
	
		
	$com=(int)$_GET[comscroll];
		$sql = "SELECT 	commentary.*,
						accounts.name,
						accounts.login,
						accounts.acctid,
						accounts.loggedin,
						accounts.location,
						accounts.laston,
						accounts.superuser,
						accounts.activated
	          FROM	 	commentary
	         INNER JOIN accounts
	            ON 		accounts.acctid = commentary.author AND accounts.locked=0
	         WHERE 		section = '$section' ".($session['disable_npc_comment'] && $only_rpg ? 'AND self=1' : '')."
	         ORDER BY	commentid DESC
	         LIMIT 		".($com*$limit).",$limit";
 
		
	$result = db_query($sql) or die(db_error(LINK));
	$counttoday=0;
	
	if(su_check(SU_RIGHT_COMMENT)) {
		if(($message=="X") || ($message==" ")) {
			$req = 'su_comment.php?op=del_comment&return='.urlencode($REQUEST_URI);
			output('<form action="'.$req.'" method="POST">',true);
		}
	}
	
	$count = db_num_rows($result);
	
	for ($i=0;$i < $count;$i++) {
		$row = db_fetch_assoc($result);
		
		// Alle Tags bis auf erlaubte Farben raus
		$row['comment'] = preg_replace('/[`][^'.regex_appoencode(1,false).']/','',$row['comment']);
		
		$commentids[$i] = $row[commentid];
		
		$date = ($showdate || $message == 'X') ? "`3(".date('d.m.y',strtotime($row['postdate'])).") " : "";
				
		$x=0;
		$ft='';
		
		/**
		*Timestamps eingefügt, aber nur wenn das Datum nicht gezeigt wird (Ausnahme: Admin)
		*/
		$timestamp='';
		if(!$showdate)
		{
			if ($session['user']['prefs']['timestamps'] || $message == 'X') $timestamp='`0['.date('H:i',strtotime($row['postdate'])).'] ';
		}
		
		$comment_length = strlen($row['comment']);
		
		for ($x=0;strlen($ft)<3 && $x<$comment_length;$x++){
			if (substr($row[comment],$x,1)=="`" && strlen($ft)==0) {
				$x++;
			}else{
				$ft.=substr($row[comment],$x,1);
			}
		}
		
		if(substr($row['comment'],0,4)=='/msg' && $row['superuser']) {
			
			$op[$i] .= $timestamp.$date.'`b`7'.str_replace('&amp;','&',HTMLEntities(substr($row[comment],4)))
						.'`b`0`n';
			if ($message=='X') {$op[$i]='`0('.$row['section'].') '.$op[$i];}
		}
		else {
			
			$link = 'bio.php?id='.$row['acctid'] . '&ret='.URLEncode($_SERVER['REQUEST_URI']);
			if (substr($ft,0,2)=='::') {$ft = substr($ft,0,2);}
			else {
				if (substr($ft,0,1)==':') {$ft = substr($ft,0,1);}
			}
			if ($ft=='::' || $ft=='/me' || $ft==':'){
				$x = strpos($row[comment],$ft);
				if ($x!==false){
								
					if ($linkbios) {
						$op[$i] .= $timestamp.$date.str_replace('&amp;','&',HTMLEntities(substr($row[comment],0,$x)))
						.'`0<a href="'.$link.'" style="text-decoration: none">`&'.$row['name'].'`0</a>`& '
						.str_replace('&amp;','&',HTMLEntities(substr($row[comment],$x+strlen($ft))))
						.'`0`n';
						addnav('',$link);
					}
					else {
						$op[$i] .= $timestamp.$date.str_replace('&amp;','&',HTMLEntities(substr($row[comment],0,$x)))
						.'`0`&'.$row['name'].'`0`& '
						.str_replace('&amp;','&',HTMLEntities(substr($row[comment],$x+strlen($ft))))
						.'`0`n';
						
					}
				}
			}
	
			if ($op[$i]=='') {
			
				if ($linkbios) {
					$op[$i] .= $timestamp.$date.'`0<a href="'.$link.'" style="text-decoration: none">`&'.$row['name'].'`0</a>`3 sagt: "`#'
					.str_replace('&amp;','&',HTMLEntities($row[comment])).'`3"`0`n';
					addnav('',$link);
				}
				else {
					$op[$i] .= $timestamp.$date.'`0`&'.$row['name'].'`0`3 sagt: "`#'
					.str_replace('&amp;','&',HTMLEntities($row[comment])).'`3"`0`n';
				}
							
			}
			
			if ($message=='X') {$op[$i]='`0('.$row['section'].') '.$op[$i];}
			$loggedin=user_get_online(0,$row);
			if ($row['postdate']>=$session['user']['recentcomments'] && !$mail_ver) {$op[$i]=($loggedin?'<img src="images/new-online.gif" alt="&gt;" width="3" height="5" align="absmiddle"> ':'<img src="images/new.gif" alt="&gt;" width="3" height="5" align="absmiddle"> ').$op[$i];}
			
		}	// END if keine /msg
	}
	$i--;
	$outputcomments=array();
	$sect="x";
	for (;$i>=0;$i--){
		$out="";
		if (su_check(SU_RIGHT_COMMENT) && ($message=="X") || ($message==" ")) {
		
			$out .= '`0<input type="checkbox" name="commentid[]" value="'.$commentids[$i].'">&nbsp;&nbsp;';
		
			$out.="[ <a href='su_comment.php?op=del_comment&commentid=$commentids[$i]&return=".urlencode($REQUEST_URI)."'>Löschen</a> ]&nbsp;";
			
			$matches=array();
			
			preg_match("/[(][^)]*[)]/",$op[$i],$matches);
			$sect=$matches[0];
		}

		$out.=$op[$i];
		if (!is_array($outputcomments[$sect])) $outputcomments[$sect]=array();
		array_push($outputcomments[$sect],$out);
	}
	ksort($outputcomments);
	reset($outputcomments);
	while (list($sec,$v)=each($outputcomments)){
				
		if (($sec!="x") && ($sec!="g")) output("`n`b$sec`b`n");
		output(implode('',$v),true);
				
	}
	
	if($session['user']['activated'] == USER_ACTIVATED_MUTE_AUTO && $session['user']['loggedin'] && $show_addform) {
	
		output('`^`bNoch hast du dich noch nicht als würdig erwiesen, hier etwas zu schreiben. Falls du dies ändern willst, wende deine
					Schritte gen `iDrachenbücherei`i auf dem Dorfplatz und durchschreite dort die Prüfung, die dich zum Bürger '.getsetting('townname','Atrahor').'s machen wird!`b`0`n');
	
	}
			
	if ($session[user][loggedin] && $show_addform && $session['user']['activated'] != USER_ACTIVATED_MUTE && $session['user']['activated'] != USER_ACTIVATED_MUTE_AUTO) {
	
		if (($message!="X") && ($message!="O")) {
			if ($talkline!="says") {$tll = strlen($talkline)+11;
			} else $tll=0;
			
			// Vorschaufunktion by talion
			if ($session['user']['prefs']['preview']) {
				rawoutput('<script type="text/javascript" language="JavaScript" src="templates/chat_prev.js">
						</script><script type="text/javascript" language="JavaScript">
						var name = \''.appoencode(addslashes($session['user']['name'])).'\';
						var ecol = \''.$session['user']['prefs']['commentemotecolor'].'\';
						var tcol = \''.$session['user']['prefs']['commenttalkcolor'].'\';
						var reg = /[`](['.regex_appoencode(1,false).'])/;
						</script>');
				output('<hr><br><b>`&Vorschau: // </b><span id="comprev"></span>',true);		
			}
			if ($long_posting==true)
			{ $max_length=600; }
            else
            { $max_length=400; }
            
			// Zu erlaubten Kommentarsektionen hinzufügen			
			if($allownonnav[$SCRIPT_NAME]) {		
				unset($session['user']['prefs']['comsection_nonav']);
				$session['user']['prefs']['comsection_nonav'][$section] = $su_min;
			}
			else {
				unset($session['user']['prefs']['comsection_nav']);
				$session['user']['prefs']['comsection_nav'][$section] = $su_min;
			}

			
			output("<form action=\"$REQUEST_URI\" method='POST'>`@$message`n
			<input name='insertcommentary[$section]' size='80' maxlength='".($max_length-$tll)."' ",true);
			if($session['user']['prefs']['preview']) {
				output(" id='comin' onkeyup='com_prev(",true);
				output("\"".$talkline."\"");
				output(");this.focus()'",true);
			}
			
			output(" >
			<input type='hidden' name='talkline' value='$talkline'>
			<input type='hidden' name='section' value='$section'>
			<input type='submit' class='button' value='Hinzufügen'>`n`0`n</form>",true);
									
			addnav("",$REQUEST_URI);			
			
		}
	
	}
	
	$req = preg_replace("'[&]?c(omscroll)?=([[:digit:]-])*'","",$REQUEST_URI)."&comscroll=";
	$req = str_replace("?&","?",$req);
	if (!strpos($req,"?")) $req = str_replace("&","?",$req);
	
	if ($count>=$limit){
		$str_lnk = $req . ($com+1);
		output("<a href=\"$str_lnk\">&lt;&lt; Vorherige</a>",true);
		addnav("",$str_lnk);
	}
	
	$str_lnk = $req . '0';
	output("&nbsp;<a href=\"$str_lnk\">Aktualisieren</a>&nbsp;",true);
	addnav("",$str_lnk);
	
	if ($com>0){
		$str_lnk = $req.($com-1);

		output(" <a href=\"$str_lnk\">Nächste &gt;&gt;</a>",true);
		addnav("",$str_lnk);
	}
	
	if(su_check(SU_RIGHT_COMMENT)) {
		if(($message!="X") && ($message!=" ")) {
			$req = 'su_comment.php?op=single_section&section='.$section;
			output('&nbsp;&nbsp;&nbsp; `&[ <a href="#" target="_blank" onClick="'.popup($req).';return false;" >Admin</a> ]',true);
		}
		else {
			output('`n`n<input type="submit" value="Markierte Löschen">',true);
			output('`n`n<input type="hidden" value="1" name="commentback"><input type="submit" value="Markierte in Beweissicherung verschieben"></form>',true);
		}
	}

	if($session['user']['loggedin'] && getsetting('enable_commentemail',0)) {
		output('&nbsp;&nbsp; `&[ <a href="#" target="_blank" onClick="'.popup('comment_funcs.php?comment_sendmail=1&section='.$section.'&limit='.$limit.'&com='.$com.'').';return false;" >An EMail</a> ]',true);
	}
	
	if($session['user']['prefs']['template'] == 'dragonslayer_1.html' && $session['user']['loggedin'] && !$BOOL_POPUP) {
		
		$link = 'comment_funcs.php?vitalchange=1&ret='.urlencode($REQUEST_URI);
		addnav('',$link);		
		output('&nbsp;&nbsp; [ <a href="'.$link.'">Vollbild '.($session['disablevital'] ? 'aus':'ein').'!</a> ]',true);
		
	}
	
	if($only_rpg) {
		$link = 'comment_funcs.php?npc_off=1&ret='.urlencode($REQUEST_URI);
		addnav('',$link);		
		output('&nbsp;&nbsp; [ <a href="'.$link.'">NichtRPG '.($session['disable_npc_comment'] ? 'ein':'aus').'!</a> ]',true);
	}
	
	if(time() - $session['modcalled'] >= 300 && getsetting('enable_modcall',0) && $session['user']['loggedin']) {
		$req = 'petition.php?callmod=1&section='.$section.'&ret='.urlencode($REQUEST_URI);
		addnav('',$req);
		output("`n`n`&[ <a href=\"$req\" onClick='return confirm(\"Wirklich Mod rufen?\");'>Moderator rufen</a> !]",true);
	}
	
	if($session['user']['loggedin'] && $message != 'X') {
			
		$link = 'comment_funcs.php?section='.$section.'&ret='.urlencode($REQUEST_URI);
		addnav("",$link);
		output('<div align="right"><form method="POST" action="'.$link.'">
					Kommentare pro Seite: 
					<select name="commentlimit" onchange="this.form.submit()">
						<option value="'.$limit.'"> '.$limit.' </option>
						<option value="10" '.($limit==10 ? 'selected' : '').'> 10 </option>
						<option value="25" '.($limit==25 ? 'selected' : '').'> 25 </option>
						<option value="50" '.($limit==50 ? 'selected' : '').'> 50 </option>
						<option value="75" '.($limit==75 ? 'selected' : '').'> 75 </option>
					</select></form></div>',true);
	}
		
	db_free_result($result);
}

function getcommentary($where,$from=0,$count=10,$showdate=false) {
	global $session,$REQUEST_URI;
			
	$sql = "SELECT commentary.*,
	               accounts.name,
	               accounts.login,
				accounts.location,
				accounts.superuser		
	          FROM commentary
	         INNER JOIN accounts
	            ON accounts.acctid = commentary.author
	         WHERE ".$where."
	         ORDER BY commentid ASC
	         LIMIT ".($from).",$count";
	$result = db_query($sql) or die(db_error(LINK));
	
	$arr_comments = array();
			
	$count = db_num_rows($result);
	
	for ($i=0;$i < $count;$i++) {
		$row = db_fetch_assoc($result);
						
		$date = ($showdate) ? "`3(".date('d.m.y',strtotime($row['postdate'])).") " : "";
				
		$x=0;
		$ft='';
		
		/**
		*Timestamps eingefügt, aber nur wenn das Datum nicht gezeigt wird
		*/
		$timestamp='';
		if(!$showdate)
		{
			if ($session['user']['prefs']['timestamps'] || $message == 'X') {
				$timestamp='`&['.date('H:i',strtotime($row['postdate'])).'] ';
			}
		}
		
		$comment_length = strlen($row['comment']);
		
		for ($x=0;strlen($ft)<3 && $x<$comment_length;$x++){
			if (substr($row[comment],$x,1)=="`" && strlen($ft)==0) {
				$x++;
			}else{
				$ft.=substr($row[comment],$x,1);
			}
		}
		
		if(substr($row['comment'],0,4)=='/msg' && su_check(SU_RIGHT_MSG)) {
			
			$row['comment'] = $timestamp.$date.'`b`7'.str_replace('&amp;','&',HTMLEntities(substr($row[comment],4)))
						.'`b`0`n';
			if ($message=='X') {$row['comment']='`0('.$row['section'].') '.$row['comment'];}
		}
		else {
						
			if (substr($ft,0,2)=='::') {$ft = substr($ft,0,2);}
			else {
				if (substr($ft,0,1)==':') {$ft = substr($ft,0,1);}
			}
			if ($ft=='::' || $ft=='/me' || $ft==':'){
				$x = strpos($row[comment],$ft);
				if ($x!==false){
								
					$row['comment'] = $timestamp.$date.str_replace('&amp;','&',HTMLEntities(substr($row[comment],0,$x)))
						.'`0`&'.$row['name'].'`0`& '
						.str_replace('&amp;','&',HTMLEntities(substr($row[comment],$x+strlen($ft))))
						.'`0`n';
						
					
				}
			}
	
			else {
			
				$row['comment'] = $timestamp.$date.'`0`&'.$row['name'].'`0`3 sagt: "`#'
					.str_replace('&amp;','&',HTMLEntities($row[comment])).'`3"`0`n';
										
			}
			
			if ($message=='X') {$row['comment']='`&('.$row['section'].') '.$row['comment'];}
		
		}	// END if keine /msg
		
		$arr_comments[$i] = $row;
		
	}	// END Schleife
	
	db_free_result($result);	
	return($arr_comments);
		
}

function insertcommentary ($author,$msg,$section,$su_min=1,$self=0) {
	
	$sql = 'INSERT INTO commentary SET 
				postdate=NOW(),author='.$author.',comment="'.addslashes($msg).'",section="'.addslashes($section).'",su_min='.$su_min.',self='.$self;
	db_query($sql);
	
}
?>
