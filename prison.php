<?
require_once "common.php";
checkday();
addcommentary();

page_header("Der Kerker");
output("`b`c`2Der Kerker`0`c`b`n");

$sql3 = "SELECT sentence FROM account_extra_info WHERE acctid=".$session['user']['acctid']."";
    	$res3 = db_query($sql3);
        $row3 = db_fetch_assoc($res3);
        
if ($session['user']['imprisoned']!=0) {
 if ($session['user']['drunkenness']>90) { $session['user']['drunkenness']=90;}
 if ($session['user']['imprisoned']>0) {
   if ($row3['sentence']>0) {
   $maxsentence=getsetting("maxsentence",5);
   $lockup=getsetting("locksentence",4);
   $session['user']['imprisoned']+=$row3['sentence'];
   if ($session['user']['imprisoned']>$maxsentence) $session['user']['imprisoned']=$maxsentence;
   $sql = "UPDATE account_extra_info SET sentence=0 WHERE acctid=".$session['user']['acctid']."";
   db_query($sql);  }
 }


if ($_GET[op]=="") {
output ("`tMan hat dich also erwischt. Du wusstest instinktiv, dass nicht auf ewig so durchkommen w�rdest, aber nun bist du geschnappt worden und sitzt ");
if ($session['user']['imprisoned']>0) {
output ("f�r die n�chsten `@".($session['user']['imprisoned'])." Tage`t im Kerker.`n");}
else { 
	output ("auf `bunbestimmte Zeit`b im Kerker.`n Wahrscheinlich hast du `bgegen die Regeln versto�en`b oder eine `bMail zur Namens�nderung ignoriert`b! �fnne zun�chst durch einen Klick auf 'Brieftauben' dein `bPostfach`b und seh nach, ob du dort einen Grund findest. Falls nicht, schreib eine `bAnfrage`b.`n`n"); 
}
output("`tDeine Zelle ist nicht sehr gro�, aber wenigstens hast du sie f�r dich allein. Auf wenigen Quadratmetern hat man dir eine Pritsche, einen Tisch und ein �belriechendes Loch im Boden als Abort zur Verf�gung gestellt. Die anderen Zellen sind neben deiner und jenseits eines breiten Ganges deiner Zelle gegen�ber gelegen. Deine armen Mitgefangenen kannst du sehen und h�ren, aber so weit du deine Arme auch ausstreckst, erreichen kannst du sie nicht.`n`n`^Du sp�rst, dass �ber diesem Kerker eine starke Aura liegt, die jegliche Magie unterbindet. Auch hat man dir gesagt, dass nur der Versuch zu zaubern, mit nicht weniger als `410 Peitschenhieben`^ belohnt wird!`n`n`&");
output ("So bleibt dir nichts �brig als die Zeit, die dir auferlegt wurde abzusitzen. Oder kommt dir etwas Anderes in den Sinn ?`n`n");

if ($session['user']['imprisoned']!=0) {$session['user']['location'] = USER_LOC_PRISON;}

addnav("Umsehen","prison.php?op=look");
if ($session['user']['imprisoned']>1) {addnav("Strafe absitzen","prison.php?op=wait");}
if ($session['user']['imprisoned']>0) {addnav("Freikaufen","prison.php?op=bribe");}
if ($session['user']['imprisoned']>0) {addnav("Ausbrechen","prison.php?op=flee");}
if (($session['user']['imprisoned']>0) && ($session['user']['marks']>=31))
{addnav("Die Male zeigen","prison.php?op=chosen");}
if ($session['user']['imprisoned']>0) {addnav("Inventar zeigen","prison.php?op=letter");}


addnav("Schlafen (Logout)","login.php?op=logout&loc=".USER_LOC_PRISON,true);
viewcommentary("prison","Flehen:",30,"fleht");
}

} else {
if ($_GET[op]=="")
{
output ("Du befindest dich nun im Kerker, jedoch nur zu Besuch. Der Kerkermeister f�hrt dich durch den langen Wachgang und du kannst die armen Gefangenen ganz genau sehen, wie sie in ihren engen Zellen sitzen und um Gnade betteln. Ein leichtes L�cheln umspielt deine Lippen, als du das ein oder andere dir bekannte Gesicht erblickst. Der Kerkermeister l�sst dich gew�hren, beh�lt dich aber immer wachsam im Auge.`n`n`^Du sp�rst, dass �ber diesem Kerker eine starke Aura liegt, die jegliche Magie unterbindet. Auch wei�t du, dass dich allein der Versuch zu zaubern, schnell selbst hinter Gitter bringen k�nnte!`n`n`&");
viewcommentary("prison","Verspotten:",30,"spottet");
addnav("Umsehen","prison.php?op=look");
addnav("Kaution zahlen","prison.php?op=free");
addnav("Schutzhaft","prison.php?op=prot");
if ($row3['sentence']>0) addnav("Den Beh�rden stellen","prison.php?op=imprison");
addnav("Den Kerker verlassen","village.php");
}
}
if ($_GET[op]=="prot") {
output ("`&Der Kerkermeister mustert dich : \"`4Soso... dir will man also an den Kragen... und du suchst hier Zuflucht?! Ha, du Narr! Kannst du gern haben, aber obwohl du nichts getan hast wirst du behandelt werden, wie der ganze Abschaum hier unten! Denn meine Schicht ist gleich rum und meine Abl�sung wird nicht wissen, wer was verbrochen hat und wer hier nur zu Gast ist...`nAu�erdem wird dich der Spa� `#3 Edelsteine`4 kosten... f�r die �ppige Verpflegung und... so.\"");
output ("`n`n`&Das klingt ja toll. Bist du dir sicher?");
addnav ("Ok, Schutzhaft!","prison.php?op=prot2");
addnav ("Nee, dann nicht...","prison.php");
}
else if ($_GET[op]=="prot2") {
 if ($session['user']['gems']<3) {
   output ("`&`nOh je... zu arm f�r den Kerker... der Kerkermeister klopft sich lachend auf die Schenkel.`n`n");
   addnav("Zur�ck","prison.php");
} else {
   output ("`&`nDu zahlst 3 Edelsteine und der Kerkermeister sperrt dich in eine Zelle. Hier wird dich niemand angreifen k�nnen!");
   $session['user']['gems']-=3;
   $session['user']['imprisoned']=1;
   addnav("In die Zelle","prison.php");
 }
}
else if ($_GET[op]=="imprison") {
output("`&Gegen dich liegt ein Haftbefehl vor.`nWenn du dich jetzt stellst wirst du f�r ".$row3['sentence']." Tage den Kerker von der anderen Seite kennen lernen.`nWillst du das wirklich tun?");
   addnav("Ja","prison.php?op=imprison2");
   addnav("Nein","prison.php");
}
else if ($_GET[op]=="imprison2") {
   $maxsentence=getsetting("maxsentence",5);
   $lockup=getsetting("locksentence",4);
   $session['user']['imprisoned']+=$row3['sentence'];
   if ($session['user']['imprisoned']>$maxsentence) { $session['user']['imprisoned']=$maxsentence;}

    $sql = "UPDATE account_extra_info SET sentence=0 WHERE acctid=".$session['user']['acctid']."";
   db_query($sql);
   addnews("`#".$session['user']['name']." hat `^sich dem Kerkermeister gestellt und eine ".$session['user']['imprisoned']."t�gige Haftstrafe angetreten.");
   redirect("prison.php");
}

else if ($_GET[op]=="wait") {
output("`&Du hast hier die M�glichkeit dich deinen Schicksal zu ergeben und die dir auferlegte Strafe abzusitzen.`nDie abgesessenen Tag werden dabei deinem Strafregister hinzugef�gt, du darfst den Kerker allerdings nach Ablauf dieses Tages verlassen. M�chtest du das?`n(Dies beschleunigt lediglich die Wartezeit f�r den Spieler, die eigentliche Haftzeit des Charakters verk�rzt sich dadurch nicht!)");
addnav("Ja, warten","prison.php?op=wait2");
addnav("Nein","prison.php");
}

else if ($_GET[op]=="wait2") {
$days=$session['user']['imprisoned'];
$injail=$days-1;
output("`&Du setzt dich auf deine Pritsche und l�sst die Tage bis zu deiner Entlassung verstreichen...`nDeinem Strafregister wurden `^$injail`& Tage hinzugef�gt.`nMorgen kommst du hier raus!`n");
$sql = "UPDATE account_extra_info SET daysinjail=daysinjail+$injail WHERE acctid = ".$session['user']['acctid']."";
db_query($sql) or die(sql_error($sql));
$session['user']['imprisoned']=1;
$session['user']['age']+=$injail;
addnav("Weiter","prison.php");
}

else if ($_GET[op]=="free") {
output ("Wer soll freigekauft werden?`n`n");
$sql = "SELECT acctid,name,race,imprisoned,login,sex,level,laston,loggedin,activated FROM accounts WHERE imprisoned!=0 ORDER BY level DESC, dragonkills DESC, login ASC";
$result = db_query($sql) or die(db_error(LINK));
output("<table border=0 cellpadding=2 cellspacing=1 bgcolor='#999999'>",true);
output("<tr class='trhead'><td><b>Level</b></td><td><b>Name</b></td><td><b>Rasse</b></td><td><b><img src=\"images/female.gif\">/<img src=\"images/male.gif\"></b></td><td><b>Status</b></td><td><b>Strafe in Tagen</b></tr>",true);
$max = db_num_rows($result);
for($i=0;$i<$max;$i++){
    $row = db_fetch_assoc($result);
    output("<tr class='".($i%2?"trdark":"trlight")."'><td>",true);
    output("`^$row[level]`0");
    output("</td><td>",true);
    if ($session[user][loggedin]) output("<a href=\"mail.php?op=write&to=".rawurlencode($row['login'])."\" target=\"_blank\" onClick=\"".popup("mail.php?op=write&to=".rawurlencode($row['login'])."").";return false;\"><img src='images/newscroll.GIF' width='16' height='16' alt='Mail schreiben' border='0'></a>",true);
    if ($session[user][loggedin]) output("<a href='prison.php?op=free2&char=".$row['acctid']."'>",true);
    if ($session[user][loggedin]) addnav("","prison.php?op=free2&char=".$row['acctid']."");
    output("`".($row[acctid]==getsetting("hasegg",0)?"^":"&")."$row[name]`0");
    if ($session[user][loggedin]) output("</a>",true);
    output("</td><td>",true);
    output($colraces[$row['race']]);
    output("</td><td align=\"center\">",true);
    output($row[sex]?"<img src=\"images/female.gif\">":"<img src=\"images/male.gif\">",true);
    output("</td><td>",true);
    $loggedin=user_get_online(0,$row);
    output($loggedin?"`#Wach`0":"`3Schl�ft`0");
    output("</td><td>",true);
     if ($row['imprisoned']>0) {
    output($row['imprisoned']); }
     else { output("unbestimmt"); }
    output("</td></tr>",true);

}
output("</table>",true);
addnav("Zur�ck","prison.php");
  }
else if ($_GET[op]=="free2") {
$result = db_query("SELECT name,acctid,level,imprisoned FROM accounts WHERE acctid=$_GET[char]");
$row = db_fetch_assoc($result);
$cost= abs($row['imprisoned'])*$row['level'];
$lockup=getsetting("locksentence",4);

if ($row['imprisoned']>0) {
  if ($row['imprisoned']<$lockup)
  {
output("`&Der Kerkermeister sieht dich scharf an : \"`4So? Du willst also ".($row['name'])."`4 aus diesem Loch rausholen? Kannst du gern haben, aber das wird nicht billig! Bezahl mir `^$cost`4 Edelsteine und ich schlie�e die T�r auf!`&\"`n`nWillst du die Kaution bezahlen?");
addnav("Ja","prison.php?op=free3&gem=$cost&char=$row[acctid]");
addnav("NEIN!","prison.php");
  } else {
   output("`&Der Kerkermeister sieht dich scharf an : \"`4Nein! ".($row['name'])."`4 hat eine zu hohe Haftstrafe um freigekauft zu werden!`&\"`n`n");
  }
}
else
if ($row['imprisoned']<0) {
output("`&Der Kerkermeister sieht dich scharf an : \"`4Nein! ".($row['name'])."`4 hat zu viel auf dem Gewissen um freigekauft zu werden!`&\"`n`n");
}
addnav("Weiter","prison.php");}

else if ($_GET[op]=="free3") {
$result = db_query("SELECT name,acctid,imprisoned FROM accounts WHERE acctid=$_GET[char]");
$row = db_fetch_assoc($result);
  $cost=$_GET[gem];
if ($session['user']['gems']<$cost) {
 output("`&Na das kannst du dir beim besten Willen nicht leisten...");
 addnav("Stimmt...","prison.php");
} else {
output ("`&Du dr�ckst dem Kerkermeister $cost Edelsteine in die Hand und ".($row['name'])."`& ist wieder frei! Du f�hlst dich einfach... toll.");
 $session['user']['gems']-=$cost;
$sql = "UPDATE accounts SET imprisoned=0,location=0 WHERE acctid = ".$row['acctid']."";
db_query($sql) or die(sql_error($sql));
systemmail($row['acctid'],"`\$Kaution bezahlt!`0","`@{$session['user']['name']}`& hat die Kaution f�r dich bezahlt und dich damit aus dem Kerker befreit. Du solltest dich dankbar erweisen!");
addnews("`#".$session['user']['name']." hat `@".$row['name']."`& aus dem Kerker freigekauft!");
$sql="INSERT INTO commentary (postdate,section,author,comment) VALUES (now(),'prison',".$session[user][acctid].",'/me `@bezahlt die Kaution f�r ".$row['name']."`@.')";
db_query($sql) or die(db_error(LINK));

}

addnav("Weiter","prison.php");
 }
else if ($_GET[op]=="look") {
output ("Zur Zeit sind folgende Krieger inhaftiert:`n`n");
$sql = "SELECT acctid,name,race,imprisoned,login,sex,level,laston,loggedin,activated FROM accounts WHERE imprisoned!=0 ORDER BY level DESC, dragonkills DESC, login ASC";
$result = db_query($sql) or die(db_error(LINK));
output("<table border=0 cellpadding=2 cellspacing=1 bgcolor='#999999'>",true);
output("<tr class='trhead'><td><b>Level</b></td><td><b>Name</b></td><td><b>Rasse</b></td><td><b><img src=\"images/female.gif\">/<img src=\"images/male.gif\"></b></td><td><b>Status</b></td><td><b>Strafe in Tagen</b></tr>",true);
$max = db_num_rows($result);
for($i=0;$i<$max;$i++){
    $row = db_fetch_assoc($result);
    output("<tr class='".($i%2?"trdark":"trlight")."'><td>",true);
    output("`^$row[level]`0");
    output("</td><td>",true);
    if ($session[user][loggedin]) output("<a href=\"mail.php?op=write&to=".rawurlencode($row['login'])."\" target=\"_blank\" onClick=\"".popup("mail.php?op=write&to=".rawurlencode($row['login'])."").";return false;\"><img src='images/newscroll.GIF' width='16' height='16' alt='Mail schreiben' border='0'></a>",true);
    if ($session[user][loggedin]) output("<a href='bio.php?op=pri&char=".rawurlencode($row['login'])."'>",true);
    if ($session[user][loggedin]) addnav("","bio.php?op=pri&char=".rawurlencode($row['login'])."");
    output("`".($row[acctid]==getsetting("hasegg",0)?"^":"&")."$row[name]`0");
    if ($session[user][loggedin]) output("</a>",true);
    output("</td><td>",true);
    output($colraces[$row['race']]);
    output("</td><td align=\"center\">",true);
    output($row[sex]?"<img src=\"images/female.gif\">":"<img src=\"images/male.gif\">",true);
    output("</td><td>",true);
    $loggedin=user_get_online(0,$row);
    output($loggedin?"`#Wach`0":"`3Schl�ft`0");
    output("</td><td>",true);
     if ($row['imprisoned']>0) {
    output($row['imprisoned']); }
     else { output("unbestimmt"); }
    output("</td></tr>",true);

}
output("</table>",true);
addnav("Zur�ck","prison.php");
}
else if ($_GET[op]=="chosen") {
output ("`&Du wei�t, dass die G�tter den Missbrauch ihrer Geschenke nicht gern sehen und glaubst geh�rt zu haben, dass sie einen solchen Frevel ab und an mit dem Entzug eines der Male bestrafen.`nWillst du es wirklich riskieren ein Mal zu verlieren um jetzt hier raus zu kommen ?");
addnav("Ja","prison.php?op=chosen2");
addnav("NEIN!","prison.php");
}
else if ($_GET[op]=="chosen2") {
output ("`&Du wartest bis der W�chter wieder seine Runde dreht und r�usperst dich laut, h�lst ihm dabei demonstrativ deinen Arm mit den `#5 Malen`& unter die Nase. Kreidebleich und unter wortreicher Entschuldigung schlie�t er deine Zelle auf und l�sst dich frei.`n");
if (e_rand(1,3)==2) {output ("`n`4Die G�tter reagieren zornig auf das Ausnutzen der Male. Sie sehen es nicht ein deine kriminellen Machenschaften zu decken und entziehen dir das `^Erdmal`4`nSag nicht, man h�tte dich nicht gewarnt...");
if ($session['user']['marks']>=32) { $session['user']['marks']=30;
systemmail($session['user']['acctid'],"`\$Von : Blutgott!`0","`&Sterblicher!`nWisse dass sich der Blutgott nur mit jenen abgibt, die sich die Auserw�hlten nennen. Da du nun nicht mehr dazu geh�rst betrachte unseren Pakt als nichtig!");
} else {$session['user']['marks']=30;}
} else
{ output ("`n`^Die G�tter registrieren missbilligend deine Tat, lassen dich aber diesmal noch davon kommen."); }
$session['user']['imprisoned']=0;
$session['user']['location']=0;
$sql="INSERT INTO commentary (postdate,section,author,comment) VALUES (now(),'prison',".$session[user][acctid].",'/me `@wird von der Wache frei gelassen.')";
db_query($sql) or die(db_error(LINK));
addnav("Na also","prison.php");
}
else if ($_GET[op]=="bribe") {
$caution=($session['user']['imprisoned']*($session['user']['level']));
output ("`&Du wartest bis der W�chter wieder seine Runde dreht und raschelst einmal ganz unauff�llig mit deinem Edelsteinbeutel. Diese Geste wohl verstehend schaut dich der W�chter an und deutet dir ebenso unauff�llig an, dass deine Freilassung wohl `^$caution `&Edelsteine kosten w�rde.`n");
output ("Nach reichlicher �berlegung und Pr�fung deines Edelsteinvorrates triffst du folgende Entscheidung...");
if ($session['user']['gems']>= $caution) addnav("Bestechen","prison.php?op=bribe2");
addnav("Die Sache vergessen","prison.php");
}
else if ($_GET[op]=="bribe2") {
$caution=($session['user']['imprisoned']*($session['user']['level']));
$lockup=getsetting("locksentence",4);
$chance=e_rand(1,2);
if ($session['user']['imprisoned']>=$lockup) { $chance=2; }
if ($chance==1) {
output ("`&Der W�chter schaut in deine Zelle und beginnt pl�tzlich laut zu rufen : `#\"Nein! Aber dich kenne ich doch! DU kannst es gewiss nicht gewesen sein... Es handelt sich um einen Irrtum! So warte, ich lasse dich frei!\"`&`nEr �ffnet die T�re und nimmt sich unauff�llig die versprochenen Edelsteine. Du bist frei! ");
$session['user']['gems']-=$caution;
$session['user']['imprisoned']=0;
$session['user']['location']=0;
$sql="INSERT INTO commentary (postdate,section,author,comment) VALUES (now(),'prison',".$session[user][acctid].",'/me `@wird von der Wache frei gelassen.')";
db_query($sql) or die(db_error(LINK));
addnav("Raus hier!","village.php"); } else
{
output ("`&Der W�chter nimmt deine Edelsteine entgegen und geht grinsend seines Weges, nat�rlich ohne dich freizulassen... Der Schuft hat dich reingelegt!");
$session['user']['gems']-=$caution;
addnav("Mist!","prison.php");
}

}
else if ($_GET[op]=="letter") {
  if (!$_GET[id]){
  
	output("`&Du wartest bist der W�rter wieder seine Runden dreht und r�usperst dich laut. Nun, da du seine Aufmerksamkeit hast, kramst du in deinen Taschen und h�ltst dem W�rter folgendes unter die Nase :`n`n");
	
	$options = array('Zeigen'=>'letter');
		
	item_show_invent(' owner='.$session[user][acctid].' AND showinvent>0 AND deposit1=0 AND deposit2=0 ', false, 0, 1, 1, 'Irgendwie scheinen deine Taschen ein Loch zu haben. Du findest nichts was den W�rter interessieren k�nnte.', $options);
	
	addnav("Zur�ck","prison.php");
    }else{ 

		$row = item_get(' id='.(int)$_GET['id'],false);
		
 if ($row['tpl_id']!='frbrf') {
 output ("`&Der W�rter betrachtet sich dein $row[name] ganz genau und schaut dich mit ernster Miene an. Langsam streckt er den Zeigefinger seiner rechten Hand auf und f�hrt ihn ebenso langsam an seine Schl�fe, wo er mehrmals mit dem Finger dagegen tippt, bevor er seinen Rundgang fortsetzt.`n`n");
addnav("Das war wohl nix...","prison.php");
} else {
output("`&Der W�rter erkennt sehr wohl was du ihm mit diesem $row[name] deutlich machen willst und schlie�t die T�re zu deiner Zelle auf. Du �bergibst ihm den $row[name] und er zerreist das Pergament in kleine Streifen. Dann verl�sst du deine Zelle, da du ja nun wieder frei bist!");
		
		item_delete(' id='.(int)$_GET['id']);
        
$session['user']['imprisoned']=0;
$session['user']['location']=0;
$sql="INSERT INTO commentary (postdate,section,author,comment) VALUES (now(),'prison',".$session[user][acctid].",'/me `@wird vom W�chter frei gelassen.')";
db_query($sql) or die(db_error(LINK));
addnav("Freiheit!","prison.php");
}
}
}
else if ($_GET[op]=="flee") {
$lockup=getsetting("locksentence",4);
if ($session['user']['turns']>0){
 if ($session['user']['imprisoned']<$lockup)
 {
output ("`&Du machst dich auf die Suche nach einem Weg in die Freiheit. Du r�ttelst an den Gitterst�ben, suchst nach Geheimt�ren in der Wand, f�r einen winzigen Augenblick denkst du sogar an das stinkende Loch... aber dann kommt dir nur noch der �lteste Trick der Welt in den Sinn und du wirfst dich schreiend vor Schmerz auf den Boden.`n");
$session['user']['turns']-=1;
switch(e_rand(1,5)){
case 1 :
case 2 :
output ("Doch au�er einem sp�ttichen L�cheln deiner Mitgefangenen bringt dir das nichts.");
$session['user']['turns']-=1;
addnav("Na super...","prison.php");
break;
case 3 :
case 4 :
output ("`&Als der W�rter kommt um nach dir zu sehen h�lst du deinen h�lzernen Napf bereit und versuchst ihn damit niederzuschlagen. Doch leider bist du nicht der Erste, der diese geniale Idee hatte und der W�chter schafft es leicht dich zu �berw�ltigen.`n");
if ($session['user']['imprisoned']<5) {
output ("`&Tja... deine Haftstrafe wurde soeben um `#1 Tag`& verl�ngert");
$session['user']['imprisoned']+=1; }
$session['user']['turns']-=1;
addnav("Sch...ade","prison.php");
break;
case 5 :
$bounty=$session['user']['imprisoned']*$session['user']['level']*50;

// Items verlieren
$lost_str = '';

$min_chance = e_rand(1,255);

$res = item_list_get(' prisonescloose >= '.$min_chance.' AND owner='.$session['user']['acctid'].' AND deposit1=0 AND deposit2=0 ', 
						' ORDER BY RAND() LIMIT 1',true,' name,vendor,id ' );

if(db_num_rows($res) > 0) {
	$item = db_fetch_assoc($res);
	
	if($item['vendor'] == 1 || $item['vendor'] == 3) {	// Wenn bei Wanderh�ndler zu erwerben
		item_set_owner(' id='.$item['id'], array('owner'=>0));
	}
	else {
		item_delete(' id='.$item['id']);
	}
	
	$lost_str = '`n`n`^W�hrend der Flucht f�llt dir '.$item['name'].'`^ aus der Tasche!';
	$lost_comment_str = '. Dabei fiel '.$item['name'].'`@ aus der Tasche';
	
}
// END items verlieren

output ("`&Als sich dir der W�chter sorgenvoll n�hert nutzt du deine Chance und schl�gst ihn mit deinem Holznapf nieder. Durch die offene T�re rennst du so schnell du kannst hinaus und versteckst dich erst einmal im Wald.`n`nSp�ter erf�hrst du, dass aufgrund deiner Flucht nun ein Kopfgeld in H�he von $bounty Gold auf dich ausgesetzt ist...".$lost_str);
$session['user']['turns']-=1;
$session['user']['imprisoned']=0;
$session['user']['location']=0;
$session['user']['bounty']+=$bounty;
$bounty=$session['user']['bounty'];
addnews("`^".$session['user']['name']."`@ ist aus dem Kerker geflohen! Es steht nun ein Kopfgeld von `^$bounty Gold`@ aus!");
addcrimes("`^".$session['user']['name']."`@ ist aus dem Kerker geflohen! Es steht nun ein Kopfgeld von `^$bounty Gold`@ aus!");
$sql="INSERT INTO commentary (postdate,section,author,comment) VALUES (now(),'prison',".$session[user][acctid].",'/me `@ist soeben geflohen".$lost_comment_str."!')";
db_query($sql) or die(db_error(LINK));


addnav("Schnell weg...","forest.php");
break;
}
 }
 else
 {
 output ("`&Aufgrund der H�he deiner Haftstrafe hat man dich in den Hochsicherheitsbereich gebracht. Hier ist eine Flucht unm�glich!");
 addnav("Zur�ck","prison.php");
 } // h�chst
} else
{ output ("`&So gern du hier auch raus willst, du bist einfach zu m�de f�r eine Flucht.");
addnav("Zur�ck","prison.php");
}
}
page_footer();
?>
