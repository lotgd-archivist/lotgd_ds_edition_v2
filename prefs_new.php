<?php
/**
* prefs_new.php: Profil + Einstellungen. Umgestellt auf Popup-Modus
* @author 	partly LOGD-Core, modded and rewritten by talion <t@ssilo.de>
* @version DS-E V/2
*/

// Wenn neues Template gesetzt werden soll
if (isset($_POST['template'])){
	$overwrite_template = $_POST['template'];	
}

require_once('common.php');

if(!$session['user']['loggedin']) { exit; }

popup_header('Einstellungen & Profil');

$biolink='prefs_new.php?op=bio';
$preflink='prefs_new.php';

output('`&`c`bEinstellungen & Profil');
output('<script language="javascript">window.resizeTo(640,400);</script>',true);

$rowex = user_get_aei('bio,biotime,has_long_bio,charclass,avatar,long_bio,shortcuts,html_locked');

// Char löschen
if ($_GET['op']=="suicide" && getsetting("selfdelete",0)!=0) {
	user_delete($session['user']['acctid']);
	output("Dein Charakter, sein Inventar und alle seine Kommentare wurden gelöscht!");
	addnews("`#{$session['user']['name']} beging Selbstmord.");

	$session=array();
	$session['user'] = array();
	$session['loggedin'] = false;
	$session['user']['loggedin'] = false;
}
else if ($_GET['op']=="bio") {
	
	output(' ( <a href="'.$preflink.'">Zu den Einstellungen</a> )`b`c`0`n',true);	
					
	if ($rowex['avatar']) {
		$pic_size = @getimagesize($rowex['avatar']);
		$pic_width = $pic_size[0];
		$pic_height = $pic_size[1];
		output('`n<img src="'.$rowex['avatar'].'" ',true);
		if ($pic_width > 200) output('width="200" ',true );
		if ($pic_height > 200) output('height="200" ',true );
		output('alt="'.preg_replace("'[`].'",'',$session['user']['name']).'">&nbsp;',true);
	} 
	else {
		output('`n`n(kein Bild)&nbsp;&nbsp;&nbsp;',true);
	}
	
	if(strlen($rowex['charclass']) > 0) {output('`n`^Klasse: `@'.closetags($rowex['charclass'],'`b`c`i').'`n');}
	
	if (($session['user']['marks']<CHOSEN_FULL) && ($rowex['has_long_bio']!=1)) {
		if ($rowex['bio']>"") {
			$bio = $rowex['bio'];		
		} 
	}
	else {
		$max_l = getsetting('longbiomaxlength',4096);
		
		if ($rowex['long_bio']>"") {
			$bio = substr($rowex['long_bio'],0,$max_l);		
		}
	
	}
	
	$bio = soap(closetags($bio,'`b`c`i'));
	
	$allow_tags = ($rowex['html_locked'] ? '' : '<img>');
	
	$bio = strip_tags($bio,$allow_tags);
	
	output('`n`^Bio: `@`n'.$bio.'`n'); 
	
	popup_footer();
	exit;
	
}
else {	// Einstellungen speichern
			
	if (count($_POST)==0){	
		
	}
	else {	// wenn Einstellungen abgeschickt
		
		$array_aei_changes = array();
		
		if ($_POST['pass1']!=$_POST['pass2']){
			output("`#Deine Passwörter stimmen nicht überein.`n");
		}
		else{
			if ($_POST['pass1']!=""){
				if (strlen($_POST['pass1'])>3){
					$session['user']['password']=md5($_POST['pass1']);
					output("`#Dein Passwort wurde geändert.`n");
				}
				else{
					output("`#Dein Passwort ist zu kurz. Es muss mindestens 4 Zeichen lang sein.`n");
				}
			}
		}
		
		$_POST['commenttalkcolor'] = substr($_POST['commenttalkcolor'],0,1);
		$_POST['commenttalkcolor'] = preg_replace('/[^'.regex_appoencode().']/','',$_POST['commenttalkcolor']);
		
		$_POST['commentemotecolor'] = substr($_POST['commentemotecolor'],0,1);
		$_POST['commentemotecolor'] = preg_replace('/[^'.regex_appoencode().']/','',$_POST['commentemotecolor']);
		
		if(isset($_POST['long_bio']) && $rowex['biotime'] != BIO_LOCKED) {
			$long = preg_replace('/\r\n|\r|\n/', '', $_POST['long_bio']); // Zeilenumbrüche raus
			$max_l = getsetting('longbiomaxlength',4096);
			$long = substr($long,0,$max_l);
			$rowex['long_bio'] = stripslashes($long);
			$array_aei_changes['long_bio'] = $long;	
			$array_aei_changes['biotime'] = date("Y-m-d H:i:s");
		}
		if(isset($_POST['bio']) && $rowex['biotime'] != BIO_LOCKED) {		
			$str_bio = closetags($_POST['bio'],'`i`b`c`H');
	
			if ($str_bio != $rowex['bio']){
				$array_aei_changes['bio'] = $str_bio;
				$array_aei_changes['biotime'] = date("Y-m-d H:i:s");
			}
		}
				
		reset($_POST);
		$nonsettings = array("pass1"=>1,"pass2"=>1,"emailaddress"=>1,"bio"=>1,"avatar"=>1,"long_bio"=>1);
		
		while (list($key,$val)=each($_POST)){
			if (!$nonsettings[$key]) $session['user']['prefs'][$key]=$_POST[$key];
		}
						
		$array_aei_changes['charclass'] = closetags($_POST['charclass'],'`i`b`c`H');
		
		if (getsetting("avatare",0)==1) {
			if (stripslashes($_POST['avatar'])!=$rowex['avatar']){
			
				$rowex['avatar']=stripslashes(preg_replace("'[\"\'\\><@?*&#; ]'","",$_POST['avatar']));
				
				$url=$rowex['avatar'];
				
				if ($url>"" && strpos($url,".gif")<1 && strpos($url,".GIF")<1 && strpos($url,".jpg")<1 && strpos($url,".JPG")<1 && strpos($url,".png")<1 && strpos($url,".PNG")<1){
					$rowex['avatar']="";
					$msg.="`\$Ungültiger Avatar! Nur .jpg, .png, oder .gif`0`n";
				}
				else {
					$array_aei_changes['avatar'] = addslashes($rowex['avatar']);
				}
			}
		}
		
		if (isset($_POST['emailaddress']) && $_POST['emailaddress']!=$session['user']['emailaddress']){
			if (is_email($_POST['emailaddress'])){
				if (getsetting("requirevalidemail",0)==1){
					output("`#Die E-Mail Adresse kann nicht geändert werden, die Systemeinstellungen verbieten es. (E-Mail Adressen können nur geändert werden, wenn der Server mehr als einen Account pro Adresse zulässt.) Sende eine Petition, wenn du deine Adresse ändern willst, weil sie nicht mehr länger gültig ist.`n");
				}
				else{
					output("`#Deine E-Mail Adresse wurde geändert.`n");
					$session['user']['emailaddress']=$_POST['emailaddress'];
				}
			}
			else {
				output("`#Das ist keine gültige E-Mail Adresse.`n");
			}
		}
		
		if( sizeof($array_aei_changes) > 0 ) {
			user_set_aei($array_aei_changes);
		}
		
		output( $msg );
		output("`n`@`bEinstellungen gespeichert!`b`0`n");
		
	}	// END Einstellungen abgeschickt
}	// END Einstellungen abspeichern		

output(' ( <a href="'.$biolink.'">Zur Bio</a> )`b`c`0`n',true);

$str_skin_radio = 'radio';

if ($handle = @opendir("templates")){
	$skins = array();
	while (false !== ($file = @readdir($handle))){
		if (strpos($file,".htm")>0){
			array_push($skins,$file);
		}
	}
	if (count($skins)==0){
		output("`b`@Argh, dein Admin hat entschieden, daß du keine Skins benutzen darfst. Beschwer dich bei ihm, nicht bei mir.`n");
	}
	else{
		while (list($key,$val)=each($skins)){
		
			$str_name = substr($val,0,strpos($val,".htm"));
		
			$str_skin_radio .= ','.$val.','.$str_name;
			
			//output("<input type='radio' name='template' value='$val'".($_COOKIE['template']==""&&$val=="yarbrough.htm" || $_COOKIE['template']==$val?" checked":"").">".substr($val,0,strpos($val,".htm"))."<br>",true);
		}
	}
}

// Farbübersicht
$str_colors = '';
$colors = get_appoencode(true);		
foreach($colors as $c) {
	
	if(!empty($c['color']) && $c['allowed']) {
		$str_colors .= '`'.$c['code'].'&#0096;'.$c['code'].'`0 ';
	}	
	
}

output('`nDie Farbcodes:`n'.$str_colors);
// END Farbübersicht

// Datenarray erstellen
$prefs = $session['user']['prefs'];
$prefs['emailaddress'] = $session['user']['emailaddress'];
$prefs['bio'] = $rowex['bio'];
$prefs['charclass'] = $rowex['charclass'];
$prefs['template'] = ($_COOKIE['template'] != '' ? $_COOKIE['template'] : getsetting('defaultskin','yarbrough.htm'));
$prefs['email'] = $session['user']['emailadress'];

if (getsetting("avatare",0)==1) {
	$prefs['avatar'] = $rowex['avatar'];
}
else {
	$prefs['avatar'] = "(kein Avatar erlaubt)";
}

// Formulararray erstellen
$form=array(
		"Allgemeine Einstellungen,title"
		,"template"=>'Skin,'.$str_skin_radio
		,"pass1"=>'Neues Passwort,password|?Lasse das Feld leer, wenn du es nicht ändern möchtest.'
		,"pass2"=>'Passwort wiederholen,password'
		,"emailonmail"=>"E-Mail senden wenn du eine Ye Olde Mail bekommst?,bool"
		,"systemmail"=>"E-Mail bei Systemmeldungen senden?,bool|?Z.b. Niederlage im PvP."
		,"dirtyemail"=>"Kein Wortfilter für Ye Olde Mail?,bool"
		,"preview"=>"Vorschau für Chatnachrichten anzeigen?,bool|?Wenn aktiv, wird unter jedem Chat eine Vorschau dessen angezeigt, was du gerade in das Feld eintippst."
		,"timestamps"=>"Uhrzeit vor Chatnachrichten anzeigen?,bool|?Wenn aktiv, wird vor jedem Chatbeitrag die Uhrzeit angezeigt, zu der er geschrieben wurde."
		,"nosounds"=>"Die Sounds deaktivieren?,bool"
		,"noimg"=>"Navigationsbilder deaktivieren?,bool"
);
if (getsetting("requirevalidemail",0)==0) {
	$form = array_merge($form,array("emailaddress"=>"E-Mail Adresse`n"));			
}
else {
	$form = array_merge($form,array("emailaddress"=>"E-Mail Adresse`n,viewonly|?Nutze die Funktion 'Anfrage schreiben', um die Administration über eine evtl. neue Emailadresse in Kenntnis zu setzen und sie ändern zu lassen."));
}

$form = array_merge($form, 
						array(
						"Charaktereinstellungen,title"
						,"avatar"=>"Link auf einen Avatar|?Bilddatei - maximal 200x200 Pixel. Das Bild muss auf einem Webserver liegen, von dem du es verlinken kannst (http:// nicht vergessen!). Bsp.: http://www.meinserver.de/avatar.jpg"
						,"charclass"=>"Charakterklasse|?Günstig für das Rollenspiel und die Ausgestaltung deines Charakters, wenn du z.b. die Rasse noch weiter spezialisieren möchtest. Besitzt allein kosmetischen Charakter ; )"
						,"commenttalkcolor"=>'`'.($prefs['commenttalkcolor'] != '' ? $prefs['commenttalkcolor'] : '#')."Farbe`0 für Gesagtes in Kommentaren (Ohne &#0096; !)`n"
						,"commentemotecolor"=>'`'.($prefs['commentemotecolor'] != '' ? $prefs['commentemotecolor'] : '&')."Farbe`0 für Aktionen in Kommentaren (Ohne &#0096; !)`n"
						)
					);

if( ($session['user']['marks']<31) && ($rowex['has_long_bio']!=1) && $rowex['biotime'] != BIO_LOCKED ) { 
	$form = array_merge($form,array("bio"=>"Kurzbeschreibung des Charakters (Maximal 255 Zeichen)`n"));
}

for ($i=0;$i<=$rowex['shortcuts'];$i++){
	$form = array_merge($form,array('sx'.$i => 'Shortcut %x'.$i.' => '.$prefs['sx'.$i]));
}

// verlängerte Bio
if (($session['user']['marks']>=31 || $rowex['has_long_bio']==1) && $rowex['biotime'] != BIO_LOCKED) {
	
	//$rowex['long_bio'] = htmlentities($rowex['long_bio']);
		
	$rowex['long_bio'] = preg_replace('/\r\n|\r|\n/', '', $rowex['long_bio']); // Zeilenumbrüche raus
	
	$rowex['long_bio'] = str_replace('`n','`n
',$rowex['long_bio']);		
				
	$prefs['long_bio'] = $rowex['long_bio'];
		
	$max_l = getsetting('longbiomaxlength',4096);
	
	$form[] = 'Verlängerte Bio,title';
	$form['long_bio'] = 'Biotext:,textarea,60,20,'.$max_l;
	
} 

// Formular anzeigen
$str_lnk = 'prefs_new.php?op=save';
output("`n<form action='".$str_lnk."' method='POST'>",true);
showform($form,$prefs);
output("</form>",true);
// END Formular anzeigen

// Nur Löschung zulassen, wenn User am Leben: Soll verhindern, dass frustrierte Spieler sich gedankenlos löschen
if ($session['user']['alive'] && getsetting("selfdelete",0)!=0) {
	output("`n`n`n<form action='prefs_new.php?op=suicide&userid={$session['user']['acctid']}' method='POST'>",true);
	output("<input type='submit' class='button' value='Charakter löschen' onClick='return confirm(\"Willst du deinen Charakter wirklich löschen?\");'>", true);
	output("</form>",true);
}

popup_footer();
?>