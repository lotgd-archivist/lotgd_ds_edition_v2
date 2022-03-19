<?php

// 1508004

require_once "common.php";
addcommentary();

if ($_GET[op]=="killed")
{   page_header();
    addnews("`6Vessa`5 wurde beobachtet, wie sie die Leiche von `^".$session['user']['name']."`5 im Wald verscharrte.");
    output("`5Du bezahlst und die Zigeunerin versetzt dich in Trance.`n");
    output("`5Du befindest dich nun im Reich der Toten, wandelst umher, auf der Suche nach dem wertvollen Kleinod.`n`n");

if ($_GET[act]=="user") {
output("Das gefällt dem alten Besitzer natürlich gar nicht! Und bevor du dich versiehst hast du eine Geisterhand an deiner Kehle, die unerbittlich zudrückt. Bevor dich Dunkelheit umgibt hörst du `6Vessa`5 leise fluchen, als sie deinen leblosen Körper entsorgt."); }
if ($_GET[act]=="chance") {
output("Du willst gerade zurückkehren als du bemerkst, dass plötzlich von überall her Hände nach dir greifen und dich festhalten. Du spürst wie sich deine Seele von deinem Körper körper löst und bevor dich Dunkelheit umgibt hörst du `6Vessa`5 leise fluchen, als sie deinen leblosen Körper entsorgt."); }
    $session['user']['alive']=false;
    $session['user']['hitpoints']=0;
    addnav("Du bist tot");
    addnav("Och nee!","shades.php");
} else {

$cost = $session[user][level]*20;
$gems=array(1=>1,2,3);
$costs=array(1=>4000-3*getsetting("selledgems",0),7800-6*getsetting("selledgems",0),11400-9*getsetting("selledgems",0));
$scost=1200-getsetting("selledgems",0);
if ($_GET[op]=="pay"){
	if ($session[user][gold]>=$cost)
	{ // Gunnar Kreitz
		$session[user][gold]-=$cost;
		//debuglog("spent $cost gold to speak to the dead");
		if ($_GET[was]=="flirt")
		{
			 redirect("gypsy.php?op=flirt2");
		} 
		else 
		{
			redirect("gypsy.php?op=talk");
		}
	}
	else
	{
		page_header("Zigeunerzelt");
		addnav("Zurück zum Marktplatz","market.php");
		output("`5Du bietest der alten Zigeunerin deine `^{$session[user][gold]}`5 Gold für die Beschwörungssitzung. Sie informiert dich, dass die Toten zwar tot, aber deswegen trotzdem nicht billig sind.");
	}
}
else if ($_GET[op]=="talk")
{
	page_header("In tiefer Trance sprichst du mit den Schatten");
	// by nTE- with modifications from anpera
	$sql="SELECT name FROM accounts WHERE locked=0 AND loggedin=1 AND alive=0 AND laston>'".date("Y-m-d H:i:s",strtotime(date("r")."-".getsetting("LOGINTIMEOUT",900)." seconds"))."' ORDER BY login ASC"; 
	$result=db_query($sql) or die(sql_error($sql));
	$count=db_num_rows($result);
	$names=$count?"":"niemandem";
	for ($i=0;$i<$count;$i++)
	{ 
		$row=db_fetch_assoc($result); 
		$names.="`^$row[name]"; 
		if ($i<$count) $names.=", "; 
	} 
	db_free_result($result); 
	output("`5Du fühlst die Anwesenheit von $names`5.`n`n"); 
	output("`5Solange du in tiefer Trance bist, kannst du mit den Toten sprechen:`n");
	viewcommentary("shade","Sprich zu den Toten",25,"spricht");
	addnav("Erwachen","market.php");
} 
else if ($_GET[op]=="flirt2")
{ 
	page_header("In tiefer Trance sprichst du mit den Schatten");
	output("`5Die Zigeunerin versetzt dich in tiefe Trance.`n`% Du findest ".($session[user][sex]?"deinen Mann":"deine Frau")." im Land der Schatten und flirtest eine Weile mit ".($session[user][sex]?"ihm, um sein":"ihr, um ihr")." Leid zu lindern. ");
	output("`n`^Du bekommst einen Charmepunkt.");
	$session['bufflist']['lover']=array("name"=>"`!Schutz der Liebe","rounds"=>60,"wearoff"=>"`!Du vermisst deine große Liebe!`0","defmod"=>1.2,"roundmsg"=>"Deine große Liebe lässt dich an deine Sicherheit denken!","activate"=>"defense");
	$session['user']['charm']++;
			
	$session['user']['seenlover']=1;
	addnav("Erwachen","market.php");
}
//Eier-Klau, die 2.
else if($_GET[op]=="egg")
{
page_header();
$sql = "SELECT acctid,name,loggedin,alive FROM accounts WHERE acctid = ".getsetting("hasegg",0);
        $result = db_query($sql) or die(db_error(LINK));
        $row = db_fetch_assoc($result);
        
$ecost=$session[user][level]*100;
if ($session[user][gold]<$ecost) {
output("`5Dein Griff ins Totenreich erwies sich sehr schnell als Griff ins Klo, nachdem die Zigeunerin bemerkt hatte, dass du dir dieses Unternehmen gar nicht leisten kannst. Mit hochrotem Kopf entfernst du dich rasch.`n");
addnav("Nix wie weg","market.php");
   } else {
   $session[user][gold]-=$ecost;
   output("`5Du bezahlst und die Zigeunerin versetzt dich in Trance.`n");
   output("`5Du befindest dich nun im Reich der Toten, wandelst umher, auf der Suche nach dem wertvollen Kleinod.`n`n");
   if ($row[alive]) {
   output("`5Zu deinem Ärger musst du jedoch feststellen, dass sich sowohl das `^goldenen Ei`5 als auch sein Besitzer nicht mehr hier aufhalten. Da hat wohl jemand den Braten gerochen! Dir bleibt nichts weiter übrig als wieder zurück zu kommen.`n");
   addnav("Zurück zum Marktplatz","market.php");
   } else {
   output ("Dann erblickst du es : Das `^goldene Ei`5 in strahlendem Glanz! Langsam pirschst du dich an $row[name] heran und schnappst dir das Ei.`n");
   if ($row[loggedin]) { 
   redirect('gypsy.php?op=killed&act=user');
   } else {
      switch (e_rand(1,5)){

case 1 :
output("Mit mehr Glück als Verstand gelingt es dir tatsächlich mit dem `^goldenen Ei`5 zu entkommen!`n");
output("Ohne ein Wort des Dankes erhebst du dich schnell und flüchtest vor der Zigeunerin, die schon ganz gierig schaut.`n");

systemmail($row[acctid],"`\$Diebstahl!`0","`\${$session['user']['name']}`\$ hat dir im Totenreich das goldene Ei abgenommen!");
savesetting("hasegg",stripslashes($session[user][acctid]));
 item_set(' tpl_id="goldenegg"', array('owner'=>$session[user][acctid]) );
addnews("`^".$session['user']['name']."`^ stiehlt das goldene Ei aus dem Totenreich!");
addnav("Schnell weg","market.php");
break;
case 2 :
case 3 :
case 4 :
case 5 :
redirect('gypsy.php?op=killed&act=chance');
break;
      }

}

}
}
}
//Ausgeklaut
else if($_GET[op]=="buy")
{

	page_header("Zigeunerzelt");
	
	$rowe = user_get_aei('gemsin');
	
	if ($rowe['gemsin']>getsetting("transferreceive",3))
	{
		output("`5Du hast heute schon genug Geschäfte gemacht. `6Vessa`5 hat keine Lust mit dir zu handeln. Warte bis morgen.");
	}
	else if ($session[user][gems]>getsetting("selledgems",0)) 
	{
		output("`6Vessa`5wirft einen neidischen Blick auf dein Säckchen Edelsteine und beschließt, dir nichts mehr zu geben.");
	}
	else 
	{
  	      	if ($session[user][gold]>=$costs[$_GET[level]])
  	      	{
			if (getsetting("selledgems",0) >= $_GET[level]) 
			{
				output( "`6Vessa`5 grapscht sich deine `^".($costs[$_GET[level]])."`5 Goldstücke und gibt dir im Gegenzug `#".($gems[$_GET[level]])."`5 Edelstein".($gems[$_GET[level]]>=2?"e":"").".`n`n");
				$session[user][gold]-=$costs[$_GET[level]];
				$session[user][gems]+=$gems[$_GET[level]];
				
				user_set_aei( array('gemsin'=>$rowe['gemsin']+$gems[$_GET[level]]) );
				
				if (getsetting("selledgems",0) - $_GET[level] < 1) 
				{
					savesetting("selledgems","0");
				}
				else 
				{
					savesetting("selledgems",getsetting("selledgems",0)-$_GET[level]);
				}
			} 
			else 
			{
				output("`6Vessa`5 teilt dir mit, dass sie nicht mehr so viele Edelsteine hat und bittet dich später noch einmal wiederzukommen.`n`n");
			}
		}
		else
		{
			output( "`6Vessa`5 zeigt dir den Stinkefinger, als du versuchst, ihr weniger zu zahlen als ihre Edelsteine momentan Wert sind.`n`n");    
		}
	}
	addnav("Zurück zum Marktplatz","market.php");
}
elseif($_GET[op]=="sell")
{
	page_header("Zigeunerzelt");
	
	$rowe = user_get_aei('gemsout');
	
	$maxout = $session[user][level]*getsetting("maxtransferout",25);
    	if ($session[user][gems]<1)
    	{
        	output("`6Vessa`5 haut mit der Faust auf den Tisch und fragt dich, ob du sie veralbern willst. Du hast keinen Edelstein.`n`n");
	}
	else if ($rowe['gemsout']>getsetting("transferreceive",3))
	{
		output("`5Du hast heute schon genug Geschäfte gemacht. `6Vessa`5 hat keine Lust mit dir zu handeln. Warte bis morgen.");
    	}
    	else
    	{
        	output("`6Vessa`5 nimmt deinen Edelstein und gibt dir dafür $scost Goldstücke.`n`n");
        	$session[user][gold]+=$scost;
        	$session[user][gems]-=1;
        	savesetting("selledgems",getsetting("selledgems",0)+1);
			
			user_set_aei( array('gemsout'=>$rowe['gemsout']+1) );
    	}
	addnav("Zigeunerzelt","gypsy.php");
	addnav("Zurück zum Marktplatz","market.php");
}
else
{
	checkday();
	page_header("Zigeunerzelt");
	$ecost=$cost*5;
	output("`5Du betrittst das Zigeunerzelt hinter `#Pegasus`5' Rüstungsladen, welches eine Unterhaltung mit den Verstorbenen verspricht. Im typischen Zigeunerstil sitzt eine alte Frau hinter 
	einer irgendwie schmierigen Kristallkugel. Sie sagt dir, dass die Verstorbenen nur mit den Bezahlenden reden. Der Preis ist `^$cost`5 Gold.");
	output("`nSollte sich das `^goldene Ei`5 im Totenreich befinden, so könnte sie dir einen Versuch ermöglichen , es zu stehlen. Dies kostet `^$ecost`5 Gold.");
	output("`nDie Zigeunerin `6Vessa`5 gibt dir auch zu verstehen, dass sie mit Edelsteinen handelt.`nMomentan hat sie `#".getsetting("selledgems",0)."`5 Edelsteine auf Lager.");
	if (getsetting("selledgems",0)>=1000)output(" Sie scheint aber kein Interesse an weiteren Edelsteinen zu haben. Oder sie hat einfach kein Gold mehr, um weitere Edelsteine zu kaufen.");
	addnav("Bezahle und rede mit den Toten","gypsy.php?op=pay");

//Goldenes Ei aus dem Totenreich klauen
if (getsetting("hasegg",0)>0){
        $sql = "SELECT name,loggedin,alive FROM accounts WHERE acctid = ".getsetting("hasegg",0);
        $result = db_query($sql) or die(db_error(LINK));
        $row = db_fetch_assoc($result);
        if (!$row[alive]) {
        addnav("Versuche das goldene Ei aus dem Totenreich zu stehlen","gypsy.php?op=egg"); }}
//Klau-Ende

    if ($session[user][charisma]==4294967295 && $session[user][seenlover]<1) 
	{
  		$sql = "SELECT name,alive FROM accounts WHERE ".$session[user][marriedto]." = acctid ORDER BY charm DESC";
  		$result = db_query($sql) or die(db_error(LINK));
		$row = db_fetch_assoc($result);
		if ($row[alive]==0) addnav("Bezahle und flirte mit $row[name]","gypsy.php?op=pay&was=flirt");
	}
	//addnav("Tarotkarten legen (1 Edelstein)","tarot.php");
	if (su_check(SU_RIGHT_COMMENT)) addnav("Superusereintrag","gypsy.php?op=talk");
	addnav("Edelsteine");
	if ($session['user']['level']<15)
	{
		addnav("Kaufe 1 Edelstein ($costs[1] Gold)","gypsy.php?op=buy&level=1");
		addnav("Kaufe 2 Edelsteine ($costs[2] Gold)","gypsy.php?op=buy&level=2");
		addnav("Kaufe 3 Edelsteine ($costs[3] Gold)","gypsy.php?op=buy&level=3");
	}
	if (getsetting("selledgems",100)<100 && $session[user][level]>1) addnav("Verkaufe 1 Edelstein für $scost Gold","gypsy.php?op=sell");
	addnav("Zurück");
	addnav("Zurück zum Marktplatz","market.php");
}
}

page_footer();
?>
