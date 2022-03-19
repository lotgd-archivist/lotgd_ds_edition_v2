<?
/*
@file gardenmaze.php
@desc Addon fürs verlassene Schloss (Anti-"Auswendiglerner")
@author Sven-Michael "Alucard" Stübe
*/
require_once 'common.php';
//checkday();
page_header("Der Irrgarten des verlassenen Schloßes");
define("WALL_WEST", 	8);
define("WALL_SOUTH", 	4);
define("WALL_EAST", 	2);
define("WALL_NORTH", 	1);
//simple mazegeneration ;>
/*
	walls = W | S | E | N
	TODO: perfomance untersuchen
*/


//Position
$g_pos=$_GET['pos'];
if ($g_pos=="")
{
	$g_pos=$session['user']['pqtemp'];
}
$session['user']['pqtemp']=$g_pos;

function maze_initalize()
{
	global $session;
	$session['maze_map'] = "";
	$nVisited 	= 1;
	$nTotal		= 144;
	$nCurr		= e_rand(1,$nTotal)-1;
	$stack 		= array();
	$maze 		= array_fill(0, $nTotal, 15);
	
	
	while( $nVisited < $nTotal ){
		$find 	= array();//pos, drop wallcurr, drop wallthis
		$found 	= 0;
		for($i=0; $i < 4; $i++){
			switch($i){
				case 0:
					if( $nCurr % 12 ){//westwand?
						if( $maze[ $nCurr-1 ] == 15){
							array_push($find, array($nCurr-1, WALL_WEST, WALL_EAST));
							$found++;
						}
					}
					
				break;
				
				case 1:	
					if( $nCurr < 132 ){ //südwand?
						if( $maze[ $nCurr+12 ] == 15 ){
							array_push($find, array($nCurr+12, WALL_SOUTH, WALL_NORTH));
							$found++;
						}
					}
				
				break;
				
				case 2:
					if( ($nCurr+1) % 12){ //ostwand?
						if( $maze[ $nCurr+1 ] == 15 ){
							array_push($find, array($nCurr+1, WALL_EAST, WALL_WEST));
							$found++;
						}
					}
				break;
				
				case 3:
					if( $nCurr > 11 ){ //nordwand?
							if( $maze[ $nCurr-12 ] == 15 ){
							array_push($find, array($nCurr-12, WALL_NORTH, WALL_SOUTH));
							$found++;
						}
					}
				break;
			}			
		}
		
		if($found){
			$next = $find[e_rand(0,$found-1)];
			$maze[ $next[0] ] ^= $next[2];
			$maze[  $nCurr 	] ^= $next[1];
			array_push($stack, $nCurr);
			$nCurr = $next[0];
			$nVisited++;
		}
		else{
			$nCurr = array_pop($stack);
		}
	
	}
	
	$randomdrop = e_rand(5,15);
	for($i=0; $i<$randomdrop; ++$i){
		$dropwall = (1<<e_rand(0,3));
		$dropfield = e_rand(1,143);
		if( !($maze[ $dropfield ] & $dropwall) ){
			continue;
		}
		
		switch( $dropwall ){
			case WALL_NORTH:
				if( $dropfield > 11 ){ //nordwand?
					$maze[ $dropfield ] 	^= $dropwall;
					$maze[ $dropfield-12 ] 	^= WALL_SOUTH;
				}			
			break;
			
			case WALL_EAST:
				if( ($dropfield+1) % 12){ //ostwand?
					$maze[ $dropfield ] 	^= $dropwall;
					$maze[ $dropfield+1 ] 	^= WALL_WEST;
				}
			break;
			
			case WALL_SOUTH:
				if( $dropfield < 132 ){ //südwand?
					$maze[ $dropfield ] 	^= $dropwall;
					$maze[ $dropfield+12 ] 	^= WALL_NORTH;
				}
			break;
			
			case WALL_WEST:
				if( $dropfield % 12 ){//westwand?
					$maze[ $dropfield ] 	^= $dropwall;
					$maze[ $dropfield-1 ] 	^= WALL_EAST;	
				}
			break;			
		}
	}
	
	$maze[  0  ] ^= WALL_NORTH;
	$maze[ 143 ] ^= WALL_SOUTH;
	
	$session['maze_map'] 					= implode(",", $maze);
	user_set_aei(array('maze_map' => $session['maze_map']));
	$session['user']['maze_visited'] 		= implode("", array_fill(0,$nTotal,0));
	$session['user']['maze_visited'][ 0 ] 	= 1;
}



function maze_draw()
{
	global $session, $g_pos, $quickkeys;
	if( !isset($session['maze_map']) ){
		$load = user_get_aei("maze_map");
		$session['maze_map'] = $load['maze_map'];
	}
	
	$maze = explode(",", $session['maze_map']);
	$out  = "<style>td.maze{border-width:0px; padding:0px; margin:0px;}\n
					img.maze{border-width:0px; padding:0px; margin:0px; display: block;}</style>";
	$out .= "<div><div style=\"position: relative;z-index: 2;";
	$out .= "left:".($g_pos%12*30)."px;top:".(floor($g_pos/12)*30)."px;\"><img class=\"maze\" src=\"./images/maze/maze_".($session['user']['sex']?"w":"m")."player.gif\"></div>";
	$out .= "<table style=\"position: relative;z-index: 1; top: -30px;\" border=\"0\" cellpadding=\"0\" colspan=\"0\" rowspan=\"0\" cellspacing=\"0\"><tr>";
	
	for($i=0; $i<144; ++$i){
		if( !($i % 12) && $i){
			$out .= "</tr>\n";
		}
		$out .= "<td><img class=\"maze\" border=\"0\" src=\"./images/maze/";
		if( $i == 143 ){
			$out .= "gold";
		}
		elseif($session['user']['maze_visited'][ $i ]){
			$out .= "_".$maze[ $i ];
		}
		else{
			$out .= "black";
		}
		$out .= ".gif\" height=\"30\" width=\"30\"></td>";
		
	}
	$out .= "</tr></table></div>";
	if( $g_pos < 143 ){ 
		if( $g_pos && !($maze[ $g_pos ]&WALL_NORTH) ){
			$str_lnk = addnav("Norden", "gardenmaze.php?pos=".($g_pos-12));
			$quickkeys['arrowup'] = "window.location='$str_lnk'";
		}
		
		if( !($maze[ $g_pos ]&WALL_EAST) ){
			$str_lnk = addnav("Osten", "gardenmaze.php?pos=".($g_pos+1));
			$quickkeys['arrowright'] = "window.location='$str_lnk'";
		}
		
		if( !($maze[ $g_pos ]&WALL_SOUTH) ){
			$str_lnk = addnav("Süden", "gardenmaze.php?pos=".($g_pos+12));
			$quickkeys['arrowdown'] = "window.location='$str_lnk'";
		}
		
		if( !($maze[ $g_pos ]&WALL_WEST) ){
			$str_lnk = addnav("Westen", "gardenmaze.php?pos=".($g_pos-1));
			$quickkeys['arrowleft'] = "window.location='$str_lnk'";
		}
	}
	
	
	return $out;
}



function maze_special(){
	global $session, $g_pos;
	$ret = 0; //wenn gestorben wird->1
	$out = "";
	
	switch( e_rand( 1, 2500) ){
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
			$out = "Glück gehabt!  Du siehst ein Nest einer Elster im Gestrüpp! Du findest einen Edelstein!";
			$session['user']['gems']++;
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
			$out = "Glück gehabt! Du findest einen Beutel mit 100 Goldstücken! Den wird hier wohl einer verloren haben";
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
			if($session['user']['turns'] > 0){ 
				redirect("gardenmazeevents.php?specialid=appletree&pos=".$g_pos);
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
		//$locale=$HTTP_GET_VARS[loc];
		//if($session['user']['turns'] > 0) redirect ("castleevents.php?op=web&loc=$locale&op2=spec");
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
			if($session['user']['turns'] > 0){ 
				redirect("gardenmazeevents.php?specialid=well&pos=".$g_pos);
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
		//if($session['user']['turns'] > 0) redirect ("castleevents.php?op=gang&loc=$locale&op2=spec");
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
			$out = "Du streifst mit Deinem Arm einen Dornenbusch!`nAutsch!";
			$session['user']['hitpoints'] *= 0.95;
			if ($session['user']['hitpoints']<1) $session['user']['hitpoints']=1;
		break;
		case 99:
		case 100:
		// $locale=$HTTP_GET_VARS[loc];
		// redirect ("castleevents.php?op=earthshrine&loc=$locale&op2=spec");
		break;
		case 101:
		case 102:
		//$locale=$HTTP_GET_VARS[loc];
		//if($session['user']['turns'] > 0) redirect ("castleevents.php?op=airshrine&loc=$locale&op2=spec");
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
		//$locale=$HTTP_GET_VARS[loc];
		//if($session['user']['turns'] > 0) redirect ("castleevents.php?op=truhe&loc=$locale&op2=spec");
		break;
		case 125:
		case 126:
		case 127:
		case 128:
		case 129:
		case 130:
			$out = "Glück gehabt! Du findest 23 Gold!";
			$session['user']['gold']+=23;
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
		break;
	
		case 2323:
		case 2324:
		case 2325:
		case 2326:
		case 2327:
			$out = "Du findest einen Zettel.`n";
			if( ($session['user']['mazeturns'] > 5 && e_rand(1,23)%2) || $session['user']['marks'] == 31 ){
				$out .= "Als Du ihn genauer betrachtest, merkst du dass es ein Plan dieses Labyrinthes ist.";
				$session['user']['maze_visited'] = implode("", array_fill(0, 144, 1));
			}
			else{
				$out .= "Du weisst jedoch nicht, was du mit dieser Kritzelei anfangen sollst und schmeisst den Zettel achtlos weg."; 
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
			$out = "Du läufst durch einen grausig stinkenden Abschnitt des Irrgartens. Die düngen hier wohl mit Gülle?";
			$session['user']['clean']+=1;
		break;
		case 2345:
		case 2346:
		case 2347:
		case 2348:
		case 2349:
		case 2350:
		case 2351:
		case 2352:
			if($session['user']['turns'] > 0){
				redirect("mazemonster.php?op=gardner");
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
			//special bitte
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
			$out = "Ganz in Deiner Nähe hörst Du ein Rascheln im Gebüsch.";
		break;
		case 2373:
		case 2374:
		case 2375:
		case 2376:
		case 2377:
		case 2378:
		case 2379:
			$out = "Du läufst durch einen wohlriechenden, mit wundervoll blühenden Blumen bepflanzten Abschnitt des Irrgartens. Wie erholsam!";
			$session['user']['hitpoints']+=20;
			if( $session['user']['marriedto'] ){
				$out .= "Als Du die Blumen betrachtest, denkst du an ";
				if( $session['user']['sex'] ){
					$out .= "deinen Schatz und pflückst ihm"; 
				}
				else{
					$out .= "deine Süße und pflückst ihr";
				}
				$out .= " einen kleinen Strauß.`n";
				$out .= "`^Du erhälst einen Charmpunkt!";
				$session['user']['charm']++;
			}
		break;
		case 2380:
		case 2381:
		case 2382:
		case 2383:
			if($session['user']['turns'] > 0){
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
			if($session['user']['turns'] > 0){
				redirect("mazemonster.php?op=bigspider");
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
			$session['user']['hitpoints']*=0.15;
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
			if ($session['user']['hitpoints']<1) $session['user']['hitpoints']=1;
		break;
		case 2423:
		case 2424:
		case 2425:
		case 2426:
		case 2427:
		case 2428:
		case 2429:
			if($session['user']['turns'] > 0){
				redirect("mazemonster.php?op=zyklop");
			}
		break;
		case 2430:
		case 2431:
		case 2432:
		case 2433:
		case 2434:
			$out = "Eine Mücke sticht Dich und saugt Dir etwas Blut ab.";
			$session['user']['hitpoints']-=2;
			if ($session['user']['hitpoints']<1) $session['user']['hitpoints']=1;
		break;
		case 2435:
		case 2436:
		case 2437:
		case 2438:
		case 2439:
			$out  = "Eine Schlange beisst Dir in die Wade und schlängelt sich zurück in die Büsche.`n";
			$out .= "`^Gift durchströmt deinen Körper.";
			$session['user']['hitpoints']-=30;
			if ($session['user']['hitpoints']<1) $session['user']['hitpoints']=1;		
		break;
		case 2440:
		case 2441:
		case 2442:
		case 2443:
		case 2444:
		case 2445:
			$out = "Du stolperst über einen Maulwurfshügel";
			if(e_rand(1,4)==3){
				$out .= " und fällst mit dem Kopf auf einen Stein und wirst bewusstlos.`n";
				$out .= "Als Du wieder zu Dir kommst, fasst Du Dir an den Kopf und bemrkst eine Wunde.`n";
				$out .= "Du schaust Dich um und merkst, wie sich ein Schleier des Vergessens über Dich gelegt hat.";
				$session['user']['hitpoints'] 		*= 0.65;
				$session['user']['maze_visited']     = implode("", array_fill(0, 144, 0));
				$session['user']['maze_visited'][0]  = 1;
				$session['user']['maze_visited'][ $g_pos ] = 1;
			}
			else{
				$out .= ", drehst Dich um und siehst, wie ein kleiner Maulwurf herausguckt und lachend quieqt.";
			}
		break;
		case 2446:
		case 2447:
		case 2448:
		case 2449:
		case 2450:
		case 2451:
			if($session['user']['turns'] > 0){ 
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
		//if($session['user']['turns'] > 0) redirect("mazemonster.php?op=ghost1");
		break;
		case 2459:
		case 2460:
		case 2461:
		//if($session['user']['turns'] > 0) redirect("mazemonster.php?op=bat");
		break;
		case 2462:
		case 2463:
		case 2464:
			if($session['user']['turns'] > 0){
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
			if($session['user']['turns'] > 0){
				redirect("gardenmazeevents.php?specialid=jigsaw&pos=".$g_pos);
			}
		break;
		case 2477:
		case 2478:
		case 2479:
		case 2480:
		case 2481:
		case 2482:
		//$locale=$HTTP_GET_VARS[loc];
		//if($session['user']['turns'] > 0) redirect ("castleevents.php?op=vamp&loc=$locale&op2=spec");
		break;
		case 2483:
		case 2484:
		case 2485:
		case 2486:
		case 2487:
		case 2488:
		case 2489:
			if($session['user']['turns'] > 0){
				redirect("gardenmazeevents.php?specialid=blingbling&pos=".$g_pos);
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
		    $out  = "Du hörst ein Rascheln im Gebüsch, bleibst kurz stehen um nachzusehen, als plötzlich...`n";
			$out .= "<big><big><big>`4Krrrrrrrr!<small><small><small>`n`n";
			$out .= "<img src=\"images/maze/maze_trap2.gif\">`n";
			$out .= "`@1000 Speere schiessen aus den Büschen und löchern Dich, so dass der Lebenssaft aus Dir sprudelt.";
			$ret = 1;
		break;
		case 2498:
		case 2499:
		case 2500:
			$out  = "<big><big><big>`4Ahhhhhhhhhh!<small><small><small>`n`n";
			$out .= "<img src=\"images/maze/maze_trap1.gif\">`n";
		    $out .= "`@Plötzlich verlierst du den Boden unter den Füßen und landest in einer Fallgrube. Zu deinem Pech ist sie mit angespitzen Holzpfählen bestückt`n";
			$ret = 1;
		break;
	}	
	
	if( !empty($out)){
		output("`@".$out."`n");
	}
	
	return $ret; //0,
}




if($_GET['init']==1){
	$session['user']['mazeturn'] = 0;
	maze_initalize();
	redirect("gardenmaze.php?pos=0");
}
else{
	if( !empty($_GET['superuser']) ){
		if($_GET['superuser']=="showmap"){
			$session['user']['maze_visited'] = implode("", array_fill(0, 144, 1));
		}
		elseif($_GET['superuser']=="leave"){
			$session['user']['maze_visited'] = "";
			redirect("gardenmaze.php?pos=143");
		}
	}
	if( !empty($session['maze_output']) ){
		output("`n`@".$session['maze_output']);
		$session['maze_output'] = "";
	}
	if( $g_pos == 143 ){//ende
		$session['user']['mazeturn']++;
		
		$gold = round(max( getsetting("castle_gold",5000) - ($session['user']['mazeturn']/1.25*50), 0),0);
		$gemreward = getsetting("castle_gems",4);
		if( $session['user']['mazeturn'] > 200 || !$gold ) {$gemreward=0;}
		elseif($session['user']['mazeturn'] <= 200 && $session['user']['mazeturn'] > 110) {$gemreward-=3;}
		elseif($session['user']['mazeturn'] <= 110 && $session['user']['mazeturn'] > 55) {$gemreward-=2;}
		elseif($session['user']['mazeturn'] <= 55 && $session['user']['mazeturn'] > 30) {$gemreward--;}
		$gems = max($gemreward,0);
		
		$out = "`n`n`2Du findest einen Goldtopf in dem sich `^".$gold."`2 Goldstücke und `^".$gems."`2 Edelsteine befinden`n";
		
		// GILDENMOD
		require_once(LIB_PATH.'dg_funcs.lib.php');
		if($session['user']['guildid'] && $session['user']['guildfunc'] != DG_FUNC_APPLICANT) {
			
			$tribute = dg_member_tribute($session['user']['guildid'],$gold,$gems);
			dg_save_guild();
			if($tribute[0] > 0 || $tribute[1] > 0) {
				$out  .="`2Davon zahlst du `^".$tribute[0]."`2 Goldstücke und `^".$tribute[1]."`2 Edelsteine Tribut an deine Gilde.`n";
				$gold -= $tribute[0];
				$gems -= $tribute[1];
			}	
		}
		// END GILDENMOD
		
		$session['user']['gold'] += $gold;
		$session['user']['gems'] += $gems;
		$session['maze_map']	  = "";	
		user_set_aei( array("maze_map" => "") );
		
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
		
		addnav("Raus hier!", "village.php");
		output($out); 
	}
	else{
		if( maze_special() ){
			killplayer(0,1,0,"");
			addnews("`%".$session['user']['name']."`5 versuchte sich an einem Labyrinth, kam aber nie wieder lebendig heraus!.");
			addnav("Mist!","shades.php");
		}
		elseif( $session['user']['hitpoints'] < 1 ){
			$session['user']['maze_visited'] = "";
			addnav("Schatten", "shades.php");
		}
		else{
			$session['user']['mazeturn']++;
			$session['user']['maze_visited'][ $g_pos ] = 1;
			output( maze_draw() );
			if( su_check(SU_RIGHT_CASTLECHOOSE) ){
				addnav("Besonderes");
				addnav("göttlicher Überblick", "gardenmaze.php?superuser=showmap&pos=".$g_pos);
				addnav("H?göttliche Heckenschere", "gardenmaze.php?superuser=leave");
				addnav("Specialstest");
				addnav("jigsaw", "gardenmazeevents.php?specialid=jigsaw&pos=".$g_pos);
				addnav("blingbling", "gardenmazeevents.php?specialid=blingbling&pos=".$g_pos);
				addnav("brunnen", "gardenmazeevents.php?specialid=well&pos=".$g_pos);
				addnav("apfelbaum", "gardenmazeevents.php?specialid=appletree&pos=".$g_pos);
			}
		}
	}
}
page_footer();
?>
