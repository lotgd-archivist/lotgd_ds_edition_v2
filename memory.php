<?php
// Memory
// by Maris (Maraxxus@gmx.de)
// For Tara - we shall meet again

require_once "common.php";

page_header("Memory");
output("`c`b`&Memory V1.3`0`b`c`n`n");

   $bet         = 200; // Einsatz
   $step_base   = 10; // Bertrag für das schwierige Spiel um den der Gewinn pro falschem Versuch reduziert wird
   $easy_mod    =  2; // Multiplikator für Abzüge beim leichten Spiel
   $bonus       = 50; // Punkte für Treffer
   $boardsize   = 64; // Größe des Brettes
   $hintcost    = 100; // Kosten für Hinweis
   $award       = 500; // Bonuszahlung für Sieg
   $award_multi = 2;    // Multiplikator für den Bonus bei schwierigem Spiel

// Leeres Brett mit Karten paarweise füllen
function fillboard($size){
  for ($i=1;$i<=$size;$i++){
$board[$i]=$i;
$board[$i+$size]=$i;
$board[$i+2*$size]=$i;
$board[$i+3*$size]=$i;
}
return($board);
}

// Leeres Brett mit Karten im 4-er Satz füllen
function fillboard2($size){
  for ($i=1;$i<=$size;$i++){
$board[$i]=$i;
$board[$i+$size]=$i;
}
return($board);
}

// Brett aus Sicht des Spielers
function filluserboard($size){
  for ($i=1;$i<=$size;$i++){
$board[$i]=0;
$board[$i+$size]=0;
}
return($board);
}

// Tauscht die Positionen von 2 Karten
function exchange($board,$pos1,$pos2){
$copy = $board[$pos1];
$board[$pos1]=$board[$pos2];
$board[$pos2]=$copy;
return($board);
}

// Mischt das Brett gut durch
function shuffle_board($board,$size){
  for ($i=1;$i<=100;$i++){
    $pos1=e_rand(1,$size);
    $pos2=e_rand(1,$size);
  $board=exchange($board,$pos1,$pos2);
  }
return($board);
}

// Lösung zeigen
function showboard($board,$size){
for ($i=1;$i<=$size;$i++){
  $file=$board[$i].".jpg";
output("<img src='images/memory/".$file."'width='50' height='50' border='0'></a>",true);
if (($i % sqrt($size))==0) { output("`n"); }
}
}

// Hinweis ermitteln
function hintboard($type,$size,$userboard,$type,$prize){
$hintboard=filluserboard($size*0.5);
$file="back.jpg";
$uboard=serialize($userboard);
$hboard=serialize($hintboard);
$prize-=100;
for ($i=1;$i<=$size;$i++){
output("<a href=\"memory.php?op=hint2&userboard=$uboard&type=$type&pick=$i&prize=$prize\"><img src='images/memory/".$file."' width='50' height='50' border='0'</a>",true);
addnav("","memory.php?op=hint2&userboard=$uboard&type=$type&pick=$i&prize=$prize");

if (($i % sqrt($size))==0) { output("`n"); }
}
}

// Hinweis zeigen
function showhintboard($hintboard,$board,$type,$size,$userboard,$type,$prize){
$file="back.jpg";
$uboard=serialize($userboard);
$hboard=serialize($hintboard);
for ($i=1;$i<=$size;$i++){
 if ($hintboard[$i]==0) { $file="back.jpg"; }
  else
  if ($hintboard[$i]==1) { $file=$board[$i].".jpg"; }
  else
  if ($hintboard[$i]==2) { $file="blanc.jpg"; }

output("<img src='images/memory/".$file."'width='50' height='50' border='0'></a>",true);
if (($i % sqrt($size))==0) { output("`n"); }
}
}

// Brett aus Sicht des Spielers zeigen
function showuserboard($board,$userboard,$size,$type,$turn,$pick1,$pick2,$prize){
  $turn++;
$uboard=serialize($userboard);
for ($i=1;$i<=$size;$i++){
  if ($userboard[$i]==0) { $file="back.jpg"; }
  else
  if ($userboard[$i]==1) { $file=$board[$i].".jpg"; }
  else
  if ($userboard[$i]==2) { $file="blanc.jpg"; }

if ($turn==1)
{
if ($userboard[$i]==0) {
output("<a href=\"memory.php?op=play&pick1=$i&pick2=0&userboard=$uboard&type=$type&turn=$turn&prize=$prize\"><img src='images/memory/".$file."' width='50' height='50' border='0'</a>",true);
addnav("","memory.php?op=play&pick1=$i&pick2=0&userboard=$uboard&type=$type&turn=$turn&prize=$prize");
}
else
{
output("<img src='images/memory/".$file."'width='50' height='50' border='0'></a>",true);
}
}

if ($turn==2)
{
if ($userboard[$i]==0) {
output("<a href=\"memory.php?op=play&pick1=$pick1&pick2=$i&userboard=$uboard&type=$type&turn=$turn&prize=$prize\"><img src='images/memory/".$file."' width='50' height='50' border='0'</a>",true);
addnav("","memory.php?op=play&pick1=$pick1&pick2=$i&userboard=$uboard&type=$type&turn=$turn&prize=$prize");
}
else
{
output("<img src='images/memory/".$file."'width='50' height='50' border='0'></a>",true);
}
}

if ($turn==3)
{
output("<img src='images/memory/".$file."'width='50' height='50' border='0'></a>",true);
}

if ($i % (sqrt($size))==0) { output("`n"); }
}
}

if ($_GET[op]==''){
       addnav("Memory");
       output('Du begibst dich in eine der dunklen Ecken der Schenke, wo sich ein dürrer, alter Mann aufhält. Auf dem Tisch vor sich hat er viele kleine Karten liegen, manche offen, manche verdeckt.`n`n
             ',true);
       if ($session[user][gold]<$bet){
          output("Der Alte spricht dich an:`2\"`9Hier kannst du dir mit etwas Köpfchen ein paar schnelle Goldmünzen verdienen. Allerdings kostet der Versuch ".$bet." Gold. Soviel hast du wohl nicht dabei!`2\".");
       }
       else{
         output("Der Alte spricht dich an:`2\"`9Hier kannst du um ein paar Goldstücke spielen.`2\"");
         addnav("Einfaches Spiel spielen","memory.php?op=new&type=1");
         addnav("Schwieriges Spiel spielen","memory.php?op=new&type=2");
       }
   addnav("Spielanleitung","memory.php?op=rules");
   addnav("Zurück");
   addnav("Zurück zur Schenke","memory.php?op=leave");

}elseif($_GET[op]=='new'){
$type=$_GET['type'];

$prize=$bet;
if ($type==1)
{
$board=fillboard($boardsize*0.25);
}
else
{
$board=fillboard2($boardsize*0.5);
}
$session['user']['gold']-=$bet;
$userboard=filluserboard($boardsize*0.5);
$board=shuffle_board($board,$boardsize);
$board=serialize($board);
$userboard=serialize($userboard);
$sql = "UPDATE account_extra_info SET memory='$board' WHERE acctid=".$session[user][acctid]."";
db_query($sql);
redirect("memory.php?op=play&userboard=$userboard&type=$type&turn=0&prize=$prize");

}elseif($_GET[op]=='play'){
$sql = "SELECT memory FROM account_extra_info WHERE acctid=".$session[user][acctid]."";
$result = db_query($sql) or die(db_error(LINK));
$rowb = db_fetch_assoc($result);
$board=unserialize($rowb['memory']);
$turn=$_GET['turn'];
$prize=$_GET['prize'];
if ($turn>3) $turn=0;
$type=$_GET['type'];
$userboard=unserialize($_GET['userboard']);

$pick=0;
$pick1=0;
$pick2=0;
$pick=$_GET['pick'];
$pick1=$_GET['pick1'];
$pick2=$_GET['pick2'];

output("<font size=+1>`^Gewinn : ".$prize." Gold</font>`n`0",true);

if ($turn==0)
  {
  output("`&1. Karte wählen `0");
  $userboard=serialize($userboard);
if ($prize>=$hintcost) {
  output("<a href=\"memory.php?op=hint&userboard=$userboard&type=$type&prize=$prize\">Hinweis (".$hintcost." Gold)</a>",true);
  addnav("","memory.php?op=hint&userboard=$userboard&type=$type&prize=$prize");
}
  output("`n");
  $userboard=unserialize($userboard);
  }

if ($turn==1)
  {
  output("`&2. Karte wählen`0`n");
  $userboard[$pick1]=1;
  }

if ($turn==2)
  {
  $userboard[$pick1]=1;
  $userboard[$pick2]=1;
  if ($board[$pick1]==$board[$pick2])
            {
               output("`@Treffer!`0`n");
               $prize+=$bonus;
            }
  else
            {
               output("`4Keine Übereinstimmung!`0`n");
               if ($type==2)
               { $prize-=$step_base; }
                else { $prize-=$step_base*$easy_mod; }
            }

  }
  
if ($turn==2)
{
$userboard=serialize($userboard);
output("<a href=\"memory.php?op=proceed&userboard=$userboard&type=$type&pick1=$pick1&pick2=$pick2&prize=$prize\"><font size=+1>Weiter</font></a>`n",true);
addnav("","memory.php?op=proceed&userboard=$userboard&type=$type&pick1=$pick1&pick2=$pick2&prize=$prize");
$userboard=unserialize($userboard);
}

showuserboard($board,$userboard,$boardsize,$type,$turn,$pick1,$pick2,$prize);

addnav("Zurück zur Schenke","memory.php?op=leave");

// Hinweise
}elseif($_GET[op]=='hint'){
$prize=$_GET['prize'];
$type=$_GET['type'];
$userboard=unserialize($_GET['userboard']);
output("<font size=+1>`^Gewinn : ".$prize." Gold</font>`n`0",true);
output("`&Karte wählen, von der aus als Zentrum alle Karten bis zum Rand aufgedeckt werden.`0`n");
hintboard($type,$boardsize,$userboard,$type,$prize);

// Hinweise werden gezeigt
}elseif($_GET[op]=='hint2'){
$sql = "SELECT memory FROM account_extra_info WHERE acctid=".$session[user][acctid]."";
$result = db_query($sql) or die(db_error(LINK));
$rowb = db_fetch_assoc($result);
$board=unserialize($rowb['memory']);
$type=$_GET['type'];
$prize=$_GET['prize'];
$pick=$_GET['pick'];
$userboard=unserialize($_GET['userboard']);
$hintboard=filluserboard($size*0.5);
output("<font size=+1>`^Gewinn : ".$prize." Gold</font>`n`0",true);

// Angrenzende Karten berechnen
$length=sqrt($boardsize);
$horizontal=floor(($pick-1)/$length)+1;
$vertical=$pick % $length;
$starth=(($horizontal-1)*$length)+1;

for ($i=$starth;$i<$starth+$length;$i++){
$hintboard[$i]=1;
}
for ($i=$vertical;$i<=$boardsize;$i+=$length){
$hintboard[$i]=1;
}
$userboard=serialize($userboard);
output("<a href=\"memory.php?op=play&userboard=$userboard&type=$type&turn=0&prize=$prize\"><font size=+1>Weiter</font></a>`n",true);
addnav("","memory.php?op=play&userboard=$userboard&type=$type&turn=0&prize=$prize");
$userboard=unserialize($userboard);
showhintboard($hintboard,$board,$type,$boardsize,$userboard,$type,$prize);

// Aufgedeckte Karten zurücksetzen und nächste Runde beginnen
}elseif($_GET[op]=='proceed'){
$sql = "SELECT memory FROM account_extra_info WHERE acctid=".$session[user][acctid]."";
$result = db_query($sql) or die(db_error(LINK));
$rowb = db_fetch_assoc($result);
$board=unserialize($rowb['memory']);
$userboard=unserialize($_GET['userboard']);
$prize=$_GET['prize'];
$pick1=$_GET['pick1'];
$pick2=$_GET['pick2'];
$type=$_GET['type'];

If ($board[$pick1]==$board[$pick2])
{
$userboard[$pick1]=2;
$userboard[$pick2]=2;
} else
{
$userboard[$pick1]=0;
$userboard[$pick2]=0;
}

$check=0;
for ($i=1;$i<=$boardsize;$i++){
  if ($userboard[$i]==2) { $check++; }
}
if ($check>=$boardsize)
{
  redirect("memory.php?op=win&type=$type&prize=$prize");
}

if ($prize<0)
{
  redirect("memory.php?op=lose&type=$type");
}

$userboard=serialize($userboard);
redirect("memory.php?op=play&userboard=$userboard&type=$type&turn=0&prize=$prize");

}elseif($_GET[op]=='win'){
$type=$_GET['type'];
output("<font size=+1>`^Du hast gewonnen!`n`n`0",true);
output("<font size=+1>`&Dein Guthaben : ".$_GET['prize']." Goldmünzen!</font>`n`0",true);
// Bonus für den Sieg
if ($type==1)
{ $prize=$_GET['prize']+$award;
output("<font size=+1>`&Bonus für das leichte Spiel : ".$award." Goldmünzen!</font>`n`0",true);
}
else
{ $prize=$_GET['prize']+($award*$award_multi);
output("<font size=+1>`&Bonus für das schwierige Spiel : ".($award*$award_multi)." Goldmünzen!</font>`n`0",true);
}

output("<font size=+1>`n`^Dein Gesamtgewinn beträgt ".$prize." Goldmünzen!</font>`n`0",true);
$session['user']['gold']+=$prize;
$sql = "UPDATE account_extra_info SET memory=' ' WHERE acctid=".$session[user][acctid]."";
db_query($sql);
addnav("Zurück zur Schenke","memory.php?op=leave");

}elseif($_GET[op]=='lose'){
$type=$_GET['type'];
output("<font size=+1>`4Du hast verloren und dein Einsatz ist weg!</font>`n`0",true);
output("`&Die Lösung : `n`n`0");
$sql = "SELECT memory FROM account_extra_info WHERE acctid=".$session[user][acctid]."";
$result = db_query($sql) or die(db_error(LINK));
$rowb = db_fetch_assoc($result);
$board=unserialize($rowb['memory']);
showboard($board,$boardsize);
$sql = "UPDATE account_extra_info SET memory=' ' WHERE acctid=".$session[user][acctid]."";
db_query($sql);
addnav("Zurück zur Schenke","memory.php?op=leave");

}elseif($_GET[op]=='leave'){
$sql = "UPDATE account_extra_info SET memory=' ' WHERE acctid=".$session[user][acctid]."";
db_query($sql);
redirect("inn.php");

}elseif($_GET[op]=='rules'){
   addnav("Zurück","memory.php");
   output("`c`b`&Anleitung für Memory `0`b`c`n`n");
   output("`Q `2Spielverlauf `Q`n`n
           `7Ziel des Spiels ist alle Karten durch das Finden von Paaren aufzudecken.`n
           Dazu wählst du einfach nacheinander 2 der verdeckten Karten. Hast du eine Übereinstimmung gefunden verschwinden beide Karten, hast du 2 unterschiedliche Karten gewählt werden diese wieder verdeckt und dir wird ein Fehlversuch angerechnet.`n
           Es gibt 2 Spielvarianten.`nBeim einfachen Spiel sind von jeder Kartensorte 4 Stück vorhanden, beim Schwierigen nur jeweils 2.`n
           In keiner der beiden Spielmodi werden die Karten zu Beginn des Spiels gezeigt!`n
           Dein Einsatz beträgt ".$bet." Goldmünzen. Beim schwierigen Spiel wird dieser Betrag pro Fehlversuch um ".$step_base." Gold reduziert, beim Einfachen sogar um ".($step_base*$easy_mod).".`n
           Für ".$hintcost." Gold kannst du dir einen Hinweis geben lassen, der dir, beginnend von einer Karte deiner Wahl alle weiteren Karten bis zum Rand des Spielfeldes kurz aufdeckt.`n
           Für jedes gefundene Paar werden dir ".$bonus." Gold gutgeschrieben.`n`n
           Sollte dein Guthaben negativ werden ist das Spiel vorbei. Der Alte Mann sieht dann freundlicherweise davon ab dir noch mehr Gold aus der Tasche zu ziehen.`nSobald du alle Paare gefunden hast wird dir dein Guthaben ausgezahlt, zuzüglich ".$award." Gold als Bonus für das leichte Spiel, bzw ".$award*$award_multi." für das schwierige Spiel.`n`n
         ");
}
page_footer();
?>
