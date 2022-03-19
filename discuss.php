<?php

// Mod-Diskussionen zu einem speziellen Spieler
// by Maris

require_once "common.php";

if ($session['user']['superuser']>0) //Man kann ja nie wissen...
  {
  if ($_GET[op]=="end")
  {
    $sql = "UPDATE account_extra_info SET discussion=0 WHERE acctid = ".$_GET[who]."";
    db_query($sql) or die(sql_error($sql));
    $return = preg_replace("'[&?]c=[[:digit:]-]+'","",$_GET[ret]);
	$return = substr($return,strrpos($return,"/")+1);
    redirect($return);
  }
  
  $sql = "SELECT name,acctid FROM accounts WHERE acctid=".$_GET[who];
  $result = db_query($sql) or die(db_error(LINK));
  $row = db_fetch_assoc($result);

  page_header("Diskussionen zu ".$row['name']."`&");
  output('`&Aktuelle Diskussion zu '.$row['name'].'`&`n`n');
  addcommentary(false); // kein Alk hier

  $return = preg_replace("'[&?]c=[[:digit:]-]+'","",$_GET[ret]);
  $return = substr($return,strrpos($return,"/")+1);

  $roomname = "Discuss-".$_GET[who];
  viewcommentary($roomname,"Hinzufügen:",50,"sagt");
  }
else
  {
  output("`&Und was hast DU hier zu suchen ?`nRAUS!`n");
  }

addnav("Zurück",$return);
addnav("Zur Bio","bio.php?char=".urlencode($_GET[char]));
if (su_check(SU_RIGHT_DEBUG)>=3)
{
addnav("Admin");
addnav("Diskussion beenden","discuss.php?op=end&who=".$_GET[who]."&char=".urlencode($_GET[char])."&ret=".$_GET['ret']);
}
page_footer();
?>
