<?php
require_once "common.php";

// Eine wei�e Mauer zum Beschmieren, hier k�nnen Spr�che und �hnliches angebracht werden.
// Erfordert : Textabschnitt aus "market.php" (gekennzeichnet durch "Mauer")
//
// by Maris (Maraxxus@gmx.de)

$wallchangetime=getsetting("wallchangetime","300");
$time=getsetting("wall_chgtime","0");
$oldtime=(strtotime($time));
$acttime=(strtotime(date("H:i:s")));
$newtime=$acttime-$oldtime;

if ($newtime>$wallchangetime) {
if ($_GET[op]==write)
{
savesetting("wall_author",addslashes($session['user']['login']));
savesetting("wall_chgtime",date("Y-m-d H:i:s"));

$number=e_rand(1,20);

switch($number)
{
  case 1 :
  $message=$session['user']['login']." ist ".($session[user][sex]?"die":"der")." Gr��te!";
  break;
  case 2 :
  $message=$session['user']['login']." zeigts Euch allen!";
  break;
  case 3 :
  $message=$session['user']['login']." herrscht hier!";
  break;
  case 4 :
  $message=$session['user']['login']." war hier!";
  break;
  case 5 :
  $message=$session['user']['login']." l�sst ".($session[user][sex]?"M�nner":"Frauen")."herzen h�her schlagen!";
  break;
  case 6 :
  $message=$session['user']['login']." hat hier das Sagen!";
  break;
  case 7 :
  $message=$session['user']['login']." tritt Euch allen in den Hintern!";
  break;
  case 8 :
  $message=$session['user']['login']." - von Freunden begehrt, von Feinden gef�rchtet!";
  break;
  case 9 :
  $message=$session['user']['login']." ist ".($session[user][sex]?"die":"der")." Sch�nste im ganzen Dorf!";
  break;
  case 10 :
  $message=$session['user']['login']." ist jedermans Liebling!";
  break;
  case 11 :
  $message=$session['user']['login']." ist gottgleich!";
  break;
  case 12 :
  $message=$session['user']['login']." l�sst die Erde erbeben!";
  break;
  case 13 :
  $message=$session['user']['login']." lehrt Euch das F�rchten!";
  break;
  case 14 :
  $message=$session['user']['login']." ist Euch �berlegen!";
  break;
  case 15 :
  $message=$session['user']['login']." t�tet Drachen wie kein Anderer!";
  break;
  case 16 :
  $message=$session['user']['login']." ist der Inbegriff von Eleganz und Sch�nheit!";
  break;
  case 17 :
  $message=$session['user']['login']." l�sst andere ".($session[user][sex]?"Frauen":"M�nner")." vor Neid erblassen!";
  break;
  case 18 :
  $message=$session['user']['login']." ist sch�n und klug!";
  break;
  case 19 :
  $message=$session['user']['login']." kennt keine Furcht!";
  break;
  case 20 :
  $message=$session['user']['login']." �berzeugt durch Witz und Charme!";
  break;
}
savesetting("wall_msg",$message);
}

if ($_GET[op]==change)
{
savesetting("wall_chgtime",date("Y-m-d H:i:s"));
$author = getsetting("wall_author","0");

$number=e_rand(1,20);

$sql = "SELECT sex FROM accounts WHERE login='".addslashes($author)."'";
$result = db_query($sql) or die(db_error(LINK));
$row = db_fetch_assoc($result);

switch($number)
{
  case 1 :
  $message=" ist ".($row[sex]?"die":"der")." Schw�chste!";
  break;
  case 2 :
  $message=" kann nix!";
  break;
  case 3 :
  $message=" isst aus der M�lltonne!";
  break;
  case 4 :
  $message=" nervt gewaltig!";
  break;
  case 5 :
  $message=" bringt ".($row[sex]?"M�nner":"Frauen")." zum fl�chten!";
  break;
  case 6 :
  $message=" hat hier nichts zu sagen!";
  break;
  case 7 :
  $message=" leckt Euch allen den Hintern!";
  break;
  case 8 :
  $message=" stinkt wie ein Iltis!";
  break;
  case 9 :
  $message=" ist ".($row[sex]?"die":"der")." H�sslichste im ganzen Dorf!";
  break;
  case 10 :
  $message=" ist dumm wie ein St�ck Brot!";
  break;
  case 11 :
  $message=" vertr�gt kein Ale!";
  break;
  case 12 :
  $message=" ist ein Bauerntrampel!";
  break;
  case 13 :
  $message=" ist der Witz des Dorfes!";
  break;
  case 14 :
  $message=" k�mpft wie ein ".($row[sex]?"Kind":"M�dchen")."!";
  break;
  case 15 :
  $message=" ist dick und faul!";
  break;
  case 16 :
  $message=" - am Liebsten sehen wir ".($row[sex]?"sie":"ihn")." von hinten!";
  break;
  case 17 :
  $message=" hat ja gar keine Ahnung!";
  break;
  case 18 :
  $message=" bedeutet : gro�er Kopf und wenig Verstand!";
  break;
  case 19 :
  $message=" - was will man bei solch einem Namen auch schon erwarten?";
  break;
  case 20 :
  $message=" liegt gerade besoffen hinter dieser Mauer!";
  break;
}
$message=$author.$message;
savesetting("wall_msg",$message);
}
redirect("market.php");
}
else redirect("market.php?op=toolate");
?>
