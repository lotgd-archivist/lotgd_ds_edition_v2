<?
// Bregomil
// By Maris (Maraxxus@gmx.de)

if (!isset($session)) exit();

$dart_cost=1; // Kosten f�r die Scheibe
$sack_cost=3; // Kosten f�r den Sandsack
$dummy_cost=5; // Kosten f�r die Puppe

if ($HTTP_GET_VARS['op']==""){
    $session['user']['specialinc']="bregomil.php";
output("`nDu gelangst an eine kleine Lichtung, die du irgendwo schon einmal gesehen hast. Doch diesmal erkennst du ein kleines H�uschen, nah am Waldrand. Die H�tte ist aus Stein und Holz gearbeitet und sieht sehr einladend aus. Die T�r steht weit offen und ohne zu �berlegen gehst du n�her und trittst ein.`n`5\"Willkommen, Freund!\"`0 ert�nt es aus einer Ecke, in der ein kleiner,d�nner Mann sitzt, der gerade an einem St�ck Holz schnitzt.`n`5\"Ich bin Bregomil Auerhahn, K�nstler und Handwerker. Ich habe mich auf die Fertigung von �bungsger�ten spezialisiert. Seid nicht abgeschreckt von meinen Preisen, ich garantiere Euch h�chste Qualit�t! Und dazu werde ich die Ger�te mit dem Antlitz Eures schlimmsten Feindes versehen, damit das Training gleich doppelt so viel Spass macht! Na, was sagt Ihr ?\"`0");
        addnav("Etwas kaufen?");
        addnav("Zielscheibe f�r ".$dart_cost." Edelsteine","forest.php?op=weiter&was=scheibe");
        addnav("Sandsack f�r ".$sack_cost." Edelsteine","forest.php?op=weiter&was=sack");
        addnav("Strohpuppe f�r ".$dummy_cost." Edelsteine","forest.php?op=weiter&was=puppe");
        addnav("Danke, heute nicht!","forest.php?op=weg");
        $session['user']['specialinc'] = "bregomil.php";

}
else if ($HTTP_GET_VARS['op']=="weiter"){
   $session['user']['specialinc']="bregomil.php";
      $was=$HTTP_GET_VARS['was'];
       If ($was=="scheibe") { $cost=$dart_cost; }
       If ($was=="sack") { $cost=$sack_cost; }
       If ($was=="puppe") { $cost=$dummy_cost; }

    if ( $session['user']['gems'] < $cost ) {
        output("`nDu hast leider nicht genug Edelsteine um dir das leisten zu k�nnen. Also l�chelst du peinlich ber�hrt und machst dich davon.`0");
    } else

{
        if ($HTTP_GET_VARS[who]=="") {
            output("\"`#Na, wem soll Euer neues Trainingsger�t denn �hnlich sehen ?`&\"");
            if ($_GET['subop']!="search"){
                output("<form action='forest.php?op=weiter&was=$was&subop=search' method='POST'><input name='name'><input type='submit' class='button' value='Suchen'></form>",true);
                addnav("","forest.php?op=weiter&was=$was&subop=search");
            }else{
                addnav("Neue Suche","forest.php?op=weiter&was=$was");
                addnav("Kann ich das Andere nochmal sehen?","forest.php");
                $search = "%";
                for ($i=0;$i<strlen($_POST['name']);$i++){
                    $search.=substr($_POST['name'],$i,1)."%";
                }
                $sql = "SELECT name,login FROM accounts WHERE (locked=0 AND name LIKE '$search') ORDER BY level DESC";
                //output($sql);
                $result = db_query($sql) or die(db_error(LINK));
                $max = db_num_rows($result);
                if ($max > 100) {
                    output("`n`n\"`#Beschreibt bitte etwas genauer, ich kann so nicht arbeiten!`&`n");
                    $max = 100;
                }
                output("<table border=0 cellpadding=0><tr><td>Name</td></tr>",true);
                for ($i=0;$i<$max;$i++){
                    $row = db_fetch_assoc($result);
                    output("<tr><td><a href='forest.php?op=weiter&was=$was&who=".rawurlencode($row[login])."'>$row[name]</a></td></tr>",true);
                    addnav("","forest.php?op=weiter&was=$was&who=".rawurlencode($row[login]));
                }
                output("</table>",true);
            }
        }else

{
   $sql = "SELECT name,acctid FROM accounts WHERE login=\"$HTTP_GET_VARS[who]\"";
                $result = db_query($sql) or die(db_error(LINK));
                $row = db_fetch_assoc($result);
output ("`5Soso... Euer �berungsger�t soll also so aussehen wie ".($row[name])." ?`0");
addnav("Ja","forest.php?op=finish&was=$was&who=$row[acctid]");
addnav("Nein","forest.php?op=weiter&was=$was");
addnav("Ich will was ganz Anderes!","forest.php");
}

}


}
else if ($HTTP_GET_VARS['op']=="finish"){
$sql = "SELECT name FROM accounts WHERE acctid=\"$HTTP_GET_VARS[who]\"";
                $result = db_query($sql) or die(db_error(LINK));
                $row = db_fetch_assoc($result);
                
if ($HTTP_GET_VARS['was']=="scheibe") {
	$was=" Zielscheibe"; 
	$tpl_id = 'zielsch';
	$item['tpl_gems']=$dart_cost; 
	$item['tpl_name']="Zielscheibe";
	$item['tpl_description']="Auf der Scheibe befindet sich ein Bild von ".($row[name]).""; 
}
if ($HTTP_GET_VARS['was']=="sack") {
	$was="n Sandsack"; 
	$tpl_id='sandsack';
	$item['tpl_gems']=$sack_cost;
	$item['tpl_name']="Sandsack";
	$item['tpl_description']="Auf dem Sack wurde ein Bild von ".($row[name])." aufgen�ht"; 
}
if ($HTTP_GET_VARS['was']=="puppe") {
	$was=" Strohpuppe";
	$tpl_id='strpuppe'; 
	$item['tpl_gems']=$dummy_cost; 
	$item['tpl_name']="Strohpuppe";
	$item['tpl_description']="Die Puppe hat t�uschende �hnlichkeit mit ".($row[name]).""; 
}

$session['user']['gems']-=$item['tpl_gems'];
$name=$session['user']['acctid'];

output ("Der Mann streicht deine Edelsteine ein und macht sich an die Arbeit. Nach einiger Zeit kommt er wieder und gibt dir deine".($was).". Sieht tats�chlich so aus wie ".($row[name])."!`n`nDu klemmst dir das Meisterwerk unter den Arm und gehst deines Weges.`nVergiss nicht das gut St�ck in deinem Haus einzulagern damit es nicht verloren geht!");
$session['user']['specialinc']="";

$item['tpl_description'] = 'Zum �ben. '.$item['tpl_description'];

item_add($name, $tpl_id, true, $item);

}
else if ($HTTP_GET_VARS['op']=="weg"){
    output("`n`QDu beschlie�t so etwas nicht n�tig zu haben und verl�sst die H�tte.`0");
    $session['user']['specialinc']="";
}
?>
