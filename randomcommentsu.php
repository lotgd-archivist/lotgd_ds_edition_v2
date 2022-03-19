<?php

// Zufallskommentareditor by Talion (t@ssilo.de) für lotgd.drachenserver.de

require_once "common.php";

page_header("Zufallskommentareditor");

addnav('Zurück');
addnav("G?Zur Grotte","superuser.php");



addnav('W?Zum Weltlichen',$session['su_return']);

output('`c`b`&Zufallskommentareditor`&`b`c`n`n');

if($session['message'] != '') {
	output('`n`b'.$session['message'].'`b`n`n');
	$session['message'] = '';
}

$op = ($_REQUEST['op'] ? $_REQUEST['op'] : '');

switch($op) {
	
	case '':	// Standardansicht, Übersicht aller Zufallskommentare
		
		$section = $_REQUEST['section'];
		$style = 'trdark';
		$sections = '';
		
		$sql = 'SELECT section FROM random_commentary WHERE section != "" GROUP BY section ORDER BY section ASC';
		$res = db_query($sql);
		
		$sections = '<option value="" '.($section == '' ? 'selected="selected"': '').'>Keine / Alle</option>';
		
		// Sucharray erstellen
		while($s = db_fetch_assoc($res)) {
			
			$sections .= '<option value="'.$s['section'].'" '.($section == $s['section'] ? 'selected="selected"': '').'>'.$s['section'].'</option>';
			
		}
		
		$link = 'randomcommentsu.php';
		addnav('',$link);
		
		addnav('Aktionen');
		addnav('Neu','randomcommentsu.php?op=edit');
		
		$out = '`c<form method="POST" action="'.$link.'">
					`bSection:`b <select name="section" size="1" onchange="this.form.submit()">'.$sections.'</select>
				</form>`n';
						
		$out .= '<table cellspacing="2" cellpadding="2"><tr class="trhead">
					<td>`bID`b</td>		
					<td>`bSection`b</td>
					<td>`bText`b</td>
					<td>`bWahrscheinl.`b</td>
					<td>`bAbstand`b</td>
					<td>`bWetter`b</td>
					<td>`bZeit`b</td>
					<td>`bRL-Date`b</td>
					<td>`bAktionen`b</td>
				</tr>';
		
		$sql = 'SELECT * FROM random_commentary '.($section != '' ? 'WHERE section="'.$section.'"' : '').' ORDER BY id ASC';
		$res = db_query($sql);
				
		while($c = db_fetch_assoc($res)) {
			
			$c['section'] = ($c['section'] == '' ? 'Alle' : $c['section']);
			$c['weather'] = ($c['weather'] == 0 ? 'Keines' : $weather[$c['weather']]['name']);
			$style = ($style == 'trlight' ? 'trdark' : 'trlight');
			$editlink = 'randomcommentsu.php?op=edit&id='.$c['id'];
			addnav('',$editlink);
			$dellink = 'randomcommentsu.php?op=del&id='.$c['id'];
			addnav('',$dellink);
			
			$out .= '<tr class="'.$style.'">
						<td>'.$c['id'].'</td>
						<td>'.$c['section'].'`&</td>
						<td>'.$c['comment'].'`&</td>
						<td>'.$c['chance'].'</td>
						<td>'.$c['gap'].'</td>
						<td>'.$c['weather'].'`&</td>
						<td>Monat '.$c['month_min'].' - '.$c['month_max'].', Stunde '.$c['hour_min'].' - '.$c['hour_max'].'`&</td>
						<td>'.$c['rldate'].'`&</td>
						<td>
							[ <a href="'.$editlink.'">Edit</a> ] 
							[ `$<a href="'.$dellink.'" onClick="return confirm(\'Willst du diesen Kommentar wirklich löschen?\');">Del</a>`& ]
						</td>
					</tr>';
			
		}	
		
		$out .= '</table>`c';
		
		output($out,true);	
	
		break;
	
	case 'edit':	// Editformular für einen Zufallskommentar
						
		$weather_enum = 'enum,0,Keines';
		foreach($weather as $id=>$w) {
			$weather_enum.=','.$id.','.substr($w['name'],0,15).'..';
		}
		
		$section_enum = 'enum, ,Alle,all_private,Alle privat,all_public,Alle öffentlich,all_inside,Alle drinnen,all_outside,Alle draußen';
		foreach($rcomment_sections as $s=>$v) {
			$section_enum.=','.$s.','.$s;
		}
		
		$form = array(
					'id' => 'ID,viewonly',
					'comment' => 'Kommentar',
					'section' => 'Chatsektion (interner Name),'.$section_enum,
					'gap' => 'Mindestabstand zw. 2 identischen',
					'chance' => 'Wahrscheinlichkeit (0-250),int',
					'month_min' => 'Monat min. (1-12) für Erscheinen,int',
					'month_max' => 'Monat max. (1-12) für Erscheinen,int',
					'hour_min' => 'Stunde min. (0-23) für Erscheinen,int',
					'hour_max' => 'Stunde max. (0-23) für Erscheinen,int',
					'rldate' => 'Realdate für Erscheinen (Y-m-d)',
					'weather' => 'Bestimmtem Wetter zuweisen,'.$weather_enum					
				);
		$data = array('id'=>'NEU','comment'=>'/msg', 'section'=>'village', 'gap'=>1, 'chance'=>1, 'weather'=>0, 'month_min'=>1, 'month_max'=>12, 'hour_min'=>0, 'hour_max'=>23, 'rldate'=>'0000-00-00');
		
		$id = (int)$_GET['id'];
		
		if($id) {
			$sql = 'SELECT * FROM random_commentary WHERE id='.$id;
			$res = db_query($sql);
			$data = db_fetch_assoc($res);
		}
		
		addnav('Aktionen');
		addnav('Abbruch','randomcommentsu.php'.($id ? '?section='.$data['section'] : '') );						
				
		$link = 'randomcommentsu.php?op=save';
		addnav('',$link);
					
		output('<form action="'.$link.'" method="POST"><input type="hidden" name="id" value="'.$id.'">',true);
		
		showform($form,$data);
		
		output('</form>',true);
			
		break;
	
	case 'del':	// Löschen
		
		$id = (int)$_GET['id'];
		
		$sql = 'DELETE FROM random_commentary WHERE id='.$id;
		db_query($sql);
		
		if(!db_affected_rows()) {
			$session['message'] = '`$Fehler bei Löschen!';
			
			redirect('randomcommentsu.php');
		}
		else {
			$session['message'] = '`@Erfolgreich gelöscht!';
									
			redirect('randomcommentsu.php');
		}
		
		break;	
		
	case 'save':	// Abspeichern
		
		$id = (int)$_POST['id'];
		$comment = addslashes($_POST['comment']);
		$section = addslashes($_POST['section']);
		$chance = (int)$_POST['chance'];
		$gap = (int)$_POST['gap'];
		$month_min = (int)$_POST['month_min'];
		$month_max = (int)$_POST['month_max'];
		$hour_max = (int)$_POST['hour_max'];
		$hour_min = (int)$_POST['hour_min'];
		$rldate = $_POST['rldate'];
		$weather = (int)$_POST['weather'];
		
		$sql = ($id ? 'UPDATE ' : 'INSERT INTO ');
		
		$sql .= ' random_commentary SET 
					comment="'.$comment.'",
					section="'.$section.'",
					chance="'.$chance.'",
					gap="'.$gap.'",
					month_min="'.$month_min.'",
					month_max="'.$month_max.'",
					hour_max="'.$hour_max.'",
					hour_min="'.$hour_min.'",
					rldate="'.$rldate.'",
					weather="'.$weather.'"';
					
		$sql .= ($id ? ' WHERE id='.$id : '');
		
		db_query($sql);
		
		/*if(!db_affected_rows()) {
			$session['message'] = '`$Fehler bei Speichern!';
			
			redirect('randomcommentsu.php');
		}
		else {*/
			$session['message'] = '`@Erfolgreich gespeichert!';
			
			if(!$id) {$id = db_insert_id();}
			
			redirect('randomcommentsu.php?op=edit&id='.$id);
		//}
						
		break;
	
	
}

addnav('Startseite','randomcommentsu.php' );

page_footer();
?>
