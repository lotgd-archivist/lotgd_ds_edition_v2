var g_log;
var g_errorfields;
var g_starttime = new Date();


function tolog(str)
{
	g_log.value += str+'\n';
}

function clearlog()
{
	g_log.value = '';
}

function adderror( pos )
{
	g_errorfields[ g_errorfields.length ] = pos;
}

function sesswarn()
{
	var x = new Date();
	var diff = (x.getTime() - g_starttime.getTime())/1000;
	diff /= 60;
	//var diff = 0;
	
	 
	if( diff > 17){
		window.document.getElementById('startsestime').innerHTML = '<font color="#FF0000"><b>&gt;' + Math.round(diff) + 'min</b></font>';
		alert("Achtung!\nBitte zwischenspeichern!");
	}
	else{
		window.document.getElementById('startsestime').innerHTML = '<b>' + Math.round(diff) + 'min</b>';
		window.setTimeout('sesswarn()', 1000);
	}	
}


function debugmap( mapcode )
{
	//init
	g_errorfields = new Array();
	
	window.document.getElementById('dbgwnd').style.visibility = 'visible';
	
	g_log  = window.document.getElementById('debuglog');
	clearlog();
	tolog('start debuging...');
	
	var aMap = mapcode.split(","); //join(",")
	
	tolog('initaliazed...');
	//searching  leaks
	var i;
	var c;
	var err, fexit = 0;
	var str;
	tolog('scanning for leaks...');
	for(i=0;i<aMap.length; ++i){
		o= window.document.getElementById('mazefield'+i);
		o.className = 'mazefieldok';
		
		c = aMap[ i ];
		err = 0;
		
		if( c == 'z'){
			fexit = 1;
			continue;
		}
		if( i == 5 ){
			str = "acefghi";
			if( str.indexOf(c) == -1 ){
				tolog('no valid enter field @ 5' + i + ' can\'t be equal to: ' + c );
				err++;
			}
		}
		if( c == 'x' ){
			tolog('no field set @ ' + i);	
			err++;
		}
		//tolog( i );
		if( i != 5 && i<11){ //
			str = "acefghim";
			if( str.indexOf(c) != -1 ){
				tolog('leak detected @ ' + i + ' value can\'t be equal to: ' + c );	
				err++;
			}
		}
		
		if(!(i%11)){
			str = "abcdehkn";
			if( str.indexOf(c) != -1 ){
				tolog('leak detected @ ' + i + ' value can\'t be equal to: ' + c );	
				err++;
			}
		}
		
		if(!((i+1)%11)){
			str = "abcdfijo";
			if( str.indexOf(c) != -1 ){
				tolog('leak detected @ ' + i + ' value can\'t be equal to: ' + c );	
				err++;
			}
		}
		
		if(i>131){ //
			str = "abefgjkl";
			if( str.indexOf(c) != -1 ){
				tolog('leak detected @ ' + i + ' value can\'t be equal to: ' + c );	
				err++;
			}
		}
		
		if( err ){
			adderror( i );
		}
		
	}
	if( !fexit ){
		tolog('NO EIXT FOUND!');
	}
	tolog('...done!');
	
	
	tolog('backtrace...');
	tolog('not impl.!');
	tolog('...done!');
	
	
	tolog('errors found @ ' + g_errorfields.length + ' fields');
}

function closedebug()
{
	window.document.getElementById('dbgwnd').style.visibility = 'hidden';
	var i, o;
	for(i=0;i<g_errorfields.length;++i){
		o= window.document.getElementById('mazefield'+g_errorfields[ i ]);
		o.className = 'mazefielderr';
		//o.style.borderWidth = '1px';
		//o.style.borderStyle = 'solid';
	}
}


function switchgrid(obj)
{
	var i, o, v;
	if( obj.value == 'off'){
		v = '0px';	
		obj.value = 'on';
	}
	else{
		v = '1px';
		obj.value = 'off';
	}
	
	for(i=0;i<143;++i){
		o= window.document.getElementById('mazefield'+ i );
		o.style.borderWidth = v;
	}
}



g_currObj = 0;
g_currLetter = 'i';

function Init()
{
	var str = window.document.mazevalues.maze.value;
	if( str == '' ){
	
		window.document.mazevalues.maze.value = 	
											'x,x,x,x,x,x,x,x,x,x,x,' +
											'x,x,x,x,x,x,x,x,x,x,x,' +
											'x,x,x,x,x,x,x,x,x,x,x,' +
											'x,x,x,x,x,x,x,x,x,x,x,' +
											'x,x,x,x,x,x,x,x,x,x,x,' +
											'x,x,x,x,x,x,x,x,x,x,x,' +
											'x,x,x,x,x,x,x,x,x,x,x,' +
											'x,x,x,x,x,x,x,x,x,x,x,' +
											'x,x,x,x,x,x,x,x,x,x,x,' +
											'x,x,x,x,x,x,x,x,x,x,x,' +
											'x,x,x,x,x,x,x,x,x,x,x,' +
											'x,x,x,x,x,x,x,x,x,x,x,' +
											'x,x,x,x,x,x,x,x,x,x,x';
	}
	else{
		var i = 0;
		for(i=0;i<143;i++){
			window.document.getElementById('mazefield'+i).innerHTML = '<img style="display: block;" src="images/' + str.substr(i*2,1) + 'maze.gif" width="30" height="30">';
		}
	}
}

function setCurrent(obj, letter)
{
	obj.className 		= 'currenttile';
	if( !g_currObj ){
		g_currObj = window.document.getElementById('inittile');			
	}
	g_currObj.className = 'notile';
	g_currObj 			= obj;
	g_currLetter		= letter;
}

function setField(obj, pos)
{
	obj.innerHTML = '<img style="display: block;" src="images/' + g_currLetter + 'maze.gif" width="30" height="30">';
	var str = window.document.mazevalues.maze.value;
	str = str.substring( 0, pos*2) + g_currLetter + str.substr(pos*2+1, str.length);
	window.document.mazevalues.maze.value = str;
}
