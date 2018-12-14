<!doctype html>
<?php include "minimal.php";?>
<html lang="<?php echo $lang;?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="initial-scale=1.0,user-scalable=yes">
<meta name="description" content="Demo to animate one AnimCJK SVG
representing Japanese or Chinese characters,
display radical in a different color">
<link rel="stylesheet" href="_css/minimal.css" type="text/css">
<style>
#cartoucheDiv
{
	margin:0 auto 1em auto;
	max-width:256px;
	text-align:left;
}
</style>
<title>AnimCJK - Radical</title>
</head>
<body>
<?php displayHeader("AnimCJK - Radical");?>
<p>Display the radical of the character in a different color</p>
<button class="actionBtn" type="button" onclick="restartAnime()">Animate</button>
<?php
// get data from dictionaryXyz.txt files
if ($lang=="ja") $f="dictionaryJa.txt";
else $f="dictionaryZhHans.txt";
$s=file_get_contents("../".$f);
$c=unichr($dec);
if (preg_match("/\\{\"character\":\"".$c."[^}]+\\}/u",$s,$r))
{
	$l=$r[0];
	if (preg_match("/\"radical\":\"([^\"]+)\"/u",$l,$r2)) $radical=$r2[1];
	else $radical="";
	if (preg_match("/\"decomposition\":\"([^\"]+)\"/u",$l,$r2)) $decomposition=$r2[1];
	else $decomposition="";
	if (preg_match("/\"acjk\":\"([^\"]+)\"/u",$l,$r2)) $acjk=$r2[1];
	else $acjk="";
	// special decomposition
	if (preg_match("/\"acjks\":\"([^\"]+)\"/u",$l,$r2)) $acjks=$r2[1];
	else $acjks="";
}
else
{
	$radical="";
	$decomposition="";
	$acjk="";
	$acjks="";
}
?>
<div id="charDiv" data-acjk="<?php echo $acjk;?>">
<?php
// get svg file
// some characters are special because some strokes are split to show the radical
$special="../".$dir."Special/".$dec.".svg";
if (file_exists($special)) $s=file_get_contents($special);
else $s=file_get_contents("../".$dir."/".$dec.".svg");
function getNumOfStroke($s)
{
	if (preg_match_all("/path style/u",$s,$m,PREG_SET_ORDER)) return count($m);
	return 0;
}
function isRadicalStroke($n,$d)
{
	if (!$d) return false;
	if (substr($d,1,1)==".") return true;
	$nsi=0;
	$d=preg_replace("/:/u","",$d);
	$d=preg_replace("/[^0-9.]+/u","?",$d);
	while (preg_match("/[0-9]+/u",$d,$m,PREG_OFFSET_CAPTURE)&&($m[0][1]>=1))
	{
		$pos=$m[0][1];
		$nsj=$nsi+intval(substr($d,$pos));
		$c=substr($d,$pos-1,1);
		if (($c==".")&&($n>=$nsi)&&($n<$nsj)) return true;
		$nsi=$nsj;
		$d=preg_replace("/^[^0-9]*[0-9]+/u","",$d);
	}
	return false;
}
$km=getNumOfStroke($s);
for ($k=0;$k<$km;$k++)
{
	$p="/<path style=\"--d:([0-9]+);\"/u";
	// manage special decomposition when some strokes are split to show the radical
	// normal decomposition is in $acjk, special decomposition is in $acjks
	// special characters are for instance 由, 甲, 申 ...
	if (isRadicalStroke($k,$acjks?$acjks:$acjk))
		$s=preg_replace($p,"<path style=\"--d:$1;stroke:#fa0;\"",$s,1);
	else
		$s=preg_replace($p,"<path style=\"--d:$1;stroke:#00f;\"",$s,1);
}
echo $s;
?>
</div>
<div id="cartoucheDiv">
<?php
echo "Radical: ".$radical;
echo "<br>";
echo "Decomposition: ".$decomposition;
echo "<br>";
echo "Acjk: ".$acjk;
?>
</div>
<?php echo displayFooter("radical");?>
<!-- add polyfill for String.codePointAt() and String.fromCodePoint() functions -->
<script src="_js/codePoint.js"></script>
<!-- add asvg.js to support some pitiful browsers that cannot animate Svg properly -->
<script src="_js/asvg.js"></script>
<script>
asvg.acjk="<?php echo $acjk;?>";
asvg.acjks="<?php echo $acjks;?>";
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
	if (asvg.activated>0) asvg.run('radical'); // pitiful browser
	else forceReflow(); // normal browser
}
window.addEventListener("load",function(){asvg.run('radical');},false); // pitiful browser
</script>
</body>
</html>
