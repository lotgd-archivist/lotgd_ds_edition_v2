<?
/**
* music.php: 	Stellt Seitenrahmen für einen Musik- / Streaming-Player zwecks Abspielen der Ingame-Musik
*				zur Verfügung
* @author talion
* @version DS-E V/2
*/

// Ausgabestring
$str_out = '';

// Was soll gespielt werden?
$str_play = $_GET['play'];

// 'Playlist'
if($_GET['op'] == 'pl') {
	$str_out .= '<html><head></head><body>';
	$str_out .= '	<script type="text/javascript">
						function add_entry (filename, title) {
							var pl = document.getElementById("playlist");
							var new_entry =	new Option(title, filename);
							pl.options[pl.length] = new_entry;
						}
						window.setTimeout("location.reload()",3000);
					</script>
				';
						
	$str_out .= '	<select id="playlist" onchange="parent.document.location.href=\'music.php?play=\'+this.options[this.options.selectedIndex].value;">';
	$str_out .= '		<option value="" '.(empty($str_play) ? 'selected="selected"':'').'> ~~~~~ </option>';
	
	session_register('session');

	$session =& $_SESSION['session'];
	
	$arr_pl = $session['playlist'];
						
	if(is_array($arr_pl)) {
		
		foreach($arr_pl as $m) {
			
			$str_out .= 
				'		<option value="'.$m['filename'].'" '.($str_play==$m['filename'] ? 'selected="selected"':'').'>'.$m['title'].'</option>';
			
		}
		
	}
						
	$str_out .= '	</select>
				</body>
				</html>';
	echo($str_out);	
}
// Player, Rahmen
else {
	
	require_once('common.php');

	if(!$session['user']['loggedin']) {
		exit;
	}
	
	if($session['user']['acctid'] != 2310) {
		exit;
	}
	
	// Feststellen, ob Spieler über benötigtes Item verfügt
	/*if(!item_count(' tpl_id="brde" AND owner='.$session['user']['acctid'])) {
		exit;
	}*/
	
	// Seitenstart
	popup_header('Der Wanderbarde');
		
	$str_out .= '<iframe src="music.php?op=pl" width="200" height="50" name="playlist"></iframe>';
	if(!empty($str_play)) {
		$str_out .= '
						<embed id="player_src" src="media/'.$str_play.'" width=10 height=10 autostart=true loop=false hidden=true volume=100>
					';
	}
	else {
		$str_out .= 'Kein Musikstück ausgewählt!';
	}
	
	// Ausgabe
	rawoutput($str_out);
		
	// Seitenende
	popup_footer();
}
?>