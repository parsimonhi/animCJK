<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="initial-scale=1.0,user-scalable=yes">
<meta name="description" content="To draw kana, kanji, jinmeyō Kanji and hanzi
stroke by stroke using AnimCJK SVG files">
<?php
include_once __DIR__."/samples/_php/getCharList.php";
$loc=(($_SERVER['SERVER_NAME']=="localhost")?1:0);
?>
<style>
:root
{
	--highlight-color:#c00;
	--speed:1;
	--size:256px;
}
body
{
	font-family:sans-serif;
	background:#fff;
	margin:1rem;
	padding:0;
}
:focus-visible
{
	outline:2px solid #07f7;
	outline-offset:1px;
}
a {color:#000;}
a {display:inline-block;margin:0.25rem;padding:0.25rem;}
h1,nav,fieldset,#a {text-align:center;}
nav {margin:0.5rem 0;}
h2
{
	margin:0.5rem 0;
	border-left:0.5rem solid #c00;
	padding:0 0 0 0.75rem;
}
summary
{
	margin:0;
	padding:0;
}
h3
{
	display:inline;
	margin:0;
	padding:0.5rem;
}
.important {color:#000;}
.very-important {color:#c00;}
p.instruction {padding-left:1rem;padding-right:1rem;}
[name="size"]
{
	font-size:1rem;
	display:block;
	width:14rem;
	margin:0 auto;
}
#sizeMarks
{
	font-size:1rem;
	display:flex;
	margin:0 auto;
	width:14rem;
	justify-content:space-between;
}
#sizeMarks option:not([label]) {display:none;}
#sizeMarks option {width:3rem;text-align:center;margin:0;padding:0;}

#jaSection,
#koSection,
#zhHansSection,
#zhHantSection {display:none;}
body:has([name="section"][value="Ja"]:checked) #jaSection {display:block;}
body:has([name="section"][value="Ko"]:checked) #koSection {display:block;}
body:has([name="section"][value="ZhHans"]:checked) #zhHansSection {display:block;}
body:has([name="section"][value="ZhHant"]:checked) #zhHantSection {display:block;}
section.description p {margin:1rem 1rem;}
.charList
{
	cursor:pointer;
	letter-spacing:0.25rem;
	color:#000;
	padding:0.5rem;
	margin:0.25rem 1rem;
	font-size:2rem;
	border:1px solid #ccc;
	text-align:left;
}

#data
{
	display:block;
	text-align:center;
	width:8rem;
	font-size:2rem;
	height:3rem;
	line-height:3rem;
	margin:0 auto;
	padding:0;
	border:1px solid #0003;
}
#ok
{
	-webkit-appearance:none;
	appearance:none;
	margin-top:0.5rem;
	padding:0 0.5rem;
	font-size:2rem;
	height:3.5rem;
	line-height:3.5rem;
	border:0;
	background:#c00;
	color:#fff;
	border-radius:0.5rem;
}
button:hover {cursor:pointer;}
fieldset {border:0;margin:0.5rem;padding:0;}
label {display:inline-block;margin:0 0.5rem;white-space:nowrap;}
input[type="radio"],
input[type="checkbox"] {margin:0 0.25rem;}

#output
{
	list-style-type:none;
	margin:0;
	padding:0.5rem 0;
}
#output li
{
	max-width:calc(var(--size) + 2px);
	margin:0.5rem auto;
	padding:0;
}
#output dl, #output dt, #output dd
{
	margin:0;
	padding:0;
}
#output svg
{
	display:block;
	border:1px solid #0003;
}
#output svg text
{
	fill:var(--highlight-color);
}
body:has([name="grid"]:checked) #output svg
{
	background:url('data:image/svg+xml,<svg viewBox="0 0 4 4" fill="none" stroke="%230003" xmlns="http://www.w3.org/2000/svg"><path vector-effect="non-scaling-stroke" d="M1 0V4M2 0V4M3 0V4M0 1H4M0 2H4M0 3H4M.4 .4H3.6V3.6H.4Z"/></svg>');
}
#output dl
{
	display:none;
}
body:has([name="dico"]:checked) #output dl
{
	display:block;
	text-align:left;
	overflow:auto;
	margin:0;
	padding:0;
	line-height:1.5rem;
}
body:has([name="dico"]:checked) #output dt:not(:first-of-type)
{
	float:left;
	margin-right:0.25rem;
}
body:has([name="dico"]:checked) #output dt:not(:first-of-type)::after
{
	content:":";
}
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
body:has([name="xrays"]:checked) svg.acjk path[clip-path] {stroke:var(--highlight-color);stroke-width:6.4;}
body:has([name="xrays"]:checked) svg.acjk path[id] {fill:#6666;}

</style>
<title>AnimCJK Demo</title>
</head>
<body>
<h1>AnimCJK Demo</h1>
<nav>
<a href="https://github.com/parsimonhi/animCJK">Download</a>
<a href="samples/">Samples</a>
</nav>
<fieldset>
<label><input id="jaRadio" type="radio" name="section" value="Ja" data-lang="ja">Japanese</label>
<label><input id="koRadio" type="radio" name="section" value="Ko" data-lang="ko">Korean</label>
<label><input id="zhHansRadio" type="radio" name="section" value="ZhHans" data-lang="zh-Hans">Simplifed Chinese</label>
<label><input id="zhHantRadio" type="radio" name="section" value="ZhHant" data-lang="zh-Hant">Traditional Chinese</label>
</fieldset>
<fieldset>
<label><input name="dico" type="checkbox">Dico</label>
<label><input name="grid" type="checkbox">Grid</label>
<label><input name="numbers" type="checkbox">Numbers</label>
<label><input name="xrays" type="checkbox">X-rays</label>
</fieldset>
<fieldset>
<label>Size <input name="size" type="range" list="sizeMarks" value="256" step="64" min="64" max="960"></label>
<datalist id="sizeMarks">
<option value="128" label="128"></option>
<option value="256"></option>
<option value="384"></option>
<option value="512" label="512"></option>
<option value="640"></option>
<option value="768"></option>
<option value="896" label="896"></option>
</datalist>
</fieldset>
<fieldset>
<input autocomplete="off" id="data" lang="ja" type="text" value="" maxlength="23" placeholder="Enter data here">
<p class="important instruction"><span class="very-important">Enter only one character</span> in the data field above or
<span class="very-important">click on a character</span> in the lists
at the bottom of the page.</p>
<button id="ok" type="button" onclick="ok()">Animate</button>
</fieldset>
<ul id="output"></ul>
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
<p>The traditional Chinese repository contains "HSK v3 level 1", "HSK v3 level 2" and some other traditional hanzi.</p>
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
	["g9","Some hyōgai"],
	["gc","Some components"],
	["stroke","Strokes"]
	];
$q["ko"]=[
	["hanja8","Hanja level 8"],
	["hanja7","Hanja level 7"],
	["hanja6","Hanja level 6"],
	["hanja5","Hanja level 5"],
	["hanja4","Hanja level 4"],
	//["hanja3","Hanja level 3"],
	//["hanja2","Hanja level 2"],
	//["hanja1","Hanja level 1"],
	["ku","Some uncommon hanja"],
	["kc","Some components"]
	];
$q["zhHans"]=[
	["hsk31","HSK v3 level 1, simplified hanzi"],
	["hsk32","HSK v3 level 2, simplified hanzi"],
	["hsk33","HSK v3 level 3, simplified hanzi"],
	["hsk34","HSK v3 level 4, simplified hanzi"],
	["hsk35","HSK v3 level 5, simplified hanzi"],
	["hsk36","HSK v3 level 6, simplified hanzi"],
	["hsk37","HSK v3 level 7, simplified hanzi"],
	["hsk38","HSK v3 level 8, simplified hanzi"],
	["hsk39","HSK v3 level 9, simplified hanzi"],
	["frequentNotHsk3","Other frequently used hanzi"],
	["commonNotHsk3NorFrequent","Other commonly used hanzi"],
	["uncommon","Some uncommon hanzi"],
	["traditional","Some traditional hanzi when used with simplified hanzi"],
	["component","Some components"],
	["stroke","Strokes"]
	];
$q["zhHant"]=[
	["t31","HSK v3 level 1, traditional hanzi"],
	["t32","HSK v3 level 2, traditional hanzi"],
	//["t33","HSK v3 level 3, traditional hanzi"],
	//["t34","HSK v3 level 4, traditional hanzi"],
	//["t35","HSK v3 level 5, traditional hanzi"],
	//["t36","HSK v3 level 6, traditional hanzi"],
	//["t37","HSK v3 level 7, traditional hanzi"],
	//["t38","HSK v3 level 8, traditional hanzi"],
	//["t39","HSK v3 level 9, traditional hanzi"],
	["tu","Some uncommon traditional hanzi"],
	["tc","Some components"]
	];

function navigation($lang)
{
	echo "<nav>";
	if ($lang=="ja")
	{
		echo "<a href=\"#jaHiragana\">Hiragana</a>";
		echo "<a href=\"#jaKatakana\">Katakana</a>";
		for ($k=1;$k<7;$k++)
			echo "<a href=\"#jaG".$k."\">Grade ".$k."</a>";
		echo "<a href=\"#jaG7\">Junior high school</a>";
		echo "<a href=\"#jaG8\">Jinmeiyō</a>";
		echo "<a href=\"#jaG9\">Hyōgai</a>";
		echo "<a href=\"#jaGc\">Components</a>";
		echo "<a href=\"#jaStroke\">Strokes</a>";
	}
	else if ($lang=="ko")
	{
		for ($k=8;$k>3;$k--)
			echo "<a href=\"#koHanja".$k."\">Level ".$k."</a>";
	}
	else if ($lang=="zhHans")
	{
		for ($k=1;$k<10;$k++)
			echo "<a href=\"#zhHansHsk3".$k."\">HSK ".$k."</a>";
		echo "<a href=\"#zhHansFrequentNotHsk3\">Frequent</a>";
		echo "<a href=\"#zhHansCommonNotHsk3NorFrequent\">Common</a>";
		echo "<a href=\"#zhHansUncommon\">Uncommon</a>";
		echo "<a href=\"#zhHanscomponent\">Components</a>";
		echo "<a href=\"#zhHansStroke\">Strokes</a>";
	}
	else if ($lang=="zhHant")
	{
		for ($k=1;$k<3;$k++)
			echo "<a href=\"#zhHantTraditional".$k."\">HSK ".$k." traditional</a>";
		echo "<a href=\"#zhHantTraditionalu\">Uncommon traditional</a>";
	}
	echo "<a href=\"#\">Top</a>";
	echo "<a href=\"#bottom\">Bottom</a>";
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
		else if($a[0]=="hsk31") echo "<h2>HSK v3 (zh-Hans)</h2>";
		else if($a[0]=="frequentNotHsk3") echo "<h2>Others (zh-Hans)</h2>";
		else if($a[0]=="t31") echo "<h2>HSK v3 traditional hanzi (zh-Hant)</h2>";
		else if($a[0]=="tu") echo "<h2>Others (zh-Hant)</h2>";
		$id=$section.ucfirst($a[0]);
		echo "<details open>";
		echo "<summary>";
		echo "<h3 id='".$id."'>".$a[1]." (".$a[3]." characters)</h3>\n";
		echo "</summary>";
		echo "<p class=\"charList\">".$a[2]."</p>\n";
		navigation($section);
		echo "</details>";
	}
	echo "</section>";
}
?>
<nav id="bottom">
<a href="licenses/COPYING.txt">Licences</a>
<a href="https://github.com/parsimonhi/animCJK">Download</a>
<a href="samples/">Samples</a>
</nav>
<script src="samples/_js/setNumbersAcjk.js"></script>
<script>
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
function buildDicoContent(r)
{
	let c,dec,hex,dico="";
	c=r.character; // assume r has the character property
	dec=c.codePointAt(0);
	hex=dec.toString(16).padStart(5,'0');
	dico+="<dt>"+c+" U+"+hex+" &amp;#"+dec+";</dt>";
	if(Object.hasOwn(r,"pinyin")) dico+="<dt>Pinyin</dt><dd class=\"pinyin\">"+(r.pinyin+"").replace(/\([^\)]+\)/g,"").replace(/[ ]+/g,", ")+"</dd>";
	if(Object.hasOwn(r,"on")) dico+="<dt>On</dt><dd class=\"on\">"+(r.on+"").replace(/,([^ ])/g,", $1")+"</dd>";
	if(Object.hasOwn(r,"kun")) dico+="<dt>Kun</dt><dd class=\"kun\">"+(r.kun+"").replace(/,([^ ])/g,", $1")+"</dd>";
	if(Object.hasOwn(r,"definition")) dico+="<dt>Definition</dt><dd class=\"definition\">"+r.definition+"</dd>";
	if(Object.hasOwn(r,"radical")) dico+="<dt>Radical</dt><dd class=\"radical\">"+r.radical+"</dd>";
	if(Object.hasOwn(r,"decomposition")) dico+="<dt>Decomposition</dt><dd class=\"decomposition\">"+r.decomposition+"</dd>";
	if(Object.hasOwn(r,"acjk")) dico+="<dt>Acjk</dt><dd class=\"acjkDecomposition\">"+r.acjk+"</dd>";
	if(Object.hasOwn(r,"acjks")) dico+="<dt>Acjks</dt><dd class=\"acjksDecomposition\">"+r.acjks+"</dd>";
	return dico;
}
function createItems(j)
{
	let ul=document.getElementById("output"),d0=0;
	for(let k=0;k<j.length;k++)
	{
		let svg=j[k].svg,dico,dicoLine;
		dico="<dl>"+buildDicoContent(j[k])+"</dl>";
		svg=svg.replace(/<svg/,"<svg style=\"--d0:"+d0+"s;\"");
		svg=svg.replace(/(z[0-9]+)/g,"$1"+"-"+(k+1));
		d0+=(svg.match(/id="z[0-9-]+d[0-9]+a?"/g)||[]).length;
		li=document.createElement("li");
		li.innerHTML=svg+dico;
		ul.append(li);
	}
	setNumbers(document.querySelector('[name="numbers"]').checked);
	ul.scrollIntoView({block:"nearest"});
}
function ok()
{
	let a,b,c,dec,dir,e,s,lang,options={};
	e=document.getElementById("data");
	s=document.querySelector('[name="section"]:checked');
	lang=s?s.getAttribute("data-lang"):"ja";
	document.getElementById("output").innerHTML="";
	cleanData(e);
	c=e.value;
	if(c)
	{
		dec=c.codePointAt(0);
		options.cache=<?=$loc?>?"reload":"default";
		options.method="POST";
		options.body=JSON.stringify({lang:lang,data:[dec]});
		fetch('samples/_php/fetchData.php',options)
		.then(r=>{if(!r.ok) throw r.statusText; return r.json();})
		.then(j=>{createItems(j);return true;})
		.catch(e=>{console.log(e);b.innerHTML=e;});
	}
}
function doIt(c)
{
	document.getElementById("data").value=c;
	ok();
}

function initSection()
{
	let section=localStorage.getItem("section")?localStorage.getItem("section"):"Ja",
		e=document.querySelector('[name="section"][value="'+section+'"]');
	if(e)
	{
		e.checked=true;
		document.documentElement.lang=e.getAttribute("data-lang");
	}
}
function initDico()
{
	let dico=(localStorage.getItem("dico")=="1")?true:false;
	document.querySelector('[name="dico"]').checked=dico;
}
function initGrid()
{
	let grid=(localStorage.getItem("grid")=="1")?true:false;
	document.querySelector('[name="grid"]').checked=grid;
}
function initNumbers()
{
	let numbers=(localStorage.getItem("numbers")=="1")?true:false;
	document.querySelector('[name="numbers"]').checked=numbers;
}
function initXrays()
{
	let xrays=(localStorage.getItem("xrays")=="0")?false:true;
	document.querySelector('[name="xrays"]').checked=xrays;
}
function initSize()
{
	let size;
	size=localStorage.getItem("size")?localStorage.getItem("size"):"256";
	document.querySelector('[name="size"]').value=size;
	document.querySelector(':root').style.setProperty('--size',size+"px");
}
function initAll()
{
	initSection();
	initDico();
	initGrid();
	initNumbers();
	initXrays();
	initSize();
}
function changeSection()
{
	let e=document.querySelector('[name="section"]:checked'),section;
	if(e)
	{
		section=e.value;
		localStorage.setItem("section",section);
		document.documentElement.lang=e.getAttribute("data-lang");
		ok();
	}
}
function changeDico()
{
	let dico=document.querySelector('[name="dico"]').checked;
	localStorage.setItem("dico",dico?"1":"0");
}
function changeGrid()
{
	let grid=document.querySelector('[name="grid"]').checked;
	localStorage.setItem("grid",grid?"1":"0");
}
function changeNumbers()
{
	let numbers=document.querySelector('[name="numbers"]').checked;
	localStorage.setItem("numbers",numbers?"1":"0");
	setNumbers(numbers);
}
function changeXrays()
{
	let xrays=document.querySelector('[name="xrays"]').checked;
	localStorage.setItem("xrays",xrays?"1":"0");
}
function changeSize()
{
	let size=document.querySelector('[name="size"]').value;
	localStorage.setItem("size", size);
	document.querySelector(':root').style.setProperty('--size',size+"px");
}
function changeSection()
{
	let e=document.querySelector('[name="section"]:checked'),
		section,lang;
	if(e)
	{
		section=e.value;
		lang=e.getAttribute("data-lang");
		localStorage.setItem("section",section);
		document.documentElement.lang=lang;
		ok();
	}
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
	document.querySelector('[name="section"][value="Ja"]').addEventListener("change",changeSection);
	document.querySelector('[name="section"][value="Ko"]').addEventListener("change",changeSection);
	document.querySelector('[name="section"][value="ZhHans"]').addEventListener("change",changeSection);
	document.querySelector('[name="section"][value="ZhHant"]').addEventListener("change",changeSection);
	document.querySelector('[name="dico"]').addEventListener("change",changeDico);
	document.querySelector('[name="grid"]').addEventListener("change",changeGrid);
	document.querySelector('[name="numbers"]').addEventListener("change",changeNumbers);
	document.querySelector('[name="xrays"]').addEventListener("change",changeXrays);
	document.querySelector('[name="size"]').addEventListener("change",changeSize);
	initAll();
});
</script>
</body>
</html>