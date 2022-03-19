<?
#-----------------------------#
#       Farbverwaltung        #
#                             #
#    Coded by Anima Azura     #
#-----------------------------#
require_once "common.php";

su_check(SU_RIGHT_EDITORCOLORS,true);

page_header("Farbverwaltung");
addnav("G?Zurück zur Grotte","superuser.php");
addnav("W?Zurück zum Weltlichen","village.php");

if ($_GET['op']=="del"){
	savesetting('cachereloadtime',1);
	$sql = "UPDATE appoencode SET active=0 WHERE id='{$_GET['id']}'";
	db_query($sql);
	$_GET['op']="";
}
if ($_GET['op']=="undel"){
	savesetting('cachereloadtime',1);
	$sql = "UPDATE appoencode SET active='1' WHERE id='{$_GET['id']}'";
	db_query($sql);
	$_GET['op']="";
}
if ($_GET['op']=="forbid"){
	savesetting('cachereloadtime',1);
	$sql = "UPDATE appoencode SET allowed=0 WHERE id='{$_GET['id']}'";
	db_query($sql);
	$_GET['op']="";
}
if ($_GET['op']=="allow"){
	savesetting('cachereloadtime',1);
	$sql = "UPDATE appoencode SET allowed='1' WHERE id='{$_GET['id']}'";
	db_query($sql);
	$_GET['op']="";
}
if ($_GET['op']=="kill"){
	savesetting('cachereloadtime',1);
	$sql = "DELETE FROM appoencode WHERE id='{$_GET['id']}'";
	db_query($sql);
	$_GET['op']="";
}

if ($_GET['op']==""){
    addnav("Neue Farbe einfügen","colors.php?op=add");
	
	addnav("CSS schreiben","colors.php?op=writecss");
	
	$sql = "SELECT * FROM appoencode ORDER BY id ASC";
	output("<table>",true);
	output("<tr class='trhead'><td>Ops</td><td>Farbtag</td><td>HEX-Farbe</td><td>Zusatztag</td><td>Style</td></tr>",true);
	$result = db_query($sql);
	for ($i=0;$i<db_num_rows($result);$i++){
		$row = db_fetch_assoc($result);
		output("<tr class='trdark'>",true);
		output("<td>[<a href='colors.php?op=edit&id={$row['id']}'>Bearbeiten</a> |",true);
		addnav("","colors.php?op=edit&id={$row['id']}");
		output(" <a href='colors.php?op=kill&id={$row['id']}'>Löschen</a> |",true);
		addnav("","colors.php?op=kill&id={$row['id']}");
		if ($row['allowed']) {
			output(" <a href='colors.php?op=forbid&id={$row['id']}'>Verbieten</a> |",true);
			addnav("","colors.php?op=forbid&id={$row['id']}");
		}else{
			output(" <a href='colors.php?op=allow&id={$row['id']}'>Erlauben</a> |",true);
			addnav("","colors.php?op=allow&id={$row['id']}");
		}
		if ($row['active']) {
			output(" <a href='colors.php?op=del&id={$row['id']}'>Deaktivieren</a>]</td>",true);
			addnav("","colors.php?op=del&id={$row['id']}");
		}else{
			output(" <a href='colors.php?op=undel&id={$row['id']}'>Aktivieren</a>]</td>",true);
			addnav("","colors.php?op=undel&id={$row['id']}");
		}
		if(empty($row['color'])){$row['color']=NULL;}
		if(empty($row['tag'])){$row['tag']=NULL;}
		if(empty($row['style'])){$row['style']=NULL;}
		output("<td>{$row['code']}</td>",true);
		output("<td>".(!empty($row['color']) ? '`'.$row['code'] : '')."{$row['color']}`0</td>",true);
		output("<td>{$row['tag']}</td>",true);
		output("<td>{$row['style']}</td>",true);
		output("</tr>",true);
	}
	output("</table>",true);
}
elseif ($_GET['op']=="add"){
	output("Neue farben hinzufügen:`n");
	addnav("Zurück zur Farbverwaltung","colors.php");
	colorform(array());
}
elseif ($_GET['op']=="edit"){
	addnav("Zurück zur Farbverwaltung","colors.php");
	$sql = "SELECT * FROM appoencode WHERE id='{$_GET['id']}'";
	$result = db_query($sql);
	if (db_num_rows($result)<=0){
		output("`iFarbe nicht gefunden.`i");
	}else{
		output("Farbeneditor:`n");
		$row = db_fetch_assoc($result);
		colorform($row);
	}
}
elseif ($_GET['op']=="writecss"){
	addnav("Zurück zur Farbverwaltung","colors.php");
	
	$str_out = write_appoencode_css();
	
	$str_file = 'templates/colors.css';
	
	$fhandler = fopen($str_file,'w+');
	fwrite($fhandler,$str_out);
	fclose($fhandler);	
}
elseif ($_GET['op']=="save"){
	reset($_POST['color']);
	$keys='';
	$vals='';
	$sql='';
	$i=0;
	while (list($key,$val)=each($_POST['color'])){
		if (is_array($val)) $val = addslashes(serialize($val));
		if ($_GET['id']>""){
		    if(empty($val)){
		        $sql.=($i>0?",":"")."$key=NULL";}
		    else {
		        $sql.=($i>0?",":"")."$key='$val'";}
		}else{
			$keys.=($i>0?",":"")."$key";
			if(empty($val)){
			    $vals.=($i>0?",":"")."NULL";}
			else {
			    $vals.=($i>0?",":"")."'$val'";}
		}
		$i++;
	}
	if ($_GET['id']>""){
		$sql="UPDATE appoencode SET $sql WHERE id='{$_GET['id']}'";
	}else{
		$sql="INSERT INTO appoencode ($keys) VALUES ($vals)";
	}
	db_query($sql);
	 if (!db_error(LINK)){
	 
	 	savesetting('cachereloadtime',1);
	 
		output("Farbe gespeichert!");
	}else{
		output("Fehler beim Speichern: $sql");
	}
	addnav("Zurück zur Farbverwaltung","colors.php");
}




function colorform($color){
	global $output;
//	if(empty($color['color'])){$color['color']="NULL";}
//	if(empty($color['tag'])){$color['tag']="NULL";}
//	if(empty($color['style'])){$color['style']="NULL";}
	output("<form action='colors.php?op=save&id={$color['id']}' method='POST'>",true);
	addnav("","colors.php?op=save&id={$color['id']}");
	$output.="<table>";
//	$output.="<tr class='trhead'><td>&nbsp;</td><td>&nbsp;</td></tr>";
	$output.="<tr class='trdark'><td>Farbtag:</td><td><input name='color[code]' value=\"".htmlentities($color['code'])."\"></td></tr>";
	$output.="<tr class='trdark'><td>".(!empty($color['color']) ? appoencode('`'.$color['code']) : '')."HEX-Farbe:</td><td><input name='color[color]' value=\"".htmlentities($color['color'])."\"></td></tr>";
	$output.="<tr class='trdark'><td>Zusatztag:</td><td><input name='color[tag]' value=\"".htmlentities($color['tag'])."\"></td></tr>";
	$output.="<tr class='trdark'><td>Style:</td><td><input name='color[style]' value=\"".htmlentities($color['style'])."\"></td></tr>";
	$output.="</table>";
	$output.="<input type='submit' class='button' value='Speichern'></form>";
}

page_footer();
?>
