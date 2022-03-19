<?php

function wakeupinhouse($sort,$newday)
{
	global $session;
	switch ($sort)
	{

		// Wohnhaus
		case 1 :
		case 3 :
		case 5 :
			$text="`2Gut erholt wachst du im Haus auf und bist bereit f�r neue Abenteuer.`n`n";
			break;

			// Anwesen
		case 10 :
		case 11 :
		case 13 :
			$text="`2Du erwachst umgeben von Luxus und Wohlstand im Anwesen.`n`n";
			if ($newday==1)
			{
				$reward= e_rand(1,2);
				$text=$text."`2Nach dem Aufstehen nimmst du erstmal ein hei�es Bad und richtest dich sch�n her. Du erh�lst `#$reward Charmepunkte`2.`n";
				$session['user']['charm']+=$reward;
			}
			else
			{
				$text=$text."Nach einem Nickerchen im Anwesen bis du gut erholt f�r neue Taten.";
			}
			break;
			// Villa
		case 14 :
		case 16 :
			$text="`2Du erwachst umgeben von Luxus und Wohlstand in der Villa.`n`n";
			if ($newday==1)
			{
				$reward= e_rand(1,2);
				$text=$text."`2Nach dem Aufstehen nimmst du erstmal ein hei�es Bad und richtest dich sch�n her. Du erh�lst `#$reward Charmepunkte`2.`n";
				$session['user']['charm']+=$reward;
			}
			else
			{
				$text=$text."Nach einem Nickerchen im Anwesen bis du gut erholt f�r neue Taten.";
			}
			break;
			// Gasthaus
		case 17 :
		case 19 :
			$text="`2Du erwachst umgeben von Wohlstand im Gasthaus.`n`n";
			if ($newday==1)
			{
				$reward= e_rand(1,2);
				$text=$text."`2Nach dem Aufstehen nimmst du erstmal ein hei�es Bad und richtest dich sch�n her. Du erh�lst `#$reward Charmepunkte`2.`n";
				$session['user']['charm']+=$reward;
			}
			else
			{
				$text=$text."Nach einem Nickerchen im Anwesen bis du gut erholt f�r neue Taten.";
			}
			break;

			// Festung
		case 20 :
		case 21 :
		case 23 :
			$text="`2Gut erholt erwachst du in der Festung und bist bereit f�r neue Abenteuer.`n`n";
			If ($newday==1)
			{
				$text=$text."Die sichere Umgebung hat dich mal wieder richtig gut schlafen lassen. Du bekommst einen zus�tzlichen Waldkampf f�r heute.";
				$session['user']['turns']+=1;
			}
			else
			{
				$text=$text."`nNach einer kurzen Pause in der Festung bist du bereit f�r neue Abenteuer.`n";
			}
			break;
			// Turm
		case 24 :
		case 26 :
			$text="`2Gut erholt erwachst du im Turm und bist bereit f�r neue Abenteuer.`n`n";
			If ($newday==1)
			{
				$text=$text."Die sichere Umgebung hat dich mal wieder richtig gut schlafen lassen. Du bekommst einen zus�tzlichen Waldkampf f�r heute.";
				$session['user']['turns']+=1;
			}
			else
			{
				$text=$text."`nNach einer kurzen Pause im Turm bist du bereit f�r neue Abenteuer.`n";
			}
			break;
			// Burg
		case 27 :
		case 29 :
			$text="`2Gut erholt erwachst du in der Burg und bist bereit f�r neue Abenteuer.`n`n";
			If ($newday==1)
			{
				$text=$text."Die sichere Umgebung hat dich mal wieder richtig gut schlafen lassen. Du bekommst einen zus�tzlichen Waldkampf f�r heute.";
				$session['user']['turns']+=1;
			}
			else
			{
				$text=$text."`nNach einer kurzen Pause in der Burg bist du bereit f�r neue Abenteuer.`n";
			}
			break;

			// Versteck
		case 30 :
		case 31 :
		case 33 :
			If ($newday==1)
			{
				$text="`2Du erwachst in deinem Versteck mit R�ckenschmerzen und sehr schlecht erholt.`n`n";
				$mal = e_rand(30,70);
				$mal*=0.01;
				$text=$text."`2Die Nacht war so schrecklich, dass du Lebenspunkte verlierst!`n";

				$ache = array("name"=>"`!Gliederschmerzen","rounds"=>400,"wearoff"=>"`!Es geht dir nun wieder besser.`0","defmod"=>0.95,"atkmod"=>0.95,"roundmsg"=>"Die letzte Nacht war grauenvoll!","activate"=>"defense","activate"=>"offense");
				$session['bufflist']['ache']=$ache;
				$session['user']['hitpoints']*=$mal;
			}
			else
			{
				$text="`2Du erwachst nach einem Nickerchen im Versteck und bist dankbar endlich hier raus zu kommen.";
			}
			break;

			//Refugium
		case 34 :
		case 36 :
			$text="`2Du erwachst im Refugium und f�hlst dich einigermassen erholt.";
			break;

			//Kellergew�lbe
		case 37 :
		case 39 :
			If ($newday==1)
			{
				$text="`2Du erwachst im Kellergew�lbe mit leichten Gliederschmerzen und nicht so gut erholt.`n`n";
				$mal = e_rand(50,90);
				$mal*=0.01;
				$text=$text."`2Die Rast war so unangenehm, dass du Lebenspunkte verlierst!`n";
				$ache = array("name"=>"`!Leichte Gliederschmerzen","rounds"=>300,"wearoff"=>"`!Es geht dir nun wieder besser.`0","defmod"=>0.97,"atkmod"=>0.97,"roundmsg"=>"Die letzte Nacht war mies!","activate"=>"defense","activate"=>"offense");
				$session['bufflist']['ache']=$ache;
				$session['user']['hitpoints']*=$mal;
			}
			else
			{
				$text="`2Du erwachst nach einem Nickerchen im Kellergew�lbe und bist froh hier raus zu kommen.";
			}
			break;

			//Gildenhaus
		case 40 :
		case 41 :
		case 43 :
			$sql = "SELECT specid FROM specialty WHERE active='1'";
			$result = db_query($sql);
			$max=db_num_rows($result);
			$bonus= e_rand(1,$max);
			$sql = "SELECT specid,specname,filename,usename FROM specialty WHERE active='1' AND specid=$bonus";
			$result = db_query($sql);
			$row = db_fetch_assoc($result);
			$reward= e_rand(1,4);
			If ($newday==1)
			{
				$text="`2Du erwachst gut erholt im Gildenhaus`n`n";
				$text=$text."`2Die abendliche Diskussion mit den Meistern brachte dir `#$reward`2 zus�tzliche Anwendungen in ";
				$skills = array($row['specid']=>$row['specname']);
				$text=$text."`@".$skills[$bonus]."`2.`n";
				$session['user']['specialtyuses'][$row['usename']."uses"]+=$reward;
			}
			else
			{
				$text="`2Gut erholt wachst du im Gildenhaus auf und bist bereit f�r neue Abenteuer.`n`n";
			}
			break;

			//Zunfthaus
		case 44 :
		case 46 :
			$sql = "SELECT specid FROM specialty WHERE active='1'";
			$result = db_query($sql);
			$max=db_num_rows($result);
			$bonus= e_rand(1,$max);
			$sql = "SELECT specid,specname,filename,usename FROM specialty WHERE active='1' AND specid=$bonus";
			$result = db_query($sql);
			$row = db_fetch_assoc($result);
			$reward= e_rand(2,5);
			If ($newday==1)
			{
				$text="`2Du erwachst gut erholt im Zunfthaus.`n`n";
				$text=$text."`2Die abendliche Diskussion mit den Meistern brachte dir `#$reward`2 zus�tzliche Anwendungen in ";
				$skills = array($row['specid']=>$row['specname']);
				$text=$text."`@".$skills[$bonus]."`2.`n";
				$session['user']['specialtyuses'][$row['usename']."uses"]+=$reward;
			}
			else
			{
				$text="`2Gut erholt wachst du im Zunfthaus auf und bist bereit f�r neue Abenteuer.`n`n";
			}
			break;

			//Handelshaus
		case 47 :
		case 49 :
			$sql = "SELECT specid FROM specialty WHERE active='1'";
			$result = db_query($sql);
			$max=db_num_rows($result);
			$bonus= e_rand(1,$max);
			$sql = "SELECT specid,specname,filename,usename FROM specialty WHERE active='1' AND specid=$bonus";
			$result = db_query($sql);
			$row = db_fetch_assoc($result);
			$reward= e_rand(2,5);
			If ($newday==1)
			{
				$text="`2Du erwachst gut erholt im Handelshaus. `n`n";
				$text=$text."`2Die abendliche Diskussion mit den Meistern brachte dir `#$reward`2 zus�tzliche Anwendungen in ";
				$skills = array($row['specid']=>$row['specname']);
				$text=$text."`@".$skills[$bonus]."`2.`n";
				$session['user']['specialtyuses'][$row['usename']."uses"]+=$reward;
			}
			else
			{
				$text="`2Gut erholt wachst du im Handelshaus auf und bist bereit f�r neue Abenteuer.`n`n";
			}
			break;

			//Bauernhof
		case 50 :
		case 51 :
		case 53 :
			$text="`2Ein lauter Hahnenschrei weckt dich in aller Fr�h auf dem Bauernhof.`n`n";
			$baubon = $session['user']['level']*100;
			If ($newday==1)
			{
				$text=$text."`2Du hast hart gearbeitet und bekommst daf�r `#$baubon`2 Gold!`n";
				$session['user']['gold']+=$baubon;
			}
			else
			{
				$text="`2Gut erholt wachst du auf dem Bauernhof auf und bist bereit f�r neue Abenteuer.";
			}
			break;
			// Tierfarm
		case 54 :
		case 56 :
			$text="`2Ein lautes Schnauben und Wiehern weckt dich in aller Fr�h auf der Tierfarm.`n`n";
			$baubon = $session['user']['level']*200;
			If ($newday==1)
			{
				$text=$text."`2Du hast hart gearbeitet und bekommst daf�r `#$baubon`2 Gold!`n";
				$session['user']['gold']+=$baubon;
			}
			else
			{
				$text="`2Gut erholt wachst du auf der Tierfarm auf und bist bereit f�r neue Abenteuer.";
			}
			break;
			// Gutshof
		case 57 :
		case 59 :
			$text="`2Die Arbeit ruft in aller Fr�h auf dem Gutshof.`n`n";
			$baubon = $session['user']['level']*200;
			If ($newday==1)
			{
				$text=$text."`2Du hast hart gearbeitet und bekommst daf�r `#$baubon`2 Gold!`n";
				$session['user']['gold']+=$baubon;
			}
			else
			{
				$text="`2Gut erholt wachst du auf dem Gutshof auf und bist bereit f�r neue Abenteuer.";
			}
			break;

			//Gruft
		case 60 :
		case 61 :
		case 63 :
			$text="`2Du erwachst in der Gruft und klappst stilecht den Sargdeckel hoch.`n`n";
			$gruftbon = e_rand(10,50);
			If ($newday==1)
			{
				$text=$text."`2Ramius gef�llt das finstre Treiben so gut, dass er dir `#$gruftbon`2 Gefallen gew�hrt!`n";
				$session[user][deathpower]+=$gruftbon;
			}
			else
			{
				$text="`2Du erwachst gut erholt in der Gruft und bist bereit f�r neue Abenteuer.";
			}
			break;
			// Krypta
		case 64 :
		case 66 :
			$text="`2Du erwachst in der Krypta und klappst stilecht den Sargdeckel hoch.`n`n";
			$gruftbon = e_rand(30,60);
			If ($newday==1)
			{
				$text=$text."`2Ramius gef�llt das finstre Treiben so gut, dass er dir `#$gruftbon`2 Gefallen gew�hrt!`n";
				$session[user][deathpower]+=$gruftbon;
			}
			else
			{
				$text="`2Du erwachst gut erholt in der Krypta und bist bereit f�r neue Abenteuer.";
			}
			break;
			// Katakomben
		case 67 :
		case 59 :
			$text="`2Du erwachst in den Katakomben und klappst stilecht den Sargdeckel hoch.`n`n";
			$gruftbon = e_rand(30,60);
			If ($newday==1)
			{
				$text=$text."`2Ramius gef�llt das finstre Treiben so gut, dass er dir `#$gruftbon`2 Gefallen gew�hrt!`n";
				$session[user][deathpower]+=$gruftbon;
			}
			else
			{
				$text="`2Du erwachst gut erholt in den Katakomben und bist bereit f�r neue Abenteuer.";
			}
			break;

			//Kerker
		case 70 :
		case 71 :
		case 73 :
			$text="`2Die Schreie der Gefangenen im Kerker wecken dich am Morgen.`n`n";
			If ($newday==1)
			{
				$text=$text."`2F�r die �bernahme des Wachdienstes entlohnt dich der Kerkermeister mit `#einem Edelstein`2!`n";
				$session['user']['gems']+=1;
			}
			else
			{
				$text="`2Gut erholt wachst du Im W�rterzimmer des Kerkers auf und bist bereit f�r neue Abenteuer.";
			}
			break;
			// Verliess
		case 74 :
		case 76 :
			$text="`2Die Schreie der Gefangenen im Gef�ngnis wecken dich am Morgen.`n`n";
			If ($newday==1)
			{
				$text=$text."`2F�r die �bernahme des Wachdienstes entlohnt dich der Kerkermeister mit `#einem Edelstein`2!`n";
				$text=$text."`nDu erh�lst einen Spielerkampf zus�tzlich!";
				$session['user']['playerfights']+=1;
				$session['user']['gems']+=1;
			}
			else
			{
				$text="`2Gut erholt wachst du im W�rterzimmer des Gef�ngnisses auf und bist bereit f�r neue Abenteuer.";
			}
			break;
			// Arena
		case 77 :
		case 79 :
			$text="`2Die Schreie der Gefangenen im Verlies wecken dich am Morgen.`n`n";
			If ($newday==1)
			{
				$text=$text."`2F�r die �bernahme des Wachdienstes entlohnt dich der Kerkermeister mit `#einem Edelstein`2!`n";
				$text=$text."`nDu erh�lst einen Spielerkampf zus�tzlich!";
				$session['user']['playerfights']+=1;
				$session['user']['gems']+=1;
			}
			else
			{
				$text="`2Gut erholt wachst du im W�rterzimmer des Verliesses auf und bist bereit f�r neue Abenteuer.";
			}
			break;

			// Kloster
		case 80 :
		case 81 :
		case 83 :
			$text="`2Gut erholt wirst du im Kloster in aller Fr�h durch Glockenl�uten geweckt.`n`n";
			If ($newday==1)
			{
				$text=$text."`2Durch ein opulentes Fr�hst�ck und den Segen der Nonnen f�hlst du dich gest�rkt.`n";
				$session['user']['hitpoints']*=1.1;
			}
			else
			{
				$text="`2Gut erholt wachst du im Kloster auf und bist bereit f�r neue Abenteuer.";
			}
			break;
			// Abtei
		case 84 :
		case 86 :
			$text="`2Gut erholt wirst du in der Abtei in aller Fr�h durch Glockenl�uten geweckt.`n`n";
			If ($newday==1)
			{
				$text=$text."`2Durch ein opulentes Fr�hst�ck und der Klosterbr�der f�hlst du dich gest�rkt.`n";
				$session['user']['hitpoints']*=1.3;
			}
			else
			{
				$text="`2Gut erholt wachst du in der Abtei auf und bist bereit f�r neue Abenteuer.";
			}
			break;
		case 87 :
		case 89 :
			$text="`2Gut erholt wirst du im Ritterorden in aller Fr�h geweckt.`n`n";
			If ($newday==1)
			{
				$text=$text."`2Durch ein opulentes Fr�hst�ck und den Segen des Ordens f�hlst du dich gest�rkt.`n";
				$session['user']['hitpoints']*=1.3;
			}
			else
			{
				$text="`2Gut erholt wachst du im Ritterorden auf und bist bereit f�r neue Abenteuer.";
			}
			break;

			// Trainingslager
		case 90 :
		case 91 :
		case 93 :
			$text."`2Du erwachst am fr�hen Morgen durch lautes Schwerterklirren im Trainingslager.`n`n";
			$kasbon = round($session['user']['experience']*0.05);
			If ($newday==1)
			{
				$text=$text."Die Geschichten der Veteranen, denen du noch bis sp�t in die Nacht gelauscht hast, waren dir eine gro�e Lehre. Du erh�lst `#$kasbon`2 Erfahrung!`n";
				$session['user']['experience']+=$kasbon;
			}
			else
			{
				$text="`2Gut erholt wachst du im Trainingslager auf und bist bereit f�r neue Abenteuer.";
			}
			break;
			// Kaserne
		case 94 :
		case 96 :
			$text."`2Du erwachst am fr�hen Morgen durch die lauten Marschges�nge in der Kaserne.`n`n";
			$kasbon = round($session['user']['experience']*0.10);
			If ($newday==1)
			{
				$text=$text."Die Geschichten der Veteranen, denen du noch bis sp�t in die Nacht gelauscht hast, waren dir eine gro�e Lehre. Du erh�lst `#$kasbon`2 Erfahrung!`n";
				$session['user']['experience']+=$kasbon;
			}
			else
			{
				$text="`2Gut erholt wachst du in der Kaserne auf und bist bereit f�r neue Abenteuer.";
			}
			break;
			// S�ldnerlager
		case 97 :
		case 99 :
			$text."`2Du erwachst am fr�hen Morgen durch lautes Schwerterklirren im S�ldnerlager.`n`n";
			$kasbon = round($session['user']['experience']*0.10);
			If ($newday==1)
			{
				$text=$text."Die Geschichten der Veteranen, denen du noch bis sp�t in die Nacht gelauscht hast, waren dir eine gro�e Lehre. Du erh�lst `#$kasbon`2 Erfahrung!`n";
				$session['user']['experience']+=$kasbon;
			}
			else
			{
				$text="`2Gut erholt wachst du im S�ldnerlager auf und bist bereit f�r neue Abenteuer.";
			}
			break;

			// Bordell
		case 100 :
		case 101 :
		case 103 :
			output("Bordell`n");
			$text="`2Nach einer langen wild durchzechten Nacht erwachst du gut gelaunt im Bordell.`n`n";
			$happy = array("name"=>"`!Extrem gute Laune","rounds"=>45,"wearoff"=>"`!Deine gute Laune vergeht allm�hlich wieder.`0","defmod"=>1.15,"roundmsg"=>"Du schwelgst in Erinnerung an den Bordellbesuch und tust alles daf�r dass es nicht dein Letzter war!","activate"=>"defense");
			If ($newday==1)
			{
				$text=$text."`2War das eine Nacht!`n";
				$session['bufflist']['happy']=$happy;

				switch (e_rand(1,3))
				{
					case 1:
						break;
					case 2:
						addnews("`@".$session['user']['name']."`@ wurde gesehen, wie  ".($session[user][sex]?"sie":"er")." mit einem breiten Grinsen ein Bordell verliess!");

						if ($session['user']['charisma']==4294967295)
						{
							$sql = "SELECT acctid,name FROM accounts WHERE locked=0 AND acctid=".$session['user'][marriedto]."";
							$result = db_query($sql) or die(db_error(LINK));
							$row = db_fetch_assoc($result);
							$partner=$row['name'];
							systemmail($row['acctid'],"`$Bordellbesuch!`0","`&{$session['user']['name']}
                    		`6 wurde gesehen, wie ".($session['user']['sex']?"sie":"er")." sich im Bordell vergn�gt hat. Willst du dir das gefallen lassen ?");
						}
						break;
					case 3:
						break;
				}
			}
			else
			{
				$text="`2Gut erholt wachst du im Bordell auf und bist bereit f�r neue Abenteuer.";
			}
			break;
			// Luxusbordell
		case 104 :
		case 106 :
			$text="`2Nach einer langen wild durchzechten Nacht erwachst du sehr gut gelaunt im Rotlichtpalast.`n`n";
			$happy = array("name"=>"`!Extrem gute Laune","rounds"=>60,"wearoff"=>"`!Deine gute Laune vergeht allm�hlich wieder.`0","defmod"=>1.15,"roundmsg"=>"Du schwelgst in Erinnerung an den Bordellbesuch und tust alles daf�r dass es nicht dein Letzter war!","activate"=>"defense");
			If ($newday==1)
			{
				$text=$text."`2War das eine Nacht!`n";
				$session['bufflist']['happy']=$happy;

				switch (e_rand(1,3))
				{
					case 1:
						break;
					case 2:
						addnews("`@".$session['user']['name']."`@ wurde gesehen, wie  ".($session['user']['sex']?"sie":"er")." mit einem breiten Grinsen ein Bordell verliess!");

						if ($session['user'][charisma]==4294967295)
						{
							$sql = "SELECT acctid,name FROM accounts WHERE locked=0 AND acctid=".$session['user'][marriedto]."";
							$result = db_query($sql) or die(db_error(LINK));
							$row = db_fetch_assoc($result);
							$partner=$row['name'];
							systemmail($row['acctid'],"`$Bordellbesuch!`0","`&{$session['user']['name']}
                    		`6 wurde gesehen, wie ".($session[user][sex]?"sie":"er")." sich im Bordell vergn�gt hat. Willst du dir das gefallen lassen ?");
						}
						break;
					case 3:
						break;
				}
			}
			else
			{
				$text="`2Gut erholt wachst du im Rotlichtpalast auf und bist bereit f�r neue Abenteuer.";
			}
			break;
		case 107 :
		case 109 :
			$text="`2Nach einer langen wild durchzechten Nacht erwachst du gut gelaunt in der Spelunke.`n`n";
			$happy = array("name"=>"`!Extrem gute Laune","rounds"=>60,"wearoff"=>"`!Deine gute Laune vergeht allm�hlich wieder.`0","defmod"=>1.15,"roundmsg"=>"Du schwelgst in Erinnerung an den Bordellbesuch und tust alles daf�r dass es nicht dein Letzter war!","activate"=>"defense");
			If ($newday==1)
			{
				$text=$text."`2War das eine Nacht!`n";
				$session['bufflist']['happy']=$happy;

				switch (e_rand(1,3))
				{
					case 1:
						break;
					case 2:
						//News-Eintrag und Mail an den Partner... so gehts ja nicht
						addnews("`@".$session['user']['name']."`@ wurde gesehen, wie  ".($session[user][sex]?"sie":"er")." mit einem breiten Grinsen ein Bordell verliess!");

						if ($session['user'][charisma]==4294967295)
						{
							$sql = "SELECT acctid,name FROM accounts WHERE locked=0 AND acctid=".$session['user'][marriedto]."";
							$result = db_query($sql) or die(db_error(LINK));
							$row = db_fetch_assoc($result);
							$partner=$row['name'];
							systemmail($row['acctid'],"`$Bordellbesuch!`0","`&{$session['user']['name']}
                    		`6 wurde gesehen, wie ".($session[user][sex]?"sie":"er")." sich im Bordell vergn�gt hat. Willst du dir das gefallen lassen ?");
						}
						break;
					case 3:
						break;
				}
			}
			else
			{
				$text="`2Gut erholt wachst du in der Spelunke auf und bist bereit f�r neue Abenteuer.";
			}
			break;

	}
	return($text);
}
?>