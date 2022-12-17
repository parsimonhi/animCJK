<?php
include_once "samples/_php/encoding.php";
include_once "samples/_php/unicode.php";
include_once "samples/_php/convertKana.php";

function my_json_decode($line)
{
	// decode a line from graphicsXxx.txt or dictionaryXxx.txt
	$a=new StdClass();
	if (preg_match("/^\\{\"character\":\"([^\"]+)\",\"strokes\":\\[\"([^\\]]+)\"\\],\"medians\":\\[(.+)\\]\\}$/",$line,$match))
	{
		$a->{'character'}=$match[1];
		$a->{'strokes'}=explode("\",\"",$match[2]);
		$x=explode("]],[[",$match[3]);
		$kmx=count($x);
		$x[0]=str_replace("[[","",$x[0]);
		$x[$kmx-1]=str_replace("]]","",$x[$kmx-1]);
		$y=array();
		for($kx=0;$kx<$kmx;$kx++)
		{
			$y=explode("],[",$x[$kx]);
			$kmy=count($y);
			for($ky=0;$ky<$kmy;$ky++)
			{
				$y[$ky]=explode(",",$y[$ky]);
			}
			$x[$kx]=$y;
		}
		$a->{'medians'}=$x;
	}
	else if (preg_match("/\"character\":\"([^\"]+)\"/",$line,$match))
	{
		$a->{'character'}=$match[1];
		if (preg_match("/\"set\":\\[\"([^\\]]+)\"\\]/",$line,$match))
			$a->{'set'}=explode("\",\"",$match[1]);
		if (preg_match("/\"definition\":\"([^\"]+)\"/",$line,$match))
			$a->{'definition'}=$match[1];
		if (preg_match("/\"pinyin\":\\[\"([^\\]]+)\"\\]/",$line,$match))
			$a->{'pinyin'}=explode("\",\"",$match[1]);
		if (preg_match("/\"on\":\\[\"([^\\]]+)\"\\]/",$line,$match))
			$a->{'on'}=explode("\",\"",$match[1]);
		if (preg_match("/\"kun\":\\[\"([^\\]]+)\"\\]/",$line,$match))
			$a->{'kun'}=explode("\",\"",$match[1]);
		if (preg_match("/\"radical\":\"([^\"]+)\"/",$line,$match))
			$a->{'radical'}=$match[1];
		if (preg_match("/\"decomposition\":\"([^\"]+)\"/",$line,$match))
			$a->{'decomposition'}=$match[1];
		if (preg_match("/\"acjk\":\"([^\"]+)\"/",$line,$match))
			$a->{'acjk'}=$match[1];
	}
	return $a;
}

function convertSet($s,$lang)
{
	if ($lang=="ja")
	{
		if (preg_match("/^g([1-6])$/",$s)) $r=preg_replace("/^g([1-6])$/","Joyo kanji, grade $1",$s);
		else if ($s=="g7") $r="Jōyō kanji, junior high school";
		else if ($s=="g8") $r="Jinmeyō kanji";
		else $r="Hyōgai kanji";
	}
	else if ($lang=="zh-Hans")
	{
		if (preg_match("/^hsk([1-6])$/",$s)) $r=preg_replace("/^hsk([1-6])$/","HSK $1",$s);
		else if ($s=="hsk7") $r="Frequent hanzi";
		else if ($s=="hsk8") $r="Common hanzi";
		else $r="Uncommon hanzi";
	}
	else if ($lang=="zh-Hant")
	{
		if (preg_match("/^traditional([1-6])$/",$s)) $r=preg_replace("/^traditional([1-6])$/","HSK $1 traditional",$s);
		else if ($s=="traditional7") $r="Frequent traditional hanzi";
		else if ($s=="traditional8") $r="Common traditional hanzi";
		else $r="Uncommon hanzi";
	}
	else $r="";
	return $r;
}

function getDictionaryData($char,$lang="zh-hans")
{
	$s="<div class=\"dico\">";
	$s.="<div class=\"unicode\"><span class=\"cjkChar\" lang=\"".$lang."\">".$char."</span> ";
	$s.="U+".hexUnicode($char)." "."&amp;#".decUnicode($char).";"."</div>\n";
	if (strtolower($lang)=="ja") $handle=fopen("dictionaryJa.txt","r");
	else if (strtolower($lang)=="zh-hant") $handle=fopen("dictionaryZhHant.txt","r");
	else $handle=fopen("dictionaryZhHans.txt","r");
	if ($handle)
	{
		$k=0;
		while (($line=fgets($handle))!==false)
		{
			$k++;
			if (mb_strpos($line,'{"character":"'.$char,0,'UTF-8')!==false)
			{
				$a=my_json_decode($line);
				if (count($a->{'set'}))
				{
					$s.="<div class=\"set\">";
					$ini=true;
					foreach ($a->{'set'} as $b) {if (!$ini) $s.=", ";$s.=convertSet($b,$lang);$ini=false;}
					$s.="</div>";
				}
				if (property_exists($a,'radical')&&$a->{'radical'})
					$s.="<div class=\"radical\">Radical: <span class=\"cjkChar\" lang=\"".$lang."\">".$a->{'radical'}."</span></div>";
				if (property_exists($a,'decomposition')&&$a->{'decomposition'})
					$s.="<div class=\"radical\">Decomposition: <span class=\"cjkChar\" lang=\"".$lang."\">".$a->{'decomposition'}."</span></div>";
				if (property_exists($a,'acjk')&&$a->{'acjk'})
					$s.="<div class=\"radical\">Acjk: <span class=\"cjkChar\" lang=\"".$lang."\">".$a->{'acjk'}."</span></div>";
				if (($lang=="zh-hans")||($lang=="zh-hant"))
				{
					if (property_exists($a,'pinyin')&&count($a->{'pinyin'}))
					{
						$s.="<div class=\"pinyin\">Pinyin: ";
						$ini=true;
						foreach ($a->{'pinyin'} as $b)
						{
							if (!$ini) $s.=", ";
							$b=str_replace(" ",", ",$b);
							$b=preg_replace("/\\([0-9]+\\)/","",$b);
							$s.=$b;
							$ini=false;
						}
						$s.="</div>";
					}
				}
				else if ($lang=="ja")
				{
					if (property_exists($a,'on')&&count($a->{'on'}))
					{
						$s.="<div class=\"yomi\">Onyomi: ";
						$ini=true;
						foreach ($a->{'on'} as $b)
						{
							if (!$ini) $s.=", ";
							$s.=convertJapaneseOn($b);
							$ini=false;
						}
						$s.="</div>";
					}
					if (property_exists($a,'kun')&&count($a->{'kun'}))
					{
						$s.="<div class=\"yomi\">Kunyomi: ";
						$ini=true;
						foreach ($a->{'kun'} as $b)
						{
							if (!$ini) $s.=", ";
							$s.=convertJapaneseKun($b);
							$ini=false;
						}
						$s.="</div>";
					}
				}
				if (property_exists($a,'definition'))
					$s.="<div class=\"english\">Definition: ".$a->{'definition'}."</div>";
				break;
			}
		}
		fclose($handle);
	}
	else $s.="Error";
	$s.="</div>";
	return $s;
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

function buildSvg($a)
{
	$u=decUnicode($a->{'character'});
	$id="z".$u;
	$x="xmlns=\"http://www.w3.org/2000/svg\" xmlns:xlink=\"http://www.w3.org/1999/xlink\"";
	$s="<svg id=\"".$id."\" class=\"acjk\" version=\"1.1\" viewBox=\"0 0 1024 1024\" ".$x.">\n";
	
	// style
	$s.="<style>\n<![CDATA[\n";
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

	// stroke shapes
	$k=0;
	foreach($a->{'strokes'} as $p)
	{
		$k++;
		$p=str_replace(","," ",$p);
		$p=preg_replace("#\s?([MQCLZ])\s?#","$1",$p);
		$p=preg_replace("#([^ ])-#","$1 -",$p);
		// transform coordinates of path nodes (x2 = x1, y2 = 900-y1)
		// don't do this transformation if $_GET["t"] exists and is not 1
		if (!isset($_GET["t"])||($_GET["t"]==1)) $p=transformPathFromGraphics($p);
		$s.="<path id=\"".$id."d".$k."\" d=\"".$p."\"/>\n";
	}
	
	// clip paths
	$s.="<defs>\n";
	$k=0;
	foreach($a->{'strokes'} as $p)
	{
		$k++;
		$s.="\t<clipPath id=\"".$id."c".$k."\">";
		$s.="<use xlink:href=\"#".$id."d".$k."\"/>";
		$s.="</clipPath>\n";
	}
	$s.="</defs>\n";
	
	// medians
	$k=0;
	foreach($a->{'medians'} as $m)
	{
		$k++;
		$z="";
		foreach($m as $point) $z.=($z?"L":"M").$point[0]." ".$point[1];
		if (!isset($_GET["t"])||($_GET["t"]==1)) $z=transformPathFromGraphics($z);
		$s.="<path style=\"--d:".$k."s;\" pathLength=\"3333\" clip-path=\"url(#".$id."c".$k.")\" d=\"".$z."\"/>\n";
	}
	
	$s.="</svg>";
	return $s;
}
?>