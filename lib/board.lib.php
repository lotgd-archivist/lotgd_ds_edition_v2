<?php
/**
* board.lib.php: Funktionsbibliothek für Methoden, die zur Modifizierung / Anzeige von Schwarzen Brettern dienen
* @author talion <t@ssilo.de>
* @version DS-E V/2
*/

/**
*@desc Zeigt ein Nachrichtenbrett an
*@param string Interne Kategorie, deren Einträge angezeigt werden sollen
*@param int Löschen von Einträgen erlauben für: 0 = Keinen, 1 = Autor, 2 = Alle (Optional, Standard 0)
*@param string Überschrift (Optional)
*@param string Nachricht bei leerer Kategorie (Optional)
*@param bool YeOlde-Maillink anzeigen
*@param bool Datum anzeigen
*@param bool Aktualisieren-Button anbieten
*@author talion
*/
function board_view ($section,$del=0,
					$header='Am schwarzen Brett sind folgende Nachrichten zu lesen:',
					$nomsg='Es sind keine Nachrichten vorhanden!',
					$showmail=true,$showdate=false,$showrefresh=false) {
	global $session;
	$link = calcreturnpath();
	if($_GET['board_action'] == 'del') 
	{		
		$sql = 'DELETE FROM boards WHERE id='.(int)$_GET['msgid'];
		db_query($sql);
		redirect( preg_replace('/(\?|&)(board_action|msgid)=(\w)*/','',$link) );	
	}
	else 
	{
		$sql = 'DELETE FROM boards WHERE expire < "'.date('Y-m-d H:i:s').'" AND section="'.$section.'"';
		db_query($sql);
		
		$sql = 'SELECT a.name,a.login,b.* FROM boards b LEFT JOIN accounts a ON a.acctid=b.author WHERE b.section="'.$section.'" ORDER BY b.postdate DESC, b.expire DESC';
		$res = db_query($sql);
		$out = (db_num_rows($res)) ? $header : $nomsg;
		
		while($msg = db_fetch_assoc($res)) 
		{
			$link_del = $link.(( strpos($link,'&') || strpos($link,'?') ) ? '&' : '?').'board_action=del&msgid='.$msg['id'];
			$date = ($showdate) ? '`&(`i '.date('d.m.y',strtotime($msg['postdate'])).' `i)`n' : '';
			$out .= '`n`n';
			$out .= ($showmail) ? '<a href="mail.php?op=write&to='.rawurlencode($msg['login']).'" target="_blank" onClick="'.popup("mail.php?op=write&to=".rawurlencode($msg['login'])."").';return false;"><img src="images/newscroll.GIF" width="16" height="16" alt="Mail schreiben" border="0"></a>' : '';
			$out .= '`&'.$msg['name'].'`&:`n';
			$out .= $date;
			$out .= '`^'.strip_tags(closetags($msg['message'],'`b`c`i'));
			if( ($del == 1 && $session['user']['acctid'] == $msg['author']) || $del >= 2) {
				$out .= '`n<a href="'.$link_del.'">[ Entfernen ]</a>';
				addnav('',$link_del);
			}
		}
		if($showrefresh) 
		{
			$out .= '`n`n<a href="'.$link.'">Aktualisieren</a>';addnav('',$link);
		}
		output($out,true);
	}
}

/**
*@desc Zeigt Formular zum Einstellen eines Boardeintrags an
*@param string Text des Buttons
*@param string Handlungsanweisung vor Eingabefeld
*@author talion
*/
function board_view_form ($buttontext='Aufgeben',$msg='Hier kannst du deine Nachricht eingeben:')
{
	$link = calcreturnpath();
	$link .= ( strpos($link,'&') || strpos($link,'?') ) ? '&' : '?';
	
	$output=$addmsg;
	$output.='<form action="'.$link.'board_action=add" method="POST">';
	$output.=$msg.' <input type="text" name="msg" size="60" maxlength="254">';
	$output.='<input type="submit" name="ok" value="'.$buttontext.'">`n';
	$output.='</form>';
	
	output($output,true);
	addnav('',$link.'board_action=add');
	return (1);
}

/**
*@desc 	Fügt Eintrag zu Nachrichtenbrettern hinzu, nimmt dafür normalerweise Inhalt in POST
*		Zum Hinzufügen: Auf GET['board_action'] prüfen, muss 'add' sein, wenn Form abgeschickt wurde
*@param string Interne Kategorie, unter der Eintrag gespeichert wird
*@param int Max. RL-Zeit in Tagen bis Eintrag gelöscht wird (Optional, Standard 100)
*@param int Max. Anzahl an Posts vom gleichen Autor (Optional, Standard 0)
*@param string Nachricht (Optional, sonst POST-Inhalt)
*@author talion
*/
function board_add ($section,$days=100,$max_posts=0,$msg='') {
	global $session;
	
	if($max_posts > 0) 
	{
		$sql = 'SELECT id FROM boards WHERE author='.$session['user']['acctid'].' AND section="'.$section.'"';
		$res = db_query($sql);
		if(db_num_rows($res) >= $max_posts) 
		{
			return(-1);
		}
	}
	if(strlen($msg) == 0) {
		$msg = $_POST['msg'];
	}
	$sql = 'INSERT INTO boards SET section="'.$section.'",author='.$session['user']['acctid'].',message="'.$msg.'",postdate=NOW(),expire="'.date("Y-m-d H:i:s",strtotime(date("r")."+".$days." days")).'"';
	db_query($sql);
}
?>