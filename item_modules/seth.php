<?php

function seth_hook_process ( $item_hook , &$item ) {
	
	global $session,$item_hook_info;
	
	switch ( $item_hook ) {
		
		case 'furniture':
			
			if($_GET['act'] == '') {						
				output ("`7Du begibst dich zur lebensgroßen Seth-Statue und entdeckst eine Plakette mit der Aufschrift :`n`&Lieber Benutzer, sorge bitte dafür dass du auch fähig bist Musik zu hören bevor du Seth benutzt. Vielen Dank`7`n`n");
				addnav("Wähle deinen Song");
				addnav("Membrain",$item_hook_info['link']."&act=seth2&snd=0");
				addnav("Ich liebe MightyE",$item_hook_info['link']."&act=seth2&snd=1");
				addnav("Sanfte Momente",$item_hook_info['link']."&act=seth2&snd=2");
				addnav("Klagelied",$item_hook_info['link']."&act=seth2&snd=3");
				addnav("Ameisensong",$item_hook_info['link']."&act=seth2&snd=4");
				addnav("Schlachtruf",$item_hook_info['link']."&act=seth2&snd=5");
			}
			
			else if ($_GET[act]=="seth2"){
				if ($session['user']['prefs']['nosounds']) {
				output("`&Seth schaut dich grimmig an und natürlich hörst du keine Musik, da du es nicht kannst. Vielleicht solltest du erst mal in den Optionen die Musik einschalten ?");
				addnav($msg,$link);
				} else {
					switch($_GET[snd]) {
					case 0 :
					output("<embed src=\"media/ragtime.mid\" width=10 height=10 autostart=true loop=false hidden=true volume=100>",true);
					break;
					case 1 :
					output("<embed src=\"media/matlock.mid\" width=10 height=10 autostart=true loop=false hidden=true volume=100>",true);
					break;
					case 2 :
					output("<embed src=\"media/indianajones.mid\" width=10 height=10 autostart=true loop=false hidden=true volume=100>",true);
					break;
					case 3 :
					output("<embed src=\"media/eternal.mid\" width=10 height=10 autostart=true loop=false hidden=true volume=100>",true);
					break;
					case 4 :
					output("<embed src=\"media/babyphan.mid\" width=10 height=10 autostart=true loop=false hidden=true volume=100>",true);
					break;
					case 5 :
					output("<embed src=\"media/knightrider.mid\" width=10 height=10 autostart=true loop=false hidden=true volume=100>",true);
					break;
					}
				}
				
				output("`&Die Seth Statue beginnt sich zu bewegen und schlägt ihre Laute. Sie bewegt sogar ihren Mund! Einfach toll");
			}
			
			addnav($item_hook_info['back_msg'],$item_hook_info['back_link']);
			
			break;
			
	}
		
	
}

?>