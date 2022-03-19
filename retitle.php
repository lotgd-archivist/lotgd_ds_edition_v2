<?
// modded by talion: cname / ctitle backup

require_once "common.php";
su_check(SU_RIGHT_RETITLE,true);

page_header("Retitler");
addnav("G?Zurück zur Grotte","superuser.php");


addnav('W?Zurück zum Weltlichen',$session['su_return']);
addnav("Titel neu zuweisen","retitle.php?op=rebuild");
if ($_GET['op']=="rebuild"){

    $sql = "SELECT name,login,title,dragonkills,a.acctid,sex,ae.ctitle,ae.cname,a.ctitle AS ctitle_o FROM  accounts a LEFT JOIN account_extra_info ae USING(acctid) ";
    $result = db_query($sql);
    for ($i=0;$i<db_num_rows($result);$i++){
        $row = db_fetch_assoc($result);
        
		$oname = $row['name'];
		
        $newtitle = $titles[(int)$row['dragonkills']][(int)$row['sex']];
		
		if($newtitle == '') {$newtitle = end($titles)[(int)$row['sex']];}
		
		$row['title'] = ($row['title'] == 'Feigling' ? 'Feigling' : $newtitle);
		
//		$row['ctitle'] = $row['ctitle_o'];	// TEMP
		
		$title = trim($row['ctitle'] != '' ? $row['ctitle'] : $row['title']);
		
		// TEMP
		
	/*	if($row['cname'] == '') {
			$pos = strpos($row['name'],$title) + strlen($title);
			$name = trim( substr($row['name'],$pos) );
			
			if(strstr($name,'`')) {
				$row['cname'] = $name;
				
			}	
		}*/
				
		// TEMP
			
		
		$name = trim($row['cname'] != '' ? $row['cname'] : $row['login']);
		
//		if($row['name_back'] == '') {$row['name_back'] = $row['name'];} // TEMP
		
		//$name = str_replace('`0','',$name);
		
		$row['name'] = $title.' '.$name.'`0';
				
        output("`@Ändere `^$oname`@ auf `^{$row['name']} `@($newtitle-{$row['dragonkills']}[{$row['sex']}]({$row['ctitle']}`@))`n");
        if ($session['user']['acctid']==$row['acctid']){
            $session['user']['title']=$row['title'];
            $session['user']['name']=$row['name'];
        }else{
            $sql = "UPDATE accounts SET name_back='".addslashes($row['name_back'])."',name='".addslashes($row['name'])."', title='".addslashes($row['title'])."' WHERE acctid='{$row['acctid']}'";
			db_query($sql);
	    }
		$sql = "UPDATE account_extra_info SET ctitle='".addslashes($row['ctitle'])."', cname='".addslashes($row['cname'])."' WHERE acctid='{$row['acctid']}'";
		db_query($sql);
    }
}else{
    output("Diese Seite lässt dich alle Usertitel anpassen, wenn die im Drachenscript verändert wurden.");
}
page_footer();
?>
