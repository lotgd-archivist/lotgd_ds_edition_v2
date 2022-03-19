<?
/**
*@desc Titleeditor for LOTGD 0.97
*@longdesc This little editor simplifies editing/adding/removing of titles for superusers
* Please don't test the script with bogus values. It is ONLY for Admins which know what this
* is all about.
* As Always I don't display a nagging copyright notice, please just leave this message in here
*@copyright Kolja Engelmann(Dragonslayer) for lotgd.drachenserver.de
*/

/*
Database:
You have to change the database for this to work. It does not affect speed, 
it only uses a bit more space in the Database:
Execute: ALTER TABLE `settings` CHANGE `value` `value` TEXT;

Open superuser.php
Add: addnav("Titel Editor","titleeditor.php");
Whereever you want it

Open common.php
Search: $titles = array(...);
Replace: $titles = getsetting('title_array',null,true);

This file:
I use a function to sanitize parameters provided by a user.
You might want to either copy this function to the common.php
or just put it here into this file. Of course you have to remove all the 
line comments // at the begining of the line

///**
//* @desc return a given parameter which has been checked and altered in order not
//* to be dangerous for SQL Queries
//* @param string the parameter
//* @param bool remove html tags
//* @param bool remove sql commands
//* @param bool remove html special chars
//* @return returns the corrected parameter or false if the parameter was empty, else true
//*/
//function mixed_check_parameter($str_parameter, $bool_remove_tags = true, $bool_remove_sql = true,
//$bool_no_html_special_chars = true)
//{
//	if($str_parameter == null)
//	{
//		return false;
//	}
//	if($str_parameter == '')
//	{
//		return true;
//	}
//	$str_parameter = mysql_escape_string($str_parameter);
//	if($bool_remove_tags == true)
//	{
//		$str_parameter = strip_tags($str_parameter);
//	}
//	if($bool_no_html_special_chars == true)
//	{
//		$str_parameter = htmlentities($str_parameter);
//	}
//	//Not fully functional right now, dos not do anything
//	//Is planned to remove SQL statements by a regular expression
//	if($bool_remove_sql == true)
//	{
//		$str_regex = '#((select.*from.*(where)?.*)|(insert.*into.*values.*)|'.
//		'(delete.*from.*|create.*(table|database)))#iu';
//
//		//Remove what was defined in the regular expression above
//		$str_parameter = preg_replace($str_regex, '',$str_parameter);
//	}
//	//Return the cleaned parameter
//	return $str_parameter;
//}

/***/

////////////////
//Standard Part
////////////////
require_once 'common.php';
su_check(SU_RIGHT_EDITORTITLES,true);

/**
*@desc The filename of this editor
*/
$str_filename = basename(__FILE__);

/**
*@desc The Title
*/
$str_title = 'Titel Editor';

/**
*@desc Where will you be directed whe nyou click the back button
*/
$str_backlink = 'superuser.php';
/**
*@desc What text will be written on the backlink
*/
$str_backtext = 'Zurück zur Grotte';
/**
*@desc The header and introduction text for the page
*/
/**
*@desc The text written on the reset-button
*/
$str_resettext = 'Zurücksetzen';
/**
*@desc The text written on the save-button
*/
$str_savetext = 'Speichern';
/**
*@desc The link to redirect to when sving the settings
*/
$str_savelink = $str_filename.'?op=save_titles';

/**
*@desc No value has been submitted
*/
$str_status_no_values = 'Es wurde nichts übertragen';
/**
*@desc All was fine
*/
$str_status_ok = 'Alle Werte wurden übernommen';

/**
*@desc Header text
*/
$str_header = <<<END
`c`bTiteleditor`b`c
Im Titeleditor kannst Du die für die Spieler verwendeten einfach Titel editieren. 
Gib dafür einfach im Textfeld die entsprechenden Titel in der Form`n
`@männlicher Titel,weiblicher Titel`n`0 ein. Pro Zeile nur einen Titel.
Die Reihenfolge dieser Liste bestimmt auch die Reihenfolge in der die User die Titel erlangen können.`n
END;

/**
*@desc HTML for the form, contains already the decoded titles
*/
$str_form = <<<END
<form method=post name='edit_titles' action='$str_savelink'>
	<textarea class= "input" name='list_of_titles' style='width:300px; height:300px;'>%decoded_titles%</textarea>
	<br />
	<input name='submit' type='submit' value='$str_savetext'>
	<input name='reset' type='reset' value='$str_resettext'>
</form>
END;

//////////////////////
//Function definition
//////////////////////
/**
*@desc get the list of titles and convert it to a string
*@return string
*/
function get_decoded_titles()
{
	//Get the titles from the Database
	$arr_title_array = unserialize(getsetting('title_array',null));

	$str_titles = '';
	//stability addition if the variable is empty
	if($arr_title_array == null)
	{
		$arr_title_array = array();
	}
	//Make a string from the array
	foreach ($arr_title_array as $arr_element)
	{
		$str_titles .= $arr_element[0].','.$arr_element[1]."\n";
	}
	return trim($str_titles);
}

////////////////////
//Do the main stuff
////////////////////
switch ($_GET['op'])
{

	case 'save_titles':
	//These are the titles received from the form sanitized by the function given above
	$str_titles = mixed_check_parameter($_POST['list_of_titles'], true, true, false);

	//Now we try splitting each line
	$arr_titles = explode('\r\n',$str_titles);

	//Check whether there is at least one line
	if(count($arr_titles)==0 || $str_titles == '')
	{
		//Set an error message
		$str_status_message = $str_status_no_values;
		break;
	}
	else
	{
		$arr_title_array_new = array();
		//Proces each line
		foreach($arr_titles as $str_title_line)
		{
			//Remove leading and trailing whitespaces
			$str_title_line = trim($str_title_line);
			
			if($str_title_line == '')
			{
				continue;
			}

			//Split the line into an array itself
			$arr_title_array_new[] = preg_split('/\s*,\s*/',$str_title_line);
		}
		//Serialize this multidimensional array
		$str_save_to_db = serialize($arr_title_array_new);
		//Save it in the settings database
		savesetting('title_array',$str_save_to_db);
		//Set the array which has to be displayed to the array which has just been generated
		$arr_title_array = $arr_title_array_new;
		//Set the status message to an ok
		$str_status_message = $str_status_ok;
	}
}

//Start the main stuff here
page_header($str_title);
//The header and description text
output($str_header,1);

//Output a status message;
output('`c`b`1'.$str_status_message.'`0`b`c');

//Output the form
$str_form = str_replace('%decoded_titles%',get_decoded_titles(),$str_form);
output($str_form,1);
addnav('',$str_savelink);

//Add navigations so we don't get stuck
addnav($str_backtext, $str_backlink);

page_footer();
?>