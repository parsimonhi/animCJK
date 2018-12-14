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
	if ($_GET["lang"]=="zh-hant") $lang="zh-hant";
	else if ($_GET["lang"]=="zh-hans") $lang="zh-hans";
	else $lang="ja";
}
else $lang="ja";
if ($lang=="zh-hant") {$dir="svgsZhHant";$defaultCharDec="28450";}
else if ($lang=="zh-hans") {$dir="svgsZhHans";$defaultCharDec="27721";}
else {$dir="svgsJa";$defaultCharDec="28450";}
if (isset($_GET["sample"]))
{
	if (($_GET["sample"]=="card")
		||($_GET["sample"]=="color")
		||($_GET["sample"]=="infinite")
		||($_GET["sample"]=="number")
		||($_GET["sample"]=="radical")
		||($_GET["sample"]=="red")
		||($_GET["sample"]=="size")
		||($_GET["sample"]=="speed")) $sample=$_GET["sample"];
	else $sample="animeOne";
}
else $sample="animeOne";
if (isset($_GET["dec"]))
{
	$dec=$_GET["dec"];
	if (!is_numeric($dec)) $dec=$defaultCharDec;
	else
	{
		$declen=strlen($dec);
		if (($declen!=5)&&($declen!=6)) $dec=$defaultCharDec;
	}
}
else if (isset($_GET["data"])) $dec=decUnicode($_GET["data"]);
else $dec=$defaultCharDec;
$data=unichr($dec);
echo "<!-- ".$data." -->\n";
?>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="initial-scale=1.0,user-scalable=yes">
<style>
* {box-sizing:border-box;}
body {text-align:center;}
h2,ul,form {text-align:left;max-width:512px;}
h2 {margin:0 auto;padding:0;}
form {margin:1em auto;border:1px solid #ccc;padding:1em;}
ul {display:block;border:1px solid #ccc;margin:1em auto;padding:1em;padding-left:2em;}
a {color:#000;}
a:visited {color:#666;}
.actionBtn
{
	box-sizing:border-box;
	-webkit-appearance:none;
	margin-top:0.25em;
	font-size:1.5em;
	height:2em;
	line-height:2em;
	border:0;
	background:#999;
	color:#fff;
	border-radius:0.5em;
	margin:0.5em;
	padding:0 0.25em;
	vertical-align:middle;
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
.submitBtnDiv
{
	height:4em;
	line-height:4em;
	text-align:center;
}
label
{
	display:block;
	padding:0.25em 0 0.25em 2em;
}
input.charInput
{
	box-sizing:border-box;
	font-size:2em;
	height:1.5em;
	line-height:1.5em;
	padding-top:0;
	padding-bottom:0;
	width:5em;
	text-align:center;
	vertical-align:middle;
}
#debug {color:#000;margin-top:0.5em;}
#debug.nok {color:#c00;}
</style>
<title>AnimCJK - Sample selector</title>
<script src="_js/codePoint.js"></script>
</head>
<body>
<h1>AnimCJK - Sample selector</h1>
<h2>Basic samples</h2>
<form id="sampleForm" action="animeOne.php" method="post">
<div class="selectorDiv">
<p>Select a language:</p>
<label><input id="jaRadio" type="radio" name="lang" value="ja" onclick="setLang('ja',0);"> Japanese</label>
<label><input id="zhHansRadio" type="radio" name="lang" value="zh-hans" onclick="setLang('zh-hans',0);"> Simplified Chinese</label>
<label><input id="zhHantRadio" type="radio" name="lang" value="zh-hant" onclick="setLang('zh-hant',0);"> Traditional Chinese</label>
</div>
<div class="selectorDiv">
<p>Select a sample:</p>
<label><input id="animeOneRadio" type="radio" name="sample" value="animeOne" onclick="setSample('animeOne',0);"> Anime one</label>
<label><input id="cardRadio" type="radio" name="sample" value="card" onclick="setSample('card',0);"> Card</label>
<label><input id="colorRadio" type="radio" name="sample" value="color" onclick="setSample('color',0);"> Color</label>
<label><input id="infiniteRadio" type="radio" name="sample" value="infinite" onclick="setSample('infinite',0);"> Infinite</label>
<label><input id="numberRadio" type="radio" name="sample" value="number" onclick="setSample('number',0);"> Number</label>
<label><input id="radicalRadio" type="radio" name="sample" value="radical" onclick="setSample('radical',0);"> Radical</label>
<label><input id="redRadio" type="radio" name="sample" value="red" onclick="setSample('red',0);"> Red</label>
<label><input id="sizeRadio" type="radio" name="sample" value="size" onclick="setSample('size',0);"> Size</label>
<label><input id="speedRadio" type="radio" name="sample" value="speed" onclick="setSample('speed',0);"> Speed</label>
</div>
<div class="selectorDiv">
<p>Enter one character in the data field below:</p>
<div lang="<?php echo $lang;?>" class="submitBtnDiv">
<input autocomplete="off" id="charInput" class="charInput" name="val" type="text" maxlength="4" value="<?php echo $data;?>">
<input id="decHiddenInput" type="hidden" name="dec" value="">
<input id="dataHiddenInput" type="hidden" name="data" value="">
<button id="submitBtn" type="submit" class="actionBtn">Display <?php echo $data;?></button>
</div>
<div id="debug"></div>
</div>
</form>
<h2>Other samples</h2>
<ul>
<li><a href="animeSeveral.html">Animation of several characters</a>
<li><a href="animatedGIF.html">Animated GIF image generator</a>
<li><a href="redPNG.html">Red PNG image generator</a>
</ul>
<footer>
<a href="../">Home</a>
- <a href="../licenses/COPYING.txt">Licences</a>
</footer>
<script>
// Create a list of possible decimal unicode for the selected lang
<?php
echo "var dataJa=[];\n";
$a=scandir("../svgsJa");
foreach($a as $b)
	if (preg_match('/^(\d+)\.svg$/i',$b,$m)) echo "dataJa.push(\"".$m[1]."\");\n";
echo "var dataZhHans=[];\n";
$a=scandir("../svgsZhHans");
foreach($a as $b)
	if (preg_match('/^(\d+)\.svg$/i',$b,$m)) echo "dataZhHans.push(\"".$m[1]."\");\n";
echo "var dataZhHant=[];\n";
$a=scandir("../svgsZhHant");
foreach($a as $b)
	if (preg_match('/^(\d+)\.svg$/i',$b,$m)) echo "dataZhHant.push(\"".$m[1]."\");\n";
?>
function debug(s,a,c)
{
	var e;
	if (e=document.getElementById("debug"));
	{
		if (a) e.innerHTML+=s;
		else e.innerHTML=s;
		if (c) e.className=c;
	}
}
function setSubmit()
{
	var lang,label,val,dec,data,found;
	lang=document.getElementById("charInput").parentNode.getAttribute('lang');
	val=document.getElementById("charInput").value;
	// remove zero-length characters U+200B if any
	// happen frequently when copying-pasting part of our char lists
	val=val.replace("\u200B",""); // the separator is the zero-length character
	document.getElementById("charInput").value=val;
	if (!val) dec="65311";
	else dec=val.codePointAt(0)+"";
	// use Array.indexOf instead of Array.includes for a better compatibility
	if (lang=="zh-hant") found=(dataZhHant.indexOf(dec)>=0);
	else if (lang=="zh-hans") found=(dataZhHans.indexOf(dec)>=0);
	else found=(dataJa.indexOf(dec)>=0);
	if (!found) dec="65311";
	data=String.fromCodePoint(parseInt(dec));
	label="Display "+data;
	document.getElementById('submitBtn').innerHTML=label;
	document.getElementById('submitBtn').disabled=!found;
	document.getElementById('decHiddenInput').value=dec;
	document.getElementById('dataHiddenInput').value=data;
	if (lang=="zh-hant") language="traditional Chinese";
	else if (lang=="zh-hans") language="simplified Chinese";
	else language="Japanese";
	if (found)
	{
		if (data==val) debug("Character found in our "+language+" repository!",0,"ok");
		else debug("1st character found in our "+language+" repository (others ignored)!",0,"ok");
	}
	else debug("Character not found in our "+language+" repository!",0,"nok");
}
function setLang(lang,z)
{
	if (z)
	{
		switch(lang)
		{
			case 'zh-hant': document.getElementById('zhHantRadio').checked=true;break;
			case 'zh-hans': document.getElementById('zhHansRadio').checked=true;break;
			default: document.getElementById('jaRadio').checked=true;
		}
	}
	else
	{
		document.getElementById("charInput").parentNode.setAttribute('lang',lang);
		setSubmit();
	}
}
function setSample(sample,z)
{
	document.getElementById("sampleForm").setAttribute('action',sample+".php");
	if (z)
	{
		switch(sample)
		{
			case 'card': document.getElementById('cardRadio').checked=true;break;
			case 'color': document.getElementById('colorRadio').checked=true;break;
			case 'infinite': document.getElementById('infiniteRadio').checked=true;break;
			case 'number': document.getElementById('numberRadio').checked=true;break;
			case 'radical': document.getElementById('radicalRadio').checked=true;break;
			case 'red': document.getElementById('redRadio').checked=true;break;
			case 'size': document.getElementById('sizeRadio').checked=true;break;
			case 'speed': document.getElementById('speedRadio').checked=true;break;
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
