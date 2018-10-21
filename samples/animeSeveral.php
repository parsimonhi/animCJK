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
.charDiv
{
	display:inline-block;
	margin:0 1px 0.5em 1px;
	width:256px;
	height:256px;
	border:1px solid #ccc;
}
.actionBtn
{
	-webkit-appearance:none;
	margin-top:0.25em;
	font-size:2em;
	border:0;
	background:#999;
	color:#fff;
	border-radius:0.5em;
	margin:0.5em;
}
.actionBtn:hover,
.actionBtn:active,
.actionBtn:focus {background:#c00;outline:0;}
.actionBtn:hover {cursor:pointer;}
.actionBtn:disabled {background:#ccc;cursor:default;}
.actionBtn::-moz-focus-inner {border:0;}
</style>
<title>AnimCJK - Anime several</title>
</head>
<body>
<?php displayHeader("AnimCJK - Anime several");?>
<button class="actionBtn" type="button" onclick="animeSeveralChar()">Animate</button>
<div class="stringDiv">
<?php
$km=mb_strlen($data,'UTF-8');
$s="";
$k2=0;
function changeDelay($m)
{
	global $k2;
	$k2++;
	return "delay:".$k2."s";
}
for($k=0;$k<$km;$k++)
{
	$s.="<div class=\"charDiv\">";
	// get svg file contents
	$s2=file_get_contents("../".$dir."/".decUnicode(mb_substr($data,$k,1,'UTF-8')).".svg");
	// rename id to avoid duplicate in case of a character is displayed several times
	$s2=preg_replace("/(z\d{5})/","$1-".($k+1),$s2);
	// change animation-delay to display characters one after another 
	$s2=preg_replace_callback("/delay:\d+s/","changeDelay",$s2);
	$s.=$s2;
	$s.="</div>";
}
echo $s;
?>
</div>
<?php echo displayFooter("animeSeveral");?>
<script>
function animeSeveralChar()
{
	// (re)start animation when clicking on "Animate" button
	var e,s,list,k,km;
	list=document.querySelectorAll(".charDiv");
	km=list.length;
	for (k=0;k<km;k++)
	{
		e=list[k];
		s=e.innerHTML;
		e.innerHTML="";
		e.innerHTML=s;
	}
}
</script>
</body>
</html>
