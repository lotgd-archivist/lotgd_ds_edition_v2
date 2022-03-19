<?
require_once "common.php";
page_header("Der Baum des Lebens");
output("`c`2Der `@Baum `2des Lebens`c`n`n");
//--------------------------------------------------------------------------------------------------------
//| Written by:  DeathDragon
//| Version:    1.2 - 06/11/2004
//| Translated by: Beleggrodion
//| Thanks Talisman for help with stupid mistakes :D
//| Revisions by: Odyssey
//| About:  A tree that randomly gives a user something
//| make sure you include the image that comes with the file which can be found at..
//| http://images.google.com/images?q=tree&ie=UTF-8&hl=en
//|
//| SQL: ALTER TABLE `accounts` ADD `treepick` INT( 11 ) UNSIGNED DEFAULT '0' NOT NULL ;
//| in newday add $session['user']['treepick']=0;
//| small modifycations by hadriel @ www.hadrielnet.ch
//--------------------------------------------------------------------------------------------------------

$sql = "SELECT acctid,treepick FROM account_extra_info WHERE acctid=".$session['user']['acctid']."";
$result = db_query($sql) or die(db_error(LINK));
$row = db_fetch_assoc($result);

switch($HTTP_GET_VARS[op])
    {

case "":
output("`c`2Als du so auf ausgetretenen Pfaden läufst, bemerkst du eine Stelle die scheinbar immer von Sonnenlicht umgeben ist.
Deine Neugier kommt wieder einmal zum Vorschein und du läufst dem ausgetretenen Pfad entlang und findest den `@Baum `2des Lebens.
Vor Erfurcht über seine Schönheit fällst du einen Entschluss ...`c");
addnav("Optionen");
addnav("Nimm was vom Baum","treeoflife.php?op=pickfruit");
addnav("Zurück zum Garten","gardens.php");
break;

case "pickfruit":
    if ($row['treepick'] <1){
    output("`n`n`^Du versuchst etwas vom `2`@Baum `2des Lebens zu nehmen und findest....`n`n");
        $rand1 = e_rand(1,16);
        switch ($rand1){
        case 1:
            output("`^das der Baum noch keine reifen Früchte hat!");
            break;
        case 2:
            output("`^ Einen Edelstein!!");
            $session[user][gems]+=1;
            break;
        case 3:
            output("`^ Eine charmante Elfe welche zwischen zwei Ästen feststeckt. Immer bereit jemandem zu Helfen, befreist du die Elfe, und sie ist dafür sehr dankbar. Sie schwingt ihren Stab, und du stellst fest das du besser aussiehst.");
            $session[user][charm]+=2;
            break;
        case 4:
            output("`^Nichts!  Du verfluchst die Vögel und gehst zurück ins Dorf.");
            break;
        case 5:
            output("`^ Eine kleine Tasche voller Gold!!");
            $session[user][gold]+=200;
            break;
        case 6:
            output("`^ 2 Edelsteine!!!");
                        $session[user][gems]+=2;
            break;
            case 7:
            output("`^Eine faulige Frucht fällt vom Baum. Der Hunger überkommt deine Klugheit, du entschliesst dich einen Bissen von der verfaulten Frucht zu nehmen.  Kurz als du beschliesst zurück zu laufen, bemerkst du einen starken Schmerz und du fällst auf den Boden. Und es wird vor deinen Augen Schwarz, du beginnst die Seelen gefallener Krieger zu sehen. Erst jetzt bemerkst du, dass du tot bist!!!");
       $session[user][alive]=0;
       $session[user][hitpoints]=0;
      addnews($session[user][name]."`5 ist gestorben an einer verdorbenen Frucht, beim `2 `@Baum `2des Lebens ");
       addnav("Die Schatten","shades.php");

            break;
            case 8:
            output("`^ Nichts!  Du verfluchst die Eichhörnchen und gehst zurück ins Dorf.");
            break;
            case 9:
            output("`^ 3 Edelsteine!!!");
                        $session[user][gems]+=3;
            break;
            case 10:
            output("`^ Als du auf den Baum klettern willst, fühlst du etwas glitschiges auf deiner Hand.  Um herauszufinden was das ist, schaust du wie wild umher. Schlussendlich schaust du genau mit deinem Gesicht in die Augen einer riesigen Schlange! Das ist das letzte an das du dich erinnern kannst...");
       $session[user][alive]=0;
       $session[user][hitpoints]=0;
      addnews($session[user][name]."`5 wurde gebissen und getötet  von einer Schlange, beim `2 `@Baum `2des Lebens ");
       addnav("Die Schatten","shades.php");
            break;
            case 11:
            output("`^ Nichts! Du verfluchst die Schlangen und gehst zurück ins Dorf");
            break;
            case 12:
            output("`^ 2 Edelsteine!!!");
                        $session[user][gems]+=2;
            break;
        case 13:
        output("`^das  der `@Baum `^beschlossen hat dich für den Kampf zu Segnen!");
        $session[bufflist][865] = array(
          "name"=>"`2Der Segen des `@Baumes",
          "rounds"=>10,
          "wearoff"=>"`2Der `@Baum `2hat dir genug geholfen.",
          "defmod"=>1,
          "atkmod"=>2,
          "roundmsg"=>"`2Der `@Baum `2gibt dir seinen Segen!",
          "activate"=>"defense");
      break;
      case 13:
        output("`^Du bekommst eine Attacke und 10 Lebenspunkte dazu, jedoch verlierst du einen Verteidigungspunkt!");
       $session[user][attack]++;
       $session[user][maxhitpoints]+=10;
       $session[user][defence]--;
      break;
      case 14:
          output("`^Einige Früchte fallen zu Boden.  \"Die Früchte sehen etwas seltsam aus\" ,denkst Du Dir. Du denkst an deinen Hunger und beschliesst, dass es es die Konsequenzen Wert sind!!!");
          $session[user][drunkenness]=66;
          $session[user][turns]-10;
          break;
      case 15:
          output("`^Du beginnst den  `@Baum `2des Lebens `^hochzuklettern, als ein Ast bricht!! Der `@Baum `^beginnt dunkelrot zu glühen! Du fühlst Dich, als hättest du eine schwere Bürde auf deinen Schultern und hast das Gefühl schlechter zu Kämpfen!");
          $session[bufflist][999] = array(
          "name"=>"`4Fluch `7des `@Baumes",
          "rounds"=>10,
          "wearoff"=>"`^Deine Bürde ist verschwunden!",
          "defmod"=>0.7,
          "atkmod"=>0.3,
          "roundmsg"=>"`4Die Bürde erschwert es dir dich zu Verteidigen!",
          "activate"=>"roundstart");
      break;
      case 16:
           output("`^Du erreichst den obersten Ast des Baumes und findest eine Schale voll Gold!");
      $session[user][gold]+=100;
      break;
        }
$sql = "UPDATE account_extra_info SET treepick=1 WHERE acctid = ".$session['user']['acctid'];
db_query($sql) or die(sql_error($sql));
        if ($session[user][alive]==1)        
    addnav("Zurück zum Garten","gardens.php");
    }else{
        output("`@Du beschliesst den andern auch eine Chance zu geben...");
    addnav("Zurück zum Garten","gardens.php");
    }
      
break;

}
page_footer();
?> 
