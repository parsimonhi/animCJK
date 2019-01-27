if (typeof debug=='undefined') debug=0;

SVGPathElement.prototype.getPointAtLengthForAcjk = function(k) {
	if (!this.gpal) this.gpal=[];
	if (!this.gpal[k]) this.gpal[k]=this.getPointAtLength(k);
	return this.gpal[k];
}
SVGPathElement.prototype.getTotalLengthForAcjk = function(k) {
	if (!this.gtl) this.gtl=Math.floor(this.getTotalLength());
	return this.gtl;
}
function getPathIdFromClipPathAttribute(e)
{
	return e.getAttribute("clip-path").replace(/^.*(z[0-9-]+)c(.*)\)$/,"$1d$2");
}
function getLengthAtNode(e,p,sign)
{
	var k,k2=0,len,p1,p2,x,y;
	len=e.getTotalLengthForAcjk();
	p1=p;
	z=len>>2;
	if (sign=="-")
	{
		k2=0;
		while(z)
		{
			for (k=k2;k<=len;k+=z)
			{
				p2=e.getPointAtLengthForAcjk(k);
				if ((p1.x>(p2.x-z))&&(p1.x<(p2.x+z))&&(p1.y>(p2.y-z))&&(p1.y<(p2.y+z))) break;
			}
			k2=k;
			z=z>>1;
		}
	}
	else
	{
		k2=len;
		while(z)
		{
			for (k=k2;k>=0;k-=z)
			{
				p2=e.getPointAtLengthForAcjk(k);
				if ((p1.x>(p2.x-z))&&(p1.x<(p2.x+z))&&(p1.y>(p2.y-z))&&(p1.y<(p2.y+z))) break;
			}
			k2=k;
			z=z>>1;
		}
	}
	if (debug) debug("in getLengthAtNode id="+e.id+" k="+k+"<br>",1);
	return k;
}
function getAnotherPointOnPath(eBrush,p1,sign)
{
	var k,len,n=5,p2;
	len=eBrush.getTotalLengthForAcjk();
	k=getLengthAtNode(eBrush,p1,sign);
	if (sign=="-")
	{
		if (k>=n) p2=eBrush.getPointAtLengthForAcjk(k-n);
		else p2=eBrush.getPointAtLengthForAcjk(k-n+len);
	}
	else
	{
		if ((len-k)>=n) p2=eBrush.getPointAtLengthForAcjk(k+n);
		else p2=eBrush.getPointAtLengthForAcjk(k+n-len);
	}
	p2.x=Math.round(p2.x);
	p2.y=Math.round(p2.y);
	if (debug) debug("getAnotherPointOnPath x1="+p1.x+" y1="+p1.y+" k="+Math.round(k)+" len="+len+" x2="+p2.x+" y2="+p2.y+"<br>",1);
	return p2;
}
function cleanPathBeforeBrushing(d)
{
	var q,k,kmax,m;
	// clean the path
	d=d.replace(/[,\s]/g," ");
	d=d.replace(/([0-9])-/g,"$1 -");
	d=d.replace(/\.[0-9]+/g,"");
	d=d.replace(/z/,"Z");
	d=d.replace(/\s?([MQCLVHZ])\s?/g,"$1");
	// add C if omitted
	q=/(C([0-9-]+\s){5}[0-9-]+)\s/;
	while (d.match(q)) d=d.replace(q,"$1C");
	// add Q if omitted
	q=/(Q([0-9-]+\s){3}[0-9-]+)\s/;
	while (d.match(q)) d=d.replace(q,"$1Q");
	// add V if omitted
	q=/(V[0-9-]+)\s/;
	while (d.match(q)) d=d.replace(q,"$1V");
	// add H if omitted
	q=/(H[0-9-]+)\s/;
	while (d.match(q)) d=d.replace(q,"$1H");
	// transform V in L
	q=/([0-9-]+)(\s[0-9-]+)V([0-9-]+)/;
	while (d.match(q)) d=d.replace(q,"$1$2L$1 $3");
	// transform H in L
	q=/([0-9-]+\s)([0-9-]+)H([0-9-]+)/;
	while (d.match(q)) d=d.replace(q,"$1$2L$3 $2");
	// add L if omitted
	q=/([ML][0-9-]+\s[0-9-]+)\s/;
	while (d.match(q)) d=d.replace(q,"$1L");
	// if start point is different from end point, add a L at end
	m=d.match(/^M([0-9-]+) ([0-9-]+).*[^0-9-]([0-9-]+) ([0-9-]+)Z$/);
	if (m&&((m[1]!=m[3])||(m[2]!=m[4]))) d=d.replace(/Z$/,"L"+m[1]+" "+m[2]+"Z");
	// while there is a L just after M, move it to end
	kmax=9; // just in case
	k=0;
	while ((k<kmax)&&(m=d.match(/^M([0-9 -]+)L([0-9 -]+)(.*)Z$/)))
	{
		k++;
		d=d.replace(/^M([0-9 -]+)L([0-9 -]+)(.*)Z$/,"M"+m[2]+m[3]+"L"+m[2]+"Z");
	}
	return d;
}
function isNodeInStroke(e,x,y)
{
	var q=new RegExp(x+" "+y+"[MLQCZ]");
	return e.getAttribute("d").match(q);
}
function isPointInStroke(e,x,y)
{
	// rarely used
	if (debug) debug("in isPointInStroke "+x+" "+y+" in "+e.getAttribute("id")+"<br>",1);
	var len=e.getTotalLengthForAcjk();
	var k,p,z=3;
	x=parseInt(x);
	y=parseInt(y);
	for (k=0;k<=len;k=k+z) // todo: dichotomia here? or accelerator?
	{
		p=e.getPointAtLengthForAcjk(k);
		if ((x>(p.x-z))&&(x<(p.x+z))&&(y>(p.y-z))&&(y<(p.y+z)))
		{
			if (debug) debug("in isPointInStroke, found "+x+" "+y+" at "+k+" in "+e.getAttribute("id")+"<br>",1);
			return k;
		}
	}
	return -1;
}
function isNodeElsewhere(target,idBrush,x,y)
{
	// check if a point is in another stroke
	var list=target.querySelectorAll("svg.acjk path[id]");
	var k,km,n1,m1,n2,m2,id;
	n1=idBrush.replace(/^(z[0-9-]+)d[0-9abcd]+$/,"$1");
	m1=idBrush.replace(/^z[0-9-]+d([0-9abcd]+)$/,"$1");
	km=list.length;
	for (k=0;k<km;k++)
	{
		id=list[k].getAttribute("id");
		n2=id.replace(/^(z[0-9-]+)d[0-9abcd]+$/,"$1");
		m2=id.replace(/^z[0-9-]+d([0-9abcd]+)$/,"$1");
		if ((n1==n2)&&(m1!=m2)&&isNodeInStroke(list[k],x,y)) return 1;
	}
	return 0;
}
function isPointElsewhere(target,idBrush,x,y)
{
	// check if a point is in another stroke
	var list=target.querySelectorAll("svg.acjk path[id]");
	var k,km,n1,m1,n2,m2,id;
	n1=idBrush.replace(/^(z[0-9-]+)d[0-9abcd]+$/,"$1");
	m1=idBrush.replace(/^z[0-9-]+d([0-9abcd]+)$/,"$1");
	km=list.length;
	for (k=0;k<km;k++)
	{
		id=list[k].getAttribute("id");
		n2=id.replace(/^(z[0-9-]+)d[0-9abcd]+$/,"$1");
		m2=id.replace(/^z[0-9-]+d([0-9abcd]+)$/,"$1");
		if ((n1==n2)&&(m1!=m2)&&(isPointInStroke(list[k],x,y)>=0)) return 1;
	}
	return 0;
}
function getAngle(x1,y1,x2,y2,x3,y3)
{
    var angle1=Math.atan2(y1-y2,x1-x2);
    var angle2=Math.atan2(y3-y2,x3-x2);
    var angle=Math.abs(angle1-angle2)*180/Math.PI;
    if (angle>180) angle=360-angle;
    return angle;
}
function isNodesElsewhere(target,id1,x1,y1,x2,y2)
{
	// check if (x1,y1) and (x2,y2) are both nodes of another stroke
	var list=target.querySelectorAll("svg.acjk path[id]");
	var k,km,n1,m1,n2,m2,id2,d,q1,q2;
	n1=id1.replace(/^(z[0-9-]+)d[0-9abcd]+$/,"$1");
	m1=id1.replace(/^z[0-9-]+d([0-9abcd]+)$/,"$1");
	km=list.length;
	if (debug) debug(id1+" in isNodesElsewhere<br>",1);
	for (k=0;k<km;k++)
	{
		id2=list[k].getAttribute("id");
		n2=id2.replace(/^(z[0-9-]+)d[0-9abcd]+$/,"$1");
		m2=id2.replace(/^z[0-9-]+d([0-9abcd]+)$/,"$1");
		if ((n1==n2)&&(m1!=m2)) // same character, different stroke
		{
			d=list[k].getAttribute("d");
			q1=new RegExp(x1+" "+y1+"(L|(Q[0-9-]+ [0-9-]+ )|(C[0-9-]+ [0-9-]+ [0-9-]+ [0-9-]+ ))");
			q2=new RegExp(x2+" "+y2+"(L|(Q[0-9-]+ [0-9-]+ )|(C[0-9-]+ [0-9-]+ [0-9-]+ [0-9-]+ ))");
			if (d.match(q1)&&d.match(q2))
			{
				if (debug) debug(id1+" Nodes elsewhere found in "+id2+"<br>",1);
				return 1;
			}
		}
	}
	if (debug) debug(id1+" Nodes elsewhere not found<br>",1);
	return 0;
}
function isSegmentElsewhere(target,id1,x1,y1,x2,y2)
{
	// check if (x1,y1,x2,y2) is in another stroke
	// could be "x1 y1 L x2 y2" or "x1 y1 Q ... x2 y2" or "x1 y1 C ... x2 y2"
	var list=target.querySelectorAll("svg.acjk path[id]");
	var k,km,n1,m1,n2,m2,id2,d,q;
	n1=id1.replace(/^(z[0-9-]+)d[0-9abcd]+$/,"$1");
	m1=id1.replace(/^z[0-9-]+d([0-9abcd]+)$/,"$1");
	km=list.length;
	for (k=0;k<km;k++)
	{
		id2=list[k].getAttribute("id");
		n2=id2.replace(/^(z[0-9-]+)d[0-9abcd]+$/,"$1");
		m2=id2.replace(/^z[0-9-]+d([0-9abcd]+)$/,"$1");
		if ((n1==n2)&&(m1!=m2)) // same character, different stroke
		{
			d=list[k].getAttribute("d");
			q=new RegExp(x1+" "+y1+'(L|(Q[0-9-]+ [0-9-]+ )|(C[0-9-]+ [0-9-]+ [0-9-]+ [0-9-]+ ))'+x2+" "+y2);
			if (d.match(q)) return 1;
			q=new RegExp(x2+" "+y2+'(L|(Q[0-9-]+ [0-9-]+ )|(C[0-9-]+ [0-9-]+ [0-9-]+ [0-9-]+ ))'+x1+" "+y1);
			if (d.match(q)) return 1;
		}
	}
	return 0;
}
function whichSegmentElsewhere(target,id1,x1,y1,x2,y2)
{
	// look if curve from (x1,y1) to (x2,y2) is in another stroke, and what is its kind
	// return 0 if not elsewhere
	// return 1 if bridge
	// return 2 if clip
	if (isSegmentElsewhere(target,id1,x1,y1,x2,y2))
	{
		if (debug) debug(id1+" Segment elsewhere "+x1+" "+y1+" "+x2+" "+y2+"<br>",1);
		return 2; // (x1,y1) and (x2,y2) are adjacent nodes => clip
	}
	else if (isNodesElsewhere(target,id1,x1,y1,x2,y2))
	{
		if (debug) debug(id1+" Nodes elsewhere "+x1+" "+y1+" "+x2+" "+y2+"<br>",1);
		return 1; // (x1,y1) and (x2,y2) are not adjacent => bridge
	}
	// one of the two nodes not found, thus look for points
	var list=target.querySelectorAll("svg.acjk path[id]");
	var k,km,n1,m1,n2,m2,id2,d,q,k1,k2,k3,ktot,delta,ratio;
	n1=id1.replace(/^(z[0-9-]+)d[0-9abcd]+$/,"$1");
	m1=id1.replace(/^z[0-9-]+d([0-9abcd]+)$/,"$1");
	km=list.length;
	for (k=0;k<km;k++)
	{
		id2=list[k].getAttribute("id");
		n2=id2.replace(/^(z[0-9-]+)d[0-9abcd]+$/,"$1");
		m2=id2.replace(/^z[0-9-]+d([0-9abcd]+)$/,"$1");
		if ((n1==n2)&&(m1!=m2)) // same character, different stroke
		{
			if (((k1=isPointInStroke(list[k],x1,y1))>=0)&&((k2=isPointInStroke(list[k],x2,y2))>=0))
			{
				ktot=list[k].getTotalLengthForAcjk();
				if (k1>k2) {k3=k2;k2=k1;k1=k3;}
				delta=Math.min(k2-k1,ktot-k2+k1);
				ratio=delta/ktot;
				// a small delta indicates that (x1,y1) and (x2,y2) are too closed together
				// thus probable disturbing node thus return 0
				// a big ratio indicates that (x1,y1) and (x2,y2) are not closed together
				// thus probable bridge thus return 1
				// otherwise probable clip thus return 2
				if (debug) debug(id1+" whichSegmentElsewhere "+k1+" "+k2+" "+ktot+" "+delta+" "+ratio+"<br>",1);
				if (delta<3) return 0;
				if (ratio>0.25) return 1;
				return 2;
			}
		}
	}
	return 0;
}
function isFlat(x0,y0,x1,y1,x4,y4,x5,y5,am)
{
	var a1,a2;
	a1=getAngle(x0,y0,x1,y1,x4,y4);
	a2=getAngle(x1,y1,x4,y4,x5,y5);
	if (debug) debug("in isFlat x1="+x1+" y1="+y1+" x4="+x4+" y4="+y4+" a1="+Math.round(a1)+" a2="+Math.round(a2)+"<br>",1);
	return (a1>am)&&(a2>am);
}
function isHalfFlat(x0,y0,x1,y1,x4,y4,x5,y5,am)
{
	var a1,a2;
	a1=getAngle(x0,y0,x1,y1,x4,y4);
	a2=getAngle(x1,y1,x4,y4,x5,y5);
	if (debug) debug("in isHalfFlat "+Math.round(a1)+" "+Math.round(a2)+"<br>",1);
	return (a1>am)||(a2>am);
}
function isDoubleClip(target,id,d,x1,y1,x2,y2)
{
	// assume curve from (x1,y1) to (x2,y2) is a clip
	var m;
	q=new RegExp(x1+' '+y1+'L'+x2+' '+y2+'L([0-9-]+) ([0-9-]+)');
	if (m=d.match(q))
	{
		x3=m[1];
		y3=m[2];
		if (whichSegmentElsewhere(target,id,x2,y2,x3,y3)==2)
			return m;
	}
	return 0;
}
function strokeBrushing(eBrush)
{
	// eBrush: HTML element not necessarily in the DOM that is the path to brush
	var idBrush=eBrush.id;
	var target=eBrush.parentNode;
	var dBrush=eBrush.getAttribute("d");
	var qBrush,mBrush,mBrush2,dSeg,dseg1,dseg2;
	var x0,y0,x1,y1,x2,y2,x3,y3,x4,y4,x5,y5,x6,y6,x7,y7,x8,y8,x4ex,y4ex,xxx,yy;
	var p0,p3,p5,p8;
	var segmentElsewhere,doubleClipEffect,hasToCurve;
	// replace each straight line L by a cubic bezier curve C
	if (debug) debug("<br>strokeBrushing "+idBrush+" "+dBrush+"<br>",1);
	while (mBrush=dBrush.match(/([0-9-]+) ([0-9-]+)L([0-9-]+) ([0-9-]+)/))
	{
			// dSeg is a fraction of the length of L
			// (x1,y1) is the start point of C
			// (x2,y2) is the first handle of C, on the tangent at (x1,y1) at dSeg from (x1,y1)
			// (x3,y3) is the second handle of C, on the tangent at (x4,y4) at dSeg from (x4,y4)
			// (x4,y4) is the end point of C
			// ((x0,y0),(x1,y1)) define the tangent at (x1,y1)
			// ((x4,y4),(x5,y5)) define the tangent at (x4,y4)
			x1=parseInt(mBrush[1]);
			y1=parseInt(mBrush[2]);
			x4=parseInt(mBrush[3]);
			y4=parseInt(mBrush[4]);
			// try to find convenient tangents at (x1,y1) and (x4,y4)
			p0=getAnotherPointOnPath(eBrush,{x:x1,y:y1},"-");
			p5=getAnotherPointOnPath(eBrush,{x:x4,y:y4},"+");
			x0=Math.round(p0.x);
			y0=Math.round(p0.y);
			x5=Math.round(p5.x);
			y5=Math.round(p5.y);
			hasToCurve=0;
			doubleClipEffect=0;
			if (!isFlat(x0,y0,x1,y1,x4,y4,x5,y5,165))
			{
				if (debug) debug(idBrush+" not flat "+x1+" "+y1+" "+x4+" "+y4+"<br>",1);
				segmentElsewhere=whichSegmentElsewhere(target,idBrush,x1,y1,x4,y4);
				if ((segmentElsewhere==2)
					&&(mBrush2=isDoubleClip(target,idBrush,dBrush,x1,y1,x4,y4)))
				{
					// two consecutive clips
					p3=getAnotherPointOnPath(eBrush,{x:x4,y:y4},"-");
					x3=Math.round(p3.x);
					y3=Math.round(p3.y);
					x7=parseInt(mBrush2[1]);
					y7=parseInt(mBrush2[2]);
					p8=getAnotherPointOnPath(eBrush,{x:x7,y:y7},"+");
					x8=Math.round(p8.x);
					y8=Math.round(p8.y);
					if (debug) debug(idBrush+" double clip "+x1+" "+y1+" "+x4+" "+y4+" "+x7+" "+y7+"<br>",1);
					// double clip as in 木
					// remove the middle node
					// curve the resulting line
					hasToCurve=1;
					dSeg1=Math.sqrt((x1-x4)*(x1-x4)+(y1-y4)*(y1-y4));
					dSeg2=Math.sqrt((x4-x7)*(x4-x7)+(y4-y7)*(y4-y7));
					// if nearly flat double clip, no double clip effect such as in 彖
					if (getAngle(x1,y1,x4,y4,x7,y7)>165) doubleClipEffect=0;
					// if very small segment, no double clip effect
					else if ((dSeg1<3)||(dSeg2<3)) doubleClipEffect=0;
					else doubleClipEffect=1;
					// remove (x4,y4)
					if (debug) debug(idBrush+" double clip="+doubleClipEffect+" remove "+x4+" "+y4+"<br>",1);
					qBrush=new RegExp('L'+x4+' '+y4);
					dBrush=dBrush.replace(qBrush,"");
					x4ex=x4;
					y4ex=y4;
					x4=x7;
					y4=y7;
					x5=x8;
					y5=y8;
				}
				else
				{
					if (segmentElsewhere==2)
					{
						if (debug) debug(idBrush+" single clip "+x1+" "+y1+" "+x4+" "+y4+"<br>",1);
						// single clip
						// just curve the line
						hasToCurve=1;
					}
					else if (segmentElsewhere==1)
					{
						if (debug) debug(idBrush+" bridge "+x1+" "+y1+" "+x4+" "+y4+"<br>",1);
						// for instance 2nd stroke of 女 as bottom component (妻委姿...)
						// just curve the line
						hasToCurve=1;
					}
				}
			}
			else if (debug) debug(idBrush+" flat "+x1+" "+y1+" "+x4+" "+y4+"<br>",1);
			if (hasToCurve)
			{
				// replace L by a C
				dSeg=Math.sqrt((x1-x4)*(x1-x4)+(y1-y4)*(y1-y4));
				if (doubleClipEffect) dSeg=0.8*dSeg*(180-getAngle(x1,y1,x4ex,y4ex,x4,y4))/90;
				else dSeg=dSeg/3;
				// compute first handle of the cubic Bezier curve
				if (x0==x1) // vertical line
				{
					x2=x1;
					if (y0>y1) y2=y1-dSeg;
					else y2=y1+dSeg;
				}
				else
				{
					xx=Math.sqrt(dSeg*dSeg*(x1-x0)*(x1-x0)/((y1-y0)*(y1-y0)+(x1-x0)*(x1-x0)));
					if (x0>x1) x2=x1-xx;
					else x2=x1+xx;
					yy=(y1-y0)*(x2-x1)/(x1-x0);
					y2=y1+yy;
				}
				// compute second handle of the cubic Bezier curve
				if (x4==x5) // vertical line
				{
					x3=x4;
					if (y5>y4) y3=y4-dSeg;
					else y3=y4+dSeg;
				}
				else
				{
					xx=Math.sqrt(dSeg*dSeg*(x4-x5)*(x4-x5)/((y4-y5)*(y4-y5)+(x4-x5)*(x4-x5)));
					if (x5>x4) x3=x4-xx;
					else x3=x4+xx;
					yy=(y4-y5)*(x3-x4)/(x4-x5);
					y3=y4+yy;
				}
			}
			else
			{
				// replace L by a "flat" C
				x2=(x1+x4)/2+((x1+x4)/2-x1)/3;
				y2=(y1+y4)/2+((y1+y4)/2-y1)/3;
				x3=(x1+x4)/2+(x4-(x1+x4)/2)/3;
				y3=(y1+y4)/2+(y4-(y1+y4)/2)/3;
			}
			// update path
			x2=Math.round(x2);
			y2=Math.round(y2);
			x3=Math.round(x3);
			y3=Math.round(y3);
			x4=Math.round(x4);
			y4=Math.round(y4);
			dBrush=dBrush.replace(/L([0-9-]+) ([0-9-]+)/,"C"+x2+" "+y2+" "+x3+" "+y3+" "+x4+" "+y4);
	}
	if (debug) debug(idBrush+" "+dBrush+"<br>",1);
	return dBrush;
}