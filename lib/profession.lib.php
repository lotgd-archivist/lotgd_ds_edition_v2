<?php
define('PROF_PRIEST',11);
define('PROF_PRIEST_HEAD',12);
define('PROF_PRIEST_NEW',13);

define('PROF_GUARD',1);
define('PROF_GUARD_HEAD',2);
define('PROF_GUARD_ENT',3);
define('PROF_GUARD_NEW',5);

define('PROF_JUDGE',21);
define('PROF_JUDGE_HEAD',22);
define('PROF_JUDGE_ENT',23);
define('PROF_JUDGE_NEW',25);

define('PROF_TEMPLE_SERVANT_NEW',31);
define('PROF_TEMPLE_SERVANT',32);

define('PROF_DDL_RECRUIT',41);
define('PROF_DDL_CORPORAL',42);
define('PROF_DDL_SERGEANT',43);
define('PROF_DDL_STSERGEANT',44);
define('PROF_DDL_ENSIGN',45);
define('PROF_DDL_LIEUTENANT',46);
define('PROF_DDL_CAPTAIN',47);
define('PROF_DDL_MAJOR',48);
define('PROF_DDL_COLONEL',49);

define('PROF_WITCH',61);
define('PROF_WITCH_HEAD',62);
define('PROF_WITCH_NEW',63);

// Erkl�rung: Schl�ssel = Konstante = Wert der Profession-Var
//				Array-Feld0: M�nnl. Titel
//				Array-Feld1: Weibl. Titel
//				Array-Feld2: Wird �ffentlich angezeigt, true oder false
//				Array-Feld3: Farbe

$profs = array (
				PROF_PRIEST => array('Priester','Priesterin',true,'`7'),
				PROF_PRIEST_NEW => array('Novize','Novizin',false,'`7'),
				PROF_PRIEST_HEAD => array('Hohepriester','Hohepriesterin',true,'`7'),
				PROF_GUARD => array('Stadtwache','Stadtwache',true,'`4'),
				PROF_GUARD_HEAD => array('Hauptmann der Stadtwache','Hauptmann der Stadtwache',true,'`4'),
				PROF_GUARD_ENT => array('Stadtwache (in Entlassung)','Stadtwache (in Entlassung)',true,'`4'),
				PROF_GUARD_ENT => array('Stadtwache (in Bewerbung)','Stadtwache (in Bewerbung)',false,'`4'),
				PROF_JUDGE => array('Richter','Richterin',true,'`4'),
				PROF_JUDGE_HEAD => array('Oberster Richter','Oberste Richterin',true,'`4'),
				PROF_JUDGE_ENT => array('Richter (in Entlassung)','Richterin (in Entlassung)',true,'`4'),
				PROF_JUDGE_NEW => array('Richter (in Bewerbung)','Richterin (in Bewerbung)',false,'`4'),
				PROF_TEMPLE_SERVANT_NEW => array('Tempeldiener (in Bewerbung)','Tempeldienerin (in Bewerbung)',false,'`8'),
				PROF_TEMPLE_SERVANT => array('Tempeldiener','Tempeldienerin',true,'`8'),
				
				PROF_WITCH => array('Hexer','Hexe',true,'`7'),
				PROF_WITCH_NEW => array('Hexensch�ler','Hexensch�lerin',false,'`7'),
				PROF_WITCH_HEAD => array('Hohepriester der Hexen','Hohepriesterin der Hexen',true,'`7'),
				
				PROF_DDL_RECRUIT => array('Rekrut in der B�rgerwehr','Rekrutin in der B�rgerwehr',true,'`2'),
				PROF_DDL_CORPORAL => array('Corporal in der B�rgerwehr','Corporal in der B�rgerwehr',true,'`2'),
				PROF_DDL_SERGEANT => array('Sergeant in der B�rgerwehr','Sergeant in der B�rgerwehr',true,'`2'),
				PROF_DDL_STSERGEANT => array('Feldwebel in der B�rgerwehr','Feldwebel in der B�rgerwehr',true,'`2'),
				PROF_DDL_ENSIGN => array('F�hnrich in der B�rgerwehr','F�hnrich in der B�rgerwehr',true,'`2'),
				PROF_DDL_LIEUTENANT => array('Leutnant in der B�rgerwehr','Leutnant in der B�rgerwehr',true,'`2'),
				PROF_DDL_CAPTAIN => array('Hauptmann in der B�rgerwehr','Hauptmann in der B�rgerwehr',true,'`2'),
				PROF_DDL_MAJOR => array('Major in der B�rgerwehr','Major in der B�rgerwehr',true,'`2'),
				PROF_DDL_COLONEL => array('Oberst in der B�rgerwehr','Oberst in der B�rgerwehr',true,'`2')
	     		);
	     		
function getprofession ($profession) {
  switch ($profession)
      {
        case 41 :
        $rank='Rekrut';
        break;
        case 42 :
        $rank='Corporal';
        break;
        case 43 :
        $rank='Sergeant';
        break;
        case 44 :
        $rank='Feldwebel';
        break;
        case 45 :
        $rank='F�hnrich';
        break;
        case 46 :
        $rank='Leutnant';
        break;
        case 47 :
        $rank='Hauptmann';
        break;
        case 48 :
        $rank='Major';
        break;
        case 49 :
        $rank='Oberst';
        break;
        default :
        $rank='Zivilist';
        break;
      }
return ($rank);
}
	

?>
