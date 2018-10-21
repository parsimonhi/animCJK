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
<title>AnimCJK - Anime one</title>
</head>
<body>
<?php displayHeader("AnimCJK - Anime one");?>
<button class="actionBtn" type="button" onclick="animeChar()">Animate</button>
<div id="charDiv">
<?php include "../".$dir."/".$dec.".svg";?>
</div>
<?php echo displayFooter("animeOne");?>
<script>
function animeChar()
{
	// (re)start animation when clicking on "Animate" button
	var e,s;
	e=document.getElementById("charDiv");
	s=e.innerHTML;
	e.innerHTML="";
	e.innerHTML=s;
}
</script>
</body>
</html>
