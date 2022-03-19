<?
/*
@file gardenmazeevents.php
@desc Specials f�r gardenmaze.php (specialguest: jigsaw)
@author Sven-Michael "Alucard" St�be
*/
require_once 'common.php';
page_header("Der Irrgarten des verlassenen Schlo�es");
$pos = $_GET['pos'];
$specialid = $_GET['specialid'];
$out = "";
switch($specialid){
	
	case "jigsaw"://ja das muss sein :>
		switch($_GET['op']){
			case "help":
				if( $_GET['try'] == 1 )
				{
					$out = "Du schreist nocheinmal um Hilfe. Ob das was bringt?`n";
				}
				else{
					$out = "Du schreist eine Stunde um Hilfe.`n";
				}
				$res = db_query("SELECT acctid,name,weapon,sex,gold FROM accounts WHERE acctid<>".$session['user']['acctid']." AND loggedin<>0 AND maze_visited<>'' ORDER BY RAND() LIMIT 1");
				$gethelp = (db_num_rows($res) == 1)? (e_rand(1,5)==3 ? 1 : 0) : 0;
				if( $gethelp ){
					$hero = db_fetch_assoc($res);
					$out .= "Pl�tzlich merkst Du, wie sich ".$hero['name']."`@ mit ".($hero['sex'] ? "ihrem" : "seinem")." ".$hero['weapon']."`@ zu Dir hindurch k�mpft und Dich befreit.`n";
					if( $session['user']['gold'] > 0 ){
						$out .= "Als Dank f�r seine Tapferkeit gibst du ".$hero['name']."`@ dein ganzes Gold.";
						$out .= ($hero['sex'] ? "Sie" : "Er")." l�chelt und macht sich davon.`n"; 
						$msg = "`&Du hast ".$session['user']['name']."`& im Labyrinth aus einer Falle gerettet. Du bekommst `^".$session['user']['gold']."`& Goldst�ke als Dankesch�n von ihm.";  
						systemmail($hero['acctid'],"Dankesch�n",$msg);
						db_query("UPDATE accounts SET gold=".($hero['gold']+$session['user']['gold'])." WHERE acctid=".$hero['acctid']);
						$session['user']['gold'] = 0;
					}
					else{
						$what = $hero['sex'] ? "einen wundersch�nen Prinzen" : "eine wundersch�ne Prinzessin";
						$out .= $hero['name']." `@hat gedacht, dass ".($hero['sex'] ? "sie" : "er")." ".$what." vorfinden w�rde. Stattdessen findet ".($hero['sex'] ? "sie" : "er")." nur dich vor, schaut ennt�uscht und macht sich vom Acker.";
					}
					$out .= "Du freust dich �ber Deine Rettung und findest Deine Waffe und Deine R�stung wieder. Hoffentlich hat das bald ein Ende!";
					addnews($hero['name']."`& hat ".$session['user']['name']."`& aus einer h�llischen Falle im Labyrinth gerettet");
					addnav("Was f�r ein Gl�ck");
					addnav("Weiter", "gardenmaze.php?pos=".$pos);
				}
				elseif( $_GET['try'] == 1 ){
					$out .= "So sehr Du auch schreien magst; keiner h�rt dich! Du siehst den einzigsten Ausweg darin dich in die B�sche zu st�rzen. Jedoch bist du schon etwas geschw�cht! Wie willst du es versuchen?";	
					$session['user']['hitpoints'] *= 0.75;
					addnav("Wie?");
					addnav("vorsichtig", "gardenmazeevents.php?specialid=jigsaw&pos=".$pos."&op=try&strength=1&help=2");
					addnav("energisch", "gardenmazeevents.php?specialid=jigsaw&pos=".$pos."&op=try&strength=3&help=2");
				}
				else{
					$out .= "So laut Du auch schreist; Es kommt niemand!`n";
					if( db_num_rows($res) < 1 ){ //Wenn keiner da ist, gibt es sogar nen Tip (nur wer aufmerksam ist weiss, was er zu tun hat) :D
						$out .= "Die mysteri�se Stimme spricht erneut zu Dir: \"`i`&Es wird Dich keiner h�ren!`i`@\"`n";
					}
					$out .= "Was tust du nun?`n";
					$out .= "`4- Trotz Ersch�pfung weiter um Hilfe rufen`n";
					$out .= "- versuchen, Dich vorsichtig durch die B�sche zu schl�ngeln`n";
					$out .= "- energisch in die B�sche st�rzen und sie wie wild auseinander rei�en";
					addnav("Was nun?");
					addnav("Hilfe!!!! Hilfe!!!!", "gardenmazeevents.php?specialid=jigsaw&pos=".$pos."&op=help&try=1");
					addnav("vorsichtig", "gardenmazeevents.php?specialid=jigsaw&pos=".$pos."&op=try&strength=1&help=1");
					addnav("energisch", "gardenmazeevents.php?specialid=jigsaw&pos=".$pos."&op=try&strength=3&help=1");
				}
			break;
			
			case "try":
				$free 	= 0;
				$chance = $_GET['try']+$_GET['strength'];
				$help   = $_GET['help'];
				$hplost = e_rand(10, $_GET['strength']*15)*0.01*$session['user']['maxhitpoints'];
				
				$out = "Du versuchst Dich ".($_GET['strength']==1? "vorsichtig" : "energisch")." durch die Dornenb�sche zu k�mpfen.`n";
				for($i=0; $i<$chance && !$free; $i++){
					$free = (e_rand(0,9+$help)== 2 ? 1 : 0); 
				}
				
				if( $free ){
					$gems = e_rand(3,6);
					$gold = e_rand(3,8)*500;
					$out = "Als Du Dein Schicksal akzeptieren willst, bemerkst du Dein ".$session['user']['weapon']."`@ und Dein ".$session['user']['armor']."`@ vor Deinen F��en!`n";
					$out .= "Du ziehst Deine R�stung an und nimmst Deine Waffe an Dich, als Du `^".$gold."`@ Golds�cke und `#".$gems."`@ Edelsteine vor Dir erblickst! Nat�rlich steckst Du Deinen Lohn f�r dieses makarbere Spielchen schnellstm�glich ein und gehst so schnell, wie m�glich weiter.";
					$session['user']['gems'] += $gems;
					$session['user']['gold'] += $gold;
					if( $hplost > $session['user']['hitpoints'] ){
						$session['user']['hitpoints'] = 1;
					}
					else{
						$session['user']['hitpoints'] -= $hplost;
					}
					addnav("Schnell weg!", "gardenmaze.php?pos=".$pos);
				}
				elseif( $hplost < $session['user']['hitpoints'] ){
					$out .= "Du kommst zwar ein St�ckchen weiter, bist aber noch nicht drau�en!";
					$session['user']['hitpoints'] -= $hplost;
					addnav("Was nun?");
					addnav("vorsichtig", "gardenmazeevents.php?specialid=jigsaw&pos=".$pos."&op=try&strength=1&help=".$help."&try=".($_GET['try']+1));
					addnav("energisch", "gardenmazeevents.php?specialid=jigsaw&pos=".$pos."&op=try&strength=3&help=".$help."&try=".($_GET['try']+1));
				}
				else{//sterben :>
					$out .= "Du hast soviel Blut verloren, dass Dein K�rper letztendlich leblos im Gestr�pp in sich zusammenf�llt.`n";
					if(!$help){//wer nach hilfe geschrieen hat ist ein Feigling!
						$out .= "Jedoch wird Dich Ramius f�r Deinen Mut belohnen.";
						$session['user']['gravefights'] += 5;
					}
					killplayer(0,0,0,"",0);
					addnav("Mist!", "shades.php");
				}		
			break;			
			
			default:
				$out  = "Du l�ufst ahnungslos durch das Labyrinth, als Du einen Stich in Deinem Nacken merkst.`n";
				$out .= "Du fasst Dir an den Nacken, sp�rst einen Dornen und ziehst ihn raus. Als Du ihn betrachten willst, wird Dir schwarz vor Augen und du kippst um.`n";
				$out .= "Nach einiger zeit wachst Du an einem Ort auf, der von meterhohen Dornenb�schen umgeben ist. Du merkst, dass Du Dein ".$session['user']['weapon']."`@ und Dein ".$session['user']['armor']."`@ nicht mehr bei dir hast.`n`n";
				$out .= "Pl�tzlich spricht eine Stimme zu dir:`n";
				$out .= "`&\"`iHallo ".$session['user']['name']."`&! Ich m�chte ein Spiel spielen!`n Zeig: Wieviel Blut willst du vergie�en um zu �berleben?`i\"`n";
				$out .= "`@Die Stimme vehallt nach einem lauten Lachen. Nun bist du auf dich allein gestellt. Was wirst du tun?`n";
				$out .= "`4- Hilfe rufen`n";
				$out .= "- versuchen Dich vorsichtig durch die B�sche zu schl�ngeln`n";
				$out .= "- energisch in die B�sche st�rzen und sie wie wild auseinander rei�en";
				addnav("Was nun?");
				addnav("Hilfe!!!! Hilfe!!!!", "gardenmazeevents.php?specialid=jigsaw&pos=".$pos."&op=help");
				addnav("vorsichtig", "gardenmazeevents.php?specialid=jigsaw&pos=".$pos."&op=try&strength=1");
				addnav("energisch", "gardenmazeevents.php?specialid=jigsaw&pos=".$pos."&op=try&strength=3");
			break;			
		}
	
	break;
	
	case "blingbling":
		switch($_GET['op']){
			
			case "leave":
				$session['maze_output'] = "Du gehst einfach weiter.";
				redirect("gardenmaze.php?pos=".$pos);
			break;
			
			case "watch":
				$out .= "Du schaust etwas genauer hin und findest ";
				switch(e_rand(1,6)){
					case 1:
					case 2:
						$out .= "einen Edelstein und steckst ihn ein.";
						$session['user']['gems']++;
						addnav("Juchee!", "gardenmaze.php?pos=".$pos);
					break;
					case 3:
					case 4:
						$out .= "eine Heckenschere!`n";
						$out .= "Was willst du mit ihr anstellen?`n";
						$out .= "`4- testen, ob sie scharf ist`n";
						$out .= "- den Weg freischneiden.`n";
						$out .= "- sie wegwerfen und weiter gehen.";
						addnav("Testen", "gardenmazeevents.php?specialid=blingbling&pos=".$pos."&op=test");
						addnav("Freischneiden", "gardenmazeevents.php?specialid=blingbling&pos=".$pos."&op=cut&tested=0");
						addnav("Weg damit", "gardenmazeevents.php?specialid=blingbling&pos=".$pos."&op=trash");
					break;
					
					default:
						$out .= "ein billiges St�ck Blech.`n";
						$out .= "Entt�uscht schmei�t Du es wieder ins Geb�sch und gehst Deines Weges.";
						addnav("Weiter", "gardenmaze.php?pos=".$pos);					
					break;				
				}
			break;
			
			case "test":
				$out = "Du f�hrst mit mit Deinem Daumen quer �ber die Klinge und merkst, dass ";
				switch(e_rand(1,9)){
					case 1:
					case 4:
					case 7:
						$out .= "die Schere stumpf ist, l�sst sie fallen und gehst weiter.";
						addnav("Weiter", "gardenmaze.php?pos=".$pos);
					break;
					
					case 2:
					case 5:
					case 8:
						$out .= "Du einen Finger weniger hast.`nEtwas Blut sprudelt aus dem Stumpf.`n";
						$out .= "`^Du verlierst einige Lebenspunkte!`n";
						$out .= "`@Aber nun wei�t Du, dass die Heckenschere scharf ist.`nWas machst du jetzt?`n";
						$out .= "`4- Ich hab mich genug verletzt. Ich lass es bleiben!`n";
						$out .= "- Ich will hier raus und werde mir den Weg freischneiden! Das sind doch nur B�sche!`n";
						$session['user']['hitpoints']-=20;
						if ($session['user']['hitpoints']<1){
							$session['user']['hitpoints']=1;
						}
						addnav("Weg damit", "gardenmazeevents.php?specialid=blingbling&pos=".$pos."&op=trash");
						addnav("Freischneiden", "gardenmazeevents.php?specialid=blingbling&pos=".$pos."&op=cut&tested=1");
					break;
					
					case 3:
					case 6:
					case 9:
						$out .= "Dir die Hornhaut vom Daumen geschabt wird. Das Ding ist scharf!`nWas machst du jetzt?`n";
						$out .= "`4- Ich hab angst mich zu verletzen. Ich lass es bleiben!`n";
						$out .= "- Das Ding schneidet meine Hornhaut, also werden die B�sche ein Klaxx.`n";
						addnav("Weg damit", "gardenmazeevents.php?specialid=blingbling&pos=".$pos."&op=trash");
						addnav("Freischneiden", "gardenmazeevents.php?specialid=blingbling&pos=".$pos."&op=cut&tested=1");
					break;
				}
			break;
			
			
			case "cut":
				$cancut = $_GET['tested'] == 1 ? 1 : e_rand(1,24)%2;
				$out .= "Du gehst wie wild auf die Str�ucher los.`n";
				if($cancut){
					switch(e_rand(1,7)){
						case 1:
						case 2:
						case 3:
							$out .= "Du verf�llst einen wahren Schnibbelwahn!`n";
							$out .= "Als Du dich umschaust bemerkst Du, dass Du am Ende dieses Labyrinthes bist.";
							$session['maze_output'] = $out;
							redirect("gardenmaze.php?pos=143");
						break;
						
						case 4:
							$out .= "Du verf�llst einen wahren Schnibbelwahn und rutschst ab!`n";
							$out .= "Dein Kopf rollt einen Meter und Dein restlicher K�rper sackt in sich zusammen.`n";
							$out .= "`^Du bist tot!`n";
							killplayer(100, 0.5, 0, "");
							addnav("Verdammt!", "shades.php");
						break;
						
						case 5:
						case 6:
						case 7:
							$gems = e_rand(1,3);
							$out .= "Du verf�llst einen wahren Schnibbelwahn!`n";
							$out .= "Als Du dich umschaust bemerkst Du, dass Du wieder am Anfang dieses Labyrinthes bist.`n";
							$out .= "Jedoch hast Du auf deinem Weg durch die B�sche `#".$gems." `@Edelsteine gefunden.";
							$session['maze_output'] = $out;
							redirect("gardenmaze.php?pos=0");
						break;
					}
				}
				else{
					$out .= "Nach einer Weile merkst Du, dass Du lediglich die Rinde verbeulst, weil die Heckenschere stumpf ist und Du schmerzende Blasen an den H�nden hast.`n";
					$out .= "`^Du verlierst ein paar Lebenspunkte!`n";
					addnav("Weiter", "gardenmaze.php?pos=".$pos);
				}
			breaK;
			
			
			case "trash":
				$out .= "Da du kein G�rtner bist, schmeisst du die Heckenschere �ber die B�sche und h�rst ein \"`i`&AUUUUUA! Du Ar...loch!`i\"`@ und gehst schnell weiter.`n";
				addnav("Weiter", "gardenmaze.php?pos=".$pos);
			breaK;
			
			default:
				$out  = "Als Du zuf�llig in die B�sche schaust, siehst du etwas funkeln.`n";
				$out .= "Was wirst du tun?";
				addnav("Was nun?");
				addnav("nachschauen", "gardenmazeevents.php?specialid=blingbling&pos=".$pos."&op=watch");
				addnav("weitergehen", "gardenmazeevents.php?specialid=blingbling&pos=".$pos."&op=leave");
			
			
			break;
		}
		
	break;
	
	
	
	case "well":
		
		switch($_GET['op']){
			
			case "trink":
				$out  = "Du nimmst einen Schluck Wasser zu Dir und ";
				switch(e_rand(1,10)){
					case 1:
					case 2:
					case 3:
					case 4:
						$out .= "f�hlst Dich gest�rkt.";
						$session['user']['hitpoints'] = round($session['user']['maxhitpoints']*(1+$session['user']['level']*0.01),0);
						addnav("Weiter", "gardenmaze.php?pos=".$pos);
					break;
					case 5:
					case 6:
					case 7:
						$out .= "f�hlst Dich erfrischt.";
						$session['user']['hitpoints'] =$session['user']['maxhitpoints'];
						addnav("Weiter", "gardenmaze.php?pos=".$pos);
					break;
					case 8:
					case 9:
						$out .= "bekommst einen ungeheuren Powerschub!`n";
						$out .= "`^Deine max. Lebenspunkte erh�hen sich `bpermanent um 3`b!";
						$session['user']['hitpoints'] = round($session['user']['maxhitpoints']*(1+$session['user']['level']*0.02),0);
						$session['user']['maxhitpoints']+=3;
						addnav("Juhu", "gardenmaze.php?pos=".$pos);
					break;
					case 10:
						$out .= "f�llst auf der Stelle tot um!`n";
						$out .= "`4Deine max. Lebenspunkte verringern sich `bpermanent um 1`b!`n";
						$out .= "`@Ausserdem hast Du das Gef�hl etwas vergessen zu haben.";
						$session['user']['maxhitpoints']--;
						killplayer(0,1,0,"");
						addnav("Mist!", "shades.php");
					break;						
				}			
			break;
		
			
			default:
				$out  = "Du h�rst Wasser pl�tschern und entdeckst einen Brunnen, als zu dem Ger�usch nachgehst.`n";
				$out .= "Was wirst du tun?";
				addnav("Was nun?");
				addnav("erfrischen", "gardenmazeevents.php?specialid=well&pos=".$pos."&op=trink");
				addnav("weitergehen", "gardenmaze.php?pos=".$pos);
			
			break;
			
		}		
		
	break;
	
	
	
	case "appletree":
		
		switch($_GET['op']){
			
			case "apple":
				$out  = "Du nimmst einen Apfel vom Baum und beisst hinein.`n";
				switch(e_rand(1,12)){
					case 1:
					case 2:
					case 3:
					case 4:
						$out .= "Dieser Apfel schmeckt sauer.";
						addnav("Igitt!", "gardenmaze.php?pos=".$pos);
					break;
					case 5:
					case 6:
						$out .= "Dieser Apfel schmeckt k�stlich!`n`^Du f�hlst Dich gest�rkt.";
						$session['user']['hitpoints'] =$session['user']['maxhitpoints']+50;
						addnav("Weiter", "gardenmaze.php?pos=".$pos);
					break;
					case 7:
					case 8:
					
						$out .= "Dieser Apfel birgt magische Kr�fte!`n`^Du f�hlst dich ";
						
						switch(e_rand(1,5)){
							case 1:
								$out .= "st�rker!`n";
								$out .= "`^Dein Angriff steigt um 2 Punkte!";
								$session['user']['attack']+=2;
							break;
							
							case 2:
								$out .= "lebendiger!`n";
								$out .= "`^Deine max. Lebenspunkte erh�hen sich permanent um 1!";
								$session['user']['maxhitpoints']++;
							break;
							
							case 3:
								$out .= "gesch�tzter!`n";
								$out .= "`^Deine verteidigung steigt um 2 Punkte!";
								$session['user']['defence']++;
							break;
							
							case 4:
								$out .= "erfahrener!`n";
								$out .= "`^Deine Erfahrung steigt!";
								$session['user']['experience'] *= 1+$session['user']['level']*0.01;
							break;
							
							case 5:
								$out .= "reicher!`n";
								$out .= "`^Du bemerkst, dass du 500 Golst�cke mehr mit Dir herumschleppst!";
								$session['user']['gold'] += 500;
							break;
						}
						addnav("Juhu", "gardenmaze.php?pos=".$pos);
					break;
					case 9:
					case 10:
						$out .= "Dieser Apfel war vergiftet.`n";
						$out .= "Du f�llst auf der Stelle tot um!`n";
						$out .= "`4Deine max. Lebenspunkte verringern sich `bpermanent um 1`b!`n";
						$out .= "`@Ausserdem hast Du das Gef�hl etwas vergessen zu haben.";
						$session['user']['maxhitpoints']--;
						killplayer(0,1,0,"");
						addnav("Mist!", "shades.php");
					break;					
					case 11:
					case 12:
						$out .= "Aua! Dieser Apfel birgt einen Edelstein in sich.`n";
						$out .= "Du verlierst ein paar Lebenspunkte, vergist den Schmerz aber wieder, als du den Edelstein in deine Tasche stopfst!`n";
						$session['user']['hitpoints']-=10;
						if ($session['user']['hitpoints']<1) $session['user']['hitpoints']=1;
						$session['user']['gems']++;
						addnav("Gut!", "gardenmaze.php?pos=".$pos);
					break;
				}			
			break;
			
			case "slay":
				$out = "Du ziehst dein ".$session['user']['weapon']."`@ und schl�gst der Schlange den Kopf ab.`n";
				switch(e_rand(1,4)){
					case 1:
					case 2:
					case 3:
						$out .= "Als du genauer hinschaust, bemerkst du, dass die Augen der Schlange Edelsteine sind. Schnell schneidest Du Sie raus und steckst sie zu deinen anderen Gemmen.";
						$session['user']['gems']+=2;
						addnav("Jippie!", "gardenmaze.php?pos=".$pos);
					break;
					
					case 4:
						$out .= "Pl�tzlich wird es dunkel und Wadjet, die �gyptische Schlangeng�ttin, spricht zu dir:`n";
						$out .= "`&\"`iDu hast es gewagt, eine unschuldige Kreatur zu t�ten. Nun wirst du meinen Zorn sp�ren!`i\"";
						$session['user']['gems']+=2;
						addnav("K�mpfe!", "mazemonster.php?op=snakegod");
					break;
					
				}
			
			break;
		
			
			default:
				$out  = "Du siehst einen Baum, der herrlich rot leuchtende �pfel tr�gt.`n";
				$out .= "Als du an den Baum herantrittst siehst du eine Schlange. Diese spricht zu Dir:`n";
				$out .= "`&\"`iHallo ".$session['user']['name']."`&! Siehst Du diese wundervollen Fr�chte? Nimm ruhig einen! Sie schmecken wirklich wunderbar.`i\"`n";
				$out .= "`@Was wirst du tun?";
				addnav("Was nun?");
				addnav("Apfel nehmen.", "gardenmazeevents.php?specialid=appletree&pos=".$pos."&op=apple");
				addnav("Schlange t�ten", "gardenmazeevents.php?specialid=appletree&pos=".$pos."&op=slay");
				addnav("weitergehen", "gardenmaze.php?pos=".$pos);
			break;
			
		}		
		
	break;
}

if( !empty($out) ){
	output("`n`@".$out."`0");
}
if(su_check(SU_RIGHT_CASTLECHOOSE)){
	addnav("Besonderes");
	addnav("Zum garten", "gardenmaze.php?&pos=".$pos);
	addnav("Zum dorf", "gardenmaze.php?superuser=leave");
}
page_footer();



?>
