<?php

/*###############################################################
Skript : dragonmind.php
Ersteller: deZent / draKarr von www.plueschdrache.de
Edit by: Hadriel von http://www.hadrielnet.ch
Version: 0.15
Beschreibung : siehe Regeln im Spiel ;-)
Gründe für den chaoscode : Eigentlich sollte das Skript nur ein kleiner $_POST[]-Test werden.. Am Ende wurds halt Mastermind.
                           --> also nicht motzen--> besser machen
Install:
1. dragonmind.php in root Ordner kopieren
2. neuen Ordner "dragonmind" im "images" Ordner erstellen.
3. checken ob die Datei "transparentpixel.gif" schon im templates Ordner ist.
3.1 JA --> gut so
3.2 Nein --> also reinkopieren

in "inn.php"
------------
addnav("DragonMind","dragonmind.php");

DATENBANK:
----------
Ich habe absichtlich auf $session[user] Felder weitesgehend verzichtet.
Darum nutze ich das Feld "pqtemp" , das bereits in diversen Skripten angezogen wird.

Falls noch nicht vorhanden:
ALTER TABLE `accounts` ADD `pqtemp` TEXT NOT NULL
Bin mir nicht sicher ob das Feld orginalerweise vom Typ "TEXT" war. Hier würde eigentlich auch VARCHAR 255 locker reichen.

Viel Spaß,
deZent

P.S. WER WÜRDE MAL EIN PAAR EINTRÄGE FÜR DIE TAUNTS TABELLE BEI ANPERA POSTEN!?! *Verdammt!*
*/

// Modified by Maris (Maraxxus@gmx.de)

require_once "common.php";
page_header("Dragonmind");
output("`c`b`&Dragonmind V0.15`0`b`c`n`n");

   $__anzahl_versuche = 10;    // wieviele Versuche um den Code zu knacken
   $__anzahl_farben   = 12;    // wieviele der 10 Farben?
   $__einsatz         = 200;   // Einsatz Gold
   $__gewinn          = 400;   // Gewinn Gold    achte darauf, dass der Gewinn nicht zu extrem wird, da es Programme gibt, die
                               // Mastermind in 5 Zügen lösen. Somit cheat-Gefahr... 
   $farbe[0][farbe]="#800000";
   $farbe[0][name]="dunkelrot";

   $farbe[1][farbe]="#008000";
   $farbe[1][name]="grün";

   $farbe[2][farbe]="#E6E629";
   $farbe[2][name]="gelb";

   $farbe[3][farbe]="#0000F0";
   $farbe[3][name]="blau";

   $farbe[4][farbe]="#800080";
   $farbe[4][name]="lila";

   $farbe[5][farbe]="#FF0000";
   $farbe[5][name]="rot";

   $farbe[6][farbe]="#14EAD3";
   $farbe[6][name]="türkis";

   $farbe[7][farbe]="#F26A10";
   $farbe[7][name]="orange";

   $farbe[8][farbe]="#00A8FF";
   $farbe[8][name]="hellblau";

   $farbe[9][farbe]="#FFFFFF";
   $farbe[9][name]="weiß";
   
   $farbe[10][farbe]="#100000";
   $farbe[10][name]="schwarz";
   
   $farbe[11][farbe]="#B0A0B0";
   $farbe[11][name]="grau";

if ($_GET[op]==''){
       addnav("Dragonmind");
       output('Du betrittst einen etwas abgeschiedenen Bereich der Kneipe,`n
              In feurig roten Lettern steht an der Wand `n  `n
              <font size=+1>`b`$D`4ragon`$M`4ind`b </font>`n`n`7 geschrieben.`n`n
             ',true);
       if ($session[user][gold]<$__einsatz){
          output("Der Barkeeper raunzt dich an:`7\"`9Hier kannst du um ein paar Goldstücke spielen. Der Spieleinsatz ist jedoch 200 Gold. Soviel Gold hast du wohl nicht! *HARHAR*`7\".");
       }
       else{
         output("Der Barkeeper raunzt dich an:`7\"`9Hier kannst du um ein paar Goldstücke spielen.`7\"");
         addnav("Einfaches Spiel spielen","dragonmind.php?op=new&type=1");
         addnav("Schwieriges Spiel spielen","dragonmind.php?op=new&type=2");
       }
   addnav("Regeln","dragonmind.php?op=regeln");
   addnav("Zurück");
   addnav("Zur Kneipe","inn.php");
}elseif($_GET[op]=='new'){

$type=$_GET[type];
if ($type==1)
{
$__anzahl_farben   = 10;    // wieviele der 12 Farben?
$__gewinn          = 400;
} else
if ($type==2)
{
$__anzahl_farben   = 12;    // wieviele der 12 Farben?
$__gewinn          = 600;
}

   $session[user][gold]-=$__einsatz;  
    addnav("Du hast noch:");
     addnav("$__anzahl_versuche Versuche");

   // farbkombi festlegen
   $zuf=array();
   for ($i=0;$i<4;$i++){
      while(true){
        $check=e_rand(0,($__anzahl_farben-1));
        if (array_search($check,$zuf)===false){
           $zuf[$i]=$check;
           break;
        }
      }
      $zufall[$i][farbe]=$farbe[$check][farbe];
      $zufall[$i][name]=$farbe[$check][name];
   }
   $session[user][pqtemp]=serialize($zufall);
   output("`7 Wähle Deine Farben:`n`n");
   output("<form action='dragonmind.php?op=play&type=$type' method='post' name='f1'>",true);
   for ($i=0;$i<4;$i++){
       output("<select name='auswahl[".$i."]'>",true);
if($__anzahl_farben<12) $sel=$__anzahl_farben;
            if($__anzahl_farben>=12) $sel=count($farbe);
              for ($j=0;$j<($sel);$j++){
          output("<option value='".$farbe[$j][farbe]."' style='background-color:".$farbe[$j][farbe]."'>".$farbe[$j][name]."</option>",true);
        }
       output("</select>",true);
   }

   output("<br><br><input type='submit' name='rate' value='Tipp abgeben'>",true);
   output("</form>",true);
   addnav("","dragonmind.php?op=play&type=$type");
}elseif($_GET[op]=='play'){
   $type=$_GET[type];
if ($type==1)
{
$__anzahl_farben   = 10;    // wieviele der 10 Farben?
$__gewinn          = 400;
} else
if ($type==2)
{
$__anzahl_farben   = 12;    // wieviele der 10 Farben?
$__gewinn          = 600;
}
   // erstmal die such farben wieder auslesen
   $farben=unserialize($session[user][pqtemp]);
   // mal schauen ob er was erraten hat.
   $rs=0; // richtige stelle  + richtige Farbe
   $rf=0; // richtige farbe
   // check ob richtige farbe an richtiger stelle
   for ($i=0;$i<count($farben);$i++){
      for ($j=0;$j<count($farben);$j++){
         if ($_POST[auswahl][$i]==$farben[$j][farbe]){
           if ($i==$j){
             $rs++;
             $farben[$j][farbe]='';
           }
         }
      }
   }
   // richtige farbe aber falsche stelle
   for ($i=0;$i<count($farben);$i++){
      for ($j=0;$j<count($farben);$j++){
         if ($_POST[auswahl][$i]==$farben[$j][farbe]){
             $rf++;
             $farben[$j][farbe]='';
          }
       }
    }
   $farben=unserialize($session[user][pqtemp]);
   // Farbpunkte für aktuellen rateversuch zusammenbauen
   for ($i=0;$i<$rs;$i++){
     $bilder_aktuell.='<img src="./images/dragonmind/gruen.gif" alt="Richtige Farbe an richtiger Stelle">';
   }
   for ($i=0;$i<$rf;$i++){
     $bilder_aktuell.='<img src="./images/dragonmind/rot.gif" alt="Richtige Farbe an falscher Stelle">';
   }

   if ($rs==4){
      $gewonnen=true;
   }  // player hat gewonnen
   addnav("","dragonmind.php?op=play&type=$type");
   output("<form action='dragonmind.php?op=play&type=$type' method='post' name='f1'>",true);
   if (count($_POST[versuche])>=$__anzahl_versuche-1 || $gewonnen){
   output("<table>
               <tr><td colspan='5' align='center' style='background-color:#AFDB02;color:#000000;font-weight:bold;'>LÖSUNG</td></tr>
               <tr>
                <td bgcolor=".$farben[0][farbe].">
                <img src='./templates/transparentpixel.gif' width='83' height='10'>
                </td>
                <td bgcolor=".$farben[1][farbe].">
                <img src='./templates/transparentpixel.gif' width='83' height='10'>
                </td>
                <td bgcolor=".$farben[2][farbe].">
                <img src='./templates/transparentpixel.gif' width='83' height='10'>
                </td>
                <td bgcolor=".$farben[3][farbe].">
                <img src='./templates/transparentpixel.gif' width='83' height='10'>
                </td>
                <td>
                 Lösung
                </td>
              </tr>",true);
   }
   else{
     output("<table>",true);
   }
    output("<tr>
                  <td style='background-color:#AFDB02;color:#000000;font-weight:bold;' align='center'>
                   Farbe 1
                  </td>
                  <td style='background-color:#AFDB02;color:#000000;font-weight:bold;' align='center'>
                   Farbe 2
                  </td>
                  <td style='background-color:#AFDB02;color:#000000;font-weight:bold;' align='center'>
                   Farbe 3
                  </td>
                  <td style='background-color:#AFDB02;color:#000000;font-weight:bold;' align='center'>
                   Farbe 4
                  </td>
                  <td style='background-color:#AFDB02;color:#000000;font-weight:bold;' align='center'>
                    Info
                  </td>
                </tr>",true);
   for ($i=0;$i<count($_POST[versuche]);$i++){
      // letzte auswertung auslesen und Bilder -code schreiben.
      $auswertung = explode("-",$_POST[tip][$i]);
      $bilder='';
      for ($k=0;$k<$auswertung[0];$k++){
        $bilder.='<img src="./images/dragonmind/gruen.gif" alt="Richtige Farbe an richtiger Stelle">';
      }
      for ($k=0;$k<$auswertung[1];$k++){
        $bilder.='<img src="./images/dragonmind/rot.gif" alt="Richtige Farbe an falscher Stelle">';
      }
      output("<tr>
                  <td bgcolor=".$_POST[versuche][$i][0].">
                  &nbsp;
                  </td>
                  <td bgcolor=".$_POST[versuche][$i][1].">
                  </td>
                  <td bgcolor=".$_POST[versuche][$i][2].">
                  </td>
                  <td bgcolor=".$_POST[versuche][$i][3].">
                  </td>
                  <td>
                    $bilder
                  </td>
                </tr>",true);
       for ($k=0;$k<count($_POST[versuche][$i]);$k++){
         output("<input type='hidden' name='versuche[$i][$k]' value='".$_POST[versuche][$i][$k]."'> ",true);
       }
       output("<input type='hidden' name='tip[".$i."]' value='".$_POST[tip][$i]."'> ",true);
   }
      output("<tr>
                <td bgcolor=".$_POST[auswahl][0].">
                &nbsp;
                </td>
                <td bgcolor=".$_POST[auswahl][1].">
                </td>
                <td bgcolor=".$_POST[auswahl][2].">
                </td>
                <td bgcolor=".$_POST[auswahl][3].">
                </td>
                <td>
                $bilder_aktuell
                </td>
              </tr>",true);

       for ($k=0;$k<count($_POST[auswahl]);$k++){
         output("<input type='hidden' name='versuche[".$i."][$k]' value='".$_POST[auswahl][$k]."'>",true);
       }
       output("<input type='hidden' name='tip[".$i."]' value='".$rs."-".$rf."'> ",true);

   if ((count($_POST[versuche])<$__anzahl_versuche-1) && $gewonnen!=true){
         output("<tr>",true);

         for ($i=0;$i<4;$i++){
             output("<td><select name='auswahl[".$i."]'>",true);
            if($__anzahl_farben<12) $sel=$__anzahl_farben;
            if($__anzahl_farben>=12) $sel=count($farbe);
              for ($j=0;$j<($sel);$j++){
                output("<option value='".$farbe[$j][farbe]."' style='background-color:".$farbe[$j][farbe]."' ".($_POST[auswahl][$i] == $farbe[$j][farbe]?" selected":"").">
                        ".$farbe[$j][name]."
                        </option>",true);
              }
             output("</select></td>",true);
         }
         output("</tr></table>",true);
      output("<br><br><input type='submit' name='rate' value='Tipp abgeben'>",true);
      output("</form>",true);
      $versuche=$__anzahl_versuche - count($_POST[versuche]) -1;
      if ($versuche!=0){
         addnav("Du hast noch");
         addnav("$versuche Versuche");
      }
   }
   else{ // fertig
     output("</table></form>",true);
     //schauen ob gewonnen oder Ende
     if ($gewonnen){
       
       $session[user][gold]+=$__gewinn +$__einsatz;
       redirect("dragonmind.php?op=gewonnen");
     }
     else{
        output("<h1>DU HAST VERLOREN</h1>",true);
     }
     if ($session[user][gold]>=$__einsatz) addnav("Erneut spielen","dragonmind.php");
     addnav("Zurück");
     addnav("Zur Kneipe","inn.php");
   }

}elseif($_GET[op]=='gewonnen'){
output("<h1>DU HAST GEWONNEN</h1>",true);
output("`$ Stolz gehst du zurück zur Kneipe");
addnav("Zurück");
addnav("Zur Kneipe","inn.php");

}elseif($_GET[op]=='regeln'){
   addnav("Zurück","dragonmind.php");
   output("`c`b`&Dragonmind Regeln `0`b`c`n`n");
   output("`Q ### `2allgemeiner Ablauf des Spiels `Q####`n`n
           `7Das Ziel des Spiels ist das Erraten der Farbkombination, die der Spielführer ausgewählt hat.`n
           Jede Farbe kommt nur EINMAL vor!`n
           Du kannst zu Beginn wählen ob du einfach (10 Farben) oder schwierig (12 Farben) spielen willst.`nDas einfache Spiel bringt dir bei einem Sieg 400 Goldmünzen ein, das Schwierige 600.`n
           Du wählst in den Drop-Down Feldern deine Farbkombination aus und drückst auf 'raten'`n
           Daraufhin erscheint deine Auswahl.`n`n

           Dir wird mitgeteilt ob du richtige Farben gewählt bzw diese auch an der richtigen Stelle plaziert hast.`n`n

           Welche Farben deiner Auswahl richtig gewählt oder richtig plaziert wurden wird nicht verraten!`n`n
           Du hast maximal 10 Versuche die richtige Auswahl zu erraten.`n`n
           P.S.  Bei z.B. 10 Farben gibt es 5040 verschiedene Farbkombinationen... Nur durch probieren wirst du es wohl eher nicht schaffen.`n`n
           www.plueschdrache.de
         ");
}
//show_array($_POST);
page_footer();
?>
