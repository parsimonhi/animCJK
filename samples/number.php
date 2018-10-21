<!doctype html>
<?php include "minimal.php";?>
<html lang="<?php echo $lang;?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="initial-scale=1.0,user-scalable=yes">
<style>
body {text-align:center;}
a {color:#000;}
a:visited {color:#666;}
#charDiv
{
	margin:0 auto 0.5em auto;
	max-width:256px;
	max-height:256px;
	border:1px solid #ccc;
}
</style>
<title>AnimCJK - Number</title>
</head>
<body>
<?php displayHeader("AnimCJK - Number");?>
<p>
<label>Add/remove stroke numbers on the fly:
<input id="number" checked type="checkbox" onclick="switchNumber()">
</label>
</p>
<div id="charDiv">
<?php include "../".$dir."/".$dec.".svg";?>
</div>
<?php echo displayFooter("number");?>
<script>
// "set number" script
function removeAnimationAndSetColorToBlack()
{
	// by default, a character is animated
	// this function removes animation
	// by default, a character stroke color is grey
	// this function changes the color to black
	var k,km,list;
	list=document.querySelectorAll("svg.acjk path[clip-path]");
	km=list.length;
	for (k=0;k<km;k++)
	{
		list[k].style.animation="none";
		list[k].style.stroke="#000";
	}
}
function setNumber(x)
{
	// this function adds/removes stroke numbers
	var go,g,list,k,km,l,a,c,e,cx,cy,cx1,cy1,cx2,cy2,d,sx,sy,fs=40;
	if (x==2) removeAnimationAndSetColorToBlack();
	if (x)
	{
		// add numbers
		list=document.querySelectorAll("svg.acjk path[clip-path]");
		km=list.length;
		l=0;
		go=0;
		for (k=0;k<km;k++)
		{
			// several character svg can be in the page, do not set g outside the loop
			g=list[k];
			while (g.tagName!="svg") g=g.parentNode;
			if (g!=go) {l=0;go=g;}
			if (list[k].getAttribute('clip-path').match(/[0-9a]\)/))
			{
				l++;
				a=list[k].getAttribute("d");
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
					e.setAttribute("cx",cx);
					e.setAttribute("cy",cy);
					e.setAttribute("r",fs);
					e.setAttribute("stroke","#000");
					e.setAttribute("fill","#fff");
					e.setAttribute("stroke-width",Math.max(1,fs>>3));
					g.appendChild(e);
					e=document.createElementNS('http://www.w3.org/2000/svg','text');
					e.setAttribute("x",cx);
					e.setAttribute("y",cy+(fs>>1));
					e.setAttribute("text-anchor","middle");
					e.setAttribute("font-family","arial");
					e.setAttribute("font-weight","normal");
					e.setAttribute("fill","#000");
					e.setAttribute("font-size",(fs>>1)*3);
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
}
function switchNumber()
{
	setNumber(document.getElementById("number").checked);
}
window.addEventListener("load",function(){setNumber(2);},false);
</script>
</body>
</html>