<?php

require_once("common.php");
checkday();
page_header("Superusereditor");

// Gruppen aus Settings laden
$arr_grps = array();
$str_grps = stripslashes(getsetting('sugroups',''));
$arr_grps = unserialize($str_grps);
if(!is_array($arr_grps)) {
	$arr_grps = array();
}
ksort($arr_grps);

addnav('Zurück');
addnav("G?Zur Grotte","superuser.php");
addnav('W?Zum Weltlichen',$session['su_return']);
addnav('Aktionen');

output("`c`b`&Superusereditor`0`b`c");

if($session['message'] != '') {
	output('`n`b'.$session['message'].'`b`n`n');
	$session['message'] = '';
}

// MAIN SWITCH
$op = ($_REQUEST['op'] ? $_REQUEST['op'] : '');

switch($op) {
			
	case 'editgroup':
						
		addnav("E?Edit beenden","usergroupsu.php");
				
		$id = (int)$_REQUEST['id'];
		
		$surights = array('Superuser-Rechte,title');
		foreach($ARR_SURIGHTS as $r=>$v) {
			
			$surights['surights['.$r.']'] = $v['desc'].',checkbox,1';
			
		}
		
		if($id > 0) {
			
			$arr_editgrp = $arr_grps[$id];
			$arr_editgrp = array('name_sing'=>$arr_editgrp[0],
								'name_plur'=>$arr_editgrp[1],
								'surights'=>$arr_editgrp[2],
								'lst_show'=>$arr_editgrp[3]);
									
			// Formulardaten der Rechte erstellen					
			$arr_editgrp_rights = explode(';',$arr_editgrp['surights']);
			
			foreach($arr_editgrp_rights as $r=>$v) {
				$arr_editgrp['surights['.$r.']'] = $v;
			}
						
		}
						
		$form = array('Allgemeines,title',
						'name_sing'=>'Name Singular',
						'name_plur'=>'Name Plural',
						'lst_show'=>'In "Wer ist online?"-Liste auf Startseite gesondert aufführen?,bool'
						);
		
		$form = array_merge($form,$surights);
		
		$link = "usergroupsu.php?op=savegroup";
						
		$out .=	"<form method=\"POST\" action=\"".$link."\">";
		addnav("",$link);
		
		if($_GET['copy']) {
			$arr_editgrp['name_sing'] = 'Kopie '.$arr_editgrp['name_sing'];
			$id = 0;
		}
		else {
			$out .=	"<input type=\"hidden\" value=\"".$id."\" name=\"id\">";				
			if($id > 0) {
				addnav('Kopie anlegen','usergroupsu.php?op=editgroup&id='.$id.'&copy=1');
			}
		}
		
		output($out,true);				
		showform($form,$arr_editgrp);
							
		break;
	
	
	// Gruppe löschen
	case 'delgroup':
		
		$id = (int)$_GET['id'];
		
		$sql = 'SELECT login FROM accounts WHERE superuser='.$id.' ORDER BY acctid';
		$res = db_query($sql);
		
		if(db_num_rows($res)) {
			
			output('`$Folgende Superuser-Accounts befinden sich noch in dieser Gruppe:`n`n');
			while($a = db_fetch_assoc($res)) {
				output('`&'.$a['login'].'`n');
			}
			output('`n`$Bitte zuerst diese Accounts einer anderen Gruppe zuordnen!');
			
		}
		else {
			
			unset($arr_grps[$id]);
		
			savesetting( 'sugroups', addslashes(serialize($arr_grps)) );
			
			$session['message'] = '`@Erfolgreich gelöscht!';
							
			redirect('usergroupsu.php');	
			
		}
						
		break;
	
	// Speichern	
	case 'savegroup':
		
		$id = (int)$_REQUEST['id'];
		
		// Konvertierung der Rechte
		// Leere Felder auffüllen
		end($_POST['surights']);
		$int_lastkey = (int)key($_POST['surights']);
		ksort($_POST['surights']);
		
		for($i=0; $i<=$int_lastkey; $i++) {
			if(!isset($_POST['surights'][$i])) {
				$_POST['surights'][$i] = 0;
			}
			
		}
		ksort($_POST['surights']);

		$str_rights = implode(';',$_POST['surights']);
		// END Konvertierung der Rechte
		
		// Übersetzung der Formulardaten in numerische Array-Schlüssel
		$arr_savegrp = array(0=>$_POST['name_sing'],
								1=>$_POST['name_plur'],				
								2=>$str_rights,
								3=>$_POST['lst_show']);
		
		if($id > 0) {
			systemlog('Superuser-Gruppe '.$arr_grps[$id][0].' geändert.',$session['user']['acctid']);
		
			$arr_grps[$id] = $arr_savegrp;
		}
		else {
			$arr_grps[0] = 0;
			$arr_grps[] = $arr_savegrp;
			unset($arr_grps[0]);
		}
		
		savesetting( 'sugroups', addslashes(serialize($arr_grps)) );
					
		$session['message'] = '`@Erfolgreich gespeichert!`0';
		
		redirect('usergroupsu.php');
						
		break;
	
	
	// Standardansicht, Auswahl
	default:
		
		$out = '`c<table cellspacing="2" cellpadding="2"><tr class="trhead">
					<td>`bID`b</td>		
					<td>`bName`b</td>
					<td>`bAktionen`b</td>
				</tr>';
		
		addnav('Neue Gruppe','usergroupsu.php?op=editgroup&id=0');
		
		foreach($arr_grps as $id => $g) {
			
			$style = ($style == 'trlight' ? 'trdark' : 'trlight');
			$editlink = create_lnk('Edit','usergroupsu.php?op=editgroup&id='.$id);
			$dellink = create_lnk('Del','usergroupsu.php?op=delgroup&id='.$id);
						
			$out .= '<tr class="'.$style.'">
						<td>'.$id.'</td>
						<td>'.$g[0].' / '.$g[1].'`&</td>
						<td>
							[ '.$editlink.' ] 
							[ `$'.$dellink.'`& ]
						</td>
					</tr>';
			
		}	
		
		$out .= '</table>`c';
		
		output($out,true);
		
		break;
	
}

page_footer();
?>