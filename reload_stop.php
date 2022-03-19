<!-- Created 2005 by tcb / Talion for http://lotgd.drachenserver.de -->
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//DE">
<html>
<head>
<title>LoGD 0.9.7 +jt ext (GER) 3</title>
<link href="templates/yarbrough.css" rel="stylesheet" type="text/css">
</head>
<body bgcolor="#000000" text="#CCCCCC">
<table border="0" cellpadding=4 cellspacing=0 width="100%">
  <tr>
    <td colspan=2 class="pageheader" valign="top"> <img src="images/title.gif" align="right"><span class="pagetitle"><br>

    LoGD 0.9.7 +jt ext (GER) 3 </span></td>

  </tr>
  <tr>
    <td width=190 bgcolor="#433828" valign="top" align="center"> 
      <a href="motd.php" target="_blank" onClick='window.open("motd.php","motdphp","scrollbars=yes,resizable=yes,width=550,height=300");return false;' class="motd"><b>MoTD</b></a> <br>
       <br>
      <a href="petition.php" onClick='window.open("petition.php","petitionphp","scrollbars=yes,resizable=yes,width=550,height=300");return false;' target="_blank" align="right" class="motd">Anfrage schreiben</a> <br>
      <a href="petition.php?op=faq" target="_blank" class="motd" onClick='window.open("petition.php?op=faq","petitionphpopfaq","scrollbars=yes,resizable=yes,width=550,height=300");return false;'><b>Regeln</b> und FAQ</a> <br>

 </td>
    <td width="100%" rowspan=2 valign="top" bgcolor="#352D20">
	
	<h2>Aktualisierungssperre!</h2>
	
	Um die Belastung des Servers nicht unnötig zu steigern und damit mehr Nutzern einen störungsfreien Spielgenuss zu garantieren, kann die Seite im ausgeloggten Zustand nur alle
	<b> <?=RELOAD_STOP_TIME ?> Sekunden</b> aktualisiert werden.<br>
	Wir bitten um euer Verständnis, die Maßnahme war leider notwendig. Letztendlich kommt es euch zu Gute, da mehr Spieler gleichzeitig online sein können!<br>
	Weiter gehts mit einem Klick auf den aktiven Button:<br><br>
	
	<div align="center"><input id="ok_button" type="button" value="Weiter!" onclick='document.location="index.php"'></div>

		<script type="text/javascript" language="JavaScript">
			var count = <? echo (RELOAD_STOP_TIME-$timediff); ?>;
			counter();
			function counter () {
				if(count == 0) {
					document.getElementById("ok_button").value = "Weiter!";
					document.getElementById("ok_button").disabled = false;
				}
				else {
					document.getElementById("ok_button").value = "Weiter! (noch "+count+" Sekunden)";
					document.getElementById("ok_button").disabled = true;
					count--;
					setTimeout("counter()",1000);
				}
			}	
		</script>
	
	
    </td>

  </tr>
  <tr>
    <td width="190" valign="top" bgcolor="#433828">
      
      
  </tr>
  <tr>

    <td colspan=2 class="footer">
      <table border="0" cellpadding="0" cellspacing="0" width="100%" class="noborder">
        <tr>
          <td class="noborder">Copyright 2002-2003, Game: Eric Stevens, Design: Chris Yarbrough</td>

          <td align="right" class="noborder"></td>
        </tr>
				  
      </table>
    </td>

  </tr>
</table>
</body>
</html>
