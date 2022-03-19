<?php

// Geschenke-Editor
// Erlaubt Erstellung, Löschung und Anpassung der Items aus dem Geschenkeladen.
// Erfordert : newgiftshop.php (modifiziert),
//             Datenbanktabelle "gift" als kopie von "items" mit zusätzlichen Feldern
//
//
// by Maris (Maraxxus@gmx.de)
// Abgeleitet von :

// 11072004

// Item Editor
// by anpera; based on mount editor
//
// This is for administer items of all kind with anpera's item table
// (first introduced in houses mod)
// items table REQUIRED!
//
// insert:
// 	if ($session[user][superuser]>=2) addnav("Item Editor","itemeditor.php");
// into menu of superuser.php
//

require_once "common.php";

page_header("Geschenke Editor");
addnav("G?Zurück zur Grotte","superuser.php");


addnav('W?Zurück zum Weltlichen',$session['su_return']);

if ($_GET['op']=="del"){
	$sql = "DELETE FROM gift WHERE id=$_GET[id]";
	db_query($sql);
	$_GET['op']="";
	$_GET['show']=$_GET['show']; // huh? weshalb hab ich das geschrieben?
}

if ($_GET['op']=="add"){
	output("Geschenk erzeugen:`n");
	addnav("Geschenke Editor","gifteditor.php");
    itemform(array());
    
}elseif ($_GET['op']=="edit"){
	addnav("Geschenke Editor","gifteditor.php");
	$sql = "SELECT * FROM gift WHERE id='{$_GET['id']}'";
	$result = db_query($sql);
	if (db_num_rows($result)<=0){
		output("`iGeschenk nicht vorhanden.`i");
	}else{
		output("Geschenke Editor:`n");
		$row = db_fetch_assoc($result);
		$row['buff']=unserialize($row['buff']);
		itemform($row);
	}
}elseif ($_GET['op']=="save"){
	$buff = array();
	reset($_POST['gift']['buff']);
	if (isset($_POST['gift']['buff']['activate'])) $_POST['gift']['buff']['activate']=join(",",$_POST['gift']['buff']['activate']);
	while (list($key,$val)=each($_POST['gift']['buff'])){
		if ($val>""){
			$buff[$key]=stripslashes($val);
		}
	}
	$_POST['gift']['buff']=$buff;
	reset($_POST['gift']);
	$keys='';
	$vals='';
	$sql='';
	$i=0;
	while (list($key,$val)=each($_POST['gift'])){
		if (is_array($val)) $val = addslashes(serialize($val));
		if ($_GET['id']>""){
			$sql.=($i>0?",":"")."$key='$val'";
		}else{
			$keys.=($i>0?",":"")."$key";
			$vals.=($i>0?",":"")."'$val'";
		}
		$i++;
	}
	if ($_GET['id']>""){
		$sql="UPDATE gift SET $sql WHERE id='{$_GET['id']}'";
	}else{
		$sql="INSERT INTO gift ($keys) VALUES ($vals)";
	}
	db_query($sql);
	if (db_affected_rows()>0){
		output("Geschenk gespeichert!");
	}else{
		output("Geschenk nicht gespeichert: $sql");
	}
	addnav("Geschenke Editor","gifteditor.php");
}else{
		$ppp=50; // Player Per Page to display
		if (!$_GET[limit]){
			$page=0;
		}else{
			$page=(int)$_GET[limit];
			addnav("Vorherige Seite","gifteditor.php?limit=".($page-1)."");
		}
		$limit="".($page*$ppp).",".($ppp+1);
		$sql = "SELECT gift.* FROM gift ORDER BY id LIMIT $limit";
		output("<table>",true);
		output("<tr><td>Ops</td><td>Name</td><td>Effekt</td></tr>",true);
		$result = db_query($sql);
		if (db_num_rows($result)>$ppp) addnav("Nächste Seite","gifteditor.php?limit=".($page+1)."");
		$cat = "";
		for ($i=0;$i<db_num_rows($result);$i++){
			$row = db_fetch_assoc($result);
			output("<tr>",true);
			output("<td>[ <a href='gifteditor.php?op=edit&id=$row[id]'>Edit</a> |",true);
			addnav("","gifteditor.php?op=edit&id=$row[id]");
			output(" <a href='gifteditor.php?op=del&id=$row[id]' onClick=\"return confirm('Diesen Gegenstand wirklich löschen?');\">Löschen</a> ]</td>",true);
			addnav("","gifteditor.php?op=del&id=$row[id]");
			output("<td>$row[name]</td>",true);
			output("<td>$row[effect]</td>",true);
			output("</tr>",true);
		}
		output("</table>",true);
		addnav("Geschenk hinzufügen","gifteditor.php?op=add");
}

function itemform($gift){
	global $output;
	output("<form action='gifteditor.php?op=save&id=$gift[id]' method='POST'>",true);
	addnav("","gifteditor.php?op=save&id=$gift[id]");
	$output.="<table>";
	$output.="<tr><td>Geschenk Name:</td><td><input name='gift[name]' value=\"".htmlentities($gift['name'])."\" maxlength='25'></td></tr>";
	$output.="<tr><td>Geschenk Effekt:</td><td><input name='gift[effect]' value=\"".htmlentities($gift['effect'])."\" maxlength='200'></td></tr>";
	$output.="<tr><td>Item Beschreibung:</td><td><input name='gift[description]' value=\"".htmlentities($gift['description'])."\" maxlength='200'></td></tr>";
	$output.="<tr><td>Geschenk Wert (Edelsteine):</td><td><input name='gift[gems]' value=\"".htmlentities((int)$gift['gems'])."\" size='5'></td></tr>";
	$output.="<tr><td>Geschenk Wert (Gold):</td><td><input name='gift[gold]' value=\"".htmlentities((int)$gift['gold'])."\" size='5'></td></tr>";
	$output.="<tr><td>Gefallen+ minimal:</td><td><input name='gift[deathpower_u]' value=\"".htmlentities((int)$gift['deathpower_u'])."\" size='5'></td></tr>";
	$output.="<tr><td>Gefallen+ maximal:</td><td><input name='gift[deathpower_o]' value=\"".htmlentities((int)$gift['deathpower_o'])."\" size='5'></td></tr>";
	$output.="<tr><td>Ansehen+ minimal:</td><td><input name='gift[reputation_u]' value=\"".htmlentities((int)$gift['reputation_u'])."\" size='5'></td></tr>";
	$output.="<tr><td>Ansehen+ maximal:</td><td><input name='gift[reputation_o]' value=\"".htmlentities((int)$gift['reputation_o'])."\" size='5'></td></tr>";
	$output.="<tr><td>Versteckter Wert:</td><td><input name='gift[hvalue]' value=\"".htmlentities((int)$gift['hvalue'])."\" size='5'></td></tr>";
	$output.="<tr><td valign='top'>Geschenk Buff:</td><td>";
	$output.="<b>Meldungen:</b><Br/>";
	$output.="Buff Name: <input name='gift[buff][name]' value=\"".htmlentities($gift['buff']['name'])."\"><Br/>";
	$output.="Meldung jede Runde: <input name='gift[buff][roundmsg]' value=\"".htmlentities($gift['buff']['roundmsg'])."\"><Br/>";
	$output.="Ablaufmeldung: <input name='gift[buff][wearoff]' value=\"".htmlentities($gift['buff']['wearoff'])."\"><Br/>";
	$output.="Effektmeldung: <input name='gift[buff][effectmsg]' value=\"".htmlentities($gift['buff']['effectmsg'])."\"><Br/>";
	$output.="Kein Schaden Meldung: <input name='gift[buff][effectnodmgmsg]' value=\"".htmlentities($gift['buff']['effectnodmgmsg'])."\"><Br/>";
	$output.="Fehlgeschlagen Meldung: <input name='gift[buff][effectfailmsg]' value=\"".htmlentities($gift['buff']['effectfailmsg'])."\"><Br/>";
	$output.="<Br/><b>Effekt:</b><Br/>";
	$output.="Hält Runden (nach Aktivierung): <input name='gift[buff][rounds]' value=\"".htmlentities($gift['buff']['rounds'])."\" size='5'><Br/>";
	$output.="Angriffsmulti Spieler: <input name='gift[buff][atkmod]' value=\"".htmlentities($gift['buff']['atkmod'])."\" size='5'><Br/>";
	$output.="Verteidigungsmulti Spieler: <input name='gift[buff][defmod]' value=\"".htmlentities($gift['buff']['defmod'])."\" size='5'><Br/>";
	$output.="Regen: <input name='gift[buff][regen]' value=\"".htmlentities($gift['buff']['regen'])."\"><Br/>";
	$output.="Diener Anzahl: <input name='gift[buff][minioncount]' value=\"".htmlentities($gift['buff']['minioncount'])."\"><Br/>";
	$output.="Min Badguy Damage: <input name='gift[buff][minbadguydamage]' value=\"".htmlentities($gift['buff']['minbadguydamage'])."\" size='5'><Br/>";
	$output.="Max Badguy Damage: <input name='gift[buff][maxbadguydamage]' value=\"".htmlentities($gift['buff']['maxbadguydamage'])."\" size='5'><Br/>";
	$output.="Lifetap: <input name='gift[buff][lifetap]' value=\"".htmlentities($gift['buff']['lifetap'])."\" size='5'><Br/>";
	$output.="Damage shield: <input name='gift[buff][damageshield]' value=\"".htmlentities($gift['buff']['damageshield'])."\" size='5'> (multiplier)<Br/>";
	$output.="Badguy Damage mod: <input name='gift[buff][badguydmgmod]' value=\"".htmlentities($gift['buff']['badguydmgmod'])."\" size='5'> (multiplier)<Br/>";
	$output.="Badguy Atk mod: <input name='gift[buff][badguyatkmod]' value=\"".htmlentities($gift['buff']['badguyatkmod'])."\" size='5'> (multiplier)<Br/>";
	$output.="Badguy Def mod: <input name='gift[buff][badguydefmod]' value=\"".htmlentities($gift['buff']['badguydefmod'])."\" size='5'> (multiplier)<Br/>";
	$output.="<Br/><b>Aktiviert bei:</b><Br/>";
	$output.="<input type='checkbox' name='gift[buff][activate][]' value=\"roundstart\"".(strpos($gift['buff']['activate'],"roundstart")!==false?" checked":"")."> Start der Runde<Br/>";
	$output.="<input type='checkbox' name='gift[buff][activate][]' value=\"offense\"".(strpos($gift['buff']['activate'],"offense")!==false?" checked":"")."> Bei Angriff<Br/>";
	$output.="<input type='checkbox' name='gift[buff][activate][]' value=\"defense\"".(strpos($gift['buff']['activate'],"defense")!==false?" checked":"")."> Bei Verteidigung<Br/>";
	$output.="<Br/>";
	$output.="</td></tr>";
	$output.="</table>";
	$output.="<input type='submit' class='button' value='Speichern'></form>";
}

page_footer();
?>
