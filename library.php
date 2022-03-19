<?php
/*
* author: bibir (logd_bibir@email.de)
*      and Chaosmaker (webmaster@chaosonline.de)
*      for http://logd.chaosonline.de
*
* version: 1.2
*
*     a library with text from users to help other
* 	   a bit like faq
*
* details:
*  (15.11.04) start of idea
*  (15.01.05) project finished
*  (16.01.05) version 1.2: several minor bugfixes
*  (11.11.05) Talion: added feature for recommending books that are well done.
*/

require_once "common.php";

function lib_show_rules () {
	
	output('
	Jeder Spieler kann eigene Werke einreichen, die dann von den Bibliothekaren (Auf diesem Server: Die Moderatoren) begutachtet und entweder zugelassen oder abgelehnt werden können. `n
	Damit ein Buch durchgelassen werden kann, sollte es folgende Regeln und Richtlinien einhalten:`n
	`n- Angemessene Textlänge: Je nach Textart (Gedicht, Geschichte, Einführung in diese Welt etc.) sind unterschiedliche Textlängen erforderlich. Gedichte und Lieder sollten dabei eine Länge von 16 Zeilen nicht unterschreiten, wobei eine Zeile nicht nur aus 5 Wörtern bestehen sollte. Alle anderen Texte können von der Länge her variieren. Bei Einführungen in diese Welt sind auch kürzere Texte in Ordnung, wenn das Thema nicht mehr hergibt.  Allgemein sollte jedoch darauf geachtet werden, die Texte möglichst weit auszuführen. Geschichten der Art: „Ich ging in den Wald, schlachtete viele Monster ab, verlor einen Freund und ging nach Hause“ werden nicht durchgelassen. Ist der Verlauf der Geschichte jedoch einigermaßen ausführlich geschildert, wird es durchgelassen, sofern die anderen Regeln eingehalten werden.
	`n`n- Rechtschreibung: Bitte prüft eure Werke hierauf, ehe ihr sie einreicht! Fehler kommen vor und werden gerne von den Bibliothekaren behoben, allerdings nur in Maßen. Sie sind nicht dazu da, eure kompletten Texte Korrektur zu lesen. Texte, die vor Fehlern sprießen (Buchstabierung, Zeichensetzung), werden, ohne weiter gelesen zu werden, gelöscht. 
	`n`n- Themen: Ihr könnt natürlich über alles schreiben, was euch einfällt. Vorzugsweise sollte es jedoch mit diesem Spiel oder zumindest dieser Welt zu tun haben. Das heißt: Wenn ihr ein Buch über euren Kampf mit dem grünen Drachen schreiben möchtet, oder andere Fantasiewesen, tut das. Bücher hingegen über die 50 edelsten Kaffeesorten von heute etc. behaltet lieber für euch. Des Weiteren reicht bitte keine Werke über die Rassen ein, in denen ihr schreibt, dass sie sich nur auf die eine Weise verhalten oder es nur einen wahren Ursprung gäbe. Die Rassen auf diesem Server werden von jedem anders gespielt, da jeder ein anderes Hintergrundwissen zu ihnen hat. Wollt ihr also, über Ursprung etc. einer oder mehrerer Rassen schreiben, beginnt euer Buch z.B. mit „Eine Theorie besagt, dass…“. So tretet ihr niemandem auf die Füße und alle können sich an eurem Buch erfreuen. 
	`n`n- Qualität: Hier gibt es keine Richtlinien. Wenn die Bibliothekare ein Buch erhalten, von dem sie denken, dass es keiner lesen und es nur auf dem Server vermodern wird, werden sie es nicht durchlassen. Bemüht euch, gerade bei Erzählungen, spannend und gut leserlich zu schreiben. 
	`n`n- Zuletzt noch die wichtigste Regel: Sendet keine Bücher ein, die nicht von euch selbst sind! Texte, die ihr kopiert habt (Jeder Text wird überprüft!), werden sofort gelöscht, da diese urheberrechtlich geschützt sind und die Betreiber sich strafbar machen würden, wenn sie diese veröffentlichen würden. Achtet also hierauf besonders!	
	');
	
}


checkday();
addcommentary();
if(!isset($_GET['op'])) $_GET['op']="";

addnav('Bibliothek');


$sql = "SELECT count(bookid) AS anz FROM lib_books WHERE activated='1'";
$result = db_query($sql) or die(db_error(LINK));
$books = db_fetch_assoc($result);
page_header("Drachen Bibliothek");
output("`c`b`9Drachen Bibliothek des gesammelten Wissens in ".($books['anz']==1?'einem Band':$books['anz'].' Bänden')."`0`b`c`n");

switch($_GET['op']){
case "browse":
	addnav("H?Zurück in die Halle","library.php");
	if($session['user']['alive']) {
		addnav("Buch einreichen","library.php?op=offer");
	}
	output("`tDu ".(!$session['user']['alive'] ? 'schwebst' : 'gehst')." durch die Regalreihen und siehst, dass alle Bücher ordentlich nach Themen einsortiert sind.`n
	Folgende Themen stehen derzeit zur Auswahl:`n`n");
	$sql = "SELECT t.*, COUNT(b.bookid) as anz FROM lib_themes t
			LEFT JOIN lib_books b ON b.themeid=t.themeid AND b.activated='1'
			GROUP BY themeid
			ORDER BY listorder ASC";
	$result = db_query($sql) or die(db_error(LINK));
	output("<table cellpadding=2 cellspacing=1 bgcolor='#999999'><tr class='trhead'><td>Thema</td><td>Bücher</td></tr>",true);
	$bgclass = '';
	addnav("Themen");
	while ($row = db_fetch_assoc($result)) {
		$bgclass = ($bgclass=='trdark'?'trlight':'trdark');
		if ($row['anz']>0) {
			output("<tr class='$bgclass'><td><a href=\"library.php?op=theme&id=".$row['themeid']."\">",true);
			output($row['theme']);
			output("`0</a></td><td align='right'>".$row['anz']."</td></tr>",true);
		}
		else {
			output("<tr class='$bgclass'><td>",true);
			output($row['theme']);
			output("`0</td><td>kein Buch</td></tr>",true);
		}
		addnav("","library.php?op=theme&id=".$row['themeid']);
		addnav($row['theme'],"library.php?op=theme&id=".$row['themeid']);
	}
	output("</table>",true);
	break;

case "theme":
	addnav("H?Zurück in die Halle","library.php");
	//addnav("Andere Regale","library.php?op=browse");
	if($session['user']['alive']) {
		addnav("Buch einreichen","library.php?op=offer");
	}

	addnav("Themen");
	$sql = "SELECT themeid, theme FROM lib_themes ORDER BY listorder ASC";
	$result = db_query($sql) or die(db_error(LINK));
	while ($row = db_fetch_assoc($result)) {
		if ($row['themeid']!=$_GET['id']) {
			addnav($row['theme'],"library.php?op=theme&id=".$row['themeid']);
 		}
		else {
			addnav($row['theme'],'');
			$thistheme = $row['theme'];
		}
	}

	output("`c`b".$thistheme."`0`b`c");
	output("`n`6Zu diesem Thema stehen dir folgende Bücher zur Verfügung:`n`n");

	$sql = "SELECT title, bookid, author, recommended FROM lib_books
			WHERE themeid=".$_GET['id']." AND activated='1' ORDER BY listorder ASC";
	$result = db_query($sql) or die(db_error(LINK));
	output("<table cellpadding=2 cellspacing=1 bgcolor='#999999'><tr class='trhead'><td>Titel</td><td>Autor</td><td>Besonders Empfehlenswert?</td></tr>",true);
	if (db_num_rows($result)==0) {
		output("<tr class='trdark'><td colspan='3'>Es gibt leider bisher noch keine Bücher zu diesem Thema.</td></tr>",true);
	}
	else {
		addnav('Bücher');
		$bgclass = '';
		while ($row = db_fetch_assoc($result)) {
			$bgclass = ($bgclass=='trdark'?'trlight':'trdark');
			output("<tr class='$bgclass'><td><a href=\"library.php?op=book&bookid=".$row['bookid']."\">",true);
			output($row['title']);
			output("`0</a></td><td>",true);
			output($row['author']);
			output("`0</a></td><td align='center'>",true);
			output( ($row['recommended'] ? 'Ja' : ' - ') );
			output("`0</td></tr>",true);
			addnav("","library.php?op=book&bookid=".$row['bookid']);
			addnav($row['title'],'library.php?op=book&bookid='.$row['bookid']);
		}
	}
	output("</table>",true);
	break;

case 'recommended':
	addnav("H?Zurück in die Halle","library.php");
	
	output('`n`6Die folgenden Bücher werden von den Göttern als besonders empfehlenswert angesehen. Es lohnt sich also bestimmt, hier einen Blick hineinzuwerfen:`n`n');
	
	$sql = 'SELECT b.*,t.theme FROM lib_books b
			LEFT JOIN lib_themes t ON t.themeid=b.themeid 
			WHERE recommended = 1 AND activated = "1"
			ORDER BY t.themeid ASC, t.listorder DESC, b.listorder DESC';
	
	$result = db_query($sql) or die(db_error(LINK));
	output("<table cellpadding=2 cellspacing=1 bgcolor='#999999'><tr class='trhead'><td>Titel</td><td>Autor</td></tr>",true);
	if (db_num_rows($result)==0) {
		output("<tr class='trdark'><td colspan='2'>Es gibt leider bisher noch keine empfohlenen Bücher.</td></tr>",true);
	}
	else {
		addnav('Bücher');
		$bgclass = '';
		$last_theme = 0;
		while ($row = db_fetch_assoc($result)) {
			
			if($last_theme != $row['themeid']) {
				output('<tr class="trhead"><td colspan="2">`b'.$row['theme'].'`b</td></tr>',true);
				$last_theme = $row['themeid'];
			}	
			
			$bgclass = ($bgclass=='trdark'?'trlight':'trdark');
			output("<tr class='$bgclass'><td><a href=\"library.php?op=book&bookid=".$row['bookid']."\">",true);
			output($row['title']);
			output("`0</a></td><td>",true);
			output($row['author']);
			output("`0</td></tr>",true);
			addnav("","library.php?op=book&bookid=".$row['bookid']);
			addnav($row['title'],'library.php?op=book&bookid='.$row['bookid']);
		}
	}
	output("</table>",true);
	
	break;

case "book":
	addnav("H?Zurück in die Halle","library.php");
	//addnav("Ein anderes Thema","library.php?op=browse");

	$sql = "SELECT t.theme, b.themeid, b.title, b.book, b.author, b.bookid FROM lib_books b
			LEFT JOIN lib_themes t USING(themeid)
			WHERE bookid=".$_GET['bookid'];
	$result = db_query($sql) or die(db_error(LINK));
	$row = db_fetch_assoc($result);
	
	// Views erhöhen
	if(!$session['bookview'.$b['bookid']]) {
		
		$session['bookview'.$b['bookid']] = true;
		$sql = 'UPDATE lib_books SET views=views+1 WHERE bookid='.$row['bookid'];
		db_query($sql);
		
	}

	//addnav("R?Zurück ans Regal","library.php?op=theme&id=".$row['themeid']);
	addnav("Buch einreichen","library.php?op=offer");
	addnav("Empfehlenswerte Lektüre","library.php?op=recommended");

	addnav("Themen");
	$sql = "SELECT themeid, theme FROM lib_themes ORDER BY listorder ASC";
	$result = db_query($sql) or die(db_error(LINK));
	while ($row2 = db_fetch_assoc($result)) {
		addnav($row2['theme'],"library.php?op=theme&id=".$row2['themeid']);
	}

	addnav('Bücher');
	$sql = 'SELECT title, bookid FROM lib_books WHERE themeid='.$row['themeid'].' AND activated="1" ORDER BY listorder ASC';
	$result = db_query($sql) or die(db_error(LINK));
	while ($row2 = db_fetch_assoc($result)) {
		if ($row2['bookid']!=$_GET['bookid']) addnav($row2['title'],'library.php?op=book&bookid='.$row2['bookid']);
		else addnav($row2['title'],'');
	}

	//nichts editierbar
	output("<table cellpadding=2 cellspacing=1 bgcolor='#999999'><tr class='trdark'><td>Thema:</td><td>",true);
	output($row['theme']);
	output("`0</td></tr><tr class='trlight'><td>Titel:</td><td>",true);
	output($row['title']);
	output("`0</td></tr><tr class='trdark'><td>Autor:</td><td>",true);
	output($row['author']);
	output("`0</td></tr><tr class='trlight'><td colspan='2'>",true);
	output(str_replace("\n",'`n',$row['book']));
	output('</td></tr></table>',true);
	break;

case "offer":
	addnav("H?Zurück in die Halle","library.php");
	if ($_GET['subop']=="save" && !empty($_POST['title']) && !empty($_POST['book'])) {
		addnav("Weiteres Buch schreiben","library.php?op=offer");
		output("`tDein Buch wurde zum Druck eingereicht.`0");
		// maximale sortiernummer holen
		$sql = 'SELECT MAX(listorder) AS maxorder FROM lib_books';
		$result = db_query($sql);
		$row = db_fetch_assoc($result);
		$sql = "INSERT INTO lib_books (themeid, acctid, author, title, book, listorder)
			VALUES ('{$_POST['themeid']}', '{$session['user']['acctid']}', '{$session['user']['name']}', '{$_POST['title']}', '{$_POST['book']}', '{$row['maxorder']}')";
		db_query($sql);
	}
	else {
		if ($_GET['subop']=='save') {
			output('`c`$Wie soll ein Buch gedruckt werden, wenn nicht Titel und Inhalt existieren?`0`c`n`n');
			$_POST['title'] = str_replace('`','``',$_POST['title']);
			$_POST['book'] = str_replace('`','``',$_POST['book']);
		}
		else $_POST['title'] = $_POST['book'] = $_POST['themeid'] = '';
		output("`tHier hast du die Möglichkeit, eigenes Wissen niederzuschreiben und anderen damit zur Verfügung zu stellen.`n`n
		Nun liegt es an dir, die Zeilen auf das Pergament zu bringen, die du dein Wissen nennst.`0");
		output("<form action=\"library.php?op=offer&subop=save\" method='POST'>",true);
		output("<table cellpadding=2 cellspacing=1 bgcolor='#999999'><tr class='trdark'><td>Thema:</td><td><select name='themeid'>",true);
		$sql2 = "SELECT * FROM lib_themes ORDER BY listorder ASC";
		$result2 = db_query($sql2) or die(db_error(LINK));
		while ($row2 = db_fetch_assoc($result2)) {
			output("<option value='".$row2['themeid']."' ".($row2['themeid']==$_POST['themeid']?" selected='selected'":"").">".preg_replace('/`./','',$row2['theme'])."</option>",true);
		}
		output("</select></td></tr>",true);
		output("<tr class='trlight'><td>Titel:</td><td><input class='input' type='text' name='title' value='{$_POST['title']}' maxlength='50' size='50'></td></tr>",true);
		output("<tr class='trdark'><td colspan='2'>Mein Wissen über dieses Thema:</td></tr>",true);
		output("<tr class='trdark'><td colspan='2'><textarea name='book' class='input' cols='60' rows='10'>{$_POST['book']}</textarea></td></tr>",true);
		output("<tr class='trlight'><td colspan='2'><input type='submit' class='button' value='Einreichen'></td></tr></table></form>",true);
		addnav("","library.php?op=offer&subop=save");
	}
	break;

case 'rules':
	
	output('`tAm Schalter der Bibliothekare hängt ein Pergament. Bei näherer Betrachtung kannst du die (dir aufgrund der manchmal etwas seltsamen Sprache leicht unverständlichen) Regeln der Götter lesen, welche für das Einreichen neuer Bücher gelten:`n`n`q');
	
	lib_show_rules();
	
	addnav('Zurück zur Halle','library.php');
	
	break;

default:
	output('`tAm Eingang zur Bibliothek hängt ein Plakat. Du liest:`n`n');
	output('`qDie Bibliothek ist ein Ort des Wissens.`n
           Dieses Wissen kann aber nur gehalten werden, wenn jemand es niedergeschrieben hat.`n
           Dazu steht in dieser Bibliothek die Möglichkeit bereit, Texte zu verfassen und diese einzureichen.`n
           Nach Genehmigung durch Regenten oder Bevollmächtigte wird das Buch gedruckt und in die Regale der Bücherei gestellt.`n
           Von nun an hat jeder die Möglichkeit, einen Blick in dieses Buch zu werfen und sowohl interessante als auch nützliche Informationen zu bekommen.`n
           Sollte das geschriebene Buch gedruckt werden, erhält der Autor ein Dankeschön in Form von `bbis zu`b '.getsetting("libdp","5").' Punkten in J.C. Petersens Jägerhütte, je nach Qualität.`n`n');
	
	addnav("Stöbern","library.php?op=browse");
	addnav("`^Empfohlene Lektüre`0","library.php?op=recommended");
		   
	if($session['user']['alive']) {		   
		output("`tDu betrittst den großen Raum mit den unzähligen Regalen.`n
		Hier sind gedämpfte Unterhaltungen zu hören und in den vielen bequemen
		Drachenleder-Sesseln sitzen eifrige Kämpfer, um zu lesen.`n
		An ein paar Tischen kannst du hin und wieder auch sehr erfahrene Drachentöter finden,
		die ihr Wissen in Büchern niederschreiben.");
		output("`n`nEin paar Leute unterhalten sich leise:`n`0");
		
		viewcommentary("library","Leise flüstern:",25);
		addnav('Eigene Bücher');
		addnav("Buch einreichen","library.php?op=offer");
		addnav("Regeln","library.php?op=rules");
	}
	else {
		output('`tDein Geist schwebt zwischen den Regalen der Bibliothek. Nur undeutlich und schemenhaft kannst du die
				Umrisse der Bücherborde ausmachen. Dennoch sollte es kein Problem für dich darstellen, das Wissen aufzunehmen.');
	}
}

addnav('Zurück');
if($session['user']['alive']) {
	addnav('Zum Dorfplatz','village.php');
	addnav('Zum Marktplatz','market.php');
}
else {
	addnav('Zu den Schatten','shades.php');
}

page_footer();
?>
