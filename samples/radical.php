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
	margin:0 auto 0.5em auto;
	max-width:256px;
	max-height:256px;
	border:1px solid #ccc;
}
.cartoucheDiv
{
	margin:0 auto 1em auto;
	max-width:256px;
	text-align:left;
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
<title>AnimCJK - Radical</title>
</head>
<body>
<?php displayHeader("AnimCJK - Radical");?>
<p>Display the radical of the character in a different color</p>
<button class="actionBtn" type="button" onclick="startAnim()">Animate</button>
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
}
else
{
	$radical="";
	$decomposition="";
	$acjk="";
}
?>
<div class="charDiv" data-acjk="<?php echo $acjk;?>">
<?php
// get svg file
// some characters are special because some strokes are split to show the radical
$special="../".$dir."Special/".$dec.".svg";
if (file_exists($special)) include $special;
else include "../".$dir."/".$dec.".svg";
?>
</div>
<div class="cartoucheDiv">
<?php
echo "Radical: ".$radical;
echo "<br>";
echo "Decomposition: ".$decomposition;
echo "<br>";
echo "Acjk: ".$acjk;
?>
</div>
<?php echo displayFooter("radical");?>
<script>
function isRadicalStroke(path,n,d)
{
	var c,d,e,list,nsi,nsj,pos;
	// manage special decomposition when some strokes are split to show the radical
	if (!d) return false;
	if (d.substr(0,3)=="凹⿱⿰") d="凹⿱⿰㇑.1⿲㇅1㇑1㇐1㇑.1一.1";
	else if (d=="凸⿱⿰⿳㇑1㇐1㇑.1㇎.1㇐.1") d="凸⿱⿰⿳㇑1㇐1㇑.1⿱㇅1㇑.1㇐.1";
	else if (d.substr(0,1)=="及") d="及⿻⿻丿1𠃌1又.2";
	else if (d=="由.⿻曰:2丨1曰:2") d="由⿻曰.:2丨1丨.1曰.:2";//Ja
	else if (d=="由.⿻曰:3丨1曰:1") d="由⿻曰.:3丨1丨.1曰.:1";//Zh
	else if (d=="甲.⿻曰4丨1") d="甲⿻曰.4丨.1丨1";
	else if (d=="申.⿻曰4丨1") d="申⿻曰.4丨1丨.1丨1";
	else if (d=="电.⿻曰4乚1") d="申⿻曰.4乚1乚.1乚1";
	else if (d=="画⿱一1⿶由.5凵2") d="画⿱一1⿶由⿻曰.:2丨1丨.1曰.:2凵2";//Ja only
	else if (d.substr(0,1)=="畅") d="畅⿰申⿻曰.4丨1丨.1丨1𠃓3";
	else if (d.substr(0,1)=="氓") d="氓⿰亡3民⿻巳1氏.:1巳:1氏.:3";
	else if (d=="既⿰艮5旡.4") d="既⿰艮5旡.:1旡:1旡.:3";
	else if (d=="釜⿱父⿱八2乂.2⿻王.:3丷.2王.:1") d="釜⿱父⿱八2乂1乂.1乂1乂.1⿻王.:3丷.2王.:1";
	else if (d=="粛⿻肀3⿻米.6⿰丿1丨1") d="粛⿻肀4⿻米.6⿰丿1丨1";
	else if (d=="重⿻千2里.7") d="重⿻千:2里.:4千:1里.:3";
	else if (d=="之⿳丶1㇇.1乀1") d="之⿳丶1㇇:1㇇.:1乀1";
	else if (d=="民⿻巳1氏.4") d="民⿻巳:1氏.:1巳:1氏.:3";
	else if (d.substr(0,1)=="乡") d="乡⿳"+String.fromCodePoint(131275)+".1"+String.fromCodePoint(131275)+".1丿.:1丿:1"; // special char does'nt work
	else if (d.substr(0,1)=="甩") d="甩⿻甩.:5甩:1";
	else if (d.substr(0,1)=="甫") d="甫⿻⿻⿱一1月.4丨:1丨.:1丶1";
	else if (d=="象⿱⺈2⿻口⿱⿰丨1"+String.fromCodePoint(131277)+"1一.1𧰨.6") d="象⿱⺈2⿻口⿱⿰丨1"+String.fromCodePoint(131277)+"1一.1𧰨:1𧰨.:6";
	else if (d=="豫⿰予4象⿱⺈2⿻口⿱⿰丨1"+String.fromCodePoint(131277)+"1一.1𧰨.6") d="豫⿰予4象⿱⺈2⿻口⿱⿰丨1"+String.fromCodePoint(131277)+"1一.1𧰨:1𧰨.6";
	if (d.charAt(1)==".") return true;
	nsi=0;
	d=d.replace(/:/g,"");
	d=d.replace(/[^0-9.]+/g,"?");
	while ((pos=d.search(/[0-9]+/))>=1)
	{
		nsj=nsi+parseInt(d.substr(pos));
		c=d.charAt(pos-1);
		if ((c==".")&&(n>=nsi)&&(n<nsj)) return true;
		nsi=nsj;
		d=d.replace(/^[^0-9]*[0-9]+/,"");
	}
	return false;
}
function colorizeRadical()
{
	// work as is even if several characters are displayed in the page
	var list,k,km,k2,k3,ko,go,g,p,d,cp;
	list=document.querySelectorAll("svg.acjk path[clip-path]");
	km=list.length;
	k2=0;
	go=0;
	ko=0;
	for (k=0;k<km;k++)
	{
		// assume acjk was stored in data-acjk attribute of svg parent node
		p=list[k].parentNode;
		g=p;
		if (g!=go) {go=g;ko=k;}
		while(p&&!p.hasAttribute("data-acjk")) p=p.parentNode;
		if (p) d=p.getAttribute("data-acjk");else d="";
		if (isRadicalStroke(list[k],k-ko,d)) list[k].style.stroke="orange";
		else list[k].style.stroke="blue";
		cp=list[k].getAttribute("clip-path");
		if (!cp.match(/[b-z]\)/)) k2++;
		k3=k2*1.25;
		list[k].style.animation="zk var(--t) linear forwards calc("+k3+" * var(--t)";
	}
}
function startAnim()
{
	// (re)start animation when clicking on "Animate" button
	// work as is even if several characters are displayed in the page
	var list,k,km,s;
	list=document.querySelectorAll("svg.acjk");
	km=list.length;
	for (k=0;k<km;k++)
	{
		s=list[k].innerHTML;
		list[k].innerHTML="";
		list[k].innerHTML=s;
	}
	colorizeRadical();
}
window.addEventListener("load",startAnim,false);
</script>
</body>
</html>
