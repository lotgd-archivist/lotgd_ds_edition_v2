<?php

// Tool zum Editieren der neuen Specials
// by Maris
// IN ARBEIT

require_once "common.php";
require_once(LIB_PATH.'dg_funcs.lib.php');
su_check(SU_RIGHT_EDITORUSER,true);

page_header("User Editor Besondere Fertigkeiten");



addnav('W?Zurück zum Weltlichen',$session['su_return']);

// Angepasste Ausgabe für Specials
function showform2(&$layout,$row,$nosave=false)
{
	global $output,$session;
	
	$row[specialtyuses]=unserialize($row[specialtyuses]);
	
	$output.='<table>';
	while(list($key,$val)=each($layout))
	{

		$info = split(',',$val);
		if ($info[1]=='title')
		{
			$output.='<tr><td colspan="2" bgcolor="#666666">';
			output('`b`^'.$info[0].'`0`b');
			$output.='</td></tr>';
		}
		else
		{
			$output.='<tr><td nowrap valign="top">';
  	        output($info[0]);
			$output.='</td><td>';
		}
		switch ($info[1])
		{
			case 'title':
			break;
			
			case 'skill':
			$output.='<input name="'.$key.'" value="'.HTMLEntities($row['specialtyuses'][$info[0]]).'" size="5">';
			break;
			case 'uses':
			$output.='<input name="'.$key.'" value="'.HTMLEntities($row['specialtyuses'][$info[0]]).'" size="5">';
			break;
			default:
			$output.='<input size="50" name="'.$key.'" value="'.HTMLEntities($row['specialtyuses'][$info[0]]).'">';
		}
		$output.='</td></tr>';
	}
	
	$output.='</table>';
}

// Anzahl der installierten Module ermitteln
$sql = "SELECT * FROM specialty";
	$result = db_query($sql);
	if(db_num_rows($result)<=0) {
		output("`4Keine Module installiert");
	} else {
		$sql2 = "SELECT * FROM specialty WHERE active='1'";
		$result2 = db_query($sql2);
		$rows1 = db_num_rows($result);
		$rows2 = db_num_rows($result2);

		output($session['message']);
  }

for ($i=1;$i<=$rows1;$i++){
$row=db_fetch_assoc($result);

 // Komma nicht vor dem ersten Durchlauf setzen
 if ($i>1)
 {
   $userinfo=$userinfo.";";
 }
      // Umwandlung ID->Name
      $sql = "SELECT * FROM specialty WHERE specid=$i";
      $rowspec = db_fetch_assoc(db_query($sql));
      $skillnames = array($rowspec['specid']=>$rowspec['specname']);
      $skills = array($rowspec['specid']=>$rowspec['usename']);
      $skillpoints = array($rowspec['specid']=>$rowspec['usename']."uses");
			
// Array erstellt sich selbst
$userinfo=$userinfo.$skillnames[$i].",title;".$skills[$i].",skill;".$skillpoints[$i].",uses\"";
}
$userinfo=explode(";",$userinfo);

if ($_GET[op]=="edit"){
	$result = db_query("SELECT * FROM accounts WHERE acctid='$_GET[userid]'") or die(db_error(LINK));
	$row = db_fetch_assoc($result) or die(db_error(LINK));
	output("<form action='user_special.php?op=save&userid=$_GET[userid]".($_GET['returnpetition']!=""?"&returnpetition={$_GET['returnpetition']}":"")."' method='POST'>",true);
	addnav("","user_special.php?op=save&userid=$_GET[userid]".($_GET['returnpetition']!=""?"&returnpetition={$_GET['returnpetition']}":"")."");
	addnav("","user_special.php?op=edit&userid=$_GET[userid]".($_GET['returnpetition']!=""?"&returnpetition={$_GET['returnpetition']}":"")."");
	addnav("Usereditor");
	addnav("Usereditor","user.php?op=edit&userid=$row[acctid]");
	addnav("Zusatzinfos","user.php?op=edit2&userid=$row[acctid]");

  	showform2($userinfo,$row);
  	output("<input type='submit' class='button' value='Speichern'>",true);
    output("</form>",true);

}elseif ($_GET[op]=="save"){

$userid=$_GET['userid'];
reset($_POST);
$savearray=array();

$it=1;
while (list($key,$val)=each($_POST)){

// Zuordnung zu den Specials
$val2=((int)($key/3))+1;

// Umwandlung ID->Name
$sql = "SELECT * FROM specialty WHERE specid=$val2";
$rowspec = db_fetch_assoc(db_query($sql));
$skillnames = array($rowspec['specid']=>$rowspec['specname']);
$skills = array($rowspec['specid']=>$rowspec['usename']);
$skillpoints = array($rowspec['specid']=>$rowspec['usename']."uses");

// Abwechselnd Skill und Anwendungen aus der Form nehmen
if ($it==1)
{
  if ($val>0) {
  $savearray[$skills[$val2]]=(int)$val;
  }
$it=0;
}
else
{
  $savearray[$skillpoints[$val2]]=(int)$val;
  $it=1;
}

}

$savearray=serialize($savearray);
output("array up:".$savearray."`n`n");

$sql = "UPDATE accounts SET specialtyuses='$savearray' WHERE acctid='$userid'";
db_query($sql) or die(db_error(LINK));
output($sql."`n");

addnav("G?Zurück zur Grotte","superuser.php");

//	debuglog("Useredit_Special - Editierte User ".$_POST['login'],$_GET['userid']);

}

page_footer();
?>
