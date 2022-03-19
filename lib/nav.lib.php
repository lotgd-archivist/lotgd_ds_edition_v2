<?php

// Einstellungsarrays für Navs
$allowanonymous=array('index.php'=>true,'login.php'=>true,'create.php'=>true,'create_rules.php'=>true,'about.php'=>true,'list.php'=>true,'petition.php'=>true,'connector.php'=>true,'logdnet.php'=>true,'referral.php'=>true,'news.php'=>true,'motd.php'=>true,'topwebvote.php'=>true,'source.php'=>true);
$allownonnav = array('badnav.php'=>true,'motd.php'=>true,'petition.php'=>true,'mail.php'=>true,'topwebvote.php'=>true,'chat.php'=>true,'source.php'=>true,'watchsu.php'=>true,'comment_funcs.php'=>true,'prefs_new.php'=>true,'music.php'=>true,'su_comment.php'=>true);
$nokeeprestore=array('newday.php'=>1,'badnav.php'=>1,'motd.php'=>1,'mail.php'=>1,'petition.php'=>1,'chat.php'=>1,'comment_funcs.php'=>1,'prefs_new.php'=>1,'music.php'=>true,'su_comment.php'=>true);

$accesskeys=array();
$quickkeys=array();
function addnav($text,$link=false,$priv=false,$pop=false,$newwin=false,$hotkey=true)
{
	global $nav,$session,$accesskeys,$REQUEST_URI,$quickkeys;
	if ($link===false)
	{
		$nav.=templatereplace('navhead',array('title'=>appoencode($text,$priv)));
	}
	elseif (empty($link)) 
	{
		$nav.=templatereplace('navhelp',array('text'=>appoencode($text,$priv)));
	}
	else
	{
		if (!empty($text))
		{
			$extra='';
			if ($newwin===false) 
			{
				if (strpos($link,'?'))
				{
					$extra='&c='.$session['counter'];
				}
				else
				{
					$extra='?c='.$session['counter'];
				}
			}

			if ($newwin===false) 
			{
				$extra.='-'.date('His');
			}
			//$link = str_replace(" ","%20",$link);
			//hotkey for the link.
			if($hotkey) {
				$key='';
				if (substr($text,1,1)=='?') 
				{
					// check to see if a key was specified up front.
					if ($accesskeys[strtolower(substr($text, 0, 1))]==1)
					{
						// output ("key ".substr($text,0,1)." already taken`n");
						$text = substr($text,2);
					}
					else
					{
						$key = substr($text,0,1);
						$text = substr($text,2);
						//output("key set to $key`n");
						$found=false;
						$int_strlen = strlen($text);
						for ($i=0;$i<$int_strlen; $i++)
						{
							$char = substr($text,$i,1);
							if ($ignoreuntil == $char)
							{
								$ignoreuntil='';
							}
							else
							{
								if ($ignoreuntil<>'')
								{
									if ($char=='<') $ignoreuntil='>';
									if ($char=='&') $ignoreuntil=';';
									if ($char=='`') $ignoreuntil=substr($text,$i+1,1);
								}
								else
								{
									if ($char==$key) 
									{
										$found=true;
										break;
									}
								}
							}
						}
						if ($found==false) 
						{
							if (strpos($text, '__') !== false)
							{
								$text=str_replace('__', '('.$key.') ', $text);
							}
							else
							{
								$text='('.strtoupper($key).') '.$text;
							}
							$i=strpos($text, $key);
							// output("Not found`n");
						}
					}
				}
				if (empty($key))
				{
					$int_strlen = strlen($text);
					for ($i=0;$i<$int_strlen; $i++)
					{
						$char = substr($text,$i,1);
						if ($ignoreuntil == $char) 
						{
							$ignoreuntil='';
						}
						else
						{
							if (($accesskeys[strtolower($char)]==1) || (strpos('abcdefghijklmnopqrstuvwxyz0123456789', strtolower($char)) === false) || $ignoreuntil<>'')
							{
								if ($char=='<') $ignoreuntil='>';
								if ($char=='&') $ignoreuntil=';';
								if ($char=='`') $ignoreuntil=substr($text,$i+1,1);
							}
							else
							{
								break;
							}
						}
					}
				}
				if ($i<strlen($text))
				{
					$key=substr($text,$i,1);
					$accesskeys[strtolower($key)]=1;
					$keyrep=' accesskey="'.$key.'" ';
				}
				else
				{
					$key='';
					$keyrep='';
				}
				//output("Key is $key for $text`n");
	
				if ($key!='')
				{
					$text=substr($text,0,strpos($text,$key)).'`H'.$key.'`H'.substr($text,strpos($text,$key)+1);
					if ($pop)
					{
						$quickkeys[$key]=popup($link.$extra);
					}
					else
					{
						$quickkeys[$key]="window.location='$link$extra'";
					}
				}
			}
			$nav.=templatereplace('navitem',array(
			"text"=>appoencode($text,$priv),
			"link"=>HTMLEntities($link.$extra),
			"accesskey"=>$keyrep,
			"popup"=>($pop==true ? "target='_blank' onClick=\"".popup($link.$extra)."; return false;\"" : ($newwin==true?"target='_blank'":""))
			));
			//$nav.="<a href=\"".HTMLEntities($link.$extra)."\" $keyrep class='nav'>".appoencode($text,$priv)."<br></a>";
		}
		$session['allowednavs'][$link.$extra]=true;
		$session['allowednavs'][str_replace(' ', '%20', $link).$extra]=true;
		$session['allowednavs'][str_replace(' ', '+', $link).$extra]=true;
		
		return($link.$extra);
	}
}

function clearnav()
{
	$session['allowednavs']=array();
}

function redirect($location,$reason=false)
{
	global $session,$REQUEST_URI;
		
	if ($location!='badnav.php')
	{
		$session[allowednavs]=array();
		addnav('',$location);
	}
	if (strpos($location,'badnav.php')===false) $session['output']="<a href=\"".HTMLEntities($location)."\">Hier klicken</a>";
	$session['debug'].="Redirected to $location from $REQUEST_URI.  $reason\n";
	saveuser();
	header("Location: $location");
	echo $location;
	echo $session['debug'];
	exit();
}

function forest($noshowmessage=false)
{
	global $session,$playermount;
	$conf = unserialize($session['user']['donationconfig']);
	if ($conf['healer'] || $session['user']['acctid']==getsetting('hasegg',0) || ($session['user']['marks']>=31))
	{
		addnav('H?Golindas Hütte','healer.php');
	}
	else
	{
		addnav('H?Hütte des Heilers','healer.php');
	}
	addnav('Kampf');
	addnav('B?Etwas zum Bekämpfen suchen','forest.php?op=search');
	if ($session['user']['level']>1)
	{
		addnav('H?Herumziehen','forest.php?op=search&type=slum');
	}
	addnav('N?Nervenkitzel suchen','forest.php?op=search&type=thrill');
	
	if($session['user']['dragonkills'] >= 50 && $session['user']['level'] <= 14) {
		addnav('l?Hölle suchen','forest.php?op=search&type=extreme');
	}

	addnav('Sonderbare Orte');
	addnav('Die Waldlichtung','waldlichtung.php');
	addnav('Der dunkle Pfad','thepath.php');
	
	$admin = su_check(SU_RIGHT_COMMENT);
	
	if ($session['user']['race']==RACE_TROLL || $session['user']['race']==RACE_AVATAR || $admin)
	{ addnav('Zur Trollfeste','racesspecial.php?race='.RACE_TROLL);}
	if ($session['user']['race']==RACE_ELF || $session['user']['race']==RACE_AVATAR || $admin)
	{ addnav('Zum Elfenhain','racesspecial.php?race='.RACE_ELF);}
	if ($session['user']['race']==RACE_ECHSE || $session['user']['race']==RACE_AVATAR || $admin)
	{ addnav('Zu den Echsensümpfen','racesspecial.php?race='.RACE_ECHSE);}
	if ($session['user']['race']==RACE_DUNKELELF || $session['user']['race']==RACE_AVATAR || $admin)
	{ addnav('Zum Finsterwald','racesspecial.php?race='.RACE_DUNKELELF);}
	if ($session['user']['race']==RACE_WERWOLF || $session['user']['race']==RACE_AVATAR || $admin)
	{ addnav('Zur Werwolflichtung','racesspecial.php?race='.RACE_WERWOLF);}
	if ($session['user']['race']==RACE_ORK || $session['user']['race']==RACE_AVATAR || $admin)
	{ addnav('Zur Orkfeste','racesspecial.php?race='.RACE_ORK);}
	if ($session['user']['race']==RACE_DAEMON || $session['user']['race']==RACE_AVATAR || $admin)
	{ addnav('Zu den Schwefelquellen','racesspecial.php?race='.RACE_DAEMON);}
	if ($session['user']['race']==RACE_ENGEL || $session['user']['race']==RACE_AVATAR || $admin)
	{ addnav('Zur Wolkenfestung','racesspecial.php?race='.RACE_ENGEL);}
	if ($session['user']['race']==RACE_AVATAR || $session['user']['race']==RACE_AVATAR || $admin)
	{ addnav('In die Leere','racesspecial.php?race='.RACE_AVATAR);}

	//if ($session[user][hashorse]>=2) addnav('D?Dark Horse Tavern",'forest.php?op=darkhorse");
	if ($playermount['tavern']>0) addnav('D?Nimm '.$playermount['mountname'].' zur Dark Horse Taverne','forest.php?op=darkhorse');
	if ($playermount['tavern']>0 && $conf['castle']) addnav('B?Nimm '.$playermount['mountname'].' zur Burg','forest.php?op=castle');
	if ($conf['goldmine']>0) addnav('Goldmine ('.$conf[goldmine].'x)','paths.php?ziel=goldmine&pass=conf');

	addnav('','forest.php');
	if ($session['user']['level']>=15  && $session['user']['seendragon']==0){
		addnav('G?`@Den Grünen Drachen suchen','forest.php?op=dragon');
	}
	addnav('Sonstiges');
	addnav('Z?Zurück zum Dorf','village.php');
	addnav('Z?Zurück zum Marktplatz','market.php');
	addnav('P?Plumpsklo','outhouse.php');
	if ($session['user']['turns']<=1 ) 
	{
		addnav('Hexenhaus','hexe.php');
	}
	if ($noshowmessage!=true){
        if ($session['user']['prefs']['noimg']==1) output('`c`7`bDer Wald`b`0`c`n');
		output('
		Der Wald, Heimat von bösartigen Kreaturen und üblen Übeltätern aller Art.`n`n
		Die dichten Blätter des Waldes erlauben an den meisten Stellen nur wenige Meter Sicht.  
		Die Wege würden dir verborgen bleiben, hättest du nicht ein so gut geschultes Auge. Du bewegst dich so leise wie 
		eine milde Brise über den dicken Humus, der den Boden bedeckt. Dabei versuchst du es zu vermeiden 
		auf dünne Zweige oder irgendwelche der ausgebleichten Knochenstücke zu treten, welche den Waldboden spicken. 
		Du verbirgst deine Gegenwart vor den abscheulichen Monstern, die den Wald durchwandern.');

        if ($session['user']['turns']<=1)
		{
			output(' In der Nähe siehst du wieder den Rauch aus dem Kamin eines windschiefen Hexenhäuschens aufsteigen, von dem du schwören könntest, es war eben noch nicht da. ');
		}
	}

	// Imagemap by Maris
	if ($session['user']['prefs']['noimg']==0)
	{
			 $gen_output='`c`7`bDer Wald`b`0`c`n<div><map name="Der Wald">';
			 if ($session['user']['level']>1)
			 {
			 $gen_output.='<area shape="rect" coords="30,150,100,40" href="forest.php?op=search&type=slum" title="Herumziehen">';
			 addnav('','forest.php?op=search&type=slum');
			 }
			 $gen_output.='<area shape="rect" coords="170,160,260,50" href="forest.php?op=search" title="Etwas zum Bekämpfen suchen">
			 <area shape="rect" coords="310,190,380,60" href="forest.php?op=search&type=thrill" title="Nervenkitzel">';
			 addnav('','forest.php?op=search');
			 addnav('','forest.php?op=search&type=thrill');
			 if($session['user']['dragonkills'] >= 50 && $session['user']['level'] <= 14)
			 {
			 $gen_output.='<area shape="rect" coords="470,190,520,100" href="forest.php?op=search&type=extreme" title="Hölle suchen">';
			 addnav('','forest.php?op=search&type=extreme');
			 }
			 $gen_output.='</map></div>`n<p><center><img border="0" src="images/forest.jpg" usemap="#Der Wald"></center></p>`n';
			 headoutput($gen_output,true);
	}
	// Ende Imagemap

	//Changed to adapt the walspecialeditor needs
	if (su_check(SU_RIGHT_FORESTSPECIAL))
	{
		output('`n`nSUPERUSER Specials:`n');
		$query_result = db_query('SELECT filename FROM waldspecial ORDER BY filename ASC') or die(db_error(LINK));
		$count = db_num_rows($query_result);
		for ($i=0;$i<$count;$i++)
		{
			$row = db_fetch_assoc($query_result);
			output('<a href="forest.php?specialinc='.$row['filename'].'">'.$row['filename'].'</a>`n', true);
			addnav('','forest.php?specialinc='.$row['filename']);
		}
	}
}
?>