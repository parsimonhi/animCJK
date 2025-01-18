// to recompute medians
// need an approximative starting point positioned near the optimal starting point
//   for each median
// usage
//   include this script in your page
//   acjkm.run(p);
//     where p is an object that contains some parameters
//     see acjkm.run() code for more details

Array.prototype.unique=function()
{
    var a=this.concat();
    for(var i=0; i<a.length; ++i) {
        for(var j=i+1; j<a.length; ++j) {
            if(a[i] === a[j])
                a.splice(j--, 1);
        }
    }
    return a;
};
// one and only one global variable: acjkm
if (typeof acjkm=='undefined') acjkm={};
// must be done at the beginning since currentScript could become null later
acjkm.url=new URL(document.currentScript.src);
acjkm.d=1024; // canvas width
acjkm.c=Math.round(42*acjkm.d/1024); // contour min length
// acjkm.step:
// step when looping through contour point list
// one retains only one point every step
// A too small value may let disturbing medians (espcially when a final translation has to be done).
// A too big value may skip relevant medians.
// don't forget very small strokes
// acjkm.largeThreshold:
// when a segment length > acjkm.largeThreshold, one transaltes its median to the "external" edge
// acjkm.abnormal:
// when a segment length > acjkm.abnormal, one retries to compute medians with another acjkm.step
// acjkm.stepMax:
// if a segment length > acjkm.abnormal one stops to iterate computation if acjkm.step > acjkm.stepMax
// acjkm.simplificationThreshold:
// threshold for median list simplification using Douglas Peucker algorithm

// boardClass:
// each board has a canvas where a stroke is displayed
acjkm.boardClass=function(c,k) {this.character=c;this.k=k;};
acjkm.boardClass.prototype.isEmpty=function(x,y)
{
	if ((x<0)||(y<0)||(x>=acjkm.d)||(y>=acjkm.d)) return true;
	if (this.bitmap[acjkm.xy2k(x,y)+3])
	{
		if ((x>0)&&this.bitmap[acjkm.xy2k(x-1,y)+3]) return false;
		if ((y>0)&&this.bitmap[acjkm.xy2k(x,y-1)+3]) return false;
		if ((x<(acjkm.d-1))&&this.bitmap[acjkm.xy2k(x+1,y)+3]) return false;
		if ((y<(acjkm.d-1))&&this.bitmap[acjkm.xy2k(x,y+1)+3]) return false;
	}
	return true;
};
acjkm.boardClass.prototype.isOutside=function(x1,y1,x2,y2)
{
	// return true if (x1,y1,x2,y2) segment has a signifiant part outside the stroke shape
	var outside=false;
	var epsilon=2;
	if (x1==x2)
	{
		if (y1>y2) {y=y2;y2=y1;y1=y;}
		for (k2=(y1+epsilon+1);k2<(y2-epsilon);k2++) if (this.isEmpty(x1,k2)) outside=true;
	}
	else if (y1==y2)
	{
		if (x1>x2) {x=x2;x2=x1;x1=x;}
		for (k2=(x1+epsilon+1);k2<(x2-epsilon);k2++) if (this.isEmpty(k2,y1)) outside=true;
	}
	else
	{
		if (Math.abs(x2-x1)>=Math.abs(y2-y1))
		{
			if (x1>x2) {x=x2;x2=x1;x1=x;y=y2;y2=y1;y1=y;}
			a=(y2-y1)/(x2-x1);
			b=y1-a*x1;
			for (k2=(x1+epsilon+1);k2<(x2-epsilon);k2++)
				if (this.isEmpty(k2,Math.round(a*k2+b))) outside=true;
		}
		else
		{
			z=x1;x1=y1;y1=z;z=x2;x2=y2;y2=z;
			if (x1>x2) {x=x2;x2=x1;x1=x;y=y2;y2=y1;y1=y;}
			a=(y2-y1)/(x2-x1);
			b=y1-a*x1;
			for (k2=(x1+epsilon+1);k2<(x2-epsilon);k2++)
				if (this.isEmpty(Math.round(a*k2+b),k2)) outside=true;
		}
	}
	return outside;
}
acjkm.boardClass.prototype.drawDot=function(x,y,c)
{
	this.cx.fillStyle=c;
	this.cx.fillRect(x,y,1,1);
};
acjkm.boardClass.prototype.drawPoint=function(x,y,c)
{
	this.cx.beginPath();
	this.cx.fillStyle=c;
	this.cx.arc(x,y,3*acjkm.d/1024,0,2*Math.PI);
	this.cx.fill();
};
acjkm.boardClass.prototype.drawBigPoint=function(x,y,c)
{
	this.cx.beginPath();
	this.cx.fillStyle=c;
	this.cx.arc(x,y,6*acjkm.d/1024,0,2*Math.PI);
	this.cx.fill();
};
acjkm.boardClass.prototype.drawLine=function(x1,y1,x2,y2,c)
{
	this.cx.beginPath();
	this.cx.lineWidth=2;
	this.cx.strokeStyle=c;
	this.cx.moveTo(x1,y1);
	this.cx.lineTo(x2,y2);
	this.cx.stroke();
	this.cx.lineWidth=1;
};
acjkm.boardClass.prototype.getMiniLocal=function(nStroke,p,dir)
{
	// return p;
	// the algorithm below is not perfect
	var xo,yo,x1,y1,x2,y2,x3,y3,x4,y4,x,y,xm,ym,x5,y5,x6,y6;
	var k,dir2,bestSpecialD,delta,strange,angle,s;
	var Z;
	// find points on the edge of the stroke shape using the Square Tracing Algorithm
	// near the start point, then return the point that is the most in a "corner"
	xo=p[0];
	yo=p[1];
	delta=acjkm.c;
	x=xo;
	y=yo;
	k=0;
	M=[];
	M.push({x:x,y:y});
	if (this.isEmpty(x-1,y)) dir2="E";
	else if (this.isEmpty(x,y-1)) dir2="S";
	else if (this.isEmpty(x+1,y)) dir2="W";
	else dir2="N";
	while ((k<10000)&&(x>xo-delta)&&(y>yo-delta)&&(x<xo+delta)&&(y<yo+delta)&&!((x==xo)&&(y==yo)&&(k>4)))
	{
		k++;
		if (!this.isEmpty(x,y))
		{
			M.push({x:x,y:y});
			if (acjkm.startContourOn) this.drawPoint(x,y,"orange");
			if (dir2=="E") {y=y-1;dir2="N";}
			else if (dir2=="S") {x=x+1;dir2="E";}
			else if (dir2=="W") {y=y+1;dir2="S";}
			else {x=x-1;dir2="W";}
		}
		else
		{
			if (dir2=="E") {y=y+1;dir2="S";}
			else if (dir2=="S") {x=x-1;dir2="W";}
			else if (dir2=="W") {y=y-1;dir2="N";}
			else {x=x+1;dir2="E";}
		}
	}
	x1=x;
	y1=y;
	x=xo;
	y=yo;
	k=0;
	if (this.isEmpty(x-1,y)) dir2="E";
	else if (this.isEmpty(x,y-1)) dir2="S";
	else if (this.isEmpty(x+1,y)) dir2="W";
	else dir2="N";
	while ((k<10000)&&(x>xo-delta)&&(y>yo-delta)&&(x<xo+delta)&&(y<yo+delta)&&!((x==xo)&&(y==yo)&&(k>4)))
	{
		k++;
		if (!this.isEmpty(x,y))
		{
			M.push({x:x,y:y});
			if (acjkm.startContourOn) this.drawPoint(x,y,"orange");
			if (dir2=="E") {y=y+1;dir2="S";}
			else if (dir2=="S") {x=x-1;dir2="W";}
			else if (dir2=="W") {y=y-1;dir2="N";}
			else {x=x+1;dir2="E";}
		}
		else
		{
			if (dir2=="E") {y=y-1;dir2="N";}
			else if (dir2=="S") {x=x+1;dir2="E";}
			else if (dir2=="W") {y=y+1;dir2="S";}
			else {x=x-1;dir2="W";}
		}
	}
	x2=x;
	y2=y;
	xm=Math.round((x1+x2)/2);
	ym=Math.round((y1+y2)/2);
	if (x1==x2)
	{
		if (xo<xm) x4=xm+Math.abs((y2-y1)/2);
		else x4=xm-Math.abs((y2-y1)/2);
		y4=ym;
	}
	else if (y1==y2)
	{
		x4=xm;
		if (yo<ym) y4=ym+Math.abs((x2-x1)/2);
		else y4=ym-Math.abs((x2-x1)/2);
	}
	else
	{
		x5=xm-y1+ym;
		y5=ym+x1-xm;
		x6=xm+y1-ym;
		y6=ym-x1+xm;
		d5=(xo-x5)*(xo-x5)+(yo-y5)*(yo-y5);
		d6=(xo-x6)*(xo-x6)+(yo-y6)*(yo-y6);
		if (d5>d6) {x4=x5;y4=y5;}
		else {x4=x6;y4=y6;}
	}
	bestSpecialD=0;
	x3=xo;
	y3=yo;
	for (k=0;k<M.length;k++)
	{
		x=M[k].x;
		y=M[k].y;
		specialD=(x-x4)*(x-x4)+(y-y4)*(y-y4);
		if (specialD>bestSpecialD)
		{
			bestSpecialD=specialD;
			x3=x;
			y3=y;
		}
	}
	if (acjkm.startContourOn)
	{
		this.drawLine(x1,y1,x2,y2,"orange");
		this.drawBigPoint(x1,y1,"orange");
		this.drawBigPoint(x2,y2,"orange");
		this.drawLine(xm,ym,x4,y4,"orange");
		this.drawPoint(xm,ym,"orange");
		this.drawPoint(x4,y4,"orange");
	}
	return [x3,y3];
};
acjkm.boardClass.prototype.startPoint=function(nStroke,xo,yo)
{
	// find an acceptable "start" point on the edge of the stroke shape.
	var x,y,dWest,dEast,dSouth,dNorth,dMax,edge,x1,y1,x2,y2,k,s;
	x=xo;
	y=yo;
	
	if (this.isEmpty(x,y))
	{
		// the point is outside the stroke shape (not normal but can happen)
		if (acjkm.strangeStrokes) acjkm.strangeStrokes+=", ";
		s=this.character+" stroke #"+(nStroke+1)+" (starting point outside stroke shape)";
		acjkm.strangeStrokes+="<span class=\"strangeStroke\">"+s+"</span>";
		k=0;
		while (k<acjkm.d)
		{
			k++;
			if (!this.isEmpty(x+k,y)) {x=x+k;break;}
			if (!this.isEmpty(x+k,y+k)) {x=x+k;y=y+k;break;}
			if (!this.isEmpty(x,y+k)) {y=y+k;break;}
			if (!this.isEmpty(x-k,y+k)) {x=x-k;y=y+k;break;}
			if (!this.isEmpty(x-k,y)) {x=x-k;break;}
			if (!this.isEmpty(x-k,y-k)) {x=x-k;y=y-k;break;}
			if (!this.isEmpty(x,y-k)) {y=y-k;break;}
			if (!this.isEmpty(x+k,y-k)) {x=x+k;y=y-k;break;}
		}
		xo=x;
		yo=y;
	}
	// the point is inside the stroke shape
	while (x>0)
	{
		x--;
		if (this.isEmpty(x,y)) {dWest=xo-x-1;break;}
	}
	if (acjkm.extremaOn) this.drawBigPoint(xo-dWest,y,"lime");
	x=xo;
	while (y>0)
	{
		y--;
		if (this.isEmpty(x,y)) {dNorth=yo-y-1;break;}
	}
	if (acjkm.extremaOn) this.drawBigPoint(xo,yo-dNorth,y,"lime");
	y=yo;
	while (x<acjkm.d)
	{
		x++;
		if (this.isEmpty(x,y)) {dEast=x-xo-1;break;}
	}
	if (acjkm.extremaOn) this.drawBigPoint(xo+dEast,y,"lime");
	x=xo;
	while (y<acjkm.d)
	{
		y++;
		if (this.isEmpty(x,y)) {dSouth=y-yo-1;break;}
	}
	if (acjkm.extremaOn) this.drawBigPoint(xo,yo+dSouth,y,"lime");
	y=yo;
	
	dMax=Math.max(dEast,dWest,dSouth,dNorth);
	if (dMax==dEast) {edge=this.getMiniLocal(nStroke,[xo-dWest,yo],"W");}
	else if (dMax==dWest) {edge=this.getMiniLocal(nStroke,[xo+dEast,yo],"E");}
	else if (dMax==dSouth) {edge=this.getMiniLocal(nStroke,[xo,yo-dNorth],"N");}
	else {edge=this.getMiniLocal(nStroke,[xo,yo+dSouth],"S");}
	
	if (acjkm.startPointOn) this.drawBigPoint(edge[0],edge[1],"blue");
	return edge;
};
acjkm.boardClass.prototype.contour=function(nStroke,xo,yo,wo)
{
	// Find all points on the edge of the stroke shape using the Square Tracing Algorithm.
	var k,l,x,y,Z,dir,w,s;
	w=wo;
	while (w)
	{
		k=0;
		l=0;
		x=xo;
		y=yo;
		Z=[];
		if (this.isEmpty(x-1,y)) dir="E";
		else if (this.isEmpty(x,y-1)) dir="S";
		else if (this.isEmpty(x+1,y)) dir="W";
		else dir="N";
		while ((k<10000)&&!((x==xo)&&(y==yo)&&(k>4)))
		{
			k++;
			if (!this.isEmpty(x,y))
			{
				if (acjkm.fullContourOn) this.drawPoint(x,y,"orange");
				if (dir=="E") {y=y-1;dir="N";}
				else if (dir=="W") {y=y+1;dir="S";}
				else if (dir=="S") {x=x+1;dir="E";}
				else {x=x-1;dir="W";}
			}
			else
			{
				if (dir=="E") {y=y+1;dir="S";}
				else if (dir=="W") {y=y-1;dir="N";}
				else if (dir=="S") {x=x-1;dir="W";}
				else {x=x+1;dir="E";}
			}
			if (!this.isEmpty(x,y))
			{
				l++;
				if (!(l%w)) Z.push([x,y]);
			}
		}
		// remove last point if too close to the end
		// take care of very small strokes
		if (Z.length>4)
		{
			if ((l%w)<(w>>1)) Z.pop();
			w=0;
		}
		else
		{
			if (acjkm.strangeStrokes) acjkm.strangeStrokes+=", ";
			s=this.character+" stroke #"+(nStroke+1)+" (very small stroke, step="+w+")";
			acjkm.strangeStrokes+="<span class=\"strangeStroke\">"+s+"</span>";
			w=w>>1;
		}
	}
	return Z;
};
acjkm.boardClass.prototype.magic=function(nStroke,p1)
{
	// The magic chinese character stroke medians algorithm
	// Given a stroke shape (a black shape on a bitmap) and a "start" point,
	// find a relevant list of points call medians that are in the "middle" of the shape.
	// Usefull to animate the character using css stroke-dashoffset and stroke-dasharray.
	var x,y,x1,y1,x2,y2,xo,yo,xi1,yi1,xj1,yj1,xi2,yi2,xj2,yj2,xi3,yi3,xj3,yj3;
	var k,km,kom,i,im,j,jm,t,s;
	var Z=[],M=[];
	var dis,start,signe,side,angle,A,B,C,C1,C2;
	var dis11,dis12,dis13,dis21,dis22,dis23;
	var bestT,bestDisMax,bestRecompute;
	
	// Step 1
	// Using the first median point found in graphics.txt,
	// find an acceptable "start" point on the edge of the stroke shape.
	if (acjkm.originOn) this.drawBigPoint(p1[0],p1[1],"yellow");
	start=this.startPoint(nStroke,p1[0],p1[1]);
	
	// Step 2
	// Find all points on the edge of the stroke shape using the Square Tracing Algorithm.
	xo=start[0];
	yo=start[1];
	t=1;
	bestT=1;
	bestDisMax=acjkm.d*acjkm.d;
	bestRecompute=false;
	this.disMaxList="";
	while (t)
	{
		M=[];
		Z=this.contour(nStroke,xo,yo,t+acjkm.step-1);
		// Step 3
		// For every point on edge, find a close point on "opposite" side.
		// The middle of the lines between each couple of these points are the medians.
		// Stop when points of one side cross points of other side at the stroke "end".
		km=Z.length;
		kom=Math.max(1,Math.min(Math.ceil(acjkm.largeThreshold/acjkm.step)>>1,km));
		for (k=0;k<kom;k++)
		{
			i=k;
			j=km-1-k;
			x1=Z[i][0];
			y1=Z[i][1];
			x2=Z[j][0];
			y2=Z[j][1];
			dis=(x1-x2)*(x1-x2)+(y1-y2)*(y1-y2);
			xm=Math.round((x1+x2)/2);
			ym=Math.round((y1+y2)/2);
			M.push({x:xm,y:ym,d:dis,xi:x1,yi:y1,xj:x2,yj:y2,side:"right"});			
			if (i>=(j-2)) break;
		}
		side="left";
		while (i<(j-2))
		{
			if (side=="left")
			{
				i++;
				xi1=Z[i][0];
				yi1=Z[i][1];
				xi2=Z[i+1][0];
				yi2=Z[i+1][1];
				xi3=Z[i+2][0];
				yi3=Z[i+2][1];
				xj1=Z[j][0];
				yj1=Z[j][1];
				xj2=Z[j-1][0];
				yj2=Z[j-1][1];
				xj3=Z[j-2][0];
				yj3=Z[j-2][1];
				dis11=(xi1-xj1)*(xi1-xj1)+(yi1-yj1)*(yi1-yj1);
				dis12=(xi1-xj2)*(xi1-xj2)+(yi1-yj2)*(yi1-yj2);
				dis13=(xi1-xj3)*(xi1-xj3)+(yi1-yj3)*(yi1-yj3);
				dis21=(xi2-xj1)*(xi2-xj1)+(yi2-yj1)*(yi2-yj1);
				dis22=(xi2-xj2)*(xi2-xj2)+(yi2-yj2)*(yi2-yj2);
				dis23=(xi2-xj3)*(xi2-xj3)+(yi2-yj3)*(yi2-yj3);
				dis31=(xi3-xj1)*(xi3-xj1)+(yi3-yj1)*(yi3-yj1);
				if ((i<(j-2))&&((dis11>=dis12)||(!this.isOutside(xi1,yi1,xj2,yj2)&&this.isOutside(xi1,yi1,xj1,yj1))))
				{
					side="right";
					if ((M[M.length-1].xi!=xi1)||(M[M.length-1].yi!=yi1)||(M[M.length-1].xj!=xj2)||(M[M.length-1].yj!=yj2))
					{
						xm=Math.round((xi1+xj2)/2);
						ym=Math.round((yi1+yj2)/2);
						M.push({x:xm,y:ym,d:dis12,xi:xi1,yi:yi1,xj:xj2,yj:yj2,side:"left"});
					}
				}
				else
				{
					if (this.isOutside(xi2,yi2,xj1,yj1)&&this.isOutside(xi2,yi2,xj2,yj2)&&!this.isOutside(xi1,yi1,xj2,yj2)) side="right";
					else if (!this.isOutside(xi2,yi2,xj1,yj1)&&this.isOutside(xi2,yi2,xj2,yj2)&&this.isOutside(xi1,yi1,xj2,yj2)) side="left";
					else if (dis31<dis12) side="left";
					else if (dis21<dis12) side="left";
					else if (dis12<dis22) side="right";
					else if (dis12<dis21) side="right";
					else side="left";
					if ((M[M.length-1].xi!=xi1)||(M[M.length-1].yi!=yi1)||(M[M.length-1].xj!=xj1)||(M[M.length-1].yj!=yj1))
					{
						xm=Math.round((xi1+xj1)/2);
						ym=Math.round((yi1+yj1)/2);
						M.push({x:xm,y:ym,d:dis11,xi:xi1,yi:yi1,xj:xj1,yj:yj1,side:"left"});
					}
				}
			}
			else
			{
				j--;
				xj1=Z[j][0];
				yj1=Z[j][1];
				xj2=Z[j-1][0];
				yj2=Z[j-1][1];
				xj3=Z[j-2][0];
				yj3=Z[j-2][1];
				xi1=Z[i][0];
				yi1=Z[i][1];
				xi2=Z[i+1][0];
				yi2=Z[i+1][1];
				xi3=Z[i+2][0];
				yi3=Z[i+2][1];
				dis11=(xj1-xi1)*(xj1-xi1)+(yj1-yi1)*(yj1-yi1);
				dis12=(xj1-xi2)*(xj1-xi2)+(yj1-yi2)*(yj1-yi2);
				dis13=(xj1-xi3)*(xj1-xi3)+(yj1-yi3)*(yj1-yi3);
				dis21=(xj2-xi1)*(xj2-xi1)+(yj2-yi1)*(yj2-yi1);
				dis22=(xj2-xi2)*(xj2-xi2)+(yj2-yi2)*(yj2-yi2);
				dis23=(xj2-xi3)*(xj2-xi3)+(yj2-yi3)*(yj2-yi3);
				dis31=(xj3-xi1)*(xj3-xi1)+(yj3-yi1)*(yj3-yi1);
				if ((i<(j-2))&&((dis11>=dis12)||(!this.isOutside(xj1,yj1,xi2,yi2)&&this.isOutside(xj1,yj1,xi1,yi1))))
				{
					side="left";
					if ((M[M.length-1].xi!=xi2)||(M[M.length-1].yi!=yi2)||(M[M.length-1].xj!=xj1)||(M[M.length-1].yj!=yj1))
					{
						xm=Math.round((xj1+xi2)/2);
						ym=Math.round((yj1+yi2)/2);
						M.push({x:xm,y:ym,d:dis12,xi:xi2,yi:yi2,xj:xj1,yj:yj1,side:"right"});
					}
				}
				else
				{
					if (this.isOutside(xj2,yj2,xi1,yi1)&&this.isOutside(xj2,yj2,xi2,yi2)&&!this.isOutside(xj1,yj1,xi2,yi2)) side="left";
					else if (!this.isOutside(xj2,yj2,xi1,yi1)&&this.isOutside(xj2,yj2,xi2,yi2)&&this.isOutside(xj1,yj1,xi2,yi2)) side="right";
					else if (dis31<dis12) side="right";
					else if (dis21<dis12) side="right";
					else if (dis12<dis22) side="left";
					else if (dis12<dis21) side="left";
					else side="right";
					if ((M[M.length-1].xi!=xi1)||(M[M.length-1].yi!=yi1)||(M[M.length-1].xj!=xj1)||(M[M.length-1].yj!=yj1))
					{
						xm=Math.round((xj1+xi1)/2);
						ym=Math.round((yj1+yi1)/2);
						M.push({x:xm,y:ym,d:dis11,xi:xi1,yi:yi1,xj:xj1,yj:yj1,side:"right"});
					}
				}
			}
		}

		// Step 4
		// Compute the longuest distance between points of both sides.
		// Display segments
		this.disMax=0;
		km=M.length;
		for (k=0;k<km;k++)
		{
			if (this.disMax<M[k].d) this.disMax=M[k].d;
			if ((M[k].side=="left")&&acjkm.edge2edgeLeftOn)
			{
				this.drawPoint(M[k].x,M[k].y,"red");
				this.drawLine(M[k].xi,M[k].yi,M[k].xj,M[k].yj,"red");
			}
			else if ((M[k].side=="right")&&acjkm.edge2edgeRightOn)
			{
				this.drawPoint(M[k].x,M[k].y,"cyan");
				this.drawLine(M[k].xi,M[k].yi,M[k].xj,M[k].yj,"cyan");
			}
		}
		// A segment length > acjkm.abnormal is abnormal.
		// In such a case, retry using a different step to find a better contour
		if (bestRecompute)
		{
			t=0;
			this.disMaxList+=",r"+Math.ceil(Math.sqrt(this.disMax));
		}
		else
		{
			if (this.disMaxList) this.disMaxList+=",";
			this.disMaxList+=Math.ceil(Math.sqrt(this.disMax));
			if (this.disMax<acjkm.disAbnormal)
			{
				t=0;
			}
			else
			{
				if (t>(acjkm.stepMax-acjkm.step))
				{
					if (bestDisMax>this.disMax) t=0;
					else
					{
						t=bestT;
						bestRecompute=true;
					}
				}
				else
				{
					if (bestDisMax>this.disMax)
					{
						bestT=t;
						bestDisMax=this.disMax;
					}
					t++;
				}
			}
		}
		if (!t)
			if (!acjkm.disMax||(acjkm.disMax.d<this.disMax))
				acjkm.disMax={character:[...acjkm.data][0],stroke:nStroke+1,d:this.disMax};
	}

	// Step 5
	// Simplify median point list using Douglas-Peucker Algorithm.
	if (acjkm.simplificationThreshold&&(M.length>2)) M=acjkm.simplify(M,acjkm.simplificationThreshold);

	// Step 6
	// Drift medians in direction of the "external" edge if they are too far from it
	if (acjkm.largeThreshold&&(this.disMax>acjkm.disLargeThreshold)&&(M.length>2))
	{
		
		if (acjkm.largeStrokes) acjkm.largeStrokes+=" ";
		s=this.character+" stroke #"+(nStroke+1)+" ("+this.disMaxList+")";
		if (this.disMaxList.indexOf(",")>=0) s="<span class=\"veryLargeStroke\">"+s+"</span>";
		acjkm.largeStrokes+=s;
		km=M.length;
		M[0].convexity=0;
		M[km-1].convexity=0;
		for (k=1;k<(km-1);k++)
		{
			if (M[k].d>acjkm.disLargeThreshold)
			{
				A={x:M[k].xi,y:M[k].yi};
				B={x:M[k].x,y:M[k].y};
				C1={x:M[k-1].x,y:M[k-1].y};
				C2={x:M[k+1].x,y:M[k+1].y};
				angle=Math.abs(acjkm.angle(A,B,C1))+Math.abs(acjkm.angle(A,B,C2));
				if (angle>Math.PI) M[k].convexity=1;
				else if (angle<Math.PI) M[k].convexity=-1;
				else M[k].convexity=0;
			}
			else M[k].convexity=0;
		}
		for (k=1;k<(km-1);k++)
		{			
			if (M[k].convexity)
			{
				if (M[k].convexity>0)
				{
					M[k].x=acjkm.drift(M[k].xi,M[k].xj,Math.sqrt(M[k].d),acjkm.largeThreshold);
					M[k].y=acjkm.drift(M[k].yi,M[k].yj,Math.sqrt(M[k].d),acjkm.largeThreshold);
				}
				else if (M[k].convexity<0)
				{
					M[k].x=acjkm.drift(M[k].xj,M[k].xi,Math.sqrt(M[k].d),acjkm.largeThreshold);
					M[k].y=acjkm.drift(M[k].yj,M[k].yi,Math.sqrt(M[k].d),acjkm.largeThreshold);
				}
			}
		}
		
	}
	if (M.length<2)
	{
		if (acjkm.strangeStrokes) acjkm.strangeStrokes+=", ";
		s=this.character+" stroke #"+(nStroke+1)+" (unable to find at least two medians)";
		acjkm.strangeStrokes+="<span class=\"strangeStroke\">"+s+"</span>";
	}
	return M;
};
acjkm.boardClass.prototype.createNewSvg=function(nStroke,p1)
{
	var s,k,km,allAreHere;
	var M=this.magic(nStroke,p1);
	// final step
	// Draw medians on the canvas.
	km=M.length;
	for (k=0;k<km;k++)
	{
		if (acjkm.mediansOn)
		{
			this.drawBigPoint(M[k].x,M[k].y,"#eee");
			if (k>0) this.drawLine(M[k-1].x,M[k-1].y,M[k].x,M[k].y,"#eee");
		}
	}
	// Build new medians array for the current stroke as a json.
	s="[";
	for (k=0;k<km;k++)
	{
		if (k>0) s+=",";
		s+="[";
		M[k].x=Math.round(1024*M[k].x/acjkm.d);
		M[k].y=Math.round(900-1024*M[k].y/acjkm.d);
		s+=M[k].x+","+M[k].y;
		s+="]";
	}
	s+="]";
	this.medians=s;
	allAreHere=true;
	acjkm.newMedianNum+=km;
	if(acjkm.canvasContainer)
	{
		document.getElementById("result"+this.k).innerHTML="Stroke #"+(this.k+1)+"<br>";
		document.getElementById("result"+this.k).innerHTML+="Num of medians: "+km+"<br>";
		document.getElementById("result"+this.k).innerHTML+="Stroke width max: "+Math.ceil(Math.sqrt(this.disMax))+"<br>";
	}
	// Display and save results if all are here
	for (k=0;k<acjkm.numOfStrokes;k++) if (!acjkm.board[k].medians) {allAreHere=false;break;}
	if (allAreHere)
	{
		s="[";
		for (k=0;k<acjkm.numOfStrokes;k++)
		{
			if (k>0) s+=",";
			s+=acjkm.board[k].medians;
		}
		s+="]";
		acjkm.newJsonLine=acjkm.oldJsonLine.replace(/\[\[\[(.*)\]\]\]/,s);
		if(acjkm.strangeStrokesOutput)
			acjkm.strangeStrokesOutput.innerHTML="Strange strokes: "+(acjkm.strangeStrokes?acjkm.strangeStrokes:"none");
		if ((acjkm.data.length==1)&&(acjkm.finalResultOutput))
		{
			acjkm.finalResultOutput.innerHTML+="New Json line:<br>"+acjkm.newJsonLine+"<br>";
			acjkm.finalResultOutput.innerHTML+="Stroke width max amid all data: character="+acjkm.disMax.character+", stroke=#"+acjkm.disMax.stroke+", width max="+Math.ceil(Math.sqrt(acjkm.disMax.d))+"<br>";
			acjkm.finalResultOutput.innerHTML+="Execution time: "+(new Date().getTime()-acjkm.startTime)/1000+" secondes<br>";
			if(acjkm.canComputeOldMedianNum)
				acjkm.finalResultOutput.innerHTML+="Old median num total: "+acjkm.oldMedianNum+"<br>";
			acjkm.finalResultOutput.innerHTML+="New median num total: "+acjkm.newMedianNum+"<br>";
			acjkm.finalResultOutput.innerHTML+="Large strokes: "+(acjkm.largeStrokes?acjkm.largeStrokes:"none")+"<br>";
		}
		if (acjkm.saveOn)
		{
			if (acjkm.allJsonLines) acjkm.allJsonLines+="\n";
			acjkm.allJsonLines+=acjkm.newJsonLine;
			if(acjkm.recordType=="jsonFile")
			{
				if (!(acjkm.data.length%100)||(acjkm.data.length==1))
				{
					acjkm.setNewSvgAsJsonFile(acjkm.allJsonLines);
					acjkm.allJsonLines="";
				}
			}
			else if(acjkm.recordType=="svg")
			{
				acjkm.setNewSvgAsSvg(acjkm.allJsonLines);
				acjkm.allJsonLines="";
			}
		}
		acjkm.data=acjkm.data.replace([...acjkm.data][0],"");
		if (acjkm.data&&(acjkm.recordType=="jsonFile"))
		{
			acjkm.board=[];
			acjkm.svg=[];
			acjkm.p1List=[];
			acjkm.getOldSvgAsJsonFile([...acjkm.data][0]);
		}
		else if (!acjkm.data)
		{
			if(acjkm.after) acjkm.after();
		}
		// else data is processed asynchronously
	}
};
// methods of acjkm:
acjkm.xy2k=function(x,y) {return (y*this.d+x)*4;};
acjkm.drift=function(z2,z1,d,t) {return Math.ceil(z2-t/d/2*(z2-z1));};
acjkm.angle=function(A,B,C)
{
	// B is the center point
    var AB=Math.sqrt(Math.pow(B.x-A.x,2)+Math.pow(B.y-A.y,2));    
    var BC=Math.sqrt(Math.pow(B.x-C.x,2)+Math.pow(B.y-C.y,2)); 
    var AC=Math.sqrt(Math.pow(C.x-A.x,2)+Math.pow(C.y-A.y,2));
    return Math.acos((BC*BC+AB*AB-AC*AC)/(2*BC*AB));
};
acjkm.simplify=function(points,tolerance)
{
	// Douglas Peucker algorithm implementation
	var smartLine=function(p1,p2)
	{
		this.p1=p1;
		this.p2=p2;
		this.distanceToPoint=function(point)
		{
			var m,a,b,d,e;
			// slope
			a=this.p2.x-this.p1.x;
			e=1/1024;
			// avoid to divide by 0
			if (Math.abs(a)<e) a=((a<0)?-e:e);
			m=(this.p2.y-this.p1.y)/a;
			// y offset
			b=this.p1.y-(m*this.p1.x);
			d=[];
			// distance to the linear equation
			d.push(Math.abs(point.y-(m*point.x)-b)/Math.sqrt(Math.pow(m,2)+1));
			// distance to p1
			d.push(Math.sqrt(Math.pow((point.x-this.p1.x),2)+Math.pow((point.y-this.p1.y),2)));
			// distance to p2
			d.push(Math.sqrt(Math.pow((point.x-this.p2.x),2)+Math.pow((point.y-this.p2.y),2)));
			// return the smallest distance
			return d.sort(function(a,b) {
				return (a-b); // cause an array to be sorted numerically and ascending
			})[0];
		};
	};
	var douglasPeucker=function(points,tolerance)
	{
		var i,distance,arr,returnPoints,maxDistance,maxDistanceIndex,p;
		if (points.length<=2) return [points[0]];
		returnPoints=[];
		// make line from start to end 
		line=new smartLine(points[0],points[points.length-1]);
		// find the largest distance from intermediate points to this line
		maxDistance=0;
		maxDistanceIndex=0;
		for (i=1;i<=points.length-2;i++)
		{
			distance=line.distanceToPoint(points[i]);
			if (distance>maxDistance)
			{
				maxDistance=distance;
				maxDistanceIndex=i;
			}
		}
		// check if the max distance is greater than tolerance 
		if (maxDistance>=tolerance)
		{
			p=points[maxDistanceIndex];
			line.distanceToPoint(p);
			// include this point in the output 
			returnPoints=returnPoints.concat(douglasPeucker(points.slice(0,maxDistanceIndex+1),tolerance));
			returnPoints=returnPoints.concat(douglasPeucker(points.slice(maxDistanceIndex,points.length),tolerance));
		}
		else
		{
			// exclude this point
			p=points[maxDistanceIndex];
			line.distanceToPoint(p);
			returnPoints=[points[0]];
		}
		return returnPoints;
	};
	arr=douglasPeucker(points,tolerance);
	// always have to push the very last point on so it doesn't get left off
	arr.push(points[points.length-1]);
	return arr;
};
acjkm.buildSvg2=function(a,n)
{
	var u,id,width,height,xmlns,viewBox,s,p1=[];
	u=a.character.charCodeAt(0);
	id="hvg"+u;
	viewBox="viewBox=\"0 0 1024 1024\"";
	xmlns="xmlns=\"http://www.w3.org/2000/svg\" xmlns:xlink=\"http://www.w3.org/1999/xlink\"";
	width=" width=\""+acjkm.d+"px\"";
	height=" height=\""+acjkm.d+"px\"";
	s="<svg id=\""+id+"\" version=\"1.1\" "+width+" "+height+" "+viewBox+" "+xmlns+">\n";
	s+="<g transform=\"scale(1,-1) translate(0,-900)\">\n";
	s+="\t<defs>\n";
	let stroke="",medianStr=a.medians[n].toString();
	// if special, just merge stroke definitions in case of special sibling strokes
	// remember sibling strokes have the same medians
	// do the merge only in the canvas to have a convenient mask for computation
	for(let k=0;k<a.medians.length;k++)
		if(a.medians[k].toString()==medianStr) stroke+=a.strokes[k];
	s+="\t\t<path shape-rendering=\"optimizeSpeed\" id=\""+id+"-def-"+n+"\" d=\""+stroke+"\"/>\n";
	s+="\t</defs>\n";
	s+="\t<use xlink:href=\"#"+id+"-def-"+n+"\"/>\n";
	s+="</g>\n";
	s+="</svg>";
	p1[0]=Math.round(a.medians[n][0][0]*(acjkm.d/1024));
	p1[1]=Math.round((900-a.medians[n][0][1])*(acjkm.d/1024));
	return {s:s,start:p1};
};
acjkm.createCanvas=function(a)
{
	var s="",k,p1,imgs=[],imgd;
	if(acjkm.canvasContainer)
	{
		for (k=0;k<acjkm.numOfStrokes;k++)
		{
			s+="<li class=\"strokeContainer\">";
			s+="<p id=\"result"+k+"\"></p>";
			if (acjkm.strokeOn) s+="<canvas id=\"canvas"+k+"\" width=\""+acjkm.d+"\" height=\""+acjkm.d+"\"></canvas>";
			s+="</li>";
		}
		acjkm.canvasContainer.innerHTML=s;
	}
	for (k=0;k<acjkm.numOfStrokes;k++)
	{
		if(acjkm.canvasContainer)
			document.getElementById("result"+k).innerHTML="Stroke: #"+(k+1)+"<br>";
		if(acjkm.canComputeOldMedianNum)
		{
			acjkm.oldMedianNum+=a.medians[k].length;
			if(acjkm.canvasContainer)
				document.getElementById("result"+k).innerHTML+="Old num of medians: "+a.medians[k].length+"<br>";
		}
		acjkm.board[k]=new acjkm.boardClass(a.character,k);
		if (acjkm.strokeOn&&acjkm.canvasContainer)
			acjkm.board[k].cn=document.getElementById("canvas"+k);
		else
		{
			acjkm.board[k].cn=document.createElement('canvas');
			acjkm.board[k].cn.height=acjkm.d;
			acjkm.board[k].cn.width=acjkm.d;
		}
		acjkm.board[k].cx=acjkm.board[k].cn.getContext('2d');
		acjkm.board[k].img=new Image();
		acjkm.board[k].img.k=k;
		acjkm.board[k].img.onload=function()
		{
			acjkm.board[this.k].cx.drawImage(this,0,0,acjkm.d,acjkm.d);
			imgd=acjkm.board[this.k].cx.getImageData(0,0,acjkm.d,acjkm.d);
			acjkm.board[this.k].bitmap=imgd.data;
			acjkm.board[this.k].createNewSvg(this.k,acjkm.p1List[this.k]);
		};
		acjkm.board[k].img.src="data:image/svg+xml;base64,"+btoa(acjkm.svg[k]);
	}
};
acjkm.getOldSvgAsJsonFile=function(data)
{
	let xhr;
	if (data)
	{
		xhr=new XMLHttpRequest();
		xhr.onreadystatechange=function()
		{
			if ((xhr.readyState==4)&&(xhr.status==200))
			{
				if (!xhr.responseText)
				{
					if(acjkm.errorOutput)
						acjkm.errorOutput.innerHTML+="Error when get "+data;
				}
				else if (xhr.responseText.match(/^Error:/))
				{
					if(acjkm.errorOutput)
						acjkm.errorOutput.innerHTML+=xhr.responseText;
				}
				else
				{
					let a,r,s;
					s=xhr.responseText;
					acjkm.oldJsonLine=s;
					a=JSON.parse(s);
					acjkm.numOfStrokes=a.strokes.length;
					acjkm.counter++;
					if(acjkm.debugOutput)
					{
						let b;
						b="#"+acjkm.counter+" character";
						if(typeof acjkm.source==='string')
							b+=" (read from "+acjkm.source+")";
						b+=": "+data+"<br>";
						b+="Num of strokes: "+a.strokes.length+"<br>";
						b+="Old Json line:<br>"+s+"<br>";
						acjkm.debugOutput.innerHTML=b;
					}
					for (let k=0;k<acjkm.numOfStrokes;k++)
					{
						r=acjkm.buildSvg2(a,k);
						acjkm.svg.push(r.s);
						acjkm.p1List.push(r.start);
					}
					acjkm.createCanvas(a);
				}
			}
		};
		// jsonGetLine.php gets json line of the current character from acjkm.source
		// acjkm.source can be something like "graphicsXxx.txt"
		let path=acjkm.url.pathname.replace("_js/mediansAcjk.js","")+'_php/';
		xhr.open("POST",path+"jsonGetLine.php",true);
		xhr.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
		xhr.send("data="+encodeURIComponent(data)+"&source="+acjkm.source);
	}
	else if(acjkm.errorOutput) acjkm.errorOutput.innerHTML+="Error, no data";
}
acjkm.setNewSvgAsJsonFile=function(data)
{
	var xhr,s,r,a,k,km;
	xhr=new XMLHttpRequest();
	xhr.onreadystatechange=function()
	{
		if (!((xhr.readyState==4)&&(xhr.status==200)))
		{
			if(acjkm.finalResultOutput)
				acjkm.finalResultOutput.innerHTML+="...";
		}
		else
		{
			if(acjkm.finalResultOutput)
				acjkm.finalResultOutput.innerHTML+=xhr.responseText+"<br>";
		}
    };
    // jsonSetLine.php appends new json lines to acjkm.target
    // acjkm.target can be something like "graphicsXxx-new.txt"
	let path=acjkm.url.pathname.replace("_js/mediansAcjk.js","")+'_php/';
	xhr.open("POST",path+"jsonSetLine.php",true);
	xhr.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	xhr.send("data="+encodeURIComponent(data)+"&target="+acjkm.target);
}
acjkm.transformYCoordinate=function(a)
{
	let k,km;
	km=a.strokes.length;
	for(k=0;k<km;k++)
	{
		let m,n,q,s="";
		q=/([MQCL, ]+)([0-9.-]+)[ ,]+([0-9.-]+)/g;
		m=a.strokes[k].matchAll(q);
		for (n of m) s+=n[1]+n[2]+" "+(-n[3]+900);
		a.strokes[k]=s+"Z";
	}
	km=a.medians.length;
	for(k=0;k<km;k++)
	{
		let k2,km2;
		km2=a.medians[k].length;
		for(k2=0;k2<km2;k2++) a.medians[k][k2][1]=-a.medians[k][k2][1]+900;
	}
	return a;
}
acjkm.getOldSvgAsSvg=function(data)
{
	// transform the source svg in a json line like in graphicsXxx.txt files
	let list,a={character:data,strokes:[],medians:[]},k,km,k2,km2,r,s;
	list=acjkm.source.querySelectorAll("path[id]");
	km=list.length;
	for(k=0;k<km;k++) a.strokes[k]=list[k].getAttribute("d");
	list=acjkm.source.querySelectorAll("path[clip-path]");
	km=list.length;
	for(k=0;k<km;k++)
	{
		s=list[k].getAttribute("d");
		s=s.replace(/M/,"[");
		s=s.replaceAll(/L/g,",");
		s=s.replaceAll(/([0-9.-]+)[,\s]([0-9.-]+)/g,"[$1,$2]");
		s+="]";
		a.medians[k]=JSON.parse(s);
	}
	a=acjkm.transformYCoordinate(a);
	s=JSON.stringify(a);
	acjkm.oldJsonLine=s;
	// then process data as getOldSvgAsJsonFile() does
	acjkm.numOfStrokes=a.strokes.length;
	acjkm.counter++;
	if(acjkm.debugOutput)
	{
		let b;
		b="#"+acjkm.counter+" character";
		if(typeof acjkm.source==='string') b+=" (read from "+acjkm.source+")";
		b+=": "+data+"<br>";
		b+="Num of strokes: "+a.strokes.length+"<br>";
		b+="Old Json line:<br>"+s+"<br>";
		acjkm.debugOutput.innerHTML=b;
	}
	for (k=0;k<acjkm.numOfStrokes;k++)
	{
		r=acjkm.buildSvg2(a,k);
		acjkm.svg.push(r.s);
		acjkm.p1List.push(r.start);
	}
	acjkm.createCanvas(a);
}
acjkm.setNewSvgAsSvg=function(data)
{
	// copy the svg from source to target
	let svgCode=acjkm.source.innerHTML;
	let svgId=svgCode.replace(/^[^£]*<svg id="([^"]+)"[^£]*$/,"$1");
	let dec=svgId.replace(/^.*[^0-9]([0-9]+)$/,"$1");
	let a=JSON.parse(data).medians;
	acjkm.target.innerHTML=svgCode.replaceAll(svgId,"z"+dec);
	// replace old medians by new ones
	let list=acjkm.target.querySelectorAll("path[clip-path]");
	let k,km;
	km=list.length;
	for(k=0;k<km;k++)
	{
		let d="",k2,km2=a[k].length;
		// todo: change y coordinates, see also getNewSvgAsSvg()
		for(k2=0;k2<km2;k2++) d+=(!k2?"M":"L")+a[k][k2][0]+" "+(-a[k][k2][1]+900);
		list[k].setAttribute("d",d);
	}
}
acjkm.run=function(p)
{
	// recordType: "jsonFile" (many files) or "svg" (only one svg record), default "jsonFile"
	// if (recordType=="jsonFile") source and target should be graphics txt files
	// if (recordType=="svg") source and target should be html elements
	if(p.recordType) acjkm.recordType=p.recordType; // optional
	else acjkm.recordType="jsonFile";
	// data:
	//   if (recordType=="svg") only one character
	//   if (recordType=="jsonFile") a string of characters
	if(p.data) acjkm.data=p.data; // required
	else {console.log("Error: no data provided to acjkm!");return;}
	// source:
	//   if (recordType=="svg") a html element (not the svg itself) than contains a svg
	//   if (recordType=="jsonFile") a graphics txt file name such graphicsXxx.txt
	if(p.source) acjkm.source=p.source; // required
	else {console.log("Error: no source provided to acjkm!");return;}
	// target:
	//   if (recordType=="svg") a html element than can contain a svg
	//   if (recordType=="jsonFile") a graphics txt file name such graphicsXxx-new.txt
	if(p.target) acjkm.target=p.target; // optional
	else acjkm.target=null;
	if(p.errorOutput) acjkm.errorOutput=p.errorOutput; // optional
	else acjkm.errorOutput=null;
	if(p.strangeStrokesOutput) acjkm.strangeStrokesOutput=p.strangeStrokesOutput; // optional
	else acjkm.strangeStrokesOutput=null;
	if(p.debugOutput) acjkm.debugOutput=p.debugOutput; // optional
	else acjkm.debugOutput=null;
	if(p.canvasContainer) acjkm.canvasContainer=p.canvasContainer; // optional
	else acjkm.canvasContainer=null;
	if(p.finalResultOutput) acjkm.finalResultOutput=p.finalResultOutput; // optional
	else acjkm.finalResultOutput=null;
	if(acjkm.errorOutput) acjkm.errorOutput.innerHTML="";
	if(acjkm.strangeStrokesOutput) acjkm.strangeStrokesOutput.innerHTML="";
	if(acjkm.debugOutput) acjkm.debugOutput.innerHTML="";
	if(acjkm.canvasContainer) acjkm.canvasContainer.innerHTML="";
	if(acjkm.finalResultOutput) acjkm.finalResultOutput.innerHTML="";
	acjkm.saveOn=p.saveOn&&p.target&&p.recordType; // optional
	acjkm.strokeOn=p.strokeOn; // optional
	acjkm.originOn=p.originOn; // optional
	acjkm.extremaOn=p.extremaOn; // optional
	acjkm.startPointOn=p.startPointOn; // optional
	acjkm.startContourOn=p.startContourOn; // optional
	acjkm.fullContourOn=p.fullContourOn; // optional
	acjkm.edge2edgeLeftOn=p.edge2edgeLeftOn; // optional
	acjkm.edge2edgeRightOn=p.edge2edgeRightOn; // optional
	acjkm.mediansOn=p.mediansOn; // optional
	if(p.step) acjkm.step=p.step; // optional
	else acjkm.step=14;
	if(p.stepMax) acjkm.stepMax=p.stepMax; // optional
	else acjkm.stepMax=42;
	if(p.simplificationThreshold) acjkm.simplificationThreshold=p.simplificationThreshold; // optional
	else acjkm.simplificationThreshold=14;
	if(p.largeThreshold) acjkm.largeThreshold=p.largeThreshold; // optional
	else acjkm.largeThreshold=108;
	acjkm.disLargeThreshold=acjkm.largeThreshold*acjkm.largeThreshold;
	if(p.abnormal) acjkm.abnormal=p.abnormal; // optional
	else acjkm.abnormal=180;
	acjkm.disAbnormal=acjkm.abnormal*acjkm.abnormal;
	if(p.after) acjkm.after=p.after; // optional
	if(p.canComputeOldMedianNum) acjkm.canComputeOldMedianNum=p.canComputeOldMedianNum; // optional
	acjkm.allJsonLines="";
	acjkm.svg=[];
	acjkm.board=[];
	acjkm.p1List=[];
	if(acjkm.canComputeOldMedianNum) acjkm.oldMedianNum=0;
	acjkm.newMedianNum=0;
	acjkm.largeStrokes="";
	acjkm.strangeStrokes="";
	acjkm.disMax=0;
	acjkm.counter=0;
	acjkm.startTime=new Date().getTime();
	if (acjkm.data)
	{
		acjkm.data=acjkm.data.replace(/\s/g,"");
		if(acjkm.recordType=="svg") acjkm.getOldSvgAsSvg([...acjkm.data][0]);
		else acjkm.getOldSvgAsJsonFile([...acjkm.data][0]);
	}
	else if(acjkm.errorOutput) acjkm.errorOutput.innerHTML="No data to process!";
};