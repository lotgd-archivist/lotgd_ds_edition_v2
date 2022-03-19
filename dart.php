<?php

// by Maris (Maraxxus@gmx.de)
// Bilderwackelscript by talion

require_once "common.php";

page_header("Dartspiel");

$points=$_GET['points'];
$round=$_GET['round'];
$pointstot=$_GET['pointstot'];

    if (!$round)
     {
       output("`4Versuche dein Glück!`0`n`n");
       $round=0;
       $pointstot=0;
     }
     else
     {
     if ($points>0)
     {
     $pointstot+=$points;
     output("`&Du hast `^{$points}`& Punkte gemacht!`n
     Gesamtpunktzahl: `^{$pointstot}`&`n`n");
     }
     else switch (e_rand(1,5))
     {
     case 1 :
     output("`&Du hast `4Cedriks Bierfässer`& getroffen!");
     break;
     case 2 :
     output("`&Du hast `4eine streunende Katze`& erlegt!");
     break;
     case 3 :
     output("`&Du hast `4Violets Allerwertesten`& getroffen!");
     break;
     case 4 :
     output("`&Du hast den Pfeil `4zum Fenster raus`& geworfen!");
     break;
     case 5 :
     output("`&Du hast `4einem Gast`& das Bierglas aus der Hand geschossen!");
     break;
     }
     }
     if ($round>=10)
     {
       output("`&`nDas Spiel ist vorbei.`0`n`n");
       addnav("Zurück","inn.php");
     }
     else
     {
     $round++;
     addnav("`&`bGib dein Bestes!`b`n");
     addnav('Aufhören','inn.php');
	 
	 $str_out = '
	 		
			<script>
				var str_id = "bild";
				
				function wackel() {
					
					document.getElementById(str_id).style.left = 650 - 35*Math.random()+"px";
					document.getElementById(str_id).style.top = 150 - 20*Math.random()+"px";

				}
			
			</script>
	 		<script>
				window.setInterval("wackel()",400);
			</script>
	 
	 ';
	 
	 $str_lnk = "dart.php?op=play&round=$round&pointstot=$pointstot";
	 
     output('<div><map name="dart">
     <area shape="circle" coords="151,159,3" href="'.$str_lnk.'&points=50">
     <area shape="circle" coords="151,159,9" href="'.$str_lnk.'&points=25">
     <area shape="rect" coords="142,98,162,91" href="'.$str_lnk.'&points=60">
     <area shape="rect" coords="160,98,182,99" href="'.$str_lnk.'&points=3">
     <area shape="rect" coords="180,104,199,111" href="'.$str_lnk.'&points=54">
     <area shape="rect" coords="195,115,212,128" href="'.$str_lnk.'&points=12">
     <area shape="rect" coords="207,132,219,149" href="'.$str_lnk.'&points=39">
     <area shape="rect" coords="213,150,220,169" href="'.$str_lnk.'&points=18">
     <area shape="rect" coords="212,169,214,190" href="'.$str_lnk.'&points=30">
     <area shape="rect" coords="207,188,200,208" href="'.$str_lnk.'&points=45">
     <area shape="rect" coords="195,203,183,220" href="'.$str_lnk.'&points=6">
     <area shape="rect" coords="180,215,162,227" href="'.$str_lnk.'&points=51">
     <area shape="rect" coords="160,221,141,227" href="'.$str_lnk.'&points=9">
     <area shape="rect" coords="141,229,123,215" href="'.$str_lnk.'&points=57">
     <area shape="rect" coords="123,215,104,207" href="'.$str_lnk.'&points=21">
     <area shape="rect" coords="107,203,90,190" href="'.$str_lnk.'&points=48">
     <area shape="rect" coords="96,187,84,170" href="'.$str_lnk.'&points=24">
     <area shape="rect" coords="90,168,84,150" href="'.$str_lnk.'&points=33">
     <area shape="rect" coords="84,149,96,132" href="'.$str_lnk.'&points=42">
     <area shape="rect" coords="96,131,103,112" href="'.$str_lnk.'&points=27">
     <area shape="rect" coords="108,114,120,99" href="'.$str_lnk.'&points=36">
     <area shape="rect" coords="123,104,141,92" href="'.$str_lnk.'&points=15">
     <area shape="rect" coords="135,56,168,50" href="'.$str_lnk.'&points=40">
     <area shape="rect" coords="170,49,198,66" href="'.$str_lnk.'&points=2">
     <area shape="rect" coords="202,61,224,85" href="'.$str_lnk.'&points=36">
     <area shape="rect" coords="230,81,244,111" href="'.$str_lnk.'&points=8">
     <area shape="rect" coords="250,109,254,142" href="'.$str_lnk.'&points=26">
     <area shape="rect" coords="255,144,262,176" href="'.$str_lnk.'&points=12">
     <area shape="rect" coords="255,176,251,209" href="'.$str_lnk.'&points=20">
     <area shape="rect" coords="225,232,251,211" href="'.$str_lnk.'&points=30">
     <area shape="rect" coords="225,234,203,258" href="'.$str_lnk.'&points=4">
     <area shape="rect" coords="199,252,169,269" href="'.$str_lnk.'&points=34">
     <area shape="rect" coords="167,262,135,269" href="'.$str_lnk.'&points=6">
     <area shape="rect" coords="133,269,103,252" href="'.$str_lnk.'&points=38">
     <area shape="rect" coords="104,252,72,239" href="'.$str_lnk.'&points=14">
     <area shape="rect" coords="73,237,59,207" href="'.$str_lnk.'&points=32">
     <area shape="rect" coords="52,208,48,176" href="'.$str_lnk.'&points=16">
     <area shape="rect" coords="42,175,49,145" href="'.$str_lnk.'&points=22">
     <area shape="rect" coords="43,141,59,112" href="'.$str_lnk.'&points=28">
     <area shape="rect" coords="53,109,77,86" href="'.$str_lnk.'&points=18">
     <area shape="rect" coords="78,85,101,61" href="'.$str_lnk.'&points=24">
     <area shape="rect" coords="104,67,135,50" href="'.$str_lnk.'&points=10">
     <area shape="poly" coords="151,159,135,50,169,50,151,159" href="'.$str_lnk.'&points=20">
     <area shape="poly" coords="151,159,169,50,201,61,151,159" href="'.$str_lnk.'&points=1">
     <area shape="poly" coords="151,159,201,61,230,81,151,159" href="'.$str_lnk.'&points=18">
     <area shape="poly" coords="151,159,230,81,250,109,151,159" href="'.$str_lnk.'&points=4">
     <area shape="poly" coords="151,159,250,109,261,142,151,159" href="'.$str_lnk.'&points=13">
     <area shape="poly" coords="151,159,261,142,262,176,151,159" href="'.$str_lnk.'&points=6">
     <area shape="poly" coords="151,159,262,176,251,209,151,159" href="'.$str_lnk.'&points=10">
     <area shape="poly" coords="151,159,251,209,231,238,151,159" href="'.$str_lnk.'&points=15">
     <area shape="poly" coords="151,159,231,238,202,259,151,159" href="'.$str_lnk.'&points=2">
     <area shape="poly" coords="151,159,202,259,169,270,151,159" href="'.$str_lnk.'&points=17">
     <area shape="poly" coords="151,159,169,270,135,269,151,159" href="'.$str_lnk.'&points=3">
     <area shape="poly" coords="151,159,135,269,101,259,151,159" href="'.$str_lnk.'&points=19">
     <area shape="poly" coords="151,159,101,259,73,237,151,159" href="'.$str_lnk.'&points=7">
     <area shape="poly" coords="151,159,73,237,53,209,151,159" href="'.$str_lnk.'&points=16">
     <area shape="poly" coords="151,159,53,209,42,177,151,159" href="'.$str_lnk.'&points=8">
     <area shape="poly" coords="151,159,42,177,42,143,151,159" href="'.$str_lnk.'&points=11">
     <area shape="poly" coords="151,159,42,143,52,109,151,159" href="'.$str_lnk.'&points=14">
     <area shape="poly" coords="151,159,52,109,73,81,151,159" href="'.$str_lnk.'&points=9">
     <area shape="poly" coords="151,159,73,81,101,60,151,159" href="'.$str_lnk.'&points=12">
     <area shape="poly" coords="151,159,101,60,135,50,151,159" href="'.$str_lnk.'&points=5">
     <area shape="rect" coords="1,1,307,317" href="'.$str_lnk.'&points=0">
     ',true);

     for ($i=0;$i<=60;$i++){
     addnav("",$str_lnk.'&points='.$i);
     }
     
     output('</map></div>`n<p>
	 			<img src="images/dart.jpg" id="bild" usemap="#dart" style="position:absolute;left:650px;top:180px;">
			</p>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
			
			`n',true);
	 
	 output($str_out,true);
	 
     output("`@Das ist dein `^{$round}. Wurf`@ von `^10`@.`&");
   }
   
page_footer();
?>
