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
    output("`n`3`c`bBrücke des Todes`b`c `n `n");
    output(" `2Du wanderst durch den Wald und triffst auf den Mann aus der Szene 24`n");
    output(" Du hast die `3Brücke des Todes`2! gefunden`n");
    output(" `2Die Hängebrücke ist in einem schrecklichen Zustand, aber es ist der einzige Weg auf die andere Seite.`n");
    output(" Der Wächter der Brücke ruft, `3 Stop!  Wer über die Brücke des Todes will gehen, der muß 3 mal Rede und Antwort stehen.`2`n");
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
    addnav("Die Suche nach dem grünen Drachen","forest.php?op=grail");
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
        addnav("Das weiß ich nicht","forest.php?op=know");
        output("WELCHES...ist die Hauptstadt von Assyrien?`n");
        break;
        case 5:
        case 6:
        case 7:
        case 8:
        addnav("Blau","forest.php?op=blue");
	addnav("Grün","forest.php?op=blue");
	addnav("Rot","forest.php?op=blue");
        output("WELCHES...ist Deine Lieblingsfarbe?`n");
        break;
        case 9:
        case 10:
        addnav("Afrikanische oder Europäische?","forest.php?op=swallow");
        output("WELCHES...ist die Geschwindigkeit einer unbeladenen Taube?`n");
        break;
        }
    }

elseif ($HTTP_GET_VARS[op]=="leave"){
    $session[user][specialinc]="";
    output("`#Eingeschüchtert kommst du in den Wald zurück");
    }
else if ($HTTP_GET_VARS[op]=="blue")
{
    $session[user][specialinc] = "bridge.php";
    if (e_rand(0,1)==0)    {
        output("`2Du änderst schnell Deine Meinung, und noch bevor du `^Gelb`2 sagen kannst... `n");
        output("wirst du in die Luft katapultiert. `4AAAIIIIIIIIHHHHHHHHHHHHHHHH.!`2`n");
	output("Doch du hast Glück und landest nur wenige Zentimeter vom Abgrund entfernt`n");
        output("Allerdings schlägst du hart auf und verlierst fast alle Lebenspunkte!");
        addnav("Zurück in den Wald","forest.php?op=leave");
        $session[user][hitpoints]= 3;
        }
    else{
        $session[user][specialinc]="";
    	addnav("Weiter","forest.php");
        output("Richtig. Du kannst passieren.`n");
        //output("You gain one charm point!`n");
        //$session[user][charm]++;
        //if (e_rand(0,1)==0){
        output("Du überquerst die Brücke. In der Mitte der Brücke findet du `3einen Edelstein`2");
        $session[user][gems]++;
	//}
	}
    }
else if ($HTTP_GET_VARS[op]=="know"){
    output("`4AAAAIIIIIIIIIHHHHHHHHHHHHHHH!`2, `n");
    output("du wirst in hohem Bogen in die Luft katapultiert......`n");
    output("Und stürzt in den Abgrund!`n`n");
    output("Natürlich bist du jetzt....TOT`n");
    $session[user][alive]=false;
    $session[user][hitpoints]=0;
    $session[user][specialinc]="";
    addnav("Zu den News","news.php");
    $session[user][specialinc]="";
    }
else if ($HTTP_GET_VARS[op]=="swallow"){
    output("`4AAAAAAAIIIIIIIIIIIHHHHHHHHHHHHH!, `2`n");
    output("Der Wächter der Brücke wird in die Luft katapultiert und fliegt in hohen Bogen den Abgrund hinab!`n");
    output("`3\"Woher weißt du soviel über Schwalben?\"`2, ruft er im hinabstürtzen`n");
    output("Für dein großes Wissen erhältst du `500 Erfahrungspunkte`2!`n");
    $session[user][experience]+=500;
    $session[user][specialinc]="";
    addnav("Über die Brücke","forest.php");
    }
?>