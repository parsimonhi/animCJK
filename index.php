<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="initial-scale=1.0,user-scalable=yes">
<meta name="description" content="To draw kana, kanji, jinmeyō Kanji and hanzi
stroke by stroke using AnimCJK SVG files">
<?php
include "samples/_php/getCharList.php";
$fs=256;
$loc=(($_SERVER['SERVER_NAME']=="localhost")?1:0);
?>
<style>
:root {--fs:<?=$fs?>px;}
body
{
	font-family:sans-serif;
	background:#fff;
	margin:0;
	padding:0;
}
a {color:#000;}
nav>*:not(:last-of-type):after {content:" - ";}
h1,nav,fieldset,#a {text-align:center;}
h2,h3,nav {margin:0;padding:0.5em;}
h2
{
	height:1.2em;
	line-height:1.2em;
	margin:0.5rem 1rem ;
	border-left:0.5rem solid #c00;
}
summary
{
	margin-left:1rem;
}
h3
{
	display:inline-block;
	position:relative;
	height:1.2em;
	line-height:1.2em;
	margin:0.25rem 0;
}
.important {color:#000;}
.very-important {color:#c00;}
p.instruction {padding-left:1rem;padding-right:1rem;}
#size
{
	font-size:1rem;
	display:block;
	width:13.5em;
	margin:0 auto;
}
#sizeMarks
{
	font-size:1rem;
	display:flex;
	margin:0 auto;
	width:17.5em;
	justify-content:space-between;
}
#sizeMarks option:not([label]) {display:none;}
#sizeMarks option {width:4em;text-align:center;margin:0;padding:0;}

#jaSection {display:block;}
#zhHansSection,
#zhHantSection {display:none;}
section.description p {margin:1em 1rem;}
.charList
{
	cursor:pointer;
	letter-spacing:0.25rem;
	color:#000;
	padding:0.5em 0.5rem;
	margin:0.25em 1rem;
	font-size:2rem;
	border:1px solid #ccc;
}

#data
{
	display:block;
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
	width:var(--fs);
	height:var(--fs);
	margin:1em auto;
}
#a {border:1px solid #ccc;color:#000;}
button:hover {cursor:pointer;}
#ok:focus,
#ok:active,
#ok:hover {background:#c00;}
fieldset {border:0;margin:0.5em;padding:0;}
label {display:inline-block;margin:0 0.5em;white-space:nowrap;}
input[type="radio"],
input[type="checkbox"] {margin:0 0.25em;}
#b
{
	display:none;
	width:var(--fs);
	min-height:9.5em;
	margin:1em auto;
}
#b.dico {display:block;}
#b.dico>div {margin:0 0.25em;padding-top:0.25em;text-align:left;line-height:1.25em;}
span.cjkChar {vertical-align:top;}

/* style for svg.acjk since style from svg file was removed when loading svg */
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
.grid {background:url('data:image/svg+xml,<svg viewBox="0 0 4 4" fill="none" stroke="%23ccc" xmlns="http://www.w3.org/2000/svg"><path vector-effect="non-scaling-stroke" d="M1 0V4M2 0V4M3 0V4M0 1H4M0 2H4M0 3H4M.4 .4H3.6V3.6H.4Z"/></svg>');}
svg.error {font-size:var(--fs);}

</style>
<title>AnimCJK Demo</title>
</head>
<body>
<h1>AnimCJK Demo</h1>
<nav>
<span><a href="https://github.com/parsimonhi/animCJK">Download</a></span><!--
--><span><a href="samples/">Samples</a></span>
</nav>
<fieldset>
<label><input id="jaRadio" type="radio" checked name="sectionSwitch" value="Ja" onclick="switchSection()">Japanese</label>
<label><input id="koRadio" type="radio" name="sectionSwitch" value="Ko" onclick="switchSection()">Korean</label>
<label><input id="zhHansRadio" type="radio" name="sectionSwitch" value="ZhHans" onclick="switchSection()">Simplifed Chinese</label>
<label><input id="zhHantRadio" type="radio" name="sectionSwitch" value="ZhHant" onclick="switchSection()">Traditional Chinese</label>
</fieldset>
<fieldset>
<label><input id="dico" type="checkbox" checked onclick="switchDico()">Dico</label>
<label><input id="grid" type="checkbox" onclick="switchGrid()">Grid</label>
<label><input id="numbers" type="checkbox" onclick="switchNumbers()">Stroke numbering</label>
<label><input id="xrays" type="checkbox" onclick="switchXrays()">X-rays</label>
</fieldset>
<fieldset>
<label><input id="size" type="range" list="sizeMarks" onclick="switchSize()" value="<?=$fs?>" step="64" min="0" max="1024"></label>
<datalist id="sizeMarks">
<option value="0" label="0 px"></option>
<option value="64"></option>
<option value="128"></option>
<option value="192"></option>
<option value="256"></option>
<option value="320"></option>
<option value="384"></option>
<option value="448"></option>
<option value="512" label="512 px"></option>
<option value="576"></option>
<option value="640"></option>
<option value="704"></option>
<option value="768"></option>
<option value="832"></option>
<option value="896"></option>
<option value="960"></option>
<option value="1024" label="1024 px"></option>
</datalist>
</fieldset>
<fieldset>
<input autocomplete="off" id="data" lang="ja" type="text" value="" maxlength="23" placeholder="Enter data here">
<p class="important instruction"><span class="very-important">Enter only one character</span> in the data field above or
<span class="very-important">click on a character</span> in the lists
at the bottom of the page.</p>
<button id="ok" type="button" onclick="ok()">Animate</button>
</fieldset>
<div id="a"></div>
<div id="b"></div>
<section class="description">
<h2>Description</h2>
<p>
AnimCJK contains SVG files to draw 
Japanese kana or kanji and simplified or traditional Chinese hanzi stroke by stroke.
</p>
<p>The Japanese repository contains "Kana" (177 characters),
"Jōyō kanji" (2136 characters), "Jinmeyō kanji" (863 characters) and some other characters ("Hyōgai kanji", "components" and "strokes").</p>
<p>The Korean repository contains some "Hanja".</p>
<p>The simplified Chinese repository contains "Commonly used hanzi" (7000 characters)
and some other characters ("Uncommon hanzi", "Traditional hanzi used with simplified hanzi", "components" and "strokes").
Note that "HSK hanzi" (2663 characters)
and "Frequently used hanzi" (3500 characters) are subsets of "Commonly used hanzi".</p>
<p>The traditional Chinese repository contains "HSK 1", "HSK 2", "HSK 3" and some other traditional hanzi.</p>
</section>
<?php
$q=[];
$q["ja"]=[
	["hiragana","Hiragana"],
	["katakana","Katakana"],
	["g1","Grade 1"],
	["g2","Grade 2"],
	["g3","Grade 3"],
	["g4","Grade 4"],
	["g5","Grade 5"],
	["g6","Grade 6"],
	["g7","Junior high school"],
	["g8","Jinmeiyō"],
	["g9","Hyōgai"],
	["gc","Components"],
	["stroke","Strokes"]];
$q["ko"]=[
	["hanja8","Hanja level 8"],
	["hanja7","Hanja level 7"]//,
	//["hanja6","Hanja level 6"]//,
	//["hanja5","Hanja level 5"]//,
	//["hanja4","Hanja level 4"]//,
	//"hanja3","Hanja level 3"]//,
	//["hanja2","Hanja level 2"]//,
	//["hanja1","Hanja level 1"]
	];
$q["zhHans"]=[
	["hsk1","HSK 1, simplified hanzi"],
	["hsk2","HSK 2, simplified hanzi"],
	["hsk3","HSK 3, simplified hanzi"],
	["hsk4","HSK 4, simplified hanzi"],
	["hsk5","HSK 5, simplified hanzi"],
	["hsk6","HSK 6, simplified hanzi"],
	["frequentNotHsk","Other frequently used hanzi"],
	["commonNotHskNorFrequent","Other commonly used hanzi"],
	["uncommon","Uncommon hanzi"],
	["traditional","Traditional hanzi"],
	["component","Components"],
	["stroke","Strokes"]];
$q["zhHant"]=[
	["traditional1","HSK 1, traditional hanzi"],
	["traditional2","HSK 2, traditional hanzi"],
	["traditional3","HSK 3, traditional hanzi"],
	["traditionalu","Uncommon traditional hanzi"]];

function navigation($lang)
{
	echo "<nav>";
	if ($lang=="ja")
	{
		echo "<span><a href=\"#jaHiragana\">Hiragana</a></span>";
		echo "<span><a href=\"#jaKatakana\">Katakana</a></span>";
		for ($k=1;$k<7;$k++)
			echo "<span><a href=\"#jaG".$k."\">Grade ".$k."</a></span>";
		echo "<span><a href=\"#jaG7\">Junior high school</a></span>";
		echo "<span><a href=\"#jaG8\">Jinmeiyō</a></span>";
		echo "<span><a href=\"#jaG9\">Hyōgai</a></span>";
		echo "<span><a href=\"#jaGc\">Components</a></span>";
		echo "<span><a href=\"#jaStroke\">Strokes</a></span>";
	}
	else if ($lang=="ko")
	{
		for ($k=8;$k>6;$k--)
			echo "<span><a href=\"#koHanja".$k."\">Level ".$k."</a></span>";
	}
	else if ($lang=="zhHans")
	{
		for ($k=1;$k<7;$k++)
			echo "<span><a href=\"#zhHansHsk".$k."\">HSK ".$k."</a></span>";
		echo "<span><a href=\"#zhHansFrequentNotHsk\">Frequent</a></span>";
		echo "<span><a href=\"#zhHansCommonNotHskNorFrequent\">Common</a></span>";
		echo "<span><a href=\"#zhHansUncommon\">Uncommon</a></span>";
		echo "<span><a href=\"#zhHanscomponent\">Components</a></span>";
		echo "<span><a href=\"#zhHansStroke\">Strokes</a></span>";
	}
	else if ($lang=="zhHant")
	{
		for ($k=1;$k<3;$k++)
			echo "<span><a href=\"#zhHantTraditional".$k."\">HSK ".$k." traditional</a></span>";
		echo "<span><a href=\"#zhHantTraditionalu\">Uncommon traditional</a></span>";
	}
	echo "<span><a href=\"#\">Top</a></span>";
	echo "<span><a href=\"#bottom\">Bottom</a></span>";
	echo "</nav>\n";
}
function langIso($section)
{
	if($section=="zhHans") return "zh-Hans";
	if($section=="zhHant") return "zh-Hant";
	if($section=="ko") return "ko";
	if($section=="ja") return "ja";
	return "en";
}
foreach(["ja","ko","zhHans","zhHant"] as $section)
{
	echo "<section id=\"".$section."Section\" lang=\"".langIso($section)."\">";
	$km=count($q[$section]);
	for($k=0;$k<$km;$k++)
	{
		$set=$q[$section][$k][0];
		$q[$section][$k][2]=getCharList($q[$section][$k][0]);
		$q[$section][$k][3]=mb_strlen($q[$section][$k][2]);
	}
	foreach($q[$section] as $a)
	{
		if($a[0]=="hiragana") echo "<h2>Kana</h2>";
		else if($a[0]=="g1") echo "<h2>Kanji</h2>";
		else if($a[0]=="gc") echo "<h2>Others</h2>";
		else if($a[0]=="hanja8") echo "<h2>Hanja (ko)</h2>";
		else if($a[0]=="hsk1") echo "<h2>HSK (zh-Hans)</h2>";
		else if($a[0]=="frequentNotHsk") echo "<h2>Other hanzi (zh-Hans)</h2>";
		else if($a[0]=="traditional1") echo "<h2>HSK traditional hanzi (zh-Hant)</h2>";
		else if($a[0]=="traditionalu") echo "<h2>Other traditional hanzi (zh-Hant)</h2>";
		$id=$section.ucfirst($a[0]);
		echo "<details open>";
		echo "<summary>";
		echo "<h3 id='".$id."'>".$a[1]." (".$a[3]." characters)</h3>\n";
		echo "</summary>";
		echo "<div class=\"charList\">".$a[2]."</div>\n";
		navigation($section);
		echo "</details>";
	}
	echo "</section>";
}
?>
<nav id="bottom">
<span><a href="licenses/COPYING.txt">Licences</a></span><!--
--><span><a href="https://github.com/parsimonhi/animCJK">Download</a></span><!--
--><span><a href="samples/">Samples</a></span>
</nav>
<script src="samples/_js/setNumbersAcjk.js"></script>
<script>
function getLang()
{
	let list=document.querySelectorAll('[name="sectionSwitch"]');
	for(e of list)
	{
		if(e.checked) return e.value;
	}
	return "Ja";
}
function cleanData(e)
{
	// don't use this function as a oninput handler because it disturbs asian language IME
	let data=e.value;
	// \u200B-\u200D\uFEFF are the zero-length characters
	data=data.replace(/[A-Za-z0-9+*.:,?!\s\u200B-\u200D\uFEFF]/g,'');
	// keep only the 1st character
	if (data.length) data=String.fromCodePoint(data.codePointAt(0));
	e.value=data;
}
function setSection()
{
	let langIso="";
	if (document.getElementById("jaRadio").checked)
	{
		langIso="ja";
		document.getElementById("jaSection").style.display="block";
		document.getElementById("koSection").style.display="none";
		document.getElementById("zhHansSection").style.display="none";
		document.getElementById("zhHantSection").style.display="none";
	}
	else if (document.getElementById("koRadio").checked)
	{
		langIso="ko";
		document.getElementById("jaSection").style.display="none";
		document.getElementById("koSection").style.display="block";
		document.getElementById("zhHansSection").style.display="none";
		document.getElementById("zhHantSection").style.display="none";
	}
	else if (document.getElementById("zhHansRadio").checked)
	{
		langIso="zh-hans";
		document.getElementById("jaSection").style.display="none";
		document.getElementById("koSection").style.display="none";
		document.getElementById("zhHansSection").style.display="block";
		document.getElementById("zhHantSection").style.display="none";
	}
	else if (document.getElementById("zhHantRadio").checked)
	{
		langIso="zh-hant";
		document.getElementById("jaSection").style.display="none";
		document.getElementById("koSection").style.display="none";
		document.getElementById("zhHansSection").style.display="none";
		document.getElementById("zhHantSection").style.display="block";
	}
	else
	{
		document.getElementById("jaSection").style.display="none";
		document.getElementById("koSection").style.display="none";
		document.getElementById("zhHansSection").style.display="none";
		document.getElementById("zhHantSection").style.display="none";
	}
	if(langIso) document.getElementById("data").setAttribute("lang",langIso);
	else  document.getElementById("data").removeAttribute("lang");
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
	let a,b,c,dec,dir,e,lang,langIso,options={cache:<?=$loc?>?"reload":"default"};
	e=document.getElementById("data");
	lang=getLang();
	langIso=e.getAttribute("lang");
	a=document.getElementById("a");
	b=document.getElementById("b");
	a.innerHTML="";
	b.innerHTML="";
	cleanData(e);
	c=e.value;
	if(c)
	{
		scrollToOk();
		dec=c.codePointAt(0);
		dir="svgs"+(((lang=="Ja")&&(dec>12352)&&(dec<12541))?"Kana":lang);
		fetch(dir+"/"+dec+".svg",options)
		.then(r=>{if(!r.ok) throw r.statusText;return r.text();})
		.then(r=>
			{
				if(r&&r.match(/<svg id="z/))
				{
					a.innerHTML=r.replace(/<style[\s\S]+\/style>\s/,"");
					switchNumbers();
					return true;
				}
				else
				{
					a.innerHTML=c+" not in "+dir+" repository!";
					return false;
				}
			})
		.catch(e=>a.innerHTML=e);
		options.method="POST";
		options.body=JSON.stringify({lang:langIso,data:c});
		fetch('getOneFromDico.php',options)
		.then(r=>{if(!r.ok) throw r.statusText;return r.text();})
		.then(r=>b.innerHTML=r)
		.catch(e=>b.innerHTML=e);
	}
}
function doIt(c)
{
	document.getElementById("data").value=c;
	ok();
}
function switchDico()
{
	let b=document.getElementById("b");
	let x=document.getElementById("dico").checked;
	if(x) b.classList.add("dico");
	else b.classList.remove("dico");
}
function switchGrid()
{
	let a=document.getElementById("a");
	let x=document.getElementById("grid").checked;
	if (x) a.classList.add("grid");
	else a.classList.remove("grid");
}
function switchNumbers()
{
	// setNumbers() is defined in setNumbersAcjk.js
	setNumbers(document.getElementById("numbers").checked);
}
function switchSection()
{
	setSection();
	ok();
}
function switchSize()
{
	let size=-(-document.getElementById("size").value);
	document.documentElement.style.setProperty('--fs',size+"px");
}
function switchXrays()
{
	let a=document.getElementById("a");
	let x=document.getElementById("xrays").checked;
	if(x) a.classList.add("xrays");
	else a.classList.remove("xrays");
}
function magic(ev)
{
	let p1,p2,sel;
	sel=window.getSelection();
	if (sel.toString()) return; // actual selection thus just return
	// to deal characters not in BMP, one has to recompute p
	// for...of can deal chars not in the BMP
	p1=sel.focusOffset;
	p2=0;
	for(let c of ev.target.innerHTML)
	{
		if(c!="\u200B") // ignore \u200B which is the zero-length character
		{
			p1-=c.length;
			p2++;
			if (p2>p1) // the user clicked c and p2 is the position of c in the string
			{
				doIt(c); // do something when user clicked c
				break;
			}
		}
	}
}
window.addEventListener("load",function()
{
	document.querySelectorAll('.charList').forEach(e=>
	{
		// insert a zero-length character between each characters
		e.innerHTML=e.innerHTML.replace(/(.)/ug,"$1\u200B");
		// add a handler to each .charList to do something when clicking on a character
		e.addEventListener("click",function(ev){magic(ev);});
	});
	// add a handler to #data input to do something when the user hits the return key
	document.getElementById("data").addEventListener("keyup",function(event)
	{
		event.preventDefault();
		if (event.keyCode==13) ok();
	});
	setSection();
	switchDico();
	switchGrid();
	switchSize();
	switchXrays();
});
</script>
</body>
</html>