<?php
/* MOD:
Ersteller: www.plueschdrache.de
Datum: irgendwann 2004
Descr: - Ermöglicht jedem Waldspecial eine gewisse Wahrscheinlichkeit zuzuweisen, wie oft es auftritt
- Ermöglicht eine bessere Übersicht über die Waldspecials --> Descr-Feld
- Zählt die Häufigkeit wie oft welches Skript aufgerufen wurde
- Ermöglicht einzele Specials erst ab einem höheren DK zu spielen
- ist schön bunt

Sonst: Alles zwischen den "~~~~~" Linien darf gelöscht werden. "www.plueschdrache.de" darf nicht gelöscht / verändert / ausgeblendet werden!
--> Die user sehen's ja nicht .. also keine Panik
*/

/**
 * @desc Modified by Dragonslayer for lotgd.drachenserver.de
 * @longdesc +Inserted a possibility to turn specials on and off
             +Inserted a link to the sourcecode
             +Altered the DB and SQL Parts
             -Removed/Altered some performance decreasing parts             
 */


require_once 'common.php';
page_header('Einstellungen Waldspecial');
addnav('Waldspecialeditor');
addnav('Files aktualisieren','waldspecialeditor.php?op=neu');
addnav('Eigenschaften festlegen','waldspecialeditor.php?op=edit');
addnav('Zurück');
addnav('Zur Grotte','superuser.php');
addnav('Zum Wald','forest.php');


if ($_GET['op']=='')
{
	$ausgabe.='`7<h3>Waldspecialeditor</h3>`n
  Mit diesem Tool kann festgelegt werden, welches Special ab welcher Anzahl an Drachenkills wie oft und mit welcher Wahrscheinlichkeit eintreten wird.';
}
elseif ($HTTP_GET_VARS[op]=='neu')
{
	if ($handle = @opendir('special'))
	{
		$filename = array();
		while (false !== ($file = @readdir($handle)))
		{
			if (strpos($file,'.php')>0)
			{
				array_push($filename,$file);
			}
		}
		if (count($filename)==0)
		{
			$ausgabe.='`b`@<h3>Keine Waldspecials vorhanden</h1>`n';
		}
		else
		{
			output('`7<b>Waldspecial Einstellungen:</b><br>',true);

			// eingetragene specials auslesen
			$sql='SELECT filename FROM waldspecial';
			$result=mysql_query($sql);
			$anzahl=mysql_num_rows($result);
			// in array speichern
			while($row=mysql_fetch_assoc($result))
			{
				$files[$row['filename']]=true;
			}
			// checken
			$i=0;
			foreach($filename as $key => $val)
			{
				if ($files[$val]!=true)
				{
					$sql='INSERT INTO waldspecial (filename, descr, prio, dk, anzahl) VALUES ("'.$val.'", "keine Beschreibung vorhanden", 0, 0, 0)';
					mysql_query($sql);
					$i++;
				}
				else
				{
					$files[$val]='alt';
				}
			}
			if ($i>0)
			{
				$ausgabe.='<b>Es wurden <u>'.$i.'</u> neue Waldspecials eingetragen. Diese können jetzt angepasst werden</b><br />';
			}
		}
	}
	else
	{
		$ausgabe.='`c`b`\$FEHLER!!!`b`c`&Kann den Ordner mit den Waldspecials nicht finden. Bitte benachrichtige den Admin!! Du bist der Admin?!?... Ja... das könnte sich zum Problem entwickeln';
	}

	// gelöschte Waldspecials aus DB löschen
	$j=0;

	if (count($files)>0)
	{
		foreach($files as $key=>$val)
		{
			if ($val!='alt')
			{
				$sql="DELETE FROM waldspecial WHERE filename='$key'";
				mysql_query($sql);
				$ausgabe.="$sql <br>";
				$j++;
			}
		}
	}

	if ($j)
	{
		$ausgabe.="<b>Es wurden <b><u>$j</u></b> neue Waldspecials aus der Datenbank gelöscht</b><br>";
	}

	if ($ausgabe=='')
	{
		$ausgabe='<h2>Es gibt keine Veränderungen im special-Ordner... </h2>';
	}

}
elseif($_GET['op']=='edit')
{
	$sql='SELECT * FROM waldspecial ORDER BY filename';
	$result=mysql_query($sql);
	$anzahl=mysql_num_rows($result);
	if ($anzahl)
	{
		$ausgabe.='
    `n`n
     Waldspecial Editor`n`n
     Priorität absteigend! Je niedrieger die Prio ist, desto öfters kommt das Special dran!`n
     Achte darauf, dass mind. ein Waldspecial Prio 0 und DK 0 hat!`n`n
     <form action="waldspecialeditor.php?op=save" method="POST">';
		addnav('','waldspecialeditor.php?op=save');
		$ausgabe.='<table width="600">';
		$ausgabe.='<tr>
               <td>SpecialNr.</td>
               <td>file-Name</td>
               <td>Priorität</td>
               <td>MinDk</td>
               <td>Anzahl</td>
               <td>Freigeschaltet</td>
               <td>Beschreibung</td>
             </tr>';
		$i=0;
		while($row=mysql_fetch_assoc($result))
		{
			$color[0]='#008000';
			$color[1]='#14EAD3';
			$color[2]='#E6E629';
			$color[3]='#F26A10';
			$color[4]='#FF0000';

			$ausgabe.='<tr style="background-color:'.$color[$row[prio]].'">';
			$ausgabe.='<td>'.($i+1).'</td>';
			$ausgabe.="<td><font color=black><a href='#' onclick=javascript:".popup('source.php?url=/special/'.$row[filename]).">$row[filename]</a></font></td>";
			$ausgabe.="<td><select name='data[".$i."][prio]'>
                        <option value='0' ".($row[prio]=='0'?"selected":"")." style='background-color:".$color[0]."; color:black;'>sehr häufig</option>
                        <option value='1' ".($row[prio]=='1'?"selected":"")." style='background-color:".$color[1]."; color:black;'>häufig</option>
                        <option value='2' ".($row[prio]=='2'?"selected":"")." style='background-color:".$color[2]."; color:black;'>recht selten</option>
                        <option value='3' ".($row[prio]=='3'?"selected":"")." style='background-color:".$color[3]."; color:black;'>sehr selten</option>
                        <option value='4' ".($row[prio]=='4'?"selected":"")." style='background-color:".$color[4]."; color:black;'>deaktiviert</option>
                       </select>
                 </td>";
			$ausgabe.="<td><input type='text' name='data[".$i."][dk]' value='$row[dk]' size='3'>
                 </td>";
			$ausgabe.="<td><input type='text' name='data[".$i."][anzahl]' value='$row[anzahl]' size='5'>
                 </td>";
			$ausgabe.="<td>
			<select name='data[".$i."][released]'>
			<option value='0' ".($row[released]=='0'?"selected":"")." style='background-color:".$color[0]."; color:black;'>nein</option>
			<option value='1' ".($row[released]=='1'?"selected":"")." style='background-color:".$color[1]."; color:black;'>ja</option>
			</select>
			</td>";
			$ausgabe.="<td><textarea class='input' name='data[$i][descr]' rows='3' cols='40'>".stripslashes($row[descr])."</textarea></td>";
			$ausgabe.="<input type='hidden' name='data[".$i."][filename]' value='$row[filename]'>";
			$ausgabe.="<input type='hidden' name='data[".$i."][row_id]' value='$row[row_id]'>";
			$ausgabe.='</tr>';
			$i++;
		}

		$ausgabe.='</table><br>';
		$ausgabe.='<input type="submit" name="s1" value="Einstellungen speichern"></form>';
	} // ende check ob was in DB steht
	else
	{  // steht nix in DB
		$ausgabe.='<h1>Du solltest erstmal ein paar Specials importieren!</h1>';
	}
}
elseif($_GET['op']=='save')
{
	$count = count($_POST['data']);
	for ($i=0;$i<$count;$i++)
	{
		$sql='UPDATE waldspecial SET prio='.abs((int)$_POST['data'][$i]['prio']).', dk='.abs((int)$_POST['data'][$i]['dk']).', descr="'.mysql_escape_string($_POST['data'][$i]['descr']).'", anzahl='.abs((int)$_POST['data'][$i]['anzahl']).', released='.(int)$_POST['data'][$i]['released'].' WHERE row_id='.(int)$_POST['data'][$i]['row_id'] ;
		mysql_query($sql);
			$check= mysql_error();
		if ($check!='')
		{
			$ausgabe.='<br><b>'.$check.'</b><br>';
		}
		$ausgabe.='<h2>Jupp, das wars.</h2>';
	}
}

output($ausgabe,true);
page_footer();
?>