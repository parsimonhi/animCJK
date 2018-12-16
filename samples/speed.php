<!doctype html>
<?php include "minimal.php";?>
<html lang="<?php echo $lang;?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="initial-scale=1.0,user-scalable=yes">
<meta name="description" content="Demo to animate one AnimCJK SVG
representing Japanese or Chinese characters,
change display speed">
<link rel="stylesheet" href="_css/minimal.css" type="text/css">
<style>
label
{
	display:block;
}
label input[type="text"]
{
	font-size:1rem;
	width:3rem;
	text-align:center;
	vertical-align:middle;
}
label span
{
	vertical-align:middle;
}
</style>
<title>AnimCJK - Speed</title>
</head>
<body>
<?php displayHeader("AnimCJK - Speed");?>
<p>Change display speed</p>
<label><span>Duration (in second):</span> <input id="speedInput" type="text" value="1"></label>
<button class="actionBtn" type="button" onclick="restartAnime()">Animate</button>
<div id="charDiv">
<?php
// just include the svg as is and that's all what normal browsers need
include "../".$dir."/".$dec.".svg";
?>
</div>
<?php echo displayFooter("speed");?>
<!-- add asvg.js to support some pitiful browsers that cannot animate Svg properly -->
<script src="_js/asvg.js"></script>
<script>
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
function getDelay(e)
{
	// normal browsers
	var a,m;
	a=e.getAttributeNS(null,"style");
	if (a&&(m=a.match(/--d:([^;]+);/))) return Math.max(parseFloat(m[1]),0.01);
	return 0.01;
}
function setDuration(e,t,r)
{
	// normal browsers
	var a,m,d;
	a=e.getAttributeNS(null,"style");
	if (a&&(m=a.match(/--d:([^;]+);/)))
	{
		d=Math.max(parseFloat(m[1]),0.01);
		// remember the 1st time, there is no --t in style attribute
		a=a.replace(/--t:[^;]+;/,""); // work even if no --t in style attribute
		a=a.replace(/--d:[^;]+;/,"--d:"+(d*r)+"s;--t:"+t+"s;");
		e.setAttributeNS(null,"style",a);
	}
}
function speed(new_t)
{
	// normal browsers
	var list,k,km,t,r,ex_t;
	list=document.querySelectorAll("svg.acjk path:not([id])");
	if (list&&(km=list.length))
	{
		ex_t=getDelay(list[0]);
		t=new_t*0.8;
		r=new_t/ex_t;
		for(k=0;k<km;k++) setDuration(list[k],t,r);
	}
}
function restartAnime()
{
	// all browsers
	var new_t,ex_t;
	// new_t will be the delay for the first stroke
	// other delays will be changed accordingly
	// force new_t to be at least 0.01s
	new_t=parseFloat(document.getElementById("speedInput").value);
	if (!new_t||(new_t<0.01)) new_t=0.01;
	if (asvg.activated>0) asvg.speed(new_t); // pitiful browser
	else speed(new_t); // normal browser
	if (asvg.activated>0) asvg.run('one'); // pitiful browser
	else forceReflow(); // normal browser
}
document.getElementById("speedInput").addEventListener("keyup",function(event) {
	event.preventDefault();
	if (event.keyCode==13) restartAnime();
});
window.addEventListener("load",function(){asvg.run('one');},false); // pitiful browser
</script>
</body>
</html>
