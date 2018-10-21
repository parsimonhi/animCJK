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
<title>AnimCJK - Red</title>
</head>
<body>
<?php displayHeader("AnimCJK - Red");?>
<p>
<label>Colored/uncolored strokes on the fly:
<input id="colorize" checked type="checkbox" onclick="switchColorize()">
</label>
</p>
<div id="charDiv">
<?php include "../".$dir."/".$dec.".svg";?>
</div>
<?php echo displayFooter("red");?>
<script>
// "colorize" script
function removeAnimation()
{
	// by default, a character is animated
	// for this sample one doesn't need animation, so remove it
	var k,km,list;
	list=document.querySelectorAll("svg.acjk path[clip-path]");
	km=list.length;
	for (k=0;k<km;k++) list[k].style.animation="none";
}
function computeOne(z,k,km)
{
	return Math.floor(k*z/(km?km:1));
}
function computeFinalColor(c,k,km)
{
	var r,g,b,a;
	if (!km) // case of a character with an unique stroke
		return "rgba("+c.r*0.8+","+c.g*0.8+","+c.b*0.8+","+c.a+")";
	r=computeOne(c.r,k,km);
	g=computeOne(c.g,k,km);
	b=computeOne(c.b,k,km);
	a=c.a;
	return "rgba("+r+","+g+","+b+","+a+")";
}
function colorizeStrokes(x)
{
	var k,km,list,c;
	list=document.querySelectorAll("svg.acjk path[id]");
	km=list.length;
	if (x)
	{
		c={r:255,g:0,b:0,a:1}; // can be replaced by another color
		for (k=0;k<km;k++) list[k].style.fill=computeFinalColor(c,k,km-1);
	}
	else for (k=0;k<km;k++) list[k].style.fill="#000";
}
function switchColorize()
{
	colorizeStrokes(document.getElementById("colorize").checked);
}
window.addEventListener("load",function(){removeAnimation();colorizeStrokes(1);},false);
</script>
</body>
</html>
