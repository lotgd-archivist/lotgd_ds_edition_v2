<?php

// 17092004

/*
riddle editor for 0.9.7 ext GER by anpera
based on the code from creature editor by mightye
*/

require_once "common.php";
su_check(SU_RIGHT_EDITORWORLD,true);

page_header("R�tseleditor");

if ($session[user][superuser] >= 0){
	addnav("G?Zur�ck zur Grotte","superuser.php");
	

addnav('W?Zur�ck zum Weltlichen',$session['su_return']);
	if ($_POST['save']<>""){
		if ($_POST['id']!=""){
			$sql="UPDATE riddles SET riddle='{$_POST['riddle']}',answer='{$_POST['answer']}' WHERE id={$_POST['id']}";
			output(db_affected_rows()." ".(db_affected_rows()==1?"Eintrag":"Eintr�ge")." ge�ndert.");
		}else{
			$sql="INSERT INTO riddles (riddle,answer) VALUES ('{$_POST['riddle']}','{$_POST['answer']}')";
		}
		db_query($sql) or output("`\$".db_error(LINK)."`0`n`#$sql`0`n");
	}
	if ($_GET['op']=="del"){
		$sql = "DELETE FROM riddles WHERE id={$_GET['id']}";
		db_query($sql);
		if (db_affected_rows()>0){
			output("R�tsel gel�scht`n`n");
		}else{
			output("R�tsel nicht gel�scht: ".db_error(LINK));
		}
		$_GET['op']="";
	}
	if ($_GET['op']==""){
		$sql = "SELECT * FROM riddles ORDER BY riddle";
		$result = db_query($sql) or die(db_error(LINK));
		addnav("R�tsel hinzuf�gen","riddleeditor.php?op=add");
		output("<table><tr><td>Ops</td><td width='50%'>R�tsel</td><td>L�sung</td></tr>",true);
		addnav("","riddleeditor.php");
		for ($i=0;$i<db_num_rows($result);$i++){
			$row = db_fetch_assoc($result);
			output("<tr><td valign='top'> [<a href='riddleeditor.php?op=edit&id={$row['id']}'>Edit</a>|".
			"<a href='riddleeditor.php?op=del&id={$row['id']}' onClick='return confirm(\"Bist du dir sicher, dass du dieses R�tsel l�schen willst?\");'>Del</a>] </td><td>",true);
			addnav("","riddleeditor.php?op=edit&id={$row['id']}");
			addnav("","riddleeditor.php?op=del&id={$row['id']}");
			output($row['riddle']);
			output("</td><td>",true);
			output($row['answer']);
			output("</td></tr>",true);
		}
		output("</table>",true);
	}else{
		if ($_GET['op']=="edit" || $_GET['op']=="add"){
			if ($_GET['op']=="edit"){
				$sql = "SELECT * FROM riddles WHERE id={$_GET['id']}";
				$result = db_query($sql) or die(db_error(LINK));
				if (db_num_rows($result)<>1){
					output("`4Fehler`0, dieses R�tsel wurde nicht gefunden!");
				}else{
					$row = db_fetch_assoc($result);
				}
			}
			output("<form action='riddleeditor.php' method='POST'>",true);
			output("<input name='id' value=\"".HTMLEntities($_GET[id])."\" type='hidden'>",true);
			output("<table border='0' cellpadding='2' cellspacing='0'>",true);
			output("<tr><td>R�tsel:</td><td><textarea name='riddle' class='input' cols='50' rows='9'>".HTMLentities(str_replace('`','``',$row['riddle']))."</textarea></td></tr>",true);
			output("<tr><td>Antwort: </td><td><input name='answer' maxlength='250' size='50' value=\"".HTMLentities($row['answer'])."\"></td></tr>",true);
			output("<tr><td colspan='2'><input type='hidden' name='save' value='Save'><input type='submit' class='button' name='submit' value='Speichern'></td></tr>",true);
			output("</table>",true);
			output("</form>",true);
			addnav("","riddleeditor.php");
		}else{

		}
		addnav("Zur�ck zum R�tsel-Editor","riddleeditor.php");
	}
}else{
	output("Weil du versucht hast, die G�tter zu betr�gen, wurdest du niedergeschmettert!");
	addnews("`&".$session['user']['name']." wurde f�r den Versuch, die G�tter zu betr�gen, niedergeschmettert (hat versucht die Superuser-Seiten zu hacken).");
	$session['user']['hitpoints']=0;
}
page_footer();
?>