<?php

// 15082004

require_once "common.php";
if ($session[user][loggedin]) {
	checkday();
	if ($session[user][alive]) {
        addnav("Zurück");
		addnav("Zum Dorfplatz","village.php");
		addnav("Zum Marktplatz","market.php");
	} else {
		addnav("Zurück zu den Schatten", "shades.php");
	}

}else{
	addnav("Login Seite","index.php");
	
}
addnav('Anzeigen');
addnav('Gerade Online','list.php');
addnav('Alle Spieler','list.php?p=all');
addnav('Adminteam','list.php?p=su');
page_header("Kämpferliste");

// Order the list by level, dragonkills, name so that the ordering is total!
// Without this, some users would show up on multiple pages and some users
// wouldn't show up
if ($_GET['p']=="" && $_GET['op'] == ''){
	output('`c`bFolgende Bürger '.getsetting('townname','Atrahor').'s sind gerade online:`b`c');
	user_show_list(200,user_get_online(),'level DESC, dragonkills DESC, login ASC',true,200);
}
else if ($_GET['p'] == 'su') {
	$arr_usergroups = unserialize( stripslashes(getsetting('sugroups','')) );
	
	$str_not_show = '0';
	foreach($arr_usergroups as $id=>$val) {
		// Gesondert zeigen?
		if(!$val[3]) {
			$str_not_show .= ','.$id;
		}
	}
	
	output('`c`bDas Administrationsteam '.getsetting('townname','Atrahor').'s:`b`c');
	user_show_list(200,' superuser NOT IN('.$str_not_show.') ',' RAND() ',true);
}
else {
	output('`c`bDie ehrenhaften Bürger '.getsetting('townname','Atrahor').'s:`b`c');
	user_show_list(40,'','level DESC, dragonkills DESC, login ASC',true);
}

page_footer();
?>
