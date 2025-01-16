<!doctype html>
<html>
<!--
Purpose: make graphicsKana.txt from svgsJaKana folder content
Usage: run makeGraphicsFromSvgs4Kana.php in a browser
Requirements: svgsJaKana directory must exist, graphicsKana.txt file must not exist
Clean svg paths (remove decimal, replace "," by " ", ...)
Does nothing if svgsJaKana doesn't exist
Does nothing if graphicsJaKana.txt already exists
Note: the server must be "localhost"
-->
<head>
<meta charset="UTF-8">
<meta name="viewport" content="initial-scale=1.0,user-scalable=yes">
<style>
footer a {text-align:center;color:#000;}
</style>
</head>
<body>
<?php
// you must run this script on "localhost"
$version="JaKana";
if (isset($_GET["p"])&&(md5($_GET["p"]."acjk")=="897950b81d960d551df6a6e9e9df9be5"))
	$pAcjk=1;
else
	$pAcjk=0;
$dir="svgs".$version;
$target="graphics".$version.".txt";
?>
<h1>Make graphics<?php echo $version;?>.txt file
from svgs<?php echo $version;?> directory content</h1>
<?php
include_once __DIR__."/samples/_php/unicode.php";

function transformPathFromSvgs($p)
{
	//assume "-" never follows a number
	if (preg_match_all("#([MQCLZ, ]+)([0-9.-]+)[ ,]+([0-9.-]+)#",$p,$m))
	{
		$npm=count($m[0]);
		$q="";
		for ($np=0;$np<$npm;$np++)
		{
			$x=intval($m[2][$np]);
			$y=-intval($m[3][$np])+900;
			$q.=$m[1][$np].$x.",".$y;
		}
		if (preg_match("/Z/",$p)) $q.="Z";
		//print "<br>p=".$p."<br>";
		//print "<br>q=".$q."<br>";
		return $q;
	}
	return $p;
}

function replaceVHbyL($p)
{
	// assume "-" never follows a number
	// assume no ,
	// assume no space near a letter
	// add V or H if omitted
	$q="/([VH])([0-9-]+)[\s]([0-9-]+)/";
	while (preg_match($q,$p)) $p=preg_replace($q,"$1$2$1$3",$p);
	while (preg_match("/[VH]/",$p))
	{
		// replace V by L
		$q="/([0-9-]+)\s([0-9-]+)V([0-9-]+)/";
		if (preg_match($q,$p)) $p=preg_replace($q,"$1 $2L$1 $3",$p);
		// replace H by L
		$q="/([0-9-]+)\s([0-9-]+)H([0-9-]+)/";
		if (preg_match($q,$p)) $p=preg_replace($q,"$1 $2L$3 $2",$p);
	}
	return $p;
}

function makeGraphics($dir,$target,$version)
{
	if (file_exists($target)) unlink($target);
	$a=scandir($dir);
	$k=0;
	foreach ($a as $f)
	{
		if (substr($f,0,1)==".") echo "Skip ".$f."<br>\n";
		else if ($f=="00000.svg") echo "Skip ".$f."<br>\n";
		else
		{
			
			$dec=intVal($f);
			$handle=fopen($dir."/".$f,"r");
			$s='{"character":"'.unichr($dec).'","strokes":[';
			if ($handle)
			{
				$k++;
				echo $k.": ".unichr($dec)." ".$f."<br>\n";
				$n=0;
				while (($line=fgets($handle))!==false)
				{
					$r='#<path[^>]+id="z[0-9]+d[0-9]+[a-z]?"[^>]+d="([^"]+)"[^>]*>#';
					if (preg_match($r,$line,$m))
					{
						if ($n) $s.=",";
						$m2=$m[1];
						// replace , by space
						$m2=preg_replace("/,/"," ",$m2);
						// add space before -
						$m2=preg_replace("/([0-9])-/","$1 -",$m2);
						// remove extra space
						$m2=preg_replace("/\s+/"," ",$m2);
						// remove decimal
						$m2=preg_replace("/\.[0-9]+/","",$m2);
						// replace z by Z
						$m2=preg_replace("/z/","Z",$m2);
						// remove space before and after M, Q, C, L, H, V et Z
						$m2=preg_replace("/\s?([MQCLVHZ])\s?/","$1",$m2);
						// add C if omitted
						$q="/(C([0-9-]+\s){5}[0-9-]+)\s/";
						while (preg_match($q,$m2)) $m2=preg_replace($q,"$1C",$m2);
						// add Q if omitted
						$q="/(Q([0-9-]+\s){3}[0-9-]+)\s/";
						while (preg_match($q,$m2)) $m2=preg_replace($q,"$1Q",$m2);
						// add L if omitted
						$q="/([ML][0-9-]+\s[0-9-]+)\s/";
						while (preg_match($q,$m2)) $m2=preg_replace($q,"$1L",$m2);
						$m2=replaceVHbyL($m2);
						if (!isset($_GET["t"])||($_GET["t"]==1)) $s.='"'.transformPathFromSvgs($m2).'"';
						//echo $m2."<br>\n";
						$n++;
					}
				}
				$s.='],"medians":[';
				rewind($handle);
				$n=0;
				while (($line=fgets($handle))!==false)
				{
					$r='#<path[^>]+pathLength[^>]+d="([^"]+)"[^>]*>#';
					if (preg_match($r,$line,$m))
					{
						if ($n) $s.=",";
						$m2=$m[1];
						// replace , by space
						$m2=preg_replace("/,/"," ",$m2);
						// add space before -
						$m2=preg_replace("/([0-9])-/","$1 -",$m2);
						// remove extra space
						$m2=preg_replace("/\s+/"," ",$m2);
						// remove decimal
						$m2=preg_replace("/\.[0-9]+/","",$m2);
						// remove space before and after M, L, H, and V
						$m2=preg_replace("/\s?([MLVH])\s?/","$1",$m2);
						// add L if omitted
						$q="/([ML][0-9-]+\s[0-9-]+)\s/";
						while (preg_match($q,$m2)) $m2=preg_replace($q,"$1L",$m2);
						// replace V and H by L
						$m2=replaceVHbyL($m2);
						//echo $m2."<br>\n";
						if (!isset($_GET["t"])||($_GET["t"]==1)) $m2=transformPathFromSvgs($m2);
						$m2=preg_replace("/\s+/",",",$m2);
						$m2=preg_replace("/L/",",",$m2);
						$m2=preg_replace("/M/","[[",$m2);
						$m2=preg_replace("/([0-9-]+,[0-9-]+),/","$1],[",$m2);
						$m2.="]]";
						$s.=$m2;
						//echo $m2."<br>\n";
						$n++;
					}
				}
				$s.=']}';
				//echo $s."<br>\n";
				file_put_contents("graphics".$version.".txt",$s.PHP_EOL,FILE_APPEND|LOCK_EX);
				echo "n=".$n."<br>\n";
				fclose($handle);
			}
			else echo "Cannot open \"".$f."\"<br>\n";
		}
	}
}

echo "<p>Begin<br>\n";
if ($_SERVER['SERVER_NAME']!="localhost") echo "Error: not a convenient server<br>\n";
else if (!file_exists($dir)) echo "Error: ".$dir." directory not found<br>\n";
else if (!$pAcjk&&file_exists($target))
{
	echo "Error: ".$target." file already exists<br>\n";
	echo "Verify if the version is correct";
	echo " (you specified ".($version?"\"".$version."\"":"an empty string")." as version)<br>\n";
	echo "If the version is correct, rename or delete the existing ".$target." file before retrying<br>\n";
}
else makeGraphics($dir,$target,$version);
echo "End</p>\n";
?>
<footer>
<a href="./">Home</a>
- <a href="licenses/COPYING.txt">Licences</a><br>
Copyright 2016-2022 - FM&SH
</footer>
</body>
</html>