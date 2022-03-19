/*
 * Drag&Drop Javascript
 * by Alucard
 * See http://www.atrahor.de for details.
 */

/*
	Objekt für Drag
*/
function DragObject(){
	this.obj 	= 0;
	this.drop 	= -1;
	this.value1 = 0;
	this.value2 = 0;
}

/*
	Objekt für Drop
*/
function DropObject(){
	this.obj 	= 0;	//objekt
	this.id		= 0;	//<muh id='x'>
	this.drag  	= -1;	//id des Drag objekts, das drauf liegt
	this.width 	= 0;	//breite
	this.height = 0;	//höhe
	this.offX	= 0;	//drag wird left+offX positioniert
	this.offY 	= 0;	//drag wird top+offY positioniert
	this.onDrop	= 0;	//funktion, die beim ablegen eines elements auf dieses objekt aufgerufen wird (param: dropobjekt, dragobjekt)
	this.onLeave = 0; 	//funktion, die beim ablegen des elements, das auf diesem objekt lag, auf ein anderes objekt aufgerufen wird
						//(param: dropobjekt)
}



var g_isdrag  			= false;		//draggin aktiv?
var g_dragobj 			= new Array();	//dragobjekte
var g_dropobj 			= new Array();	//dropobjekte
var g_dragID  			= -1;			//ID des dragelements
var g_dropclassname 	= 'dropme';		//klasse der dropobjekte
var g_dragclassname 	= 'dragme';		//klasse der dragobjekte
var g_dragedclassname 	= 'draged';		//klasse des objekts, das atm gedragt is
var g_backtoX 			= 0;
var g_backtoY 			= 0;
var g_diffX				= 0;
var g_diffY				= 0;
var ie=document.all;
var nn6=document.getElementById&&!document.all;
var topelement = nn6 ? "HTML" : "BODY";


function setDropClassName( str )
{
	g_dropclassname = str;	
}

function setDragClassName( str )
{
	g_dragclassname = str;	
}

function setDragedClassName( str )
{
	g_dragedclassname = str;	
}


function isDropObj( obj )
{
	var i;
	for( i=0; i<g_dropobj.length; ++i ){
		if( g_dropobj[ i ].obj == obj ){
			return i;
		}
	}
	return -1;
}

function isDragObj( obj )
{
	var i;
	
	for( i=0; i<g_dragobj.length; ++i ){
		if( g_dragobj[ i ].obj == obj ){
			return i;
		}
	}
	return -1;
}

function registerDragObj(obj,value,value2)
{
	var i = g_dragobj.length;
	g_dragobj[ i ] 		  = new DragObject();
	g_dragobj[ i ].obj 	  = obj;
	g_dragobj[ i ].value1 = value;
	g_dragobj[ i ].value2 = value2; 
	
	obj.className = g_dragclassname;	
}



function getXpos(element) {
	return (element.offsetParent) ? element.offsetLeft + getXpos(element.offsetParent) : element.offsetLeft;
}
    
function getYpos(element) {
	return (element.offsetParent) ? element.offsetTop + getYpos(element.offsetParent) : element.offsetTop;
}

function registerDropObjByID(id,width,height,offX,offY,onDrop,onLeave)
{
	var drop = document.getElementById(id);	
	var i = g_dropobj.length;
	
	if( drop ){
		
		g_dropobj[ i ] 			= new DropObject();
		g_dropobj[ i ].obj 		= drop; //objekt
		g_dropobj[ i ].id 		= id;
		g_dropobj[ i ].drag 	= -1;   //kein dragobj
		g_dropobj[ i ].width 	= width;
		g_dropobj[ i ].height 	= height
		g_dropobj[ i ].offX 	= offX
		g_dropobj[ i ].offY 	= offY;
		g_dropobj[ i ].onDrop 	= onDrop;
		g_dropobj[ i ].onLeave 	= onLeave;
		
		drop.className = g_dropclassname;	
		return true;
	}
	return false;
}

function registerDropObjsByID(prfx,width,height,offX,offY,onDrop,onLeave)
{
	var i=0;
	var drop=0;

	while( registerDropObjByID( prfx+i, width, height, offX, offY, onDrop, onLeave ) ){
		++i;
	}
}

function registerDragObjByID(id, val, val2)
{
	var drag = document.getElementById(id);	
	
	if( drag ){
		registerDragObj(drag, val, val2);	
	}	
}

function registerDragObjsByID(prfx)
{
	var i=0;
	var drag=0;
	
	while( (drag=document.getElementById(prfx+i)) ){
		registerDragObj( drag );
		++i;
	}
}

function setDragToDrop( drg, drp )
{
	if( g_dropobj.length > drp && 
	    g_dragobj.length > drg){
		
		g_dragobj[ drg ].obj.style.left = (getXpos( g_dropobj[ drp ].obj )+2)+'px';
		g_dragobj[ drg ].obj.style.top  = (getYpos( g_dropobj[ drp ].obj )+2)+'px';

		g_dropobj[ drp ].drag = drg;
		if( g_dragobj[ drg ].drop != -1 ){
			var d = g_dropobj[ g_dragobj[ drg ].drop ];
			d.drag = -1;
			if( d.onLeave ){
				d.onLeave( d );
			}
		}
		g_dragobj[ drg ].drop = drp;
		g_dropobj[ drp ].drag = drg;
		if( g_dropobj[ drp ].onDrop ){
			g_dropobj[ drp ].onDrop( g_dropobj[ drp ], g_dragobj[ drg ]);
		}
	}
}

function setDropOnDrop( seg, fkt, off )
{
	var i;
	for(i=0;i<=off;++i){
		g_dropobj[ seg + i ].onDrop = fkt;	
	}
}

function setDropOnLeave( seg, fkt, off )
{
	var i;
	for(i=0;i<=off;++i){
		g_dropobj[ seg + i ].onLeave = fkt;	
	}
}


function mouseMoveDrag( e )
{
	if( g_isdrag ){
		var x = nn6 ? e.clientX : event.clientX;
		var y = nn6 ? e.clientY : event.clientY;
		
		g_dragobj[ g_dragID ].obj.style.left = (x-g_diffX)+'px';
		g_dragobj[ g_dragID ].obj.style.top  = (y-g_diffY)+'px';
		
		//für debug
		//window.status = 'x: '+x+'     y: '+y+'****'+g_dragobj[ g_dragID ][ 0 ].innerHTML+'----------'+g_dragobj[ g_dragID ][ 0 ].className;
		
		return false;	
	}
}

function mouseDrag( e )
{
    var fobj = nn6 ? e.target : event.srcElement;
	var x 	 = nn6 ? e.clientX : event.clientX;
	var y 	 = nn6 ? e.clientY : event.clientY;
	var id   = -1;
	
    while (fobj.tagName != topelement && (id=isDragObj(fobj)) == -1 ){
		fobj = nn6 ? fobj.parentNode : fobj.parentElement;
    }

	if ( id != -1 ){
		g_isdrag 	= true;
		g_dragID 	= id;
		
		g_backtoX = getXpos( g_dragobj[ g_dragID ].obj );
		g_backtoY = getYpos( g_dragobj[ g_dragID ].obj );
		g_diffX	  = x - g_backtoX;
		g_diffY	  = y - g_backtoY;
		g_dragobj[ g_dragID ].obj.className  = g_dragedclassname;
		
		document.onmousemove = mouseMoveDrag;
		return false;
	}
}




function mouseDrop( e )
{
	if( !g_isdrag ){
		return;
	}
	
	g_isdrag = false;
	
	var obj = nn6 ? e.target : event.srcElement;

	var i=0;
	var lx = 0;
	var ly = 0;
	

	var scrobj = document.getElementsByTagName(topelement)[0];
	ly = scrobj.scrollTop;
	lx = scrobj.scrollLeft;

	
	lx += (nn6 ? e.clientX : event.clientX);
	ly += (nn6 ? e.clientY : event.clientY);
	
	//document.getElementById('main_content').parentNode.scrollTop);
	var ol, or, ot, ob;
	
	var i;
	g_dragobj[ g_dragID ].obj.className  = g_dragclassname;
	for( i=0; i<g_dropobj.length; ++i ){
		ol = getXpos( g_dropobj[ i ].obj );
		or = ol+g_dropobj[ i ].width;
		ot = getYpos( g_dropobj[ i ].obj );
		ob = ot+g_dropobj[ i ].height;
		/*window.status = 'ol: '+ol+
						'<'+lx+
						' or: '+or+
						'>'+lx+
						' ot: '+ot+
						'<'+ly+
						' ob: '+ob+
						'>'+ly;*/
		//alert(ol+'___'+or+'___'+ot+'___'+ob);
		if( lx >= ol &&
			lx <= or &&
			ly >= ot &&
			ly <= ob &&
			g_dropobj[ i ].drag == -1){
			
			setDragToDrop( g_dragID, i );
			return;
		}
	}

	g_dragobj[ g_dragID ].obj.style.left = g_backtoX+'px';
	g_dragobj[ g_dragID ].obj.style.top  = g_backtoY+'px';
	
}

function initializeDragDrop()
{
	document.onmousedown = mouseDrag;
	document.onmouseup   = mouseDrop;	
}
