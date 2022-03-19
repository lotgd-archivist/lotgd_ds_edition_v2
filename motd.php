<?php
/**
* motd.php: Stellt bewährte MoTD-Funktionalität zur Verfügung, erweitert
*			um Editfunktion, Autorangabe, Datumsanzeige, Archiv.
* @author LOGD-Core, modified and rewritten by talion <t@ssilo.de>
* @version DS-E V/2
*/

require_once('common.php');

addcommentary(false);
session_write_close();
popup_header(getsetting('townname','Atrahor').': Message of the Day (MoTD)');
output((su_check(SU_RIGHT_MOTD)?" [<a href='motd.php?op=edit'>MoTD / Umfrage erstellen</a>]`n":""),true);

function motditem($subject,$body,$date='',$author='')
{
    output('`b'.($author != '' ? $author : '').' '.$subject.'`b`n',true);
    if ($date!='')
    {
		output('`i[ '.strftime('%A, %e. %B %Y, %H:%M',strtotime($date)).' ]`i`n');
        //output('`i[ '.date('d.m.Y, H:i',strtotime($date)).' ]`i`n');
    }
    output($body);
    output('<hr>',true);
}
function pollitem($id,$subject,$body,$date='')
{
    global $session;
    $sql = "SELECT count(resultid) AS c, MAX(choice) AS choice FROM pollresults WHERE motditem='$id' AND account='{$session['user']['acctid']}'";
    $result = db_query($sql);
    $row = db_fetch_assoc($result);
    $choice = $row['choice'];
    $body = unserialize($body);
    if ($row['c']==0 && 0)
    {
        output('<form action="motd.php?op=vote" method="POST">',true);
        output('<input type="hidden" name="motditem" value="'.$id.'">',true);
        output('`#`bUmfrage: '.$subject.'`b`n',true);
        if ($date!='')
        {
            output('`i[ '.strftime('%A, %e. %B %Y, %H:%M',strtotime($date)).' ]`i`n');
        }
        output('`3'.stripslashes($body['body']));
        while (list($key,$val)=each($body['opt']))
        {
            if (trim($val)!="")
            {
                output("`n<input type='radio' name='choice' value='$key'>",true);
                output(stripslashes($val));
            }
        }
        output("`n<input type='submit' class='button' value='Abstimmen'>",true);
        output("</form>",true);
    }
    else
    {
        output("<form action='motd.php?op=vote' method='POST'>",true);
        output("<input type='hidden' name='motditem' value='$id'>",true);
        output('`#`bUmfrage: '.$subject.'`b`n',true);
        if ($date!='')
        {
            output('`i[ '.date('d.m.Y, H:i',strtotime($date)).' ]`i`n');
        }
        output('`3'.stripslashes($body['body']));
        $sql = "SELECT count(resultid) AS c, choice FROM pollresults WHERE motditem='$id' GROUP BY choice ORDER BY choice";
        $result = db_query($sql);
        $choices=array();
        $totalanswers=0;
        $maxitem = 0;
        for ($i=0; $i<db_num_rows($result); $i++)
        {
            $row = db_fetch_assoc($result);
            $choices[$row['choice']]=$row['c'];
            $totalanswers+=$row['c'];
            if ($row['c']>$maxitem)
            {
                $maxitem = $row['c'];
            }
        }
        while (list($key,$val)=each($body['opt']))
        {
            if (trim($val)!="")
            {
                if ($totalanswers<=0)
                {
                    $totalanswers=1;
                }
                $percent = round($choices[$key] / $totalanswers * 100,1);
                output("`n<input type='radio' name='choice' value='$key'".($choice==$key?" checked":"").">",true);
                output(stripslashes($val)." (".(int)$choices[$key]." - $percent%)");
                if ($maxitem==0)
                {
                    $width=1;
                }
                else
                {
                    $width = round(($choices[$key]/$maxitem) * 400,0);
                }
                $width = max($width,1);
                output("`n<img src='images/rule.gif' width='$width' height='2' alt='$percent'>",true);
                //output(stripslashes($val)."`n");
            }
        }
        output("`n<input type='submit' class='button' value='Abstimmen'></form>",true);
    }
    output("<hr>",true);
}

switch($_GET['op']) {

	case 'vote':

		if (!isset($session['user']['acctid']))
		{
			header("Location: motd.php");
			exit();
		}
    
		output("true");
		$sql = "DELETE FROM pollresults WHERE motditem='{$_POST['motditem']}
		' AND account='{$session['user']['acctid']}
		'";
		db_query($sql);
		$sql = "INSERT INTO pollresults(choice,account,motditem) VALUES('{$_POST['choice']}
		','{$session['user']['acctid']}
		','{$_POST['motditem']}
		')";
		db_query($sql);
		header("Location: motd.php");
		exit();
		
	break;
		
	case 'edit':
		
		if(!su_check(SU_RIGHT_MOTD)) {
			if ($session['user']['loggedin'])
			{
				
				$session['user']['experience']=round($session[user][experience]*0.9,0);
				addnews($session[user][name]." wurde für den Versuch, die Götter zu betrügen, bestraft.");
				output("Du hast versucht die Götter zu betrügen. Du wurdest mit Vergessen bestraft. Einiges von dem, was du einmal gewusst hast, weisst du nicht mehr.");
				saveuser();
			}
			exit;
		}
		
		output(' [<a href="motd.php">MoTD Index</a>] ',true);
		
		$int_item = (int)$_REQUEST['motditem'];
		$str_body = $_POST['motdbody'];
		$str_savebody = '';
		$str_title = $_POST['motdtitle'];
		$int_type = (int)$_POST['motdtype'];
		$int_author = (int)$_POST['motdauthor'];
		$str_opt = $_POST['opt'];
		$arr_body = array();
		$arr_opt = array();
						
		if($_GET['act'] == 'save') {
												
			if($int_type == 1) {
				$arr_opt = explode('||',stripslashes($str_opt));
				$arr_body = array('body'=>stripslashes(nl2br($str_body)),'opt'=>$arr_opt);
				$str_savebody = addslashes(serialize($arr_body));								
			}
			else {
				$str_savebody = nl2br($str_body);							
			}
			
			$sql = ($int_item ? 'UPDATE ' : 'INSERT INTO ');
			$sql .= ' motd SET ';
				
			$sql .= '	motdtitle="'.$str_title.'",
						motdbody="'.$str_savebody.'",
						motddate='.($int_item==0 || $_POST['newmotd'] ? 'NOW()' : 'motddate').',
						motdtype="'.$int_type.'",
						motdauthor='.($int_author > -1 ? $int_author : 'motdauthor').'';
			
			$sql .= ($int_item ? ' WHERE motditem='.$int_item : '');
						
			db_query($sql);
									
			if(!db_error(LINK)) {
			
				if($int_item==0 || $_POST['newmotd']) {
					
					$sql = 'UPDATE accounts SET lastmotd = "0000-00-00 00:00:00" WHERE acctid!='.$session['user']['acctid'];
		            db_query($sql);
					
				}
			
				$session['message'] = '`@MoTD erfolgreich eingetragen!`0';
				header("Location: motd.php");
				exit;
			}
			
		}
		
		$str_author_list = ',enum,0,Drachenserver-Team,
							'.$session['user']['acctid'].','.$session['user']['login'];
							
	
		$str_type_list = ',radio,0,Ohne Umfrage,1,Mit Umfrage';
		
		$arr_form = array('motditem'=>',hidden',
							'motdauthor'=>'Autor:'.$str_author_list,
							'motdtitle'=>'Titel:',
							'motdbody'=>'Inhalt:`n,textarea,35,8',
							'motdtype'=>'Typ:'.$str_type_list,
							'opt'=>'Antwortmöglichkeiten für die Umfrage`n(mit || abtrennen)`n,textarea,35,8',
							);
		$arr_data = array('motditem'=>$int_id,
							'motdauthor'=>($int_author?$int_author:$session['user']['acctid']),
							'motdtitle'=>$str_title,
							'motdbody'=>$str_body,
							'motdtype'=>$int_type,
							'opt'=>$str_opt
							);
						
		if($int_item > 0) {
			$sql = 'SELECT * FROM motd WHERE motditem='.$int_item;
			$arr_motd = db_fetch_assoc(db_query($sql));
			
			if($arr_motd['motdtype'] == 1) {	// Umfrage vorhanden
				
				$arr_body = unserialize($arr_motd['motdbody']);
				$arr_motd['motdbody'] = $arr_body['body'];
				$arr_motd['opt'] = implode('||',$arr_body['opt']);
								
			}
			
			$arr_motd['motdbody'] = str_replace('<br />','',$arr_motd['motdbody']);
			
			$arr_form['newmotd'] = 'MoTD als neu markieren:,bool';
			$arr_data['newmotd'] = 0;
			
			$arr_form['motdauthor'] .= ',-1,~ Keine Änderung ~';
			$arr_motd['motdauthor'] = -1;
			
			$arr_data = array_merge($arr_data,$arr_motd);	
			
		}
				
		output('<form action="motd.php?op=edit&act=save" method="POST">',true);
							
		showform($arr_form,$arr_data,false,'Veröffentlichen!');
		
		output('</form>',true);
		
	break;
		
	case 'del':
		
		if (su_check(SU_RIGHT_MOTD))
		{
			$sql = 'DELETE FROM motd WHERE motditem='.(int)$_GET['id'];
			db_query($sql);
			
			$sql = 'DELETE FROM pollresults WHERE motditem='.(int)$_GET['id'];
			db_query($sql);
			
			header("Location: motd.php");
			exit();
		}
		else
		{
			if ($session['user']['loggedin'])
			{
				
				$session['user']['experience']=round($session[user][experience]*0.9,0);
				addnews($session[user][name]." wurde für den Versuch, die Götter zu betrügen, bestraft.");
				output("Du hast versucht die Götter zu betrügen. Du wurdest mit Vergessen bestraft. Einiges von dem, was du einmal gewusst hast, weisst du nicht mehr.");
				saveuser();
			}
			exit;
		}
		
	break;
		
	default:

		$last_motddate = '0000-00-00 00:00:00';
		$per_page = 10;
		
		output("`&");
		motditem("Beta!","Bitte beachte die Hinweise ganz unten.");
		
		$sql = 'SELECT COUNT(*) AS anzahl FROM motd';
		$res = db_query($sql);
		$nr = db_fetch_assoc($res);
		
		$pagecount = ceil($nr['anzahl']/$per_page);
		$page = ($_POST['page'])?$_POST['page']:1;
		$from = ($page-1) * $per_page;
		$select = '<form action="motd.php" method="POST">
		-&#8212; MotD-Archiv: <select name="page" size="1" onChange="this.form.submit();">';
		
		for ($i=1; $i<=$pagecount; $i++)
		{
			
			$select .= '<option value="'.$i.'" '.(($page==$i)?'selected="selected"':'').'>Seite '.$i.'</option>';
			
		}
		$select .= '</select>  -&#8212;</form>';
		
		$sql = 'SELECT m.*,a.login FROM motd m LEFT JOIN accounts a ON a.acctid=m.motdauthor ORDER BY m.motddate DESC LIMIT '.$from.','.$per_page;
		$result = db_query($sql);
		for ($i=0; $i<db_num_rows($result); $i++)
		{
			
			$row = db_fetch_assoc($result);
			
			if ($i == 0)
			{
				$last_motddate = $row['motddate'];
			}
			
			$author = '`&'.($row['login'] != '' ? $row['login'] : 'Drachenserver-Team').' :';
			
			if ($row['motdtype']==0)
			{
				$str_subj = '`#'.$row['motdtitle']
				.(su_check(SU_RIGHT_MOTD)?
				" [<a href='motd.php?op=del&id=$row[motditem]' onClick=\"return confirm('Bist du sicher, dass dieser Eintrag gelöscht werden soll?');\">Del</a>]
				 [<a href='motd.php?op=edit&motditem=$row[motditem]'>Edit</a>] "
				:"");
				$str_body = '`3'.$row['motdbody'];
				
				motditem($str_subj,$str_body,$row['motddate'],$author);
			}
			else
			{
				$str_subj = ''.$row['motdtitle']
				.(su_check(SU_RIGHT_MOTD)?
				"[<a href='motd.php?op=del&id=$row[motditem]' onClick=\"return confirm('Bist du sicher, dass dieser Eintrag gelöscht werden soll?');\">Del</a>]
				[<a href='motd.php?op=edit&motditem=$row[motditem]'>Edit</a>] "
				:"");
				$str_body = $row['motdbody'];
				
				pollitem($row['motditem'],$str_subj,$str_body,$row['motddate']);
			}
			//}
		}
		output("`&");
		motditem("Beta!","Dieses Spiel ist im Beta-Status! Wir basteln an der Draognslayer-Edition, wenn wir Zeit haben und versuchen, das Spiel so bugfrei wie möglich zu halten. Das ist KEIN Freibrief zum Ausnutzen von Bugs, sondern alle Spieler (Teilnehmer am Beta-Test) sind verpflichtet, gefundene Fehler zu melden! Wünsche und Anregungen werden ebenfalls jederzeit gerne angenommen. : )");
		//Diese Seite ist im BETA Status! Ich bastel daran herum, wenn ich Zeit habe. Auch Änderungen von offizieller Seite (MightyE) werden hier übernommen. Das ist KEIN Freibrief zum Ausnutzen von Bugs, sondern alle Spieler (Teilnehmer am Beta-Test) sind verpflichtet, gefundene Fehler zu melden! Wünsche und Anregungen werden ebenfalls jederzeit gern angenommen. :-)");
		output('`c'.$select.'`c',true);
		output("`@Kommentare und Fehler:`0`n");
		
		viewcommentary("motd","Kommentar hinzufügen?",10,"sagt",true,true,false,false,false,false);
		
		$session['needtoviewmotd']=false;
		$session['user']['lastmotd']=$last_motddate;
		
	break;
		
}

popup_footer();
?>