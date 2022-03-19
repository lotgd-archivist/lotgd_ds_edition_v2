<?	
require_once "common.php";
su_check(SU_RIGHT_EDITORWORLD,true);

//select distinct creaturelevel,max(creaturehealth) as creaturehealth,max(creatureattack) as creatureattack,max(creaturedefense) as creaturedefense,max(creatureexp) as creatureexp,max(creaturegold) as creaturegold from creatures where creaturelevel<17 group by creaturelevel;
$creaturestattable="
+---------------+----------------+----------------+-----------------+-------------+--------------+
| creaturelevel | creaturehealth | creatureattack | creaturedefense | creatureexp | creaturegold |
+---------------+----------------+----------------+-----------------+-------------+--------------+
|             1 |             11 |              1 |               1 |          14 |           36 |
|             2 |             22 |              3 |               3 |          24 |           97 |
|             3 |             33 |              5 |               4 |          34 |          148 |
|             4 |             44 |              7 |               6 |          45 |          162 |
|             5 |             55 |              9 |               7 |          55 |          198 |
|             6 |             66 |             11 |               8 |          66 |          234 |
|             7 |             77 |             13 |              10 |          77 |          268 |
|             8 |             88 |             15 |              11 |          89 |          302 |
|             9 |             99 |             17 |              13 |         101 |          336 |
|            10 |            110 |             19 |              14 |         114 |          369 |
|            11 |            121 |             21 |              15 |         127 |          402 |
|            12 |            132 |             23 |              17 |         141 |          435 |
|            13 |            143 |             25 |              18 |         156 |          467 |
|            14 |            154 |             27 |              20 |         172 |          499 |
|            15 |            165 |             29 |              21 |         189 |          531 |
|            16 |            176 |             31 |              22 |         207 |          563 |
+---------------+----------------+----------------+-----------------+-------------+--------------+
";
$creaturestats=Array();
$creaturestattable=split("\n",$creaturestattable);
$x=0;
while (list($key,$val)=each($creaturestattable)){
	if (strpos($val,"|")!==false){
		//echo("$val`n");
		$x++;
		$a = split("\\|",$val);
		if ($x==1)
		{
			$stats=array();
			while (list($key1,$val1)=each($a))
			{
				if (trim($val1)>"") {
					$stats[$key1]=trim($val1);
					//output($val1." is col $key1`n");
				}
			}
		}else{
			reset($stats);
			while (list($key1,$val1)=each($stats)){
				$creaturestats[(int)$a[1]][$val1]=trim($a[$key1]);
				//output ("[".(int)$a[1]."][$val1]=".trim($a[$key1])."`n");
			}
		}
	}
}

page_header("Creature Editor");





if (su_check(SU_RIGHT_EDITORWORLD)){
	addnav("G?Zurück zur Grotte","superuser.php");
	
	addnav('W?Zurück zum Weltlichen',$session['su_return']);
	if ($_POST[save]<>""){
		if (!isset($_POST['location'])) $_POST['location']=0;
		if ($_POST['id']!=''){
			$sql="UPDATE creatures SET ";
			//unset($_POST[save]);

			foreach ($_POST as $key=>$value)
			{
				if (substr($key,0,8)=="creature") 
				{
					$sql.="$key = '$value', ";
				}				
			}
			reset($creaturestats[(int)$_POST[creaturelevel]]);
			foreach($creaturestats[$_POST[creaturelevel]] as $key=>$value)
			{
				if ( $key!="creaturelevel" && substr($key,0,8)=="creature")
				{
					$sql.="$key = \"".addslashes($value)."\", ";
				}
			}
			$sql.=" location=\"".(int)($_POST['location'])."\", ";
			//$sql = substr($sql,0,strlen($sql)-2);
			$sql.= " createdby=\"".addslashes($session[user][login])."\" ";
			$sql.= " WHERE creatureid='$_POST[id]'";
			db_query($sql) or output("`\$".db_error(LINK)."`0`n`#$sql`0`n");
			output(db_affected_rows()." ".(db_affected_rows()==1?"Eintrag":"Einträge")." geändert.");
		}
		else
		{
			$cols = array();
			$vals = array();

			foreach ($_POST as $key=>$value)
			{
				if (substr($key,0,8)=="creature" || $key=="location") {
					array_push($cols,$key);
					array_push($vals,$value);
					//$sql.="$key = \"$val\", ";
				}
			}
			reset($creaturestats[(int)$_POST[creaturelevel]]);
			foreach($creaturestats[$_POST[creaturelevel]] as $key=>$value)
			{
				if ($key!="creaturelevel"){
					//$sql.="$key = \"".addslashes($val)."\", ";
					array_push($cols,$key);
					array_push($vals,$value);
				}
			}
			$sql="INSERT INTO creatures (".join(",",$cols).",createdby) VALUES (\"".join("\",\"",$vals)."\",\"".addslashes($session['user']['login'])."\")";
			//echo $sql;
			db_query($sql);
		}
	}
	if ($_GET[op]=="del"){
		$sql = "DELETE FROM creatures WHERE creatureid = \"$_GET[id]\"";
		db_query($sql);
		if (db_affected_rows()>0){
			output("Kreatur gelöscht`n`n");
		}else{
			output("Kreatur nicht gelöscht: ".db_error(LINK));
		}
		$_GET[op]="";
	}
	// if ($_GET['page']=="") $_GET['page']=0;


	if ($_GET[op]==""){

		// Pages-Mod by Eliwood, based on list.php
		$sql = "SELECT count(creatureid) AS c FROM creatures";
		$result = db_query($sql);
		$row = db_fetch_assoc($result);
		$totalcreatures = $row['c'];
		$createuresperpage=30;
		if ($_GET['page']=="") $_GET['page']=1;
		$pageoffset = (int)$_GET['page'];
		if ($pageoffset>0) $pageoffset--;
		$pageoffset*=$createuresperpage;
		$from = $pageoffset+1;
		$to = min($pageoffset+$createuresperpage,$totalcreatures);
		$limit=" LIMIT $pageoffset,$createuresperpage ";
		// End First-Teil

		$sql = "SELECT * FROM creatures ORDER BY creaturelevel,creaturename ASC $limit";
		$result = db_query($sql) or die(db_error(LINK));
		addnav("Eine Kreatur hinzufügen","creatures.php?op=add");

		// Start Secondteil
		addnav("Seiten");
		for ($i=0;$i<$totalcreatures;$i+=$createuresperpage){
			addnav("Seite ".($i/$createuresperpage+1)." (".($i+1)."-".min($i+$createuresperpage,$totalcreatures).")","creatures.php?page=".($i/$createuresperpage+1));
		}
		// End Secondteil & End Pages-Mod

		output("<table><tr><td>Ops</td><td>Kreaturname</td><td>Level</td><td>Waffe</td><td>Nachricht beim Tod</td><td>Autor</td></tr>",true);
		addnav("","creatures.php");
		for ($i=0;$i<db_num_rows($result);$i++){
			$row = db_fetch_assoc($result);
			if ($row[creaturelevel]==17 || $row[creaturelevel]==18){
				output("<tr><td> [Edit|Del] </td><td>",true);
			}else{
				output("<tr><td> [<a href='creatures.php?op=edit&page=".$_GET['page']."&id=$row[creatureid]'>Edit</a>|".
				"<a href='creatures.php?op=del&page=".$_GET['page']."&id=$row[creatureid]' onClick='return confirm(\"Bist du dir sicher, dass du diese Kreatur löschen willst?\");'>Del</a>] </td><td>",true);
				addnav("","creatures.php?op=edit&page=".$_GET['page']."&id=$row[creatureid]");
				addnav("","creatures.php?op=del&page=".$_GET['page']."&id=$row[creatureid]");
			}
			output($row[creaturename]);
			output("</td><td>",true);
			output($row[creaturelevel]);
			output("</td><td>",true);
			output($row[creatureweapon]);
			output("</td><td>",true);
			output($row[creaturelose]);
			output("</td><td>",true);
			output($row[createdby]);
			output("</td></tr>",true);
		}
		output("</table>",true);
	}else{
		if ($_GET[op]=="edit" || $_GET[op]=="add"){
			if ($_GET[op]=="edit"){
				$sql = "SELECT * FROM creatures WHERE creatureid=$_GET[id]";
				$result = db_query($sql) or die(db_error(LINK));
				if (db_num_rows($result)<>1){
					output("`4Fehler`0, diese Kreatur wurde nicht gefunden!");
				}else{
					$row = db_fetch_assoc($result);
				}
			}
			output("<form action='creatures.php' method='POST'>",true);
			output("<input name='id' value=\"".HTMLEntities($_GET[id])."\" type='hidden'>",true);
			output("<table border='0' cellpadding='2' cellspacing='0'>",true);
			output("<tr><td>Kreaturname:</td><td><input name='creaturename' maxlength='50' value=\"".HTMLEntities($row[creaturename])."\"></td></tr>",true);
			output("<tr><td>Waffe: </td><td><input name='creatureweapon' maxlength='50' value=\"".HTMLEntities($row[creatureweapon])."\"></td></tr>",true);
			output("<tr><td colspan='2'>Nachricht beim Tod: <br><input name='creaturelose' size='65' maxlength='120' value=\"".HTMLEntities($row[creaturelose])."\"></td></tr>",true);
			output("<tr><td>Level: </td><td><select name='creaturelevel'>",true);
			for ($i=1;$i<=16;$i++){
				output("<option value='$i'".($row[creaturelevel]==$i?" selected":"").">$i</option>\n",true);
			}
			output("</select></td></tr>",true);
			output("<tr><td>Kreatur ist auch auf dem Friedhof</td><td><input type='radio' name='location' value='1'".($row['location']==1?" checked":"").">Ja <input type='radio' name='location' value='0'".($row['location']==0?" checked":"").">Nein </td></tr>",true);
			output("<tr><td colspan='2'><input type='hidden' name='save' value='Save'><input type='submit' class='button' name='submit' value='Speichern'></td></tr>",true);
			output("</table>",true);
			output("</form>",true);
			addnav("","creatures.php");
		}
		addnav("Zurück zum Monster-Editor","creatures.php?page=".$_GET['page']."");
	}
}

page_footer();
?>
