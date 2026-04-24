<!doctype html>
<html>
<?php
// can run only on localhost
if ($_SERVER['SERVER_NAME']!="localhost"){echo "Can run only on localhost";exit(0);}
mb_internal_encoding("UTF-8");
mb_regex_encoding("UTF-8");
?>
<!--
Purpose: Make svgs from graphicsXxx.txt and put them in svgsXxx folder
Usage 1: run makeSvgsFromGraphics.php in a browser 
Usage 2: run makeSvgsFromGraphics.php?d=Xxx in a browser 
Usage 3: run makeSvgsFromGraphics.php?d=Xxx&r=ppp in a browser
Xxx may be omitted or an empty string
Xxx can contain only latin letters and numbers
Xxx should start with a capital letter
ppp is a relative path between this script and the directory that contains graphicsXxx.txt
ppp can contain only latin letters, numbers, and ./_- characters
Does nothing if graphicsXxx.txt doesn't exist
Does nothing if svgsXxx already exists and contains data that are also in graphicsXxx.txt
Create svgsXxx if svgsXxx directory doesn't exist
Note: the server must be "localhost"
-->
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<style>
body {font-family:sans-serif;}
footer a {text-align:center;color:#000;}
.error {color:red;}
</style>
</head>
<body>
<?php
$version=(isset($_GET["d"])?$_GET["d"]:"");
$version=preg_replace("/[^A-Za-z0-9]/","",$version);
$version=ucfirst($version); // mandatory
$r=(isset($_GET["r"])?$_GET["r"]."/":"");
$r=preg_replace("/[^A-Za-z0-9.\/_-]/","",$r);
$file=$r."graphics".$version.".txt";
// in case one splits graphicsXxx.txt in several sub-files graphicsXxxn.txt
// remove the final number to build the target directory name
if(isset($_GET["v"])&&$_GET["v"]=="1") $version2=preg_replace("/[0-9]+$/","",$version);
else $version2=$version;
$dir=$r."svgs".$version2;
if(isset($_GET["z"])&&$_GET["z"]=="1") $addZ=1;
else $addZ=0;
?>
<h1>Make svgs file from <?php echo $file;?></h1>
<nav><a href="./">Menu</a></nav>
<p>
<?php
function showError($s)
{
	echo "<span class='error'>".$s."</span><br>\n";
}
function showMsg($s)
{
	echo "<span class='msg'>".$s."</span><br>\n";
}
function unichr($u)
{
	// return a char from its decimal unicode
    return mb_convert_encoding('&#' . intval($u) . ';', 'UTF-8', 'HTML-ENTITIES');
}

function decUnicode($c)
{
	// return the decimal unicode of a char
	$len=strlen($c);
	if ($len==0) return 63;
	$r1=ord($c[0]);
	if ($len==1) return $r1;
	$r2=ord($c[1]);
	if ($len==2) return (($r1&31)<< 6)+($r2&63);
	$r3=ord($c[2]);
	if ($len==3) return (($r1&15)<<12)+(($r2&63)<< 6)+($r3&63);
	$r4=ord($c[3]);
	if ($len==4) return (($r1& 7)<<18)+(($r2&63)<<12)+(($r3&63)<<6)+($r4&63);
	return 63;
}
function transformPathFromGraphics($p)
{
	if (preg_match_all("#([MQCLZ ]+)([0-9.-]+) ([0-9.-]+)#",$p,$m))
	{
		$npm=count($m[0]);
		$q="";
		for ($np=0;$np<$npm;$np++)
		{
			$x=intval($m[2][$np]);
			$y=-(intval($m[3][$np])-900);
			$q.=$m[1][$np].$x." ".$y;
		}
		if (preg_match("/Z/",$p)) $q.="Z";
		return $q;
	}
	return $p;
}
function buildStyle()
{
	$s="<style>\n<![CDATA[\n";
	$s.="@keyframes zk {\n";
	$s.="\tto {\n";
	$s.="\t\tstroke-dashoffset:0;\n";
	$s.="\t}\n";
	$s.="}\n";
	$s.="svg.acjk path[clip-path] {\n";
	$s.="\t--t:0.8s;\n";
	$s.="\tanimation:zk var(--t) linear forwards var(--d);\n";
	$s.="\tstroke-dasharray:3337;\n"; // more than pathLength + 1
	$s.="\tstroke-dashoffset:3339;\n"; // less than 2 * strokeDasharray - pathLength
	$s.="\tstroke-width:128;\n"; // acjk.strokeWidthMax + 8 or 16?
	$s.="\tstroke-linecap:round;\n";
	$s.="\tfill:none;\n";
	$s.="\tstroke:#000;\n";
	$s.="}\n";
	$s.="svg.acjk path[id] {fill:#ccc;}\n";
	$s.="]]>\n</style>\n";
	return $s;
}
function hasMedianInCommon($m1,$m2)
{
	$km1=count($m1);
	$km2=count($m2);
	for($k1=1;$k1<$km1;$k1++)
	{
		for($k2=1;$k2<$km2;$k2++)
		{
			if(($m1[$k1][0]==$m2[$k2][0])&&($m1[$k1][1]==$m2[$k2][1])
				&&($m1[$k1-1][0]==$m2[$k2-1][0])&&($m1[$k1-1][1]==$m2[$k2-1][1]))
				return 1;
		}
	}
	return 0;
}
function computeSuffixesAndDelays($a)
{
	// if some medians of a stroke have a segment in common
	// that means this stroke is divided in several parts for a better rendering
	//   when it overlaps on itself (see あ)
	//   or when the radical is special (see 申)
	// in such a case
	// add alphabetic suffixes to the id of the different stroke parts
	// and set the same delay for all the stroke parts
	$m=$a->{'medians'};
	$imax=count($m);
	$r=array();
	$j=0;
	$k=0;
	for($i=0;$i<$imax;$i++)
	{
		$r[$i]=array();
		if($i)
		{
			if(hasMedianInCommon($m[$i],$m[$i-1]))
			{
				// do not incremente $j here
				if(!$k)
				{
					$k++;
					// add 'a' to the id of the previous path
					$r[$i-1][0].='a';
				}
				$k++;
			}
			else
			{
				$j++;
				$k=0;
			}
		}
		else $j=1;
		$r[$i][0]=$j; // id suffix
		$r[$i][1]=$j; // delay
		// add an alphabetic suffix to the id of the path if required
		if($k) $r[$i][0].=chr(96+$k);
	}
	return $r;
}
function buildSvg($a,$svgStyle)
{
	$u=decUnicode($a->{'character'});
	$id="z".$u;
	$x="xmlns=\"http://www.w3.org/2000/svg\"";
	$s="<svg id=\"".$id."\" class=\"acjk\" viewBox=\"0 0 1024 1024\" ".$x.">\n";
	// style
	$s.=$svgStyle;
	$sd=computeSuffixesAndDelays($a);
	// stroke shapes
	$k=0;
	foreach($a->{'strokes'} as $p)
	{
		$ls=$sd[$k][0];
		$k++;
		$p=str_replace(","," ",$p);
		$p=preg_replace("#\s?([MQCLZ])\s?#","$1",$p);
		$p=preg_replace("#([^ ])-#","$1 -",$p);
		// transform coordinates of path nodes (x2 = x1, y2 = 900-y1)
		// don't do this transformation if $_GET["t"] exists and is not 1
		if (!isset($_GET["t"])||($_GET["t"]==1)) $p=transformPathFromGraphics($p);
		$s.="<path id=\"".$id."d".$ls."\" d=\"".$p."\"/>\n";
	}
	
	// clip paths
	$s.="<defs>\n";
	$k=0;
	foreach($a->{'strokes'} as $p)
	{
		$ls=$sd[$k][0];
		$k++;
		$s.="\t<clipPath id=\"".$id."c".$ls."\">";
		$s.="<use href=\"#".$id."d".$ls."\"/>";
		$s.="</clipPath>\n";
	}
	$s.="</defs>\n";
	
	// medians
	$k=0;
	foreach($a->{'medians'} as $m)
	{
		$ls=$sd[$k][0];
		$ld=$sd[$k][1];
		$k++;
		$z="";
		foreach($m as $point) $z.=($z?"L":"M").$point[0]." ".$point[1];
		if (!isset($_GET["t"])||($_GET["t"]==1)) $z=transformPathFromGraphics($z);
		$s.="<path style=\"--d:".$ld."s;\" pathLength=\"3333\" clip-path=\"url(#".$id."c".$ls.")\" d=\"".$z."\"/>\n";
	}
	
	$s.="</svg>";
	return $s;
}
function haveCommonData($file,$dir,$graphics)
{
	// return false;// uncomment this line to force update
	
	// old method
	// check if $file is older than $dir
	// $fileDate=filemtime($file);
	// $dirDate=filemtime($dir."/.");
	// return ($fileDate<$dirDate);

	// new method
	// check if some characters are already in both $dir and $json
	$graphicsCharList="";
	foreach($graphics as $g) $graphicsCharList.=$g->{'character'};
	$a=scandir($dir);
	foreach($a as $f)
	{
		if(preg_match("/^[0-9]+z?\.svg$/",$f))
		{
			$dec=intVal($f);
			$c=unichr($dec);
			if(mb_strpos($graphicsCharList,$c,0,"UTF-8")!==false) return 1;
		}
	}
	return 0;
}
function arphicDerivated($c)
{
	return (preg_match("/\p{Han}/u",$c)?1:0);
}
function getCommentWhenArphicDerivated()
{
	$cp1="<!--\n";
	$cp1.="AnimCJK 2016-".date("Y")." Copyright FM-SH, https://github.com/parsimonhi/animCJK\n";
	$cp1.="Derived from:\n";
	$cp1.="    MakeMeAHanzi project - https://github.com/skishore/makemeahanzi\n";
	$cp1.="    Arphic PL KaitiM GB font\n";
	$cp1.="    Arphic PL KaitiM Big5 font\n";
	$cp1.="You can redistribute and/or modify this file under the terms of the Arphic Public License\n";
	$cp1.="as published by Arphic Technology Co., Ltd.\n";
	$cp1.="You should have received a copy of this license along with this file.\n";
	$cp1.="If not, see https://ftp.gnu.org/non-gnu/chinese-fonts-truetype/LICENSE.\n";
	$cp1.="-->\n";
	return $cp1;
}
function getCommentWhenNotArphicDerivated()
{
	$cp2="<!--\n";
	$cp2.="AnimCJK 2016-".date("Y")." Copyright FM-SH, https://github.com/parsimonhi/animCJK\n";
	$cp2.="You can redistribute and/or modify these files under the terms of the GNU\n";
	$cp2.="Lesser General Public License as published by the Free Software Foundation,\n";
	$cp2.="either version 3 of the license, or \(at your option\) any later version. You\n";
	$cp2.="should have received a copy of this license \(the file \"LGPL.txt\"\) along with\n";
	$cp2.="these files; if not, see https://www.gnu.org/licenses/.\n";
	$cp2.="-->\n";
	return $cp2;
}
function errorMsgWhenCommonData($file,$dir)
{
	$msg="Some characters have an entry in ".$file." and also have a svg in ".$dir.".<br>\n";
	$msg.="Nothing was done to prevent data loss.<br>\n";
	$msg.="There are several possibilities to solve this issue which depends on your context.<br>\n";
	$msg.="For instance, you can rename or remove ".$dir.", then run this script again, and then merge (if desired) the new svgs folder with the old one.<br>\n";
	$msg.="Or you can rename ".$file.", then run this script again, and then merge (if desired) the new svgs folder with the old one.";
	return $msg;
}
function parseGraphicsFile($file)
{
	// very big files may be not json_decoded (memory overflow) (case of graphicsXxx.txt)
	// so json_decode $file line by line
	$handle=fopen($file,"r");
	if ($handle)
	{
		$r=array();
		while (($line=fgets($handle))!==false)
		{
			$line=trim($line);
			$r[]=json_decode($line);
		}
		fclose($handle);
		return $r;
	}
	return null;
}
function createSvgDir($dir)
{
	if (mkdir($dir,0755))
	{
		showMsg("Created ".$dir);
		return 1;
	}
	showError("Failed to create ".$dir);
	return 0;
}
if (!file_exists($file)) showError($file." file not found");
else
{
	$graphics=parseGraphicsFile($file);
	// $graphics is an array
	if(!$graphics) showError("Fail to parse ".$file);
	else
	{
		$newDirDone=0;
		if (!file_exists($dir)) $newDirDone=createSvgDir($dir);
		if (!file_exists($dir)) showError($dir." directory not found");
		else if(!is_dir($dir)) showError($dir." exists but is not a directory");
		else if(!$newDirDone&&haveCommonData($file,$dir,$graphics))
			showError(errorMsgWhenCommonData($file,$dir));
		else
		{
			if(!isset($_GET["t"])||($_GET["t"]==1))
				showMsg("Svg coordinates will be transformed using scale(1,-1) and translate(0,-900)!");
			$cp1=getCommentWhenArphicDerivated();
			$cp2=getCommentWhenNotArphicDerivated();
			$svgStyle=buildStyle();
			foreach($graphics as $g)
			{
				if ($g->{'character'})
				{
					$cp=(arphicDerivated($g->{'character'})?$cp1:$cp2);
					$svgFile=$dir."/".decUnicode($g->{'character'}).($addZ?"z":"").".svg";
					$content=buildSvg($g,$svgStyle);
					file_put_contents($svgFile,$cp.$content);
					echo $g->{'character'};
				}
			}
			
		}
	}
}
?>
</p>
<footer>
<a href="./">Home</a>
- <a href="javascript:history.back()">Back</a>
- <a href="licenses/COPYING.txt">Licences</a><br>
Copyright 2016-<?=date("Y")?> - FM&SH
</footer>
</body>
</html>