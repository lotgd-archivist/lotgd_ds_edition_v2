<?
/* ******************* 
Baumstammfalle
Written by Sven-Michael Stübe
Bemerkung:
alle variablen mit tt_ präfix
********************* */
$tt_out = "`n`n"; //Das wird ausgegeben :>

switch( $_GET['op'] ){
	
	
	case "move":
		$tt_dir = $_GET['dir'];
		$tt_willdie = 0;   //wirst du sterben?
		$tt_trunkop = "";  //was macht der baumstamm?
		$tt_trunkdir = e_rand(1,3); //wo kommt der baumstamm runter?
		/*
		1 = left
		2 = right
		3 = turn		
		*/
		switch($tt_dir){			
			
			case "left":
				if( $tt_trunkdir == 1 ){
					$tt_willdie = 1;
				}
				else{
					$tt_trunkop = "rechts";
				}				
			break;
			
			case "right":
				if( $tt_trunkdir == 2 ){
					$tt_willdie = 1;
				}
				else{
					$tt_trunkop = "links";
				}			
			break;
			
			case "turn":
				if( $tt_trunkdir == 3 ){
					$tt_willdie = 1;
				}
				else if( $tt_trunkdir == 2 ){
					$tt_trunkop = "links";
				}
				else{
					$tt_trunkop = "rechts";
				}
			break; 
		}
		
		$tt_out .= "`2Ein gewalitger Baumstamm an einem Seil rast ";

		if( $tt_willdie == 1 ){
			$tt_bodypart = "";
			switch(e_rand(1,2)){
				case 1: $tt_bodypart = "Brustkorb"; break;
				case 2: $tt_bodypart = "Kopf";	    break;
			}
			$tt_out .= "auf dich zu, erfasst dich und zerschmettert deinen ".$tt_bodypart.".";
			
			addnews($session['user']['name']."'s `&".$tt_bodypart." wurde zu Matsch verarbeitet!");
			killplayer(100, 0, 0, "", 0);
			addnav("Ach Mist!");
			addnav("Ramius besuchen", "shades.php");
		}
		else{
			addnav("Glück gehabt");
			$tt_out .= $tt_trunkop." an dir vorbei und ";
			if(e_rand(1,5)==2){//etwas finden ;>
				$tt_gold = $session['user']['level']*e_rand(50,200);
				$session['user']['gold'] += $tt_gold;
				$tt_out .= "rammt einen Baum. Dieser kippt um und unter seinem Wurzelwerk kommen `@".$tt_gold." `2Goldstücke zum Vorschein.";
				addnav("Juhu!", "forest.php");
			}
			else{
				$tt_out .= "pendelt hin und her.";				
				addnav("Weiter", "forest.php");
			}
		}
		$session['user']['specialinc'] = "";
	
	break;	
	
	
	default:
		$session['user']['specialinc'] = "trunktrap.php";
		
		$tt_out .= "`c`&Du läufst im Wald und denkst an nichts Schlechtes.`c`n";
		$tt_out .= "`2Plötzlich hörst du ein Knacken und just im selben Moment ein Zischen hinter dir.`n`n";
		$tt_out .= "Wie reagierst du?";
		addnav("Was nun?");
		addnav("nach links springen","forest.php?op=move&dir=left");
		addnav("nach rechts springen","forest.php?op=move&dir=right");
		addnav("rumdrehen","forest.php?op=move&dir=turn");
}

output($tt_out);
?>
