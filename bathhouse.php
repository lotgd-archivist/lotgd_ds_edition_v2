<?
/********************************************************
/* Bathhouse
/* with 2 forest specials (mud.php, riverbath.php)
/* Difficulty: Medium
/* Author Lonny Luberts of http://www.pqcomp.com/logd
/* version 1.0
/* July 2004
/* Modification by SkyPhy, July 2004
/* Transaltion by SkyPhy, July 2004
/******************************************************/

/* INSTALLATION
/****************
In common.php
before $u['hitpoints']=round($u['hitpoints'],0);
add fter templatereplace("statrow",array("title"=>"Hitpoints","value"=> (not showing all of it as there are different versions)
.templatereplace("statrow",array("title"=>"Odor","value"=>$u['clean']))

in newday.php after $session['user']['bounties']=0;
add
//begin cleanliness code
//code for bathroom mod (schmutzig...)
		if ($session ['user']['clean'] > 5){
			$session['user']['charm']--;
			output("Du bist etwas schmutzig und verlierst daher `6einen Charmpunkt");
		}
		$session['user']['clean']+=1;
		if ($session['user']['clean']>9 && $session['user']['clean']<15)
			addnews($session['user']['name']."`2 stinkt etwas!");
		if ($session['user']['clean']>14 and $session['user']['clean']<20){
			output("Du hältst deinen Gestank kaum noch aus!");
			addnews($session['user']['name']."`2 stinkt zum Himmel!");
		}
		if ($session['user']['clean']>19){
			output("`@Weil du so dreckig bist hast du dir den Titel `6Saubär`@ verdient!`n");
			$name=$session['user']['name'];
			addnews("$name `7hat sich den Titel Saubär verdient, weil er extrem schmutzig ist!");
			$newtitle="Saubär";
			$n = $session['user']['name'];
			$x = strpos($n,$session['user']['title']);
			if ($x!==false){
				$regname=substr($n,$x+strlen($session['user']['title']));
				$session['user']['name'] = substr($n,0,$x).$newtitle.$regname;
				$session['user']['title'] = $newtitle;
			}else{
				$regname = $session['user']['name'];
				$session['user']['name'] = $newtitle." ".$session['user']['name'];
				$session['user']['title'] = $newtitle;
			}
			//remove unamecolor if you are not using my colored names mod
			//unamecolor();
		} //end cleanliness code

add to your files where you would like
$session['user']['clean'] += 1; (or whatever value)
when you want to make someone dirty

add e.g. in inn.php in nav section
addnav("Badezimmer","bathhouse.php");
for a public bathhouse (costs 5 Gold)

add e.g. in houses.php in nac section
addnav("Badezimmer","bathhouse.php?op=batheroom");
for a private bathroom (costs 0 Gold, can win/loose Charmepoint)

Mysql inclusions
ALTER TABLE accounts ADD `clean` int(100) NOT NULL default '0'
**************************************************************************/
/* END OF INSTALLATION DESCRIPTION
/*************************************************/

require_once "common.php";
checkday();
page_header("Badezimmer");
//checkevent("bathhouse.php");
output("`c`b`&Badezimmer`0`b`c`n`n");
if ($HTTP_GET_VARS[op] == ""){
output("`2Du betrittst das Badezimmer und bemerkst, daß alles feucht ist, sogar die alte Frau, die hier auf alles aufpasst.`n");
output("Vorhänge versperren den Blick zu den einzelnen Badewannen. Du denkst, ein heißes Bad wäre jetzt nicht schlecht.`n");
output("Die alte Frau deutet auf ein Schild an der Wand. Dort liest du: `6Ein heißes Bad: 5 Gold`2`n");
addnav("Ein Bad nehmen (5 Gold)","bathhouse.php?op=bathe");
addnav("Zurück zur Kneipe","inn.php");
}
if ($HTTP_GET_VARS[op] == "bathe"){  //when called from inn --> bath costs 5 gold
	if ($session['user']['gold']<5){
		output("`2Verzweiflest suchst du nach 5 Gold, doch du hast zuwenig Gold bei dir.");
		output("Die alte Frau zeigt dir schweigend den Weg nach draussen.`n");
		addnav("Zurück zur Kneipe","inn.php");
	}else{
		output("`2Du zahlst der alten Frau 5 Gold und sie deutet schweigend auf eine freie Badewzelle. ");
		output("Du ziehst den Vorhang zu, entkleidest dich schnell und gleitest in die Badewanne, um den angesammelten Dreck aus dem Wald abzuwaschen. ");
		output("Du fphlst dich sauwohl und möchtest noch länger bleiben, doch gerade als es am bequemsten ist geht plötzlich");
		output(" der Vorhang auf. Die alte Frau meint streng, daß es Zeit wäre, zu gehen und zieht den Vorhang wieder zu.");
		output(" Schnell trocknest du dich ab und ziehst dich wieder an. Du fühlst dich nun viel besser`n");
		$session['user']['clean']=0;
		$session['user']['gold']-=5;
		addnav("Zurück zur Kneipe","inn.php");
	}
}

if ($HTTP_GET_VARS[op] == "batheroom"){  //when called from house --> bath is for free, extra charmpoint posssible
	output("`2Du betritts das Badezimmer um dich ein wenig frisch zu machen. ");
	output("Schnell ziehst du dich aus und gleitest in die Badewanne. Sorgfältig wäscht du den Dreck aus dem Wald ab.`n");
	output("Du fühlst dich sauwohl und könntest für immer hier bleiben, doch langsam wird das Wasser kälter und du");
	output("seigst daher wieder aus der Badewanne.`n");
	if ($session[user][sex]) { //weiblich
		output("Du rasierst dir noch schnell die Beine bevor du wieder ein deine Kleider springst");
		output("Nach einer Stunde schminken vor dem Spiegel bist du bereit für neue Abenteuer`n");
	} else { //männlich
		output("Du rasierst dich noch schnell und ziehst dich danach schnell wieder an.");
		output("Erfrischt geht es auf zu neuen Abenteuern`n");
	} //if
	switch (e_rand(0,7)) {
	case 4: //get charmpoint
		output("Du fühlst dich besonders erfrischt und schön und erhälst daher `6einen Charmpunkt`2");
		$session[user][charm]++;
		break;
	case 6: // loose charmpoint
		output("Du schneidest dich beim rasieren und verlierst daher `6einen Charmpunkt`2");
		$session[user][charm]--;
		break;
	} //switch
	$session['user']['clean']=0;
	addnav("Zurück zum Haus","houses.php?op=drin");
}
//I cannot make you keep this line here but would appreciate it left in.
output("`n`n");
//rawoutput("<div style=\"text-align: left;\"><a href=\"http://www.pqcomp.com\" target=\"_blank\">Based on Bathhouse and Odor by Lonny @ http://www.pqcomp.com</a><br>");
page_footer();
?>