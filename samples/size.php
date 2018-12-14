<!doctype html>
<?php include "minimal.php";?>
<html lang="<?php echo $lang;?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="initial-scale=1.0,user-scalable=yes">
<meta name="description" content="Demo to animate one AnimCJK SVG
representing Japanese or Chinese characters,
change character size">
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
<title>AnimCJK - Size</title>
</head>
<body>
<?php displayHeader("AnimCJK - Size");?>
<p>Change character size</p>
<label>Size (in px):
	<input id="sizeInput" type="text" value="256"">
</label>
<button class="actionBtn" type="button" onclick="restartAnime()">Animate</button>
<div id="charDiv">
<?php
// just include the svg as is and that's all what normal browsers need
include "../".$dir."/".$dec.".svg";
?>
</div>
<?php echo displayFooter("size");?>
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
function restartAnime()
{
	var new_size;
	new_size=parseInt(document.getElementById("sizeInput").value);
	if (!new_size||(new_size<24)) new_size=24;
	document.getElementById("charDiv").style.maxWidth=new_size+"px";
	document.getElementById("charDiv").style.maxHeight=new_size+"px";
	if (asvg.activated>0) asvg.run('one'); // pitiful browser
	else forceReflow(); // normal browser
}
document.getElementById("sizeInput").addEventListener("keyup",function(event) {
	event.preventDefault();
	if (event.keyCode==13) restartAnime();
});
window.addEventListener("load",function(){asvg.run('one');},false); // pitiful browser
</script>
</body>
</html>
