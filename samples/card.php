<!doctype html>
<?php include "minimal.php";?>
<html lang="<?php echo $lang;?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="initial-scale=1.0,user-scalable=yes">
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
#run div
{
	width:27px;
}
#run div:before
{
	content:"";
	position:absolute;
	top:-11px;
	left:0;
	width:0; 
	height:0;
	border:0;
	border-top:11px solid transparent;
	border-bottom:11px solid transparent;
	border-right:11px solid black;
}
#run:active:not([disabled]) div:before,
#run:focus:not([disabled]) div:before {border-right:11px solid #09c;}
#run:hover:not([disabled]) div:before {border-right:11px solid #c00;}
#run div:after
{
	content:"";
	position:absolute;
	top:-11px;
	right:0;
	width:0; 
	height:0;
	border:0;
	border-top:11px solid transparent;
	border-bottom:11px solid transparent;
	border-left:11px solid black;
}
#run:active:not([disabled]) div:after,
#run:focus:not([disabled]) div:after {border-left:11px solid #09c;}
#run:hover:not([disabled]) div:after {border-left:11px solid #c00;}
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
function convertJapaneseKun($s)
{
	$s=str_replace("PP","っP",$s);
	$s=str_replace("TT","っT",$s);

	$s=str_replace("AA","Aあ",$s);
	$s=str_replace("II","Iい",$s);
	$s=str_replace("UU","Uう",$s);
	$s=str_replace("EE","Eえ",$s);
	$s=str_replace("OO","Oお",$s);

	$s=str_replace("KYA","きゃ",$s);
	$s=str_replace("KYU","きゅ",$s);
	$s=str_replace("KYO","きょ",$s);

	$s=str_replace("KA","か",$s);
	$s=str_replace("KI","き",$s);
	$s=str_replace("KU","く",$s);
	$s=str_replace("KE","け",$s);
	$s=str_replace("KO","こ",$s);

	$s=str_replace("GYA","ぎゃ",$s);
	$s=str_replace("GYU","ぎゅ",$s);
	$s=str_replace("GYO","ぎょ",$s);
	
	$s=str_replace("GA","が",$s);
	$s=str_replace("GI","ぎ",$s);
	$s=str_replace("GU","ぐ",$s);
	$s=str_replace("GE","げ",$s);
	$s=str_replace("GO","ご",$s);

	// tsu before su
	$s=str_replace("CHA","ちゃ",$s);
	$s=str_replace("CHU","ちゅ",$s);
	$s=str_replace("CHO","ちょ",$s);
	
	$s=str_replace("TA","た",$s);
	$s=str_replace("CHI","ち",$s);
	$s=str_replace("TSU","つ",$s);
	$s=str_replace("TE","て",$s);
	$s=str_replace("TO","と",$s);

	$s=str_replace("SHA","しゃ",$s);
	$s=str_replace("SHU","しゅ",$s);
	$s=str_replace("SHO","しょ",$s);

	$s=str_replace("SA","さ",$s);
	$s=str_replace("SHI","し",$s);
	$s=str_replace("SU","す",$s);
	$s=str_replace("SE","せ",$s);
	$s=str_replace("SO","そ",$s);

	$s=str_replace("JA","じゃ",$s);
	$s=str_replace("JU","じゅ",$s);
	$s=str_replace("JO","じょ",$s);
	
	$s=str_replace("ZA","ざ",$s);
	$s=str_replace("JI","じ",$s);
	$s=str_replace("ZU","ず",$s);
	$s=str_replace("ZE","ぜ",$s);
	$s=str_replace("ZO","ぞ",$s);

	$s=str_replace("JA","ぢゃ",$s);
	$s=str_replace("JU","ぢゅ",$s);
	$s=str_replace("JO","ぢょ",$s);

	$s=str_replace("DA","だ",$s);
	$s=str_replace("JI","ぢ",$s);
	$s=str_replace("ZU","ず",$s);
	$s=str_replace("DE","で",$s);
	$s=str_replace("DO","ど",$s);
		
	$s=str_replace("NYA","にゃ",$s);
	$s=str_replace("NYU","にゅ",$s);
	$s=str_replace("NYO","にょ",$s);
	
	$s=str_replace("NA","な",$s);
	$s=str_replace("NI","に",$s);
	$s=str_replace("NU","ぬ",$s);
	$s=str_replace("NE","ね",$s);
	$s=str_replace("NO","の",$s);

	$s=str_replace("HYA","ひゃ",$s);
	$s=str_replace("HYU","ひゅ",$s);
	$s=str_replace("HYO","ひょ",$s);

	$s=str_replace("HA","は",$s);
	$s=str_replace("HI","ひ",$s);
	$s=str_replace("FU","ふ",$s);
	$s=str_replace("HE","へ",$s);
	$s=str_replace("HO","ほ",$s);

	$s=str_replace("BYA","びゃ",$s);
	$s=str_replace("BYU","びゅ",$s);
	$s=str_replace("BYO","びょ",$s);

	$s=str_replace("BA","ば",$s);
	$s=str_replace("BI","び",$s);
	$s=str_replace("BU","ぶ",$s);
	$s=str_replace("BE","べ",$s);
	$s=str_replace("BO","ぼ",$s);

	$s=str_replace("PYA","ぴゃ",$s);
	$s=str_replace("PYU","ぴゅ",$s);
	$s=str_replace("PYO","ぴょ",$s);

	$s=str_replace("PA","ぱ",$s);
	$s=str_replace("PI","ぴ",$s);
	$s=str_replace("PU","ぷ",$s);
	$s=str_replace("PE","ぺ",$s);
	$s=str_replace("PO","ぽ",$s);

	$s=str_replace("MYA","みゃ",$s);
	$s=str_replace("MYU","みゅ",$s);
	$s=str_replace("MYO","みょ",$s);
	
	$s=str_replace("MA","ま",$s);
	$s=str_replace("MI","み",$s);
	$s=str_replace("MU","む",$s);
	$s=str_replace("ME","め",$s);
	$s=str_replace("MO","も",$s);

	$s=str_replace("RYA","りゃ",$s);
	$s=str_replace("RYU","りゅ",$s);
	$s=str_replace("RYO","りょ",$s);

	$s=str_replace("RA","ら",$s);
	$s=str_replace("RI","り",$s);
	$s=str_replace("RU","る",$s);
	$s=str_replace("RE","れ",$s);
	$s=str_replace("RO","ろ",$s);

	// ya after [x]ya
	$s=str_replace("YA","や",$s);
	$s=str_replace("YU","ゆ",$s);
	$s=str_replace("YO","よ",$s);
	
	$s=str_replace("WA","わ",$s);
	$s=str_replace("WO","を",$s);

	$s=str_replace("A","あ",$s);
	$s=str_replace("I","い",$s);
	$s=str_replace("U","う",$s);
	$s=str_replace("E","え",$s);
	$s=str_replace("O","お",$s);
	
	$s=str_replace("N","ん",$s);
	
	return $s;
}

function convertJapaneseOn($s)
{
	$s=str_replace("TT","ッT",$s);

	$s=str_replace("AA","Aア",$s);
	$s=str_replace("II","Iイ",$s);
	$s=str_replace("UU","Uウ",$s);
	$s=str_replace("EE","Eエ",$s);
	$s=str_replace("OO","Oオ",$s);
	
	$s=str_replace("KYA","キャ",$s);
	$s=str_replace("KYU","キュ",$s);
	$s=str_replace("KYO","キョ",$s);
	$s=str_replace("GYA","ギャ",$s);
	$s=str_replace("GYU","ギュ",$s);
	$s=str_replace("GYO","ギョ",$s);

	$s=str_replace("KA","カ",$s);
	$s=str_replace("KI","キ",$s);
	$s=str_replace("KU","ク",$s);
	$s=str_replace("KE","ケ",$s);
	$s=str_replace("KO","コ",$s);

	$s=str_replace("GA","ガ",$s);
	$s=str_replace("GI","ギ",$s);
	$s=str_replace("GU","グ",$s);
	$s=str_replace("GE","ゲ",$s);
	$s=str_replace("GO","ゴ",$s);

	// tsu before su
	$s=str_replace("CHA","チャ",$s);
	$s=str_replace("CHU","チュ",$s);
	$s=str_replace("CHO","チョ",$s);

	$s=str_replace("TA","タ",$s);
	$s=str_replace("CHI","チ",$s);
	$s=str_replace("TSU","ツ",$s);
	$s=str_replace("TE","テ",$s);
	$s=str_replace("TO","ト",$s);

	$s=str_replace("SHA","シャ",$s);
	$s=str_replace("SHU","シュ",$s);
	$s=str_replace("SHO","ショ",$s);
	
	$s=str_replace("SA","サ",$s);
	$s=str_replace("SHI","シ",$s);
	$s=str_replace("SU","ス",$s);
	$s=str_replace("SE","セ",$s);
	$s=str_replace("SO","ソ",$s);

	$s=str_replace("JA","ジャ",$s);
	$s=str_replace("JU","ジュ",$s);
	$s=str_replace("JO","ジョ",$s);
	
	$s=str_replace("ZA","ザ",$s);
	$s=str_replace("JI","ジ",$s);
	$s=str_replace("ZU","ズ",$s);
	$s=str_replace("ZE","ゼ",$s);
	$s=str_replace("ZO","ゾ",$s);

	$s=str_replace("CHA","チャ",$s);
	$s=str_replace("CHU","チュ",$s);
	$s=str_replace("CHO","チョ",$s);

	$s=str_replace("TA","タ",$s);
	$s=str_replace("CHI","チ",$s);
	$s=str_replace("TSU","ツ",$s);
	$s=str_replace("TE","テ",$s);
	$s=str_replace("TO","ト",$s);

	$s=str_replace("JA","ヂャ",$s);
	$s=str_replace("JU","ヂュ",$s);
	$s=str_replace("JO","ヂョ",$s);

	$s=str_replace("DA","ダ",$s);
	$s=str_replace("JI","ヂ",$s);
	$s=str_replace("ZU","ズ",$s);
	$s=str_replace("DE","デ",$s);
	$s=str_replace("DO","ド",$s);
		
	$s=str_replace("NYA","ニャ",$s);
	$s=str_replace("NYU","ニュ",$s);
	$s=str_replace("NYO","ニョ",$s);

	$s=str_replace("NA","ナ",$s);
	$s=str_replace("NI","ニ",$s);
	$s=str_replace("NU","ヌ",$s);
	$s=str_replace("NE","ネ",$s);
	$s=str_replace("NO","ノ",$s);

	$s=str_replace("HYA","ヒャ",$s);
	$s=str_replace("HYU","ヒュ",$s);
	$s=str_replace("HYO","ヒョ",$s);
	
	$s=str_replace("HA","ハ",$s);
	$s=str_replace("HI","ヒ",$s);
	$s=str_replace("FU","フ",$s);
	$s=str_replace("HE","ヘ",$s);
	$s=str_replace("HO","ホ",$s);

	$s=str_replace("BYA","ビャ",$s);
	$s=str_replace("BYU","ビュ",$s);
	$s=str_replace("BYO","ビョ",$s);

	$s=str_replace("BA","バ",$s);
	$s=str_replace("BI","ビ",$s);
	$s=str_replace("BU","ブ",$s);
	$s=str_replace("BE","ベ",$s);
	$s=str_replace("BO","ボ",$s);

	$s=str_replace("PYA","ピャ",$s);
	$s=str_replace("PYU","ピュ",$s);
	$s=str_replace("PYO","ピョ",$s);

	$s=str_replace("PA","パ",$s);
	$s=str_replace("PI","ピ",$s);
	$s=str_replace("PU","プ",$s);
	$s=str_replace("PE","ペ",$s);
	$s=str_replace("PO","ポ",$s);

	$s=str_replace("MYA","ミャ",$s);
	$s=str_replace("MYU","ミュ",$s);
	$s=str_replace("MYO","ミョ",$s);

	$s=str_replace("MA","マ",$s);
	$s=str_replace("MI","ミ",$s);
	$s=str_replace("MU","ム",$s);
	$s=str_replace("ME","メ",$s);
	$s=str_replace("MO","モ",$s);

	$s=str_replace("RYA","リャ",$s);
	$s=str_replace("RYU","リュ",$s);
	$s=str_replace("RYO","リョ",$s);

	$s=str_replace("RA","ラ",$s);
	$s=str_replace("RI","リ",$s);
	$s=str_replace("RU","ル",$s);
	$s=str_replace("RE","レ",$s);
	$s=str_replace("RO","ロ",$s);

	// ya after [x]ya
	$s=str_replace("YA","ヤ",$s);
	$s=str_replace("YU","ユ",$s);
	$s=str_replace("YO","ヨ",$s);

	$s=str_replace("WA","ワ",$s);
	$s=str_replace("WO","ヲ",$s);

	$s=str_replace("A","ア",$s);
	$s=str_replace("I","イ",$s);
	$s=str_replace("U","ウ",$s);
	$s=str_replace("E","エ",$s);
	$s=str_replace("O","オ",$s);

	$s=str_replace("N","ン",$s);

	return $s;
}

if ($lang=="ja") $f="dictionaryJa.txt";
else $f="dictionaryZhHans.txt";
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
<?php include "../".$dir."/".$dec.".svg";?>
</div>
<div class="navigationDiv">
<button id="first" onclick="doFirst()"><div>First</div></button>
<button id="pred" onclick="doPred()"><div>Pred</div></button>
<button id="next" onclick="doNext()"><div>Next</div></button>
<button id="last" onclick="doLast()"><div>Last</div></button>
<button id="stop" onclick="doStop()"><div>Stop</div></button>
<button id="run" onclick="doRun()"><div>Run</div></button>
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
<?php echo displayFooter("card");?>
<script>
var currentStroke=0;
var currentRun=[];
var currentDec="<?php echo $dec;?>";
var currentTime=1;
var inRun=0;
function doDisabling()
{
	var e,km=0;
	e=document.getElementById("charDiv");
	if (e) km=e.querySelectorAll("path[clip-path]").length;
	document.getElementById("first").disabled=(currentStroke==0);
	document.getElementById("pred").disabled=(currentStroke==0);
	document.getElementById("next").disabled=(currentStroke==km);
	document.getElementById("last").disabled=(currentStroke==km);
	document.getElementById("stop").disabled=(inRun==0);
	document.getElementById("run").disabled=(inRun==1);
}
function resetRun()
{
	var k,km;
	inRun=0;
	km=currentRun.length;
	for(k=0;k<km;k++) clearTimeout(currentRun[k]);
}
function startAnim()
{
	// (re)start animation (just force a reflow)
	void(document.querySelector("svg.acjk").offsetHeight);
}
function doOne(n)
{
	var list,k,km;
	list=document.getElementById("charDiv").querySelectorAll("path[clip-path]");
	km=list.length;
	for(k=0;k<km;k++)
	{
		if (k<n) list[k].style.strokeDashoffset="0";
		else list[k].style.strokeDashoffset="3339";
		if (k==n) list[k].style.animation="zk var(--t) linear forwards 0s";
		else list[k].style.animation="none";
	}
	currentStroke=n+1;
	if (currentStroke==km) inRun=0;
	doDisabling();
	startAnim();
}
function doStop()
{
	resetRun();
	doDisabling();
}
function doRun()
{
	var e,s,list,k,km;
	resetRun();
	inRun=1;
	e=document.getElementById("charDiv");
	list=e.querySelectorAll("path[clip-path]");
	km=list.length;
	if (currentStroke==km) currentStroke=0;
	for(k=currentStroke;k<km;k++)
		currentRun[k]=setTimeout("doOne("+k+")",currentTime*1000*(k-currentStroke));
	doDisabling();
}
function doFirst()
{
	var e,list,k,km;
	resetRun();
	e=document.getElementById("charDiv");
	list=e.querySelectorAll("path[clip-path]");
	km=list.length;
	for(k=0;k<km;k++)
	{
		list[k].style.animation="none";
		list[k].style.strokeDashoffset="3339";
	}
	currentStroke=0;
	doDisabling();
}
function doPred()
{
	var e,list,k,km;
	resetRun();
	e=document.getElementById("charDiv");
	list=e.querySelectorAll("path[clip-path]");
	km=list.length;
	if (currentStroke>0)
	{
		currentStroke--;
		for(k=0;k<km;k++)
		{
			list[k].style.animation="none";
			if ((k+1)>currentStroke) list[k].style.strokeDashoffset="3339";
			else list[k].style.strokeDashoffset="0";
		}
	}
	doDisabling();
}
function doNext()
{
	resetRun();
	doOne(currentStroke);
}
function doLast()
{
	var e,list,k,km;
	resetRun();
	e=document.getElementById("charDiv");
	list=e.querySelectorAll("path[clip-path]");
	km=list.length;
	for(k=0;k<km;k++)
	{
		list[k].style.animation="none";
		list[k].style.strokeDashoffset="0";
	}
	currentStroke=km;
	doDisabling();
}
function whenLoad()
{
	inRun=0;
	doFirst();
	doRun();
}
window.addEventListener("load",whenLoad,false);
</script>
</body>
</html>
