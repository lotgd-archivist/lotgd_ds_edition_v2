<?
/*
Complete new Mazeeditor by Alucard
*/

require_once "common.php";
require_once(LIB_PATH.'runes.lib.php');
page_header("Rune Editor");

function get_cat_select (  ) {
	
	$cats = '';
		
	$sql = 'SELECT class_name,id FROM items_classes ORDER BY class_name ASC';
	$res = db_query($sql);
	
	while( $c = db_fetch_assoc($res) ) {
		
		$cats .= ','.$c['id'].','.$c['class_name'];
		
	}
	return($cats);
	
}

addnav('Zurück');
addnav("G?Zur Grotte","superuser.php");
addnav('W?Zum Weltlichen',$session['su_return']);
addnav('Aktionen');

output("`c`b`&Rune Editor`0`b`c");

if($session['message'] != '') {
	output('`n`b'.$session['message'].'`b`n`n');
	$session['message'] = '';
}

switch($_GET['op']) {
			
	
	case 'edit':
		
		addnav("E?Editmode beenden","su_runeedit.php");
				
		$runeid = (int)$_REQUEST['rid'];
				
		// Wenn bestimmtes rune editiert werden soll
		if($runeid > 0) {
			
			$sql = 'SELECT * FROM '.RUNE_EI_TABLE.' WHERE id='.$runeid;
			$rune = db_fetch_assoc(db_query($sql));
		}

		
		$data = array(	'name'=> empty($rune['name']) ? "Rune - " : $rune['name'],
						'seltenheit'=> empty($rune['seltenheit']) ? 1 : $rune['seltenheit'],
						'hinweis'=> $rune['hinweis'],
						'id'=>$runeid,
						'buchstabe'=>$rune['buchstabe'],
						'ausrichtung'=>$rune['ausrichtung']
						);
		$choose = '';
		for($i=1;$i<256;++$i){
			$choose .= ','.$i.','.$i;	
		}
		
		$form = array(	'id'=>'ID,hidden',
						'name'=>'Name',
						'seltenheit'=>'Seltenheit,enum'.$choose,
						'hinweis'=>'Hinweis',
						'buchstabe'=>'Buchstabe',
						'ausrichtung'=>'Ausrichtung'
						);
		

		$link = 'su_runeedit.php?op=save';
		addnav('',$link);	

						
		$out .=	'<form name="runevalues" method="POST" action="'.$link.'">';
				

		$out .= generateform($form,$data,false,($runeid>0 ? 'Übernehmen' : 'Speichern'));
						
		$out .=	'</form>';
					
		break;
		
	case 'itemtpl':
	
	
	
	break;
	
	
	case 'delete':
		
		/*$mazeid = (int)$_GET['mazeid'];
		
		$sql = 'DELETE FROM mazes WHERE mazeid='.$mazeid;
		db_query($sql);
		
		if(!db_affected_rows()) {
			$session['message'] = '`$Fehler bei Löschen!';
			
		}
		else {
			$session['message'] = '`@Erfolgreich gelöscht!';

		}
		
		redirect('newmazeedit.php');*/
		
		break;
	
	// Speichern	
	case 'save':
		
		$rid = (int)$_REQUEST['id'];
						
		$sql = ($rid ? 'UPDATE '.RUNE_EI_TABLE.' SET ' : 'INSERT INTO '.RUNE_EI_TABLE.' SET ');
		
		$sql .= ' 	name="'.$_POST['name'].'",
					seltenheit='.$_POST['seltenheit'].',
					hinweis="'.$_POST['hinweis'].'",
					buchstabe="'.$_POST['buchstabe'].'",
					ausrichtung="'.$_POST['ausrichtung'].'"';
					
		$sql .= ($rid ? ' WHERE id='.$rid : '');
		
		if(db_query($sql)) {
			$session['message'] = '`@Rune erfolgreich gespeichert!`0';
		}
		else {
			$session['message'] = '`$Fehler beim Speichern!`0';
		}
		
		redirect('su_runeedit.php');
					
		break;
		
		
		case 'savemain':
			savesetting('runes_classid',$_POST['itemklasse']);
			savesetting('runes_dummytpl',$_POST['dummy_tpl']);
			$session['message'] = '`@Gespeichert!';
			redirect('su_runeedit.php');
		break;
	
	
	// Standardansicht, Auswahl
	default:
		$data = array(	'itemklasse'=> getsetting('runes_classid',0),
						'dummy_tpl'=> getsetting('runes_dummytpl','r_dummy')
						);
				
		$form = array(	'itemklasse'=>'Itemklasse,enum'.get_cat_select(),
						'dummy_tpl'=>'Unidentifizierte'
						);
						
		$link = 'su_runeedit.php?op=savemain';
		addnav('',$link);
		
						
		$out .=	'`c<form name="runesettings" method="POST" action="'.$link.'">';
		$out .= generateform($form,$data);
		$out .=	'</form>`n`n';	
		
		$out .= '<table cellspacing="2" cellpadding="2"><tr class="trhead">
					<td>`bID`b</td>		
					<td>`bName`b</td>
					<td>`bSeltenheit`b</td>
					<td>`bAktionen`b</td>
				</tr>';
		
		addnav('NEU','su_runeedit.php?op=edit&rid=0');
		
		$sql = 'SELECT id, name, seltenheit FROM '.RUNE_EI_TABLE.' ORDER BY id ASC';
		$res = db_query($sql);
				
		while($r = db_fetch_assoc($res)) {
			
			$style = ($style == 'trlight' ? 'trdark' : 'trlight');
			$editlink = 'su_runeedit.php?op=edit&rid='.$r['id'];
			addnav('',$editlink);
			$dellink = 'su_runeedit.php?op=delete&rid='.$r['id'];
			addnav('',$dellink);
			
			$out .= '<tr class="'.$style.'">
						<td align="right">'.$r['id'].'</td>
						<td>'.$r['name'].'</td>
						<td align="right">'.$r['seltenheit'].'</td>
						<td>
							[ <a href="'.$editlink.'">edit</a> ] 
							[ `$<a href="'.$dellink.'" onClick="return confirm(\'Willst du diese Rune wirklich löschen?\');">`4delete</a>`& ]
						</td>
					</tr>';
			
		}	
		
		$out .= '</table>`c';
		
		
		
		break;
	
}

output( $out, true );

page_footer();
