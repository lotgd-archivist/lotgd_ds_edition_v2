<?
//race.php
//idea and written by aska
//
//V1_ger only mounts
if ($HTTP_GET_VARS[op]=="ride")
{
$rand = e_rand(0,14);
$enmount = ($rand*$session['bufflist']['mount']['rounds']/10)+15;

	if($session['bufflist']['mount']['rounds']>$enmount)
	{
		output("`2Du holst den Reiter noch vor dem Dorf ein und kommst auch wieder als ersters zur�ck. Der Reiter und sein schwarzes Ross kommen erst ein paar Sekunden sp�ter an. `Q\"Wahrlich, ein schnelles Tier habt ihr da. Nehmt dies.\"`2`n Du erh�lst ");
		switch(e_rand(1,5))
		{
			case 1:
			case 2:
			$gold = e_rand($session[user][level]*50,$session[user][level]*80);
			$session[user][gold]+=$gold;
			output("`^".$gold." `2Gold");
			break;
			case 3:
			case 4:
			$session[user][gems]+=2;
			output("`^2 `2Edelsteine");
			break;
			case 5:
			$gold = e_rand($session[user][level]*35,$session[user][level]*80);
			$session[user][gold]+=$gold;
			$session[user][gems]+=1;
			output("`^einen`2 Edelstein und `^".$gold." `2Gold");
			break;
		}
	}
	else
	{
		output("`2Du treibst dein Tier an und verfolgst den Reiter und sein Ross. Trotz ein paar �berholversuche schaffst du es nicht in F�hrung zu gehen. Er kommt als ersters an und meint `Q\"Ich w�rde sagen, ich habe gewonnen.\"`2 `nDer Mann nimmt dir ");
		switch(e_rand(1,5))
		{
			case 1:
			case 2:
			$session[user][gold]=(int)($session[user][gold]/2);
			output("die H�lfte deines Goldes");
			break;
			case 3:
			case 4:
			if($session[user][gems]==0)
			{
				$session[user][gold]=(int)($session[user][gold]/2);
				output("die H�lfte deines Goldes.");
				break;
			}
			else if($session[user][gems]==1)
			{
				$session[user][gems]--;
				output("deinen letzten Edelstein.");
				break;
			}
			else
			{
                $lostgems=(int)($session[user][gems]/2);
                $session[user][gems]-=(int)($session[user][gems]/2);
				output("die H�lfte deiner Edelsteine.");
				debuglog("verlor $lostgems Edelsteine beim Rennen im Wald.");
				break;
			}
			case 5:
			if($session[user][gems]>0)
			{
                $lostgems=(int)($session[user][gems]/2);
				$session[user][gold]-=(int)($session[user][gold]/2);
				$session[user][gems]-=(int)($session[user][gems]/2);
				debuglog("verlor $lostgems Edelsteine beim Rennen im Wald.");
				output("Die H�lfte deiner Edelsteine und deines Goldes.");
			}
			else
			{
				$session[user][gold]=0;
				output("alle dein Gold.");
			}

			break;
		}
		output(" `nW�tend �ber deinen Verlust verpasst du deinem Tier einen Klaps und gehst weiter.");
	}
	if($session['bufflist']['mount']['rounds']>30)
	{
		$session['bufflist']['mount']['rounds']=(int)($session['bufflist']['mount']['rounds']-30);
		output("`nVon dem Rennen ist dein ".$playermount['mountname']." ersch�pft und verliert an Kraft.");
	}
	else
	{
		$session['bufflist']['mount']['rounds']=0;
		output("`nVon dem Rennen ist dein ".$playermount['mountname']." zu ersch�pft um dir heute noch zu helfen.");
	}


}
else if($HTTP_GET_VARS[op]=="ignore")
{
	$session[user][specialinc]="";
	output("`2Sein Pferd hatte irgendwie einen irren Blick.");
}
else if($HTTP_GET_VARS[op]=="")
{
	if($session['user']['hashorse']>0)
	{
		if($session['bufflist']['mount']['rounds']==0)
		{
			output("`2Ein Mann taucht auf seinem schwarzen Pferd neben dir auf. `Q\"Wie w�rs mit einem Rennen?\"`2 fragt er dich und braust schon davon. Du versuchst dein/en ".$playermount['mountname']." anzutreiben doch das Tier ist f�r heute schon zu ersch�pft f�r ein Rennen. Du h�rst noch ein irres Lachen aus der Richtung in die der Reiter abged�st ist, k�mmerst dich jedoch nicht weiter darum.");
		}
		else
		{
			output("`2Ein Mann taucht auf seinem schwarzen Pferd neben dir auf. `Q\"Wie w�rs mit einem Rennen? Bis zum Dorf und zur�ck?\"`2 fragt er dich und braust schon davon.`nWillst du seine Herausforderung annehmen? Wer wei�, was er als Gegenleistung bei deiner Niederlage fordert..");
			addnav("Reiten","forest.php?op=ride");
			addnav("Ignorieren","forest.php?op=ignore");
			$session[user][specialinc]="race.php";
		}
	}
	else
	{
		output("`2Ein Mann reitet auf seinem schwarzen Pferd an dir vorbei. Was w�rdest du daf�r geben, auch so ein Tier zu haben...");
	}
}
?>
