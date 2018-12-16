<!doctype html>
<?php include "minimal.php";?>
<html lang="<?php echo $lang;?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="initial-scale=1.0,user-scalable=yes">
<meta name="description" content="Demo to animate one AnimCJK SVG
representing Japanese or Chinese characters,
infinite animation">
<link rel="stylesheet" href="_css/minimal.css" type="text/css">
<title>AnimCJK - Infinite</title>
</head>
<body>
<?php displayHeader("AnimCJK - Infinite");?>
<p>Infinite animation of a character</p>
<div id="charDiv">
<?php
// just include the svg as is and that's all what normal browsers need
include "../".$dir."/".$dec.".svg";
?>
</div>
<?php echo displayFooter("infinite");?>
<!-- add asvg.js to support some pitiful browsers that cannot animate Svg properly -->
<script src="_js/asvg.js"></script>
<script>
function forceReflow()
{
	// normal browser
	// force a reflow to restart animation
	var e,s;
	e=document.querySelector("svg.acjk");
	s=e.innerHTML;
	e.innerHTML="";
	e.innerHTML=s;
}
function getDelay(e)
{
	// normal browser
	var a,m;
	// get css "--d" value
	a=e.getAttributeNS(null,"style");
	if (a&&(m=a.match(/--d:([^;]+);/))) return parseFloat(m[1]);
	return -1;
}
function getDuration(e)
{
	// normal browsers
	return parseFloat(window.getComputedStyle(e,null).getPropertyValue('--t'));
}
function restartAnime()
{
	if (asvg.activated>0) asvg.run('one'); // pitiful browser
	else forceReflow(); // normal browser
}
function infiniteAnime1(d,t)
{
	// all browsers
	restartAnime();
	setInterval(restartAnime,(d+2*t*1.25)*1000);
}
function infiniteAnime()
{
	// all browsers
	var List,k,km,d,t;
	if (asvg.activated<0) {setTimeout(infiniteAnime,50);return;}
	km=0;
	if (asvg.activated>0) // pitiful browser
	{
		List=document.querySelectorAll("svg.acjk path[class='median']");
		km=List.length;
		if (km)
		{
			d=asvg.getDelay(List[km-1]);
			t=asvg.getDuration(List[km-1]);
		}
	}
	else // normal browser
	{
		List=document.querySelectorAll("svg.acjk path:not([id])");
		km=List.length;
		if (km)
		{
			d=getDelay(List[km-1]);
			t=getDuration(List[km-1]);
		}
	}
	// delay of 1st loop shorter than the following 
	if (km) setTimeout("infiniteAnime1("+d+","+t+")",(d+t*1.25)*1000);
}
window.addEventListener("load",function(){asvg.run('one');},false); // pitiful browser
window.addEventListener("load",function(){infiniteAnime();},false); // all browser
</script>
</body>
</html>
