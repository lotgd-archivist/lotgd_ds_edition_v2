<?
/****************************************/
/* river for bathing
/* Version 0.1
/* Type: Forest Event
/* Web: http://www.pqcomp.com/logd
/* E-mail: logd@pqcomp.com
/* Author: Lonny Luberts
/*
/* Modification by SkyPhy, July 04
/* Translation by SkyPhy, July 04
/**************************************/
require_once "common.php";
checkday();
$session['user']['specialinc']="riverbath.php";
if ($HTTP_GET_VARS[op] == ""){
output("Du kommst bei einem kleinen Bach vorbei. Das Wasser ist einladend sauber. Was für ein schöner Platz zum baden!");
addnav("Baden gehen","forest.php?op=bathe");
addnav("Weitergehen","forest.php?op=continue");
}
if ($HTTP_GET_VARS[op] == "continue"){
	$session['user']['specialinc']="";
	redirect("forest.php");
}
if ($HTTP_GET_VARS[op] == "bathe"){
	$session['user']['clean']=0;
	output("Schnell ziehst du dich aus und springst in den Bach. Als du wieder heraussteigst fühlst du dich viel frischer und sauberer!");
	if (e_rand(0,4)==2) {
		output("Als du dich wieder anziehst findest du unter Deinen Kleidern `6einen Edelstein`2");
		$session[user][gems]++;
	}
	$session['user']['specialinc']="";
	addnav("Weiter","forest.php");
}
?>