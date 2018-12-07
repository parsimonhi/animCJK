<!doctype html>
<?php include "minimal.php";?>
<?php include "_php/convertKana.php";?>
<html lang="<?php echo $lang;?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="initial-scale=1.0,user-scalable=yes">
<meta name="description" content="Demo to animate one AnimCJK SVG
representing Japanese or Chinese characters,
display a card of the character with some additional data as its description and radical">
<style>
<?php $size=256;?>
body {text-align:center;}
a {color:#000;}
a:visited {color:#666;}
#charDiv
{
	margin:0 auto 0.5em auto;
	max-width:<?php echo $size;?>px;
	max-height:<?php echo $size;?>px;
	border:1px solid #ccc;
}
.cartoucheDiv
{
	margin:0 auto 1em auto;
	text-align:left;
	width:<?php echo $size;?>px;
	border:1px solid transparent;
}
.navigationDiv
{
	display:flex;
	margin:0 auto 1em auto;
	text-align:center;
	width:<?php echo $size+2;?>px;
}
.navigationDiv button
{
	-webkit-appearance:none;
	flex:1;
	box-sizing:border-box;
	border:1px solid #ccc;
	background:transparent;
	margin:0 4px;
	padding:2px;
	position:relative;
	cursor:pointer;
}

.navigationDiv button:first-of-type
{
	margin:0 4px 0 0;
}
.navigationDiv button:last-of-type
{
	margin:0 0 0 4px;
}
.navigationDiv button div
{
	box-sizing:border-box;
	position:relative;
	width:0;
	height:22px;
	border-top:11px solid transparent;
	border-bottom:11px solid transparent;
	margin:0 auto;
	color:transparent;
}
.navigationDiv button:hover,
.navigationDiv button:active,
.navigationDiv button:focus {outline:none;}
.navigationDiv button::-moz-focus-inner {padding:0;border:0;}
.navigationDiv button:disabled {cursor:default;}
.navigationDiv button:disabled div {opacity:0.3;}
#first div
{
	width:16px;
	border-right:11px solid black;
}
#first:not([disabled]):active div,
#first:not([disabled]):focus div {border-right:11px solid #09c;}
#first:not([disabled]):hover div {border-right:11px solid #c00;}
#first div:before
{
	content:"";
	width:5px;
	height:22px;
	background:black;
	position:absolute;
	top:-11px;
	left:0;
}
#first:active:not([disabled]) div:before,
#first:focus:not([disabled]) div:before {background:#09c;}
#first:hover:not([disabled]) div:before {background:#c00;}
#pred div
{
	width:11px;
	border-right:11px solid black;
}
#pred:not([disabled]):active div,
#pred:not([disabled]):focus div {border-right:11px solid #09c;}
#pred:not([disabled]):hover div {border-right:11px solid #c00;}
#next div
{
	width:11px;
	border-left:11px solid black;
}
#next:not([disabled]):active div,
#next:not([disabled]):focus div {border-left:11px solid #09c;}
#next:not([disabled]):hover div {border-left:11px solid #c00;}
#last div
{
	width:16px;
	border-left:11px solid black;
}
#last:not([disabled]):active div,
#last:not([disabled]):focus div {border-left:11px solid #09c;}
#last:not([disabled]):hover div {border-left:11px solid #c00;}
#last div:after
{
	content:"";
	width:5px; 
	height:22px; 
	background:black;
	position:absolute;
	top:-11px;
	right:0;
}
#last:active:not([disabled]) div:after,
#last:focus:not([disabled]) div:after {background:#09c;}
#last:hover:not([disabled]) div:after {background:#c00;}
#stop div
{
	width:15px;
}
#stop div:before
{
	content:"";
	width:5px; 
	height:22px; 
	background:black;
	position:absolute;
	top:-11px;
	left:0;
}
#stop:active:not([disabled]) div:before,
#stop:focus:not([disabled]) div:before {background:#09c;}
#stop:hover:not([disabled]) div:before {background:#c00;}
#stop div:after
{
	content:"";
	width:5px; 
	height:22px; 
	background:black;
	position:absolute;
	top:-11px;
	right:0;
}
#stop:active:not([disabled]) div:after,
#stop:focus:not([disabled]) div:after {background:#09c;}
#stop:hover:not([disabled]) div:after {background:#c00;}
#play div
{
	width:19px;
	background:#000;
}
#play:active:not([disabled]) div,
#play:focus:not([disabled]) div {background:#09c;}
#play:hover:not([disabled]) div {background:#c00;}
div.navigationDiv
{
	-khtml-user-select: none;
	-webkit-user-select: none;
	-moz-user-select: -moz-none;
	-ms-user-select: none;
	user-select: none;
}
.navigationDiv button span
{
	display:none;
}
</style>
<title>AnimCJK - Card</title>
</head>
<body>
<?php displayHeader("AnimCJK - Card");?>
<?php
if ($lang=="zh-hant") $f="dictionaryZhHant.txt";
else if ($lang=="zh-hans") $f="dictionaryZhHans.txt";
else $f="dictionaryJa.txt";
$s=file_get_contents("../".$f);
// get data from dictionaryXxx.txt files
$c=unichr($dec);
if (preg_match("/\\{\"character\":\"".$c."[^}]+\\}/u",$s,$r))
{
	$l=$r[0];
	if (preg_match("/\"radical\":\"([^\"]+)\"/u",$l,$r2)) $radical=$r2[1];
	else $radical="";
	if (preg_match("/\"decomposition\":\"([^\"]+)\"/u",$l,$r2)) $decomposition=$r2[1];
	else $decomposition="";
	if (preg_match("/\"definition\":\"([^\"]+)\"/u",$l,$r2)) $definition=$r2[1];
	else $definition="";
	if ($lang=="ja")
	{
		if (preg_match("/\"on\":\\[\"([^\\]]+)\"\\]/u",$l,$r2)) $on=explode(",",preg_replace("/\"/","",$r2[1]));
		else $on="";
		if (preg_match("/\"kun\":\\[\"([^\\]]+)\"\\]/u",$l,$r2)) $kun=explode(",",preg_replace("/\"/","",$r2[1]));
		else $kun="";
		$pinyin="";
	}
	else
	{
		if (preg_match("/\"pinyin\":\\[\"([^\\]]+)\"\\]/u",$l,$r2)) $pinyin=explode(" ",preg_replace("/\"/","",$r2[1]));
		else $pinyin="";
		$on="";
		$kun="";
	}
}
else
{
	$radical="";
	$decomposition="";
	$acjk="";
}
?>
<div id="charDiv">
<?php
include "../".$dir."/".$dec.".svg";
?>
</div>
<div class="navigationDiv">
<button id="first" onclick="doFirst()"><div>First</div></button>
<button id="pred" onclick="doPred()"><div>Pred</div></button>
<button id="next" onclick="doNext()"><div>Next</div></button>
<button id="last" onclick="doLast()"><div>Last</div></button>
<button id="stop" onclick="doStop()"><div>Stop</div></button>
<button id="play" onclick="doPlay()"><div>Play</div></button>
</div>
<div class="cartoucheDiv">
<?php
echo "Radical: ".$radical;
echo "<br>";
echo "Decomposition: ".$decomposition;
echo "<br>";
function formatPrononciation($a,$t)
{
	$s="";
	$first=true;
	foreach($a as $b)
	{
		if (!$first)
		{
			if ($t=="pinyin") $s.=", ";
			else $s.="・";
		}
		else $first=false;
		if ($t=="on") $s.=convertJapaneseOn($b);
		else if ($t=="kun") $s.=convertJapaneseKun($b);
		else $s.=preg_replace("/\\([^\\)]*\\)/","",$b);
	}
	return $s;
}
if ($on)
{
	echo "Onyomi: ".formatPrononciation($on,"on");
	echo "<br>";
}
if ($kun)
{
	echo "Kunyomi: ".formatPrononciation($kun,"kun");
	echo "<br>";
}
if ($pinyin)
{
	echo "Pinyin: ".formatPrononciation($pinyin,"pinyin");
	echo "<br>";
}
echo "Definition: ".$definition;
?>
</div>
<div id="debug"></div>
<?php echo displayFooter("card");?>
<script>
var currentStroke=0;
var currentStrokeMax=0;
var currentRun=[];
var currentDec="<?php echo $dec;?>";
var currentTime=1;
var inRun=0;
var pitiful=-1;
function debug(s,a)
{
	var e
	if (e=document.getElementById("debug"));
		if (a) e.innerHTML+=s;
		else e.innerHTML=s;
}
function forceReflow()
{
	// force a reflow when animation changes
	var e,s;
	e=document.querySelector("svg.acjk");
	s=e.innerHTML;
	e.innerHTML="";
	e.innerHTML=s;
}
function doDisabling()
{
	document.getElementById("first").disabled=(currentStroke==0);
	document.getElementById("pred").disabled=(currentStroke==0);
	document.getElementById("next").disabled=(currentStroke>currentStrokeMax);
	document.getElementById("last").disabled=(currentStroke>currentStrokeMax);
	document.getElementById("stop").disabled=(inRun==0);
	document.getElementById("play").disabled=(inRun==1);
}
function resetRun()
{
	var k,km;
	if (inRun)
	{
		inRun=0;
		km=currentRun.length;
		for(k=0;k<km;k++) clearTimeout(currentRun[k]);
		currentRun=[];
	}
}
function getDelay(e)
{
	var a,m;
	// if the delay is already stored in the "data-d" attribute, just return it
	a=e.getAttributeNS(null,"data-d");
	if (a) return parseFloat(a);
	// otherwise try to get css "--d" value
	a=e.getAttributeNS(null,"style");
	if (a&&(m=a.match(/--d:([^;]+);/))) return parseFloat(m[1]);
	// otherwise try to get delay from "clip-path" attribut value
	// remember that this attribut is removed soon after the first call to getDelay()
	a=e.getAttributeNS(null,"clip-path");
	if (a&&(m=a.match(/c([0-9]+)[^0-9]/))) return parseInt(m[1]);
	return -1; // error
}
function doInit()
{
	var list;
	list=document.querySelectorAll("svg.acjk path:not([id])");
	if (list) currentStrokeMax=list.length-1;
}
function pitifulOne(n)
{
	var list,list1,list2,k,km,km1,km2;
	list1=document.querySelectorAll("svg.acjk path[id]");
	if (list1&&(km1=list1.length))
	{
		// use "clip-path" because it still exists the first time one comes here
		list2=document.querySelectorAll("svg.acjk path[clip-path]");
		if (list2&&(km2=list2.length)&&(km1==km2))
		{
			// come here only once
			km=km2;
			for (k=0;k<km;k++)
			{
				list2[k].style.animation="none";
				list2[k].style.stroke="none";
				list2[k].style.fill="none";
				// store the delay in the data-d attribute
				// before removing the clip-path attribute
				// because getDelay may use clip-path attribute value
				list2[k].setAttributeNS(null,"data-d",getDelay(list2[k])+'');
				list2[k].removeAttribute("clip-path");
				list2[k].removeAttribute("pathLength");
				list2[k].setAttributeNS(null,"d",list1[k].getAttributeNS(null,"d"));
			}
		}
	}
	list=document.querySelectorAll("svg.acjk path:not([id])");
	if (list&&(km=list.length))
	{
		for (k=0;k<km;k++) 
			if (k>n) list[k].style.fill="none";
			else list[k].style.fill="#000";
	}
}
function normalOne(n)
{
	var list,k,km,a;
	list=document.querySelectorAll("svg.acjk path:not([id])");
	km=list.length;
	for(k=0;k<km;k++)
	{
		if (k>=n) list[k].style.strokeDashoffset="3339";
		else list[k].style.strokeDashoffset="0";
		// use calc(var(--t) * 0 * 1.25) to eliminate buggy browsers such as MS Edge
		if (k==n) list[k].style.animation="zk var(--t) linear calc(var(--t) * 0 * 1.25) forwards";
		else list[k].style.animation="none";
	}
	forceReflow();
}
function pitifulTrick(n)
{
	// detect if the browser started the animation
	// two cases: browser cannot animate svg (many?) or can poorly animate svg (edge?)
	var s,d,list,k,km;
	// clip-path attribute still exists here
	list=document.querySelectorAll("svg.acjk path[clip-path]");
	if (list&&(km=list.length))
	{
		s=window.getComputedStyle(list[n],null);
		d=parseInt(s.getPropertyValue("stroke-dashoffset"));
		if (d==3339)
		{
			// stroke-dashoffset is still 3339 => animation not done or poorly done
			pitiful=1;
		}
		else pitiful=0;
	}
	// pitiful=1; // for testing only
	if (pitiful>0) pitifulOne(n);
}
function doOne(n)
{
	var list,k,km;
	if (pitiful>0) pitifulOne(n);
	else
	{
		normalOne(n);
		if (pitiful<0) setTimeout("pitifulTrick("+n+")",100);
	}
	currentStroke=n+1;
	if (currentStroke>currentStrokeMax) inRun=0;
	doDisabling();
}
function doStop()
{
	resetRun();
	doDisabling();
}
function doPlay()
{
	var k;
	resetRun();
	inRun=1;
	if (currentStroke>currentStrokeMax) currentStroke=0;
	for (k=currentStroke;k<=currentStrokeMax;k++)
		currentRun[k]=setTimeout("doOne("+k+")",currentTime*1000*(k-currentStroke));
	doDisabling();
}
function pitifulFirst()
{
	var list,k,km;
	list=document.querySelectorAll("svg.acjk path:not([id])");
	km=list.length;
	for (k=0;k<km;k++) list[k].style.fill="none";
}
function normalFirst()
{
	var list,k,km;
	list=document.querySelectorAll("svg.acjk path:not([id])");
	km=list.length;
	for (k=0;k<km;k++)
	{
		list[k].style.animation="none";
		list[k].style.strokeDashoffset="3339";
	}
	forceReflow();
}
function doFirst()
{
	var k,km;
	resetRun();
	if (pitiful>0) pitifulFirst();
	else normalFirst();
	currentStroke=0;
	doDisabling();
}
function pitifulPred()
{
	var list,k,km;
	list=document.querySelectorAll("svg.acjk path:not([id])");
	km=list.length;
	for (k=0;k<km;k++)
		if (k>=(currentStroke-1)) list[k].style.fill="none";
		else list[k].style.fill="#000";
}
function normalPred()
{
	var list,k,km;
	list=document.querySelectorAll("svg.acjk path:not([id])");
	km=list.length;
	for (k=0;k<km;k++)
	{
		list[k].style.animation="none";
		if (k>=(currentStroke-1)) list[k].style.strokeDashoffset="3339";
		else list[k].style.strokeDashoffset="0";
	}
	forceReflow();
}
function doPred()
{
	resetRun();
	if (pitiful>0) pitifulPred();
	else normalPred();
	currentStroke--;
	doDisabling();
}
function doNext()
{
	resetRun();
	doOne(currentStroke);
}
function pitifulLast()
{
	var list,k,km;
	list=document.querySelectorAll("svg.acjk path:not([id])");
	km=list.length;
	for (k=0;k<km;k++)
		list[k].style.fill="#000";
}
function normalLast()
{
	var list,k,km;
	list=document.querySelectorAll("svg.acjk path:not([id])");
	km=list.length;
	for (k=0;k<km;k++)
	{
		list[k].style.animation="none";
		list[k].style.strokeDashoffset="0";
	}
	forceReflow();
}
function doLast()
{
	var k,km;
	resetRun();
	if (pitiful>0) pitifulLast();
	else normalLast();
	currentStroke=currentStrokeMax+1;
	doDisabling();
}
function whenLoad()
{
	inRun=0;
	doInit();
	doPlay();
}
window.addEventListener("load",whenLoad,false);
</script>
</body>
</html>
