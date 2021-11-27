<!doctype html>
<?php include "minimal.php";?>
<html lang="<?php echo $lang;?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="initial-scale=1.0,user-scalable=yes">
<meta name="description" content="Demo to show AnimCJK SVG
representing Japanese or Chinese characters,
display each stroke in a different random color">
<link rel="stylesheet" href="_css/minimal.css" type="text/css">
<title>AnimCJK - Rainbow</title>
<style>
#codeTag
{
	border:1px solid #000;
	padding:1em;
	margin:1em;
	text-align:left;
	font-family:monospace;
	white-space:pre;
	overflow:auto;
}
</style>
</head>
<body>
<?php displayHeader("AnimCJK - Rainbow");?>
<p>
<label>Colored/uncolored strokes with random colors on the fly:
<input id="colorize" checked type="checkbox" onclick="switchColorize();restartAnime();">
</label>
</p>
<p>
<label>Add/remove stroke numbers on the fly:
<input id="number" checked type="checkbox" onclick="switchNumber();restartAnime();">
</label>
</p>
<button class="actionBtn" type="button" onclick="restartAnime()">Animate</button>
<div id="charDiv">
<?php
$s=file_get_contents("../".$dir."/".$dec.".svg");
// replace native css from svg file (since in this sample, there will be no animation)
$a="<style>\n";
$a.="@keyframes zk {\n";
$a.="to {\n";
$a.="stroke-dashoffset:0;\n";
$a.="}\n";
$a.="}\n";
$a.="@keyframes zz {\n";
$a.="to {\n";
$a.="opacity:1;\n";
$a.="}\n";
$a.="}\n";
$a.="svg.acjk path[clip-path] {\n";
$a.="--t:0.8s;\n";
$a.="animation:zk var(--t) linear forwards var(--d);\n";
$a.="stroke-dasharray:3337;\n";
$a.="stroke-dashoffset:3339;\n";
$a.="stroke-width:128;\n";
$a.="stroke-linecap:round;\n";
$a.="fill:none;\n";
$a.="}\n";
$a.="svg.acjk path[id] {fill:#ccc;}\n";
$a.="svg.acjk circle, svg.acjk text {\n";
$a.="opacity:0;\n";
$a.="animation:zz 0s linear forwards var(--d);\n";
$a.="}\n";
$a.="svg.acjk text {\n";
$a.="text-anchor:middle;\n";
$a.="font-family:arial,sans-serif;\n";
$a.="font-weight:normal;\n";
$a.="}\n";
$a.="</style>";
$s=preg_replace("/<style>[\s\S]*<.style>/",$a,$s);
echo $s;
?>
</div>
<h2>SVG Code</h2>
<div id="codeTag">
</div>
<?php echo displayFooter("rainbow");?>
<script src="_js/asvg.js"></script>
<script>
// "randomize" script
function oneRandom(rgb,m)
{
	var y=223,z=95;
	if (m>2)
	{
		m=2-(m-3);
		if (m==rgb) return Math.random()*z;
		return Math.random()*(y-z)+z;
	}
	if (m!=rgb) return Math.random()*z;
	return Math.random()*(y-z)+z;
}
function randomColor(k,km)
{
	var r,g,b,m;
	m=k%6;
	r=Math.floor(oneRandom(0,m));
	g=Math.floor(oneRandom(1,m));
	b=Math.floor(oneRandom(2,m));
	return "rgb("+r+","+g+","+b+")";
}
function colorizeStrokes(x)
{
	var k,km,list,c;
	list=document.querySelectorAll("svg.acjk path:not([id])");
	km=list.length;
	if (x)
		for (k=0;k<km;k++)
		{
			c=randomColor(k,km);
			list[k].setAttributeNS(null,"stroke",c);
		}
	else for (k=0;k<km;k++)
	{
		list[k].setAttributeNS(null,"stroke","#000");
	}
	updateCode();
}
function switchColorize()
{
	colorizeStrokes(document.getElementById("colorize").checked);
	if (document.getElementById("number").checked)
	{
		setNumber(0);
		setNumber(1);
	}
}
window.addEventListener("load",function(){colorizeStrokes(1);},false);
function setNumber(x)
{
	// this function adds/removes stroke numbers
	var go,g,list,k,km,l,a,c,e,cx,cy,cx1,cy1,cx2,cy2,d,sx,sy,fs=40,list0,km0;
	if (x)
	{
		// add numbers
		list0=document.querySelectorAll("svg.acjk path[id]");
		km0=list0.length;
		list=document.querySelectorAll("svg.acjk path:not([id])");
		km=list.length;
		l=0;
		go=0;
		for (k=0;k<km;k++)
		{
			// since several character svg can be in the page,
			// do not set g outside the loop
			g=list[k];
			while (g.tagName!="svg") g=g.parentNode;
			if (g!=go) {l=0;go=g;}
			// safer to test id of list0 than clip-path url of list for normal browsers
			// clip-path attributes were removed for pitiful browsers
			// so use list0 id to get the number of the path
			if (list0[k].getAttributeNS(null,'id').match(/d[0-9a]+/))
			{
				l++;
				if (list[k].hasAttributeNS(null,"data-median"))
					a=list[k].getAttributeNS(null,"data-median");
				else a=list[k].getAttributeNS(null,"d");
				a=a.replace(/([0-9])[-]/g,"$1 -");
				c=a.match(/M[ ]*([0-9.-]+)[ ,]+([0-9.-]+)[^0-9.-]+([0-9.-]+)[ ,]+([0-9.-]+)/);
				if (c&&c.length)
				{
					cx1=parseInt(c[1]);
					cy1=parseInt(c[2]);
					cx2=parseInt(c[3]);
					cy2=parseInt(c[4]);
					d=Math.sqrt((cy2-cy1)*(cy2-cy1)+(cx2-cx1)*(cx2-cx1));
					if (d)
					{
						cx=cx1+(cx2-cx1)*fs/d/2;
						cy=cy1+(cy2-cy1)*fs/d/2;
					}
					else
					{
						cx=cx1;
						cy=cy1;
					}
					if (cx<(fs+(fs>>3))) cx=fs+(fs>>3);
					if (cy<(fs+(fs>>3))) cy=fs+(fs>>3);
					sx=((k+1)>=10)?0.875:1;
					sy=-1;
					e=document.createElementNS('http://www.w3.org/2000/svg','circle');
					e.setAttributeNS(null,"cx",Math.round(cx));
					e.setAttributeNS(null,"cy",Math.round(cy));
					e.setAttributeNS(null,"r",Math.round(fs));
					e.setAttributeNS(null,"stroke",list[k].getAttributeNS(null,"stroke"));
					e.setAttributeNS(null,"fill","#fff");
					e.setAttributeNS(null,"stroke-width",Math.max(1,fs>>3));
					e.style="--d:"+l+"s";
					g.appendChild(e);
					e=document.createElementNS('http://www.w3.org/2000/svg','text');
					e.setAttributeNS(null,"x",Math.round(cx));
					e.setAttributeNS(null,"y",Math.round(cy+(fs>>1)));
					e.setAttributeNS(null,"fill",list[k].getAttributeNS(null,"stroke"));
					e.setAttributeNS(null,"font-size",(fs>>1)*3);
					e.style="--d:"+l+"s";
					e.textContent=l;
					g.appendChild(e);
				}
			}
		}
	}
	else
	{
		// remove numbers
		list=document.querySelectorAll("svg.acjk circle, svg.acjk text");
		km=list.length;
		if (km) 
			for (k=0;k<km;k++)
			{
				g=list[k].parentNode; // must set g here
				g.removeChild(list[k]);
			}
	}
	updateCode();
}
function switchNumber()
{
	setNumber(document.getElementById("number").checked);
}
window.addEventListener("load",function(){setNumber(1);},false);
function forceReflow()
{
	// normal browsers
	// force a reflow to restart animation
	var e,s;
	e=document.querySelector("svg.acjk");
	s=e.innerHTML;
	e.innerHTML="";
	e.innerHTML=s;
}
function restartAnime()
{
	if (asvg.activated>0) {asvg.run('one');return;} // pitiful browser
	forceReflow(); // normal browser
}
window.addEventListener("load",function(){asvg.run('one');},false); // pitiful browser
function updateCode()
{
	var e,s;
	e=document.getElementById("charDiv");
	s=e.innerHTML;
	s=s.replace(/((<\/circle>)|(<\/text>))/g,"$1\n");
	s=s.replace(/&/g,"&amp;").replace(/</g,"&lt;").replace(/>/g,"&gt;");
	document.getElementById("codeTag").innerHTML=s;
}
</script>
</body>
</html>
