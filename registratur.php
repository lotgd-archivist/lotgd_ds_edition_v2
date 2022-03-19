<?
require_once "common.php";
page_header("Die Registratur");

su_check(SU_RIGHT_REGISTRATUR,true);

// Grundnavi erstellen
addnav('Zurück');
addnav('G?Zur Grotte','superuser.php');
addnav('W?Zum Weltlichen',$session['su_return']);
addnav('Registratur');
addnav("ungeprüfte Namen","registratur.php?op=newname");
addnav("angemailte Namen","registratur.php?op=mailname");
addnav("akzeptierte Namen","registratur.php?op=accname");
addnav('Aktionen');

//namecheck status-info
//0 = ungeprüft
//2 = angemailt
//4 = erinnert
//9 = akzeptiert
//Steuerung
$trenn = 5;

$arr_form = array(		'rec'=>'An:,viewonly',
						'subject'=>'Betreff:',
						'body'=>'Text:,textarea,60,30'
					);

$str_changesubj_a = '`^Namensänderung`0';
$str_changemail_a = get_extended_text('changemail_a_body','*',false,false);
	
$str_changesubj_b = '`$Namensänderung 2. Verwarnung`0';
$str_changemail_b = get_extended_text('changemail_b_body','*',false,false);

$str_welcomesubj = '`^Herzlich Willkommen!`0';	
$str_welcomemail = get_extended_text('welcomemail_body','*',false,false);

if ($_GET['op']=="newname" || $_GET['op'] == '') {    //Liste ungeprüfte Bewohner

    output("Du schlägst das grosse Buch der neuen Bewohner auf und blätterst
    aufmerksam durch die Seiten.`n
    Die folgenden Charakter-Namen sind noch unbearbeitet:`n`n`0");

    $sql = 'SELECT accounts.acctid, name, login, laston, namecheck, loggedin
			FROM accounts
			LEFT JOIN account_extra_info USING(acctid)
            WHERE locked=0 and (namecheck=0)
            ORDER BY acctid DESC';
    $result = db_query($sql) or die(db_error(LINK));
    if (db_num_rows($result) == 0) {
        output("Es sind keine Bewohner mit ungeprüften Namen verzeichnet!`0`n");
    }
    else {
        output("<table border=0 cellpadding=2 cellspacing=1 >",true);
        output("<tr class='input'><td>Nummer</td><td>Spieler</td><td>Last On</td>
        <td>Namenswechsel</td><td>Akzeptieren</td><td>Löschung</td>",true);
        $count = 0;
        while ($row = db_fetch_assoc($result)) {
            if ( $count == 5 ) {
                output("<tr class='trmain'>",true);
                output("<td>----</td><td>----------</td><td>---------</td>",true);
                output("<td>----</td><td>--</td><td>-------</td>",true);
                output("</tr>",true);
                $count = 1;
            } else $count++;
            $tmp = $row['acctid'];
            $tmp2 = $row['name'];
            $tmp3 = $row['login'];
            output("<tr class='trmain'>",true);
            output("<td>".$tmp."</td><td>".$tmp2."</td>",true);
			output("<td>",true);
			$laston=round((strtotime("0 days")-strtotime($row[laston])) / 86400,0)." Tage";
			if (substr($laston,0,2)=="1 ") $laston="1 Tag";
			if (date("Y-m-d",strtotime($row[laston])) == date("Y-m-d")) $laston="Heute";
			if (date("Y-m-d",strtotime($row[laston])) == date("Y-m-d",strtotime("-1 day"))) $laston="Gestern";
			if ($row['loggedin']) $laston="Jetzt";
			output($laston);
			output("</td>",true);
            output("<td><a href='registratur.php?op=mail&userid=".$tmp."'>`^Mail`0</a></td>",true);
            output("<td><a href='registratur.php?op=accept&userid=".$tmp."'>`@ok`0</a></td>",true);
			if(su_check(SU_RIGHT_EDITORUSER)) {
				$str_lnk = 'su_delete.php?ids[]='.$tmp.'&ret='.urlencode(calcreturnpath());
				output('<td>'.create_lnk('`4löschen`0',$str_lnk).'</td>',true);
			}
            output("</tr>",true);
            addnav("","registratur.php?op=mail&userid=$tmp");
            addnav("","registratur.php?op=accept&userid=$tmp");
            
        }
        output("</table>",true);
    }
}
else if ($_GET['op']=="mail") {   //mehlen
	
	$int_id = (int)$_GET['userid'];
	
	$sql =  "SELECT age,name,login,acctid,sex FROM accounts WHERE acctid='$int_id'";
	$result2 = db_query($sql) or die(db_error(LINK));
	$arr_user = db_fetch_assoc($result2);
	
	$str_changemail_a = str_replace('{name}',$arr_user['login'],$str_changemail_a);
	
	if($_GET['act'] == 'send') {

		$to = $int_id;
		$subject = $_POST['subject'];     //betreff
		$from = $session['user']['acctid'];  //absender: eingeloggter (halb)gott
		$body = $_POST['body'];
		
		if($_POST['blacklist']) {
			$str_login = trim(addslashes(strtolower($arr_user['login'])));
		
			// Duplikate vermeiden
			$sql = 'DELETE FROM blacklist WHERE value="'.$str_login.'" AND type=(0 ^ '.BLACKLIST_LOGIN.')';
			db_query($sql);
		
			$sql = 'INSERT INTO blacklist SET value="'.$str_login.'",type='.BLACKLIST_LOGIN;
			db_query($sql);
		}
		
		$sql = "INSERT INTO mail SET msgfrom=".$from.",msgto=".$to.",subject='".$subject."',body='".$body."',sent=NOW()";
		db_query($sql);
			
		$tag = $arr_user['age'];
		
		$sql = "UPDATE account_extra_info SET namecheck=".$session['user']['acctid'].", namecheckday=$tag WHERE acctid=$int_id";
		db_query($sql);
		
		$namechange=getsetting("unaccepted_namechange","0");
		
		if ($namechange==1)
		{
          $number=getsetting("namechange_number","1");
          $newname="Neuling mit unzulässigem Namen Nr. {$number}";
          $sql = 'UPDATE accounts SET name="'.$newname.'" WHERE acctid='.$int_id;
		  db_query($sql);
          $number++;
          savesetting("namechange_number",$number);

        }
		
		redirect("registratur.php?op=newname");
	}
	else {
		
		$arr_form['blacklist'] = $arr_user['login'].' auf Blacklist setzen,checkbox,1';
				
		$arr_data = array(
							'rec'=>$arr_user['login'].'`0',
							'subject'=>$str_changesubj_a,
							'body'=>$str_changemail_a
						);
							
		$str_lnk = 'registratur.php?op=mail&act=send&userid='.$int_id;
		addnav('',$str_lnk);
		
		output('<form action="'.$str_lnk.'" method="POST">',true);
		
		showform($arr_form,$arr_data,false,'Absenden!');
		
		output('</form>',true);			
		
	}
	            
}
else if ($_GET[op]=="imprison") {   //einkerkern
    //$wer = $_GET['name'];
    $to = $_GET['userid'];
    $subject = $str_changesubj_b;     //betreff
    $from = $session['user']['acctid'];  //absender: eingeloggter (halb)gott
    $body = $str_changemail_b;
    $sql = "INSERT INTO mail SET msgfrom=".$from.",msgto=".$to.",subject='".$subject."',body='".$body."',sent=NOW()";//systemmail($to,$subject,$body);
	db_query($sql);

	$id = $_GET[userid];
    $sql = "UPDATE accounts SET imprisoned=-2 WHERE acctid=$id";
    db_query($sql);
	
	systemlog('`qEinkerkerung von:`0 ',$session['user']['acctid'],$id);
	
    redirect("registratur.php?op=mailname");
}
else if ($_GET[op]=="accept") {  //akzeptieren
	
	$int_id = (int)$_GET['userid'];
	
	$sql =  "SELECT age,name,login,acctid,sex FROM accounts WHERE acctid='$int_id'";
	$result2 = db_query($sql) or die(db_error(LINK));
	$arr_user = db_fetch_assoc($result2);
	
	$str_welcomemail = str_replace('{name}',$arr_user['login'],$str_welcomemail);
	
	if($_GET['act'] == 'send' || $_GET['nonew']) {
		
		// Mail nur versenden, wenn Account noch nicht angemailt wurde
		if(!$_GET['nonew']) {
			$to = $int_id;
			$subject = $_POST['subject'];     //betreff
			$from = $session['user']['acctid'];  //absender: eingeloggter (halb)gott
			$body = $_POST['body'];
			
			$sql = "INSERT INTO mail SET msgfrom=".$from.",msgto=".$to.",subject='".$subject."',body='".$body."',sent=NOW()";
			db_query($sql);
		}
			
		$sql = "UPDATE account_extra_info SET namecheck=16777215 WHERE acctid=".$int_id;
		db_query($sql);
		
		$sql = "UPDATE accounts SET imprisoned=0 WHERE acctid=$int_id AND imprisoned<0";
		db_query($sql);
		
		redirect("registratur.php?op=newname");
	}
	else {
				
		$arr_data = array('rec'=>$arr_user['name'].'`0','subject'=>$str_welcomesubj,'body'=>$str_welcomemail);
							
		$str_lnk = 'registratur.php?op=accept&act=send&userid='.$int_id;
		addnav('',$str_lnk);
		
		output('<form action="'.$str_lnk.'" method="POST">',true);
		
		showform($arr_form,$arr_data,false,'Absenden!');
		
		output('</form>',true);			
		
		addnav('KEINE Willkommensmail versenden','registratur.php?op=accept&nonew=1&userid='.$int_id);
		
	}
	    	   
}
else if ($_GET[op]=="rename") {   // umbenennen
	
	$acctid = (int)$_GET['acctid'];
		
	if(!$acctid) {redirect('registratur.php?op=mailname');} 
	
	$sql = 'SELECT name,title,login,ctitle FROM accounts LEFT JOIN account_extra_info USING(acctid) WHERE accounts.acctid='.$acctid;
	$acc = db_fetch_assoc(db_query($sql));
	
	output('`&'.$acc['name'].' umbenennen:`n`n');
	
	$name = trim($_POST['name']);
	
	if(strlen($name) >= 3) {
		$sql = 'SELECT acctid FROM accounts WHERE login="'.$name.'"';
		$acc_check = db_fetch_assoc(db_query($sql));
						
		if($acc_check['acctid']) {output('`n`n`$Es existiert bereits ein Account mit diesem Namen!`&');$name='';}
		else {
			$newname = ($acc['ctitle'] ? $acc['ctitle'] : $acc['title']).' '.$name;
			$sql = 'UPDATE accounts SET login="'.$name.'",name="'.$newname.'" WHERE acctid='.$acctid;
			db_query($sql);
			
			if(db_affected_rows()) {redirect('registratur.php?op=accept&userid='.$acctid.'&nonew=1');}
		}
	}
    	
	$link = 'registratur.php?op=rename&acctid='.$acctid;
	
	output('<form method="POST" action="'.$link.'">
				<input type="text" name="name" maxlength="40" value="'.($name ? $name : $acc['login']).'">
				<input type="submit" value="Ändern!">
			</form>',true);
	
	addnav('',$link);    
    
}
else if ($_GET[op]=="accname") {  //liste akzeptierte Bewohner
   
    output("Du schmökerst im grossen Buch der Bewohner, wer alles hier
    gemeldet ist:`n`n`0");

    $sql = 'SELECT accounts.acctid, name, login, title FROM accounts
			LEFT JOIN account_extra_info USING(acctid) 
            WHERE locked=0 and namecheck=16777215
            ORDER BY login ASC';
    $result = db_query($sql) or die(db_error(LINK));
    if (db_num_rows($result) == 0) {
        output("Es sind keine Bewohner mit akzeptierten Namen verzeichnet!`0`n");
    }
    else {
        $comp = "";
        $space = " ";
        output("<table border=0 cellpadding=2 cellspacing=1 >",true);
        output("<tr class='input'><td>Nummer</td><td>Name</td><td>Titel</td>",true);
        while ($row = db_fetch_assoc($result)) {
            $tmp = $row['acctid'];
            $letter = ucfirst( substr($row['login'],0,1));
            if ( $letter != $comp ) {
                output("<tr class='trmain'>",true);
                output("<td>".$space."</td><td>`6-----`^  `b".$letter."`b  `6------`0 </td><td>".$space."</td>",true);
                output("</tr>",true);

                $comp = $letter;
            }
            output("<tr class='trmain'>",true);
            output("<td>".$tmp."</td><td>".$row['login']."</td><td>".$row['title']."</td>",true);
            output("</tr>",true);
        }
        output("</table>",true);
    }
}
else if ($_GET[op]=="mailname") {   //liste angemailte Bewohner

    output("Du schmökerst in einer Pergament-Rolle, in der alle Bewohner verzeichnet
    sind, die wegen ihrer Namenswahl angeschrieben worden sind:`n`n`n`n`n`n`0");

    $sql = 'SELECT a.age,a.imprisoned, a.acctid, a.name, a.laston, a.loggedin, su.login AS superusername,aei.namecheck, aei.namecheckday
			 FROM accounts a
			LEFT JOIN account_extra_info aei ON a.acctid=aei.acctid 
			LEFT JOIN accounts su ON aei.namecheck = su.acctid
			WHERE a.locked=0 AND (aei.namecheck>0 AND aei.namecheck < 16777215)
            ORDER BY a.acctid ASC';
    $result = db_query($sql) or die(db_error(LINK));
    if (db_num_rows($result) == 0) {
        output("Es sind keine angeschriebenen Bewohner verzeichnet!`0`n");
    }
    else {
        output("<table border=0 cellpadding=2 cellspacing=1 >",true);
        output("<tr class='input'><td>Nummer</td><td>Spieler</td><td>Last On&nbsp;</td><td>Spieltage seit Anschreiben</td><td>Superuser</td><td>Namenswechsel</td><td>Akzeptieren</td><td>Einkerkern</td><td>Löschung</td>",true);
        while ($row = db_fetch_assoc($result)) {
			if ($row[namecheckday] != 0){
				$tagheute = $row[age];
				$tagnum = $tagheute - $row[namecheckday];
				$tag = "".$tagnum." Tage";
			}
			else{
				$tag = "Keiner";
			}
            $tmp = $row['acctid'];
            output("<tr class='trmain'>",true);
            output("<td>".$tmp."</td><td>".$row['name']."</td>",true);
    	    output("<td>",true);
            $laston=round((strtotime("0 days")-strtotime($row[laston])) / 86400,0)." Tage";
            if (substr($laston,0,2)=="1 ") $laston="1 Tag";
            if (date("Y-m-d",strtotime($row[laston])) == date("Y-m-d")) $laston="Heute";
            if (date("Y-m-d",strtotime($row[laston])) == date("Y-m-d",strtotime("-1 day"))) $laston="Gestern";
            if ($row['loggedin']) $laston="Jetzt";
            output($laston);
	        output("</td>",true);
        	output("<td>".$tag."</td>",true);
			output("<td>".$row['superusername']."</td>",true);
            output("<td><a href='registratur.php?op=rename&acctid=".$tmp."'>`Qumbenennen`0</a></td>",true);
            output("<td><a href='registratur.php?op=accept&userid=".$tmp."&nonew=1'>`@ok`0</a></td>",true);
			if ($row['imprisoned']<0) {output("<td>`isitzt schon`i</td>",true);}
			else if ($row['namecheck'] == $session['user']['acctid']) { output("<td><a href='registratur.php?op=imprison&userid=".$tmp."'>`@In den Kerker`0</a></td>",true);}
			else {	output('<td>`@ - </td>',true); }

            if(su_check(SU_RIGHT_EDITORUSER)) {
				$str_lnk = 'su_delete.php?ids[]='.$tmp.'&ret='.urlencode(calcreturnpath());
				output('<td>'.create_lnk('`4löschen`0',$str_lnk).'</td>',true);
			}
            output("</tr>",true);
               //umbenennung ueber verwaltungsbuero
            addnav("","registratur.php?op=rename&acctid=".$tmp);
            addnav("","registratur.php?op=imprison&userid=$tmp");
            addnav("","registratur.php?op=accept&userid=$tmp&nonew=1");
        }
        output("</table>",true);
    }
}

page_footer();
?>
