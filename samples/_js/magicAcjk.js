// Part of this script (makeAnimatedGifFromPngs function) is an adaptation
// of Animated_GIF project sample called "basic".
// See https://github.com/sole/Animated_GIF/blob/master/tests/basic.js
// Other parts were written from scratch.

if (typeof debug=='undefined') debug=0;

function computeOne(a,k,km)
{
	if (km==1) return a>>1;
	return Math.floor((k-1)*a/Math.max(km-1,1));
}
function colorize(k,km)
{
	// compute stroke color
	var r=255,g=0,b=0;
	return "rgb("+computeOne(r,k,km)+","+computeOne(g,k,km)+","+computeOne(b,k,km)+")";
}
function generatePngFromSvg(paths,background,mmah,size)
{
	// paths is an array that contains a list of stroke d and fill attributes
	// background will be the background of the image
	// mmah indicates if the data come from MakeMeAHanzi instead of AnimCJK
	// return a base64 encoded PNG image
	var cn,cx,k,km,m,r,x0,y0,x1,y1,x2,y2,x3,y3;
	// create a ghost canvas
	cn=document.createElement('canvas');
	cn.width=size;
	cn.height=size;
	cx=cn.getContext("2d");
	if (size!=1024) cx.scale(size/1024,size/1024);
	cx.fillStyle=background;
	cx.fillRect(0,0,1024,1024);
	// draw strokes in the canvas 
	km=paths.length;
	r=/ ?([MLQC]) ?([0-9-]+) ([0-9-]+) ?([0-9-]+)? ?([0-9-]+)? ?([0-9-]+)? ?([0-9-]+)?/g;
	for(k=0;k<km;k++)
	{
		cx.beginPath();
		x0=0;
		y0=0;
		while (m=r.exec(paths[k].d))
		{
			x1=parseInt(m[2]);
			y1=parseInt(m[3]);
			if (mmah) y1=-y1+900;
			if (!x0) x0=x1;
			if (!y0) y0=y1;
			if (m[1]=="M") cx.moveTo(x1,y1);
			else if (m[1]=="L") cx.lineTo(x1,y1);
			else
			{
				x2=parseInt(m[4]);
				y2=parseInt(m[5]);
				if (mmah) y2=-y2+900;
				if (m[1]=="Q") cx.quadraticCurveTo(x1,y1,x2,y2);
				else
				{
					x3=parseInt(m[6]);
					y3=parseInt(m[7]);
					if (mmah) y3=-y3+900;
					cx.bezierCurveTo(x1,y1,x2,y2,x3,y3);
				}
			}
		}
		cx.lineTo(x0,y0); // sometimes not necessary, but it doesn't matter
		x0=0;
		y0=0;
		cx.fillStyle=paths[k].fill;
		cx.fill();
	}
	// generate a base64 encoded PNG from the canvas then return it
	return cn.toDataURL("image/png");
}
function generateRedPngFromSvg(s,size,background)
{
	// generate a "red" PNG image from a svg
	// s is a text representing a character in svg format
	// size will be the size of the PNG
	// background will be the background of the PNG (including transparent)
	var k,km,reg,m,paths=[],mmah;
	// if mmah is true, assume the svg comes from MakeMeAHanzi
	// else assume the svg comes from animCJK
	mmah=!s.match(/class="acjk"/);
	// extract "d" attribute values
	if (mmah) reg=/<path d=\"([^\"]*)\" fill=\"lightgray\"/g;
	else reg=/<path id=\"[^\"]*\" d=\"([^\"]*)\"/g;
	while (m=reg.exec(s)) paths.push({d:m[1]});
	km=paths.length;
	// colorize "d"
	for (k=0;k<km;k++) paths[k].fill=colorize(k+1,km);
	// return base64 PNG image
	return generatePngFromSvg(paths,background,mmah,size);
}
function makeAnimatedGifFromPngs(ghost,delay,background,dec,show,save) {
    var imgs = ghost.getElementsByTagName('img');
    var firstImage = imgs[0];
    var imageWidth = firstImage.getAttribute("width");
    var imageHeight = firstImage.getAttribute("height");
    var tasks = [];
    function buildImageCallback(img) {
        return function(gif) {
            img.src = gif;
        };
    }

    function getBuildGIFTask(img) {
        return function(doneCallback) {
            var ag = new Animated_GIF({
                repeat: null, // Don't repeat
            });
            ag.setSize(img.getAttribute("width"), img.getAttribute("height"));
            ag.addFrame(img);
            var img2 = document.createElement('img');
            if(img.nextSibling) {
                img.parentNode.insertBefore(img2, img.nextSibling);
            } else {
                img.parentNode.appendChild(img2);
            }
            ag.getBase64GIF(function(gif) {
                var originalSrc = img.src;
                img.addEventListener('mouseenter', function() {
                    img.src = gif;
                }, false);
                img.addEventListener('mouseleave', function() {
                    img.src = originalSrc;
                }, false);
                doneCallback();
            });
        };
    }

    function runTasks(tasks) {
        var nextTaskIndex = 0;
        runNextTask();
        function runNextTask() {
            if(nextTaskIndex < tasks.length) {
                // console.log('running task', nextTaskIndex);
                var task = tasks[nextTaskIndex];
                task(function() {
                    nextTaskIndex++;
                    setTimeout(runNextTask, 100);
                });
            }
        }
    }

    tasks.push(function(doneCallback) {
        var agAll = new Animated_GIF({
            repeat: 0, // repeat 0 = Repeat forever
        });
        agAll.setSize(imageWidth, imageHeight);
        agAll.setDelay(delay);
        
        for(var i = 0; i < imgs.length; i++) {
            var img = imgs[i];
            agAll.addFrame(img);
        }
        var imgAll = document.createElement('img');
        var lastRenderProgress = Date.now();
		imgAll.style.display="block";
		imgAll.style.border="0";
        agAll.onRenderProgress(function(progress) {
            var t = Date.now();
            var elapsed = t - lastRenderProgress;
            lastRenderProgress = t;
        });
        agAll.getBase64GIF(function(image) {
            imgAll.src = image;
            // in case of automatisation, it's the right place to show or save the image
            if (show) show(image, background, dec);
            if (save) save(image, background, dec);
            doneCallback();
        });
    });

    for(var i = 0; i < imgs.length; i++) {
        tasks.push(getBuildGIFTask(imgs[i]));
    }

    runTasks(tasks);
}
function generateAnimatedGifFromSvg(s,size,background,delay,dec,show,save)
{
	// generate an animated GIF image from a svg
	// s is a text representing a character in svg format
	// size will be the size of the PNG
	// background will be the background of the image (excepting transparent)
	// if background is transparent, make grey strokes transparent and background white
	// delay is the delay between two frames
	// dec is the decimal unicode of the character
	// show is a function called to show the image somewhere at the end of the process
	// show has 2 parameters:
	//  image which is a base64 representation of the image
	//  background which can be "transparent" or any css color
	// save is a function called to save the image somewhere at the end of the process
	// save has 3 parameters:
	//  image which is a base64 representation of the image
	//  background which can be "transparent" or any css color
	//  dec which is the decimal unicode of the character
	var k1,k2,km,img,imgsSrc=[],ghost,mmah,m,paths,reg;
	// if mmah is true, assume the svg comes from MakeMeAHanzi
	// else assume the svg comes from animCJK
	mmah=!s.match(/class="acjk/);
	// create a ghost div that will contains ephemeral PNG images
	ghost=document.createElement("div");
	// extract "d" attributes
	if (mmah) reg=/<path d=\"([^\"]*)\" fill=\"lightgray\"/g;
	else reg=/<path id=\"[^\"]*\" d=\"([^\"]*)\"/g;
	paths=[];
	while (m=reg.exec(s)) paths.push({d:m[1]});
	paths.reverse(); // draw last first in case of overlapping
	km=paths.length;
	if (km)
	{
		for (k1=km;k1>=0;k1--) // generate km+1 images, the first is special
		{
			for (k2=0;k2<km;k2++)
			{
				if (k2<k1)
				{
					// first trick to manage animated image with transparent background
					// if background parameter is "transparent", draw transparent strokes
					// else draw grey strokes
					if (background=="transparent") paths[k2].fill="transparent";
					else paths[k2].fill="#ccc";
				}
				else paths[k2].fill="#000";
			}
			// second trick to manage animated image with transparent background
			// if background parameter is "transparent", draw white background
			// else draw background as is
			// size is always 1024 here even if the generated image has a different size
			if (background=="transparent")
				imgsSrc[k1]=generatePngFromSvg(paths,"#fff",mmah,1024);
			else imgsSrc[k1]=generatePngFromSvg(paths,background,mmah,1024);
		}
		for (k1=km;k1>=0;k1--)
		{
			img=document.createElement('img');
			img.id="img"+k1;
			img.style.display="block";
			img.style.border="0";
			img.width=size;
			img.height=size;
			img.acjk={};
			img.acjk.km=km;
			img.onload=function(){
				var k,km,allDone=true;
				this.done=1;
				km=this.acjk.km;
				for(k=0;k<=km;k++)
					if (!ghost.querySelector("#img"+k).done) {allDone=false;break;}
				if (allDone) // ready to generate GIF image
					makeAnimatedGifFromPngs(ghost,delay,background,dec,show,save);
			};
			img.src=imgsSrc[k1];
			ghost.appendChild(img);
		}
	}
}
function distance(p1,p2)
{
	var x1=p1.x,x2=p2.x,y1=p1.y,y2=p2.y;
	return Math.sqrt((x2-x1)*(x2-x1)+(y2-y1)*(y2-y1));
}
function arrow(cx,x1,y1,x2,y2,color,lw)
{
	// draw an arrow at the end of the segment defined by (x1,y1) and (x2,y2)
	var angle;
	angle=Math.atan2(y2-y1,x2-x1);
	cx.beginPath();
	cx.moveTo(x2,y2);
	cx.lineTo(x2-lw*Math.cos(angle-Math.PI/7),y2-lw*Math.sin(angle-Math.PI/7));
	cx.lineTo(x2-lw*Math.cos(angle+Math.PI/7),y2-lw*Math.sin(angle+Math.PI/7));
	cx.lineTo(x2,y2);
	cx.lineTo(x2-lw*Math.cos(angle-Math.PI/7),y2-lw*Math.sin(angle-Math.PI/7));
	cx.strokeStyle=color;
	cx.lineWidth=Math.round(lw*3/2);
	cx.stroke();
	cx.fillStyle=color;
	cx.fill();
}
function noSmooth(cx,points,color,lw)
{
	var k,km,xc,yc;
	km=points.length;
	cx.beginPath();
	cx.moveTo(points[0].x,points[0].y);
	for (k=1;k<km;k++)
	{
		cx.lineTo(points[k].x,points[k].y);
	}
	cx.strokeStyle=color;
	cx.lineWidth=lw;
	cx.stroke();
}
function smooth(cx,points,color,lw)
{
	// smooth the line defined by points
	var k,km,xc,yc,q;
	km=points.length;
	if (km>4) q=2;
	else if (km>3) q=4;
	else q=8;
	cx.beginPath();
	cx.moveTo(points[0].x,points[0].y);
	for (k=1;k<km-1;k++)
	{
		xc=points[k].x+(points[k+1].x-points[k].x)/q;
		yc=points[k].y+(points[k+1].y-points[k].y)/q;
		cx.quadraticCurveTo(points[k].x,points[k].y,xc,yc);
	}
	if (km>2)
	{
		xc=points[km-1].x-(points[km-1].x-points[km-2].x)/q;
		yc=points[km-1].y-(points[km-1].y-points[km-2].y)/q;
		cx.quadraticCurveTo(xc,yc,points[km-1].x,points[km-1].y);
	}
	else cx.lineTo(points[1].x,points[1].y);
	cx.strokeStyle=color;
	cx.lineWidth=lw;
	cx.stroke();
}
function reducePointsNum(points)
{
	// replace closed points of the begin and the end of points by their middle
	var x,y,len,t=42,p0,pm;
	len=points.length;
	if (len<3) return points;
	p0={x:points[0].x,y:points[0].y};
	pm={x:points[len-1].x,y:points[len-1].y};
	while ((len>2)
		&&(distance(p0,points[1])<t)
		&&(distance(points[0],pm)>2*t))
	{
		x=Math.round((points[0].x+points[1].x)/2);
		y=Math.round((points[0].y+points[1].y)/2);
		points[1].x=x;
		points[1].y=y;
		points.shift();
		len--;
	}
	if (len<3) return points;
	while ((len>2)
		&&(distance(pm,points[len-2])<t)
		&&(distance(p0,points[len-1])>2*t))
	{
		x=Math.round((points[len-2].x+points[len-1].x)/2);
		y=Math.round((points[len-2].y+points[len-1].y)/2);
		points[len-2].x=x;
		points[len-2].y=y;
		points.pop();
		len--;
	}
	return points;
}
function minMax(points)
{
	// return the square enclosing all points
	var x0,y0,xMin,yMin,xMax,yMax,k,km;
	km=points.length;
	x0=points[0].x;
	y0=points[0].y;
	xMax=x0;
	yMax=y0;
	xMin=x0;
	yMin=y0;
	for (k=1;k<km;k++)
	{
		xMax=Math.max(xMax,points[k].x);
		yMax=Math.max(yMax,points[k].y);
	}
	for (k=1;k<km;k++)
	{
		xMin=Math.min(xMin,points[k].x);
		yMin=Math.min(yMin,points[k].y);
	}
	return {xMin:xMin,yMin:yMin,xMax:xMax,yMax:yMax};
}
function reducePointsSize(points,rx,ry,rxt,ryt,delta,mM)
{
	// shrink (or stretch) the shape defined by points
	var k,km;
	km=points.length;
	dx=mM.xMax-mM.xMin;
	dy=mM.yMax-mM.yMin;
	rx=Math.min(rx,dx/2);
	ry=Math.min(ry,dy/2);
	for (k=1;k<km;k++)
	{
		if (rx)
		{
			if ((rxt=="R")&&(points[k].x>points[0].x))
				points[k].x=points[k].x-rx*(points[k].x-mM.xMin)/dx;
			else if ((rxt=="L")&&(points[k].x<points[0].x))
				points[k].x=points[k].x-rx*(points[k].x-mM.xMax)/dx;
			else if ((points[k].x!=points[0].x)&&(rxt==""))
				points[k].x=points[k].x-rx*(points[k].x-points[0].x)/dx;
		}
		if (ry)
		{
			if ((ryt=="B")&&(points[k].y>points[0].y))
				points[k].y=points[k].y-ry*(points[k].y-mM.yMin)/dy;
			else if ((ryt=="T")&&(points[k].y<points[0].y))
				points[k].y=points[k].y-ry*(points[k].y-mM.yMax)/dy;
			else if ((points[k].y!=points[0].y)&&(ryt==""))
				points[k].y=points[k].y-ry*(points[k].y-points[0].y)/dy;
		}
	}
	return points;
}
function generateSequencePngUsingCanvas(paths,medians,background,mmah,size)
{
	// paths is an array that contains a list of stroke d and fill attributes
	// background will be the background of the image
	// mmah indicates if the data come from MakeMeAHanzi instead of AnimCJK
	// return a base64 encoded PNG image
	var cn,cx,k,km,km2,km3,k2,k3,m,m2,r,r2,x0,y0,x1,y1,x2,y2,x3,y3,xi,yi,w,h,xm,ym;
	var angle0,angleM,points,sloopType0,sloopTypeM,mM;
	var color,lw,radius,delta;
	lw=23;
	radius=32;
	delta=96;
	km=paths.length;
	km2=km;
	w=Math.min(8,km+1);
	h=((km+1)>>3)+1;
	// create a ghost canvas
	cn=document.createElement('canvas');
	cn.width=size*w;
	cn.height=size*h;
	cx=cn.getContext("2d");
	cx.lineCap="round";
	if (size!=1024) cx.scale(size/1024,size/1024);
	cx.fillStyle=background;
	cx.fillRect(0,0,1024*w,1024*h);
	// draw strokes in the canvas
	r=/ ?([MLQC]) ?([0-9-]+) ([0-9-]+) ?([0-9-]+)? ?([0-9-]+)? ?([0-9-]+)? ?([0-9-]+)?/g;
	r2=/ ?[ML] ?([0-9-]+)[ ,]([0-9-]+)/g;
	for(k2=0;k2<=km2;k2++)
	{
		xi=(k2%8)*1024;
		yi=(k2>>3)*1024;
		for(k=km-1;k>=0;k--) // draw last strokes first
		{
			cx.beginPath();
			x0=0;
			y0=0;
			while (m=r.exec(paths[k].d))
			{
				x1=parseInt(m[2])+xi;
				if (mmah) y1=-parseInt(m[3])+900+yi;
				else y1=parseInt(m[3])+yi;
				if (!x0) x0=x1;
				if (!y0) y0=y1;
				if (m[1]=="M") cx.moveTo(x1,y1);
				else if (m[1]=="L") cx.lineTo(x1,y1);
				else
				{
					x2=parseInt(m[4])+xi;
					if (mmah) y2=-parseInt(m[5])+900+yi;
					else y2=parseInt(m[5])+yi;
					if (m[1]=="Q") cx.quadraticCurveTo(x1,y1,x2,y2);
					else
					{
						x3=parseInt(m[6])+xi;
						if (mmah) y3=-parseInt(m[7])+900+yi;
						else y3=parseInt(m[7])+yi;
						cx.bezierCurveTo(x1,y1,x2,y2,x3,y3);
					}
				}
			}
			cx.lineTo(x0,y0); // sometimes not necessary, but it doesn't matter
			if (k2==0) cx.fillStyle="#000";
			else if (k<k2) cx.fillStyle="#666";
			else cx.fillStyle="#e7e7e7";
			cx.fill();
		}
		if (k2)
		{
			color="#77f";
			points=[];
			while (m=r2.exec(medians[k2-1].d))
			{
				if (mmah) points.push({x:parseInt(m[1]),y:-parseInt(m[2])+900});
				else points.push({x:parseInt(m[1]),y:parseInt(m[2])});
			}
			points=reducePointsNum(points);
			km3=points.length;
			if (km3>0)
			{
				x0=points[0].x+xi;
				y0=points[0].y+yi;
				cx.beginPath();
				cx.arc(x0,y0,radius,0,2*Math.PI);
				cx.fillStyle="#0f6";
				cx.fill();
				if (km3>1)
				{
					x1=points[1].x+xi;
					y1=points[1].y+yi;
					angle0=Math.atan2(y1-y0,x1-x0);
					angle0=Math.round(360/(2*Math.PI))*angle0;
					sloopType0=[];
					if ((Math.abs(angle0)<30)||(Math.abs(angle0)>150))
					{
						sloopType0[0]="H";
					}
					else if ((Math.abs(angle0)>60)&&(Math.abs(angle0)<120))
					{
						sloopType0[0]="V";
					}
					else
					{
						sloopType0[0]="O";
					}
					if (x1>x0) sloopType0[1]="R";
					else sloopType0[1]="L";
					if (y1>y0) sloopType0[2]="B";
					else sloopType0[2]="T";
					xm0=points[km3-1].x+xi;
					ym0=points[km3-1].y+yi;
					xm1=points[km3-2].x+xi;
					ym1=points[km3-2].y+yi;
					angleM=Math.atan2(ym0-ym1,xm0-xm1);
					angleM=Math.round(360/(2*Math.PI))*angleM;
					mM=minMax(points);
					sloopTypeM=[];
					if ((Math.abs(angleM)<30)||(Math.abs(angleM)>150))
					{
						sloopTypeM[0]="H";
					}
					else if ((Math.abs(angleM)>60)&&(Math.abs(angleM)<120))
					{
						sloopTypeM[0]="V";
					}
					else
					{
						sloopTypeM[0]="O";
					}
					if (xm0>xm1) sloopTypeM[1]="R";
					else sloopTypeM[1]="L";
					if (ym0>ym1) sloopTypeM[2]="B";
					else sloopTypeM[2]="T";
					a0=0;
					b0=0;
					reducX=0;
					reducY=0;
					reducXT="";
					reducYT="";
					color="#f77";
					//if (km3>5) color="#f93";
					
					if (0)
					{
					}
					
					/////////////////////////////
					// sloopType0[0]=="H" section
					/////////////////////////////
					
					else if ((sloopType0[0]=="H")&&(sloopTypeM[0]=="H"))
					{
						if ((sloopType0[1]=="R")&&(sloopTypeM[1]=="R"))
						{
							if ((km3>4)&&(Math.abs(ym0-y0)>2*delta))
							{
								// 四4
								a0=delta*1.125*0.714;
								b0=-delta*0.714;
								reducX=delta*1.125*0.714;
								//reducY=delta*1.714;
							}
							else
							{
								// ㇐1
								a0=0;
								b0=delta;
								reducX=0;
								reducY=0;
							}
						}
						else if ((sloopType0[1]=="R")&&(sloopTypeM[1]=="L"))
						{
							if (xm0<x0)
							{
								// 夕2
								a0=-delta;
								b0=delta;
								reducX=delta;
								reducY=2*delta;
							}
							else
							{
								// 寧3
								a0=0;
								b0=delta;
								if (xm0<(mM.xMax+xi)) reducX=delta*0.5+mM.xMax+xi-xm0;
								else reducX=delta;
								reducY=0;
							}
						}
						else
						{
							a0=0;
							b0=delta;
							reducX=0;
							reducY=0;
						}
					}
					else if ((sloopType0[0]=="H")&&(sloopTypeM[0]=="V"))
					{
						if ((sloopType0[1]=="R")&&(sloopTypeM[1]=="L")&&((mM.yMin+yi)<y0))
						{
							// 中2
							a0=0;
							b0=delta;
							reducX=delta*1.25;
							reducY=delta;
							reducYT="B";
						}
						else if ((sloopType0[1]=="R")&&(sloopTypeM[1]=="L")&&((mM.xMax-mM.xMin)<delta))
						{
							// 十2
							a0=-delta;
							b0=0;
							reducX=0;
							reducY=0;
						}
						else
						{
							// ㇖ (今4)
							a0=0;
							b0=delta;
							reducX=delta*1.25;
							reducY=delta*2;
						}
					}
					else if ((sloopType0[0]=="H")&&(sloopTypeM[0]=="O"))
					{
						if ((sloopType0[1]=="R")&&(sloopTypeM[1]=="L"))
						{
							// 名2 完3
							if (xm0<x0) a0=-delta;  // 名2
							else a0=0; // 完3 刀1
							b0=delta;
							if (xm0<x0) reducX=2*delta; // 名2
							else if (xm0<(mM.xMax+xi))
							{
								if ((mM.yMax-mM.yMin)<(mM.xMax-mM.xMin)/2)
									reducX=delta*0.5+(mM.xMax+xi-xm0); // 完3 家3
								else
									reducX=delta; // 刀1
							}
							else reducX=delta; // never?
							if (xm0<x0) reducY=2*delta; // 名2
							else if (ym0<(mM.yMax+yi))
							{
								if ((mM.yMax-mM.yMin)>(mM.xMax-mM.xMin)/2)
								{
									// 刀1
									reducY=delta*1.5+(mM.yMax+yi-ym0); 
									reducYT="B";
								}
								else reducY=0;
							}
							else reducY=0; // 完3
						}
						else if ((sloopType0[1]=="R")&&(sloopTypeM[1]=="R"))
						{
							if (sloopTypeM[2]=="T")
							{
								// 救4
								a0=0;
								b0=-delta;
								reducX=0;
								reducY=0;
							}
							else
							{
								// 完1
								a0=delta*1.25*0.714;
								b0=-delta*0.714;
								reducX=0;
								reducY=0;
							}
						}
						else if ((sloopType0[1]=="L")&&(sloopTypeM[1]=="R"))
						{
							a0=delta*1.25;
							b0=0;
							reducX=0;
							reducY=0;
						}
						else
						{
							a0=delta*1.25;
							b0=0;
							reducX=0;
							reducY=0;
						}
					}
					
					/////////////////////////////
					// sloopType0[0]=="V" section
					/////////////////////////////
					
					else if ((sloopType0[0]=="V")&&(sloopTypeM[0]=="V"))
					{
						if ((sloopType0[1]=="R")&&(sloopTypeM[1]=="L")
							&&(sloopType0[2]=="B")&&(sloopTypeM[2]=="T"))
						{
							// 籠19
							a0=delta*1.25*0.714;
							b0=-delta*0.714;
							reducX=delta*(1.25*0.714+1);
							reducY=delta*(1-0.714);
						}
						else
						{
							// ㇑1
							a0=delta*1.25;
							b0=0;
							reducX=0;
							reducY=0;
						}
					}
					else if ((sloopType0[0]=="V")&&(sloopTypeM[0]=="H"))
					{
						if ((sloopType0[2]=="B")&&(sloopTypeM[1]=="R"))
						{
							a0=delta*1.25;
							b0=0;
							reducX=delta*1.25;
							reducY=delta;
						}
						else if ((sloopType0[2]=="B")&&(sloopTypeM[1]=="L"))
						{
							// ㇒ (火3)
							a0=-delta;
							b0=0;
							reducX=delta*1.25;
							reducY=delta;
						}
					}
					else if ((sloopType0[0]=="V")&&(sloopTypeM[0]=="O"))
					{
						if ((sloopType0[1]=="R")&&(sloopTypeM[1]=="R"))
						{
							// 母1
							a0=delta*1.25;
							b0=0;
							reducX=delta*1.25;
							reducY=delta;
						}
						else
						{
							// ㇒ (今1)
							a0=-delta;
							b0=0;
							reducX=delta*1.25;
							reducY=delta;
						}
					}
					
					/////////////////////////////
					// sloopType0[0]=="O" section
					/////////////////////////////
					
					else if ((sloopType0[0]=="O")&&(sloopTypeM[0]=="O"))
					{
						if ((sloopType0[1]=="R")&&(sloopTypeM[1]=="R"))
						{
							if ((sloopType0[2]=="B")&&(sloopTypeM[2]=="T"))
							{
								// 比2
								a0=delta*1.25*0.714;
								b0=-delta*0.714;
								reducX=0;
								reducY=delta*(1-0.714+0.5);
							}
							else
							{
								// ㇔ (火2)
								a0=delta*1.25*0.714;
								b0=-delta*0.714;
								reducX=0;
								reducY=0;
							}
						}
						else if ((sloopType0[1]=="L")&&(sloopTypeM[1]=="L"))
						{
							// ㇒(火1)
							a0=delta*1.25*0.714;
							b0=delta*0.714;
							reducX=0;
							reducY=0;
						}
						else if ((sloopType0[1]=="R")&&(sloopTypeM[1]=="L"))
						{
							if ((sloopType0[2]=="T")&&(sloopTypeM[2]=="T"))
							{
								// 馬6
								a0=-delta*1.25*0.714;
								b0=delta*0.714;
								reducX=0;
								reducY=delta*(0.714+1);
								reducYT="B";
							}
							else if ((sloopType0[2]=="B")&&(sloopTypeM[2]=="T")&&(xm0>x0))
							{
								// 寧5 狗2
								a0=-delta*0.714;
								b0=delta*0.714;
								if ((mM.xMax-mM.xMin)>(mM.yMax-mM.yMin)) // 寧5
									reducX=-delta*(0.714*2)-2*(mM.xMax+xi-xm0);
								else // 狗2
									reducX=0;
								if ((mM.xMax-mM.xMin)>(mM.yMax-mM.yMin)) // 寧5
									reducY=0;
								else // 狗2
									reducY=delta*(0.714*2)+(mM.yMax+yi-ym0);
							}
							else
							{
								// 丁2 夕1
								a0=-delta;
								b0=0;
								reducX=delta;
								reducY=delta;
							}
						}
						else
						{
							// 
							a0=delta*1.25;
							b0=0;
							reducX=0;
							reducY=0;
						}
					}
					else if ((sloopType0[0]=="O")&&(sloopTypeM[0]=="H"))
					{
						if ((sloopType0[1]=="R")&&(sloopTypeM[1]=="R"))
						{
							// ㇏ (火4)
							// 七2
							// 出2 出4
							a0=delta*1.25*0.714;
							b0=-delta*0.714;
							reducX=delta*1.25*0.714;
							reducY=delta*(1-0.714);
						}
						else if ((sloopType0[1]=="L")&&(sloopTypeM[1]=="L"))
						{
							// ㇒ (家5)
							a0=delta*1.25*0.714;
							b0=delta*0.714;
							reducX=0;
							reducY=0;
						}
						else if ((sloopType0[1]=="R")&&(sloopTypeM[1]=="L"))
						{
							// ㇒ (豸4?)
							// 火3
							a0=-delta;
							b0=0;
							reducX=delta;
							reducY=delta;
						}
						else
						{
							a0=delta*1.25;
							b0=0;
							reducX=0;
							reducY=delta;
						}
					}
					else if ((sloopType0[0]=="O")&&(sloopTypeM[0]=="V"))
					{
						if ((sloopType0[1]=="R")&&(sloopTypeM[1]=="L"))
						{
							if ((sloopType0[2]=="B")&&(sloopTypeM[2]=="T"))
							{
								// 見6 near from ORVR (池6 先5)
								a0=delta*0.714;
								b0=-delta*0.714;
								reducX=delta*1.714;
								reducY=0;
							}
							else
							{
								// 出1ja 馬1ja 中3
								a0=-delta;
								b0=0;
								reducX=0;
								reducY=0;
							}

						}
						else if ((sloopType0[1]=="R")&&(sloopTypeM[1]=="R"))
						{
							if ((sloopType0[2]=="B")&&(sloopTypeM[2]=="T"))
							{
								if (ym0<y0)
								{
									// 池3
									a0=-delta*0.714;
									b0=-delta*0.714;
									reducX=0;
									reducY=delta*0.714;
								}
								else
								{
									// 池6
									a0=delta*0.714;
									b0=-delta*0.714;
									reducX=delta*1.714;
									reducY=0;
								}
							}
							else
							{
								// 夕3
								a0=delta*1.25*0.714;
								b0=-delta*0.714;
								reducX=0;
								reducY=0;
							}
						}
						else if ((sloopType0[1]=="L")&&(sloopTypeM[1]=="L"))
						{
							a0=delta*1.25;
							b0=0;
							reducX=0;
							reducY=0;
						}
						else
						{
							a0=delta*1.25;
							b0=0;
							reducX=0;
							reducY=0;
						}
					}
					
					/////////////////////////////
					// complex stroke section
					/////////////////////////////

					else if (km3>5)
					{
						// 2nd 九
						a0=-delta*1.25;
						b0=delta;
						reducX=0;
						reducY=0;
						color="#70f";
					}
					else
					{
						a0=0;
						b0=0;
						reducX=0;
						reducY=0;
						color="#f7f";
					}
					
					if (debug) debug(k2+" km3="+km3+" rx:"+Math.round(reducX)+" ry:"+Math.round(reducY)+" a0:"+Math.round(angle0)+" aM:"+Math.round(angleM)+" "+sloopType0[0]+" "+sloopType0[1]+" "+sloopType0[2]+" "+sloopTypeM[0]+" "+sloopTypeM[1]+" "+sloopTypeM[2]+"<br>",1);
					points=reducePointsSize(points,reducX,reducY,reducXT,reducYT,delta,mM);
					for (k3=0;k3<km3;k3++)
					{
						points[k3].x=points[k3].x+xi+a0;
						points[k3].y=points[k3].y+yi+b0;
					}
					smooth(cx,points,color,lw);
					arrow(cx,points[km3-2].x,points[km3-2].y,points[km3-1].x,points[km3-1].y,color,lw);
				}
			}
		}
	}
	// generate a base64 encoded PNG from the canvas then return it
	return cn.toDataURL("image/png");
}
function generateSequencePngFromSvg(s,size,background)
{
	// generate a "sequence" PNG image from a svg
	// s is a text representing a character in svg format
	// size will be the size of the PNG
	// background will be the background of the PNG (including transparent)
	var k,km,reg,m,paths=[],medians=[],mmah;
	// if mmah is true, assume the svg comes from MakeMeAHanzi
	// else assume the svg comes from animCJK
	mmah=!s.match(/class="acjk"/);
	// extract "d" attribute values
	if (mmah) reg=/<path d=\"([^\"]*)\" fill=\"lightgray\"/g;
	else reg=/<path id=\"[^\"]*\" d=\"([^\"]*)\"/g;
	while (m=reg.exec(s)) paths.push({d:m[1]});
	reg=/<path[^>]+clip-path=[^>]+ d=\"([^\"]*)\"/g;
	while (m=reg.exec(s)) medians.push({d:m[1]});
	km=paths.length;
	if (debug) debug("<br>generateSequencePngFromSvg pathNum="+km+" medianNum="+medians.length+" mmah="+mmah+"<br>",0);
	// first image
	for (k=0;k<km;k++) paths[k].fill="black";
	// return base64 PNG image
	return generateSequencePngUsingCanvas(paths,medians,background,mmah,size);
}
