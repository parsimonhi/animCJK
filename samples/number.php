<!doctype html>
<?php include "minimal.php";?>
<html lang="<?php echo $lang;?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="initial-scale=1.0,user-scalable=yes">
<meta name="description" content="Demo to add stroke numbers to AnimCJK SVG
representing Japanese or Chinese characters">
<link rel="stylesheet" href="_css/minimal.css" type="text/css">
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
<?php
$s=file_get_contents("../".$dir."/".$dec.".svg");
// replace native css from svg file (since in this sample, there will be no animation)
$a="<style>\n";
$a.="svg.acjk path[clip-path] {\n";
$a.="fill:none;\n";
$a.="}\n";
$a.="</style>";
$s=preg_replace("/<style>[\s\S]*<.style>/",$a,$s);
echo $s;
?>
</div>
<?php echo displayFooter("number");?>
<script>
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
					e.setAttributeNS(null,"cx",cx);
					e.setAttributeNS(null,"cy",cy);
					e.setAttributeNS(null,"r",fs);
					e.setAttributeNS(null,"stroke","#000");
					e.setAttributeNS(null,"fill","#fff");
					e.setAttributeNS(null,"stroke-width",Math.max(1,fs>>3));
					g.appendChild(e);
					e=document.createElementNS('http://www.w3.org/2000/svg','text');
					e.setAttributeNS(null,"x",cx);
					e.setAttributeNS(null,"y",cy+(fs>>1));
					e.setAttributeNS(null,"text-anchor","middle");
					e.setAttributeNS(null,"font-family","arial");
					e.setAttributeNS(null,"font-weight","normal");
					e.setAttributeNS(null,"fill","#000");
					e.setAttributeNS(null,"font-size",(fs>>1)*3);
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
window.addEventListener("load",function(){setNumber(1);},false);
</script>
</body>
</html>