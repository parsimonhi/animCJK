<!doctype html>
<?php include "minimal.php";?>
<html lang="<?php echo $lang;?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="initial-scale=1.0,user-scalable=yes">
<meta name="description" content="Demo to animate one AnimCJK SVG
representing Japanese or Chinese characters,
display the stroke that is being drawn in a different color">
<link rel="stylesheet" href="_css/minimal.css" type="text/css">
<title>AnimCJK - Color</title>
</head>
<body>
<?php displayHeader("AnimCJK - Color");?>
<p>Display the stroke that is being drawn in a different color</p>
<button class="actionBtn" type="button" onclick="restartAnime()">Animate</button>
<div id="charDiv">
<?php
$s=file_get_contents("../".$dir."/".$dec.".svg");
// modify @keyframes zk to colorize the stroke that is being drawn
$a="\tto {\n\t\tstroke-dashoffset:0;\n\t}\n";
$b="\tfrom {\n\t\tstroke-dashoffset:3339;\n\t\tstroke:#c00;\n\t}\n";
$b.="\t75% {\n\t\tstroke-dashoffset:0;\n\t\tstroke:#c00;\n\t}\n";
$b.="\tto {\n\t\tstroke-dashoffset:0;\n\t\tstroke:#000;\n\t}\n";
$s=str_replace($a,$b,$s);
echo $s;
?>
</div>
<?php echo displayFooter("color");?>
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
	if (asvg.activated>0) asvg.run('color'); // pitiful browser
	else forceReflow(); // normal browser
}
window.addEventListener("load",function(){asvg.run('color');},false); // pitiful browser
</script>
</body>
</html>
