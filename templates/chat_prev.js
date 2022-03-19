// by talion
						
function com_prev (verb) {
	
	var str = document.getElementById('comin').value;	
	var i = 0;
	var sp = '';
	var out = '';
	
	if(str.substr(0,4) == '/msg') {
		out = "<span class='c"+String('7').charCodeAt(0)+"'><b>";
		str = str.substring(4,str.length);
		str += ' </b>';
	}
	else {
		
		ecol = ecol!='' ? ecol : '&';
		
		var wh = "<span class='c"+ecol.charCodeAt(0)+"'>";
		
		if(str.substr(0,3) == '/me') {
			out = wh;
			str = str.substring(3,str.length);
		}
		else if(str.substr(0,2) == '::') {
			out = wh;
			str = str.substring(2,str.length);
		}
		else if(str.substr(0,1) == ':') {		
			out = wh;
			str = str.substring(1,str.length);			
		}
		else {
			var sh = "<span class='c"+tcol.charCodeAt(0)+"'>";
			out = "<span class='c"+String('3').charCodeAt(0)+"'> "+verb+': '+sh+'"';
			str += '"</span>';
		}
		str = str;
		out = name+out;		
		
	}
				
	out = out + str;
	
	out = out.replace(/%x(\d)/g,'<i>shortcut$1</i>');
	out = out.replace(/[`][`]/g,'');
	out = out.replace(/[`][0]/g,'</span>');
	
	while( out.search(reg) != -1 ) {
		i++;
		
		if(i > 5) {
			out += sp;
			break;
		}
		
		out = out.replace(reg,sp+'<span class="c'+RegExp.$1.charCodeAt(0)+'">');
		sp = '</span>';
	
	}
										
	document.getElementById('comprev').innerHTML = out; 	

}


function input_prev (field) {
	
	var i = 0;
	var sp = '';
	var out = field.value;	
	var reg = /[`]([123456789\!@#\$%\^&\)QqrRVvgGTtwfdesapklmx])/;
	
	out = out.replace(/%x(\d)/g,'<i>shortcut$1</i>');
	out = out.replace(/[`][`]/g,'');
	out = out.replace(/[`][0]/g,'</span>');
	
	while( out.search(reg) != -1 ) {
		i++;
		
		if(i > 5) {
			
			out += sp;
			break;
		}
		
		out = out.replace(reg,sp+'<span class="c'+RegExp.$1.charCodeAt(0)+'">');
		sp = '</span>';
	
	}
										
	document.getElementById(field.name + '_prev').innerHTML = out; 	

}
