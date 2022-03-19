<?php

// 10092004
	
// created by Lonny Luberts for http://www.pqcomp.com/logd, built on idea from quest's giftshop with all new code.
// this file needs customization before use and is designed to be added in many places if need be 
// as different gift shops.
// search and replace (newgiftshop.php) with what you name the giftshop php file
// search and replace (gift 1)-(your gift) with your gifts - make sure you use the space inbetween gift & 1 etc...
// if you do an auto replace with your editor.
// be sure to edit the return nav
// please feel free to use and edit this file, any major upgrades or improvements should be
// mailed to logd@pqcomp.com for consideration as a permenant inclusion
// please do not remove the comments from this file.
// Version: 03212004
//
// changes to fit ext (GER) and translation by anpera
// added items with buffs
//
// Bugfix u. Kerker-Addon by Maris (Maraxxus@gmx.de)
// Nachrichten zusammen mit Geschenken versenden by talion (t@ssilo.de),
// Umgestellt auf neues Itemsystem

require_once "common.php";
checkday();
page_header("Geschenkeladen");

output("`c`b`&Geschenkeladen`0`b`c`n`n");

switch ( $_GET['op'] ) {
	
	case 'send':
		
		$gift = $_GET['op2'];
		
		if (isset($_POST['search']) || $_GET['search']>""){
			if ($_GET['search']>"") $_POST['search']=$_GET['search'];
			$search = '%';
			for ($x=0;$x<strlen($_POST['search']);$x++){
				$search .= substr($_POST['search'],$x,1).'%';
			}
			$search = 'name LIKE "'.$search.'" AND ';
			if ($_POST['search']=="weiblich") $search="sex=1 AND ";
			if ($_POST['search']=="männlich") $search="sex=0 AND ";
		}else{
			$search="";
		}
		$ppp=25; // Player Per Page to display
		if (!$_GET['limit']){
			$page=0;
		}
		else{
			$page=(int)$_GET['limit'];
			addnav('Vorherige Seite','newgiftshop.php?op=send&op2='.$gift.'&limit='.($page-1).'&search='.$_POST['search']);
		}
		
		$limit="".($page*$ppp).",".($ppp+1);
		
		$sql = "SELECT login,name,level,sex,acctid FROM accounts WHERE $search locked=0 AND acctid<>".$session[user][acctid]." AND charm>-1 ORDER BY login,level LIMIT $limit"; 		
		$result = db_query($sql);
		
		$count = db_num_rows($result);
		
		if ($count>$ppp) addnav('Nächste Seite','newgiftshop.php?op=send&op2='.$gift.'&limit='.($page+1).'&search='.$_POST['search']);
		
		$link = 'newgiftshop.php?op=send&op2='.$gift;
				
		output('`rWem willst du das Geschenk schicken? Du hast außerdem die Möglichkeit, eine nette Botschaft beizulegen.`n`n');
		output('<form action="'.$link.'" method="POST">
			Nach Name suchen: <input name="search" value="'.$_POST['search'].'">`n`n',true);
		output('<input type="submit" class="button" value="Suchen"></form>',true);
		
		addnav('',$link);
		
		output("<table cellpadding='3' cellspacing='0' border='0'><tr class='trhead'><td>Name</td><td>Level</td><td>Geschlecht</td><td>Versenden</td></tr>",true);
		for ($i=0;$i<$count;$i++){
		
			$row = db_fetch_assoc($result);
			
			output("<tr class='".($i%2?"trlight":"trdark")."'><td>",true);
			output($row['name']);
			output("</td><td>",true);
			output($row['level']);
			
			$link = 'newgiftshop.php?op=send2&op2='.$gift.'&name='.$row['acctid'];
			
			output("</td><td align='center'><img src='images/".($row['sex']?"female":"male").".gif'></td>
					<td>
						[ <a href='".$link."'> Ohne </a> ] 
						[ <a href='".$link."&send_msg=1'> Mit </a> ] 
						Nachricht
					</td>
					</tr>",true);
			addnav('',$link);
			addnav('',$link.'&send_msg=1');
		}
		output("</table>",true);
		addnav("Zurück zum Laden","newgiftshop.php");
	
		break;
	
	case 'send2':
						
		$name = (int)$_GET['name'];
	
		$giftmsg = $_POST['message'];
		
		$sq3 = "SELECT name,acctid FROM accounts WHERE acctid=".$name."";
		$result3=db_query($sq3);
		$row3 = db_fetch_assoc($result3);
						
		if($_GET['send_msg']) {
			
			$link = 'newgiftshop.php?op=send2&op2='.$_GET['op2'].'&name='.$name;
			
			addnav('',$link);
			
			output("`rDu kannst hier ".$row3['name']."`r eine nette Botschaft beilegen:`n`n");
			output("<form action='".$link."' method='POST'>",true);
			output("Deine Botschaft: <input type='text' name='message' maxlength='500'>`n`n",true);		
			output("<input type='submit' class='button' value='Geschenk abschicken!'></form>",true);
			
			$check = 1;
			
		}
			
		if ($check!=1) {
			
			$gift = item_get_tpl ( ' tpl_id="'.$_GET['op2'].'"' );
						
			// Platzhalter in den Beschreibungen, die in Geschenkitems verwendet werden können
			// Wenn Verwendung in Geschenkehook: global nicht vergessen!
			$arr_placeholder = array('{name}'=>$session['user']['name'],
									'{shortname}'=>$session['user']['login'],
									'{date}'=>getgamedate(),
									'{recipient_name}'=>$row3['name'],
									'{gift_name}'=>$gift['tpl_name']
									);
			$arr_search = array_keys($arr_placeholder);
			$arr_rpl = array_values($arr_placeholder);
			$gift['tpl_description'] = str_replace($arr_search,$arr_rpl,$gift['tpl_description']);
			
			$item_hook_info ['mailmsg'] = '';
			$item_hook_info ['failmsg'] = '';
			$item_hook_info ['effect'] = '';
			$item_hook_info ['acctid'] = $name;
			$item_hook_info ['rec_name'] = $row3['name'];
			$item_hook_info ['check'] = 0;
							
			if ( $gift ['gift_hook'] != '' ) {
				
				item_load_hook ( $gift ['gift_hook'] , 'gift' , $gift );
				
			}
			
			if(!$item_hook_info['hookstop']) {
													
				$item_hook_info['effect'] = $gift['tpl_description'];			
										
				$session['user']['gold'] -= $gift['tpl_gold'];
				$session['user']['gems'] -= $gift['tpl_gems'];
				
				$gift['tpl_gold'] = round($gift['tpl_gold']*0.4);
				$gift['tpl_gems'] = round($gift['tpl_gems']*0.4);
				
				item_add ( $item_hook_info['acctid'] , 0 , false , $gift );
			}
			
			if($item_hook_info['check'] != 1) {
		
				$item_hook_info ['mailmsg'] .= $session['user']['name'];
				
				$item_hook_info ['mailmsg'] .= '`7 hat dir ein Geschenk geschickt.  Du öffnest es. Es ist ein/e `6' 
												. $gift['tpl_name'] . '`7 aus dem Geschenkeladen.`n'
												. $item_hook_info ['effect'];
				
				if($giftmsg != '') {$item_hook_info ['mailmsg'] .= '`n`nAls du die Verpackung näher betrachtest, fällt dir eine handgeschriebene Botschaft auf:`n'.$giftmsg.'`7';}
				
				systemmail($name,"`2Geschenk erhalten!`2",$item_hook_info ['mailmsg']);
				
				debuglog('Hat Geschenk '.$gift['tpl_name'].' versendet an: ',$name);
				
				output('`rDein '.$gift['tpl_name'].'`r wurde als Geschenk verschickt!');
				
				if (e_rand(1,3)==2){
					output(' Bei der Wahl des Geschenks und dem liebevollen Verpacken vergisst du die Zeit und vertrödelst einen Waldkampf.');
					$session['user']['turns']--;
				}
			}
					
		}
		
		addnav("Zum Laden","newgiftshop.php");
		
		break;
	
	default:
		if($session['user']['turns']>0) {
			output('`rDu betrittst den Geschenkeladen und siehst eine Menge einzigartiger Gegenstände.`n');
			output('Ein'.($session['user']['sex']?' junger Mann':'e junge Frau').' steht hinter der Ladentheke und lächelt dich sanft an.`n');
			output('Ein Schild an der Wand verspricht "`iGeschenkverpackung und Lieferung frei.`i"`n`n<ul>',true);
			
			addnav('Geschenke');
			
			// Itemliste aller Geschenke
			$res = item_tpl_list_get ( ' giftshop>0 ' , ' ORDER BY tpl_gold ASC, tpl_gems ASC ' );
			
			while ( $g = db_fetch_assoc ( $res ) ) {
											
				//if( $session['user']['gold'] >= $g['tpl_gold'] && $session['user']['gems'] >= $g['tpl_gems'] ) { 
				
					$link = 'newgiftshop.php?op=send&op2=' . $g['tpl_id'];
														
					output( '<li>' . 
							($session['user']['gold'] >= $g['tpl_gold'] && $session['user']['gems'] >= $g['tpl_gems'] 
								? create_lnk($g['tpl_name'], $link, true, true)
								: '`i'.$g['tpl_name'].'`i') . 
								($g['tpl_gold'] > 0 ? ' ( '. $g['tpl_gold'] . ' Gold ) ' : ''). 
								($g['tpl_gems'] > 0 ? ' ( '. $g['tpl_gems'] . ' Edelsteine ) ' : '').
							''
							, true );
				//}
				
			}
			// END Geschenkliste
			
			output('</ul>',true);
			
			addnav('Sonstiges');
			if (getsetting("activategamedate","0")>0){
				$cakecost=$session['user']['level']*15;
				//addnav("Torte werfen ($cakecost Gold)","newgiftshop.php?op=cake");
			}
			
			addnav('Zurück');
	
			
		}
		else {	// Keine Runden mehr
			
			output('`rDer Geschenkeladen hat jetzt leider schon geschlossen.');
			
			
		}
		
		$show_invent = true;
		
		addnav('Zum Garten','gardens.php');
		
		break;	// END default
		
	
}

page_footer();
?>
