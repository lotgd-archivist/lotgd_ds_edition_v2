<?php
/*
* Version:        10.09.2004
* Author:        bibir
* Email:       logd_bibir@email.de
* For:         http://logd.chaosonline.de
*
* Purpose:    special where you can send a surprising present to a person
*
*/

//how much gold per level to pay
$lvlcost = 100;
$session['user']['specialinc']="surprise.php";


if ($_GET['op']=="leave") {
    $session['user']['specialinc']="";
    redirect("forest.php");

}
elseif ($_GET['op']=="gift") {
    if ($session['user']['gold'] < $lvlcost*$session['user']['level']){
        output("`QDu hast nicht genügend Gold dabei - so sieht der Händler zu, dass er das Weite sucht.`n
        Du stehst noch einige Zeit verdutzt da und verlierst deshalb einen Waldkampf.");
        $session['user']['turns']--;
        $session['user']['specialinc']="";
    }else{
        output("`9Der Händler schaut dich an und fragt:`n");
        output("`t\"Wem willst du dieses Paket schicken?\"`0");
        if (isset($_POST['search']) || !empty($_GET['search'])){
            if (!empty($_GET['search'])) $_POST['search']=$_GET['search'];
            $search="%";
            for ($x=0;$x<strlen($_POST['search']);$x++){
                $search .= substr($_POST['search'],$x,1)."%";
            }
            $search="name LIKE '".$search."' AND ";
            if ($_POST['search']=="weiblich") $search="sex=1 AND ";
            if ($_POST['search']=="männlich") $search="sex=0 AND ";
        }else{
			
			$link = 'forest.php?op=gift';
			addnav('',$link);
			
			output('`n
					<form method="POST" action="'.$link.'">
						Name des Empfängers:  
						<input type="text" name="search"> <input type="submit" value="Suchen"> 
					</form>');
		
            $search="";
        }
		
		if($search != '') {
		
			$ppp=25; // Player Per Page to display
			if (!$_GET['limit']){
				$page=0;
			}else{
				$page=(int)$_GET['limit'];
				addnav("Vorherige Seite","forest.php?op=gift&limit=".($page-1)."&search=$_POST[search]");
			}
			$limit=($page*$ppp).",".($ppp+1);
			$sql = "SELECT login,name,level,sex,acctid FROM accounts WHERE $search locked=0 AND acctid<>".$session[user][acctid]." AND charm>1 ORDER BY (acctid='".$session['user']['marriedto']."') DESC,login,level LIMIT $limit";
			$result = db_query($sql);
			if (db_num_rows($result)>$ppp) addnav("Nächste Seite","forest.php?op=gift&limit=".($page+1)."&search=$_POST[search]");
			//output("<form action='forest.php?op=send' method='POST'>Nach Name suchen: <input name='search' value='$_POST[search]'><input type='submit' class='button' value='Suchen'></form>",true);
			addnav("","forest.php?op=send");
			output("<table cellpadding='3' cellspacing='0' border='0'><tr class='trhead'><td>Name</td><td>Level</td><td>Geschlecht</td></tr>",true);
			for ($i=0;$i<db_num_rows($result);$i++){
				$row = db_fetch_assoc($result);
				output("<tr class='".($i%2?"trlight":"trdark")."'><td><a href='forest.php?op=send&name=".HTMLEntities($row['acctid'])."'>",true);
				output($row['name']);
				output("</a></td><td>",true);
				output($row['level']);
				output("</td><td align='center'><img src='images/".($row['sex']?"female":"male").".gif'></td></tr>",true);
				addnav("","forest.php?op=send&name=".HTMLEntities($row['acctid']));
			}
			output("</table>",true);
		}
		
        addnav("Zurück in den Wald","forest.php?op=leave");
    }

}
elseif ($_GET['op']=="send") {
    $name=$_GET['name'];
    $session['user']['specialinc']="";
    $session['user']['gold'] -= $lvlcost*$session['user']['level'];
    switch(e_rand(1,4)){
        case 1:
            $gift = "Rubin.";
            $effekt = "";
            db_query("UPDATE accounts SET gems=gems+1 WHERE acctid=$name");
            break;
        case 2:
            $gift = "Beutel mit Knochenstücken.";
            $gefallen = 15;
            $effekt = "Du erhältst $gefallen Gefallen bei Ramius.";
               db_query("UPDATE accounts SET deathpower=deathpower+$gefallen WHERE acctid=$name");
            break;
        case 3:
            $gift = "Amulett.";
			
			item_add($session['user']['acctid'],'gamulett');

            break;
        case 4:
            $gift = "";
            break;
        default:
            output("Es ist ein Fehler aufgetreten - wie hast du das geschafft?");
    }
    if($gift == "") {
        output("`TDer Händler nimmt dein Gold und versucht, sich aus dem Staub zu machen. Du verfolgst ihn und schlägst ihn mit
        deiner Waffe `q".$session['user']['weapon']." `Tnieder.`nIn deiner Wut bekommst du `22 Waldkämpfe. `TDoch dein Gold bleibt aus unerklärlichen Gründen spurlos verschwunden.");
        $session['user']['turns']+=2;
    } else {
        output("`TDein Paket wurde versandt. Es ist ein ".$gift);
        $mailmessage=$session['user']['name'];
        $mailmessage.="`7 hat dir ein Überraschungspaket geschickt.  Du öffnest es. Es ist ein `6";
        $mailmessage.=$gift."`n".$effekt;
        systemmail($name,"`2Geschenk erhalten!`0",$mailmessage);
    }

}
else {
    output("`9Du kommst auf eine Lichtung und dir kommt ein fahrender Händler entgegen.
    Er hält sofort sein Pferd an, als er dich erblickt und bietet dir seine Waren an.");
    output("`n`t\"".($session['user']['sex']?"Gute Frau":"Guter Mann").", was haltet Ihr davon, einem lieben Menschen eine kleine
    Aufmerksamkeit zukommen zu lassen?\"`n");
    output("`9Er erzählt dir, dass er der Person ein Überraschungspaket bringen wird und auch du nicht wissen wirst,
    was diese Person bekommt. Doch soviel erzählt er dir:");
    output("`n`t\"Ich hab schon einigen einen wunderschön glitzernden Rubin zukommen lassen, aber auch
    Knochenstücke, die Ramius faszinierten und demjenigen einige Gefallen bei ihm einbrachten. Andererseits ist
    da auch noch ein Amulett.`nDoch es ist natürlich nicht kostenlos.`n");
    $cost = $lvlcost * $session['user']['level'];
    output("Wenn dir `^".$cost." Goldstücke `tnicht zu viel sind, werd ich für dich das Päckchen wählen und ausliefern.\"`n");
    output("`n`@Was machst du?`n`0");
    output("<a href=\"forest.php?op=gift\">Geschenk verschicken</a>`n",true);
    output("<a href=\"forest.php?op=leave\">weitergehen</a>`n",true);
    addnav("Geschenk verschicken","forest.php?op=gift");
    addnav("Weitergehen","forest.php?op=leave");
    addnav("","forest.php?op=gift");
    addnav("","forest.php?op=leave");
}
?>