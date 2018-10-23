<!doctype html>
<?php
function unichr($u)
{
	// return the UTF-8 char corresponding to the decimal unicode $u
    return mb_convert_encoding('&#' . intval($u) . ';', 'UTF-8', 'HTML-ENTITIES');
}
function decUnicode($u)
{
	// return the decimal unicode of UTF-8 char $u
	$len=strlen($u);
	if ($len==0) return 63;
	$r1=ord($u[0]);
	if ($len==1) return $r1;
	$r2=ord($u[1]);
	if ($len==2) return (($r1&31)<< 6)+($r2&63);
	$r3=ord($u[2]);
	if ($len==3) return (($r1&15)<<12)+(($r2&63)<< 6)+($r3&63);
	$r4=ord($u[3]);
	if ($len==4) return (($r1& 7)<<18)+(($r2&63)<<12)+(($r3&63)<<6)+($r4&63);
	return 63;
}
if (isset($_GET["lang"]))
{
	$lang=$_GET["lang"];
	if ($lang!="zh-hans") $lang="ja";
}
else $lang="ja";
if (isset($_GET["sample"]))
{
	$sample=$_GET["sample"];
	if (($sample!="animeSeveral")
		&&($sample!="number")
		&&($sample!="png")
		&&($sample!="radical")
		&&($sample!="red"))
	{
		$sample="animeOne";
	}
}
else $sample="animeOne";
if ($lang=="ja") {$dir="svgsJa";$defaultChar="28450";}
else {$dir="svgsZhHans";$defaultChar="27721";}
if (isset($_GET["dec"]))
{
	$dec=$_GET["dec"];
	if (!is_numeric($dec)) $dec=$defaultChar;
	else if (strlen($dec)!=5) $dec=$defaultChar;
}
else $dec=$defaultChar;
$char=unichr($dec);
if (isset($_GET["data"])) $data=$_GET["data"];
else $data="";
?>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="initial-scale=1.0,user-scalable=yes">
<style>
body {text-align:center;}
h2,ul {text-align:left;max-width:512px;}
h2 {margin:0 auto;}
ul {display:block;margin:1em auto;}
a {color:#000;}
a:visited {color:#666;}
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
	padding:0.25em;
}
.actionBtn:hover,
.actionBtn:active,
.actionBtn:focus {background:#c00;outline:0;}
.actionBtn:hover {cursor:pointer;}
.actionBtn:disabled {background:#ccc;cursor:default;}
.actionBtn::-moz-focus-inner {border:0;}
.selectorDiv
{
	margin:0 auto;
	text-align:left;
	max-width:512px;
}
label
{
	display:block;
	padding:0.25em 0 0.25em 2em;
}
input.charInput {font-size:2em;width:5.25em;text-align:center;}
</style>
<link rel="stylesheet" type="text/css" href="_css/autocomplete.css">
<title>AnimCJK - Sample selector</title>
</head>
<body>
<h1>AnimCJK - Sample selector</h1>
<h2>Simple samples</h2>
<form id="sampleForm" action="animeOne.php" method="post">
<div class="selectorDiv">
<p>Select a language:</p>
<label><input id="jaRadio" type="radio" name="lang" value="ja" onclick="setLang('ja',0);"> Japanese</label>
<label><input id="zhHansRadio" type="radio" name="lang" value="zh-hans" onclick="setLang('zh-hans',0);"> Simplified Chinese</label>
</div>
<div class="selectorDiv">
<p>Select a sample:</p>
<label><input id="animeOneRadio" type="radio" name="sample" value="animeOne" onclick="setSample('animeOne',0);"> Anime one</label>
<label><input id="animeSeveralRadio" type="radio" name="sample" value="animeSeveral" onclick="setSample('animeSeveral',0);"> Anime several</label>
<label><input id="numberRadio" type="radio" name="sample" value="number" onclick="setSample('number',0);"> Number</label>
<label><input id="pngRadio" type="radio" name="sample" value="png" onclick="setSample('png',0);"> PNG</label>
<label><input id="radicalRadio" type="radio" name="sample" value="radical" onclick="setSample('radical',0);"> Radical</label>
<label><input id="redRadio" type="radio" name="sample" value="red" onclick="setSample('red',0);"> Red</label>
</div>
<div class="selectorDiv">
<p>Enter one or several characters or a decimal unicode of one character
(for instance enter 19968 to display 一):</p>
<div class="autocomplete">
<input autocomplete="off" id="charInput" class="charInput" name="dec2" type="text" maxlength="5" value="<?php echo $data?$data:$dec;?>">
<input id="decHiddenInput" type="hidden" name="dec" value="">
<input id="dataHiddenInput" type="hidden" name="data" value="">
</div>
</div>
<button id="submitBtn" type="submit" class="actionBtn">Display <?php echo $char;?></button>
</form>
<h2>Other samples</h2>
<ul>
<li><a href="animatedGIF.html">Animated GIF image generator</a>
<li><a href="redPNG.html">Red PNG image generator</a>
</ul>
<footer>
<a href="../">Home</a>
- <a href="../licenses/COPYING.txt">Licences</a><br>
Copyright 2016-2018 - François Mizessyn
</footer>
<div id="debug"></div>
<script src="_js/autocomplete.js"></script>
<script>
// Create a customized autocomplete list of possible decimal unicode for the selected lang
<?php
echo "var dataJa=[];\n";
$a=scandir("../svgsJa");
foreach($a as $b)
	if (preg_match('/^(\d+)\.svg$/i',$b,$m)) echo "dataJa.push(\"".$m[1]."\");\n";
echo "var dataZhHans=[];\n";
$a=scandir("../svgsZhHans");
foreach($a as $b)
	if (preg_match('/^(\d+)\.svg$/i',$b,$m)) echo "dataZhHans.push(\"".$m[1]."\");\n";
?>
function debug(s,a)
{
	var e;
	if (e=document.getElementById("debug"));
		if (a) e.innerHTML+=s;
		else e.innerHTML=s;
}
function setSubmit()
{
	var val,found,data,dec,label,sample,k,km;
	lang=document.getElementsByTagName('html')[0].getAttribute('lang');
	val=document.getElementById("charInput").value;
	sample=document.querySelector('input[name="sample"]:checked').value;
	if (!val) dec="65311";
	else if (val.match(/^[0-9]+$/))
	{
		dec=val;
		data='';
	}
	else
	{
		dec=val.codePointAt(0)+"";
		data=val;
	}
	if (data&&(sample=="animeSeveral"))
	{
		km=data.length;
		for(k=0;k<km;k++)
		{
			dec=val.codePointAt(k)+"";
			if (lang=="ja") found=dataJa.includes(dec);
			else found=dataZhHans.includes(dec);
			if (!found) break;
		}
	}
	else
	{
		if (lang=="ja") found=dataJa.includes(dec);
		else found=dataZhHans.includes(dec);
	}
	if (!found) dec="65311";
	if (data&&(sample=="animeSeveral")) label="Display "+data;
	else label="Display "+String.fromCodePoint(parseInt(dec));
	document.getElementById('submitBtn').innerHTML=label
	document.getElementById('submitBtn').disabled=!found;
	document.getElementById('decHiddenInput').value=dec;
	document.getElementById('dataHiddenInput').value=data;
}
function setLang(lang,z)
{
	var dec,found;
	dec=document.getElementById("charInput").value;
	document.getElementsByTagName('html')[0].setAttribute('lang',lang);
	if (lang=="ja")
	{
		autocomplete(document.getElementById("charInput"),dataJa);
		found=dataJa.includes(dec);
		document.getElementById('submitBtn').disabled = !found;
	}
	else
	{
		autocomplete(document.getElementById("charInput"),dataZhHans);
		found=dataZhHans.includes(dec);
		document.getElementById('submitBtn').disabled=!found;
	}
	if (z)
	{
		switch(lang)
		{
			case 'zh-hans': document.getElementById('zhHansRadio').checked=true;break;
			default: document.getElementById('jaRadio').checked=true;
		}
	}
	else setSubmit();
}
function setSample(sample,z)
{
	document.getElementById("sampleForm").setAttribute('action',sample+".php");
	if (z)
	{
		switch(sample)
		{
			case 'animeSeveral': document.getElementById('animeSeveralRadio').checked=true;break;
			case 'number': document.getElementById('numberRadio').checked=true;break;
			case 'png': document.getElementById('pngRadio').checked=true;break;
			case 'radical': document.getElementById('radicalRadio').checked=true;break;
			case 'red': document.getElementById('redRadio').checked=true;break;
			default: document.getElementById('animeOneRadio').checked=true;
		}
	}
	else setSubmit();
}
document.getElementById("charInput").addEventListener("input",setSubmit);
document.getElementById("charInput").addEventListener("focus",setSubmit);
function whenLoad()
{
	setLang("<?php echo $lang;?>",1);
	setSample("<?php echo $sample;?>",1);
	setSubmit();
}
window.addEventListener("load",whenLoad,false);
</script>
</body>
</html>
