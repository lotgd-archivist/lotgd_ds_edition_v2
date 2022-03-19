<?php
/*-------------------------------/
Name: dg_output.php
Autor: tcb / talion für Drachenserver (mail: t@ssilo.de)
Erstellungsdatum: 6/05 - 9/05
Beschreibung:	Anzeigefunktionen des Gildensystems; übernehmen häufig benötigte Ausgaben, vornehmlich Listen.
				Diese Datei ist Bestandteil des Drachenserver-Gildenmods (DG). 
				Copyright-Box muss intakt bleiben, bei Verwendung Mail an Autor mit Serveradresse.
/*-------------------------------*/

function dg_show_header ($txt) {

	output('`&`b`c'.$txt.'`c`b`0`n`n');

}

function dg_show_furniture ($where) {
	
	global $gid;
	
	$properties = ' deposit_guild>0 AND owner='.ITEM_OWNER_GUILD.' AND deposit1='.$gid.' AND deposit2='.($where == 'hall' ? ITEM_LOC_GUILDHALL : ITEM_LOC_GUILDEXT);
	$extra = ' ORDER BY name DESC, id ASC';
	
	$res = item_list_get ( $properties , $extra , true , ' name,description,id,furniture_guild_hook ' );
					
	$int_furniture_count = db_num_rows($res);
	
	if($int_furniture_count > 0) {
		output('<div style="float:right;width:250px;margin:10px;border:1px solid #FFFFFF;padding:5px;">`&`bMobiliar:`b',true);
	}
	
	$hooks = array();
	
	while($item = db_fetch_assoc($res)) {
			
		output("`n- `&$item[name]`0 (`i$item[description]`i)");

		if(!empty($item['furniture_guild_hook']) && !$hooks[$item['furniture_guild_hook']]) {
			$hooks[$item['furniture_private_hook']] = true;
			addnav($item['name'],'furniture.php?item_id='.$item['id']);
		}

	}
	if($int_furniture_count > 0) {
		output('</div>',true);
	}

}

function dg_show_builds ($gid,$actions=false,$admin_mode=0) {
	
	global $dg_builds,$dg_types,$dg_child_types,$dg_build_levels;
	
	$guild = &dg_load_guild($gid,array('build_list','type','gold','gems','points'));
	
	$out = array('','');	// Um zwischen typeneigenen und fremden Ausbauten zu trennen
				
	$count = 0;
	
	$recent_build = $guild['build_list'][0];
	
	if($admin_mode) {
		$link = 'dg_su.php?op=edit&subop=save_builds&gid='.$gid;
		output('<form action="'.$link.'" method="POST">',true);
		addnav('',$link);
		if($guild['build_list'][0][0]) {
			$recent_form = '<select name="recent" size="1">';
			for($days=$guild['build_list'][0][1];$days>-1;$days--) { 
				$recent_form .= '<option value="'.$days.'" '.($days==$guild['build_list'][0][1]?'selected="selected"':'').'>Noch '.$days.' Tage</option>';
			}
			$recent_form .= '</select>';
		}
	}
			
	foreach($dg_builds as $k=>$b) {
		
		if(!dg_build_is_allowed($gid,$k)) {continue;}
	
		$count++;
		$which = 1;
		$lvl = ($guild['build_list'][$k]) ? $guild['build_list'][$k] : 0;					
		$max_lvl = 3;
		if( $dg_builds[$k]['special_types'] === true )
		{
			$max_lvl = DG_BUILD_MAX_LVL;
			$which=0;	
		}
		else if( in_array($guild['ptype'],$dg_builds[$k]['special_types']) ) 
		{
			$max_lvl = DG_BUILD_MAX_LVL;
			$which=0;
		}
				
		$costs = &dg_get_build_cost($guild['ptype'],$k,$lvl);
		
		if($admin_mode) {
			
			$lvl_form = '<select name="build'.$k.'" size="1">';
			foreach($dg_build_levels as $l=>$name) {
				//if($l > 0) {
					$lvl_form .= '<option value="'.$l.'" '.($l==$lvl?'selected="selected"':'').'>'.$l.' : '.$name.'</option>';
				//}
			}
			$lvl_form .= '</select>';
			
		}
								
		$out[$which] .= '<tr class="'.($count%2?"trlight":"trdark").'"><td>`b'.$b['color'].$b['name'].'`0`b</td>
						<td>'.($admin_mode?$lvl_form.' ':'').$dg_build_levels[$lvl].(($recent_build[0] == $k)?' `i(Im Ausbau!)`i'.($admin_mode?$recent_form:''):'').(($lvl >= $max_lvl)?' `bMaximum erreicht!`b ':'').'</td>
						<td>'.$costs['gp'].'</td>
						<td>'.$costs['gold'].'</td>
						<td>'.$costs['gems'].'</td>
						<td>'.$costs['days'].' Tage</td>';
		
		if( $actions ) {
			$out[$which] .= '<td>';
			if(!$recent_build[0] && $lvl < $max_lvl) {
				if($guild['points'] >= $costs['gp'] && $guild['gold'] >= $costs['gold'] && $guild['gems'] >= $costs['gems']) {
					$out[$which] .= ' [ '.create_lnk('`@Beginnen!`0','dg_main.php?op=in&subop=builds&act=start&type='.$k).' ]`n';
				}
				else {
					$out[$which] .= 'Zu teuer!';
				}
							
			}	// END if kein Ausbau in Arbeit
			
			if($lvl > 0) {
				
				$out[$which] .= ' [ '.create_lnk('`$Abreißen!`0','dg_main.php?op=in&subop=builds&act=del&type='.$k,true,false,'Diesen Ausbau wirklich komplett abreißen?').' ] ';
				
			}
			
			$out[$which] .= '</td>';						
		}	// END if lvl < max
					
	}	// END foreach
				
	output('<table bgcolor="#999999" border="0" cellpadding="3" cellspacing="1"><tr class="trhead"><td>Ausbau</td><td>Status</td><td>Gildenpunkte</td><td>Gold</td><td>Edelsteine</td><td>Dauer</td>'.(($actions)?'<td>Bauen?</td>':'').'</tr>',true);
	
	if($out[0] != '') {
		output('<tr class="trhead"><td colspan="7">`bSpezialausbauten '.$dg_child_types[$guild['type']][0].':`b</td></tr>',true);		
		output($out[0],true);
	}
	
	if($out[1] != '') {
		output('<tr class="trhead"><td colspan="7">`bSonstige Ausbauten:`b</td></tr>',true);
		output($out[1],true);
	}
	
	if($admin_mode) {
		output('<tr class="trhead"><td colspan="7" align="right"><input type="submit" value="Speichern"></form></td></tr>',true);
	}
	
	output('</table>',true);
		
}

function dg_show_state_info ($gid) {
	
	$guild = &dg_load_guild($gid,array('points','gold','gems'));
	
	$int_max_regalia = getsetting('dgmaxregalia',15);
	
	output('<table align="center" border="0" cellspace="5" cellpadding="5"><tr class="trhead"><td>`bGildenpunkte: '.$guild['points'].' | Gold: '.$guild['gold'].' | Edelsteine: '.$guild['gems'].' | Insignien: '.$guild['regalia'].' / '.$int_max_regalia.'`b</td></tr></table>`n',true);
	
}

// admin_mode: 0 Keine Rechte, 1 Ränge setzen, 2 entlassen / aufnehmen, 4 Nur Usereditorlink
function dg_show_member_list ($gid,
			$admin_mode = 0,
			$mail=true,
			$bio=true,
			$online=true,
			$orderby='guildfunc DESC, guildrank ASC, dragonkills DESC, name ASC') {
	
	global $session,$dg_funcs;
	
	$guild = dg_load_guild($gid,array('ranks','founder'));
	
	$out = '';
	$sql = 'SELECT acctid,name,login,sex,guildfunc,guildrank,loggedin,dragonkills,activated,laston FROM accounts WHERE guildid='.$gid.((!$admin_mode)?' AND guildfunc!='.DG_FUNC_APPLICANT:'').' ORDER BY '.$orderby;
	$res = db_query($sql);
	
	if(!db_num_rows($res)) {return(false);}	
	
	$out = '<table bgcolor="#999999" border="0" cellpadding="3" cellspacing="1"><tr class="trhead"><td>Nr.</td><td>Name</td><td>Rang</td><td>Funktion</td><td>Drachenkills</td>'.(($online)?'<td>Status</td>':'');
	$out .= ($admin_mode) ? '<td>Aktionen</td>' : '';
	$out .= '</tr>';
			
	$count = 1;
	
	while($m = db_fetch_assoc($res)) {
		
		$maillink = '';
		$biolink = '';
		
		if($mail) {		
			$maillink = "mail.php?op=write&to=".rawurlencode($m['login']);
			addnav("",$maillink);
		}
		if($bio) {
			$biolink = "bio.php?char=".rawurlencode($m['login']) . "&ret=".URLEncode($_SERVER['REQUEST_URI']);
			addnav("",$biolink);
		}				
				
		$out .= '<tr class="'.($count%2?"trlight":"trdark").'"><td>'.$count.'</td>';
		$out .= '<td>'.(($mail)?'<a href="'.$maillink.'" target="_blank" onClick="'.popup($maillink).';return false;"><img src="images/newscroll.GIF" width="16" height="16" alt="Mail schreiben" border="0"></a>':'').' '.(($bio)?'<a href="'.$biolink.'">':'').$m['name'].(($bio)?'</a>':'').'</td>';
		$out .= '<td>'.$guild['ranks'][$m['guildrank']][$m['sex']].' ('.$m['guildrank'].')</td>';
		$out .= '<td>'.( ($m['guildfunc']) ? $dg_funcs[$m['guildfunc']][$m['sex']] : 'Keine' ).($m['acctid']==$guild['founder']?' `i(Gründer'.($m['sex']?'in':'').')`i':'').'</td>';
		$out .= '<td>'.$m['dragonkills'].'</td>';
		if($online) $out .= '<td>'.( user_get_online(0,$m) ? '`@Online' : '`4Offline' ).'`0</td>';
		
		if($admin_mode) {
			$out .= '<td>';
		
			if($admin_mode == 4 && su_check(SU_RIGHT_EDITORUSER)) {
				
				$out .= create_lnk('In Usereditor laden','user.php?op=edit&userid='.$m['acctid']);
				
			}
			else {
				if($m['guildfunc'] != DG_FUNC_APPLICANT) {
					$out .= create_lnk('Ändern','dg_main.php?op=in&subop=member_edit&acctid='.$m['acctid']);
				}
				
				if($admin_mode >= 2 || $admin_mode < 3) {
					if($m['guildfunc'] == DG_FUNC_APPLICANT) {
						$out .= '`n'.create_lnk('Aufnehmen','dg_main.php?op=in&subop=members&act=accept_applicant&acctid='.$m['acctid']);
						$out .= '`n'.create_lnk('Ablehnen','dg_main.php?op=in&subop=members&act=refuse_applicant&acctid='.$m['acctid']);
					}
					else {
						if($m['guildfunc'] <= $session['user']['guildfunc'] && $m['acctid'] != $session['user']['acctid'] && $m['acctid'] != $guild['founder']) {
							$out .= '`n'.create_lnk('Entlassen','dg_main.php?op=in&subop=members&act=fire&acctid='.$m['acctid'],true,false,'Bist du dir sicher, dieses Mitglied entlassen zu wollen?');
						}
					}	// END wenn Mitglied
				}	// END wenn Adminmode >= 2

			
			}	// END admin_mode != 4
			
			$out .= '</td>';
		}	// END if admin_mode
		
		$out .= '</tr>';
		
		$count++;
			 
	}	// END while
	
	$out .= '</table>';
	
	output($out,true);

}	

function dg_show_transfer_list ($gid,$acctid=0,$old=false) {

	$guild = &dg_load_guild($gid,array('transfers'));
							
	$out = '<table bgcolor="#999999" border="0" cellpadding="3" cellspacing="1"><tr class="trhead"><td>Name</td><td>Gold ein-/ausgezahlt</td><td>Edelsteine ein-/ausgezahlt</td><td>Ist Mitglied?</td></tr>';
	
	if(!$acctid) {
		
		if(!is_array($guild['transfers'])) {$out .= '<tr><td colspan="4">`iKeine Transfers vorhanden!</td></tr>';}
		
		else {
		
			$ids = array_keys($guild['transfers']);
			$id_str = implode(',',$ids);
			$names = array();
			
			$sql = 'SELECT name,acctid,guildid,guildfunc FROM accounts WHERE acctid IN ('.$id_str.') '.(!$old ? ' AND guildid='.$gid.' AND guildfunc!='.DG_FUNC_APPLICANT : '');
			$res = db_query($sql);
			while($a = db_fetch_assoc($res)) {
				$names[$a['acctid']] = $a;				
			}
			$i=0;	
			foreach($guild['transfers'] as $k=>$t) {
				$i++;
				
				$name = $names[$k]['name'];
				
				// Gleich mal aufräumen..
				if( ( $name == '' || !isset($name) ) && $old ) {
					unset($guild['transfers'][$k]);
				}
				// END gleich mal aufräumen
				
				if($name != '') {
												
					$out .= '<tr class="'.($i%2?"trlight":"trdark").'"><td>'.$name.'</td><td>'.
						(($t['gold_in'])?$t['gold_in']:'0').' / '.
						(($t['gold_out'])?$t['gold_out']:'0').
						'</td><td>'.
						(($t['gems_in'])?$t['gems_in']:'0').' / '.
						(($t['gems_out'])?$t['gems_out']:'0').'</td>';
					$out .= '<td>'.($names[$k]['guildid']==$gid && $names[$k]['guildfunc']!=DG_FUNC_APPLICANT?'Ja':'Nein').'</td></tr>';		
				}
			}
		}
		
	}
	else {
		$t = &$guild['transfers'][$acctid];
		if(!$t) {$out .= '<tr class="trlight"><td  colspan="4">`iKeine Transfers vorhanden!</td></tr>';}
		else {
			$sql = 'SELECT name FROM accounts WHERE acctid = '.$acctid.' AND guildid='.$gid;
			$res = db_query($sql);
			$name = db_fetch_assoc($res);
					
			$out .= '<tr class="trlight"><td>'.$name['name'].'</td><td>'.
					(($t['gold_in'])?$t['gold_in']:'0').' / '.
					(($t['gold_out'])?$t['gold_out']:'0').
					'</td><td>'.
					(($t['gems_in'])?$t['gems_in']:'0').' / '.
					(($t['gems_out'])?$t['gems_out']:'0').'</td></tr>';		
		}
	}
	
	$out .= '</table>';
	output($out,true);
	
}

function dg_show_ranks ($gid,$admin_mode=0) {
	
	global $session;
	
	$guild = &dg_load_guild($gid,array('ranks'));
		
	output('<table bgcolor="#999999" border="0" cellpadding="3" cellspacing="1"><tr class="trhead"><td>Nummer</td><td> <img src="images/male.gif"> </td><td> <img src="images/female.gif"> </td>'.($admin_mode ? '<td>Aktion</td>':'').'</tr>',true);
			
	foreach($guild['ranks'] as $k=>$v) {
		
		output('<tr class="'.($k%2?"trlight":"trdark").'" valign="top"><td align="center"  valign="middle">'.$k.'</td><td>'.$v[0],true);
		if($admin_mode) {
			rawoutput('<form method="POST" action="dg_main.php?op=in&subop=ranks&act=save&nr='.$k.'"><input type="text" name="man" value="'.$v[0].'" size="20" maxlength="25">');
		}
		output('</td><td>'.$v[1],true);
		if($admin_mode) {
			rawoutput('<br /><input type="text" name="woman" value="'.HTMLEntities($v[1]).'" size="20" maxlength="25"></td><td valign="middle"><input type="submit" value="Speichern"></form>');
			addnav('','dg_main.php?op=in&subop=ranks&act=save&nr='.$k);
		}
		output('</td></tr>',true);
						
	}
	
	output('</table>',true);;
	
}

function dg_show_bio (&$char) {
	
	global $dg_funcs;
	
	if($char['guildfunc'] == DG_FUNC_APPLICANT || !$char['guildid']) {return;}
	
	$guild = dg_load_guild($char['guildid'],array('name','ranks','guildid','founder'));
	
	$out .= '`n<a href="dg_main.php?op=show_guild_bio&gid='.$guild['guildid'].'">`@'.$guild['name'].'</a>';
	if($guild['top_repu']) {
		$out .= ' (Zur Zeit angesehenste Gilde '.getsetting('townname','Atrahor').'s!)';
	}
	$out .= '`n`^Rang: `n`@'.$guild['ranks'][$char['guildrank']][$char['sex']];
	$out .= '`n`^Posten: `n`@'.$dg_funcs[$char['guildfunc']][$char['sex']];
	if($guild['founder'] == $char['acctid']) {
		$out .= ' `i(Gründer'.($char['sex']?'in':'').')`i';
	}
			
	addnav('','dg_main.php?op=show_guild_bio&gid='.$char['guildid']);
	
	output($out,true);
		
}

function dg_show_guild_bio ($gid) {
	
	global $dg_child_types,$profs,$ret_page;
				
	$guild = &dg_load_guild($gid,array('name','bio','founded','type','ranks','founder','rules','professions_allowed','regalia','reputation','top_repu'));
	
	$count = dg_count_guild_members($gid);
	
	dg_show_header('Profil der Gilde '.$guild['name'].'');
	output('`@Typ: '.$dg_child_types[$guild['type']][1].$dg_child_types[$guild['type']][0].'`0`n
			`@Gründung: `^'.getgamedate($guild['founded']).'`n
			`@Insignien: `^'.$guild['regalia'].'`n
			`@Ansehen beim König: `^'.dg_get_reputation($gid).($guild['top_repu'] ? ' (Angesehenste Gilde!)' : '').'`n'
			,true);
	output('`n`n`@`bBio:`b`n`n`^'.closetags($guild['bio'],'`b`c`i').'`n`n`@`bRegeln der Gilde:`b`n`n`^'.$guild['rules'].'',true);
	
	if( strlen($guild['professions_allowed']) > 1) {
		$prof_list = explode(',',$guild['professions_allowed']);
		output('`n`n`@`bDie Gilde ist nur für Angehörige dieser Berufsgruppen zugänglich:`b`^ ');
		foreach($prof_list as $p) {
			if($p) {
				output($profs[$p][0].' ');
			}
		}
	}	
	
	output('`n`n`^'.$count.' Mitglieder:`n`n',true);
	dg_show_member_list($gid,0,true,false,false);
	
	$out = '`n`n`@Letzte Leistungen (und Niederlagen) von '.$guild['name'].'`^';
	$result = db_query('SELECT * FROM news WHERE guildid='.$guild['guildid'].' ORDER BY newsdate DESC,newsid ASC LIMIT 30');
	$odate="";
	for ($i=0;$i<db_num_rows($result);$i++){
		$row = db_fetch_assoc($result);
		if ($odate!=$row['newsdate']){
			$out .= '`n`b`^'.strftime('%A, %e. %B',strtotime($row['newsdate'])).'`b`n';
			$odate=$row['newsdate'];
		}
		$out .= '`^'.$row['newstext'].'`n';
	
	}
			
	output($out,true);
			
}


// admin_mode: 0 Keine Rechte, 1 Gilden zulassen / zurückweisen, 2 Alle Rechte
// diplo: 0 Keine Dilpomatie-Anzeige, 1 nur Anzeige, 2 nur ANgriff starten, 3 Verträge ändern
function dg_show_guild_list ($admin_mode = 0,
			$actions=false,			
			$orderby='guildid ASC',
			$bio=true,
			$diplo=0) {
	
	global $session,$dg_states,$dg_points,$dg_child_types,$dg_session_copy;
					
	$out = '';
	dg_load_guild(0,array('name','founded','state','type','immune_days','guildid','treaties','last_state_change','professions_allowed','guildwar_allowed','building_vars','war_target'));
			
	if( !count($session['guilds']) ) {output('`n`n`iZur Zeit sind keine Gilden vorhanden!`i`n`n');return(false);}	
	
	if($diplo) {
		$guild = &$session['guilds'][$session['user']['guildid']];
		$treaties = &$guild['treaties'];
		
		// Feststellen, ob eine andere Gilde gerade einen Angriff auf uns laufen hat. Wenn ja, keine Statusänderungen
		// bei dieser Gilde möglich
		$sql = 'SELECT guildid FROM dg_guilds WHERE war_target='.$session['user']['guildid'];
		$res = db_query($sql);
		if(db_num_rows($res)) {
			
			$arr_warguilds = db_create_list($res, 'guildid');
			
		}
		
	}
	
	if($admin_mode) {
		
		$sql = 'SELECT a.acctid,a.guildfunc,a.guildid FROM accounts a LEFT JOIN dg_guilds g ON g.guildid=a.guildid WHERE a.guildid > 0 AND a.guildfunc!='.DG_FUNC_APPLICANT;	
		$res2 = db_query($sql);
		
		$guilds_valid = array();
		
		while($a = db_fetch_assoc($res2)) {
			$guilds_valid[$a['guildid']]['membercount']++;	
				
			if($a['guildfunc'] == DG_FUNC_LEADER) {
				$guilds_valid[$a['guildid']]['leader_count']++;
			}
		
		}
		
	}
	
	ksort($session['guilds']);
	
	$out = '<table bgcolor="#999999" border="0" cellpadding="3" cellspacing="1"><tr class="trhead"><td>Name</td><td>Typ</td><td>Gegründet</td><td>Status</td>';
	$out .= ($diplo) ? '<td>Vertrag</td>' : '';
	$out .= ($actions || $diplo > 1) ? '<td>Aktionen</td>' : '';
	$out .= ($admin_mode) ? '<td>Statusänderung</td><td>Valide?</td><td>Admin-Aktionen</td>' : '';
	$out .= '</tr>';
	
	$count = 1;
	
	foreach($session['guilds'] as $g) {
	
		if($g['guildid'] == $session['user']['guildid'] && $diplo) {continue;}
		
		$biolink = ( ($bio) ? '<a href="dg_main.php?op=show_guild_bio&gid='.$g['guildid'].'">' : '' );
		$biolink .= $g['name'];
		$biolink .= ( ($bio) ? '</a>' : '' );
		if($bio) {addnav('','dg_main.php?op=show_guild_bio&gid='.$g['guildid']);}
		$out .= '<tr class="'.($count%2?"trlight":"trdark").'">';
		$out .= '<td>'.$biolink.'</td>';
		$out .= '<td>'.$dg_child_types[$g['type']][1].$dg_child_types[$g['type']][0].'`0</td>';
		$out .= '<td>'.getgamedate($g['founded']).'</td>';
		$out .= '<td align="center">'.$dg_states[$g['state']].'</td>';
		
		if($diplo) {
						
			$out .= '<td>';			
			if($treaties[ $g['guildid'] ][0] == DG_TREATY_WAR_SELF) {
				$out.='`4Krieg`0';
			}
			elseif($treaties[ $g['guildid'] ][0] == DG_TREATY_WAR_OTHER) {
				$out.='`4Krieg`0';
			}
			elseif($treaties[ $g['guildid'] ][0] == DG_TREATY_PEACE_SELF) {
				$out.='`@Frieden`0';
			}
			elseif($treaties[ $g['guildid'] ][0] == DG_TREATY_PEACE_OTHER) {
				$out.='`@Frieden`0';
			}
			else {
				$out.='Neutral';
			}
			if($guild['war_target'] == $g['guildid']) {
				$out.=' `i(Angriff läuft)`i';
			}
			if($treaties[ $g['guildid'] ][1] == 1) {
				$out .= '`i(Angebot offen)`i';
			}
			$out .= '</td>';
		}
						
		if($actions || $diplo > 1) {	
			
			$out .= '<td>';
			
			if($diplo > 1) {
														
				if($g['state'] == DG_STATE_ACTIVE && !$arr_warguilds[$g['guildid']] && $guild['war_target'] != $g['guildid']) {
					
					// Bei Krieg
					if($treaties[ $g['guildid'] ][0] == DG_TREATY_WAR_SELF 
					|| $treaties[ $g['guildid'] ][0] == DG_TREATY_WAR_OTHER) {
						
						$link = 'dg_main.php?op=in&subop=treaties&act=neutral&target='.$g['guildid'];
						$out.='<a href="'.$link.'"> Neutral</a> | ';
						addnav('',$link);
																
						if($guild['guildwar_allowed'] && $guild['war_target'] == 0 && $g['guildwar_allowed']) {
						
							if($g['immune_days'] > 0) {
								$out .= '`iimmun`i';
							}
							else {
								$link = 'dg_main.php?op=in&subop=war&act=start&target='.$g['guildid'];
								$out.='<a href="'.$link.'">ANGRIFF ('.$dg_points['war_cost'].' Gildenpunkte)</a>';
								addnav('',$link);
							}
							
						}
						
						elseif($guild['war_target'] == $g['guildid']) {
							$link = 'dg_main.php?op=in&subop=war&act=cancel&target='.$g['guildid'];
							$out.='<a href="'.$link.'">ANGRIFF beenden</a>';
							addnav('',$link);						
						}
						
												
					}
					// END Krieg
					
					// Bei Frieden
					elseif($treaties[ $g['guildid'] ][0] == DG_TREATY_PEACE_SELF 
					|| $treaties[ $g['guildid'] ][0] == DG_TREATY_PEACE_OTHER
					&& $diplo >= 3) {
						
						// Friedensangebot am Laufen
						if($treaties[ $g['guildid'] ][1] == 1) {
							
							// Wenn Angebot von anderer Seite kommt:
							if($treaties[ $g['guildid'] ][0] == DG_TREATY_PEACE_OTHER) {
								$link = 'dg_main.php?op=in&subop=treaties&act=accept_peace&target='.$g['guildid'];
								$out.='<a href="'.$link.'">Angebot annehmen</a> | ';
								addnav('',$link);
								$link = 'dg_main.php?op=in&subop=treaties&act=refuse_peace&target='.$g['guildid'];
								$out.='<a href="'.$link.'">Angebot ablehnen</a>';
								addnav('',$link);							
							}

						}
						else {	// sonst: -> Neutral
														
							$link = 'dg_main.php?op=in&subop=treaties&act=neutral&target='.$g['guildid'];
							$out.='<a href="'.$link.'">Neutral</a> | ';
							addnav('',$link);
							
							$link = 'dg_main.php?op=in&subop=guild_talk&target='.$g['guildid'];
							$out.='<a href="'.$link.'">Gespräch</a>';
							addnav('',$link);														
							
						}
						
					}	// END Frieden
					
					else {	// Neutral
						
						$link = 'dg_main.php?op=in&subop=treaties&act=war&target='.$g['guildid'];
						$out.='<a href="'.$link.'">Krieg</a> | ';
						addnav('',$link);
						
						$link = 'dg_main.php?op=in&subop=treaties&act=peace&target='.$g['guildid'];
						$out.='<a href="'.$link.'">Frieden</a>';
						addnav('',$link);														
												
					}
									
				}	// END if STATE_ACTIVE
						
			}	// END if diplo
			
			
			if($actions) {							
				//if($g['state'] == DG_STATE_ACTIVE) {
					if($session['user']['guildid'] == $g['guildid']) {
					
						if($session['user']['guildfunc'] == DG_FUNC_APPLICANT) {
							
							$out .= '<a href="dg_council.php?op=apply&subop=cancel&gid='.$g['guildid'].'">Bewerbung zurückziehen</a>';
							addnav('','dg_council.php?op=apply&subop=cancel&gid='.$g['guildid']);
							
						}
						
					}
					elseif($session['user']['guildid'] == 0) {	// noch kein Mitglied, noch keine Bewerbung
						if(strlen($g['professions_allowed']) == 0 || strstr($g['professions_allowed'],$session['user']['profession'].',')) {
							$out .= '<a href="dg_council.php?op=apply&gid='.$g['guildid'].'">Bewerben</a>';
							addnav('','dg_council.php?op=apply&gid='.$g['guildid']);
						}
					}
				
				//}	// END if STATE_ACTIVE
			}	// END if actions
			
			$out .= '</td>';
					
		}	// END if actions | diplo
				
		if($admin_mode) {
			
			//$str = dg_calc_strength(array($g['guildid']));
//			$out .= '<td>'.round($str[$g['guildid']],1).'</td>';
			
			$out .= '<td>'.($g['last_state_change']!='0000-00-00 00:00:00' ? date('d. m. Y',strtotime($g['last_state_change'])) : 'Frisch gegründet').'</td>';
									
			$out .= '<td>';
			
			if($guilds_valid[$g['guildid']]['leader_count'] > 0 && $guilds_valid[$g['guildid']]['membercount'] >= getsetting('dgminmembers',3)) {
				$out .= '`@Ja`0';					
			}
			else {
				$out .= '`4Nein`0';
			}
			
			$out .= '</td>';
								
			$out .= '<td>';
			$out .= '<a href="dg_su.php?op=logs&gid='.$g['guildid'].'">Logs</a>';
			addnav('','dg_su.php?op=logs&gid='.$g['guildid']);
			
			if($admin_mode >= 2) {
				$out .= '|<a href="dg_su.php?op=edit&gid='.$g['guildid'].'">Edit</a>';
				addnav('','dg_su.php?op=edit&gid='.$g['guildid']);
				
				$out .= '|<a href="dg_su.php?op=delete&gid='.$g['guildid'].'" onClick="return confirm(\'Willst du diese Gilde wirklich löschen?\');">Del</a>';
				addnav('','dg_su.php?op=delete&gid='.$g['guildid']);
			}
			
			if($g['state'] == DG_STATE_INACTIVE) {
				$out .= '|<a href="dg_su.php?op=activate&gid='.$g['guildid'].'">Aktivieren</a>';
				addnav('','dg_su.php?op=activate&gid='.$g['guildid']);
		
			}
			else {
				$out .= '|<a href="dg_su.php?op=deactivate&gid='.$g['guildid'].'" onClick="return confirm(\'Willst du diese Gilde wirklich deaktivieren?\');">Deaktivieren</a>';
				addnav('','dg_su.php?op=deactivate&gid='.$g['guildid']);
							
			}
			$out .= '|<a href="dg_main.php?op=in&gid='.$g['guildid'].'">Zum HQ</a>';
			addnav('','dg_main.php?op=in&gid='.$g['guildid']);
			$out .= '</td>';
					
		}	// END if admin_mode
		
		$out .= '</tr>';
		
		$count++;
		 
	}	// END while
	
	$out .= '</table>';
	
	output($out,true);

}	

// Entnommen aus hof.php
function dg_show_hof ($title, $sql, $none=false, $foot=false, $data_header=false, $tag=false){
	global $session, $subop, $order;
				
	$gpp = 50;
	$count_sql = 'SELECT COUNT(*) AS c FROM dg_guilds WHERE state!='.DG_STATE_INACTIVE;
			
	$result = db_query($count_sql);
	$row = db_fetch_assoc($result);
	$totalguilds = $row['c'];
	
	$page = 1;
	if ($_GET['page']) $page = (int)$_GET['page'];
	$pageoffset = $page;
	if ($pageoffset > 0) $pageoffset--;
	$pageoffset *= $gpp;
	$from = $pageoffset+1;
	$to = min($pageoffset+$gpp, $totalguilds);
	$limit = $pageoffset.','.$gpp;
		
	addnav('Sortieren nach');
	addnav('Besten','dg_council.php?op=hof&subop='.$subop.'&page='.$page);
	addnav('Schlechtesten','dg_council.php?op=hof&subop='.$subop.'&order=asc&page='.$page);
	addnav("Seiten");
	for($i = 0; $i < $totalguilds; $i += $gpp) {
		$pnum = ($i/$gpp+1);
		$min = ($i+1);
		$max = min($i+$gpp,$totalguilds);
		addnav('Seite '.$pnum.' ('.$min.'-'.$max.')', 'dg_council.php?op=hof&subop='.$subop.'&order='.$order.'&page='.$pnum);
	}
	addnav('Sonstiges');
	addnav('Zurück zum Gildenviertel','dg_main.php');
		
	dg_show_header('Ruhmeshalle der Gilden (Seite '.$page.': '.$from.'-'.$to.')');
	
	output('`c'.$title,true);
	
	output('`n<table cellspacing="2" cellpadding="3" align="center"><tr class="trhead">',true);
	output('<td>`bRang`b</td><td>`bName`b</td>', true);
	if ($data_header !== false) {
		for ($i = 0; $i < count($data_header); $i++) {
			output("<td>`b".$data_header[$i]."`b</td>", true);
		}
	}
	if(!is_array($sql)) {
		$result = db_query($sql) or die(db_error(LINK));
	}
	$count = (is_array($sql) ? sizeof($sql) : db_num_rows($result));
	
	if ($count==0){
		$size = ($data_header === false) ? 2 : 2+count($data_header);
		if ($none === false) $none = "Keine Gilden gefunden";
		output('<tr class="trlight"><td colspan="'. $size .'" align="center">`&' . $none .'`0</td></tr>',true);
	} else {
		for ($i=0;$i<$count;$i++){
		
			if(!is_array($sql)) {$row = db_fetch_assoc($result);}
			else {$row = $sql[$i];}
			
			if ($row['guildid']==$session['user']['guildid']){
				//output("<tr class='hilight'>",true);
				output('<tr bgcolor="#005500">',true);
			} else {
				output('<tr class="'.($i%2?"trlight":"trdark").'">',true);
			}
			output('<td>'.($i+$from).'.</td><td>`&'.$row['name'].'`0</td>',true);
			if ($data_header !== false) {
				for ($j = 0; $j < count($data_header); $j++) {
					$id = 'data' . ($j+1);
					$val = $row[$id];
					if ($tag !== false) $val = $val . " " . $tag[$j];
					output('<td align="right">'.$val.'</td>',true);
				}
			}
			output("</tr>",true);
		}
	}
	output("</table>", true);
	if ($foot !== false) output('`n`c'.$foot.'`c');
}

function dg_show_hitlist ($gid,$admin_mode = false) {
	
	global $dg_funcs;
	
	$g = &dg_load_guild($gid,array('hitlist','treaties'));
	
	$ids = '';
	
	if(!is_array($g['hitlist']) || sizeof($g['hitlist']) == 0) {
		output('`iKeine Aufträge vorhanden!`i');
		return;
	}
	
	foreach($g['hitlist'] as $victim => $o) {
		$ids .= ','.$victim;
	}
	
	$sql = 'SELECT a.acctid,a.name,a.dragonkills,a.level,a.sex,a.guildid,a.guildfunc,g.name AS guildname FROM accounts a LEFT JOIN dg_guilds g ON g.guildid=a.guildid WHERE acctid IN (-1'.$ids.') ORDER BY dragonkills DESC,level DESC,name ASC,acctid ASC'; 
	$res = db_query($sql);
	
	$out = '<table bgcolor="#999999" border="0" cellpadding="3" cellspacing="1"><tr class="trhead"><td>Name</td><td>Gilde (Funktion) / Vertrag</td><td>DKs</td><td>Level</td><td>Kopfgeld</td><td>Datum</td>'.($admin_mode ? '<td>Aktionen</td>':'');
	
	$counter = 0;
	
	while($a = db_fetch_assoc($res)) {
			
		$out .= '<tr class="'.($counter % 1 ? 'trlight' : 'trdark').'">
					<td>'.$a['name'].'`&</td>
					<td>';
		if($a['guildname']!='') {
			$out .= $a['guildname'].'`& ('.$dg_funcs[$a['guildfunc']][$a['sex']].')';
			$treaty = dg_get_treaty($g['treaties'][$a['guildid']]);
			
			if($treaty == 1) {$out.=' / `@Frieden`&';}
			elseif($treaty == -1) {$out.=' / `4Krieg`&';}
			elseif($treaty == 0) {$out.=' / Neutral`&';}
		}
		else {
			$out .= 'Keine';
		}	
				
		$out .= '</td>
				<td>'.$a['dragonkills'].'</td>
				<td>'.$a['level'].'</td>
				<td>`^'.$g['hitlist'][$a['acctid']]['bounty'].'`& Gold</td>
				<td>'.getgamedate($g['hitlist'][$a['acctid']]['date']).'</td>';
				
		if($admin_mode) {
			$link = 'dg_main.php?op=in&subop=hitlist&act=del&acctid='.$a['acctid'];
			$out .= '<td><a href="'.$link.'">Entfernen</a></td>';
			addnav('',$link);
		}
		
		$out.='</tr>';
		
		$counter++;
		
	}
			
	$out .= '</table>';
	output($out,true);
}
?>
