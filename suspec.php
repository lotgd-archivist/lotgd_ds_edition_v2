<?php
 ####################################
# Spezialfähigkeiten-Module          # 
# 26. Juli 2005                      # 
# © by Eliwood (Basilius)            # 
# Editor:                            # 
# © by Devilzimti (logd-game.de)     # 
 #################################### 

require_once "common.php";
page_header("Spezialfähigkeiten");
su_check(SU_RIGHT_EDITORSPECIALTIES,true);
global $info,$session,$info;

// Navigation
addnav("Module");
addnav("Module anzeigen",($_GET['op']==""?"":"suspec.php"));
addnav("Module installieren",($_GET['op']=="install"?"":"suspec.php?op=install"));
addnav("Zur&uuml;ck",false,true);
addnav("Zur&uuml;ck zur Grotte","superuser.php",true);
addnav("Zur&uuml;ck zum Weltlichen","village.php",true);
// Ende Navigation

if($_GET['op']=='') {
	$sql = "SELECT * FROM specialty";
	$result = db_query($sql);
	if(db_num_rows($result)<=0) {
		output("`4Keine Module installiert");
	} else {
		$sql2 = "SELECT * FROM specialty WHERE active='1'";
		$result2 = db_query($sql2);
		$rows1 = db_num_rows($result);
		$rows2 = db_num_rows($result2);

		output($session['message']);

		output("`#Installierte Module: `^".$rows1."`n");
		output("`#Aktive Module: `^".$rows2."`n");
		output("`#Nicht aktive Module: `^".($rows1-$rows2)."`n");
		output("`n`n");
		rawoutput("<table bgcolor='#999999' cellpadding=2 cellspacing=1>");
		rawoutput("<tr class='trhead'><td>");
		output("Name");
		rawoutput("</td><td>");
		output("Autor");
		rawoutput("</td><td>");
		output("Version");
		rawoutput("</td><td>");
		output("Dateiname");
		rawoutput("</td></tr>");
		$i = 0;
		while($row = db_fetch_assoc($result)) {
			$i++;
			$bgcolor = ($i%2==1?"trlight":"trdark");
			require_once "module/".$row['filename'].".php";
			$f1 = $file."_info";
			$f1();
			output('`c');
			rawoutput("<tr class='$bgcolor'><td>");
			output($info['color'].$info['specname']);
			rawoutput("</td><td>");
			output($info['author']);
			rawoutput("</td><td>");
			output($info['version']);
			rawoutput("</td><td>");
			output($file.".php");
			rawoutput("</td></tr>");
			rawoutput("<tr class='$bgcolor'><td colspan='2'>");
			output("`2Kategorie: `^".$info['category']);
			rawoutput("</td><td colspan='2'>");
			output(($row['active']==1?"`&[`^<a href='suspec.php?op=deactivate&filename=".$row['filename']."'>Deaktivieren</a>`&]":"`&[<a href='suspec.php?op=activate&filename=".$row['filename']."'>`@Aktivieren</a>`&]")." | [`^<a href='suspec.php?op=uninstall&filename=".$row['filename']."' onClick=\"return confirm('".$info['specname']." wirklich deinstallieren? Wenn du ".$info['specname']." deinstallierst, müssen alle Benutzer mit dieser Specialty ihre Specialty neu wählen.')\";>Deinstallieren</a>`&]",true);
			rawoutput("</td></tr>");
			rawoutput("<tr bgcolor=''><td colspan='4'></td></tr>");
			output('`c');
			addnav('','suspec.php?op=activate&filename='.$row['filename']);
			addnav('','suspec.php?op=deactivate&filename='.$row['filename']);
			addnav('','suspec.php?op=uninstall&filename='.$row['filename']);
		}
		rawoutput("</table>");
	}
} elseif ($_GET['op']=='install') {
	//(c) Devilzimti
	if ($_GET['filename']){
		require_once('module/'.$_GET['filename'].'.php');
		$f1 = $_GET['filename'].'_info';
		$f1();
		$f2 = $_GET['filename'].'_install';
		$f2();
		output("`b`4Modul (".$info['specname'].") erfolgreich installiert!`b`n`n`n");
	}
	rawoutput("<table bgcolor='#999999' cellpadding=2 cellspacing=1>");
	rawoutput("<tr class='trhead'><td>");
	output("Name");
	rawoutput("</td><td>");
	output("Autor");
	rawoutput("</td><td>");
	output("Version");
	rawoutput("</td><td>");
	output("Dateiname");
	rawoutput("</td></tr>");

	$dir = dir("module");
	while (false !== ($modul = $dir->read())){
		// So.. Filtern ma die Dateien..
		if(strpos($modul,'.php') === false) continue; //Ist es ne PHP Datei?
		if(strpos($modul,'specialty_') === false) continue; //Ein Spezialfähigkeits Modul?
		if(strpos($modul,'blank') != false) continue; //Nicht da blank modul?
		if(db_num_rows(db_query('SELECT filename FROM specialty WHERE filename=\''.str_replace('.php','',$modul).'\''))>0) continue; //Modul installiert?
		$modul = str_replace('.php','',$modul);
		//So. Nun hohlen ma uns die Infos
		require_once('module/'.$modul.'.php');
		$f = str_replace('.php','',$modul).'_info';
		$f();
		//Dann wird alles schön ausgegeben Danke an Eliwood für das coole layout ;)
		$i++;
		$bgcolor = ($i%2==1?"trlight":"trdark");
		rawoutput("<tr class='$bgcolor'><td>");
		output($info['color'].$info['specname']);
		rawoutput("</td><td>");
		output($info['author']);
		rawoutput("</td><td>");
		output($info['version']);
		rawoutput("</td><td>");
		output($file.".php");
		rawoutput("</td></tr>");
		rawoutput("<tr class='$bgcolor'><td colspan='2'>");
		output("`2Kategorie: `^".$info['category']);
		rawoutput("</td><td colspan='2'>");
		output("`&[`^<a href='suspec.php?op=install&filename=".$info['filename']."'>Installieren</a>`&]",true);
		rawoutput("</td></tr>");
		rawoutput("<tr bgcolor=''><td colspan='4'></td></tr>");
		addnav('','suspec.php?op=install&filename='.$info['filename']);

	}
	if(!$i) rawoutput("<tr bgcolor=''><td colspan='4'>Keine uninstallierte Module gefunden!</td></tr>");

} elseif ($_GET['op']=='uninstall') {
	$row = db_fetch_assoc(db_query("SELECT specid,specname FROM specialty WHERE filename='".$_GET['filename']."'"));
	db_query("UPDATE accounts SET specialty='0' WHERE specialty='".$row['specid']."'"); //Wenn ein Modul gelöscht wurde müssen eben die Betroffenen die Konsequenzen ziehen.
	require_once('module/'.$_GET['filename'].'.php');
	$f1 = $_GET['filename'].'_info';
	$f1();
	$f2 = $_GET['filename'].'_uninstall';
	$f2();
	$session['message']='`n`n`c`b`4Das Modul '.$row['specname'].' wurder erfolgreich deinstalliert.`b`n`n`c';
	redirect('suspec.php');
} elseif ($_GET['op']=='activate') {
	db_query("UPDATE specialty SET active='1' WHERE filename='".$_GET['filename']."'");
	$session['message']='`c`b`4Das Modul '.$row['specname'].' wurder erfolgreich aktiviert.`b`n`n`c';
	redirect('suspec.php');
} elseif ($_GET['op']=='deactivate') {
	db_query("UPDATE specialty SET active='0' WHERE filename='".$_GET['filename']."'");
	$session['message']='`n`n`c`b`4Das Modul '.$row['specname'].' wurder erfolgreich deaktiviert.`b`n`n`c';
	redirect('suspec.php');
}


//$session['user']['wo'] = "Verschollen";
page_footer();
?>
