<?php

// Feuerschrein. Hier kann das Mal des Feuers erworben werden.
// Benötigt : Erweiterung "Die Auserwählten"
//
// by Maris (Maraxxus@gmx.de)

require_once "common.php";
checkday();
page_header("Am Lagerfeuer");

$mark=$session['user']['marks'];
if ($mark>=16) {$mark-=16;}
if ($mark>=8) {$mark-=8;}

if ($HTTP_GET_VARS['op']=="") {
  if ($mark<4) {
output ("`&Du sitzt leicht angetrunken am Lagerfeuer und erfreust dich der guten Stimmung. Plötzlich fällt dein Blick auf die lodernden Flammen, und es scheint so als würden kleine Hände aus Feuer dir zuwinken und dich näher an die Flammen locken wollen. Du sitzt schon sehr nah am Lagerfeuer und spürst die Hitze in deinem Gesicht.`nWas willst du tun ?`n");
addnav("Ignorieren","dorffest.php?op=fire&action=gossip");
addnav("Darauf zugehen","fireshrine.php?op=go1");
} else
{
output ("`&Du setzt dich nah an das Feuer, dessen Hitze dir überhaupt nichts auszumachen scheint, und starrst in die Flammen. Du weißt, was sich hinter ihnen verbirgt und nach sehr kurzer Zeit ziehen dich die vielen kleinen Hände aus Feuer hinein! Dort bist du wieder, am Feuerschrein. Mit dem Mal des Feuers passierst du den Wächter und lässt dich zu einem kurzen Gebet nieder.`n");
addnav("Zurück","dorffest.php?op=fire&action=gossip");
}
}

if ($HTTP_GET_VARS['op']=="go1") {

$sql="INSERT INTO commentary (postdate,section,author,comment) VALUES (now(),'party_fireplace',".$session[user][acctid].",'/me starrt mit einem Mal völlig abwesend in die Flammen.')";
db_query($sql) or die(db_error(LINK));

output ("`&Du erhebst dich von deinem Platz und gehst langsam auf das Lagerfeuer zu. Nun stehst du direkt vor den lodernden Flammen. Die Hitze ist unerträglich und es beginnen sich kleine Bläschen auf deiner Haut zu bilden. Du verlierst einige Lebenspunkte!`nNoch kannst du umkehren. Die Feuerhände strecken sich nach dir aus um dich zu greifen!");
$chance=e_rand(2,7);
$chance=$chance*0.1;
(int)$session['user']['hitpoints']*=$chance;
addnav ("Umkehren","dorffest.php?op=fire&action=gossip");
addnav ("In die Flammen gehen","fireshrine.php?op=shrine");
}
if ($HTTP_GET_VARS['op']=="shrine") {
output ("`&Du stürzt dich in das große Lagerfeuer und ein grelles Licht blendet dich. Wenig später findest du dich an einem komplett anderen Ort wieder. Es ist ein großer Raum aus rotem und schwarzem Stein. Ein kleiner Altar steht am anderen Ende dieses Raumes.`n");
output ("`&Gerade als du dich zu diesem Altar bewegen willst lodern aus dem Boden vor dir Flammen auf und formen eine menschliche Gestalt. Das Wesen greift dich an!");

$badguy = array(
	"creaturename"=>"`4Feuerelementar`0"
	,"creaturelevel"=>$session['user']['level']+e_rand(1,2)
	,"creatureweapon"=>"Lodernde Flammen"
	,"creatureattack"=>$session['user']['attack']*0.8
	,"creaturedefense"=>$session['user']['defence']*0.8
	,"creaturehealth"=>600+$session['user']['dragonkills']*10
	,"diddamage"=>0);

	$session['user']['badguy']=createstring($badguy);
	$atkflux = e_rand(0,$session['user']['dragonkills']*2);
	$defflux = e_rand(0,($session['user']['dragonkills']*2-$atkflux));
	$hpflux = ($session['user']['dragonkills']*2 - ($atkflux+$defflux)) * 5;
	$badguy['creatureattack']+=$atkflux;
	$badguy['creaturedefense']+=$defflux;
	$badguy['creaturehealth']+=$hpflux;
addnav("Kämpfen","fireshrine.php?op=fight");
}

if ($HTTP_GET_VARS['op']=="run")
{
	output("\"`%Keine Chance zu fliehen!`0\"`n");
	$HTTP_GET_VARS['op']="fight";
}

if ($HTTP_GET_VARS['op']=="fight")
{
	$battle=true;
}

if ($battle)
{
	include ("battle.php");
	if ($victory)
	{
		output("`nDu hast das `^".$badguy['creaturename']." geschlagen.");
		$badguy=array();
		$session['user']['badguy']="";
        addnav("Weiter","fireshrine.php?op=goon");
	}

	elseif($defeat)
	{
		output("Du wurdest vom Feuerelementar geröstet!");
		output("`n`4Du bist tot.`n");
		output("Du verlierst 10% deiner Erfahrung und all Dein Gold.`n");
		$session['user']['gold']=0;
		$session['user']['experience']=round($session['user']['experience']*.9,0); $session['user']['alive']=false;
		$session['user']['hitpoints']=0;
		addnews("`@".$session['user']['name']."`t wurde von einem Feuerelementar knusprig braun gebraten.");
        addnav("Tägliche News","news.php");
	}
	else
	{
		fightnav();
	}

}

if ($HTTP_GET_VARS['op']=="goon")
{
output("`&Nachdem du den Wächter bewzungen hast gehst du schnellen Schrittes auf den Altar zu. Es ist ein kleiner `^Feuerschrein`&. Ein Knistern liegt in der Luft als du dich ihm näherst. An der Wand über dem Schrein prangt eine Glyphe. Sie glüht so stark und strahlt eine derartige Hitze aus, dass du meinen könntest, sie sei aus flüssigem Metall!`n");
addnav("Was tust du ?");
addnav("Die Glyphe berühren","fireshrine.php?op=fireshrine");
addnav("Diesen Ort verlassen","dorffest.php?op=fire&action=gossip");
}
if ($_GET[op]=="fireshrine")
{
output ("`&Mit stark pochendem Herzen trittst du an den Schrein und drückt deinen Arm gegen die Glyphe. Ein grauenvoller Schmerz durchzuckt deinen ganzen Körper als sich die Glyphe in dein Fleisch brennt. Das wird sehr hässliche Narben geben!");
$session['user']['charm']-=5;
debuglog("Gab 5 Charme am Feuerschrein.");
   switch(e_rand(1,3)){
      case 1 :
      case 2 :

$sql="INSERT INTO commentary (postdate,section,author,comment) VALUES (now(),'party_fireplace',".$session[user][acctid].",'/me springt plötzlich auf und stürzt sich in das Lagerfeuer!')";
db_query($sql) or die(db_error(LINK));

output ("`&Obwohl du mit aller Gewalt ziehst kannst du deinen Arm nicht von der Glyphe lösen.`n `4 Unwürdiger Narr! `& hörst du es noch dumpf schallen bevor dein ganzer Körper verglüht... ");
$session['user']['hitpoints']=0;
addnews("`%".$session[user][name]."`^ hat sich auf dem Dorffest ins Lagerfeuer gestürzt und ist verbrannt!");
addnav("Weiter","shades.php");
      break;
      case 3 :
output ("`&Unter gewaltigen Schmerzen bleibst du dennoch standhaft und als du deinen Arm von der Glyphe lösen kannst, stellst du ein Mal fest, dass sich tief in dein Fleisch gebrannt hat.`n");
output ("Du hast das `^Mal des Feuers`& erlangt!");
$session['user']['marks']+=4;
addnews("`@".$session['user']['name']."`& hat das `^Mal des Feuers`& erlangt!");
addnav("Weiter","dorffest.php?op=fire&action=gossip");
      break;

   }
}



page_footer();
?>
