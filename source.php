<?php

// 15082004

require_once "common.php";


$url=$_GET['url'];
$dir = str_replace("\\","/",dirname($url)."/");
$subdir = str_replace("\\","/",dirname($_SERVER['SCRIPT_NAME'])."/");

while(substr($subdir,0,2)=="//" ){
     $subdir = substr($subdir,1);
}

//echo "<pre>$url  $dir  $subdir</pre>";
$legal_dirs = array(
	$subdir."" => 1,
	$subdir."special/"  => 1
);

$illegal_files = array(
	($subdir=="//"?"/":$subdir)."dbconnect.php"=>"X",
	($subdir=="//"?"/":$subdir)."topwebvote.php"=>"X", // hide completely
	($session[user][donation]>500?"none":($subdir=="//"?"/":$subdir)."lodge.php")=>"Spiele und sammle 500 Donationpoints, um dieses Script zu sehen ;)",
	($session[user][superuser]>0?"none":($subdir=="//"?"/":$subdir)."housefeats.php")=>"Diese Datei ist nur für Moderatoren einsehbar ;)",

    ($session[user][superuser]>0?"none":($subdir=="//"?"/":$subdir)."alchemie.php")=>"Diese Datei ist nur für Moderatoren einsehbar ;)",
	($session[user][superuser]>0?"none":($subdir=="//"?"/":$subdir)."chosenfeats.php")=>"Diese Datei ist nur für Moderatoren einsehbar ;)",
	($session[user][superuser]>0?"none":($subdir=="//"?"/":$subdir)."dinner.php")=>"Diese Datei ist nur für Moderatoren einsehbar ;)",
	($session[user][superuser]>0?"none":($subdir=="//"?"/":$subdir)."furniture.php")=>"Diese Datei ist nur für Moderatoren einsehbar ;)",
	($session[user][superuser]>0?"none":($subdir=="//"?"/":$subdir)."thepath.php")=>"Diese Datei ist nur für Moderatoren einsehbar ;)",
	($session[user][superuser]>0?"none":($subdir=="//"?"/":$subdir)."villageevents.php")=>"Diese Datei ist nur für Moderatoren einsehbar ;)",
	($session[user][superuser]>0?"none":($subdir=="//"?"/":$subdir)."houses.php")=>"Hier gibt es überhaupt nichts zu sehen ;)",
	($session[user][superuser]>0?"none":($subdir=="//"?"/":$subdir)."expedition.php")=>"Hier gibt es überhaupt nichts zu sehen ;)",
	($subdir=="//"?"/":$subdir)."translator_de.php"=>"Hol dir lieber die vollständige Datei von <a href='http://www.anpera.net/forum/viewtopic.php?t=341' target='_blank'>hier</a>!",
	($subdir=="//"?"/":$subdir)."source.php"=>"X",
	($subdir=="//"?"/":$subdir)."anticheat.php"=>"X",
//	($subdir=="//"?"/":$subdir)."common.php"=>"X",
	(getsetting("vendor",0)==1?($subdir=="//"?"/":$subdir)."vendor.php":"none")=>"Der Wanderhändler ist heute in der Stadt! ;)",
	($subdir=="//"?"/":$subdir)."chat.php"=>"X",
	($subdir=="//"?"/":$subdir)."test.php"=>"X",
	($subdir=="//"?"/":$subdir)."dg_main.php"=>"Noch nicht veröffentlicht",
	($subdir=="//"?"/":$subdir)."dg_output.php"=>"Noch nicht veröffentlicht",
	($subdir=="//"?"/":$subdir)."dg_battle.php"=>"Noch nicht veröffentlicht",
	($subdir=="//"?"/":$subdir)."dg_su.php"=>"Noch nicht veröffentlicht",
	($subdir=="//"?"/":$subdir)."dg_builds.php"=>"Noch nicht veröffentlicht",
	($subdir=="//"?"/":$subdir)."tragoraz.php"=>"Noch nicht veröffentlicht",
	($subdir=="//"?"/":$subdir)."schatzsuche.php"=>"Noch nicht veröffentlicht",
	($subdir=="//"?"/":$subdir)."trophy.php"=>"Noch nicht veröffentlicht",
	($subdir=="//"?"/":$subdir)."runemaster.php"=>"Noch nicht veröffentlicht",
	($subdir=="//"?"/":$subdir)."watchsu.php"=>"X",	
	($session[user][dragonkills]?"none":($subdir=="//"?"/":$subdir)."dragon.php")=>"Wenn du das Drachenskript lesen willst, schlage ich vor, du besiegst erst den Drachen!",
	($session[user][specialinc]=="vampire.php"?($subdir=="//"?"/":$subdir)."special/vampire.php":"none")=>"Du kannst diese Datei JETZT nicht lesen!",
	($session[user][specialinc]=="gladiator.php"?($subdir=="//"?"/":$subdir)."special/gladiator.php":"none")=>"Du kannst diese Datei JETZT nicht lesen!",
	($session[user][specialinc]=="alter.php"?($subdir=="//"?"/":$subdir)."special/alter.php":"none")=>"Du kannst diese Datei JETZT nicht lesen!",
	($session[user][specialinc]=="darkhorse.php"?($subdir=="//"?"/":$subdir)."special/darkhorse.php":"none")=>"Du kannst diese Datei JETZT nicht lesen!",
	($session[user][specialinc]=="necromancer.php"?($subdir=="//"?"/":$subdir)."special/necromancer.php":"none")=>"Du kannst diese Datei JETZT nicht lesen!",
	($session[user][specialinc]=="sacrificealtar.php"?($subdir=="//"?"/":$subdir)."special/sacrificealtar.php":"none")=>"Du kannst diese Datei JETZT nicht lesen!",
	($session[user][specialinc]=="stonehenge.php"?($subdir=="//"?"/":$subdir)."special/stonehenge.php":"none")=>"Du kannst diese Datei JETZT nicht lesen!",
	($session[user][specialinc]=="castle.php"?($subdir=="//"?"/":$subdir)."special/castle.php":"none")=>"Du kannst diese Datei JETZT nicht lesen!",
	($session[user][specialinc]=="randdragon.php"?($subdir=="//"?"/":$subdir)."special/randdragon.php":"none")=>"Du kannst diese Datei JETZT nicht lesen!",
	($session[user][specialinc]=="forestlake.php"?($subdir=="//"?"/":$subdir)."special/forestlake.php":"none")=>"Du kannst diese Datei JETZT nicht lesen!",
	($session[user][specialinc]=="remains.php"?($subdir=="//"?"/":$subdir)."special/remains.php":"none")=>"Du kannst diese Datei JETZT nicht lesen!",
	($session[user][specialinc]=="wannabe.php"?($subdir=="//"?"/":$subdir)."special/wannabe.php":"none")=>"Du kannst diese Datei JETZT nicht lesen!",
	($session[user][specialinc]=="graeultat.php"?($subdir=="//"?"/":$subdir)."special/graeultat.php":"none")=>"Du kannst diese Datei JETZT nicht lesen!",
);

$legal_files=array();

while (list($key,$val)=each($legal_dirs)){
	//echo "<pre>$key</pre>";
	$skey = substr($key,strlen($subdir));
	//echo $skey." ".$key;
	if ($key==dirname($_SERVER[SCRIPT_NAME])) $skey="";
	$d = dir("./$skey");
	if (substr($key,0,2)=="//") $key = substr($key,1);
	if ($key=="//") $key="/";
	while (false !== ($entry = $d->read())) {
			if (substr($entry,strrpos($entry,"."))==".php"){
				$zeit=filemtime("$skey$entry");
				$zeit = gmdate("d M Y",$zeit);
				if ($illegal_files["$key$entry"]!=""){
					if ($illegal_files["$key$entry"]=="X")
					{
						//we're hiding the file completely.
					}
					else
					{
						$files[$key][$entry]= array('skey'=>$skey,'entry'=>$entry,'zeit'=>$zeit);
					}
				}else{
					
					$legal_files[$key.$entry] = true;
					$files[$key][$entry]= array('skey'=>$skey,'entry'=>$entry,'zeit'=>$zeit);
				}
			}
	}
	$d->close();
}

echo "LoGD Standardrelease steht <a href='http://sourceforge.net/projects/lotgd'>hier zum Download</a> zur Verfügung!<br><br>";
echo "<h1>Zeige Source: ", htmlentities($url), "</h1>";
echo "<a href='#source'>Hier klicken für den Source,</a> ODER<br>";
echo "<b>Weitere Dateien, von denen du den Quelltext sehen kannst:</b><br>(Das Lesen des Source, um sich spielerische Vorteile zu verschaffen, ist nicht erlaubt.
 Solltest du Schwachstellen oder Fehler entdecken, bist du als Spieler verpflichtet, diese zu melden.)<ul>";

ksort($files);

foreach($files as $dirkey=>$dir) {
	
	ksort($files[$dirkey]);
	
	foreach($files[$dirkey] as $key=>$f) {
	
		if($legal_files[$dirkey.$key]) {
		
			echo "<li>$f[zeit] - <a href='source.php?url=$dirkey$key'>$f[skey]$f[entry]</a></li>\n";
		
		}
		else {
			
			echo "<li>$f[zeit] - $f[skey]$f[entry] &#151; Datei kann nicht angezeigt werden: ".$illegal_files[$dirkey.$key]."</li>\n";
			
		}
	}

}

echo "</ul>";

echo "<h1><a name='source'>Source von: ", htmlentities($url), "</a></h1>";

$page_name = substr($url,strlen($subdir)-1);
if (substr($page_name,0,1)=="/") $page_name=substr($page_name,1);
if ($legal_files[$url]){
	show_source($page_name);
}else if ($illegal_files[$url]!="" && $illegal_files[$url]!="X"){
	echo "<p>Datei kan nicht angezeigt werden: $illegal_files[$url]</p>";
}else {
	echo "<p>Datei kann nicht angezeigt werden.</p>";
}
?>
