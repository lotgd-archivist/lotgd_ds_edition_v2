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
			$text="`2Gut erholt wachst du im Haus auf und bist bereit für neue Abenteuer.`n`n";
			break;

			// Anwesen
		case 10 :
		case 11 :
		case 13 :
			$text="`2Du erwachst umgeben von Luxus und Wohlstand im Anwesen.`n`n";
			if ($newday==1)
			{
				$reward= e_rand(1,2);
				$text=$text."`2Nach dem Aufstehen nimmst du erstmal ein heißes Bad und richtest dich schön her. Du erhälst `#$reward Charmepunkte`2.`n";
				$session['user']['charm']+=$reward;
			}
			else
			{
				$text=$text."Nach einem Nickerchen im Anwesen bis du gut erholt für neue Taten.";
			}
			break;
			// Villa
		case 14 :
		case 16 :
			$text="`2Du erwachst umgeben von Luxus und Wohlstand in der Villa.`n`n";
			if ($newday==1)
			{
				$reward= e_rand(1,2);
				$text=$text."`2Nach dem Aufstehen nimmst du erstmal ein heißes Bad und richtest dich schön her. Du erhälst `#$reward Charmepunkte`2.`n";
				$session['user']['charm']+=$reward;
			}
			else
			{
				$text=$text."Nach einem Nickerchen im Anwesen bis du gut erholt für neue Taten.";
			}
			break;
			// Gasthaus
		case 17 :
		case 19 :
			$text="`2Du erwachst umgeben von Wohlstand im Gasthaus.`n`n";
			if ($newday==1)
			{
				$reward= e_rand(1,2);
				$text=$text."`2Nach dem Aufstehen nimmst du erstmal ein heißes Bad und richtest dich schön her. Du erhälst `#$reward Charmepunkte`2.`n";
				$session['user']['charm']+=$reward;
			}
			else
			{
				$text=$text."Nach einem Nickerchen im Anwesen bis du gut erholt für neue Taten.";
			}
			break;

			// Festung
		case 20 :
		case 21 :
		case 23 :
			$text="`2Gut erholt erwachst du in der Festung und bist bereit für neue Abenteuer.`n`n";
			If ($newday==1)
			{
				$text=$text."Die sichere Umgebung hat dich mal wieder richtig gut schlafen lassen. Du bekommst einen zusätzlichen Waldkampf für heute.";
				$session['user']['turns']+=1;
			}
			else
			{
				$text=$text."`nNach einer kurzen Pause in der Festung bist du bereit für neue Abenteuer.`n";
			}
			break;
			// Turm
		case 24 :
		case 26 :
			$text="`2Gut erholt erwachst du im Turm und bist bereit für neue Abenteuer.`n`n";
			If ($newday==1)
			{
				$text=$text."Die sichere Umgebung hat dich mal wieder richtig gut schlafen lassen. Du bekommst einen zusätzlichen Waldkampf für heute.";
				$session['user']['turns']+=1;
			}
			else
			{
				$text=$text."`nNach einer kurzen Pause im Turm bist du bereit für neue Abenteuer.`n";
			}
			break;
			// Burg
		case 27 :
		case 29 :
			$text="`2Gut erholt erwachst du in der Burg und bist bereit für neue Abenteuer.`n`n";
			If ($newday==1)
			{
				$text=$text."Die sichere Umgebung hat dich mal wieder richtig gut schlafen lassen. Du bekommst einen zusätzlichen Waldkampf für heute.";
				$session['user']['turns']+=1;
			}
			else
			{
				$text=$text."`nNach einer kurzen Pause in der Burg bist du bereit für neue Abenteuer.`n";
			}
			break;

			// Versteck
		case 30 :
		case 31 :
		case 33 :
			If ($newday==1)
			{
				$text="`2Du erwachst in deinem Versteck mit Rückenschmerzen und sehr schlecht erholt.`n`n";
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
			$text="`2Du erwachst im Refugium und fühlst dich einigermassen erholt.";
			break;

			//Kellergewölbe
		case 37 :
		case 39 :
			If ($newday==1)
			{
				$text="`2Du erwachst im Kellergewölbe mit leichten Gliederschmerzen und nicht so gut erholt.`n`n";
				$mal = e_rand(50,90);
				$mal*=0.01;
				$text=$text."`2Die Rast war so unangenehm, dass du Lebenspunkte verlierst!`n";
				$ache = array("name"=>"`!Leichte Gliederschmerzen","rounds"=>300,"wearoff"=>"`!Es geht dir nun wieder besser.`0","defmod"=>0.97,"atkmod"=>0.97,"roundmsg"=>"Die letzte Nacht war mies!","activate"=>"defense","activate"=>"offense");
				$session['bufflist']['ache']=$ache;
				$session['user']['hitpoints']*=$mal;
			}
			else
			{
				$text="`2Du erwachst nach einem Nickerchen im Kellergewölbe und bist froh hier raus zu kommen.";
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
				$text=$text."`2Die abendliche Diskussion mit den Meistern brachte dir `#$reward`2 zusätzliche Anwendungen in ";
				$skills = array($row['specid']=>$row['specname']);
				$text=$text."`@".$skills[$bonus]."`2.`n";
				$session['user']['specialtyuses'][$row['usename']."uses"]+=$reward;
			}
			else
			{
				$text="`2Gut erholt wachst du im Gildenhaus auf und bist bereit für neue Abenteuer.`n`n";
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
				$text=$text."`2Die abendliche Diskussion mit den Meistern brachte dir `#$reward`2 zusätzliche Anwendungen in ";
				$skills = array($row['specid']=>$row['specname']);
				$text=$text."`@".$skills[$bonus]."`2.`n";
				$session['user']['specialtyuses'][$row['usename']."uses"]+=$reward;
			}
			else
			{
				$text="`2Gut erholt wachst du im Zunfthaus auf und bist bereit für neue Abenteuer.`n`n";
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
				$text=$text."`2Die abendliche Diskussion mit den Meistern brachte dir `#$reward`2 zusätzliche Anwendungen in ";
				$skills = array($row['specid']=>$row['specname']);
				$text=$text."`@".$skills[$bonus]."`2.`n";
				$session['user']['specialtyuses'][$row['usename']."uses"]+=$reward;
			}
			else
			{
				$text="`2Gut erholt wachst du im Handelshaus auf und bist bereit für neue Abenteuer.`n`n";
			}
			break;

			//Bauernhof
		case 50 :
		case 51 :
		case 53 :
			$text="`2Ein lauter Hahnenschrei weckt dich in aller Früh auf dem Bauernhof.`n`n";
			$baubon = $session['user']['level']*100;
			If ($newday==1)
			{
				$text=$text."`2Du hast hart gearbeitet und bekommst dafür `#$baubon`2 Gold!`n";
				$session['user']['gold']+=$baubon;
			}
			else
			{
				$text="`2Gut erholt wachst du auf dem Bauernhof auf und bist bereit für neue Abenteuer.";
			}
			break;
			// Tierfarm
		case 54 :
		case 56 :
			$text="`2Ein lautes Schnauben und Wiehern weckt dich in aller Früh auf der Tierfarm.`n`n";
			$baubon = $session['user']['level']*200;
			If ($newday==1)
			{
				$text=$text."`2Du hast hart gearbeitet und bekommst dafür `#$baubon`2 Gold!`n";
				$session['user']['gold']+=$baubon;
			}
			else
			{
				$text="`2Gut erholt wachst du auf der Tierfarm auf und bist bereit für neue Abenteuer.";
			}
			break;
			// Gutshof
		case 57 :
		case 59 :
			$text="`2Die Arbeit ruft in aller Früh auf dem Gutshof.`n`n";
			$baubon = $session['user']['level']*200;
			If ($newday==1)
			{
				$text=$text."`2Du hast hart gearbeitet und bekommst dafür `#$baubon`2 Gold!`n";
				$session['user']['gold']+=$baubon;
			}
			else
			{
				$text="`2Gut erholt wachst du auf dem Gutshof auf und bist bereit für neue Abenteuer.";
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
				$text=$text."`2Ramius gefällt das finstre Treiben so gut, dass er dir `#$gruftbon`2 Gefallen gewährt!`n";
				$session[user][deathpower]+=$gruftbon;
			}
			else
			{
				$text="`2Du erwachst gut erholt in der Gruft und bist bereit für neue Abenteuer.";
			}
			break;
			// Krypta
		case 64 :
		case 66 :
			$text="`2Du erwachst in der Krypta und klappst stilecht den Sargdeckel hoch.`n`n";
			$gruftbon = e_rand(30,60);
			If ($newday==1)
			{
				$text=$text."`2Ramius gefällt das finstre Treiben so gut, dass er dir `#$gruftbon`2 Gefallen gewährt!`n";
				$session[user][deathpower]+=$gruftbon;
			}
			else
			{
				$text="`2Du erwachst gut erholt in der Krypta und bist bereit für neue Abenteuer.";
			}
			break;
			// Katakomben
		case 67 :
		case 59 :
			$text="`2Du erwachst in den Katakomben und klappst stilecht den Sargdeckel hoch.`n`n";
			$gruftbon = e_rand(30,60);
			If ($newday==1)
			{
				$text=$text."`2Ramius gefällt das finstre Treiben so gut, dass er dir `#$gruftbon`2 Gefallen gewährt!`n";
				$session[user][deathpower]+=$gruftbon;
			}
			else
			{
				$text="`2Du erwachst gut erholt in den Katakomben und bist bereit für neue Abenteuer.";
			}
			break;

			//Kerker
		case 70 :
		case 71 :
		case 73 :
			$text="`2Die Schreie der Gefangenen im Kerker wecken dich am Morgen.`n`n";
			If ($newday==1)
			{
				$text=$text."`2Für die Übernahme des Wachdienstes entlohnt dich der Kerkermeister mit `#einem Edelstein`2!`n";
				$session['user']['gems']+=1;
			}
			else
			{
				$text="`2Gut erholt wachst du Im Wärterzimmer des Kerkers auf und bist bereit für neue Abenteuer.";
			}
			break;
			// Verliess
		case 74 :
		case 76 :
			$text="`2Die Schreie der Gefangenen im Gefängnis wecken dich am Morgen.`n`n";
			If ($newday==1)
			{
				$text=$text."`2Für die Übernahme des Wachdienstes entlohnt dich der Kerkermeister mit `#einem Edelstein`2!`n";
				$text=$text."`nDu erhälst einen Spielerkampf zusätzlich!";
				$session['user']['playerfights']+=1;
				$session['user']['gems']+=1;
			}
			else
			{
				$text="`2Gut erholt wachst du im Wärterzimmer des Gefängnisses auf und bist bereit für neue Abenteuer.";
			}
			break;
			// Arena
		case 77 :
		case 79 :
			$text="`2Die Schreie der Gefangenen im Verlies wecken dich am Morgen.`n`n";
			If ($newday==1)
			{
				$text=$text."`2Für die Übernahme des Wachdienstes entlohnt dich der Kerkermeister mit `#einem Edelstein`2!`n";
				$text=$text."`nDu erhälst einen Spielerkampf zusätzlich!";
				$session['user']['playerfights']+=1;
				$session['user']['gems']+=1;
			}
			else
			{
				$text="`2Gut erholt wachst du im Wärterzimmer des Verliesses auf und bist bereit für neue Abenteuer.";
			}
			break;

			// Kloster
		case 80 :
		case 81 :
		case 83 :
			$text="`2Gut erholt wirst du im Kloster in aller Früh durch Glockenläuten geweckt.`n`n";
			If ($newday==1)
			{
				$text=$text."`2Durch ein opulentes Frühstück und den Segen der Nonnen fühlst du dich gestärkt.`n";
				$session['user']['hitpoints']*=1.1;
			}
			else
			{
				$text="`2Gut erholt wachst du im Kloster auf und bist bereit für neue Abenteuer.";
			}
			break;
			// Abtei
		case 84 :
		case 86 :
			$text="`2Gut erholt wirst du in der Abtei in aller Früh durch Glockenläuten geweckt.`n`n";
			If ($newday==1)
			{
				$text=$text."`2Durch ein opulentes Frühstück und der Klosterbrüder fühlst du dich gestärkt.`n";
				$session['user']['hitpoints']*=1.3;
			}
			else
			{
				$text="`2Gut erholt wachst du in der Abtei auf und bist bereit für neue Abenteuer.";
			}
			break;
		case 87 :
		case 89 :
			$text="`2Gut erholt wirst du im Ritterorden in aller Früh geweckt.`n`n";
			If ($newday==1)
			{
				$text=$text."`2Durch ein opulentes Frühstück und den Segen des Ordens fühlst du dich gestärkt.`n";
				$session['user']['hitpoints']*=1.3;
			}
			else
			{
				$text="`2Gut erholt wachst du im Ritterorden auf und bist bereit für neue Abenteuer.";
			}
			break;

			// Trainingslager
		case 90 :
		case 91 :
		case 93 :
			$text."`2Du erwachst am frühen Morgen durch lautes Schwerterklirren im Trainingslager.`n`n";
			$kasbon = round($session['user']['experience']*0.05);
			If ($newday==1)
			{
				$text=$text."Die Geschichten der Veteranen, denen du noch bis spät in die Nacht gelauscht hast, waren dir eine große Lehre. Du erhälst `#$kasbon`2 Erfahrung!`n";
				$session['user']['experience']+=$kasbon;
			}
			else
			{
				$text="`2Gut erholt wachst du im Trainingslager auf und bist bereit für neue Abenteuer.";
			}
			break;
			// Kaserne
		case 94 :
		case 96 :
			$text."`2Du erwachst am frühen Morgen durch die lauten Marschgesänge in der Kaserne.`n`n";
			$kasbon = round($session['user']['experience']*0.10);
			If ($newday==1)
			{
				$text=$text."Die Geschichten der Veteranen, denen du noch bis spät in die Nacht gelauscht hast, waren dir eine große Lehre. Du erhälst `#$kasbon`2 Erfahrung!`n";
				$session['user']['experience']+=$kasbon;
			}
			else
			{
				$text="`2Gut erholt wachst du in der Kaserne auf und bist bereit für neue Abenteuer.";
			}
			break;
			// Söldnerlager
		case 97 :
		case 99 :
			$text."`2Du erwachst am frühen Morgen durch lautes Schwerterklirren im Söldnerlager.`n`n";
			$kasbon = round($session['user']['experience']*0.10);
			If ($newday==1)
			{
				$text=$text."Die Geschichten der Veteranen, denen du noch bis spät in die Nacht gelauscht hast, waren dir eine große Lehre. Du erhälst `#$kasbon`2 Erfahrung!`n";
				$session['user']['experience']+=$kasbon;
			}
			else
			{
				$text="`2Gut erholt wachst du im Söldnerlager auf und bist bereit für neue Abenteuer.";
			}
			break;

			// Bordell
		case 100 :
		case 101 :
		case 103 :
			output("Bordell`n");
			$text="`2Nach einer langen wild durchzechten Nacht erwachst du gut gelaunt im Bordell.`n`n";
			$happy = array("name"=>"`!Extrem gute Laune","rounds"=>45,"wearoff"=>"`!Deine gute Laune vergeht allmählich wieder.`0","defmod"=>1.15,"roundmsg"=>"Du schwelgst in Erinnerung an den Bordellbesuch und tust alles dafür dass es nicht dein Letzter war!","activate"=>"defense");
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
                    		`6 wurde gesehen, wie ".($session['user']['sex']?"sie":"er")." sich im Bordell vergnügt hat. Willst du dir das gefallen lassen ?");
						}
						break;
					case 3:
						break;
				}
			}
			else
			{
				$text="`2Gut erholt wachst du im Bordell auf und bist bereit für neue Abenteuer.";
			}
			break;
			// Luxusbordell
		case 104 :
		case 106 :
			$text="`2Nach einer langen wild durchzechten Nacht erwachst du sehr gut gelaunt im Rotlichtpalast.`n`n";
			$happy = array("name"=>"`!Extrem gute Laune","rounds"=>60,"wearoff"=>"`!Deine gute Laune vergeht allmählich wieder.`0","defmod"=>1.15,"roundmsg"=>"Du schwelgst in Erinnerung an den Bordellbesuch und tust alles dafür dass es nicht dein Letzter war!","activate"=>"defense");
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
                    		`6 wurde gesehen, wie ".($session[user][sex]?"sie":"er")." sich im Bordell vergnügt hat. Willst du dir das gefallen lassen ?");
						}
						break;
					case 3:
						break;
				}
			}
			else
			{
				$text="`2Gut erholt wachst du im Rotlichtpalast auf und bist bereit für neue Abenteuer.";
			}
			break;
		case 107 :
		case 109 :
			$text="`2Nach einer langen wild durchzechten Nacht erwachst du gut gelaunt in der Spelunke.`n`n";
			$happy = array("name"=>"`!Extrem gute Laune","rounds"=>60,"wearoff"=>"`!Deine gute Laune vergeht allmählich wieder.`0","defmod"=>1.15,"roundmsg"=>"Du schwelgst in Erinnerung an den Bordellbesuch und tust alles dafür dass es nicht dein Letzter war!","activate"=>"defense");
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
                    		`6 wurde gesehen, wie ".($session[user][sex]?"sie":"er")." sich im Bordell vergnügt hat. Willst du dir das gefallen lassen ?");
						}
						break;
					case 3:
						break;
				}
			}
			else
			{
				$text="`2Gut erholt wachst du in der Spelunke auf und bist bereit für neue Abenteuer.";
			}
			break;

	}
	return($text);
}
?>