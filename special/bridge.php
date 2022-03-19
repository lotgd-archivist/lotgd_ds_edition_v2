<?
/********************************************
/* The Bridge of Death - forest special
/* Description: this is a port of the monty python and the holy grail skit.
/* filename: bridge.php
/* original mod by Sixf00t4 for sixf00t4.com/dragon
/* modified and translated by SkyPhy, July 2004
/*************************************************
/* INSTALLATION
/* simply put bridge.php in your special directory
/*************************************************/
if (!isset($session)) exit();
if ($HTTP_GET_VARS[op]==""){
    addnav("Frage, ich kenne keine Furcht!","forest.php?op=ask");
    addnav("LAUF WEG!","forest.php?op=leave");
    $session[user][specialinc] = "bridge.php";
    output("`n`3`c`bBr�cke des Todes`b`c `n `n");
    output(" `2Du wanderst durch den Wald und triffst auf den Mann aus der Szene 24`n");
    output(" Du hast die `3Br�cke des Todes`2! gefunden`n");
    output(" `2Die H�ngebr�cke ist in einem schrecklichen Zustand, aber es ist der einzige Weg auf die andere Seite.`n");
    output(" Der W�chter der Br�cke ruft, `3 Stop!  Wer �ber die Br�cke des Todes will gehen, der mu� 3 mal Rede und Antwort stehen.`2`n");
    }
else if ($HTTP_GET_VARS[op]=="ask"){
    addnav($session[user][name],"forest.php?op=lance");
    addnav("Lancelot","forest.php?op=know");
    addnav("Kasperl","forest.php?op=know");
    $session[user][specialinc] = "bridge.php";
    output("WELCHES...ist dein Name?`n");
    }
else if ($HTTP_GET_VARS[op]=="lance"){
    $session[user][specialinc] = "bridge.php";
    addnav("Die Suche nach dem gr�nen Drachen","forest.php?op=grail");
    addnav("Sauffen bis zum umfallen","forest.php?op=know");
    output("WELCHES...ist Deine Aufgabe?`n");
    }
else if ($HTTP_GET_VARS[op]=="grail"){
    $session[user][specialinc] = "bridge.php";
    switch(e_rand(1,10)){
        case 1:
        case 2:
        case 3:
        case 4:
        addnav("Das wei� ich nicht","forest.php?op=know");
        output("WELCHES...ist die Hauptstadt von Assyrien?`n");
        break;
        case 5:
        case 6:
        case 7:
        case 8:
        addnav("Blau","forest.php?op=blue");
	addnav("Gr�n","forest.php?op=blue");
	addnav("Rot","forest.php?op=blue");
        output("WELCHES...ist Deine Lieblingsfarbe?`n");
        break;
        case 9:
        case 10:
        addnav("Afrikanische oder Europ�ische?","forest.php?op=swallow");
        output("WELCHES...ist die Geschwindigkeit einer unbeladenen Taube?`n");
        break;
        }
    }

elseif ($HTTP_GET_VARS[op]=="leave"){
    $session[user][specialinc]="";
    output("`#Eingesch�chtert kommst du in den Wald zur�ck");
    }
else if ($HTTP_GET_VARS[op]=="blue")
{
    $session[user][specialinc] = "bridge.php";
    if (e_rand(0,1)==0)    {
        output("`2Du �nderst schnell Deine Meinung, und noch bevor du `^Gelb`2 sagen kannst... `n");
        output("wirst du in die Luft katapultiert. `4AAAIIIIIIIIHHHHHHHHHHHHHHHH.!`2`n");
	output("Doch du hast Gl�ck und landest nur wenige Zentimeter vom Abgrund entfernt`n");
        output("Allerdings schl�gst du hart auf und verlierst fast alle Lebenspunkte!");
        addnav("Zur�ck in den Wald","forest.php?op=leave");
        $session[user][hitpoints]= 3;
        }
    else{
        $session[user][specialinc]="";
    	addnav("Weiter","forest.php");
        output("Richtig. Du kannst passieren.`n");
        //output("You gain one charm point!`n");
        //$session[user][charm]++;
        //if (e_rand(0,1)==0){
        output("Du �berquerst die Br�cke. In der Mitte der Br�cke findet du `3einen Edelstein`2");
        $session[user][gems]++;
	//}
	}
    }
else if ($HTTP_GET_VARS[op]=="know"){
    output("`4AAAAIIIIIIIIIHHHHHHHHHHHHHHH!`2, `n");
    output("du wirst in hohem Bogen in die Luft katapultiert......`n");
    output("Und st�rzt in den Abgrund!`n`n");
    output("Nat�rlich bist du jetzt....TOT`n");
    $session[user][alive]=false;
    $session[user][hitpoints]=0;
    $session[user][specialinc]="";
    addnav("Zu den News","news.php");
    $session[user][specialinc]="";
    }
else if ($HTTP_GET_VARS[op]=="swallow"){
    output("`4AAAAAAAIIIIIIIIIIIHHHHHHHHHHHHH!, `2`n");
    output("Der W�chter der Br�cke wird in die Luft katapultiert und fliegt in hohen Bogen den Abgrund hinab!`n");
    output("`3\"Woher wei�t du soviel �ber Schwalben?\"`2, ruft er im hinabst�rtzen`n");
    output("F�r dein gro�es Wissen erh�ltst du `500 Erfahrungspunkte`2!`n");
    $session[user][experience]+=500;
    $session[user][specialinc]="";
    addnav("�ber die Br�cke","forest.php");
    }
?>