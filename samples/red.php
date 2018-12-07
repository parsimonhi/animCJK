<!doctype html>
<?php include "minimal.php";?>
<html lang="<?php echo $lang;?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="initial-scale=1.0,user-scalable=yes">
<meta name="description" content="Demo to show AnimCJK SVG
representing Japanese or Chinese characters,
display each stroke in a different color between black and red">
<link rel="stylesheet" href="_css/minimal.css" type="text/css">
<title>AnimCJK - Red</title>
</head>
<body>
<?php displayHeader("AnimCJK - Red");?>
<p>
<label>Colored/uncolored strokes with colors between black and red on the fly:
<input id="colorize" checked type="checkbox" onclick="switchColorize()">
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
<?php echo displayFooter("red");?>
<script>
// "colorize" script
function computeOne(z,k,km)
{
	return Math.floor(k*z/(km?km:1));
}
function computeFinalColor(c,k,km)
{
	var r,g,b,a;
	if (!km) // case of a character that has only one stroke
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
		// c is the color of the last stroke
		// can be replaced by any other color, including color with transparency
		c={r:255,g:0,b:0,a:1};
		for (k=0;k<km;k++) list[k].style.fill=computeFinalColor(c,k,km-1);
	}
	else for (k=0;k<km;k++) list[k].style.fill="#000";
}
function switchColorize()
{
	colorizeStrokes(document.getElementById("colorize").checked);
}
window.addEventListener("load",function(){colorizeStrokes(1);},false);
</script>
</body>
</html>
