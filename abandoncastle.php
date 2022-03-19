<?
/*
Verlassenes Schloss Maze
Author Lonny Luberts
with Mazes by Lonny, Kain (Paul Syverson), Tundrawolf, Hermione, Blayze of http://www.pqcomp.com/logd
version 1.1
June 2004

add to dragon.php after ,"beta"=>1
,"mazeedit"=>1

Mysql inclusions
ALTER TABLE accounts ADD `mazeedit` text NOT NULL
ALTER TABLE accounts ADD `maze` text NOT NULL
ALTER TABLE accounts ADD `mazeturn` int(11) NOT NULL default '0'
ALTER TABLE accounts ADD `pqtemp` text NOT NULL

pqtemp is used in a number of my mods for a temporary (recyclable) place to store info that
I do not want players to see on the url.
Mazes must always start at location 6!
Location 6 should ALWAYS be a piece with a south nav for continuity.
Mazes can end anywhere, and one should use every piece of the grid for their maze
There is no BLANK maze piece... I could code this, however I would rather make
dead ends for the player.  At present there is no limit to the number of times a
player can enter and do a maze.

I did not code this mod with any database access as an admin may want to let users
make maps!  A bad map could cause errors!  Make sure all maps do NOT have any X's
(there is checking for this and the app will not die, but your player will), make
sure all corridors connect or terminate properly or you will have a confusing and
unrealistic maze!  Do NOT use too many traps as players will no longer use a feature
that constantly kills them.  Do NOT use more than one exit... the app allows for this
however 2 exits will confuse the heck out of a player.

there is code for potions, chow and trading/lonny's castle items in the random event routine.. comment
these out if you are not using these mods!
*/

// MOD by talion, 15.5.05: umgestellt auf Schlossrunden statt Waldkämpfe, Ereignisse & Monster benötigen WK
// Februar 06: Umgestellt auf Datenbankspeicherung der Karten
// Addon by Alucard Februar 06: Irrgarten hinzugefügt
require_once "common.php";
checkday();
page_header("Verlassenes Schloss");

// events

if ($session['user']['hitpoints'] > 0)
{
}
else
{
    redirect("shades.php");
}

if($_GET['suentry']) {
	
	$su_maze = (int)$_POST['maze'];
	user_set_aei(array('maze'=>$su_maze));	
	$session['user']['castleturns']++;
	
}

//checkevent();
if ($_GET['op'] == "" and $_GET['loc'] == "")
{
    output("`c`b`&Verlassenes Schloss`0`b`c`n`n");
    $int_dragonkills_to_go = 10-$session['user']['dragonkills'];
    if ($int_dragonkills_to_go<0)
    {
        $int_dragonkills_to_go = 0;
    }
    
    $time = gametime();
    $tomorrow = strtotime(date("Y-m-d H:i:s",$time)." + 1 day");
    $tomorrow = strtotime(date("Y-m-d 00:00:00",$tomorrow));
    $secstotomorrow = $tomorrow-$time;
    //  $realsecstotomorrow = $secstotomorrow / getsetting("daysperday",4);
    
    $opentime = 24-date("G\\",strtotime("1980-01-01 00:00:00 + $secstotomorrow seconds"));
    
    if (($opentime<4 || $opentime>23) && !su_check(SU_RIGHT_DEBUG))
    {
        output("`2 Als du auf das verlassene Schloss zureitest bist Du frohen Mutes Dich endlich einmal mehr beweisen zu können und diesem dunklen Gemäuer ein paar mehr Geheimnisse oder sogar Gold entreissen zu können.");
        output("`2Doch als du die schwere Türe zum Eingang aufstoßen willst bemerkst du ein Schild :`n");
        output("`@Das verlassene Schloss. Öffnungszeiten von 03:00 - 23:00.`n");
        output("`2Enttäuscht wendest du dich ab.`n");
        addnav("Ich komme wieder!","village.php");
        
    }
    else //if ($session['user']['turns'] < getsetting("castle_min_turns",4) )
    if($session['user']['castleturns'] <= 0)
    {
        output("`2 Als du auf das verlassene Schloss zureitest bist Du frohen Mutes Dich endlich einmal mehr beweisen zu können und diesem dunklen Gemäuer ein paar mehr Geheimnisse oder sogar Gold entreissen zu können.");
        output("`2 `n Wenige Schritte jedoch vor dem Tor durchfährt Dich plötzlich ein Schauer und eine Stimme flüstert Dir scheinbar von nirgendwoher ins Ohr`n");
        output("`&`n\"So, Du willst es also mit den Schrecken dieses Ortes aufnehmen? Du kannst ja selbst kaum mehr stehen!\"");
        output("`2`nBetreten stellst Du fest, dass Dein Gewissen...oder was auch immer...Recht hat, Du bist wirklich ziemlich müde. Vielleicht solltest Du morgen wieder kommen, wenn Du ausgeruht hast!");
        output("`2`nDu kehrst dem Schloss dem Nacken und entfernst Dich langsam. Mit jedem Schritt lässt das Prickeln auf Deinem Nacken ein wenig mehr nach, bis Du im Dorf ankommst.");
        addnav("Zurück zum Dorf","village.php");
    }
    else if ($session['user']['dragonkills'] > 9)
    {
        output("`2 Als Du das verlassene Schloss betrittst, schlägt hinter Dir plötzlich und unerwartet die Tür zu.");
        output("Erschrocken rennst Du zurück und stemmst Dich dagegen, doch so sehr Du es auch versuchst, sie lässt sich nicht bewegen.");

		/*nur zum testen:*/
		if( !empty($_GET['choose']) || !su_check(SU_RIGHT_DEBUG)){
			//Statistik...
			savesetting("CASTLEVISITS",getsetting("CASTLEVISITS",0)+1);
			
			$maze_disciple = 0;
			$session['user']['castleturns']--;
			$session['user']['maze_visited'] = "";
			
			
			if (count($session['bufflist'])>0 && is_array($session['bufflist']) || $_GET['skill']!="")
			{
				$_GET['skill']="";
				if ($_GET['skill']=="")
				{
					$session['user']['buffbackup']=serialize($session['bufflist']);
				}
				
				
				//knappe kommt mit
				$sql = "SELECT name,state FROM disciples WHERE master=".$session['user']['acctid']."";
				$result = db_query($sql) or die(db_error(LINK));
				if (db_num_rows($result)>0)
				{
					$rowk = db_fetch_assoc($result);
				}
				$kname=$rowk['name'];
				$kstate=$rowk['state'];
				
				if (($kstate>0) && (is_array($session['bufflist']['decbuff'])))
				{
					$maze_disciple = 1;
					$decbuff=$session['bufflist']['decbuff'];
					
				}
				
				// Edelsteinelsterbuff
				if ($session['bufflist']['gemelster'])
				{
					$arr_gemelster_buff = $session['bufflist']['gemelster'];
				}
				
				$session['bufflist']=array();
				if (is_array($decbuff))
				{
					$session['bufflist']['decbuff']=$decbuff;
				}
				if (is_array($arr_gemelster_buff))
				{
					$session['bufflist']['gemelster'] = $arr_gemelster_buff;
				}
			}
			
			$session['user']['mazeturn'] = 0;
		}
		//zum testen ne auswahl
		if(empty($_GET['choose']) && su_check(SU_RIGHT_CASTLECHOOSE)){
			addnav("Superuser Wahl");
			addnav("Schloss",   "abandoncastle.php?choose=1");
			addnav("Irrgarten", "abandoncastle.php?choose=2");
		}
		elseif( $_GET['choose']==1 || (!su_check(SU_RIGHT_CASTLECHOOSE) && rand(1,24)%2)){
			output("Es scheint so, als ob Du einen anderen Ausgang aus diesem Schloss finden müsstest!`n");
			output("Du schaust Dich im schummrigen Licht der Eingangshalle um und stellst fest, dass diese ");
			output("verdreckt, staubig und voll von den Resten früherer Besucher ist. Na, wenn das mal kein gutes Omen ist...`n");
			if ($session['user']['hashorse']>0)
			{
				output("Schade eigentlich, dass Dein {$playermount['mountname']} `2hier nicht bei Dir ist.`n");
			}
			output("Außerdem bemerkst Du, dass dieser Ort irgendwie seltsam riecht-irgendwie magisch. Es scheint so, als ob all Deine Vorteile momentan vorüber wären...`n`n");
			
			if( $maze_disciple ){
				output("`2Lediglich `^".$kname."`2 steht dir jetzt noch treu zur Seite...`n`n");
			}
			
			$locale=6;
			
			
			// mod by talion: Maze aus DB abrufen
			$sql = 'SELECT m.maze,aei.maze AS mazeid,mazegold,mazegems,mazeturns
						FROM account_extra_info aei
						INNER JOIN mazes m ON m.mazeid=aei.maze
						WHERE acctid='.$session['user']['acctid'];
			
			$aei = db_fetch_assoc(db_query($sql));
			
			$str_maze = $aei['maze'];
			$int_maze = $aei['mazeid'];
			$int_mazegold = $aei['mazegold'];
			$int_mazegems = $aei['mazegems'];
			$int_mazeturns = $aei['mazeturns'];
			
			//they have to do an unfinished maze.
			// (Kein Maze gegeben)
			if ($str_maze=='')
			{
				$maze = '';
			
				$sql = 'SELECT maze,mazeid,mazegold,mazegems,mazeturns FROM mazes WHERE mazechance > 0 ORDER BY RAND('.e_rand().') LIMIT 1';
				$maze_row = db_fetch_assoc(db_query($sql));
				
				$str_maze = $maze_row['maze'];
				$int_maze = $maze_row['mazeid'];
				$int_mazegold = $maze_row['mazegold'];
				$int_mazegems = $maze_row['mazegems'];
				$int_mazeturns = $maze_row['mazeturns'];
										
				$sql = 'UPDATE account_extra_info SET maze='.$maze_row['mazeid'].' WHERE acctid='.$session['user']['acctid'];
				db_query($sql);
				  
			}
			
			// Hier sollte Maze als String auf jeden Fall gegeben sein
			// Nun als Array in Session speichern
			$session['maze'] = explode(',',$str_maze);
			$session['mazeid'] = $int_maze;
			$session['mazegold'] = $int_mazegold;
			$session['mazegems'] = $int_mazegems;
			$session['mazeturns'] = $int_mazeturns;
			
			
			addnav("Weiter","abandoncastle.php?loc=6");
		}
		else{
			$out  = "`2Du gehst durch das Verlassene Schloss hindurch und kommst in den Garten hinter dem Schloß.`n";
			$out .= "Kaum bist du im Garten angelangt merkst du auch schon, dass du in einem Labyrinth aus Bäumen steckst`n";
			$out .= "Außerdem bemerkst Du, dass dieser Ort irgendwie nach exotischen Blumen riecht-irgendwie magisch. Es scheint so, als ob all Deine Vorteile momentan vorüber wären...`n`n";
			
			if( $maze_disciple ){
				$out .= "`2Lediglich `^".$kname."`2 steht dir jetzt noch treu zur Seite...`n`n";
			}
			
			output($out);
			addnav("Weiter", "gardenmaze.php?init=1");
		}
    }
    else
    {
        output("Du rüttelst an der Tür des riesigen verlassenen Anwesens, aber die Tür gibt keinen Millimeter nach und Du kommst somit nicht hinein.`n");
        output("Vielleicht solltest Du wiederkommen wenn Du etwas stärker geworden bist. Du könntest ja noch ".$int_dragonkills_to_go." Drachen töten, dann klappt es bestimmt.`n");
        addnav("Weiter","village.php");
    }
}
// END intro


//now let's navigate the maze
if ($_GET[op] <> "")
{
    $locale=$_GET[loc];
    if ($_GET[op] == "n")
    {
        $locale+=11;
        redirect("abandoncastle.php?loc=$locale");
    }
    if ($_GET[op] == "s")
    {
        $locale-=11;
        redirect("abandoncastle.php?loc=$locale");
    }
    if ($_GET[op] == "w")
    {
        $locale-=1;
        redirect("abandoncastle.php?loc=$locale");
    }
    if ($_GET[op] == "e")
    {
        $locale+=1;
        redirect("abandoncastle.php?loc=$locale");
    }
}
else
{
    if ($_GET[loc] <> "")
    {
        //now deal with random events good stuff first
        //good stuff diminshes the longer player is in the maze
        //this is big... with lots of cases to help keep options open for future events
        //the lower cases should be good things the best at the lowest number
        //and the opposite for bad things
        
        // Bei jedem Schritt überprüfen, ob Maze noch in der Session
        if (!is_array($session['maze']))
        {
            // Sonst Abruf aus DB
            $sql = 'SELECT m.maze, m.mazeid,mazegold,mazegems,mazeturns 
					FROM account_extra_info aei LEFT JOIN mazes m ON m.mazeid=aei.maze
					WHERE acctid='.$session['user']['acctid'];
            $aei = db_fetch_assoc(db_query($sql));
            $session['maze'] = explode(',',$aei['maze']);
			$session['mazeid'] = $aei['mazeid'];
			$session['mazegold'] = $aei['mazegold'];
			$session['mazegems'] = $aei['mazegems'];
			$session['mazeturns'] = $aei['mazeturns'];
        }
        
        $maze = $session['maze'];
        
        $locale=$_GET['loc'];
        if ($locale=="")
        {
            $locale=$session['user']['pqtemp'];
        }
        $session['user']['pqtemp']=$locale;
        for ($i=0; $i<$locale-1; $i++)
        {
        }
        $navigate=ltrim($maze[$i]);
        $out = "";
        
        if ($navigate <> "z")
        {
            
            $val_min = (!$session['user']['turns']) ? 200 : 0;
            
            $val_min += $session['user']['mazeturn'];
			            
            if ($session['user']['mazeturn']>0)
			
			$int_decide = e_rand($val_min,2500);
			
			// Edelsteinbuff
			if ($session['bufflist']['gemelster'] && e_rand(1,5) == 1)
            {
                output('`c'.$session['bufflist']['gemelster']['effectmsg'].'`n');
                $session['bufflist']['gemelster']['rounds']--;
				
				if(e_rand(1,3) == 1) {
					$int_decide = 1;
				}
				else {
					output($session['bufflist']['gemelster']['failmsg'].'`n');
				}
				
                if ($session['bufflist']['gemelster']['rounds'] <= 0)
                {
                    output($session['bufflist']['gemelster']['wearoff'].'`n');
                    unset($session['bufflist']['gemelster']);
                }
				
				output('`c`n`n');
												
            }
			
            switch($int_decide)
            {
            case 1:
            case 2:
            case 3:
            case 4:
            case 5:
            case 6:
            case 7:
            case 8:
            case 9:
            case 10:
                $out = "Glück gehabt!  Du findest einen Edelstein!";
                $session['user']['gems']+=1;
                break;
            case 11:
            case 12:
            case 13:
            case 14:
            case 15:
            case 16:
            case 17:
            case 18:
            case 19:
            case 20:
                $out = "Glück gehabt! Du findest 100 Gold!";
                $session['user']['gold']+=100;
                break;
            case 21:
            case 22:
            case 23:
            case 24:
            case 25:
            case 26:
            case 27:
            case 28:
            case 29:
            case 30:
            case 31:
            case 32:
            case 33:
            case 34:
            case 35:
            case 36:
            case 37:
            case 38:
            case 39:
            case 40:
                $locale=$_GET[loc];
                if ($session['user']['turns'] > 0)
                {
                    redirect("castleevents.php?op=fairy&loc=$locale&op2=spec");
                }
                break;
            case 41:
            case 42:
            case 43:
            case 44:
            case 45:
            case 46:
            case 47:
            case 48:
            case 49:
            case 50:
            case 51:
            case 52:
            case 53:
            case 54:
            case 55:
                $locale=$_GET[loc];
                if ($session['user']['turns'] > 0)
                {
                    redirect("castleevents.php?op=web&loc=$locale&op2=spec");
                }
                break;
            case 56:
            case 57:
            case 58:
            case 59:
            case 60:
            case 61:
            case 62:
            case 63:
            case 64:
            case 65:
            case 66:
            case 67:
            case 68:
                $locale=$_GET[loc];
                if ($session['user']['turns'] > 0)
                {
                    redirect("castleevents.php?op=well&loc=$locale&op2=spec");
                }
                break;
            case 69:
            case 70:
            case 71:
            case 72:
            case 73:
            case 74:
            case 75:
            case 76:
            case 77:
            case 78:
            case 79:
            case 80:
            case 81:
            case 82:
            case 83:
            case 84:
            case 85:
            case 86:
            case 87:
                if ($session['user']['turns'] > 0)
                {
                    redirect("castleevents.php?op=gang&loc=$locale&op2=spec");
                }
                break;
            case 88:
            case 89:
            case 90:
            case 91:
            case 92:
            case 93:
            case 94:
            case 95:
            case 96:
            case 97:
            case 98:
                $locale=$_GET[loc];
                if ($session['user']['turns'] > 0)
                {
                    redirect("castleevents.php?op=hole&loc=$locale&op2=spec");
                }
                break;
            case 99:
            case 100:
                // $locale=$HTTP_GET_VARS[loc];
                // redirect ("castleevents.php?op=earthshrine&loc=$locale&op2=spec");
                break;
            case 101:
            case 102:
                $locale=$_GET[loc];
                if ($session['user']['turns'] > 0)
                {
                    redirect("castleevents.php?op=airshrine&loc=$locale&op2=spec");
                }
                break;
            case 103:
            case 104:
                // $locale=$HTTP_GET_VARS[loc];
                // redirect ("castleevents.php?op=fireshrine&loc=$locale&op2=spec");
                break;
            case 105:
            case 106:
                // $locale=$HTTP_GET_VARS[loc];
                // redirect ("castleevents.php?op=watershrine&loc=$locale&op2=spec");
                break;
            case 107:
            case 108:
                // $locale=$HTTP_GET_VARS[loc];
                // redirect ("castleevents.php?op=spiritshrine&loc=$locale&op2=spec");
                break;
            case 109:
            case 110:
            case 111:
            case 112:
            case 113:
            case 114:
            case 115:
            case 116:
            case 117:
            case 118:
            case 119:
            case 120:
            case 121:
            case 122:
                $locale=$_GET[loc];
                if ($session['user']['turns'] > 0)
                {
                    redirect("castleevents.php?op=truhe&loc=$locale&op2=spec");
                }
                break;
                
                //comment out potions for if you are not using potion mod!
                /*		if ($session['user']['potion']<5)
                {
                    output("Glück gehabt! Du findest einen Heiltrank!");
                    $session['user']['potion']+=1;
                }
                break;
            case 123:
            case 124:
                //comment out chow if you are not using chow mod!
                for ($i=0; $i<6; $i+=1)
                {
                    $chow[$i]=substr(strval($session['user']['chow']),$i,1);
                    if ($chow[$i] > 0)
                    {
                        $userchow++;
                    }
                }
                if ($userchow<5)
                {
                    switch (e_rand(1,7))
                    {
                    case 1:
                        output("`^Was für ein Dusel - Du findest eine scheibe Brot!`0");
                        for ($i=0; $i<6; $i+=1)
                        {
                            $chow[$i]=substr(strval($session['user']['chow']),$i,1);
                            if ($chow[$i]=="0" and $done < 1)
                            {
                                $chow[$i]="1";
                                $done = 1;
                            }
                            $newchow.=$chow[$i];
                        }
                        break;
                    case 2:
                        output("`^Was für ein Dusel - Du findest eine Schweinekeule!`0");
                        for ($i=0; $i<6; $i+=1)
                        {
                            $chow[$i]=substr(strval($session['user']['chow']),$i,1);
                            if ($chow[$i]=="0" and $done < 1)
                            {
                                $chow[$i]="2";
                                $done = 1;
                            }
                            $newchow.=$chow[$i];
                        }
                        break;
                    case 3:
                        output("`^Was für ein Dusel - Du findest ein Hacksteak!`0");
                        for ($i=0; $i<6; $i+=1)
                        {
                            $chow[$i]=substr(strval($session['user']['chow']),$i,1);
                            if ($chow[$i]=="0" and $done < 1)
                            {
                                $chow[$i]="3";
                                $done = 1;
                            }
                            $newchow.=$chow[$i];
                        }
                        break;
                    case 4:
                        output("`^Was für ein Dusel - Du findest ein Steak!`0");
                        for ($i=0; $i<6; $i+=1)
                        {
                            $chow[$i]=substr(strval($session['user']['chow']),$i,1);
                            if ($chow[$i]=="0" and $done < 1)
                            {
                                $chow[$i]="4";
                                $done = 1;
                            }
                            $newchow.=$chow[$i];
                        }
                        break;
                    case 5:
                        output("`^Was für ein Dusel - Du findest ein ganzes Hühnchen!`0");
                        for ($i=0; $i<6; $i+=1)
                        {
                            $chow[$i]=substr(strval($session['user']['chow']),$i,1);
                            if ($chow[$i]=="0" and $done < 1)
                            {
                                $chow[$i]="5";
                                $done = 1;
                            }
                            $newchow.=$chow[$i];
                        }
                        break;
                    case 6:
                        output("`^Was für ein Dusel - Du findest eine Flasche Milch!`0");
                        for ($i=0; $i<6; $i+=1)
                        {
                            $chow[$i]=substr(strval($session['user']['chow']),$i,1);
                            if ($chow[$i]=="0" and $done < 1)
                            {
                                $chow[$i]="6";
                                $done = 1;
                            }
                            $newchow.=$chow[$i];
                        }
                        break;
                    case 7:
                        output("`^Was für ein Dusel - Du findest eine Flasche Wasser!`0");
                        for ($i=0; $i<6; $i+=1)
                        {
                            $chow[$i]=substr(strval($session['user']['chow']),$i,1);
                            if ($chow[$i]=="0" and $done < 1)
                            {
                                $chow[$i]="7";
                                $done = 1;
                            }
                            $newchow.=$chow[$i];
                        }
                        break;
                    }
                    $session['user']['chow']=$newchow;
                }
                break;
                */
            case 125:
            case 126:
            case 127:
            case 128:
            case 129:
            case 130:
                $out = "Glück gehabt! Du findest 10 Gold!";
                $session['user']['gold']+=10;
                break;
            case 131:
            case 132:
            case 133:
            case 134:
            case 135:
            case 136:
            case 137:
            case 138:
            case 139:
            case 140:
                // output("Du findest ");
                //comment out if you are not using the trading mod and lonny's castle!
                //$session['user']['evil']-=1;
                //find();
                break;
                
            case 2323:
            case 2324:
            case 2325:
            case 2326:
            case 2327:
                if ($session['user']['turns'] > 0)
                {
                    redirect("castleevents.php?op=corpse");
                }
                break;
            case 2328:
            case 2329:
            case 2330:
            case 2331:
            case 2332:
            case 2333:
            case 2334:
            case 2335:
            case 2336:
                $out = "Du hörst einen markerschütternden Schrei, der von irgednwoher zu kommen scheint.";
                break;
            case 2337:
            case 2338:
            case 2339:
            case 2340:
            case 2341:
            case 2342:
            case 2343:
            case 2344:
                $out = "Du läufst durch einen grausig stinkenden Abschnitt des Schlosses.";
                //	$session['user']['clean']+=1;
                break;
            case 2345:
            case 2346:
            case 2347:
            case 2348:
            case 2349:
            case 2350:
            case 2351:
            case 2352:
                if ($session['user']['turns'] > 0)
                {
                    redirect("mazemonster.php?op=bigbat");
                }
                break;
            case 2353:
            case 2354:
            case 2355:
                $out = "Da liegt ein Skelett auf dem Boden-Armer Kerl, der hat den Weg wohl nicht heraus gefunden.";
                break;
            case 2356:
            case 2357:
            case 2358:
            case 2359:
            case 2360:
            case 2361:
            case 2362:
                $out = "Du siehst eine Ratte, die auf etwas herumkaut, dass wie eine Hand aussieht.";
                break;
            case 2363:
            case 2364:
            case 2365:
            case 2366:
            case 2367:
            case 2368:
            case 2369:
            case 2370:
            case 2371:
            case 2372:
                $out = "Ganz in Deiner Nähe hörst Du ein Grollen.";
                break;
            case 2373:
            case 2374:
            case 2375:
            case 2376:
            case 2377:
            case 2378:
            case 2379:
                $out = "Ein eisiger Schauer überkommt Dich.";
                break;
            case 2380:
            case 2381:
            case 2382:
            case 2383:
                if ($session['user']['turns'] > 0)
                {
                    redirect("mazemonster.php?op=lost_soul");
                }
                break;
            case 2384:
            case 2385:
            case 2386:
            case 2387:
            case 2388:
                $out = "Du hörst einen Hilferuf von irgendwoher.";
                break;
            case 2389:
            case 2390:
            case 2391:
            case 2392:
            case 2393:
                if ($session['user']['turns'] > 0)
                {
                    redirect("mazemonster.php?op=devoured_soul");
                }
                break;
            case 2394:
            case 2395:
            case 2396:
            case 2397:
            case 2398:
            case 2399:
            case 2400:
            case 2401:
                $out = "Ein Hilferuf, ganz nah! Aber wo?";
                break;
            case 2402:
            case 2403:
            case 2404:
            case 2405:
            case 2406:
            case 2407:
            case 2408:
            case 2409:
            case 2410:
            case 2411:
            case 2412:
                $out = "Du hörst einen Hilferuf von sehr nah. Urplötzlich verstummt der Schrei.";
                break;
            case 2413:
            case 2414:
            case 2415:
            case 2416:
                $out = "Autsch! Du bist auf etwas scharfes getreten! Gift strömt durch deinen Körper.";
                $session['user']['hitpoints'] = max($session['user']['hitpoints'] * 0.15, 1);
                $session['user']['turns'] = max($session['user']['turns']-1,0);
                break;
            case 2417:
            case 2418:
            case 2419:
            case 2420:
            case 2421:
            case 2422:
                $out = "Autsch! Eine Spinne hat Dich gebissen!";
                $session['user']['hitpoints']-=10;
                if ($session['user']['hitpoints']<1)
                {
                    $session['user']['hitpoints']=1;
                }
                break;
            case 2423:
            case 2424:
            case 2425:
            case 2426:
            case 2427:
            case 2428:
            case 2429:
                if ($session['user']['turns'] > 0)
                {
                    redirect("mazemonster.php?op=ghost3");
                }
                break;
            case 2430:
            case 2431:
            case 2432:
            case 2433:
            case 2434:
                $out = "Autsch! Eine Ratte hat Dich gebissen und anschließend aus dem Staub gemacht.";
                $session['user']['hitpoints']-=10;
                if ($session['user']['hitpoints']<1)
                {
                    $session['user']['hitpoints']=1;
                }
                break;
            case 2435:
            case 2436:
            case 2437:
            case 2438:
            case 2439:
                $out = "Autsch!Eine große Ratte hat Dich gebissen und sich anschließend aus dem Staub gemacht.";
                $session['user']['hitpoints']-=15;
                if ($session['user']['hitpoints']<1)
                {
                    $session['user']['hitpoints']=1;
                }
                break;
            case 2440:
            case 2441:
            case 2442:
            case 2443:
            case 2444:
            case 2445:
                if ($session['user']['turns'] > 0)
                {
                    redirect("mazemonster.php?op=ghost2");
                }
                break;
            case 2446:
            case 2447:
            case 2448:
            case 2449:
            case 2450:
            case 2451:
                if ($session['user']['turns'] > 0)

                {
                    redirect("mazemonster.php?op=rat");
                }
                break;
            case 2452:
            case 2453:
            case 2454:
            case 2455:
            case 2456:
            case 2457:
            case 2458:
                if ($session['user']['turns'] > 0)
                {
                    redirect("mazemonster.php?op=ghost1");
                }
                break;
            case 2459:
            case 2460:
            case 2461:
                if ($session['user']['turns'] > 0)
                {
                    redirect("mazemonster.php?op=bat");
                }
                break;
            case 2462:
            case 2463:
            case 2464:
				if ($session['user']['turns'] > 0)
                {
					redirect("mazemonster.php?op=minotaur");
				}
                break;
            case 2467:
            case 2468:
            case 2469:
            case 2470:
            case 2471:
            case 2472:
            case 2473:
            case 2474:
            case 2475:
            case 2476:
                $locale=$_GET[loc];
                if ($session['user']['turns'] > 0)
                {
                    redirect("castleevents.php?op=truhe&loc=$locale&op2=spec");
                }
                break;
            case 2477:
            case 2478:
            case 2479:
            case 2480:
            case 2481:
            case 2482:
                $locale=$_GET[loc];
                if ($session['user']['turns'] > 0)
                {
                    redirect("castleevents.php?op=vamp&loc=$locale&op2=spec");
                }
                break;
            case 2483:
            case 2484:
            case 2485:
            case 2486:
            case 2487:
            case 2488:
            case 2489:
                $locale=$_GET[loc];
                if ($session['user']['turns'] > 0)
                {
                    redirect("castleevents.php?op=hebel&loc=$locale&op2=spec");
                }
                break;
            case 2490:
            case 2491:
            case 2492:
            case 2493:
            case 2494:
                // redirect("castleevents.php?op=teleport");
                break;
            case 2495:
            case 2496:
            case 2497:
                redirect("castleevents.php?op=wham");
                break;
            case 2498:
            case 2499:
            case 2500:
                redirect("castleevents.php?op=shoop");
                break;
            }
        }
        
        if ($navigate<>"z")
        {
            if ($navigate=="x")
            {
                $out .= "Du fällst vom Ende der Welt hinunter!";
                $session['user']['hitpoints']=0;
                $session['user']['experience']-=100;
                addnews("`%".$session['user']['name']."`5 ging in das verlassene Schloss, kam aber nie wieder lebendig heraus!.");
            }
            if ($navigate=="p")
            {
                $out .= "Du fällst in eine Fallgrube, in der Dich Speere durchbohren`n";
                $session['user']['hitpoints']=0;
                $session['user']['experience']-=100;
                addnews("`%".$session['user']['name']."`5 ging in das verlassene Schloss, kam aber nie wieder lebendig heraus!.");
            }
            if ($navigate=="q")
            {
                $out .= "Du trittst auf etwas und hörst noch das bedrohliche Dröhnen, kurz bevor sich der Gang mit Wasser füllt-genau wie Deine Lungen!";
                $session['user']['hitpoints']=0;
                $session['user']['experience']-=100;
                addnews("`%".$session['user']['name']."`5 ging in das verlassene Schloss, kam aber nie wieder lebendig heraus!.");
            }
            if ($navigate=="r")
            {
                $out .= "Hinter Dir hörst Du einen lauten Knall. Als Du herumwirbelst, sieht Du, dass eine Tür hinter Dir zugeschlagen ist und Dir den Rückweg versperrt.";
                $out .= "Plötzlich beginnen sich Dir die Wände zu nähern und Du kannst schnell feststellen, wie es für einen Käfer sein muss, wenn er unter deinem Schuh klebt!";
                $session['user']['hitpoints']=0;
                $session['user']['experience']-=100;
                addnews("`%".$session['user']['name']."`5 ging in das verlassene Schloss, kam aber nie wieder lebendig heraus!.");
            }
            if ($navigate=="s")
            {
                $out .= "Scheinbar von nirgednwo schwingt plötzlich eine Klinge quer durch den Raum und quer durch Dich!`n";
                $session['user']['hitpoints']=0;
                $session['user']['experience']-=100;
                addnews("`%".$session['user']['name']."`5 ging in das verlassene Schloss, kam aber nie wieder lebendig heraus!.");
            }
			
			if( !empty($out) ){
				output("`c`b`&&raquo;&raquo;&raquo; Besonderes &laquo;&laquo;&laquo;`b`n`4".$out."`c",true);
			}
			$out = "";
			
            $special=$_GET['op2'];
            if (($session['user']['hitpoints'] > 0) && ($special!="spec"))
            {
                if ($locale=="6")
                {
                    $out = "`nDu stehst im Eingang, welcher Verzweigungen in folgende Richtungen enthält:`n";
                }
                else
                {
                    $out = "`nDu bist in einem Gang mit Verzweigungsmöglichkeiten nach:`n";
                }
                $session['user']['mazeturn']++;
                savesetting("CASTLEMOVES",getsetting("CASTLEMOVES",0)+1);
                if ($navigate=="a" or $navigate=="b" or $navigate=="e" or $navigate=="f" or $navigate=="g" or $navigate=="j" or $navigate=="k" or $navigate=="l")
                {
                    $str_lnk = addnav("Norden","abandoncastle.php?op=n&loc=$locale");
					
					$quickkeys['arrowup'] = "window.location='$str_lnk'";
					
                    $directions.=" Norden";
                    $navcount++;
                }
                if ($navigate=="a" or $navigate=="c" or $navigate=="e" or $navigate=="f" or $navigate=="g" or $navigate=="h" or $navigate=="i" or $navigate=="m")
                {
                    if ($locale <> 6)
                    {
                        $str_lnk = addnav("Süden","abandoncastle.php?op=s&loc=$locale");
						
						$quickkeys['arrowdown'] = "window.location='$str_lnk'";
						
                        $navcount++;
                        if ($navcount > 1)
                        {
                            $directions.=",";
                        }
                        $directions.=" Süden";
                    }
                }
                if ($navigate=="a" or $navigate=="b" or $navigate=="c" or $navigate=="d" or $navigate=="e" or $navigate=="h" or $navigate=="k" or $navigate=="n")
                {
                    $str_lnk = addnav("Westen","abandoncastle.php?op=w&loc=$locale");
					
					$quickkeys['arrowleft'] = "window.location='$str_lnk'";
					
                    $navcount++;
                    if ($navcount > 1)
                    {
                        $directions.=",";
                    }
                    $directions.=" Westen";
					
					
					
                }
                if ($navigate=="a" or $navigate=="b" or $navigate=="c" or $navigate=="d" or $navigate=="f" or $navigate=="i" or $navigate=="j" or $navigate=="o")
                {
                    $str_lnk = addnav("Osten","abandoncastle.php?op=e&loc=$locale");
					
					$quickkeys['arrowright'] = "window.location='$str_lnk'";
					
                    $navcount++;
                    if ($navcount > 1)
                    {
                        $directions.=",";
                    }
                    $directions.=" Osten";
                }
                
                
            }
            else
            {
                addnav("Weiter","shades.php");
            }
            //user map generation.... may make code to grey spots that a player has been
			$out .= "`b".$directions."`b";
			$out .= '`n(Navigation: Mit den Pfeiltasten oder über die Hotkeys)';
			
            $mazemap=$navigate;
            $mazemap.="maze.gif";
            //$out .= "`n";
            
            $out .= "`n`n<style>
			td.mazefield{
				border-width:0px;
				border-color: gray;
				border-style: solid;
				border-bottom-width: 1px;
				border-right-width: 1px;
				padding: 0px;
				margin:0px;			
			}
			
			
			</style>
			
			<table border=\"0\" cellpadding=\"0\" colspan=\"0\" rowspan=\"0\" cellspacing=\"0\"><tr>";
			$mapkey2 = "";
			$img_style = "display:block;border-width:0px;width: 10px; height: 10px;";
			for ($i=0; $i<143; $i++)
            {
				
				$field_style = "";

				if( !($i % 11) ){
					$field_style = "border-left-width: 1px;";
				}
				
				$mapkey .= "<td class=\"mazefield\" style=\"".$field_style."\">";
				
				
				
                if ($i==$locale-1)
                {
                    $mapkey.="<img src=\"./images/mcyan.gif\" title=\"\" alt=\"\" style=\"".$img_style."\">";
                }
                else
                {
                    if ($i==5)
                    {
                        $mapkey.="<img src=\"./images/mgreen.gif\" title=\"\" alt=\"\" style=\"".$img_style."\">";
                    }
                    else
                    {
                        if (ltrim($maze[$i])=="z")
                        {
                            $exit=$i+1;
                            $mapkey.="<img src=\"./images/mred.gif\" title=\"\" alt=\"\" style=\"".$img_style."\">";
                        }
                        else
                        {
                            $mapkey.="<img src=\"./images/mblack.gif\" title=\"\" alt=\"\" style=\"".$img_style."\">";
                        }
                    }
                }
				$mapkey .= "</td>\n";
                if ($i==10 or $i==21 or $i==32 or $i==43 or $i==54 or $i==65 or $i==76 or $i==87 or $i==98 or $i==109 or $i==120 or $i==131 or $i==142)
                {
					if( $i == 142 ){
						$mapkey .= "<td rowspan=\"12\" style=\"padding-left:5px; text-align: left;vertical-align:top;\">
									<img src=\"./images/mcyan.gif\" title=\"\" alt=\"\" style=\"width: 7px; height: 7px;\"> = `7Du`n
									<img src=\"./images/mgreen.gif\" title=\"\" alt=\"\" style=\"width: 7px; height: 7px;\">`7 = Eingang`n
									<img src=\"./images/mred.gif\" title=\"\" alt=\"\" style=\"width: 7px; height: 7px;\">`7 = Ausgang`n
									<img style=\"border-width:1px; border-color: gray;border-style: dotted;\" src=\"images/".$mazemap."\">
									</td>";
					}
					
					$mapkey = "<tr>".$mapkey."</tr>";
                    $mapkey2=$mapkey.$mapkey2;
                    $mapkey="";
                }
            }
			
			$mapkey2 = "<tr><td colspan=\"11\" style=\"border-width:1px; border-color: gray;border-style: solid;text-align: center;vertical-align:middle;\">`b`&Karte`b</td></tr>".$mapkey2;

			
			$out .= $mapkey2."</table>";
            output("`c`7".$out."`c",true);
            if (su_check(SU_RIGHT_CASTLEMAP))
            {
                output("Superuser Map`n");
                $mapkey2="";
                $mapkey="";
                for ($i=0; $i<143; $i++)
                {
                    $keymap=ltrim($maze[$i]);
                    $mazemap=$keymap;
                    $mazemap.="maze.gif";
                    $mapkey.="<img src=\"./images/$mazemap\" title=\"\" alt=\"\" style=\"width: 20px; height: 20px;\">";
                    if ($i==10 or $i==21 or $i==32 or $i==43 or $i==54 or $i==65 or $i==76 or $i==87 or $i==98 or $i==109 or $i==120 or $i==131 or $i==142)
                    {
                        $mapkey="`n".$mapkey;
                        $mapkey2=$mapkey.$mapkey2;
                        $mapkey="";
                    }
                }
                output($mapkey2,true);
            }
            if (su_check(SU_RIGHT_CASTLECHOOSE))
            {
				
				addnav('Spinnwebenereignis','castleevents.php?op=web&loc='.$locale);
				addnav('Leichenereignis','castleevents.php?op=corpse&loc='.$locale);
				
				$sql = 'SELECT * FROM mazes ORDER BY mazeid';
				$res = db_query($sql);
											
				$link = 'abandoncastle.php?suentry=1';
				addnav('',$link);
												
				$mazes = '';
					
				while($m = db_fetch_assoc($res)) {
					
					$mazes .= '<option value="'.$m['mazeid'].'" '.($session['mazeid'] == $m['mazeid'] ? 'selected="selected"': '').'>'.$m['mazetitle'].'</option>';
					
				}
				
				output('<form method="POST" action="'.$link.'">
							`bMaze:`b <select name="maze" size="1" onchange="this.form.submit()">'.$mazes.'</select>
						</form>`n',true);
											
                addnav("X?Superuser Exit","abandoncastle.php?loc=$exit");
            }
        }
        else
        {
            //found your way out!
            if (!is_array($session['bufflist']) || (count($session['bufflist']) <= 0) || (is_array($session['bufflist']['decbuff'])))
            {
                if (is_array($session['bufflist']['decbuff']))
                {
                    $decbuff=$session['bufflist']['decbuff'];
                }
                $session['bufflist'] = unserialize($session['user']['buffbackup']);
                if (is_array($decbuff))
                {
                    $session['bufflist']['decbuff']=$decbuff;
                }
                if (!is_array($session['bufflist']))
                {
                    $session['bufflist'] = array();
                }
            }
            if ($session['user']['hashorse']>0)
            {
                output("Dein {$playermount['mountname']} begrüsst Dich erfreut am Ausgang.`n");
            }
            output("Du hast einen Weg nach draussen gefunden!`n");
            
            //$int_turns_to_take = min((int)($session['user']['mazeturn'] / getsetting("castle_turns_to_take",10) ) , 8);
            
            //$session['user']['turns']= max($session['user']['turns']-$int_turns_to_take, 0);
            
            //output("Da der Besuch des Schlosses sehr langwierig und erschöpfend war, verlierst Du $int_turns_to_take Waldkämpfe`n");
            
            output("Da der Besuch des Schlosses sehr langwierig und erschöpfend war, verlierst Du eine Schlossrunde`n");
            
            //addnews("`%".$session[user][name]."`5 hat das verlassene Schloss lebendig verlassen!  Und das in nur ".$session['user']['mazeturn']." Zügen!");            
			
			$int_turns_over = max($session['user']['mazeturn'] - $session['mazeturns'],0);
			
			// Verlustrate einberechnen
			$int_reward = $session['mazegold'] - $int_turns_over * (int)getsetting('castlegolddesc',50);			
			$int_reward = max(0,$int_reward);
			$int_gemreward = $session['mazegems'] - ceil($int_turns_over * getsetting('castlegemdesc',0.1));
			$int_gemreward = max(0,$int_gemreward);
               
            output("`2Du hast das Labyrinth in ".$session['user']['mazeturn']." Zügen überstanden.`n");
            output("`2Du findest einen Schatz von ".$int_reward." Gold und ".$int_gemreward." Edelsteinen.`n`n");
            
            // GILDENMOD
            require_once(LIB_PATH.'dg_funcs.lib.php');
            if ($session['user']['guildid'] && $session['user']['guildfunc'] != DG_FUNC_APPLICANT)
            {
                
                $tribute = dg_member_tribute($session['user']['guildid'],$int_reward,$int_gemreward);
                dg_save_guild();
                if ($tribute[0] > 0 || $tribute[1] > 0)
                {
                    output('`2Davon zahlst du `^'.$tribute[0].'`2 Goldstücke und `^'.$tribute[1].'`2 Edelsteine Tribut an deine Gilde.`n');
                    $int_reward -= $tribute[0];
                    $int_gemreward -= $tribute[1];
                }
            }
            // END GILDENMOD
            
            addnav("Weiter","village.php");
            $session['user']['gold']+=$int_reward;
            $session['user']['gems']+=$int_gemreward;
            
            // Wir sind draußen, also auch Maze leeren.
            $sql = 'UPDATE account_extra_info SET maze=0 WHERE acctid='.$session['user']['acctid'];
            db_query($sql);
            unset($session['maze']);
            unset($session['mazeid']);
            
            $session['user']['mazeturn']=0;
            $session['user']['pqtemp']="";
        }
    }
}
//I cannot make you keep this line here but would appreciate it left in.
//rawoutput("<div style=\"text-align: left;\"><a href=\"http://www.pqcomp.com\" target=\"_blank\">Verlassenes Schloss by Lonny @ http://www.pqcomp.com</a><br>");
page_footer();
?>
