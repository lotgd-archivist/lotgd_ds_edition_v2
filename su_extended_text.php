<?
/**
 * @desc This file is used for adminsitration of long database texts
 * @author Dragonslayer for atrahor.de/lotgd.drachenserver.de
 * @longdesc Install the following two functions anywhere in your global function repository
 */
///**
// * Get an extended string from the database
// *
// * @param string $str_text_id contains the id of the text
// * @param string $str_category contains the optional category, set to "*" to search in any category
// * @param bool $bool_get_as_array Set true to receive an array, else receive just the text
// * @return mixed - array or string (false if an error ocurred)
// */
//function get_extended_text($str_text_id = false,$str_category = '*',$bool_get_as_array = false)
//{
//	global $session;
//	if($str_text_id == false || empty($str_text_id))
//	{
//		return '';
//	}
//	if($str_category == false || is_null($str_category) || empty($str_category))
//	{
//		$str_category = 'standard';
//	}
//
//	//Sanitize
//	$str_text_id = addslashes(stripslashes($str_text_id));
//	$str_category = addslashes(stripslashes($str_category));
//
//	$str_sql_get_text = 'SELECT id,text,category FROM extended_text WHERE id="'.$str_text_id.'"';
//	if($str_category != '*')
//	{
//		$str_sql_get_text.= ' AND category = "'.$str_category.'"';
//	}
//
//	$db_result = db_query($str_sql_get_text);
//	if(db_num_rows($db_result)==0)
//	{
//		return '';
//	}
//	$arr_return_text = db_fetch_assoc($db_result);
//
//	$arr_return_text['text'] = stripslashes($arr_return_text['text']);
//
//	//Replacing all PHP Sourcecode
//	$str_temp_text = $arr_return_text['text'];
//	preg_match_all('/{{(.*)}}/sU',$str_temp_text,$arr_matches,PREG_SET_ORDER);
//	foreach($arr_matches as $arr_match)
//	{
//		$arr_match[1] = eval($arr_match[1]);
//		$str_temp_text = str_replace($arr_match[0],$arr_match[1],$str_temp_text);
//	}
//
//	if($session['user']['superuser']>1 && $bool_get_as_array == false)
//	{
//		$str_link = 'su_extended_text.php?op=edit&id='.$arr_return_text['id'];
//		addnav('',$str_link);
//		$str_html_edit = '[ <span class="colWhiteBlack "><a href="'.$str_link.'">Ändern</a></span> ]<br clear=all />';
//		$str_temp_text = $str_html_edit.$str_temp_text;
//	}
//
//	if($bool_get_as_array == true)
//	{
//		return $arr_return_text;
//	}
//	else
//	{
//		return $str_temp_text;
//	}
//}
//
///**
// * Write an extended text to the database
// *
// * @param string $str_text_id contains the id of the text
// * @param string $str_text contains the text
// * @param strin $str_category contains the optional category
// * @return bool
// */
//function set_extended_text($str_text_id = false,$str_text = false, $str_category = 'standard')
//{
//	if($str_text_id == false || empty($str_text_id) || $str_text == false || empty($str_text))
//	{
//		return false;
//	}
//	if($str_category == false || is_null($str_category) || empty($str_category))
//	{
//		$str_category = 'standard';
//	}
//
//	//Sanitize
//	$str_text_id = addslashes(stripslashes($str_text_id));
//	$str_text = addslashes(stripslashes($str_text));
//	$str_category = addslashes(stripslashes($str_category));
//
//	$result = get_extended_text($str_text_id,'*');
//	$str_sql_get_text = '';
//	if($result != '')
//	{
//		$str_sql_get_text = 'UPDATE extended_text SET text="'.$str_text.'",category="'.$str_category.'" WHERE id="'.$str_text_id.'"';
//	}
//	else
//	{
//		$str_sql_get_text = 'INSERT INTO extended_text (id,text,category) VALUES("'.$str_text_id.'","'.$str_text.'","'.$str_category.'")';
//	}
//	$db_result = db_query($str_sql_get_text);
//	return ($db_result==false)?false:true;
//}


$str_filename = basename(__FILE__);
include_once('common.php');
su_check(SU_RIGHT_EDITOREXTTXT,true);

page_header('Extended Texts Administrieren');
output('`c`bExtended Texts Administrieren`b`c`n`n');

addnav('Mod Optionen');
addnav('Zurück zur Grotte','superuser.php');
if(getsetting('su_extended_text_installed',false)==true)
{
	addnav('Liste der Texte',$str_filename.'?op=list');
	addnav('Text hinzufügen',$str_filename.'?op=edit&action=new');
}
if(su_check(SU_RIGHT_DEV))
{
	addnav('Superuser Optionen');
	if(getsetting('su_extended_text_installed',false)==false)
	{
		addnav('Installieren',$str_filename.'?op=install');
	}
	else
	{
		addnav('Entfernen (komplett)',$str_filename.'?op=uninstall');
	}
}

if(!empty($session['message'])) {
	output('`n`b'.$session['message'].'`b`n`n');
	$session['message'] = '';
}

global $output;
switch ($_GET['op'])
{
	case 'install':
		{
			$str_sql_install = '
			CREATE TABLE IF NOT EXISTS `extended_text` (
			 	`id` CHAR( 255 ) NOT NULL ,
			 	`text` TEXT NOT NULL ,
			 	`category` CHAR( 255 ) DEFAULT "standard" NOT NULL ,
			 	PRIMARY KEY ( `id` ) ,
			 	INDEX ( `category` )
		 	) TYPE = MYISAM COMMENT = "Saves some long texts in the database";
			';
			$db_result = db_query($str_sql_install);
			if($db_result != false)
			{
				savesetting('su_extended_text_installed',true);
			}
			$str_demo_text = <<< END
			"`c`bAnleitung`b`c`n`n\r\n`^\r\nExtended Text soll dazu verwendet werden lange Texte aus den Dateien in die Datenbank auszulagern, um sie dadurch schneller mal ändern zu können.`nPrädestiniert hierfür sind z.B. die Regeln/F.A.Q. die GPL usw.`n`n\r\n`bVerwendung`b`n\r\nJeder Text muss eine eindeutige ID erhalten, damit diese in der Datenbank schnell gefundenn werden kann. Es dürfen dabei keine Zeichen außer Buchstaben, Ziffern, Binde- und Unterstrich verwendet werden (für die Progger: es wird der reguläre Ausdruck [^w-_] angewendet).`n\r\nDer Text darf frei nach Schnauze editiert werden und darf sowohl bekannte `^L`2O`3T`4G`5D`^ Tags ``1 usw. aber auch <b>HTML<b/> <i>Tags</i> enthalten.`n`n\r\n`n\r\n`bVerwendung von PHP Code (Für Progger)`b`n\r\nEs ist möglich PHP Quelltext, Variablen o.ä. innerhalb der Texte zu verwenden. Alles was sich zwischen den Zeichen { { und } } befindet wird als PHP Code interpretiert und evaluiert! Dabei gelten die für evaluierten Code gültigen Regeln:\r\n`n1. Der Quellcode muss eine gültige Syntax haben, es müssen auch Semikolon gesetzt werden.\r\n`n2. Evaluierter Code verhält sich wie eine Funktion, alle Variablen müssen ggf. mit \"global\" eingebunden werden.\r\n`n3. Evaluierter Code muss einen returnwert haben, der weiterverarbeitet werden kann`n`n\r\n`bBeispiel:`b`n\r\n{ { return \$session[\'user\'][\'login\']; } } ergibt => `2NICHTS`^`n\r\nWarum? Die Variable \$session existiert nicht im aktuelen Scope, sie muss erst eingebunden werden`n\r\n{ { global \$session; return \$session[\'user\'][\'login\']; } } ergibt => \r\n`2{{\r\nglobal \$session;\r\nreturn \$session[\'user\'][\'login\'];\r\n}}`^`n`n\r\n\r\n`bVerwendung im Code`b`nDer Code enthält zwei Funktionen:`n`n\r\n`2\r\n/**`n\r\n * Get an extended string from the database`n\r\n * @param string \$str_text_id contains the id of the text`n\r\n * @param string \$str_category contains the optional category, set to \"*\" to search in any category`n\r\n * @param bool \$bool_get_as_array Set true to receive an array, else receive just the text`n\r\n * @return mixed - array or string (false if an error ocurred)`n\r\n */\r\n`n`3function get_extended_text(\$str_text_id = false,\$str_category = \'standard\',\$bool_get_as_array = false)`n`n\r\n\r\n`2/**`n\r\n * Write an extended text to the database`n\r\n * @param string \$str_text_id contains the id of the text`n\r\n * @param string \$str_text contains the text`n\r\n * @param string \$str_category contains the optional category`n\r\n * @return bool`n\r\n */\r\n`n`3function set_extended_text(\$str_text_id = false,\$str_text = false, \$str_category = \'standard\')`n`^\r\n`nDiese Funktionen können dann in jeder Datei verwendet werden, um einen entsprechenden Text aus der DB abzurufen!`n`n\r\nViel Spass damit `nDragonslayer"
END;
			set_extended_text('su_extended_text_manual',$str_demo_text,'standard');

			redirect($str_filename.'?op=list');
}
break;
	case 'uninstall':
		{
			if($_GET['confirm']==false)
			{
				$output .= '<br>Sollen tatsächlich alle Einträge, die Tabelle und die Settingseinträge gelöscht werden?<br>';
				addnav('Entfernen?');
				addnav('Nein, doch nicht',$str_filename.'?op=list');
				addnav('Ja, alles entfernen',$str_filename.'?op=uninstall&confirm=true');
				break;
			}
			$str_sql_uninstall = 'DROP TABLE IF EXISTS extended_text';
			$db_result = db_query($str_sql_uninstall);
			$str_sql_uninstall_setting = 'DELETE FROM settings WHERE setting="su_extended_text_installed"';
			$db_result_setting = db_query($str_sql_uninstall_setting);
			if($db_result != false)
			{
				redirect($str_filename.'?op=list');
			}
			else
			{
				$output .= 'Die Tabelle konnte nicht gelöscht werden oder existiert noch gar nicht';
			}
		}
		break;
	case 'list':
		{
			$int_page = (int)$_GET['page'];
			$str_category = $_GET['category'];
						
			//Write a list of categories at the top of the list
			$str_sql_get_categories = 'SELECT DISTINCT category FROM extended_text ORDER BY category ASC';
			$db_result_categories = @mysql_query($str_sql_get_categories);
			
			$output .= '<form>Wähle eine Kategorie<select id="category" name="category" onchange="parent.location=this.options[this.selectedIndex].value">';
			while ($arr_result = db_fetch_assoc($db_result_categories))
			{
				addnav('',$str_filename.'?op=list&category='.$arr_result['category']);
				$output .= '<option value="'.$str_filename.'?op=list&category='.$arr_result['category'].'" '.(($arr_result['category']==$str_category)?'selected':'').'>'.$arr_result['category'].'</option>';
			}
    
  			$output .= '</select>&nbsp;<input type="button" onClick="parent.location=document.getElementById(\'category\').options[document.getElementById(\'category\').selectedIndex].value" value="Go!">
			</form>';
  			//End writing a list of categories to the top of the screen		

			if($str_category != '')
			{
				$str_sql_get_texts = 'SELECT id,SUBSTRING(text,1,100) as text FROM extended_text WHERE category="'.$str_category.'" ORDER BY id ASC';
			}
			else 
			{
				$str_sql_get_texts = 'SELECT id,SUBSTRING(text,1,100) as text FROM extended_text ORDER BY id ASC';
			}
			
			$db_result = @mysql_query($str_sql_get_texts);

			if($db_result == false || db_num_rows($db_result)==0)
			{
				$output .= 'Es gibt noch keine Texte!';
				break;
			}
			while ($arr_result = db_fetch_assoc($db_result))
			{
				$str_id = $arr_result['id'];
				$str_text = appoencode(stripslashes($arr_result['text'])).'...<br />';
				$str_link_view = '<a href="'.$str_filename.'?op=view&id='.$str_id.'">Ansehen</a>';
				$str_link_edit = '<a href="'.$str_filename.'?op=edit&id='.$str_id.'">Ändern</a>';
				$str_link_delete = '<a href="'.$str_filename.'?op=delete&id='.$str_id.'">löschen</a>';

				addnav('',$str_filename.'?op=view&id='.$str_id);
				addnav('',$str_filename.'?op=edit&id='.$str_id);
				addnav('',$str_filename.'?op=delete&id='.$str_id);

				$output .= '<p><div class="trhead "><b>'.$str_id.'</b>&nbsp;[<a href=# onClick="document.getElementById(\'su_ext_'.$str_id.'\').style.display=\'block\';">+</a>]/[<a href=# onClick="document.getElementById(\'su_ext_'.$str_id.'\').style.display=\'none\';">-</a>]</div><div id="su_ext_'.$str_id.'" style="display:none;" class="trlight">';
				output($str_text);
				$output .= '</div><div class="trdark ">'.$str_link_view.' - '.$str_link_edit.' - '.$str_link_delete.'</div></p><hr />';
			}
		}
		break;		
	case 'view':
		{
			$str_id = $_GET['id'];
			output(get_extended_text($str_id));
		}
		break;
	case 'edit':
		{
			$str_id = $_GET['id'];
			$str_action = $_GET['action'];
			$str_sql_get_categories = 'SELECT DISTINCT category FROM extended_text ORDER BY category ASC';
			$db_result = db_query($str_sql_get_categories);

			$arr_categories = array();
			while($arr_result = db_fetch_assoc($db_result))
			{
				$arr_categories[] = $arr_result['category'].','.$arr_result['category'];
			}

			if(count($arr_categories)==0)
			{
				$str_categories = 'standard,standard';
			}
			else
			{
				$str_categories =implode(',',$arr_categories);
			}
			$arr_layout = array(
			'Texteinstellungen,title',
			'id'=>'Die ID des Textes - keine Sonderzeichen erlaubt',
			'text'=>'Der Text - Farbtags sind erlaubt/erwünscht,textarea,60,15',
			'category'=>'Die Kategorie des Textes,enum,'.$str_categories,
			'newcategory'=>'Befindet sich die Kategorie nicht in der obigen Liste? Hier eine Neue eingeben'
			);
			//$output .= print_r($arr_layout,true);
			if($str_action == 'new')
			{
				$arr_values = array();
			}
			else
			{
				$arr_values = get_extended_text($str_id,'*',true);
			}
			
			if(isset($session['ext_txt_formdata'])) {
				
				// Bereits abgesendete Daten ins Formular einsetzen
				$arr_values = array_merge($arr_values,$session['ext_txt_formdata']);
				unset($session['ext_txt_formdata']);
				
			}

			$str_lnk = $str_filename.'?op=save&action='.$str_action;
			addnav('',$str_lnk);

			output ('<form action="'.$str_lnk.'" method="POST">');
			showform($arr_layout,$arr_values);

			output ('</form>');
			$output .= '<hr /><b>Vorschau</b><br />';
			output(get_extended_text($str_id));
	
		}
		break;
	case 'save':
		{
			$bool_error = false;
		
			$str_id = $_POST['id'];
			$str_text = $_POST['text'];
			$str_category = $_POST['category'];
			$str_new_category = $_POST['newcategory'];

			//Sanitize
			$str_id = addslashes($str_id);
			$str_text = addslashes($str_text);
			$str_category = addslashes($str_category);
			$str_new_category = addslashes($str_new_category);
				
			$str_id = preg_replace('/[^\w_-]/','',$str_id);
			$str_category = preg_replace('/[^\w_-]/','',$str_category);
			$str_new_category = preg_replace('/[^\w_-]/','',$str_new_category);
			
//			echo($str_id);
			
			if($str_new_category != '')
			{
				$str_category = $str_new_category;
			}
			
			if($str_id == '')
			{
				$session['message'] = '`$Die ID enthält ungültige Zeichen! Der Text wurde abgelehnt.`0';
								
				$bool_error = true;
			}
			else {
			
				$mixed_return = set_extended_text($str_id,$str_text,$str_category);
				
				if($mixed_return == true)
				{
					output('Der Text wurde gespeichert!');
				}
				else
				{
					$bool_error = true;
					
					$session['message'] = '`$Der Text konnte nicht gespeichert werden!`0';
				}
			
			}
						
			if($bool_error) {
				$session['ext_txt_formdata']['id'] = $_POST['id'];
				$session['ext_txt_formdata']['text'] = $_POST['text'];
				$session['ext_txt_formdata']['category'] = $_POST['category'];
				$session['ext_txt_formdata']['newcategory'] = $_POST['newcategory'];
				redirect($str_filename.'?op=edit&id='.$_POST['id'].'&action='.$_GET['action']);
			}
			
		}
		break;
	case 'delete':
		{
			$str_id = $_GET['id'];
			$str_id = mixed_check_parameter($str_id);

			$str_sql_delete_id = 'DELETE FROM extended_text WHERE id="'.$str_id.'" LIMIT 1';
			db_query($str_sql_delete_id);
			redirect($str_filename.'?op=list');
		}
		break;
	default:
		{
			redirect($str_filename.'?op=list');
		}
		break;
}

page_footer();

?>