<?php

function buecher_hook_process ( $item_hook , &$item ) {
	
	global $session,$item_hook_info;
	
	switch ( $item_hook ) {
		
		case 'furniture':
									
			output("`2Du greifst in das B�cherregal und nimmst wahllos eines der B�cher heraus.`n`n ");
			if ($session['user']['turns']<=0) {
				output("`2Es ist das 3000-Seitige Werk '`#Durch Askese den Zugang zur Transzendenz erlangen'`2.`n ");
				output("Auf sowas hast du heute nun wirklich keine Lust mehr! Du stellst das Buch zur�ck.`n`n ");
			} 
			else {
			
				switch(e_rand(1,11)){
			
					case 1:
					  output("`2Du liest das Buch '`#Was Alkohol deinem K�rper antut`2'.`n ");
						if ($session['user'][drunkenness]>0) {
					  output("`2Geschockt durch die offene Direktheit dieses Buches wirst du schlagartig wieder `@n�chtern`2!`n ");
					  } else { output("`2Du denkst : `@Wie gut, dass ich nicht trinke!`2`n");
					  output("`2Irgendwie k�nntest du dennoch jetzt ein gutes Bierchen vertragen - auf den Schrecken.`n`n ");
					  }
					  $session['user'][drunkenness]=0;

					  break;
					
					  case 2:
					  output("`2Du liest das Buch '`#Das kleine Handbuch f�r die feine Gesellschaft`2'.`n ");
					  output("`2Dir er�ffnen sich v�llig neue Perspektiven im gesellschaftlichen Umgang.");
					  output("`2Du erh�lst `@einen Charmpunkt`2!");
					  output("`2Beim Lesen des B�chleins verlierst du jedoch einen Waldkampf.");
					  $session[user][turns]-=1;
					  $session[user][charm]+=1;

					  break;
					
					  case 3:
					  output("`2Du liest das Buch '`#Romeo und Julia`2'.`n ");
					  output("`2V�llig ergriffen von der Tragik dieses Werkes wirst du den Rest des Tagen schniefend mit einem Taschentuch verbringen.`n ");
					  output("`2Du verlierst alle deine verbleibenden Waldk�mpfe!`n ");
					  $session[user][turns]=0;
					  addnews("`@".$session['user']['name']."`@ wurde gesehen wie ".($session[user][sex]?"sie":"er")." mit einem Taschentuch umherlief und heulend von Liebe und Leid erz�hlte.`n");

					  break;
					
					  case 4:
					  output("`2Du liest das Buch '`#K�nig Arthus`2'.`n ");
					  output("`2V�llig mitgerissen von der Geschichte steigt deine Kampfeslust.`n ");
					  output("`2Du erh�lst `@3 Waldk�mpfe`2!`n ");
					  $session[user][turns]+=3;

					  addnews("`@".$session['user']['name']."`@ rannte mit einem lauten Kampfschrei von ".($session[user][sex]?"ihrem":"seinem")." Haus direkt in den Wald.`n");
					  break;
					
					  case 5:
					  output("`2Du liest das Buch '`#Harry Potter und der offene Klodeckel`2'.`n ");
					  output("`2Nachdem du angefangen hast zu lesen merkst du wie du langsam `#verdummst`2, jedoch kannst du dich irgendwie nicht davon losreissen.`n ");
					  output("`2Du verlierst `@2%`2 deiner Erfahrung und einen Waldkampf!`n ");
					  $session[user][turns]-=1;
					  $session[user][experience]=$session[user][experience]*0.98;
					  addnews("`@".$session['user']['name']."`@ wurde beobachtet wie ".($session[user][sex]?"sie":"er")." mit einem peinlichen Cape umherlief und laut `#'Bei Dumbledore!'`@ rief.`n");

					  break;
					
					  case 6:
					  output("`2Du liest das Buch '`#Die Weisheiten des Konfusius`2'.`n ");
					  output("`2Dieses Buch ist wirklich sehr lehrreich und du gelangst zu einigen Erkenntnissen.`n ");
					  output("`2Deine Erfahrung steigt um `@5%`2, jedoch verlierst du w�hrend des Lesens einen Waldkampf!`n ");
					  $session[user][turns]-=1;
					  $session[user][experience]=$session[user][experience]*1.05;

					  break;
					
					  case 7:
					  output("`2Du liest das Buch '`#Die R�uber`2'.`n ");
					  output("`2Die rauhe und r�de Art der wilden R�uber fasziniert dich und du beschliesst genauso rauh und r�de zu werden.`n ");
					  output("`2Du verlierst `@einen Charmepunkt`2 und einen Waldkampf.`n ");
					  $session[user][turns]-=1;
					  $session[user][charm]-=1;
					  addnews("`@".$session['user']['name']."`@ wurde beobachtet wie ".($session[user][sex]?"sie":"er")." laut r�lpsend `#'Harr harr harr!'`@ rief.`n");

					  break;
					
					  case 8:
					  output("`2Du liest das Buch '`#Lady Chatterley's Geliebter'`2'.`n ");
					  output("`2Ziemlich schnell stellst du es mit hochrotem Kopf zur�ck ins Regal.`n ");
					  output("`2Vielleicht solltest du doch etwas Anderes lesen.`n ");

					  break;
					
					  case 9:
					  output("`2Du liest das Buch '`#Romeo und Julia`2'.`n ");
					  output("`2V�llig ergriffen von der Tragik dieses Werkes wirst du den Rest des Tagen schniefend mit einem Taschentuch verbringen.`n ");
					  output("`2Du verlierst alle deine verbleibenden Waldk�mpfe!`n ");
					  $session[user][turns]=0;
					  addnews("`@".$session['user']['name']."`@ wurde gesehen wie ".($session[user][sex]?"sie":"er")." mit einem Taschentuch umherlief und heulend von Liebe und Leid erz�hlte.`n");

					  break;
					
					  case 10:
					  output("`2Du liest das Buch '`#Der Gladiator'`2'.`n ");
					  output("`2Angespornt durch die packende Geschichte erh�lst du `@einen Spielerkampf`2 mehr.`n ");
					  output("`2Auf in die Felder!.`n ");
					  $session[user][playerfights]+=1;

					  break;
					
					  case 11:
					  output("`2Du liest das Buch '`#Angewandte Heilkunde'`2'.`n ");
					  if ($session[user][hitpoints]<$session[user][maxhitpoints]) {
					  output("`2Du befolgst die Anweisungen des Buches und verarztest deine Wunden.`n ");
					  output("`@Dadurch heilst du komplett!`2`n ");
					  $session[user][hitpoints]=$session[user][maxhitpoints];
					  } else {
					  output("`2Doch da du nicht verwundet bist hilft dir das im Moment nicht weiter.`n ");
					  }
					  output("`2Du verlierst `@einen Waldkampf`2.`n ");
					  $session[user][turns]-=1;

					  break;
			
				  }
			  }
					
			addnav($item_hook_info['back_msg'],$item_hook_info['back_link']);
			
			break;
			
	}
		
	
}

?>