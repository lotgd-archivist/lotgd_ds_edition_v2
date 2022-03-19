<?php
/*-------------------------------/
Name: dg_su.php
Autor: tcb / talion für Drachenserver (mail: t@ssilo.de)
Erstellungsdatum: 6/05 - 9/05
Beschreibung:	Diese Datei ist Bestandteil des Drachenserver-Gildenmods (DG). 
				Copyright-Box muss intakt bleiben, bei Verwendung Mail an Autor mit Serveradresse.
/*-------------------------------*/

require_once('common.php');
require_once(LIB_PATH.'dg_funcs.lib.php');
require_once('dg_output.php');

checkday(3);
page_header('Der Gildeneditor');

$op = ($_GET['op']) ? $_GET['op'] : '';

// Gilden komplett neu laden
//dg_load_guild(0,array(),true);

switch($op) {

	case '':
		
		addnav('Zur Grotte','superuser.php');
		addnav('Zum Weltlichen','village.php');									
				
		dg_show_guild_list(2);
		
		break;
		
	case 'edit':
		
		$gid = ($_GET['gid']) ? $_GET['gid'] : 0;
		
		if(!$gid) {redirect('dg_su.php');}
		
		$guild = &dg_load_guild($gid);
		
		if($_GET['subop'] == 'save') {
															
			foreach($_POST as $k=>$v) {
				if (isset($guild[$k])){
					$guild[$k] = $v;
				}
			}
			dg_save_guild();			
			redirect('dg_su.php?op=edit&gid='.$gid);
			
		}
		elseif($_GET['subop'] == 'save_builds') {
			
			if($_POST['recent'] && is_array($guild['build_list'][0])) {
				$guild['build_list'][0][1] = $_POST['recent'];
				if($_POST['recent'] == 0) {	// wenn abgeschlossen..
					$type = $g['build_list'][0][0];
					$guild['build_list'][$type] = min($guild['build_list'][$type]+1,12);
					$guild['build_list'][0][0] = 0;
				}
			}
												
			foreach($dg_builds as $k=>$b) {
				$guild['build_list'][$k] = (int)$_POST['build'.$k];
			}
						
			dg_save_guild();
									
			redirect('dg_su.php?op=edit&gid='.$gid);
			
		}
		
				
		dg_show_member_list($gid,4);
		output('`n`n');
		dg_show_builds($gid,false,1);
		
		$types = '0,Keiner';

		foreach($dg_child_types as $k=>$t) {
			
			$types .= ','.$k.','.$t[0].' ('.$dg_types[$t[3]]['name'].')';
			
		}
		
		$prof_list = '';
		foreach($profs as $k=>$p) {
			
			$prof_list .= $k.': '.$p[0].';`n';
			
		}
		
		output('`n`n');
				
		$edit_form = array(
						'Allgemeines,title',
						'guildid'=>'Gildenid,viewonly',
						'name'=>'Gildenname',
						'bio'=>'Gildenbio',
						'rules'=>'Gildenregeln',
						'points'=>'Gildenpunkte,int',
						'reputation'=>'Ansehen,int',
						'atk_upgrade'=>'Angriffsupgrade,enum_order,0,3',
						'def_upgrade'=>'Verteidigungsupgrade,enum_order,0,3',
						'state'=>'Gildenstatus,enum,'.DG_STATE_INACTIVE.',Inaktiv,'.DG_STATE_ACTIVE.',Aktiv',
						'founded'=>'Gildengründung,viewonly',
						'founder'=>'Gründer (Userid),int',
						'guard_hp'=>'Aktuelle Anzahl an Gildenwachen,int',
						'guard_hp_before'=>'Anzahl an Gildenwachen vor Krieg,int',
						'war_target'=>'Aktuelles Kriegsziel (Gildenid),int',
						'immune_days'=>'Verbleibende Spieltage Immunität,int',
						'regalia'=>'Insignien,int',
						'gold'=>'Gold,int',
						'gems'=>'Edelsteine,int',
						'gold_in'=>'Goldeinzahlung bisher an diesem Spieltag,int',
						'gems_in'=>'Gemeinzahlung bisher an diesem Spieltag,int',
						'taxdays'=>'Tage seit letzter Steuerzahlung,int',
						'fights_suffered'=>'Angriffe an diesem Spieltag,int',
						'fights_suffered_period'=>'Angriffe in letzter Zeit,int',
						'type'=>'Gildentyp,enum,'.$types,
						'Sondereinstellungen,title',
						'professions_allowed'=>'Erlaubte Berufe in Gilde (Zahlenwert mit Komma. Leerlassen für alle Berufe:`n'.$prof_list.')',
						'guildwar_allowed'=>'Gildenkrieg für diese Gilde erlaubt,enum,1,Ja,0,Nein',
						'taxfree_allowed'=>'Steuerfreiheit für diese Gilde,enum,1,Ja,0,Nein',
						'Listen & Sonstiges,title',
						'lastupdate'=>'Letztes Update,viewonly',
						'build_list'=>'Ausbauten,viewonly',
						'points_spent'=>'Ausgegebene Punkte,viewonly',
						'treaties'=>'Verträge,viewonly',
						'transfers'=>'Transfers,viewonly',
						'ranks'=>'Ränge,viewonly'
						);
						
		$savelink = 'dg_su.php?op=edit&subop=save&gid='.$gid;
		
		output('<form action="'.$savelink.'" method="POST">',true);
				
		showform($edit_form,$guild);
		
		output('</form>',true);
		
		addnav('',$savelink);
		addnav('Logs','dg_su.php?op=logs&gid='.$gid);
		addnav('Zum Editor','dg_su.php');
		addnav('Zur Grotte','superuser.php');							
		addnav('Zum Weltlichen','village.php');								
		
		break;
		
	case 'delete':
		
		$gid = ($_GET['gid']) ? $_GET['gid'] : 0;
		
		if(!$gid) {redirect('dg_su.php');}
		
		if($_GET['subop'] == 'ok') {
			
			dg_massmail($gid,'`4Gilde gelöscht!','`4Die Gilde, in der du Mitglied warst, wurde von den Mods aufgelöst.`n
								Noch vorhandene Schätze wurden auf die Mitglieder verteilt.');			
			dg_delete_guild($gid);
			
			redirect('dg_su.php');
						
		}
		else {
			
			output('`4Gilde ID '.$gid.' ('.$session['guilds'][$gid]['name'].'`4) wirklich löschen?');
			addnav('Nein!','dg_su.php');
			addnav('Ja!','dg_su.php?op=delete&subop=ok&gid='.$gid);
						
		}
		
		break;	// END del
		
	case 'activate':
		
		$gid = ($_GET['gid']) ? $_GET['gid'] : 0;
		
		if(!$gid) {redirect('dg_su.php');}
		
		$guild = &dg_load_guild($gid);
				
		// Wenn erst gegründet
		if($guild['last_state_change'] == '0000-00-00 00:00:00') {
			
			$guild['gold'] = dg_calc_boni($gid,'startgold',getsetting('dgstartgold',1000));
			$guild['gems'] = dg_calc_boni($gid,'startgems',getsetting('dgstartgems',5));
			$guild['points'] = dg_calc_boni($gid,'startpts',getsetting('dgstartpoints',10));
			$guild['regalia'] = dg_calc_boni($gid,'startregalia',getsetting('dgstartregalia',10));
			$guild['guard_hp'] = dg_calc_boni($gid,'startguardhp',1000);
			
			$sql = 'SELECT name,acctid FROM accounts WHERE acctid='.$guild['founder'];
			$res = db_query($sql);
			$acc = db_fetch_assoc($res);
			
			dg_addnews($acc['name'].'`@ hat die Gilde '.$guild['name'].'`@ gegründet!',$acc['acctid'],$gid);
				
			addhistory('`2Gegründet von '.$acc['name'].'`2!',2,$gid);
			addhistory('`2Gründung der Gilde '.$guild['name'].'`2',1,$acc['acctid']);
									
			dg_massmail($gid,'`@Gilde angenommen!','`@Deine Gilde wurde von den Mods freigeschaltet und kann nun genutzt werden. Als Startguthaben erhält sie '.$guild['gold'].' Gold, '.$guild['gems'].' Edelsteine und '.$guild['points'].' Gildenpunkte.');
										
		}
		else {
			
			dg_massmail($gid,'`@Gilde aktiviert!','`@Deine Gilde wurde von den Mods wieder aktiviert und kann nun weiter genutzt werden.');
			
		}
		
		$guild['state'] = DG_STATE_ACTIVE;
		$guild['last_state_change'] = date('Y-m-d H:i:s');	
		
		dg_save_guild();
						
		redirect('dg_su.php');
		
		break;
		
	case 'deactivate':
		
		$gid = ($_GET['gid']) ? $_GET['gid'] : 0;
		
		if(!$gid) {redirect('dg_su.php');}
		
		$guild = &dg_load_guild($gid);
		
		dg_massmail($gid,'`4Gilde deaktiviert!','`4Deine Gilde wurde von den Mods deaktiviert. Wahrscheinlich hat sie nicht genügend Mitglieder (min. '.getsetting('dgminmembers',3).') bzw. keinen Gildenführer.');
						
		$guild['state'] = DG_STATE_INACTIVE;
		$guild['last_state_change'] = date('Y-m-d H:i:s');	
		
		dg_save_guild();
					
		redirect('dg_su.php');
		
		break;
		
	case 'logs':
		
		$gid = $_GET['gid'];
		
		$sql = 'SELECT dg_log.*,g1.name as guildname,g2.name as targetname FROM dg_log LEFT JOIN dg_guilds as g1 ON g1.guildid=dg_log.guild LEFT JOIN dg_guilds as g2 ON g2.guildid=dg_log.target WHERE dg_log.guild='.$gid.' OR dg_log.target='.$gid.' ORDER by dg_log.date DESC,dg_log.logid ASC LIMIT 500';
		$result = db_query($sql);
		$odate = "";
		while($row=db_fetch_assoc($result)) {

			$dom = date("D, M d",strtotime($row['date']));
			if ($odate != $dom){
				output("`n`b`@".$dom."`b`n");
				$odate = $dom;
			}
			$time = date("H:i:s", strtotime($row['date']));
			output($time.' - '.$row['guildname'].' '.$row['message']);
			if ($row['target']) output(' '.$row['targetname']);
			output("`n");
		}
		
		addnav('Zurück',$ret_page);
		
		break;
		
	case 'callking':
				
		savesetting('dgkingdays','0');		
		savesetting('newdaysemaphore','0000-00-00 00:00:00');		
		
		$session['user']['lasthit'] = 0;
				
		addnav('Zurück','dg_main.php');
		
		break;
		
}	

page_footer();
?>
