<?

//Gefixxt von Devilzimti..

// Modified by Maris

$file = "specialty_blank";  // Ersetze blank mit dem namen der Datei hinter specialty_
// Alle blank müssen ersetzt werden!

function specialty_blank_info()
{
	global $info,$file;
	$info = array(
	"author"=>"", // Dein Name
	"version"=>"", // Die Versionsnummer
	"download"=>"",  // eventueller Download (leerlassen, falls kein Download gewünscht
	"filename"=>$file,  // So lassen
	"specname"=>"Nichts", // Name der Spezialfähigkeit
	"color"=>"`&",  // Farbcode
	"category"=>"Leere", // Kategorie
	"fieldname"=>"blank" // Feldname  (Für Level & Anwendungen)
	);
}

function specialty_blank_install()
{
	global $info;
	$sql  = "INSERT INTO specialty (filename,usename,specname,category,author,active) ";
	$sql .= "VALUES ('".$info['filename']."','".$info['fieldname']."','".$info['specname']."','".$info['category']."','".$info['author']."','0')";
	db_query($sql);
	// Ab hier Optionale Datenbankeinträge
}

function specialty_blank_uninstall()
{
	global $info;
	$sql  = "DELETE FROM specialty WHERE filename='".$info['filename']."'";
	db_query($sql);
	// Die Installierten, Optionale Datenbankeinträge Rückgängig machen
}

function specialty_blank_run($underfunction,$mid=0,$beginlink="forest.php?op=fight",$varvar="session")
{
	global $session,$info,$script,$cost_low,$cost_medium,$cost_high,$resline;

	switch($underfunction)
	{
		case "fightnav":
		// ~~~~ Mit den Texten ersetzen
		// Erste Anwendung + Titelschema
		if (($varvar=="session"?$session['user']['specialtyuses'][$info['fieldname'].'uses']:$GLOBALS[$varvar]['specialtyuses'][$info['fieldname'].'uses'])>0)
		{
			addnav("~~~~", "");
			addnav("&#149; ~~~~`7 (1/".($varvar=="session"?$session['user']['specialtyuses'][$info['fieldname'].'uses']:$GLOBALS[$varvar]['specialtyuses'][$info['fieldname'].'uses']).")`0",""
			.$beginlink."&skill=".$info['fieldname']."&l=1",true);
		}

		// 2. Anwendung
		if (($varvar=="session"?$session['user']['specialtyuses'][$info['fieldname'].'uses']:$GLOBALS[$varvar]['specialtyuses'][$info['fieldname'].'uses'])>1)
		addnav("&#149; ~~~~`7 (2/".($varvar=="session"?$session['user']['specialtyuses'][$info['fieldname'].'uses']:$GLOBALS[$varvar]['specialtyuses'][$info['fieldname'].'uses']).")`0",
		$beginlink."&skill=".$info['fieldname']."&l=2",true);

		// 3. Anwendung
		if (($varvar=="session"?$session['user']['specialtyuses'][$info['fieldname'].'uses']:$GLOBALS[$varvar]['specialtyuses'][$info['fieldname'].'uses'])>2)
		addnav("&#149; ~~~~`7 (3/".($varvar=="session"?$session['user']['specialtyuses'][$info['fieldname'].'uses']:$GLOBALS[$varvar]['specialtyuses'][$info['fieldname'].'uses']).")`0",
		$beginlink."&skill=".$info['fieldname']."&l=3",true);

		// 4. Anwendung
		if (($varvar=="session"?$session['user']['specialtyuses'][$info['fieldname'].'uses']:$GLOBALS[$varvar]['specialtyuses'][$info['fieldname'].'uses'])>4)
		addnav("&#149; ~~~~`7 (5/".($varvar=="session"?$session['user']['specialtyuses'][$info['fieldname'].'uses']:$GLOBALS[$varvar]['specialtyuses'][$info['fieldname'].'uses']).")`0",
		$beginlink."&skill=".$info['fieldname']."&l=5",true);
		break;
		case "backgroundstory":
		// Hintergrundgeschichte, verwende Funktion output
		break;
		case "link":
		// ~~ Ersetzen mit dem Text, den Rest so lassen
		output("<a href='newday.php?setspecialty=".$mid.$resline."'>~~ (`\$Dunkle Künste`0)</a>`n",true);
		addnav("","newday.php?setspecialty=".$mid.$resline);
		addnav($info['color'].$info['specname']."`0","newday.php?setspecialty=".$mid.$resline);
		break;
		case "buff":
		// Die Buffs

$GLOBALS[$varvar]['specialtyuses']=unserialize($GLOBALS[$varvar]['specialtyuses']);

		if (($varvar=="session"?$session['user']['specialtyuses'][$info['fieldname'].'uses']:$GLOBALS[$varvar]['specialtyuses'][$info['fieldname'].'uses']) >= (int)$_GET['l']){
			$creaturedmg = 0;
			switch((int)$_GET['l']){
				case 1:
				$buff = array(
				// Buff für Anwendung 1
				);
				if($varvar=="session") $session['bufflist'][$info['fieldname'].'1'] = $buff;
				else $GLOBALS[$varvar]['bufflist'][$info['fieldname'].'1'] = $buff;
				break;

				case 2:
				$buff = array(
				// Buff für Anwendung 2
				);
				if($varvar=="session") $session['bufflist'][$info['fieldname'].'2'] = $buff;
				else $GLOBALS[$varvar]['bufflist'][$info['fieldname'].'2'] = $buff;
				break;

				case 3:
				$buff = array(
				// Buff für Anwendung 3
				);
				if($varvar=="session") $session['bufflist'][$info['fieldname'].'3'] = $buff;
				else $GLOBALS[$varvar]['bufflist'][$info['fieldname'].'3'] = $buff;
				break;

				case 5:
				$buff = array(
				// Buff für Anwendung 4
				);
				if($varvar=="session")
				{
					$session['bufflist'][$info['fieldname'].'5'] = $buff;
					$session['user']['reputation']--;  // Stärkster Zauber verwendet? Nicht sehr ehrenhaft...
				}
				else $GLOBALS[$varvar]['bufflist'][$info['fieldname'].'5'] = $buff;
				break;
			}
			if ($varvar=="session") $session['user']['specialtyuses'][$info['fieldname'].'uses']-=(int)$_GET[l];
			else $GLOBALS[$varvar]['specialtyuses'][$info['fieldname'].'uses']-=(int)$_GET[l];
		}else{
			$buff = array(
			// Buff, falls User zu wenig Anwenungen hat (Trifft normalerweise nicht ein, aber vielleicht is es ein Cheater ;)
			);
			if($varvar=="session")
			{
				$session['bufflist'][$info['fieldname'].'0'] = $buff;
				$session['user']['reputation']--; // Cheater sind unehrenhaft =)
			}
			else $GLOBALS[$varvar]['bufflist'][$info['fieldname'].'0'] = $buff;
		}

        $GLOBALS[$varvar]['specialtyuses']=serialize($GLOBALS[$varvar]['specialtyuses']);

		break;

		case "academy_desc":
		// Akademie - Beschreibung der Lehrstunden
		break;


		case "academy_pratice":
		// Praktische Stunde & User betrunken, der Text

		// Nicht weglöschen, aber anpassen (Produkt ändern)
		$session[user][hitpoints] = $session[user][hitpoints] - $session[user][hitpoints] * 0.2;
		break;


		case "weahter":
		// Eingreifen in Wetterbonus
		break;
	}
