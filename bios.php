<?
require_once "common.php";

if ($_GET['op']=="block"){
    $sql = "UPDATE account_extra_info SET bio='`iGesperrt aufgrund unangebrachten Inhalts`i',biotime='9999-12-31 23:59:59' WHERE acctid='{$_GET['userid']}'";
	systemmail($_GET['userid'],"Deine Kurzbeschreibung wurde gesperrt","Der Administrator hat beschlossen, dass deine Kurzbeschreibung unangebracht ist und hat sie gesperrt.`n`nWenn du dar?ber diskutieren willst, benutze bitte den Link zur Hilfeanfrage.");
	db_query($sql);
}
if ($_GET['op']=="unblock"){
	$sql = "UPDATE account_extra_info SET bio='',biotime='0000-00-00 00:00:00' WHERE acctid='{$_GET['userid']}'";
	systemmail($_GET['userid'],"Deine Kurzbeschreibung wurde wieder freigegeben","Der Administrator hat beschlossen, deine Kurzbeschreibung wieder freizugeben. Du kannst wieder eine Beschreibung eingeben.");
	db_query($sql);
}
$sql = "SELECT acctid,bio,biotime FROM account_extra_info WHERE biotime<'9999-12-31' AND bio>'' ORDER BY biotime DESC LIMIT 100";
$result = db_query($sql);
page_header("Spielerkurzbeschreibungen");
output("`b`&Spieler Kurzbeschreibungen:`0`b`n");
for ($i=0;$i<db_num_rows($result);$i++){
    $row = db_fetch_assoc($result);
    if ($row['biotime']>$session['user']['recentcomments'])
        output("<img src='images/new.gif' alt='&gt;' width='3' height='5' align='absmiddle'> ",true);
    output("`![<a href='bios.php?op=block&userid={$row['acctid']}'>Block</a>]",true);
    addnav("","bios.php?op=block&userid={$row['acctid']}");
$sql2 = "SELECT name FROM accounts WHERE acctid=".$row[acctid]."";
$result2 = db_query($sql2);
$row2 = db_fetch_assoc($result2);
output("`&{$row2['name']}: `^".soap($row['bio'])."`n");
}
db_free_result($result);
addnav("G?Zur?ck zur Grotte","superuser.php");


addnav('W?Zur?ck zum Weltlichen',$session['su_return']);
addnav("Aktualisieren","bios.php");
//output("`n`n`bBlocked Bios:`b`n"); //This seems unneeded since we print it below
$sql = "SELECT acctid,bio,biotime FROM account_extra_info WHERE biotime>'9000-01-01' AND bio>'' ORDER BY biotime DESC LIMIT 100";
$result = db_query($sql);
output("`n`n`b`&Gesperrte Beschreibungen:`0`b`n");
for ($i=0;$i<db_num_rows($result);$i++){
    $row = db_fetch_assoc($result);
    output("`![<a href='bios.php?op=unblock&userid={$row['acctid']}'>Unblock</a>]",true);
    addnav("","bios.php?op=unblock&userid={$row['acctid']}");
$sql2 = "SELECT name FROM accounts WHERE acctid=".$row[acctid]."";
$result2 = db_query($sql2);
$row2 = db_fetch_assoc($result2);
    output("`&{$row2['name']}: `^".soap($row['bio'])."`n");
}
db_free_result($result);
page_footer();
?>
