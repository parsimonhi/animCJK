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
	display:none;
}
img
{
	border:1px solid #ccc;
	width:256px;
	height:256px;
}
</style>
<title>AnimCJK - PNG</title>
</head>
<body>
<?php displayHeader("AnimCJK - PNG");?>
<p>Generate a PNG image of a character on the fly</p>
<div id="charDiv">
<?php include "../".$dir."/".$dec.".svg";?>
</div>
<?php echo displayFooter("png");?>
<script>
function generatePngFromSvg()
{
	// generate a PNG image of size 1024x1024 from a svg corresponding to a character
	// assume the svg is coming from one of the acjk official repositories
	var e,p,cn,cx,list,k,km,d,m,r,x0,y0,x1,y1,x2,y2,x3,y3;
	cn=document.createElement('canvas');
	cn.width=1024;
	cn.height=1024;
	cx=cn.getContext("2d");
	list=document.querySelectorAll("svg.acjk path[id]");
	km=list.length;
	r=/([MLQC])([0-9-]+) ([0-9-]+) ?([0-9-]+)? ?([0-9-]+)? ?([0-9-]+)? ?([0-9-]+)?/g;
	for(k=0;k<km;k++)
	{
		cx.beginPath();
		d=list[k].getAttribute("d");
		x0=0;
		y0=0;
		while (m=r.exec(d))
		{
			x1=parseInt(m[2]);
			y1=parseInt(m[3]);
			if (!x0) x0=x1;
			if (!y0) y0=y1;
			if (m[1]=="M") cx.moveTo(x1,y1);
			else if (m[1]=="L") cx.lineTo(x1,y1);
			else
			{
				x2=parseInt(m[4]);
				y2=parseInt(m[5]);
				if (m[1]=="Q") cx.quadraticCurveTo(x1,y1,x2,y2);
				else
				{
					x3=parseInt(m[6]);
					y3=parseInt(m[7]);
					cx.bezierCurveTo(x1,y1,x2,y2,x3,y3);
				}
			}
		}
		cx.lineTo(x0,y0); // sometimes not necessary, but it doesn't matter
		x0=0;
		y0=0;
		cx.fill();
	}
	var imgSrc=cn.toDataURL("image/png");
	var pngImg=new Image(1024,1024);
	pngImg.src=imgSrc;
	e=document.getElementById('charDiv');
	p=e.parentNode;
	p.insertBefore(pngImg,e);
	p.removeChild(e); // remove the svg and keep only the png image
}
window.addEventListener("load",generatePngFromSvg,false);
</script>
</body>
</html>
