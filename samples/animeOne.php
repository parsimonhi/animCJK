<!doctype html>
<?php include "minimal.php";?>
<html lang="<?php echo $lang;?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="initial-scale=1.0,user-scalable=yes">
<meta name="description" content="Demo to animate one AnimCJK SVG
representing Japanese or Chinese characters">
<link rel="stylesheet" href="_css/minimal.css" type="text/css">
<title>AnimCJK - Anime one</title>
</head>
<body>
<?php displayHeader("AnimCJK - Anime one");?>
<p>Basic display of one character</p>
<button class="actionBtn" type="button" onclick="restartAnime()">Animate</button>
<div id="charDiv">
<?php
// just include the svg as is and that's all what normal browsers need
include "../".$dir."/".$dec.".svg";
?>
</div>
<?php echo displayFooter("animeOne");?>
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
	if (asvg.activated>0) asvg.run('one'); // pitiful browser
	else forceReflow(); // normal browser
}
window.addEventListener("load",function(){asvg.run('one');},false); // pitiful browser
</script>
</body>
</html>
