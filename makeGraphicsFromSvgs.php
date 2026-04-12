<!doctype html>
<html>
<!--
Purpose: make graphicsXxx.txt from svgsXxx folder content
Usage 1: run makeGraphicsFromSvgs.php in a browser
Usage 2: run makeGraphicsFromSvgs.php?d=Xxx in a browser
Usage 3: run makeGraphicsFromSvgs.php?d=Xxx&r=ppp in a browser
(where ppp is a relative path between the directory that contains this script
and the directory that contains svgsXxx)
Xxx may be omitted or an empty string, and can contain only letters, number and minus sign
Requirements: svgsXxx directory must exist, graphicsXxx.txt file must not exist
Clean svg paths (remove decimal, replace "," by " ", ...)
Does nothing if svgsXxx doesn't exist
Does nothing if graphicsXxx.txt already exists
Note1: no dependencies
Note2: the server must be "localhost"
-->
<?php
// run only on localhost
if ($_SERVER['SERVER_NAME']!="localhost")
{
	echo "<body>Can run only on localhost</body></html>\n";
	exit(0);
}
mb_internal_encoding("UTF-8");
mb_regex_encoding("UTF-8");
?>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<style>
body {font-family:sans-serif;}
footer a {text-align:center;color:#000;}
</style>
</head>
<body>
<?php
$version=(isset($_GET["d"])?$_GET["d"]:"");
if (isset($_GET["r"])) $r=$_GET["r"]."/";
else $r="";
$version=ucfirst($version); // mandatory
$dir=$r."svgs".$version;
$target=$r."graphics".$version.".txt";
?>
<h1>Make graphics<?=$version;?>.txt from svgs<?=$version;?></h1>
<?php
function unichr($u)
{
	// return a char from its decimal unicode
    return mb_convert_encoding('&#'.intval($u).';','UTF-8','HTML-ENTITIES');
}

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
	natsort($a);
	$k=0;
	// assume $dir contains only the svgs one wants
	foreach ($a as $f)
	{
		if(preg_match("/^[0-9]+z?\.svg$/",$f))
		{
			
			$dec=intVal($f);
			$char=unichr($dec);
			$handle=fopen($dir."/".$f,"r");
			$s='{"character":"'.$char.'","strokes":[';
			if ($handle)
			{
				$k++;
				echo $k.": ".unichr($dec)." ".$f."\n";
				$n=0;
				while (($line=fgets($handle))!==false)
				{
					$r='#<path[^>]+id="z[0-9]+d[0-9]+[a-z]?"[^>]+d="([^"]+)"[^>]+>#';
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
					$r='#<path[^>]+pathLength[^>]+d="([^"]+)"[^>]+>#';
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
				file_put_contents($target,$s.PHP_EOL,FILE_APPEND|LOCK_EX);
				echo " n=".$n."<br>\n";
				fclose($handle);
			}
			else echo "Cannot open \"".$f."\"<br>\n";
		}
	}
	echo "Target: ".$target."<br>\n";
}

echo "<p>Begin<br>\n";
if (!file_exists($dir)) echo "Error: ".$dir." directory not found<br>\n";
else if (file_exists($target))
{
	echo "<br>Error: ".$target." file already exists<br><br>\n";
	echo "Verify if d parameter is correct";
	echo " (you specified ".($version?"\"".$version."\"":"an empty string")." as d value)<br>\n";
	echo "If d value is correct, rename or delete the existing ".$target." file before retrying<br><br>\n";
	echo "Usage 1: run makeGraphicsFromSvgs.php in a browser<br>\n";
	echo "Usage 2: run makeGraphicsFromSvgs.php?d=Xxx in a browser<br>\n";
	echo "Usage 3: run makeGraphicsFromSvgs.php?d=Xxx&r=ppp in a browser<br><br>\n";
	echo "ppp is a relative path between the directory that contains this script and the directory that contains svgsXxx<br>\n";
	echo "Xxx may be omitted or an empty string, and can contain only letters, number and minus sign<br>\n";
	echo "svgsXxx directory must exist, graphicsXxx.txt file must not exist<br><br>\n";
	echo "Can run only on localhost<br><br>\n";
}
else makeGraphics($dir,$target,$version);
echo "End</p>\n";
?>
<footer>
<a href="./">Home</a>
- <a href="licenses/COPYING.txt">Licences</a><br>
Copyright 2016-2026 - FM&SH
</footer>
</body>
</html>