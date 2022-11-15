<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="initial-scale=1.0,user-scalable=yes">
<meta name="description" content="To draw kana, jōyō kanji, jinmeyō Kanji,
and hanzi stroke by stroke, using AnimCJK SVG files">
<?php if (isset($_GET["fs"])) $fs=intVal($_GET["fs"]);else $fs=256;?>
<?php include "samples/_php/getCharList.php"; ?>
<style>
body
{
	font-size:1em;
	font-family:"arial",sans-serif;
	background:#fff;
	margin:0;
	padding:0;
}
h1,div.link,div.input,#a,footer {text-align:center;}
h2,h3,nav {margin:0;padding:0.5em;}
h2
{
	height:1.2em;
	line-height:1.2em;
	background:#fff;
	color:#000;
	margin:0.5rem 1rem ;
	border-left:0.5rem solid #c00;
}
h3
{
	position:relative;
	height:1.2em;
	line-height:1.2em;
	background:#fff;
	color:#000;
	padding-left:1.5rem;
	margin:0.25rem 0.5rem ;
}
h3:before
{
	position:absolute;
	top:0.5em;
	bottom:0.5em;
	left:0.5rem;
	height:0;
	content:'';
	border:0.5em solid transparent;
	border-left:0.5em solid #000;
}
nav
{
	background:#fff;
	color:#000;
	margin:0.25em 0.25rem;
}
div.link a {color:#000;}
nav a {color:#000;}
div.input {padding-top:1em;}
nav a {display:inline-block;padding:0.25em 0.25rem;}
.important {color:#000;}
.very-important {color:#c00;}
p.instruction {padding-left:1rem;padding-right:1rem;}

#joyoSection {display:block;}
#commonSection,
#traditionalCommonSection {display:none;}

section.description p {margin:1em 1rem;}
div.charList
{
	cursor:pointer;
	letter-spacing:0.25rem;
	color:#000;
	padding:0.5em 0.5rem;
	margin:0.25em 1rem;
	font-size:2rem;
	border:1px solid #ccc;
	font-family:'noto sans','arial',sans-serif;
}

#data
{
	display:block;
	font-family:'noto sans','arial',sans-serif;
	text-align:center;
	width:8em;
	font-size:2em;
	height:1.5em;
	line-height:1.5em;
	margin:0 auto;
	padding:0;
}
#ok
{	
	-webkit-appearance:none;
	margin-top:0.5em;
	font-size:2em;
	border:0;
	background:#999;
	color:#fff;
	border-radius:0.5em;
}
#a
{
	position:relative;
	width:256px;
	height:256px;
	margin:1em auto;
}
#a {border:1px solid #ccc;color:#000;}
#a.noBorder {border-color:transparent}
#ok:hover,
#joyoSection button:hover,
#commonSection button:hover {cursor:pointer;}
#ok:focus,
#ok:active,
#ok:hover {background:#c00;outline:none;}
#ok::-moz-focus-inner {border: 0;}

label {display:inline-block;white-space:normal;margin:0 0.5rem 1em 0.5rem;}
label input {vertical-align:text-bottom;}
#b
{
	width:256px;
	min-height:9.5em;
	margin:1em auto;
}
div.dico {margin:0 0.25em;padding-top:0.25em;text-align:left;line-height:1.25em;}
span.cjkChar {vertical-align:top;}
div.grid
{
	position:absolute;
	box-sizing:border-box;
	z-index:-1;
}
div.grid0
{
	left:0;
	top:0;
	bottom:0;
	right:0;
	border:1px solid #ccc;
}
div.grid1
{
	top:25%;
	left:0;
	width:100%;
	height:50%;
	border-top:1px solid #ccc;
	border-bottom:1px solid #ccc;
}
div.grid2
{
	top:0;
	left:25%;
	width:50%;
	height:100%;
	border-left:1px solid #ccc;
	border-right:1px solid #ccc;
}
div.grid3
{
	top:0;
	left:0;
	width:100%;
	height:50%;
	border-bottom:1px solid #ccc;
}
div.grid4
{
	top:0;
	left:0;
	width:50%;
	height:100%;
	border-right:1px solid #ccc;
}
div.grid5
{
	left:9.175%;
	top:9.175%;
	width:81.65%;
	height:81.65%;
	border:1px solid #ccc;
}

footer {padding-top:1em;}
footer a {color:#000;}
/* style for svg since style from svg file was removed when loading svg */
svg
{
	width:256px;
	height:256px;
}
svg.error {font-size:256px;}
@keyframes zk
{
	from {
		stroke-dashoffset:3339;
		stroke:#c00;
	}
	75% {
		stroke-dashoffset:0;
		stroke:#c00;
	}
	to {
		stroke-dashoffset:0;
		stroke:#000;
	}
}
svg.acjk path[clip-path]
{
	--t:0.8s;
	animation:zk var(--t) linear forwards var(--d);
	stroke-dasharray:3337;
	stroke-dashoffset:3339;
	stroke-width:128;
	stroke-linecap:round;
	fill:none;
	stroke:#000;
}
svg.acjk path[id] {fill:#ccc;}
.xrays svg.acjk path[clip-path] {stroke-width:6.4;}
.xrays svg.acjk path[id] {fill:#6666;}
</style>
<title>AnimCJK Demo</title>
<script src="samples/_js/codePoint.js"></script>
<script src="samples/_js/asvg.js"></script>
<script>
function cleanData(e)
{
	var data;
	data=e.value;
	// \u200B-\u200D\uFEFF are the zero-length characters
	data=data.replace(/[0-9+*.:?!\s\u200B-\u200D\uFEFF]/g,'');
	// keep only the 1st character
	// don't put this function as oninput function of input element
	// because it disturbs asian language IME
	if (data.length) data=String.fromCodePoint(data.codePointAt(0));
	e.value=data;
}
function setNumber(x)
{
	// this function adds/removes stroke numbers
	var go,g,list,k,km,l,a,c,e,cx,cy,cx1,cy1,cx2,cy2,d,sx,sy,fs=40,list0,km0;
	if (x)
	{
		// add numbers
		list0=document.querySelectorAll("svg.acjk path[id]");
		km0=list0.length;
		list=document.querySelectorAll("svg.acjk path:not([id])");
		km=list.length;
		l=0;
		go=0;
		for (k=0;k<km;k++)
		{
			// since several character svg can be in the page,
			// do not set g outside the loop
			g=list[k];
			while (g.tagName!="svg") g=g.parentNode;
			if (g!=go) {l=0;go=g;}
			// safer to test id of list0 than clip-path url of list for normal browsers
			// clip-path attributes were removed for pitiful browsers
			// so use list0 id to get the number of the path
			if (list0[k].getAttributeNS(null,'id').match(/d[0-9]+a?$/))
			{
				l++;
				if (list[k].hasAttributeNS(null,"data-median"))
					a=list[k].getAttributeNS(null,"data-median");
				else a=list[k].getAttributeNS(null,"d");
				a=a.replace(/([0-9])[-]/g,"$1 -");
				c=a.match(/M[ ]*([0-9.-]+)[ ,]+([0-9.-]+)[^0-9.-]+([0-9.-]+)[ ,]+([0-9.-]+)/);
				if (c&&c.length)
				{
					cx1=parseInt(c[1]);
					cy1=parseInt(c[2]);
					cx2=parseInt(c[3]);
					cy2=parseInt(c[4]);
					d=Math.sqrt((cy2-cy1)*(cy2-cy1)+(cx2-cx1)*(cx2-cx1));
					if (d)
					{
						cx=cx1+(cx2-cx1)*fs/d/2;
						cy=cy1+(cy2-cy1)*fs/d/2;
					}
					else
					{
						cx=cx1;
						cy=cy1;
					}
					if (cx<(fs+(fs>>3))) cx=fs+(fs>>3);
					if (cy<(fs+(fs>>3))) cy=fs+(fs>>3);
					sx=((k+1)>=10)?0.875:1;
					sy=-1;
					e=document.createElementNS('http://www.w3.org/2000/svg','circle');
					e.setAttributeNS(null,"cx",cx);
					e.setAttributeNS(null,"cy",cy);
					e.setAttributeNS(null,"r",fs);
					e.setAttributeNS(null,"stroke","#000");
					e.setAttributeNS(null,"fill","#fff");
					e.setAttributeNS(null,"stroke-width",Math.max(1,fs>>3));
					g.appendChild(e);
					e=document.createElementNS('http://www.w3.org/2000/svg','text');
					e.setAttributeNS(null,"x",cx);
					e.setAttributeNS(null,"y",cy+(fs>>1));
					e.setAttributeNS(null,"text-anchor","middle");
					e.setAttributeNS(null,"font-family","arial");
					e.setAttributeNS(null,"font-weight","normal");
					e.setAttributeNS(null,"fill","#000");
					e.setAttributeNS(null,"font-size",(fs>>1)*3);
					e.textContent=l;
					g.appendChild(e);
				}
			}
		}
	}
	else
	{
		// remove numbers
		list=document.querySelectorAll("svg.acjk circle, svg.acjk text");
		km=list.length;
		if (km) 
			for (k=0;k<km;k++)
			{
				g=list[k].parentNode; // must set g here
				g.removeChild(list[k]);
			}
	}
}
function setGrid(x)
{
	var a,e,list,k,km;
	a=document.getElementById("a");
	list=document.querySelectorAll("#a div.grid");
	km=list?list.length:0;
	if (x)
	{
		if (!km)
		{
			a.classList.add("noBorder");
			for (k=0;k<6;k++)
			{
				e=document.createElement('div');
				e.className="grid grid"+k;
				a.appendChild(e);
			}
		}
	}
	else
	{
		a.classList.remove("noBorder");
		for (k=0;k<km;k++) a.removeChild(list[k]);
	}
}
function setXrays(x)
{
	a=document.getElementById("a");
	if(x) a.classList.add("xrays");
	else a.classList.remove("xrays");
}
function setSection()
{
	var lang;
	if (document.getElementById("joyoRadio").checked)
	{
		lang="ja";
		document.getElementById("joyoSection").style.display="block";
		document.getElementById("commonSection").style.display="none";
		document.getElementById("traditionalCommonSection").style.display="none";
	}
	else if (document.getElementById("commonRadio").checked)
	{
		lang="zh-hans";
		document.getElementById("joyoSection").style.display="none";
		document.getElementById("commonSection").style.display="block";
		document.getElementById("traditionalCommonSection").style.display="none";
	}
	else
	{
		lang="zh-hant";
		document.getElementById("joyoSection").style.display="none";
		document.getElementById("commonSection").style.display="none";
		document.getElementById("traditionalCommonSection").style.display="block";
	}
	document.getElementById("data").setAttribute("lang",lang);
}
function hideSvg()
{
	var list,k,km;
	list=document.querySelectorAll("#a svg.acjk");
	if (list&&(km=list.length))
		for (k=0;k<km;k++) list[k].style.visible="none";
}
function scrollToOk()
{
	var top,height,okTop;
	top=document.getElementById('a').offsetTop;
	okTop=document.getElementById('ok').offsetTop;
	height=document.getElementById('a').getBoundingClientRect().height;
	hideSvg(); // cosmetic
	if (window.innerHeight>(top+height)) window.scrollTo(0,0);
    else window.scrollTo(0,okTop-8);
}
function ok()
{
	var data,xhr,xhr2,lang;
	scrollToOk();
	cleanData(document.getElementById("data"));
	data=document.getElementById("data").value;
	xhr=new XMLHttpRequest();
	xhr.onreadystatechange=function()
	{
		var s;
		if ((xhr.readyState==4)&&(xhr.status==200))
		{
			// remove style from svg
			s=xhr.responseText.replace(/<style[\s\S]+\/style>\s/,"");
			document.getElementById("a").innerHTML=s;
			setNumber(document.getElementById("number").checked);
			setGrid(document.getElementById("grid").checked);
			// hack for pitiful browser that cannot animate svg (require asvg.js)
			if (typeof asvg!='undefined') asvg.run('color');
        }
    };
    xhr.open("POST","getOneFromSvgs.php",true);
	xhr.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	lang=document.getElementById("data").getAttribute("lang");
	xhr.send("data="+encodeURIComponent(data)+"&lang="+lang);
	xhr2=new XMLHttpRequest();
	xhr2.onreadystatechange=function()
	{
		if ((xhr2.readyState==4)&&(xhr2.status==200))
		{
			document.getElementById("b").innerHTML=xhr2.responseText;
        }
    };
    xhr2.open("POST","getOneFromDico.php",true);
	xhr2.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	xhr2.send("data="+encodeURIComponent(data)+"&lang="+lang);
}
function doIt(c)
{
	document.getElementById("data").value=c;
	ok();
}
function switchNumber()
{
	setNumber(document.getElementById("number").checked);
}
function switchGrid()
{
	setGrid(document.getElementById("grid").checked);
}
function switchXrays()
{
	setXrays(document.getElementById("xrays").checked);
}
function switchSection()
{
	setSection();
	ok();
}
function switchSize(fs)
{
	var a,e=document.createElement("style");
	a="#a {width:"+fs+"px;height:"+fs+"px;}";
	a+="svg {width:"+fs+"px;height:"+fs+"px;}";
	a+="svg.error {font-size:"+fs+"px;}";
	a+="#b {width:"+fs+"px;}";
	e.type='text/css';
	if (e.styleSheet) e.styleSheet.cssText=a;
	else e.appendChild(document.createTextNode(a));
	document.getElementsByTagName('head')[0].appendChild(e);
}
</script>
</head>
<body>
<h1 id="top">AnimCJK Demo</h1>
<div class="link">
<a href="https://github.com/parsimonhi/animCJK">Download</a>
- <a href="samples/">Samples</a>
</div>
<div class="input">
<div class="sectionSwitch">
<label><input id="joyoRadio" type="radio" checked name="sectionSwitch" onclick="switchSection()"> Japanese (kana, jōyō and jinmeyō kanji)</label>
<label><input id="commonRadio" type="radio" name="sectionSwitch" onclick="switchSection()"> Simplifed Chinese (commonly used hanzi)</label>
<label><input id="traditionalCommonRadio" type="radio" name="sectionSwitch" onclick="switchSection()"> Traditional Chinese (HSK 1 hanzi only)</label>
</div>
<div class="sectionCheckBox">
<label for="number"><input id="number" type="checkbox" onclick="switchNumber()"> Stroke numbering</label>
<label for="grid"><input id="grid" type="checkbox" onclick="switchGrid()"> Grid</label>
<label for="xrays"><input id="xrays" type="checkbox" onclick="switchXrays()"> X-rays</label>
</div>
<div class="sectionSize">
<label><input id="fs128" type="radio"  name="sectionSize" onclick="switchSize(128)"> 128 px</label>
<label><input id="fs256" type="radio" checked name="sectionSize" onclick="switchSize(256)"> 256 px</label>
<label><input id="fs512" type="radio"  name="sectionSize" onclick="switchSize(512)"> 512 px</label>
<label><input id="fs1024" type="radio"  name="sectionSize" onclick="switchSize(1024)"> 1024 px</label>
</div>
<input id="data" lang="ja" type="text" value="" maxlength="23" placeholder="Enter data here">
<p class="important instruction"><span class="very-important">Enter only one character</span> in the data field above or
<span class="very-important">click on a character</span> in the lists
at the bottom of the page.</p>
<input id="ok" type="button" value="Animate" onclick="ok()">
</div>
<div id="a"></div>
<div id="b"></div>
<section class="description">
<h2>Description</h2>
<p>
AnimCJK contains SVG files to draw 
Japanese kana or kanji, and simplified or traditional Chinese hanzi stroke by stroke.
</p>
<p>The Japanese repository contains 3175 characters: "kana" (177 characters),
"jōyō kanji" (2136 characters), "jinmeyō Kanji" (862 characters) and some "hyōgai kanji".</p>
<p>The simplified Chinese repository contains 7000 characters ("Commonly used hanzi")
and some other characters ("Uncommon hanzi").
Note that "HSK hanzi" (2663 characters)
and "Frequently used hanzi" (3500 characters) are subsets of "Commonly used hanzi".</p>
<p>The traditional Chinese repository contains 197 characters ("HSK 1 hanzi").</p>
</section>
<?php
function navigation($lang)
{
	$lm=7;
	echo "<nav>";
	if ($lang=="Ja")
	{
		echo "<a href=\"#hiragana\">Hiragana</a>";
		echo "<a href=\"#katakana\">Katakana</a>";
		for ($l=0;$l<($lm-1);$l++)
		{
			echo "<a href=\"#g".($l+1)."\">Grade ".($l+1)."</a>";
		}
		echo "<a href=\"#g7\">Junior high school</a>";
		echo "<a href=\"#g8\">Jinmeiyō</a>";
		echo "<a href=\"#g9\">Hyōgai</a>";
	}
	else if ($lang=="ZhHans")
	{
		for ($l=0;$l<($lm-1);$l++)
		{
			echo "<a href=\"#hsk".($l+1)."\">HSK ".($l+1)."</a>";
		}
		echo "<a href=\"#frequentNotHsk\">Frequent</a>";
		echo "<a href=\"#commonNotHskNorFrequent\">Common</a>";
		echo "<a href=\"#others\">Others</a>";
	}
	else // $lang=="ZhHant"
	{
		$lm=2;
		for ($l=0;$l<($lm-1);$l++)
		{
			echo "<a href=\"#hsk".($l+1)."\">HSK ".($l+1)."</a>";
		}
		//echo "<a href=\"#traditionalFrequentNotHsk\">Frequent</a>";
		//echo "<a href=\"#traditionalCommonNotHskNorFrequent\">Common</a>";
	}
	echo "<a href=\"#top\">Top</a>";
	echo "<a href=\"#bottom\">Bottom</a>";
	echo "</nav>\n";
}
?>
<section class="c" id="joyoSection" lang="ja">
<script>
function magic(e)
{
	// to select one character in a text
	// each character has to be separated by a zero-length character
	var s,p1,p2,a,k,km,sel;
	sel=window.getSelection();
	if (sel.toString()) return;
    s=e.innerHTML;
    p1=window.getSelection().focusOffset;
    a=s.split("\u200B"); // the separator is the zero-length character
    // deal characters not in BMP => has to recompute p
    // note: for...of can deal not BMP chars
    //       but it doesn't work everywhere (on ie for instance)
    p2=0;
    km=a.length;
    for (k=0;k<km;k++)
    {
    	p2+=a[k].length+1;
    	if (p2>p1) break;
    }
    doIt(a[k]);
}
</script>
<?php
$a=array();
$b="";
$lm=9;
for ($l=0;$l<$lm;$l++)
{
	$a[$l]=getCharList("g".($l+1));
	$b.=$a[$l];
	if ($l==6) $kmJoyo=mb_strlen($b,'UTF-8');
	else if ($l==7) $kmJinmeyo=mb_strlen($a[$l],'UTF-8');
	else if ($l==8) $kmHyogai=mb_strlen($a[$l],'UTF-8');
}
$km=mb_strlen($b,'UTF-8');
$hiragana=getCharList("hiragana");
$kmHiragana=mb_strlen($hiragana,'UTF-8');
$katakana=getCharList("katakana");
$kmKatakana=mb_strlen($katakana,'UTF-8');
$kmKana=$kmHiragana+$kmKatakana;
echo "<h2>Kana (".$kmKana." characters)</h2>\n";
echo "<h3 id='hiragana'>Hiragana (".$kmHiragana." characters)</h3>\n";
echo "<div class=\"charList\" onclick=\"magic(this)\">".preg_replace("/(.)/u","$1\xE2\x80\x8B",$hiragana)."</div>\n";
navigation("Ja");
echo "<h3 id='katakana'>Katakana (".$kmKatakana." characters)</h3>\n";
echo "<div class=\"charList\" onclick=\"magic(this)\">".preg_replace("/(.)/u","$1\xE2\x80\x8B",$katakana)."</div>\n";
navigation("Ja");
echo "<h2>Jōyō kanji (".$kmJoyo." characters)</h2>\n";
for ($l=0;$l<$lm;$l++)
{
	$km=mb_strlen($a[$l],'UTF-8');
	if ($l<6) echo "<h3 id='g".($l+1)."'>Grade ".($l+1)." (".$km." characters)</h3>\n";
	else if ($l==6) echo "<h3 id='g7'>Junior high school (".$km." characters)</h3>\n";
	else if ($l==7) echo "<h2 id='g8'>Jinmeiyō (".$kmJinmeyo." characters)</h2>\n";
	else echo "<h2 id='g9'>Hyōgai (".$kmHyogai." characters)</h2>\n";
	echo "<div class=\"charList\" onclick=\"magic(this)\">".preg_replace("/(.)/u","$1\xE2\x80\x8B",$a[$l])."</div>\n";
	navigation("Ja");
}
?>
</section>
<section class="c" id="commonSection" lang="zh-Hans">
<?php
$a=array();
$b="";
$lm=6;
for ($l=0;$l<$lm;$l++)
{
	$a[$l]=getCharList("hsk".($l+1));
	$b.=$a[$l];
}
$kmHsk=mb_strlen($b,'UTF-8');
$a[$lm]=getCharList("frequentNotHsk");
$kmFrequentNotHsk=mb_strlen($a[$lm],'UTF-8');
$a[$lm+1]=getCharList("commonNotHskNorFrequent");
$kmCommonNotHskNorFrequent=mb_strlen($a[$lm+1],'UTF-8');
$a[$lm+2]=getCharList("uncommon");
$kmUncommon=mb_strlen($a[$lm+2],'UTF-8');
$b.=$a[$lm];
$b.=$a[$lm+1];
$b.=$a[$lm+2];
$km=mb_strlen($b,'UTF-8');
echo "<h2>HSK hanzi (".$kmHsk." characters)</h2>\n";
for ($l=0;$l<=($lm+2);$l++)
{
	$km=mb_strlen($a[$l],'UTF-8');
	if ($l<6) echo "<h3 id='hsk".($l+1)."'>HSK ".($l+1)." (".$km." characters)</h3>\n";
	else if ($l==$lm) echo "<h2 id='frequentNotHsk'>Frequently used hanzi not in HSK (".$kmFrequentNotHsk." characters)</h2>\n";
	else if ($l==($lm+1)) echo "<h2 id='commonNotHskNorFrequent'>Commonly used hanzi not in HSK nor frequently used (".$kmCommonNotHskNorFrequent." characters)</h2>\n";
	else echo "<h2 id='others'>Other hanzi (".$kmUncommon." characters)</h2>\n";
	echo "<div class=\"charList\" onclick=\"magic(this)\">".preg_replace("/(.)/u","$1\xE2\x80\x8B",$a[$l])."</div>\n";
	navigation("ZhHans");
}
?>
</section>
<section class="c" id="traditionalCommonSection" lang="zh-Hant">
<?php
$a=array();
$b="";
$lm=1;
for ($l=0;$l<$lm;$l++)
{
	$a[$l]=getCharList("traditional".($l+1));
	$b.=$a[$l];
}
$kmTraditionalHsk=mb_strlen($b,'UTF-8');
//$a[$lm]=getCharList("traditionalFrequentNotHsk");
//$kmTraditionalFrequentNotHsk=mb_strlen($a[$lm],'UTF-8');
//$a[$lm+1]=getCharList("traditionalCommonNotHskNorFrequent");
//$kmTraditionalCommonNotHskNorFrequent=mb_strlen($a[$lm+1],'UTF-8');
//$b.=$a[$lm+1];
$km=mb_strlen($b,'UTF-8');
echo "<h2>HSK traditional hanzi (".$kmTraditionalHsk." characters)</h2>\n";
//for ($l=0;$l<=($lm+1);$l++)
for ($l=0;$l<$lm;$l++)
{
	$km=mb_strlen($a[$l],'UTF-8');
	if ($l<6) echo "<h3 id='traditional".($l+1)."'>HSK ".($l+1)." traditional (".$km." characters)</h3>\n";
	//else if ($l==$lm) echo "<h2 id='traditionalFrequentNotHsk'>Frequently used traditional hanzi not in HSK (".$kmTraditionalFrequentNotHsk." characters)</h2>\n";
	//else echo "<h2 id='traditionalCommonNotHskNorFrequent'>Commonly used traditional hanzi not in HSK nor frequently used (".$kmTraditionalCommonNotHskNorFrequent." characters)</h2>\n";
	echo "<div class=\"charList\" onclick=\"magic(this)\">".preg_replace("/(.)/u","$1\xE2\x80\x8B",$a[$l])."</div>\n";
	navigation("ZhHant");
}
?>
</section>
<footer>
<div id="bottom" class="link"><a href="#top">Top</a></div>
<a href="licenses/COPYING.txt">Licences</a>
- <a href="https://github.com/parsimonhi/animCJK">Download</a>
- <a href="samples/">Samples</a>
</footer>
<script>
document.getElementById("data").addEventListener("keyup",function(event) {
	event.preventDefault();
	if (event.keyCode==13) ok();
});
</script>
</body>
</html>